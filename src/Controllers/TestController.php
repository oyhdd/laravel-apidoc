<?php

namespace Oyhdd\Document\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TestController extends Controller
{

    /**
     * @name   测试
     * @uses   测试接口
     * @author wangmeng
     * @date   2018-10-19
     * @header string|true               $token              header头
     * @param  string|true               $str                字符串
     * @param  int|true                  $number             数字
     * @param  array|true                $arr                数组
     * @return array
     */
    public function test(Request $request)
    {
        $ret = [
            'code' => 0,
            'msg' => 'success',
            'data' => [
                'header' => ['token' => $request->header('token')],
                'body' => $request->all(),
            ],
        ];

        return $ret;
    }


}