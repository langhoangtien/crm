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
$this->excel->getActiveSheet()->getPageMargins()->setLEFT(0.95);
$this->excel->getActiveSheet()->getPageMargins()->setRIGHT(0.14);
$this->excel->getActiveSheet()->getPageMargins()->setTop(0.95);
$this->excel->getActiveSheet()->getPageMargins()->setBOTTOM(0.95);
$this->excel->getActiveSheet()->getPageMargins()->setFOOTER(0.95);
$this->excel->getActiveSheet()->getPageMargins()->setHEADER(0.95);
//activate worksheet number 1
$this->excel->setActiveSheetIndex(0);
//error_reporting(E_ALL);
error_reporting(E_ALL & ~E_NOTICE);
//name the worksheet

/*
$sheet = $this->excel->getActiveSheet();
$objDrawing = new PHPExcel_Worksheet_Drawing();
$objDrawing->setPath('images/viethung.png');
$objDrawing->setOffsetX(50);
$objDrawing->setOffsetY(50);
$objDrawing->setCoordinates('B1');
$objDrawing->setWorksheet($sheet);
*/

$this->excel->setActiveSheetIndex(0)->mergeCells('A1:D1');
$this->excel->getActiveSheet()->setCellValue('A1',$this->config->item('company'));
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('E1:H1');
$this->excel->getActiveSheet()->setCellValue('E1',"CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM \n Độc lập – Tự do – Hạnh phúc");
$this->excel->getActiveSheet()->getStyle('E1')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('E1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('E1')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('C6:G6');
$this->excel->getActiveSheet()->setCellValue('C6',"HỢP ĐỒNG");
$this->excel->getActiveSheet()->getStyle('C6')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('C6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('C6')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A7:H7');
$this->excel->getActiveSheet()->setCellValue('A7',$this->config->item('title_contract'));
$this->excel->getActiveSheet()->getStyle('A7')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A7')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A11:H11');
$this->excel->getActiveSheet()->setCellValue('A11',"Căn cứ Luật Thương mại đã được Quốc hội nước Cộng hòa xã hội chủ nghĩa Việt Nam khóa XI, kỳ họp thứ 7 thông qua ngày 14 tháng 6 năm 2005");
$this->excel->getActiveSheet()->getStyle('A11')->getFont()->setItalic(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A11')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A11')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A12:H12');
$this->excel->getActiveSheet()->setCellValue('A12',"Căn cứ Bộ Luật Dân sự này đã được Quốc hội nước Cộng hòa xã hội chủ nghĩa Việt Nam khóa XI, kỳ họp thứ 7 thông qua ngày 14 tháng 6 năm 2005");
$this->excel->getActiveSheet()->getStyle('A12')->getFont()->setItalic(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A12')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A12')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A13:H13');
$this->excel->getActiveSheet()->setCellValue('A13',"Căn cứ nhu cầu và khả năng cung cấp hai bên ký hợp đồng.");
$this->excel->getActiveSheet()->getStyle('A13')->getFont()->setItalic(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A13')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A13')->getAlignment()->setWrapText(true);

$this->excel->getActiveSheet()->setCellValue('A16',"BÊN A");
$this->excel->getActiveSheet()->getStyle('A16')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A16')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A16')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('A16')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('B16:H16');
$this->excel->getActiveSheet()->setCellValue('B16',$cust_info->company_name);
$this->excel->getActiveSheet()->getStyle('B16')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('B16')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B16')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('B16')->getAlignment()->setWrapText(true);


$this->excel->getActiveSheet()->setCellValue('A17',"Địa chỉ");
$this->excel->getActiveSheet()->getStyle('A17')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A17')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A17')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('A17')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('B17:H17');
$this->excel->getActiveSheet()->setCellValue('B17',$data['address']);
$this->excel->getActiveSheet()->getStyle('B17')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('B17')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B17')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('B17')->getAlignment()->setWrapText(true);

$this->excel->getActiveSheet()->setCellValue('A18',"Điện thoại");
$this->excel->getActiveSheet()->getStyle('A18')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A18')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A18')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('A18')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('B18:H18');
$this->excel->getActiveSheet()->setCellValue('B18',$cust_info->phone_number);
$this->excel->getActiveSheet()->getStyle('B18')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('B18')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B18')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('B18')->getAlignment()->setWrapText(true);

$this->excel->getActiveSheet()->setCellValue('A19',"Đại diện bởi");
$this->excel->getActiveSheet()->getStyle('A19')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A19')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A19')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('A19')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('B19:H19');
$this->excel->getActiveSheet()->setCellValue('B19',"(Ông/Bà) ".$data['customer']."              Chức vụ: ".$data['positions']);
$this->excel->getActiveSheet()->getStyle('B19')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('B19')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B19')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('B19')->getAlignment()->setWrapText(true);

