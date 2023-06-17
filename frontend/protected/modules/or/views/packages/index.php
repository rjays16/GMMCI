<?php
$this->breadcrumbs=array(
    'Home'=>array('main/index'),
	'Packages',
);
$this->menu=array(
    array('label'=>'Create Package', 'url'=>array('create')),
);
?>
<h1>Package List</h1>
<div class="well">
<?php

$this->widget(
    'bootstrap.widgets.TbGridView',
    array(
        'type'=>'striped bordered',
        'dataProvider' => $package->search(),
        'template' => "{summary}{items}{pager}",
        'filter'=>$package,
        'columns' => $gridColumns,
    )
);
?>
</div>