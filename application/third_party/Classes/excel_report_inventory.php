<?php
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

$this->excel->getActiveSheet()->setTitle('Báo cáo hàng tồn kho');
$this->excel->setActiveSheetIndex(0)->mergeCells('A1:G1');
$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);

$this->excel->setActiveSheetIndex(0)->mergeCells('A2:G2');
$this->excel->getActiveSheet()->setCellValue('A2', "BÁO CÁO HÀNG TỒN KHO");
$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(14)->setBold(true);
$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
if($store == 0){
    $name_store = "Kho tổng";
}else if($store == "2000"){
    $name_store = "Tất cả";
}else{
    $info_store = $this->Create_invetory->get_info($store);
    $name_store = $info_store->name_inventory;
}
$this->excel->setActiveSheetIndex(0)->mergeCells('A3:B3');
$this->excel->getActiveSheet()->setCellValue('A3', "Kho mặt hàng:");
$this->excel->getActiveSheet()->setCellValue('C3', $name_store);

$this->excel->getActiveSheet()->getStyle('A3')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('C3')->getFont()->setSize(12);

$this->excel->getActiveSheet()->setCellValue('A4', "STT");
$this->excel->getActiveSheet()->setCellValue('B4', "Mã mặt hàng");
$this->excel->getActiveSheet()->setCellValue('C4', "Tên mặt hàng");
$this->excel->getActiveSheet()->setCellValue('D4', "Đơn vị tính");
$this->excel->getActiveSheet()->setCellValue('E4', "Giá nhập");
$this->excel->getActiveSheet()->setCellValue('F4', "Số lượng tồn");
$this->excel->getActiveSheet()->setCellValue('G4', "Giá trị tồn");

$this->excel->getActiveSheet()->getStyle('A4:B4')->getAlignment()->setWrapText(true);

$this->excel->getActiveSheet()->getStyle('A4:G4')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getRowDimension('4')->setRowHeight(18);

$this->excel->getActiveSheet()->getStyle('A4:G4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A4:G4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

$k = 5;
$stt = 1;
$total_quantity = 0;
$total_value_inventory = 0;
foreach ($report_inventory as $value){    
    if($value['quantity_first'] == 0){
        $info_unit = $this->Unit->get_info($value['unit']);
        $cost_price = $value['cost_price'];
    }else{
        $info_unit = $this->Unit->get_info($value['unit_from']);
        $cost_price = $value['cost_price_rate'];
    }
    $this->excel->getActiveSheet()->setCellValue('A' . $k, $stt);
    $this->excel->getActiveSheet()->getStyle('A' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $this->excel->getActiveSheet()->setCellValue('B' . $k, $value['item_number']);
    $this->excel->getActiveSheet()->getStyle('B' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $this->excel->getActiveSheet()->setCellValue('C' . $k, $value['name']);
    $this->excel->getActiveSheet()->setCellValue('D' . $k, $info_unit->name);
    $this->excel->getActiveSheet()->setCellValue('E' . $k, number_format($cost_price));
    $this->excel->getActiveSheet()->getStyle('E' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $this->excel->getActiveSheet()->setCellValue('F' . $k, format_quantity($value['quantity']));
    $this->excel->getActiveSheet()->getStyle('F' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $this->excel->getActiveSheet()->setCellValue('G' . $k, number_format(($cost_price*$value['quantity'])));
    $this->excel->getActiveSheet()->getStyle('G' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $this->excel->getActiveSheet()->getRowDimension($k)->setRowHeight(15.75);
    $total_quantity += $value['quantity'];
    $total_value_inventory += $cost_price*$value['quantity'];
    $k++;
    $stt++;
}
$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(7);
$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);

$this->excel->getActiveSheet()->getPageMargins()->setRight(0.25);
$this->excel->getActiveSheet()->getPageMargins()->setLeft(1.5);

$j = $this->excel->getActiveSheet()->getHighestRow();

$this->excel->setActiveSheetIndex(0)->mergeCells('A' . ($j+1) . ':E' . ($j+1));
$this->excel->getActiveSheet()->setCellValue('A' . ($j+1), 'Tổng');
$this->excel->getActiveSheet()->getStyle('A' . ($j+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$this->excel->getActiveSheet()->getRowDimension($j+1)->setRowHeight(18);
$this->excel->getActiveSheet()->getStyle('A' . ($j+1))->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->setCellValue('F' . ($j+1), format_quantity($total_quantity));
$this->excel->getActiveSheet()->getStyle('F' . ($j+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$this->excel->getActiveSheet()->setCellValue('G' . ($j+1), number_format($total_value_inventory));
$this->excel->getActiveSheet()->getStyle('G' . ($j+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$styleThinBlackBorderOutline = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
        ),
    ),
);


$this->excel->getActiveSheet()->getStyle('A4:G' . ($k - 1))->applyFromArray($styleThinBlackBorderOutline);

$this->excel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L&BInvoice&RPrinted on &D');

$this->excel->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&B' . $this->excel->getProperties()->getTitle() . '&RPage &P of &N');

//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
//if you want to save it as .XLSX Excel 2007 format

$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
$md5file = md5(date('YmdHis')) . '.xlsx';
$filename = 'Bao_cao_hang_ton_kho.xlsx'; //save our workbook as this file name
$objWriter->save($filename);
if (file_exists($filename)) {
    header('Content-Type: application/vnd.ms-excel'); //mime type
    header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
    header('Cache-Control: max-age=0'); //no cache    
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