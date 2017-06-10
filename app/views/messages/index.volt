
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            {{ link_to("/messages/create", '<i class="glyphicon glyphicon-plus"></i> Создать', 'class' : 'btn btn-primary', 'style' : "float: right;") }}
        </div>
    </div>
</div>

<h1>Список шаблонов сообщений</h1>

<br>
{% if page.items %}
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped table-hover">
                    {% for message in page.items %}
                        {% if loop.first %}
                            <thead>
                            <tr>
                                <th>№</th>

                                {% if isPriveleged %}<th>Id</th>{% endif %}

                                <th>Тема</th>
                                <th>Категория</th>
                                {% if isPriveleged %}<th>Пользователь</th>{% endif %}
                                <th>Дата создания</th>
                                <th>Дата обновления</th>
                                <th>Состояние</th>
                                <th>Действия</th>
                            </tr>
                            </thead>
                        {% endif %}
                        <tbody>
                        <tr>
                            <td>{{ loop.index }}</td>

                            {% if isPriveleged %}<td>{{ message.id }}</td>{% endif %}

                            <td>{{ message.subject | e }}</td>

                            {% for category in categories %}
                                {% if category.id == message.categoryId %}
                                    <td>{{ category.name|e }}</td>
                                {% endif %}
                            {% endfor %}

                            {% for user in users if isPriveleged %}
                                {% if user.id == message.userId %}
                                    <td>{{ user.name|e }}</td>
                                {% endif %}
                            {% endfor %}

                            <td>{{ date("Y/m/d H:m:s", message.createdAt) }}</td>
                            <td>{{ date("Y/m/d H:m:s", message.modifyAt) }}</td>
                            <td>
                                {% if message.status == 1 %}
                                    <span style="color: #00ff00;" class="glyphicon glyphicon-eye-open" title="Опубликовано"></span>
                                {% else %}
                                    <span class="glyphicon glyphicon-eye-close" title="Не опубликовано"></span>
                                {% endif %}
                            </td>
                            <td>
                                {{ link_to("/messages/edit/" ~ message.id, '<span class="glyphicon glyphicon-edit" title="Редактировать"></span>', 'class' : 'btn btn-link btn-xs') }}

                                {{ link_to("/messages/delete/" ~ message.id, '<span class="glyphicon glyphicon-remove" title="Удалить"></span>', 'class' : 'btn btn-link btn-xs') }}
                            </td>
                        </tr>
                        </tbody>

                        {%if loop.last  and page.total_pages > 1 %}
                            <tfoot>
                            <tr>
                                <td colspan="5">
                                    <ul class="pager">
                                        <li class="previous">{{ link_to("messages", '&larr; First') }}</li>
                                        <li class="previous">{{ link_to("messages?page=" ~ page.before, '<i class="icon-step-backward"></i> Previous', "class": "btn ") }}</li>
                                        {% if page.current > 9 or page.total_pages > 9 %}
                                            {% for number in page.current..page.current+9 %}
                                                {% if page.current === number %}
                                                    <li class="active">{{ link_to("messages?page=" ~ number, number, 'class' : "active", "style" : "color: brown!important;") }}</li>
                                                {% else %}
                                                    <li>{{ link_to("messages?page=" ~ number, number) }}</li>
                                                {% endif %}
                                            {% endfor %}
                                        {% else %}
                                            {% for number in page.first..page.total_pages %}
                                                {% if page.current === number %}
                                                    <li class="active">{{ link_to("messages?page=" ~ number, number, 'class' : "active", "style" : "color: brown!important;") }}</li>
                                                {% else %}
                                                    <li>{{ link_to("messages?page=" ~ number, number) }}</li>
                                                {% endif %}
                                            {% endfor %}
                                        {% endif %}

                                        <li class="next">{{ link_to("messages?page=" ~ page.last, 'Last &rarr; ') }}</li>
                                        <li class="next">{{ link_to("messages?page=" ~ page.next, '<i class="icon-step-forward"></i> Next', "class": "btn") }}</li>
                                    </ul>
                                </td>
                            </tr>
                            </tfoot>
                        {%endif %}
                    {%endfor%}
                </table>
            </div>
        </div>
    </div>
{% endif %}

{{ content() }}


