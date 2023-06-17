<?php

/**
 * This is the model class for table "care_personell".
 *
 * The followings are the available columns in table 'care_personell':
 * @property integer $nr
 * @property string $short_id
 * @property string $pid
 * @property integer $job_type_nr
 * @property string $job_function_title
 * @property string $job_position
 * @property string $date_join
 * @property string $date_exit
 * @property string $contract_class
 * @property string $contract_start
 * @property string $contract_end
 * @property integer $is_discharged
 * @property string $pay_class
 * @property string $pay_class_sub
 * @property string $local_premium_id
 * @property string $tax_account_nr
 * @property string $ir_code
 * @property integer $nr_workday
 * @property double $nr_weekhour
 * @property integer $nr_vacation_day
 * @property integer $multiple_employer
 * @property integer $nr_dependent
 * @property string $status
 * @property string $history
 * @property string $modify_id
 * @property string $modify_time
 * @property string $create_id
 * @property string $create_time
 * @property string $license_nr
 * @property string $prescription_license_nr
 * @property string $tin
 * @property integer $is_resident_dr
 * @property string $tier_nr
 * @property string $other_title
 * @property integer $ward_nr
 * @property integer $is_reliever
 * @property string $newpid
 * @property integer $is_housecase_attdr
 * @property integer $is_housecase_surgeon
 * @property integer $is_housecase_anesth
 * @property string $ptr_nr
 * @property string $s2_nr
 * @property string $doctor_role
 * @property string $doctor_level
 *
 */

class Personnel extends CActiveRecord{

