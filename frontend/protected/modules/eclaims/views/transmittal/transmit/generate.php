<?php
Yii::import('bootstrap.widgets.TbButton');

$this->breadcrumbs[] = $transmittal->transmit_no;

// /* urls for javascript */
// $info = $transmittal->xml_cache;
// $processUrl = $this->createUrl('transmittal/processXml');
// $generateUrl = $this->createUrl('transmittal/generateXml');
// $mapUrl = $this->createUrl('transmittal/map');
// $uploadUrl = $this->createUrl('transmittal/upload');
// $url = $this->createUrl('transmittal/index');
// $showUrl = $this->createUrl('transmittal/showXML');

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
    height: 35em;
}
STYLE
    )->registerScriptFile($baseUrl . '/js/codemirror/lib/codemirror.js')
    ->registerScriptFile($baseUrl . '/js/codemirror/mode/xml/xml.js')
    ->registerScriptFile($baseUrl . '/js/codemirror/addon/display/placeholder.js')
    ->registerScriptFile($baseUrl . '/js/codemirror/addon/hint/show-hint.js')
    ->registerScriptFile($baseUrl . '/js/codemirror/addon/hint/xml-hint.js')
    ->registerScriptFile($baseUrl . '/js/codemirror/addon/selection/active-line.js')
    ->registerScriptFile($baseUrl . '/js/frontend/eclaims/transmittal/tags.js')
    ->registerScriptFile($baseUrl . '/js/frontend/eclaims/transmittal/generate.js', CClientScript::POS_END);


$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'generate-form',
    'type'=>'horizontal',
));


$this->beginWidget('application.widgets.SegBox', array(
    'title' => ' ',
    'headerButtons' => array(
        array(
            'class' => 'bootstrap.widgets.TbButtonGroup',
            'buttons' => array(
                // 'upload'=> array(
                //      'label' => 'Upload',
                //      'type' => 'success',
                //      'icon' => 'fa fa-cloud-upload',
                //      'disabled' => $transmittal->xml_is_valid == false,
                //      'buttonType'=>TbButton::BUTTON_BUTTON,
                //      'htmlOptions' => array('id' => 'uploadBtn',),
                //  ),
                'generate'=> array(
                    'label' => 'Generate',
                    'icon' => 'fa fa-file-code-o',
                    'disabled' => empty($transmittal->transmit_no),
                    'buttonType'=>TbButton::BUTTON_BUTTON,
                    'htmlOptions' => array(
                        'id' => 'generateBtn',
                        'data-action' => $this->createUrl('generateXml', array(
                            'id' => $transmittal->transmit_no
                        ))
                    ),
                ),
                'validate'=> array(
                    'label' => 'Validate',
                    'icon' => 'fa fa-check-circle-o',
                    'buttonType'=>TbButton::BUTTON_BUTTON,
                    'htmlOptions' => array(
                        'id' => 'validateBtn',
                        'data-action' => $this->createUrl('validateXml', array())
                    ),
                ),
                'error'=> array(
                    'label' => '<i class="fa fa-exclamation-triangle"></i> Show errors <span class="badge badge-important">'.($errors['count'] ? $errors['count'] : '').'</span>',
                    'encodeLabel' => false,
                    'buttonType'=>TbButton::BUTTON_BUTTON,
                    'htmlOptions'=>array(
                        'id'=>'errorBtn',
                        'disabled' => !$errors['count'],
                    )
                ),

            ),
        ),
        array(
            'class' => 'bootstrap.widgets.TbButtonGroup',
            'buttons' => array(
                array(
                    'label' => 'Save',
                    'icon' => 'fa fa-save',
                    'type' => 'success',
                    'buttonType'=>TbButton::BUTTON_SUBMIT,
                    'htmlOptions' => array(
                        'id' => 'saveBtn',
                        'disabled' => trim($transmittal->ext->xml_cache) == ''
                    ),
                ),
                array(
                    'label' => 'Reset',
                    'icon' => 'fa fa-retweet',
                    'buttonType'=>TbButton::BUTTON_SUBMIT,
                    'htmlOptions' => array(
                        'id'=>'resetBtn',
                        'data-action' => $this->createUrl('resetXml', array(
                            'id' => $transmittal->transmit_no
                        ))
                    ),
                )
            )
        )
    ),
));

?>
    <div class="row-fluid">
        <div class="span12">
            <div class="form">

                <?php
                    echo $form->textArea($transmittal->ext, 'xml_cache', array(
                        'id' => 'xmlTextArea' ,
                        'class'=>'input-level-block',
                        'rows'=>20,
                        'placeholder' => 'There is no existing XML data. Please click the "Generate" button',
                        'spellcheck' => 'false',
                    ));

                    // echo $form->hiddenField($transmittal, 'xml_cache', array(
                    //     'id' => 'xml_cache'
                    // ));
                    // echo $form->hiddenField($transmittal, 'transmit_no', array(
                    //     'id' => 'transmit_no'
                    // ));
                    // echo $form->hiddenField($transmittal, 'xml_is_valid', array(
                    //     'id' => 'xml_is_valid'
                    // ));
                ?>
            </div>
        </div>
    </div>

<?php $this->endWidget(); // Box ?>

<?php $this->endWidget(); // Form ?>

<?php
    $this->beginWidget('bootstrap.widgets.TbModal',
        array('id'=>'errorModal',
            'htmlOptions' => array('style' => 'width: 1000px; margin-left:-500px;'),
        )
    );
?>

<div class="modal-header">
    <h4>Validation Errors</h4>
</div>

<div class="modal-body">

<?php

if ($errors['errors']) {
    foreach ($errors['errors'] as $header => $error) {

        $this->beginWidget('application.widgets.SegBox', array(
            'title' => $header,
            'headerIcon' => 'fa fa-user',
            'htmlOptions' => array(
                'class' => 'bootstrap-widget-table'
            )
        ));


        $dp = new CArrayDataProvider($error);
        $this->widget('bootstrap.widgets.TbGridView', array(
            'dataProvider' => $dp,
            'type' => 'striped condensed bordered',
            'template' => "{items}\n",
            'hideHeader' => true,
            'columns' => array(
                array(
                    'value' => '$row+1'
                ),
                array(
                    'value' => '$data'
                )
            )
        ));

        $this->endWidget();

    }
}


?>

</div>

<div class="modal-footer">
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'label'=>'Close',
        'type' => 'inverse',
        'url' => '#',
        'htmlOptions'=>array('data-dismiss'=>'modal'),
    )); ?>
</div>

<?php $this->endWidget(); // Modal ?>

<!-- Template for JavaScript-based validation errors -->
<script id="validationErrorsTemplate" type="text/template">
<?php
$this->beginWidget('application.widgets.SegBox', array(
    'title' => '{{{header}}}',
    'headerIcon' => 'fa fa-user',
    'htmlOptions' => array(
        'class' => 'bootstrap-widget-table'
    )
));
?>

<table class="detail-view table table-striped table-bordered table-condensed table-hover">
    <tbody>
{{#errors}}
        <tr>
            <td>{{{index}}}</td>
            <td>{{{error}}}</td>
        </tr>
{{/errors}}
    </tbody>
</table>

<?php $this->endWidget(); ?>
</script>