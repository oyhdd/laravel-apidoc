<?php echo !empty($visualRoute) ? "<h3>{$visualRoute}</h3>" : ''; ?>
<button id="analyse-btn" type="button" class="btn submit-btn" data-loading-text="分析中..." autocomplete="off">分析</button>

<script type="text/javascript">

    var visualRoute = '<?php echo $visualRoute; ?>';

    $(function(){
        $('#analyse-btn').click(function(){
            if (!visualRoute) {
                alert("当前接口不可分析");
                return;
            }
            var btn = $(this).button('loading');
            $.ajax({
                url: '/document/visual-code',
                type: 'POST',
                data: {
                    route: visualRoute
                },
                success: function(retData) {
                    if (retData.code == 0) {

                    } else {
                        alert(retData.message);
                    }
                },
                error: function(retData) {
                    alert('分析失败！')
                }
            });
            btn.button('reset');
        });
    });
</script>