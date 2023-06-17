 <?php


    Yii::import('eclaims.services.CF4GeneratorService');
    Yii::import('eclaims.services.prenatalConsultaion.menstrualService');
    Yii::import('eclaims.services.prenatalConsultaion.obstetricService');
    Yii::import('eclaims.services.clinicalRecords.clinicalRecordService');
    Yii::import('eclaims.services.postpartumCare.FamilyPlanningService');
    class FamilyPlanningController extends Controller
    {
        public function actionSaveFamilyPlanning()
        {

            $service = new FamilyPlanningService($_POST['encounter_nr']);
            $data = $service->save($_POST);
            if ($data) {
                echo \CJSON::encode(true);
            } else {
                echo \CJSON::encode(false);
            }
        }
    }
