
<div class="container">
    <div class="col-lg-6 col-lg-offset-3">
        <form method="post" class="form-horizontal" autocomplete="off" action="{{ url("users/changePassword") }}">

            <fieldset>
                <legend>Смена пароля</legend>

                <div class="form-group">
                    <label for="password" class="col-sm-4 control-label">Пароль</label>
                    <div class="col-lg-6">
                        {{ form.render('password', [ 'class' : 'form-control']) }}
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="col-sm-4 control-label">Подтвердить пароль</label>
                    <div class="col-lg-6">
                        {{ form.render('confirmPassword', [ 'class' : 'form-control']) }}
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-lg-6 col-lg-offset-4" style="text-align: center;">
                        <button type="reset" class="btn btn-default">Сбрость ввод</button>
                        {{ submit_button("Сменить пароль", "class": "btn btn-primary") }}
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</div>

{{ content() }}

