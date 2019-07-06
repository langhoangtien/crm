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

/* $sheet = $this->excel->getActiveSheet();
$objDrawing = new PHPExcel_Worksheet_Drawing(); */
$id_image = $this->config->item('report_logo');
 $imagePath = 'images/logoreport/'."$id_image";
 $objDrawing = new PHPExcel_Worksheet_Drawing();  
        $objDrawing->setPath($imagePath);
        $objDrawing->setCoordinates('B1');
        $objDrawing->setHeight(70);
        $objDrawing->setWidth(160);
        $objDrawing->setWorksheet($this->excel->getActiveSheet());
/* $objDrawing->setPath('images/viethung.png');
$objDrawing->setOffsetX(50);
$objDrawing->setOffsetY(50); */
/* $objDrawing->setCoordinates('B1');
$objDrawing->setWorksheet($sheet); */
$this->excel->setActiveSheetIndex(0)->mergeCells('D2:G2');
$this->excel->getActiveSheet()->setCellValue('D2',"HOÁ ĐƠN ĐƠN HÀNG ");
$this->excel->getActiveSheet()->getStyle('D2')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle("D2")->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('D2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->setActiveSheetIndex(0)->mergeCells('D3'.':G3');
$this->excel->getActiveSheet()->setCellValue('D3',"Bản sao (Copy)");
$this->excel->getActiveSheet()->getStyle("D3")->getFont()->setSize(10);
$this->excel->getActiveSheet()->getStyle('D3')->getFont()->setItalic(true);
$this->excel->getActiveSheet()->getStyle('D3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->getActiveSheet()->setCellValue('H2',"Mẫu");
$this->excel->getActiveSheet()->setCellValue('H3',"Ký hiệu");
$this->excel->getActiveSheet()->setCellValue('H4',"Số");
$this->excel->getActiveSheet()->setCellValue('I2',"V02GTTT2/001");
$this->excel->getActiveSheet()->setCellValue('I3',"VH/12T");
$this->excel->getActiveSheet()->setCellValue('I4',$data['sale_id']);
//$this->excel->getActiveSheet()->getStyle('H2:H4')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('H2:H4')->getFont()->setItalic(true);
$this->excel->getActiveSheet()->getStyle('I2:I4')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle("I2:I4")->getFont()->setSize(8.5);
$this->excel->getActiveSheet()->getStyle("H2:H4")->getFont()->setSize(8.5);
$this->excel->getActiveSheet()->getStyle('I2:I4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('H2:H4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$ngay = date('d');
$thang = date('m');
$nam = date('Y');
$this->excel->setActiveSheetIndex(0)->mergeCells('D5'.':H5');
$this->excel->setActiveSheetIndex(0)->mergeCells('B12'.':C12');
$this->excel->setActiveSheetIndex(0)->mergeCells('B13'.':C13');
$this->excel->setActiveSheetIndex(0)->mergeCells('F13'.':I13');
$this->excel->setActiveSheetIndex(0)->mergeCells('F5'.':I5');
$this->excel->setActiveSheetIndex(0)->mergeCells('F6'.':I6');
$this->excel->setActiveSheetIndex(0)->mergeCells('F16'.':I16');
$this->excel->setActiveSheetIndex(0)->setCellValue('D5',' Ngày (Date) '.$ngay.' Tháng (Month) '.$thang.' Năm (Year) '.$nam.'');
$this->excel->getActiveSheet()->getStyle('D5')->getFont()->setSize(9);
$this->excel->getActiveSheet()->getStyle('D5')->getFont()->setItalic(true);
$this->excel->getActiveSheet()->getStyle('D5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

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
$this->excel->setActiveSheetIndex(0)->mergeCells('B8'.':C8');
$objRichText9 = new PHPExcel_RichText();
$objRed9 = $objRichText9->createTextRun('Mã số thuế');
//$objRed->getFont()->setColor("FFFF0000");
$objRed9->getFont()->setBold(true);
$objGreen9 = $objRichText9->createTextRun(' (Tax code):'); 
$objRichText9->createText(' ');
$objRed9->getFont()->setSize(10);
$objRed9->getFont()->setName('Times New Roman');
$objGreen9->getFont()->setSize(10);
$objGreen9->getFont()->setItalic(true);
$objGreen9->getFont()->setName('Times New Roman');
 $this->excel->getActiveSheet()->getCell('B8')->setValue($objRichText9);
  $this->excel->getActiveSheet()->getStyle('B8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
//$this->excel->getActiveSheet()->setCellValue('C11',"Địa chỉ" );
//->excel->getActiveSheet()->setCellValue('D11',"(Address): " );
  $this->excel->setActiveSheetIndex(0)->mergeCells('B9:C9');
$objRichText10 = new PHPExcel_RichText();
$objRed10 = $objRichText10->createTextRun('Địa chỉ');
//$objRed->getFont()->setColor("FFFF0000");
$objRed10->getFont()->setBold(true);
$objGreen10 = $objRichText10->createTextRun(' (Address):'); 
$objRichText10->createText(' ');
$objRed10->getFont()->setSize(10);
$objRed10->getFont()->setName('Times New Roman');
$objGreen10->getFont()->setSize(10);
$objGreen10->getFont()->setItalic(true);
$objGreen10->getFont()->setName('Times New Roman');
 $this->excel->getActiveSheet()->getCell('B9')->setValue($objRichText10);
  $this->excel->getActiveSheet()->getStyle('B9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
//$this->excel->getActiveSheet()->setCellValue('C12',"Điện thoại");
//$this->excel->getActiveSheet()->setCellValue('D12',"(Tex/Fax): ");
   $this->excel->setActiveSheetIndex(0)->mergeCells('B10'.':C10');
$objRichText11 = new PHPExcel_RichText();
$objRed11 = $objRichText11->createTextRun('Điện thoại');
//$objRed->getFont()->setColor("FFFF0000");
$objRed11->getFont()->setBold(true);
$objGreen11 = $objRichText11->createTextRun(' (Tex/Fax):'); 
$objRichText11->createText(' ');
$objRed11->getFont()->setSize(10);
$objRed11->getFont()->setName('Times New Roman');
$objGreen11->getFont()->setSize(10);
$objGreen11->getFont()->setItalic(true);
$objGreen11->getFont()->setName('Times New Roman');
 $this->excel->getActiveSheet()->getCell('B10')->setValue($objRichText11);
  $this->excel->getActiveSheet()->getStyle('B10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
  //DEENS DDAAY
//$this->excel->getActiveSheet()->setCellValue('C13',"Số tài khoản");
//$this->excel->getActiveSheet()->setCellValue('D13',"(Account/no): ");
   $this->excel->setActiveSheetIndex(0)->mergeCells('B11'.':C11');
$objRichText12 = new PHPExcel_RichText();
$objRed12 = $objRichText12->createTextRun('Số tài khoản');
//$objRed->getFont()->setColor("FFFF0000");
$objRed12->getFont()->setBold(true);
$objGreen12 = $objRichText12->createTextRun(' (Account/no):'); 
$objRichText12->createText(' ');
$objRed12->getFont()->setSize(10);
$objRed12->getFont()->setName('Times New Roman');
$objGreen12->getFont()->setSize(10);
$objGreen12->getFont()->setItalic(true);
$objGreen12->getFont()->setName('Times New Roman');
$this->excel->getActiveSheet()->getCell('B11')->setValue($objRichText12);
$this->excel->getActiveSheet()->getStyle('B11')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->setActiveSheetIndex(0)->mergeCells('D7'.':I7');
$this->excel->getActiveSheet()->getStyle('D7')->getFont()->setBold(true);
$this->excel->getActiveSheet()->setCellValue('D7',$this->config->item('company'));
$this->excel->getActiveSheet()->getStyle('D7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->setActiveSheetIndex(0)->mergeCells('D8'.':I8');
$this->excel->getActiveSheet()->setCellValue('D8',"0102620355");
$this->excel->getActiveSheet()->getStyle('D8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->setActiveSheetIndex(0)->mergeCells('D9'.':I9');
$this->excel->getActiveSheet()->setCellValue('D9',nl2br($this->config->item('address')));
$this->excel->getActiveSheet()->getStyle('D9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->setActiveSheetIndex(0)->mergeCells('D10'.':I10');
$this->excel->getActiveSheet()->setCellValue('D10',$this->config->item('phone'));
$this->excel->getActiveSheet()->getStyle('D10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->setActiveSheetIndex(0)->mergeCells('D11'.':E11');
$this->excel->setActiveSheetIndex(0)->mergeCells('D12'.':E12');
$this->excel->setActiveSheetIndex(0)->mergeCells('D13'.':E13');
$this->excel->setActiveSheetIndex(0)->mergeCells('D5'.':E5');
$this->excel->setActiveSheetIndex(0)->mergeCells('D6'.':E6');
$this->excel->setActiveSheetIndex(0)->mergeCells('D2'.':E2');
$this->excel->setActiveSheetIndex(0)->mergeCells('D20'.':E20');
$a='1440 201 015 270';
$b= (string)$a;
$this->excel->getActiveSheet()->setCellValue('D11',$b);
$this->excel->getActiveSheet()->getStyle('D11')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
$this->excel->getActiveSheet()->getStyle('D7:D11')->getFont()->setSize(10);

//$this->excel->getDefaultStyle('E13',"1440201015270")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
//$this->excel->getActiveSheet()->getStyle('E13',"1440201015270")->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
$this->excel->setActiveSheetIndex(0)->mergeCells('F11'.':I11');
$objRichText13 = new PHPExcel_RichText();
$objRed13 = $objRichText13->createTextRun('Tại ngân hàng');
//$objRed->getFont()->setColor("FFFF0000");
$objRed13->getFont()->setBold(true);
$objGreen13 = $objRichText13->createTextRun(" (Bank's name):"); 
$objRichText13->createText(' ');
$objRed13->getFont()->setSize(9);
$objRed13->getFont()->setName('Times New Roman');
$objGreen13->getFont()->setSize(9);
$objGreen13->getFont()->setItalic(true);
$objGreen13->getFont()->setName('Times New Roman');
$this->excel->getActiveSheet()->getCell('F11')->setValue($objRichText13);
$this->excel->getActiveSheet()->getStyle('F11')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
  
$this->excel->setActiveSheetIndex(0)->mergeCells('F12'.':I12');
$this->excel->getActiveSheet()->setCellValue('F12',"Ngân Hàng NN & PTNT - CN Bắc Hà Nội - PGD Số 03 ");
$this->excel->getActiveSheet()->getStyle('F12')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('F11:F12')->getFont()->setSize(8);

/* do rong dong tt nguoi ban */
$this->excel->getActiveSheet()->getRowDimension(1)->setRowHeight(6.75);
$this->excel->getActiveSheet()->getRowDimension(7)->setRowHeight(20.25);
$this->excel->getActiveSheet()->getRowDimension(8)->setRowHeight(20.25);
$this->excel->getActiveSheet()->getRowDimension(9)->setRowHeight(20.25);
$this->excel->getActiveSheet()->getRowDimension(10)->setRowHeight(20.25);
$this->excel->getActiveSheet()->getRowDimension(11)->setRowHeight(20.25);
$this->excel->getActiveSheet()->getRowDimension(12)->setRowHeight(20.25);
$this->excel->getActiveSheet()->getRowDimension(14)->setRowHeight(15);
$this->excel->getActiveSheet()->getRowDimension(15)->setRowHeight(15);
$this->excel->getActiveSheet()->getRowDimension(16)->setRowHeight(15);
$this->excel->getActiveSheet()->getRowDimension(17)->setRowHeight(15);
$this->excel->getActiveSheet()->getRowDimension(18)->setRowHeight(15);
$this->excel->getActiveSheet()->getRowDimension(19)->setRowHeight(15);
$this->excel->getActiveSheet()->getRowDimension(20)->setRowHeight(15);
/* do rong dong */
$this->excel->setActiveSheetIndex(0)->mergeCells('B14'.':C14');
$objRichText15 = new PHPExcel_RichText();
$objRed15 = $objRichText15->createTextRun('Họ tên Người mua hàng');
//$objRed->getFont()->setColor("FFFF0000");
$objRed15->getFont()->setBold(true);
$objGreen15 = $objRichText15->createTextRun(" (Customer's name):"); 
$objRichText15->createText(' ');
$objRed15->getFont()->setSize(9);
$objRed15->getFont()->setName('Times New Roman');
$objGreen15->getFont()->setSize(9);
$objGreen15->getFont()->setItalic(true);
$objGreen15->getFont()->setName('Times New Roman');
$this->excel->getActiveSheet()->getCell('B14')->setValue($objRichText15);
$this->excel->getActiveSheet()->getStyle('B14')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->setActiveSheetIndex(0)->mergeCells('D14'.':I14');
$this->excel->getActiveSheet()->setCellValue('D14',$data['customer']);
$this->excel->getActiveSheet()->getStyle('D14')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->setActiveSheetIndex(0)->mergeCells('B15'.':C15');
$objRichText16 = new PHPExcel_RichText();
$objRed16 = $objRichText16->createTextRun('Tên đơn vị');
$objRed16->getFont()->setBold(true);
$objGreen16 = $objRichText16->createTextRun(" (Company):"); 
$objRichText16->createText(' ');
$objRed16->getFont()->setSize(9);
$objRed16->getFont()->setName('Times New Roman');
$objGreen16->getFont()->setSize(9);
$objGreen16->getFont()->setItalic(true);
$objGreen16->getFont()->setName('Times New Roman');
$this->excel->getActiveSheet()->getCell('B15')->setValue($objRichText16);
$this->excel->getActiveSheet()->getStyle('B15')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->setActiveSheetIndex(0)->mergeCells('D15'.':I15');
$this->excel->getActiveSheet()->setCellValue('D15',$data['cus_name'] );
$this->excel->getActiveSheet()->getStyle('D15')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->setActiveSheetIndex(0)->mergeCells('B16'.':C16');
$objRichText17 = new PHPExcel_RichText();
$objRed17 = $objRichText17->createTextRun('Mã số thuế');
$objRed17->getFont()->setBold(true);
$objGreen17 = $objRichText17->createTextRun(" (Tax Code):"); 
$objRichText17->createText(' ');
$objRed17->getFont()->setSize(9);
$objRed17->getFont()->setName('Times New Roman');
$objGreen17->getFont()->setSize(9);
$objGreen17->getFont()->setItalic(true);
$objGreen17->getFont()->setName('Times New Roman');
$this->excel->getActiveSheet()->getCell('B16')->setValue($objRichText17);
$this->excel->getActiveSheet()->getStyle('B16')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->setActiveSheetIndex(0)->mergeCells('D16'.':E16');
$this->excel->getActiveSheet()->setCellValue('D16',$data['tax_code'] );
$this->excel->getActiveSheet()->getStyle('D16')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->setActiveSheetIndex(0)->mergeCells('B17'.':C17');
$objRichText18 = new PHPExcel_RichText();
$objRed18 = $objRichText18->createTextRun('Địa chỉ');
$objRed18->getFont()->setBold(true);
$objGreen18 = $objRichText18->createTextRun(" (Address):"); 
$objRichText18->createText(' ');
$objRed18->getFont()->setSize(9);
$objRed18->getFont()->setName('Times New Roman');
$objGreen18->getFont()->setSize(9);
$objGreen18->getFont()->setItalic(true);
$objGreen18->getFont()->setName('Times New Roman');
$this->excel->getActiveSheet()->getCell('B17')->setValue($objRichText18);
$this->excel->getActiveSheet()->getStyle('B17')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->setActiveSheetIndex(0)->mergeCells('D17'.':I17');
$this->excel->getActiveSheet()->setCellValue('D17',$data['address1']);
$this->excel->getActiveSheet()->getStyle('D17')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->setActiveSheetIndex(0)->mergeCells('B18'.':C18');
$objRichText19 = new PHPExcel_RichText();
$objRed19 = $objRichText19->createTextRun('Số tài khoản');
//$objRed->getFont()->setColor("FFFF0000");
$objRed19->getFont()->setBold(true);
$objGreen19 = $objRichText19->createTextRun(" (Acount no):"); 
$objRichText19->createText(' ');
$objRed19->getFont()->setSize(9);
$objRed19->getFont()->setName('Times New Roman');
$objGreen19->getFont()->setSize(9);
$objGreen19->getFont()->setItalic(true);
$objGreen19->getFont()->setName('Times New Roman');
$this->excel->getActiveSheet()->getCell('B18')->setValue($objRichText19);
$this->excel->getActiveSheet()->getStyle('B18')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->setActiveSheetIndex(0)->mergeCells('D18'.':E18');
$this->excel->getActiveSheet()->setCellValue('D18',$data['account_number'] );
$this->excel->getActiveSheet()->getStyle('D18')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->setActiveSheetIndex(0)->mergeCells('B19'.':C19');
$this->excel->setActiveSheetIndex(0)->mergeCells('D19'.':E19');
//$this->excel->setActiveSheetIndex(0)->mergeCells('F19'.':I19');
$objRichText20 = new PHPExcel_RichText();
$objRed20 = $objRichText20->createTextRun('Hình thức TT');
//$objRed->getFont()->setColor("FFFF0000");
$objRed20->getFont()->setBold(true);
$objGreen20 = $objRichText20->createTextRun(" (Payment of term):"); 
$objRichText20->createText(' ');
$objRed20->getFont()->setSize(9);
$objRed20->getFont()->setName('Times New Roman');
$objGreen20->getFont()->setSize(9);
$objGreen20->getFont()->setItalic(true);
$objGreen20->getFont()->setName('Times New Roman');
$this->excel->getActiveSheet()->getCell('B19')->setValue($objRichText20);
$this->excel->getActiveSheet()->getStyle('B19')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->setActiveSheetIndex(0)->mergeCells('F18'.':I18');
$objRichText14 = new PHPExcel_RichText();
$objRed14 = $objRichText14->createTextRun('Tại ngân hàng');
//$objRed->getFont()->setColor("FFFF0000");
$objRed14->getFont()->setBold(true);
$objGreen14 = $objRichText14->createTextRun(" (Bank's name):"); 
$objRichText14->createText(' ');
$objRed14->getFont()->setSize(9);
$objRed14->getFont()->setName('Times New Roman');
$objGreen14->getFont()->setSize(9);
$objGreen14->getFont()->setItalic(true);
$objGreen14->getFont()->setName('Times New Roman');
$this->excel->getActiveSheet()->getCell('F18')->setValue($objRichText14);
$this->excel->getActiveSheet()->getStyle('F18')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);


$this->excel->getActiveSheet()->getStyle('F19')->getFont()->setSize(9);
$this->excel->getActiveSheet()->getStyle('F19')->getFont()->setBold(true);
$this->excel->getActiveSheet()->setCellValue('F19',$payment['payment_type']);
//$this->excel->getActiveSheet()->getStyle('H19')->getFont()->setSize(9);
//$this->excel->getActiveSheet()->getStyle('I19')->getFont()->setSize(9);
//$this->excel->getActiveSheet()->getStyle('H19')->getFont()->setBold(true);
//$this->excel->getActiveSheet()->setCellValue('H19',"Còn  : ");
//$this->excel->getActiveSheet()->setCellValue('I19',to_currency_unVND_nomar($payments_cost));
$this->excel->getActiveSheet()->getStyle('G19')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$this->excel->setActiveSheetIndex(0)->mergeCells('F20'.':H20');
$objRichText32 = new PHPExcel_RichText();
$objRed32 = $objRichText32->createTextRun('Đơn vị tiền tệ');
//$objRed->getFont()->setColor("FFFF0000");
$objRed32->getFont()->setBold(true);
$objGreen32 = $objRichText32->createTextRun(" (Unit currency):"); 
$objRichText32->createText(' ');
$objRed32->getFont()->setSize(9);
$objRed32->getFont()->setName('Times New Roman');
$objGreen32->getFont()->setSize(9);
$objGreen32->getFont()->setItalic(true);
$objGreen32->getFont()->setName('Times New Roman');
$this->excel->getActiveSheet()->getCell('F20')->setValue($objRichText32);
$this->excel->getActiveSheet()->getStyle('F20')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->setCellValue('I20',"VND");


$this->excel->getActiveSheet()->setCellValue('B22',"STT");
//$this->excel->getActiveSheet()->setCellValue('E25',"Tên hàng hóa, dịch vụ");
$this->excel->getActiveSheet()->getStyle('B22')->getFont()->setBold(true);
$this->excel->setActiveSheetIndex(0)->mergeCells('C22:E22');
$this->excel->getActiveSheet()->setCellValue('C22',"Tên hàng hóa, dịch vụ");
$this->excel->getActiveSheet()->getStyle('C22')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('C22')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B22')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->setCellValue('F22',"ĐV tính");
$this->excel->getActiveSheet()->setCellValue('G22',"Số lượng");
$this->excel->getActiveSheet()->setCellValue('H22',"Đơn giá");
$this->excel->getActiveSheet()->setCellValue('I22',"Thành tiền");

$this->excel->getActiveSheet()->setCellValue('B23',"(Items)");
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

$this->excel->getActiveSheet()->getStyle('A23:I23')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
//DEN DAY
$this->excel->getActiveSheet()->getStyle('F22:I22')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('F22:I22')->getFont()->setSize(9);
$this->excel->getActiveSheet()->getStyle('F22:I22')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

//A		B		C		1		2		3=1X2
$this->excel->setActiveSheetIndex(0)->mergeCells('C24'.':E24');
$this->excel->getActiveSheet()->setCellValue('B24',"A");
$this->excel->getActiveSheet()->setCellValue('C24',"B");
$this->excel->getActiveSheet()->getStyle('B24:I24')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B24')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('C24')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->setCellValue('F24',"C");
$this->excel->getActiveSheet()->setCellValue('G24',"1");
$this->excel->getActiveSheet()->setCellValue('H24',"2");
$this->excel->getActiveSheet()->setCellValue('I24',"3 = 1 x 2");
$this->excel->getActiveSheet()->getStyle('B24:I24')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('B24:I24')->getFont()->setSize(8);
$this->excel->getActiveSheet()->getStyle('F24:I24')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
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
$k = 25;
$stt = 1;
$tongtienhang = 0;
foreach(array_reverse($data['cart'], true) as $line=>$item){
	$this->excel->getActiveSheet()->setCellValue('B'.$k,$stt);
        $this->excel->getActiveSheet()->getStyle('B'.$k)->getAlignment()->setIndent(1);
        $this->excel->setActiveSheetIndex(0)->mergeCells('C'.$k.':E'.$k);
	$this->excel->getActiveSheet()->setCellValue('C'.$k,$item['name']);
        $this->excel->getActiveSheet()->getStyle('C'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$this->excel->getActiveSheet()->setCellValue('F'.$k,$this->Unit->item_unit($this->Item->get_info($item['item_id'])->unit)->name);
        $this->excel->getActiveSheet()->getStyle('F'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$this->excel->getActiveSheet()->setCellValue('G'.$k,$item['quantity']);
        $this->excel->getActiveSheet()->getStyle('G'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$this->excel->getActiveSheet()->setCellValue('H'.$k,to_currency_unVND_nomar($item['price']));
        $this->excel->getActiveSheet()->getStyle('H'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	$this->excel->getActiveSheet()->setCellValue('I'.$k,to_currency_unVND_nomar($item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100));
        $this->excel->getActiveSheet()->getStyle('I'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
 
$this->excel->getActiveSheet()->getStyle('B'.$k.':I'.$k)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('B'.$k.':I' . ($k))->applyFromArray($styleDOTBlackBorderOutline);
        // $this->excel->getActiveSheet()->getStyle('J'.($k))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $this->excel->getActiveSheet()->getStyle('B'.($k))->getBorders()->getLEFT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$tongtienhang += $item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100; 
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
		$this->excel->setActiveSheetIndex(0)->mergeCells('C'.$a.':E'.$a);
	    $this->excel->getActiveSheet()->getStyle('B'.$a.':I' . ($a))->applyFromArray($styleDOTBlackBorderOutline);
		$this->excel->getActiveSheet()->getRowDimension($a)->setRowHeight(19.50);
} }
/* style border cot */
$this->excel->getActiveSheet()->getStyle('B'.($k).':B40')->getBorders()->getLEFT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('B'.($k).':B40')->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('E'.($k).':E40')->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('F'.($k).':F40')->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('G'.($k).':G40')->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('H'.($k).':H40')->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
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
$objRed2 = $objRichText2->createTextRun('Thuế suất GTGT');
//$objRed->getFont()->setColor("FFFF0000");
$objRed2->getFont()->setBold(true);
$objGreen2 = $objRichText2->createTextRun(' (VAT):'); 
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
foreach($data['payments'] as $payment_id=>$payment)
 {
    $this->excel->getActiveSheet()->setCellValue('I'.($i+2),to_currency_unVND_nomar($payment['payment_amount']));
    $this->excel->getActiveSheet()->getStyle('I'.($i+2))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
 }
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
$objRed7 = $objRichText7->createTextRun('Số tiền viết bằng chữ');
//$objRed->getFont()->setColor("FFFF0000");
$objRed7->getFont()->setBold(true);
$objGreen7 = $objRichText7->createTextRun(' (Inwords):'); 
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
$tong_tienno_chu = 0;
$tienno_chus = $this->Cost->get_tongtien_tra($sale_id);

//foreach ($tienno_chus as $tienno_chu){
//	$tong_tienno_chu += $tienno_chu['payment_amount'];
//}

    $this->excel->getActiveSheet()->setCellValue('D'.($i+4),$this->Cost->get_string_number($tong_tienno_chu));
    $this->excel->getActiveSheet()->getStyle('D'.($i+4))->getFont()->setSize(10)->setBold(true)->setItalic(true);
	$this->excel->getActiveSheet()->getStyle('D'.($i+4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$this->excel->getActiveSheet()->getStyle('D'.($i+4))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

$this->excel->getActiveSheet()->getStyle('B'.($i+4))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);


$this->excel->setActiveSheetIndex(0)->mergeCells('B'.($i+5).':C'.($i+5));
$objRichText3 = new PHPExcel_RichText();
$objRed3 = $objRichText3->createTextRun('Người mua hàng');
$objRed3->getFont()->setBold(true);
$objGreen3 = $objRichText3->createTextRun(' (Customer)'); 
$objRichText3->createText(' ');
$objRed3->getFont()->setSize(9);
$objRed3->getFont()->setName('Times New Roman');
$objGreen3->getFont()->setSize(9);
$objGreen3->getFont()->setItalic(true);
$objGreen3->getFont()->setName('Times New Roman');
$this->excel->getActiveSheet()->getCell('B'.($i+5))->setValue($objRichText3);
$this->excel->getActiveSheet()->getStyle('B'.($i+5))->getAlignment()->setIndent(1);
$this->excel->getActiveSheet()->getStyle('B'.($i+5))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->setActiveSheetIndex(0)->mergeCells('D'.($i+5).':F'.($i+5));
$objRichText90 = new PHPExcel_RichText();
$objRed90 = $objRichText90->createTextRun('Người giao hàng');
//$objRed->getFont()->setColor("FFFF0000");
$objRed90->getFont()->setBold(true);
$objGreen90 = $objRichText90->createTextRun(' (Delivery)'); 
$objRichText90->createText(' ');
$objRed90->getFont()->setSize(9);
$objRed90->getFont()->setName('Times New Roman');
$objGreen90->getFont()->setSize(9);
$objGreen90->getFont()->setItalic(true);
$objGreen90->getFont()->setName('Times New Roman');
$this->excel->getActiveSheet()->getCell('D'.($i+5))->setValue($objRichText90);
$this->excel->getActiveSheet()->getStyle('D'.($i+5))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->setActiveSheetIndex(0)->mergeCells('G'.($i+5).':I'.($i+5));
$objRichText4 = new PHPExcel_RichText();
$objRed4 = $objRichText4->createTextRun('Người bán hàng');
//$objRed->getFont()->setColor("FFFF0000");
$objRed4->getFont()->setBold(true);
$objGreen4 = $objRichText4->createTextRun(' (Seller)'); 
$objRichText4->createText(' ');
$objRed4->getFont()->setSize(9);
$objRed4->getFont()->setName('Times New Roman');
$objGreen4->getFont()->setSize(9);
$objGreen4->getFont()->setItalic(true);
$objGreen4->getFont()->setName('Times New Roman');
$this->excel->getActiveSheet()->getCell('G'.($i+5))->setValue($objRichText4);
$this->excel->getActiveSheet()->getStyle('G'.($i+5))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

//$this->excel->getActiveSheet()->getStyle('G'.($i+5))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->setActiveSheetIndex(0)->mergeCells('B'.($i+6).':I'.($i+6));
$this->excel->getActiveSheet()->setCellValue('B'.($i+6),' (Ký, ghi rõ họ tên - Sign,Fullname)                  (Ký, ghi rõ họ tên - Sign,Fullname)      (Đóng dấu ghi rõ họ tên - Sign, Stamp,Fullname)');
$this->excel->getActiveSheet()->getStyle('B'.($i+6))->getFont()->setItalic(true);
$this->excel->getActiveSheet()->getStyle('B'.($i+6))->getFont()->setSize(8);
$this->excel->setActiveSheetIndex(0)->mergeCells('B50'.':I50');
//$this->excel->getActiveSheet()->getStyle('B4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

$this->excel->getActiveSheet()->setCellValue('B50',"Ghi chú: Quý khách hàng xin vui lòng kiểm tra kỹ đơn hàng và ký nhận đầy đủ");
$this->excel->getActiveSheet()->getStyle('B50')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B26')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$styleArray = array(
		'font' => array(
				'bold' => true,
				'color' => array('rgb' => 'FF0000'),
				'size' => 9,
				'italic' =>true,
		),
	);
$this->excel->getActiveSheet()->getStyle('B50')->applyFromArray($styleArray);
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
$this->excel->getActiveSheet()->getStyle('B14:J14')->getBorders()->getTOP()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//$this->excel->getActiveSheet()->getStyle('B22:J22')->getBorders()->getBOTTOM()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('B22:J22')->getBorders()->getTOP()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('B23:J23')->getBorders()->getBOTTOM()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('B24:J24')->getBorders()->getBOTTOM()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('B23:B24')->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
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
$this->excel->getActiveSheet()->getRowDimension('1:5')->setRowHeight(15.75);
// $this->excel->getActiveSheet()->getRowDimension('15:20')->setRowHeight(15.75);
$this->excel->getActiveSheet()->getRowDimension(13)->setRowHeight(8.75);
$this->excel->getActiveSheet()->getRowDimension('14')->setRowHeight(18.75);
$this->excel->getActiveSheet()->getRowDimension(6)->setRowHeight(8);
// $this->excel->getActiveSheet()->getRowDimension('1:13')->setRowHeight(15.75);
$this->excel->getActiveSheet()->getRowDimension(21)->setRowHeight(8);
$this->excel->getActiveSheet()->getRowDimension(22)->setRowHeight(20.25);
$this->excel->getActiveSheet()->getRowDimension(23)->setRowHeight(19.5);
$this->excel->getActiveSheet()->getRowDimension(24)->setRowHeight(12);
$this->excel->getActiveSheet()->getRowDimension($i)->setRowHeight(14.75);
$this->excel->getActiveSheet()->getRowDimension($i+1)->setRowHeight(14.75);
$this->excel->getActiveSheet()->getRowDimension($i+2)->setRowHeight(14.75);
$this->excel->getActiveSheet()->getRowDimension($i+3)->setRowHeight(14.75);
$this->excel->getActiveSheet()->getRowDimension($i+4)->setRowHeight(27.75);
$this->excel->getActiveSheet()->getRowDimension($i+5)->setRowHeight(18);
$this->excel->getActiveSheet()->getRowDimension($i+6)->setRowHeight(12.75);

$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(0.5);
$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(5.7);
$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(28.95);
$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(6.55);
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
$filename = 'hoadon_donhang.xlsx'; //save our workbook as this file name
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