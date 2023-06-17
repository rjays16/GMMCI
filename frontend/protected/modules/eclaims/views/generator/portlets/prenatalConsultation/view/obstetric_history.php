<div class="row-fluid">
    <div class="span6">
        <?php

        echo $form->NumberFieldRow($obstetricHistory, 'gravida', array(
            'class' => 'input-medium span7',
            'id' => 'gravida',
        ));
        ?>
    </div>
    <div class="span6">
        <?php

        echo $form->NumberFieldRow($obstetricHistory, 'parity', array(
            'class' => 'input-medium span7',
            'id' => 'parity',
        ));
        ?>
    </div>
</div>


<div class="row-fluid">
    <div class="span6">
        <?php

        echo $form->NumberFieldRow($obstetricHistory, 'term_births', array(
            'class' => 'input-medium span7',
            'id' => 'term_births',
        ));
        ?>
    </div>
    <div class="span6">
        <?php

        echo $form->NumberFieldRow($obstetricHistory, 'preterm_births', array(
            'class' => 'input-medium span7',
            'id' => 'preterm_births',
        ));
        ?>
    </div>

</div>




<div class="row-fluid">
    <div class="span6">
        <?php

        echo $form->NumberFieldRow($obstetricHistory, 'abortion', array(
            'class' => 'input-medium span7',
            'id' => 'abortion',
        ));
        ?>
    </div>

    <div class="span6">
        <?php

        echo $form->NumberFieldRow($obstetricHistory, 'living_children', array(
            'class' => 'input-medium span7',
            'id' => 'living_children',
        ));
        ?>
    </div>

</div>


<div class="row-fluid">
    <div class="span6">
        <?php

        echo $form->HiddenField($obstetricHistory, 'number_stillbirth', array(
            'class' => 'input-medium span7',
            'id' => 'number_stillbirth',
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
                    'id' => 'save-obstetric-history',
                    'class' => 'pull-right'
                ),
            )
        );
        ?>

    </div>
</div>
<script>
    $("#save-obstetric-history").on("click", function(e) {
        e.preventDefault();
        const gravida = $("#gravida").val();
        const parity = $("#parity").val();
        const term_births = $("#term_births").val();
        const preterm_births = $("#preterm_births").val();
        const abortion = $("#abortion").val();
        const living_children = $("#living_children").val();

        if (term_births == "") {
            Swal.fire(
                'No. of Full Term Pregnancy is required!',
                'Please dont leave it blank!',
                'error'
            )

            return false;
        }
        if (preterm_births == "") {
            Swal.fire(
                'No. of Premature Pregnancy is required!',
                'Please dont leave it blank!',
                'error'
            )

            return false;
        }
        if (abortion == "") {
            Swal.fire(
                'No. of Abortion is required!',
                'Please dont leave it blank!',
                'error'
            )

            return false;
        }
        if (living_children == "") {
            Swal.fire(
                'No. of Living Children is required!',
                'Please dont leave it blank!',
                'error'
            )

            return false;
        }
        if (gravida == "") {
            Swal.fire(
                'Gravida is required!',
                'Please dont leave it blank!',
                'error'
            )

            return false;
        }

        if (parity == "") {
            Swal.fire(
                'Parity is required!',
                'Please dont leave it blank!',
                'error'
            )

            return false;
        }


        Swal.fire({
            title: 'Save Obstetric History',
            text: "Are you sure do you want to save?",
            type: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Save it!'
        }).then((result) => {
            if (result.value) {
                saveObstetric();
            } else {
                return false;
            }
        })
    });

    function saveObstetric() {
        const $form = $("#prenatal-consultation-information");
        const url = $form.data('url-save-obstetric-history');
        const encounter_nr = $form.data('encounter_nr');
        const pid = $form.data('pid');
        const gravida = $("#gravida").val();
        const parity = $("#parity").val();
        const term_births = $("#term_births").val();
        const preterm_births = $("#preterm_births").val();
        const abortion = $("#abortion").val();
        const living_children = $("#living_children").val();
        const number_stillbirth = $("#number_stillbirth").val();
        // alert(pid);
        $.ajax({
            url: url,
            type: 'post',
            data: {
                pid: pid,
                encounter_nr: encounter_nr,
                gravida: gravida,
                parity: parity,
                term_births: term_births,
                preterm_births: preterm_births,
                abortion: abortion,
                living_children: living_children,
                number_stillbirth: number_stillbirth,

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
                // alert('Something went wrong, Please contact your administrator');
                Swal.fire(
                    'Please contact your administrato!',
                    'Something went wrong!',
                    'error'
                )
                // toastr.error('Something went wrong, Please contact your administrator');
            }

        });
        // alert(url)
    }
</script>