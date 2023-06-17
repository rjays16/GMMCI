<?php
use SegHis\modules\inventory\models\InventoryExporter;
/* @var $this AdjustmentController */

/* @var $model Adjustment */
/* @var $form InventoryActiveForm */
/* @var $areas Array */
/* @var $cs CClientScript */
/* @var $units \SegHis\models\inventory\Unit[] */

$cs = Yii::app()->clientScript;
$baseUrl = Yii::app()->request->baseUrl;
$cs->registerScriptFile($baseUrl . '/modules/inventory/adjustment.js');
$cs->registerScript('headJs', <<<JS
var baseUrl = '{$baseUrl}';
JS
, CClientScript::POS_HEAD);


$readyJs = <<<'JS'

$('#form-search-item')
  .off('submit')
  .on('submit', function(e) {
    e.preventDefault();
    var $this = $(this);
    $('#item-search-grid').yiiGridView('update', {
      data: $this.serialize()
    });
  });

$('#search-results-container').off('click', '.add-to-tray').on('click', '.add-to-tray', function(e) {
  e.preventDefault();
  var $tr = $(this).parents('tr:first');
  var data = $.makeArray($tr.find('[name="result-data"]')).reduce(function(prev, curr) {
    var $curr = $(curr);
    prev[$curr.attr('data-id')] = $curr.val();
    return prev;
  }, {});

  addToTray(data);
});

JS;

$cs->registerScript('headJs', $readyJs, CClientScript::POS_READY);

?>

