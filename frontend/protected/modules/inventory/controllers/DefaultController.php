<?php

Yii::import('inventory.components.SkuInventory');

class DefaultController extends Controller
{
    /**
    * @see CController::filters
    */
    public function filters() {
        return array(
            array('bootstrap.filters.BootstrapFilter')
        );
    }

	public function actionIndex()
	{
		$this->render('index');
	}

    public function actionTestAdd() {
        $skuInventory = new SkuInventory();
        $skuInventory->addInventory(1002, 1, 12, 1, NULL, 100, '2014-10-01', 100, '2014-12-03', '1235', '123G');

    }
}