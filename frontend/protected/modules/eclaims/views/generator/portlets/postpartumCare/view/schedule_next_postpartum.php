<div class="row-fluid">
    <div class="span6">
        <h5>Schedule the next postpartum follow-up</h5>
        <label for="is_done">Done?</label>
        <?php
        $list = CHtml::listData($ynlist, 'value', 'details');
        echo $form->dropDownList(
            $schedulenextpostpartum,
            'is_done',
            $list,
            array(
                'class' => 'form-control col-md-12',
                'id' => 'is_done_schedule_next_postpartum',
                'required' => true,
                'options' => array(
                    $schedulenextpostpartum->is_done != '' ? $schedulenextpostpartum->is_done : ''  => array('selected' => true)
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
        echo $form->TextFieldRow($schedulenextpostpartum, 'remarks', array(
            'class' => 'input-medium span7',
            'id' => 'remarks_schedule_next_postpartum',
            // 'placeholder' => 'Age of Menarche',
        ));
        ?>
    </div>
</div>