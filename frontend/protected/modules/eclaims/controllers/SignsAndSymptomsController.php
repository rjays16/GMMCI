 <?php


    // Yii::import('eclaims.services.CF4GeneratorService');
    // Yii::import('eclaims.services.prenatalConsultaion.menstrualService');
    // Yii::import('eclaims.services.prenatalConsultaion.obstetricService');
    // Yii::import('eclaims.services.clinicalRecords.clinicalRecordService');
    // Yii::import('eclaims.services.prenatalConsultaion.physicalExaminationService');

    Yii::import('eclaims.services.signsAndSymptoms.signsAndSymptomsService');

    class SignsAndSymptomsController extends Controller
    {
        public function actionSaveSigns()
        {
            $transaction = Yii::app()->db->beginTransaction();
            try {
                $service = new signsAndSymptomsService($_POST['encounter_nr']);
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
