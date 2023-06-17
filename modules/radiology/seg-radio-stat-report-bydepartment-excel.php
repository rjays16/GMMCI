<?php
//created by cha
  require('./roots.php');
  error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);    
  require($root_path."/classes/excel/Writer.php"); 
  require_once($root_path.'include/inc_environment_global.php');
  include_once($root_path.'include/inc_date_format_functions.php');
  
  #require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
  #$srvObj=new SegLab;
  require_once($root_path.'include/care_api_classes/class_radiology.php');
  $radio_Obj=new SegRadio;
  require_once($root_path.'include/care_api_classes/class_department.php');
  $dept_obj=new Department;
  require_once($root_path.'include/care_api_classes/class_person.php');
  $person_obj=new Person;
  require_once($root_path.'include/care_api_classes/class_encounter.php');
  $enc_obj=new Encounter;
  require_once($root_path.'include/care_api_classes/class_personell.php');
  $pers_obj=new Personell;
  require_once($root_path.'include/care_api_classes/class_ward.php');
  $ward_obj=new Ward;
  
  require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
  $objInfo = new Hospital_Admin();
  
  require($root_path.'classes/adodb/adodb.inc.php');
  include($root_path.'include/inc_init_hclab_main.php');
  include($root_path.'include/inc_seg_mylib.php');
   
  // Creating a workbook
$workbook = new Spreadsheet_Excel_Writer();

// sending HTTP headers
$workbook->send('radio_stat_report_bydept.xls');

// Creating a worksheet
$worksheet =& $workbook->addWorksheet();
$worksheet->setHeader("Gonzales Maranan Medical Center Incorporated\nDEPARTMENT OF RADIOLOGICAL & IMAGING SCIENCES\nROENTGENOLOGICAL STATISTICS REPORT\n",0.3);

//format of text
$worksheet->setLandscape();
$worksheet->setPaper(5);        //8 is A3 size, 9 is A4, 5 is Legal
$worksheet->setMarginTop(1.0);
$worksheet->setMarginLeft(0.5);
$worksheet->setMarginRight(0.04);
$worksheet->setMarginBottom(0.8);

$header_format=& $workbook->addFormat();
$header_format->setSize(8);
$header_format->setBold();
$header_format->setAlign('center');

$deptname_format=& $workbook->addFormat();
$deptname_format->setSize(8);
$deptname_format->setBold();
$deptname_format->setAlign('left');

$category_format=& $workbook->addFormat();
$category_format->setSize(8);
$category_format->setAlign('left');

$patient_format=& $workbook->addFormat();
$patient_format->setSize(8);
$patient_format->setAlign('right');

$number_format=& $workbook->addFormat();
$number_format->setSize(8);
$number_format->setAlign('right');

$summary_format=& $workbook->addFormat();
$summary_format->setSize(9);
$summary_format->setAlign('left');


//begin data here
$datefrom = $_GET['fromdate'];
$dateto = $_GET['todate'];
  
global $db;

$row=0;
$col=0;

#$worksheet->setColumn(0,0,3);
#$worksheet->write(0,0, "Charlene");
$worksheet->write($row, $col, "Date:");
$worksheet->write($row, $col+1, date("F d, Y "));
$worksheet->write($row+1, $col, "Time:");
$worksheet->write($row+1, $col+1, date("h:i:s A"));
$worksheet->write($row+2, $col, "Start Date:");
$worksheet->write($row+2, $col+1, date("F d, Y ", strtotime($datefrom)));
$worksheet->write($row+3, $col, "End Date:");
$worksheet->write($row+3, $col+1, date("F d, Y ", strtotime($dateto)));
$row=5;

