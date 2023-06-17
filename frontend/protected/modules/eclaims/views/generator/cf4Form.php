<?php
header('Content-Type: text/html; charset=utf-8');
$this->setPageTitle('CF4 DATA ENTRY');
$baseUrl = Yii::app()->baseUrl;
$cs = Yii::app()->getClientScript();
$cs->registerCssFile($baseUrl . '/css/spinner/sweetalert2.min.css');
$cs->registerScriptFile($baseUrl . '/css/spinner/sweetalert2.all.min.js');
?>

<div class="row-fluid">
    <div class="span12">
        <div class="row-fluid">
            <h3><?php echo $person['person']->name_last . ', ' . $person['person']->name_first . ' ' . $person['person']->name_middle ?></h3>
        </div>
        <?php
// \CVarDumper::dump($peChest, 10, true);die;
Yii::import('bootstrap.widgets.TbButton');
$box = $this->beginWidget(
    'application.widgets.SegBox',
    array(
        'title' => 'PATIENT CLINICAL RECORDS',
        'headerIcon' => 'icon-user',
        'htmlOptions' => array('class' => 'header-color'),
        'content' => $this->renderPartial(
            'portlets/personalInfo/index',
            array(
                'model' => $person,
                'encounter' => $encounter,
                'patient_info' => $patient_info,
                'getChiefComplaint' => $getChiefComplaint,
            ),
            true
        ),
        'headerButtons' => array(
            array(
                'class' => 'bootstrap.widgets.TbButton',
                'buttonType' => TbButton::BUTTON_BUTTON,
                'label' => 'Comprehensive Report',
                'items' => array(
                    array(
                        'icon' => 'fa fa-print',
                        'label' => 'Pediatrics',
                        'url' => '#',
                        'linkOptions' => array(
                            'id' => 'comprehensive_report_pedia',
                        ),
                    ),
                    array(
                        'icon' => 'fa fa-print',
                        'label' => 'Internal Medicine',
                        'url' => '#',
                        'linkOptions' => array(
                            'id' => 'comprehensive_report_im',
                        ),
                    ),
                    array(
                        'icon' => 'fa fa-print',
                        'label' => 'Gynecology',
                        'url' => '#',
                        'linkOptions' => array(
                            'id' => 'comprehensive_report_gyne',
                        ),
                    ),
                    array(
                        'icon' => 'fa fa-print',
                        'label' => 'Obstetrics',
                        'url' => '#',
                        'linkOptions' => array(
                            'id' => 'comprehensive_report_obs',
                        ),
                    ),
                ),
            ),
            array(
                'class' => 'bootstrap.widgets.TbButton',
                'buttonType' => TbButton::BUTTON_BUTTON,
                'icon' => 'fa fa-print',
                'label' => 'Print CF4',
                'htmlOptions' => array(
                    'id' => 'print_cf4',
                ),
            ),
            array(
                'class' => 'bootstrap.widgets.TbButton',
                'buttonType' => TbButton::BUTTON_BUTTON,
                'icon' => 'fa fa-print',
                'label' => 'Print CF3',
                'htmlOptions' => array(
                    'id' => 'cf3',
                ),
            ),
            array(
                'class' => 'bootstrap.widgets.TbButton',
                'buttonType' => TbButton::BUTTON_BUTTON,
                'icon' => 'fa fa-print',
                'label' => 'Medical Abstract',
                'htmlOptions' => array(
                    'id' => 'med_abstract',
                ),
            ),
        ),
    )
);
?>
        <?php $this->endWidget(); /* box */?>

        <?php
Yii::import('bootstrap.widgets.TbButton');
$box = $this->beginWidget(
    'application.widgets.SegBox',
    array(
        'title' => 'Vital Signs',
        'headerIcon' => 'icon-heart',
        'htmlOptions' => array('class' => ''),
        'content' => $this->renderPartial(
            'portlets/vitalSigns/index',
            array(
                'model' => $person,
                'encounter' => $encounter,
                'patient_info' => $patient_info,
            ),
            true
        ),
    )
);
?>

        <?php $this->endWidget(); /* box */?>

        <?php
Yii::import('bootstrap.widgets.TbButton');
$box = $this->beginWidget(
    'application.widgets.SegBox',
    array(
        'title' => 'Pertinent Signs & Symptoms',
        'headerIcon' => 'icon-heart',
        'htmlOptions' => array('class' => ''),
        'content' => $this->renderPartial(
            'portlets/signsAndSymptoms/index',
            array(
                'model' => $person,
                'encounter' => $encounter,
                'getSelectedSignsAndSymptoms' => $getSelectedSignsAndSymptoms,
                'getSignsAndSymptomsOne' => $getSignsAndSymptomsOne,
                'getSignsAndSymptomsTwo' => $getSignsAndSymptomsTwo,
                'getSignsAndSymptomsThree' => $getSignsAndSymptomsThree,
                'getSignsAndSymptomsFour' => $getSignsAndSymptomsFour,
            ),
            true
        ),
    )
);
?>

        <?php $this->endWidget(); /* box */?>

        <?php
