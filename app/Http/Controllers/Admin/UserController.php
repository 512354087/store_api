<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Utils\ReturnData;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


/**
 * Created by PhpStorm.
 * User: dl
 * Date: 2018/7/6
 * Time: 15:03
 */
 class UserController extends Controller{
    public function login(Request $request)
    {
        try{
            $this->validate($request,[
                'username'=>'required',
                'password'=>'required'
            ]);
            $ishas=DB::table('t_admin')
                ->where('username',$request->input('username'))
                ->where('password',$request->input('password'))
                ->exists();
            if ($ishas){
                $admin=DB::table('t_admin')
                    ->where('username',$request->input('username'))
                    ->where('password',$request->input('password'))
                    ->frist();
                return ReturnData::returnDataResponse($admin,200);
            }else{
                throw new \Exception('用户名或密码错误');
            }

        }catch (\Exception $e){
            return ReturnData::returnDataError(['msg'=>$e->getMessage()],402);
        }


    }
}