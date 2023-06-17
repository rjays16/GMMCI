<?php

/**
 * 
 * ClaimDatePicker.php
 *
 * @author Mary Joy L. Abuyo
 * @copyright
 *
 */

Yii::import('bootstrap.widgets.TbDatePicker');
/**
 * Extension of Yii-booster's TbDatePicker widget. 
 * Provides additional widget feature in date formatting.
 *
 * @package eclaims.widgets
 *
 */

class ClaimDatePicker extends TbDatePicker
{

	/**
     * Widget initialization
     */

	public function init()
	{
		

	     if (!isset($this->options['format']))
  	 	     $this->options['format'] = 'yyyy-mm-dd';
		 
		
		parent::init();
	}
	
}