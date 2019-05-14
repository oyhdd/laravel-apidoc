<?php
namespace Oyhdd\Document\Controllers;

use Cache;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Oyhdd\Document\Models\ActionModel;
use Oyhdd\Document\Models\ApiDoc;
use Oyhdd\Document\Models\ApiDocParams;

/**
 * Document controller
 */
class ApiController extends Controller
{
    public $delimiter;//路由分组的分隔符

    public $hiddenMethods; //不想展示出来的接口可以在此定义
    public $showUndefinedRouter;//是否显示未配置路由的接口

    protected $undefinedRouterList = [];//未配置的路由
    protected $routerList = [];//已配置的路由
    protected $host;//域名
    protected $action;//当前请求的接口
    protected $actionModel;//当前请求的接口详情类
    protected $debugRoute;//当前请求的路由(不含host)
    protected $debugUrl;//当前请求的完整路由(包含host)

    function __construct()
    {
        $this->delimiter = !is_null(Config::get('document.delimiter')) ? Config::get('document.delimiter') : '.';
        $this->hiddenMethods = !is_null(Config::get('document.hiddenMethods')) ? Config::get('document.hiddenMethods') : [];
        $this->showUndefinedRouter = !is_null(Config::get('document.showUndefinedRouter')) ? Config::get('document.showUndefinedRouter') : false;
    }

    /**
     * 文档首页
     */
    public function index(Request $request)
    {
        //获取host和action
        $this->host = str_replace($request->path(), '', $request->url());
        $this->action = $request->get('action');

        //获取路由分组
        $routeList = $this->getRouteGroupList();

        //获取菜单数据
        $routeList = $this->getNavItems($routeList);

        //格式化菜单
        $navItems = [];
        foreach ($routeList as $key => $value) {
            if (!empty($value)) {
                $navItems = $this->arrayMerge($navItems, $value);
            }
        }

        if ($this->showUndefinedRouter && !empty($this->undefinedRouterList)) {
            $navItems[] = $this->handleUndefinedRouter();
        }

        return view('document::api', [
            'navItems' => $navItems,
            'model' => $this->actionModel,
            'debugRoute' => $this->debugRoute,
            'debugUrl' => $this->debugUrl,
        ]);
    }


    /**
     * 上传接口请求|返回示例和返回值说明
     */
    public function uploadExample(Request $request)
    {
        $params = $request->all();
        if (!empty($params['type'])) {
            $params[$params['type']] = empty($params['value']) ? "" : $params['value'];
        }
        ApiDoc::saveApidoc($params);
        return [];
    }


    /**
     * 上传接口请求参数,用于回归测试
     */
    public function uploadApiParams(Request $request)
    {
        $ret = [
            'code' => 0,
            'message' => '保存成功！'
        ];

        $title = $request->get('title');
        $header = $request->get('header');
        $body = $request->get('body');
        $response = $request->get('response');
        $url = $request->get('url');
        $method = $request->get('method');
        if (empty($title)) {
            $ret['code'] = -1;
            $ret['message'] = '请填写测试用例标题';
            return $ret;
        }

        $this->uploadExample($request);
        $apidocModel = ApiDoc::getByUrl($url);
        $params = compact('title', 'header', 'body', 'response');
        $params['api_id'] = $apidocModel->id;

        if (!ApiDocParams::saveApiParams($params)) {
            $ret['code'] = -1;
            $ret['message'] = '保存失败！';
        }
        return $ret;
    }

    public function getApiParams(Request $request)
    {
        $ret = [
            'code' => 0,
            'message' => '成功！',
            'data' => [],
        ];

        $url = $request->get('url');
        $model = ApiDoc::getByUrl($url);
        if (empty($model)) {
            $ret['code'] = -1;
            $ret['message'] = "未查询到测试用例";
            return $ret;
        }
        $api_id = $model->id;
        $list = ApiDocParams::getApiParams($api_id);
        if (empty($list)) {
            $ret['code'] = -1;
            $ret['message'] = "未查询到测试用例";
            return $ret;
        }
        $ret['data'] = $list;
        return $ret;
    }

    public function deleteApiParams(Request $request)
    {
        $ret = [
            'code' => 0,
            'message' => '删除成功！',
            'data' => [],
        ];

        $id = $request->get('id');
        if (!ApiDocParams::deleteApiParams($id)) {
            $ret['code'] = -1;
            $ret['message'] = "删除失败！";
        }
        return $ret;
    }

    /**
     * 获取接口请求返回示例
     */
    public function getExample(Request $request)
    {
        $url = $request->get('url');

        $model = ApiDoc::getByUrl($url);
        if (empty($model)) {
            return ['request_example' => '', 'response_example' => '', 'response_desc' => ''];
        }

        return [
            'request_example' => $model->request_example,
            'response_example' => $model->response_example,
            'response_desc' => $model->response_desc,
        ];
    }

    //获取路由分组配置信息
    public function getRouteGroupList()
    {
        //获取所有路由
        $routes = app('router')->getRoutes();

        $routeList = $retData = [];
        $length = strlen($this->delimiter);

        foreach ($routes as $item) {

            $as = empty($item->action['as']) ? '' : $item->action['as'];
            $controller = empty($item->action['controller']) ? '' : $item->action['controller'];
            if (!empty($controller)) {
                //自动添加group中name()
                if (substr($as, -$length) === $this->delimiter || empty($as)) {
                    $as .= $item->uri;
                }

                $routeList[$as] = [
                    'uri' => $item->uri,
                    'action' => $controller,
                    'methods' => $item->methods(),
                ];

                $this->routerList[$controller] = $controller;
            }
        }

        foreach ($routeList as $key => $value) {
            $modules = explode($this->delimiter, $key);
            $retData[] = $this->deepFormat($modules, $value);
        }

        return $retData;
    }

