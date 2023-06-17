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
$cs->registerScript('transmittal.manageAttachments', <<<JAVASCRIPT

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

$('#attachments-form-submit').off('click').on('click', function() {
    $('#attachments-form').submit();
});

$('#attachments-form').off('submit').on('submit', function(e) {
    var form = $(this);
//    var data = new FormData();
    var files = $('.attachment-file');

    var error = '';
    files.each(function(index) {
        var file = this.files[0];
        if ('undefined' == typeof file) {
            error = 'One or more attachments do not have a <b>file selected</b>.';
            return false;
        }
//        data.append('attachment[]', file, file.name);

        var type = $('.attachment-type:eq(' + index + ')').val();
        if (!type) {
            error = 'One or more attachments do not have a selected <b>attachment type</b>.';
            return false;
        }
//        data.append('type[]', type);
    });

    if (error) {
        Alerts.error({
            title: 'Attachment error',
            content: error
        });
        return false;
    } else {
        Alerts.loading({
            title: 'Please wait',
            content: 'Uploading attachments to the HIE server...'
        });
        return true;
//        $.ajax({
//            url: form.prop('action'),
//            type: 'POST',
//            data: data,
//            processData: false,
//            contentType: false
//        });
    }
});

$('#close-button').click(function(e) {
    e.preventDefault();
    var that=this;

    if ($._hasPendingUploads()) {
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
    } else {
        window.location.href = that.href;
    }
});

$('#add-attachment').click(function() {
    $._addAttachmentRow();
});

$('#clear-attachments').click(function() {
    $._clearEmptyAttachments();
});

// add
$._addAttachmentRow = function() {
    var id = new Date().getTime();
    var template = $('#attachment-template').html();
    var html = Mustache.to_html(template, {
        rowID: id
    });
    $('#attachments-grid tbody').append(html);
};

// empty?
$._isAttachmentsEmpty = function() {
    return !$('#attachments-grid .attachment-file').length;
};

//
$._hasPendingUploads = function() {
    var found = false;
    var files = $('#attachments-grid .attachment-file');
    files.each(function() {
        if (this.files[0]) {
            found = true;
            return false;
        }
    });
    return found;
};

// clear all
$._clearEmptyAttachments = function(obj) {
    var files = $('#attachments-grid .attachment-file');
    files.each(function() {
        if (!this.files[0]) {
            $._removeAttachmentRow(this);
        }
    });
    if ($._isAttachmentsEmpty()) {
        $._addAttachmentRow();
    }
};

// delete
$._removeAttachmentRow = function(obj) {
    var file = $(obj).parent().parent().find(':file')[0];
    if (file && file.files[0]) {
        Alerts.confirm({
            title: 'Delete this attachment?',
            content: '',
            callback: function(result) {
                if (result) {
                    $(obj).parents('tr').first().remove();
                    if ($._isAttachmentsEmpty()) {
                        $._addAttachmentRow();
                    }
                }
            }
        });
    } else {
        $(obj).parents('tr').first().remove();
        if ($._isAttachmentsEmpty()) {
            $._addAttachmentRow();
        }
    }
};

$._addAttachmentRow();
JAVASCRIPT
, CClientScript::POS_READY);

?>

<div class="row-fluid">
    <div class="span5">

    <?php
        $this->beginWidget('application.widgets.SegBox', array(
            'title' => '',
            'headerButtons' => array(
                array(
                    'class' => 'bootstrap.widgets.TbButtonGroup',
                    'buttons' => array(
                        array(
                            'label' => 'Add attachment',
                            'htmlOptions' => array(
                                'id' => 'add-attachment'
                            )
                        ),
                        array(
                            'label' => 'Clear empty attachments',
                            'htmlOptions' => array(
                                'id' => 'clear-attachments'
                            )
                        )
                    ),
                )
            ),
            'footer' => CHtml::tag('div', array('class' => 'form-actions'),

                $this->widget('bootstrap.widgets.TbButton', array(
                    'id' => 'attachments-form-submit',
                    'label' => 'Upload attachment/s',
                    'type' => TbButton::TYPE_PRIMARY,
                    'buttonType' => TbButton::BUTTON_BUTTON,
                ), true) . ' ' .

                $this->widget('bootstrap.widgets.TbButton', array(
                    'id' => 'close-button',
                    'label' => 'Close',
                    'buttonType' => TbButton::BUTTON_LINK,
                    'url' => $this->createUrl('attachments', array('id' => $details->transmit_no))
                ), true)
            )
        ));
    ?>


        <?php
            $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
                'id' => 'attachments-form',
                'type' => TbActiveForm::TYPE_VERTICAL,
                'action' => $this->createUrl('uploadAttachment', array(
                    'transmit_no' => $details->transmit_no,
                    'encounter_nr' => $details->encounter_nr,
                )),
                'htmlOptions'=>array('enctype'=>'multipart/form-data'),
            ));
            /* @var $form TbActiveForm */
        ?>
        <script id="attachment-template" type="text/template">
            <tr>
                <td>
                    <?php
                        $types = CMap::mergeArray(array('' => '-Select type-'), ClaimAttachment::getAttachmentTypes());
                        echo CHtml::activeDropDownList($attachmentForm, 'type[{{rowID}}]', $types, array('class' => 'input-block-level attachment-type'));
                    ?>
                </td>
                <td>
                    <?php echo CHtml::activeFileField($attachmentForm, 'attachment[{{rowID}}]', array('class' => 'input-block-level attachment-file')); ?>
                </td>
                <td class="button-column">
                    <?php
                        $this->widget('bootstrap.widgets.TbButtonGroup', array(
                            'buttonType' => TbButton::BUTTON_LINK,
                            'size' => TbButton::SIZE_MINI,
                            'buttons' => array(
                                array(
                                    'icon' => 'fa fa-minus',
                                    'class' => 'attachment-row-delete',
                                    'htmlOptions' => array(
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Remove this attachment',
                                    )
                                ),
                            ),
                            'htmlOptions' => array(
                                'onClick' => '$._removeAttachmentRow(this)'
                            )
                        ));
                    ?>
                </td>
            </tr>
        </script>
        <div class="row-fluid">
            <div class="span12">
                <div class="grid-view">
                    <table id="attachments-grid" class="table table-striped table-condensed table-bordered">
                        <thead>
                            <tr>
                                <th>Document type</th>
                                <th>Attachment</th>
                                <th class="button-column"></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php $this->endWidget() /* Form */?>
        <?php $this->endWidget() /* Box */ ?>

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



