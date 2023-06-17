<?php
/**
 * MedicineController.php
 *
 * @author Jan Chris S. Ogel <iamjc93@gmail.com>
 * @copyright (c) 2019, Segworks Technologies Corporation (http://www.segworks.com)
 */

Yii::import('eclaims.services.medicine.MedicineService');

class MedicineController extends Controller
{

    public function actionSaveMedicine()
    {
        $transaction = Yii::app()->db->beginTransaction();

        try {
            $service = new MedicineService($_POST['encounter_nr']);

            $data = $service->saveMedicine($_POST);

            $transaction->commit();
            echo CJSON::encode(array(
                'status' => true
            ));
        } catch (\Exception $e) {
            $transaction->rollback();

            echo CJSON::encode(array(
                'message' => $e->getMessage(),
                'status' => false
            ));
        }
    }

    public function actionDestroyMedicine()
    {
        $transaction = Yii::app()->db->beginTransaction();

        try {
            $service = new MedicineService($_POST['encounter_nr']);

            $data = $service->destroyMedicine($_POST);

            $transaction->commit();
            echo CJSON::encode(array(
                'message' => '',
                'status' => true
            ));
        } catch (\Exception $e) {
            $transaction->rollback();

            echo CJSON::encode(array(
                'message' => $e->getMessage(),
                'status' => false
            ));
        }
    }

    public function actionMedicineList()
    {
        $sql = "SELECT 
                  t.description,
                  t.drug_code
                FROM
                  seg_phil_medicine t 
                WHERE t.`description` LIKE '%" . $_GET['t'] . "%'";

        $medicine = Yii::app()->db->createCommand($sql)->queryAll();

        echo CJSON::encode($medicine);
    }

    public function actionGetData()
    {
        $sql = "SELECT
                  t.generic,
                  t.drug_code,
                  t.quantity,
                  t.route,
                  t.frequency,
                  t.cost,
                  t.is_pndf
                FROM
                  seg_cf4_medicine t
                WHERE t.`id` LIKE '%" . $_GET['id'] . "%'";
        $data = Yii::app()->db->createCommand($sql)->queryAll();
        echo CJSON::encode($data);
    }

    public function actionUpdateMedicine() {
        $transaction = Yii::app()->db->beginTransaction();
        try {
            $service = new MedicineService($_POST['encounter_nr']);

            $data = $service->updateMedicine($_POST);

            $transaction->commit();
            echo CJSON::encode(array(
                'message' => 'Medicine successfully Updated!',
                'status' => true
            ));
        } catch (\Exception $e) {
            $transaction->rollback();

            echo CJSON::encode(array(
                'message' => $e->getMessage(),
                'status' => false
            ));
        }
    }

}
