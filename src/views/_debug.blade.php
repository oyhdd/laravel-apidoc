<style type="text/css">
    .input-group{margin: 10px 0}
    .form-group{margin-top: 10px}
    .form-group input{width: 100%;}
    .submit-btn{width: 82px;margin: 20px 0; background: linear-gradient(to right, #2091cf, #0758f0);color: white;}
    .delete-btn{width: 82px;margin: 20px 0; background: linear-gradient(to right, #f36565, #c55c5c);color: white;}
    .save-btn{width: 82px;background: linear-gradient(to right, #5dd03e, #5CB85C);color: white;}
</style>

<div class="container-fluid" style="padding:2%;">
    <div class="row">

        <div class="col-md-4 col-sm-12">
            <h3>测试用例：</h3>

            <select  id="selectpicker" class="selectpicker" data-style="btn-info" data-live-search="true">
            </select>

            </br></br>
            <form role="form" class="debug">
                <h3>header</h3>
                <?php if ($model['header']): ?>
                    <?php foreach ($model['header'] as $param): ?>
                        <div class="form-group">
                            <label>
                                <?php
                                    echo $param['name'];
                                    if (!empty($param['is_necessary'])) {
                                        echo '<span style="color:red">&nbsp*</span>';
                                    }
                                ?>
                            </label>
                            <textarea type="text" class="form-control form-control-header" data_type="<?php echo $param['type']?>" name="<?php echo trim($param['name'], '$'); ?>" placeholder="<?php echo $param['desc']; ?>" style="height: 34px"></textarea>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <span>无</span>
                <?php endif; ?>
                <h3>body</h3>
                <?php if ($model['params']): ?>
                    <?php foreach ($model['params'] as $param): ?>
                        <div class="form-group">
                            <label>
                                <?php
                                    echo $param['name'];
                                    if (!empty($param['is_necessary'])) {
                                        echo '<span style="color:red">&nbsp*</span>';
                                    }
                                ?>
                            </label>
                            <?php if ($param['type'] == 'file'): ?>
                                <input type="<?php echo ($param['type'] == 'file') ? 'file' : 'text'; ?>" class="form-control form-control-body" data_type="<?php echo $param['type']?>" name="<?php echo trim($param['name'], '$'); ?>" placeholder="<?php echo $param['desc']; ?>" style="height: 34px"></input>
                            <?php else: ?>
                                <textarea type="<?php echo ($param['type'] == 'file') ? 'file' : 'text'; ?>" class="form-control form-control-body" data_type="<?php echo $param['type']?>" name="<?php echo trim($param['name'], '$'); ?>" placeholder="<?php echo $param['desc']; ?>" style="height: 34px"></textarea>
                            <?php endif; ?>

                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="form-group">无</div>
                <?php endif; ?>
                <button id="submit-btn" type="button" class="btn submit-btn" data-loading-text="运行中..." autocomplete="off">运行</button>
                <button id="delete-btn" type="button" class="btn delete-btn" data-loading-text="删除中..." autocomplete="off" style="display: none">删除用例</button>
            </form>
        </div>

        <div class="col-md-8 col-sm-12" role="main">
            <h3>请求返回:</h3>
            <div class="input-group">
                <input id="save_title" type="text" class="form-control" placeholder="请输入测试用例标题">
                <span class="input-group-btn">
                    <button id="save-btn" type="button" class="btn save-btn" data-loading-text="保存中..." autocomplete="off">保存用例</button>
                </span>
            </div>
            <pre id="ret">HTTP状态码：</br>请求时间：</br>curl请求示例：</pre>
            <pre id="response">返回内容：</pre>
        </div>

    </div>
</div>
<script type="text/javascript">
    var header = {};
    var data = {};
    var can_save = false;
    var response = {};
    var author = '<?php echo $model['author']; ?>';
    var uses = '<?php echo $model['uses']; ?>';
    var title = '<?php echo $model['title']; ?>';
    var debugUrl = '<?php echo $debugUrl; ?>';
    var request_method;
    var testUnitData = {};

    $(function(){

        $('#submit-btn').click(function(){
            request_method = '<?php echo $model['method']; ?>';

            var form = new FormData();
            var processData = false;
            var contentType = false;
            header = {};
            data = {};
            response = {};

            var btn = $(this).button('loading');

            if (debugUrl == '') {
                alert("未配置路由");
                btn.button('reset');
                return;
            }
            $('.form-control-header').each(function(){
                var val = $(this).val();
                var data_type = $(this).attr('data_type');
                if (val != '') {
                    var type = '';
                    if ($(this).attr('data_type') == 'array') {
                        type = '[]';
                        val = new Array();
                        val = $(this).val().split(',');
                    }
                    header[$(this).attr('name') + type] = (data_type == 'int') ? Number(val) : val;
                }
            });
            $('.form-control-body').each(function(){
                var val = $(this).val();
                var data_type = $(this).attr('data_type');
                if (data_type == 'file') {
                    var files = $(this).prop('files');
                    if (files.length != 0) {
                        form.append($(this).attr('name'), files[0]);
                    }
                } else if (val != '') {
                    var type = '';
                    if (data_type == 'array') {
                        type = '[]';
                        val = new Array();
                        val = $(this).val().split(',');
                        for (var param in val) {
                            form.append($(this).attr('name')+'[]', val[param]);
                        }
                    } else {
                        form.append($(this).attr('name'), val);
                    }
                    data[$(this).attr('name') + type] = (data_type == 'int') ? Number(val) : val;
                }
            });

            var sendDate = (new Date()).getTime();
            if (request_method == 'GET' || request_method == 'get') {
                form = data;
                processData = true;
                contentType = true;
            }
            $.ajax({
                url: debugUrl,
                type: request_method,
                headers: header,
                data: form,
                processData:processData,
                contentType:contentType,
                success: function(retData) {
                    can_save = true;
                    response = retData;

                    if (typeof retData === 'string') {
                        retData = $.trim(retData);
                    }
                    var receiveDate = (new Date()).getTime();
                    btn.button('reset');
                    var curl_example = getCurlExample();
                    $('#ret').html("HTTP状态码：200</br>请求时间：" + (receiveDate - sendDate) + "ms" + "</br>curl请求示例：" + curl_example);
                    $('#ret').css({
                        color: 'green',
                    });
                    if (typeof retData === 'string' && retData.indexOf('content="text/html;') != -1) {
                        for (key in data) {
                            debugUrl += key + '=' + data[key] + '&';
                        }
                        window.open(debugUrl);
                        $('#response').html('该接口是返回html页面，请允许浏览器弹出新页面或自行在浏览器调试');
                    } else {
                        if (typeof retData == 'object') {
                            retData = JSON.stringify(retData);
                            var formatText = js_beautify(retData, 4, ' ');
                        } else if (retData.indexOf('<script> Sfdump = window.Sfdump') != -1) {
                            var formatText = retData;
                        } else if (typeof retData === 'string') {
                            //防中文乱码
                            retData = eval("("+retData+")")
                            retData = JSON.stringify(retData);
                            var formatText = js_beautify(retData, 4, ' ');
                        } else {
                            var formatText = retData;
                        }

                        $('#response').html(formatText);
                    }
                },
                error: function(retData) {
                    var receiveDate = (new Date()).getTime();
                    btn.button('reset');
                    var curl_example = getCurlExample();
                    $('#ret').html("HTTP状态码：" + retData.status + "</br>请求时间："+(receiveDate - sendDate)+"ms" + "</br>curl请求示例：" + curl_example);
                    $('#ret').css({
                        color: 'red',
                    });
                    if (typeof retData === 'string' && retData.indexOf('content="text/html;') != -1) {
                        for (key in data) {
                            debugUrl += key + '=' + data[key] + '&';
                        }
                        window.open(debugUrl);
                        $('#response').html('该接口是返回html页面，请允许浏览器弹出新页面或自行在浏览器调试');
                    } else {
                        if (retData.responseText.indexOf('<script> Sfdump = window.Sfdump') != -1) {
                            var formatText = retData.responseText;
                        } else if (typeof retData == 'object') {
                            retData = JSON.stringify(retData);
                            var formatText = js_beautify(retData, 4, ' ');
                        }
                        $('#response').html(formatText);
                    }
                }
            });
        });

        // 保存测试用例
        $('#save-btn').click(function(){
            var test_title = $("#save_title").val();

            if (!can_save) {
                alert("请先提交测试并确保测试结果正确");
                return;
            }
            if (test_title == '') {
                alert("请填写测试用例标题");
                return;
            }

            var btn = $(this).button('loading');
            $.ajax({
                url: '/document/upload-api-params',
                type: 'POST',
                data: {
                    test_title: test_title,
                    title: title,
                    url: debugUrl,
                    method: request_method,
                    header: JSON.stringify(header),
                    body: JSON.stringify(data),
                    response: JSON.stringify(response),
                    author: author,
                    uses: uses,
                },
                success: function(retData) {
                    if (retData.code == 0) {
                        can_save = false;
                    }
                    refreshTestUnit();
                    alert(retData.message);
                },
                error: function(retData) {
                    alert('保存失败！')
                }
            });
            btn.button('reset');
        });

        // 保存测试用例
        $('#delete-btn').click(function(){
            if(!confirm("确定删除该测试用例?")){
                return;
            }
            var test_unit_index = $('#selectpicker').val();

            var btn = $(this).button('loading');
            $.ajax({
                url: '/document/delete-api-params',
                type: 'POST',
                data: {
                    id: testUnitData[test_unit_index]['id'],
                },
                success: function(retData) {
                    if (retData.code == 0) {
                        alert(retData.message);
                    }
                    refreshTestUnit();
                },
                error: function(retData) {
                    alert('删除失败！')
                }
            });
            btn.button('reset');
        });

        //初始化测试用例下拉框
        $('#selectpicker').on('loaded.bs.select', function () {
            refreshTestUnit()
        });

        //下拉选择改变事件
        $('#selectpicker').on('changed.bs.select', function () {
            var test_unit_index = $('#selectpicker').val();
            autoLoadParams(test_unit_index);
        });
    });

    //刷新测试用例
    function refreshTestUnit() {
        $(".selectpicker").empty();//清空option列表数据
            $(".selectpicker").append("<option value='-1'>新建用例</option>")
            $.ajax({
                url: '/document/get-api-params',
                type: 'GET',
                data: {
                    url: debugUrl,
                },
                success: function(retData) {
                    if (retData.code == 0) {
                        testUnitData = retData.data;
                        for (var i in testUnitData) {
                            $(".selectpicker").append("<option value='"+i+"'>"+testUnitData[i].test_title+"</option>")
                        }
                    }
                    $('.selectpicker').selectpicker('refresh');
                },
                error: function(retData) {
                    console.log('获取测试用例失败！')
                }
            });
    }

    /**
     * @name   自动加载参数
     * @uses   header更新所有接口 body更新当前接口
     */
    function autoLoadParams(test_unit_index) {
        var data = testUnitData[test_unit_index];

        $(".debug textarea").val("");
        $(".debug input").val("");
        $("#save_title").val("");
        if (test_unit_index < 0) {
            $("#save_title").removeAttr("readonly");
            $("#delete-btn").css('display','none');
            return;
        }
        $("#delete-btn").css('display','inline');
        $("#save_title").attr("readonly","readonly");
        $("#save_title").val(data['test_title']);
        //加载当前接口的header和body
        for (var i in data['header']) {
            $("textarea[name='" + i + "'].form-control-header").val(data['header'][i]);
        }
        for (var i in data['body']) {
            var cache = data['body'][i];
            if (cache instanceof Array) {
                i = i.slice(0, -2);
                cache = cache.join(',');
            }
            $("textarea[name='" + i + "'].form-control-body").val(cache);
        }
    }

    /**
     * @name   获取curl请求示例
     * @uses   将当前请求参数转换为curl请求示例
     */
    function getCurlExample() {
        var curl_example = '';
        var test_unit_index = $('#selectpicker').val();
        if (request_method == 'GET') {
            curl_example += "curl -g " + debugUrl;
            var index = 0;
            for (var key in data) {
                if (data[key] instanceof Array) {
                    for (var i in data[key]) {
                        if (index == 0) {
                            curl_example += "?" + key + "=" + data[key][i];
                        } else {
                            curl_example += "\\&" + key + "=" + data[key][i];
                        }
                    }
                } else {
                    if (index == 0) {
                        curl_example += "?" + key + "=" + data[key];
                    } else {
                        curl_example += "\\&" + key + "=" + data[key];
                    }
                }
                index ++;
            }
            for (var key in header) {
                curl_example += " -H '" + key + ": " + header[key] + "'";
            }
        } else if (request_method == 'POST') {
            curl_example += "curl -X POST " + debugUrl;
            for (var i in header) {
                curl_example += " -H '" + i + ": " + header[i] + "'";
            }
            for (var key in data) {
                if (data[key] instanceof Array) {
                    for (var i in data[key]) {
                        curl_example += " -F " + key + "=" + data[key][i];
                    }
                } else {
                    curl_example += " -F " + key + "=" + data[key];
                }
            }
        }

        return curl_example;
    }
</script>
