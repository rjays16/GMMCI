<?php
  require('./roots.php');
  require($root_path.'include/inc_environment_global.php');
    
  global $db;
  
  $sql = 'SELECT * FROM seg_blood_received_sample_d
          WHERE qty_ordered > 1';
          
  $rs = $db->Execute($sql);
  while ($row=$rs->Fetchrow()){
      #if (($refno!=$row['refno']) && ($service_code!=$row['service_code'])){
          for($i=1;$i<$row['qty_ordered'];$i++){
            echo "<br>".$row['refno']."==".$row['service_code'];
            $b = $i+1;
            $sql2 =  "INSERT INTO seg_blood_received_details 
                     SELECT d.refno, service_code, 
                     '$b' AS ordering, received_date, '' AS component,
                     '' AS SERIAL, 'received' AS STATUS, 
                     CONCAT('Update ',received_date,' ',receiver_id) AS history, receiver_id AS create_id, received_date AS create_dt, receiver_id AS modify_id, received_date AS modify_dt
                     FROM seg_blood_received_sample_d d
                     INNER JOIN seg_blood_received_sample_h h ON h.refno=d.refno
                     where d.refno='".$row['refno']."' and service_code='".$row['service_code']."'";
            $rs2 = $db->Execute($sql2);
            #echo "<br>".$sql2;
          }     
         #echo "<br><br>";
      #}              
      #$refno = $row['refno'];
      #$service_code = $row['service_code'];          
  }        
  
?>
