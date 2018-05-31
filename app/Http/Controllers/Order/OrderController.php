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
            return $product_arr;
            $stock=[];
            foreach ($product_arr as $k=>$v){
                $stock=DB::select('select * from product_srock WHERE id = ? ',$v->id);
            }

           return $stock;
            if (!$stock){
                throw new \Exception('您需要的商品库存不足');
            }
            $product=Product::where('id',$request->input('product_id'))->with('discount')->first();
            $product->stock=$stock;
            $order=$request->all();
            //查看是否满足折扣条件
            if ($request->input('product_num')>=$product->discount->purchasers){
                $order['payable_total']=round(($product->price * $order['product_num'])-$product->discount->discount,2);
                $order['discount_total']=$product->discount->discount;
            }else{
                $order['payable_total']=round($product->price * $order['product_num'],2);
                $order['discount_total']=0;
            }
            $order['fact_total']=round($product->price * $order['product_num'],2);
            $newOrder= new Order();
            $newOrder->order_no=Util::randomNum(4,$request->input('user_id'));
            $newOrder->product_num=$order['product_num'];
            $newOrder->product_id=$order['product_id'];
            $newOrder->user_id=$order['user_id'];
            $newOrder->user_address_id=$order['user_address_id'];
            $newOrder->payable_total=$order['payable_total'];
            $newOrder->discount_total=$order['discount_total'];
            $newOrder->fact_total=$order['fact_total'];
            $newOrder->created_at=date('Y-m-d H:i:s');
            $newOrder->remark=$request->input('remark') ? $order['remark'] : '';
            $newOrder->save();

            return ReturnData::returnDataResponse($newOrder,200);
        }catch (\Exception $e){
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
