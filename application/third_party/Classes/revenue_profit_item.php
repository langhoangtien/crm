<?php
//$count = count($data);
$this->load->library('Excel');
$objPHPExcel = new PHPExcel();
$this->excel->getDefaultStyle()
    ->getAlignment()
    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->setShowGridlines(true);
$this->excel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Times New Roman');
$this->excel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(9);
$this->excel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
$this->excel->getActiveSheet()->getPageMargins()->setRight(2);
$this->excel->getActiveSheet()->getPageMargins()->setLeft(2);
$this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$this->excel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTop(array(6,6));
//activate worksheet number 1
$this->excel->setActiveSheetIndex(0);
//error_reporting(E_ALL);
error_reporting(E_ALL & ~E_NOTICE);
//name the worksheet
$this->excel->getActiveSheet()->setTitle('Báo cáo doanh thu lợi nhuận');
$this->excel->setActiveSheetIndex(0)->mergeCells('A1:E1');
$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
$this->excel->getActiveSheet()->setCellValue('A1',$data['company']);
$this->excel->getActiveSheet()->setCellValue('A2',$data['address']);
$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $this->excel->setActiveSheetIndex(0)->mergeCells('A4:G4');
    $this->excel->getActiveSheet()->setCellValue('A4', "BÁO CÁO DOANH THU LỢI NHUẬN THEO MẶT HÀNG");
    $this->excel->getActiveSheet()->getStyle('A4')->getFont()->setSize(14)->setBold(true);
    $this->excel->getActiveSheet()->getStyle('A4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $this->excel->getActiveSheet()->getStyle('A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->getActiveSheet()->setCellValue('A6', "STT");
$this->excel->getActiveSheet()->setCellValue('B6', "Mã mặt hàng");
$this->excel->getActiveSheet()->setCellValue('C6', "Tên mặt hàng");
$this->excel->getActiveSheet()->setCellValue('D6', "ĐVT");
$this->excel->getActiveSheet()->setCellValue('E6', "Số lượng");
$this->excel->getActiveSheet()->setCellValue('F6', "Doanh thu");
$this->excel->getActiveSheet()->setCellValue('G6', "Lợi nhuận");

$this->load->model('Inventory');
$this->load->model('Item');
$this->load->model('Unit');

$k =7;
$i=1;
$gia_nhap_trung_binh = 0;
$gia_ban_trung_binh = 0;
$doanh_thu = 0;
$loi_nhuan = 0;
foreach ($data_item as $item){
    $quantity_sale = 0;
    $quantity_recv = 0;
    $price_recv = 0;
    $price_sale = 0;
    $dis_per = 0;
    $tax_per = 0;
    $term_nhap = $this->Inventory->get_price_receving($item['trans_items']);
    foreach ($term_nhap as $term1){
        $quantity_recv += $term1['trans_inventory'];
        $price_recv  += $term1['trans_inventory']*$term1['trans_money'];
    }
    $gia_nhap_trung_binh = $price_recv/$quantity_recv;
    $term_ban = $this->Inventory->get_price_sale($start_date,$end_date,$item['trans_items']);
    foreach ($term_ban as $term2){
        $quantity_sale += $term2['trans_inventory']*(-1);
        $price_sale += $term2['trans_inventory']*$term2['trans_money']*(-1);
        $tam_data = $this->Sale->get_sale_item_by_trans_sale($item['trans_items'],$term2['trans_sale']);
        $dis_per += $term2['trans_inventory']*$term2['trans_money']*$tam_data['discount_percent']/100*(-1);
        $tax_per += ($term2['trans_inventory']*$term2['trans_money']*(-1)-$term2['trans_inventory']*$term2['trans_money']*$tam_data['discount_percent']/100*(-1))*$tam_data['taxes_percent']/100;
    }
    $gia_ban_trung_binh = $price_sale/$quantity_sale;
    $doanh_thu = $price_sale-$dis_per+$tax_per;
    $loi_nhuan = ($gia_ban_trung_binh-$gia_nhap_trung_binh)*$quantity_sale-$dis_per+$tax_per;
    $tong_doanh_thu += $doanh_thu;
    $tong_loi_nhuan += $loi_nhuan;
    $tong_so_luong += $quantity_sale;
    
    if($term_ban){
        $this->excel->getActiveSheet()->setCellValue('A' . $k, $i);
        $this->excel->getActiveSheet()->getStyle('A'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);         
        $this->excel->getActiveSheet()->setCellValue('B' . $k, $this->Item->get_info($item['trans_items'])->item_number);
        $this->excel->getActiveSheet()->getStyle('B'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $this->excel->getActiveSheet()->setCellValue('C' . $k, $this->Item->get_info($item['trans_items'])->name);
        $this->excel->getActiveSheet()->getStyle('C'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        if($this->Item->get_info($item['trans_items'])->quantity_first){
            $unit = $this->Item->get_info($item['trans_items'])->unit_from;
        }else{
            $unit = $this->Item->get_info($item['trans_items'])->unit;
        }
        $this->excel->getActiveSheet()->setCellValue('D' . $k, $this->Unit->get_info($unit)->name);
        $this->excel->getActiveSheet()->getStyle('D'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $this->excel->getActiveSheet()->setCellValue('E' . $k, format_quantity($quantity_sale));
        $this->excel->getActiveSheet()->getStyle('E'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $this->excel->getActiveSheet()->setCellValue('F' . $k, number_format($doanh_thu));
        $this->excel->getActiveSheet()->getStyle('F'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $this->excel->getActiveSheet()->setCellValue('G' . $k, number_format($loi_nhuan));
        $this->excel->getActiveSheet()->getStyle('G'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $k++;
        $i++;
    }
 }
$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(25);

$this->excel->getActiveSheet()->getStyle('A6:G6')->getFont()->setSize(14)->setBold(true);
 $this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('C6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('D6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('E6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('F6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('G6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(25);

    $this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('B6')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('C6')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('D6')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('E6')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('F6')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('G6')->getAlignment()->setWrapText(true);

$j = $this->excel->getActiveSheet()->getHighestRow() + 1;

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($j).':D'.($j));
$this->excel->getActiveSheet()->setCellValue('A'.($j), 'TỔNG');
$this->excel->getActiveSheet()->getRowDimension($j)->setRowHeight(18);
$this->excel->getActiveSheet()->getStyle('A'.($j))->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A'.($j))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$this->excel->getActiveSheet()->setCellValue('E'.($j), format_quantity($tong_so_luong));
$this->excel->getActiveSheet()->getStyle('E'.($j))->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('E'.($j))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$this->excel->getActiveSheet()->setCellValue('F' . ($j), number_format($tong_doanh_thu));
$this->excel->getActiveSheet()->getStyle('F'.($j))->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('F'.($j))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$this->excel->getActiveSheet()->setCellValue('G' . ($j), number_format($tong_loi_nhuan));
$this->excel->getActiveSheet()->getStyle('G'.($j))->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('G'.($j))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$styleThinBlackBorderOutline = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
            ),
        ),
    );
/* */
$this->excel->getActiveSheet()->getStyle('A6:G' . ($k-1))->applyFromArray($styleThinBlackBorderOutline);
$this->excel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L&BInvoice&RPrinted on &D');
$this->excel->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&B' . $this->excel->getProperties()->getTitle() . '&RPage &P of &N');
//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
//if you want to save it as .XLSX Excel 2007 format



$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $md5file = md5(date('YmdHis')) . '.xlsx';
$filename = 'Báo cáo tổng hợp mặt hàng.xlsx'; //save our workbook as this file name
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