<?php

/**
 *
 * PatientController.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2014, Segworks Technologies Corporation
 */

Yii::import('eclaims.models.EclaimsPerson');

/**
 * Description of PatientController
 *
 * @package eclaims.controllers
 */
class PatientController extends Controller {

    /**
     *
     * @return array
     */
    public function filters() {
        return array(
            'accessControl'
        );
    }

    /**
     *
     * @return array
     */
    public function accessRules() {
        return array(
            array(
                'allow',
                'actions' => array('search'),
                'users' => array('@')
            ),
        );
    }

    /**
     *
     * @throws CHttpException
     */
    public function actionIndex() {

    }

    /**
     *
     */
    public function actionInfo() {
        if (isset($_GET['id'])) {
            $person = EclaimsPerson::model()->findByPk($_GET['id']);
            if ($person) {
                echo CJSON::encode($person->getPatientInfo(array('dateFormat' => 'm-d-Y')));
            } else {
                throw new CHttpException(404, 'Patient does not exist');
            }
        } else {
            throw new CHttpException(400, 'Patient ID not specified');
        }
    }

    /**
     *
     */
    public function actionSearch() {
        if(isset($_GET['q'])){
            if (preg_match("/\d+/", $_GET['q'])) {
                $person = EclaimsPerson::model()->findByPk($_GET['q']);
                if ($person) {
                    $persons = array($person);
                } else {
                    $persons = array();
                }
            } else {
                $persons = EclaimsPerson::searchByName($_GET['q']);
            }
            $result=array();
            foreach($persons as $person) {
                $result[] = $person->getPatientInfo();
            }
        } else {
            $result = array();
        }
        echo CJSON::encode($result);
    }

}
