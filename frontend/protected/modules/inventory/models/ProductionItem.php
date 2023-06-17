<?php

/**
 * This is the model class for table "seg_inventory_production_item".
 *
 * The followings are the available columns in table 'seg_inventory_production_item':
 * @property string $id
 * @property string $production_id
 * @property string $item_id
 * @property integer $quantity
 * @property integer $is_deleted
 * @property string $create_id
 * @property string $create_time
 * @property string $modify_id
 * @property string $modify_time
 * @property string $history
 *
 * @property string $itemName
 * @property string $itemPrice
 *
 * The followings are the available model relations:
 * @property Production $production
 * @property ItemCatalog $product
 */
class ProductionItem extends CareActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_inventory_production_item';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('production_id, item_id, quantity', 'required'),
			array('quantity', 'numerical', 'integerOnly'=>true, 'min' => 1),
			array('production_id, create_id', 'length', 'max'=>11),
			array('item_id', 'length', 'max'=>25),
			array('is_deleted, create_id, create_time, modify_id, modify_time, history', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('itemName, itemPrice, id, production_id, item_id, quantity', 'safe', 'on'=>'search'),
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
			'production' => array(self::BELONGS_TO, 'Production', 'production_id'),
			'product' => array(self::BELONGS_TO, 'ItemCatalog', 'item_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'production_id' => 'Production',
			'item_id' => 'Item',
			'quantity' => 'Quantity',
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
	 * @return \CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('production_id',$this->production_id,true);
		$criteria->compare('item_id',$this->item_id,true);
		$criteria->compare('quantity',$this->quantity);
		$criteria->compare('create_id',$this->create_id,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('product.artikelname',$this->itemName,true);

		$criteria->with = array(
			'product' => array(
				'select' => 'product.artikelname, product.price_cash'
			)
		);

		$dataProvider = new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));

		$dataProvider->sort->attributes = array(
			'itemName' => array(
				'asc' => 'product.artikelname ASC',
				'desc' => 'product.artikelname DESC'
			),
			'quantity',
			'itemPrice' => array(
				'asc' => 'product.price_cash ASC',
				'desc' => 'product.price_cash DESC'
			),
		);

		return $dataProvider;
	}

	public function getItemName(){
		return $this->product->artikelname;
	}

	public function setItemName($value){
		$this->itemName = $value;
	}

	public function getItemPrice(){
		return number_format($this->product->price_cash,2);
	}

	public function setItemPrice($value){
		$this->itemPrice = $value;
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ProductionItem the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function updateInventory()
	{
		require_once dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/include/care_api_classes/inventory/class_inventory_helper.php';
		$inventory = new Inventory();
		$inventory->setInventoryParams($this->item_id,$this->production->area,$this->production->id,REPACK);

		if($this->production->to_smaller){
			$statusOk = $inventory->addInventory(
				$this->quantity,
				$this->production->unit,
				$this->production->expiry_date,
				$this->production->serial_no,
				$this->production->production_date,
				$this->product->price_cash,
				$this->production->lot_no
			);
		}else{
			$itemUnit = ItemPackaging::model()->findByPk($this->item_id)->pc_unit_id;
			$statusOk = $inventory->remInventory($this->quantity,$itemUnit);
		}

		if(!$statusOk){
			$this->addErrors(array('Error updating inventory for item '.$this->product->artikelname.'.'));
			return false;
		}
		return true;
	}

}
