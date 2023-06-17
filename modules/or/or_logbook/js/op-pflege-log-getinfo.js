
gui_addQuicklist(docId,docName,entrycount,counter){
	
//	 counter = 0
//	Awhile($qlist=$quicklist->FetchRow())
//		{
//			#print_r($qlist);
	newRowSrc = '<tr bgcolor="#ffffff">' +
    			  '<td class="v13" >'+
				    '&nbsp;<a href="javascript:savedata(\''.$qlist[name_last].'\',\''.$qlist[name_first].'\',document.quickselect.f'.$counter.',\''.$qlist[jof_function_title].'\')" title="'.str_replace("~tagword~",$title,$LDUseData).'">'.$qlist[name_last].', '.$qlist[name_first].'</a>' + 
				   '</td> ' +
    			'<td class="v13" >' +
				    '&nbsp;'.$qlist['job_function_title'].' ' +
				'</td>'+
    				'<td   class="v13" ><select name="f'.$counter.'">';
    				
				 if(!$entrycount) $entrycount=1;
				for($i=1;$i<=($entrycount);$i++)
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
		
	

}