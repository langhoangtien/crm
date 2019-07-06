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
$this->excel->getActiveSheet()->getPageMargins()->setLeft(1);
$this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$this->excel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTop(array(6,6));
//activate worksheet number 1
$this->excel->setActiveSheetIndex(0);
//error_reporting(E_ALL);
error_reporting(E_ALL & ~E_NOTICE);
//name the worksheet
$this->excel->getActiveSheet()->setTitle('Báo cáo tổng hợp đơn hàng');
$this->excel->setActiveSheetIndex(0)->mergeCells('A1:D1');
$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
$this->excel->getActiveSheet()->setCellValue('A1',$data['company']);
$this->excel->getActiveSheet()->setCellValue('A2',$data['address']);
$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $this->excel->setActiveSheetIndex(0)->mergeCells('A4:D4');
    $this->excel->getActiveSheet()->setCellValue('A4', "BÁO CÁO TỔNG HỢP ĐƠN HÀNG");
    $this->excel->getActiveSheet()->getStyle('A4')->getFont()->setSize(14)->setBold(true);
    $this->excel->getActiveSheet()->getStyle('A4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $this->excel->getActiveSheet()->getStyle('A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


/*$row =2;
$col =0;
foreach($header as $key => $rows)
{
$this->excel->getActiveSheet()->setCellValueByColumnAndRow($col,$row,$rows['data']);

$col++;	
}*/
$this->excel->getActiveSheet()->setCellValue('A6', "Ngày ");
$this->excel->getActiveSheet()->setCellValue('B6', "Thành tiền");
$this->excel->getActiveSheet()->setCellValue('C6', "Thuế");
$this->excel->getActiveSheet()->setCellValue('D6', "Tổng cộng");
$k =7;
 foreach ($dates as $date){ 
    $thanh_tien = 0;
    $thue = 0;
    $tong_cong = 0;
    $cktm = 0;
    foreach ($datas as $data){
        if(date('d-m-Y',strtotime($data['sale_time']))== $date){
            if($data['unit_from']){
                if($data['unit_from'] == $data['unit_item']){
                    $thanh_tien += ($data['item_unit_price_rate']*$data['quantity_purchased'])-($data['item_unit_price_rate']*$data['quantity_purchased']*$data['discount_percent']/100);
                    $thue += ($data['item_unit_price_rate']*$data['quantity_purchased']-($data['item_unit_price_rate']*$data['quantity_purchased']*$data['discount_percent']/100))*$data['taxes_percent']/100;
                    $cktm += $data['discount_money2'];
                    $tong_cong += ($data['item_unit_price_rate']*$data['quantity_purchased'])-($data['item_unit_price_rate']*$data['quantity_purchased']*$data['discount_percent']/100) + ($data['item_unit_price_rate']*$data['quantity_purchased']-($data['item_unit_price_rate']*$data['quantity_purchased']*$data['discount_percent']/100))*$data['taxes_percent']/100 - $data['discount_money2'];
                }else{
                    $thanh_tien += $data['item_unit_price']*$data['quantity_purchased']-($data['item_unit_price']*$data['quantity_purchased']*$data['discount_percent']/100);
                    $thue += ($data['item_unit_price']*$data['quantity_purchased']-($data['item_unit_price']*$data['quantity_purchased']*$data['discount_percent']/100))*$data['taxes_percent']/100;
                    $cktm += $data['discount_money2'];
                    $tong_cong += ($data['item_unit_price']*$data['quantity_purchased'])-($data['item_unit_price']*$data['quantity_purchased']*$data['discount_percent']/100) +($data['item_unit_price']*$data['quantity_purchased']-($data['item_unit_price']*$data['quantity_purchased']*$data['discount_percent']/100))*$data['taxes_percent']/100 - $data['discount_money2'];
                }
            }else{
                $thanh_tien += ($data['item_unit_price']*$data['quantity_purchased'])-($data['item_unit_price']*$data['quantity_purchased']*$data['discount_percent']/100);
                $thue += ($data['item_unit_price']*$data['quantity_purchased']-($data['item_unit_price']*$data['quantity_purchased']*$data['discount_percent']/100))*$data['taxes_percent']/100;
                $cktm += $data['discount_money2'];
                $tong_cong += ($data['item_unit_price']*$data['quantity_purchased'])-($data['item_unit_price']*$data['quantity_purchased']*$data['discount_percent']/100) +($data['item_unit_price']*$data['quantity_purchased']-($data['item_unit_price']*$data['quantity_purchased']*$data['discount_percent']/100))*$data['taxes_percent']/100 - $data['discount_money2'];
            }
        }
    }
    $tong_thanh_tien += $thanh_tien;
    $tong_thue += $thue;
    $tong_cktm += $cktm;
    $tong_cong_tien += $tong_cong;
	  $this->excel->getActiveSheet()->setCellValue('A' . $k, $date);
	  $this->excel->getActiveSheet()->setCellValue('B' . $k, to_currency_unVND_nomar($thanh_tien).' VNĐ');
	  $this->excel->getActiveSheet()->setCellValue('C' . $k, to_currency_unVND_nomar($thue).' VNĐ');
	  $this->excel->getActiveSheet()->setCellValue('D' . $k,  to_currency_unVND_nomar($tong_cong).' VNĐ');
          $this->excel->getActiveSheet()->getStyle($k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	  $k++;
	  $this->excel->getActiveSheet()->getRowDimension($k)->setRowHeight(15.75);
 }
 
$this->excel->getActiveSheet()->getStyle('A6:E6')->getFont()->setSize(12)->setBold(true);
 $this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('C6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('D6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);

    $this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('B6')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('C6')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('D6')->getAlignment()->setWrapText(true);
         

$styleThinBlackBorderOutline = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
            ),
        ),
    );
