<?php
/**
 * Transmittal.php
 *
 * @author Alvin Jay C. Cosare <ajunecosare15@gmail.com>
 * @author Christian Joseph Dalisay <cjsdjoseph098@gmail.com>
 * @copyright (c) 2014, Segworks Technologies Corporation (http://www.segworks.com)
 */


/**
 * This is the model class for table "seg_transmittal".
 * The followings are the available columns in table 'seg_transmittal':
 * @property string $transmit_no
 * @property string $transmit_dte
 * @property integer $hcare_id
 * @property string $remarks
 * @property string $create_id
 * @property string $create_dt
 * @property string $modify_id
 * @property string $modify_dt
 * @property string $xml_data
 * @property string $xml_is_valid
 * @property string $is_uploaded
 * @property string $is_mapped
 *
 * The followings are the available model relations:
 */
class Transmittal extends CareActiveRecord {
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_transmittal';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('transmit_no, transmit_dte, is_uploaded, is_mapped', 'safe', 'on'=>'search'),
             array('modify_dt','default',
              'value'=>new CDbExpression('NOW()'),
              'setOnEmpty'=>false,'on'=>'update'),
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
            'details' => array(self::HAS_MANY, 'TransmittalDetails', 'transmit_no'),
            // 'encounter' => array(self::HAS_ONE, 'Encounter', 'transmit_no'),

        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'transmit_no' => 'Transmittal Number',
            'transmit_dte' => 'Transmittal Date',
            'is_uploaded' => 'Status',
            'is_mapped' => 'Map Status',
            'hcare_id' => 'HCARE ID',
            'remarks' => 'Remarks',
            'modify_dt' => 'Modified DateTime',
            'control_no' => 'Control Number',
            'ticket_no' => 'Receipt Ticket Number',
            'no_claim' => 'Number of Claims',
            'xml_data' => 'Transmittal XML',
            'xml_is_valid' => 'Validity'
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return SegTransmittal the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

}
