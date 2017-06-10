{{ content() }}

<div align="right">
    {{ link_to("users/create", "<i class='icon-plus-sign'></i> Create Users", "class": "btn btn-primary") }}
</div>

<form method="post" action="{{ url("users/search") }}" class="form-horizontal" autocomplete="off">

    <fieldset>
        <legend><h2>Search users</h2></legend>
        <div class="form-group">
            <label for="id" class="col-lg-2 control-label">Id</label>
            <div class="col-lg-4">
                {{ form.render("id", ['class' : "form-control"] ) }}
            </div>
        </div>
        <div class="form-group">
            <label for="name" class="col-lg-2 control-label">Name</label>
            <div class="col-lg-4">
                {{ form.render("name", ['class' : "form-control"] ) }}
            </div>
        </div>
         <div class="form-group">
            <label for="email" class="col-lg-2 control-label">E-Mail</label>
            <div class="col-lg-4">
                {{ form.render("email", ['class' : "form-control"] ) }}
            </div>
        </div>
         <div class="form-group">
            <label for="profilesId" class="col-lg-2 control-label">Profile</label>
            <div class="col-lg-4">
                {{ form.render("profilesId", ['class' : "form-control"] ) }}
            </div>
        </div>
        <div class="form-group">
            <div class="col-lg-10 col-lg-offset-2">
                <button type="reset" class="btn btn-default">Cancel</button>
                {{ submit_button("Search", "class": "btn btn-primary") }}
            </div>
        </div>
    </fieldset>
</form>