<?php

Yii::import('bootstrap.widgets.TbActiveForm');

class PhicActiveForm extends TbActiveForm
{

    protected function customFieldRowInternal(&$fieldData, &$model, &$attribute, &$rowOptions)
    {
        $html = parent::customFieldRowInternal($fieldData, $model, $attribute, $rowOptions);
        if(!$this->enableAjaxValidation && !$this->enableClientValidation){
            $id = CHtml::activeId($model,$attribute);
            $html .= sprintf('<div class="help-block error" id="%s_em_" style="display:none"></div>',$id);
        }
        return $html;
    }

}