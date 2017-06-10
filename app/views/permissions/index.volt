
{{ content() }}

<form method="post" class="form-inline">

<h2>Manage Permissions</h2>

<div class="well" align="center">

		<div class="form-group">
			{#<label for="profileId">Профиль</label>#}
			<div class="input-group">
				<div class="input-group-addon"><i class="fa fa-briefcase"></i></div>
				{{ select('profileId', profiles, 'using': ['id', 'name'], 'useEmpty': true, 'emptyText': 'Выберите профиль...', 'emptyValue': '', 'class': 'form-control') }}
				{#{{ submit_button('Искать', 'class': 'form-control btn btn-primary') }}#}
				<button type="submit" class="form-control btn btn-primary"><i class="fa fa-search"></i> Искать</button>
			</div>
		</div>

</div>

{% if request.isPost() and profile %}

{% for resource, actions in acl.getResources() %}

	<h3>{{ resource }}</h3>

	<table class="table table-bordered table-striped" align="center">
		<thead>
			<tr>
				<th width="5%"></th>
				<th>Description</th>
			</tr>
		</thead>
		<tbody>
			{% for action in actions %}
			<tr>
				<td align="center"><input type="checkbox" name="permissions[]" value="{{ resource ~ '.' ~ action }}"  {% if permissions[resource ~ '.' ~ action] is defined %} checked="checked" {% endif %}></td>
				<td>{{ acl.getActionDescription(action) ~ ' ' ~ resource }}</td>
			</tr>
			{% endfor %}
		</tbody>
	</table>

{% endfor %}

{% endif %}

</form>