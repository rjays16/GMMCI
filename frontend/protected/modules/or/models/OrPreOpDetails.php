<?php

/**
 * This is the model class for table "seg_or_pre_op_details".
 *
 * The followings are the available columns in table 'seg_or_pre_op_details':
 * @property string $or_refno
 * @property string $operation_date
 * @property string $est_length_op
 * @property string $case_classification
 * @property string $pre_op_diagnosis
 * @property string $blood_pressure
 * @property string $temperature
 * @property string $pulse
 * @property string $respiration
 * @property string $create_id
 * @property string $create_date
 * @property string $modify_id
 * @property string $modify_date
 * @property string $history
 *
 * The followings are the available model relations:
 * @property OrChecklistPreopData[] $orChecklistPreopDatas
 * @property OrRequest $orRefno
 * @property OrSurgicalTeam[] $orSurgicalTeams
 */
class OrPreOpDetails extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_or_pre_op_details';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('or_refno,operation_date', 'required'),
			array('or_refno, create_id, modify_id', 'length', 'max'=>12),
			array('est_length_op, blood_pressure, temperature, pulse, respiration', 'length', 'max'=>20),
			array('case_classification', 'length', 'max'=>30),
			array('operation_date, pre_op_diagnosis, create_date, history', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('or_refno, operation_date, est_length_op, case_classification, pre_op_diagnosis, blood_pressure, temperature, pulse, respiration, create_id, create_date, modify_id, modify_date, history', 'safe', 'on'=>'search'),
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
            'orChecklists' => array(self::MANY_MANY, 'OrChecklist', 'seg_or_checklist_preop_data(refno, checklist_id)'),
			'orRefno' => array(self::BELONGS_TO, 'OrRequest', 'or_refno'),
			'orSurgicalTeams' => array(self::HAS_MANY, 'OrSurgicalTeam', 'or_refno'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'or_refno' => 'Or Refno',
			'operation_date' => 'Operation Date',
			'est_length_op' => 'Estimated Length of Operation',
			'case_classification' => 'Case Classification',
			'pre_op_diagnosis' => 'Pre Operation Diagnosis',
			'blood_pressure' => 'Blood Pressure',
			'temperature' => 'Temperature',
			'pulse' => 'Pulse',
			'respiration' => 'Respiration',
			'create_id' => 'Create',
			'create_date' => 'Create Date',
			'modify_id' => 'Modify',
			'modify_date' => 'Modify Date',
			'history' => 'History',
            'orChecklists'=>'Pre Operative Requirements'
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
		$criteria->compare('operation_date',$this->operation_date,true);
		$criteria->compare('est_length_op',$this->est_length_op,true);
		$criteria->compare('case_classification',$this->case_classification,true);
		$criteria->compare('pre_op_diagnosis',$this->pre_op_diagnosis,true);
		$criteria->compare('blood_pressure',$this->blood_pressure,true);
		$criteria->compare('temperature',$this->temperature,true);
		$criteria->compare('pulse',$this->pulse,true);
		$criteria->compare('respiration',$this->respiration,true);
		$criteria->compare('create_id',$this->create_id,true);
		$criteria->compare('create_date',$this->create_date,true);
		$criteria->compare('modify_id',$this->modify_id,true);
		$criteria->compare('modify_date',$this->modify_date,true);
		$criteria->compare('history',$this->history,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return OrPreOpDetails the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function getOperationDateText(){
        return (isset($this->operation_date) && ($this->operation_date !== "0000-00-00 00:00:00"))?date("F d, Y H:i A",strtotime($this->operation_date)):"Not Specified";
    }

    public function behaviors(){
        return array( 'CAdvancedArBehavior' => array(
            'class' => 'application.extensions.CAdvancedArBehavior'));
    }
}
