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
        <script type="text/javascript" src="{{ URL::asset('/vendor/document/js/bootstrap-select.js') }}"></script>
        <link rel="stylesheet" href="{{ URL::asset('/vendor/document/css/treeMenu.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('/vendor/document/css/bootstrap.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('/vendor/document/css/bootstrap-select.css') }}">


        <title>在线测试API文档</title>
        <style>
            body{margin: 10px}
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
            .submit-example{background: linear-gradient(to right, #2091cf, #0758f0);}
            #regression_testing_detail .panel{margin-bottom: 5px;}
            .panel-title>.label{display: inline-block;}
            .panel-title>.label-default{margin-right: 5px;}
            .glyphicon {top: 4px}
            .bootstrap-switch .bootstrap-switch-handle-off, .bootstrap-switch .bootstrap-switch-handle-on, .bootstrap-switch .bootstrap-switch-label{
                line-height: 12px
            }
        </style>

    </head>
    <body>

        <!-- 模态弹出窗 -->
        <div class="modal fade" id="mymodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden='true' data-backdrop='static'>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header" style="padding:15px 15px 5px;">
                        <h2 class="modal-title" id="myModalLabel">回归测试
                            <span class="label label-default" id="total_api" style="font-size:12px;"></span>
                            <span class="label label-default" id="total_unit" style="font-size:12px;margin-left: 5px"></span>
                        </h2>
                        <span class="label label-success" id="fail_count" style="float: right;margin-left: 5px"></span>
                        <span class="label label-success" id="success_count" style="float: right;"></span>
                    </div>
                    <div id="regression_testing_detail" class="modal-body" >测试所有接口保存的所有测试用例<br/>正在进行回归测试时可关闭面板，成功后也可查看！
                    </div>
                    <div class="modal-footer">
                        <span id="start_regression_test_time" style="float: left;"></span>
                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                        <button id="start_regression_test" type="button" class="btn btn-primary">开始测试</button>
                    </div>
                </div>
            </div>
        </div>

        <div>

            <div class="col-md-3 col-sm-12">
                <div class="TreeMenuList">
                    <div id="TreeMenu"></div>
                </div>
            </div>

            <div class="col-md-9 col-sm-12" role="main">

                <?php if ($model): ?>
                    <h1>
                        <?php echo $model['title']; ?>
                        <?php if ($model['author']): ?>
                            <span style="font-size:16px;margin-left:20px;">— <?php echo $model['author']; ?>
                                <button id="regression_testing" type="button" data-toggle="modal" data-target="#mymodal" class="btn save-btn" style="float: right;">回归测试</button>
                            </span>

                        <?php endif; ?>
                    </h1>

                    <pre><span class="label label-primary"><?php echo $model['method']; ?></span>  <span class="label label-default"><?php echo !empty($debugRoute) ? '{host}/'.$debugRoute : ''; ?></span><br/><br/><?php echo $model['uses'] ? "<b>用途：{$model['uses']}</b>" : ''; ?><br/><b>回归测试：</b><input id="save_reg_test" type="checkbox"/><br/><b>完全匹配：</b><input type="radio" name="reg-model" value="1"/>&nbsp;<b>请求成功：</b><input type="radio" name="reg-model" value="2">
                    </pre>
                    <ul class="tabs">
                        <li class="active"><a href="#tab1">接口文档</a></li>
                        <li><a href="#tab2">在线测试</a></li>
                    </ul>
                    <div class="tab_container">
                        <div id="tab1" class="tab_content" style="display: block; padding: 2%;">
                            <?php
                                echo view('document::_table', [
                                    'title'  => '请求头',
                                    'values' => $model['header'],
                                ]);
                                echo view('document::_table', [
                                    'title'  => '请求参数',
                                    'values' => $model['params'],
                                ]);
                            ?>

                            <h3>请求示例</h3>
                            <textarea id="input_request_example" class="input-example" placeholder="请输入curl请求示例" style="width:100%;overflow: scroll;"></textarea>
                            <div class="form-group">
                                <button name="request_example" type="button" class="btn btn-primary submit-example" data-loading-text="保存中..." autocomplete="off">保存</button>
                            </div>

                            <h3>返回示例</h3>
                            <textarea id="input_response_example" class="input-example" placeholder="请输入返回示例" style="width:100%;; min-height:300px;max-height:600px;overflow: scroll;"></textarea>
                            <div class="form-group">
                                <button name="response_example" type="button" class="btn btn-primary submit-example" data-loading-text="保存中..." autocomplete="off">保存</button>
                            </div>

                            <h3>返回值说明</h3>
                            <textarea id="input_response_desc" class="input-example" placeholder="请输入返回值说明" style="width:100%; min-height:200px;max-height:600px;overflow: scroll;"></textarea>
                            <div class="form-group">
                                <button name="response_desc" type="button" class="btn btn-primary submit-example" data-loading-text="保存中..." autocomplete="off">保存</button>
                            </div>

                        </div>
                        <div id="tab2" class="tab_content" style="display: none; ">
                            <?php
                                echo view('document::_debug', [
                                    'route'      => $debugRoute,
                                    'debugUrl'   => $debugUrl,
                                    'model'      => $model,
                                    'localApi'   => $localApi,
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
                    <p>备注:目前已支持的类型包括：int,string,array,file</p>
                    <pre>/**<br/> * @name &nbsp; 测试<br/> * @uses &nbsp; 测试接口<br/> * @author wangmeng<br/> * <?php    echo '@method'; ?> &nbsp;POST<br/> * @header&nbsp; string|true    $token              header头<br/> * @param &nbsp;string|true    $str                字符串<br/> * @param &nbsp;int|true       $number             数字<br/> * @param &nbsp;array|true     $arr                数组<br/> * @return&nbsp; array<br/> */<br/>public function test(Request $request)<br/>{<br/>}<br/></pre>
                    <h1>接口请求参数示例</h1>
                    <?php
                        echo view('document::_table', [
                            'title'  => '请求头',
                            'values' => [['name' => 'token', 'is_necessary' => 'true', 'type' => 'string', 'desc' => 'header头']]
                        ]);
                        echo view('document::_table', [
                            'title'  => '请求参数',
                            'values' => [
                                ['name' => 'str', 'is_necessary' => 'true', 'type' => 'string', 'desc' => '字符串'],
                                ['name' => 'number', 'is_necessary' => 'true', 'type' => 'int', 'desc' => '数字'],
                                ['name' => 'arr', 'is_necessary' => 'true', 'type' => 'array', 'desc' => '数组'],
                            ]
                        ]);
                    ?>
                    <h3>请求示例</h3>
                    <pre class="input-example" style="overflow: scroll;">curl -X POST http://dev.apidoc.com/test1 -H 'token: 1' -F str=2 -F number=3 -F arr[]=1 -F arr[]=1
                    </pre>

                    <h3>返回示例</h3>
                    <pre class="input-example" style=" overflow: scroll;">{<br/>    "code": 0,<br/>    "msg": "success",<br/>    "data": {<br/>        "header": {<br/>            "token": "1"<br/>        },<br/>        "body": {<br/>            "str": "2",<br/>            "number": 3,<br/>            "arr": ["1", "1"]<br/>        }<br/>    }<br/>}
                    </pre>

                    <h3>返回值说明</h3>
                    <pre class="input-example" style=" overflow: scroll;">token : header头<br/>str : 字符串<br/>number : 数字<br/>arr : 数组</pre>
                <?php endif; ?>

            </div>

        </div>

    </body>
    <script type="text/javascript">

        var debugUrl = '<?php echo $debugUrl; ?>';

        $(document).ready(function() {

            //是否加入回归测试
            $("#save_reg_test").click(function () {
                uploadExample();
            });

            //是否加入回归测试
            $(":radio[name=reg-model]").click(function () {
                uploadExample();
            });

            //关闭回归测试面板
            $('#mymodal').on('hide.bs.modal', function () {
            });

            //回归测试
            $('#start_regression_test').click(function () {
                var btn = $(this).button('loading');
                var regression_testing_btn = $('#regression_testing').button('loading');
                $('#regression_testing_detail').empty();
                $('#start_regression_test_time').empty();
                $("#total_api").empty();
                $("#total_unit").empty();
                $("#success_count").empty();
                $("#fail_count").empty();
                $('#regression_testing_detail').append('正在进行回归测试，可稍后查看！');
                $.ajax({
                    url: '/document/regression-test',
                    type: 'POST',
                    data: {},
                    success: function(retData) {
                        btn.button('reset');
                        regression_testing_btn.button('reset');
                        $('#start_regression_test_time').append(getDate());
                        $('#regression_testing_detail').empty();
                        if (retData.code == 0) {
                            $("#total_api").html('接口：'+ retData.data.total_api);
                            $("#total_unit").html('用例：'+ retData.data.total_unit);
                            $("#success_count").html('成功：'+ retData.data.success_count);
                            $("#fail_count").html('失败：'+ retData.data.fail_count);
                            if (retData.data.fail_count > 0) {
                                $("#fail_count").addClass('label-danger');
                            }

                            var html = "";
                            var list = retData.data.list;

                            for (var i in list) {
                                var temp = list[i];

                                var fail_count_class = 'label label-success';
                                if (temp.fail_count > 0) {
                                    fail_count_class = 'label label-danger';
                                }

                                html += "<div class='panel panel-default'><div class='panel-heading' style='background-color: #e9e9ec;'>"
                                    + "<h3 class='panel-title'><span class='label label-primary'>"
                                    + temp.title + "</span>&nbsp;<span class='label label-info'>" + temp.method + "</span>&nbsp;&nbsp;<span class='label label-default'>"
                                    + temp.url + "</span>&nbsp;"
                                    + "<span class='" + fail_count_class + "'>失败："
                                    + temp.fail_count + "</span>&nbsp;"
                                    + "<a data-toggle='collapse' href='#collapse_api_" + i + "'>"
                                    + "<span  class='collapse_click glyphicon glyphicon-chevron-right'></span></a>"
                                    + "</h3></div><div id='collapse_api_" + i + "' class='panel-collapse collapse'><div class='panel-body'>";

                                for (var j in temp.list) {
                                    var sub_temp = temp.list[j];

                                    var success_status = '失败';
                                    var success_class = "label label-danger";
                                    if (sub_temp.success) {
                                        success_status = '成功';
                                        success_class = "label label-success";
                                    }

                                    var response = sub_temp.response
                                    response = JSON.stringify(response);
                                    response = js_beautify(response, 4, ' ');

                                    html += "<div class='panel panel-default'><div class='panel-heading' style='padding: 10px 5px;'><h4 class='panel-title'>"
                                        + "<span class='label label-info'>" + sub_temp.test_title + "</span>&nbsp;&nbsp;<span class='"
                                        + success_class +"'>" + success_status + "</span>&nbsp;&nbsp;<a data-toggle='collapse' href='#collapse_unit_test"
                                        + sub_temp.id + "'><span class='label label-warning'>查看结果</span></a></h4></div><div id='collapse_unit_test"
                                        + sub_temp.id + "' class='panel-collapse collapse'><div class='panel-body'><pre><xmp>"
                                        + response + "</xmp></pre></div></div></div>";
                                }
                                html += "</div></div></div>";
                            }
                            $('#regression_testing_detail').append(html);

                            $(".collapse_click").on("click",function(){
                                $(this).toggleClass('glyphicon-chevron-down');
                            });
                        } else {
                            $('#regression_testing_detail').append('回归测试失败,请重试！');
                        }
                    },
                    error: function(retData) {
                        btn.button('reset');
                        regression_testing_btn.button('reset');
                        $('#regression_testing_detail').empty();
                        $('#regression_testing_detail').append('回归测试失败,请重试！');
                    }
                });
            })


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

            renderExample();

            //保存请求响应示例
            $('.submit-example').click(function(){
                var request_method = '<?php echo !empty($model) ? $model['method'] : ''; ?>';
                var author = '<?php echo !empty($model) ? $model['author'] : ''; ?>';
                var uses = '<?php echo !empty($model) ? $model['uses'] : ''; ?>';
                var title = '<?php echo !empty($model) ? $model['title'] : ''; ?>';
                var type = $(this).attr('name');

                var btn = $(this).button('loading');
                if (debugUrl == '') {
                    alert("未配置路由");
                    btn.button('reset');
                    return;
                }
                var desc = $('#input_'+type).val();
                $.ajax({
                    url: '/document/upload-example',
                    type: 'POST',
                    data: {
                        title: title,
                        type: type,
                        value: desc,
                        url: debugUrl,
                        method: request_method,
                        author: author,
                        uses: uses,
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

        function uploadExample() {
            $.ajax({
                url: '/document/upload-example',
                type: 'POST',
                data: {
                    url: debugUrl,
                    regression_test: Number($("#save_reg_test").prop('checked')),
                    regression_model: $(":radio[name=reg-model]:checked").val(),
                    method: '<?php echo !empty($model) ? $model['method'] : ''; ?>',
                    title: '<?php echo !empty($model) ? $model['title'] : ''; ?>',
                    author: '<?php echo !empty($model) ? $model['author'] : ''; ?>',
                    uses: '<?php echo !empty($model) ? $model['uses'] : ''; ?>'
                },
                success: function(retData) {
                },
                error: function(retData) {
                    alert('保存失败');
                }
            });
        }

        //获取当前时间，date格式
        function getDate(){
            var nowDate = new Date();
            var year = nowDate.getFullYear();
            var month = nowDate.getMonth() + 1 < 10 ? "0" + (nowDate.getMonth() + 1) : nowDate.getMonth() + 1;
            var date = nowDate.getDate() < 10 ? "0" + nowDate.getDate() : nowDate.getDate();
            var hour = nowDate.getHours()< 10 ? "0" + nowDate.getHours() : nowDate.getHours();
            var minute = nowDate.getMinutes()< 10 ? "0" + nowDate.getMinutes() : nowDate.getMinutes();
            var second = nowDate.getSeconds()< 10 ? "0" + nowDate.getSeconds() : nowDate.getSeconds();
            return year + "-" + month + "-" + date+" "+hour+":"+minute+":"+second;
        }

        //加载请求返回说明示例
        function renderExample() {
            $.ajax({
                url: '/document/get-example',
                type: 'GET',
                data: {
                    url: debugUrl,
                },
                success: function(retData) {
                    if (retData.regression_test == 1) {
                        $("#save_reg_test").prop("checked", true);
                    }
                    $("input:radio[value='"+retData.regression_model+"']").attr('checked','true');

                    for (var i in retData) {
                        $('#input_'+i).html(retData[i]);
                        $('#input_'+i).autoTextarea({
                            maxHeight:800,
                            minHeight:50
                        });
                    }

                },
                error: function(retData) {
                }
            });
        }

</script>
</html>
