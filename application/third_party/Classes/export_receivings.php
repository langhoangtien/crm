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
$this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
$this->excel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
$this->excel->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
$this->excel->getActiveSheet()->getPageSetup()->setVerticalCentered(true);
//activate worksheet number 1
$this->excel->setActiveSheetIndex(0);
//error_reporting(E_ALL);
error_reporting(E_ALL & ~E_NOTICE);
//name the worksheet

$this->excel->setActiveSheetIndex(0)->mergeCells('B2:E2');
$this->excel->getActiveSheet()->getStyle('B2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B2')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('B2')->getFont()->setSize(9);
$this->excel->setActiveSheetIndex(0)->mergeCells('B3:D3');
$this->excel->getActiveSheet()->getStyle('B3')->getFont()->setSize(8);
$this->excel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->setCellValue('B2',$this->config->item('company'));
$this->excel->getActiveSheet()->setCellValue('B3',$this->config->item('address'));

$this->excel->setActiveSheetIndex(0)->mergeCells('F2:I2');
$this->excel->getActiveSheet()->setCellValue('F2',"Mẫu số 01 - VT");
$this->excel->getActiveSheet()->getStyle('F2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('F2')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('F2')->getFont()->setSize(10);
$this->excel->setActiveSheetIndex(0)->mergeCells('F3:I3');
$this->excel->getActiveSheet()->getStyle('F3')->getFont()->setSize(8);
$this->excel->getActiveSheet()->getStyle('F3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->setCellValue('F3',"Ban hành theo QĐ số 48/2006/QĐ - BTC");
$this->excel->setActiveSheetIndex(0)->mergeCells('F4:I4');
$this->excel->getActiveSheet()->getStyle('F4')->getFont()->setSize(8);
$this->excel->getActiveSheet()->setCellValue('F4',"Ngày 14 tháng 9 năm 2006 cảu bộ trưởng BTC");
$this->excel->getActiveSheet()->getStyle('F4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('F2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('F3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('F4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->setActiveSheetIndex(0)->mergeCells('B5:I5');
$this->excel->getActiveSheet()->setCellValue('B5',"PHIẾU NHẬP KHO");
$this->excel->getActiveSheet()->getStyle('B5')->getFont()->setSize(15);
$this->excel->getActiveSheet()->getStyle('B5')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('B5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$ngay = date('d');
$thang = date('m');
$nam = date('Y');
$this->excel->setActiveSheetIndex(0)->mergeCells('B6:I6');
$this->excel->setActiveSheetIndex(0)->setCellValue('B6',' Ngày '.$ngay.' Tháng '.$thang.' Năm '.$nam.'');
$this->excel->getActiveSheet()->getStyle('B6')->getFont()->setSize(10);
$this->excel->getActiveSheet()->getStyle('B6')->getFont()->setItalic(true);
$this->excel->getActiveSheet()->getStyle('B6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$this->excel->setActiveSheetIndex(0)->mergeCells('F7:G7');
$this->excel->getActiveSheet()->setCellValue('F7',"Số : ");
$this->excel->setActiveSheetIndex(0)->mergeCells('F8:G8');
$this->excel->getActiveSheet()->setCellValue('F8',"Nợ : ");
$this->excel->setActiveSheetIndex(0)->mergeCells('F9:G9');
$this->excel->getActiveSheet()->setCellValue('F9',"Có : ");
$this->excel->getActiveSheet()->getStyle('F7')->getFont()->setSize(10);
$this->excel->getActiveSheet()->getStyle('F8')->getFont()->setSize(10);
$this->excel->getActiveSheet()->getStyle('F9')->getFont()->setSize(10);
$this->excel->getActiveSheet()->getStyle('F7')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('F8')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('F9')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('F7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('F8')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('F9')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('F7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$this->excel->getActiveSheet()->getStyle('F8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$this->excel->getActiveSheet()->getStyle('F9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$this->excel->setActiveSheetIndex(0)->mergeCells('H7:I7');
$this->excel->getActiveSheet()->setCellValue('H7',$data['receiving_id']);
$this->excel->setActiveSheetIndex(0)->mergeCells('H8:I8');
//$this->excel->getActiveSheet()->setCellValue('H8',"1561");
$this->excel->setActiveSheetIndex(0)->mergeCells('H9:I9');
//$this->excel->getActiveSheet()->setCellValue('H9',"1111");
$this->excel->getActiveSheet()->getStyle('H7')->getFont()->setSize(10);
$this->excel->getActiveSheet()->getStyle('H8')->getFont()->setSize(10);
$this->excel->getActiveSheet()->getStyle('H9')->getFont()->setSize(10);

$this->excel->setActiveSheetIndex(0)->mergeCells('B10:C10');
$this->excel->setActiveSheetIndex(0)->mergeCells('D10:I10');
$this->excel->getActiveSheet()->setCellValue('B10',"Họ tên người giao hàng : ");
$this->excel->getActiveSheet()->setCellValue('D10',$data['employee']);
$this->excel->getActiveSheet()->getStyle('B10')->getFont()->setSize(10);
$this->excel->getActiveSheet()->getStyle('B10')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('D10')->getFont()->setSize(10);
$this->excel->getActiveSheet()->getStyle('D10')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('B10')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('D10')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('D10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->setActiveSheetIndex(0)->mergeCells('B11:I11');
$this->excel->getActiveSheet()->setCellValue('B11',"Diễn giải : ");
$this->excel->getActiveSheet()->getStyle('B11')->getFont()->setSize(10);
$this->excel->getActiveSheet()->getStyle('B11')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('B11')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B11')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->setActiveSheetIndex(0)->mergeCells('B12:C12');
$this->excel->setActiveSheetIndex(0)->mergeCells('D12:I12');
$this->excel->getActiveSheet()->setCellValue('B12',"Nhập tại kho : ");
$this->excel->getActiveSheet()->setCellValue('D12',$data['supplier']);
$this->excel->getActiveSheet()->getStyle('B12')->getFont()->setSize(10);
$this->excel->getActiveSheet()->getStyle('B12')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('B12')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B12')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('D12')->getFont()->setSize(10);
$this->excel->getActiveSheet()->getStyle('D12')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('D12')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('D12')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->getActiveSheet()->setCellValue('B14',"STT");
//$this->excel->getActiveSheet()->setCellValue('E25',"Tên hàng hóa, dịch vụ");
$this->excel->getActiveSheet()->getStyle('B14')->getFont()->setBold(true);
$this->excel->setActiveSheetIndex(0)->mergeCells('C14:E14');
$this->excel->getActiveSheet()->setCellValue('C14',"Tên nhãn hiệu, quy cách, vật tư (hàng hóa)");
$this->excel->getActiveSheet()->getStyle('C14')->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('C14')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B14')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('F14')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('G14')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('H14')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('I14')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B14')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('C14')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('F14')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('H14')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('G14')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('I14')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->setCellValue('F14',"ĐV tính");
$this->excel->getActiveSheet()->getStyle('F14')->getFont()->setBold(true);
$this->excel->getActiveSheet()->setCellValue('G14',"Đơn giá");
$this->excel->getActiveSheet()->getStyle('G14')->getFont()->setBold(true);
$this->excel->getActiveSheet()->setCellValue('H14',"Số lượng");
$this->excel->getActiveSheet()->getStyle('H14')->getFont()->setBold(true);
$this->excel->getActiveSheet()->setCellValue('I14',"Thành tiền");
$this->excel->getActiveSheet()->getStyle('I14')->getFont()->setBold(true);
$this->excel->getActiveSheet()->setShowGridlines(true);
/* */
$k = 15;
$stt = 1;
$tongtienhang = 0;
foreach(array_reverse($data['cart'], true) as $line=>$item){
	$this->excel->getActiveSheet()->setCellValue('B'.$k,$stt);
        $this->excel->getActiveSheet()->getStyle('B'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->setActiveSheetIndex(0)->mergeCells('C'.$k.':E'.$k);
	$this->excel->getActiveSheet()->setCellValue('C'.$k,$item['name']);
        $this->excel->getActiveSheet()->getStyle('C'.($k))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('C'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$this->excel->getActiveSheet()->setCellValue('F'.$k,$this->Unit->item_unit($this->Item->get_info($item['item_id'])->unit)->name);
        $this->excel->getActiveSheet()->getStyle('F'.($k))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('F'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$this->excel->getActiveSheet()->setCellValue('H'.$k,$item['quantity']);
        $this->excel->getActiveSheet()->getStyle('H'.($k))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('H'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$this->excel->getActiveSheet()->setCellValue('G'.$k,to_currency_unVND_nomar($item['price']));
        $this->excel->getActiveSheet()->getStyle('G'.($k))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('G'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	$this->excel->getActiveSheet()->setCellValue('I'.$k,to_currency_unVND_nomar($item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100));
        $this->excel->getActiveSheet()->getStyle('I'.($k))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('I'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $styleDOTBlackBorderOutline = array(
            'borders' => array(
            'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
             ),
        ),
    );

        $this->excel->getActiveSheet()->getStyle('B15:I' .$k)->applyFromArray($styleDOTBlackBorderOutline);
        $this->excel->getActiveSheet()->getStyle('J'.($k))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $this->excel->getActiveSheet()->getStyle('B'.($k))->getBorders()->getLEFT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//        $this->excel->getActiveSheet()->getStyle('A'.($k).)->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//        $this->excel->getActiveSheet()->getStyle('E'.($k))->getBorders()->getLEFT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//        $this->excel->getActiveSheet()->getStyle('F'.($k))->getBorders()->getLEFT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//        $this->excel->getActiveSheet()->getStyle('G'.($k))->getBorders()->getLEFT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//        $this->excel->getActiveSheet()->getStyle('H'.($k))->getBorders()->getLEFT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//$this->excel->getActiveSheet()->getStyle('H22:H23')->getBorders()->getLEFT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        //$this->excel->getActiveSheet()->getStyle('A'.($k).':I'.$k)->getBorders()->getBOTTOM()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$tongtienhang += $item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100; 
$k++;
$stt++;
}
$i = $this->excel->getActiveSheet()->getHighestRow() +1;
$this->excel->setActiveSheetIndex(0)->mergeCells('B'.($i).':G'.($i));
$objRichText = new PHPExcel_RichText();
$objRed = $objRichText->createTextRun('Cộng');
$objRed->getFont()->setBold(true);
$objGreen = $objRichText->createTextRun(''); 
$objRichText->createText(' ');
$objRed->getFont()->setSize(9);
$objRed->getFont()->setName('Times New Roman');
$objGreen->getFont()->setSize(9);
$objGreen->getFont()->setItalic(true);
$objGreen->getFont()->setName('Times New Roman');
$this->excel->getActiveSheet()->getCell('B'.($i))->setValue($objRichText);
$this->excel->getActiveSheet()->getStyle('B'.($i))->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('B'.($i).':G'.($i+2))->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('B'.($i).':I'.($i))->getBorders()->getBOTTOM()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//$this->excel->getActiveSheet()->getStyle('B'.($i+3).':I'.($i+2))->getBorders()->getBOTTOM()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('B'.($i).':I'.($i))->getBorders()->getTOP()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('B'.($i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B'.($i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->setCellValue('I'.$i,to_currency_unVND_nomar($tongtienhang));
$this->excel->getActiveSheet()->getStyle('I'.($i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$this->excel->getActiveSheet()->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);



$this->excel->setActiveSheetIndex(0)->mergeCells('B'.($i+1).':C'.($i+1));
$this->excel->setActiveSheetIndex(0)->mergeCells('D'.($i+1).':I'.($i+1));
$objRichText7 = new PHPExcel_RichText();
$objRed7 = $objRichText7->createTextRun('Số tiền viết bằng chữ');
//$objRed->getFont()->setColor("FFFF0000");
$objRed7->getFont()->setBold(true);
$objGreen7 = $objRichText7->createTextRun(' (Inwords): '); 
$objRichText7->createText(' ');
$objRed7->getFont()->setSize(9);
$objRed7->getFont()->setName('Times New Roman');
$objGreen7->getFont()->setSize(9);
$objGreen7->getFont()->setItalic(true);
$objGreen7->getFont()->setName('Times New Roman');
$this->excel->getActiveSheet()->getCell('B'.($i+1))->setValue($objRichText7);
$this->excel->getActiveSheet()->getStyle('B'.($i+1))->getAlignment()->setIndent(1);
$this->excel->getActiveSheet()->getStyle('B'.($i+1).':I'.($i+2))->getBorders()->getTOP()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('B'.($i+1).':I'.($i+2))->getBorders()->getBOTTOM()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('B'.($i+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$this->excel->getActiveSheet()->getStyle('B'.($i+1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->load->model('Cost');
$this->excel->getActiveSheet()->setCellValue('D'.($i+1),$this->Cost->get_string_number($tongtienhang));
$this->excel->getActiveSheet()->getStyle('D'.($i+1))->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('D'.($i+1))->getFont()->setSize(9);
$this->excel->setActiveSheetIndex(0)->mergeCells('B'.($i+2).':i'.($i+2));
$this->excel->getActiveSheet()->setCellValue('B'.($i+2),"Số chứng từ gốc kèm theo : ");
$this->excel->getActiveSheet()->getStyle('B'.($i+2))->getFont()->setSize(9);
$this->excel->getActiveSheet()->getStyle('B'.($i+2))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B'.($i+2).':I'.($i+2))->getBorders()->getTOP()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('B'.($i+2))->getAlignment()->setIndent(1);
$this->excel->getActiveSheet()->getStyle('B'.($i+2))->getFont()->setBold(true);

$this->excel->setActiveSheetIndex(0)->mergeCells('G'.($i+3).':I'.($i+3));
$this->excel->setActiveSheetIndex(0)->setCellValue('G'.($i+3),' Ngày '.$ngay.' Tháng '.$thang.' Năm '.$nam.'');
$this->excel->getActiveSheet()->getStyle('G'.($i+3))->getFont()->setSize(10);
$this->excel->getActiveSheet()->getStyle('G'.($i+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->setActiveSheetIndex(0)->mergeCells('B'.($i+4).':I'.($i+4));
$this->excel->getActiveSheet()->setCellValue('B'.($i+4),"        Người lập phiếu                 Người giao hàng                    Thủ kho                       Kế toán trưởng");
$this->excel->getActiveSheet()->getStyle('B'.($i+4))->getFont()->setSize(10);
$this->excel->getActiveSheet()->getStyle('B'.($i+4))->getFont()->setBold(true);
$this->excel->getActiveSheet()->getStyle('B'.($i+4))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B'.($i+4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$this->excel->setActiveSheetIndex(0)->mergeCells('B'.($i+5).':I'.($i+5));
$this->excel->getActiveSheet()->setCellValue('B'.($i+5),"           (Ký, họ tên)                        (Ký, họ tên)                     (Ký, họ tên)                        (Ký, họ tên)");
$this->excel->getActiveSheet()->getStyle('B'.($i+5))->getFont()->setSize(10);
$this->excel->getActiveSheet()->getStyle('B'.($i+5))->getFont()->setItalic(true);
$this->excel->getActiveSheet()->getStyle('B'.($i+5))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$this->excel->getActiveSheet()->getStyle('B'.($i+5))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

//$this->excel->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&B' .);
$this->excel->getActiveSheet()->getHeaderFooter()->setOddFooter( '&RPage &P of &N');
$this->excel->setActiveSheetIndex(0)->mergeCells('B49'.':I49');
$this->excel->getActiveSheet()->setCellValue('B49',"Ghi chú: Quý khách hàng xin vui lòng kiểm tra kỹ đơn hàng và ký nhận đầy đủ");
$this->excel->getActiveSheet()->getStyle('B49')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//$this->excel->getActiveSheet()->getStyle('B26')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$styleArray = array(
    'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => 'FF0000'),
        'size'  => 9,
		'italic'=>true,
    ));
$this->excel->getActiveSheet()->getStyle('B49')->applyFromArray($styleArray);
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
//$this->excel->getActiveSheet()->getStyle('A24:H' . ($k))->applyFromArray($styleThinBlackBorderOutline);
/* */
//$styleThinBlueBorderOutline = array(
//    'borders' => array(
//        'allborders' => array(
//            'style' => PHPExcel_Style_Border::BORDER_THIN,
//            'color' => array('argb' => 'ffffffff'),
//        ),
//    ),
//);
//$this->excel->getActiveSheet()->getStyle('A2')->getBorders()->getLEFT()->applyFromArray($styleThinBlueBorderOutline);
$this->excel->getActiveSheet()->getStyle('B1:J1')->getBorders()->getTOP()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('J1:J24')->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('A1:A24')->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//$this->excel->getActiveSheet()->getStyle('J1:J23')->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//$this->excel->getActiveSheet()->getStyle('B7:J7')->getBorders()->getTOP()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('B14:J14')->getBorders()->getTOP()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//$this->excel->getActiveSheet()->getStyle('B22:J22')->getBorders()->getBOTTOM()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//$this->excel->getActiveSheet()->getStyle('B14:J14')->getBorders()->getTOP()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//$this->excel->getActiveSheet()->getStyle('B23:J23')->getBorders()->getBOTTOM()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//$this->excel->getActiveSheet()->getStyle('B24:J24')->getBorders()->getBOTTOM()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('B14')->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('F14')->getBorders()->getLEFT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('G14')->getBorders()->getLEFT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('H14')->getBorders()->getLEFT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('I14')->getBorders()->getLEFT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

//$this->excel->getActiveSheet()->getStyle('B22:B23')->getBorders()->getLEFT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//$this->excel->getActiveSheet()->getStyle('C22:C23')->getBorders()->getLEFT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//$this->excel->getActiveSheet()->getStyle('G22:G23')->getBorders()->getLEFT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//$this->excel->getActiveSheet()->getStyle('F22:F23')->getBorders()->getLEFT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//$this->excel->getActiveSheet()->getStyle('H22:H23')->getBorders()->getLEFT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//$this->excel->getActiveSheet()->getStyle('I22:I23')->getBorders()->getLEFT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

$this->excel->getActiveSheet()->getStyle('J'.($i-2).':J49')->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('A'.($i-2).':A49')->getBorders()->getRIGHT()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//$this->excel->getActiveSheet()->getStyle('B'.($i).':J'.($i+12))->getBorders()->getBOTTOM()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('B49'.':J49')->getBorders()->getBOTTOM()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$this->excel->getActiveSheet()->getStyle('B5:B20')->getAlignment()->setIndent(1);
/* */
$this->excel->getActiveSheet()->getRowDimension('1:5')->setRowHeight(15.75);
$this->excel->getActiveSheet()->getRowDimension('15:20')->setRowHeight(15.75);
$this->excel->getActiveSheet()->getRowDimension(13)->setRowHeight(8.75);
$this->excel->getActiveSheet()->getRowDimension('14')->setRowHeight(18.75);
$this->excel->getActiveSheet()->getRowDimension(6)->setRowHeight(15);
$this->excel->getActiveSheet()->getRowDimension(7)->setRowHeight(17);
$this->excel->getActiveSheet()->getRowDimension('1:13')->setRowHeight(15.75);
$this->excel->getActiveSheet()->getRowDimension(21)->setRowHeight(8);
$this->excel->getActiveSheet()->getRowDimension(22)->setRowHeight(15.25);
$this->excel->getActiveSheet()->getRowDimension(23)->setRowHeight(15);
$this->excel->getActiveSheet()->getRowDimension($i)->setRowHeight(20.75);
$this->excel->getActiveSheet()->getRowDimension($i+1)->setRowHeight(24);
$this->excel->getActiveSheet()->getRowDimension($i+2)->setRowHeight(24);
$this->excel->getActiveSheet()->getRowDimension($i+3)->setRowHeight(24);
$this->excel->getActiveSheet()->getRowDimension($i+4)->setRowHeight(20);
$this->excel->getActiveSheet()->getRowDimension($i+5)->setRowHeight(20);

$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(0.5);
$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(5.7);
$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(28.95);
$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(6.55);
$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(3.55);
$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(9.33);
$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(12.2);
$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(9.33);
$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(12.8);
$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(0.08);

$this->excel->getActiveSheet()->getHeaderFooter()->setOddFooter('&RPage &P of &N');

//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
//if you want to save it as .XLSX Excel 2007 format
$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $md5file = md5(date('YmdHis')) . '.xlsx';
//$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
//force user to download the Excel file without writing it to server's HD
$filename = 'hoadon_nhaphang.xlsx'; //save our workbook as this file name
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