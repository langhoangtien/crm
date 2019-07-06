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
$this->excel->getActiveSheet()->setTitle('Báo cáo chi tiết dơn hàng');
$this->excel->setActiveSheetIndex(0)->mergeCells('A1:E1');
$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
$this->excel->getActiveSheet()->setCellValue('A1', $data['company']);
$this->excel->getActiveSheet()->setCellValue('A2', $this->config->item('address'));
$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
//$this->excel->getActiveSheet()->setCellValue('A3',$data['full_name']);
$this->excel->setActiveSheetIndex(0)->mergeCells('B4:I4');
$this->excel->getActiveSheet()->setCellValue('B4', "BÁO CÁO CHI TIẾT ĐƠN HÀNG");
$this->excel->getActiveSheet()->getStyle('B4')->getFont()->setSize(14)->setBold(true);
$this->excel->getActiveSheet()->getStyle('B4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->getActiveSheet()->setCellValue('C5', 'Từ '.date('d-m-Y H:i:s',strtotime($start_date)) .' đến '.date('d-m-Y H:i:s',strtotime($end_date)));
$this->excel->setActiveSheetIndex(0)->mergeCells('C5:H5');
$this->excel->getActiveSheet()->getStyle('C5')->getFont()->setSize(10)->setItalic(true);
$this->excel->getActiveSheet()->getStyle('C5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('C5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->getActiveSheet()->setCellValue('A7', "Mã ĐH ");
$this->excel->getActiveSheet()->setCellValue('B7', "Ngày ");
$this->excel->getActiveSheet()->setCellValue('C7', "Nhân viên bán");
$this->excel->getActiveSheet()->setCellValue('D7', "Tên khách hàng");
$this->excel->getActiveSheet()->setCellValue('E7', "Số lượng");
$this->excel->getActiveSheet()->setCellValue('F7', "Giá trị đơn hàng");
$this->excel->getActiveSheet()->setCellValue('G7', "Tổng chiết khấu");
$this->excel->getActiveSheet()->setCellValue('H7', "Thuế");
$this->excel->getActiveSheet()->setCellValue('I7', "Doanh thu");
$this->excel->getActiveSheet()->setCellValue('J7', "Thực thu");
$this->excel->getActiveSheet()->getStyle('A7:J7')->getFont()->setSize(12)->setBold(true);
$this->excel->getActiveSheet()->getStyle('A7:J7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A7:J7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$k = 8;
$total_cost = 0;
$total_discount = 0;
$total_real = 0;
 foreach ($data_sale_item as $key => $val){ 
    foreach ($val as $key1 => $val1){
  	//print_r($val);
            $this->excel->getActiveSheet()->setCellValue('A' . $k, 'ĐH '.$key);
            $tam =  $this->Sale->get_info($key)->row_array();
            $customer = $this->Person->get_info($tam['customer_id']);
            $employe = $this->Person->get_info($tam['employee_id']);
	    $this->excel->getActiveSheet()->setCellValue('B' . $k, $tam['sale_time']);
	    $this->excel->getActiveSheet()->setCellValue('C' . $k, $employe->first_name.' '.$employe->last_name);
	    $this->excel->getActiveSheet()->setCellValue('D' . $k, $customer->first_name.' '.$customer->last_name);
            
            $quan_puc = 0;
            $discount_percent = 0;
            $tax = 0;
            $total_sale = 0;
            $discount_money = 0;
            $pay_amount = 0;
            foreach ($val1 as $val2){
                $quan_puc += $val2['quantity_purchased'];
                if($val2['unit_from']){
                    if($val2['unit_from'] == $val2['unit_item']){
                        $item_unit_price = $val2['item_unit_price_rate'];
                    }else{
                        $item_unit_price = $val2['item_unit_price'];
                    }
                }
                else{
                    $item_unit_price = $val2['item_unit_price'];
                }                                            
                $discount_percent += $item_unit_price*$val2['quantity_purchased']*$val2['discount_percent']/100;
                $tax += ($item_unit_price*$val2['quantity_purchased']-$item_unit_price*$val2['quantity_purchased']*$val2['discount_percent']/100)*$val2['taxes_percent']/100;
                $total_sale += $item_unit_price*$val2['quantity_purchased'];
            }                                                                               
            $sale_tam = $this->Sale->get_sales_tam($key);
            foreach ($sale_tam as $val3){
                $pay_amount += $val3['pays_amount'];
                $discount_money += $val3['discount_money'];
            }
            $total_tax += $tax;

            $total_real += $pay_amount;
            if($item_type == 4){
                $total_discount_money += $discount_money + $discount_percent;
                $total_cost += $total_sale-$discount_percent+$tax- $discount_money;
            }else{
                $total_discount_money += $discount_percent;
                $total_cost += $total_sale-$discount_percent+$tax;
            }
            
            $this->excel->getActiveSheet()->setCellValue('E' . $k, format_quantity($quan_puc));
            $this->excel->getActiveSheet()->getStyle('C'.$k.':E'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->excel->getActiveSheet()->setCellValue('F' . $k, (($key1==0) && ($val1['liability']==0))?to_currency_unVND_nomar($total_sale):'');
            $this->excel->getActiveSheet()->getStyle('F'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            if($item_type == 4){
                $this->excel->getActiveSheet()->setCellValue('G' . $k, to_currency_unVND_nomar($discount_percent + $discount_money));
            }else{
                $this->excel->getActiveSheet()->setCellValue('G' . $k, to_currency_unVND_nomar($discount_percent));
            }
            $this->excel->getActiveSheet()->getStyle('G'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	    
	    $this->excel->getActiveSheet()->setCellValue('H' . $k, to_currency_unVND_nomar($tax));  
            if($item_type == 4){
                $this->excel->getActiveSheet()->setCellValue('I' . $k, to_currency_unVND_nomar($total_sale-$discount_percent+$tax-$discount_money));
            }else{
                $this->excel->getActiveSheet()->setCellValue('I' . $k, to_currency_unVND_nomar($total_sale-$discount_percent+$tax));
            }
	    $this->excel->getActiveSheet()->getStyle('I'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);	       
	    if ($item_type == '4') {
                $this->excel->getActiveSheet()->setCellValue('J' . $k, to_currency_unVND_nomar($pay_amount));
            }
	    $this->excel->getActiveSheet()->getStyle('J'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	    		
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
$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(21);
$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(21);
$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(18);
$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(14);
$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(17);
$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(17);
//$this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(23);

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
//$this->excel->getActiveSheet()->getStyle('K7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

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
//$this->excel->getActiveSheet()->getStyle('K7')->getAlignment()->setWrapText(true);

$j = $this->excel->getActiveSheet()->getHighestRow() + 1;

$this->excel->setActiveSheetIndex(0)->mergeCells('H'.($j).':I'.($j));
$this->excel->getActiveSheet()->setCellValue('H'.($j), 'Tổng tiền');
$this->excel->getActiveSheet()->getRowDimension($j)->setRowHeight(18);
$this->excel->getActiveSheet()->getStyle('J'.($j))->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->setCellValue('J'.($j), to_currency_unVND_nomar($total_cost) . ' VNĐ');

$this->excel->setActiveSheetIndex(0)->mergeCells('H'.($j+1).':I'.($j+1));
$this->excel->getActiveSheet()->setCellValue('H'.($j+1), 'Tổng chiết khấu');
$this->excel->getActiveSheet()->getRowDimension($j+1)->setRowHeight(18);
$this->excel->getActiveSheet()->getStyle('J'.($j+1))->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->setCellValue('J' . ($j+1), to_currency_unVND_nomar($total_discount_money) . ' VNĐ');

$this->excel->setActiveSheetIndex(0)->mergeCells('H'.($j+2).':I'.($j+2));
$this->excel->getActiveSheet()->setCellValue('H'.($j+2), 'Tổng thuế');
$this->excel->getActiveSheet()->getRowDimension($j+2)->setRowHeight(18);
$this->excel->getActiveSheet()->getStyle('J'.($j+2))->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->setCellValue('J' . ($j+2), to_currency_unVND_nomar($total_tax) . ' VNĐ');

$this->excel->setActiveSheetIndex(0)->mergeCells('H'.($j+3).':I'.($j+3));
$this->excel->getActiveSheet()->setCellValue('H'.($j+3), 'Tổng thực thu');
$this->excel->getActiveSheet()->getRowDimension($j+3)->setRowHeight(18);
$this->excel->getActiveSheet()->getStyle('J'.($j+3))->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->setCellValue('J' . ($j+3), to_currency_unVND_nomar($total_real) . ' VNĐ');

$this->excel->getActiveSheet()->getStyle('H'.$j.':H'.($j+3))->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('J'.$j.':J'.($j+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

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
$filename = 'Báo cáo chi tiết đơn hàng.xlsx'; //save our workbook as this file name
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