<?php

/**
 *
 */

Yii::import('billing.models.MiscellaneousOperationDetails');

/**
 * This is the model class for table "seg_misc_ops".
 *
 * The followings are the available columns in table 'seg_misc_ops':
 * @property string $refno
 * @property string $chrge_dte
 * @property string $encounter_nr
 * @property string $modify_id
 * @property string $modify_dt
 * @property string $create_id
 * @property string $create_dt
 *
 * The followings are the available model relations:
 * @property CareEncounter $encounterNr
 * @property SegMiscOpsDetails[] $segMiscOpsDetails
 */
class MiscellaneousOperation extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_misc_ops';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('refno, chrge_dte, encounter_nr, modify_id, modify_dt, create_id', 'required'),
			array('refno, encounter_nr', 'length', 'max'=>12),
			array('modify_id, create_id', 'length', 'max'=>35),
			array('create_dt', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('refno, chrge_dte, encounter_nr, modify_id, modify_dt, create_id, create_dt', 'safe', 'on'=>'search'),
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
			'encounter' => array(self::BELONGS_TO, 'Encounter', 'encounter_nr'),
			'details' => array(self::HAS_MANY, 'MiscellaneousOperationDetails', 'refno'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'refno' => 'Refno',
			'chrge_dte' => 'Chrge Dte',
			'encounter_nr' => 'Encounter Nr',
			'modify_id' => 'Modify',
			'modify_dt' => 'Modify Dt',
			'create_id' => 'Create',
			'create_dt' => 'Create Dt',
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

		$criteria->compare('refno',$this->refno,true);
		$criteria->compare('chrge_dte',$this->chrge_dte,true);
		$criteria->compare('encounter_nr',$this->encounter_nr,true);
		$criteria->compare('modify_id',$this->modify_id,true);
		$criteria->compare('modify_dt',$this->modify_dt,true);
		$criteria->compare('create_id',$this->create_id,true);
		$criteria->compare('create_dt',$this->create_dt,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return MiscellaneousOperation the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
