<?php

/**
 * 
 * MainController.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2005-2014, Segworks Technologies Corporation
 */

/**
 * Description of MainController
 *
 * @package
 */
class MainController extends Controller {

    /**
     * 
     * @return type
     */
    public function filters() {
        return array(
            'accessControl',
            array('bootstrap.filters.BootstrapFilter'),
            );
    }
    
    /**
     * 
     */
    public function accessRules() {
        return array(
            array(
                'deny',
                'actions' => array('index'),
                'users' => array('?')
                ),
            
            array(
                'allow',
                'actions' => array('index'),
                'users' => array('@')
                ),
            );
    }
    
    /**
     * 
     * @param type $action
     */
    public function beforeAction($action) {
        $this->breadcrumbs[] = 'Home';
        $this->activeMenu = 'home';
        return parent::beforeAction($action);
    }
    
    /**
     * 
     */
    public function actionIndex() {
        $this->render('index', array('active_menu'=>'home'));
    }

}