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
$this->excel->getActiveSheet()->setTitle('Báo cáo công nợ');
/* ten dia chi cong ty ngay thang noi dung */
$this->excel->setActiveSheetIndex(0)->mergeCells('A1:B1');
$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
$this->excel->getActiveSheet()->setCellValue('A1',$data['company']);
$this->excel->getActiveSheet()->setCellValue('A2',$data['address']);
$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->setActiveSheetIndex(0)->mergeCells('A2:B2');
$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setItalic(true);
$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(9);


$this->excel->setActiveSheetIndex(0)->mergeCells('E1:H1');
$this->excel->getActiveSheet()->getStyle('E1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('E1')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('E1')->getFont()->setSize(9);
$this->excel->getActiveSheet()->setCellValue('E1',"Mẫu số S20 - DNN");
$this->excel->setActiveSheetIndex(0)->mergeCells('E2:H2');
$this->excel->getActiveSheet()->getStyle('E2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('E2')->getFont()->setSize(8);
$this->excel->getActiveSheet()->setCellValue('E2',"Ban hành theo QĐ số 48/2006/QĐ - BTC");
$this->excel->setActiveSheetIndex(0)->mergeCells('E3:H3');
$this->excel->getActiveSheet()->getStyle('E3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('E3')->getFont()->setSize(8);
$this->excel->getActiveSheet()->setCellValue('E3',"Ngày 14 tháng 9 năm 2006");
$this->excel->getActiveSheet()->getStyle('E1:H3')->getFont()->setItalic(true);

	$this->excel->getActiveSheet()->setCellValue('B4', "BẢNG TỔNG HỢP THEO DÕI ĐỐI TƯỢNG - 1311 - PHẢI THU CỦA KHÁCH HÀNG");
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

	$this->excel->getActiveSheet()->setCellValue('C7', "Dư đầu kỳ");
	$this->excel->getActiveSheet()->setCellValue('E7', "Phát sinh trong kỳ");
	$this->excel->getActiveSheet()->setCellValue('G7', "Dư cuối kỳ");

$this->excel->setActiveSheetIndex(0)->mergeCells('A7:A8');	
$this->excel->getActiveSheet()->setCellValue('A7', "Mã");
$this->excel->setActiveSheetIndex(0)->mergeCells('B7:B8');	
$this->excel->getActiveSheet()->setCellValue('B7', "Tên đối tượng");
$this->excel->getActiveSheet()->getStyle('A7:B7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A7:B7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->getActiveSheet()->setCellValue('C8', "Nợ");
$this->excel->getActiveSheet()->setCellValue('D8', "Có");
$this->excel->getActiveSheet()->setCellValue('E8', "Nợ");
$this->excel->getActiveSheet()->setCellValue('F8', "Có");
$this->excel->getActiveSheet()->setCellValue('G8', "Nợ");
$this->excel->getActiveSheet()->setCellValue('H8', "Có");
/* style */
 $styleThinBlackBorderOutline = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
			),
		),
	);
$this->excel->getActiveSheet()->getStyle('A7:H8')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('A7:H8' . $k)->applyFromArray($styleThinBlackBorderOutline);
$this->excel->setActiveSheetIndex(0)->mergeCells('C7:D7');
$this->excel->setActiveSheetIndex(0)->mergeCells('E7:F7');
$this->excel->setActiveSheetIndex(0)->mergeCells('G7:H7');

$this->excel->getActiveSheet()->getStyle('C7:G7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->getActiveSheet()->getStyle('C8:H8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
/* end tieu de cac cot */
 $styleThinBlackBorderOutline = array(
'borders' => array(
	'allborders' => array(
		'style' => PHPExcel_Style_Border::BORDER_THIN,
		'color' => array('argb' => 'FF000000'),
		),
	),
);
$styleDOTBlackBorderOutline = array(
    'borders' => array(
		'top' => array(
            'style' => PHPExcel_Style_Border::BORDER_DOTTED,
            'color' => array('argb' => 'FF000000'),
        ),
        'bottom' => array(
            'style' => PHPExcel_Style_Border::BORDER_DOTTED,
            'color' => array('argb' => 'FF000000'),
        ),
    ),
);
$styleDOTBlackBorderTOPBOTOutline = array(
    'borders' => array(
        'top' => array(
            'style' => PHPExcel_Style_Border::BORDER_DOTTED,
            'color' => array('argb' => 'FF000000'),
        ),
		'bottom' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
        ),
    ),
);
/* */
		$array_tong_cot_tienno_phatsinh_tong = 0;
		$array_tong_cot_tienco_phatsinh_tong = 0;
		$array_tong_cot_tienco_dauky_tong = 0;
		$array_tong_cot_tienno_dauky_tong = 0;
		$array_tong_cot_tienco_cuoiky_tong = 0;
		$array_tong_cot_tienno_cuoiky_tong = 0;
$k = 9;
if($code_cities != null){ foreach ($code_cities as $code_city){ 
		 if($code_city['id_city_code'] != null){
		//$customers_info = $this->Customer->find_customer($code_city['id_city_code']) ; 
		$customers_info = $this->Customer->find_customer($code_city['id_city_code'],$start_date,$end_date) ; 
		$array_tongtienno_phatsinh = array();
		$array_tongtienco_phatsinh = array();
		$array_tongtienco_dauky = array();
		$array_tongtienno_dauky = array();
		$array_tongtienco_cuoiky = array();
		$array_tongtienno_cuoiky = array();
		foreach($customers_info as $cus_info){
		$this->excel->getActiveSheet()->setCellValue('A'.$k, $this->Customer->get_info($cus_info['id_customer'])->person_id);
		  $this->excel->getActiveSheet()->getStyle('A'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->excel->getActiveSheet()->setCellValue('B'.$k, $this->Customer->get_info($cus_info['id_customer'])->first_name);
		/*no dau ky */
		$sales_ids_dauky = $this->Customer->find_sale_id_customer_dauky($cus_info['id_customer'],$start_date);
			$tongtienno_dauky = 0;
		if($sales_ids_dauky != null){
		foreach($sales_ids_dauky as $sales_id_dauky ){
			 $query_tiennos_dauky = $this->Item->get_price_sale_item($sales_id_dauky['id_sale']);
			 foreach ($query_tiennos_dauky as $query_tienno_dauky){
				$tongtienno_dauky += $query_tienno_dauky['tienno'];
			 }
		 } } else $tongtienno_dauky = 0 ;
		 $array_tongtienno_dauky[] = $tongtienno_dauky ;
		$this->excel->getActiveSheet()->setCellValue('C'.$k, to_currency_unVND($tongtienno_dauky));
                $this->excel->getActiveSheet()->getStyle('C'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		/* end no dau ky */
		/* co dau ky */
		$sales_ids_dauky = $this->Customer->find_sale_id_customer_dauky($cus_info['id_customer'],$start_date);
		 $array_tienco_dauky = array();
		if($sales_ids_dauky != null){
		foreach($sales_ids_dauky as $sales_id_dauky ){
		  $hinhthucs_dauky = $this->Customer->find_pay_type_congno_dauky($cus_info['id_customer'],$start_date,$sales_id_dauky['id_sale']);
		  foreach ($hinhthucs_dauky as $hinhthuc_dauky){
			  $tien_cos_dauky = $this->Customer->find_tien_congno_dauky($cus_info['id_customer'],$start_date,$sales_id_dauky['id_sale'],$hinhthuc_dauky['pay_type']);
			 foreach ($tien_cos_dauky as $tien_co_dauky){
				$array_tienco_dauky[] = $tien_co_dauky['pay_amount'];
			 }
		  }
		 } }
		  $tongtienco_dauky = 0;
		  for($l = 0; $l < count($array_tienco_dauky); $l++ ){
			 $tongtienco_dauky += $array_tienco_dauky[$l];
		  }
		  $array_tongtienco_dauky[] = $tongtienco_dauky;
		 $this->excel->getActiveSheet()->setCellValue('D'.$k, to_currency_unVND($tongtienco_dauky));
                 $this->excel->getActiveSheet()->getStyle('D'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		/* end co dau ky */
		/* no phat sinh trong ky */
		$sales_ids = $this->Customer->find_sale_id_customer($cus_info['id_customer'],$start_date,$end_date);
		$tongtienno = 0;
		if($sales_ids != null){
		foreach($sales_ids as $sales_id ){
			 $query_tiennos = $this->Item->get_price_sale_item($sales_id['id_sale']);
			 foreach ($query_tiennos as $query_tienno){
				$tongtienno += $query_tienno['tienno'];
			 }
		 } } else $tongtienno = 0;
		 $array_tongtienno_phatsinh[] = $tongtienno ;
		 $this->excel->getActiveSheet()->setCellValue('E'.$k, to_currency_unVND($tongtienno));
                 $this->excel->getActiveSheet()->getStyle('E'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		/* co phat sinh trong ky */
		$sales_ids = $this->Customer->find_sale_id_customer($cus_info['id_customer'],$start_date,$end_date);
		$array_tienco = array();
		if($sales_ids != null){
		foreach($sales_ids as $sales_id ){
		  $hinhthucs = $this->Customer->find_pay_type_congno($cus_info['id_customer'],$start_date,$end_date,$sales_id['id_sale']);
		 foreach ($hinhthucs as $hinhthuc){
			  $tien_cos = $this->Customer->find_tien_congno($cus_info['id_customer'],$start_date,$end_date,$sales_id['id_sale'],$hinhthuc['pay_type']);
			 foreach ($tien_cos as $tien_co){
				$array_tienco[] = $tien_co['pay_amount'];
			 }
		  }
		 } }
		 $tongtienco = 0;
		 for($i = 0; $i < count($array_tienco); $i++ ){
			$tongtienco += $array_tienco[$i];
		 }
		 $array_tongtienco_phatsinh[] = $tongtienco;
		 $this->excel->getActiveSheet()->setCellValue('F'.$k, to_currency_unVND($tongtienco));
                 $this->excel->getActiveSheet()->getStyle('F'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		/* end phat sinh trong ky */
		/* no cuoi ky */
		$sales_ids_cuoiky = $this->Customer->find_sale_id_customer_cuoiky($cus_info['id_customer'],$end_date);
			$tongtienno_cuoiky = 0;
		if($sales_ids_cuoiky != null){
		foreach($sales_ids_cuoiky as $sales_id_cuoiky ){
			 $query_tiennos_cuoiky = $this->Item->get_price_sale_item($sales_id_cuoiky['id_sale']);
			 foreach ($query_tiennos_cuoiky as $query_tienno_cuoiky){
				$tongtienno_cuoiky += $query_tienno_cuoiky['tienno'];
			 }
		 } }
		 $array_tongtienno_cuoiky[] = $tongtienno_cuoiky+$tongtienno_dauky+$tongtienno ;
		 $this->excel->getActiveSheet()->setCellValue('G'.$k, to_currency_unVND($tongtienno_cuoiky+$tongtienno_dauky+$tongtienno));
                 $this->excel->getActiveSheet()->getStyle('G'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                 /* co cuoi ky */
		$sales_ids_cuoiky = $this->Customer->find_sale_id_customer_cuoiky($cus_info['id_customer'],$end_date);
		 $array_tienco_cuoiky = array();
		 if($array_tienco_cuoiky != null){
		foreach($sales_ids_cuoiky as $sales_id_cuoiky ){
		  $hinhthucs_cuoky = $this->Customer->find_pay_type_congno_cuoiky($cus_info['id_customer'],$end_date,$sales_id_cuoiky['id_sale']);
		  foreach ($hinhthucs_cuoky as $hinhthuc_cuoky){
			  $tien_cos_cuoky = $this->Customer->find_tien_congno_cuoky($cus_info['id_customer'],$end_date,$sales_id_cuoiky['id_sale'],$hinhthuc['pay_type']);
			 foreach ($tien_cos_cuoky as $tien_co_cuoky){
				$array_tienco_cuoiky[] = $tien_co_cuoky['pay_amount'];
			 }
		  }
		 } }
		  $tongtienco_cuoiky = 0;
		  for($l = 0; $l < count($array_tienco_cuoiky); $l++ ){
			 $tongtienco_cuoiky += $array_tienco_cuoiky[$l];
		  }
		  $array_tongtienco_cuoiky[] = $tongtienco_cuoiky + $tongtienco_dauky+$tongtienco;
		   $this->excel->getActiveSheet()->setCellValue('H'.$k, to_currency_unVND($tongtienco_cuoiky + $tongtienco_dauky+$tongtienco));
                   $this->excel->getActiveSheet()->getStyle('H'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                   /* end co cuoi ky */
		$this->excel->getActiveSheet()->getStyle('A'.$k.':H'. $k)->applyFromArray($styleDOTBlackBorderOutline);
		$this->excel->getActiveSheet()->getRowDimension($k)->setRowHeight(20);
		$k++;
		}
		
                $this->excel->getActiveSheet()->setCellValue('A'.($k), $this->City->get_info($code_city['id_city_code'])->name);
		$this->excel->getActiveSheet()->getStyle('A'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                /* tong no dau ky */
		$tongtienno_dauky_tong = 0;
		 for($m = 0; $m < count($array_tongtienno_dauky); $m++ ){
			$tongtienno_dauky_tong += $array_tongtienno_dauky[$m];
		 }
		 $array_tong_cot_tienno_dauky_tong += $tongtienno_dauky_tong ;
		$this->excel->getActiveSheet()->setCellValue('C'.($k), to_currency_unVND($tongtienno_dauky_tong));
		$this->excel->getActiveSheet()->getStyle('C'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        /* tong co dau ky */
		$tongtienco_dauky_tong = 0;
		 for($n = 0; $n < count($array_tongtienco_dauky); $n++ ){
			$tongtienco_dauky_tong += $array_tongtienco_dauky[$n];
		 }
		  $array_tong_cot_tienco_dauky_tong += $tongtienco_dauky_tong ;
		$this->excel->getActiveSheet()->setCellValue('D'.($k), to_currency_unVND($tongtienco_dauky_tong));
		$this->excel->getActiveSheet()->getStyle('D'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		
		 /* tong no phat sinh */
		$tongtienno_phatsinh = 0;
		 for($j = 0; $j < count($array_tongtienno_phatsinh); $j++ ){
			$tongtienno_phatsinh += $array_tongtienno_phatsinh[$j];
		 }
		  $array_tong_cot_tienno_phatsinh_tong += $tongtienno_phatsinh;
		$this->excel->getActiveSheet()->setCellValue('E'.($k), to_currency_unVND($tongtienno_phatsinh));
		$this->excel->getActiveSheet()->getStyle('E'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                /* tong co phat sinh */
		$tongtienco_phatsinh = 0;
		 for($k1 = 0; $k1 < count($array_tongtienco_phatsinh); $k1++ ){
			$tongtienco_phatsinh += $array_tongtienco_phatsinh[$k1];
		 }
		 $array_tong_cot_tienco_phatsinh_tong += $tongtienco_phatsinh ;
		$this->excel->getActiveSheet()->setCellValue('F'.($k), to_currency_unVND($tongtienco_phatsinh));
		$this->excel->getActiveSheet()->getStyle('F'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                /* tong no cuoi ky */
		 $tongtienno_cuoiky_tong = 0;
		 for($p = 0; $p < count($array_tongtienno_cuoiky); $p++ ){
			$tongtienno_cuoiky_tong += $array_tongtienno_cuoiky[$p];
		 }
		  $array_tong_cot_tienno_cuoiky_tong += $tongtienno_cuoiky_tong ;
		$this->excel->getActiveSheet()->setCellValue('G'.($k), to_currency_unVND($tongtienno_cuoiky_tong));
		$this->excel->getActiveSheet()->getStyle('G'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		/* tong co cuoi ky */
		 $tongtienco_cuoiky_tong = 0;
		 for($q = 0; $q < count($array_tongtienco_cuoiky); $q++ ){
			$tongtienco_cuoiky_tong += $array_tongtienco_cuoiky[$q];
		 }
		 $array_tong_cot_tienco_cuoiky_tong += $tongtienco_cuoiky_tong;
		$this->excel->getActiveSheet()->setCellValue('H'.($k), to_currency_unVND($tongtienco_cuoiky_tong));
                $this->excel->getActiveSheet()->getStyle('h'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$this->excel->getActiveSheet()->getStyle('A'.$k.':H'. $k)->applyFromArray($styleDOTBlackBorderTOPBOTOutline);
		$this->excel->getActiveSheet()->getStyle('A'.$k.':H'.$k)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getRowDimension($k)->setRowHeight(20);
$k++;
} } }

  $i = $this->excel->getActiveSheet()->getHighestRow() +3;
 
 /* style border cot */
$this->excel->getActiveSheet()->getStyle('A9:A'.($i-3))->getBorders()->getLEFT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('A9:A'.($i-3))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('B9:B'.($i-3))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('C9:C'.($i-3))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('D9:D'.($i-3))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('E9:E'.($i-3))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('F9:F'.($i-3))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('G9:G'.($i-3))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('H9:H'.($i-3))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

 
  $this->excel->getActiveSheet()->setCellValue('B' . ($i-2),"TỔNG CỘNG" );
  $this->excel->getActiveSheet()->getStyle('B'.($i-2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
 $this->excel->getActiveSheet()->setCellValue('C' . ($i-2),to_currency_unVND($array_tong_cot_tienno_dauky_tong) );
 $this->excel->getActiveSheet()->setCellValue('D' . ($i-2),to_currency_unVND($array_tong_cot_tienco_dauky_tong) );
 $this->excel->getActiveSheet()->setCellValue('E' . ($i-2), to_currency_unVND($array_tong_cot_tienno_phatsinh_tong));
 $this->excel->getActiveSheet()->setCellValue('F' . ($i-2), to_currency_unVND($array_tong_cot_tienco_phatsinh_tong));
 $this->excel->getActiveSheet()->setCellValue('G' . ($i-2), to_currency_unVND($array_tong_cot_tienno_cuoiky_tong));
 $this->excel->getActiveSheet()->setCellValue('H' . ($i-2), to_currency_unVND($array_tong_cot_tienco_cuoiky_tong));
 $this->excel->getActiveSheet()->getStyle('C' . ($i-2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
 $this->excel->getActiveSheet()->getStyle('G'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
 $this->excel->getActiveSheet()->getStyle('D' . ($i-2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
 $this->excel->getActiveSheet()->getStyle('E' . ($i-2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
 $this->excel->getActiveSheet()->getStyle('F' . ($i-2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
 $this->excel->getActiveSheet()->getStyle('G' . ($i-2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
 $this->excel->getActiveSheet()->getStyle('H' . ($i-2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
 $this->excel->getActiveSheet()->getStyle('A'.($i-2).':H'.($i-2))->applyFromArray($styleThinBlackBorderOutline);
 $this->excel->getActiveSheet()->getStyle('A'.($i-2).':H'.($i-2))->getFont()->setBold(true);
 $this->excel->getActiveSheet()->getStyle('A'.($i-2).':H'.($i-2))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
 $this->excel->getActiveSheet()->getRowDimension($i-2)->setRowHeight(18.25);
 
$this->excel->getActiveSheet()->setCellValue('G' . ($i+2), 'Ngày ......./......./.............');
 
 $this->excel->getActiveSheet()->setCellValue('B' . ($i+4), 'NGƯỜI LẬP BIỂU');
 $this->excel->getActiveSheet()->setCellValue('G' . ($i+4), 'KẾ TOÁN TRƯỞNG');
 $this->excel->getActiveSheet()->getStyle('A'.($i+4).':I'.($i+4))->getFont()->setBold(true);
 
 $this->excel->getActiveSheet()->setCellValue('B' . ($i+5), '(Ký, ghi họ tên)');
 $this->excel->getActiveSheet()->setCellValue('G' . ($i+5), '(Ký, ghi họ tên)');
 $this->excel->getActiveSheet()->getStyle('B'.($i+5))->getFont()->setItalic(true);
 $this->excel->getActiveSheet()->getStyle('G'.($i+5))->getFont()->setItalic(true);

 
$this->excel->getActiveSheet()->getHeaderFooter()->setOddFooter('&RPage &P of &N');
$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(52.14);
$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(13.29);
$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(13.29);
$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(16.3);
$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(16.3);
$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(16.3);
$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(16.3);

 

//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
//if you want to save it as .XLSX Excel 2007 format
$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $md5file = md5(date('YmdHis')) . '.xlsx';
//$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
//force user to download the Excel file without writing it to server's HD
$filename = 'baocao_congno.xlsx'; //save our workbook as this file name


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