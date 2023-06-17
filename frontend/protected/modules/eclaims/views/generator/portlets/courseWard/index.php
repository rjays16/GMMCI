<?php
/**
 * @var $model -- courseWard
 */

\Yii::import('bootstrap.widgets.TbButton');
\Yii::import('bootstrap.widgets.TbActiveForm');
$baseUrl = Yii::app()->baseUrl;
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile($baseUrl . '/js/jquery/ui/jquery-ui.js');

Yii::app()->clientScript->registerScriptFile('js/jquery/ui/jquery.livequery.min.js');

$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id' => 'course-ward-form',
    'type' => \TbActiveForm::TYPE_VERTICAL,
    'htmlOptions' => array(
        'data-url' => $this->createUrl('courseWard/saveCourseWard'),
        'data-url-delete' => $this->createUrl('courseWard/destroyCourseWard'),
        'data-encounter_nr' => $encounter->encounter_nr,
        'data-pid' => $encounter->pid,
    )
));

?>
    <div class="row-fluid">
        <div class="span12">
            <?php
            $this->renderPartial('portlets/courseWard/view/_form', array(
                    'encounter' => $encounter,
                    'form' => $form,
                    'model' => $model,
                )
            );
            ?>

        </div>
    </div>

    <div class="row-fluid">
        <div class="span12">
            <?php
            $this->renderPartial('portlets/courseWard/view/_list', array(
                    'encounter' => $encounter,
                    'form' => $form,
                    'model' => $model,
                )
            );
            ?>
        </div>
    </div>

<?php $this->endWidget(); /** Form **/ ?>