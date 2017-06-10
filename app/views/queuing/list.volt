<style type="text/css">
    .ellipsis {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>

<ul class="pager">
    <li class="previous pull-left">
        {{ link_to("/queuing", "&larr; К списку очередей") }}
    </li>

    <li class="pull-right">
        <a href="javascript:window.location.reload();"><i class="glyphicon glyphicon-refresh"></i> Обновить</a>
    </li>
</ul>
<div class="container-fluid">
    <h1>Список задач</h1>
    <br>

    <form class="form-inline" method="post">
        <div class="form-group">
            <div class="input-group">
                <div class="input-group-addon"><i class="fa fa-user"></i></div>
                <select name="userId" id="userId" class="form-control">
                    <option value="">Выберите пользователя</option>
                    {{ dump(filter) }}
                    {% if users %}
                        {% for option in users %}
                            <option value="{{ option.id }}">{{ option.name }}</option>
                        {% endfor %}
                    {% endif %}
                </select>
            </div>
        </div>

        <div class="form-group">
            <div class="input-group">
                <div class="input-group-addon"><i class="fa fa-tasks"></i></div>
                <select name="queueId" id="queueId" class="form-control">
                    <option value="">Выберите очередь</option>
                    {{ dump(filter) }}
                    {% if filter %}
                        {% for option in filter %}
                            <option value="{{ option.id }}"> {{ option.group.name }} - {{ option.category.name }}</option>
                        {% endfor %}
                    {% endif %}
                </select>
            </div>
        </div>

        <div class="form-group">
            <input type="submit" value="Фильтровать" class="btn btn-primary form-control" />
        </div>
    </form>

    <br>
    {{ content() }}
    <br>

    {% if page.items %}
        <div class="row">
            <div class="col-md-12">
                {#<table class="table table-striped table-hover">#}
                <table class="table table-hover">
                    {% for item in page.items %}
                        {% if loop.first %}
                            <thead>
                                <tr>
                                    <th>№</th>
                                    <th>Очередь</th>
                                    <th>Адресс</th>
                                    <th style="min-width: 100px;">Список</th>
                                    <th style="min-width: 120px;">Дата создания</th>
                                    <th style="min-width: 120px;">Дата отправки</th>
                                    <th>Состояние</th>
                                    <th>Ошибки</th>
                                </tr>
                            </thead>
                        {% endif %}
                        <tbody>
                        <tr{{ item.errors !== "" ? 'class="errors"' : '' }}>
                            <td>{{ loop.index }}</td>
                            <td><span style="display: inline-block;">{{ option.category.name }}</span> / <span style="display: inline-block; max-width: 180px; " class="ellipsis" title="{{ option.message.subject}}">{{ option.message.subject}}</span></td>
                            <td>{{ item.email.address }}</td>
                            <td>{{ item.group.name }}</td>
                            <td>{{ date("H:i:s d/m/Y ", item.createAt) }}</td>
                            <td>{{ item.modifyAt > 0 ? date("H:i:s d/m/Y", item.modifyAt ) : "Ещё не обработан" }}</td>
                            <td>
                                {% if item.status == 2 %}Ошибка!{% endif %}

                                {% if item.status == 1 %}Отправлено{% endif %}

                                {% if item.status == 0 and item.lock == 1 %}В обработке...{% endif %}
                            </td>
                            <td style="min-width: 250px; max-width: 300px;" class="ellipsis" title="{{ item.errors }}">{{ item.errors }}</td>
                        </tr>
                        </tbody>

                        {%if loop.last  and page.total_pages > 1 %}
                            <tfoot>
                                <tr style="background-color: rgba(221, 221, 183, 0.25);">
                                    <td colspan="10">
                                        <ul class="pager">
                                            <li class="previous">{{ link_to("queuing/list?page=" ~ page.first, '&larr; First') }}</li>
                                            <li class="previous">{{ link_to("queuing/list?page=" ~ page.before, '<i class="icon-step-backward"></i> Previous', "class": "btn ") }}</li>
                                            {% if page.current > 9 or page.total_pages > 9 %}
                                                <li class="previous">{{ link_to("queuing/list?page=" ~ (page.before-10), '<i class="icon-step-backward"></i> Previous 10', "class": "btn ") }}</li>
                                            {% endif %}

                                            {% if page.current > 9 or page.total_pages > 9 %}
                                                {% if page.current == page.total_pages or page.current+9 >= page.total_pages %}
                                                    {% for number in page.current..page.total_pages %}
                                                        {% if page.current === number %}
                                                            <li class="active">{{ link_to("queuing/list?page=" ~ number, number, 'class' : "active", "style" : "color: brown!important;") }}</li>
                                                        {% else %}
                                                            <li>{{ link_to("queuing/list?page=" ~ number, number) }}</li>
                                                        {% endif %}
                                                    {% endfor %}
                                                {% else %}
                                                    {% for number in page.current..page.current+9 %}
                                                        {% if page.current === number %}
                                                            <li class="active">{{ link_to("queuing/list?page=" ~ number, number, 'class' : "active", "style" : "color: brown!important;") }}</li>
                                                        {% else %}
                                                            <li>{{ link_to("queuing/list?page=" ~ number, number) }}</li>
                                                        {% endif %}
                                                    {% endfor %}
                                                {% endif %}
                                            {% else %}
                                                {% for number in page.first..page.total_pages %}
                                                    {% if page.current === number %}
                                                        <li class="active">{{ link_to("queuing/list?page=" ~ number, number, 'class' : "active", "style" : "color: brown!important;") }}</li>
                                                    {% else %}
                                                        <li>{{ link_to("queuing/list?page=" ~ number, number) }}</li>
                                                    {% endif %}
                                                {% endfor %}
                                            {% endif %}

                                            <li class="next">{{ link_to("queuing/list?page=" ~ page.last, 'Last &rarr; ') }}</li>
                                            <li class="next">{{ link_to("queuing/list?page=" ~ page.next, '<i class="icon-step-forward"></i> Next', "class": "btn") }}</li>
                                            {% if page.current > 9 or page.total_pages > 9 %}
                                                <li class="next">{{ link_to("queuing/list?page=" ~ (page.next+10), '<i class="icon-step-forward"></i> Next 10', "class": "btn") }}</li>
                                            {% endif %}
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


