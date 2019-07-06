<?php
$count = count($data);
$this->load->library('Excel');
$objPHPExcel = new PHPExcel();
$this->excel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->setShowGridlines(true);
$this->excel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Times New Roman');
$this->excel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(9);
$this->excel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
$this->excel->getActiveSheet()->getPageMargins()->setRight(0.1);
$this->excel->getActiveSheet()->getPageMargins()->setLeft(0.27);
$this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$this->excel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTop(array(6,6));
//activate worksheet number 1
$this->excel->setActiveSheetIndex(0);
//error_reporting(E_ALL);
error_reporting(E_ALL & ~E_NOTICE);
//name the worksheet
$this->excel->getActiveSheet()->setTitle('Báo cáo tổng hợp khách hàng');
$this->excel->setActiveSheetIndex(0)->mergeCells('A1:E1');
$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->setCellValue('A1',$data['company']);
$this->excel->getActiveSheet()->setCellValue('A2',$data['address']);
$this->excel->setActiveSheetIndex(0)->mergeCells('A4:E4');
$this->excel->getActiveSheet()->setCellValue('A4', "BÁO CÁO TỔNG HỢP KHÁCH HÀNG");
$this->excel->getActiveSheet()->getStyle('A4')->getFont()->setSize(14)->setBold(true);
$this->excel->getActiveSheet()->getStyle('A4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->getActiveSheet()->setCellValue('A6', "Khách hàng");
$this->excel->getActiveSheet()->setCellValue('B6', "Tổng tiền hàng");
$this->excel->getActiveSheet()->setCellValue('C6', "Tổng thuế");
if($item_type == 4){
    $this->excel->getActiveSheet()->setCellValue('D6', "Tổng tiền thanh toán");
}
$this->excel->getActiveSheet()->getStyle('A6:D6')->getFont()->setSize(12)->setBold(true);

$tong_cong_don_hang = 0;
$tong_cong_thue = 0;
$tong_cong_thanh_toan = 0;    
$customers_all = $this->Sale->get_customer_in_sale_all($start_date, $end_date, $sale_type, $item_type);
$k = 7;
foreach ($customers_all as $customer_all){
                           		
	$tong_tien_don_hang = 0;
    $tong_thue = 0;
    $customers = $this->Sale->get_customer_in_sale($start_date, $end_date, $sale_type, $item_type, $customer_all['customer_id']);
    foreach ($customers as $customer){
                                    
    	$datas = $this->Sale->get_sale_item_by_summary_customer($start_date, $end_date, $sale_type,$customer_all['customer_id'], $customer['sale_id'],$item_type);
        $payments = $this->Sale->get_sale_tam_by_summary_customer($start_date, $end_date,$customer_all['customer_id'],$sale_type);
        $tien_don_hang = 0;
        $tien_thue = 0;
        foreach ($datas as $data){
                                        
        	if($data['item_id']){
            	$price = ($data['unit_item']== $data['unit_from']) ? $data['item_unit_price_rate'] : $data['item_unit_price'];
                $tien_discount_percent = $price * $data['discount_percent'] / 100;
                $tien_don_hang += ($price - $tien_discount_percent) * $data['quantity_purchased'];
                $tien_thue += ($price - $tien_discount_percent) * $data['quantity_purchased'] * $data['taxes_percent'] / 100;
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
            foreach ($payments as $payment){
            	$tong_tien_thanh_toan += $payment['pays_amount'];
            }
        }
        $tong_tien_don_hang += $tien_don_hang;
        $tong_thue += $tien_thue;
    }
    if($customer_all['customer_id'] == -1 || $customer_all['customer_id'] == NULL){
        $this->excel->getActiveSheet()->setCellValue('A' . $k, "Khách lẻ");
    }else{
        $customer = $this->Person->get_info($customer_all['customer_id']);
        $this->excel->getActiveSheet()->setCellValue('A' . $k,$customer->first_name.' '.$customer->last_name);
    }
    $this->excel->getActiveSheet()->setCellValue('B' . $k, to_currency_unVND_nomar($tong_tien_don_hang));
    $this->excel->getActiveSheet()->setCellValue('C' . $k, to_currency_unVND_nomar($tong_thue));
    if($item_type == 4){
    	$this->excel->getActiveSheet()->setCellValue('D' . $k, to_currency_unVND_nomar($tong_tien_thanh_toan));
    }
    $this->excel->getActiveSheet()->getStyle('B'.$k.':D'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
     
    $tong_cong_don_hang += $tong_tien_don_hang;
    $tong_cong_thue += $tong_thue;
    $tong_cong_thanh_toan += $tong_tien_thanh_toan;
    $k++;
	$this->excel->getActiveSheet()->getRowDimension($k)->setRowHeight(15.75);
} 
$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$this->excel->getActiveSheet()->getPageMargins()->setRight(0.25);
$this->excel->getActiveSheet()->getPageMargins()->setLeft(1.5);
$this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getStyle('B6')->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getStyle('C6')->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getStyle('D6')->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getStyle('E6')->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('C6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('D6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('E6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
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

$this->excel->setActiveSheetIndex(0)->mergeCells('B'.($j).':C'.($j));
$this->excel->getActiveSheet()->setCellValue('B'.($j), 'Tổng tiền hàng');
$this->excel->getActiveSheet()->getRowDimension($j)->setRowHeight(14);
$this->excel->getActiveSheet()->getStyle('D'.($j))->getFont()->setBold(true)->setSize(10);
$this->excel->getActiveSheet()->setCellValue('D'.($j), to_currency_unVND_nomar($tong_cong_don_hang));
$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$this->excel->setActiveSheetIndex(0)->mergeCells('B'.($j+1).':C'.($j+1));
$this->excel->getActiveSheet()->setCellValue('B'.($j+1), 'Tổng thuế');
$this->excel->getActiveSheet()->getRowDimension($j+1)->setRowHeight(14);
$this->excel->getActiveSheet()->getStyle('D'.($j+1))->getFont()->setBold(true)->setSize(10);
$this->excel->getActiveSheet()->setCellValue('D'.($j+1), to_currency_unVND_nomar($tong_cong_thue));
$this->excel->getActiveSheet()->getStyle('B'.($j+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('D'.($j+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

if($item_type == 4){
    $this->excel->setActiveSheetIndex(0)->mergeCells('B'.($j+2).':C'.($j+2));
    $this->excel->getActiveSheet()->setCellValue('B'.($j+2), 'Tổng tiền thanh toán');
    $this->excel->getActiveSheet()->getRowDimension($j+2)->setRowHeight(14);
    $this->excel->getActiveSheet()->getStyle('D'.($j+2))->getFont()->setBold(true)->setSize(10);
    $this->excel->getActiveSheet()->setCellValue('D'.($j+2), to_currency_unVND_nomar($tong_cong_thanh_toan));
    $this->excel->getActiveSheet()->getStyle('D'.($j+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
}

$this->excel->getActiveSheet()->getStyle('A6:D' . ($k-1))->applyFromArray($styleThinBlackBorderOutline);
$this->excel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L&BInvoice&RPrinted on &D');
$this->excel->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&B' . $this->excel->getProperties()->getTitle() . '&RPage &P of &N');

//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
//if you want to save it as .XLSX Excel 2007 format
$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $md5file = md5(date('YmdHis')) . '.xlsx';
//$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
//force user to download the Excel file without writing it to server's HD
$filename = 'báo cáo tổng hợp khách hàng.xlsx'; //save our workbook as this file name

//$filename = 'Book1.xlsx';

$objWriter->save($filename);

if (file_exists($filename)) {

    header('Content-Type: application/vnd.ms-excel'); //mime type

    header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name

    header('Cache-Control: max-age=0'); //no cache    

//    header('Content-Description: File Transfer');

//   header('Content-Type: application/octet-stream');

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