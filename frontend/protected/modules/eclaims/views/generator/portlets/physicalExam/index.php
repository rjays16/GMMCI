<?php 
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id' => 'pe-information',
    'type' => \TbActiveForm::TYPE_VERTICAL,
    'htmlOptions' => array(
        'data-url-save-pe' =>  $this->createUrl('physicalExamination/saveGenSurvey'),
        // 'data-encounter_nr' =>  $encounter->encounter_nr,
        // 'data-pid' =>  $model['person']->pid,
    ),

));
?>
<input type="hidden" name="encounter_nr" value="<?php echo $encounter->encounter_nr ?>">
<input type="hidden" name="pid" value="<?php echo $model['person']->pid ?>">
<div class="row-fluid">
    <div class="span4">
        <?php
            $this->renderPartial('portlets/physicalExam/forms/general', array(
                    'encounter' => $encounter,
                    'peGen_survey' => $peGen_survey,
                    'selected_gensurvey' => $selected_gensurvey,
                    'form' => $form,
                )
            );
        ?>
    </div>
</div>
<hr>
<div class="row-fluid">
    <div class="span3">
        <?php
            $this->renderPartial('portlets/physicalExam/forms/heent', array(
                    'encounter' => $encounter,
                    'peHeent' => $peHeent,
                    'selected_heent' => $selected_heent,
                    'form' => $form,
                )
            );
        ?>
    </div>
    <div class="span3">
        <?php
            $this->renderPartial('portlets/physicalExam/forms/skin', array(
                    'encounter' => $encounter,
                    'peSkin' => $peSkin,
                    'selected_skin' => $selected_skin,
                    'form' => $form,
                )
            );
        ?>
    </div>
    <div class="span3">
        <?php
            $this->renderPartial('portlets/physicalExam/forms/chest_lungs', array(
                    'encounter' => $encounter,
                    'peChest' => $peChest,
                    'selected_chest' => $selected_chest,
                    'form' => $form,
                )
            );
        ?>
    </div>
    <div class="span3">
        <?php
            $this->renderPartial('portlets/physicalExam/forms/cvs', array(
                    'encounter' => $encounter,
                    'peCvs' => $peCvs,
                    'selected_cvs' => $selected_cvs,
                    'form' => $form,
                )
            );
        ?>
    </div>
</div>
<hr>
<div class="row-fluid">
    <div class="span3">
        <?php
            $this->renderPartial('portlets/physicalExam/forms/abdomen', array(
                    'encounter' => $encounter,
                    'peAbdomen' => $peAbdomen,
                    'selected_abdomen' => $selected_abdomen,
                    'form' => $form,
                )
            );
        ?>
    </div>
    <div class="span3">
        <?php
            $this->renderPartial('portlets/physicalExam/forms/neuro', array(
                    'encounter' => $encounter,
                    'peNeuro' => $peNeuro,
                    'selected_neuro' => $selected_neuro,
                    'form' => $form,
                )
            );
        ?>
    </div>
    <div class="span3">
        <?php
            $this->renderPartial('portlets/physicalExam/forms/rectal', array(
                    'encounter' => $encounter,
                    'peRectal' => $peRectal,
                    'selected_rectal' => $selected_rectal,
                    'form' => $form,
                )
            );
        ?>
    </div>
    <div class="span3">
        <?php
            $this->renderPartial('portlets/physicalExam/forms/guie', array(
                    'encounter' => $encounter,
                    'peGuie' => $peGuie,
                    'selected_guie' => $selected_guie,
                    'form' => $form,
                )
            );
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
                    'id' => 'save-pe',
                    'class' => 'pull-right'
                ),
            )
        );
        ?>

    </div>
</div>
<?php
    $this->endWidget();
?>

<script>
    $("#pe-information").on("submit", function (e) {
        e.preventDefault();
        var $this = $(this);
        var url = $(this).data('url-save-pe');
        var form_data = $(this).serialize();

        var gen = [];
        var heent = [];
        var skin = [];
        var chest = [];
        var cvs = [];
        var abdomen = [];
        var neuro = [];
        var rectal = [];
        var guie = [];

        $.each($("input[name='Gen[gen_data][]']:checked"), function(){ 
                gen.push($(this).val());
        });
        $.each($("input[name='Heent[heent_data][]']:checked"), function(){ 
                heent.push($(this).val());
        });
        $.each($("input[name='Skin[skin_data][]']:checked"), function(){ 
                skin.push($(this).val());
        });
        $.each($("input[name='Chest[chest_data][]']:checked"), function(){ 
                chest.push($(this).val());
        });
        $.each($("input[name='Cvs[heart_data][]']:checked"), function(){ 
                cvs.push($(this).val());
        });
        $.each($("input[name='Abdomen[abdomen_data][]']:checked"), function(){ 
                abdomen.push($(this).val());
        });
        $.each($("input[name='Neuro[neuro_data][]']:checked"), function(){ 
                neuro.push($(this).val());
        });
        $.each($("input[name='Guie[guie_data][]']:checked"), function(){ 
                guie.push($(this).val());
        });

        if($("#remarks").val() != ''){
            heent.push('99');
        }
        if($("#remarks_neuro").val() != ''){
            neuro.push('99');
        }
        if($("#remarks_rectal").val() != ''){
            rectal.push('99');
        }
        if($("#remarks_skin").val() != ''){
            skin.push('99');
        }
        if($("#remarks_guie").val() != ''){
            guie.push('99');
        }
        if($("#remarks_cvs").val() != ''){
            cvs.push('99');
        }
        if($("#remarks_chest").val() != ''){
            chest.push('99');
        }
        if($("#remarks_abdomen").val() != ''){
            abdomen.push('99');
        }

        var check_others = gen.includes("2");
        var remark_val = $("#remarks_gen").val();

        if(check_others == true && remark_val != ""){
            if(gen != "" && cvs != "" && heent != "" && skin != "" && chest != "" && cvs != "" && abdomen != "" && neuro != "" && guie != ""){
                callSwal(form_data, url);
            }else{
                Swal.fire(
                  'Failed to save!',
                  'Please complete all the inputs!',
                  'error'
                )
            }
        }else if(check_others == false ){
            if(gen != "" && cvs != "" && heent != "" && skin != "" && chest != "" && cvs != "" && abdomen != "" && neuro != "" && guie != ""){
                callSwal(form_data, url);
            }else{
                Swal.fire(
                  'Failed to save!',
                  'Please complete all the inputs!',
                  'error'
                )
            }
        }else{
            Swal.fire(
              'Failed!',
              'Altered Sensorium remarks field is required!',
              'error'
            )
        }
    });

    function callSwal(form_data, url) {
        Swal.fire({
          title: 'Save PHYSICAL EXAMINATION',
          text: "Are you sure do you want to save?",
          type: 'info',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, Save it!'
        }).then((result) => {
          if (result.value) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: form_data,
                    dataType: 'JSON',
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
                        } else if (response == 'no data') {
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
          }else{
            return false;
          }
        })
    }
</script>