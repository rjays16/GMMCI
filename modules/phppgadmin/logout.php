<?php

/**
 * Logs a user out of the app
 *
 * $Id: logout.php,v 1.2 2005/10/29 20:08:14 kaloyan_raev Exp $
 */

if (!ini_get('session.auto_start')) {
	session_name('PPA_ID'); 
	session_start();
}
unset($_SESSION);
session_destroy();

header('Location: index.php');

?>
