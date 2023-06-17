<?php

/**
 * This is the model class for table "seg_internal_request".
 *
 * The followings are the available columns in table 'seg_internal_request':
 * @property string $refno
 * @property string $request_date
 * @property integer $requestor_id
 * @property string $area_code
 * @property string $area_code_dest
 * @property string $status
 *
 * The followings are the available model relations:
 * @property CarePersonell $requestor
 * @property SegInternalRequestDetails[] $segInternalRequestDetails
 */
class Requisition extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_internal_request';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('refno, requestor_id, area_code, area_code_dest', 'required'),
			array('requestor_id', 'numerical', 'integerOnly'=>true),
			array('refno', 'length', 'max'=>12),
			array('area_code, area_code_dest', 'length', 'max'=>10),
			array('status', 'length', 'max'=>15),
			array('request_date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('refno, request_date, requestor_id, area_code, area_code_dest, status', 'safe', 'on'=>'search'),
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
			'requestor' => array(self::BELONGS_TO, 'CarePersonell', 'requestor_id'),
			'segInternalRequestDetails' => array(self::HAS_MANY, 'SegInternalRequestDetails', 'refno'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'refno' => 'Refno',
			'request_date' => 'Request Date',
			'requestor_id' => 'Requestor',
			'area_code' => 'Area Code',
			'area_code_dest' => 'Area Code Dest',
			'status' => 'Status',
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
		$criteria->compare('request_date',$this->request_date,true);
		$criteria->compare('requestor_id',$this->requestor_id);
		$criteria->compare('area_code',$this->area_code,true);
		$criteria->compare('area_code_dest',$this->area_code_dest,true);
		$criteria->compare('status',$this->status,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Requisition the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
