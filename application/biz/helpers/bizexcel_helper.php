<?php
require_once (APPPATH.'libraries/PHPExcel/PHPExcel.php');
require_once(APPPATH.'libraries/PHPExcel/PHPExcel/IOFactory.php');

class BizExcel {
	
	protected $oPHPExcel;
	
	protected $oWriter;
	
	protected $formattedFile;
	
	protected $newFileName;
	
	protected $excelPath;
    
	protected $listName;
	
	protected $numberRowStartBody;
	
	protected $numberRowBeginRow;

	protected $RowEndBody;

	protected $Tinh_tong_dau_trang;

	protected $RowEndBody_theo_tung_cot;

	protected $Row_title;

	protected $Chu_ky_cuoi_bang = false;

	protected $dataExcel = array();
    
	protected $typeExport = '';
	
	//add more infomation about parent rows 
	protected $moreChildData = false;
	
	protected $childDataExcel = [];
	
	protected $moreChildDataExcel = [];
	
	protected $moreNumberOfChildData = 0;
	
	protected $headerOfChildCol = [];
	
	protected $headerOfMoreChildCol = [];
	
	protected $headerOfMoreChildColForCreating = [];
	
	protected $fieldOfChildBody =[];
	
	protected $moreFieldOfChildBody = [];
	
	protected $mergeCell = [];
	
	protected $dataExtra = [];

	protected $headerOfBody = [];
	

	
	protected $headerOfCol =[];
	
	protected $headerOfMultiCol=[];

	protected $tat_auto_size = true;

	# Style
	protected $border = array(
		      'borders' => array(
		          'allborders' => array(
		              'style' => PHPExcel_Style_Border::BORDER_THIN
		          )
		      ));

	protected $bold = array(
		      'borders' => array(
		          'allborders' => array(
		              'style' => PHPExcel_Style_Border::BORDER_THIN
		          )
		      ));
              
    /*
    * @String mergeRow
    * @return BizExcel
    */
    public function setTypeExport($typeExport) {
		$this->typeExport = $typeExport;
		return $this;
	}
	public function __construct($formattedFile = '') {
		$this->excelPath = DOCUMENT_PATH . 'excel/';
		$this->formattedFile = $this->excelPath . $formattedFile;
		if (is_file($this->formattedFile)) {
			$this->oPHPExcel = PHPExcel_IOFactory::createReader('Excel2007');
			$this->oPHPExcel = $this->oPHPExcel->load($this->formattedFile);
		}

	}
	
	public function tat_auto_size($tat_auto_size = true){
		$this->tat_auto_size = $tat_auto_size;
		return $this;
	}
	public function setNewFileName($newFileName = '') {
		$this->newFileName = $newFileName;
		return $this;
	}
	
	public function setDataExcel($dataExcel = array()) {
		$this->dataExcel = $dataExcel;
		return $this;
	}
	public function setChildDataExcel($childDataExcel = array()) {
        $this->childDataExcel = $childDataExcel;
	return $this;
	}
	public function setMoreChildDataExcel($moreChildDataExcel = array()) {
        $this->moreChildDataExcel = $moreChildDataExcel;
	return $this;
	}
	
	public function generateFile($saveToLocal = true, $newFileName = '', $final = true) {
		if (!empty($newFileName)) {
			$this->newFileName = $newFileName;
		}

		if (!empty($this->Row_title)) {

			$this->xu_ly_du_lieu_hien_thi_dau_trang();
		}
		if (!empty($this->headerOfMultiCol)) {
			$this->buildHeaderOfMultiCol();
		}
		if (!empty($this->headerOfCol)) {
			$this->buildHeaderOfCol();
		}
	
		
		if (!empty($this->dataExcel)) {
			$this->buildBobyOfTable();
		}
		
		if (!empty($this->dataExtra)) {
			$this->buildExtraData();
		}

		if ($final) {
			
			$sheetNames = $this->oPHPExcel->getSheetNames();
			if (in_array('___TEMPLATE', $sheetNames)) {
				$this->oPHPExcel->removeSheetByIndex(array_search('___TEMPLATE', $sheetNames));
			}
			$objWriter = PHPExcel_IOFactory::createWriter($this->oPHPExcel, 'Excel2007');

			# Set cấu hình để sử dụng được lệnh của excel
			$objWriter->setPreCalculateFormulas(true);

			if ($saveToLocal) {

				$objWriter->save($this->excelPath . $this->newFileName);
				return null;
			} else {
				ob_start();
				$objWriter->save('php://output');
				$excelOutput = ob_get_clean();
				return $excelOutput;
			}
		}
	}
	
	public function addToNewSheet($sheetName = '') {
		$sheetCount = $this->oPHPExcel->getSheetCount();
		$activeSheet = $this->oPHPExcel->getSheet(0)->copy();
		$objWorkSheet = clone $activeSheet;
		if ($sheetName) {
			$objWorkSheet->setTitle($sheetName);
		}

		$this->oPHPExcel->addSheet($objWorkSheet);
		$this->oPHPExcel->setActiveSheetIndex($sheetCount);
		return $this;
	}
	
