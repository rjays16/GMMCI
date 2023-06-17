<?php
// \CVarDumper::dump($selected_guie, 10, true);die;
// foreach ($peChest as $key => $data){
$guie_remarks = $selected_guie['for_remarks']->remarks;
?>
<input type="hidden" id="guieCount" value="<?php echo count($peGuie); ?>">
<h5>GU (IE) <span style="color:red">*</span>:</h5>
<div class="span12" id="heent_div">
    <?php
    // if()
    echo CHtml::checkBoxList('Guie[guie_data]',
        $selected_guie['entries'],
        $peGuie,
        array(
            'class' => 'guie_name',
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
        <label id="label_remarks_guie">Others:</label>
        <?php
        echo $form->textAreaRow($selected_guie['for_remarks'], 'remarks', array(
            'class' => 'input-medium span7',
            'id' => 'remarks_guie',
            'disabled' => false,
            'placeholder' => 'Others ...',
            'htmlOptions' => array('class' => 'wew')
        ));
        ?>
    </div>
</div>

<script>
    var guieCount = $('#guieCount').val();
    $('.guie_name:eq(0)').on('click', function () {
        if ($(this).is(':checked')) {
            for (var i = 0; i < guieCount; i++) {
                if (i !== 0) {
                    $(".guie_name").eq(i).prop("checked", false);
                    $(".guie_name").eq(i).prop("disabled", true);
                }
            }
            $("#remarks_guie").prop("disabled", true);
            $('#remarks_guie').val("");
        } else {
            for (var i = 0; i < guieCount; i++) {
                $(".guie_name").eq(i).prop("disabled", false);
            }
            $("#remarks_guie").prop("disabled", false);
        }
    });

    $(document).ready(function () {
        if ($('.guie_name:eq(0)').is(':checked')) {
            for (var i = 0; i < guieCount; i++) {
                if (i !== 0) {
                    $(".guie_name").eq(i).prop("disabled", true);
                }
            }
            $("#remarks_guie").prop("disabled", true);
        }
    });

    $("#save-guie").on("click", function (e) {
        e.preventDefault();

        var guie = [];

        $.each($("input[name='Guie[name][]']:checked"), function () {
            guie.push($(this).val());
        });

        if (guie != "") {
            Swal.fire({
                title: 'Save GU(IE)',
                text: "Are you sure do you want to save?",
                type: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Save it!'
            }).then((result) => {
                if (result.value) {
                    saveGuie(guie);
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

    function saveGuie(guie) {
        // alert(1);
        const $form = $("#pe-information");
        const url = $form.data('url-save-pe-guie');
        const encounter_nr = $form.data('encounter_nr');
        const pid = $form.data('pid');
        const guie_data = guie;
        const remarks = $("#remarks_guie").val();
        // alert(url);
        $.ajax({
            url: url,
            type: 'post',
            data: {
                encounter_nr: encounter_nr,
                pid: pid,
                guie_data: guie_data,
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

    #remarks_guie {
        width: 312px;
    }

    #span_guie {
        color: #bc2f2f;
    }

    #heent_container {
        margin-top: 14px;
    }

</style>