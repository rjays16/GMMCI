<?php

/**
 * This is the model class for table "seg_encounter_insurance_memberinfo".
 *
 * The followings are the available columns in table 'seg_encounter_insurance_memberinfo':
 * @property string $encounter_nr
 * @property string $pid
 * @property string $hcare_id
 * @property string $insurance_nr
 * @property string $member_lname
 * @property string $member_fname
 * @property string $member_mname
 * @property string $suffix
 * @property string $birth_date
 * @property string $street_name
 * @property integer $brgy_nr
 * @property integer $mun_nr
 * @property string $relation
 * @property string $member_type
 * @property string $employer_no
 * @property string $employer_name
 * @property string $create_dt
 * @property string $create_id
 * @property string $modify_dt
 * @property string $modify_id
 * @property string $history
 * @property string $sex
 */

Yii::import('application.models.address.AddressBarangay');
Yii::import('application.models.address.AddressMunicipality');

class PhicMember extends CareActiveRecord
{

    const HCARE_ID = 18;
    const defaultPIN = 000000000000;
    /**
     * @var Default member type, if set member_type
     * is not suppported by PHIC
     * @author Jolly Caralos
     */
    const MEMBERTYPE_DEFAULT = 'I';

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_encounter_insurance_memberinfo';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('encounter_nr , pid, hcare_id, member_type, relation, member_lname, member_fname', 'required'),
            array('brgy_nr, mun_nr', 'numerical', 'integerOnly' => true),
            array('encounter_nr, pid, hcare_id', 'length', 'max' => 12),
            array('insurance_nr', 'length', 'max' => 20),
            array('member_lname, member_fname, member_mname, suffix, create_id, modify_id', 'length', 'max' => 50),
            array('street_name', 'length', 'max' => 150),
            array('relation, sex', 'length', 'max' => 1),
            array('member_type', 'length', 'max' => 5),
            array('employer_no', 'length', 'max' => 12),
            array('birth_date, employer_name, create_dt, modify_dt,  encounter_nr,history,patient_pin', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('encounter_nr, pid, hcare_id, insurance_nr, member_lname, member_fname, member_mname, suffix, birth_date, street_name, brgy_nr, mun_nr, relation, member_type, employer_no, employer_name, create_dt, create_id, modify_dt, modify_id, history, sex,patient_pin', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'person' => array(self::BELONGS_TO, 'Person', 'pid'),
            'barangay' => array(self::BELONGS_TO, 'AddressBarangay', 'brgy_nr'),
            'municipality' => array(self::BELONGS_TO, 'AddressMunicipality', 'mun_nr'),
            'hcare' => array(self::BELONGS_TO, 'InsuranceProvider', 'hcare_id'),
            'personInsurance' => array(self::BELONGS_TO, 'PersonInsurance', array('pid' => 'pid', 'hcare_id' => 'hcare_id')),
            'encounter' => array(self::HAS_ONE, 'Encounter', 'encounter_nr'),

            'memberCategoryByCode' => array(self::BELONGS_TO, 'Memcategory', array('member_type' => 'memcategory_code'))
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'encounter_nr' => 'Encounter',
            'pid' => 'Pid',
            'hcare_id' => 'Hcare',
            'insurance_nr' => 'PIN',
            'member_lname' => 'Last name',
            'member_fname' => 'First name',
            'member_mname' => 'Middle name',
            'MemberFullName' => 'Full name',
            'suffix' => 'Suffix',
            'birth_date' => 'Birth Date',
            'street_name' => 'Street Name',
            'brgy_nr' => 'Brgy Nr',
            'mun_nr' => 'Mun Nr',
            'relation' => 'Relation',
            'member_type' => 'Member Type',
            'employer_no' => 'Employer No',
            'employer_name' => 'Employer Name',
            'create_dt' => 'Create Date',
            'create_id' => 'Create',
            'modify_dt' => 'Modify Date',
            'modify_id' => 'Modify',
            'history' => 'History',
            'sex' => 'Sex',
            'patient_pin' => 'Patient PIN',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('encounter_nr', $this->encounter_nr, true);
        $criteria->compare('pid', $this->pid, true);
        $criteria->compare('hcare_id', $this->hcare_id, true);
        $criteria->compare('insurance_nr', $this->insurance_nr, true);
        $criteria->compare('member_lname', $this->member_lname, true);
        $criteria->compare('member_fname', $this->member_fname, true);
        $criteria->compare('member_mname', $this->member_mname, true);
        $criteria->compare('suffix', $this->suffix, true);
        $criteria->compare('birth_date', $this->birth_date, true);
        $criteria->compare('street_name', $this->street_name, true);
        $criteria->compare('brgy_nr', $this->brgy_nr);
        $criteria->compare('mun_nr', $this->mun_nr);
        $criteria->compare('relation', $this->relation, true);
        $criteria->compare('member_type', $this->member_type, true);
        $criteria->compare('employer_no', $this->employer_no, true);
        $criteria->compare('employer_name', $this->employer_name, true);
        $criteria->compare('create_dt', $this->create_dt, true);
        $criteria->compare('create_id', $this->create_id, true);
        $criteria->compare('modify_dt', $this->modify_dt, true);
        $criteria->compare('modify_id', $this->modify_id, true);
        $criteria->compare('history', $this->history, true);
        $criteria->compare('sex', $this->sex, true);
        $criteria->compare('patient_pin', $this->patient_pin, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return PhicMember the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     *
     * @return void
     */
    public function init()
    {
        $this->hcare_id = self::HCARE_ID;
    }


    /**
     * [beforeSave description]
     * @return [type] [description]
     */
    public function beforeSave()
    {

        # Added by jeff 02-19-18 for no middle names.
//        $varmnane = $this->person->name_middle;
//        if ($varmnane == '' || $varmnane == NULL ) {
//            $this->person->name_middle = '.';
//        }


//        if ($this->relation == 'M') {
//            $this->member_lname = $this->person->name_last;
//            $this->member_fname = $this->person->getNameFirst();
//            $this->member_mname = $this->person->name_middle;
//            $this->suffix = $this->person->getSuffix();
//            $this->sex = $this->person->sex;
//            $this->birth_date = $this->person->date_birth;
//
//            $this->street_name = $this->person->street_name;
//            $this->brgy_nr     = $this->person->brgy_nr;
//            $this->mun_nr      = $this->person->mun_nr;
//
//            /* Encounter Mem Catergory Model */
//            /* If not current encounter found */
//            $encounter = $this->encounter;
//            if(!empty($encounter)) {
//                if(empty($encounter->encounterMemCategory)) {
//                    $encounter->encounterMemCategory = new EncounterMemcategory;
//                    $encounter->encounterMemCategory->encounter_nr = $encounter->encounter_nr;
//                }
//                $memCategory = Memcategory::model()->findByCode($this->member_type);
//                if(!empty($memCategory)) {
//                    $encounter->encounterMemCategory->memcategory_id = $memCategory->memcategory_id;
//                }
//                if(!$encounter->encounterMemCategory->save())
//                    return false;
//            }
//        }

        /* Other tables that should be updated for billing */
        /* PersonInsurance Model */

//        if(empty($this->personInsurance)) {
//            $this->personInsurance = new PersonInsurance;
//            $this->personInsurance->attributes = array(
//                'pid'          => $this->pid,
//                'hcare_id'     => $this->hcare_id,
//                'pid'          => $this->pid,
//            );
//        }

        // /* Added by jeff 03-02-18 for saving on recent encounter*/
        // Yii::import('eclaims.models.EclaimsPerson');
        // $personEncounter = EclaimsPerson::model()->findByPk($this->pid);
        // $this->personInsurance->encounter_nr = $personEncounter->recentEncounterInsurance->encounter_nr;

//        $this->personInsurance->create_id = $_SESSION['sess_user_name'];
//        $this->personInsurance->insurance_nr = $this->insurance_nr;
//
//        if(!$this->personInsurance->save())
//            return false;
//
//
//
//        $beforeSave = parent::beforeSave();
//
//        /* Set tracking History field */
//		$this->setTrackHistory();

        return true;
//        return $beforeSave;
    }

    /**
     * @author Jolly Caralos
     */
    private function setTrackHistory()
    {
        global $HTTP_SESSION_VARS;
        $_attr = $this->attributes;
        unset($_attr['history']);
        $_attr = Yii::app()->getUrlManager()->createPathInfo($_attr, '=', ',');

        if ($this->isNewRecord) {
            if ($this->hasAttribute('history')) {
                $this->history = "Created by " . $HTTP_SESSION_VARS['sess_login_username'] . " on " . date('Y-m-d H:i:s') . "\n"
                    . $_attr . "\n\n";
            }
        } else {
            if ($this->hasAttribute('history')) {
                $this->history = $this->history
                    . "Updated by " . $HTTP_SESSION_VARS['sess_login_username'] . " on " . date('Y-m-d H:i:s') . "\n"
                    . $_attr . "\n\n";
            }
        }
    }

    /**
     *
     * @return string
     */
    public function getFullName()
    {
        $name = '';
        if ($this->member_lname) {
            $name .= $this->member_lname;
        }

        if ($this->member_fname) {
            $name .= ', ' . $this->member_fname;
        }

        if ($this->member_mname) {
            $name .= ' ' . substr($this->member_mname, 0, 1) . '.';
        }

        if ($this->suffix) {
            $name .= ' ' . $this->suffix;
        }

        if ($name) {
            /* :not sure ?? */
            if (mb_detect_encoding($name) == 'UTF-8') {
                // return strtoupper(utf8_encode($name));
            }
            return strtoupper($name);
        } else {
            return null;
        }
    }

    /**
     *
     * @return string
     */
    public function getSex()
    {
        $sex = strtoupper($this->sex);
        if ($sex == 'M') {
            return 'Male';
        } else if ($sex == 'F') {
            return 'Female';
        } else {
            return null;
        }
    }

    public function setSex($value = null)
    {
        $this->sex = $value;
    }

    /**
     * Returns the full address of the member based on the assigned
     * barangay or municipality.
     *
     * @return string|null
     */
    public function getFullAddress()
    {
        if ($this->barangay) {
            return $this->barangay->getFullName();
        } elseif ($this->municipality) {
            return $this->municipality->getFullName();
        } else {
            return '';
        }
    }

    /**
     *
     * @return string|null
     */
    public function getZipCode()
    {
        if ($this->municipality)
            return $this->municipality->zipcode;
        else
            return '';
    }

    /**
     * Returns the code table for the various values to identify the
     * member's relation to the patient
     *
     * @return array
     */
    public static function getRelationTypes()
    {
        return array(
            'M' => 'Patient is member (Self)',
            'S' => 'Patient is spouse',
            'C' => 'Patient is child',
            'P' => 'Patient is parent',
        );
    }

    /**
     * Returns the code table for the various member types
     * @return array
     */
    public static function getMemberTypes()
    {
        return array(
            'S' => 'Private Employee',
            'G' => 'Government Employee',
            'I' => 'Indigent',
            'NS' => 'Individually Paying',
            'NO' => 'OFW',
            'PS' => 'Non Paying Private Employee',
            'PG' => 'Non Paying Government Employee',
        );
    }

    public static function getRequiredMemberTypeByEmployer()
    {
        return array('S', 'G');
    }

    public function getMemberType()
    {
        if (empty($this->member_type))
            return null;

        $_memberTypes = self::getMemberTypes();
        return isset($_memberTypes[$this->member_type]) ?
            $this->member_type : self::MEMBERTYPE_DEFAULT;
    }

    /**
     * Default: Indigent
     * @return string
     * Modified by JEFF 01-07-18 for fetching realtime member category type instead of static/default.
     */
    public function getMemberTypeDesc()
    {

        $memberCategory = Memcategory::model()->findByCode($this->member_type);

        if (empty($this->member_type)) {
            return null;
        } else if ($this->member_type) {
            return $this->person->currentEncounter->encounterMemCategory->memcategory_id = $memberCategory->memcategory_desc;
        } else {
            $_memberTypes = self::getMemberTypes();
            return isset($_memberTypes[$this->member_type]) ?
                $_memberTypes[$this->member_type] : $_memberTypes[self::MEMBERTYPE_DEFAULT];
        }
    }

    /**
     *
     * @return string
     */
    public function getMemberRelation()
    {
        switch ($this->relation) {
            case "M":
                return 'Self';
                break;
            case "S":
                return 'Spouse';
                break;
            case "C":
                return 'Child';
                break;
            case "P":
                return 'Parent';
                break;
        }

    }

    /**
     * @author Jolly Caralos
     */
    public static function getGenderTypes()
    {
        return array(
            'm' => 'Male',
            'f' => 'Female'
        );
    }

    /**
     * @author Jeff Ponteras 02-28-18
     * @return string or NULL
     */
    public function getPatientPin()
    {
        if ($this->patient_pin == self::defaultPIN) {
            return NULL;
        }
        return $this->patient_pin;
    }

    public function hasInsurance()
    {
        return !!$this->insurance_nr;
    }

}