	public function setActiveSheet($index = 0, $sheetName = '') {
		if ($index) {
			$objWorkSheet = $this->oPHPExcel->createSheet($index);
			$this->oPHPExcel->setActiveSheetIndex($index);
		}
		
		if ($sheetName) {
			!empty($objWorkSheet) ? $objWorkSheet->setTitle($sheetName) : $this->oPHPExcel->getActiveSheet()->setTitle($sheetName);
		}
		return $this;
	}
	

	
	
	//------------------------------------------------------------------------------------------------------------------------------------------
	//  BUILD HEADER FOR COLUMN OR MULTICOLUMNS
	//------------------------------------------------------------------------------------------------------------------------------------------
	
	
	public function buildHeaderOfCol() {
		foreach ($this->headerOfCol as $headerCell) {
			if (!empty($headerCell['text'])) {
				$this->oPHPExcel->getActiveSheet()->setCellValue($headerCell['col'] .($this->numberRowStartBody), $headerCell['text']);
				$this->applyCellStyle($headerCell['col'] . $this->numberRowStartBody, $headerCell['styles']);
				$this->oPHPExcel->getActiveSheet()->getStyle($headerCell['col'] .$this->numberRowStartBody)->applyFromArray($this->border);
			}
		}
	}
	// build header for child table which is in collapsed row
	
	
	//The first one
	private function buildHeaderOfChildCol($numberRowStart) {
		foreach ($this->headerOfChildCol as $headerCell) {
		
			if (!empty($headerCell['text'])) {

				$this->oPHPExcel->getActiveSheet()->setCellValue($headerCell['col'] . $numberRowStart, $headerCell['text']);
				$this->applyCellStyle($headerCell['col'] . $numberRowStart, $headerCell['styles']);
				$this->oPHPExcel->getActiveSheet()->getStyle($headerCell['col'] . $numberRowStart)->applyFromArray($this->border);
			}
		}
	}
	
	//Another
	private function buildHeaderOfMoreChildCol($numberRowStart) {
		foreach ($this->headerOfMoreChildColForCreating as $headerCell) {
		
			if (!empty($headerCell['text'])) {
			
				if(strpos($headerCell['col'],':'))
				{
					$this->oPHPExcel->getActiveSheet()->mergeCells(explode(':',$headerCell['col'])[0].$numberRowStart.':'.explode(':',$headerCell['col'])[1].$numberRowStart)->setCellValue(explode(':',$headerCell['col'])[0].$numberRowStart, $headerCell['text']);
					$this->oPHPExcel->getActiveSheet()->getStyle(explode(':',$headerCell['col'])[0].$numberRowStart.':'.explode(':',$headerCell['col'])[1].$numberRowStart)->applyFromArray($this->border);
					$this->applyCellStyle(explode(':',$headerCell['col'])[0].$numberRowStart.':'.explode(':',$headerCell['col'])[1].$numberRowStart, $headerCell['styles']);
                    
				}
				else
				{
					$this->oPHPExcel->getActiveSheet()->setCellValue($headerCell['col'] . $numberRowStart, $headerCell['text']);
					$this->applyCellStyle($headerCell['col'] . $numberRowStart, $headerCell['styles']);
					$this->oPHPExcel->getActiveSheet()->getStyle($headerCell['col'] . $numberRowStart)->applyFromArray($this->border);
				}
		
			}
		}
	}
	
	// create column header for multicols
	protected function buildHeaderOfMultiCol() {
		if(!empty($this->numberRowBeginRow))
		{
		  foreach($this->headerOfMultiCol as $headerCell){
				if (!empty($headerCell['text'])) {
						$this->oPHPExcel->getActiveSheet()->mergeCells($headerCell['mergeStartCol'].$this->numberRowBeginRow.':'.$headerCell['mergeEndCol'].'1')->setCellValue($headerCell['mergeStartCol'] . $this->numberRowBeginRow, $headerCell['text']);
						$this->applyCellStyle($headerCell['mergeStartCol'].$this->numberRowBeginRow.':'.$headerCell['mergeEndCol'].$this->numberRowBeginRow, $headerCell['styles']);
						
				}
			}
		}
		else
		{
			 foreach($this->headerOfMultiCol as $headerCell){
				if (!empty($headerCell['text'])) {
						$this->oPHPExcel->getActiveSheet()->mergeCells($headerCell['mergeStartCol'].':'.$headerCell['mergeEndCol'])->setCellValue($headerCell['mergeStartCol'], $headerCell['text']);
						$this->applyCellStyle($headerCell['mergeStartCol'].':'.$headerCell['mergeEndCol'], $headerCell['styles']);
						
				}
			}
		}
	}
	//------------------------------------------------------------------------------------------------------------------------------------------
	//                END BUILD HEADER FOR COLUMN OR MULTICOLUMNS
	//------------------------------------------------------------------------------------------------------------------------------------------
	
	
	
	
	
	# ------------------------------------------------------------------------------------------------#
								# Phần body và phần cuối trang
	# ------------------------------------------------------------------------------------------------#

