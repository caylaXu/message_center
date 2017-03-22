<!-- start: Content -->
<div id="content" class="span10">

    <div class="row-fluid">
        <div class="box span12">
            <div class="box-header" data-original-title>
                <h2><i class="halflings-icon map-marker"></i><span class="break"></span>电话管理</h2>

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
                            电话：
                            <input type="text" id="telephone">
                        </div>
                    </div>
                    <tr>
                        <th>电话标识</th>
                        <th>电话号码</th>
                        <th>所属人</th>
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
        <div class="modal-body" style="margin-left: 16%;">
            <input type="hidden" value="" id="modal_id"/><br/>
            <span style="width: 70px;display: inline-block">电话号码: </span>
            <input type="text" value="" id="modal_telephone" maxlength="11"/>(数字)<br/>
            <span style="width: 70px;display: inline-block">所属人: </span>
            <input type="text" value="" id="modal_owner" maxlength="32"/><br/>
            <span style="width: 70px;display: inline-block">模板: </span>
            <ul id="modal_template" style="list-style-type: none">
            </ul>
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
                url: '/telephone/filter',
                type: 'json',
                data: function (d)
                {
                    //传递额外的参数给服务器
                    d.telephone = $('#telephone').val();
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
                    "data": "Telephone",
                },
                {
                    "targets": 2,
                    "data": "Owner",
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
                $('#telephone').bind('change', function ()
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
            $('#modal_telephone').val('');
            $('#modal_owner').val('');

            $.getJSON('/template/fetch', {}, function (data)
            {
                var template = $('#modal_template');
                var child = '';
                template.children().remove();

                for (var i = 0; i < data.length; i++)
                {
                    child = '<li style="margin-left: 50px">' +
                        '<input type="checkbox" name="template_list" id="tpl' + data[i]['Id'] +
                        '" style="width: 20px;height: 20px;" value="' + data[i]['Id'] + '"/>' +
                        '<label style="padding-left: 10px;display: inline;" ' +
                        'for="tpl' + data[i]['Id'] + '">' + data[i]['Name'] + '</label>' +
                        '</li>';
                    template.append(child);
                }
                $('#modal_add').modal('show');
            });

        });

        //编辑弹出框
        $('#datatable tbody').on('click', 'a#edit', function ()
        {
            var row = table.row($(this).parents('tr')).data();
            $('#modal_title').text('编辑');
            $('#modal_id').val(row['Id']);
            $('#modal_telephone').val(row['Telephone']);
            $('#modal_owner').val(row['Owner']);

            var param = {telephone_id: row['Id']};
            $.getJSON('/template/fetch', param, function (data)
            {
                var template = $('#modal_template');
                var child = '';
                template.children().remove();

                for (var i = 0; i < data.length; i++)
                {
                    child = '<li style="margin-left: 50px">' +
                        '<input type="checkbox" name="template_list" id="tpl' + data[i]['Id'] +
                        '" style="width: 20px;height: 20px;" value="' + data[i]['Id'] +
                        '" ' + data[i]['checked'] + '/>' +
                        '<label style="padding-left: 10px;display: inline;" ' +
                        'for="tpl' + data[i]['Id'] + '">' + data[i]['Name'] + '</label>' +
                        '</li>';
                    template.append(child);
                }
                $('#modal_add').modal('show');
            });
        });

        //添加/编辑类型
        $('#modal_submit').click(function ()
        {
            var id = $('#modal_id').val();
            var telephone = $('#modal_telephone').val();
            var owner = $('#modal_owner').val();
            var template = new Array();
            $(":input[name='template_list']:checked").each(function ()
            {
                template.push($(this).val());
            });

            var phoneReg = /^(((13[0-9]{1})|(15[0-9]{1})|(18[0-9]{1}))+\d{8})$/;
            if (!phoneReg.test(telephone))
            {
                alert('请输入有效的手机号码!');
                return false;
            }

            if (id == '')
            {
                var action = 'add';
                var data = {telephone: telephone, owner: owner, template: template}
            }
            else
            {
                var action = 'edit';
                var data = {id: id, telephone: telephone, owner: owner, template: template}
            }

            $.post("/telephone/" + action, data, function (result)
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