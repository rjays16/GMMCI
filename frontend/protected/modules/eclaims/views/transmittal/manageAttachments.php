<?php

Yii::import('bootstrap.helpers.TbHtml');
Yii::import('bootstrap.widgets.TbActiveForm');
Yii::import('bootstrap.widgets.TbButton');



$this->breadcrumbs[$details->transmit_no] = array('attachments', 'id' => $details->transmit_no);
$this->breadcrumbs[] = 'Manage attachments';


$cs = Yii::app()->clientScript;
$baseUrl = $baseUrl = Yii::app()->request->baseUrl;
$cs
    ->registerCssFile($baseUrl . '/css/codemirror/lib/codemirror.css')
    ->registerCssFile($baseUrl . '/css/codemirror/theme/ambiance.css')
    ->registerCssFile($baseUrl . '/css/codemirror/addon/hint/show-hint.css')
    ->registerCss('transmittal.generate', <<<STYLE
.CodeMirror {
    border: 1px solid #ccc;
    font-family: Monaco, Menlo, Consolas, 'Courier New', monospace;
    font-size: 14px;
        height: auto;
}
STYLE
    )->registerScriptFile($baseUrl . '/js/codemirror/lib/codemirror.js')
    ->registerScriptFile($baseUrl . '/js/codemirror/mode/xml/xml.js')
    ->registerScriptFile($baseUrl . '/js/codemirror/addon/display/placeholder.js')
    ->registerScriptFile($baseUrl . '/js/codemirror/addon/hint/show-hint.js')
    ->registerScriptFile($baseUrl . '/js/codemirror/addon/hint/xml-hint.js')
    ->registerScriptFile($baseUrl . '/js/codemirror/addon/selection/active-line.js')
    ->registerScriptFile($baseUrl . '/js/frontend/eclaims/transmittal/tags.js')
    ->registerScriptFile($baseUrl . '/js/jquery/ui/jquery.livequery.min.js');
//    ->registerScriptFile($baseUrl . '/js/frontend/eclaims/transmittal/generate.js', CClientScript::POS_END);


