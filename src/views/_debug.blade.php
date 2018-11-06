<style type="text/css">
    .form-group{margin-top: 10px}
    .form-group input{width: 100%;}
    .submit-btn{margin: 20px 0; background: linear-gradient(to right, #2091cf, #0758f0);color: white;}
</style>
<div class="container-fluid" style="padding:2%;">
    <div class="row">

        <div class="col-md-4 col-sm-12">
            <h3>路由：</h3>
            <span><?php echo empty($route) ? "未配置路由" : '/'.$route; ?></span>
            </br></br>
            <form role="form">
                <h3>header</h3>
                <?php if ($model->header()): ?>
                    <?php foreach ($model->header() as $param): ?>
                        <div class="form-group">
                            <label>
                                <?php echo $param['name']; ?>
                            </label>
                            <input type="text" class="form-control form-control-header" data_type="<?php echo $param['type']?>" name="<?php echo trim($param['name'], '$'); ?>" placeholder="<?php echo $param['desc']; ?>">
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <span>无</span>
                <?php endif; ?>
                <h3>body</h3>
                <?php if ($model->params()): ?>
                    <?php foreach ($model->params() as $param): ?>
                        <div class="form-group">
                            <label>
                                <?php echo  $param['name']; ?>
                            </label>
                            <input type="text" class="form-control form-control-body" data_type="<?php echo $param['type']?>" name="<?php echo trim($param['name'], '$'); ?>" placeholder="<?php echo $param['desc']; ?>">
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="form-group">无</div>
                <?php endif; ?>
                <button id="submit-btn" type="button" class="btn btn-primary submit-btn" data-loading-text="提交中..." autocomplete="off">提交</button>
            </form>
        </div>

        <div class="col-md-8 col-sm-12" role="main">
            <h3>请求返回:</h3>
            <pre id="ret">HTTP状态码：</br>请求时间：</pre>

            <pre id="response">返回内容：</pre>
        </div>

    </div>
</div>
<script type="text/javascript">
    var syncHeader = '<?php echo $syncHeader; ?>';

    $(function(){

        autoLoadParams();

        $('#submit-btn').click(function(){
            var btn = $(this).button('loading');

            var header = {};
            var data = {};
            var debugUrl = '<?php echo $debugUrl; ?>';
            if (debugUrl == '') {
                alert("未配置路由");
                btn.button('reset');
                return;
            }
            $('.form-control-header').each(function(){
                var val = $(this).val();
                if (val != '') {
                    var data_type = $(this).attr('data_type');
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
                if (val != '') {
                    var data_type = $(this).attr('data_type');
                    var type = '';
                    if (data_type == 'array') {
                        type = '[]';
                        val = new Array();
                        val = $(this).val().split(',');
                    }
                    data[$(this).attr('name') + type] = (data_type == 'int') ? Number(val) : val;
                }
            });

            //存储参数
            storageParams(debugUrl, header, data)
            var sendDate = (new Date()).getTime();
            console.log(1,sendDate)
            $.ajax({
                url: debugUrl,
                type: '<?php echo $model->method(); ?>',
                headers: header,
                data: data,
                success: function(retData) {
                    var receiveDate = (new Date()).getTime();
                    btn.button('reset');
                    $('#ret').html("HTTP状态码：200</br>请求时间："+(receiveDate - sendDate)+"ms");
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
                        } else {
                            var formatText = retData;
                        }
                        $('#response').html(formatText);
                    }
                },
                error: function(retData) {
                    var receiveDate = (new Date()).getTime();
                    btn.button('reset');
                    $('#ret').html("HTTP状态码：" + retData.status + "</br>请求时间："+(receiveDate - sendDate)+"ms");
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
    });

    /**
     * @name   自动加载参数
     * @uses   header更新所有接口 body更新当前接口
     */
    function autoLoadParams() {
        var debugUrl = '<?php echo $debugUrl; ?>';

        //加载当前接口的header和body
        var data = JSON.parse(window.localStorage.getItem(debugUrl))
        if (data) {
            for (var i in data['header']) {
                $("input[name='" + i + "'].form-control-header").val(data['header'][i]);
            }
            for (var i in data['body']) {
                var cache = data['body'][i];
                if (cache instanceof Array) {
                    i = i.slice(0, -2);
                    cache = cache.join(',');
                }
                $("input[name='" + i + "'].form-control-body").val(cache);
            }
        }

        //若设置header同步，则使用最新的header
        var header = JSON.parse(window.localStorage.getItem('header'))
        if (syncHeader && header) {
            for (var i in header) {
                $("input[name='" + i + "'].form-control-header").val(header[i]);
            }
        }
    }

    //存储参数
    function storageParams(debugUrl, header, body) {
        var data = {
            "header" : header,
            "body" : body,
        }

        //header不为空且设置为同步时缓存
        if (Object.keys(header).length > 0 && syncHeader) {
            window.localStorage.setItem('header', JSON.stringify(header));
        }

        window.localStorage.setItem(debugUrl, JSON.stringify(data));
    }
</script>