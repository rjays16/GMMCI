<?php

/** 
 * This is the model class for table "seg_cf4_obstetric_history". 
 * 
 * The followings are the available columns in table 'seg_cf4_obstetric_history': 
 * @property integer $id
 * @property string $entry_id
 * @property string $encounter_nr
 * @property integer $gravida
 * @property integer $parity
 * @property integer $term_births
 * @property integer $preterm_births
 * @property integer $abortion
 * @property integer $living_children
 * @property integer $number_stillbirth
 * @property string $created_at
 * @property string $updated_at
 * @property string $created_by
 * @property string $updated_by
 * 
 * The followings are the available model relations: 
 * @property SegCf4H $entry
 */ 
class ObstetricHistory extends CActiveRecord
{ 
    /** 
     * @return string the associated database table name 
     */ 
    public function tableName() 
    { 
        return 'seg_cf4_obstetric_history'; 
    } 

    /** 
     * @return array validation rules for model attributes. 
     */ 
    public function rules() 
    { 
        // NOTE: you should only define rules for those attributes that 
        // will receive user inputs. 
        return array( 
          array('abortion,term_births,gravida,parity,living_children,preterm_births', 'required'),
          array(
            'gravida, parity, term_births, preterm_births, abortion, living_children, number_stillbirth',
            'numerical',
            'integerOnly' => true
          ),
            array('entry_id', 'length', 'max'=>36),
            array('encounter_nr', 'length', 'max'=>12),
            array('created_by, updated_by', 'length', 'max'=>450),
            array('updated_at', 'safe'),
            // The following rule is used by search(). 
            // @todo Please remove those attributes that should not be searched. 
          array(
            'id, entry_id, encounter_nr, gravida, parity, term_births, preterm_births, abortion, living_children, number_stillbirth, created_at, updated_at, created_by, updated_by',
            'safe',
            'on' => 'search'
          ),
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
            'gravida' => 'Gravida',
            'parity' => 'Parity',
            'term_births' => 'No. of Full Term Pregnancy',
            'preterm_births' => 'No. of Premature Pregnancy',
            'abortion' => 'No. of Abortion',
            'living_children' => 'No. of Living Children',
            'number_stillbirth' => 'Number Stillbirth',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
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
        $criteria->compare('gravida',$this->gravida);
        $criteria->compare('parity',$this->parity);
        $criteria->compare('term_births',$this->term_births);
        $criteria->compare('preterm_births',$this->preterm_births);
        $criteria->compare('abortion',$this->abortion);
        $criteria->compare('living_children',$this->living_children);
        $criteria->compare('number_stillbirth',$this->number_stillbirth);
        $criteria->compare('created_at',$this->created_at,true);
        $criteria->compare('updated_at',$this->updated_at,true);
        $criteria->compare('created_by',$this->created_by,true);
        $criteria->compare('updated_by',$this->updated_by,true);

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
     * @return Cf4ObstetricHistory the static model class 
     */ 
    public static function model($className=__CLASS__) 
    { 
        return parent::model($className); 
    } 
} 