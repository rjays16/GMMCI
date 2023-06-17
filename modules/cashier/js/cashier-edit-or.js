//created by cha 05-27-09
var from_OrNo=0;                   //value of from_OR# from user input
var to_OrNo=0;                     //value of to_OR# from user input
var old_arraylen=0;                //array length of searched OR numbers
var new_arraylen=0;                //array length of new OR numbers
var new_OR_array = new Array();    //buffer for new OR numbers
var old_OR_array = new Array();    //buffer for searched OR numbers
var new_from_ORNo=0;               //value of new from_OR# from user input
var new_to_ORNo=0;                 //value of new to_OR# from user input
var valid=0;

function startAJAXSearch(inputid,page)
{
   from_OrNo = document.getElementById("fromOrNo").value;
   to_OrNo = document.getElementById("toOrNo").value;
   var searchID=(inputid);  
   var int_from = parseFloat(from_OrNo);
   var int_to = parseFloat(to_OrNo);
   var maxORLength=7;
   if(from_OrNo.length<7)
   {
      temp_or=from_OrNo.length;
      var appendzero=maxORLength-(temp_or);
      var zero=appendZeros(appendzero);
      from_OrNo=zero+""+from_OrNo;
   }
   if(to_OrNo.length<7)
   {
      temp_or=to_OrNo.length;
      var appendzero=maxORLength-(temp_or);
      var zero=appendZeros(appendzero);
      to_OrNo=zero+""+to_OrNo;
   }
 
          
   if(int_from>int_to || int_to<int_from)
   {
      alert("Invalid Range of OR numbers");
   }
  else if(from_OrNo=="" || to_OrNo=="") 
  {
      alert("No OR number specified.");
  }
  else if( !isInteger(from_OrNo) || !isInteger(to_OrNo))  
  {
      alert("Invalid OR number format.");
  }
  else
  {
      if(searchID)
     {    
        xajax_populateORList(from_OrNo,to_OrNo);
        lastServ = searchID.value;
     }      
  } 
}

function showWarning(orno, ok, title) {
  if ($('warn_'+orno)) {
    $('warn_'+orno).style.display = (ok<0 ? '' : 'none');
    $('warn_'+orno).title = title;
  }
  if ($('ok_'+orno)) {
    $('ok_'+orno).style.display = (ok>0 ? '' : 'none');
    $('ok_'+orno).title = title;
  }
  if ($(orno)) {
    if (ok>0) $(orno).style.color = "#004000";
    else if (ok<0) $(orno).style.color = "#c00000";
    else $(orno).style.color = "";
  }
}

function viewORList(listID, or_no, date, name, status, encoder)
{
    var list=$(listID), dRows, dBody, rowSrc;

    if (list) {
        dBody=list.getElementsByTagName("tbody")[0];
        dRows=dBody.getElementsByTagName("tr");
        if (or_no) {
            alt = (dRows.length%2)+1;
            rowSrc = '<tr class="wardlistrow'+alt+'" id="row'+addslashes(or_no)+'" value="'+or_no+'">'+
                      '<td align="center" ><span style="font:bold 11px Arial;color:#660000">'+or_no+'</span></td>'+ 
                      '<td align="center" id="date">'+date+'</td>'+
                      '<td align="left" id="encoder">'+encoder+'</td>'+
                      '<td align="left" id="name">'+name+'</td>'+                        
                      '<td align="right" id="status">'+status+'</td>'+
                      '<td align="center" id="newOR" style="white-space:nowrap" valign="middle">'+
                        '<table width="100%"><tr><td width="80%" valign="middle">'+
                          '<input class="segInput" type="text" id="'+or_no+'" name="or" value="'+or_no+'" style="width:99%" onblur="validateInput(this);" onfocus="this.select()"></input><input type="hidden" id="origOR'+or_no+'" value="'+or_no+'" />'+
                          '<input type="hidden" id="hiddenOR" value="'+or_no+'" />'+
                        '</td><td valign="middle">'+
                          '<img id="warn_'+or_no+'" src="../../gui/img/common/default/error.png" style="display:none"/>'+
                          '<img id="ok_'+or_no+'" src="../../gui/img/common/default/accept.png"/>'+
                        '</td></tr></table>'+
                      '</td>'+
                    '</tr>';
        } 
        else {
            rowSrc = '<tr><td colspan="7" style="">OR number series does not exist...</td></tr>';
        }
        dBody.innerHTML += rowSrc;
        or_no=0;
        date="";
        name="";
        status="";
    }
}

