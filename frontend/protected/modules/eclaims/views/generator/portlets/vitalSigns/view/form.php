<?php

use SegHis\modules\eclaims\helpers\cf4\CF4Helper;

?>
<span style="color:red; display: inline-block; margin-bottom: 25px; text-align:center"><i>Definite value required for Patient from age three years old and above. Use “1” as value for systolic and diastolic for patients which BP are not available or not required and “2” for Palpatory Patients.</i></span>
<div class="container">
    <div class="row">
        <div class="span8">
            <label class="control-label">Blood Pressure <span style="color:red;">*</label>
            <div class="controls">
                <input type="number" name="" class="bp_systolic" min="0" placeholder="Systolic">
                <span>/</span>
                <input type="number" name="" class="bp_diastolic" min="0" placeholder="Diastolic">
                <span>mmHg</span>
            </div>
        </div>
    </div>
    <p style="display: block;"></p>
    <div class="row">
        <div class="span5">
            <label class="control-label">Heart Rate <span style="color:red;">*</label>
            <div class="controls">
                <input type="number" name="" class="heart_rate" min="0" placeholder="Heart Rate">
                <span>/m</span>
            </div>
        </div>

        <div class="span5" style="margin-left: -84px;">
            <label class="control-label">Height <span style="color:red;">*</label>
            <div class="controls">
                <input type="number" step="0.01" name="" class="height" min="0"  placeholder="height">
                <span>(cm)</span>
        </div>
    </div>
    </div>
    <p style="display: block;"></p>
    <div class="row">
        <div class="span5">
            <label class="control-label">Temperature <span style="color:red;">*</label>
            <div class="controls">
                <input type="number" name="" class="temp" min="0" placeholder="Temperature">
                <span>℃</span>
            </div>
        </div>

        <div class="span5" style="margin-left: -84px;">
            <label class="control-label">Weight <span style="color:red;">*</label>
            <div class="controls">
                <input type="number" step="0.01" name="" class="weight" min="0" max="6" placeholder="weight">
                <span>(kg)</span>
            </div>
    </div>
    </div>
    <p style="display: block;"></p>
    <div class="row">
        <label class="control-label">Respiratory Rate <span style="color:red;">*</label>
        <div class="controls">
            <input type="number" name="" class="resp_rate" min="0" placeholder="Respiratory Rate">
            <span>/m</span>
        </div>
    </div>
</div>
<hr>
<p style="display: block;"></p>
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
                    'id' => 'add-vital-signs',
                    'class' => 'pull-right'
                ),
            )
        );
        ?>

    </div>
</div>
<p style="display: block;"></p>
<script>


    $("#add-vital-signs").on("click", function (e) {
        e.preventDefault();
        // https://www.philhealth.gov.ph/advisories/2020/adv2020-0057.pdf
        <?php if ($encounter->admission_dt > CF4Helper::getVitalSignsImplementationDate()): ?>
        if( $(".bp_systolic").val() == "" ||
            $(".bp_diastolic").val() == "" ||
            $(".heart_rate").val() == "" ||
            $(".resp_rate").val() == "" ||
            $(".temp").val() == ""||
            $(".height").val() == "" ||
            $(".weight").val() == ""
            )
        <?php else : ?>
            if( $(".bp_systolic").val() == "" ||
                $(".bp_diastolic").val() == "" ||
                $(".heart_rate").val() == "" ||
                $(".resp_rate").val() == "" ||
                $(".temp").val() == ""
            )
            <?php endif; ?>
        {
            Swal.fire({
                title: 'All fields are required!',
                type: 'warning',
                showConfirmButton: true,
                timer: 1500
            });
        }else{

            Swal.fire({
                title: 'Save Data',
                text: "Are you sure do you want to save?",
                type: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Save it!'
            }).then((result) => {
                if (result.value) {
                    saveVitalSigns();
                } else {
                    return false;
                }
            });
        }
    
    });

    function saveVitalSigns() {
        const systolic = $(".bp_systolic").val();
        const diastolic = $(".bp_diastolic").val();
        const heart_rate = $(".heart_rate").val();
        const resp_rate = $(".resp_rate").val();
        const temp = $(".temp").val();
        const height = $(".height").val();
        const weight = $(".weight").val();

        const $form = $("#vitalSigns-record");
        const url = $form.data('url-save-vital-signs');
        const pid = $form.data('pid');
        const encounter_nr = $form.data('encounter_nr');

        // alert(systolic+'/'+diastolic+'/'+heart_rate);

        $.ajax({
            url: url,
            type: 'post',
            data: {
                diastolic: diastolic,
                systolic: systolic,
                resp_rate: resp_rate,
                temp: temp,
                height: height,
                weight: weight,
                heart_rate: heart_rate,
                encounter_nr: encounter_nr,
                pid: pid,
            },
            dataType: 'json',
            beforeSend: () => {
                Alerts.loading({
                    content: 'Saving/Updating data. Please wait...'
                });
            },
            success: (response) => {
                if (response.status) {
                    Swal.fire({
                        title: 'The data has been saved!',
                        type: 'success',
                        showConfirmButton: true,
                        timer: 1500
                    });
                    Alerts.close();
                    $("#bp_systolic").val('');
                    $("#bp_diastolic").val('');
                    $("#heart_rate").val('');
                    $("#resp_rate").val('');
                    $("#temp").val('');
                    $("#height").val('');
                    $("#weight").val('');
                    $.fn.yiiGridView.update("vital_signs_list");
                } else {
                    Swal.fire({
                        title: response.message,
                        type: 'error',
                        showConfirmButton: true,
                        timer: 1500
                    });
                }
            },
            error: (response) => {
                Alerts.close();
                Swal.fire({
                    title: 'Something went wrong, Please contact your administrator',
                    text: response.message,
                    type: 'error',
                    showConfirmButton: true,
                    timer: 2000
                });
            }
        });
    }
</script>

<style>
    #dias{
        margin-left: -99px;
    }
    #bp_systolic{
        width: 113%;
    }
    #bp_diastolic{
        width: 113%;
    }
    #heart_rate{
        width: 113%;
    }
    #resp_rate{
        width: 113%;
    }
    #temp{
        width: 113%;
    }
    #dias_label{
        width: 0;
        margin-left: 384px;
        margin-top: -30px;
    }
</style>