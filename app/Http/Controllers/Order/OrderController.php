<?php

namespace App\Http\Controllers\Order;

use App\Model\Order;
use App\Model\OrderDetail;
use App\Model\Product;
use App\Utils\ReturnData;
use App\Utils\Util;
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
                ->limit($limit)
                ->offset($offset)
                ->get();
            foreach ($list as $k=>$v){
                $v->order_list=OrderDetail::where('order_no',$v->order_no)->get();
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
                if (count($arr)){
                    foreach ($arr as $k=>$val) {
                       if ($val['id']==$v['id']){
                           $arr[$k]['num']=$val['num'] + $v['num'];
                       }else{
                           array_push($arr,["id"=>$v['id'],"num"=>$v['num']]);
                       }
                    }
                }else{
                    array_push($arr,["id"=>$v['id'],"num"=>$v['num']]);
                }
                $list=DB::select('select product_stock.*,IF(product_discount.purchasers <= ? , product_discount.discount , 0) as discount from product_stock LEFT JOIN product_discount ON discount  
                    WHERE product_stock.product_id = ? AND product_stock.color_id = ? AND  product_stock.size_id= ? ',[$v['num'],$v['id'],$v['color_id'],$v['size_id']]);
                if (!$list){
                    throw new \Exception('您需要的商品库存不足');
                 }
            }
            //商品的种类数   订单实付款数   订单实付款数     订单的总折扣数
            $product_num=null;  $payable_total=null;  $fact_total=null;  $discount_total=null; 
            foreach($arr as $k=>$v){
                $product_list=DB::select('select product.*,IF(product_discount.purchasers <= ? , product_discount.discount , 0) as discount from product LEFT JOIN product_discount ON discount  
                    WHERE product.id = ?  ',[$v['num'],$v['id']]);
                $arr[$k]['product_list']=$product_list[0];
                $arr[$k]['payable_total']=$product_list[0]['price']*$v['num']-$product_list[0]['discount'];
                $arr[$k]['fact']=$product_list[0]['price']*$v['num'];
                $arr[$k]['discount']=$product_list[0]['discount'];
            }
            DB::beginTransaction();
            DB::table('order')->insert([
                [
                    'order_no'=>Util::randomNum(4,$request->input('user_id')),
                    'product_num'=>count($arr),
                    'status'=>101,
                    'payable_total'=>$payable_total
                ]
            ]);
            DB::commit();
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
