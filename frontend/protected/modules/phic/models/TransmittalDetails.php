<?php
/**
 * TransmittalDetails.php
 *
 * @author Christian Joseph Dalisay <cjsdjoseph098@gmail.com>
 * @copyright (c) 2014, Segworks Technologies Corporation (http://www.segworks.com)
 */

Yii::import('phic.models.PhicHospitalBill');

/**
 * This is the model class for table "seg_transmittal_details".
 * The followings are the available columns in table 'seg_transmittal_details':
 * @property string $transmit_no
 * @property string $encounter_nr
 * @property double $patient_claim
 *
 * The followings are the available model relations:
 * @property PhicMember $memberType
 * @property Encounter $encounter
 */
class TransmittalDetails extends ActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_transmittal_details';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('transmit_no, encounter_nr', 'safe', 'on' => 'search',),

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
            'encounter' => array(self::BELONGS_TO, 'Encounter', 'encounter_nr'),
            'billing' => array(
                self::BELONGS_TO,
                'PhicHospitalBill',
                array('encounter_nr' => 'encounter_nr'),
                'on' => '(billing.is_deleted IS NULL OR billing.is_deleted=0) AND billing.is_final=1',
            )
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'transmit_no' => 'Transmittal Number',
            'encounter_nr' => 'Encounter Number',
            'patient_claim' => 'Patient Claim',
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

        $criteria->compare('transmit_no', $this->transmit_no, true);
        $criteria->compare('encounter_nr', $this->encounter_nr, true);
        $criteria->compare('patient_claim', $this->patient_claim, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return SegTransmittalDetails the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

}
