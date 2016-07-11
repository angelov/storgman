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