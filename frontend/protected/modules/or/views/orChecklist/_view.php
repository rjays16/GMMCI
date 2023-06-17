<?php 

$datetime = strtotime($data->date_created);
$data->date_created = date("F j, Y, g:i a", $datetime);

$datetime = strtotime($data->date_modified);
$data->date_modified = date("F j, Y, g:i a", $datetime);

?>

<div class="well">
	<div class="view">

		<dl class="dl-horizontal">
			<dt>
				<?php echo CHtml::encode($data->getAttributeLabel('checklist_question')); ?>:
			</dt>
			<dd>
				<?php echo CHtml::encode(strip_tags($data->checklist_question)); ?>	
			</dd>

			<dt>
				<?php echo CHtml::encode($data->getAttributeLabel('label_data')); ?>:
			</dt>
			<dd>
				<?php echo CHtml::encode($data->label_data); ?>
			</dd>

			<dt>
				<?php echo CHtml::encode($data->getAttributeLabel('date_created')); ?>:
			</dt>
			<dd>
				<?php echo CHtml::encode($data->date_created); ?>
			</dd>

			<dt>
				<?php echo CHtml::encode($data->getAttributeLabel('date_modified')); ?>:
			</dt>
			<dd>
				<?php echo CHtml::encode($data->date_modified); ?>
			</dd>

			<dt>
				<?php echo CHtml::encode($data->getAttributeLabel('type')); ?>:
			</dt>
			<dd>
				<?php echo CHtml::encode($data->type); ?>
			</dd>
		</dl>

	</div>
</div>
