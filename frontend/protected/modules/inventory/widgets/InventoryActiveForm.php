<?php
Yii::import('bootstrap.widgets.TbActiveForm');

/**
 * @author Nick B. Alcala 8-21-2015 :P
 * Class InventoryActiveForm
 */
class InventoryActiveForm extends TbActiveForm {

	public function displayTextFieldRow($model, $displayValue, $hiddenValue, $attribute, $htmlOptions = array(), $rowOptions = array()){

		$tempHtmlOptions = $htmlOptions;
		$tempAttribute = $attribute . '_display';
		CHtml::resolveNameID($model,$tempAttribute,$tempHtmlOptions);

		return $this->textFieldRow($model, $attribute,array_merge($htmlOptions,array(
			'id' => $tempHtmlOptions['id'],
			'name' => $tempHtmlOptions['name'],
			'value' => $displayValue
		)), array_merge($rowOptions,array(
			'append' => $this->hiddenField($model, $attribute, array('value' => $hiddenValue)),
			'appendOptions' => array('isRaw' => true)
		)));

	}

	public function dateTimePickerSlider($model, $attribute, $widgetOptions = array(), $rowOptions = array())
	{
		return $this->widgetRowInternal('inventory.widgets.DateTimePickerSlider', $model, $attribute, $widgetOptions, $rowOptions);
	}

}