<?php
/**
 * 
 */
use PhpOffice\PhpWord\Shared\Converter;
require_once("Reports.php");
require_once (APPPATH . "biz/controllers/BizReports.php");
require_once ("Kpi_ratings.php");
class ReportInternal extends BizReports
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('Kpi_Person');
	}
	// BIÊN BẢN PHÂN CHIA DOANH THU NỘI BỘ
	function view($id=-1)
	{
		$employee_logged = $this->Employee->get_logged_in_employee_info();

		$id_location = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id())->location_id;
		$contract_kpi = $this->db->select('s.task_id, s.sale_id')
		->from('phppos_task_kpiperson_approve kpi')
		->join('phppos_sales s','s.task_id = kpi.task_id')
		->where('kpi.history',1)
		->group_by('kpi.task_id')
		->get()->result_array();
		
		if (!empty($contract_kpi)) {
			foreach ($contract_kpi as $key => $value) {
				$task_idkpi[] = $value['task_id'];
			}
		}

		if ($employee_logged->group_id==9) {
			$data_task = $this->db->select('s.sale_id, s.task_id, tu.user_id, tu.is_implement, tu.is_join, ct.id, ct.name, ct.code')->from('phppos_sales s')->join('phppos_task_user_relations tu','s.task_id = tu.task_id')->join('phppos_contract ct','ct.sale_id = s.sale_id')->join('phppos_task_kpiperson_approve ap','s.task_id = ap.task_id')->where('(tu.is_implement=1 OR tu.is_join=1)')->where('s.location_id',$id_location)->get()->result_array();
			for ($i=0; $i < count($data_task) ; $i++) { 
				if ($data_task[$i]['user_id']==$employee_logged->id) {
					$data['all_contract'][] = $data_task[$i];
				}
			}
			// echo "<pre>"; print_r($data['all_contract']); die();
		}else{
			$data['all_contract'] = $this->db->select('ct.id, ct.name,ct.code')->from('phppos_contract ct')
			->join('phppos_sales s','s.sale_id = ct.sale_id')
			->where('location_id',$id_location)
			->where_in('s.task_id',$task_idkpi)
			->get()->result_array();
		}
		
		if ($this->Employee->has_module_action_permission('reports','phan_chia_doanh_thu_theo_hd', $this->Employee->get_logged_in_employee_info()->person_id))
		{
			$this->load->view('reports/contract/bao_cao_doanh_thu_noi_bo',$data);
		}else{
			$this->load->view('reports/contract/not_permission_reports.php');
		}
	}

	function export_data()
	{
		$contract_id = $this->input->post('contract_id');
		$contract= $this->db->select('it.name as name_item,ct.name as name_contract,ct.code as code_contract,s.task_id')
		->from('phppos_contract ct')
		->join('phppos_items it','ct.item_id = it.item_id')
		->join('phppos_sales s','s.sale_id = ct.sale_id')
		->where('ct.id',$contract_id)
		->get()
		->row();

		$data['contract_code'] =$contract->code_contract;
		$data['contract_item'] =$contract->name_item;
		$data['check'] = 'DTNB';
		$data['datetime'] = 'ngày '.Date('d').' tháng '.Date('m').' năm '.Date('Y');
		$data['location'] = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id());
		
		$data['nguoi_tham_gia_da'] =$this->Kpi_Person->get_employee_join_tasks(array('task_id'=>$contract->task_id))->where('(tu.is_join=1 OR tu.is_implement=1)')->group_by('e.id')->get()->result_array();
		$data['task_id']= $contract->task_id;
		$data['kpi'] = $this->Contract->get_ratio_employee_task();
		$this->load->view('reports/contract/table_report',$data);
	}
	function exportWord()
	{
		$contract_id = $this->input->post('contract_id');
		$contract= $this->db->select('it.name as name_item,ct.name as name_contract,ct.code as code_contract,s.task_id')
		->from('phppos_contract ct')
		->join('phppos_items it','ct.item_id = it.item_id')
		->join('phppos_sales s','s.sale_id = ct.sale_id')
		->where('ct.id',$contract_id)
		->get()
		->row();
		$location = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id());
		
		$contract_code =$contract->code_contract;
		$contract_item =$contract->name_item;
		$contract_add = $location->address;
		$location_code =$location->code;
		$datetime = 'ngày '.Date('d').' tháng'.Date('m').' năm '.Date('Y');
		
		$nguoi_tham_gia_da =$this->Kpi_Person->get_employee_join_tasks(array('task_id'=>$contract->task_id))->where('(tu.is_join=1 OR tu.is_implement=1)')->group_by('e.id')->get()->result_array();
		$task_id= $contract->task_id;
		$kpi = $this->Contract->get_ratio_employee_task();

		$style_header = array(
			'font'=>true, 
			'size'=>16,
		);
		$phpWord = new \PhpOffice\PhpWord\PhpWord();
		$phpWord->getCompatibility()->setOoxmlVersion(14);
		$phpWord->getCompatibility()->setOoxmlVersion(15);
		$section = $phpWord->addSection();
		$phpWord->setDefaultFontName('Times New Roman');
		$phpWord->setDefaultFontSize(12);
		
		$phpWord->addParagraphStyle('pStyle', ['align' => 'center', 'spaceAfter' => 100]);
		$phpWord->addFontStyle('StyleHeader', ['bold' => true, 'size' => 16]);
		$phpWord->addFontStyle('hStyle', ['bold' => true, 'size' => 13]);
		$styleTable = ['borderSize' => 6, 'borderColor' => '006699', 'cellMargin' => 80];
		$phpWord->addFontStyle('center', ['align' => 'center']);
		$fontStyle_thead = ['bold' => true, 'align' => 'center'];
		$fontStyle = ['align' => 'center'];
		$phpWord->addTableStyle('table_header',null,null);
		$table_header = $section->addTable('table_header');
		$table_header->addRow();
		$table_header->addCell(6000)->addText('CÔNG TY CHỨNG KHOÁN NHTMCPNTVN', $fontStyle_thead);
		$table_header->addCell(6000)->addText('CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM', $fontStyle_thead);
		$table_header->addRow();
		$table_header->addCell(6000)->addText('PHÒNG TVTCDN-'.$location->code, $fontStyle_thead);
		$table_header->addCell(6000)->addText('Độc lập - Tự do - Hạnh phúc', $fontStyle_thead);
		$table_header->addRow();
		$table_header->addCell(6000)->addText('V/v: Phân chia doanh thu nội bộ', array('align' => 'center'));
		$current = $location->name.', '.$datetime;
		$table_header->addCell(6000)->addText($current);


		$section->addTextBreak();
		$section->addTextBreak();
		$section->addText('BIÊN BẢN PHÂN CHIA DOANH THU NỘI BỘ', 'StyleHeader', 'pStyle');
		$h1 = "Hợp đồng: $contract_item , số: $contract_code";
		$section->addText($h1,'hStyle', 'pStyle');
		$section->addTextBreak();
		$ka = "Kính gửi: Trưởng phòng TVTCDN- $contract_code";
		$section->addText($ka,'hStyle', 'pStyle');
		$section->addText('Căn cứ vào sự đóng góp của từng người vào việc thực hiện hợp đồng số '.$contract_code.', chúng tôi đã thống nhất phân chia doanh thu như sau:');

		$phpWord->addTableStyle('table1', $styleTable,null);
		$table1 = $section->addTable('table1');
		$table1->addRow();
		$table1->addCell(2000)->addText('STT', $fontStyle_thead);
		$table1->addCell(6000)->addText('Họ và tên', $fontStyle_thead);
		$table1->addCell(6000)->addText('Tỷ lệ doanh thu', $fontStyle_thead);
		$table1->addRow();
		$table1->addCell(2000)->addText('', $fontStyle_thead);
		$table1->addCell(6000)->addText('Người phụ trách', $fontStyle_thead);
		$table1->addCell(6000)->addText('', $fontStyle_thead);
		$sum1 =0; 
		if (!empty($nguoi_tham_gia_da)) {
			$stt=1;
			foreach ($nguoi_tham_gia_da as $key => $value) {
				if ($value['is_implement']==1) {
					$table1->addRow();
					$table1->addCell(2000)->addText($stt, $fontStyle);
					if ($value['first_name']==null) {
						$table1->addCell(6000)->addText($value['last_name'], $fontStyle);
					}else{
						$table1->addCell(6000)->addText($value['first_name'], $fontStyle);
					}
					$kpi_implement =0;
					$employee_id = $value['employee_id'];
					if (!empty($kpi)) {
						foreach ($kpi as $key => $value_kpi) {
							if ($value_kpi['task_id'] == $task_id) {
								$arr_ratio = json_decode($value_kpi['ratio'],true);
								$arr_employeeID = json_decode($value_kpi['employee_id'],true);
								for($i=0;$i<count($arr_employeeID);$i++){
									if ($arr_employeeID[$i] == $employee_id) {
										$kpi_implement =$arr_ratio[$i];
										$sum1 +=$kpi_implement;
									}

								}
							}
						}

					}
					$table1->addCell(6000)->addText($kpi_implement, $fontStyle);
				}
				$stt++;
			}
		}
		$table1->addRow();
		$table1->addCell(2000)->addText('', $fontStyle_thead);
		$table1->addCell(6000)->addText('Người tham gia', $fontStyle_thead);
		$table1->addCell(6000)->addText('', $fontStyle_thead);
		if (!empty($nguoi_tham_gia_da)) {
			$dem=1;
			$sum2=0;
			foreach ($nguoi_tham_gia_da as $key => $value) {
				if ($value['is_join']==1) {
					$table1->addRow();


					$table1->addCell(2000)->addText($dem, $fontStyle);
					
					if ($value['first_name']==null) {
						$table1->addCell(6000)->addText($value['last_name'], $fontStyle);
					}else{$table1->addCell(6000)->addText($value['first_name'], $fontStyle);}
					
					$sum_kpi_join =0;
					$kpi_join =0;
					$employee_id = $value['employee_id'];
					if (!empty($kpi)) {
						foreach ($kpi as $key => $value_kpi) {
							if ($value_kpi['task_id'] == $task_id) {
								$arr_ratio = json_decode($value_kpi['ratio'],true);
								$arr_employeeID = json_decode($value_kpi['employee_id'],true);
								for($i=0;$i<count($arr_employeeID);$i++){
									if ($arr_employeeID[$i] == $employee_id) {
										$kpi_join = $arr_ratio[$i];
										$sum2+= $kpi_join;
									}

								}
							}
						}
					}
					$table1->addCell(6000)->addText($kpi_join, $fontStyle);
					$dem++;
				}
				
			}
		}
		
		$table1->addRow();
		$table1->addCell(2000)->addText('', $fontStyle_thead);
		$table1->addCell(6000)->addText('Tổng cộng', $fontStyle_thead);
		$table1->addCell(6000)->addText(($sum1+$sum2), $fontStyle_thead);

		$section->addText("Kính trình Trưởng phòng TVTCDN-$location_code phê duyệt, làm căn cứ để triển khai đánh giá hiệu quả kinh doanh của nhân viên theo đúng quy định.");
		$phpWord->addTableStyle('table2',null,null);
		$table2 = $section->addTable('table2');
		$table2->addRow();
		foreach ($nguoi_tham_gia_da as $key => $value) {
			if ($value['is_implement']==1) {
				$table2->addCell(3000)->addText('Người phụ trách', $fontStyle_thead);
			}
			if ($value['is_join']==1) {
				$table2->addCell(3000)->addText('Người tham gia', $fontStyle_thead);
			}
		}
		$table2->addRow();
		$table2->addCell(3000)->addText('');
		$table2->addCell(3000)->addText('');
		$table2->addRow();
		$table2->addCell(3000)->addText('');
		$table2->addCell(3000)->addText('');
		$table2->addRow();
		foreach ($nguoi_tham_gia_da as $key => $value) {

			if ($value['is_implement']==1) {
				if ($value['first_name']==null) {
					$table2->addCell(3000)->addText($value['last_name'],$fontStyle_thead);
				}else{$table2->addCell(3000)->addText($value['first_name'],$fontStyle_thead);}
			}
			if ($value['is_join']==1) {
				if ($value['first_name']==null) {
					$table2->addCell(3000)->addText($value['last_name'],$fontStyle_thead);
				}else{$table2->addCell(3000)->addText($value['first_name'],$fontStyle_thead);}
			}
		}

		
		$section->addText("Ý kiến Trưởng phòng TVTCDN-".mb_strtoupper($location->name));
		$section->addText('…………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………',$fontStyle_thead);

		$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
		$filename = 'Biên bản phân chia doanh thu nội bộ.docx';
		$objWriter->save($filename);
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.$filename);
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . filesize($filename));
		flush();
		readfile($filename);
		unlink($filename); 
		exit;
	}
}
