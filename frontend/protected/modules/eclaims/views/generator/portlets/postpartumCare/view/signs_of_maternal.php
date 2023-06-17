<div class="row-fluid">
    <div class="span6">
        <h5>Signs of Maternal Postpartum Complications</h5>
        <label for="is_done">Done?</label>
        <?php
        $list = CHtml::listData($ynlist, 'value', 'details');
        echo $form->dropDownList(
            $signsofmaternal,
            'is_done',
            $list,
            array(
                'class' => 'form-control col-md-12',
                'id' => 'is_done_signs_of_maternal',
                'required' => true,
                'options' => array(
                    $signsofmaternal->is_done != '' ? $signsofmaternal->is_done : ''  => array('selected' => true)
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
        echo $form->TextFieldRow($signsofmaternal, 'remarks', array(
            'class' => 'input-medium span7',
            'id' => 'remarks_signs_of_maternal',
            // 'placeholder' => 'Age of Menarche',
        ));
        ?>
    </div>
</div>