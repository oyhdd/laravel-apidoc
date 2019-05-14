<?php

// 在线调试接口
Route::get('document/api', 'Oyhdd\Document\Controllers\ApiController@index');
Route::post('document/upload-example', 'Oyhdd\Document\Controllers\ApiController@uploadExample');
Route::get('document/get-example', 'Oyhdd\Document\Controllers\ApiController@getExample');
Route::post('document/upload-api-params', 'Oyhdd\Document\Controllers\ApiController@uploadApiParams');
Route::get('document/get-api-params', 'Oyhdd\Document\Controllers\ApiController@getApiParams');
Route::post('document/delete-api-params', 'Oyhdd\Document\Controllers\ApiController@deleteApiParams');

//测试示例接口
Route::group([], function ($router) {
    // 接口组
    $router->name('测试.')->group(function ($router) {
        $router->post('test', 'Oyhdd\Document\Controllers\TestController@test');
    });
});