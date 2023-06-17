<?php
/**
 * Created by PhpStorm.
 * User: ger
 * Date: 8/4/2018
 * Time: 11:18 PM
 */

namespace SegHis\modules\eclaims\services\encounter;

use EclaimsPerson;
use Encounter;
use CDBCriteria;
use CActiveDataProvider;
use SegHis\modules\phic\models\EncounterInsurance;

class EncounterService
{

    public $person;

    public $active;

    public $encounterNo;

    public $phic;

    public function __construct(EclaimsPerson $person, $active = true, $phic)
    {

        $this->person = $person;

        $this->active = $active;

        $this->phic = $phic;

    }

    public function displayEncounters()
    {

        $model = new \EclaimsEncounter();

        $criteria = new CDBCriteria();


        $params = array(
            ':pid' => $this->person->pid
        );

        $criteria->addCondition('t.pid = :pid ', 'AND');

        if ($this->active) {
            $criteria->addCondition('t.is_discharged != 1', 'AND');
        }

        if ($this->phic) {

            $criteria->with = array(
                'phicMember' => array(
                    'joinType' => 'INNER JOIN'
                ),
                'encounterInsurance' => array(
                    'joinType' => 'INNER JOIN'
                )
            );

            $params[':hcare'] = 18;
            $criteria->addCondition('phicMember.hcare_id = :hcare', 'AND');
        }


        if (!empty($this->encounterNo)) {
            $params[':encounter'] = $this->encounterNo;
            $criteria->addCondition('t.encounter_nr = :encounter', 'AND');
        }


        $criteria->params = $params;

        $criteria->order = 't.encounter_date DESC';

        return new CActiveDataProvider($model, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 5,
            )
        ));
    }

    public function CheckEncounterExist($encounter)
    {
        $model = new EncounterInsurance();

        $criteria = new CDBCriteria();

        $criteria->with = array(
            'encounter.type'
        );

        $criteria->select = 't.encounter_nr';

        $criteria->params = array(
            ':encounter_no' => $encounter
        );

        $criteria->addCondition('t.encounter_nr = :encounter_no');

        $data = $model->find($criteria);

        return !empty($data);

    }


}