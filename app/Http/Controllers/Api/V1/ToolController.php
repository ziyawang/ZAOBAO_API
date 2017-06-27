<?php

namespace App\Http\Controllers\Api\V1;

use App\Collect;
use App\Messages;
use App\Note;
use App\Publish;
use App\System;
use App\Update;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use App\Project;
use App\Types;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use JWTAuth;
use App\Service;
use Cache;
use App\User;
use Mockery\CountValidator\Exception;
use Tymon\users\Exceptions\JWTException;
use DB;

class ToolController extends Controller
{
    use Helpers;

    /**
     * 获取定制信息
     * parameter $token  $types;
     * @return mixed
     */
    public function getLabel(){
        $payload = app('request')->all();
        $user = JWTAuth::parseToken()->authenticate();
        $userId=$user['userid'];
        $counts=Types::where("userId",$userId)->count();
        if(empty($counts)){
            try{
                Types::insert([
                    "types"=>$payload['types'],
                    "userId"=>$userId,
                    "created_at"=>date("Y-m-d H:i;s",time()),
                    "updated_at"=>date("Y-m-d H:i;s",time()),
                ]);
            }catch (Exception $e){
                throw $e;
            }
        }else{
            try{
                Types::where("userId",$userId)->update([
                    "types"=>$payload['types'],
                    "created_at"=>date("Y-m-d H:i;s",time()),
                    "updated_at"=>date("Y-m-d H:i;s",time()),
                ]);
            }catch (Exception $e){
                throw $e;
            }
        }
        if(isset($e)){
            return $this->response->array(['status_code' => '400','error_msg'=>"提交定制失败"]);
        }else{
            return $this->response->array(['status_code' => '200','success_msg'=>"提交定制成功"]);
        }
    }

    /**
     * 我的发布
     * parameter $token
     * @return mixed
     */
    public  function  myPublish(){
         $user = JWTAuth::parseToken()->authenticate();
        $userId=$user['userid'];
        $data=array();
        $result1=Publish::select("type","created_at")->where("userId",$userId)->where("type",1)->orderBy("created_at","desc")->take(1)->get();
        $result2=Publish::select("type","created_at")->where("userId",$userId)->where("type",2)->orderBy("created_at","desc")->take(1)->get();
        if($result1){
            foreach($result1 as $val){
                $times=$val->created_at;
                $val->publishTime=date("Y-m-d H:i",strtotime($times));
                $data[]=$val;
            }
        }
        if($result2){
            foreach($result2 as $value){
                $times=$value->created_at;
                $value->publishTime=date("Y-m-d H:i",strtotime($times));
                $data[]=$value;
            }
        }

        if(empty($data)){
            return $this->response->array(["status_code"=>400,"error_msg"=>"您暂时未发布信息"]);
        }else{
            return $this->response->array(["status_code"=>200,'data'=>$data]);
        }
    }

    /**
     * 我的发布列表
     * @parameter $token $type
     * @return mixed
     */
    public function  publishList(){
        $payload=app("request")->all();
        $user = JWTAuth::parseToken()->authenticate();
        $userId=$user['userid'];
        $startpage = isset($payload['startpage']) ?  $payload['startpage'] : 1;
        $pagecount = isset($payload['pagecount']) ?  $payload['pagecount'] : 5;
        $skipnum = ($startpage-1)*$pagecount;
        $datas=Publish::select("describe","created_at","type","status")->where("userId",$userId)->where("type",$payload['type'])->skip($skipnum)->take($pagecount)->orderBy("created_at","desc")->get();
        if(count($datas)!=0){
            foreach ($datas as $val){
                if($val->status==0){
                    $val->label="资芽客服未浏览";
                }else{
                    $val->label="资芽客服已浏览";
                }
            }
            return $this->response->array(["status_code"=>200,"data"=>$datas]);
        }else{
            return $this->response->array(["status_code"=>400,"data"=>[]]);
        }
    }

