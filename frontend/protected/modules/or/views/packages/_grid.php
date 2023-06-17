<?php
$this->widget(
    'bootstrap.widgets.TbJsonGridView',
    array(
        'dataProvider' => $model->search(),
        'filter' => $model,
        'type' => 'striped bordered condensed',
        'columns' => array(
            'package_name',
            array(
                'class' => 'bootstrap.widgets.TbJsonButtonColumn',
            ),
        ),
    )
);