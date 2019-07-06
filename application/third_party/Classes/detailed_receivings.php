<?php

$count = count($report_data['summary']) + count($report_data['details']) + count($headers) * (count($report_data['summary']) + count($report_data['details']));
$this->load->library('Excel');
$objPHPExcel = new PHPExcel();
$this->excel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
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
$this->excel->getActiveSheet()->setTitle('Báo cáo chi tiết nhập hàng');
$this->excel->setActiveSheetIndex(0)->mergeCells('A1:F1');
$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);    
$this->excel->setActiveSheetIndex(0)->mergeCells('B4:H4');    
$this->excel->getActiveSheet()->setCellValue('A1',$data['company']);    
$this->excel->getActiveSheet()->setCellValue('A2',$data['address']);    
$this->excel->getActiveSheet()->setCellValue('B4', "BÁO CÁO CHI TIẾT NHẬP HÀNG");    
$this->excel->getActiveSheet()->getStyle('B4')->getFont()->setSize(14)->setBold(true);    
$this->excel->getActiveSheet()->getStyle('B4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);    
$this->excel->getActiveSheet()->getStyle('B4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);    
$this->excel->getActiveSheet()->setCellValue('A6', "ID Nhập hàng ");
$this->excel->getActiveSheet()->setCellValue('B6', "Ngày ");
$this->excel->getActiveSheet()->setCellValue('C6', "Số lượng nhập");
$this->excel->getActiveSheet()->setCellValue('D6', "Giao nhận bởi");
$this->excel->getActiveSheet()->setCellValue('E6', "Cung cấp bởi");
$this->excel->getActiveSheet()->setCellValue('F6', "Tổng cộng");
$this->excel->getActiveSheet()->setCellValue('G6', "Hình thức thanh toán");
$this->excel->getActiveSheet()->setCellValue('H6', "Các ghi chú");
$this->excel->getActiveSheet()->getStyle('A6:H6')->getFont()->setSize(12)->setBold(true);

$k =7;
$tongtien = 0;
foreach ($report_data['summary'] as $key=>$r) 
{	//echo $r['sale_id'];die;//	
	//$name_supplier = $this->Supplier->get_name_suppiler(array('person_id'=>$r[3]));	//$name_supplier);die;	  
	$this->excel->getActiveSheet()->setCellValue('A' . $k, $r['receiving_id']);	  
	$this->excel->getActiveSheet()->setCellValue('B' . $k, $r['receiving_date']);	  
	$this->excel->getActiveSheet()->setCellValue('C' . $k, $r['items_purchased']);	  
	$this->excel->getActiveSheet()->setCellValue('D' . $k, $r['employee_name']);	  
	$this->excel->getActiveSheet()->setCellValue('E' . $k, $r['supplier_name']);	  
	$this->excel->getActiveSheet()->setCellValue('F' . $k, number_format($r['total']));	  
	$this->excel->getActiveSheet()->setCellValue('G' . $k, str_replace("<sup>VNĐ</sup><br />","",$r['payment_type']));	 
	//$this->excel->getActiveSheet()->setCellValue('G' . $k, $r['payment_type']);	  
	$this->excel->getActiveSheet()->setCellValue('H' . $k, $r['comment']);    
	$this->excel->getActiveSheet()->getRowDimension($k)->setRowHeight(15.75);	

	$tongtien += $r['total'];
	$k++; 
}
$i = $this->excel->getActiveSheet()->getHighestRow() +1;
//$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i).':E'.($i));
$this->excel->getActiveSheet()->setCellValue('D' . ($i),"TỔNG CỘNG" );
$this->excel->getActiveSheet()->getStyle('D' . ($i))->getFont()->setSize(12)->setBold(true);
  $this->excel->getActiveSheet()->getStyle('D'.($i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
 $this->excel->getActiveSheet()->setCellValue('F' . ($i),to_currency_unVND($tongtien));
  $this->excel->getActiveSheet()->getStyle('F' . ($i))->getFont()->setSize(12)->setBold(true);
  //$this->excel->getActiveSheet()->getRowDimension($i)->setRowHeight(15.75);

  
$this->excel->getActiveSheet()->getRowDimension(1)->setRowHeight(21.75);
$this->excel->getActiveSheet()->getRowDimension(6)->setRowHeight(30);
$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
$this->excel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(18);
$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(18);
$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(18);
$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(16);
$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(0);
$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(33);

$this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('C6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('D6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('E6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('F6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('G6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('H6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
 
$this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setWrapText(true);    
$this->excel->getActiveSheet()->getStyle('B6')->getAlignment()->setWrapText(true);    
$this->excel->getActiveSheet()->getStyle('C6')->getAlignment()->setWrapText(true);    
$this->excel->getActiveSheet()->getStyle('D6')->getAlignment()->setWrapText(true);    
$this->excel->getActiveSheet()->getStyle('E6')->getAlignment()->setWrapText(true);    
$this->excel->getActiveSheet()->getStyle('F6')->getAlignment()->setWrapText(true);    
$this->excel->getActiveSheet()->getStyle('G6')->getAlignment()->setWrapText(true);    
$this->excel->getActiveSheet()->getStyle('H6')->getAlignment()->setWrapText(true);

$styleThinBlackBorderOutline = array(    
			'borders' => array(        
			'allborders' => array(         
			'style' => PHPExcel_Style_Border::BORDER_THIN,            
			'color' => array('argb' => 'FF000000'),            
			),     
		),    
	);/* */
$this->excel->getActiveSheet()->getStyle('A6:H'.($k-1))->applyFromArray($styleThinBlackBorderOutline);

$this->excel->getActiveSheet()->getHeaderFooter()->setOddFooter('&RPage &P of &N');

//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
//if you want to save it as .XLSX Excel 2007 format
$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $md5file = md5(date('YmdHis')) . '.xlsx';
//$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
//force user to download the Excel file without writing it to server's HD
$filename = 'thongtin_nhaphang.xlsx'; //save our workbook as this file name
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