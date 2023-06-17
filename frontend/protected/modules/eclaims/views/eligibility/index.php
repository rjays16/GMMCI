<?php
/**
 * @var $this EligibilityController
 * @var $eligibility Eligibility
 * @var $member PhicMember
 * @var $encounter EclaimsEncounter
 * @var $documents EligibilityDocument
 * @var $person EclaimsPerson
 */

$this->setPageTitle('Verify Eligibility');
$this->breadcrumbs[] = 'Verify';

$errorMessages = array();
$nonPHIC = 0;

/*
    Cause we have to pass a unempty model for the detail view and
    we do not want to render error messages if there is no person selected.
    We have to check if the person model is a new record.
*/
if (!empty($person))
    $isPersonNewRecord = $person->getIsNewRecord();


// var_dump($encounter);die();
if (empty($isPersonNewRecord) && empty($encounter)) {
    $errorMessages[] = 'This patient currently does not have an active encounter';
}
// if(!empty($isPersonNewRecord)) {
if (empty($encounter)) {
    $encounter = new EclaimsEncounter;
}

$member = $encounter->phicMember;
if (empty($isPersonNewRecord) && empty($member)) {
    $errorMessages[] = 'This patient\'s does not have a PHIC member profile';
    $nonPHIC = 1;
}
if (empty($member)) {
    $member = new EclaimsPhicMember;
}
$member->member_mname= mb_convert_case($member->member_mname, MB_CASE_UPPER, "UTF-8");
$member->member_lname = mb_convert_case($member->member_lname, MB_CASE_UPPER, "UTF-8");
$member->member_fname = mb_convert_case($member->member_fname, MB_CASE_UPPER, "UTF-8");  

$eligibility = $encounter->eligibility;
if (empty($eligibility)) {
    $eligibility = new Eligibility;
}


if ($errorMessages) {
    $listMessages = '<ul><li>' . implode('</li><li>', $errorMessages) . '</li></ul>';
    Yii::app()->user->setFlash('warning', '<strong>Warning!</strong> ' . $listMessages);
}



$url = $this->createUrl('eligibility/index');
$encno = $encounter->encounter_nr;
$pid = $encounter->pid;
$baseUrl = Yii::app()->request->baseUrl;
Yii::app()->clientScript->registerScript('eligibility.form', <<<JAVASCRIPT
  /* Patient Select2 Search */
  $('#patient_search').on('change',function(e){
      $.ajax({
          url:$(this).data('url'),
          type: 'POST',
          data: {
            id : $(this).val()
          },
          dataType : 'json',
          beforeSend : function() {
              Alerts.loading({ content: 'Loading Patient Data. Please wait...' });
  
          },
          success: function(data) {
            window.location.href = '{$url}'+'&id='+data.encounter;
          }
      });
      
  });
  /* Print Button */
  $('.printBtn').click(function(e) {
    var btn = $(this);
  
    var _callPrintWindow = function(_btn) {
        var printUrl = _btn.attr('href');
        window.open(printUrl, '_blank');
    };
    /* Generic checker for all print buttons */
    if(btn.parent().hasClass('disabled') || btn.hasClass('disabled')) {
        /* PBEF: special scenario */
        if(btn.hasClass('pbefbtn')) {
            Alerts.confirm({
                title: "Are you sure?",
                content: btn.data('warning-msg'),
                callback: function(result) {
                    if(result) {
                        /* Call */
                        _callPrintWindow(btn);
                    }
                }
            });
        }
        return false;
    }
    _callPrintWindow(btn);
  
  
    return false;
  });
  /* SegBox Header Buttons */
  $('div.bootstrap-widget-header a.disabled').on('click', function(e) {
    e.preventDefault();
  });
    $('.verify-final').click(function() {
        var that = $(this)
            parent_li = that.parent();
  
        if(parent_li.hasClass('disabled')) {
            Alerts.error({
                title: 'Action Verify (final) Denied!',
                content: 'Final verification of eligibility is not allowed, for patient\'s who has not yet been discharged!'
            });
            return false;
        }
    });
  //added by Jasper Ian Q. Matunog 11/19/2014
  $('#printCsf1').click(function() {
    var that = $(this);
    if(that.hasClass('disabled')) {
        return false;
    }
  
        var enc_no = '{$encno}';
        var pid = '{$pid}';
        var rawUrlData = {reportid:'csfp1', 
                          repformat:'pdf',
                          param:{enc_no:enc_no,pid:pid}};
        var urlParams = $.param(rawUrlData);
    window.open('{$baseUrl}' + '/modules/reports/show_report.php?'+ urlParams, '_blank');
    // window.open('{$baseUrl}' + '/modules/reports/show_report.php?reportid='+reportid+'&repformat='+repformat+'&from_date=&to_date=&param='+data, '_blank');
  });

  // for csf Full Page added by Johnmel Sulla
  $('#printCsfFullPage').click(function() {
    var that = $(this);
    if(that.hasClass('disabled')) {
        return false;
    }
  
        var enc_no = '{$encno}';
        var pid = '{$pid}';
        var rawUrlData = {reportid:'csfFP', 
                          repformat:'pdf',
                          param:{enc_no:enc_no,pid:pid}};
        var urlParams = $.param(rawUrlData);
    window.open('{$baseUrl}' + '/modules/reports/show_report.php?'+ urlParams, '_blank');
    // window.open('{$baseUrl}' + '/modules/reports/show_report.php?reportid='+reportid+'&repformat='+repformat+'&from_date=&to_date=&param='+data, '_blank');
  });
  
  $('#reflect-insurance-billing').click(function(e) {
    var _button = $(this);
    if(_button.hasClass('disabled')) 
        return false;
    e.preventDefault();
  
    Alerts.confirm({
        title: "Are you sure?",
        content: _button.data('alert-message'),
        callback: function(result) {
            if(result) {
                window.location = _button.attr('href');
                Alerts.loading({ content: 'Adding insurance to the billing. Please wait...' });
            }
        }
    });
  });
//      /**
//      *  Added by JEFF 01-18-18 for loading screen upon verifying data of member.
//      */
//      $('#yw8').on('click',function(e){
//          var vlocker = '{$nonPHIC}';
//              if (vlocker == 1) {
//                  Alerts.alert({
//                      title: 'INVALID!',
//                      content: 'Patient has no PHIC member Profile.'
//                  });
//              }
//              else{
//                  Alerts.loading({
//                      content: 'Verifying membership data. Please wait...'
//                  });
//              }     
//          });
//  
    /**
      *  Added by JEFF 03-22-18 for loading screen upon verifying data of member.
    */
      $('#vfinal').on('click',function(e){
                Alerts.loading({
                    content: 'Verifying membership data. Please wait...'
              });   
                    });
      $('#vinitial').on('click',function(e){
            Alerts.loading({
                    content: 'Verifying membership data. Please wait...'
                    });   
        });
JAVASCRIPT
    , CClientScript::POS_READY);
