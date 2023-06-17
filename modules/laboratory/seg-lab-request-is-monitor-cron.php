<?php
	error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
	require('./roots.php');
	
	require($root_path.'include/inc_environment_global.php');
	require($root_path.'modules/laboratory/ajax/lab-request-new.common.php'); 
	
	#-------------added by VAN ----------
	$dbtable='care_config_global'; // Taboile name for global configurations
	$GLOBAL_CONFIG=array();
	$new_date_ok=0;

	# Create global config object
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');
	require_once($root_path.'include/inc_date_format_functions.php');

	#------------------------------------
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
	define('LANG_FILE','lab.php');
	$local_user='ck_lab_user';
	
	define('NO_2LEVEL_CHK',1);
	require_once($root_path.'include/inc_front_chain_lang.php');
	

	# Create laboratory service object
	require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
	$srvObj=new SegLab();
	$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
	$glob_obj->getConfig('refno_%');
	global $db;
	
	$date_now = date("Y-m-d H:i");
	#$date_now = "2010-02-02";
	#echo "datenow=".date("Y-m-d H:i:s");
	$query1 = "select s.refno, s.create_dt, s.serv_dt, s.serv_tm, d.service_code, d.is_monitor, m.every_hour, m.no_takes".
	" from seg_lab_serv as s left join seg_lab_servdetails as d on s.refno=d.refno ".
	" left join seg_lab_serv_monitor as m on d.refno=m.refno where d.is_monitor='1' ".
	" and s.create_dt like '".$date_now.":%' order by s.create_dt asc";
	#echo $query1."<br><br>";
	$res = $db->Execute($query1);
	while($row = $res->FetchRow())
	{
		$request_dt = explode(" ",$row['create_dt']);
		$time = explode(":",$request_dt[1]);
		#echo "start_time=".$time[0].":".$time[1];
		$time[0] = $time[0]+$row['every_hour'];
		$time = array($time[0], $time[1]);
		$new_time = implode(":",$time);
		#echo "/new_time=".$new_time;
		$time_now = date("H:i");
		#echo "/time_now=".$time_now."<br>";
		if($new_time==$time_now)
		{
				$new_no_takes = $row['no_takes']-1;
				$data = $db->GetRow("select * from seg_lab_serv where refno=".$db->qstr($row['refno']));
				$data['refno'] = $srvObj->getLastNr(date("Y-m-d"),"'".$GLOBAL_CONFIG['refno_init']."'");
				$data['parent_refno'] = $row['refno'];
				$data['create_dt']=date("Y-m-d H:i:s");
				$data['modify_dt']=date("Y-m-d H:i:s");
				$data['history'].=$data['history']."\nUpdated ".date("Y-m-d H:i:s")."[".$data['modify_id']."]";
				#print_r($data);
				$srvObj->useLabServ();
				$srvObj->setDataArray($data);
				$saveok=$srvObj->insertDataFromInternalArray();
				if($saveok) echo "1.done updating";
				else	echo "1. update not successful";
				echo $srvObj->sql;
				$data2 = $db->GetRow("select * from seg_lab_servdetails where refno=".$db->qstr($row['refno']));
				$data2['refno'] = $data['refno'];
				$data2['parent_refno'] = $row['refno'];
				echo "<br><br>";
				#print_r($data2);
				$srvObj->useLabServDetails();
				$srvObj->setDataArray($data2);
				$saveok=$srvObj->insertDataFromInternalArray();
				if($saveok) echo "2.done updating";
				else	echo "2. update not successful";
				echo $srvObj->sql;
				$query2 = "update seg_lab_serv_monitor set no_takes=".$db->qstr($new_no_takes)." where refno=".$db->qstr($row['refno']);
				echo "<br><br>".$query2;
				$db->Execute($query2);
				if ($db->Affected_Rows())
				{
					echo "3.done updating";
				}
				else	echo "3. update not successful";	
		}
	}
	
?>
