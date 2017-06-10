
<h2>Список адресов</h2>

{% if page.items or isPriveleged %}
<form method="post" autocomplete="off">
    <div class="center form-inline">

        <div class="panel panel-default">
            <div class="panel-body">
                <div class="form-group" style="float: left;">
                    {% if isPriveleged %}{{ form.render("userId", [ 'class' : "form-control"]) }}{% endif %}
                    {{ form.render("groupId", [ 'class' : "form-control", 'style' : "width: 160px"]) }}
                    {{ form.render("confirmed", [ 'class' : "form-control", 'style' : "width: 160px"]) }}
                    {{ form.render("unsubscribe", [ 'class' : "form-control", 'style' : "width: 160px"]) }}
                    {{ form.render("status", [ 'class' : "form-control", 'style' : "width: 140px"]) }}

                    {#{{ submit_button('<i class="fa fa-search"></i> Искать', "class": "btn btn-info") }}#}
                    <button type="submit" class="btn btn-info"><i class="fa fa-search"></i> Искать</button>
                </div>
                <div style="float: right;">
                    {% set import_title = "Импортировать <span class='badge'>" ~ imports.count() ~ "</span>" %}
                    {{ link_to("/importings", import_title, "class": "btn btn-primary") }}
                </div>
            </div>
        </div>
        </div>
</form>
{% endif %}

<br>

{{ content() }}
{{ flashSession.output() }}

 {% if showImportLink %}
     {{ link_to("/importings", 'Перейти в раздел импортирования списоков адресов') }}
 {% endif %}

{% for email in page.items %}
    {% if loop.first %}
        <table class="table table-responsive table-hover" align="center">
        <thead>
            <tr>
                <th>№</th>
                {% if isPriveleged %}<th>Id</th>{% endif %}
                <th>Адресс</th>
                <th>Имя подписчика</th>

                {% if isPriveleged %}<th>Пользователь</th>{% endif %}

                <th>Группа рассылки</th>
                {% if isPriveleged %}
                    <th>Confirmed</th>
                    <th>Unsubcribe</th>
                {% endif %}
                <th>Статус</th>
                <th>Действие</th>
            </tr>
        </thead>
    {% endif %}
    <tbody>
        {% if not email.group.name %}
            <tr class="danger">
        {% else %}
            <tr>
        {% endif %}
        <td>{{ loop.index }}</td>

        {% if isPriveleged %}<td>{{ email.id }}</td>{% endif %}

        <td>{{ email.address|e }}</td>
        <td>{{ email.name|e }}</td>

        {% if isPriveleged %}<td>{{ email.user.name|e }}</td>{% endif %}


        <td>
            {% if email.group.name %}
            {{ email.group.name |e }}
            {% else %}
            <span><i class="fa fa-calendar-times-o"></i> Группа удалена</span>
            {% endif %}
        </td>


        {% if isPriveleged %}
            <td>{{ email.confirmed == 1 ? 'Yes' : 'No' }}</td>
            <td>{{ email.unsubscribe == 1 ? 'Yes' : 'No' }}</td>
        {% endif %}
        <td>
            {% if email.status == 1 %}
                <span class="glyphicon glyphicon-eye-open" style="color: green;"></span>
            {% else %}
                <span class="glyphicon glyphicon-eye-close" style="color: brown;"></span>
            {% endif %}
                {#{{ email.status == 1 ? 'Yes' : 'No'  }}#}
            </td>
        <td width="10%">
            {#{{ link_to("/mailings/edit/" ~ email.id, '<span class="glyphicon glyphicon-edit" title="Редактировать"></span>', 'class' : 'btn btn-link btn-xs') }}#}
            {{ link_to("/mailings/delete/" ~ email.id, '<span class="glyphicon glyphicon-remove" title="Удалить"></span>', 'class' : 'btn btn-link btn-xs') }}
        </td>
    </tr>
    </tbody>
    {% if loop.last and page.total_pages > 1 %}
        <tfoot>
    <tr>
        <td colspan="11" align="right">
            <ul class="pager">
                <li class="previous">{{ link_to("mailings/index?page=" ~ page.first, '&larr; First') }}</li>
                <li class="previous">{{ link_to("mailings/index?page=" ~ page.before, '<i class="icon-step-backward"></i> Previous', "class": "btn ") }}</li>
                {% if page.current > 9 %}
                <li class="previous">{{ link_to("mailings/index?page=" ~ (page.before-10), '<i class="icon-step-backward"></i> Previous 10', "class": "btn ") }}</li>
                {% endif %}

                {% if page.current > 9 or page.total_pages > 9 %}
                    {% for number in page.current..page.current+9 %}
                        {% if page.current === number %}
                            <li class="active">{{ link_to("mailings/index?page=" ~ number, number, 'class' : "active", "style" : "color: brown!important;") }}</li>
                        {% else %}
                            <li>{{ link_to("mailings/index?page=" ~ number, number) }}</li>
                        {% endif %}
                    {% endfor %}
                {% else %}
                    {% for number in page.first..page.total_pages %}
                        {% if page.current === number %}
                            <li class="active">{{ link_to("mailings/index?page=" ~ number, number, 'class' : "active", "style" : "color: brown!important;") }}</li>
                        {% else %}
                            <li>{{ link_to("mailings/index?page=" ~ number, number) }}</li>
                        {% endif %}
                    {% endfor %}
                {% endif %}

                <li class="next">{{ link_to("mailings/index?page=" ~ page.last, 'Last &rarr; ') }}</li>
                <li class="next">{{ link_to("mailings/index?page=" ~ page.next, '<i class="icon-step-forward"></i> Next', "class": "btn") }}</li>
            {% if page.current > 9 %}
            <li class="next">{{ link_to("mailings/index?page=" ~ (page.next+10), '<i class="icon-step-forward"></i> Next 10', "class": "btn") }}</li>
            {% endif %}
            </ul>
        </td>
    </tr>
    <tfoot>
    </table>
    {% endif %}
{#{% else %}#}
    {#No emails are recorded#}
{% endfor %}