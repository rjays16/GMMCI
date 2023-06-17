<?php

$this->widget(
    'bootstrap.widgets.TbGridView',
    array(
        'type' => 'striped',
        'dataProvider' => $gridDataProvider,
        'template' => "{items}{pager}",
        'columns' => $gridColumns,
    )
);