<!-- start: Content -->
<div id="content" class="span10" xmlns="http://www.w3.org/1999/html">

    <div class="row-fluid">
        <div class="box span12">
            <div class="box-header" data-original-title>
                <h2><i class="halflings-icon map-marker"></i><span class="break"></span>消息模板管理</h2>

                <div class="box-icon">
                    <a href="#" class="btn-setting" id="add_telephone"><i class="halflings-icon plus"></i></a>
                </div>
            </div>
            <div class="box-content">
                <table id="datatable" class="table table-striped table-bordered bootstrap-datatable datatable"
                       style="width: 100%">
                    <thead>
                    <div class="filters">
                        <div>
                            名称：
                            <input type="text" id="template">
                        </div>
                    </div>
                    <tr>
                        <th>模板标识</th>
                        <th>模板名称</th>
                        <th>模板内容</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!--/row-->

    <div class="modal hide fade in" id="modal_add" aria-hidden="false" style="">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">×</button>
            <h3 id="modal_title">编辑</h3>
        </div>
        <div class="modal-body" style="margin-left: 10%;">
            <input type="hidden" value="" id="modal_id"/><br/>
            <span style="width: 70px;display: inline-block">模板名称：</span>
            <input type="text" style="width: 300px" value="" id="modal_name" maxlength="32"/>(英文)<br/>
            <span style="width: 70px;display: inline-block">模板内容: </span>
            <textarea type="text" style="width: 300px;height: 300px;" value="" id="modal_message"
                      maxlength="255"></textarea><br/>
        </div>
        <div class="modal-footer">
            <a href="#" class="btn" data-dismiss="modal">关闭</a>
            <a href="#" class="btn btn-primary" id="modal_submit">提交</a>
        </div>
    </div>

</div>
<!--/.fluid-container-->

<?php include('layout/footer.php'); ?>

<script>
    $(document).ready(function ()
    {
        // DataTable
        var table = $('#datatable').DataTable({
            dom: '<<"#mytoolbox"><r>>t<ip>',
            serverSide: true,
            ajax: {
                url: '/template/filter',
                type: 'json',
                data: function (d)
                {
                    //传递额外的参数给服务器
                    d.template = $('#template').val();
                },
                type: 'GET',
            },
            pageLength: 20,
            ordering: false,
            processing: true,
            pagingType: 'simple_numbers',
            oLanguage: {
                "sProcessing": "努力加载数据中...",
                "sLengthMenu": "每页显示 _MENU_ 条记录",
                "sZeroRecords": "抱歉， 没有找到",
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
                    "data": "Name",
                },
                {
                    "targets": 2,
                    "data": "Message",
                },
                {
                    "targets": -1,
                    "data": null,
                    "defaultContent": '' +
                    '<td class="center">' +
                    '<a class="" href="#" id="edit"><i class="halflings-icon edit"></i></a>' +
                    '</td>'
                }
            ],
            initComplete: function (data)
            {
                $('#template').bind('change', function ()
                {
                    $('#datatable').DataTable().ajax.reload();
                });
            },
        });

        //添加弹出框
        $('#add_telephone').click(function ()
        {
            $('#modal_title').text('添加');
            $('#modal_id').val('');
            $('#modal_name').val('');
            $('#modal_message').val('');
            $('#modal_add').modal('show');
        });

        //编辑弹出框
        $('#datatable tbody').on('click', 'a#edit', function ()
        {
            var row = table.row($(this).parents('tr')).data();
            $('#modal_title').text('编辑');
            $('#modal_id').val(row['Id']);
            $('#modal_name').val(row['Name']);
            $('#modal_message').val(row['Message']);
            $('#modal_add').modal('show');
        });

        //添加/编辑类型
        $('#modal_submit').click(function ()
        {
            var id = $('#modal_id').val();
            var name = $('#modal_name').val();
            var message = $('#modal_message').val();

            if (id == '')
            {
                var action = 'add';
                var data = {name: name, message: message}
            }
            else
            {
                var action = 'edit';
                var data = {id: id, name: name, message: message}
            }

            $.post("/template/" + action, data, function (result)
            {
                if (result.status != 0)
                {
                    alert(result.message);
                }
                else
                {
                    $('#modal_add').modal('hide');
                    $('#datatable').DataTable().ajax.reload();
                }
            });
        });

    });
</script>

</body>
</html>