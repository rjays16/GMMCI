<?php

/**
 *
 * MemberPinController.php
 */

/**
 * Controller for Module 1a (Get PhilHealth Identification  Number)
 *
 * @package eclaims.controllers
 */

namespace {
    header('Content-Type: text/html; charset=utf-8');

    Yii::import('eclaims.models.MemberPinForm');
    Yii::import('eclaims.models.EclaimsPerson');
    Yii::import('eclaims.models.EclaimsPhicMember');
    Yii::import('eclaims.models.Member');
    Yii::import('eclaims.models.EclaimsPhicMember2');
    Yii::import('eclaims.models.EclaimsEncounterInsurance');
    Yii::import('eclaims.services.member.*');

    use SegHis\modules\eclaims\services\member\EncounterInsuranceService;
    use SegHis\modules\eclaims\services\member\MemberService;
    use SegHis\modules\eclaims\models\EclaimsPersonInsurance;
    use SegHis\modules\eclaims\widgets\encounter\EncounterList;

    class MemberController extends Controller
    {

        /**
         * @see CController::filters
         */
        public function filters()
        {
            return array(
                'accessControl',
                array('bootstrap.filters.BootstrapFilter'),
            );
        }

        /**
         * @todo Create GetPIN Actions, to separate accessRules for sudomanage
         *       and view
         */
        public function accessRules()
        {
            return array(
                array(
                    'deny',
                    'actions' => array('index'),
                    'users'   => array('?'),
                ),
                array(
                    'deny',
                    'expression' => '!Yii::app()->user->checkPermission("eclaims")',
                ),
                array(
                    'deny',
                    'actions'    => array('manageInsuranceToBilling'),
                    'expression' => '!Yii::app()->user->checkPermission("member_sudomanage")',
                ),
                array(
                    'allow',
                    'actions' => array('index'),
                    'users'   => array('@'),
                ),
            );
        }

        /**
         *
         * @param CAction $actione
         *
         * @return boolean
         */
        public function beforeAction($action)
        {
            $this->breadcrumbs['Get Member PIN'] = array('member/getPIN');

            return parent::beforeAction($action);
        }

        /**
         * Features
         * - Add Insurance Member Info
         *
         * @params $pid Person::pid
         * @params $_POST['EclaimsPhicMember2']
         *
         *
         * @author Gerard Baluyot
         */
        public function actionSaveMemberInfo()
        {

            $transaction = Yii::app()->db->beginTransaction();
            try {

                $pid = $_POST['pid'];

                $postData = $_POST;

                $person = EclaimsPerson::model()->findByPk($pid);

                $service = new MemberService($person);


                $service->saveMember($postData['EclaimsPhicMember2']);


                $transaction->commit();

                Yii::app()->user->setFlash(
                    'success',
                    '<b>Great!</b> The member information was successfully saved!'
                );

                echo CJSON::encode(true);
                Yii::app()->end();
            } catch (\Exception $e) {

                echo CJSON::encode(array('message' => $e->getMessage()));
                Yii::app()->end();
            }
        }

