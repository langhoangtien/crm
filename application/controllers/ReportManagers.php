<?php
/**
 * 
 */
use PhpOffice\PhpWord\Shared\Converter;
require_once("Reports.php");
require_once (APPPATH . "biz/controllers/BizReports.php");
require_once ("Kpi.php");
class ReportManagers extends Kpi
{
	function __construct()
	{
// 		error_reporting(-1);
// ini_set('display_errors', 'On');
		parent::__construct();
		$this->load->model('Ratings');
		$this->load->model('Kpi_Person');
		$this->load->library('PHPWord');
	}
	function view($id=-1)
	{
		$id_location = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id())->location_id;

		$view_data = $this->Ratings->view_data(2018,1,'DDT','KH');
		$view_row = array(
			'TDT'=>'Tổng doanh thu',
			'DTCPB'=>'Doanh thu chia cho phòng ban khác',
			'DTTH'=>'Doanh thu thực hiện',
			'CPHBTB'=>'Chi phí bên thứ ba',
			'CPC'=>'Chi phí chung',
			'LNTH'=>'Lợi nhuận thực hiện',
			'LNKH'=>'Lợi nhuân kế hoạch',
			'THKH'=>'TH/KH(%)',
			'DTLNTH'=>'Điểm thu lợi nhuận thực hiện(*)',
			'CPLCB'=>'Chi phí lương cơ bản',
			'LNR'=>'Lợi nhuận ròng(trừ cả lương cơ bản)',
		);
		$location = $this->Location->list_item();
		$data['location_id'] = $id_location;
		// $data['task_all'] = $this->Contract->bao_cao_ca_nhan(array('check'=>true));
		// echo "<pre>"; print_r($data['task_all']); die();
		
