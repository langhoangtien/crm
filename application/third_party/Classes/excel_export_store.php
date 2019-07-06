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
$this->excel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTop(array(6, 6));
//activate worksheet number 1
$this->excel->setActiveSheetIndex(0);
//error_reporting(E_ALL);
error_reporting(E_ALL & ~E_NOTICE);

$this->excel->getActiveSheet()->setTitle('Báo cáo xuất kho');
$this->excel->setActiveSheetIndex(0)->mergeCells('A1:G1');
$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);

$this->excel->setActiveSheetIndex(0)->mergeCells('A1:G1');
$this->excel->getActiveSheet()->setCellValue('A1', "BÁO CÁO XUẤT KHO");
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(14)->setBold(true);
$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->setActiveSheetIndex(0)->mergeCells('B2:F2');
$this->excel->getActiveSheet()->setCellValue('B2', 'Từ ngày :' . date('d-m-Y H:i:s', strtotime($start_date)) . ' đến ngày ' . date('d-m-Y H:i:s', strtotime($end_date)));
$this->excel->getActiveSheet()->getStyle('B2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

if ($store_id == "0") {
    $name_store = " Kho tổng";
} elseif ($store_id == "all") {
    $name_store = " Tất cả";
} else {
    $info_store = $this->Create_invetory->get_info($store_id);
    $name_store = $info_store->name_inventory;
}

//$this->excel->setActiveSheetIndex(0)->mergeCells('A3:B3');
$this->excel->getActiveSheet()->setCellValue('A3', "Kho mặt hàng:");
$this->excel->getActiveSheet()->setCellValue('B3', $name_store);

$this->excel->getActiveSheet()->getStyle('A3')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('C3')->getFont()->setSize(12);

$this->excel->getActiveSheet()->setCellValue('A4', "Mã đơn hàng");
$this->excel->getActiveSheet()->setCellValue('B4', "Mã mặt hàng");
$this->excel->getActiveSheet()->setCellValue('C4', "Tên mặt hàng");
$this->excel->getActiveSheet()->setCellValue('D4', "Ngày xuất kho");
$this->excel->getActiveSheet()->setCellValue('E4', "ĐVT");
$this->excel->getActiveSheet()->setCellValue('F4', "Giá vốn");
$this->excel->getActiveSheet()->setCellValue('G4', "Số lượng");
$this->excel->getActiveSheet()->setCellValue('H4', "Thành tiền");
$this->excel->getActiveSheet()->setCellValue('I4', 'Nhân viên xuất');

$this->excel->getActiveSheet()->getStyle('A4:B4')->getAlignment()->setWrapText(true);

$this->excel->getActiveSheet()->getStyle('A4:I4')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getRowDimension('4')->setRowHeight(18);

$this->excel->getActiveSheet()->getStyle('A4:H4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A4:H4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

$k = 5;

$this->load->model('Create_invetory');

$data_dh = $this->Create_invetory->get_export_store($start_date, $end_date, $store_id);
$data_export = $this->Create_invetory->get_export_store_item($start_date, $end_date, $store_id);
$count = 0;
foreach ($data_dh as $val) {
    $data_tam = $this->Create_invetory->get_row_export_store_item($val['export_store_id']);
    $count = count($data_tam);

    $this->excel->setActiveSheetIndex(0)->mergeCells('A' . $k . ':A' . ($k + $count - 1));
    $this->excel->getActiveSheet()->setCellValue('A' . $k, 'MĐH' . ($val['export_store_id']));
    $this->excel->getActiveSheet()->getStyle('A' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_TOP);
    foreach ($data_export as $val1) {
        if ($val1['export_store_id'] == $val['export_store_id']) {
            $total_thanh_tien += $val1['cost_price_export'] * $val1['quantity_export'];
            $total_quan += $val1['quantity_export'];
            $name_employee = $this->Employee->get_info($val['employee_id'])->first_name . ' ' . $this->Employee->get_info($val['employee_id'])->last_name;
            $this->excel->getActiveSheet()->setCellValue('B' . $k, $this->Item->get_info($val1['item_id'])->item_number);
            $this->excel->getActiveSheet()->getStyle('B' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $this->excel->getActiveSheet()->setCellValue('C' . $k, $this->Item->get_info($val1['item_id'])->name);
            $this->excel->getActiveSheet()->setCellValue('D' . $k, date('d-m-Y H:i:s', strtotime($val['date_export'])));
            $this->excel->getActiveSheet()->setCellValue('E' . $k, $this->Unit->get_info($val1['unit_export'])->name);
            $this->excel->getActiveSheet()->getStyle('E' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $this->excel->getActiveSheet()->setCellValue('F' . $k, number_format($val1['cost_price_export']));
            $this->excel->getActiveSheet()->getStyle('F' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $this->excel->getActiveSheet()->setCellValue('G' . $k, format_quantity($val1['quantity_export']));
            $this->excel->getActiveSheet()->getStyle('G' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $this->excel->getActiveSheet()->setCellValue('H' . $k, number_format($val1['cost_price_export'] * $val1['quantity_export']));
            $this->excel->getActiveSheet()->getStyle('H' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $this->excel->getActiveSheet()->setCellValue('I' . $k, $name_employee);
            $this->excel->getActiveSheet()->getStyle('I' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $this->excel->getActiveSheet()->getRowDimension($k)->setRowHeight(15.75);
            $k++;
        }
    }
}
$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);

$this->excel->getActiveSheet()->getPageMargins()->setRight(0.25);
$this->excel->getActiveSheet()->getPageMargins()->setLeft(1.5);

$j = $this->excel->getActiveSheet()->getHighestRow();

$this->excel->setActiveSheetIndex(0)->mergeCells('A' . ($j + 1) . ':F' . ($j + 1));
$this->excel->getActiveSheet()->setCellValue('A' . ($j + 1), 'Tổng cộng');
$this->excel->getActiveSheet()->getStyle('A' . ($j + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$this->excel->getActiveSheet()->getRowDimension($j + 1)->setRowHeight(18);
$this->excel->getActiveSheet()->getStyle('A' . ($j + 1))->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->setCellValue('G' . ($j + 1), format_quantity($total_quan));
$this->excel->getActiveSheet()->getStyle('G' . ($j + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$this->excel->getActiveSheet()->setCellValue('H' . ($j + 1), number_format($total_thanh_tien));
$this->excel->getActiveSheet()->getStyle('H' . ($j + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$styleThinBlackBorderOutline = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
        ),
    ),
);


$this->excel->getActiveSheet()->getStyle('A4:I' . ($k - 1))->applyFromArray($styleThinBlackBorderOutline);

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
