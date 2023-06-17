<?php
/**
 * Cf4VitalSigns.php
 *
 * @author Jan Chris S. Ogel <iamjc93@gmail.com>
 * @copyright (c) 2019, Segworks Technologies Corporation (http://www.segworks.com)
 */

/**
 * This is the model class for table "seg_cf4_vital_signs".
 *
 * The followings are the available columns in table 'seg_cf4_vital_signs':
 * @property string $id
 * @property string $entry_id
 * @property string $encounter_nr
 * @property string $systolic
 * @property string $diastolic
 * @property string $cr
 * @property string $rr
 * @property string $temperature
 * @property string $height
 * @property string $weight
 * @property string $created_at
 * @property string $modify
 * @property string $modified_by
 * @property string $deleted_by
 * @property string $deleted_at
 * @property integer $is_deleted
 *
 * The followings are the available model relations:
 * @property Cf4 $entry
 * @property Encounter $encounterNo
 */
class Cf4VitalSigns extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_cf4_vital_signs';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
        return array(
            array('systolic, diastolic, cr, rr, temperature', 'required'),
            array('is_deleted', 'numerical', 'integerOnly'=>true),
            array('id, entry_id', 'length', 'max'=>36),
            array('encounter_nr', 'length', 'max'=>12),
            array('systolic, diastolic, cr, rr, temperature, height, weight', 'length', 'max'=>10),
            array('modified_by, deleted_by', 'length', 'max'=>50),
            array('modify, deleted_at', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, entry_id, encounter_nr, systolic, diastolic, cr, rr, temperature, height, weight, created_at, modify, modified_by, deleted_by, deleted_at, is_deleted', 'safe', 'on'=>'search'),
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
		// );
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
            'id' => 'ID',
            'entry_id' => 'Entry',
            'encounter_nr' => 'Encounter Nr',
            'systolic' => 'Blood Pressure',
            'diastolic' => '/',
            'cr' => 'Heart Rate',
            'rr' => 'Respiratory Rate',
            'temperature' => 'Temperature',
            'height' => 'Height',
            'weight' => 'Weight',
            'created_at' => 'Created At',
            'modify' => 'Modify',
            'modified_by' => 'Modified By',
            'deleted_by' => 'Deleted By',
            'deleted_at' => 'Deleted At',
            'is_deleted' => 'Is Deleted',
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

        $criteria->compare('id',$this->id,true);
        $criteria->compare('entry_id',$this->entry_id,true);
        $criteria->compare('encounter_nr',$this->encounter_nr,true);
        $criteria->compare('systolic',$this->systolic,true);
        $criteria->compare('diastolic',$this->diastolic,true);
        $criteria->compare('cr',$this->cr,true);
        $criteria->compare('rr',$this->rr,true);
        $criteria->compare('temperature',$this->temperature,true);
        $criteria->compare('height',$this->height,true);
        $criteria->compare('weight',$this->weight,true);
        $criteria->compare('created_at',$this->created_at,true);
        $criteria->compare('modify',$this->modify,true);
        $criteria->compare('modified_by',$this->modified_by,true);
        $criteria->compare('deleted_by',$this->deleted_by,true);
        $criteria->compare('deleted_at',$this->deleted_at,true);
        $criteria->compare('is_deleted',$this->is_deleted);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        )); 
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Cf4VitalSigns the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function getUuid()
    {
        $command = $this->dbConnection->createCommand('Select UUID()');

        return $command->queryScalar();
    }

    public function getVitalSigns($encounter_nr)
    {
        $criteria = new CDbCriteria;
        $criteria->condition = 'encounter_nr = :encounter_nr AND is_deleted = 0';
        $criteria->params = array(
            ':encounter_nr' => $encounter_nr
        );

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
}
