<?php
//echo "hfjdahdj";die;
//print_r($rows);die;
$count = count($rows);
//echo $count;die;
$this->load->library('Excel');
$objPHPExcel = new PHPExcel();

$this->excel->getActiveSheet()->setShowGridlines(false);
$this->excel->getDefaultStyle()
    ->getAlignment()
    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->setShowGridlines(false);
$this->excel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Times New Roman');
$this->excel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(9);
$this->excel->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
$this->excel->getActiveSheet()->getPageSetup()->setVerticalCentered(false);
$this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$this->excel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTop(array(7,8));
//activate worksheet number 1
$this->excel->setActiveSheetIndex(0);
//error_reporting(E_ALL);
error_reporting(E_ALL & ~E_NOTICE);
//name the worksheet
$this->excel->getActiveSheet()->setTitle('Báo cáo tiền thu chi');
/* ten dia chi cong ty ngay thang noi dung */
$this->excel->setActiveSheetIndex(0)->mergeCells('A1:E1');
$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
$this->excel->getActiveSheet()->setCellValue('A1',$this->config->item('company'));
$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->setActiveSheetIndex(0)->mergeCells('A2:D2');
$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setItalic(true);
$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(9);
$this->excel->getActiveSheet()->setCellValue('A2',$this->config->item('address'));

