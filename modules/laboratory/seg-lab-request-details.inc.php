<!-- RETAIL DETAILS BLOCK -->
<script language="javascript">

function toggleDisplay2(id, mod) {
	var el=document.getElementById(id);
	//alert("toggleDisplay2 id:el = "+id+" - "+el);
	if (el) {
		if (el.style.overflow=="hidden") el.style.overflow="visible";
		else el.style.overflow="hidden";
	}
}

</script>
<center>
<br/>
	<span id="stable2">
	<h3 style="margin:4px">Clinical Laboratory Services</h3>
	<div id="listContainer2" style="width:100%">
	
	<table width="70%" border="0" cellpadding="0" cellspacing="1" id="srcRowsTable">
		<thead style="background-color:red;color:white;font-weight:bold">
			<td colspan="5">
			<table width="100%" cellpadding="2" cellspacing="2" border="0"><tr>
				<td colspan="4" style="background-color:red;color:white;font-weight:bold;padding:2px">List of Laboratory Services that is requested.</td>
				<!--<td width="1%" align="right" style="padding:2px;font-weight:normal" class="reg_header"><span class="reglink" onclick="toggleDisplay2('grpHead');toggleDisplay2('grpBody');">Show/Hide</span>-->
				<td width="1%" align="right" style="padding:2px;font-weight:normal" class="reg_header"><span class="reglink" onclick="toggleDisplay2('grpBody');">Show/Hide</span>
				
			</tr></table>
			</td>
			
		</thead>
		<thead class="reg_list_titlebar" style="height:0;overflow:visible;font-weight:bold;padding:4px;" id="srcRowsHeader">
		<!--<thead class="reg_list_titlebar" style="height:0;overflow:visible;font-weight:bold;padding:4px;" id="grpHead">-->
			<td width="1"><input id="chk_all" name="chk_all" type="checkbox" disabled></td>
			<td width="12%" nowrap>Code</td>
			<td width="50%" nowrap>Description</td>
			<td width="28%" nowrap>Group</td>
			<td width="10%">Price</td>
		</thead>
		<tbody id="grpBody" style="height:0;overflow:visible">
		 <?php
		
		 		#get the laboratory services that the patient requested
				$servreqObj = $srvObj->getRequestedServices($refno);
				#echo "sql = ".$srvObj->sql;
				#print_r($servreqObj);
		 		$count=0;
				if ($servreqObj) {
					while($result=$servreqObj->FetchRow()) {
						#$alt=($count%2!=0)+1;
						#$idd=$result['refno']."_".$result['service_code']."_".$result['group_id'];
						print "<tr class=\"wardlistrow1\">";
						print "<td><input id=\"chk_all\" name=\"chk_all\" type=\"checkbox\" disabled checked></td>";
						print "<td>".$result['service_code']."</td>";
						print "<td>".$result['name']."</td>";
						print "<td>".$result['groupnm']."</td>";
						print "<td align=\"right\">".number_format($result['rate'],2)."</td>";
						#echo "is_cash = $is_cash";
						/*
						if ($is_cash)
							print "<td align=\"right\">".number_format($result['price_cash'],2)."</td>";
						else
							print "<td align=\"right\">".number_format($result['price_charge'],2)."</td>";
						*/	
						print "</tr>";
						
						$count++;
					}
				}	
					
		?>	
			<!--
			<tr class="wardlistrow2">
				<td><input id="chk_all" name="chk_all" type="checkbox"></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			-->
			
		</tbody>
		<tbody>
		</tbody>
	</table>

	</div></span>
<br/></center>
<!-- END: RETAIL DETAILS BLOCK -->