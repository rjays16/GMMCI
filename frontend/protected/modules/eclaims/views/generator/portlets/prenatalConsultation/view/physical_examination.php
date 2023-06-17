<div class="row-fluid">
    <div class="span12 offset-3">
        <label for="is_normal">Vital Signs are Normal</label>
        <?php
        $list = CHtml::listData($ynlist, 'value', 'details');
        echo $form->dropDownList(
            $physicalExamination,
            'is_normal',
            $list,
            array(
                'class' => 'form-control col-md-12',
                'id' => 'is_normal',
                'required' => true,
                'options' => array(
                    $physicalExamination->is_normal != '' ? $physicalExamination->is_normal : ''  => array('selected' => true)
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
    <div class="span12 offset-3">
        <label for="is_normal">Ascertain the present Pregnancy is Low-Risk</label>
        <?php
        $list = CHtml::listData($ynlist, 'value', 'details');
        echo $form->dropDownList(
            $physicalExamination,
            'is_low_risk',
            $list,
            array(
                'class' => 'form-control col-md-12',
                'id' => 'is_low_risk',
                'required' => true,
                'options' => array(
                    $physicalExamination->is_low_risk != '' ? $physicalExamination->is_low_risk : ''  => array('selected' => true)
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
        $this->widget(
            'bootstrap.widgets.TbButton',
            array(
                'buttonType' => 'submit',
                'type' => 'primary',
                'icon' => 'fa fa-save',
                'label' => 'Save',
                'htmlOptions' => array(
                    'id' => 'save-physical-examination',
                    'class' => 'pull-right'
                ),
            )
        );
        ?>

    </div>
</div>

<script>
    $("#save-physical-examination").on("click", function(e) {
        e.preventDefault();
        // if (confirm('Do you really want to save?')) {
        //     savePhysicalExamination();
        // } else {
        //     return false;
        // }
        Swal.fire({
            title: 'Save Physical Examination',
            text: "Are you sure do you want to save?",
            type: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Save it!'
        }).then((result) => {
            if (result.value) {
                savePhysicalExamination();
            } else {
                return false;
            }
        })
    });

    function savePhysicalExamination() {
        const $form = $("#prenatal-consultation-information");
        const url = $form.data('url-save-physical-examination');
        const encounter_nr = $form.data('encounter_nr');
        const pid = $form.data('pid');
        const is_normal = $("#is_normal").val();
        const is_low_risk = $("#is_low_risk").val();

        $.ajax({
            url: url,
            type: 'post',
            data: {
                pid: pid,
                encounter_nr: encounter_nr,
                is_normal: is_normal,
                is_low_risk: is_low_risk,
            },
            dataType: 'json',
            beforeSend: () => {
                // toastr.info('Updating changes, please wait...');
                Alerts.loading({
                    content: 'Updating changes. Please wait...'
                });

            },
            success: (response) => {
                Alerts.close();
                if (response['status']) {
                    Swal.fire(
                        'Saved!',
                        response['message'],
                        'success'
                    )
                    // swal("Info!", "Can`t Delete, Already Discharged!", "info");
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