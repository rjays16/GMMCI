//created by cha 06-11-09

function listORPersonnel(listID)
{
  if(listID=='surgeons')
  {
    //alert('surgeons'); 
    xajax_populateORPersonnel('Surgeon');
  }
  if(listID=='asstsurgeons') 
  {
    //alert('asst surgeons');
    xajax_populateORPersonnel('Assisting Surgeon'); 
  }
  if(listID=='anesth')
  {
    //alert('abesthesiologists');
    xajax_populateORPersonnel('Anesthesiologist');  
  }
  if(listID=='nurses') 
  {
    //alert('nurses');
    xajax_populateORPersonnel('Nurse'); 
  }
}

function viewORSchedList(name,)
{
    var list=$(listID), dRows, dBody, rowSrc;

    if (list) {
        dBody=list.getElementsByTagName("tbody")[0];
        dRows=dBody.getElementsByTagName("tr");
        if (or_no) {
            alt = (dRows.length%2)+1;

            rowSrc = '<tr class="wardlistrow'+alt+'" id="row'+addslashes(or_no)+'" value="'+or_no+'">'+
                            '<td>&nbsp;</td>'+
                            '<td width="5%" align="left" ><span style="font:bold 11px Arial;color:#660000">'+or_no+'</span></td>'+ 
                            '<td width="15%" align="left" id="date">'+date+'</td>'+
                         '<td width="15%" align="left" id="name">'+name+'</td>'+
                            '<td width="5%" align="right" id="status">'+status+'</td>'+
                            '<td width="10%" align="center" id="newOR"><input type="text" id="'+or_no+'" size="10" value="" onblur="callAlert(this.value,this.id);"></input><input type="hidden" id="origOR'+or_no+'" value="'+or_no+'"></input><input type="hidden" id="hiddenOR" value="'+or_no+'"></input></td>'+
                      '</tr>';        
        } 
        else {
            rowSrc = '<tr><td colspan="6" style="">OR number does not exist...</td></tr>';
        }
        dBody.innerHTML += rowSrc;
        or_no=0;
        date="";
        name="";
        status="";
    }
}