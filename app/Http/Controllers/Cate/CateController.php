<?php

namespace App\Http\Controllers\Cate;

use App\Utils\ReturnData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pid=$request->input('pid') ? $request->input('pid') : 0;
        $data = DB::select('select * from product_cate WHERE pid= ?',[$pid]);
        return ReturnData::returnNoPageListResponse($data,200);
    }

    public function keyword(Request $request){
        $keyword=$request->input('keyword') ? $request->input('keyword') : '';
        $data = DB::select('select * from product_cate WHERE cate_name like ? ',['%'.$keyword.'%']);
        return ReturnData::returnNoPageListResponse($data,200);
    }


    public function productAttributes(Request $request){
        $pid=$request->input('pid') ? $request->input('pid') : false;
        $data = DB::select('select * from product_attributes WHERE 1=1 AND IF(?, pid = ?, 0 = 0)',[$pid,$pid]);
        return ReturnData::returnNoPageListResponse($data,200);
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
