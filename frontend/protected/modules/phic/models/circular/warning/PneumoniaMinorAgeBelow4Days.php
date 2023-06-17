<?php
namespace SegHis\modules\phic\models\circular\warning;

/**
 * Class PneumoniaMinorAgeBelow4Days
 * @package SegHis\modules\phic\models\circular
 * @author Nick B. Alcala
 */
class PneumoniaMinorAgeBelow4Days extends PneumoniaAdultAgeBelow4Days
{

    const EFFECTIVE_DATE = '2015-12-07';

    protected $warningMessage = 'Patient with Pneumonia is 19 years old or above';

    public function withPneumoniaBelow4Days($encounter, $billDate, array $diagnosis)
    {
        return (static::isAgeAbove19($encounter->pid) &&
            static::isBelow4Days($encounter->encounter_date, $billDate) &&
            static::isEncounterDateConsidered($encounter->encounter_date) &&
            static::hasPneumonia($diagnosis));//edited by Kenneth 04-23-2016
    }

    public static function isEncounterDateConsidered($encounterDate)
    {
        return strtotime($encounterDate) >= strtotime(static::EFFECTIVE_DATE);
    }

}