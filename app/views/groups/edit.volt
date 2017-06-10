<ul class="pager">
    <li class="previous pull-left">
        {{ link_to("/groups", "&larr; К списку") }}
    </li>

    <li class="pull-right">
        <a href="javascript:window.location.reload();"><i class="glyphicon glyphicon-refresh"></i> Обновить</a>    </li>
</ul>

<h2>Редактирование списка рассылки</h2>
<br><br>
<form method="post" autocomplete="on">
    <div class="center form-inline">
        <div class="form-group">
            {{ form.render("name", [ 'class' : "form-control"]) }}
            {{ form.render("description", [ 'class' : "form-control"]) }}
            {% if isPriveleged %}{{ form.render("user", [ 'class' : "form-control"]) }}{% endif %}
            {{ form.render("status", [ 'class' : "form-control"]) }}
            {{ submit_button("Обновить", "class": "btn btn-success") }}
        </div>
    </div>

</form>
<br>
{{ content() }}