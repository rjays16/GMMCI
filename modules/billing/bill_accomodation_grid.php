<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title>Billing grid</title>
	<meta name="KEYWORDS" content="dhtmlxgrid, dhtml grid, javascript grid, javascript, DHTML, grid, grid control, dynamical scrolling, xml, AJAX, API, cross-browser, checkbox, XHTML, compatible, gridview, navigation, script, javascript navigation, web-site, dynamic, javascript grid, dhtml grid, dynamic grid, item, row, cell, asp, .net, jsp, cold fusion, custom tag, loading, widget, checkbox, drag, drop, drag and drop, component, html, scand" />

<meta name="DESCRIPTION" content="Cross-browser DHTML grid with XML support and powerful API. This DHTML JavaScript grid can load its content dynamically from server using AJAX technology." />

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<style type="text/css">
<!--
.style1 {
	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
	color: #FFFFFF;
}
-->
</style>
</head>
<style>
	body {font-size:12px}
	.{font-family:arial;font-size:12px}
	h1 {cursor:hand;font-size:16px;margin-left:10px;line-height:10px}
	xmp {color:green;font-size:12px;margin:0px;font-family:courier;background-color:#e6e6fa;padding:2px}
	div.hdr{
		background-color:lightgrey;
		margin-bottom:10px;
		padding-left:10px;
	}
</style>
<body>
<link rel="STYLESHEET" type="text/css" href="../css/dhtmlXGrid.css" />
	<script  src="../js/dhtmlXCommon.js"></script>
	<script  src="../js/dhtmlXGrid.js"></script>		
	<script  src="../js/dhtmlXGridCell.js"></script>	
	
	<table width="600" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#94AF23">
          <table width="90%" border="0" align="center" cellpadding="0" cellspacing="1">
            <tr>
              <td><span class="style1">Hospital Accomodation</span> </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
	<table width="600">
		<tr>
			<td>
				<div id="gridbox" width="100%" height="250px" style="background-color:#94AF23;overflow:hidden"></div>
			</td>
		</tr>
	</table>
	<table width="600">
		<tr>
			<td>
			  <li><a href="javascript:void(0)" onClick="mygrid.addRow(Date.parse(new Date()),window.prompt('Value for the first cell')+','+window.prompt('Value for the second cell'),1)"><img src="../imgs/folder.gif" border="0"></a></li>
				<li><a href="javascript:void(0)" onClick="mygrid.deleteRow(mygrid.getRowId(window.prompt('Row[index] to delete')))">Delete Row </a></li>
		  </td>
		</tr>
	</table>
    <script>
	mygrid = new dhtmlXGridObject('gridbox');
	mygrid.setImagePath("../imgs/");
	mygrid.setHeader("Admission date, Discharged");
	mygrid.setInitWidths("200,200")
	mygrid.setColAlign("right,left")
	mygrid.setColTypes("ro,ed");
	mygrid.setColSorting("str,str")
	mygrid.init();
	mygrid.loadXML("../grid.xml");
</script>

</body>
</html>
