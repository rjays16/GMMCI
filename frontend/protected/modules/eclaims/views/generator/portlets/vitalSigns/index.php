<?php 

\Yii::import('bootstrap.widgets.TbButton');
\Yii::import('bootstrap.widgets.TbActiveForm');
$baseUrl = Yii::app()->baseUrl;
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile($baseUrl . '/js/jquery/ui/jquery-ui.js');
// $cs->registerCssFile($baseUrl . '/css/generator_css/bootstrap.css');

Yii::app()->clientScript->registerScriptFile('js/jquery/ui/jquery.livequery.min.js');

$form = $this->beginWidget(
    'bootstrap.widgets.TbActiveForm',
    array(
        'id'     => 'vitalSigns-record',
        'type'   => 'horizontal',
        'htmlOptions' => array(
            'data-url-save-vital-signs' => $this->createUrl('vitalSigns/SaveVitalSigns'),
            'data-url-delete-vital-signs' => $this->createUrl('vitalSigns/DeleteVitalSigns'),
            'data-encounter_nr' =>  $encounter->encounter_nr,
            'data-pid' =>  $model['person']->pid,

        ),
    )
);

?>

<div class="row-fluid">
    <div class="span12">
	    <div class="row-fluid">
	        <div class="span12">
	            <?php
	            $this->renderPartial('portlets/vitalSigns/view/form', array(
                        'model' => $model,
                        'encounter' => $encounter,
                        'patient_info' => $patient_info,
                        'form' => $form
	                )
	            );
	            ?>
	        </div>
	    </div>

	    <div class="row-fluid">
	        <div class="span12">
	            <?php
	            $this->renderPartial('portlets/vitalSigns/view/list', array(
                        'model' => $model,
                        'encounter' => $encounter,
                        'patient_info' => $patient_info
	                )
	            );
	            ?>
	        </div>
	    </div>

    </div>
</div>

<?php $this->endWidget(); ?>