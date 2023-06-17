<?php

/** 
 * This is the model class for table "seg_cf4_prenatal_consultation_list". 
 * 
 * The followings are the available columns in table 'seg_cf4_prenatal_consultation_list': 
 * @property integer $id
 * @property string $value
 * @property string $detail
 * @property timestamp $created_at
 * @property string $create_by
 * 
 */ 
class PrenatalConsultationList extends CActiveRecord
{ 
    /** 
     * @return string the associated database table name 
     */ 
    public function tableName() 
    { 
        return 'seg_cf4_prenatal_consultation_list'; 
    } 

    /** 
     * @return array validation rules for model attributes. 
     */ 
    public function rules() 
    { 
        // NOTE: you should only define rules for those attributes that 
        // will receive user inputs. 
        return array( 
            // array('created_at,date_of_lmp, init_prenatal_cons', 'required'),
            // array('age_of_menarche, period_duration, is_deleted', 'numerical', 'integerOnly'=>true),
            // array('entry_id', 'length', 'max'=>36),
            // array('encounter_nr', 'length', 'max'=>12),
            // array('created_by, updated_by, deleted_by', 'length', 'max'=>450),
            // array('is_applicable', 'length', 'max'=>1),
            array('value, detail, created_at', 'safe'),
            // The following rule is used by search(). 
            // @todo Please remove those attributes that should not be searched. 
            // array('id, value, detail, created_at, created_by', 'safe', 'on'=>'search'), 
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
        //     'entry' => array(self::BELONGS_TO, 'SegCf4H', 'entry_id'),
        // ); 
    } 

    /** 
     * @return array customized attribute labels (name=>label) 
     */ 
    public function attributeLabels() 
    { 
        // return array( 
        //     'id' => 'ID',
        //     'entry_id' => 'Entry',
        //     'encounter_nr' => 'Encounter Nr',
        //     'init_prenatal_cons' => 'Initial Prenatal Consultation',
        //     'date_of_lmp' => 'Last Menstrual Period',
        //     'age_of_menarche' => 'Age Of Menarche',
        //     'period_duration' => 'Period Duration',
        //     'created_at' => 'Created At',
        //     'updated_at' => 'Updated At',
        //     'created_by' => 'Created By',
        //     'updated_by' => 'Updated By',
        //     'is_deleted' => 'Is Deleted',
        //     'deleted_by' => 'Deleted By',
        //     'is_applicable' => 'Is Applicable',
        // ); 
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

        // $criteria=new CDbCriteria; 

        // $criteria->compare('id',$this->id);
        // $criteria->compare('entry_id',$this->entry_id,true);
        // $criteria->compare('encounter_nr',$this->encounter_nr,true);
        // $criteria->compare('init_prenatal_cons',$this->init_prenatal_cons,true);
        // $criteria->compare('date_of_lmp',$this->date_of_lmp,true);
        // $criteria->compare('age_of_menarche',$this->age_of_menarche);
        // $criteria->compare('period_duration',$this->period_duration);
        // $criteria->compare('created_at',$this->created_at,true);
        // $criteria->compare('updated_at',$this->updated_at,true);
        // $criteria->compare('created_by',$this->created_by,true);
        // $criteria->compare('updated_by',$this->updated_by,true);
        // $criteria->compare('is_deleted',$this->is_deleted);
        // $criteria->compare('deleted_by',$this->deleted_by,true);
        // $criteria->compare('is_applicable',$this->is_applicable,true);

        // return new CActiveDataProvider($this, array( 
        //     'criteria'=>$criteria, 
        // )); 
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