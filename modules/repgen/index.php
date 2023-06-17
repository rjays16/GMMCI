<?php
	include("pdf_rep_opdtrans.php");
	$icd = new RepGen_OPD_Trans();
	$icd->FetchData();
	$icd->Report();
?>