<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title></title>
{{foreach from=$css_and_js item=js}}
    {{$js}}
{{/foreach}}
<style>
</style>
</head>
<body>

{{$form_start}}
<div id="select_department">
  <div class="floated_divs">
    <div class="header">Please select a department</div>
    <table align="left" cellspacing="0" width="100%">
      {{$department_table}}
    </table>
  </div>
  <div class="floated_divs">
    <div class="header">Please select an operating room</div>
    <table>
    </table>
  </div>
</div>

{{$form_end}}
</body>
</html>