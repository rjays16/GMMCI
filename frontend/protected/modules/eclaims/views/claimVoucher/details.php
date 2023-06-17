<?php
/**
 *
 * @author  Ma. Dulce O. Polinar  <dulcepolinar1010@gmail.com> 
 * @copyright (c) 2014, Segworks Technologies Corporation
 *
 */

$voucher = Voucher::model()->findByAttributes(array('voucher_no'=>$voucherNo));
if(empty($voucher->voucherCharge)){
	$voucher = Voucher::getDetails($voucherNo);
}

if(!empty($claimId)){
	$charge = VoucherCharge::model()->findAllByAttributes(array('voucher_id'=>$voucher->id, 'claim_id'=>$claimId));
}
else{
	$charge = VoucherCharge::model()->findAllByAttributes(array('voucher_id'=>$voucher->id));	
}
$gridDataProvider = new CArrayDataProvider($charge);
$gridColumns = array(
	array(
		'name'=>'PayeeDetails',
		'header'=>'Payee Details',
		'type'=>'raw',
		'value'=>function($data){
		        return CHtml::tag('div', array('title'=>'Payee name, type and code', 'data-toggle'=>'tooltip'), $data['PayeeDetails']);
		},
	),
	array(
		'name'=>'rmbd',
		'header'=>'RMBD',
		'type' => 'raw',
		'htmlOptions'=>array('style' => 'text-align: right;'),
		'value'=>function($data){
		        return CHtml::tag('div', array('title'=>'Room and Board', 'data-toggle'=>'tooltip'), number_format($data['rmbd'], 2, '.', ','));
		},
	),
	array(
		'name'=>'xray',
		'header'=>'XRAY',
		'type' => 'raw',
		'htmlOptions'=>array('style' => 'text-align: right;'),
		'value'=>function($data){
		        return CHtml::tag('div', array('title'=>'X-Ray, Laboratory and Other Fees', 'data-toggle'=>'tooltip'), number_format($data['xray'], 2, '.', ','));
		},
	),
	array(
		'name'=>'drugs',
		'header'=>'DRUGS',
		'type' => 'raw',
		'htmlOptions'=>array('style' => 'text-align: right;'),
		'value'=>function($data){
		        return CHtml::tag('div', array('title'=>'Drugs and Medicine Fees', 'data-toggle'=>'tooltip'), number_format( $data['drugs'], 2, '.', ','));
		},
	),
	array(
		'name'=>'oprm',
		'header'=>'OPRM',
		'type' => 'raw',
		'htmlOptions'=>array('style' => 'text-align: right;'),
		'value'=>function($data){
		        return CHtml::tag('div', array('title'=>'Operating Room Fees', 'data-toggle'=>'tooltip'), number_format( $data['oprm'], 2, '.', ','));
		},
	),
	array(
		'name'=>'spfee',
		'header'=>'SPFEE',
		'type' => 'raw',
		'htmlOptions'=>array('style' => 'text-align: right;'),
		'value'=>function($data){
		        return CHtml::tag('div', array('title'=>'Specialist Fees', 'data-toggle'=>'tooltip'), number_format( $data['spfee'], 2, '.', ','));
		},
	),
	array(
		'name'=>'gpfee',
		'header'=>'GPFEE',
		'type' => 'raw',
		'htmlOptions'=>array('style' => 'text-align: right;'),
		'value'=>function($data){
		        return CHtml::tag('div', array('title'=>'General Practitioner Fees', 'data-toggle'=>'tooltip'), number_format( $data['gpfee'], 2, '.', ','));
		},
	),
	array(
		'name'=>'anesfee',
		'header'=>'ANESFEE',
		'type' => 'raw',
		'htmlOptions'=>array('style' => 'text-align: right;'),
		'value'=>function($data){
		        return CHtml::tag('div', array('title'=>'Anesthesiologist Fees', 'data-toggle'=>'tooltip'), number_format( $data['anesfee'], 2, '.', ','));
		},
	),
	array(
		'name'=>'surfee',
		'header'=>'SURFEE',
		'type' => 'raw',
		'htmlOptions'=>array('style' => 'text-align: right;'),
		'value'=>function($data){
		        return CHtml::tag('div', array('title'=>'Surgeon Fee', 'data-toggle'=>'tooltip'), number_format( $data['surfee'], 2, '.', ','));
		},
	),
	array(
		'name'=>'gross_amount',
		'header'=>'GROSS AMOUNT',
		'htmlOptions'=>array('style'=>' text-align: right;'),
		'type' => 'raw',
		'value'=>function($data){
		        return CHtml::tag('div', array('title'=>'Gross Amount', 'data-toggle'=>'tooltip'), number_format( $data['gross_amount'], 2, '.', ','));
		},
	),
	array(
		'name'=>'tax_amount',
		'header'=>'TAX AMOUNT',
		'htmlOptions'=>array('style'=>' text-align: right;'),
		'type' => 'raw',
		'value'=>function($data){
		        return CHtml::tag('div', array('title'=>'Tax Amount', 'data-toggle'=>'tooltip'), number_format( $data['tax_amount'], 2, '.', ','));
		},
	),
	array(
		'name'=>'net_amount',
		'header'=>'NET AMOUNT',
		'htmlOptions'=>array('style'=>' text-align: right;'),
		'type' => 'raw',
		'value'=>function($data){
		        return CHtml::tag('div', array('title'=>'Net Amount', 'data-toggle'=>'tooltip'), number_format( $data['net_amount'], 2, '.', ','));
		},
	),
);
$this->widget(
	'bootstrap.widgets.TbGridView',
	array(
		'dataProvider' => $gridDataProvider,
		'template' => "{items}",
		'type' => 'striped bordered condensed',
		'columns' => $gridColumns,
	)
);