		if ($this->Employee->has_module_action_permission('reports','bc_quan_tri_tong_the', $this->Employee->get_logged_in_employee_info()->person_id))
		{
			$this->load->view('reports/contract/bao_cao_quan_tri_tong_the',$data);
		}else{
			$this->load->view('reports/contract/not_permission_reports.php');
		}
	}
	function export_data()
	{
		$month = $this->input->post('month');
		$year  =  $this->input->post('year');
		$check =  $this->input->post('check');
		$arrParam['month'] = $month;
		$arrParam['year'] = $year;
		$arrParam['check'] = 'BCP';
		$arrParam['check_tp'] = $check;
		$arrParam['contract_completed'] =true;
		$data['contract_complete'] = $this->Contract->bao_cao_ca_nhan($arrParam);

		// MUC I.2
		$data['task_all'] = $this->Contract->bao_cao_ca_nhan(array('check'=>true));

		$data['du_an_hoan_thanh'] =$this->Contract->bao_cao_ca_nhan(array('check_tp'=>$check,'check'=>'BCP','month'=>$month,'year'=>$year,'hoanthanh'=>true));
		$data['giai_doan_dang_thuc_hien'] = $this->db->select('*')->from('phppos_tasks')->where('trangthai',1)->get()->result_array();
		$data['cong_viec_dang_thuc_hien'] =$this->Contract->cong_viec_dang_thuc_hien();
		$data['task_child_delay'] = $this->Contract->get_task_child_delay();


		$view_row = array(
			'TDT'=>'Tổng doanh thu',
			'DTCPB'=>'Doanh thu chia cho phòng ban khác',
			'DTTH'=>'Doanh thu thực hiện',
			'CPHBTB'=>'Chi phí bên thứ ba',
			'CPC'=>'Chi phí chung',
			'LNTH'=>'Lợi nhuận thực hiện',
			'LNKH'=>'Lợi nhuân kế hoạch',
			'THKH'=>'TH/KH(%)',
			'DTLNTH'=>'Điểm thu lợi nhuận thực hiện(*)',
			'CPLCB'=>'Chi phí lương cơ bản',
			'LNR'=>'Lợi nhuận ròng(trừ cả lương cơ bản)',
		);
		$location = $this->Location->list_item();
		if ($check==='QUY') {
			$view_data = $this->Ratings->view_data($year,$month,'DDT','KH');
			$data['doanhthu_kpi'] = json_decode($view_data['data_room_rate'],true);
		}
		$id_location = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id())->location_id;
		$data['name_location']= $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id())->name;
		$data['location_id'] =$id_location;
		$data['check'] ='BCQT';
		$data['check_tp']= $check;
		$data['datetime'] = $this->input->post();
		$this->load->view('reports/contract/table_report',$data);
	}
	function download()
	{
		$month = $this->input->post('input_month');
		$year  =  $this->input->post('input_year');
		$check =  $this->input->post('check');
		$arrParam['month'] = $month;
		$arrParam['year'] = $year;
		$arrParam['check'] = 'BCP';
		$arrParam['check_tp'] = $check;
		$arrParam['contract_completed'] =true;
		$contract_complete = $this->Contract->bao_cao_ca_nhan($arrParam);

		// MUC I.2
		$task_all = $this->Contract->bao_cao_ca_nhan(array('check'=>true));

		$du_an_hoan_thanh =$this->Contract->bao_cao_ca_nhan(array('check_tp'=>$check,'check'=>'BCP','month'=>$month,'year'=>$year,'hoanthanh'=>true));
		$giai_doan_dang_thuc_hien = $this->db->select('*')->from('phppos_tasks')->where('trangthai',1)->get()->result_array();
		$cong_viec_dang_thuc_hien =$this->Contract->cong_viec_dang_thuc_hien();
		$task_child_delay = $this->Contract->get_task_child_delay();

		$location = $this->Location->list_item();
		if ($check==='QUY') {
			$view_data = $this->Ratings->view_data($year,$month,'DDT','KH');
			$doanhthu_kpi = json_decode($view_data['data_room_rate'],true);
		}
		$id_location = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id())->location_id;
		$name_location= $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id())->name;


		if ($check=='QUY') {
			$title1= "I. Tình hình thực hiện các dự án, công việc trong quý $month - năm $year";
			$title ="BÁO CÁO QUẢN TRỊ TỔNG THỂ QUÝ $month - NĂM $year";
		}elseif ($check=='THANG') {
			if ($month!=0) {
				$title1= "I. Tình hình thực hiện các dự án, công việc trong tháng $month - NĂM $year";
				$title ="BÁO CÁO QUẢN TRỊ TỔNG THỂ THÁNG $month - năm $year";
			}else{
				$title1= "I. Tình hình thực hiện các dự án, công việc năm $year";
				$title ="BÁO CÁO QUẢN TRỊ TỔNG THỂ NĂM $year";
			}
		}else{
			$title1= "I. Tình hình thực hiện các dự án, công việc năm $year";
			$title ="BÁO CÁO QUẢN TRỊ TỔNG THỂ NĂM $year";
		}
		if ($check=='THANG') {
			$date= date_create($year.'-'.$month);
			$date_e = date_format($date,"Y-m-t");
			$date_s = date_format($date,"Y-m-d");
		}elseif ($check=='QUY') {
			switch ($month) {
				case 1:
				$date_ss = date_create($year.'-01');
				$date = date_create($year.'-03');
				break;
				case 2:
				$date_ss = date_create($year.'-04');
				$date = date_create($year.'-06');
				break;
				case 3:
				$date_ss = date_create($year.'-07');
				$date = date_create($year.'-09');
				break;
				case 4:
				$date_ss = date_create($year.'-10');
				$date = date_create($year.'-12');
				break;

			}
			$date_e = date_format($date,"Y-m-t");
			$date_s = date_format($date_ss,"Y-m-d");
		}else{
			$date = date_create($year.'-12');
			$date_ss = date_create($year.'-01');
			$date_e = date_format($date,"Y-m-t");
			$date_s = date_format($date_ss,"Y-m-d");
		}
		$view_row = array(
			'TDT'=>'Tổng doanh thu',
			'DTCPB'=>'Doanh thu chia cho phòng ban khác',
			'DTTH'=>'Doanh thu thực hiện',
			'CPHBTB'=>'Chi phí bên thứ ba',
			'CPC'=>'Chi phí chung',
			'LNTH'=>'Lợi nhuận thực hiện',
			'LNKH'=>'Lợi nhuân kế hoạch',
			'THKH'=>'TH/KH(%)',
			'DTLNTH'=>'Điểm thu lợi nhuận thực hiện(*)',
			'CPLCB'=>'Chi phí lương cơ bản',
			'LNR'=>'Lợi nhuận ròng(trừ cả lương cơ bản)',
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
		$section->addText($title, 'StyleHeader', 'pStyle');

		$styleTable = ['borderSize' => 6, 'borderColor' => '006699', 'cellMargin' => 80];
		$styleFirstRow = ['borderBottomSize' => 5];
		$styleCell = ['valign' => 'center'];
		$fontStyle_thead = ['bold' => true, 'align' => 'center'];
		$fontStyle = ['align' => 'center'];

		$section->addText($title1, null, 'hStyle');
		$section->addText('1. Thông tin các dự án, công việc đang thực hiện ', null, 'hStyle');
		$phpWord->addTableStyle('table1', $styleTable,null);
		$table1 = $section->addTable('table1');
		$table1->addRow();
		$table1->addCell(500)->addText('STT', $fontStyle_thead);
		$table1->addCell(2000)->addText('Tên dự án', $fontStyle_thead);
		$table1->addCell(2000)->addText('Hợp đồng liên quan', $fontStyle_thead);
		$table1->addCell(2000)->addText('Loại dịch vụ', $fontStyle_thead);
		$table1->addCell(2000)->addText('Người phụ trách', $fontStyle_thead);
		$table1->addCell(2000)->addText('Người tham gia', $fontStyle_thead);
		$table1->addCell(2000)->addText('Tình trạng của dự án', $fontStyle_thead);	
		if (!empty($task_all)) 
		{
			$stt =1;
			foreach ($task_all as $key => $value) {
				$data['nguoi_tham_gia_da'] =$this->Kpi_Person->get_employee_join_tasks(array('task_id'=>$value['task_id']))->where('(tu.is_join=1 OR tu.is_implement=1)')->group_by('e.id')->get()->result_array();
				$date_finish = strtotime($value['date_finish']);
				$date_end = strtotime($value['date_end']);
				$date_start = strtotime($value['date_start']);
				$date_pheduyet =  strtotime($value['date_pheduyet']);
				if ($value['pheduyet']!=1) {
					if (($date_start - strtotime($date_e))<=0) {
						$nguoi_tham_gia ='';
						$nguoi_phu_trach ='';
						if (!empty($data['nguoi_tham_gia_da'])) {
							foreach ($data['nguoi_tham_gia_da'] as $key => $valpt) {
								if ($valpt['is_implement']==1) {
									$nguoi_phu_trach .= '- '.$valpt['username'].', ';
								}else{
									$nguoi_tham_gia .= '- '.$valpt['username'].', ';
								}
							}
						}
						$table1->addRow();
						$table1->addCell(500)->addText($stt, $fontStyle);
						$table1->addCell(2000)->addText(htmlspecialchars($value['name_task']), $fontStyle);
						$table1->addCell(2000)->addText(htmlspecialchars($value['name_contract']), $fontStyle);
						$table1->addCell(2000)->addText(htmlspecialchars($value['ten_dv']), $fontStyle);
						$table1->addCell(2000)->addText(htmlspecialchars($nguoi_phu_trach), $fontStyle);
						$table1->addCell(2000)->addText(htmlspecialchars($nguoi_tham_gia), $fontStyle);
						$table1->addCell(2000)->addText('Đang thực hiện ('.round($value['progress'],2).'%)', $fontStyle);
						$stt++;
					}
				}elseif ($value['pheduyet']==1) {
					if (($date_pheduyet - strtotime($date_e))<=0 && ($date_pheduyet - strtotime($date_s)) >=0) {
						$nguoi_tham_gia ='';
						$nguoi_phu_trach ='';
						if (!empty($data['nguoi_tham_gia_da'])) {
							foreach ($data['nguoi_tham_gia_da'] as $key => $valpt) {
								if ($valpt['is_implement']==1) {
									$nguoi_phu_trach .= '- '.$valpt['username'].', ';
								}else{
									$nguoi_tham_gia .= '- '.$valpt['username'].', ';
								}
							}
						}
						$table1->addRow();
						$table1->addCell(500)->addText($stt, $fontStyle);
						$table1->addCell(2000)->addText(htmlspecialchars($value['name_task']), $fontStyle);
						$table1->addCell(2000)->addText(htmlspecialchars($value['name_contract']), $fontStyle);
						$table1->addCell(2000)->addText(htmlspecialchars($value['ten_dv']), $fontStyle);
						$table1->addCell(2000)->addText(htmlspecialchars($nguoi_phu_trach), $fontStyle);
						$table1->addCell(2000)->addText(htmlspecialchars($nguoi_tham_gia), $fontStyle);
						$table1->addCell(2000)->addText('Đã hoàn thành ('.round($value['progress'],2).'%)', $fontStyle);
						$stt++;
					}
					if (strtotime($date_e)-$date_start>=0 && $date_pheduyet-strtotime($date_e)>=0) {
						$nguoi_tham_gia ='';
						$nguoi_phu_trach ='';
						if (!empty($data['nguoi_tham_gia_da'])) {
							foreach ($data['nguoi_tham_gia_da'] as $key => $valpt) {
								if ($valpt['is_implement']==1) {
									$nguoi_phu_trach .= '- '.$valpt['username'].', ';
								}else{
									$nguoi_tham_gia .= '- '.$valpt['username'].', ';
								}
							}
						}
						$table1->addRow();
						$table1->addCell(500)->addText($stt, $fontStyle);
						$table1->addCell(2000)->addText(htmlspecialchars($value['name_task']), $fontStyle);
						$table1->addCell(2000)->addText(htmlspecialchars($value['name_contract']), $fontStyle);
						$table1->addCell(2000)->addText(htmlspecialchars($value['ten_dv']), $fontStyle);
						$table1->addCell(2000)->addText(htmlspecialchars($nguoi_phu_trach), $fontStyle);
						$table1->addCell(2000)->addText(htmlspecialchars($nguoi_tham_gia), $fontStyle);
						$table1->addCell(2000)->addText('Đang thực hiện ('.round($value['progress'],2).'%)', $fontStyle);
						$stt++;
					}
				}
			}
		}

		$section->addText('2. Tình hình hoàn thành các dự án, công việc ', null, 'hStyle');
		$phpWord->addTableStyle('table2', $styleTable,null);
		$table2 = $section->addTable('table2');
		$table2->addRow();
		$table2->addCell(500)->addText('STT', $fontStyle_thead);
		$table2->addCell(2000)->addText('Tên dự án', $fontStyle_thead);
		$table2->addCell(2000)->addText('Giai đoạn đang thực hiện', $fontStyle_thead);
		$table2->addCell(2000)->addText('Công việc đang thực hiện', $fontStyle_thead);
		$table2->addCell(2000)->addText('Tỷ lệ hoàn thành dự án', $fontStyle_thead);
		$table2->addCell(2000)->addText('Phần trăm công việc thực hiện chậm', $fontStyle_thead);
		
		if (!empty($task_all))
		{
			$stt=1; 
			foreach ($task_all as $key => $value)
			{
				$date_finish = strtotime($value['date_finish']);
				$date_end = strtotime($value['date_end']);
				$date_start = strtotime($value['date_start']);
				$date_pheduyet =  strtotime($value['date_pheduyet']);
				if ($value['pheduyet']!=1) {
					if (($date_start - strtotime($date_e))<=0) {
						$giai_doan_th ='';
						if (!empty($giai_doan_dang_thuc_hien)) {
							foreach ($giai_doan_dang_thuc_hien as $key => $value_gd) {
								if ($value_gd['parent'] == $value['task_id']) {
									$giai_doan_th .=  '- '.$value_gd['name'].', ';
								}
							}
						}
						$cv_th ='';
						if (!empty($cong_viec_dang_thuc_hien)) {
							foreach ($cong_viec_dang_thuc_hien as $key => $value_cv) {
								if ($value_cv['project_id']==$value['task_id']) {
									$cv_th .= '- '.$value_cv['name'].', ';
								}
							}
						}
						
						$cv_delay='';
						if (!empty($task_child_delay)) {
							foreach ($task_child_delay as $key => $value_delay) {
								if ($value_delay['project_id'] == $value['task_id']) {
									if ($value_delay['progress']==100) {
										if ((strtotime($value_delay['date_finish'])-strtotime($value_delay['date_end']))>0) {
											$cv_delay .= '- '. $value_delay['name'].' ('.round($value_delay['progress'],2) .'%), ';
										}
									}else{
										if ((strtotime($value_delay['date_end'])) - (strtotime(Date('Y-m-d')))<0) {
											$cv_delay .='- '. $value_delay['name'].' ('.round($value_delay['progress'],2) .'%), ';
										}
									}
								}
							}
						}
						$table2->addRow();
						$table2->addCell(500)->addText($stt, $fontStyle);
						$table2->addCell(2000)->addText(htmlspecialchars($value['name_task']), $fontStyle);
						$table2->addCell(2000)->addText(htmlspecialchars($gd_thuc_hien), $fontStyle);
						$table2->addCell(2000)->addText(htmlspecialchars($cv_th), $fontStyle);
						$table2->addCell(2000)->addText(round($value['progress'],2).'%', $fontStyle);
						$table2->addCell(2000)->addText(htmlspecialchars($cv_delay), $fontStyle);
						$stt++; 
					}
				}elseif($value['pheduyet']==1){
					if (($date_pheduyet - strtotime($date_e))<=0 && ($date_pheduyet - strtotime($date_s)) >=0) {
						$giai_doan_th ='';
						if (!empty($giai_doan_dang_thuc_hien)) {
							foreach ($giai_doan_dang_thuc_hien as $key => $value_gd) {
								if ($value_gd['parent'] == $value['task_id']) {
									$giai_doan_th .=  '- '.$value_gd['name'].', ';
								}
							}
						}
						$cv_th ='';
						if (!empty($cong_viec_dang_thuc_hien)) {
							foreach ($cong_viec_dang_thuc_hien as $key => $value_cv) {
								if ($value_cv['project_id']==$value['task_id']) {
									$cv_th .= '- '.$value_cv['name'].', ';
								}
							}
						}
						
						$cv_delay='';
						if (!empty($task_child_delay)) {
							foreach ($task_child_delay as $key => $value_delay) {
								if ($value_delay['project_id'] == $value['task_id']) {
									if ($value_delay['progress']==100) {
										if ((strtotime($value_delay['date_finish'])-strtotime($value_delay['date_end']))>0) {
											$cv_delay .= '- '. $value_delay['name'].' ('.round($value_delay['progress'],2) .'%), ';
										}
									}else{
										if ((strtotime($value_delay['date_end'])) - (strtotime(Date('Y-m-d')))<0) {
											$cv_delay .='- '. $value_delay['name'].' ('.round($value_delay['progress'],2) .'%), ';
										}
									}
								}
							}
						}
						$table2->addRow();
						$table2->addCell(500)->addText($stt, $fontStyle);
						$table2->addCell(2000)->addText(htmlspecialchars($value['name_task']), $fontStyle);
						$table2->addCell(2000)->addText(htmlspecialchars($gd_thuc_hien), $fontStyle);
						$table2->addCell(2000)->addText(htmlspecialchars($cv_th), $fontStyle);
						$table2->addCell(2000)->addText(round($value['progress'],2).'%', $fontStyle);
						$table2->addCell(2000)->addText(htmlspecialchars($cv_delay), $fontStyle);
						$stt++;
					}
					if (strtotime($date_e)-$date_start>=0 && $date_pheduyet-strtotime($date_e)>=0) {
						$giai_doan_th ='';
						if (!empty($giai_doan_dang_thuc_hien)) {
							foreach ($giai_doan_dang_thuc_hien as $key => $value_gd) {
								if ($value_gd['parent'] == $value['task_id']) {
									$giai_doan_th .=  '- '.$value_gd['name'].', ';
								}
							}
						}
						$cv_th ='';
						if (!empty($cong_viec_dang_thuc_hien)) {
							foreach ($cong_viec_dang_thuc_hien as $key => $value_cv) {
								if ($value_cv['project_id']==$value['task_id']) {
									$cv_th .= '- '.$value_cv['name'].', ';
								}
							}
						}
						
						$cv_delay='';
						if (!empty($task_child_delay)) {
							foreach ($task_child_delay as $key => $value_delay) {
								if ($value_delay['project_id'] == $value['task_id']) {
									if ($value_delay['progress']==100) {
										if ((strtotime($value_delay['date_finish'])-strtotime($value_delay['date_end']))>0) {
											$cv_delay .= '- '. $value_delay['name'].' ('.round($value_delay['progress'],2) .'%), ';
										}
									}else{
										if ((strtotime($value_delay['date_end'])) - (strtotime(Date('Y-m-d')))<0) {
											$cv_delay .='- '. $value_delay['name'].' ('.round($value_delay['progress'],2) .'%), ';
										}
									}
								}
							}
						}
						$table2->addRow();
						$table2->addCell(500)->addText($stt, $fontStyle);
						$table2->addCell(2000)->addText(htmlspecialchars($value['name_task']), $fontStyle);
						$table2->addCell(2000)->addText(htmlspecialchars($gd_thuc_hien), $fontStyle);
						$table2->addCell(2000)->addText(htmlspecialchars($cv_th), $fontStyle);
						$table2->addCell(2000)->addText(round($value['progress'],2).'%', $fontStyle);
						$table2->addCell(2000)->addText(htmlspecialchars($cv_delay), $fontStyle);
						$stt++;
					}
				}
			}
		}

		$section->addText('II. Các hoạt động khác', null, 'hStyle');
		$section->addText('...', null, 'hStyle');
		$section->addText('III. Phân tích, đánh giá nhân viên', null, 'hStyle');
		$section->addText('1. Biểu đồ doanh thu, lợi nhuận', null, 'hStyle');
		$phpWord->addTableStyle('table3', $styleTable,null);
		$table3 = $section->addTable('table3');
		$table3->addRow();
		$table3->addCell(2000)->addText('Chỉ tiêu chính', $fontStyle_thead);
		$table3->addCell(2000)->addText('Tỷ trọng', $fontStyle_thead);
		$table3->addCell(2000)->addText('Tỷ trọng thành phần', $fontStyle_thead);
		$table3->addCell(2000)->addText('Thực tế', $fontStyle_thead);
		$table3->addCell(2000)->addText('Kế hoạch', $fontStyle_thead);
		$table3->addCell(2000)->addText('Thực tế/Kế hoạch', $fontStyle_thead);
		$table3->addCell(2000)->addText('Điểm*', $fontStyle_thead);
		$table3->addRow();

		if ($check=='THANG') {
			if ($month<=3) {
				$month = 1;
			}else if ($month<=6) {
				$month =2;
			}else if ($month<=9) {
				$month =3;
			}else if ($month <=12) {
				$month =4;
			}
		}

		$plan = parent::kpi_room_data_d7(array('select'=>'revenue','tp'=>'plan','year'=>$year,'quater'=>$month))['dt'];
		if (!empty($plan)) {
			$location_data = $plan[3]['location_data'];
			$plan_tp = $location_data[1]['value'];
			$plan_cp = $location_data[2]['value'];
			$plan_ma = $location_data[3]['value'];
			$plan_tvk = $location_data[4]['value'];
			$sum_plan = $plan_tp +$plan_cp+$plan_ma+$plan_tvk;
		}
		$result = parent::kpi_room_data_d7(array('select'=>'revenue','tp'=>'result','year'=>$year,'quater'=>$month));

		if (!empty($result)) {
			$data_result = $result['dt'][3];
			$result_tp = $data_result['location_data'][1];
			$result_cp = $data_result['location_data'][2];
			$result_ma = $data_result['location_data'][3];
			$result_tvk = $data_result['location_data'][4];
			$sum_result = $result_tp +$result_cp+$result_ma+$result_tvk;
		}
		if ($plan_tp>0) {
			$percent_tp = round(($result_tp/$plan_tp)*100,2);
		}else{
			$percent_tp =0;
		}
		if ($plan_cp>0) {
			$percent_cp = round(($result_cp/$plan_cp)*100,2);
		}else{
			$percent_cp =0;
		}
		if ($plan_ma>0) {
			$percent_ma = round(($result_ma/$plan_ma)*100,2);
		}else{
			$percent_ma =0;
		}
		if ($plan_tvk>0) {
			$percent_tvk = round(($result_tvk/$plan_tvk)*100,2);
		}else{
			$percent_tvk =0;
		}
		if ($sum_plan>0) {
			$sum_percent = round(($sum_result/$sum_plan)*100,2);
		}else{
			$sum_percent =0;
		}
		// echo "<pre>"; print_r($result); die();

		$table3->addCell(2000)->addText('Doanh thu', $fontStyle_thead);
		$table3->addCell(2000)->addText('20%', $fontStyle);
		$table3->addCell(2000)->addText('', $fontStyle);
		$table3->addCell(2000)->addText('', $fontStyle);
		$table3->addCell(2000)->addText('', $fontStyle);
		$table3->addCell(2000)->addText('', $fontStyle);
		$table3->addCell(2000)->addText('', $fontStyle);
		$table3->addRow();
		$table3->addCell(2000)->addText('Doanh thu bảo lãnh, đại lý phát hành trái phiếu', $fontStyle_thead);
		$table3->addCell(2000)->addText('40%', $fontStyle);
		$table3->addCell(2000)->addText($result['rate'][1], $fontStyle);
		$table3->addCell(2000)->addText(number_format($result_tp), $fontStyle);
		$table3->addCell(2000)->addText(number_format($plan_tp), $fontStyle);
		$table3->addCell(2000)->addText($percent_tp, $fontStyle);
		$table3->addCell(2000)->addText(parent::convert_rate_d7(array('rate'=>$percent_tp)), $fontStyle);
		$table3->addRow();
		$table3->addCell(2000)->addText('Doanh thu bảo lãnh, đại lý phát hành cổ phiếu', $fontStyle_thead);
		$table3->addCell(2000)->addText('20%', $fontStyle);
		$table3->addCell(2000)->addText($result['rate'][2], $fontStyle);
		$table3->addCell(2000)->addText(number_format($result_cp), $fontStyle);
		$table3->addCell(2000)->addText(number_format($plan_cp), $fontStyle);
		$table3->addCell(2000)->addText($percent_cp, $fontStyle);
		$table3->addCell(2000)->addText(parent::convert_rate_d7(array('rate'=>$percent_cp)), $fontStyle);
		$table3->addRow();
		$table3->addCell(2000)->addText('Doanh thu MA', $fontStyle_thead);
		$table3->addCell(2000)->addText('20%', $fontStyle);
		$table3->addCell(2000)->addText($result['rate'][3], $fontStyle);
		$table3->addCell(2000)->addText(number_format($result_ma), $fontStyle);
		$table3->addCell(2000)->addText(number_format($plan_ma), $fontStyle);
		$table3->addCell(2000)->addText($percent_ma, $fontStyle);
		$table3->addCell(2000)->addText(parent::convert_rate_d7(array('rate'=>$percent_ma)), $fontStyle);
		$table3->addRow();
		$table3->addCell(2000)->addText('Doanh thu tư vấn khác', $fontStyle_thead);
		$table3->addCell(2000)->addText('20%', $fontStyle);
		$table3->addCell(2000)->addText($result['rate'][4], $fontStyle);
		$table3->addCell(2000)->addText(number_format($result_tvk), $fontStyle);
		$table3->addCell(2000)->addText(number_format($plan_tvk), $fontStyle);
		$table3->addCell(2000)->addText($percent_tvk, $fontStyle);
		$table3->addCell(2000)->addText(parent::convert_rate_d7(array('rate'=>$percent_tvk)), $fontStyle);
		$table3->addRow();
		$table3->addCell(2000)->addText('Lợi nhuận', $fontStyle_thead);
		$table3->addCell(2000)->addText('', $fontStyle);
		$table3->addCell(2000)->addText('', $fontStyle);
		$table3->addCell(2000)->addText('', $fontStyle);
		$table3->addCell(2000)->addText('', $fontStyle);
		$table3->addCell(2000)->addText('', $fontStyle);
		$table3->addCell(2000)->addText('', $fontStyle);
		$table3->addRow();
		$table3->addCell(2000)->addText('Tổng', $fontStyle_thead);
		$table3->addCell(2000)->addText('', $fontStyle);
		$table3->addCell(2000)->addText('', $fontStyle);
		$table3->addCell(2000)->addText(number_format($sum_result), $fontStyle);
		$table3->addCell(2000)->addText(number_format($sum_plan), $fontStyle);
		$table3->addCell(2000)->addText($sum_percent, $fontStyle);
		$table3->addCell(2000)->addText(parent::convert_rate_d7(array('rate'=>$sum_percent)), $fontStyle);

		$rate = $this->Kpi_model->get_rate();

		$section->addText('* Ghi chú:', null, 'hStyle');
		$phpWord->addTableStyle('table5', $styleTable,null);
		$table5 = $section->addTable('table5');
		$table5->addRow();
		$table5->addCell(2000)->addText('Khung đánh giá', $fontStyle_thead);
		$table5->addCell(2000)->addText('Điểm', $fontStyle_thead);
		$table5->addRow();
		$table5->addCell(2000)->addText('- Tỷ lệ TH/KH nhỏ hơn 70%', $fontStyle);
		$table5->addCell(2000)->addText($rate[0]['point'], $fontStyle);
		$table5->addRow();
		$table5->addCell(2000)->addText('- Từ 70% đến dưới 90%', $fontStyle);
		$table5->addCell(2000)->addText($rate[1]['point'], $fontStyle);
		$table5->addRow();
		$table5->addCell(2000)->addText('- Từ 90% đến dưới 110%', $fontStyle);
		$table5->addCell(2000)->addText($rate[2]['point'], $fontStyle);
		$table5->addRow();
		$table5->addCell(2000)->addText('- Từ 110% đến dưới 130%', $fontStyle);
		$table5->addCell(2000)->addText($rate[3]['point'], $fontStyle);
		$table5->addRow();
		$table5->addCell(2000)->addText('- >= 130%', $fontStyle);
		$table5->addCell(2000)->addText($rate[4]['point'], $fontStyle);

		$section->addText('2. Biểu đồ năng lực', null, 'hStyle');
		$section->addText('...', null, 'hStyle');
		$section->addText('IV. Thông tin các vấn đề phát sinh ', null, 'hStyle');
		$section->addText('1. Các vấn đề phát sinh đã xử lý', null, 'hStyle');
		$section->addText('...', null, 'hStyle');
		$section->addText('2. Các vấn đề phát sinh chưa xử lý', null, 'hStyle');
		$section->addText('...', null, 'hStyle');

		$section->addTextBreak();
		$phpWord->addTableStyle('table_footer', NULL,null);
		$table_footer = $section->addTable('table_footer');
		$table_footer->addRow();
		$table_footer->addCell(6000)->addText('',NULL);
		$table_footer->addCell(4000)->addText('TRƯỞNG PHÒNG TVTCDN-'.mb_strtoupper($name_location), $fontStyle);


		$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
		$filename = $title.'.docx';
		$objWriter->save($filename);
	// send results to browser to download
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