<div class="row-fluid">
    <div class="span6">
        <!-- <h5>Menstrual History</h5> -->
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <?php
        echo $form->DateFieldRow($menstrualHistory, 'init_prenatal_cons', array(
          'class' => 'input-medium span7',
          'id' => 'init_prenatal_cons',
        ));
        ?>
    </div>
    

</div>
<div class="row-fluid">
    <div class="span6">
        <?php
        echo $form->DateFieldRow($menstrualHistory, 'date_of_lmp', array(
          'class' => 'input-medium span7',
          'id' => 'date_of_lmp',

        ));

        ?>
    </div>
    <div class="span6">
        <?php
        // echo $form->HiddenField($menstrualHistory, 'init_prenatal_cons', array(
        //   'class' => 'input-medium span7',
        //   'id' => 'init_prenatal_cons',
        // ));

        ?>
    </div>

</div>


<div class="row-fluid">
    <div class="span6">
        <?php
        echo $form->TextFieldRow($menstrualHistory, 'age_of_menarche', array(
          'class' => 'input-medium span7',
          'id' => 'age_of_menarche',
          'placeholder' => 'Age of Menarche',
        ));
        ?>
    </div>
    <div class="span6">
        <?php
        echo $form->HiddenField($menstrualHistory, 'period_duration', array(
          'class' => 'input-medium span7',
          'id' => 'period_duration',
          'placeholder' => 'Period of Duration',
        ));
        ?>
    </div>

</div>
<div class="row-fluid">
    <div class="span6">
        <h5>Obstetric History</h5>
    </div>
</div>
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
              'id' => 'save-menstrual-history',
              'class' => 'pull-right'
            ),
          )
        );
        ?>

    </div>
</div>


<script>
    $("#save-menstrual-history").on("click", function (e) {
        e.preventDefault();
        const init_prenatal_cons = $("#init_prenatal_cons").val();
        const date_of_lmp = $("#date_of_lmp").val();

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
        // if (init_prenatal_cons == "") {
        //     Swal.fire(
        //         'Initial Prenatal Consultation is required!',
        //         'Please dont leave it blank!',
        //         'error'
        //     )

        //     return false;
        // }

        if (date_of_lmp == "") {
            Swal.fire(
                'Date of LMP is required!',
                'Please dont leave it blank!',
                'error'
            )

            return false;
        }
        Swal.fire({
            title: 'Save OB-GYNE History',
            text: "Are you sure do you want to save?",
            type: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Save it!'
        }).then((result) => {
            if (result.value === true) {
                saveMenstrual();
                saveObstetric();
            }
        })
    });

    function saveMenstrual() {
        // alert(1);
        const $form = $("#prenatal-consultation-information");
        const url = $form.data('url-save-menstrual-history');
        const encounter_nr = $form.data('encounter_nr');
        const pid = $form.data('pid');
        const init_prenatal_cons = $("#init_prenatal_cons").val();
        const date_of_lmp = $("#date_of_lmp").val();
        const age_of_menarche = $("#age_of_menarche").val();
        const period_duration = $("#period_duration").val();
        const is_applicable = $("#is_applicable").val();
        // alert(is_applicable);
        $.ajax({
            url: url,
            type: 'post',
            data: {
                pid: pid,
                encounter_nr: encounter_nr,
                init_prenatal_cons: init_prenatal_cons,
                date_of_lmp: date_of_lmp,
                age_of_menarche: age_of_menarche,
                period_duration: period_duration,
                is_applicable: is_applicable,
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
                         'Successfully saved OB-GYNE History',
                        'success'
                    )
                } else {
                    Swal.fire(
                        'Failed to save!',
                        'Failed in saving OB-GYNE History',
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
            }

        });
    }

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
                console.log(response);
                Alerts.close();
                // if (response['status']) {
                //     // swal("Info!", "Can`t Delete, Already Discharged!", "info");
                //     // alert(response['message']);
                //     Swal.fire(
                //         'Saved!',
                //         // response['message'],
                //         'Successfully saved OB-GYNE History',
                //         'success'
                //     )
                // } else {
                //     // toastr.success('Past Medical History updated');
                //     // alert(response['message']);
                //     Swal.fire(
                //         'Failed to save!',
                //         // response['message'],
                //         'Failed in saving OB-GYNE History',
                //         'error'
                //     )
                // }
            },

            error: () => {
                // alert('Something went wrong, Please contact your administrator');
                Swal.fire(
                    'Please contact your administrator!',
                    'Something went wrong!',
                    'error'
                )
                // toastr.error('Something went wrong, Please contact your administrator');
            }

        });
        // alert(url)
    }
</script>