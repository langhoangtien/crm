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
$this->excel->getActiveSheet()->getPageMargins()->setRight(1.5);
$this->excel->getActiveSheet()->getPageMargins()->setLeft(1.5);
$this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$this->excel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTop(array(6,6));
//activate worksheet number 1
$this->excel->setActiveSheetIndex(0);
//error_reporting(E_ALL);
error_reporting(E_ALL & ~E_NOTICE);
//name the worksheet
$this->excel->getActiveSheet()->setTitle('Báo cáo tồn kho mức thấp');
$this->excel->setActiveSheetIndex(0)->mergeCells('A1:F1');
$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
$this->excel->getActiveSheet()->setCellValue('A1',$data['company']);
$this->excel->getActiveSheet()->setCellValue('A2',$data['address']);
$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $this->excel->setActiveSheetIndex(0)->mergeCells('B4:F4');
    $this->excel->getActiveSheet()->setCellValue('B4', "BÁO CÁO TỒN KHO MỨC THẤP");
    $this->excel->getActiveSheet()->getStyle('B4')->getFont()->setSize(14)->setBold(true);
    $this->excel->getActiveSheet()->getStyle('B4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $this->excel->getActiveSheet()->getStyle('B4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->getActiveSheet()->setCellValue('A6', "Mã mặt hàng");
$this->excel->getActiveSheet()->setCellValue('B6', "Tên mặt hàng");
$this->excel->getActiveSheet()->setCellValue('C6', "Mô tả");	
$this->excel->getActiveSheet()->setCellValue('D6', "Giá gốc ");
$this->excel->getActiveSheet()->setCellValue('E6', "Đơn giá ");
$this->excel->getActiveSheet()->setCellValue('F6', "ĐVT ");
$this->excel->getActiveSheet()->setCellValue('G6', "Số lượng  ");
$this->excel->getActiveSheet()->setCellValue('H6', "Mức đặt hàng");
	
$k =7;
 foreach ($report_data as $key => $r) {
	  $this->excel->getActiveSheet()->setCellValue('A' . $k, $r['item_number']);
	  $this->excel->getActiveSheet()->setCellValue('B' . $k, $r['name']);
	  $this->excel->getActiveSheet()->setCellValue('C' . $k, $r['description']);
	  $this->excel->getActiveSheet()->setCellValue('D' . $k, number_format($r['cost_price']));
	  $this->excel->getActiveSheet()->setCellValue('E' . $k, number_format($r['unit_price']));
          $this->excel->getActiveSheet()->setCellValue('F' . $k, $this->Unit->get_info($r['unit'])->name);
	  $this->excel->getActiveSheet()->setCellValue('G' . $k, number_format($r['quantity']));
	  $this->excel->getActiveSheet()->setCellValue('H' . $k, $r['reorder_level']);
	  
	  $this->excel->getActiveSheet()->getStyle('A' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	  $this->excel->getActiveSheet()->getStyle('B' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	  $this->excel->getActiveSheet()->getStyle('C' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	  $this->excel->getActiveSheet()->getStyle('D' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	  $this->excel->getActiveSheet()->getStyle('E' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	  $this->excel->getActiveSheet()->getStyle('F' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	  $this->excel->getActiveSheet()->getStyle('G' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
          $this->excel->getActiveSheet()->getStyle('G' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	  $k++;
 }

 $this->excel->getActiveSheet()->getStyle('A6:H6')->getFont()->setSize(12)->setBold(true);
 $this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('C6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('D6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('E6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('F6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('G6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('H6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);

    $this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('B6')->getAlignment()->setWrapText(true);
	$this->excel->getActiveSheet()->getStyle('C6')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('D6')->getAlignment()->setWrapText(true);
	$this->excel->getActiveSheet()->getStyle('E6')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('F6')->getAlignment()->setWrapText(true);
	$this->excel->getActiveSheet()->getStyle('G6')->getAlignment()->setWrapText(true);

$styleThinBlackBorderOutline = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
            ),
        ),
    );
/* */
$this->excel->getActiveSheet()->getStyle('A6:H' . ($k-1))->applyFromArray($styleThinBlackBorderOutline);
$this->excel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L&BInvoice&RPrinted on &D');
$this->excel->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&B' . $this->excel->getProperties()->getTitle() . '&RPage &P of &N');
//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
//if you want to save it as .XLSX Excel 2007 format
$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $md5file = md5(date('YmdHis')) . '.xlsx';
//$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
//force user to download the Excel file without writing it to server's HD
$filename = 'Báo cáo chi tiết tồn kho.xlsx'; //save our workbook as this file name
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