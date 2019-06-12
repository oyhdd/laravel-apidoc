<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiDocParamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_doc_params', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('api_id')->comment('接口id');
            $table->string('test_title', 64)->comment('测试用例标题');
            $table->text('header')->nullable()->comment('header参数，json格式');
            $table->text('body')->nullable()->comment('body参数，json格式');
            $table->string('response_md5', 32)->nullable()->comment('返回值的md5');
            $table->tinyInteger('status')->default(1)->comment('状态, 0 : 无效 1 : 有效');
            $table->timestamp('create_time')->default(\DB::raw('CURRENT_TIMESTAMP'))->comment('创建时间');

            $table->index('api_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_doc_params');
    }
}
