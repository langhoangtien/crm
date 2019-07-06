<?php
$count = count($rows);
$this->load->library('Excel');

$objPHPExcel = new PHPExcel();

$this->excel->getDefaultStyle()
    ->getAlignment()
    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->setShowGridlines(true);
$this->excel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Times New Roman');
$this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
$this->excel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
$this->excel->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
$this->excel->getActiveSheet()->getPageSetup()->setVerticalCentered(false);
$this->excel->getActiveSheet()->getPageMargins()->setLEFT(0.25);
$this->excel->getActiveSheet()->getPageMargins()->setRIGHT(0.25);
$this->excel->getActiveSheet()->getPageMargins()->setTop(0.25);
$this->excel->getActiveSheet()->getPageMargins()->setBOTTOM(0.25);
$this->excel->getActiveSheet()->getPageMargins()->setFOOTER(0);
$this->excel->getActiveSheet()->getPageMargins()->setHEADER(0);
//activate worksheet number 1
$this->excel->setActiveSheetIndex(0);
//error_reporting(E_ALL);
error_reporting(E_ALL & ~E_NOTICE);
//name the worksheet

/*$sheet = $this->excel->getActiveSheet();
$objDrawing = new PHPExcel_Worksheet_Drawing();
$objDrawing->setPath('images/1.png');
$objDrawing->setOffsetX(50);
$objDrawing->setOffsetY(50);
$objDrawing->setCoordinates('B1');
$objDrawing->setWorksheet($sheet);*/

$id_image = $this->config->item('report_logo');
 $imagePath = 'images/logoreport/'."$id_image";
 $objDrawing = new PHPExcel_Worksheet_Drawing();  
        $objDrawing->setPath($imagePath);
        $objDrawing->setCoordinates('B3');
        $objDrawing->setHeight(70);
        $objDrawing->setWidth(160);
        $objDrawing->setWorksheet($this->excel->getActiveSheet());
		
/* $id_image = $this->config->item('company_logo');
 $imagePath = 'images/'."$id_image".'.png';
 $objDrawing = new PHPExcel_Worksheet_Drawing();
        $objDrawing->setPath($imagePath);
        $objDrawing->setCoordinates('B3');
		$objDrawing->setHeight(150);
		$objDrawing->setWidth(150);
        $objDrawing->setWorksheet($this->excel->getActiveSheet()); */

