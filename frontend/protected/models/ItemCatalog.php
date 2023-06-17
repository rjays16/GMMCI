<?php

/**
 * This is the model class for table "care_pharma_products_main".
 *
 * The followings are the available columns in table 'care_pharma_products_main':
 * @property string $bestellnum
 * @property string $inventory_code
 * @property string $artikelnum
 * @property string $industrynum
 * @property string $artikelname
 * @property string $generic
 * @property string $description
 * @property string $packing
 * @property string $type_nr
 * @property string $prod_class
 * @property integer $minorder
 * @property integer $maxorder
 * @property string $proorder
 * @property string $picfile
 * @property string $encoder
 * @property string $enc_date
 * @property string $enc_time
 * @property integer $lock_flag
 * @property integer $is_socialized
 * @property integer $is_restricted
 * @property integer $classification
 * @property string $medgroup
 * @property string $cave
 * @property string $status
 * @property string $price_cash
 * @property string $price_charge
 * @property string $unit
 * @property string $history
 * @property string $modify_id
 * @property string $modify_time
 * @property string $create_id
 * @property string $create_time
 * @property string $ref_source
 * @property integer $is_deleted
 * @property integer $category_id
 * @property integer $is_vat
 *
 * The followings are the available model relations:
 * @property SegProductClassification $classification0
 * @property SegTypeProduct $typeNr
 * @property SegBloodComponent[] $segBloodComponents
 * @property SegDeliveryDetails[] $segDeliveryDetails
 * @property SegEodInventory[] $segEodInventories
 * @property SegEquipmentOrders[] $segEquipmentOrders
 * @property SegExpiryInventory[] $segExpiryInventories
 * @property SegInternalRequestDetails[] $segInternalRequestDetails
 * @property SegInventory[] $segInventories
 * @property SegInventoryAdjustmentDetails[] $segInventoryAdjustmentDetails
 * @property SegIssuanceDetails[] $segIssuanceDetails
 * @property SegItemExtended $segItemExtended
 * @property SegMorePhorderDetails[] $segMorePhorderDetails
 * @property SegOrSponge[] $segOrSponges
 * @property SegPharmaOrders[] $segPharmaOrders
 * @property SegPharmaPrices $segPharmaPrices
 * @property SegPharmaProductsAvailability[] $segPharmaProductsAvailabilities
 * @property SegProductClassification[] $segProductClassifications
 * @property SegPharmaRdetails[] $segPharmaRdetails
 * @property SegPharmaReturnItems[] $segPharmaReturnItems
 * @property SegPoD[] $segPoDs
 * @property SegRequestsServed[] $segRequestsServeds
 * @property SegSkuCatalog[] $segSkuCatalogs
 */
