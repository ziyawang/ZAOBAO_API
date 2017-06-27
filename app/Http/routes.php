<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});
$api = app('Dingo\Api\Routing\Router');
//共有接口不需要登录
$api->version('v1', function ($api) {
    // 用户注册 Token
    $api->post('/register', 'App\Http\Controllers\Api\V1\LoginController@register');
    //发送邮件
    $api->get('/sendmail', 'App\Http\Controllers\Api\V1\LoginController@sendmail');
    //获取手机验证码
    $api->get('/getSmsCode', 'App\Http\Controllers\Api\V1\LoginController@getSmsCode');
    //用户登录验证
    $api->get('/login', 'App\Http\Controllers\Api\V1\LoginController@login');
    //首页
    $api->get('/index', 'App\Http\Controllers\Api\V1\IndexController@index');
    //项目详情页
    $api->get('/getDetail/{projectId}', 'App\Http\Controllers\Api\V1\IndexController@getDetail');
    //留言
    $api->post('/message','App\Http\Controllers\Api\V1\ToolController@message');
    //获取留言
    $api->get('/getMessage','App\Http\Controllers\Api\V1\IndexController@getMessage');
    //忘记密码
    $api->post('/resetPassword','App\Http\Controllers\Api\V1\LoginController@resetPassword');
	
	 //意见反馈
    $api->post('/note','App\Http\Controllers\Api\V1\ToolController@note');
	//app分享链接
    $api->get('/detail/{projectId}','App\Http\Controllers\Api\V1\ToolController@detail');
	//androad
    $api->get('update','App\Http\Controllers\Api\V1\ToolController@update');


});
//私有接口需要登录
$api->version('v1', ['middleware' => 'jwt.auth'], function ($api) {
    //定制信息
    $api->post('/getLabel','App\Http\Controllers\Api\V1\ToolController@getLabel');
    //发布
    $api->post('/publish','App\Http\Controllers\Api\V1\PublishController@publish');
    //我的发布
    $api->post('/myPublish','App\Http\Controllers\Api\V1\ToolController@myPublish');
    //发布详情
    $api->post('/publishList','App\Http\Controllers\Api\V1\ToolController@publishList');
    //收藏
    $api->post('/collect','App\Http\Controllers\Api\V1\ToolController@collect');
    //收藏列表
    $api->post('/collectList','App\Http\Controllers\Api\V1\ToolController@collectList');
    //我的系统消息
    $api->post('/mySystem','App\Http\Controllers\Api\V1\ToolController@mySystem');
   
   //我的留言
    $api->post('/myMessage','App\Http\Controllers\Api\V1\ToolController@myMessage');
    //changeNickName
    $api->post('/changeName','App\Http\Controllers\Api\V1\ToolController@changeName');
    //changePic
    $api->post('/upload','App\Http\Controllers\Api\V1\ToolController@upload');
    //我的
    $api->post('/auth','App\Http\Controllers\Api\V1\LoginController@auth');
    //修改密码
    $api->post('/changePwd','App\Http\Controllers\Api\V1\ToolController@changePwd');

    //获取定制信息
    $api->post('/returnType','App\Http\Controllers\Api\V1\ToolController@returnType');



    
});
