<?php
namespace App\Http\Controllers\Order;
use App\Jobs\ChangeOrderStatus;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Model\Product;
use App\Utils\Message;
use App\Utils\ReturnData;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;


class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        try{
            $offset = $request->input('offset') ? $request->input('offset') : 0;
            $limit = $request->input('limit') ? $request->input('limit') : 10;
            $user_id=$request->input('user_id') ? $request->input('user_id') : 0;
            $list=Order::whereRaw("(CASE WHEN '$user_id'<> 0 THEN user_id=$user_id  ELSE 1=1 END)")
                ->where('is_delete',0)
                ->limit($limit)
                ->offset($offset)
                ->get();
            foreach ($list as $k=>$v){
                $v->order_detail=OrderDetail::selectRaw('*,product.id As product_id')->where('order_id',$v->id)->leftJoin('product','order_detail.product_id','=','product.id')->get();
            }
            $count=Order::whereRaw("(CASE WHEN '$user_id'<> 0 THEN user_id=$user_id  ELSE 1=1 END)")
                ->count();
            return ReturnData::returnListResponse($list,$count,200);

        }catch (\Exception $e){
            return ReturnData::returnDataError($e->getMessage(),402);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $this->validate($request,[
                'product_arr'=> 'required',
                'user_id'=>'required|integer',
                'user_address_id'=>'required|integer'
            ]);
            //获得该商品的库存信息
            $product_arr=json_decode($request->input('product_arr'),true);
            $arr = [];  //商品数组

            foreach ($product_arr as $k=>$v){
                $list=DB::select('select product_stock.*,IF(product_discount.purchasers <= ? , product_discount.discount , 0) as discount from product_stock LEFT JOIN product_discount ON discount  
                    WHERE product_stock.product_id = ? AND product_stock.color_id = ? AND  product_stock.size_id= ?  AND  product_stock.num >= ?',[$v['num'],$v['id'],$v['color_id'],$v['size_id'],$v['num']]);
                if (!$list){
                    throw new \Exception('您需要的商品库存不足');
                }
                $product_arr[$k]['stock']=$list[0];
                array_push($arr,["id"=>$v['id'],"num"=>$v['num'],"stock"=>$list[0]]);

            }


            //商品的种类数   订单实付款数   订单实付款数     订单的总折扣数
            $product_num_arr=array(); $product_num=null; $payable_total=null;  $fact_total=null;  $discount_total=null;
            foreach($arr as $k=>$v){
                $product_list=DB::select('select product.*,IF(product_discount.purchasers <= ? , product_discount.discount , 0) as discount from product LEFT JOIN product_discount ON discount  
                    WHERE product.id = ?  ',[$v['num'],$v['id']]);
                $arr[$k]['product']=$product_list[0];
                $arr[$k]['payable']=$product_list[0]->price * $v['num'] - $product_list[0]->discount;
                $arr[$k]['num']=$v['num'];
                $arr[$k]['fact']=$product_list[0]->price * $v['num'];
                $arr[$k]['discount']=$product_list[0]->discount;
                $arr[$k]['product_id']=$v['id'];
                $arr[$k]['stock_id']=$v['stock']->id;
                $payable_total+=$product_list[0]->price * $v['num'] - $product_list[0]->discount;
                $fact_total+=$product_list[0]->price * $v['num'];
                $discount_total+= $product_list[0]->discount;
                if (!in_array($v['id'], $product_num_arr)){
                   array_push($product_num_arr,$v['id']);
                }
            };
            $product_num=count($product_num_arr);

            //这里有一系列的事务处理
            DB::beginTransaction();
            //获得刚刚插入的记录id
            $order_id=DB::table('t_order')->insertGetId(
                [
                    'order_no'=>Util::randomNum(4,$request->input('user_id')),
                    'status'=>101,
                    'product_num'=>$product_num,
                    'payable_total'=>$payable_total,
                    'fact_total'=>$fact_total,
                    'discount_total'=>$discount_total,
                    'user_id'=>$request->input('user_id'),
                    'user_address_id'=>$request->input('user_address_id'),
                    'remark'=>$request->input('remark') ? $request->input('remark') : '',
                    'created_at'=>date('Y-m-d H:i:s',time()),
                    'address_detail'=>$request->input('address_detail'),
                ]
            );
            foreach($arr as $k=>$v){
                $a=array_except($v, ['id','product','stock']);
                $a['order_id']=$order_id;
                //插入订单详情
                DB::table('order_detail')->insert(
                    $a
                );
            }

            //更新商品库存  //添加库存记录
           foreach($product_arr as $k=>$v){
                 DB::table('product_stock')->where('id',$v['stock']->id)->update(['num'=>$v['stock']->num-$v['num']]);
           }
            DB::commit();
            dispatch((new ChangeOrderStatus($order_id,$status=105,$product_arr))->delay(Carbon::now()->addMinutes(1)));
            return ReturnData::returnDataResponse(1,200);

        }catch (\Exception $e){
            DB::rollBack();
            return ReturnData::returnDataError($e->getMessage(),402);
        }


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $order=Order::where('id',$id)->with('orderdetail')->first();
            return ReturnData::returnDataResponse($order,200);
        }catch (\Exception $e){
            return ReturnData::returnDataError('参数错误',402);

        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{
            $data=$request->only('status');
            $order_status=DB::table('t_order')->where('id',$id)->value('status');
            if ($data['status']==$order_status){
               return $this->response= [
                    'status' => 200,
                    'msg' => '设置成功'
                ];
            }
            $order_product_list=DB::table('order_detail')->where('order_id',$id)->get();
            DB::beginTransaction();
            switch ($data['status']){
                case 104:    //交易完成  添加商品的销售量
                    DB::table('t_order')->where('id',$id)->update(['status'=>$data['status']]);
                    foreach ($order_product_list as $k=>$v){
                        $product=Product::where('id',$v->product_id)->first();
                        DB::table('product')->where('id',$v->product_id)->update(['sale_num'=>$product->sale_num+$v->num]);
                    }
                    break;
                case 105:  //交易关闭  返回商品的库存
                    DB::table('t_order')->where('id',$id)->update(['status'=>$data['status']]);
                    $orderDetailList=OrderDetail::where('order_id',53)->with('log')->get();
                    foreach ($orderDetailList as $k=>$v){
                        //查询商品详情所对应的库存id
                        foreach ($v['log'] as $key=>$val){
                          //通过库存id返回相应库存
                          DB::table('product_stock')->where('id',$val->id)->increment('num',$val->num);
                        }
                    }
                    break;
                default:
                    DB::table('t_order')->where('id',$id)->update(['status'=>$data['status']]);
            }
           DB::commit();
           return $this->response=[
               'status' => 200,
               'msg' => '设置成功'
           ];

        }catch (\Exception $e){
            DB::rollBack();
            return ReturnData::returnDataError('修改失败',402);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //  这里做软删除
        try{
           DB::table('t_order')->where('id',$id)->update(['is_delete' => 1]);
          // Message::create(1,1,1,'订单被删除');
            return ReturnData::returnDataResponse(['message'=>'删除成功'],200);
        }catch (\Exception $e){
          return ReturnData::returnDataError($e->getMessage(),402);
        }
    }
//测试队列
    public function test(){
        // Redis::set('name', 'Taylor');
//       dispatch(
//           (new ChangeOrderStatus('512354087@qq.com'))->delay(Carbon::now()->addSeconds(15))
//        );
    }
}
