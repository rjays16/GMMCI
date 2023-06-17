<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title></title>

{{foreach from=$css_and_js item=script}}
    {{$script}}
{{/foreach}}

</head>

<body>

<div id="cancel_or_main_request" align="left">
  <div id="header"><span style="float:left">Cancel OR Main Request</span>{{$close_cancel}}<br style="clear:both" /></div>
  
  <div id="body">
    This request cannot be cancelled unless a reason for cancellation is provided.
    Take note that once this request is cancelled, other operations such as 
    viewing/editing of the request details will be permanently removed. 
    {{$form_open}}
    {{$cancellation_reason_label}}{{$required_mark}}
    {{$error_msg}}
    {{$cancellation_reason}}
    
    {{$submit_cancel}}
    {{$cancel_cancel}}
    {{$submitted}}
    {{$refno}}
    {{$mode}}
    {{$form_close}}
    <br style="clear:both" />
    
  </div>
</div>

<div id="select_or">
  <br/>
 
       
<div id="charge_request">

<div id="search_bar" align="left">
  {{$search_field}}{{html_options name="qtype" options=$departments selected=$selected_department}}{{$search_button}}
</div>
<div id="navigation">
    
    <div class="group"><select name="number_of_pages">{{html_options options=$number_of_pages}}</select></div>
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

<div align="left">
<br/>
{{$return}}
</div>
</div>

  
</body>

</html>