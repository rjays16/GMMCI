<?php
namespace SegHis\modules\phic\models\circular\warning;

use SegHis\models\encounter\Diagnosis;
use SegHis\models\encounter\Encounter;

/**
 * Class PneumoniaBelow4Days
 * @package SegHis\modules\phic\models\circular\warning
 * @author Nick B. Alcala 3-2-2016
 */
class PneumoniaAdultAgeBelow4Days extends BaseBillWarning
{

    const EFFECTIVE_DATE = '2015-09-15';
    const EFFECTIVE_DATE2 = '2015-12-07';

    protected $warningMessage = 'Confinement with pneumonia is below 4 days.';

    /**
     * @return bool Returns true whether the validation is passed and no errors, false otherwise.
     * @inheritdoc
     */
    public function validate(Encounter $encounter, $encounterInsurance, array $diagnosis, array $billInfo)
    {
        if (static::isPatientNeedsValidation($encounter, $encounterInsurance)) {
            return !($this->withPneumoniaBelow4Days($encounter, $billInfo['billDate'], $diagnosis));
        }

        return true;
    }

    /**
     * @param Encounter $encounter
     * @param string $billDate
     * @param array $diagnosis
     * @return bool
     */
    public function withPneumoniaBelow4Days($encounter, $billDate, array $diagnosis)
    {
        return static::isBelow4Days($encounter->encounter_date, $billDate) &&
        static::hasPneumonia($diagnosis) &&
        (
            //added by kenneth 04-29-16
            (static::isEncounterDateConsidered($billDate) &&
            static::isAgeAbove19($encounter->pid))
            ||
            (static::isEncounterDateConsidered2($billDate) &&
            !static::isAgeAbove19($encounter->pid))
            //end kenneth
        );
    }
    public static function isAgeAbove19($pid)
    {
            $row = \Yii::app()->db->createCommand("SELECT fn_calculate_age(cp.`date_birth`,NOW())/2 as age FROM care_person AS cp WHERE cp.pid='" . $pid . "'")->queryRow(); //edited by Kenneth 04-23-2016
            return $row['age']>=19;
    }

    public static function isBelow4Days($encounterDate, $billDate)
    {
        return strtotime($billDate) < strtotime('+4 day', strtotime($encounterDate));
    }

    public static function isEncounterDateConsidered($encounterDate)
    {
        return strtotime($encounterDate) >= strtotime(static::EFFECTIVE_DATE);
    }

    public static function isEncounterDateConsidered2($encounterDate)
    {
        return strtotime($encounterDate) >= strtotime(static::EFFECTIVE_DATE2);
    }

    public static function hasPneumonia(array $diagnosis)
    {
        /* @var $diagnosis Diagnosis[] */
        foreach ($diagnosis as $d) {
            if ($d->caseRate) {
                if (in_array(strtoupper($d->caseRate->code), array('J18.92', 'J18.93'))) {
                    return true;
                }
            }
        }
        return false;
    }

}