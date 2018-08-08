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
                            <input type="text" class="form-control form-control-header" name="<?php echo trim($param['name'], '$'); ?>" >
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
                                <?php echo $param['name']; ?>
                            </label>
                            <input type="text" class="form-control form-control-body" name="<?php echo trim($param['name'], '$'); ?>">
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
            <pre id="response">Empty.</pre>
        </div>

    </div>
</div>
<script type="text/javascript">
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
                if ($(this).val() != '') {
                    header[$(this).attr('name')] = $(this).val();
                }
            });
            $('.form-control-body').each(function(){
                if ($(this).val() != '') {
                    data[$(this).attr('name')] = $(this).val();
                }
            });

            //存储参数
            storageParams(debugUrl, header, data)

            $.ajax({
                url: debugUrl,
                type: '<?php echo $model->method(); ?>',
                headers: header,
                data: data,
                success: function(retData) {
                    btn.button('reset');
                    if (typeof retData === 'string' && retData.indexOf('content="text/html;') != -1) {
                        for (key in data) {
                            debugUrl += key + '=' + data[key] + '&';
                        }
                        window.open(debugUrl);
                        $('#response').html('该接口是返回html页面，请允许浏览器弹出新页面或自行在浏览器调试');
                    } else {
                        if(typeof retData == 'object')
                        {
                            retData = JSON.stringify(retData);
                        }
                        var formatText = js_beautify(retData, 4, ' ');
                        $('#response').html(formatText);
                    }
                },
                error: function(retData) {
                    btn.button('reset');
                    alert('发生错误');
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

        //body更新当前接口
        var data = JSON.parse(window.localStorage.getItem(debugUrl))
        if (data) {
            for (var i in data['body']) {
                $("input[name='" + i + "'].form-control-body").val(data['body'][i]);
            }
        }

        //header更新所有接口
        var header = JSON.parse(window.localStorage.getItem('header'))
        if (header) {
            for (var i in header) {
                $("input[name='" + i + "'].form-control-header").val(header[i]);
            }
        }
    }

    //存储参数
    function storageParams(debugUrl, header, body) {
        var data = {
            "body" : body,
        }
        if (Object.keys(header).length > 0) {
            window.localStorage.setItem('header', JSON.stringify(header));
        }

        window.localStorage.setItem(debugUrl, JSON.stringify(data));
    }
</script>