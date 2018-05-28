<?php

namespace App\Http\Controllers\Order;

use App\Model\Order;
use App\Model\Product;
use App\Utils\ReturnData;
use App\Utils\Util;
use Egulias\EmailValidator\Validation\Error\RFCWarnings;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
            $count=Order::whereRaw("(CASE WHEN '$user_id'<> 0 THEN user_id=$user_id  ELSE 1=1 END)")
                ->limit($limit)
                ->offset($offset)
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
                'product_num'=>'required|integer',
                'product_id'=> 'required|integer',
                'user_id'=>'required|integer',
                'user_address_id'=>'required|integer',
                'size_id'=>'required|integer',
                'color_id'=>'required|integer',
            ]);
            //获得该商品的库存信息
            $stock=Product::where('product.id',$request->input('product_id'))
                ->with('stock')
               
                ->first();
            return $stock;
            if (!$stock){
                throw new \Exception('您需要的商品库存不足');
            }

            $order=new Order();
            //return ReturnData::returnDataResponse(,200);
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