$this->excel->setActiveSheetIndex(0)->mergeCells('F1:H1');
$this->excel->getActiveSheet()->getStyle('F1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('F1')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('F1')->getFont()->setSize(9);
$this->excel->getActiveSheet()->setCellValue('F1',"Mẫu số S05 - DNN");
$this->excel->setActiveSheetIndex(0)->mergeCells('F2:H2');
$this->excel->getActiveSheet()->getStyle('F2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('F2')->getFont()->setSize(8);
$this->excel->getActiveSheet()->setCellValue('F2',"Ban hành theo QĐ số 48/2006/QĐ - BTC");
$this->excel->setActiveSheetIndex(0)->mergeCells('F3:H3');
$this->excel->getActiveSheet()->getStyle('F3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('F3')->getFont()->setSize(8);
$this->excel->getActiveSheet()->setCellValue('F3',"Ngày 14 tháng 9 năm 2006");
$this->excel->getActiveSheet()->getStyle('F1:H3')->getFont()->setItalic(true);
	$this->excel->setActiveSheetIndex(0)->mergeCells('A4:H4');
	$this->excel->getActiveSheet()->setCellValue('A4', "SỔ TIỀN THU - CHI");
	$this->excel->getActiveSheet()->getStyle('A4')->getFont()->setSize(14)->setBold(true);
	$this->excel->getActiveSheet()->getRowDimension(4)->setRowHeight(30.75);
	$this->excel->getActiveSheet()->getStyle('A4:H4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$this->excel->getActiveSheet()->getStyle('A4:H4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objRichTextdate = new PHPExcel_RichText();
$tu = $objRichTextdate->createTextRun('Từ: ')->getFont()->setSize(9)->setName('Times New Roman');
$startdate = $objRichTextdate->createTextRun(date('d-m-Y H:i:s', strtotime($start_date)))->getFont()->setItalic(true)->setSize(9)->setName('Times New Roman')->setBold(true); 
$den = $objRichTextdate->createTextRun(' đến: ')->getFont()->setSize(9);
$enddate = $objRichTextdate->createTextRun(date('d-m-Y H:i:s', strtotime($end_date)))->getFont()->setItalic(true)->setSize(9)->setName('Times New Roman')->setBold(true); 
$this->excel->setActiveSheetIndex(0)->mergeCells('A5:H5');
$this->excel->getActiveSheet()->getCell('A5')->setValue($objRichTextdate);
$this->excel->getActiveSheet()->getStyle('A5:H5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A5:H5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getRowDimension(5)->setRowHeight(12.75);

 $this->excel->setActiveSheetIndex()->mergeCells('A7:A8');
 $this->excel->getActiveSheet()->setCellValue('A7', "NGÀY GS");
 $this->excel->getActiveSheet()->getStyle('A7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
 $this->excel->getActiveSheet()->getStyle('A7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
 $this->excel->setActiveSheetIndex()->mergeCells('B7:C7');
 $this->excel->getActiveSheet()->setCellValue('B7', "CHỨNG TỪ");
 $this->excel->getActiveSheet()->getStyle('B7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
 $this->excel->getActiveSheet()->getStyle('B7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

 $this->excel->setActiveSheetIndex(0)->mergeCells('D7:D8');
 $this->excel->getActiveSheet()->setCellValue('D7', "TÊN CHI PHÍ");
 $this->excel->getActiveSheet()->getStyle('D7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
 $this->excel->getActiveSheet()->getStyle('D7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

 $this->excel->setActiveSheetIndex(0)->mergeCells('E7:E8');
 $this->excel->getActiveSheet()->setCellValue('E7', "NỘI DUNG");
 $this->excel->getActiveSheet()->getStyle('E7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
 $this->excel->getActiveSheet()->getStyle('E7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

 $this->excel->getActiveSheet()->setCellValue('F7', "TÀI KHOẢN");
 $this->excel->getActiveSheet()->getStyle('F7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
 $this->excel->getActiveSheet()->getStyle('F7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

 $this->excel->setActiveSheetIndex(0)->mergeCells('G7:H7');
 $this->excel->getActiveSheet()->getStyle('G7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
 $this->excel->getActiveSheet()->getStyle('G7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

 $this->excel->getActiveSheet()->setCellValue('G7', "SỐ TIỀN");

 $this->excel->getActiveSheet()->setCellValue('B8', "SỐ");
 $this->excel->getActiveSheet()->setCellValue('C8', "NGÀY");
 $this->excel->getActiveSheet()->setCellValue('F8', "ĐỐI ỨNG");
 
 
 $this->excel->getActiveSheet()->setCellValue('G8', "THU");
 
 
 $this->excel->getActiveSheet()->setCellValue('H8', "CHI");

 $this->excel->getActiveSheet()->getStyle('A8:H8')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
 $this->excel->getActiveSheet()->getStyle('A8:H8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
 $this->excel->getActiveSheet()->getStyle('A7:H8')->getFont()->setBold(true)->setSize(9);
 $styleThinBlackBorderOutline = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
			),
		),
	);
$this->excel->getActiveSheet()->getStyle('A7:H8')->applyFromArray($styleThinBlackBorderOutline);
$this->excel->getActiveSheet()->getRowDimension(7)->setRowHeight(16.50);
/* end tieu de cot */
$styleDOTBlackBorderOutline = array(
    'borders' => array(
		'top' => array(
            'style' => PHPExcel_Style_Border::BORDER_DOTTED,
            'color' => array('argb' => 'FF000000'),
        ),
        'bottom' => array(
            'style' => PHPExcel_Style_Border::BORDER_DOTTED,
            'color' => array('argb' => 'FF000000'),
        ),
    ),
);
$styleDOTBlackBorderTOPBOTOutline = array(
    'borders' => array(
        'top' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
        ),
		'bottom' => array(
            'style' => PHPExcel_Style_Border::BORDER_DOTTED,
            'color' => array('argb' => 'FF000000'),
        ),
    ),
);
$k =9;
foreach ($rows as $key=>$r) {
	  $this->excel->getActiveSheet()->setCellValue('A' . $k, $r[0]);
	  $this->excel->getActiveSheet()->setCellValue('B' . $k, $r[1]);
	  $this->excel->getActiveSheet()->setCellValue('C' . $k, $r[2]);
	  
	  $this->excel->getActiveSheet()->setCellValue('D' . $k,$this->Cost->get_info_name($r[3])->cost_name);
	  $this->excel->getActiveSheet()->setCellValue('E' . $k, $r[4]);
	  $this->excel->getActiveSheet()->setCellValue('F' . $k, $r[5]);
         
	  $this->excel->getActiveSheet()->setCellValue('G' . $k, $r[6]);
        
	  $this->excel->getActiveSheet()->setCellValue('H' . $k, $r[7]);
       
	  $this->excel->getActiveSheet()->getStyle('A'.($k).':H'.$k)->applyFromArray($styleDOTBlackBorderOutline);
	  $this->excel->getActiveSheet()->getRowDimension($k)->setRowHeight(16);
	  $this->excel->getActiveSheet()->getStyle('G'.$k.':H'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	  $this->excel->getActiveSheet()->getRowDimension($k)->setRowHeight(20);
	  $k++;
 }
 $this->excel->getActiveSheet()->getStyle('A'.($k).':H'.($k))->applyFromArray($styleDOTBlackBorderOutline);
 $this->excel->getActiveSheet()->getStyle('A'.($k+1).':H'.($k+1))->applyFromArray($styleDOTBlackBorderOutline);
/* boder cot */
 /* style border cot */
$this->excel->getActiveSheet()->getStyle('A9:A'.($k+1))->getBorders()->getLEFT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('A9:A'.($k+1))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('B9:B'.($k+1))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('C9:C'.($k+1))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('D9:D'.($k+1))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('E9:E'.($k+1))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('F9:F'.($k+1))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//if($cost_type == 'thu' || $cost_type == 'all'){
$this->excel->getActiveSheet()->getStyle('G9:G'.($k+1))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//}if($cost_type == 'chi' || $cost_type == 'all'){
$this->excel->getActiveSheet()->getStyle('H9:H'.($k+1))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//}
/* end border cot */
  $i = $this->excel->getActiveSheet()->getHighestRow() ;
  $this->excel->setActiveSheetIndex(0)->mergeCells('A' . ($i).':E'.($i));
  $this->excel->getActiveSheet()->setCellValue('A' . ($i), 'TỔNG CỘNG');
  $this->excel->getActiveSheet()->getStyle('A' . ($i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  
  $this->excel->getActiveSheet()->getStyle('G'.$i.':H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
 
  $this->excel->getActiveSheet()->setCellValue('G' . ($i), to_currency_unVND_nomar($tien_thu_tong));
  
    
  $this->excel->getActiveSheet()->setCellValue('H' . ($i), to_currency_unVND_nomar($tien_chi_tong));
 
  $this->excel->getActiveSheet()->getRowDimension($i)->setRowHeight(18);
  $this->excel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$this->excel->getActiveSheet()->getStyle('G'.$i.':H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	$this->excel->getActiveSheet()->getStyle('E'.($i))->getFont()->setBold(true);
	$this->excel->getActiveSheet()->getStyle('G'.($i))->getFont()->setBold(true);
	$this->excel->getActiveSheet()->getStyle('H'.($i))->getFont()->setBold(true);
	$this->excel->getActiveSheet()->getStyle('A'.($i).':H'.($i))->applyFromArray($styleThinBlackBorderOutline);
 
 $this->excel->getActiveSheet()->setCellValue('F' . ($i+2), 'Ngày ......../......../............');
 
 $this->excel->setActiveSheetIndex(0)->mergeCells('B' . ($i+4).':C'.($i+4));
 $this->excel->getActiveSheet()->setCellValue('B' . ($i+4), 'THỦ QUỸ');
 $this->excel->getActiveSheet()->getStyle('B' . ($i+4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
 $this->excel->getActiveSheet()->setCellValue('E' . ($i+4), 'KẾ TOÁN TRƯỞNG');
 $this->excel->setActiveSheetIndex(0)->mergeCells('F' . ($i+4).':G'.($i+4));
 $this->excel->getActiveSheet()->setCellValue('F' . ($i+4), 'GIÁM ĐỐC');
 $this->excel->getActiveSheet()->getStyle('F' . ($i+4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
 $this->excel->getActiveSheet()->getStyle('A'.($i+4).':H'.($i+4))->getFont()->setBold(true);
 
 $this->excel->setActiveSheetIndex(0)->mergeCells('B' . ($i+5).':C'.($i+5));
 $this->excel->getActiveSheet()->setCellValue('B' . ($i+5), '(Ký, ghi họ tên)');
 $this->excel->getActiveSheet()->getStyle('B' . ($i+5))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
 $this->excel->getActiveSheet()->setCellValue('E' . ($i+5), '(Ký, ghi họ tên)');
 $this->excel->setActiveSheetIndex(0)->mergeCells('F' . ($i+5).':G'.($i+5));
 $this->excel->getActiveSheet()->setCellValue('F' . ($i+5), '(Ký, ghi họ tên, đóng dấu)');
 $this->excel->getActiveSheet()->getStyle('F' . ($i+5))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
 $this->excel->getActiveSheet()->getStyle('A'.($i+5).':H'.($i+5))->getFont()->setItalic(true);
 $styleThinBlackBorderOutline = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
        ),
    ),
);


$this->excel->getActiveSheet()->getHeaderFooter()->setOddFooter('&RPage &P of &N');
$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(11);
$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(11);
$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(11);
$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(17.17);
$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(67.67);
$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(12.17);
$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(13.86);
$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(13.86);

$this->excel->getActiveSheet()->getPageMargins()->setRight(0.1);
$this->excel->getActiveSheet()->getPageMargins()->setLeft(0.15);
$this->excel->getActiveSheet()->getPageMargins()->setTop(0.15);
$this->excel->getActiveSheet()->getPageMargins()->setBOTTOM(0.2);
$this->excel->getActiveSheet()->getPageMargins()->setHEADER(0.1);
$this->excel->getActiveSheet()->getPageMargins()->setFOOTER(0.1);

$this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('C6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('D6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('E6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('F6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('G6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('H6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
//if you want to save it as .XLSX Excel 2007 format
$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $md5file = md5(date('YmdHis')) . '.xlsx';
//$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
//force user to download the Excel file without writing it to server's HD
$filename = 'thongtin_chiphi_thuchi.xlsx'; //save our workbook as this file name
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