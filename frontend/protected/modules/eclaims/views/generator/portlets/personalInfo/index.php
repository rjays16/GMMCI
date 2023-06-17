<?php 
use SegHEIRS\components\web\ClientScript;
use SegHEIRS\components\web\Controller;
\Yii::import('bootstrap.widgets.TbActiveForm');
     $data[] = $model;
// \CVarDumper::dump($getChiefComplaint, 10, true);die;

 ?>
<input type="hidden" id="encounter_type" value="">
<input type="hidden" id="encounter_status" value="">
<div class="row-fluid">
    <div class="span6">
        <?php

        $this->widget('bootstrap.widgets.TbDetailView', array(
            'data'         => $data,
            'type'         => 'striped condensed bordered',
            'itemTemplate' => "<tr class=\"{class}\"><th style=\"width:30%\">{label}</th><td>{value}</td></tr>\n",
            'attributes'   => array(
                array(
                    'label' => 'Patient Name',
                    'value' => $model['person']->getFullName(),
                ),
                array(
                    'label' => 'HRN',
                    'value' => $model['person']->pid,
                ),
                array(
                    'label' => 'Case No',
                    'value' => $encounter->encounter_nr,
                ),
            ),
        ));
        ?>
    </div>

    <div class="span6">
        <?php
        $this->widget('bootstrap.widgets.TbDetailView', array(
            'data'         => $data,
            'itemTemplate' => "<tr class=\"{class}\"><th style=\"width:30%\">{label}</th><td>{value}</td></tr>\n",
            'type'         => 'striped condensed bordered',
            'attributes'   => array(
                array(
                    'label' => 'Admission Date',
                    'value' => date('M d, Y h:i a', strtotime($encounter->admission_dt)),
                ),
                array(
                    'label' => 'Discharge Date',
                    'value' => $encounter->discharge_date == null ? '' : date('M d, Y h:i a', strtotime($encounter->discharge_date.' '.$encounter->discharge_time)),
                ),
            ),
        ));
        ?>
    </div>
    <div class="row-fluid">
        <div class="span12">
            <?php 
                $form = $this->beginWidget(
                    'bootstrap.widgets.TbActiveForm',
                    array(
                        'id' => 'patient_clinical_records',
                        'type' => \TbActiveForm::TYPE_HORIZONTAL,
                        'htmlOptions' => array(
                            'data-url-save-patient-records' =>  $this->createUrl('clinicalRecords/saveClinicalRecords'),
                            'data-encounter_nr' =>  $encounter->encounter_nr,
                            'data-pid' =>  $model['person']->pid,

                        ),
                    ));
            ?>
            <legend>
              <h5>
                Chief Complaint
              </h5>
            </legend>
            <?php 

                    echo $form->textAreaRow($getChiefComplaint['data'], 'chief_complaint', array(
                        'class' => 'input-medium span7',
                        'id' => 'chief_complaint',
                        'disabled' => $getChiefComplaint['read_only'],
                        'placeholder' => ' Chief Complaint',
                    ));

             ?>
            <legend>
              <h5>
                History of Present Illness
              </h5>
            </legend>
            <?php
                echo $form->textAreaRow($patient_info['clinical_records'], 'present_illness', array(
                    'class' => 'input-medium span7',
                    'id' => 'present_illness',
                    'placeholder' => 'Brief History of Present Illness/OB History'
                ));
            ?>
            <legend>
              <h5>
                Pertinent Past Medical History
              </h5>
            </legend>
            <?php 
                    echo $form->textAreaRow($patient_info['past_med'], 'pertinent', array(
                        'class' => 'input-medium span7',
                        'id' => 'pertinent',
                        'placeholder' => 'Pertinent Past Medical History'
                    ));
             ?>  
            <legend></legend>
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
                                    'id' => 'save-patient-records',
                                    'class' => 'pull-right'
                                ),
                            )
                        );
                        ?>

                    </div>
                </div>

        <?php $this->endWidget(); /* box */ ?>
        </div>
    </div>
</div>

<script>
    $("#save-patient-records").on("click", function(e) {
        e.preventDefault();

        if($("#pertinent").val() != "" && $("#chief_complaint").val() != "" && $("#present_illness").val()){
            Swal.fire({
              title: 'Save Patient Clinical Records',
              text: "Are you sure do you want to save?",
              type: 'info',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Yes, Save it!'
            }).then((result) => {
              if (result.value) {
                saveClinicalRecords();
              }else{
                return false;
              }
            })
        }else{
            Swal.fire(
              'Failed!',
              'All fields are required!',
              'error'
            )
        }
        
    });

    function saveClinicalRecords() {
        // alert(1);
        const $form = $("#patient_clinical_records");
        const url = $form.data('url-save-patient-records');
        const encounter_nr = $form.data('encounter_nr');
        const pid = $form.data('pid');
        const present_illness = $("#present_illness").val();
        const pertinent = $("#pertinent").val();
        const chief_complaint = $("#chief_complaint").val();
        // alert(pid);
        $.ajax({
            url: url,
            type: 'post',
            data: {
                encounter_nr: encounter_nr,
                present_illness: present_illness,
                pertinent: pertinent,
                chief_complaint: chief_complaint,
            },
            dataType: 'json',
            beforeSend: () => {
                Alerts.loading({
                    content: 'Saving/Updating data. Please wait...'
                });
            },
            success: (response) => {
                Alerts.close();
                if (response) {
                      Swal.fire(
                      'Saved!',
                      'The data has been saved!',
                      'success'
                    )
                } else if (resonse == 'no data') {
                     Swal.fire(
                      'Saved!',
                      'The data has been saved!',
                      'success'
                    )
                }else{
                     Swal.fire(
                      'failed to save!',
                      'Something went wrong!',
                      'error'
                    )
                }
            },
        });

    }
</script>