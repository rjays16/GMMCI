<?php 
     $data[] = $model;
 ?>

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
</div>

<div class="row-fluid">
    <div class="span12">

        <legend>
          <h5>
            Clinical Records
          </h5>
        </legend>

        <?php 
            $form = $this->beginWidget(
                'bootstrap.widgets.TbActiveForm',
                array(
                    'id'     => 'clinical-records',
                    'type'   => 'horizontal',
                    'htmlOptions' => array(
                        'class' => 'form-bordered form-label-stripped',
                        'data-url-save-clinical-records' =>  '',
                    ),
                )
            );

            echo $form->textAreaRow($patient_info, 'present_illness', array(
                'class' => 'input-medium span7',
                'id' => 'present-illness',
                'placeholder' => 'Brief History of Present Illness/OB History'
            ));

        ?>

        <legend>
          <h5>
            Past Medical History
          </h5>
        </legend>
            <?php 
                    echo $form->textAreaRow($patient_info, 'past_med_history', array(
                        'class' => 'input-medium span7',
                        'id' => 'present-illness',
                        'placeholder' => 'Pertinent Past Medical History'
                    ));

                    echo $form->textFieldRow($patient_info, 'disease_code', array(
                        'class' => 'input-medium span7',
                        'id' => 'present-illness',
                        'placeholder' => 'Disease Code of Past Medical History',
                        'style' => 'margin-top:11px',

                    ));
             ?>

        <legend>
          <h5>
            Vital Signs
          </h5>
        </legend>
            <?php 
                    echo $form->textFieldRow($patient_info, 'bp', array(
                        'class' => 'input-medium span7',
                        'id' => 'blood-pressure',
                        'placeholder' => 'Blood Pressure',
                    ));

                    echo $form->textFieldRow($patient_info, 'cr', array(
                        'class' => 'input-medium span7',
                        'id' => 'cardiac-rate',
                        'placeholder' => 'Cardiac Rate',
                    ));

                    echo $form->textFieldRow($patient_info, 'rr', array(
                        'class' => 'input-medium span7',
                        'id' => 'resp_rate',
                        'placeholder' => 'Respiratory Rate',
                    ));

                    echo $form->textFieldRow($patient_info, 'temperature', array(
                        'class' => 'input-medium span7',
                        'id' => 'temperature',
                        'placeholder' => 'Respiratory Rate',
                    ));
             ?>

             <div class="row-fluid">
                <div class="span12">
                    <legend></legend>
                        <?php 
                              $this->widget(
                                  'bootstrap.widgets.TbButton',
                                  array(
                                      'buttonType' => 'submit',
                                      'type' => 'primary',
                                      'icon' => 'fa fa-save',
                                      'label' => 'Save',
                                      'htmlOptions' => array(
                                          'id' => 'save-clinical-records',
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
