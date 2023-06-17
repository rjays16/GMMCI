<?php

/**
 * This is the model class for table "seg_encounter_memcategory".
 *
 * The followings are the available columns in table 'seg_encounter_memcategory':
 * @property string $encounter_nr
 * @property string $memcategory_id
 *
 * The followings are the available model relations:
 * @property Encounter $encounterNr
 * @property Memcategory $memcategory
 */
class EncounterMemcategory extends CareActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_encounter_memcategory';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('encounter_nr, memcategory_id', 'required'),
			array('encounter_nr', 'length', 'max'=>12),
			array('memcategory_id', 'length', 'max'=>8),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('encounter_nr, memcategory_id', 'safe', 'on'=>'search'),
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
			'encounterNr' => array(self::BELONGS_TO, 'CareEncounter', 'encounter_nr'),
			'memcategory' => array(self::BELONGS_TO, 'Memcategory', 'memcategory_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'encounter_nr' => 'Encounter Nr',
			'memcategory_id' => 'Memcategory',
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

		$criteria->compare('encounter_nr',$this->encounter_nr,true);
		$criteria->compare('memcategory_id',$this->memcategory_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return EncounterMemcategory the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function getTypesToArray() {
		$_list = Memcategory::model()->findAll();
        $_types = array();

        array_map(function($data) use(&$_types) {
            $_types[$data->memcategory_code] =  $data->memcategory_desc;
        }, $_list);

        return $_types;
	}

}