        /**
         * Features
         * - Add insurance to the billing
         *
         * @params $pid Person::pid
         * @params $action
         * For now, this can be "add" or "remove"
         *
         * @author Jolly Caralos
         */
        public function actionManageInsuranceToBilling()
        {
            Yii::import('eclaims.models.EclaimsEncounter');
            Yii::import('eclaims.models.EclaimsPerson');
            Yii::import('eclaims.models.EclaimsPhicMember');
            Yii::import('eclaims.models.EclaimsEncounterInsurance');
            Yii::import('eclaims.models.MemberPinForm');
            Yii::import('phic.models.PhicMember');

            $request = Yii::app()->getRequest();

            $pid = $request->getQuery('pid');

            $action = $request->getQuery('action');
            $selectedEnc = $request->getQuery(
                'encounter'
            );  // added by JOY @ 02-23-2018
            $reasonSelected = $request->getQuery('riModalSelect');
            $reasonOthers = $request->getQuery('riModalTextArea');

            if (empty($selectedEnc)) {
                Yii::app()->user->setFlash(
                    'error',
                    '<strong>Error!</strong> Patient Has no Encounter.'
                );
            }

            $encounter = EclaimsEncounter::model()->findbyPk($selectedEnc);


            $service = new EncounterInsuranceService($encounter);

            $person = EclaimsPerson::model()->findByPk($pid);

            $message = 'Failed to remove PhilHealth Insurance to the billing';
            $status = false;

            // added by JOY @ 02-23-2018
            if ($selectedEnc != "") {
                $person->latestEncounter->encounter_nr = $selectedEnc;
            }

            try {
                if ($person) {
                    if ($encounter->bill->is_final) {
                        Yii::app()->user->setFlash(
                            'error',
                            '<strong>Error!</strong> Attached insurance for encounter cannot be removed. Encounter already has final bill.'
                        );
                        throw new \CException(
                            'Encounter already has final bill',
                            403
                        );
                    }
                    switch ($action) {
                        case 'add':
                            $service->addInsurance();
                            Yii::app()->user->setFlash(
                                'success',
                                '<strong>Success!</strong> PhilHealth Insurance was finally added to the billing.'
                            );
                            break;
                        case 'remove':
                            $service->removeInsurance(
                                $reasonSelected,
                                $reasonOthers
                            );


                            Yii::app()->user->setFlash(
                                'success',
                                '<strong>Success!</strong> Successfully removed PhilHealth Insurance to the billing.'
                            );
                            echo CJSON::encode(array('bool' => true));
                            Yii::app()->end();
                            break;
                        default:
                            echo CJSON::encode(true);
                            # Do nothing
                            break;
                    }
                } else {

                    Yii::app()->user->setFlash(
                        'error',
                        '<strong>Error!</strong> Person record not found!'
                    );
                }
            } catch (\Exception $e) {

                Yii::app()->user->setFlash('error', $e->getMessage());
            }

            $redirectUrl = $this->createUrl('getPin', array('pid' => $pid));
            $this->redirect($redirectUrl);
        }

