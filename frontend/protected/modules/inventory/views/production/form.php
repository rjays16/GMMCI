<?php
/* @var $this ProductionController */
/* @var $model Production */
/* @var $cs CClientScript */
/* @var $area Area[] */
/* @var $unit SegHis\models\inventory\Unit[] */


$isNewRecord = $model->getIsNewRecord();

$baseUrl = Yii::app()->request->baseUrl;

$this->breadcrumbs['Inventory'] = $baseUrl.'/modules/supply_office/seg-supply-functions.php';

if(!$isNewRecord){
    $this->breadcrumbs['List'] = array('production/admin');
    $this->breadcrumbs[] = $model->id;
}

if($isNewRecord)
    $this->breadcrumbs[] = 'New';


$cs = Yii::app()->clientScript;
$cs->registerCss('style', 'body{padding:0;}');
$this->setPageTitle('Production');
$this->showFooter = false;

$cs->registerScriptFile($baseUrl . '/js/jquery/ui/jquery-ui-1.9.1.js');
$cs->registerScriptFile($baseUrl . '/js/jquery/jquery.number_format.js');
$cs->registerScriptFile($baseUrl . '/js/jquery/jquery.datetimepicker/jquery-ui-timepicker-addon.js');
$cs->registerScriptFile($baseUrl . '/js/jquery/jquery.datetimepicker/jquery-ui-sliderAccess.js');

$readyJs = <<<JS

$('.datetime').datetimepicker({
    dateFormat: 'mm/dd/yy',
    timeFormat: 'hh:mm TT',
    changeMonth: true,
    changeYear: true
});

$('#Production_area').on('click',function(){
    lastAreaSelected = $('#Production_area option:selected');
}).on('change',function(){
    if(!resetForm()){
        lastAreaSelected.prop('selected',true);
    }else{
        $('#item-list').empty();
        $('#Production_package_id').select2('val','');
    }
});

$('#btn-add-item').on('click',function(){

    var item_search = $('#item-search');
    var item_list = $('#item-list');
    var item_quantity = $('#add_item_quantity');
    var item_price = $('#add_item_price');
    var item_remaining = $('#add_item_remaining');
    var item_name =$('#add_item_name');
    var production_quantity = $('#Production_quantity');

    if(item_search.val().trim().length == 0)
        return false;

    if(parseInt(item_remaining.val()) < parseInt(item_quantity.val())){
        alert('Not enough stocks.');
        return false;
    }

    if(item_list.find('[value='+item_search.val()+']').length > 0){
        alert("Item is already added in the list.");
        return false;
    }

    var template = Mustache.render($('#add-item-layout').html(),{
        item_name : item_name.val(),
        item_id : item_search.val(),
        item_quantity : item_quantity.val(),
        item_price_formatted : $().number_format(item_price.val(),{numberOfDecimals:2}),
        item_price : item_price.val(),
        item_total_formatted : $().number_format(item_quantity.val() * item_price.val(),{numberOfDecimals:2}),
        item_total : item_quantity.val() * item_price.val()
    });

    item_list.append(template);
});
JS;

$headJs = <<<JS

var lastAreaSelected;

function resetForm(){
    var list = $('#item-list');
    var packageId = $('#Production_package_id');

    if(list.find('tr').length > 0 && packageId.val() != ""){
        if(confirm('This action requires the Items form to reset. Continue?')){
            list.empty();
            packageId.select2('val','');
            return true;
        }else{
            return false;
        }
    }
    return true;
}
function updateInventory(){
    Alerts.confirm({
        title: 'Confirm Action',
        content: 'Are you sure you want to post this to inventory?' +
         '<br/><strong style="color:red;">This action can\'t be undone.' +
          '<br/>Make sure all data is correct and final.</strong>',
        callback: function(result) {
            if(result){
                $('#post-to-inventory').val(1);
                $('#production-form').submit();
            }
        }
    });
}
function updateProductionTotalPrice(){
    var total = $().number_format($('#Production_quantity').val() * $("#Production_unit_price").val(),{numberOfDecimals:2});
    $('#productionTotalPrice').val(total);
}

