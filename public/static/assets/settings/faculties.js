$(function() {

    $(document).on('click', '#btn-store-faculty', function(e) {
        var btn = $(this);
        var form = btn.parents('form');
        var url = form.attr('action');
        var token = $("#csrf-token").val();
        var titleField = form.find("#title");
        var universityField = form.find("#university");
        var abbreviationField = form.find("#abbreviation");

        $.ajax({
            type: 'post',
            url: url,
            dataType: "json",
            data: {
                _token: token,
                title: titleField.val(),
                university: universityField.val(),
                abbreviation: abbreviationField.val()
            },
            success:function(data){

                var el = $("#status-add-faculty");
                var msgField = el.find("#status-add-faculty-message");

                if (data.status == "success") {
                    msgField.html(data.message);
                    el.addClass('alert-success');

                    $("#supported-faculties-table").append(data.data.view);

                    titleField.val("");
                    universityField.val("");
                    abbreviationField.val("");

                } else {
                    el.addClass('alert-danger');

                    var msg = "<p><strong>" + data.message + "</strong></p><ul>";

                    $.each(data.data.errors, function (i, message) {
                        msg += "<li>" + message + "</li>";
                    });

                    msg += "</ul>";

                    msgField.html(msg);
                }

                el.show();

            }
        });

        e.preventDefault();
        return false;
    });

    $(document).on('click', '.btn-change-status-faculty', function(e) {
        var url = $(this).attr("href");
        var el = $("#action-status");
        var token = $("#csrf-token").val();
        var row = $(this).parents("tr");

        $.ajax({
            type: 'post',
            url: url,
            data: {
                _token: token
            },
            success: function (data) {

                if (data.status == "success") {
                    el.find("#action-status-message").html(data.message);
                    el.addClass("alert-success");

                    if (data.data.enabled) {
                        row.find(".btn-enable-faculty").hide();
                        row.find(".btn-disable-faculty").show();
                        row.removeClass("text-muted");
                    } else {
                        row.find(".btn-enable-faculty").show();
                        row.find(".btn-disable-faculty").hide();
                        row.addClass("text-muted");
                    }

                    el.show();
                }

            }
        });

        e.preventDefault();
        return false;
    });

    $(document).on('click', '.btn-delete-faculty', function(e) {
        var url = $(this).attr("href");
        var el = $("#action-status");
        var token = $("#csrf-token").val();
        var row = $(this).parents("tr");

        var confirmed = confirm('Are you sure?');

        if (! confirmed) {
            return;
        }

        $.ajax({
            type: 'delete',
            url: url,
            data: {
                _token: token
            },
            success: function (data) {

                el.removeClass("alert-danger");
                el.removeClass("alert-success");

                if (data.status == "success") {
                    el.find("#action-status-message").html(data.message);
                    el.addClass("alert-success");
                    row.hide();
                } else {
                    el.find("#action-status-message").html("<strong>Error:</strong> " + data.message);
                    el.addClass("alert-danger");
                }

                el.show();

            }
        });

        e.preventDefault();
        return false;
    });

    var editingId = null;

    $(document).on("click", ".btn-edit-faculty", function(e) {

        var url = $(this).attr("href");
        var modal = $("#modal-edit-faculty");
        editingId = $(this).parents("tr").data('id');

        $.ajax({
            type: 'get',
            url: url,
            success: function (data) {
                modal.html(data);
                modal.modal('show');

            }
        });

        e.preventDefault();
        return false;
    });

    $(document).on("click", ".btn-update-faculty", function(e) {

        var modal = $("#modal-edit-faculty");
        var modalBody = modal.find(".modal-body");
        var form = modalBody.find('form');
        var url = form.attr('action');
        var token = $("#csrf-token").val();
        var titleField = form.find("#title");
        var universityField = form.find("#university");
        var abbreviationField = form.find("#abbreviation");

        $.ajax({
            type: 'put',
            url: url,
            dataType: "json",
            data: {
                _token: token,
                title: titleField.val(),
                university: universityField.val(),
                abbreviation: abbreviationField.val()
            },
            success: function (data) {

                var el = $("#status-update-faculty");
                var msgField = el.find("#status-update-faculty-message");

                if (data.status == "success") {
                    msgField.html(data.message);
                    el.addClass('alert-success');
                }

                var row = $("#supported-faculties-table").find("[data-id='" + editingId + "']");

                row.find(".title-cell").html(titleField.val());
                row.find(".university-cell").html(universityField.val());
                row.find(".abbreviation-cell").html(abbreviationField.val());

                el.show();

                setTimeout(function(){
                    modal.modal('hide');
                }, 1000);

            }
        });

        e.preventDefault();
        return false;

    });

});