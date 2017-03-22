<!-- start: Content -->
<div id="content" class="span10">
    选择您需要的通道？<br /><br />
    <label><input name="ChannelNames" type="radio" value="ShangHaiChuangLan" />上海创蓝</label>
    <label><input name="ChannelNames" type="radio" value="MengWang" />梦网</label>
    <label><input name="ChannelNames" type="radio" value="WeiWangTongLian" />微网通联</label>
    <button type="button" onclick="jqchk()" style="margin-top: 20px;">发送</button>
    <script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
    <script>
        $(function(){
            $.get('/change_config/get','',
                    function (data) //回传函数
                    {
                        $("input[value="+data.data+"]").attr("checked",true);
                    },"json"
            );
        });

        function jqchk(){  //jquery获取复选框值
            var ChannelNames ='';
            ChannelNames = $('input:radio:checked').val();
            if(ChannelNames.length==0)
            {
                alert('请选择短信通道？');
            }
            else
            {
                $.post('/change_config/change',{ChannelNames:ChannelNames},
                        function (data) //回传函数
                        {
                            alert(data.msg);
                        },"json"
                );
            }
        }
    </script>
</div>
</body>
</html>