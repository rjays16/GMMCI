<?php

/**
 * This is the model class for table "seg_or_deposit".
 *
 * The followings are the available columns in table 'seg_or_deposit':
 * @property integer $id
 * @property string $refno
 * @property string $encounter_nr
 * @property string $pid
 * @property string $amount
 * @property string $status
 * @property string $proc_status
 * @property string $remarks
 * @property string $is_deleted
 * @property string $create_id
 * @property string $create_time
 * @property string $modify_id
 * @property string $modify_time
 * @property string $or_no
 *
 * The followings are the available model relations:
 * @property OrRequest $refno0
 */
class OrDeposit extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_or_deposit';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('encounter_nr, pid, status, proc_status', 'required'),
			array('refno', 'length', 'max'=>258),
			array('encounter_nr, pid, or_no', 'length', 'max'=>12),
			array('amount', 'length', 'max'=>10),
			array('status', 'length', 'max'=>7),
			array('proc_status, is_deleted', 'length', 'max'=>5),
			array('remarks', 'length', 'max'=>300),
			array('create_id, modify_id', 'length', 'max'=>30),
			array('create_time, modify_time', 'safe'),
			array('modify_time, create_time','default',
				'value'=>new CDbExpression('NOW()'),
				'setOnEmpty'=>false,'on'=>'insert'),
			array('modify_id, create_id','default',
				'value'=> $_SESSION['sess_temp_userid'],
				'setOnEmpty'=>false,'on'=>'insert'),
			array('modify_time','default',
				'value'=>new CDbExpression('NOW()'),
				'setOnEmpty'=>false,'on'=>'update'),
			array('modify_id','default',
				'value'=> $_SESSION['sess_temp_userid'],
				'setOnEmpty'=>false,'on'=>'update'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, refno, encounter_nr, pid, amount, status, proc_status, remarks, is_deleted, create_id, create_time, modify_id, modify_time, or_no', 'safe', 'on'=>'search'),
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
			'refno0' => array(self::BELONGS_TO, 'OrRequest', 'refno'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'refno' => 'Refno',
			'encounter_nr' => 'Encounter Nr',
			'pid' => 'Pid',
			'amount' => 'Amount',
			'status' => 'Status',
			'proc_status' => 'Proc Status',
			'remarks' => 'Remarks',
			'is_deleted' => 'Is Deleted',
			'create_id' => 'Create',
			'create_time' => 'Create Time',
			'modify_id' => 'Modify',
			'modify_time' => 'Modify Time',
			'or_no' => 'Or No',
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
		$criteria->compare('refno',$this->refno,true);
		$criteria->compare('encounter_nr',$this->encounter_nr,true);
		$criteria->compare('pid',$this->pid,true);
		$criteria->compare('amount',$this->amount,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('proc_status',$this->proc_status,true);
		$criteria->compare('remarks',$this->remarks,true);
		$criteria->compare('is_deleted',$this->is_deleted,true);
		$criteria->compare('create_id',$this->create_id,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('modify_id',$this->modify_id,true);
		$criteria->compare('modify_time',$this->modify_time,true);
		$criteria->compare('or_no',$this->or_no,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return OrDeposit the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
