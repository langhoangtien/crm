<?php
$count = count($data);
$this->load->library('Excel');
$objPHPExcel = new PHPExcel();
$this->excel->getActiveSheet()->setShowGridlines(false);
//activate worksheet number 1
$this->excel->setActiveSheetIndex(0);
//error_reporting(E_ALL);
error_reporting(E_ALL & ~E_NOTICE);
$this->excel->getDefaultStyle()
    ->getAlignment()
    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Times New Roman');
$this->excel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(9);
$this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$this->excel->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
$this->excel->getActiveSheet()->getPageSetup()->setVerticalCentered(false);
$this->excel->getActiveSheet()->getPageMargins()->setRight(0.1);
$this->excel->getActiveSheet()->getPageMargins()->setLeft(0.27);
$this->excel->getActiveSheet()->getPageMargins()->setTop(0.25);
$this->excel->getActiveSheet()->getPageMargins()->setBOTTOM(0.5);
$this->excel->getActiveSheet()->getPageMargins()->setFOOTER(0.1);
$this->excel->getActiveSheet()->getPageMargins()->setHEADER(0);
$this->excel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTop(array(6,7));
//name the worksheet
$this->excel->getActiveSheet()->setTitle('Báo cáo công việc');
/* ten dia chi cong ty ngay thang noi dung */

$this->excel->getActiveSheet()->setCellValue('A1',"CÔNG TY TNHH CÔNG NGHỆ LIFETEK");
$this->excel->setActiveSheetIndex(0)->mergeCells('A1:E1');
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

$this->excel->getActiveSheet()->setCellValue('A2',"LIFETEK CO.LTD.,");
$this->excel->setActiveSheetIndex(0)->mergeCells('A2:E2');
$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

