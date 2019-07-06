<?php
require_once (APPPATH.'libraries/PHPExcel/PHPExcel.php');
require_once (APPPATH.'libraries/PHPExcel/PHPExcel/IOFactory.php');
require_once (APPPATH.'libraries/PHPExcel/PHPEXCHelper.php');

class HelpFuncExportExcel {
    /**
     * Hàm này được dùng để set các giá trị cho các cột Excel
     */
    function setValueForSheet($sheet, $local, $text, $colIndex) {
        $_sheet = $sheet;
        $_sheet->setCellValue ( $local, $text );
        $colIndex = NextCol ( $colIndex );
        return $colIndex;
    }
    /**
     * Hàm này được dùng để set các giá trị cho các cột Excel
     * và định dạng kiểu dữ liệu.
     */
    function setValueAndTypeForSheet($sheet, $local, $text, $type, $colIndex) {
        $_sheet = $sheet;
        $_sheet->setCellValueExplicit ( $local, $text, $type );
        $colIndex = NextCol ( $colIndex );
        return $colIndex;
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	40
     * font-family:	Times New Roman
     * font-weight:	Bold
     * text-align:	Center
     */
    function setStyle_40_TNR_B_C($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => true,
                'italic' => false,
                'size' => 40
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }

    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	25
     * font-family:	Times New Roman
     * font-weight:	Bold
     * text-align:	Center
     */
    function setStyle_25_TNR_B_C($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => true,
                'italic' => false,
                'size' => 25
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	16
     * font-family:	Times New Roman
     * font-weight:	Not Set
     * text-align:	Not Set
     */
    function setStyle_16_TNR($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => false,
                'italic' => false,
                'size' => 16
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	16
     * font-family:	Times New Roman
     * font-weight:	Italic
     * text-align:	Center
     */
    function setStyle_16_TNR_I_C($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => false,
                'italic' => true,
                'size' => 16
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	16
     * font-family:	Times New Roman
     * font-weight:	Bold
     * text-align:	Center
     */
    function setStyle_16_TNR_B_C($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => true,
                'italic' => false,
                'size' => 16
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	16
     * font-family:	Times New Roman
     * font-weight:	Bold
     * text-align:	Left
     */
    function setStyle_16_TNR_B_L($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => true,
                'italic' => false,
                'size' => 16
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }

    function setStyle_12_TNR_B_R($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => true,
                'italic' => false,
                'size' => 12
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	12
     * font-family:	Times New Roman
     * font-weight:	Bold
     * text-align:	Left
     */
    function setStyle_12_TNR_B_L($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => true,
                'italic' => false,
                'size' => 12
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }

    function setStyle_12_TNR_I_L($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => false,
                'italic' => true,
                'size' => 12
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	8
     * font-family:	Times New Roman
     * font-weight:	italic
     * text-align:	Center
     */
    function setStyle_8_TNR_I_C($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => false,
                'italic' => true,
                'size' => 8
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	14
     * font-family:	Times New Roman
     * font-weight:	Bold
     * text-align:	Center
     */
    function setStyle_14_TNR_B_C($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => true,
                'italic' => false,
                'size' => 14
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	24
     * font-family:	Times New Roman
     * font-weight:	Bold
     * text-align:	Center
     */
    function setStyle_24_TNR_B_C($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => true,
                'italic' => false,
                'size' => 24
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	12
     * font-family:	Times New Roman
     * font-weight:	Bold
     * text-align:	Center
     */
    function setStyle_12_TNR_B_C($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => true,
                'italic' => false,
                'size' => 12
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }

    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	12
     * font-family:	Times New Roman
     * font-weight:	NO
     * text-align:	Left
     */
    function setStyle_12_TNR_N_L($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => false,
                'italic' => false,
                'size' => 12
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }

    function setStyle_12_TNR_N_R($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => false,
                'italic' => false,
                'size' => 12
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	12
     * font-family:	Times New Roman
     * font-weight:	italic
     * text-align:	Center
     */
    function setStyle_12_TNR_I_C($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => false,
                'italic' => true,
                'size' => 12
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }

    /**
     * lê văn kiên
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	12
     * font-family:	Times New Roman
     * font-weight:	No set
     * text-align:	Center
     */
    function setStyle_12_TNR_N_C($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => false,
                'italic' => false,
                'size' => 12
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	11
     * font-family:	Times New Roman
     * font-weight:	No set
     * text-align:	Center
     */
    function setStyle_11_TNR_N_C($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => false,
                'italic' => false,
                'size' => 11
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }

    function setStyle_11_A_B_L_B($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Arial',
                'bold' => true,
                'italic' => false,
                'size' => 11
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_BOTTOM,
                'wrap' => true
            )
        ) );

    }

    function setStyle_11_A_N_R_C($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Arial',
                'bold' => false,
                'italic' => false,
                'size' => 11
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }

    function setStyle_11_A_B_R_C($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Arial',
                'bold' => true,
                'italic' => false,
                'size' => 11
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }

    function setStyle_11_A_B_C_C($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Arial',
                'bold' => true,
                'italic' => false,
                'size' => 11
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }

    function setStyle_11_A_N_L_C($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Arial',
                'bold' => false,
                'italic' => false,
                'size' => 11
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }

    function setStyle_11_A_B_L_T($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Arial',
                'bold' => true,
                'italic' => false,
                'size' => 11
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
                'wrap' => true
            )
        ) );
    }

    function setStyle_11_A_N_L_T($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Arial',
                'bold' => false,
                'italic' => false,
                'size' => 11
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
                'wrap' => true
            )
        ) );
    }

    function setStyle_11_A_N_L_B($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Arial',
                'bold' => false,
                'italic' => false,
                'size' => 11
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_BOTTOM,
                'wrap' => true
            )
        ) );
    }

