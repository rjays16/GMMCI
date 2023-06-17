<div class="row-fluid">
    <div class="span6">
        <label for="is_normal">Orientation to MCPA/Availment of Benefit</label>
        <?php
        $list = CHtml::listData($ynlist, 'value', 'details');
        echo $form->dropDownList(
            $deliveryPlan,
            'is_benefit',
            $list,
            array(
                'class' => 'form-control col-md-12',
                'id' => 'is_benefit',
                'required' => true,
                'options' => array(
                    $deliveryPlan->is_benefit != '' ? $deliveryPlan->is_benefit : ''  => array('selected' => true)
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
    <div class="span6">
        <?php

        echo $form->TextFieldRow($deliveryPlan, 'edc', array(
            'class' => 'input-medium span7',
            'id' => 'edc',
            'readonly' => true,

        ));
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
                    'id' => 'save-delivery-plan',
                    'class' => 'pull-right'
                ),
            )
        );
        ?>

    </div>
</div>

<script>
    $("#save-delivery-plan").on("click", function(e) {
        e.preventDefault();
        // if (confirm('Do you really want to save?')) {
        //     saveDeliveryPlan();
        // } else {
        //     return false;
        // }
        Swal.fire({
            title: 'Save Delivery Plan',
            text: "Are you sure do you want to save?",
            type: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Save it!'
        }).then((result) => {
            if (result.value) {
                saveDeliveryPlan();
            } else {
                return false;
            }
        })
    });

    function saveDeliveryPlan() {
        const $form = $("#prenatal-consultation-information");
        const url = $form.data('url-save-delivery-plan');
        const encounter_nr = $form.data('encounter_nr');
        const pid = $form.data('pid');
        const is_benefit = $("#is_benefit").val();
        const edc = $("#edc").val();
        // const is_low_risk = $("#is_low_risk").val();

        $.ajax({
            url: url,
            type: 'post',
            data: {
                pid: pid,
                encounter_nr: encounter_nr,
                is_benefit: is_benefit,
                edc: edc,
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
                    // swal("Info!", "Can`t Delete, Already Discharged!", "info");
                    // alert(response['message']);
                    Swal.fire(
                        'Saved!',
                        response['message'],
                        'success'
                    )
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