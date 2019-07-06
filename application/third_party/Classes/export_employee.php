
<?php

//echo "hfjdahdj";die;
//print_r($rows);die;
$count = count($rows);
//echo $count;die;
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
$this->excel->getActiveSheet()->setCellValue('D1', "DANH SÁCH NHÂN VIÊN");

$this->excel->getActiveSheet()->setCellValue('A2', "Tên riêng");
$this->excel->getActiveSheet()->setCellValue('B2', "Tên họ");
$this->excel->getActiveSheet()->setCellValue('C2', "E-mail");
$this->excel->getActiveSheet()->setCellValue('D2', "Số điện thoại");
$this->excel->getActiveSheet()->setCellValue('E2', "Địa chỉ 1");
$this->excel->getActiveSheet()->setCellValue('F2', "Địa chỉ 2");
$this->excel->getActiveSheet()->setCellValue('G2', "Thành phố");
$this->excel->getActiveSheet()->setCellValue('H2', "Tỉnh");
$this->excel->getActiveSheet()->setCellValue('I2', "Mã vùng");
$this->excel->getActiveSheet()->setCellValue('J2', "Quốc gia");
$this->excel->getActiveSheet()->setCellValue('K2', "Ghi chú");
$k =3;
foreach ($rows as $key=>$r) {
	$name_supplier = $this->Supplier->get_name_suppiler(array('person_id'=>$r[3]));
	//$name_supplier);die;
	  $this->excel->getActiveSheet()->setCellValue('A' . $k, $r[0]);
	  $this->excel->getActiveSheet()->setCellValue('B' . $k, $r[1]);
	  $this->excel->getActiveSheet()->setCellValue('C' . $k, $r[2]);
	  $this->excel->getActiveSheet()->setCellValue('D' . $k, $name_supplier[0]['company_name']);
	  $this->excel->getActiveSheet()->setCellValue('E' . $k, $r[4]);
	  $this->excel->getActiveSheet()->setCellValue('F' . $k, $r[5]);
	  $this->excel->getActiveSheet()->setCellValue('G' . $k, $r[6]);
	  $this->excel->getActiveSheet()->setCellValue('H' . $k, $r[7]);
	  $this->excel->getActiveSheet()->setCellValue('I' . $k, $r[8]);
	  $this->excel->getActiveSheet()->setCellValue('J' . $k, $r[9]);
	  $this->excel->getActiveSheet()->setCellValue('K' . $k, $r[10]);
	 
	 
	  $k++;
 }


$styleThinBlackBorderOutline = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
        ),
    ),
);

$this->excel->getActiveSheet()->getStyle('A2:K' . ($count+5))->applyFromArray($styleThinBlackBorderOutline);
$this->excel->getActiveSheet()->getHeaderFooter()->setOddFooter('&RPage &P of &N');

$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('C2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('D2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('E2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('F2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('G2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('H2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('I2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('J2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('K2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
//if you want to save it as .XLSX Excel 2007 format
$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $md5file = md5(date('YmdHis')) . '.xlsx';
//$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
//force user to download the Excel file without writing it to server's HD
$filename = 'thongtin_nhanvien.xlsx'; //save our workbook as this file name
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