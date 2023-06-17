"use strict";
(function() {

/**
 * Initializes the CodeMirror widget
 *
 */
function completeAfter(cm, pred) {
    var cur = cm.getCursor();
    if (!pred || pred()) {
        setTimeout(function() {
            if (!cm.state.completionActive)
                cm.showHint({completeSingle: false});
        }, 100);
    }
    return CodeMirror.Pass;
}

function completeIfAfterLt(cm) {
    return completeAfter(cm, function() {
        var cur = cm.getCursor();
        return cm.getRange(CodeMirror.Pos(cur.line, cur.ch - 1), cur) == "<";
    });
}

function completeIfInTag(cm) {
    return completeAfter(cm, function() {
        var tok = cm.getTokenAt(cm.getCursor());
        if (tok.type == "string" && (!/['"]/.test(tok.string.charAt(tok.string.length - 1)) || tok.string.length == 1)) return false;
        var inner = CodeMirror.innerMode(cm.getMode(), tok.state).state;
        return inner.tagName;
    });
}

var editor = CodeMirror.fromTextArea(document.getElementById('xmlTextArea'), {
    lineNumbers: true,
    lineWrapping: true,
    styleActiveLine: true,
    theme: "ambiance",
    mode: "xml",
    hintOptions: {
        schemaInfo: eclaims.transmittal.tags
    },
    extraKeys: {
        "'<'": completeAfter,
        "'/'": completeIfAfterLt,
        "' '": completeIfInTag,
        "'='": completeIfInTag,
        "Ctrl-Space": "autocomplete",
    }
});

editor.on('change', function(instance, change) {
    var isEmpty = !instance.getValue();
    $('#saveBtn').prop('disabled', isEmpty);
    $('#validateBtn').prop('disabled', isEmpty);
});


//$('.CodeMirror').resizable({
//    resize: function() {
//        editor.setSize($(this).width(), $(this).height());
//    }
//});

/**
 * Done with the CodeMirror stuff!
 */


/**
 * Validation errors
 */
var validationErrorsTemplate = $('#validationErrorsTemplate').html();
Mustache.parse(validationErrorsTemplate);

/**
* Dynamically populates XML Deficiencies without reloading or rendering new page
*
*/
function populateValidationErrors(errors) {

    var modal = $('#errorModal .modal-body');
    modal.html('');
    var errorCount = 0;


    $.each(errors, function(index, _errors) {

        var normalizedErrors = [];
        $.each(_errors, function(i, error) {
            normalizedErrors.push({
                index: i+1,
                error: error
            });

            errorCount++;
        });

        var data = {
            header: index,
            errors: normalizedErrors
        };
        modal.append(Mustache.render(validationErrorsTemplate, data));
    });

    $('#errorBtn').prop('disabled', errorCount == 0);
    $('#errorBtn .badge').text(errorCount || '');
}


/* Handles 'Generate' button click event */
$('#generateBtn').off('click').on('click',function(e){
    e.preventDefault();
    var that = this;
    var r = $(that).data('action');

    function generate(that) {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: $(that).data('action'),
            beforeSend: function() {
                Alerts.loading({
                    title: 'Please wait',
                    content: 'We are currently generating the eClaims Transmittal XML. This may take a while..'
                });
            },
        }).done(function(data) {
            Alerts.close();
            if (data !== false) {
                editor.setValue(data.content)
                $('#validateBtn').prop('disabled', false);
                if (data.errors && data.count > 0) {
                    populateValidationErrors(data.errors);
                    Alerts.warn({
                        title: 'Success but with some errors...',
                        content: 'Your eClaims Transmittal XML was successfully generated but we found a few <b>validation errors</b> along the way. You can click <b>Show errors</b> button to review them.',
                    });
                } else {
                    populateValidationErrors({});
                    Alerts.warn({
                        title: 'Success!',
                        content: 'Your eClaims Transmittal XML is valid and ready for upload',
                        icon: 'fa-check-circle-o',
                        iconColor: '#2DCC70',
                    });
                }
            } else {
                Alerts.error({
                    title: 'Ooops!',
                    content: 'Something went wrong while generating the Transmittal XML. Please try again',
                    icon: 'fa-frown-o'
                });
            }
        }).error(function(jqXHR, textStatus, errorThrown) {
            Alerts.close();
            Alerts.error({
                title: 'Oops!',
                content: 'Something went wrong while generating the transmittal XML. Please contact your system administrator'
            });
        });
    }

    if (!!editor.getValue()) {
        Alerts.confirm({
            title: 'Your current data will be overwritten',
            content: 'Do you wish to proceed?',
            callback: function(result) {
                if (result) {
                    generate(that)
                }
            }
        });
    } else {
        generate(that)
    }
});

/*reset button*/
$('#resetBtn').off('click').on('click', function(e){
    e.preventDefault();
    var r = $(this).data('action');
        Alerts.confirm({
                title: 'This will clear out your current XML data.',
                content: 'Do you wish to proceed?',
                callback: function(result) {
                    if (result) {
                       editor.setValue('');
                        $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: r
                        // beforeSend: function() {
                        //     // Alerts.loading({
                        //     //     title: 'Please wait',
                        //     //     content: 'We are currently generating the eClaims Transmittal XML. This may take a while..'
                        //     // });
                        // }
                        }).done(function(){  
                            // Alerts.warn({
                            //     title: 'Success!',
                            //     content: 'eClaims Transmittal XML reset successful',
                            //     icon: 'fa-check-circle-o',
                            //     iconColor: '#2DCC70',
                            // });
                        })
                    }
                }
            });
    });

/* Handles 'Validate' button click event */
$('#validateBtn').off('click').on('click',function(e) {
    e.preventDefault();
    var that = this;
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: $(that).data('action'),
        data: {
            xml: editor.getValue()
        },
        beforeSend: function() {
            Alerts.loading({ content: 'Validating Transmittal XML...' });
        },
    }).done(function(data) {
        Alerts.close();
        if (data.errors && data.count > 0) {
            populateValidationErrors(data.errors);
            Alerts.warn({
                title: 'Oops!',
                content: 'Your Transmittal XML contained a few <b>validation errors</b>. You can click <b>Show errors</b> button to review them.',
            });
        } else {
            populateValidationErrors({});
            Alerts.warn({
                title: 'Success!',
                content: 'Your eClaims Transmittal XML is valid and ready for upload',
                icon: 'fa-check-circle-o',
                iconColor: '#2DCC70',
            });
        }

    }).error(function() {
        Alerts.close();
        Alerts.error({
            title: 'Unable to validate',
            content: 'Error encountered when attempting to validate Transmittal XML. Please try again...'
        });
    });
});

