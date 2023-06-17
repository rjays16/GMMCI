 <?php

    Yii::import('eclaims.services.chiefComplaint.chiefComplaintService');

    class ChiefComplaintController extends Controller
    {
        public function actionSaveChiefComplaint()
        {
            $transaction = Yii::app()->db->beginTransaction();
            try {

                $service = new chiefComplaintService($_POST['encounter_nr']);
                $data = $service->save($_POST);

                $transaction->commit();
                echo CJSON::encode(array(
                    'message' => "Signs and Symptoms Successfully Saved",
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
