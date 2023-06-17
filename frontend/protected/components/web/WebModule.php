<?php

/**
 * WebModule.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2017, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\components\web;

use League\Event\ListenerProviderInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * The base module all the modules should inherit
 *
 */
class WebModule extends \CWebModule
{
    /**
     * @var string
     */
    public $layout='main';

    /**
     * @var array
     */
    protected $features = [];

    /**
     *
     */
    public function init()
    {
        parent::init();

        // Loads the PHP configuration file if it exists
        $configPath = realpath($this->getBasePath().'/config/module.php');
        if (file_exists($configPath)) {
            $config = require($configPath);
            $this->configure($config);
        }

        // Loads the YML configuration file if it exists
        $configPath = realpath($this->getBasePath().'/config/module.yml');
        if (file_exists($configPath)) {
            $config = Yaml::parse(@file_get_contents($configPath));
            $this->configure($config);
        }
    }

    /**
     * Helps the system to set the default homeUrl to root.
     *
     * @param \CController $controller
     * @param \CAction $action
     *
     * @return bool
     *
     * @author Jolly Caralos
     */
    public function beforeControllerAction($controller, $action)
    {
        if (parent::beforeControllerAction($controller, $action)) {
            if (\Yii::app()->defaultController == $this->getId()) {
                $controller->homeUrl = '/' . $this->getId()
                    . '/' . $this->defaultController
                    . '/' . $controller->defaultAction;
            } else {
                $controller->homeUrl = '/' .\Yii::app()->defaultController;
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * Defines the list of listeners registered by this module
     *
     * @return ListenerProviderInterface[]
     */
    public static function getListeners()
    {
        return [];
    }
}
