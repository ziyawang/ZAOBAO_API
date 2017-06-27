<?php

namespace App\Http\Controllers\Api\V1;

use App\Check;
use App\Collect;
use App\Messages;
use App\Project;
use App\Types;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Dingo\Api\Routing\Helpers;
use JWTAuth;
use Cache;
use Tymon\users\Exceptions\JWTException;
use DB;

class IndexController extends Controller
{
    use Helpers;

    /**
     * parameter $token
     * @return mixed
     */
    public function index(){
        $payload = app('request')->all();
        $startpage = isset($payload['startpage']) ?  $payload['startpage'] : 1;
        $pagecount = isset($payload['pagecount']) ?  $payload['pagecount'] : 5;
        $skipnum = ($startpage-1)*$pagecount;
        if(!empty($payload['token'])){
            $packageWhere=array();
            $legalWhere=array();
            $landWhere=array();
            $houseWhere=array();
            $officeWhere=array();
            $user = JWTAuth::parseToken()->authenticate();
            $userId=$user['userid'];
            $types=Types::where("userId",$userId)->pluck("types");
            $type=explode(",",$types);
            foreach ($type as $value){
                switch($value){
                    case 1:
                        $packageWhere=array("package"=>1);
                    break;
                    case 2:
                        $legalWhere=array("legal"=>2);
                        break;
                    case 3:
                        $landWhere=array("land"=>3);
                        break;
                    case 4:
                        $houseWhere=array("house"=>4);
                        break;
                    case 5:
                        $officeWhere=array("office"=>5);
                        break;
                }
            }
            $datas=Project::skip($skipnum)
                ->take($pagecount)
                ->where($packageWhere)
                ->where("deleteLabel",0)
                ->where("publishStatus",2)
                ->orWhere($legalWhere)
                ->orWhere($landWhere)
                ->orWhere($houseWhere)
                ->orWhere($officeWhere)
                ->orderBy("created_at","desc")
                ->get();
            foreach($datas as $val ){
                $projectId=$val->projectId;
                $collects=Collect::where(["userId"=>$userId,"projectId"=>$projectId])->pluck("status");
                if(!empty($collects)){
                    switch($collects){
                        case 0:
                            $val->status=0;
                            break;
                        case 1:
                            $val->status=1;
                            break;
                    }
                }else{
                    $val->status=0;
                }
            }
        }else{

            $datas=Project::skip($skipnum)->take($pagecount)->where("deleteLabel",0)->orderBy("created_at","desc")->get();
            foreach ($datas as $val){
                $val->status=0;
            }
        }
        return $this->response->array(['status_code' => '200','data'=>$datas]);
    }

    /**
     * 首页列表详情页
     * @param $projectId $token
     * @return mixed
     */
    public function  getDetail($projectId){
        $payload=app("request")->all();
        Project::where("projectId",$projectId)->increment("viewCount");
        if(!empty($payload['token'])){
            $user = JWTAuth::parseToken()->authenticate();
            $userId=$user['userid'];
            Check::insert([
                "projectId"=>$projectId,
                "userId"=>$userId,
                "created_at"=>date("Y-m-d H:i:s",time()),
                "updated_at"=>date("Y-m-d H:i:s",time()),
            ]);
            $user = JWTAuth::parseToken()->authenticate();
            $userId=$user['userid'];
            $datas=Project::where("projectId",$projectId)->get();
            foreach($datas as $val ){
                $collects=Collect::where(["userId"=>$userId,"projectId"=>$projectId])->pluck("status");
                if(!empty($collects)){
                    switch($collects){
                        case 0:
                            $val->status=0;
                            break;
                        case 1:
                            $val->status=1;
                            break;
                    }
                }else{
                    $val->status=0;
                }
            }
        }else{
            $datas=Project::where("projectId",$projectId)->get();
            foreach ($datas as $val){
                $val->status=0;
            }
        }
        return $this->response->array(['status_code'=>200,"data"=>$datas]);
    }

    /**
     * 信息详情页的留言
     * @parameter $project
     * @return mixed
     */
    public function  getMessage(){
        $payload=app('request')->all();
        $startpage = isset($payload['startpage']) ?  $payload['startpage'] : 1;
        $pagecount = isset($payload['pagecount']) ?  $payload['pagecount'] : 10;
        $skipnum = ($startpage-1)*$pagecount;
        $datas=Messages::where("projectId",$payload['projectId'])->skip($skipnum)->take($pagecount)->orderBy("created_at","desc")->get();
        if(count($datas)!=0){
            foreach ($datas as $data){
                $userId=$data->userId;
                $picture=User::where("userid",$userId)->pluck("UserPicture");
                $data->picture=!empty($picture)?$picture:"/user/defaltoux.jpg";
            }
            return $this->response->array(['status_code'=>200,"data"=>$datas]);
        }else{
            return $this->response->array(['status_code'=>400,"error_msg"=>"暂无留言"]);
        }
    }
}
