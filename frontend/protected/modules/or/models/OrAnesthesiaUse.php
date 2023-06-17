<?php

/**
 * This is the model class for table "seg_or_anesthesia_use".
 *
 * The followings are the available columns in table 'seg_or_anesthesia_use':
 * @property integer $id
 * @property string $or_refno
 * @property string $anesth_id
 * @property string $time_end
 * @property string $time_begun
 *
 * The followings are the available model relations:
 * @property OrPostOpDetails $orRefno
 * @property OrAnesthesia $anesth
 */
class OrAnesthesiaUse extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_or_anesthesia_use';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('or_refno, anesth_id, time_end, time_begun', 'required'),
			array('or_refno, anesth_id', 'length', 'max'=>12),
			array('time_end, time_begun', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, or_refno, anesth_id, time_end, time_begun', 'safe', 'on'=>'search'),
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
			'orRefno' => array(self::BELONGS_TO, 'OrPostOpDetails', 'or_refno'),
			'anesth' => array(self::BELONGS_TO, 'OrAnesthesia', 'anesth_id'),
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
			'anesth_id' => 'Anesth',
			'time_end' => 'Time End',
			'time_begun' => 'Time Begun',
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
		$criteria->compare('anesth_id',$this->anesth_id,true);
		$criteria->compare('time_end',$this->time_end,true);
		$criteria->compare('time_begun',$this->time_begun,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return OrAnesthesiaUse the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
