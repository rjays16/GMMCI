<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
define('LANG_FILE','or.php');
$local_user='ck_op_pflegelogbuch_user';
require_once($root_path.'include/inc_front_chain_lang.php');


#require_once($root_path.'modules/or_logbook/ajax/op_common.php');

/* Create the personell object */
require_once($root_path.'include/care_api_classes/class_personell.php');
$pers_obj=new Personell;

	# Create operation billing object
require_once($root_path.'include/care_api_classes/billing/class_ops.php');
$ops_obj = new SegOps;


$title=$LDOpPersonElements[$winid];


switch($winid)
{
	case 'operator': 
							$element='operator';
							//$maxelement=10;
							$quickid='doctor';
							$quicklist=$pers_obj->getDoctorsOfDept($dept_nr);
							break;
	case 'assist': 
							$element='assistant';
							//$maxelement=10;
							$quickid='doctor';
							$quicklist=$pers_obj->getDoctorsOfDept($dept_nr);
							break;
	case 'scrub': 
							$element='scrub_nurse';
							//$maxelement=10;
							$quickid='nurse';
							$quicklist=$pers_obj->getNursesOfDept($dept_nr);
							break;
	case 'rotating':
							$element='rotating_nurse';
							//$maxelement=10;
							$quickid='nurse';
							$quicklist=$pers_obj->getNursesOfDept($dept_nr);
							break;
	case 'ana':
							$element='an_doctor';
							//$maxelement=10;
							$quickid='doctor';
//							$quicklist=$pers_obj->getDoctorsOfDept(42); // 42 = anesthesiology department
							$quicklist=$pers_obj->getDoctorsOfDept(153); //153 = anesthesiology department
							break;
	default:{header('Location:'.$root_path.'language/'.$lang.'/lang_'.$lang.'_invalid-access-warning.php'); exit;}; 
}

//print_r($quicklist);

if($pers_obj->record_count) $quickexist=true;

$thisfile=basename(__FILE__);

