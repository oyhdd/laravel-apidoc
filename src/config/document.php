<?php

return [

    // 路由分组的分隔符
    'delimiter' => '.',

    // 不需展示的接口路由
    'hiddenMethods' => [
        // Controller::class
        'App\Http\Controllers\TestController' => [
            'test',//该Controller下的此action
            '*',//该Controller下的所有action
        ],
    ],

    // 是否显示未配置路由的接口
    'showUndefinedRouter' => false,

    // 各接口的header都自动同步为最新的header
    'syncHeader' => true,

];