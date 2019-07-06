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

/*$this->excel->setActiveSheetIndex(0)->mergeCells('A1:D1');
$this->excel->getActiveSheet()->setCellValue('A1',$this->config->item('company'));
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setWrapText(true);*/

$this->excel->setActiveSheetIndex(0)->mergeCells('A1:H1');
$this->excel->getActiveSheet()->setCellValue('A1',"CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM \n Độc lập – Tự do – Hạnh phúc");
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A2:H2');
$this->excel->getActiveSheet()->setCellValue('A2',"--------***--------");
$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A4:H4');
$this->excel->getActiveSheet()->setCellValue('A4',"BIÊN BẢN THANH LÝ HỢP ĐỒNG");
$this->excel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A4')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A5:H5');
$this->excel->getActiveSheet()->setCellValue('A5',"(Số: 241013/PN-HLV/HĐKT)");
$this->excel->getActiveSheet()->getStyle('A5')->getFont()->setItalic(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A5')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A6:H6');
$this->excel->getActiveSheet()->setCellValue('A6',"- Căn cứ vào Luật Dân sự số 33/2005/QH11 ngày 14 tháng 6 năm 2005 của Quốc nước Cộng hòa xã hội Chủ nghĩa Việt Nam.");
$this->excel->getActiveSheet()->getStyle('A6')->getFont()->setItalic(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A7:H7');
$this->excel->getActiveSheet()->setCellValue('A7',"- Căn cứ vào Luật Thương mại số 36/2005/QH11 ngày 14 tháng 6 năm 2005 của Quốc nước Cộng hòa xã hội Chủ nghĩa Việt Nam.");
$this->excel->getActiveSheet()->getStyle('A7')->getFont()->setItalic(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A7')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A8:H8');
$this->excel->getActiveSheet()->setCellValue('A8',"- Căn cứ vào hợp đồng kinh tế số: 241013/PN-HLV/HĐKT.");
$this->excel->getActiveSheet()->getStyle('A8')->getFont()->setItalic(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A8')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A9:H9');
$this->excel->getActiveSheet()->setCellValue('A9',"- Căn cứ vào biên bản bàn giao nghiệm thu có chữ ký xác nhận của hai bên.");
$this->excel->getActiveSheet()->getStyle('A9')->getFont()->setItalic(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A9')->getAlignment()->setWrapText(true);

/*$ngay = date('d');
$thang = date('m');
$nam = date('Y');
$this->excel->setActiveSheetIndex(0)->mergeCells('A9:H9');
$this->excel->getActiveSheet()->setCellValue('A9','Hôm nay, Ngày '.$ngay.' Tháng '.$thang.' Năm '.$nam.' tại Hà Nội. Chúng tôi gồm:');
$this->excel->getActiveSheet()->getStyle('A9')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A9')->getFont()->setItalic(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A9')->getAlignment()->setWrapText(true);*/

$this->excel->setActiveSheetIndex(0)->mergeCells('A11:H11');
$this->excel->getActiveSheet()->setCellValue('A11',"");
$this->excel->getActiveSheet()->getStyle('A11')->getFont()->setItalic(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A11')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A11')->getAlignment()->setWrapText(true);

/*$this->excel->setActiveSheetIndex(0)->mergeCells('A12:H12');
$this->excel->getActiveSheet()->setCellValue('A12',"");
$this->excel->getActiveSheet()->getStyle('A12')->getFont()->setItalic(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A12')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A12')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A13:H13');
$this->excel->getActiveSheet()->setCellValue('A13',"");
$this->excel->getActiveSheet()->getStyle('A13')->getFont()->setItalic(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A13')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A13')->getAlignment()->setWrapText(true);*/

$this->excel->setActiveSheetIndex(0)->mergeCells('A12:H12');
$this->excel->getActiveSheet()->setCellValue('A12',"Bên A (Chủ đầu tư)");
$this->excel->getActiveSheet()->getStyle('A12')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A12')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A12')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('A12')->getAlignment()->setWrapText(true);

/*$this->excel->setActiveSheetIndex(0)->mergeCells('B14:H14');
$this->excel->getActiveSheet()->setCellValue('B14',"(Chủ đầu tư)");
$this->excel->getActiveSheet()->getStyle('B14')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('B14')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B14')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('B14')->getAlignment()->setWrapText(true);*/

$this->excel->getActiveSheet()->setCellValue('A13',"");
$this->excel->getActiveSheet()->getStyle('A13')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A13')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A13')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('A13')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('B13:H13');
$this->excel->getActiveSheet()->setCellValue('B13',mb_convert_case($cust_info->company_name,MB_CASE_UPPER, "UTF-8" ));
$this->excel->getActiveSheet()->getStyle('B13')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('B13')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B13')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('B13')->getAlignment()->setWrapText(true);

$this->excel->getActiveSheet()->setCellValue('A14',"Đại diện bởi");
$this->excel->getActiveSheet()->getStyle('A14')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A14')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A14')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('A14')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('B14:H14');
$this->excel->getActiveSheet()->setCellValue('B14',"(Ông/Bà) ".$data['customer']);
$this->excel->getActiveSheet()->getStyle('B14')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('B14')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B14')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('B14')->getAlignment()->setWrapText(true);

/*$this->excel->setActiveSheetIndex(0)->mergeCells('B16:H17');
$this->excel->getActiveSheet()->setCellValue('B16',$data['address']);
$this->excel->getActiveSheet()->getStyle('B16')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('B16')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B16')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('B16')->getAlignment()->setWrapText(true);*/

$this->excel->getActiveSheet()->setCellValue('A15',"Chức vụ");
$this->excel->getActiveSheet()->getStyle('A15')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A15')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A15')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('A15')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('B15:H15');
$this->excel->getActiveSheet()->setCellValue('B15',$cust_info->positions);
$this->excel->getActiveSheet()->getStyle('B15')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('B15')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B15')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('B15')->getAlignment()->setWrapText(true);

$this->excel->getActiveSheet()->setCellValue('A16',"Địa chỉ");
$this->excel->getActiveSheet()->getStyle('A16')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A16')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A16')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('A16')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('B16:H16');
$this->excel->getActiveSheet()->setCellValue('B16',$data['address']);
$this->excel->getActiveSheet()->getStyle('B16')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('B16')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B16')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('B16')->getAlignment()->setWrapText(true);


