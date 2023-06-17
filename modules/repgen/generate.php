<?php
	include($_GET['file']);
	$icd = new RepGen_OPD_Trans();
	$icd->FetchData();
	$icd->Report();
?>