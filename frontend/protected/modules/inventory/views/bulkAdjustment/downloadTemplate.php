<?php

/**
 *
 * @var CController $this
 * @var PHPExcel $excel
 */

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="template-'.date('YmdHis').'.xls"');
header('Cache-Control: max-age=0');
$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
$writer->save('php://output');