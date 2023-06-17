<?php

/**
 * This is the model class for table "seg_inventory_production".
 *
 * The followings are the available columns in table 'seg_inventory_production':
 * @property string $id
 * @property string $package_id
 * @property string $production_date
 * @property string $expiry_date
 * @property string $area
 * @property string $serial_no
 * @property string $lot_no
 * @property integer $quantity
 * @property integer $unit
 * @property double $unit_price
 * @property integer $is_posted
 * @property integer $to_smaller
 * @property string $create_id
 * @property string $create_time
 * @property string $modify_id
 * @property string $modify_time
 * @property string $history
 *
 * @property string $packageName
 * @property string $isPostedToInventory
 * @property string $areaName
 *
 * The followings are the available model relations:
 * @property ItemCatalog $package
 * @property ProductionItem $productionItems[]
 * @property Area $areaInfo
 * @property SegHis\models\inventory\Unit $unitInfo
 * @property  Array $attributes
 */
class Production extends CareActiveRecord
{

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_inventory_production';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('package_id, quantity, unit_price, production_date, area, unit', 'required'),
			array('quantity', 'numerical', 'integerOnly'=>true, 'min' => 1),
			array('unit_price, is_posted, to_smaller', 'numerical'),
			array('package_id', 'length', 'max'=>25),
			array('area, serial_no, lot_no, create_id, modify_id', 'length', 'max'=>11),
			array('production_date, expiry_date, create_time, modify_time, history', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('areaName ,packageName, isPostedToInventory, id, package_id, production_date,
			expiry_date, area, serial_no, lot_no, quantity, unit_price,
			is_posted, create_id, create_time, modify_id, modify_time,
			history', 'safe', 'on'=>'search'),
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
			'package' => array(self::BELONGS_TO, 'ItemCatalog', 'package_id'),
			'productionItems' => array(self::HAS_MANY, 'ProductionItem', 'production_id', 'condition' => 'productionItems.is_deleted = 0'),
			'areaInfo' => array(self::HAS_ONE, 'Area', array('area_code' => 'area')),
			'unitInfo' => array(self::HAS_ONE, 'SegHis\models\inventory\Unit', array('unit_id' => 'unit')),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'package_id' => 'Package',
			'production_date' => 'Production Date',
			'packageName' => 'Package Name',
			'areaName' => 'Area',
			'expiry_date' => 'Expiration Date',
			'area' => 'Area',
			'serial_no' => 'Serial #',
			'lot_no' => 'Lot #',
			'quantity' => 'Quantity',
			'unit' => 'Unit',
			'unit_price' => 'Unit Price',
			'is_posted' => 'Is Posted to Inventory',
			'to_smaller' => 'Divide to Smaller Unit(s)',
			'create_id' => 'Create',
			'create_time' => 'Create Time',
			'modify_id' => 'Modify',
			'modify_time' => 'Modify Time',
			'history' => 'History',
		);
	}

	public function getIsPostedToInventory(){
		return $this->is_posted == 1 ? 'Yes' : 'No';
	}

	public function setIsPostedToInventory($value){
		return $this->isPostedToInventory = $value;
	}

	public function getPackageName(){
		return $this->package->artikelname;
	}

	public function setPackageName($value){
		$this->packageName = $value;
	}

	public function getAreaName(){
		return $this->areaInfo->area_name;
	}

	public function setAreaName($value){
		$this->areaName = $value;
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('package_id',$this->package_id,true);
		$criteria->compare('production_date',$this->production_date?date('Y-m-d',strtotime($this->production_date)):'',true);
		$criteria->compare('expiry_date',$this->expiry_date?date('Y-m-d',strtotime($this->expiry_date)):'',true);
		$criteria->compare('area',$this->area,true);
		$criteria->compare('serial_no',$this->serial_no,true);
		$criteria->compare('lot_no',$this->lot_no,true);
		$criteria->compare('quantity',$this->quantity);
		$criteria->compare('unit',$this->unit);
		$criteria->compare('unit_price',$this->unit_price);
		$criteria->compare('create_id',$this->create_id,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('modify_id',$this->modify_id,true);
		$criteria->compare('modify_time',$this->modify_time,true);
		$criteria->compare('history',$this->history,true);
		$criteria->compare('package.artikelname',$this->packageName,true);
		$criteria->compare('areaInfo.area_name',$this->areaName,true);

		if(strtolower($this->isPostedToInventory) == 'yes')
			$criteria->compare('is_posted',1,true);
		else if(strtolower($this->isPostedToInventory) == 'no')
			$criteria->compare('is_posted',0,true);

		$criteria->with = array(
			'package' => array(
				'select' => 'artikelname'
			),
			'areaInfo' => array(
				'select' => 'area_name'
			),
		);

		$dataProvider = new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));

		$dataProvider->sort->attributes = array(
			'packageName' => array(
				'asc' => 'package.artikelname ASC',
				'desc' => 'package.artikelname DESC'
			),
			'isPostedToInventory' => array(
				'asc' => 'is_posted ASC',
				'desc' => 'is_posted DESC'
			),
			'production_date',
			'expiry_date',
			'areaName' => array(
				'asc' => 'areaInfo.area_name ASC',
				'desc' => 'areaInfo.area_name DESC'
			),
			'serial_no',
			'is_posted',
		);

		return $dataProvider;
	}

	public static function getNewRefNo(){
		$model = new Production();
		$criteria=new CDbCriteria;
		$criteria->select='max(id) AS id';
		$row = $model->model()->find($criteria);
		if($row['id']){
			return $row['id'] + 1;
		}else{
			return date('Y') . "000001";
		}
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Production the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	protected function beforeSave()
	{
		if ($this->isNewRecord) {
			$this->history = 'Created by ' . Yii::app()->user->getId() . ' at ' . date('Y-m-d h:i:s a') . "\n";
		}else{
			$this->history .= 'Updated by ' . Yii::app()->user->getId() . ' at ' . date('Y-m-d h:i:s a') . "\n";
		}
		$this->production_date = date('Y-m-d H:i:s',strtotime($this->production_date));
		$this->expiry_date = date('Y-m-d H:i:s',strtotime($this->expiry_date));

		return parent::beforeSave();
	}

	public function updateInventory()
	{
		require_once dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))). '/include/care_api_classes/inventory/class_inventory_helper.php';
		$inventory = new Inventory();
		$inventory->setInventoryParams($this->package_id,$this->area,$this->id,REPACK);

		if($this->to_smaller){
			$itemUnit = ItemPackaging::model()->findByPk($this->package_id)->pc_unit_id;
			$statusOk = $inventory->remInventory($this->quantity,$itemUnit);
		}else{
			$statusOk = $inventory->addInventory(
				$this->quantity,
				$this->unit,
				$this->expiry_date,
				$this->serial_no,
				$this->production_date,
				$this->unit_price,
				$this->lot_no
			);
		}

		if(!$statusOk){
			$this->addErrors(array('Error updating inventory for production.'));
			return false;
		}

		return true;

	}

}
