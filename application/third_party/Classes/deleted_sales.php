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
$this->excel->getActiveSheet()->getPageMargins()->setRight(0.25);
$this->excel->getActiveSheet()->getPageMargins()->setLeft(1);
$this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$this->excel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTop(array(6,6));
//activate worksheet number 1
$this->excel->setActiveSheetIndex(0);
//error_reporting(E_ALL);
error_reporting(E_ALL & ~E_NOTICE);
//name the worksheet
$this->excel->getActiveSheet()->setTitle('Báo cáo chi tiết đơn hàng xóa');
$this->excel->setActiveSheetIndex(0)->mergeCells('A1:E1');
$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
$this->excel->getActiveSheet()->setCellValue('A1',$data['company']);
$this->excel->getActiveSheet()->setCellValue('A2',$data['address']);
$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

    $this->excel->setActiveSheetIndex(0)->mergeCells('D4:H4');
    $this->excel->getActiveSheet()->setCellValue('D4', "BÁO CÁO CHI TIẾT ĐƠN HÀNG XÓA");
    $this->excel->getActiveSheet()->getStyle('D4')->getFont()->setSize(14)->setBold(true);
    $this->excel->getActiveSheet()->getStyle('D4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $this->excel->getActiveSheet()->getStyle('D4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->getActiveSheet()->setCellValue('A6', "ID đơn hàng");
$this->excel->getActiveSheet()->setCellValue('B6', "Ngày ");
$this->excel->getActiveSheet()->setCellValue('C6', "Bán bởi ");	
$this->excel->getActiveSheet()->setCellValue('D6', "Tên khách hàng ");
$this->excel->getActiveSheet()->setCellValue('E6', "SL bán ");
$this->excel->getActiveSheet()->setCellValue('F6', "Tổng từng phần ");
$this->excel->getActiveSheet()->setCellValue('G6', "Tổng cộng ");
$this->excel->getActiveSheet()->setCellValue('H6', "Thuế ");
$this->excel->getActiveSheet()->setCellValue('I6', "Lợi nhuận ");
$this->excel->getActiveSheet()->setCellValue('J6', "Hình thức thanh toán ");
$this->excel->getActiveSheet()->setCellValue('K6', "Ghi chú");
	
	
$k =7;
$tongtungphan=0;
  $loinhuan=0;
  $tongcong=0;
  $thue=0;
  //foreach ($report_data as $key => $r) {
  foreach($report_data['summary'] as $key=>$r){
	  $this->excel->getActiveSheet()->setCellValue('A' . $k, $r['sale_id']);
	  $this->excel->getActiveSheet()->setCellValue('B' . $k, $r['sale_time']);
	  $this->excel->getActiveSheet()->setCellValue('C' . $k, $r['employee_name']);
	  $this->excel->getActiveSheet()->setCellValue('D' . $k, $r['customer_name']);
	  $this->excel->getActiveSheet()->setCellValue('E' . $k, $r['items_purchased']);
	  $this->excel->getActiveSheet()->setCellValue('F' . $k, to_currency_unVND_nomar($r['subtotal']));
	  $this->excel->getActiveSheet()->getStyle('F'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	  $this->excel->getActiveSheet()->setCellValue('G' . $k, to_currency_unVND_nomar($r['total']));
	  $this->excel->getActiveSheet()->getStyle('G'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	  $this->excel->getActiveSheet()->setCellValue('H' . $k, to_currency_unVND_nomar($r['tax']));
	  $this->excel->getActiveSheet()->getStyle('H'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	  $this->excel->getActiveSheet()->setCellValue('I' . $k, to_currency_unVND_nomar($r['profit']));
	  $this->excel->getActiveSheet()->getStyle('I'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	  
	 // $this->excel->getActiveSheet()->setCellValue('J' . $k, $r['payment_type']);
	  $this->excel->getActiveSheet()->setCellValue('J' . $k, str_replace("<br />"," ",$r['payment_type']));
	  $this->excel->getActiveSheet()->getStyle('J'. $k)->getAlignment()->setWrapText(true);
	  $this->excel->getActiveSheet()->setCellValue('K' . $k, $r['comment']);
	  
	  $tongtungphan+= $r['subtotal'];
      $loinhuan+=$r['profit'];
      $tongcong+= $r['total'];
      $thue+= $r['tax'];
	  
	  $k++;
 }

 $this->excel->getActiveSheet()->getStyle('A6:K6')->getFont()->setSize(12)->setBold(true);
 $this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('C6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('D6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('E6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('F6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('G6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('H6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('I6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('J6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('K6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(20);



    $this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('B6')->getAlignment()->setWrapText(true);
	$this->excel->getActiveSheet()->getStyle('C6')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('D6')->getAlignment()->setWrapText(true);
	$this->excel->getActiveSheet()->getStyle('E6')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('F6')->getAlignment()->setWrapText(true);
	$this->excel->getActiveSheet()->getStyle('G6')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('H6')->getAlignment()->setWrapText(true);
	 $this->excel->getActiveSheet()->getStyle('I6')->getAlignment()->setWrapText(true);
	$this->excel->getActiveSheet()->getStyle('J6')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('K6')->getAlignment()->setWrapText(true);
 

$styleThinBlackBorderOutline = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
            ),
        ),
    );
/* */
$this->excel->getActiveSheet()->getStyle('A6:K' . ($k-1))->applyFromArray($styleThinBlackBorderOutline);
$this->excel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L&BInvoice&RPrinted on &D');
$this->excel->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&B' . $this->excel->getProperties()->getTitle() . '&RPage &P of &N');
//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
//if you want to save it as .XLSX Excel 2007 format
$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $md5file = md5(date('YmdHis')) . '.xlsx';
//$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
//force user to download the Excel file without writing it to server's HD
$filename = 'Báo cáo chi tiết đơn hàng xóa.xlsx'; //save our workbook as this file name
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