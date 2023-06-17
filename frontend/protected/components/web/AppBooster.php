<?php

/**
 * Booster.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2016, Segworks Tecnolggies Corporation
 */

namespace SegHEIRS\components\web;

\Yii::import('bootstrap.components.Booster');

/**
 * Overrides yii-booster to load our own assets
 */

class AppBooster extends \Booster
{

    /**
     * Override the default YiiBooster package locations
     *
     */
    protected function addOurPackagesToYii() {
        parent::addOurPackagesToYii();

        // Load packages
        $packages = require(__DIR__.DIRECTORY_SEPARATOR.'packages.php');
        foreach ($packages as $name => $definition) {
            $this->cs->addPackage($name, $definition);
        }
    }

}
