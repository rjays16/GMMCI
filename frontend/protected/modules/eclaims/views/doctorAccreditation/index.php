<?php

$this->setpageTitle('Doctor\'s Accreditation Verification Utility');

$url = $this->createUrl('index');
$checkUrl = $this->createUrl('check',
    array(
        'accreditation_nr' => $accreditation->accreditation_nr,
        'personnel_nr' => $personnel->nr
    )
);

$checkUrlRaw = $this->createUrl('check');

$panUrl = $this->createUrl('getPan', array(
    'personnel_nr'=>$personnel->nr
));

Yii::app()->clientScript->registerScript('doctor-accreditation-check',"
    $('#doctor_search').on('change',function(e){
        window.location = '{$url}'+'&nr='+$(this).val();
    });
    $('#checkAccreditation').click(function() {
        Alerts.loading({
            content: 'Please wait. We are currently retrieving accreditation information from the PHIC web service!'
        });
        window.location = '{$checkUrl}';
    });

    $('#getPAN').click(function() {
        Alerts.loading({
            // icon: 'fa-wrench',
            // iconColor: '#888',
            content: 'Please wait. We are currently retrieving accreditation information from the PHIC web service!'
        });
        $.ajax({
            url : '{$panUrl}',
            type : 'GET',
            dataType : 'json',
            success : function(data){
                console.log(data);
                var accr = data['data'];
                window.location = '{$checkUrlRaw}'+'&personnel_nr='+'{$personnel->nr}'+'&accreditation_nr='+accr;
            },
            error : function(data){
                console.log(data);
            },

        });
    });

    ", CClientScript::POS_READY
);


Yii::import('bootstrap.widgets.TbButton');

$this->beginWidget('application.widgets.SegBox', array(
    'title' => 'Doctor\'s Information',
    'headerIcon' => 'icon-user',
    'footer' =>  '<div class="form-actions">' . $this->widget('bootstrap.widgets.TbButton', array(
            'label' => 'Check Accreditation',
            'buttonType' => TbButton::BUTTON_BUTTON,
            'icon' => 'fa fa-check-square',
            'disabled' => (empty($accreditation->accreditation_nr)),
            'htmlOptions' => array(
                'id' => 'checkAccreditation',
                'class' => 'btn-success',
            )
        ), true) . '</div>'
    )
);

?>



<div class="control-group">
<?php

echo CHtml::label('Search for doctor', 'doctor_search');
$this->widget('eclaims.widgets.DoctorSearch', array(
    'name' => 'doctor_search',
    'htmlOptions' => array(
        'class' => 'input-xxlarge'
    ),
));
?>
</div>


<?php

//condition that would alert the user that the get PAN function is not applicable because the Doctor's TIN number is missing
if ($personnel->nr && (!(trim($personnel->tin))) && (empty($accreditation->dr_nr)) ){
    Yii::app()->user->setFlash('warning',"<strong>Warning!</strong><br> Doctor's TIN number is required <br> Accreditation number is NOT Applicable");
}

?>

<div class="row-fluid">
    <div class="span6">
        <legend><h5>Personal information</h5></legend>
        <?php
            $this->widget('bootstrap.widgets.TbDetailView',array(
                'data' => $personnel,
                'type'=>'striped condensed bordered',
                'attributes'=>array(
                    'person.name_last',
                    'person.name_first',
                    'person.name_middle',
                    'person.date_birth',
                    'person.Sex',
                    'DepartmentName',
                    'job_function_title',
                ),
            ));
        ?>
    </div>


    <div class="span6">
        <legend><h5>Accreditation information</h5></legend>
        <?php
        // var_dump($personnel);die();
            $this->widget('bootstrap.widgets.TbDetailView',array(
                'data' =>$accreditation,
                'type'=>'striped condensed bordered',
                'attributes'=>array(
                    array(
                        'name' => 'personnel.tin', 'label' => 'TIN', 'value' => $personnel->tin,
                        'htmlOptions' =>array(
                            'empty-cells' => 'show'
                        ),
                    ),
                    array('name' => 'personnel.license_nr', 'label' => 'License No.', 'value' => $personnel->license_nr),
                    array(
                        'name' => 'accreditation_nr',
                        'label' => 'Accreditation No.' .
                            $this->widget('bootstrap.widgets.TbButton', array(
                                'label' => 'Get PAN',
                                'icon' => 'fa fa-refresh',
                                'size' => TbButton::SIZE_MINI,
                                'buttonType' => TbButton::BUTTON_BUTTON,
                                'disabled'=> (empty($personnel->tin)) || (!empty($accreditation->dr_nr)) ,
                                'htmlOptions' => array(
                                    'id' => 'getPAN',
                                )
                            ), true)
                    ),
                    'accreditation_start',
                    'accreditation_end',
                ),
            ));


        ?>
    </div>
</div>

<?php $this->endWidget() ?>