/* Handles 'Save' button click event */
// $('#saveBtn').off('click').on('click',function(e){
//     $('.modal-body').html('');
//     var xmlString = editor.getValue();
//     e.preventDefault();
//      $.ajax({
//         type: 'POST',
//         data: 'process=save' + '&xmlString=' + xmlString +'&transmit_no=' + $('#transmit_no').val(),
//         dataType: 'json',
//         url: '{$processUrl}',
//         beforeSend: function() {
//             Alerts.loading({ content: 'Saving Transmittal XML' });
//         },
//       }).done(function(data) {
//         Alerts.close();
//         if(data == true){
//             Alerts.warn({ title: 'Success!', content: 'Transmittal XML successfully saved and is valid', icon: 'fa-check-circle-o', iconColor: '#2DCC70', actions: '' });
//             location.reload();
//         } else if(data == false) {
//             Alerts.error({ title: 'Syntax Error', content: 'Transmittal XML has syntax errors'});
//             $('#uploadBtn').attr('disabled', 'disabled');
//             $('#yw5').html('');
//         } else {
//             Alerts.error({ title: 'XML Error', content: 'Transmittal XML has validation errors. Please view XML deficiencies '});
//             $('#yw5').remove();
//             $('#errorBtn').removeAttr('disabled', 'disabled');
//             $('#uploadBtn').attr('disabled', 'disabled');
//             setTimeout(populateValidationErrors(data), 5000);
//         }
//                 }).error(function(jqXHR, textStatus, errorThrown) {
//         Alerts.error({ content: 'Error in saving Transmittal XML'});
//     });
// });

