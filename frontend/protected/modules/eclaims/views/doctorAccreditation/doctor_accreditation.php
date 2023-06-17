<?php
    /*
     * Created by: Abuyo, Mary Joy
     * Created on: May 21,2014
     * Function: View function that holds the Select 2 widget for selecting specific doctor.
     *           And renders the doctor_form view that holds the informations of the doctor selected in th TBbox widget
     *           This function also holds the check accreditation button that is disabled if 
     *           the doctor doesn't have an accreditation number.
     * Parameters: Personnel Model
     *
     *
    */
     

$url = $this->createUrl('doctoraccreditation/dr_accreditation');

    $checkUrl = CJSON::encode(Yii::app()->createUrl('eclaims/doctoraccreditation/CheckAccreditation', 
                                 array('accreditation_nr'=>$personnel->doctoraccre->accreditation_nr, 
                                        'personnel_nr' => $personnel->nr)));
        
    Yii::app()->clientScript->registerScript('doctor-accreditation-check',"
    $('#doctor_search').on('change',function(e){
        window.location.href = '{$url}'+'&nr='+$(this).val();
    });
    $('#checkAccreditation').click(function() {
        window.location = {$checkUrl};
    });
"
,CClientScript::POS_READY);
        
$this->setpageTitle('Doctor\'s Accreditation Verification Utility');
 
    /*
        Created by: Lagmay, Gabriel
    */
    $this->widget('bootstrap.widgets.TbSelect2',
    array(
        'name' => 'doctor_search',
        'asDropDownList' => false,
        'options' => array(
            'placeholder' => 'Search Doctor',
            'minimumInputLength' => '3',
            'dataType' => 'jsonp',
            'allowClear' => true,
            'width' => '40%',
            'ajax' => array(
                    'quietMillis' => 1500,
                    'url' => Yii::app()->createUrl('eclaims/doctoraccreditation/getdoctors'),
                    'data' => 'js:function(term, page) { return {q: term}; }',
                    'results' => 'js:function(data,page) { return {results: data}; }',
                ),
            'formatResult' => "js:function(data) { 
                        var gender = 'male';
                        var genderColor='#00c';
                        if ('string' === typeof data.sex && data.sex.toUpperCase()=='F') {
                            gender = 'female';
                            genderColor='#E200AC';
                        }
                        return data.text+' <i class=\"fa fa-' + gender + '\" style=\"color:' + genderColor + '\"></i>'+'<div class=\"row\">'+'<div class=\"span3\">'+data.department+''+'</div>'+'</div>';
                    }",
            'escapeMarkup' => 'js:function(m) { return m; }',
        ),
        'htmlOptions' => array(
                'id' => 'doctor_search'),
    )
);


    echo "<br><br>";


    $this->widget('bootstrap.widgets.TbBox',
    array(
        'title' => 'Doctor\'s Information',
        'headerIcon' => 'icon-user',
                'content' =>  $this->renderPartial('/doctoraccreditation/doctor_form', array('personnel'=>$personnel), true),
       )
);


?>

<div class= "row-fluid">
    <div class = "span2 offset9" >

<?php
               $this->widget(
                    'bootstrap.widgets.TbButton',
                    array(
                        'label' => 'Check Accreditation',
                        'buttonType' => TbButton::BUTTON_BUTTON,
                        //'buttonType' => 'button',
                        'disabled' => (empty($personnel->doctoraccre->accreditation_nr)),
                        //'url' => Yii::app()->createUrl('eclaims/doctoraccreditation/CheckAccreditation', array('accreditation_nr'=>$personnel->doctoraccre->accreditation_nr)),
                        'htmlOptions' => array(
                            'id' => 'checkAccreditation',
                        )
                    )
                );
?>

    </div>
</div>




