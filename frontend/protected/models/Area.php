<?php

/**
 * This is the model class for table "seg_areas".
 *
 * The followings are the available columns in table 'seg_areas':
 * @property string $area_code
 * @property string $area_name
 * @property integer $allow_socialized
 * @property integer $lockflag
 * @property integer $dept_nr
 * @property integer $ward_nr
 * @property integer $has_stocks
 *
 * The followings are the available model relations:
 * @property CareDepartment $deptNr
 * @property SegDelivery[] $segDeliveries
 * @property SegInventory[] $segInventories
 * @property SegMorePhorder[] $segMorePhorders
 * @property SegSkuCatalog[] $segSkuCatalogs
 */
class Area extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_areas';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('area_name, dept_nr', 'required'),
			array('allow_socialized, lockflag, dept_nr, ward_nr, has_stocks', 'numerical', 'integerOnly'=>true),
			array('area_code', 'length', 'max'=>10),
			array('area_name', 'length', 'max'=>80),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('area_code, area_name, allow_socialized, lockflag, dept_nr, ward_nr, has_stocks', 'safe', 'on'=>'search'),
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
			'deptNr' => array(self::BELONGS_TO, 'CareDepartment', 'dept_nr'),
			'segDeliveries' => array(self::HAS_MANY, 'SegDelivery', 'area_code'),
			'segInventories' => array(self::HAS_MANY, 'SegInventory', 'area_code'),
			'segSkuCatalogs' => array(self::HAS_MANY, 'SegSkuCatalog', 'area_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'area_code' => 'Area Code',
			'area_name' => 'Area Name',
			'allow_socialized' => 'Allow Socialized',
			'lockflag' => 'Lockflag',
			'dept_nr' => 'Dept Nr',
			'ward_nr' => 'Ward Nr',
			'has_stocks' => 'Has Stocks',
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

		$criteria->compare('area_code',$this->area_code,true);
		$criteria->compare('area_name',$this->area_name,true);
		$criteria->compare('allow_socialized',$this->allow_socialized);
		$criteria->compare('lockflag',$this->lockflag);
		$criteria->compare('dept_nr',$this->dept_nr);
		$criteria->compare('ward_nr',$this->ward_nr);
		$criteria->compare('has_stocks',$this->has_stocks);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Area the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
