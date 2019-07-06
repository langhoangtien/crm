<?php
//echo "hfjdahdj";die;
//print_r($rows);die;
$count = count($rows);
//echo $count;die;
$this->load->library('Excel');

$objPHPExcel = new PHPExcel();
/* */
// $objDrawing = new PHPExcel_Worksheet_Drawing();
// $objDrawing->setName('PHPExcel logo');
// $objDrawing->setDescription('PHPExcel logo');
// $objDrawing->setPath($this->Appconfig->get_logo_image());
// $objDrawing->setCoordinates('A30'); -->want to insert image in C33
// $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
/* */
$this->excel->getDefaultStyle()
    ->getAlignment()
    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->setShowGridlines(true);
$this->excel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Times New Roman');
$this->excel->setActiveSheetIndex(0);
$this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
$this->excel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
$this->excel->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
$this->excel->getActiveSheet()->getPageSetup()->setVerticalCentered(true);
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

// $sheet = $this->excel->getActiveSheet();
// $objDrawing = new PHPExcel_Worksheet_Drawing();
// $objDrawing->setPath('images/viethung.png');
// $objDrawing->setOffsetX(50);
// $objDrawing->setOffsetY(50);
// $objDrawing->setCoordinates('B1');
// $objDrawing->setWorksheet($sheet);

$this->excel->setActiveSheetIndex(0)->mergeCells('B2:C2');
$this->excel->getActiveSheet()->setCellValue('B2',$this->config->item('company'));
$this->excel->getActiveSheet()->getStyle('B2')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle("B2")->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B2')->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getRowDimension(2)->setRowHeight(65);
		
