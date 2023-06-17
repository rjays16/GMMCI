<?php
/* @var $this PackagesController */
/* @var $model Packages */
/* @var $form CActiveForm */

Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/frontend/scripts/packages/view.js');
?>

<div class="form">

<?php
//$form=$this->beginWidget('CActiveForm', array(
//	'id'=>'packages-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
//	'enableAjaxValidation'=>false,
//));
$form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
    'id'=>'packages-form',
    'enableAjaxValidation'=>false,
    'type'=>'horizontal',
    'htmlOptions'=>array('class'=>'well')
));
?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

    <?php
        $box = $this->beginWidget(
            'bootstrap.widgets.TbBox',
            array(
                'title' => 'Package Details'
            )
        )
    ?>

	<?php echo $form->textFieldRow($model,'package_name',array('class'=>'span5','maxlength'=>50)); ?>

    <?php echo $form->textFieldRow($model,'pkg_phiccode',array('class'=>'span5','maxlength'=>12)); ?>

    <?php echo $form->toggleButtonRow($model,'is_surgical',array('enabledLabel'=>'Yes','disabledLabel'=>'No')); ?>

    <?php echo $form->toggleButtonRow($model,'is_zpackage',array('enabledLabel'=>'Yes','disabledLabel'=>'No')); ?>

    <?php echo $form->dropDownListRow($model,'clinic_id', 
    CHtml::listData(Department::model()->allOPDMedical()->findAll(), 'nr', 'name_formal'), 
    array('class' => 'span5','maxlength'=>258)); ?>

    <?php 
        $this->endWidget(); 

        $box = $this->beginWidget(
            'bootstrap.widgets.TbBox',
            array(
                'title' => 'Package Items',
                'headerButtons'=>array(
                    array(
                        'class'=>'bootstrap.widgets.TbButton',
                        'label' => 'Add Item',
                        'type' => 'primary',
                        'htmlOptions'=>array(
                            'data-toggle'=>'modal',
                            'data-target' => '#populate-items',
                        )
                    )
                )
            )
        );
    ?>

    <table class="items table table-striped" id="package-items" style="width: 100%;">
        <thead>
        <tr>
            <th style="width: 100px;">Item Code</th>
            <th>Name</th>
            <th>Purpose</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Total</th>
            <th style="width: 1px;">&nbsp;</th>
        </tr>
        </thead>
        <tbody id="package-items-block">
            <?php if(empty($model->packageDetails)): ?>
                <tr id="no-item"><td colspan="100"><i>No item currently added</i></td></tr>
            <?php else: ?>
                <?php foreach($model->packageDetails as $pd): ?>
                    <tr>
                        <td width="10%">
                            <input type="hidden" name="Packages[items][item_code][]" value="<?= $pd->item_code; ?>" />
                            <input type="hidden" name="Packages[items][item_name][]" value="<?= $pd->item_name; ?>" />
                            <input type="hidden" name="Packages[items][item_purpose][]" value="<?= $pd->item_purpose; ?>" />
                            <input type="hidden" name="Packages[items][quantity][]" value="<?= $pd->quantity; ?>" />
                            <?= $pd->item_code; ?>
                        </td>
                        <td width="40%"><?= $pd->item_name; ?></td>
                        <td width="15%"><?= $pd->itemPurposeText; ?></td>
                        <td width="15%">
                            <input class="span12" onchange="javascript:calculateTotal(this)" type="text" name="Packages[items][price][]" value="<?= number_format($pd->price, 2); ?>" />
                        </td>
                        <td width="10%"><?= $pd->quantity; ?></td>
                        <td width="10%" class = 'item_total'><?= $pd->quantity * $pd->price; ?></td>
                        <td><a rel="tooltip" data-toggle="tooltip" title="" href="javascript:;" onclick="javascript:removeItem(this);" data-original-title="Remove"><i class="icon-trash"></i></a></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <?php 
        $this->endWidget();

        $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType'=>'submit',
            'type'=>'primary',
            'label'=>$model->isNewRecord ? 'Create' : 'Save',
        )); 
    ?>

<?php $this->endWidget(); ?>
</div><!-- form -->


<!-- Modals -->
<?php $this->beginWidget(
    'bootstrap.widgets.TbModal',
    array('id' => 'populate-items', 'htmlOptions'=>array('style' => 'width: 800px; margin-left: -400px'))
); ?>

    <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h4>Select Item</h4>
    </div>

    <div class="modal-body" style="height: 300px; overflow-y: none;">
        <?php 
        $form = $this->beginWidget(
            'bootstrap.widgets.TbActiveForm',
            array(
                'id' => 'search-items-form',
                'type' => 'inline',
                'htmlOptions' => array('class' => 'well'),
            )
        );
        echo $form->textFieldRow(
            $packageDetail,
            'item_name',
            array(
                'class' => 'input-medium',
            ),
            array(
                'prepend' => '<i class="icon-search loader"></i>'
            )
        );
?>
&nbsp;
<?php 
        echo $form->dropDownListRow(
            $packageDetail,
            'item_purpose',
            $packageDetail->itemPurposeOptions,
            array('placeholder'=>false)
        );
        $this->endWidget();
        unset($form);
        ?>
        <div id="table-scroll" style="overflow-y: auto;position: relative; max-height: 210px;">
            <table class="items table table-striped" id="items-search-result" style="width: 100%;">
                <thead>
                <tr>
                    <th style="width: 100px;">Item Code</th>
                    <th>Name</th>
                    <th style="width: 60px;">Quantity</th>
                    <th style="width: 1px;">&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                    <tr><td colspan="100"><i>Please enter item name on search box</i></td></tr>
                </tbody>
            </table>
        </div>
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