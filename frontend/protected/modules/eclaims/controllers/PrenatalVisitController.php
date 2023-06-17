 <?php


    Yii::import('eclaims.services.CF4GeneratorService');
    Yii::import('eclaims.services.prenatalConsultaion.menstrualService');
    Yii::import('eclaims.services.prenatalConsultaion.obstetricService');
    Yii::import('eclaims.services.clinicalRecords.clinicalRecordService');
    Yii::import('eclaims.services.prenatalConsultaion.prenatalVisitService');
    Yii::import('eclaims.models.cf4.PrenatalVisits');
    class PrenatalVisitController extends Controller
    {
        public function actionSavePrenatalVisit()
        {
            $transaction = Yii::app()->db->beginTransaction();
            try {
                $this->savePrenatalVisit($_POST);
                $transaction->commit();

                echo \CJSON::encode(array(
                    'status' => true,
                    'message' => 'Successfully Saved Prenatal Visit',
                ));
            } catch (\Exception $e) {
                $transaction->rollback();
                echo \CJSON::encode(array(
                    'status' => false,
                    'message' => $e->getMessage(),
                ));
            }
        }

        public function actionUpdatePrenatalVisit(){
            $transaction = Yii::app()->db->beginTransaction();
            try {
                $this->updatePrenatalVisit($_POST);
                $transaction->commit();

                echo \CJSON::encode(array(
                    'status' => true,
                    'message' => 'Successfully Updated Prenatal Visit',
                ));
            } catch (\Exception $e) {
                $transaction->rollback();
                echo \CJSON::encode(array(
                    'status' => false,
                    'message' => $e->getMessage(),
                ));
            }
        }

        public function actionDeletePrenatalVisit()
        {
            $transaction = Yii::app()->db->beginTransaction();
            try {
                $this->deletePrenatalVisit($_POST);
                $transaction->commit();
                echo \CJSON::encode(array(
                    'status' => true,
                    'message' => 'Successfully deleted Prenatal Visit!',
                ));
            } catch (\Exception $e) {
                $transaction->rollback();

                echo \CJSON::encode(array(
                    'status' => false,
                    'message' => $e->getMessage(),
                ));
            }
        }

        public function actionGetDataPrenatalVisit(){
            $model = PrenatalVisits::model()->findByPk($_POST['prenatal_visit_id']);
            echo \CJSON::encode(array(
                    'status' => false,
                    'message' =>'test get data',
                    'data' => $model
                    // 'id' => $_POST['prenatal_visit_id'],
                ));
        }

        public function savePrenatalVisit($data)
        {
            $service = new prenatalVisitService($_POST['encounter_nr']);
            $service->save($_POST);
        }

        public function updatePrenatalVisit($data){
            $service = new prenatalVisitService($_POST['encounter_nr']);
            $service->update($_POST);
        }
        public function deletePrenatalVisit($data)
        {
            $service = new prenatalVisitService($_POST['encounter_nr']);
            $data = $service->delete($_POST);
        }
    }
