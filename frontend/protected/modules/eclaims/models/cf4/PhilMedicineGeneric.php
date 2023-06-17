<?php
/**
 * PhilMedicineGeneric.php
 *
 * @author Jan Chris S. Ogel <iamjc93@gmail.com>
 * @copyright (c) 2019, Segworks Technologies Corporation (http://www.segworks.com)
 */

/**
 * This is the model class for table "seg_phil_medicine_generic".
 *
 * The followings are the available columns in table 'seg_phil_medicine_generic':
 * @property string $gen_code
 * @property string $gen_desc
 * @property string $date_added
 *
 * The followings are the available model relations:
 * @property PhilMedicine[] $PhilMedicines
 */
class PhilMedicineGeneric extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_phil_medicine_generic';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('gen_code', 'length', 'max'=>20),
			array('gen_desc, date_added', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('gen_code, gen_desc, date_added', 'safe', 'on'=>'search'),
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
			'PhilMedicines' => array(self::HAS_MANY, 'PhilMedicine', 'gen_code'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'gen_code' => 'Gen Code',
			'gen_desc' => 'Gen Desc',
			'date_added' => 'Date Added',
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

		$criteria->compare('gen_code',$this->gen_code,true);
		$criteria->compare('gen_desc',$this->gen_desc,true);
		$criteria->compare('date_added',$this->date_added,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PhilMedicineGeneric the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
