<?php

/**
 * This is the model class for table "care_encounter".
 *
 * The followings are the available columns in table 'care_encounter':
 * @property string $encounter_nr
 * @property string $pid
 * @property string $encounter_date
 * @property integer $encounter_class_nr
 * @property integer $encounter_type
 * @property string $encounter_status
 * @property string $official_receipt_nr
 * @property string $er_opd_diagnosis
 * @property integer $consulting_dept_nr
 * @property integer $consulting_dr_nr
 * @property string $referrer_diagnosis
 * @property string $referrer_recom_therapy
 * @property string $referrer_dr
 * @property string $referrer_dept
 * @property string $referrer_institution
 * @property string $referrer_notes
 * @property integer $financial_class_nr
 * @property string $insurance_nr
 * @property string $insurance_firm_id
 * @property integer $insurance_class_nr
 * @property string $insurance_2_nr
 * @property string $insurance_2_firm_id
 * @property integer $guarantor_pid
 * @property integer $contact_pid
 * @property string $contact_relation
 * @property integer $current_ward_nr
 * @property integer $current_room_nr
 * @property integer $in_ward
 * @property string $area
 * @property integer $current_dept_nr
 * @property integer $in_dept
 * @property integer $current_firm_nr
 * @property integer $current_att_dr_nr
 * @property string $consulting_dr
 * @property string $extra_service
 * @property string $admission_dt
 * @property integer $is_discharged
 * @property string $discharge_date
 * @property string $discharge_time
 * @property string $followup_date
 * @property string $followup_responsibility
 * @property string $post_encounter_notes
 * @property string $informant_name
 * @property string $info_address
 * @property string $relation_informant
 * @property string $occupation
 * @property string $source_income
 * @property integer $monthly_income
 * @property integer $nr_dependents
 * @property string $status
 * @property string $history
 * @property string $modify_id
 * @property string $modify_time
 * @property string $create_id
 * @property string $create_time
 * @property integer $is_medico
 * @property integer $is_confidential
 * @property string $POI
 * @property string $TOI
 * @property string $DOI
 * @property integer $is_DOA
 * @property string $is_DOA_reason
 * @property integer $category
 * @property integer $is_served
 * @property string $date_served
 * @property string $clerk_served_by
 * @property string $clerk_served_date
 * @property string $clerk_served_history
 * @property integer $is_maygohome
 * @property string $mgh_setdte
 * @property string $parent_encounter_nr
 * @property string $chief_complaint
 * @property string $received_date
 * @property string $smoker_history
 * @property string $drinker_history
 */
