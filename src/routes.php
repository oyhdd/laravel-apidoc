<?php

// 在线调试接口
Route::get('document/api', 'Oyhdd\Document\Controllers\ApiController@index');

//单元测试相关接口
Route::post('document/upload-example', 'Oyhdd\Document\Controllers\UnitTestController@uploadExample');
Route::get('document/get-example', 'Oyhdd\Document\Controllers\UnitTestController@getExample');
Route::post('document/upload-api-params', 'Oyhdd\Document\Controllers\UnitTestController@uploadApiParams');
Route::get('document/get-api-params', 'Oyhdd\Document\Controllers\UnitTestController@getApiParams');
Route::post('document/delete-api-params', 'Oyhdd\Document\Controllers\UnitTestController@deleteApiParams');
Route::post('/document/regression-test', 'Oyhdd\Document\Controllers\UnitTestController@regressionTest');
Route::post('/document/test', 'Oyhdd\Document\Controllers\UnitTestController@test');
Route::post('/document/visual-code', 'Oyhdd\Document\Controllers\VisualCodeController@visualCode');

//测试示例接口
Route::group([], function ($router) {
    // 接口组
    $router->name('测试.')->group(function ($router) {
        $router->get('test1', 'Oyhdd\Document\Controllers\TestController@test1');
        $router->post('test2', 'Oyhdd\Document\Controllers\TestController@test2');
        $router->get('test3/{id}', 'Oyhdd\Document\Controllers\TestController@test3');
        $router->post('test4/{id}', 'Oyhdd\Document\Controllers\TestController@test4');
    });
});