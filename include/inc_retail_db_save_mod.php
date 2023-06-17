 <?php
/*------begin------ This protection code was suggested by Luki R. luki@karet.org ---- */
if (eregi('inc_retail_db_save_mod.php',$PHP_SELF)) 
	die('<meta http-equiv="refresh" content="0; url=../">');
/*------end------*/

//if(isset($cat)&&($cat=='pharma'))
	$dbtable='seg_pharma_retail';
//	else $dbtable='care_med_products_main';


// if mode is save then save the data
if(isset($mode)&&($mode=='save')){

    $saveok=false;
    $error=false;
    $error_bnum=false;
    $error_name=false;
    $error_besc=false;
    $error_minmax=false;


/*    $bestellnum=trim($bestellnum);
	if ($bestellnum=='') { $error_bnum=true; $error=true;};
    $artname=trim($artname); 
	if ($artname=='') { $error_name=true; $error=true; };
    $besc=trim($besc);
	if ($besc=='') { $error_besc=true; $error=true; };
	
	if(!is_numeric($minorder)) $minorder=NULL;
	if(!is_numeric($maxorder)) $maxorder=NULL;
	$proorder=(int)$proorder;
	
	if($maxorder&&$minorder>$maxorder){ $error_minmax=true; $error=true;}
	# Default nr.of pcs. pro order is 1
	if(!$proorder) $proorder=1;
	*/
		$refno=trim($refno);
		
    if(!$update){	
		# check if order number exists
		if($pharma_obj->TransactionExists($refno)){
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
					
			if(!($update)){
			  $data=array(
					'refno'=>$refno,
					'purchasedte'=>date("Y-m-d",strtotime(formatDate2STD($purchasedt, $date_format))),
					'encounter_nr'=>$pencnum,
					'is_cash'=>$is_cash,
					'create_id'=>$_SESSION['sess_temp_userid'],   # burn added: August 9, 2006
					'modify_id'=>$_SESSION['sess_temp_userid'],   # burn added: August 9, 2006
					'modify_dt'=>date('YmdHis'),   # burn added: August 10, 2006
//					'create_id'=>$HTTP_SESSION_VARS['sess_user_name'],
					'create_dt'=>date('YmdHis')
				);
				
				# Set core to main products
				$pharma_obj->usePharmaRetail();
				$pharma_obj->setDataArray($data);
				$saveok=$pharma_obj->insertDataFromInternalArray();
				$oktosql=false;
				//print("iscash:".$is_cash."<br>");
				if (!saveok) print "no save<p>".$sql."<p>$LDDbNoSave:".$pharma_obj->sql;
			}else{
					 	$updateok=true;
						if($pharma_obj->UpdatePharmaTransaction($refnoex, $refno, date("Y-m-d",strtotime(formatDate2STD($purchasedt, $date_format))), $pencnum, $is_cash?1:0, $_SESSION['sess_temp_userid']))   # burn added: August 9, 2006
//						if($pharma_obj->UpdatePharmaTransaction($refnoex, $refno, $purchasedt, $pencnum, $is_cash?1:0, $HTTP_SESSION_VARS['sess_user_name']))
							$saveok=true;
						else{print "no save<p>".$sql."<p>$LDDbNoSave:".$pharma_obj->sql;};

						#if($updateok) $keyword=$bestellnum;else  $keyword=$ref_bnum;
			}
			#echo $sql;

	}
}
?>