	protected function buildBobyOfTable() {

    // create a body of table with many subbodies
		if(is_array($this->headerOfChildCol)&&!empty($this->headerOfChildCol)){
                
                // increment row which uses for inserting value
				$row_index = 0;
                // auto increment, parent row
				$row_parent_count = 1;
				if(!empty($this->headerOfCol)&&is_array($this->headerOfCol))
				{
					$this->buildHeaderOfCol();
				}
                $this->oPHPExcel->getActiveSheet()->freezePane("A".($this->numberRowStartBody+1));
				foreach ($this->dataExcel as $index => $row) {
					foreach ($this->headerOfBody as $cell) {
                         // if field = __AUTO__, auto increment
						if($cell['value_field'] == '__AUTO__') {
							$this->oPHPExcel->getActiveSheet()->getStyle($cell['col'] . ($this->numberRowStartBody + $row_index + 1))->applyFromArray($this->border);
							$this->oPHPExcel->getActiveSheet()->setCellValue($cell['col'].($this->numberRowStartBody + $row_index + 1),$row_parent_count);
							$this->applyCellStyle($cell['col'].($this->numberRowStartBody + $row_index + 1),array('is_fill'=>true,'color'=>'dee6ec','font'=>true,'font_size'=>12));
						} elseif(isset($cell['col'])) {
							# Thực thi chèn dữ liệu
							$this->oPHPExcel->getActiveSheet()->getStyle($cell['col'] . ($this->numberRowStartBody + $row_index + 1))->applyFromArray($this->border);
							if($this->tat_auto_size){
								$this->oPHPExcel->getActiveSheet()->getColumnDimension($cell['col'])->setAutoSize(true);
							}
							$this->oPHPExcel->getActiveSheet()->setCellValue($cell['col'] . ($this->numberRowStartBody + $row_index + 1), isset($row[$cell['value_field']]) ? $row[$cell['value_field']] : '');
						  $this->applyCellStyle($cell['col'].($this->numberRowStartBody + $row_index + 1),array('is_fill'=>true,'color'=>'dee6ec','font'=>true,'font_size'=>12));
						}
					}
					$row_parent_count++;
					$row_index = $this->numberRowStartBody + $row_index + 2;
					$merge_row_start = $row_index;
					
					
					//add header col for the first child  
					$this->buildHeaderOfChildCol($row_index);
                    
					//add data  for the first child  
					if(!empty($this->childDataExcel[$index])) {
						foreach ($this->childDataExcel[$index] as $child_index => $child_row) {
						foreach ($this->fieldOfChildBody as $cell) {
								if($cell['value_field'] == '__AUTO__') {
									$this->oPHPExcel->getActiveSheet()->getStyle($cell['col'] . ($row_index+1))->applyFromArray($this->border);
									$this->oPHPExcel->getActiveSheet()->setCellValue($cell['col'].($row_index+1),$child_index+1 );
									$this->applyCellStyle($cell['col'].($row_index+1),array('font'=>true,'font_size'=>10));
								} elseif(isset($cell['col'])) {
                                    
									# Thực thi chèn dữ liệu
									$this->oPHPExcel->getActiveSheet()->getStyle($cell['col'] . ($row_index+1))->applyFromArray($this->border);
									if($this->tat_auto_size){
										$this->oPHPExcel->getActiveSheet()->getColumnDimension($cell['col'])->setAutoSize(true);
									}
									$this->oPHPExcel->getActiveSheet()->setCellValue($cell['col'] . ($row_index+1), isset($child_row[$cell['value_field']]) ? $child_row[$cell['value_field']] : '');
									$this->applyCellStyle($cell['col'].($row_index+1),array('font'=>true,'font_size'=>10));
								}
							}
							$row_index++;
						}
					}

					//add data for another child if bool(moreChildData) equals to true
					if($this->moreChildData) {
						foreach($this->headerOfMoreChildCol as $key=>$headerOfMoreChildCol)
						{
							if(!empty($this->moreChildDataExcel[$key][$index]))
								{
								
									$this->headerOfMoreChildColForCreating= $headerOfMoreChildCol;
									$this->buildHeaderOfMoreChildCol($row_index+1);
									$row_index++;
								
										foreach ($this->moreChildDataExcel[$key][$index] as $moreChildDataExcelRow) {
											foreach ($this->moreFieldOfChildBody[$key] as $cell) {
									                // if field = __AUTO__, auto increment
													if($cell['value_field'] == '__AUTO__') {
														$this->oPHPExcel->getActiveSheet()->getStyle($cell['col'] . ($row_index+1))->applyFromArray($this->border);
														$this->oPHPExcel->getActiveSheet()->setCellValue($cell['col'].($row_index),$index );
														$this->applyCellStyle($cell['col'].($row_index),array('font'=>true,'font_size'=>10));
													} elseif(isset($cell['col'])) {
														# Thực thi chèn dữ liệu
														if(strpos($cell['col'],':'))
															{
																$this->oPHPExcel->getActiveSheet()->mergeCells(explode(':',$cell['col'])[0]. ($row_index+1).':'.explode(':',$cell['col'])[1]. ($row_index+1))->setCellValue(explode(':',$cell['col'])[0]. ($row_index+1),  isset($moreChildDataExcelRow[$cell['value_field']]) ? $moreChildDataExcelRow[$cell['value_field']] : '');
																$this->oPHPExcel->getActiveSheet()->getStyle(explode(':',$cell['col'])[0]. ($row_index+1).':'.explode(':',$cell['col'])[1]. ($row_index+1))->applyFromArray($this->border);
																$this->applyCellStyle(explode(':',$cell['col'])[0]. ($row_index+1).':'.explode(':',$cell['col'])[1]. ($row_index+1), array('font'=>true,'font_size'=>10));
																		
															}
															else
															{	
																$this->applyCellStyle($cell['col'] .  ($row_index+1),array('font'=>true,'font_size'=>10));
																$this->oPHPExcel->getActiveSheet()->getStyle($cell['col'] . ($row_index+1))->applyFromArray($this->border);
																$this->oPHPExcel->getActiveSheet()->setCellValue($cell['col'] . ($row_index+1), isset($moreChildDataExcelRow[$cell['value_field']]) ? $moreChildDataExcelRow[$cell['value_field']] : '');
															}
													}
											}
											$row_index++;
										}
								}
						}
			
					}
					$this->oPHPExcel->getActiveSheet()->mergeCells('A'.$merge_row_start.':'.'A'.($row_index));
					for ($row = $merge_row_start; $row <= $row_index; ++$row) {
					$this->oPHPExcel->getActiveSheet()
											->getRowDimension($row)
											->setOutlineLevel(1)
											->setVisible(false)
											->setCollapsed(true);
						}
					$row_index = $row_index - $this->numberRowStartBody;
			  }
		}elseif ($this->typeExport == 'mergeCol') {
            $index  = 0;
            $endCol   = end($this->headerOfBody)['col'];
            $listCordinateTotal = [];
            foreach ($this->dataExcel as $listName => $dataContains) {
                $cordinateItemName =  $this->numberRowStartBody + $index;
                $this->oPHPExcel->getActiveSheet()->mergeCells('A'.$cordinateItemName.':'.$endCol.$cordinateItemName)->setCellValue('A'.$cordinateItemName, isset($this->listName[$listName]) ? $this->listName[$listName] : '');
                $this->oPHPExcel->getActiveSheet()->getStyle('A'.$cordinateItemName.':'.$endCol.$cordinateItemName)->applyFromArray($this->border);
                $index++;
                foreach ($dataContains as $data) {
                    if (empty($data['item'])) {
                        $dataForeach = $dataContains;
                    } else {
                        $dataForeach = $data['item'];
                    }
 
                    $startIndex = $this->numberRowStartBody + $index;
                    foreach ($dataForeach as $row) {
                        $quantityCol = ''; 
                        $priceCol ='';
                        $discountCol = ''; 
                        foreach ($this->headerOfBody as $cell) {
                            if(isset($cell['col'])) {
                                # Thực thi chèn dữ liệu
                                if($cell['value_field'] == '__AUTO__') {
                                    $this->oPHPExcel->getActiveSheet()->getStyle($cell['col'] . ($this->numberRowStartBody + $index ))->applyFromArray($this->border);
                                    $this->oPHPExcel->getActiveSheet()->setCellValue($cell['col'].($this->numberRowStartBody + $index),$index + 1);
                                } elseif(isset($cell['value_field'])) {
                                    switch ($cell['value_field']) {
                                        case 'quantity' : $quantityCol = $cell['col']; break;
                                        case 'price'    : $priceCol = $cell['col']; break;
                                        case 'discount' : $discountCol = $cell['col']; break;
                                        default : break; 
                                    }
                                    $cordinate =  $this->numberRowStartBody + $index;
                                    if ($cell['value_field'] != 'total' ) {
                                        
                                        # Thực thi chèn dữ liệu
                                        $this->oPHPExcel->getActiveSheet()->getStyle($cell['col'] . ($cordinate))->applyFromArray($this->border);
                                        if($this->tat_auto_size){
                                            $this->oPHPExcel->getActiveSheet()->getColumnDimension($cell['col'])->setAutoSize(true);
                                        }
                                        $this->oPHPExcel->getActiveSheet()->setCellValue($cell['col'] . ($cordinate), isset($row[$cell['value_field']]) ? $row[$cell['value_field']] : '');
                                    } else {
                                        $this->oPHPExcel->getActiveSheet()->getStyle($cell['col'] . ($cordinate))->applyFromArray($this->border);
                                        $this->oPHPExcel->getActiveSheet()->setCellValue($cell['col'] . ($cordinate),'='.$priceCol.$cordinate.'*'.$quantityCol.$cordinate.'-'.$priceCol.$cordinate.'*'.$quantityCol.$cordinate.'*'.$discountCol.$cordinate.'/100');
                                    }
                                }
                            }
                        }
                        $index++;
                    }
                    if (!empty($data['item'])) {
						$this->oPHPExcel->getActiveSheet()->getStyle('A'.$startIndex)->applyFromArray($this->border);
                        $this->oPHPExcel->getActiveSheet()->mergeCells('A'.$startIndex.':A'.($this->numberRowStartBody + $index-1));
                        $this->oPHPExcel->getActiveSheet()->setCellValue('A'.$startIndex,$data['itemName'] );
                    } else {
                        $stt = 1;
                        for ($i= $startIndex; $i<= ($this->numberRowStartBody + $index);$i++ ) {
                            $this->oPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($this->border);
                            $this->oPHPExcel->getActiveSheet()->setCellValue('A'.$i, $stt++);
                           
                        }
                        
                    }
                    
                }
            }
        } elseif($this->typeExport == 'ManyPart') {
            $index  = 0;
            $endCol   = end($this->headerOfBody)['col'];
            $listCordinateTotal = [];
            foreach ($this->dataExcel as $listName => $dataContains) {
                $cordinateItemName =  $this->numberRowStartBody + $index;
                $this->oPHPExcel->getActiveSheet()->mergeCells('A'.$cordinateItemName.':'.$endCol.$cordinateItemName)->setCellValue('A'.$cordinateItemName, isset($dataContains['itemName']) ? $dataContains['itemName'] : '');
                $this->oPHPExcel->getActiveSheet()->getStyle('A'.$cordinateItemName.':'.$endCol.$cordinateItemName)->applyFromArray($this->border);
                $index++;
                $startIndex = $this->numberRowStartBody + $index;
                foreach ($dataContains['item'] as $row) {
                    $quantityCol = ''; 
                    $priceCol ='';
                    $discountCol = ''; 
                    foreach ($this->headerOfBody as $cell) {
                        if(isset($cell['col'])) {
                            # Thực thi chèn dữ liệu
                             $cordinate =  $this->numberRowStartBody + $index;
                            if($cell['value_field'] == '__AUTO__') {
                                $this->oPHPExcel->getActiveSheet()->getStyle($cell['col'] . $cordinate)->applyFromArray($this->border);
                                $this->oPHPExcel->getActiveSheet()->setCellValue($cell['col'].$cordinate ,$index + 1);
                            } elseif(isset($cell['value_field'])) {
                                switch ($cell['value_field']) {
                                    case 'quantity' : $quantityCol = $cell['col']; break;
                                    case 'price'    : $priceCol = $cell['col']; break;
                                    case 'discount' : $discountCol = $cell['col']; break;
                                    default : break; 
                                }
                               
                                if ($cell['value_field'] != 'total' ) {
                                    if(strpos($cell['col'],':')) {
                                        $this->oPHPExcel->getActiveSheet()->mergeCells(explode(':',$cell['col'])[0]. $cordinate.':'.explode(':',$cell['col'])[1]. $cordinate)->setCellValue(explode(':',$cell['col'])[0].$cordinate, isset($row[$cell['value_field']]) ? $row[$cell['value_field']] : '');
                                        $this->oPHPExcel->getActiveSheet()->getStyle(explode(':',$cell['col'])[0]. $cordinate.':'.explode(':',$cell['col'])[1].$cordinate)->applyFromArray($this->border);
                                    } else {	
                                        $this->applyCellStyle($cell['col'] .  ($row_index+1),array('font'=>true,'font_size'=>10));
                                        $this->oPHPExcel->getActiveSheet()->getStyle($cell['col'] . $cordinate)->applyFromArray($this->border);
                                        $this->oPHPExcel->getActiveSheet()->setCellValue($cell['col'] . $cordinate, isset($row[$cell['value_field']]) ? $row[$cell['value_field']] : '');
                                    } 
                                } else {
                                    $this->oPHPExcel->getActiveSheet()->getStyle($cell['col'] . ($cordinate))->applyFromArray($this->border);
                                    $this->oPHPExcel->getActiveSheet()->setCellValue($cell['col'] . ($cordinate),'='.$priceCol.$cordinate.'*'.$quantityCol.$cordinate.'-'.$priceCol.$cordinate.'*'.$quantityCol.$cordinate.'*'.$discountCol.$cordinate.'/100');
                                }
                            }
                        }
                    }
                    $index++;
                }
                $cordinateTotal =$this->numberRowStartBody + $index;
                $listCordinateTotal[] = 'A'.$cordinateTotal;
                $this->oPHPExcel->getActiveSheet()->mergeCells('A'.$cordinateTotal.':'.$endCol.$cordinateTotal)->setCellValue('A'.$cordinateTotal, '=SUM('.$endCol.$startIndex.':'.$endCol.$cordinateTotal.')');
                $this->oPHPExcel->getActiveSheet()->getStyle('A'.$cordinateTotal.':'.$endCol.$cordinateTotal)->applyFromArray($this->border);
                $index++;
                $stt = 1;
                for ($i= $startIndex; $i<($this->numberRowStartBody + $index -1 );$i++ ) {
                    $this->oPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($this->border);
                    $this->oPHPExcel->getActiveSheet()->setCellValue('A'.$i, $stt++);
                }
            }
                $this->oPHPExcel->getActiveSheet()->mergeCells('A'. ($this->numberRowStartBody + $index).':'.$endCol.( $this->numberRowStartBody + $index))->setCellValue('A'.($this->numberRowStartBody + $index), '=SUM('.reset($listCordinateTotal).','.end($listCordinateTotal).')');
                $this->oPHPExcel->getActiveSheet()->getStyle('A'. ($this->numberRowStartBody + $index).':'.$endCol.( $this->numberRowStartBody + $index))->applyFromArray($this->border);
                          
           
            // die;
        }
        
        else
		{
		
			foreach ($this->dataExcel as $index => $row) {
				foreach ($this->headerOfBody as $cell) {
                    
					// if field = __AUTO__, auto increment
					if($cell['value_field'] == '__AUTO__') {
						$this->oPHPExcel->getActiveSheet()->getStyle($cell['col'] . ($this->numberRowStartBody + $index + 1))->applyFromArray($this->border);
						$this->oPHPExcel->getActiveSheet()->setCellValue($cell['col'].($this->numberRowStartBody + $index + 1),$index + 1);
					} elseif(isset($cell['col'])) {
						# Thực thi chèn dữ liệu
						$this->oPHPExcel->getActiveSheet()->getStyle($cell['col'] . ($this->numberRowStartBody + $index + 1))->applyFromArray($this->border);
						if($this->tat_auto_size){
							$this->oPHPExcel->getActiveSheet()->getColumnDimension($cell['col'])->setAutoSize(true);
						}
						$this->oPHPExcel->getActiveSheet()->setCellValue($cell['col'] . ($this->numberRowStartBody + $index + 1), isset($row[$cell['value_field']]) ? $row[$cell['value_field']] : '');
					}
				}
			}
		}

		
		if($this->RowEndBody_theo_tung_cot)
		# xử lý tiếp dữ liệu, cho phần cuối trang
		$row_fotter = $this->xu_ly_du_lieu_tinh_tong_cuoi_bang_theo_tung_cot($this->numberRowStartBody+1,$this->numberRowStartBody + $index);
		elseif($this->RowEndBody)
		$row_fotter = $this->xu_ly_du_lieu_tinh_tong_cuoi_bang($this->numberRowStartBody+1,$this->numberRowStartBody + $index + 1);

		if($this->Tinh_tong_dau_trang) $this->xu_ly_du_lieu_tinh_tong_dau_trang($this->numberRowStartBody+1,$this->numberRowStartBody + $index);
		if($this->Chu_ky_cuoi_bang) $this->hien_thi_chu_ky_cuoi_bang($row_fotter);

	}


