<div class="row-fluid">
    <div class="span6">
        <?php

        echo $form->DateFieldRow($dtDischargeOutcome, 'date', array(
            'class' => 'input-medium span7',
            'id' => 'date_dtDischargeOutcome',
        ));
        ?>
    </div>

</div>
<div class="row-fluid">
    <div class="span6">
        <?php
       $dtDischargeOutcome->time  = $model->created_at= date('H:i',strtotime($dtDischargeOutcome->time));
        echo $form->TimeFieldRow($dtDischargeOutcome, 'time', array(
            'class' => 'input-medium span7',
            'id' => 'time_dtDischargeOutcome',
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
                    'id' => 'save-dt-discharge',
                    'class' => 'pull-right'
                ),
            )
        );
        ?>

    </div>
</div>

<script>
    $("#save-dt-discharge").on("click", function(e) {
        e.preventDefault();
        // if (confirm('Do you really want to save?')) {
        //     saveDTDeliveryOutcome();
        // } else {
        //     return false;
        // }

        const date_dtdischargeOutcome = $("#date_dtDischargeOutcome").val();
        const time_dtdischargeOutcome = $("#time_dtDischargeOutcome").val();
        if (date_dtdischargeOutcome == "") {
            Swal.fire(
                'Discharge Date is required!',
                'Please dont leave it blank!',
                'error'
            )

            return false;
        }

        if (time_dtdischargeOutcome == "") {
            Swal.fire(
                'Discharge Time is required!',
                'Please dont leave it blank!',
                'error'
            )

            return false;
        }
        Swal.fire({
            title: 'Save Date and time of Discharge',
            text: "Are you sure do you want to save?",
            type: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Save it!'
        }).then((result) => {
            if (result.value) {
                saveDTDischargeOutcome();
            } else {
                return false;
            }
        })
    });

    function saveDTDischargeOutcome() {
        // alert(1);
        const $form = $("#dt-delivery-outcome");
        const url = $form.data('url-save-dt-discharge-outcome');
        const encounter_nr = $form.data('encounter_nr');
        const pid = $form.data('pid');
        const date = $("#date_dtDischargeOutcome").val();
        const time = $("#time_dtDischargeOutcome").val();

        $.ajax({
            url: url,
            type: 'post',
            data: {
                pid: pid,
                encounter_nr: encounter_nr,
                date: date,
                time: time,
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
                    'Please contact your administrato!',
                    'Something went wrong!',
                    'error'
                )
                // alert('Something went wrong, Please contact your administrator');
                // toastr.error('Something went wrong, Please contact your administrator');
            }

        });
        // alert(url)
    }
</script>