/* @var $this Controller */
/* @var $details EclaimsTransmittalDetails */
/* @var $claim Claim */

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

    jQuery(function($) {
        var remove = $('.remove-attachment-btn');
        remove.livequery(function(e) {
            $(this).click(function(e) {
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
                });                
            });
        });
    });


    $('#attachments-submit').on('click', function() {
        if($._isAttachmentListEmpty()) {
            Alerts.error({
                title: 'Attachment error',
                content: 'No attachments/files selected for uploading!'
            });
            return false;
        }
    });

    // Added by Johnmel --- Alert for button Re-upload 07-04-2018
    $('#reUploadttachments-submit').on('click', function() {
        if($._isAttachmentListEmpty()) {
            Alerts.error({
                title: 'Attachment error',
                content: 'No attachments/files selected for Re-uploading!'
            });
            return false;
        }
    });
    // end Johnmel


    // auto assign file type on attachment : monmon
    $('#btnAssign').on('click', function(e){
        e.preventDefault();
        var filesAttached = [];
        var ftypes = [];
        $.ajax({
            type: "POST",
            url: $(this).data('url'),
            dataType:'JSON',
            data : [] ,
            success: function (response) {
                ftypes = response;
                // gets the attached file name
                var obj = document.querySelectorAll("[data-title]")
                for (var i in obj) if (obj.hasOwnProperty(i)) {
                    var filename = obj[i].getAttribute('data-title');
                    // separate filename and file extension
                    filename = filename.split(".");
                    var holder = filename[0].toUpperCase();

                    if(ftypes.indexOf(holder) >= 0)
                        filesAttached.push(holder);
                    else
                        filesAttached.push('');
                }
                // assign selected value from the array above
                var attTypes = document.getElementsByClassName("attachment-type");
                for(var i = 0; i < attTypes.length; i++){
                    var select = attTypes.item(i);
                    select.value = filesAttached[i];
                }
            },
        });
        return false;
    });
    function getFileTypes(){
        <!--var ftypes = [-->
            <!--'CF1',-->
            <!--'CF2',-->
            <!--'CF3',-->
            <!--'CF4',-->
            <!--'CSF',-->
            <!--'COE',-->
            <!--'SOA',-->
            <!--'MDR',-->
            <!--'ORS',-->
            <!--'POR',-->
            <!--'CAE',-->
            <!--'PIC',-->
            <!--'MBC',-->
            <!--'MMC',-->
            <!--'CAB',-->
            <!--'CTR',-->
            <!--'DTR',-->
            <!--'MEF',-->
            <!--'MSR',-->
            <!--'MWV',-->
            <!--'NTP',-->
            <!--'OPR',-->
            <!--'PAC',-->
            <!--'PBC',-->
            <!--'STR',-->
            <!--'TCC',-->
            <!--'TYP',-->
            <!--'ITB',-->
            <!--'ITX',-->
            <!--'NHC',-->
            <!--'NHT',-->
            <!--'MRF',-->
            <!--'ANR',-->
            <!--'HDR',-->
            <!--'OTH'-->
        <!--];-->
        var ftypes = [];
        var url = $('#btnAssign').data('url');
        $.ajax({
            type: "POST",
            url: $(this).data('url'),
            dataType:'JSON',
            data : [] ,
            success: function (response) {
                ftypes = response;
            },
        });
        return ftypes;
    }
    // end

    $('#close-button').click(function(e) {
        e.preventDefault();
        var that=this;

        if ($._isAttachmentListEmpty()) {
            window.history.back();
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

        if(error) {
            Alerts.error({
                title: 'Attachment error',
                content: 'One or more attachments do not have a selected <b>attachment type</b>.'
            });
            return false;
        }
        return true;
    }

    $(".return-attachment").click(function (e) {
        e.preventDefault();

        var return_attachment = $(this);

        $.ajax({
            url: return_attachment.data('url'),
            dataType: 'json',
            type: 'post',
            data: {
                'encounter': return_attachment.data('encounter'),
                'transmit': return_attachment.data('transmit')
            },
            beforeSend : function () {
                Alerts.loading({ content: 'Contacting Philhealth Web Service...' });
            },
            success: function (response) {
                if (response === true) {
                    location.reload();
                }
            },
            error: function (jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    });
    
    $(".check-status").click(function (e) {
        setTimeout(function(){ location.reload(); }, 2000);
    });

JAVASCRIPT
        , CClientScript::POS_READY);
?>


<div id="myDiv">
    <?php if (Yii::app()->user->hasFlash('error')): ?>

        <div class="flash-error">
            <div class="alert alert-danger" role="alert">
                <?php echo Yii::app()->user->getFlash("error"); ?>
            </div>
        </div>
    <?php endif ?>
</div>
<div class="row-fluid">
    <?php
    $this->widget(
        'bootstrap.widgets.TbDetailView',
        array(
            'data' => $details->encounter,
            'attributes' => array(
                array('label' => "Patient Name", 'value' => $details->encounter->person->getFullName()),
                array('label' => "HRN", 'value' => $details->encounter->pid),
                array('label' => "Case No.", 'value' => $details->encounter->encounter_nr),
            ),
            'type' => 'striped condensed bordered',
        )
    );
    ?>
</div>


<div class="row-fluid">
    <div class="span5">

        <?php
        $url = $service->checkReturn() ? "reupload" : "UploadAttachment";
        $this->widget('eclaims.widgets.AttachmentsUpload', array(
            'url' => $this->createUrl($url, array(
                'transmit_no' => $details->transmit_no,
                'encounter_nr' => $details->encounter_nr,
            )),
            'extra' => array(
                'details' => $details,
                'service' => $service
            ),
            'model' => $attachmentForm,
            'attribute' => 'attachment', // see the attribute?
            'multiple' => true,
            'options' => array(
                'maxFileSize' => 151000000,
                'acceptFileTypes' => 'js:/(\.|\/)(pdf|xml|csv|xlsx|xls)$/i',
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
                    $.fn.yiiGridView.update("returned-attachments");
                }',
                'stop' => 'js:function(e, data) {
                    location.reload();
                }',
                'submit' => 'js:function(e, data) {
                    return $._validateForm();
                }',
            ),
            'formView' => 'eclaims.views.transmittal.uploadtemplates.fileupload-form',
            'uploadView' => 'eclaims.views.transmittal.uploadtemplates.fileupload-item',
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
            'headerButtons' => array(
                array(
                    'class' => 'bootstrap.widgets.TbButtonGroup',
                    'buttons' => array(
                        array(
                            'label' => 'Check Status',
                            'buttonType' => TbButton::BUTTON_LINK,
                            'icon' => 'check',
                            'url' => Yii::app()->createUrl("eclaims/claimStatus/viewStatus", array(
                                    "claim_id" => $claim->id,
                                    "enc_nr" => $claim->encounter_nr,
                                    "searchin" => '',
                                    "update_status" => 1,
                                    "current_page" => ''
                                )
                            ),
                            'htmlOptions' => array(
                                'target' => "_blank",
                                'class' => 'check-status btn-secondary',
                            ),
                        ),
                    ),
                ),
            ),
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
            'htmlOptions' => array('enctype' => 'multipart/form-data'),
        ));

        echo CHtml::hiddenField('id', null, array(
            'id' => 'remove-attachment-id'
        ));

        ?>
        <!-- Mod jeff 06-07-18 -->
        <?php
        $this->widget('bootstrap.widgets.TbGridView', array(
            'id' => 'uploaded-attachments',
            'type' => 'striped condensed bordered hover',
            'dataProvider' => $details->searchAttachments(),
            'template' => "{items}</br>{pager}",
            'columns' => array(
                array(
                    'header' => 'Type',
                    'type' => 'raw',
                    'value' => '"<span class=\"label label-info\">{$data->attachment_type}</span>"'
                ),
                array(
                    'header' => 'File name',
                    'type' => 'raw',
                    'value' => function ($data) {
                        $fileUrl = $data->getUrl();
                        if ($fileUrl) {
                            $item = CHtml::link($data->filename, $fileUrl, array('target' => '_blank'));
                        } else {
                            $item = $data->filename;
                        }
                        return "<b>{$item}</b><br/><small>Size: {$data->FileSize}</small> <small style=\"color:#888\">(Hash: {$data->hash})</small>";
                    }
                ),
                array(
                    'header' => 'RTH File',
                    'type' => 'raw',
                    'value' => function ($data) {
                        return $data->is_return ? '<b style="color: red;">YES</b>' : '<b>NO</b>' ;
                    }
                ),
                array(
                    'header' => 'Actions',
                    'type' => 'raw',
                    'value' => function ($data) {
                        Yii::app()->controller->widget('bootstrap.widgets.TbButton', array(
                                'buttonType' => 'submit',
                                'size' => 'mini',
                                'label' => 'Remove',
                                'icon' => 'fa fa-ban',
                                'htmlOptions' => array(
                                    'class' => 'remove-attachment-btn',
                                    'value' => $data->id
                                )
                            )
                        );
                    }
                ),
            )
        ));
        ?>

        <?php $this->endWidget(); // Form ?>
        <?php $this->endWidget(); // SegBox2 ?>

        <?php

        // Added by Johnmel --- Table for the list of return attachment/files 07-04-2018
        if ($service->checkReturn()) {
            $this->beginWidget('application.widgets.SegBox', array(
                'title' => 'List of returned attachments',
                'headerButtons' => array(
                    array(
                        'class'       => 'bootstrap.widgets.TbButtonGroup',
                        'buttons'     => array(
                            array(
                                'label'       => 'Refile to PHIC',
                                'buttonType'  => 'button',
                                'htmlOptions' => array(
                                    'class'          => 'return-attachment btn-success',
                                    'data-url'       => $this->createUrl(
                                        'addDocument'
                                    ),
                                    'data-encounter' => $details->encounter_nr,
                                    'data-transmit'  => $details->transmit_no,
                                ),
                                'visible' => $service->checkReturn(),

                            ),
                        ),
                        'htmlOptions' => array(
                            'class' => 'fileupload-buttonbar',
                        ),
                    ),

                ),
                'htmlOptions' => array(
                    'visible' => false,

                    'class' => 'bootstrap-widget-table'
                )
            ));


            echo CHtml::hiddenField('id', null, array(
                'id' => 'remove-attachment-id'
            ));

            ?>

            <?php

            $this->widget('bootstrap.widgets.TbGridView', array(
                'id' => 'returned-attachments',
                'type' => 'striped condensed bordered hover',
                'dataProvider' => $details->searchReturned(),
                'template' => "{items}{pager}",
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
                        'value' => function ($data) {
                            Yii::app()->controller->widget('bootstrap.widgets.TbButton', array(
                                    'buttonType' => 'submit',
                                    'size' => 'mini',
                                    'label' => 'Remove',
                                    'icon' => 'fa fa-ban',
                                    'htmlOptions' => array(
                                        'class' => 'remove-attachment-btn',
                                        'value' => $data->id
                                    )
                                )
                            );
                        }
                    ),
                )
            ));

            $this->endWidget();
        } // SegBox2 ?>
    </div>