class ItemCatalog extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'care_pharma_products_main';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('artikelnum, industrynum, artikelname, generic, description, packing, proorder, picfile, encoder, enc_date, enc_time, medgroup, cave, history, modify_time, category_id', 'required'),
			array('minorder, maxorder, lock_flag, is_socialized, is_restricted, classification, is_deleted, category_id, is_vat', 'numerical', 'integerOnly'=>true),
			array('bestellnum, inventory_code', 'length', 'max'=>12),
			array('type_nr, price_cash, price_charge, unit, ref_source', 'length', 'max'=>10),
			array('prod_class', 'length', 'max'=>1),
			array('status', 'length', 'max'=>20),
			array('modify_id, create_id', 'length', 'max'=>35),
			array('create_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('bestellnum, inventory_code, artikelnum, industrynum, artikelname, generic, description, packing, type_nr, prod_class, minorder, maxorder, proorder, picfile, encoder, enc_date, enc_time, lock_flag, is_socialized, is_restricted, classification, medgroup, cave, status, price_cash, price_charge, unit, history, modify_id, modify_time, create_id, create_time, ref_source, is_deleted, category_id, is_vat', 'safe', 'on'=>'search'),
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
			'classification0' => array(self::BELONGS_TO, 'SegProductClassification', 'classification'),
			'typeNr' => array(self::BELONGS_TO, 'SegTypeProduct', 'type_nr'),
			'segBloodComponents' => array(self::MANY_MANY, 'SegBloodComponent', 'seg_blood_products_item(item_code, service_code)'),
			'segDeliveryDetails' => array(self::HAS_MANY, 'SegDeliveryDetails', 'item_code'),
			'segEodInventories' => array(self::HAS_MANY, 'SegEodInventory', 'item_code'),
			'segEquipmentOrders' => array(self::MANY_MANY, 'SegEquipmentOrders', 'seg_equipment_order_items(equipment_id, refno)'),
			'segExpiryInventories' => array(self::HAS_MANY, 'SegExpiryInventory', 'item_code'),
			'segInternalRequestDetails' => array(self::HAS_MANY, 'SegInternalRequestDetails', 'item_code'),
			'segInventories' => array(self::HAS_MANY, 'SegInventory', 'item_code'),
			'segInventoryAdjustmentDetails' => array(self::HAS_MANY, 'SegInventoryAdjustmentDetails', 'item_code'),
			'segIssuanceDetails' => array(self::HAS_MANY, 'SegIssuanceDetails', 'item_code'),
			'segItemExtended' => array(self::HAS_ONE, 'SegItemExtended', 'item_code'),
			'segMorePhorderDetails' => array(self::HAS_MANY, 'SegMorePhorderDetails', 'bestellnum'),
			'segOrSponges' => array(self::HAS_MANY, 'SegOrSponge', 'sponge_code'),
			'segPharmaOrders' => array(self::MANY_MANY, 'SegPharmaOrders', 'seg_pharma_order_items(bestellnum, refno)'),
			'segPharmaPrices' => array(self::HAS_ONE, 'SegPharmaPrices', 'bestellnum'),
			'segPharmaProductsAvailabilities' => array(self::HAS_MANY, 'SegPharmaProductsAvailability', 'bestellnum'),
			'segProductClassifications' => array(self::MANY_MANY, 'SegProductClassification', 'seg_pharma_products_classification(bestellnum, class_code)'),
			'segPharmaRdetails' => array(self::HAS_MANY, 'SegPharmaRdetails', 'bestellnum'),
			'segPharmaReturnItems' => array(self::HAS_MANY, 'SegPharmaReturnItems', 'bestellnum'),
			'segPoDs' => array(self::HAS_MANY, 'SegPoD', 'item_code'),
			'segRequestsServeds' => array(self::HAS_MANY, 'SegRequestsServed', 'item_code'),
			'segSkuCatalogs' => array(self::HAS_MANY, 'SegSkuCatalog', 'item_code'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'bestellnum' => 'Bestellnum',
			'inventory_code' => 'Inventory Code',
			'artikelnum' => 'Artikelnum',
			'industrynum' => 'Industrynum',
			'artikelname' => 'Artikelname',
			'generic' => 'Generic',
			'description' => 'Description',
			'packing' => 'Packing',
			'type_nr' => 'Type Nr',
			'prod_class' => 'Prod Class',
			'minorder' => 'Minorder',
			'maxorder' => 'Maxorder',
			'proorder' => 'Proorder',
			'picfile' => 'Picfile',
			'encoder' => 'Encoder',
			'enc_date' => 'Enc Date',
			'enc_time' => 'Enc Time',
			'lock_flag' => 'Lock Flag',
			'is_socialized' => 'Is Socialized',
			'is_restricted' => 'Is Restricted',
			'classification' => 'Classification',
			'medgroup' => 'Medgroup',
			'cave' => 'Cave',
			'status' => 'Status',
			'price_cash' => 'Price Cash',
			'price_charge' => 'Price Charge',
			'unit' => 'Unit',
			'history' => 'History',
			'modify_id' => 'Modify',
			'modify_time' => 'Modify Time',
			'create_id' => 'Create',
			'create_time' => 'Create Time',
			'ref_source' => 'Ref Source',
			'is_deleted' => 'Is Deleted',
			'category_id' => 'Category',
			'is_vat' => 'Is Vat',
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

		$criteria->compare('bestellnum',$this->bestellnum,true);
		$criteria->compare('inventory_code',$this->inventory_code,true);
		$criteria->compare('artikelnum',$this->artikelnum,true);
		$criteria->compare('industrynum',$this->industrynum,true);
		$criteria->compare('artikelname',$this->artikelname,true);
		$criteria->compare('generic',$this->generic,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('packing',$this->packing,true);
		$criteria->compare('type_nr',$this->type_nr,true);
		$criteria->compare('prod_class',$this->prod_class,true);
		$criteria->compare('minorder',$this->minorder);
		$criteria->compare('maxorder',$this->maxorder);
		$criteria->compare('proorder',$this->proorder,true);
		$criteria->compare('picfile',$this->picfile,true);
		$criteria->compare('encoder',$this->encoder,true);
		$criteria->compare('enc_date',$this->enc_date,true);
		$criteria->compare('enc_time',$this->enc_time,true);
		$criteria->compare('lock_flag',$this->lock_flag);
		$criteria->compare('is_socialized',$this->is_socialized);
		$criteria->compare('is_restricted',$this->is_restricted);
		$criteria->compare('classification',$this->classification);
		$criteria->compare('medgroup',$this->medgroup,true);
		$criteria->compare('cave',$this->cave,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('price_cash',$this->price_cash,true);
		$criteria->compare('price_charge',$this->price_charge,true);
		$criteria->compare('unit',$this->unit,true);
		$criteria->compare('history',$this->history,true);
		$criteria->compare('modify_id',$this->modify_id,true);
		$criteria->compare('modify_time',$this->modify_time,true);
		$criteria->compare('create_id',$this->create_id,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('ref_source',$this->ref_source,true);
		$criteria->compare('is_deleted',$this->is_deleted);
		$criteria->compare('category_id',$this->category_id);
		$criteria->compare('is_vat',$this->is_vat);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ItemCatalog the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
