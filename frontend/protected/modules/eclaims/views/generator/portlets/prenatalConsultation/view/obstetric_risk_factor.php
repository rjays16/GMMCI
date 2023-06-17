<div class="span5" id="heent_div">
    <?php
    // if()
    // \CVarDumper::dump($clinicalLibObstetric, 10, true);
    // die;
    echo CHtml::checkBoxList(
        'ObstetricRiskFactor[name][]',
        $obstetricRiskFactor,
        $clinicalLibObstetric,
        array(
            'class' => 'heent_name',
            'labelOptions' => array(
                'style' => 'display:inline;',
            ),
            'style' => 'margin-top: -3px;'
        )
    );
    ?>

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
                    'id' => 'save-risk-factor',
                    'class' => 'pull-right'
                ),
            )
        );
        ?>

    </div>
</div>

<script>
    $("#save-risk-factor").on("click", function(e) {
        e.preventDefault();

        var riskfactor = [];

        $.each($("input[name='ObstetricRiskFactor[name][]']:checked"), function() {
            riskfactor.push($(this).val());
        });

        Swal.fire({
            title: 'Save Obstetric Risk Factor',
            text: "Are you sure do you want to save?",
            type: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Save it!'
        }).then((result) => {
            if (result.value) {
                saveObstetricRiskFactor(riskfactor);
            } else {
                return false;
            }
        })
        // if (confirm('Do you really want to save?')) {
        //     saveObstetricRiskFactor(riskfactor);
        // } else {
        //     return false;
        // }
    });

    function saveObstetricRiskFactor(riskfactor) {

        const $form = $("#prenatal-consultation-information");
        const url = $form.data('url-save-obstetric-risk-factor');
        const encounter_nr = $form.data('encounter_nr');
        const pid = $form.data('pid');
        const clinical_history_id = riskfactor;
        $.ajax({
            url: url,
            type: 'post',
            data: {
                encounter_nr: encounter_nr,
                pid: pid,
                clinical_history_id: clinical_history_id,
            },
            dataType: 'json',
            beforeSend: () => {
                Alerts.loading({
                    content: 'Updating changes. Please wait...'
                });
            },
            success: (response) => {
                Alerts.close();
                if (response['status']) {
                    // alert(response['message']);
                    Swal.fire(
                        'Saved!',
                        response['message'],
                        'success'
                    )
                } else {
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