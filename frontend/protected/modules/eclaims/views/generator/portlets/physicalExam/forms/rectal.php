<?php
// \CVarDumper::dump($peAbdomen, 10, true);die;
// foreach ($peChest as $key => $data){
?>
<input type="hidden" id="rectalCount" value="<?php echo count($peRectal); ?>">
<h5>RECTAL:</h5>
<div class="span12" id="heent_div">
    <?php
    // if()
    echo CHtml::checkBoxList('Rectal[rectal_data]',
        $selected_rectal['entries'],
        $peRectal,
        array(
            'class' => 'rectal_name',
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
        <label id="label_remarks_rectal">Others:</label>
        <?php
        echo $form->textAreaRow($selected_rectal['for_remarks'], 'remarks', array(
            'class' => 'input-medium span7',
            'id' => 'remarks_rectal',
            'disabled' => false,
            'placeholder' => 'Others ...',
            'htmlOptions' => array('class' => 'wew')
        ));
        ?>
    </div>
</div>

<script>
    var rectalCount = $('#rectalCount').val();
    $('.rectal_name:eq(0)').on('click', function () {
        if ($(this).is(':checked')) {
            for (var i = 0; i < rectalCount; i++) {
                if (i !== 0) {
                    $(".rectal_name").eq(i).prop("checked", false);
                    $(".rectal_name").eq(i).prop("disabled", true);
                }
            }
            $("#remarks_rectal").prop("disabled", true);
            $('#remarks_rectal').val("");
        } else {
            for (var i = 0; i < rectalCount; i++) {
                $(".rectal_name").eq(i).prop("disabled", false);
            }
            $("#remarks_rectal").prop("disabled", false);
        }
    });

    $(document).ready(function () {
        if ($('.rectal_name:eq(0)').is(':checked')) {
            for (var i = 0; i < rectalCount; i++) {
                if (i !== 0) {
                    $(".rectal_name").eq(i).prop("disabled", true);
                }
            }
            $("#remarks_rectal").prop("disabled", true);
        }
    });

    $("#save-rectal").on("click", function (e) {
        e.preventDefault();

        var rectal = [];

        $.each($("input[name='Rectal[name][]']:checked"), function () {
            rectal.push($(this).val());
        });

        if (rectal != "") {
            Swal.fire({
                title: 'Save RECTAL',
                text: "Are you sure do you want to save?",
                type: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Save it!'
            }).then((result) => {
                if (result.value) {
                    saveRectal(rectal);
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

    function saveRectal(rectal) {
        // alert(1);
        const $form = $("#pe-information");
        const url = $form.data('url-save-pe-rectal');
        const encounter_nr = $form.data('encounter_nr');
        const pid = $form.data('pid');
        const rectal_data = rectal;
        const remarks = $("#remarks_rectal").val();
        // alert(url);
        $.ajax({
            url: url,
            type: 'post',
            data: {
                encounter_nr: encounter_nr,
                pid: pid,
                rectal_data: rectal_data,
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

    #remarks_rectal {
        width: 312px;
    }

    #span_rectal {
        color: #bc2f2f;
    }

    #heent_container {
        margin-top: 14px;
    }

</style>