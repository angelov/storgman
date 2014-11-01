$(function(){

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

});