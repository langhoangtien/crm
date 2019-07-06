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
$this->excel->getActiveSheet()->getPageMargins()->setRight(1.25);
$this->excel->getActiveSheet()->getPageMargins()->setLeft(1.75);
$this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$this->excel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTop(array(6,6));
//activate worksheet number 1
$this->excel->setActiveSheetIndex(0);
//error_reporting(E_ALL);
error_reporting(E_ALL & ~E_NOTICE);
//name the worksheet
$this->excel->getActiveSheet()->setTitle('Báo cáo tổng hợp tồn kho');

$this->excel->setActiveSheetIndex(0)->mergeCells('A1:E1');

$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);

$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
$this->excel->getActiveSheet()->setCellValue('A1',$data['company']);
$this->excel->getActiveSheet()->setCellValue('A2',$data['address']);

$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);


    $this->excel->setActiveSheetIndex(0)->mergeCells('B4:E4');

    $this->excel->getActiveSheet()->setCellValue('B4', "BÁO CÁO TỔNG HỢP TỒN KHO");

    $this->excel->getActiveSheet()->getStyle('B4')->getFont()->setSize(14)->setBold(true);

    $this->excel->getActiveSheet()->getStyle('B4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

    $this->excel->getActiveSheet()->getStyle('B4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    

$this->excel->getActiveSheet()->setCellValue('A6', "Mã hàng");

$this->excel->getActiveSheet()->setCellValue('B6', "Tên mặt hàng ");

$this->excel->getActiveSheet()->setCellValue('C6', "Giá gốc");

$this->excel->getActiveSheet()->setCellValue('D6', "Đơn giá");

$this->excel->getActiveSheet()->setCellValue('E6', "Số lượng ");

$this->excel->getActiveSheet()->setCellValue('F6', "Mức đặt hàng");
$this->excel->getActiveSheet()->setCellValue('G6', "Mô tả ");


$this->excel->getActiveSheet()->getStyle('A6:G6')->getFont()->setSize(12)->setBold(true);



$k =7;
 foreach ($report_data as $key => $r) {
	  $this->excel->getActiveSheet()->setCellValue('A' . $k, $r['item_number']);
	  //$this->excel->getActiveSheet()->setCellValue('B' . $k, $r['company_name']);
	  $this->excel->getActiveSheet()->setCellValue('B' . $k, $r['name']);
	  $this->excel->getActiveSheet()->setCellValue('C' . $k, $r['reorder_level']);
	  $this->excel->getActiveSheet()->setCellValue('D' . $k,  $r['cost_price']);
	  $this->excel->getActiveSheet()->setCellValue('E' . $k,  $r['unit_price']);
	  $this->excel->getActiveSheet()->setCellValue('F' . $k,  $r['quantity']);
	   $this->excel->getActiveSheet()->setCellValue('G' . $k,  $r['description']);
	  $k++;
 }
 $this->excel->getActiveSheet()->getRowDimension(1)->setRowHeight(21.75);
 $this->excel->getActiveSheet()->getRowDimension(6)->setRowHeight(30);
 
$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(18);
$this->excel->getActiveSheet()->getColumnDimension('G')->setAutoSize(10);

$this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->getActiveSheet()->getStyle('B6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->getActiveSheet()->getStyle('C6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->getActiveSheet()->getStyle('D6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->getActiveSheet()->getStyle('E6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->getActiveSheet()->getStyle('F6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->getActiveSheet()->getStyle('G6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


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

$this->excel->getActiveSheet()->getStyle('A6:G' . ($k-1))->applyFromArray($styleThinBlackBorderOutline);
$this->excel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L&BInvoice&RPrinted on &D');
$this->excel->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&B' . $this->excel->getProperties()->getTitle() . '&RPage &P of &N');
//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
//if you want to save it as .XLSX Excel 2007 format
$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $md5file = md5(date('YmdHis')) . '.xlsx';
//$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
//force user to download the Excel file without writing it to server's HD
$filename = 'Báo cáo tổng tồn kho.xlsx'; //save our workbook as this file name
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