$this->excel->getActiveSheet()->setCellValue('A17',"Điện thoại");
$this->excel->getActiveSheet()->getStyle('A17')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A17')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A17')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('A17')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('B17:E17');
$this->excel->getActiveSheet()->setCellValue('B17',$cust_info->phone_number);
$this->excel->getActiveSheet()->getStyle('B17')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('B17')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B17')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('B17')->getAlignment()->setWrapText(true);

$this->excel->getActiveSheet()->setCellValue('F17',"Fax");
$this->excel->getActiveSheet()->getStyle('F17')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('F17')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('F17')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('F17')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('G17:H17');
$this->excel->getActiveSheet()->setCellValue('G17',"");
$this->excel->getActiveSheet()->getStyle('G17')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('G17')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('G17')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('G17')->getAlignment()->setWrapText(true);

$this->excel->getActiveSheet()->setCellValue('A18',"Số tài khoản");
$this->excel->getActiveSheet()->getStyle('A18')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A18')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A18')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('A18')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('B18:H18');
$this->excel->getActiveSheet()->setCellValue('B18',$data['account_number']);
$this->excel->getActiveSheet()->getStyle('B18')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('B18')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B18')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('B18')->getAlignment()->setWrapText(true);

$this->excel->getActiveSheet()->setCellValue('A19',"Tại ngân hàng");
$this->excel->getActiveSheet()->getStyle('A19')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A19')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A19')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('A19')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('B19:H19');
$this->excel->getActiveSheet()->setCellValue('B19',"");
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


$this->excel->setActiveSheetIndex(0)->mergeCells('A21:H21');
$this->excel->getActiveSheet()->setCellValue('A21',"");
$this->excel->getActiveSheet()->getStyle('A21')->getFont()->setItalic(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A21')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A21')->getAlignment()->setWrapText(true);
/* thong tin ben B */

$this->excel->setActiveSheetIndex(0)->mergeCells('A22:H22');
$this->excel->getActiveSheet()->setCellValue('A22',"Bên B (Nhà cung cấp)");
$this->excel->getActiveSheet()->getStyle('A22')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A22')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A22')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('A22')->getAlignment()->setWrapText(true);

/*$this->excel->setActiveSheetIndex(0)->mergeCells('B23:H23');
$this->excel->getActiveSheet()->setCellValue('B23', "(Nhà cung cấp)");
$this->excel->getActiveSheet()->getStyle('B23')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('B23')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B23')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('B23')->getAlignment()->setWrapText(true);
*/

$this->excel->getActiveSheet()->setCellValue('A23',"");
$this->excel->getActiveSheet()->getStyle('A23')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A23')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A23')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('A23')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('B23:H23');
$this->excel->getActiveSheet()->setCellValue('B23',$this->config->item('company'));
$this->excel->getActiveSheet()->getStyle('B23')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('B23')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B23')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('B23')->getAlignment()->setWrapText(true);


$this->excel->getActiveSheet()->setCellValue('A24',"Đại diện bởi");
$this->excel->getActiveSheet()->getStyle('A24')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A24')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A24')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('A24')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('B24:H24');
$this->excel->getActiveSheet()->setCellValue('B24'," Ông Phạm Văn Thanh ");
$this->excel->getActiveSheet()->getStyle('B24')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('B24')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B24')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('B24')->getAlignment()->setWrapText(true);

$this->excel->getActiveSheet()->setCellValue('A25',"Chức vụ");
$this->excel->getActiveSheet()->getStyle('A25')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A25')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A25')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('A25')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('B25:H25');
$this->excel->getActiveSheet()->setCellValue('B25'," Giám đốc Công ty");
$this->excel->getActiveSheet()->getStyle('B25')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B25')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('B25')->getAlignment()->setWrapText(true);


$this->excel->getActiveSheet()->setCellValue('A26',"Địa chỉ");
$this->excel->getActiveSheet()->getStyle('A26')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A26')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A26')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('A26')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('B26:H26');
$this->excel->getActiveSheet()->setCellValue('B26',$this->config->item('address'));
$this->excel->getActiveSheet()->getStyle('B26')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('B26')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B26')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('B26')->getAlignment()->setWrapText(true);

$this->excel->getActiveSheet()->setCellValue('A27',"Điện thoại");
$this->excel->getActiveSheet()->getStyle('A27')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A27')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A27')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('A27')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('B27:E27');
$this->excel->getActiveSheet()->setCellValue('B27',$this->config->item('phone'));
$this->excel->getActiveSheet()->getStyle('B27')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('B27')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B27')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('B27')->getAlignment()->setWrapText(true);


$this->excel->getActiveSheet()->setCellValue('F27',"Fax");
$this->excel->getActiveSheet()->getStyle('F27')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('F27')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('F27')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('F27')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('G27:H27');
$this->excel->getActiveSheet()->setCellValue('G27',' 04 6265 0065');
$this->excel->getActiveSheet()->getStyle('G27')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('G27')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('G27')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('G27')->getAlignment()->setWrapText(true);

$this->excel->getActiveSheet()->setCellValue('A28',"Mã số thuế");
$this->excel->getActiveSheet()->getStyle('A28')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A28')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A28')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('A28')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('B28:H28');
$this->excel->getActiveSheet()->setCellValue('B28', '0103962351');
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
$this->excel->getActiveSheet()->setCellValue('B29','102 010 001 579 242');
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
$this->excel->getActiveSheet()->setCellValue('B30','Ngân hàng TMCP Công thương Việt Nam – CN Nam Thăng Long.');
$this->excel->getActiveSheet()->getStyle('B30')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('B30')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B30')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('B30')->getAlignment()->setWrapText(true);

/* end thong tin ben B */
$this->excel->setActiveSheetIndex(0)->mergeCells('A31:H31');
$this->excel->getActiveSheet()->setCellValue('A31',"");
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


$ngay = date('d');
$thang = date('m');
$nam = date('Y');
/*$this->excel->setActiveSheetIndex(0)->mergeCells('A9:H9');
$this->excel->getActiveSheet()->setCellValue('A9','Hôm nay, Ngày '.$ngay.' Tháng '.$thang.' Năm '.$nam.' tại Hà Nội. Chúng tôi gồm:');
$this->excel->getActiveSheet()->getStyle('A9')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A9')->getFont()->setItalic(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A9')->getAlignment()->setWrapText(true);*/

