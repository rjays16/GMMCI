<?php
/**
 * Cf4PatientInfo.php
 *
 * @author Jan Chris S. Ogel <iamjc93@gmail.com>
 * @copyright (c) 2019, Segworks Technologies Corporation (http://www.segworks.com)
 */

/**
 * This is the model class for table "seg_cf4_patient_info".
 *
 * The followings are the available columns in table 'seg_cf4_patient_info':
 * @property string $id
 * @property string $entry_id
 * @property string $encounter_nr
 * @property string $present_illness
 * @property string $past_med_history
 * @property string $disease_code
 * @property string $bp
 * @property string $cr
 * @property string $rr
 * @property string $temperature
 * @property integer $is_deleted
 * @property string $created_at
 * @property string $modified_at
 *
 * The followings are the available model relations:
 * @property Cf4 $entry
 * @property Encounter $encounterNr
 */
class Cf4PatientInfo extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_cf4_patient_info';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('id', 'required'),
            array('is_deleted', 'numerical', 'integerOnly' => true),
            array('id, entry_id', 'length', 'max' => 36),
            array('encounter_nr', 'length', 'max' => 15),
            array('bp, cr, rr, temperature', 'length', 'max' => 10),
            array('present_illness, past_med_history, disease_code, created_at, modified_at', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, entry_id, encounter_nr, present_illness, past_med_history, disease_code, bp, cr, rr, temperature, is_deleted, created_at, modified_at', 'safe', 'on' => 'search'),
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
            'entry' => array(self::BELONGS_TO, 'Cf4', 'entry_id'),
            'encounterNr' => array(self::BELONGS_TO, 'Encounter', 'encounter_nr'),
        );
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
            'present_illness' => 'Brief History of Present Illness/OB History',
            'past_med_history' => 'Pertinent Past Medical History',
            'disease_code' => 'Disease Code of Past Medical History',
            'bp' => 'Blood Pressure',
            'cr' => 'Cardiac Rate',
            'rr' => 'Respiratory Rate',
            'temperature' => 'Temperature',
            'is_deleted' => 'Is Deleted',
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
        $criteria->compare('entry_id', $this->entry_id, true);
        $criteria->compare('encounter_nr', $this->encounter_nr, true);
        $criteria->compare('present_illness', $this->present_illness, true);
        $criteria->compare('past_med_history', $this->past_med_history, true);
        $criteria->compare('disease_code', $this->disease_code, true);
        $criteria->compare('bp', $this->bp, true);
        $criteria->compare('cr', $this->cr, true);
        $criteria->compare('rr', $this->rr, true);
        $criteria->compare('temperature', $this->temperature, true);
        $criteria->compare('is_deleted', $this->is_deleted);
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
     * @return Cf4PatientInfo the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
