
<?php

/** 
 * This is the model class for table "seg_billing_pf". 
 * 
 * The followings are the available columns in table 'seg_billing_pf': 
 * @property string $bill_nr
 * @property string $hcare_id
 * @property integer $dr_nr
 * @property string $role_area
 * @property string $dr_charge
 * @property string $dr_claim
 * 
 * The followings are the available model relations: 
 * @property CareInsuranceFirm $hcare
 * @property CarePersonell $drNr
 */ 
class BillingPf extends CActiveRecord
{ 
    /** 
     * @return string the associated database table name 
     */ 
    public function tableName() 
    { 
        return 'seg_billing_pf'; 
    } 

    /** 
     * @return array validation rules for model attributes. 
     */ 
    public function rules() 
    { 
        // NOTE: you should only define rules for those attributes that 
        // will receive user inputs. 
        return array( 
            array('bill_nr, hcare_id, dr_nr, role_area', 'required'),
            array('dr_nr', 'numerical', 'integerOnly'=>true),
            array('bill_nr', 'length', 'max'=>13),
            array('hcare_id', 'length', 'max'=>8),
            array('role_area', 'length', 'max'=>2),
            array('dr_charge, dr_claim', 'length', 'max'=>20),
            // The following rule is used by search(). 
            // @todo Please remove those attributes that should not be searched. 
            array('bill_nr, hcare_id, dr_nr, role_area, dr_charge, dr_claim', 'safe', 'on'=>'search'), 
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
            // 'hcare' => array(self::BELONGS_TO, 'CareInsuranceFirm', 'hcare_id'),
            'drNr' => array(self::BELONGS_TO, 'Personnel', 'dr_nr'),
        ); 
    } 

    /** 
     * @return array customized attribute labels (name=>label) 
     */ 
    public function attributeLabels() 
    { 
        return array( 
            'bill_nr' => 'Bill Nr',
            'hcare_id' => 'Hcare',
            'dr_nr' => 'Dr Nr',
            'role_area' => 'Role Area',
            'dr_charge' => 'Dr Charge',
            'dr_claim' => 'Dr Claim',
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

        $criteria->compare('bill_nr',$this->bill_nr,true);
        $criteria->compare('hcare_id',$this->hcare_id,true);
        $criteria->compare('dr_nr',$this->dr_nr);
        $criteria->compare('role_area',$this->role_area,true);
        $criteria->compare('dr_charge',$this->dr_charge,true);
        $criteria->compare('dr_claim',$this->dr_claim,true);

        return new CActiveDataProvider($this, array( 
            'criteria'=>$criteria, 
        )); 
    } 

    /** 
     * Returns the static model of the specified AR class. 
     * Please note that you should have this exact method in all your CActiveRecord descendants! 
     * @param string $className active record class name. 
     * @return BillingPf the static model class 
     */ 
    public static function model($className=__CLASS__) 
    { 
        return parent::model($className); 
    } 
} 