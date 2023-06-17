<div class="row-fluid">
    <div class="span6">
        <label for="is_normal">Prenatal Consultation No.</label>
        <?php
        $list = CHtml::listData($prenatalConsultaionNoList, 'value', 'details');
        echo $form->dropDownList(
            $prenatalVisit,
            'prenatal_consultation_no',
            $list,
            array(
                'class' => 'form-control col-md-12',
                'id' => 'prenatal_consultation_no',
                'required' => true,
                'options' => array(
                    $prenatalVisit->prenatal_consultation_no != '' ? $prenatalVisit->prenatal_consultation_no : ''  => array('selected' => true)
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

        echo $form->DateFieldRow($prenatalVisit, 'date_visit', array(
            'class' => 'input-medium span7',
            'id' => 'date_visit',
        ));
        ?>
    </div>
    <div class="span6">
        <?php

        echo $form->TextFieldRow($prenatalVisit, 'aog', array(
            'class' => 'input-medium span7',
            'id' => 'aog',
            'readonly' => true,
        ));
        ?>
    </div>

</div>


<div class="row-fluid">
    <div class="span6">
        <?php

        echo $form->TextFieldRow($prenatalVisit, 'weight', array(
            'class' => 'input-medium span7',
            'id' => 'weight',

        ));
        ?>
    </div>
    <div class="span6">
        <?php

        echo $form->TextFieldRow($prenatalVisit, 'cardiac_rate', array(
            'class' => 'input-medium span7',
            'id' => 'cardiac_rate',

        ));
        ?>
    </div>

</div>


<div class="row-fluid">
    <div class="span6">
        <?php

        echo $form->TextFieldRow($prenatalVisit, 'respiratory_rate', array(
            'class' => 'input-medium span7',
            'id' => 'respiratory_rate',

        ));
        ?>
    </div>
    <div class="span6">
        <?php

        echo $form->TextFieldRow($prenatalVisit, 'bp', array(
            'class' => 'input-medium span7',
            'id' => 'bp',

        ));
        ?>
    </div>

</div>


<div class="row-fluid">
    <div class="span6">
        <?php

        echo $form->TextFieldRow($prenatalVisit, 'temperature', array(
            'class' => 'input-medium span7',
            'id' => 'temperature_prenatal',

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
                    'id' => 'save-prenatal-visit',
                    'class' => 'pull-right'
                ),
            )
        );
        ?>

    </div>
</div>
<hr>
<?php
$columns = array(
    array(
        'name'   => 'prenatal_consultation_no',
        'header' => 'Prenatal Consultation No.',
    ),
    array(
        'name'   => 'date_visit',
        'header' => 'Date Visit',
    ),
    array(
        'name'   => 'aog',
        'header' => 'AOG',
        'htmlOptions' => array(),
        // 'style' => 'text-align:center;width:5%;',


        // 'type'   => 'datetime',
        // 'value'  => function ($data) {
        //     return $data['encounter_date'];
        // },
    ),
    array(
        'name' => 'weight',
        'header' => 'Weight',
    ),

    array(
        'name' => 'cardiac_rate',
        'header' => 'Cardiac Rate',
    ),

    array(
        'name' => 'respiratory_rate',
        'header' => 'Respiratory Rate',
    ),



    array(
        'name' => 'bp',
        'header' => 'BP',
    ),

    array(
        'name' => 'temperature',
        'header' => 'Temperature',
    ),
    array(
        'header'      => 'Action',
        'class'       => 'CButtonColumn',
        'htmlOptions' => array(
            'style' => 'text-align:center;width:10%;',
        ),
        'buttons'     => array(
            'Delete' => array(
                'label'   => 'Delete',
                'icon'    => ' fa fa-trash',

                // 'visible' => \PersonnelCatalog::model()->checkDoctor(),
                // 'url' => function ($data) {
                //     return Yii::app()->urlManager->createUrl('doctor/patient/dashboard',[
                //         'id' => $data['id']
                //     ]);
                // },
                'options' => array(
                    'id'       => 'delete_prenatal_visits',
                    'class'    => 'btn btn-danger delete_prenatal_visits',
                ),
            ),
            'Update' => array(
                'label'   => 'Edit',
                'icon'    => ' fa fa-pencil',

                // 'visible' => \PersonnelCatalog::model()->checkDoctor(),
                // 'url' => function ($data) {
                //     return Yii::app()->urlManager->createUrl('doctor/patient/dashboard',[
                //         'id' => $data['id']
                //     ]);
                // },
                'options' => array(
                    'id'       => 'date_prenatal_visits_update',
                    'class'    => 'btn btn-success date_prenatal_visits_update',
                    // 'data-toggle' => 'modal',
                    // 'data-target' => '#myModalDatePrenatalVisits',
                ),
            ),
        ),
        'template'    => '{Delete}{Update}',
    ),
);
$this->widget('bootstrap.widgets.TbGridView', array(
    'id'                    => 'date-prenatal-visits-view',
    'type'                  => array('condensed', 'bordered', 'striped', 'hover'),
    'columns'               => $columns,
    'filter'                => null,
    'dataProvider'          => $prenatalVisits,
    // 'rowCssClassExpression' => function ($row, $data) {
    //     $ts = strtotime($data['encounter_no']);
    //     if ($ts && date('Ymd', $ts) == date('Ymd')) {
    //         return 'success bold';
    //     }
    // },
    'rowHtmlOptionsExpression' => function ($row, $data) {
        return array(
            'data-id-prenatal-consultation' => $data['id']
        );
    },
    'template' => '<div class="margin-bottom-10">{items}</div>
    {summary}
    {pager}',
));

?>
<?php
$this->renderPartial('portlets/prenatalConsultation/modal/update', array(
        'encounter' => $encounter,
    )
);
?>
<script>
     $("#update-date-prenatal-visits").on("click", function(e) {
        e.preventDefault();
        // if (confirm('Do you really want to save?')) {
        //     savePrenatalVisit();
        // } else {
        //     return false;
        // }
        const date_visit = $("#date_visit_edit").val();
        const aog = $("#aog_edit").val();
        const prenatal_consultation_no = $("#prenatal_consultation_no_edit").val();

        if (prenatal_consultation_no == "" || prenatal_consultation_no == '0') {
            Swal.fire(
                'Prenatal Consultation No is required!',
                'Please dont leave it blank!',
                'error'
            )

            return false;
        }

        if (date_visit == "") {
            Swal.fire(
                'Date of visit is required!',
                'Please dont leave it blank!',
                'error'
            )

            return false;
        }

        if (aog == "") {
            Swal.fire(
                'Aog is required!',
                'Please dont leave it blank!',
                'error'
            )

            return false;
        }

        Swal.fire({
            title: 'Update the Date of Prenatal Visits',
            text: "Are you sure do you want to update?",
            type: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Save it!'
        }).then((result) => {
            if (result.value) {
                updatePrenatalVisit();
            } else {
                return false;
            }
        })
    });
    $("#save-prenatal-visit").on("click", function(e) {
        e.preventDefault();
        // if (confirm('Do you really want to save?')) {
        //     savePrenatalVisit();
        // } else {
        //     return false;
        // }
        const date_visit = $("#date_visit").val();
        const aog = $("#aog").val();
        const prenatal_consultation_no = $("#prenatal_consultation_no").val();

        if (prenatal_consultation_no == "" || prenatal_consultation_no == '0') {
            Swal.fire(
                'Prenatal Consultation No is required!',
                'Please dont leave it blank!',
                'error'
            )

            return false;
        }

        if (date_visit == "") {
            Swal.fire(
                'Date of visit is required!',
                'Please dont leave it blank!',
                'error'
            )

            return false;
        }

        if (aog == "") {
            Swal.fire(
                'Aog is required!',
                'Please dont leave it blank!',
                'error'
            )

            return false;
        }

        Swal.fire({
            title: 'Save Date of Prenatal Visits',
            text: "Are you sure do you want to save?",
            type: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Save it!'
        }).then((result) => {
            if (result.value) {
                savePrenatalVisit();
            } else {
                return false;
            }
        })
    });

    $("#date_visit").on("change", function() {

        var date = new Date($(this).val());
        var newdate = new Date(date);

        newdate.setDate(newdate.getDate() + 280);

        var dd = newdate.getDate();
        var mm = newdate.getMonth() + 1;
        var y = newdate.getFullYear();

        var someFormattedDate = mm + '/' + dd + '/' + y;
        // console.log(someFormattedDate);
        // date_default_timezone_set('Europe/Warsaw');
        // $from = strtotime('2013-11-01');
        // $today = time();
        // $difference = $today - $from;
        // One day Time in ms (milliseconds) 
    var one_day = 1000 * 60 * 60 * 24 
  
// To set present_dates to two variables 
var present_date = new Date(); 
  
// 0-11 is Month in JavaScript 
// var christmas_day = new Date(present_date.getFullYear(), 11, 25) 
  
// // To Calculate next year's Christmas if passed already. 
// if (present_date.getMonth() == 11 && present_date.getdate() > 25) 
//     newdate.setFullYear(newdate.getFullYear() + 1) 
  
// To Calculate the result in milliseconds and then converting into days 
var Result = (Math.round( present_date.getTime() - date.getTime() ) / (one_day))/7; 
  
// To remove the decimals from the (Result) resulting days value 
var Final_Result = Result.toFixed(1); 
var weeks = Final_Result.split(".")[0]+ "w ";
var days = Final_Result.split(".")[1];
var daysConvert = "0d";
// alert(days);
    switch (days) {
            case '0':
              daysConvert = "1d";
              break;
            case '9':
                daysConvert = "7d";
                break;
            case '8':
                daysConvert = "6d";
                break;
            case '6':
                daysConvert = "5d";
                break;
            case '5':
                daysConvert = "4d";
                break;
            case '3':
                daysConvert = "3d";
                break;
            case '2':
                daysConvert = "2d";
                break;
            default:
            // days = "0 day/s"
            //   break;
          }

// alert(Final_Result);
        $("#aog").val( weeks +" "+daysConvert);
        // $("#aog").val(daysConvert);
        // document.getElementById('follow_Date').value = someFormattedDate;
    });

     $("#date_visit_edit").on("change", function() {

        var date = new Date($(this).val());
        var newdate = new Date(date);

        newdate.setDate(newdate.getDate() + 280);

        var dd = newdate.getDate();
        var mm = newdate.getMonth() + 1;
        var y = newdate.getFullYear();

        var someFormattedDate = mm + '/' + dd + '/' + y;
        // console.log(someFormattedDate);
        // date_default_timezone_set('Europe/Warsaw');
        // $from = strtotime('2013-11-01');
        // $today = time();
        // $difference = $today - $from;
        // One day Time in ms (milliseconds) 
    var one_day = 1000 * 60 * 60 * 24 
  
// To set present_dates to two variables 
var present_date = new Date(); 
  
// 0-11 is Month in JavaScript 
// var christmas_day = new Date(present_date.getFullYear(), 11, 25) 
  
// // To Calculate next year's Christmas if passed already. 
// if (present_date.getMonth() == 11 && present_date.getdate() > 25) 
//     newdate.setFullYear(newdate.getFullYear() + 1) 
  
// To Calculate the result in milliseconds and then converting into days 
var Result = (Math.round( present_date.getTime() - date.getTime() ) / (one_day))/7; 
  
// To remove the decimals from the (Result) resulting days value 
var Final_Result = Result.toFixed(1); 
var weeks = Final_Result.split(".")[0]+ "w ";
var days = Final_Result.split(".")[1];
var daysConvert = "0d";
// alert(days);
    switch (days) {
            case '0':
              daysConvert = "1d";
              break;
            case '9':
                daysConvert = "7d";
                break;
            case '8':
                daysConvert = "6d";
                break;
            case '6':
                daysConvert = "5d";
                break;
            case '5':
                daysConvert = "4d";
                break;
            case '3':
                daysConvert = "3d";
                break;
            case '2':
                daysConvert = "2d";
                break;
            default:
            // days = "0 day/s"
            //   break;
          }

// alert(Final_Result);
        $("#aog_edit").val( weeks +" "+daysConvert);
        // $("#aog").val(daysConvert);
        // document.getElementById('follow_Date').value = someFormattedDate;
    });

    function savePrenatalVisit() {
        // alert(1);
        const $form = $("#prenatal-consultation-information");
        const url = $form.data('url-save-prenatal-visit');
        const encounter_nr = $form.data('encounter_nr');
        const pid = $form.data('pid');
        const date_visit = $("#date_visit").val();
        const aog = $("#aog").val();
        const weight = $("#weight").val();
        const cardiac_rate = $("#cardiac_rate").val();
        const respiratory_rate = $("#respiratory_rate").val();
        const bp = $("#bp").val();
        const temperature = $("#temperature_prenatal").val();
        const prenatal_consultation_no = $("#prenatal_consultation_no").val();

        $.ajax({
            url: url,
            type: 'post',
            data: {
                pid: pid,
                encounter_nr: encounter_nr,
                date_visit: date_visit,
                aog: aog,
                weight: weight,
                cardiac_rate: cardiac_rate,
                respiratory_rate: respiratory_rate,
                bp: bp,
                temperature: temperature,
                prenatal_consultation_no: prenatal_consultation_no,
            },
            dataType: 'json',
            beforeSend: () => {
                // toastr.info('Updating changes, please wait...');
                Alerts.loading({
                    content: 'Adding data for date of prenatal vists. Please wait...'
                });

            },
            success: (response) => {
                Alerts.close();
                if (response['status']) {
                    // swal("Info!", "Can`t Delete, Already Discharged!", "info");
                    $.fn.yiiGridView.update("date-prenatal-visits-view");
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
                    'Please contact your administrator!',
                    'Something went wrong!',
                    'error'
                )
                // alert('Something went wrong, Please contact your administrator');
                // toastr.error('Something went wrong, Please contact your administrator');
            }

        });
        // alert(prenatal_consultaion_no)
    }

    function deletePrenatalVisit(id) {

        const $form = $("#prenatal-consultation-information");
        const url = $form.data('url-delete-prenatal-visit');
        const encounter_nr = $form.data('encounter_nr');
        const pid = $form.data('pid');
        const prenatal_visit_id = id;


        $.ajax({
            url: url,
            type: 'post',
            data: {
                pid: pid,
                encounter_nr: encounter_nr,
                prenatal_visit_id: id,
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
                    $.fn.yiiGridView.update("date-prenatal-visits-view");
                    // alert(response['message']);
                    Swal.fire(
                        'Saved!',
                        response['message'],
                        'success'
                    )
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
                    'Please contact your administrator!',
                    'Something went wrong!',
                    'error'
                )
                // alert('Something went wrong, Please contact your administrator');
                // toastr.error('Something went wrong, Please contact your administrator');
            }

        });
        // alert(url)
    }
     function editPrenatalVisit(id) {

        const $form = $("#prenatal-consultation-information");
        const url = $form.data('url-get-data-prenatal-visit');
        const encounter_nr = $form.data('encounter_nr');
        const pid = $form.data('pid');
        const prenatal_visit_id = id;


        $.ajax({
            url: url,
            type: 'post',
            data: {
                pid: pid,
                encounter_nr: encounter_nr,
                prenatal_visit_id: id,
            },
            dataType: 'json',
            beforeSend: () => {
                // toastr.info('Updating changes, please wait...');
                Alerts.loading({
                    content: 'Fetching the data. Please wait...'
                });

            },
            success: (response) => {
                Alerts.close();
                $("#myModalDatePrenatalVisits").modal('show');
                $("#date_visit_edit").val(response.data.date_visit);
                $("#aog_edit").val(response.data.aog);
                $("#weight_edit").val(response.data.weight);
                $("#weight_edit").val(response.data.weight);
                $("#cardiac_rate_edit").val(response.data.cardiac_rate);
                $("#respiratory_rate_edit").val(response.data.respiratory_rate);
                $("#bp_edit").val(response.data.bp);
                $("#temperature_edit").val(response.data.temperature);
                $("#prenatal_consultation_no_edit").val(response.data.prenatal_consultation_no);
                // alert('check');
                // console.log(response);
                // if (response['status']) {
                //     // swal("Info!", "Can`t Delete, Already Discharged!", "info");
                //     $.fn.yiiGridView.update("date-prenatal-visits-view");
                //     // alert(response['message']);
                //     Swal.fire(
                //         'Saved!',
                //         response['message'],
                //         'success'
                //     )
                // } else {
                //     Swal.fire(
                //         'Failed to save!',
                //         response['message'],
                //         'error'
                //     )
                //     // toastr.success('Past Medical History updated');
                //     // alert(response['message']);
                // }
            },

            error: () => {
                Swal.fire(
                    'Please contact your administrator!',
                    'Something went wrong!',
                    'error'
                )
                // alert('Something went wrong, Please contact your administrator');
                // toastr.error('Something went wrong, Please contact your administrator');
            }

        });
        // alert(url)
    }
    function updatePrenatalVisit() {

        const $form = $("#prenatal-consultation-information");
        const prenatal_consultation_no_edit = $("#prenatal_consultation_no_edit").val();
        const date_visit_edit = $("#date_visit_edit").val();
        const aog_edit = $("#aog_edit").val();
        const weight_edit = $("#weight_edit").val();
        const cardiac_rate_edit = $("#cardiac_rate_edit").val();
        const respiratory_rate_edit = $("#respiratory_rate_edit").val();
        const bp_edit = $("#bp_edit").val();
        const temperature_edit = $("#temperature_edit").val();
        const url = $form.data('url-update-data-prenatal-visit');
        const encounter_nr = $form.data('encounter_nr');
        const pid = $form.data('pid');
        const prenatal_visit_id = $("#date_prenatal_visits_edit_id").val();

        $.ajax({
            url: url,
            type: 'post',
            data: {
                pid: pid,
                encounter_nr: encounter_nr,
                prenatal_visit_id: prenatal_visit_id,
                prenatal_consultation_no: prenatal_consultation_no_edit,
                date_visit: date_visit_edit,
                aog: aog_edit,
                weight: weight_edit,
                cardiac_rate: cardiac_rate_edit,
                respiratory_rate: respiratory_rate_edit,
                bp: bp_edit,
                temperature: temperature_edit
            },
            dataType: 'json',
            beforeSend: () => {
                // toastr.info('Updating changes, please wait...');
                Alerts.loading({
                    content: 'Update the date of prenatal visit data. Please wait...'
                });

            },
            success: (response) => {
                Alerts.close();
                $("#myModalDatePrenatalVisits").modal('hide');
                // alert('check');
                // console.log(response);
                if (response['status']) {
                    // swal("Info!", "Can`t Delete, Already Discharged!", "info");
                    $.fn.yiiGridView.update("date-prenatal-visits-view");
                    // alert(response['message']);
                    Swal.fire(
                        'Saved!',
                        response['message'],
                        'success'
                    )
                } else {
                    Swal.fire(
                        'Failed to save!',
                        response['message'],
                        'error'
                    )
                    // toastr.success('Past Medical History updated');
                    // alert(response['message']);
                }
                $("#myModalDatePrenatalVisits").attr("hidden",true);
                $("#myModal").attr("hidden",true);
            },

            error: () => {
                Swal.fire(
                    'Please contact your administrator!',
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
            var deletePrenatalVisits = $('.delete_prenatal_visits');
            deletePrenatalVisits.livequery(function(e) {
                $(this).click(function(e) {
                    e.preventDefault();
                    // var $this = $(this);

                    var $this = $(this).parents('tr');
                    var id = $this.data('id-prenatal-consultation');
                    // alert(id)
                    // if (confirm('Do you really want to delete?')) {
                    //     deletePrenatalVisit(id);
                    // } else {
                    //     return false;
                    // }
                    Swal.fire({
                        title: 'Delete Date of Prenatal Visit',
                        text: "Are you sure do you want to delete?",
                        type: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, Delete it!'
                    }).then((result) => {
                        if (result.value) {
                            deletePrenatalVisit(id);
                        } else {
                            return false;
                        }
                    })
                });
            });
        });

        jQuery(function ($) {
            var date_prenatal_visits_update = $('.date_prenatal_visits_update');
            date_prenatal_visits_update.livequery(function (e) {
                $(this).click(function (e) {
                    e.preventDefault();
                    var $this = $(this).parents('tr');
                    var id = $this.data('id-prenatal-consultation');
                    $('#date_prenatal_visits_edit_id').val(id);
                    // var url = $("#medicine-form").data('url-get-data-for-date-prenatal-visit');
                    editPrenatalVisit(id);
                    // var url_get = $("#medicine-form").data('url-get-update');
                    // console.log(id);
                    // console.log(url);
                    // $.ajax({
                    //     url: url,
                    //     type: 'get',
                    //     data: {
                    //         id: id,
                    //     },
                    //     dataType: 'json',
                    //     beforeSend: () => {
                    //     },
                    //     success: (response) => {
                    //         console.log(response);
                    //         populateData(response)
                    //     }
                    // });
                });
            });
        });
    });
</script>