    /**
     * 收藏操作
     * @parameter $token $projectId
     * @return mixed
     */
    public function  collect(){
        $payload=app("request")->all();
        $user = JWTAuth::parseToken()->authenticate();
        $userId=$user['userid'];
        $counts=Collect::where("userId",$userId)->where("projectId",$payload['projectId'])->count();
        $status="";
        if($counts){
            $datas=Collect::where("userId",$userId)->where("projectId",$payload['projectId'])->get();
            foreach($datas as $data){
                $status=$data->status;
            }
            if($status==0){
               $res=Collect::where("userId",$userId)->where("projectId",$payload['projectId'])->update([
                    "status"=>1,
                   "updated_at"=>date("Y-m-d H:i:s",time())
                ]);
                if($res){
                    return $this->response->array(["status_code"=>200,"success_msg"=>"收藏成功"]);
                }else{
                    return $this->response->array(["status_code"=>500,"error_msg"=>"收藏失败"]);
                }
            }else{
               $res= Collect::where("userId",$userId)->where("projectId",$payload['projectId'])->update([
                    "status"=>0,
                    "updated_at"=>date("Y-m-d H:i:s",time())
                ]);
                if($res){
                    return $this->response->array(["status_code"=>200,"success_msg"=>"取消收藏成功"]);
                }else{
                    return $this->response->array(["status_code"=>500,"error_msg"=>"取消收藏失败"]);
                }
            }
        }else{
            try{
                Collect::insert([
                    "userId"=>$userId,
                    "projectId"=>$payload['projectId'],
                    "status"=>1,
                    "created_at"=>date("Y-m-d H:i:s",time()),
                    "updated_at"=>date("Y-m-d H:i:s",time()),
                ]);
            }catch (Exception $e){
                throw $e;
            }
            if(!isset($e)){
                return $this->response->array(["status_code"=>200,"success_msg"=>"收藏成功"]);
            }else{
                return $this->response->array(["status_code"=>500,"error_msg"=>"收藏失败"]);
            }
        }
    }

    /**
     * 收藏列表
     * @parameter @token
     * @return mixed
     */
    public  function collectList(){
        $payload=app("request")->all();
        $user = JWTAuth::parseToken()->authenticate();
        $userId=$user['userid'];
        $startpage = isset($payload['startpage']) ?  $payload['startpage'] : 1;
        $pagecount = isset($payload['pagecount']) ?  $payload['pagecount'] : 5;
        $skipnum = ($startpage-1)*$pagecount;
        $counts=Collect::where("userId",$userId)->where("status",1)->count();
        if(!empty($counts)){
            $datas=Collect::leftJoin("T_P_PROJECT","T_P_PROJECT.projectId","=","T_P_COLLECT.projectId")
                ->select("T_P_PROJECT.*","T_P_COLLECT.status")
                ->where("userId",$userId)
                ->where("status",1)
                ->skip($skipnum)
                ->take($pagecount)
                ->orderBy("created_at","desc")
                ->get();
            if(count($datas)!=0){
                return $this->response->array(["status_code"=>200,"number"=>$counts,"data"=>$datas]);
            }else{
                return $this->response->array(["status_code"=>400,"data"=>[]]);
            }
        }else{
            return $this->response->array(["status_code"=>400,"data"=>[]]);
        }
        
    }

    /**
     * 我的系统消息
     * @parameter
     * @return mixed
     */
    public function  mySystem(){
        $payload=app("request")->all();
        $user = JWTAuth::parseToken()->authenticate();
        $userId=$user['userid'];
        $startpage = isset($payload['startpage']) ?  $payload['startpage'] : 1;
        $pagecount = isset($payload['pagecount']) ?  $payload['pagecount'] : 5;
        $skipnum = ($startpage-1)*$pagecount;
        $results=System::where("userId",$userId)->skip($skipnum)->take($pagecount)->orderBy("created_at","desc")->get();
        if(count($results)!=0){
            return $this->response->array(["status_code"=>200,"data"=>$results]);
        }else{
            return $this->response->array(["status_code"=>400,"data"=>[]]);
        }
    }

