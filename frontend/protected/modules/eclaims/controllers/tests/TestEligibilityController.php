<?php

Yii::import('eclaims.models.Eligibility');
Yii::import('eclaims.models.EligibilityDocument');

/**
 *
 * EligibilityController.php
 *
 * @author Jolly Caralos <jadcaralos@gmail.com>
 * @copyright  (c) 2014, Segworks Technologies Corporation (http://www.segworks.com)
 */

/**
 *
 * @package eclaims
 */
class EligibilityController extends Controller {

    /**
     *
     * @return type
     */
    public function filters() {
        return array(
            'accessControl',
            array('bootstrap.filters.BootstrapFilter'),
        );
    }

    /**
     *
     */
    public function accessRules() {
        return array(
            array(
                'deny',
                'actions' => array('index'),
                'users' => array('?')
            ),
            array(
                'deny',
                'expression' => '!Yii::app()->user->checkPermission("eclaims")',
            ),
            array(
                'deny',
                'actions' => array('verify', 'print'),
                'expression' => '!Yii::app()->user->checkPermission("eligibility_sudomanage")',
            ),
            array(
                'allow',
                'actions' => array('index'),
                'users' => array('@')
            ),
        );
    }

    /**
     *
     * @param type $action
     */
    public function beforeAction($action) {
        $this->breadcrumbs['Eligibility'] = array('eligibility/index');
        return parent::beforeAction($action);
    }

    /* ------------------------ FIXER Functions ------------------------- */
    public function actionVerifyAll() {

        Yii::import('eclaims.services.ServiceExecutor');
        Yii::import('eclaims.models.EclaimsPerson');
        Yii::import('eclaims.models.EclaimsEncounter');

        $criteria = new CDbCriteria;
        $criteria->addCondition("as_of BETWEEN CAST('2014-11-26' AS DATE) AND CAST('2014-12-03' AS DATE)");
        $criteria->addCondition("tracking_number IS NOT NULL OR tracking_number != ''");
        // $criteria->addInCondition('encounter_nr', array(
        //     '2014057526'
        // ));
        $eligibilities = Eligibility::model()->findAll($criteria);

        $response = array();
        echo count($eligibilities);
        echo "<br>";

        $index = 0;
        foreach($eligibilities as $eligibility) {
            $encounter = EclaimsEncounter::model()->findByPk($eligibility->encounter_nr);
            if(!empty($encounter->bill->bill_dte) && empty($eligibility->tracking_number)) {
                // $this->_print($index, $eligibility, $encounter);

                $response[$eligibility->encounter_nr] = $this->_verify($encounter);

                ++$index;
            }
        }
        CVarDumper::dump("REST Response: ", 10, true);
        CVarDumper::dump($response, 10, true);
    }

    private function _print($index, $eligibility, $encounter) {
        echo "-----------------------"  . ($index + 1) . "--------------------------<br>";
        CVarDumper::dump("Encounter: " . $eligibility->encounter_nr, 10, true);
        echo "<br>";
        CVarDumper::dump("PID: " . $encounter->pid, 10, true);
        echo "<br>";
        CVarDumper::dump("Discharge Date: " . $encounter->bill->bill_dte, 10, true);
        echo "<br>";
        CVarDumper::dump("Tracking #: " . $eligibility->tracking_number, 10, true);
        echo "<br>";
        CVarDumper::dump("Documents: ", 10, true);
        foreach($eligibility->document as $doc) {
            CVarDumper::dump('doc name: ' . $doc->name, 10, true);
            echo "<br>";
            CVarDumper::dump('doc reason: ' . $doc->reason, 10, true);
            echo "<br>";
        }
        echo "<br>";
        echo "<br>";
        echo "<br>";
    }

    private function _verify($encounter) {
        $myreply = array();

        $params = Eligibility::compact($encounter);
        $params['pIsFinal'] = 1;
        $service = new ServiceExecutor(
            array(
                'endpoint'=>'hie/eligibility/check',
                'params'=> $params
            )
        );

        try {
            $result = $service->execute();
            $eligibility = Eligibility::extractResult($encounter->encounter_nr, $result, $params['pIsFinal']);
            if ($eligibility) {
                $myreply[] = 'success';

                if(empty($eligibility->is_eligible))
                    $myreply[] = 'not eligible';
                else {
                    $myreply['tracking_number'] = $eligibility->tracking_number;
                }
            } else {
                $myreply[] = 'Failed to save eligibility';
            }
        } catch (ServiceCallException $e) {
            $result = $e->getData();
            $myreply['error'] = @$result['reason'];
        }

        return $myreply;
    }

}
