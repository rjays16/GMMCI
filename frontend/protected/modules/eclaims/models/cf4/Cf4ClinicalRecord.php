<?php
/**
 * Cf4ClinicalRecord.php
 *
 * @author Jan Chris S. Ogel <iamjc93@gmail.com>
 * @copyright (c) 2019, Segworks Technologies Corporation (http://www.segworks.com)
 */

/**
 * This is the model class for table "seg_cf4_clinical_record".
 *
 * The followings are the available columns in table 'seg_cf4_clinical_record':
 * @property string $id
 * @property string $entry_id
 * @property string $encounter_nr
 * @property string $present_illness
 * @property string $created_at
 * @property string $modify
 * @property string $modified_by
 * @property string $deleted_by
 * @property string $deleted_at
 * @property integer $is_deleted
 *
 * The followings are the available model relations:
 * @property Cf4 $entry
 * @property Encounter $encounterNo
 */
class Cf4ClinicalRecord extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_cf4_clinical_record';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('present_illness', 'required'),
            array('is_deleted', 'numerical', 'integerOnly'=>true),
            array('id, entry_id', 'length', 'max'=>36),
            array('encounter_nr', 'length', 'max'=>15),
            array('modified_by, deleted_by', 'length', 'max'=>50),
            array('present_illness, modify, deleted_at', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, entry_id, encounter_nr, present_illness, created_at, modify, modified_by, deleted_by, deleted_at, is_deleted', 'safe', 'on'=>'search'),
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
        //     'entry' => array(self::BELONGS_TO, 'Cf4', 'entry_id'),
        //     'encounterNo' => array(self::BELONGS_TO, 'Encounter', 'encounter_nr'),
        // );
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
            'present_illness' => 'Brief History of Present Illness',
            'created_at' => 'Created At',
            'modify' => 'Modify',
            'modified_by' => 'Modified By',
            'deleted_by' => 'Deleted By',
            'deleted_at' => 'Deleted At',
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
        $criteria->compare('present_illness',$this->present_illness,true);
        $criteria->compare('created_at',$this->created_at,true);
        $criteria->compare('modify',$this->modify,true);
        $criteria->compare('modified_by',$this->modified_by,true);
        $criteria->compare('deleted_by',$this->deleted_by,true);
        $criteria->compare('deleted_at',$this->deleted_at,true);
        $criteria->compare('is_deleted',$this->is_deleted);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Cf4ClinicalRecord the static model class
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