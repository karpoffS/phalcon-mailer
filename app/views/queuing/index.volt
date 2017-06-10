<style type="text/css">
    .ellipsis {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            {{ link_to("/queuing/list", '<i class="fa fa-tasks"></i> Список задач', 'class' : 'btn btn-primary', 'style' : "float: left;") }}
            {{ link_to("/queuing/create", '<i class="glyphicon glyphicon-plus"></i> Создать', 'class' : 'btn btn-primary', 'style' : "float: right;") }}
        </div>
    </div>
</div>


<div class="container-fluid">
    <h1>Список очередей <a href="javascript:void();" style="display: inline-block; float: right;" onclick="window.location.reload()" class="btn btn-md btn-success" title="Обновить"><i class="glyphicon glyphicon-refresh"></i></a></h1>
    <br>

    {{ content() }}
    {{ flashSession.output() }}

    {% if page.items %}
        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped table-hover">
                    {% for item in page.items %}
                        {% if loop.first %}
                            <thead>
                            <tr>
                                <th>№</th>
                                <th>Cообщение / Категория </th>
                                <th>Список адресов</th>
                                <th>Прогресс</th>
                                <th>Действие</th>
                            </tr>
                            </thead>
                        {% endif %}
                        <tbody>
                        {# 0 - приостановлена, 1 - выполняется, 2 - выполнена #}
                        {% if item.status == 0 or item.status == 2 %}{% endif %}
                        <tr>
                            <td>{{ loop.index }}</td>
                            <td><span style="display: inline-block;">{{ item.category.name }}</span> / <span style="display: inline-block; max-width: 180px; " class="ellipsis" title="{{ item.message.subject}}">{{ item.message.subject}}</span></td>
                            <td>{{ item.group.name }} ({{ item.totals }})</td>
                            <td>
                                {% if item.status == 2 %}
                                <div class="progress" title="Выполнено {{ item.getPercent() }}% из 100% ошибок {{ 100 - item.getPercent() }}%">
                                {% else %}
                                <div class="progress">
                                {% endif %}
                                    <div class="progress-bar progress-bar-success" style="width: {{ item.getPercent() }}%; text-align: center; color: {{ item.getPercent() < 25 ? 'black; ' : 'white;' }}">
                                        {{ item.getPercent() }}%
                                    </div>
                                    {% if item.status == 2 %}
                                    <div class="progress-bar progress-bar-danger " style="width: {{ 100 - item.getPercent() }}%">
                                        <span class="sr-only">{{ 100 - item.getPercent(false) }}%</span>
                                    </div>
                                    {% endif %}
                                </div>
                            </td>
                            <td>
                                {% if item.status == 0 %}
                                {{ link_to("/queuing/play/" ~ item.id, '<span class="glyphicon glyphicon-play"></span>', 'class' : 'btn btn-link btn-xs', 'title' : "Возобновть") }}
                                {% endif %}

                                {#{% if item.status == 1 %}#}
                                {#{{ link_to("/queuing/pause/" ~ item.id, '<i class="fa fa-pause"></i>', 'class' : 'btn btn-link btn-xs', 'title' : "Приостановть") }}#}
                                {#{% endif %}#}

                                {% if item.status == 0 or item.status == 2 %}
                                    {{ link_to("/queuing/delete/" ~ item.id, '<span class="glyphicon glyphicon-remove" title="Удалить"></span>', 'class' : 'btn btn-link btn-xs') }}
                                {% endif %}
                            </td>
                        </tr>
                        </tbody>

                        {%if loop.last  and page.total_pages > 1 %}
                            <tfoot>
                                <tr>
                                    <td colspan="5">
                                        <ul class="pager">
                                            <li class="previous">{{ link_to("queuing", '&larr; First') }}</li>
                                            <li class="previous">{{ link_to("queuing?page=" ~ page.before, '<i class="icon-step-backward"></i> Previous', "class": "btn ") }}</li>
                                            {% for i in page.current..page.total_pages %}
                                                {% if page.current === i %}
                                                    <li class="active"><a href="#">{{ i }}</a></li>
                                                {% else %}
                                                    <li><a href="#">{{ i }}</a></li>
                                                {% endif %}
                                            {% endfor %}

                                            <li class="next">{{ link_to("queuing?page=" ~ page.last, 'Last &rarr; ') }}</li>
                                            <li class="next">{{ link_to("queuing?page=" ~ page.next, '<i class="icon-step-forward"></i> Next', "class": "btn") }}</li>
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


