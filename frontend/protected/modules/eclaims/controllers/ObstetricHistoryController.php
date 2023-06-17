 <?php


    Yii::import('eclaims.services.CF4GeneratorService');
    Yii::import('eclaims.services.prenatalConsultaion.menstrualService');
    Yii::import('eclaims.services.prenatalConsultaion.obstetricService');
    Yii::import('eclaims.services.clinicalRecords.clinicalRecordService');
    Yii::import('eclaims.services.prenatalConsultaion.obstetricService');
    class ObstetricHistoryController extends Controller
    {
        public function actionSaveObstetricHistory()
        {
            $transaction = Yii::app()->db->beginTransaction();
            try {

                $service = new obstetricService($_POST['encounter_nr']);
                $service->save($_POST);

                $transaction->commit();

                echo CJSON::encode(array(
                    'message' => 'Successfully Saved Obstetric History',
                    'status' => true,
                ));
            } catch (\Exception $e) {

                $transaction->rollback();

                echo CJSON::encode(array(
                    'message' => $e->getMessage(),
                    'status' => false,
                ));
            }
        }
    }