	const STATUS_DELETED = 'deleted';
    const TITLE_DOCTOR = 'Doctor';
    const TITLE_CONSULTING_DOCTOR = 'Consulting doctor';

	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'care_personell';
	}

	/**
	 * @return array validation rules for model attributes.
	 */

	public function rules(){
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('pay_class, pay_class_sub, local_premium_id, tax_account_nr, ir_code, status, history, modify_id, modify_time, create_id, tier_nr', 'required'),
			array('job_type_nr, is_discharged, nr_workday, nr_vacation_day, multiple_employer, nr_dependent, is_resident_dr, ward_nr, is_reliever, is_housecase_attdr, is_housecase_surgeon, is_housecase_anesth', 'numerical', 'integerOnly'=>true),
			array('nr_weekhour', 'numerical'),
			array('short_id, tier_nr, doctor_level', 'length', 'max'=>10),
			array('pid, newpid', 'length', 'max'=>12),
			array('job_function_title, tax_account_nr', 'length', 'max'=>60),
			array('job_position', 'length', 'max'=>100),
			array('contract_class, modify_id, create_id', 'length', 'max'=>35),
			array('pay_class, pay_class_sub, ir_code, status', 'length', 'max'=>25),
			array('local_premium_id', 'length', 'max'=>5),
			array('license_nr, prescription_license_nr, tin, ptr_nr, s2_nr', 'length', 'max'=>20),
			array('other_title', 'length', 'max'=>50),
			array('doctor_role', 'length', 'max'=>6),
			array('date_join, date_exit, contract_start, contract_end, create_time', 'safe'),
			array('nr, short_id, pid, job_type_nr, job_function_title, job_position, date_join, date_exit, contract_class, contract_start, contract_end, is_discharged, pay_class, pay_class_sub, local_premium_id, tax_account_nr, ir_code, nr_workday, nr_weekhour, nr_vacation_day, multiple_employer, nr_dependent, status, history, modify_id, modify_time, create_id, create_time, license_nr, prescription_license_nr, tin, is_resident_dr, tier_nr, other_title, ward_nr, is_reliever, newpid, is_housecase_attdr, is_housecase_surgeon, is_housecase_anesth, ptr_nr, s2_nr, doctor_role, doctor_level', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations(){
		return array(
			'person' => array(self::BELONGS_TO, 'Person', 'pid'),
			'assignment'=>array(self::HAS_ONE,'PersonnelAssignment','personell_nr', 'condition'=>'status!=:status', 'params'=>array(':status'=>PersonnelAssignment::STATUS_DELETED)),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){
		return array(
			'nr' => 'Nr',
			'short_id' => 'Short',
			'pid' => 'Pid',
			'job_type_nr' => 'Job Type Nr',
			'job_function_title' => 'Job Function Title',
			'job_position' => 'Job Position',
			'date_join' => 'Date Join',
			'date_exit' => 'Date Exit',
			'contract_class' => 'Contract Class',
			'contract_start' => 'Contract Start',
			'contract_end' => 'Contract End',
			'is_discharged' => 'Is Discharged',
			'pay_class' => 'Pay Class',
			'pay_class_sub' => 'Pay Class Sub',
			'local_premium_id' => 'Local Premium',
			'tax_account_nr' => 'Tax Account Nr',
			'ir_code' => 'Ir Code',
			'nr_workday' => 'Nr Workday',
			'nr_weekhour' => 'Nr Weekhour',
			'nr_vacation_day' => 'Nr Vacation Day',
			'multiple_employer' => 'Multiple Employer',
			'nr_dependent' => 'Nr Dependent',
			'status' => 'Status',
			'history' => 'History',
			'modify_id' => 'Modify',
			'modify_time' => 'Modify Time',
			'create_id' => 'Create',
			'create_time' => 'Create Time',
			'license_nr' => 'License Nr',
			'prescription_license_nr' => 'Prescription License Nr',
			'tin' => 'Tin',
			'is_resident_dr' => 'Is Resident Dr',
			'tier_nr' => 'Tier Nr',
			'other_title' => 'Other Title',
			'ward_nr' => 'Ward Nr',
			'is_reliever' => 'Is Reliever',
			'newpid' => 'Newpid',
			'is_housecase_attdr' => 'Is Housecase Attdr',
			'is_housecase_surgeon' => 'Is Housecase Surgeon',
			'is_housecase_anesth' => 'Is Housecase Anesth',
			'ptr_nr' => 'Ptr Nr',
			's2_nr' => 'S2 Nr',
			'doctor_role' => 'Doctor Role',
			'doctor_level' => 'Doctor Level',
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Personel the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}

	/*
	* Virtual attribute for getting the doctor's department,
	* that would return null if the doctor was not assigned
	* to a department
	*/
	public function getDepartmentName(){
        if(!empty($this->assignment->department)){
             return $this->assignment->department->name_formal;
		} else {
			return null;
        }
    }

    /**
     *
     */
    public function toArray() {
        $person = $this->person;
        if (empty($person)) {
            $person = new Person;
        }

        $result = array(
            'id' => $this->nr,
            'lastName' => $person->name_last,
            'firstName' => $person->name_first,
            'middleName' => $person->name_middle,
            'fullName' => $person->getFullName(),
            'sex' => $person->sex,
            'department' => $this->getDepartmentName(),
            'title' => $this->job_function_title
        );

        array_walk($result, function(&$value, $key) {
            if (empty($value)) {
                $value = '';
            }
            $value = strtoupper($value);
        });

        return $result;
    }

    /**
     *
     * @param string $term
     * @param int $limit
     * @return Personnel[]
     */
	public static function search($term, $limit=10){
		$criteria = new CDbCriteria();
		$criteria->with = 'person';
		$criteria->limit =  $limit;

        $terms = explode(',', $term, 2);

        // No lastname and firstname in the search query
        if (sizeof($terms) == 0) {
            return array();
        }

        $params = array();
        if (trim($terms[0]) !== '') {
            $criteria->addCondition('person.name_last LIKE :lastName');
            $params['lastName'] = trim($terms[0]).'%';
        }

        if (isset($terms[1]) && trim($terms[1]) !== '') {
            $criteria->addCondition('person.name_first LIKE :firstName');
            $params['firstName'] = trim($terms[1]).'%';
        }


        /*
		* Commented by jeff 01-13-18 for this line can only fetched doctors.
        */
        // $criteria->params = $params;
        // $criteria->compare('job_function_title', self::TITLE_DOCTOR);

        /**
		* Added by jeff 01-13-18 for fetching also consulting doctors.
        */
        $criteria->params = $params;
        $criteria->addInCondition('job_function_title', array (self::TITLE_DOCTOR, self::TITLE_CONSULTING_DOCTOR),'AND');
		return self::model()->findAll($criteria);
	}
}
