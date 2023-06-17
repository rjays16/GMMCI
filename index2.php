<?php
$root_path='';
require($root_path.'include/inc_environment_global.php');
define('LANG_FILE','products.php');
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');
# Create products object
require_once($root_path.'include/care_api_classes/class_pharma_transaction.php');
$p=new SegPharma;
var_dump($p);
?>



