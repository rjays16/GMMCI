
<?php 
// \CVarDumper::dump($getSelectedSignsAndSymptoms['for_pains'], 10, true);
// \CVarDumper::dump($getSelectedSignsAndSymptoms, 10, true);die;
?>

<div class="span3" id="ss_one_div">
    <?php 
        // if()
        echo CHtml::checkBoxList('signs_one[name][]', 
            $getSelectedSignsAndSymptoms['entries'], 
            $getSignsAndSymptomsOne,
            array(
                'class' => 'signs_name',
                    'labelOptions'=>array(
                        'style'=> 'display:inline;',
                        ),
                'style' => 'margin-top: -3px;'
                )    
        );
    ?>

</div>

<div class="span3" id="ss_one_div">
    <?php 
        // if()
        echo CHtml::checkBoxList('signs_two[name][]', 
            $getSelectedSignsAndSymptoms['entries'], 
            $getSignsAndSymptomsTwo,
            array(
                'class' => 'signs_name',
                    'labelOptions'=>array(
                        'style'=> 'display:inline;',
                        ),
                'style' => 'margin-top: -3px;'
                )    
        );
    ?>

</div>

<div class="span3" id="ss_one_div">
    <?php 
        // if()
        echo CHtml::checkBoxList('signs_three[name][]', 
            $getSelectedSignsAndSymptoms['entries'], 
            $getSignsAndSymptomsThree,
            array(
                'class' => 'signs_name',
                    'labelOptions'=>array(
                        'style'=> 'display:inline;',
                        ),
                'style' => 'margin-top: -3px;'
                )    
        );
    ?>

</div>

<div class="span3" id="ss_one_div">
    <?php 
        // if()
        echo CHtml::checkBoxList('signs_four[name][]', 
            $getSelectedSignsAndSymptoms['entries'], 
            $getSignsAndSymptomsFour,
            array(
                'class' => 'signs_name',
                    'labelOptions'=>array(
                        'style'=> 'display:inline;',
                        ),
                'style' => 'margin-top: -3px;'
                )    
        );
    ?>
</div>

<div class="row-fluid" id='pain_container'>
    <div class="span5" id="text_field_div">
        <label id="label_pain_remarks" class="hidden">Pains <span style="color:red">*</span>:</label>

        <?php 
            echo $form->textAreaRow($getSelectedSignsAndSymptoms['for_pains'], 'pains', array(
                'class' => 'input-medium span7 hidden',
                'id' => 'pain_remarks',
                'placeholder' => 'Remarks ...',
                'htmlOptions' => array( 'class' => 'wew')
            ));
         ?>

        <br>&nbsp;
        <span id="span_pain_remarks" class="hidden"><i>Please fill-out this area if you check 'Pains'</i></span>
    </div>

    <div class="span5" id="text_field_div">
        <label id="label_other_remarks" class="hidden">Others <span style="color:red">*</span>:</label>

        <?php 
            echo $form->textAreaRow($getSelectedSignsAndSymptoms['for_others'], 'others', array(
                'class' => 'input-medium span7 hidden',
                'id' => 'other_remarks',
                'placeholder' => 'Remarks ...',
                'htmlOptions' => array( 'class' => 'wew')
            ));
         ?>

        <br>&nbsp;
        <span id="span_other_remarks" class="hidden"><i>Please fill-out this area if you check 'Others'</i></span>
    </div>
</div>



<div class="row-fluid">
    <div class="span12">
        <legend class="legend"></legend>
        <?php
        $this->widget(
            'bootstrap.widgets.TbButton',
            array(
                'buttonType' => 'submit',
                'type' => 'primary',
                'icon' => 'fa fa-save',
                'label' => 'Save',
                'htmlOptions' => array(
                    'id' => 'save-signs',
                    'class' => 'pull-right'
                ),
            )
        );
        ?>

    </div>
</div>
<?php 
    // }
?>

