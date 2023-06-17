<?php

Yii::import('phic.models.CaseRatePackage');

/**
 * This is the model class for table "seg_encounter_diagnosis".
 *
 * The followings are the available columns in table 'seg_encounter_diagnosis':
 * @property integer $diagnosis_nr
 * @property string $encounter_nr
 * @property integer $entry_no
 * @property string $code
 * @property string $description
 * @property integer $is_deleted
 * @property string $modify_id
 * @property string $modify_time
 * @property string $create_id
 * @property string $create_time
 * @property string $code_alt
 * @property integer $type_nr
 */
class EncounterDiagnosis extends CareActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_encounter_diagnosis';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('encounter_nr, entry_no, code, description, modify_id, modify_time, create_id', 'required'),
            array('entry_no, is_deleted, type_nr', 'numerical', 'integerOnly'=>true),
            array('encounter_nr', 'length', 'max'=>12),
            array('code, code_alt', 'length', 'max'=>15),
            array('modify_id, create_id', 'length', 'max'=>35),
            array('create_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('diagnosis_nr, encounter_nr, entry_no, code, description, is_deleted, modify_id, modify_time, create_id, create_time, code_alt, type_nr', 'safe', 'on'=>'search'),
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
            'icd10' => array(self::BELONGS_TO, 'Icd10', 'code'),
            'package' => array(self::BELONGS_TO, 'CaseRatePackage', array('code' => 'code')),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'diagnosis_nr' => 'Diagnosis Nr',
            'encounter_nr' => 'Encounter Nr',
            'entry_no' => 'Entry No',
            'code' => 'Code',
            'description' => 'Description',
            'is_deleted' => 'Is Deleted',
            'modify_id' => 'Modify',
            'modify_time' => 'Modify Time',
            'create_id' => 'Create',
            'create_time' => 'Create Time',
            'code_alt' => 'Code Alt',
            'type_nr' => 'Type Nr',
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

        $criteria->compare('diagnosis_nr',$this->diagnosis_nr);
        $criteria->compare('encounter_nr',$this->encounter_nr,true);
        $criteria->compare('entry_no',$this->entry_no);
        $criteria->compare('code',$this->code,true);
        $criteria->compare('description',$this->description,true);
        $criteria->compare('is_deleted',$this->is_deleted);
        $criteria->compare('modify_id',$this->modify_id,true);
        $criteria->compare('modify_time',$this->modify_time,true);
        $criteria->compare('create_id',$this->create_id,true);
        $criteria->compare('create_time',$this->create_time,true);
        $criteria->compare('code_alt',$this->code_alt,true);
        $criteria->compare('type_nr',$this->type_nr);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return EncounterDiagnosis the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}
