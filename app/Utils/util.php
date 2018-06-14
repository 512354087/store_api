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
  
}