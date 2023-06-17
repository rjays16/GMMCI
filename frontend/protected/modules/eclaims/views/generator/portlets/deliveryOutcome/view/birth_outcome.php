<div class="row-fluid">
    <div class="span6">
        <?php

        echo $form->TextFieldRow($birthoutcome, 'fetal_outcome', array(
            'class' => 'input-medium span7',
            'id' => 'fetal_outcome',
        ));
        ?>
    </div>
    <div class="span6">
        <label for="is_normal">Sex</label>
        <?php
        $list = CHtml::listData($genderList, 'value', 'details');
        echo $form->dropDownList(
            $birthoutcome,
            'sex',
            $list,
            array(
                'class' => 'form-control col-md-12',
                'id' => 'sex',
                'required' => true,
                'options' => array(
                    $birthoutcome->sex != '' ? $birthoutcome->sex : ''  => array('selected' => true)
                ),

            ),
            array(
                'htmlOptions' => array(
                    'required' => true,
                    // 'name' => 'PatientPreassessment[patient_type]'
                ),

            )
        );
        ?>
    </div>

</div>
<div class="row-fluid">
    <div class="span6">
        <?php

        echo $form->NumberFieldRow($birthoutcome, 'birth_weight', array(
            'class' => 'input-medium span7',
            'id' => 'birth_weight',
        ));
        ?>
    </div>
    <div class="span6">
        <?php

        echo $form->TextFieldRow($birthoutcome, 'apgar_score', array(
            'class' => 'input-medium span7',
            'id' => 'apgar_score',

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
                    'id' => 'save-birth-outcome',
                    'class' => 'pull-right'
                ),
            )
        );
        ?>

    </div>
</div>
<hr>
<?php
// $columns = array(
//     array(
//         'name'   => 'fetal_outcome',
//         'header' => 'Fetal Outcome',
//     ),
//     array(
//         'name'   => 'sex',
//         'header' => 'Sex',
//         'htmlOptions' => array(),
//         // 'style' => 'text-align:center;width:5%;',


//         // 'type'   => 'datetime',
//         'value'  => function ($data) {
//             $gender =  $data['sex'] == 'M' ? 'Male' : 'Female';
//             return $gender;
//         },
//     ),
//     array(
//         'name' => 'birth_weight',
//         'header' => 'Birth Weight',
//     ),

//     array(
//         'name' => 'apgar_score',
//         'header' => 'APGAR Score',
//     ),






