<?php
/* $Id: grab_globals.inc.php3,v 1.3 2005/10/29 20:08:11 kaloyan_raev Exp $ */;

/**
 * This library grabs the names and values of the variables sent or posted to a
 * script in the '$HTTP_*_VARS' arrays and sets simple globals variables from
 * them
 */
if (!empty($HTTP_GET_VARS)) {
	while(list($name, $value) = each($HTTP_GET_VARS))
		$$name = $value;
}

if (!empty($HTTP_POST_VARS)) {
	while(list($name, $value) = each($HTTP_POST_VARS))
		$$name = $value;
}

if (!empty($HTTP_POST_FILES)) {
	while(list($name, $value) = each($HTTP_POST_FILES))
		$$name = $value['tmp_name'];
}
?>