<?php

namespace App\Http\Controllers\Product;

use App\Utils\ReturnData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ProductCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            $this->validate($request,[
                'product_id'=>'required|integer'
            ]);
            $id=$request->input('product_id') ? $request->input('product_id') : 1;


            $list=DB::select('select product_comment.*,users.name,users.nickname  from product_comment  LEFT JOIN users ON product_comment.reply_id=users.id  WHERE product_comment.product_id = ? ',[$id]);
            $comment_list=$this->resolve2($list);
            return ReturnData::returnDataResponse($comment_list,200);
        }catch (\ Exception $e){
            return ReturnData::returnDataError(['msg'=>$e->getMessage()],402);
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
}
