<?php

namespace Oyhdd\Document\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Oyhdd\Document\Models\ApiDoc;
use Oyhdd\Document\Models\ApiDocParams;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use GuzzleHttp\Exception\ClientException;

class UnitTestController extends Controller
{

    /**
     * 上传接口请求|返回示例和返回值说明
     */
    public function uploadExample(Request $request)
    {
        $params = $request->all();

        if (!empty($params['type'])) {
            $params[$params['type']] = empty($params['value']) ? "" : $params['value'];
        }
        if (isset($params['regression_test'])) {
            $params['regression_test'] = intval($params['regression_test']);
        }
        ApiDoc::saveApidoc($params);
        return [];
    }

    /**
     * 获取接口请求返回示例
     */
    public function getExample(Request $request)
    {
        $url = $request->get('url');

        $model = ApiDoc::getByUrl($url);
        if (empty($model)) {
            return ['request_example' => '', 'response_example' => '', 'response_desc' => '', 'regression_test' => 0];
        }

        return [
            'request_example' => $model->request_example,
            'response_example' => $model->response_example,
            'response_desc' => $model->response_desc,
            'regression_test' => $model->regression_test,
        ];
    }

    /**
     * 上传接口测试用例
     */
    public function uploadApiParams(Request $request)
    {
        $ret = [
            'code' => 0,
            'message' => '保存成功！'
        ];

        $test_title = $request->get('test_title');
        $header = $request->get('header');
        $body = $request->get('body');
        $response_md5 = md5($request->get('response'));
        $url = $request->get('url');
        $method = $request->get('method');
        if (empty($test_title)) {
            $ret['code'] = -1;
            $ret['message'] = '请填写测试用例标题!';
            return $ret;
        }
        $this->uploadExample($request);
        $apidocModel = ApiDoc::getByUrl($url);
        $params = compact('test_title', 'header', 'body', 'response_md5');
        $params['api_id'] = $apidocModel->id;

        if (!ApiDocParams::saveApiParams($params)) {
            $ret['code'] = -1;
            $ret['message'] = '保存失败！';
        }
        return $ret;
    }

    /**
     * 获取接口测试用例
     */
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

    /**
     * 删除接口测试用例
     */
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
     * 回归测试
     */
    public function regressionTest(Request $request)
    {
        $ret = [
            'code' => -1,
            'message' => '回归测试失败，请重试！',
            'data' => [],
        ];

        $url = $request->get('url');
        $apiDocModels = ApiDoc::getAll($url);
        $apiDocModels = array_column($apiDocModels, null, 'id');
        $apiDocIds = array_keys($apiDocModels);
        $apiParamsModels = ApiDocParams::getByApiIds($apiDocIds);

        foreach ($apiDocModels as $apiId => $apiDoc) {
            if ($apiDoc['regression_test'] != ApiDoc::STATUS_REG_TEST_YES) {
                unset($apiDocModels[$apiId]);
            } else {
                $apiDocModels[$apiId]['api_params'] = empty($apiParamsModels[$apiId]) ? '' : $apiParamsModels[$apiId];
                $apiDocs[$apiId] = $apiDoc;
            }
        }
        $ret['data'] = $this->sendRequest($apiDocModels);
        if (!empty($ret['data'])) {
            $ret['code'] = 0;
            $ret['message'] = '成功';
        }

        return $ret;
    }

