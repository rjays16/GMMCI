<?php

/**
 * This is the model class for table "seg_radio_serv".
 *
 * The followings are the available columns in table 'seg_radio_serv':
 * @property string $refno
 * @property string $request_date
 * @property string $request_time
 * @property string $encounter_nr
 * @property string $discountid
 * @property string $discount
 * @property string $pid
 * @property string $ordername
 * @property string $orderaddress
 * @property integer $is_cash
 * @property integer $type_charge
 * @property integer $is_urgent
 * @property integer $is_tpl
 * @property integer $is_approved
 * @property string $comments
 * @property string $status
 * @property string $history
 * @property string $modify_id
 * @property string $modify_dt
 * @property string $create_id
 * @property string $create_dt
 * @property integer $is_pay_full
 * @property string $walkin_pid
 * @property string $source_req
 * @property string $area_type
 * @property string $grant_type
 * @property integer $is_pe
 * @property integer $is_rdu
 *
 * The followings are the available model relations:
 * @property ServiceArea $areaType
 * @property TypeRequestSource $sourceReq
 */
class RadioServ extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_radio_serv';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('refno, request_date, request_time, is_cash', 'required'),
			array('is_cash, type_charge, is_urgent, is_tpl, is_approved, is_pay_full, is_pe, is_rdu', 'numerical', 'integerOnly'=>true),
			array('refno, encounter_nr, pid, walkin_pid', 'length', 'max'=>12),
			array('discountid, discount, source_req, area_type, grant_type', 'length', 'max'=>10),
			array('ordername, comments', 'length', 'max'=>200),
			array('orderaddress', 'length', 'max'=>300),
			array('status, modify_id, create_id', 'length', 'max'=>35),
			array('history, modify_dt, create_dt', 'safe'),
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
			array('refno, request_date, request_time, encounter_nr, discountid, discount, pid, ordername, orderaddress, is_cash, type_charge, is_urgent, is_tpl, is_approved, comments, status, history, modify_id, modify_dt, create_id, create_dt, is_pay_full, walkin_pid, source_req, area_type, grant_type, is_pe, is_rdu', 'safe', 'on'=>'search'),
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
			'areaType' => array(self::BELONGS_TO, 'ServiceArea', 'area_type'),
			'sourceReq' => array(self::BELONGS_TO, 'TypeRequestSource', 'source_req'),
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
			'request_time' => 'Request Time',
			'encounter_nr' => 'Encounter Nr',
			'discountid' => 'Discountid',
			'discount' => 'Discount',
			'pid' => 'Pid',
			'ordername' => 'Ordername',
			'orderaddress' => 'Orderaddress',
			'is_cash' => 'Is Cash',
			'type_charge' => 'Type Charge',
			'is_urgent' => 'Is Urgent',
			'is_tpl' => 'Is Tpl',
			'is_approved' => 'Is Approved',
			'comments' => 'Comments',
			'status' => 'Status',
			'history' => 'History',
			'modify_id' => 'Modify',
			'modify_dt' => 'Modify Dt',
			'create_id' => 'Create',
			'create_dt' => 'Create Dt',
			'is_pay_full' => 'Is Pay Full',
			'walkin_pid' => 'Walkin Pid',
			'source_req' => 'Source Req',
			'area_type' => 'Area Type',
			'grant_type' => 'Grant Type',
			'is_pe' => 'Is Pe',
			'is_rdu' => 'Is Rdu',
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
		$criteria->compare('request_time',$this->request_time,true);
		$criteria->compare('encounter_nr',$this->encounter_nr,true);
		$criteria->compare('discountid',$this->discountid,true);
		$criteria->compare('discount',$this->discount,true);
		$criteria->compare('pid',$this->pid,true);
		$criteria->compare('ordername',$this->ordername,true);
		$criteria->compare('orderaddress',$this->orderaddress,true);
		$criteria->compare('is_cash',$this->is_cash);
		$criteria->compare('type_charge',$this->type_charge);
		$criteria->compare('is_urgent',$this->is_urgent);
		$criteria->compare('is_tpl',$this->is_tpl);
		$criteria->compare('is_approved',$this->is_approved);
		$criteria->compare('comments',$this->comments,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('history',$this->history,true);
		$criteria->compare('modify_id',$this->modify_id,true);
		$criteria->compare('modify_dt',$this->modify_dt,true);
		$criteria->compare('create_id',$this->create_id,true);
		$criteria->compare('create_dt',$this->create_dt,true);
		$criteria->compare('is_pay_full',$this->is_pay_full);
		$criteria->compare('walkin_pid',$this->walkin_pid,true);
		$criteria->compare('source_req',$this->source_req,true);
		$criteria->compare('area_type',$this->area_type,true);
		$criteria->compare('grant_type',$this->grant_type,true);
		$criteria->compare('is_pe',$this->is_pe);
		$criteria->compare('is_rdu',$this->is_rdu);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return RadioServ the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function scopes(){
		return array(
			'latest' => array(
				'order' => 'refno DESC'
			)
		);
	}
}
