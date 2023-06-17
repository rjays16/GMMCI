<?php
/**
 * _list.php
 *
 * @author Jan Chris S. Ogel <iamjc93@gmail.com>
 * @copyright (c) 2019, Segworks Technologies Corporation (http://www.segworks.com)
 */

\Yii::import('bootstrap.widgets.TbButton');

$columns = array(
    array(
        'name' => 'generic',
        'header' => 'Drug Description',
        'type' => 'raw',
    ),
    array(
        'name' => 'route',
        'header' => 'Route',
        'type' => 'raw',
    ),
    array(
        'name' => 'frequency',
        'header' => 'Frequency',
        'type' => 'raw',
    ),
    array(
        'name' => 'quantity',
        'header' => 'Quantity',
        'type' => 'raw',
        'headerHtmlOptions' => array('style' => 'text-align: center; width: 100px;'),
    ),
    array(
        'name' => 'cost',
        'header' => 'Total Amount',
        'type' => 'raw',
        'headerHtmlOptions' => array('style' => 'text-align: center; width: 100px;'),
    ),
    array(
        /* Below are the button changes if returned or not. */
        'header' => 'Action',
        'class' => 'CButtonColumn',
        'headerHtmlOptions' => array('style' => 'text-align: center; width: 215px;'),
//        'htmlOptions' => array('style' => 'align: center; text-align: center;'),
        'buttons' => array(
            'Delete' => array(
                'label' => 'Delete',
                'icon' => ' fa fa-trash',
                'options' => array(
                    'id' => 'medicine_delete',
                    'class' => 'btn btn-danger medicine_delete',
                ),
            ),
            'Update' => array(
                'label' => 'Update',
                'icon' => ' fa fa-pencil',
                'options' => array(
                    'id' => 'medicine_update',
                    'class' => 'btn btn-success medicine_update',
                    'data-toggle' => 'modal',
                    'data-target' => '#myModal',
                ),
            ),
        ),
        'template' => '{Delete} {Update}',
    ),
);

$dataProvider = Cf4Medicine::model()->getMedicine($encounter->encounter_nr);

$this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'medicine_list',
    'type' => array('condensed', 'bordered', 'striped', 'hover'),
    'columns' => $columns,
    'dataProvider' => $dataProvider,
    'rowHtmlOptionsExpression' => function ($row, $data) {
        return array(
            'data-id' => $data->id,
        );
    },
    'template' => "{items}\n{summary}\n{pager}",
));

?>

<?php
$this->renderPartial('portlets/medicine/modal/update', array(
        'encounter' => $encounter,
    )
);
?>

<script>
    $(document).ready(function () {

        jQuery(function ($) {
            var medicine_delete = $('.medicine_delete');
            medicine_delete.livequery(function (e) {
                $(this).click(function (e) {
                    e.preventDefault();
                    var $this = $(this).parents('tr');
                    var id = $this.data('id');

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.value) {
                            destroyMedicine(id);
                        } else {
                            return false;
                        }
                    });
                });
            });
        });

        /*Get Medicine Data*/
        jQuery(function ($) {
            var medicine_update = $('.medicine_update');
            medicine_update.livequery(function (e) {
                $(this).click(function (e) {
                    e.preventDefault();
                    var $this = $(this).parents('tr');
                    var id = $this.data('id');
                    $('#Medicine_id').val(id);
                    var url = $("#medicine-form").data('url-get-data');
                    var url_get = $("#medicine-form").data('url-get-update');
                    console.log(id);
                    console.log(url);
                    $.ajax({
                        url: url,
                        type: 'get',
                        data: {
                            id: id,
                        },
                        dataType: 'json',
                        beforeSend: () => {
                        },
                        success: (response) => {
                            console.log(response);
                            populateData(response)
                        }
                    });
                });
            });
        });

        function populateData(data){
            $('#Medicine_route').val(data[0].route);
            $('#Medicine_frequency').val(data[0].frequency);
            $('#Medicine_quantity').val(data[0].quantity);
            $('#Medicine_tot_amount').val(data[0].cost);

            if(data[0].is_pndf == 0){
                $("#generic_field").css("display", "");
                $('#Medicine_description').val(data[0].generic);
                $('#Medicine_description').removeClass('hidden');
                $('#gen_label').removeClass('hidden');
            }else{
                $("#generic_field").css("display", "none");
                $('#Medicine_description').addClass('hidden');
                $('#Medicine_description').val('');
                $('#gen_label').addClass('hidden');
            }
        }

        /*Update Medicine*/
        $("#update-medicine").on("click", function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Save Update',
                text: "Are you sure do you want to update?",
                type: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Update it!'
            }).then((result) => {
                if (result.value) {
                    updateMedicine();
                } else {
                    return false;
                }
            });
        });
    });

    function updateMedicine() {
        const route = $("#Medicine_route").val();
        const frequency = $("#Medicine_frequency").val();
        const quantity = $("#Medicine_quantity").val();
        const cost = $("#Medicine_tot_amount").val();
        const description = $("#Medicine_description").val();
        const id = $("#Medicine_id").val();
        var url = $("#medicine-form").data('url-update');
        var encounter_nr = $("#medicine-form").data('encounter_nr');
        $.ajax({
            url: url,
            type: 'post',
            data: {
                encounter_nr: encounter_nr,
                id: id,
                route: route,
                frequency: frequency,
                quantity: quantity,
                cost: cost,
                description: description,
            },
            dataType: 'json',
            beforeSend: () => {
            },
            success: (response) => {
                console.log(response);
                if (response.status) {
                    Swal.fire({
                        title: 'The data has been updated!',
                        type: 'success',
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    Swal.fire({
                        title: 'Something went wrong!',
                        text: response.message,
                        type: 'error',
                        showConfirmButton: true,
                    });
                }
                $('#myModal').modal("hide");
                $.fn.yiiGridView.update("medicine_list");
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

    function destroyMedicine(id) {
        const $form = $("#medicine-form");
        const url = $form.data('url-delete');
        const encounter_nr = $form.data('encounter_nr');
        const pid = $form.data('pid');

        $.ajax({
            url: url,
            type: 'post',
            data: {
                encounter_nr: encounter_nr,
                pid: pid,
                id: id,
            },
            dataType: 'json',
            beforeSend: () => {
            },
            success: (response) => {
                console.log(response);
                if (response.status) {
                    Swal.fire({
                        title: 'The data has been deleted!',
                        type: 'success',
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    Swal.fire({
                        title: 'Something went wrong!',
                        text: response.message,
                        type: 'error',
                        showConfirmButton: true,
                    });
                }
                $.fn.yiiGridView.update("medicine_list");
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
<style>
    #myModal {
        width: 872px;
    }

    .modal {
        left: 41%;
    }
</style>
