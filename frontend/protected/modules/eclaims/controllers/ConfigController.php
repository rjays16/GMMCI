<?php

/**
 *
 * ConfigController.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2005-2013, Segworks Technologies Corporation
 */

Yii::import('eclaims.models.HospitalConfigForm');


/**
 * Description of ConfigController
 *
 * @package
 */
class ConfigController extends Controller {

    /**
     * @ee CController::filters
     */
    public function filters() {
        return array(
            'accessControl',
            array('bootstrap.filters.BootstrapFilter')
        );
    }

    /**
     *
     * @param CAction  $action
     * @return boolean
     */
    public function beforeAction($action) {
        $this->breadcrumbs['Service Configuration'] = array('view');
        return parent::beforeAction($action);
    }

    /**
     *
     */
    public function actionUpdate() {
        $config = new HospitalConfigForm;

        if(isset($_POST['ajax']) && $_POST['ajax']==='eclaims-config-form')
        {
            echo CActiveForm::validate($config);
            Yii::app()->end();
        }

        if (isset($_POST['HospitalConfigForm'])) {
            if(CActiveForm::validate($config)){
                   
            }
            

            $config->attributes = $_POST['HospitalConfigForm'];
            try {
                $saveOk = $config->save();
            } catch (CException $e) {
                die('rawr');
                $saveOk = false;
            }

            if ($saveOk) {
                Yii::app()->user->setFlash('success', '<strong>Success!</strong> Service configuration successfully updated!');
                if (!Yii::app()->getRequest()->isAjaxRequest) {
                    $this->redirect(array('view'));
                    exit;
                }
            } else {
                Yii::app()->user->setFlash('error', '<strong>Oops!</strong> Something went wrong. The service configuration was not updated!');
            }
        }

        if (Yii::app()->getRequest()->isAjaxRequest) {
            $this->render('ajax/update', array('model' => $config));
        } else {
            $this->render('update', array('model' => $config));
        }
    }

    /**
     *
     */
    public function actionView() {
        $config = new HospitalConfigForm;
        $this->render('view', array('model' => $config));
    }
}
