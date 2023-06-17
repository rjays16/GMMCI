<?php

/**
 * This is the model class for table "care_person_insurance".
 *
 * The followings are the available columns in table 'care_person_insurance':
 * @property string $pid
 * @property string $hcare_id
 * @property string $insurance_nr
 * @property integer $priority
 * @property integer $is_principal
 * @property integer $is_pbef
 * @property integer $class_nr
 * @property integer $is_void
 * @property string $modify_id
 * @property string $modify_time
 * @property string $create_id
 * @property string $create_time
 * @property string $start_coverage_date
 * @property string $end_coverage_date
 */

namespace SegHis\modules\eclaims\models;


class EclaimsPersonInsurance extends \CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'care_person_insurance';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('hcare_id, priority, modify_time', 'required'),
            array('priority, is_principal, is_pbef, class_nr, is_void', 'numerical', 'integerOnly' => true),
            array('pid', 'length', 'max' => 12),
            array('hcare_id', 'length', 'max' => 8),
            array('insurance_nr', 'length', 'max' => 25),
            array('modify_id, create_id', 'length', 'max' => 35),
            array('create_time, start_coverage_date, end_coverage_date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('pid, hcare_id, insurance_nr, priority, is_principal, is_pbef, class_nr, is_void, modify_id, modify_time, create_id, create_time, start_coverage_date, end_coverage_date', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'pid' => 'Pid',
            'hcare_id' => 'Hcare',
            'insurance_nr' => 'Insurance Nr',
            'priority' => 'Priority',
            'is_principal' => 'Is Principal',
            'is_pbef' => 'Is Pbef',
            'class_nr' => 'Class Nr',
            'is_void' => 'Is Void',
            'modify_id' => 'Modify',
            'modify_time' => 'Modify Time',
            'create_id' => 'Create',
            'create_time' => 'Create Time',
            'start_coverage_date' => 'Start Coverage Date',
            'end_coverage_date' => 'End Coverage Date',
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

        $criteria->compare('pid', $this->pid, true);
        $criteria->compare('hcare_id', $this->hcare_id, true);
        $criteria->compare('insurance_nr', $this->insurance_nr, true);
        $criteria->compare('priority', $this->priority);
        $criteria->compare('is_principal', $this->is_principal);
        $criteria->compare('is_pbef', $this->is_pbef);
        $criteria->compare('class_nr', $this->class_nr);
        $criteria->compare('is_void', $this->is_void);
        $criteria->compare('modify_id', $this->modify_id, true);
        $criteria->compare('modify_time', $this->modify_time, true);
        $criteria->compare('create_id', $this->create_id, true);
        $criteria->compare('create_time', $this->create_time, true);
        $criteria->compare('start_coverage_date', $this->start_coverage_date, true);
        $criteria->compare('end_coverage_date', $this->end_coverage_date, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return PersonInsurance the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}