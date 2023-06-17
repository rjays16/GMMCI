<?php

/**
 * @author Nick B. Alcala 8-22-2015
 * Class DateTimePickerSlider
 */
class DateTimePickerSlider extends CInputWidget{

    /**
     * @var TbActiveForm when created via TbActiveForm.
     * This attribute is set to the form that renders the widget
     * @see TbActionForm->inputRow
     */
    public $form;

    /**
     * @var array the options for the Bootstrap JavaScript plugin.
     */
    public $options = array();

    /**
     * @var string[] the JavaScript event handlers.
     */
    public $events = array();

    public function init()
    {
        $this->htmlOptions['type'] = 'text';
        $this->htmlOptions['autocomplete'] = 'off';
    }

    public function run()
    {
        list($name, $id) = $this->resolveNameID();

        if ($this->hasModel()) {
            if ($this->form) {
                echo $this->form->textField($this->model, $this->attribute, $this->htmlOptions);
            } else {
                echo CHtml::activeTextField($this->model, $this->attribute, $this->htmlOptions);
            }

        } else {
            echo CHtml::textField($name, $this->value, $this->htmlOptions);
        }

        $this->registerClientScript();
        $options = !empty($this->options) ? CJavaScript::encode($this->options) : '';

        ob_start();
        echo "jQuery('#{$id}').datetimepicker({$options})";
        foreach ($this->events as $event => $handler) {
            echo ".on('{$event}', " . CJavaScript::encode($handler) . ")";
        }

        Yii::app()->getClientScript()->registerScript(__CLASS__ . '#' . $this->getId(), ob_get_clean() . ';');

    }

    public function registerClientScript()
    {
        $baseUrl = Yii::app()->request->baseUrl;
        /* @var $cs CClientScript */
        $cs = Yii::app()->clientScript;
        $cs->registerScriptFile($baseUrl . '/js/jquery/ui/jquery-ui-1.9.1.js');
        $cs->registerScriptFile($baseUrl . '/js/jquery/jquery.number_format.js');
        $cs->registerScriptFile($baseUrl . '/js/jquery/jquery.datetimepicker/jquery-ui-timepicker-addon.js');
        $cs->registerScriptFile($baseUrl . '/js/jquery/jquery.datetimepicker/jquery-ui-sliderAccess.js');
    }

}