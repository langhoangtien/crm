<?php

$count = count($rows);

$this->load->library('Excel');
$objPHPExcel = new PHPExcel();

$this->excel->getActiveSheet()->setShowGridlines(false);
$this->excel->getDefaultStyle()
        ->getAlignment()
        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->setShowGridlines(false);
$this->excel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Times New Roman');
$this->excel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(9);
$this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$this->excel->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
$this->excel->getActiveSheet()->getPageSetup()->setVerticalCentered(false);
$this->excel->getActiveSheet()->getPageMargins()->setRight(0.15);
$this->excel->getActiveSheet()->getPageMargins()->setLeft(0.2);
$this->excel->getActiveSheet()->getPageMargins()->setTop(0.25);
$this->excel->getActiveSheet()->getPageMargins()->setBOTTOM(0.2);
$this->excel->getActiveSheet()->getPageMargins()->setFOOTER(0.1);
$this->excel->getActiveSheet()->getPageMargins()->setHEADER(0);
$this->excel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTop(array(7, 9));
//activate worksheet number 1
$this->excel->setActiveSheetIndex(0);
//error_reporting(E_ALL);
error_reporting(E_ALL & ~E_NOTICE);
//name the worksheet
$this->excel->getActiveSheet()->setTitle('Báo cáo tồn kho');
/* ten dia chi cong ty ngay thang noi dung */
$this->excel->setActiveSheetIndex(0)->mergeCells('A1:E1');
$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
$this->excel->getActiveSheet()->setCellValue('A1', $data['company']);
$this->excel->getActiveSheet()->setCellValue('A2', $data['address']);
$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->setActiveSheetIndex(0)->mergeCells('A2:E2');
$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setItalic(true);
$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(9);

