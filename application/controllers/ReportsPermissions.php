<?php  
require_once (APPPATH . "biz/controllers/BizReports.php");
class ReportsPermissions extends BizReports 
{
	function __construct()
	{
		parent::__construct();
	}
	function view($id=-1)
	{
		$this->check_action_permission('bao_cao_truy_cap');
		$data['employee_all'] = $this->Employee->get_list_employees_by_location();
		// echo "<pre>"; print_r($data['employee_all']); die();
		// if ($this->Employee->has_module_action_permission('reports','bao_cao_truy_cap', $this->Employee->get_logged_in_employee_info()->person_id))
		// {
		
		// }else{
		// 	$this->load->view('reports/contract/not_permission_reports.php');
		// }
		$this->load->view('reports/contract/view_report_permission.php',$data);
		
	}
	function download(){
		$employee_id = $this->input->post('employee_id'); // person_id
		
		if ($employee_id !=null) {
			$data_employees = $this->Employee->get_list_employees_by_location();
			foreach ($data_employees as $key => $value) {
				if ($value['person_id']==$employee_id) {
					$data_employee[] =$data_employees[$key]; 
				}
			}
		}else{
			$data_employee = $this->Employee->get_list_employees_by_location();
		}

		// echo "<pre>"; print_r($data_employee); die();
		$objPHPExcel = new PHPExcel();
		$title ="BÁO CÁO XÁC NHẬN QUYỀN TRUY CẬP HỆ THỐNG";
		$title_top_left1 = "CÔNG TY CHỨNG KHOÁN NGÂN HÀNG TMCP NTVN";
		$style_font_title = array(
			'font' => array(
				'size' => 16,
			)
		);
		$style_center = array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
			),
			'font' => array(
				'size' => 13,
			)
		);
		$style_center_vertical=array(
			'alignment' => array(
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
			),
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
		);
		$style_number =array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
			)
		);
		$style_backgroud=array(
			'background'=>'ccc',
		);
		$style_font_bold =array(
			'font' =>array(
				'bold' => true,
			)
		);
		$thoigian = '..., ngày ... tháng ... năm 20';
		// echo $thoigian; die();
		$objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
		$objPHPExcel->getDefaultStyle()->applyFromArray(array('font'=>array('size'=>13,'name'=>'Times New Roman')));
		$objPHPExcel->getDefaultStyle()->getAlignment()->setWrapText(true);

		$objPHPExcel->getActiveSheet()->mergeCells('A1:C1');
		$objPHPExcel->getActiveSheet()->setCellValue('A1','CÔNG TY CHỨNG KHOÁN NGÂN HÀNG TMCP NTVN');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:C2');
		$objPHPExcel->getActiveSheet()->setCellValue('A2','PHÒNG TIN HỌC');
		$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($style_center);
		$objPHPExcel->getActiveSheet()->getStyle('A2')->applyFromArray($style_center);
		$objPHPExcel->getActiveSheet()->mergeCells('E1:F1');
		$objPHPExcel->getActiveSheet()->setCellValue('E1','CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM');
		$objPHPExcel->getActiveSheet()->mergeCells('E2:F2');
		$objPHPExcel->getActiveSheet()->setCellValue('E2','ĐỘC LẬP-TỰ DO-HẠNH PHÚC');
		$objPHPExcel->getActiveSheet()->mergeCells('E3:F3');
		$objPHPExcel->getActiveSheet()->setCellValue('E3',($thoigian));
		$objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($style_center);
		$objPHPExcel->getActiveSheet()->getStyle('E2')->applyFromArray($style_center);
		$objPHPExcel->getActiveSheet()->getStyle('E3')->applyFromArray($style_center);
		$objPHPExcel->getActiveSheet()->mergeCells('A5:G5');
		$objPHPExcel->getActiveSheet()->setCellValue('A5',($title));
		$objPHPExcel->getActiveSheet()->getStyle('A5')->applyFromArray($style_font_bold);
		$objPHPExcel->getActiveSheet()->getStyle('A5')->applyFromArray($style_center);

		$objPHPExcel->getActiveSheet()->mergeCells('A6:B6');
		$objPHPExcel->getActiveSheet()->setCellValue('A6','Ngày '.date('d').' tháng '.date('m').' năm '.date('Y'));


		
		$title_col =array('User','Họ và Tên','Mã nhân sự','Khu vực','Nhóm phân quyền','Cấu trúc khối tư vấn','Xác nhận');
		$name_col=array('A','B','C','D','E','F','G');
		for ($i=0; $i < count($name_col) ; $i++) { 
			$objPHPExcel->getActiveSheet()->setCellValue($name_col[$i].'9',$title_col[$i]);
			$objPHPExcel->getActiveSheet()->getStyle($name_col[$i].'9')->applyFromArray($style_center);
			$objPHPExcel->getActiveSheet()->getStyle($name_col[$i].'9')->applyFromArray($style_font_bold);
		}
		$row_start=10;
		$count_e = count($data_employee);
		if (!empty($data_employee)) {
			foreach ($data_employee as $key => $value) {
				$data_nv = $this->db->select('*')->from('phppos_employees_locations')->where('employee_id',$value['person_id'])->get()->result_array();
				foreach ($data_nv as $key => $val) {
					$objPHPExcel->getActiveSheet()->setCellValue('A'.$row_start,$value['username']);
					$objPHPExcel->getActiveSheet()->setCellValue('B'.$row_start,$value['employee_name']);
					$objPHPExcel->getActiveSheet()->setCellValue('C'.$row_start,$value['employee_number']);
					$objPHPExcel->getActiveSheet()->getStyle('C'.$row_start)->applyFromArray($style_center);
					$objPHPExcel->getActiveSheet()->setCellValue('D'.$row_start,$this->Location->get_info($val['location_id'])->name);
					$objPHPExcel->getActiveSheet()->setCellValue('E'.$row_start,$this->Group->get_info($value['group_id'])->name);

					$objPHPExcel->getActiveSheet()->setCellValue('F'.$row_start,$this->Department->get_info($value['department_id'])->name);
					$objPHPExcel->getActiveSheet()->setCellValue('G'.$row_start,'');
					$row_start++;
				}
				
			}
		}

		for ($i=9; $i < $row_start; $i++) { 
			// $objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(30);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($style_center_vertical);
			$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->applyFromArray($style_center_vertical);
			$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->applyFromArray($style_center_vertical);
			$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->applyFromArray($style_center_vertical);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->applyFromArray($style_center_vertical);
			$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray($style_center_vertical);
			$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->applyFromArray($style_center_vertical);
			
		}
		
		$sheet1 = $objPHPExcel->createSheet(1);
		$sheet1->getDefaultColumnDimension()->setWidth(20);
		$sheet1->setCellValue('A1','MenuID');
		$sheet1->setCellValue('B1','Danh mục chức năng');
		$modules = $this->get_permission($employee_id);

		$name_col = ['A','B'];
		$name_col_next = ['C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];

		$col = array_merge($name_col,$name_col_next);
		for ($i=0; $i < count($col) ; $i++) { 
			$col1[] = $col[0].''.$col[$i];
		}
		for ($i=0; $i < count($col) ; $i++) { 
			$col2[] = $col[1].''.$col[$i];
		}
		for ($i=0; $i < count($col) ; $i++) { 
			$col3[] = $col[2].''.$col[$i];
		}
		$name_col_all = array_merge($col, $col1, $col2,$col3);
		$name_col_n  = array_merge($name_col_next, $col1, $col2,$col3);

		if (!empty($data_employee)) {
			for ($j=0; $j < $count_e ; $j++) { 
				$sheet1->setCellValue($name_col_n[$j].'1',$data_employee[$j]['username']);
			}
		}
		$row_start=2;
		if ($employee_id!=null) {
			if (!empty($modules)) {
				$stt=1;
				for ($i=0; $i< count($modules); $i++) {
					$sub=1;
					$sheet1->setCellValue('A'.$row_start,$stt);
					$sheet1->setCellValue('B'.$row_start,$modules[$i]['name']);
					if (!empty($data_employee)) {
						for ($a=0; $a < $count_e ; $a++) { 
							if ($modules[$i]['permission']==1) {
								$sheet1->setCellValue($name_col_n[$a].$row_start,'x');
								$sheet1->getStyle($name_col_n[$a].$row_start)->applyFromArray($style_center);
							}
						}
					}
					$sheet1->getStyle('B'.$row_start)->applyFromArray($style_font_bold);
					$sheet1->getStyle('A'.$row_start)->applyFromArray($style_font_bold);
					$row_start++;

					$module_action =  $modules[$i]['module_action'];
					for ($j=0; $j< count($module_action); $j++) {
						$sheet1->setCellValue('A'.$row_start,$stt.'.'.$sub);
						$sheet1->setCellValue('B'.$row_start,$module_action[$j]['name']);
						if (!empty($data_employee)) {
							for ($a=0; $a < $count_e ; $a++) { 
								if ($module_action[$j]['permission']==1) {
									$sheet1->setCellValue($name_col_n[$a].$row_start,'x');
									$sheet1->getStyle($name_col_n[$a].$row_start)->applyFromArray($style_center);
								}
							}
						}
						$sub++;
						$row_start++;
					}
					$stt++;
				}
			}
		}else{
			if (!empty($modules)) {
				$stt=1;
				for ($i=0; $i< count($modules); $i++) {
					$sub=1;
					$sheet1->setCellValue('A'.$row_start,$stt);
					$sheet1->setCellValue('B'.$row_start,$modules[$i]['name']);

					$sheet1->getStyle('B'.$row_start)->applyFromArray($style_font_bold);
					$sheet1->getStyle('A'.$row_start)->applyFromArray($style_font_bold);
					$row_start++;

					$module_action =  $modules[$i]['module_action'];
					for ($j=0; $j< count($module_action); $j++) {
						$sheet1->setCellValue('A'.$row_start,$stt.'.'.$sub);
						$sheet1->setCellValue('B'.$row_start,$module_action[$j]['name']);
						$sub++;
						$row_start++;
					}
					$stt++;
				}
			}

			if (!empty($data_employee)) {
				for ($a=0; $a < $count_e ; $a++) {
					$start=2; 
					$module_e = $this->get_permission($data_employee[$a]['person_id']);
					for ($ii=0; $ii < count($module_e); $ii++) {

						if ($module_e[$ii]['permission']==1) {
							$sheet1->setCellValue($name_col_n[$a].$start,'x');
							$sheet1->getStyle($name_col_n[$a].$start)->applyFromArray($style_font_bold);
							$sheet1->getStyle($name_col_n[$a].$start)->applyFromArray($style_center);
						}

						$start++;
						$module_action_e = $module_e[$ii]['module_action'];
						for ($jj=0; $jj < count($module_action_e); $jj++) { 
							if ($module_action_e[$jj]['permission']==1) {
								$sheet1->setCellValue($name_col_n[$a].$start,'x');
								$sheet1->getStyle($name_col_n[$a].$start)->applyFromArray($style_center);
							}
							$start++;
						}
					}
				}
			}
		}


		for ($i=1; $i <= $row_start ; $i++) { 
			for ($j=0; $j < $count_e+2 ; $j++) { 
				$sheet1->getStyle($name_col_all[$j].$i)->applyFromArray($style_center_vertical);
			}
		}

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename='.$title.'.xls');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}

	function get_permission($id=null){
		$modules = $this->Module->get_all_modules()->result_array();
		foreach ($modules as $key => &$module) {
			$module['permission'] = $this->Employee->has_module_permission($module['module_id'],$id);
			$module['name'] = lang($module['name_lang_key']);
			$module['module_action'] = $this->Module_action->get_module_actions($module['module_id'])->result_array();
			foreach ($module['module_action'] as $key2 => $value2) {
				$module['module_action'][$key2]['permission'] = $this->Employee->has_module_action_permission($module['module_id'],$value2['action_id'],$id);
				$module['module_action'][$key2]['name'] = lang($value2['action_name_key']);
			}
		}
		return $modules;
		// echo "<pre>"; print_r($modules);
	}
}
?>