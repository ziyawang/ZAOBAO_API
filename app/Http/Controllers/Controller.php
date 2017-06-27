<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * 发送信息
     * @param $mobile
     * @param $message
     * @param $action
     * @return bool
     */
    protected function _sendSms($mobile, $message, $action)
    {
        require(base_path().'/vendor/alidayu/TopSdk.php');
        date_default_timezone_set('Asia/Shanghai');

        $c = new \TopClient;
        $c->appkey = '23401348';//需要加引号
        $c->secretKey = env('ALIDAYU_APPSECRET');
        $c->format = 'xml';
        $req = new \AlibabaAliqinFcSmsNumSendRequest;
        $req->setExtend("");//暂时不填
        $req->setSmsType("normal");//默认可用
        $req->setSmsFreeSignName("资芽网");//设置短信免费符号名(需在阿里认证中有记录的)
        $req->setSmsParam("{\"code\":\"{$message}\"}");//设置短信参数
        $req->setRecNum($mobile);//设置接受手机号
        if($action == 'register'){
            $req->setSmsTemplateCode("SMS_12660435");//设置模板
        } elseif($action == 'login') {
            $req->setSmsTemplateCode("SMS_12670230");//设置模板
        }
        $resp = $c->execute($req);//执行

        if($resp->result->success)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * 发送邮件
     * @param $email
     * @param $title
     * @param $msg
     */
    protected function _sendMail($email, $title, $msg)
    {
        // $data = ['email'=>$email, 'name'=>1, 'uid'=>1, 'activationcode'=>1];
        $data = ['email'=>$email, 'title'=>$title, 'msg'=>$msg];
        Mail::send('activemail', $data, function($message) use($data)
        {
            $message->to($data['email'])->subject($data['title']);
        });
    }
}
