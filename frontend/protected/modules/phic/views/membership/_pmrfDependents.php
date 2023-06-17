<?php
/* @var $this MembershipController */
/* @var $model MembershipForm */
/* @var $form TbActiveForm */

$this->widget('bootstrap.widgets.TbButton',array(
    'buttonType' => 'button',
    'type' => 'primary',
    'id' => 'btn-add-dependent',
    'icon' => 'fa fa-plus',
    'label' => 'Add Dependent',
    'htmlOptions' => array(
        'class' => 'pull-right',
        'style' => 'margin-bottom:10px;'
    )
));

$this->widget('bootstrap.widgets.TbButton',array(
    'buttonType' => 'button',
    'type' => 'warning',
    'id' => 'btn-add-patient-dependent',
    'icon' => 'fa fa-plus',
    'label' => 'Add Patient Dependent',
    'htmlOptions' => array(
        'class' => 'pull-right',
        'style' => 'margin-bottom:10px;margin-right:10px;'
    )
));
?>
<table class="table table-bordered">
    <thead>
    <tr>
        <th width="10%">Relation</th>
        <th width="15%">PIN</th>
        <th width="10%">First Name</th>
        <th width="10%">Middle Name</th>
        <th width="10%">Last Name</th>
        <th width="10%">Name Suffix</th>
        <th width="10%">Date of Birth</th>
        <th width="10%">Sex</th>
        <th width="10%">Disabled</th>
        <th width="5%" class="cell-button"><i class="fa fa-gear"></i></th>
    </tr>
    </thead>
    <tbody id="dependents">
    <?php if (!empty($model->pmrf->dependents)) : ?>
        <?php foreach ($model->pmrf->dependents as $dependent) :/* @var $dependent PmrfDependent */ ?>
            <tr>
                <td>
                    <?= $form->dropDownList($dependent, 'relation', array(
                        'c' => 'Child',
                        's' => 'Spouse',
                        'f' => 'Father',
                        'm' => 'Mother',
                    ), array(
                        'placeholder' => false,
                        'class' => 'relation',
                        'name' => "Dependents_o[{$dependent->id}][relation]"
                    )) ?>
                </td>
                <td>
                    <input class="pin" type="text" name="Dependents_o[<?= $dependent->id ?>][pin]" title="PIN"
                           value="<?= $dependent->pin ?>"/>
                </td>
                <td>
                    <input class="first_name" type="text" name="Dependents_o[<?= $dependent->id ?>][first_name]"
                           title="First Name" value="<?= $dependent->first_name ?>"/>
                </td>
                <td>
                    <input class="middle_name" type="text" name="Dependents_o[<?= $dependent->id ?>][middle_name]"
                           title="Middle Name" value="<?= $dependent->middle_name ?>"/>
                </td>
                <td>
                    <input class="last_name" type="text" name="Dependents_o[<?= $dependent->id ?>][last_name]"
                           title="Last Name" value="<?= $dependent->last_name ?>"/>
                </td>
                <td>
                    <input class="name_extension" type="text" name="Dependents_o[<?= $dependent->id ?>][name_extension]"
                           title="Name Suffix" value="<?= $dependent->name_extension ?>"/>
                </td>
                <td>
                    <input class="birth_date calendar" type="text" name="Dependents_o[<?= $dependent->id ?>][birth_date]"
                           title="Birth Date (mm/dd/yyyy)"
                           value="<?= strtotime($dependent->birth_date) ? date('m/d/Y', strtotime($dependent->birth_date)) : '' ?>"/>
                </td>
                <td>
                    <?= $form->dropDownList($dependent, 'sex', array(
                        'm' => 'Male',
                        'f' => 'Female'
                    ), array(
                        'placeholder' => false,
                        'class' => 'sex',
                        'name' => "Dependents_o[{$dependent->id}][sex]"
                    )) ?>
                </td>
                <td>
                    <?= $form->dropDownList($dependent, 'is_disabled', array(
                        '1' => 'Yes',
                        '0' => 'No'
                    ), array(
                        'placeholder' => false,
                        'class' => 'is_disabled',
                        'name' => "Dependents_o[{$dependent->id}][is_disabled]"
                    )) ?>
                </td>
                <td class="cell-button">
                    <input class="dependent-id" type="hidden" value="<?= $dependent->id ?>"/>
                    <a class="delete-button-o" style="font-size: x-large;color: #da4f49;">
                        <i class="fa fa-times-circle"></i>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>