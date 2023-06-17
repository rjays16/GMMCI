<?php

$this->breadcrumbs[] = 'View';
$this->setPageTitle("HIE Service Configuration");

?>

<div class="row-fluid">
    <div class="span12">


<?php
    $box = $this->beginWidget(
        'bootstrap.widgets.TbBox',
        array(
            'title' => 'HIE Service',
            'headerIcon' => 'icon-cog',
            'htmlOptions' => array('class' => 'bootstrap-widget-table')
        )
    );
?>

        <?php $this->widget('bootstrap.widgets.TbDetailView', array(
            'data' => $model,
            'attributes'=>array(
                'hospital_name',
                'client_id',
                array(
                    'type' => 'raw',
                    'name' => 'client_secret',
                    'value' => '****'
                ),
                'base_url',
                'files_url'
            ),
        ));
        ?>


        <div class="form-actions">
            <?php $this->widget('bootstrap.widgets.TbButton',
                array(
                    'buttonType' => 'link',
                    'type' => 'primary',
                    'icon' => 'pencil white',
                    'label' => 'Update',
                    'url' => $this->createUrl('update')
                ));
            ?>
        </div>

<?php $this->endWidget(); ?>

    </div>
</div>
