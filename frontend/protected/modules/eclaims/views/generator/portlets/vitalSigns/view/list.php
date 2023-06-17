<?php
//\Yii::import('bootstrap.widgets.TbButton');

$columns = array(
    array(
        'name' => 'created_at',
        'header' => 'Date',
        'type' => 'raw',
        'headerHtmlOptions' => array('style' => 'text-align: center; width: 100px;'),
        'value' => function ($data) {
            $date = date('Y-m-d', strtotime($data->created_at));

            return $date;
        }
    ),
    array(
        'name' => 'systolic',
        'header' => '(BP) Systolic',
        'type' => 'raw',
    ),
    array(
        'name' => 'diastolic',
        'header' => '(BP) Diastolic',
        'type' => 'raw',
    ),
    array(
        'name' => 'cr',
        'header' => 'Heart Rate /m',
        'type' => 'raw',
    ),
    array(
        'name' => 'rr',
        'header' => 'Respiratory /m',
        'type' => 'raw',
    ),
    array(
        'name' => 'temperature',
        'header' => 'Temparature ℃',
        'type' => 'raw',
    ),
    array(
        'name' => 'height',
        'header' => 'Height (cm)',
        'type' => 'raw',
    ),
    array(
        'name' => 'weight',
        'header' => 'Weight (kg)',
        'type' => 'raw',
    ),
    array(
        /* Below are the button changes if returned or not. */
        'header' => 'Action',
        'class' => 'CButtonColumn',
        'headerHtmlOptions' => array('style' => 'text-align: center; width: 100px;'),
//        'htmlOptions' => array('style' => 'align: center; text-align: center;'),
        'buttons' => array(
            'Delete' => array(
                'label' => 'Delete',
                'icon' => ' fa fa-trash',
                'options' => array(
                    'id' => 'vital_signs_delete',
                    'class' => 'btn btn-danger vital_signs_delete',
                ),
            ),
        ),
        'template' => '{Delete}',

    ),
);


$dataProvider = Cf4VitalSigns::model()->getVitalSigns($encounter->encounter_nr);

$this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'vital_signs_list',
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

<script>
    $(document).ready(function () {
        jQuery(function ($) {
            let vital_signs_delete = $('.vital_signs_delete');
            vital_signs_delete.livequery(function (e) {
                $(this).click(function (e) {
                    e.preventDefault();
                    let $this = $(this).parents('tr');
                    let id = $this.data('id');

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
                            destroyVitalSigns(id);
                        } else {
                            return false;
                        }
                    });
                });
            });
        });
    });

    function destroyVitalSigns(id) {
        const $form = $("#vitalSigns-record");
        const url = $form.data('url-delete-vital-signs');
        const encounter_nr = $form.data('encounter_nr');

        $.ajax({
            url: url,
            type: 'post',
            data: {
                encounter_nr: encounter_nr,
                id: id,
            },
            dataType: 'json',
            beforeSend: () => {
            },
            success: (response) => {
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
                        type: 'error',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
                $.fn.yiiGridView.update("vital_signs_list");
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
