<?php
  
?>

<html>
<head>
<SCRIPT LANGUAGE="JavaScript">

function sendValue(){
window.opener.document.sakeok = true;
window.close();
}

</script>
</head>
<body>
<center>
<form name=selectform>
<select name=selectmenu size="8">
<option value="Item A">Item A
<option value="Item B">Item B
<option value="Item C">Item C
<option value="Item D">Item D
<option value="Item E">Item E
<option value="Item F">Item F
<option value="Item G">Item G
<option value="Item H">Item H
<option value="Item I">Item I
<option value="Item J">Item J
<option value="Item K">Item K
<option value="Item L">Item L
<option value="Item M">Item M
<option value="Item N">Item N
<option value="Item O">Item O
<option value="Item P">Item P
<option value="Item Q">Item Q
<option value="Item R">Item R
<option value="Item S">Item S
<option value="Item T">Item T
<option value="Item U">Item U
<option value="Item V">Item V
<option value="Item W">Item W
<option value="Item X">Item X
<option value="Item Y">Item Y
<option value="Item Z">Item Z
</select>
<p>
<input type=button value="Save" onClick="sendValue();">
</form>
</center>
</body>
</html>
