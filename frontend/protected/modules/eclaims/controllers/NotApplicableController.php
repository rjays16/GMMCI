<?php
Yii::import('eclaims.services.CF4GeneratorService');
Yii::import('eclaims.services.prenatalConsultaion.notapplicableService');




class NotApplicableController extends Controller
{
    public function actionSaveNotApplicable()
    {
        // \CVarDumper::dump($_POST);
        // die;
        $transaction = Yii::app()->db->beginTransaction();
        try {

            $this->saveNotApplicable($_POST);
            $transaction->commit();

            echo CJSON::encode(array(
                'message' => 'Successfully Saved Not applicable',
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

    public function saveNotApplicable($data)
    {
        $service = new notapplicableService($data['encounter_nr']);
        $service->save($data);
    }
}
