<?php
namespace SegHis\modules\phic\models\circular\warning;

use SegHis\models\encounter\Encounter;

/**
 * Class NoAdmittingDiagnosis
 * @package SegHis\modules\phic\models\circular\warning
 * @author Carriane Lastimoso 8-17-2017
 */
class NoAdmittingDiagnosis extends BaseBillWarning
{

    /**
     * @return bool Returns true whether the validation is passed and no errors, false otherwise.
     * @inheritdoc
     */
    public function validate(Encounter $encounter, $encounterInsurance, array $diagnosis, array $billInfo)
    {

        /* warn if patient has no admitting diagnosis */
        return !(static::hasNoDiagnosis($encounter->er_opd_diagnosis,$encounterInsurance));

    }

    public function getWarningMessage()
    {
        return 'Patient has no admitting diagnosis.';
    }

    public static function hasNoDiagnosis($admittingdiagnosis,$encounterInsurance)
    {
        return ($admittingdiagnosis == "" || $admittingdiagnosis == null)&&(isset($encounterInsurance)&&($encounterInsurance->hcare_id == 18));
    }

}