$this->excel->setActiveSheetIndex(0)->mergeCells('C2:I2');
$this->excel->getActiveSheet()->setCellValue('C2',"CÔNG TY TNHH THIẾT BỊ VÀ GIẢI PHÁP CÔNG NGHỆ PHƯƠNG NAM");
$this->excel->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle("C2")->getFont()->setSize(11);
$this->excel->getActiveSheet()->getStyle('C2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$this->excel->setActiveSheetIndex(0)->mergeCells('D3:I3');
$this->excel->getActiveSheet()->setCellValue('D3',$this->config->item('address'));
$this->excel->getActiveSheet()->getStyle("D3")->getFont()->setSize(11);
$this->excel->getActiveSheet()->getStyle('D3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->setActiveSheetIndex(0)->mergeCells('D4:I4');
$this->excel->getActiveSheet()->setCellValue('D4',"Điện thoại: ".$this->config->item('phone')." - Fax: ".$this->config->item('fax') );
$this->excel->getActiveSheet()->getStyle("D4")->getFont()->setSize(11);
$this->excel->getActiveSheet()->getStyle('D4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->setActiveSheetIndex(0)->mergeCells('D5:I5');
$this->excel->getActiveSheet()->setCellValue('D5',$this->config->item('website')." - Email: ".$this->config->item('email'));
$this->excel->getActiveSheet()->getStyle("D5")->getFont()->setSize(11);
$this->excel->getActiveSheet()->getStyle('D5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


$this->excel->getActiveSheet()->getStyle('H2:H4')->getFont()->setItalic(true);
$this->excel->getActiveSheet()->getStyle('I2:I4')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle("I2:I4")->getFont()->setSize(8.5);
$this->excel->getActiveSheet()->getStyle("H2:H4")->getFont()->setSize(8.5);
$this->excel->getActiveSheet()->getStyle('I2:I4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('H2:H4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);



$this->excel->setActiveSheetIndex(0)->mergeCells('B7'.':C7');
$objRichText8 = new PHPExcel_RichText();
$objRed8 = $objRichText8->createTextRun('Đơn vị bán hàng');
//$objRed->getFont()->setColor("FFFF0000");
$objRed8->getFont()->setBold(true);
$objGreen8 = $objRichText8->createTextRun(' (Sale Company):'); 
$objRichText8->createText(' ');
$objRed8->getFont()->setSize(10);
$objRed8->getFont()->setName('Times New Roman');
$objGreen8->getFont()->setSize(10);
$objGreen8->getFont()->setItalic(true);
$objGreen8->getFont()->setName('Times New Roman');
 $this->excel->getActiveSheet()->getCell('B7')->setValue($objRichText8);
  $this->excel->getActiveSheet()->getStyle('B7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
//$this->excel->getActiveSheet()->setCellValue('C10',"Mã số thuế");
//$this->excel->getActiveSheet()->setCellValue('D10',"(Tax code): ");
$this->excel->setActiveSheetIndex(0)->mergeCells('B8:I8');
$this->excel->getActiveSheet()->setCellValue('B8',"BIÊN BẢN BÀN GIAO, NGHIỆM THU");
$this->excel->getActiveSheet()->getStyle("B8")->getFont()->setSize(12)->setBold(true);
$this->excel->getActiveSheet()->getStyle('B8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$ngay = date('d');
$thang = date('m');
$nam = date('Y');
$this->excel->setActiveSheetIndex(0)->mergeCells('B9'.':I9');
$this->excel->setActiveSheetIndex(0)->mergeCells('B12'.':C12');
$this->excel->setActiveSheetIndex(0)->mergeCells('B13'.':C13');
$this->excel->setActiveSheetIndex(0)->mergeCells('F16'.':I16');
$this->excel->setActiveSheetIndex(0)->setCellValue('B9','Hôm nay, Ngày '.$ngay.' Tháng '.$thang.' Năm '.$nam.'');
$this->excel->getActiveSheet()->getStyle('B9')->getFont()->setSize(9);
$this->excel->getActiveSheet()->getStyle('B9')->getFont()->setItalic(true);
$this->excel->getActiveSheet()->getStyle('B9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);


$this->excel->setActiveSheetIndex(0)->mergeCells('B10'.':I10');
$this->excel->setActiveSheetIndex(0)->setCellValue('B10','Tại Hà Nội, chúng tôi gồm:');
$this->excel->getActiveSheet()->getStyle('B10')->getFont()->setSize(11);
$this->excel->getActiveSheet()->getStyle('B10')->getFont()->setItalic(true)->setBold(true);
$this->excel->getActiveSheet()->getStyle('B10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->setActiveSheetIndex(0)->mergeCells('B11'.':C11');
$this->excel->setActiveSheetIndex(0)->setCellValue('B11','Bên nhận:');
$this->excel->getActiveSheet()->getStyle('B11')->getFont()->setSize(11);
$this->excel->getActiveSheet()->getStyle('B11')->getFont()->setItalic(true)->setBold(true);
$this->excel->getActiveSheet()->getStyle('B11')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->setActiveSheetIndex(0)->mergeCells('D11'.':I11');
$this->excel->setActiveSheetIndex(0)->setCellValue('D11',mb_convert_case($data['cus_name'],MB_CASE_UPPER, "UTF-8" ));
$this->excel->getActiveSheet()->getStyle('D11')->getFont()->setSize(11);
$this->excel->getActiveSheet()->getStyle('D11')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('D11')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->setActiveSheetIndex(0)->mergeCells('B13:C13');
$this->excel->setActiveSheetIndex(0)->setCellValue('B13','Đại diện:');
$this->excel->getActiveSheet()->getStyle('B13')->getFont()->setSize(11);
$this->excel->getActiveSheet()->getStyle('B13')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->setActiveSheetIndex(0)->mergeCells('D13:E13');
$this->excel->getActiveSheet()->getStyle('D13')->getFont()->setSize(11);
$this->excel->getActiveSheet()->setCellValue('D13',$data['customer']);
$this->excel->getActiveSheet()->getStyle('D13')->getFont()->setItalic(true)->setBold(true);
$this->excel->getActiveSheet()->getStyle('D13')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

//$this->excel->setActiveSheetIndex(0)->mergeCells('F13:G13');
$this->excel->setActiveSheetIndex(0)->setCellValue('F13','Chức vụ: ');
$this->excel->getActiveSheet()->getStyle('F13')->getFont()->setSize(11);
$this->excel->getActiveSheet()->getStyle('F13')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->setActiveSheetIndex(0)->mergeCells('H13:I13');
$this->excel->getActiveSheet()->getStyle('H13')->getFont()->setSize(11);
$this->excel->getActiveSheet()->setCellValue('H13',$cust_info->positions);
$this->excel->getActiveSheet()->getStyle('H13')->getFont()->setItalic(true)->setBold(true);
$this->excel->getActiveSheet()->getStyle('H13')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->setActiveSheetIndex(0)->mergeCells('B14'.':C14');
$this->excel->setActiveSheetIndex(0)->setCellValue('B14','Địa chỉ:');
$this->excel->getActiveSheet()->getStyle('B14')->getFont()->setSize(11);
$this->excel->getActiveSheet()->getStyle('B14')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->setActiveSheetIndex(0)->mergeCells('D14'.':I14');
$this->excel->getActiveSheet()->getStyle('D14')->getFont()->setSize(11);
$this->excel->getActiveSheet()->setCellValue('D14',$data['address']);
$this->excel->getActiveSheet()->getStyle('D14')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

//
$this->excel->setActiveSheetIndex(0)->mergeCells('B15'.':C15');
$this->excel->setActiveSheetIndex(0)->setCellValue('B15','Điện thoại:');
$this->excel->getActiveSheet()->getStyle('B15')->getFont()->setSize(11);
$this->excel->getActiveSheet()->getStyle('B15')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->setActiveSheetIndex(0)->mergeCells('D15'.':E15');
$this->excel->getActiveSheet()->getStyle('D15')->getFont()->setSize(11);
$this->excel->getActiveSheet()->setCellValue('D15',$cust_info->phone_number);
$this->excel->getActiveSheet()->getStyle('D15')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->setActiveSheetIndex(0)->mergeCells('F15'.':G15');
$this->excel->setActiveSheetIndex(0)->setCellValue('F15','Fax:');
$this->excel->getActiveSheet()->getStyle('F15')->getFont()->setSize(11);
$this->excel->getActiveSheet()->getStyle('F15')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->setActiveSheetIndex(0)->mergeCells('H15'.':I15');
$this->excel->getActiveSheet()->getStyle('H15')->getFont()->setSize(11);
$this->excel->getActiveSheet()->setCellValue('H15',"");
$this->excel->getActiveSheet()->getStyle('H15')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

// bên A
$this->excel->setActiveSheetIndex(0)->mergeCells('B17'.':C17');
$this->excel->setActiveSheetIndex(0)->setCellValue('B17','Bên giao:');
$this->excel->getActiveSheet()->getStyle('B17')->getFont()->setSize(11)->setBold(true)->setItalic(true);
$this->excel->getActiveSheet()->getStyle('B17')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->setActiveSheetIndex(0)->mergeCells('D17'.':I17');
$this->excel->getActiveSheet()->getStyle('D17')->getFont()->setSize(12)->setBold(true);
$this->excel->getActiveSheet()->setCellValue('D17',$this->config->item('company'));
$this->excel->getActiveSheet()->getStyle('D17')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->setActiveSheetIndex(0)->mergeCells('B18'.':C18');
$this->excel->setActiveSheetIndex(0)->setCellValue('B18','Đại diện:');
$this->excel->getActiveSheet()->getStyle('B18')->getFont()->setSize(11);

$this->excel->getActiveSheet()->getStyle('B18')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->setActiveSheetIndex(0)->mergeCells('D18:E18');
$this->excel->getActiveSheet()->getStyle('D18')->getFont()->setSize(11)->setBold(true);
$this->excel->getActiveSheet()->setCellValue('D18','Ông Phạm Văn Thanh');
$this->excel->getActiveSheet()->getStyle('D18')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->setActiveSheetIndex(0)->mergeCells('F18'.':G18');
$this->excel->setActiveSheetIndex(0)->setCellValue('F18','Chức vụ:');
$this->excel->getActiveSheet()->getStyle('F18')->getFont()->setSize(11);
$this->excel->getActiveSheet()->getStyle('F18')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->setActiveSheetIndex(0)->mergeCells('H18'.':I18');
$this->excel->getActiveSheet()->getStyle('H18')->getFont()->setSize(11)->setBold(true);
$this->excel->getActiveSheet()->setCellValue('H18','Giám đốc');
$this->excel->getActiveSheet()->getStyle('H18')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->setActiveSheetIndex(0)->mergeCells('B19'.':C19');
$this->excel->setActiveSheetIndex(0)->setCellValue('B19','Địa chỉ:');
$this->excel->getActiveSheet()->getStyle('B19')->getFont()->setSize(11);
$this->excel->getActiveSheet()->getStyle('B19')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->setActiveSheetIndex(0)->mergeCells('D19'.':I19');
$this->excel->getActiveSheet()->getStyle('D19')->getFont()->setSize(11);
$this->excel->getActiveSheet()->setCellValue('D19',$this->config->item('address'));
$this->excel->getActiveSheet()->getStyle('D19')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

//
$this->excel->setActiveSheetIndex(0)->mergeCells('B20'.':C20');
$this->excel->setActiveSheetIndex(0)->setCellValue('B20','Điện thoại:');
$this->excel->getActiveSheet()->getStyle('B20')->getFont()->setSize(11);
$this->excel->getActiveSheet()->getStyle('B20')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->setActiveSheetIndex(0)->mergeCells('D20'.':I20');
$this->excel->getActiveSheet()->getStyle('D20')->getFont()->setSize(11);
$this->excel->getActiveSheet()->setCellValue('D20',$this->config->item('phone'));
$this->excel->getActiveSheet()->getStyle('D20')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->setActiveSheetIndex(0)->mergeCells('B22'.':I22');
$this->excel->setActiveSheetIndex(0)->setCellValue('B22','I, Chúng tôi tiến hành bàn giao thiết bị với nội dung chi tiết sau:');
$this->excel->getActiveSheet()->getStyle('B22')->getFont()->setSize(11)->setBold(true)->setItalic(true);
$this->excel->getActiveSheet()->getStyle('B22')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);



$this->excel->getActiveSheet()->setCellValue('B23',"STT");
//$this->excel->getActiveSheet()->setCellValue('E25',"Tên hàng hóa, dịch vụ");
$this->excel->getActiveSheet()->getStyle('B23')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('B23')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->getActiveSheet()->setCellValue('C23',"MÃ HIỆU");
$this->excel->getActiveSheet()->getStyle('C23')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('C23')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->setActiveSheetIndex(0)->mergeCells('D23:E23');
$this->excel->getActiveSheet()->setCellValue('D23',"NỘI DUNG");
$this->excel->getActiveSheet()->getStyle('D23')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('D23')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->setCellValue('F23',"ĐVT");
$this->excel->getActiveSheet()->getStyle('F23')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('F23')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('G23')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('G23')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->setCellValue('G23',"SL");

$this->excel->setActiveSheetIndex(0)->mergeCells('H23:I23');
$this->excel->getActiveSheet()->setCellValue('H23',"GHI CHÚ");
$this->excel->getActiveSheet()->getStyle('H23')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('H23')->getFont()->setBold(true);


/*$this->excel->getActiveSheet()->setCellValue('B23',"(Items)");
$this->excel->getActiveSheet()->getStyle('B23')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B23')->getFont()->setItalic(true);
$this->excel->setActiveSheetIndex(0)->mergeCells('C23'.':E23');
$this->excel->getActiveSheet()->setCellValue('C23',"(Description)");
$this->excel->getActiveSheet()->getStyle('C23')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('C23')->getFont()->setItalic(true);
$this->excel->getActiveSheet()->setCellValue('F23',"(Units)");
$this->excel->getActiveSheet()->getStyle('F23')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('F23')->getFont()->setItalic(true);
$this->excel->getActiveSheet()->setCellValue('G23',"(Quantity)");
$this->excel->getActiveSheet()->getStyle('G23')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('G23')->getFont()->setItalic(true);
$this->excel->getActiveSheet()->setCellValue('H23',"(Unit price)");
$this->excel->getActiveSheet()->getStyle('H23')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('H23')->getFont()->setItalic(true);
$this->excel->getActiveSheet()->getStyle('I23')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->setCellValue('I23',"(Amount)");
$this->excel->getActiveSheet()->getStyle('I23')->getFont()->setItalic(true);
$this->excel->getActiveSheet()->getStyle('A23:I23')->getFont()->setSize(9);

$this->excel->getActiveSheet()->getStyle('A23:I23')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);*/
//DEN DAY
/*$this->excel->getActiveSheet()->getStyle('F23:I23')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('F23:I23')->getFont()->setSize(9);
$this->excel->getActiveSheet()->getStyle('F23:I23')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->setActiveSheetIndex(0)->mergeCells('C24'.':E24');
$this->excel->getActiveSheet()->setCellValue('B24',"A");
$this->excel->getActiveSheet()->setCellValue('C24',"B");
$this->excel->getActiveSheet()->getStyle('B24:I24')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B24')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('C24')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->setCellValue('F24',"C");
$this->excel->getActiveSheet()->setCellValue('G24',"D");
$this->excel->setActiveSheetIndex(0)->mergeCells('H24:I24',"E");

$this->excel->getActiveSheet()->getStyle('B24:I24')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('B24:I24')->getFont()->setSize(8);
$this->excel->getActiveSheet()->getStyle('F24:I24')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);*/
/* */
$styleDOTBlackBorderOutline = array(
    'borders' => array(
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
            'style' => PHPExcel_Style_Border::BORDER_DOTTED,
            'color' => array('argb' => 'FF000000'),
        ),
    ),
);
/* */
$k = 24;
$stt = 1;
$tongtienhang = 0;
foreach(array_reverse($data['cart'], true) as $line=>$item){
	$this->excel->getActiveSheet()->setCellValue('B'.$k,$stt);
        $this->excel->getActiveSheet()->getStyle('B'.$k)->getAlignment()->setIndent(1);
        $this->excel->getActiveSheet()->setCellValue('C'.$k);
	$this->excel->getActiveSheet()->setCellValue('C'.$k,$data['sale_id']);
        $this->excel->getActiveSheet()->getStyle('C'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		 $this->excel->setActiveSheetIndex(0)->mergeCells('D'.$k.':E'.$k);
	$this->excel->getActiveSheet()->setCellValue('D'.$k,$item['name']);
        $this->excel->getActiveSheet()->getStyle('D'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$this->excel->getActiveSheet()->setCellValue('F'.$k,$this->Unit->item_unit($this->Item->get_info($item['item_id'])->unit)->name);
        $this->excel->getActiveSheet()->getStyle('F'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$this->excel->getActiveSheet()->setCellValue('G'.$k,$item['quantity']);
        $this->excel->getActiveSheet()->getStyle('G'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$this->excel->setActiveSheetIndex(0)->mergeCells('H'.$k.':I'.$k);
	$this->excel->getActiveSheet()->setCellValue('H'.$k,$data['show_comment_on_receipt']);
        $this->excel->getActiveSheet()->getStyle('H'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	/*$this->excel->getActiveSheet()->setCellValue('I'.$k,to_currency_unVND_nomar($item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100));
        $this->excel->getActiveSheet()->getStyle('I'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
 
$this->excel->getActiveSheet()->getStyle('B'.$k.':I'.$k)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('B'.$k.':I' . ($k))->applyFromArray($styleDOTBlackBorderOutline);
        // $this->excel->getActiveSheet()->getStyle('J'.($k))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $this->excel->getActiveSheet()->getStyle('B'.($k))->getBorders()->getLEFT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$tongtienhang += $item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100; */
$this->excel->getActiveSheet()->getRowDimension($k)->setRowHeight(19.50);
$k++;
$stt++;
}
/* style border cot */
$this->excel->getActiveSheet()->getStyle('B25:B'.($k-1))->getBorders()->getLEFT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('B25:B'.($k-1))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('C25:C'.($k-1))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('D25:D'.($k-1))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('E25:E'.($k-1))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('F25:F'.($k-1))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('G25:G'.($k-1))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('H25:H'.($k-1))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('I25:I'.($k-1))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
/* end style border cot */
if($k < 41){
for($a = $k ;$a < 41;$a++){
		$this->excel->setActiveSheetIndex(0)->mergeCells('D'.$a.':E'.$a);
		$this->excel->setActiveSheetIndex(0)->mergeCells('H'.$a.':I'.$a);
	    $this->excel->getActiveSheet()->getStyle('B'.$a.':I' . ($a))->applyFromArray($styleDOTBlackBorderOutline);
		$this->excel->getActiveSheet()->getRowDimension($a)->setRowHeight(19.50);
} }
/* style border cot */
$this->excel->getActiveSheet()->getStyle('B'.($k).':B40')->getBorders()->getLEFT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('B'.($k).':B40')->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('C'.($k).':C40')->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('E'.($k).':E40')->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('F'.($k).':F40')->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('G'.($k).':G40')->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//$this->excel->getActiveSheet()->getStyle('H'.($k).':H40')->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('I'.($k).':I40')->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
/* end style border cot */

$i = $this->excel->getActiveSheet()->getHighestRow() +1 ;
$this->excel->setActiveSheetIndex(0)->mergeCells('D'.($i).':G'.($i));
$objRichText = new PHPExcel_RichText();
$objRed = $objRichText->createTextRun('Cộng tiền hàng');
$objRed->getFont()->setBold(true);
$objGreen = $objRichText->createTextRun(' (Value of goods):'); 
$objRichText->createText(' ');
$objRed->getFont()->setSize(9);
$objRed->getFont()->setName('Times New Roman');
$objGreen->getFont()->setSize(9);
$objGreen->getFont()->setItalic(true);
$objGreen->getFont()->setName('Times New Roman');
$this->excel->getActiveSheet()->getCell('D'.($i))->setValue($objRichText);
$this->excel->getActiveSheet()->getStyle('B'.($i).':G'.($i+2))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('B'.($i+3).':I'.($i+2))->getBorders()->getBOTTOM()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('B'.($i).':I'.($i))->getBorders()->getTOP()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('D'.($i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$this->excel->getActiveSheet()->getStyle('D'.($i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->setCellValue('I'.$i,to_currency_unVND_nomar($tongtienhang));
$this->excel->getActiveSheet()->getStyle('I'.($i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$this->excel->getActiveSheet()->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$this->excel->setActiveSheetIndex(0)->mergeCells('B'.($i+1).':C'.($i+1));
$objRichText2 = new PHPExcel_RichText();
$objRed2 = $objRichText2->createTextRun('');
//$objRed->getFont()->setColor("FFFF0000");
$objRed2->getFont()->setBold(true);
$objGreen2 = $objRichText2->createTextRun(' '); 
$objRichText2->createText(' ');
$objRed2->getFont()->setSize(9);
$objRed2->getFont()->setName('Times New Roman');
$objGreen2->getFont()->setSize(9);
$objGreen2->getFont()->setItalic(true);
$objGreen2->getFont()->setName('Times New Roman');
$this->excel->getActiveSheet()->getCell('B'.($i+1))->setValue($objRichText2);
$this->excel->getActiveSheet()->getStyle('B'.($i+1))->getAlignment()->setIndent(1);
$this->excel->getActiveSheet()->getStyle('B'.($i+1).':I'.($i+1))->getBorders()->getTOP()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('B'.($i+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B'.($i+1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

$this->excel->setActiveSheetIndex(0)->mergeCells('D'.($i+1).':G'.($i+1));
$this->excel->getActiveSheet()->setCellValue('D'.($i+1),"Tiền thuế GTGT (VAT): ");
$this->excel->getActiveSheet()->getStyle('D'.($i+1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('D'.($i+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$this->excel->getActiveSheet()->getStyle('D'.($i+1))->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('D'.($i+1))->getFont()->setSize(9);
$this->excel->getActiveSheet()->setCellValue('I'.($i+1),to_currency_unVND_nomar($data['total'] - $tongtienhang));
$this->excel->getActiveSheet()->getStyle('I'.($i+1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('I'.($i+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$this->excel->getActiveSheet()->getStyle('I'.($i+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$this->excel->setActiveSheetIndex(0)->mergeCells('B'.($i+2).':G'.($i+2));
$objRichText1 = new PHPExcel_RichText();
$objRed1 = $objRichText1->createTextRun('Tổng cộng tiền thanh toán');
//$objRed->getFont()->setColor("FFFF0000");
$objRed1->getFont()->setBold(true);
$objGreen1 = $objRichText1->createTextRun(' (Total):'); 
$objRichText1->createText(' ');
$objRed1->getFont()->setSize(9);
$objRed1->getFont()->setName('Times New Roman');
$objGreen1->getFont()->setSize(9);
$objGreen1->getFont()->setItalic(true);
$objGreen1->getFont()->setName('Times New Roman');
$this->excel->getActiveSheet()->getCell('B'.($i+2))->setValue($objRichText1);
$this->excel->getActiveSheet()->getStyle('B'.($i+2).':I'.($i+2))->getBorders()->getTOP()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('B'.($i+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$this->excel->getActiveSheet()->getStyle('B'.($i+2))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

$this->excel->getActiveSheet()->setCellValue('I'.($i+2),to_currency_unVND_nomar($data['total']));
$this->excel->getActiveSheet()->getStyle('I'.($i+2))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('I'.($i+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$this->excel->getActiveSheet()->getStyle('I'.($i+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
// $this->excel->getActiveSheet()->setCellValue('I'.($i+2),to_currency_unVND_nomar($data['total']);
// $this->excel->getActiveSheet()->getStyle('I'.($i+2))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
// foreach($data['payments'] as $payment_id=>$payment)
 // {
    // $this->excel->getActiveSheet()->setCellValue('I'.($i+2),to_currency_unVND_nomar($payment['payment_amount']));
    // $this->excel->getActiveSheet()->getStyle('I'.($i+2))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
 // }
//$this->excel->getActiveSheet()->getStyle('I'.($i+2))->getBorders()->getTOP()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//$this->excel->getActiveSheet()->getStyle('I'.($i+2))->getBorders()->getBOTTOM()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('I'.($i+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
///$this->excel->getActiveSheet()->getStyle('B'.($i+3))->getBorders()->getTOP()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->setActiveSheetIndex(0)->mergeCells('B'.($i+3).':G'.($i+3));
$this->excel->getActiveSheet()->getStyle('B'.($i+3))->getFont()->setSize(9);
$this->excel->getActiveSheet()->getStyle('B'.($i+3))->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('B'.($i+3).':I'.($i+3))->getBorders()->getTOP()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('B'.($i+3).':G'.($i+3))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('B'.($i+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$this->excel->getActiveSheet()->setCellValue('B'.($i+3),"Còn nợ lại : ");
//$this->excel->getActiveSheet()->getStyle('I'.($i+3))->getBorders()->getTOP()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('B'.($i+3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('I'.($i+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$this->excel->getActiveSheet()->getStyle('I'.($i+3))->getFont()->setSize(11);
if($payments_cost > 0){
    $this->excel->getActiveSheet()->setCellValue('I'.($i+3),to_currency_unVND_nomar($payments_cost));
    $this->excel->getActiveSheet()->getStyle('I'.($i+3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
}elseif($payments_cost == 0){
    $this->excel->getActiveSheet()->setCellValue('I'.($i+3),'0 VNĐ');
    $this->excel->getActiveSheet()->getStyle('I'.($i+3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
}


$this->excel->setActiveSheetIndex(0)->mergeCells('B'.($i+4).':C'.($i+4));
$this->excel->setActiveSheetIndex(0)->mergeCells('D'.($i+4).':I'.($i+4));
$objRichText7 = new PHPExcel_RichText();
$objRed7 = $objRichText7->createTextRun('');//Số tiền viết bằng chữ
//$objRed->getFont()->setColor("FFFF0000");
$objRed7->getFont()->setBold(true);
$objGreen7 = $objRichText7->createTextRun(' '); //(Inwords):
$objRichText7->createText(' ');
$objRed7->getFont()->setSize(9);
$objRed7->getFont()->setName('Times New Roman');
$objGreen7->getFont()->setSize(9);
$objGreen7->getFont()->setItalic(true);
$objGreen7->getFont()->setName('Times New Roman');
$this->excel->getActiveSheet()->getCell('B'.($i+4))->setValue($objRichText7);
$this->excel->getActiveSheet()->getStyle('B'.($i+4))->getAlignment()->setIndent(1);
$this->excel->getActiveSheet()->getStyle('B'.($i+4).':I'.($i+4))->getBorders()->getTOP()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('B'.($i+4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->load->model('Cost');
$tongtien = to_currency_unVND_nomar($data['total']/10);

$tong_tienno_chu = 0;
$tienno_chus = $this->Cost->get_tongtien_tra($sale_id);
foreach ($tienno_chus as $tienno_chu){
	$tong_tienno_chu += $tienno_chu['payment_amount'];
}
    //$this->excel->getActiveSheet()->setCellValue('D'.($i+4),$this->Cost->get_string_number($tong_tienno_chu));
	$this->excel->getActiveSheet()->setCellValue('D'.($i+4),'');//$this->Cost->get_string_number($tongtien)
    $this->excel->getActiveSheet()->getStyle('D'.($i+4))->getFont()->setSize(10)->setBold(true)->setItalic(true);
	$this->excel->getActiveSheet()->getStyle('D'.($i+4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$this->excel->getActiveSheet()->getStyle('D'.($i+4))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

$this->excel->getActiveSheet()->getStyle('B'.($i+4))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+5).':I'.($i+5));
$this->excel->getActiveSheet()->setCellValue('A'.($i+5),"II, CÁC ĐIỀU KHOẢN KHÁC ");
$this->excel->getActiveSheet()->getStyle('A'.($i+5))->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('A'.($i+5))->getAlignment()->setWrapText(true);

$this->excel->getActiveSheet()->setCellValue('A'.($i+6)," 1. Bên B đã bàn giao cho Bên A số lượng thiết bị trên theo đúng danh mục ở trên.");
$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+6).':I'.($i+6));
$this->excel->getActiveSheet()->getStyle('A'.($i+6))->getFont()->setItalic(true);
$this->excel->getActiveSheet()->getStyle('A'.($i+6))->getAlignment()->setWrapText(true);


$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+7).':I'.($i+7));
$this->excel->getActiveSheet()->setCellValue('A'.($i+7)," 2, Ngày kiểm tra kỹ thuật: ");
$this->excel->getActiveSheet()->getStyle('A'.($i+7))->getFont()->setItalic(true);
$this->excel->getActiveSheet()->getStyle('A'.($i+7))->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+8).':I'.($i+8));
$this->excel->getActiveSheet()->setCellValue('A'.($i+8)," 3, Mục đích giao nhận theo ");
$this->excel->getActiveSheet()->getStyle('A'.($i+8))->getFont()->setItalic(true);
$this->excel->getActiveSheet()->getStyle('A'.($i+8))->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+9).':I'.($i+9));
$this->excel->getActiveSheet()->setCellValue('A'.($i+9)," 4, Toàn bộ thiết bị và dịch vụ của Bên B đã được Bên A kiểm tra đúng kỹ thuật, đúng chất lượng, đúng số lượng, mớí 100%  đã lắp đặt và đang hoạt động tốt.");
$this->excel->getActiveSheet()->getStyle('A'.($i+9))->getFont()->setItalic(true);
$this->excel->getActiveSheet()->getStyle('A'.($i+9))->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+10).':I'.($i+10));
$this->excel->getActiveSheet()->setCellValue('A'.($i+10)," 5, Biên bản này có giá trị như phiếu bảo hành thiết bị và hệ thống trong vòng 12 tháng (6 tháng với vật tư và phụ kiện)");
$this->excel->getActiveSheet()->getStyle('A'.($i+10))->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getStyle('A'.($i+10))->getFont()->setItalic(true);
$this->excel->getActiveSheet()->getStyle('A'.($i+10))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+11).':I'.($i+11));
$this->excel->getActiveSheet()->setCellValue('A'.($i+11)," *Biên bản này được lập thành 2 bản, mỗi bên giữ 1 bản có giá trị pháp lý như nhau, có hiệu lực kể từ ngày ký.");
$this->excel->getActiveSheet()->getStyle('A'.($i+11))->getFont()->setItalic(true)->setBold(true);
$this->excel->getActiveSheet()->getStyle('A'.($i+11))->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+13).':F'.($i+13));
$this->excel->getActiveSheet()->setCellValue('A'.($i+13),"       ĐẠI DIỆN BÊN NHẬN");
$this->excel->getActiveSheet()->getStyle('A'.($i+13))->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('A'.($i+13))->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('G'.($i+13).':I'.($i+13));
$this->excel->getActiveSheet()->setCellValue('G'.($i+13),"ĐẠI DIỆN BÊN GIAO");
$this->excel->getActiveSheet()->getStyle('G'.($i+13))->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('G'.($i+13))->getAlignment()->setWrapText(true);



 /* */
/* */
 $styleThinBlackBorderOutline = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
        ),
    ),
);
$this->excel->getActiveSheet()->getStyle('B1:J1')->getBorders()->getTOP()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('J1:J24')->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('A1:A24')->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//$this->excel->getActiveSheet()->getStyle('J1:J23')->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('B7:J7')->getBorders()->getTOP()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//$this->excel->getActiveSheet()->getStyle('B14:J14')->getBorders()->getTOP()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//$this->excel->getActiveSheet()->getStyle('B22:J22')->getBorders()->getBOTTOM()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('B23:J23')->getBorders()->getTOP()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('B23:J23')->getBorders()->getBOTTOM()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('B24:J24')->getBorders()->getBOTTOM()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('B23:B24')->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('C23:C24')->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('G23:G24')->getBorders()->getLEFT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('F23:F24')->getBorders()->getLEFT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('H23:H24')->getBorders()->getLEFT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('I23:I24')->getBorders()->getLEFT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

$this->excel->getActiveSheet()->getStyle('B22:B23')->getBorders()->getLEFT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('C22:C23')->getBorders()->getLEFT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('G22:G23')->getBorders()->getLEFT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('F22:F23')->getBorders()->getLEFT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('H22:H23')->getBorders()->getLEFT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('I22:I23')->getBorders()->getLEFT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

$this->excel->getActiveSheet()->getStyle('J'.($i).':J'.($i+3))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('A'.($i-2).':A'.($i+3))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//$this->excel->getActiveSheet()->getStyle('B'.($i).':J'.($i+12))->getBorders()->getBOTTOM()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
// $this->excel->getActiveSheet()->getStyle('B50'.':J50')->getBorders()->getBOTTOM()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('B1:B20')->getAlignment()->setIndent(1);
/* */

/* do rong dong tt nguoi ban */
$this->excel->getActiveSheet()->getRowDimension(1)->setRowHeight(6.75);

$this->excel->getActiveSheet()->getRowDimension(8)->setRowHeight(20.25);
$this->excel->getActiveSheet()->getRowDimension(9)->setRowHeight(16);
$this->excel->getActiveSheet()->getRowDimension(10)->setRowHeight(20.25);
$this->excel->getActiveSheet()->getRowDimension(11)->setRowHeight(20.25);
$this->excel->getActiveSheet()->getRowDimension(12)->setRowHeight(0);

$this->excel->getActiveSheet()->getRowDimension(15)->setRowHeight(18);
$this->excel->getActiveSheet()->getRowDimension(16)->setRowHeight(15);
$this->excel->getActiveSheet()->getRowDimension(17)->setRowHeight(20.25);
$this->excel->getActiveSheet()->getRowDimension(18)->setRowHeight(18.75);
$this->excel->getActiveSheet()->getRowDimension(19)->setRowHeight(18.75);
$this->excel->getActiveSheet()->getRowDimension(20)->setRowHeight(18.75);

$this->excel->getActiveSheet()->getRowDimension('2:5')->setRowHeight(15.75);
// $this->excel->getActiveSheet()->getRowDimension('15:20')->setRowHeight(15.75);
$this->excel->getActiveSheet()->getRowDimension(13)->setRowHeight(15.75);
$this->excel->getActiveSheet()->getRowDimension('14')->setRowHeight(18.75);
$this->excel->getActiveSheet()->getRowDimension(6)->setRowHeight(15.75);
$this->excel->getActiveSheet()->getRowDimension(7)->setRowHeight(1);
// $this->excel->getActiveSheet()->getRowDimension('1:13')->setRowHeight(15.75);
$this->excel->getActiveSheet()->getRowDimension(21)->setRowHeight(16);
$this->excel->getActiveSheet()->getRowDimension(22)->setRowHeight(20.25);
$this->excel->getActiveSheet()->getRowDimension(23)->setRowHeight(19.5);
//$this->excel->getActiveSheet()->getRowDimension(24)->setRowHeight(12);
$this->excel->getActiveSheet()->getRowDimension($i)->setRowHeight(0);
$this->excel->getActiveSheet()->getRowDimension($i+1)->setRowHeight(0);
$this->excel->getActiveSheet()->getRowDimension($i+2)->setRowHeight(0);
$this->excel->getActiveSheet()->getRowDimension($i+3)->setRowHeight(0);
$this->excel->getActiveSheet()->getRowDimension($i+4)->setRowHeight(27.75);
$this->excel->getActiveSheet()->getRowDimension($i+5)->setRowHeight(18);
$this->excel->getActiveSheet()->getRowDimension($i+6)->setRowHeight(18.75);
$this->excel->getActiveSheet()->getRowDimension($i+7)->setRowHeight(18.75);
$this->excel->getActiveSheet()->getRowDimension($i+8)->setRowHeight(18.75);
$this->excel->getActiveSheet()->getRowDimension($i+9)->setRowHeight(32.75);
$this->excel->getActiveSheet()->getRowDimension($i+10)->setRowHeight(32.75);
$this->excel->getActiveSheet()->getRowDimension($i+11)->setRowHeight(38);

$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(0.5);
$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(5.7);
$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15.95);
$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(19.55);
$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(8.35);
$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(8.15);
$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(8.9);
$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(12.5);
$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(14.57);
$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(0.08);

//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
//if you want to save it as .XLSX Excel 2007 format
$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $md5file = md5(date('YmdHis')) . '.xlsx';
//$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
//force user to download the Excel file without writing it to server's HD
$filename = 'bangnghiemthubangiao.xlsx'; //save our workbook as this file name
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
}                                
?>