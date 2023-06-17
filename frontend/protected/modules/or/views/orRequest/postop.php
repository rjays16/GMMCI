<?php
/* @var $this OrRequestController */
/* @var $model OrRequest */

Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/frontend/scripts/orRequest/schedule.js');
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/frontend/scripts/orRequest/postop.js');
Yii::app()->bootstrap->registerAssetCss('bootstrap-timepicker/bootstrap-timepicker.css');
Yii::app()->bootstrap->registerAssetJs('bootstrap-timepicker/bootstrap-timepicker.js');

$this->breadcrumbs=array(
    'Or Requests'=>array('index'),
    $orRequest->or_refno,
);

$this->menu=array(
    array('label'=>'View All Requests', 'url'=>array('index')),
);
?>

<h1>Request Post Operative</h1>

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
    'id'=>'or-request-form',
    'enableAjaxValidation'=>false,
    'htmlOptions'=>array('class'=>'well'),
    'type'=>'horizontal',
)); ?>
<?php echo $form->errorSummary($model); ?>
<?= $form->hiddenField($model, 'or_refno', array('value'=>$orRequest->or_refno)); ?>
<?= CHtml::hiddenField('isFinalBill', $isFinalBill?1:0); ?>
<?= CHtml::hiddenField('request_flag', $orRequest->request_flag); ?>

<?php echo $this->renderPartial('_detail', array('model'=>$orRequest)); ?>

<!-- Post Op Details -->
<?php $box = $this->beginWidget(
    'bootstrap.widgets.TbBox',
    array(
        'title' => 'Post Operative Details',
    )
);?>
<?= $form->textFieldRow($model, 'operation_start',array('class'=>'datetime_field','placeholder'=>false)); ?>
<?= $form->textFieldRow($model, 'operation_end',array('class'=>'datetime_field','placeholder'=>false)); ?>
<?= $form->textAreaRow($preOp, 'pre_op_diagnosis', array('class' => 'span5')); ?>
<?= $form->textAreaRow($model, 'operation_diagnosis',array('class'=>'span5')); ?>
<?= $form->textAreaRow($model, 'operation_perform',array('class'=>'span5')); ?>
<?= $form->dropDownListRow($model,'technique_id',$techniquesArray,array('place_holder'=>false, 'prompt'=>'-SELECT-')); ?>
Technique Description
<br/><br/>
<?php
    $this->widget(
        'bootstrap.widgets.TbCKEditor',
        array(
            'model' => $model,
            'attribute' => 'technique_desc',
            'editorOptions' => array(
                'plugins' => 'basicstyles,toolbar,enterkey,entities,floatingspace,wysiwygarea,'
                                .'indentlist,link,list,dialog,dialogui,button,indent,fakeobjects'
            )
        )
    );
?>
<?php //$this->widget('bootstrap.widgets.TbTimePicker', array('name'=>'lala')); ?>
<?php $this->endWidget(); ?>

<!-- Personnel Involved -->
<?php echo $this->renderPartial('_personnelInvolved', array('model'=>$model, 'form'=>$form, 'orRequest'=>$orRequest)); ?>

<!-- Post Op Details -->
<?php $box = $this->beginWidget(
    'bootstrap.widgets.TbBox',
    array(
        'title' => 'Anesthesia Procedures',
        'headerButtons'=>array(
            array(
                'class'=>'bootstrap.widgets.TbButton',
                'label' => 'Add Anesthesia',
                'size'=>'small',
                'type' => 'primary',
                'htmlOptions'=>array(
                    'data-toggle'=>'modal',
                    'data-target' => '#populate-anesthesia',
                    'data-purpose'=>'preop-circulating-nurses',
                    'data-personnel-type'=>'CN',
                    'style'=>'margin-right:5px'
                )
            )
        )
    )
);?>
    <table id="anesthesia-procedures" class="items table table-striped">
        <thead>
        <tr>
            <td>
                Anesthesia
            </td>
            <td>
                Category
            </td>
            <td width="20%">
                Time Begin
            </td>
            <td width="20%">
                Time Ended
            </td>
            <td width="1%">
            </td>
        </tr>
        </thead>
        <tbody>
            <tr id='default-entry'><td colspan='5'><i>No Anesthesia</i></td></tr>
        </tbody>
    </table>
<?php $this->endWidget(); ?>

<!-- ICPM -->
<?php 
    $this->beginWidget(
        'bootstrap.widgets.TbBox',
        array(
            'title' => 'ICPM',
            'headerButtons'=>array(
                array(
                    'class'=>'bootstrap.widgets.TbButton',
                    'label' => 'Add ICPM',
                    'size'=>'small',
                    'type' => 'primary',
                    'visible' => !$isFinalBill,
                    'htmlOptions'=>array(
                        'data-toggle'=>'modal',
                        'data-target' => '#icpm-modal',
                        'style'=>'margin-right:5px'
                    )
                )
            )
        )
    );
