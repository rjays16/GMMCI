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
 *
 * The followings are the available model relations:
 * @property SegBarangays $brgyNr
 * @property CareInsuranceFirm $hcare
 * @property SegMunicity $munNr
 * @property CarePerson $p
 */
class Member extends CActiveRecord
{
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
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('pid, hcare_id, create_date, insurance_nr', 'required'),
			array('pid', 'length', 'max'=>12),
			array('hcare_id', 'length', 'max'=>8),
			array('insurance_nr, employer_no', 'length', 'max'=>25),
			array('member_lname, member_fname, member_mname, street_name, employer_name', 'length', 'max'=>150),
			array('suffix', 'length', 'max'=>10),
			array('brgy_nr, mun_nr', 'length', 'max'=>11),
			array('relation', 'length', 'max'=>1),
			array('member_type', 'length', 'max'=>5),
			array('birth_date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('pid, hcare_id, insurance_nr, member_lname, member_fname, member_mname, suffix, birth_date, street_name, brgy_nr, mun_nr, relation, member_type, employer_no, employer_name, create_date', 'safe', 'on'=>'search'),
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
			'brgyNr' => array(self::BELONGS_TO, 'SegBarangays', 'brgy_nr'),
			'hcare' => array(self::BELONGS_TO, 'CareInsuranceFirm', 'hcare_id'),
			'munNr' => array(self::BELONGS_TO, 'SegMunicity', 'mun_nr'),
			'p' => array(self::BELONGS_TO, 'CarePerson', 'pid'),
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
			'insurance_nr' => 'Insurance No.',
			'member_lname' => 'Last Name',
			'member_fname' => 'First Name',
			'member_mname' => 'Middle Name',
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
		);
	}

    /**
     * Returns the code table for the various values to identify the
     * member's relation to the patient
     *
     * @return array
     */
    public static function getRelationTypes() {
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
    public static function getMemberTypes() {
        return array(
            'S' => 'Employed Private',
            'G' => 'Employer Government',
            'I' => 'Indigent',
            'NS' => 'Individually Paying',
            'NO' => 'OFW',
            'PS' => 'Non Paying Private',
            'PG' => 'Non Paying Government',
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

		$criteria->compare('pid',$this->pid,true);
		$criteria->compare('hcare_id',$this->hcare_id,true);
		$criteria->compare('insurance_nr',$this->insurance_nr,true);
		$criteria->compare('member_lname',$this->member_lname,true);
		$criteria->compare('member_fname',$this->member_fname,true);
		$criteria->compare('member_mname',$this->member_mname,true);
		$criteria->compare('suffix',$this->suffix,true);
		$criteria->compare('birth_date',$this->birth_date,true);
		$criteria->compare('street_name',$this->street_name,true);
		$criteria->compare('brgy_nr',$this->brgy_nr,true);
		$criteria->compare('mun_nr',$this->mun_nr,true);
		$criteria->compare('relation',$this->relation,true);
		$criteria->compare('member_type',$this->member_type,true);
		$criteria->compare('employer_no',$this->employer_no,true);
		$criteria->compare('employer_name',$this->employer_name,true);
		$criteria->compare('create_date',$this->create_date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Member the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