    //递归处理路由格式
    public function deepFormat($modules, $value)
    {
        $ret = [];
        if (count($modules) <= 1) {
            $ret['name'] = '';
            $ret['href'] = empty($value['uri']) ? '' : $value['uri'];
            $ret['action'] = empty($value['action']) ? '' : $value['action'];
            $ret['methods'] = empty($value['methods']) ? '' : $value['methods'];
        } else {
            $ret['name'] = current($modules);
            $ret['href'] = '';
            $ret['action'] = '';
            array_shift($modules);
            $ret['subMenus'][] = $this->deepFormat($modules, $value);
        }

        return $ret;
    }

    //递归获取路由参数
    public function getNavItems($routeList)
    {
        if (empty($routeList)) {
            return [];
        }

        foreach ($routeList as $key => $route) {
            if (empty($route['subMenus'])) {
                $ret = $this->getNavParams($route);//获取路由参数
                if (!empty($ret)) {
                    $routeList[$key] = $ret;
                } else {
                    unset($routeList[$key]);
                }
            } else {
                $ret = $this->getNavItems($route['subMenus']);//递归获取路由参数
                if (!empty($ret)) {
                    $routeList[$key]['subMenus'] = $ret;
                } else {
                    unset($routeList[$key]);
                }
            }
        }

        return $routeList;
    }

    //递归合并数据
    public function arrayMerge($navItems, $format)
    {
        if (empty($navItems)) {
            return [$format];
        }

        $flag = true;
        foreach ($navItems as $key => $navItem) {
            if (isset($navItem['name']) && isset($format['name']) && $navItem['name'] == $format['name']) {
                $flag = false;
                if (!isset($navItems[$key]['subMenus']) || !isset($format['subMenus'])) {
                    continue;
                }

                $navItems[$key]['subMenus'] = $this->arrayMerge($navItems[$key]['subMenus'], $format['subMenus'][0]);
            }
        }
        if ($flag) {
            $navItems[] = $format;
        }

        return $navItems;
    }

    //从数组中返回函数请求方式
    public function getMethod($methods)
    {
        foreach ($methods as $value) {
            if (strtoupper($value) === 'GET') {
                return 'GET';
            } elseif (strtoupper($value) === 'POST') {
                return 'POST';
            } else {
                return strtoupper($value);
            }
        }
    }

    //获取菜单参数
    public function getNavParams($route = [])
    {
        list($class, $actionName) = explode('@', $route['action']);

        $subMenu = [];
        try {
            $rf = new \ReflectionClass($class); //构建controller
            $methods = $rf->getMethods(\ReflectionMethod::IS_PUBLIC); //获取controller中的所有publick函数

            foreach ($methods as $method) {
                //过滤自定义隐藏的函数
                if (isset($this->hiddenMethods[$method->class])) {
                    if (in_array($method->name, $this->hiddenMethods[$method->class]) || in_array('*', $this->hiddenMethods[$method->class])) {
                        continue;
                    }
                }

                //过滤系统函数
                if (strpos($method->name, '_') === 0) {
                    continue;
                }

                //函数相关信息类
                $actionModel = new ActionModel($method);

                //过滤函数名等于函数注释中的name
                if($actionModel->title() == $method->name) {
                    continue;
                }

                //未配置路由的函数单独处理
                if (!isset($this->routerList[$class.'@'.$method->name])) {
                    $this->undefinedRouterList[$class.'@'.$method->name] = $class.'@'.$method->name;
                }

                if($actionName != $method->name) {
                    continue;
                }

                $active = false;
                if (!empty($this->action)) {
                    list($cur_class, $cur_action) = explode($this->delimiter, $this->action);
                    if ($class == $cur_class && $cur_action == $method->name) {
                        $this->actionModel = $actionModel;
                        try {
                            $this->debugUrl = empty($route['href']) ? '' : $this->host . $route['href'];
                            $this->debugRoute = $route['href'];
                        } catch (\Exception $e) {
                            $this->debugUrl = '';
                            $this->debugRoute = '';
                        }
                        $active = true;

                        $cur_method = $this->getMethod($route['methods']);
                        if (!empty($cur_method)) {
                            $actionModel->setMethod($cur_method);
                        }

                    }
                }

                $subMenu = [
                    'name' => $actionModel->title(),
                    'uri' => empty($route['href']) ? '' : $route['href'],
                    'href' => '/document/api?action='.$class.$this->delimiter.$method->name,
                    'active' => $active,
                ];
                break;
            }
        } catch (\Exception $e) {
        }

        return $subMenu;
    }

    //处理未定义的路由
    public function handleUndefinedRouter()
    {
        if (empty($this->undefinedRouterList)) {
            return [];
        }
        $routeList = $route = [
            'name' => '',
            'href' => '',
        ];
        $routeList['name'] = '未配置的路由';
        $route['methods'] = [];
        foreach ($this->undefinedRouterList as $action) {
            $route['action'] = $action;
            $routeList['subMenus'][] = $this->getNavParams($route);
        }
        return $routeList;
    }

}