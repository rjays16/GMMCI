<?php
//Yii::app()->clientScript->registerScript('items_update', "$('#id-dropdown').change(function(){
//        alert('ok'); //this works
//        $.fn.yiiGridView.update('jurnal-grid', {
//                type:'GET',
//                data: $(this).serialize(),
//                success=>
//                   js:function() { $.fn.yiiGridView.update('course_ward_list');}
//                }
//            }
//        );
//    });
//    return false;",
//    CClientScript::POS_READY);
//?>

<div class="row-fluid">
    <div class="span4">

        <?php
        echo $form->dateFieldRow(Cf4CourseInTheWard::model(), 'date_action', array(
            'class' => 'input-medium span12',
            'id' => 'date_action',
        ));
        ?>

    </div>
    <div class="span8">

        <?php
        echo $form->textAreaRow(Cf4CourseInTheWard::model(), 'doctor_action', array(
            'class' => 'input-medium span12',
            'id' => 'doctor_action',
            'placeholder' => 'Doctor\'s action/order'
        ));
        ?>

    </div>
</div>

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
                    'id' => 'add-course-ward',
                    'class' => 'pull-right'
                ),
            )
        );
        ?>

    </div>
</div>
<hr>

<script>
    $("#add-course-ward").on("click", function (e) {
        e.preventDefault();

        Swal.fire({
            title: 'Save Changes',
            text: "Are you sure do you want to save?",
            type: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Save it!'
        }).then((result) => {
            if (result.value) {
                saveCourseWard();
            } else {
                return false;
            }
        });
    });

    function saveCourseWard() {
        let $form = $("#course-ward-form");
        let url = $form.data('url');
        let encounter_nr = $form.data('encounter_nr');
        let pid = $form.data('pid');
        let $date_action = $("#date_action");
        let $doctor_action = $("#doctor_action");
        $.ajax({
            url: url,
            type: 'post',
            data: {
                encounter_nr: encounter_nr,
                pid: pid,
                date_action: $date_action.val(),
                doctor_action: $doctor_action.val(),
            },
            dataType: 'json',
            beforeSend: () => {
            },
            success: (response) => {
                if (response.status) {
                    Swal.fire({
                        title: 'The data has been saved!',
                        type: 'success',
                        showConfirmButton: false,
                        timer: 1500
                    });

                    $date_action.val("");
                    $doctor_action.val("");
                } else {
                    Swal.fire({
                        title: response.message,
                        type: 'error',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
                $.fn.yiiGridView.update("course_ward_list");
            },
            error: (response) => {
                Swal.fire({
                    title: 'Something went wrong, Please contact your administrator',
                    type: 'error',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    }
</script>