Yii::import('bootstrap.widgets.TbButton');
$box = $this->beginWidget(
    'application.widgets.SegBox',
    array(
        'title' => 'Physical Examinations',
        'headerIcon' => 'icon-file',
        'htmlOptions' => array('class' => ''),
        'content' => $this->renderPartial(
            'portlets/physicalExam/index',
            array(
                'model' => $person,
                'encounter' => $encounter,
                'peHeent' => $peHeent,
                'selected_heent' => $selected_heent,
                'peSkin' => $peSkin,
                'selected_skin' => $selected_skin,
                'peChest' => $peChest,
                'selected_chest' => $selected_chest,
                'peGen_survey' => $peGen_survey,
                'selected_gensurvey' => $selected_gensurvey,
                'peCvs' => $peCvs,
                'selected_cvs' => $selected_cvs,
                'peAbdomen' => $peAbdomen,
                'selected_abdomen' => $selected_abdomen,
                'peNeuro' => $peNeuro,
                'selected_neuro' => $selected_neuro,
                'peRectal' => $peRectal,
                'selected_rectal' => $selected_rectal,
                'peGuie' => $peGuie,
                'selected_guie' => $selected_guie,
            ),
            true
        ),
    )
);
?>

        <?php $this->endWidget(); /* box */?>


        <?php
Yii::import('bootstrap.widgets.TbButton');
$box = $this->beginWidget(
    'application.widgets.SegBox',
    array(
        'title' => 'Course in the Ward',
        'headerIcon' => 'icon-list',
        'htmlOptions' => array('class' => ''),
        'content' => $this->renderPartial(
            'portlets/courseWard/index',
            array(
                'model' => $courseWard,
                'encounter' => $encounter,
            ),
            true
        ),
    )
);
?>

        <?php $this->endWidget(); /* box */?>


        <?php
Yii::import('bootstrap.widgets.TbButton');
$box = $this->beginWidget(
    'application.widgets.SegBox',
    array(
        'title' => 'Drug/Medicine',
        'headerIcon' => 'fa fa-user-md',
        'htmlOptions' => array('class' => ''),
        'content' => $this->renderPartial(
            'portlets/medicine/index',
            array(
                'model' => $medicine,
                'encounter' => $encounter,
                'medicine_library' => $medicine_library,
            ),
            true
        ),
    )
);
?>

        <?php $this->endWidget(); /* box */?>
        <?php
Yii::import('bootstrap.widgets.TbButton');
$box = $this->beginWidget(
    'application.widgets.SegBox',
    array(
        'title' => 'Prenatal Consultation',
        'headerIcon' => 'fa fa-user-md',
        'htmlOptions' => array('class' => ''),
        'content' => $this->renderPartial(
            'portlets/prenatalConsultation/index',
            array(
                'model' => $person,
                'encounter' => $encounter,
                'menstrualHistory' => $menstrualHistory,
                'obstetricHistory' => $obstetricHistory,
                'physicalExamination' => $physicalExamination,
                'ynlist' => $ynlist,
                'obstetricRiskFactor' => $obstetricRiskFactor,
                'medicalRiskFactor' => $medicalRiskFactor,
                'clinicalLibObstetric' => $clinicalLibObstetric,
                'clinicalLibMedical' => $clinicalLibMedical,
                'deliveryPlan' => $deliveryPlan,
                'prenatalVisit' => $prenatalVisit,
                'prenatalVisits' => $prenatalVisits,
                'prenatalConsultaionNoList' => $prenatalConsultaionNoList,
            ),
            true
        ),
    )
);
?>

        <?php $this->endWidget(); /* box */?>

        <?php
Yii::import('bootstrap.widgets.TbButton');
$box = $this->beginWidget(
    'application.widgets.SegBox',
    array(
        'title' => 'Delivery Outcome',
        'headerIcon' => 'fa fa-user-md',
        'htmlOptions' => array('class' => ''),
        'content' => $this->renderPartial(
            'portlets/deliveryOutcome/index',
            array(
                'model' => $person,
                'encounter' => $encounter,
                'dtDeliveryOutcome' => $dtDeliveryOutcome,
                'dtDischargeOutcome' => $dtDischargeOutcome,
                'maternalOutcome' => $maternalOutcome,
                'pregnancyUterine' => $pregnancyUterine,
                'birthoutcome' => $birthoutcome,
                'birthoutcomes' => $birthoutcomes,
                'genderList' => $genderList,
                'spfdeliveryoutcome' => $spfdeliveryoutcome,
            ),
            true
        ),
    )
);
?>

        <?php
$this->endWidget();
/* box */?>

        <?php
