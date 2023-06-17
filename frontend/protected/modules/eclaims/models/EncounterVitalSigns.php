<?php

/**
 * This is the model class for table "seg_encounter_vitalsigns".
 *
 * The followings are the available columns in table 'seg_encounter_vitalsigns':
 * @property string $encounter_nr
 * @property string $date
 * @property string $pid
 * @property integer $systole
 * @property integer $diastole
 * @property double $temp
 * @property double $weight
 * @property double $resp_rate
 * @property double $pulse_rate
 * @property integer $bp_unit
 * @property integer $temp_unit
 * @property integer $weight_unit
 * @property integer $rr_unit
 * @property integer $pr_unit
 * @property string $history
 * @property string $modify_id
 * @property string $modify_dt
 * @property string $create_id
 * @property string $create_dt
 * @property string $vitalsign_no
 *
 * The followings are the available model relations:
 * @property SegDialysisRequest[] $segDialysisRequests
 */
class EncounterVitalsigns extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_encounter_vitalsigns';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('encounter_nr, pid, modify_id, create_id', 'required'),
            array('systole, diastole, bp_unit, temp_unit, weight_unit, rr_unit, pr_unit', 'numerical', 'integerOnly'=>true),
            array('temp, weight, resp_rate, pulse_rate', 'numerical'),
            array('encounter_nr, pid', 'length', 'max'=>12),
            array('modify_id, create_id', 'length', 'max'=>35),
            array('date, history, modify_dt, create_dt', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('encounter_nr, date, pid, systole, diastole, temp, weight, resp_rate, pulse_rate, bp_unit, temp_unit, weight_unit, rr_unit, pr_unit, history, modify_id, modify_dt, create_id, create_dt, vitalsign_no', 'safe', 'on'=>'search'),
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
            'segDialysisRequests' => array(self::HAS_MANY, 'SegDialysisRequest', 'vitalsign_no'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'encounter_nr' => 'Encounter Nr',
            'date' => 'Date',
            'pid' => 'Pid',
            'systole' => 'Systole',
            'diastole' => 'Diastole',
            'temp' => 'Temp',
            'weight' => 'Weight',
            'resp_rate' => 'Resp Rate',
            'pulse_rate' => 'Pulse Rate',
            'bp_unit' => 'Bp Unit',
            'temp_unit' => 'Temp Unit',
            'weight_unit' => 'Weight Unit',
            'rr_unit' => 'Rr Unit',
            'pr_unit' => 'Pr Unit',
            'history' => 'History',
            'modify_id' => 'Modify',
            'modify_dt' => 'Modify Dt',
            'create_id' => 'Create',
            'create_dt' => 'Create Dt',
            'vitalsign_no' => 'Vitalsign No',
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

        $criteria->compare('encounter_nr',$this->encounter_nr,true);
        $criteria->compare('date',$this->date,true);
        $criteria->compare('pid',$this->pid,true);
        $criteria->compare('systole',$this->systole);
        $criteria->compare('diastole',$this->diastole);
        $criteria->compare('temp',$this->temp);
        $criteria->compare('weight',$this->weight);
        $criteria->compare('resp_rate',$this->resp_rate);
        $criteria->compare('pulse_rate',$this->pulse_rate);
        $criteria->compare('bp_unit',$this->bp_unit);
        $criteria->compare('temp_unit',$this->temp_unit);
        $criteria->compare('weight_unit',$this->weight_unit);
        $criteria->compare('rr_unit',$this->rr_unit);
        $criteria->compare('pr_unit',$this->pr_unit);
        $criteria->compare('history',$this->history,true);
        $criteria->compare('modify_id',$this->modify_id,true);
        $criteria->compare('modify_dt',$this->modify_dt,true);
        $criteria->compare('create_id',$this->create_id,true);
        $criteria->compare('create_dt',$this->create_dt,true);
        $criteria->compare('vitalsign_no',$this->vitalsign_no,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return EncounterVitalsigns the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
} 