	# ------------------------------------------------------------------------------------------------#
								# Phần đầu trang
	# ------------------------------------------------------------------------------------------------#

	public function xu_ly_du_lieu_hien_thi_dau_trang(){

		foreach ($this->dataExcel as $index => $row) {
			foreach ($this->Row_title as $cell) {
				$dong_nao = $cell['dong_nao'];
				$merge = explode(":", $cell['hien_thi']);
				# Merge excel
				$this->oPHPExcel->getActiveSheet()->mergeCells($merge['0'].$dong_nao.':'.$merge['1'].$dong_nao)->setCellValue($merge['2'].$dong_nao,$row[$cell['value_field']]);

			}
		}
	}

	public function xu_ly_du_lieu_tinh_tong_dau_trang($vi_tri_dau=false,$vi_tri_cuoi = false) {
		$dem = 0;
		$end_body = $vi_tri_cuoi;

		foreach ($this->RowEndBody as $cell) {

			$vi_tri_cuoi = $vi_tri_cuoi + $dem;
			$dem ++;
			$merge = explode(":", $cell['hien_thi']);
			# Merge excel
			$this->oPHPExcel->getActiveSheet()->getStyle($merge['0'].($vi_tri_cuoi+2))->getFont()->setBold(true);
			$this->oPHPExcel->getActiveSheet()->mergeCells($merge['0'].$vi_tri_cuoi.':'.$merge['0'].$vi_tri_cuoi)->setCellValue($merge['0'].($vi_tri_cuoi+2),$cell['ten_truong']);

			$this->oPHPExcel->getActiveSheet()->setCellValue($merge['2'].($vi_tri_cuoi+2),"=SUM(".$cell['sum'].$vi_tri_dau.":".$cell['sum'].$end_body.")" );

			$this->oPHPExcel->getActiveSheet()->getStyle($merge['2'].($vi_tri_cuoi+2))->getFont()->setBold(true);

		}
			
		
	}

