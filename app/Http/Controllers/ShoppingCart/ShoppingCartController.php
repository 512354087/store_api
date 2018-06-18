<?php

namespace App\Http\Controllers\ShoppingCart;

use App\Utils\ReturnData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ShoppingCartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            $res=DB::table('shopping_cart')->select('product.*','product_stock.*','product.id as product_id','product_stock.id as stock_id','attributes.id as  color_id','size.id as  size_id','attributes.name as  color_name','size.name as size_name')
                ->leftJoin('product','shopping_cart.product_id','=','product.id')
                ->leftJoin('product_stock','shopping_cart.stock_id','=','product_stock.id')
                ->leftJoin('product_attributes as attributes','product_stock.color_id','=','attributes.id')
                ->leftJoin('product_attributes as size','product_stock.size_id','=','size.id')
                ->where('user_id',$request->input('user_id'))
                ->get();
            return $res;
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
        //
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
        $res=DB::table('shopping_cart')->select('product.*','product_stock.*','product.id as product_id','product_stock.id as stock_id','attributes.id as  color_id','size.id as  size_id','attributes.name as  color_name','size.name as size_name')
            ->leftJoin('product','shopping_cart.product_id','=','product.id')
            ->leftJoin('product_stock','shopping_cart.stock_id','=','product_stock.id')
            ->leftJoin('product_attributes as attributes','product_stock.color_id','=','attributes.id')
            ->leftJoin('product_attributes as size','product_stock.size_id','=','size.id')
            ->where('shopping_cart.id',$id)
            ->get();
            return $res[0];
        }catch (\Exception $e){
           return ReturnData::returnDataError($e->getMessage(),402);
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
        //
        try{
            $ishas=DB::table('shopping_cart')->where('id',$id)->exists();
            if($ishas){
                DB::table('shopping_cart')->where('id',$id)->update([
                    'is_detele'=>1]);
                return ReturnData::returnDataError('成功',200);
            }else{
                throw new \Exception('商品不存在');
            }

        }catch (\Exception $e){
            return ReturnData::returnDataError($e->getMessage(),402);
        }
    }
}