Yii::import('bootstrap.widgets.TbButton');
$box = $this->beginWidget(
    'application.widgets.SegBox',
    array(
        'title' => 'Postpartum Care',
        'headerIcon' => 'fa fa-user-md',
        'htmlOptions' => array('class' => ''),
        'content' => $this->renderPartial(
            'portlets/postpartumCare/index',
            array(
                'model' => $person,
                'encounter' => $encounter,
                'perinealwoundcare' => $perinealwoundcare,
                'signsofmaternal' => $signsofmaternal,
                'breastfeedingnutrition' => $breastfeedingnutrition,
                'familyplanning' => $familyplanning,
                'providedfamilyplanning' => $providedfamilyplanning,
                'referredpartnerphysician' => $referredpartnerphysician,
                'schedulenextpostpartum' => $schedulenextpostpartum,
                'ynlist' => $ynlist,
            ),
            true
        ),
    )
);
?>

        <?php
$this->endWidget(); /* box */
?>
    </div>
</div>
<input type="hidden" name="encounter_nr" id="encounter_nr" value="<?php echo $encounter->encounter_nr ?>">
<input type="hidden" name="pid" id="pid" value="<?php echo $encounter->pid ?>">
<input type="hidden" name="baseUrl" id="baseUrl" value="<?php echo $baseUrl ?>">

<script>
    $('#print_cf4').click(function() {
        var that = $(this);
        var enc_no = $('#encounter_nr').val();
        var pid = $('#pid').val();
        var baseUrl = $('#baseUrl').val();
        var rawUrlData = {
            reportid: 'cf4',
            repformat: 'pdf',
            param: {
                enc_no: enc_no,
                pid: pid
            }
        };
        var urlParams = $.param(rawUrlData);
        window.open(baseUrl + '/modules/reports/show_report.php?' + urlParams, '_blank');
        // window.open(that.data('id'), '_blank');
    });
    $('#med_abstract').click(function() {
        var that = $(this);
        var enc_no = $('#encounter_nr').val();
        var pid = $('#pid').val();
        var baseUrl = $('#baseUrl').val();
        var rawUrlData = {
            reportid: 'med_abstract',
            repformat: 'pdf',
            param: {
                enc_no: enc_no,
                pid: pid
            }
        };
        var urlParams = $.param(rawUrlData);
        window.open(baseUrl + '/modules/reports/show_report.php?' + urlParams, '_blank');
    });
    $('#cf3').click(function() {
        var that = $(this);
        var enc_no = $('#encounter_nr').val();
        var pid = $('#pid').val();
        var baseUrl = $('#baseUrl').val();
        var rawUrlData = {
            reportid: 'cf3',
            repformat: 'pdf',
            param: {
                enc_no: enc_no,
                pid: pid
            }
        };
        var urlParams = $.param(rawUrlData);
        window.open(baseUrl + '/modules/reports/show_report.php?' + urlParams, '_blank');
    });

    $('#comprehensive_report_pedia').click(function() {
        var that = $(this);
        var enc_no = $('#encounter_nr').val();
        var pid = $('#pid').val();
        var baseUrl = $('#baseUrl').val();
        var rawUrlData = {
            reportid: 'comprehensive_report_pedia',
            repformat: 'pdf',
            param: {
                enc_no: enc_no,
                pid: pid
            }
        };
        var urlParams = $.param(rawUrlData);
        window.open(baseUrl + '/modules/reports/show_report.php?' + urlParams, '_blank');
    });

    $('#comprehensive_report_im').click(function() {
        var that = $(this);
        var enc_no = $('#encounter_nr').val();
        var pid = $('#pid').val();
        var baseUrl = $('#baseUrl').val();
        var rawUrlData = {
            reportid: 'comprehensive_report_im',
            repformat: 'pdf',
            param: {
                enc_no: enc_no,
                pid: pid
            }
        };
        var urlParams = $.param(rawUrlData);
        window.open(baseUrl + '/modules/reports/show_report.php?' + urlParams, '_blank');
    });

    $('#comprehensive_report_gyne').click(function() {
        var that = $(this);
        var enc_no = $('#encounter_nr').val();
        var pid = $('#pid').val();
        var baseUrl = $('#baseUrl').val();
        var rawUrlData = {
            reportid: 'comprehensive_report_gyne',
            repformat: 'pdf',
            param: {
                enc_no: enc_no,
                pid: pid
            }
        };
        var urlParams = $.param(rawUrlData);
        window.open(baseUrl + '/modules/reports/show_report.php?' + urlParams, '_blank');
    });

    $('#comprehensive_report_obs').click(function() {
        var that = $(this);
        var enc_no = $('#encounter_nr').val();
        var pid = $('#pid').val();
        var baseUrl = $('#baseUrl').val();
        var rawUrlData = {
            reportid: 'comprehensive_report_obs',
            repformat: 'pdf',
            param: {
                enc_no: enc_no,
                pid: pid
            }
        };
        var urlParams = $.param(rawUrlData);
        window.open(baseUrl + '/modules/reports/show_report.php?' + urlParams, '_blank');
    });

</script>
<style type="text/css">
    .header-color {
        background: green;
    }
</style>
