{{* form.tpl  Form template for orders module (pharmacy & meddepot) 2004-07-04 Elpidio Latorilla *}}
<div align="center" style="font:bold 12px Tahoma; color:#990000; ">{{$sBtnAddItem}}</div><br />
<div id="mainTablediv" align="center">
   <table width="96%" cellpadding="2" cellspacing="2" id="mainTable" style="border-collapse:collapse; border:1px solid #a6b4c9; color:black">
        <thead>
            <tr><th id="billcol_01" colspan="2" rowspan="2" align="left" class="jedPanelHeader" style="border-right:none">PRIMARY HOLDER INFORMATION</th>
            </tr>
        </thead>
    </table>
    <table width="96%" cellpadding="2" cellspacing="2" id="mainTable2" style="border-collapse:collapse; border:1px solid #a6b4c9; color:black">
        <tbody>            
            <!-- Basic information -->
            <tr>
                <td colspan="2" rowspan="5" align="left" valign="top" class="jedPanel">
                    <table width="100%" border="0" cellpadding="2" cellspacing="0">
                        <tr>                            
                            <td>First Name:</td>
                            <td>{{$fname}}<span style="vertical-align:bottom">{{$sSelectPatient}}</span></td>
                        </tr>
                        <tr>
                            <td>Middle Name:</td>
                            <td>{{$mname}}</td>
                        </tr>
                        <tr>
                            <td>Last Name:</td>
                            <td>{{$lname}}</td>
                        </tr>
                        <tr>
                            <td>{{$SelectArea}}</td>
                            <td>{{$sbrgy}}</td>
                        </tr>
                        <tr>
                            <td>Street Name:</td>
                            <td>{{$StreetName}}</td>
                        </tr>
                        <tr>
                            <td>Barangay:</td>
                            <td>{{$brgy_name}}</td>
                        </tr>
                        <tr>
                            <td>Municipality:</td>
                            <td>{{$mun_name}}</td>
                        </tr>
                        <tr>
                            <td colspan="2" align="center">{{$SubBut}}{{$CanBut}}</td>
                        </tr>
					  </table>
					</td>
			   </tr>
           </tbody>
          </table>
        {{$sOpAccChrgHiddenInputs}}
    </form>
</div>
</div>

<span style="font:bold 15px Arial">{{$sDebug}}</span>
{{$sFormEnd}}
{{$sTailScripts}}     
