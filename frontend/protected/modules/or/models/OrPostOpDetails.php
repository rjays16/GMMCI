<?php

/**
 * This is the model class for table "seg_or_post_op_details".
 *
 * The followings are the available columns in table 'seg_or_post_op_details':
 * @property string $or_refno
 * @property string $operation_start
 * @property string $operation_end
 * @property string $operation_diagnosis
 * @property string $operation_perform
 * @property string $technique_id
 * @property string $technique_desc
 * @property string $medium_sponge
 * @property string $abdominal_pack
 * @property string $operating_sponge
 * @property string $cherry_balls
 * @property string $cottonoids
 * @property string $needles_nonatraumatic
 * @property string $needles_atraumatic
 * @property string $peanut_balls
 * @property string $others
 * @property string $modify_id
 * @property string $modify_date
 * @property string $create_id
 * @property string $create_date
 * @property string $history
 *
 * The followings are the available model relations:
 * @property OrAnesthesiaUse $orAnesthesiaUse
 * @property OrRequest $orRefno
 */
class OrPostOpDetails extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_or_post_op_details';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('or_refno, technique_id, modify_date', 'required'),
			array('or_refno, modify_id, create_id', 'length', 'max'=>12),
			array('technique_id', 'length', 'max'=>10),
			array('operation_start, operation_end, operation_diagnosis, operation_perform, technique_desc, medium_sponge, abdominal_pack, operating_sponge, cherry_balls, cottonoids, needles_nonatraumatic, needles_atraumatic, peanut_balls, others, create_date, history', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('or_refno, operation_start, operation_end, operation_diagnosis, operation_perform, technique_id, technique_desc, medium_sponge, abdominal_pack, operating_sponge, cherry_balls, cottonoids, needles_nonatraumatic, needles_atraumatic, peanut_balls, others, modify_id, modify_date, create_id, create_date, history', 'safe', 'on'=>'search'),
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
			'orAnesthesiaUse' => array(self::HAS_ONE, 'OrAnesthesiaUse', 'or_refno'),
			'orRefno' => array(self::BELONGS_TO, 'OrRequest', 'or_refno'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'or_refno' => 'Or Refno',
			'operation_start' => 'Operation Start',
			'operation_end' => 'Operation End',
			'operation_diagnosis' => 'Operation Diagnosis',
			'operation_perform' => 'Operation Perform',
			'technique_id' => 'Technique',
			'technique_desc' => 'Technique Desc',
			'medium_sponge' => 'Medium Sponge',
			'abdominal_pack' => 'Abdominal Pack',
			'operating_sponge' => 'Operating Sponge',
			'cherry_balls' => 'Cherry Balls',
			'cottonoids' => 'Cottonoids',
			'needles_nonatraumatic' => 'Needles Nonatraumatic',
			'needles_atraumatic' => 'Needles Atraumatic',
			'peanut_balls' => 'Peanut Balls',
			'others' => 'Others',
			'modify_id' => 'Modify',
			'modify_date' => 'Modify Date',
			'create_id' => 'Create',
			'create_date' => 'Create Date',
			'history' => 'History',
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

		$criteria->compare('or_refno',$this->or_refno,true);
		$criteria->compare('operation_start',$this->operation_start,true);
		$criteria->compare('operation_end',$this->operation_end,true);
		$criteria->compare('operation_diagnosis',$this->operation_diagnosis,true);
		$criteria->compare('operation_perform',$this->operation_perform,true);
		$criteria->compare('technique_id',$this->technique_id,true);
		$criteria->compare('technique_desc',$this->technique_desc,true);
		$criteria->compare('medium_sponge',$this->medium_sponge,true);
		$criteria->compare('abdominal_pack',$this->abdominal_pack,true);
		$criteria->compare('operating_sponge',$this->operating_sponge,true);
		$criteria->compare('cherry_balls',$this->cherry_balls,true);
		$criteria->compare('cottonoids',$this->cottonoids,true);
		$criteria->compare('needles_nonatraumatic',$this->needles_nonatraumatic,true);
		$criteria->compare('needles_atraumatic',$this->needles_atraumatic,true);
		$criteria->compare('peanut_balls',$this->peanut_balls,true);
		$criteria->compare('others',$this->others,true);
		$criteria->compare('modify_id',$this->modify_id,true);
		$criteria->compare('modify_date',$this->modify_date,true);
		$criteria->compare('create_id',$this->create_id,true);
		$criteria->compare('create_date',$this->create_date,true);
		$criteria->compare('history',$this->history,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return OrPostOpDetails the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
