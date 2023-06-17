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
$summary = $voucher->Summary;
$gridDataProvider = new CArrayDataProvider($summary, array('keyField'=>false) );
$gridColumns = array(
	array(
		'name'=>'payee_name',
		'header'=>'Payee Details',
		'type'=>'raw',
		'value'=>function($data){
	        return CHtml::tag('div', array('title'=>'Payee name and code', 'data-toggle'=>'tooltip'), $data['payee_name']);
	    },
	),
	array(
		'header'=>'RMBD',
		'name'=>'sum.rmbd',
		'type'=>'raw',
		'htmlOptions'=>array('style' => 'text-align: right;'),
		'value'=>function($data){
	        return CHtml::tag('div', array('title'=>'Room and Board Fees', 'data-toggle'=>'tooltip'), number_format($data['sum']['rmbd'], 2, '.', ','));
	    },
	),
	array(
		'header'=>'OPRM',
		'name'=>'sum.oprm',
		'type'=>'raw',
		'htmlOptions'=>array('style' => 'text-align: right;'),
		'value'=>function($data){
	        return CHtml::tag('div', array('title'=>'Operating Room Fees', 'data-toggle'=>'tooltip'), number_format($data['sum']['oprm'], 2, '.', ','));
	    },
	),
	array(
		'header'=>'XRAY',
		'name'=>'sum.xray',
		'type'=>'raw',
		'htmlOptions'=>array('style' => 'text-align: right;'),
		'value'=>function($data){
	        return CHtml::tag('div', array('title'=>'X-Ray, Laboratories and Other Fees', 'data-toggle'=>'tooltip'), number_format($data['sum']['xray'], 2, '.', ','));
	    },
	),
	array(
		'header'=>'DRUGS',
		'name'=>'sum.drugs',
		'type'=>'raw',
		'htmlOptions'=>array('style' => 'text-align: right;'),
		'value'=>function($data){
	        return CHtml::tag('div', array('title'=>'Drugs and Medicine Fees', 'data-toggle'=>'tooltip'), number_format($data['sum']['drugs'], 2, '.', ','));
	    },
	),
	array(
		'header'=>'GPFEE',
		'name'=>'sum.gpfee',
		'type'=>'raw',
		'htmlOptions'=>array('style' => 'text-align: right;'),
		'value'=>function($data){
	        return CHtml::tag('div', array('title'=>'General Practitioners Fees', 'data-toggle'=>'tooltip'), number_format($data['sum']['gpfee'], 2, '.', ','));
	    },
	),
	array(
		'header'=>'SPFEE',
		'name'=>'sum.spfee',
		'type'=>'raw',
		'htmlOptions'=>array('style' => 'text-align: right;'),
		'value'=>function($data){
	        return CHtml::tag('div', array('title'=>'Specialist Fees', 'data-toggle'=>'tooltip'), number_format($data['sum']['spfee'], 2, '.', ','));
	    },
	),
	array(
		'header'=>'ANESFEE',
		'name'=>'sum.anesfee',
		'type'=>'raw',
		'htmlOptions'=>array('style' => 'text-align: right;'),
		'value'=>function($data){
	        return CHtml::tag('div', array('title'=>'Anesthesiologist Fees', 'data-toggle'=>'tooltip'), number_format($data['sum']['anesfee'], 2, '.', ','));
	    },
	),
	array(
		'header'=>'SURFEE',
		'name'=>'sum.surfee',
		'type'=>'raw',
		'htmlOptions'=>array('style' => 'text-align: right;'),
		'value'=>function($data){
	        return CHtml::tag('div', array('title'=>'Surgeon Fees', 'data-toggle'=>'tooltip'), number_format($data['sum']['surfee'], 2, '.', ','));
	    },
	),
	array(
		'header'=>'Gross Amount',
		'name'=>'sum.gross',
		'type'=>'raw',
		'htmlOptions'=>array('style' => 'text-align: right;'),
		'value'=>function($data){
	        return CHtml::tag('div', array('title'=>'Gross Amount', 'data-toggle'=>'tooltip'), number_format($data['sum']['gross'], 2, '.', ','));
	    },
	),
	array(
		'header'=>'Tax Amount',
		'name'=>'sum.tax',
		'type'=>'raw',
		'htmlOptions'=>array('style' => 'text-align: right;'),
		'value'=>function($data){
	        return CHtml::tag('div', array('title'=>'Tax Amount', 'data-toggle'=>'tooltip'), number_format($data['sum']['tax'], 2, '.', ','));
	    },
	),
	array(
		'header'=>'Net Amount',
		'name'=>'sum.net',
		'type'=>'raw',
		'htmlOptions'=>array('style' => 'text-align: right;'),
		'value'=>function($data){
	        return CHtml::tag('div', array('title'=>'Net Amount', 'data-toggle'=>'tooltip'), number_format($data['sum']['net'], 2, '.', ','));
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
