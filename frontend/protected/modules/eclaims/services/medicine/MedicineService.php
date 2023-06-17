<?php
/**
 * MedicineService.php
 *
 * @author Jan Chris S. Ogel <iamjc93@gmail.com>
 * @copyright (c) 2019, Segworks Technologies Corporation (http://www.segworks.com)
 */

Yii::import('eclaims.models.cf4.Cf4Medicine');
Yii::import('eclaims.models.cf4.PhilMedicine');
Yii::import('eclaims.models.cf4.PhilMedicineForm');
Yii::import('eclaims.models.cf4.PhilMedicineGeneric');
Yii::import('eclaims.models.cf4.PhilMedicinePackage');
Yii::import('eclaims.models.cf4.PhilMedicineSalt');
Yii::import('eclaims.models.cf4.PhilMedicineStrength');
Yii::import('eclaims.models.cf4.PhilMedicineUnit');
Yii::import('eclaims.models.cf4.Cf4');
Yii::import('eclaims.services.CF4HeaderService');

class MedicineService
{
    public $encounter_nr;

    public function __construct($encounter_nr)
    {
        $this->encounter_nr = $encounter_nr;
    }

    /**
     * @return CActiveRecord[]|Cf4Medicine
     */
    public function getData()
    {
        $model = Cf4Medicine::model()->findAllByAttributes(array(
          'entry_id' => $this->cf4->id,
          'encounter_nr' => $this->encounter_nr
        ));

        if (empty($model)) {
            $model = new Cf4Medicine();
        }

        return $model;
    }

    public function getLibrary()
    {
        $model = PhilMedicine::model()->findAll();

        if (empty($model)) {
            $model = new PhilMedicine();
        }

        return $model;
    }

    /**
     * @param $data
     * @return bool
     * @throws CException
     */
    public function saveMedicine($data)
    {
        $date = date('Y-m-d H:i:s');
        $model = new Cf4Medicine();

        $medicine = PhilMedicine::model()->findByAttributes(array(
          'drug_code' => $data['drug_code']
        ));

        $entry_id = $this->getEntryId($data);
        $is_pndf = $data['is_pndf'] == 'true' ? 1 : 0;
        $drug_code = $data['is_pndf'] == 'true' ? $data['drug_code'] : null;
        $generic = $data['is_pndf'] == 'false' ? $data['generic'] : $medicine->description;

        $model->id = $model->getUuid();
        $model->entry_id = $entry_id;
        $model->encounter_nr = $this->encounter_nr;

        $model->drug_code = $drug_code;
        $model->generic = $generic;
        $model->quantity = $data['quantity'];
        $model->route = $data['route'];
        $model->frequency = $data['frequency'];
        $model->cost = $data['cost'];
        $model->is_pndf = $is_pndf;

        $model->modify = $date;
        $model->modified_by = $_SESSION['sess_login_username'];

        if (!$model->save()) {
            throw new Exception("Error Processing Request", 1);
        }

        return true;

    }

    /**
     * @param $data
     * @return bool
     * @throws Exception
     */
    public function destroyMedicine($data)
    {
        $date = date('Y-m-d H:i:s');
        $model = Cf4Medicine::model()->findByPk($data['id']);
        $model->modify = $date;
        $model->modified_by = $_SESSION['sess_login_username'];
        $model->deleted_by = $_SESSION['sess_login_username'];
        $model->deleted_at = $date;
        $model->is_deleted = 1;

        if (!$model->update()) {
            throw new Exception("Error Processing Request", 1);
        }

        return true;
    }

    /**
     * @param $data
     * @return bool
     * @throws Exception
     */
    public function updateMedicine($data)
    {
        $date = date('Y-m-d H:i:s');
        $model = Cf4Medicine::model()->findByPk($data['id']);

        if($data['description']){
            $model->generic = $data['description'];
        }
        $model->quantity = $data['quantity'];
        $model->route = $data['route'];
        $model->frequency = $data['frequency'];
        $model->cost = $data['cost'];
        $model->modify = $date;
        $model->modified_by = $_SESSION['sess_login_username'];

        if (!$model->update()) {
            throw new Exception("Error Processing Request", 1);
        }

        return true;
    }

    public function getEntryId($data)
    {
        $service = new CF4HeaderService($data['encounter_nr'], $data['pid']);
        $service->save();

        return $service->getId();
    }

}
