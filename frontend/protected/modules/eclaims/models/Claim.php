<?php

/**
 *
 * @author  Ma. Dulce O. Polinar  <dulcepolinar1010@gmail.com>
 * @copyright (c) 2014, Segworks Technologies Corporation
 */

Yii::import('billing.models.CaseRatePackage');
Yii::import('billing.models.EncounterDiagnosis');
Yii::import('billing.models.MiscellaneousOperation');
Yii::import('eclaims.components.EclaimsFormatter');
Yii::import('eclaims.models.ClaimStatus');
Yii::import('eclaims.models.EclaimsEncounter');
Yii::import('eclaims.models.EclaimsPerson');
Yii::import('eclaims.models.EclaimsTransmittal');
Yii::import('eclaims.models.EclaimsTransmittalDetails');
Yii::import('eclaims.models.VoucherCharge');
Yii::import('eclaims.models.CareEncounterDiagnosis');
Yii::import('phic.models.PhicHospitalBill');


/**
 * This is the model class for table "seg_eclaims_claim".
 *
 * The followings are the available columns in table 'seg_eclaims_claim':
 *
 * @property integer $id
 * @property string $claim_series_lhio
 * @property string $encounter_nr
 */
class Claim extends CareActiveRecord {

    protected $formatter;

    /**
     * Initialization routine
     * @return void
     */
    public function init() {
        $this->formatter = new EclaimsFormatter;
    }

    /**
     *
     * @return string the associated database table name
     */
    public function tableName() {
        return 'seg_eclaims_claim';
    }

    /**
     *
     * @return array validation rules for model attributes.
     */
    public function rules() {
            return array(
            array('transmit_no, encounter_nr', 'required'),
            array('transmit_no', 'length', 'max'=>14),
            array('encounter_nr', 'length', 'max'=>12),
            array('claim_series_lhio', 'length', 'max'=>15),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id,transmit_no, encounter_nr, claim_series_lhio', 'safe', 'on'=>'search'),
        );
    }

    /**
     *
     * @return array relational rules.
     */
    public function relations() {
        return array(
            'encounter' => array(self::BELONGS_TO, 'EclaimsEncounter', 'encounter_nr'),
            'transmittal' => array(self::BELONGS_TO, 'EclaimsTransmittal', 'transmit_no'),
            'details' => array(self::BELONGS_TO, 'EclaimsTransmittalDetails', array('encounter_nr', 'transmit_no')),
            'status' => array(self::HAS_ONE, 'ClaimStatus', 'claim_id'),
            'voucherCharge' => array(self::HAS_MANY, 'voucherCharge', 'claim_id'),
            'person' => array(
                self::HAS_ONE,
                'EclaimsPerson',
                array('pid'=>'pid'),
                'through'=>'encounter'
            ),
            // 'attachments' => array(
            // ),
            'billing' => array(
                self::BELONGS_TO,
                'PhicHospitalBill',
                array('encounter_nr' => 'encounter_nr'),
                'on' => '(billing.is_deleted IS NULL OR billing.is_deleted=0) AND billing.is_final=1',
            ),
            'diagnoses' => array(
                self::HAS_MANY,
                'EncounterDiagnosis',
                 array('encounter_nr' => 'encounter_nr'),
                 'condition' => 'diagnoses.is_deleted = 0'
            ),
            'careDiagnoses' => array(
                self::HAS_MANY,
                'CareEncounterDiagnosis',
                array('encounter_nr' => 'encounter_nr'),
                'condition' => 'careDiagnoses.status IS NULL || careDiagnoses.status != "deleted"',
            ),
            'operations' => array(
                self::HAS_MANY,
                'MiscellaneousOperation',
                 array('encounter_nr' => 'encounter_nr'),
            ),
            'attachments' => array(
                self::HAS_MANY,
                'ClaimAttachment',
                array(
                    'transmit_no' => 'transmit_no',
                    'encounter_nr' => 'encounter_nr'
                )
            )
        );
    }

    /**
     *
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'encounter_nr' => 'Encounter No',
            'claim_series_lhio' => 'Claim Series Number',
            'status' => 'Status',
            'transmit_no' => 'Transmit No'
        );
    }

    /**
     *
     * @return
     */
    public function getStatus() {
        if ($this->status) {
            return strtoupper($this->status->status);
        } else {
            return 'PENDING';
        }
    }