        /**
         * Action Getpin: saves the input from the form
         * Features:
         * - Get PIN via web service call so HITP
         * - Save Member's PIN and other Data
         */
        public function actionGetPin()
        {
            Yii::import('eclaims.models.MemberPinForm');
            Yii::import('eclaims.models.EclaimsPerson');
            Yii::import('eclaims.models.EclaimsPhicMember');
            Yii::import('eclaims.models.Member');
            Yii::import('eclaims.models.EclaimsPhicMember2');
            Yii::import('eclaims.models.EclaimsEncounterInsurance');
            Yii::import('phic.models.PhicMember');

            $request = Yii::app()->getRequest();
            $pid = $request->getQuery('pid');

            $model = new MemberPinForm;

            if (
                isset($_POST['pid']) && isset($_POST['tab'])
                && $_POST['tab'] == 'patient'
            ) {
                $pid = $_POST['pid'];
            }

            if ($pid) {
                $person = EclaimsPerson::model()->findByPk($pid);
            }

            if (empty($person)) {
                $person = new EclaimsPerson;
            }

            if (empty($person->latestEncounter)) {
                $person->latestEncounter = new EclaimsEncounter;
            }

            if (empty($person->phicMember2)) {
                $person->phicMember2 = new EclaimsPhicMember2;
                $person->phicMember2->pid = $person->pid;
            }

            /* suffix */
            if (!empty($person->phicMember2->suffix)) {
                $suffixReplace = str_replace(
                    ".",
                    "",
                    $person->phicMember2->suffix
                );
                $person->phicMember2->suffix = strtoupper($suffixReplace);
            }

            if (
                $person->phicMember2->member_mname == ''
                && ($person->phicMember2->member_fname != ''
                    && $person->phicMember2->member_lname != '')
            ) {
                $person->phicMember2->member_mname = '.';
            }

            /* Perform ajax validation */
            if (isset($_POST['ajax']) && $_POST['ajax'] === 'member-form') {
                echo CActiveForm::validate($person->phicMember2);
                Yii::app()->end();
            }

            if (
                isset($_POST['EclaimsPhicMember2'])
                && is_array($_POST['EclaimsPhicMember2'])
            ) {
                if (!$person->isNewRecord) {
                    $person->phicMember2->attributes
                        = $_POST['EclaimsPhicMember2'];
                    if (!empty($person->phicMember2->birth_date)) {
                        $birthDate = \DateTime::createFromFormat(
                            'm/d/Y',
                            $person->phicMember2->birth_date
                        );
                        $person->phicMember2->birth_date
                            = $birthDate->format('Y-m-d');
                    }

                    $phicMember2 = new EclaimsPhicMember2();

                    $memberExists = EclaimsPhicMember2::model()
                        ->findByAttributes(array(
                            'pid'      => $person->pid,
                            'hcare_id' => $person->phicMember2->hcare_id,
                        ));

                    if (empty($memberExists)) {
                        $person->phicMember2->setScenario('insert');
                        $person->phicMember2->setIsNewRecord(true);
                        $person->phicMember2->encounter_nr
                            = $person->latestEncounter->encounter_nr;
                    }

                    #monmon : temporary workaround
                    #updating seg_encounter_memcategory value

                    // Added by johnmel 10-29-2018
                    // update will reflected to the check eligibility
                    $phicMember = new PhicMember();
                    $phicEncounter = $phicMember->findByPk($person->latestEncounter->encounter_nr);
                    $phicSaved = true;

                    if ($phicEncounter) {

                        $phicEncounter->member_fname = $_POST['EclaimsPhicMember2']['member_fname'];
                        $phicEncounter->member_lname = $_POST['EclaimsPhicMember2']['member_lname'];
                        $phicEncounter->member_mname = $_POST['EclaimsPhicMember2']['member_mname'];

                        $phicEncounter->suffix = str_replace(".", "", $_POST['EclaimsPhicMember2']['suffix']);
                        $phicEncounter->suffix = strtoupper($phicEncounter->suffix);

                        $phicEncounter->birth_date = \DateTime::createFromFormat('m/d/Y', $_POST['EclaimsPhicMember2']['birth_date']);
                        $phicEncounter->birth_date = $phicEncounter->birth_date->format('Y-m-d');

                        $phicEncounter->relation = $_POST['EclaimsPhicMember2']['relation'];
                        $phicEncounter->insurance_nr =  $_POST['EclaimsPhicMember2']['insurance_nr'];

                        $phicEncounter->employer_no = $_POST['EclaimsPhicMember2']['employer_no'];
                        $phicEncounter->employer_name = $_POST['EclaimsPhicMember2']['employer_name'];
                        $phicEncounter->member_type =  $_POST['EclaimsPhicMember2']['member_type'];

                        $phicSaved = $phicEncounter->save();
                    }

                    # Added condition by jeff 01-30-18
                    $enc = $person->latestEncounter->encounter_nr;
                    // $is_principal = $person->phicMember2->
                    global $db;
                    $memcode = $_POST['EclaimsPhicMember2']['member_type'];
                    $relation = $_POST['EclaimsPhicMember2']['relation'];
                    if ($relation == 'M') {
                        $db->Execute('UPDATE care_person_insurance set is_principal = 1 WHERE pid='
                            . $pid);
                    } else {
                        $db->Execute(
                            'UPDATE care_person_insurance set is_principal = 0 WHERE pid='
                            . $pid
                        );
                    }

                    #end monmon

                    $transaction = Yii::app()->getDb()->beginTransaction();
                    try {
                        /* Problem pa sa saving.. diretso lang,.. */
                        if ($person->phicMember2->save() && $phicSaved) {
                            $transaction->commit();
                            Yii::app()->user->setFlash(
                                'success',
                                '<b>Great!</b> The member information was successfully saved!'
                            );
                            $this->redirect($this->createUrl(
                                'getPin',
                                array('pid' => $pid)
                            ));
                        } else {
                            throw new CDbException(
                                "Record was not saved!",
                                500
                            );
                        }
                    } catch (CDbException $ex) {
                        $_exception = true;
                    }
                    if (isset($_exception)) {
                        if ($transaction->active) {
                            $transaction->rollback();
                        }
                        $model->addErrors($person->phicMember2->getErrors());
                    }
                } else {
                    throw new CHttpException('Cannot update the member information of a non-existent person!');
                }
            }

            if (isset($_POST['tab'])) {

                if ($_POST['tab'] == 'walkin') {
                    $model->attributes = $_POST['MemberPinForm'];
                } else {
                    $model->attributes = $person->phicMember2->getPinParams();
                }

                if ($model->validate()) {
                    Yii::import('eclaims.services.ServiceExecutor');

                    $service = new ServiceExecutor(
                        array(
                            'endpoint' => 'hie/eligibility/getpin',
                            'params'   => $model->getPinParams(),
                        )
                    );

                    try {
                        $response = $service->execute();
                        if ($response['success']) {
                            Yii::app()->user->setFlash(
                                'success',
                                '<strong>PHIC PIN</strong><br/><h3>'
                                . $response['data'] . '</h3>'
                            );

                            if (
                                $_POST['tab'] !== 'walkin'
                                && $person->phicMember2->pid
                            ) {
                                $person->phicMember2->insurance_nr
                                    = $response['data'];

                                /* Check if New or Update? */
                                $memberExists = EclaimsPhicMember2::model()
                                    ->findByAttributes(array(
                                        'pid'      => $person->pid,
                                        'hcare_id' => $person->phicMember2->hcare_id,
                                    ));

                                if (empty($memberExists)) {
                                    $person->phicMember2->setScenario('insert');
                                    $person->phicMember2->setIsNewRecord(true);
                                    $person->phicMember2->encounter_nr
                                        = $person->latestEncounter->encounter_nr;
                                }
                                $person->phicMember2->save();
                            }
                        } else {
                            Yii::app()->user->setFlash(
                                'error',
                                'Error <strong>'
                                . $response['message'] . '</strong>'
                            );
                        }
                    } catch (ServiceCallException $e) {
                        Yii::app()->user->setFlash(
                            'error',
                            '<strong>Web service error:</strong> '
                            . $e->getMessage()
                        );
                    }
                } else {
                }
            }
            //added by Jasper Ian Q. Matunog 11/25/2014
            $hasFinalBill = $person->activeInsuranceEncounter->bill->is_final
                && is_null($person->activeInsuranceEncounter->bill->is_deleted);

            // added by JOY @ 02-22-2018
            if ($pid) {
                Yii::import('models.Encounter'); // added by JOY @ 02-22-2018
                $encModel = new Encounter;
                $dp = $encModel->getActiveCaseNos($pid, '', null);
                $countActiveEnc = $encModel->getTotalEncounter();
            } // end by JOY


            if (($_GET['ajax'] == 'encounter-search-grid')) {

                $this->widget('eclaims.widgets.eclaims.EncounterList', array(
                    'pid'         => $person->pid,
                    'encounterNo' => $_GET['Encounter']['encounter_nr'],
                    'active'      => true,
                    'template'    => array('add', 'delete'),
                ));
            } else {

                $this->render('getPin', array(
                    'model'          => $model,
                    'person'         => $person,
                    'member'         => $person->phicMember2,
                    'hasFinalBill'   => $hasFinalBill,
                    'countActiveEnc' => $countActiveEnc,
                    // added by JOY @ 02-22-2018
                ));
            }
        }

