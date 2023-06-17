<div class="row-fluid">
    <div class="span6">

    <?php 
        echo $form->textAreaRow($getChiefComplaint, 'chief_complaint', array(
            'class' => 'input-medium span7',
            'id' => 'chief_complaint',
            'placeholder' => 'Chief Complaint',
            'style' => 'width: 100%;'
        ));
     ?>

    </div>
    <div class="span6">

     <?php 
        echo $form->textAreaRow($getChiefComplaint, 'others', array(
            'class' => 'input-medium span7',
            'id' => 'others',
            'placeholder' => 'Other Complaint',
            'style' => 'margin-left: -13%;width: 105%;',
        ));     
     ?>

    </div>
</div>

<hr>

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
                    'id' => 'chief-complaint-save',
                    'class' => 'pull-right'
                ),
            )
        );
        ?>

    </div>
</div>

<script>


    $("#chief-complaint-save").on("click", function (e) {
        e.preventDefault();
            Swal.fire({
                title: 'Save Chief Complaint',
                text: "Are you sure do you want to save?",
                type: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Save it!'
            }).then((result) => {
                if (result.value) {
                    saveChiefComplaint();
                } else {
                    return false;
                }
            });
    });

    function saveChiefComplaint() {
        const chief_complaint = $("#chief_complaint").val();
        const other_chief_complaint = $("#others").val();
        const $form = $("#chief-complaint-record");
        const url = $form.data('url-save-chief-complaint');
        const pid = $form.data('pid');
        const encounter_nr = $form.data('encounter_nr');

        // alert(systolic+'/'+diastolic+'/'+heart_rate);

        $.ajax({
            url: url,
            type: 'post',
            data: {
                chief_complaint: chief_complaint,
                other_chief_complaint: other_chief_complaint,
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
                    $.fn.yiiGridView.update("vital_signs_list");
                } else {
                    Swal.fire({
                        title: response.message,
                        type: 'error',
                        showConfirmButton: true,
                        timer: 1500
                    });
                   Alerts.close();
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
    .control-label[for="Cf4ChiefcomplaintData_others"]{
        margin-left: -9%;
    }
</style>