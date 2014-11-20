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
        var token = $("#csrf-token").val();

        if (confirmed) {

            $.ajax({
                type: 'delete',
                url: btn.attr('href'),
                data: {
                    '_token': token
                },
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
     * Approving a member
     */

    $(document).on('click', '.btn-approve-member', function() {
        var btn = $(this);
        var member = btn.data('member');
        var token = $("#csrf-token").val();

        console.log(token);

        $.ajax({
            type: 'post',
            url: btn.attr('href'),
            dataType: "json",
            data: { _token: token },
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

        return false;
    });

    /**
     * Declining a pending member account
     */

    $(document).on('click', '.btn-decline-member', function() {
        var btn = $(this);
        var member = btn.data('member');
        var token = $("#csrf-token").val();

        $.ajax({
            type: 'post',
            url: btn.attr('href'),
            dataType: "json",
            data: { _token: token },
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

        return false;
    });

    /**
     * Searching for a member
     * needs refactoring.
     */

//    var searchResized = false;
//
//    $("#quick-member-search").click(function(){
//
//        if (!searchResized) {
//            console.log($(this).css("left"));
//            var elwidth = $(this).width() + 80;
//
//            $(this).animate({
//                'marginLeft'    : "-=50px",
//                'width': elwidth + "px"
//            });
//
//            searchResized = true;
//        }
//
//    });

    if ($("#quick-member-search").length) {

        var engine2 = new Bloodhound({
            name: 'members',
            prefetch: {url: '/members/prefetch'},
            datumTokenizer: function(d) {
                return Bloodhound.tokenizers.whitespace(d.value);
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace
        });
        engine2.initialize();

        $("#quick-member-search").typeahead(
            {
                hint: true,
                highlight: true,
                minLength: 1
            },
            {
                name: 'states',
                displayKey: 'value',
                source: engine2.ttAdapter()
            }
        ).bind('typeahead:selected', function(obj, member) {
                window.location = "/members/" + member.id;
            });

    }

    /**
     * Show modal window with member's membership fees
     */

    $(document).on('click', '.btn-renew-membership', function() {

        var btn = $(this);
        var member = btn.data('member');

        $.ajax({
            type: 'get',
            url: btn.attr('href'),
            success:function(data){
                $("#modal-renew-membership").html(data);
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
        console.log("clicked");
        var token = $("#csrf-token").val();

        $.ajax({
            type: 'post',
            url: btn.attr('href'),
            data: {
                'from': $('#fee-from').val(),
                'to': $('#fee-to').val(),
                'member_id': $('#fee-member-id').val(),
                '_token': token
            },
            dataType: "json",
            success:function(data){
                $("#renew-message").html(data.message);

                $("#renew-status").removeClass()
                    .addClass('alert')
                    .addClass('alert-dismissable')
                    .addClass('alert-' + data.status)
                    .show();

                if (data.status == 'success') {
                    setTimeout(function(){
                        $('#modal-renew-membership').modal('hide');
                    }, 1500);
                }
            }
        });

        return false;
    });

    /**
     * Deleting a paid membership fee
     */

    $(document).on('click', '.btn-delete-fee', function() {
        var confirmed = confirm('Are you sure?');
        var btn = $(this);
        var fee = btn.data('fee');
        var token = $("#csrf-token").val();

        if (confirmed) {

            $.ajax({
                type: 'delete',
                url: btn.attr('href'),
                dataType: "json",
                data: {
                    '_token': token
                },
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
     * Member's photo upload
     */

    $("#member-photo").on("change", function()
    {
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
     * Meeting's attendants
     * (only if needed)
     * dirty.
     */

    // load the members only if needed
    if ($("#new-attendant").length) {

        var countAttendants = 0;

        var engine = new Bloodhound({
            name: 'members',
            prefetch: {url: '/members/prefetch'},
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

                $('#count-attendants').html(++countAttendants);

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
            $('#count-attendants').html(--countAttendants);

            return false;

        });

        $(document).on('mouseenter', '.attendant', function() {
            $(this).find('.delete-attendant').show();
        });

        $(document).on('mouseleave', '.attendant', function() {
            $(this).find('.delete-attendant').hide();
        });

    }

    /**
     * Deleting a meeting report
     */

    $(document).on('click', '.btn-delete-meeting', function() {
        var confirmed = confirm('Are you sure?');
        var btn = $(this);
        var member = btn.data('meeting');
        var token = $("#csrf-token").val();

        if (confirmed) {

            $.ajax({
                type: 'delete',
                url: btn.attr('href'),
                dataType: "json",
                data: {
                    '_token': token
                },
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

    $('#table-member-attendance').slimScroll({
        height: '130px'
    });

    if ($('#quick-member-search-for-fee').length) {
        var engineA = new Bloodhound({
            name: 'members',
            prefetch: {url: '/members/prefetch'},
            datumTokenizer: function(d) {
                return Bloodhound.tokenizers.whitespace(d.value);
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace
        });
        engineA.initialize();

        $('#quick-member-search-for-fee').typeahead(
            {
                hint: true,
                highlight: true,
                minLength: 1
            },
            {
                name: 'states',
                displayKey: 'value',
                source: engineA.ttAdapter()
            }
        ).bind('typeahead:selected', function(obj, member) {

                $.ajax({
                    type: 'get',
                    url: '/members/' + member.id + '/quick-info',
                    success:function(data){

                        console.log(data);
                        $("#member-info").html(data).show();

                    }
                });

            });
    }

    $('#quick-member-search-for-fee').click(function(){
        $(this).val('');
    })

    $('.row-fee').hover(
        function(){
            $(this).children().eq(1).find('.link-all-fees-member').show();
        },
        function(){
            $(this).children().eq(1).find('.link-all-fees-member').hide();
        }
    );

});