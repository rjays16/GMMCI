<?php

/**
 *
 * DoctorController.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) Segworks Technologies Corporation
 */

/**
 * Description of DoctorController
 *
 * @package
 */
class DoctorController extends Controller {
    
    /**
     *
     */
    public function actionSearch() {
        $result = array();
        if(isset($_GET['q'])){
            $term  = $_GET['q'];
            $doctors = Personnel::search($term);
            foreach($doctors as $doctor){
                $result[] = $doctor->toArray();
            }
        }

        echo CJSON::encode($result);
    }
}

