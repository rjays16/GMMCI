<?php
/**
 * CourseWardController.php
 *
 * @author Jan Chris S. Ogel <iamjc93@gmail.com>
 * @copyright (c) 2019, Segworks Technologies Corporation (http://www.segworks.com)
 */

Yii::import('eclaims.services.CourseWard.CourseWardService');

class CourseWardController extends Controller
{

    public function actionSaveCourseWard()
    {
        $transaction = Yii::app()->db->beginTransaction();

        try {
            $service = new CourseWardService($_POST['encounter_nr']);

            $data = $service->saveCourseWard($_POST);

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

    public function actionDestroyCourseWard()
    {
        $transaction = Yii::app()->db->beginTransaction();

        try {
            $service = new CourseWardService($_POST['encounter_nr']);

            $data = $service->destroyCourseWard($_POST);

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
}