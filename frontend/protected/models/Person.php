<?php

/**
 * This is the model class for table "care_person".
 *
 * The followings are the available columns in table 'care_person':
 * @property string $pid
 * @property string $date_reg
 * @property string $name_first
 * @property string $name_2
 * @property string $name_3
 * @property string $name_middle
 * @property string $name_last
 * @property string $suffix
 * @property string $name_maiden
 * @property string $name_others
 * @property string $title
 * @property string $date_birth
 * @property string $birth_time
 * @property string $place_birth
 * @property string $blood_group
 * @property string $addr_str
 * @property string $addr_str_nr
 * @property string $addr_zip
 * @property integer $addr_citytown_nr
 * @property integer $addr_is_valid
 * @property string $street_name
 * @property string $brgy_nr
 * @property string $mun_nr
 * @property string $citizenship
 * @property string $occupation
 * @property string $employer
 * @property string $phone_1_code
 * @property string $phone_1_nr
 * @property string $phone_2_code
 * @property string $phone_2_nr
 * @property string $cellphone_1_nr
 * @property string $cellphone_2_nr
 * @property string $fax
 * @property string $email
 * @property string $civil_status
 * @property string $sex
 * @property string $photo
 * @property string $photo_filename
 * @property string $fpimage_filename
 * @property integer $ethnic_orig
 * @property string $org_id
 * @property string $sss_nr
 * @property string $nat_id_nr
 * @property string $religion
 * @property string $mother_pid
 * @property string $mother_fname
 * @property string $mother_maidenname
 * @property string $mother_mname
 * @property string $mother_lname
 * @property string $father_pid
 * @property string $father_fname
 * @property string $father_mname
 * @property string $father_lname
 * @property string $spouse_name
 * @property string $guardian_name
 * @property string $contact_person
 * @property string $contact_pid
 * @property string $contact_relation
 * @property string $death_date
 * @property string $death_time
 * @property string $death_encounter_nr
 * @property string $death_cause
 * @property string $death_cause_code
 * @property string $date_update
 * @property string $status
 * @property string $history
 * @property string $modify_id
 * @property string $modify_time
 * @property string $create_id
 * @property string $create_time
 * @property integer $fromtemp
 * @property integer $admitted_baby
 * @property string $senior_ID
 * @property string $veteran_ID
 * @property integer $is_indigent
 * @property string $DOH_ID
 * @property integer $age
 * @property string $name_search
 * @property string $soundex_namelast
 * @property string $soundex_namefirst
 * @property integer $is_temp_bdate
 * @property PhicMember2 $phicMember2
 */
class Person extends CareActiveRecord{


	const HCARE_ID = 18;

