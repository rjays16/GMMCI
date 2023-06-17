<?php

/**
 * RestFilter.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2017, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\components\web;

use CFilter;
use Yii;

/**
 *
 * Description of RestFilter
 *
 */

class RestFilter extends CFilter
{

    /**
     * @inheritdoc
     */
    protected function preFilter($filterChain)
    {
        Yii::app()->setComponent('errorHandler', [
            'class' => RestErrorHandler::class
        ]);
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function postFilter($filterChain)
    {
    }

}
