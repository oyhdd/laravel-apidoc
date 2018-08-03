# apidoc
自动生成在线测试接口和文档

## 安装

composer安装wealedger/apidoc

```java
composer require wealedger/apidoc
```

发布资源文件

```java
php artisan vendor:publish --provider="Wealedger\Document\DocumentServiceProvider"
```

在浏览器打开http://localhost/document/api 即可访问

## 使用方法
1.控制器接口中按如下格式进行函数注释：

```java
/**
 * @name    测试
 * @uses    测试
 * @author  wangmeng
 * @date    2017-08-07
 * @header  string|true           $token         令牌
 * @param   string|false          $name          姓名
 * @param   string|false          $mobile        手机号
 * @param   string|false          $id_card       身份证
 * @return  array
 */
public function test(Request $request)
{
}
```

2.在routes/api.php中按如下格式配置路由：

```java
//公共接口
Route::name('公共接口.')->group(function ($router) {
    $router->get('test/test', 'Back\TestController@test');
});

//后端接口
Route::name('后端接口.')->namespace('Back')->prefix('back')->group(function ($router) {

    $router->post('test/test5', 'TestController@test5');

    //功能组1
    $router->name('功能组1.')->group(function($router) {
        $router->get('test/test1', 'TestController@test1');
        $router->post('test/test4', 'TestController@test4');

        //功能组1.1
        $router->name('功能组1-1.')->group(function($router) {
            $router->get('test/test6', 'TestController@test6');
            $router->get('test/test7', 'TestController@test7');
        });
    });

    //功能组2
    $router->name('功能组2.')->group(function($router) {
        $router->post('test/test2', 'TestController@test2');
        $router->get('test/test3', 'TestController@test3');
    });
});

//前端接口
Route::name('前端接口.')->namespace('Front')->prefix('front')->group(function ($router) {

    $router->get('test/test1', 'TestController@test1');

    //功能组1
    $router->name('功能组1.')->group(function($router) {
        $router->get('test/test2', 'TestController@test2');
    });

    $router->get('test/test3', 'TestController@test3');

});
```
3.config/document.php中可配置相关功能

```java
<?php

return [

    // 路由分组的分隔符
    'delimiter' => '.',

    // 不需展示的接口路由
    'hiddenMethods' => [
        // 'App\Http\Controllers\Back\TestController' => [ //Controller::class
            // 'test',//该Controller下的action
        // ],
    ],

    // 是否显示未配置路由的接口
    'showUndefinedRouter' => false,

];
```

4.返回示例与返回值说明待开发