?>
<table id="icpm-table" class="items table table-striped">
    <thead>
        <tr>
            <td width="15%">
                Code
            </td>
            <td width="54%">
                Description
            </td>
           <!--  <td width="10%">
                RVU
            </td> -->
            <td width="10%">
                Laterality
            </td> 
            <td width="1%">
            </td>
        </tr>
    </thead>
    <tbody>
        <tr id='default-entry'><td colspan='5'><i>No ICPM</i></td></tr>
    </tbody>
</table>

<?php
    $this->endWidget();
?>

<!-- Packages -->
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

<!-- Medicine and Supplies -->
<?php 
    $this->beginWidget(
        'bootstrap.widgets.TbBox',
        array(
            'title' => 'Medicine and Supplies',
            'headerButtons'=>array(
                array(
                    'class'=>'bootstrap.widgets.TbButton',
                    'label' => 'Add Item',
                    'size'=>'small',
                    'type' => 'primary',
                    'visible' => !$isFinalBill,
                    'htmlOptions'=>array(
                        'data-toggle'=>'modal',
                        'data-target' => '#med-and-sup-modal',
                        'style'=>'margin-right:5px'
                    )
                )
            )
        )
    );
?>
<table id="medsup-table" class="items table table-striped">
    <thead>
        <tr>
            <td width="9%">
                Item No.
            </td>
            <td width="60%">
                Item Description
            </td>
            <td width="10%">
                Quantity
            </td>
            <td width="10%">
                Price
            </td>
            <td width="10%">
                Total
            </td>
            <td width="1%">
            </td>
        </tr>
    </thead>
    <tbody>
        <tr id='default-entry'><td colspan='6'><i>No Medicine and Supplies</i></td></tr>
    </tbody>
</table>

<?php
    $this->endWidget();
?>

<!-- Miscellaneous -->
<?php 
    $this->beginWidget(
        'bootstrap.widgets.TbBox',
        array(
            'title' => 'Miscellaneous',
            'headerButtons'=>array(
                array(
                    'class'=>'bootstrap.widgets.TbButton',
                    'label' => 'Add Item',
                    'size'=>'small',
                    'type' => 'primary',
                    'visible' => !$isFinalBill,
                    'htmlOptions'=>array(
                        'data-toggle'=>'modal',
                        'data-target' => '#populate-misc',
                        'style'=>'margin-right:5px'
                    )
                )
            )
        )
    );
?>
<table id="misc-table" class="items table table-striped">
    <thead>
        <tr>
            <td width="9%">
                Item No.
            </td>
            <td width="60%">
                Item Description
            </td>
            <td width="10%">
                Quantity
            </td>
            <td width="10%">
                Price
            </td>
            <td width="10%">
                Total
            </td>
            <td width="1%">
            </td>
        </tr>
    </thead>
    <tbody>
        <tr id='default-entry'><td colspan='6'><i>No Miscellaneous</i></td></tr>
    </tbody>
</table>

<?php
    $this->endWidget();
?>

<!-- Others -->
<?php $box = $this->beginWidget(
    'bootstrap.widgets.TbBox',
    array(
        'title' => 'Others',
    )
);?>
<?= $form->textFieldRow($model, 'medium_sponge',array('class'=>'span5')); ?>
<?= $form->textFieldRow($model, 'abdominal_pack',array('class'=>'span5')); ?>
<?= $form->textFieldRow($model, 'operating_sponge',array('class'=>'span5')); ?>
<?= $form->textFieldRow($model, 'cherry_balls',array('class'=>'span5')); ?>
<?= $form->textFieldRow($model, 'cottonoids',array('class'=>'span5')); ?>
<?= $form->textFieldRow($model, 'needles_nonatraumatic',array('class'=>'span5')); ?>
<?= $form->textFieldRow($model, 'needles_atraumatic',array('class'=>'span5')); ?>
<?= $form->textFieldRow($model, 'peanut_balls',array('class'=>'span5')); ?>
<?= $form->textAreaRow($model, 'others',array('class'=>'span5', 'style'=>'min-height:5em;')); ?>
<?php $this->endWidget(); ?>

<?php
$this->widget(
    'bootstrap.widgets.TbButton',
    array(
        'label' => $orRequest->request_flag=='done'?'Update':'Approve',
        'type' => 'success',
        'buttonType'=>'submit',
    )
);
?>
&nbsp;
<?php
$this->widget(
    'bootstrap.widgets.TbButton',
    array(
        'label' => 'Cancel',
        'type' => 'warning',
        'url'=>$this->createUrl('orRequest/index',array('flag'=>$orRequest->request_flag))
    )
);
?>
<?php $this->endWidget(); ?>

<!-- Personnel Modal -->
<?php echo $this->renderPartial('_personnelModal', array('person'=>$person)); ?>

