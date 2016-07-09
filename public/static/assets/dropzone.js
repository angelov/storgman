$(function() {

    if (typeof uploadUrl === 'undefined') {
        return;
    }

    var previewNode = document.querySelector("#template");
    previewNode.id = "";
    var previewTemplate = previewNode.parentNode.innerHTML;
    var parentNode = previewNode.parentNode;
    parentNode.removeChild(previewNode);

    var progressBars = [];
    var uploadedFiles = [];

    myDropzone = new Dropzone(document.body, {
        url: uploadUrl,
        parallelUploads: 1,
        previewTemplate: previewTemplate,
        autoQueue: false,
        previewsContainer: "#previews",
        clickable: ".fileinput-button"
    });

    myDropzone.on("addedfile", function(file) {

        var existingId = file.id;
        var id = "f" + Date.now() + (Math.round(Math.random() * 100));

        file.id = id;

        var previewElement = $(file.previewElement);
        var el = previewElement.find('.file-progress-bar');

        el.attr('id', id);

        if (file.mock) { // this is true when there are existing files to be listed
            uploadedFiles[id] = existingId;
            return;
        }

        progressBars[id] = new ProgressBar.Line("#" + id, {
            strokeWidth: 2,
            color: '#c4e3f3',
            trailColor: '#eee'
        });

        previewElement.find(".progress-bar-row").show();

        var fsb = myDropzone.options.filesizeBase;
        var fileSize = (file.size / fsb) / fsb;
        var maxFileSize = myDropzone.options.maxFilesize;

        if (fileSize <= maxFileSize) {
            myDropzone.uploadFile(file);
        }

    });

    myDropzone.on("uploadprogress", function(file, progress) {

        var progressBar = progressBars[file.id];
        progress = progress / 100;

        progressBar.animate(progress, {
            duration: 1
        });

    });

    myDropzone.on("error", function(file, errors) {

        var previewElement = $(file.previewElement);
        var el = previewElement.find(".file-error-messages");

        if (errors.constructor === Array) {
            for (var i in errors) {
                var error = errors[i];

                if (errors[i+1] != undefined) {
                    error += "<br />";
                }

                el.append(error);
            }
        } else {
            el.append(errors + "<br />");
        }

        previewElement.css('background', '#fcf8e3');

        el.show();

    });

    myDropzone.on("success", function(file, response) {
        uploadedFiles[file.id] = response;
    });

    myDropzone.on("removedfile", function(file) {
        delete uploadedFiles[file.id];
    });

    myDropzone.on("sending", function(file, xhr, formData) {
        formData.append("_token", $('[name=_token]').val());
    });

    $(".uploads-files").on('click', function(e) {
        var form = $(this).parents('form');
        var attachments = [];

        for (var i in uploadedFiles) {
            attachments.push(uploadedFiles[i]);
        }

        attachments = JSON.stringify(attachments);

        var field = "<input type='hidden' name='attachments' id='attachments' value='"+attachments+"' />";

        form.append(field);
    });

});