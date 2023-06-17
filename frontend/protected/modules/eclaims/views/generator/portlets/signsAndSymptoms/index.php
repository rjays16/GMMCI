<?php
/**
 * @var $model -- courseWard
 */

\Yii::import('bootstrap.widgets.TbButton');
\Yii::import('bootstrap.widgets.TbActiveForm');

$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id' => 'signs-symptoms-information',
    'type' => \TbActiveForm::TYPE_VERTICAL,
    'htmlOptions' => array(
        'data-url-save-signs' => $this->createUrl('signsAndSymptoms/saveSigns'),
        'data-encounter_nr' => $encounter->encounter_nr,
        'data-pid' =>  $model['person']->pid,
    )
));


?>
    <div class="row-fluid">
        <div class="span12">
            <?php
            $this->renderPartial('portlets/signsAndSymptoms/view/list_form', array(
                    'encounter' => $encounter,
                    'form' => $form,
                    'model' => $model,
                    'getSelectedSignsAndSymptoms' => $getSelectedSignsAndSymptoms,
	                'getSignsAndSymptomsOne' => $getSignsAndSymptomsOne,
	                'getSignsAndSymptomsTwo' => $getSignsAndSymptomsTwo,
	                'getSignsAndSymptomsThree' => $getSignsAndSymptomsThree,
	                'getSignsAndSymptomsFour' => $getSignsAndSymptomsFour,
                )
            );
            ?>

        </div>
    </div>

<?php $this->endWidget(); /** Form **/ ?>