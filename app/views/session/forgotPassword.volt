
<div class="well bs-component" style="width: 50%; margin: 0 auto;">

	{{ form('class': 'form-inline') }}
		<fieldset>
			<legend>Забыли Пароль?</legend>
			<div class="form-group">
					{{ form.render('email', [ 'class' : 'form-control']) }}
					{#{{ form.messages('email') }}#}
			</div>
			<button type="submit" class="btn btn-primary">Отправить</button>
			{#{{ form.render('Send') }}#}
		</fieldset>
	</form>
	<br>

	{{ content() }}

</div>