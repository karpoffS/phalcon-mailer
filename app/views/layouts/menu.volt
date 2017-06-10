<div class="container">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        {{ link_to('/', 'class': 'navbar-brand', '<img src="/img/logo.png" style="float: left;width: 48px; height: 48px; position: relative; top: -16px; display: inline-block;" />Mailer')}}
    </div>
    <div class="navbar-collapse collapse">

        {%- set menus = [
        'О нас': '/about'
        ] -%}

        <ul class="nav navbar-nav" style="width: 90%;">

            {%- for key, value in menus %}
                {% if value == router.getRewriteUri() %}
                    <li class="active">{{ link_to(value, key) }}</li>
                {% else %}
                    <li>{{ link_to(value, key) }}</li>
                {% endif %}
            {%- endfor -%}

            {% if not(logged_in is empty) and isPriveleged %}
                {% set iconTodo = '<i class="glyphicon glyphicon-screenshot"></i> ' %}
                {% if "todo" == dispatcher.getControllerName() %}
                    <li class="active">{{ link_to("todo", iconTodo ~ "Список задач") }}</li>
                {% else %}
                    <li>{{ link_to("todo", iconTodo ~ 'Список задач') }}</li>
                {% endif %}
            {% endif %}

            {% if not(logged_in is empty) %}

                {# для подменю администртору #}
                {%- set adminInstruments = [
                    '<i class="fa fa-ticket"></i> Приглашения': '/invitings',
                    '<i class="fa fa-users"></i> Пользователи': '/users',
                    '<i class="fa fa-briefcase"></i> Профили': '/profiles',
                    '<i class="fa fa-check-square-o"></i> Разрешения': '/permissions'
                ] -%}

                {# Для всех #}
                {%- set instruments = [
                    '<i class="glyphicon glyphicon-upload"></i> Импортирование списков': '/importings',
                    '<i class="glyphicon glyphicon-list-alt"></i> Список рассылки': '/groups',
                    '<i class="fa fa-book"></i> Список адресов': '/mailings',
                    '<i class="fa fa-envelope-o"></i> Категории шаблонов': '/categories',
                    '<i class="fa fa-envelope"></i> Список шаблонов': '/messages',
                    '<i class="fa fa-tasks"></i> Очереди заданий': '/queuing'
                ] -%}

                {# Настройки пользователя #}
                {%- set profile = [
                    'profile' : '<i class="fa fa-user"></i> ' ~ auth.getName(),
                    '/users/changePassword' : '<i class="fa fa-unlock-alt"></i> ' ~ 'Изменить пароль'
                ] -%}

                <li style="float: right;">{{ link_to('session/logout', '<span class="glyphicon glyphicon-log-out"></span> Выход') }}</li>

                <li style="float: right;" class="dropdown">
                    <ul class="dropdown-menu">

                        {% if isPriveleged %}
                            <li class="divider"></li>
                            <li class="dropdown-header">Администрирование</li>
                            {% for name, url in adminInstruments %}
                                {% if contains_text(router.getRewriteUri(), url ) is numeric %}
                                    <li class="active">{{ link_to(url, name) }}</li>
                                {% else %}
                                    <li>{{ link_to(url, name) }}</li>
                                {% endif %}
                            {%- endfor -%}
                            <li class="divider"></li>
                        {% endif %}

                        {#Перебираем массив меню#}
                        {% for name, url in instruments %}
                            {% if contains_text(router.getRewriteUri(), url ) is numeric %}
                                <li class="active">{{ link_to(url, name) }}</li>
                            {% else %}
                                <li>{{ link_to(url, name) }}</li>
                            {% endif %}
                        {%- endfor -%}

                        <li class="divider"></li>
                        <li class="dropdown-header">Профиль пользователя</li>
                        {%- for url , value in profile %}
                            {% if url == router.getRewriteUri() %}
                                <li class="active">{{ link_to(url, value) }}</li>
                            {% else %}
                                <li>{{ link_to(url, value) }}</li>
                            {% endif %}
                        {%- endfor -%}
                    </ul>
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-cog"></i>
                        Инструменты
                        <b class="caret"></b>
                    </a>
                </li>
            {% else %}

                <li style="float: right;">{{ link_to('session/login', '<span class="glyphicon glyphicon-log-in"></span> Вход') }}</li>
                <li style="float: right;">{{ link_to('session/signup', '<i class="glyphicon glyphicon-ok"></i>&nbsp;&nbsp;Регистрация') }}</li>
            {% endif %}
        </ul>
    </div><!--/.navbar-collapse -->
</div>
