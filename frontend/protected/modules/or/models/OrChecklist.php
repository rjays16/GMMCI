<?php

/**
 * This is the model class for table "seg_or_checklist".
 *
 * The followings are the available columns in table 'seg_or_checklist':
 * @property string $checklist_id
 * @property string $checklist_question
 * @property integer $has_detail
 * @property string $label_data
 * @property integer $is_deleted
 * @property string $history
 * @property string $date_created
 * @property string $created_id
 * @property string $date_modified
 * @property string $modified_id
 * @property string $type
 *
 * The followings are the available model relations:
 * @property OrRequest[] $segOrRequests
 */
class OrChecklist extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_or_checklist';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('date_created', 'required'),
			array('has_detail, is_deleted', 'numerical', 'integerOnly'=>true),
			array('label_data', 'length', 'max'=>50),
			array('created_id, modified_id', 'length', 'max'=>35),
			array('type', 'length', 'max'=>7),
			array('checklist_question, history, date_modified', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('checklist_id, checklist_question, has_detail, label_data, is_deleted, history, date_created, created_id, date_modified, modified_id, type', 'safe', 'on'=>'search'),
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
			'segOrRequests' => array(self::MANY_MANY, 'OrRequest', 'seg_or_checklist_request_data(checklist_id, refno)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'checklist_id' => 'Checklist',
			'checklist_question' => 'Checklist Question',
			'has_detail' => 'Has Detail',
			'label_data' => 'Label Data',
			'is_deleted' => 'Is Deleted',
			'history' => 'History',
			'date_created' => 'Date Created',
			'created_id' => 'Created',
			'date_modified' => 'Date Modified',
			'modified_id' => 'Modified',
			'type' => 'Type',
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

		$criteria->compare('checklist_id',$this->checklist_id,true);
		$criteria->compare('checklist_question',$this->checklist_question,true);
		$criteria->compare('has_detail',$this->has_detail);
		$criteria->compare('label_data',$this->label_data,true);
		$criteria->compare('is_deleted',$this->is_deleted);
		$criteria->compare('history',$this->history,true);
		$criteria->compare('date_created',$this->date_created,true);
		$criteria->compare('created_id',$this->created_id,true);
		$criteria->compare('date_modified',$this->date_modified,true);
		$criteria->compare('modified_id',$this->modified_id,true);
		$criteria->compare('type',$this->type,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return OrChecklist the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
