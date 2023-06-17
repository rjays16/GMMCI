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
 *
 * The followings are the available model relations:
 * @property SegProductClassification $classification0
 * @property SegTypeProduct $typeNr
 */
class PharmaProductsMain extends CActiveRecord
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
			array('minorder, maxorder, lock_flag, is_socialized, is_restricted, classification, is_deleted, category_id', 'numerical', 'integerOnly'=>true),
			array('bestellnum, inventory_code', 'length', 'max'=>12),
			array('type_nr, price_cash, price_charge, unit, ref_source', 'length', 'max'=>10),
			array('prod_class', 'length', 'max'=>1),
			array('status', 'length', 'max'=>20),
			array('modify_id, create_id', 'length', 'max'=>35),
			array('create_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('bestellnum, inventory_code, artikelnum, industrynum, artikelname, generic, description, packing, type_nr, prod_class, minorder, maxorder, proorder, picfile, encoder, enc_date, enc_time, lock_flag, is_socialized, is_restricted, classification, medgroup, cave, status, price_cash, price_charge, unit, history, modify_id, modify_time, create_id, create_time, ref_source, is_deleted, category_id', 'safe', 'on'=>'search'),
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
			'artikelname' => 'Name',
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

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PharmaProductsMain the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function medSearch($query = array()) {
		$criteria = new CDbCriteria();

		if (!empty($query)) {
			$criteria->addSearchCondition('artikelname', $query['name'], true, 'OR');
			$criteria->addSearchCondition('generic', $query['name'], true, 'OR');
		}

		$criteria->limit = 20;

		$criteria->addCondition('is_deleted = 0', 'AND');

		return $this->findAll($criteria);
	}

	public function getName(){
		return $this->artikelname. ' ' .$this->generic;
	}

	public function getItem_code(){
		return $this->bestellnum;
	}
}
