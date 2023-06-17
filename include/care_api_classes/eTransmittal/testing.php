<?php
include 'XmlTransmittal.php';

$test = new XmlTransmittal($_GET['transmit-number'],$_GET['category']);

if(count($test->xml->getErrors()) > 0){
    var_dump($test->xml->getErrors());
}else{
    header('Content-Type: text/xml');
    echo $test->xml->toString();
}