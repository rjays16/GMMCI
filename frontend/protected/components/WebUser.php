<?php

/**
 * WebUser.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2005-2013, Segworks Technologies Corporation
 */

/**
 * Description of WebUser
 *
 * @package
 */
class WebUser extends CWebUser {

    public $loginUrl = 'main/login.php';

    /**
     *
     */
    public function init() {
        global $key_login;

        /**
         * Pass credentials information from Care2x session to
         * Yii-managed session
         */
        Yii::app()->getSession()->open();

        $session = Yii::app()->session;
        $id = @$session['sess_login_userid'];
        $pw = @$session['sess_login_pw'];
        // Check if the password is valid
        // originally Yii::app()->request->getScriptFile()
        if (!empty($id)) {
            require_once dirname(Yii::app()->request->getScriptFile()) . DIRECTORY_SEPARATOR .
                'classes' . DIRECTORY_SEPARATOR .
                'pear' . DIRECTORY_SEPARATOR .
                'crypt' . DIRECTORY_SEPARATOR .
                'hcemd5.php';

            $hcemd5 = new Crypt_HCEMD5($key_login,'');
            $password = $hcemd5->DecodeMimeSelfRand($pw);

            $user = User::model()->findByPk($id);
            if (!empty($user) && $user->validatePassword($password)) {
                // Pass user data to session data
                $this->setState('__id', $id);
            }
        } else {
            $this->logout();
        }

		parent::init();
    }
    // @todo Move from this line to end, another class.
    // Check access class, specifically to be used by the module
    const PERMISSION_SUDOMANAGE = 'sudomanage';
    private $permissionsDirectory;

    /**
     * @param $operation
     * e.g:
     *  - module
     *  - module.user_defined
     *  - module.controller
     *  - module.controller.action
     * Sudo/Root/Admin Permission
     *  - module/controller.sudomanage
     */
    public function checkPermission($operations = array(), $allowCaching=true)
    {
        $session = new CHttpSession;
        $_permissions = $this->getPermissionToArray($session);

        $_modulePermissions = $this->extractModulePermissions($_permissions);
        if($_modulePermissions) {
            // CVarDumper::dump($_modulePermissions, 10, true);
            $permitted = $this->searchPermissionByOperation($operations, $_modulePermissions);

            return $permitted;
        }

        return false;
    }

    private function getPermissionToArray(CHttpSession $session) 
    {
        $_permissions = $session->get('sess_permission', array());
        $_exploded = explode(' ', trim($_permissions));

        sort($_exploded);
        return $_exploded;
    }


    private function extractModulePermissions(array $permissions = array()) 
    {
        $_moduleName = Yii::app()->getController()->getModule()->getId();
        $_moduleNameCount = strlen($_moduleName);

        $_permissions = $this->pregReplaceInArray($permissions, '/_a_(\d+)_/', '');
        $modulePermissions = array();

        /* Extract permissions, related only to the current module */

        /**
        *  Mod by jeff 01-08-18 for Access permission error 403 if user has All Areas Permission.
        */
        // var_dump($_permissions);die();
        foreach ($_permissions as $value) {
            if ($value == 'all') { $value = 'eclaims_sudomanage'; }
            $_key = substr($value, 0, $_moduleNameCount);

            if($_key == $_moduleName) {
                $modulePermissions[] = $value;
            }
        }
        return $modulePermissions;
    }


    private function pregReplaceInArray(array $items = array(), $pattern = '', $replacedStr = '') 
    {
        $_container = array();
        foreach($items as $item) {
            $_temp = preg_replace($pattern, $replacedStr, $item);
            $_container[] = $_temp;
        }
        return $_container;
    }

    private function searchPermissionByOperation($operations, array $permissions = array())
    {
        if(is_array($operations)) {
            foreach($operations as $operation) {
                $permitted = $this->searchPermissionByOperation($operation, $permissions);
                return $permitted;
            }
        } else {

            if(!empty($operations)) {
                $_controller = Yii::app()->getController();
                $_module = $_controller->getModule();

                $permitted = $this->pregSearchPermission($operations, $permissions);
                /*
                    Explode the Operation, to see the parent permissions.
                */
                if(!$permitted) {
                    $mappedOperation = $this->pregSearchPermission($operations, $this->getPermissionDirectory());
                    $mappedOperation = preg_replace('/_' . self::PERMISSION_SUDOMANAGE . '/', '', $mappedOperation);

                    $explodedOperation = array_reverse(explode('_', $mappedOperation));

                    /* Check the exploded operation + _sudomanage, if exists in permissions */
                    foreach($explodedOperation as $operation) {
                        $sudoOperation = $operation . '_' . self::PERMISSION_SUDOMANAGE;
                        $permitted = $this->pregSearchPermission($sudoOperation, $permissions);
                        if($permitted) {
                            break;
                        }
                    }
                }
                return $permitted;
            }
        }
        return false;
    }

    private function pregSearchPermission($operation = '', array $permissions = array()) 
    {
        foreach($permissions as $index => $item) {
            $match = preg_match('/' . $operation . '/', $item);
            if($match) {
                return $item;
            }
        }
        return false;
    }

        /**
     * Singleton
     * Get all Permission in global_conf/areas.php. 
     * Then extracted the current modules permissions.
     */
    private function getPermissionDirectory() 
    {
        if(!isset($this->permissionsDirectory)) {
            include_once('./global_conf/areas.php');
            $this->permissionsDirectory = $this->extractModulePermissions(array_keys($area_opt));
        }
        return $this->permissionsDirectory;
    }
}
