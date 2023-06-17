<?php

  require('./roots.php');
  require($root_path.'include/inc_environment_global.php');
  require($root_path.'include/care_api_classes/class_walkin.php');
  
  $key = strtolower( $_GET['s'] );
  $len = strlen($key);
  $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 0;
  
  $aResults = array();
  
  if ($len)  {
    
    $wc = new SegWalkin();  
    $result = $wc->getWalkin($key);
    
    if ($result) {
      while (($row=$result->FetchRow())!==FALSE) {

        // had to use utf_decode, here
        // not necessary if the results are coming from mysql
        //
        
        $name = $row['name_last'];
        if ($row['name_first'])
          $name.=", ".$row['name_first'];
        $aResults[] = array( "id"=>$row['pid'] ,"value"=>htmlspecialchars($name), "info"=>htmlspecialchars($row["address"]) );
        
        if ($limit && $count==$limit)
          break;
      }
    }
  }
  
  header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
  header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
  header ("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
  header ("Pragma: no-cache"); // HTTP/1.0
  
  sleep(1);
  
  if (isset($_REQUEST['json']))
  {
    header("Content-Type: application/json");
  
    echo "{\"results\": [";
    $arr = array();
    for ($i=0;$i<count($aResults);$i++)
    {
      $arr[] = "{\"id\": \"".$aResults[$i]['id']."\", \"value\": \"".$aResults[$i]['value']."\", \"info\":\"".$aResults[$i]['info']."\"}";
    }
    echo implode(", ", $arr);
    echo "]}";
  }
  else
  {
    header("Content-Type: text/xml");

    echo "<?xml version=\"1.0\" encoding=\"utf-8\" ?><results>";
    for ($i=0;$i<count($aResults);$i++)
    {
      echo "<rs id=\"".$aResults[$i]['id']."\" info=\"".$aResults[$i]['info']."\">".$aResults[$i]['value']."</rs>";
    }
    echo "</results>";
  }
