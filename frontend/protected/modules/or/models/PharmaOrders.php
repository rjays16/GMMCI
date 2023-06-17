<?php
Yii::import('or.models.PharmaProductsMain');
Yii::import('or.models.PharmaOrderItems');

/**
 * This is the model class for table "seg_pharma_orders".
 *
 * The followings are the available columns in table 'seg_pharma_orders':
 * @property string $refno
 * @property string $department
 * @property string $pharma_area
 * @property string $request_source
 * @property string $orderdate
 * @property string $pid
 * @property string $walkin_pid
 * @property string $request_dept
 * @property string $encounter_nr
 * @property string $related_refno
 * @property string $related_refsource
 * @property string $ordername
 * @property string $orderaddress
 * @property string $discountid
 * @property string $discount
 * @property string $charge_type
 * @property integer $is_cash
 * @property integer $is_tpl
 * @property integer $is_urgent
 * @property string $amount_due
 * @property string $serve_status
 * @property string $comments
 * @property string $history
 * @property string $create_id
 * @property string $create_time
 * @property string $modify_id
 * @property string $modify_time
 * @property integer $is_deleted
 *
 * The followings are the available model relations:
 * @property PharmaOrMain[] $pharmaOrMains
 * @property Discount[] $segDiscounts
 * @property CarePharmaProductsMain[] $carePharmaProductsMains
 * @property PharmaAreas $pharmaArea
 * @property CarePerson $p
 * @property TypeRequestSource $requestSource
 * @property Walkin $walkinP
 * @property PharmaReturnItems[] $pharmaReturnItems
 */
class PharmaOrders extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_pharma_orders';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('refno, ordername, orderaddress', 'required'),
			array('is_cash, is_tpl, is_urgent, is_deleted', 'numerical', 'integerOnly'=>true),
			array('refno, pid, walkin_pid, request_dept, encounter_nr, related_refno', 'length', 'max'=>12),
			array('department, serve_status', 'length', 'max'=>1),
			array('pharma_area, request_source, discountid, discount, amount_due', 'length', 'max'=>10),
			array('related_refsource', 'length', 'max'=>2),
			array('ordername, comments', 'length', 'max'=>200),
			array('orderaddress', 'length', 'max'=>250),
			array('charge_type', 'length', 'max'=>8),
			array('create_id, modify_id', 'length', 'max'=>35),
			array('orderdate, history, create_time, modify_time', 'safe'),
			array('modify_time, create_time','default',
				'value'=>new CDbExpression('NOW()'),
				'setOnEmpty'=>false,'on'=>'insert'),
			array('modify_id, create_id','default',
				'value'=> $_SESSION['sess_temp_userid'],
				'setOnEmpty'=>false,'on'=>'insert'),
			array('modify_time','default',
				'value'=>new CDbExpression('NOW()'),
				'setOnEmpty'=>false,'on'=>'update'),
			array('modify_id','default',
				'value'=> $_SESSION['sess_temp_userid'],
				'setOnEmpty'=>false,'on'=>'update'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('refno, department, pharma_area, request_source, orderdate, pid, walkin_pid, request_dept, encounter_nr, related_refno, related_refsource, ordername, orderaddress, discountid, discount, charge_type, is_cash, is_tpl, is_urgent, amount_due, serve_status, comments, history, create_id, create_time, modify_id, modify_time, is_deleted', 'safe', 'on'=>'search'),
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
			'pharmaOrMains' => array(self::HAS_MANY, 'PharmaOrMain', 'pharma_refno'),
			'segDiscounts' => array(self::MANY_MANY, 'Discount', 'seg_pharma_order_discounts(refno, discountid)'),
			'carePharmaProductsMains' => array(self::MANY_MANY, 'CarePharmaProductsMain', 'seg_pharma_order_items(refno, bestellnum)'),
			'pharmaArea' => array(self::BELONGS_TO, 'PharmaAreas', 'pharma_area'),
			'p' => array(self::BELONGS_TO, 'CarePerson', 'pid'),
			'requestSource' => array(self::BELONGS_TO, 'TypeRequestSource', 'request_source'),
			'walkinP' => array(self::BELONGS_TO, 'Walkin', 'walkin_pid'),
			'pharmaReturnItems' => array(self::HAS_MANY, 'PharmaReturnItems', 'ref_no'),
			'pharmaOrderItems' => array(self::HAS_MANY, 'PharmaOrderItems', 'refno'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'refno' => 'Refno',
			'department' => 'Department',
			'pharma_area' => 'Pharma Area',
			'request_source' => 'Request Source',
			'orderdate' => 'Orderdate',
			'pid' => 'Pid',
			'walkin_pid' => 'Walkin Pid',
			'request_dept' => 'Request Dept',
			'encounter_nr' => 'Encounter Nr',
			'related_refno' => 'Related Refno',
			'related_refsource' => 'Related Refsource',
			'ordername' => 'Ordername',
			'orderaddress' => 'Orderaddress',
			'discountid' => 'Discountid',
			'discount' => 'Discount',
			'charge_type' => 'Charge Type',
			'is_cash' => 'Is Cash',
			'is_tpl' => 'Is Tpl',
			'is_urgent' => 'Is Urgent',
			'amount_due' => 'Amount Due',
			'serve_status' => 'Serve Status',
			'comments' => 'Comments',
			'history' => 'History',
			'create_id' => 'Create',
			'create_time' => 'Create Time',
			'modify_id' => 'Modify',
			'modify_time' => 'Modify Time',
			'is_deleted' => 'Is Deleted',
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
		$criteria->compare('department',$this->department,true);
		$criteria->compare('pharma_area',$this->pharma_area,true);
		$criteria->compare('request_source',$this->request_source,true);
		$criteria->compare('orderdate',$this->orderdate,true);
		$criteria->compare('pid',$this->pid,true);
		$criteria->compare('walkin_pid',$this->walkin_pid,true);
		$criteria->compare('request_dept',$this->request_dept,true);
		$criteria->compare('encounter_nr',$this->encounter_nr,true);
		$criteria->compare('related_refno',$this->related_refno,true);
		$criteria->compare('related_refsource',$this->related_refsource,true);
		$criteria->compare('ordername',$this->ordername,true);
		$criteria->compare('orderaddress',$this->orderaddress,true);
		$criteria->compare('discountid',$this->discountid,true);
		$criteria->compare('discount',$this->discount,true);
		$criteria->compare('charge_type',$this->charge_type,true);
		$criteria->compare('is_cash',$this->is_cash);
		$criteria->compare('is_tpl',$this->is_tpl);
		$criteria->compare('is_urgent',$this->is_urgent);
		$criteria->compare('amount_due',$this->amount_due,true);
		$criteria->compare('serve_status',$this->serve_status,true);
		$criteria->compare('comments',$this->comments,true);
		$criteria->compare('history',$this->history,true);
		$criteria->compare('create_id',$this->create_id,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('modify_id',$this->modify_id,true);
		$criteria->compare('modify_time',$this->modify_time,true);
		$criteria->compare('is_deleted',$this->is_deleted);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PharmaOrders the static model class
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
