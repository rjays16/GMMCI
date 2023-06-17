{{* Frame template of medocs page *}}
{{* Note: this template uses a template from the /registration_admission/ *}}
</style>
<table width="100%" cellspacing="0" cellpadding="0">
  <tbody>
    <tr>
      <td>{{include file="medocs/tabs.tpl"}}</td>
    </tr>

    <tr>
      <td>
		<table width="100%" cellspacing="0" cellpadding="0">
		<tbody>
			<tr>
				<!--<td width="840">-->
				<!--edited by VAN 02-18-08 -->
				{{if $sShow}}
					<td width="78%">
						{{include file="registration_admission/basic_data.tpl"}}				
					</td>
					<td width="22%">{{$sRegOptions}}</td>
				{{else}}
					<td width="840">
						{{include file="registration_admission/basic_data.tpl"}}				
					</td>
				{{/if}}	
			</tr>
			{{if $sPHICShow}}
			<tr>
				<!--{{if $sPHICShow}}-->
				<td colspan="2">
					<table border="0" width="74%" >
						<tr>
							<td {{$sClassItem}} width="31%">{{$LDDischargedDate}}</td>
							<td bgcolor="#ffffee" class="vi_data"><b>{{$sDischargedDate}}</b></td>
						</tr>
						<tr>
							<td {{$sClassItem}} width="31%">{{$LDReceivedDate}}</td>
							<td bgcolor="#ffffee" class="vi_data"><b>{{$sReceivedDate}}</b></td>
						</tr>
						<tr>
							<td {{$sClassItem}} width="31%">{{$LDPHIC}}</td>
							<td bgcolor="#ffffee" class="vi_data"><b>{{$sPHIC}}</b></td>
						</tr>
                        <tr>
                            <td {{$sClassItem}} width="31%">{{$LDTrans}}</td>
                            <td bgcolor="#ffffee" class="vi_data"><b>{{$sTrans}}</b></td>
                        </tr>
					</table>
				</td>
				<!--
				{{else}}
					<td colspan="2">
					<table border="0" width="74%" >
						<tr>
							<td {{$sClassItem}} width="31%">{{$LDDischargedDate}}</td>
							<td bgcolor="#ffffee" class="vi_data"><b>{{$sDischargedDate}}</b></td>
						</tr>
						<tr>
							<td {{$sClassItem}} width="40%">{{$LDReceivedDate}}</td>
							<td bgcolor="#ffffee" class="vi_data"><b>{{$sReceivedDate}}</b></td>
						</tr>
					</table>
				</td>
			
			   {{/if}}	
			   	-->
			</tr>
			{{/if}}	
			<tr>
				<td>
					{{if $bShowNoRecord}}
						{{include file="registration_admission/common_norecord.tpl"}}
					{{else}}
						{{include file=$sDocsBlockIncludeFile}}
					{{/if}}
	  			</td>
			</tr>
		</tbody>
		</table>

	  </td>
    </tr>
    
	<tr>
      <td> 
	  	{{ if $sHideNewRecLink}}	
			{{$sNewLinkIcon}} {{$sNewRecLink}}<br />
			{{$sKeyListener}}
		{{/if}}	
			{{$sPdfLinkIcon}} {{$sMakePdfLink}}<br />
			{{$sListLinkIcon}} {{$sListRecLink}}<p>
			{{$pbBottomClose}}
			{{$segPrint}}
	  </p></td>
    </tr>

  </tbody>
</table>
