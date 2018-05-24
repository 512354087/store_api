<?php

namespace App\Http\Controllers\User;

use App\Utils\ReturnData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class UserPointController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $offset = $request->input('offset') ? $request->input('offset') : 0;
            $limit = $request->input('limit') ? $request->input('limit') : 10;
            $this->validate($request, [
                'user_id' => 'required|integer'
            ]);
            $user_id = $request->input('user_id');
            $points_record = DB::table('user_point')
              ->leftJoin('user_point_log as b','user_point.id','=','b.points_id')
              ->where('user_id',$user_id)
              ->offset($offset)
              ->limit($limit)
              ->get();
            return ReturnData::returnListResponse($points_record,10,200);
        }catch (\Exception $e){
            return ReturnData::returnDataError('参数错误',200);
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
