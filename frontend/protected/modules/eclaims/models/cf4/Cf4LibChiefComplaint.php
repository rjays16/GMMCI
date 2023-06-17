<?php
/**
 * Cf4LibChiefComplaint.php
 *
 * @author Jan Chris S. Ogel <iamjc93@gmail.com>
 * @copyright (c) 2019, Segworks Technologies Corporation (http://www.segworks.com)
 */

/**
 * This is the model class for table "seg_cf4_lib_chief_complaint".
 *
 * The followings are the available columns in table 'seg_cf4_lib_chief_complaint':
 * @property integer $id
 * @property string $name
 * @property integer $is_active
 * @property integer $ordering
 *
 * The followings are the available model relations:
 * @property Cf4PertinentSignSymptoms[] $Cf4PertinentSignSymptoms
 */
class Cf4LibChiefComplaint extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_cf4_lib_chief_complaint';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id', 'required'),
			array('id, is_active, ordering', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>200),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, is_active, ordering', 'safe', 'on'=>'search'),
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
			'Cf4PertinentSignSymptoms' => array(self::HAS_MANY, 'Cf4PertinentSignSymptoms', 'sign_symptoms'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'is_active' => 'Is Active',
			'ordering' => 'Ordering',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('is_active',$this->is_active);
		$criteria->compare('ordering',$this->ordering);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Cf4LibChiefComplaint the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function scopes() {
        return array(
            'byOrdering' => array('order' => 'ordering ASC'),
        );
    }

	public function getDefaultDataRowOne()
	{

		 $entries = array();
		 $ctr = 0;
		 foreach ($this->byOrdering()->findAll() as $key => $entry) {
		 	if($entry->is_active == '1') {
		 		if($ctr == '10'){
		 			break;
		 		}else{
				  $entries[$entry->id] = $entry->name;
		 		}
			 	
		 	}
		 	$ctr++;
		 }

		 return $entries;

	}

	public function getDefaultDataRowTwo()
	{

		 $entries = array();
		 $ctr = 0;
		 foreach ($this->byOrdering()->findAll() as $key => $entry) {
		 	if($entry->is_active == '1') {
		 		if($ctr == '20'){
		 			break;
		 		}
		 		if($ctr >= '10'){
				  $entries[$entry->id] = $entry->name;
		 		}
			 	
		 	}
		 	$ctr++;
		 }

		 return $entries;

	}

	public function getDefaultDataRowThree()
	{

		 $entries = array();
		 $ctr = 0;
		 foreach ($this->byOrdering()->findAll() as $key => $entry) {
		 	if($entry->is_active == '1') {
		 		if($ctr == '30'){
		 			break;
		 		}
		 		if($ctr >= '20'){
				  $entries[$entry->id] = $entry->name;
		 		}
			 	
		 	}
		 	$ctr++;
		 }

		 return $entries;

	}

	public function getDefaultDataRowFour()
	{

		 $entries = array();
		 $ctr = 0;
		 foreach ($this->byOrdering()->findAll() as $key => $entry) {
		 	if($entry->is_active == '1') {
		 		if($ctr == '40'){
		 			break;
		 		}
		 		if($ctr >= '30'){
				  $entries[$entry->id] = $entry->name;
		 		}
			 	
		 	}
		 	$ctr++;
		 }

		 return $entries;

	}

}
