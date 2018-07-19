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
        try{
            //库存列表
            $stock_list=DB::table('product_stock')
                ->selectRaw(' product_stock.* ,a.id as color_id ,a.name as color_name, b.id as size_id ,b.name as size_name')
                ->where('product_stock.product_id',$id)
                ->leftJoin('product_attributes as a','a.id','=','product_stock.color_id')
                ->leftJoin('product_attributes as b','b.id','=','product_stock.size_id')
                ->get();
            //默认搜索条件 加上一个 尺码的第一个
            $res=DB::table('product')
                ->select('product_discount.*','product.introduction','product.id', 'product.title','product.product_no','product.price','product.sale_num','product.logo', 'product.brand_id','product.cate_id','product_brand.title as brand_name','product_cate.cate_name as cate_name','product_discount.purchasers as purchasers','product_discount.discount as discount' )
                ->leftJoin('product_brand', 'product.brand_id', '=', 'product_brand.id')
                ->leftJoin('product_cate','product.cate_id' ,'=','product_cate.id')
                ->leftJoin('product_discount', 'product.id', '=', 'product_discount.product_id')
                ->where('product.id',$id)
                ->first();
            $num=DB::select('select SUM(num) as num  from product_stock WHERE product_id = ?',[$res->id]);
            $list=DB::select('select product_comment.*,users.name,users.nickname  from product_comment  LEFT JOIN users ON product_comment.reply_id=users.id  WHERE product_comment.product_id = ? ',[$res->id]);
            $comment_list=$this->resolve2($list);
            $res->stock_list=$stock_list;
            $res->stock_num=$num[0]->num;
            $res->comment_list=$comment_list;
            return ReturnData::returnDataResponse($res,200);
        }catch (\Exception $e){
              return ReturnData::returnDataError($e->getMessage(),402);
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

    /**
     * @param Request $request   获得折扣商品
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
