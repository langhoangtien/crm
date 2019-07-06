<?php

$count = count($data);
$this->load->library('Excel');
$objPHPExcel = new PHPExcel();
$this->excel->getDefaultStyle()
        ->getAlignment()
        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->setShowGridlines(true);
$this->excel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Times New Roman');
$this->excel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(9);
$this->excel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
$this->excel->getActiveSheet()->getPageMargins()->setRight(0.5);
$this->excel->getActiveSheet()->getPageMargins()->setLeft(0.5);
$this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$this->excel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTop(array(6, 6));
//activate worksheet number 1
$this->excel->setActiveSheetIndex(0);
//error_reporting(E_ALL);
error_reporting(E_ALL & ~E_NOTICE);
//name the worksheet
$this->excel->getActiveSheet()->setTitle('Báo cáo tổng hợp chiết khấu');
$this->excel->setActiveSheetIndex(0)->mergeCells('A1:E1');
$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
$this->excel->getActiveSheet()->setCellValue('A1', $data['company']);
$this->excel->getActiveSheet()->setCellValue('A2', $data['address']);
$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->setActiveSheetIndex(0)->mergeCells('A4:C4');
$this->excel->getActiveSheet()->setCellValue('A4', "BÁO CÁO TỔNG HỢP CHIẾT KHẤU");
$this->excel->getActiveSheet()->getStyle('A4')->getFont()->setSize(16)->setBold(true);
$this->excel->getActiveSheet()->getStyle('A4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->getActiveSheet()->setCellValue('A6', "Tên mặt hàng ");
$this->excel->getActiveSheet()->setCellValue('B6', "Số lượng");
$this->excel->getActiveSheet()->setCellValue('C6', "Chiết khấu (%)");
$this->excel->getActiveSheet()->getStyle('C6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$k = 7;
$total_discount_precent = 0;
if ($item_type == 0) {
    $discount_get = $this->Sale->get_total_discount($start_date, $end_date, $sale_type);
} elseif ($item_type == 1) {
    $discount_get = $this->Sale->get_total_discount_recv($start_date, $end_date, $sale_type);
} else {
    $d1 = $this->Sale->get_total_discount($start_date, $end_date, $sale_type);
    $d2 = $this->Sale->get_total_discount_recv($start_date, $end_date, $sale_type);
    $discount_get = array_merge($d1, $d2);
}
if ($discount_get != NULL):
    foreach ($discount_get as $key => $discount_get1):
        $this->excel->getActiveSheet()->setCellValue('A' . $k, $discount_get1['name']);
        $this->excel->getActiveSheet()->getStyle('A' . ($k))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->setCellValue('B' . $k, $discount_get1['quantity_purchased'] > 0 ? format_quantity($discount_get1['quantity_purchased']) : format_quantity($discount_get1['quantity_purchased']) . ' (Trả hàng)');
        $this->excel->getActiveSheet()->getStyle('B' . ($k))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $this->excel->getActiveSheet()->setCellValue('C' . $k, number_format($discount_get1['discount_percent']));
        $this->excel->getActiveSheet()->getStyle('C' . ($k))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $k++;
        $total_discount_precent += $discount_get1['discount_percent'];
    endforeach;
endif;

$i = $this->excel->getActiveSheet()->getHighestRow();
$this->excel->setActiveSheetIndex(0)->mergeCells('A' . ($i + 2) . ':C' . ($i + 2));
$this->excel->getActiveSheet()->setCellValue('A' . ($i + 2), 'TỔNG CỘNG :' . number_format($total_discount_precent) . '%');
$this->excel->getActiveSheet()->getStyle('A' . ($i + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$this->excel->getActiveSheet()->getStyle('A6:C6')->getFont()->setSize(16)->setBold(true);
$this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(55);
$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(55);
$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(55);

$this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getStyle('B6')->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getStyle('C6')->getAlignment()->setWrapText(true);

$styleThinBlackBorderOutline = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
        ),
    ),
);
/* */
$this->excel->getActiveSheet()->getStyle('A6:C' . ($k - 1))->applyFromArray($styleThinBlackBorderOutline);



$this->excel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L&BInvoice&RPrinted on &D');
$this->excel->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&B' . $this->excel->getProperties()->getTitle() . '&RPage &P of &N');
//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
//if you want to save it as .XLSX Excel 2007 format
$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
$md5file = md5(date('YmdHis')) . '.xlsx';
//$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
//force user to download the Excel file without writing it to server's HD
$filename = 'Báo cáo tổng hợp chiếu khấu.xlsx'; //save our workbook as this file name
//$filename = 'Book1.xlsx';
$objWriter->save($filename);
if (file_exists($filename)) {
    header('Content-Type: application/vnd.ms-excel'); //mime type
    header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
    header('Cache-Control: max-age=0'); //no cache    
//    header('Content-Description: File Transfer');
//    header('Content-Type: application/octet-stream');
//    header('Content-Disposition: attachment; filename=' . basename($file));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filename));
    ob_clean();
    flush();
    readfile($filename);
    exit;
}
?>