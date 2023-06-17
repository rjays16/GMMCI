<?php

class ReferredPartnerPhysician extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_cf4_referred_partner_physician';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('entry_id, encounter_nr, is_done', 'required'),

			array('id, entry_id', 'length', 'max' => 36),
			array('encounter_nr', 'length', 'max' => 15),
			array('is_done, remarks', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			// array('id, entry_id, encounter_nr, chest_id, remarks, created_at, modify, modified_by, deleted_by, is_deleted', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		// return array(
		// 	'entry' => array(self::BELONGS_TO, 'Cf4', 'entry_id'),
		// 	'encounterNo' => array(self::BELONGS_TO, 'Encounter', 'encounter_nr'),
		// 	'chest0' => array(self::BELONGS_TO, 'Cf4LibChest', 'chest_id'),
		// );
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'is_done' => 'Done',
			'remarks' => 'Remarks',

			// 'is_low_risk' => 'Ascertain the present Pregnancy is Low-Risk',

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

		// $criteria = new CDbCriteria;

		// $criteria->compare('id', $this->id, true);
		// $criteria->compare('entry_id', $this->entry_id, true);
		// $criteria->compare('encounter_nr', $this->encounter_nr, true);
		// $criteria->compare('chest_id', $this->chest_id);
		// $criteria->compare('remarks', $this->remarks, true);
		// $criteria->compare('created_at', $this->created_at, true);
		// $criteria->compare('modify', $this->modify, true);
		// $criteria->compare('modified_by', $this->modified_by, true);
		// $criteria->compare('deleted_by', $this->deleted_by, true);
		// $criteria->compare('is_deleted', $this->is_deleted);

		// return new CActiveDataProvider($this, array(
		// 	'criteria' => $criteria,
		// ));
	}
	public function getUuid()
	{
		$command = $this->dbConnection->createCommand('Select UUID()');

		return $command->queryScalar();
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Cf4Chest the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}
}
