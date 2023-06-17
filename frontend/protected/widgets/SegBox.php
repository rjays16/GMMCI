<?php

/**
 * 
 * SegBox.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2005-2014, Segworks Technologies Corporation
 */

Yii::import('bootstrap.widgets.TbBox');
        
/**
 * Extension of Yii-booster's TBBox widget. Provides additional widget
 * features.
 *
 * @package application.widgets
 */
class SegBox extends TbBox {
    
    /**
     *
     * @var string
     */
    public $footer = false;
    
    /**
     *
     * @var array
     */
    public $htmlFooterOptions = array();
    
    /**
     * Widget initialization
     */
    public function init() {
        if (isset($this->htmlFooterOptions['class'])) {
			$this->htmlFooterOptions['class'] = 'bootstrap-widget-footer ' . $this->htmlFooterOptions['class'];
		} else {
			$this->htmlFooterOptions['class'] = 'bootstrap-widget-footer';
		}
        parent::init();
    }
    
    /**
     * 
     */

    public function renderContentEnd() {
        parent::renderContentEnd();
        if (!empty($this->footer)) {
            echo CHtml::tag('div', $this->htmlFooterOptions, $this->footer);
        }
	}
}

