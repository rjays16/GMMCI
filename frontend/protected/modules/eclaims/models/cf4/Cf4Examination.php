<?php
/**
 * Created by PhpStorm.
 * User: Bender
 * Date: 7/24/2019
 * Time: 2:32 AM
 */

namespace SegHis\modules\eclaims\models\cf4;

/**
 * This is the model class for table "seg_cf4_examination".
 *
 * The followings are the available columns in table 'seg_cf4_examination':
 * @property integer $id
 * @property string $exam_code
 * @property string $table_name
 * @property string $phic_id
 * @property string $create_dt
 * @property string $modify_dt
 * @property string $modify_id
 * @property string $create_id
 * @property integer $is_deleted
 * @property string $deleted_at
 */
class Cf4Examination extends \CActiveRecord
{

  /* default value for codes*/
  const CHEST = 'CHEST';
  const HEART = 'HEART';
  const HEENT = 'HEENT';
  const ABD = 'ABD';
  const NEURO = 'NEURO';
  const RECTAL = 'RECTAL';
  const SKIN = 'SKIN';
  const GUIE = 'GUIE';


  /**
   * @return string the associated database table name
   */
  public function tableName()
  {
    return 'seg_cf4_examination';
  }

  /**
   * @return array validation rules for model attributes.
   */
  public function rules()
  {
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return array(
        array('create_dt', 'required'),
        array('is_deleted', 'numerical', 'integerOnly' => true),
        array('exam_code', 'length', 'max' => 55),
        array('table_name, phic_id', 'length', 'max' => 100),
        array('modify_id, create_id', 'length', 'max' => 12),
        array('modify_dt, deleted_at', 'safe'),
      // The following rule is used by search().
      // @todo Please remove those attributes that should not be searched.
        array(
            'id, exam_code, table_name, phic_id, create_dt, modify_dt, modify_id, create_id, is_deleted, deleted_at',
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
    return array();
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels()
  {
    return array(
        'id' => 'ID',
        'exam_code' => 'Exam Code',
        'table_name' => 'Table Name',
        'phic_id' => 'Phic',
        'create_dt' => 'Create Dt',
        'modify_dt' => 'Modify Dt',
        'modify_id' => 'Modify',
        'create_id' => 'Create',
        'is_deleted' => 'Is Deleted',
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

    $criteria->compare('id', $this->id);
    $criteria->compare('exam_code', $this->exam_code, true);
    $criteria->compare('table_name', $this->table_name, true);
    $criteria->compare('phic_id', $this->phic_id, true);
    $criteria->compare('create_dt', $this->create_dt, true);
    $criteria->compare('modify_dt', $this->modify_dt, true);
    $criteria->compare('modify_id', $this->modify_id, true);
    $criteria->compare('create_id', $this->create_id, true);
    $criteria->compare('is_deleted', $this->is_deleted);
    $criteria->compare('deleted_at', $this->deleted_at, true);

    return new \CActiveDataProvider($this, array(
        'criteria' => $criteria,
    ));
  }

  /**
   * Returns the static model of the specified AR class.
   * Please note that you should have this exact method in all your CActiveRecord descendants!
   * @param string $className active record class name.
   * @return Cf4Examination the static model class
   */
  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }
}