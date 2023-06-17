<script>
	function redirectMedTabs(key){	
		if(key == 'r'){
		 window.location.href = "<?=$yhMedSearch?>";
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
				redirectMedTabs(char);
			//	alert(char);
			}
			
		}
		YAHOO.util.Event.addListener(window, "keypress", init);
	})();		

</script>