$this->excel->getActiveSheet()->setCellValue('A20',"Mã số thuế");
$this->excel->getActiveSheet()->getStyle('A20')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A20')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A20')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('A20')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('B20:H20');
$this->excel->getActiveSheet()->setCellValue('B20',$data['code_tax']);
$this->excel->getActiveSheet()->getStyle('B20')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('B20')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B20')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('B20')->getAlignment()->setWrapText(true);

$this->excel->getActiveSheet()->setCellValue('A21',"Số tài khoản");
$this->excel->getActiveSheet()->getStyle('A21')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A21')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A21')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('A21')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('B21:H21');
$this->excel->getActiveSheet()->setCellValue('B21',$data['account_number']);
$this->excel->getActiveSheet()->getStyle('B21')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('B21')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B21')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('B21')->getAlignment()->setWrapText(true);

$this->excel->getActiveSheet()->setCellValue('A22',"Tại ngân hàng");
$this->excel->getActiveSheet()->getStyle('A22')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A22')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A22')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('A22')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('B22:H22');
$this->excel->getActiveSheet()->setCellValue('B22',$cust_info->account_number);
$this->excel->getActiveSheet()->getStyle('B22')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('B22')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B22')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('B22')->getAlignment()->setWrapText(true);

$this->excel->getActiveSheet()->setCellValue('A24',"BÊN B");
$this->excel->getActiveSheet()->getStyle('A24')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A24')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A24')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('A24')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('B24:H24');
$this->excel->getActiveSheet()->setCellValue('B24',$this->config->item('company'));
$this->excel->getActiveSheet()->getStyle('B24')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('B24')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B24')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('B24')->getAlignment()->setWrapText(true);

/* thong tin ben B */

$this->excel->getActiveSheet()->setCellValue('A25',"Địa chỉ");
$this->excel->getActiveSheet()->getStyle('A25')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A25')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A25')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('A25')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('B25:H25');
$this->excel->getActiveSheet()->setCellValue('B25',$this->config->item('address'));
$this->excel->getActiveSheet()->getStyle('B25')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('B25')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B25')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('B25')->getAlignment()->setWrapText(true);

$this->excel->getActiveSheet()->setCellValue('A26',"Điện thoại");
$this->excel->getActiveSheet()->getStyle('A26')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A26')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A26')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('A26')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('B26:H26');
$this->excel->getActiveSheet()->setCellValue('B26',$this->config->item('phone'));
$this->excel->getActiveSheet()->getStyle('B26')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('B26')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B26')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('B26')->getAlignment()->setWrapText(true);

$this->excel->getActiveSheet()->setCellValue('A27',"Đại diện bởi");
$this->excel->getActiveSheet()->getStyle('A27')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A27')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A27')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('A27')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('B27:H27');
$this->excel->getActiveSheet()->setCellValue('B27',"(Ông/Bà) Nguyễn Mạnh Trường              Chức vụ: Giám đốc");
$this->excel->getActiveSheet()->getStyle('B27')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('B27')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B27')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('B27')->getAlignment()->setWrapText(true);

$this->excel->getActiveSheet()->setCellValue('A28',"Mã số thuế");
$this->excel->getActiveSheet()->getStyle('A28')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A28')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A28')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('A28')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('B28:H28');
$this->excel->getActiveSheet()->setCellValue('B28',$data['code_tax']);
$this->excel->getActiveSheet()->getStyle('B28')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('B28')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B28')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('B28')->getAlignment()->setWrapText(true);

$this->excel->getActiveSheet()->setCellValue('A29',"Số tài khoản");
$this->excel->getActiveSheet()->getStyle('A29')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A29')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A29')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('A29')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('B29:H29');
$this->excel->getActiveSheet()->setCellValue('B29',$data['account_number']);
$this->excel->getActiveSheet()->getStyle('B29')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('B29')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B29')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('B29')->getAlignment()->setWrapText(true);

$this->excel->getActiveSheet()->setCellValue('A30',"Tại ngân hàng");
$this->excel->getActiveSheet()->getStyle('A30')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A30')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A30')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('A30')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('B30:H30');
$this->excel->getActiveSheet()->setCellValue('B30',$cust_info->account_number);
$this->excel->getActiveSheet()->getStyle('B30')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('B30')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B30')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('B30')->getAlignment()->setWrapText(true);

/* end thong tin ben B */
$this->excel->setActiveSheetIndex(0)->mergeCells('A31:H31');
$this->excel->getActiveSheet()->setCellValue('A31',"ĐIỀU 1: NỘI DUNG HỢP ĐỒNG MUA BÁN");
$this->excel->getActiveSheet()->getStyle('A31')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A31')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A31')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A31')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A32:H32');
$this->excel->getActiveSheet()->setCellValue('A32',"");
$this->excel->getActiveSheet()->getStyle('A32')->getFont()->setBold(true)->setItalic(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A32')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A32')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A32')->getAlignment()->setWrapText(true);

