<?php

Yii::import('application.models.EncounterInsurance');

class EclaimsEncounterInsurance extends EncounterInsurance
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * Creates an Encounter Insurance for PhilHealth
     * Flow:
     * - Finds the InsuranceProvider by InsuranceProvider::INSURANCE_PHIC
     * - If empty return null, else create EncounterInsurance
     * - Return EncounterInsurance
     *
     * @author Jolly Caralos
     */
    public function createEncounterInsurance(Encounter $encounter)
    {
        // Yii::import('eclaims.models.EncounterMemcategory');
        Yii::import('phic.models.PhicMember');

        $insuranceProvider = InsuranceProvider::getProviderByShortFirmId(InsuranceProvider::INSURANCE_PHIC);

        $this->encounter_nr = $encounter->encounter_nr;


        $encounterInsurance = $this->getEncounterInsuranceByProvider($insuranceProvider);

        if (empty($encounterInsurance)) {
            // CVarDumper::dump($encounterInsurance, 10, true); die('asdf');
            $encounterInsurance = new EclaimsEncounterInsurance;
            $encounterInsurance->attributes = array(
                'encounter_nr' => $this->encounter_nr,
                'hcare_id' => $insuranceProvider->hcare_id,
            );


            $encounterInsurance->save();

            #added by monmon
            $phicmember = PhicMember::model()->findByPk($this->encounter_nr);

            // if($phicmember->member_type){
            global $db;
            $memCategory = $db->GetOne('SELECT memcategory_id FROM seg_memcategory WHERE memcategory_code=' . $db->qstr($phicmember->member_type));

            #temporary workaround
            $db->Execute("INSERT INTO seg_encounter_memcategory (encounter_nr,memcategory_id) VALUES ('$this->encounter_nr',$memCategory)");

            #uncomment this if problem was found :(
            /*$encounterMemcategory = new EncounterMemcategory;
            $encounterMemcategory->attributes = array(
                'encounter_nr' => $this->encounter_nr,
                'memcategory_id' => $memCategory
            );
            $encounterMemcategory->save();*/
            // }

        }
        return $encounterInsurance;
    }

    /**
     * @param $encouter Encounter
     * @return Boolean
     * TRUE if there is a deleted record; else FALSE
     * @author Jolly Caralos
     */
    public function removeEncounterInsurance(Encounter $encounter)
    {
        $insuranceProvider = InsuranceProvider::getProviderByShortFirmId(InsuranceProvider::INSURANCE_PHIC);
        $this->encounter_nr = $encounter->encounter_nr;
        $encounterInsurance = $this->getEncounterInsuranceByProvider($insuranceProvider);

        if (!empty($encounterInsurance)) {
            $encounterInsurance->delete();
            return true;
        }
        return false;
    }

    /**
     * @param $encouter Encounter, selected reason, other reason, insurance number.
     * Saving audit trail of removing insurance from eclaims to billing.
     * @author Jeff Ponteras on 01-28-18.
     */
    public function removeReasonInsurance($pid, $session_user, $reasonSelected, $reasonOthers, Encounter $encounter)
    {

        global $db;
        $this->encounter_nr = $encounter->encounter_nr;

        $insurance_nr = $db->GetOne('SELECT insurance_nr FROM seg_encounter_insurance_memberinfo WHERE encounter_nr=' . $db->qstr($this->encounter_nr));

        if (!$insurance_nr || $insurance_nr == NULL || $insurance_nr == '0') {
            $insurance_nr = $db->GetOne('SELECT insurance_nr FROM seg_insurance_member_info WHERE pid=' . $db->qstr($pid));
        }

            if ($reasonSelected == 'Others') {
                $text = "Deleted by " . $session_user . " on " . date("F j, Y, g:i a") . " Other reason:" . $reasonOthers . "\nencounter_nr=" . $db->qstr($this->encounter_nr) . ",pid=" . $db->qstr($pid) . ",reason=" . $db->qstr($reasonSelected) . ",other_reason=" . $db->qstr($reasonOthers) . ",insurance_nr=" . $db->qstr($insurance_nr) . "\n\n";
            } else {
                $text = "Deleted by " . $session_user . " on " . date("F j, Y, g:i a") . " Reason:" . $reasonSelected . "\nencounter_nr=" . $db->qstr($this->encounter_nr) . ",pid=" . $db->qstr($pid) . ",reason=" . $db->qstr($reasonSelected) . ",other_reason=" . $db->qstr($reasonOthers) . ",insurance_nr=" . $db->qstr($insurance_nr) . "\n\n";
            }

                $text = $db->qstr($text);
                $enc = $db->qstr($this->encounter_nr);

                $rmv = $db->Execute("UPDATE seg_encounter_insurance_memberinfo
                                SET history = CONCAT(history,$text)
                                WHERE encounter_nr = $enc");

                if ($rmv) {
                    return true;
                }
                return false;
    }

    /**
     * @param $encouter Encounter, selected reason, other reason, insurance number.
     * Saving audit trail of removing insurance from eclaims to billing.
     * @author Jeff Ponteras on 01-31-18.
     */
    public function InsertReasonInsurance($pid, $session_user, Encounter $encounter)
    {

        global $db;
        $this->encounter_nr = $encounter->encounter_nr;

        $insurance_nr = $db->GetOne('SELECT insurance_nr FROM seg_encounter_insurance_memberinfo WHERE encounter_nr=' . $db->qstr($this->encounter_nr));

        if (!$insurance_nr || $insurance_nr == NULL || $insurance_nr == '0') {
            $insurance_nr = $db->GetOne('SELECT insurance_nr FROM seg_insurance_member_info WHERE pid=' . $db->qstr($pid));
        }

        $text = "Created by " . $session_user . " on " . date("F j, Y, g:i a") . "\nencounter_nr=" . $db->qstr($this->encounter_nr) . ",pid=" . $db->qstr($pid) . ",insurance_nr=" . $db->qstr($insurance_nr) . "\n\n";
        $text = $db->qstr($text);
        $enc = $db->qstr($this->encounter_nr);

        $ins = $db->Execute("UPDATE seg_encounter_insurance_memberinfo
                        SET history = CONCAT(history,$text)
                        WHERE encounter_nr = $enc");

        if ($ins) {
            return true;
        }
        return false;
    }

    /**
     * @param $encouter Encounter.
     * Checking if insurance number is null before adding of insurance to billing.
     * @author Jeff Ponteras on 02-08-18.
     */
    public function CheckInsuranceNotNull(Encounter $encounter)
    {

        $phicmember = PhicMember::model()->findByPk($encounter->encounter_nr);
        if (!$phicmember) {
            return false;
        }
        return $phicmember->hasInsurance();
    }

    /**
     * @param $encouter Encounter.
     * Checking if insurance number is null before adding of insurance to billing.
     * @author Jeff Ponteras on 02-23-18.
     */
    public function CheckEncounterExist($encounter)
    {
        $model = new EncounterInsurance();

        $criteria = new CDBCriteria();

        $criteria->params = array(
            ':encounter_no' => $encounter
        );

        $criteria->addCondition('t.encounter_nr = :encounter_no');


        $data = $model->find($criteria);

        return !empty($data);

    }

    /**
     * @param $encouter Encounter.
     * Saving of insurance info to active encounters.
     * @author Jeff Ponteras on 02-05-18.
     */
    public function UpdateInsuranceInfo($pid, $pin)
    {

        Yii::import('eclaims.models.*');
        // Yii::import('eclaims.models.EclaimsEncounterInsurance');

        $model = new EclaimsPhicMember();

        $criteria = new CDBCriteria();

        $criteria->select = 't.pid';

        $criteria->with = array(
            'encounter',
            'encounter.billingEncounter'
        );
        $criteria->params = array(
            ':pid' => $pid
        );

        $criteria->condition = 't.pid = :pid AND encounter.is_discharged != 1';

        $data = $model->findAll($criteria);

        $arr = array();

        foreach ($data as $key => $value) {

            $phic = $model->findByPk($value['encounter_nr']);

            $phic->patient_pin = $pin;

            $this->checkUpdateInBilling($value['encounter_nr'], $pin);

            if (!$phic->save())
                return false;

        }

    }

    /**
     * @param $encouter Encounter.
     * Saving of insurance info to active encounters.
     * @author Jeff Ponteras on 02-05-18.
     */
    public function checkUpdateInBilling($enc, $pin)
    {

        Yii::import('eclaims.models.*');
        // Yii::import('eclaims.models.EclaimsEncounterInsurance');

        $model = new BillingEncounter();

        $criteria = new CDBCriteria();

        $criteria->with = array(
            'phic'
        );

        $criteria->params = array(
            ':encounter_nr' => $enc
        );

        $criteria->addCondition('t.is_final !=1 AND t.is_deleted != 1 AND t.encounter_nr = :encounter_nr');

        $data = $model->find($criteria);


        if (!empty($data)) {

            $data->phic->patient_pin = $pin;

            $data->phic->save();

            if (!$data->phic->save())
                return false;
        }


    }
}