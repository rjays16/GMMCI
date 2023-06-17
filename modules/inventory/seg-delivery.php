<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path."modules/inventory/ajax/seg-delivery.common.php");

define('NO_2LEVEL_CHK',1);
define('LANG_FILE','products.php');
$local_user='ck_inv_delivery_user';  

require_once($root_path.'include/inc_front_chain_lang.php');

# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');

$GLOBAL_CONFIG=array(); 
$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
$date_format=$GLOBAL_CONFIG['date_format'];
$phpfd=$date_format;
$phpfd=str_replace("dd", "%d", strtolower($phpfd));
$phpfd=str_replace("mm", "%m", strtolower($phpfd));
$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
$phpfd=str_replace("yy","%y", strtolower($phpfd));

$php_date_format = strtolower($date_format);
$php_date_format = str_replace("dd","d",$php_date_format);
$php_date_format = str_replace("mm","m",$php_date_format);
$php_date_format = str_replace("yyyy","Y",$php_date_format);
$php_date_format = str_replace("yy","y",$php_date_format);

$source=$_GET['src'];
if (isset($_GET['from']) && (strcmp($_GET['from'], 'list') == 0)) 
    $breakfile=$root_path."modules/inventory/seg-trans-list.php".URL_APPEND."&userck=$userck&list=deliveries";    
else if($_GET['from']=='phs')
        $breakfile=$root_path."modules/phs/seg-phs-function.php".URL_APPEND."&userck=$userck";
else    
    $breakfile=$root_path."modules/supply_office/seg-supply-functions.php".URL_APPEND."&userck=$userck";
    
$thisfile='seg-delivery.php';
if (!isset($target)) $target = "New";
include_once("seg-delivery-edit.php");  
?>