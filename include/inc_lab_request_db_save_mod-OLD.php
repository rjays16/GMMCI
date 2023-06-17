 <?php
/*------begin------ This protection code was suggested by Luki R. luki@karet.org ---- */
if (eregi('inc_lab_request_db_save_mod.php',$PHP_SELF)) 
	die('<meta http-equiv="refresh" content="0; url=../">');
/*------end------*/

$dbtable='seg_lab_serv';
// if mode is save then save the data
#if(isset($mode)&&($mode=='save')){
if((isset($mode)&&($mode=='save'))&&(isset($saverequest)&&($saverequest))){

    $saveok=false;
    $error=false;
    $error_bnum=false;
    $error_name=false;
    $error_besc=false;
    $error_minmax=false;

	 $refno=trim($refno);
	 
	 if(!$update){	
		# check if order number exists
		if($srvObj->TransactionExists($refno)){
			$error='refno_exists';
			$refno='';
		}
	}

	if(!$error){	
		//clean and check input data variables

		$encoder=trim($encoder); 
		if($encoder=='') 	$encoder=$ck_prod_db_user; 
		// save the uploaded picture
					$oktosql=true;

			$encounter = $enc_obj->getEncounter($pencnum);
			if (($encounter['encounter_type'] == 2) || ($encounter['encounter_nr'] == NULL)){
				$encounter_nr = 0;
				$encounter_type = 5;   # walkin
			}else{
				$encounter_nr = $encounter['encounter_nr'];
				$encounter_type = $encounter['encounter_type'];	
			}
				
			#$discount = $_POST['discount']; -- comment by VAN 07-20-2007
			$serv = explode(",",$_POST['serviceArray']);
			$serv_prev = explode(",",$_POST['serviceArray_prev']);
			
			/*
			echo "serv_prev = ";   #previous request list
			print_r($serv_prev);
			echo "<br> serv";		  #current request list
			print_r($serv);
			*/
			/*
			for ($i=0; $i<sizeof($serv); $i++){
				for ($j=0; $j<sizeof($serv_prev); $j++){
					if ($serv[$i] == $serv[$j]){
						$oldlist = $oldlist.$serv[$i].",";
						break; 
					}else{
						$newlist = $newlist.$serv[$i].",";
						break;
					}
				}
			}
			*/
			/*
			echo "<br> prev list size = ".sizeof($serv_prev);
			echo "<br> current list size = ".sizeof($serv);
			
			for ($i=0; $i<sizeof($serv); $i++){
				for ($j=0; $j<sizeof($serv_prev); $j++){
					if ($serv[$i] == $serv[$j]){
						$existing = $existing.$serv[$i].",";
						break;
					}
				}
			}
			
			echo "<br>existing list = ".$existing;
			#echo "<br>newlist = ".$newlist;
			*/
			$service = array();
			
			for ($i=0; $i<sizeof($serv); $i++){
				$servObj = $srvObj->getLabServiceInfo($serv[$i],$parameterselect);
				$code = $servObj['service_code'];			
				
				if ($is_cash)
					$rate = $servObj['price_cash'];
				else	
					$rate = $servObj['price_charge'];
				
				$service[$i]['code'] = $code;
				$service[$i]['rate'] = $rate;
			}
			
			#echo "service = "; #the list that should be save
			#print_r($service);
			
			if(!($update)){
				
				$refno = $srvObj->get_RefNo();
				/*
				$data=array(
					'refno'=>$refno,
					'serv_dt'=>date("YmdHis",strtotime(formatDate2STD($purchasedt, $date_format))),
					'encounter_nr'=>$encounter_nr,
					'encounter_type'=>$encounter_type,
					'pid'=>$pencnum,
					'is_cash'=>$is_cash,
					'discount'=>$discount,
					'create_id'=>$_SESSION['sess_temp_userid'],   
					'modify_id'=>$_SESSION['sess_temp_userid'],   
					'modify_dt'=>date('YmdHis'),   
					'create_dt'=>date('YmdHis'),
					'history'=>"Create: ".date('Y-m-d H:i:s')." [\\".$_SESSION['sess_temp_userid']."]\\n"
				);
				*/
				
				$data=array(
					'refno'=>$refno,
					'serv_dt'=>date("YmdHis",strtotime(formatDate2STD($purchasedt, $date_format))),
					'encounter_nr'=>$encounter_nr,
					'encounter_type'=>$encounter_type,
					'pid'=>$pencnum,
					'is_cash'=>$is_cash,
					'create_id'=>$_SESSION['sess_temp_userid'],   
					'modify_id'=>$_SESSION['sess_temp_userid'],   
					'modify_dt'=>date('YmdHis'),   
					'create_dt'=>date('YmdHis'),
					'history'=>"Create: ".date('Y-m-d H:i:s')." [\\".$_SESSION['sess_temp_userid']."]\\n"
				);
				
				# Set core to main products
				$srvObj->useLabServ();
				$srvObj->setDataArray($data);
				$saveok=$srvObj->insertDataFromInternalArray(); 
				
				if ($parameterselect!="none"){			
					$srvObj->ClearTransactionDetails($refno,$parameterselect);
				}	
				
				if (($_POST['serviceArray']!=NULL)&&($parameterselect!="none")){
					#$ok=$srvObj->AddLabServiceDetails($refno,@explode(",",$_POST['serviceArray']),$parameterselect,@explode(",",$rate_list),$discount);
					$ok=$srvObj->AddLabServiceDetails($refno,$parameterselect,$service);
				}
				#echo "<br>sql =".$srvObj->sql."<br>";
				if ($ok) {
					echo "The Laboratory Services Requested is successfully created...<br>";
				}
				else
					print_r($db->ErrorMsg());
				
				#-------------------------
				
				$oktosql=false;
				//print("iscash:".$is_cash."<br>");
				if (!saveok) print "no save<p>".$sql."<p>$LDDbNoSave:".$pharma_obj->sql;
			}else{
						$updateok=true;
						if($srvObj->UpdateLabTransaction($refnoex, $refno, date("Y-m-d",strtotime(formatDate2STD($purchasedt, $date_format))), $encounter_nr,$encounter_type,$pencnum,$is_cash?1:0, $discount,$_SESSION['sess_temp_userid'])){   # van edited: June 18, 2007
							$saveok=true;
							
							if ($parameterselect!="none"){				
								$srvObj->ClearTransactionDetails($refno,$parameterselect);
							}	
							
							if (($_POST['serviceArray']!=NULL)&&($parameterselect!="none")){
								$ok=$srvObj->AddLabServiceDetails($refno,$parameterselect,$service);
							}
							if ($ok) {
								echo "The Laboratory Services Requested is successfully updated...<br>";
							}
							else
								print_r($db->ErrorMsg());
								
							#--------------
							
						}else{
							print "no save<p>".$sql."<p>$LDDbNoSave:".$srvObj->sql;
						}

						#if($updateok) $keyword=$bestellnum;else  $keyword=$ref_bnum;
			}
			#echo $sql;

	}
}
?>

