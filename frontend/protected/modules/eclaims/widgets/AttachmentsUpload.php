<?php

Yii::import('bootstrap.widgets.TbFileUpload');
class AttachmentsUpload extends TbFileUpload 
{

	/**
	 * @var $extra[] array
	 * Extra data.
	 */
	public $extra;

	public function init() {
		parent::init();
	}
	
	public function run() {
		parent::run();
	}

}