    /**
     * Returns the static model of the specified AR class.
     *
     * @param string $className active record class name.
     * @return Claim the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * Creates a new DOMDocument that is preloaded with the DTD validator
     *
     * @return DOMDocument
     */
    protected static function createDocument($xml = null) {

        $impl = new DOMImplementation();
        $dtd = $impl->createDocumentType('CLAIM', '', 'frontend/protected/modules/eclaims/config/eclaims-claim.dtd');
        $document = $impl->createDocument('CLAIM', '', $dtd);

        if (!empty($xml)) {
            // Strip XML version
            $xml = preg_replace('/^<\?.+\?>\s*/s', '', $xml);
            // Strip DOCTYPE
            $xml = preg_replace('/\<\!DOCTYPE.+\>\s+/Us', '', $xml);
            $fragment = $document->createDocumentFragment();
            $fragment->appendXml($xml);
            $document->appendChild($fragment);
        }

        return $document;
    }

    /**
     * Validates a Claim XML through the Phil Health DTD
     *
     * @return array
     */
    public static function validateXml($xml) {
        $document = self::createDocument($xml);
        $validator = new XmlValidator($document);
        $isValid = $validator->validate();
        if (!$isValid) {
           return $validator->errors;
        } else {
            return true;
        }
    }

    /**
     * Retrieves and sorts a list of models based on the current search/filter conditions
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {

        // $criteria=new CDbCriteria;
        // $criteria->with = array('encounter','person', 'status', 'transmittal');
        // $criteria->compare('t.id',$this->id);
        // $criteria->compare('t.transmit_no',$this->transmit_no,true);
        // $criteria->compare('t.claim_series_lhio',$this->claim_series_lhio,true);
        // $criteria->compare('encounter.encounter_nr',$this->encounter_nr,true);
        // $criteria->compare('encounter.discharge_date', $this->encounter->discharge_date, true);
        // $criteria->compare('encounter.admission_dt', $this->encounter->admission_dt, true);
        // $criteria->compare('person.name_last', $this->person->name_last,true);

        // $sort = new CSort();
        // $sort->attributes = array(
        //     'transmittal.transmit_dte'=>array(
        //       'asc'=>'transmittal.transmit_dte',
        //       'desc'=>'transmittal.transmit_dte desc',
        //     ),
        //     'encounter_nr'=>array(
        //       'asc'=>'t.encounter_nr',
        //       'desc'=>'t.encounter_nr desc',
        //     ),
        //     'claim_series_lhio'=>array(
        //         'asc'=>'t.claim_series_lhio',
        //         'desc'=>'t.claim_series_lhio desc',
        //      ),

        //     'encounter.discharge_date'=>array(
        //         'asc'=>'encounter.discharge_date',
        //         'desc'=>'encounter.discharge_date desc',
        //      ),

        //     'encounter.admission_dt'=>array(
        //         'asc'=>'encounter.admission_dt',
        //         'desc'=>'encounter.admission_dt desc',
        //      ),

        //     'person.FullName'=>array(
        //         'asc' => 'person.name_last, person.name_first, person.name_middle',
        //         'desc' => 'person.name_last DESC, person.name_first DESC, person.name_middle DESC',
        //     ),

        //     'person.Sex'=>array(
        //         'asc'=>'person.Sex',
        //         'desc'=>'person.Sex desc',
        //     ),

        //     'Status'=>array(
        //         'asc' => 'status.status',
        //         'desc' => 'status.status',
        //     ),
        // );
        // $sort->defaultOrder = array(
        //     'transmittal.transmit_dte' => CSort::SORT_DESC,
        // );
        // // CVarDumper::dump($criteria); die;

        // return new CActiveDataProvider($this, array(
        //     'criteria'=>$criteria,
        //     'sort'=>$sort
        // ));


        /*added by MARK April 21, 2017*/
         $sql ="SELECT DISTINCT
                      t.id,
                      t.encounter_nr,
                      t.transmit_no,
                      t.claim_series_lhio,
                      IF(status.status IS NULL,'PENDING',status.status) AS STATUS,
                      fn_get_person_name (person.pid) AS name_lasted,
                      transmittal.transmit_dte,
                      IF(encounter.admission_dt IS NULL,encounter.encounter_date,encounter.admission_dt) AS admission_dt,
                      encounter.discharge_date,
                      person.sex,
                      encounter.encounter_type,
                      fn_get_encounter_type_name(encounter.encounter_type) AS typ_enc,
                      CONCAT(sbc.`amount`,'|',scp.`description`,'|',scp.`code`) AS package_data_new,
                     billed_table.bill_dte
                    FROM
                      seg_eclaims_claim t 
                      LEFT JOIN care_encounter encounter 
                        ON (
                          t.encounter_nr = encounter.encounter_nr
                        ) 
                      LEFT JOIN care_person person 
                        ON (
                          encounter.pid = person.pid
                        ) 
                      LEFT JOIN seg_eclaims_claim_status status 
                        ON (status.claim_id = t.id) 
                      LEFT JOIN seg_transmittal transmittal 
                        ON (
                          t.transmit_no = transmittal.transmit_no
                        )
                     INNER JOIN `seg_billing_encounter` billed_table 
                        ON (
                          billed_table.encounter_nr = encounter.encounter_nr
                         
                        )
                    INNER JOIN  seg_billing_caserate sbc
                        ON(
                        billed_table.bill_nr = sbc.bill_nr
                    )
                    LEFT JOIN seg_case_rate_packages  scp
                         ON (scp.code = sbc.package_id
                    )

                    
                    WHERE (NOT ISNULL(transmittal.transmit_dte) AND encounter.is_discharged = '1'  AND billed_table.is_final='1' AND NOT ISNULL(t.claim_series_lhio))
                    GROUP BY t.encounter_nr
                        ORDER BY transmittal.`transmit_dte` ASC";
         // die($sql);
        
