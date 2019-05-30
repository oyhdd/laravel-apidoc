<?php

namespace Oyhdd\Document\Models;

use Illuminate\Database\Eloquent\Model;


class ApiDoc extends Model
{
    protected $table = "api_doc";

    protected $fillable = [
        'title',
        'url',
        'method',
        'author',
        'uses',
        'request_example',
        'response_example',
        'response_desc',
        'regression_test',
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
    // 回归测试状态
    const STATUS_REG_TEST_NO  = 0;
    const STATUS_REG_TEST_YES = 1;
    public static $label_regression_test = [
        self::STATUS_REG_TEST_NO => '否',
        self::STATUS_REG_TEST_YES   => '是',
    ];

    /**
     * @name   保存apidoc
     * @param  array      $params [title,url,method,author,uses,request_example,response_example,response_desc,status]
     * @return bool
     */
    public static function saveApidoc($params)
    {
        $model = ApiDoc::where(['url' => $params['url']])->first();
        if (empty($model)) {
            $model = new ApiDoc();
            $params['status'] = ApiDoc::STATUS_EFFECTIVE;
        }
        $model->fill($params);
        return $model->save();
    }

    /**
     * @name   根据url获取数据
     * @param  string      $url
     * @return bool
     */
    public static function getByUrl($url)
    {
        return ApiDoc::where(['url' => $url, 'status' => ApiDoc::STATUS_EFFECTIVE])->first();
    }

    /**
     * @name   获取所有数据
     * @param  string      $url
     * @return array
     */
    public static function getAll($url = '')
    {
        $where = [];
        if (!empty($url)) {
            $where['url'] = $url;
        }
        $where['status'] = ApiDoc::STATUS_EFFECTIVE;
        return ApiDoc::where($where)->get()->toArray();
    }
}
