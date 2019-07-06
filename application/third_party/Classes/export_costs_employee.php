<?php
$count = count($rows);
$this->load->library('Excel');
$objPHPExcel = new PHPExcel();
$this->excel->getDefaultStyle()
        ->getAlignment()
        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->setShowGridlines(true);
$this->excel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Times New Roman');
$this->excel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(9);
$this->excel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
$this->excel->getActiveSheet()->getPageMargins()->setRight(0.25);
$this->excel->getActiveSheet()->getPageMargins()->setLeft(0.25);
$this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$this->excel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTop(array(7, 7));
//activate worksheet number 1
$this->excel->setActiveSheetIndex(0);
//error_reporting(E_ALL);
error_reporting(E_ALL & ~E_NOTICE);
//name the worksheet
$this->excel->getActiveSheet()->setTitle('Báo cáo thu chi nhân viên');
$this->excel->setActiveSheetIndex(0)->mergeCells('A1:E1');
$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
$this->excel->getActiveSheet()->setCellValue('A1', $company);
$this->excel->getActiveSheet()->setCellValue('A2', $this->config->item('address'));
$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
//$this->excel->getActiveSheet()->setCellValue('A3',$data['full_name']);
$this->excel->setActiveSheetIndex(0)->mergeCells('B4:G4');
$this->excel->getActiveSheet()->setCellValue('B4', "BÁO CÁO THU CHI NHÂN VIÊN");
$this->excel->getActiveSheet()->getStyle('B4')->getFont()->setSize(15)->setBold(true);
$this->excel->getActiveSheet()->getStyle('B4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->getActiveSheet()->setCellValue('C5', 'Từ ' . date('d-m-Y H:i:s', strtotime($start_date)) . ' đến ' . date('d-m-Y H:i:s', strtotime($end_date)));
$this->excel->setActiveSheetIndex(0)->mergeCells('C5:F5');
$this->excel->getActiveSheet()->getStyle('C5')->getFont()->setSize(10)->setItalic(true);
$this->excel->getActiveSheet()->getStyle('C5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('C5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);



$this->excel->setActiveSheetIndex(0)->mergeCells('A6:H6');
$this->excel->getActiveSheet()->getStyle('A6')->getFont()->setSize(10)->setBold(true);
$this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->getActiveSheet()->setCellValue('A7', "STT");
$this->excel->getActiveSheet()->setCellValue('B7', "NGÀY THU CHI ");
$this->excel->getActiveSheet()->setCellValue('C7', "");
$this->excel->getActiveSheet()->setCellValue('D7', "NHÂN VIÊN THỰC HIÊN");
$this->excel->getActiveSheet()->setCellValue('E7', "NỘI DUNG");
$this->excel->getActiveSheet()->setCellValue('F7', "ĐỐI TƯỢNG NHẬN THU CHI");
if($cost_type == 'thu' || $cost_type == 'all'){
$this->excel->getActiveSheet()->setCellValue('G7', "TIỀN THU");
}if($cost_type == 'chi' || $cost_type == 'all'){
$this->excel->getActiveSheet()->setCellValue('H7', "TIỀN CHI");
}
$this->excel->getActiveSheet()->getStyle('A7:H7')->getFont()->setSize(11)->setBold(true);
$this->excel->getActiveSheet()->getStyle('A7:H7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A7:H7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$styleThinBlackBorderOutline = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
			),
		),
	);
$this->excel->getActiveSheet()->getStyle('A7:H8')->applyFromArray($styleThinBlackBorderOutline);
$this->excel->getActiveSheet()->getRowDimension(7)->setRowHeight(16.50);
/* end tieu de cot */
$styleDOTBlackBorderOutline = array(
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
$styleDOTBlackBorderTOPBOTOutline = array(
    'borders' => array(
        'top' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
        ),
		'bottom' => array(
            'style' => PHPExcel_Style_Border::BORDER_DOTTED,
            'color' => array('argb' => 'FF000000'),
        ),
    ),
);

