<?php

Yii::import('application.models.Encounter');

/**
 * This is the model class for table "seg_billing_encounter".
 *
 * The followings are the available columns in table 'seg_billing_encounter':
 * @property string $bill_nr
 * @property string $bill_dte
 * @property string $bill_frmdte
 * @property string $encounter_nr
 * @property integer $accommodation_type
 * @property double $total_acc_charge
 * @property double $total_med_charge
 * @property double $total_sup_charge
 * @property double $total_srv_charge
 * @property double $total_ops_charge
 * @property double $total_doc_charge
 * @property double $total_msc_charge
 * @property double $total_prevpayments
 * @property integer $applied_hrs_cutoff
 * @property integer $is_final
 * @property string $request_flag
 * @property string $modify_id
 * @property string $modify_dt
 * @property string $create_id
 * @property string $create_dt
 * @property integer $is_deleted
 * @property integer $delete_reason
 * @property string $delete_other_reason
 * @property string $bill_time_started
 * @property string $bill_time_ended
 * @property integer $opd_type
 * @property string $discount_type
 * @property string $history
 *
 * The followings are the available model relations:
 * @property BillingCaserate[] $billingCaserates
 * @property CareInsuranceFirm[] $careInsuranceFirms
 * @property Discount[] $segDiscounts
 * @property CareEncounter $encounterNr
 * @property BillingEncounterDetails[] $billingEncounterDetails
 * @property BillingcomputedDiscount $billingcomputedDiscount
 * @property CmapEntriesBill[] $cmapEntriesBills
 * @property ConfinementTracker[] $confinementTrackers
 * @property CreditCollectionLedger[] $creditCollectionLedgers
 * @property LingapEntriesBill[] $lingapEntriesBills
 */
