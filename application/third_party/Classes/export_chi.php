<?php
//echo "hfjdahdj";die;
//print_r($rows);die;
$count = count($rows);
//echo $count;die;
$this->load->library('Excel');
$objPHPExcel = new PHPExcel();

$this->excel->getDefaultStyle()
    ->getAlignment()
    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->setShowGridlines(true);
$this->excel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Times New Roman');
$this->excel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(9);
$this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
$this->excel->getActiveSheet()->getPageSetup()->setHorizontalCentered(false);
$this->excel->getActiveSheet()->getPageSetup()->setVerticalCentered(false);
$this->excel->getActiveSheet()->getPageMargins()->setTop(0.3);
$this->excel->getActiveSheet()->getPageMargins()->setLEFT(0.4);
$this->excel->getActiveSheet()->getPageMargins()->setRIGHT(0.2);
$this->excel->getActiveSheet()->getPageMargins()->setFOOTER(0.1);
//activate worksheet number 1
$this->excel->setActiveSheetIndex(0);
//error_reporting(E_ALL);
error_reporting(E_ALL & ~E_NOTICE);
//name the worksheet
$this->excel->getActiveSheet()->setTitle('Báo cáo tiền chi 1 khách hàng');
/* ten dia chi cong ty ngay thang noi dung */
$this->excel->setActiveSheetIndex(0)->mergeCells('A1:E1');
$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(8);
$this->excel->getActiveSheet()->setCellValue('A1',$this->config->item('company'));
$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->setActiveSheetIndex(0)->mergeCells('A2:D2');
$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setItalic(true);
$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(8);
$this->excel->getActiveSheet()->setCellValue('A2',"11P12 - Ngõ 103 Nguyễn An Ninh - Hoàng Mai - Hà Nội");

