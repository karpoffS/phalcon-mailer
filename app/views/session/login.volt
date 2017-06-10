
<script>

	function checkShowPasswd(){
		if($(this).val() == 1){
			$("#password").attr("type","text");
			$(this).val(0);
		} else {
			$("#password").attr("type","password");
			$(this).val(1);
		}
	}

	$(document).ready(function(){
		$("#showpassword").load(checkShowPasswd).click(checkShowPasswd);
	});
</script>

<div align="well bs-component" style="width: 50%; margin: 0 auto;">

	{{ form('class': 'form-horizontal') }}

	<fieldset>
		<legend>
			Вход
		</legend>

		<div class="form-group">
			{{ form.label('email', ['class' : "col-sm-3 control-label"]) }}
			<div class="input-group col-lg-6">
				<span class="input-group-addon">
					<i class="fa fa-envelope" style="width: 14px;"></i>
			  	</span>
				{{ form.render('email', [ 'class' : 'form-control']) }}
			</div>
			{{ form.messages('email') }}
		</div>

		<div class="form-group">
			{{ form.label('password', ['class' : "col-sm-3 control-label"]) }}
			<div class="input-group col-lg-6">
				<span class="input-group-addon">
					<i class="fa fa-lock" style="width: 14px;"></i>
			  	</span>
				{{ form.render('password', [ 'class' : 'form-control']) }}
				{{ form.messages('password') }}
			</div>
		</div>

		<div class="form-group">
			<div class="col-lg-6 col-lg-offset-3">
				<div class="checkbox">
					<label>
						{{ form.render('remember') }}
						{{ form.label('remember') }}

					</label>
					<label style="margin-top: 10px;">
						{{ form.render('showpassword') }}
						{{ form.label('showpassword') }}
					</label>
				</div>
				<br>
				{{ form.render('go', [ 'style' : 'width: 100%']) }}
			</div>
		</div>

		<div class="form-group">
			<div class="col-lg-8 col-lg-offset-3">
				<hr>
				<div class="forgot">
					{{ link_to("session/forgotPassword", "Забыли пароль?") }}
				</div>
			</div>
		</div>

		{{ form.render('csrf', ['value': security.getToken()]) }}
		{{ form.messages('csrf') }}

	</fieldset>
	</form>

	{{ content() }}

</div>