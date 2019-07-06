<?php
$this->load->library('Excel');
$objPHPExcel = new PHPExcel();
$this->excel->getActiveSheet()->setShowGridlines(false);
$this->excel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Times New Roman');
$this->excel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(9);
$this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$this->excel->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
$this->excel->getActiveSheet()->getPageSetup()->setVerticalCentered(false);
$this->excel->getActiveSheet()->getPageMargins()->setRight(0.1);
$this->excel->getActiveSheet()->getPageMargins()->setLeft(0.15);
$this->excel->getActiveSheet()->getPageMargins()->setTop(0.15);
$this->excel->getActiveSheet()->getPageMargins()->setBOTTOM(0.2);
$this->excel->getActiveSheet()->getPageMargins()->setFOOTER(0.1);
$this->excel->getActiveSheet()->getPageMargins()->setHEADER(0.1);
$this->excel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTop(array(7,8));
//activate worksheet number 1
$this->excel->setActiveSheetIndex(0);
//error_reporting(E_ALL);
error_reporting(E_ALL & ~E_NOTICE);
$this->excel->getDefaultStyle()
    ->getAlignment()
    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
//name the worksheet
$this->excel->getActiveSheet()->setTitle('Lập kế hoạch');
/* ten dia chi cong ty ngay thang noi dung */
$this->excel->setActiveSheetIndex(0)->mergeCells('A1:B1');
$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
$this->excel->getActiveSheet()->setCellValue('A1',$this->config->item('company'));
$this->excel->getActiveSheet()->setCellValue('A2',$this->config->item('address'));
$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->setActiveSheetIndex(0)->mergeCells('A2:B2');
$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setItalic(true);
$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(9);

	$this->excel->getActiveSheet()->setCellValue('B4', "KẾ HOẠCH TÀI CHÍNH");
	$this->excel->getActiveSheet()->getStyle('B4')->getFont()->setSize(14)->setBold(true);
	$this->excel->getActiveSheet()->getRowDimension(4)->setRowHeight(25.50);
	$this->excel->getActiveSheet()->getStyle('B4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objRichTextdate = new PHPExcel_RichText();
$tu = $objRichTextdate->createTextRun('Từ: ')->getFont()->setSize(9)->setName('Times New Roman');
$startdate = $objRichTextdate->createTextRun(date("d/m/Y", strtotime($start_date)))->getFont()->setItalic(true)->setSize(9)->setName('Times New Roman')->setBold(true); 
$den = $objRichTextdate->createTextRun(' đến: ')->getFont()->setSize(9);
$enddate = $objRichTextdate->createTextRun(date("d/m/Y", strtotime($end_date)))->getFont()->setItalic(true)->setSize(9)->setName('Times New Roman')->setBold(true); 
$this->excel->getActiveSheet()->getCell('C5')->setValue($objRichTextdate);
$this->excel->getActiveSheet()->getRowDimension(5)->setRowHeight(18.75);
	$this->excel->getActiveSheet()->setCellValue('A7', "Mã");
	$this->excel->getActiveSheet()->setCellValue('B7', "Tên dự án, công việc");
	$this->excel->getActiveSheet()->setCellValue('C7', "Hạng mục");
	$this->excel->getActiveSheet()->setCellValue('D7', "Khách hàng");
	$this->excel->getActiveSheet()->setCellValue('E7', "Giá trị HĐ");
	$this->excel->getActiveSheet()->setCellValue('F7', "Giá trị đã thực hiện");
	$this->excel->getActiveSheet()->getStyle('F7:H7')
    ->getAlignment()->setWrapText(true); 
	$this->excel->getActiveSheet()->setCellValue('G7', "Thực hiện tháng này");	$this->excel->getActiveSheet()->setCellValue('H7', "Giá trị còn lại chuyển kỳ sau thực hiện");
	$this->excel->getActiveSheet()->setCellValue('I7', $dttc_info->date_1);
	$this->excel->getActiveSheet()->setCellValue('J7', $dttc_info->date_2);
	$this->excel->getActiveSheet()->setCellValue('K7', $dttc_info->date_3);
	$this->excel->getActiveSheet()->setCellValue('L7', $dttc_info->date_4);
	$this->excel->getActiveSheet()->setCellValue('M7', $dttc_info->date_5);
	$this->excel->getActiveSheet()->setCellValue('N7', $dttc_info->date_6);
	$this->excel->getActiveSheet()->getStyle('I7:N7')->getFill()->applyFromArray(
	array(
		'type'       => PHPExcel_Style_Fill::FILL_SOLID,
		'startcolor' => array('rgb' => 'fee302'),
		'endcolor'   => array('rgb' => 'fee302')
	)
);

	$this->excel->getActiveSheet()->setCellValue('O7', "Đánh giá");
/* set color background cell */
/* style */
 $styleThinBlackBorderOutline = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
			),
		),
	);
