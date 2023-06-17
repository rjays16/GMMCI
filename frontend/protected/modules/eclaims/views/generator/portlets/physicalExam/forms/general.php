<?php
$remarks = $selected_gensurvey['for_remarks']->remarks;
// \CVarDumper::dump($selected_gensurvey['for_remarks'], 10, true);die;
// foreach ($peGen_survey as $key => $data){
?>
<h5>GENERAL SURVEY <span style="color:red">*</span>:</h5>
<div class="span5" id="heent_div">
    <?php
    // if()
    echo CHtml::checkBoxList(
        'Gen[gen_data]',
        $selected_gensurvey['entries'],
        $peGen_survey,
        array(
            'class' => 'gen_name',
            'labelOptions' => array(
                'style' => 'display:inline;',
            ),
            'style' => 'margin-top: -3px;'
        )
    );
    ?>

</div>
<?php
// }
?>

<div class="row-fluid" id='heent_container'>
    <div class="span12" id="text_field_div">
        <label id="label_remarks_gen">Remarks:</label>
        <?php 
            echo $form->textAreaRow($selected_gensurvey['for_remarks'], 'remarks', array(
                'class' => 'input-medium span7',
                'id' => 'remarks_gen',
                'disabled' => false,
                'placeholder' => 'Remarks ...',
                'htmlOptions' => array( 'class' => 'wew')
            ));
         ?>
        <br>&nbsp;
        <span id="span_gen"><i>Please fill-out this area if you check 'Altered Sensorium'</i></span>
    </div>
</div>


<script>
    $('.gen_name:eq(0)').on('click', function() {
        if ($(this).is(':checked')) {
            $(".gen_name:eq(1)").prop("checked", false);
            $(".gen_name:eq(1)").prop("disabled", true);
            $("#remarks_gen").prop("disabled", true);
            $('#remarks_gen').val("");

        } else {
            $(".gen_name:eq(1)").prop("disabled", false);
            $("#remarks_gen").prop("disabled", false);
        }
    });

    $(document).ready(function() {
        if ($('.gen_name:eq(0)').is(':checked')) {
            $(".gen_name:eq(1)").prop("disabled", true);
            $("#remarks_gen").prop("disabled", true);

        }
    });

    $("#save-gen").on("click", function(e) {
        e.preventDefault();

        var gen = [];

        $.each($("input[name='Gen[name][]']:checked"), function() {
            gen.push($(this).val());
        });

        if(gen != "") {
            Swal.fire({
              title: 'Save GENERAL SURVEY',
              text: "Are you sure do you want to save?",
              type: 'info',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Yes, Save it!'
            }).then((result) => {
              if (result.value) {
                saveGen(gen);
              }else{
                return false;
              }
            })
        }else{
            Swal.fire(
              'Failed!',
              'Please select alteast One(1) on the list!',
              'error'
            )
        }


    });

    function saveGen(gen) {
        // alert(1);
        const $form = $("#pe-information");
        const url = $form.data('url-save-pe-gensurvey');
        const encounter_nr = $form.data('encounter_nr');
        const pid = $form.data('pid');
        const gen_data = gen;
        const remarks = $("#remarks_gen").val();
        // alert(gen_data);
        $.ajax({
            url: url,
            type: 'post',
            data: {
                encounter_nr: encounter_nr,
                pid: pid,
                gen_data: gen_data,
                remarks: remarks,
            },
            dataType: 'json',
            beforeSend: () => {
                Alerts.loading({
                    content: 'Saving/Updating data. Please wait...'
                });
            },
            success: (response) => {
                Alerts.close();
                if (response) {
                      Swal.fire(
                      'Saved!',
                      'The data has been saved!',
                      'success'
                    )
                } else if (resonse == 'no data') {
                     Swal.fire(
                      'Saved!',
                      'The data has been saved!',
                      'success'
                    )
                }else{
                     Swal.fire(
                      'failed to save!',
                      'Something went wrong!',
                      'error'
                    )
                }
            },
        });

    }
</script>

<style type="text/css">
    body {
        line-height: 1px;
    }

    #text_field_div {
        margin-top: 22px;
    }

    #remarks_gen {
        width: 312px;
    }

    #span_gen {
        color: #bc2f2f;
    }

    #heent_container {
        margin-top: 14px;
    }
</style>