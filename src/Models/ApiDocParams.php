<?php

namespace Oyhdd\Document\Models;

use Illuminate\Database\Eloquent\Model;


class ApiDocParams extends Model
{
    protected $table = "api_doc_params";

    protected $fillable = [
        'api_id',
        'test_title',
        'header',
        'body',
        'response_md5',
        'status',
    ];

    const CREATED_AT = "create_time";
    const UPDATED_AT = null;

    protected $dateFormat = 'Y-m-d H:i:s';

    protected $dates = ['create_time'];

    // 状态
    const STATUS_INEFFECTIVE = 0;
    const STATUS_EFFECTIVE   = 1;
    public static $label_status = [
        self::STATUS_INEFFECTIVE => '无效',
        self::STATUS_EFFECTIVE   => '有效',
    ];

    /**
     * @name   保存api接口测试用例
     * @param  array      $params [api_id,test_title,header,body,response_md5]
     * @return bool
     */
    public static function saveApiParams($params)
    {
        $model = ApiDocParams::where(['api_id' => $params['api_id'], 'test_title' => $params['test_title']])->first();
        if (empty($model)) {
            $model = new ApiDocParams();
        }
        $params['status'] = ApiDocParams::STATUS_EFFECTIVE;
        $model->fill($params);
        return $model->save();
    }

    /**
     * @name   获取api接口测试用例
     * @param  int      $api_id
     * @return array
     */
    public static function getApiParams($api_id)
    {
        $list = ApiDocParams::select(['id', 'test_title', 'header', 'body', 'response_md5'])
            ->where(['api_id' => $api_id, 'status' => ApiDocParams::STATUS_EFFECTIVE])
            ->get()
            ->toArray();

        array_walk($list, function(&$val, $key) {
            $val['header'] = @json_decode($val['header'], true);
            $val['body'] = @json_decode($val['body'], true);
        });

        return $list;
    }

    /**
     * @name   删除api接口测试用例
     * @param  int      $api_id
     * @return array
     */
    public static function deleteApiParams($id)
    {
        return ApiDocParams::where(['id' => $id])->update(['status' => ApiDocParams::STATUS_INEFFECTIVE]);
    }

    public static function getByApiIds($apiDocIds)
    {
        return ApiDocParams::whereIn('api_id', $apiDocIds)
            ->where(['status' => ApiDocParams::STATUS_EFFECTIVE])
            ->get()
            ->groupBy('api_id')
            ->toArray();
    }
}
