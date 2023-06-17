<?php
// \CVarDumper::dump($selected_chest, 10, true);die;
// foreach ($peChest as $key => $data){
?>
<input type="hidden" id="chestCount" value="<?php echo count($peChest); ?>">
<h5>CHEST/LUNGS <span style="color:red">*</span>:</h5>
<div class="span12" id="heent_div">
    <?php
    // if()
    echo CHtml::checkBoxList('Chest[chest_data]',
        $selected_chest['entries'],
        $peChest,
        array(
            'class' => 'chest_name',
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
        <label id="label_remarks_chest">Others:</label>
        <?php
        echo $form->textAreaRow($selected_chest['for_remarks'], 'remarks', array(
            'class' => 'input-medium span7',
            'id' => 'remarks_chest',
            'disabled' => false,
            'placeholder' => 'Others ...',
            'htmlOptions' => array('class' => 'wew')
        ));
        ?>
    </div>
</div>

<script>
    var chestCount = $('#chestCount').val();
    $('.chest_name:eq(0)').on('click', function () {
        if ($(this).is(':checked')) {
            for (var i = 0; i < chestCount; i++) {
                if (i !== 0) {
                    $(".chest_name").eq(i).prop("checked", false);
                    $(".chest_name").eq(i).prop("disabled", true);
                }
            }
            $("#remarks_chest").prop("disabled", true);
            $('#remarks_chest').val("");
        } else {
            for (var i = 0; i < chestCount; i++) {
                $(".chest_name").eq(i).prop("disabled", false);
            }
            $("#remarks_chest").prop("disabled", false);
        }
    });

    $(document).ready(function () {
        if ($('.chest_name:eq(0)').is(':checked')) {
            for (var i = 0; i < chestCount; i++) {
                if (i !== 0) {
                    $(".chest_name").eq(i).prop("disabled", true);
                }
            }
            $("#remarks_chest").prop("disabled", true);
        }
    });

    $("#save-chest").on("click", function (e) {
        e.preventDefault();

        var chest = [];

        $.each($("input[name='Chest[name][]']:checked"), function () {
            chest.push($(this).val());
        });

        if (chest != "") {
            Swal.fire({
                title: 'Save CHEST/LUNGS',
                text: "Are you sure do you want to save?",
                type: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Save it!'
            }).then((result) => {
                if (result.value) {
                    saveChest(chest);
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

    function saveChest(chest) {
        // alert(1);
        const $form = $("#pe-information");
        const url = $form.data('url-save-pe-chest');
        const encounter_nr = $form.data('encounter_nr');
        const pid = $form.data('pid');
        const chest_data = chest;
        const remarks = $("#remarks_chest").val();
        // alert(heent_data);
        $.ajax({
            url: url,
            type: 'post',
            data: {
                encounter_nr: encounter_nr,
                pid: pid,
                chest_data: chest_data,
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

    #remarks_chest {
        width: 312px;
    }

    #span_chest {
        color: #bc2f2f;
    }

    #heent_container {
        margin-top: 14px;
    }

</style>