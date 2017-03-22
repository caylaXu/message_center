<!-- start: Content -->
<div id="content" class="span10">

    <div class="row-fluid">
        <div class="box span12">
            <div class="box-header" data-original-title>
                <h2><i class="halflings-icon map-marker"></i><span class="break"></span>消息日志</h2>

                <div class="box-icon">
                </div>
            </div>
            <div class="box-content">
                <table id="datatable" class="table table-striped table-bordered bootstrap-datatable datatable"
                       style="width: 100%">
                    <thead>
                    <div class="filters">
                        <div>
                            方式：
                            <select name="select" id="message_type">
                                <option value="sms">短信</option>
                                <option value="email">邮件</option>
                                <option value="app_push">推送</option>
                            </select>
                        </div>
                        <div>
                            时间：
                            <input type="text" id="message_start">
                            至
                            <input type="text" id="message_end">
                        </div>
                        <div>
                            发送给：
                            <input type="text" id="message_tos">
                        </div>
                        <div>
                            内容：
                            <input type="text" id="message_content">
                        </div>
                        <div>
                            <input type="button" value="筛选" id="message_filter">
                        </div>
                    </div>
                    <tr>
                        <th>日志标识</th>
                        <th>类型</th>
                        <th>发送给</th>
                        <th>内容</th>
                        <th>备注</th>
                        <th>时间</th>
                        <th>响应</th>
                    </tr>
                    </thead>
                    <tbody style="width: 100%;position: relative;">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!--/row-->

</div>
<!--/.fluid-container-->

<?php include('layout/footer.php'); ?>

<script src="/resource/js/jquery.datetimepicker.min.js"></script>

<script>
$(document).ready(function ()
{
    $.datetimepicker.setLocale('ch');

    $('#message_start').datetimepicker({
        step: 15,
    });

    $('#message_end').datetimepicker({
        step: 15,
    });

    var date = new Date();
    $('#message_start').val(date.getFullYear()+"/"+(date.getMonth())+"/"+date.getDate()+" "+date.getHours()+":"+date.getMinutes());
    $('#message_end').val(date.getFullYear()+"/"+(date.getMonth()+1)+"/"+date.getDate()+" "+date.getHours()+":"+date.getMinutes());

    // DataTable
    var table = $('#datatable').DataTable({
        dom: '<<"#mytoolbox"><r>>t<ip>',
        serverSide: true,
        autoWidth: false,
        ajax: {
            url: '/message_log/filter',
            dataType: 'json',
            data: function (d)
            {
                console.log(d);
                //传递额外的参数给服务器
                d.Type = $('#message_type').val();
                d.Start = $('#message_start').val();
                d.End = $('#message_end').val();
                d.Tos = $('#message_tos').val();
                d.Content = $('#message_content').val();
            },
            type: 'GET',
        },
        pageLength: 20,
        ordering: false,
        processing: true,
        pagingType: 'simple_numbers',
        language: {
            "sProcessing": "努力加载数据中...",
            "sLengthMenu": "每页显示 _MENU_ 条记录",
            "sInfoEmpty": "没有数据",
            "sInfo": "从 _START_ 到 _END_ /共 _TOTAL_ 条数据",
            "sInfoFiltered": "(从 _MAX_ 条数据中检索)",
            "sZeroRecords": "没有检索到数据",
            "oPaginate": {
                "sFirst": "首页",
                "sPrevious": "前一页",
                "sNext": "后一页",
                "sLast": "尾页"
            }
        },
        "columnDefs": [
            {
                "targets": 0,
                "data": "Id",
            },
            {
                "targets": 1,
                "data": "Type",
            },
            {
                "targets": 2,
                "data": "ToUsers",
                "render": function (data, type, row)
                {
                    var tos = '';
                    for (var i = 0; i < data.length; i++)
                    {
                        tos += '<span class="label label-success" style="margin: 2px">' + data[i] + '</span>';
                    }
                    return tos;
                },
            },
            {
                "targets": 3,
                "data": "Content",
                 "render":function(data,type,row){
                    return '<span style="word-wrap: break-word;display:block;max-width:200px;">'+data+'</span>';
                }
            },
            {
                "targets": 4,
                "data": "Attr",
                "render":function(data,type,row){
                    return '<span style="word-wrap: break-word;display:inline-block;max-width:300px;">'+data+'</span>';
                }
            },
            {
                "targets": 5,
                "data": "ReceiptTime",
                "render": function (data, type, row)
                {
                    return '<nobr>' + data + '</nobr>';
                }
            },
            {
                "targets": 6,
                "data": "Response",
                "render":function(data,type,row){
                    return '<span style="word-wrap: break-word;display:inline-block;max-width:275px;">'+data+'</span>';
                }
            }
        ],
        initComplete: function (data)
        {
            $('#message_filter').on('click', function ()
            {
                var start = $('#message_start').val();
                var end = $('#message_end').val();

                if (!start || !end)
                {
                    alert('请正确选择起止日期');
                    return ;
                }

                start = new Date(Date.parse(start)).getTime();
                end = new Date(Date.parse(end)).getTime();
                if (start > end)
                {
                    alert('请正确选择起止日期');
                    return ;
                }
                $('#datatable').DataTable().ajax.reload();
            });

            $('#message_tos').bind('change', function ()
            {
                $('#datatable').DataTable().ajax.reload();
            });

            $('#message_content').bind('change', function ()
            {
                $('#datatable').DataTable().ajax.reload();
            });
        }
    });
});
</script>

</body>
</html>