<?php

Yii::import('phic.models.MemberInfo');
Yii::import('phic.models.ClaimForm1');
Yii::import('phic.models.Pmrf');
Yii::import('phic.models.PmrfDependent');

class MembershipForm extends CFormModel
{
    const PMRF_PURPOSE_UPDATE = 'update';
    const PMRF_PURPOSE_ENROLLMENT = 'enrollment';
    const CIVIL_STATUS_MARRIED = 'married';
    const PHILHEALTH_ID = 18;

    const RELATION_CHILD = 'C';
    const RELATION_PARENT = 'P';
    const RELATION_SPOUSE = 'S';

    /* @var $memberInfo MemberInfo */
    public $memberInfo;

    /* @var $cf1 ClaimForm1 */
    public $cf1;

    /* @var $pmrf Pmrf */
    public $pmrf;

    /* Membership Info */
    public $cf1Form;
    public $pmrfForm;

    public $id;
    public $pid;
    public $encounterNr;
    public $hcareId;
    public $isMember;
    public $pin;
    public $relation;
    public $nameLast;
    public $nameFirst;
    public $nameMiddle;
    public $nameExtension;
    public $maidenNameLast;
    public $maidenNameFirst;
    public $maidenNameMiddle;
    public $maidenNameExtension;
    public $sex;
    public $civilStatus;
    public $birthDate;
    public $birthPlace;
    public $nationality;
    public $floor;
    public $buildingName;
    public $lotNo;
    public $street;
    public $subdivision;
    public $barangay;
    public $municipality;
    public $province;
    public $country;
    public $zipCode;
    public $telNo;
    public $mobileNo;
    public $email;

    /* CF1 */
    public $cf1Signatory_is_representative;
    public $cf1Signatory_name;
    public $cf1Signed_date;
    public $cf1Signatory_relation;
    public $cf1Other_relation;
    public $cf1Is_incapacitated;
    public $cf1Reason;
    public $cf1Pin;
    /*part 3*/
    public $cf1EmployerPen;
    public $cf1ContactNo;
    public $cf1BusinessName;
    public $cf1EmployerName;
    public $cf1OfficialCapacity;
    public $cf1DateSigned;


    /* PMRF */
    public $pmrfPurpose;
    public $pmrfMembershipCategory;
    public $pmrfMembershipOther;
    public $pmrfMembershipIncome;
    public $pmrfMembershipEffectiveDate;
    public $pmrfTin;
    public $pmrfDependentsData;

