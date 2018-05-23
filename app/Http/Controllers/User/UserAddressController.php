<?php

namespace App\Http\Controllers\User;

use App\Model\Address;
use App\Model\UserAddress;
use App\Utils\ReturnData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class UserAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       try{
        $user_id=$request->input('user_id') ? $request->input('user_id') : '';
           $user_address=UserAddress::select('user_address.*','address1.name as province','address2.name as city','address3.name as county','address4.name as town ')
               ->leftJoin('address as address1','user_address.province_id','=','address1.id')
               ->leftJoin('address as address2','user_address.city_id','=','address2.id')
               ->leftJoin('address as address3','user_address.county_id','=','address3.id')
               ->leftJoin('address as address4','user_address.town_id','=','address4.id');
        if($user_id)
            $res=$user_address->where('user_id',$user_id)->get();
        else
            $res=$user_address->get();
            return ReturnData::returnNoPageListResponse($res,200);

       }catch (\Exception $e){
            return ReturnData::returnDataError('参数错误',401);
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
    public function update(Request $request,$id)
    {

       try{
           $user=$request->all();
           $userAddress=UserAddress::where('id',$id)->update($user);
           return ReturnData::returnDataResponse($userAddress,200);
       }catch (\Exception $e){
           return ReturnData::returnDataError('参数错误',401);
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
