<?php
/**
 * Created by PhpStorm.
 * User: dl
 * Date: 2018/5/28
 * Time: 14:35
 */

namespace App\Utils;


class Util
{
    /**
     * 生成订单号
     */
    static function randomNum($length,$uid) {
        $time=date('YmdHis');
        //pow(10,2) 10的2次方
        return 'WXSTORE'.$time.rand(pow(10,($length-1)), pow(10,$length)-1).$uid;
    }

    /**
     * 判断时间是否在当天之内
     */
    static  function get_curr_time_section($old_time){
        $checkDayStr = date('Y-m-d ',time());
        $curr_time = date('Y-m-d ',strtotime($old_time));
        if($curr_time == $checkDayStr)
        {
             return 1;
        }
           return 0;
   }





}