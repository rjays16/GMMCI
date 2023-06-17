<?php
Yii::import('phic.models.CaseRatePackage');
/**
 * This is the model class for table "care_encounter_diagnosis".
 *
 * The followings are the available columns in table 'care_encounter_diagnosis':
 * @property integer $diagnosis_nr
 * @property string $encounter_nr
 * @property integer $encounter_type
 * @property integer $type_nr
 * @property string $op_nr
 * @property string $date
 * @property string $code
 * @property string $code_parent
 * @property integer $group_nr
 * @property integer $code_version
 * @property string $localcode
 * @property integer $category_nr
 * @property string $type
 * @property string $localization
 * @property string $diagnosing_clinician
 * @property integer $diagnosing_dept_nr
 * @property string $status
 * @property string $history
 * @property string $modify_id
 * @property string $modify_time
 * @property string $create_id
 * @property string $create_time
 * @property string $referral_nr
 * @property string $diagnosis_description
 *
 * The followings are the available model relations:
 * @property Encounter $encounterNr
 * @property Icd10 $icd10
 */
class CareEncounterDiagnosis extends CareActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'care_encounter_diagnosis';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('encounter_nr, encounter_type, type_nr, history, modify_time', 'required'),
            array('encounter_type, type_nr, group_nr, code_version, category_nr, diagnosing_dept_nr', 'numerical', 'integerOnly'=>true),
            array('encounter_nr, referral_nr', 'length', 'max'=>12),
            array('op_nr', 'length', 'max'=>10),
            array('code, code_parent, status', 'length', 'max'=>25),
            array('localcode, type, localization, modify_id, create_id', 'length', 'max'=>35),
            array('diagnosing_clinician', 'length', 'max'=>60),
            array('date, create_time, diagnosis_description', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('diagnosis_nr, encounter_nr, encounter_type, type_nr, op_nr, date, code, code_parent, group_nr, code_version, localcode, category_nr, type, localization, diagnosing_clinician, diagnosing_dept_nr, status, history, modify_id, modify_time, create_id, create_time, referral_nr, diagnosis_description', 'safe', 'on'=>'search'),
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
            'encounter_type' => 'Encounter Type',
            'type_nr' => 'Type Nr',
            'op_nr' => 'Op Nr',
            'date' => 'Date',
            'code' => 'Code',
            'code_parent' => 'Code Parent',
            'group_nr' => 'Group Nr',
            'code_version' => 'Code Version',
            'localcode' => 'Localcode',
            'category_nr' => 'Category Nr',
            'type' => 'Type',
            'localization' => 'Localization',
            'diagnosing_clinician' => 'Diagnosing Clinician',
            'diagnosing_dept_nr' => 'Diagnosing Dept Nr',
            'status' => 'Status',
            'history' => 'History',
            'modify_id' => 'Modify',
            'modify_time' => 'Modify Time',
            'create_id' => 'Create',
            'create_time' => 'Create Time',
            'referral_nr' => 'Referral Nr',
            'diagnosis_description' => 'Diagnosis Description',
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
        $criteria->compare('encounter_type',$this->encounter_type);
        $criteria->compare('type_nr',$this->type_nr);
        $criteria->compare('op_nr',$this->op_nr,true);
        $criteria->compare('date',$this->date,true);
        $criteria->compare('code',$this->code,true);
        $criteria->compare('code_parent',$this->code_parent,true);
        $criteria->compare('group_nr',$this->group_nr);
        $criteria->compare('code_version',$this->code_version);
        $criteria->compare('localcode',$this->localcode,true);
        $criteria->compare('category_nr',$this->category_nr);
        $criteria->compare('type',$this->type,true);
        $criteria->compare('localization',$this->localization,true);
        $criteria->compare('diagnosing_clinician',$this->diagnosing_clinician,true);
        $criteria->compare('diagnosing_dept_nr',$this->diagnosing_dept_nr);
        $criteria->compare('status',$this->status,true);
        $criteria->compare('history',$this->history,true);
        $criteria->compare('modify_id',$this->modify_id,true);
        $criteria->compare('modify_time',$this->modify_time,true);
        $criteria->compare('create_id',$this->create_id,true);
        $criteria->compare('create_time',$this->create_time,true);
        $criteria->compare('referral_nr',$this->referral_nr,true);
        $criteria->compare('diagnosis_description',$this->diagnosis_description,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CareEncounterDiagnosis the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}