<div class="form">
    <?php
        $form = $this->beginWidget('inventory.widgets.InventoryActiveForm', array(
            'id' => 'adjustment-form',
            'enableAjaxValidation' => false,
            'enableClientValidation' => false,
            'clientOptions' => array(
                'validateOnSubmit' => false
            ),
            'type' => 'horizontal'
        ));
    ?>

    <?php
        $isNewRecord = $model->getIsNewRecord();
        $this->beginWidget('application.widgets.SegBox', array(
            'title' => 'Adjustment information',
            'headerIcon' => 'fa fa-wrench',
            'headerButtons' => array(),
            'footer' => CHtml::tag('div', array(),
                $this->widget('bootstrap.widgets.TbButton', array(
                    'id' => 'btn-submit',
                    'buttonType' => 'button',
                    'type' => 'primary',
                    'icon' => 'fa fa-save',
                    'label' => 'Save',
                    'htmlOptions' => array(
                        'style' => 'margin-left: 0.5em; font-weight: 600',
                        'class' => 'pull-right'
                    )
                ), true) .
                $this->widget('bootstrap.widgets.TbButton', array(
                    'buttonType' => 'button',
                    'type' => 'success',
                    'icon' => 'fa fa-thumb-tack rotate-45',
                    'label' => 'Save and Post to Inventory',
                    'visible' => !$isNewRecord,
                    'htmlOptions' => array(
                        'style' => 'font-weight: 600',
                        'class' => 'pull-right',
                        'onclick' => 'postToInventory();'
                    )
                ), true)
            )
        ));

    ?>

        <div class="alert alert-info">
            Fields with <span class="required">*</span> are required.
        </div>

    <?php
        echo CHtml::hiddenField('post-to-inventory', 0);

        echo $form->errorSummary($model);

        echo CHtml::tag('div', array('class' => 'clearfix'));
        echo CHtml::tag('div', array('class' => 'span5'));

        //echo $form->dropDownListRow($model, 'area_code', $areas, array('placeholder' => false));
        echo $form->select2Row($model, 'area_code', array(
            'data' => $areas,
        ));

        echo $form->dateTimePickerSlider($model, 'adjust_date', array(
            'htmlOptions' => array(
                'value' => $isNewRecord ? date('m/d/Y h:i A') : date('m/d/Y h:i A', strtotime($model->adjust_date))
            ),
            'options' => array(
                'dateFormat' => 'mm/dd/yy',
                'timeFormat' => 'hh:mm TT',
                'changeMonth' => true,
                'changeYear' => true,
            )
        ));

        echo CHtml::closeTag('div');/*.span6*/

        echo CHtml::tag('div', array('class' => 'span5'));

        echo $form->textAreaRow($model, 'remarks');
        echo $form->displayTextFieldRow(
            $model,
            $isNewRecord ? $_SESSION['sess_login_username'] : $model->personnel->person->getFullName(),
            $_SESSION['sess_user_personell_nr'],
            'adjusting_id',
            array('readonly' => 'readonly')
        );
        echo CHtml::closeTag('div');/*.span6*/
        echo CHtml::closeTag('div');/*.clearfix*/

        $this->endWidget();
    ?>

    <?php
        $count = count($model->adjustmentDetails);

        $this->beginWidget('application.widgets.SegBox', array(
            'title' => 'Items to Adjust',
            'headerIcon' => 'fa fa-medkit',
            'headerButtons' => array(
                array(
                    'class' => 'bootstrap.widgets.TbButton',
                    'label' => 'Add Items',
                    'icon' => 'fa fa-plus',
                    'type' => '',
                    'htmlOptions' => array(
                        'data-toggle' => 'modal',
                        'data-target' => '#modal-add-items',
                    ),
                ),
                // array(
                //     'class' => 'bootstrap.widgets.TbButton',
                //     'label' => 'Add All',
                //     'icon' => 'fa fa-list-ul',
                //     'type' => '',
                //     'htmlOptions' => array(
                //         'id' => 'add-all-items',
                //         'data-toggle' => 'tooltip',
                //         'title' => 'Add all items available from the selected area.',
                //     ),
                // ),
            ),
            'htmlOptions' => array(
                'class' => 'bootstrap-widget-table',
            ),
            'footer' => "<strong>Count:</strong> <span id=\"items-count\">{$count}</span>"
        ));
    ?>




    <table class="table table-hover table-bordered">
        <thead>
            <tr>
                <th width="2%"><i class="fa fa-gear"></i></th>
                <th width="6%">Item #</th>
                <th width="*">Name</th>
                <th width="5%">Qty</th>
                <th width="8%">Unit</th>
                <th width="8%">Serial #</th>
                <th width="8%">Lot #</th>
                <th width="10%">Expiry Date</th>
                <th width="7%">Unit Cost</th>
                <th width="10%">Adj Qty</th>
                <th width="10%">+/- Qty</th>
                <th width="8%">Reason</th>
            </tr>
        </thead>
        <tbody id="list">
        <?php
            /* @var AdjustmentDetails $item */
            foreach ($model->adjustmentDetails as $item):


        ?>
            <tr data-id="<?= $item->item_code ?>" data-skuid="<?= $item->sku_id ?>">
                <td><a href="#" onclick="deleteRowAjax('<?= $item->id ?>',this)"><i class="fa fa-times-circle fa-lg"></i></a></td>
                <td>
                    <?= $item->item_code ?>
                    <input type="hidden" name="adjustment_items[<?= $item->id ?>][item_id]"
                           value="<?= $item->item_code ?>"/>
                    <input type="hidden" name="adjustment_items[<?= $item->id ?>][item_sku_id]"
                           value="<?= $item->sku_id ?>"/>
                </td>
                <td>
                    <?= $item->product->artikelname ?>
                    <input type="hidden" name="adjustment_items[<?= $item->id ?>][item_name]"
                           value="<?= $item->product->artikelname ?>"/>
                </td>
                <td>
                <!-- Modified by julz -->
                    <?= InventoryExporter::getHospitalItemQty($item->item_code,$model->area_code,'',date('Y-m-d',strtotime($model->adjust_date)));   
                     ?>
                    <input class="item_remaining" type="hidden"
                           name="adjustment_items[<?= $item->id ?>][item_remaining]"
                           value="<?= InventoryExporter::getHospitalItemQty($item->item_code,$model->area_code,'',date('Y-m-d',strtotime($model->adjust_date)));?>"/>
                <!-- End -->
                </td>
                <td class="cell">
                    <?php echo CHtml::dropDownList(
                        "adjustment_items[{$item->id}][item_unit]",
                        $item->unit_id,
                        CMap::mergeArray(
                            array('- SELECT -' => ''),
                            CHtml::listData($units, 'unit_id', 'unit_name')
                        ),array(
                        'class' => 'cell-input input-block-level'
                    )); ?>
                </td>
                <td class="cell">
                    <input class="cell-input input-block-level" type="text" name="adjustment_items[<?= $item->id ?>][item_serial_no]"
                           title="Serial Number" value="<?= $item->serial_no ?>"/>
                </td>
                <td class="cell">
                    <input class="cell-input input-block-level" type="text" name="adjustment_items[<?= $item->id ?>][item_lot_no]"
                           title="Lot Number" value="<?= $item->lot_no ?>"/>
                </td>
                <td class="cell">
                    <input class="cell-input datetime input-block-level" type="text"
                           name="adjustment_items[<?= $item->id ?>][item_expiry_date]" title="Expiry Date"
                           placeholder="MM/DD/YYYY"
                           value="<?= ($item->expiry_date == '1970-01-01' || $item->expiry_date === '0000-00-00') ? '' : date('m/d/Y', strtotime($item->expiry_date)) ?>"/>
                </td>
                <td class="cell">
                    <input class="cell-input input-block-level" type="text" name="adjustment_items[<?= $item->id ?>][item_unit_cost]"
                           title="Unit Cost" value="<?= number_format($item->unit_cost, 2, '.', '') ?>"/>
                </td>
                <td class="cell">
                    <input class="cell-input item_adj input-block-level" type="number"
                           name="adjustment_items[<?= $item->id ?>][item_adj]" title="adjusted quantity"
                           onchange="adjustRow(this)" value="<?= $item->adj_qty ?>"/>
                </td>
                <td class="cell">
                <!-- Modified by julz -->
                    <input class="cell-input item_adj_result input-block-level" type="number"
                           name="adjustment_items[<?= $item->id ?>][item_adj_result]"
                           title="Resulting quantity after adjustment" onchange="adjustRow2(this)"
                           value="<?= $item->adj_qty - $item->orig_qty ?>"/>
                <!-- End -->
                </td>
                <td class="cell">
                    <?php echo CHtml::dropDownList('adjustment_items[' . $item->id . '][item_reason]', $item->reason, $this->getReasons(), array(
                        'class' => 'cell-input input-block-level',
                        'title' => 'Reason for adjustment'
                    )); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php
    $this->endWidget();/*SegBox*/
    $this->endWidget();/*form*/
    ?>
