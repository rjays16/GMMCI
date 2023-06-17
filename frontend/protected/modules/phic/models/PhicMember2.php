<?php

/**
 * This is the model class for table "seg_insurance_member_info".
 *
 * The followings are the available columns in table 'seg_insurance_member_info':
 * @property string $pid
 * @property string $hcare_id
 * @property string $insurance_nr
 * @property string $member_lname
 * @property string $member_fname
 * @property string $member_mname
 * @property string $suffix
 * @property string $birth_date
 * @property string $street_name
 * @property string $brgy_nr
 * @property string $mun_nr
 * @property string $relation
 * @property string $member_type
 * @property string $employer_no
 * @property string $employer_name
 * @property string $create_date
 * @property string $sex
 * @property AddressBarangay $barangay
 * @property AddressMunicipality $municipality
 *
 */

Yii::import('application.models.address.AddressBarangay');
Yii::import('application.models.address.AddressMunicipality');


class PhicMember2 extends CareActiveRecord
{
    const HCARE_ID = 18;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_insurance_member_info';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array(
                'pid, hcare_id, member_type , relation, member_lname, member_fname, sex, birth_date', 
                'required',
                'message' => "{attribute} cannot be blank.",
            ),
            array('pid , patient_pin', 'length', 'max' => 12),
            array('hcare_id', 'length', 'max' => 8),
            array('insurance_nr, employer_no', 'length', 'max' => 25),
            array('member_lname, member_fname, member_mname, street_name', 'length', 'max' => 150),
            array('suffix', 'length', 'max' => 10),
            array('brgy_nr, mun_nr', 'length', 'max' => 11),
            array('relation', 'length', 'max' => 1),
            array('member_type', 'length', 'max' => 5),
            array('birth_date, sex, employer_name', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('pid, hcare_id, insurance_nr, member_lname, member_fname, member_mname, suffix, birth_date, street_name, brgy_nr, mun_nr, relation, member_type, employer_no, employer_name, create_date', 'safe', 'on' => 'search'),
            array('member_type, relation, member_lname, member_fname, insurance_nr', 'required', 'on' => 'requirementsForAddInsurance')
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'person' => array(self::BELONGS_TO, 'Person', 'pid'),
            'barangay' => array(self::BELONGS_TO, 'AddressBarangay', 'brgy_nr'),
            'municipality' => array(self::BELONGS_TO, 'AddressMunicipality', 'mun_nr'),
            'hcare' => array(self::BELONGS_TO, 'InsuranceProvider', 'hcare_id'),
            'personInsurance' => array(self::BELONGS_TO, 'PersonInsurance', array('pid' => 'pid', 'hcare_id' => 'hcare_id')),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'pid' => 'Pid',
            'hcare_id' => 'Hcare',
            'insurance_nr' => 'Member PIN',
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
            'create_date' => 'Create Date',
            'sex' => 'Sex',
            'patient_pin' => 'Patient PIN'
        );
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
        /* Other tables that should be updated for billing */
        /* PersonInsurance Model */
        if (empty($this->personInsurance)) {
            $this->personInsurance = new PersonInsurance;
            $this->personInsurance->attributes = array(
                'pid' => $this->pid,
                'hcare_id' => $this->hcare_id,
            );
        }

        return parent::beforeSave();
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
            return strtoupper($name);
        } else {
            return null;
        }
    }

    /**
     *
     * @return string
     */
    public function getMemberType()
    {
        if (empty($this->member_type))
            return null;

        switch ($this->member_type) {
            case 'S':
                return 'Private Employee';
                break;
            case 'G':
                return 'Government Employee';
                break;
            case 'I':
                return 'Indigent';
                break;
            case 'NS':
                return 'Individually Paying';
                break;
            case 'NO':
                return 'OFW';
                break;
            case 'PS':
                return 'Non Paying Private Employee';
                break;
            case 'PG':
                return 'Non Paying Government Employee';
                break;
            default:
                return 'NS';
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
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Member the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
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
            'S' => 'Employed Private',
            'G' => 'Employed Government',
            'I' => 'Indigent',
            'NS' => 'Individually Paying',
            'NO' => 'OFW',
            'PS' => 'Non Paying Private',
            'PG' => 'Non Paying Government',
        );
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
     * @return [type]
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
                $_memberTypes[$this->member_type] : $_memberTypes['I'];
        }
    }

}
