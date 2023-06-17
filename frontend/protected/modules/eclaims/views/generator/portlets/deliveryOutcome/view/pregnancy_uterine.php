<div class="row-fluid">
    <div class="span6 offset-3">
        <label for="is_normal">Maternal Outcome</label>
        <?php
        echo $form->TextField($pregnancyUterine, 'maternal_outcome', array(
            'class' => 'input-medium span7',
            'id' => 'maternal_outcome',
            'placeholder' => 'Obstetric Index',
        ));
        ?>
    </div>

</div>

<div class="row-fluid">
    <div class="span6">
        <?php
        echo $form->TextFieldRow($pregnancyUterine, 'aog_by_lmp', array(
            'class' => 'input-medium span7',
            'id' => 'aog_by_lmp',
        ));
        ?>
    </div>

</div>

<div class="row-fluid">
    <div class="span6">
        <?php

        echo $form->TextFieldRow($pregnancyUterine, 'manner_of_delivery', array(
            'class' => 'input-medium span7',
            'id' => 'manner_of_delivery',
        ));
        ?>
    </div>

</div>


<div class="row-fluid">
    <div class="span6">
        <?php

        echo $form->TextFieldRow($pregnancyUterine, 'presentation', array(
            'class' => 'input-medium span7',
            'id' => 'presentation',
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
                    'id' => 'save-pregnancy-uterine',
                    'class' => 'pull-right'
                ),
            )
        );
        ?>

    </div>
</div>

<script>
    $("#save-pregnancy-uterine").on("click", function(e) {
        e.preventDefault();
        // if (confirm('Do you really want to save?')) {
        //     savePregnancyUterine();
        // } else {
        //     return false;
        // }
        const aog_by_lmp = $("#aog_by_lmp").val();
        if (aog_by_lmp == "") {
            Swal.fire(
                'Aog by LMP is required!',
                'Please dont leave it blank!',
                'error'
            )

            return false;
        }
        Swal.fire({
            title: 'Save Pregnancy Uterine',
            text: "Are you sure do you want to save?",
            type: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Save it!'
        }).then((result) => {
            if (result.value) {
                savePregnancyUterine();
            } else {
                return false;
            }
        })
    });

    function savePregnancyUterine() {
        const $form = $("#dt-delivery-outcome");
        const url = $form.data('url-save-pregnancy-uterine');
        const encounter_nr = $form.data('encounter_nr');
        const pid = $form.data('pid');
        const maternal_outcome = $("#maternal_outcome").val();
        const aog_by_lmp = $("#aog_by_lmp").val();
        const manner_of_delivery = $("#manner_of_delivery").val();
        const presentation = $("#presentation").val();

        $.ajax({
            url: url,
            type: 'post',
            data: {
                pid: pid,
                encounter_nr: encounter_nr,
                maternal_outcome: maternal_outcome,
                aog_by_lmp: aog_by_lmp,
                manner_of_delivery: manner_of_delivery,
                presentation: presentation,
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