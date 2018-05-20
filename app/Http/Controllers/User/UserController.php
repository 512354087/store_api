<?php

namespace App\Http\Controllers\User;


use App\Model\User;
use App\Utils\ReturnData;
use App\Utils\WXBizDataCrypt;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{

    /**
     * @var string
     */
    private $appId;
    private $secret;
    private $code2session_url;
    private $sessionKey;

    function __construct()
    {
        $this->appId = config('app.appid', '');
        $this->secret = config('app.secret', '');
        $this->code2session_url = config('app.code2session_url', '');
    }

    public function index(Request $request)
    {


    }

    public function show($id)
    {
        $user=User::where('id', $id)->first();
        return ReturnData::returnDataResponse($user,200);
    }

    public function wx_login()
    {
        //code 在小程序端使用 wx.login 获取
        $code = request('code', '');
        //encryptedData 和 iv 在小程序端使用 wx.getUserInfo 获取
        $encryptedData = request('encryptedData', '');
        $iv = request('iv', '');
        $res=$this->getLoginInfo($code);
        //获取解密后的用户信息
        $user=$this->getUserInfo($encryptedData, $iv);
        return $this->create(json_decode($user,true),$res);
    }

    public function create($data,$res){
        $token = Hash::make($res['openid'].$res['session_key']);
        $res1=DB::select('SELECT * FROM users WHERE openId = ? ',[$data['openId']]);
        if (count($res1)){
          DB::table('users')
                ->where('openId',$data['openId'])
                ->update(['token' => $token]);
        }else{
            DB::table('users')->insert(
                ['openid' => $data['openId'], 'nickname' =>$data['nickName'],'token'=>$token,'address'=>$data['country'].$data['province'].$data['city'],]
            );
        }
        $res2=DB::select('SELECT * FROM users WHERE openId = ? ',[$data['openId']]);
        $data['id']=$res2[0]->id;
        $data['token']=$res2[0]->token;
        $data['expires_in']=$res['expires_in'];

        return ReturnData::returnDataResponse($data,200);
    }
    public function getLoginInfo($code){
        return $this->authCodeAndCode2session($code);
    }

    public function authCodeAndCode2session($code){
        $code2session_url = sprintf($this->code2session_url,$this->appId,$this->secret,$code);
        $userInfo =$this->httpRequest($code2session_url);
        if(!isset($userInfo['session_key'])){
            return [
                'code' => 10000,
                'message' => '获取 session_key 失败',
            ];
        }
        $this->sessionKey = $userInfo['session_key'];
        return $userInfo;
    }

    /**
     * php 使用curl 发送http
     * @param $url
     * @param null $data
     * @return bool
     */
    private function httpRequest($url, $data='')
    {
        $ch = curl_init();
        //设置选项，包括URL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);//绕过ssl验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){    //如果为空则采用post 方式
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //执行并获取HTML文档内容
        $output = curl_exec($ch);
        if($output === FALSE ){
            return false;
        }
        //释放curl句柄
        curl_close($ch);
        return json_decode($output,JSON_UNESCAPED_UNICODE);

    }

    /**
     * Created by vicleos
     * @param $encryptedData
     * @param $iv
     * @return string
     * @throws \Exception
     */
    public function getUserInfo($encryptedData, $iv, $sessionKey = null){
        if (empty($sessionKey)) {
            $sessionKey = $this->sessionKey;
        }
        $pc = new WXBizDataCrypt($this->appId, $sessionKey);
        $decodeData = "";
        $errCode = $pc->decryptData($encryptedData, $iv, $decodeData);
        if ($errCode !=0 ) {
            return [
                'code' => 10001,
                'message' => 'encryptedData 解密失败'
            ];
        }
        return $decodeData;
    }

    /**
     *  更新用户信息
     */
    public function edit(Request $request,$id)
    {
    try{
         $phone=$request->input('phone') ? $request->input('phone') : '';

         $name=$request->input('name') ? $request->input('name') : '';

        $data=array(
           'phone'=>$phone,
            'name'=>$name
        );
        foreach( $data as $k=>$v){
            ;
            if( !$v )
             unset( $data[$k] );
        }
        User::where('id', $id)
            ->update($data);

            return ReturnData::returnDataResponse(1,200);

        }catch (\Exception $e){
           return ReturnData::returnDataError('验证失败',402);
        }

    }
}
