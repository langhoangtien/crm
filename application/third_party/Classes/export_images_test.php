<?php
$excel = new PHPExcel();
$excel->getDefaultStyle()->getFont()->setName('Arial');
$excel->getDefaultStyle()->getFont()->setSize(10);
$sheet = $excel->getActiveSheet();
$objDrawing = new PHPExcel_Worksheet_Drawing();

$objDrawing->setPath('images/anh.png');
$objDrawing->setCoordinates('A1');
$objDrawing->setWorksheet($sheet);

$writer = new PHPExcel_Writer_Excel5($excel);

$writer->save('c:/write.xls');
 
?>