{{ content() }}

<div align="right">
    {{ link_to("profiles/create", "<i class='icon-plus-sign'></i> Create Profiles", "class": "btn btn-primary") }}
</div>

<form method="post" class="form-inline" action="{{ url("profiles/search") }}" autocomplete="off">

    <div class="center scaffold">

        <h2>Search profiles</h2>

        <div class="form-group">
            <label for="name">Name</label>
            {{ form.render("name", [ 'class': 'form-control' ]) }}
        </div>
        {{ submit_button("Search", "class": "btn btn-primary") }}


    </div>

</form>