        /**
         * Ajax action, checks if the relation is "M".
         * If TRUE, return the person data. ElSE null.
         *
         * @param $pid
         * @param $relation
         *
         * @author Jolly Caralos
         */
        public function actionGetPersonData()
        {
            $request = Yii::app()->getRequest();
            $pid = $request->getQuery('pid');
            $relation = $request->getQuery('relation');

            if ($pid) {
                Yii::import('eclaims.models.EclaimsPerson');
                $person = EclaimsPerson::model()->findByPk($pid);

                echo CJSON::encode(array(
                    'member_fname' => $person->getNameFirst(),
                    'member_mname' => $person->name_middle,
                    'member_lname' => $person->name_last,
                    'suffix'       => $person->getSuffix(),
                    'birth_date'   => $person->date_birth,
                    'sex'          => $person->sex,
                    'insurance_nr' => $person->phicMember->insurance_nr,
                ));
            }
            Yii::app()->end();
        }

        public function actionRemoveData()
        {
            echo CJSON::encode($_POST);
        }

        // added by JOY @ 02-21-2018
        public function actionSearchGrid($q = '', $pid = '')
        {
            Yii::import('models.Encounter'); // added by JOY @ 02-22-2018
            $model = new Encounter;
            if ($pid) {
                $dp = $model->getActiveCaseNos($pid, $q, null);
                $countEnc = $model->getTotalEncounter();

                $this->renderPartial(
                    'eclaims.views.common.encounter.list',
                    array(
                        'dataProvider' => $dp,
                        'model'        => $model,
                        'countEnc'     => $countEnc,
                    )
                );
            }
        } // end by JOY

