<?php

/**
 *
 * @author  Ma. Dulce O. Polinar  <dulcepolinar1010@gmail.com>
 * @copyright (c) 2014, Segworks Technologies Corporation
 */
class VoucherCharge extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_eclaims_voucher_charge';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('claim_id, voucher_id', 'numerical', 'integerOnly'=>true),
			array('payee_name', 'length', 'max'=>100),
			array('payee_type', 'length', 'max'=>1),
			array('payee_code', 'length', 'max'=>14),
			array('rmbd, drugs, xray, oprm, spfee, gpfee, surfee, anesfee, net_amount', 'length', 'max'=>10),
			array('gross_amount, tax_amount', 'length', 'max'=>12),
			array('id, claim_id, voucher_id, payee_name, payee_type, payee_code, rmbd, drugs, xray, oprm, spfee, gpfee, surfee, anesfee, gross_amount, tax_amount, net_amount', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'claim' => array(self::BELONGS_TO, 'Claim', 'claim_id'),
			'voucher' => array(self::BELONGS_TO, 'Voucher', 'voucher_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'claim_id' => 'Claim ID',
			'voucher_id' => 'Voucher',
			'payee_name' => 'Payee Name',
			'payee_type' => 'Payee Type',
			'payee_code' => 'Payee Code',
			'rmbd' => 'Room and Board',
			'drugs' => 'Drugs and Medicines',
			'xray' => 'Xray and Laboratory',
			'oprm' => 'Operating Room',
			'spfee' => 'Specialist',
			'gpfee' => 'General Practitioner',
			'surfee' => 'Surgeon',
			'anesfee' => 'Anesthesionlogist',
			'gross_amount' => 'Gross Amount',
			'tax_amount' => 'Tax Amount',
			'net_amount' => 'Net Amount',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('claim_id',$this->claim_id);
		$criteria->compare('voucher_id',$this->voucher_id);
		$criteria->compare('payee_name',$this->payee_name,true);
		$criteria->compare('payee_type',$this->payee_type,true);
		$criteria->compare('payee_code',$this->payee_code,true);
		$criteria->compare('rmbd',$this->rmbd,true);
		$criteria->compare('drugs',$this->drugs,true);
		$criteria->compare('xray',$this->xray,true);
		$criteria->compare('oprm',$this->oprm,true);
		$criteria->compare('spfee',$this->spfee,true);
		$criteria->compare('gpfee',$this->gpfee,true);
		$criteria->compare('surfee',$this->surfee,true);
		$criteria->compare('anesfee',$this->anesfee,true);
		$criteria->compare('gross_amount',$this->gross_amount,true);
		$criteria->compare('tax_amount',$this->tax_amount,true);
		$criteria->compare('net_amount',$this->net_amount,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return VoucherCharge the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * Returns the full payee type (i.e. H = Hospital, etc)
	 * @return string $payee_type
	 *
	 */
	public function getPayeeType(){
		if(strcasecmp($this->payee_type, 'H') == 0){
			return "Hospital";
		}
		else if(strcasecmp($this->payee_type, 'D') == 0){
			return "Doctor";
		}
		else if(strcasecmp($this->payee_type, 'M') == 0){
			return "Member";
		}
		else if(strcasecmp($this->payee_type, 'C') == 0){
			return "Chief of Hospital";
		}

	}

	/**
	 * Returns the compressed payee details
	 * @return string
	 *
	 */
	public function getPayeeDetails(){
		return $this->payee_name."<br><small>".$this->PayeeType." - ".$this->payee_code."</small>";
	}

}