$this->excel->setActiveSheetIndex(0)->mergeCells('A33:H33');
$this->excel->getActiveSheet()->setCellValue('A33'," Hôm nay, ngày ".$ngay." tháng ".$thang." năm ".$nam."Hai bên thống nhất ký kết Hợp đồng kinh tế số: 241013/PN-HLV/HĐKT theo các điều khoản sau:");
$this->excel->getActiveSheet()->getStyle('A33')->getFont()->setBold(true)->setItalic(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A33')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A33')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A34:H34');
$this->excel->getActiveSheet()->setCellValue('A34',"Điều 1:   Giao nhận");
$this->excel->getActiveSheet()->getStyle('A34')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A34')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->getActiveSheet()->getStyle('A34')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A35:H35');
$this->excel->getActiveSheet()->setCellValue('A35',"Bên B đã hoàn thành việc cung cấp lắp đặt cho bên A hệ thống camera theo như hợp đồng kinh tế số 241013/PN-HLV/HĐKT  đã ký, kèm theo biên bản bàn giao, nghiệm thu có chữ ký xác nhận của đại diện hai bên.");
$this->excel->getActiveSheet()->getStyle('A35')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A35')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->getActiveSheet()->getStyle('A35')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A36:H36');
$this->excel->getActiveSheet()->setCellValue('A36',"Điều 2:  Nghiệm thu và bàn giao");
$this->excel->getActiveSheet()->getStyle('A36')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A36')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->getActiveSheet()->getStyle('A36')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('B37:H37');
$this->excel->getActiveSheet()->setCellValue('B37',"Bên B đã bàn giao đầy đủ thiết bị và phụ kiện đi kèm cho bên A.");
$this->excel->getActiveSheet()->getStyle('B37')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('B37')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->getActiveSheet()->getStyle('B37')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('B38:H38');
$this->excel->getActiveSheet()->setCellValue('B38',"Quá trình thi công, lắp đặt của bên B đạt các yêu cầu đặt ra của bên A và hợp đồng.");
$this->excel->getActiveSheet()->getStyle('B38')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('B38')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B38')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('B39:H39');
$this->excel->getActiveSheet()->setCellValue('B39'," Bên B đã hướng dẫn sử dụng đầy đủ cho bên A.");
$this->excel->getActiveSheet()->getStyle('B39')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('B39')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B39')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A40:H40');
$this->excel->getActiveSheet()->setCellValue('A40'," ");
$this->excel->getActiveSheet()->getStyle('A40')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A40')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->getActiveSheet()->getStyle('A40')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A41:H41');
$this->excel->getActiveSheet()->setCellValue('A41',"Điều 3: Trách nhiệm bảo hành");
$this->excel->getActiveSheet()->getStyle('A41')->getFont()->setBold(true)->setItalic(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A41')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->getActiveSheet()->getStyle('A41')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A42:H42');
$this->excel->getActiveSheet()->setCellValue('A42'," - Thời gian bảo hành: Bên B bảo hành miễn phí sản phẩm của mình là 12 tháng với thiết bị chính và 06 tháng với phụ kiện đi kèm.");
$this->excel->getActiveSheet()->getStyle('A42')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A42')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->getActiveSheet()->getStyle('A42')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A43:H43');
$this->excel->getActiveSheet()->setCellValue('A43'," - Điều kiện bảo hành: Với các lỗi của nhà sản xuất và của người thi công lắp đặt trong thời gian bảo hành. ");
$this->excel->getActiveSheet()->getStyle('A43')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A43')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->getActiveSheet()->getStyle('A43')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A44:H44');
$this->excel->getActiveSheet()->setCellValue('A44'," - Trong trường hợp xảy ra bất kỳ sự cố nào liên quan đến việc bảo hành, Bên A sẽ giữ nguyên hiện trường và thông báo bằng văn bản cho .Bên B để Bên B cử nhân viên đến kiểm tra và xác định nguyên nhân hư hỏng và có biện pháp khắc phục.  ");
$this->excel->getActiveSheet()->getStyle('A44')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A44')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->getActiveSheet()->getStyle('A44')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A45:H45');
$this->excel->getActiveSheet()->setCellValue('A45',"- Từ chối bảo hành: Với các sản phẩm bị mất tem mác, móp méo, bị phá hoại do người sử dụng tự ý tháo lắp, thay đổi thiết kế ban đầu của nhà sản xuất, người sử dụng vận hành sai hướng dẫn của nhà cung cấp. Hệ thống điện cung cấp không ổn định dẫn đến chập điện hay do yếu tố khách quan thiên tai, địch họa… không không do lỗi của nhà sản xuất.");
$this->excel->getActiveSheet()->getStyle('A45')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A45')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->getActiveSheet()->getStyle('A45')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A46:H46');
$this->excel->getActiveSheet()->setCellValue('A46'," ");
$this->excel->getActiveSheet()->getStyle('A46')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A46')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->getActiveSheet()->getStyle('A46')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A47:H47');
$this->excel->getActiveSheet()->setCellValue('A47',"Điều 4: Thanh quyết toán");
$this->excel->getActiveSheet()->getStyle('A47')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A47')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->getActiveSheet()->getStyle('A47')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A48:H48');
$this->excel->getActiveSheet()->setCellValue('A48',"-         Giá trị hợp đồng đã thực hiện:");
$this->excel->getActiveSheet()->getStyle('A48')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A48')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->getActiveSheet()->getStyle('A48')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A49:H49');
$this->excel->getActiveSheet()->setCellValue('A49',"");
$this->excel->getActiveSheet()->getStyle('A49')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A49')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->getActiveSheet()->getStyle('A49')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A50:H50');
$this->excel->getActiveSheet()->setCellValue('A50',"ĐIỀU IV: GIÁ TRỊ HỢP ĐỒNG");
$this->excel->getActiveSheet()->getStyle('A50')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A50')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->getActiveSheet()->getStyle('A50')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A51:H51');
$this->excel->getActiveSheet()->setCellValue('A51',"-    Giá trị Hợp đồng tạm tính: ". $data['total']."(VNĐ)");
$this->excel->getActiveSheet()->getStyle('A51')->getFont()->setSize(12)->setBold(true)->setItalic(true);
$this->excel->getActiveSheet()->getStyle('A51')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->getActiveSheet()->getStyle('A51')->getAlignment()->setWrapText(true);

