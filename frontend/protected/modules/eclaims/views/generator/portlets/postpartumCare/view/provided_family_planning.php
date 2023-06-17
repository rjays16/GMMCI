<div class="row-fluid">
    <div class="span6">
        <h5>Provided family planning service to patient</h5>
        <label for="is_done">Done?</label>
        <?php
        $list = CHtml::listData($ynlist, 'value', 'details');
        echo $form->dropDownList(
            $providedfamilyplanning,
            'is_done',
            $list,
            array(
                'class' => 'form-control col-md-12',
                'id' => 'is_done_provided_family_planning',
                'required' => true,
                'options' => array(
                    $providedfamilyplanning->is_done != '' ? $providedfamilyplanning->is_done : ''  => array('selected' => true)
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
        echo $form->TextFieldRow($providedfamilyplanning, 'remarks', array(
            'class' => 'input-medium span7',
            'id' => 'remarks_provided_family_planning',
            // 'placeholder' => 'Age of Menarche',
        ));
        ?>
    </div>
</div>