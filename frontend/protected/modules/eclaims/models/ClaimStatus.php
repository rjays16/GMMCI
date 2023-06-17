<?php

/**
 *
 * @author  Ma. Dulce O. Polinar  <dulcepolinar1010@gmail.com>
 * @copyright (c) 2014, Segworks Technologies Corporation (http://www.segworks.com)
 *
 */

Yii::import('eclaims.models.Claim');
Yii::import('eclaims.models.status.*');

/**
 * This is the model class for table "seg_eclaims_claim_status".
 *
 * The followings are the available columns in table 'seg_eclaims_claim_status':
 * @property integer $id
 * @property integer $claim_id
 * @property string $as_of
 * @property string $as_of_time
 * @property string $status
 * @property string $claim_date_received
 * @property string $claim_date_refile
 *
 */
class ClaimStatus extends CActiveRecord
{
    const STATUS_IN_PROCESS = 'IN PROCESS';
    const STATUS_DENIED = 'DENIED';
    const STATUS_RETURN = 'RETURN';
    const STATUS_WITH_VOUCHER = 'WITH VOUCHER';
    const STATUS_VOUCHERING = 'VOUCHERING';
    const STATUS_WITH_CHEQUE = 'WITH CHEQUE';
    const STATUS_CLAIM_NOT_FOUND = 'CLAIM SERIES NOT FOUND';