/*function listNewOR(listID,or_no, date, name, status, new_or, checker)
{
     var list=$(listID), dRows, dBody, rowSrc;
     var parsedOR=parseFloat(or_no);
    if (list) {
        dBody=list.getElementsByTagName("tbody")[0];
        dRows=dBody.getElementsByTagName("tr");
        if (or_no) {
            alt = (dRows.length%2)+1;
            if(!checker)
            {
            rowSrc = '<tr class="wardlistrow'+alt+'" id="row'+addslashes(or_no)+'" value="'+or_no+'">'+
                            '<td>&nbsp;</td>'+
                            '<td width="5%" align="left"><span style="font:bold 11px Arial;color:#660000">'+or_no+'</span></td>'+ 
                            '<td width="15%" align="left" id="'+or_no+'">'+date+'</td>'+
                         '<td width="15%" align="left">'+name+'</td>'+
                            '<td width="5%" align="right">'+status+'</td>'+
                            '<td width="10%" align="center"><input type="text" id="origOR'+or_no+'" size="10" value="'+new_or+'" onBlur="callAlert(this.value);"></input><input type="hidden" id="origOR'+or_no+'" value="'+or_no+'"></input></td>'+
                      '</tr>';        
            }
            else
            { 
            rowSrc = '<tr class="wardlistrow'+alt+'" id="row'+addslashes(or_no)+'" value="'+or_no+'">'+
                            '<td>&nbsp;</td>'+
                            '<td width="5%" align="left"><span style="font:bold 11px Arial;color:#660000">'+or_no+'</span></td>'+ 
                            '<td width="15%" align="left" id="'+or_no+'">'+date+'</td>'+
                         '<td width="15%" align="left">'+name+'</td>'+
                            '<td width="5%" align="right">'+status+'</td>'+
                            '<td width="10%" align="center"><input type="text" id="origOR'+or_no+'" size="10" value="'+new_or+'" STYLE="color: #ff0000; font-family: Arial; font-weight: bold; font-size: 12px;" onBlur="callAlert(this.value);"></input><input type="hidden" id="origOR'+or_no+'" value="'+or_no+'"></input></td>'+
                      '</tr>';      
            }
        } 
        else {
            rowSrc = '<tr><td colspan="6" style="">No OR number selected...</td></tr>';
        }
        dBody.innerHTML += rowSrc;
    }
}*/

function validateInput(obj)
{
  if (obj=$(obj)) {
    orValue = obj.value;
    var appendzero=parseFloat(7-orValue.length);
    var zero=appendZeros(appendzero);
    orValue=zero+""+orValue;
    obj.value = orValue;
    validateInputs();
  }
/*
  var x=isInteger(orValue);
  //alert('x='+x);
    if(x)
    {
      var appendzero=parseFloat(7-orValue.length);
      var zero=appendZeros(appendzero);
      orValue=zero+""+orValue;
      xajax_checkIfORExists(origOR,orValue);
    }
    else
    {
      var input=document.getElementById(origOR);
      input.style.color='#ff0000';
      alert('Invalid OR number format.');
    }
*/
    //alert("or value="+orValue+"orID="+orID);
}

function highlightOR(checker,orig_or,new_or,ORcolor)
{
  if(checker)
  {
      //var origOR='origOR'+new_or;
      var input=document.getElementById(orig_or);
      input.style.color=ORcolor;
  }
  else
  {
     //var origOR='origOR'+orig_no;
     var input=document.getElementById(orig_or);
     input.style.color=ORcolor;
  }
}

