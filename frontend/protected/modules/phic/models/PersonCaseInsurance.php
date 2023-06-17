<?php
namespace SegHis\modules\phic\models;

use SegHis\modules\person\models\Encounter;

\Yii::import('phic.models.PhicMember');
\Yii::import('phic.models.PhicMember2');

/**
 * Class PersonCaseInsurance
 * TODO add more properties
 * @package SegHis\modules\phic\models
 */
class PersonCaseInsurance
{

    public $encounterNr;

    /**
     * @var $phicMember1 \PhicMember
     */
    public $phicMember1;

    /**
     * @var $phicMember2 \PhicMember2
     */
    public $phicMember2;

    public static function find($encounterNr, $pid)
    {
        $model = new PersonCaseInsurance();
        $model->encounterNr = $encounterNr;

        /* @var $encounter \Encounter */
        $encounter = Encounter::model()->findByPk($encounterNr);
        $model->phicMember1 = \PhicMember::model()->findByPk($encounterNr);
        $model->phicMember2 = \PhicMember2::model()->findByAttributes(array(
            'pid' => $encounter->pid
        ));
        return $model;
    }

    /**
     * @return string|null
     */
    public function getInsuranceNumber()
    {
        if($this->phicMember1)
            return $this->phicMember1->insurance_nr;
        if($this->phicMember2)
            return $this->phicMember2->insurance_nr;
    }

}