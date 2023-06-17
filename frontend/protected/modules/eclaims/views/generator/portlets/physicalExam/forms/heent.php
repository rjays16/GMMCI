<?php
// \CVarDumper::dump($peHeent, 10, true);die;
// foreach ($peHeent as $key => $data){
?>
<input type="hidden" id="heentCount" value="<?php echo count($peHeent); ?>">
<h5>HEENT <span style="color:red">*</span>:</h5>
<div class="span12" id="heent_div">
    <?php
    // if()
    echo CHtml::checkBoxList('Heent[heent_data]',
        $selected_heent['entries'],
        $peHeent,
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
<?php
// }
?>

<div class="row-fluid" id='heent_container'>
    <div class="span12" id="text_field_div">
        <label id="label_remarks">Others:</label>
        <?php
        echo $form->textAreaRow($selected_heent['for_remarks'], 'remarks', array(
            'class' => 'input-medium span7',
            'id' => 'remarks',
            'disabled' => false,
            'placeholder' => 'Others ...',
            'htmlOptions' => array('class' => 'wew')
        ));
        ?>
    </div>
</div>

<script>
    var heentCount = $('#heentCount').val();
    $('.heent_name:eq(0)').on('click', function () {
        if ($(this).is(':checked')) {
            for (var i = 0; i < heentCount; i++) {
                if (i !== 0) {
                    $(".heent_name").eq(i).prop("checked", false);
                    $(".heent_name").eq(i).prop("disabled", true);
                }
            }
            $("#remarks").prop("disabled", true);
            $('#remarks').val("");
        } else {
            for (var i = 0; i < heentCount; i++) {
                $(".heent_name").eq(i).prop("disabled", false);
            }
            $("#remarks").prop("disabled", false);
        }
    });

    $(document).ready(function () {
        if ($('.heent_name:eq(0)').is(':checked')) {
            for (var i = 0; i < heentCount; i++) {
                if (i !== 0) {
                    $(".heent_name").eq(i).prop("disabled", true);
                }
            }
            $("#remarks").prop("disabled", true);
        }
    });

    $("#save-heent").on("click", function (e) {
        e.preventDefault();

        var heent = [];

        $.each($("input[name='Heent[name][]']:checked"), function () {
            heent.push($(this).val());
        });

        if (heent != "") {
            Swal.fire({
                title: 'Save HEENT',
                text: "Are you sure do you want to save?",
                type: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Save it!'
            }).then((result) => {
                if (result.value) {
                    saveHeent(heent);
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

    function saveHeent(heent) {
        // alert(1);
        const $form = $("#pe-information");
        const url = $form.data('url-save-pe-heent');
        const encounter_nr = $form.data('encounter_nr');
        const pid = $form.data('pid');
        const heent_data = heent;
        const remarks = $("#remarks").val();
        // alert(heent_data);
        $.ajax({
            url: url,
            type: 'post',
            data: {
                encounter_nr: encounter_nr,
                pid: pid,
                heent_data: heent_data,
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

    #remarks {
        width: 312px;
    }

    #span {
        color: #bc2f2f;
    }

    #heent_container {
        margin-top: 14px;
    }

</style>