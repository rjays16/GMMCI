<?php
// \CVarDumper::dump($peCvs, 10, true);die;
// foreach ($peChest as $key => $data){
?>
<input type="hidden" id="heartCount" value="<?php echo count($peCvs); ?>">
<h5>CVS(Heart) <span style="color:red">*</span>:</h5>
<div class="span12" id="heent_div">
    <?php
    // if()
    echo CHtml::checkBoxList('Cvs[heart_data]',
        $selected_cvs['entries'],
        $peCvs,
        array(
            'class' => 'cvs_name',
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
        <label id="label_remarks_cvs">Others:</label>
        <?php
        echo $form->textAreaRow($selected_cvs['for_remarks'], 'remarks', array(
            'class' => 'input-medium span7',
            'id' => 'remarks_cvs',
            'disabled' => false,
            'placeholder' => 'Others ...',
            'htmlOptions' => array('class' => 'wew')
        ));
        ?>
    </div>
</div>

<script>
    var heartCount = $('#heartCount').val();
    $('.cvs_name:eq(0)').on('click', function () {
        if ($(this).is(':checked')) {
            for (var i = 0; i < heartCount; i++) {
                if (i !== 0) {
                    $(".cvs_name").eq(i).prop("checked", false);
                    $(".cvs_name").eq(i).prop("disabled", true);
                }
            }
            $("#remarks_cvs").prop("disabled", true);
            $('#remarks_cvs').val("");
        } else {
            for (var i = 0; i < heartCount; i++) {
                $(".cvs_name").eq(i).prop("disabled", false);
            }
            $("#remarks_cvs").prop("disabled", false);
        }
    });

    $(document).ready(function () {
        if ($('.cvs_name:eq(0)').is(':checked')) {
            for (var i = 0; i < heartCount; i++) {
                if (i !== 0) {
                    $(".cvs_name").eq(i).prop("disabled", true);
                }
            }
            $("#remarks_cvs").prop("disabled", true);
        }
    });

    $("#save-cvs").on("click", function (e) {
        e.preventDefault();

        var cvs = [];

        $.each($("input[name='Cvs[name][]']:checked"), function () {
            cvs.push($(this).val());
        });

        if (cvs != "") {
            Swal.fire({
                title: 'Save CVS(HEART)',
                text: "Are you sure do you want to save?",
                type: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Save it!'
            }).then((result) => {
                if (result.value) {
                    saveCvs(cvs);
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

    function saveCvs(cvs) {
        // alert(1);
        const $form = $("#pe-information");
        const url = $form.data('url-save-pe-cvs');
        const encounter_nr = $form.data('encounter_nr');
        const pid = $form.data('pid');
        const heart_data = cvs;
        const remarks = $("#remarks_cvs").val();
        // alert(heent_data);
        $.ajax({
            url: url,
            type: 'post',
            data: {
                form_data,
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

    #remarks_cvs {
        width: 312px;
    }

    #span_cvs {
        color: #bc2f2f;
    }

    #heent_container {
        margin-top: 14px;
    }

</style>