function generateOR(new_or,or_no)
{
  //var origOR=or_no;
  $(or_no).value=new_or;
}

function clearList(listID)
{
    var list=$(listID),dRows, dBody;
    if (list) {
        dBody=list.getElementsByTagName("tbody")[0];
        if (dBody) {
            dBody.innerHTML = "";
            return true;    // success
        }
        else return false;    // fail
    }
    else return false;    // fail
}

function addslashes(str) 
{
    str=str.replace("'","\\'");
    return str;
}

function startAJAXSave(saveID)
{
  if (!valid) {
    alert("Errors in new OR series still not corrected...");
    return false;
  }
  var nodes = $$('input[name=or]');
  var orArray = {};
  if (nodes) {
    for (i=0; i<nodes.length; i++)
      orArray[nodes[i].id] = nodes[i].value;
  }
  xajax.call('saveChanges', { parameters: [orArray] });
  
  /*
  var checkColor=false;
  if(saveID=='save')
  {  
    var cnt=0;
    while(cnt<old_arraylen)
    {
      var inputColor=document.getElementById(old_OR_array[cnt]);
      var red='rgb(255, 0, 0)';
      if(inputColor.style.color==red)
      {
        checkColor=true;
      }
      cnt++;
    }
       
    if(checkColor)
    {
      alert('Cannot save, invalid input!'); 
    }
    else
    {
       var d = new Date();
       var year=d.getFullYear();
       var month=d.getMonth()+1;
       var day=d.getDate();
       var hour=d.getHours();
       var min=d.getMinutes();
       var sec=d.getSeconds();
       var datenow=year+"-"+month+"-"+day+" "+hour+":"+min+":"+sec;
       //add codes here to fetch all new or data from textbox 
       var cnt=0;
       var tempArray= new Array();
       var bool=1;
       while(cnt<old_arraylen && bool==1) 
       {
          //var orNewID=old_OR_array[cnt];
          //var orOrigID='origOR'+old_OR_array[cnt];
           
          var temp_or=document.getElementById(old_OR_array[cnt]).value;
          //var orig_or=document.getElementById(orOrigID).value;
          
          if(temp_or=='')
          {
            alert('No OR number specified.');
            bool=0;
          }
          else
          {
            orlen=temp_or.length; 
            if(orlen<7)
            {
              var appendzero=7-orlen;
              var zero=appendZeros(appendzero);
              temp_or=zero+""+temp_or;
            }
            tempArray[cnt]=temp_or;
            //alert('final or='+tempArray[cnt]+' orig or='+old_OR_array[cnt]);
          }
          cnt++;  
       }         
       xajax_saveChanges(tempArray,old_OR_array,new_arraylen,datenow); 
    }     
  }
  */
}

function clearHeader(searchID)
{
    document.getElementById('fromOrNo').value="";
    document.getElementById('toOrNo').value="";
} 

function saveOldORNo(or_no)
{
    var cnt=0;
    var result=false;
    while(cnt<old_arraylen)
    {
        if(old_OR_array[cnt]==or_no)
        {
            result=true;
        }
        cnt++;       
    }
    if(result==false)
    {
        old_OR_array[old_arraylen]=or_no;
        old_arraylen++;
    }
}

function saveNewORNo(or_no)
{
    var cnt=0;
    var result=false;
    while(cnt<new_arraylen)
    {
        if(new_OR_array[cnt]==or_no)
        {
            result=true;
        }
        cnt++;       
    }
    if(result==false)
    {
        new_OR_array[new_arraylen]=or_no;
        new_arraylen++;
    }
}

function startAJAXGenerate(inputID)
{
    if(inputID=='genNewOR')
    {
      xajax_generateNewOR(old_OR_array,old_arraylen,new_from_ORNo,new_to_ORNo);
    }
}