	# ------------------------------------------------------------------------------------------------#
								# Phần cuối trang
	# ------------------------------------------------------------------------------------------------#
	public function xu_ly_du_lieu_tinh_tong_cuoi_bang_theo_tung_cot($vi_tri_dau=false,$vi_tri_cuoi = false) {
		// echo $vi_tri;
		// die;
		$dem = 0;
		$end_body = $vi_tri_cuoi + 1;
		// echo 'vi_tri_cuoi'.$vi_tri_cuoi;
		foreach ($this->RowEndBody_theo_tung_cot as $cell) {

			// $vi_tri_cuoi = $vi_tri_cuoi + $dem;
			// $dem ++;
			$merge = explode(":", $cell['hien_thi']);
			# Merge excel
			$this->oPHPExcel->getActiveSheet()->getStyle($merge['0'].($vi_tri_cuoi+2))->getFont()->setBold(true);
			$this->oPHPExcel->getActiveSheet()->getStyle($merge['0'].($vi_tri_cuoi+2))->applyFromArray($this->border);
			if(isset($cell['ten_truong'])){
				$this->oPHPExcel->getActiveSheet()->mergeCells($merge['0'].($vi_tri_cuoi+2).':'.$merge['1'].($vi_tri_cuoi+2))->setCellValue($merge['2'].($vi_tri_cuoi+2),$cell['ten_truong']);
				$this->oPHPExcel->getActiveSheet()->getStyle($merge['0'].($vi_tri_cuoi+2).':'.$merge['1'].($vi_tri_cuoi+2))->applyFromArray($this->border);
			break;
			}
			
			$this->oPHPExcel->getActiveSheet()->setCellValue($merge['2'].($vi_tri_cuoi+2),"=SUM(".$cell['sum'].$vi_tri_dau.":".$cell['sum'].$end_body.")" );
			$this->oPHPExcel->getActiveSheet()->getStyle($merge['2'].($vi_tri_cuoi+2))->getFont()->setBold(true);

		}
		return $vi_tri_cuoi+2;
		
	}


