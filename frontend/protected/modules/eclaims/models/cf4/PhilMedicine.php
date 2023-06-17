<?php
/**
 * PhilMedicine.php
 *
 * @author Jan Chris S. Ogel <iamjc93@gmail.com>
 * @copyright (c) 2019, Segworks Technologies Corporation (http://www.segworks.com)
 */

/**
 * This is the model class for table "seg_phil_medicine".
 *
 * The followings are the available columns in table 'seg_phil_medicine':
 * @property string $drug_code
 * @property string $description
 * @property string $gen_code
 * @property string $salt_code
 * @property string $form_code
 * @property string $strength_code
 * @property string $unit_code
 * @property string $package_code
 * @property string $date_added
 *
 * The followings are the available model relations:
 * @property PhilMedicineGeneric $genCode
 * @property PhilMedicineSalt $saltCode
 * @property PhilMedicineForm $formCode
 * @property PhilMedicineStrength $strengthCode
 * @property PhilMedicineUnit $unitCode
 * @property PhilMedicinePackage $packageCode
 */
class PhilMedicine extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_phil_medicine';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('drug_code', 'required'),
			array('drug_code', 'length', 'max'=>30),
			array('gen_code', 'length', 'max'=>20),
			array('salt_code, form_code, strength_code, unit_code, package_code', 'length', 'max'=>10),
			array('description, date_added', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('drug_code, description, gen_code, salt_code, form_code, strength_code, unit_code, package_code, date_added', 'safe', 'on'=>'search'),
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
			'genCode' => array(self::BELONGS_TO, 'PhilMedicineGeneric', 'gen_code'),
			'saltCode' => array(self::BELONGS_TO, 'PhilMedicineSalt', 'salt_code'),
			'formCode' => array(self::BELONGS_TO, 'PhilMedicineForm', 'form_code'),
			'strengthCode' => array(self::BELONGS_TO, 'PhilMedicineStrength', 'strength_code'),
			'unitCode' => array(self::BELONGS_TO, 'PhilMedicineUnit', 'unit_code'),
			'packageCode' => array(self::BELONGS_TO, 'PhilMedicinePackage', 'package_code'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'drug_code' => 'Drug Code',
			'description' => 'Description',
			'gen_code' => 'Gen Code',
			'salt_code' => 'Salt Code',
			'form_code' => 'Form Code',
			'strength_code' => 'Strength Code',
			'unit_code' => 'Unit Code',
			'package_code' => 'Package Code',
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

		$criteria->compare('drug_code',$this->drug_code,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('gen_code',$this->gen_code,true);
		$criteria->compare('salt_code',$this->salt_code,true);
		$criteria->compare('form_code',$this->form_code,true);
		$criteria->compare('strength_code',$this->strength_code,true);
		$criteria->compare('unit_code',$this->unit_code,true);
		$criteria->compare('package_code',$this->package_code,true);
		$criteria->compare('date_added',$this->date_added,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PhilMedicine the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
