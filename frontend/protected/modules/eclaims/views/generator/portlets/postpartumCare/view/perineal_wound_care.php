<div class="row-fluid">
    <div class="span12">
        <h5>Perineal Wound Care</h5>
        <label for="is_done">Done?</label>
        <?php
        $list = CHtml::listData($ynlist, 'value', 'details');
        echo $form->dropDownList(
            $perinealwoundcare,
            'is_done',
            $list,
            array(
                'class' => 'form-control col-md-12',
                'id' => 'is_done_perineal_wound_care',
                'required' => true,
                'options' => array(
                    $perinealwoundcare->is_done != '' ? $perinealwoundcare->is_done : ''  => array('selected' => true)
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
        echo $form->TextFieldRow($perinealwoundcare, 'remarks', array(
            'class' => 'input-medium span7',
            'id' => 'remarks_perineal_wound_care',
            // 'placeholder' => 'Age of Menarche',
        ));
        ?>
    </div>
</div>