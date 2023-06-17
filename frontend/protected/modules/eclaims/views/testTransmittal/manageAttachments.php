<?php

Yii::import('bootstrap.helpers.TbHtml');
Yii::import('bootstrap.widgets.TbActiveForm');
Yii::import('bootstrap.widgets.TbButton');

$this->breadcrumbs[$details->transmit_no] = array('attachments', 'id' => $details->transmit_no);
$this->breadcrumbs[] = 'Manage attachments';

/* @var $this Controller */
/* @var $details EclaimsTransmittalDetails */

$this->setPageTitle('Manage claim attachments');
$cs = Yii::app()->getClientScript();
$cs->registerCss('transmittal.manageAttachments.css', <<<CSS
    .filename {
        font-weight: bold;
        cursor: pointer;
    }
    .popover {
        max-width: none;
    }
    .popover-content {
        width: 65em;
        height: 20em;
    }
CSS
)
->registerScript('transmittal.manageAttachments.js', <<<JAVASCRIPT

/**
 * Not checked: NULL, w/o errors: false, w/ errors: true
 */
var uploadFormAttachment = {
    error: null,
    getError: function() { return this.error; },
    setError: function(val) { this.error = val; },
    refreshError: function() { this.error = null; }
};

$('.remove-attachment-btn').off('click').on('click', function(e) {
    e.preventDefault();
    var that = this;
    Alerts.confirm({
        title: 'Remove this attachment?',
        content: 'The attachment will be <b>permanently deleted</b>. Are you sure?',
        callback: function(result) {
            if (result) {
                Alerts.loading({
                    title: 'Please wait',
                    content: 'Attempting to delete the attachment from our remote server...'
                });
                $('#remove-attachment-id').val(that.value);
                $('#remove-attachment-form').submit();
            }
        }
    })
});


$('#attachments-submit').on('click', function() {
    // if($._isAttachmentListEmpty()) {
    //     Alerts.error({
    //         title: 'Attachment error',
    //         content: 'No attachments/files selected for uploading!'
    //     });
    //     return false;
    // }
});

$('#close-button').click(function(e) {
    e.preventDefault();
    var that=this;

    if ($._isAttachmentListEmpty()) {
        window.location.href = that.href;
    } else {
        Alerts.confirm({
            title: 'Hey! Wait a minute...',
            content: 'Some attachments have not been uploaded yet. Do you still wish to leave this page?',
            callback: function(result) {
                if (result) {
                    window.location.href = that.href;
                } else {
                    // Do nothing
                }
            }
        });
    }
});

// Get all <select> attachment types elements
$._getAllSelectAttachmentType = function() {
    var form = $('form.multi-upload');
    return form.find('select.attachment-type');
};
// Is attachment form empty?
$._isAttachmentListEmpty = function() {
    var attachmentTypes = $._getAllSelectAttachmentType();
    if(attachmentTypes.length > 0) {
            return false;
        }
    return true;
};
// Has an empty attachment type?
$._hasEmptyAttachmentType = function() {
    var attachmentTypes = $._getAllSelectAttachmentType(),
        error = false;

    attachmentTypes.each(function() {
        var item = $(this);
        if(item.val().length < 1) {
            return error = true;
        }
    });
    return error;
};
// Validate attachment form, e.g. if has empty attachment type.
$._validateForm = function() {
    /* If already vaidated, do not revalidate; unless new item added */
    var error = uploadFormAttachment.getError();
    if(error === null) {
        error = $._hasEmptyAttachmentType();
        uploadFormAttachment.setError(error);
    }

    // if(error) {
    //     Alerts.error({
    //         title: 'Attachment error',
    //         content: 'One or more attachments do not have a selected <b>attachment type</b>.'
    //     });
    //     return false;
    // }
    return true;
    }

JAVASCRIPT
, CClientScript::POS_READY);
?>

