function get_daily_radiology(dept, day, month, year)
{
	var date=convert_date();
	alert(date); 	       
	get_radiology_today(date, department);     
}


function convert_date(day, month, year)
{
	if(day<10){day='0'+day;}
	if(month<10){month='0'+month; }
	return year+'-'+month+'-'+day;
}