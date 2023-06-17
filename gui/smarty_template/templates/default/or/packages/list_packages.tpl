<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title></title>

{{foreach from=$css_and_js item=script}}
    {{$script}}
{{/foreach}}

</head>

<body>

<div id="new_package_popup" align="left"> <!-- new_package_popup start -->
  <div id="header" class="jqDrag"><span style="float:left">Create New Package</span>{{$close_new_package_popup}}<br style="clear:both" /></div>

  <div id="body"> <!-- body starts -->
    <ul id="#container">
      <li><a href="#fragment-1"><span>One</span></a></li>
      <li><a href="#fragment-2"><span>Two</span></a></li>

             <li><a href="#fragment-3"><span>Three</span></a></li>

    </ul>
  </div>  <!-- body ends -->
  {{$resize}}
</div> <!-- new_package_popup end -->

<div id="select_or" style="margin-bottom:5px;">

<div id="charge_request">

<div id="search_bar" align="left">
  {{$search_field}}{{$search_button}}
  &nbsp;&nbsp;&nbsp;
  {{$new_package}}
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



</div>
<div align="left">

</div>
</body>

</html>