          $res = Yii::app()->db->createCommand($sql)->queryAll();
          return  $dataProvider=new CArrayDataProvider($res, array('keyField' => 'id',
                                                         'id'=>'user',
                                                            'sort'=>array(
                                                                'attributes'=>array(
                                                                     'encounter_nr', 'transmit_no', 'claim_id','claim_series_lhio','bill_dte','name_lasted',
            ),
             ),
                                                            'pagination'=>array(
                                                                'pageSize'=>10,
                                                            )));
    }

/*added by MARK April 21, 2017*/
     public function searchNews(){
         
           $data_eClaims = \Yii::app()->db->createCommand()
                ->select("DISTINCT t.id,
                          t.encounter_nr,
                          t.transmit_no,
                          t.claim_series_lhio,
                          IF(status.status IS NULL,'PENDING',status.status) AS STATUS,
                          fn_get_person_name (person.pid) AS name_lasted,
                          transmittal.transmit_dte,
                          encounter.admission_dt,
                          encounter.discharge_date,
                          person.sex,
                          person.name_last,
                          encounter.encounter_type,
                          fn_get_encounter_type_name(encounter.encounter_type) AS typ_enc,
                          CONCAT(sbc.`amount`,'|',scp.`description`,'|',scp.`code`) AS package_data_new,
                          billed_table.bill_dte")
                ->from("seg_eclaims_claim t 
                          LEFT JOIN care_encounter encounter 
                            ON (
                              t.encounter_nr = encounter.encounter_nr
                            ) 
                          LEFT JOIN care_person person 
                            ON (
                              encounter.pid = person.pid
                            ) 
                          LEFT JOIN seg_eclaims_claim_status status 
                            ON (status.claim_id = t.id) 
                          LEFT JOIN seg_transmittal transmittal 
                            ON (
                              t.transmit_no = transmittal.transmit_no
                            )
                         INNER JOIN `seg_billing_encounter` billed_table 
                            ON (
                              billed_table.encounter_nr = encounter.encounter_nr
                             
                            )
                        INNER JOIN  seg_billing_caserate sbc
                            ON(
                            billed_table.bill_nr = sbc.bill_nr
                        )
                        LEFT JOIN seg_case_rate_packages  scp
                             ON (scp.code = sbc.package_id
                        )");
                $data_eClaims->andWhere("encounter.is_discharged=:is_discharged",array(':is_discharged' => '1'));
                $data_eClaims->andWhere("billed_table.is_final=:is_final",array(':is_final' => '1'));

           if (!empty($_REQUEST['encounter_nr_new_data'])) {
                $data_eClaims->andWhere("encounter.encounter_nr= :encounter_nr", array(':encounter_nr' => $_REQUEST['encounter_nr_new_data']));              
           }
            if (!empty($_REQUEST['transmit_no_new_data'])) {
                $data_eClaims->andWhere("t.transmit_no LIKE :transmit_no", array(':transmit_no' => $_REQUEST['transmit_no_new_data']));              
           }
            if (!empty($_REQUEST['Transmittal_date'])) {
                $new_date = date("Y-m-d", strtotime($_REQUEST['Transmittal_date']));
                $data_eClaims->andWhere("DATE(transmittal.transmit_dte) BETWEEN '$new_date' AND '$new_date'");              
           }
           if (!empty($_REQUEST['claim_series_lhio'])) {
                 $data_eClaims->andWhere("t.claim_series_lhio= :claim_series_lhio", array(':claim_series_lhio' => $_REQUEST['claim_series_lhio']));              
           }
           if (!empty($_REQUEST['patient_lastname'])) {
                 $data_eClaims->andWhere("person.name_last LIKE :name_last", array(':name_last' => trim($_REQUEST['patient_lastname'])));              
           }
           if (!empty($_REQUEST['admission_date'])) {
                $new_date = date("Y-m-d", strtotime($_REQUEST['admission_date']));
                $data_eClaims->andWhere("DATE(billed_table.bill_dte) BETWEEN '$new_date' AND '$new_date'");
            }
            if (!empty($_REQUEST['discharge_date'])) {
                $new_date = date("Y-m-d", strtotime($_REQUEST['discharge_date']));
                $data_eClaims->andWhere("DATE(encounter.discharge_date) BETWEEN '$new_date' AND '$new_date'");
            }
            #added by monmon : filter for status
            if(!empty($_REQUEST['status'])){
              $status = $_REQUEST['status'];
              if($status == 'PENDING')
                  $data_eClaims->andWhere("ISNULL(status.status)");
              else
                  $data_eClaims->andWhere("status.status = '$status'");

            }

            // $r = $data_eClaims->GetText();
            // die($r);
           $res = $data_eClaims->queryAll();
          return    $dataProvider=new CArrayDataProvider($res, array('keyField' => 'id',
                                                         'id'=>'user',
                                                            'sort'=>array(
                                                                'attributes'=>array(
                                                                     'encounter_nr', 'transmit_no', 'claim_id','claim_series_lhio','discharge_date','name_lasted',
            ),
            ),
                                                            'pagination'=>array(
                                                                'pageSize'=>10,
                                                            )));
    }

    private function getHospitalCode(){
        Yii::import('eclaims.models.HospitalConfigForm');
        $configModel = new HospitalConfigForm;
        $hospitalCode = $configModel->hospital_code;
        return $hospitalCode;
    }
    
/*ENd added by MARK April 21, 2017*/
    /**
     * 
     * @author Jolly Caralos
     * @return Array
     */
    public function compactClaimStatus(Claim $claim = null) 
    {   
        $claim = (empty($claim)) ? $this : $claim;
        #workaround : added static hospital code
        // $insurance = new InsuranceProvider;
        // $accreditationNo = empty(InsuranceProvider::model()->findByPk($claim->transmittal->hcare_id)->accreditation_no) ?
        //     '941733' : InsuranceProvider::model()->findByPk($claim->transmittal->hcare_id)->accreditation_no;
        #edited by VAS 01/29/2018
        $accreditationNo = self::getHospitalCode();
            
        $result = array(
            'pUserName' => '',
            'pPassword' => '',
            'pHospitalCode' => $accreditationNo,
            'pSeriesLhioNos' => $claim->claim_series_lhio
        );

        return $result;
    }

    /**
     * Get claim data by Encounter
     * @param $encounter_nr
     * @return bool
     */
    public function getClaimByEncounterNo($encounter_nr)
    {
        $criteria = new \CDbCriteria();

        $criteria->addCondition('t.encounter_nr = :encounter_nr');
        $criteria->params = array(
            ':encounter_nr' => $encounter_nr
        );

        return self::find($criteria) ?: null;
    }
}


/**
 *
 */
class XmlValidator {

    private $_delegate;
    private $_validationErrors;

    /**
     * [__construct description]
     * @param DOMDocument $document [description]
     */
    public function __construct (DOMDocument $document) {
        $this->_delegate = $document;
        $this->_validationErrors = array();
    }

    /**
     * [__call description]
     * @param  [type] $method [description]
     * @param  [type] $args   [description]
     * @return [type]         [description]
     */
    public function __call ($method, $args) {
        if ($method == "validate") {
            $prevHandler = set_error_handler(array($this, "onValidateError"));
            $rv = $this->_delegate->validate();

            // restore error handler
            set_error_handler($prevHandler);
            return $rv;
        }
        else {
            return call_user_func_array(array($this->_delegate, $method), $args);
        }
    }

    /**
     * [__get description]
     * @param  [type] $var [description]
     * @return [type]      [description]
     */
    public function __get ($var) {
        if ($var == "errors") {
            return $this->_validationErrors;
        }
        else {
            return $this->_delegate->$var;
        }
    }

    /**
     * [__set description]
     * @param [type] $var   [description]
     * @param [type] $value [description]
     */
    public function __set ($var, $value) {
        $this->_delegate->$var = $value;
    }

    /**
     * [onValidateError description]
     * @param  [type] $pNo      [description]
     * @param  [type] $pString  [description]
     * @param  [type] $pFile    [description]
     * @param  [type] $pLine    [description]
     * @param  [type] $pContext [description]
     * @return [type]           [description]
     */
    public function onValidateError ($pNo, $pString, $pFile = null, $pLine = null, $pContext = null) {
        /**
         * Parse PHP errors by trimming everything before the "]:" token
         */
        $this->_validationErrors[] = preg_replace("/^.*\]: /", "", $pString, 1);
    }
}