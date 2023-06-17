<?php

/**
 * This is the model class for table "seg_ops_rvs".
 *
 * The followings are the available columns in table 'seg_ops_rvs':
 * @property string $code
 * @property string $description
 * @property integer $rvu
 * @property integer $is_active
 * @property string $modify_id
 * @property string $modify_date
 * @property string $create_id
 * @property string $create_date
 *
 * The followings are the available model relations:
 * @property MiscOpsDetails[] $miscOpsDetails
 * @property OpsNormaldelivery[] $opsNormaldeliveries
 * @property OpsPersonell[] $opsPersonells
 * @property OpsServ[] $segOpsServs
 */
class OpsRvs extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_ops_rvs';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('code, description, modify_id, modify_date, create_id', 'required'),
			array('rvu, is_active', 'numerical', 'integerOnly'=>true),
			array('code', 'length', 'max'=>12),
			array('modify_id, create_id', 'length', 'max'=>35),
			array('create_date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('code, description, rvu, is_active, modify_id, modify_date, create_id, create_date', 'safe', 'on'=>'search'),
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
			'miscOpsDetails' => array(self::HAS_MANY, 'MiscOpsDetails', 'ops_code'),
			'opsNormaldeliveries' => array(self::HAS_MANY, 'OpsNormaldelivery', 'ops_code'),
			'opsPersonells' => array(self::HAS_MANY, 'OpsPersonell', 'ops_code'),
			'segOpsServs' => array(self::MANY_MANY, 'OpsServ', 'seg_ops_servdetails(ops_code, refno)'),
			'caseRatePackage' => array(self::HAS_ONE, 'CaseRatePackages', 'code'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'code' => 'Code',
			'description' => 'Description',
			'rvu' => 'Rvu',
			'is_active' => 'Is Active',
			'modify_id' => 'Modify',
			'modify_date' => 'Modify Date',
			'create_id' => 'Create',
			'create_date' => 'Create Date',
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

		$criteria->compare('code',$this->code,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('rvu',$this->rvu);
		$criteria->compare('is_active',$this->is_active);
		$criteria->compare('modify_id',$this->modify_id,true);
		$criteria->compare('modify_date',$this->modify_date,true);
		$criteria->compare('create_id',$this->create_id,true);
		$criteria->compare('create_date',$this->create_date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return OpsRvs the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function icpmSearch($query = array()) {
		$criteria = new CDbCriteria();

		$criteria->with = array(
			'caseRatePackage' => array('joinType' => 'INNER JOIN')
		);

		if (!empty($query)) {
			$criteria->addSearchCondition('t.code', $query['code'], true, 'AND');
			$criteria->addSearchCondition('t.description', $query['desc'], true, 'AND');
		}

		$criteria->addCondition('case_type = "p"', 'AND');

		$criteria->limit = 50;

		return $this->findAll($criteria);
	}
}
