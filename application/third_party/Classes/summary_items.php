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
$this->excel->getActiveSheet()->getPageMargins()->setRight(2);
$this->excel->getActiveSheet()->getPageMargins()->setLeft(2);
$this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$this->excel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTop(array(6,6));
//activate worksheet number 1
$this->excel->setActiveSheetIndex(0);
//error_reporting(E_ALL);
error_reporting(E_ALL & ~E_NOTICE);
//name the worksheet
$this->excel->getActiveSheet()->setTitle('Báo cáo tổng hợp mặt hàng');
$this->excel->setActiveSheetIndex(0)->mergeCells('A1:E1');
$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
$this->excel->getActiveSheet()->setCellValue('A1',$data['company']);
$this->excel->getActiveSheet()->setCellValue('A2',$data['address']);
$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $this->excel->setActiveSheetIndex(0)->mergeCells('A4:E4');
    $this->excel->getActiveSheet()->setCellValue('A4', "BÁO CÁO TỔNG HỢP MẶT HÀNG");
    $this->excel->getActiveSheet()->getStyle('A4')->getFont()->setSize(14)->setBold(true);
    $this->excel->getActiveSheet()->getStyle('A4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $this->excel->getActiveSheet()->getStyle('A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->getActiveSheet()->setCellValue('A6', "Mặt hàng ");
$this->excel->getActiveSheet()->setCellValue('B6', "ĐVT");
$this->excel->getActiveSheet()->setCellValue('C6', "Số lượng");
$this->excel->getActiveSheet()->setCellValue('D6', "Số lượng đã bán");
$this->excel->getActiveSheet()->setCellValue('E6', "Thành tiền");
$this->excel->getActiveSheet()->setCellValue('F6', "Thuế");
$this->excel->getActiveSheet()->setCellValue('G6', "Tổng cộng");


$k =7;
 foreach ($data_item as $val){
    if($val['item_id']){
        $item_name = $this->Item->get_info($val['item_id']);
        $quan_total = $item_name->quantity_total;
    }else{
        $item_name = $this->Pack->get_info($val['pack_id']);
        $quan_total = $item_name->total_quantity;
    }
    if($item_type != 4){
        if($item_type != 2){
            $info_item = $this->Sale->get_all_item_id_in_sale_item($start_date, $end_date, $sale_type,$item_type,$val['item_id']);
        }else{
             $info_item = $this->Sale->get_all_pack_id_in_sale_pack($start_date, $end_date, $sale_type,$val['pack_id']);
        }
    }else{                                    
        $info_item1 = $this->Sale->get_all_item_id_in_sale_item($start_date, $end_date, $sale_type,$item_type,$val['item_id']);
        $info_item2 = $this->Sale->get_all_pack_id_in_sale_pack($start_date, $end_date, $sale_type,$val['pack_id']);
        $info_item = array_merge($info_item1,$info_item2);
    }
//                                    echo "<pre>"; print_r($info_item);
    $total_quan = 0;
    $thanh_tien = 0;
    $thue = 0;
    $tong_cong = 0;
    foreach ($info_item as $val1){
        $total_quan += $val1['quantity_purchased'];
        if($val['item_id']){
            if($item_name->quantity_first > 0){
                $thanh_tien += $val1['quantity_purchased']*$val1['item_unit_price_rate'] - $val1['quantity_purchased']*$val1['item_unit_price_rate']*$val1['discount_percent']/100;
                $thue += $val1['quantity_purchased']*$val1['item_unit_price_rate']*$val1['taxes_percent']/100;
                $tong_cong += $val1['quantity_purchased']*$val1['item_unit_price_rate'] - $val1['quantity_purchased']*$val1['item_unit_price_rate']*$val1['discount_percent']/100 + $val1['quantity_purchased']*$val1['item_unit_price_rate']*$val1['taxes_percent']/100;
            }else{
                $thanh_tien += $val1['quantity_purchased']*$val1['item_unit_price'] - $val1['quantity_purchased']*$val1['item_unit_price']*$val1['discount_percent']/100;
                $thue += $val1['quantity_purchased']*$val1['item_unit_price']*$val1['taxes_percent']/100;
                $tong_cong += $val1['quantity_purchased']*$val1['item_unit_price'] - $val1['quantity_purchased']*$val1['item_unit_price']*$val1['discount_percent']/100 + $val1['quantity_purchased']*$val1['item_unit_price']*$val1['taxes_percent']/100;
            }
        }else{
            $thanh_tien += $val1['pack_unit_price']*$val1['quantity_purchased']-$val1['pack_unit_price']*$val1['quantity_purchased']*$val1['discount_percent']/100;
             $tong_cong += $val1['pack_unit_price']*$val1['quantity_purchased']-$val1['pack_unit_price']*$val1['quantity_purchased']*$val1['discount_percent']/100;
        }
    }
    $tong_thanh_tien += $thanh_tien;
    $tong_thue += $thue;
    $tong_cong_tien += $tong_cong;
	  $this->excel->getActiveSheet()->setCellValue('A' . $k, $item_name->name);
          $this->excel->getActiveSheet()->getStyle('A'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
          if($item_name->unit_from){
                $this->excel->getActiveSheet()->setCellValue('B' . $k, $this->Unit->get_info($item_name->unit)->name.'-'.$this->Unit->get_info($item_name->unit_from)->name);
                $this->excel->getActiveSheet()->getStyle('B'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
          }  else {
                $this->excel->getActiveSheet()->setCellValue('B' . $k, $this->Unit->get_info($item_name->unit)->name);
                $this->excel->getActiveSheet()->getStyle('B'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
          }
            $this->excel->getActiveSheet()->setCellValue('C' . $k, format_quantity($quan_total));
            $this->excel->getActiveSheet()->getStyle('C'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $this->excel->getActiveSheet()->setCellValue('D' . $k, format_quantity($total_quan));
            $this->excel->getActiveSheet()->getStyle('D'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $this->excel->getActiveSheet()->setCellValue('E' . $k, number_format($thanh_tien));
            $this->excel->getActiveSheet()->getStyle('E'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $this->excel->getActiveSheet()->setCellValue('F' . $k, number_format($thue));
            $this->excel->getActiveSheet()->getStyle('F'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $this->excel->getActiveSheet()->setCellValue('G' . $k, number_format($tong_cong));
            $this->excel->getActiveSheet()->getStyle('G'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $k++;
 }
$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);

$this->excel->getActiveSheet()->getStyle('A6:G6')->getFont()->setSize(12)->setBold(true);
 $this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('C6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('D6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('E6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('F6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('G6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(40);
$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);

    $this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('B6')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('C6')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('D6')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('E6')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('F6')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('G6')->getAlignment()->setWrapText(true);

$j = $this->excel->getActiveSheet()->getHighestRow() + 1;

$this->excel->setActiveSheetIndex(0)->mergeCells('E'.($j).':F'.($j));
$this->excel->getActiveSheet()->setCellValue('E'.($j), 'Thành tiền');
$this->excel->getActiveSheet()->getRowDimension($j)->setRowHeight(18);
$this->excel->getActiveSheet()->getStyle('E'.($j))->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->setCellValue('G'.($j), number_format($tong_thanh_tien) . ' VNĐ');
$this->excel->getActiveSheet()->getStyle('G'.($j))->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('G'.($j))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$this->excel->setActiveSheetIndex(0)->mergeCells('E'.($j+1).':F'.($j+1));
$this->excel->getActiveSheet()->setCellValue('E'.($j+1), 'Thuế');
$this->excel->getActiveSheet()->getRowDimension($j+1)->setRowHeight(18);
$this->excel->getActiveSheet()->getStyle('E'.($j+1))->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->setCellValue('G' . ($j+1), number_format($tong_thue) . ' VNĐ');
$this->excel->getActiveSheet()->getStyle('G'.($j+1))->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('G'.($j+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$this->excel->setActiveSheetIndex(0)->mergeCells('E'.($j+2).':F'.($j+2));
$this->excel->getActiveSheet()->setCellValue('E'.($j+2), 'Tổng cộng');
$this->excel->getActiveSheet()->getRowDimension($j+2)->setRowHeight(18);
$this->excel->getActiveSheet()->getStyle('E'.($j+2))->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->setCellValue('G' . ($j+2), number_format($tong_cong_tien) . ' VNĐ');
$this->excel->getActiveSheet()->getStyle('G'.($j+2))->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('G'.($j+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

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