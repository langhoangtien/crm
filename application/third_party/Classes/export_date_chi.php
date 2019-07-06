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
$this->excel->getActiveSheet()->getPageMargins()->setRight(0.1);
$this->excel->getActiveSheet()->getPageMargins()->setLeft(0.27);
$this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$this->excel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTop(array(6,6));
//activate worksheet number 1
$this->excel->setActiveSheetIndex(0);
//error_reporting(E_ALL);
error_reporting(E_ALL & ~E_NOTICE);
//name the worksheet
$this->excel->getActiveSheet()->setTitle('Báo cáo chi theo ngày');
$this->excel->setActiveSheetIndex(0)->mergeCells('A1:C1');
$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
$this->excel->getActiveSheet()->setCellValue('A1',$this->config->item('company'));
$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->setCellValue('A2',$this->config->item('address'));
	$this->excel->setActiveSheetIndex(0)->mergeCells('A4:H4');
	$this->excel->getActiveSheet()->setCellValue('A4', "TỔNG CÁC KHOẢN CHI THEO NGÀY");
	$this->excel->getActiveSheet()->getStyle('A4')->getFont()->setSize(14)->setBold(true);
	$this->excel->getActiveSheet()->getStyle('A4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$this->excel->getActiveSheet()->getStyle('A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
$this->excel->getActiveSheet()->setCellValue('A6', "STT");	
$this->excel->getActiveSheet()->setCellValue('B6', "Ngày thu");
$this->excel->getActiveSheet()->setCellValue('C6', "Số chứng từ");
$this->excel->getActiveSheet()->setCellValue('D6', "Ngày chứng từ");
$this->excel->getActiveSheet()->setCellValue('E6', "Nội dung");
$this->excel->getActiveSheet()->setCellValue('F6', "Tên KH - NCC");
$this->excel->getActiveSheet()->setCellValue('G6', "Nhân viên");
$this->excel->getActiveSheet()->setCellValue('H6', "Tiền chi");

$this->excel->getActiveSheet()->getStyle('A6:H6')->getFont()->setSize(12)->setBold(true);
$k = 7;
$subtotal=0;
if($chi_date != null){
	$stt=1;
foreach($chi_date as $query){
			
			$this->excel->getActiveSheet()->setCellValue('A'.$k, $stt );
			$this->excel->getActiveSheet()->setCellValue('B'.$k, date('d-m-Y H:i:s', strtotime($query['date'])) );
			$this->excel->getActiveSheet()->setCellValue('C'.$k,$query['chungtu']);
			$this->excel->getActiveSheet()->setCellValue('D'.$k, date('d-m-Y', strtotime($query['cost_date_ct'])));
			$this->excel->getActiveSheet()->setCellValue('E'.$k, $query['comment']);
			$this->excel->getActiveSheet()->setCellValue('F'.$k, $this->Customer->get_info($query['id_customer'])->first_name.' '.
																$this->Customer->get_info($query['id_customer'])->last_name);
			$this->excel->getActiveSheet()->setCellValue('G'.$k, $this->Person->get_info($query['cost_employees'])->first_name.' '.
																$this->Person->get_info($query['cost_employees'])->last_name);
			if ($query['tien_chi']>0) {
		$this->excel->getActiveSheet()->setCellValue('H'.$k, to_currency_unVND_nomar($query['tien_chi']));
			} else {
				$this->excel->getActiveSheet()->setCellValue('H'.$k, to_currency_unVND_nomar('0'));
			}
			$this->excel->getActiveSheet()->getRowDimension($k)->setRowHeight(15.75);
	$stt++;		
	$k++;
	$subtotal+=$query['tien_chi'];
		}
} 

$styleThinBlackBorderOutline = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
        ),
    ),
);
$i = $this->excel->getActiveSheet()->getHighestRow() +3;

$this->excel->getActiveSheet()->getPageMargins()->setRight(0.1);
$this->excel->getActiveSheet()->getPageMargins()->setLeft(0.15);
$this->excel->getActiveSheet()->getPageMargins()->setTop(0.25);
$this->excel->getActiveSheet()->getPageMargins()->setBOTTOM(0.5);
$this->excel->getActiveSheet()->getPageMargins()->setHEADER(0.1);
$this->excel->getActiveSheet()->getPageMargins()->setFOOTER(0);

$this->excel->getActiveSheet()->getRowDimension(1)->setRowHeight(21.75);
//$this->excel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
$this->excel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
$this->excel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
$this->excel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
$this->excel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
$this->excel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
$this->excel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
$this->excel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);

$this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('C6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('D6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('E6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('F6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('G6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('H6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->getActiveSheet()->setCellValue('E' . ($i-2),"TỔNG CỘNG" );
$this->excel->getActiveSheet()->getStyle('E' . ($i-2))->getFont()->setBold(true);
  $this->excel->getActiveSheet()->getStyle('E'.($i-2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
 $this->excel->getActiveSheet()->setCellValue('H' . ($i-2),to_currency_unVND($subtotal));
 $this->excel->getActiveSheet()->getStyle('H' . ($i-2))->getFont()->setBold(true);
 $this->excel->getActiveSheet()->getRowDimension($i-2)->setRowHeight(16.75);

/* style */
 $styleThinBlackBorderOutline = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
			),
		),
	);
/* */
$this->excel->getActiveSheet()->getStyle('A6:H' . ($k))->applyFromArray($styleThinBlackBorderOutline);


$this->excel->getActiveSheet()->getHeaderFooter()->setOddFooter('&RPage &P of &N');

//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
//if you want to save it as .XLSX Excel 2007 format
$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $md5file = md5(date('YmdHis')) . '.xlsx';
//$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
//force user to download the Excel file without writing it to server's HD
$filename = 'baocaochi_theongay.xlsx'; //save our workbook as this file name
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