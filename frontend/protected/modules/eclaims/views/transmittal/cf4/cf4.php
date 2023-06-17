<?php
/**
 * Created by PhpStorm.
 * User: Bender
 * Date: 3/19/2019
 * Time: 12:18 AM
 */

$cs = Yii::app()->clientScript;
$baseUrl = $baseUrl = Yii::app()->request->baseUrl;

$cs
    ->registerCssFile($baseUrl . '/css/codemirror/lib/codemirror.css')
    ->registerCssFile($baseUrl . '/css/codemirror/theme/ambiance.css')
    ->registerCssFile($baseUrl . '/css/codemirror/addon/hint/show-hint.css')
    ->registerCss('transmittal.cf4', <<<STYLE
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
    ->registerScriptFile($baseUrl . '/js/frontend/eclaims/transmittal/tags.js');
//    ->registerScriptFile($baseUrl . '/js/frontend/eclaims/transmittal/generate.js', CClientScript::POS_END);


$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id' => 'cf4-form',
    'type' => 'horizontal',
    'htmlOptions' => array(
        'data-url' => $this->createUrl('UploadCf4', array('id' => $model->phic_trans_no)),
        'data-url-Download' => $this->createUrl('DownloadCF4', array('id' => $model->phic_trans_no))

    )
));


Yii::app()->clientScript->registerScript('transmittal.showXML', <<<JSCRIPT
    
    /* Initialize CodeMirror */
    var editor = CodeMirror.fromTextArea(document.getElementById('cf4TextArea'), {
        lineNumbers: true,
        styleActiveLine: true,
        theme: "ambiance",
        mode: "xml",
        onKeyEvent : function (editor, e) {
            hotkey($.event.fix(e));
        },
        extraKeys:{
        Backspace: function(e){
                $('#saveBtn').removeAttr('disabled');
                return CodeMirror.Pass;
            },
        Enter: function(e){
                $('#saveBtn').removeAttr('disabled');
                return CodeMirror.Pass;
                }
            }
        });
    
    $( document ).ready(function(e){
    
        if($('#xml_is_valid').val() == true) {
            setFlash('Sucess','Transmittal XML is valid and ready for upload', 'success');
        }
        $('#saveBtn').attr('disabled','disabled');
    });

JSCRIPT
    , CClientScript::POS_LOAD);

?>

<div class="row-fluid">
    <div class="span12 alert alert-success">
        <i class="fa fa-question-circle"></i> Eclaims CF4 Successfully Generated without errors
        <strong>Click download</strong> to upload the XML file on attachments
    </div>
</div>

<div class="row-fluid">
    <div class="span12">
        <?php
        echo $form->hiddenField($model, 'phic_trans_no',
            array(
                'readonly' => 'readonly',
                'type' => 'hidden',
                'id' => 'transNo'
            ));

        echo $form->textArea($model, 'xml', array(
            'id' => 'cf4TextArea',
            'class' => 'input-level-block',
            'rows' => 20,
            'placeholder' => 'There is no existing XML data. Please click the "Generate" button',
            'spellcheck' => 'false',
        ));
        ?>
    </div>
</div>

<?php $this->endWidget(); // Box ?>


<script>
    $(document).ready(function (e) {
        var editor = CodeMirror.fromTextArea(document.getElementById('cf4TextArea'), {
            lineNumbers: true,
            styleActiveLine: true,
            theme: "ambiance",
            mode: "xml",
            lineWrapping: true,
            onKeyEvent: function (editor, e) {
                hotkey($.event.fix(e));
            },
            extraKeys: {
                Backspace: function (e) {
                    return CodeMirror.Pass;
                },
                Enter: function (e) {
                    return CodeMirror.Pass;
                }
            }
        });
    });

    $("#uploadCF4").click(function (e) {
        e.stopPropagation();
        var form = $('#cf4-form');

        $.ajax({
            type: "GET",
            url: form.data('url'),
            dataType: 'json',
            beforeSend: function () {
                Alerts.loading({
                    'title': 'Please wait...',
                    content: 'Generating Eclaims CF4'
                });
            },
            complete: function (data) {
                Alerts.alert({
                    icon: 'icon-check',
                    title: "success",
                    content: "CF4 Generated Please Download",
                    callback: function (data) {
                        $('#' + 'uploaded-attachments').yiiGridView('update');
                    }
                });
            },
        });
    });

    $("#downloadCF4").click(function (e) {
        e.stopPropagation();
        var form = $('#cf4-form');

        console.log(form.data('urlDownload'));
        $.ajax({
            type: "GET",
            url: form.data('urlDownload'),
            dataType: 'json',
            beforeSend: function () {
                Alerts.loading({
                    'title': 'Please wait...',
                    content: 'Generating Eclaims CF4'
                });
            },
            complete: function (data) {
                Alerts.alert({
                    icon: 'icon-check',
                    title: "success",
                    content: "CF4 Generated Please Download",
                    callback: function (data) {
                        window.location = form.data('urlDownload');

                    }
                });
            },
        });

    });


</script>

