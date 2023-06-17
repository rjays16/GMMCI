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
			
			$size_prev = sizeof($serv_prev);
			$size_cur =  sizeof($serv);
			
			if ($size_prev < $size_cur) {
				for ($i=0; $i<sizeof($serv); $i++){
					if (in_array($serv[$i], $serv_prev)) {
						$existing = $existing.$serv[$i].",";
					}else{
						$not_existing = $not_existing.$serv[$i].","; 
					}
				}
				
				for ($i=0; $i<sizeof($serv_prev); $i++){
					if (in_array($serv_prev[$i], $serv)) {
						$existing2 = $existing2.$serv_prev[$i].",";
					}else{
						$not_existing2 = $not_existing2.$serv_prev[$i].",";
					}
				}
				
			}elseif($size_prev > $size_cur){
				
				for ($i=0; $i<sizeof($serv); $i++){
					if (in_array($serv[$i], $serv_prev)) {
						$existing2 = $existing2.$serv[$i].",";
					}else{
						$not_existing2 = $not_existing2.$serv[$i].","; 
					}
				}
				
				for ($i=0; $i<sizeof($serv_prev); $i++){
					if (in_array($serv_prev[$i], $serv)) {
						$existing = $existing.$serv_prev[$i].",";
					}else{
						$not_existing = $not_existing.$serv_prev[$i].",";
					}
				}
			}elseif($size_prev == $size_cur){
				
				for ($i=0; $i<sizeof($serv); $i++){
					if (in_array($serv[$i], $serv_prev)) {
						$existing = $existing.$serv[$i].",";
					}else{
						$not_existing = $not_existing.$serv[$i].","; 
					}
				}
				
				for ($i=0; $i<sizeof($serv_prev); $i++){
					if (in_array($serv_prev[$i], $serv)) {
						$existing2 = $existing2.$serv_prev[$i].",";
					}else{
						$not_existing2 = $not_existing2.$serv_prev[$i].",";
					}
				}
			}
			
			$existinglist = substr($existing, 0, strlen($existing)-1);  
			$serv_existing = explode(",",$existinglist);

			#echo "<br>existing array =";
			#print_r($serv_existing);
			
			$existinglist2 = substr($existing2, 0, strlen($existing2)-1);  
			$serv_existing2 = explode(",",$existinglist2);
			
			#echo "<br>existing array2 =";
			#print_r($serv_existing2);
			
			$not_existinglist = substr($not_existing, 0, strlen($not_existing)-1);  
			$serv_not_existing = explode(",",$not_existinglist);

			#echo "<br>not existing array =";
			#print_r($serv_not_existing);
			
			$not_existinglist2 = substr($not_existing2, 0, strlen($not_existing2)-1);  
			$serv_not_existing2 = explode(",",$not_existinglist2);
			
			#echo "<br>not existing array2 =";
			#print_r($serv_not_existing2);
			
			$service = array();
			$service2 = array();
			
			#mode = save
			if (!($update)) {  
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
			} else{   #mode = update
			
				for ($i=0; $i<sizeof($serv_not_existing); $i++){
					$servObj = $srvObj->getLabServiceInfo($serv_not_existing[$i],$parameterselect);
					$code = $servObj['service_code'];			
				
					if ($is_cash)
						$rate = $servObj['price_cash'];
					else	
						$rate = $servObj['price_charge'];
				
					$service[$i]['code'] = $code;
					$service[$i]['rate'] = $rate;
				}
				
				for ($j=0; $j<sizeof($serv_not_existing2); $j++){
					$servObj2 = $srvObj->getLabServiceInfo($serv_not_existing2[$j],$parameterselect);
					$code2 = $servObj2['service_code'];			
				
					if ($is_cash)
						$rate2 = $servObj2['price_cash'];
					else	
						$rate2 = $servObj2['price_charge'];
				
					$service2[$j]['code'] = $code2;
					$service2[$j]['rate'] = $rate2;
				}
			}
			
			#echo "<br>service = "; #the list that should be save
			#print_r($service);
			
			#echo "<br>service2 = "; #the list that should be save
			#print_r($service2);
			
			if(!($update)){
				
				#get reference no.
				
				#$refno = $srvObj->get_RefNo();
				if($GLOBAL_CONFIG['refno_fullyear_prepend']){ 
					$ref_nr=(int)date('Y').$GLOBAL_CONFIG['refno_init'];
				}else{ 
					$ref_nr=$GLOBAL_CONFIG['refno_init'];
				}	
				
				$new_ref_nr = $ref_nr+$GLOBAL_CONFIG['refno_serv_adder'];
				
				$refno = $srvObj->getNewRefno($new_ref_nr);
				
				#echo "<br>refno = ".$refno."<br>";
				
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
					echo "The Laboratory Service is successfully created...<br>";
				}
				else
					print_r($db->ErrorMsg());
				
				#-------------------------
				
				$oktosql=false;
				//print("iscash:".$is_cash."<br>");
				if (!saveok) print "no save<p>".$sql."<p>$LDDbNoSave:".$pharma_obj->sql;
			}else{
						$updateok=true;
						$history = $srvObj->ConcatHistory("Update: ".date('Y-m-d H:i:s')." [\\".$_SESSION['sess_temp_userid']."]\\n");
						if($srvObj->UpdateLabTransaction($refnoex, $refno, date("Y-m-d",strtotime(formatDate2STD($purchasedt, $date_format))), $encounter_nr,$encounter_type,$pencnum,$is_cash?1:0, $discount,$_SESSION['sess_temp_userid']), $history){   # van edited: June 18, 2007
							$saveok=true;
							
							/*
							if ($parameterselect!="none"){				
								$srvObj->ClearTransactionDetails($refno,$parameterselect);
							}	
							
							if (($_POST['serviceArray']!=NULL)&&($parameterselect!="none")){
								$ok=$srvObj->AddLabServiceDetails($refno,$parameterselect,$service);
							}
							*/
						
							if (($serv_not_existing!=NULL) && ($size_cur > $size_prev)){
								if ($service2[0][code]!=NULL)
									$ok2=$srvObj->UpdateLabServiceDetails($refno,$parameterselect,$service2,0);  # delete
								if ($service[0][code]!=NULL)
									$ok=$srvObj->UpdateLabServiceDetails($refno,$parameterselect,$service,1);		# save
							}elseif(($serv_not_existing!=NULL) && ($size_cur < $size_prev)){
								if ($service[0][code]!=NULL)
									$ok=$srvObj->UpdateLabServiceDetails($refno,$parameterselect,$service,0);		# delete
								if ($service2[0][code]!=NULL)
									$ok2=$srvObj->UpdateLabServiceDetails($refno,$parameterselect,$service2,1);  # save
							}elseif(($serv_not_existing!=NULL) && ($size_cur = $size_prev)){
								if ($service2[0][code]!=NULL)	
									$ok2=$srvObj->UpdateLabServiceDetails($refno,$parameterselect,$service2,0); # delete
								if ($service[0][code]!=NULL)
									$ok=$srvObj->UpdateLabServiceDetails($refno,$parameterselect,$service,1);  # save
							}
							
							if ($ok || $ok2) {
								echo "The Laboratory Service is successfully updated...<br>";
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