	public function xu_ly_du_lieu_tinh_tong_cuoi_bang($vi_tri_dau=false,$vi_tri_cuoi = false) {
									
				 
		$dem = 0;
		$end_body = $vi_tri_cuoi;
									

		foreach ($this->RowEndBody as $cell) {
			$vi_tri_cuoi = $vi_tri_cuoi + 1;
			$dem ++;
			$merge = explode(":", $cell['hien_thi']);
			# Merge excel
			$this->oPHPExcel->getActiveSheet()->getStyle($merge['0'].($vi_tri_cuoi+2))->getFont()->setBold(true);

			// echo $merge['0'].$vi_tri_cuoi.':'.$merge['1'].$vi_tri_cuoi.'<br>';

			$this->oPHPExcel->getActiveSheet()->mergeCells($merge['0'].($vi_tri_cuoi+2).':'.$merge['1'].($vi_tri_cuoi+2))->setCellValue($merge['0'].($vi_tri_cuoi+2),$cell['ten_truong']);
			$this->oPHPExcel->getActiveSheet()->setCellValue($merge['2'].($vi_tri_cuoi+2),"=SUM(".$cell['sum'].$vi_tri_dau.":".$cell['sum'].$end_body.")" );
			$this->oPHPExcel->getActiveSheet()->getStyle($merge['2'].($vi_tri_cuoi+2))->getNumberFormat()->setFormatCode("#,##");
			$this->oPHPExcel->getActiveSheet()->getStyle($merge['2'].($vi_tri_cuoi+2))->getFont()->setBold(true);
		}
		return $vi_tri_cuoi+2;
		
	}

