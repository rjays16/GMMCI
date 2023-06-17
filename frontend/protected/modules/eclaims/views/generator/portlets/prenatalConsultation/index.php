<?php

use SegHEIRS\components\web\ClientScript;
use SegHEIRS\components\web\Controller;


\Yii::import('bootstrap.widgets.TbActiveForm');
$baseUrl = Yii::app()->baseUrl;
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile($baseUrl . '/js/jquery/ui/jquery-ui.js');

Yii::app()->clientScript->registerScriptFile('js/jquery/ui/jquery.livequery.min.js');

$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'id' => 'prenatal-consultation-information',
  'type' => \TbActiveForm::TYPE_VERTICAL,
  'htmlOptions' => array(
    'data-url-save-menstrual-history' => $this->createUrl('menstrualHistory/saveMenstrualHistory'),
    'data-url-save-obstetric-history' => $this->createUrl('obstetricHistory/saveObstetricHistory'),
    'data-url-save-physical-examination' => $this->createUrl('physical/savePhysicalExamination'),
    'data-url-save-obstetric-risk-factor' => $this->createUrl('obstetricRiskFactor/saveObstetricRiskFactor'),
    'data-url-save-medical-risk-factor' => $this->createUrl('medicalRiskFactor/saveMedicalRiskFactor'),
    'data-url-save-delivery-plan' => $this->createUrl('deliveryPlan/saveDeliveryPlan'),
    'data-url-save-prenatal-visit' => $this->createUrl('prenatalVisit/savePrenatalVisit'),
    'data-url-delete-prenatal-visit' => $this->createUrl('prenatalVisit/deletePrenatalVisit'),
    'data-url-not-applicable' => $this->createUrl('notApplicable/saveNotApplicable'),
    'data-url-get-data-prenatal-visit' => $this->createUrl('prenatalVisit/getDataPrenatalVisit'),
    'data-url-update-data-prenatal-visit' => $this->createUrl('prenatalVisit/updatePrenatalVisit'),
    'data-encounter_nr' => $encounter->encounter_nr,
    'data-pid' => $model['person']->pid,

  ),
    // 'action' => $this->createUrl('medicalHistory.newSaveMedHistory', array(
    //     // 'encounter_no' => $model_latestEncounter['encounter_no'],
    //     // 'spin' => $model_latestEncounter['spin'],
    // ))
));

?>

