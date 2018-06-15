<?php

namespace App\Http\Controllers\User;

use App\Utils\ReturnData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class UserStarController extends Controller
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
                'user_id'=>'required|integer'
            ]);
            $limit=$request->input('limit') ? $request->input('limit') : 10;
            $offset=$request->input('offset') ? $request->input('offset') : 0;
//            return $this->response=[
//                'limit' => $limit,
//                'offset' => $offset
//            ];
            $res=DB::table('user_star')
                ->where('user_id',$request->input('user_id'))
                ->leftJoin('product','user_star.product_id','=','product.id')
                ->limit($limit)
                ->offset($offset)
                ->get();

            $count=DB::table('user_star')
                ->where('user_id',$request->input('user_id'))
                ->leftJoin('product','user_star.product_id','=','product.id')
                ->count();
            return ReturnData::returnListResponse($res,$count,200);
        }catch (\Exception $e){
            return ReturnData::returnDataError($e->getMessage(),401);
        }


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {


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
        try{
            $this->validate($request,[
                'user_id'=>'required|integer',
                'product_id'=>'required|integer'
            ]);
            //是否存在此条收藏   如果存在  则返回收存成功   除了通过 count 方法来判断匹配查询条件的结果是否存在外，还可以使用 exists 或 doesntExist 方法：    是否存在   手否不存在
            $isNotExist=DB::table('user_star')->where([
                ['user_id', '=', $request->input('user_id')],
                ['product_id', '=', $request->input('product_id')],
            ])->doesntExist();
            if($isNotExist){
                DB::table('user_star')->insert(
                    ['user_id' => $request->input('user_id'), 'product_id' => $request->input('product_id')]
                );
            }

            return ReturnData::returnDataResponse('成功',200);
        }catch (\Exception $e){
            return ReturnData::returnDataError($e->getMessage(),401);
        }

    }


    public function unStar($product_id,$user_id){

        try{
            //是否存在此条收藏   如果存在  则返回收存成功   除了通过 count 方法来判断匹配查询条件的结果是否存在外，还可以使用 exists 或 doesntExist 方法：    是否存在   手否不存在
            $isExist=DB::table('user_star')->where([
                ['user_id', '=', $user_id],
                ['product_id', '=', $product_id],
            ])->exists();
            if ($isExist){
                DB::table('users')->where([
                    ['product_id', '=', $product_id],
                    ['user_id', '=', $user_id]
                ])->delete();
            }

            return ReturnData::returnDataResponse(['message'=>'删除成功'],200);
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
