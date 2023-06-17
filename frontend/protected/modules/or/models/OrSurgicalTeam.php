<?php

/**
 * This is the model class for table "seg_or_surgical_team".
 *
 * The followings are the available columns in table 'seg_or_surgical_team':
 * @property integer $id
 * @property string $or_refno
 * @property integer $personell_nr
 * @property string $role_type
 *
 * The followings are the available model relations:
 * @property CarePersonell $personellNr
 * @property OrPreOpDetails $orRefno
 */
class OrSurgicalTeam extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_or_surgical_team';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('personell_nr', 'numerical', 'integerOnly'=>true),
			array('or_refno', 'length', 'max'=>12),
			array('role_type', 'length', 'max'=>2),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, or_refno, personell_nr, role_type', 'safe', 'on'=>'search'),
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
			'personnel' => array(self::BELONGS_TO, 'Personnel', 'personell_nr'),
			'orRefno' => array(self::BELONGS_TO, 'OrPreOpDetails', 'or_refno'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'or_refno' => 'Or Refno',
			'personell_nr' => 'Personell Nr',
			'role_type' => 'Role Type',
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
		$criteria->compare('or_refno',$this->or_refno,true);
		$criteria->compare('personell_nr',$this->personell_nr);
		$criteria->compare('role_type',$this->role_type,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return OrSurgicalTeam the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
