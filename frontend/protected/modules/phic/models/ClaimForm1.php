<?php

/**
 * This is the model class for table "seg_cf1".
 *
 * The followings are the available columns in table 'seg_cf1':
 * @property string $member_info_id
 * @property integer $signatory_is_representative
 * @property string $signed_date
 * @property string $signatory_name
 * @property string $signatory_relation
 * @property string $other_relation
 * @property integer $is_incapacitated
 * @property string $reason
 * @property string $pin
 * @property string $employer_pen
 * @property string $employer_name
 * @property string $employer_contact_no
 * @property string $employer_business_name
 * @property string $employer_capacity
 * @property string $employer_date_signed
 * @property string $create_id
 * @property string $create_time
 * @property string $modify_id
 * @property string $modify_time
 *
 * The followings are the available model relations:
 * @property MemberInfo $memberInfo
 */
class ClaimForm1 extends CareActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_cf1';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('member_info_id', 'required'),
			array('signatory_is_representative, is_incapacitated', 'numerical', 'integerOnly'=>true),
			array('member_info_id', 'length', 'max'=>10),
			array('signatory_name, other_relation, reason, employer_name, employer_business_name, employer_capacity', 'length', 'max'=>50),
			array('signatory_relation', 'length', 'max'=>1),
			array('pin, employer_pen, employer_contact_no, create_id, modify_id', 'length', 'max'=>20),
			array('signed_date, employer_date_signed, create_time, modify_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('member_info_id, signatory_is_representative, signed_date, signatory_name, signatory_relation, other_relation, is_incapacitated, reason, pin, employer_pen, employer_name, employer_contact_no, employer_business_name, employer_capacity, employer_date_signed, create_id, create_time, modify_id, modify_time', 'safe', 'on'=>'search'),
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
			'memberInfo' => array(self::BELONGS_TO, 'MemberInfo', 'member_info_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'member_info_id' => 'Member Info',
			'signatory_is_representative' => 'Signatory Is Representative',
			'signed_date' => 'Signed Date',
			'signatory_name' => 'Signatory Name',
			'signatory_relation' => 'Signatory Relation',
			'other_relation' => 'Other Relation',
			'is_incapacitated' => 'Is Incapacitated',
			'reason' => 'Reason',
			'pin' => 'Pin',
			'employer_pen' => 'Employer Pen',
			'employer_name' => 'Employer Name',
			'employer_contact_no' => 'Employer Contact No',
			'employer_business_name' => 'Employer Business Name',
			'employer_capacity' => 'Employer Capacity',
			'employer_date_signed' => 'Employer Date Signed',
			'create_id' => 'Create',
			'create_time' => 'Create Time',
			'modify_id' => 'Modify',
			'modify_time' => 'Modify Time',
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

		$criteria->compare('member_info_id',$this->member_info_id,true);
		$criteria->compare('signatory_is_representative',$this->signatory_is_representative);
		$criteria->compare('signed_date',$this->signed_date,true);
		$criteria->compare('signatory_name',$this->signatory_name,true);
		$criteria->compare('signatory_relation',$this->signatory_relation,true);
		$criteria->compare('other_relation',$this->other_relation,true);
		$criteria->compare('is_incapacitated',$this->is_incapacitated);
		$criteria->compare('reason',$this->reason,true);
		$criteria->compare('pin',$this->pin,true);
		$criteria->compare('employer_pen',$this->employer_pen,true);
		$criteria->compare('employer_name',$this->employer_name,true);
		$criteria->compare('employer_contact_no',$this->employer_contact_no,true);
		$criteria->compare('employer_business_name',$this->employer_business_name,true);
		$criteria->compare('employer_capacity',$this->employer_capacity,true);
		$criteria->compare('employer_date_signed',$this->employer_date_signed,true);
		$criteria->compare('create_id',$this->create_id,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('modify_id',$this->modify_id,true);
		$criteria->compare('modify_time',$this->modify_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ClaimForm1 the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
