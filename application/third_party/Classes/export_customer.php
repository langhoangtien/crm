<?php

$count = count($data);
$this->load->library('Excel');
$objPHPExcel = new PHPExcel();
$this->excel->getDefaultStyle()
        ->getAlignment()
        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->setShowGridlines(true);
$this->excel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Times New Roman');
$this->excel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(9);
$this->excel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
$this->excel->getActiveSheet()->getPageMargins()->setRight(0.1);
$this->excel->getActiveSheet()->getPageMargins()->setLeft(0.27);
$this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$this->excel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTop(array(6, 6));
//activate worksheet number 1
$this->excel->setActiveSheetIndex(0);
//error_reporting(E_ALL);
error_reporting(E_ALL & ~E_NOTICE);
//name the worksheet
$this->excel->getActiveSheet()->setTitle('Báo cáo danh sách khách hàng');
$this->excel->setActiveSheetIndex(0)->mergeCells('A1:C1');
$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
$this->excel->getActiveSheet()->setCellValue('A1', $this->config->item('company'));
$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->setCellValue('A2', nl2br($this->config->item('address')));
$this->excel->setActiveSheetIndex(0)->mergeCells('A4:H4');
$this->excel->getActiveSheet()->setCellValue('A4', "TỔNG HỢP DANH SÁCH KHÁCH HÀNG");
$this->excel->getActiveSheet()->getStyle('A4')->getFont()->setSize(14)->setBold(true);
$this->excel->getActiveSheet()->getStyle('A4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->getActiveSheet()->setCellValue('A6', "Tên khách hàng");
$this->excel->getActiveSheet()->setCellValue('B6', "NV quản lý");
$this->excel->getActiveSheet()->setCellValue('C6', "Email");
$this->excel->getActiveSheet()->setCellValue('D6', "SĐT di động");
$this->excel->getActiveSheet()->setCellValue('E6', "SĐT bàn");
$this->excel->getActiveSheet()->setCellValue('F6', "Địa chỉ");
$this->excel->getActiveSheet()->setCellValue('G6', "Ngày sinh");
$this->excel->getActiveSheet()->setCellValue('H6', "Ghi chú");
$this->excel->getActiveSheet()->setCellValue('I6', "Tên doanh nghiệp");
$this->excel->getActiveSheet()->setCellValue('J6', "Sinh nhật Cty");
$this->excel->getActiveSheet()->setCellValue('K6', "Vợ con");
$this->excel->getActiveSheet()->setCellValue('L6', "Loại KH");
$this->excel->getActiveSheet()->setCellValue('M6', "Tài khoản ngân hàng");
$this->excel->getActiveSheet()->setCellValue('N6', "MS thuế");
$this->excel->getActiveSheet()->setCellValue('O6', "Chịu thuế");
$this->excel->getActiveSheet()->setCellValue('P6', "Chức vụ");
$this->excel->getActiveSheet()->setCellValue('Q6', "Giới tính");
$this->excel->getActiveSheet()->getStyle('A6:P6')->getFont()->setSize(12)->setBold(true);
$k = 8;
$info_cus_not_city = $this->City->customer_info_in_city_by_employee('', $this->session->userdata('person_id'), $this->session->userdata('ma_nhan_vien'), $this->session->userdata('loai_khach_hang'));
if ($info_cus_not_city) {
    $this->excel->getActiveSheet()->setCellValue('A' . ($k - 1), 'Không thành phố');
    $this->excel->getActiveSheet()->getStyle('A' . ($k - 1))->getFont()->setBold(true);
    foreach ($info_cus_not_city as $value) {
        $this->excel->getActiveSheet()->setCellValue('A' . $k, $value['first_name'] . ' ' . $value['last_name']);
        $info_emp_not_city = $this->Employee->get_info($value['employee_id']);
        $this->excel->getActiveSheet()->setCellValue('B' . $k, $info_emp_not_city->first_name . ' ' . $info_emp_not_city->last_name);
        $this->excel->getActiveSheet()->setCellValue('C' . $k, $value['email']);
        $this->excel->getActiveSheet()->setCellValue('D' . $k, $value['phone_number']);
        $this->excel->getActiveSheet()->setCellValue('E' . $k, $value['phone']);
        $this->excel->getActiveSheet()->setCellValue('F' . $k, $value['address_1']);
        $this->excel->getActiveSheet()->setCellValue('G' . $k, $value['birth_date']);
        $this->excel->getActiveSheet()->setCellValue('H' . $k, $value['comments']);
        $this->excel->getActiveSheet()->setCellValue('I' . $k, $value['company_name']);
        $this->excel->getActiveSheet()->setCellValue('J' . $k, $value['birth_date_1']);
        $this->excel->getActiveSheet()->setCellValue('K' . $k, $value['wife']);
        $info_type_cus_not_city = $this->M_customer_type->get_info($value['type_customer']);
        $this->excel->getActiveSheet()->setCellValue('L' . $k, $info_type_cus_not_city->name);
        $this->excel->getActiveSheet()->setCellValue('M' . $k, $value['account_number']);
        $this->excel->getActiveSheet()->setCellValue('N' . $k, $value['code_tax']);
        if ($value['taxable'] == 1) {
            $this->excel->getActiveSheet()->setCellValue('O' . $k, 'Có');
        } else {
            $this->excel->getActiveSheet()->setCellValue('O' . $k, 'Không');
        }
        $this->excel->getActiveSheet()->setCellValue('P' . $k, $value['positions']);
        if ($value['sex'] == 1) {
            $this->excel->getActiveSheet()->setCellValue('Q' . $k, 'Nam');
        } else {
            $this->excel->getActiveSheet()->setCellValue('Q' . $k, 'Nữ');
        }
        $this->excel->getActiveSheet()->getRowDimension($k)->setRowHeight(15.75);
        $k++;
    }
    $k++;
}
if ($cities != null) {
    foreach ($cities as $city) {
        $queries = $this->City->customer_info_in_city_by_employee($city['id_city'], $this->session->userdata('person_id'), $this->session->userdata('ma_nhan_vien'),$this->session->userdata('loai_khach_hang'));
        if ($queries != null) {
            $this->excel->getActiveSheet()->setCellValue('A' . ($k - 1), $city['name']);
            $this->excel->getActiveSheet()->getStyle('A' . ($k - 1))->getFont()->setBold(true);
            foreach ($queries as $query) {
                $this->excel->getActiveSheet()->setCellValue('A' . $k, $query['first_name'] . ' ' . $query['last_name']);
                $info_emp = $this->Employee->get_info($query['employee_id']);
                $this->excel->getActiveSheet()->setCellValue('B' . $k, $info_emp->first_name . ' ' . $info_emp->last_name);
                $this->excel->getActiveSheet()->setCellValue('C' . $k, $query['email']);
                $this->excel->getActiveSheet()->setCellValue('D' . $k, $query['phone_number']);
                $this->excel->getActiveSheet()->setCellValue('E' . $k, $query['phone']);
                $this->excel->getActiveSheet()->setCellValue('F' . $k, $query['address_1']);
                $this->excel->getActiveSheet()->setCellValue('G' . $k, $query['birth_date']);
                $this->excel->getActiveSheet()->setCellValue('H' . $k, $query['comments']);
                $this->excel->getActiveSheet()->setCellValue('I' . $k, $query['company_name']);
                $this->excel->getActiveSheet()->setCellValue('J' . $k, $query['birth_date_1']);
                $this->excel->getActiveSheet()->setCellValue('K' . $k, $query['wife']);
                $info_type_cus = $this->M_customer_type->get_info($query['type_customer']);
                $this->excel->getActiveSheet()->setCellValue('L' . $k, $info_type_cus->name);
                $this->excel->getActiveSheet()->setCellValue('M' . $k, $query['account_number']);
                $this->excel->getActiveSheet()->setCellValue('N' . $k, $query['code_tax']);
                if ($query['taxable'] == 1) {
                    $this->excel->getActiveSheet()->setCellValue('O' . $k, 'Có');
                } else {
                    $this->excel->getActiveSheet()->setCellValue('O' . $k, 'Không');
                }
                $this->excel->getActiveSheet()->setCellValue('P' . $k, $query['positions']);
                if ($query['sex'] == 1) {
                    $this->excel->getActiveSheet()->setCellValue('Q' . $k, 'Nam');
                } else {
                    $this->excel->getActiveSheet()->setCellValue('Q' . $k, 'Nữ');
                }
                $this->excel->getActiveSheet()->getRowDimension($k)->setRowHeight(15.75);
                $k++;
            }
            $k++;
        }
    }
}
$styleThinBlackBorderOutline = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
        ),
    ),
);

