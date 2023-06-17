<?php

use SegHis\modules\eclaims\models\ConfigGlobal;

Yii::import('eclaims.models.Eligibility');
Yii::import('eclaims.models.EligibilityDocument');
Yii::import('eclaims.models.EclaimsPerson');
Yii::import('eclaims.models.EclaimsEncounter');
header('Content-Type: text/html; charset=utf-8');
/**
 *
 * EligibilityController.php
 *
 * @author Ma. Dulce Amor O. Polinar <dulcepolinar1010@gmail.com>
 * @copyright  (c) 2014, Segworks Technologies Corporation (http://www.segworks.com)
 */

/**
 *
 * @package eclaims
 */
class EligibilityController extends Controller
{
    public $template = array('select');

    /**
     *
     * @return type
     */
    public function filters()
    {
        return array(
            'accessControl',
            array('bootstrap.filters.BootstrapFilter'),
        );
    }

    /**
     *
     */
    public function accessRules()
    {
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
    public function beforeAction($action)
    {
        $this->breadcrumbs['Eligibility'] = array('eligibility/index');
        return parent::beforeAction($action);
    }

    /**
     *
     */
    public function actionIndex()
    {
        if (!empty($_GET['id'])) {
            $encounter = EclaimsEncounter::model()->findByPk($_GET['id']);

            $person = $encounter->person;

            if (empty($encounter)) {
                $person = EclaimsPerson::model()->findByPk($_GET['id']);
                $encounter = $person->recentEncounterInsurance;
            }
        }

        if (!$person) {
            $person = new EclaimsPerson;
        }
        $person->name_middle = mb_convert_case($person->name_middle, MB_CASE_UPPER, "UTF-8");
        $person->name_last = mb_convert_case($person->name_last, MB_CASE_UPPER, "UTF-8");
        $person->name_first = mb_convert_case($person->name_first, MB_CASE_UPPER, "UTF-8");

        if (($_GET['ajax'] == 'encounter-search-grid')) {

            $this->widget('eclaims.widgets.eclaims.EncounterList', array(
                'pid' => $person->pid,
                'encounterNo' => $_GET['Encounter']['encounter_nr'],
                'active' => false,
                'template' => $this->template,
                'phic' => true
            ));
        } else {
            $this->render('index', array(
                'person' => $person,
                'encounter' => $encounter,
                'template' => $this->template

            ));
        }
    }

    public function actionGetEncounterData()
    {
        $person = EclaimsPerson::model()->findbyPK($_POST['id']);

        echo CJSON::encode(
            array(
                'encounter' => $person->recentEncounterInsurance->encounter_nr
            )
        );
    }

    /**
     * @param string $id
     */
    public function actionPrint($id)
    {
        $this->layout = false;
        Yii::import('eclaims.models.EclaimsEncounter');
        $encounter = EclaimsEncounter::model()->findbyPk($id);
        $config = new ConfigGlobal();

        $hciName = $config->findByAttributes(
            array(
                'type' => 'hie_service_hospital_name'
            )
        )->value;


        $hciCode = $config->findByAttributes(
            array(
                'type' => 'ph_inst_code'
            )
        )->value;


        $this->render('print', array(
            'encounter' => $encounter,
            'hciName' => $hciName,
            'hciCode' => $hciCode
        ));
    }

    /**
     * This function sends the encounter's data to PhilHealth to (initial) verify a member's eligibility.
     * It also saves or updates the eligibility info (and documents) of the member in the database.
     *
     * Errors and its Meanings:
     * - pPIN does not exist : Character length is small or too long(invalid)
     *
     */
    public function actionVerify()
    {

        Yii::import('eclaims.services.ServiceExecutor');
        Yii::import('eclaims.models.EclaimsPerson');

        // Mod by Jeff 03-02-18 for recent encounter used by user.
        //        $person = EclaimsPerson::model()->findByPk($_GET['id']);

        $encounter = new EclaimsEncounter();
        $model = $encounter->findByPk($_GET['id']);
        $params = Eligibility::compact($model);
        $params['pIsFinal'] = intval($_GET['is_final']);


        $service = new ServiceExecutor(
            array(
                'endpoint' => 'hie/eligibility/check',
                'params' => $params
            )
        );


        try {

            $result = $service->execute();

            if ($result['success']) {

                $eligibility = Eligibility::extractResult($model->encounter_nr, $result, $params['pIsFinal']);
                if ($eligibility) {

                    if (empty($eligibility->is_eligible))
                        Yii::app()->user->setFlash('warning', '<strong>Patient is not eligible!</strong> Please see the details below');


                    Yii::app()->user->setFlash('success', '<strong>Success!</strong> Eligibility information successfully updated');
                } else {
                    Yii::app()->user->setFlash('error', '<strong>Error!</strong> Failed to save eligibility information');
                }
            } else {
                Yii::app()->user->setFlash('error', '<strong>Error!</strong> ' . $result['message']);
            }
        } catch (ServiceCallException $e) {
            $result = $e->getData();
            Yii::app()->user->setFlash(
                'error',
                '<strong>' . $e->getMessage() . ':</strong> ' .
                @$result['reason'] .
                ' <small>(Code:' . $e->statusCode . ')</small>'
            );
        }
        $this->redirect(array('index', 'id' => $_GET['id']));
    }
}
