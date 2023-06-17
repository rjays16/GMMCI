<?php
namespace SegHis\modules\phic\models\circular\warning;

use SegHis\models\encounter\Encounter;
use SegHis\modules\phic\models\EncounterInsurance;

/**
 * Class BaseBillWarning
 * @package SegHis\modules\phic\models\circular\warning
 * @author Nick B. Alcala 3-2-2016
 */
abstract class BaseBillWarning
{

    protected $warningMessage = 'Warning!';

    /**
     * Naa na diri tanan variables na imong kelangan. Ikaw na tirada unsa rules/validation nimo haha.
     *
     * @param Encounter $encounter The patient's encounter.
     * @param EncounterInsurance $encounterInsurance The insurance used by the patient of the specified encounter.
     * @param \SegHis\models\encounter\Diagnosis[] $diagnosis Array of the diagnosis used by the specified encounter.
     * @param array $billInfo The array of additional information regarding the bill. Example:
     * <code>
     * $billInfo = [
     *     'billDate' => '03/11/2016 5:26 PM',
     *     'encounter_nr' => '2016000001'
     * ];
     * </code>
     * @return bool Returns true whether the validation is passed and no errors, false otherwise.
     */
    public abstract function validate(Encounter $encounter, $encounterInsurance, array $diagnosis, array $billInfo);

    private static function isPatientDead($pid)
    {
        /* manual query nalang kay naay conflict sa Class Names ang class Person sa billing_new.server.php */
        $row = \Yii::app()->db->createCommand("SELECT death_encounter_nr FROM care_person WHERE pid='" . $pid . "'")->queryRow();
        return ($row['death_encounter_nr'] != '0');
    }

    /**
     * Common checks before validation.
     *
     * @param Encounter $encounter
     * @param $encounterInsurance
     * @return bool
     */
    public static function isPatientNeedsValidation(Encounter $encounter, $encounterInsurance)
    {
        /* no warning when patient is dead */
        if (static::isPatientDead($encounter->pid)) {
            return false;
        }

        /* no warning when encounter_type is not in-patient */
        if (!in_array($encounter->encounter_type, array(3, 4))) {
            return false;
        }

        /* no warning when no insurance */
        if (!$encounterInsurance) {
            return false;
        }

        /* no warning when the insurance used is not PhilHealth */
        if ($encounterInsurance->hcare_id != 18) {
            return false;
        }

        return true;
    }

    public function getWarningMessage()
    {
        return $this->warningMessage;
    }
}