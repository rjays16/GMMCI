 <?php

    Yii::import('eclaims.services.vitalSigns.vitalSignsService');

    class VitalSignsController extends Controller
    {
        public function actionSaveVitalSigns()
        {
            $transaction = Yii::app()->db->beginTransaction();
            try {

                $service = new vitalSignsService($_POST['encounter_nr']);
                $data = $service->save($_POST);

                $transaction->commit();
                echo CJSON::encode(array(
                    'message' => "Vital Signs Successfully Saved",
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

        public function actionDeleteVitalSigns()
        {
            $transaction = Yii::app()->db->beginTransaction();
            try {

                $service = new vitalSignsService($_POST['encounter_nr']);
                $data = $service->delete($_POST);

                $transaction->commit();
                echo CJSON::encode(array(
                    'message' => "Vital Signs Successfully Deleted",
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
