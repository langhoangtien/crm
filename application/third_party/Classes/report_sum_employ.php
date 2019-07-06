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
$this->excel->getActiveSheet()->setTitle('Báo cáo tổng hợp nhân viên');
$this->excel->setActiveSheetIndex(0)->mergeCells('A1:E1');
$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
$this->excel->getActiveSheet()->setCellValue('A1',$data['company']);
$this->excel->getActiveSheet()->setCellValue('A2',$data['address']);
$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $this->excel->setActiveSheetIndex(0)->mergeCells('A4:E4');
    $this->excel->getActiveSheet()->setCellValue('A4', "BÁO CÁO TỔNG HỢP NHÂN VIÊN");
    $this->excel->getActiveSheet()->getStyle('A4')->getFont()->setSize(14)->setBold(true);
    $this->excel->getActiveSheet()->getStyle('A4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $this->excel->getActiveSheet()->getStyle('A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


$this->excel->getActiveSheet()->setCellValue('A6', "Nhân viên");
$this->excel->getActiveSheet()->setCellValue('B6', "Thành tiền");
$this->excel->getActiveSheet()->setCellValue('C6', "Thuế");
$this->excel->getActiveSheet()->setCellValue('D6', "CK tiền mặt");
if($item_type ==4){
$this->excel->getActiveSheet()->setCellValue('E6', "Tổng cộng");
}
$k =7;
							$tong_cong_don_hang = 0;
                            $tong_cong_thue = 0;
                            $tong_cong_chiet_khau = 0;
                            $tong_cong_thanh_toan = 0;    
                           	$employees_all = $this->Sale->get_employee_in_sale_all($start_date, $end_date, $sale_type, $item_type);
                           	foreach ($employees_all as $employee_all){
                           		
                                $tong_tien_don_hang = 0;
                                $tong_thue = 0;
                                $employees = $this->Sale->get_employee_in_sale($start_date, $end_date, $sale_type, $item_type, $employee_all['employee_id']);
                                foreach ($employees as $employee){
                                    
                                	$datas = $this->Sale->get_sale_item_by_summary_employee($start_date, $end_date, $sale_type,$employee_all['employee_id'], $employee['sale_id'],$item_type);
                                    $payments = $this->Sale->get_sale_tam_by_summary_employee($start_date, $end_date,$employee_all['employee_id'],$sale_type);
                                    $tien_don_hang = 0;
                                    $tien_thue = 0;
                                    foreach ($datas as $data){
                                        
                                    	if($data['item_id']){
                                        	$price = ($data['unit_item']== $data['unit_from']) ? $data['item_unit_price_rate'] : $data['item_unit_price'];
                                        	$tien_don_hang += $price * $data['quantity_purchased'];
                                        	$tien_thue += $price * $data['quantity_purchased'] * $data['taxes_percent'] / 100;
                                        }
                                   	 	if($data['pack_id']){
                                   	 		$tien_don_hang += $data['pack_unit_price'] * $data['quantity_purchased'];
                                   	 		$tien_thue_item = 0;
                                   	 		$pack_infos = $this->Pack_items->get_info($data['pack_id']);
                                   	 		foreach($pack_infos as $pack_info){
                                   	 			$item_info = $this->Item->get_info($pack_info->item_id);
                                   	 			$tien_thue_item += $pack_info->price * $pack_info->quantity * $item_info->taxes / 100;
                                   	 		}
                                            $tien_thue += $tien_thue_item * $data['quantity_purchased'];
                                        }
                                    } 
                                	if($item_type ==4){
                                        $tong_tien_thanh_toan = 0;
                                        $tong_tien_chiet_khau = 0;
                                        foreach ($payments as $payment){
                                            $tong_tien_thanh_toan += $payment['pays_amount'];
                                        	$tong_tien_chiet_khau += $payment['discount_money'];
                                        }
                                    }
                                    $tong_tien_don_hang += $tien_don_hang;
                                    $tong_thue += $tien_thue;
                                }
                              
	  	$employee = $this->Person->get_info($employee_all['employee_id']);
	  	$this->excel->getActiveSheet()->setCellValue('A' . $k, $employee->first_name.' '.$employee->last_name);
	  	$this->excel->getActiveSheet()->setCellValue('B' . $k, number_format($tong_tien_don_hang));
	  	$this->excel->getActiveSheet()->setCellValue('C' . $k, number_format($tong_thue));
	  	$this->excel->getActiveSheet()->setCellValue('D' . $k, number_format($tong_tien_chiet_khau));
	  	if($item_type ==4){
	  		$this->excel->getActiveSheet()->setCellValue('E' . $k, number_format($tong_tien_thanh_toan));
	  	}
	  	$this->excel->getActiveSheet()->getStyle('A'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	  	$this->excel->getActiveSheet()->getStyle('B'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	  	$this->excel->getActiveSheet()->getStyle('C'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	  	$this->excel->getActiveSheet()->getStyle('D'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	  	$this->excel->getActiveSheet()->getStyle('E'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	  	
	  								$tong_cong_don_hang += $tong_tien_don_hang;
                                    $tong_cong_thue += $tong_thue;
                                    $tong_cong_chiet_khau += $tong_tien_chiet_khau;
                                    $tong_cong_thanh_toan += $tong_tien_thanh_toan;
	  $k++;
	  $this->excel->getActiveSheet()->getRowDimension($k)->setRowHeight(15.75);

 }
$this->excel->getActiveSheet()->getStyle('A6:E6')->getFont()->setSize(12)->setBold(true);
 $this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('C6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('D6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('E6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);

    $this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('B6')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('C6')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('D6')->getAlignment()->setWrapText(true);
    $this->excel->getActiveSheet()->getStyle('E6')->getAlignment()->setWrapText(true);


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
if($item_type ==4){
	$this->excel->getActiveSheet()->setCellValue('D'.($j), 'Tổng tiền hàng');
	$this->excel->getActiveSheet()->getRowDimension($j)->setRowHeight(14);
	$this->excel->getActiveSheet()->setCellValue('E'.($j), to_currency_unVND_nomar($tong_cong_don_hang));
	$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	
	$this->excel->getActiveSheet()->setCellValue('D'.($j+1), 'Tổng thuế');
	$this->excel->getActiveSheet()->getRowDimension($j+1)->setRowHeight(14);
	$this->excel->getActiveSheet()->setCellValue('E'.($j+1), to_currency_unVND_nomar($tong_cong_thue));
	$this->excel->getActiveSheet()->getStyle('E'.($j+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	
	$this->excel->getActiveSheet()->setCellValue('D'.($j+2), 'Tổng chiết khấu');
	$this->excel->getActiveSheet()->getRowDimension($j+2)->setRowHeight(14);
	$this->excel->getActiveSheet()->setCellValue('E'.($j+2), to_currency_unVND_nomar($tong_cong_chiet_khau));
	$this->excel->getActiveSheet()->getStyle('E'.($j+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

	$this->excel->getActiveSheet()->setCellValue('D'.($j+3), 'Tổng tiền thanh toán');
	$this->excel->getActiveSheet()->getRowDimension($j+3)->setRowHeight(14);
	$this->excel->getActiveSheet()->setCellValue('E'.($j+3), to_currency_unVND_nomar($tong_cong_thanh_toan));
	$this->excel->getActiveSheet()->getStyle('E'.($j+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	
	$this->excel->getActiveSheet()->getStyle('D'.($j).':E'.($j+3))->getFont()->setSize(10)->setBold(true);
	$this->excel->getActiveSheet()->getStyle('A6:E' . ($k-1))->applyFromArray($styleThinBlackBorderOutline);
}ELSE{
	$this->excel->getActiveSheet()->setCellValue('C'.($j), 'Tổng tiền hàng');
	$this->excel->getActiveSheet()->getRowDimension($j)->setRowHeight(14);
	$this->excel->getActiveSheet()->setCellValue('D'.($j), to_currency_unVND_nomar($tong_cong_don_hang));
	$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	
	$this->excel->getActiveSheet()->setCellValue('C'.($j+1), 'Tổng thuế');
	$this->excel->getActiveSheet()->getRowDimension($j+1)->setRowHeight(14);
	$this->excel->getActiveSheet()->setCellValue('D'.($j+1), to_currency_unVND_nomar($tong_cong_thue));
	$this->excel->getActiveSheet()->getStyle('D'.($j+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	
	$this->excel->getActiveSheet()->getStyle('C'.($j).':D'.($j+3))->getFont()->setSize(10)->setBold(true);
	$this->excel->getActiveSheet()->getStyle('A6:D' . ($k-1))->applyFromArray($styleThinBlackBorderOutline);
}

$this->excel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L&BInvoice&RPrinted on &D');
$this->excel->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&B' . $this->excel->getProperties()->getTitle() . '&RPage &P of &N');
//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
//if you want to save it as .XLSX Excel 2007 format
$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $md5file = md5(date('YmdHis')) . '.xlsx';

//$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
//force user to download the Excel file without writing it to server's HD
$filename = 'Báo cáo tổng hợp nhân viên.xlsx'; //save our workbook as this file name
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