<!-- Modals -->
<?php $this->beginWidget(
    'bootstrap.widgets.TbModal',
    array('id' => 'populate-anesthesia', 'htmlOptions'=>array('style' => 'width: 800px; margin-left: -400px'))
); ?>
    <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h4>Select Anesthesia</h4>
    </div>
    <div class="modal-body" style="height: 300px;">
        <?php
        $form = $this->beginWidget(
            'bootstrap.widgets.TbActiveForm',
            array(
                'id' => 'search-anesthesia-form',
                'type' => 'search',
                'htmlOptions' => array('class' => 'well'),
            )
        );
        echo $form->textFieldRow(
            $anesthesia,
            'anest_name',
            array(
                'class' => 'input-medium',
            ),
            array(
                'prepend' => '<i class="icon-search loader"></i>'
            )
        );
        $this->endWidget();
        //unset($form);
        ?>
        <table id="anesthesia-search-result" style="display: none; width: 100%;" class="items table table-striped table-bordered">
            <thead>
            <tr>
                <th width="15%">Name</th>
                <th width="*">Category</th>
                <th width="1%">&nbsp;</th>
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

<!-- ICPM Modal -->
<?php 
    $this->beginWidget(
        'bootstrap.widgets.TbModal',
        array(
            'id' => 'icpm-modal', 
            'htmlOptions'=>array(
                'style' => 'width: 800px; 
                margin-left: -400px'
            )
        )
    ); 
?>
    <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h4>Select ICPM</h4>
    </div>
    <div class="modal-body" style="height: 300px;">
        <?php
            $form = $this->beginWidget(
                'bootstrap.widgets.TbActiveForm',
                array(
                    'id' => 'search-icpm-form',
                    'type' => 'inline',
                    'htmlOptions' => array('class' => 'well'),
                )
            );
            echo $form->textFieldRow(
                $caseRate,
                'code',
                array(
                    'class' => 'input-medium'
                )
            );
            echo $form->textFieldRow(
                $caseRate,
                'description',
                array(
                    'class' => 'input-medium',
                    'style' => 'margin-left: 1em;margin-right: 1em;'
                )
            );
            $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label'=>'Go', 'icon'=>'search', 'id'=>'icpm-search-btn'));
            $this->endWidget();
            //unset($form);
        ?>
        <table id="icpm-search-result" style="display: none; width: 100%;" class="items table table-striped table-bordered">
            <thead>
            <tr>
                <th>Code</th>
                <th>Description</th>
                <!-- <th>Date</th>
                <th>RVU</th>
                <th>Charge</th> -->
                <th width="1%">&nbsp;</th>
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

<!-- Medicine and Supplies Modal -->
<?php 
    $this->beginWidget(
        'bootstrap.widgets.TbModal',
        array(
            'id' => 'med-and-sup-modal', 
            'htmlOptions'=>array(
                'style' => 'width: 800px; 
                margin-left: -400px'
            )
        )
    ); 
?>
    <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h4>Select Medicine and Supplies</h4>
    </div>
    <div class="modal-body" style="height: 300px;">
        <?php
            $form = $this->beginWidget(
                'bootstrap.widgets.TbActiveForm',
                array(
                    'id' => 'search-med-form',
                    'type' => 'search',
                    'htmlOptions' => array('class' => 'well'),
                )
            );
            echo $form->textFieldRow(
                $medsAndSupplies,
                'artikelname',
                array(
                    'class' => 'input-medium'
                ),
                array(
                    'prepend' => '<i class="icon-search loader"></i>'
                )
            );
            $this->endWidget();
            //unset($form);
        ?>
        <table id="med-search-result" style="display: none; width: 100%;" class="items table table-striped table-bordered">
            <thead>
            <tr>
                <th>Item No.</th>
                <th>Generic Name</th>
                <th width="1%">&nbsp;</th>
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

<!-- Package -->
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
            $orRequest,
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

<!-- Miscellaneous -->
<?php $this->beginWidget(
    'bootstrap.widgets.TbModal',
    array('id' => 'populate-misc', 'htmlOptions'=>array('style' => 'width: 800px; margin-left: -400px'))
); ?>

    <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h4>Miscellaneous</h4>
    </div>

    <div class="modal-body" style="height: 300px;">
        <?php
            $form = $this->beginWidget(
                'bootstrap.widgets.TbActiveForm',
                array(
                    'id' => 'search-misc-form',
                    'type' => 'search',
                    'htmlOptions' => array('class' => 'well'),
                )
            );
            echo $form->textFieldRow(
                $miscellaneous,
                'name',
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
        <table id="misc-search-result" style="display: none; width: 100%;" class="items table table-striped table-bordered">
            <thead>
            <tr>
                <th>Item No.</th>
                <th>Name</th>
                <th width="1%">&nbsp;</th>
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