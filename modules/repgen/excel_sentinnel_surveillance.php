<?php
  #require_once 'Spreadsheet/Excel/Writer.php';
  require('./roots.php');
require($root_path."/classes/excel/Writer.php");
  
// Creating a workbook
$workbook = new Spreadsheet_Excel_Writer();

$TableHeader = array('BUKIDNON PROVINCIAL HOSPITAL',
                    'Target Annual Income',
                    'Professional Fees',
                    'Medicare I (GSIS, SSS)',
                    'Medicare II (Masa-PIHP & Masa-Sponsored by other LGUs)',
                    'Paying patients',
                    'Total Cash Income',
                    'Add PHIC Receivable to Date',
                    'Total Income (Cash and Receivable)',
                    'Total Hospital Services w/o Payment',
                    'Total Gross Income');
                    
$TableWidth = array(50,12,12,12,12,12,12,12,12,12,12);

$worksheet =& $workbook->addWorksheet();
$worksheet->setPaper(5);
$worksheet->setLandscape();
$worksheet->setMarginTop(0.5);
$worksheet->setMarginLeft(0.5);
$worksheet->setMarginRight(0.04);
$worksheet->setMarginBottom(0.5);

//text format for header
$format1 =& $workbook->addFormat(array('Size' => 9,
                                        'Align' => 'top',
                                        'Align' => 'center',
                                        'Bold' => 1
                                ));
$format1->setTextWrap(1);

$format2 =& $workbook->addFormat(array('Size' => 9,
                                        'Align' => 'center',
                                        ));
$format2->setTextWrap(1);
                                
// put text at the top
$format_top =& $workbook->addFormat();
$format_top->setAlign('top');
$format_top->setTextWrap(1);

// center the text horizontally
$format_center =& $workbook->addFormat();
$format_center->setAlign('center');

// put text at the top and center it horizontally
$format_top_center =& $workbook->addFormat();
$format_top_center->setAlign('top');
$format_top_center->setAlign('center');
$format_top_center->setTextWrap(1); 

//Table Header
$startrow = 0;
$NumTable = count($TableHeader);

for($cnt = 0; $cnt<$NumTable; $cnt++){
  $worksheet->setColumn($startrow, $cnt, $TableWidth[$cnt]);
  $worksheet->write($startrow, $cnt, $TableHeader[$cnt], $format1);
}

/*$worksheet->setColumn(0,0,50);
$worksheet->write(0, 0, 'PARTICULARS',
                  $format1);
$worksheet->setColumn(0,1,10);
$worksheet->write(0, 1, 'MEDICARE I Total income to date Last Month', $format1);
$worksheet->setColumn(0,2,10);
$worksheet->write(0, 2, 'MEDICARE I Income for this Month', $format1);
$worksheet->setColumn(0,3,10);
$worksheet->write(0, 3, 'MEDICARE I Total Income to Date', $format1);
$worksheet->setColumn(0,4,10);
$worksheet->write(0, 4, 'MEDICARE II Total Income to date Last Month', $format1);
$worksheet->setColumn(0,5,10);
$worksheet->write(0, 5, 'MEDICARE II Income for this Month', $format1);
$worksheet->setColumn(0,6,10);
$worksheet->write(0, 6, 'MEDICARE II Total Income to Date', $format1);
$worksheet->setColumn(0,7,10);
$worksheet->write(0,7, 'MEDICARE III and IV Total income to date Last Month', $format1);
$worksheet->setColumn(0,8,10);
$worksheet->write(0, 8, 'MEDICARE III and IV Income for this Month', $format1);
$worksheet->setColumn(0,9, 10);
$worksheet->write(0, 9, 'MEDICARE III and IV Total Income to Date', $format1);
$worksheet->setColumn(0,10,10);
$worksheet->write(0, 10, 'INCOME from Non-Member Patients Total income to date Last Month', $format1);
$worksheet->setColumn(0,11, 10);
$worksheet->write(0, 11, 'INCOME from Non-Member Patients Income for this Month', $format1);
$worksheet->setColumn(0,12,10);
$worksheet->write(0, 12, 'INCOME from Non-Member Patients Total income to Date', $format1);
$worksheet->setColumn(0,13,10);
$worksheet->write(0, 13, 'TOTAL INCOME TO DATE', $format1);
$worksheet->setColumn(0,14,10);
$worksheet->write(0, 14, 'Unpaid Excess Med care II Total Income to date last Month', $format1);
$worksheet->setColumn(0,15,10);
$worksheet->write(0, 15, 'Unpaid Excess Med care II Income for this Month', $format1);
$worksheet->setColumn(0,16,10);
$worksheet->write(0, 16, 'Unpaid Excess Med care II Total Income to Date', $format1);
$worksheet->setColumn(0,17,10);
$worksheet->write(0, 17, 'PHIC Slashed/Denied Total Income to date last Month', $format1);
$worksheet->setColumn(0,18,10);
$worksheet->write(0, 18, 'PHIC Slashed/Denied Income for this Month', $format1);
$worksheet->setColumn(0,19,10);
$worksheet->write(0, 19, 'PHIC Slashed/Denied Total Income to Date', $format1);
$worksheet->setColumn(0,20,10);
$worksheet->write(0, 20, 'Income from Charity cases Total Income to Date last Month', $format1);
$worksheet->setColumn(0,21,10);
$worksheet->write(0, 21, 'Income from Charity cases Income for this Month', $format1);
$worksheet->setColumn(0,22,10);
$worksheet->write(0, 22, 'Income from Charity cases Total Income to Date', $format1);
*/
$workbook->send('excel_income_breakdown.xls');


// Let's send the file
$workbook->close();
?>
