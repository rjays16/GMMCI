<?php

Yii::import('eclaims.models.cf4.Cf4');


class CF4HeaderService
{
    public $encounter_nr;
    public $pid;
    public $model;
    public function __construct($encounter_nr, $pid)
    {
        $this->encounter_nr = $encounter_nr;
        $this->pid = $pid;
        $this->model = Cf4::model()->find(array(
            'condition' => 't.encounter_nr = :encounter_nr',
            'params' => array(
                ':encounter_nr' => $this->encounter_nr
            )
        ));
    }

    public function save()
    {
        if (count($this->model) == 0) {
            $this->model = new Cf4;
            $this->model->id = $this->model->getUuid();
            $this->model->entry_date =  date('Y-m-d h:m:s');
            $this->model->pid = $this->pid;
            $this->model->encounter_nr = $this->encounter_nr;
            $this->model->created_at = date('Y-m-d h:m:s');
            $ok = $this->model->save();
        }
    }

    public function getId()
    {
        return $this->model->id;
    }
}
