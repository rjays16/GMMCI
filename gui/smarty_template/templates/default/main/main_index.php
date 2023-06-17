{{* main_index.tpl *}}

<link rel="stylesheet" href="images/template_css.css" type="text/css">

</style>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"><style type="text/css">
</style></head>

<body>


<table align="center" border="0" cellpadding="0" cellspacing="0" height="837" width="100%">
  <tbody><tr>
    <td height="837" valign="top"><a name="up" id="up"></a>
        
	  
	  <table style="border: 1px solid rgb(153, 160, 170);" border="0" cellpadding="0" cellspacing="0" width="100%">
        <tbody><tr>
          <td height="494" valign="top"><table align="center" bgcolor="#eef0f0" border="0" cellpadding="0" cellspacing="0" width="100%">
            <tbody><tr>
              <td width="20%" height="713" valign="top" background="images/modulback.gif" style="border-right: 1px solid rgb(153, 160, 170); border-bottom: 1px solid rgb(255, 255, 255);">                <table border="0" cellpadding="0" cellspacing="0" width="188">
                  <tbody><tr>
                    <td>			<table width="92%" cellpadding="0" cellspacing="0" class="moduletable">
                      <tbody>
                        <tr>
                          <td valign="top">
						  
						  
						  <table border="0" cellpadding="0" cellspacing="0" width="100%" class="moduletable">
                              <tbody>
                                <tr align="left">
                                  <th >Main Menu</th></tr>
																	<?php
																			
																			 /*Connect to DB */

 																			$db_connection = mysql_connect("localhost","root","")
                  																			or die("Could not find DB");
  																		/*Select DB*/

  																		mysql_select_db("hisdb",$db_connection)
																										or die("Could not find DB");			
																	
																			$sql="SELECT nr,sort_nr,name,LD_var AS \"LD_var\",url,is_visible FROM care_menu_main WHERE is_visible=1 OR LD_var='LDEDP' OR LD_var='LDLogin' ORDER by sort_nr";
																			$query_result = mysql_query ($sql, $db_connection);
																			while ($row = mysql_fetch_array ($query_result)) {
																	 	
																	 ?> 									
																	 <tr align="left">
																	 
                                  <td><a href="<?php echo $root_path.$menu['url']; ?>" class="mainlevel" target="contframe"><?php echo $menu['name']; ?></a></td>
                                  </tr>
																	<? } ?> 
                                <!--<tr align="left">
                                  <td><a href="main/startframe.php" class="mainlevel" target="contframe">Home</a></td>
                                </tr>
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
                                  <td><a href="modules/doctors/doctors.php" target="contframe" class="mainlevel">Doctors</a></td>
                                </tr>
                                <tr align="left">
                                  <td><a href="modules/nursing/nursing.php" target="contframe" class="mainlevel">Nursing</a></td>
                                </tr>
                                <tr align="left">
                                  <td><a href="main/op-doku.php" class="mainlevel" target="contframe">OP Room </a></td>
                                </tr>
                                <tr align="left">
                                  <td><a href="modules/laboratory/labor.php" target="contframe" class="mainlevel" >Laboratories</a></td>
                                </tr>
                                <tr align="left">
                                  <td><a href="modules/radiology/radiolog.php" target="contframe" class="mainlevel">Radiology</a></b></td>
                                </tr>
                                <tr align="left">
                                  <td><a href="modules/pharmacy/apotheke.php" class="mainlevel" target="contframe">Pharmacy</a></td>
                                </tr>
                                <tr align="left">
                                  <td><a href="modules/med_depot/medlager.php" target="contframe" class="mainlevel">Medical Depot</a></td>
                                </tr>
                                <tr align="left">
                                  <td><a href="modules/phone_directory/phone.php" class="mainlevel" target="contframe">Directory</a></td>
                                </tr>
                                <tr align="left">
                                  <td><a href="modules/tech/technik.php" target="contframe" class="mainlevel">Tech Support</a></td>
                                </tr>
                                <tr align="left">
                                  <td><a href="modules/system_admin/edv.php" class="mainlevel" target="contframe"> System Admin </a> </td>
                                </tr>
                                <tr align="left">
                                  <td><a href="main/spediens.php" class="mainlevel" target="contframe">Special Tools</a> </td>
                                </tr>--> 
                                <tr align="left">
                                  <td><a href="#" class="mainlevel">Contact Us</a></td>
                                </tr>
                              </tbody>
                          </table></td>
                        </tr>
                      </tbody>
                    </table>
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
										Updates </th>
				</tr>
							<tr>
				<td>
					
		<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
	<tbody><tr>
		<td><table width="100%"  border="0" cellspacing="2" cellpadding="2">
                 
                  <tr>
                    <td height="17" align="center"><a href="modules/news/open-time.php" target="contframe">Admission Hours  </a></td>
                  </tr>
                  <tr>
                    <td height="17" align="center"><a href="modules/news/newscolumns.php" target="contframe">Management</a>s</td>
                  </tr>
                  <tr>
                    <td height="17" align="center"><a href="modules/news/departments.php" target="contframe">Departments</a></td>
                  </tr>
                  <tr>
                    <td height="17" align="center"><a href="modules/cafeteria/cafenews.php" target="contframe">Cafeteria News </a></td>
                  </tr>
                  <tr>
                    <td height="17" align="center"><a href="modules/news/newscolumns.php" target="contframe">Admission</a></td>
                  </tr>
                  <tr>
                    <td height="17" align="center"><a href="modules/news/newscolumns.php" target="contframe">Exhibitions</a></td>
                  </tr>
                  <tr>
                    <td height="17" align="center"><a href="modules/news/newscolumns.php" target="contframe">Education</a></td>
                  </tr>
                  <tr>
                    <td height="17" align="center"><a href="modules/news/newscolumns.php" target="contframe">Studies</a></td>
                  </tr>
                  <tr>
                    <td height="17" align="center"><a href="modules/news/newscolumns.php" target="contframe">Physical Therapy</a> </td>
                  </tr>
                  <tr>
                    <td height="17" align="center"><a href="modules/news/newscolumns.php" target="contframe">Health Tips</a> </td>
                  </tr>
                  <tr>
                    <td height="17" align="center"><a href="modules/calendar/calendar.php" target="contframe">Calendar</a></td>
                  </tr>
                  <tr>
                    <td height="17" align="center"><a href="javascript:gethelp()">Help</a></td>
                  </tr>
                  <tr>
                    <td height="17" align="center"><a href="modules/news/editor-pass.php" target="contframe">Submit News</a></td>
                  </tr>
                  <tr>
                    <td height="17" align="center">Credits</td>
                  </tr>
                </table>		</td>
	</tr>
	
			<tr>
			<td>&nbsp;			</td>
		</tr>
			</tbody></table>
                                
					</td>
			</tr>
			</tbody></table>
						<table class="moduletable" cellpadding="0" cellspacing="0">
							<tbody><tr>
					<th valign="top">&nbsp;</th>
				</tr>
							
			</tbody></table>
			</td>
                  </tr>
                </tbody></table>
                </td>
              <td width="100%" valign="top" bgcolor="#D2DEE3" style="border-left: 1px solid rgb(255, 255, 255); border-right: 1px solid rgb(255, 255, 255); border-bottom: 1px solid rgb(255, 255, 255);">
			  
			 <div align="right" >
		 <!--  <span onmousedown="document.getElementById('contframe').contentWindow.scrollByLines(5)" style="cursor:pointer"><img src="images/frm_up.jpg"></span>  -->
   	<!--		  <span onmousedown="document.getElementById('contframe').contentWindow.scrollByLines(-5)" style="cursor:pointer"><img src="images/frm_dwn.jpg"></span>  -->
			</div>
			
		
			
			  
			  
                </td>
              </tr>
          </tbody></table></td>
        </tr>
      </tbody></table>
      <table align="center" background="images/center2.jpg" border="0" cellpadding="0" cellspacing="0" width="100%">
        <tbody><tr>
          <td width="10" height="104"><img src="images/left3.jpg"></td>
          <td width="952" align="right" valign="top"><table background="images/center2.jpg" border="0" cellpadding="0" cellspacing="0" height="29" width="100%">
            <tbody><tr>
              <td height="29" align="right"><!-- <span onmousedown="document.getElementById('contframe').contentWindow.scrollByLines(-10)" style="cursor:pointer"><img src="images/frm_up.jpg"></span> -->
								    <!--  <span onmousedown="document.getElementById('contframe').contentWindow.scrollByLines(10)" style="cursor:pointer"><img src="images/frm_dwn.jpg"></span> -->
					  </td>
            </tr>
          </tbody></table>            
            <table background="images/center4.jpg" border="0" cellpadding="0" cellspacing="0" height="73" width="740">
            <tbody><tr>
              <td width="668" height="73" align="center"><span class="style2">Powered by: </span><br />
                <span class="style3">Segworks Technologies Corporation...</span></td>
              <td width="72" align="right" valign="top"><img src="images/top.jpg" usemap="#Map" border="0" height="73" width="44" />
                <map name="Map" id="Map">
                  <area shape="rect" coords="1,25,29,53" href="#" />
                </map></td>
            </tr>
          </tbody>
            </table></td>
          <td width="10"><img src="images/right3.jpg"></td>
        </tr>
      </tbody></table>
      </td>
  </tr>
</tbody></table>
<!-- 1144654829 -->

</body></html>




