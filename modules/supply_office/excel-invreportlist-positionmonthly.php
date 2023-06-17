<?php
require('./roots.php');
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);

include_once($root_path.'include/care_api_classes/class_department.php');
include_once($root_path.'include/care_api_classes/inventory/class_inventory.php');
include_once($root_path.'include/care_api_classes/inventory/class_issuance.php');
include_once($root_path.'include/care_api_classes/inventory/class_unit.php');
include_once($root_path.'include/care_api_classes/class_pharma_product.php');
include_once($root_path."include/care_api_classes/class_hospital_admin.php");
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/themes/dmc/dmc.php');    
require($root_path."/classes/excel/Writer.php");  

 
// Creating a workbook
$workbook = new Spreadsheet_Excel_Writer();

// sending HTTP headers
$workbook->send('inventory_data.xls');

// Creating a worksheet
$worksheet =& $workbook->addWorksheet();
if($date!='')
{
  $worksheet->setHeader("PEEDMO INVENTORY REPORT\nFor the month of ".date("F Y",strtotime($date)),0.5);
}
else
{
  $worksheet->setHeader("PEEDMO INVENTORY REPORT\nAll Dates",0.5);
}

//format of text
$worksheet->setLandscape();
$worksheet->setPaper(8);        //8 is A3 size
$worksheet->setMargins(1.25);

$format_rotation=& $workbook->addFormat();
$format_rotation->setTextRotation(270);

$format_bold=& $workbook->addFormat();
$format_bold->setBold();

$format_top=& $workbook->addFormat();
$format_top->setBold();
$format_top->setSize(8);
$format_top->setAlign('center');
$format_top->setTextWrap(1);

$format_fsize=& $workbook->addFormat();
$format_fsize->setSize(8);

$format_header1=& $workbook->addFormat();
$format_header1->setBold();
$format_header1->setAlign('center');
$format_header1->setSize(8);

$format_header2=& $workbook->addFormat();
$format_header2->setAlign('center');
$format_header2->setSize(8);

$format_header3=& $workbook->addFormat();
$format_header3->setAlign('center');
$format_header3->setSize(8);
$format_header3->setTextRotation(270);

// The actual data

 $Header=array(
  'ITEM DESCRIPTION',
  'UNIT OF MEASURE',
  'HOSPITAL',
  'BEGINNING BALANCE',
  'IN',
  'OUT',
  'ENDING BALANCE'
  );


#Header
$worksheet->setColumn(0, 0, 5);
$worksheet->write(0, 0, '');
$worksheet->setColumn(0, 1, 50);
$worksheet->write(0, 1,  $Header[0], $format_top);
$worksheet->setColumn(0, 2, 10);
$worksheet->write(0, 2,  $Header[1], $format_top);
$worksheet->setColumn(0, 3, 30);
$worksheet->write(0, 3,  $Header[2], $format_top);
$worksheet->setColumn(0, 4, 10);
$worksheet->write(0, 4,  $Header[3], $format_top);
$worksheet->write(0, 5,  $Header[4], $format_top);
$worksheet->write(0, 6, $Header[5], $format_top);
$worksheet->write(0, 7, $Header[6], $format_top);  


