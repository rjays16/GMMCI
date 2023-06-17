<?php
/* $Id: db_create.php3,v 1.3 2005/10/29 20:08:11 kaloyan_raev Exp $ */


require("./grab_globals.inc.php3");
 
require("./header.inc.php3");

$result = mysql_query("CREATE DATABASE " . db_name($db)) or mysql_die();

$message = "$strDatabase " . db_name($db) . " $strHasBeenCreated";
require("./db_details.php3");

?>
