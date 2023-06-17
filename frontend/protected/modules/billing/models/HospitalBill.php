<?php
/**
 * HospitalBill.php
 */

Yii::import('billing.models.HospitalBillDiscount');
Yii::import('billing.models.HospitalBillCoverage');
Yii::import('eclaims.models.BillingPf');

/**
 * This is the model class for table "seg_billing_encounter".
 * The followings are the available columns in table 'seg_billing_encounter':
 * @property string $bill_nr
 * @property string $bill_dte
 * @property string $bill_frmdte
 * @property string $encounter_nr
 * @property integer $accomodation_type
 * @property double $total_acc_charge
 * @property double $total_med_charge
 * @property double $total_sup_charge
 * @property double $total_srv_charge
 * @property double $total_ops_charge
 * @property double $total_docs_charge
 * @property double $total_msc_charge
 * @property double $total_prevpayments
 * @property double $total_auto_excess
 * @property integer $is_deleted
 * @property string $bill_time_started
 * @property string $bill_time_ended
 * @property integer $is_final
 *
 * The followings are the available model relations:
 * @property Encounter $encounter
 * @property HospitalBillDiscount $discount
 * @property HospitalBillCoverage[] $coverages
 */
class HospitalBill extends CareActiveRecord
{
    
    const HOUSECASE_ACCOMMODATION = 1;

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
            ''
        );
    }

    /**
     * @return array relational rules
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'encounter' => array(self::BELONGS_TO, 'Encounter', 'encounter_nr'),
            'discount' => array(self::HAS_ONE, 'HospitalBillDiscount', 'bill_nr'),
            'coverages' => array(self::HAS_MANY, 'HospitalBillCoverage', 'bill_nr'),
            'billingPF' => array(self::BELONGS_TO , 'BillingPf' , 'bill_nr')
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'bill_nr' => 'Bill No',
            'bill_dte' => 'End Date',
            'bill_frmdte' => 'Start Date',
            'encounter_nr' => 'Encounter No',
        );
    }

    /**
     * {@inheritDoc}
     * @return array
     */
    public function scopes()
    {
        $alias = $this->tableAlias;
        return array(
            'final' => array(
                'condition' => "{$alias}.is_final = 1"
            ),
            'notFinal' => array(
                'condition' => "{$alias}.is_final = 0 OR {$alias}.is_final IS NULL"
            ),
            'deleted' => array(
                'condition' => "{$alias}.is_deleted = '1'"
            ),
            'unDeleted' => array(
               'condition' => "{$alias}.is_deleted = '0' OR {$alias}.is_deleted IS NULL"
               //'condition' => "t.is_deleted IS NULL"
            ),
            'unPaid' => array(
                'condition' => "{$alias}.request_flag IS NULL"
            ),
            'paid' => array(
                'condition' => "{$alias}.request_flag IS NOT NULL"
            ),
            'latest' => array(
                'order' => "{$alias}.bill_dte DESC"
            )
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
        if (isset($this->encounter_nr)) {
            $criteria->addSearchCondition('encounter_nr', $this->encounter_nr.'%', false);
        }
        /*$criteria->compare('bill_nr',$this->bill_nr,true);
        $criteria->compare('bill_dte',$this->bill_dte,true);
        $criteria->compare('bill_frmdte',$this->bill_frmdte,true);
        $criteria->compare('encounter_nr',$this->encounter_nr,true);
        $criteria->compare('accomodation_type',$this->accomodation_type,true);*/
        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HospitalBill the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     *
     * @return float
     */
    public function getBillAmount() {
        //return Yii::app()->numberFormatter->formatCurrency($this->total_med_charge, 'PHP ');

        $areas = array('acc', 'med', 'sup', 'srv', 'ops', 'doc', 'msc');
        $total = 0;
        foreach ($areas as $area) {
            $var = 'total_' . $area . '_charge';
            $total += $this->$var;
        }

        $discount = 0;
        if ($this->discount) {
            $discount = $this->discount->getDiscountedAmount();
        }

        $totalCoverage = 0;
        foreach ($this->coverages as $coverage) {
            $totalCoverage += $coverage->getCoveredAmount();
        }

        return $total - $discount - $totalCoverage;

    }

    /**
     * This will return total amount / gross amount
     * @return array|int|mixed|null
     * @author michelle 03-04-15
     */
    public function getTotalCharges()
    {
        return $this->total_acc_charge + $this->total_med_charge +
        $this->total_sup_charge + $this->total_srv_charge +
        $this->total_ops_charge + $this->total_doc_charge +
        $this->total_msc_charge;
    }

    /**
     * This will return total coverages
     * @return array|int|mixed|null
     * @author michelle 03-04-15
     */
    public function getTotalCoverage()
    {
        $totalCoverage = 0;
        foreach ($this->coverages as $coverage) {
            $totalCoverage += $coverage->getCoveredAmount();
        }
        return $totalCoverage;
    }

    /**
     * This will return total discounts
     * @return array|int|mixed|null
     * @author michelle 03-04-15
     */
    public function getTotalDiscounts()
    {
        $discounts = $this->discount;
        $totalDiscounts = 0;
        $excludedCols = array('bill_nr');
        foreach ($discounts as $k => $discount) {
            if (!in_array($k, $excludedCols)) {
              $totalDiscounts += $discount;
            }
        }
        return $totalDiscounts;
    }

    /**
     * This will return patient seg_bill_encounter item.
     * `bill_nr`, `encounter_nr` are set as params
     *
     * @param $params
     * @return mixed
     * @author michelle 03-04-15
     */
    public function findPatientSaveBillByParams($params)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('t.bill_nr = ' . $params['billNr']);
        $criteria->addCondition('t.encounter_nr = ' . $params['encounterNr']);

        $model = self::model()->final()->unDeleted()->unPaid()->findAll($criteria);
        return $model;
    }

    /**
     * @param array $nr
     * @return mixed
     */
    public function findAllSaveBillByEncr($nr)
    {
        $criteria = new CDbCriteria();
        $criteria->addInCondition("t.encounter_nr", $nr);

        //$model = self::model()->final()->unDeleted()->unPaid()->findAll($criteria);
        $model = self::model()->final()->unDeleted()->findAll($criteria);
        return $model;
    }
    

    public function isHouseCase() 
    {
        return $this->accommodation_type == self::HOUSECASE_ACCOMMODATION;
    }

    public function billHasHouseDoctor($encounter){
        
        $criteria = new CDBCriteria();

        $model = new HospitalBill();
        $houseDoctor = Config::get('house_doctor')->value;

        $criteria->params = array(
            ':encounter' =>  $encounter,
            ':houseDoctor' => $houseDoctor
        );

        $criteria->with = array(
            'billingPF' => array(
                'joinType' => 'INNER JOIN'
            )
        );

        $criteria->addCondition('t.is_final = 1 AND t.encounter_nr = :encounter AND billingPF.dr_nr = :houseDoctor ' ,'AND');
        
        $data = $model->findAll($criteria);

        if (!empty($data))
            return true;
        else
            return false;

    }


}
