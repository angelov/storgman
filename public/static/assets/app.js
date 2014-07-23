$(function(){

    /**
     * Hide the alert boxes
     */

    $("#action-status").hide();

    /**
     * Used as an alternative to data-dismiss to just hide the elements instead
     * of completely removing them
     */

    $("[data-hide]").on("click", function(){
        $(this).closest("." + $(this).attr("data-hide")).hide();
    });

    /**
     * Display a tooltip when needed
     */

    $('.has-tooltip').tooltip();

    /**
     * Deleting a member
     */

    $(document).on('click', '.btn-delete-member', function() {
        var confirmed = confirm('Are you sure?');
        var btn = $(this);
        var member = btn.data('member');

        if (confirmed) {

            $.ajax({
                type: 'delete',
                url: btn.attr('href'),
                dataType: "json",
                success:function(data){

                    $("#action-message").html(data.message);

                    $("#action-status").removeClass()
                        .addClass('alert')
                        .addClass('alert-dismissable')
                        .addClass('alert-' + data.status)
                        .show();

                    btn.closest('tr').slideUp('slow');

                }
            });

        }

        return false;
    });

    /**
     * Show modal window with member's membership fees
     */

    $('.btn-renew-membership').click(function() {

        var btn = $(this);
        var member = btn.data('member');

        $.ajax({
            type: 'get',
            url: btn.attr('href'),
            success:function(data){
                $("#modal-renew-membership").html(data);
                console.log(btn.attr('href'));
                $('#renew-status').hide();
            }
        });

        $("#modal-renew-membership").modal('show');
        return false;

    });

    /**
     * Store the new membership fee
     */

    $(document).on('click', '#btn-proceed-fee', function() {
        var btn = $(this);

        $.ajax({
            type: 'post',
            url: btn.attr('href'),
            data: {
                'from': $('#fee-from').val(),
                'to': $('#fee-to').val(),
                'member-id': $('#fee-member-id').val()
            },
            dataType: "json",
            success:function(data){
                $("#renew-message").html(data.message);

                $("#renew-status").removeClass()
                    .addClass('alert')
                    .addClass('alert-dismissable')
                    .addClass('alert-' + data.status)
                    .show();

                setTimeout(function(){
                    $('#modal-renew-membership').modal('hide');
                }, 1500);
            }
        });

        console.log("will save.");
        return false;
    });

    /**
     * Date picker
     */

    var formatDate = function(date) {

        var days = date.getDate();
        if (days < 10) {
            days = '0' + days;
        }

        var month = date.getMonth();
        if (month < 10) {
            month = '0' + month;
        }

        return date.getFullYear() + "-" + month + "-" + days;

    };

    var datePickerOptions = {
        weekStart: 1,
        autoclose : true,
        format: "yyyy-mm-dd"
    };

    $('.input-group.date').datepicker(datePickerOptions);

    $(document).on('focus',"#fee-from", function(){
        $(this).datepicker(datePickerOptions).on('changeDate', function() {
            //console.log($(this).val());
            var date = new Date($(this).val());
            date.setFullYear(date.getFullYear() + 1);
            $('#fee-to').val(formatDate(date));
        });
    });

    /**
     * Member's photo upload
     */

    $("#member-photo").on("change", function()
    {
        console.log("changed.");

        var files = !!this.files ? this.files : [];
        if (!files.length || !window.FileReader) { // no file selected, or no FileReader support
            return;
        }

        if (/^image/.test( files[0].type)){
            var reader = new FileReader();
            reader.readAsDataURL(files[0]);

            reader.onloadend = function(){
                $("#img-preview-photo").css("background-image", "url("+this.result+")");
            }
        }
    });

    /**
     * WYSIWYG editor for meeting's details
     */

    $('#txt-meeting-details').wysihtml5({
        toolbar: {
            "font-styles": false,
            "emphasis": true,
            "lists": true,
            "html": false,
            "link": true,
            "image": false,
            "color": false,
            "blockquote": false
        }

    });

    /**
     * Meeting's attendants
     * (only if needed)
     * dirty.
     */


    // load the members only if needed
    if ($("#new-attendant").length) {

        var engine = new Bloodhound({
            name: 'members',
            prefetch: {url: '/members'},
            datumTokenizer: function(d) {
                return Bloodhound.tokenizers.whitespace(d.value);
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace
        });
        engine.initialize();

        $('#new-attendant').typeahead(
            {
                hint: true,
                highlight: true,
                minLength: 1
            },
            {
                name: 'states',
                displayKey: 'value',
                source: engine.ttAdapter()
            }
        ).bind('typeahead:selected', function(obj, member) {

                var att = $('#tpl-attendant').clone();
                att.removeAttr('id');

                var img = att.children('img');

                img.attr('src', member.image);
                att.attr('title', member.value);
                att.attr('data-id', member.id);
                att.tooltip();

                $('#attendants-list').append(att);
                att.show();

                var inp = $("#attendants");
                inp.val(inp.val() + member.id + "|");
                //console.log($("#attendants").val());

                $('#new-attendant').val('');

            });

        // delete the attendant from the meeting
        $(document).on('click', '.delete-attendant a', function() {

            var inp = $("#attendants");
            var str = inp.val();
            var id = $(this).closest('.attendant').data('id');
            var pattern = new RegExp("\\|+" +id + "\\|", "g");
            str = str.replace(pattern, "|");
            inp.val(str);

            $(this).closest('.attendant').hide();
            return false;

        });

        $(document).on('mouseenter', '.attendant', function() {
            $(this).find('.delete-attendant').show();
        });

        $(document).on('mouseleave', '.attendant', function() {
            $(this).find('.delete-attendant').hide();
        });

    }



});