/* */
$j = $this->excel->getActiveSheet()->getHighestRow(); 
$this->excel->setActiveSheetIndex(0)->mergeCells('C'.($j).':C'.($j));
$this->excel->getActiveSheet()->setCellValue('C'.($j), 'Tổng thành tiền: ');
$this->excel->getActiveSheet()->getRowDimension($j)->setRowHeight(14);
$this->excel->getActiveSheet()->getStyle('D'.($j))->getFont()->setBold(true)->setSize(10);
$this->excel->getActiveSheet()->setCellValue('D'.($j), to_currency_unVND_nomar($tong_thanh_tien));

$this->excel->setActiveSheetIndex(0)->mergeCells('C'.($j+1).':C'.($j+1));
$this->excel->getActiveSheet()->setCellValue('C'.($j+1), 'Tổng thuế');
$this->excel->getActiveSheet()->getRowDimension($j+1)->setRowHeight(14);
$this->excel->getActiveSheet()->getStyle('D'.($j+1))->getFont()->setBold(true)->setSize(10);
$this->excel->getActiveSheet()->setCellValue('D'.($j+1), to_currency_unVND_nomar($tong_thue));
$this->excel->getActiveSheet()->getStyle('C'.($j+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->setActiveSheetIndex(0)->mergeCells('C'.($j+2).':C'.($j+2));
$this->excel->getActiveSheet()->setCellValue('C'.($j+2), 'Tổng cộng');
$this->excel->getActiveSheet()->getRowDimension($j+2)->setRowHeight(14);
$this->excel->getActiveSheet()->getStyle('D'.($j+2))->getFont()->setBold(true)->setSize(10);
$this->excel->getActiveSheet()->setCellValue('D'.($j+2), to_currency_unVND_nomar($tong_cong_tien));

$this->excel->getActiveSheet()->getStyle('C'.($j).':D'.($j+2))->getFont()->setSize(10)->setBold(true);
$this->excel->getActiveSheet()->getStyle('A6:D' . ($k-1))->applyFromArray($styleThinBlackBorderOutline);
$this->excel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L&BInvoice&RPrinted on &D');
$this->excel->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&B' . $this->excel->getProperties()->getTitle() . '&RPage &P of &N');
//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
//if you want to save it as .XLSX Excel 2007 format
$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $md5file = md5(date('YmdHis')) . '.xlsx';
//$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
//force user to download the Excel file without writing it to server's HD
$filename = 'Báo cáo tổng hợp đơn hàng.xlsx'; //save our workbook as this file name
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