    /**
     * 意见反馈
     * @parameter $phone $content $connecter $token
     * @return mixed
     */
    public function  note(){
        $payload=app("request")->all();
        if(isset($payload['token'])){
            $user = JWTAuth::parseToken()->authenticate();
            $userId=$user['userid'];
            try{
                Note::insert([
                    "content"=>$payload['content'],
                    "connecter"=>$payload['connecter'],
                    "phone"=>$payload['phone'],
                    "userId"=>$userId,
                    "created_at"=>date("Y-m-d H:i:s",time()),
                    "updated_at"=>date("Y-m-d H:i:s",time())
                ]);
            }catch (Exception $e){
                throw $e;
            }
            if(!isset($e)){
                return $this->response->array(["status_code"=>200,"success_msg"=>"意见反馈成功"]);
            }else{
                return $this->response->array(["status_code"=>400,"error_msg"=>"意见反馈失败"]);
            }
        }else{
            try{
                Note::insert([
                    "content"=>$payload['content'],
                    "connecter"=>$payload['connecter'],
                    "phone"=>$payload['phone'],
                    "created_at"=>date("Y-m-d H:i:s",time()),
                    "updated_at"=>date("Y-m-d H:i:s",time())
                ]);
            }catch (Exception $e){
                throw $e;
            }
            if(!isset($e)){
                return $this->response->array(["status_code"=>200,"success_msg"=>"意见反馈成功"]);
            }else{
                return $this->response->array(["status_code"=>400,"error_msg"=>"意见反馈失败"]);
            }
        }

    }

    /**
     * 留言
     * @parameter $token $projectId $content
     * @return mixed
     */
    public function message(){
        $payload=app('request')->all();
        if(!empty($payload['token'])){
            $user = JWTAuth::parseToken()->authenticate();
            try{
                Messages::insert([
                    "phone"=>$user['phonenumber'],
                   // "picture"=>$user['UserPicture'],
                    "reply"=>"",
                    "userId"=>$user['userid'],
                    "content"=>$payload['content'],
                    "projectId"=>$payload['projectId'],
                    "created_at"=>date("Y-m-d H:i:s",time()),
                    "updated_at"=>date('Y-m-d H:i:s',time())
                ]);
            }catch(Exception $e){
                throw $e;
            }
        }else{
            try{
                Messages::insert([
                    "phone"=>"游客",
                   // "picture"=>'/user/defaltoux.jpg',
                    "reply"=>"",
                    "userId"=>0,
                    "content"=>$payload['content'],
                    "projectId"=>$payload['projectId'],
                    "created_at"=>date("Y-m-d H:i:s",time()),
                    "updated_at"=>date('Y-m-d H:i:s',time())
                ]);
            }catch(Exception $e){
                throw $e;
            }
        }
        if(!isset($e)){
            return $this->response->array(["status_code"=>200,"success_msg"=>"留言成功"]);
        }else{
            return $this->response->array(["status_code"=>400,"error_msg"=>"留言失败"]);
        }

    }

    /**
     * 我的留言
     * @parameter $token
     * @return mixed
     */
    public function myMessage(){
        $payload=app("request")->all();
        $user = JWTAuth::parseToken()->authenticate();
        $userId=$user['userid'];
        $startpage = isset($payload['startpage']) ?  $payload['startpage'] : 1;
        $pagecount = isset($payload['pagecount']) ?  $payload['pagecount'] : 5;
        $skipnum = ($startpage-1)*$pagecount;
        $datas=Messages::leftJoin("T_P_PROJECT","T_P_MESSAGE.projectId","=","T_P_PROJECT.projectId")
            ->select("T_P_MESSAGE.*","T_P_PROJECT.title")
            ->where("userId",$userId)
            ->skip($skipnum)
            ->take($pagecount)
            ->orderBy("created_at","desc")
            ->get();
        if(count($datas)){
            foreach($datas as $val){
                $val->picture=$user['UserPicture'];
                $times=strtotime($val->created_at);
                $val->time=date("Y-m-d H:i",$times);
            }
            return $this->response->array(["status_code"=>200,"data"=>$datas]);
        }else{
            return $this->response->array(["status_code"=>400,"data"=>[]]);
        }
    }

