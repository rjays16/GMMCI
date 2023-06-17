<?php
namespace SegHis\modules\phic\models\circular\warning;

use SegHis\models\encounter\Diagnosis;
use SegHis\models\encounter\Encounter;
use SegHis\modules\phic\models\EncounterInsurance;

/**
 * Class DiarrheaBelow3Days
 * @package SegHis\modules\phic\models\circular\warning
 * @author Nick B. Alcala 3-2-2016
 */
class DiarrheaBelow3Days extends BaseBillWarning
{

    protected $warningMessage = 'Confinement with Diarrhea is below 3 days.';

    /**
     * @return bool Returns true whether the validation is passed and no errors, false otherwise.
     * @inheritdoc
     */
    public function validate(Encounter $encounter, $encounterInsurance, array $diagnosis, array $billInfo)
    {

        /* no warning when encounter type is not inpatient */
        if (!in_array($encounter->encounter_type, array(1,3,4))) {
            return true;
        }

        /* no warning when no insurance */
        if (!$encounterInsurance && !in_array($encounter->encounter_type, array(1)))
            return true;

        /* no warning when the insurance used is not PhilHealth */
        if ($encounterInsurance->hcare_id != 18 && !in_array($encounter->encounter_type, array(1)))
            return true;

        /* warn if less than 3 days has past and has pneumonia */
        return !(static::isBelow4Days($encounter->encounter_date, $billInfo['billDate']) && static::hasDiarrhea($diagnosis));
    }

    public static function isBelow4Days($encounterDate, $billDate)
    {
        return strtotime($billDate) < strtotime('+3 day', strtotime($encounterDate));
    }

    public static function hasDiarrhea(array $diagnosis)
    {
        /* @var $diagnosis Diagnosis[] */
        foreach ($diagnosis as $d) {
            if ($d->caseRate) {
                $caseRateGroup = $d->caseRate->group;
                if (in_array(strtoupper($caseRateGroup), array(
                    'ACUTE GASTROENTERITIS',
                ))) {
                    return true;
                }
            }
        }
        return false;
    }

}