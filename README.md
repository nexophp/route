# Router
 
路由

## 安装

~~~
composer require nexophp/route
~~~

路由加载优先级  `app` > `modules`

## rewrite

~~~
location / {
  if (!-e $request_filename){
    rewrite ^(.*)$ /index.php last;
  }
}
~~~

## 目录

~~~
app/user/controller/SiteController.php 
~~~

演示代码

~~~
<?php 
namespace app\user\controller;

class SiteController{ 

	public function actionIndex(){
		return 'site';
	} 

}
~~~

需要返回jsons数据可用

~~~
return ['data'=>''];
~~~


## 开始
~~~
Route::get('/',function(){
	echo 1;
});   
Route::all('user','core/user/controller/site@index');   
return Route::do(function(){
	//路由存在
	 
},function(){
	//路由不存在
	echo '路由不存在';
	//取具体错误信息
	pr(Route::$err);
}); 
~~~

## 取当前的模块、控制器、动作
~~~ 
protected $package;
protected $module;
protected $controller;
protected $action;

$route = Route::get_action();
$this->package = $route['package'];
$this->module = $route['module'];
$this->controller = $route['controller'];
$this->action = $route['action'];
$model = $this->model;
~~~

## 生成URL

~~~
Router::url($url,$par = []);
~~~
 

## 更多规则

~~~ 
Route::get('/',function(){
	echo 1;
});
Route::all('login/<name:\w+>','app\login\$name@index','login');  
//aa 为url地址，home为生成url链接所用的名称 	
Route::get('aa',"app\controller\index@index",'home'); 
Route::get('post/<id:\d+>/<g:\d+>',"app\controller\index@test",'post');
Route::get('payadmin','app\pay\admin@list');
Route::get('payadmin/<page:\d+>','app\pay\admin@list');
Route::domain('user2.api.com',function(){
	Route::get('/',function(){
		echo 111;		
	});
	Route::get('test',function(){
		echo 'test';		
	});
});
Route::get('post/<id:\d+>|post',function(){    
},'@post');
Route::get('post/<id:\d+>|post',function(){    
},'@post|$po');
~~~

## composer 

~~~
"autoload": {
    "psr-4": {
        "core\\": "core",
        "app\\": "app",
        "modules\\": "modules"
    }
}
~~~