$this->excel->getActiveSheet()->setCellValue('A33',"");
$this->excel->getActiveSheet()->getStyle('A33')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A33')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A33')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A33')->getAlignment()->setWrapText(true);

$this->excel->getActiveSheet()->setCellValue('B33',"");
$this->excel->getActiveSheet()->getStyle('B33')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('B33')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B33')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B33')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('C33:H33');
$this->excel->getActiveSheet()->setCellValue('C33',"");
$this->excel->getActiveSheet()->getStyle('C33')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('C33')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('C33')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('C33')->getAlignment()->setWrapText(true);

for ($f = 34; $f <43;$f++){
$this->excel->getActiveSheet()->setCellValue('A'.$f," ");
$this->excel->getActiveSheet()->setCellValue('B'.$f," ");
$this->excel->setActiveSheetIndex(0)->mergeCells('C'.$f.':H'.$f);
$this->excel->getActiveSheet()->setCellValue('C'.$f," ");
}

$this->excel->setActiveSheetIndex(0)->mergeCells('A44:H44');
$this->excel->getActiveSheet()->setCellValue('A44',"");
$this->excel->getActiveSheet()->getStyle('A44')->getFont()->setBold(true)->setItalic(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A44')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A44')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A44')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A45:H45');
$this->excel->getActiveSheet()->setCellValue('A45',"");
$this->excel->getActiveSheet()->getStyle('A45')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A45')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A45')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A45')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A46:H46');
$this->excel->getActiveSheet()->setCellValue('A46',"");
$this->excel->getActiveSheet()->getStyle('A46')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A46')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A46')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A46')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A47:H47');
$this->excel->getActiveSheet()->setCellValue('A47',"");
$this->excel->getActiveSheet()->getStyle('A47')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A47')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A47')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A47')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A48:H48');
$this->excel->getActiveSheet()->setCellValue('A48',"");
$this->excel->getActiveSheet()->getStyle('A48')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A48')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A48')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A48')->getAlignment()->setWrapText(true);

$styleArray = array(
    'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => 'FF0000'),
        'size'  => 12,
		'italic'=>false,
    ));

$this->excel->setActiveSheetIndex(0)->mergeCells('A49:H49');
$this->excel->getActiveSheet()->setCellValue('A49',"");
$this->excel->getActiveSheet()->getStyle('A49')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A49')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A49')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A49')->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getStyle('A49')->applyFromArray($styleArray);

