<?php
# created by Vanessa A. Saren
class DateGenerator{

	var $intYear;
	var $intMonth;
	var $intDay;
	var $bolSetToCurrentDay;
	
	var $intHour;
	var $intMinutes;
	var $intSecounds;
	var $intMeridiem;
	var $bolSetToCurrentTime;

	function DateGenerator(){
		$this->bolSetToday = false;
		$this->intYear  = date("Y");
		$this->intMonth = date("m");
		$this->intDay   = date("d");
		
		$this->bolSetTodayTime = false;
		$this->intHour   = date("h");
		$this->intMinutes   = date("i");
		$this->intSeconds   = date("s");
		$this->intMeridiem   = date("A");
	}

	function setToCurrentDay(){
		$this->bolSetToCurrentDay = true;
	}
	
	function setToCurrentTime(){
		$this->bolSetToCurrentTime = true;
	}


	#Generate Year range
	function genYear($selName = 'Year', $yearCount = 16, $year = ''){
		#global $_SESSION;
		/*
			Check if the year passed in is the same as current year.
			If the year got is not given or same as current year, the list 
			will select the current year by default.  Otherwise, $yearSelect
			will be set to what user entered.
		*/
		#echo "yr = ".$_SESSION['yr'];
		$yearSelect = $year == '' ? date("Y") : $year;
		
		/*
			$yearCount: it is the length of your drop down list, i.e. how many 
			years do you want to show.  It is 50 by default, which shows 50 years
			from now.
		*/
		
		$str = "<select name='$selName' id='$selName'>\n";
		#for($i = $yearSelect; $i >= ($yearSelect - $yearCount); $i--){
		$yearbegin = 2001;
		for($i = $yearbegin; $i <= ($yearbegin + $yearCount); $i++){
			if($this->bolSetToCurrentDay == true){
				$selected = $this->intYear == $i ? 'selected="selected"' : '';
			}else{
				$selected = $_SESSION['yr'] == $i ? 'selected="selected"' : '';
			}
			$str .= "\t<option value='$i' $selected>$i</option>\n";
		}
		$str .= "</select>\n";
		print $str;
	}

	#Generate month range from 1 to 12
	function genMonth($selName = 'Month', $date_format = 'short'){
		#global $_SESSION;
		$shortM = array(1 => "Jan", "Feb", "Mar" ,
							 "Apr", "May", "Jun" ,
							 "Jul", "Aug", "Sept",
							 "Oct", "Nov", "Dec");
		
		$longM  = array(1 => "January", "February", "March",
							 "April"  , "May" 	  , "June" ,
							 "July"	  , "Aug" 	  , "September",
							 "October", "November", "December");
	
		$str = "<select name='$selName' id='$selName'>\n";
		if($date_format == 'short'){
			for($i = 1; $i <= 12; $i++){
				if($this->bolSetToCurrentDay == true){
					$selected = $this->intMonth == $i ? 'selected="selected"' : '';
				}else{
					$selected = $_SESSION['mon'] == $i ? 'selected="selected"' : '';
				}
				$str .= "\t<option value='$i' $selected>".$shortM[$i]."</option>\n";
			}
		}elseif($date_format == 'long'){
			for($i = 1; $i <= 12; $i++){
				if($this->bolSetToCurrentDay == true){
					$selected = $this->intMonth == $i ? 'selected="selected"' : '';
				}else{
					$selected = $_SESSION['mon'] == $i ? 'selected="selected"' : '';
				}
				$str .= "\t<option value='$i' $selected>".$longM[$i]."</option>\n";
			}
		}
		$str .= "</select>\n";

		print $str;
	}

	#Generate day range from 1 to 31
	function genDay($selName = 'Day'){
		$str = "<select name='$selName' id='$selName'>\n";
		for($i = 1; $i <= 31; $i++){
			if($this->bolSetToCurrentDay == true){
				$selected = $this->intDay == $i ? 'selected="selected"' : '';
			}else{
				$selected = $_SESSION['day'] == $i ? 'selected="selected"' : '';
			}
			$str .= "\t<option value='$i' $selected>$i</option>\n";
		}
		$str .= "</select>\n";
		print $str;
	}
	
	#Generate Hour range from 1 to 12
	function genHour($selName = 'Hour'){
		$str = "<select name='$selName' id='$selName'>\n";
		for($i = 1; $i <= 12; $i++){
			if($this->bolSetToCurrentTime == true){
				$selected = $this->intHour == $i ? 'selected="selected"' : '';
			}else{
				$selected = $_SESSION['hr'] == $i ? 'selected="selected"' : '';
			}
			$str .= "\t<option value='$i' $selected>$i</option>\n";
		}
		$str .= "</select>\n";
		print $str;
	}

	#Generate Minutes range from 1 to 60
	function genMinutes($selName = 'Minutes'){
		$str = "<select name='$selName' id='$selName'>\n";
		for($i = 0; $i < 60; $i++){
			if($this->bolSetToCurrentTime == true){
				$selected = $this->intMinutes == $i ? 'selected="selected"' : '';
			}else{
				$selected = $_SESSION['min'] == $i ? 'selected="selected"' : '';
			}
			$str .= "\t<option value='$i' $selected>$i</option>\n";
		}
		$str .= "</select>\n";
		print $str;
	}

	#Generate Seconds range from 1 to 60
	function genSeconds($selName = 'Seconds'){
		$str = "<select name='$selName' id='$selName'>\n";
		for($i = 0; $i < 60; $i++){
			if($this->bolSetToCurrentTime == true){
				$selected = $this->intSeconds == $i ? 'selected="selected"' : '';
			}else{
				$selected = $_SESSION['sec'] == $i ? 'selected="selected"' : '';
			}
			$str .= "\t<option value='$i' $selected>$i</option>\n";
		}
		$str .= "</select>\n";
		print $str;
	}
	
	#Generate Meridiem range from 1 to 12
	function genMeridiem($selName = 'Meridiem'){
		$meridime = array("AM", "PM");
		
		$str = "<select name='$selName' id='$selName'>\n";
		
		foreach ($meridime as $i) {
		    if($this->bolSetToCurrentTime == true){
				   $selected = $this->intMeridiem == $i ? 'selected="selected"' : '';
			 }else{
			 		$selected = $_SESSION['mer'] == $i ? 'selected="selected"' : '';
			 }
				$str .= "\t<option value='$i' $selected>".$i."</option>\n";
		}

		$str .= "</select>\n";

		print $str;
	}
}
?>