//     array(
//         'header'      => 'Action',
//         'class'       => 'CButtonColumn',
//         'htmlOptions' => array(
//             // 'style' => 'text-align:center;width:5%;',
//         ),
//         'buttons'     => array(
//             'Delete' => array(
//                 'label'   => 'Delete',
//                 'icon'    => ' fa fa-trash',
//                 'options' => array(
//                     'id'       => 'delete_birth_outcome',
//                     'class'    => 'btn btn-danger delete_birth_outcome',
//                 ),
//             ),
//         ),
//         'template'    => '{Delete}',
//     ),
// );
// $this->widget('bootstrap.widgets.TbGridView', array(
//     'id'                    => 'birth-outcome-view',
//     'type'                  => array('condensed', 'bordered', 'striped', 'hover'),
//     'columns'               => $columns,
//     'filter'                => null,
//     'dataProvider'          => $birthoutcomes,
//     // 'rowCssClassExpression' => function ($row, $data) {
//     //     $ts = strtotime($data['encounter_no']);
//     //     if ($ts && date('Ymd', $ts) == date('Ymd')) {
//     //         return 'success bold';
//     //     }
//     // },
//     'rowHtmlOptionsExpression' => function ($row, $data) {
//         return array(
//             'data-id-prenatal-consultation' => $data['id']
//         );
//     },
//     'template'              => '<div class="margin-bottom-10">{items}</div>
//         {summary}
//         {pager}',
// ));
?>
<script>
    $("#save-birth-outcome").on("click", function(e) {
        e.preventDefault();
        // if (confirm('Do you really want to save?')) {
        //     saveBirthOutcome();
        // } else {
        //     return false;
        // }

        const fetal_outcome = $("#fetal_outcome").val();
        if (fetal_outcome == "") {
            Swal.fire(
                'Fetal Outcome is required!',
                'Please dont leave it blank!',
                'error'
            )

            return false;
        }
        Swal.fire({
            title: 'Save Birth Outcome',
            text: "Are you sure do you want to save?",
            type: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Save it!'
        }).then((result) => {
            if (result.value) {
                saveBirthOutcome();
            } else {
                return false;
            }
        })
    });

    function saveBirthOutcome() {
        // alert(1);
        const $form = $("#dt-delivery-outcome");
        const url = $form.data('url-save-birth-outcome');
        const encounter_nr = $form.data('encounter_nr');
        const pid = $form.data('pid');
        const fetal_outcome = $("#fetal_outcome").val();
        const sex = $("#sex").val();
        const birth_weight = $("#birth_weight").val();
        const apgar_score = $("#apgar_score").val();


        $.ajax({
            url: url,
            type: 'post',
            data: {
                pid: pid,
                encounter_nr: encounter_nr,
                fetal_outcome: fetal_outcome,
                sex: sex,
                birth_weight: birth_weight,
                apgar_score: apgar_score,

            },
            dataType: 'json',
            beforeSend: () => {
                // toastr.info('Updating changes, please wait...');
                Alerts.loading({
                    content: 'Adding data. Please wait...'
                });
            },
            success: (response) => {
                Alerts.close();
                if (response['status']) {
                    // swal("Info!", "Can`t Delete, Already Discharged!", "info");
                    $.fn.yiiGridView.update("birth-outcome-view");
                    Swal.fire(
                        'Saved!',
                        response['message'],
                        'success'
                    )
                    // alert(response['message']);
                } else {
                    Swal.fire(
                        'Failed to save!',
                        response['message'],
                        'error'
                    )
                    // toastr.success('Past Medical History updated');
                    // alert(response['message']);
                }
            },

            error: () => {
                Swal.fire(
                    'Please contact your administrato!',
                    'Something went wrong!',
                    'error'
                )
                // alert('Something went wrong, Please contact your administrator');
                // toastr.error('Something went wrong, Please contact your administrator');
            }

        });
        // alert(url)
    }

    function deleteBirthOutcome(id) {

        const $form = $("#dt-delivery-outcome");
        const url = $form.data('url-delete-birth-outcome');
        const encounter_nr = $form.data('encounter_nr');
        const pid = $form.data('pid');
        const birth_outcome_id = id;


        $.ajax({
            url: url,
            type: 'post',
            data: {
                pid: pid,
                encounter_nr: encounter_nr,
                birth_outcome_id: birth_outcome_id,
            },
            dataType: 'json',
            beforeSend: () => {
                // toastr.info('Updating changes, please wait...');
                Alerts.loading({
                    content: 'Deleting data. Please wait...'
                });
            },
            success: (response) => {
                Alerts.close();
                if (response['status']) {
                    // swal("Info!", "Can`t Delete, Already Discharged!", "info");
                    $.fn.yiiGridView.update("birth-outcome-view");
                    Swal.fire(
                        'Deleted!',
                        response['message'],
                        'success'
                    )
                    // alert(response['message']);
                } else {
                    // toastr.success('Past Medical History updated');
                    // alert(response['message']);
                    Swal.fire(
                        'Failed to delete!',
                        response['message'],
                        'error'
                    )
                }
            },

            error: () => {
                Swal.fire(
                    'Please contact your administrato!',
                    'Something went wrong!',
                    'error'
                )
                // alert('Something went wrong, Please contact your administrator');
                // toastr.error('Something went wrong, Please contact your administrator');
            }

        });
        // alert(url)
    }
</script>

<script>
    $(document).ready(function() {
        jQuery(function($) {
            var deleteBirthOutcomes = $('.delete_birth_outcome');
            deleteBirthOutcomes.livequery(function(e) {
                $(this).click(function(e) {
                    e.preventDefault();
                    // var $this = $(this);

                    var $this = $(this).parents('tr');
                    var id = $this.data('id-prenatal-consultation');


                    Swal.fire({
                        title: 'Delete Birth Outcome',
                        text: "Are you sure do you want to delete?",
                        type: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, Delete it!'
                    }).then((result) => {
                        if (result.value) {
                            deleteBirthOutcome(id);
                        } else {
                            return false;
                        }
                    })
                    // alert(id)
                    // if (confirm('Do you really want to delete?')) {
                    //     deleteBirthOutcome(id);
                    // } else {
                    //     return false;
                    // }
                });
            });
        });
    });
</script>