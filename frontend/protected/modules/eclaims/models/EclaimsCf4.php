<?php

namespace SegHis\modules\eclaims\models;

/**
 * This is the model class for table "seg_eclaims_cf4".
 *
 * The followings are the available columns in table 'seg_eclaims_cf4':
 * @property string $phic_trans_no
 * @property string $xml
 * @property integer $is_uploaded
 * @property string $created_at
 * @property string $updated_at
 */
class EclaimsCf4 extends \CActiveRecord
{
  /**
   * @return string the associated database table name
   */
  public function tableName()
  {
    return 'seg_eclaims_cf4';
  }

  /**
   * @return array validation rules for model attributes.
   */
  public function rules()
  {
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return array(
        array('phic_trans_no, xml, is_uploaded', 'required'),
        array('is_uploaded', 'numerical', 'integerOnly' => true),
        array('phic_trans_no', 'length', 'max' => 21),
        array('created_at, updated_at', 'safe'),
      // The following rule is used by search().
      // @todo Please remove those attributes that should not be searched.
        array('phic_trans_no, xml, is_uploaded, created_at, updated_at', 'safe', 'on' => 'search'),
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
        'phic_trans_no' => 'Phic Trans No',
        'xml' => 'Xml',
        'is_uploaded' => 'Is Uploaded',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
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

    $criteria->compare('phic_trans_no', $this->phic_trans_no, true);
    $criteria->compare('xml', $this->xml, true);
    $criteria->compare('is_uploaded', $this->is_uploaded);
    $criteria->compare('created_at', $this->created_at, true);
    $criteria->compare('updated_at', $this->updated_at, true);

    return new CActiveDataProvider($this, array(
        'criteria' => $criteria,
    ));
  }

  /**
   * Returns the static model of the specified AR class.
   * Please note that you should have this exact method in all your CActiveRecord descendants!
   * @param string $className active record class name.
   * @return EclaimsCf4 the static model class
   */
  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }
}