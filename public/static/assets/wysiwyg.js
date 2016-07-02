$(function(){

    /**
     * WYSIWYG editor for meeting's details
     */

    var options = {
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
    };
    $('.has-editor').wysihtml5(options);

    $('#txt-meeting-details').wysihtml5(options);


});