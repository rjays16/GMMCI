<?php

/**
 *
 * EclaimsEncounter.php
 *
 * @author        Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2014, Segworks Technologies Corporation
 */

Yii::import('eclaims.models.EclaimsPerson');
Yii::import('eclaims.models.Eligibility');
Yii::import('eclaims.models.EclaimsPhicMember');
Yii::import('eclaims.models.EclaimsPhicMember2');
Yii::import('billing.models.HospitalBill');

/**
 * Description of EclaimsEncounter
 *
 * @package eclaims.models
 */
class EclaimsEncounter extends Encounter
{

    const RECOVERED = 5;

    const DISCHARGED = 7;

    /**
     *
     */
    public function relations()
    {
        return array_merge(
            parent::relations(),
            array(
                'eligibility' => array(
                    self::HAS_ONE, 'Eligibility', 'encounter_nr',
                ),
                'person'      => array(
                    self::BELONGS_TO, 'EclaimsPerson', 'pid',
                ),
                'phicMember'  => array(
                    self::BELONGS_TO, 'EclaimsPhicMember', 'encounter_nr',
                ),
                'phicMember2' => array(
                    self::BELONGS_TO, 'EclaimsPhicMember2', 'encounter_nr',
                ),
                'bill'        => array(
                    self::HAS_ONE, 'HospitalBill', 'encounter_nr',
                    'order' => 'bill.bill_dte DESC',
                ),
                'finalBill'   => array(
                    self::HAS_ONE, 'HospitalBill', 'encounter_nr',
                    'scopes' => array(
                        'final', 'unDeleted', 'latest',
                    ),
                ),

                'encounterInsurance' => array(
                    self::HAS_ONE, 'EncounterInsurance', 'encounter_nr',
                ),
            )
        );
    }

    public function scopes()
    {
        $alias = $this->tableAlias;

        return array_merge(
            parent::scopes(), array(
            'notBilled' => array(
                'with' => array(
                    'bill' => array(
                        'scopes' => 'notFinal',
                    ),
                ),
            ),
        )
        );
    }

    /**
     * Returns the type of patient (inpatient | )
     *
     * @return string
     */
    public function getPatientType()
    {
        if ($this->type) {
            switch ($this->type->type_nr) {
            case 3:
            case 4:
                return 'I';
                break;

            default:
                return 'O';
            }
        } else {
            return '';
        }
    }

    /**
     * Returns the type of patient (inpatient | )
     *
     * @return string
     */
    public function getEmergencyStatus()
    {
        if ($this->type) {
            switch ($this->type->type_nr) {
            case 1:
                return 'Y';
                break;

            default:
                return 'N';
            }
        } else {
            return '';
        }
    }

    // /**
    //  * Returns the number of days the patient was confined in the hospital
    //  *
    //  * @return string
    //  */
    // public function getDaysConfined() {
    //     if ($this->admission_dt && $this->discharge_date && $this->discharge_time) {
    //         $admission = new DateTime($this->admission_dt));
    //         $discharge = new DateTime($this->discharge_date . ' ' . $this_discharge_time));
    //         $diff = $admission->diff($discharge);
    //         if ($diff->d) {
    //             return $diff->d . ' ';
    //         } else {
    //             return 'error';
    //         }
    //     } else {

    //         //ask sir alvin what if NULL sila
    //         return '';
    //     }
    // }

    /**
     *
     * Get Encounter Disposition | Mod by jeff 03-16-18
     *
     * @author Alvin Jay Cosare
     * @return string dispo
     *
     */
    public function getDispositionCode()
    {

        $codes = $this->getRecoveredCode();
        $resultCodes = explode(',', $codes);
        $result = $this->result->result_code;
        $subResult = $this->disposition->disp_code;

        if ($result == $resultCodes[0] && $subResult == $resultCodes[1]) {
            $this->disposition->disp_code = $resultCodes[2];
        }

        if ($this->person->death_date == self::EMPTY_DATE) {
            switch ($this->disposition->disp_code) {

            case 4:
            case 9:
                // HAMA/DAMA
                return 'H';
                break;

            case 8:
                // Transferrred
                return 'T';
                break;

            case 5:
            case 10:
                // Absconded
                return 'A';
                break;

            case 11:
                // Recovered
                return 'R';
                break;

            case 6:
            default:
                return 'I';

            }
        } else {
            // Expired
            return 'E';
        }


    }

    /**
     * @author Jolly Caralos
     */
    public function hasBilledDt()
    {
        if (empty($this->bill->bill_dte)) {
            return false;
        }

        return true;
    }

    /**
     * @author Jolly Caralos
     */
    private function hasDischarge_dt()
    {
        if (empty($this->discharge_date)
            || $this->discharge_date == self::EMPTY_DATE
        ) {
            return false;
        }

        return true;
    }

    /**
     * @author Jolly Caralos
     */
    public function getDischarge_dt()
    {
        if (!$this->hasDischarge_dt()) {
            return null;
        }

        return $this->discharge_date . ' ' . $this->discharge_time;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your
     * CActiveRecord descendants!
     *
     * @param string $className active record class name.
     *
     * @return Person the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function attributeLabels()
    {
        $_labels = array(
            'discharge_dt' => 'Discharge Date',
        );

        return CMap::mergeArray(parent::attributeLabels(), $_labels);
    }

    public function getEncounterInsurance()
    {
        Yii::import('eclaims.models.EclaimsEncounterInsurance');
        $insuranceProvider = InsuranceProvider::getProviderByShortFirmId(
            InsuranceProvider::INSURANCE_PHIC
        );
        $encounterInsurance = new EclaimsEncounterInsurance;
        $encounterInsurance->encounter_nr = $this->encounter_nr;

        return $encounterInsurance->getEncounterInsuranceByProvider(
            $insuranceProvider
        );


    }

    /**
     * Prioritize admission_dt, if empty, use encounter_date
     *
     * @author Jolly Caralos
     */
    public function getAdmissionDt()
    {
        if (empty($this->admission_dt)) {
            return $this->encounter_date;
        } else {
            return $this->admission_dt;
        }
    }

    public function getDischargeDt()
    {

        if ($this->is_expired) {

            $death = $this->person->death_date . ' '
                . $this->person->death_time;

            $date = DateTime::createFromFormat('Y-m-d H:i:s', $death);

            return $date->format('Y-m-d H:i:s');

        }
        if ($this->finalBill->bill_dte) {

            return $this->finalBill->bill_dte;

        } else {

            if (!$this->hasDischarge_dt()) {
                return date('Y-m-d H:i:s');
            }

            return $this->discharge_date . ' ' . $this->discharge_time;
        }

    }

    /**
     * Select values from global config for dynamic values
     *
     * @author Jeff Ponteras 03-16-18
     * @return int Codes
     */
    public function getRecoveredCode()
    {
        include_once($root_path
            . 'include/care_api_classes/class_globalconfig.php');
        $obj_global = new GlobalConfig($this);
        $codesResult = $obj_global->getRecoveredResult();

        return $codesResult;
    }

    public function getDischargeDate()
    {
        $dischargeDt = $this->getDischarge_dt();


        if (!empty($this->finalBill)) {
            $dischargeDt = $this->finalBill->bill_dte;
        }

        return $dischargeDt;
    }

    public function pullDischargeDate()
    {
        return $this->discharge_date;
    }

    public function pullDischargeTime()
    {
        return $this->discharge_time;
    }

}
