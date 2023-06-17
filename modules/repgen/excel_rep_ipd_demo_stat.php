<?php
  #created by Cherry
  #11-17-09
  
  error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
  require('./roots.php');
  require($root_path.'include/inc_environment_global.php');
  require_once($root_path."/classes/excel/Writer.php");
  require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
  require_once($root_path.'include/care_api_classes/class_department.php'); 
  
  class ExcelGen_DemographicData extends Spreadsheet_Excel_Writer
  {
      var $worksheet;
      var $Headers;
      var $format1, $format1a, $format2, $format3;
      
      var $fromdate;
      var $todate;
      var $location;
      var $count;

      function ExcelGen_DemographicData($fromdate, $todate, $location)
      {
          $this->Spreadsheet_Excel_Writer();
          $this->worksheet = & $this->addWorksheet();
          $this->worksheet->setPaper(5);      // Legal
          $this->worksheet->setLandscape();
          $this->worksheet->setMarginTop(1.8);
          $this->worksheet->setMarginLeft(0.8);
          $this->worksheet->setMarginRight(0.8);
          $this->worksheet->setMarginBottom(0.3);
          $this->Headers = array(
              'Number of Patients', 'Districts',
              'Pay', 'Service', 'Total',
              'Non-PHIC', 'PHIC', 'OWWA',
              'Member/Dep', 'Indigent',
              '% of Grand Total'
            );
          $this->format1=& $this->addFormat();
          $this->format1->setSize(9);
          $this->format1->setBold();
          $this->format1->setAlign('center');
          $this->format1a=& $this->addFormat();
          $this->format1a->setSize(9);
          $this->format1a->setBold();
          $this->format1a->setAlign('center');
          $this->format1a->setTextWrap(1);
          $this->format2=& $this->addFormat();
          $this->format2->setSize(8);
          $this->format2->setAlign('left');
          $this->format2->setTextWrap(1);
          $this->format3=& $this->addFormat();
          $this->format3->setSize(9);
          $this->format3->setAlign('center');
          
          if ($fromdate) $this->fromdate=date("Y-m-d",strtotime($fromdate));
          if ($todate) $this->todate=date("Y-m-d",strtotime($todate));
          $this->location = $location;
      }
      
      function ExcelHeader()
      {
          $dept_obj = new Department();
          $objInfo = new Hospital_Admin();
    
          if ($row = $objInfo->getAllHospitalInfo()) {      
            $row['hosp_agency'] = strtoupper($row['hosp_agency']);
            $row['hosp_name']   = strtoupper($row['hosp_name']);
          }
          else {
            $row['hosp_country'] = "Republic of the Philippines";
            $row['hosp_agency']  = "PROVINCE OF BUKIDNON";
            $row['hosp_name']    = "BPH - Malaybalay";
            $row['hosp_addr1']   = "Malaybalay, Bukidnon";      
          }
          if ($this->fromdate==$this->todate)
            $text = "For ".date("F j, Y",strtotime($this->fromdate));
          else
            $text = "From ".date("F j, Y",strtotime($this->fromdate))." To ".date("F j, Y",strtotime($this->todate));            
            
          if ($this->dept_nr){
            $deptinfo = $dept_obj->getDeptAllInfo($this->dept_nr);
            $deptname = $deptinfo['name_formal'];
          }else
            $deptname = "All Department";
                   
          $this->worksheet->setHeader("".$row['hosp_country']."\n".$row['hosp_agency']."\n".$row['hosp_name']."\n".$row['hosp_addr1']."\n\nHOSPITAL STATISTICS REPORT\n".$text."\n",0.5);
          
          switch($this->location)
          {
            case '0': $dist = "Congressional districts within and outside the region"; break;
            case '1': $dist = "Congressional districts within the province"; break;
            case '2': $dist = "Congressional districts outside the province but within the region"; break;
            case '3': $dist = "Congressional districts outside the region"; break;
          }
          $this->worksheet->setColumn(0, 0, 20);
          $this->worksheet->setColumn(0, 1, 10);
          $this->worksheet->setColumn(0, 4, 15);
          $this->worksheet->setColumn(0, 5, 10);
          $this->worksheet->setColumn(0, 8, 15);
          $this->worksheet->setColumn(0, 10, 10); 
          $this->worksheet->setColumn(0, 11, 10);
          $this->worksheet->write(0, 0, $dist);
          $this->worksheet->write(1, 4, $this->Headers[0], $this->format1);
          $this->worksheet->write(2, 3, $this->Headers[2], $this->format1);
          $this->worksheet->write(2, 7, $this->Headers[3], $this->format1);
          $this->worksheet->write(3, 1, $this->Headers[5], $this->format1); 
          $this->worksheet->write(3, 2, $this->Headers[6], $this->format1);
          $this->worksheet->write(3, 5, $this->Headers[5], $this->format1);
          $this->worksheet->write(3, 6, $this->Headers[6], $this->format1);
          $this->worksheet->write(4, 0, $this->Headers[1], $this->format1);
          $this->worksheet->write(4, 2, $this->Headers[8], $this->format1); 
          $this->worksheet->write(4, 3, $this->Headers[9], $this->format1);
          $this->worksheet->write(4, 4, $this->Headers[7], $this->format1);
          $this->worksheet->write(4, 6, $this->Headers[8], $this->format1);
          $this->worksheet->write(4, 7, $this->Headers[9], $this->format1);
          $this->worksheet->write(4, 8, $this->Headers[7], $this->format1);
          $this->worksheet->write(4, 9, $this->Headers[4], $this->format1);
          $this->worksheet->write(4, 10, $this->Headers[10], $this->format1a);
      }
               
      function FetchData()
      {
          global $db;
          //To get grand total
          $sql_total = "SELECT SUM(t.total) as total FROM (SELECT count(e.encounter_nr) AS total

                        FROM  care_encounter AS e
                        INNER JOIN care_person AS p ON p.pid = e.pid 
                        LEFT JOIN seg_encounter_insurance AS i ON i.encounter_nr = e.encounter_nr
                        LEFT JOIN seg_encounter_memcategory AS em ON em.encounter_nr=e.encounter_nr
                        LEFT JOIN seg_municity AS mun ON mun.mun_nr=p.mun_nr
                        LEFT JOIN seg_provinces AS prov ON prov.prov_nr=mun.prov_nr
                        LEFT JOIN seg_regions AS reg ON reg.region_nr=prov.region_nr
                        LEFT JOIN (SELECT s.pid,SUBSTRING(MAX(CONCAT(s.grant_dte,s.discountid)),20) AS discountid 
                              FROM seg_charity_grants_pid AS s 
                              WHERE s.discountid='D'
                              GROUP BY s.pid 
                              ORDER BY s.pid, s.grant_dte DESC) AS soc ON soc.pid=e.pid
                        WHERE e.encounter_type IN (3,4) 
                        AND (DATE(e.encounter_date) BETWEEN '".$this->fromdate."' AND '".$this->todate."') 
                        AND e.status NOT IN ('deleted','hidden','inactive','void') 
                        ";
          
           //---------------------------------------------------------------------------
          
          //within Davao del Sur
          $sql_loc1 = "SELECT  mun.mun_name AS Districts, prov.prov_name,
                SUM(CASE WHEN (i.hcare_id<>18 OR i.hcare_id IS NULL) AND w.accomodation_type=2 then 1 else 0 end) AS pay_non_phic, 
                SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL) AND w.accomodation_type=2 then 1 else 0 end) AS pay_phic, 
                SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id=5 AND w.accomodation_type=2 then 1 else 0 end) AS pay_phic_indigent, 
                SUM(CASE WHEN em.memcategory_id=3 AND w.accomodation_type=2 then 1 else 0 end) AS pay_owwa, 
                SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL) AND w.accomodation_type=2 then 1 else 0 end) AS pay_phic_member, 
                SUM(CASE WHEN w.accomodation_type=2 then 1 else 0 end) AS total_pay, SUM(CASE WHEN (i.hcare_id<>18 OR i.hcare_id IS NULL) AND w.accomodation_type=1 then 1 else 0 end) AS charity_non_phic, 
                SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL) AND w.accomodation_type=1 then 1 else 0 end) AS charity_phic, 
                SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id=5 AND w.accomodation_type=1 then 1 else 0 end) AS charity_phic_indigent, 
                SUM(CASE WHEN em.memcategory_id=3 AND w.accomodation_type=1 then 1 else 0 end) AS charity_owwa, 
                SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL) AND w.accomodation_type=1 then 1 else 0 end) AS charity_phic_member, 
                SUM(CASE WHEN w.accomodation_type=1 then 1 else 0 end) AS total_charity, SUM(CASE WHEN (w.accomodation_type=1 OR w.accomodation_type=2) then 1 else 0 end) AS total
                
                FROM care_encounter AS e 
                
                INNER JOIN care_person AS p ON p.pid=e.pid
                INNER JOIN care_ward AS w ON e.current_ward_nr=w.nr 
                LEFT JOIN seg_encounter_insurance AS i ON i.encounter_nr=e.encounter_nr 
                LEFT JOIN care_person_insurance AS pti ON pti.pid=e.pid 
                LEFT JOIN seg_encounter_memcategory AS em ON em.encounter_nr=e.encounter_nr 
                LEFT JOIN seg_memcategory AS m ON m.memcategory_id=em.memcategory_id

                LEFT JOIN seg_municity AS mun ON mun.mun_nr=p.mun_nr
                LEFT JOIN seg_provinces AS prov ON prov.prov_nr=mun.prov_nr
                LEFT JOIN seg_regions AS reg ON reg.region_nr=prov.region_nr
                 
                WHERE e.encounter_type IN (3,4) 
                AND (DATE(e.admission_dt) BETWEEN '".$this->fromdate."' AND '".$this->todate."') 
                AND e.status NOT IN ('deleted','hidden','inactive','void') 
                AND prov.prov_name like '%Davao del Sur%'
                GROUP BY mun.mun_name
                ORDER BY SUM(CASE WHEN (w.accomodation_type=1 OR w.accomodation_type=2) then 1 else 0 end) DESC;
                ";
         //========================================================
             //All from Region XI excluding Davao del Sur
       
        $sql_loc2 = "SELECT  mun.mun_name AS Districts, prov.prov_name, reg.region_name,
                SUM(CASE WHEN (i.hcare_id<>18 OR i.hcare_id IS NULL) AND w.accomodation_type=2 then 1 else 0 end) AS pay_non_phic, 
                SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL) AND w.accomodation_type=2 then 1 else 0 end) AS pay_phic, 
                SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id=5 AND w.accomodation_type=2 then 1 else 0 end) AS pay_phic_indigent, 
                SUM(CASE WHEN em.memcategory_id=3 AND w.accomodation_type=2 then 1 else 0 end) AS pay_owwa, 
                SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL) AND w.accomodation_type=2 then 1 else 0 end) AS pay_phic_member, 
                SUM(CASE WHEN w.accomodation_type=2 then 1 else 0 end) AS total_pay, SUM(CASE WHEN (i.hcare_id<>18 OR i.hcare_id IS NULL) AND w.accomodation_type=1 then 1 else 0 end) AS charity_non_phic, 
                SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL) AND w.accomodation_type=1 then 1 else 0 end) AS charity_phic, 
                SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id=5 AND w.accomodation_type=1 then 1 else 0 end) AS charity_phic_indigent, 
                SUM(CASE WHEN em.memcategory_id=3 AND w.accomodation_type=1 then 1 else 0 end) AS charity_owwa, 
                SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL) AND w.accomodation_type=1 then 1 else 0 end) AS charity_phic_member, 
                SUM(CASE WHEN w.accomodation_type=1 then 1 else 0 end) AS total_charity, SUM(CASE WHEN (w.accomodation_type=1 OR w.accomodation_type=2) then 1 else 0 end) AS total
                FROM care_encounter AS e 
                INNER JOIN care_person AS p ON p.pid=e.pid
                INNER JOIN care_ward AS w ON e.current_ward_nr=w.nr 
                LEFT JOIN seg_encounter_insurance AS i ON i.encounter_nr=e.encounter_nr 
                LEFT JOIN care_person_insurance AS pti ON pti.pid=e.pid 
                LEFT JOIN seg_encounter_memcategory AS em ON em.encounter_nr=e.encounter_nr 
                LEFT JOIN seg_memcategory AS m ON m.memcategory_id=em.memcategory_id

                LEFT JOIN seg_municity AS mun ON mun.mun_nr=p.mun_nr
                LEFT JOIN seg_provinces AS prov ON prov.prov_nr=mun.prov_nr
                LEFT JOIN seg_regions AS reg ON reg.region_nr=prov.region_nr
                 
                WHERE e.encounter_type IN (3,4) 
                AND (DATE(e.admission_dt) BETWEEN '".$this->fromdate."' AND '".$this->todate."') 
                AND e.status NOT IN ('deleted','hidden','inactive','void') 
                AND reg.region_name='Region XI'
                AND prov.prov_name!='DAVAO DEL SUR'
                GROUP BY mun.mun_name
                ORDER BY prov.prov_name, SUM(CASE WHEN (w.accomodation_type=1 OR w.accomodation_type=2) then 1 else 0 end) DESC;";
 
       
        //=============================================================
            //Outside Region XI
      
      $sql_loc3 = "SELECT  mun.mun_name AS Districts, prov.prov_name, reg.region_name,
                    SUM(CASE WHEN (i.hcare_id<>18 OR i.hcare_id IS NULL) AND w.accomodation_type=2 then 1 else 0 end) AS pay_non_phic, 
                    SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL) AND w.accomodation_type=2 then 1 else 0 end) AS pay_phic, 
                    SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id=5 AND w.accomodation_type=2 then 1 else 0 end) AS pay_phic_indigent, 
                    SUM(CASE WHEN em.memcategory_id=3 AND w.accomodation_type=2 then 1 else 0 end) AS pay_owwa, 
                    SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL) AND w.accomodation_type=2 then 1 else 0 end) AS pay_phic_member, 
                    SUM(CASE WHEN w.accomodation_type=2 then 1 else 0 end) AS total_pay, SUM(CASE WHEN (i.hcare_id<>18 OR i.hcare_id IS NULL) AND w.accomodation_type=1 then 1 else 0 end) AS charity_non_phic, 
                    SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL) AND w.accomodation_type=1 then 1 else 0 end) AS charity_phic, 
                    SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id=5 AND w.accomodation_type=1 then 1 else 0 end) AS charity_phic_indigent, 
                    SUM(CASE WHEN em.memcategory_id=3 AND w.accomodation_type=1 then 1 else 0 end) AS charity_owwa, 
                    SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL) AND w.accomodation_type=1 then 1 else 0 end) AS charity_phic_member, 
                    SUM(CASE WHEN w.accomodation_type=1 then 1 else 0 end) AS total_charity, SUM(CASE WHEN (w.accomodation_type=1 OR w.accomodation_type=2) then 1 else 0 end) AS total
                    
                    FROM care_encounter AS e 
                    INNER JOIN care_person AS p ON p.pid=e.pid
                    INNER JOIN care_ward AS w ON e.current_ward_nr=w.nr 
                    LEFT JOIN seg_encounter_insurance AS i ON i.encounter_nr=e.encounter_nr 
                    LEFT JOIN care_person_insurance AS pti ON pti.pid=e.pid 
                    LEFT JOIN seg_encounter_memcategory AS em ON em.encounter_nr=e.encounter_nr 
                    LEFT JOIN seg_memcategory AS m ON m.memcategory_id=em.memcategory_id

                    LEFT JOIN seg_municity AS mun ON mun.mun_nr=p.mun_nr
                    LEFT JOIN seg_provinces AS prov ON prov.prov_nr=mun.prov_nr
                    LEFT JOIN seg_regions AS reg ON reg.region_nr=prov.region_nr
                     
                    WHERE e.encounter_type IN (3,4) 
                    AND (DATE(e.admission_dt) BETWEEN '".$this->fromdate."' AND '".$this->todate."') 
                    AND e.status NOT IN ('deleted','hidden','inactive','void') 
                    AND reg.region_name!='Region XI'
                    GROUP BY mun.mun_name
                    ORDER BY reg.region_name,prov.prov_name, SUM(CASE WHEN (w.accomodation_type=1 OR w.accomodation_type=2) then 1 else 0 end) DESC;";
 
       //===============================================================
            //All Regions (no discrimination =^_^=)
      $sql_loc0 = "SELECT  mun.mun_name AS Districts, prov.prov_name, reg.region_name,
                SUM(CASE WHEN (i.hcare_id<>18 OR i.hcare_id IS NULL) AND w.accomodation_type=2 then 1 else 0 end) AS pay_non_phic, 
                SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL) AND w.accomodation_type=2 then 1 else 0 end) AS pay_phic, 
                SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id=5 AND w.accomodation_type=2 then 1 else 0 end) AS pay_phic_indigent, 
                SUM(CASE WHEN em.memcategory_id=3 AND w.accomodation_type=2 then 1 else 0 end) AS pay_owwa, 
                SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL) AND w.accomodation_type=2 then 1 else 0 end) AS pay_phic_member, 
                SUM(CASE WHEN w.accomodation_type=2 then 1 else 0 end) AS total_pay, SUM(CASE WHEN (i.hcare_id<>18 OR i.hcare_id IS NULL) AND w.accomodation_type=1 then 1 else 0 end) AS charity_non_phic, 
                SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL) AND w.accomodation_type=1 then 1 else 0 end) AS charity_phic, 
                SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id=5 AND w.accomodation_type=1 then 1 else 0 end) AS charity_phic_indigent, 
                SUM(CASE WHEN em.memcategory_id=3 AND w.accomodation_type=1 then 1 else 0 end) AS charity_owwa, 
                SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL) AND w.accomodation_type=1 then 1 else 0 end) AS charity_phic_member, 
                SUM(CASE WHEN w.accomodation_type=1 then 1 else 0 end) AS total_charity, SUM(CASE WHEN (w.accomodation_type=1 OR w.accomodation_type=2) then 1 else 0 end) AS total
                FROM care_encounter AS e 
                INNER JOIN care_person AS p ON p.pid=e.pid
                INNER JOIN care_ward AS w ON e.current_ward_nr=w.nr 
                LEFT JOIN seg_encounter_insurance AS i ON i.encounter_nr=e.encounter_nr 
                LEFT JOIN care_person_insurance AS pti ON pti.pid=e.pid 
                LEFT JOIN seg_encounter_memcategory AS em ON em.encounter_nr=e.encounter_nr 
                LEFT JOIN seg_memcategory AS m ON m.memcategory_id=em.memcategory_id

                LEFT JOIN seg_municity AS mun ON mun.mun_nr=p.mun_nr
                LEFT JOIN seg_provinces AS prov ON prov.prov_nr=mun.prov_nr
                LEFT JOIN seg_regions AS reg ON reg.region_nr=prov.region_nr
                 
                WHERE e.encounter_type IN (3,4) 
                AND (DATE(e.admission_dt) BETWEEN '".$this->fromdate."' AND '".$this->todate."') 
                AND e.status NOT IN ('deleted','hidden','inactive','void') 
                GROUP BY mun.mun_name
                ORDER BY reg.region_name,prov.prov_name, SUM(CASE WHEN (w.accomodation_type=1 OR w.accomodation_type=2) then 1 else 0 end) DESC;";
                      
            

      if($this->location == 1){
        $sql = $sql_loc1;
        $sql_total .="AND prov.prov_name = 'Davao del Sur'
                    GROUP BY e.encounter_nr
                    ORDER BY count(e.encounter_nr) DESC) as t"; 
      }
      else if($this->location == 2){
        $sql = $sql_loc2;
        $sql_total .="AND reg.region_name='Region XI' 
                      AND prov.prov_name!='DAVAO DEL SUR'
                      GROUP BY e.encounter_nr
                      ORDER BY count(e.encounter_nr) DESC) as t ";
      }
      else if($this->location == 3){
        $sql = $sql_loc3;
        $sql_total .="AND reg.region_name!='Region XI'
                      GROUP BY e.encounter_nr
                      ORDER BY count(e.encounter_nr) DESC) as t";
      }
      else if ($this->location == 0){
        $sql = $sql_loc0;
        $sql_total .="GROUP BY e.encounter_nr
                      ORDER BY count(e.encounter_nr) DESC) as t";
      }
            
            $tot_result = $db->Execute($sql_total);
            $grandtotal = $tot_result->FetchRow();

            $result=$db->Execute($sql);
            if ($result)
            {
                  $this->count = $result->RecordCount();
                  $newrow=5;
                  while ($row=$result->FetchRow())
                  {
                    $percentage = ($row['total'] / $grandtotal['total']) * 100;
                    $percentage = round($percentage,2);        
                    $col=0;
                    $this->worksheet->write($newrow, $col, $row['Districts'], $this->format2);
                    $this->worksheet->write($newrow, $col+1, $row['pay_non_phic'], $this->format3);
                    $this->worksheet->write($newrow, $col+2, $row['pay_phic'], $this->format3);
                    $this->worksheet->write($newrow, $col+3, $row['pay_phic_indigent'], $this->format3);
                    $this->worksheet->write($newrow, $col+4, $row['pay_owwa'], $this->format3);
                    $this->worksheet->write($newrow, $col+5, $row['charity_non_phic'], $this->format3);
                    $this->worksheet->write($newrow, $col+6, $row['charity_phic'], $this->format3);
                    $this->worksheet->write($newrow, $col+7, $row['charity_phic_indigent'], $this->format3);
                    $this->worksheet->write($newrow, $col+8, $row['charity_owwa'], $this->format3);
                    $this->worksheet->write($newrow, $col+9, $row['total'], $this->format3);
                    $this->worksheet->write($newrow, $col+10, $percentage."%", $this->format3);
                    $newrow++;
                }
            
           }
      }

      function AfterData()
      {
          if (!$this->count) 
          {
            $this->worksheet->write(5, 0, "No records found for this report...");
          }
      }
  }
  
  $rep = new ExcelGen_DemographicData($_GET['from'], $_GET['to'], $_GET['location']);
  $rep->ExcelHeader();
  $rep->FetchData();
  $rep->AfterData();
  $rep->send('rep_ipd_demo_stat.xls');
  $rep->close();
?>