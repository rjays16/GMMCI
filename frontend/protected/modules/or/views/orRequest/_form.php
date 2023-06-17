<?php
/* @var $this OrRequestController */
/* @var $model OrRequest */
/* @var $form CActiveForm */

    Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/frontend/scripts/orRequest/create.js');
?>

<div class="form">

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'or-request-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>true,
    'htmlOptions'=>array('class'=>'well'),
    'type'=>'horizontal',
)); ?>

<?= $form->hiddenField($model, 'request_flag', array('value'=>'pending')); ?>
<?= CHtml::hiddenField('or_refno', $model->or_refno); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>
    <?php $box = $this->beginWidget(
        'bootstrap.widgets.TbBox',
        array(
            'title' => 'Patient Details',
            'headerButtons'=>array(
                array(
                    'class'=>'bootstrap.widgets.TbButton',
                    'label' => 'Change Patient',
                    'type' => 'primary',
                    'visible' => empty($model->patient_name)?true:false,
                    'htmlOptions'=>array(
                        'data-toggle'=>'modal',
                        'data-target' => '#populate-patients',
                    )
                )
            )
        )
    );?>
    <?= $form->hiddenField($model, 'encounter_nr'); ?>
    <?= $form->textFieldRow($model, 'patient_name', array('class' => 'span5', 'readonly'=>true, 'placeholder'=>false)); ?>
    <?= $form->textFieldRow($model, 'patient_gender', array('class'=>'span5', 'readonly'=>true, 'placeholder'=>false)); ?>
    <?= $form->textFieldRow($model, 'patient_age', array('class'=>'span5', 'readonly'=>true, 'placeholder'=>false)); ?>
    <?= $form->textAreaRow($model, 'patient_address', array('class' => 'span5', 'readonly'=>true, 'placeholder'=>false)); ?>
    <?php $this->endWidget(); ?>
    <?php $box = $this->beginWidget(
        'bootstrap.widgets.TbBox',
        array(
            'title' => 'Request Details',
        )
    );?>
    <?php echo $form->dropDownListRow(
        $model,
        'dept_nr',
        $departmentsArray,
        array('place_holder'=>false)
    ); ?>
    <?php echo $form->radioButtonListRow(
        $model,
        'trans_type',
        $model->getTypeOptions()
    ); ?>
    <?php echo $form->checkBoxRow(
        $model,
        'is_urgent'
    ); ?>
    <?php echo $form->dropDownListRow(
        $model,
        'or_type',
        $orTypesArray,
        array('place_holder'=>false)
    ); ?>
    <?php echo $form->textFieldRow($model, 'date_requested',
        array('class' => 'datetime_field','placeholder'=>false)); 

        echo $form->textFieldRow($model, 'personnel_name', array('class' => 'span5', 'readonly'=>true, 'placeholder'=>false));
    ?>
    <?php $this->endWidget(); ?>
    <?php $box = $this->beginWidget(
        'bootstrap.widgets.TbBox',
        array(
            'title' => 'Requirements',
        )
    );?>
    <?= $form->checkBoxListRow(
        $model,
        'orChecklists',
        $checkListArray
    ) ?>
    <?php $this->endWidget(); ?>
    
    <?php $box = $this->beginWidget(
        'bootstrap.widgets.TbBox',
        array(
            'title' => 'Packages',
            'headerButtons'=>array(
                array(
                    'class'=>'bootstrap.widgets.TbButton',
                    'label' => 'Add Package',
                    'type' => 'primary',
                    'htmlOptions'=>array(
                        'data-toggle'=>'modal',
                        'data-target' => '#populate-packages',
                    )
                )
            )
        )
    );?>
        <?php $collapse = $this->beginWidget(
            'bootstrap.widgets.TbCollapse',
            array(
                'id'=>'request-package-list',
                'htmlOptions'=>array(
                    'style'=>'height:auto;'
                )
            )
        ); ?>
        <input type="hidden" id="package-counter" value="0" />
        <?php $this->endWidget(); ?>
    <?php $this->endWidget(); ?>


    <!-- Surgery Type -->
    <?php 
        $box = $this->beginWidget(
            'bootstrap.widgets.TbBox',
            array(
                'title' => 'Other Details',
                // 'headerIcon' => 'icon-th-list',
                // 'htmlOptions'=>array('style'=>'width: 49%;display: inline-block;'),
            )
        );

        echo $form->radioButtonListRow(
            $model,
            'surgery_type',
            $model->surgeryTypeOptions
        );


        echo $form->textAreaRow($model, 'remarks');
        echo $form->numberFieldRow($model, 'amount', array('step' => 'any'));

        $this->endWidget(); 
    ?>

        <?php
            $buttonText = ($model->isNewRecord) ? 'Create' : 'Save';
            $this->widget(
            'bootstrap.widgets.TbButton',
                array(
                    'buttonType' => 'submit',
                    'label' => $buttonText
                )
            );
        ?>

    <?php $this->endWidget(); ?>

<!-- Modals -->
<?php $this->beginWidget(
        'bootstrap.widgets.TbModal',
        array('id' => 'populate-patients', 'htmlOptions'=>array('style' => 'width: 800px; margin-left: -400px'))
    ); ?>

    <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h4>Select Patient</h4>
    </div>

    <div class="modal-body" style="height: 300px;">
        <?php
            $form = $this->beginWidget(
                'bootstrap.widgets.TbActiveForm',
                array(
                    'id' => 'search-patients-form',
                    'type' => 'search',
                    'htmlOptions' => array('class' => 'well'),
                )
            );
            echo $form->textFieldRow(
                $model,
                'patient_search_text',
                array(
                    'class' => 'input-medium',
                ),
                array(
                    'prepend' => '<i class="icon-search loader"></i>'
                )
            );
            $this->endWidget();
            unset($form);
        ?>
        <table id="patient-search-result" style="display: none; width: 100%;">
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Sex</th>
                    <th>Age</th>
                    <th>Birth Date</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>

    <div class="modal-footer">
        <?php $this->widget(
            'bootstrap.widgets.TbButton',
            array(
                'label' => 'Close',
                'url' => '#',
                'htmlOptions' => array('data-dismiss' => 'modal'),
            )
        ); ?>
    </div>
<?php $this->endWidget(); ?>

<?php $this->beginWidget(
    'bootstrap.widgets.TbModal',
    array('id' => 'populate-packages', 'htmlOptions'=>array('style' => 'width: 800px; margin-left: -400px'))
); ?>

    <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h4>Select Package</h4>
    </div>

    <div class="modal-body" style="height: 300px;">
        <?php
        $form = $this->beginWidget(
            'bootstrap.widgets.TbActiveForm',
            array(
                'id' => 'search-packages-form',
                'type' => 'search',
                'htmlOptions' => array('class' => 'well'),
            )
        );
        echo $form->textFieldRow(
            $model,
            'package_search_text',
            array(
                'class' => 'input-medium',
            ),
            array(
                'prepend' => '<i class="icon-search loader"></i>'
            )
        );
        $this->endWidget();
        unset($form);
        ?>
        <table id="package-search-result" style="display: none; width: 100%;">
            <thead>
            <tr>
                <th>Package No</th>
                <th>Name</th>
                <th>Price</th>
                <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>

    <div class="modal-footer">
        <?php $this->widget(
            'bootstrap.widgets.TbButton',
            array(
                'label' => 'Close',
                'url' => '#',
                'htmlOptions' => array('data-dismiss' => 'modal'),
            )
        ); ?>
    </div>
<?php $this->endWidget(); ?>
</div><!-- form -->