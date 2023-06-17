<div class="row-fluid">
    <div class="span6">
        <h5>Breastfeeding and Nutrition</h5>
        <label for="is_done">Done?</label>
        <?php
        $list = CHtml::listData($ynlist, 'value', 'details');
        echo $form->dropDownList(
            $breastfeedingnutrition,
            'is_done',
            $list,
            array(
                'class' => 'form-control col-md-12',
                'id' => 'is_done_breastfeeding_nutrition',
                'required' => true,
                'options' => array(
                    $breastfeedingnutrition->is_done != '' ? $breastfeedingnutrition->is_done : ''  => array('selected' => true)
                ),

            ),
            array(
                'htmlOptions' => array(
                    'required' => true,
                    // 'name' => 'PatientPreassessment[patient_type]'
                ),

            )
        );


        ?>
    </div>


</div>
<div class="row-fluid">
    <div class="span12">
        <?php
        echo $form->TextFieldRow($breastfeedingnutrition, 'remarks', array(
            'class' => 'input-medium span7',
            'id' => 'remarks_breastfeeding_nutrition',
            // 'placeholder' => 'Age of Menarche',
        ));
        ?>
    </div>
</div>