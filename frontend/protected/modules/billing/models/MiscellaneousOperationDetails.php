<?php
/**
 *
 */

Yii::import('billing.models.MiscellaneousOperation');
Yii::import('billing.models.OperationRvs');

/**
 * This is the model class for table "seg_misc_ops_details".
 *
 * The followings are the available columns in table 'seg_misc_ops_details':
 * @property string $refno
 * @property string $ops_code
 * @property integer $entry_no
 * @property string $op_date
 * @property string $rvu
 * @property double $multiplier
 * @property double $chrg_amnt
 * @property string $group_code
 * @property string $laterality
 * @property integer $num_sessions
 * @property string $special_dates
 * @property string $description
 *
 * The followings are the available model relations:
 * @property SegMiscOps $refno0
 * @property SegOpsRvs $opsCode
 */
class MiscellaneousOperationDetails extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_misc_ops_details';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('refno, ops_code, entry_no, op_date, rvu, multiplier, chrg_amnt, group_code, laterality', 'required'),
			array('entry_no, num_sessions', 'numerical', 'integerOnly'=>true),
			array('multiplier, chrg_amnt', 'numerical'),
			array('refno, ops_code', 'length', 'max'=>12),
			array('rvu', 'length', 'max'=>10),
			array('group_code', 'length', 'max'=>4),
			array('laterality', 'length', 'max'=>1),
			array('special_dates, description', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('refno, ops_code, entry_no, op_date, rvu, multiplier, chrg_amnt, group_code, laterality, num_sessions, special_dates, description', 'safe', 'on'=>'search'),
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
            'operation' => array(self::BELONGS_TO, 'MiscellaneousOperation', 'refno'),
			'rvs' => array(self::BELONGS_TO, 'OperationRvs', 'ops_code'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'refno' => 'Refno',
			'ops_code' => 'Ops Code',
			'entry_no' => 'Entry No',
			'op_date' => 'Op Date',
			'rvu' => 'Rvu',
			'multiplier' => 'Multiplier',
			'chrg_amnt' => 'Chrg Amnt',
			'group_code' => 'Group Code',
			'laterality' => 'Laterality',
			'num_sessions' => 'Num Sessions',
			'special_dates' => 'Special Dates',
			'description' => 'Description',
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
		$criteria->compare('ops_code',$this->ops_code,true);
		$criteria->compare('entry_no',$this->entry_no);
		$criteria->compare('op_date',$this->op_date,true);
		$criteria->compare('rvu',$this->rvu,true);
		$criteria->compare('multiplier',$this->multiplier);
		$criteria->compare('chrg_amnt',$this->chrg_amnt);
		$criteria->compare('group_code',$this->group_code,true);
		$criteria->compare('laterality',$this->laterality,true);
		$criteria->compare('num_sessions',$this->num_sessions);
		$criteria->compare('special_dates',$this->special_dates,true);
		$criteria->compare('description',$this->description,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return MiscellaneousOperationDetails the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
