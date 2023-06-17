 <?php


    Yii::import('eclaims.services.CF4GeneratorService');
    Yii::import('eclaims.services.prenatalConsultaion.menstrualService');
    Yii::import('eclaims.services.prenatalConsultaion.obstetricService');
    Yii::import('eclaims.services.clinicalRecords.clinicalRecordService');
    Yii::import('eclaims.services.postpartumCare.SignsOfMaternalService');
    class SignsOfMaternalController extends Controller
    {
        public function actionSaveSignsOfMaternal()
        {

            $service = new SignsOfMaternalService($_POST['encounter_nr']);
            $data = $service->save($_POST);
            if ($data) {
                echo \CJSON::encode(true);
            } else {
                echo \CJSON::encode(false);
            }
        }
    }
