<?php
/**
 * ${NAME}.php
 *
 * @author Jan Chris S. Ogel <iamjc93@gmail.com>
 * @copyright (c) 2019, Segworks Technologies Corporation (http://www.segworks.com)
 */

//\Yii::import('bootstrap.widgets.TbButton');

$columns = array(
    array(
        'name' => 'date_action',
        'header' => 'Date',
        'type' => 'raw',
        'headerHtmlOptions' => array('style' => 'text-align: center; width: 100px;'),
        'value' => function ($data) {
            $date = date('Y-m-d', strtotime($data->date_action));

            return $date;
        }
    ),
    array(
        'name' => 'doctor_action',
        'header' => 'Doctor\'s Action/Order',
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
                    'id' => 'course_ward_delete',
                    'class' => 'btn btn-danger course_ward_delete',
                ),
            ),
        ),
        'template' => '{Delete}',

    ),
);


$dataProvider = Cf4CourseInTheWard::model()->getCourseWard($encounter->encounter_nr);

$this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'course_ward_list',
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
            let course_ward_delete = $('.course_ward_delete');
            course_ward_delete.livequery(function (e) {
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
                            destroyCourseWard(id);
                        } else {
                            return false;
                        }
                    });
                });
            });
        });
    });

    function destroyCourseWard(id) {
        const $form = $("#course-ward-form");
        const url = $form.data('url-delete');
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
