<?php

/**
 * This is the model class for table "seg_item_extended".
 *
 * The followings are the available columns in table 'seg_item_extended':
 * @property string $item_code
 * @property string $avg_cost
 * @property double $min_qty
 * @property string $pack_unit_id
 * @property string $pc_unit_id
 * @property double $qty_per_pack
 *
 * The followings are the available model relations:
 * @property CarePharmaProductsMain $itemCode
 * @property SegUnit $packUnit
 * @property SegUnit $pcUnit
 */
class ItemPackaging extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_item_extended';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('item_code, pack_unit_id, pc_unit_id', 'required'),
			array('min_qty, qty_per_pack', 'numerical'),
			array('item_code', 'length', 'max'=>25),
			array('avg_cost, pack_unit_id, pc_unit_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('item_code, avg_cost, min_qty, pack_unit_id, pc_unit_id, qty_per_pack', 'safe', 'on'=>'search'),
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
			'itemCode' => array(self::BELONGS_TO, 'CarePharmaProductsMain', 'item_code'),
			'packUnit' => array(self::BELONGS_TO, 'SegUnit', 'pack_unit_id'),
			'pcUnit' => array(self::BELONGS_TO, 'SegUnit', 'pc_unit_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'item_code' => 'Item Code',
			'avg_cost' => 'Avg Cost',
			'min_qty' => 'Min Qty',
			'pack_unit_id' => 'Pack Unit',
			'pc_unit_id' => 'Pc Unit',
			'qty_per_pack' => 'Qty Per Pack',
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

		$criteria->compare('item_code',$this->item_code,true);
		$criteria->compare('avg_cost',$this->avg_cost,true);
		$criteria->compare('min_qty',$this->min_qty);
		$criteria->compare('pack_unit_id',$this->pack_unit_id,true);
		$criteria->compare('pc_unit_id',$this->pc_unit_id,true);
		$criteria->compare('qty_per_pack',$this->qty_per_pack);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ItemPackaging the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
