(function(window, document) {
    "use strict";

    var $manage_body = $("#manage-body");
    var json_url = SITE_ROOT + "json/manage.php";

    // role variables
    var $role_edit_value, $role_edit_btn, $role_delete_btn, selected_role;

    function manageFormSubmit(form_identifier, callback_success) {
        onFormSubmit(form_identifier, callback_success, $manage_body, json_url);
    }

    // left panel item clicked
    $('a.manage-list').click(function() {
        History.pushState(null, '', this.href);
        var view = getUrlVars(this.href)['view'];
        loadContentWithAjax("#manage-body", SITE_ROOT + 'manage-panel.php', {view: view});

        return false;
    });

    // role clicked
    $manage_body.on("click", "#manage-roles-roles button", function() {
        var $this = $(this);
        var $siblings = $this.siblings();

        // mark as active
        $this.addClass("active");

        // remove mark from others
        $siblings.removeClass("active");

        // update form role
        selected_role = $this.text();
        $("#manage-roles-permission-role").val(selected_role);

        // update toolbox values
        $role_edit_value = $("#manage-roles-edit-value");
        $role_edit_btn = $("#manage-roles-edit-btn");
        $role_delete_btn = $("#manage-roles-delete-btn");

        $role_edit_value.prop("disabled", false).val(selected_role);
        $role_edit_btn.removeClass("disabled");
        $role_delete_btn.removeClass("disabled");


        // update role checkboxes
        $.post(json_url, {action: "get-role", role: selected_role}, function(data) {
            var jData = parseJSON(data);
            if (jData.hasOwnProperty("error")) {
                growlError(jData["error"]);
            }
            if (jData.hasOwnProperty("success")) {
                var permissions = jData["permissions"];
                var $checkboxes = $(".manage-roles-permission-checkbox");
                console.log(permissions);

                // update permissions checkboxes
                $checkboxes.each(function() {
                    this.checked = false; // uncheck

                    // role has permissions
                    if (_.contains(permissions, this.value)) {
                        this.checked = true;
                    }
                });
            }
        });
    });

    // role add clicked
    $manage_body.on("click", "#manage-roles-add-btn", function() {

    });

    // role edit clicked
    $manage_body.on("click", "#manage-roles-edit-btn", function() {

    });

    // role delete clicked
    $manage_body.on("click", "#manage-roles-delete-btn", function() {

    });

    // role permission submitted
    manageFormSubmit("#manage-roles-permission-form", function(data) {
        var jData = parseJSON(data);
        if (jData.hasOwnProperty("error")) {
            growlError(jData["error"]);
        }
        if (jData.hasOwnProperty("success")) {
            growlSuccess(jData["success"]);
        }
    });

})(window, document);