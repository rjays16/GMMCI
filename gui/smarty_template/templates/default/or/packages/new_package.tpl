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
<div id="new_package">
	<ul>
		<li><a href="#basic_info"><span>Basic Info</span></a></li>
		<li><a href="#details"><span>Details</span></a></li>

		<!--<li><a href="#pre_op"><span>Pre-op</span></a></li>-->
		<!--<li><a href="#prep"><span>Prep</span></a></li>
		<li><a href="#anesthesia"><span>Anesthesia</span></a></li>
		<li><a href="#op_prop"><span>Op. Prop.</span></a></li>
		<li><a href="#meds"><span>D/C Meds</span></a></li>
		<li><a href="#others"><span>Others</span></a></li>-->
	</ul>
	<div id="basic_info">

			<label>Package Name:</label>
			{{$package_name}}
			<label>Package Price:</label>
			{{$package_price}}
			<label>Surgical?:</label><span style="vertical-align:middle">{{$issurgical}}</span>
			<label>Departments Assigned:</label>
			<select style="float:left;margin-top:2px;" name="departments">{{html_options options=$departments}}</select> {{$add_department}}
			<br style="clear:both"/>
			<table id="department_table">
				<tbody>

				</tbody>
			</table>

	</div>
	<div class="blues" id="details">
		<table>
			<tr>
				<td align="left" valign="middle">
				<button type="button" class="segButton" onclick="open_package_items('LB')" name="lab_add" id="lab_add">
				<img src="../../../gui/img/common/default/sitemap_animator.gif" />
				Add LAB items
				</button>
				 </td>
				 <td align="left" valign="middle">
					<button type="button" class="segButton" onclick="open_package_items('RD')" name="lab_add" id="lab_add">
					<img src="../../../gui/img/common/default/bilder.gif" />
				Add	RADIO items
					</button>
				 </td>
				 <td align="left" valign="middle">
					<button type="button" class="segButton" onclick="open_package_items('PH')" name="lab_add" id="lab_add">
					<img src="../../../gui/img/common/default/medicine.gif" />
				Add	PHARMA items
					</button>
				</td>
				 <td align="left" valign="middle">
					<button type="button" class="segButton" onclick="open_package_items('MISC')" name="lab_add" id="lab_add">
					<img src="../../../gui/img/common/default/indexbox3.gif" />
				Add	MISC items
					</button>
				</td>
			</tr>
		</table>
			<div id="item_purpose_list">
				<table id="purpose_table">
					<tbody id="items_table">
					</tbody>
				</table>
			</div>
		<table>
			<tr>

			</tr>
		</table>
	</div>
</div>
<br/>
{{$package_submit}}
{{$package_cancel}}
{{$is_submitted}}
{{if $package_id}}
	{{$package_id}}
{{/if}}
{{$form_end}}

</body>

</html>