class BillingEncounter extends CareActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_billing_encounter';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('bill_nr, bill_dte, bill_frmdte, encounter_nr, accommodation_type, is_final, modify_dt', 'required'),
            array('accommodation_type, applied_hrs_cutoff, is_final, is_deleted, delete_reason, opd_type', 'numerical', 'integerOnly'=>true),
            array('total_acc_charge, total_med_charge, total_sup_charge, total_srv_charge, total_ops_charge, total_doc_charge, total_msc_charge, total_prevpayments', 'numerical'),
            array('bill_nr, encounter_nr', 'length', 'max'=>12),
            array('request_flag', 'length', 'max'=>10),
            array('modify_id, create_id', 'length', 'max'=>35),
            array('discount_type', 'length', 'max'=>9),
            array('create_dt, delete_other_reason, bill_time_started, bill_time_ended, history', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('bill_nr, bill_dte, bill_frmdte, encounter_nr, accommodation_type, total_acc_charge, total_med_charge, total_sup_charge, total_srv_charge, total_ops_charge, total_doc_charge, total_msc_charge, total_prevpayments, applied_hrs_cutoff, is_final, request_flag, modify_id, modify_dt, create_id, create_dt, is_deleted, delete_reason, delete_other_reason, bill_time_started, bill_time_ended, opd_type, discount_type, history', 'safe', 'on'=>'search'),
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
            // 'billingCaserates' => array(self::HAS_MANY, 'BillingCaserate', 'bill_nr'),
            // 'careInsuranceFirms' => array(self::MANY_MANY, 'CareInsuranceFirm', 'seg_billing_coverage(bill_nr, hcare_id)'),
            // 'segDiscounts' => array(self::MANY_MANY, 'Discount', 'seg_billing_discount(bill_nr, discountid)'),
            'encounterNr' => array(self::HAS_ONE, 'Encounter', 'encounter_nr'),
            'phic' => array(self::BELONGS_TO, 'PhicMember', 'encounter_nr'),
            // 'billingEncounterDetails' => array(self::HAS_MANY, 'BillingEncounterDetails', 'bill_nr'),
            // 'billingcomputedDiscount' => array(self::HAS_ONE, 'BillingcomputedDiscount', 'bill_nr'),
            // 'cmapEntriesBills' => array(self::HAS_MANY, 'CmapEntriesBill', 'ref_no'),
            // 'confinementTrackers' => array(self::HAS_MANY, 'ConfinementTracker', 'bill_nr'),
            // 'creditCollectionLedgers' => array(self::HAS_MANY, 'CreditCollectionLedger', 'bill_nr'),
            // 'lingapEntriesBills' => array(self::HAS_MANY, 'LingapEntriesBill', 'ref_no'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'bill_nr' => 'Bill Nr',
            'bill_dte' => 'Bill Dte',
            'bill_frmdte' => 'Bill Frmdte',
            'encounter_nr' => 'Encounter Nr',
            'accommodation_type' => 'Accommodation Type',
            'total_acc_charge' => 'Total Acc Charge',
            'total_med_charge' => 'Total Med Charge',
            'total_sup_charge' => 'Total Sup Charge',
            'total_srv_charge' => 'Total Srv Charge',
            'total_ops_charge' => 'Total Ops Charge',
            'total_doc_charge' => 'Total Doc Charge',
            'total_msc_charge' => 'Total Msc Charge',
            'total_prevpayments' => 'Total Prevpayments',
            'applied_hrs_cutoff' => 'Applied Hrs Cutoff',
            'is_final' => 'Is Final',
            'request_flag' => 'Request Flag',
            'modify_id' => 'Modify',
            'modify_dt' => 'Modify Dt',
            'create_id' => 'Create',
            'create_dt' => 'Create Dt',
            'is_deleted' => 'Is Deleted',
            'delete_reason' => 'Delete Reason',
            'delete_other_reason' => 'Delete Other Reason',
            'bill_time_started' => 'Bill Time Started',
            'bill_time_ended' => 'Bill Time Ended',
            'opd_type' => 'Opd Type',
            'discount_type' => 'Discount Type',
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

        $criteria=new CDbCriteria;

        $criteria->compare('bill_nr',$this->bill_nr,true);
        $criteria->compare('bill_dte',$this->bill_dte,true);
        $criteria->compare('bill_frmdte',$this->bill_frmdte,true);
        $criteria->compare('encounter_nr',$this->encounter_nr,true);
        $criteria->compare('accommodation_type',$this->accommodation_type);
        $criteria->compare('total_acc_charge',$this->total_acc_charge);
        $criteria->compare('total_med_charge',$this->total_med_charge);
        $criteria->compare('total_sup_charge',$this->total_sup_charge);
        $criteria->compare('total_srv_charge',$this->total_srv_charge);
        $criteria->compare('total_ops_charge',$this->total_ops_charge);
        $criteria->compare('total_doc_charge',$this->total_doc_charge);
        $criteria->compare('total_msc_charge',$this->total_msc_charge);
        $criteria->compare('total_prevpayments',$this->total_prevpayments);
        $criteria->compare('applied_hrs_cutoff',$this->applied_hrs_cutoff);
        $criteria->compare('is_final',$this->is_final);
        $criteria->compare('request_flag',$this->request_flag,true);
        $criteria->compare('modify_id',$this->modify_id,true);
        $criteria->compare('modify_dt',$this->modify_dt,true);
        $criteria->compare('create_id',$this->create_id,true);
        $criteria->compare('create_dt',$this->create_dt,true);
        $criteria->compare('is_deleted',$this->is_deleted);
        $criteria->compare('delete_reason',$this->delete_reason);
        $criteria->compare('delete_other_reason',$this->delete_other_reason,true);
        $criteria->compare('bill_time_started',$this->bill_time_started,true);
        $criteria->compare('bill_time_ended',$this->bill_time_ended,true);
        $criteria->compare('opd_type',$this->opd_type);
        $criteria->compare('discount_type',$this->discount_type,true);
        $criteria->compare('history',$this->history,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return BillingEncounter the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
} 