<?php
/**
 * Cf4Medicine.php
 *
 * @author Jan Chris S. Ogel <iamjc93@gmail.com>
 * @copyright (c) 2019, Segworks Technologies Corporation (http://www.segworks.com)
 */

/**
 * This is the model class for table "seg_cf4_medicine".
 *
 * The followings are the available columns in table 'seg_cf4_medicine':
 * @property string $id
 * @property string $entry_id
 * @property string $encounter_nr
 * @property string $drug_code
 * @property string $generic
 * @property string $quantity
 * @property string $route
 * @property string $frequency
 * @property string $cost
 * @property integer $is_pndf
 * @property string $created_at
 * @property string $modify
 * @property string $modified_by
 * @property string $deleted_by
 * @property string $deleted_at
 * @property integer $is_deleted
 *
 * The followings are the available model relations:
 * @property Cf4 $entry
 * @property Encounter $encounterNo
 * @property PhilMedicine $drugCode
 */
class Cf4Medicine extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_cf4_medicine';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('quantity, route, frequency, cost', 'required'),
			array('is_pndf, is_deleted', 'numerical', 'integerOnly'=>true),
			array('id, entry_id', 'length', 'max'=>36),
			array('encounter_nr', 'length', 'max'=>15),
			array('drug_code', 'length', 'max'=>40),
			array('route', 'length', 'max'=>500),
			array('frequency', 'length', 'max'=>50),
			array('quantity, cost', 'length', 'max'=>12),
			array('modified_by, deleted_by', 'length', 'max'=>50),
			array('generic, created_at, modify, deleted_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, entry_id, encounter_nr, drug_code, generic, quantity, route, frequency, cost, is_pndf, created_at, modify, modified_by, deleted_by, deleted_at, is_deleted', 'safe', 'on'=>'search'),
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
			'entry' => array(self::BELONGS_TO, 'Cf4', 'entry_id'),
			'encounterNo' => array(self::BELONGS_TO, 'Encounter', 'encounter_nr'),
			'drugCode' => array(self::BELONGS_TO, 'PhilMedicine', 'drug_code'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'entry_id' => 'Entry',
			'encounter_nr' => 'Encounter Nr',
			'drug_code' => '',
			'generic' => 'Generic / Drug Description',
			'quantity' => 'Quantity',
			'route' => 'Route',
			'frequency' => 'Frequency',
			'cost' => 'Total Amount',
			'is_pndf' => 'Is Pndf',
			'created_at' => 'Created At',
			'modify' => 'Modify',
			'modified_by' => 'Modified By',
			'deleted_by' => 'Deleted By',
			'deleted_at' => 'Deleted At',
			'is_deleted' => 'Is Deleted',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('entry_id',$this->entry_id,true);
		$criteria->compare('encounter_nr',$this->encounter_nr,true);
		$criteria->compare('drug_code',$this->drug_code,true);
		$criteria->compare('generic',$this->generic,true);
		$criteria->compare('quantity',$this->quantity,true);
		$criteria->compare('route',$this->route,true);
		$criteria->compare('frequency',$this->frequency,true);
		$criteria->compare('cost',$this->cost,true);
		$criteria->compare('is_pndf',$this->is_pndf);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('modify',$this->modify,true);
		$criteria->compare('modified_by',$this->modified_by,true);
		$criteria->compare('deleted_by',$this->deleted_by,true);
		$criteria->compare('deleted_at',$this->deleted_at,true);
		$criteria->compare('is_deleted',$this->is_deleted);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Cf4Medicine the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    /**
     * @return bool|CDbDataReader|mixed|string
     * @throws CException
     */
    public function getUuid()
    {
        $command = $this->dbConnection->createCommand('Select UUID()');

        return $command->queryScalar();
    }

    /**
     * @param $encounter_nr
     * @return CActiveDataProvider
     */
    public function getMedicine($encounter_nr)
    {
        $criteria = new CDbCriteria;
        $criteria->condition = 'encounter_nr = :encounter_nr AND is_deleted = 0';
        $criteria->params = array(
            ':encounter_nr' => $encounter_nr
        );

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
	}
}
