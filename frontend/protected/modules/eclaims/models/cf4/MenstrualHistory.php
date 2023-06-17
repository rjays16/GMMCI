<?php

/** 
 * This is the model class for table "seg_cf4_menstrual_history". 
 * 
 * The followings are the available columns in table 'seg_cf4_menstrual_history': 
 * @property integer $id
 * @property string $entry_id
 * @property string $encounter_nr
 * @property string $init_prenatal_cons
 * @property string $date_of_lmp
 * @property integer $age_of_menarche
 * @property integer $period_duration
 * @property string $created_at
 * @property string $updated_at
 * @property string $created_by
 * @property string $updated_by
 * @property integer $is_deleted
 * @property string $deleted_by
 * @property string $is_applicable
 * 
 * The followings are the available model relations: 
 * @property SegCf4H $entry
 */ 
class MenstrualHistory extends CActiveRecord
{ 
    /** 
     * @return string the associated database table name 
     */ 
    public function tableName() 
    { 
        return 'seg_cf4_menstrual_history'; 
    } 

    /** 
     * @return array validation rules for model attributes. 
     */ 
    public function rules() 
    { 
        // NOTE: you should only define rules for those attributes that 
        // will receive user inputs. 
        return array( 
            array('created_at,date_of_lmp', 'required'),
            array('period_duration, is_deleted', 'numerical', 'integerOnly'=>true),
            array('entry_id', 'length', 'max'=>36),
            array('encounter_nr', 'length', 'max'=>12),
            array('created_by, updated_by, deleted_by', 'length', 'max'=>450),
            array('is_applicable', 'length', 'max'=>1),
            array('init_prenatal_cons, date_of_lmp, updated_at', 'safe'),
            // The following rule is used by search(). 
            // @todo Please remove those attributes that should not be searched. 
            array('id, entry_id, encounter_nr, init_prenatal_cons, date_of_lmp, age_of_menarche, period_duration, created_at, updated_at, created_by, updated_by, is_deleted, deleted_by, is_applicable', 'safe', 'on'=>'search'), 
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
            'entry' => array(self::BELONGS_TO, 'SegCf4H', 'entry_id'),
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
            'init_prenatal_cons' => 'Initial Prenatal Consultation',
            'date_of_lmp' => 'Last Menstrual Period',
            'age_of_menarche' => 'Age Of Menarche',
            'period_duration' => 'Period Duration',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'is_deleted' => 'Is Deleted',
            'deleted_by' => 'Deleted By',
            'is_applicable' => 'Is Applicable',
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
        $criteria->compare('entry_id',$this->entry_id,true);
        $criteria->compare('encounter_nr',$this->encounter_nr,true);
        $criteria->compare('init_prenatal_cons',$this->init_prenatal_cons,true);
        $criteria->compare('date_of_lmp',$this->date_of_lmp,true);
        $criteria->compare('age_of_menarche',$this->age_of_menarche);
        $criteria->compare('period_duration',$this->period_duration);
        $criteria->compare('created_at',$this->created_at,true);
        $criteria->compare('updated_at',$this->updated_at,true);
        $criteria->compare('created_by',$this->created_by,true);
        $criteria->compare('updated_by',$this->updated_by,true);
        $criteria->compare('is_deleted',$this->is_deleted);
        $criteria->compare('deleted_by',$this->deleted_by,true);
        $criteria->compare('is_applicable',$this->is_applicable,true);

        return new CActiveDataProvider($this, array( 
            'criteria'=>$criteria, 
        )); 
    } 

    public function getUuid()
    {
        $command = $this->dbConnection->createCommand('Select UUID()');

        return $command->queryScalar();
    }

    /** 
     * Returns the static model of the specified AR class. 
     * Please note that you should have this exact method in all your CActiveRecord descendants! 
     * @param string $className active record class name. 
     * @return Cf4MenstrualHistory the static model class 
     */ 
    public static function model($className=__CLASS__) 
    { 
        return parent::model($className); 
    } 
} 