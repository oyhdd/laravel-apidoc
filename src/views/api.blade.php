<!doctype html>
<html >
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script type="text/javascript" src="{{ URL::asset('/vendor/document/js/jquery.min.js') }}"></script>
        <script type="text/javascript" src="{{ URL::asset('/vendor/document/js/jsbeautify.js') }}"></script>
        <script type="text/javascript" src="{{ URL::asset('/vendor/document/js/checkutil.js') }}?v=201809121443"></script>
        <script type="text/javascript" src="{{ URL::asset('/vendor/document/js/treeMenu.js')}}?v=201808271719"></script>
        <script type="text/javascript" src="{{ URL::asset('/vendor/document/js/bootstrap.min.js') }}"></script>
        <link rel="stylesheet" href="{{ URL::asset('/vendor/document/css/treeMenu.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('/vendor/document/css/bootstrap.css') }}">
        <title>在线测试API文档</title>
        <style>
            .TreeMenuList>div {margin: 5px;border: ridge;}
            .TreeMenuList {display: -webkit-box; display: -moz-box; display: -ms-flexbox; display: -webkit-flex; display: flex; -webkit-box-orient: horizontal; -webkit-flex-direction: row;-moz-flex-direction: row;-ms-flex-direction: row;-o-flex-direction: row;flex-direction: row;}

            ul.tabs {margin: 0;padding: 0;float: left;list-style: none;height: 32px;border-bottom: 1px solid #999;border-left: 1px solid #999;width: 100%;}
            ul.tabs li {float: left;margin: 0;padding: 0;height: 31px;line-height: 31px;border: 1px solid #999;border-left: none;margin-bottom: -1px;background: #e0e0e0;overflow: hidden;position: relative;}
            ul.tabs li a {text-decoration: none;color: #000;display: block;font-size: 1.2em;padding: 0 20px;border: 1px solid #fff;outline: none;}
            html ul.tabs li.active, html ul.tabs li.active a:hover  {background: #fff;border-bottom: 1px solid #fff;}
            .tab_container {border: 1px solid #999;border-top: none;clear: both;float: left; width: 100%;background: #fff;-moz-border-radius-bottomright: 5px;-khtml-border-radius-bottomright: 5px;-webkit-border-bottom-right-radius: 5px;-moz-border-radius-bottomleft: 5px;-khtml-border-radius-bottomleft: 5px;-webkit-border-bottom-left-radius: 5px;}

            table {width: 100%;table-layout: fixed;}
            table tbody tr:nth-child(2n){background:#ffffff}
            table tbody tr:nth-child(2n+1){background:#f9f9f9}
            table thead tr th{text-align: left;border: 1px solid #dddddd;background-color: #ddd;}
            table td{text-align: left;border: 1px solid #dddddd;vertical-align: inherit;}
        </style>

    </head>
    <body>
        <div>

            <div class="col-md-3 col-sm-12"">
                <div class="TreeMenuList">
                    <div id="TreeMenu"></div>
                </div>
            </div>

            <div class="col-md-9 col-sm-12" role="main">

                <?php if ($model): ?>
                    <h1>
                        <?php echo $model->title(); ?>
                        <?php if ($model->author()): ?>
                            <span style="font-size:16px;margin-left:20px;">— <?php echo $model->author(); ?></span>
                        <?php endif; ?>
                    </h1>
                    <pre> URL地址：<?php echo !empty($debugRoute) ? '{host}/'.$debugRoute : ''; ?><br/> 请求方式：<?php echo $model->method(); ?><?php echo $model->uses() ? "<br/> <b>用途：{$model->uses()}</b>" : ''; ?></pre>
                    <ul class="tabs">
                        <li class="active"><a href="#tab1">请求与返回</a></li>
                        <li><a href="#tab2">在线测试</a></li>
                    </ul>
                    <div class="tab_container">
                        <div id="tab1" class="tab_content" style="display: block; padding: 2%;">
                            <?php
                                echo view('document::_table', [
                                    'title'  => '请求头',
                                    'values' => $model->header(),
                                ]);
                                echo view('document::_table', [
                                    'title'  => '请求参数',
                                    'values' => $model->params(),
                                ]);
                            ?>

                            <h3>请求示例</h3>
                            <textarea id="input_request" class="input-example" style="width:80%;overflow: scroll;"></textarea>
                            <div class="form-group">
                                <button name="request" type="button" class="btn btn-primary submit-example" data-loading-text="保存中..." autocomplete="off">保存</button>
                            </div>

                            <h3>返回示例</h3>
                            <textarea id="input_response" class="input-example" style="width:80%;; min-height:300px;max-height:600px;overflow: scroll;"></textarea>
                            <div class="form-group">
                                <button name="response" type="button" class="btn btn-primary submit-example" data-loading-text="保存中..." autocomplete="off">保存</button>
                            </div>

                            <h3>返回值说明</h3>
                            <textarea id="input_response_desc" class="input-example" style="width:80%; min-height:200px;max-height:600px;overflow: scroll;"></textarea>
                            <div class="form-group">
                                <button name="response_desc" type="button" class="btn btn-primary submit-example" data-loading-text="保存中..." autocomplete="off">保存</button>
                            </div>

                        </div>
                        <div id="tab2" class="tab_content" style="display: none; ">
                            <?php
                                echo view('document::_debug', [
                                    'syncHeader' => $syncHeader,
                                    'route'      => $debugRoute,
                                    'debugUrl'   => $debugUrl,
                                    'model'      => $model
                                ]);
                            ?>
                        </div>
                    </div>
                <?php else: ?>
                    <h1>接口注释规范</h1>
                    <p>@name表示接口名称, 不能为空</p>
                    <p>@uses表示接口简介/用途等，可空</p>
                    <p>@author表示接口作者/负责人，可空</p>
                    <p><?php echo '@method'; ?> 请求方式, GET | POST</p>
                    <p>@header表示请求头，可空可多个，后面分别跟：类型|必须、参数名、备注</p>
                    <p>@param表示请求body，可空可多个，后面分别跟：类型|必须、参数名、备注</p>
                    <pre>/**<br/>* @name    获取注册验证码<br/>* @uses    注册步骤一：手机号获取验证码<br/>* @author   wangmeng<br/>* <?php echo '@method'; ?>      POST<br/>* @header   string|true    $token     令牌校验<br/>* @param   &nbsp;string|true    $phone     手机号<br/>*/<br/>public function getSmsCode($phone)<br/>{<br/>}<br/></pre>
                    <h1>接口请求参数示例</h1>
                    <?php
                        echo view('document::_table', [
                            'title'  => '请求头',
                            'values' => [['name' => 'token', 'is_necessary' => 'true', 'type' => 'string', 'desc' => '令牌校验']]
                        ]);
                        echo view('document::_table', [
                            'title'  => '请求参数',
                            'values' => [['name' => 'phone', 'is_necessary' => 'true', 'type' => 'string', 'desc' => '手机号']]
                        ]);
                    ?>
                    <h1>错误码规范</h1>
                    <table class="table table-condensed table-bordered table-striped table-hover request-table">
                        <thead>
                            <tr>
                                <th style="width: 160px;">错误码</th>
                                <th style="width: 160px;">注释</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>0</td>
                                <td>请求成功</td>
                            </tr>
                            <tr>
                                <td>-1</td>
                                <td>请求失败</td>
                            </tr>
                        </tbody>
                    </table>
                <?php endif; ?>

            </div>

        </div>

    </body>
    <script type="text/javascript">

        var debugRoute = '<?php echo $debugRoute; ?>';

        $(document).ready(function() {

            //左侧菜单栏
            var menuConfig = {
                treeMenuId: "#TreeMenu",
                superLevel: 0,
                multiple: true,
            };
            var navItems = <?php echo json_encode($navItems);?>;
            treeMenu.init(navItems, menuConfig);

            //tab栏
            $(".tab_content").hide();
            $("ul.tabs li:first").addClass("active").show();
            $(".tab_content:first").show();

            $("ul.tabs li").click(function() {
                $("ul.tabs li").removeClass("active");
                $(this).addClass("active");
                $(".tab_content").hide();
                var activeTab = $(this).find("a").attr("href");
                $(activeTab).fadeIn();
                return false;
            });

            renderExample('request');
            renderExample('response');
            renderExample('response_desc');

            //保存请求响应示例
            $('.submit-example').click(function(){
                var type = $(this).attr('name');

                var btn = $(this).button('loading');
                if (debugRoute == '') {
                    alert("未配置路由");
                    btn.button('reset');
                    return;
                }
                var desc = $('#input_'+type).val();
                if (desc == '') {
                    alert("内容不能为空");
                    btn.button('reset');
                    return;
                }

                $.ajax({
                    url: '/document/upload-example',
                    type: 'POST',
                    data: {
                        type: type,
                        action: debugRoute,
                        desc: desc,
                    },
                    success: function(retData) {
                        btn.button('reset');
                    },
                    error: function(retData) {
                        btn.button('reset');
                        alert('保存失败');
                    }
                });
            });
        });

        //加载请求返回说明示例
        function renderExample(type) {
            $.ajax({
                url: '/document/get-example',
                type: 'GET',
                data: {
                    type: type,
                    action: debugRoute
                },
                success: function(retData) {
                    $('#input_'+type).html(retData.data);
                    $('#input_'+type).autoTextarea({
                        maxHeight:800,
                        minHeight:50
                    });
                },
                error: function(retData) {
                }
            });
        }

</script>
</html>