	/**
	 * @return string the associated database table name
	 */
	public function tableName(){

		return 'care_person';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('pid, name_first, name_middle, name_last, suffix, date_birth', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations(){
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'encounter'=>array(self::HAS_MANY, 'Encounter', 'pid'),
            'currentEncounter' => array(self::HAS_ONE, 'Encounter', 'pid'),
			'member' => array(self::HAS_ONE, 'PhicMember', 'pid','condition'=>'hcare_id = :hcare_id', 'params'=>array(':hcare_id'=>PhicMember::HCARE_ID)),
            'phicMember' => array(self::HAS_ONE, 'PhicMember', 'pid','condition'=>'hcare_id = :hcare_id', 'params'=>array(':hcare_id'=>PhicMember::HCARE_ID)),
            'barangay'=> array(self::BELONGS_TO, 'AddressBarangay', 'brgy_nr'),
            'municipality'=> array(self::BELONGS_TO, 'AddressMunicipality', 'mun_nr'),
            'work' => array(self::HAS_ONE,'Occupation', array('occupation_nr' => 'occupation')),
            'country' => array(self::HAS_ONE,'AddressCountry',array('country_code'=>'citizenship'))
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){

		return array(
			'pid' => 'HRN',
			'date_reg' => 'Date Reg',
			'name_first' => 'First Name',
			'name_middle' => 'Middle Name',
			'name_last' => 'Last Name',
			'suffix' => 'Suffix',
			'name_maiden' => 'Maiden Name',
			'name_others' => 'Other Names',
			'title' => 'Title',
			'date_birth' => 'Date of Birth',
			'birth_time' => 'Time of Birth',
			'place_birth' => 'Place of Birth',
			'blood_group' => 'Blood Group',
			'street_name' => 'Street Name',
			'brgy_nr' => 'Barangay',
			'mun_nr' => 'Municipality',
			'citizenship' => 'Citizenship',
			'occupation' => 'Occupation',
			'employer' => 'Employer',
            'history' => 'Brief History of Present Illness/OB History',
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Person the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}


    /**
     * Returns a list of
     * @param type $term
     */
    public static function search($term) {
        $criteria = new CDbCriteria();
		//$criteria->limit =  $limit;
        $criteria->order = 'name_last, name_first, name_middle';
        $terms = explode(',', $term, 2);

        // No lastname and firstname in the search query
        if (count($terms) == 1 && empty($term)) {
            return array();
        }

        $params = array();
        if (trim($terms[0]) !== '') {
            $criteria->addCondition('name_last LIKE :lastName');
            $params['lastName'] = trim($terms[0]).'%';
        }

        if (isset($terms[1]) && trim($terms[1]) !== '') {
            $criteria->addCondition('name_first LIKE :firstName');
            $params['firstName'] = trim($terms[1]).'%';
        }
        $criteria->params = $params;
		return self::model()->findAll($criteria);
    }


        /**
     * @param $name String
     * @author Jolly Caralos
     */
    private static function toArrayNameSuffix($name)
    {
        if(!empty($name)) {
            $splitName = preg_split('@([^/]+) (\w+)\.@', $name, 
                -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        }
        return empty($splitName) ? array() : $splitName;
    }

    public function getOccupation()
    {
        return $this->work->occupation_name;
    }


    /**
     *
     * @return string
     */
	public function getFullName() {
        $name = '';
        if ($this->name_last) {
            $name .= $this->name_last;
        }

        if ($this->name_first) {
            $name .= ', ' . $this->name_first;
        }

        if ($this->name_middle) {
            $name .= ' ' . substr($this->name_middle,0,1) . '.';
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
	public function getSex() {
		if (strtoupper($this->sex)=='F'){
			return 'Female';
		} else {
			return 'Male';
		}
	}

    /**
     *
     * @return int
     */
	public function getPID(){
		if(empty($this->pid)){
			return null;
		} else{
			return $this->pid;
		}
	}

    /**
     * Returns the current age of the person in years
     * @return int
     */
	public function getAge(){
        date_default_timezone_set('Asia/Manila');
		$datetime1 = new DateTime($this->date_birth);
		$datetime2 = new DateTime();
		$diff = $datetime1->diff($datetime2);
		if ($diff->y) {
            return $diff->y . ' year/s old';
        } elseif ($diff->m) {
            return $diff->m . ' month/s old';
        } else {
            return $diff->d . ' day/s old';
        }
	}




    /**
     *
     * @param string $time
     * @param string $format
     * @return string
     */
    protected function formatDateValue($time='now', $format='YmdHis') {
        date_default_timezone_set('Asia/Manila');
        $dt = new DateTime($time);
        return $dt->format($format);
    }

    /**
     * Returns an array of this person's related patient information
     * @return array
     */
    public function getPatientInfo($options=array(), $is_or = false) {
        $date_format = 'Y-m-d';
        if($is_or){
            $date_format = 'F m, Y';
        }
        $options = CMap::mergeArray(array(
            'dateFormat' => $date_format
        ), $options);
        $encounter = $this->currentEncounter ? $this->currentEncounter : new Encounter;
        
        return array(
            'id' => $this->pid,
            'lastName' => strtoupper($this['name_last']),
            'firstName' => strtoupper($this['name_first']),
            'middleName' => strtoupper($this['name_middle']),
            'fullName' => $this->getFullName(),
            'sex' => strtoupper($this['sex']),
            'age' => $this->getAge(),
            'birthDate' => $this->formatDateValue($this['date_birth'], $options['dateFormat']),
            'encounterNr' => $encounter->encounter_nr,
            'patientType' =>  $encounter->getEncounterType(),
            'department' => $encounter->getDepartmentName(),
        );
    }



    /**
     * @author Jolly Caralos
     */
    public function getNameFirst()
    {
        if(!empty($this->name_first)) {
            /* Is suffix exists in firstname? */
            $splitName = self::toArrayNameSuffix($this->name_first);

            return empty($splitName[0]) ? $this->name_first : $splitName[0];
        }
        return $this->name_first;
    }

    public function getSuffix()
    {
        /* Is suffix exists in firstname? */
        $splitName = self::toArrayNameSuffix($this->name_first);

        return empty($splitName[1]) ? '' : $splitName[1];
    }

    public function getSuffixForXml()
    {
        return $this->suffix;
    }


    public function getAddress(){
        $barangay = AddressBarangay::model()->findByPk($this->brgy_nr);
        $municipality = AddressMunicipality::model()->findByPk($this->mun_nr);
        return $this->street_name.", ".ucfirst(strtolower($barangay->brgy_name)).", ".ucfirst(strtolower($municipality->mun_name));
    }

    /**
     * Returns the full address of the member based on the assigned
     * barangay or municipality.
     *
     * @return string|null
     */
    public function getFullAddress()
    {
            if ($this->street_name) {
                $address = $this->street_name . ' ';
            } else {
                $address = '';
            }
            if ($this->barangay) {
                return $address . $this->barangay->getFullName();
            } elseif ($this->municipality) {
                return $address . $this->municipality->getFullName();
            } else {
                return $address;
            }
    }

}
