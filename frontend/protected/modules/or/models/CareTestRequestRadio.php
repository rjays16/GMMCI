<?php

/**
 * This is the model class for table "care_test_request_radio".
 *
 * The followings are the available columns in table 'care_test_request_radio':
 * @property string $batch_nr
 * @property string $refno
 * @property string $clinical_info
 * @property string $service_code
 * @property string $price_cash
 * @property string $price_cash_orig
 * @property string $price_charge
 * @property string $service_date
 * @property integer $is_in_house
 * @property string $request_doctor
 * @property string $request_date
 * @property string $encoder
 * @property string $status
 * @property string $history
 * @property string $modify_id
 * @property string $modify_dt
 * @property string $create_id
 * @property string $create_dt
 * @property integer $parent_batch_nr
 * @property string $parent_refno
 * @property string $approved_by_head
 * @property string $remarks
 * @property string $headID
 * @property string $headpasswd
 * @property string $request_flag
 * @property string $cancel_reason
 * @property string $or_number
 * @property integer $is_served
 * @property string $served_date
 * @property integer $rad_tech
 *
 * The followings are the available model relations:
 * @property CmapEntriesRadiology[] $cmapEntriesRadiologies
 * @property CmapEntriesRadiology[] $cmapEntriesRadiologies1
 * @property LingapEntriesRadiology[] $lingapEntriesRadiologies
 * @property LingapEntriesRadiology[] $lingapEntriesRadiologies1
 */
class CareTestRequestRadio extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'care_test_request_radio';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('refno, status, history', 'required'),
			array('is_in_house, parent_batch_nr, is_served, rad_tech', 'numerical', 'integerOnly'=>true),
			array('batch_nr, service_code, price_cash, price_cash_orig, price_charge, status, parent_refno, request_flag', 'length', 'max'=>10),
			array('refno, or_number', 'length', 'max'=>12),
			array('request_doctor, encoder, approved_by_head', 'length', 'max'=>50),
			array('modify_id, create_id, headID', 'length', 'max'=>35),
			array('headpasswd', 'length', 'max'=>255),
			array('service_date, request_date, modify_dt, create_dt, remarks, cancel_reason, served_date', 'safe'),
			array('modify_dt, create_dt','default',
				'value'=>new CDbExpression('NOW()'),
				'setOnEmpty'=>false,'on'=>'insert'),
			array('modify_id, create_id','default',
				'value'=> $_SESSION['sess_temp_userid'],
				'setOnEmpty'=>false,'on'=>'insert'),
			array('modify_dt','default',
				'value'=>new CDbExpression('NOW()'),
				'setOnEmpty'=>false,'on'=>'update'),
			array('modify_id','default',
				'value'=> $_SESSION['sess_temp_userid'],
				'setOnEmpty'=>false,'on'=>'update'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('batch_nr, refno, clinical_info, service_code, price_cash, price_cash_orig, price_charge, service_date, is_in_house, request_doctor, request_date, encoder, status, history, modify_id, modify_dt, create_id, create_dt, parent_batch_nr, parent_refno, approved_by_head, remarks, headID, headpasswd, request_flag, cancel_reason, or_number, is_served, served_date, rad_tech', 'safe', 'on'=>'search'),
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
			'cmapEntriesRadiologies' => array(self::HAS_MANY, 'CmapEntriesRadiology', 'ref_no'),
			'cmapEntriesRadiologies1' => array(self::HAS_MANY, 'CmapEntriesRadiology', 'service_code'),
			'lingapEntriesRadiologies' => array(self::HAS_MANY, 'LingapEntriesRadiology', 'ref_no'),
			'lingapEntriesRadiologies1' => array(self::HAS_MANY, 'LingapEntriesRadiology', 'service_code'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'batch_nr' => 'Batch Nr',
			'refno' => 'Refno',
			'clinical_info' => 'Clinical Info',
			'service_code' => 'Service Code',
			'price_cash' => 'Price Cash',
			'price_cash_orig' => 'Price Cash Orig',
			'price_charge' => 'Price Charge',
			'service_date' => 'Service Date',
			'is_in_house' => 'Is In House',
			'request_doctor' => 'Request Doctor',
			'request_date' => 'Request Date',
			'encoder' => 'Encoder',
			'status' => 'Status',
			'history' => 'History',
			'modify_id' => 'Modify',
			'modify_dt' => 'Modify Dt',
			'create_id' => 'Create',
			'create_dt' => 'Create Dt',
			'parent_batch_nr' => 'Parent Batch Nr',
			'parent_refno' => 'Parent Refno',
			'approved_by_head' => 'Approved By Head',
			'remarks' => 'Remarks',
			'headID' => 'Head',
			'headpasswd' => 'Headpasswd',
			'request_flag' => 'Request Flag',
			'cancel_reason' => 'Cancel Reason',
			'or_number' => 'Or Number',
			'is_served' => 'Is Served',
			'served_date' => 'Served Date',
			'rad_tech' => 'Rad Tech',
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

		$criteria->compare('batch_nr',$this->batch_nr,true);
		$criteria->compare('refno',$this->refno,true);
		$criteria->compare('clinical_info',$this->clinical_info,true);
		$criteria->compare('service_code',$this->service_code,true);
		$criteria->compare('price_cash',$this->price_cash,true);
		$criteria->compare('price_cash_orig',$this->price_cash_orig,true);
		$criteria->compare('price_charge',$this->price_charge,true);
		$criteria->compare('service_date',$this->service_date,true);
		$criteria->compare('is_in_house',$this->is_in_house);
		$criteria->compare('request_doctor',$this->request_doctor,true);
		$criteria->compare('request_date',$this->request_date,true);
		$criteria->compare('encoder',$this->encoder,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('history',$this->history,true);
		$criteria->compare('modify_id',$this->modify_id,true);
		$criteria->compare('modify_dt',$this->modify_dt,true);
		$criteria->compare('create_id',$this->create_id,true);
		$criteria->compare('create_dt',$this->create_dt,true);
		$criteria->compare('parent_batch_nr',$this->parent_batch_nr);
		$criteria->compare('parent_refno',$this->parent_refno,true);
		$criteria->compare('approved_by_head',$this->approved_by_head,true);
		$criteria->compare('remarks',$this->remarks,true);
		$criteria->compare('headID',$this->headID,true);
		$criteria->compare('headpasswd',$this->headpasswd,true);
		$criteria->compare('request_flag',$this->request_flag,true);
		$criteria->compare('cancel_reason',$this->cancel_reason,true);
		$criteria->compare('or_number',$this->or_number,true);
		$criteria->compare('is_served',$this->is_served);
		$criteria->compare('served_date',$this->served_date,true);
		$criteria->compare('rad_tech',$this->rad_tech);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CareTestRequestRadio the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function scopes(){
		return array(
			'latest' => array(
				'order' => 'batch_nr DESC'
			)
		);
	}
}
