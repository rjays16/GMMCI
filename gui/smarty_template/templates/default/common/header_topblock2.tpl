 {{* Toolbar - Topblock  *}}
<table cellspacing="0"  class="titlebar" style="border:1px solid #cfcfcf;margin-bottom:10px" cellpadding="0">    
 <tr valign=middle  class="titlebar" >
  <td bgcolor="{{$top_bgcolor}}" valign="middle" width="1">
    &nbsp;{{$sTitleImage}}&nbsp;<font size="3" color="{{$top_txtcolor}}" style="white-space:nowrap">{{$sToolbarTitle}}</font>
     {{if $Subtitle}}
      - {{$Subtitle}}
     {{/if}}
  </td>
{{if $QuickMenu }}
	<td class="quickmenu" bgcolor="{{$top_bgcolor}}" align=right valign="middle">
		<ul>
{{foreach from=$QuickMenu key=qmId item=qItem}}
	{{if $qItem.label ne "|"}}
		  <li>
				<a href="{{$qItem.url}}">
					<span><img {{$qItem.icon}} align="absmiddle"/></span>
					{{$qItem.label}}
				</a>
			</li>
	{{else}}
		  <li class="separator"></li>
	{{/if}}
{{/foreach}}
		</ul>
	</td>
{{else}}
  <td bgcolor="{{$top_bgcolor}}" align=right valign="middle" style="">
  	{{if $pbAux2}}
		<a href="{{$pbAux2}}"><img {{$gifAux2}} alt="" {{$dhtml}} /></a>
	{{/if}}
	{{if $pbAux1}}
		<a href="{{$pbAux1}}"><img {{$gifAux1}} alt="" {{$dhtml}} /></a>
	{{/if}}
	<!-- Hide Back buttons =)  AJMQ/Oct 03 2007
	{{if $pbBack}}
		<a href="{{$pbBack}}">
			<img {{$gifBack2}} alt="" {{$dhtml}} />
		</a>
	{{/if}}
	-->
	<!---hide for the meantime...pet, apr22,2008-----
	{{if $pbHelp}}
		<a href="{{$pbHelp}}">
			<img {{$gifHilfeR}} alt="" {{$dhtml}} />
		</a>
	{{/if}}
	---pet---------------------til here only-------->
  </td>
{{/if}}
 </tr>
 </table>