<?php
/**
 * Renders:
 * 
 * @author Jolly Caralos <jadcaralos@gmail.com>
 * @copyright Copyright &copy; 2013-2014. Segworks Technologies Corporation
 * @since 1.0
 * 
 * @package 
 * 
 * @var $this TransmittalController
 * @var $attachmentForm ClaimAttachmentForm
 * @var $details EclaimsTransmittalDetails
 */

Yii::import('bootstrap.helpers.TbHtml');
Yii::import('bootstrap.widgets.TbActiveForm');
Yii::import('bootstrap.widgets.TbButton');

$this->breadcrumbs[$details->transmit_no] = array('attachments', 'id' => $details->transmit_no);
$this->breadcrumbs[] = 'Manage attachments';

/* @var $this Controller */
/* @var $details EclaimsTransmittalDetails */

$this->setPageTitle('Manage claim attachments');

$baseUrl = Yii::app()->getRequest()->baseUrl;
$cs = Yii::app()->getClientScript();
$cs
->registerCssFile($baseUrl .  '/css/frontend/uploadsorter.css')
->registerScriptFile($baseUrl . '/js/frontend/eclaims/transmittal/uploadsorter.js');
?>
<div class="row-fluid">
    <div class="span12">
        <?php
        $this->widget('bootstrap.widgets.TbFileUpload', array(
            'url'         => $this->createUrl("uploadAttachment", array(
                'transmit_no' => $details->transmit_no,
                'encounter_nr' => $details->encounter_nr,
            )),
            'model'       => $attachmentForm,
            'attribute'   => 'attachment', // see the attribute?
            'multiple'    => true,
            'options'     => array(
                'maxFileSize' => 2000000,
                'acceptFileTypes' => 'js:/(\.|\/)(pdf)$/i',
                'add' => 'js:function (e, data) {
                    var that = this;
                    $.blueimp.fileupload.prototype
                        .options.add.call(that, e, data);

                    var id = new Date().getTime();
                    data.paramName = data.paramName + "["+ id +"]";

                    var attachmentInput = $("ul.files > li:last .attachment-type");
                    attachmentInput.attr("name", attachmentInput.attr("name") + "["+ id +"]");
                }'
            ),
            'formView'     => 'eclaims.views.transmittal.multiupload.fileupload-form',
            'uploadView'   => 'eclaims.views.transmittal.multiupload.fileupload-item',
            'downloadView' => 'eclaims.views.transmittal.multiupload.fileupload-download',
            'htmlOptions' => array(
                'class' => 'multi-upload'
            )
        ));
        ?>
    </div>
</div>