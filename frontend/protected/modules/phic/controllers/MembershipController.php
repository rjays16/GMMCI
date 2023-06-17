<?php

Yii::import('phic.components.IReport');
Yii::import('phic.models.MembershipForm');
Yii::import('phic.models.MemberRelation');
Yii::import('phic.models.PmrfMemberCategory');
Yii::import('phic.models.PmrfDependent');
Yii::import('application.models.address.AddressCountry');

class MembershipController extends Controller
{

    public function filters()
    {
        return array(
            array('bootstrap.filters.BootstrapFilter')
        );
    }

    public function actionValidateForm()
    {
        if ($_POST['MembershipForm']) {
            $model = new MembershipForm;
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    protected function performAjaxValidation($model)
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'membership-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    public function actionRegistration($caseNumber)
    {
        $model = MembershipForm::findByEncounter($caseNumber);
        if (!$model) {
            $model = new MembershipForm;
            $this->setInitialValues($caseNumber, $model);
        } else {
            $model->setScenario('update');
        }

        $this->performAjaxValidation($model);

        if ($_POST['MembershipForm']) {

            if (!isset($_POST['MembershipForm']['pmrfMembershipEffectiveDate']))
                $_POST['MembershipForm']['pmrfMembershipEffectiveDate'] = '';

            $model->setAttributes($_POST['MembershipForm']);

            if($model->isMember)
                $model->cf1Pin = '';
            if($model->pmrfMembershipCategory=='')
                $model->pmrfMembershipCategory = null;

            $model->pmrfDependentsData = self::getPmrfDependents();
            if ($model->validate()) {
                if ($model->save()) {
                    Yii::app()->user->setFlash('success', 'Saved Successfully.');
                    $this->redirect(array('membership/registration/caseNumber/' . $caseNumber));
                }
            }
        }

        $this->render('membershipForm', array(
            'model' => $model,
            'relationOptions' => self::getRelations(),
            'countryOptions' => self::getNationalities(),
            'personInfo' => self::getPersonInfo($caseNumber),
            'pmrfMemberCategoryOptions' => self::getPmrfMemberCategories()
        ));
    }

    public static function getRelations()
    {
        return MemberRelation::model()->findAll();
    }

    public static function getNationalities()
    {
        return AddressCountry::model()->findAll();
    }

    public function getPmrfMemberCategories()
    {
        return PmrfMemberCategory::model()->findAll(array('order' => 'name'));
    }

    public function setInitialValues($caseNumber, MembershipForm &$model)
    {
        /* @var $encounter Encounter */
        $encounter = Encounter::model()->findByPk($caseNumber);
        if (!$encounter)
            die('Patient transaction ' . $caseNumber . ' cannot be found.');
        $model->pid = $encounter->pid;
        $model->encounterNr = $encounter->encounter_nr;
        $model->hcareId = MembershipForm::PHILHEALTH_ID;
        $model->nationality = 'Filipino';
        $model->barangay = trim($encounter->person->barangay->brgy_name);
        $model->municipality = trim($encounter->person->municipality->mun_name);
        $model->province = trim($encounter->person->municipality->parent->prov_name);
        $model->country = trim($encounter->person->country->country_name);
        $model->zipCode = trim($encounter->person->municipality->zipcode);
        $model->setScenario('insert');
    }

    public static function getPersonInfo($encounterNr)
    {
        return MembershipForm::getPersonInfo($encounterNr);
    }

    private static function getPmrfDependents()
    {
        //new
        $data = array();
        $dependents = $_POST['Dependents'];
        if (!empty($dependents)) {
            for ($i = 0; $i < count($dependents['relation']); $i++) {
                $data[] = array(
                    'relation' => $dependents['relation'][$i],
                    'pin' => $dependents['pin'][$i],
                    'first_name' => $dependents['first_name'][$i],
                    'middle_name' => $dependents['middle_name'][$i],
                    'last_name' => $dependents['last_name'][$i],
                    'name_extension' => $dependents['name_extension'][$i],
                    'birth_date' => strtotime($dependents['birth_date'][$i]) ? date('Y-m-d', strtotime($dependents['birth_date'][$i])) : '',
                    'sex' => $dependents['sex'][$i],
                    'is_disabled' => $dependents['is_disabled'][$i],
                );
            }
        }

        $dependents = $_POST['Dependents_o'];
        if (!empty($dependents)) {
            foreach ($dependents as $key => $dependent) {
                $dependent['birth_date'] = strtotime($dependent['birth_date']) ? date('Y-m-d', strtotime($dependent['birth_date'])) : '';
                $data[] = array_merge(array('id' => $key), $dependent);
            }
        }

        return $data;
    }

    public function actionDeleteDependent($id)
    {
        $delete = PmrfDependent::model()->updateByPk($id, array(
            'is_deleted' => 1
        ));
        echo CJSON::encode(array('result' => $delete));
    }

    public function actionPrintCf1($caseNr)
    {
        $model = MembershipForm::findByEncounter($caseNr);
        $iReport = new IReport;
        $iReport->format = IReport::PDF;
        $iReport->template = 'PHIC_CF1';

        $iReport->parameters = self::getCf1PrintParameters($model);
        $iReport->data = self::getCf1PrintData($model);
        $iReport->encoding = 'UTF-8';

        $iReport->show();
    }

    private static function getCf1PrintParameters(MembershipForm $model)
    {

        $personInfo = MembershipForm::getPersonInfo($model->encounterNr);
        $pin = str_replace('-', '', $model->pin);

        /* hahay */
        $memberPin = array(
            'member_pin' => $pin ? $pin[0] : '',
            'member_pin_1' => $pin ? $pin[1] : '',
            'member_pin_2' => $pin ? $pin[2] : '',
            'member_pin_3' => $pin ? $pin[3] : '',
            'member_pin_4' => $pin ? $pin[4] : '',
            'member_pin_5' => $pin ? $pin[5] : '',
            'member_pin_6' => $pin ? $pin[6] : '',
            'member_pin_7' => $pin ? $pin[7] : '',
            'member_pin_8' => $pin ? $pin[8] : '',
            'member_pin_9' => $pin ? $pin[9] : '',
            'member_pin_10' => $pin ? $pin[10] : '',
            'member_pin_11' => $pin ? $pin[11] : '',
        );

        $pen = str_replace('-','',$model->cf1->employer_pen);
        $pen = array(
            'pen' => $pen[0],
            'pen_1' => $pen[1],
            'pen_2' => $pen[2],
            'pen_3' => $pen[3],
            'pen_4' => $pen[4],
            'pen_5' => $pen[5],
            'pen_6' => $pen[6],
            'pen_7' => $pen[7],
            'pen_8' => $pen[8],
            'pen_9' => $pen[9],
            'pen_10' => $pen[10],
            'pen_11' => $pen[11],
        );

        $patientPin = str_replace('-','',$model->cf1Pin);

        $patientFullName = strtr("lastName   firstName   nameExt   middleName",array(
            'lastName' => str_pad(strtoupper($personInfo['nameLast']), 15,"  ",STR_PAD_RIGHT),
            'firstName' => str_pad(strtoupper($personInfo['nameFirst']), 15,"  ",STR_PAD_RIGHT),
            'nameExt' => str_pad(strtoupper($personInfo['nameExtension']), 15,"  ",STR_PAD_RIGHT),
            'middleName' => str_pad(strtoupper($personInfo['nameMiddle']), 15,"  ",STR_PAD_RIGHT),
        ));

        $part2 = !$model->isMember ? array(
            'patient_relation_c' => !$model->isMember && $model->relation == MembershipForm::RELATION_CHILD ? 'X' : '',
            'patient_relation_p' => !$model->isMember && $model->relation == MembershipForm::RELATION_PARENT ? 'X' : '',
            'patient_relation_s' => !$model->isMember && $model->relation == MembershipForm::RELATION_SPOUSE ? 'X' : '',
            
            'patient_name_last' => strtoupper($personInfo['nameLast']),
            'patient_name_first' => strtoupper($personInfo['nameFirst']),
            'patient_suffix' => strtoupper($personInfo['nameExtension']),
            'patient_name_middle' => strtoupper($personInfo['nameMiddle']),
            'patient_fullname' => $patientFullName,

            'patient_gender_m' => $personInfo['sex'] == 'm' ? 'X' : '',
            'patient_gender_f' => $personInfo['sex'] == 'f' ? 'X' : '',
            'patient_birth_date' => MembershipForm::formatDate($personInfo['birthDate']),
            'patient_pin' => $patientPin[0],
            'patient_pin_1' => $patientPin[1],
            'patient_pin_2' => $patientPin[2],
            'patient_pin_3' => $patientPin[3],
            'patient_pin_4' => $patientPin[4],
            'patient_pin_5' => $patientPin[5],
            'patient_pin_6' => $patientPin[6],
            'patient_pin_7' => $patientPin[7],
            'patient_pin_8' => $patientPin[8],
            'patient_pin_9' => $patientPin[9],
            'patient_pin_10' => $patientPin[10],
            'patient_pin_11' => $patientPin[11],
        ) : array();

        $part3 = array_merge($pen,array(
            'emp_contact' => strtoupper($model->cf1->employer_contact_no),
            'emp_business_name' => strtoupper($model->cf1->employer_business_name),
            'emp_date_signed' => MembershipForm::formatDate($model->cf1->employer_date_signed,'m-d-Y',''),
            'emp_name' => strtoupper($model->cf1->employer_name),
            'emp_cap' => strtoupper($model->cf1->employer_capacity),
        ));

        $memberFullName = strtr("lastName   firstName   nameExt   middleName",array(
            'lastName' => str_pad(strtoupper($model->nameLast), 15,"  ",STR_PAD_RIGHT),
            'firstName' => str_pad(strtoupper($model->nameFirst), 15,"  ",STR_PAD_RIGHT),
            'nameExt' => str_pad(strtoupper($model->nameExtension), 15,"  ",STR_PAD_RIGHT),
            'middleName' => str_pad(strtoupper($model->nameMiddle), 15,"  ",STR_PAD_RIGHT),
        ));

        return array_merge(array(
            //'signed_date' => strtotime($model->cf1Signed_date) ? date('m-d-Y',strtotime($model->cf1Signed_date)) : '',
            'name_first' => strtoupper($model->nameFirst),
            'name_last' => strtoupper($model->nameLast),
            'name_middle' => strtoupper($model->nameMiddle),
            'suffix' => strtoupper($model->nameExtension),

            'member_fullname' => $memberFullName,

            'member_gender_m' => $model->sex == 'm' ? 'X' : '',
            'member_gender_f' => $model->sex == 'f' ? 'X' : '',
            'patient_is_member_y' => $model->isMember ? 'X' : '',
            'patient_is_member_n' => (!$model->isMember) ? 'X' : '',

            /* Relation to Member */
            'is_sibling' => $model->signedByDiffPerson() && $model->cf1->signatory_relation == 'B' ? 'X' : '',
            'is_child' => $model->signedByDiffPerson() && $model->cf1->signatory_relation == 'C' ? 'X' : '',
            'is_other' => $model->signedByDiffPerson() && $model->cf1->signatory_relation == 'O' ? 'X' : '',
            'is_parent' => $model->signedByDiffPerson() && $model->cf1->signatory_relation == 'P' ? 'X' : '',
            'is_spouse' => $model->signedByDiffPerson() && $model->cf1->signatory_relation == 'S' ? 'X' : '',
            'specify' => $model->signedByDiffPerson() && $model->cf1->signatory_relation == 'O' ? $model->cf1->other_relation : '',

            'birth_date' => MembershipForm::formatDate($model->birthDate),

            /* Incapability */
            'is_incapacitated' => $model->cf1->is_incapacitated ? 'X' : '',
            'is_other_reason' => ($model->signedByDiffPerson() && !$model->cf1->is_incapacitated) ? 'X' : '',
            'reason' => ($model->signedByDiffPerson() && !$model->cf1->is_incapacitated) ? strtoupper($model->cf1->reason) : '',

            /* Signatory */
            'signatory_name_nonmember' => $model->signedByDiffPerson() ? strtoupper($model->cf1->signatory_name) : '',
            'signatory_name_member' => !$model->signedByDiffPerson() ? strtoupper($model->memberInfo->getFullName()) : '',
            'signatory_date_nonmember' => $model->signedByDiffPerson() ? MembershipForm::formatDate($model->cf1->signed_date) : null,
            'signatory_date_member' => !$model->signedByDiffPerson() ? MembershipForm::formatDate($model->cf1->signed_date) : null,

            /* Other Info */
            'unit' => strtoupper($model->floor),
            'building_name' => strtoupper($model->buildingName),
            'street' => strtoupper($model->street),
            'lot_no' => strtoupper($model->lotNo),
            'subdivision' => strtoupper($model->subdivision),
            'barangay' => strtoupper($model->barangay),
            'city' => strtoupper($model->municipality),
            'province' => strtoupper($model->province),
            'country' => strtoupper($model->country),
            'zipcode' => $model->zipCode,
            'landline_no' => $model->telNo,
            'mobile_no' => $model->mobileNo,
            'email_address' => $model->email,
        ), $part2, $memberPin, $part3);
    }

    private static function getCf1PrintData(MembershipForm $model)
    {
        $baseUrl = sprintf(
            "%s://%s%s",
            isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
            $_SERVER['SERVER_ADDR'],
            Yii::app()->baseUrl
        );

        return array(
            array(
                'image_01' => $baseUrl . '/images/phic_logo.png'
            )
        );
    }

    public function actionPrintPmrf($caseNr)
    {
        $model = MembershipForm::findByEncounter($caseNr);
        $iReport = new IReport;
        $iReport->format = IReport::PDF;
        $iReport->template = 'pmrf';

        $iReport->parameters = self::getPmrfPrintParameters($model);
        $iReport->data = self::getPmrfPrintData($model);
        $iReport->encoding = 'UTF-8';

        $iReport->show();
    }

    private static function getPmrfPrintParameters(MembershipForm $model)
    {
        /**
         * @var MembershipForm @memberInfo
         */
        $memberInfo = $model->pmrf->memberInfo;
        if (!$memberInfo)
            throw new CHttpException(404, 'The requested page does not exist.');

        return array_merge(array(
            'root' => java_resource,
            'purpose' => $model->pmrf->purpose,
            'first_name' => $memberInfo->name_first,
            'middle_name' => $memberInfo->name_middle,
            'last_name' => $memberInfo->name_last,
            'suffix' => $memberInfo->name_extension,
            'maiden_first_name' => $memberInfo->maiden_name_first,
            'maiden_middle_name' => $memberInfo->maiden_name_middle,
            'maiden_last_name' => $memberInfo->maiden_name_last,
            'maiden_suffix' => $memberInfo->maiden_name_extension,
            'birth_date' => MembershipForm::formatDate($memberInfo->birth_date),
            'birth_place' => $memberInfo->birth_place,
            'sex' => $memberInfo->sex,
            'civil_status' => $memberInfo->civil_status,
            'nationality' => mb_strtoupper($memberInfo->nationality),
            'tin' => $model->pmrf->tin,
            'floor' => $memberInfo->floor,
            'building_name' => $memberInfo->building_name,
            'lot_no' => $memberInfo->lot_no,
            'street' => $memberInfo->street,
            'subdivision' => $memberInfo->subdivision,
            'barangay' => $memberInfo->barangay,
            'city_municipality' => $memberInfo->municipality,
            'province' => $memberInfo->province,
            'country' => $memberInfo->country,
            'zip_code' => $memberInfo->zip_code,
            'telephone' => $memberInfo->tel_no,
            'mobile' => $memberInfo->mobile_no,
            'email' => $memberInfo->email,
            'member_category' => intval($model->pmrf->membership_category),
            'membership_specific' => $model->pmrf->membership_other,
            'membership_income' => $model->pmrf->membership_income > 0 ? str_replace(',', '', $model->pmrf->membership_income) : null,
            'membership_has_income' => $model->pmrf->membership_income > 0 && intval($model->pmrf->membership_category)==12 ? 'X' : '',
            'membership_effectivity' => date('mdY', strtotime(str_replace('-', '/', $model->pmrf->membership_effective_date))),
//            'create_time' => date('m-d-Y', strtotime($model->pmrf->create_time)),
//            'sign' => $model->memberInfo->getFullName(),
            'pin' => $model->pin ? str_replace('-','',$model->pin) : '            '
        ), self::getDependents($model));
    }

    private static function getPmrfPrintData(MembershipForm $model)
    {
        return array(
            array()
        );
    }

    private static function getDependents(MembershipForm $model)
    {

        $result = array();

        $childCount = 0;
        foreach ($model->pmrf->dependents as $dependent) {
            $prefix = '';
            $suffix = '';
            switch ($dependent->relation) {
                case PmrfDependent::RELATION_SPOUSE:
                    $prefix = 'spouse';
                    break;
                case PmrfDependent::RELATION_CHILD:
                    $childCount++;
                    $prefix = 'child';
                    $suffix = '_' . ($childCount);
                    break;
                case PmrfDependent::RELATION_FATHER:
                    $prefix = 'father';
                    break;
                case PmrfDependent::RELATION_MOTHER:
                    $prefix = 'mother';
                    break;
            }
            $temp = array(
                $prefix . '_pin' . $suffix => $dependent->pin,
                $prefix . '_last_name' . $suffix => $dependent->last_name,
                $prefix . '_first_name' . $suffix => $dependent->first_name,
                $prefix . '_suffix' . $suffix => $dependent->name_extension,
                $prefix . '_middle_name' . $suffix => $dependent->middle_name,
                $prefix . '_is_disabled' . $suffix => intval($dependent->is_disabled),
                $prefix . '_birth_date' . $suffix => date('m-d-Y', strtotime($dependent->birth_date)),
                $prefix . '_sex' . $suffix => strtoupper($dependent->sex)
            );

            $result = array_merge($result, $temp);
        }

        return $result;
    }

}
