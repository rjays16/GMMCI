<?php

/**
 * RestErrorHandler.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2017, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\components\web;

use CErrorEvent;
use CErrorHandler;
use CJSON;
use Exception;
use Yii;

/**
 *
 * Description of RestErrorHandler
 *
 */

class RestErrorHandler extends CErrorHandler
{

    /**
     * Handles the exception.
     * @param Exception $exception the exception captured
     */
    protected function handleException($exception)
    {
        $request = Yii::app()->getRequest();
        if ($request->isAjaxRequest) {
            header("Content-Type: application/json;charset=utf-8");
            http_response_code(200);
            $data = [
                'status' => 'error',
                'message' => $exception->getMessage(),
            ];

            if (YII_DEBUG) {
                $data['trace'] = $exception->getTrace();
            }

            echo CJSON::encode($data);
            Yii::app()->end();
        }

        parent::handleException($exception);
    }
}
