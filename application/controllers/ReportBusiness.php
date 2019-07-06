<?php 
require_once("Reports.php");
require_once ("Secure_area.php");
require_once("Kpi.php");
class ReportBusiness extends Kpi
{
	function __construct()
	{
		// error_reporting(-1);
		// ini_set('display_errors', 'On');
		parent::__construct();
		$this->load->model('Ratings');
		$this->load->model('Kpi_model');
		$this->load->library('PHPWord');
	}
	function view($id=-1){
		$location_id = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id())->location_id;
		$location = $this->Location->list_item();
		$data['location_id'] = $location_id;
		// echo "<pre>"; print_r($location); die();
		// echo $this->db->last_query();die();
		if ($this->Employee->has_module_action_permission('reports','bc_phong_phan_tich', $this->Employee->get_logged_in_employee_info()->person_id))
		{
			$this->load->view('reports/contract/bao_cao_hoat_dong_kinh_doanh',$data);
		}else{
			$this->load->view('reports/contract/not_permission_reports.php');
		}
		
	}
	function export_data(){
		$year = $this->input->post('year');
		$month = $this->input->post('month');
		$check = $this->input->post('check');

		$location_id = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id())->location_id;
		$data['location'] = $this->Location->list_item();
		$data['input'] = $this->input->post();
		
		// % hoan thanh ke hoach nam truoc nam hien tai
		// TINH CẢ NAM
		$year_b = $year-1;

		$report_contracts = $this->Contract->kpi_report_contracts($year);
		$total_242 = $total_243 = $total_246 =$total_0=0;
		if ($check=='NAM' || $check=='QUY') {
			if ($check=='QUY') {
				switch ($month) {
					case 1:
					$month_next = 2;
					$year_sub=1;
					break;
					case 2:
					$month_next = 3;
					$year_sub=2;
					break;
					case 3:
					$month_next = 4;
					$year_sub=3;
					break;
					case 4:
					$month_next = 1;
					$year = $year+1;
					$year_sub=4;
					break;
				}
			}else{
				if ($month==12) {
					$month_next=1;
					$year = $year+1;
				}else{
					$month_next=$month+1;
				}
				$year_sub= 5;
			}

			$data['KHKD'] = $this->Contract->bao_cao_ca_nhan(array('DTGN'=>true,'not_liquidated'=>true,'month'=>$month_next,'year'=>$year,'check'=>$check));
			$data['contract_done'] = $this->Contract->bao_cao_ca_nhan(array('DTGN'=>true,'not_liquidated'=>true,'month'=>$month_next,'year'=>$year,'check'=>$check,'done'=>true));

			return $this->load->view('reports/contract/tbl_gui_phong_phan_tich',$data);
		}
	}


	function quater_next($month=null){
		if ($month=null) {
			$month = (int)Date('m');
		}
		if ($month<=3) {
			$quater_next = 2;
		}elseif ( $month<=6) {
			$quater_next = 3;
		}elseif ($month<=9) {
			$quater_next = 4;
		}elseif ($month<=12) {
			$quater_next = 1;
		}
		return $quater_next;
	}
	function download(){
		$check = $this->input->post('check');
		$month = $this->input->post('input_month');
		$year = $this->input->post('input_year');

		if ($check=='NAM') {
			$this->download_year($year);
		}else{
			if ($check=='TD') {
				$date_start = $this->input->post('date_start');
				$date_end = $this->input->post('date_end');
				$this->download_form_month_quater($month, $year,$check,$date_start,$date_end);
			}else{
				$this->download_form_month_quater($month, $year,$check);
			}
		}
	}
	function download_form_month_quater($month, $year,$check,$date_start=null, $date_end=null){
		$phpWord = new \PhpOffice\PhpWord\PhpWord();
		$phpWord->getCompatibility()->setOoxmlVersion(14);
		$phpWord->getCompatibility()->setOoxmlVersion(15);
		$section = $phpWord->addSection();
		$phpWord->setDefaultFontName('Times New Roman');
		$phpWord->setDefaultFontSize(12);
		// param: group_id
		$item_id_tp =$this->Kpi_model->get_list_item_by_categories(1);
		$item_id_cp =$this->Kpi_model->get_list_item_by_categories(2);
		$item_id_ma =$this->Kpi_model->get_list_item_by_categories(3);
		$item_id_tvk =$this->Kpi_model->get_list_item_by_categories(4);


		if ($check=='THANG') {
			$time = 'Tháng '.$month.'/'.$year;
			$title ="HOẠT ĐỘNG KINH DOANH THÁNG $month/$year";
			$file = "BÁO CÁO HOẠT ĐỘNG KINH DOANH THÁNG";
		}elseif ($check=='QUY') {
			$time = 'Quý '.$month.'/'.$year;
			$title ="HOẠT ĐỘNG KINH DOANH QUÝ $month/$year";
			$file = "HOẠT ĐỘNG KINH DOANH QUÝ ";
		}else{
			$title ="HOẠT ĐỘNG KINH DOANH TỪ NGÀY $date_start ĐẾN NGÀY $date_end";
			$file = "BÁO CÁO HOẠT ĐỘNG KINH DOANH TỪ NGÀY $date_start ĐẾN NGÀY $date_end";
			$time = $date_start.' - '.$date_end;
		}

		$phpWord->addParagraphStyle('pStyle', ['align' => 'center', 'spaceAfter' => 100]);
		$phpWord->addFontStyle('StyleHeader', ['bold' => true, 'size' => 16]);
		$phpWord->addFontStyle('hStyle', ['bold' => true]);
		$styleTable = ['borderSize' => 6, 'borderColor' => '000', 'cellMargin' => 80,'name'=>'Times New Roman'];
		$phpWord->addFontStyle('center', ['align' => 'center']);
		$fontStyle_thead = array('bold' => true);

		$font_bold = array('bold'=>true);
		$fontStyle = array('align' => 'center');

		$style_number = array('align' => 'right');
		$stylebg = array('bgColor'=>'#00b300');

		$section->addText('BÁO CÁO', 'StyleHeader', 'pStyle');
		$section->addText($title, 'StyleHeader', 'pStyle');
		$section->addText('1. Kết quả hoạt động kinh doanh', 'hStyle', null);
		$section->addText('Danh sách các deal ghi nhận doanh thu trong kỳ (phân loại các deal có doanh thu dài hạn và doanh thu bất thường)');
		$phpWord->addTableStyle('table', $styleTable,null);
		$table = $section->addTable('table');
		$table->addRow();
		$table->addCell(2000)->addText('Chỉ tiêu chính', $fontStyle_thead,$fontStyle);
		$table->addCell(2000)->addText($time, $fontStyle_thead,$fontStyle);
		$table->addCell(2000)->addText('Thực hiện lũy kế', $fontStyle_thead,$fontStyle);
		$table->addCell(2000)->addText('Kế hoạch năm '.$year, $fontStyle_thead,$fontStyle);
		$table->addCell(2000)->addText('% hoàn thành kế hoạch', $fontStyle_thead,$fontStyle);
		$table->addCell(2000)->addText('Cùng kỳ', $fontStyle_thead,$fontStyle);
		$table->addCell(2000)->addText('%yoy', $fontStyle_thead,$fontStyle);

		$all_location = $this->Location->list_item();

		$location_id = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id())->location_id;

		$plan_year = parent::kpi_room_data_d7(array('select'=>'revenue','tp'=>'plan','year'=>$year))['dt'];

		if ($check=='THANG') {
			$date_end = date_create($year.'-'.$month);
			$date_start = date_format($date_end, "Y-m-d");
			$date_end = date_modify($date_end, "+1 months");
			$date_end = date_format($date_end, "Y-m-d");

			$dt_tp = $this->Kpi_model->get_revenue_categories(1,$location_id,array('start_date'=>$date_start,'end_date'=>$date_end))['value'];
			$dt_cp = $this->Kpi_model->get_revenue_categories(2,$location_id,array('start_date'=>$date_start,'end_date'=>$date_end))['value'];
			$dt_ma = $this->Kpi_model->get_revenue_categories(3,$location_id,array('start_date'=>$date_start,'end_date'=>$date_end))['value'];
			$dt_tvk = $this->Kpi_model->get_revenue_categories(4,$location_id,array('start_date'=>$date_start,'end_date'=>$date_end))['value'];

			// luy ke
			$date_firt_year = date_format(date_create($year.'-01'),'Y-m-d');
			// echo $date_firt_year; die();
			$luyke_tp = $this->Kpi_model->get_revenue_categories(1,$location_id,array('end_date'=>$date_end,'start_date'=>$date_firt_year))['value'];
			$luyke_cp = $this->Kpi_model->get_revenue_categories(2,$location_id,array('end_date'=>$date_end,'start_date'=>$date_firt_year))['value'];
			$luyke_ma = $this->Kpi_model->get_revenue_categories(3,$location_id,array('end_date'=>$date_end,'start_date'=>$date_firt_year))['value'];
			$luyke_tvk = $this->Kpi_model->get_revenue_categories(4,$location_id,array('end_date'=>$date_end,'start_date'=>$date_firt_year))['value'];

			// CUNG ky nam ngoai
			$date_end_b = date_create(($year-1).'-'.$month);
			$date_start_b = date_format($date_end_b, "Y-m-d");
			$date_end_b = date_modify($date_end_b, "+1 months");
			$date_end_b = date_format($date_end_b, "Y-m-d");

			$dt_before_tp = $this->Kpi_model->get_revenue_categories(1,$location_id,array('start_date'=>$date_start_b,'end_date'=>$date_end_b))['value'];
			$dt_before_cp = $this->Kpi_model->get_revenue_categories(2,$location_id,array('start_date'=>$date_start_b,'end_date'=>$date_end_b))['value'];
			$dt_before_ma = $this->Kpi_model->get_revenue_categories(3,$location_id,array('start_date'=>$date_start_b,'end_date'=>$date_end_b))['value'];
			$dt_before_tvk = $this->Kpi_model->get_revenue_categories(4,$location_id,array('start_date'=>$date_start_b,'end_date'=>$date_end_b))['value'];



		}
		if (!empty($plan_year)) {
			for ($i=0; $i < 4; $i++) { 
				if ($plan_year[$i]['location_id']==$location_id) {
					$location_data = $plan_year[$i]['location_data'];
					$plan_tp = $location_data[1]['value'];
					$plan_cp = $location_data[2]['value'];
					$plan_ma = $location_data[3]['value'];
					$plan_tvk = $location_data[4]['value'];
				}
			}
		}
		if ($check=='QUY') {
			$date_firt_year = date_format(date_create($year.'-01'),'Y-m-d');

			$quater=$month;
			$result = parent::kpi_room_data_d7(array('select'=>'revenue','tp'=>'result','year'=>$year,'quater'=>$quater))['dt'];
			$result_quater1 = parent::kpi_room_data_d7(array('select'=>'revenue','tp'=>'result','year'=>$year,'quater'=>1))['dt'];
			$result_quater2 = parent::kpi_room_data_d7(array('select'=>'revenue','tp'=>'result','year'=>$year,'quater'=>2))['dt'];
			$result_quater3 = parent::kpi_room_data_d7(array('select'=>'revenue','tp'=>'result','year'=>$year,'quater'=>3))['dt'];
			$result_quater4 = parent::kpi_room_data_d7(array('select'=>'revenue','tp'=>'result','year'=>$year,'quater'=>4))['dt'];


			if (!empty($result)) {
				for ($i=0; $i < 4; $i++) { 
					if ($result[$i]['location_id']==$location_id) {
						$location_data = $result[$i]['location_data'];
						$dt_tp = $location_data[1];
						$dt_cp = $location_data[2];
						$dt_ma = $location_data[3];
						$dt_tvk = $location_data[4];

					}
				}
			}
			switch ($quater) {
				case 1:
				$date_start = date_format(date_create($year.'-01'),'Y-m-d');
				$date_end = date_format(date_create($year.'-03'),'Y-m-t');
				$year_b = ($year-1);
				$date_start_b = date_format(date_create($year_b.'-01'),'Y-m-d');
				$date_end_b = date_format(date_create($year_b.'-03'),'Y-m-t');

				break;
				case 2:
				$date_start = date_format(date_create($year.'-04'),'Y-m-d');
				$date_end = date_format(date_create($year.'-06'),'Y-m-t');
				$date_start_b = date_format(date_create($year_b.'-04'),'Y-m-d');
				$date_end_b = date_format(date_create($year_b.'-06'),'Y-m-t');
				break;
				case 3:
				$date_start = date_format(date_create($year.'-07'),'Y-m-d');
				$date_end = date_format(date_create($year.'-09'),'Y-m-t');
				$date_start_b = date_format(date_create($year_b.'-07'),'Y-m-d');
				$date_end_b = date_format(date_create($year_b.'-09'),'Y-m-t');
				break;
				case 4:
				$date_start = date_format(date_create($year.'-10'),'Y-m-d');
				$date_end = date_format(date_create($year.'-12'),'Y-m-t');
				$date_start_b = date_format(date_create($year_b.'-10'),'Y-m-d');
				$date_end_b = date_format(date_create($year_b.'-12'),'Y-m-t');
				break;
			}
			$luyke_tp = $this->Kpi_model->get_revenue_categories(1,$location_id,array('end_date'=>$date_end,'start_date'=>$date_firt_year))['value'];
			$luyke_cp = $this->Kpi_model->get_revenue_categories(2,$location_id,array('end_date'=>$date_end,'start_date'=>$date_firt_year))['value'];
			$luyke_ma = $this->Kpi_model->get_revenue_categories(3,$location_id,array('end_date'=>$date_end,'start_date'=>$date_firt_year))['value'];
			$luyke_tvk = $this->Kpi_model->get_revenue_categories(4,$location_id,array('end_date'=>$date_end,'start_date'=>$date_firt_year))['value'];

			// LAY DOANH THU CUNG KY NAM TRUOC

			$revenue_before = parent::kpi_room_data_d7(array('select'=>'revenue','tp'=>'result','year'=>($year-1),'quater'=>$quater))['dt'];

			if (!empty($revenue_before)) {
				for ($i=0; $i < 4; $i++) { 
					if ($revenue_before[$i]['location_id']==$location_id) {
						$location_data = $revenue_before[$i]['location_data'];
						$dt_before_tp = $location_data[1];
						$dt_before_cp = $location_data[2];
						$dt_before_ma = $location_data[3];
						$dt_before_tvk = $location_data[4];

					}
				}
			}
		}
		// TINH % hoan thanh ke hoach
		$prevent_plan_tp = $prevent_plan_cp =$prevent_plan_ma =$prevent_plan_tvk='N/A';
		// echo $luyke_tp; die();
		if ($plan_tp>0) {
			$prevent_plan_tp = round(($luyke_tp/$plan_tp)*100,2);
		}
		if ($plan_cp>0) {
			$prevent_plan_cp = round(($luyke_cp/$plan_cp)*100,2);
		}
		if ($plan_ma>0) {
			$prevent_plan_ma = round(($luyke_ma/$plan_ma)*100,2);
		}
		if ($plan_tvk>0) {
			$prevent_plan_tvk = round(($luyke_tvk/$plan_tvk)*100,2);
		}
		// tinh yoy
		$yoy_tp = $yoy_cp= $yoy_ma = $yoy_tvk ='N/A';
		if ($dt_before_tp>0) {
			$yoy_tp = round(($dt_tp/$dt_before_tp)*100,2);
		}
		if ($dt_before_cp>0) {
			$yoy_cp = round(($dt_cp/$dt_before_cp)*100,2);
		}
		if ($dt_before_ma>0) {
			$yoy_ma = round(($dt_ma/$dt_before_ma)*100,2);
		}
		if ($dt_before_tvk>0) {
			$yoy_tvk = round(($dt_tvk/$dt_before_tvk)*100,2);
		}

		$table->addRow();
		$table->addCell(2000)->addText(htmlspecialchars('Bảo lãnh, đại lý phát hành TP'),array(),$fontStyle);
		$table->addCell(2000)->addText(number_format($dt_tp),array(),$style_number);
		$table->addCell(2000)->addText(number_format($luyke_tp),array(),$style_number);
		$table->addCell(2000)->addText(number_format($plan_tp),array(),$style_number);
		$table->addCell(2000)->addText($prevent_plan_tp,array(),$style_number);
		$table->addCell(2000)->addText(number_format($dt_before_tp),array(),$style_number);
		$table->addCell(2000)->addText($yoy_tp,array(),$style_number);

		$table->addRow();
		$table->addCell(2000)->addText('Bảo lãnh, đại lý phát hành CP',array(),$fontStyle);
		$table->addCell(2000)->addText(number_format($dt_cp),array(),$style_number);
		$table->addCell(2000)->addText(number_format($luyke_cp),array(),$style_number);
		$table->addCell(2000)->addText(number_format($plan_cp),array(),$style_number);
		$table->addCell(2000)->addText($prevent_plan_cp,array(),$style_number);
		$table->addCell(2000)->addText(number_format($dt_before_cp),array(),$style_number);
		$table->addCell(2000)->addText($yoy_cp,array(),$style_number);


		$table->addRow();
		$table->addCell(2000)->addText('Tư vấn khác',array(),$fontStyle);
		$table->addCell(2000)->addText(number_format($dt_tvk),array(),$style_number);
		$table->addCell(2000)->addText(number_format($luyke_tvk),array(),$style_number);
		$table->addCell(2000)->addText(number_format($plan_tvk),array(),$style_number);
		$table->addCell(2000)->addText($prevent_plan_tvk,array(),$style_number);
		$table->addCell(2000)->addText(number_format($dt_before_tvk),array(),$style_number);
		$table->addCell(2000)->addText($yoy_tvk,array(),$style_number);

		$table->addRow();
		$table->addCell(2000)->addText(htmlspecialchars('Tư vấn M&A'),array(),$fontStyle);
		$table->addCell(2000)->addText(number_format($dt_ma),array(),$style_number);
		$table->addCell(2000)->addText(number_format($luyke_ma),array(),$style_number);
		$table->addCell(2000)->addText(number_format($plan_ma),array(),$style_number);
		$table->addCell(2000)->addText($prevent_plan_ma,array(),$style_number);
		$table->addCell(2000)->addText(number_format($dt_before_ma),array(),$style_number);
		$table->addCell(2000)->addText($yoy_ma,array(),$style_number);

		$sum_yoy = $sum_luy_ke = $sum_plan = $sum_dt = $sum_before =0;

		$sum_luy_ke =$luyke_tp + $luyke_cp + $luyke_ma+$luyke_tvk;
		$sum_plan =  $plan_tp+$plan_cp+$plan_ma+$plan_tvk;
		if ($sum_plan>0) {
			$sum_pecent = round(($sum_luy_ke/$sum_plan)*100,2);
		}else{
			$sum_pecent ='N/A';
		}
		$sum_dt = $dt_tp+$dt_cp+$dt_ma+$dt_tvk;
		$sum_before = $dt_before_ma+$dt_before_tvk+$dt_before_cp+$dt_before_tp;

		if ($sum_before>0) {
			$sum_yoy = round(($sum_dt/$sum_before)*100,2);
		}else{
			$sum_yoy = 'N/A';
		}
		$table->addRow();
		$table->addCell(2000)->addText('Tổng cộng',$fontStyle_thead,$fontStyle);
		$table->addCell(2000)->addText(number_format($sum_dt),$fontStyle_thead,$style_number);
		$table->addCell(2000)->addText(number_format($sum_luy_ke),$fontStyle_thead,$style_number);
		$table->addCell(2000)->addText(number_format($sum_plan),$fontStyle_thead,$style_number);
		$table->addCell(2000)->addText($sum_pecent,$fontStyle_thead,$style_number);
		$table->addCell(2000)->addText(number_format($sum_before),$fontStyle_thead,$style_number);
		$table->addCell(2000)->addText($sum_yoy,$fontStyle_thead,$style_number);

		$section->addText('Đánh giá hiệu quả tại các địa bàn, nguyên nhân:');


		if ($check=='TD') {
			$contract_HSC = $this->Contract->contract_location(array('location'=>'HSC','date_payment'=>true,'check'=>$check,'date_start'=>$date_start,'date_end'=>$date_end));
			$contract_DN = $this->Contract->contract_location(array('location'=>'DN','date_payment'=>true,'check'=>$check,'date_start'=>$date_start,'date_end'=>$date_end,'c_status'=>array('done','liquidated')));
			$contract_HCM = $this->Contract->contract_location(array('location'=>'HCM','date_payment'=>true,'check'=>$check,'date_start'=>$date_start,'date_end'=>$date_end,'c_status'=>array('done','liquidated')));

		}else{
			$contract_HSC = $this->Contract->contract_location(array('location'=>'HSC','date_payment'=>true,'check'=>$check,'month'=>$month,'year'=>$year,'c_status'=>array('done','liquidated')));
			$contract_DN = $this->Contract->contract_location(array('location'=>'DN','date_payment'=>true,'check'=>$check,'month'=>$month,'year'=>$year,'c_status'=>array('done','liquidated')));
			$contract_HCM = $this->Contract->contract_location(array('location'=>'HCM','date_payment'=>true,'check'=>$check,'month'=>$month,'year'=>$year,'c_status'=>array('done','liquidated')));
		}
		// echo $this->db->last_query(); die();
		// echo "<pre>"; print_r($contract_HSC); die();
		$section->addText('- HSC:');
		if (!empty($tableHSC)) {
			$phpWord->addTableStyle('tableHSC', $styleTable,null);
			$tableHSC = $section->addTable('tableHSC');
			$tableHSC->addRow();
			$tableHSC->addCell(500)->addText('STT', $fontStyle_thead, $fontStyle);
			$tableHSC->addCell(4000)->addText('Tên hợp đồng', $fontStyle_thead, $fontStyle);
			$tableHSC->addCell(4000)->addText('Tên dịch vụ', $fontStyle_thead, $fontStyle);
			$tableHSC->addCell(4000)->addText('Giá trị hợp đồng', $fontStyle_thead, $fontStyle);
			$stt=1;
			foreach ($contract_HSC as $key => $value) {
				$tableHSC->addRow();
				$tableHSC->addCell(500)->addText($stt, $fontStyle);
				$tableHSC->addCell(4000)->addText(htmlspecialchars($value['name_contract']), $fontStyle);
				$tableHSC->addCell(4000)->addText(htmlspecialchars($value['item_name']), $fontStyle);
				$tableHSC->addCell(4000)->addText(number_format($value['co_vat']),null, $style_number);
				$stt++;
			}
		}
		// DN
		$section->addText('- Đà Nẵng:');

		if (!empty($tableDN)) {
			$phpWord->addTableStyle('tableDN', $styleTable,null);
			$tableDN = $section->addTable('tableDN');
			$tableDN->addRow();
			$tableDN->addCell(500)->addText('STT', $fontStyle_thead, $fontStyle);
			$tableDN->addCell(4000)->addText('Tên hợp đồng', $fontStyle_thead, $fontStyle);
			$tableDN->addCell(4000)->addText('Tên dịch vụ', $fontStyle_thead, $fontStyle);
			$tableDN->addCell(4000)->addText('Giá trị hợp đồng', $fontStyle_thead, $fontStyle);
			$stt=1;
			foreach ($contract_DN as $key => $value) {
				$tableDN->addRow();
				$tableDN->addCell(500)->addText($stt, $fontStyle);
				$tableDN->addCell(4000)->addText(htmlspecialchars($value['name_contract']), $fontStyle);
				$tableDN->addCell(4000)->addText(htmlspecialchars($value['item_name']), $fontStyle);
				$tableDN->addCell(4000)->addText(number_format($value['co_vat']),null, $style_number);
				$stt++;
			}
		}
		$section->addText('- Hồ Chí Minh:');

		if (!empty($tableHCM)) {
			$phpWord->addTableStyle('tableHCM', $styleTable,null);
			$tableHCM = $section->addTable('tableHCM');
			$tableHCM->addRow();
			$tableHCM->addCell(500)->addText('STT', $fontStyle_thead, $fontStyle);
			$tableHCM->addCell(4000)->addText('Tên hợp đồng', $fontStyle_thead, $fontStyle);
			$tableHCM->addCell(4000)->addText('Tên dịch vụ', $fontStyle_thead, $fontStyle);
			$tableHCM->addCell(4000)->addText('Giá trị hợp đồng', $fontStyle_thead, $fontStyle);
			$stt=1;
			foreach ($contract_HCM as $key => $value) {
				$tableHCM->addRow();
				$tableHCM->addCell(500)->addText($stt, $fontStyle);
				$tableHCM->addCell(4000)->addText(htmlspecialchars($value['name_contract']), $fontStyle);
				$tableHCM->addCell(4000)->addText(htmlspecialchars($value['item_name']), $fontStyle);
				$tableHCM->addCell(4000)->addText(number_format($value['co_vat']),null, $style_number);
				$stt++;
			}
		}

		$phpWord->addTableStyle('table2', $styleTable,null);
		$table2 = $section->addTable('table2');

		$col_name = array('Địa bàn',$time,'Thực hiện lũy kế','Kế hoạch năm '.$year,'% hoàn thành kế hoạch','Cùng kỳ','%yoy');
		$count_location = count($all_location);
		$count_col = count($col_name);
		$table2->addRow();
		for ($i=0; $i < $count_col ; $i++) { 
			$table2->addCell(2000)->addText(htmlspecialchars($col_name[$i]), $fontStyle_thead, $fontStyle);
		}

		// HSC
		// echo "<pre>"; print_r($result); die();
		$sum_plan_HSC = $sum_plan_HCM = $sum_plan_DN=0;
		$sum_before_HSC = $sum_before_HCM = $sum_before_DN =$sum_before_KTV=0;
		if (!empty($plan_year)) {
			$plan_HSC = $plan_year[0]['location_data'];
			$sum_plan_HSC = $plan_HSC[1]['value']+$plan_HSC[2]['value']+$plan_HSC[3]['value']+$plan_HSC[4]['value'];

			$plan_DN = $plan_year[1]['location_data'];
			$sum_plan_DN = $plan_DN[1]['value']+$plan_DN[2]['value']+$plan_DN[3]['value']+$plan_DN[4]['value'];

			$plan_HCM = $plan_year[2]['location_data'];
			$sum_plan_HCM = $plan_HCM[1]['value']+$plan_HCM[2]['value']+$plan_HCM[3]['value']+$plan_HCM[4]['value'];

		}
		$total_dt=0;
		$total_luyke=0;
		$total_ck=0;
		$total_yoy=0;
		$total_plan=0;
		$total_percent =0;
		// $ta = $this->Kpi_model->get_kpi_revenue_room($year,$location_id);
		// echo "<pre>"; print_r($ta); die();
		for ($i=0; $i < $count_location  ; $i++) {
			$sum_luy_ke =0;
			$sum_dt=0;
			$sum_plan =0;
			$sum_percen='N/A';
			$sum_before=0;
			$yoy='N/A';
			for ($j=1; $j <=4 ; $j++) { 
				$sum_luy_ke += $this->Kpi_model->get_revenue_categories($j,$all_location[$i]['location_id'],array('end_date'=>$date_end,'start_date'=>$date_firt_year))['value'];
				$total_luyke +=$this->Kpi_model->get_revenue_categories($j,$all_location[$i]['location_id'],array('end_date'=>$date_end,'start_date'=>$date_firt_year))['value'];
				$total_dt += $this->Kpi_model->get_revenue_categories($j,$all_location[$i]['location_id'],array('end_date'=>$date_end,'start_date'=>$date_start))['value'];
				$sum_dt += $this->Kpi_model->get_revenue_categories($j,$all_location[$i]['location_id'],array('end_date'=>$date_end,'start_date'=>$date_start))['value'];
				
				$sum_before += $this->Kpi_model->get_revenue_categories($j,$all_location[$i]['location_id'],array('end_date'=>$date_end_b,'start_date'=>$date_start_b))['value'];
				$total_ck +=$this->Kpi_model->get_revenue_categories($j,$all_location[$i]['location_id'],array('end_date'=>$date_end_b,'start_date'=>$date_start_b))['value'];
			}

			$data_plan_y = $this->Kpi_model->get_kpi_revenue_room($year,$all_location[$i]['location_id']);
			if (!empty($data_plan_y)) {
				foreach ($data_plan_y as $key => $value) {
					$sum_plan +=$value['value'];
				}
			} 
			$total_plan +=$sum_plan;

			if ($sum_plan>0) {
				$sum_percen = round(($sum_luy_ke/$sum_plan)*100,2);
			}
			if ($sum_before>0) {
				$yoy = round(($sum_dt/$sum_before)*100,2);
			}
			$table2->addRow();
			$table2->addCell(2000)->addText(htmlspecialchars($all_location[$i]['name']),null, $fontStyle);
			$table2->addCell(2000)->addText(number_format($sum_dt), null, $style_number);
			$table2->addCell(2000)->addText(number_format($sum_luy_ke), null, $style_number);
			$table2->addCell(2000)->addText(number_format($sum_plan), null, $style_number);
			$table2->addCell(2000)->addText($sum_percen, null, $style_number);
			$table2->addCell(2000)->addText(number_format($sum_before), null, $style_number);
			$table2->addCell(2000)->addText($yoy, null, $style_number);
		}
		if ($total_plan>0) {
			$total_percent = round(($total_luyke/$total_plan)*100,2);
		}else{
			$total_percent = 'N/A';
		}
		if ($total_ck>0) {
			$total_yoy = round(($total_dt/$total_ck)*100,2);
		}else{
			$total_yoy = 'N/A';
		}

		$table2->addRow();
		$table2->addCell(2000)->addText(htmlspecialchars('Tổng'), $font_bold, $fontStyle);
		$table2->addCell(2000)->addText(number_format($total_dt),  $font_bold, $style_number);
		$table2->addCell(2000)->addText(number_format($total_luyke),  $font_bold, $style_number);
		$table2->addCell(2000)->addText(number_format($total_plan),  $font_bold, $style_number);
		$table2->addCell(2000)->addText($total_percent,  $font_bold, $style_number);
		$table2->addCell(2000)->addText(number_format($total_ck),  $font_bold, $style_number);
		$table2->addCell(2000)->addText($total_yoy, $font_bold, $style_number);

		$section->addTextBreak();
		$section->addText('2. Khó khăn và vướng mắc khi thực hiện công việc trong hoạt động kinh doanh:', 'hStyle');
		$section->addText('Kết thúc '.$time.', Phòng đang gặp phải những khó khăn, vấn đề phát sinh như sau: ');

		$section->addText('...');

		if ($check=='QUY') {
			if ($month<4) {
				$quater_next = $month+1;
				$year_next = $year;
			}elseif ($month==4) {
				$quater_next = 1;
				$year_next = $year+1;
			}

			$plan_quater_next = parent::kpi_room_data_d7(array('select'=>'revenue','tp'=>'plan','year'=>$year_next,'quater'=>$quater_next))['dt'];
			$tong_doan_thu_t3=0;


			$location_data = $plan_quater_next[3]['location_data'];
			for ($j=1; $j <=4 ; $j++) { 
				$tong_doan_thu_t3+=$location_data[$j]['value'];
			}



			$section->addText('3. Kế hoạch hoạt động kinh doanh Quý '.$quater_next.'/'.$year_next, 'hStyle');
			$section->addText('Doanh thu Quý '.$quater_next.'/'.$year_next.' của toàn hệ thống ước đạt ... đồng, trong đó:');

		}elseif($check=='THANG'){
			if ($month<12) {
				$quater_next = $month+1;
				$year_next = $year;
			}else{
				$quater_next = 1;
				$year_next = $year+1;
			}

			$section->addText('3. Kế hoạch hoạt động kinh doanh tháng '.$quater_next.'/'.$year_next, 'hStyle');
			$section->addText('Doanh thu tháng '.$quater_next.'/'.$year_next.' của toàn hệ thống ước đạt … đồng, trong đó:');
		}else{
			$section->addText('3. Kế hoạch hoạt động kinh doanh từ '.$time, 'hStyle');
		}


		if ($check=='QUY'|| $check=='THANG') {
			$bao_cao_doanh_thu_tiep_theo_HSC = $this->Contract->contract_location(array('date_end'=>true,'check_tp'=>$check,'ctstatus'=>array('progress'),'location'=>'HSC','month'=>$quater_next,'year'=>$year_next));
			$bao_cao_doanh_thu_tiep_theo_DN = $this->Contract->contract_location(array('date_end'=>true,'check_tp'=>$check,'ctstatus'=>array('progress'),'location'=>'DN','month'=>$quater_next,'year'=>$year_next));
			$bao_cao_doanh_thu_tiep_theo_HCM = $this->Contract->contract_location(array('date_end'=>true,'check_tp'=>$check,'ctstatus'=>array('progress'),'location'=>'HCM','month'=>$quater_next,'year'=>$year_next));
		}


		$section->addText('Hội Sở Chính: ', 'hStyle');

		if (!empty($bao_cao_doanh_thu_tiep_theo_HSC)) {
			$phpWord->addTableStyle('table3HSC', $styleTable,null);
			$table3HSC = $section->addTable('table3HSC');
			$table3HSC->addRow();
			$table3HSC->addCell(2000)->addText('STT', $fontStyle_thead, $fontStyle);
			$table3HSC->addCell(2000)->addText('Tên dự án', $fontStyle_thead, $fontStyle);
			$table3HSC->addCell(2000)->addText('Tên dịch vụ', $fontStyle_thead, $fontStyle);
			$table3HSC->addCell(2000)->addText('Tên hợp đồng', $fontStyle_thead, $fontStyle);
			$table3HSC->addCell(2000)->addText('Ngày nghiệm thu/thanh lý dự kiến', $fontStyle_thead, $fontStyle);
			$table3HSC->addCell(2000)->addText('Tổng giá trị hợp đồng ( trừ VAT)', $fontStyle_thead, $fontStyle);
			$table3HSC->addCell(2000)->addText('Giá trị nghiệm thu/thanh lý dự kiến', $fontStyle_thead, $fontStyle);
			$stt=1;
			foreach ($bao_cao_doanh_thu_tiep_theo_HSC as $key => $value) {
				$table3HSC->addRow();
				$table3HSC->addCell(2000)->addText($stt, null, $fontStyle);
				$table3HSC->addCell(2000)->addText(htmlspecialchars($value['name_task']), $fontStyle);
				$table3HSC->addCell(2000)->addText(htmlspecialchars($value['item_name']), $fontStyle);
				$table3HSC->addCell(2000)->addText(htmlspecialchars($value['name_contract']), $fontStyle);
				$table3HSC->addCell(2000)->addText(htmlspecialchars(date('d-m-Y',strtotime($value['date_end']))), $fontStyle);
				$table3HSC->addCell(2000)->addText(number_format($value['co_vat']), null,  $style_number);
				$table3HSC->addCell(2000)->addText('', null,  $style_number);
				$stt++;
			}
		}

		$section->addText('Đà Nẵng:', 'hStyle');
		if (!empty($bao_cao_doanh_thu_tiep_theo_DN)) {
			$phpWord->addTableStyle('table3DN', $styleTable,null);
			$table3DN = $section->addTable('table3DN');
			$table3DN->addRow();
			$table3DN->addCell(2000)->addText('STT',  $fontStyle_thead, $fontStyle);
			$table3DN->addCell(2000)->addText('Tên dự án',  $fontStyle_thead, $fontStyle);
			$table3DN->addCell(2000)->addText('Tên dịch vụ',  $fontStyle_thead, $fontStyle);
			$table3DN->addCell(2000)->addText('Tên hợp đồng',  $fontStyle_thead, $fontStyle);
			$table3DN->addCell(2000)->addText('Ngày nghiệm thu/thanh lý dự kiến',  $fontStyle_thead, $fontStyle);
			$table3DN->addCell(2000)->addText('Tổng giá trị hợp đồng ( trừ VAT)',  $fontStyle_thead, $fontStyle);
			$table3DN->addCell(2000)->addText('Giá trị nghiệm thu/thanh lý dự kiến', $fontStyle_thead, $fontStyle);
			$stt=1;
			foreach ($bao_cao_doanh_thu_tiep_theo_DN as $key => $value) {
				$table3DN->addRow();
				$table3DN->addCell(2000)->addText($stt, null, $fontStyle);
				$table3DN->addCell(2000)->addText(htmlspecialchars($value['name_task']), $fontStyle);
				$table3DN->addCell(2000)->addText(htmlspecialchars($value['item_name']), $fontStyle);
				$table3DN->addCell(2000)->addText(htmlspecialchars($value['name_contract']), $fontStyle);
				$table3DN->addCell(2000)->addText(htmlspecialchars(date('d-m-Y',strtotime($value['date_end']))), $fontStyle);
				$table3DN->addCell(2000)->addText(number_format($value['co_vat']), null, $style_number);
				$table3DN->addCell(2000)->addText('', null, $style_number);
				$stt++;
			}
		}

		$section->addText('Hồ Chí Minh:', 'hStyle');

		if (!empty($bao_cao_doanh_thu_tiep_theo_HCM)) {
			$phpWord->addTableStyle('table3HCM', $styleTable,null);
			$table3HCM = $section->addTable('table3HCM');
			$table3HCM->addRow();
			$table3HCM->addCell(2000)->addText('STT', $fontStyle_thead, $fontStyle);
			$table3HCM->addCell(2000)->addText('Tên dự án', $fontStyle_thead, $fontStyle);
			$table3HCM->addCell(2000)->addText('Tên dịch vụ', $fontStyle_thead, $fontStyle);
			$table3HCM->addCell(2000)->addText('Tên hợp đồng', $fontStyle_thead, $fontStyle);
			$table3HCM->addCell(2000)->addText('Ngày nghiệm thu/thanh lý dự kiến', $fontStyle_thead, $fontStyle);
			$table3HCM->addCell(2000)->addText('Tổng giá trị hợp đồng ( trừ VAT)', $fontStyle_thead, $fontStyle);
			$table3HCM->addCell(2000)->addText('Giá trị nghiệm thu/thanh lý dự kiến', $fontStyle_thead, $fontStyle);
			$stt=1;
			foreach ($bao_cao_doanh_thu_tiep_theo_HCM as $key => $value) {
				$table3HCM->addRow();
				$table3HCM->addCell(2000)->addText($stt, null, $fontStyle);
				$table3HCM->addCell(2000)->addText(htmlspecialchars($value['name_task']), $fontStyle);
				$table3HCM->addCell(2000)->addText(htmlspecialchars($value['item_name']), $fontStyle);
				$table3HCM->addCell(2000)->addText(htmlspecialchars($value['name_contract']), $fontStyle);
				$table3HCM->addCell(2000)->addText(htmlspecialchars(date('d-m-Y',strtotime($value['date_end']))), $fontStyle);
				$table3HCM->addCell(2000)->addText(number_format($value['co_vat']), null, $style_number);
				$table3HCM->addCell(2000)->addText('', null, $style_number);
				$stt++;
			}
		}

		$section->addText('Các chương trình hành động khác:');
		$section->addText('...');
		$section->addText('Định hướng giải quyết khó khăn tồn đọng và đề xuất:');
		$section->addText('...');

		$section->addTextBreak();
		$table_footer = $section->addTable('table_footer');
		$table_footer->addRow();
		$table_footer->addCell(6000)->addText('', $fontStyle_thead);
		$table_footer->addCell(6000)->addText('PHÒNG TƯ VẤN TÀI CHÍNH DOANH NGHIỆP', $fontStyle_thead);

		$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
		$filename = $file.' '.$month.' NĂM '.$year.'.docx';

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

	function download_year($year){
		$phpWord = new \PhpOffice\PhpWord\PhpWord();
		$phpWord->getCompatibility()->setOoxmlVersion(14);
		$phpWord->getCompatibility()->setOoxmlVersion(15);
		$section = $phpWord->addSection();
		$phpWord->setDefaultFontName('Times New Roman');
		$phpWord->setDefaultFontSize(12);
		$location = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id());

		$doanh_thu = parent::kpi_room_data_d7(array('select'=>'revenue','tp'=>'plan','year'=>$year))['dt'];
		if (!empty($doanh_thu)) {
			for ($i=0; $i < 4 ; $i++) { 
				if ($doanh_thu[$i]['location_id']==$location->location_id) {
					$locaton_data = $doanh_thu[$i]['location_data'];
					for ($j=1; $j <=4 ; $j++) { 
						$tong_doanhthu += $locaton_data[$j]['value'];
					}
				}
			}
		}
		

		// echo "<pre>"; print_r($location); die();


		$phpWord->addParagraphStyle('pStyle', ['align' => 'center', 'spaceAfter' => 100,'name'=>'Times New Roman']);
		$phpWord->addFontStyle('StyleHeader', ['bold' => true, 'size' => 16]);
		$phpWord->addFontStyle('hStyle', ['bold' => true]);
		$styleTable = ['borderSize' => 6, 'borderColor' => '006699', 'cellMargin' => 80];
		$phpWord->addFontStyle('center', ['align' => 'center']);
		$fontStyle_thead = array('bold' => true, 'align' => 'center');
		$font_bold = array('bold'=>true);
		$phpWord->addFontStyle('uStyle', ['underline' => true]);
		$fontStyle = array('align' => 'center','name'=>'Times New Roman');
		$stylebg = array('bgColor'=>'#00b300');

		$phpWord->addTableStyle('table_header','hStyle','pStyle');
		$table_header = $section->addTable('table_header');
		$table_header->addRow();
		$table_header->addCell(8000)->addText('CÔNG TY CHỨNG KHOÁN NHTMCPNTVN', $fontStyle_thead,$fontStyle);
		$table_header->addCell(6000)->addText('', $fontStyle_thead);
		$table_header->addRow();
		$table_header->addCell(8000)->addText('PHÒNG TƯ VẤN TÀI CHÍNH DOANH NGHIỆP ', $fontStyle_thead,$fontStyle);
		$table_header->addCell(6000)->addText('', $fontStyle_thead);
		$table_header->addRow();
		$table_header->addCell(8000)->addText('HỘI SỞ CHÍNH', $fontStyle_thead,$fontStyle);
		$table_header->addCell(6000)->addText('', $fontStyle_thead);
		$table_header->addRow();
		$so = 'Số  /2018/BC-TVTCDN';
		$datetime = $location->name.', ngày '.Date('d').', tháng '.Date('m').' năm '.Date('Y');

		$table_header->addCell(8000)->addText(htmlspecialchars($so), null,$fontStyle);
		$table_header->addCell(6000)->addText(htmlspecialchars($datetime),null, array('align' => 'center'));
		$section->addTextBreak();
		$section->addTextBreak();
		$title = "Kết quả kinh doanh cả năm $year và kế hoạch kinh doanh";
		$title2 ="Quý 1 năm ".($year+1);
		$section->addText('BÁO CÁO', 'StyleHeader', 'pStyle');
		$section->addText($title, 'StyleHeader', 'pStyle');
		$section->addText($title2, 'StyleHeader', 'pStyle');
		$section->addText('Phòng Tư vấn tài chính doanh nghiệp – Hội sở chính (Phòng TVTCDN – HSC) kính báo cáo Giám đốc Công ty xem xét, đánh giá kết quả công tác cả năm '.$year.' như sau:');
		$section->addText('A. HOẠT ĐỘNG TRONG CẢ NĂM '.$year, 'hStyle', null);
		$section->addText('1. Doanh thu ', 'hStyle', null);

		// Doanh thu nam hien tai;


		$section->addText('Lũy kế cả năm, doanh thu Phòng TVTCDN – '.$location->name.' ước đạt khoảng ... đồng, tương đương …% so với kế hoạch cả năm '.($year-1).'. Lũy kế cả năm, doanh thu TVTC toàn hệ thống ước đạt khoảng … đồng tương đương …% so với kế hoạch cả năm '.($year-1));
		$phpWord->addTableStyle('table', $styleTable,null);
		$table = $section->addTable('table');
		$table->addRow();
		$table->addCell(500)->addText('STT',  $font_bold, $fontStyle);
		$table->addCell(6000)->addText('Khoản mục', $font_bold, $fontStyle);
		$table->addCell(6000)->addText('Lũy kế cả năm dự kiến', $font_bold, $fontStyle);
		$table->addRow();
		$table->addCell(500)->addText('A', $fontStyle_thead);
		$table->addCell(6000)->addText('Hội sở chính',$font_bold, null);
		$table->addCell(6000)->addText('', $fontStyle);
		$table->addRow();
		$table->addCell(500)->addText('', $fontStyle);
		$table->addCell(6000)->addText('Doanh thu',$font_bold, null);
		$table->addCell(6000)->addText('', $fontStyle);
		$table->addRow();
		$table->addCell(500)->addText('1', null,$fontStyle);
		$table->addCell(6000)->addText('Doanh thu đại lý PHCK',array('italic'=>true),null);
		$table->addCell(6000)->addText('', $fontStyle);
		$table->addRow();
		$table->addCell(500)->addText('2', null,$fontStyle);
		$table->addCell(6000)->addText('Doanh thu HĐ Tư vấn',array('italic'=>true),null);
		$table->addCell(6000)->addText('', $fontStyle);
		$table->addRow();
		$table->addCell(500)->addText('3', null,$fontStyle);
		$table->addCell(6000)->addText('Doanh thu BLPH', array('italic'=>true),null);
		$table->addCell(6000)->addText('', $fontStyle);
		$table->addRow();
		$table->addCell(500)->addText('4', null,$fontStyle);
		$table->addCell(6000)->addText('Doanh thu Môi giới CP và lưu ký và các doanh thu khác', array('italic'=>true),null);
		$table->addCell(6000)->addText('', $fontStyle);

		$table->addRow();
		$table->addCell(500)->addText('B', $fontStyle_thead);
		$table->addCell(6000)->addText('Toàn hệ thống',$font_bold, null);
		$table->addCell(6000)->addText('', $fontStyle);
		$table->addRow();
		$table->addCell(500)->addText('', $fontStyle);
		$table->addCell(6000)->addText('Doanh thu', $font_bold, null);
		$table->addCell(6000)->addText('', $fontStyle);
		$table->addRow();
		$table->addCell(500)->addText('1', null,$fontStyle);
		$table->addCell(6000)->addText('Doanh thu đại lý PHCK', array('italic'=>true),null);
		$table->addCell(6000)->addText('', $fontStyle);
		$table->addRow();
		$table->addCell(500)->addText('2', null,$fontStyle);
		$table->addCell(6000)->addText('Doanh thu HĐ Tư vấn', array('italic'=>true),null);
		$table->addCell(6000)->addText('', $fontStyle);
		$table->addRow();
		$table->addCell(500)->addText('3', null,$fontStyle);
		$table->addCell(6000)->addText('Doanh thu BLPH', array('italic'=>true),null);
		$table->addCell(6000)->addText('', $fontStyle);
		$table->addRow();
		$table->addCell(500)->addText('4', null,$fontStyle);
		$table->addCell(6000)->addText('Doanh thu Môi giới CP và lưu ký và các doanh thu khác',array('italic'=>true),null);
		$table->addCell(6000)->addText('', $fontStyle);
		$section->addTextBreak();
		$section->addText('2. Các hoạt động khác:', 'hStyle', null);
		$section->addText('Ngoài các hoạt động tạo doanh thu như mục 1 nêu trên, trong năm '.$year.', Phòng TVTCDN – '.$location->code.' đã thực hiện các công tác khác như: ');
		$section->addText('Công tác khách hàng:','hStyle');
		$section->addText('-	…');
		$section->addText('-	…');
		$section->addText('-	…');
		$section->addText('Xử lý một số tồn đọng về các khuyến nghị của kiểm toán:','hStyle');
		$section->addText('-	…');
		$section->addText('-	…');
		$section->addText('-	…');
		$section->addText('B. ĐÁNH GIÁ CHUNG', 'hStyle','uStyle');
		$section->addText('Phân tích kết quả doanh thu, nguyên nhân, giải pháp. ');
		$section->addText('Hoàn thành/không hoàn thành kế hoạch, nguyên nhân của kết quả thực hiện kế hoạch.');

		$quy_tiep = $this->quater_next();
		if ($quy_tiep==1) {
			$y = (int)Date('Y')+1;
		}else{
			$y = (int)Date('Y');
		}
		$y = ($year+1);
		if ($quy_tiep=1) {
			$c = "C. KẾ HOẠCH HOẠT ĐỘNG KINH DOANH QUÝ $quy_tiep/$y";
		}else{
			$c = "C. KẾ HOẠCH HOẠT ĐỘNG KINH DOANH QUÝ $quy_tiep/$y";
		}



		$doanh_thu_quy_tiep = parent::kpi_room_data_d7(array('select'=>'revenue','tp'=>'plan','year'=>$y,'quater'=>1))['dt'];
		$tong_doanh_thu_HSC =0;
		$tong_doanh_thu_DN =0;
		$tong_doanh_thu_HCM =0;
		$tong_doanh_thu_toan_he_thong =0;
		// echo "<pre>"; print_r($doanh_thu_quy_tiep); die();
		if (!empty($doanh_thu_quy_tiep)) {
			$location_data = $doanh_thu_quy_tiep[3]['location_data'];
			$location_data_HSC = $doanh_thu_quy_tiep[0]['location_data'];
			$location_data_DN = $doanh_thu_quy_tiep[1]['location_data'];
			$location_data_HCM = $doanh_thu_quy_tiep[2]['location_data'];
			for ($i=1; $i <=4 ; $i++) { 
				$tong_doanh_thu_toan_he_thong += $location_data[$i]['value'];
				$tong_doanh_thu_HSC += $location_data_HSC[$i]['value'];
				$tong_doanh_thu_DN += $location_data_DN[$i]['value'];
				$tong_doanh_thu_HCM += $location_data_HCM[$i]['value'];
			}
		}

		// echo "<pre>"; print_r($doanh_thu_quy_tiep); die();
		$section->addText($c, 'hStyle','uStyle');
		// $section->addText("Doanh thu quý $quy_tiep/$y của toàn hệ thống ước đạt ".number_format($tong_doanh_thu_toan_he_thong)." đồng, trong đó:");
		$section->addText("Doanh thu quý $quy_tiep/$y của toàn hệ thống ước đạt ... đồng, trong đó:");
		
		$date_end = date_format(date_modify(date_create($y.'-01-01'),"+3 months"),"Y-m-d");
		$date_start = date_format(date_create($y.'-01-01'),"Y-m-d");

		$tasks_progress_HSC = $this->db->select('t.name,t.id,t.date_end')
		->from('phppos_sales s')
		->join('phppos_tasks t','t.project_id = s.task_id')
		->join('phppos_locations lc','s.location_id=lc.location_id')
		->where('lc.code','HSC')
		->where('t.level',0)
		->where('t.pheduyet !=',1)->where('t.date_end <',$date_end)->where('t.date_end >=',$date_start)->get()->result_array();
		$tasks_progress_HCM = $this->db->select('t.name,t.id,t.date_end')
		->from('phppos_sales s')
		->join('phppos_tasks t','t.project_id = s.task_id')
		->join('phppos_locations lc','s.location_id=lc.location_id')
		->where('lc.code','HCM')
		->where('t.level',0)
		->where('t.pheduyet !=',1)->where('t.date_end <',$date_end)->where('t.date_end >=',$date_start)->get()->result_array();
		$tasks_progress_DN = $this->db->select('t.name,t.id,t.date_end')
		->from('phppos_sales s')
		->join('phppos_tasks t','t.project_id = s.task_id')
		->join('phppos_locations lc','s.location_id=lc.location_id')
		->where('lc.code','DN')
		->where('t.level',0)
		->where('t.pheduyet !=',1)->where('t.date_end <',$date_end)->where('t.date_end >=',$date_start)->get()->result_array();

		$giai_doan_dang_thuc_hien_HSC = $this->Contract->giai_doan_dang_thuc_hien('HSC',array('date_end'=>$date_end,'date_start'=>$date_start));
		$giai_doan_dang_thuc_hien_DN = $this->Contract->giai_doan_dang_thuc_hien('DN',array('date_end'=>$date_end,'date_start'=>$date_start));
		$giai_doan_dang_thuc_hien_HCM = $this->Contract->giai_doan_dang_thuc_hien('HCM',array('date_end'=>$date_end,'date_start'=>$date_start));
		// echo "<pre>"; print_r($tasks_progress_HSC); die();
		$section->addText('- Hội sở chính:', 'hStyle', null);
		$section->addText('• Trong quý này, Hội sở chính sẽ tiến hành hoàn thành các công việc sau:');

		if (!empty($tasks_progress_HSC)) {
			foreach ($tasks_progress_HSC as $key => $value_t) {
				$section->addText(htmlspecialchars('- '.$value_t['name']),'hStyle',null);
				if (!empty($giai_doan_dang_thuc_hien_HSC)) {
					foreach ($giai_doan_dang_thuc_hien_HSC as $key => $value) {
						if ($value_t['id']==$value['project_id']) {
							$section->addText(htmlspecialchars('   + '.$value['name']));
						}
					}
				}
			}
		}
		

		$section->addText('• Doanh thu dự kiến ước đạt ... đồng.');

		$bao_cao_doanh_thu_tiep_theo_HSC = $this->Contract->contract_location(array('date_end'=>true,'check_tp'=>'QUY','ctstatus'=>array('progress'),'location'=>'HSC','month'=>$quy_tiep,'year'=>$y));
		$bao_cao_doanh_thu_tiep_theo_DN = $this->Contract->contract_location(array('date_end'=>true,'check_tp'=>'QUY','ctstatus'=>array('progress'),'location'=>'DN','month'=>$quy_tiep,'year'=>$y));
		$bao_cao_doanh_thu_tiep_theo_HCM = $this->Contract->contract_location(array('date_end'=>true,'check_tp'=>'QUY','ctstatus'=>array('progress'),'location'=>'HCM','month'=>$quy_tiep,'year'=>$y));

		
		if (!empty($bao_cao_doanh_thu_tiep_theo_HSC)) {
			$phpWord->addTableStyle('table3HSC', $styleTable,null);
			$table3HSC = $section->addTable('table3HSC');
			$table3HSC->addRow();
			$table3HSC->addCell(2000)->addText('STT', $fontStyle_thead, $fontStyle);
			$table3HSC->addCell(2000)->addText('Tên dự án', $fontStyle_thead, $fontStyle);
			$table3HSC->addCell(2000)->addText('Tên dịch vụ', $fontStyle_thead, $fontStyle);
			$table3HSC->addCell(2000)->addText('Tên hợp đồng', $fontStyle_thead, $fontStyle);
			$table3HSC->addCell(2000)->addText('Ngày nghiệm thu/thanh lý dự kiến', $fontStyle_thead, $fontStyle);
			$table3HSC->addCell(2000)->addText('Tổng giá trị hợp đồng ( trừ VAT)', $fontStyle_thead, $fontStyle);
			$table3HSC->addCell(2000)->addText('Giá trị nghiệm thu/thanh lý dự kiến', $fontStyle_thead, $fontStyle);
			$stt=1;
			foreach ($bao_cao_doanh_thu_tiep_theo_HSC as $key => $value) {
				$table3HSC->addRow();
				$table3HSC->addCell(2000)->addText($stt, null, $fontStyle);
				$table3HSC->addCell(2000)->addText(htmlspecialchars($value['name_task']), $fontStyle);
				$table3HSC->addCell(2000)->addText(htmlspecialchars($value['item_name']), $fontStyle);
				$table3HSC->addCell(2000)->addText(htmlspecialchars($value['name_contract']), $fontStyle);
				$table3HSC->addCell(2000)->addText(htmlspecialchars(date('d-m-Y',strtotime($value['date_end']))), $fontStyle);
				$table3HSC->addCell(2000)->addText(number_format($value['co_vat']), null,  $style_number);
				$table3HSC->addCell(2000)->addText('', null,  $style_number);
				$stt++;
			}
		}
		$section->addTextBreak();
		$section->addText('- Chi nhánh Đà Nẵng:', 'hStyle', null);
		$section->addText('• Trong quý này, Hội sở chính sẽ tiến hành hoàn thành các công việc sau:');
		if (!empty($tasks_progress_DN)) {
			foreach ($tasks_progress_DN as $key => $value_t) {
				$section->addText(htmlspecialchars('- '.$value_t['name']),'hStyle',null);
				if (!empty($giai_doan_dang_thuc_hien_DN)) {
					foreach ($giai_doan_dang_thuc_hien_DN as $key => $value) {
						if ($value_t['id']==$value['project_id']) {
							$section->addText(htmlspecialchars('   + '.$value['name']));
						}
					}
				}
			}
		}

		$section->addText('• Doanh thu dự kiến ước đạt ... đồng.');
		
		if (!empty($bao_cao_doanh_thu_tiep_theo_DN)) {
			$phpWord->addTableStyle('table3DN', $styleTable,null);
			$table3DN = $section->addTable('table3DN');
			$table3DN->addRow();
			$table3DN->addCell(2000)->addText('STT', $fontStyle_thead, $fontStyle);
			$table3DN->addCell(2000)->addText('Tên dự án', $fontStyle_thead, $fontStyle);
			$table3DN->addCell(2000)->addText('Tên dịch vụ', $fontStyle_thead, $fontStyle);
			$table3DN->addCell(2000)->addText('Tên hợp đồng', $fontStyle_thead, $fontStyle);
			$table3DN->addCell(2000)->addText('Ngày nghiệm thu/thanh lý dự kiến', $fontStyle_thead, $fontStyle);
			$table3DN->addCell(2000)->addText('Tổng giá trị hợp đồng ( trừ VAT)', $fontStyle_thead, $fontStyle);
			$table3DN->addCell(2000)->addText('Giá trị nghiệm thu/thanh lý dự kiến', $fontStyle_thead, $fontStyle);
			$stt=1;
			foreach ($bao_cao_doanh_thu_tiep_theo_DN as $key => $value) {
				$table3DN->addRow();
				$table3DN->addCell(2000)->addText($stt, null, $fontStyle);
				$table3DN->addCell(2000)->addText(htmlspecialchars($value['name_task']), $fontStyle);
				$table3DN->addCell(2000)->addText(htmlspecialchars($value['item_name']), $fontStyle);
				$table3DN->addCell(2000)->addText(htmlspecialchars($value['name_contract']), $fontStyle);
				$table3DN->addCell(2000)->addText(htmlspecialchars(date('d-m-Y',strtotime($value['date_end']))), $fontStyle);
				$table3DN->addCell(2000)->addText(number_format($value['co_vat']), null,  $style_number);
				$table3DN->addCell(2000)->addText('', null,  $style_number);
				$stt++;
			}
		}
		$section->addTextBreak();

		$section->addText('- Chi nhánh Hồ Chí Minh:', 'hStyle', null);
		$section->addText('• Trong quý này, Hội sở chính sẽ tiến hành hoàn thành các công việc sau:');
		if (!empty($tasks_progress_HCM)) {
			foreach ($tasks_progress_HCM as $key => $value_t) {
				$section->addText(htmlspecialchars('- '.$value_t['name']),'hStyle',null);
				if (!empty($giai_doan_dang_thuc_hien_HCM)) {
					foreach ($giai_doan_dang_thuc_hien_HCM as $key => $value) {
						if ($value_t['id']==$value['project_id']) {
							$section->addText(htmlspecialchars('   + '.$value['name']));
						}
					}
				}
			}
		}

		$section->addText('• Doanh thu dự kiến ước đạt ... đồng.');
		if (!empty($bao_cao_doanh_thu_tiep_theo_HCM)) {
			$phpWord->addTableStyle('table3HCM', $styleTable,null);
			$table3HCM = $section->addTable('table3HCM');
			$table3HCM->addRow();
			$table3HCM->addCell(2000)->addText('STT', $fontStyle_thead, $fontStyle);
			$table3HCM->addCell(2000)->addText('Tên dự án', $fontStyle_thead, $fontStyle);
			$table3HCM->addCell(2000)->addText('Tên dịch vụ', $fontStyle_thead, $fontStyle);
			$table3HCM->addCell(2000)->addText('Tên hợp đồng', $fontStyle_thead, $fontStyle);
			$table3HCM->addCell(2000)->addText('Ngày nghiệm thu/thanh lý dự kiến', $fontStyle_thead, $fontStyle);
			$table3HCM->addCell(2000)->addText('Tổng giá trị hợp đồng ( trừ VAT)', $fontStyle_thead, $fontStyle);
			$table3HCM->addCell(2000)->addText('Giá trị nghiệm thu/thanh lý dự kiến', $fontStyle_thead, $fontStyle);
			$stt=1;
			foreach ($bao_cao_doanh_thu_tiep_theo_HCM as $key => $value) {
				$table3HCM->addRow();
				$table3HCM->addCell(2000)->addText($stt, null, $fontStyle);
				$table3HCM->addCell(2000)->addText(htmlspecialchars($value['name_task']), $fontStyle);
				$table3HCM->addCell(2000)->addText(htmlspecialchars($value['item_name']), $fontStyle);
				$table3HCM->addCell(2000)->addText(htmlspecialchars($value['name_contract']), $fontStyle);
				$table3HCM->addCell(2000)->addText(htmlspecialchars(date('d-m-Y',strtotime($value['date_end']))), $fontStyle);
				$table3HCM->addCell(2000)->addText(number_format($value['co_vat']), null,  $style_number);
				$table3HCM->addCell(2000)->addText('', null,  $style_number);
				$stt++;
			}
		}


		$section->addTextBreak();
		$code_location = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id())->code;
		$phpWord->addTableStyle('table_footer', NULL,null);
		$table_footer = $section->addTable('table_footer');
		$table_footer->addRow();
		$table_footer->addCell(6000)->addText('',NULL);
		$table_footer->addCell(6000)->addText('PHÒNG TƯ VẤN TÀI CHÍNH DOANH NGHIỆP',$fontStyle_thead,$fontStyle);

		$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
		$filename = 'Bao-cao-gui-phong-phan-tich-nam-'.$year.'.docx';
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