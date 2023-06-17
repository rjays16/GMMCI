<script>
	function redirectPage(key){	
		 switch (key) {
		 	case '.': //ctrl+ >    Next page 
		 		window.location.href = "<?=$yhNext?>";
		 		break;
		 	case ',':  //ctrl+< Previous page
		 		window.location.href = "<?=$yhPrev?>";
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
			var char = String.fromCharCode(k).toUpperCase();
			
			if(e.ctrlKey){
				redirectPage(char);
			//	alert(char);
			}
			
		}
		YAHOO.util.Event.addListener(window, "keypress", init);
	})();		

</script>