    public function rules()
    {
        return array(
            array('pid,encounterNr,hcareId,isMember,nameLast,
            nameFirst,sex,civilStatus,birthDate,mobileNo', 'required'),

            array('cf1Signatory_is_representative', 'required2', 'form' => 'cf1Form'),
            array('pmrfPurpose', 'required2', 'form' => 'pmrfForm'),

            array('birthDate,cf1Signed_date', 'date'),

            array('pin,nameMiddle,nameExtension,birthPlace,
            maidenNameMiddle,maidenNameExtension
            nationality,floor,buildingName,lotNo,street,
            subdivision,barangay,municipality,province,
            country,zipCode,telNo,mobileNo,email,cf1Is_incapacitated,
            pmrfPurpose,pmrfMembershipCategory,
            cf1Form,pmrfForm,pmrfDependentsData,cf1Pin,pmrfTin
            cf1EmployerPen,cf1ContactNo,cf1BusinessName,cf1EmployerName,
            cf1OfficialCapacity,cf1DateSigned,pmrfMembershipOther,
            pmrfMembershipIncome', 'safe'),

            // array('pmrfMembershipOther', 'memberCategoryInfo', 'in' => array(12, 13, 17, 19, 20, 21)),
            // array('pmrfMembershipIncome', 'memberCategoryInfo', 'in' => array(12, 13)),
            // array('pmrfMembershipEffectiveDate', 'memberCategoryInfo', 'in' => array(23)),
            array('pmrfMembershipEffectiveDate', 'date'),

            array('maidenNameLast,maidenNameFirst', 'requiredMarriedFemale'),
            array('email', 'email'),

            array('relation', 'requiredForNonMember'),
//            array('cf1Signatory_name,cf1Signatory_relation', 'requiredForNonMember', 'form' => 'cf1Form'),

            array('cf1Reason', 'requiredForCf1NotIncapacitated'),

            array('cf1Signatory_name,cf1Signatory_relation,cf1Is_incapacitated', 'requiredForRepresentative'),
            array('cf1Other_relation', 'cf1RelationOther'),
            array('pmrfMembershipIncome', 'money', 'in' => array(12, 13))
        );
    }

    public function money($attribute, $parameters)
    {
        if ($this->pmrfForm && !is_numeric(str_replace(array(',', ' '), '', $this->$attribute))
            && in_array($this->pmrfMembershipCategory, $parameters['in']) && trim($this->$attribute) != ''
        ) {
            $this->addError($attribute, $this->getLabel($attribute) . ' is not valid.');
        }
    }

    public function signedByDiffPerson()
    {
        return $this->cf1Form && $this->cf1Signatory_is_representative;
    }

    public function cf1RelationOther($attribute)
    {
        $signedByDiffPerson = $this->signedByDiffPerson();
        if ($signedByDiffPerson && $this->cf1Signatory_relation == 'O' && trim($this->$attribute) == '')
            $this->addError($attribute, 'Please provide ' . $this->getLabel($attribute) . '.');

        if ($signedByDiffPerson && $this->cf1Signatory_relation != 'O' && trim($this->$attribute) != '')
            $this->addError($attribute, 'The selected CF1 Relation does not require ' . $this->getLabel($attribute) . '.');
    }

    public function requiredForRepresentative($attribute)
    {
        if ($this->cf1Form && $this->cf1Signatory_is_representative && trim($this->$attribute) == '') {
            $this->addError($attribute, 'Please provide ' . $this->getLabel($attribute) . '.');
        }
    }

    public function requiredForCf1NotIncapacitated($attribute)
    {
        if ($this->signedByDiffPerson() && !$this->cf1Is_incapacitated && trim($this->$attribute) == '')
            $this->addError($attribute, 'The member is <b>not</b> Incapacitated. Please provide ' . $this->getLabel($attribute) . '.');

        if ($this->signedByDiffPerson() && $this->cf1Is_incapacitated && trim($this->$attribute) != '')
            $this->addError($attribute, 'The member is not Incapacitated, ' . $this->getLabel($attribute) . ' is not required.');
    }

    public function required2($attribute, $parameters)
    {
        if ($this->$parameters['form'] && trim($this->$attribute) == '') {
            $this->addError($attribute, 'Please provide ' . $this->getLabel($attribute) . '.');
        }
    }

    public function fillUp($attribute, $parameters)
    {
        if ($this->$parameters['form'] && !trim($this->$attribute)) {
            $this->addError($attribute, 'Please provide ' . $this->getLabel($attribute) . '.');
        }
    }

    public function memberCategoryInfo($attribute, $parameters)
    {
        if ($this->pmrfForm) {
            if (in_array($this->pmrfMembershipCategory, $parameters['in'])) {
                if (trim($this->$attribute) == '') {
                    $this->addError($attribute, 'Please provide ' . $this->getLabel($attribute) . '.');
                }
            } else {
                if (trim($this->$attribute) != '') {
                    $this->addError($attribute, 'This field is not required by the specified membership category.');
                }
            }
        }
    }

    public function requiredMarriedFemale($attribute)
    {
        if ($this->sex == 'f' && $this->civilStatus == self::CIVIL_STATUS_MARRIED && trim($this->$attribute) == '') {
            $this->addError($attribute, 'The patient is a married female. ' . $this->getLabel($attribute) . ' is required.');
        }
    }

    public function requiredForNonMember($attribute, $parameters)
    {
        if ($parameters['form']) {
            if ($this->$parameters['form'] && !$this->isMember && trim($this->$attribute) == '') {
                $this->addError($attribute, $this->getLabel($attribute) . ' is required if patient is not the principal holder.');
            }
        } else {
            if (!$this->isMember && trim($this->$attribute) == '') {
                $this->addError($attribute, $this->getLabel($attribute) . ' is required if patient is not the principal holder.');
            }
            if ($this->isMember && trim($this->$attribute) != '') {
                $this->addError($attribute, $this->getLabel($attribute) . ' is <b>not</b> required if patient is not the principal holder.');
            }
        }
    }

    public function getLabel($attribute)
    {
        $labels = $this->attributeLabels();
        return $labels[$attribute];
    }

    public function attributeLabels()
    {
        return array(
            'pid' => 'HRN',
            'encounterNr' => 'Case #',
            'hcareId' => 'Insurance Firm ID',
            'isMember' => 'Is Patient a Member?',
            'pin' => 'PIN',
            'relation' => 'Relation',
            'nameLast' => 'Last Name',
            'nameFirst' => 'First Name',
            'nameMiddle' => 'Middle Name',
            'nameExtension' => 'Name Extension',
            'maidenNameLast' => 'Maiden Last Name',
            'maidenNameFirst' => 'Maiden First Name',
            'maidenNameMiddle' => 'Maiden Middle Name',
            'maidenNameExtension' => 'Maiden Name Extension',
            'sex' => 'Sex',
            'civilStatus' => 'Civil Status',
            'birthDate' => 'Birth Date',
            'birthPlace' => 'Birth Place',
            'nationality' => 'Nationality',
            'floor' => 'Floor',
            'buildingName' => 'Building Name',
            'lotNo' => 'Lot No',
            'street' => 'Street',
            'subdivision' => 'Sub-division',
            'barangay' => 'Barangay',
            'municipality' => 'Municipality',
            'province' => 'Province',
            'country' => 'Country',
            'zipcode' => 'Zip Code',
            'telNo' => 'Tel #',
            'mobileNo' => 'Mobile #',
            'email' => 'E-mail',
            'pmrfPurpose' => 'Purpose',
            'pmrfMembershipCategory' => 'Membership Category',
            'pmrfMembershipOther' => 'Other Membership Category',
            'pmrfMembershipIncome' => 'Income',
            'pmrfMembershipEffectiveDate' => 'Effective Date',
            'pmrfTin' => 'TIN',
            'cf1Signatory_is_representative' => 'Signed by',
            'cf1Signed_date' => 'Signed Date',
            'cf1Signatory_name' => 'Signatory Name',
            'cf1Signatory_relation' => 'Relationship to Member',
            'cf1Other_relation' => 'Other Relation',
            'cf1Is_incapacitated' => 'Is Incapacitated',
            'cf1Reason' => 'Reason',
            'cf1Pin' => 'Patient\'s PIN',
            'cf1EmployerPen' => 'Employer PEN',
            'cf1ContactNo' => 'Contact #',
            'cf1BusinessName' => 'Business Name',
            'cf1EmployerName' => 'Employer Name',
            'cf1OfficialCapacity' => 'Official Capacity/Designation',
            'cf1DateSigned' => 'Signed Date',
        );
    }

    public static function findByEncounter($encounterNr)
    {
        $model = null;

        /* @var $memberInfo MemberInfo */
        $memberInfo = MemberInfo::model()->findByAttributes(array(
            'encounter_nr' => $encounterNr
        ));

        if ($memberInfo) {
            $model = new MembershipForm();
            $model->cf1 = $memberInfo->claimForm1;
            $model->pmrf = $memberInfo->pmrf;
            $model->memberInfo = $memberInfo;

            $model->cf1Form = $memberInfo->claimForm1 ? 1 : 0;
            $model->pmrfForm = $memberInfo->pmrf ? 1 : 0;

            $model->pid = $memberInfo->pid;
            $model->encounterNr = $memberInfo->encounter_nr;
            $model->hcareId = $memberInfo->hcare_id;
            $model->isMember = $memberInfo->is_member;
            $model->pin = $memberInfo->pin;
            $model->relation = $memberInfo->relation;
            $model->nameLast = $memberInfo->name_last;
            $model->nameFirst = $memberInfo->name_first;
            $model->nameMiddle = $memberInfo->name_middle;
            $model->nameExtension = $memberInfo->name_extension;
            $model->maidenNameLast = $memberInfo->maiden_name_last;
            $model->maidenNameFirst = $memberInfo->maiden_name_first;
            $model->maidenNameMiddle = $memberInfo->maiden_name_middle;
            $model->maidenNameExtension = $memberInfo->maiden_name_extension;
            $model->sex = $memberInfo->sex;
            $model->civilStatus = $memberInfo->civil_status;
            $model->birthDate = self::formatDate($memberInfo->birth_date);
            $model->birthPlace = $memberInfo->birth_place;
            $model->nationality = $memberInfo->nationality;
            $model->floor = $memberInfo->floor;
            $model->buildingName = $memberInfo->building_name;
            $model->lotNo = $memberInfo->lot_no;
            $model->street = $memberInfo->street;
            $model->subdivision = $memberInfo->subdivision;
            $model->barangay = $memberInfo->barangay;
            $model->municipality = $memberInfo->municipality;
            $model->province = $memberInfo->province;
            $model->country = $memberInfo->country;
            $model->zipCode = $memberInfo->zip_code;
            $model->telNo = $memberInfo->tel_no;
            $model->mobileNo = $memberInfo->mobile_no;
            $model->email = $memberInfo->email;

            $model->cf1Signatory_is_representative = $memberInfo->claimForm1->signatory_is_representative;
            $model->cf1Signed_date = self::formatDate($memberInfo->claimForm1->signed_date);
            $model->cf1Signatory_name = $memberInfo->claimForm1->signatory_name;
            $model->cf1Signatory_relation = $memberInfo->claimForm1->signatory_relation;
            $model->cf1Other_relation = $memberInfo->claimForm1->other_relation;
            $model->cf1Is_incapacitated = $memberInfo->claimForm1->is_incapacitated;
            $model->cf1Reason = $memberInfo->claimForm1->reason;
            $model->cf1Pin = $memberInfo->claimForm1->pin;

            $model->cf1EmployerPen = $memberInfo->claimForm1->employer_pen;
            $model->cf1ContactNo = $memberInfo->claimForm1->employer_contact_no;
            $model->cf1BusinessName = $memberInfo->claimForm1->employer_business_name;
            $model->cf1EmployerName = $memberInfo->claimForm1->employer_name;
            $model->cf1OfficialCapacity = $memberInfo->claimForm1->employer_capacity;
            $model->cf1DateSigned = self::formatDate($memberInfo->claimForm1->employer_date_signed);

            $model->pmrfPurpose = $memberInfo->pmrf->purpose;
            $model->pmrfMembershipCategory = $memberInfo->pmrf->membership_category;
            $model->pmrfMembershipOther = $memberInfo->pmrf->membership_other;
            $model->pmrfMembershipIncome = $memberInfo->pmrf->membership_income > 0 ? number_format($memberInfo->pmrf->membership_income, 2) : '';
            $model->pmrfMembershipEffectiveDate = self::formatDate($memberInfo->pmrf->membership_effective_date);
            $model->pmrfTin = $memberInfo->pmrf->tin;

        }

        return $model;
    }

    public static function getPersonInfo($encounterNr)
    {
        $encounter = Encounter::model()->with(array('person'))->findByPk($encounterNr);
        return array(
            'nameLast' => $encounter->person->name_last,
            'nameFirst' => $encounter->person->name_first,
            'nameExtension' => $encounter->person->suffix,
            'nameMiddle' => $encounter->person->name_middle,
            'birthDate' => date('m/d/Y', strtotime($encounter->person->date_birth)),
            'birthPlace' => $encounter->person->place_birth,
            'sex' => $encounter->person->sex,
            'civilStatus' => $encounter->person->civil_status,
            'nationality' => $encounter->person->country->citizenship,
            'barangay' => trim($encounter->person->barangay->brgy_name),
            'municipality' => trim($encounter->person->municipality->mun_name),
            'province' => trim($encounter->person->municipality->parent->prov_name),
            'country' => trim($encounter->person->country->country_name),
            'zipCode' => trim($encounter->person->municipality->zipcode),
            'mobileNo' => $encounter->person->cellphone_1_nr
        );
    }

    public function save()
    {
        /* @var $transaction CDbTransaction */
        $transaction = Yii::app()->db->beginTransaction();

        $membership = $this->saveMembershipInfo();
        if ($membership === false) {
            $transaction->rollback();
            return false;
        }

        if ($this->cf1Form) {
            $cf1 = $this->saveCf1($membership);
            if ($cf1 === false) {
                $transaction->rollback();
                return false;
            }
        }

        if ($this->pmrfForm) {
            $pmrf = $this->savePmrf($membership);
            if ($pmrf === false) {
                $transaction->rollback();
                return false;
            }

            $pmrfDependents = $this->savePmrfDependents($pmrf);
            if ($pmrfDependents === false) {
                $transaction->rollback();
                return false;
            }
        }

        $transaction->commit();
        return true;
    }

    private function saveMembershipInfo()
    {
        /* @var $model MemberInfo */
        $model = MemberInfo::model()->findByAttributes(array(
            'encounter_nr' => $this->encounterNr
        ));
        if (!$model) {
            $model = new MemberInfo;
        }

        $model->pid = $this->pid;
        $model->encounter_nr = $this->encounterNr;
        $model->hcare_id = $this->hcareId;
        $model->is_member = $this->isMember;
        $model->pin = $this->pin;
        $model->relation = $this->relation;
        $model->name_last = $this->nameLast;
        $model->name_first = $this->nameFirst;
        $model->name_middle = $this->nameMiddle;
        $model->name_extension = $this->nameExtension;
        $model->maiden_name_last = $this->maidenNameLast;
        $model->maiden_name_first = $this->maidenNameFirst;
        $model->maiden_name_middle = $this->maidenNameMiddle;
        $model->maiden_name_extension = $this->maidenNameExtension;
        $model->sex = $this->sex;
        $model->civil_status = $this->civilStatus;
        $model->birth_date = self::formatDate($this->birthDate,'Y-m-d');
        $model->birth_place = $this->birthPlace;
        $model->nationality = $this->nationality;
        $model->floor = $this->floor;
        $model->building_name = $this->buildingName;
        $model->lot_no = $this->lotNo;
        $model->street = $this->street;
        $model->subdivision = $this->subdivision;
        $model->barangay = $this->barangay;
        $model->municipality = $this->municipality;
        $model->province = $this->province;
        $model->country = $this->country;
        $model->zip_code = $this->zipCode;
        $model->tel_no = $this->telNo;
        $model->mobile_no = $this->mobileNo;
        $model->email = $this->email;

        return $this->_save($model);
    }

    private function saveCf1(MemberInfo $membership)
    {
        /* @var $model ClaimForm1 */
        $model = ClaimForm1::model()->findByPk($membership->id);
        if (!$model) {
            $model = new ClaimForm1;
        }

        $model->member_info_id = $membership->id;
        $model->signatory_is_representative = $this->cf1Signatory_is_representative;
        $model->signed_date = self::formatDate($this->cf1Signed_date,'Y-m-d');
        $model->signatory_name = $this->cf1Signatory_name;
        $model->signatory_relation = $this->cf1Signatory_relation;
        $model->other_relation = $this->cf1Other_relation;
        $model->is_incapacitated = $this->cf1Is_incapacitated;
        $model->reason = $this->cf1Reason;
        $model->pin = $this->cf1Pin;

        $model->employer_pen = $this->cf1EmployerPen;
        $model->employer_contact_no = $this->cf1ContactNo;
        $model->employer_business_name = $this->cf1BusinessName;
        $model->employer_name = $this->cf1EmployerName;
        $model->employer_capacity = $this->cf1OfficialCapacity;
        $model->employer_date_signed = self::formatDate($this->cf1DateSigned,'Y-m-d');

        return $this->_save($model);
    }

    private function savePmrf(MemberInfo $membership)
    {
        /* @var $model Pmrf */
        $model = Pmrf::model()->findByPk($membership->id);
        if (!$model) {
            $model = new Pmrf;
        }

        $model->member_info_id = $membership->id;
        $model->purpose = $this->pmrfPurpose;
        $model->membership_category = $this->pmrfMembershipCategory;
        $model->membership_other = $this->pmrfMembershipOther;
        $model->membership_income = str_replace(array(',', ' '), '', $this->pmrfMembershipIncome);
        $model->membership_effective_date = self::formatDate($this->pmrfMembershipEffectiveDate,'Y-m-d');
        $model->tin = $this->pmrfTin;

        return $this->_save($model);
    }

    private function savePmrfDependents(Pmrf $pmrf)
    {
        foreach ($this->pmrfDependentsData as $key => $dependent) {
            if (isset($dependent['id'])) {
                $model = PmrfDependent::model()->findByPk($dependent['id']);
            } else {
                $model = new PmrfDependent();
            }
            $model->setAttributes($dependent);
            $model->pmrf_id = $pmrf->member_info_id;
            if (!$model->save()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $model
     * @return bool|CareActiveRecord
     */
    private function _save(CareActiveRecord $model)
    {
        if ($model->hasAttribute('history')) {
            if ($model->isNewRecord) {
                $model->history = strtr("Created by userName at transactionDate\n", array(
                    'userName' => $_SESSION['sess_temp_fullname'],
                    'transactionDate' => date('Y-m-d h:i:s A')
                ));
            } else {
                $model->history .= strtr("Updated by userName at transactionDate\n", array(
                    'userName' => $_SESSION['sess_temp_fullname'],
                    'transactionDate' => date('Y-m-d h:i:s A')
                ));
            }
        }

        if (!$model->save()) {
            $this->addErrors($model->getErrors());
            return false;
        }

        return $model;
    }

    public static function formatDate($date, $format = 'm/d/Y', $whenNull = null)
    {
        $timeStamp = strtotime($date);
        if(strtotime($date) === false || $timeStamp === -1)
            return $whenNull;
        else
            return date($format,$timeStamp);
    }

}