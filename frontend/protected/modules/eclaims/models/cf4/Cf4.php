<?php
/**
 * Cf4.php
 *
 * @author Jan Chris S. Ogel <iamjc93@gmail.com>
 * @copyright (c) 2019, Segworks Technologies Corporation (http://www.segworks.com)
 */


/**
 * This is the model class for table "seg_cf4_h".
 *
 * The followings are the available columns in table 'seg_cf4_h':
 * @property string $id
 * @property string $entry_date
 * @property string $pid
 * @property string $encounter_nr
 * @property string $modify_time
 * @property integer $is_deleted
 * @property string $old_pid
 * @property integer $is_uploaded
 * @property string $encoder
 * @property string $created_at
 * @property string $modified_at
 *
 * The followings are the available model relations:
 * @property Cf4Abdomen[] $Cf4Abdomens
 * @property Cf4Chest[] $Cf4Chests
 * @property Cf4CourseInTheWard[] $Cf4CourseInTheWards
 * @property Cf4GeneralSurvey[] $Cf4GeneralSurveys
 * @property Cf4Guie[] $Cf4Guies
 * @property Person $p
 * @property Encounter $encounterNo
 * @property Cf4Heart[] $Cf4Hearts
 * @property Cf4Heent[] $Cf4Heents
 * @property Cf4Medicine[] $Cf4Medicines
 * @property Cf4MenstrualHistory[] $Cf4MenstrualHistories
 * @property Cf4Neuro[] $Cf4Neuros
 * @property Cf4ObstetricHistory[] $Cf4ObstetricHistories
 * @property Cf4PastMedHistory[] $Cf4PastMedHistories
 * @property Cf4PertinentSignSymptoms[] $Cf4PertinentSignSymptoms
 * @property Cf4Rectal[] $Cf4Rectals
 * @property Cf4Skin[] $Cf4Skins
 * @property Cf4VitalSigns[] $Cf4VitalSigns
 */
class Cf4 extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_cf4_h';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            // array('modify_time', 'required'),
            array('is_deleted, is_uploaded', 'numerical', 'integerOnly' => true),
            array('id', 'length', 'max' => 36),
            array('pid, old_pid', 'length', 'max' => 12),
            array('encounter_nr', 'length', 'max' => 15),
            array('encoder', 'length', 'max' => 100),
            array('id, entry_date, pid, encounter_nr, modify_time, is_deleted, old_pid, is_uploaded, encoder, created_at, modified_at', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, entry_date, pid, encounter_nr, modify_time, is_deleted, old_pid, is_uploaded, encoder, created_at, modified_at', 'safe', 'on' => 'search'),
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
            'Cf4Abdomens' => array(self::HAS_MANY, 'Cf4Abdomen', 'id'),
            'Cf4Chests' => array(self::HAS_MANY, 'Cf4Chest', 'id'),
            'Cf4CourseInTheWards' => array(self::HAS_MANY, 'Cf4CourseInTheWard', 'id'),
            'Cf4GeneralSurveys' => array(self::HAS_MANY, 'Cf4GeneralSurvey', 'id'),
            'Cf4Guies' => array(self::HAS_MANY, 'Cf4Guie', 'id'),
            'p' => array(self::BELONGS_TO, 'Person', 'pid'),
            'encounterNo' => array(self::BELONGS_TO, 'Encounter', 'encounter_nr'),
            'Cf4Hearts' => array(self::HAS_MANY, 'Cf4Heart', 'id'),
            'Cf4Heents' => array(self::HAS_MANY, 'Cf4Heent', 'id'),
            'Cf4Medicines' => array(self::HAS_MANY, 'Cf4Medicine', 'id'),
            'Cf4MenstrualHistories' => array(self::HAS_MANY, 'Cf4MenstrualHistory', 'id'),
            'Cf4Neuros' => array(self::HAS_MANY, 'Cf4Neuro', 'id'),
            'Cf4ObstetricHistories' => array(self::HAS_MANY, 'Cf4ObstetricHistory', 'id'),
            'Cf4PastMedHistories' => array(self::HAS_MANY, 'Cf4PastMedHistory', 'id'),
            'Cf4PertinentSignSymptoms' => array(self::HAS_MANY, 'Cf4PertinentSignSymptoms', 'id'),
            'Cf4Rectals' => array(self::HAS_MANY, 'Cf4Rectal', 'id'),
            'Cf4Skins' => array(self::HAS_MANY, 'Cf4Skin', 'id'),
            'Cf4VitalSigns' => array(self::HAS_MANY, 'Cf4VitalSigns', 'id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            // 'id' => 'Entry',
            'entry_date' => 'Entry Date',
            'pid' => 'Pid',
            'encounter_nr' => 'Encounter Nr',
            'modify_time' => 'Modify Time',
            'is_deleted' => 'Is Deleted',
            'old_pid' => 'Old Pid',
            'is_uploaded' => 'Is Uploaded',
            'encoder' => 'Encoder',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
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

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('entry_date', $this->entry_date, true);
        $criteria->compare('pid', $this->pid, true);
        $criteria->compare('encounter_nr', $this->encounter_nr, true);
        $criteria->compare('modify_time', $this->modify_time, true);
        $criteria->compare('is_deleted', $this->is_deleted);
        $criteria->compare('old_pid', $this->old_pid, true);
        $criteria->compare('is_uploaded', $this->is_uploaded);
        $criteria->compare('encoder', $this->encoder, true);
        $criteria->compare('created_at', $this->created_at, true);
        $criteria->compare('modified_at', $this->modified_at, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Cf4 the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getUuid()
    {
        $command = $this->dbConnection->createCommand('Select UUID()');

        return $command->queryScalar();
    }

    public function checkCf4HeaderDetails($encounter_nr)
    {
        $cf4Header = Cf4::model()->findByAttributes(
            array(
                'encounter_nr' => $encounter_nr
            )
        );

        return $cf4Header;
    }
}
