<ul class="pager">
    <li class="previous pull-left">
        {{ link_to("/queuing", "&larr; К списку") }}
    </li>

    <li class="pull-right">
        <a href="javascript:window.location.reload();"><i class="glyphicon glyphicon-refresh"></i> Обновить</a>
    </li>
</ul>

<h2>Создание очереди на рассылку</h2>
<br><br>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-9">
            <form class="form-horizontal" method="post" autocomplete="off">
                <div class="form-group">
                    <label for="groupId" class="col-sm-4 control-label">Список адресов</label>
                    <div class="col-sm-8 input-group">
                        <span class="input-group-addon"><i class="fa fa-book"></i></span>
                        {{ form.render("groupId", [ 'class' : "form-control"]) }}
                    </div>
                </div>
                <div class="form-group">
                    <label for="categoryId" class="col-sm-4 control-label">Сообщение</label>
                    <div class="col-sm-8 input-group">
                        <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                        {{ form.render("messageId", [ 'class' : "form-control"]) }}
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-4 col-sm-8 input-group">
                        <button type="submit" class="btn btn-success"><i class="fa fa-share-square-o"></i>  Создать очередь!</button>
                        {#{{ submit_button('<i class="fa fa-share-square-o"></i>  Создать очередь!', "class": "btn btn-success") }}#}
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<br>
{{ content() }}