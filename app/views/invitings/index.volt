
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            {{ link_to("/invitings/create", '<i class="glyphicon glyphicon-plus"></i> Создать', 'class' : 'btn btn-warning', 'style' : "float: right;") }}
        </div>
    </div>
</div>

<h1>Список приглашений {{ link_to("/invitings/mailing", '<i class="fa fa-share-square-o"></i> Рассылка приглашений', 'class' : 'btn btn-info btn-md', 'style' : "float: right;") }}</h1>

<br>
{% if page.items %}
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped table-hover">
                    {% for invite in page.items %}
                        {% if loop.first %}
                            <thead>
                            <tr>
                                <th>№</th>
                                <th>Адресс приглашонного</th>
                                <th>Код приглашения</th>
                                <th>Пользователь</th>
                                <th>Дата создания</th>
                                <th>Дата прибытия</th>
                                <th>Состояние</th>
                                <th>Действия</th>
                            </tr>
                            </thead>
                        {% endif %}
                        <tbody>
                        <tr class="{{ invite.status == 1 ? "success" : "" }}">
                            <td>{{ loop.index }}</td>
                            <td>{{ invite.email }}</td>
                            <td>{{ invite.code }}</td>
                            <td>{{ invite.userId > 0 ? invite.user.name | e : "Не определён!" }}</td>
                            <td>{{ date("H:i:s d/m/Y", invite.createAt ) }}</td>
                            <td>{{ invite.modifyAt > 0 ? date("H:i:s d/m/Y", invite.modifyAt ) : "Ещё не пришёл" }}</td>
                            <td>{{ invite.status == 1 ? "Активирован" : "Не активирован" }}</td>
                            <td>
                                {{ link_to("/invitings/delete/" ~ invite.id, '<span class="glyphicon glyphicon-remove" title="Удалить"></span>', 'class' : 'btn btn-link btn-xs') }}
                            </td>
                        </tr>
                        </tbody>

                        {%if loop.last  and page.total_pages > 1 %}
                            <tfoot>
                            <tr>
                                <td colspan="6">
                                    <ul class="pager">
                                        <li class="previous">{{ link_to("/invitings", '&larr; First') }}</li>
                                        <li class="previous">{{ link_to("/invitings?page=" ~ page.before, '<i class="icon-step-backward"></i> Previous', "class": "btn ") }}</li>
                                        {% for i in page.current..page.total_pages %}
                                            {% if page.current === i %}
                                                <li class="active">{{ link_to("/invitings?page=" ~ i, i, "class": "btn ") }}</li>
                                            {% else %}
                                                <li>{{ link_to("/invitings?page=" ~ i, i, "class": "btn ") }}</a></li>
                                            {% endif %}
                                        {% endfor %}

                                        <li class="next">{{ link_to("/invitings?page=" ~ page.last, 'Last &rarr; ') }}</li>
                                        <li class="next">{{ link_to("/invitings?page=" ~ page.next, '<i class="icon-step-forward"></i> Next', "class": "btn") }}</li>
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


