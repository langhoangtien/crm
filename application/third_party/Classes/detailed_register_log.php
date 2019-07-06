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
$this->excel->getActiveSheet()->getPageMargins()->setLeft(0.25);
$this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$this->excel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTop(array(7,7));
//activate worksheet number 1
$this->excel->setActiveSheetIndex(0);
//error_reporting(E_ALL);
error_reporting(E_ALL & ~E_NOTICE);
//name the worksheet
$this->excel->getActiveSheet()->setTitle('đăng ký ghi lại bản ghi');
$this->excel->setActiveSheetIndex(0)->mergeCells('A1:E1');
$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
$this->excel->getActiveSheet()->setCellValue('A1',$data['company']);
$this->excel->getActiveSheet()->setCellValue('A2',$data['address']);
$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
//$this->excel->getActiveSheet()->setCellValue('A3',$data['full_name']);
    $this->excel->setActiveSheetIndex(0)->mergeCells('B4:I4');
    $this->excel->getActiveSheet()->setCellValue('B4', "ĐĂNG KÝ GHI LẠI BẢN GHI");
    $this->excel->getActiveSheet()->getStyle('B4')->getFont()->setSize(14)->setBold(true);
    $this->excel->getActiveSheet()->getStyle('B4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

    $this->excel->getActiveSheet()->getStyle('B4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $this->excel->getActiveSheet()->setCellValue('A6', "Tên khách hàng:");
    $this->excel->getActiveSheet()->setCellValue('B6',$data['full_name']);
    $this->excel->setActiveSheetIndex(0)->mergeCells('B6:C6');
    $this->excel->getActiveSheet()->getStyle('B6')->getFont()->setSize(10)->setBold(true);
$this->excel->getActiveSheet()->setCellValue('A7', "Nhân viên ");
$this->excel->getActiveSheet()->setCellValue('B7', "Bắt đầu chuyển ");
$this->excel->getActiveSheet()->setCellValue('C7', "Kết thúc chuyển");
$this->excel->getActiveSheet()->setCellValue('D7', "Mở ");
$this->excel->getActiveSheet()->setCellValue('E7', "Đóng");
$this->excel->getActiveSheet()->setCellValue('F7', "Tiến bán hàng");
$this->excel->getActiveSheet()->setCellValue('G7', "Sự khác biệt");

$this->excel->getActiveSheet()->getStyle('A7:G7')->getFont()->setSize(12)->setBold(true);
$k =8;
 foreach ($report_data['summary'] as $r) {
	  $this->excel->getActiveSheet()->setCellValue('A' . $k, $r['first_name']);
	  $this->excel->getActiveSheet()->setCellValue('B' . $k, $r['shift_start']);
	  $this->excel->getActiveSheet()->setCellValue('C' . $k, $r['shift_end']);
	  $this->excel->getActiveSheet()->setCellValue('D' . $k, $r['open_amount']);
	  $this->excel->getActiveSheet()->setCellValue('E' . $k, to_currency( $r['close_amount']));
	  $this->excel->getActiveSheet()->setCellValue('F' . $k,to_currency( $r['cash_sales_amount']));
	  $this->excel->getActiveSheet()->setCellValue('G' . $k, to_currency($r['difference']));

	//  $this->excel->getActiveSheet()->setCellValue('I' . $k,$r['payment_type']);
      $this->excel->getActiveSheet()->getRowDimension($k)->setRowHeight(15.75);
	  $k++;
 }
$this->excel->getActiveSheet()->getRowDimension(1)->setRowHeight(21.75);
$this->excel->getActiveSheet()->getRowDimension(7)->setRowHeight(30);
$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(8);
$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(18);
$this->excel->getActiveSheet()->getColumnDimension('G')->setAutoSize(10);

$this->excel->getActiveSheet()->getStyle('A7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('C7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('D7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('E7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('F7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('G7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $this->excel->getActiveSheet()->getStyle('A7')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('B7')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('C7')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('D7')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('E7')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('F7')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('G7')->getAlignment()->setWrapText(true);
  

$styleThinBlackBorderOutline = array(
    'borders' => array(

        'allborders' => array(

            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
            ),

        ),
    );
/* */
$this->excel->getActiveSheet()->getStyle('A7:G' . ($k-1))->applyFromArray($styleThinBlackBorderOutline);

//$this->excel->getActiveSheet()->getHeaderFooter()->setOddFooter('&RPage &P of &N');
$this->excel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L&BInvoice&RPrinted on &D');
$this->excel->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&B' . $this->excel->getProperties()->getTitle() . '&RPage &P of &N');

$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
       $md5file = md5(date('YmdHis')) . '.xlsx';
$filename = 'Đăng ký ghi lại bản ghi.xlsx'; //save our workbook as this file name
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