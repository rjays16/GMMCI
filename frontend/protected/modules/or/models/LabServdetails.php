<?php

/**
 * This is the model class for table "seg_lab_servdetails".
 *
 * The followings are the available columns in table 'seg_lab_servdetails':
 * @property string $refno
 * @property string $service_code
 * @property string $price_cash
 * @property string $price_cash_orig
 * @property string $price_charge
 * @property string $request_doctor
 * @property string $request_dept
 * @property integer $is_in_house
 * @property string $clinical_info
 * @property string $status
 * @property integer $is_forward
 * @property integer $is_served
 * @property string $date_served
 * @property string $clerk_served_by
 * @property string $clerk_served_date
 * @property double $quantity
 * @property double $old_qty_request
 * @property string $reason_sent_out
 * @property string $sent_out_date
 * @property string $sent_out_by
 * @property integer $is_monitor
 * @property string $parent_refno
 * @property string $request_flag
 * @property double $no_gel_tubes
 * @property string $cancel_reason
 * @property integer $is_posted_lis
 * @property string $history
 * @property string $date_request
 *
 * The followings are the available model relations:
 * @property CmapEntriesLaboratory[] $cmapEntriesLaboratories
 * @property CmapEntriesLaboratory[] $cmapEntriesLaboratories1
 * @property LingapEntriesLaboratory[] $lingapEntriesLaboratories
 * @property LingapEntriesLaboratory[] $lingapEntriesLaboratories1
 */
class LabServdetails extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_lab_servdetails';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('refno, service_code', 'required'),
			array('is_in_house, is_forward, is_served, is_monitor, is_posted_lis', 'numerical', 'integerOnly'=>true),
			array('quantity, old_qty_request, no_gel_tubes', 'numerical'),
			array('refno, parent_refno', 'length', 'max'=>12),
			array('service_code, price_cash, price_cash_orig, price_charge, request_flag', 'length', 'max'=>10),
			array('request_doctor, request_dept', 'length', 'max'=>50),
			array('status', 'length', 'max'=>8),
			array('clerk_served_by, sent_out_by', 'length', 'max'=>100),
			array('clinical_info, date_served, clerk_served_date, reason_sent_out, sent_out_date, cancel_reason, history, date_request', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('refno, service_code, price_cash, price_cash_orig, price_charge, request_doctor, request_dept, is_in_house, clinical_info, status, is_forward, is_served, date_served, clerk_served_by, clerk_served_date, quantity, old_qty_request, reason_sent_out, sent_out_date, sent_out_by, is_monitor, parent_refno, request_flag, no_gel_tubes, cancel_reason, is_posted_lis, history, date_request', 'safe', 'on'=>'search'),
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
			'cmapEntriesLaboratories' => array(self::HAS_MANY, 'CmapEntriesLaboratory', 'ref_no'),
			'cmapEntriesLaboratories1' => array(self::HAS_MANY, 'CmapEntriesLaboratory', 'service_code'),
			'lingapEntriesLaboratories' => array(self::HAS_MANY, 'LingapEntriesLaboratory', 'ref_no'),
			'lingapEntriesLaboratories1' => array(self::HAS_MANY, 'LingapEntriesLaboratory', 'service_code'),
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
			'price_cash' => 'Price Cash',
			'price_cash_orig' => 'Price Cash Orig',
			'price_charge' => 'Price Charge',
			'request_doctor' => 'Request Doctor',
			'request_dept' => 'Request Dept',
			'is_in_house' => 'Is In House',
			'clinical_info' => 'Clinical Info',
			'status' => 'Status',
			'is_forward' => 'Is Forward',
			'is_served' => 'Is Served',
			'date_served' => 'Date Served',
			'clerk_served_by' => 'Clerk Served By',
			'clerk_served_date' => 'Clerk Served Date',
			'quantity' => 'Quantity',
			'old_qty_request' => 'Old Qty Request',
			'reason_sent_out' => 'Reason Sent Out',
			'sent_out_date' => 'Sent Out Date',
			'sent_out_by' => 'Sent Out By',
			'is_monitor' => 'Is Monitor',
			'parent_refno' => 'Parent Refno',
			'request_flag' => 'Request Flag',
			'no_gel_tubes' => 'No Gel Tubes',
			'cancel_reason' => 'Cancel Reason',
			'is_posted_lis' => 'Is Posted Lis',
			'history' => 'History',
			'date_request' => 'Date Request',
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
		$criteria->compare('price_cash',$this->price_cash,true);
		$criteria->compare('price_cash_orig',$this->price_cash_orig,true);
		$criteria->compare('price_charge',$this->price_charge,true);
		$criteria->compare('request_doctor',$this->request_doctor,true);
		$criteria->compare('request_dept',$this->request_dept,true);
		$criteria->compare('is_in_house',$this->is_in_house);
		$criteria->compare('clinical_info',$this->clinical_info,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('is_forward',$this->is_forward);
		$criteria->compare('is_served',$this->is_served);
		$criteria->compare('date_served',$this->date_served,true);
		$criteria->compare('clerk_served_by',$this->clerk_served_by,true);
		$criteria->compare('clerk_served_date',$this->clerk_served_date,true);
		$criteria->compare('quantity',$this->quantity);
		$criteria->compare('old_qty_request',$this->old_qty_request);
		$criteria->compare('reason_sent_out',$this->reason_sent_out,true);
		$criteria->compare('sent_out_date',$this->sent_out_date,true);
		$criteria->compare('sent_out_by',$this->sent_out_by,true);
		$criteria->compare('is_monitor',$this->is_monitor);
		$criteria->compare('parent_refno',$this->parent_refno,true);
		$criteria->compare('request_flag',$this->request_flag,true);
		$criteria->compare('no_gel_tubes',$this->no_gel_tubes);
		$criteria->compare('cancel_reason',$this->cancel_reason,true);
		$criteria->compare('is_posted_lis',$this->is_posted_lis);
		$criteria->compare('history',$this->history,true);
		$criteria->compare('date_request',$this->date_request,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return LabServdetails the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
