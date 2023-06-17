<?php
$this->breadcrumbs=array(
	'OR Checklists'=>array('index'),
	$model->checklist_id,
);

$this->menu=array(
//array('label'=>'List OR Checklist','url'=>array('index')),
array('label'=>'Create OR Checklist','url'=>array('create')),
array('label'=>'Update OR Checklist','url'=>array('update','id'=>$model->checklist_id)),
array('label'=>'Delete OR Checklist','url'=>'#','linkOptions'=>array('submit'=>array('delete','id'=>$model->checklist_id),'confirm'=>'Are you sure you want to delete this item?')),
array('label'=>'Manage OR Checklist','url'=>array('admin')),
);
?>

<h1>View OR Checklist ID # <?php echo $model->checklist_id; ?></h1>

<br />

<?php 

//changes the date format for user viewing

$datetime = strtotime($model->date_created);
$model->date_created = date("F j, Y, g:i a", $datetime);

$datetime = strtotime($model->date_modified);
$model->date_modified = date("F j, Y, g:i a", $datetime);


 ?>

 <div class="well"> 

  <?php $this->widget('bootstrap.widgets.TbDetailView',array(
  'data'=>$model,
  'attributes'=>array(
      array(            
        'name'=>'checklist_question',
        'type'=>'raw',
        ),
      'label_data',
      'date_created',
      'date_modified',
      'type',
  ),
  )); ?>

 </div>


<?php 

/*$this->widget('zii.widgets.grid.CGridView', array(
        'dataProvider'=>$dataProvider,
        'columns'=>array(
           'column1',
           array(
             'name'=>'column2',
             'value'=>'strip_tags($data->column2)'
           ),
           array(
               'class'=>'CButtonColumn',
               'template'=>'{update}{delete}',
           ),
        ),
));*/

 ?>

