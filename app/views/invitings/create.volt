
<ul class="pager">
    <li class="previous pull-left">
        {{ link_to("/invitings", "&larr; К списку") }}
    </li>

    <li class="pull-right">
        <a href="javascript:window.location.reload();"><i class="glyphicon glyphicon-refresh"></i> Обновить</a>
    </li>
</ul>

{{ content() }}

<h2>Создание приглашения</h2>
<br>
<form method="post" autocomplete="off">
    <div class="form-inline">
        <div class="form-group">
            {{ form.label("email") }}
            {{ form.render("email", [ 'class' : "form-control"]) }}
        {#</div>#}
        {#<div class="form-group">#}
            {{ submit_button("Сохранить", "class": "btn btn-success") }}
        </div>
    </div>

</form>
<br>
