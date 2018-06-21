<?php

namespace App\Http\Controllers\Product;

use App\Model\Product;
use App\Utils\ReturnData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;


class ProductStockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            $product_id = $request->input('product_id') ? $request->input('product_id') : false;
            $color_id = $request->input('color_id') ? $request->input('color_id') : false;
            $size_id = $request->input('size_id') ? $request->input('size_id') : false;
            $res=DB::table('product_stock')
            ->select('product_stock.*', 'a.id as color_id' ,'a.name as color_name', 'b.id as size_id' ,'b.name as size_name' )
            ->whereRaw('CASE  WHEN  ? THEN  product_stock.product_id = ? else 1=1 ',[$product_id,$product_id])
            ->whereRaw('CASE  WHEN  ? THEN  product_stock.color_id = ? else 1=1 ',[$color_id,$color_id])
            ->whereRaw('CASE  WHEN  ? THEN  product_stock.size_id = ? else 1=1 ',[$size_id,$size_id])
            ->leftJoin('product_attributes as a','a.id','=','product_stock.color_id')
            ->leftJoin('product_attributes as b','b.id','=','product_stock.size_id')
            ->get();
            return ReturnData::returnDataResponse($res,200);
        } catch (\Exception $e){
            return ReturnData::returnDataError($e,402);
        }
         //热销商品的搜索->leftJoin('posts', 'users.id', '=', 'posts.user_id')

        //SELECT product.id,product.product_no,product.price,product.sale_num,product.logo,b.title as brand_name,c.cate_name cate_name FROM product LEFT JOIN  product_brand b  on product.brand_id = b.id
        //LEFT JOIN product_cate c ON product.cate_id = c.id  ORDER BY product.sale_num DESC LIMIT 6 OFFSET 0
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



        }catch (\Exception $e){

        }


    }
    public function resolve2( $list,$pid = 0) {
        $manages = array();
        foreach ($list as $row) {
            if ($row->pid == $pid) {
                  $manages[] = $row;
                  $children = $this->resolve2($list, $row->id);
                foreach ($manages as $m=>$n){
                    if ($n->id == $row->id) {
                        $children && $n->children = $children;
                    }
                }
//
            }
        }
        return $manages;
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