/**
 * Populates contents of an alert box
 * @param string header - alert header
 * @param string message - alert body content
 * @param string type - alert type (success|error|warning|info)
 */
function setFlash(header ,message, type) {
    var flash = $(document).find('div#alert-flash');

    $(flash).html('');
    $(flash).append($('<div>', {
        class: 'alert in alert-block fade alert-' + type,
        id: 'content'
    }));

    var box = $(flash).find('div#content');

    $(box).append($('<strong>', {
        text: header,
    }));

    $(box).html(
        '<strong>' + header + '</strong>: ' + message
    );
}


/* Handles 'XML Deficiencies' button click event */
$('#errorBtn').click(function(e) {
    e.preventDefault();
    $('#errorModal').modal();
});


/* Handles 'Upload' button click event */
$('#uploadBtn').on('click',function(e){
    e.preventDefault();
    $.ajax({
        type: 'POST',
        url: '{$uploadUrl}',
        data: 'transmit_no=' + $('#transmit_no').val(),
        beforeSend: function() {
            Alerts.loading({ content: 'Please wait. We are currently uploading the transmittal to the PHIC web service!' });
        },
    }).done(function(data) {
        if(data == 'true'){
            setFlash('Sucess','Transmittal XML successfully uploaded. View PHIC Response for details.', 'success');
            Alerts.warn({ title: 'Success!', content: 'Transmittal XML successfully uploaded. Ready for Mapping.', icon: 'fa-check-circle-o', iconColor: '#2DCC70', actions: '' });
            setTimeout(
                function(){
                    $.ajax({
                        type: 'POST',
                        url: '{$mapUrl}',
                        data: 'transmit_no=' + $('#transmit_no').val(),
                        beforeSend: function() {
                            Alerts.loading({ content: 'Please wait. We are currently mapping the transmittal to the PHIC web service!' });
                        },
                    }).done(function(data) {
                        Alerts.close();
                        if (data == 'true'){
                            setFlash('Sucess','Transmittal XML successfully uploaded and mapped. View PHIC Response for details.', 'success');
                            Alerts.warn({ title: 'Success!', content: 'Transmittal successfully mapped', icon: 'fa-check-circle-o', iconColor: '#2DCC70' });
                        } else if(data == 'false'){
                            Alerts.error({ title: 'Error', content: 'Failed to save the map response. Try to map again. '});
                        } else{
                            setFlash('Info','Uploaded transmittal still needed to be mapped.', 'info');
                            Alerts.error({ title: 'Unexpected Error', content: data});
                        }
                        setTimeout(function(){window.location.href = '{$url}';},2000);
                    }).error(function(jqXHR, textStatus, errorThrown) {
                        setFlash('Error',textStatus + ' ' + errorThrown, 'error');
                        Alerts.error({ title: data, content: 'Error in accessing the map web service. '});
                    });
                }
                ,3000);
        } else if(data == 'false'){
            Alerts.error({ title: 'Error', content: 'Failed to save the upload response. Try to upload again. '});
        } else if(data.length <= 50){
            Alerts.error({ title: 'Unexpected Error', content: data});
        } else{
            Alerts.error({ title: 'Fail Response', content: 'Error in transmittal data parameter values. Please view XML deficiencies.'});
            $('#errorBtn').removeAttr('disabled', 'disabled');
            $("#errorModal .modal-header h4").html("Upload Deficiencies");
            $("#errorModal .modal-body").html(data);
        }
    }).error(function(jqXHR, textStatus, errorThrown) {
        setFlash('Error',textStatus + ' ' + errorThrown, 'error');
        Alerts.error({ title: data, content: 'Error in accessing the upload web service. '});
    });
});

//
$('[data-url]').click(function(e) {
    e.preventDefault();
    window.location = $(this).data('url');
});


})();