function getNewOR()
{
  var bool=true;
  do{
    new_from_ORNo = prompt("Enter new OR range (From):");
     while((new_from_ORNo=="") || (!isInteger(new_from_ORNo)) || (new_from_ORNo.length>7))  
     {
         if(new_from_ORNo==null)
        {    
            return false;
        }
        else if(new_from_ORNo=="") 
        {
            alert("No OR number specified.");
            bool=false;
        }
        else if(!isInteger(new_from_ORNo))  
        {
            alert("Invalid OR number format.");
            bool=false;
        }
        else if(new_from_ORNo.length>7)  
        {
            alert("Exceeded the OR number format.");
            bool=false;
        }
        else
        {
            bool=true;
        }
        new_from_ORNo = prompt("Enter new OR range (From):");
     }
    new_to_ORNo = prompt("Enter new OR range (To):"); 
    while((new_to_ORNo=="") || (!isInteger(new_to_ORNo)) || (new_to_ORNo.length>7))
    {
      if(new_to_ORNo==null)
      {    
          return false;
      }
      else if(new_to_ORNo=="") 
      {
          alert("No OR number specified.");
          bool= false;
      }
      else if(!isInteger(new_to_ORNo))  
      {
          alert("Invalid OR number format.");
          bool= false;
      }
      else if(new_to_ORNo.length>7)  
      {
          alert("Exceeded the OR number format.");
          bool= false;
      }
      else
      {
        bool=true;
      }
       new_to_ORNo = prompt("Enter new OR range (To):");
    }
   
    var int_from = parseFloat(new_from_ORNo);  
    var int_to = parseFloat(new_to_ORNo); 
    var or_range=parseFloat(int_to-int_from)+1;
    var kulang=old_arraylen-or_range;
        if((int_from>int_to || int_to<int_from) || (int_from==int_to))
        {
            alert("Invalid Range of OR numbers.");
            bool= false;
        }
        else if(or_range<old_arraylen)
        {
          alert("Not enough range of OR numbers");
          bool=false;
        }
        else
        {
           bool=true;
        }
        if(bool==true)
        {
          return true;
        }
  }while(int_from>int_to || int_to<int_from || or_range<old_arraylen);

}

function checkInput(inputID)
{
   if(inputID=='genNewOR' || inputID=='save')
   {
       from_OrNo = document.getElementById("fromOrNo").value;
       to_OrNo = document.getElementById("toOrNo").value;
       if(from_OrNo=="" || to_OrNo=="") 
       {
          alert("No OR number specified.");
          return false;
       }
       else
       {
        return true;
       }
   }
}

function appendZeros(numOfzero)
{
  var zero="";
  switch(numOfzero)
  {
     case 1: zero="0"; break;
     case 2: zero="00"; break;
     case 3: zero="000"; break;
     case 4: zero="0000"; break;
     case 5: zero="00000"; break;
     case 6: zero="000000"; break;
     case 7: zero="0000000"; break;
  }
  return zero;
}

function isInteger (s)
{
  var i;

  if (isEmpty(s))
  if (isInteger.arguments.length == 1) return 0;
  else return (isInteger.arguments[1] == true);

  for (i = 0; i < s.length; i++)
  {
    var c = s.charAt(i);

    if (!isDigit(c)) return false;
  }

  return true;
}

function isEmpty(s)
{
  return ((s == null) || (s.length == 0))
}

function isDigit (c)
{
  return ((c >= "0") && (c <= "9"))
}

function clearBuffer(id)
{
  if(id)
  {
     var cnt=0;
     while(cnt<old_arraylen)
     {
      old_OR_array[cnt]="";
      cnt++;
     }
     old_arraylen=0;
  }
}

function validateInputs() {
  var nodes = $$('input[name=or]');
  var orArray = {};
  if (nodes) {
    for (i=0; i<nodes.length; i++)
      orArray[nodes[i].id] = nodes[i].value;
  }
  xajax.call('checkORNos', { parameters: [orArray] });
}

function setValid(ok) {
  if (ok==1) valid=1;
  else valid=0;
}