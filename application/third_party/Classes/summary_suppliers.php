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
$this->excel->getActiveSheet()->getPageMargins()->setRight(1);
$this->excel->getActiveSheet()->getPageMargins()->setLeft(1);
$this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$this->excel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTop(array(6, 6));
//activate worksheet number 1
$this->excel->setActiveSheetIndex(0);
//error_reporting(E_ALL);
error_reporting(E_ALL & ~E_NOTICE);
//name the worksheet
$this->excel->getActiveSheet()->setTitle('Báo cáo tổng hợp nhà cung cấp');
$this->excel->setActiveSheetIndex(0)->mergeCells('A1:E1');
$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
$this->excel->getActiveSheet()->setCellValue('A1', $data['company']);
$this->excel->getActiveSheet()->setCellValue('A2', $data['address']);
$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->setActiveSheetIndex(0)->mergeCells('A4:E4');
$this->excel->getActiveSheet()->setCellValue('A4', "TỔNG HỢP DANH SÁCH NHÀ CUNG CẤP");
$this->excel->getActiveSheet()->getStyle('A4')->getFont()->setSize(14)->setBold(true);
$this->excel->getActiveSheet()->getStyle('A4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


$this->excel->getActiveSheet()->setCellValue('A6', "Nhà cung cấp");
$this->excel->setActiveSheetIndex(0)->mergeCells('A6:C6');
$this->excel->getActiveSheet()->setCellValue('D6', "Số lượng");
$this->excel->setActiveSheetIndex(0)->mergeCells('D6:F6');
$this->excel->getActiveSheet()->setCellValue('G6', "Tổng tiền thanh toán");

$k = 7;
$data_supplier = $this->Sale->get_supplier_in_receiving($start_date, $end_date);
foreach ($data_supplier as $value) {
    $info_supplier = $this->Supplier->get_info($value['supplier_id']);
	$data_receiving = $this->Sale->get_receiving_by_supplier($value['supplier_id'], $sale_type, $start_date, $end_date);
    if ($data_receiving) {
        $this->excel->getActiveSheet()->setCellValue('A' . $k, $info_supplier->company_name);
        $this->excel->setActiveSheetIndex(0)->mergeCells('A' . $k . ':C' . $k);
        $this->excel->getActiveSheet()->getStyle('A' . ($k))->getFont()->setBold(true)->setSize(12);
        
        $total_quantity = 0;
        $money = 0;
        foreach ($data_receiving as $receiving) {
            if ($receiving['supplier_id'] == $value['supplier_id']) {
                $total_quantity += $receiving['quantity_purchased'];
                $money += $receiving['quantity_purchased'] * $receiving['item_unit_price'] - $receiving['quantity_purchased'] * $receiving['item_unit_price'] * $receiving['discount_percent'] / 100;
            }
        }
        $this->excel->getActiveSheet()->setCellValue('D' . $k, format_quantity($total_quantity));
        $this->excel->setActiveSheetIndex(0)->mergeCells('D' . $k . ':F' . $k);
        $this->excel->getActiveSheet()->setCellValue('G' . $k, number_format($money));     
        $this->excel->getActiveSheet()->getStyle('A' . $k . ':C' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
        foreach ($data_receiving as $receiving) {
            if ($receiving['supplier_id'] == $value['supplier_id']) {
                $info_item = $this->Item->get_info($receiving['item_id']);
                $this->excel->getActiveSheet()->setCellValue('A' . ($k+1), $info_item->item_number);
                $this->excel->getActiveSheet()->setCellValue('B' . ($k+1), $info_item->name);                
                $info_unit = $this->Unit->get_info($info_item->unit);
                $this->excel->getActiveSheet()->setCellValue('C' . ($k+1), $info_unit->name);
                $this->excel->getActiveSheet()->getStyle('A' . ($k+1) . ':C' . ($k+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                
                $this->excel->getActiveSheet()->setCellValue('D' . ($k+1), format_quantity($receiving['quantity_purchased']));
                $this->excel->getActiveSheet()->setCellValue('E' . ($k+1), number_format($receiving['item_unit_price']));
                $this->excel->getActiveSheet()->setCellValue('F' . ($k+1), $receiving['discount_percent']);
                $this->excel->getActiveSheet()->setCellValue('G' . ($k+1), number_format($receiving['quantity_purchased'] * $receiving['item_unit_price'] - $receiving['quantity_purchased'] * $receiving['item_unit_price'] * $receiving['discount_percent'] / 100));
                $this->excel->getActiveSheet()->getStyle('D' . ($k+1) . ':G' . ($k+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            }            
            $k++;
        }        
    }
    $total_money += $money;
    $k++;
}

$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(30);

$this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getStyle('D6')->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getStyle('G6')->getAlignment()->setWrapText(true);
$j = $this->excel->getActiveSheet()->getHighestRow() + 1;

$this->excel->setActiveSheetIndex(0)->mergeCells('D' . ($j) . ':F' . ($j));
$this->excel->getActiveSheet()->setCellValue('D' . ($j), 'Tổng thanh toán');
$this->excel->getActiveSheet()->getRowDimension($j)->setRowHeight(18);
$this->excel->getActiveSheet()->getStyle('D' . ($j))->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->setCellValue('G' . ($j), number_format($total_money).' VNĐ');

$this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('D6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('G6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$styleThinBlackBorderOutline = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
        ),
    ),
);
/* */
$this->excel->getActiveSheet()->getStyle('A6:G' . ($k - 1))->applyFromArray($styleThinBlackBorderOutline);
$this->excel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L&BInvoice&RPrinted on &D');
$this->excel->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&B' . $this->excel->getProperties()->getTitle() . '&RPage &P of &N');
//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
//if you want to save it as .XLSX Excel 2007 format
$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
$md5file = md5(date('YmdHis')) . '.xlsx';
//$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
//force user to download the Excel file without writing it to server's H
$filename = 'Báo cáo tổng hợp nhà cung cấp.xlsx'; //save our workbook as this file name
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