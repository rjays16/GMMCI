<script>
	function redirectWindow(key){	
		 switch (key) {
		 	case 'c': //shift+c 67   Admission, ER/OPD-consultation
		 		//alert ("key="+key);
		 		window.location.href = "<?=$redirectAdConsult?>";
		 		break;
		 	case 'l':  //shift+l 76 Encounters' List
		 		window.location.href = "<?=$redirectEncList?>";
		 		break;
		 	case 'h':   //shift+h 72 medical history
		 		window.location.href = "<?=$redirectHistory?>";
		 		break;
		 	case 'i':  //shift+i  73 Icd10/Icpm
		 		window.location.href = "<?=$redirectIcd10?>";
		 		break;
		 	case 'f':  // clinical form shift+f
		 		window.location.href = "<?=$redirectForm?>";
		 		break;
		 	case 'e':  //Medical certficate shift+e
		 		//window.location.href = "<?=$redirectCert?>";
		 		window.open("<?=$redirectCert?>","medicalCertificate","modal, width=600,height=400,menubar=no,resizable=yes,scrollbars=no");
		 		//window.open("medical_certificate_pdf.php?id="+id,"medicalCertificate","modal, width=600,height=400,menubar=no,resizable=yes,scrollbars=no");
		 		break;
		 	case 'u':  // Update person registration  shift+u
		 		window.location.href = "<?=$redirectUpdate?>";
		 		break;
		 	case 'p': // show person registration shift+p
		 		window.location.href = "<?=$redirectShow?>";
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
			
			if(e.shiftKey){
				redirectWindow(char);
				//alert(char);
			}
			
		}
		YAHOO.util.Event.addListener(window, "keypress", init);
	})();		

</script>
