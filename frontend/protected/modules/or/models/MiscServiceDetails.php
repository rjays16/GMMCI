<?php

/**
 * This is the model class for table "seg_misc_service_details".
 *
 * The followings are the available columns in table 'seg_misc_service_details':
 * @property string $refno
 * @property string $service_code
 * @property integer $entry_no
 * @property integer $account_type
 * @property string $adjusted_amnt
 * @property string $chrg_amnt
 * @property double $quantity
 * @property string $request_flag
 * @property string $cancel_reason
 * @property string $clinical_info
 *
 * The followings are the available model relations:
 * @property CmapEntriesMisc[] $cmapEntriesMiscs
 * @property CmapEntriesMisc[] $cmapEntriesMiscs1
 * @property CmapEntriesMisc[] $cmapEntriesMiscs2
 * @property LingapEntriesMisc[] $lingapEntriesMiscs
 * @property LingapEntriesMisc[] $lingapEntriesMiscs1
 * @property LingapEntriesMisc[] $lingapEntriesMiscs2
 * @property OtherServices $serviceCode
 * @property MiscService $refno0
 */
class MiscServiceDetails extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_misc_service_details';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('refno, service_code, entry_no, account_type, chrg_amnt', 'required'),
			array('entry_no, account_type', 'numerical', 'integerOnly'=>true),
			array('quantity', 'numerical'),
			array('refno, service_code', 'length', 'max'=>12),
			array('adjusted_amnt, chrg_amnt', 'length', 'max'=>20),
			array('request_flag', 'length', 'max'=>10),
			array('cancel_reason, clinical_info', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('refno, service_code, entry_no, account_type, adjusted_amnt, chrg_amnt, quantity, request_flag, cancel_reason, clinical_info', 'safe', 'on'=>'search'),
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
			'cmapEntriesMiscs' => array(self::HAS_MANY, 'CmapEntriesMisc', 'ref_no'),
			'cmapEntriesMiscs1' => array(self::HAS_MANY, 'CmapEntriesMisc', 'service_code'),
			'cmapEntriesMiscs2' => array(self::HAS_MANY, 'CmapEntriesMisc', 'entry_no'),
			'lingapEntriesMiscs' => array(self::HAS_MANY, 'LingapEntriesMisc', 'ref_no'),
			'lingapEntriesMiscs1' => array(self::HAS_MANY, 'LingapEntriesMisc', 'service_code'),
			'lingapEntriesMiscs2' => array(self::HAS_MANY, 'LingapEntriesMisc', 'entry_no'),
			'serviceCode' => array(self::BELONGS_TO, 'OtherServices', 'service_code'),
			'refno0' => array(self::BELONGS_TO, 'MiscService', 'refno'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'refno' => 'Refno',
			'service_code' => 'Service Code',
			'entry_no' => 'Entry No',
			'account_type' => 'Account Type',
			'adjusted_amnt' => 'Adjusted Amnt',
			'chrg_amnt' => 'Chrg Amnt',
			'quantity' => 'Quantity',
			'request_flag' => 'Request Flag',
			'cancel_reason' => 'Cancel Reason',
			'clinical_info' => 'Clinical Info',
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

		$criteria->compare('refno',$this->refno,true);
		$criteria->compare('service_code',$this->service_code,true);
		$criteria->compare('entry_no',$this->entry_no);
		$criteria->compare('account_type',$this->account_type);
		$criteria->compare('adjusted_amnt',$this->adjusted_amnt,true);
		$criteria->compare('chrg_amnt',$this->chrg_amnt,true);
		$criteria->compare('quantity',$this->quantity);
		$criteria->compare('request_flag',$this->request_flag,true);
		$criteria->compare('cancel_reason',$this->cancel_reason,true);
		$criteria->compare('clinical_info',$this->clinical_info,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return MiscServiceDetails the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
