<?php
namespace SegHis\modules\phic\models\circular\warning;

use SegHis\models\encounter\Encounter;

/**
 * Class PhilHealthBelow24Hours
 * @package SegHis\modules\phic\models\circular\warning
 * @author Nick B. Alcala 3-2-2016
 */
class PhilHealthBelow24Hours extends BaseBillWarning
{

    /**
     * @return bool Returns true whether the validation is passed and no errors, false otherwise.
     * @inheritdoc
     */
    public function validate(Encounter $encounter, $encounterInsurance, array $diagnosis, array $billInfo)
    {

        /* no warning when no insurance */
        if (!$encounterInsurance && $encounter->encounter_type!=1)
            return true;

        /* no warning when the insurance used is not PhilHealth */
        if ($encounterInsurance->hcare_id != 18 && $encounter->encounter_type!=1)
            return true;

        /* warn if less than 24 hours has past */
        //var_dump($encounter->encounter_type);die();
        return !(static::isBelow24Hours($encounter->encounter_date, $billInfo['billDate']) && static::isInpatient($encounter->encounter_type));

    }

    public function getWarningMessage()
    {
        return 'Confinement is below 24 hours.';
    }

    /**
     * Returns true if less than 24 hours have past.
     * @param $encounterDate
     * @param $billDate
     * @return bool
     */
    public static function isBelow24Hours($encounterDate, $billDate)
    {
        return strtotime($billDate) < strtotime('+1 day', strtotime($encounterDate));
    }

    public static function isInpatient($encountertype)
    {
        return $encountertype=='3'||$encountertype=='4'||$encountertype=='1';
    }

}