$this->excel->getActiveSheet()->getPageMargins()->setRight(0.1);
$this->excel->getActiveSheet()->getPageMargins()->setLeft(0.15);
$this->excel->getActiveSheet()->getPageMargins()->setTop(0.25);
$this->excel->getActiveSheet()->getPageMargins()->setBOTTOM(0.5);
$this->excel->getActiveSheet()->getPageMargins()->setHEADER(0.1);
$this->excel->getActiveSheet()->getPageMargins()->setFOOTER(0);

$this->excel->getActiveSheet()->getRowDimension(1)->setRowHeight(21.75);
$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25.29);
$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(18);
$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(18);
$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(35);
$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(18);
$this->excel->getActiveSheet()->getColumnDimension('G')->setAutoSize(7.83);
$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(18.45);
$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20.43);
$this->excel->getActiveSheet()->getColumnDimension('J')->setAutoSize(7.83);
$this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(10.14);
$this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(30);
$this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(12);
$this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(12);
$this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(12);
$this->excel->getActiveSheet()->getColumnDimension('P')->setWidth(12);
$this->excel->getActiveSheet()->getColumnDimension('Q')->setWidth(12);

$this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('C6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('D6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('E6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('F6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('G6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('H6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('I6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('J6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('K6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('L6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('M6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('N6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('O6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('P6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('Q6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

/* style */
$styleThinBlackBorderOutline = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
        ),
    ),
);
/* */
$this->excel->getActiveSheet()->getStyle('A6:Q6' . ($k))->applyFromArray($styleThinBlackBorderOutline);
$this->excel->getActiveSheet()->getHeaderFooter()->setOddFooter('&RPage &P of &N');

//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
//if you want to save it as .XLSX Excel 2007 format
$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
$md5file = md5(date('YmdHis')) . '.xlsx';
//$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
//force user to download the Excel file without writing it to server's HD
$filename = 'thongtin_khachhang.xlsx'; //save our workbook as this file name
//$filename = 'Book1.xlsx';

$objWriter->save($filename);
$this->session->unset_userdata("ma_nhan_vien");
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