<?php

/**
 * This is the model class for table "seg_or_request".
 *
 * The followings are the available columns in table 'seg_or_request':
 * @property string $or_refno
 * @property string $encounter_nr
 * @property integer $trans_type
 * @property integer $is_urgent
 * @property string $dept_nr
 * @property string $dr_nr
 * @property string $or_type
 * @property string $or_case
 * @property string $request_flag
 * @property string $date_requested
 * @property string $requirements
 * @property string $create_id
 * @property string $create_date
 * @property string $modify_date
 * @property string $modify_id
 * @property string $history
 * @property string $remarks
 *
 * The followings are the available model relations:
 * @property OrChecklist[] $segOrChecklists
 * @property OrPostOpDetails $orPostOpDetails
 * @property OrPreOpDetails $orPreOpDetails
 */
Yii::import('or.models.OrType');
Yii::import('or.models.Pay');

class OrRequest extends CActiveRecord
{

    const TYPE_CASH=0;
    const TYPE_CHARGE=1;

    const TYPE_MINOR='minor';
    const TYPE_MAJOR='major';

	/**
	 * @return string the associated database table name
	 */

    public $patient_name;
    public $patient_gender;
    public $patient_age;
    public $patient_address;
    public $personnel_name;
    public $patient_search_text;
    public $package_search_text;
    public $orChecklist;

    public $checklist = array();
    public $surgery_type;
    public $amount;

	public function tableName()
	{
		return 'seg_or_request';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('patient_name, orChecklists, surgery_type, amount, date_requested', 'required'),
            array('date_requested', 'dateValidator'),
			array('is_urgent', 'numerical', 'integerOnly'=>true),
			array('or_refno, encounter_nr, dr_nr, or_case', 'length', 'max'=>12),
			array('dept_nr, or_type', 'length', 'max'=>5),
            array('request_flag', 'length', 'max'=>8),
			array('remarks', 'length', 'max'=>300),
			array('create_id, modify_id', 'length', 'max'=>20),
			array('date_requested, requirements, create_date, history, patient_gender, patient_age, patient_address, checklist, trans_type, orPackageUses, date_requested', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('or_refno, encounter_nr, trans_type, is_urgent, dept_nr, dr_nr, or_type, or_case, request_flag, date_requested, requirements, create_id, create_date, modify_date, modify_id, history, remarks', 'safe', 'on'=>'search'),
		);
	}

    public function dateValidator($attribute)
    {
        if($this->$attribute < date('Y-m-d', '2000-01-01'))
            $this->addError($attribute, 'Invalid date');
    }

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'orChecklists' => array(self::MANY_MANY, 'OrChecklist', 'seg_or_checklist_request_data(refno, checklist_id)'),
			'orPostOpDetail' => array(self::HAS_ONE, 'OrPostOpDetails', 'or_refno'),
			'orPreOpDetail' => array(self::HAS_ONE, 'OrPreOpDetails', 'or_refno'),
            'orPackageUses' => array(self::HAS_MANY, 'OrPackageUse', 'or_refno'),
			'orAnesthesiaUses' => array(self::HAS_MANY, 'OrAnesthesiaUse', 'or_refno'),
			'department' => array(self::BELONGS_TO, 'Department', 'dept_nr'),
			'encounter' => array(self::BELONGS_TO, 'Encounter', 'encounter_nr'),
            'cashierLink' => array(self::HAS_ONE, 'OrDeposit', 'refno', 'condition'=> 'cashierLink.status = "paid"'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'or_refno' => 'Or Refno',
			'encounter_nr' => 'Encounter Nr',
			'trans_type' => 'Transaction',
			'is_urgent' => 'Urgent?',
			'dept_nr' => 'Department',
			'dr_nr' => 'Dr Nr',
			'or_type' => 'Or Type',
			'or_case' => 'Or Case',
			'request_flag' => 'Request Flag',
			'date_requested' => 'Date Requested',
			'requirements' => 'Requirements',
			'create_id' => 'Create',
			'create_date' => 'Create Date',
			'modify_date' => 'Modify Date',
			'modify_id' => 'Modify',
			'history' => 'History',
            'patient_name'=>'Patient Name',
            'patient_gender'=>'Gender',
            'patient_age'=>'Age',
            'patient_address'=>'Address',
            'orChecklists'=>'Accomplished Requirements',
            'personnel_name' => 'Requestor',
            'remarks' => 'Remarks'
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

		$criteria=new CDbCriteria;

		$criteria->compare('or_refno',$this->or_refno,true);
		$criteria->compare('encounter_nr',$this->encounter_nr,true);
		$criteria->compare('trans_type',$this->trans_type);
		$criteria->compare('is_urgent',$this->is_urgent);
		$criteria->compare('dept_nr',$this->dept_nr,true);
		$criteria->compare('dr_nr',$this->dr_nr,true);
		$criteria->compare('or_type',$this->or_type,true);
		$criteria->compare('or_case',$this->or_case,true);
		$criteria->compare('request_flag','pending',true);
		$criteria->compare('date_requested',$this->date_requested,true);
		$criteria->compare('requirements',$this->requirements,true);
		$criteria->compare('create_id',$this->create_id,true);
		$criteria->compare('create_date',$this->create_date,true);
		$criteria->compare('modify_date',$this->modify_date,true);
		$criteria->compare('modify_id',$this->modify_id,true);
        $criteria->compare('history',$this->history,true);
		$criteria->compare('remarks',$this->remarks,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return OrRequest the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function getTypeOptions(){
        return array(
            self::TYPE_CHARGE=>'Charge'
        );
    }

    public function getSurgeryTypeOptions(){
        return array(
            self::TYPE_MINOR=>'Minor',
            self::TYPE_MAJOR=>'Major'
        );
    }

    public function getTypeText(){
        $typeOptions = $this->typeOptions;
        return isset($typeOptions[$this->trans_type])?$typeOptions[$this->trans_type]:"unknown type ({$this->trans_type})";
    }

    public function getUrgentText(){
        return ($this->is_urgent == 1)?"Yes":"No";
    }

    public function getDateRequestedText(){
        return (isset($this->date_requested) && ($this->date_requested !== "0000-00-00 00:00:00"))?date("F d, Y h:i A",strtotime($this->date_requested)):"Not Specified";
    }

    public function getDateRequestedDateText(){
        return (isset($this->date_requested) && ($this->date_requested !== "0000-00-00 00:00:00"))?date("F d, Y",strtotime($this->date_requested)):"Not Specified";
    }

    public function behaviors(){
        return array( 'CAdvancedArBehavior' => array(
            'class' => 'application.extensions.CAdvancedArBehavior'));
    }

    public function getOrChecklist()
    {
        if (!isset($this->orChecklist))
        {
            $this->orChecklist = array();
            foreach ($this->orChecklists as $orChecklists)
                $this->orChecklist[] = $orChecklists->checklist_id;
        }

        return $this->orChecklist;
    }

    public function getCashierOr(){
        if(empty($this->cashierLink))
            return "Not paid";
        else
            return $this->cashierLink->or_no;
        // $payModel = Pay::model()->findPaidOr($this->encounter_nr, $this->encounter->pid);
        // if(empty($payModel))
        //     return "Not paid";
        // else
        //     return $payModel->or_no;
    }
}
