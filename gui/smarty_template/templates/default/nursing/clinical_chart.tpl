<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title></title>
{{foreach from=$css_and_js item=css_js}}
    {{$css_js}}
{{/foreach}}
</head>
<body>
  <div id="header_popup" class="clinical_chart_popup" align="left">
    <div class="jqDrag header"><span style="float:left">Graphical Chart</span>{{$close_popup}}<br style="clear:both" /></div>
                    
    <div class="body">
       <table border="1">
        <tr>
          <td>Date</td>
          <td>:</td>
          <td>{{$record_date}}{{$rd_icon}}</td>
        </tr>
        <tr>
          <td>Hospital Days</td>
          <td>:</td>
          <td>{{$hospital_days}}</td>
        </tr>
        <tr>
          <td>Day P.O. or P.P.</td>
          <td>:</td>
          <td>{{$day_po_pp}}</td>
        </tr>
      </table>
      {{$add_header}}
    </div>
    {{$resize}}
  </div>
  
  <div id="footer1_popup" class="clinical_chart_popup" align="left">
    <div class="jqDrag header"><span style="float:left">Graphical Chart</span>{{$close_popup}}<br style="clear:both" /></div>
                    
    <div class="body">
       <table border="1">
        <tr>
          <td>Respiration</td>
          <td>:</td>
          <td>{{$respiration}}</td>
        </tr>
        <tr>
          <td>Blood Pressure</td>
          <td>:</td>
          <td>{{$blood_pressure1}}/{{$blood_pressure2}}</td>
        </tr>
      </table>
      {{$add_first_footer}}
    </div>
    {{$resize}}
  </div>
  
  <div id="footer2_popup" class="clinical_chart_popup" align="left">
    <div class="jqDrag header"><span style="float:left">Graphical Chart</span>{{$close_popup}}<br style="clear:both" /></div>
                    
    <div class="body">
       <table border="1">
        <tr>
          <td>Weight</td>
          <td>:</td>
          <td>{{$weight}}</td>
		  <td><select name="weight_unit">{{html_options options=$weight_unit}}</select></td>
        </tr>
       
      </table>
      {{$add_second_footer}}
    </div>
    {{$resize}}
  </div>
  
   <div id="footer3_popup" class="clinical_chart_popup" align="left">
    <div class="jqDrag header"><span style="float:left">Graphical Chart</span>{{$close_popup}}<br style="clear:both" /></div>
                    
    <div class="body">
       <table border="1">
        <tr>
          <td>Intake Oral</td>
          <td>:</td>
          <td>{{$intake_oral}}</td>
        </tr>
        <tr>
          <td>Parenteral</td>
          <td>:</td>
          <td>{{$parenteral}}</td>
        </tr>
        <tr>
          <td>Output Urine</td>
          <td>:</td>
          <td>{{$output_urine}}</td>
        </tr>
		<tr>
          <td>Drainage</td>
          <td>:</td>
          <td>{{$drainage}}</td>
        </tr>
		<tr>
          <td>Emesis</td>
          <td>:</td>
          <td>{{$emesis}}</td>
        </tr>
		<tr>
          <td>Stools</td>
          <td>:</td>
          <td>{{$stool}}</td>
        </tr>
      </table>
      {{$add_third_footer}}
    </div>
    {{$resize}}
  </div>
  

  <table width="50%">
    <tr>
      <td class="segPanelHeader" colspan="2">Patient's Information</td>
    </tr>
    <tr>
      <td class="segPanel">Surname</td>
      <td class="segPanel">{{$person_last_name}}</td> 
    </tr>
    <tr>
      <td class="segPanel">Given Name</td>
      <td class="segPanel">{{$person_first_name}}</td>
    </tr>
    <tr>
      <td class="segPanel">Age</td>
      <td class="segPanel">{{$person_age}}</td>
    </tr>
    <tr>
      <td class="segPanel">Sex</td>
      <td class="segPanel">{{$person_gender}}</td>
    </tr>
    <tr>
      <td class="segPanel">Hospital Number</td>
      <td class="segPanel">{{$hospital_number}}</td>
    </tr>
    <tr>
      <td class="segPanel">Ward/Room</td>
      <td class="segPanel">{{$ward}}</td> 
    </tr>
    
    <tr>
      <td class="segPanelHeader" colspan="2">GRAPHIC CHART (Centigrade)</td>
    </tr>
    
    <tr>
      <td class="segPanel" colspan="2">
        <div>{{$clinical_chart}}</div>
        <map name="clinical_grid" id="my_grid">
          {{$header_area}}
          {{$image_area}}
          {{$footer_first}}
          {{$footer_second}} 
        </map>
      </td>
    </tr>
    
  </table>
  <div style="background:#0066FF; width:21; height:9;position:absolute;top:821;left:175;opacity:0.7;cursor:pointer;display:none" id="ss"></div>
  <div style="background:#0066FF; width:131; height:19;position:absolute;top:821;left:175;opacity:0.7;cursor:pointer;display:none" id="dd"></div>
  <div style="background:#0066FF; width:21; height:19;position:absolute;top:821;left:175;opacity:0.7;cursor:pointer;display:none" id="ee"></div>
  <div style="background:#0066FF; width:65; height:19;position:absolute;top:821;left:175;opacity:0.7;cursor:pointer;display:none" id="ff"></div>
  <div style="background:#0066FF; width:32; height:19;position:absolute;top:821;left:175;opacity:0.7;cursor:pointer;display:none" id="gg"></div>
  {{$pointer}}
  {{$mode}}
  {{$x_axis}}
  {{$y_axis}}
  {{$temperature}}
  {{$pulse}}
</body>
</html>