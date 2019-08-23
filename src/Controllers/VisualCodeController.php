<?php

namespace Oyhdd\Document\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;

class VisualCodeController extends Controller
{
    /**
     *    可视化代码
     * @uses   测试接口
     * @author wangmeng
     * @date   2018-10-19
     * @param string|true               $action             接口地址
     * @return array
     */
    public function visualCode(Request $request)
    {
        $ret = [
            'code' => 0,
            'message' => '分析成功！',
            'data' => [],
        ];

        $delimiter = !is_null(Config::get('document.delimiter')) ? Config::get('document.delimiter') : '.';
        $route = $request->get('route', '');
        if (empty($route)) {
            $ret['code'] = -1;
            $ret['message'] = "当前接口不可分析！";
            return $ret;
        }
        list($class, $actionName) = explode($delimiter, $route);
        $rf = new \ReflectionClass($class); //构建controller
        dd($rf->getParentClass());
        $methods = $rf->getMethods(\ReflectionMethod::IS_PUBLIC); //获取controller中的所有publick函数
        $apiInfo = [];
        foreach ($methods as $key => $value) {
        }
    }


}