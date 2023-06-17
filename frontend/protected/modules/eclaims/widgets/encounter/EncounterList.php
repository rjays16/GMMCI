<?php
/**
 * Created by PhpStorm.
 * User: ger
 * Date: 17/4/2018
 * Time: 9:15 AM
 */


use SegHis\modules\eclaims\services\encounter\EncounterService;


class EncounterList
{

    public $pid;

    public $gridId;

    public $view = 'eclaims.widgets.encounter.views.list';

    public $encounterNo;

    public $active;

    public $template;


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
        $service = new EncounterService($person, $this->active);

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