<?php

class InventoryModule extends WebModule
{

    public function init() {
        Yii::import('inventory.models.*');
    }

	public function beforeControllerAction($controller, $action)
	{
        return parent::beforeControllerAction($controller, $action);
    }
}
