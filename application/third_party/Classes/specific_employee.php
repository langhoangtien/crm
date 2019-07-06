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
$this->excel->getActiveSheet()->getPageMargins()->setRight(0.25);
$this->excel->getActiveSheet()->getPageMargins()->setLeft(0.25);
$this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$this->excel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTop(array(7, 7));
//activate worksheet number 1
$this->excel->setActiveSheetIndex(0);
//error_reporting(E_ALL);
error_reporting(E_ALL & ~E_NOTICE);
//name the worksheet
$this->excel->getActiveSheet()->setTitle('Báo cáo chi tiết nhân viên');
$this->excel->setActiveSheetIndex(0)->mergeCells('A1:E1');
$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
$this->excel->getActiveSheet()->setCellValue('A1', $data['company']);
$this->excel->getActiveSheet()->setCellValue('A2', $this->config->item('address'));
$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
//$this->excel->getActiveSheet()->setCellValue('A3',$data['full_name']);
$this->excel->setActiveSheetIndex(0)->mergeCells('B4:I4');
$this->excel->getActiveSheet()->setCellValue('B4', "BÁO CÁO CHI TIẾT NHÂN VIÊN");
$this->excel->getActiveSheet()->getStyle('B4')->getFont()->setSize(14)->setBold(true);
$this->excel->getActiveSheet()->getStyle('B4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->getActiveSheet()->setCellValue('C5', 'Từ ' . date('d-m-Y H:i:s', strtotime($start_date)) . ' đến ' . date('d-m-Y H:i:s', strtotime($end_date)));
$this->excel->setActiveSheetIndex(0)->mergeCells('C5:H5');
$this->excel->getActiveSheet()->getStyle('C5')->getFont()->setSize(10)->setItalic(true);
$this->excel->getActiveSheet()->getStyle('C5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('C5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


$this->excel->getActiveSheet()->setCellValue('A6', "Tên nhân viên: " . mb_convert_case($employee_info->first_name, MB_CASE_UPPER, "UTF-8"));
$this->excel->setActiveSheetIndex(0)->mergeCells('A6:J6');
$this->excel->getActiveSheet()->getStyle('A6')->getFont()->setSize(10)->setBold(true);
$this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->getActiveSheet()->setCellValue('A7', "Mã ĐH ");
$this->excel->getActiveSheet()->setCellValue('B7', "Ngày ");
$this->excel->getActiveSheet()->setCellValue('C7', "");
$this->excel->getActiveSheet()->setCellValue('D7', "Tên khách hàng");
$this->excel->getActiveSheet()->setCellValue('E7', "Số lượng");
$this->excel->getActiveSheet()->setCellValue('F7', "Giá trị đơn hàng");
$this->excel->getActiveSheet()->setCellValue('G7', "Tổng chiết khấu");
$this->excel->getActiveSheet()->setCellValue('H7', "Thuế");
$this->excel->getActiveSheet()->setCellValue('I7', "Doanh thu");
if ($report_type == 'all') {
    $this->excel->getActiveSheet()->setCellValue('J7', "Thực thu");
}
$this->excel->getActiveSheet()->getStyle('A7:J7')->getFont()->setSize(12)->setBold(true);
$this->excel->getActiveSheet()->getStyle('A7:J7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A7:J7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$k = 8;
$total_cost = 0;
$total_discount = 0;
$total_real = 0;
foreach ($data_all_sale as $key => $val) {
    foreach ($val as $key1 => $val1) {
        $this->excel->getActiveSheet()->setCellValue('A' . $k, 'ĐH ' . $key);
        $this->excel->getActiveSheet()->setCellValue('B' . $k, $val1['date_tam']);
        $this->excel->getActiveSheet()->setCellValue('C' . $k, '');
        $this->excel->getActiveSheet()->setCellValue('D' . $k, $val1['first_name'] . ' ' . $val1['last_name']);
        foreach ($info_total_sale as $key2 => $val2) {
            if ($key2 == $key) {
                $total_price = 0;
                $total_item = 0;
                $discount_money = 0;
                foreach ($detail_sale as $key3 => $val3) {
                    if ($key3 == $key) {
                        foreach ($val3 as $key4 => $val4) {
                            foreach ($val4 as $key5 => $val5) {
                                $price = $val5['unit_item'] == $val5['unit'] ? $val5['item_unit_price'] : $val5['item_unit_price_rate'];
                                $total_item += $val5['quantity_purchased'];
                                $discount_money += $price * $val5['discount_percent'] / 100 * $val5['quantity_purchased'];
                                $total_price += ($price * $val5['quantity_purchased']);
                            }
                        }
                    }
                }
                //tien chiet khau = ( tong so luong * tien chiet khau % ) + chiet khau tien mat
                $discount_money_total = $report_type == 'all' ? $discount_money + $val1['discount_money2'] : $discount_money;

                $this->excel->getActiveSheet()->setCellValue('E' . $k, format_quantity($total_item));
                $this->excel->getActiveSheet()->getStyle('C' . $k . ':E' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->setCellValue('F' . $k, (($key1 == 0) && ($val1['liability'] == 0)) ? to_currency_unVND_nomar($total_price) : '');
                $this->excel->getActiveSheet()->getStyle('F' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $this->excel->getActiveSheet()->setCellValue('G' . $k, to_currency_unVND_nomar($discount_money_total));
                $this->excel->getActiveSheet()->getStyle('G' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $tien_thue = 0;
                foreach ($detail_sale as $key3 => $val3) {
                    if ($key3 == $key) {
                        foreach ($val3 as $key4 => $val4) {
                            foreach ($val4 as $key5 => $val5) {
                                $price = $val5['unit_item'] == $val5['unit'] ? $val5['item_unit_price'] : $val5['item_unit_price_rate'];
                                $discount_money2 = $price * $val5['discount_percent'] / 100 * $val5['quantity_purchased'];
                                $total_price2 = ($price * $val5['quantity_purchased']);
                                $tien_thue += ($total_price2 - $discount_money2) / 100 * $val5['taxes'];
                            }
                        }
                    }
                }
                //$tien_thue = ($total_price3 - $discount_money3)/100 * $taxes_percent3;
                $doanh_thu = $total_price - $discount_money_total + $tien_thue;

                $this->excel->getActiveSheet()->setCellValue('H' . $k, to_currency_unVND_nomar($tien_thue));
                $this->excel->getActiveSheet()->setCellValue('I' . $k, (($key1 == 0) && ($val1['liability'] == 0)) ? to_currency_unVND_nomar($doanh_thu) : '');
                $this->excel->getActiveSheet()->getStyle('I' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                if (($key1 == 0) && ($val1['liability'] == 0)) {
                    $total_cost += $doanh_thu;
                }
            }
        }
        if ($report_type == 'all') {
            $this->excel->getActiveSheet()->setCellValue('J' . $k, to_currency_unVND_nomar($val1['pays_amount2']));
        }
        $this->excel->getActiveSheet()->getStyle('J' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        $total_real += $val1['pays_amount2'];
        $total_discount += $discount_money_total;
        $this->excel->getActiveSheet()->getRowDimension($k)->setRowHeight(17.75);
        $k++;
    }
}

$this->excel->getActiveSheet()->getRowDimension(1)->setRowHeight(21.75);
$this->excel->getActiveSheet()->getRowDimension(5)->setRowHeight(16);
$this->excel->getActiveSheet()->getRowDimension(6)->setRowHeight(18);
$this->excel->getActiveSheet()->getRowDimension(7)->setRowHeight(28);
$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(11);
$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(19);
$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(0);
$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(21);
$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(18);
$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(14);
$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(17);
$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(17);

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

$j = $this->excel->getActiveSheet()->getHighestRow() + 1;
if ($report_type == 'all') {
    $this->excel->setActiveSheetIndex(0)->mergeCells('H' . ($j) . ':I' . ($j));
    $this->excel->getActiveSheet()->setCellValue('H' . ($j), 'Tổng doanh thu');
    $this->excel->getActiveSheet()->getRowDimension($j)->setRowHeight(18);
    $this->excel->getActiveSheet()->getStyle('J' . ($j))->getFont()->setBold(true)->setSize(12);
    $this->excel->getActiveSheet()->setCellValue('J' . ($j), to_currency_unVND_nomar($total_cost) . ' VNĐ');

    $this->excel->setActiveSheetIndex(0)->mergeCells('H' . ($j + 1) . ':I' . ($j + 1));
    $this->excel->getActiveSheet()->setCellValue('H' . ($j + 1), 'Tổng chiết khấu');
    $this->excel->getActiveSheet()->getRowDimension($j + 1)->setRowHeight(18);
    $this->excel->getActiveSheet()->getStyle('J' . ($j + 1))->getFont()->setBold(true)->setSize(12);
    $this->excel->getActiveSheet()->setCellValue('J' . ($j + 1), to_currency_unVND_nomar($total_discount) . ' VNĐ');

    $this->excel->setActiveSheetIndex(0)->mergeCells('H' . ($j + 2) . ':I' . ($j + 2));
    $this->excel->getActiveSheet()->setCellValue('H' . ($j + 2), 'Tổng thực thu');
    $this->excel->getActiveSheet()->getRowDimension($j + 2)->setRowHeight(18);
    $this->excel->getActiveSheet()->getStyle('J' . ($j + 2))->getFont()->setBold(true)->setSize(12);
    $this->excel->getActiveSheet()->setCellValue('J' . ($j + 2), to_currency_unVND_nomar($total_real) . ' VNĐ');

    $this->excel->getActiveSheet()->getStyle('H' . $j . ':H' . ($j + 2))->getFont()->setSize(12);
    $this->excel->getActiveSheet()->getStyle('J' . $j . ':J' . ($j + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
} else {
    $this->excel->setActiveSheetIndex(0)->mergeCells('G' . ($j) . ':H' . ($j));
    $this->excel->getActiveSheet()->setCellValue('G' . ($j), 'Tổng doanh thu');
    $this->excel->getActiveSheet()->getRowDimension($j)->setRowHeight(18);
    $this->excel->getActiveSheet()->getStyle('I' . ($j))->getFont()->setBold(true)->setSize(12);
    $this->excel->getActiveSheet()->setCellValue('I' . ($j), to_currency_unVND_nomar($total_cost) . ' VNĐ');

    $this->excel->setActiveSheetIndex(0)->mergeCells('G' . ($j + 1) . ':H' . ($j + 1));
    $this->excel->getActiveSheet()->setCellValue('G' . ($j + 1), 'Tổng chiết khấu');
    $this->excel->getActiveSheet()->getRowDimension($j + 1)->setRowHeight(18);
    $this->excel->getActiveSheet()->getStyle('I' . ($j + 1))->getFont()->setBold(true)->setSize(12);
    $this->excel->getActiveSheet()->setCellValue('I' . ($j + 1), to_currency_unVND_nomar($total_discount) . ' VNĐ');

    $this->excel->getActiveSheet()->getStyle('G' . $j . ':G' . ($j + 2))->getFont()->setSize(12);
    $this->excel->getActiveSheet()->getStyle('I' . $j . ':I' . ($j + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
}
$styleThinBlackBorderOutline = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
        ),
    ),
);
/* */
if ($report_type == 'all') {
    $this->excel->getActiveSheet()->getStyle('A7:J' . ($k - 1))->applyFromArray($styleThinBlackBorderOutline);
} else {
    $this->excel->getActiveSheet()->getStyle('A7:I' . ($k - 1))->applyFromArray($styleThinBlackBorderOutline);
}
$this->excel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L&BInvoice&RPrinted on &D');
$this->excel->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&B' . $this->excel->getProperties()->getTitle() . '&RPage &P of &N');
$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
$md5file = md5(date('YmdHis')) . '.xlsx';
$filename = 'Báo cáo chi tiết nhân viên.xlsx'; //save our workbook as this file name
$objWriter->save($filename);
if (file_exists($filename)) {
    header('Content-Type: application/vnd.ms-excel'); //mime type
    header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
    header('Cache-Control: max-age=0'); //no cache    
//    header('Content-Description: File Transfer')
//    header('Content-Type: application/octet-stream')
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