$this->excel->setActiveSheetIndex(0)->mergeCells('I1:K1');
$this->excel->getActiveSheet()->setCellValue('I1', "Mẫu số S07 - DNN");
$this->excel->getActiveSheet()->getStyle('I1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('I1')->getFont()->setBold(true)->setItalic(true)->setSize(9);
$this->excel->setActiveSheetIndex(0)->mergeCells('I2:K2');
$this->excel->getActiveSheet()->getStyle('I2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('I2')->getFont()->setItalic(true)->setSize(8);
$this->excel->getActiveSheet()->setCellValue('I2', "Ban hành theo QĐ số 48/2006/QĐ - BTC");
$this->excel->getActiveSheet()->getRowDimension(2)->setRowHeight(12);
$this->excel->setActiveSheetIndex(0)->mergeCells('I3:K3');
$this->excel->getActiveSheet()->getStyle('I3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('I3')->getFont()->setItalic(true)->setSize(8);
$this->excel->getActiveSheet()->setCellValue('I3', "Ngày 14 tháng 9 năm 2006");
$this->excel->getActiveSheet()->getRowDimension(3)->setRowHeight(12);
$this->excel->setActiveSheetIndex(0)->mergeCells('A4:L4');
$this->excel->getActiveSheet()->setCellValue('A4', "TỔNG HỢP HÀNG TỒN KHO NGUYÊN VẬT LIỆU");
$this->excel->getActiveSheet()->getStyle('A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A4')->getFont()->setSize(14)->setBold(true);
$this->excel->setActiveSheetIndex(0)->mergeCells('D5:G5');
$objRichTextdate = new PHPExcel_RichText();
$tu = $objRichTextdate->createTextRun('Từ: ')->getFont()->setSize(9)->setName('Times New Roman');
$startdate = $objRichTextdate->createTextRun(date("d/m/Y", strtotime($start_date)))->getFont()->setItalic(true)->setSize(9)->setName('Times New Roman')->setBold(true);
$den = $objRichTextdate->createTextRun(' đến: ')->getFont()->setSize(9);
$enddate = $objRichTextdate->createTextRun(date("d/m/Y", strtotime($end_date)))->getFont()->setItalic(true)->setSize(9)->setName('Times New Roman')->setBold(true);
$this->excel->getActiveSheet()->getCell('D5')->setValue($objRichTextdate);
$this->excel->getActiveSheet()->getStyle('D5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
/* end ten dia chi cong ty ngay thang noi dung */

/* tieu de cac cot */
$this->excel->setActiveSheetIndex(0)->mergeCells('D7:E7');
$this->excel->getActiveSheet()->getStyle('D7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->setCellValue('D7', "Tồn đầu kỳ");
$this->excel->setActiveSheetIndex(0)->mergeCells('F7:I7');
$this->excel->getActiveSheet()->getStyle('F7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->setCellValue('F7', "Phát sinh trong kỳ");
$this->excel->setActiveSheetIndex(0)->mergeCells('J7:K7');
$this->excel->getActiveSheet()->getStyle('J7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->setCellValue('J7', "Tồn cuối kỳ");
$this->excel->setActiveSheetIndex(0)->mergeCells('F8:G8');
$this->excel->getActiveSheet()->getStyle('F8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->setCellValue('F8', "Nhập");
$this->excel->setActiveSheetIndex(0)->mergeCells('H8:I8');
$this->excel->getActiveSheet()->getStyle('H8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->setCellValue('H8', "Xuất");

$this->excel->setActiveSheetIndex(0)->mergeCells('A7:A9');
$this->excel->getActiveSheet()->setCellValue('A7', "Mã VT");
$this->excel->setActiveSheetIndex(0)->mergeCells('B7:B9');
$this->excel->getActiveSheet()->setCellValue('B7', "Tên vật tư");
$this->excel->setActiveSheetIndex(0)->mergeCells('C7:C9');
$this->excel->getActiveSheet()->setCellValue('C7', "Đơn vị tính");
//$this->excel->setActiveSheetIndex(0)->mergeCells('D7:D9');
//$this->excel->getActiveSheet()->setCellValue('D7', "Đơn giá nhập");
//$this->excel->setActiveSheetIndex(0)->mergeCells('E7:E9');
//$this->excel->getActiveSheet()->setCellValue('E7', "Đơn giá xuất");
$this->excel->setActiveSheetIndex(0)->mergeCells('D8:D9');
$this->excel->getActiveSheet()->setCellValue('D8', "SL");
$this->excel->setActiveSheetIndex(0)->mergeCells('E8:E9');
$this->excel->getActiveSheet()->setCellValue('E8', "Thành Tiền");
$this->excel->getActiveSheet()->setCellValue('F9', "SL");
$this->excel->getActiveSheet()->setCellValue('G9', "Thành Tiền");
$this->excel->getActiveSheet()->setCellValue('H9', "SL");
$this->excel->getActiveSheet()->setCellValue('I9', "Thành Tiền");
$this->excel->setActiveSheetIndex(0)->mergeCells('J8:J9');
$this->excel->getActiveSheet()->setCellValue('J8', "SL");
$this->excel->setActiveSheetIndex(0)->mergeCells('K8:K9');
$this->excel->getActiveSheet()->setCellValue('K8', "Thành Tiền");
/* style */
$this->excel->getActiveSheet()->getStyle('A7:K7')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('A7:A9')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('A7:A9')->getBorders()->getLEFT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('B7:B9')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('C7:C9')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('D7:D9')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('E7:E9')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('G7:G9')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('K7:K9')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('D8:D9')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('F7:K7')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('H8:K8')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('A9:K9')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('F8:G8')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('F8:F9')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('E8:E9')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('H9')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('I9')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('J9')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('J8')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('I8')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('A7:K9')->getFont()->setBold(true);

$this->excel->getActiveSheet()->getStyle('A7:E7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('F8:G8')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('F8:G8')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('F7:M7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

$this->excel->getActiveSheet()->getStyle('A7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('C7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('D7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('E7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('F8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('G8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('H9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('I9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('J9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('K9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//$this->excel->getActiveSheet()->getStyle('L8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//$this->excel->getActiveSheet()->getStyle('M8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->getActiveSheet()->getRowDimension(7)->setRowHeight(15.75);
$this->excel->getActiveSheet()->getRowDimension(8)->setRowHeight(12);
$this->excel->getActiveSheet()->getRowDimension(9)->setRowHeight(12);
/* end tieu de cac cot */
$styleThinBlackBorderOutline = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
        ),
    ),
);
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
            'style' => PHPExcel_Style_Border::BORDER_DOTTED,
            'color' => array('argb' => 'FF000000'),
        ),
        'bottom' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
        ),
    ),
);

$k = 10;
$tongcot_dauky_all = 0;
$tongcot_cuoiky_all = 0;
$tongcotnhap_phatsinh_all = 0;
$tongcotxuat_phatsinh_all = 0;
foreach ($categories as $category) {

    $queries = $this->Item->get_item_category2($category['id_cat'], $start_date, $end_date);
    if ($queries != null) {
        $tongtiencot_dauky = 0;
        $tongtiencot_phatsinhnhap = 0;
        $tongtiencot_phatsinhxuat = 0;
        $tongtiencot_cuoiky = 0;

        foreach ($queries as $query) {
            $this->excel->getActiveSheet()->getRowDimension($k)->setRowHeight(20);
            $item_info = $this->Item->get_info($query['trans_items']);
            $this->excel->getActiveSheet()->setCellValue('A' . $k, $item_info->item_number);
            $this->excel->getActiveSheet()->setCellValue('B' . $k, $item_info->name);
            $this->excel->getActiveSheet()->setCellValue('C' . $k, $this->Unit->item_unit($item_info->unit)->name);
//	$this->excel->getActiveSheet()->setCellValue('D' . $k, to_currency_unVND_nomar($item_info->cost_price));
//	$this->excel->getActiveSheet()->setCellValue('E' . $k, to_currency_unVND_nomar($item_info->unit_price));
            $this->excel->getActiveSheet()->getStyle('A' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->excel->getActiveSheet()->getStyle('B' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->excel->getActiveSheet()->getStyle('C' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->excel->getActiveSheet()->getStyle('D' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $this->excel->getActiveSheet()->getStyle('E' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

            //phan dau ky
            $soluong_tondauky = $this->Item->get_item_category_start_number($query['trans_items'], $start_date)->trans_inventory;
            if ($soluong_tondauky != 0) {
                $this->excel->getActiveSheet()->setCellValue('D' . $k, format_quantity($soluong_tondauky));
            } else {
                $this->excel->getActiveSheet()->setCellValue('D' . $k, 0);
            }

            $this->excel->getActiveSheet()->getStyle('D' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

            /////hung audi 16-4
            //nhap ky truoc
            $tien_giuaki_nhaps2 = $this->Item->get_item_category_between_money_nhap2($query['trans_items'], $start_date);
            $tongtien_giuaki_nhap2 = 0;
            if ($tien_giuaki_nhaps2) {
                foreach ($tien_giuaki_nhaps2 as $tien_giuaki_nhap2) {
                    $tongtien_giuaki_nhap2 += $tien_giuaki_nhap2['trans_money'] * $tien_giuaki_nhap2['trans_inventory'];
                }
            }
            $tongtiencot_phatsinhnhap2 += $tongtien_giuaki_nhap2;

            //xuat ky truoc
            $tien_giuaki_xuats2 = $this->Item->get_item_category_between_money_xuat2($query['trans_items'], $start_date);
            $tongtien_giuaki_xuat2 = 0;
            if ($tien_giuaki_xuats2) {
                foreach ($tien_giuaki_xuats2 as $tien_giuaki_xuat2) {
                    $tax_array_item2 = $this->Sale->get_tax_item($tien_giuaki_xuat2['trans_items'], $tien_giuaki_xuat2['trans_sale']); //thue item
                    $tax_item2 = 0;
                    if (count($tax_array_item2) > 0) {
                        foreach ($tax_array_item2 as $tax2) {
                            $tax_item2 = $tax_item2 + $tien_giuaki_xuat2['trans_money'] * $tien_giuaki_xuat2['trans_inventory'] * $tax2['percent'] / 100;
                        }
                    }
                    //thue item_kit
                    $tax_array_item_kit2 = $this->Sale->get_tax_item_kit($tien_giuaki_xuat2['trans_items_kid'], $tien_giuaki_xuat2['trans_sale']);
                    $tax_item_kit2 = 0;
                    if (count($tax_array_item_kit2) > 0) {
                        foreach ($tax_array_item_kit2 as $tax_kid2) {
                            $tax_item_kit2 = $tax_item_kit2 + $tien_giuaki_xuat2['trans_money'] * $tien_giuaki_xuat2['trans_inventory'] * $tax_kid2['percent'] / 100;
                        }
                    }
                    $tongtien_giuaki_xuat2 += $tien_giuaki_xuat2['trans_money'] * $tien_giuaki_xuat2['trans_inventory'];
                }
            }
            $tongtiencot_phatsinhxuat2 += abs($tongtien_giuaki_xuat2);


            $tongtien_dauki = $tongtien_giuaki_nhap2 - abs($tongtien_giuaki_xuat2);



            $tongtiencot_dauky += $tongtien_dauki;
            $this->excel->getActiveSheet()->setCellValue('E' . $k, to_currency_unVND_nomar($tongtien_dauki));
            $this->excel->getActiveSheet()->getStyle('E' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            /* */
            /* */
            //phat sinh trong ky nhap         
            $soluongnhap_phatsinh = $this->Item->get_item_category_between_number_nhap($query['trans_items'], $start_date, $end_date)->trans_inventory;
            if ($soluongnhap_phatsinh != 0) {
                $this->excel->getActiveSheet()->setCellValue('F' . $k, format_quantity($soluongnhap_phatsinh));
            } else {
                $this->excel->getActiveSheet()->setCellValue('F' . $k, 0);
            }


            $this->excel->getActiveSheet()->getStyle('F' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $tien_giuaki_nhaps = $this->Item->get_item_category_between_money_nhap($query['trans_items'], $start_date, $end_date);
            $tongtien_giuaki_nhap = 0;
            if ($tien_giuaki_nhaps != null) {
                foreach ($tien_giuaki_nhaps as $tien_giuaki_nhap) {
                    $tongtien_giuaki_nhap += $tien_giuaki_nhap['trans_money'] * $tien_giuaki_nhap['trans_inventory'];
                }
            } else
                $tongtien_giuaki_nhap = null;

            $tongtiencot_phatsinhnhap += $tongtien_giuaki_nhap;
            $this->excel->getActiveSheet()->setCellValue('G' . $k, to_currency_unVND_nomar($tongtien_giuaki_nhap));
            $this->excel->getActiveSheet()->getStyle('G' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            /* */
            /* */

            //phat sinh trong ky xuat    
            $soluongxuat_phatsinh = abs($this->Item->get_item_category_between_number_xuat($query['trans_items'], $start_date, $end_date)->trans_inventory);
            if ($soluongxuat_phatsinh != 0) {
                $this->excel->getActiveSheet()->setCellValue('H' . $k, format_quantity($soluongxuat_phatsinh));
            } else {
                $this->excel->getActiveSheet()->setCellValue('H' . $k, 0);
            }
            $this->excel->getActiveSheet()->getStyle('H' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);


            $tien_giuaki_xuats = $this->Item->get_item_category_between_money_xuat($query['trans_items'], $start_date, $end_date);
            $tongtien_giuaki_xuat = 0;
            if ($tien_giuaki_xuats) {
                foreach ($tien_giuaki_xuats as $tien_giuaki_xuat) {
                    $tax_array_item = $this->Sale->get_tax_item($tien_giuaki_xuat['trans_items'], $tien_giuaki_xuat['trans_sale']); //thue item
                    $tax_item = 0;
                    if (count($tax_array_item) > 0) {
                        foreach ($tax_array_item as $tax) {
                            $tax_item = $tax_item + $tien_giuaki_xuat['trans_money'] * $tien_giuaki_xuat['trans_inventory'] * $tax['percent'] / 100;
                        }
                    }
                    //thue item_kit
                    $tax_array_item_kit = $this->Sale->get_tax_item_kit($tien_giuaki_xuat['trans_items_kid'], $tien_giuaki_xuat['trans_sale']);
                    $tax_item_kit = 0;
                    if (count($tax_array_item_kit) > 0) {
                        foreach ($tax_array_item_kit as $tax_kid) {
                            $tax_item_kit = $tax_item_kit + $tien_giuaki_xuat['trans_money'] * $tien_giuaki_xuat['trans_inventory'] * $tax_kid['percent'] / 100;
                        }
                    }
                    $tongtien_giuaki_xuat += $tien_giuaki_xuat['trans_money'] * $tien_giuaki_xuat['trans_inventory'];
                }
            }
            $tongtiencot_phatsinhxuat += abs($tongtien_giuaki_xuat);
            $this->excel->getActiveSheet()->setCellValue('I' . $k, to_currency_unVND_nomar(abs($tongtien_giuaki_xuat)));
            $this->excel->getActiveSheet()->getStyle('I' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            /* */
            /* */

            //cuoi ky   
            $soluong_toncuoiky = $soluong_tondauky + ($soluongnhap_phatsinh - $soluongxuat_phatsinh);
            $this->excel->getActiveSheet()->setCellValue('J' . $k, format_quantity($soluong_toncuoiky));
            $this->excel->getActiveSheet()->getStyle('J' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

            $tongtien_cuoiky = $tongtien_dauki + $tongtien_giuaki_nhap - abs($tongtien_giuaki_xuat);
            $tongtiencot_cuoiky += $tongtien_cuoiky;
            $this->excel->getActiveSheet()->setCellValue('K' . $k, to_currency_unVND_nomar($tongtien_cuoiky));


            $this->excel->getActiveSheet()->getStyle('K' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $this->excel->getActiveSheet()->getStyle('A' . $k . ':K' . $k)->applyFromArray($styleDOTBlackBorderOutline);
            $k++;
        }
        $tongcot_dauky_all += $tongtiencot_dauky;
        $tongcotnhap_phatsinh_all += $tongtiencot_phatsinhnhap;
        $tongcotxuat_phatsinh_all += $tongtiencot_phatsinhxuat;
        $tongcot_cuoiky_all += $tongtiencot_cuoiky;

        $this->excel->setActiveSheetIndex(0)->mergeCells('A' . $k . ':D' . $k);
        $this->excel->getActiveSheet()->setCellValue('A' . ($k), $category['name']);
        $this->excel->getActiveSheet()->getStyle('A' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $this->excel->getActiveSheet()->getStyle('A' . $k . ':K' . $k)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('D5D6DA');
        $this->excel->getActiveSheet()->setCellValue('E' . ($k), to_currency_unVND_nomar($tongtiencot_dauky));
        $this->excel->getActiveSheet()->getStyle('F' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $this->excel->getActiveSheet()->setCellValue('G' . ($k), to_currency_unVND_nomar($tongtiencot_phatsinhnhap));
        $this->excel->getActiveSheet()->getStyle('H' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $this->excel->getActiveSheet()->setCellValue('I' . ($k), to_currency_unVND_nomar($tongtiencot_phatsinhxuat));
        $this->excel->getActiveSheet()->getStyle('J' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $this->excel->getActiveSheet()->setCellValue('K' . ($k), to_currency_unVND_nomar($tongtiencot_cuoiky));
        $this->excel->getActiveSheet()->getStyle('K' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $this->excel->getActiveSheet()->getStyle('A' . $k . ':K' . $k)->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A' . $k . ':K' . $k)->applyFromArray($styleDOTBlackBorderTOPBOTOutline);
        $this->excel->getActiveSheet()->getRowDimension($k)->setRowHeight(20);
        $k++;
    }
}
$i = $this->excel->getActiveSheet()->getHighestRow() + 3;
/* style border cot */
$this->excel->getActiveSheet()->getStyle('A10:A' . ($i - 3))->getBorders()->getLEFT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('A10:A' . ($i - 3))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('B10:B' . ($i - 3))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('C10:C' . ($i - 3))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('D10:D' . ($i - 3))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('E10:E' . ($i - 3))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('F10:F' . ($i - 3))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('G10:G' . ($i - 3))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('H10:H' . ($i - 3))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('I10:I' . ($i - 3))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('J10:J' . ($i - 3))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('K10:K' . ($i - 3))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//$this->excel->getActiveSheet()->getStyle('L10:L'.($i-3))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//$this->excel->getActiveSheet()->getStyle('M10:M'.($i-3))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
/* end style border cot */
$this->excel->getActiveSheet()->setCellValue('B' . ($i - 2), 'TỔNG CỘNG');
$this->excel->getActiveSheet()->getStyle('B' . ($i - 2))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B' . ($i - 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getRowDimension($i - 2)->setRowHeight(18);

$this->excel->getActiveSheet()->setCellValue('E' . ($i - 2), to_currency_unVND_nomar($tongcot_dauky_all));
$this->excel->getActiveSheet()->getStyle('E' . ($i - 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$this->excel->getActiveSheet()->getStyle('E' . ($i - 2) . ':J' . ($i - 2))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->setCellValue('G' . ($i - 2), to_currency_unVND_nomar($tongcotnhap_phatsinh_all));
$this->excel->getActiveSheet()->getStyle('G' . ($i - 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$this->excel->getActiveSheet()->setCellValue('I' . ($i - 2), to_currency_unVND_nomar($tongcotxuat_phatsinh_all));
$this->excel->getActiveSheet()->getStyle('I' . ($i - 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$this->excel->getActiveSheet()->setCellValue('K' . ($i - 2), to_currency_unVND_nomar($tongcot_cuoiky_all));
$this->excel->getActiveSheet()->getStyle('I' . ($i - 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$this->excel->getActiveSheet()->getStyle('A' . ($i - 2) . ':K' . ($i - 2))->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('A' . ($i - 2) . ':K' . ($i - 2))->applyFromArray($styleThinBlackBorderOutline);


$this->excel->getActiveSheet()->setCellValue('I' . ($i + 2), 'Ngày ....../....../...............');
$this->excel->getActiveSheet()->getStyle('I' . ($i + 2))->getFont()->setItalic(true);


$this->excel->getActiveSheet()->setCellValue('B' . ($i + 4), 'NGƯỜI LẬP BIỂU');
$this->excel->getActiveSheet()->getStyle('B' . ($i + 4))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B' . ($i + 4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->setActiveSheetIndex()->mergeCells('E' . ($i + 4) . ':F' . ($i + 4));
$this->excel->getActiveSheet()->setCellValue('E' . ($i + 4), 'KẾ TOÁN TRƯỞNG');
$this->excel->getActiveSheet()->getStyle('E' . ($i + 4))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('E' . ($i + 4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->setActiveSheetIndex()->mergeCells('I' . ($i + 4) . ':J' . ($i + 4));
$this->excel->getActiveSheet()->setCellValue('I' . ($i + 4), 'GIÁM ĐỐC');
$this->excel->getActiveSheet()->getStyle('I' . ($i + 4))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('I' . ($i + 4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B' . ($i + 4) . ':L' . ($i + 4))->getFont()->setBold(true);

$this->excel->getActiveSheet()->setCellValue('B' . ($i + 5), '(Ký, ghi họ tên)');
$this->excel->setActiveSheetIndex()->mergeCells('E' . ($i + 5) . ':F' . ($i + 5));
$this->excel->getActiveSheet()->setCellValue('E' . ($i + 5), 'Ký, ghi họ tên');
$this->excel->setActiveSheetIndex()->mergeCells('I' . ($i + 5) . ':J' . ($i + 5));
$this->excel->getActiveSheet()->setCellValue('I' . ($i + 5), '(Ký, ghi họ tên, đóng dấu)');
$this->excel->getActiveSheet()->getStyle('B' . ($i + 5) . ':L' . ($i + 5))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B' . ($i + 5) . ':L' . ($i + 5))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->getActiveSheet()->getHeaderFooter()->setOddFooter('&RPage &P of &N');
$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(13.5);
$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(45);
$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(11.14);
$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(11.86);
$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(11.86);
$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(7.86);
$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(12.1);
$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(8.71);
$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(12.1);
$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(7.86);
$this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(12.14);
//$this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(8.29);
//$this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(12.57);
//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
//if you want to save it as .XLSX Excel 2007 format
$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
$md5file = md5(date('YmdHis')) . '.xlsx';
//$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
//force user to download the Excel file without writing it to server's HD
$filename = 'thongtin_tonkho.xlsx'; //save our workbook as this file name


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