/* Establish db connection */
if(!isset($db)||!$db) include($root_path.'include/inc_db_makelink.php');
if($dblink_ok){	
	// get data if exists
	$dbtable='care_encounter_op';
	$sql="SELECT $element,encoding, nr AS op_request_nr FROM $dbtable
					 WHERE encounter_nr='$enc_nr'
					 AND dept_nr='$dept_nr'
					 AND op_nr='$op_nr'
					 AND op_room='$saal'";

	if($ergebnis=$db->Execute($sql)){
		$rows=$ergebnis->Recordcount();
		if($rows){
			$result=$ergebnis->FetchRow();
			$fileexist=1;
			//echo $sql."<br>";
//			echo "ergebnis->FetchRow() <br>";
//			print_r($result);
//			echo "<br>";
		}
	}else{
		echo "$LDDbNoRead<br>";
	} 
	if (!isset($op_request_nr) || !$op_request_nr){
		$op_request_nr = $result['op_request_nr'];
	}
/*
echo "op-pflege-log-getinfo.php : dblink_ok = '".$dblink_ok."' <br> \n";
echo "op-pflege-log-getinfo.php : sql = '".$sql."' <br> \n";
echo "op-pflege-log-getinfo.php : result['nr'] = '".$result['nr']."' <br> \n";
echo "op-pflege-log-getinfo.php : result['op_request_nr'] = '".$result['op_request_nr']."' <br> \n";
echo "op-pflege-log-getinfo.php : 2 op_request_nr = '".$op_request_nr."' <br> \n";
*/
		if($mode=='save')
		{
					$dbtable='care_encounter_op';
					//$encoder=$ck_op_pflegelogbuch_user; 
					if($fileexist){

//    						comment by mark on July 17, 2007
//							$dbuf=htmlspecialchars($dbuf); //old_commented
//							$result[encoding].=" ~e=".$encoder."&d=".date("d.m.Y")."&t=".date("H.i")."&a=".$element;
							
							//For encoding Update
							$res = array();
							$res['encoder'] = $encoder;
							$res['date'] = date("d-m-Y");
							$res['time'] = date("H:i");
							$res['a']= $element;
							
							//assign info to result['encoding'] for updating the journal 
							$result['encoding'] .= "~".$nx.serialize($res);
							
							$tmp_ref_buffer = unserialize($result['encoding']);
#							echo "tmp_ref_buffer= "; print_r($tmp_ref_buffer); echo "<br> \n";

							//print_r ($result[encoding]);
							//$elem=explode("~",trim($result[$element]));

							//Unresialize into associative array
							$elem = unserialize($result[$element]);
#							echo "<br> elem=".$elem; print_r($elem);
							
//-------------------------- end New Inserted Code ---------------------------------
							if($delitem!=""){
							   
//								$elem=explode("~",trim($result[$element]));
//								if(!$elem[0]) array_splice($elem,0,1);  old
//								array_splice($elem,$delitem,1);
//								sort($elem,SORT_REGULAR);
//								$result[$element]=implode("~",$elem);
								
								array_splice($elem, $delitem,1);
#								print_r($elem);
#							    $tmp_result = serialize($elem);
#								$result[$element] = $tmp_result;		
/*
echo "op-pflege-log-getinfo.php : after array_splice : elem = '".$elem."' <br> \n";
echo "op-pflege-log-getinfo.php : after array_splice : "; print_r($elem); echo " <br> \n";
*/
								$i=1;
								foreach($elem as $key=>$value){
									$tmp_elem[$element.'+'.$i] = $value;
									$i++;
								}
//								sort($elem,SORT_REGULAR);
								$result[$element] = serialize($tmp_elem);	
/*
echo "op-pflege-log-getinfo.php : after sort : tmp_elem = '".$tmp_elem."' <br> \n";
echo "op-pflege-log-getinfo.php : after sort : tmp_elem : <br>"; print_r($tmp_elem); echo " <br> \n";
echo "op-pflege-log-getinfo.php : element = '".$element."' <br> \n";
echo "op-pflege-log-getinfo.php : result[$element] = '".$result[$element]."' <br> \n";
*/								
								
							}else{

//								$sbuf=$result[$element]." ~n=".$ln.",+".$fn."&x=".$nx;
							 	$dbuf=explode("~",$result[$element]);
/*								
								echo "<br> dbuf=".$dbuf; print_r($dbuf); echo "<br> \n";
								echo "<br>Surgeon name=".$ln." fn=".$fn." x= ".$nx;
								echo "<br> personell_nr=".$personell_nr."&x=".$nx;
*/								
								$opArr = '';
								$opArr = $elem;
#								echo "<br> opArr=".$opArr; print_r($opArr); echo "<br>";
								
								//Assign operator+n = personell_nr of a surgeon being selected								
								$opArr[$element."+".$nx] = $personell_nr;
								
								// serialize the array for preperation on updating 
								//operator field of care_encounter_op table
								$tmp_opArr = serialize($opArr);
								$result[$element] = $tmp_opArr;
								
								//displaying result for checking 
#								echo "<br> result[element]= ".$result[$element];
								$unserialize = unserialize($result[$element]);
/*
								echo "<br> unserialize =".$unserialize;print_r($unserialize);
								echo "<br> $element+1 =".$unserialize[$element.'+1'];
								echo "<br> $element+2 =".$unserialize[$element.'+2'];
								echo "<br> $element+3 =".$unserialize[$element.'+3'];
*/								
//								$opArray[$element]  = $tmp_dbuffer[$element."+".$nx];
//								comment by mark on July 18, 2007
//								$dbuf[]="n=".$ln.",+".$fn."&x=".$nx;   
//								sort($dbuf,SORT_REGULAR);
//								$result[$element]=implode("~",$dbuf);
								
								//$result[$element]=$result[$element]." ~n=".$ln.",+".$fn."&x=".$nx;
//----------------------------------------------------------------------------------------------------end_old
								
							}
							//echo $result[$element];
							$sql="UPDATE $dbtable SET $element='".$result[$element]."',encoding='$result[encoding]'
					 				WHERE encounter_nr='$enc_nr'
					 						AND dept_nr='$dept_nr'
					 						AND op_nr='$op_nr'
					 						AND op_room='$saal'";
											
							if($ergebnis=$db->Execute($sql)){
									//echo $sql." new update <br>";

									#update the `seg_ops_personell`
									$ops_obj->updateOpsPersonellFromNurseJournal($op_request_nr,$element);   # burn added : October 10, 2007

									$saveok=1;
							}else { echo "$LDDbNoSave<br>"; }

								
								// else create new entry
						}else{
							// get the orig patient data
							$dbtable='care_admission_patient';
							$sql="SELECT name,vorname,gebdatum,address FROM $dbtable WHERE patnum='$patnum'";
							
							$myArray = array();
							$myArray[$element."-".$nx]  = $personell_nr;
							$tmp_myArray = serialize($myArray);
							
							//'n=".$ln.",+".$fn."&x=".$nx."',
							
							if($ergebnis=$db->Execute($sql))
       						{
								$rows=0;
								if( $result=$ergebnis->FetchRow()) $rows++;
								if($rows)
								{
									mysql_data_seek($ergebnis,0);
									$result=$ergebnis->FetchRow();		
									$dbtable='care_encounter_op';
									$sql="INSERT INTO $dbtable 
										(
										year,
										dept_nr,
										op_room,
										op_nr,
										op_date,
										encounter_nr,
										$element,
										encoding,
										doc_date,
										doc_time
										)
									 	VALUES
										(
										'$pyear',
										'$dept_nr',
										'$saal',
										'$op_nr',
										'".$pday.".".$pmonth.".".$pyear."',
										'$enc_nr',
										'$tmp_myArray',
										'e=".$encoder."&d=".date("d.m.Y")."&t=".date("H.i")."&a=".$element."',
										'".date("d.m.Y")."',
										'".date("H.i")."'
										)";

									if($ergebnis=$db->Execute($sql))
       								{
										//echo $sql." new insert <br>";
										$saveok=1;
									}
									else { echo "$LDDbNoSave<br>"; } 
								 } // end of if rows
							} // end of if ergebnis
								else { echo "$LDDbNoRead<br>"; } 
						}//end of else
					if($saveok)
						{
							header("location:$thisfile?sid=$sid&lang=$lang&mode=saveok&winid=$winid&enc_nr=$enc_nr&dept_nr=$dept_nr&saal=$saal&pyear=$pyear&pmonth=$pmonth&pday=$pday&op_nr=$op_nr");
						}
				}// end of if(mode==save)
			else $saved=0;
}
  else { echo "$LDDbNoLink<br>"; } 


