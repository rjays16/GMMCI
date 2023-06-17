<?php

/**
 * This is the model class for table "seg_pmrf_member_category".
 *
 * The followings are the available columns in table 'seg_pmrf_member_category':
 * @property string $id
 * @property string $name
 * @property string $create_id
 * @property string $create_time
 * @property string $modify_id
 * @property string $modify_time
 *
 * The followings are the available model relations:
 * @property Pmrf[] $pmrves
 */
class PmrfMemberCategory extends CareActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_pmrf_member_category';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name', 'length', 'max' => 255),
            array('create_id, modify_id', 'length', 'max' => 100),
            array('create_time, modify_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, name, create_id, create_time, modify_id, modify_time', 'safe', 'on' => 'search'),
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
            'pmrves' => array(self::HAS_MANY, 'Pmrf', 'membership_category'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'create_id' => 'Create',
            'create_time' => 'Create Time',
            'modify_id' => 'Modify',
            'modify_time' => 'Modify Time',
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

        $criteria->compare('id', $this->id, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('create_id', $this->create_id, true);
        $criteria->compare('create_time', $this->create_time, true);
        $criteria->compare('modify_id', $this->modify_id, true);
        $criteria->compare('modify_time', $this->modify_time, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return PmrfMemberCategory the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
