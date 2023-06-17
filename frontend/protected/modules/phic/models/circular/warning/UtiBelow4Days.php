<?php
namespace SegHis\modules\phic\models\circular\warning;


use SegHis\models\encounter\Encounter;

/**
 * Class UtiBelow4Days
 * @package SegHis\modules\phic\models\circular\warning
 * @author Nick B. Alcala 3-11-2016
 */
class UtiBelow4Days extends BaseBillWarning
{

    public $warningMessage = 'Confinement with UTI is below 4 days.';

    /**
     * @return bool Returns true whether the validation passed and no errors, false otherwise.
     * @inheritdoc
     */
    public function validate(Encounter $encounter, $encounterInsurance, array $diagnosis, array $billInfo)
    {
        return !(self::hasUti($diagnosis) && self::isBelow4Days($encounter->encounter_date, $billInfo['billDate']));



        // if (static::isPatientNeedsValidation($encounter, $encounterInsurance)) {
        //     return !(self::hasUti($diagnosis) && self::isBelow4Days($encounter->encounter_date, $billInfo['billDate']));
        // }

        // return true;
    }

    /**
     * @param \SegHis\models\encounter\Diagnosis[] $diagnosis
     * @return bool
     */
    public static function hasUti($diagnosis)
    {
        /* @var $diagnosis \SegHis\models\encounter\Diagnosis[] */
        foreach ($diagnosis as $d) {
            if ($d->caseRate) {
                if (in_array(strtoupper($d->caseRate->code), array('N39.0', 'N30.0', 'N10'))) { //edited by Kenneth 04-23-2016
                    return true;
                }
            }
        }
        return false;
    }

    public static function isBelow4Days($encounterDate, $billDate)
    {
        return strtotime($billDate) < strtotime('+4 day', strtotime($encounterDate));
    }

}