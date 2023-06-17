<?php

namespace SegHis\modules\eclaims\models;

/**
 * This is the model class for table "seg_eclaims_config".
 *
 * The followings are the available columns in table 'seg_eclaims_config':
 * @property string $id
 * @property string $type
 * @property string $value
 * @property integer $active
 * @property string $remarks
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 */
class EclaimsConfig extends \CActiveRecord
{

    const CLOUD_STORAGE = 'cloud_storage_enabled';
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_eclaims_config';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('type, value', 'required'),
            array('active', 'numerical', 'integerOnly' => true),
            array('type, value', 'length', 'max' => 255),
            array('remarks, created_at, updated_at, deleted_at', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, type, value, active, remarks, created_at, updated_at, deleted_at', 'safe', 'on' => 'search'),
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
            'id' => 'ID',
            'type' => 'Type',
            'value' => 'Value',
            'active' => 'Active',
            'remarks' => 'Remarks',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',
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

        $criteria = new \CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('type', $this->type, true);
        $criteria->compare('value', $this->value, true);
        $criteria->compare('active', $this->active);
        $criteria->compare('remarks', $this->remarks, true);
        $criteria->compare('created_at', $this->created_at, true);
        $criteria->compare('updated_at', $this->updated_at, true);
        $criteria->compare('deleted_at', $this->deleted_at, true);

        return new \CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return EclaimsConfig the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    /**
     * cloudStorageEnabled - check if config is cloud_storage_enabled
     *
     * @return void
     */
    public function cloudStorageEnabled()
    {
        //   Eclaims config that checks if cloud storage is enabled
        $cloudStorage = EclaimsConfig::model()->find(
            array(
                'condition' => 'type=:type',
                'params' => array(
                    ':type' => self::CLOUD_STORAGE,
                ),
            )
        );
        return $cloudStorage->value;

    }
}
