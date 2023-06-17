<?php
/**
 * @var $model -- courseWard
 */

\Yii::import('bootstrap.widgets.TbButton');
\Yii::import('bootstrap.widgets.TbActiveForm');

$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id' => 'medicine-form',
    'type' => \TbActiveForm::TYPE_VERTICAL,
    'htmlOptions' => array(
        'data-url' => $this->createUrl('medicine/saveMedicine'),
        'data-url-delete' => $this->createUrl('medicine/destroyMedicine'),
        'data-url-get-data' => $this->createUrl('medicine/getData'),
        'data-url-update' => $this->createUrl('medicine/updateMedicine'),
        'data-encounter_nr' => $encounter->encounter_nr,
        'data-pid' => $encounter->pid,
    )
));

?>
    <div class="row-fluid">
        <div class="span12">
            <?php
            $this->renderPartial('portlets/medicine/view/_form', array(
                    'encounter' => $encounter,
                    'form' => $form,
                    'model' => $model,
                    'medicine_library' => $medicine_library
                )
            );
            ?>

        </div>
    </div>

    <div class="row-fluid">
        <div class="span12">
            <?php
            $this->renderPartial('portlets/medicine/view/_list', array(
                    'encounter' => $encounter,
                    'form' => $form,
                    'model' => $model,
                )
            );
            ?>
        </div>
    </div>

<?php $this->endWidget(); /** Form **/ ?>
