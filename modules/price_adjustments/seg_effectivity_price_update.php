<?php
//created by cha 2009-08-28

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/**
* Integrated Hospital Information System beta 2.0.0 - 2004-05-16
* GNU General Public License
* Copyright 2002,2003,2004 
*
* See the file "copy_notice.txt" for the licence notice
*/     
define('LANG_FILE','specials.php');
define('NO_2LEVEL_CHK',1);
require_once($root_path.'include/inc_front_chain_lang.php');
$breakfile=$root_path.'main/spediens.php'.URL_APPEND;
$returnfile=$root_path.'main/spediens.php'.URL_APPEND;
$thisfile=basename(__FILE__);



//ajax
require($root_path."modules/price_adjustments/ajax/price_adjustments.common.php");
$xajax->printJavascript($root_path.'classes/xajax_0.5');
  
# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');

$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
$thisfile='seg_effectivity_price.php';

require_once($root_path.'include/care_api_classes/class_price_adjustments.php');
$priceObj = new Price_Adjustments();
//echo $priceObj;

global $db,$HTTP_SESSION_VARS;
$datenow=date("Y-m-d"); 
//$datenow="2009-05-08"; 
#echo 'here = '.$datenow;
$sql1="select refno, effectivity_date from seg_hospital_service_price where DATE(effectivity_date)='".$datenow."'";
#echo "sql = ".$sql1;
if($result1 = $db->Execute($sql1))
{
    while($row1=$result1->FetchRow())
    {
        #echo "<br>refno=".$row1['refno']." effectivity_date=".$row1['effectivity_date'];
        
        $sql2 = "select p.history,d.service_code, d.price_cash, d.price_charge, d.ref_source from seg_hospital_service_price as p inner join".
        " seg_hospital_service_price_details as d on p.refno=d.refno where p.refno='".$row1['refno']."'";
        if($result2 = $db->Execute($sql2))
        {
            #echo "<br>sql2=".$sql2;
            $table="";
            while($row2=$result2->FetchRow())
            {
                //echo $row2['ref_source'];
                switch($row2['ref_source'])
                {
                    case 'LB': $table="seg_lab_services"; break;
                    case 'RD': $table="seg_radio_services"; break;
                    case 'PH': $table="care_pharma_products_main"; break;
                    case 'MS': $table="care_pharma_products_main"; break;
                    case 'O': $table=""; break;
                }
                
                if($row2['ref_source']=='LB' || $row2['ref_source']=='RD')
                {
                  $sql_hist = "select history from $table where service_code=".$db->qstr($row2['service_code']);
									$result_hist = $db->Execute($sql_hist);
									$row_hist = $result_hist->FetchRow();
	 								$history = $row['history']."\nUpdated ".date("Y-m-d H:i:s")." [".$HTTP_SESSION_VARS['sess_temp_userid']."]";
		  #echo "<br>sql_hist=".$sql_hist;
                  $sql3=" update $table set price_cash=".$db->qstr($row2['price_cash']).", price_charge=".$db->qstr($row2['price_charge']).", modify_dt=".$db->qstr(date("Y-m-d H:i:s")).", modify_id=".$db->qstr($HTTP_SESSION_VARS['sess_temp_userid']).", history=".$db->qstr($history)." where service_code=".$db->qstr($row2['service_code']);
                    #echo "<br>sql3=".$sql3; 
                    if($result3=$db->Execute($sql3))
                    {
                        echo "<br>update success!";
                    }
                    
                  $history = $row2['history']."\nUpdated ".date("Y-m-d H:i:s")." [".$HTTP_SESSION_VARS['sess_temp_userid']."]";
                  $sql_hist = "UPDATE seg_hospital_service_price SET history=".$db->qstr($history)." where refno=".$db->qstr($row1['refno']);
									#echo "<br>sql_hist=".$sql_hist;
									$result_hist = $db->Execute($sql_hist);
									$sql_stat = "UPDATE seg_hospital_service_price_details SET status='updated' where refno=".$db->qstr($row1['refno']);
									$result_stat = $db->Execute($sql_stat);
	 								#echo "<br>sql_stat=".$sql_stat;
                }
                else if($row2['ref_source']=='PH' || $row2['ref_source']=='MS')
                {
                  $sql_hist = "select history from $table where bestellnum=".$db->qstr($row2['service_code']);
									$result_hist = $db->Execute($sql_hist);
									$row_hist = $result_hist->FetchRow();
	 								$history = $row['history']."\nUpdated ".date("Y-m-d H:i:s")." [".$HTTP_SESSION_VARS['sess_temp_userid']."]";
		  #echo "<br>sql_hist=".$sql_hist;
                  $sql3=" update $table set price_cash=".$db->qstr($row2['price_cash']).", price_charge=".$db->qstr($row2['price_charge']).", modify_time=".$db->qstr(date("Y-m-d H:i:s")).", modify_id=".$db->qstr($HTTP_SESSION_VARS['sess_temp_userid']).", history=".$db->qstr($history)." where bestellnum=".$db->qstr($row2['service_code']);
                    #echo "<br>sql3=".$sql3; 
                    if($result3=$db->Execute($sql3))
                    {
                        echo "<br>update success!";
                    }
                    
                  $history = $row2['history']."\nUpdated ".date("Y-m-d H:i:s")." [".$HTTP_SESSION_VARS['sess_temp_userid']."]";
                  $sql_hist = "UPDATE seg_hospital_service_price SET history=".$db->qstr($history)." where refno=".$db->qstr($row1['refno']);
									#echo "<br>sql_hist=".$sql_hist;
									$result_hist = $db->Execute($sql_hist);
									$sql_stat = "UPDATE seg_hospital_service_price_details SET status='updated' where refno=".$db->qstr($row1['refno']);
									$result_stat = $db->Execute($sql_stat);
									#echo "<br>sql_stat=".$sql_stat;
                }
                else if($row2['ref_source']=='O')
                {
                    $sql3="select service_code from seg_other_services where service_code='".$row2['service_code']."'";
                    $sql4="select service_code from seg_otherhosp_services where service_code='".$row2['service_code']."'";
                    #echo "<br>".$sql3." ".$sql4;
                    $result3=$db->Execute($sql3);
                    $result4=$db->Execute($sql4);
                    $rowa=$result3->FetchRow();
                    $rowb=$result4->FetchRow();
                    if($rowa['service_code']==$row2['service_code'])
                    {
										  $table="seg_other_services";
										  $sql_hist = "select history from $table where service_code=".$db->qstr($row2['service_code']);
											$result_hist = $db->Execute($sql_hist);
											$row_hist = $result_hist->FetchRow();
	 										$history = $row['history']."\nUpdated ".date("Y-m-d H:i:s")." [".$HTTP_SESSION_VARS['sess_temp_userid']."]";
		 # echo "<br>sql_hist=".$sql_hist;
                  	$sql5=" update $table set price=".$db->qstr($row2['price_cash']).", modify_time=".$db->qstr(date("Y-m-d H:i:s")).", modify_id=".$db->qstr($HTTP_SESSION_VARS['sess_temp_userid']).", history=".$db->qstr($history)." where service_code=".$db->qstr($row2['service_code']);
                      
                           # $sql5=" update $table set price='".$row2['price_cash']."', modify_time='".date("Y-m-d H:i:s")."',modify_id='".$HTTP_SESSION_VARS['sess_temp_userid']."' where service_code='".$row2['service_code']."'";
                            //echo "<br>sql3=".$sql3; 
                            if($result5=$db->Execute($sql5))
                            {
                                #echo "<br>sql5=".$sql5; 
                                echo "<br>update success!";
                            }
                      $history = $row2['history']."\nUpdated ".date("Y-m-d H:i:s")." [".$HTTP_SESSION_VARS['sess_temp_userid']."]";
		                  $sql_hist = "UPDATE seg_hospital_service_price SET history=".$db->qstr($history)." where refno=".$db->qstr($row1['refno']);
											#echo "<br>sql_hist=".$sql_hist;
											$result_hist = $db->Execute($sql_hist);
											$sql_stat = "UPDATE seg_hospital_service_price_details SET status='updated' where refno=".$db->qstr($row1['refno']);
											$result_stat = $db->Execute($sql_stat);
										 #	echo "<br>sql_stat=".$sql_stat;
                    }
                    else if($rowb['service_code']==$row2['service_code'])
                    {
                      $table="seg_otherhosp_services";
                      $sql_hist = "select history from $table where service_code=".$db->qstr($row2['service_code']);
											$result_hist = $db->Execute($sql_hist);
											$row_hist = $result_hist->FetchRow();
	 										$history = $row['history']."\nUpdated ".date("Y-m-d H:i:s")." [".$HTTP_SESSION_VARS['sess_temp_userid']."]";
		  #echo "<br>sql_hist=".$sql_hist;
                  		$sql5=" update $table set price=".$db->qstr($row2['price_cash']).", modify_dt=".$db->qstr(date("Y-m-d H:i:s")).", modify_id=".$db->qstr($HTTP_SESSION_VARS['sess_temp_userid']).", history=".$db->qstr($history)." where service_code=".$db->qstr($row2['service_code']);
                      #$sql5=" update $table set price='".$row2['price_cash']."', modify_dt='".date("Y-m-d H:i:s")."',modify_id='".$HTTP_SESSION_VARS['sess_temp_userid']."' where service_code='".$row2['service_code']."'";
                            //echo "<br>sql4=".$sql4; 
                       if($result5=$db->Execute($sql5))
                       {
                               # echo "<br>sql5=".$sql5; 
                       				 echo "<br>update success!";
                       }
                       $history = $row2['history']."\nUpdated ".date("Y-m-d H:i:s")." [".$HTTP_SESSION_VARS['sess_temp_userid']."]";
		                  $sql_hist = "UPDATE seg_hospital_service_price SET history=".$db->qstr($history)." where refno=".$db->qstr($row1['refno']);
											$result_hist = $db->Execute($sql_hist);
											#echo "<br>sql_hist=".$sql_hist;
											$sql_stat = "UPDATE seg_hospital_service_price_details SET status='updated' where refno=".$db->qstr($row1['refno']);
											$result_stat = $db->Execute($sql_stat);
											#echo "<br>sql_stat=".$sql_stat;
                    }
                }
            }
           # echo "<br>";
        }
    }
}

?>
