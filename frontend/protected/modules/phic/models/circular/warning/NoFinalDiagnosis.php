<?php
namespace SegHis\modules\phic\models\circular\warning;

use SegHis\models\encounter\Encounter;

/**
 * Class NoAdmittingDiagnosis
 * @package SegHis\modules\phic\models\circular\warning
 * @author Carriane Lastimoso 8-17-2017
 */
define("PHIC_ID",18);
class NoFinalDiagnosis extends BaseBillWarning
{

    /**
     * @return bool Returns true whether the validation is passed and no errors, false otherwise.
     * @inheritdoc
     */

    public function validate(Encounter $encounter, $encounterInsurance, array $diagnosis, array $billInfo)
    {

        /* warn if patient has no admitting diagnosis */
        $this->asd=$billInfo['encounter_nr'];
        return !(static::hasNoDiagnosis($billInfo['encounter_nr']) && ($encounterInsurance['hcare_id']==PHIC_ID));

    }

    public function getWarningMessage()
    {
        return 'Patient has no final diagnosis.';
    }

    public static function hasNoDiagnosis($encounter)
    {
       //added by Kenneth 02-13-2018
        $row = \Yii::app()->db->createCommand("SELECT final_diagnosis FROM seg_soa_diagnosis WHERE !(final_diagnosis IS NULL OR final_diagnosis = '') AND encounter_nr = '".$encounter."'")->queryRow(); 
        return !isset($row['final_diagnosis']);
    }

}