<div class="row-fluid">
    <div class="span6">
        <label for="is_applicable">Applicable?</label>
        <?php
        $list = CHtml::listData($ynlist, 'value', 'details');
        echo $form->dropDownList(
          $menstrualHistory,
          'is_applicable',
          $list,
          array(
            'class' => 'form-control col-md-12',
            'id' => 'is_applicable',
            'required' => true,
              // 'options' => array(
              //     $menstrualHistory->is_applicable != '' ? $menstrualHistory->is_applicable : ''  => array('selected' => true)
              // ),
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
<div id="yes_applicable">
    <div class="row-fluid">

        <div class="span12">

            <?php
            $tabs = array(
              array(
                'label' => 'Clinical History',
                'content' => $this->renderPartial(
                  'portlets/prenatalConsultation/view/menstrual_history',
                  array(
                    'encounter' => $encounter,
                    'form' => $form,
                    'menstrualHistory' => $menstrualHistory,
                    'obstetricHistory' => $obstetricHistory,
                  ),
                  true
                ),
                'active' => true,
              ),
              
                array(
                    'label' => 'Physical Examination',
                    'content'     => $this->renderPartial(
                        'portlets/prenatalConsultation/view/physical_examination',
                        array(
                            'encounter' => $encounter,
                            'form' => $form,
                            'physicalExamination' => $physicalExamination,
                            'ynlist' => $ynlist,
                        ),
                        true
                    ),
                    // 'active' => true,
                ),
                array(
                    'label' => 'Obstetric Risk Factors',
                    'content'=> $this->renderPartial(
                        'portlets/prenatalConsultation/view/obstetric_risk_factor',
                        array(
                            'encounter' => $encounter,
                            'obstetricRiskFactor' => $obstetricRiskFactor,
                            'clinicalLibObstetric' => $clinicalLibObstetric,
                        ),
                        true
                    ),
                ),
                array(
                    'label' => 'Medical Risk Factors',
                    'content'=> $this->renderPartial(
                        'portlets/prenatalConsultation/view/medical_risk_factor',
                        array(
                            'encounter' => $encounter,
                            'medicalRiskFactor' => $medicalRiskFactor,
                            'clinicalLibMedical' => $clinicalLibMedical,
                        ),
                        true
                    ),
                ),
                array(
                    'label' => 'Delivery Plan',
                    'content'=> $this->renderPartial(
                        'portlets/prenatalConsultation/view/delivery_plan',
                        array(
                            'encounter' => $encounter,
                            'form' => $form,
                            'deliveryPlan' => $deliveryPlan,
                            'ynlist' => $ynlist,
                        ),
                        true
                    ),
                    // 'visible' => $model['person']->sex == 'f' ? true : false,
                    // 'visible' => true ,
                ),
                array(
                    'label' => 'Date of Prenatal Visits',
                    'content'=> $this->renderPartial(
                        'portlets/prenatalConsultation/view/date_prenatal_visits',
                        array(
                            'encounter' => $encounter,
                            'form' => $form,
                            'prenatalVisit' => $prenatalVisit,
                            'prenatalVisits' => $prenatalVisits,
                            'prenatalConsultaionNoList' => $prenatalConsultaionNoList,
                        ),
                        true
                    ),
                    // 'visible' => true ,
                ),
                  // array(
                //     'label' => 'Obstetric History',
                //     'content'     => $this->renderPartial(
                //         'portlets/prenatalConsultation/view/obstetric_history',
                //         array(
                //             'encounter' => $encounter,
                //             'form' => $form,
                //             'obstetricHistory' => $obstetricHistory,
                //             'menstrualHistory' => $menstrualHistory,
                //         ),
                //         true
                //     ),
                // ),

            );

            $this->widget('bootstrap.widgets.TbTabs', array(
              'encodeLabel' => false,
              'tabs' => $tabs
            ));

            ?>

        </div>
    </div>
</div>
<div id="button-not-applicable">
    <div class="row-fluid">
        <div class="span12">
            <?php
            $this->widget(
              'bootstrap.widgets.TbButton',
              array(
                'buttonType' => 'submit',
                'type' => 'primary',
                'icon' => 'fa fa-save',
                'label' => 'Save',
                'htmlOptions' => array(
                  'id' => 'save-not-applicable',
                  'class' => 'pull-right'
                ),
              )
            );
            ?>
        </div>
    </div>
</div>


<?php
$this->endWidget();

?>

<script>
    $("#myModalDatePrenatalVisits").attr("hidden",true);
    $("#myModal").attr("hidden",true);
    if ($("#is_applicable").val() == '0' || $("#is_applicable").val() == 'N') {
        $("#yes_applicable").hide();
        $("#button-not-applicable").show();
    } else {
        $("#yes_applicable").show();
        $("#button-not-applicable").hide();
    }
    $("#date_of_lmp").on("change", function () {

        var date = new Date($(this).val());
        var newdate = new Date(date);

        newdate.setDate(newdate.getDate() + 280);

        var dd = newdate.getDate();
        var mm = newdate.getMonth() + 1;
        var y = newdate.getFullYear();

        var someFormattedDate = mm + '/' + dd + '/' + y;
        console.log(someFormattedDate);
        $("#edc").val(someFormattedDate);
        // document.getElementById('follow_Date').value = someFormattedDate;
    });
    $("#is_applicable").on("change", function () {
        if ($(this).val() == '0' || $(this).val() == 'N') {
            $("#yes_applicable").hide();
            $("#button-not-applicable").show();
        } else {
            $("#yes_applicable").show();
            $("#button-not-applicable").hide();
        }
    });

    $("#save-not-applicable").on("click", function (e) {
        e.preventDefault();
        // alert('asdasdasdas');
        if ($("#is_applicable").val() == "0") {
            Swal.fire(
                'Cant Proceed!',
                "Please select if applicable or not",
                'error'
            )
            return false;
        }

        if ($("#is_applicable").val() == "N") {
            Swal.fire({
                title: 'Not Applicable?',
                text: "Are you sure do you want to save?",
                type: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Save it!'
            }).then((result) => {
                console.log(result.value);
                if (result.value === true) {
                saveNotApplicable();
                return true;
                } else {
                return false;
                }
            })
        }

    });

    function saveNotApplicable() {
        // alert('save na');
        const encounter_nr = $("#prenatal-consultation-information").data('encounter_nr');
        const pid = $("#prenatal-consultation-information").data('pid');
        const is_applicable = $("#is_applicable").val();
        const url = $("#prenatal-consultation-information").data('url-not-applicable');
        // alert(url);

        $.ajax({
            url: url,
            type: 'post',
            data: {
                pid: pid,
                encounter_nr: encounter_nr,
                is_applicable: is_applicable,
            },
            dataType: 'json',
            beforeSend: () => {
                // toastr.info('Updating changes, please wait...');
                Alerts.loading({
                    content: 'Adding data. Please wait...'
                });
            },
            success: (response) => {
                Alerts.close();
                if (response['status']) {
                    // swal("Info!", "Can`t Delete, Already Discharged!", "info");
                    // $.fn.yiiGridView.update("birth-outcome-view");
                    Swal.fire(
                        'Saved!',
                        response['message'],
                        'success'
                    )
                    // alert(response['message']);
                } else {
                    Swal.fire(
                        'Failed to save!',
                        response['message'],
                        'error'
                    )
                    // toastr.success('Past Medical History updated');
                    // alert(response['message']);
                }
            },

            error: () => {
                Swal.fire(
                    'Please contact your administrator!',
                    'Something went wrong!',
                    'error'
                )
                // alert('Something went wrong, Please contact your administrator');
                // toastr.error('Something went wrong, Please contact your administrator');
            }

        });
    }
</script>