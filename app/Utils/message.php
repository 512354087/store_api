<?php
/**
 * Created by PhpStorm.
 * User: dl
 * Date: 2018/6/19
 * Time: 14:40
 */
namespace App\Utils;
use Illuminate\Support\Facades\DB;
class Message{
    static function create($type=0,$user_id,$order_id,$message=''){
       $id=DB::table('user_message')->insertGetId(
           [
               'type'=>$type,
               'user_id'=>$user_id,
               'order_id'=>$order_id,
               'message'=>$message,
               'is_read'=>0  //默认未读
           ]
        );
       return $id;
    }

}