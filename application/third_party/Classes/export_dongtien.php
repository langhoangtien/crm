<?php
$this->load->library('Excel');
$objPHPExcel = new PHPExcel();
$this->excel->getActiveSheet()->setShowGridlines(false);
$this->excel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Times New Roman');
$this->excel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(11);
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
$this->excel->getActiveSheet()->setTitle('Báo cáo dòng tiền');
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

	$this->excel->getActiveSheet()->setCellValue('B4', "BÁO CÁO LƯU CHUYỂN TIỀN TỆ");
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
	$this->excel->getActiveSheet()->setCellValue('A7', "Chỉ tiêu");
	$this->excel->getActiveSheet()->setCellValue('B7', "Mã số");
	$this->excel->getActiveSheet()->setCellValue('C7', "Thuyết minh");
	$this->excel->getActiveSheet()->setCellValue('D7', "Năm nay");
	$this->excel->getActiveSheet()->setCellValue('E7', "Năm trước");	$this->excel->getActiveSheet()->setCellValue('F7', "Tài khoản nợ");
	$this->excel->getActiveSheet()->setCellValue('G7', "Tài khoản có");
	$this->excel->getActiveSheet()->setCellValue('H7', "Công thức");
/* style */
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
/* style */
for($heightrow = 8;$heightrow<= 37;$heightrow++){
$this->excel->getActiveSheet()->getRowDimension($heightrow)->setRowHeight(18);
}
for($borderdot = 8;$borderdot<= 36;$borderdot++){
$this->excel->getActiveSheet()->getStyle('A'.$borderdot.':H'.$borderdot )->applyFromArray($styleDOTBlackBorderOutline);
}
$this->excel->getActiveSheet()->getStyle('A8:A36')->getBorders()->getLEFT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('A8:A36')->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('B8:B36')->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('C8:C36')->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('D8:D36')->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('E8:E36')->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('F8:F36')->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('G8:G36')->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('H8:H36')->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('A7:H7')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A7:H7' )->applyFromArray($styleThinBlackBorderOutline);
$this->excel->getActiveSheet()->getStyle('A37:H37' )->applyFromArray($styleThinBlackBorderOutline);
$this->excel->getActiveSheet()->getStyle('A7:H7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getRowDimension(7)->setRowHeight(28.50);
$this->excel->getActiveSheet()->getStyle('B7:B36')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A7:H7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
/* end style */
/* end tieu de cac cot *//* luu chuyen tu hoan dong kinh doanh */
$this->excel->getActiveSheet()->getStyle('D9:D37')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$dongtiens01 = array(111,112,511,515,121,131,3331);
$dongtiens02 = array(152,153,154,156,331,121,1331,632,642,33312,111,112,311,131);
$dongtiens03 = array(334,141,111,112);
$dongtiens04 = array(335,635,142,242,111,112,131);
$dongtiens05 = array(3334,111,112,131);
$dongtiens06 = array(111,112);
$dongtiens07 = array(111,112);
$dongtiens01 = $this->Cost->find_export_dongtiens($start_date,$end_date,$dongtiens01) ;
if($dongtiens01 != null){
	foreach ($dongtiens01 as $dongtien01){
		$tkdongtien01 += ($dongtien01['tien_thu'] - $dongtien01['tien_chi']);
	}
}else $tkdongtien01 = 0;

$dongtiens02 = $this->Cost->find_export_dongtiens($start_date,$end_date,$dongtiens02) ;
if($dongtiens02 != null){
	foreach ($dongtiens02 as $dongtien02){
		$tkdongtien02 += ($dongtien02['tien_thu'] - $dongtien02['tien_chi']);
	}
}else $tkdongtien02 = 0;

$dongtiens03 = $this->Cost->find_export_dongtiens($start_date,$end_date,$dongtiens03) ;
if($dongtiens03 != null){
	foreach ($dongtiens03 as $dongtien03){
		$tkdongtien03 += ($dongtien03['tien_thu'] - $dongtien03['tien_chi']);
	}
}else $tkdongtien03 = 0;

$dongtiens04 = $this->Cost->find_export_dongtiens($start_date,$end_date,$dongtiens04) ;
if($dongtiens04 != null){
	foreach ($dongtiens04 as $dongtien04){
		$tkdongtien04 += ($dongtien04['tien_thu'] - $dongtien04['tien_chi']);
	}
}else $tkdongtien04 = 0;

$dongtiens05 = $this->Cost->find_export_dongtiens($start_date,$end_date,$dongtiens05) ;
if($dongtiens05 != null){
	foreach ($dongtiens05 as $dongtien05){
		$tkdongtien05 += ($dongtien05['tien_thu'] - $dongtien05['tien_chi']);
	}
}else $tkdongtien05 = 0;

$dongtiens06 = $this->Cost->find_export_dongtiens($start_date,$end_date,$dongtiens06) ;
if($dongtiens06 != null){
	foreach ($dongtiens06 as $dongtien06){
		$tkdongtien06 += ($dongtien06['tien_thu'] - $dongtien06['tien_chi']);
	}
}else $tkdongtien06 = 0;

$dongtiens07 = $this->Cost->find_export_dongtiens($start_date,$end_date,$dongtiens07) ;
if($dongtiens07 != null){
	foreach ($dongtiens07 as $dongtien07){
		$tkdongtien07 += ($dongtien07['tien_thu'] - $dongtien07['tien_chi']);
	}
}else $tkdongtien07 = 0;
$tong_0107 = $tkdongtien01 + $tkdongtien02 + $tkdongtien03 + $tkdongtien04 + $tkdongtien05 + $tkdongtien06 + $tkdongtien07;
/* end luu chuyen tu hoan dong kinh doanh */

	$this->excel->getActiveSheet()->setCellValue('A8', "I. Lưu chuyển tiền từ hoạt động kinh doanh");
	$this->excel->getActiveSheet()->setCellValue('B8', " ");
	$this->excel->getActiveSheet()->setCellValue('C8', " ");
	$this->excel->getActiveSheet()->setCellValue('D8', " ");
	$this->excel->getActiveSheet()->setCellValue('E8', " ");
	$this->excel->getActiveSheet()->getStyle('A8:H8')->getFont()->setBold(true);
	
	$this->excel->getActiveSheet()->setCellValue('A9', "  1. Tiền thu bán hàng, cung cấp dịch vụ và doanh thu khác");
	$this->excel->getActiveSheet()->setCellValue('B9', "1");
	$this->excel->getActiveSheet()->setCellValue('C9', " ");
	$this->excel->getActiveSheet()->setCellValue('D9', to_currency_unVND($tkdongtien01));
	$this->excel->getActiveSheet()->setCellValue('E9', " ");
	$this->excel->getActiveSheet()->setCellValue('F9', "111,112 ");
	$this->excel->getActiveSheet()->setCellValue('G9', "511,515,121,131, 3331");
	$this->excel->getActiveSheet()->setCellValue('H9', " ");
	
	$this->excel->getActiveSheet()->setCellValue('A10', "  2. Tiền chi trả cho người cung cấp hàng hóa và dịch vụ");
	$this->excel->getActiveSheet()->setCellValue('B10', "2");
	$this->excel->getActiveSheet()->setCellValue('C10', " ");
	$this->excel->getActiveSheet()->setCellValue('D10',to_currency_unVND($tkdongtien02));
	$this->excel->getActiveSheet()->setCellValue('E10', " ");
	$this->excel->getActiveSheet()->setCellValue('F10', "152,153,154,156,331,121,1331,632,642,33312 ");
	$this->excel->getActiveSheet()->setCellValue('G10', "111,112,311,131");
	$this->excel->getActiveSheet()->setCellValue('H10', " ");
	
	$this->excel->getActiveSheet()->setCellValue('A11', "  3. Tiền chi trả cho người lao động");
	$this->excel->getActiveSheet()->setCellValue('B11', "3");
	$this->excel->getActiveSheet()->setCellValue('C11', " ");
	$this->excel->getActiveSheet()->setCellValue('D11', to_currency_unVND($tkdongtien03));
	$this->excel->getActiveSheet()->setCellValue('E11', " ");
	$this->excel->getActiveSheet()->setCellValue('F11', "334,141 ");
	$this->excel->getActiveSheet()->setCellValue('G11', "111,112  ");
	$this->excel->getActiveSheet()->setCellValue('H11', " ");
	
	$this->excel->getActiveSheet()->setCellValue('A12', "  4. Tiền chi trả lãi vay");
	$this->excel->getActiveSheet()->setCellValue('B12', "4");
	$this->excel->getActiveSheet()->setCellValue('C12', " ");
	$this->excel->getActiveSheet()->setCellValue('D12', to_currency_unVND($tkdongtien04));
	$this->excel->getActiveSheet()->setCellValue('E12', " ");
	$this->excel->getActiveSheet()->setCellValue('F12', "335,635,142,242 ");
	$this->excel->getActiveSheet()->setCellValue('G12', "111,112,131");
	$this->excel->getActiveSheet()->setCellValue('H12', " ");
	
	$this->excel->getActiveSheet()->setCellValue('A13', "  5. Tiền chi nộp thuế thu nhập doanh nghiệp");
	$this->excel->getActiveSheet()->setCellValue('B13', "5");
	$this->excel->getActiveSheet()->setCellValue('C13', " ");
	$this->excel->getActiveSheet()->setCellValue('D13', to_currency_unVND($tkdongtien05));
	$this->excel->getActiveSheet()->setCellValue('E13', " ");
	$this->excel->getActiveSheet()->setCellValue('F13', "3334 ");
	$this->excel->getActiveSheet()->setCellValue('G13', "111,112,131");
	$this->excel->getActiveSheet()->setCellValue('H13', " ");
	
	$this->excel->getActiveSheet()->setCellValue('A14', "  6. Tiền thu khác từ hoạt động kinh doanh");
	$this->excel->getActiveSheet()->setCellValue('B14', "6");
	$this->excel->getActiveSheet()->setCellValue('C14', " ");
	$this->excel->getActiveSheet()->setCellValue('D14', to_currency_unVND($tkdongtien06));
	$this->excel->getActiveSheet()->setCellValue('E14', " ");
	$this->excel->getActiveSheet()->setCellValue('F14', "111,112 ");
	$this->excel->getActiveSheet()->setCellValue('G14', "#");
	$this->excel->getActiveSheet()->setCellValue('H14', " ");
	
	$this->excel->getActiveSheet()->setCellValue('A15', "  7. Tiền chi khác từ hoạt động kinh doanh");
	$this->excel->getActiveSheet()->setCellValue('B15', "7");
	$this->excel->getActiveSheet()->setCellValue('C15', " ");
	$this->excel->getActiveSheet()->setCellValue('D15', to_currency_unVND($tkdongtien07));
	$this->excel->getActiveSheet()->setCellValue('E15', " ");
	$this->excel->getActiveSheet()->setCellValue('F15', "#");
	$this->excel->getActiveSheet()->setCellValue('G15', "111,112");
	$this->excel->getActiveSheet()->setCellValue('H15', " ");
	
	$this->excel->getActiveSheet()->setCellValue('A16', "  Lưu chuyển tiền thuần từ hoạt động kinh doanh");
	$this->excel->getActiveSheet()->setCellValue('B16', "20");
	$this->excel->getActiveSheet()->setCellValue('C16', " ");
	$this->excel->getActiveSheet()->setCellValue('D16', to_currency_unVND($tong_0107));
	$this->excel->getActiveSheet()->setCellValue('E16', " ");
	$this->excel->getActiveSheet()->setCellValue('H16', "01+02+03+04+05+06+07 ");
	$this->excel->getActiveSheet()->getStyle('A16:H16')->getFont()->setBold(true);
/* luu chuyen tien tu hoat dong dau tu */
$dongtiens21 = array(211,217,241,111,112,341,131);
$dongtiens22A = array(111,112,7112);
$dongtiens22B = array(8112,111,112 );
$dongtiens25 = array(221,111,112 );
$dongtiens26 = array(221,111,112 );
$dongtiens27 = array(515,111,112 );
$dongtiens21 = $this->Cost->find_export_dongtiens($start_date,$end_date,$dongtiens21) ;
if($dongtiens21 != null){
	foreach ($dongtiens21 as $dongtien21){
		$tkdongtien21 += ($dongtien21['tien_thu'] - $dongtien21['tien_chi']);
	}
}else $tkdongtien21 = 0;

$dongtiens22A = $this->Cost->find_export_dongtiens($start_date,$end_date,$dongtiens22A) ;
if($dongtiens22A != null){
	foreach ($dongtiens22A as $dongtien22A){
		$tkdongtien22A += ($dongtien22A['tien_thu'] - $dongtien22A['tien_chi']);
	}
}else $tkdongtien22A = 0;

$dongtiens22B = $this->Cost->find_export_dongtiens($start_date,$end_date,$dongtiens22B) ;
if($dongtiens22B != null){
	foreach ($dongtiens22B as $dongtien22B){
		$tkdongtien22B += ($dongtien22B['tien_thu'] - $dongtien22B['tien_chi']);
	}
}else $tkdongtien22B = 0;

$dongtiens25 = $this->Cost->find_export_dongtiens($start_date,$end_date,$dongtiens25) ;
if($dongtiens25 != null){
	foreach ($dongtiens25 as $dongtien25){
		$tkdongtien25 += ($dongtien25['tien_thu'] - $dongtien25['tien_chi']);
	}
}else $tkdongtien25 = 0;

$dongtiens26 = $this->Cost->find_export_dongtiens($start_date,$end_date,$dongtiens26) ;
if($dongtiens26 != null){
	foreach ($dongtiens26 as $dongtien26){
		$tkdongtien26 += ($dongtien26['tien_thu'] - $dongtien26['tien_chi']);
	}
}else $tkdongtien26 = 0;

$dongtiens27 = $this->Cost->find_export_dongtiens($start_date,$end_date,$dongtiens27) ;
if($dongtiens27 != null){
	foreach ($dongtiens27 as $dongtien27){
		$tkdongtien27 += ($dongtien27['tien_thu'] - $dongtien27['tien_chi']);
	}
}else $tkdongtien27 = 0;
$tkdongtien22 = $tkdongtien22A + $tkdongtien22B ;
$tongtien2127 = $tkdongtien21 + $tkdongtien22 + $tkdongtien25 + $tkdongtien26 + $tkdongtien27;
/* end luu chuyen tien tu hoat dong dau tu */
	
	$this->excel->getActiveSheet()->setCellValue('A17', "II. Lưu chuyển từ hoạt động đầu tư");
	$this->excel->getActiveSheet()->setCellValue('B17', " ");
	$this->excel->getActiveSheet()->setCellValue('C17', " ");
	$this->excel->getActiveSheet()->setCellValue('D17', "");
	$this->excel->getActiveSheet()->setCellValue('E17', " ");
	$this->excel->getActiveSheet()->setCellValue('F17', "211,217,241");
	$this->excel->getActiveSheet()->setCellValue('G17', "111,112,341,131");
	$this->excel->getActiveSheet()->setCellValue('H17', " ");
	$this->excel->getActiveSheet()->getStyle('A17:H17')->getFont()->setBold(true);
	
	$this->excel->getActiveSheet()->setCellValue('A18', "  1. Tiền chi để mua sắm, xây dựng TSCĐ, BĐS đầu tư và các tài sản dài hạn khác");
	$this->excel->getActiveSheet()->setCellValue('B18', "21");
	$this->excel->getActiveSheet()->setCellValue('C18', " ");
	$this->excel->getActiveSheet()->setCellValue('D18', to_currency_unVND($tkdongtien21));
	$this->excel->getActiveSheet()->setCellValue('E18', " ");
	$this->excel->getActiveSheet()->setCellValue('H18', "22A+22B");
	
	$this->excel->getActiveSheet()->setCellValue('A19', "  2. Tiền thu từ t/lý, nhượng bán TSCĐ, BĐS đầu tư và các TS dài hạn khác");
	$this->excel->getActiveSheet()->setCellValue('B19', "22");
	$this->excel->getActiveSheet()->setCellValue('C19', " ");
	$this->excel->getActiveSheet()->setCellValue('D19', to_currency_unVND($tkdongtien22));
	$this->excel->getActiveSheet()->setCellValue('E19', " ");
	$this->excel->getActiveSheet()->setCellValue('F19', "111,112 ");
	$this->excel->getActiveSheet()->setCellValue('G19', "7112");
	$this->excel->getActiveSheet()->setCellValue('H19', " ");
	
	$this->excel->getActiveSheet()->setCellValue('A20', "  3. Tiền chi cho vay, mua các công cụ nợ của đơn vị khác");
	$this->excel->getActiveSheet()->setCellValue('B20', "23");
	$this->excel->getActiveSheet()->setCellValue('C20', " ");
	$this->excel->getActiveSheet()->setCellValue('D20', " " );
	$this->excel->getActiveSheet()->setCellValue('E20', " ");
	$this->excel->getActiveSheet()->setCellValue('F20', "X ");
	$this->excel->getActiveSheet()->setCellValue('G20', "X");
	$this->excel->getActiveSheet()->setCellValue('H20', " ");
	
	$this->excel->getActiveSheet()->setCellValue('A21', "  4. Tiền thu hồi cho vay, bán lại các công cụ nợ của đơn vị khá");
	$this->excel->getActiveSheet()->setCellValue('B21', "24");
	$this->excel->getActiveSheet()->setCellValue('C21', " ");
	$this->excel->getActiveSheet()->setCellValue('D21', " ");
	$this->excel->getActiveSheet()->setCellValue('E21', " ");
	$this->excel->getActiveSheet()->setCellValue('F21', "X ");
	$this->excel->getActiveSheet()->setCellValue('G21', "X");
	$this->excel->getActiveSheet()->setCellValue('H21', " ");
	
	$this->excel->getActiveSheet()->setCellValue('A22', "  5. Tiền chi đầu tư góp vốn vào đơn vị khác ");
	$this->excel->getActiveSheet()->setCellValue('B22', "25");
	$this->excel->getActiveSheet()->setCellValue('C22', " ");
	$this->excel->getActiveSheet()->setCellValue('D22', to_currency_unVND($tkdongtien25));
	$this->excel->getActiveSheet()->setCellValue('E22', " ");
	$this->excel->getActiveSheet()->setCellValue('F22', "221");
	$this->excel->getActiveSheet()->setCellValue('G22', "111,112");
	$this->excel->getActiveSheet()->setCellValue('H22', " ");
	
	$this->excel->getActiveSheet()->setCellValue('A23', "  6. Tiền thu hồi đầu tư góp vốn vào đơn vị khác ");
	$this->excel->getActiveSheet()->setCellValue('B23', "26");
	$this->excel->getActiveSheet()->setCellValue('C23', " ");
	$this->excel->getActiveSheet()->setCellValue('D23', to_currency_unVND($tkdongtien26));
	$this->excel->getActiveSheet()->setCellValue('E23', " ");
	$this->excel->getActiveSheet()->setCellValue('F23', "111,112 ");
	$this->excel->getActiveSheet()->setCellValue('G23', "221");
	$this->excel->getActiveSheet()->setCellValue('H23', " ");
	
	$this->excel->getActiveSheet()->setCellValue('A24', "  7. Tiền thu lãi cho vay, cổ tức và lợi nhuận được chia");
	$this->excel->getActiveSheet()->setCellValue('B24', "27");
	$this->excel->getActiveSheet()->setCellValue('C24', " ");
	$this->excel->getActiveSheet()->setCellValue('D24', to_currency_unVND($tkdongtien27));
	$this->excel->getActiveSheet()->setCellValue('E24', " ");
	$this->excel->getActiveSheet()->setCellValue('F24', "111,112 ");
	$this->excel->getActiveSheet()->setCellValue('G24', "515 ");
	$this->excel->getActiveSheet()->setCellValue('H24', " ");
	
	$this->excel->getActiveSheet()->setCellValue('A25', " Lưu chuyển tiền thuần từ hoạt động đầu tư");
	$this->excel->getActiveSheet()->setCellValue('B25', "30");
	$this->excel->getActiveSheet()->setCellValue('C25', " ");
	$this->excel->getActiveSheet()->setCellValue('D25', to_currency_unVND($tongtien2127));
	$this->excel->getActiveSheet()->setCellValue('E25', " ");
	$this->excel->getActiveSheet()->setCellValue('H25', "21+22+23+24+25+26+27");
	$this->excel->getActiveSheet()->getStyle('A25:H25')->getFont()->setBold(true);
/* luu hcuyen tien tu hoat dong tai chinh */

$dongtiens31 = array(4111,111,112 );
$dongtiens32 = array(411,419,111,112 );
$dongtiens33 = array(111,112,331,311,341,171);
$dongtiens34 = array(311,341,315,171,111,112,131);
$dongtiens35 = array(315,34122,111,112,131);
$dongtiens36 = array(421,111,112 );
$dongtiens31 = $this->Cost->find_export_dongtiens($start_date,$end_date,$dongtiens31) ;
if($dongtiens31 != null){
	foreach ($dongtiens31 as $dongtien31){
		$tkdongtien31 += ($dongtien31['tien_thu'] - $dongtien31['tien_chi']);
	}
}else $tkdongtien31 = 0;

$dongtiens32 = $this->Cost->find_export_dongtiens($start_date,$end_date,$dongtiens32) ;
if($dongtiens32 != null){
	foreach ($dongtiens32 as $dongtien32){
		$tkdongtien32 += ($dongtien32['tien_thu'] - $dongtien32['tien_chi']);
	}
}else $tkdongtien32 = 0;

$dongtiens33 = $this->Cost->find_export_dongtiens($start_date,$end_date,$dongtiens33) ;
if($dongtiens33 != null){
	foreach ($dongtiens33 as $dongtien33){
		$tkdongtien33 += ($dongtien33['tien_thu'] - $dongtien33['tien_chi']);
	}
}else $tkdongtien33 = 0;

$dongtiens34 = $this->Cost->find_export_dongtiens($start_date,$end_date,$dongtiens34) ;
if($dongtiens34 != null){
	foreach ($dongtiens34 as $dongtien34){
		$tkdongtien34 += ($dongtien34['tien_thu'] - $dongtien34['tien_chi']);
	}
}else $tkdongtien34 = 0;

$dongtiens35 = $this->Cost->find_export_dongtiens($start_date,$end_date,$dongtiens35) ;
if($dongtiens35 != null){
	foreach ($dongtiens35 as $dongtien35){
		$tkdongtien35 += ($dongtien35['tien_thu'] - $dongtien35['tien_chi']);
	}
}else $tkdongtien35 = 0;

$dongtiens36 = $this->Cost->find_export_dongtiens($start_date,$end_date,$dongtiens36) ;
if($dongtiens36 != null){
	foreach ($dongtiens36 as $dongtien36){
		$tkdongtien36 += ($dongtien36['tien_thu'] - $dongtien36['tien_chi']);
	}
}else $tkdongtien36 = 0;
$tongtien3136 = $tkdongtien31 + $tkdongtien36 + $tkdongtien32 + $tkdongtien33 + $tkdongtien34 + $tkdongtien35;
/* endluu chuyen tien tu hoat dong tai chinh */
 	$this->excel->getActiveSheet()->setCellValue('A26', "III. Lưu chuyển tiền từ hoạt động tài chính");
	$this->excel->getActiveSheet()->setCellValue('B26', " ");
	$this->excel->getActiveSheet()->setCellValue('C26', " ");
	$this->excel->getActiveSheet()->setCellValue('D26', "");
	$this->excel->getActiveSheet()->setCellValue('E26', " ");
	$this->excel->getActiveSheet()->getStyle('A26:H26')->getFont()->setBold(true);
	
	$this->excel->getActiveSheet()->setCellValue('A27', "  1. Tiền thu từ phát hành cổ phiếu, nhận vốn góp của chủ sở hữu ");
	$this->excel->getActiveSheet()->setCellValue('B27', "31");
	$this->excel->getActiveSheet()->setCellValue('C27', " ");
	$this->excel->getActiveSheet()->setCellValue('D27', to_currency_unVND($tkdongtien31));
	$this->excel->getActiveSheet()->setCellValue('E27', " ");
	$this->excel->getActiveSheet()->setCellValue('F27', "111,112 ");
	$this->excel->getActiveSheet()->setCellValue('G27', "515 ");
	$this->excel->getActiveSheet()->setCellValue('H27', " ");
	
	$this->excel->getActiveSheet()->setCellValue('A28', "  2. Tiền chi trả v/góp cho các CSH, mua lại CP của DN đã p/hành ");
	$this->excel->getActiveSheet()->setCellValue('B28', "32");
	$this->excel->getActiveSheet()->setCellValue('C28', " ");
	$this->excel->getActiveSheet()->setCellValue('D28', to_currency_unVND($tkdongtien32));
	$this->excel->getActiveSheet()->setCellValue('E28', " ");
	$this->excel->getActiveSheet()->setCellValue('F28', "411,419 ");
	$this->excel->getActiveSheet()->setCellValue('G28', "111,112");
	$this->excel->getActiveSheet()->setCellValue('H28', " ");
	
	$this->excel->getActiveSheet()->setCellValue('A29', "  3. Tiền vay ngắn hạn, dài hạn nhận được ");
	$this->excel->getActiveSheet()->setCellValue('B29', "33");
	$this->excel->getActiveSheet()->setCellValue('C29', " ");
	$this->excel->getActiveSheet()->setCellValue('D29', to_currency_unVND($tkdongtien33));
	$this->excel->getActiveSheet()->setCellValue('E29', " ");
	$this->excel->getActiveSheet()->setCellValue('F29', "111,112,331 ");
	$this->excel->getActiveSheet()->setCellValue('G29', "311,341, 171");
	$this->excel->getActiveSheet()->setCellValue('H29', " ");
	
	$this->excel->getActiveSheet()->setCellValue('A30', "  4. Tiền chi trả nợ gốc vay ");
	$this->excel->getActiveSheet()->setCellValue('B30', "34");
	$this->excel->getActiveSheet()->setCellValue('C30', " ");
	$this->excel->getActiveSheet()->setCellValue('D30', to_currency_unVND($tkdongtien34));
	$this->excel->getActiveSheet()->setCellValue('E30', " ");
	$this->excel->getActiveSheet()->setCellValue('F30', "311,341,315,171");
	$this->excel->getActiveSheet()->setCellValue('G30', "111,112,131");
	$this->excel->getActiveSheet()->setCellValue('H30', " ");
	
	$this->excel->getActiveSheet()->setCellValue('A31', "  5. Tiền chi trả nợ thuê tài chính ");
	$this->excel->getActiveSheet()->setCellValue('B31', "35");
	$this->excel->getActiveSheet()->setCellValue('C31', " ");
	$this->excel->getActiveSheet()->setCellValue('D31', to_currency_unVND($tkdongtien35));
	$this->excel->getActiveSheet()->setCellValue('E31', " ");
	$this->excel->getActiveSheet()->setCellValue('F31', "315,34122 ");
	$this->excel->getActiveSheet()->setCellValue('G31', "111,112,131");
	$this->excel->getActiveSheet()->setCellValue('H31', " ");
	
	$this->excel->getActiveSheet()->setCellValue('A32', "  6. Cổ tức, lợi nhuận đã trả cho chủ sở hữu");
	$this->excel->getActiveSheet()->setCellValue('B32', "36");
	$this->excel->getActiveSheet()->setCellValue('C32', " ");
	$this->excel->getActiveSheet()->setCellValue('D32', to_currency_unVND($tkdongtien36));
	$this->excel->getActiveSheet()->setCellValue('E32', " ");
	$this->excel->getActiveSheet()->setCellValue('F32', "421");
	$this->excel->getActiveSheet()->setCellValue('G32', "111,112");
	$this->excel->getActiveSheet()->setCellValue('H32', " ");
	
	$this->excel->getActiveSheet()->setCellValue('A33', "  Lưu chuyển tiền thuần từ hoạt động tài chính  ");
	$this->excel->getActiveSheet()->setCellValue('B33', "40");
	$this->excel->getActiveSheet()->setCellValue('C33', " ");
	$this->excel->getActiveSheet()->setCellValue('D33', to_currency_unVND($tongtien3136));
	$this->excel->getActiveSheet()->setCellValue('E33', " ");
	$this->excel->getActiveSheet()->setCellValue('H33', " 31+32+33+34+35+36   ");
	$this->excel->getActiveSheet()->getStyle('A33:H33')->getFont()->setBold(true);
	
	$this->excel->getActiveSheet()->setCellValue('A34', "  Lưu chuyển tiền thuần trong năm (50 = 20+30+40)   ");
	$this->excel->getActiveSheet()->setCellValue('B34', "50");
	$this->excel->getActiveSheet()->setCellValue('C34', " ");
	$this->excel->getActiveSheet()->setCellValue('D34', to_currency_unVND($tongtien3136 + $tongtien2127 + $tong_0107));
	$this->excel->getActiveSheet()->setCellValue('E34', " ");
	$this->excel->getActiveSheet()->setCellValue('H34', "20+30+40 ");
	$this->excel->getActiveSheet()->getStyle('A34:H34')->getFont()->setBold(true);
	
	$this->excel->getActiveSheet()->setCellValue('A35', "  Tiền và tương đương tiền đầu năm  ");
	$this->excel->getActiveSheet()->setCellValue('B35', "60");
	$this->excel->getActiveSheet()->setCellValue('C35', " ");
	$this->excel->getActiveSheet()->setCellValue('D35', "");
	$this->excel->getActiveSheet()->setCellValue('E35', " ");
	$this->excel->getActiveSheet()->getStyle('A35:H35')->getFont()->setBold(true);
	
$dongtiens61A = array(111,112,12113,413);
$dongtiens61A = $this->Cost->find_export_dongtiens($start_date,$end_date,$dongtiens61A) ;
if($dongtiens61A != null){
	foreach ($dongtiens61A as $dongtien61A){
		$tkdongtien61A += ($dongtien61A['tien_thu'] - $dongtien61A['tien_chi']);
	}
}else $tkdongtien61A = 0;

$dongtiens61B = array(111,112,12113,413);
$dongtiens61B = $this->Cost->find_export_dongtiens($start_date,$end_date,$dongtiens61B) ;
if($dongtiens61B != null){
	foreach ($dongtiens61B as $dongtien61B){
		$tkdongtien61B += ($dongtien61B['tien_thu'] - $dongtien61B['tien_chi']);
	}
}else $tkdongtien61B = 0;


	$this->excel->getActiveSheet()->setCellValue('A36', " Ảnh hưởng của thay đổi tỷ giá hối đoái quy đổi ngoại tệ   ");
	$this->excel->getActiveSheet()->setCellValue('B36', "61");
	$this->excel->getActiveSheet()->setCellValue('C36', " ");
	$this->excel->getActiveSheet()->setCellValue('D36', to_currency_unVND($tkdongtien61A + $tkdongtien61B ));
	$this->excel->getActiveSheet()->setCellValue('E36', " ");
	$this->excel->getActiveSheet()->setCellValue('H36', "61A+61B ");
	$this->excel->getActiveSheet()->getStyle('A36:H36')->getFont()->setBold(true);
	
	$this->excel->getActiveSheet()->setCellValue('A37', " Tiền và tương đương tiền cuối năm (70 = 50+60+61)  ");
	$this->excel->getActiveSheet()->setCellValue('B37', "70");
	$this->excel->getActiveSheet()->setCellValue('C37', " ");
	$this->excel->getActiveSheet()->setCellValue('D37', to_currency_unVND($tkdongtien61A + $tkdongtien61B + $tongtien3136 + $tongtien2127 + $tong_0107));
	$this->excel->getActiveSheet()->setCellValue('E37', " ");
	$this->excel->getActiveSheet()->getStyle('A37:H37')->getFont()->setBold(true);
	

/* */
$this->excel->getActiveSheet()->getHeaderFooter()->setOddFooter('&RPage &P of &N');
$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(68.17);
$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(6.15);
$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(19.67);
$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(45);
$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(27);
$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(45);

//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
//if you want to save it as .XLSX Excel 2007 format
$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $md5file = md5(date('YmdHis')) . '.xlsx';
//$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
//force user to download the Excel file without writing it to server's HD
$filename = 'baocao_dongtien.xlsx'; //save our workbook as this file name
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