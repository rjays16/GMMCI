<div class="row-fluid">
    <div class="span4">
        <?php

        echo $form->DateFieldRow($spfdeliveryoutcome, 'date', array(
            'class' => 'input-medium span7',
            'id' => 'date_spfdeliveryOutcome',
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
                    'id' => 'save-spf-delivery-outcome',
                    'class' => 'pull-right'
                ),
            )
        );
        ?>

    </div>
</div>

<script>
    $("#save-spf-delivery-outcome").on("click", function(e) {
        e.preventDefault();
        // if (confirm('Do you really want to save?')) {
        //     saveSPFDeliveryOutcome();
        // } else {
        //     return false;
        // }

        const date_spfdeliveryOutcome = $("#date_spfdeliveryOutcome").val();
        if (date_spfdeliveryOutcome == "") {
            Swal.fire(
                'Scheduled Postpartum follow up is required!',
                'Please dont leave it blank!',
                'error'
            )

            return false;
        }

        Swal.fire({
            title: 'Save Postpartum Follow up',
            text: "Are you sure do you want to save?",
            type: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Save it!'
        }).then((result) => {
            if (result.value) {
                saveSPFDeliveryOutcome();
            } else {
                return false;
            }
        })
    });

    function saveSPFDeliveryOutcome() {
        // alert(1);
        const $form = $("#dt-delivery-outcome");
        const url = $form.data('url-save-spf-delivery-outcome');
        const encounter_nr = $form.data('encounter_nr');
        const pid = $form.data('pid');
        const date = $("#date_spfdeliveryOutcome").val();

        $.ajax({
            url: url,
            type: 'post',
            data: {
                pid: pid,
                encounter_nr: encounter_nr,
                date: date,
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
                    // toastr.success('Past Medical History updated');
                    // alert(response['message']);
                    Swal.fire(
                        'Failed to save!',
                        response['message'],
                        'error'
                    )
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