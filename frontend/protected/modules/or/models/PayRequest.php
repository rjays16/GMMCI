<?php

/**
 * This is the model class for table "seg_pay_request".
 *
 * The followings are the available columns in table 'seg_pay_request':
 * @property string $or_no
 * @property string $ref_no
 * @property string $ref_source
 * @property integer $account_type
 * @property string $qty
 * @property string $amount_due
 * @property string $service_code
 *
 * The followings are the available model relations:
 * @property Pay $orNo
 */
class PayRequest extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_pay_request';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('or_no, ref_no, ref_source, service_code', 'required'),
			array('account_type', 'numerical', 'integerOnly'=>true),
			array('or_no, ref_no, service_code', 'length', 'max'=>12),
			array('ref_source', 'length', 'max'=>5),
			array('qty, amount_due', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('or_no, ref_no, ref_source, account_type, qty, amount_due, service_code', 'safe', 'on'=>'search'),
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
			'orNo' => array(self::BELONGS_TO, 'Pay', 'or_no'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'or_no' => 'Or No',
			'ref_no' => 'Ref No',
			'ref_source' => 'Ref Source',
			'account_type' => 'Account Type',
			'qty' => 'Qty',
			'amount_due' => 'Amount Due',
			'service_code' => 'Service Code',
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
		$criteria->compare('ref_no',$this->ref_no,true);
		$criteria->compare('ref_source',$this->ref_source,true);
		$criteria->compare('account_type',$this->account_type);
		$criteria->compare('qty',$this->qty,true);
		$criteria->compare('amount_due',$this->amount_due,true);
		$criteria->compare('service_code',$this->service_code,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PayRequest the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