$this->load->model('Cost');
$tongtien1 = to_currency_unVND_nomar($data['total']/10);
$tong_tienno_chu = 0;
$tienno_chus = $this->Cost->get_tongtien_tra($sale_id);
foreach ($tienno_chus as $tienno_chu){
	$tong_tienno_chu += $tienno_chu['payment_amount'];
}
   
$this->excel->setActiveSheetIndex(0)->mergeCells('A52:H52');
$this->excel->getActiveSheet()->setCellValue('A52',"  (Bằng chữ: ".$this->Cost->get_string_number($tongtien1)." )");
    $this->excel->getActiveSheet()->getStyle('A52')->getFont()->setSize(12)->setBold(true)->setItalic(true);
$this->excel->getActiveSheet()->getStyle('A52')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A52')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A53:H53');
$this->excel->getActiveSheet()->setCellValue('A53'," -    Giá trên đã bao gồm thuế GTGT và chi phí vận chuyển, lắp đặt theo yêu cầu của bên A.");
$this->excel->getActiveSheet()->getStyle('A53')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A53')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A53')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A54:H54');
$this->excel->getActiveSheet()->setCellValue('A54',"ĐIỀU VI:  TRÁCH NHIỆM CỦA CÁC BÊN ");
$this->excel->getActiveSheet()->getStyle('A54')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A54')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A54')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A55:H55');
$this->excel->getActiveSheet()->setCellValue('A55',"1. Trách nhiệm của Bên A: ");
$this->excel->getActiveSheet()->getStyle('A55')->getFont()->setBold(true)->setItalic(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A55')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A55')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A56:H56');
$this->excel->getActiveSheet()->setCellValue('A56'," - Có trách nhiệm kiểm tra số lượng, chất lượng và chủng loại thiết bị theo Hợp đồng đã ký kết và có trách nhiệm ký biên bản nghiệm thu bàn giao sau khi Bên B hoàn thành các công việc theo Hợp đồng.");
$this->excel->getActiveSheet()->getStyle('A55')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A56')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A56')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A57:H57');
$this->excel->getActiveSheet()->setCellValue('A57'," - Bố trí mặt bằng, nguồn điện, cử cán bộ phối hợp, giám sát và giúp đỡ bên B trong quá trình thi công.");
$this->excel->getActiveSheet()->getStyle('A57')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A57')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A57')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A58:H58');
$this->excel->getActiveSheet()->setCellValue('A58'," - Chịu trách nhiệm sử dụng và bảo quản sản phẩm theo đúng hướng dẫn của nhà sản xuất.");
$this->excel->getActiveSheet()->getStyle('A58')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A58')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A58')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A59:H59');
$this->excel->getActiveSheet()->setCellValue('A59'," - Đảm bảo thanh toán đầy đủ theo đúng tiến độ hợp đồng đã ký kết.");
$this->excel->getActiveSheet()->getStyle('A59')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A59')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A59')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A60:H60');
$this->excel->getActiveSheet()->setCellValue('A60',"2. Trách nhiệm của Bên B:");
$this->excel->getActiveSheet()->getStyle('A60')->getFont()->setBold(true)->setItalic(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A60')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A60')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A61:H61');
$this->excel->getActiveSheet()->setCellValue('A61'," - Đảm bảo đúng tiến độ cung cấp hàng hóa và tiến độ lắp đặt theo Hợp đồng.");
$this->excel->getActiveSheet()->getStyle('A61')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A61')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A61')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A62:H62');
$this->excel->getActiveSheet()->setCellValue('A62'," - Cung cấp sản phẩm đúng chủng loại, số lượng, chất lượng theo Hợp đồng.");
$this->excel->getActiveSheet()->getStyle('A62')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A62')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A62')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A63:H63');
$this->excel->getActiveSheet()->setCellValue('A63'," - Trong quá trình lắp đặt, phối hợp với cán bộ bên A để mọi việc diễn ra đúng theo quy định của pháp luật và nội quy của bên A.");
$this->excel->getActiveSheet()->getStyle('A63')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A63')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A63')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A64:H64');
$this->excel->getActiveSheet()->setCellValue('A64'," - Chịu trách nhiệm về an toàn và vệ sinh lao động trong quá trình lắp đặt, hạn chế thấp nhất ảnh hưởng đến hoạt động bình thường 
của Bên A; Dọn sạch sẽ rác thải phát sinh trong quá trình thực hiện công việc lắp đặt theo Hợp đồng;");
$this->excel->getActiveSheet()->getStyle('A64')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A64')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A64')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A65:H65');
$this->excel->getActiveSheet()->setCellValue('A65'," - Chịu trách nhiệm bảo hành như điều II của hợp đồng.");
$this->excel->getActiveSheet()->getStyle('A65')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A65')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A65')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A66:H66');
$this->excel->getActiveSheet()->setCellValue('A66'," - Có trách nhiệm hướng dẫn cho Bên A về cách thức sử dụng, bảo quản sản phẩm đúng hướng dẫn của nhà sản xuất khi giao và lắp 
đặt sản phẩm cho Bên A.");
$this->excel->getActiveSheet()->getStyle('A66')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A66')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A66')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A67:H67');
$this->excel->getActiveSheet()->setCellValue('A67'," - Có trách nhiệm bồi thường thiệt hại cho Bên A mọi thiệt hại có liên quan do lỗi của Bên B gây ra trong quá trình lắp đặt sản phẩm (nếu có).");
$this->excel->getActiveSheet()->getStyle('A67')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A67')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A67')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A68:H68');
$this->excel->getActiveSheet()->setCellValue('A68',"ĐIỀU VII: CAM KẾT CHUNG");
$this->excel->getActiveSheet()->getStyle('A68')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A68')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A68')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A69:H69');
$this->excel->getActiveSheet()->setCellValue('A69'," - Hai bên cam kết thực hiện đầy đủ các điều khoản đã ghi trong Hợp đồng. Trong quá trình thực hiện, nếu có khó khăn, vướng mắc hai bên gặp nhau cùng bàn bạc giải quyết trên tinh thần hợp tác. Không bên nào được đơn phương thay đổi các điều khoản của Hợp đồng đã ký. Sự thay đổi các điều khoản của Hợp đồng chỉ có giá trị bằng văn bản được hai bên ký kết.");
$this->excel->getActiveSheet()->getStyle('A69')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A69')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A69')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A70:H70');
$this->excel->getActiveSheet()->setCellValue('A70',"- Trường hợp hai bên không thể thống nhất các vấn đề phát sinh, tranh chấp gây thiệt hại cho bên kia thì sẽ đưa ra Tòa án có thẩm quyền để giải quyết, quyết định của Tòa án sẽ là quyết định cuối cùng hai bên phải chấp hành. Bên vi phạm sẽ chịu mọi phí tổn của Tòa án.");
$this->excel->getActiveSheet()->getStyle('A70')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A70')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A70')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A71:H71');
$this->excel->getActiveSheet()->setCellValue('A71',"  - Hợp đồng tự động thanh lý khi các bên hoàn thành các nghĩa vụ theo Hợp đồng.");
$this->excel->getActiveSheet()->getStyle('A71')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A71')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A71')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A72:H72');
$this->excel->getActiveSheet()->setCellValue('A72',"  - Hợp đồng này được lập thành 02 bản mỗi bên giữ 01 bản, có giá trị pháp lý như nhau, có hiệu lực kể từ ngày ký.");
$this->excel->getActiveSheet()->getStyle('A72')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A72')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A72')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A73:H73');
$this->excel->getActiveSheet()->setCellValue('A73'," ");
$this->excel->getActiveSheet()->getStyle('A73')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A73')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A73')->getAlignment()->setWrapText(true);

