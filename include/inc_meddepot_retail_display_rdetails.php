 <?php
/*------begin------ This protection code was suggested by Luki R. luki@karet.org ---- */
if (eregi('inc_meddepot_retail_display_rdetails.php',$PHP_SELF)) 
	die('<meta http-equiv="refresh" content="0; url=../">');
/*------end------*/
$rdetails = "";
if ($saveok && $refno) {
	$result=$med_obj->GetTransactionDetails($refno);
	$result->fetchMode=MYSQL_BOTH;
	// ".print_r($result->_queryID, TRUE)."
	$rdetails = "
	<table border=0 cellpadding=2 cellspacing=1 style=\"margin:5px 0px\" width=\"100%\">
		<tr>
			<td colspan=6>
			</td>
		</tr>
		<tr class=\"reg_list_titlebar\">
			<td width=\"50%\"><b>Product name</b></td>
			<td width=\"10%\"><b>Qty</b></td>
			<td width=\"15%\"><b>Price per pack</b></td>
			<td width=\"10%\"><b>Package</b></td>
			<td width=\"7%\">&nbsp;</td?
			<td width=\"8%\">&nbsp;</td?
		</tr>";
	$counter=0;
	if ($pharma_obj->count==0) {
		$rdetails.="
		<tr class=\"wardlistrow1\">
			<td colspan=\"6\">No transaction details found...</td>
		</tr>";
	}
	else {
		mysql_data_seek($result->_queryID,0);
		while ($row=mysql_fetch_array($result->_queryID,MYSQL_BOTH)) {
			$rdetails.="
		<tr class=\"".(($counter++)%2==0?"wardlistrow1":"wardlistrow2")."\">
			<td>".$row["artikelname"]."</td>
			<td>".round($row["qty"],2)."</td>
			<td>".round($row["rpriceppk"],2)."</td>
			<td>".$row["pack"]."</td>
			<td>
				<a href=\"javascript:clientPrepareEdit('".$row["bestellnum"]."',".$row["entrynum"].",".round($row["qty"],2).",".round($row["rpriceppk"],2).",'".$row["pack"]."')\">Edit</a></td>
			<td><a href=\"javascript:clientPrepareDelete('".$row["bestellnum"]."',".$row["entrynum"].")\">Delete</a></td>
		</tr>
";
		}
  }

}


?>