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
                    'value' => date('M d, Y h:i a', strtotime($encounter->discharge_date.' '.$encounter->discharge_time)),
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
                    'id'     => 'care-form',
                    'type'   => 'horizontal',
                    // 'action' => $this->createUrl(
                    //     'care.submit',
                    //     array(
                    //             'id' => $encounter->encounter_nr,
                    //     )
                    // ),
                )
            );

            // echo $form->textAreaRow($model['person'], 'history', array(
            //     'class' => 'input-medium span7',
            //     'labelOptions' => array(
            //         'label' => Chtml::label('History of Present Illness', '', array(
            //             'style' => 'color:red'
            //         )),
            //     ),
            // ));

        ?>

        <legend>
          <h5>
            Past Medical History
          </h5>
        </legend>

        <legend>
          <h5>
            Vital Signs
          </h5>
        </legend>

        <?php $this->endWidget(); /* box */ ?>
    </div>
</div>