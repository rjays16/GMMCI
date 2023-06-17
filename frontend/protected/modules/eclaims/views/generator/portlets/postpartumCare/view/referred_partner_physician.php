<div class="row-fluid">
    <div class="span6">
        <h5>Referred to partner physician for Voluntary Surgical Sterilization</h5>
        <label for="is_done">Done?</label>
        <?php
        $list = CHtml::listData($ynlist, 'value', 'details');
        echo $form->dropDownList(
            $referredpartnerphysician,
            'is_done',
            $list,
            array(
                'class' => 'form-control col-md-12',
                'id' => 'is_done_referred_partner_physician',
                'required' => true,
                'options' => array(
                    $referredpartnerphysician->is_done != '' ? $referredpartnerphysician->is_done : ''  => array('selected' => true)
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
        echo $form->TextFieldRow($referredpartnerphysician, 'remarks', array(
            'class' => 'input-medium span7',
            'id' => 'remarks_referred_partner_physician',
            // 'placeholder' => 'Age of Menarche',
        ));
        ?>
    </div>
</div>