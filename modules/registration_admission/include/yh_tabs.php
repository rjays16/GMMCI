<script>
	function redirectTabs(key){	
		 switch (key) {
		 	case 'p': //Alt+p   New Patient 
		 		//alert ("key="+key);
		 		window.location.href = "<?=$redirectNewPatient?>";
		 		break;
		 	case 'z':  //Alt+z Search
		 		window.location.href = "<?=$redirectSearch?>";
		 		break;
		 	case 'x':   //alt+x Advanced Search
		 		window.location.href = "<?=$redirectAdSearch?>";
		 		break;
		 	case 'a':  //alt+a Admission
		 		window.location.href = "<?=$redirectAdmission?>";
		 		break;
			case 'c':  //alt+c Admission
		 		window.location.href = "<?=$redirectCompSearch?>";
		 		break;	
			case 'n':  //alt+n Admission
		 		window.location.href = "<?=$redirectNewBorn?>";
		 		break;	
			case 'd':  //alt+m Admission
		 		window.location.href = "<?=$redirectMainMenu?>";
		 		break;	
		 }
	}
	(function(){
		var init = function (e){
			e = e || window.event.e;
			var k='';
          
          //option for this person 
			if(e.keyCode) k = e.keyCode;
			else if(e.which)k = e.which;
			var char = String.fromCharCode(k).toLowerCase();
			
			if(e.altKey){
				redirectTabs(char);
				//alert(char);
			}
			
		}
		YAHOO.util.Event.addListener(window, "keypress", init);
	})();		

</script>
