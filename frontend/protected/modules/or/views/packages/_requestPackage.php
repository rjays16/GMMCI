<div class="accordion-group">
    <div class="accordion-heading" style="display: inline-block;">
        <a class="accordion-toggle" data-toggle="collapse"
        data-parent="#accordion" href="#collapse<?= $package->package_id ?>">
            <?= CHtml::encode($package->package_name) ?>
        </a>
        
    </div>
    <?php
        $this->widget(
            'bootstrap.widgets.TbButton', 
            array(
                'icon'=>'remove', 
                'size'=>'mini',
                'htmlOptions' => array(
                    'class'=>'pull-right', 
                    'style' => 'margin:0.5em;',
                    'onclick'=>"javascript:deletePackage(this);"
                )
            )
        );
    ?>
    <div id="collapse<?= $package->package_id ?>" class="accordion-body collapse">
        <input id="OrRequest_orPackageUse_<?= $package->package_id ?>" value="<?= $package->package_id ?>" name="OrRequest[orPackageUses][]" type="hidden" />
        <div class="accordion-inner">
            <table>
                <thead>
                    <tr>
                        <td width="10%">Item #</td>
                        <td width="10%">Purpose</td>
                        <td width="40%">Description</td>
                        <td width="10%">Quantity</td>
                        <td width="10%">Price</td>
                        <td width="10%">Total Price</td>
                        <td width="10%">Remarks</td>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($package->packageDetails as $packageDetail): ?>
                        <tr>
                            <td><?= $packageDetail->item_id ?></td>
                            <td><?= $packageDetail->item_purpose ?></td>
                            <td><?= $packageDetail->description ?></td>
                            <td><?= $packageDetail->quantity ?></td>
                            <td><?= $packageDetail->price ?></td>
                            <td><?= $packageDetail->price * $packageDetail->quantity ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>