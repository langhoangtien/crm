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
$this->excel->getActiveSheet()->setCellValue('B4', "BÁO CÁO CHI TIẾT CÔNG NỢ KHÁCH HÀNG");
$this->excel->getActiveSheet()->getStyle('B4')->getFont()->setSize(14)->setBold(true);
$this->excel->getActiveSheet()->getStyle('B4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->getActiveSheet()->setCellValue('C5','Từ '.date('d-m-Y H:i:s', strtotime($start_date)) .' đến '.date('d-m-Y H:i:s', strtotime($end_date)));
$this->excel->setActiveSheetIndex(0)->mergeCells('C5:H5');
$this->excel->getActiveSheet()->getStyle('C5')->getFont()->setSize(10)->setItalic(true);
$this->excel->getActiveSheet()->getStyle('C5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('C5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->getActiveSheet()->setCellValue('A6',"Tên khách hàng: ".mb_convert_case($customer_info->first_name . ' ' . $customer_info->last_name, MB_CASE_UPPER, "UTF-8"));
$this->excel->setActiveSheetIndex(0)->mergeCells('A6:J6');
$this->excel->getActiveSheet()->getStyle('A6')->getFont()->setSize(10)->setBold(true);
$this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);


$this->excel->setActiveSheetIndex(0)->mergeCells('A7:A8');
$this->excel->getActiveSheet()->setCellValue('A7', "Ngày ");

$this->excel->setActiveSheetIndex(0)->mergeCells('B7:B8');
$this->excel->getActiveSheet()->setCellValue('B7', "Số");

$this->excel->setActiveSheetIndex(0)->mergeCells('C7:C8');
$this->excel->getActiveSheet()->setCellValue('C7', "Diễn giải");

$this->excel->setActiveSheetIndex(0)->mergeCells('D7:D8');
$this->excel->getActiveSheet()->setCellValue('D7', "ĐVT");

$this->excel->setActiveSheetIndex(0)->mergeCells('E7:E8');
$this->excel->getActiveSheet()->setCellValue('E7', "Số lượng");

$this->excel->setActiveSheetIndex(0)->mergeCells('F7:F8');
$this->excel->getActiveSheet()->setCellValue('F7', "Đơn giá");

$this->excel->setActiveSheetIndex(0)->mergeCells('G7:H7');
$this->excel->getActiveSheet()->setCellValue('G7', "Thành tiền");
$this->excel->getActiveSheet()->setCellValue('G8', "Nợ");
$this->excel->getActiveSheet()->setCellValue('H8', "Đã thanh toán");

$this->excel->setActiveSheetIndex(0)->mergeCells('I7:I8');
$this->excel->getActiveSheet()->setCellValue('I7', "Còn nợ");

$this->excel->getActiveSheet()->getStyle('A7:I7')->getFont()->setSize(12)->setBold(true);
$this->excel->getActiveSheet()->getStyle('G8:H8')->getFont()->setSize(11)->setBold(true)->setItalic(true);
$this->excel->getActiveSheet()->getStyle('A7:I7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A7:I7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('G8:H8')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('G8:H8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$k = 9;

$total_quantity = 0;
$total_owe = 0;
$total_pay = 0;
$total_one_finnaly = 0;

foreach($data_array_sale_tam as $val)
{                   		
	foreach($detail_sale as $key2=>$val2){
	   if($key2==$val['id_sale']){
	    $total_cost = 0;
   		$this->excel->getActiveSheet()->setCellValue('A' . $k, date('d-m-Y',strtotime($val['date_tam'])));
   		
   		$this->excel->setActiveSheetIndex(0)->mergeCells('B'.$k.':G'.$k);
	    $this->excel->getActiveSheet()->setCellValue('B' . $k, $val['id_sale']);
	    $this->excel->getActiveSheet()->setCellValue('I' . $k, to_currency_unVND_nomar($info_total_sale[$key2]['later_cost_price']));
	    $this->excel->getActiveSheet()->getRowDimension($k)->setRowHeight(17.75);
	    $this->excel->getActiveSheet()->getStyle('A'.$k.':I'.($k))->getFont()->setSize(11)->setBold(true);
	    $total_owe = $total_owe + $info_total_sale[$key2]['later_cost_price'];
	    foreach($val2 as $key3=>$val3){
	    	foreach($val3 as $key4=>$val4){
                    if ($val4['unit_from']) {
                        if ($val4['unit_from'] == $val4['unit_item']) {
                            $unit = $this->Unit->get_info($val4['unit_from'])->name;
                            $item_unit_price = $val4['item_unit_price_rate'];
                        } else {
                            $unit = $this->Unit->get_info($val4['unit'])->name;
                            $item_unit_price = $val4['item_unit_price'];
                        }
                    } else {
                        $unit = $this->Unit->get_info($val4['unit'])->name;
                        $item_unit_price = $val4['item_unit_price'];
                    }
//	    		$k++;
//	    		$i=$k+1;
	    		$this->excel->getActiveSheet()->setCellValue('A' . ($k + 1), '');
	    		$this->excel->getActiveSheet()->setCellValue('B' . ($k + 1), '');
	    		$this->excel->getActiveSheet()->setCellValue('C' . ($k + 1),$val4['name']);
                        $this->excel->getActiveSheet()->setCellValue('D' . ($k + 1),$unit);
	    		$this->excel->getActiveSheet()->setCellValue('E' . ($k + 1), format_quantity($val4['quantity_purchased']));
	    		$this->excel->getActiveSheet()->setCellValue('F' . ($k + 1),to_currency_unVND_nomar($item_unit_price-($item_unit_price*$val4['discount_percent']/100)));
                        $taxes = ($val4['quantity_purchased'] * ($val4['item_unit_price'] - ($val4['item_unit_price'] * $val4['discount_percent'] / 100))) * $val4['taxes_percent'] / 100;
	    		$this->excel->getActiveSheet()->setCellValue('G' . ($k + 1),to_currency_unVND_nomar($val4['quantity_purchased']*($item_unit_price-($item_unit_price*$val4['discount_percent']/100)) + $taxes));
	    		$total_cost = $info_total_sale[$key2]['later_cost_price'];
		        $total_quantity = $total_quantity + $val4['quantity_purchased'];
		        
	    		$this->excel->getActiveSheet()->setCellValue('H' . ($k + 1), '');
	    		$this->excel->getActiveSheet()->setCellValue('I' . ($k + 1), '');
	    		$k++;
	    	}	
	    	 	
	    }
	   unset($detail_sale[$key2]);
	   $k=$k+2;   
   		
 		}
 		
	}
//		$k++;
//		$k++;
//		$j=$k+1;
//		$i=$j+1;
		$this->excel->getActiveSheet()->setCellValue('A' . $k, date('d-m-Y',strtotime($val['date_tam'])));
   		
   		$this->excel->setActiveSheetIndex(0)->mergeCells('B'.$k.':I'.$k);
	    $this->excel->getActiveSheet()->setCellValue('B' . $k, 'PT'.$val['id_sale'].'VL');
	    $this->excel->getActiveSheet()->getRowDimension($k)->setRowHeight(17.75);
	    $this->excel->getActiveSheet()->getStyle('A'.$k.':I'.($k))->getFont()->setSize(11)->setBold(true);
	    
	    
	    $this->excel->getActiveSheet()->setCellValue('A' . ($k+1), '');
	    $this->excel->getActiveSheet()->setCellValue('B' . ($k+1), '');
	    $this->excel->getActiveSheet()->setCellValue('C' . ($k+1),'Thu tiền theo HĐ số '.$val['id_sale']);
	  	$this->excel->getActiveSheet()->setCellValue('D' . ($k+1),'' );
	    $this->excel->getActiveSheet()->setCellValue('E' . ($k+1),'');
             $this->excel->getActiveSheet()->setCellValue('F' . ($k+1),'');
	    $this->excel->getActiveSheet()->setCellValue('G' . ($k+1),'' );
	    $this->excel->getActiveSheet()->setCellValue('H' . ($k+1),to_currency_unVND_nomar($val['pays_amount']+$val['discount_money']));
	    $total_cost = $total_cost - ($val['pays_amount']+$val['discount_money']);
	    $_pay = $val['pays_amount']+$val['discount_money'];
        $total_pay = $total_pay + $_pay;
	    $this->excel->getActiveSheet()->setCellValue('I' . ($k+1),to_currency_unVND_nomar($total_cost));
//	    $j++;$k++;$i++;
		$k = $k +2;
}

$this->excel->getActiveSheet()->getRowDimension(1)->setRowHeight(21.75);
$this->excel->getActiveSheet()->getRowDimension(5)->setRowHeight(16);
$this->excel->getActiveSheet()->getRowDimension(6)->setRowHeight(18);
$this->excel->getActiveSheet()->getRowDimension(7)->setRowHeight(28);
$this->excel->getActiveSheet()->getRowDimension(8)->setRowHeight(18);
$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(19);
$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(11);
$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(33);
$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(11);
$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(16);
$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(17);
$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(17);
$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(17);
$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(17);
$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(33);


$this->excel->getActiveSheet()->getStyle('A7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('C7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('D7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('E7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('F7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('G7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('H7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('I7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


$this->excel->getActiveSheet()->getStyle('A7')->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getStyle('B7')->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getStyle('C7')->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getStyle('D7')->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getStyle('E7')->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getStyle('F7')->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getStyle('G7')->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getStyle('H7')->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getStyle('I7')->getAlignment()->setWrapText(true);




$j = $this->excel->getActiveSheet()->getHighestRow() + 1;

$this->excel->getActiveSheet()->setCellValue('H' . ($j), 'Tổng nợ');
$this->excel->getActiveSheet()->getRowDimension($j)->setRowHeight(18);
$this->excel->getActiveSheet()->getStyle('I' . ($j))->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->setCellValue('I' . ($j), to_currency_unVND_nomar($total_owe) . ' VNĐ');
//
$this->excel->getActiveSheet()->setCellValue('H' . ($j+1), 'Tổng đã TT');
$this->excel->getActiveSheet()->getRowDimension($j+1)->setRowHeight(18);
$this->excel->getActiveSheet()->getStyle('I' . ($j+1))->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->setCellValue('I' . ($j+1), to_currency_unVND_nomar($total_pay) . ' VNĐ');
//
$this->excel->getActiveSheet()->setCellValue('H' . ($j+2), 'Còn nợ');
$this->excel->getActiveSheet()->getRowDimension($j+2)->setRowHeight(18);
$this->excel->getActiveSheet()->getStyle('I' . ($j+2))->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->setCellValue('I' . ($j+2), to_currency_unVND_nomar($total_owe - $total_pay) . ' VNĐ');
//
$this->excel->getActiveSheet()->getStyle('H'.$j.':H'.($j+2))->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('I'.$j.':I'.($j+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$styleThinBlackBorderOutline = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
        ),
    ),
);
/* */
$this->excel->getActiveSheet()->getStyle('A7:I' . ($k-1))->applyFromArray($styleThinBlackBorderOutline);
//$this->excel->getActiveSheet()->getHeaderFooter()->setOddFooter('&RPage &P of &N')
$this->excel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L&BInvoice&RPrinted on &D');
$this->excel->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&B' . $this->excel->getProperties()->getTitle() . '&RPage &P of &N');
$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
$md5file = md5(date('YmdHis')) . '.xlsx';
$filename = 'Báo cáo chi tiết công nợ khách hàng.xlsx'; //save our workbook as this file name
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