    /**
     *
     * @var $details
     */
    protected $details = null;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_eclaims_claim_status';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('claim_id', 'numerical', 'integerOnly'=>true),
            array('status', 'length', 'max'=>60),
            array('as_of, claim_date_received, claim_date_refile', 'safe'),
            array('id, claim_id, as_of, status, claim_date_received, claim_date_refile', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'claim' => array(self::BELONGS_TO, 'Claim', 'claim_id'),
            'denied' => array(self::HAS_ONE, 'DeniedClaimStatus', 'status_id'),
            'return' => array(self::HAS_ONE, 'ReturnClaimStatus', 'status_id'),
            'inProcess' => array(self::HAS_ONE, 'InProcessClaimStatus', 'status_id'),
            'withPayment' => array(self::HAS_ONE, 'WithPaymentClaimStatus', 'status_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'claim_id' => 'Claim ID',
            'as_of' => 'As Of',
            'as_of_time' => 'As Of Time',
            'status' => 'Claim Status',
            'claim_date_received' => 'Claim Date Received',
            'claim_date_refile' => 'Claim Date Refile',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id);
        $criteria->compare('claim_id',$this->claim_id);
        $criteria->compare('as_of',$this->as_of, true);
        $criteria->compare('status',$this->status, true);
        $criteria->compare('claim_date_received',$this->claim_date_received,true);
        $criteria->compare('claim_date_refile',$this->claim_date_refile,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * [getStatus description]
     * @return InProcessClaimStatus|DeniedClaimStatus|ReturnClaimStatus|WithPaymentClaimStatus
     */
    public function getDetails() {
        if (empty($this->details)) {
            switch ($this->status) {
                case self::STATUS_IN_PROCESS:
                    $this->details = $this->inProcess ? $this->inProcess : new InProcessClaimStatus;
                    break;
                case self::STATUS_DENIED:
                    $this->details = $this->denied ? $this->denied : new DeniedClaimStatus;
                    break;
                case self::STATUS_RETURN:
                    $this->details = $this->return ? $this->return : new ReturnClaimStatus;
                    break;
                case self::STATUS_WITH_CHEQUE:
                case self::STATUS_WITH_VOUCHER:
                case self::STATUS_VOUCHERING:
                    $this->details = $this->withPayment ? $this->withPayment : new WithPaymentClaimStatus;
            }

            if (empty($this->details->status_id)) {
                $this->details->status_id = $this->id;
            }
        }

        return $this->details;
    }

    /**
     * [beforeSave description]
     * @return [type] [description]
     */
    public function afterSave() {
        if ($this->status !== self::STATUS_IN_PROCESS && $this->inProcess) {
            $this->inProcess->delete();
        }

        if ($this->status !== self::STATUS_DENIED && $this->denied) {
            $this->denied->delete();
        }

        if ($this->status !== self::STATUS_RETURN && $this->return) {
            $this->return->delete();
        }

        if ($this->status !== self::STATUS_WITH_VOUCHER &&
                $this->status !== self::STATUS_VOUCHERING &&
                $this->status !== self::STATUS_WITH_CHEQUE &&
                $this->withPayment) {
            $this->withPayment->delete();
        }

        $this->getDetails()->status_id = $this->id;
        $this->getDetails()->save();

        return parent::afterSave();
    }

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return ClaimStatus the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

     /**
     * Extracts the information from an array ideally returned from calling
     * the getClaimStatus HIE web service to the appropriate ClaimStatus
     * records.
     *
     * @param array $data The data returned from the Service call response
     * @return void
     *
     * @todo Maybe add some error checking?
     */
    public static function extractResult(array $data) {

        Yii::import('eclaims.components.EclaimsFormatter');
        $formatter = new EclaimsFormatter;

        $claimStatuses = @$data['STATUS']['CLAIM'];

        if (is_array($claimStatuses)) {
            foreach ($claimStatuses as $claimStatus) {
                $attributes = $claimStatus['@attributes'];
                $seriesNo = $attributes['pClaimSeriesLhio'];

                $claim = Claim::model()->findByAttributes(array(
                    'claim_series_lhio' => $seriesNo
                ));

                if ($claim) {
                    if (empty($claim->status)) {
                        $claim->status = new ClaimStatus;
                        $claim->status->claim_id = $claim->id;
                    }

                    $status = $claim->status;

                    $status->as_of = $formatter->formatDbDateTime(@$attributes['pAsOf']. ' ' . @$attributes['pAsOfTime']);
                    $status->status = strtoupper(@$attributes['pStatus']);

                    if($status->status == self::STATUS_CLAIM_NOT_FOUND) {
                        Yii::import('eclaims.services.ServiceCallException');
                        throw new ServiceCallException(403, 'Error: ', array(
                            'reason' => self::STATUS_CLAIM_NOT_FOUND
                        ));
                    }

                    $status->claim_date_received = $formatter->formatDbDate(@$attributes['pClaimDateReceived']);
                    $status->claim_date_refile = $formatter->formatDbDate(@$attributes['pClaimDateRefile']);

                    $details = $status->getDetails();

                    if ($details instanceof InProcessClaimStatus) {
                        $processTrail = array();
                        if (is_array($claimStatus['TRAIL']['PROCESS'])) {
                            foreach ($claimStatus['TRAIL']['PROCESS'] as $process) {
                                $processTrail[] = array(
                                    'stage' => @$process['@attributes']['pProcessStage'],
                                    'date' => $formatter->formatDbDate(@$process['@attributes']['pProcessDate']),
                                );
                            }
                        }
                        $details->process_trail_json = CJSON::encode($processTrail);

                    } elseif ($details instanceof ReturnClaimStatus) {
                        $deficiencies = array();
                        if (is_array($claimStatus['RETURN']['DEFECTS'])) {
                            foreach ($claimStatus['RETURN']['DEFECTS'] as $defect) {
                                $deficiency = array(
                                    'deficiency' => $defect['@attributes']['pDeficiency'],
                                    'requirements' => array()
                                );

                                if (is_array($defect['REQUIREMENT'])) {
                                    foreach ($defect['REQUIREMENT'] as $requirement) {
                                        $deficiency['requirements'][] = @$requirement['@attributes']['pRequirement'];
                                    }
                                }

                                $deficiencies[] = $deficiency;
                            }
                        }
                        $details->deficiencies_json = CJSON::encode($deficiencies);
                    } elseif ($details instanceof DeniedClaimStatus) {
                        $reasons = array();
                        if (is_array($claimStatus['DENIED']['REASON'])) {
                            foreach ($claimStatus['DENIED']['REASON'] as $reason) {
                                $reasons[] = @$reason['@attributes']['pReason'];
                            }
                        }
                        $details->reasons_json = CJSON::encode($reasons);
                    } elseif ($details instanceof WithPaymentClaimStatus) {
                        $payees = array();
                        if (is_array($claimStatus['PAYMENT']['PAYEE'])) {
                            foreach ($claimStatus['PAYMENT']['PAYEE'] as $payee) {
                                $payees[] = array(
                                    'voucherNo' => $payee['@attributes']['pVoucherNo'],
                                    'voucherDate' => $formatter->formatDbDate($payee['@attributes']['pVoucherDate']),
                                    'checkNo' => $payee['@attributes']['pCheckNo'],
                                    'checkDate' => $formatter->formatDbDate($payee['@attributes']['pCheckDate']),
                                    'checkAmount' => $payee['@attributes']['pCheckAmount'],
                                    'claimAmount' => $payee['@attributes']['pClaimAmount'],
                                    'payeeName' => $payee['@attributes']['pClaimPayeeName'],
                                );
                            }
                        }
                        $details->payees_json = CJSON::encode($payees);
                    }

                    $status->save();

                }
            }
        }
    }
}
