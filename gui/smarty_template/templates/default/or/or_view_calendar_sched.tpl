<html>
<head>
	<!-- link calendar files  -->                                                                                         
	<link rel="stylesheet" type="text/css" media="all" href="<?=$root_path?>js/calendar_or_schedule/calendar.css" type="text/css"/>
<script type="text/javascript" src="<?=$root_path?>js/calendar_or_schedule/calendar_or.js"></script>    
</head>
<body>

<div align="center" style="height:400px">
	 
{{$sFormStart}}
	{{$dateInput}}	
	<!-- calendar attaches to existing form element -->
	{{$jsCalendarSetup}}
{{$sFormEnd}}
{{$sOnHoverMenu}} 

</div>
</body>                
</html>