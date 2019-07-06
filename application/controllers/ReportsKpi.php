<?php
/**
 * 
 */
require_once("Reports.php");
require_once (APPPATH . "biz/controllers/BizReports.php");
class ReportsKpi extends BizReports
{
	function __construct()
	{
// 		error_reporting(-1);
// ini_set('display_errors', 'On');
		parent::__construct();
		$this->load->library('PHPExcel');
	}
	function view($id=-1)
	{
		$id_location 		= $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id())->location_id;
		$code_location 		= $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id())->code;
		$data['employee'] 	= $this->Employee->get_list_employees_by_location($id_location);
		$list_employee 		= $this->Employee->get_list_employees_by_location($id_location);

		$this->check_action_permission('kpi_ca_nhan_theo_du_an');
		$this->load->view('reports/contract/bao_cao_danh_gia_ca_nhan_theo_du_an');
	}
	public function export_data()
	{
		$data['input'] = $this->input->post();
		$data['contract'] = $this->Contract->bao_cao_ca_nhan(array('DTGN'=>true,'PBK'=>true));
		$this->load->view('reports/contract/table_bao_cao_danh_gia_ca_nhan_theo_du_an', $data);
	}
	public function download()
	{
		$objPHPExcel = new PHPExcel();
		$date_start = $this->input->post('date_start');
		$date_end = $this->input->post('date_end');
		$title ="Danh sách KPI đánh giá cá nhân theo dự án từ ngày $date_start đến ngày $date_end";
		$add_location = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id())->name;
		$id_location = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id())->location_id;
		$code_location = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id())->code;
		$employee_logged = $this->Employee->get_logged_in_employee_info();

		
		$list_employee = $this->Employee->get_list_employees_by_location($id_location);

		$date_s = date('Y-m-d',strtotime($date_start));
		$date_e = date('Y-m-d',strtotime($date_end));
		$kpi = $this->Contract->get_ratio_employee_task();


		// echo "<pre>"; print_r($tasks); die();

		if ($employee_logged->group_id==9) {
			$tasks = $this->db->select('t.id as task_id, t.name as name_task,s.sale_id, st.item_name,SUM(IF(pm.vat="published",
				(pm.price / 1.1),pm.price)) as co_vat, ct.id as contract_id, kpi.employee_id')->from('phppos_task_kpiperson_approve kpi')->join('phppos_tasks t','t.id = kpi.task_id')
			->join('phppos_sales s','s.task_id = t.id')
			->join('phppos_sales_items st','st.sale_id = s.sale_id')
			->join('phppos_contract ct','ct.sale_id = s.sale_id','left')
			->join('phppos_contract_payment pm','pm.contract_id = ct.id','left')
			->join('phppos_task_user_relations tu','tu.task_id = t.id','left')
			->where('kpi.history',1)
			->where('s.location_id',$id_location)
			->where('(tu.is_join=1 OR tu.is_implement=1)')
			->where('tu.user_id',$employee_logged->id)
			->where_in('pm.c_status',array('done','liquidated'))
			->where('t.date_start >=',$date_s)
			->where('t.date_start <=',$date_e)
			->group_by('t.id')
			->get()->result_array();
			if (!empty($tasks)) {
				foreach ($tasks as $key => $value) {
					$id = json_decode($value['employee_id']);
					for ($i=0; $i <count($id) ; $i++) { 
						$eid[]= $id[$i];
					}
				}
			}
			if (!empty($eid)) {
				$list_employee = $this->Employee->get_list_employees_by_location(null,null, array('arr_id'=>array_unique($eid)));
			}
			
		}else{
			$tasks = $this->db->select('t.id as task_id, t.name as name_task,s.sale_id, st.item_name,SUM(IF(pm.vat="published",
				(pm.price / 1.1),pm.price)) as co_vat, ct.id as contract_id, kpi.employee_id')->from('phppos_task_kpiperson_approve kpi')->join('phppos_tasks t','t.id = kpi.task_id')
			->join('phppos_sales s','s.task_id = t.id')
			->join('phppos_sales_items st','st.sale_id = s.sale_id')
			->join('phppos_contract ct','ct.sale_id = s.sale_id','left')
			->join('phppos_contract_payment pm','pm.contract_id = ct.id','left')
			->where('kpi.history',1)
			->where('s.location_id',$id_location)
			->where_in('pm.c_status',array('done','liquidated'))
			->where('t.date_start >=',$date_s)
			->where('t.date_start <=',$date_e)
			->group_by('t.id')
			->get()->result_array();
			$list_employee = $this->Employee->get_list_employees_by_location($id_location);
		}

		$contract = $this->Contract->bao_cao_ca_nhan(array('DTGN'=>true,'date_s'=>$date_start,'date_e'=>$date_end,'TD'=>true,'kpi'=>true));
		
		
		$chiphiPBK = $this->db->select('contract_id, sum(expense_amount) as chiphipb')->from('phppos_expenses')->where('deleted',0)->where('contract_id !=','')->group_by('contract_id')->get()->result_array();
		// echo $this->db->last_query(); die();
		// echo "<pre>";
		// print_r($chiphiPBK); die();
		$chiphibenthuba = $this->db->select('r.task_id, sum(rt.item_unit_price) as chiphibenthuba')->from('phppos_receivings r')->join('phppos_receivings_items rt','rt.receiving_id = r.receiving_id')->group_by('r.task_id')->get()->result_array();
		
		// echo "<pre>";
		// print_r($contract); die();
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
		$objPHPExcel->getDefaultStyle()->applyFromArray(array('font'=>array('size'=>13,'name'=>'Times New Roman')));
		$objPHPExcel->getDefaultStyle()->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->mergeCells('B1:H1');
		$objPHPExcel->getActiveSheet()->setCellValue('B1',$title);
		$objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($style_font_bold);
		$objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($style_center);
		$objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($style_font_title);
		$objPHPExcel->getActiveSheet()->setCellValue('E2',$add_location);
		$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($style_center);
		$objPHPExcel->getActiveSheet()->getStyle('E2')->applyFromArray($style_font_bold);
		$name_col =['A','B','C','D','E','F','G'];
		$data_title =[
			'Tên dự án','Tổng doanh thu','Doanh thu chia cho phòng ban khác','Doanh thu thực hiện', 'Chi phí bên thứ ba','Lợi nhuận tạm tính','Hệ số K'
		];
		$objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
		->setWidth(25);
		for ($i=0; $i < count($data_title) ; $i++) { 
			// $objPHPExcel->getActiveSheet()->getColumnDimension($name_col[$i])->setWidth(25);
			$objPHPExcel->getActiveSheet()->mergeCells($name_col[$i].'4:'.$name_col[$i].'5');
			$objPHPExcel->getActiveSheet()->setCellValue($name_col[$i].'4',$data_title[$i]);
			$objPHPExcel->getActiveSheet()->getStyle($name_col[$i].'4')->applyFromArray($style_center);
			$objPHPExcel->getActiveSheet()->getStyle($name_col[$i].'4')->applyFromArray($style_font_bold);
		}
		$name_col_next = ['H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];

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
		
		$total =  count($list_employee);
		
		$row_start=6;
		$tong_doanh_thu=0;
		$tong_doanh_thu_pbk=0;
		$tong_doanh_thu_th=0;
		$tong_btb=0;
		$tong_loi_nhuan=0;

		if (!empty($tasks)) {
			foreach ($tasks as $key => $value) {
				$objPHPExcel->getActiveSheet()->getStyle('A'.$row_start)->getAlignment()->setWrapText(true); 
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$row_start,htmlspecialchars($value['name_task']));
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$row_start,number_format($value['co_vat']));
				$objPHPExcel->getActiveSheet()->getStyle('B'.$row_start)->applyFromArray($style_number);
				$chiphipb=0;
				if (!empty($chiphiPBK)) {
					foreach ($chiphiPBK as $key => $vl) {
						if ($vl['contract_id']==$value['contract_id']) {
							$chiphipb = ($vl['chiphipb']);
						}
					}
				}
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$row_start,number_format($chiphipb));
				$objPHPExcel->getActiveSheet()->getStyle('C'.$row_start)->applyFromArray($style_number);

				$dtth = ($value['co_vat'] - $chiphipb);
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$row_start,number_format($dtth));
				$objPHPExcel->getActiveSheet()->getStyle('D'.$row_start)->applyFromArray($style_number);
				$chiphitb =0;
				if (!empty($chiphibenthuba)) {
					foreach ($chiphibenthuba as $key => $vl) {
						if ($vl['task_id']==$value['task_id']) {
							$chiphitb = $vl['chiphibenthuba'];
						}
					}
				}

				$objPHPExcel->getActiveSheet()->setCellValue('E'.$row_start,number_format($chiphitb));
				$objPHPExcel->getActiveSheet()->getStyle('E'.$row_start)->applyFromArray($style_number);
				$loinhuantamtinh = ($dtth-$chiphitb);
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$row_start,number_format($loinhuantamtinh));
				$objPHPExcel->getActiveSheet()->getStyle('F'.$row_start)->applyFromArray($style_number);

				$objPHPExcel->getActiveSheet()->setCellValue('G'.$row_start,'');
				$tong_doanh_thu+=$value['co_vat'];
				$tong_doanh_thu_pbk+=$chiphipb;
				$tong_doanh_thu_th+=$dtth;
				$tong_btb+=$chiphitb;
				$tong_loi_nhuan+=$loinhuantamtinh ;

				if (!empty($list_employee)) {
					if (!empty($kpi)) {
						for($i=0; $i<$total; $i++) {
							$start = $i*2;
							$end = $start+1;
							foreach ($kpi as $key => $value_kpi) {
								if ($value_kpi['task_id'] == $value['task_id']) {
									$arr_ratio = json_decode($value_kpi['ratio'], true);
									$arr_employee_id =json_decode($value_kpi['employee_id'], true);
									for ($j=0; $j < count($arr_ratio) ; $j++) {
										if ($list_employee[$i]['id']==$arr_employee_id[$j]) {
											$ratio = $arr_ratio[$j];
											$objPHPExcel->getActiveSheet()->setCellValue($name_col_n[$start].$row_start,$ratio);
										}
									}				
								}
							}
						}
					}
				}
				$row_start++;
			}
		}

		$total_col = ($total*2)+6;
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$row_start,'Tổng cộng');
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$row_start,number_format($tong_doanh_thu));
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$row_start,number_format($tong_doanh_thu_pbk));
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$row_start,number_format($tong_doanh_thu_th));
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$row_start,number_format($tong_btb));
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$row_start,number_format($tong_loi_nhuan));
		$objPHPExcel->getActiveSheet()->getStyle('B'.$row_start)->applyFromArray($style_number);
		$objPHPExcel->getActiveSheet()->getStyle('C'.$row_start)->applyFromArray($style_number);

		$objPHPExcel->getActiveSheet()->getStyle('D'.$row_start)->applyFromArray($style_number);

		$objPHPExcel->getActiveSheet()->getStyle('E'.$row_start)->applyFromArray($style_number);

		$objPHPExcel->getActiveSheet()->getStyle('F'.$row_start)->applyFromArray($style_number);

		for($i=0; $i<6;$i++){
			$objPHPExcel->getActiveSheet()->getStyle('A'.$row_start)->applyFromArray($style_center);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$row_start)->applyFromArray($style_font_bold);
		}
		for ($j=0; $j < $total_col+1 ; $j++) { 
		}

		if ($total>0) {
			// echo "<pre>"; print_r($list_employee); die();

			for ($a=0; $a<((int)$total*2) ; $a++) {
				$objPHPExcel->getActiveSheet()->getColumnDimension($name_col_n[$a])->setWidth(20);
			}

			for ($j=0; $j < $total ; $j++) {
				$start = $j*2;
				$end = $start+1;
				$objPHPExcel->getActiveSheet()->mergeCells($name_col_n[$start].'4:'.$name_col_n[$end].'4');

				$objPHPExcel->getActiveSheet()->setCellValue($name_col_n[$start].'4',$list_employee[$j]['employee_name']);
				$objPHPExcel->getActiveSheet()->getStyle($name_col_n[$start].'4')->applyFromArray($style_center);
				$objPHPExcel->getActiveSheet()->getStyle($name_col_n[$start].'4')->applyFromArray($style_font_bold);
			}
			for ($i=0; $i < $total ; $i++) { 
				$start = $i*2;
				$end = $start+1;

				$objPHPExcel->getActiveSheet()->setCellValue($name_col_n[$start].'5','Đóng góp(%)');
				$objPHPExcel->getActiveSheet()->setCellValue($name_col_n[$end].'5','Kpi đóng góp(đồng)');
			}

		}else{
			$total =0;
		}

		
		

		
		// echo "<pre>";
		// print_r($arr_col_new); die();
		for ($i=4; $i < $row_start+1; $i++) { 
			$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(30);
			for ($j=0; $j < $total_col+1 ; $j++) { 
				$objPHPExcel->getActiveSheet()->getStyle($name_col_all[$j].$i)->applyFromArray($style_center_vertical);
			}
		}

		$row_start = $row_start+2;
		$objPHPExcel->getActiveSheet()->mergeCells('F'.$row_start.':G'.$row_start);
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$row_start,'TRƯỞNG PHÒNG TVTCDN - '.$add_location);
		$objPHPExcel->getActiveSheet()->getStyle('F'.$row_start)->applyFromArray($style_font_bold);

		$filename = 'Dánh sách KPI đánh giá cá nhân';
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename='.$filename.'.xls');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}
}
