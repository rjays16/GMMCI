<?php
/**
 * Created by PhpStorm.
 * User: SEGWORKS-BENDER
 * Date: 6/26/2018
 * Time: 2:43 AM
 */

namespace SegHis\modules\eclaims\services\claims\transmittal;

use EclaimsTransmittalDetails;
use Yii;

class TransmittalService
{

    public $transmitDetails;

    public function __construct(EclaimsTransmittalDetails $transmitDetails)
    {

        $this->transmitDetails = $transmitDetails;
    }

    public function checkReturn()
    {
        Yii::import('eclaims.models.Claim');

        $claim = new \Claim;

        $criteria = new \CDbCriteria();

        $criteria->with = array(
            'status',
            'status.return' => array('joinType' => 'INNER JOIN'),
        );


        $criteria->params = array(
            ':encounter_nr' => $this->transmitDetails->encounter_nr,
            ':transmit_no' => $this->transmitDetails->transmit_no,
        );

        $criteria->condition = 't.transmit_no = :transmit_no AND t.encounter_nr = :encounter_nr';

        $data = $claim->find($criteria);
        return !empty($data);
    }


    public function hasReturnedAttachment()
    {
        Yii::import('eclaims.models.ClaimAttachment');

        $attachment = new \ClaimAttachment();

        $criteria = new \CDbCriteria();

        $criteria->params = array(
            ':encounter_nr' => $this->transmitDetails->encounter_nr,
            ':transmit_no' => $this->transmitDetails->transmit_no,
            ':return' => 0,
            ':uploaded' => 1,
        );
        $criteria->addCondition(
            't.transmit_no = :transmit_no AND t.encounter_nr = :encounter_nr AND t.is_return = :return AND t.is_uploaded = :uploaded',
            'AND'
        );

        $data = $attachment->find($criteria);
        return !empty($data);
    }

}