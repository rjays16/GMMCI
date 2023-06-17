<?php

/**
 * This is the model class for table "seg_pmrf".
 *
 * The followings are the available columns in table 'seg_pmrf':
 * @property string $member_info_id
 * @property string $purpose
 * @property string $membership_category
 * @property string $membership_other
 * @property string $membership_income
 * @property string $membership_effective_date
 * @property integer $tin
 * @property string $create_id
 * @property string $create_time
 * @property string $modify_id
 * @property string $modify_time
 * @property string $history
 *
 * The followings are the available model relations:
 * @property PmrfMemberCategory $membershipCategory
 * @property MemberInfo $memberInfo
 * @property PmrfDependent[] $dependents
 */
class Pmrf extends CareActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_pmrf';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('member_info_id, purpose', 'required'),
//			array('tin', 'numerical', 'integerOnly'=>true),
			array('member_info_id, purpose', 'length', 'max'=>10),
			array('membership_category', 'length', 'max'=>5),
			array('membership_other, create_id, modify_id', 'length', 'max'=>100),
			array('membership_income', 'length', 'max'=>20),
			array('tin, membership_effective_date, create_time, modify_time, history', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('member_info_id, purpose, membership_category, membership_other, membership_income, membership_effective_date, tin, create_id, create_time, modify_id, modify_time, history', 'safe', 'on'=>'search'),
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
			'membershipCategory' => array(self::BELONGS_TO, 'PmrfMemberCategory', 'membership_category'),
			'memberInfo' => array(self::BELONGS_TO, 'MemberInfo', 'member_info_id'),
			'dependents' => array(self::HAS_MANY, 'PmrfDependent', 'pmrf_id', 'condition' => 'is_deleted=0'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'member_info_id' => 'Member Info',
			'purpose' => 'Purpose',
			'membership_category' => 'Membership Category',
			'membership_other' => 'Membership Other',
			'membership_income' => 'Membership Income',
			'membership_effective_date' => 'Membership Effective Date',
			'tin' => 'Tin',
			'create_id' => 'Create',
			'create_time' => 'Create Time',
			'modify_id' => 'Modify',
			'modify_time' => 'Modify Time',
			'history' => 'History',
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
		$criteria->compare('purpose',$this->purpose,true);
		$criteria->compare('membership_category',$this->membership_category,true);
		$criteria->compare('membership_other',$this->membership_other,true);
		$criteria->compare('membership_income',$this->membership_income,true);
		$criteria->compare('membership_effective_date',$this->membership_effective_date,true);
		$criteria->compare('tin',$this->tin);
		$criteria->compare('create_id',$this->create_id,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('modify_id',$this->modify_id,true);
		$criteria->compare('modify_time',$this->modify_time,true);
		$criteria->compare('history',$this->history,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Pmrf the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
