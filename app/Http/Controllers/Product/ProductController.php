<?php

namespace App\Http\Controllers\Product;

use App\Model\Product;
use App\Utils\ReturnData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            $offset = $request->input('offset') ? $request->input('offset') : 0;
            $limit = $request->input('limit') ? $request->input('limit') : 6;
            $order = $request->input('order') ? $request->input('order') : 'id';
            $type = $request->input('type') ? $request->input('type') : 'desc';
            $title = $request->input('title') ? $request->input('title') : '';
            $res=DB::table('product')
            ->select('product.id', 'product.title','product.product_no','product.price','product.sale_num','product.logo', 'product.brand_id','product.cate_id','product_brand.title as brand_name','product_cate.cate_name as cate_name','product_discount.purchasers as purchasers','product_discount.discount as discount' )
            ->where([['product.is_delete','=',0],['product.title','like','%'.$title.'%']])
            ->leftJoin('product_brand', 'product.brand_id', '=', 'product_brand.id')
            ->leftJoin('product_cate','product.cate_id' ,'=','product_cate.id')
            ->leftJoin('product_discount', 'product.id', '=', 'product_discount.product_id')
            ->orderBy($order, $type)
            ->offset($offset)
            ->limit($limit)
            ->get();
            $count = DB::table('product')
            ->where([['product.is_delete','=',0],['product.title','like','%'.$title.'%']])
            ->leftJoin('product_brand', 'product.brand_id', '=', 'product_brand.id')
            ->leftJoin('product_cate','product.cate_id' ,'=','product_cate.id')
            ->leftJoin('product_discount', 'product.id', '=', 'product_discount.product_id')
            ->count();
            return ReturnData::returnListResponse($res,$count,200);
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

    /**
     * @param Request $request   获得折扣商品00
     */
    public function discount(Request $request)
    {
        try{
            $offset = $request->input('offset') ? $request->input('offset') : 0;
            $limit = $request->input('limit') ? $request->input('limit') : 6;
            $res=DB::table('product_discount')
            ->leftJoin('product','product_discount.product_id', '=', 'product.id')
            ->offset($offset)
            ->limit($limit)
            ->get();
            $count = DB::table('product_discount')
                ->leftJoin('product','product_discount.product_id', '=', 'product.id')
                ->count();
          return ReturnData::returnListResponse($res,$count,200);
        } catch (\Exception $e){
            return ReturnData::returnDataError($e,402);
        }
    }
}
