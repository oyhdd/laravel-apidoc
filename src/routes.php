<?php

// 在线调试接口
Route::get('document/api', 'Oyhdd\Document\Controllers\ApiController@index');
Route::post('document/upload-example', 'Oyhdd\Document\Controllers\ApiController@uploadExample');
Route::get('document/get-example', 'Oyhdd\Document\Controllers\ApiController@getExample');