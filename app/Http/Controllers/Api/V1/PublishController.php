<?php

namespace App\Http\Controllers\Api\V1;

use App\Publish;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Dingo\Api\Routing\Helpers;
use JWTAuth;
use App\User;
use Cache;
use Tymon\users\Exceptions\JWTException;
use DB;

class PublishController extends Controller
{
    use Helpers;

    /**
     * @parameter $token $describe $company $connecter $phone
     * @return mixed
     * @throws Exception
     */
   public  function publish(){
       $payload=app('request')->all();
       $user = JWTAuth::parseToken()->authenticate();
       $userId=$user['userid'];
       try{
           Publish::insert([
               "userId"=>$userId,
               "describe"=>$payload['describe'],
               "company"=>$payload['company'],
               "connecter"=>$payload['connecter'],
               "phone"=>$payload['phone'],
               "created_at"=>date("Y-m-d H:i:s",time()),
               "updated_at"=>date("Y-m-d H:i:s",time()),
               "type"=>$payload['type']
           ]);
       }catch (Exception $e){
           throw $e;
       }
       if(!isset($e)){
           return $this->response->array(["status_code"=>200,"success_mes"=>"发布成功"]);
       }else{
           return $this->response->array(["status_code"=>500,"success_mes"=>"发布失败"]);

       }
   }

}