        # Added by jeff 02-26-18 for using SearchEmployer API.
        public function actionGetEmployeeInfo()
        {
            Yii::import('eclaims.services.ServiceExecutor');
            Yii::import('eclaims.models.EclaimsPhicMember2');
            Yii::import('eclaims.models.EclaimsEncounterInsurance');

            $member = new EclaimsPhicMember2;
            $request = Yii::app()->getRequest();
            $cont = 0;
            $info_id = $_POST['pid'];
            $memarr = array('S', 'G');
            $p_pin = $_POST['EclaimsPhicMember2']['patient_pin'];
            $m_type = $_POST['EclaimsPhicMember2']['member_type'];
            $e_no = $_POST['EclaimsPhicMember2']['employer_no'];
            $e_name = $_POST['EclaimsPhicMember2']['employer_name'];

            if ($_POST['EclaimsPhicMember2']['relation'] != 'M') {

                $patientPin = ($_POST['EclaimsPhicMember2']['patient_pin']);
                if (empty($_POST['EclaimsPhicMember2']['patient_pin'])) {

                    echo CJSON::encode(
                        array(
                            'message' => 'Patient Pin is Required',
                            'success' => false,
                        )
                    );
                    die;
                }
                if (strlen($patientPin) < 12) {

                    echo CJSON::encode(
                        array(
                            'message' => 'Patient Pin must be 12 digits',
                            'success' => false,
                        )
                    );
                    die;
                }
            }
            foreach ($memarr as $key => $value) {
                if ($value == $m_type) {
                    $cont++;
                }
            }
            if ($cont > 0) {

                if (strlen($e_no) != 12) {
                    echo CJSON::encode(array(
                        'success' => false,
                        'message' => 'Employer Number must be 12 characters',
                    ));
                    die;
                }

                if ($e_no == '') {
                    echo CJSON::encode(
                        array(
                            'success' => false,
                            'message' => 'Employer No. Must not be blank',
                        )
                    );
                    die;
                }

                if ($e_name == '') {

                    echo CJSON::encode(
                        array(
                            'success' => false,
                            'message' => 'Employer Name must not be blank',
                        )
                    );
                    die;
                } else {
                    $info = EclaimsEncounterInsurance::model()
                        ->UpdateInsuranceInfo($info_id, $p_pin);
                    if (is_null($info)) {
                        echo CJSON::encode(array('success' => true));
                        die;
                    } else {
                        echo CJSON::encode(array('success' => false));
                        die;
                    }
                }
            } else {
                $info = EclaimsEncounterInsurance::model()
                    ->UpdateInsuranceInfo($info_id, $p_pin);
                echo CJSON::encode(
                    array('success' => true)
                );
                die;
            }
        }

        private function getHospitalCode()
        {
            Yii::import('eclaims.models.HospitalConfigForm');
            $configModel = new HospitalConfigForm;
            $hospitalCode = $configModel->hospital_code;

            return $hospitalCode;
        }
    }
}
