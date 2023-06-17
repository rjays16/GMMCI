<?php
/* @var $this PackagesController */
/* @var $model Packages */

Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/frontend/scripts/packages/view.js');

$this->breadcrumbs=array(
	'Packages'=>array('index'),
	$model->package_name,
);

$this->menu=array(
	array('label'=>'List Packages', 'url'=>array('index')),
	array('label'=>'Create Packages', 'url'=>array('create')),
	array('label'=>'Update Packages', 'url'=>array('update', 'id'=>$model->package_id)),
	array('label'=>'Delete Packages', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->package_id),'confirm'=>'Are you sure you want to delete this item?')),
);
?>

<h1><?php echo $model->package_name; ?></h1>

<?php
    //package details
    $box = $this->beginWidget(
        'bootstrap.widgets.TbBox',
        array(
            'title' => 'Package Details',
            'htmlOptions' => array('class' => 'bootstrap-widget-table')
        )
    );
    $this->widget(
        'zii.widgets.CDetailView',
        array(
            'data' => $model,
            'attributes' => array(
                'package_id',
                'package_name',
                'pkg_phiccode',
                 array(
                     'name'=>'is_package',
                     'value'=>$model->isPackageText
                 ),
                array(
                    'name'=>'is_zpackage',
                    'value'=>$model->isZpackageText
                ),
                'package_price',
            ),
        )
    );

    $this->endWidget();

    //package items
    $box = $this->beginWidget(
        'bootstrap.widgets.TbBox',
        array(
            'title' => 'Package Items',
        )
    );
?>
    <table class="items table table-striped" id="package-items" style="width: 100%;">
        <thead>
        <tr>
            <th style="width: 100px;">Item Code</th>
            <th>Name</th>
            <th>Purpose</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Total</th>
        </tr>
        </thead>
        <tbody id="package-items-block">
            <?php if(empty($model->packageDetails)): ?>
                <tr id="no-item"><td colspan="100"><i>No item currently added</i></td></tr>
            <?php else: ?>
                <?php foreach($model->packageDetails as $pd): ?>
                    <tr>
                        <td>
                            <input type="hidden" name="PackageDetails[item_code][]" value="<?= $pd->item_code; ?>" />
                            <input type="hidden" name="PackageDetails[item_name][]" value="<?= $pd->item_name; ?>" />
                            <input type="hidden" name="PackageDetails[item_purpose][]" value="<?= $pd->item_purpose; ?>" />
                            <input type="hidden" name="PackageDetails[quantity][]" value="<?= $pd->quantity; ?>" />
                            <input type="hidden" name="PackageDetails[price][]" value="<?= $pd->price; ?>" />
                            <?= $pd->item_code; ?>
                        </td>
                        <td><?= $pd->item_name; ?></td>
                        <td><?= $pd->itemPurposeText; ?></td>
                        <td><?= $pd->price; ?></td>
                        <td><?= $pd->quantity; ?></td>
                        <td><?= $pd->quantity * $pd->price; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
<?php
    $this->endWidget();
?>