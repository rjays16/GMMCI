<?php

/**
 * This is the model class for table "seg_other_services".
 *
 * The followings are the available columns in table 'seg_other_services':
 * @property string $service_code
 * @property string $alt_service_code
 * @property string $name
 * @property string $name_short
 * @property string $description
 * @property string $price
 * @property integer $account_type
 * @property integer $is_discountable
 * @property integer $is_not_socialized
 * @property integer $is_billing_related
 * @property integer $is_ER_default
 * @property integer $is_IP_default
 * @property integer $is_OP_default
 * @property integer $lockflag
 * @property integer $packageflag
 * @property integer $max_usage
 * @property integer $dept_nr
 * @property string $history
 * @property string $modify_id
 * @property string $modify_time
 * @property string $create_id
 * @property string $create_time
 *
 * The followings are the available model relations:
 * @property MiscChrgDetails[] $miscChrgDetails
 * @property MiscServiceDetails[] $miscServiceDetails
 * @property CashierAccountSubtypes $accountType
 */
class OtherServices extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_other_services';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('service_code, alt_service_code, name, history, modify_id, create_id', 'required'),
			array('account_type, is_discountable, is_not_socialized, is_billing_related, is_ER_default, is_IP_default, is_OP_default, lockflag, packageflag, max_usage, dept_nr', 'numerical', 'integerOnly'=>true),
			array('service_code, alt_service_code', 'length', 'max'=>12),
			array('name_short', 'length', 'max'=>15),
			array('description', 'length', 'max'=>200),
			array('price', 'length', 'max'=>10),
			array('modify_id, create_id', 'length', 'max'=>35),
			array('modify_time, create_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('service_code, alt_service_code, name, name_short, description, price, account_type, is_discountable, is_not_socialized, is_billing_related, is_ER_default, is_IP_default, is_OP_default, lockflag, packageflag, max_usage, dept_nr, history, modify_id, modify_time, create_id, create_time', 'safe', 'on'=>'search'),
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
			'miscChrgDetails' => array(self::HAS_MANY, 'MiscChrgDetails', 'service_code'),
			'miscServiceDetails' => array(self::HAS_MANY, 'MiscServiceDetails', 'service_code'),
			'accountType' => array(self::BELONGS_TO, 'CashierAccountSubtypes', 'account_type'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'service_code' => 'Service Code',
			'alt_service_code' => 'Alt Service Code',
			'name' => 'Name',
			'name_short' => 'Name Short',
			'description' => 'Description',
			'price' => 'Price',
			'account_type' => 'Account Type',
			'is_discountable' => 'Is Discountable',
			'is_not_socialized' => 'Is Not Socialized',
			'is_billing_related' => 'Is Billing Related',
			'is_ER_default' => 'Is Er Default',
			'is_IP_default' => 'Is Ip Default',
			'is_OP_default' => 'Is Op Default',
			'lockflag' => 'Lockflag',
			'packageflag' => 'Packageflag',
			'max_usage' => 'Max Usage',
			'dept_nr' => 'Dept Nr',
			'history' => 'History',
			'modify_id' => 'Modify',
			'modify_time' => 'Modify Time',
			'create_id' => 'Create',
			'create_time' => 'Create Time',
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

		$criteria->compare('service_code',$this->service_code,true);
		$criteria->compare('alt_service_code',$this->alt_service_code,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('name_short',$this->name_short,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('price',$this->price,true);
		$criteria->compare('account_type',$this->account_type);
		$criteria->compare('is_discountable',$this->is_discountable);
		$criteria->compare('is_not_socialized',$this->is_not_socialized);
		$criteria->compare('is_billing_related',$this->is_billing_related);
		$criteria->compare('is_ER_default',$this->is_ER_default);
		$criteria->compare('is_IP_default',$this->is_IP_default);
		$criteria->compare('is_OP_default',$this->is_OP_default);
		$criteria->compare('lockflag',$this->lockflag);
		$criteria->compare('packageflag',$this->packageflag);
		$criteria->compare('max_usage',$this->max_usage);
		$criteria->compare('dept_nr',$this->dept_nr);
		$criteria->compare('history',$this->history,true);
		$criteria->compare('modify_id',$this->modify_id,true);
		$criteria->compare('modify_time',$this->modify_time,true);
		$criteria->compare('create_id',$this->create_id,true);
		$criteria->compare('create_time',$this->create_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return OtherServices the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function othersSearch($query = array()) {
		$criteria = new CDbCriteria();

		if (!empty($query)) {
			$criteria->addSearchCondition('name', $query['name'], true, 'AND');
		}

		// $criteria->addCondition('is_deleted = 0', 'AND');

		return $this->findAll($criteria);
	}

	public function getItem_code(){
		return $this->service_code;
	}

	public function getPrice_cash(){
		return $this->price;
	}

	public function getAllOrService(){
		$criteria = new CDbCriteria();

		$criteria->condition = "t.`name` LIKE '% OR %' OR t.`name` LIKE '% OR' OR  t.`name` LIKE 'OR %'";

		return new CArrayDataProvider(
            $this->findAll($criteria), 
            array(
                'pagination' => array(
                    'pageSize' => 10,
                ),
                'keyField' => false
            )
        );
	}
}