function deleteItem(e){
    $(e).parent().parent().animate({opacity:0.25},300,'linear',function(){
        $(this).remove();
    });
}

//function updateItemPrice(e){
//    var listItem = $(e).parent().parent();
//    var quantity = $(e).val();
//    var price = listItem.find('.price').val();
//    var total = quantity * price;
//    listItem.find('input.total').val(total);
//    listItem.find('span.total').html($().number_format(total,{numberOfDecimals:2}));
//}

function deleteItemAjax(id,e){
    if(confirm("Are you sure you want to delete this item?")){
        $.ajax({
            url : '{$this->createUrl('production/deleteProductionItem/id')}/'+id,
            dataType : 'json',
            success : function(data){
                if(data.result == true){
                    alert('Item deleted.');
                    deleteItem(e);
                }else{
                    alert('Error deleting item.');
                }
            }
        });
    }
}
JS;

$cs->registerScript('readyJs', $readyJs, CClientScript::POS_READY);
$cs->registerScript('headJs', $headJs, CClientScript::POS_HEAD);

?>
<script type="mustache-template" id="add-item-layout">
    <tr>
        <td style="vertical-align: middle;">
            <span>{{item_name}}</span>
            <input class="name" type="hidden" name="item_id[]" value="{{item_id}}">
        </td>
        <td style="vertical-align: middle;">
            <input class="quantity" type="number" name="item_quantity[]" value="{{item_quantity}}" min="1" readonly>
        </td>
        <td style="vertical-align: middle;">
            <span>{{item_price_formatted}}</span>
            <input class="price" type="hidden" name="item_price[]" value="{{item_price}}">
        </td>
        <td style="vertical-align: middle;">
            <span class="total">{{item_total_formatted}}</span>
            <input class="total" type="hidden" name="item_total[]" value="{{item_total}}">
        </td>
        <td style="vertical-align: middle;">
            <button type="button" class="list button btn btn-danger item-delete" onclick="deleteItem(this);"><i
                    class="fa fa-times"></i></button>
        </td>
    </tr>
</script>

