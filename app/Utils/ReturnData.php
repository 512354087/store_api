<?php
/**
 * Created by PhpStorm.
 * User: dl
 * Date: 2018/5/1
 * Time: 9:55
 */

namespace App\Utils;
class ReturnData
{
    /**
     * @param $status  列表为空时返回
     */
   static function returnEmptyListResponse($status){
       $result['status'] = $status;
       $result['data']['list'] = array();
       $result['data']['count'] = 0;
       $result['timestamp'] = time();
       return response()->json($result);
   }

    /**
     * 空数据响应
     */
    static function returnEmptyDataResponse()
    {
        $result['status'] = 200;
        $result['data'] = (object)null;
        $result['timestamp'] = time();
        return response()->json($result);
    }

    /**
     * 无分页数据列表响应
     */
    static function returnNoPageListResponse($list,$status)
    {
        $result['status'] = $status;
        $result['data']['list'] = $list;
        $result['timestamp'] = time();
        return response()->json($result);
    }

    /**
     * 数据响应
     */
    static function returnDataResponse($data,$status)
    {
        $result['status'] = $status;
        $result['data'] = $data;
        $result['timestamp'] = time();
        return response()->json($result);
    }

}


