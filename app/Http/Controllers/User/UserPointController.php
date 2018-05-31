<?php

namespace App\Http\Controllers\User;

use App\Model\UserPoint;
use App\Model\UserPointLog;
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
            //搜索某个时间段内的数据库匹配的数据
            // SELECT *,SUM(a.point) as total  FROM (SELECT user_point_log.*,user_point_type.title AS title FROM user_point_log LEFT JOIN user_point_type ON user_point_type.id = user_point_log.type_id ) AS a
            //WHERE a.user_id = 1  And  a.create_at  between '2010-7-12 11:18:54' and '2019-7-12 11:22:20'

            //搜索一个星期内数据库匹配的数据
            // SELECT user_point_log.*,user_point_type.title AS title FROM user_point_log LEFT JOIN user_point_type ON user_point_type.id = user_point_log.type_id
            // WHERE user_point_log.user_id = 1 And  DATE_SUB(CURDATE(), INTERVAL 7 DAY) <= date(user_point_log.create_at);

            $list=DB::table('user_point_log')->leftJoin('user_point_type','user_point_log.type_id','=','user_point_type.id')
                ->where([['user_id', '=', $user_id]])
                //->whereNotNull('create_at')    查找不为空的字段
                ->get();
            return ReturnData::returnListResponse($list,10,200);

        }catch (\Exception $e){
            return ReturnData::returnDataError('参数错误',402);
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
        try{
            $pointLog=$request->all();
            $user_id=$request->input('user_id');
            //判断数据库中是否有用户   是否有积分表
            $Point=DB::table('user_point')->where('user_id', $user_id)->first();
            $isHasUser=DB::table('users')->where('id', $user_id)->exists();
            //手动控制数据库事务
            DB::beginTransaction();
              $pointLog['created_at'] =date("Y-m-d  H:i:s",time());

            if ($isHasUser){
                DB::table('user_point_log')->insert([
                    $pointLog
                ]);

                if($Point){
                    $num=$pointLog['type']==1 ? $pointLog['point'] + $Point->num :  $Point->num - $pointLog['point'];
                    DB::table('user_point')->where('user_id', $user_id)->update(['created_at' => date("Y-m-d H:i:s",time()),'updated_at'=>date("Y-m-d H:i:s",time()),'num'=> $num>0 ? $num :0]);

                }else{
                    $num=$pointLog['type']==1 ? $pointLog['point'] + $Point->num :  $Point->num - $pointLog['point'];
                    DB::table('user_point')->insert([
                        ['created_at' => date("Y-m-d H:i:s",time()),'updated_at'=>date("Y-m-d H:i:s",time()),'user_id'=>$user_id,'num'=> $num >0 ? $num :0]
                    ]);
                }
            }
          DB::commit();
           return ReturnData::returnDataResponse(1,200);

        }catch (\Exception $e){
           DB::rollBack();
            return ReturnData::returnDataError($e,402);
        }

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

    /**
     * 更改用户积分总数
     * @param type $
     * @param num $
     */
    public function updateNum($type,$num,$user_id)
    {
        $resnum=DB::table('user_point')->where('user_id',$user_id)->value('num');
        $res=DB::table('user_point')->where('user_id',$user_id)->update(['num'=>$type==1 ? $num+$resnum : $resnum- $num]);
        return $res;
    }
}
