{{foreach from=$css_and_js item=script}}
    {{$script}}
{{/foreach}}

<div id="or_main_calendar">

<div id="calback">
    <div id="calendar"></div>
</div>


</div>
<script type="text/javascript">navigate('','','');</script>

<div id="or_main_events" align="left">
  <div id="header" class="jqDrag"><span style="float:left">List of Events</span>{{$close_events}}<br style="clear:both" /></div>
  
  <div id="body" style="overflow: auto; height: 360px;">
    <div id="calback"></div>
  </div>
  

</div> 