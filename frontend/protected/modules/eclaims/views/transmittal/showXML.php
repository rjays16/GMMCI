<?php

$this->setPageTitle('View Transmittal');
$this->breadcrumbs[] = 'XML';

/* incase of empty transmittal object */
if(!$transmittal && $transmit_no) {
    $transmittal = Transmittal::model()->findByPk($transmit_no);
}

/* urls for javascript */
$info = $transmittal->xml_data;
$processUrl = $this->createUrl('transmittal/processXml');
$generateUrl = $this->createUrl('transmittal/generateXml');
$mapUrl = $this->createUrl('transmittal/map');
$uploadUrl = $this->createUrl('transmittal/upload');
$url = $this->createUrl('transmittal/index');
$showUrl = $this->createUrl('transmittal/showXML');

?>

<div id='alert-flash'></div>

<?php
Yii::app()->clientScript->registerScript('transmittal.showXML', <<<JSCRIPT

/* Initialize CodeMirror */
var editor = CodeMirror.fromTextArea(document.getElementById('xmlTextArea'), {
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
    if($('#xmlTextArea').val() === '') {
        $('#validateBtn').attr('disabled','disabled');
        $('#resetBtn').attr('disabled','disabled');
    }

    if($('#xml_is_valid').val() == true) {
        setFlash('Sucess','Transmittal XML is valid and ready for upload', 'success');
    }
    $('#saveBtn').attr('disabled','disabled');

});

/* Handle 'keydown' event on CodeMirror */
var hotkey = function (e) {
    if($('#xml').val() !== '') {
    $('#saveBtn').removeAttr('disabled');
        $('#validateBtn').removeAttr('disabled');
        }
    $('#uploadBtn').attr('disabled','disabled');
};
$(document).keypress(hotkey);

/* Handles 'Generate' button click event */
$('#generateBtn').on('click',function(e){
    e.preventDefault();
     $.ajax({
		type: 'POST',
		url: '{$generateUrl}',
		data: 'transmit_no=' + $('#transmit_no').val(),
		beforeSend: function() {
			Alerts.loading({ content: 'Generating Transmittal XML. This may take a while..' });
        },
	}).done(function(data) {
        Alerts.close();
        if(data == 'true'){
        	Alerts.warn({ title: 'Success!', content: 'Transmittal XML successfully generated and saved', icon: 'fa-check-circle-o', iconColor: '#2DCC70' , actions: ''});
		location.reload();
        } else {
            Alerts.error({ title: 'Ooops!', content: 'Something went wrong while generating the Transmittal XML. Please try again', icon: 'fa-frown-o'});
        }
	}).error(function(jqXHR, textStatus, errorThrown) {
		Alerts.error({ content: 'Error in generating Transmittal XML'});
	});
      });

/* Handles 'Validate' button click event */
$('#validateBtn').on('click',function(e){
    $('.modal-body').html('');
    var xmlString = editor.getValue();
    e.preventDefault();
     $.ajax({
        type: 'POST',
        dataType: 'json',
        data: 'process=validate' + '&xmlString=' + xmlString + '&transmit_no=' + $('#transmit_no').val(),
        url: '{$processUrl}',
		beforeSend: function() {
			Alerts.loading({ content: 'Validating Transmittal XML' });
        },
	}).done(function(data) {
        Alerts.close();
        if(data == true){
            Alerts.warn({ title: 'Success!', content: 'Transmittal XML is valid. Please save your changes', icon: 'fa-check-circle-o', iconColor: '#2DCC70'});
            $('#saveBtn').removeAttr('disabled', 'disabled');
            setFlash('Reminder', 'Please save the changes you have made', 'info');
            $('#errorBtn').attr('disabled', 'disabled');
        } else if(data == false) {
            Alerts.error({ title: 'Syntax Error', content: 'Transmittal XML has syntax errors'});
            $('#uploadBtn').attr('disabled', 'disabled');
            $('#errorBtn').attr('disabled', 'disabled');
            $('#yw5').html('');
        } else if(!data) {
            Alerts.error({ title: 'Ooops!', content: 'Something is wrong with the Transmittal XML (Hint: Please check the number of claim tags)', icon: 'fa-frown-o'});
        } else {
            Alerts.error({ title: 'XML Error', content: 'Transmittal XML has validation errors. Please view XML deficiencies '});
            $('#yw5').html('');
            $('#errorBtn').removeAttr('disabled', 'disabled');
            $('#uploadBtn').attr('disabled', 'disabled');
            setTimeout(populateErrors(data), 5000);
        }
	}).error(function(jqXHR, textStatus, errorThrown) {
		Alerts.error({ content: 'Error in validating Transmittal XML'});
	});
      });

/* Handles 'Save' button click event */
$('#saveBtn').on('click',function(e){
    $('.modal-body').html('');
    var xmlString = editor.getValue();
    e.preventDefault();
     $.ajax({
		type: 'POST',
        data: 'process=save' + '&xmlString=' + xmlString +'&transmit_no=' + $('#transmit_no').val(),
        dataType: 'json',
        url: '{$processUrl}',
		beforeSend: function() {
			Alerts.loading({ content: 'Saving Transmittal XML' });
        },
      }).done(function(data) {
        Alerts.close();
        if(data == true){
            Alerts.warn({ title: 'Success!', content: 'Transmittal XML successfully saved and is valid', icon: 'fa-check-circle-o', iconColor: '#2DCC70', actions: '' });
            location.reload();
        } else if(data == false) {
            Alerts.error({ title: 'Syntax Error', content: 'Transmittal XML has syntax errors'});
            $('#uploadBtn').attr('disabled', 'disabled');
            $('#yw5').html('');
        } else {
            Alerts.error({ title: 'XML Error', content: 'Transmittal XML has validation errors. Please view XML deficiencies '});
            $('#yw5').remove();
            $('#errorBtn').removeAttr('disabled', 'disabled');
            $('#uploadBtn').attr('disabled', 'disabled');
            setTimeout(populateErrors(data), 5000);
        }
                }).error(function(jqXHR, textStatus, errorThrown) {
		Alerts.error({ content: 'Error in saving Transmittal XML'});
	});
});

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

/**
* Dynamically populates XML Deficiencies without reloading or rendering new page
* @param JSONobject data - contains errors to display
*/
function populateErrors(data) {
    if($('.modal-body').val() === ''){
         $.each(data, function(index, element) {
            var modal = $(document).find('div.modal-body');

            $(modal).append($('<div>', {
                class: 'bootstrap-widget',
                id: 'case' + index,
            }));

            var widget = $(modal).find('div.bootstrap-widget#case' + index);

            $(widget).append($('<div>', {
                class: 'bootstrap-widget-header',
                id: 'header' + index,
            }));

            var header = $(widget).find('div.bootstrap-widget-header#header' + index);

            $(header).append($('<h3>', {
                text: 'Claim Number: ' + element['claimNumber']
            }));

            $(widget).append($('<div>', {
                class: 'bootstrap-widget-content',
                id: 'content' + index,
            }));

            var content = $(widget).find('div.bootstrap-widget-content#content' + index);

            $(content).append($('<div>', {
                class: 'row-fluid',
                id: 'row' + index,
            }));

            var fluid = $(content).find('div.row-fluid#row' + index);

             $(fluid).append($('<div>', {
                class: 'span12',
                id: 'span' + index,
            }));

            var span = $(fluid).find('div.span12#span' + index);

            $(span).append($('<table>', {
                class: 'detail-view table table-striped table-bordered table-condensed',
                id: 'table' + index,
            }));

            var table = $(fluid).find('table#table' + index);

            $(table).append($('<tbody>', {
                id: 'tbody' + index,
            }));

            var tbody = $(table).find('tbody');

            $.each(element['errors'], function(i, element) {

                 var tr = $('<tr>', {id: 'row' + i});

                 if(index%2 == 0) {
                    tr.attr('class', 'odd');
                 } else if(index%2 != 0) {
                    tr.attr('class', 'even');
                 }

                $(tbody).append(tr);

                var tr = $(table).find('tr#row'+i);

                $(tr).append($('<th>', {
                    style: 'width:1%',
                    text: i+1
                }));

                $(tr).append($('<td>', {
                    text: element
                }));

            });

                });
                }
        }


/* Handles 'XML Deficiencies' button click event */
$('#errorBtn').click(function(e) {
    e.preventDefault();
    $('#error').modal();
});

/* Handles 'Reset' button click event */
$('#resetBtn').on('click',function(e){
    editor.setValue($('#xml').val());
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
            $("#error .modal-header h4").html("Upload Deficiencies");
            $("#error .modal-body").html(data);
        }
    }).error(function(jqXHR, textStatus, errorThrown) {
        setFlash('Error',textStatus + ' ' + errorThrown, 'error');
        Alerts.error({ title: data, content: 'Error in accessing the upload web service. '});
    });
});