<div class="form">
    <?php
    /* @var InventoryActiveForm $form*/
    $form = $this->beginWidget('inventory.widgets.InventoryActiveForm', array(
        'id' => 'production-form',
        'type' => 'horizontal',
        'enableAjaxValidation' => true,
    ));

    echo $form->errorSummary($model);

    ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php

    echo CHtml::tag('div', array('class' => 'clearfix'));
    echo CHtml::tag('div', array('class' => 'span5'));/*column 1*/

    echo $form->checkBoxRow($model, 'to_smaller', array('disabled' => !$isNewRecord ? 'disabled' : ''));

    if ($isNewRecord) {

        echo $form->dropDownListRow($model, 'area', $area, array(
            'placeholder' => false
        ));

        $url = Yii::app()->createUrl('inventory/production/packages');
        echo $form->select2Row($model, 'package_id', array(
            'asDropDownList' => false,
            'events' => array(
                'select2-open' => 'js:function(){
                    if(!resetForm()){
                        $("#select2-drop-mask").click();
                    }
                }'
            ),
            'options' => array(
                'width' => '220',
                'placeholder' => 'Search package/kit',
                'dataType' => 'json',
                'id' => 'js:function(data){return data.item_id;}',
                'ajax' => array(
                    'url' => $url,
                    'data' => 'js:function(term, page) {
                        return {
                            q: term,
                            area : $("#Production_area").val()
                        };
                    }',
                    'results' => 'js:function(data,page) { return {results: data}; }',
                ),
                'allowClear' => false,
                'escapeMarkup' => 'js:function (markup) { return markup; }',
                'minimumInputLength' => 3,
                'initSelection' => 'js:function(element, callback){
					var id = $(element).val();
					if(id !== "") {
						$.ajax("' . $url . '", {
							data: {id: id},
							dataType: "json"
						}).done(function(data) {
							callback(data);
						});
					}
				}',
                'formatResult' => 'js:function(data, container, query){
                    return "<span class=\'label label-info\'>ID: " + data.item_id + "</span>" +
                           "<span class=\'label label-warning\'><i class=\'fa fa-medkit\'></i> " + data.remaining + "</span><br>" +
                           "<span>"+data.name+"</span>";
                }',
                'formatSelection' => 'js:function(data, container){
                    $("#Production_package_id").val(data.item_id);
                    $("#Production_unit").val(data.unitId);
                    $("#Production_unit_display").val(data.unitDesc);
                    return data.name;
				}'
            )
        ));
    } else {
        echo $form->displayTextFieldRow($model,$model->areaInfo->area_name,$model->area, 'area', array('readonly' => 'readonly'));
        echo $form->displayTextFieldRow($model,$model->package->artikelname,$model->package_id, 'package_id', array('readonly' => 'readonly'));
    }

    echo $form->displayTextFieldRow($model,ucwords($model->unitInfo->unit_desc),$model->unit, 'unit', array('readonly' => 'readonly'));

    echo CHtml::hiddenField('post-to-inventory',0);

    echo $form->textFieldRow($model, 'production_date', array(
        'class' => 'datetime',
        'value' => $isNewRecord ? date('m/d/Y h:i A') : date('m/d/Y h:i A', strtotime($model->production_date)),
    ));

    echo $form->textFieldRow($model, 'expiry_date', array(
        'class' => 'datetime',
        'value' => $isNewRecord ? date('m/d/Y h:i A') : date('m/d/Y h:i A', strtotime($model->expiry_date)),
    ));

    echo CHtml::closeTag('div');/*end - column 1*/

    echo CHtml::tag('div', array('class' => 'span5'));/*column 2*/
    echo $form->textFieldRow($model, 'serial_no');
    echo $form->textFieldRow($model, 'lot_no');
    echo $form->numberFieldRow($model, 'quantity', array('min' => 1, 'onchange' => 'updateProductionTotalPrice()'));
    echo $form->numberFieldRow($model, 'unit_price', array('min' => 1, 'onchange' => 'updateProductionTotalPrice()'));
    ?>
    <div class="control-group">
        <label class="control-label" for="productionTotalPrice">Total Price</label>
        <div class="controls">
            <?=CHtml::textField('productionTotalPrice',number_format($model->quantity * $model->unit_price,2),array('readonly' => 'readonly'))?>
        </div>
    </div>
    <?php
    echo CHtml::closeTag('div');/*end - column 2*/
    echo CHtml::closeTag('div');/*.clearfix*/

    $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType' => 'submit', 'type' => 'success',
        'icon' => 'fa fa-save', 'label' => $isNewRecord ? 'Save' : 'Update',
        'size' => 'large',
        'htmlOptions' => array(
            'class' => 'pull-right',
            'style' => 'margin-right:30px;margin-bottom:30px;'
        )
    ));

    if(!$isNewRecord){
        $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType' => 'button', 'type' => 'warning',
            'icon' => 'fa fa-save', 'label' => 'Update and Post to Inventory',
            'size' => 'large',
            'htmlOptions' => array(
                'onclick' => 'updateInventory()',
                'class' => 'pull-right',
                'style' => 'margin-right:10px;margin-bottom:30px;'
            )
        ));
    }

    $this->beginWidget('application.widgets.SegBox', array(
        'title' => 'Items in this production',
        'headerIcon' => 'fa fa-medkit'
    ));

    ?>

    <div class="form-inline"
         style="padding: 10px; border-radius: 5px; background-color: #F2F2F2; border: 1px solid #dadada;">
        <label for="item-search" style="margin-right: 10px;">Item to add</label>
        <?php
        $url = Yii::app()->createUrl('inventory/production/packages');
        $this->widget('bootstrap.widgets.TbSelect2', array(
            'asDropDownList' => false,
            'name' => 'item-search',
            'options' => array(
                'width' => 400,
                'placeholder' => 'Search package/kit',
                'dataType' => 'json',
                'id' => 'js:function(data){return data.item_id;}',
                'ajax' => array(
                    'url' => $url,
                    'data' => 'js:function(term, page) {
                        return {
                            q: term,
                            area : $("#Production_area").val()
                        };
                    }',
                    'results' => 'js:function(data,page) { return {results: data}; }',
                ),
                'allowClear' => false,
                'escapeMarkup' => 'js:function (markup) { return markup; }',
                'minimumInputLength' => 3,
                'initSelection' => 'js:function(element, callback){
					var id = $(element).val();
					if(id !== "") {
						$.ajax("' . $url . '", {
							data: {id: id},
							dataType: "json"
						}).done(function(data) {
							callback(data);
						});
					}
				}',
                'formatResult' => 'js:function(data, container, query){
					return "<span class=\'label label-info\'>ID "+ data.item_id +"</span>" +
					       "<span class=\'label label-warning\'><i class=\'fa fa-medkit\'></i> " + data.remaining + "</span><br/>" +
                           "<span>"+data.name+"</span>";
				}',
                'formatSelection' => 'js:function(data, container){
					$("#item_search").val(data.item_id);
					$("#add_item_name").val(data.name);
					$("#add_item_price").val(data.price);
					$("#add_item_remaining").val(data.remaining);
					$("#display_price").val($().number_format(parseFloat(data.price ? data.price : 0),{numberOfDecimals:2}));
					return data.name;
				}'
            )
        )); ?>

        <?= CHtml::hiddenField('add_item_name') ?>

        <label for="price" style="margin:0 10px 0 10px">Item price</label>
        <?= CHtml::textField('display_price', 1, array('readonly' => 'readonly', 'style' => 'width:80px;')) ?>
        <?= CHtml::hiddenField('add_item_price', 1) ?>
        <?= CHtml::hiddenField('add_item_remaining', 0) ?>

        <label for="item_quantity" style="margin:0 10px 0 10px">Quantity to add</label>
        <?= CHtml::numberField('quantity', 1, array('id' => 'add_item_quantity','min' => 1, 'style' => 'width:80px;margin-right:20px')) ?>

        <?php $this->widget('bootstrap.widgets.TbButton', array(
            'label' => 'Add', 'icon' => 'fa fa-plus',
            'buttonType' => 'button', 'type' => 'info',
            'id' => 'btn-add-item'
        )); ?>

    </div>
    <table class="table table-hover" width="100%">
        <thead>
        <tr>
            <th width="*">Item Name</th>
            <th width="15%">Quantity</th>
            <th width="15%">Price</th>
            <th width="15%">Total</th>
            <th width="15%"></th>
        </tr>
        </thead>
        <tbody id="item-list">
        <?php if (!empty($model->productionItems)) : ?>
            <?php
            /* @var ProductionItem $productionItem */
            foreach ($model->productionItems as $index => $productionItem) : ?>
                <tr>
                    <td style="vertical-align: middle;">
                        <span><?= $productionItem->product->artikelname ?></span>
                        <input class="name" type="hidden" name="product_items[<?= $index ?>][item_id]"
                               value="<?= $productionItem->item_id ?>">
                        <input class="name" type="hidden" name="product_items[<?= $index ?>][id]"
                               value="<?= $productionItem->id ?>">
                    </td>
                    <td style="vertical-align: middle;">
                        <input class="quantity" type="number" name="product_items[<?= $index ?>][item_quantity]"
                               value="<?= $productionItem->quantity ?>" min="1" readonly>
                    </td>
                    <td style="vertical-align: middle;">
                        <span><?= number_format($productionItem->product->price_cash, 2) ?></span>
                        <input class="price" type="hidden" name="product_items[<?= $index ?>][item_price]"
                               value="<?= $productionItem->product->price_cash ?>">
                    </td>
                    <td style="vertical-align: middle;">
                        <span
                            class="total"><?= number_format($productionItem->product->price_cash * $productionItem->quantity, 2) ?></span>
                        <input class="total" type="hidden" name="product_items[<?= $index ?>][item_total]"
                               value="<?= $productionItem->product->price_cash * $productionItem->quantity ?>">
                    </td>
                    <td style="vertical-align: middle;">
                        <button type="button" class="list button btn btn-danger item-delete"
                                onclick="deleteItemAjax('<?=$productionItem->id?>', this);"><i
                                class="fa fa-times"></i></button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
    <?php
    $this->endWidget();/*TbBox*/
    $this->endWidget();/*TbActiveForm*/
    ?>
</div><!-- form -->