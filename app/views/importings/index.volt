
<br>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            {{ link_to("/mailings", '&larr; К списку адресов', 'class' : 'btn btn-default', 'style' : "float: left;") }}

            {{ link_to("/importings/create", '<i class="glyphicon glyphicon-plus"></i> Импортровать', 'class' : 'btn btn-primary', 'style' : "float: right;") }}
        </div>
    </div>

    <h1>Список импортирования файлов <a href="javascript:void();" style="display: inline-block; float: right;" onclick="window.location.reload()" class="btn btn-md btn-success" title="Обновить"><i class="glyphicon glyphicon-refresh"></i></a></h1>
    <br>
</div>

{{ content() }}

{{ flashSession.output() }}


<div style="width: 80%; margin: 0 auto;">
{% for email in page.items %}
    {% if loop.first %}
    <div class="panel panel-default">
        {#<div class="panel-heading">Список файлов в обработке</div>#}
        {#<div class="panel-body">#}
            <table class="table table-striped table-hover mailings">
                <thead>
                    <tr>
                        <th>Задание</th>
                        <th>Группа</th>
                        <th title="Обработано E-mail адресов" style="cursor: help;">Всего / Из</th>
                        <th>Прогресс</th>
                        <th>Статус</th>
                        <th colspan="2">Действие</th>
                    </tr>
                </thead>
                <tbody>
    {% endif %}
                    <tr>
                        <td style="vertical-align: middle!important;">{{ email.getFilename(true)|e }}</td>
                        <td style="vertical-align: middle!important;">{{ email.group.name | e }}</td>
                        <td style="vertical-align: middle!important;">{{ number_format(email.current, 0, ".", " ") }} / {{ email.getTotals(true, 0, ".", " ") }}</td>
                        <td style="vertical-align: middle!important;">
                            <div class="progress" style="position: relative; height: 0.7em; top: 5px;;">
                                {#<span style="display: inline-block; position: absolute; font-size: 0.95em;width: 100%;line-height: 1.5em; text-align: center; color: {{ email.getPercent() < 65 ? 'black' : 'white' }}; text-shadow: 0px 0px 2px white, 0px 0px 2px {{ email.getPercent() < 65 ? 'white' : 'black' }};">{{ email.getPercent() }}%</span>#}
                                <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="{{ email.current }}" aria-valuemin="0" aria-valuemax="{{ email.totals }}" style="width: {{ email.getPercent() }}%;">
                                </div>
                            </div>
                        </td>
                        <td style="vertical-align: middle!important;">
                            {% if email.status == 0 %}
                                {{ 'В обработке...' }}
                            {% endif %}
                            {% if email.status == 1 %}
                                {{ 'Обработан' }}
                            {% endif %}
                            {% if email.status == 2 %}
                                {{ 'В очереди...' }}
                            {% endif %}
                            {% if email.status == 3 %}
                                {{ 'Отменён...' }}
                            {% endif %}
                        </td>
                        <td width="12%" style="vertical-align: middle!important;">

                        {#{% if email.status == 2 %}#}
                            {#{{ link_to("/importings/pause/" ~ email.id, '<span class="glyphicon glyphicon-pause" style="top: 3px;" title="Приостановить"></span>', "class": "info-danger" ) }}#}
                        {#{% endif %}#}
                        {% if email.status == 1 or email.status == 2 %}
                            {{ link_to("/importings/delete/" ~ email.id, '<span class="glyphicon glyphicon-remove" style="top: 3px;" title="Удалить"></span>', "class": "info-danger") }}
                        {% endif %}
                        </td>
                </tr>
    {% if loop.last and page.total_pages > 1 %}
        </tbody>
        <tbody>
            <tr>
                <td colspan="10" align="right">
                    <ul class="pager">
                        <li class="previous">{{ link_to("/importings/index", '&larr; First') }}</li>
                        <li class="previous">{{ link_to("/importings/index?page=" ~ page.before, '<i class="icon-step-backward"></i> Previous', "class": "btn ") }}</li>

                        {% if page.current > 9 or page.total_pages > 9 %}
                            {% for number in page.current..page.current+9 %}
                                {% if page.current === number %}
                                    <li class="active">{{ link_to("importings/index?page=" ~ number, number, 'class' : "active", "style" : "color: brown!important;") }}</li>
                                {% else %}
                                    <li>{{ link_to("queuing/index?page=" ~ number, number) }}</li>
                                {% endif %}
                            {% endfor %}
                        {% else  %}
                            {% for number in page.first..page.total_pages %}
                                {% if page.current === number %}
                                    <li class="active">{{ link_to("importings/index?page=" ~ number, number, 'class' : "active", "style" : "color: brown!important;") }}</li>
                                {% else %}
                                    <li>{{ link_to("importings/index?page=" ~ number, number) }}</li>
                                {% endif %}
                            {% endfor %}
                        {% endif %}

                        <li class="next">{{ link_to("/importings/index?page=" ~ page.last, 'Last &rarr; ') }}</li>
                        <li class="next">{{ link_to("/importings/index?page=" ~ page.next, '<i class="icon-step-forward"></i> Next', "class": "btn") }}</li>
                    </ul>
                </td>
            </tr>
        <tbody>
        </table>
        {#</div>#}
    </div>
    {% endif %}
        {#{% else %}#}
        {#<br><br><h1 style="color: red;">No list are recorded!</h1>#}
    {% endfor %}

</div>
