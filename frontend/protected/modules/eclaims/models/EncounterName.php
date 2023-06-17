<?php

/** 
 * This is the model class for table "seg_encounter_name". 
 * 
 * The followings are the available columns in table 'seg_encounter_name': 
 * @property string $encounter_nr
 * @property string $pid
 * @property string $name_first
 * @property string $name_middle
 * @property string $name_last
 */ 
class EncounterName extends CareActiveRecord
{ 
    /** 
     * @return string the associated database table name 
     */ 
    public function tableName() 
    { 
        return 'seg_encounter_name'; 
    } 

    /** 
     * @return array validation rules for model attributes. 
     */ 
    public function rules() 
    { 
        // NOTE: you should only define rules for those attributes that 
        // will receive user inputs. 
        return array( 
            array('encounter_nr, pid', 'required'),
            array('encounter_nr, pid', 'length', 'max'=>12),
            array('name_first, name_middle, name_last', 'length', 'max'=>50),
            // The following rule is used by search(). 
            // @todo Please remove those attributes that should not be searched. 
            array('encounter_nr, pid, name_first, name_middle, name_last', 'safe', 'on'=>'search'), 
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
        ); 
    } 

    /** 
     * @return array customized attribute labels (name=>label) 
     */ 
    public function attributeLabels() 
    { 
        return array( 
            'encounter_nr' => 'Encounter Nr',
            'pid' => 'Pid',
            'name_first' => 'Name First',
            'name_middle' => 'Name Middle',
            'name_last' => 'Name Last',
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
        $criteria->compare('pid',$this->pid,true);
        $criteria->compare('name_first',$this->name_first,true);
        $criteria->compare('name_middle',$this->name_middle,true);
        $criteria->compare('name_last',$this->name_last,true);

        return new CActiveDataProvider($this, array( 
            'criteria'=>$criteria, 
        )); 
    } 

    /** 
     * Returns the static model of the specified AR class. 
     * Please note that you should have this exact method in all your CActiveRecord descendants! 
     * @param string $className active record class name. 
     * @return EncounterName the static model class 
     */ 
    public static function model($className=__CLASS__) 
    { 
        return parent::model($className); 
    } 
} 