<div class="row-fluid">
    <div class="span5">

    <?php
        $this->widget('eclaims.widgets.AttachmentsUpload', array(
            'url'         => $this->createUrl("FixAttachments", array(
                'transmit_no' => $details->transmit_no,
                'encounter_nr' => $details->encounter_nr,
            )),
            'extra'       => array(
                'details' => $details
                        ),
            'model'       => $attachmentForm,
            'attribute'   => 'attachment', // see the attribute?
            'multiple'    => true,
            'options'     => array(
                'maxFileSize' => 21000000,
                'acceptFileTypes' => 'js:/(\.|\/)(pdf)$/i',
                'add' => 'js:function (e, data) {
                    var that = this;
                    $.blueimp.fileupload.prototype
                        .options.add.call(that, e, data);

                    var id = new Date().getTime();
                    data.paramName = data.paramName + "["+ id +"]";

                    var attachmentInput = $("tbody.files tr:last .attachment-type");
                    var cancelButton = $("tbody.files tr:last .attachment-row-cancel");
                    var filename = $("tbody.files tr:last .filename");

                    /*
                        rename <select> attachment type original name + id(see above)
                    */
                    attachmentInput.attr("name", attachmentInput.attr("name") + "["+ id +"]");
                    /* 
                        add popover on filename mouseenter and popover mouseleave 
                    */
                    filename.popover();
                    filename.off("hover").on("hover", function() {
                        var that = $(this),
                            popover = that.next();

                        that.popover("show");

                        popover.off("mouseleave").on("mouseleave", function() {
                            that.popover("hide");
                        });
                    }).off("mouseleave").on("mouseleave", function() {
                        var that = $(this),
                            sibling = that.next();
                        if(!sibling.is(":hover") && sibling.hasClass("popover")) {
                            that.popover("hide");
                        }
                    });
                    /* 
                        Refresh Form error, to enable revalidate behavior
                        Listen events on: add, change(attachment type), delete
                    */
                    attachmentInput.off("change").on("change", function() {
                        uploadFormAttachment.refreshError();
                    });
                    cancelButton.off("click").on("click", function() {
                        uploadFormAttachment.refreshError(); 
                    });
                    uploadFormAttachment.refreshError(null);
                }',
                'success' => 'js:function(file, status) {
                    $.fn.yiiGridView.update("uploaded-attachments");
                }',
                'submit' => 'js:function(e, data) {
                    return $._validateForm();
                }'
                            ),
            'formView'     => 'eclaims.views.transmittal.uploadtemplates.fileupload-form',
            'uploadView'   => 'eclaims.views.transmittal.uploadtemplates.fileupload-item',
            'downloadView' => 'eclaims.views.transmittal.uploadtemplates.fileupload-download',
                            'htmlOptions' => array(
                'class' => 'multi-upload'
                            )
                        ));
                    ?>

    </div>

    <div class="span7">
        <?php
            $this->beginWidget('application.widgets.SegBox', array(
                'title' => 'List of uploaded attachments',
                'htmlOptions' => array(
                    'class' => 'bootstrap-widget-table'
                )
            ));

            $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
                'id' => 'remove-attachment-form',
                'type' => TbActiveForm::TYPE_VERTICAL,
                'action' => $this->createUrl('removeAttachment', array(
                    'transmit_no' => $details->transmit_no,
                    'encounter_nr' => $details->encounter_nr,
                )),
                'htmlOptions'=>array('enctype'=>'multipart/form-data'),
            ));

            echo CHtml::hiddenField('id', null, array(
                'id' => 'remove-attachment-id'
            ));

        ?>

            <?php
                $this->widget('bootstrap.widgets.TbGridView', array(
                    'id' => 'uploaded-attachments',
                    'type' => 'striped condensed bordered hover',
                    'dataProvider' => $details->searchAttachments(),
                    'template' => "{items}",
                    'columns' => array(
                        array(
                            'header' => 'Type',
                            'type' => 'raw',
                            'value' => '"<span class=\"label label-info\">{$data->attachment_type}</span>"'
                        ),
                        array(
                            'header' => 'File name',
                            'type' => 'raw',
                            'value' => function($data) {
                                $fileUrl = $data->getUrl();
                                if ($fileUrl) {
                                    $item = CHtml::link($data->filename, $fileUrl, array('target' => '_blank'));
                                } else  {
                                    $item = $data->filename;
                                }
                                return "<b>{$item}</b><br/><small>Size: {$data->FileSize}</small> <small style=\"color:#888\">(Hash: {$data->hash})</small>";
                            }
                        ),
                        array(
                            'header' => 'Actions',
                            'type' => 'raw',
                            'value' => <<<ACTION
Yii::app()->controller->widget('bootstrap.widgets.TbButton', array(
    'buttonType' => 'submit',
    'size' => 'mini',
    'label' => 'Remove',
    'icon' => 'fa fa-ban',
    'htmlOptions' => array(
        'class' => 'remove-attachment-btn',
        'value' => \$data->id
    )
), true)
ACTION
                        ),
                    )
                ));
            ?>

        <?php $this->endWidget(); // Form ?>
        <?php $this->endWidget(); // SegBox2 ?>

    </div>
</div>



