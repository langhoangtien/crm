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
$this->excel->getActiveSheet()->setTitle('Báo cáo cân đối tài chính');
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

	$this->excel->getActiveSheet()->setCellValue('B4', "BẢNG CÂN ĐỐI KẾ TOÁN");
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
	$this->excel->getActiveSheet()->setCellValue('D7', "Số cuối kỳ");
	$this->excel->getActiveSheet()->setCellValue('E7', "Số đầu kỳ");
	$this->excel->getActiveSheet()->setCellValue('F7', "Công nợ");	$this->excel->getActiveSheet()->setCellValue('G7', "Tài khoản");
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
$this->excel->getActiveSheet()->getStyle('A7:H7')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A7:H60' )->applyFromArray($styleThinBlackBorderOutline);
$this->excel->getActiveSheet()->getStyle('A7:F7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getRowDimension(7)->setRowHeight(22.25);
$this->excel->getActiveSheet()->getStyle('A7:F7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
/* end tieu de cac cot *//* tai san ngan han */
$this->excel->getActiveSheet()->getStyle('D9:D59')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$costs111 = $this->Cost->find_export_tkdu($start_date,$end_date,111) ;
if($costs111 != null){
	foreach ($costs111 as $cost111){
		$tkcost111 += ($cost111['tien_thu'] - $cost111['tien_chi']);
	}
}else $tkcost111 = 0;

//chi tiền khi nhập hàng lấy tk 111
//$costs111_1 = $this->Cost->find_export_reciprocal($start_date,$end_date,111) ;
//if($costs111_1 != null){
//	foreach ($costs111_1 as $cost111){
//		$tkcost111_1 += ($cost111['tien_thu'] - $cost111['tien_chi']);
//	}
//}else $tkcost111_1 = 0;

$costs156 = $this->Cost->find_export_tkdu($start_date,$end_date,156) ;
if($costs156 != null){
	foreach ($costs156 as $cost156){
		$tkcost156 += ($cost156['tien_thu'] - $cost156['tien_chi']);
	}
}else $tkcost156 = 0;


$costs121 = $this->Cost->find_export_tkdu($start_date,$end_date,121) ;
if($costs121 != null){
	foreach ($costs121 as $cost121){
		$tkcost121 += ($cost112['tien_thu'] - $cost121['tien_chi']);
	}
}else $tkcost121 = 0;

$costs112 = $this->Cost->find_export_tkdu($start_date,$end_date,112) ;
if($costs112 != null){
	foreach ($costs112 as $cost112){
		$tkcost112 += ($cost112['tien_thu'] - $cost112['tien_chi']);
	}
}else $tkcost112 = 0;

$costs1591 = $this->Cost->find_export_tkdu($start_date,$end_date,1591) ;
if($costs1591 != null){
	foreach ($costs1591 as $cost1591){
		$tkcost1591 += ($cost1591['tien_thu'] - $cost1591['tien_chi']);
	}
}else $tkcost1591 = 0;
	$this->excel->getActiveSheet()->setCellValue('A8', "A. Tài sản ngắn hạn (100=110+120+130+140+150)");
	$this->excel->getActiveSheet()->setCellValue('B8', "100");
	$this->excel->getActiveSheet()->setCellValue('C8', " ");
	$this->excel->getActiveSheet()->setCellValue('D8', " ");
	$this->excel->getActiveSheet()->setCellValue('E8', " ");
	$this->excel->getActiveSheet()->setCellValue('F8', " ");
	
	$this->excel->getActiveSheet()->setCellValue('A9', " I. Tiền và các khoản tương đương tiền");
	$this->excel->getActiveSheet()->setCellValue('B9', "110");
	$this->excel->getActiveSheet()->setCellValue('C9', " ");
	$this->excel->getActiveSheet()->setCellValue('D9', " ");
	$this->excel->getActiveSheet()->setCellValue('E9', " ");
	$this->excel->getActiveSheet()->setCellValue('F9', " ");
	
	$this->excel->getActiveSheet()->setCellValue('A10', "   1.Tiền mặt");
	$this->excel->getActiveSheet()->setCellValue('B10', "110A");
	$this->excel->getActiveSheet()->setCellValue('C10', " ");
	$this->excel->getActiveSheet()->setCellValue('D10',to_currency_unVND_nomar($tkcost111));
	$this->excel->getActiveSheet()->setCellValue('E10', " ");
	$this->excel->getActiveSheet()->setCellValue('F10', " ");
	
	$this->excel->getActiveSheet()->setCellValue('A11', "   2.Tiền gởi ngân hàng");
	$this->excel->getActiveSheet()->setCellValue('B11', "110B");
	$this->excel->getActiveSheet()->setCellValue('C11', " ");
	$this->excel->getActiveSheet()->setCellValue('D11', to_currency_unVND_nomar($tkcost112));
	$this->excel->getActiveSheet()->setCellValue('E11', " ");
	$this->excel->getActiveSheet()->setCellValue('F11', " ");
	
	$this->excel->getActiveSheet()->setCellValue('A12', " II. Đầu tư tài chính ngắn hạn");
	$this->excel->getActiveSheet()->setCellValue('B12', "120");
	$this->excel->getActiveSheet()->setCellValue('C12', " ");
	$this->excel->getActiveSheet()->setCellValue('D12', " ");
	$this->excel->getActiveSheet()->setCellValue('E12', " ");
	$this->excel->getActiveSheet()->setCellValue('F12', " ");
	
	$this->excel->getActiveSheet()->setCellValue('A13', "   1.Đầu tư tài chính ngắn hạn");
	$this->excel->getActiveSheet()->setCellValue('B13', "121");
	$this->excel->getActiveSheet()->setCellValue('C13', " ");
	$this->excel->getActiveSheet()->setCellValue('D13', to_currency_unVND_nomar($tkcost121));
	$this->excel->getActiveSheet()->setCellValue('E13', " ");
	$this->excel->getActiveSheet()->setCellValue('F13', " ");
	
	$this->excel->getActiveSheet()->setCellValue('A14', "   2.Dự phòng giảm giá đầu tư tài chính ngắn hạn");
	$this->excel->getActiveSheet()->setCellValue('B14', "129");
	$this->excel->getActiveSheet()->setCellValue('C14', " ");
	$this->excel->getActiveSheet()->setCellValue('D14', to_currency_unVND_nomar($tkcost1591));
	$this->excel->getActiveSheet()->setCellValue('E14', " ");
	$this->excel->getActiveSheet()->setCellValue('F14', " ");
	
	$this->excel->getActiveSheet()->setCellValue('A15', "III. Các khoản phải thu ngắn hạn");
	$this->excel->getActiveSheet()->setCellValue('B15', "130");
	$this->excel->getActiveSheet()->setCellValue('C15', " ");
	$this->excel->getActiveSheet()->setCellValue('D15', "");
	$this->excel->getActiveSheet()->setCellValue('E15', " ");
	$this->excel->getActiveSheet()->setCellValue('F15', " ");

$costs1311 = $this->Cost->find_export_tkdu($start_date,$end_date,1311) ;
if($costs1311 != null){
	foreach ($costs1311 as $cost1311){
		$cost1311 += ($cost1311['tien_thu'] - $cost1311['tien_chi']);
	}
}else $costs1311 = 0;	
	$this->excel->getActiveSheet()->setCellValue('A16', " 1.Phải thu của khách hàng");
	$this->excel->getActiveSheet()->setCellValue('B16', "131");
	$this->excel->getActiveSheet()->setCellValue('C16', " ");
	$this->excel->getActiveSheet()->setCellValue('D16', to_currency_unVND_nomar($costs1311));
	$this->excel->getActiveSheet()->setCellValue('E16', " ");
	$this->excel->getActiveSheet()->setCellValue('F16', " ");

$costs3311 = $this->Cost->find_export_tkdu($start_date,$end_date,331) ;
if($costs3311 != null){
	foreach ($costs3311 as $cost33111){
		$cost3311 += ($cost3311['tien_thu'] - $cost3311['tien_chi']);
	}
}else $costs3311 = 0;
	$this->excel->getActiveSheet()->setCellValue('A17', " 2.Trả trước cho người bán");
	$this->excel->getActiveSheet()->setCellValue('B17', "132");
	$this->excel->getActiveSheet()->setCellValue('C17', " ");
	$this->excel->getActiveSheet()->setCellValue('D17', to_currency_unVND_nomar($costs3311));
	$this->excel->getActiveSheet()->setCellValue('E17', " ");
	$this->excel->getActiveSheet()->setCellValue('F17', " ");

	$this->excel->getActiveSheet()->setCellValue('A18', " 3.Các khoản phải thu khác");
	$this->excel->getActiveSheet()->setCellValue('B18', "138");
	$this->excel->getActiveSheet()->setCellValue('C18', " ");
	$this->excel->getActiveSheet()->setCellValue('D18', "");
	$this->excel->getActiveSheet()->setCellValue('E18', " ");
	$this->excel->getActiveSheet()->setCellValue('F18', " ");

$costs13881 = $this->Cost->find_export_tkdu($start_date,$end_date,13881) ;
if($costs13881 != null){
	foreach ($costs13881 as $cost13881){
		$cost13881 += ($cost13881['tien_thu'] - $cost13881['tien_chi']);
	}
}else $costs13881 = 0;	
$this->excel->getActiveSheet()->setCellValue('A19', "  - Phải thu khác (1388)");
	$this->excel->getActiveSheet()->setCellValue('B19', "138A");
	$this->excel->getActiveSheet()->setCellValue('C19', " ");
	$this->excel->getActiveSheet()->setCellValue('D19', $costs13881);
	$this->excel->getActiveSheet()->setCellValue('E19', " ");
	$this->excel->getActiveSheet()->setCellValue('F19', "1 ");
	$this->excel->getActiveSheet()->setCellValue('G19', "13881");
	$this->excel->getActiveSheet()->setCellValue('H19', " ");
	
$costs33882 = $this->Cost->find_export_tkdu($start_date,$end_date,33882) ;
if($costs33882 != null){
	foreach ($costs33882 as $cost33882){
		$cost33882 += ($cost33882['tien_thu'] - $cost33882['tien_chi']);
	}
}else $costs33882 = 0;	
$this->excel->getActiveSheet()->setCellValue('A20', "  - Phải thu khác (33882)");
	$this->excel->getActiveSheet()->setCellValue('B20', "138B");
	$this->excel->getActiveSheet()->setCellValue('C20', " ");
	$this->excel->getActiveSheet()->setCellValue('D20', $costs33882);
	$this->excel->getActiveSheet()->setCellValue('E20', " ");
	$this->excel->getActiveSheet()->setCellValue('F20', "1 ");
	$this->excel->getActiveSheet()->setCellValue('G20', "33882");
	$this->excel->getActiveSheet()->setCellValue('H20', " ");

$costs334 = $this->Cost->find_export_tkdu($start_date,$end_date,334) ;
if($costs334 != null){
	foreach ($costs334 as $cost334){
		$cost334 += ($cost334['tien_thu'] - $cost334['tien_chi']);
	}
}else $costs334 = 0;	
$this->excel->getActiveSheet()->setCellValue('A21', "  - Phải thu khác (334)");
	$this->excel->getActiveSheet()->setCellValue('B21', "138C");
	$this->excel->getActiveSheet()->setCellValue('C21', " ");
	$this->excel->getActiveSheet()->setCellValue('D21', $costs334);
	$this->excel->getActiveSheet()->setCellValue('E21', " ");
	$this->excel->getActiveSheet()->setCellValue('F21', "0 ");
	$this->excel->getActiveSheet()->setCellValue('G21', "334");
	$this->excel->getActiveSheet()->setCellValue('H21', " ");

$costs15921 = $this->Cost->find_export_tkdu($start_date,$end_date,15921) ;
if($costs15921 != null){
	foreach ($costs15921 as $cost15921){
		$cost15921 += ($cost15921['tien_thu'] - $cost15921['tien_chi']);
	}
}else $costs15921 = 0;	
$this->excel->getActiveSheet()->setCellValue('A22', " 4.Dự phòng phải thu ngắn hạn khó đòi");
	$this->excel->getActiveSheet()->setCellValue('B22', "138C");
	$this->excel->getActiveSheet()->setCellValue('C22', " ");
	$this->excel->getActiveSheet()->setCellValue('D22', $costs15921);
	$this->excel->getActiveSheet()->setCellValue('E22', " ");
	$this->excel->getActiveSheet()->setCellValue('F22', "0 ");
	$this->excel->getActiveSheet()->setCellValue('G22', "334");
	$this->excel->getActiveSheet()->setCellValue('H22', " ");

$this->excel->getActiveSheet()->setCellValue('A23', "IV. Hàng tồn kho");
	$this->excel->getActiveSheet()->setCellValue('B23', "140");
	$this->excel->getActiveSheet()->setCellValue('C23', " ");
	$this->excel->getActiveSheet()->setCellValue('D23', $costs15921);
	$this->excel->getActiveSheet()->setCellValue('E23', " ");
	$this->excel->getActiveSheet()->setCellValue('F23', "0 ");
	$this->excel->getActiveSheet()->setCellValue('G23', "");
	$this->excel->getActiveSheet()->setCellValue('H23', "141+149 ");

$this->excel->getActiveSheet()->setCellValue('A24', " 1.Hàng tồn kho");
	$this->excel->getActiveSheet()->setCellValue('B24', "141");
	$this->excel->getActiveSheet()->setCellValue('C24', " ");
	$this->excel->getActiveSheet()->setCellValue('D24', " ");
	$this->excel->getActiveSheet()->setCellValue('E24', " ");
	$this->excel->getActiveSheet()->setCellValue('F24', "0 ");
	$this->excel->getActiveSheet()->setCellValue('G24', " ");
	$this->excel->getActiveSheet()->setCellValue('H24', " 141A+141B+141C+141D+141E+141F");
	
$this->excel->getActiveSheet()->setCellValue('A29', " Hàng hóa tồn kho");
	$this->excel->getActiveSheet()->setCellValue('B29', "141E");
	$this->excel->getActiveSheet()->setCellValue('C29', " ");
	$this->excel->getActiveSheet()->setCellValue('D29',to_currency_unVND_nomar($tkcost156));
	$this->excel->getActiveSheet()->setCellValue('E29', " ");
	$this->excel->getActiveSheet()->setCellValue('F29', "0");
	$this->excel->getActiveSheet()->setCellValue('G29', "156");

$array_htk = array(
	'25'=> array(
			'name'=>'    Nguyên liệu, vật liệu tồn kho',
			'tk'=>'152',
			'maso'=>'141A',
			'congno'=>'0',
			),
	'26'=> array(
			'name'=>'    Công cụ, dụng cụ trong kho',
			'tk'=>'153',
			'maso'=>'141B',
			'congno'=>'0',
			),
	'27'=> array(
			'name'=>'    Chi phí sản xuất dở dang',
			'tk'=>'154',
			'maso'=>'141C',
			'congno'=>'0',
			),
	'28'=> array(
			'name'=>'    Thành phẩm tồn kho',
			'tk'=>'155',
			'maso'=>'141D',
			'congno'=>'0',
			),
//	'29'=> array(
//			'name'=>'    Hàng hóa tồn kho',
//			'tk'=>'156',
//			'maso'=>'141E',
//			'congno'=>'0',
//			),
	'30'=> array(
			'name'=>'    Hàng gửi đi bán',
			'tk'=>'157',
			'maso'=>'141F',
			'congno'=>'0',
			),
	'31'=> array(
			'name'=>'  2.Dự phòng giảm giá hàng tồn kho(*)',
			'tk'=>'1593',
			'maso'=>'149',
			'congno'=>'0',
			),
);

foreach($array_htk as $k=>$htk){
	$costs = $this->Cost->find_export_tkdu1($start_date,$end_date,$htk['tk']) ;
//	print_r($costs);
//	die('aaa');
	if($costs != null){
		foreach ($costs as $cost){
			$cost1 += ($cost['tien_thu'] - $cost['tien_chi']);
		}
	}else $costs = 0;	
	$this->excel->getActiveSheet()->setCellValue('A'.$k, $htk['name']);
		$this->excel->getActiveSheet()->setCellValue('B'.$k, $htk['maso']);
		$this->excel->getActiveSheet()->setCellValue('C'.$k, " ");
		$this->excel->getActiveSheet()->setCellValue('D'.$k, $costs);
		$this->excel->getActiveSheet()->setCellValue('E'.$k, " ");
		$this->excel->getActiveSheet()->setCellValue('F'.$k, $htk['congno']);
		$this->excel->getActiveSheet()->setCellValue('G'.$k, $htk['tk']);
		$this->excel->getActiveSheet()->setCellValue('H'.$k, " ");
}	
$this->excel->getActiveSheet()->setCellValue('A32', "V.Tài sản ngắn hạn khác");
	$this->excel->getActiveSheet()->setCellValue('B32', "150");
	$this->excel->getActiveSheet()->setCellValue('C32', " ");
	$this->excel->getActiveSheet()->setCellValue('D32', " ");
	$this->excel->getActiveSheet()->setCellValue('E32', " ");
	$this->excel->getActiveSheet()->setCellValue('F32', "0 ");
	$this->excel->getActiveSheet()->setCellValue('G32', " ");
	$this->excel->getActiveSheet()->setCellValue('H32', "151+152+157+158");

$costs133 = $this->Cost->find_export_tkdu($start_date,$end_date,133) ;
if($costs133 != null){
	foreach ($costs133 as $cost133){
		$cost133 += ($cost133['tien_thu'] - $cost133['tien_chi']);
	}
}else $costs133 = 0;	
$this->excel->getActiveSheet()->setCellValue('A33', "  1.Thuế GTGT được khấu trừ");
	$this->excel->getActiveSheet()->setCellValue('B33', "151");
	$this->excel->getActiveSheet()->setCellValue('C33', " ");
	$this->excel->getActiveSheet()->setCellValue('D33', $costs133);
	$this->excel->getActiveSheet()->setCellValue('E33', " ");
	$this->excel->getActiveSheet()->setCellValue('F33', "0 ");
	$this->excel->getActiveSheet()->setCellValue('G33', "133");
	$this->excel->getActiveSheet()->setCellValue('H33', " ");

$this->excel->getActiveSheet()->setCellValue('A34', "  2.Thuế và các khoản khác phải thu nhà nước");
	$this->excel->getActiveSheet()->setCellValue('B34', "152");
	$this->excel->getActiveSheet()->setCellValue('C34', " ");
	$this->excel->getActiveSheet()->setCellValue('D34', " ");
	$this->excel->getActiveSheet()->setCellValue('E34', " ");
	$this->excel->getActiveSheet()->setCellValue('F34', "0 ");
	$this->excel->getActiveSheet()->setCellValue('G34', " ");
	$this->excel->getActiveSheet()->setCellValue('H34', "15A+15B+15C+15D+15E+15F+15G+15H+15I");
$array_thue = array(
	'35'=> array(
			'name'=>'    - Thuế và các khoản khác phải thu nhà nước(3331)',
			'tk'=>'3331',
			'maso'=>'15A',
			'congno'=>'0',
			),
	'36'=> array(
			'name'=>'    - Thuế và các khoản khác phải thu nhà nước(3332)',
			'tk'=>'3332',
			'maso'=>'15B',
			'congno'=>'0',
			),
	'37'=> array(
			'name'=>'    - Thuế và các khoản khác phải thu nhà nước(3333)',
			'tk'=>'3333',
			'maso'=>'15C',
			'congno'=>'0',
			),
	'38'=> array(
			'name'=>'    - Thuế và các khoản khác phải thu nhà nước(3334)',
			'tk'=>'3334',
			'maso'=>'15D',
			'congno'=>'0',
			),
	'39'=> array(
			'name'=>'    - Thuế và các khoản khác phải thu nhà nước(3335)',
			'tk'=>'3335',
			'maso'=>'15E',
			'congno'=>'0',
			),
	'40'=> array(
			'name'=>'    - Thuế và các khoản khác phải thu nhà nước(3336)',
			'tk'=>'3336',
			'maso'=>'15F',
			'congno'=>'0',
			),
	'41'=> array(
			'name'=>'    - Thuế và các khoản khác phải thu nhà nước(3337)',
			'tk'=>'3337',
			'maso'=>'15G',
			'congno'=>'0',
			),
	'42'=> array(
			'name'=>'    - Thuế và các khoản khác phải thu nhà nước(3338)',
			'tk'=>'3338',
			'maso'=>'15H',
			'congno'=>'0',
			),
	'43'=> array(
			'name'=>'    - Thuế và các khoản khác phải thu nhà nước(3339)',
			'tk'=>'3339',
			'maso'=>'15I',
			'congno'=>'0',
			),
);
foreach($array_thue as $k=>$thue){
	$costs = $this->Cost->find_export_tkdu($start_date,$end_date,$thue['tk']) ;
	if($costs != null){
		foreach ($costs as $cost){
			$cost += ($cost['tien_thu'] - $cost['tien_chi']);
		}
	}else $costs = 0;	
	$this->excel->getActiveSheet()->setCellValue('A'.$k, $thue['name']);
		$this->excel->getActiveSheet()->setCellValue('B'.$k, $thue['maso']);
		$this->excel->getActiveSheet()->setCellValue('C'.$k, " ");
		$this->excel->getActiveSheet()->setCellValue('D'.$k, $costs);
		$this->excel->getActiveSheet()->setCellValue('E'.$k, " ");
		$this->excel->getActiveSheet()->setCellValue('F'.$k, $thue['congno']);
		$this->excel->getActiveSheet()->setCellValue('G'.$k, $thue['tk']);
		$this->excel->getActiveSheet()->setCellValue('H'.$k, " ");
}
$costs157 = $this->Cost->find_export_tkdu($start_date,$end_date,157) ;
if($costs157 != null){
	foreach ($costs157 as $cost157){
		$cost157 += ($cost157['tien_thu'] - $cost157['tien_chi']);
	}
}else $costs157 = 0;	
$this->excel->getActiveSheet()->setCellValue('A44', "  3.Giao dịch mua bán trái phiếu chính phủ");
	$this->excel->getActiveSheet()->setCellValue('B44', "157");
	$this->excel->getActiveSheet()->setCellValue('C44', " ");
	$this->excel->getActiveSheet()->setCellValue('D44', $costs157);
	$this->excel->getActiveSheet()->setCellValue('E44', " ");
	$this->excel->getActiveSheet()->setCellValue('F44', "0 ");
	$this->excel->getActiveSheet()->setCellValue('G44', "157");
	$this->excel->getActiveSheet()->setCellValue('H44', " ");
	
$this->excel->getActiveSheet()->setCellValue('A45', "  4.Tài sản ngắn hạn khác");
	$this->excel->getActiveSheet()->setCellValue('B45', "158");
	$this->excel->getActiveSheet()->setCellValue('C45', " ");
	$this->excel->getActiveSheet()->setCellValue('D45', " ");
	$this->excel->getActiveSheet()->setCellValue('E45', " ");
	$this->excel->getActiveSheet()->setCellValue('F45', "0 ");
	$this->excel->getActiveSheet()->setCellValue('G45', "158A+158B+158C  ");
	$this->excel->getActiveSheet()->setCellValue('H45', " ");
$array_tsnhk = array(
	'46'=> array(
			'name'=>'    - Tài sản ngắn hạn khác(1381)',
			'tk'=>'1381',
			'maso'=>'158A',
			'congno'=>'0',
			),
	'47'=> array(
			'name'=>'    - Tài sản ngắn hạn khác(141)',
			'tk'=>'141',
			'maso'=>'158B',
			'congno'=>'0',
			),
	'48'=> array(
			'name'=>'    - Tài sản ngắn hạn khác(144)',
			'tk'=>'142',
			'maso'=>'158C',
			'congno'=>'0',
			),
);
foreach($array_tsnhk as $k=>$tsnhk){
	$costs = $this->Cost->find_export_tkdu($start_date,$end_date,$tsnhk['tk']) ;
	// if($costs != null){
		// foreach ($costs as $cost){
			// $cost += ($cost['tien_thu'] - $cost['tien_chi']);
		// }
	// }else $costs = 0;	
	$this->excel->getActiveSheet()->setCellValue('A'.$k, $tsnhk['name']);
		$this->excel->getActiveSheet()->setCellValue('B'.$k, $tsnhk['maso']);
		$this->excel->getActiveSheet()->setCellValue('C'.$k, " ");
		$this->excel->getActiveSheet()->setCellValue('D'.$k, $costs);
		$this->excel->getActiveSheet()->setCellValue('E'.$k, " ");
		$this->excel->getActiveSheet()->setCellValue('F'.$k, $tsnhk['congno']);
		$this->excel->getActiveSheet()->setCellValue('G'.$k, $tsnhk['tk']);
		$this->excel->getActiveSheet()->setCellValue('H'.$k, " ");
}
/* end tai san ngan han */
/*tai san dai han */
$array_tsdh = array(
	'49'=> array(
			'name'=>'B.Tài sản dài hạn (200=210+220+230+240)  ',
			'tk'=>'',
			'maso'=>'200',
			'congno'=>'0',
			'congthuc'=>'210+220+230+240',
			),
	'50'=> array(
			'name'=>'I. Tài sản cố định     ',
			'tk'=>'',
			'maso'=>'210',
			'congno'=>'0',
			'congthuc'=>'211+212+213',
			),
	'51'=> array(
			'name'=>'  1. Nguyên giá',
			'tk'=>'211',
			'maso'=>'211',
			'congno'=>'0',
			'congthuc'=>'',
			),
	'52'=> array(
			'name'=>'  1. Giá trị hao mòn lũy kế(*)',
			'tk'=>'212',
			'maso'=>'211',
			'congno'=>'0',
			'congthuc'=>'212A+212B+212C',
			),
	'52'=> array(
			'name'=>'     Hao mòn TSCD hữu hình',
			'maso'=>'212A',
			'tk'=>'2141',
			'congno'=>'0',
			'congthuc'=>'',
			),
	'53'=> array(
			'name'=>'     Hao mòn TSCD thuê tài chính',
			'maso'=>'212B',
			'tk'=>'2142',
			'congno'=>'0',
			'congthuc'=>'',
			),
	'54'=> array(
			'name'=>'     Hao mòn TSCD vô hình',
			'maso'=>'212C',
			'tk'=>'2143',
			'congno'=>'0',
			'congthuc'=>'',
			),
	'55'=> array(
			'name'=>'  3. Chi phí xây dựng cơ bản dở dang',
			'maso'=>'213',
			'tk'=>'241',
			'congno'=>'0',
			'congthuc'=>'',
			),
	'56'=> array(
			'name'=>'II.Bất động sản đầu tư',
			'maso'=>'220',
			'tk'=>'241',
			'congno'=>'0',
			'congthuc'=>'221+222    ',
			),
	'57'=> array(
			'name'=>'  1.Nguyên giá',
			'maso'=>'221',
			'tk'=>'217',
			'congno'=>'0',
			'congthuc'=>'',
			),
	'58'=> array(
			'name'=>'  2.Giá trị hao mòn lũy kế',
			'maso'=>'222',
			'tk'=>'2147',
			'congno'=>'0',
			'congthuc'=>'',
			),
	'59'=> array(
			'name'=>'III.Các khoản đầu tư tài chính dài hạn',
			'maso'=>'230',
			'tk'=>'',
			'congno'=>'0',
			'congthuc'=>'231+239',
			),
	'60'=> array(
			'name'=>'  1.Đầu tư tài chính dài hạn',
			'maso'=>'221',
			'tk'=>'',
			'congno'=>'0',
			'congthuc'=>'',
			),
);
foreach($array_tsdh as $k=>$tsdh){	
	$this->excel->getActiveSheet()->setCellValue('A'.$k, $tsdh['name']);
		$this->excel->getActiveSheet()->setCellValue('B'.$k, $tsdh['maso']);
		$this->excel->getActiveSheet()->setCellValue('C'.$k, " ");
		$this->excel->getActiveSheet()->setCellValue('D'.$k, " ");
		$this->excel->getActiveSheet()->setCellValue('E'.$k, " ");
		$this->excel->getActiveSheet()->setCellValue('F'.$k, $tsdh['congno']);
		$this->excel->getActiveSheet()->setCellValue('G'.$k, $tsdh['tk']);
		$this->excel->getActiveSheet()->setCellValue('H'.$k, $tsdh['congthuc']);
}
/* end tai san dai han */
/* */
$this->excel->getActiveSheet()->getHeaderFooter()->setOddFooter('&RPage &P of &N');
$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(68.17);
$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(12.50);
$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(19.67);
$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(19.50);
$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(43.67);
//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
//if you want to save it as .XLSX Excel 2007 format
$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $md5file = md5(date('YmdHis')) . '.xlsx';
//$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
//force user to download the Excel file without writing it to server's HD
$filename = 'baocao_taichinh.xlsx'; //save our workbook as this file name
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