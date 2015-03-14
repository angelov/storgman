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

        var countAttendants = parseInt($('#count-attendants').text());

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
    });

    $('.row-fee').hover(
        function(){
            $(this).children().eq(1).find('.link-all-fees-member').show();
        },
        function(){
            $(this).children().eq(1).find('.link-all-fees-member').hide();
        }
    );

    /**
     * Store the new document
     */

    $(document).on('click', '#btn-store-document', function() {
        var btn = $(this);

        $.ajax({
            type: 'post',
            url: btn.attr('href'),
            data: {
                'title': $('#document-title').val(),
                'description': $('#document-description').val(),
                'url': $('#document-url').val(),
                '_token': $("#csrf-token").val()
            },
            success:function(data){

                /** @todo Show success message */

                $("#documents-list").prepend(data);

                setTimeout(function(){
                    $('#modal-add-document').modal('hide');
                    $("#form-add-document").trigger("reset");
                }, 1500);

            },
            error: function() {
                /** @todo Show error message */
            }
        });

        return false;
    });

    /**
     * Show share-able document's link
     */

    $(document).on('click', '.btn-show-link', function() {
        $(this).parents(".document-item").find(".document-share-link").slideDown();

        return false;
    });

    /**
     * Filter tags when browsing documents
     */

    $("#input-filter-tags").keyup(function() {
        var find = $(this).val().toLowerCase();

        $(".tag-item").each(function() {
            var link = $(this).find("a");
            var tag = link.text().toLowerCase();

            if (tag.indexOf(find) >= 0) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    /**
     * Show delete/edit buttons for document
     */

    $(".document-item").hover(
        function(){
            var a = $(this).find('.document-actions');
            var h = $(this).find('.panel-heading');

            var org_height = h.height();

            a.show();

            h.height(org_height);
        },
        function(){
            $(this).find('.document-actions').hide();
        }
    );

    /**
     * Deleting a document
     */

    $(document).on('click', '.btn-delete-document', function() {
        var confirmed = confirm('Are you sure?');
        var btn = $(this);
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

                    var document_item = btn.parents('.document-item');

                    //console.log(document_item.html());

                    document_item.html("");
                    document_item.removeClass('panel panel-default');

                    document_item.html('' +
                        '<div class="alert alert-success alert-dismissible" role="alert">' +
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                            '<span aria-hidden="true">&times;</span>' +
                        '</button>' +
                        data.message + '</div>'
                    );

                },
                error: function(data, status, message) {
                    console.log(status + ": " + message);
                }
            });

        }

        return false;
    });

});