<?php
Yii::import('or.models.PayRequest');

/**
 * This is the model class for table "seg_pay".
 *
 * The followings are the available columns in table 'seg_pay':
 * @property string $or_no
 * @property integer $account_type
 * @property string $cancel_date
 * @property string $cancelled_by
 * @property string $or_date
 * @property string $or_name
 * @property string $or_address
 * @property string $encounter_nr
 * @property string $pid
 * @property string $company_id
 * @property string $amount_tendered
 * @property string $amount_due
 * @property string $remarks
 * @property string $history
 * @property string $modify_id
 * @property string $modify_dt
 * @property string $create_id
 * @property string $create_dt
 * @property string $discount_tendered
 * @property string $vat_amount
 *
 * The followings are the available model relations:
 * @property CreditMemoDetails[] $creditMemoDetails
 * @property CashierAccountSubtypes $accountType
 * @property PayChecks[] $payChecks
 * @property PayCreditCards $payCreditCards
 * @property PayDeposit $payDeposit
 * @property Discount[] $segDiscounts
 * @property PayRequest[] $payRequests
 * @property CarePerson[] $carePeople
 */
class Pay extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_pay';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('or_no, cancelled_by, history', 'required'),
			array('account_type', 'numerical', 'integerOnly'=>true),
			array('or_no, encounter_nr, pid, company_id', 'length', 'max'=>12),
			array('cancelled_by, modify_id, create_id', 'length', 'max'=>35),
			array('or_name', 'length', 'max'=>200),
			array('or_address, remarks', 'length', 'max'=>300),
			array('amount_tendered, amount_due, discount_tendered, vat_amount', 'length', 'max'=>10),
			array('cancel_date, or_date, modify_dt, create_dt', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('or_no, account_type, cancel_date, cancelled_by, or_date, or_name, or_address, encounter_nr, pid, company_id, amount_tendered, amount_due, remarks, history, modify_id, modify_dt, create_id, create_dt, discount_tendered, vat_amount', 'safe', 'on'=>'search'),
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
			'creditMemoDetails' => array(self::HAS_MANY, 'CreditMemoDetails', 'or_no'),
			'accountType' => array(self::BELONGS_TO, 'CashierAccountSubtypes', 'account_type'),
			'payChecks' => array(self::HAS_MANY, 'PayChecks', 'or_no'),
			'payCreditCards' => array(self::HAS_ONE, 'PayCreditCards', 'or_no'),
			'payDeposit' => array(self::HAS_ONE, 'PayDeposit', 'or_no'),
			'segDiscounts' => array(self::MANY_MANY, 'Discount', 'seg_pay_discount(or_no, discountid)'),
			'payRequests' => array(self::HAS_MANY, 'PayRequest', 'or_no'),
			'carePeople' => array(self::MANY_MANY, 'CarePerson', 'seg_prepaid_consultation(or_no, pid)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'or_no' => 'Or No',
			'account_type' => 'Account Type',
			'cancel_date' => 'Cancel Date',
			'cancelled_by' => 'Cancelled By',
			'or_date' => 'Or Date',
			'or_name' => 'Or Name',
			'or_address' => 'Or Address',
			'encounter_nr' => 'Encounter Nr',
			'pid' => 'Pid',
			'company_id' => 'Company',
			'amount_tendered' => 'Amount Tendered',
			'amount_due' => 'Amount Due',
			'remarks' => 'Remarks',
			'history' => 'History',
			'modify_id' => 'Modify',
			'modify_dt' => 'Modify Dt',
			'create_id' => 'Create',
			'create_dt' => 'Create Dt',
			'discount_tendered' => 'Discount Tendered',
			'vat_amount' => 'Vat Amount',
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

		$criteria->compare('or_no',$this->or_no,true);
		$criteria->compare('account_type',$this->account_type);
		$criteria->compare('cancel_date',$this->cancel_date,true);
		$criteria->compare('cancelled_by',$this->cancelled_by,true);
		$criteria->compare('or_date',$this->or_date,true);
		$criteria->compare('or_name',$this->or_name,true);
		$criteria->compare('or_address',$this->or_address,true);
		$criteria->compare('encounter_nr',$this->encounter_nr,true);
		$criteria->compare('pid',$this->pid,true);
		$criteria->compare('company_id',$this->company_id,true);
		$criteria->compare('amount_tendered',$this->amount_tendered,true);
		$criteria->compare('amount_due',$this->amount_due,true);
		$criteria->compare('remarks',$this->remarks,true);
		$criteria->compare('history',$this->history,true);
		$criteria->compare('modify_id',$this->modify_id,true);
		$criteria->compare('modify_dt',$this->modify_dt,true);
		$criteria->compare('create_id',$this->create_id,true);
		$criteria->compare('create_dt',$this->create_dt,true);
		$criteria->compare('discount_tendered',$this->discount_tendered,true);
		$criteria->compare('vat_amount',$this->vat_amount,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Pay the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function findPaidOr($enc_no, $pid){
		$criteria = new CDbCriteria();

		$criteria->with = array(
			'payRequests' => array('joinType' => 'INNER JOIN')
		);

		$criteria->addSearchCondition('encounter_nr', $enc_no, true, 'AND');
		$criteria->addSearchCondition('pid', $pid, true, 'AND');
		$criteria->addSearchCondition('payRequests.ref_source', "PP", true, 'AND');

		return $this->find($criteria);
	}
}
