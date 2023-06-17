<?php

/**
 * 
 * QRCodeController.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2005-2013, Segworks Technologies Corporation
 */

Yii::import('eclaims.extensions.qrcode.QRCode');

/**
 * Description of QRCodeController
 *
 * @package
 */
class QRCodeController extends Controller {

    const PHIC_CACHE_KEY = 'eclaims.qrcode.phic';
    
    /**
     * 
     * @return type
     */
    public function filters() {
        return array(
            'accessControl'
        );
    }
    
    /**
     * 
     */
    public function actionPhic() {
        $cache = Yii::app()->getCache();
        if (isset($cache[self::PHIC_CACHE_KEY])) {
            $output = $cache[self::PHIC_CACHE_KEY];
            //echo $output;
            die('x');
        } else {
            $code = new QRCode("http://philhealth.gov.ph");
            @ob_start();
            $code->create();
            $output = @ob_get_clean();
            $cache[self::PHIC_CACHE_KEY] = $output;
        }
        echo $output;
    }
    
    /**
     * 
     */
    public function actionIndex() {
        $code=new QRCode("http://philhealth.gov.ph");
        $code->create();
    }
    
}