	public function hien_thi_chu_ky_cuoi_bang($row_fotter){
		$dem = 0;
		$vi_tri_cuoi = $row_fotter + 1;

		foreach ($this->Chu_ky_cuoi_bang as $cell) {
			$merge = explode(":", $cell['hien_thi']);
				
			if(isset($cell['chu_ky'])){
				# Tag Name
				$this->oPHPExcel->getActiveSheet()->getStyle($merge['0'].($vi_tri_cuoi+2).':'.$merge['1'].($vi_tri_cuoi+2))->getFont()->setBold(true)->setSize($cell['size1']);
				$this->oPHPExcel->getActiveSheet()->mergeCells($merge['0'].($vi_tri_cuoi+2).':'.$merge['1'].($vi_tri_cuoi+2))->setCellValue($merge['0'].($vi_tri_cuoi+2),$cell['ten_truong']);

				# Ký tên và đóng dấu
				$this->oPHPExcel->getActiveSheet()->getStyle($merge['0'].($vi_tri_cuoi+3).':'.$merge['1'].($vi_tri_cuoi+3))->getFont()->setItalic(true)->setSize($cell['size2']);
				$this->oPHPExcel->getActiveSheet()->mergeCells($merge['0'].($vi_tri_cuoi+3).':'.$merge['1'].($vi_tri_cuoi+3))->setCellValue($merge['0'].($vi_tri_cuoi+3),$cell['chu_ky']);

			} elseif($cell['style'] == 'in_dam'){
					$this->oPHPExcel->getActiveSheet()->getStyle($merge['0'].($vi_tri_cuoi+1).':'.$merge['1'].($vi_tri_cuoi+1))->getFont()->setBold(true)->setSize($cell['size1']);
				$this->oPHPExcel->getActiveSheet()->mergeCells($merge['0'].($vi_tri_cuoi+1).':'.$merge['1'].($vi_tri_cuoi+1))->setCellValue($merge['0'].($vi_tri_cuoi+2),$cell['ten_truong']);
			} else{
				$this->oPHPExcel->getActiveSheet()->getStyle($merge['0'].($vi_tri_cuoi+1).':'.$merge['1'].($vi_tri_cuoi+1))->getFont()->setItalic(true)->setSize($cell['size1']);
				$this->oPHPExcel->getActiveSheet()->mergeCells($merge['0'].($vi_tri_cuoi+1).':'.$merge['1'].($vi_tri_cuoi+1))->setCellValue($merge['0'].($vi_tri_cuoi+1),$cell['ten_truong']);
			}

		}
				
	}