<script type="text/javascript">

    $('.signs_name:eq(34)').on('click', function(){
        if($(this).is(':checked')){
            $('#pain_remarks').removeClass('hidden');
            $('#span_pain_remarks').removeClass('hidden');
            $("#label_pain_remarks").removeClass('hidden');
            $("#pain_remarks").val('');
        }
        else{
            $('#pain_remarks').addClass('hidden');
            $('#span_pain_remarks').addClass('hidden');
            $("#label_pain_remarks").addClass('hidden');
        }
    });

    $('.signs_name:eq(35)').on('click', function(){
        if($(this).is(':checked')){
            $('#span_other_remarks').removeClass('hidden');
            $('#label_other_remarks').removeClass('hidden');
            $("#other_remarks").removeClass('hidden');
            $("#other_remarks").val('');
        }
        else{
            $('#span_other_remarks').addClass('hidden');
            $('#label_other_remarks').addClass('hidden');
            $("#other_remarks").addClass('hidden');
        }
    });

    $(document).ready(function (){
        if($('.signs_name:eq(35)').is(':checked')){
            $('#span_other_remarks').removeClass('hidden');
            $('#label_other_remarks').removeClass('hidden');
            $("#other_remarks").removeClass('hidden');
        }
    });

    $(document).ready(function (){
        if($('.signs_name:eq(34)').is(':checked')){
            $('#pain_remarks').removeClass('hidden');
            $('#span_pain_remarks').removeClass('hidden');
            $("#label_pain_remarks").removeClass('hidden');
        }
    });

    $("#save-signs").on("click", function(e) {
        e.preventDefault();

            var ss1 = [];
            var ss2 = [];
            var ss3 = [];
            var ss4 = [];

            $.each($("input[name='signs_one[name][]']:checked"), function(){ 
                    ss1.push($(this).val());
            });

            $.each($("input[name='signs_two[name][]']:checked"), function(){ 
                    ss2.push($(this).val());
            });

            $.each($("input[name='signs_three[name][]']:checked"), function(){ 
                    ss3.push($(this).val());
            });

            $.each($("input[name='signs_four[name][]']:checked"), function(){ 
                    ss4.push($(this).val());
            });

            var merge1 = $.merge(ss1,ss2);
            var merge2 = $.merge(ss3,ss4);
            var final_data = $.merge(merge1,merge2);

            var check_others = final_data.includes("X");
            var remark_val = $("#other_remarks").val();

            var check_pains = final_data.includes("38");
            var pain_val = $("#pain_remarks").val();

            var status;
            var message;

            if ( (check_others == true && remark_val != "") && (check_pains == true && pain_val != "") ){
                status = true;
            }else if (check_others == false && check_pains == false){
                status = true;
            }else if ( (check_others == true && remark_val == "") || (check_pains == true && pain_val == "") ){
                message = 'Please dont leave the required filed blank!';
                status = false;
            }else if (check_pains == true && pain_val != ""){
                status = true;
            }else if (check_others == true && remark_val != ""){
                status = true;
            }else{
                message = 'Other remarks and Pains remarks is required!'
                status = false;
            }


            if(status){
                if (final_data != ""){
                    Swal.fire({
                      title: 'Save Pertinent Signs and Symptoms',
                      text: "Are you sure do you want to save?",
                      type: 'info',
                      showCancelButton: true,
                      confirmButtonColor: '#3085d6',
                      cancelButtonColor: '#d33',
                      confirmButtonText: 'Yes, Save it!'
                    }).then((result) => {
                      if (result.value) {
                        saveSigns(final_data);
                      }else{
                        return false;
                      }
                    })
                }else{
                    Swal.fire(
                      'Failed!',
                      'Please Select atleast One(1) Sign & Symptoms after you save!',
                      'error'
                    )
                }
            }else{
                Swal.fire(
                  'Failed!',
                  message,
                  'error'
                )
            }

    });

    function saveSigns(final_data) {
        const $form = $("#signs-symptoms-information");
        const url = $form.data('url-save-signs');
        const encounter_nr = $form.data('encounter_nr');
        const pid = $form.data('pid');
        const signs_data = final_data;
        const pains =  $("#pain_remarks").val();
        const others =  $("#other_remarks").val();
        $.ajax({
            url: url,
            type: 'post',
            data: {
                encounter_nr: encounter_nr,
                pid: pid,
                signs_data: signs_data,
                pains: pains,
                others: others,
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
                      response.message,
                      'success'
                    )
                } else if (reponse == 'no data') {
                     Swal.fire(
                      'Saved!',
                      'The data has been saved!',
                      'success'
                    )
                }else{
                     Swal.fire(
                      response.message,
                      'Something went wrong!',
                      'error'
                    )
                }
            },
        });

    }
</script>

<style type="text/css">
    .legend{
        margin-top: 40px;
    }
    #pain_remarks{
        width: 300px;
    }
    #span_pain_remarks{
        color:#bc2f2f;
    }
    #other_remarks{
        width: 300px;
    }
    #span_other_remarks{
        color:#bc2f2f;
    }
</style>