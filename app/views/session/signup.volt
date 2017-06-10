{{ content() }}

<div align="well bs-component" style="width: 60%; margin: 0 auto;">

{{ form('class': 'form-horizontal') }}

	<fieldset>
		<legend>Регистрация</legend>

		<div class="form-group">
			{{ form.label('name', ['class' : "col-sm-3 control-label"]) }}

			<div class="col-lg-8">
				<div class="input-group">
					<div class="input-group-addon"><i class="fa fa-user"></i></div>
					{{ form.render('name', [ 'class' : 'form-control']) }}
				</div>
				{{ form.messages('name') }}
			</div>
		</div>

		<div class="form-group">
			{{ form.label('email', ['class' : "col-sm-3 control-label"]) }}
			<div class="col-lg-8">
				<div class="input-group">
					<div class="input-group-addon"><i class="fa fa-at"></i></div>
					{{ form.render('email', [ 'class' : 'form-control']) }}
				</div>
				{{ form.messages('email') }}
			</div>
		</div>

		<div class="form-group">
			{{ form.label('password', ['class' : "col-sm-3 control-label"]) }}
			<div class="col-lg-8">
				<div class="input-group">
					<div class="input-group-addon"><i style="font-size: 1.2em" class="fa fa-lock"></i></div>
					{{ form.render('password', [ 'class' : 'form-control']) }}
				</div>
				{{ form.messages('password') }}
			</div>
		</div>

		<div class="form-group">
			{{ form.label('invitationCode', ['class' : "col-sm-3 control-label"]) }}
			<div class="col-lg-8">
				<div class="input-group">
					<div class="input-group-addon"><i class="fa fa-ticket"></i></div>
					{{ form.render('invitationCode', [ 'class' : 'form-control']) }}
				</div>
				{{ form.messages('invitationCode') }}
			</div>
		</div>

		<div class="form-group">
			<div class="col-lg-8 col-lg-offset-3">
				<div class="checkbox">
					<label style="text-align: left;">
						{{ form.render('terms') }}{{ form.label('terms') }}
						{{ form.messages('terms') }}
					</label>
				</div>
				<br>
				{{ form.render('Sign Up', [ 'style' : 'width: 100%']) }}
			</div>
		</div>

		{{ form.render('csrf', ['value': security.getToken()]) }}
		{{ form.messages('csrf') }}
	</fieldset>
</form>

</div>