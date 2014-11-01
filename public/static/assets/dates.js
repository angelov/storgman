$(function(){

    /**
     * Formatting the dates and making them human-readable
     */

    var dateFormat = "DD MMM YYYY";

    $(".date-to-humanize").each(function() {
        //console.log($(this).html());
        var original = $(this).html();
        var date = moment(original);
        var now = moment();
        var duration = moment.duration(now - date);

        var prefix = '';
        var suffix = '';
        if (now.diff(date) < 0) {
            prefix = 'in ';
        } else {
            suffix = ' ago';
        }

        $(this).html(prefix + duration.humanize() + suffix);
        $(this).attr('title', original);
    });

    $(".date-to-format").each(function() {
        var original = $(this).html();
        var date = moment(original);

        $(this).html(date.format(dateFormat));
        $(this).attr('title', original);
    });

});