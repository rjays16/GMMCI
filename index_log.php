<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>

<title>Segworks Hospital Information System - Online</title>


<link rel="stylesheet" href="images/template_css.css" type="text/css">
<!--<link rel="shortcut icon" href="http://localhost/segclinic/templates/247portal-b-brown/favicon.ico">
<link rel="alternate" title="SegClinic " href="http://localhost/segclinic/index2.php?option=com_rss&amp;no_html=1" type="application/rss+xml">
//-->
<script language="JavaScript" type="text/javascript">
    <!--
    function MM_reloadPage(init) {  //reloads the window if Nav4 resized
      if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
        document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
      else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
    }
    MM_reloadPage(true);
    //-->
  </script>
<style type="text/css">
<!--
.style2 {color: #666666}
.style3 {color: #C3D4DB}
body {
	margin-left: 8px;
	margin-top: 3px;
	margin-right: 8px;
	margin-bottom: 10px;
}
.style5 {
	color: #476978;
	font-weight: bold;
}
-->
</style>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"></head>

<body>
<a name="up" id="up"></a><table align="center" border="0" cellpadding="0" cellspacing="0" height="300" width="100%">
  <tbody><tr>
    <td valign="top"><table align="center" background="images/center1.jpg" border="0" cellpadding="0" cellspacing="0" width="100%">
      <tbody><tr>
        <td colspan="4"><img src="images/space.gif" height="1" width="760"></td>
      </tr>
      <tr>
        <td width="46"><img src="images/left1.jpg"></td>
        <td style="padding-left: 5px;" class="title" width="50%"><img src="images/seghis_logo.gif" width="186" height="40" /></td>
        <td width="50%"><table style="padding-right: 38px;" align="right" background="images/center3.jpg" border="0" cellpadding="0" cellspacing="0" height="71" width="509">
          <tbody><tr>
            <td align="right"><form action="index.php" method="post">
                <div align="center">
                  <input class="search_box" name="searchword" size="15" value="search..." onblur="if(this.value=='') this.value='search...';" onfocus="if(this.value=='search...') this.value='';" type="text">
                  <input name="option" value="search" type="hidden">
                </div>
            </form></td>
          </tr>
        </tbody></table></td>
        <td width="45"><img src="images/right1.jpg"></td>
      </tr>
      <tr>
        <td width="46" rowspan="2" valign="top"><img src="images/left2.jpg"></td>
        <td colspan="2"><table border="0" cellpadding="0" cellspacing="0" height="24" width="100%">
          <tbody><tr>
            <td width="86%">              <div align="left">
			 <?php echo  $HTTP_SESSION_VARS['sess_login_username']; ?>
			 <?php 
			// require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 			 //$smarty = new smarty_care('common');
			 
			// $smarty->assign('sUserName',$HTTP_SESSION_VARS['sess_login_username']);
			
			 ?>			 
			 
			</div>
					</td>
            <td width="14%" align="left"><a href="main/logout_confirm.php" target="contframe" >Logout</a></td>
          </tr>
        </tbody></table></td>
        <td width="45" rowspan="2" valign="top"><img src="images/right2.jpg"></td>
      </tr>
      <tr>
        <td colspan="2" align="left"><table align="left" border="0" cellpadding="0" cellspacing="0" width="100%">
          <tbody><tr>
            <td height="38" valign="top"><div id="buttons">
		      <ul class="mostread">
<a href="http://www.segworkstech.com" class="style3">Segworks Technologies Corporation </a><br>

</ul>
	          <div align="left"></div>
        </div></td>
          </tr>
        </tbody></table></td>
        </tr>
    </tbody></table>
      <table style="border: 1px solid rgb(153, 160, 170);" border="0" cellpadding="0" cellspacing="0" width="100%">
        <tbody><tr>
          <td height="494" valign="top"><table align="center" bgcolor="#eef0f0" border="0" cellpadding="0" cellspacing="0" width="100%">
            <tbody><tr>
              <td width="20%" valign="top" background="images/modulback.gif" style="border-right: 1px solid rgb(153, 160, 170); border-bottom: 1px solid rgb(255, 255, 255);">                <table border="0" cellpadding="0" cellspacing="0" width="188">
                  <tbody><tr>
                    <td>			<table width="74%" cellpadding="0" cellspacing="0" class="moduletable">
						<tbody><tr>
				<td>
				
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tbody><tr align="left"><td><a href="main/startframe.php" class="mainlevel" target="contframe">Home</a></td></tr>
<tr align="left">
  <td><a href="modules/registration_admission/patient_register_pass.php" class="mainlevel" target="contframe">Patients</a></td>
</tr>
<tr align="left">
  <td><a href="modules/appointment_scheduler/appt_main_pass.php" class="mainlevel" target="contframe">Appointments</a></td>
</tr>
<tr align="left">
  <td><a href="modules/registration_admission/aufnahme_pass.php" class="mainlevel" target="contframe">Admission</a></td>
</tr>
<tr align="left">
  <td><a href="modules/ambulatory/ambulatory.php" class="mainlevel" target="contframe">Ambulatory</a></td>
</tr>
<tr align="left">
  <td><a href="modules/medocs/medocs_pass.php" class="mainlevel" target="contframe">Medocs</a></td>
</tr>
<tr align="left">
  <td><a href="modules/nursing/nursing.php" target="contframe" class="mainlevel">Nursing</a></td>
</tr>
<tr align="left">
  <td><a href="main/op-doku.php" class="mainlevel" target="contframe">OP Room </a></td>
</tr>
<tr align="left">
  <td><a href="/HIS/clinic/modules/pharmacy/apotheke.php" class="mainlevel" target="contframe">Pharmacy</a></td>
</tr>
<tr align="left">
  <td><a href="/HIS/clinic/modules/phone_directory/phone.php" class="mainlevel" target="contframe">Directory</a></td>
</tr>
<tr align="left">
  <td><a href="/HIS/clinic/modules/system_admin/edv.php" class="mainlevel" target="contframe"> System Admin </a> </td>
</tr>
<tr align="left">
  <td><a href="/HIS/clinic/main/spediens.php" class="mainlevel" target="contframe">Special Tools</a> </td>
</tr>

<tr align="left"><td><a href="www.segworkstech.com" class="mainlevel">Contact Us</a></td>
</tr>

</tbody></table>				</td>
			</tr>
			</tbody></table>
						<table class="moduletable" cellpadding="0" cellspacing="0">
						<tbody><tr>
				<td>
				
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tbody><tr align="left"><td>&nbsp;</td>
</tr>
</tbody></table>				</td>
			</tr>
			</tbody></table>
						<table class="moduletable" cellpadding="0" cellspacing="0">
							<tbody><tr>
					<th valign="top">
										OSI Certified </th>
				</tr>
							<tr>
				<td>
					<form action="/HIS/clinic/index_1.php" method="post" name="login">
		<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
	<tbody><tr>
		<td>		<br>
		<br>		<br>
		<br>		<br>
		</td>
	</tr>
	<tr>
		<td>
		<a href="http://localhost/SegClinic/index.php?option=com_registration&amp;task=lostPassword">		</a>
		</td>
	</tr>
			<tr>
			<td>&nbsp;			</td>
		</tr>
			</tbody></table>
                                        </form>
					</td>
			</tr>
			</tbody></table>
						<table class="moduletable" cellpadding="0" cellspacing="0">
							<tbody><tr>
					<th valign="top">&nbsp;										</th>
				</tr>
							<tr>
				<td>
				
<div class="syndicate">

	<div align="center">
	<a href="http://localhost/segclinic/index2.php?option=com_rss&amp;feed=RSS0.91&amp;no_html=1">		</a>
	</div>
	
	<div align="center">
	<a href="http://localhost/segclinic/index2.php?option=com_rss&amp;feed=RSS1.0&amp;no_html=1">		</a>
	</div>
	
	<div align="center">
	<a href="http://localhost/segclinic/index2.php?option=com_rss&amp;feed=RSS2.0&amp;no_html=1">		</a>
	</div>
	
	<div align="center">
	<a href="http://localhost/segclinic/index2.php?option=com_rss&amp;feed=ATOM0.3&amp;no_html=1">		</a>
	</div>
	
	<div align="center">
	<a href="http://localhost/segclinic/index2.php?option=com_rss&amp;feed=OPML&amp;no_html=1">		</a>
	</div>
	</div>
				</td>
			</tr>
			</tbody></table>
			</td>
                  </tr>
                </tbody></table>
                </td>
              <td width="80%" valign="top" bgcolor="#E6EAEC" style="border-left: 1px solid rgb(255, 255, 255); border-right: 1px solid rgb(255, 255, 255); border-bottom: 1px solid rgb(255, 255, 255);">
			  
			  <iframe src="main/login.php" name="contframe" width="100%" height="500" frameborder="0">***</iframe>
                </td>
              </tr>
          </tbody></table></td>
        </tr>
      </tbody></table>
      <table align="center" background="images/center2.jpg" border="0" cellpadding="0" cellspacing="0" width="100%">
        <tbody><tr>
          <td width="10" height="104"><img src="images/left3.jpg"></td>
          <td width="952" align="right"><table background="images/center2.jpg" border="0" cellpadding="0" cellspacing="0" height="32" width="100%">
            <tbody><tr>
              <td height="32">&nbsp;</td>
            </tr>
          </tbody></table>            
            <table background="images/center4.jpg" border="0" cellpadding="0" cellspacing="0" height="73" width="740">
            <tbody><tr>
              <td width="668" height="73" align="left"><span class="style2"></span></td>
              <td width="72" align="right" valign="top"><img src="images/top.jpg" usemap="#Map" border="0" height="73" width="44" />
                <map name="Map" id="Map">
                  <area shape="rect" coords="0,25,28,53" href="#" />
                </map></td>
            </tr>
          </tbody></table></td>
          <td width="10"><img src="images/right3.jpg"></td>
        </tr>
      </tbody></table>
            <table align="center" border="0" cellpadding="0" cellspacing="10" width="100%">
        <tbody><tr>
          <td align="center">
          <div align="center">Segworks Technologies Corporation <br>
          </div>
</td>
        </tr>
      </tbody></table></td>
  </tr>
</tbody></table>
<!-- 1144654829 -->
</body></html>