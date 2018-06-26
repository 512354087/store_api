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
            $arr=$request->all();
            $limit=$request->input('limit') ? $request->input('limit') : 10;
            $offset=$request->input('offset') ? $request->input('offset') : 0;
            $is_delete=isset($arr['is_delete']) ? $arr['is_delete'] : '';
            //DB::enableQueryLog();
            $res=DB::table('shopping_cart')->select('shopping_cart.*','product.title','product.price','product.is_delete as  product_is_delete','product.logo','product_stock.color_id','product_stock.size_id','product_stock.num as stock_num','product.id as product_id','product_stock.id as stock_id','attributes.id as  color_id','size.id as  size_id','attributes.name as  color_name','size.name as size_name')
                ->leftJoin('product','shopping_cart.product_id','=','product.id')
                ->leftJoin('product_stock','shopping_cart.stock_id','=','product_stock.id')
                ->leftJoin('product_attributes as attributes','product_stock.color_id','=','attributes.id')
                ->leftJoin('product_attributes as size','product_stock.size_id','=','size.id')
                ->whereRaw('case when ? then shopping_cart.user_id= ? else 1=1 end',[$request->input('user_id'),$request->input('user_id')])
                ->whereRaw("case when ? <> '' then shopping_cart.is_delete = ? else 1=1 end",[$is_delete,$is_delete])
                ->limit($limit)
                ->offset($offset)
                ->get();
            //return DB::getQueryLog();
            $count=DB::table('shopping_cart')
                ->whereRaw('case when ? then shopping_cart.user_id= ? else 1=1 end',[$request->input('user_id'),$request->input('user_id')])
                ->whereRaw("case when ? <> '' then shopping_cart.is_delete = ? else 1=1 end",[$is_delete,$is_delete])
                ->count();
            return ReturnData::returnListResponse($res,$count,200);
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
                'user_id'=>'required|integer',
                'stock_id'=>'required|integer',
                'num'=>'required|integer',
                'product_id'=>'required|integer'
            ]);
            //判断购物车中是否由此条记录
            $res=DB::table('shopping_cart')->where([
                ['user_id', '=', $request->input('user_id')],
                ['product_id', '=',$request->input('product_id')],
                ['stock_id', '=', $request->input('stock_id')]
            ])->get();

            if (count($res)){
                DB::table('shopping_cart')->where('id',$res[0]->id)->update([
                    'num'=> $res[0]->num+$request->input('num')
                ]);
                return ReturnData::returnDataResponse(['id'=>$res[0]->id],200);
            }else{
                $newId=DB::table('shopping_cart')->insertGetId([
                    'user_id'=>$request->input('user_id'),
                    'num'=>$request->input('num'),
                    'product_id'=>$request->input('product_id'),
                    'stock_id'=>$request->input('stock_id'),
                    'is_delete'=>0
                ]);
                return ReturnData::returnDataResponse(['id'=>$newId],200);
            }
        }catch (\Exception $e){
            return ReturnData::returnDataError($e->getMessage(),401);
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
            $res=DB::table('shopping_cart')->select('product.*','product_stock.*','product.id as product_id','product_stock.id as stock_id','attributes.id as  color_id','size.id as  size_id','attributes.name as  color_name','size.name as size_name')
                ->leftJoin('product','shopping_cart.product_id','=','product.id')
                ->leftJoin('product_stock','shopping_cart.stock_id','=','product_stock.id')
                ->leftJoin('product_attributes as attributes','product_stock.color_id','=','attributes.id')
                ->leftJoin('product_attributes as size','product_stock.size_id','=','size.id')
                ->where('shopping_cart.id',$id)
                ->first();
             return ReturnData::returnDataResponse($res,200);
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
    public function update(Request $request,$id)
    {
          
        //修改购物车商品
        try{
            $list=$request->all();
            DB::table('shopping_cart')->where('id',$id)->update(
                $list
            );
            return ReturnData::returnDataResponse(['msg'=>'修改成功'],200);
        }catch (\Exception $e){
            return ReturnData::returnDataError(['msg'=>$e->getMessage()],402);
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
        //
        //
        try{
            $ishas=DB::table('shopping_cart')->where('id',$id)->exists();
            if($ishas){
                DB::table('shopping_cart')->where('id',$id)->update([
                    'is_delete'=>1]);
                return ReturnData::returnDataError('成功',200);
            }else{
                throw new \Exception('商品不存在,无法删除');
            }

        }catch (\Exception $e){
            return ReturnData::returnDataError($e->getMessage(),402);
        }
    }
}
