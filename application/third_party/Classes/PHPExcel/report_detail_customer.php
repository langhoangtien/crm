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
$this->excel->getActiveSheet()->setTitle('Báo cáo chi tiết khách hàng');
$this->excel->setActiveSheetIndex(0)->mergeCells('A1:E1');
$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
$this->excel->getActiveSheet()->setCellValue('A1', $data['company']);
$this->excel->getActiveSheet()->setCellValue('A2', $this->config->item('address'));
$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
//$this->excel->getActiveSheet()->setCellValue('A3',$data['full_name']);
$this->excel->setActiveSheetIndex(0)->mergeCells('B4:I4');
$this->excel->getActiveSheet()->setCellValue('B4', "BÁO CÁO CHI TIẾT KHÁCH HÀNG");
$this->excel->getActiveSheet()->getStyle('B4')->getFont()->setSize(14)->setBold(true);
$this->excel->getActiveSheet()->getStyle('B4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->getActiveSheet()->setCellValue('C5',"Từ ngày ".$start_date. ' đến ngày ' . $end_date);
$this->excel->setActiveSheetIndex(0)->mergeCells('C5:H5');
$this->excel->getActiveSheet()->getStyle('C5')->getFont()->setSize(10)->setItalic(true);
$this->excel->getActiveSheet()->getStyle('C5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('C5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->getActiveSheet()->setCellValue('A6',"Tên khách hàng: ".mb_convert_case($customer_info->first_name . ' ' . $customer_info->last_name, MB_CASE_UPPER, "UTF-8"));
$this->excel->setActiveSheetIndex(0)->mergeCells('A6:J6');
$this->excel->getActiveSheet()->getStyle('A6')->getFont()->setSize(10)->setBold(true);
$this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->getActiveSheet()->setCellValue('A7', "Mã ĐH ");
$this->excel->getActiveSheet()->setCellValue('B7', "Ngày ");
$this->excel->getActiveSheet()->setCellValue('C7', "Bán bởi");
$this->excel->getActiveSheet()->setCellValue('D7', "Số lượng");
$this->excel->getActiveSheet()->setCellValue('E7', "Tổng tiền");
$this->excel->getActiveSheet()->setCellValue('F7', "Tổng sau CK");
$this->excel->getActiveSheet()->setCellValue('G7', "Thuế");
$this->excel->getActiveSheet()->setCellValue('H7', "CK tiền mặt");
$this->excel->getActiveSheet()->setCellValue('I7', "Thực thu");
$this->excel->getActiveSheet()->setCellValue('J7', "Ghi chú");
$this->excel->getActiveSheet()->getStyle('A7:J7')->getFont()->setSize(12)->setBold(true);
$this->excel->getActiveSheet()->getStyle('A7:J7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A7:J7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$k = 8;
$total_cost = 0;
$total_discount = 0;
$total_real = 0;
foreach ($data_all_sale as $key=>$val){ 
  foreach($val as $key1=>$val1)
  {
   		$this->excel->getActiveSheet()->setCellValue('A' . $k, 'ĐH '.$key);
	    $this->excel->getActiveSheet()->setCellValue('B' . $k, $val1['date_tam']);
	    $this->excel->getActiveSheet()->setCellValue('C' . $k, $val1['first_name1']);
	    foreach($info_total_sale as $key2=>$val2){
	       if($key2==$key){
	    	$this->excel->getActiveSheet()->setCellValue('D' . $k, $val2['total_item']);
	    	$this->excel->getActiveSheet()->getStyle('C'.$k.':D'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	    	$this->excel->getActiveSheet()->setCellValue('E' . $k, (($key1==0) && ($val1['liability']==0))?to_currency_unVND_nomar($val2['later_cost_price']):'');
	    	$this->excel->getActiveSheet()->setCellValue('F' . $k, ($key1==0)?to_currency_unVND_nomar($val2['total_discount']):'');
	    	$this->excel->getActiveSheet()->getStyle('E'.$k.':F'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	    	if(($key1==0) && ($val1['liability']==0))
	    	{
				$total_cost = $total_cost + $val2['later_cost_price'];
			}
        }}
	    $this->excel->getActiveSheet()->setCellValue('G' . $k, '');  
	    $this->excel->getActiveSheet()->setCellValue('H' . $k, to_currency_unVND_nomar($val1['discount_money']));
	    $this->excel->getActiveSheet()->getStyle('H'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	    $this->excel->getActiveSheet()->setCellValue('I' . $k, to_currency_unVND_nomar($val1['pays_amount']));
	    $this->excel->getActiveSheet()->getStyle('I'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	    $this->excel->getActiveSheet()->setCellValue('J' . $k, $val1['comment']);
	    
	    $total_real = $total_real + $val1['pays_amount'];
	   // print_r($total_real);die();
		$total_discount = $total_discount + $val1['discount_money'];
		
	    $this->excel->getActiveSheet()->getRowDimension($k)->setRowHeight(17.75);
	    //
//	    $this->excel->getActiveSheet()->setCellValue('A'.($k+1), "+");
//	   
//		$this->excel->getActiveSheet()->setCellValue('B'.($k+1), "Mã MH");
//		$this->excel->setActiveSheetIndex(0)->mergeCells('C'.($k+1).':F'.($k+1));
//		$this->excel->getActiveSheet()->setCellValue('C'.($k+1), "Tên mặt hàng");
//		$this->excel->setActiveSheetIndex(0)->mergeCells('G'.($k+1).':H'.($k+1));
//		$this->excel->getActiveSheet()->setCellValue('G'.($k+1), "Số lượng");
//		$this->excel->setActiveSheetIndex(0)->mergeCells('I'.($k+1).':J'.($k+1));
//		$this->excel->getActiveSheet()->setCellValue('I'.($k+1), "Thực thu");
//		$this->excel->getActiveSheet()->getStyle('A'.($k+1).':J'.($k+1))->getFont()->setSize(9)->setBold(true)->setItalic(true);
//		 $this->excel->getActiveSheet()->getStyle('A'.($k+1).':J'.($k+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER);
//		 $this->excel->getActiveSheet()->getRowDimension($k+1)->setRowHeight(15.75);
		 
//		 $stt = $k+1;
//		 foreach($detail_sale as $key3=>$val3){
//         	if($key3==$key){
//                foreach($val3 as $key4=>$val4){
//                  foreach($val4 as $key5=>$val5){
//                  	
// 					$this->excel->getActiveSheet()->setCellValue('B' . $stt, $val5['item_id']);
// 					$this->excel->setActiveSheetIndex(0)->mergeCells('C'.($stt).':F'.($stt));
// 					$this->excel->getActiveSheet()->setCellValue('C' . $stt, $val5['name']);
// 					$this->excel->setActiveSheetIndex(0)->mergeCells('G'.($stt).':H'.($stt));
// 					$this->excel->getActiveSheet()->setCellValue('G' . $stt, $val5['quantity_purchased']);
// 					$this->excel->setActiveSheetIndex(0)->mergeCells('I'.($stt).':J'.($stt));
//                  	$this->excel->getActiveSheet()->setCellValue('I' . $stt, to_currency_unVND_nomar($val5['item_unit_price']));
//                  }
//                }
//         	  }
//         	  $stt++;
//         	}
	    //
   		// $k+=2;
   		$k++;
 	}
}

$this->excel->getActiveSheet()->getRowDimension(1)->setRowHeight(21.75);
$this->excel->getActiveSheet()->getRowDimension(5)->setRowHeight(16);
$this->excel->getActiveSheet()->getRowDimension(6)->setRowHeight(18);
$this->excel->getActiveSheet()->getRowDimension(7)->setRowHeight(28);
$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(11);
$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(19);
$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(21);
$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(11);
$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(18);
$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(18);
$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(11);
$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(14.75);
$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(18);
$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(23);

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

$this->excel->getActiveSheet()->setCellValue('I' . ($j), 'Tổng tiền');
$this->excel->getActiveSheet()->getRowDimension($j)->setRowHeight(18);
$this->excel->getActiveSheet()->getStyle('J' . ($j))->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->setCellValue('J' . ($j), to_currency_unVND_nomar($total_cost) . ' VNĐ');

$this->excel->getActiveSheet()->setCellValue('I' . ($j+1), 'Tổng chiết khấu');
$this->excel->getActiveSheet()->getRowDimension($j+1)->setRowHeight(18);
$this->excel->getActiveSheet()->getStyle('J' . ($j+1))->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->setCellValue('J' . ($j+1), to_currency_unVND_nomar($total_discount) . ' VNĐ');

$this->excel->getActiveSheet()->setCellValue('I' . ($j+2), 'Tổng thực thu');
$this->excel->getActiveSheet()->getRowDimension($j+2)->setRowHeight(18);
$this->excel->getActiveSheet()->getStyle('J' . ($j+2))->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->setCellValue('J' . ($j+2), to_currency_unVND_nomar($total_real) . ' VNĐ');

$this->excel->getActiveSheet()->getStyle('I'.$j.':I'.($j+2))->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('J'.$j.':J'.($j+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$styleThinBlackBorderOutline = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
        ),
    ),
);
/* */
$this->excel->getActiveSheet()->getStyle('A7:J' . ($k - 1))->applyFromArray($styleThinBlackBorderOutline);
//$this->excel->getActiveSheet()->getHeaderFooter()->setOddFooter('&RPage &P of &N')
$this->excel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L&BInvoice&RPrinted on &D');
$this->excel->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&B' . $this->excel->getProperties()->getTitle() . '&RPage &P of &N');
$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
$md5file = md5(date('YmdHis')) . '.xlsx';
$filename = 'Báo cáo chi tiết khách hàng.xlsx'; //save our workbook as this file name
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