    function setStyle_11_A_B_L_C($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Arial',
                'bold' => true,
                'italic' => false,
                'size' => 11
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }

    function setStyle_11_A_B_C_T($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Arial',
                'bold' => true,
                'italic' => false,
                'size' => 11
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
                'wrap' => true
            )
        ) );
    }

    function setStyle_11_A_N_C_T($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Arial',
                'bold' => false,
                'italic' => false,
                'size' => 11
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
                'wrap' => true
            )
        ) );
    }

    function setStyle_11_A_B_R_T($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Arial',
                'bold' => true,
                'italic' => false,
                'size' => 11
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
                'wrap' => true
            )
        ) );
    }

    function setStyle_11_A_N_R_T($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Arial',
                'bold' => false,
                'italic' => false,
                'size' => 11
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
                'wrap' => true
            )
        ) );
    }

    /**
     */
    function setStyle_11_TNR_N_L($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => false,
                'italic' => false,
                'size' => 11
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	11
     * font-family:	Times New Roman
     * font-weight:	No set
     * text-align:	No set
     */
    function setStyle_11_TNR($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => false,
                'italic' => false,
                'size' => 11
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	11
     * font-family:	Times New Roman
     * font-weight:	Bold
     * text-align:	No set
     */
    function setStyle_11_TNR_B($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => true,
                'italic' => false,
                'size' => 11
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	11
     * font-family:	Times New Roman
     * font-weight:	Bold
     * text-align:	Center
     */
    function setStyle_11_TNR_B_C($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => true,
                'italic' => false,
                'size' => 11
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	11
     * font-family:	Times New Roman
     * font-weight:	Bold
     * text-align:	Right
     */
    function setStyle_11_TNR_B_R($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => true,
                'italic' => false,
                'size' => 11
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	11
     * font-family:	Times New Roman
     * font-weight:	Bold
     * text-align:	Left
     */
    function setStyle_11_TNR_B_L($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => true,
                'italic' => false,
                'size' => 11
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	11
     * font-family:	Times New Roman
     * font-weight:	Italic
     * text-align:	Center
     */
    function setStyle_11_TNR_I_C($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => false,
                'italic' => true,
                'size' => 11
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	11
     * font-family:	Times New Roman
     * font-weight:	Italic
     * text-align:	Center
     */
    function setStyle_11_TNR_I_L($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => false,
                'italic' => true,
                'size' => 11
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	10
     * font-family:	Times New Roman
     * font-weight:	No set
     * text-align:	Center
     */
    function setStyle_10_TNR_N_C($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => false,
                'italic' => false,
                'size' => 10
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }

    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	10
     * font-family:	Times New Roman
     * font-weight:	No set
     * text-align:	Center
     */
    function setStyle_10_TNR_N_L($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => false,
                'italic' => false,
                'size' => 10
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	10
     * font-family:	Times New Roman
     * font-weight:	No set
     * text-align:	Center
     */
    function setStyle_10_TNR_N_R($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => false,
                'italic' => false,
                'size' => 10
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	10
     * font-family:	Times New Roman
     * font-weight:	No set
     * text-align:	Right
     */
    function setStyle_10_TNR_I_R($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => false,
                'italic' => true,
                'size' => 10
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	10
     * font-family:	Times New Roman
     * font-weight:	Bold
     * text-align:	Not set
     */
    function setStyle_10_TNR_B($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => true,
                'italic' => false,
                'size' => 10
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	10
     * font-family:	Times New Roman
     * font-weight:	Bold
     * text-align:	Left
     */
    function setStyle_10_TNR_B_L($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => true,
                'italic' => false,
                'size' => 10
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	10
     * font-family:	Times New Roman
     * font-weight:	Bold
     * text-align:	Center
     */
    function setStyle_10_TNR_B_C($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => true,
                'italic' => false,
                'size' => 10
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	9
     * font-family:	Times New Roman
     * font-weight:	Bold
     * text-align:	Center
     */
    function setStyle_9_TNR_B_C($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => true,
                'italic' => false,
                'size' => 9
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	9
     * font-family:	Times New Roman
     * font-weight:	Nomarl
     * text-align:	Left
     */
    function setStyle_9_TNR_N_L($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => false,
                'italic' => false,
                'size' => 9
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	9
     * font-family:	Times New Roman
     * font-weight:	Nomarl
     * text-align:	Center
     */
    function setStyle_9_TNR_N_C($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => false,
                'italic' => false,
                'size' => 9
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	10
     * font-family:	Times New Roman
     * font-weight:	Italic
     * text-align:	Center
     */
    function setStyle_10_TNR_I_C($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => false,
                'italic' => true,
                'size' => 10
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	10
     * font-family:	Times New Roman
     * font-weight:	Italic
     * text-align:	Left
     */
    function setStyle_10_TNR_I_L($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => false,
                'italic' => true,
                'size' => 10
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	10
     * font-family:	Times New Roman
     * font-weight:	Not set
     * text-align:	Center
     */
    function setStyle_10_TNR_NO_C($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => false,
                'italic' => false,
                'size' => 10
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	10
     * font-family:	Times New Roman
     * font-weight:	Not set
     * text-align:	Left
     */
    function setStyle_10_TNR_NO_L($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => false,
                'italic' => false,
                'size' => 10
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	10
     * font-family:	Times New Roman
     * font-weight:	Not set
     * text-align:	Right
     */
    function setStyle_10_TNR_NO_R($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => false,
                'italic' => false,
                'size' => 10
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	8
     * font-family:	Times New Roman
     * font-weight:	No set
     * text-align:	Right
     */
    function setStyle_8_TNR_N_R($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => false,
                'italic' => false,
                'size' => 8
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	8
     * font-family:	Times New Roman
     * font-weight:	No set
     * text-align:	Left
     */
    function setStyle_8_TNR_N_L($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => false,
                'italic' => false,
                'size' => 8
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	8
     * font-family:	Times New Roman
     * font-weight:	Bold
     * text-align:	Right
     */
    function setStyle_8_TNR_B_R($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => true,
                'italic' => false,
                'size' => 8
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	8
     * font-family:	Times New Roman
     * font-weight:	Bold
     * text-align:	Left
     */
    function setStyle_8_TNR_B_L($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => true,
                'italic' => false,
                'size' => 8
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	8
     * font-family:	Times New Roman
     * font-weight:	Bold
     * text-align:	Center
     */
    function setStyle_8_TNR_B_C($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => true,
                'italic' => false,
                'size' => 8
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	Not set
     * font-family:	Not set
     * font-weight:	Not set
     * text-align:	Center
     */
    function setStyle_Align_Center($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    function setStyle_Align_Right($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    function setStyle_Align_Left($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng định dạng style cho các tiêu đề của doc
     */
    function setStyleTitleDoc($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => true,
                'italic' => false,
                'size' => 14
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng định dạng style cho ngày tạo doc
     */
    function setStyleDateDoc($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng định dạng style cho các tiêu đề của bảng
     * fontSize:	10
     * font-family:	Times New Roman
     * font-weight:	Bold
     * text-align:	Center
     */
    function setStyleTitleTable($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => true,
                'italic' => false,
                'size' => 10
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng định dạng style cho các dòng dữ liệu của bảng
     * fontSize:	10
     * font-family:	Times New Roman
     * font-weight:	False
     * text-align:	Not set
     */
    function setStyleRowDataTable($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => false,
                'italic' => false,
                'size' => 10
            )
        ) );
    }
    /**
     * Hàm này được dùng định dạng style cho các summary của bảng
     */
    function setStyleSummaryTable($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => true,
                'italic' => false,
                'size' => 10
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }

    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	13
     * font-family:	Times New Roman
     * font-weight:	Bold
     * text-align:	Center
     */
    function setStyle_13_TNR_B_C($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => true,
                'italic' => false,
                'size' => 13
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }

    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	13
     * font-family:	Times New Roman
     * font-weight:	Bold
     * text-align:	Left
     */
    function setStyle_13_TNR_B_L($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => true,
                'italic' => false,
                'size' => 13
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	13
     * font-family:	Times New Roman
     * font-weight:	No Set
     * text-align:	Center
     */
    function setStyle_13_TNR_N_C($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => false,
                'italic' => false,
                'size' => 13
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }

    function setStyle_13_TNR_N_R($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => false,
                'italic' => false,
                'size' => 13
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }

    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	13
     * font-family:	Times New Roman
     * font-weight:	Italic
     * text-align:	Right
     */
    function setStyle_13_TNR_I_R($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => false,
                'italic' => true,
                'size' => 13
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	13
     * font-family:	Times New Roman
     * font-weight:	No Set
     * text-align:	Left
     */

    // free style for excel
    function setStyle($sheet, $colRowStart, $colRowStop, $data) {
        $_sheet = $sheet;
        foreach($data['font'] as $key => $val) {
            $font[$key] = $val;
        }

        foreach($data['alignment'] as $key => $val) {
            $alignment[$key] = $val;
        }

        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => $font,
            'alignment' => $alignment
        ) );
    }

    function setStyle_13_TNR_N_L($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => false,
                'italic' => false,
                'size' => 13
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	13
     * font-family:	Times New Roman
     * font-weight:	italic
     * text-align:	Center
     */
    function setStyle_13_TNR_I_C($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => false,
                'italic' => true,
                'size' => 13
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	13
     * font-family:	Times New Roman
     * font-weight:	italic
     * text-align:	Left
     */
    function setStyle_13_TNR_I_L($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => false,
                'italic' => true,
                'size' => 13
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }

    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	15
     * font-family:	Times New Roman
     * font-weight:	Bold
     * text-align:	Center
     */
    function setStyle_15_TNR_B_C($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => true,
                'italic' => false,
                'size' => 15
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }



    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	13
     * font-family:	Times New Roman
     * font-weight:	No set
     * text-align:	No set
     */
    function setStyle_13_TNR($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => false,
                'italic' => false,
                'size' => 13
            )
        ) );
    }

    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	15
     * font-family:	Times New Roman
     * font-weight:	No set
     * text-align:	No set
     */
    function setStyle_15_TNR($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => false,
                'italic' => false,
                'size' => 15
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	15
     * font-family:	Times New Roman
     * font-weight:	No set
     * text-align:	Center
     */
    function setStyle_15_TNR_N_C($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => false,
                'italic' => false,
                'size' => 15
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }

    function setStyle_12_TNR_N_I($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => false,
                'italic' => true,
                'size' => 12
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	15
     * font-family:	Times New Roman
     * font-weight:	Italic
     * text-align:	Center
     */
    function setStyle_15_TNR_I_C($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => false,
                'italic' => true,
                'size' => 15
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }
    /**
     * Hàm này được dùng để set style với các giá trị
     * fontSize:	15
     * font-family:	Times New Roman
     * font-weight:	Bold
     * text-align:	Left
     */
    function setStyle_15_TNR_B_L($sheet, $colRowStart, $colRowStop) {
        $_sheet = $sheet;
        $_sheet->getStyle ( $colRowStart . ':' . $colRowStop )->applyFromArray ( array (
            'font' => array (
                'name' => 'Times New Roman',
                'bold' => true,
                'italic' => false,
                'size' => 15
            ),
            'alignment' => array (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        ) );
    }

    function get_name_by_column($currentColumn, $adjustment) {
        $columnIndex = PHPExcel_Cell::columnIndexFromString($currentColumn);
        $adjustedColumnIndex = $columnIndex + $adjustment;
        $adjustedColumn = PHPExcel_Cell::stringFromColumnIndex($adjustedColumnIndex - 1);

        return $adjustedColumn;
    }

}