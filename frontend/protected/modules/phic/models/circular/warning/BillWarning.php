<?php
namespace SegHis\modules\phic\models\circular\warning;

use SegHis\models\encounter\Diagnosis;
use SegHis\models\encounter\Encounter;
use SegHis\modules\phic\models\EncounterInsurance;

/**
 * Class BillWarning
 * @package SegHis\modules\phic\models\circular\warning
 * @author Nick B. Alcala 3-2-2016
 */
class BillWarning
{

    /**
     * @var BaseBillWarning[]
     */
    public static $warningObjects = array();

    public static function add(BaseBillWarning $warning)
    {
        BillWarning::$warningObjects[] = $warning;
    }

    public static function getWarnings($encounterNr, $billInfo)
    {

        /* @var $encounter Encounter */
        $encounter = Encounter::model()->find(array(
            'condition' => 'encounter_nr = :encounterNr',
            'params' => array(':encounterNr' => $encounterNr),
            'select' => 'pid,encounter_date,encounter_type,er_opd_diagnosis'
        ));

        /* @var $insurance EncounterInsurance */
        $insurance = EncounterInsurance::model()->find(array(
            'condition' => 'encounter_nr = :encounterNr',
            'params' => array(':encounterNr' => $encounterNr)
        ));

        /**
         * Query all diagnosis so that all other subclasses of BaseBillWarning
         * don't have to query specific diagnosis for their validation.
         * @var $diagnosis Diagnosis[]
         */
        $diagnosis = Diagnosis::model()
            ->with('caseRate')
            ->filterByEncounter($encounterNr)
            ->filterActive()
            ->findAll();
        //edited and added by Kenneth 04-29-16
        $withInsurance=true;
        $forDiagnosis = false;
        $warnings = array();
        /* no warning when no insurance */
        $row = \Yii::app()->db->createCommand("SELECT encounter_type FROM care_encounter WHERE encounter_nr='" . $encounterNr . "'")->queryRow(); //edited by Kenneth 04-23-2016
        if(!($row['encounter_type']==3||$row['encounter_type']==4||$row['encounter_type']==1)){ $withInsurance = false;}
        
        if($row['encounter_type'] == 2){ $forDiagnosis = true; }
        

        if (!$insurance && $row['encounter_type']!=1) {
             $withInsurance=false;
        }

        /* no warning when the insurance used is not PhilHealth */
        if ($insurance->hcare_id != 18 && $row['encounter_type']!=1) {
             $withInsurance=false;
        }

        if($withInsurance || $forDiagnosis){
            foreach (BillWarning::$warningObjects as $warning) {
                // pede pud himuon ug properties instead of parameters lang
                
                if($forDiagnosis){
                    $class = explode('\\', get_class($warning));
                    if((end($class) == 'NoAdmittingDiagnosis')||(end($class) == 'NoFinalDiagnosis')){
                        if (!$warning->validate($encounter, $insurance, $diagnosis, $billInfo)) {

                            $warnings[] = $warning->getWarningMessage();
                        }
                    }
                }else{
                    if (!$warning->validate($encounter, $insurance, $diagnosis, $billInfo)) {

                        $warnings[] = $warning->getWarningMessage();
                    }
                }   
            }
        }
        //end Kenneth
        return $warnings;
    }

}