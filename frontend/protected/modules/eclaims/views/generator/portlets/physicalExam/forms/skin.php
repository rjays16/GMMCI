<?php
// \CVarDumper::dump($selected_skin, 10, true);die;
// foreach ($peSkin as $key => $data){
?>
<input type="hidden" id="skinCount" value="<?php echo count($peSkin); ?>">
<h5>SKIN/EXTREMITIES <span style="color:red">*</span>:</h5>
<div class="span12" id="heent_div">
    <?php
    // if()
    echo CHtml::checkBoxList('Skin[skin_data]',
        $selected_skin['entries'],
        $peSkin,
        array(
            'class' => 'skin_name',
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
        <label id="label_remarks_skin">Others:</label>
        <?php
        echo $form->textAreaRow($selected_skin['for_remarks'], 'remarks', array(
            'class' => 'input-medium span7',
            'id' => 'remarks_skin',
            'disabled' => false,
            'placeholder' => 'Others ...',
            'htmlOptions' => array('class' => 'wew')
        ));
        ?>
    </div>
</div>

<script>
    var skinCount = $('#skinCount').val();
    $('.skin_name:eq(0)').on('click', function () {
        if ($(this).is(':checked')) {
            for (var i = 0; i < skinCount; i++) {
                if (i !== 0) {
                    $(".skin_name").eq(i).prop("checked", false);
                    $(".skin_name").eq(i).prop("disabled", true);
                }
            }
            $("#remarks_skin").prop("disabled", true);
            $('#remarks_skin').val("");
        } else {
            for (var i = 0; i < skinCount; i++) {
                $(".skin_name").eq(i).prop("disabled", false);
            }
            $("#remarks_skin").prop("disabled", false);
        }
    });

    $(document).ready(function () {
        if ($('.skin_name:eq(0)').is(':checked')) {
            for (var i = 0; i < skinCount; i++) {
                if (i !== 0) {
                    $(".skin_name").eq(i).prop("disabled", true);
                }
            }
            $("#remarks_skin").prop("disabled", true);
        }
    });

    $("#save-skin").on("click", function (e) {
        e.preventDefault();

        var skin = [];

        $.each($("input[name='Skin[name][]']:checked"), function () {
            skin.push($(this).val());
        });

        if (skin != "") {
            Swal.fire({
                title: 'Save SKIN/EXTREMITIES',
                text: "Are you sure do you want to save?",
                type: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Save it!'
            }).then((result) => {
                if (result.value) {
                    saveSkin(skin);
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

    function saveSkin(skin) {
        // alert(1);
        const $form = $("#pe-information");
        const url = $form.data('url-save-pe-skin');
        const encounter_nr = $form.data('encounter_nr');
        const pid = $form.data('pid');
        const skin_data = skin;
        const remarks = $("#remarks_skin").val();
        // alert(url);
        $.ajax({
            url: url,
            type: 'post',
            data: {
                encounter_nr: encounter_nr,
                pid: pid,
                skin_data: skin_data,
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

    #remarks_skin {
        width: 312px;
    }

    #span_skin {
        color: #bc2f2f;
    }

    #heent_container {
        margin-top: 14px;
    }

</style>