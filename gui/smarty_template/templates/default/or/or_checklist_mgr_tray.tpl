<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title></title>             
{{foreach from=$css_and_js item=script}}
		{{$script}}
{{/foreach}}               
</head>                        
<body>                                                                                    
 {{$form_start}}
 <div id="body" style="overflow: auto; height: 100%; background: silver; border: 2px;">                                                       
 <table width="100%" height="100%">
	 <tr>
			<td bgcolor="#E9E9E9">            
					<table cellpadding="4" align="LEFT" valign="MIDDLE" width="98%">
			 <tr>
					<td>
							<label>Checklist Question: </label> {{$cb_question}}    
					</td>
			 </tr>  
			 
			 <tr>
					<td>
						{{$additional_detail}} <label> Additional Detail </label>
						 {{$detail_div}}<dd><label>Detail Label: </label>{{$detail}}</div>         
					</td>
			 </tr>   
			 <tr>
					<td>
						{{$mandatory}}<label> Mandatory Item</label> 
					</td>
			 </tr>
			 <tr>
					<td>
						 <label>Applicable OR Areas: </label><br/>      
							<div style="display:block"><dd>
								{{html_checkboxes name="question" options=$areas selected=$areas_selected separator="<dd>"}}     
							</div>     
					</td>
			 </tr> 
			 <tr>
					<td> <br/>
						 {{$package_submit}}
						 {{$package_cancel}}
					</td>
			 </tr>
		</table>
			</td>
	 </tr>
</table>            

{{$is_submitted}}   
{{$new_question}}
{{$new_detail}}
{{$is_detail}}
{{$is_mandatory}}
{{$checklist_id}}    
{{$mode}}  
									 
{{$form_end}}    
</div>  
</body>           
</html>