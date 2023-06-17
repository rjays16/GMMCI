<?php

/**
 *
 * PatientController.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2005-2014, Segworks Technologies Corporation
 */

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
            $person = Person::model()->findByPk($_GET['id']);

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
                $person = Person::model()->findByPk($_GET['q']);
                if ($person) {
                    $persons = array($person);
                } else {
                    $persons = array();
                }
            } else {
                $persons = Person::search($_GET['q']);
            }
            $result=array();
            foreach($persons as $person) {
                if(count($person->encounter) > 0){
                    $is_or=false;
                    if(isset($_GET['or'])){
                        $is_or = true;
                    }
                    $result[] = $person->getPatientInfo(array(),$is_or);
                }
            }
        } else {
            $result = array();
        }
        echo CJSON::encode($result);
    }

    public function actionLatestEncounter($pid)
    {
        $encounter = Encounter::model()->find('pid = :pid ORDER BY encounter_date DESC', array('pid'=>$pid));
        $person = Person::model()->findByPk($pid);
        $data = array(
            'encounter_nr'=>$encounter->encounter_nr,
            'person_name'=>$person->getFullName(),
            'gender'=>$person->sex,
            'age'=>$person->getAge(),
            'address'=>$person->getAddress()
        );
        die(json_encode($data));
    }
}