    /**
     * 发送并发请求
     * @author wangmeng
     * @date   2019-05-15
     * @param  array        $apiDocs            apiDoc
     * @param  integer      $timeOut            超时限制60s
     * @return false|array
     */
    public static function sendRequest($apiDocs = [], $timeOut = 60)
    {
        $requestData = [];
        $total_api = $total_unit = $match_count = $not_match_count = $success_count = $fail_count = 0;
        $client = new Client(['timeout' => $timeOut]);
        foreach ($apiDocs as $apiId => $apiDoc) {
            if (in_array($apiDoc['method'], ["GET", "get"])) {
                foreach ($apiDoc['api_params'] as $key => $apiParams) {
                    $body = json_decode($apiParams['body'], true);
                    $header = json_decode($apiParams['header'], true);
                    foreach ($body as $key1 => $value) {
                        if (is_array($value) && substr($key1, -2) == '[]') {
                            unset($body[$key1]);
                            $body[substr($key1, 0, -2)] = $value;
                        }
                    }
                    $requestData[] = [
                        'url' => $apiDoc['url']."?".http_build_query($body),
                        'headers' => $header,
                        'key' => $key,
                        'api_id' => $apiId,
                        'method' => "GET",
                    ];
                    $total_unit ++;
                }
            } elseif (in_array($apiDoc['method'], ["POST", "post"])) {
                foreach ($apiDoc['api_params'] as $key => $apiParams) {
                    $body = json_decode($apiParams['body'], true);
                    $header = json_decode($apiParams['header'], true);

                    foreach ($body as $key1 => $value) {
                        if (is_array($value) && substr($key1, -2) == '[]') {
                            unset($body[$key1]);
                            $body[substr($key1, 0, -2)] = $value;
                        }
                    }

                    $requestData[] = [
                        'url' => $apiDoc['url'],
                        'form_params' => $body,
                        'headers' => $header,
                        'key' => $key,
                        'api_id' => $apiId,
                        'method' => "POST",
                    ];
                    $total_unit ++;
                }
            } else {
                return false;
            }
            $total_api ++;
        }

        $requests = function ($params) use ($client) {
            if (!empty($params)) {
                foreach ($params as $key => $param) {
                    if ($param['method'] == "GET") {
                        yield new GuzzleRequest('GET', $param['url'], $param['headers']);
                    } elseif ($param['method'] == "POST") {
                        yield function () use ($client, $param) {
                            return $client->requestAsync('post', $param['url'], [
                                'headers' => $param['headers'],
                                'json' => $param['form_params'],
                            ]);
                        };
                    }
                }
            }
        };
        $temp = [];
        $pool = new Pool($client, $requests($requestData), [
            'concurrency' => 20,
            'fulfilled' => function ($response, $index) use (&$temp){//成功
                $temp[$index] = [
                    'key' => $index,
                    'success' => true,
                    'response' => $response->getBody()->getContents(),
                ];
            },
            'rejected' => function ($reason, $index) use (&$temp) {//失败
                $str = $reason->getMessage();
                $str = str_replace("\\", '\\\\', $str);
                $str = str_replace("\r\n", '\n', $str);
                $temp[$index] = [
                    'key' => $index,
                    'success' => false,
                    'response' => json_encode([$str]),
                ];
            }
        ]);

        $promise = $pool->promise();
        $promise->wait();

        $ret = [];
        foreach ($temp as $key => $response) {
            $api_id = $requestData[$key]['api_id'];
            $index = $requestData[$key]['key'];
            $data = [
                'id' => $apiDocs[$api_id]['api_params'][$index]['id'],
                'success' => $response['success'],
                'test_title' => $apiDocs[$api_id]['api_params'][$index]['test_title'],
                'match' => false,
                'response' => json_decode($response['response'], true)
            ];

            if (!isset($ret['list'][$api_id]['not_match_count'])) {
                $ret['list'][$api_id]['not_match_count'] = 0;
            }
            if (!isset($ret['list'][$api_id]['fail_count'])) {
                $ret['list'][$api_id]['fail_count'] = 0;
            }
            if ($response['success']) {
                if (md5($response['response']) == $apiDocs[$api_id]['api_params'][$index]['response_md5']) {
                    $data['match'] = true;
                    $match_count ++;
                } else {
                    $ret['list'][$api_id]['not_match_count'] ++;
                    $not_match_count ++;
                }
                $success_count ++;
            } else {
                $ret['list'][$api_id]['fail_count'] ++;
                $fail_count ++;
            }

            $ret['list'][$api_id]['title'] = $apiDocs[$api_id]['title'];
            $ret['list'][$api_id]['url'] = $apiDocs[$api_id]['url'];
            $ret['list'][$api_id]['list'][] = $data;
        }

        $ret['total_api'] = $total_api;
        $ret['total_unit'] = $total_unit;
        $ret['match_count'] = $match_count;
        $ret['not_match_count'] = $not_match_count;
        $ret['success_count'] = $success_count;
        $ret['fail_count'] = $fail_count;

        return $ret;
    }
}