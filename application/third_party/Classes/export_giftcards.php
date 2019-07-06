<?php
$count = count($data);
$this->load->library('Excel');
$objPHPExcel = new PHPExcel();

$this->excel->getActiveSheet()->setShowGridlines(false);
//activate worksheet number 1
$this->excel->setActiveSheetIndex(0);
//error_reporting(E_ALL);
error_reporting(E_ALL & ~E_NOTICE);
//name the worksheet
$this->excel->getActiveSheet()->setTitle('test worksheet');
$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->setCellValue('A1', "dANH SÁCH THẺ QUÀ TẶNG");
$row =2;
$col =0;
$this->excel->getActiveSheet()->setCellValue('A2', "Mã số thẻ quà tặng");
$this->excel->getActiveSheet()->setCellValue('B2', "Giá trị");
/*foreach($header as $rows)
{
$this->excel->getActiveSheet()->setCellValueByColumnAndRow($col,$row,$rows[$col]);
$col++;	
}
/*$this->excel->getActiveSheet()->setCellValue('A2', "Tên khách hàng");
$this->excel->getActiveSheet()->setCellValue('B2', "Họ");
$this->excel->getActiveSheet()->setCellValue('C2', "E-Mail");
$this->excel->getActiveSheet()->setCellValue('D2', "Số điện thoại nhà");
$this->excel->getActiveSheet()->setCellValue('E2', "Địa chi 1");
$this->excel->getActiveSheet()->setCellValue('F2', "Địa chi 2");
$this->excel->getActiveSheet()->setCellValue('G2', "Thành phố");
$this->excel->getActiveSheet()->setCellValue('H2', "Tỉnh");
$this->excel->getActiveSheet()->setCellValue('I2', "Mã vùng");
$this->excel->getActiveSheet()->setCellValue('J2', "Quốc gia");
$this->excel->getActiveSheet()->setCellValue('K2', "Ghi chú");
$this->excel->getActiveSheet()->setCellValue('L2', "Tài khoản #");
$this->excel->getActiveSheet()->setCellValue('M2', "Chịu thuế");
$this->excel->getActiveSheet()->setCellValue('N2', "Tên công ty");
*/
$k =3;
 foreach ($data as $r) {
	  $this->excel->getActiveSheet()->setCellValue('A' . $k, $r->giftcard_number);
	  $this->excel->getActiveSheet()->setCellValue('B' . $k, $r->value);
	 
	  $k++;
 }
$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);

$styleThinBlackBorderOutline = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
        ),
    ),
);

$this->excel->getActiveSheet()->getStyle('A2:B' . ($count+5))->applyFromArray($styleThinBlackBorderOutline);
$this->excel->getActiveSheet()->getHeaderFooter()->setOddFooter('&RPage &P of &N');

$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
//if you want to save it as .XLSX Excel 2007 format
$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $md5file = md5(date('YmdHis')) . '.xlsx';
//$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
//force user to download the Excel file without writing it to server's HD
$filename = 'DANH SÁCH THẺ QUÀ TẶNG.xlsx'; //save our workbook as this file name
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