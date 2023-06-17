<?php

/**
 *
 * @author  Ma. Dulce O. Polinar  <dulcepolinar1010@gmail.com>
 * @copyright (c) 2014, Segworks Technologies Corporation (http://www.segworks.com)
 *
 */

/**
 * This is the model class for table "seg_eclaims_eligibility".
 * The followings are the available columns in table 'seg_eclaims_eligibility':
 * @property string $id
 * @property string $encounter_nr
 * @property string $tracking_number
 * @property string $as_of
 * @property integer $remaining_days
 * @property integer $is_nhts
 * @property integer $with_3over6
 * @property integer $with_9over12
 * @property integer $is_eligible
 * @property integer $is_final
 * @property string $patient_lname
 * @property string $patient_fname
 * @property string $patient_mname
 * @property string $patient_suffix
 * @property string $patient_birth_date
 * @property string $patient_admission_date
 * @property string $patient_discharged_date
 * @property string $member_pin
 * @property string $member_type
 * @property string $member_lname
 * @property string $member_fname
 * @property string $member_mname
 * @property string $member_suffix
 * @property string $member_birth_date
 * @property string $member_relation
 * @property string $member_employer_no
 * @property string $member_employer_name
 *
 */
class Eligibility extends CareActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_eclaims_eligibility';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array();
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
          'encounterNr' => array(self::BELONGS_TO, 'CareEncounter', 'encounter_nr'),
          'document' => array(self::HAS_MANY, 'EligibilityDocument', 'eligibility_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
          'id' => 'ID',
          'encounter_nr' => 'Encounter Number',
          'tracking_number' => 'Tracking Number',
          'as_of' => 'Date & Time of Generation',
          'remaining_days' => 'Number of days remaining from the 45 days benefit limit',
          'is_nhts' => 'Is Nhts',
          'with_3over6' => 'With 3 monthly contributions within the past 6 months?',
          'with_9over12' => 'With 9 monthly contributions within the past 12 months?',
          'is_eligible' => 'Eligible to Avail PhilHealth Benefits?',
          'is_final' => 'Is Final',
          'patient_lname' => 'Patient Lname',
          'patient_fname' => 'Patient Fname',
          'patient_mname' => 'Patient Mname',
          'patient_suffix' => 'Patient Suffix',
          'patient_birth_date' => 'Patient Birth Date',
          'patient_admission_date' => 'Patient Admission Date',
          'patient_discharged_date' => 'Patient Discharged Date',
          'member_pin' => 'Member Pin',
          'member_type' => 'Member Type',
          'member_lname' => 'Member Lname',
          'member_fname' => 'Member Fname',
          'member_mname' => 'Member Mname',
          'member_suffix' => 'Member Suffix',
          'member_birth_date' => 'Member Birth Date',
          'member_relation' => 'Member Relation',
          'member_employer_no' => 'Member Employer No',
          'member_employer_name' => 'Member Employer Name',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('encounter_nr', $this->encounter_nr, true);
        $criteria->compare('tracking_number', $this->tracking_number, true);
        $criteria->compare('as_of', $this->as_of, true);
        $criteria->compare('remaining_days', $this->remaining_days);
        $criteria->compare('is_nhts', $this->is_nhts);
        $criteria->compare('with_3over6', $this->with_3over6);
        $criteria->compare('with_9over12', $this->with_9over12);
        $criteria->compare('is_eligible', $this->is_eligible);

        return new CActiveDataProvider($this, array(
          'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return SegEclaimsEligibility the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     *
     * @return string If member is eligible or not
     */
    public function getEligibility()
    {
        switch ($this->is_eligible) {
            case "1":
                return "Yes";
                break;
            case "0":
                return "No";
                break;
        }
    }

    /**
     *
     * @return string
     */
    public function getNHTS()
    {
        switch ($this->is_nhts) {
            case "1":
                return "Yes";
                break;
            case "0":
                return "No";
                break;
        }
    }

    /**
     *
     * @return string
     */
    public function get3Over6()
    {
        switch ($this->with_3over6) {
            case "1":
                return "Yes";
                break;
            case "0":
                return "No";
                break;
        }
    }

    /**
     *
     * @return string
     */
    public function get9Over12()
    {
        switch ($this->with_9over12) {
            case "1":
                return "Yes";
                break;
            case "0":
                return "No";
                break;
        }
    }

    /**
     *
     * @param type $dateValue
     */
    private static function fixDate($dateValue = null, $nullToCurrentTime = false)
    {
        $format = 'm-d-Y';
        $_dateFixer = function ($isCurrentTime) use ($format) {
            if ($isCurrentTime) {
                $dateValue = Date($format, time());
            } else {
                $dateValue = '';
            }
            return $dateValue;
        };
        /* Check if Initialy, $dateValue is empty */
        if (empty($dateValue)) {
            return $_dateFixer($nullToCurrentTime);
        } else {
            /* If not */
            $dateValue = strtotime($dateValue);
            /* Check if empty, after converting to time */
            if (empty($dateValue)) {
                return $_dateFixer($nullToCurrentTime);
            }
        }

        return date($format, $dateValue);
    }

    /**
     * Returns an array based on the encounter's information used for calling
     * the eligibility web service.
     *
     * @param Encounter $encounter
     * @return array
     */
    public static function compact($encounter)
    {
        #added hospital code
        Yii::import('eclaims.models.HospitalConfigForm');
        Yii::import('phic.models.*');

        $configModel = new HospitalConfigForm;
        $hospitalCode = $configModel->hospital_code;


        if (empty($encounter)) {
            $encounter = new EclaimsEncounter;
        }

        $person = $encounter->person;
        if (empty($person)) {
            $person = new EclaimsPerson;
        }

        $member = PhicMember::model()->findbyPk($encounter->encounter_nr);

        if (empty($member)) {
            $member = new EclaimsPhicMember;
        }


        // @todo Create a Model, to make use of rules() for validation.
        $result = array(
            #added hospital code
          'pHospitalCode' => $hospitalCode,
            // 'pMemberPIN' => $member->insurance_nr,
          'pPIN' => $member->insurance_nr,
          'pMemberLastName' => $member->member_lname,
          'pMemberFirstName' => $member->member_fname,
          'pMemberMiddleName' => $member->member_mname,
          'pMemberSuffix' => $member->suffix,
          'pMemberBirthDate' => self::fixDate($member->birth_date),
            // 'pMemberSex' => $member->sex,
            // 'pMailingAddress' => $member->getFullAddress(),
          'pMailingAddress' => $person->getFullAddress(),
          'pZipCode' => $member->getZipCode(),
          'pPatientIs' => $member->relation,
          'pAdmissionDate' => self::fixDate($encounter->getAdmissionDt(), true),
          'pDischargeDate' => self::fixDate(empty($encounter->bill->bill_dte) ? null : $encounter->bill->bill_dte,
            true),
          'pPatientLastName' => $person->name_last,
          'pPatientFirstName' => $person->getNameFirst(),
          'pPatientMiddleName' => empty($person->name_middle) ? '.' : $person->name_middle,
          'pPatientSuffix' => $person->suffix,
          'pPatientBirthDate' => self::fixDate($person->date_birth),
          'pPatientGender' => $person->sex,
          'pMemberShipType' => $member->getMemberType(),
          'pPEN' => $member->employer_no,
          'pEmployerName' => $member->employer_name,
        );


        array_walk($result, function (&$value, $key) {
            if (empty($value)) {
                $value = '';
            }
            $value = strtoupper($value);
        });

        return $result;
    }

    /**
     * Extracts the information from an array ideally returned from calling
     * the isClaimEligible HIE web service.
     *
     * @param string $encounter
     * @param array $result Description
     */
    public static function extractResult($encounter, array $result, $isFinal = 0)
    {
        Yii::import('eclaims.components.EclaimsFormatter');
        $formatter = new EclaimsFormatter;


        $transaction = Yii::app()->getDb()->beginTransaction();
        try {
            $ok = true;
            $old = self::model()->find(array(
              'condition' => 'encounter_nr=:enc_nr',
              'params' => array(':enc_nr' => $encounter)
            ));

            if ($old) {
                $ok = $old->delete();
            }

            if ($ok) {
                $new = new Eligibility;
                $new->encounter_nr = $encounter;

                $new->as_of = date("Ymd",
                  strtotime(str_replace('-', '/', @$result["data"]["RESPONSE"]["@attributes"]["ASOF"])));
                $new->remaining_days = @$result["data"]["RESPONSE"]["@attributes"]["REMAINING_DAYS"];
                $new->is_nhts = $formatter->formatStringToBoolean(@$result["data"]["RESPONSE"]["@attributes"]["ISNHTS"]);
                $new->with_3over6 = $formatter->formatStringToBoolean(@$result["data"]["RESPONSE"]["@attributes"]["WITH3OVER6"]);
                $new->with_9over12 = $formatter->formatStringToBoolean(@$result["data"]["RESPONSE"]["@attributes"]["WITH9OVER12"]);
                $new->is_eligible = $formatter->formatStringToBoolean(@$result["data"]["RESPONSE"]["@attributes"]["ISOK"]);
                $new->is_final = $isFinal;

                /* @author Jolly Caralos */
                if ($new->is_final) {
                    $new->tracking_number = @$result["data"]["RESPONSE"]["@attributes"]["TRACKING_NUMBER"];
                } else {
                    if ($new->is_eligible) {
                        $new->tracking_number = $old->tracking_number;
                    } else {
                        /* Empty naman talaga. */
                        $new->tracking_number = '';
                    }
                }

                $new->patient_lname = @$result["data"]["RESPONSE"]["PATIENT"]["@attributes"]["LASTNAME"];
                $new->patient_fname = @$result["data"]["RESPONSE"]["PATIENT"]["@attributes"]["FIRSTNAME"];
                $new->patient_mname = @$result["data"]["RESPONSE"]["PATIENT"]["@attributes"]["MIDDLENAME"];
                $new->patient_suffix = @$result["data"]["RESPONSE"]["PATIENT"]["@attributes"]["SUFFIX"];
                $new->patient_birth_date = date("Ymd", strtotime(str_replace('-', '/',
                  @$result["data"]["RESPONSE"]["PATIENT"]["@attributes"]["BIRTHDATE"])));
                $new->patient_admission_date = date("Ymd", strtotime(str_replace('-', '/',
                  @$result["data"]["RESPONSE"]["CONFINMENT"]["@attributes"]["ADMITTED"])));
                $new->patient_discharged_date = date("Ymd", strtotime(str_replace('-', '/',
                  @$result["data"]["RESPONSE"]["CONFINMENT"]["@attributes"]["DISCHARGE"])));
                $new->member_pin = @$result["data"]["RESPONSE"]["MEMBER"]["@attributes"]["PIN"];
                $new->member_type = @$result["data"]["RESPONSE"]["MEMBER"]["@attributes"]["MEMBER_TYPE"];
                $new->member_lname = @$result["data"]["RESPONSE"]["MEMBER"]["@attributes"]["LASTNAME"];
                $new->member_fname = @$result["data"]["RESPONSE"]["MEMBER"]["@attributes"]["FIRSTNAME"];
                $new->member_mname = @$result["data"]["RESPONSE"]["MEMBER"]["@attributes"]["MIDDLENAME"];
                $new->member_suffix = @$result["data"]["RESPONSE"]["MEMBER"]["@attributes"]["SUFFIX"];
                $new->member_birth_date = date("Ymd",
                  strtotime(str_replace('-', '/', @$result["data"]["RESPONSE"]["MEMBER"]["@attributes"]["BIRTHDATE"])));
                $new->member_relation = @$result["data"]["RESPONSE"]["PATIENT"]["@attributes"]["PATIENTIS"];
                $new->member_employer_no = @$result["data"]["RESPONSE"]["EMPLOYER"]["@attributes"]["PEN"];
                $new->member_employer_name = @$result["data"]["RESPONSE"]["EMPLOYER"]["@attributes"]["NAME"];
                $ok = $new->save();

                if ($ok) {
                    if (!empty($result["data"]["RESPONSE"]["DOCUMENTS"])) {
                        if (is_array($result["data"]["RESPONSE"]["DOCUMENTS"]["DOCUMENT"])) {
                            $docs = $result["data"]["RESPONSE"]["DOCUMENTS"]["DOCUMENT"];
                            if (isset($docs['@value'])) {
                                // We are dealing with a single DOCUMENT child tag
                                $docs = array($docs);
                            }

                            if (!empty($docs[0])) {
                                foreach ($docs as $doc) {
                                    $document = new EligibilityDocument;
                                    $document->eligibility_id = $new->id;
                                    $document->code = @$doc["@attributes"]["CODE"];
                                    $document->name = @$doc["@attributes"]["NAME"];
                                    $document->reason = @$doc["@value"];
                                    $ok = $document->save();
                                    if (!$ok) {
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
            }

        } catch (Exception $e) {
            $ok = false;
        }

        if ($ok) {
            $transaction->commit();
            return $new;
        } else {
            $transaction->rollback();
            return false;
        }

    }
}
