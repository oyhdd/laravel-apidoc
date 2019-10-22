<?php

return [

    //外部接口
    'otherApi'  => [
        // 项目名 => 获取该项目接口列表的接口
        // 'rpc' => [
        //     'url' => 'http://127.0.0.1:18306/apidoc/getApiDoc', //获取该项目接口列表的接口
        //     'token' => 'CVf3d5FYAG7786evdgsvF9ds5', //访问令牌
        // ],
    ],

    // 路由分组的分隔符
    'delimiter' => '.',

    // 不需展示的接口路由
    'hiddenMethods' => [
        // Controller::class
        'Oyhdd\Document\Controllers\TestController' => [
            // 'test',//该Controller下的此action
            // '*',//该Controller下的所有action
        ],
    ],

    // 是否显示未配置路由的接口
    'showUndefinedRouter' => false,
];