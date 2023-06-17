<?php
/**
 * CourseWardService.php
 *
 * @author Jan Chris S. Ogel <iamjc93@gmail.com>
 * @copyright (c) 2019, Segworks Technologies Corporation (http://www.segworks.com)
 */

Yii::import('eclaims.models.cf4.Cf4CourseInTheWard');
Yii::import('eclaims.models.cf4.Cf4');

class CourseWardService
{
    public $encounter_nr;

    public $cf4;

    public function __construct($encounter_nr)
    {
        $this->encounter_nr = $encounter_nr;
        $this->getCf4();
    }

    public function getCf4()
    {
        $model = \Cf4::model()->findByAttributes(array(
            'encounter_nr' => $this->encounter_nr
        ));

        if (empty($model))
            $model = new Cf4();

        $this->cf4 = $model;

    }

    /**
     * @return CActiveRecord|Cf4CourseInTheWard
     */
    public function getData()
    {
        $model = Cf4CourseInTheWard::model()->findAllByAttributes(array(
            'entry_id' => $this->cf4->id,
            'encounter_nr' => $this->encounter_nr
        ));

        if (empty($model))
            $model = new Cf4CourseInTheWard();

        return $model;
    }

    /**
     * @param $data
     * @return bool
     * @throws CException
     */
    public function saveCourseWard($data)
    {
        $date = date('Y-m-d');
        $time = date(' H:i:s');
        $model = new Cf4CourseInTheWard;

        $entry_id = $this->getEntryId($data);

        $model->id = $model->getUuid();
        $model->entry_id = $entry_id;
        $model->encounter_nr = $this->encounter_nr;
        $model->date_action = $data['date_action'] . $time;
        $model->doctor_action = $data['doctor_action'];
        $model->modify = $date . $time;
        $model->modified_by = $_SESSION['sess_login_username'];

        if (!$model->save())
            throw new Exception("Error Processing Request", 1);

        return true;

    }

    public function destroyCourseWard($data)
    {
        $date = date('Y-m-d H:i:s');
        $model = Cf4CourseInTheWard::model()->findByPk($data['id']);

        $model->modify = $date;
        $model->modified_by = $_SESSION['sess_login_username'];
        $model->deleted_by = $_SESSION['sess_login_username'];
        $model->deleted_at = $date;
        $model->is_deleted = 1;

        if (!$model->save())
            throw new Exception("Error Processing Request", 1);

        return true;
    }

    public function getEntryId($data)
    {
        $service = new CF4HeaderService($data['encounter_nr'], $data['pid']);
        $service->save();

        return $service->getId();
    }

}