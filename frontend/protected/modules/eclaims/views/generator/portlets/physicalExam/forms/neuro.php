<?php
// \CVarDumper::dump($peAbdomen, 10, true);die;
// foreach ($peChest as $key => $data){
?>
<input type="hidden" id="neuroCount" value="<?php echo count($peNeuro); ?>">
<h5>NEURO <span style="color:red">*</span>:</h5>
<div class="span12" id="heent_div">
    <?php
    // if()
    echo CHtml::checkBoxList('Neuro[neuro_data]',
        $selected_neuro['entries'],
        $peNeuro,
        array(
            'class' => 'neuro_name',
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
        <label id="label_remarks_neuro">Others:</label>
        <?php
        echo $form->textAreaRow($selected_neuro['for_remarks'], 'remarks', array(
            'class' => 'input-medium span7',
            'id' => 'remarks_neuro',
            'disabled' => false,
            'placeholder' => 'Others ...',
            'htmlOptions' => array('class' => 'wew')
        ));
        ?>
    </div>
</div>

<script>
    var neuroCount = $('#neuroCount').val();
    $('.neuro_name:eq(0)').on('click', function () {
        if ($(this).is(':checked')) {
            for (var i = 0; i < neuroCount; i++) {
                if (i !== 0) {
                    $(".neuro_name").eq(i).prop("checked", false);
                    $(".neuro_name").eq(i).prop("disabled", true);
                }
            }
            $("#remarks_neuro").prop("disabled", true);
            $('#remarks_neuro').val("");
        } else {
            for (var i = 0; i < neuroCount; i++) {
                $(".neuro_name").eq(i).prop("disabled", false);
            }
            $("#remarks_neuro").prop("disabled", false);
        }
    });

    $(document).ready(function () {
        if ($('.neuro_name:eq(0)').is(':checked')) {
            for (var i = 0; i < neuroCount; i++) {
                if (i !== 0) {
                    $(".neuro_name").eq(i).prop("disabled", true);
                }
            }
            $("#remarks_neuro").prop("disabled", true);
        }
    });


    $("#save-neuro").on("click", function (e) {
        e.preventDefault();

        var neuro = [];

        $.each($("input[name='Neuro[name][]']:checked"), function () {
            neuro.push($(this).val());
        });

        if (neuro != "") {
            Swal.fire({
                title: 'Save NEURO-EXAM',
                text: "Are you sure do you want to save?",
                type: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Save it!'
            }).then((result) => {
                if (result.value) {
                    saveNeuro(neuro);
                } else {
                    return false;
                }
            })
        } else {
            Swal.fire(
                'Failed!',
                'Please select alteast One(1) on the list!',
                'error'
            )
        }
    });

    function saveNeuro(neuro) {
        // alert(1);
        const $form = $("#pe-information");
        const url = $form.data('url-save-pe-neuro');
        const encounter_nr = $form.data('encounter_nr');
        const pid = $form.data('pid');
        const neuro_data = neuro;
        const remarks = $("#remarks_neuro").val();
        // alert(url);
        $.ajax({
            url: url,
            type: 'post',
            data: {
                encounter_nr: encounter_nr,
                pid: pid,
                neuro_data: neuro_data,
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
                } else {
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

    #remarks_neuro {
        width: 312px;
    }

    #span_neuro {
        color: #bc2f2f;
    }

    #heent_container {
        margin-top: 14px;
    }

</style>