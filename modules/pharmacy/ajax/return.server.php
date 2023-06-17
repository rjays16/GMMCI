<?php
	function reset_returnNr() {
		global $db;
		$objResponse = new xajaxResponse();
		$rc = new SegPharmaReturn();
		$lastnr = $rc->getLastNr();
		if ($lastnr)
			$objResponse->call("resetNr",$lastnr);
		else
			$objResponse->call("resetNr","Error!",1);
		return $objResponse;
	}

	function populate_items( $nr, $disabled=NULL ) {
		global $db;
		$objResponse = new xajaxResponse();
		$rc = new SegPharmaReturn();
		$result = $rc->GetReturnItems($nr);
		$objResponse->call("clearItems",NULL);
		$rows = 0;
		if ($result) {
			$rows=$result->RecordCount();
			while ($row=$result->FetchRow()) {
				$obj = (object) 'details';
				$obj->id = $row["bestellnum"];
				$obj->ref = $row["ref_no"];
				$obj->name= $row["artikelname"];
				$obj->generic= $row["generic"];
				$obj->previous = $row["previous_returns"];
				$obj->price = $row["price"];
				$obj->qty = $row["quantity"];
				$obj->returned = $row["returned"];
				$objResponse->call("appendItem", $obj, $disabled);
			}
			if (!$rows) $objResponse->call("appendItem",NULL,NULL);
		}
		else {
			if (true) {
				$objResponse->call("display",$rc->sql);
				# $objResponse->alert($sql);
			}
			else {
				$objResponse->alert("A database error has occurred. Please contact your system administrator...");
			}
		}
		return $objResponse;
	}

	//added by cha, july 8, 2010 *from BPH
	function returnItem( $encounter_nr, $id, $quantity, $returnNr=null ) {
	global $db;

	$objResponse = new xajaxResponse();
	$rc = new PharmacyReturn();
	$cutoff = $rc->GetCutOff();

	$quantity_left = $quantity;

	if ($quantity_left > 0) {

		$sql =
			"SELECT bestellnum `code`,artikelname `name`,generic\n".
			"FROM care_pharma_products_main\n".
			"WHERE bestellnum=".$db->qstr($id);
		if ( ($info=$db->GetRow($sql))==false ) {
			$objResponse->alert("SQL error:\n".$sql);
			return $onjResponse;
		}

		$where = array();
		$where[] = "o.is_cash=0";
		$where[] = "oi.bestellnum=".$db->qstr($id);
		$where[] = "oi.serve_status='S'";
		$where[] = "oi.serve_dt>=DATE(NOW()-INTERVAL $cutoff)";

		$parent = $db->GetOne('SELECT parent_encounter_nr FROM care_encounter WHERE encounter_nr='.$db->qstr($encounter_nr));
		if ($parent) {
			$where[] = "o.encounter_nr=".$db->qstr( $encounter_nr )." OR o.encounter_nr=".$db->qstr( $parent );
		}
		else {
			$where[] = "o.encounter_nr=".$db->qstr( $encounter_nr );
		}

		$sql =
			"SELECT oi.refno,oi.pricecash,oi.quantity,\n".
				"(SELECT IFNULL(SUM(IFNULL(ri.quantity,0)),0) FROM seg_pharma_return_items ri\n".
					"WHERE ri.ref_no=oi.refno AND ri.bestellnum=oi.bestellnum".
						($returnNr ? (" AND ri.return_nr!=".$db->qstr($returnNr)) : "").") returned\n".
			"FROM seg_pharma_order_items oi\n".
			"INNER JOIN seg_pharma_orders o ON o.refno=oi.refno\n".
			"INNER JOIN care_pharma_products_main p ON p.bestellnum=oi.bestellnum\n".
			"WHERE (".implode(")\nAND (",$where).")\n".
			"ORDER BY oi.serve_dt ASC\n";

		//$objResponse->alert($sql);
		$result = $db->Execute( $sql );
		if ( $result !== FALSE ) {
			while ($row=$result->FetchRow()) {
				$r_returned = (int) $row['returned'];
				$r_quantity = (int) $row['quantity']-$r_returned;

				if ($r_quantity>0) {
					if ($quantity_left <= $r_quantity) {
						$r_quantity = $quantity_left;
						$quantity_left=0;
					}
					else {
						$quantity_left=$quantity_left-$r_quantity;
					}

					$d = array(
						'id' => $id,
						'ref' => $row['refno'],
						'qty' => (int) $row['quantity'],
						'previous' => (int) $row['returned'],
						'returned' => $r_quantity,
						'price' => (float) $row['pricecash'],
						'name' => $info['name'],
						'generic' => $info['generic']
					);
					$objResponse->call('appendItem', $d);
//					$objResponse->alert("Ref:".$row['refno']."\nRet:".$r_quantity."\n"."Left:".$quantity_left  );
				}
				if ( $quantity_left == 0 ) {
					$objResponse->alert('Item successfully added to list of returnables...');
					return $objResponse;
				}
			}
			return $objResponse;
		}
		else {
			// SQL error
			$objResponse->alert("SQL error:\n".$sql);
			return $objResponse;
		}
	}
	else {
		$objResponse->alert('Invalid quantity specified...');
		return $objResponse;
	}
} //end of function returnItem() *from BPH

	//added by cha, july 8, 2010
	function populate_info( $nr, $disabled=NULL ) {
		global $db;
		$objResponse = new xajaxResponse();
		$rc = new PharmacyReturn();
		$result = $rc->GetReturnInfo($nr);
		$objResponse->alert($rc->sql);
		if ($result) {
			$objResponse->alert(print_r($result,true));
		}
		else {
			if (true) {
				$objResponse->call("display",$rc->sql);
			}
			else {
				$objResponse->alert("A database error has occurred. Please contact your system administrator...");
			}
		}
		return $objResponse;
	}//end of function populate_info

	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
	require($root_path.'include/care_api_classes/class_pharma_return.php');
	require_once($root_path.'include/care_api_classes/pharmacy/class_return.php');
	require($root_path."modules/pharmacy/ajax/return.common.php");
	$xajax->processRequest();
