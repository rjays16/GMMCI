<?php
include 'roots.php';
include '../../inc_environment_global.php';
include 'class_eTransmittalXml.php';

$test = new eTransmittalXml($_GET['transmit-number'],$_GET['category']);

header('Content-Type: text/xml');
$test->Generate();
echo $test->xmlToString();