$('[data-url]').click(function(e) {
	e.preventDefault();
	window.location = $(this).data('url');
    });

JSCRIPT
    ,CClientScript::POS_LOAD);

Yii::import('bootstrap.widgets.TbButton');

$this->beginWidget('bootstrap.widgets.TbBox', array(
	'title' => ' ',
    'headerButtons' => array(
        array(
            'class' => 'bootstrap.widgets.TbButtonGroup',
            'buttons' => array(
               'upload'=> array(
                    'label' => 'Upload',
                    'type' => 'success',
                    'icon' => 'fa fa-cloud-upload',
                    'disabled' => $transmittal->xml_is_valid == false,
                    'buttonType'=>TbButton::BUTTON_BUTTON,
                    'htmlOptions' => array('id' => 'uploadBtn',),
                ),
            )
        ),
        array(
            'class' => 'bootstrap.widgets.TbButtonGroup',
            'buttons' => array(
               'error'=> array(
                    'label' => 'XML Deficiencies',
                    'type' => 'danger',
                    'icon' => 'fa fa-exclamation-triangle',
                    'buttonType'=>TbButton::BUTTON_BUTTON,
                    'htmlOptions'=>array(
                        'id'=>'errorBtn',
                        'disabled' => 'disabled',
                    )

                ),
                ),
        ),
        array(
            'class' => 'bootstrap.widgets.TbButtonGroup',
            'buttons' => array(
               'validate'=> array(
                    'label' => 'Validate',
					'type' => 'primary',
                    'icon' => 'fa fa-check-circle-o',
                    'buttonType'=>TbButton::BUTTON_BUTTON,
                    'htmlOptions' => array(
                        'id' => 'validateBtn',
                    ),
                ),
                ),
        )    ,
        array(
            'class' => 'bootstrap.widgets.TbButtonGroup',
            'buttons' => array(
               'generate'=> array(
                    'label' => 'Generate',
                    'type' => 'primary',
                    'icon' => 'fa fa-file-code-o',
					'disabled' => empty($transmittal->transmit_no),
                    'buttonType'=>TbButton::BUTTON_BUTTON,
                    'htmlOptions' => array(
                        'id' => 'generateBtn',
				),
			),
                    )
                ),
	),
));

