<?php
/**
 *
 * EclaimsTransmittalDetails.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2014, Segworks Technologies Corporation
 */

Yii::import('eclaims.models.ClaimAttachment');
Yii::import('eclaims.models.EclaimsEncounter');
Yii::import('eclaims.models.EclaimsTransmittal');
Yii::import('eclaims.models.Eligibility');
Yii::import('phic.models.TransmittalDetails');
Yii::import('application.models.EncounterMemcategory');
Yii::import('phic.models.PhicMember');
header('Content-Type: text/html; charset=utf-8');

/**
 * Description of EclaimsTransmittalDetails
 *
 * @package application.models
 */
class EclaimsTransmittalDetails extends TransmittalDetails
{

    /**
     * @see CActiveRecord::relations.
     */
    public function relations()
    {
        return array_merge(parent::relations(), array(
            'transmittal' => array(self::BELONGS_TO, 'EclaimsTransmittal', 'transmit_no'),
            'eligibility' => array(
                self::HAS_ONE,
                'Eligibility',
                array('encounter_nr' => 'encounter_nr'),
                'through' => 'encounter'
            ),
            'memberType' => array(
                self::HAS_ONE,
                'PhicMember',
                array('encounter_nr' => 'encounter_nr'),
                'through' => 'encounter'
            ),
            'encounter' => array(self::BELONGS_TO, 'EclaimsEncounter', 'encounter_nr'),
            'attachments' => array(
                self::HAS_MANY,
                'ClaimAttachment',
                array(
                    'transmit_no' => 'transmit_no',
                    'encounter_nr' => 'encounter_nr'
                )
            )
        ));
    }

    /**
     * Returns an array of required attachments.
     * Keys of the required attachments, found from
     * ClaimAttachment::getAttachmentTypes().
     *
     * @return Array
     * @author Jolly Caralos
     */
    public function getRequiredAttachments()
    {
        $result = ClaimAttachment::getDefaultRequiredAttachments();
        if (empty($this->billing->caseRate)) {
            return $result;
        }
        foreach ($this->billing->caseRate as $caseRate) {
            $result = CMap::mergeArray(
                ClaimAttachment::getRequiredAttachmentsByCaseType($caseRate->package), $result
            );
        }
        return $result;
    }

    /**
     *
     */
    public function getAttachmentsSummary()
    {
//        $requireAttachments = $this->getRequiredAttachments();
//        CVarDumper::dump($requireAttachments, 10, true);die;
        if ($this->attachments) {
            $output = array();
            foreach ($this->attachments as $attachment) {
                $output[] = CHtml::tag('span', array('class' => 'label label-info'),
                    $attachment->attachment_type);

                /* Remove Existing Attachments */
//                unset($requireAttachments[$attachment->attachment_type]);
            }
        }
//        foreach($requireAttachments as $attachment) {
//            $output[] = CHtml::tag('span', array('class'=>'label label-warning'),
//                $attachment);
//        }
        return implode('&nbsp;', $output);
    }

    /**
     *
     */
    public function searchAttachments()
    {
        $criteria = new CDbCriteria;

        $criteria->addCondition(
            't.transmit_no = :transmit AND t.encounter_nr = :encounter',
            'AND'
        );

        $criteria->condition = '(t.transmit_no = :transmit AND t.encounter_nr = :encounter) 
                           AND (t.is_return = 1  
                           AND (
                              t.is_return IS NULL 
                              OR t.is_return = 0
                            ) 
                            OR t.is_uploaded = 0 
                            OR t.is_uploaded IS NULL
                          )';

        $criteria->params = array(
            ':transmit' => $this->transmit_no,
            ':encounter' => $this->encounter_nr,
        );
        return new CActiveDataProvider('ClaimAttachment', array(
            'criteria' => $criteria
        ));
    }


    public function searchReturned()
    {
        $criteria = new CDbCriteria;
        $criteria->addColumnCondition(
            array(
                'transmit_no' => $this->transmit_no,
                'encounter_nr' => $this->encounter_nr,
                'is_return' => 0,
                'is_uploaded' => 1,
            )
        );
        return new CActiveDataProvider(
            'ClaimAttachment', array(
                'criteria' => $criteria,
            )
        );
    }

    /**
     *
     * @return type
     */
    public function toArray()
    {

        $this->encounter->person->name_middle = mb_convert_case($this->encounter->person->name_middle, MB_CASE_UPPER,
            "UTF-8");
        $this->encounter->person->name_last = mb_convert_case($this->encounter->person->name_last, MB_CASE_UPPER,
            "UTF-8");
        $this->encounter->person->name_first = mb_convert_case($this->encounter->person->name_first, MB_CASE_UPPER,
            "UTF-8");
        $result = array(
            'id' => $this->encounter->encounter_nr,
            'fullName' => $this->encounter->person->getFullName(),
            # Mod by jeff 01-27-18 for fetching right MembershipType.
            'memberType' => $this->memberType->getMemberTypeDesc(),
            // 'memberType' => $this->memberType->memcategory->memcategory_desc,
            // 'sortOrder' =>  $this->memberType->memcategory->memcategory_arr,
            'encounterType' => $this->encounter->type->name,
            'bill' => isset($this->billing->bill_nr) ? $this->billing->bill_nr : null,
            'billDate' => isset($this->billing->bill_dte) ? $this->billing->bill_dte : null,
            // 'amount' => $this->billing->getBillAmount(),
            'amount' => isset($this->billing) ? $this->billing->getTotalCaseRateAmount() : null,
        );

        array_walk($result, function (&$value, $key) {
            if (empty($value)) {
                $value = '';
            }
            $value = strtoupper($value);
        });

        return $result;
    }


    /**
     * function toResponseArray
     *
     * Converts the active record, from the claimsMap response to an array
     *
     * @return array
     */
    public function toResponseArray()
    {

        /* The active record claim is called to retrieve the claim series lhio. */
        $claim = Claim::model()->find(array(
            'condition' => 'encounter_nr=:encounter_nr',
            'params' => array(':encounter_nr' => $this->encounter_nr)
        ));

        /* This active record (transmittaldetails) is called to retrieve specific data. */
        $result = array(
            'claim_no' => $this->encounter_nr,
            'name_full' => $claim->encounter->person->getFullName(),
            'encounterType' => $this->encounter->type->name,
            'admission_dt' => $this->encounter->getAdmissionDt(),
            'discharge_dt' => $this->encounter->discharge_date . " " . $this->encounter->discharge_time,
            'claim_series_lhio' => $claim->claim_series_lhio,
        );

        /* Each string is converted to an upper case string*/
        array_walk($result, function (&$value, $key) {
            $value = (empty($value)) ? '' : mb_strtoupper($value);
        });

        return $result;
    }

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Category the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

}
