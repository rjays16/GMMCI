<?php

/**
 * This is the model class for table "seg_member_info".
 *
 * The followings are the available columns in table 'seg_member_info':
 * @property string $id
 * @property string $pid
 * @property string $encounter_nr
 * @property integer $hcare_id
 * @property integer $is_member
 * @property string $pin
 * @property string $relation
 * @property string $name_last
 * @property string $name_first
 * @property string $name_middle
 * @property string $name_extension
 * @property string $maiden_name_last
 * @property string $maiden_name_first
 * @property string $maiden_name_middle
 * @property string $maiden_name_extension
 * @property string $sex
 * @property string $civil_status
 * @property string $birth_date
 * @property string $birth_place
 * @property string $nationality
 * @property string $floor
 * @property string $building_name
 * @property string $lot_no
 * @property string $street
 * @property string $subdivision
 * @property string $barangay
 * @property string $municipality
 * @property string $province
 * @property string $country
 * @property string $zip_code
 * @property string $tel_no
 * @property string $mobile_no
 * @property string $email
 * @property string $create_id
 * @property string $create_time
 * @property string $modify_id
 * @property string $modify_time
 * @property string $history
 *
 * The followings are the available model relations:
 * @property ClaimForm1 $claimForm1
 * @property Pmrf $pmrf
 */
class MemberInfo extends CareActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_member_info';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('hcare_id, is_member', 'numerical', 'integerOnly' => true),
            array('pid, encounter_nr', 'length', 'max' => 12),
            array('pin, civil_status', 'length', 'max' => 20),
            array('relation, sex', 'length', 'max' => 1),
            array('name_last, name_first, name_middle, name_extension, maiden_name_last, maiden_name_first, maiden_name_middle, maiden_name_extension', 'length', 'max' => 30),
            array('birth_place', 'length', 'max' => 100),
            array('nationality, floor, building_name, lot_no, street, subdivision, barangay, municipality, province, email', 'length', 'max' => 50),
            array('country', 'length', 'max' => 150),
            array('zip_code', 'length', 'max' => 10),
            array('tel_no, mobile_no', 'length', 'max' => 15),
            array('create_id, modify_id', 'length', 'max' => 35),
            array('birth_date, create_time, modify_time, history', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, pid, encounter_nr, hcare_id, is_member, pin, relation, name_last, name_first, name_middle, name_extension, maiden_name_last, maiden_name_first, maiden_name_middle, maiden_name_extension, sex, civil_status, birth_date, birth_place, nationality, floor, building_name, lot_no, street, subdivision, barangay, municipality, province, country, zip_code, tel_no, mobile_no, email, create_id, create_time, modify_id, modify_time, history', 'safe', 'on' => 'search'),
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
            'claimForm1' => array(self::HAS_ONE, 'ClaimForm1', 'member_info_id'),
            'pmrf' => array(self::HAS_ONE, 'Pmrf', 'member_info_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'pid' => 'Pid',
            'encounter_nr' => 'Encounter Nr',
            'hcare_id' => 'Hcare',
            'is_member' => 'Is Member',
            'pin' => 'Pin',
            'relation' => 'Relation',
            'name_last' => 'Name Last',
            'name_first' => 'Name First',
            'name_middle' => 'Name Middle',
            'name_extension' => 'Name Extension',
            'maiden_name_last' => 'Maiden Name Last',
            'maiden_name_first' => 'Maiden Name First',
            'maiden_name_middle' => 'Maiden Name Middle',
            'maiden_name_extension' => 'Maiden Name Extension',
            'sex' => 'Sex',
            'civil_status' => 'Civil Status',
            'birth_date' => 'Birth Date',
            'birth_place' => 'Birth Place',
            'nationality' => 'Nationality',
            'floor' => 'Floor',
            'building_name' => 'Building Name',
            'lot_no' => 'Lot No',
            'street' => 'Street',
            'subdivision' => 'Subdivision',
            'barangay' => 'Barangay',
            'municipality' => 'Municipality',
            'province' => 'Province',
            'country' => 'Country',
            'zip_code' => 'Zip Code',
            'tel_no' => 'Tel No',
            'mobile_no' => 'Mobile No',
            'email' => 'Email',
            'create_id' => 'Create',
            'create_time' => 'Create Time',
            'modify_id' => 'Modify',
            'modify_time' => 'Modify Time',
            'history' => 'History',
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
        $criteria->compare('pid', $this->pid, true);
        $criteria->compare('encounter_nr', $this->encounter_nr, true);
        $criteria->compare('hcare_id', $this->hcare_id);
        $criteria->compare('is_member', $this->is_member);
        $criteria->compare('pin', $this->pin, true);
        $criteria->compare('relation', $this->relation, true);
        $criteria->compare('name_last', $this->name_last, true);
        $criteria->compare('name_first', $this->name_first, true);
        $criteria->compare('name_middle', $this->name_middle, true);
        $criteria->compare('name_extension', $this->name_extension, true);
        $criteria->compare('maiden_name_last', $this->maiden_name_last, true);
        $criteria->compare('maiden_name_first', $this->maiden_name_first, true);
        $criteria->compare('maiden_name_middle', $this->maiden_name_middle, true);
        $criteria->compare('maiden_name_extension', $this->maiden_name_extension, true);
        $criteria->compare('sex', $this->sex, true);
        $criteria->compare('civil_status', $this->civil_status, true);
        $criteria->compare('birth_date', $this->birth_date, true);
        $criteria->compare('birth_place', $this->birth_place, true);
        $criteria->compare('nationality', $this->nationality, true);
        $criteria->compare('floor', $this->floor, true);
        $criteria->compare('building_name', $this->building_name, true);
        $criteria->compare('lot_no', $this->lot_no, true);
        $criteria->compare('street', $this->street, true);
        $criteria->compare('subdivision', $this->subdivision, true);
        $criteria->compare('barangay', $this->barangay, true);
        $criteria->compare('municipality', $this->municipality, true);
        $criteria->compare('province', $this->province, true);
        $criteria->compare('country', $this->country, true);
        $criteria->compare('zip_code', $this->zip_code, true);
        $criteria->compare('tel_no', $this->tel_no, true);
        $criteria->compare('mobile_no', $this->mobile_no, true);
        $criteria->compare('email', $this->email, true);
        $criteria->compare('create_id', $this->create_id, true);
        $criteria->compare('create_time', $this->create_time, true);
        $criteria->compare('modify_id', $this->modify_id, true);
        $criteria->compare('modify_time', $this->modify_time, true);
        $criteria->compare('history', $this->history, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return MemberInfo the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @param string $format <ul>
     * <li>{first_name}</li>
     * <li>{middle_initial}</li>
     * <li>{last_name}</li>
     * <li>{suffix}</li>
     * </ul>
     * @param bool $upperCase
     * @return string
     */
    public function getFullName($format = '{first_name} {middle_initial} {last_name} {suffix}', $upperCase = true)
    {
        $fullName = strtr($format,array(
            '{first_name}' => $this->name_first,
            '{middle_name}' => $this->name_middle,
            '{middle_initial}' => $this->name_middle ? $this->name_middle[0] . '.' : '',
            '{last_name}' => $this->name_last,
            '{suffix}' => $this->name_extension,
        ));
        return trim($upperCase ? strtoupper($fullName) : $fullName);
    }
}