$this->excel->setActiveSheetIndex(0)->mergeCells('F1:I1');
$this->excel->getActiveSheet()->getStyle('F1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('F1')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('F1')->getFont()->setSize(10);
$this->excel->getActiveSheet()->setCellValue('F1',"Mẫu số 02 - TT");
$this->excel->setActiveSheetIndex(0)->mergeCells('F2:I2');
$this->excel->getActiveSheet()->getStyle('F2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('F2')->getFont()->setSize(8);
$this->excel->getActiveSheet()->setCellValue('F2',"Ban hành theo QĐ số 48/2006/QĐ - BTC");
$this->excel->setActiveSheetIndex(0)->mergeCells('F3:I3');
$this->excel->getActiveSheet()->getStyle('F3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('F3')->getFont()->setSize(8);
$this->excel->getActiveSheet()->setCellValue('F3',"Ngày 14 tháng 9 năm 2006");
$this->excel->getActiveSheet()->setCellValue('H4',"Liên: ");
$this->excel->getActiveSheet()->getStyle('H4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$this->excel->getActiveSheet()->setCellValue('I4',"1");
$this->excel->getActiveSheet()->getStyle('H4:I4')->getFont()->setSize(12);

	$this->excel->setActiveSheetIndex(0)->mergeCells('A6:I6');
	$this->excel->getActiveSheet()->setCellValue('A6', "PHIẾU CHI");
	$this->excel->getActiveSheet()->getStyle('A6')->getFont()->setSize(14)->setBold(true);
	$this->excel->getActiveSheet()->getRowDimension(6)->setRowHeight(24.75);
	$this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->setActiveSheetIndex(0)->mergeCells('D7:F7');	
$this->excel->getActiveSheet()->setCellValue('D7', "Ngày ".date('d')." tháng ".date('m')." năm ".date('Y'));
$this->excel->getActiveSheet()->getStyle('D7')->getFont()->setSize(12)->setItalic(true);
$this->excel->getActiveSheet()->getRowDimension(7)->setRowHeight(13.50);
$this->excel->setActiveSheetIndex(0)->mergeCells('G7:I7');
$this->excel->getActiveSheet()->setCellValue('G7', "Số: ".$cost->id_cost);
$this->excel->getActiveSheet()->getStyle('G7')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A7:I7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A7:I7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->getActiveSheet()->setCellValue('G8', "Nợ: 6422");
$this->excel->getActiveSheet()->setCellValue('G9', "Có: 1111");
$this->excel->getActiveSheet()->getStyle('G8:G9')->getFont()->setSize(12);

$this->excel->setActiveSheetIndex(0)->mergeCells('C10:I10');
$this->excel->getActiveSheet()->setCellValue('A10', "Họ tên người nộp tiền: ");
$this->excel->getActiveSheet()->setCellValue('C10', $this->Person->get_info($cost->id_customer)->first_name.' '.$this->Person->get_info($cost->id_customer)->first_name->last_name);
$this->excel->getActiveSheet()->getStyle('C10')->getFont()->setBold(true);
$this->excel->getActiveSheet()->setCellValue('A11', "Địa chỉ: ");
$this->excel->setActiveSheetIndex(0)->mergeCells('B11:I11');
$this->excel->getActiveSheet()->setCellValue('B11', $this->Person->get_info($cost->id_customer)->address_1);
$this->excel->getActiveSheet()->setCellValue('A12', "Lý do nộp: ");
$this->excel->setActiveSheetIndex(0)->mergeCells('B12:I12');
$this->excel->getActiveSheet()->setCellValue('B12', $cost->comment);
$this->excel->getActiveSheet()->setCellValue('A13', "Số tiền: ");
$this->excel->getActiveSheet()->setCellValue('B13', to_currency_unVND_nomar($cost->tien_chi));
$this->excel->getActiveSheet()->getStyle('B13')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$this->excel->getActiveSheet()->setCellValue('C13', " (đồng)");
$this->excel->setActiveSheetIndex(0)->mergeCells('A14:I14');
$this->excel->getActiveSheet()->setCellValue('A14', "(Bằng chữ: ".$this->Cost->get_string_number($cost->tien_chi)."./.)");
$this->excel->setActiveSheetIndex(0)->mergeCells('A15:I15');
$this->excel->getActiveSheet()->setCellValue('A15', "Kèm theo: chứng từ gốc");
$this->excel->getActiveSheet()->getRowDimension(8)->setRowHeight(13.50);
$this->excel->getActiveSheet()->getRowDimension(9)->setRowHeight(12.75);
$this->excel->getActiveSheet()->getRowDimension(10)->setRowHeight(17.25);
$this->excel->getActiveSheet()->getRowDimension(11)->setRowHeight(17.25);
$this->excel->getActiveSheet()->getRowDimension(12)->setRowHeight(17.25);
$this->excel->getActiveSheet()->getRowDimension(13)->setRowHeight(17.25);
$this->excel->getActiveSheet()->getRowDimension(14)->setRowHeight(17.25);
$this->excel->getActiveSheet()->getRowDimension(15)->setRowHeight(17.25);
$this->excel->getActiveSheet()->getStyle('A10:I15')->getFont()->setSize(12);

$this->excel->setActiveSheetIndex(0)->mergeCells('G16:I16');
$this->excel->getActiveSheet()->setCellValue('G16', "Ngày.....tháng.....năm....... ");
$this->excel->getActiveSheet()->getStyle('G16')->getFont()->setSize(12)->setItalic(true);
$this->excel->getActiveSheet()->getStyle('G16')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('G16')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->getActiveSheet()->setCellValue('A17', "          Giám đốc          Kế toán trưởng          Thủ quỹ          Người lập biểu          Người thu tiền");
$this->excel->getActiveSheet()->getStyle('A17:I17')->getFont()->setSize(12)->setBold(true);
$this->excel->getActiveSheet()->setCellValue('A18',"(Ký, họ tên, đóng dấu)      (Ký, họ tên)          (Ký, họ tên)         (Ký, họ tên)               (Ký, họ tên)");
$this->excel->getActiveSheet()->getStyle('A18')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getRowDimension(19)->setRowHeight(17.25);
$this->excel->getActiveSheet()->getRowDimension(20)->setRowHeight(18);
$this->excel->getActiveSheet()->getRowDimension(21)->setRowHeight(21);

$this->excel->setActiveSheetIndex(0)->mergeCells('A22:I22');                                                                                                                     
$this->excel->getActiveSheet()->setCellValue('A22', " Đã nhận đủ số tiền (Viết bằng chữ ):..................................................................................................");
$this->excel->setActiveSheetIndex(0)->mergeCells('A23:I23');
$this->excel->getActiveSheet()->setCellValue('A23', "    +Tỷ giá ngoại tệ (Vàng, bạc, đá quý ):...........................................................................................");
$this->excel->setActiveSheetIndex(0)->mergeCells('A24:I24');
$this->excel->getActiveSheet()->setCellValue('A24', "    +Số tiền quy đổi:.............................................................................................................................");
$this->excel->setActiveSheetIndex(0)->mergeCells('A25:I25');
$this->excel->getActiveSheet()->setCellValue('A25', "    +Liên gửi ra ngoài phải đóng dấu:.................................................................................................");
$this->excel->getActiveSheet()->getStyle('A22:I25')->getFont()->setSize(11)->setItalic(true);
/* ten dia chi cong ty ngay thang noi dung */

$this->excel->getActiveSheet()->getPageMargins()->setRight(0.1);
$this->excel->getActiveSheet()->getPageMargins()->setLeft(0.15);
$this->excel->getActiveSheet()->getPageMargins()->setTop(0.15);
$this->excel->getActiveSheet()->getPageMargins()->setBOTTOM(0.2);
$this->excel->getActiveSheet()->getPageMargins()->setHEADER(0.1);
$this->excel->getActiveSheet()->getPageMargins()->setFOOTER(0.1);

$this->excel->getActiveSheet()->getHeaderFooter()->setOddFooter('&RPage &P of &N');
$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(11.5);
$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(19);
$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(7.5);
$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(14.17);
$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(12.14);
$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(13.83);
$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(9);
$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(8.5);
$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(17.67);
	//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
//if you want to save it as .XLSX Excel 2007 format
$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $md5file = md5(date('YmdHis')) . '.xlsx';
//$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
//force user to download the Excel file without writing it to server's HD
$filename = 'thongtin_chitien_kh.xlsx'; //save our workbook as this file name
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
    exit;
}
?>