</div><!-- form -->
<script id="new_item" type="text/x-tmpl">
    <tr data-id="{{item_id}}" data-skuid="{{sku_id}}">
        <td><a href="#" onclick="deleteRow(this)"><i class="fa fa-times-circle fa-lg"></i></a></td>
        <td>
            {{item_id}}
            <input type="hidden" name="item_id[]" value="{{item_id}}"/>
            <input type="hidden" name="item_sku_id[]" value="{{sku_id}}"/>
        </td>
        <td>
            {{item_name}}
            <input type="hidden" name="item_name[]" value="{{item_name}}"/>
        </td>
        <td>
            {{item_remaining}}
            <input class="item_remaining" type="hidden" name="item_remaining[]" value="{{item_remaining}}"/>
        </td>
        <td class="cell">
            <?php echo CHtml::dropDownList(
                "item_unit[]",
                '',
                CMap::mergeArray(
                    array('- SELECT -' => ''),
                    CHtml::listData($units, 'unit_id', 'unit_name')
                ),array(
                'class' => 'cell-input input-block-level',
            )); ?>
        </td>
        <td class="cell">
            <input class="cell-input input-block-level" type="text" name="item_serial_no[]" title="Serial number (for equipment)"value="{{item_serial_no}}" />
        </td>
        <td class="cell">
            <input class="cell-input input-block-level" type="text" name="item_lot_no[]" title="Lot number (for inventory tracking)" value="{{item_lot_no}}"/>
        </td>
        <td class="cell">
            <input
                class="cell-input datetime input-block-level"
                type="text" name="item_expiry_date[]"
                title="Expiration date for this batch"
                placeholder="MM/DD/YYYY"
                value="{{item_expiry_date}}"
            />
        </td>
        <td class="cell">
            <input class="cell-input input-block-level" type="text" name="item_unit_cost[]" title="Unit cost for this batch"
                   value="{{item_unit_cost}}"/>
        </td>
        <td class="cell">
            <input class="cell-input item_adj input-block-level" type="number" name="item_adj[]" title="The correct quantity for this batch"
                   onchange="adjustRow(this)"/>
        </td>
        <td class="cell">
            <input class="cell-input item_adj_result input-block-level" type="number" name="item_adj_result[]"
                   title="Resulting quantity after adjustment" onchange="adjustRow2(this)" value="{{item_adj_result}}"/>
        </td>
        <td class="cell">
            <?php echo CHtml::dropDownList('item_reason[]', '', $this->getReasons(), array(
                'class' => 'cell-input input-block-level',
                'title' => 'Reason of Adjustment'
            )); ?>
        </td>
    </tr>
