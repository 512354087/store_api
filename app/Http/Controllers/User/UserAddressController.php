<?php

namespace App\Http\Controllers\User;

use App\Model\UserAddress;
use App\Utils\ReturnData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       $user_address=UserAddress::get();

        return ReturnData::returnNoPageListResponse($user_address,200);
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

       // try{

            $user=$request->all();
            $user_id=$request->input('user_id');
            if($user['is_default']){
                $data= UserAddress::where('user_id',$user_id)->where('is_default',1)->update(['is_default' => 0]);
                $res=UserAddress::create($user);
                return ReturnData::returnDataResponse($res,200);
            }else{
                $res=UserAddress::create($user);
                return ReturnData::returnDataResponse($res,200);
            }
//        }catch (\Exception $e){
//             return ReturnData::returnDataError('参数验证失败',401);
//        }
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
