<?php

/**
 *
 * @author  Ma. Dulce O. Polinar  <dulcepolinar1010@gmail.com>
 * @copyright (c) 2014, Segworks Technologies Corporation (http://www.segworks.com)
 *
 */

Yii::import('eclaims.models.ClaimVoucherCharge');

/**
 * This is the model class for table "seg_eclaims_voucher".
 *
 * The followings are the available columns in table 'seg_eclaims_voucher':
 * @property integer $id
 * @property string $voucher_no
 * @property string $voucher_date
 *
 */
class ClaimVoucher extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_eclaims_voucher';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('voucher_date', 'safe'),
			array('id, voucher_no, voucher_date', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'voucherCharge' => array(self::HAS_MANY, 'ClaimVoucherCharge', 'voucher_id'),

		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'voucher_no' => 'Voucher No',
			'voucher_date' => 'Voucher Date',
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
		$criteria->compare('voucher_no',$this->voucher_no,true);
		$criteria->compare('voucher_date',$this->voucher_date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Voucher the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * Gets the sum of all fees paid for a specific payee.
	 * @var object baseVoucher Holds the record with the specified voucher No
	 * @var object payeeList holds the list of all payees under a specific voucher (baseVoucher)
	 * @return array Holds all the sum of the fees paid to each payee under the same voucher
	 *
	 *
	 **/
	public function getSummary(){
		$payeeList = ClaimVoucherCharge::model()->findAllByAttributes(
			array('voucher_id'=>$this->id),
			array(
				'select'=>'t.payee_name, t.payee_code',
				'group' =>'t.payee_name, t.payee_code',
				'distinct'=>true,
			)
		);
		$i=0;
		foreach ($payeeList as $payee){
			$payeeInfo[$i]['payee_name'] = $payee['payee_name']."<br><small>".$payee['payee_code']."</small>";;
			$charges = ClaimVoucherCharge::model()->findAllByAttributes(array('voucher_id'=> $this->id,'payee_name'=> $payee['payee_name'], 'payee_code'=>$payee['payee_code']));
			foreach ($charges as $charge) {
				$payeeInfo[$i]['sum']['rmbd'] 		= $payeeInfo[$i]['sum']['rmbd'] + $charge['rmbd'];
				$payeeInfo[$i]['sum']['xray'] 		= $payeeInfo[$i]['sum']['xray'] + $charge['xray'];
				$payeeInfo[$i]['sum']['oprm'] 		= $payeeInfo[$i]['sum']['oprm'] + $charge['oprm'];
				$payeeInfo[$i]['sum']['surfee'] 	= $payeeInfo[$i]['sum']['surfee'] + $charge['surfee'];
				$payeeInfo[$i]['sum']['anesfee'] 	= $payeeInfo[$i]['sum']['anesfee'] + $charge['anesfee'];
				$payeeInfo[$i]['sum']['gpfee'] 		= $payeeInfo[$i]['sum']['gpfee'] + $charge['gpfee'];
				$payeeInfo[$i]['sum']['drugs'] 		= $payeeInfo[$i]['sum']['drugs'] + $charge['drugs'];
				$payeeInfo[$i]['sum']['spfee'] 		= $payeeInfo[$i]['sum']['spfee'] + $charge['spfee'];
				$payeeInfo[$i]['sum']['gross'] 		= $payeeInfo[$i]['sum']['gross'] + $charge['gross_amount'];
				$payeeInfo[$i]['sum']['tax'] 		= $payeeInfo[$i]['sum']['tax'] + $charge['tax_amount'];
				$payeeInfo[$i]['sum']['net'] 		= $payeeInfo[$i]['sum']['net'] + $charge['net_amount'];
			}
            $i++;
		}

		return $payeeInfo;
	}

	/**
     * Extracts the information from an array ideally returned from calling
     * the getVoucherDetails HIE web service.
     *
     * @param string $voucherNo
     * @param array $response Response from calling the HIE web service
     */
    public function extractResult(array $data) {
	    $transaction=Yii::app()->getDb()->beginTransaction();

        // Delete all CHARGES
        $ok = ClaimVoucherCharge::model()->deleteAllByAttributes(array(
            'voucher_id' => $this->id
        ));

        if ($ok && isset($data['CLAIM'])) {
            foreach ($data['VOUCHER']['CLAIM'] as $claim) {
                Yii::import('eclaims.models.Claim');
                $ec = Claim::model()->findByAttributes(array(
                    "claim_series_lhio" => $claim["@attributes"]["pClaimSeriesLhio"]
                ));

                if (!$ec) {
                    $ec = new Claim;
                }

                foreach ($claim['CHARGE'] as $charge) {

                    $charge = new ClaimVoucherCharge;
                    $charge->voucher_id = $this->id;
                    $charge->claim_id = $ec->id;

                    $charge->payee_name = $charge["@attributes"]["pPayeeName"];
                    $charge->payee_type = $charge["@attributes"]["pPayeeType"];
                    $charge->payee_code = $charge["@attributes"]["pPayeeCode"];
                    $charge->rmbd = $charge["@attributes"]["pRMBD"];
                    $charge->drugs = $charge["@attributes"]["pDRUGS"];
                    $charge->xray = $charge["@attributes"]["pXRAY"];
                    $charge->oprm = $charge["@attributes"]["pOPRM"];
                    $charge->sp_fee = $charge["@attributes"]["pSPFee"];
                    $charge->gp_fee = $charge["@attributes"]["pGPFee"];
                    $charge->sur_fee = $charge["@attributes"]["pSURFee"];
                    $charge->anes_fee = $charge["@attributes"]["pANESFee"];
                    $charge->gross_amount = $charge["@attributes"]["pGrossAmount"];
                    $charge->tax_amount = $charge["@attributes"]["pTaxAmount"];
                    $charge->net_amount = $charge["@attributes"]["pNetAmount"];

                    $ok = $charge->save();
                    if (!$ok) {
                        break 2;
                    }
                }
            }
        }

        if ($ok) {
            $transaction->commit();
        } else {
            $transaction->rollback();
        }

        return $ok;
    }

    /**
     * Calls the HIE Web Service when the View voucher details or the View voucher summary link is clicked
     * and no related data exists in the seg_eclaims_voucher_charge
     *
     * @param string voucherNo
     * @return boolean status
     */
    public static function getDetails($voucherNo){
        if (!empty($voucherNo)) {
            $show = self::model()->findByAttributes(array('voucher_no'=>$voucherNo));
            if(empty($show)){

		        Yii::import('eclaims.services.ServiceExecutor');
		        $service = new ServiceExecutor(
		           array(
		               'endpoint' => 'hie/voucher/details',
		               'params' => array(
                           'pVoucherNo' => $voucherNo
                        )
		           )
		        );

		        try {
		            $result = $service->execute();
		            if ($result['success']) {
		                $status = self::extractResult($voucherNo, $result);
		            }
		        } catch (ServiceCallException $e) {
		            Yii::app()->user->setFlash('error', '<strong>Web service error:</strong> ' . $e->getMessage());
		        }
            }
            return $status;
        }
        else{
        	return false;
        }
    }

}