?>

<div class="row-fluid">
    <div class="span12" style="margin-bottom: 20px">

        <?php

        $this->widget('eclaims.widgets.PatientSearch',
            array(
                'name'        => 'patient_search',
                'value'       => $person->pid,
                'htmlOptions' => array(
                    'class'    => 'input-xxlarge',
                    'data-url' => $this->createUrl('getEncounterData'),
                ),
            )
        );

        ?>
    </div>
</div>


<?php
Yii::import('eclaims.components.EclaimsFormatter');
$formatter = Yii::createComponent(array(
    'class'          => 'EclaimsFormatter',
    'dateFormat'     => 'F j, Y',
    'datetimeFormat' => 'F j, Y h:ia',
));


/* CF1 Print PDF Url */
if (!empty($encounter->encounter_nr)) {
    $urlManager = Yii::app()->getUrlManager();
    $phic = InsuranceProvider::getProviderByShortFirmId(InsuranceProvider::INSURANCE_PHIC);
    $cf1Url = array(
        'domain' => Yii::app()->getBaseUrl(),
        'route'  => $urlManager->createPathInfo(array(
            'modules'          => '',
            'repgen'           => '',
            'pdf_cf1_form.php' => '',
        ), '', '/'),
        'params' => $urlManager->createPathInfo(array(
            'ntid'         => 'false',
            'lang'         => 'en',
            'encounter_nr' => $encounter->encounter_nr,
            'id'           => $phic->hcare_id,
        ), '=', '&'),
    );
    $cf1Url = "{$cf1Url['domain']}/{$cf1Url['route']}?{$cf1Url['params']}";
}

Yii::import('bootstrap.widgets.TbButton');