$this->excel->setActiveSheetIndex(0)->mergeCells('A51:H51');
$this->excel->getActiveSheet()->setCellValue('A51',"ĐIỀU 2: GIÁ TRỊ HỢP ĐỒNG :");
$this->excel->getActiveSheet()->getStyle('A51')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A51')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A51')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A51')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('B53:C53');
$this->excel->getActiveSheet()->setCellValue('A53',"STT");
$this->excel->getActiveSheet()->setCellValue('B53',"Khoản mục");
$this->excel->getActiveSheet()->setCellValue('D53',"Đơn giá (VNĐ)");
$this->excel->getActiveSheet()->setCellValue('E53',"Số lượng");
$this->excel->getActiveSheet()->setCellValue('F53',"Thời gian");
$this->excel->getActiveSheet()->setCellValue('G53',"Thành tiền (VNĐ)");
$this->excel->getActiveSheet()->setCellValue('H53',"Ghi chú");
$this->excel->getActiveSheet()->getStyle('A53:H53')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A53:H53')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A53:H53')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A53:H53')->getAlignment()->setWrapText(true);
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
$k = 54;
$stt = 1;
$tongtienhang = 0;
foreach(array_reverse($data['cart'], true) as $line=>$item){
	$this->excel->getActiveSheet()->setCellValue('A'.$k,$stt);
        $this->excel->getActiveSheet()->getStyle('A'.$k)->getAlignment()->setIndent(1);
        $this->excel->setActiveSheetIndex(0)->mergeCells('B'.$k.':C'.$k);
	$this->excel->getActiveSheet()->setCellValue('B'.$k,$item['name']);
        $this->excel->getActiveSheet()->getStyle('B'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$this->excel->getActiveSheet()->setCellValue('D'.$k,to_currency_unVND_nomar($item['price']));
        $this->excel->getActiveSheet()->getStyle('D'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	$this->excel->getActiveSheet()->setCellValue('E'.$k,$item['quantity']);
        $this->excel->getActiveSheet()->getStyle('E'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$this->excel->getActiveSheet()->setCellValue('F'.$k," ");
        $this->excel->getActiveSheet()->getStyle('F'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
	$this->excel->getActiveSheet()->setCellValue('G'.$k,to_currency_unVND_nomar($item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100));
        $this->excel->getActiveSheet()->getStyle('G'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	$this->excel->getActiveSheet()->setCellValue('H'.$k,$data['show_comment_on_receipt']);
        $this->excel->getActiveSheet()->getStyle('H'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	
$this->excel->getActiveSheet()->getStyle('A'.$k.':H'.$k)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('A'.$k.':H' . ($k))->applyFromArray($styleDOTBlackBorderOutline);
        // $this->excel->getActiveSheet()->getStyle('J'.($k))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $this->excel->getActiveSheet()->getStyle('A'.($k))->getBorders()->getLEFT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$tongtienhang += $item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100; 
$this->excel->getActiveSheet()->getRowDimension($k)->setRowHeight(19.50);
$k++;
$stt++;
}


/* style border cot */

/* end style border cot */
$i = $this->excel->getActiveSheet()->getHighestRow() +1 ;
$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i).':E'.($i));
$this->excel->getActiveSheet()->setCellValue('A'.($i),"Cộng: ");
$this->excel->getActiveSheet()->getStyle('A'.($i))->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A'.($i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A'.($i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A'.($i))->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('F'.($i).':H'.($i));
$this->excel->getActiveSheet()->setCellValue('F'.$i,to_currency_unVND_nomar($tongtienhang));
$this->excel->getActiveSheet()->getStyle('F'.($i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$this->excel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+1).':E'.($i+1));
$this->excel->getActiveSheet()->setCellValue('A'.($i+1),"Chiết khấu thương mại (trước thuế)");
$this->excel->getActiveSheet()->getStyle('A'.($i+1))->getAlignment()->setIndent(1);
$this->excel->getActiveSheet()->getStyle('A'.($i+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A'.($i+1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

$this->excel->setActiveSheetIndex(0)->mergeCells('F'.($i+1).':H'.($i+1));
$this->excel->getActiveSheet()->setCellValue('F'.($i+1)," ");

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+2).':E'.($i+2));
$this->excel->getActiveSheet()->setCellValue('A'.($i+2)," Thuế VAT phần mềm (%) ");
$this->excel->getActiveSheet()->getStyle('A'.($i+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A'.($i+2))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->setActiveSheetIndex(0)->mergeCells('F'.($i+2).':H'.($i+2));
foreach($data['payments'] as $payment_id=>$payment)
 {
    $this->excel->getActiveSheet()->setCellValue('F'.($i+2),to_currency_unVND_nomar($payment['payment_amount']));
    $this->excel->getActiveSheet()->getStyle('F'.($i+2))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$this->excel->getActiveSheet()->getStyle('F'.($i+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
 }
 
$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+3).':E'.($i+3));
$this->excel->setActiveSheetIndex(0)->mergeCells('F'.($i+3).':H'.($i+3));
$this->excel->getActiveSheet()->setCellValue('A'.($i+3),"Tổng cộng : ");
$this->excel->getActiveSheet()->getStyle('A'.($i+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A'.($i+3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('F'.($i+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$this->excel->getActiveSheet()->getStyle('F'.($i+3))->getFont()->setSize(11);
if($payments_cost > 0){
    $this->excel->getActiveSheet()->setCellValue('F'.($i+3),to_currency_unVND_nomar($payments_cost));
    $this->excel->getActiveSheet()->getStyle('F'.($i+3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
}elseif($payments_cost == 0){
    $this->excel->getActiveSheet()->setCellValue('F'.($i+3),'0 VNĐ');
    $this->excel->getActiveSheet()->getStyle('F'.($i+3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
}
$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+4).':E'.($i+4));
$this->excel->setActiveSheetIndex(0)->mergeCells('F'.($i+4).':H'.($i+4));
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
$this->excel->getActiveSheet()->getCell('A'.($i+4))->setValue($objRichText7);
$this->excel->getActiveSheet()->getStyle('A'.($i+4))->getAlignment()->setIndent(1);
$this->excel->getActiveSheet()->getStyle('A'.($i+4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->load->model('Cost');
$tong_tienno_chu = 0;
$tienno_chus = $this->Cost->get_tongtien_tra($sale_id);
foreach ($tienno_chus as $tienno_chu){
	$tong_tienno_chu += $tienno_chu['payment_amount'];
}
    $this->excel->getActiveSheet()->setCellValue('F'.($i+4),$this->Cost->get_string_number($tong_tienno_chu));
    $this->excel->getActiveSheet()->getStyle('F'.($i+4))->getFont()->setSize(12)->setBold(true)->setItalic(true);
	$this->excel->getActiveSheet()->getStyle('F'.($i+4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$this->excel->getActiveSheet()->getStyle('F'.($i+4))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

$this->excel->getActiveSheet()->getStyle('A'.($i+4))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);


$styleArray = array(
    'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => 'FF0000'),
        'size'  => 9,
		'italic'=>true,
    ));
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
$this->excel->getActiveSheet()->getStyle('B1:B20')->getAlignment()->setIndent(1);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+5).':H'.($i+5));
$this->excel->getActiveSheet()->setCellValue('A'.($i+5),"ĐIỀU 3: GIAO HÀNG ");
$this->excel->getActiveSheet()->getStyle('A'.($i+5))->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A'.($i+5))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A'.($i+5))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A'.($i+5))->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+6).':H'.($i+6));
$this->excel->getActiveSheet()->setCellValue('A'.($i+6),"Thời gian chuyển giao: Một ngày sau khi nhận được tiền ");

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+7).':H'.($i+7));
$this->excel->getActiveSheet()->setCellValue('A'.($i+7),"Hình thức chuyển giao: Nhân viên kĩ thuật sẽ hướng dẫn cài đặt hoặc trực tiếp xuống cài đặt và gửi file  ");
$this->excel->getActiveSheet()->getStyle('A'.($i+7))->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getRowDimension($i+7)->setRowHeight(36);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+8).':H'.($i+8));
$this->excel->getActiveSheet()->setCellValue('A'.($i+8),"ĐIỀU 4: BẢO HÀNH VÀ TƯ VẤN ");
$this->excel->getActiveSheet()->getStyle('A'.($i+8))->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A'.($i+8))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A'.($i+8))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A'.($i+8))->getAlignment()->setWrapText(true);


$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+9).':H'.($i+9));
$this->excel->getActiveSheet()->setCellValue('A'.($i+9),"o    BẢO HÀNH");

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+10).':H'.($i+10));
$this->excel->getActiveSheet()->setCellValue('A'.($i+10)," Bên B chịu trách nhiệm bảo hành sản phẩm trong vòng 12 tháng kể từ ngày ký kết hợp đồng chuyển giao.");
$this->excel->getActiveSheet()->getStyle('A'.($i+10))->getAlignment()->setWrapText(true);


$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+11).':H'.($i+11));
$this->excel->getActiveSheet()->setCellValue('A'.($i+11)," Bên B chịu bảo hành các lỗi gặp phải khi vận hành phần mềm đúng như yêu cầu đặt hàng phần mềm của bên A");
$this->excel->getActiveSheet()->getStyle('A'.($i+11))->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getRowDimension($i+11)->setRowHeight(32.25);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+12).':H'.($i+12));
$this->excel->getActiveSheet()->setCellValue('A'.($i+12),"Bên B không chịu trách nhiệm bảo hành các lỗi do thiết bị phần cứng gây ra, các lỗi do người sử dụng vô tình hay cố ý gây ra khi vận hành phần mềm không đúng với tài liệu hướng dẫn sử dụng. Bên B không chịu trách nhiệm bảo hành tính pháp lý của số liệu kế toán trong phần mềm. Bên B không chịu trách nhiệm bảo hành phần mềm trong các trường hợp sự cố gây ra do thiên tai: lũ lụt, động đất, sét đánh, hỏa hoạn, mất trộm, mất điện …");
$this->excel->getActiveSheet()->getStyle('A'.($i+12))->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getRowDimension($i+12)->setRowHeight(84);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+13).':H'.($i+13));
$this->excel->getActiveSheet()->setCellValue('A'.($i+13),"o              TƯ VẤN");
$this->excel->getActiveSheet()->getStyle('A'.($i+13))->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+14).':H'.($i+14));
$this->excel->getActiveSheet()->setCellValue('A'.($i+14),"Bên B chịu trách nhiệm hướng dẫn sử dụng thành thạo (miến phí) cho các nhân viên của bên A thông qua điện thoại, email, remote");
$this->excel->getActiveSheet()->getStyle('A'.($i+14))->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getRowDimension($i+14)->setRowHeight(30.75);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+15).':H'.($i+15));
$this->excel->getActiveSheet()->setCellValue('A'.($i+15),"Bên B chịu trách nhiệm tư vấn trong suốt quá trình sử dụng phần mềm thông qua hệ thống fax, e-mail, điện thoại, chuyển phát thư.");
$this->excel->getActiveSheet()->getStyle('A'.($i+15))->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getRowDimension($i+15)->setRowHeight(31.50);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+16).':H'.($i+16));
$this->excel->getActiveSheet()->setCellValue('A'.($i+16)," Khi gặp sự cố trong quá trình sử dụng trong giờ làm việc (từ 8:00AM đến 5:30PM), người sử dụng liên lạc với Phòng tư vấn theo địa chỉ e-mail: infor@lifetek.com.vn  . Bên B có trách nhiệm trả lời tư vấn hoặc khắc phục sự cố trong vòng 24h làm việc kể từ khi nhận được yêu cầu. nếu ngoài giờ làm việc của bên B mà bên A gặp sự cố, người sử dụng liên lạc theo số điện thoại nóng 098.885.7861 hoặc 01663.065.057 để được hỗ trợ.");
$this->excel->getActiveSheet()->getStyle('A'.($i+16))->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getRowDimension($i+16)->setRowHeight(78.75);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+17).':H'.($i+17));
$this->excel->getActiveSheet()->setCellValue('A'.($i+17),"ĐIỀU 5: THANH TOÁN");
$this->excel->getActiveSheet()->getStyle('A'.($i+17))->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+18).':H'.($i+18));
$this->excel->getActiveSheet()->setCellValue('A'.($i+18),"Tổng giá trị hợp đồng: VNĐ (……………………..)");
$this->excel->getActiveSheet()->getStyle('A'.($i+18))->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+19).':H'.($i+19));
$this->excel->getActiveSheet()->setCellValue('A'.($i+19),"Thời hạn thanh toán : ");
$this->excel->getActiveSheet()->getStyle('A'.($i+19))->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+20).':H'.($i+20));
$this->excel->getActiveSheet()->setCellValue('A'.($i+20),"Bên A sẽ thanh toán cho bên B 100% giá trị hợp đồng ngay sau khi ký hợp đồng và trước khi chuyển giao");
$this->excel->getActiveSheet()->getStyle('A'.($i+20))->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getRowDimension($i+20)->setRowHeight(31.50);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+21).':H'.($i+21));
$this->excel->getActiveSheet()->setCellValue('A'.($i+21),"Trong trường hợp bên A thanh toán chậm cho bên B theo thời gian hợp đồng đã quy định thì bên A sẽ phải chịu lãi suất không kỳ hạn (tính theo lãi suất không kỳ hạn của ngân hàng Vietcombank) tại thời điểm của hợp đồng. ");
$this->excel->getActiveSheet()->getStyle('A'.($i+21))->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getRowDimension($i+20)->setRowHeight(50.25);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+22).':H'.($i+22));
$this->excel->getActiveSheet()->setCellValue('A'.($i+22),"Hình thức thanh toán bằng séc, tiền mặt hoặc chuyển khoản .");
$this->excel->getActiveSheet()->getStyle('A'.($i+22))->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+23).':H'.($i+23));
$this->excel->getActiveSheet()->setCellValue('A'.($i+23),"ĐIỀU 6:  QUYỀN VÀ TRÁCH NHIỆM BÊN A");
$this->excel->getActiveSheet()->getStyle('A'.($i+23))->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+24).':H'.($i+24));
$this->excel->getActiveSheet()->setCellValue('A'.($i+24),"6.1     Chịu trách nhiệm trước pháp luật về mục đích sử dụng và tính chính xác của các thông tin cung cấp, đảm bảo việc đăng ký, sử dụng dịch vụ đúng quy định hiện hành, không xâm phạm các quyền, lợi ích hợp pháp của các tổ chức, cá nhân khác.");
$this->excel->getActiveSheet()->getStyle('A'.($i+24))->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getRowDimension($i+24)->setRowHeight(66);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+25).':H'.($i+25));
$this->excel->getActiveSheet()->setCellValue('A'.($i+25),"6.2     Chịu trách nhiệm về việc quản lý, duy trì quyền sử dụng phần mềm quản lý khách hàng - bán hàng LifePos của mình và 
phải chịu trách nhiệm trong bất kỳ trường hợp vi phạm nào về sử dụng phần mềm quản lý khách hàng - bán hàng LifePos của mình do quản lý lỏng lẻo gây ra.");
$this->excel->getActiveSheet()->getStyle('A'.($i+25))->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getRowDimension($i+25)->setRowHeight(69);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+26).':H'.($i+26));
$this->excel->getActiveSheet()->setCellValue('A'.($i+26),"6.3     Chấp nhận các  Điều khoản thỏa thuận sử dụng phần mềm  tại địa chỉ: http://www.lifetek.com.vn và http://pos.vn là một
 phần không thể tách rời hợp đồng này.");
$this->excel->getActiveSheet()->getStyle('A'.($i+26))->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getRowDimension($i+26)->setRowHeight(55.50);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+27).':H'.($i+27));
$this->excel->getActiveSheet()->setCellValue('A'.($i+27),"6.4     Cung cấp thông tin và phối hợp với các cơ quan nhà nước có thẩm quyền để giải quyết, xử lý các vụ việc liên quan tới tên miền và các dịch vụ khác.");
$this->excel->getActiveSheet()->getStyle('A'.($i+27))->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getRowDimension($i+27)->setRowHeight(33);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+28).':H'.($i+28));
$this->excel->getActiveSheet()->setCellValue('A'.($i+28),"ĐIỀU 7: QUYỀN VÀ TRÁCH NHIỆM BÊN B");
$this->excel->getActiveSheet()->getStyle('A'.($i+28))->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+29).':H'.($i+29));
$this->excel->getActiveSheet()->setCellValue('A'.($i+29),"7.1     Hoàn thành việc khởi tạo phần mềm quản lý khách hàng - bán hàng trong vòng hai (02) ngày làm việc kể từ khi nhận được đầy đủ thông tin và tiền thanh toán của bên A.");
$this->excel->getActiveSheet()->getStyle('A'.($i+29))->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getRowDimension($i+29)->setRowHeight(33.75);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+30).':H'.($i+30));
$this->excel->getActiveSheet()->setCellValue('A'.($i+30),"7.2     Cung cấp và đảm vào các chuẩn kỹ thuật cần thiết để bên A thực hiện việc khai thác phần mềm liên quan.");
$this->excel->getActiveSheet()->getStyle('A'.($i+30))->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+31).':H'.($i+31));
$this->excel->getActiveSheet()->setCellValue('A'.($i+31),"7.3     Cung cấp cho bên A hoá đơn tài chính sau khi bên A đã thanh toán.");
$this->excel->getActiveSheet()->getStyle('A'.($i+31))->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+32).':H'.($i+32));
$this->excel->getActiveSheet()->setCellValue('A'.($i+32),"7.3     Cung cấp cho bên A hoá đơn tài chính sau khi bên A đã thanh toán.");
$this->excel->getActiveSheet()->getStyle('A'.($i+32))->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+33).':H'.($i+33));
$this->excel->getActiveSheet()->setCellValue('A'.($i+33),"7.4     Được quyền tạm ngưng các tài khoản truy cập tới LifeTek của Bên A hoặc xem xét chấm dứt hợp đồng của bên A
 trong trường hợp bên A không thực hiện đúng những điều khoản ràng buộc trong hợp đồng này.");
$this->excel->getActiveSheet()->getStyle('A'.($i+33))->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+34).':H'.($i+34));
$this->excel->getActiveSheet()->setCellValue('A'.($i+34),"ĐIỀU 8: PHẠT DO VI PHẠM HỢP ĐỒNG");
$this->excel->getActiveSheet()->getStyle('A'.($i+34))->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+35).':H'.($i+35));
$this->excel->getActiveSheet()->setCellValue('A'.($i+35),"8.1     Nếu bên B tự ý hủy hợp đồng mà không được sự chấp thuận của bên A, thì bên B phải hoàn trả toàn bộ số tiền đã nhận.");
$this->excel->getActiveSheet()->getStyle('A'.($i+35))->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+36).':H'.($i+36));
$this->excel->getActiveSheet()->setCellValue('A'.($i+36),"8.2     Nếu bên A tự ý hủy hợp đồng mà không được sự chấp thuận của bên B, thì bên A phải chịu mất toàn bộ số tiền đã thanh toán.");
$this->excel->getActiveSheet()->getStyle('A'.($i+36))->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+37).':H'.($i+37));
$this->excel->getActiveSheet()->setCellValue('A'.($i+37),"ĐIỀU 9: ĐIỀU KHOẢN CHUNG");
$this->excel->getActiveSheet()->getStyle('A'.($i+37))->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+38).':H'.($i+38));
$this->excel->getActiveSheet()->setCellValue('A'.($i+38),"9.1     Hợp đồng này có hiệu lực kể từ ngày ký và bên A đã thanh toán cho bên B theo Điều 2 Hợp đồng này. ");
$this->excel->getActiveSheet()->getStyle('A'.($i+38))->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+39).':H'.($i+39));
$this->excel->getActiveSheet()->setCellValue('A'.($i+39),"9.2     Mọi tranh chấp phát sinh trong quá trình thực hiện sẽ được hai Bên thương lượng giải quyết trên tinh thần hợp tác, tôn trọng lẫn nhau. Trường hợp hai Bên không thống nhất giải quyết thì vụ việc sẽ được chuyển đến Tòa án có thẩm quyền để giải quyết, phán quyết của Tòa án là quyết định cuối cùng buộc các Bên phải chấp hành.");
$this->excel->getActiveSheet()->getStyle('A'.($i+39))->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getRowDimension($i+39)->setRowHeight(63.75);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+40).':H'.($i+40));
$this->excel->getActiveSheet()->setCellValue('A'.($i+40),"9.3     Hợp đồng này được lập thành 02 bản tiếng Việt, có giá trị pháp lý như nhau, mỗi bên giữ 01 bản để thực hiện.");
$this->excel->getActiveSheet()->getStyle('A'.($i+40))->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+42).':H'.($i+42));
$this->excel->getActiveSheet()->setCellValue('A'.($i+42),"                 Hà nội, ngày … tháng ... năm 2013  ");
$this->excel->getActiveSheet()->getStyle('A'.($i+42))->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+44).':E'.($i+44));
$this->excel->getActiveSheet()->setCellValue('A'.($i+44),"ĐẠI DIỆN BÊN A");
$this->excel->getActiveSheet()->getStyle('A'.($i+44))->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('F'.($i+44).':H'.($i+44));
$this->excel->getActiveSheet()->setCellValue('F'.($i+44),"ĐẠI DIỆN BÊN B");
$this->excel->getActiveSheet()->getStyle('F'.($i+44))->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getStyle('A'.($i+44).':H'.($i+44))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