	public function setNumberRowStartBody($numberRow = 1) {
		$this->numberRowStartBody = $numberRow;
		return $this;
	}
	public function Tinh_tong_dau_trang($data = array()) {
		$this->Tinh_tong_dau_trang = $data;
		return $this;
	}

	public function RowEndBody($data = array()) {
		$this->RowEndBody = $data;
		return $this;
	}

	public function RowEndBody_theo_tung_cot($data = array()) {
		$this->RowEndBody_theo_tung_cot = $data;
		return $this;
	}

	public function Chu_ky_cuoi_bang($data = array()) {
		$this->Chu_ky_cuoi_bang = $data;
		return $this;
	}

	public function Row_title($data = array()) {
		$this->Row_title = $data;
		return $this;
	}
	
	public function setExtraData($extraData = []) {
		$this->dataExtra = $extraData;
		return $this;
	}
    
    public function setListName($listName = []) {
		$this->listName = $listName;
		return $this;
	}
	
	protected function buildExtraData() {
		foreach ($this->dataExtra as $cellData) {
			$this->oPHPExcel->getActiveSheet()->setCellValue($cellData['cell'], $cellData['value']);
		}
		return $this;
	}
	
	
	public function setHeaderOfBody($headerOfBody) {
		$this->headerOfBody = $headerOfBody;
		return $this;
	}
	public function setMoreChildData($moreChildData) {
		$this->moreChildData = $moreChildData;
		return $this;
	}
	
	/* Add header col
  * Set value for headerCol;
	* @param headerOfMultiCol
	*/
	public function setHeaderOfCol($headerOfCol) {
		$this->headerOfCol = $headerOfCol;
		return $this;
	}													
	/* Add header child col
  * Add header col in a collapsible rows, using export data to excel with collapsible rows
	* @param headerOfMultiCol
	*/
	public function setHeaderOfChildCol($headerOfChildCol) {
		$this->headerOfChildCol = $headerOfChildCol;
		return $this;
	}
	public function setHeaderOfMoreChildCol($headerOfMoreChildCol) {
		$this->headerOfMoreChildCol = $headerOfMoreChildCol;
		return $this;
	}
	/* Add header multicol
  * Set value for headerOfMultiCol: merge cells and set value ;
	* @param headerOfMultiCol
	*/
  public function setHeaderOfMultiCol($headerOfMultiCol) {
		$this->headerOfMultiCol = $headerOfMultiCol;
		return $this;
	}
	
	
	/* Set Begin row 
  * Use for setting row title of file;
	* @param numberRowBeginRow
	*/
	public function setNumberRowBeginRow($numberRowBeginRow) {
		$this->numberRowBeginRow = $numberRowBeginRow;
		return $this;
	}
	
	


	public function setFieldOfChildBody($fieldOfChildBody) {
		$this->fieldOfChildBody = $fieldOfChildBody;
		return $this;
	}
	public function setMoreFieldOfChildBody($moreFieldOfChildBody) {
		$this->moreFieldOfChildBody = $moreFieldOfChildBody;
		return $this;
	}
	
	//setting strart and end Cell for merging
	public function setMergeCell($mergeCell) {
		$this->mergeCell = $mergeCell;
		return $this;
	}
	
	

	public function border_data() {
		$this->border = true;
		 $styleArray = array(
		      'borders' => array(
		          'allborders' => array(
		              'style' => PHPExcel_Style_Border::BORDER_THIN
		          )
		      )
		  );
		$this->oPHPExcel->getDefaultStyle()->applyFromArray($styleArray);
	}
	protected function applyCellStyle($cellName = '', $styles = array()) {
	
		if (!empty($styles['bold'])) {
			$this->oPHPExcel->getActiveSheet()->getStyle($cellName)
												->getFont()
												->setBold(true);
		}
		if (!empty($styles['font'])) {
			$this->oPHPExcel->getActiveSheet()->getStyle($cellName)
												->getFont()
												->setSize($styles['font_size']);
		}
		if (!empty($styles['is_fill'])) {
			$this->oPHPExcel->getActiveSheet()->getStyle($cellName)
												->getFill()
												->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			
			$this->oPHPExcel->getActiveSheet()->getStyle($cellName)
												->getFill()
												->getStartColor()
												->setRGB($styles['color']);
		}
		return $styles;
	}

}
?>