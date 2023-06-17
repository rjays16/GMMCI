<?php
/**
 * Created by PhpStorm.
 * User: ger
 * Date: 8/4/2018
 * Time: 10:10 PM
 */

use SegHis\modules\eclaims\services\encounter\EncounterService;

class EncounterList
{

    public $pid;

    public $gridId;

    public $view = 'eclaims.views.common.encounter.list';

    public $encounterNo;

    public $active;

    public $template;

    public $phic = false;


    public function init()
    {

        if (empty($this->pid)) {
            return new CException('Pid is Required');
        }

    }

    public function run()
    {

        $person = EclaimsPerson::model()->findByPk($this->pid);

        if (empty($person)) {
            $person = new EclaimsPerson();
        }


        $service = new EncounterService($person, $this->active , $this->phic);

        $dataProvider = $service->displayEncounters();

        if (!empty($this->encounterNo)) {

            $service->encounterNo = $this->encounterNo;
            $dataProvider = $service->displayEncounters();
        }

        Yii::app()->getController()->renderPartial($this->view,
            array(
                'encounter' => $this->encounterNo,
                'service' => $service,
                'template' => $this->template,
                'model' => new Encounter()
            )
        );
    }

}