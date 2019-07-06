
<?php

//echo "hfjdahdj";die;
//print_r($rows);die;
$count = count($report_data['summary'])+count($report_data['details'])+count($headers)*(count($report_data['summary'])+count($report_data['details']));
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
$this->excel->getActiveSheet()->setCellValue('E1', "BÁO CÁO CHI TIẾT CÁC ĐƠN HÀNG");

$row =2;
$col =0;

	foreach($headers['summary'] as $key1 => $rows)
	{
	$this->excel->getActiveSheet()->setCellValueByColumnAndRow($col,$row,$rows["data"]);
	$col++;	
	}
$k =3;


foreach ($report_data['summary'] as $key=>$r) {
	//echo $r['sale_id'];die;
	$name_supplier = $this->Supplier->get_name_suppiler(array('person_id'=>$r[3]));
	//$name_supplier);die;
	  $this->excel->getActiveSheet()->setCellValue('A' . $k, $r['sale_id']);
	  $this->excel->getActiveSheet()->setCellValue('B' . $k, $r['sale_date']);
	  $this->excel->getActiveSheet()->setCellValue('C' . $k, $r['items_purchased']);
	  $this->excel->getActiveSheet()->setCellValue('D' . $k, $r['customer_name']);
	  $this->excel->getActiveSheet()->setCellValue('E' . $k, $r['subtotal']);
	  $this->excel->getActiveSheet()->setCellValue('F' . $k, $r['total']);
	  $this->excel->getActiveSheet()->setCellValue('G' . $k, $r['tax']);
	  $this->excel->getActiveSheet()->setCellValue('H' . $k, $r['profit']);
	  $this->excel->getActiveSheet()->setCellValue('I' . $k, $r['payment_type']);
		$this->excel->getActiveSheet()->setCellValue('J' . $k, $r['comment']);
		//header[details]
		$j = $k+1;
		$column=0;
	 foreach($headers['details'] as $key2 => $rowdata)
	{
	$this->excel->getActiveSheet()->setCellValueByColumnAndRow($column,$j,$rowdata["data"]);
	$column++;	
	}
	$g =$j+1;
	$column2=0;
	//print_r($details_data[0]);die;
	foreach($details_data[$key] as $datarow2)
	{
		foreach($datarow2 as $cell)
			{
				$this->excel->getActiveSheet()->setCellValueByColumnAndRow($column2,$g,$cell["data"]);
	$column2++;
			}
	}
	 
	  $k=$g+1;
 }
$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(20);

$styleThinBlackBorderOutline = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
        ),
    ),
);

$this->excel->getActiveSheet()->getStyle('A2:J' . ($count+5))->applyFromArray($styleThinBlackBorderOutline);
$this->excel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L&BInvoice&RPrinted on &D');
$this->excel->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&B' . $this->excel->getProperties()->getTitle() . '&RPage &P of &N');

//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
//if you want to save it as .XLSX Excel 2007 format
$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $md5file = md5(date('YmdHis')) . '.xlsx';
//$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
//force user to download the Excel file without writing it to server's HD
$filename = 'thongtin_banhang.xlsx'; //save our workbook as this file name
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