//restrict to copy, cut and paste the whole document
function preset(){
	$J(document).ready(function(){
      $J(document).bind("cut copy paste",function(e) {
          e.preventDefault();
      });

      //restrict the right click
      block = setInterval("window.clipboardData.setData('text','')",2);  
      clearInterval(block);
      //restrict the print screen
      window.addEventListener("keyup",kPress,false);
    });
}

function kPress(e){ 
	var c=e.keyCode||e.charCode; 
	if (c==44) alert("print screen");
}