$this->excel->getActiveSheet()->getStyle('A7:O7')->getFont()->setBold(true)->setSize(12);

$this->excel->getActiveSheet()->getStyle('A7:O7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getRowDimension(7)->setRowHeight(60);
$this->excel->getActiveSheet()->getStyle('A7:O7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
/* end tieu de cac cot */

/* dong thu */
	$this->excel->getActiveSheet()->setCellValue('A8', "A");
	$this->excel->getActiveSheet()->setCellValue('B8', "Các khoản thu");
$this->excel->getActiveSheet()->getRowDimension(8)->setRowHeight(18.75);
$this->excel->getActiveSheet()->getStyle('A8:O8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A8:O8')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A8:O8')->getFill()->applyFromArray(
	array(
		'type'       => PHPExcel_Style_Fill::FILL_SOLID,
		'startcolor' => array('rgb' => '85efba'),
		'endcolor'   => array('rgb' => '85efba')
	)
);
/* end dong thu */
$k = 9;if ($dttc_details_thu != null){
	foreach($dttc_details_thu as $dttc_detail_thu){
		$this->excel->getActiveSheet()->setCellValue('A'.$k, $dttc_detail_thu['id']);
		$this->excel->getActiveSheet()->setCellValue('B'.$k, $dttc_detail_thu['name']);
		$this->excel->getActiveSheet()->setCellValue('C'.$k, $dttc_detail_thu['hangmuc']);
		$this->excel->getActiveSheet()->setCellValue('D'.$k, $dttc_detail_thu['name_customer']);
		$this->excel->getActiveSheet()->setCellValue('E'.$k, to_currency_unVND_nomar($dttc_detail_thu['cost_contract']));
		$tong_cost_contract += $dttc_detail_thu['cost_contract'];
		$this->excel->getActiveSheet()->setCellValue('F'.$k, to_currency_unVND_nomar($dttc_detail_thu['cost_done']));
		$tong_cost_done += $dttc_detail_thu['cost_done'];
		$this->excel->getActiveSheet()->setCellValue('I'.$k, to_currency_unVND_nomar($dttc_detail_thu['date_1']));
		$tong_date_1 += $dttc_detail_thu['date_1'];
		$this->excel->getActiveSheet()->setCellValue('J'.$k, to_currency_unVND_nomar($dttc_detail_thu['date_2']));
		$tong_date_2 += $dttc_detail_thu['date_2'];
		$this->excel->getActiveSheet()->setCellValue('K'.$k, to_currency_unVND_nomar($dttc_detail_thu['date_3']));
		$tong_date_3 += $dttc_detail_thu['date_3'];
		$this->excel->getActiveSheet()->setCellValue('L'.$k, to_currency_unVND_nomar($dttc_detail_thu['date_4']));
		$tong_date_4 += $dttc_detail_thu['date_4'];
		$this->excel->getActiveSheet()->setCellValue('M'.$k, to_currency_unVND_nomar($dttc_detail_thu['date_5']));
		$tong_date_5 += $dttc_detail_thu['date_5'];
		$this->excel->getActiveSheet()->setCellValue('N'.$k, to_currency_unVND_nomar($dttc_detail_thu['date_6']));
		$tong_date_6 += $dttc_detail_thu['date_6'];
		$this->excel->getActiveSheet()->setCellValue('O'.$k, $dttc_detail_thu['danhgia']);
		$dttc_month = $dttc_detail_thu['date_1'] + $dttc_detail_thu['date_2'] + $dttc_detail_thu['date_3'] + $dttc_detail_thu['date_4'] + $dttc_detail_thu['date_5'] + $dttc_detail_thu['date_6'] ;
		$this->excel->getActiveSheet()->setCellValue('G'.$k, to_currency_unVND_nomar($dttc_month));
		$this->excel->getActiveSheet()->setCellValue('H'.$k, to_currency_unVND_nomar(($dttc_detail_thu['cost_contract']-$dttc_month - $dttc_detail_thu['cost_done'])));
		$k++;
	}
}	
/* tong thu */
$tong_thang_nay = $tong_date_1 + $tong_date_2 + $tong_date_3 + $tong_date_4 + $tong_date_5 + $tong_date_6 ;
	$this->excel->getActiveSheet()->setCellValue('E'.$k, to_currency_unVND_nomar($tong_cost_contract));
	$this->excel->getActiveSheet()->setCellValue('F'.$k, " ");
	$this->excel->getActiveSheet()->setCellValue('G'.$k, to_currency_unVND_nomar($tong_thang_nay));
	$this->excel->getActiveSheet()->setCellValue('H'.$k, to_currency_unVND_nomar(($tong_cost_contract-$tong_thang_nay)));
	$this->excel->getActiveSheet()->setCellValue('I'.$k, to_currency_unVND_nomar($tong_date_1));
	$this->excel->getActiveSheet()->setCellValue('J'.$k, to_currency_unVND_nomar($tong_date_2));
	$this->excel->getActiveSheet()->setCellValue('K'.$k, to_currency_unVND_nomar($tong_date_3));
	$this->excel->getActiveSheet()->setCellValue('L'.$k, to_currency_unVND_nomar($tong_date_4));
	$this->excel->getActiveSheet()->setCellValue('M'.$k, to_currency_unVND_nomar($tong_date_5));
	$this->excel->getActiveSheet()->setCellValue('N'.$k, to_currency_unVND_nomar($tong_date_6));
$this->excel->getActiveSheet()->getStyle('E'.$k.':O'.$k)->getFill()->applyFromArray(
	array(
		'type'       => PHPExcel_Style_Fill::FILL_SOLID,
		'startcolor' => array('rgb' => '85efba'),
		'endcolor'   => array('rgb' => '85efba')
	)
);
	$k += 1;
/* end tong thu */
 /* dong chi */
	$this->excel->getActiveSheet()->setCellValue('A'.$k, "B");
	$this->excel->getActiveSheet()->setCellValue('B'.$k, "Các khoản chi");
$this->excel->getActiveSheet()->getRowDimension($k)->setRowHeight(18.75);
$this->excel->getActiveSheet()->getStyle('A'.$k.':O'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A'.$k.':O'.$k)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A'.$k.':O'.$k)->getFill()->applyFromArray(
	array(
		'type'       => PHPExcel_Style_Fill::FILL_SOLID,
		'startcolor' => array('rgb' => 'f77451'),
		'endcolor'   => array('rgb' => 'f77451')
	)
);
/* end dong chi */	
$k +=1;
/*  chi */
if ($dttc_details_chi != null){
	foreach($dttc_details_chi as $dttc_detail_chi){
		$this->excel->getActiveSheet()->setCellValue('A'.$k, $dttc_detail_chi['id']);
		$this->excel->getActiveSheet()->setCellValue('B'.$k, $dttc_detail_chi['name']);
		$this->excel->getActiveSheet()->setCellValue('C'.$k, $dttc_detail_chi['hangmuc']);
		$this->excel->getActiveSheet()->setCellValue('D'.$k, $dttc_detail_chi['name_customer']);
		$this->excel->getActiveSheet()->setCellValue('E'.$k, to_currency_unVND_nomar($dttc_detail_chi['cost_contract']));
		$tong_cost_contract_chi += $dttc_detail_chi['cost_contract'];
		$this->excel->getActiveSheet()->setCellValue('I'.$k, to_currency_unVND_nomar($dttc_detail_chi['date_1']));
		$tong_date_1_chi += $dttc_detail_chi['date_1'];
		$this->excel->getActiveSheet()->setCellValue('J'.$k, to_currency_unVND_nomar($dttc_detail_chi['date_2']));
		$tong_date_2_chi += $dttc_detail_chi['date_2'];
		$this->excel->getActiveSheet()->setCellValue('K'.$k, to_currency_unVND_nomar($dttc_detail_chi['date_3']));
		$tong_date_3_chi += $dttc_detail_chi['date_3'];
		$this->excel->getActiveSheet()->setCellValue('L'.$k, to_currency_unVND_nomar($dttc_detail_chi['date_4']));
		$tong_date_4_chi += $dttc_detail_chi['date_4'];
		$this->excel->getActiveSheet()->setCellValue('M'.$k, to_currency_unVND_nomar($dttc_detail_chi['date_5']));
		$tong_date_5_chi += $dttc_detail_chi['date_5'];
		$this->excel->getActiveSheet()->setCellValue('N'.$k, to_currency_unVND_nomar($dttc_detail_chi['date_6']));
		$tong_date_6_chi += $dttc_detail_chi['date_6'];
		$this->excel->getActiveSheet()->setCellValue('O'.$k, $dttc_detail_chi['danhgia']);
		$dttc_month_chi = $dttc_detail_chi['date_1'] + $dttc_detail_chi['date_2'] + $dttc_detail_chi['date_3'] + $dttc_detail_chi['date_4'] + $dttc_detail_chi['date_5'] + $dttc_detail_chi['date_6'] ;
		$this->excel->getActiveSheet()->setCellValue('G'.$k, $dttc_month_chi);
		$this->excel->getActiveSheet()->setCellValue('H'.$k, ($dttc_detail_chi['cost_contract']-$dttc_month_chi));
		$k++;
	}
}	
/* end chi */
/* tong thu */
$tong_thang_nay_chi = $tong_date_1_chi + $tong_date_2_chi + $tong_date_3_chi + $tong_date_4_chi + $tong_date_5_chi + $tong_date_6_chi ;
	$this->excel->getActiveSheet()->setCellValue('E'.$k, to_currency_unVND_nomar($tong_cost_contract_chi));
	$this->excel->getActiveSheet()->setCellValue('F'.$k, " ");
	$this->excel->getActiveSheet()->setCellValue('G'.$k, $tong_thang_nay_chi);
	$this->excel->getActiveSheet()->setCellValue('H'.$k, to_currency_unVND_nomar(($tong_cost_contract_chi-$tong_thang_nay_chi)));
	$this->excel->getActiveSheet()->setCellValue('I'.$k, to_currency_unVND_nomar($tong_date_1_chi));
	$this->excel->getActiveSheet()->setCellValue('J'.$k, to_currency_unVND_nomar($tong_date_2_chi));
	$this->excel->getActiveSheet()->setCellValue('K'.$k, to_currency_unVND_nomar($tong_date_3_chi));
	$this->excel->getActiveSheet()->setCellValue('L'.$k, to_currency_unVND_nomar($tong_date_4_chi));
	$this->excel->getActiveSheet()->setCellValue('M'.$k, to_currency_unVND_nomar($tong_date_5_chi));
	$this->excel->getActiveSheet()->setCellValue('N'.$k, to_currency_unVND_nomar($tong_date_6_chi));
$this->excel->getActiveSheet()->getStyle('E'.$k.':O'.$k)->getFill()->applyFromArray(
	array(
		'type'       => PHPExcel_Style_Fill::FILL_SOLID,
		'startcolor' => array('rgb' => 'f77451'),
		'endcolor'   => array('rgb' => 'f77451')
	)
);
	/* end tong thu */
$this->excel->getActiveSheet()->getStyle('A7:O'.$k )->applyFromArray($styleThinBlackBorderOutline);
/* style cot */
$this->excel->getActiveSheet()->getStyle('A8:A'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('E9:O'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
/* */
$this->excel->getActiveSheet()->getHeaderFooter()->setOddFooter('&RPage &P of &N');
$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(10.17);
$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(45.50);
$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(19.67);
$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(20);

//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
//if you want to save it as .XLSX Excel 2007 format
$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $md5file = md5(date('YmdHis')) . '.xlsx';
//$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
//force user to download the Excel file without writing it to server's HD
$filename = 'lapkehoach.xlsx'; //save our workbook as this file name
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