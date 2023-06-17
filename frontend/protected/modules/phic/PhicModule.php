<?php
class PhicModule extends WebModule {
    public $defaultController='default';

    public function beforeControllerAction($controller, $action)
	{
        return parent::beforeControllerAction($controller, $action);
	}
}