#fetch data
        /*$count = 1;         
        $newrow=2;
                
        $inv_obj = new Inventory();
        $prod_obj = new SegPharmaProduct();
        $unit_obj = new Unit();
        $iss_obj = new Issuance();
        
        $resultItems = $inv_obj->getItemsinArea( $area);
        
        if($resultItems){
            while($row = $resultItems->FetchRow()){
            $newcol=0;
                if($row['item_code'] != ''){
                    $prodinfo = $prod_obj->getProductInfo($row['item_code']);
                    $prodextend = $prod_obj->getExtendedProductInfo($row['item_code']);
                    
                    $smallunit = $unit_obj->getUnitName($prodextend['pc_unit_id']);
                    
                    $isscounter = $iss_obj->countAllIssuancesThisMonth($date, $row['item_code'],  $area);
                    $delisscounter = $iss_obj->countAllIncomingIssuancesThisMonth($date, $row['item_code'],  $area);
                    $delisscounter = $iss_obj->countAllIncomingDeliveriesThisMonth($date, $row['item_code'],  $area);
                    
                    $balcounter = $inv_obj->getInventoryAtHandbyDate($row['item_code'], $area,$date);
                    
                    $endbalcounter = $balcounter + $delisscounter - $isscounter;
                    
                    $Data[]=array(
                        $count,
                        $prodinfo['artikelname'],
                        $smallunit,
                        ($balcounter > 0 ? $balcounter : '' ), 
                        ($balcounter > 0 ? $prodextend['avg_cost'] : '' ), 
                        ($balcounter > 0 ? ($prodextend['avg_cost'] * $balcounter) : '' ), 
                        ($delisscounter > 0 ? $delisscounter : '' ), 
                        ($delisscounter > 0 ? $prodextend['avg_cost'] : '' ), 
                        ($delisscounter > 0 ? ($prodextend['avg_cost'] * $delisscounter) : '' ), 
                        ($isscounter > 0 ? $isscounter : '' ), 
                        ($isscounter > 0 ? $prodextend['avg_cost'] : '' ), 
                        ($isscounter > 0 ? ($prodextend['avg_cost'] * $isscounter) : '' ), 
                        ($endbalcounter > 0 ? $endbalcounter : '' ), 
                        ($endbalcounter > 0 ? $prodextend['avg_cost'] : '' ), 
                        ($endbalcounter > 0 ? ($prodextend['avg_cost'] * $endbalcounter) : '' )
                    );

                    #data for the table
                   // $worksheet->setColumn($newrow, $newcol, 5);
                    $worksheet->write($newrow, $newcol, $count, $format_fsize);
                    $worksheet->write($newrow, $newcol+1, $prodinfo['artikelname'], $format_fsize);
                    $worksheet->write($newrow, $newcol+2, $smallunit, $format_fsize);
                    $worksheet->write($newrow, $newcol+3, ($balcounter > 0 ? $balcounter : '' ), $format_fsize);
                    $worksheet->write($newrow, $newcol+4, ($balcounter > 0 ? $prodextend['avg_cost'] : '' ), $format_fsize);
                    $worksheet->write($newrow, $newcol+5, ($balcounter > 0 ? ($prodextend['avg_cost'] * $balcounter) : '' ), $format_fsize);
                    $worksheet->write($newrow, $newcol+6, ($delisscounter > 0 ? $delisscounter : '' ), $format_fsize);
                    $worksheet->write($newrow, $newcol+7, ($delisscounter > 0 ? $prodextend['avg_cost'] : '' ), $format_fsize);
                    $worksheet->write($newrow, $newcol+8, ($delisscounter > 0 ? ($prodextend['avg_cost'] * $delisscounter) : '' ), $format_fsize);
                    $worksheet->write($newrow, $newcol+9, ($isscounter > 0 ? $isscounter : '' ), $format_fsize);
                    $worksheet->write($newrow, $newcol+10, ($isscounter > 0 ? $prodextend['avg_cost'] : '' ), $format_fsize);
                    $worksheet->write($newrow, $newcol+11, ($isscounter > 0 ? ($prodextend['avg_cost'] * $isscounter) : '' ), $format_fsize);
                    $worksheet->write($newrow, $newcol+12, ($endbalcounter > 0 ? $endbalcounter : '' ), $format_fsize);
                    $worksheet->write($newrow, $newcol+13, ($endbalcounter > 0 ? $prodextend['avg_cost'] : '' ), $format_fsize);
                    $worksheet->write($newrow, $newcol+14, ($endbalcounter > 0 ? ($prodextend['avg_cost'] * $endbalcounter) : '' ), $format_fsize);
                    $newrow++;

                    $total+=$row['amount_due'];  
                    $count++;  
                }
                
            }
        }  */
        
         global $db;
        $count = 1;   
        $where0 = array();
        
        $start_date='2009-06-20';
        $end_date='2009-06-31';
        
        if($hospitals){
            if (!is_array($hospitals)) $hospitals = array($hospitals);
            $hospital_ids = $hospitals;
        }
        else{
            $hospital_ids = array('BPHMY');
        }
         $newrow=1;
        foreach($hospital_ids as $hosp){
                   
            $sql0 = "select DISTINCT item_name from seg_consolidated_inventory ";
            
            if($end_date){
                $where0[]="cutoff_date <= '$end_date'";
            }
            
            if ($hospital_ids) {
                    $where0[]="hosp_id = '$hosp'";
            }
            
            if ($where)
                $sql0 .= "WHERE (".implode(") AND (",$where0).") group by hosp_id,item_name";
            else $sql0 .= "group by hosp_id,item_name";
            
            //echo $sql;
            $result0 = $db->Execute($sql0);
            if($result0){
                while($rowInv0=$result0->FetchRow()){
                    $newcol=0;
                    
                    $sql = "select hosp_id,item_name, cutoff_date, unit_name, SUM(inqty) as totin, SUM(outqty) as totout FROM seg_consolidated_inventory " ;  
        
                    $where = array();
                    
                    $where[] = "item_name = '".$rowInv0['item_name']."'";
                    
                    if($start_date){
                        $where[]="cutoff_date >= '$start_date'";
                    }
                    
                    if($end_date){
                        $where[]="cutoff_date <= '$end_date'";
                    }
                    
                    if ($hospital_ids) {
                            $where[]="hosp_id = '$hosp'";
                    }
                    
                    if ($where)
                        $sql .= "WHERE (".implode(") AND (",$where).") group by hosp_id,item_name ";
                    else $sql .= "group by hosp_id,item_name ";
                    
                    $result = $db->Execute($sql);
                    
                    if($result){
                        while($rowInv=$result->FetchRow()){
                            $sql2 = "select (SUM(inqty) - SUM(outqty)) as beginning FROM seg_consolidated_inventory
                                WHERE cutoff_date < '$start_date' AND hosp_id = '".$rowInv['hosp_id']."' AND item_name = '".$rowInv['item_name']."'
                                GROUP BY item_name";
                            //echo $sql2;       
                            $result2 = $db->Execute($sql2);
                            if($result2)
                                $row2 = $result2->FetchRow();
                            
                            $ending = ($row2['beginning'] + $rowInv['totin'] - $rowInv['totout']);
                            
                            $sql3 = "select hosp_name FROM seg_hospital_info
                                        WHERE hosp_id = '".$rowInv['hosp_id']."'";
                          
                            $result3 = $db->Execute($sql3);
                            if($result3)
                                $row3 = $result3->FetchRow();
                                
                            if(!$rowInv['unit_name']) $rowInv['unit_name'] = "piece(s)";
                            
                            $Data[]=array(
                                $count,
                                $rowInv['item_name'],
                                $rowInv['unit_name'],
                                $row3['hosp_name'],
                                ($row2['beginning'] ? $row2['beginning'] : '' ), 
                                ($rowInv['totin'] > 0 ? $rowInv['totin'] : '' ),  
                                ($rowInv['totout'] > 0 ? $rowInv['totout'] : '' ), 
                                ($ending > 0 ? $ending : '' )
                            );
                            
                    $worksheet->write($newrow, $newcol, $count, $format_fsize);
                    $worksheet->write($newrow, $newcol+1, $rowInv['item_name'], $format_fsize);
                    $worksheet->write($newrow, $newcol+2, $rowInv['unit_name'], $format_fsize);
                    $worksheet->write($newrow, $newcol+3, $row3['hosp_name'], $format_fsize);
                    $worksheet->write($newrow, $newcol+4, ($row2['beginning'] ? $row2['beginning'] : '' ), $format_fsize);
                    $worksheet->write($newrow, $newcol+5, ($rowInv['totin'] > 0 ? $rowInv['totin'] : '' ), $format_fsize);
                    $worksheet->write($newrow, $newcol+6, ($rowInv['totout'] > 0 ? $rowInv['totout'] : '' ), $format_fsize);
                    $worksheet->write($newrow, $newcol+7, ($ending > 0 ? $ending : '' ), $format_fsize); 
                    $newrow++;
                    
                            $count++; 
                        }
                    }
                      
                }
            } 
        }
        
//footer
/*$newrow+=3;
$worksheet->write($newrow, 3, 'PREPARED BY:', $format_header1);
$worksheet->write($newrow, 9, 'APPROVED BY:', $format_header1);
$newrow+=2;
$worksheet->write($newrow, 3, '_____________________', $format_header1);
$worksheet->write($newrow, 9, '_____________________', $format_header1);
$newrow+=1;
$worksheet->write($newrow, 3, 'PIHP COORDINATOR', $format_header1);
$worksheet->write($newrow, 9, 'MEDICAL OFFICER III', $format_header1);   */
                                                                  
$worksheet->setFooter('Generated: '.date("Y-m-d h:i:sa"));

// Let's send the file
$workbook->close();  

?>
