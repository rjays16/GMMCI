<?php

/**
 * DoctorAccreditationController.php
 *
 * @author Mary Joy L. Abuyo
 */


/**
 *
 *
 * @copyright (c) 2014, Segworks Technologies Corporation
 */
class DoctorAccreditationController extends Controller{


    /**
     *
     * @return type
     */
    public function filters(){
        return array('accessControl',
            array('bootstrap.filters.BootstrapFilter')
        );
    }

    public function accessRules() {
        return array(
            array(
                'deny',
                'actions' => array('index'),
                'users' => array('?')
            ),
            array(
                'deny',
                'expression' => '!Yii::app()->user->checkPermission("eclaims")',
            ),
            array(
                'deny',
                'actions' => array('check'),
                'expression' => '!Yii::app()->user->checkPermission("doctorAccreditation_sudomanage")',
            ),
            array(
                'allow',
                'actions' => array('index'),
                'users' => array('@')
            ),
        );
    }

    /*
     * Function: Action that will parse the data from PhilHealth into json to get the accreditation
     *           start and end date of a certain doctor and save it to the seg_dr_accreditation table.
     *
     * Parameters for parsing: Doctor's accreditation number, Admission date of a patient which is set to curent time,
     *                         Discharge date of a patient which is set to curent time.
     *
     * Parameters for saving to seg_dr_accreditation table:  Personnel number of the Doctor, HCare Id of the doctor
     *
     * Return: Doctor's Accreditation start date which is save to the seg_dr_accreditation table and Doctor's Accreditation
     *         end date which is saved to the seg_dr_accreditation table..
     */

    /**
     *
     * @param type $accreditation_nr
     * @param type $personnel_nr
     */
    public function actionCheck() {

        Yii::import('eclaims.services.accreditation.AccreditationService');
        
        Yii::import('eclaims.models.EclaimsDoctorAccreditation');
        
        Yii::import('eclaims.services.ServiceExecutor');

        $accreditationNr = $_GET['accreditation_nr'];

        $personnelNr = $_GET['personnel_nr'];


        $personnel = Personnel::model()->findByPk($personnelNr);

        $service = new ServiceExecutor(array(
            'endpoint'=>'hie/doctor/checkAccreditation',
            'params'=>array(
                'pHospitalCode' => $this->getHospitalCode(),
                'pDoctorAccreCode' => $accreditationNr,
                'pAdmissionDate' => date("m-d-Y"),  
                'pDischargeDate' => date("m-d-Y"),
            )
        ));


        try {
            
            $accreService = new AccreditationService($personnel , $accreditationNr);

            $accreService->saveAccreditation(array());
            try {

                $result = $service->execute();
                
                $accreService->saveAccreditation($result['data']);


                Yii::app()->user->setFlash('success', '<strong>Success!</strong> Doctor accreditation successfully updated');

            } catch (ServiceCallException $e) {

                if (empty($accreditationNr)) {
                 
                    Yii::app()->user->setFlash('warning', '<strong>Notice: </strong> Doctor is not accredited by PhilHealth.');
                } else { 

                    Yii::app()->user->setFlash('error', '<strong>Service call error:</strong> ' . $e->getReason());
                }
            }

        } catch (\CDbException $e) {

            Yii::app()->user->setFlash('error', '<strong>Service call error:</strong> ' . $e->getMessage());

        }


        // try {
        // $service = new AccreditationService( $personnel , $accreditationNr );

        //     try {
        //         $service->saveAccreditation();
        //         // if ($accreditation->save()) {
        //         // } else {
        //         //     Yii::app()->user->setFlash('error', '<strong>Oops!</strong> Unable to save the doctor accreditation information');
        //         // }    
        //     } catch (CException $e) {
        //         Yii::app()->user->setFlash('error', '<strong>Oops!</strong> Unable to save the doctor accreditation information');
        //     }
        // } catch (ServiceCallException $e) {
        //     // if (empty($accreditationNr)) {
        //     //     Yii::app()->user->setFlash('warning', '<strong>Notice: </strong> Doctor is not accredited by PhilHealth.');
        //     // }else{
        //         Yii::app()->user->setFlash('error', '<strong>Service call error:</strong> ' . $e->getReason());
        //     // }
        // }

        $this->redirect(array('index', 'nr' => $personnelNr));
    }

    /**
     *
     */
    public function actionIndex() {

        Yii::import('phic.models.PhicPersonnel');
        $personnel = PhicPersonnel::model()->findbyPK($_GET['nr']);

        if (empty($personnel)) {
            $personnel = new PhicPersonnel;
            Yii::app()->user->setFlash('info', '<strong></strong><i class="fa fa-exclamation-circle"></i> Search and select a specific doctor to start accreditation check');
        }
        $accreditation = empty($personnel->phicAccreditation) ? 
            new DoctorAccreditation : $personnel->phicAccreditation;

        $this->render('index', array(
            'personnel' => $personnel,
            'accreditation' => $accreditation
        ));
    }

    #added monmon
    public function actionGetPan(){
        Yii::import('phic.models.PhicPersonnel');
        Yii::import('eclaims.services.ServiceExecutor');
        Yii::import('eclaims.models.EclaimsPerson');
      
        $personnel_nr = $_GET['personnel_nr'];
        $personnel = PhicPersonnel::model()->findbyPK($personnel_nr);
        $person = EclaimsPerson::model()->findByPk($personnel->pid);

        $params = array(
            'pHospitalCode' => $this->getHospitalCode(),
            'pDoctorTIN' => $personnel->tin,
            'pDoctorLastName' => $person->name_last,
            'pDoctorFirstName' => $person->name_first,
            'pDoctorMiddleName' => $person->name_middle,
            'pDoctorSuffix' => ($person->suffix) ? $person->suffix : '',
            'pDoctorBirthDate' => date('m-d-Y',strtotime($person->date_birth))
        );
        $service = new ServiceExecutor(
            array(
                'endpoint'=>'hie/doctor/getpan',
                'params'=> $params
            )
        );
        $response = $service->execute();
        echo json_encode($response);
    
    }
    private function getHospitalCode(){
        #added hospital code
        Yii::import('eclaims.models.HospitalConfigForm');
        $configModel = new HospitalConfigForm;
        $hospitalCode = $configModel->hospital_code;
        return $hospitalCode;
    }

}