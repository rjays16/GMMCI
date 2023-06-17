<?php

/**
 * This is the model class for table "seg_inventory_ledger".
 *
 * The followings are the available columns in table 'seg_inventory_ledger':
 * @property string $post_uid
 * @property string $post_date
 * @property string $tref_no
 * @property string $tr_code
 * @property string $tr_date
 * @property string $sku_id
 * @property double $unit_cost
 * @property double $movement
 * @property double $prev_qty
 * @property double $prev_cost
 * @property double $packqty
 * @property string $encounter_no
 *
 * The followings are the available model relations:
 * @property SmedEncounter $encounterNo
 * @property SegSkuCatalog $sku
 */
class InventoryLedger extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_inventory_ledger';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('unit_cost, mvmnt_qty, prev_qty, prev_cost, packqty', 'numerical'),
			array('post_uid, tref_no, sku_id', 'length', 'max'=>36),
			array('tr_code', 'length', 'max'=>3),
			// array('encounter_no', 'length', 'max'=>12),
			array('tr_date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('post_uid, tref_no, tr_code, tr_date, sku_id, unit_cost, mvmnt_qty, prev_qty, prev_cost, packqty', 'safe', 'on'=>'search'),
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
			// 'encounterNo' => array(self::BELONGS_TO, 'SmedEncounter', 'encounter_no'),
			'sku' => array(self::BELONGS_TO, 'SegSkuCatalog', 'sku_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'post_uid' => 'Post Uid',
			// 'post_date' => 'Post Date',
			'tref_no' => 'Tref No',
			'tr_code' => 'Tr Code',
			'tr_date' => 'Tr Date',
			'sku_id' => 'Sku',
			'unit_cost' => 'Unit Cost',
			'mvmnt_qty' => 'Movement',
			'prev_qty' => 'Prev Qty',
			'prev_cost' => 'Prev Cost',
			'packqty' => 'Pack Qty',
			// 'encounter_no' => 'Encounter No',
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

		$criteria->compare('post_uid',$this->post_uid,true);
		// $criteria->compare('post_date',$this->post_date,true);
		$criteria->compare('tref_no',$this->tref_no,true);
		$criteria->compare('tr_code',$this->tr_code,true);
		$criteria->compare('tr_date',$this->tr_date,true);
		$criteria->compare('sku_id',$this->sku_id,true);
		$criteria->compare('unit_cost',$this->unit_cost);
		$criteria->compare('mvmnt_qty',$this->mvmnt_qty);
		$criteria->compare('prev_qty',$this->prev_qty);
		$criteria->compare('prev_cost',$this->prev_cost);
		$criteria->compare('packqty',$this->packqty);
		// $criteria->compare('encounter_no',$this->encounter_no,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return InventoryLedger the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    /***
     * Public function which inserts an entry for the inventory movement in the inventory ledger.
     * @params string $refno
     * @params string $trcode
     * @params date   $trdate
     * @params string $skuid
     * @params double $qty
     * @params double $packqty
     * @params double $ucost
     *
     * @return boolean TRUE if successfully saved, FALSE otherwise.
     */
    public function postSkuItemMovement($enc_no, $refno, $trcode, $trdate, $skuid, $qty, $packqty, $ucost = 0)
    {
        try {
            $skuobj = new SkuInventory();
            $trdate = (empty($trdate)) ? date("Y-m-d") : $trdate;
            $ucost = (empty($ucost)) ? $skuobj->getSKUAvgCost($skuid, $trdate) : $ucost;

            $this->attributes = array(
                'tref_no' => $refno,
                'tr_code' => $trcode,
                'tr_date' => $trdate,
                'sku_id'  => $skuid,
                'unit_cost' => doubleval($ucost),
                'mvmnt_qty' => doubleval($qty),
                'packqty' => doubleval($packqty),
                // 'encounter_no' => (empty($enc_no) ? NULL : $enc_no)
            );
            return $this->save();
        } catch (Exception $ex) {
            return false;
        }
    }

    /***
     * Public function which clears the table for temporarily holding inventory movement
     * to make the proper adjustments should a transaction affect later transactions.
     */
    public function clearTmpTable()
    {
        return Yii::app()->db->createCommand("DELETE FROM seg_inventory_ledger_temp;")->execute();
    }
}
