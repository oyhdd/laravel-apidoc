<?php

// 在线调试接口
Route::get('document/api', 'Wealedger\Document\Controllers\ApiController@index');
Route::post('document/upload-example', 'Wealedger\Document\Controllers\ApiController@uploadExample');
Route::get('document/get-example', 'Wealedger\Document\Controllers\ApiController@getExample');