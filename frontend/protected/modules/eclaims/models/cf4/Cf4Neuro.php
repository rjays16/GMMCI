<?php

/**
 * This is the model class for table "seg_cf4_neuro".
 *
 * The followings are the available columns in table 'seg_cf4_neuro':
 * @property string $id
 * @property string $entry_id
 * @property string $encounter_nr
 * @property integer $neuro_id
 * @property string $remarks
 * @property string $created_at
 * @property string $modify
 * @property string $modified_by
 * @property string $deleted_by
 * @property integer $is_deleted
 *
 * The followings are the available model relations:
 * @property SegCf4H $entry
 * @property SegCf4LibNeuro $neuro
 */
class Cf4Neuro extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_cf4_neuro';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            // array('id, created_at', 'required'),
            array('neuro_id, is_deleted', 'numerical', 'integerOnly'=>true),
            array('id, entry_id', 'length', 'max'=>36),
            array('encounter_nr', 'length', 'max'=>12),
            array('modified_by, deleted_by', 'length', 'max'=>50),
            array('remarks, modify', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, entry_id, encounter_nr, neuro_id, remarks, created_at, modify, modified_by, deleted_by, is_deleted', 'safe', 'on'=>'search'),
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
            'neuro' => array(self::BELONGS_TO, 'SegCf4LibNeuro', 'neuro_id'),
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
            'neuro_id' => 'Neuro',
            'remarks' => '',
            'created_at' => 'Created At',
            'modify' => 'Modify',
            'modified_by' => 'Modified By',
            'deleted_by' => 'Deleted By',
            'is_deleted' => 'Is Deleted',
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

        $criteria->compare('id',$this->id,true);
        $criteria->compare('entry_id',$this->entry_id,true);
        $criteria->compare('encounter_nr',$this->encounter_nr,true);
        $criteria->compare('neuro_id',$this->neuro_id);
        $criteria->compare('remarks',$this->remarks,true);
        $criteria->compare('created_at',$this->created_at,true);
        $criteria->compare('modify',$this->modify,true);
        $criteria->compare('modified_by',$this->modified_by,true);
        $criteria->compare('deleted_by',$this->deleted_by,true);
        $criteria->compare('is_deleted',$this->is_deleted);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Cf4Neuro the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function getUuid()
    {
        $command = $this->dbConnection->createCommand('Select UUID()');

        return $command->queryScalar();
    }

} 