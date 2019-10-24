<?php

namespace Oyhdd\Document\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TestController extends Controller
{

    /**
     * @name   GET请求
     * @uses   GET请求
     * @author wangmeng
     * @date   2018-10-19
     * @header string|true               $token              header头
     * @param  string|true               $str                字符串
     * @param  int|true                  $number             数字
     * @param  array|false               $arr                数组
     * @return array
     */
    public function test1(Request $request)
    {
        $ret = [
            'code' => 0,
            'msg' => 'success',
            'data' => [
                'header' => [
                    'token' => $request->header('token')
                ],
                'body' => $request->all(),
            ],
        ];
        if (!empty($ret['data']['body']['number'])) {
            $ret['data']['body']['number'] = intval($ret['data']['body']['number']);
        }


        return $ret;
    }

    /**
     * @name   POST请求
     * @uses   POST请求
     * @author wangmeng
     * @date   2018-10-19
     * @header string|true               $token              header头
     * @param  string|true               $str                字符串
     * @param  int|true                  $number             数字
     * @param  array|false               $arr                数组
     * @return array
     */
    public function test2(Request $request)
    {
        $ret = [
            'code' => 0,
            'msg' => 'success',
            'data' => [
                'header' => [
                    'token' => $request->header('token')
                ],
                'body' => $request->all(),
            ],
        ];
        if (!empty($ret['data']['body']['number'])) {
            $ret['data']['body']['number'] = intval($ret['data']['body']['number']);
        }


        return $ret;
    }

    /**
     * @name   Restful GET请求
     * @uses   restful风格接口GET请求
     * @author wangmeng
     * @date   2018-10-19
     * @param  string|true               $id                id
     * @return array
     */
    public function test3($id)
    {
        $ret = [
            'code' => 0,
            'msg' => 'success',
            'data' => [
                'id' => $id,
            ],
        ];

        return $ret;
    }

    /**
     * @name   Restful POST请求
     * @uses   restful风格接口POST请求
     * @author wangmeng
     * @date   2018-10-19
     * @param  string|true               $id                id
     * @return array
     */
    public function test4($id)
    {
        $ret = [
            'code' => 0,
            'msg' => 'success',
            'data' => [
                'id' => $id,
            ],
        ];

        return $ret;
    }


}