class Encounter extends CareActiveRecord
{
	const EMPTY_DATE = '0000-00-00';
	public static $_totalEncounter = null; // added by JOY @ 02-21-2018

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'care_encounter';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array();
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
			'department' => array(self::BELONGS_TO, 'Department', 'consulting_dept_nr'),
			'type' => array(self::BELONGS_TO, 'EncounterType', 'encounter_type'),
            'disposition' => array(self::HAS_ONE, 'EncounterDisposition', 'encounter_nr'),
            'result' => array(self::HAS_ONE, 'EncounterResult', 'encounter_nr'),
            'encounterMemCategory' => array(self::HAS_ONE, 'EncounterMemcategory', 'encounter_nr'),
            'billingEncounter' => array(self::HAS_ONE, 'BillingEncounter' , 'encounter_nr')

		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'encounter_nr' => 'Encounter Number',
			'pid' => 'Pid',
			'encounter_date' => 'Encounter Date',
			'encounter_class_nr' => 'Encounter Class Nr',
			'encounter_type' => 'Encounter Type',
			'encounter_status' => 'Encounter Status',
			'official_receipt_nr' => 'Official Receipt Nr',
			'er_opd_diagnosis' => 'Er Opd Diagnosis',
			'consulting_dept_nr' => 'Consulting Dept Nr',
			'consulting_dr_nr' => 'Consulting Dr Nr',
			'referrer_diagnosis' => 'Referrer Diagnosis',
			'referrer_recom_therapy' => 'Referrer Recom Therapy',
			'referrer_dr' => 'Referrer Dr',
			'referrer_dept' => 'Referrer Dept',
			'referrer_institution' => 'Referrer Institution',
			'referrer_notes' => 'Referrer Notes',
			'financial_class_nr' => 'Financial Class Nr',
			'insurance_nr' => 'Insurance Nr',
			'insurance_firm_id' => 'Insurance Firm',
			'insurance_class_nr' => 'Insurance Class Nr',
			'insurance_2_nr' => 'Insurance 2 Nr',
			'insurance_2_firm_id' => 'Insurance 2 Firm',
			'guarantor_pid' => 'Guarantor Pid',
			'contact_pid' => 'Contact Pid',
			'contact_relation' => 'Contact Relation',
			'current_ward_nr' => 'Current Ward Nr',
			'current_room_nr' => 'Current Room Nr',
			'in_ward' => 'In Ward',
			'area' => 'Area',
			'current_dept_nr' => 'Current Dept Nr',
			'in_dept' => 'In Dept',
			'current_firm_nr' => 'Current Firm Nr',
			'current_att_dr_nr' => 'Current Att Dr Nr',
			'consulting_dr' => 'Consulting Dr',
			'extra_service' => 'Extra Service',
			'admission_dt' => 'Admission Date',
			'is_discharged' => 'Is Discharged',
			'discharge_date' => 'Discharge Date',
			'discharge_time' => 'Discharge Time',
			'followup_date' => 'Followup Date',
			'followup_responsibility' => 'Followup Responsibility',
			'post_encounter_notes' => 'Post Encounter Notes',
			'informant_name' => 'Informant Name',
			'info_address' => 'Info Address',
			'relation_informant' => 'Relation Informant',
			'occupation' => 'Occupation',
			'source_income' => 'Source Income',
			'monthly_income' => 'Monthly Income',
			'nr_dependents' => 'Nr Dependents',
			'status' => 'Status',
			'history' => 'History',
			'modify_id' => 'Modify',
			'modify_time' => 'Modify Time',
			'create_id' => 'Create',
			'create_time' => 'Create Time',
			'is_medico' => 'Is Medico',
			'is_confidential' => 'Is Confidential',
			'POI' => 'Poi',
			'TOI' => 'Toi',
			'DOI' => 'Doi',
			'is_DOA' => 'Is Doa',
			'is_DOA_reason' => 'Is Doa Reason',
			'category' => 'Category',
			'is_served' => 'Is Served',
			'date_served' => 'Date Served',
			'clerk_served_by' => 'Clerk Served By',
			'clerk_served_date' => 'Clerk Served Date',
			'clerk_served_history' => 'Clerk Served History',
			'is_maygohome' => 'Is Maygohome',
			'mgh_setdte' => 'Mgh Setdte',
			'parent_encounter_nr' => 'Parent Encounter Nr',
			'chief_complaint' => 'Chief Complaint '.'<span style="color:red;"> *</span>',
			'received_date' => 'Received Date',
			'smoker_history' => 'Smoker History',
			'drinker_history' => 'Drinker History',
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Encounter the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function scopes()
    {
    	$alias = $this->tableAlias;
        return array(
            'active' => array(
                'condition' => "{$alias}.is_discharged IS NULL OR {$alias}.is_discharged = 0"
            ),
            'notBilled' => array(
            	'with' => 'billingEncounter',
            	'condition' => 'billingEncounter.is_final is null'
            )
        );
    }

	/**
	 * This function is used to retrieve the department where the patient is located
	 *
	 * @return string
	*/
	public function getDepartmentName(){
		if ($this->department) {
		return $this->department->name_formal;
		} else {
			return '';
	}
	}

	/**
	 * Returns the type of encounter the patient is registered as
	 * @return string
	*/
	public function getEncounterType(){
		if ($this->type) {
		return $this->type->name;
		} else {
			return '';
		}
	}

	// created by JOY @ 02-21-2018
	 public static function getActiveCaseNos($pid='', $searchKey='', $pagination=false)
    {   
        /** @var CDbCommand $command */
        $command = Yii::app()->db->createCommand();
        $command->select(array(
            'ce.encounter_nr',
            'ct.type AS enc_type',
            'IF(
            ce.admission_dt,
            ce.admission_dt,
            ce.encounter_date
          	) AS admission_dt',
          	'IF(
            ce.is_discharged=1,
            CONCAT(ce.discharge_date,"",ce.discharge_time),
            "") AS discharge_time'
        ));
        $command->from('care_encounter ce');
        $command->join('care_person p', 'ce.pid=p.pid');
        $command->leftJoin('care_type_encounter ct', 'ce.encounter_type=ct.type_nr');
        $command->where("ce.is_discharged=0 AND ce.pid=:pid");
        $command->params[':pid'] = $pid;
    
        $summaryCommand = clone $command;
        $summaryCommand->select = "COUNT(*)";
        $filterSummaryCommand = clone $summaryCommand;

        self::$_totalEncounter = $summaryCommand->queryScalar();
        
        // Apply filters here
        if ($searchKey) {
            $searchKeyFilter = "ce.encounter_nr =:key";
            $searchKeyParams =  array(':key' => $searchKey);
            $command->andWhere($searchKeyFilter, $searchKeyParams);
            $filterSummaryCommand->andWhere($searchKeyFilter, $searchKeyParams);
        }
        
        $command->group = array('ce.encounter_nr'); 
        $command->order = array('ce.encounter_date DESC');
        if ($pagination['limit'] !== null && $pagination['offset'] !== null) {
            $command->limit($pagination['limit'], $pagination['offset']);
            $pagination = false;
        }

        return new CSqlDataProvider($command, array(
            'totalItemCount' => $summaryCommand->queryScalar(),
            'pagination'=> $pagination,
        ));
    } // end by JOY

    // created by JOY @ 02-22-2018 
    public function getTotalEncounter(){
    	return self::$_totalEncounter;
    }
}
