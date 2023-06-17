<?php

/**
 * This is the model class for table "seg_religion".
 *
 * The followings are the available columns in table 'seg_religion':
 * @property integer $religion_nr
 * @property string $religion_name
 * @property integer $is_deleted
 * @property string $modify_id
 * @property string $modify_date
 * @property string $create_id
 * @property string $create_date
 *
 * The followings are the available model relations:
 * @property PersonCatalog[] $personCatalogs
 */
class ReligionCatalog extends \CareActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_religion';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('religion_name', 'required'),
            array('religion_id, is_deleted', 'numerical', 'integerOnly'=>true),
            array('religion_name', 'length', 'max'=>150),
            array('modify_id, create_id', 'length', 'max'=>35),
            array('modify_dt, create_dt', 'safe'),
            array('is_deleted','default',
                'value'=> 0,
                'setOnEmpty'=>false,'on'=>'insert'),
            array('modify_dt, create_dt','default',
                'value'=>new CDbExpression('NOW()'),
                'setOnEmpty'=>false,'on'=>'insert'),
            array('modify_id, create_id','default',
                'value'=> Yii::app()->user->personnel->personnel_id,
                'setOnEmpty'=>false,'on'=>'insert'),
            array('modify_dt','default',
                'value'=>new CDbExpression('NOW()'),
                'setOnEmpty'=>false,'on'=>'update'),
            array('modify_id','default',
                'value'=> Yii::app()->user->personnel->personnel_id,
                'setOnEmpty'=>false,'on'=>'update'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('religion_id, religion_name, is_deleted, modify_id, modify_dt, create_id, create_dt', 'safe', 'on'=>'search'),
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
            'personCatalogs' => array(self::HAS_MANY, 'PersonCatalog', 'religion_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'religion_nr' => 'Religion ID',
            'religion_name' => 'Religion Name',
            'modify_id' => 'Modify',
            'modify_date' => 'Modify Dt',
            'create_id' => 'Create',
            'create_date' => 'Create Dt',
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

        $criteria->compare('religion_nr',$this->religion_nr);
        $criteria->compare('religion_name',$this->religion_name,true);
        $criteria->compare('is_deleted',$this->is_deleted);
        $criteria->compare('modify_id',$this->modify_id,true);
        $criteria->compare('modify_date',$this->modify_date,true);
        $criteria->compare('create_id',$this->create_id,true);
        $criteria->compare('create_date',$this->create_date,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->religion_name;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return ReligionCatalog the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function getReligionSearch($query = array()) {
        $criteria = new CDbCriteria();

        if (!empty($query)) {
            $criteria->addSearchCondition('religion_nr', $query['religion_nr'], true, 'AND');
            $criteria->addSearchCondition('religion_name', $query['religion_name'], true, 'AND');
        }

        $criteria->addCondition('t.is_deleted = 0', 'AND');

        return new CArrayDataProvider(
            $this->findAll($criteria), 
            array(
                'pagination' => array(
                    'pageSize' => 10,
                ),
                'keyField' => false
            )
        );
    }
}
