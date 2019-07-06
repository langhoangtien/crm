<?php

$this->load->library('Excel');
$objPHPExcel = new PHPExcel();
$this->excel->getDefaultStyle()
        ->getAlignment()
        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Times New Roman');
$this->excel->getActiveSheet()->getDefaultStyle()->getFont()->getSize(9);
$this->excel->getActiveSheet()->setShowGridlines(false);
$this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
$this->excel->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
$this->excel->getActiveSheet()->getPageSetup()->setVerticalCentered(false);
$this->excel->getActiveSheet()->getPageMargins()->setLEFT(0.25);
$this->excel->getActiveSheet()->getPageMargins()->setRIGHT(0.25);
$this->excel->getActiveSheet()->getPageMargins()->setTop(0.25);
$this->excel->getActiveSheet()->getPageMargins()->setBOTTOM(0.3);
$this->excel->getActiveSheet()->getPageMargins()->setFOOTER(0.1);
$this->excel->getActiveSheet()->getPageMargins()->setHEADER(0);
$this->excel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTop(array(6, 6));
//activate worksheet number 1
$this->excel->setActiveSheetIndex(0);
//error_reporting(E_ALL);
error_reporting(E_ALL & ~E_NOTICE);
//name the worksheet
$this->excel->getActiveSheet()->setTitle('Danh sách mặt hàng');
$this->excel->setActiveSheetIndex(0)->mergeCells('A1:P1');
$this->excel->getActiveSheet()->setCellValue("A1", "DANH SÁCH MẶT HÀNG");
$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(11);
$this->excel->getActiveSheet()->getRowDimension(1)->setRowHeight(30.75);

$this->excel->getActiveSheet()->setCellValue('A2', "Mã mặt hàng");
$this->excel->getActiveSheet()->setCellValue('B2', "Tên mặt hàng");
$this->excel->getActiveSheet()->setCellValue('C2', "Mô tả");
$this->excel->getActiveSheet()->setCellValue('D2', "Mã nhóm MH");
$this->excel->getActiveSheet()->setCellValue('E2', "Mã ĐVT");
$this->excel->getActiveSheet()->setCellValue('F2', "Số lượng");
$this->excel->getActiveSheet()->setCellValue('G2', "Giá nhập");
$this->excel->getActiveSheet()->setCellValue('H2', "Giá Bán");
$this->excel->getActiveSheet()->setCellValue('I2', "Mã ĐVT quy đổi");
$this->excel->getActiveSheet()->setCellValue('J2', "Tỉ lệ quy đổi");
$this->excel->getActiveSheet()->setCellValue('K2', "Thuế 1");
$this->excel->getActiveSheet()->setCellValue('L2', "Hạn mức bán hàng");
$this->excel->getActiveSheet()->setCellValue('M2', "Mã kho");
$this->excel->getActiveSheet()->getStyle('A2:M2')->getFont()->setSize(10)->setBold(true);
$this->excel->getActiveSheet()->getStyle('A2:M2')->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getStyle('A2:M2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A2:M2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B2')->getFont()->getColor()->setRGB('FF0000');
$this->excel->getActiveSheet()->getStyle('D2')->getFont()->getColor()->setRGB('FF0000');
$this->excel->getActiveSheet()->getStyle('E2')->getFont()->getColor()->setRGB('FF0000');
$this->excel->getActiveSheet()->getStyle('L2')->getFont()->getColor()->setRGB('FF0000');
$this->excel->getActiveSheet()->getStyle('M2')->getFont()->getColor()->setRGB('FF0000');
$this->excel->getActiveSheet()->getComment('A2')->getText()->createTextRun('Mã mặt hàng');
$this->excel->getActiveSheet()->getComment('B2')->getText()->createTextRun('Tên mặt hàng');
$this->excel->getActiveSheet()->getComment('C2')->getText()->createTextRun('Mô tả về mặt hàng');
$this->excel->getActiveSheet()->getComment('D2')->getText()->createTextRun('Mã nhóm mặt hàng');
$this->excel->getActiveSheet()->getComment('E2')->getText()->createTextRun('Mã đơn vị tính');
$this->excel->getActiveSheet()->getComment('F2')->getText()->createTextRun('Số lượng');
$this->excel->getActiveSheet()->getComment('G2')->getText()->createTextRun('Giá nhập');
$this->excel->getActiveSheet()->getComment('H2')->getText()->createTextRun('Giá bán');
$this->excel->getActiveSheet()->getComment('I2')->getText()->createTextRun('Mã đơn vị tính sau quy đổi');
$this->excel->getActiveSheet()->getComment('J2')->getText()->createTextRun('Tỉ lệ quy đổi tương ứng khi có quy đổi');
$this->excel->getActiveSheet()->getComment('K2')->getText()->createTextRun('Thuế');
$this->excel->getActiveSheet()->getComment('L2')->getText()->createTextRun('Hạn mức bán hàng (Nhập số)');
$this->excel->getActiveSheet()->getComment('M2')->getText()->createTextRun('Mã kho');