/*$this->excel->setActiveSheetIndex(0)->mergeCells('A74:E74');
$this->excel->getActiveSheet()->setCellValue('A74',"ĐẠI DIỆN BÊN A ");
$this->excel->getActiveSheet()->getStyle('A74')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A74')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A74')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('F74:H74');
$this->excel->getActiveSheet()->setCellValue('F74',"ĐẠI DIỆN BÊN B ");
$this->excel->getActiveSheet()->getStyle('F74')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('F74')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('F74')->getAlignment()->setWrapText(true);*/

$this->excel->setActiveSheetIndex(0)->mergeCells('A75:H75');
$this->excel->getActiveSheet()->setCellValue('A75'," ");
$this->excel->getActiveSheet()->getStyle('A75')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A75')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A75')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A76:H76');
$this->excel->getActiveSheet()->setCellValue('A76'," ");
$this->excel->getActiveSheet()->getStyle('A76')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A76')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A76')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A77:H77');
$this->excel->getActiveSheet()->setCellValue('A77'," PHỤ LỤC HỢP ĐỒNG");
$this->excel->getActiveSheet()->getStyle('A77')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A77')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A77')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A78:H78');
$this->excel->getActiveSheet()->setCellValue('A78',"(Là phần không thể tách rời của hợp đồng kinh tế số: 241013/PN-HLV/HĐKT)");
$this->excel->getActiveSheet()->getStyle('A78')->getFont()->setBold(true)->setItalic(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A78')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A78')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A79:H79');
$this->excel->getActiveSheet()->setCellValue('A79'," ");
$this->excel->getActiveSheet()->getStyle('A79')->getFont()->setBold(true)->setItalic(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A79')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A79')->getAlignment()->setWrapText(true);


$this->excel->getActiveSheet()->setCellValue('A80',"STT");
//$this->excel->getActiveSheet()->setCellValue('E25',"Tên hàng hóa, dịch vụ");
$this->excel->getActiveSheet()->getStyle('A80')->getFont()->setBold(true);
$this->excel->setActiveSheetIndex(0)->mergeCells('B80:D80');
$this->excel->getActiveSheet()->setCellValue('B80',"Tên hàng hóa, dịch vụ");
$this->excel->getActiveSheet()->getStyle('B80')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('B80')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A80')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->setCellValue('E80',"ĐV tính");
$this->excel->getActiveSheet()->setCellValue('F80',"Số lượng");
$this->excel->getActiveSheet()->setCellValue('G80',"Đơn giá");
$this->excel->getActiveSheet()->setCellValue('H80',"Thành tiền");

$this->excel->getActiveSheet()->setCellValue('A81',"(Items)");
$this->excel->getActiveSheet()->getStyle('A81')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A81')->getFont()->setItalic(true);
$this->excel->setActiveSheetIndex(0)->mergeCells('B81'.':D81');
$this->excel->getActiveSheet()->setCellValue('B81',"(Description)");
$this->excel->getActiveSheet()->getStyle('B81')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B81')->getFont()->setItalic(true);
$this->excel->getActiveSheet()->setCellValue('E81',"(Units)");
$this->excel->getActiveSheet()->getStyle('E81')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('E81')->getFont()->setItalic(true);
$this->excel->getActiveSheet()->setCellValue('F81',"(Quantity)");
$this->excel->getActiveSheet()->getStyle('F81')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('F81')->getFont()->setItalic(true);
$this->excel->getActiveSheet()->setCellValue('G81',"(Unit price)");
$this->excel->getActiveSheet()->getStyle('G81')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('G81')->getFont()->setItalic(true);
$this->excel->getActiveSheet()->getStyle('H81')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->setCellValue('H81',"(Amount)");
$this->excel->getActiveSheet()->getStyle('H81')->getFont()->setItalic(true);
$this->excel->getActiveSheet()->getStyle('A81:H81')->getFont()->setSize(9);