    /**
     * 修改昵称
     * @parameter $token $name
     * @return mixed
     */
    public  function  changeName(){
        $payload=app("request")->all();
        $user = JWTAuth::parseToken()->authenticate();
        $userId=$user['userid'];
        try{
            User::where("userid",$userId)->update([
                "username"=>$payload['name'],
                "updated_at"=>date("Y-m-d H:i:s",time())
            ]);
        }catch(Exception $e){
            throw $e;
        }

        if(!isset($e)){
            return $this->response->array(["status_code"=>200,"success_msg"=>"修改成功"]);
        }
            return $this->response->array(["status_code"=>400,"error_msg"=>"修改失败"]);
    }

    /**
     * $parameter $koken $UserPicture
     * @return mixed
     */
    public function upload(){
        $image_path=dirname(base_path()).'/ziyaupload/images/user/';
      if(!is_dir($image_path)){
           mkdir($image_path,0777,true);
      }
       $baseName=basename($_FILES['UserPicture']['name'] );
       $extension=strrchr($baseName, ".");
        $newName=time() . mt_rand(1000, 9999).$extension;
        $target_path = $image_path . $newName;
        $filePath="/user/".$newName;
       if(move_uploaded_file($_FILES['UserPicture']['tmp_name'], $target_path)){
           $user = JWTAuth::parseToken()->authenticate();
           $userId=$user['userid'];
           $dbs= DB::table("users")->where("userid",$userId)->update([
               "UserPicture"=>$filePath,
                "updated_at"=>date("Y-m-d H:i:s",time()),
           ]);
            if($dbs){
                return $this->response->array(['status_code'=>'200','success' => 'update User Success']);
            }else{
                return $this->response->array(['status_code'=>'409','success' => 'update User Error']);
            }

       }

        return $this->response->array(['status_code'=>'200','success' => 'update User Success']);
    }

    /**
     * 修改密码
     * @param $token  $password
     * @return mixed
     */

    public function  changePwd(){
        // 验证规则
        $rules = [
            'password' => ['required', 'min:6'],
        ];

        $payload = app('request')->only('password');
        $validator = app('validator')->make($payload, $rules);
        if ($validator->fails()) {
            return $this->response->array(['status_code' => '401', 'msg' => $validator->errors()]);
        }

        // 获取用户id和新密码
        $payload = app('request')->only('password');
        $password = $payload['password'];

        // 更新用户密码
        $user = JWTAuth::parseToken()->authenticate();
        $userId=$user['userid'];
        $res = User::where("userid",$userId)->update([
            "password"=>bcrypt($password),
            "updated_at"=>date("Y-m-d H:i:s",time())
        ]);

        // 发送结果
        if ($res) {
            return $this->response->array(['status_code'=>'200','msg' => 'Password Change Success']);
        } else {
            return $this->response->array(['status_code'=>'410','msg' => 'Password Change Error']);
        }
    }

    /**
     * 获取用户已定制的信息
     * @param  $token
     * @return mixed
     */
    public function  returnType(){
        $user = JWTAuth::parseToken()->authenticate();
        $userId=$user['userid'];
        $types=Types::where("userId",$userId)->pluck("types");
       if(!empty($types)){
           $type=explode(",",$types);
           return  $this->response->array(["status_code"=>200,"data"=>$type]);
       }else{
           return  $this->response->array(["status_code"=>400,"data"=>array()]);
       }
    }

    /**
     * 分享链接
     * @param $projectId
     * @return mixed
     */
    public  function detail($projectId){
        $datas=Project::where("projectId",$projectId)->get();
        return view("project.detail",compact("datas"));
    }


    //安卓更新的接口
    public function update(){
        $datas=Update::get();
         if($datas){
             return $this->response->array(["status_code"=>200,"data"=>$datas]);
         }else{
             return $this->response->array(["status_code"=>400,"data"=>[]]);
         }
    }


}
