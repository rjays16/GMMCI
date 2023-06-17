<div class="well">
	<div class="view">

		<dl class="dl-horizontal">
			<dt>
				<?php echo CHtml::encode($data->getAttributeLabel('technique_name')); ?>:
			</dt>
			<dd>
				<?php echo CHtml::encode($data->technique_name); ?>	
			</dd>

			<dt>
				<?php echo CHtml::encode($data->getAttributeLabel('technique_desc')); ?>:
			</dt>
			<dd>
				<?php echo CHtml::encode(strip_tags($data->technique_desc)); ?>
			</dd>

		</dl>

	</div>
	
</div>

