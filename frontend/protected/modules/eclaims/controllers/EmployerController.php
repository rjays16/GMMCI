<?php

/**
 *
 * @author  Gerard Angelo Baluyot <gerbender017@gmail.com> 
 * @copyright (c) 2018, Segworks Technologies Corporation
 */

Yii::import('eclaims.services.ServiceExecutor');
Yii::import('eclaims.models.HospitalConfigForm');

Yii::import('eclaims.models.EclaimsPerson');


class EmployerController extends Controller
{

    public function filters() {
        return array(
            'accessControl',
            array(
                'bootstrap.filters.BootstrapFilter'
            )
        );
    }


    public function actionInfo(){
  
        if (isset($_GET['id'])) {
            $person = EclaimsPerson::model()->findByPk($_GET['id']);
            if ($person) {
                echo CJSON::encode(array(
                	'pPEN' => $person->phicMember2->employer_no,
                	'pEmployerName' => $person->phicMember2->employer_name
                ));
            } else {
                throw new CHttpException(404, 'Employer Does Not Exist');
            }
        } else {
            throw new CHttpException(400, 'Patient ID not specified');
        }

    }


    public function actionSearch(){

        if(isset($_GET['q'])){
	        $configModel = new HospitalConfigForm;
	        $hospitalCode = $configModel->hospital_code;
	    
	    	$params = array(
	            'pHospitalCode' => $hospitalCode,                        
	            'pPEN' => '',
	            'pEmployerName' => $_GET['q'],
	    	);

			$service = new ServiceExecutor(array(
			    'endpoint'=>'hie/eligibility/SearchEmployer',
			    'params'=> $params
			));	    	

			$data = $service->execute();

			$data2 = array();

			if (!empty($data))  {

				$employers = $data['data']['eEMPLOYERS']['employer'];

				if (empty($employers['@attributes'])) {
					
					foreach ($employers as $key => $value) {
						
						$data2[] = $employers[$key]['@attributes'];					
					}						
				}  else {

						$data2[] = $employers['@attributes'];
				}

				$result = $data2;

			}
			else {

				$result = array();
			}

        } else {
            $result = array();
        }

        echo CJSON::encode($result);

    }

}