$this->excel->setActiveSheetIndex(0)->mergeCells('D2:I2');
$this->excel->getActiveSheet()->setCellValue('D2'," CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM \n  Độc lập – Tự do – Hạnh phúc \r\n  ---o0o--- ");
$this->excel->getActiveSheet()->getStyle('D2')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle("D2")->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('D2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('D2')->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getStyle('D2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

$this->excel->setActiveSheetIndex(0)->mergeCells('B3'.':D3');
$this->excel->getActiveSheet()->setCellValue('B3',"Số: 2013-57/HLV-PN ");
$this->excel->getActiveSheet()->getStyle("B3")->getFont()->setSize(10);
$this->excel->getActiveSheet()->getStyle('B3')->getFont()->setItalic(true);
$this->excel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->setActiveSheetIndex(0)->mergeCells('B4'.':D4');
$this->excel->getActiveSheet()->setCellValue('B4',"V/v: Đề nghị thanh toán");
$this->excel->getActiveSheet()->getStyle("B4")->getFont()->setSize(10);
$this->excel->getActiveSheet()->getStyle('B4')->getFont()->setItalic(true);
$this->excel->getActiveSheet()->getStyle('B4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$ngay = date('d');
$thang = date('m');
$nam = date('Y');
$this->excel->setActiveSheetIndex(0)->mergeCells('E4'.':I4');
$this->excel->getActiveSheet()->setCellValue('E4',"Hà nội, ngày ".$ngay." tháng ".$thang." năm".$nam);
$this->excel->getActiveSheet()->getStyle("E4")->getFont()->setSize(10);
$this->excel->getActiveSheet()->getStyle('E4')->getFont()->setItalic(true);
$this->excel->getActiveSheet()->getStyle('E4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->setActiveSheetIndex(0)->mergeCells('B7:I7');
$this->excel->getActiveSheet()->getStyle('B7')->getFont()->setSize(12)->setBold(true);
$this->excel->getActiveSheet()->setCellValue('B7',"Kính gửi: (Ông/Bà)     ".mb_convert_case($cust_info->company_name,MB_CASE_UPPER, "UTF-8" ));
$this->excel->getActiveSheet()->getStyle('B7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('B7')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('C8:I8');
$this->excel->getActiveSheet()->getStyle('C8')->getFont()->setSize(11)->setItalic(true);
$this->excel->getActiveSheet()->setCellValue('C8',"Địa chỉ: ".$data['address']);
$this->excel->getActiveSheet()->getStyle('C8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('C8')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$this->excel->getActiveSheet()->getStyle('C8')->getAlignment()->setWrapText(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('B9:I9');
$this->excel->getActiveSheet()->getStyle('B9')->getFont()->setSize(11);
$this->excel->getActiveSheet()->setCellValue('B9',"- Căn cứ vào hợp đồng số: 241013/PN-HLV/HĐKT ngày  ".date(get_date_format(),strtotime($suspended_sale['sale_time']))."  giữa ".mb_convert_case($cust_info->company_name,MB_CASE_UPPER, "UTF-8" )."  và CÔNG TY TNHH THIẾT BỊ VÀ GIẢI PHÁP CÔNG NGHỆ PHƯƠNG NAM  về việc cung cấp, lắp đặt hệ thống camera cho Quý Công ty.");
$this->excel->getActiveSheet()->getStyle('B9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B9')->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getRowDimension(9)->setRowHeight(48);

$this->excel->setActiveSheetIndex(0)->mergeCells('B10:I10');
$this->excel->getActiveSheet()->getStyle('B10')->getFont()->setSize(11);
$this->excel->getActiveSheet()->setCellValue('B10',"- Căn cứ vào biên bản thanh lý hợp đồng số: 241013/PNS-HLV/HĐKT ngày ".$ngay." tháng ".$thang." năm".$nam.".");
$this->excel->getActiveSheet()->getStyle('B10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B10')->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getRowDimension(10)->setRowHeight(18.75);

$this->load->model('Cost');
$tongtien = to_currency_unVND_nomar($data['total']/10);
//$tongtien = 12000;
$tong_tienno_chu = 0;
$tienno_chus = $this->Cost->get_tongtien_tra($sale_id);
foreach ($tienno_chus as $tienno_chu){
	$tong_tienno_chu += $tienno_chu['payment_amount'];
	}
$this->excel->setActiveSheetIndex(0)->mergeCells('B11:I11');
$this->excel->getActiveSheet()->getStyle('B11')->getFont()->setSize(11);
$this->excel->getActiveSheet()->setCellValue('B11',"Công ty TNHH Thiết bị và Giải pháp Công nghệ Phương Nam đề nghị Quý Công ty thanh toán số tiền: ");
$this->excel->getActiveSheet()->getStyle('B11')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B11')->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getRowDimension(11)->setRowHeight(21);
//$this->Cost->get_string_number
$this->excel->setActiveSheetIndex(0)->mergeCells('B12:I12');
$this->excel->getActiveSheet()->getStyle('B12')->getFont()->setSize(12)->setBold(true);
$this->excel->getActiveSheet()->setCellValue('B12',$data['total']." VNĐ  (Bằng chữ: ".$this->Cost->get_string_number($data['total'])." ./.) ");
$this->excel->getActiveSheet()->getStyle('B12')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B12')->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getRowDimension(12)->setRowHeight(23);

$this->excel->setActiveSheetIndex(0)->mergeCells('B13:I13');
$this->excel->getActiveSheet()->getStyle('B13')->getFont()->setSize(11);
$this->excel->getActiveSheet()->setCellValue('B13',"bằng tiền mặt hoặc chuyển khoản vào tài khoản: Công ty TNHH Thiết bị và giải pháp công nghệ Phương Nam; số: 102.01.000.157.9242; tại ngân hàng TMCP Công thương Việt Nam chi nhánh Nam Thăng Long.");
$this->excel->getActiveSheet()->getStyle('B13')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B13')->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getRowDimension(13)->setRowHeight(36);

$this->excel->setActiveSheetIndex(0)->mergeCells('B14:I14');
$this->excel->getActiveSheet()->getStyle('B14')->getFont()->setSize(11);
$this->excel->getActiveSheet()->setCellValue('B14',"Công ty Chúng tôi rất mong nhận được sự hỗ trợ, hợp tác của Quý công ty để vệc cung cấp này được diễn ra tốt đẹp.");
$this->excel->getActiveSheet()->getStyle('B14')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B14')->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getRowDimension(14)->setRowHeight(33.25);

$this->excel->setActiveSheetIndex(0)->mergeCells('B15:I15');
$this->excel->getActiveSheet()->getStyle('B15')->getFont()->setSize(11)->setBold(true)->setItalic(true);
$this->excel->getActiveSheet()->setCellValue('B15',"Chúng tôi xin chân thành cảm ơn!");
$this->excel->getActiveSheet()->getStyle('B15')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B15')->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getRowDimension(15)->setRowHeight(21);

$this->excel->setActiveSheetIndex(0)->mergeCells('B16:I16');
$this->excel->getActiveSheet()->getStyle('B16')->getFont()->setSize(11)->setBold(true)->setItalic(true);
$this->excel->getActiveSheet()->setCellValue('B16',"Kính chúc Quý khách hàng sức khỏe – thành công – hạnh phúc!");
$this->excel->getActiveSheet()->getStyle('B16')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B16')->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getRowDimension(16)->setRowHeight(21);

$this->excel->setActiveSheetIndex(0)->mergeCells('B18:C18');
$this->excel->getActiveSheet()->getStyle('B18')->getFont()->setSize(11)->setItalic(true);
$this->excel->getActiveSheet()->setCellValue('B18',"  Nơi nhận:");
$this->excel->getActiveSheet()->getStyle('B18')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B18')->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getRowDimension(18)->setRowHeight(15);

$this->excel->setActiveSheetIndex(0)->mergeCells('B19:C19');
$this->excel->getActiveSheet()->getStyle('B19')->getFont()->setSize(11)->setItalic(true);
$this->excel->getActiveSheet()->setCellValue('B19',"  - Như trên;");
$this->excel->getActiveSheet()->getStyle('B19')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B19')->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getRowDimension(19)->setRowHeight(15);

$this->excel->setActiveSheetIndex(0)->mergeCells('B20:C20');
$this->excel->getActiveSheet()->getStyle('B20')->getFont()->setSize(11)->setItalic(true);
$this->excel->getActiveSheet()->setCellValue('B20',"  - Lưu văn phòng Công ty;");
$this->excel->getActiveSheet()->getStyle('B20')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B20')->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getRowDimension(20)->setRowHeight(15);

$this->excel->setActiveSheetIndex(0)->mergeCells('D20:I20');
$this->excel->getActiveSheet()->getStyle('D20')->getFont()->setSize(11)->setBold(true);
$this->excel->getActiveSheet()->setCellValue('D20',"                ĐẠI DIỆN CÔNG TY");
$this->excel->getActiveSheet()->getStyle('D20')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('D20')->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getRowDimension(20)->setRowHeight(15);



$styleArray = array(
    'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => 'FF0000'),
        'size'  => 9,
		'italic'=>true,
    ));
$this->excel->getActiveSheet()->getStyle('B49')->applyFromArray($styleArray);
 /* end phan loi */
/* */
 $styleThinBlackBorderOutline = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
        ),
    ),
); 



/* */
$this->excel->getActiveSheet()->getRowDimension('1:5')->setRowHeight(15.75);


$this->excel->getActiveSheet()->getRowDimension(6)->setRowHeight(0);
$this->excel->getActiveSheet()->getRowDimension(7)->setRowHeight(20);

// $this->excel->getActiveSheet()->getRowDimension($i)->setRowHeight(20.75);
// $this->excel->getActiveSheet()->getRowDimension($i+3)->setRowHeight(24);
// $this->excel->getActiveSheet()->getRowDimension($i+4)->setRowHeight(24);

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
$filename = 'thanhtoan.xlsx'; //save our workbook as this file name
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