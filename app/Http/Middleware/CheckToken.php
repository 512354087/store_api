<?php

namespace App\Http\Middleware;

use App\Utils\ReturnData;
use Closure;
use Illuminate\Support\Facades\DB;

class CheckToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->header('token');
       if($token){
           $user=DB::table('users')
               ->where('token',$token)
               ->first();
           if ($user){
               return $next($request);
           }else{
               return ReturnData::returnDataError('认证失败',401);
           }

       }else{
           return ReturnData::returnDataError('登录已过期',401);
       }

    }
}