?>

<div class="form">

	<?php
		$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
		'id'=>'horizontalForm',
		'type'=>'horizontal',
		));
	?>

	<?php
		if(!$transmittal->xml_data || trim($transmittal->xml_data) == '') {
            echo $form->textAreaRow($transmittal, 'xml_data', array('id' => 'xmlTextArea' ,'class'=>'span11', 'rows'=>20, 'placeholder' => 'There is no existing XML data. Please click the "Generate" button',));
		}
		else {
            echo $form->textAreaRow($transmittal, 'xml_data', array('id' => 'xmlTextArea' ,'class'=>'span11', 'rows'=>20, 'value' => $transmittal->xml_data, 'style' =>'resize:none', 'spellcheck' => 'false'));
		}
        echo $form->hiddenField($transmittal, 'xml_data', array('id' => 'xml', 'value' => $transmittal->xml_data));
        echo $form->hiddenField($transmittal, 'transmit_no', array('id' => 'transmit_no', 'value' => $transmittal->transmit_no));
        echo $form->hiddenField($transmittal, 'xml_is_valid', array('id' => 'xml_is_valid', 'value' => $transmittal->xml_is_valid));
	?>

	<div class="row-fluid" align="right">
<?php $this->widget('bootstrap.widgets.TbButton', array(
        'label' => 'Save',
        'icon' => 'fa fa-save',
		'type' => 'primary',
		'buttonType'=>TbButton::BUTTON_BUTTON,
        'htmlOptions' => array(
            'id' => 'saveBtn',
        ),
    ));
 ?>

<?php $this->widget('bootstrap.widgets.TbButton', array(
        'label' => 'Reset',
		'type' => 'primary',
        'icon' => 'fa fa-retweet',
		'buttonType'=>TbButton::BUTTON_BUTTON,
		'htmlOptions' => array(
            'id'=>'resetBtn'
		),
    ));
?>


</div>

<?php $this->endWidget(); ?>

</div>

<?php $this->beginWidget('bootstrap.widgets.TbModal',
              array('id'=>'error',
                    'htmlOptions' => array('style' => 'width: 1000px; margin-left:-500px;'),
                    )
              ); ?>

<div class="modal-header">
    <h4>Transmittal XML Deficiences</h4>
</div>

<div class="modal-body">

</div>

<div class="modal-footer">
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'label'=>'Close',
        'type' => 'inverse',
        'url'=>'#',
        'htmlOptions'=>array('data-dismiss'=>'modal'),
    )); ?>
</div>

<?php $this->endWidget(); ?>
<?php $this->endWidget(); ?>