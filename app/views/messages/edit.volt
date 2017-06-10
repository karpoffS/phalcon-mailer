{#<script src="/ckeditor/ckeditor.js"></script>#}
<ul class="pager">
    <li class="previous pull-left">
        {{ link_to("/messages", "&larr; К списку") }}
    </li>

    <li class="pull-right">
        <a href="javascript:window.location.reload();"><i class="glyphicon glyphicon-refresh"></i> Обновить</a>
    </li>
</ul>
{{ content() }}
<h2>Редактирование сообщения</h2>
<br><br>
{#{{ dump(form) }}#}
<form method="post" autocomplete="off">
    <div class="center col-md-8 col-md-offset-2">
        <div class="form-group">
            {{ form.label("subject") }}
            {{ form.render("subject", [ 'class' : "form-control"]) }}
        </div>
        <div class="form-group">
            {{ form.label("body") }}
            {{ form.render("body", [ 'class' : "form-control"]) }}
            {#<script type="text/javascript">#}
                {#CKEDITOR.replace( 'body' );#}
            {#</script>#}
        </div>

        <div class="form-group">
            {{ form.label("categoryId") }}
            {{ form.render("categoryId", [ 'class' : "form-control"]) }}
        </div>
        {#<div class="form-group">#}
        {#{{ form.render("createdAt", [ 'class' : "form-control"]) }}#}
        {#{{ form.render("modifyAt", [ 'class' : "form-control"]) }}#}
        {#</div>#}
        {% if isPriveleged %}
            <div class="form-group">
                {{ form.render("user", [ 'class' : "form-control"]) }}
            </div>
        {% endif %}
        <div class="form-group">
            {{ form.render("status", [ 'class' : "form-control"]) }}
        </div>
        <div class="form-group">
            {{ submit_button("Сохранить", "class": "btn btn-success") }}
        </div>
    </div>

</form>
<br>