?>

<?php html_rtl($lang); ?>
<HEAD>
<?php echo setCharSet(); ?>
<TITLE><?php echo $title ?></TITLE>

<script language="javascript">
<!-- 
  function resetinput(){
	document.infoform.reset();
	}

  function pruf(d){
	if(!d.inputdata.value) return false;
	else return true
	}

function refreshparent()
{
	<?php $comdat="&dept_nr=$dept_nr&saal=$saal&thisday=$pyear-$pmonth-$pday&op_nr=$op_nr"; ?>
	//resetlogdisplays();resettimebars();resettimeframe();
	window.opener.parent.LOGINPUT.location.replace('<?php echo "oploginput.php?sid=$sid&lang=$lang&enc_nr=$enc_nr&mode=notimereset$comdat"; ?>');
	window.opener.parent.OPLOGMAIN.location.replace('<?php echo "oplogmain.php?sid=$sid&lang=$lang&gotoid=$enc_nr$comdat"; ?>');
}

function delete_item(i)
{
	d=document.infoform;
	d.action="<?php echo $thisfile ?>";
	d.delitem.value=i;
	d.inputdata.value="?";
	d.submit();
}
function savedata(iln,ifn,inx,ipr,personell_nr)
{
	if (isExistInList(iln,ifn,personell_nr))
		return;
	x=inx.selectedIndex;
	//urlholder="<?php echo $forwardfile ?>&ln="+ln+"&fn="+fn+"&nx="+d[x].value;
	//window.location.replace(urlholder);
	d=document.quickselect;
	d.ln.value=iln;
	d.fn.value=ifn;
	d.pr.value=ipr;
	d.nx.value=inx[x].value;
	d.personell_nr.value = personell_nr;
	//d.inputdata.value="?";
	d.submit();
}
	/*
	 * Checks if the personnel to be included in the list is already in it.
	 *	burn added : October 11, 2007
	 */