$this->excel->getActiveSheet()->getStyle('A81:H81')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
//DEN DAY
$this->excel->getActiveSheet()->getStyle('F80:H80')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('F80:H80')->getFont()->setSize(9);
$this->excel->getActiveSheet()->getStyle('F80:H80')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->setActiveSheetIndex(0)->mergeCells('B82'.':D82');
$this->excel->getActiveSheet()->setCellValue('A82',"A");
$this->excel->getActiveSheet()->setCellValue('B82',"B");
$this->excel->getActiveSheet()->getStyle('B82:H82')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B82')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('C82')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->setCellValue('E82',"C");
$this->excel->getActiveSheet()->setCellValue('F82',"1");
$this->excel->getActiveSheet()->setCellValue('G82',"2");
$this->excel->getActiveSheet()->setCellValue('H82',"3 = 1 x 2");
$this->excel->getActiveSheet()->getStyle('A82:H82')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('A82:H82')->getFont()->setSize(8);
$this->excel->getActiveSheet()->getStyle('A82:H82')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
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
$k = 83;
$stt = 1;
$tongtienhang = 0;
foreach(array_reverse($data['cart'], true) as $line=>$item){
	$this->excel->getActiveSheet()->setCellValue('A'.$k,$stt);
        $this->excel->getActiveSheet()->getStyle('A'.$k)->getAlignment()->setIndent(1);
        $this->excel->setActiveSheetIndex(0)->mergeCells('B'.$k.':D'.$k);
	$this->excel->getActiveSheet()->setCellValue('B'.$k,$item['name']);
	
        $this->excel->getActiveSheet()->getStyle('B'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$this->excel->getActiveSheet()->setCellValue('E'.$k,$this->Unit->item_unit($this->Item->get_info($item['item_id'])->unit)->name);
        $this->excel->getActiveSheet()->getStyle('E'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$this->excel->getActiveSheet()->setCellValue('F'.$k,$item['quantity']);
        $this->excel->getActiveSheet()->getStyle('F'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$this->excel->getActiveSheet()->setCellValue('G'.$k,to_currency_unVND_nomar($item['price']));
        $this->excel->getActiveSheet()->getStyle('G'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	$this->excel->getActiveSheet()->setCellValue('H'.$k,to_currency_unVND_nomar($item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100));
        $this->excel->getActiveSheet()->getStyle('H'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
 
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
$this->excel->getActiveSheet()->getStyle('A80:H'.($k-1))->getBorders()->getTOP()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('A82:H'.($k-1))->getBorders()->getTOP()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('A83:H'.($k-1))->getBorders()->getTOP()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('A80:A'.($k-1))->getBorders()->getLEFT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('A80:A'.($k-1))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('B80:B'.($k-1))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('D80:D'.($k-1))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('E80:E'.($k-1))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('F80:F'.($k-1))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('G80:G'.($k-1))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('H80:H'.($k-1))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//$this->excel->getActiveSheet()->getStyle('I25:I'.($k-1))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
/* end style border cot */
if($k < 96){
for($a = $k ;$a < 96;$a++){
		$this->excel->setActiveSheetIndex(0)->mergeCells('B'.$a.':D'.$a);
	    $this->excel->getActiveSheet()->getStyle('A'.$a.':H' . ($a))->applyFromArray($styleDOTBlackBorderOutline);
		$this->excel->getActiveSheet()->getRowDimension($a)->setRowHeight(19.50);
} }
/* style border cot */
$this->excel->getActiveSheet()->getStyle('A'.($k).':A95')->getBorders()->getLEFT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('A'.($k).':A95')->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('D'.($k).':D95')->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('E'.($k).':E95')->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('F'.($k).':F95')->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('G'.($k).':G95')->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('H'.($k).':H95')->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
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
$this->excel->getActiveSheet()->getStyle('A'.($i).':G'.($i+2))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('A'.($i+3).':H'.($i+2))->getBorders()->getBOTTOM()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('A'.($i).':H'.($i))->getBorders()->getTOP()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('D'.($i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$this->excel->getActiveSheet()->getStyle('D'.($i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->setCellValue('H'.$i,to_currency_unVND_nomar($tongtienhang));
$this->excel->getActiveSheet()->getStyle('H'.($i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$this->excel->getActiveSheet()->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$this->excel->getActiveSheet()->getStyle('H'.($i))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

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
$this->excel->getActiveSheet()->getCell('A'.($i+1))->setValue($objRichText2);
$this->excel->getActiveSheet()->getStyle('A'.($i+1))->getAlignment()->setIndent(1);
$this->excel->getActiveSheet()->getStyle('A'.($i+1).':H'.($i+1))->getBorders()->getTOP()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('B'.($i+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B'.($i+1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

$this->excel->setActiveSheetIndex(0)->mergeCells('D'.($i+1).':G'.($i+1));
$this->excel->getActiveSheet()->setCellValue('D'.($i+1),"Tiền thuế GTGT (VAT): ");
$this->excel->getActiveSheet()->getStyle('D'.($i+1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('D'.($i+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$this->excel->getActiveSheet()->getStyle('D'.($i+1))->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('D'.($i+1))->getFont()->setSize(9);
$this->excel->getActiveSheet()->setCellValue('H'.($i+1),to_currency_unVND_nomar($data['total'] - $tongtienhang));
$this->excel->getActiveSheet()->getStyle('H'.($i+1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('H'.($i+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$this->excel->getActiveSheet()->getStyle('H'.($i+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$this->excel->getActiveSheet()->getStyle('H'.($i+1))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);



$this->excel->setActiveSheetIndex(0)->mergeCells('D'.($i+2).':G'.($i+2));
$this->excel->getActiveSheet()->setCellValue('D'.($i+2),"Tổng cộng tiền thanh toán (Total):");
$this->excel->getActiveSheet()->getStyle('A'.($i+2).':H'.($i+2))->getBorders()->getTOP()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('D'.($i+2))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('D'.($i+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$this->excel->getActiveSheet()->getStyle('D'.($i+2))->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('D'.($i+2))->getFont()->setSize(9);

$this->excel->getActiveSheet()->setCellValue('H'.($i+2),to_currency_unVND_nomar($data['total']));
$this->excel->getActiveSheet()->getStyle('H'.($i+2))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('H'.($i+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$this->excel->getActiveSheet()->getStyle('H'.($i+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$this->excel->getActiveSheet()->getStyle('H'.($i+2))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('H'.($i+3))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('G'.($i+3))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
// $this->excel->getActiveSheet()->setCellValue('I'.($i+2),to_currency_unVND_nomar($data['total']);
// $this->excel->getActiveSheet()->getStyle('I'.($i+2))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
// foreach($data['payments'] as $payment_id=>$payment)
 // {
    // $this->excel->getActiveSheet()->setCellValue('I'.($i+2),to_currency_unVND_nomar($payment['payment_amount']));
    // $this->excel->getActiveSheet()->getStyle('I'.($i+2))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
 // }
//$this->excel->getActiveSheet()->getStyle('I'.($i+2))->getBorders()->getTOP()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//$this->excel->getActiveSheet()->getStyle('I'.($i+2))->getBorders()->getBOTTOM()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
/*$this->excel->getActiveSheet()->getStyle('H'.($i+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
///$this->excel->getActiveSheet()->getStyle('B'.($i+3))->getBorders()->getTOP()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->setActiveSheetIndex(0)->mergeCells('B'.($i+3).':G'.($i+3));
$this->excel->getActiveSheet()->getStyle('A'.($i+3))->getFont()->setSize(9);
$this->excel->getActiveSheet()->getStyle('A'.($i+3))->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('A'.($i+3).':H'.($i+3))->getBorders()->getTOP()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('A'.($i+3).':G'.($i+3))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('A'.($i+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$this->excel->getActiveSheet()->setCellValue('A'.($i+3),"Còn nợ lại : ");
//$this->excel->getActiveSheet()->getStyle('I'.($i+3))->getBorders()->getTOP()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('A'.($i+3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('H'.($i+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$this->excel->getActiveSheet()->getStyle('H'.($i+3))->getFont()->setSize(11);
if($payments_cost > 0){
    $this->excel->getActiveSheet()->setCellValue('H'.($i+3),to_currency_unVND_nomar($payments_cost));
    $this->excel->getActiveSheet()->getStyle('H'.($i+3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
}elseif($payments_cost == 0){
    $this->excel->getActiveSheet()->setCellValue('H'.($i+3),'0 VNĐ');
    $this->excel->getActiveSheet()->getStyle('H'.($i+3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
}*/


$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+4).':C'.($i+4));
$this->excel->setActiveSheetIndex(0)->mergeCells('D'.($i+4).':H'.($i+4));
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
$this->excel->getActiveSheet()->getStyle('A'.($i+4).':H'.($i+4))->getBorders()->getTOP()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('A'.($i+4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->load->model('Cost');
$tongtien = to_currency_unVND_nomar($data['total']/10);

$tong_tienno_chu = 0;
$tienno_chus = $this->Cost->get_tongtien_tra($sale_id);
foreach ($tienno_chus as $tienno_chu){
	$tong_tienno_chu += $tienno_chu['payment_amount'];
}
    //$this->excel->getActiveSheet()->setCellValue('D'.($i+4),$this->Cost->get_string_number($tong_tienno_chu));
	$this->excel->getActiveSheet()->setCellValue('D'.($i+4),$this->Cost->get_string_number($tongtien));
    $this->excel->getActiveSheet()->getStyle('D'.($i+4))->getFont()->setSize(10)->setBold(true)->setItalic(true);
	$this->excel->getActiveSheet()->getStyle('D'.($i+4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$this->excel->getActiveSheet()->getStyle('B'.($i+4))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+5).':H'.($i+5));
$this->excel->getActiveSheet()->setCellValue('A'.($i+5)," - Số tiền thanh toán lần 01 đã ứng trước: 0 VNĐ. ");
$this->excel->getActiveSheet()->getStyle('A'.($i+5))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A'.($i+5))->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+6).':H'.($i+6));
$this->excel->getActiveSheet()->setCellValue('A'.($i+6)," - Số tiền thanh toán trong vòng 04 ngày sau khi thanh lý:".to_currency_unVND_nomar($data['total']));
$this->excel->getActiveSheet()->getStyle('A'.($i+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A'.($i+6))->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+7).':H'.($i+7));
$this->excel->getActiveSheet()->setCellValue('A'.($i+7)," Điều 5: Điều khoản chung");
$this->excel->getActiveSheet()->getStyle('A'.($i+7))->getFont()->setSize(11)->setBold(true)->setItalic(true);
$this->excel->getActiveSheet()->getStyle('A'.($i+7))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A'.($i+7))->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+8).':H'.($i+8));
$this->excel->getActiveSheet()->setCellValue('A'.($i+8),"Hai bên thống nhất thanh lý hợp đồng kinh tế số: 241013/PN-HLV/HĐKT giữa CÔNG TY TNHH HONDA LOCK VIỆT NAM và CÔNG TY TNHH THIẾT BỊ VÀ GIẢI PHÁP CÔNG NGHỆ PHƯƠNG NAM.");
$this->excel->getActiveSheet()->getStyle('A'.($i+8))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A'.($i+8))->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+9).':H'.($i+9));
$this->excel->getActiveSheet()->setCellValue('A'.($i+9),"Biên bản được lập thành 02 bản, mỗi bên giữ 01 bản có giá trị như nhau và có hiệu lực kể từ ngày ký.");
$this->excel->getActiveSheet()->getStyle('A'.($i+9))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A'.($i+9))->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('A'.($i+11).':D'.($i+11));
$this->excel->getActiveSheet()->setCellValue('A'.($i+11),"               ĐẠI DIỆN BÊN A");
$this->excel->getActiveSheet()->getStyle('A'.($i+11))->getFont()->setSize(10)->setBold(true);
$this->excel->getActiveSheet()->getStyle('A'.($i+11))->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('E'.($i+11).':H'.($i+11));
$this->excel->getActiveSheet()->setCellValue('E'.($i+11),"     ĐẠI DIỆN BÊN B");
$this->excel->getActiveSheet()->getStyle('E'.($i+11))->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getStyle('E'.($i+11))->getFont()->setSize(10)->setBold(true);
$this->excel->getActiveSheet()->getStyle('A'.($i+11).':H'.($i+8))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

/* */
$this->excel->getActiveSheet()->getRowDimension(1)->setRowHeight(33.75);
$this->excel->getActiveSheet()->getRowDimension('2:5')->setRowHeight(15.75);
$this->excel->getActiveSheet()->getRowDimension(13)->setRowHeight(8.75);

$this->excel->getActiveSheet()->getRowDimension(6)->setRowHeight(33.75);
$this->excel->getActiveSheet()->getRowDimension(7)->setRowHeight(33.75);
$this->excel->getActiveSheet()->getRowDimension(9)->setRowHeight(18);
$this->excel->getActiveSheet()->getRowDimension(11)->setRowHeight(0);
$this->excel->getActiveSheet()->getRowDimension(12)->setRowHeight(16);
$this->excel->getActiveSheet()->getRowDimension(13)->setRowHeight(16);
$this->excel->getActiveSheet()->getRowDimension(14)->setRowHeight(16);
$this->excel->getActiveSheet()->getRowDimension(15)->setRowHeight(19);
$this->excel->getActiveSheet()->getRowDimension(16)->setRowHeight(18);
$this->excel->getActiveSheet()->getRowDimension(17)->setRowHeight(18);
$this->excel->getActiveSheet()->getRowDimension(18)->setRowHeight(18);
$this->excel->getActiveSheet()->getRowDimension(19)->setRowHeight(18);
$this->excel->getActiveSheet()->getRowDimension(20)->setRowHeight(18);
$this->excel->getActiveSheet()->getRowDimension(21)->setRowHeight(18);
$this->excel->getActiveSheet()->getRowDimension(22)->setRowHeight(18);
$this->excel->getActiveSheet()->getRowDimension(23)->setRowHeight(18);
$this->excel->getActiveSheet()->getRowDimension(24)->setRowHeight(19);
$this->excel->getActiveSheet()->getRowDimension(25)->setRowHeight(18);
$this->excel->getActiveSheet()->getRowDimension(26)->setRowHeight(18);
$this->excel->getActiveSheet()->getRowDimension(27)->setRowHeight(18);
$this->excel->getActiveSheet()->getRowDimension(28)->setRowHeight(18);
$this->excel->getActiveSheet()->getRowDimension(29)->setRowHeight(18);
$this->excel->getActiveSheet()->getRowDimension(30)->setRowHeight(18);
$this->excel->getActiveSheet()->getRowDimension(31)->setRowHeight(0);
$this->excel->getActiveSheet()->getRowDimension(32)->setRowHeight(0);
$this->excel->getActiveSheet()->getRowDimension(33)->setRowHeight(44.25);
$this->excel->getActiveSheet()->getRowDimension(34)->setRowHeight(22);
$this->excel->getActiveSheet()->getRowDimension(35)->setRowHeight(51);
$this->excel->getActiveSheet()->getRowDimension(36)->setRowHeight(22);
$this->excel->getActiveSheet()->getRowDimension(37)->setRowHeight(17);
$this->excel->getActiveSheet()->getRowDimension(40)->setRowHeight(0);
$this->excel->getActiveSheet()->getRowDimension(41)->setRowHeight(17);
$this->excel->getActiveSheet()->getRowDimension(42)->setRowHeight(33);
$this->excel->getActiveSheet()->getRowDimension(43)->setRowHeight(33);
$this->excel->getActiveSheet()->getRowDimension(44)->setRowHeight(48.75);
$this->excel->getActiveSheet()->getRowDimension(45)->setRowHeight(65);
$this->excel->getActiveSheet()->getRowDimension(46)->setRowHeight(0);
$this->excel->getActiveSheet()->getRowDimension(47)->setRowHeight(22);
$this->excel->getActiveSheet()->getRowDimension(48)->setRowHeight(19.5);
$this->excel->getActiveSheet()->getRowDimension(49)->setRowHeight(0);
$this->excel->getActiveSheet()->getRowDimension(50)->setRowHeight(0);
$this->excel->getActiveSheet()->getRowDimension(51)->setRowHeight(0);
$this->excel->getActiveSheet()->getRowDimension(54)->setRowHeight(0);
$this->excel->getActiveSheet()->getRowDimension(52)->setRowHeight(0);
$this->excel->getActiveSheet()->getRowDimension(53)->setRowHeight(0);
$this->excel->getActiveSheet()->getRowDimension(55)->setRowHeight(0);
$this->excel->getActiveSheet()->getRowDimension(58)->setRowHeight(0);
$this->excel->getActiveSheet()->getRowDimension(59)->setRowHeight(0);
$this->excel->getActiveSheet()->getRowDimension(60)->setRowHeight(0);
$this->excel->getActiveSheet()->getRowDimension(61)->setRowHeight(0);
$this->excel->getActiveSheet()->getRowDimension(62)->setRowHeight(0);
$this->excel->getActiveSheet()->getRowDimension(56)->setRowHeight(0);
$this->excel->getActiveSheet()->getRowDimension(57)->setRowHeight(0);
$this->excel->getActiveSheet()->getRowDimension(63)->setRowHeight(0);
$this->excel->getActiveSheet()->getRowDimension(64)->setRowHeight(0);
$this->excel->getActiveSheet()->getRowDimension(65)->setRowHeight(0);
$this->excel->getActiveSheet()->getRowDimension(66)->setRowHeight(0);
$this->excel->getActiveSheet()->getRowDimension(67)->setRowHeight(0);
$this->excel->getActiveSheet()->getRowDimension(68)->setRowHeight(0);
$this->excel->getActiveSheet()->getRowDimension(69)->setRowHeight(0);
$this->excel->getActiveSheet()->getRowDimension(70)->setRowHeight(0);
$this->excel->getActiveSheet()->getRowDimension(71)->setRowHeight(0);
$this->excel->getActiveSheet()->getRowDimension(72)->setRowHeight(0);
$this->excel->getActiveSheet()->getRowDimension(73)->setRowHeight(0);
$this->excel->getActiveSheet()->getRowDimension(74)->setRowHeight(0);
$this->excel->getActiveSheet()->getRowDimension(75)->setRowHeight(0);
$this->excel->getActiveSheet()->getRowDimension(76)->setRowHeight(0);
$this->excel->getActiveSheet()->getRowDimension(77)->setRowHeight(0);
$this->excel->getActiveSheet()->getRowDimension(78)->setRowHeight(0);

$this->excel->getActiveSheet()->getRowDimension($i)->setRowHeight(14.75);
$this->excel->getActiveSheet()->getRowDimension($i+1)->setRowHeight(14.75);
$this->excel->getActiveSheet()->getRowDimension($i+2)->setRowHeight(14.75);
$this->excel->getActiveSheet()->getRowDimension($i+3)->setRowHeight(14.75);
$this->excel->getActiveSheet()->getRowDimension($i+4)->setRowHeight(27.75);
$this->excel->getActiveSheet()->getRowDimension($i+5)->setRowHeight(18);
$this->excel->getActiveSheet()->getRowDimension($i+6)->setRowHeight(12.75);
$this->excel->getActiveSheet()->getRowDimension($i+8)->setRowHeight(46.5);
$this->excel->getActiveSheet()->getRowDimension($i+9)->setRowHeight(16.5);

$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(14.48);
$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(10.25);
$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(10.08);
$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(10.08);
$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(9);
$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(9.08);
$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(13);

//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
//if you want to save it as .XLSX Excel 2007 format
$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $md5file = md5(date('YmdHis')) . '.xlsx';
//$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
//force user to download the Excel file without writing it to server's HD
$filename = 'thanhlyhopdong.xlsx'; //save our workbook as this file name
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