$this->excel->getActiveSheet()->setCellValue('J1',"CỘNG HOÀ XÃ HỘI CHỦ NGHĨA VIỆT NAM");
$this->excel->setActiveSheetIndex(0)->mergeCells('J1:L1');
$this->excel->getActiveSheet()->getStyle('J1')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('J1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('J1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

$this->excel->getActiveSheet()->setCellValue('J2',"Độc lập - Tự do - Hạnh phúc");
$this->excel->setActiveSheetIndex(0)->mergeCells('J2:L2');
$this->excel->getActiveSheet()->getStyle('J2')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('J2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('J2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

$this->excel->getActiveSheet()->setCellValue('A4',"BÁO CÁO KẾT QUẢ THỰC HIỆN CÔNG VIỆC ${type}");
$this->excel->setActiveSheetIndex(0)->mergeCells('A4:L4');
$this->excel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

$this->excel->getActiveSheet()->setCellValue('A5',"(Từ ngày ${start_date} đến ngày ${end_date})");
$this->excel->setActiveSheetIndex(0)->mergeCells('A5:L5');
$this->excel->getActiveSheet()->getStyle('A5')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(28);
$this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
$this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(25);


$this->excel->getActiveSheet()->setCellValue('A7', "STT");
$this->excel->setActiveSheetIndex(0)->mergeCells('A7:A8');
$this->excel->getActiveSheet()->getStyle('A7')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('A7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

$this->excel->getActiveSheet()->setCellValue('B7', "TÊN CÔNG VIỆC");
$this->excel->setActiveSheetIndex(0)->mergeCells('B7: B8');
$this->excel->getActiveSheet()->getStyle('B7')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('B7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);


$this->excel->getActiveSheet()->setCellValue('C7',"NGÀY BÁO CÁO");
$this->excel->setActiveSheetIndex(0)->mergeCells('C7:C8');
$this->excel->getActiveSheet()->getStyle('C7')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('C7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('C7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);


$this->excel->getActiveSheet()->setCellValue('D7',"ĐƠN VỊ");
$this->excel->setActiveSheetIndex(0)->mergeCells('D7:D8');
$this->excel->getActiveSheet()->getStyle('D7')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('D7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('D7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

$this->excel->getActiveSheet()->setCellValue('E7',"NỘI DUNG CÔNG VIỆC");
$this->excel->setActiveSheetIndex(0)->mergeCells('E7:E8');
$this->excel->getActiveSheet()->getStyle('E7')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('E7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('E7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

$this->excel->getActiveSheet()->setCellValue('F7',"TIẾN ĐỘ CÔNG VIỆC");
$this->excel->setActiveSheetIndex(0)->mergeCells('F7:I7');
$this->excel->getActiveSheet()->getStyle('F7')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('F7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('F7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

$this->excel->getActiveSheet()->setCellValue('F8',"BẮT ĐẦU");
$this->excel->getActiveSheet()->getStyle('F8')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('F8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('F8')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

$this->excel->getActiveSheet()->setCellValue('G8',"KẾT THÚC");
$this->excel->getActiveSheet()->getStyle('G8')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('G8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('G8')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

$this->excel->getActiveSheet()->setCellValue('H8',"HOÀN THÀNH");
$this->excel->getActiveSheet()->getStyle('H8')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('H8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('H8')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

$this->excel->getActiveSheet()->setCellValue('I8',"PHÊ DUYỆT");
$this->excel->getActiveSheet()->getStyle('I8')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('I8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('I8')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

$this->excel->getActiveSheet()->setCellValue('J7',"TIẾN ĐỘ CÔNG VIỆC");
$this->excel->setActiveSheetIndex(0)->mergeCells('J7:J8');
$this->excel->getActiveSheet()->getStyle('J7')->getFont()->setBold(true)->setSize(12);
$this->excel->getActiveSheet()->getStyle('J7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('J7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);


$this->excel->getActiveSheet()->setCellValue('K7',"TỰ ĐÁNH GIÁ");
$this->excel->setActiveSheetIndex(0)->mergeCells('K7:K8');
$this->excel->getActiveSheet()->getStyle('K7')->getFont()->setSize(12);
$this->excel->getActiveSheet()->getStyle('K7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('K7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

$k = 9;
$stt = 1;
foreach ($data['job_finished'] as $key => $r) {
    //$name_supplier);die;
    $this->excel->getActiveSheet()->setCellValue('A' . $k,$stt);
    $this->excel->getActiveSheet()->setCellValue('B' . $k, $r['jobs_name']);
    $this->excel->getActiveSheet()->setCellValue('C' . $k, $r['jobs_reports_date']);
    $this->excel->getActiveSheet()->setCellValue('D' . $k,$r['department_name']);
    $this->excel->getActiveSheet()->setCellValue('E' . $k, $r['jobs_content']);
    $this->excel->getActiveSheet()->setCellValue('F' . $k, $r['jobs_start_date']);
    $this->excel->getActiveSheet()->setCellValue('G' . $k, $r['jobs_end_date']);
    if($r['jobs_reports_result'] == '100'){
        $this->excel->getActiveSheet()->setCellValue('H' . $k,$r['jobs_reports_date']);
    }else{
        $this->excel->getActiveSheet()->setCellValue('H' . $k,'Chưa hoàn thành');
    }

    $this->excel->getActiveSheet()->setCellValue('I' . $k, $r['jobs_reports_date_manager']);
    $this->excel->getActiveSheet()->setCellValue('J' . $k,'Hoàn thành : '. $r['jobs_reports_result'].' %');
    $this->excel->getActiveSheet()->setCellValue('K' . $k, $r['jobs_reports_content']);
    $stt+=1;
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
/* */
$this->excel->getActiveSheet()->getStyle('A7:K' . ($k-1))->applyFromArray($styleThinBlackBorderOutline);
$this->excel->getActiveSheet()->getHeaderFooter()->setOddFooter('&RPage &P of &N');

//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
//if you want to save it as .XLSX Excel 2007 format
$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
$md5file = md5(date('YmdHis')) . '.xlsx';
//$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
//force user to download the Excel file without writing it to server's HD
$filename = 'thongtin_xuathang_Thuha.xlsx'; //save our workbook as this file name
$objWriter->save($filename);
if (file_exists($filename)) {
    header('Content-Type: application/vnd.ms-excel'); //mime type
    header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
    header('Cache-Control: max-age=0'); //no cache  
    // header('Content-Description: File Transfer');
    // header('Content-Type: application/octet-stream');
    // header('Content-Disposition: attachment; filename=' . basename($file));
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