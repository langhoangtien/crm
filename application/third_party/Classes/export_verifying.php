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
$this->excel->getActiveSheet()->setTitle('Báo cáo kiểm kê');
$this->excel->setActiveSheetIndex(0)->mergeCells('A1:D1');
$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
$this->excel->getActiveSheet()->setCellValue('A1',$this->config->item('company'));
$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->setActiveSheetIndex(0)->mergeCells('A2:D2');
$this->excel->getActiveSheet()->setCellValue('A2',$this->config->item('address'));
	$this->excel->setActiveSheetIndex(0)->mergeCells('A4:I4');
	$this->excel->getActiveSheet()->setCellValue('A4', "BÁO CÁO KIỂM KHO");
	$this->excel->getActiveSheet()->getStyle('A4')->getFont()->setSize(14)->setBold(true);
	$this->excel->getActiveSheet()->getStyle('A4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$this->excel->getActiveSheet()->getStyle('A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
	$this->excel->getActiveSheet()->setCellValue('A5', "KHO : ");
	$this->excel->getActiveSheet()->setCellValue('B5',$this->Create_invetory->get_info($store)->name_inventory);
	$this->excel->getActiveSheet()->getStyle('B5')->getFont()->setBold(true);
	$this->excel->getActiveSheet()->getRowDimension('B5')->setRowHeight(15.75);
	
$this->excel->getActiveSheet()->setCellValue('A6', "STT");
$this->excel->getActiveSheet()->setCellValue('B6', "Tên sản phẩm");
$this->excel->getActiveSheet()->setCellValue('C6', "Loại sản phẩm");
$this->excel->getActiveSheet()->setCellValue('D6', "Giá nhập");
$this->excel->getActiveSheet()->setCellValue('E6', "SL nhập");
$this->excel->getActiveSheet()->setCellValue('F6', "SL kho");
$this->excel->getActiveSheet()->setCellValue('G6', "SL bán");
$this->excel->getActiveSheet()->setCellValue('H6', "SL kiểm kê thực tế");
$this->excel->getActiveSheet()->setCellValue('I6', "SL kiểm chênh lệch");

$this->excel->getActiveSheet()->getStyle('A6:I6')->getFont()->setSize(12)->setBold(true);
$k = 7;
$stt=1;
if($verifying != null){
foreach($verifying as $query){

			$this->excel->getActiveSheet()->setCellValue('A'.$k, $stt );
			$this->excel->getActiveSheet()->setCellValue('B'.$k,$query['name']);
			$this->excel->getActiveSheet()->setCellValue('C'.$k, $this->Category->get_info($query['category'])->name);
			$this->excel->getActiveSheet()->setCellValue('D'.$k, number_format($query['cost_price']));
			$this->excel->getActiveSheet()->setCellValue('E'.$k, number_format($query['quantity_input']));
			$this->excel->getActiveSheet()->setCellValue('F'.$k, number_format($query['quantity_inventory']));
			$this->excel->getActiveSheet()->setCellValue('G'.$k, number_format($query['quantity_sale']));
			$this->excel->getActiveSheet()->setCellValue('H'.$k, number_format($query['quantity_verifying']));

			$this->excel->getActiveSheet()->setCellValue('I'.$k, number_format($query['quantity_inventory']-$query['quantity_verifying']));
			$this->excel->getActiveSheet()->getRowDimension($k)->setRowHeight(15.75);
		$k++;
	$stt++;
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

$this->excel->getActiveSheet()->getPageMargins()->setRight(0.1);
$this->excel->getActiveSheet()->getPageMargins()->setLeft(0.15);
$this->excel->getActiveSheet()->getPageMargins()->setTop(0.25);
$this->excel->getActiveSheet()->getPageMargins()->setBOTTOM(0.5);
$this->excel->getActiveSheet()->getPageMargins()->setHEADER(0.1);
$this->excel->getActiveSheet()->getPageMargins()->setFOOTER(0);

$this->excel->getActiveSheet()->getRowDimension(1)->setRowHeight(21.75);
$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(7);
$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(17.00);
$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$this->excel->getActiveSheet()->getColumnDimension('G')->setAutoSize(15);
$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(27);
$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(27);


$this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('C6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('D6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('E6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('F6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('G6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('H6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('I6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

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
$this->excel->getActiveSheet()->getStyle('A6:I' . ($k-1))->applyFromArray($styleThinBlackBorderOutline);
$this->excel->getActiveSheet()->getHeaderFooter()->setOddFooter('&RPage &P of &N');

//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
//if you want to save it as .XLSX Excel 2007 format
$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $md5file = md5(date('YmdHis')) . '.xlsx';
//$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
//force user to download the Excel file without writing it to server's HD
$filename = 'baocao_kiemkho.xlsx'; //save our workbook as this file name
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