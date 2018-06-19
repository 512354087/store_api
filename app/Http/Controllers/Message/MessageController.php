<?php

namespace App\Http\Controllers\Message;

use App\Utils\ReturnData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
class MessageController extends Controller
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
            $limit=$request->input('limit') ? $request->input('limit') : 10;
            $offset=$request->input('offset') ? $request->input('offset') : 0;
            $res=DB::table('user_message')->select('user_message.*','t_order.*','t_order.id as order_id')
                ->leftJoin('t_order','user_message.order_id','=','t_order.id')
                ->whereRaw('case when ? then user_message.user_id= ? when ? then user_message.type= ? else 1=1 end',[$request->input('user_id'),$request->input('user_id'),$request->input('type'),$request->input('type')])
                ->limit($limit)
                ->offset($offset)
                ->get();
            $count=DB::table('user_message')
                ->whereRaw('case when ? then user_message.user_id= ? when ? then user_message.type= ? else 1=1 end',[$request->input('user_id'),$request->input('user_id'),$request->input('type'),$request->input('type')])
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
        try{
            DB::table('user_message')->where('id',$id)->update(
                $request->all()
            );
            return ReturnData::returnDataResponse(1,200);
        }catch (\Exception $e){
            return ReturnData::returnDataError($e->getMessage(),402);
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
    }
}
