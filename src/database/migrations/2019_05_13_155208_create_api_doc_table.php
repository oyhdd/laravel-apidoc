<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiDocTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_doc', function (Blueprint $table) {
            $table->engine='InnoDB';
            $table->charset='utf8mb4';
            $table->collation='utf8mb4_unicode_ci';

            $table->increments('id');
            $table->string('title', 64)->comment('接口标题');
            $table->string('url', 255)->comment('接口地址');
            $table->string('method', 16)->comment('请求方法：GET，POST');
            $table->string('author', 32)->comment('作者');
            $table->string('uses', 255)->nullable()->comment('接口用途描述');
            $table->text('request_example')->nullable()->comment('请求示例');
            $table->text('response_example')->nullable()->comment('返回示例');
            $table->text('response_desc')->nullable()->comment('返回值说明');
            $table->tinyInteger('regression_test')->default(0)->comment('是否回归测试：0否 1是');
            $table->tinyInteger('regression_model')->default(1)->comment('回归模式：1完全匹配 2请求成功');
            $table->tinyInteger('status')->default(1)->comment('状态, 0 : 无效 1 : 有效');
            $table->timestamp('create_time')->default(\DB::raw('CURRENT_TIMESTAMP'))->comment('创建时间');

            // $table->index('url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_doc');
    }
}
