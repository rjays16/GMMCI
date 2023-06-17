{{* supply-office-dept.tpl Template for selecting department. 2008-10-27 Paul Timothy B. Matunog *}}

<ul>
<table border=0>
	<tr>
		<td>
			{{$sMascotImg}}
		</td>

		<td colspan=4 class="prompt">
			<center>
			{{$sSelectDept}}
			</center>
		</td>
	</tr>
</table>

<table cellpadding="3" cellspacing=1 border="0" width="400">
	{{$sDeptRows}}
</table>

<p>
{{$sBackLink}}
</ul>
<p>