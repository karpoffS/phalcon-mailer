
{{ content() }}

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            {{ link_to("/categories/create", '<i class="glyphicon glyphicon-plus"></i> Создать', 'class' : 'btn btn-primary', 'style' : "float: right;") }}
        </div>
    </div>
</div>


<div class="container-fluid">
    <h1>Категории сообщений</h1>
    <br>
    {% if page.items %}
        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped table-hover">
                    {% for group in page.items %}
                        {% if loop.first %}
                            <thead>
                            <tr>
                                <th>№</th>
                                {% if isPriveleged %}<th>Id</th>{% endif %}
                                <th>Name</th>
                                <th>Description</th>
                                {% if isPriveleged %}<th>User</th>{% endif %}
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                        {% endif %}
                        <tbody>
                        <tr>
                            <td>{{ loop.index }}</td>
                            {% if isPriveleged %}<td>{{ group.id }}</td>{% endif %}
                            <td>{{ group.name | e }}</td>
                            <td>{{ group.description | e }}</td>
                            {% for user in users if isPriveleged %}
                                {% if user.id == group.userId %}
                                    <td>{{ user.name|e }}</td>
                                {% endif %}
                            {% endfor %}
                            <td>
                                {% if group.status == 1 %}
                                    <span style="color: #00ff00;" class="glyphicon glyphicon-eye-open"></span>
                                {% else %}
                                    <span class="glyphicon glyphicon-eye-close"></span>
                                {% endif %}
                            </td>
                            <td>
                                {{ link_to("/categories/edit/" ~ group.id, '<span class="glyphicon glyphicon-edit" title="Редактировать"></span>', 'class' : 'btn btn-link btn-xs') }}

                                {{ link_to("/categories/delete/" ~ group.id, '<span class="glyphicon glyphicon-remove" title="Удалить"></span>', 'class' : 'btn btn-link btn-xs') }}
                            </td>
                        </tr>
                        </tbody>

                        {%if loop.last  and page.total_pages > 1 %}
                            <tfoot>
                                <tr>
                                    <td colspan="5">
                                        <ul class="pager">
                                            <li class="previous">{{ link_to("categories", '&larr; First') }}</li>
                                            <li class="previous">{{ link_to("categories?page=" ~ page.before, '<i class="icon-step-backward"></i> Previous', "class": "btn ") }}</li>
                                            {% for i in page.current..page.total_pages %}
                                                {% if page.current === i %}
                                                    <li class="active"><a href="#">{{ i }}</a></li>
                                                {% else %}
                                                    <li><a href="#">{{ i }}</a></li>
                                                {% endif %}
                                            {% endfor %}

                                            <li class="next">{{ link_to("categories?page=" ~ page.last, 'Last &rarr; ') }}</li>
                                            <li class="next">{{ link_to("categories?page=" ~ page.next, '<i class="icon-step-forward"></i> Next', "class": "btn") }}</li>
                                        </ul>
                                    </td>
                                </tr>
                            </tfoot>
                        {%endif %}
                    {%endfor%}
                </table>
            </div>
        </div>
    {% endif %}
</div>