</div>


<?php

$this->beginWidget(
    'bootstrap.widgets.TbModal',
    array(
        'id' => 'cf4Modal',
        'htmlOptions' => array(
            'style' => "width:1250px;margin-left:-625px; overflow-y: auto; margin-top:-55px;",
            'data-backdrop' => "static",
        ),
    )
); ?>


<div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h4><i class="color-blue fa fa-search"></i> CF4 Xml Generator</h4>
</div>

<div class="modal-body" style="min-height:540px;">


</div>


<div class="modal-footer">

    <?php
    //    $this->widget(
    //        'bootstrap.widgets.TbButton',
    //        array(
    //            'buttonType' => 'submit',
    //            'type' => 'primary',
    //            'icon' => 'fa fa-upload',
    //            'label' => 'Upload CF4',
    //            'htmlOptions' => array(
    //                'data-encounter' => $details->encounter_nr,
    //                'data-transno' => '',
    //                'id' => 'uploadCF4'
    //            )
    //        )
    //    );

    $this->widget(
        'bootstrap.widgets.TbButton',
        array(
            'type' => 'success',
            'icon' => 'fa fa-upload',
            'label' => 'Download CF4',
            'htmlOptions' => array(
                'data-encounter' => $details->encounter_nr,
                'data-transno' => '',
                'id' => 'downloadCF4',
            )
        )
    );

    $this->widget(
        'bootstrap.widgets.TbButton',
        array(
            'label' => 'Close',
            'url' => '#',
            'htmlOptions' => array('data-dismiss' => 'modal'),
        )
    );
    ?>

</div>
<?php $this->endWidget(); ?>

