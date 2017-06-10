
<script type="text/javascript" src="/js/bootstrap-filestyle.min.js"></script>


<ul class="pager">
    <li class="previous pull-left">
        {{ link_to("/importings", "&larr; Назад к списку") }}
    </li>

    <li class="pull-right">
        <a href="javascript:window.location.reload();"><i class="glyphicon glyphicon-refresh"></i> Обновить</a>
    </li>
</ul>

<br>

<h2>Загрузка списка</h2>

<br>
<form class="form-horizontal" method="post" enctype="multipart/form-data">

    <table border="0" style="background-color: transparent;">
        <tbody>
        <tr>
            <td>
                <label class="control-label">Загрузить список</label>
                <input  style="display: none;" type="file" name="file" id="file" class="filestyle" data-buttonText="Select a File">
            </td>
            <td>
                {{ select( "groupId", groups, 'using': ['id', 'name'],  'useEmpty' : true, 'emptyText' : 'Группа списка', 'emptyValue' : '' ,  'class' : "form-control", 'style' : "margin: 8px 8px 8px 3px; width: 160px; position: relative;top: 14px;") }}
            </td>
            <td>
                {{ select('checking', checking,  'useEmpty' : true, 'emptyText' : 'Проверка DNS / MX и RFC записи адресов', 'emptyValue' : 0 ,  'class' : "form-control", 'style' : "margin: 8px -2px; position: relative;top: 14px;") }}
            </td>
            <td>
                <button type="submit" style="margin-top: 29px;" class="btn btn-warning">Отправить</button>
            </td>
        </tr>
        </tbody>
    </table>
</form>


<br>
{{ content() }}
{{ flashSession.output() }}
<br><br><br><br>

<script>
    $('#file').filestyle({
//        iconName : 'glyphicon glyphicon-file',
        iconName : 'glyphicon glyphicon-upload',
        buttonText : 'Выбрать файл',
        buttonName : 'btn-success'
    });
</script>