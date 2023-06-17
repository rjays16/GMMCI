<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title></title>

{{foreach from=$css_and_js item=script}}
    {{$script}}
{{/foreach}}

</head>

<body>

<div id="select_or">
  <br/>
 
       
<div id="approve_or">

<!--<div id="search_bar" align="left">
  {{$search_field}}{{html_options name="qtype" options=$departments selected=$selected_department}}{{$search_button}}
</div>-->
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