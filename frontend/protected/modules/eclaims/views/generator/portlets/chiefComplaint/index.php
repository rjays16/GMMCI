<?php 

\Yii::import('bootstrap.widgets.TbButton');
\Yii::import('bootstrap.widgets.TbActiveForm');
$baseUrl = Yii::app()->baseUrl;
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile($baseUrl . '/js/jquery/ui/jquery-ui.js');

Yii::app()->clientScript->registerScriptFile('js/jquery/ui/jquery.livequery.min.js');

$form = $this->beginWidget(
    'bootstrap.widgets.TbActiveForm',
    array(
        'id'     => 'chief-complaint-record',
        'type'   => 'horizontal',
        'htmlOptions' => array(
            'data-url-save-chief-complaint' => $this->createUrl('chiefComplaint/saveChiefComplaint'),
            'data-encounter_nr' =>  $encounter->encounter_nr,
            'data-pid' =>  $model['person']->pid,

        ),
    )
);

?>

<div class="row-fluid">
    <div class="span12">
        <?php
        $this->renderPartial('portlets/chiefComplaint/view/form', array(
                'model' => $model,
                'encounter' => $encounter,
                'getChiefComplaint' => $getChiefComplaint,
                'form' => $form
            )
        );
        ?>
    </div>
</div>

<?php $this->endWidget(); ?>