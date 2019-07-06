<?php

$count = count($report_data['summary'])+count($report_data['details'])+count($headers)*(count($report_data['summary'])+count($report_data['details']));
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
$this->excel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTop(array(7,7));
//activate worksheet number 1
$this->excel->setActiveSheetIndex(0);
//error_reporting(E_ALL);
error_reporting(E_ALL & ~E_NOTICE);
//name the worksheet
$this->excel->getActiveSheet()->setTitle('Báo cáo chi tiết nhà cung cấp');
$this->excel->setActiveSheetIndex(0)->mergeCells('A1:E1');
$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->setCellValue('A1',$data['company']);
$this->excel->getActiveSheet()->setCellValue('A2',$data['address']);
    $this->excel->setActiveSheetIndex(0)->mergeCells('B4:I4');
    $this->excel->getActiveSheet()->setCellValue('B4', "BÁO CÁO CHI TIẾT NHÀ CUNG CẤP");
    $this->excel->getActiveSheet()->getStyle('B4')->getFont()->setSize(14)->setBold(true);
    $this->excel->getActiveSheet()->getStyle('B4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $this->excel->getActiveSheet()->getStyle('B4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$this->excel->getActiveSheet()->setCellValue('A5', "Tên nhà cung cấp");
$this->excel->getActiveSheet()->setCellValue('B5',$data['company_name']);

$this->excel->getActiveSheet()->getStyle('B5')->getFont()->setSize(12)->setBold(true);
$this->excel->getActiveSheet()->setCellValue('A7', "Mã ĐH ");
$this->excel->getActiveSheet()->setCellValue('B7', "Ngày nhập ");

$this->excel->getActiveSheet()->setCellValue('C7', "Nhà cung cấp/ \n Tên mặt hàng");
$this->excel->getActiveSheet()->setCellValue('D7', "Giao nhận bởi/ \n Loại");
$this->excel->getActiveSheet()->setCellValue('E7', "ĐVT");
$this->excel->getActiveSheet()->setCellValue('F7', "SL nhập");
$this->excel->getActiveSheet()->setCellValue('G7', "Tổng cộng ");
$this->excel->getActiveSheet()->setCellValue('H7', "Chiết khấu (%)");
$this->excel->getActiveSheet()->setCellValue('I7', "Tổng cộng sau CK");
$this->excel->getActiveSheet()->setCellValue('J7', "Ghi chú");
//$this->excel->getActiveSheet()->setCellValue('J7', "Ghi chú");
$this->excel->getActiveSheet()->getStyle('A7:J7')->getFont()->setSize(12)->setBold(true);
$this->excel->getActiveSheet()->getStyle('A7:J7')->getAlignment()->setWrapText(true);
$k =8;
$total_money_suppliers=0;
$total_money_suppliers_payment=0;
foreach ($report_data['summary'] as $key=>$r) {
	  $this->excel->getActiveSheet()->setCellValue('A' . $k, $r['receiving_id']);
	  $this->excel->getActiveSheet()->setCellValue('B' . $k, $r['receiving_date']);
	  $this->excel->getActiveSheet()->setCellValue('C' . $k, $r['supplier_name']);
	  $this->excel->getActiveSheet()->setCellValue('D' . $k, $r['employee_name']);
          $this->excel->getActiveSheet()->setCellValue('E' . $k, '');
	  $this->excel->getActiveSheet()->setCellValue('F' . $k, $r['items_purchased']);
	  
	  $this->excel->getActiveSheet()->setCellValue('G' . $k, number_format($r['subtotal']));
	  $this->excel->getActiveSheet()->setCellValue('H' . $k, $r['discount_percent']);
	  $this->excel->getActiveSheet()->setCellValue('I' . $k, number_format($r['total']));

	  $this->excel->getActiveSheet()->setCellValue('J' . $k,$r['comment']);

	  $this->excel->getActiveSheet()->getRowDimension($k)->setRowHeight(18.75);
	  $this->excel->getActiveSheet()->getStyle('A'.$k.':J'.($k))->getFont()->setSize(10)->setBold(true);
	  $this->excel->getActiveSheet()->getStyle('A'.$k.':J'.($k))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('E9E9E9');
	  foreach($report_data['details'][$key] as $dr)
	  {
	  	$i=$k+1;
	  	  $this->excel->getActiveSheet()->setCellValue('A' . $i, '');
	  	  $this->excel->getActiveSheet()->setCellValue('B' . $i, '');
	  	  $this->excel->getActiveSheet()->setCellValue('C' . $i, $dr['name']);
		  $this->excel->getActiveSheet()->setCellValue('D' . $i, $dr['category']);
		  $this->excel->getActiveSheet()->setCellValue('E' . $i, $this->Unit->get_info($dr['unit'])->name);
                  $this->excel->getActiveSheet()->setCellValue('F' . $i, $dr['quantity_purchased']);
		  $this->excel->getActiveSheet()->setCellValue('G' . $i, number_format($dr['subtotal']));
		  $this->excel->getActiveSheet()->setCellValue('H' . $i, $dr['discount_percent']);
		  $this->excel->getActiveSheet()->setCellValue('I' . $i, number_format($dr['total']));
		  $this->excel->getActiveSheet()->setCellValue('J' . $i, $dr['description']);
		  $this->excel->getActiveSheet()->getStyle('J' . $i)->getAlignment()->setWrapText(true);
		
		  $this->excel->getActiveSheet()->getRowDimension($k)->setRowHeight(17.75);
		  $k++;
                  
                  
	  }
	  $this->excel->getActiveSheet()->getRowDimension($k)->setRowHeight(17.75);
	  $k++;
          
          $total_money_suppliers += $r['subtotal'];
          $total_money_suppliers_payment += $r['total'];
 }

  $i = $this->excel->getActiveSheet()->getHighestRow() ;
  $this->excel->setActiveSheetIndex(0)->mergeCells('A' . ($i+2).':J'.($i+2));
  $this->excel->getActiveSheet()->setCellValue('A' . ($i+2), 'TỔNG TIỀN HÀNG :'.number_format($total_money_suppliers));
  $this->excel->getActiveSheet()->getStyle('A'.($i+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
 
    $i = $this->excel->getActiveSheet()->getHighestRow() ;
  $this->excel->setActiveSheetIndex(0)->mergeCells('A' . ($i+1).':J'.($i+1));
  $this->excel->getActiveSheet()->setCellValue('A' . ($i+1), 'TỔNG TIỀN THANH TOÁN :'.number_format($total_money_suppliers_payment));
  $this->excel->getActiveSheet()->getStyle('A'.($i+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
  
$this->excel->getActiveSheet()->getRowDimension(1)->setRowHeight(21.75);
$this->excel->getActiveSheet()->getRowDimension(7)->setRowHeight(30);
$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(27);
$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
//$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
$this->excel->getActiveSheet()->getStyle('A7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('C7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('D7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('E7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('F7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('G7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('H7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('I7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('J7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//$this->excel->getActiveSheet()->getStyle('J7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);





    $this->excel->getActiveSheet()->getStyle('A7')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('B7')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('C7')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('D7')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('E7')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('F7')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('G7')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('H7')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('I7')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('J7')->getAlignment()->setWrapText(true);
   // $this->excel->getActiveSheet()->getStyle('J7')->getAlignment()->setWrapText(true);



$styleThinBlackBorderOutline = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
            ),
        ),
    );

$this->excel->getActiveSheet()->getStyle('A7:J' . ($k-1))->applyFromArray($styleThinBlackBorderOutline);
$this->excel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L&BInvoice&RPrinted on &D');
$this->excel->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&B' . $this->excel->getProperties()->getTitle() . '&RPage &P of &N');

//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
//if you want to save it as .XLSX Excel 2007 format
$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $md5file = md5(date('YmdHis')) . '.xlsx';

//$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
//force user to download the Excel file without writing it to server's HD
$filename = 'thongtin_chitiet_nhacungcap.xlsx'; //save our workbook as this file name
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