</script>
<script id="search-item" type="text/x-tmpl">
    <tr>
        <td>{{code}}</td>
        <td>{{name}}</td>
        <td>{{quantity}}</td>
        <td>{{unit}}</td>
        <td>{{serial}}</td>
        <td>{{lot}}</td>
        <td>{{expiryDate}}</td>
        <td>{{cost}}</td>
        <td>
            <button type="button" class="btn btn-primary" onclick="addToTray({{index}})">
                <i class="fa fa-plus"></i>
            </button>
        </td>
    </tr>
</script>

<?php

    $this->beginWidget(
        'bootstrap.widgets.TbModal',
        array('id' => 'modal-add-items',
            'htmlOptions' => array(
                'style' => 'width:900px;margin-left:-450px;'
            ),
            'events' => array(
                'shown' => "js:function(){ $('#search-item-field').focus(); $('#search-item-area').val($('#Adjustment_area_code').val()); $('#form-search-item').submit(); }"
            )
        )
    );
?>
    <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h4><i class="fa fa-search"></i> Search Items to Add</h4>
    </div>
    <div class="modal-body">
        <div id="search-message" class="alert in fade alert-error" style="display: none;">
            <span></span>
            <a class="close" data-dismiss="alert" href="#">&times;</a>
        </div>
        <form id="form-search-item" class="form-search">
            <input type="text" id="search-item-field" name="q" class="input-large search-query" placeholder="Type an item name">
            <input type="hidden" id="search-item-area" name="area" value="">
            <button type="submit" class="btn" id="btn-search"><i class="fa fa-search"></i> Search</button>
        </form>
        <?php $this->actionSearchGrid('', key($areas)); ?>
<!--        <table class="table table-hover table-bordered">-->
<!--            <thead>-->
<!--            <tr>-->
<!--                <th width="10%">Item #</th>-->
<!--                <th width="*">Name</th>-->
<!--                <th width="10%">Qty</th>-->
<!--                <th width="10%">Unit</th>-->
<!--                <th width="10%">Serial #</th>-->
<!--                <th width="10%">Lot #</th>-->
<!--                <th width="10%">Expiry Date</th>-->
<!--                <th width="10%">Unit Cost</th>-->
<!--                <th width="5%"></th>-->
<!--            </tr>-->
<!--            </thead>-->
<!--            <tbody id="search-results"></tbody>-->
<!--        </table>-->
    </div>
<?php $this->endWidget(); ?>