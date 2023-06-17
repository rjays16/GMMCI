<?php
// \CVarDumper::dump($peAbdomen, 10, true);die;
// foreach ($peChest as $key => $data){
?>
<input type="hidden" id="abdomenCount" value="<?php echo count($peAbdomen); ?>">
<h5>ABDOMEN <span style="color:red">*</span>:</h5>
<div class="span12" id="heent_div">
    <?php
    // if()
    echo CHtml::checkBoxList('Abdomen[abdomen_data]',
        $selected_abdomen['entries'],
        $peAbdomen,
        array(
            'class' => 'abdomen_name',
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
        <label id="label_remarks_abdomen">Others:</label>
        <?php
        echo $form->textAreaRow($selected_abdomen['for_remarks'], 'remarks', array(
            'class' => 'input-medium span7',
            'id' => 'remarks_abdomen',
            'disabled' => false,
            'placeholder' => 'Others ...',
            'htmlOptions' => array('class' => 'wew')
        ));
        ?>
    </div>
</div>

<script>
    var abdomenCount = $('#abdomenCount').val();
    $('.abdomen_name:eq(0)').on('click', function () {
        if ($(this).is(':checked')) {
            for (var i = 0; i < abdomenCount; i++) {
                if (i !== 0) {
                    $(".abdomen_name").eq(i).prop("checked", false);
                    $(".abdomen_name").eq(i).prop("disabled", true);
                }
            }
            $("#remarks_abdomen").prop("disabled", true);
            $('#remarks_abdomen').val("");
        } else {
            for (var i = 0; i < abdomenCount; i++) {
                $(".abdomen_name").eq(i).prop("disabled", false);
            }
            $("#remarks_abdomen").prop("disabled", false);
        }
    });

    $(document).ready(function () {
        if ($('.abdomen_name:eq(0)').is(':checked')) {
            for (var i = 0; i < abdomenCount; i++) {
                if (i !== 0) {
                    $(".abdomen_name").eq(i).prop("disabled", true);
                }
            }
            $("#remarks_abdomen").prop("disabled", true);
        }
    });

    $("#save-abdomen").on("click", function (e) {
        e.preventDefault();

        var abdomen = [];

        $.each($("input[name='Abdomen[name][]']:checked"), function () {
            abdomen.push($(this).val());
        });

        if (abdomen != "") {
            Swal.fire({
                title: 'Save ABDOMEN',
                text: "Are you sure do you want to save?",
                type: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Save it!'
            }).then((result) => {
                if (result.value) {
                    saveAbdomen(abdomen);
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

    function saveAbdomen(abdomen) {
        // alert(1);
        const $form = $("#pe-information");
        const url = $form.data('url-save-pe-abdomen');
        const encounter_nr = $form.data('encounter_nr');
        const pid = $form.data('pid');
        const abdomen_data = abdomen;
        const remarks = $("#remarks_abdomen").val();
        // alert(heent_data);
        $.ajax({
            url: url,
            type: 'post',
            data: {
                encounter_nr: encounter_nr,
                pid: pid,
                abdomen_data: abdomen_data,
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

    #remarks_abdomen {
        width: 312px;
    }

    #span_abdomen {
        color: #bc2f2f;
    }

    #heent_container {
        margin-top: 14px;
    }

</style>