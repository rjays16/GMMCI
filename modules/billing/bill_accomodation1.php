<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>

<!--<script language="javascript">
 function addRow(id){
    var tbody = document.getElementById
(id).getElementsByTagName("TBODY")[0];
    var row = document.createElement("TR")
	
    var td1 = document.createElement("TD")
    td1.appendChild(document.createTextNode("."))
    var td2 = document.createElement("TD")
    td2.appendChild (document.createTextNode(""))
    row.appendChild(td1);
    row.appendChild(td2);
    tbody.appendChild(row);
  }

</script>-->
<script language="JavaScript" src="js/tabledeleterow.js"></script>

<style type="text/css">
<!--
#tblSample td, th { padding: 1px; }
.classy0 { background-color: #234567; color: #89abcd; }
.classy1 { background-color: #89abcd; color: #234567; }
-->
</style>


<style type="text/css">
<!--
.style1 {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
.style2 {
	font-family: Arial, Helvetica, sans-serif;
	color: #FFFFFF;
	font-weight: bold;
	font-size: 12px;
}
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
a {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10px;
	color: #000000;
}
a:link {
	text-decoration: none;
}
a:visited {
	text-decoration: none;
}
a:hover {
	text-decoration: none;
}
a:active {
	text-decoration: none;
}
-->
</style>
</head>

<body>
<table width="100%" border="0" cellspacing="0" cellpadding="1">
  <tr>
    <td valign="top" bgcolor="#EBEEF1">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td valign="top" bgcolor="#EBEEF1">
            <table width="100%" border="0" align="center" cellpadding="0" cellspacing="1">
              <tr>
                <td bgcolor="#5577DD">
                  <table width="95%" border="0" align="center" cellpadding="0" cellspacing="1">
                    <tr>
                      <td><span class="style2">Hospital Accomodation</span></td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
		
		
		
            <table width="100%" border="0" align="center" cellpadding="1" cellspacing="0" >
              <tr>
                <td valign="top" bgcolor="#E1F0FF">
                 
				
				
             
			 
			      <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td valign="top" bgcolor="#5577DD">
					  
				
					  
					 <!-- <a href="javascript:addRow('myTable')">Add row</a>-->
					 
					 
					 <form action="tableaddrow_nw.html" method="get">
<p>
<input type="button" value="Add" onclick="addRowToTable();" />
<!--
<input type="button" value="Insert [I]" onclick="insertRowToTable();" />
<input type="button" value="Delete [D]" onclick="deleteChecked();" />
<input type="button" value="Submit" onclick="openInNewWindow(this.form);" />
-->
                        <table id="tblSample" width="100%" border="0" cellspacing="1" cellpadding="0">
						
						<thead>
						
                          <tr>
                            <th width="16%" align="center" bgcolor="#E1F0FF"  class="style1">Admission Date </th>
                            <th width="12%" align="center" bgcolor="#E1F0FF"  class="style1">Discharged</th>
                            <th width="12%" align="center" bgcolor="#E1F0FF"  class="style1">Room No. </th>
                            <th width="11%" align="center" bgcolor="#E1F0FF" class="style1">Type No. </th>
                            <th width="22%" align="center" bgcolor="#E1F0FF" class="style1">Description</th>
                            <th width="6%" align="center" bgcolor="#E1F0FF" class="style1">Rate</th>
                            <th width="21%" align="center" bgcolor="#E1F0FF" class="style1">Available Healthcare(No. of days) </th>
                          </tr>
						  </thead>
						   <tbody></tbody>
                        </table>
						
						</p>
						</form>
						
					    <script language="JavaScript">
	tigra_tables('billing_table', 0, 0, '#ffffff', '#E1F0FF', '#b5cee0', '#90afd9');
</script>		  
                      
					  
					  
					  
					  </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
        
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>