$totalcount = 0;
$totalyear = 0; 
$report_info = $radio_Obj->getStatReport($datefrom, $dateto);
$totalcount = $radio_Obj->count;
if($totalcount)
{
  while($row=$report_info->FetchRow())
  {
      $report_year = $radio_Obj->getStatReportByYear($row['year'], $datefrom, $dateto);
      $report_year2 = $radio_Obj->getStatReportByYear($row['year'], $datefrom, $dateto);      
      $report_year3 = $radio_Obj->getStatReportByYear($row['year'], $datefrom, $dateto);      
      $report_year4 = $radio_Obj->getStatReportByYear($row['year'], $datefrom, $dateto);
      $report_year5 = $radio_Obj->getStatReportByYear($row['year'], $datefrom, $dateto);     
      $report_year6 = $radio_Obj->getStatReportByYear($row['year'], $datefrom, $dateto);
      $report_year7 = $radio_Obj->getStatReportByYear($row['year'], $datefrom, $dateto);      
      $report_year8 = $radio_Obj->getStatReportByYear($row['year'], $datefrom, $dateto);
      $report_year9 = $radio_Obj->getStatReportByYear($row['year'], $datefrom, $dateto);     
      $report_year10 = $radio_Obj->getStatReportByYear($row['year'], $datefrom, $dateto);
      $report_year11 = $radio_Obj->getStatReportByYear($row['year'], $datefrom, $dateto);
      $totalyear = $radio_Obj->count;
      //echo "totalyear=".$totalyear;
      if ($totalyear)
      {
        $buf = array();
        $buf_er_wo = array();
        $buf_opd_wo = array();
        $buf_in_wo = array();
        $buf_ipd_wo = array();
        
        $buf_er_w = array();
        $buf_opd_w = array();
        $buf_in_w = array();
        $buf_ipd_w = array();
        
        $i=0;
        while ($row_yr2=$report_year2->FetchRow())
        {         
          $month = $radio_Obj->getMonth($row_yr2['month']);
          $row_yr=$row_yr2['year'];
          $buf[$row_yr2['year']][] = $row_yr2['month'];
          $i++;
        }
        
        //header
        #$worksheet->setColumn(5, 4, 30);
        $worksheet->write(5, $col+2, "1st qrt.", $header_format);
        #$worksheet->setColumn(5, 8, 30); 
        $worksheet->write(5, $col+6, "2nd qrt.", $header_format);
        #$worksheet->setColumn(5, 12, 30); 
        $worksheet->write(5, $col+10, "3rd qrt.", $header_format);
        #$worksheet->setColumn(5, 16, 30); 
        $worksheet->write(5, $col+14, "4th qrt.", $header_format);
        //$row++;
        $worksheet->write(6, $col+2, "Jan", $header_format);
        $worksheet->write(6, $col+3, "Feb", $header_format);
        $worksheet->write(6, $col+4, "Mar", $header_format);
        $worksheet->write(6, $col+5, "Total", $header_format);
        $worksheet->write(6, $col+6, "Apr", $header_format);
        $worksheet->write(6, $col+7, "May", $header_format);
        $worksheet->write(6, $col+8, "Jun", $header_format);
        $worksheet->write(6, $col+9, "Total", $header_format);
        $worksheet->write(6, $col+10, "Jul", $header_format);
        $worksheet->write(6, $col+11, "Aug", $header_format);
        $worksheet->write(6, $col+12, "Sep", $header_format);
        $worksheet->write(6, $col+13, "Total", $header_format);
        $worksheet->write(6, $col+14, "Oct", $header_format);
        $worksheet->write(6, $col+15, "Nov", $header_format);
        $worksheet->write(6, $col+16, "Dec", $header_format);
        $worksheet->write(6, $col+17, "Total", $header_format);
        //$row+=2;
        $report_dept = $radio_Obj->getMainRadioDepartment();
        $totaldept = $radio_Obj->count;
        $row=7;       
        for($i=0;$i<16;$i++)
        { $total_er_w_ar[$i]=0;
          $total_opd_w_ar[$i]=0;
          $total_in_w_ar[$i]=0;
          $total_ipd_w_ar[$i]=0;              
          $total_er_wo[$i]=0;
          $total_opd_wo[$i]=0;
          $total_in_wo[$i]=0;
          $total_ipd_wo[$i]=0;
          $total_er_w[$i]=0;
          $total_opd_w[$i]=0;
          $total_in_w[$i]=0;
          $total_ipd_w[$i]=0; } 
        while($row_yr=$report_year->FetchRow())
        {
            if($totaldept)
            {
              while ($row_dept=$report_dept->FetchRow())
              {
                $col=0;
                $worksheet->write($row, $col, strtoupper($row_dept['name_formal']), $deptname_format);
                $row=$row+2;
                $report_group = $radio_Obj->getRadioServiceGroupsbyDept($row_dept['nr']);
                for($i=0;$i<16;$i++)
                {
                  $er_wo_ar[$i]=0;                
                  $opd_wo_ar[$i]=0;                
                  $in_wo_ar[$i]=0;                
                  $ipd_wo_ar[$i]=0;
                  $total_wo_ar[$i]=0;
                  $er_w_ar[$i]=0;                
                  $opd_w_ar[$i]=0;                
                  $in_w_ar[$i]=0;                
                  $ipd_w_ar[$i]=0;                
                  $total_w_ar[$i]=0;              
                  $total_er_wo_ar[$i]=0;
                  $total_opd_wo_ar[$i]=0;
                  $total_in_wo_ar[$i]=0;
                  $total_ipd_wo_ar[$i]=0;
                  $total_er_w_ar[$i]=0;
                  $total_opd_w_ar[$i]=0;
                  $total_in_w_ar[$i]=0;
                  $total_ipd_w_ar[$i]=0;
                }
                while($row_mnth=$report_group->FetchRow())
                {
                    $enctype = '1';
                    $buf_er_wo=array();
                    $er_wo=0;                    
                    for ($i=0; $i<$totalyear;$i++)
                    {                   
                      $er_stat_wo = $radio_Obj->getStatByResultEncType($row_yr['year'], $buf[$row_yr['year']][$i], $datefrom, $dateto, $row_mnth['group_code'], $enctype, 0);
                      if ($er_stat_wo['stat_result'])
                        $er_wo = $er_stat_wo['stat_result'];
                      else
                        $er_wo = 0;
                      
                      $buf_er_wo[$i] = $er_wo;
                      $total_er_wo[$i] = $total_er_wo[$i] + $buf_er_wo[$i];
                      switch($buf[$row_yr['year']][$i])
                      {
                        case 1: $er_wo_ar[0]+=$er_wo; $total_er_wo_ar[0]+=$buf_er_wo[$i]; break;
                        case 2: $er_wo_ar[1]+=$er_wo; $total_er_wo_ar[1]+=$buf_er_wo[$i]; break;
                        case 3: $er_wo_ar[2]+=$er_wo; $total_er_wo_ar[2]+=$buf_er_wo[$i]; break;
                        case 4: $er_wo_ar[4]+=$er_wo; $total_er_wo_ar[4]+=$buf_er_wo[$i]; break;
                        case 5: $er_wo_ar[5]+=$er_wo; $total_er_wo_ar[5]+=$buf_er_wo[$i]; break;
                        case 6: $er_wo_ar[6]+=$er_wo; $total_er_wo_ar[6]+=$buf_er_wo[$i]; break;
                        case 7: $er_wo_ar[8]+=$er_wo; $total_er_wo_ar[8]+=$buf_er_wo[$i]; break;
                        case 8: $er_wo_ar[9]+=$er_wo; $total_er_wo_ar[9]+=$buf_er_wo[$i]; break;
                        case 9: $er_wo_ar[10]+=$er_wo; $total_er_wo_ar[10]+=$buf_er_wo[$i]; break;
                        case 10: $er_wo_ar[12]+=$er_wo; $total_er_wo_ar[12]+=$buf_er_wo[$i]; break;
                        case 11: $er_wo_ar[13]+=$er_wo; $total_er_wo_ar[13]+=$buf_er_wo[$i]; break;
                        case 12: $er_wo_ar[14]+=$er_wo; $total_er_wo_ar[14]+=$buf_er_wo[$i]; break;
                      }
                      
                    }
                    $er_wo_ar[3]=($er_wo_ar[0]+$er_wo_ar[1]+$er_wo_ar[2]);
                    $er_wo_ar[7]=($er_wo_ar[4]+$er_wo_ar[5]+$er_wo_ar[6]);
                    $er_wo_ar[11]=($er_wo_ar[8]+$er_wo_ar[9]+$er_wo_ar[10]);
                    $er_wo_ar[15]=($er_wo_ar[12]+$er_wo_ar[13]+$er_wo_ar[14]);                 
                    $total_er_wo_ar[3]=($total_er_wo_ar[0]+$total_er_wo_ar[1]+$total_er_wo_ar[2]);
                    $total_er_wo_ar[7]=($total_er_wo_ar[4]+$total_er_wo_ar[5]+$total_er_wo_ar[6]);
                    $total_er_wo_ar[11]=($total_er_wo_ar[8]+$total_er_wo_ar[9]+$total_er_wo_ar[10]);
                    $total_er_wo_ar[15]=($total_er_wo_ar[12]+$total_er_wo_ar[13]+$total_er_wo_ar[14]);
                    
                    $enctype = '2';
                    $buf_opd_wo=array();
                    $opd_wo=0;
                    for ($i=0; $i<$totalyear;$i++){
                      $opd_stat_wo = $radio_Obj->getStatByResultEncType($row_yr['year'], $buf[$row_yr['year']][$i], $datefrom, $dateto, $row_mnth['group_code'], $enctype, 0);
                      if ($opd_stat_wo['stat_result'])
                        $opd_wo = $opd_stat_wo['stat_result'];
                      else
                        $opd_wo = 0;
                      
                      $buf_opd_wo[$i] = $opd_wo;
                      $total_opd_wo[$i] = $total_opd_wo[$i] + $buf_opd_wo[$i];
                      switch($buf[$row_yr['year']][$i])
                      {
                        case 1: $opd_wo_ar[0]+=$opd_wo; $total_opd_wo_ar[0]+=$buf_opd_wo[$i]; break;
                        case 2: $opd_wo_ar[1]+=$opd_wo; $total_opd_wo_ar[1]+=$buf_opd_wo[$i]; break;
                        case 3: $opd_wo_ar[2]+=$opd_wo; $total_opd_wo_ar[2]+=$buf_opd_wo[$i]; break;
                        case 4: $opd_wo_ar[4]+=$opd_wo; $total_opd_wo_ar[4]+=$buf_opd_wo[$i]; break;
                        case 5: $opd_wo_ar[5]+=$opd_wo; $total_opd_wo_ar[5]+=$buf_opd_wo[$i]; break;
                        case 6: $opd_wo_ar[6]+=$opd_wo; $total_opd_wo_ar[6]+=$buf_opd_wo[$i]; break;
                        case 7: $opd_wo_ar[8]+=$opd_wo; $total_opd_wo_ar[8]+=$buf_opd_wo[$i]; break;
                        case 8: $opd_wo_ar[9]+=$opd_wo; $total_opd_wo_ar[9]+=$buf_opd_wo[$i]; break;
                        case 9: $opd_wo_ar[10]+=$opd_wo; $total_opd_wo_ar[10]+=$buf_opd_wo[$i]; break;
                        case 10: $opd_wo_ar[12]+=$opd_wo; $total_opd_wo_ar[12]+=$buf_opd_wo[$i]; break;
                        case 11: $opd_wo_ar[13]+=$opd_wo; $total_opd_wo_ar[13]+=$buf_opd_wo[$i]; break;
                        case 12: $opd_wo_ar[14]+=$opd_wo; $total_opd_wo_ar[14]+=$buf_opd_wo[$i]; break;
                      }                                          
                    }                                     
                    $opd_wo_ar[3]=($opd_wo_ar[0]+$opd_wo_ar[1]+$opd_wo_ar[2]);
                    $opd_wo_ar[7]=($opd_wo_ar[4]+$opd_wo_ar[5]+$opd_wo_ar[6]);
                    $opd_wo_ar[11]=($opd_wo_ar[8]+$opd_wo_ar[9]+$opd_wo_ar[10]);
                    $opd_wo_ar[15]=($opd_wo_ar[12]+$opd_wo_ar[13]+$opd_wo_ar[14]);                    
                    $total_opd_wo_ar[3]=($total_opd_wo_ar[0]+$total_opd_wo_ar[1]+$total_opd_wo_ar[2]);
                    $total_opd_wo_ar[7]=($total_opd_wo_ar[4]+$total_opd_wo_ar[5]+$total_opd_wo_ar[6]);
                    $total_opd_wo_ar[11]=($total_opd_wo_ar[8]+$total_opd_wo_ar[9]+$total_opd_wo_ar[10]);
                    $total_opd_wo_ar[15]=($total_opd_wo_ar[12]+$total_opd_wo_ar[13]+$total_opd_wo_ar[14]);
 
                    $enctype = '0';
                    $buf_in_wo=array();
                    $in_wo=0;
                    for ($i=0; $i<$totalyear;$i++){
                      $in_stat_wo = $radio_Obj->getStatByResultEncType($row_yr['year'], $buf[$row_yr['year']][$i], $datefrom, $dateto, $row_mnth['group_code'], $enctype, 0);
                      if ($in_stat_wo['stat_result'])
                        $in_wo = $in_stat_wo['stat_result'];
                      else
                        $in_wo = 0;
                      
                      $buf_in_wo[$i] = $in_wo;
                      $total_in_wo[$i] = $total_in_wo[$i] + $buf_in_wo[$i];
                      switch($buf[$row_yr['year']][$i])
                      {
                        case 1: $in_wo_ar[0]+=$in_wo; $total_in_wo_ar[0]+=$buf_in_wo[$i]; break;
                        case 2: $in_wo_ar[1]+=$in_wo; $total_in_wo_ar[1]+=$buf_in_wo[$i]; break;
                        case 3: $in_wo_ar[2]+=$in_wo; $total_in_wo_ar[2]+=$buf_in_wo[$i]; break;
                        case 4: $in_wo_ar[4]+=$in_wo; $total_in_wo_ar[4]+=$buf_in_wo[$i]; break;
                        case 5: $in_wo_ar[5]+=$in_wo; $total_in_wo_ar[5]+=$buf_in_wo[$i]; break;
                        case 6: $in_wo_ar[6]+=$in_wo; $total_in_wo_ar[6]+=$buf_in_wo[$i]; break;
                        case 7: $in_wo_ar[8]+=$in_wo; $total_in_wo_ar[8]+=$buf_in_wo[$i]; break;
                        case 8: $in_wo_ar[9]+=$in_wo; $total_in_wo_ar[9]+=$buf_in_wo[$i]; break;
                        case 9: $in_wo_ar[10]+=$in_wo; $total_in_wo_ar[10]+=$buf_in_wo[$i]; break;
                        case 10: $in_wo_ar[12]+=$in_wo; $total_in_wo_ar[12]+=$buf_in_wo[$i]; break;
                        case 11: $in_wo_ar[13]+=$in_wo; $total_in_wo_ar[13]+=$buf_in_wo[$i]; break;
                        case 12: $in_wo_ar[14]+=$in_wo; $total_in_wo_ar[14]+=$buf_in_wo[$i]; break;
                      }                     
                    }
                    $in_wo_ar[3]=($in_wo_ar[0]+$in_wo_ar[1]+$in_wo_ar[2]);
                    $in_wo_ar[7]=($in_wo_ar[4]+$in_wo_ar[5]+$in_wo_ar[6]);
                    $in_wo_ar[11]=($in_wo_ar[8]+$in_wo_ar[9]+$in_wo_ar[10]);
                    $in_wo_ar[15]=($in_wo_ar[12]+$in_wo_ar[13]+$in_wo_ar[14]);                     
                    $total_in_wo_ar[3]=($total_in_wo_ar[0]+$total_in_wo_ar[1]+$total_in_wo_ar[2]);
                    $total_in_wo_ar[7]=($total_in_wo_ar[4]+$total_in_wo_ar[5]+$total_in_wo_ar[6]);
                    $total_in_wo_ar[11]=($total_in_wo_ar[8]+$total_in_wo_ar[9]+$total_in_wo_ar[10]);
                    $total_in_wo_ar[15]=($total_in_wo_ar[12]+$total_in_wo_ar[13]+$total_in_wo_ar[14]);
                                        
                    $enctype = '3,4';
                    $buf_ipd_wo=array();
                    $ipd_wo=0;
                    for ($i=0; $i<$totalyear;$i++){
                      $ipd_stat_wo = $radio_Obj->getStatByResultEncType($row_yr['year'], $buf[$row_yr['year']][$i], $datefrom, $dateto, $row_mnth['group_code'], $enctype, 0);
                      if ($ipd_stat_wo['stat_result'])
                        $ipd_wo = $ipd_stat_wo['stat_result'];
                      else
                        $ipd_wo = 0;
                      
                      $buf_ipd_wo[$i] = $ipd_wo;
                      $total_ipd_wo[$i] = $total_ipd_wo[$i] + $buf_ipd_wo[$i];
                      switch($buf[$row_yr['year']][$i])
                      {
                        case 1: $ipd_wo_ar[0]+=$ipd_wo; $total_ipd_wo_ar[0]+=$buf_ipd_wo[$i]; break;
                        case 2: $ipd_wo_ar[1]+=$ipd_wo; $total_ipd_wo_ar[1]+=$buf_ipd_wo[$i]; break;
                        case 3: $ipd_wo_ar[2]+=$ipd_wo; $total_ipd_wo_ar[2]+=$buf_ipd_wo[$i]; break;
                        case 4: $ipd_wo_ar[4]+=$ipd_wo; $total_ipd_wo_ar[4]+=$buf_ipd_wo[$i]; break;
                        case 5: $ipd_wo_ar[5]+=$ipd_wo; $total_ipd_wo_ar[5]+=$buf_ipd_wo[$i]; break;
                        case 6: $ipd_wo_ar[6]+=$ipd_wo; $total_ipd_wo_ar[6]+=$buf_ipd_wo[$i]; break;
                        case 7: $ipd_wo_ar[8]+=$ipd_wo; $total_ipd_wo_ar[8]+=$buf_ipd_wo[$i]; break;
                        case 8: $ipd_wo_ar[9]+=$ipd_wo; $total_ipd_wo_ar[9]+=$buf_ipd_wo[$i]; break;
                        case 9: $ipd_wo_ar[10]+=$ipd_wo; $total_ipd_wo_ar[10]+=$buf_ipd_wo[$i]; break;
                        case 10: $ipd_wo_ar[12]+=$ipd_wo; $total_ipd_wo_ar[12]+=$buf_ipd_wo[$i]; break;
                        case 11: $ipd_wo_ar[13]+=$ipd_wo; $total_ipd_wo_ar[13]+=$buf_ipd_wo[$i]; break;
                        case 12: $ipd_wo_ar[14]+=$ipd_wo; $total_ipd_wo_ar[14]+=$buf_ipd_wo[$i]; break;
                      }
                    }
                    $ipd_wo_ar[3]=($ipd_wo_ar[0]+$ipd_wo_ar[1]+$ipd_wo_ar[2]);
                    $ipd_wo_ar[7]=($ipd_wo_ar[4]+$ipd_wo_ar[5]+$ipd_wo_ar[6]);
                    $ipd_wo_ar[11]=($ipd_wo_ar[8]+$ipd_wo_ar[9]+$ipd_wo_ar[10]);
                    $ipd_wo_ar[15]=($ipd_wo_ar[12]+$ipd_wo_ar[13]+$ipd_wo_ar[14]);                    
                    $total_ipd_wo_ar[3]=($total_ipd_wo_ar[0]+$total_ipd_wo_ar[1]+$total_ipd_wo_ar[2]);
                    $total_ipd_wo_ar[7]=($total_ipd_wo_ar[4]+$total_ipd_wo_ar[5]+$total_ipd_wo_ar[6]);
                    $total_ipd_wo_ar[11]=($total_ipd_wo_ar[8]+$total_ipd_wo_ar[9]+$total_ipd_wo_ar[10]);
                    $total_ipd_wo_ar[15]=($total_ipd_wo_ar[12]+$total_ipd_wo_ar[13]+$total_ipd_wo_ar[14]);
                    
                    for ($i=0; $i<$totalyear;$i++){
                      $total_wo = $buf_er_wo[$i] + $buf_opd_wo[$i] + $buf_in_wo[$i] + $buf_ipd_wo[$i];
                    }
                    
                    for($i=0;$i<16;$i++)
                    {
                      $total_wo_ar[$i]=($total_er_wo_ar[$i]+$total_opd_wo_ar[$i]+$total_in_wo_ar[$i]+$total_ipd_wo_ar[$i]);
                    }
                    
                    //number of patients served                                    
                    $enctype = '1';
                    $buf_er_w=array();
                    $er_w=0;
                    for ($i=0; $i<$totalyear;$i++){
                      $er_stat_w = $radio_Obj->getStatByResultEncType($row_yr['year'], $buf[$row_yr['year']][$i], $datefrom, $dateto, $row_mnth['group_code'], $enctype, 1);
                      if ($er_stat_w['stat_result'])
                        $er_w = $er_stat_w['stat_result'];
                      else
                        $er_w = 0;
                      
                      $buf_er_w[$i] = $er_w;
                      $total_er_w[$i] = $total_er_w[$i] + $buf_er_w[$i];
                      switch($buf[$row_yr['year']][$i])
                      {
                        case 1: $er_w_ar[0]+=$er_w; $total_er_w_ar[0]+=$buf_er_w[$i]; break;
                        case 2: $er_w_ar[1]+=$er_w; $total_er_w_ar[1]+=$buf_er_w[$i]; break;
                        case 3: $er_w_ar[2]+=$er_w; $total_er_w_ar[2]+=$buf_er_w[$i]; break;
                        case 4: $er_w_ar[4]+=$er_w; $total_er_w_ar[4]+=$buf_er_w[$i]; break;
                        case 5: $er_w_ar[5]+=$er_w; $total_er_w_ar[5]+=$buf_er_w[$i]; break;
                        case 6: $er_w_ar[6]+=$er_w; $total_er_w_ar[6]+=$buf_er_w[$i]; break;
                        case 7: $er_w_ar[8]+=$er_w; $total_er_w_ar[8]+=$buf_er_w[$i]; break;
                        case 8: $er_w_ar[9]+=$er_w; $total_er_w_ar[9]+=$buf_er_w[$i]; break;
                        case 9: $er_w_ar[10]+=$er_w; $total_er_w_ar[10]+=$buf_er_w[$i]; break;
                        case 10: $er_w_ar[12]+=$er_w; $total_er_w_ar[12]+=$buf_er_w[$i]; break;
                        case 11: $er_w_ar[13]+=$er_w; $total_er_w_ar[13]+=$buf_er_w[$i]; break;
                        case 12: $er_w_ar[14]+=$er_w; $total_er_w_ar[14]+=$buf_er_w[$i]; break;
                      }                      
                    }
                    $er_w_ar[3]=($er_w_ar[0]+$er_w_ar[1]+$er_w_ar[2]);
                    $er_w_ar[7]=($er_w_ar[4]+$er_w_ar[5]+$er_w_ar[6]);
                    $er_w_ar[11]=($er_w_ar[8]+$er_w_ar[9]+$er_w_ar[10]);
                    $er_w_ar[15]=($er_w_ar[12]+$er_w_ar[13]+$er_w_ar[14]);                        
                    $total_er_w_ar[3]=($total_er_w_ar[0]+$total_er_w_ar[1]+$total_er_w_ar[2]);
                    $total_er_w_ar[7]=($total_er_w_ar[4]+$total_er_w_ar[5]+$total_er_w_ar[6]);
                    $total_er_w_ar[11]=($total_er_w_ar[8]+$total_er_w_ar[9]+$total_er_w_ar[10]);
                    $total_er_w_ar[15]=($total_er_w_ar[12]+$total_er_w_ar[13]+$total_er_w_ar[14]);
                    
                                       
                    $enctype = '2';
                    $buf_opd_w=array();
                    $opd_w=0;
                    for ($i=0; $i<$totalyear;$i++){
                      $opd_stat_w = $radio_Obj->getStatByResultEncType($row_yr['year'], $buf[$row_yr['year']][$i], $datefrom, $dateto, $row_mnth['group_code'], $enctype, 1);
                      if ($opd_stat_w['stat_result'])
                        $opd_w = $opd_stat_w['stat_result'];
                      else
                        $opd_w = 0;
                      
                      $buf_opd_w[$i] = $opd_w;
                      $total_opd_w[$i] = $total_opd_w[$i] + $buf_opd_w[$i];
                      switch($buf[$row_yr['year']][$i])
                      {
                        case 1: $opd_w_ar[0]+=$opd_w; $total_opd_w_ar[0]+=$buf_opd_w[$i]; break;
                        case 2: $opd_w_ar[1]+=$opd_w; $total_opd_w_ar[1]+=$buf_opd_w[$i]; break;
                        case 3: $opd_w_ar[2]+=$opd_w; $total_opd_w_ar[2]+=$buf_opd_w[$i]; break;
                        case 4: $opd_w_ar[4]+=$opd_w; $total_opd_w_ar[4]+=$buf_opd_w[$i]; break;
                        case 5: $opd_w_ar[5]+=$opd_w; $total_opd_w_ar[5]+=$buf_opd_w[$i]; break;
                        case 6: $opd_w_ar[6]+=$opd_w; $total_opd_w_ar[6]+=$buf_opd_w[$i]; break;
                        case 7: $opd_w_ar[8]+=$opd_w; $total_opd_w_ar[8]+=$buf_opd_w[$i]; break;
                        case 8: $opd_w_ar[9]+=$opd_w; $total_opd_w_ar[9]+=$buf_opd_w[$i]; break;
                        case 9: $opd_w_ar[10]+=$opd_w; $total_opd_w_ar[10]+=$buf_opd_w[$i]; break;
                        case 10: $opd_w_ar[12]+=$opd_w; $total_opd_w_ar[12]+=$buf_opd_w[$i]; break;
                        case 11: $opd_w_ar[13]+=$opd_w; $total_opd_w_ar[13]+=$buf_opd_w[$i]; break;
                        case 12: $opd_w_ar[14]+=$opd_w; $total_opd_w_ar[14]+=$buf_opd_w[$i]; break;
                      }                                                            
                    }
                    $opd_w_ar[3]=($opd_w_ar[0]+$opd_w_ar[1]+$opd_w_ar[2]);
                    $opd_w_ar[7]=($opd_w_ar[4]+$opd_w_ar[5]+$opd_w_ar[6]);
                    $opd_w_ar[11]=($opd_w_ar[8]+$opd_w_ar[9]+$opd_w_ar[10]);
                    $opd_w_ar[15]=($opd_w_ar[12]+$opd_w_ar[13]+$opd_w_ar[14]);                      
                    $total_opd_w_ar[3]=($total_opd_w_ar[0]+$total_opd_w_ar[1]+$total_opd_w_ar[2]);
                    $total_opd_w_ar[7]=($total_opd_w_ar[4]+$total_opd_w_ar[5]+$total_opd_w_ar[6]);
                    $total_opd_w_ar[11]=($total_opd_w_ar[8]+$total_opd_w_ar[9]+$total_opd_w_ar[10]);
                    $total_opd_w_ar[15]=($total_opd_w_ar[12]+$total_opd_w_ar[13]+$total_opd_w_ar[14]);
                     
                    $enctype = '0';
                    $buf_in_w=array();
                    $in_w=0;
                    for ($i=0; $i<$totalyear;$i++){
                      $in_stat_w = $radio_Obj->getStatByResultEncType($row_yr['year'], $buf[$row_yr['year']][$i], $datefrom, $dateto, $row_mnth['group_code'], $enctype, 1);
                      if ($in_stat_w['stat_result'])
                        $in_w = $in_stat_w['stat_result'];
                      else
                        $in_w = 0;
                      
                      $buf_in_w[$i] = $in_w;
                      $total_in_w[$i] = $total_in_w[$i] + $buf_in_w[$i];
                      switch($buf[$row_yr['year']][$i])
                      {
                        case 1: $in_w_ar[0]+=$in_w; $total_in_w_ar[0]+=$buf_in_w[$i]; break;
                        case 2: $in_w_ar[1]+=$in_w; $total_in_w_ar[1]+=$buf_in_w[$i]; break;
                        case 3: $in_w_ar[2]+=$in_w; $total_in_w_ar[2]+=$buf_in_w[$i]; break;
                        case 4: $in_w_ar[4]+=$in_w; $total_in_w_ar[4]+=$buf_in_w[$i]; break;
                        case 5: $in_w_ar[5]+=$in_w; $total_in_w_ar[5]+=$buf_in_w[$i]; break;
                        case 6: $in_w_ar[6]+=$in_w; $total_in_w_ar[6]+=$buf_in_w[$i]; break;
                        case 7: $in_w_ar[8]+=$in_w; $total_in_w_ar[8]+=$buf_in_w[$i]; break;
                        case 8: $in_w_ar[9]+=$in_w; $total_in_w_ar[9]+=$buf_in_w[$i]; break;
                        case 9: $in_w_ar[10]+=$in_w; $total_in_w_ar[10]+=$buf_in_w[$i]; break;
                        case 10: $in_w_ar[12]+=$in_w; $total_in_w_ar[12]+=$buf_in_w[$i]; break;
                        case 11: $in_w_ar[13]+=$in_w; $total_in_w_ar[13]+=$buf_in_w[$i]; break;
                        case 12: $in_w_ar[14]+=$in_w; $total_in_w_ar[14]+=$buf_in_w[$i]; break;
                      }                      
                    }
                    $in_w_ar[3]=($in_w_ar[0]+$in_w_ar[1]+$in_w_ar[2]);
                    $in_w_ar[7]=($in_w_ar[4]+$in_w_ar[5]+$in_w_ar[6]);
                    $in_w_ar[11]=($in_w_ar[8]+$in_w_ar[9]+$in_w_ar[10]);
                    $in_w_ar[15]=($in_w_ar[12]+$in_w_ar[13]+$in_w_ar[14]);                      
                    $total_in_w_ar[3]=($total_in_w_ar[0]+$total_in_w_ar[1]+$total_in_w_ar[2]);
                    $total_in_w_ar[7]=($total_in_w_ar[4]+$total_in_w_ar[5]+$total_in_w_ar[6]);
                    $total_in_w_ar[11]=($total_in_w_ar[8]+$total_in_w_ar[9]+$total_in_w_ar[10]);
                    $total_in_w_ar[15]=($total_in_w_ar[12]+$total_in_w_ar[13]+$total_in_w_ar[14]);   

                    $enctype = '3,4';
                    $buf_ipd_w=array();
                    $ipd_w=0;
                    for ($i=0; $i<$totalyear;$i++){
                      $ipd_stat_w = $radio_Obj->getStatByResultEncType($row_yr['year'], $buf[$row_yr['year']][$i], $datefrom, $dateto, $row_mnth['group_code'], $enctype, 1);
                      if ($ipd_stat_w['stat_result'])
                        $ipd_w = $ipd_stat_w['stat_result'];
                      else
                        $ipd_w = 0;
                      
                      $buf_ipd_w[$i] = $ipd_w;
                      $total_ipd_w[$i] = $total_ipd_w[$i] + $buf_ipd_w[$i];
                      switch($buf[$row_yr['year']][$i])
                      {
                        case 1: $ipd_w_ar[0]+=$ipd_w; $total_ipd_w_ar[0]+=$buf_ipd_w[$i]; break;
                        case 2: $ipd_w_ar[1]+=$ipd_w; $total_ipd_w_ar[1]+=$buf_ipd_w[$i]; break;
                        case 3: $ipd_w_ar[2]+=$ipd_w; $total_ipd_w_ar[2]+=$buf_ipd_w[$i]; break;
                        case 4: $ipd_w_ar[4]+=$ipd_w; $total_ipd_w_ar[4]+=$buf_ipd_w[$i]; break;
                        case 5: $ipd_w_ar[5]+=$ipd_w; $total_ipd_w_ar[5]+=$buf_ipd_w[$i]; break;
                        case 6: $ipd_w_ar[6]+=$ipd_w; $total_ipd_w_ar[6]+=$buf_ipd_w[$i]; break;
                        case 7: $ipd_w_ar[8]+=$ipd_w; $total_ipd_w_ar[8]+=$buf_ipd_w[$i]; break;
                        case 8: $ipd_w_ar[9]+=$ipd_w; $total_ipd_w_ar[9]+=$buf_ipd_w[$i]; break;
                        case 9: $ipd_w_ar[10]+=$ipd_w; $total_ipd_w_ar[10]+=$buf_ipd_w[$i]; break;
                        case 10: $ipd_w_ar[12]+=$ipd_w; $total_ipd_w_ar[12]+=$buf_ipd_w[$i]; break;
                        case 11: $ipd_w_ar[13]+=$ipd_w; $total_ipd_w_ar[13]+=$buf_ipd_w[$i]; break;
                        case 12: $ipd_w_ar[14]+=$ipd_w; $total_ipd_w_ar[14]+=$buf_ipd_w[$i]; break;
                      }                      
                    }
                    $ipd_w_ar[3]=($ipd_w_ar[0]+$ipd_w_ar[1]+$ipd_w_ar[2]);
                    $ipd_w_ar[7]=($ipd_w_ar[4]+$ipd_w_ar[5]+$ipd_w_ar[6]);
                    $ipd_w_ar[11]=($ipd_w_ar[8]+$ipd_w_ar[9]+$ipd_w_ar[10]);
                    $ipd_w_ar[15]=($ipd_w_ar[12]+$ipd_w_ar[13]+$ipd_w_ar[14]);                     
                    $total_ipd_w_ar[3]=($total_ipd_w_ar[0]+$total_ipd_w_ar[1]+$total_ipd_w_ar[2]);
                    $total_ipd_w_ar[7]=($total_ipd_w_ar[4]+$total_ipd_w_ar[5]+$total_ipd_w_ar[6]);
                    $total_ipd_w_ar[11]=($total_ipd_w_ar[8]+$total_ipd_w_ar[9]+$total_ipd_w_ar[10]);
                    $total_ipd_w_ar[15]=($total_ipd_w_ar[12]+$total_ipd_w_ar[13]+$total_ipd_w_ar[14]);

                                        
                    for ($i=0; $i<$totalyear;$i++){
                      $total_w = $buf_er_w[$i] + $buf_opd_w[$i] + $buf_in_w[$i] + $buf_ipd_w[$i];
                    }
                     
                    for($i=0;$i<16;$i++)
                    {
                      $total_w_ar[$i]=($total_er_w_ar[$i]+$total_opd_w_ar[$i]+$total_in_w_ar[$i]+$total_ipd_w_ar[$i]);
                    }
                }
                $col=0;
                $worksheet->write($row, $col, "Number of Examinations", $category_format);
                $row=$row+1;
                $col=1;
                $worksheet->write($row, $col, "ER", $patient_format);
                $col=2;
                for($i=0;$i<16;$i++)
                { $worksheet->write($row, $col, $total_er_wo_ar[$i], $number_format); 
                  $total_er_wo[$i]+=$total_er_wo_ar[$i];
                  $col++; }
                $row=$row+1;
                $col=1;
                $worksheet->write($row, $col, "OPD", $patient_format);
                $col=2;
                for($i=0;$i<16;$i++)
                { $worksheet->write($row, $col, $total_opd_wo_ar[$i], $number_format); 
                  $total_opd_wo[$i]+=$total_opd_wo_ar[$i];
                  $col++; }
                $row=$row+1;
                $col=1;
                $worksheet->write($row, $col, "Industrial", $patient_format);
                $col=2;
                for($i=0;$i<16;$i++)
                { $worksheet->write($row, $col, $total_in_wo_ar[$i], $number_format); 
                  $total_in_wo[$i]+=$total_in_wo_ar[$i];
                  $col++; }
                $row=$row+1;
                $col=1;
                $worksheet->write($row, $col, "In-patient", $patient_format);
                $col=2;
                for($i=0;$i<16;$i++)
                { $worksheet->write($row, $col, $total_ipd_wo_ar[$i], $number_format); 
                  $total_ipd_wo[$i]+=$total_ipd_wo_ar[$i];
                  $col++; }
                $row=$row+1;
                $col=1;
                $worksheet->write($row, $col, "TOTAL", $patient_format);
                $col=2;
                for($i=0;$i<16;$i++)
                { $worksheet->write($row, $col, $total_wo_ar[$i], $number_format); $col++; }
                
                $row=$row+1;
                $col=0;
                $worksheet->write($row, $col, "Number of Patients served", $category_format);
                $row=$row+1;
                $col=1;
                $worksheet->write($row, $col, "ER", $patient_format);
                $col=2;
                for($i=0;$i<16;$i++)
                { $worksheet->write($row, $col, $total_er_w_ar[$i], $number_format); 
                  $total_er_w[$i]+=$total_er_w_ar[$i];
                  $col++; }
                $row=$row+1;
                $col=1;
                $worksheet->write($row, $col, "OPD", $patient_format);
                $col=2;
                for($i=0;$i<16;$i++)
                { $worksheet->write($row, $col, $total_opd_w_ar[$i], $number_format); 
                  $total_opd_w[$i]+=$total_opd_w_ar[$i];
                  $col++; }
                $row=$row+1;
                $col=1;
                $worksheet->write($row, $col, "Industrial", $patient_format);
                $col=2;
                for($i=0;$i<16;$i++)
                { $worksheet->write($row, $col, $total_in_w_ar[$i], $number_format); 
                  $total_in_w[$i]+=$total_in_w_ar[$i];
                  $col++; }
                $row=$row+1;
                $col=1;
                $worksheet->write($row, $col, "In-patient", $patient_format);
                $col=2;
                for($i=0;$i<16;$i++)
                { $worksheet->write($row, $col, $total_ipd_w_ar[$i], $number_format);
                  $total_ipd_w[$i]+=$total_ipd_w_ar[$i];
                  $col++; }
                $row=$row+1;
                $col=1;
                $worksheet->write($row, $col, "TOTAL", $patient_format);
                $col=2;
                for($i=0;$i<16;$i++)
                { $worksheet->write($row, $col, $total_w_ar[$i], $number_format); $col++; }
                
                $row=$row+2;
              }
            }
        } 
      }
      /*$col=0;
      $worksheet->write($row, $col, 'Average No. of Persons/day :', $category_format);
      $average_px=array();
      while ($row=$report_year3->FetchRow()){
        $totalinfo = $radio_Obj->getStatByYearMonth($row['year'], $row['month']);
        switch($row['month'])
        {
        case 1: $average_px[0]=$totalinfo['totalpat']; break;
        case 2: $average_px[1]=$totalinfo['totalpat']; break;
        case 3: $average_px[2]=$totalinfo['totalpat']; break;
        case 4: $average_px[4]=$totalinfo['totalpat']; break;
        case 5: $average_px[5]=$totalinfo['totalpat']; break;
        case 6: $average_px[6]=$totalinfo['totalpat']; break;
        case 7: $average_px[8]=$totalinfo['totalpat']; break;
        case 8: $average_px[9]=$totalinfo['totalpat']; break;
        case 9: $average_px[10]=$totalinfo['totalpat']; break;
        case 10: $average_px[12]=$totalinfo['totalpat']; break;
        case 11: $average_px[13]=$totalinfo['totalpat']; break;
        case 12: $average_px[14]=$totalinfo['totalpat']; break;
        }                      
        $average_px[3]=round(($average_px[0]/30)+($average_px[1]/30)+($average_px[2]/30));
        $average_px[7]=round(($average_px[4]/30)+($average_px[5]/30)+($average_px[6]/30));
        $average_px[11]=round(($average_px[8]/30)+($average_pxr[9]/30)+($average_px[10]/30));
        $average_px[15]=round(($average_px[12]/30)+($average_px[13]/30)+($average_px[14]/30));               
      }
      $col=2;
      for($i=0;$i<16;$i++)
      {
        if($average_px[$i])
        { $worksheet->write($row, $col, $average_px[$cnt]."/30 : ".round($avergae_px[$i]/30), $number_format); } 
        else
        { $worksheet->write($row, $col, "0", $number_format); } 
        $col++;
      }
      $row++;
      $col=0;
      $worksheet->write($row, $col, 'Total ERPx Not Served :', $category_format);
      $col=2;
      for($i=0;$i<16;$i++)
      { $worksheet->write($row, $col, $total_er_wo[$i], $number_format); $col++;  }
      $row++;
      $col=0;
      $worksheet->write($row, $col, 'Total ERPx Served :', $category_format);
      $col=2;
      for($i=0;$i<16;$i++)
      { $worksheet->write($row, $col, $total_er_w[$i], $number_format); $col++;  }
      $row++;
      $col=0;
      $worksheet->write($row, $col, 'Total OPDPx Not Served :', $category_format);
      $col=2;
      for($i=0;$i<16;$i++)
      { $worksheet->write($row, $col, $total_opd_wo[$i], $number_format); $col++;  }
      $row++;
      $col=0;
      $worksheet->write($row, $col, 'Total OPDPx Served :', $category_format);
      $col=2;
      for($i=0;$i<16;$i++)
      { $worksheet->write($row, $col, $total_opd_w[$i], $number_format); $col++;  }
      $row++;
      $col=0;
      $worksheet->write($row, $col, 'Total IPDPx Not Served :', $category_format);
      $col=2;
      for($i=0;$i<16;$i++)
      { $worksheet->write($row, $col, $total_ipd_wo[$i], $number_format); $col++;  }
      $row++;
      $col=0;
      $worksheet->write($row, $col, 'Total IPDPx Served :', $category_format);
      $col=2;
      for($i=0;$i<16;$i++)
      { $worksheet->write($row, $col, $total_ipd_w[$i], $number_format); $col++;  }
      $row++;
      $col=0;
      $worksheet->write($row, $col, 'Total ICPPx Not Served :', $category_format);
      $col=2;
      for($i=0;$i<16;$i++)
      { $worksheet->write($row, $col, $total_in_wo[$i], $number_format); $col++;  }
      $row++;
      $col=0;
      $worksheet->write($row, $col, 'Total ICPPx Served :', $category_format);
      $col=2;
      for($i=0;$i<16;$i++)
      { $worksheet->write($row, $col, $total_in_w[$i], $number_format); $col++;  }
      */
  }
}
else
{
  $worksheet->write($row, 0, "No query results available at this time...");
}

$workbook->close(); 
?>