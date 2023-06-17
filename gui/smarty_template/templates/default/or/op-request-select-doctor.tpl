{{* form.tpl  Form template for orders module (pharmacy & meddepot) 2004-07-04 Elpidio Latorilla *}}
<div align="center" style="font:bold 12px Tahoma; color:#990000; ">{{$sWarning}}</div><br />

{{$sFormStart}}

	<div align="left" style="width:80%">
		{{$sPersonnelList}}
	</div>
	<div align="center" style="width:95%">	
		<br>
		{{$sBreakButton}}
	</div>
	{{$sHiddenInputs}}
</div>
{{$sFormEnd}}
{{$sTailScripts}} 	
<hr/>
