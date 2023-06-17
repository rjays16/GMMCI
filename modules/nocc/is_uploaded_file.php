<?php 
/*
 * $Header: /cvsroot/care2002/Care2002/modules/nocc/is_uploaded_file.php,v 1.2 2005/10/29 20:08:10 kaloyan_raev Exp $
 *
 * Copyright 2001 Nicolas Chalanset <nicocha@free.fr>
 * Copyright 2001 Olivier Cahagne <cahagn_o@epita.fr>
 *
 * See the enclosed file COPYING for license information (GPL).  If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 *
 * Function is_uploaded_file in case PHP < 4.0.2 is used
 */

function is_uploaded_file($filename)
{
	if (!$tmp_file = ini_get('upload_tmp_dir'))
		$tmp_file = dirname(tempnam('', ''));
    $tmp_file .= '/' . basename($filename);
    return (ereg_replace('/+', '/', $tmp_file) == $filename);
}
?>