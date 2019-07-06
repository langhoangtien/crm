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
$this->excel->getActiveSheet()->getPageMargins()->setLeft(0.75);
$this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$this->excel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTop(array(6,6));
//activate worksheet number 1
$this->excel->setActiveSheetIndex(0);
//error_reporting(E_ALL);
error_reporting(E_ALL & ~E_NOTICE);
//name the worksheet
$this->excel->getActiveSheet()->setTitle('Báo cáo tổng hợp loại mặt hàng');
$this->excel->setActiveSheetIndex(0)->mergeCells('A1:D1');
$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
$this->excel->getActiveSheet()->setCellValue('A1',$data['company']);
$this->excel->getActiveSheet()->setCellValue('A2',$data['address']);
$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $this->excel->setActiveSheetIndex(0)->mergeCells('A4:D4');
    $this->excel->getActiveSheet()->setCellValue('A4', "TỔNG HỢP LOẠI MẶT HÀNG");
    $this->excel->getActiveSheet()->getStyle('A4')->getFont()->setSize(14)->setBold(true);
    $this->excel->getActiveSheet()->getStyle('A4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $this->excel->getActiveSheet()->getStyle('A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


$this->excel->getActiveSheet()->setCellValue('A6', "Loại mặt hàng");
$this->excel->getActiveSheet()->setCellValue('B6', "Tổng tiền hàng");
$this->excel->getActiveSheet()->setCellValue('C6', "Tổng tiền thanh toán");
$this->excel->getActiveSheet()->setCellValue('D6', "Tổng thuế");

$k =7;
$cat_id1 = $this->Sale->get_cat_in_sale_item();
$cat_id2 = $this->Sale->get_cat_in_sale_pack();
$cat_id_tam = array_merge($cat_id1,$cat_id2);
$cat_id = array_unique($cat_id_tam);                              
$data_by_cat1 = $this->Sale->get_sale_item_by_cat_id($start_date, $end_date, $sale_type);
$data_by_cat2 = $this->Sale->get_sale_pack_by_cat_id($start_date, $end_date, $sale_type);
$data_by_cat = array_merge($data_by_cat1,$data_by_cat2);
foreach ($cat_id as $key =>$val){
    $thanh_tien = 0;
    $thue = 0;
    $tong_cong = 0;
    foreach ($data_by_cat as $val1){
        if($val1['cat_id'] == $val['cat_id']){
            if($val1['item_id']){
                if($val1['unit_from']==$val1['unit_item']){
                    $thanh_tien += $val1['unit_price_rate']*$val1['quantity_purchased']- $val1['unit_price_rate']*$val1['quantity_purchased']*$val1['discount_percent']/100;
                    $thue += $val1['unit_price_rate']*$val1['quantity_purchased']*$val1['taxes_percent']/100;
                    $tong_cong += $val1['unit_price_rate']*$val1['quantity_purchased']- $val1['unit_price_rate']*$val1['quantity_purchased']*$val1['discount_percent']/100 + $val1['unit_price_rate']*$val1['quantity_purchased']*$val1['taxes_percent']/100;
                }else{
                     $thanh_tien += $val1['unit_price']*$val1['quantity_purchased']- $val1['unit_price']*$val1['quantity_purchased']*$val1['discount_percent']/100;
                    $thue += $val1['unit_price']*$val1['quantity_purchased']*$val1['taxes_percent']/100;
                    $tong_cong += $val1['unit_price']*$val1['quantity_purchased']- $val1['unit_price']*$val1['quantity_purchased']*$val1['discount_percent']/100 + $val1['unit_price']*$val1['quantity_purchased']*$val1['taxes_percent']/100;
                }                                             
            }else{
                $thanh_tien += $val1['pack_unit_price']*$val1['quantity_purchased'] - $val1['pack_unit_price']*$val1['quantity_purchased']*$val1['discount_percent']/100;
                $tong_cong += $val1['pack_unit_price']*$val1['quantity_purchased'] - $val1['pack_unit_price']*$val1['quantity_purchased']*$val1['discount_percent']/100;
            }
        }
    }
     $total_money += $tong_cong;
     $total_tax_money += $thue;
     $total_thanhtien += $thanh_tien;
    $name = $this->Category->get_info($val['cat_id']);
	  $this->excel->getActiveSheet()->setCellValue('A' . $k, $name->name);
	  $this->excel->getActiveSheet()->setCellValue('B' . $k, to_currency_unVND_nomar($thanh_tien).' VNĐ');
	  $this->excel->getActiveSheet()->setCellValue('C' . $k, to_currency_unVND_nomar($thue).' VNĐ');
	  $this->excel->getActiveSheet()->setCellValue('D' . $k, to_currency_unVND_nomar($tong_cong).' VNĐ');
	  $k++;
 }
$this->excel->getActiveSheet()->getStyle('A6:D6')->getFont()->setSize(12)->setBold(true);
 $this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('C6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('D6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);

    $this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('B6')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('C6')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('D6')->getAlignment()->setWrapText(true);

    // tong hop so lieu 
  $i = $this->excel->getActiveSheet()->getHighestRow() +1;
    $this->excel->getActiveSheet()->getStyle('B'.($i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $this->excel->getActiveSheet()->getStyle('B'.($i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
    $this->excel->getActiveSheet()->getStyle('B'.($i))->getFont()->setBold(true)->setSize(11);
    $this->excel->getActiveSheet()->setCellValue('B'.($i), "Thành tiền: ");
    
    $this->excel->setActiveSheetIndex(0)->mergeCells('C'.($i).':D'.($i));
     $this->excel->getActiveSheet()->getStyle('C'.($i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $this->excel->getActiveSheet()->getStyle('C'.($i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
     $this->excel->getActiveSheet()->setCellValue('C'.($i),to_currency_unVND_nomar($total_thanhtien)."  VNĐ");
     $this->excel->getActiveSheet()->getStyle('C'.($i))->getFont()->setBold(true)->setSize(11);
///
    $this->excel->getActiveSheet()->getStyle('B'.($i+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $this->excel->getActiveSheet()->getStyle('B'.($i+1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
    $this->excel->getActiveSheet()->getStyle('B'.($i+1))->getFont()->setBold(true)->setSize(11);
    $this->excel->getActiveSheet()->setCellValue('B'.($i+1), "Thuế: ");
   
    $this->excel->setActiveSheetIndex(0)->mergeCells('C'.($i+1).':D'.($i+1));
     $this->excel->getActiveSheet()->getStyle('C'.($i+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $this->excel->getActiveSheet()->getStyle('C'.($i+1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
     $this->excel->getActiveSheet()->setCellValue('C'.($i+1),to_currency_unVND_nomar($total_tax_money)."  VNĐ");
     $this->excel->getActiveSheet()->getStyle('C'.($i+1))->getFont()->setBold(true)->setSize(11);
///
    $this->excel->getActiveSheet()->getStyle('B'.($i+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $this->excel->getActiveSheet()->getStyle('B'.($i+2))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
    $this->excel->getActiveSheet()->getStyle('B'.($i+2))->getFont()->setBold(true)->setSize(11);
    $this->excel->getActiveSheet()->setCellValue('B'.($i+2), "Tổng cộng : ");
   
    $this->excel->setActiveSheetIndex(0)->mergeCells('C'.($i+2).':D'.($i+2));
     $this->excel->getActiveSheet()->getStyle('C'.($i+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $this->excel->getActiveSheet()->getStyle('C'.($i+2))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
     $this->excel->getActiveSheet()->setCellValue('C'.($i+2),to_currency_unVND_nomar($total_money)." VNĐ");
     $this->excel->getActiveSheet()->getStyle('C'.($i+2))->getFont()->setBold(true)->setSize(11);


$styleThinBlackBorderOutline = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
            ),
        ),
    );
/* */
$this->excel->getActiveSheet()->getStyle('A6:D' . ($k-1))->applyFromArray($styleThinBlackBorderOutline);
$this->excel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L&BInvoice&RPrinted on &D');
$this->excel->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&B' . $this->excel->getProperties()->getTitle() . '&RPage &P of &N');
//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
//if you want to save it as .XLSX Excel 2007 format
$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $md5file = md5(date('YmdHis')) . '.xlsx';
//$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
//force user to download the Excel file without writing it to server's HD
$filename = 'Báo cáo tổng hợp loại hàng.xlsx'; //save our workbook as this file name
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