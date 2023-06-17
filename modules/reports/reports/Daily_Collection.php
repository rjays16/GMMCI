<?php
/*
created by Nick, 11/29/2013 10:00 PM
*/
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
include('parameters.php');

// var_dump($encoder);exit();

$sql1 = "SELECT fn_get_personell_lastname_first(personell_nr) as full_name FROM care_users WHERE login_id='".$encoder."'";
$result=$db->Execute($sql1);
if ($result) {
			while ($row=$result->FetchRow()) {
			$name = $row['full_name'];
			}
		}
		// var_dump($name);exit();

#_________________________________________________
$from = date("F j, Y", strtotime($from_date_format) );
$to = date("F j, Y", strtotime($to_date_format) );

// var_dump($acct_type);exit();
$params->put('dateRange',$from);

#_________________________________________________
global $db;

$sql = "SELECT\n".
				"fn_get_pay_account_type(pr.ref_source, pr.ref_no, pr.service_code, pr.or_no) AS account,\n".
				"pay.or_no,pay.or_date,pay.or_name,IF(pay.cancel_date IS NULL,pay.amount_due,0) AS amount_due,\n".
				"pay.cancel_date,pay.cancelled_by,\n".
				"(SUM(pr.amount_due) - pay.discount_tendered) AS `amount`,(cancel_date IS NOT NULL) AS is_cancelled\n".
			"FROM seg_pay_request AS pr\n".
				"INNER JOIN seg_pay AS pay ON pr.or_no=pay.or_no\n";

		$where = array();
		if ($from_date_format) {
			$where[]="DATE(pay.or_date)='$from_date_format'";
			$dTime = strtotime($date);
			if (is_numeric($shift_start) && is_numeric($shift_end)) {
				if ($shift_start < $shift_end) {
					$start_time = $dTime + $shift_start*3600;
					$end_time = $dTime + $shift_end*3600;
				}
				else {
					$start_time = $dTime + $shift_start*3600;
					$end_time = $dTime + $shift_end*3600+86400;
				}
				$where[] = "pay.or_date>='".date("Y-m-d H:i:s",$start_time)."' AND pay.or_date<='".date("Y-m-d H:i:s",$end_time)."'";
			}
			elseif (is_numeric($shift_start)) {
				$start_time = $dTime + $shift_start*3600;
				$where[] = "pay.or_date>='".date("Y-m-d H:i:s",$start_time)."' AND pay.or_date<=NOW()";
			}
		}

		if ($or_from)
			$where[] = "CAST(pay.or_no AS UNSIGNED) >= ".$db->qstr($or_from);

		if ($or_to)
			$where[] = "CAST(pay.or_no AS UNSIGNED) <= ".$db->qstr($or_to);

		if ($encoder) {
			$where[]="pay.create_id=".$db->qstr($encoder);
		}

		if ($where)
			$sql .= "WHERE (".implode(") AND (",$where).") GROUP BY or_no\n";
		$sql .= "HAVING (SELECT parent_account FROM seg_pay_subaccounts WHERE id=`account`)=".$db->qstr($acct_type)."\n";

		$sql .= "ORDER BY pay.or_no ASC";
		// var_dump($sql);exit();
		$result=$db->Execute($sql);
		$s_count=0;
		$s_total = 0;
         $rowindex = 0;
         if ($result->RecordCount() > 0){ // edited by: syboy 07/11/2015
         	$count_ors = array();
        while($row=$result->FetchRow()){ 
        		 $data[$rowindex] =array(
                                   'bill_date'=>date("m/d h:ia",strtotime($row['or_date'])),
                                   'or'=>$row['or_no'],
                                   'amounts2'=>($row['is_cancelled']!='1') ? (float)$row['amount'] : (float)'0.00',
                                   'patient_name'=>strtoupper($row['or_name']),
                                   'notes'=>($row['is_cancelled']=='1' ? 'Cancelled' : ''),
                                  );
        		 $count_ors[] =$row['or_no'];
        		 $s_total+= $row['amount'];
             $rowindex++;
             
        }
      }
        $params->put('title',$encoder!=''?$name:"All");
     	$params->put('total_ss',number_format($s_total,2));
      	$params->put('count_or',"OR used: ".count($count_ors));
		// $params->put('count_or',"OR used: ".count($count_ors));
		// if ($result) {
// 			$Data=array();
// 			while ($row=$result->FetchRow()) {
// 				$Data[]=array(
// 					'bill_date' => date("m/d h:ia",
// 						strtotime($row['or_date'])),
// 					$row['or_no'],
// 					($row['is_cancelled']!='1') ? number_format($row['amount'],2) : '0.00',
// 					strtoupper($row['or_name']),
// 					($row['is_cancelled']=='1' ? 'Cancelled' : '')
// 				);
// 				if ($row['is_cancelled']!='1') $s_total+=$row['amount'];
// 				$s_count++;
// 			}
// #			die(print_r($row,TRUE));
// 		}
// 		else {
// 			print_r($sql);
// 			print_r($db->ErrorMsg());
// 			exit;
// 			# Error
// 		}

// 

?>