function isExistInList(lname,fname,personell_nr){
	d=document.infoform;
	pers_nr = document.getElementsByName('pers_nr[]');
	if (pers_nr) {
		for (var i=0;i<pers_nr.length;i++) {
			if (pers_nr[i].value == personell_nr) {
				alert('"'+fname+' '+lname+'" is already in the list!');
				return true;
			}
		}
	}
	return false;
}
-->
</script>
<?php
require($root_path.'include/inc_js_gethelp.php');
require($root_path.'include/inc_css_a_hilitebu.php');
//echo '<script type="text/javascript" src ="'.$root_path.'modules/or_logbook/js/op-pflege-log-getinfo.js"></script>';
//$xajax->printJavascript($root_path.'classes/xajax-0.2.5');
?>

<STYLE type=text/css>
div.box { border: double; border-width: thin; width: 100%; border-color: black; }
.v12 { font-family:verdana,arial;font-size:12; }
.v13 { font-family:verdana,arial;font-size:13; }
.v13_n { font-family:verdana,arial;font-size:13; color:#0000cc}
.v10 { font-family:verdana,arial;font-size:10; }
</style>





</HEAD>
<BODY   bgcolor="#cde1ec" TEXT="#000000" LINK="#0000FF" VLINK="#800080"  topmargin=2 marginheight=2 
onLoad="<?php if($mode=="saveok") echo "refreshparent();window.focus();"; ?>if (window.focus) window.focus();
				window.focus();document.infoform.inputdata.focus();" >
<a href="javascript:gethelp('oplog.php','person','<?php echo $winid ?>')"><img <?php echo createLDImgSrc($root_path,'hilfe-r.gif','0') ?> alt="<?php echo $LDHelp ?>" align="right"></a>
<form name="infoform" action="op-pflege-log-getpersonell.php" method="post" onSubmit="return pruf(this)">
				
<font face=verdana,arial size=5 color=maroon>
<b>
<?php 
	echo $title.'<br><font size=4>';	
?>
</b>
</font>
<p>
<table border=0 width=100% bgcolor="#6f6f6f" cellspacing=0 cellpadding=0 >
  <tr>
    <td>
<table border=0 width=100% cellspacing=1 cellpadding=0>
  <tr>
    <td  bgcolor="#cfcfcf" class="v13" colspan=6>&nbsp;<b><?php echo $LDCurrentEntries ?>:</b></td>
  </tr>
  <tr  class="v13_n">
    <td align=center bgcolor="#ffffff">
	</td>     <td align=center bgcolor="#ffffff" width="20%">
<!-- <?php echo "$LDLastName, $LDName" ?>
 -->	</td> 
    <td align=center bgcolor="#ffffff">
<?php echo $LDFunction ?>
	</td> 

    <td align=center bgcolor="#ffffff">
<?php echo $LDFrom ?>:
	</td> 

    <td align=center bgcolor="#ffffff" >
<?php echo $LDTo ?>:
	</td> 
    <td bgcolor="#ffffff">
&nbsp;<?php echo $LDExtraInfo ?>:
	</td> 
  </tr>	

<?php 


if($result[$element]!="") 
{
	//echo $result[$element];
	//$dbuf=explode("~",trim($result[$element]));
	$tmp_dbuf = unserialize(trim($result[$element]));
		//if(!$dbuf[0]) array_splice($dbuf,0,1);

		//$entrycount=sizeof($dbuf);
		$entrycount = sizeof($tmp_dbuf);
/*
		echo "<br> entrycount=>".$entrycount;
		
		echo "<br> outer_tmp_dbuf = ".$tmp_dbuf."<br>\n";
		print_r($tmp_dbuf);
		echo "<br> \n";
*/		
		$j='';
		$elems=array();
		for($i=0;$i<$entrycount;$i++)
		{
			//if(trim($dbuf[$i])=="") continue;
			$j = $i + 1;
			if(trim($tmp_dbuf[$element."+".$j]) == "") continue; 
			#echo "parse_str=".parse_str(trim($dbuf[$i]),$elems);
//			parse_str(trim($dbuf[$i]),$elems);

#			echo "<br>personell_nr= ".$tmp_dbuf[$element."+".$j];
			
			if($pers_obj->loadPersonellData($tmp_dbuf[$element."+".$j])){
				$elems1 = $pers_obj->personell_data['name_last'];
				$elems2 = $pers_obj->personell_data['name_first'];
				$elems[n] = $elems1.", ".$elems2;
			}

			echo '
	  		<tr bgcolor="#ffffff">
    			<td   class="v13" >
				&nbsp;<a href="javascript:delete_item(\''.$i.'\')"><img '.createComIcon($root_path,'delete2.gif','0').' alt="'.$LDDeleteEntry.'"></a>
				<input type="hidden" name="pers_nr[]" value="'.$tmp_dbuf[$element."+".$j].'">
				</td> 
    			<td   class="v13" >
				&nbsp;'.$elems[n].'
				</td> 
    			<td class="v13" >
				&nbsp;'.$title.' '.$elems[x].'
				</td> 
    			<td class="v13" >
				&nbsp;'.$elems[s].'<input type="text" name="ab" size=5 maxlength=5 value="">
				</td> 
    			<td class="v13" >
				&nbsp;'.$elems[e].'<input type="text" name="bis" size=5 maxlength=5 value="">
				</td> 
    			<td class="v13" >
				&nbsp;'.$elem[x].'<input type="text" name="x_info" size=30 maxlength=5 value="">
				</td> 
  				</tr>';
		}
}
 else
 
 {
 echo '
  <tr>'; 
for($i=0;$i<6;$i++)
echo '
    <td align=center bgcolor="#ffffff" align=center  class="v13" >
		&nbsp;
	</td> ';
echo'
  </tr>	';
  }
?>

  		<tr>
   			 <td  class="v12"  bgcolor="#cfcfcf" colspan=6>&nbsp;</td>
		</tr>
  		<tr>
   			 <td  class="v12"  bgcolor="#ffffff" colspan=6 align=center>
				 <font size=3><b><?php echo str_replace("~tagword~",$title,$LDSearchNewPerson) ?>:</b>	<br>
				 <input type="text" name="inputdata" size=25 maxlength=30><br> <input type="submit" value="OK">				 
			 </td>
		  </tr>
    </table>
  </td>
 </tr>
</table>

<input type="hidden" name="encoder" value="<?php echo $HTTP_COOKIE_VARS[$local_user.$sid]; ?>">
<input type="hidden" name="sid" value="<?php echo $sid ?>">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<input type="hidden" name="winid" value="<?php echo $winid ?>">
<input type="hidden" name="pyear" value="<?php echo $pyear ?>">
<input type="hidden" name="pmonth" value="<?php echo $pmonth ?>">
<input type="hidden" name="pday" value="<?php echo $pday ?>">
<input type="hidden" name="dept_nr" value="<?php echo $dept_nr ?>">
<input type="hidden" name="saal" value="<?php echo $saal ?>">
<input type="hidden" name="op_nr" value="<?php echo $op_nr ?>">
<input type="hidden" name="enc_nr" value="<?php echo $enc_nr ?>">
<input type="hidden" name="op_request_nr" value="<?php echo $op_request_nr ?>">
<input type="hidden" name="entrycount" value="<?php if(!$entrycount) echo "1"; else echo $entrycount; ?>">
<input type="hidden" name="mode" value="save">
<input type="hidden" name="delitem" value="">
</form>
<p>
<?php if($quickexist) : ?>
<form name="quickselect" action="<?php echo $thisfile ?>" method="post">
<table border=0 width=100% bgcolor="#6f6f6f" cellspacing=0 cellpadding=0 >
  <tr>
    <td>
<table border=0 width=100% cellspacing=1>
  <tr>
	<td bgcolor="#cfcfcf" class="v13_n" colspan=4>&nbsp;<font color="#ff0000"><b><?php echo $LDQuickSelectList ?>:</b></td>
  </tr>
 <tr>
    <td align=center bgcolor="#ffffff" class="v13_n" >
<!-- <?php echo $LDLastName ?>
	</td> 
    <td align=center bgcolor="#ffffff" class="v13_n" >
<?php echo $LDName ?> -->

	</td> 
    <td align=center bgcolor="#ffffff"  class="v13_n" >
<?php echo $LDJobId ?>

	</td> 
    <td align=center bgcolor="#ffffff"   class="v13_n" >
<?php echo "$LDOr $LDFunction" ?>
	</td> 
    <td align=center bgcolor="#ffffff"   class="v13_n" >

	</td> 
  </tr>	


<?php 	$counter=0;
//		echo "<br>before_ entrycount = >".$entrycount;
		//echo "<br> quicklist=>".$quicklist;print_r($quicklist);
		$entrycount++;
		while($qlist=$quicklist->FetchRow())
		{
//			echo "inside";
//		    print_r($qlist);
			echo '
	  		<tr bgcolor="#ffffff">
    			<td class="v13" >
				&nbsp;<a href="javascript:savedata(\''.$qlist[name_last].'\',\''.$qlist[name_first].'\',document.quickselect.f'.$counter.',\''.$qlist[job_function_title].'\', \''.$qlist['personell_nr'].'\')" title="'.str_replace("~tagword~",$title,$LDUseData).'">'.$qlist[name_last].', '.$qlist[name_first].'</a>
				</td> ';
    			/*<td   class="v13" >
				&nbsp;<a href="javascript:savedata(\''.$quicklist[lastname].'\',\''.$quicklist[firstname].'\',document.quickselect.f'.$counter.',\''.$quicklist[profession].'\')" title="'.str_replace("~tagword~",$title,$LDUseData).'">'.$quicklist[firstname].'</a>
				</td> */
			echo '
    			<td class="v13" >
				&nbsp;'.$qlist['job_function_title'].'
				</td> 
    			<td   class="v13" >
				<select name="f'.$counter.'">';
				//if(!$entrycount) 
				//$entrycount++;
					for($i=1;$i<=$entrycount;$i++)
					{
						echo '
	    				<option value="'.$i.'" ';
						   if($i==$entrycount) echo "selected";
						   echo '>'.$title.' '.$i.'</option>';
					}
	    			echo '
				</select>
    
				</td> 
    			<td   class="v13" >
				&nbsp;<a href="javascript:savedata(\''.$qlist[name_last].'\',\''.$qlist[name_first].'\',document.quickselect.f'.$counter.',\''.$qlist['job_function_title'].'\', \''.$qlist['personell_nr'].'\')"><img '.createComIcon($root_path,'uparrowgrnlrg.gif','0').' align=absmiddle>
				'.str_replace("~tagword~",$title,$LDUseData).'..</a>
				</td> 
    			
  				</tr>';
				$counter++;
		}
?>
		  </table>
</td>
  </tr>
</table>
<input type="hidden" name="encoder" value="<?php echo $HTTP_COOKIE_VARS[$local_user.$sid]; ?>">
<input type="hidden" name="sid" value="<?php echo $sid ?>">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<input type="hidden" name="winid" value="<?php echo $winid ?>">
<input type="hidden" name="pyear" value="<?php echo $pyear ?>">
<input type="hidden" name="pmonth" value="<?php echo $pmonth ?>">
<input type="hidden" name="pday" value="<?php echo $pday ?>">
<input type="hidden" name="dept_nr" value="<?php echo $dept_nr ?>">
<input type="hidden" name="saal" value="<?php echo $saal ?>">
<input type="hidden" name="op_nr" value="<?php echo $op_nr ?>">
<input type="hidden" name="enc_nr" value="<?php echo $enc_nr ?>">
<input type="hidden" name="op_request_nr" value="<?php echo $op_request_nr ?>">
<input type="hidden" name="mode" value="save">
<input type="hidden" name="ln" value="">
<input type="hidden" name="fn" value="">
<input type="hidden" name="pr" value="">
<input type="hidden" name="nx" value="">
<input type="hidden" name="personell_nr" id="personell_nr" value="">

</form>
<?php endif ?>

<div align=right>
&nbsp;&nbsp;
<a href="javascript:window.close()">
<?php if($mode=='saveok')  { ?>
<img <?php echo createLDImgSrc($root_path,'close2.gif','0') ?> alt="<?php echo $LDClose ?>">
<?php }else{ ?>
<img <?php echo createLDImgSrc($root_path,'cancel.gif','0') ?> border="0" alt="<?php echo $LDClose ?>">
<?php } ?>
</a></div>
</BODY>

</HTML>