/* */
$this->excel->getActiveSheet()->getRowDimension(1)->setRowHeight(33.75);
$this->excel->getActiveSheet()->getRowDimension('2:5')->setRowHeight(15.75);
$this->excel->getActiveSheet()->getRowDimension(13)->setRowHeight(8.75);
$this->excel->getActiveSheet()->getRowDimension('14')->setRowHeight(18.75);
$this->excel->getActiveSheet()->getRowDimension(6)->setRowHeight(18.75);
$this->excel->getActiveSheet()->getRowDimension(7)->setRowHeight(18.75);
$this->excel->getActiveSheet()->getRowDimension(11)->setRowHeight(30.75);
$this->excel->getActiveSheet()->getRowDimension(12)->setRowHeight(30.75);
$this->excel->getActiveSheet()->getRowDimension(13)->setRowHeight(15.75);
$this->excel->getActiveSheet()->getRowDimension(16)->setRowHeight(18);
$this->excel->getActiveSheet()->getRowDimension(17)->setRowHeight(18);
$this->excel->getActiveSheet()->getRowDimension(18)->setRowHeight(18);
$this->excel->getActiveSheet()->getRowDimension(19)->setRowHeight(18);
$this->excel->getActiveSheet()->getRowDimension(20)->setRowHeight(18);
$this->excel->getActiveSheet()->getRowDimension(21)->setRowHeight(18);
$this->excel->getActiveSheet()->getRowDimension(22)->setRowHeight(18);
$this->excel->getActiveSheet()->getRowDimension(23)->setRowHeight(19.5);
$this->excel->getActiveSheet()->getRowDimension(24)->setRowHeight(36);
$this->excel->getActiveSheet()->getRowDimension(25)->setRowHeight(18);
$this->excel->getActiveSheet()->getRowDimension(26)->setRowHeight(18);
$this->excel->getActiveSheet()->getRowDimension(27)->setRowHeight(18);
$this->excel->getActiveSheet()->getRowDimension(28)->setRowHeight(18);
$this->excel->getActiveSheet()->getRowDimension(29)->setRowHeight(18);
$this->excel->getActiveSheet()->getRowDimension(30)->setRowHeight(18);
$this->excel->getActiveSheet()->getRowDimension(31)->setRowHeight(18);
$this->excel->getActiveSheet()->getRowDimension(32)->setRowHeight(31.30);
$this->excel->getActiveSheet()->getRowDimension($i)->setRowHeight(14.75);
$this->excel->getActiveSheet()->getRowDimension($i+1)->setRowHeight(14.75);
$this->excel->getActiveSheet()->getRowDimension($i+2)->setRowHeight(14.75);
$this->excel->getActiveSheet()->getRowDimension($i+3)->setRowHeight(14.75);
$this->excel->getActiveSheet()->getRowDimension($i+4)->setRowHeight(27.75);
$this->excel->getActiveSheet()->getRowDimension($i+5)->setRowHeight(18);
$this->excel->getActiveSheet()->getRowDimension($i+6)->setRowHeight(12.75);

$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(14.48);
$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(9.08);
$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(9.08);
$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(9.08);
$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(6.45);
$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(9.08);
$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15.33);
$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(18.33);

//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
//if you want to save it as .XLSX Excel 2007 format
$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
$md5file = md5(date('YmdHis')) . '.xlsx';
//$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
//force user to download the Excel file without writing it to server's HD
//$filename = 'hopdong.xlsx'; //save our workbook as this file name
//$filename = 'Book1.xlsx';
$filename = "HD_".$sale_id."_".str_replace(" ","",replace_character($data['customer']))."_".date('dmYHis').".xlsx";
//$objWriter->save($filename);
$objWriter->save(APPPATH . "/../excel_materials/".$filename);
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