$k = 8;
foreach ($rows as $key=>$r) {
	  $this->excel->getActiveSheet()->setCellValue('A' . $k, $r[0]);
	  $this->excel->getActiveSheet()->setCellValue('B' . $k, $r[1]);
//	  $this->excel->getActiveSheet()->setCellValue('C' . $k, $r[2]);
	  
	  $this->excel->getActiveSheet()->setCellValue('D' . $k,$this->Person->get_info($r[2])->first_name.' '.$this->Person->get_info($r[2])->last_name);
	  $this->excel->getActiveSheet()->setCellValue('E' . $k, $r[3]);
	   $this->excel->getActiveSheet()->setCellValue('F' . $k, $r[4]==-1 ? 'Khách lẻ' : $this->Person->get_info($r[4])->first_name.' '.$this->Person->get_info($r[4])->last_name);
         if($cost_type == 'thu' || $cost_type == 'all'){
	  $this->excel->getActiveSheet()->setCellValue('G' . $k, $r[5]);
         }if($cost_type == 'chi' || $cost_type == 'all'){
	  $this->excel->getActiveSheet()->setCellValue('H' . $k, $r[6]);
         }
	  $this->excel->getActiveSheet()->getStyle('A'.($k).':H'.$k)->applyFromArray($styleDOTBlackBorderOutline);
	  $this->excel->getActiveSheet()->getRowDimension($k)->setRowHeight(16);
	  $this->excel->getActiveSheet()->getStyle('G'.$k.':H'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	  $this->excel->getActiveSheet()->getRowDimension($k)->setRowHeight(20);
	  $k++;
 
}
  $i = $this->excel->getActiveSheet()->getHighestRow() ;
  $this->excel->setActiveSheetIndex(0)->mergeCells('A' . ($i+1).':F'.($i+1));
  $this->excel->getActiveSheet()->setCellValue('A' . ($i+1), 'TỔNG CỘNG');
  $this->excel->getActiveSheet()->getStyle('A'.($i+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  
  $this->excel->getActiveSheet()->getStyle('G'.$i.':H'.$i+1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  if($cost_type == 'thu' || $cost_type == 'all'){
  $this->excel->getActiveSheet()->setCellValue('G' . ($i+1), to_currency_unVND_nomar($tien_thu_tong));
  }if($cost_type == 'chi' || $cost_type == 'all'){
  $this->excel->getActiveSheet()->setCellValue('H' . ($i+1), to_currency_unVND_nomar($tien_chi_tong));
  }
  $this->excel->getActiveSheet()->getRowDimension($i)->setRowHeight(18);
  $this->excel->getActiveSheet()->getStyle('A'.$i.':H'.$i+1)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$this->excel->getActiveSheet()->getStyle('G'.($i+1).':H'.($i+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	$this->excel->getActiveSheet()->getStyle('F'.($i+1))->getFont()->setBold(true);
	$this->excel->getActiveSheet()->getStyle('G'.($i+1))->getFont()->setBold(true);
	$this->excel->getActiveSheet()->getStyle('H'.($i+1))->getFont()->setBold(true);
	$this->excel->getActiveSheet()->getStyle('A'.($i+1).':H'.($i+1))->applyFromArray($styleThinBlackBorderOutline);

$this->excel->getActiveSheet()->getRowDimension(1)->setRowHeight(21.75);
$this->excel->getActiveSheet()->getRowDimension(5)->setRowHeight(16);
$this->excel->getActiveSheet()->getRowDimension(6)->setRowHeight(18);
$this->excel->getActiveSheet()->getRowDimension(7)->setRowHeight(28);
$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(0);
$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(35);
$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(38);
$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);

$this->excel->getActiveSheet()->getStyle('A7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('C7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('D7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('E7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('F7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('G7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('H7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->getActiveSheet()->getStyle('A7')->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getStyle('B7')->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getStyle('C7')->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getStyle('D7')->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getStyle('E7')->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getStyle('F7')->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getStyle('G7')->getAlignment()->setWrapText(true);
$this->excel->getActiveSheet()->getStyle('H7')->getAlignment()->setWrapText(true);



$this->excel->getActiveSheet()->getStyle('A7:H' . ($k - 1))->applyFromArray($styleThinBlackBorderOutline);
//$this->excel->getActiveSheet()->getHeaderFooter()->setOddFooter('&RPage &P of &N')
$this->excel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L&BInvoice&RPrinted on &D');
$this->excel->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&B' . $this->excel->getProperties()->getTitle() . '&RPage &P of &N');
$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
$md5file = md5(date('YmdHis')) . '.xlsx';
$filename = 'Báo cáo chi tiết nhân viên.xlsx'; //save our workbook as this file name
$objWriter->save($filename);
if (file_exists($filename)) {
    header('Content-Type: application/vnd.ms-excel'); //mime type
    header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
    header('Cache-Control: max-age=0'); //no cache    
//    header('Content-Description: File Transfer')
//    header('Content-Type: application/octet-stream')
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