$this->beginWidget('bootstrap.widgets.TbBox', array(
    'title'         => 'PhilHealth Benefits Eligibility Information',
    'headerIcon'    => 'fa fa-user',
    'headerButtons' => array(
        array(
            'class'   => 'bootstrap.widgets.TbButtonGroup',
            'buttons' => array(
                array(
                    'buttonType'  => TbButton::BUTTON_LINK,
                    'type'        => TbButton::TYPE_SUCCESS,
                    'label'       => 'List of Encounters',
                    'icon'        => 'fa fa-pencil',
                    'disabled'    => empty($person->pid),
                    'visible'     => !empty($person->encounter),
                    'htmlOptions' => array(
                        'class'        => 'pull-right',
                        'data-toggle'  => 'modal',
                        'data-target'  => '#caseno-modal',
                        'data-tooltip' => 'tooltip',
                        'title'        => 'Edit member info',
                    ),

                ),

            ),
        ),
        array(
            'class'   => 'bootstrap.widgets.TbButtonGroup',
            'buttons' => array(
                array(
                    'buttonType' => TbButton::BUTTON_LINK,
                    'type'       => TbButton::TYPE_PRIMARY,
                    'label'      => 'Go To Get PIN',
                    'icon'       => 'fa fa-link',
                    'disabled'   => empty($person->pid),
                    'url'        => $this->createUrl('member/getPIN', array(
                        'pid' => $person->pid,
                    )),
                ),


            ),
        ),
        array(
            'class'   => 'bootstrap.widgets.TbButtonGroup',
            'buttons' => array(
                array(
                    'label'       => 'CSF Full Page',
                    'icon'        => 'fa fa-print',
                    'disabled'    => empty($member->insurance_nr),
                    'url'         => '',
                    'htmlOptions' => array('id' => 'printCsfFullPage'),
                ),
                array(
                    'label'       => 'CSF p.1',
                    'icon'        => 'fa fa-print',
                    'disabled'    => empty($member->insurance_nr),
                    'url'         => '',
                    'htmlOptions' => array('id' => 'printCsf1'),
                ),
                array(
                    'buttonType' => TbButton::BUTTON_BUTTON,
                    'items'      => array(
                        array(
                            'label'       => 'PBEF',
                            'icon'        => 'fa fa-print',
                            'url'         => $this->createUrl('eligibility/print', array('id' => $encounter->encounter_nr)),
                            'linkOptions' => array(
                                'class'            => 'printBtn pbefbtn',
                                'data-warning-msg' => 'The member was not yet check for eligibility. Do you want to continue?',
                            ),
                            'itemOptions' => array(
                                'class' => empty($eligibility->id) ? 'disabled' : '',
                            ),
                        ),
                        array(
                            'label'       => 'CF1',
                            'icon'        => 'fa fa-print',
                            'url'         => isset($cf1Url) ? $cf1Url : '',
                            'linkOptions' => array('class' => 'printBtn'),
                            'itemOptions' => array(
                                'class' => empty($member->insurance_nr) ? 'disabled' : '',
                            ),
                        ),
                    ),
                ),
            ),
        ),
        array(
            'class'       => 'bootstrap.widgets.TbButtonGroup',
            'encodeLabel' => false,
            'buttons'     => array(
                array(
                    'buttonType'  => TbButton::BUTTON_LINK,
                    'label'       => 'Verify (Initial)',
                    'url'         => $this->createUrl('eligibility/verify', array('id' => $encounter->encounter_nr, 'is_final' => 0)),
                    'disabled'    => (empty($encounter->encounter_nr) || empty($member->insurance_nr)),
                    'id'          => 'vinitial',
                    'htmlOptions' => array('class' => 'verify-final', 'id' => 'vinitial'),

                ),
                array(
                    'buttonType' => TbButton::BUTTON_BUTTON,
                    'disabled'   => !$encounter->hasBilledDt(),
                    'items'      => array(
                        array(
                            'label'       => "Verify (final)",
                            'url'         => $this->createUrl('eligibility/verify', array('id' => $encounter->encounter_nr, 'is_final' => 1)),
                            'linkOptions' => array('class' => 'verify-final', 'id' => 'vfinal'),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'htmlOptions'   => array(),
));

echo CHtml::tag('h4', array('class' => 'text-right'),
    $formatter->formatEligibility(
        $encounter, 'eligibility.eligibility', 'eligibility.is_eligible'
    )
);
?>
<!-- Added textfield by jeff 02/13-18 for verify initial... -->
<input type="hidden" name="vlocker" value="<?php echo $nonPHIC; ?>">
<div class="row-fluid">
    <div class="span6">
        <legend>
            <h5>PHIC Member Informations</h5>
        </legend>
        <?php
        $memCategpryHelpText = CHtml::tag('i', array(
            'class'       => 'fa fa-question-circle',
            'data-toggle' => 'tooltip',
            'title'       => '"Non-PHIC" Member Category is auto set to "Indigent"',
        ), '');
        $this->widget('bootstrap.widgets.TbDetailView', array(
            'data'         => $member,
            'formatter'    => $formatter,
            'type'         => 'striped condensed bordered',
            'itemTemplate' => "<tr class=\"{class}\"><th style=\"width:30%\">{label}</th><td>{value}</td></tr>\n",
            'attributes'   => array(
                array(
                    'name'  => 'insurance_nr',
                    'label' => 'PIN',
                ),
                array(
                    'name'  => 'FullName',
                    'label' => 'Full Name',
                ),
                array(
                    'name'  => 'Sex',
                    'label' => 'Sex',
                ),
                array(
                    'name'  => 'birth_date',
                    'label' => 'Date of Birth',
                    'type'  => 'date',
                ),
                array(
                    'name'  => 'MemberRelation',
                    'label' => 'Relation to Patient',
                ),
                array(
                    'name'  => 'MemberTypeDesc',
                    'label' => 'Member Category ' . $memCategpryHelpText,
                ),
                array(
                    'name'    => 'employer_name',
                    'label'   => 'Employer Name',
                    'visible' => !empty($member->employer_name),
                ),
                array(
                    'name'    => 'employer_no',
                    'label'   => 'Employer No.',
                    'visible' => !empty($member->employer_no),

                ),

            ),
        ));
        ?>
    </div>

    <div class="span6">
        <legend>
            <h5>Patient Information</h5>
        </legend>
        <?php
        $dischargeHelpText = CHtml::tag('i', array(
            'class'       => 'fa fa-question-circle',
            'data-toggle' => 'tooltip',
            'title'       => 'Based on Bill Date',
        ), '');


        $this->widget('bootstrap.widgets.TbDetailView', array(
            'formatter'    => $formatter,
            'data'         => $encounter,
            'itemTemplate' => "<tr class=\"{class}\"><th style=\"width:30%\">{label}</th><td>{value}</td></tr>\n",
            'type'         => 'striped condensed bordered',
            'attributes'   => array(
                array(
                    'name'  => 'pid',
                    'label' => 'Patient ID',
                ),
                array(
                    'name'  => 'person.fullName',
                    'label' => 'Full name',
                ),
                array(
                    'label' => 'Admission Date',
                    'name'  => 'admissionDt',
                    'type'  => 'dateTime',

                ),
                array(
                    'encodeLabel' => false,
                    'name'        => 'dischargeDt',
                    'label'       => 'Discharge Date ' . $dischargeHelpText,
                    'type'        => 'dateTime',
                    // mod by JC 7-30-18
                    'value' => function ($data) {
                        
                        $discharge = $data->getDischargeDate();
    
                        if (empty($discharge)) {
                            $discharge = NULL;
                        } else {
                            $discharge = date("M d, Y h:i A", strtotime($data->getDischargeDate()));
                        }
    
                        return $discharge;
                    }
                    // end
                ),

                array('name' => 'person.Sex', 'label' => 'Sex'),
                array('name' => 'person.DateBirth', 'label' => 'Date of Birth', 'type' => 'date',),
            ),
        ));
        ?>
    </div>
</div>


<div class="row-fluid">
    <div class="span6">
        <legend>
            <h5>PHIC Eligibility Information</h5>
        </legend>
        <?php
        $this->widget('bootstrap.widgets.TbDetailView', array(
            'data'         => $eligibility,
            'type'         => 'striped condensed bordered',
            'itemTemplate' => "<tr class=\"{class}\"><th style=\"width:50%\">{label}</th><td>{value}</td></tr>\n",
            'attributes'   => array(
                array('name' => 'Eligibility', 'label' => 'Eligible to avail PhilHealth benefits?'),
                array('name' => '3over6', 'label' => 'With 3 monthly contributions within the past 6 months?'),
                array('name' => '9over12', 'label' => 'With 9 monthly contributions within the past 12 months?'),
                array('name' => 'remaining_days', 'label' => 'Number of days remaining from the 45 days benefit limit'),
            ),
        ));
        ?>
    </div>

    <div class="span6">
        <legend>
            <h5>Required Documents</h5>
        </legend>
        <?php
        $dataProvider = new CActiveDataProvider('EligibilityDocument', array(
            'criteria' => array(
                'condition' => 'eligibility_id=:elig_id',
                'params'    => array(':elig_id' => $eligibility->id),
            ),
        ));

        $this->widget('bootstrap.widgets.TbGridView', array(
            'type'         => 'striped bordered condensed hover',
            'dataProvider' => $dataProvider,
            'columns'      => array(
                array('name' => 'name', 'header' => 'Document Name'),
                array('name' => 'reason', 'header' => 'Reason'),
            ),
        ));
        ?>
    </div>
</div>

<div class="row-fluid">

    <?php
    $this->widget('eclaims.widgets.eclaims.EncounterList', array(
        'pid'      => $pid,
        'active'   => false,
        'template' => $template,
        'phic'     => true,
    ));
    ?>


</div>

<?php $this->endWidget(); ?>

