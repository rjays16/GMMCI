<div id="select_or" style="margin-bottom:5px;">
	<div id="charge_request">
		<div id="search_bar" align="left">
			{{$search_field}}{{$search_button}}
			<span style="font: normal 11px Arial;color:#000">
				Enter the search key(Patient's HRN, Name, and Request Date). Dates should be in the format MM.DD.YYYY
			</span>
		</div>
		<div id="navigation">
			<div class="group">
				<select class="segInput" name="number_of_pages">{{html_options options=$number_of_pages}}</select>
			</div>
			<div id="button_separator"></div>
			<div class="group">
				<div id="first" class="button"><span></span></div>
				<div id="prev" class="button"><span></span></div>
			</div>
			<div id="button_separator"></div>
			<div class="group"><span id="control">Page {{$page_number}} of <span></span></span></div>
			<div id="button_separator"></div>
			<div class="group">
				<div id="next" class="button"><span></span></div>
				<div id="last" class="button"><span></span></div>
			</div>
			<div id="button_separator"></div>
			<div class="group">
				<div id="reloader" class="pre_load button loading"><span></span></div>
			</div>
			<div id="button_separator"></div>
			<div class="group"><span id="page_stat">Processing, please wait...</span></div>
		</div>
		<table id="or_request_table" align="left"></table>
	</div>

	</div>
<div align="left">{{$return}}</div>