$this->excel->getActiveSheet()->getRowDimension(2)->setRowHeight(25.50);
$k = 3;
$n = count($items);
//Dung vong lap for thay foreach de no ko xuat ra dong cuoi cung ko co du lieu, phuc vu cho viec import nguoc lai file 
//excel vua xuat ra khong bi thong bao loi
for ($i = 0; $i <= ($n - 1); $i++) {
    $this->excel->getActiveSheet()->setCellValue("A" . $k, $items[$i]['item_number']);
    $this->excel->getActiveSheet()->setCellValue("B" . $k, $items[$i]['name']);
    $this->excel->getActiveSheet()->setCellValue("C" . $k, $items[$i]['description']);
    $info_cat = $this->Category->get_info($items[$i]['category']);
    $this->excel->getActiveSheet()->setCellValue("D" . $k, $info_cat->code_cat);
    $this->excel->getActiveSheet()->setCellValue("E" . $k, $items[$i]['unit']);
    $quan = $items[$i]['quantity_first'] == 0 ? $items[$i]['quantity_store'] : ($items[$i]['quantity_store'] / $items[$i]['unit_rate']);
    $this->excel->getActiveSheet()->setCellValue("F" . $k, $quan);
    $this->excel->getActiveSheet()->setCellValue("G" . $k, $items[$i]['cost_price']);
    $this->excel->getActiveSheet()->setCellValue("H" . $k, $items[$i]['unit_price']);
    $this->excel->getActiveSheet()->setCellValue("I" . $k, $items[$i]['unit_from']);
    $this->excel->getActiveSheet()->setCellValue("J" . $k, $items[$i]['unit_rate']);
    $this->excel->getActiveSheet()->setCellValue("K" . $k, $items[$i]['taxes']);
    $this->excel->getActiveSheet()->setCellValue("L" . $k, $items[$i]['reorder_level']);
    if ($stores) {
        $this->excel->getActiveSheet()->setCellValue("M" . $k, $stores);
    } else {
        $this->excel->getActiveSheet()->setCellValue("M" . $k, 0);
    }
    $this->excel->getActiveSheet()->getStyle("A" . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $this->excel->getActiveSheet()->getStyle("D" . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $this->excel->getActiveSheet()->getStyle("E" . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $this->excel->getActiveSheet()->getStyle("F" . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $this->excel->getActiveSheet()->getStyle("G" . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $this->excel->getActiveSheet()->getStyle("H" . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $this->excel->getActiveSheet()->getStyle("I" . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $this->excel->getActiveSheet()->getStyle("J" . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $this->excel->getActiveSheet()->getStyle("K" . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $this->excel->getActiveSheet()->getStyle("N" . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $this->excel->getActiveSheet()->getStyle("L" . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $this->excel->getActiveSheet()->getStyle("M" . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

    $this->excel->getActiveSheet()->getStyle("F" . $k)->getNumberFormat()->setFormatCode("#,##0");
    $this->excel->getActiveSheet()->getStyle("G" . $k)->getNumberFormat()->setFormatCode("#,##0");
    $this->excel->getActiveSheet()->getStyle("H" . $k)->getNumberFormat()->setFormatCode("#,##0");

    $this->excel->getActiveSheet()->getStyle("A" . $k . ":M" . $k)->getFont()->setSize(10);
    if ($i == ($n - 1)) {
        break;
    } else {
        $k++;
    }
}
$this->excel->getActiveSheet()->getRowDimension($k)->setRowHeight(12.75);

$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(21.14);
$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20.86);
$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(8.43);
$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(8.43);
$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(8.43);
$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(10.43);
$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(9.86);
$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(9.86);
$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(9.29);
$this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(9.29);
$this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(14);
$this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(8.29);

$styleThinBlackBorderOutline = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
        ),
    ),
);

$this->excel->getActiveSheet()->getStyle('A2:M' . ($k))->applyFromArray($styleThinBlackBorderOutline);
$this->excel->getActiveSheet()->getHeaderFooter()->setOddFooter('&RPage &P of &N');

//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
//if you want to save it as .XLSX Excel 2007 format
$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
$md5file = md5(date('YmdHis')) . '.xlsx';
//$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
//force user to download the Excel file without writing it to server's HD
$filename = 'mat_hang.xlsx'; //save our workbook as this file name
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