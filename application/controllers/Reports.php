<?php
require_once ("Secure_area.php");
class Reports extends Secure_area 
{
	protected $_paginator = array(
		'per_page' => 10,
		'uri_segment' => 3
	);

	function __construct()
	{
		parent::__construct('reports');
		$this->load->helper('report');
		//$this->load->model('report');
		$this->has_profit_permission = $this->Employee->has_module_action_permission('reports','show_profit',$this->Employee->get_logged_in_employee_info()->person_id);
		$this->has_cost_price_permission = $this->Employee->has_module_action_permission('reports','show_cost_price',$this->Employee->get_logged_in_employee_info()->person_id);
		
		//Need to query database directly as load config hook doesn't happen until after constructor
		$this->decimals = $this->Appconfig->get_raw_number_of_decimals();
		$this->decimals = $this->decimals !== NULL && $this->decimals!= '' ? $this->decimals : 2;
		require_once (APPPATH.'models/reports/Report.php');
		$this->load->vars(array('reports_selected_location_ids' => Report::get_selected_location_ids()));
		$this->lang->load('reports');
		$this->lang->load('module');
		$this->load->model('Category');
		$this->load->model('Customer');
		$this->load->helper('report');
		$this->load->library('sale_lib');
		$this->load->library('PHPExcel');
		$this->load->library('PHPWord');
		$this->load->model('Kpi_Person');
		$this->load->model('Appfile');
	}

	function set_selected_location_ids()
	{
		$this->session->set_userdata('reports_selected_location_ids', $this->input->post('reports_selected_location_ids'));
	}

	//Initial report listing screen
	function index()
	{
		$data = array();
		$shift_category_id       = $this->config->item('shift_category_id');
		if($shift_category_id > 0) {
			$category = $this->Category->getItem($shift_category_id);
			$data['category'] = $category;
		}
        // echo "1";die();
		$this->load->view("reports/listing", $data);
	}

	// Sales Generator Reports 
	function sales_generator() {	

		
		if ($this->input->get('act') == 'autocomplete') { // Must return a json string
			if ($this->input->get('w') != '') { // From where should we return data
				if ($this->input->get('term') != '') { // What exactly are we searchin
					
					//allow parallel searchs to improve performance.
					session_write_close();
					
					switch($this->input->get('w')) {
						case 'customers': 
						$this->load->model('Customer');
						$t = $this->Customer->search($this->input->get('term'), 100, 0, 'last_name', 'asc')->result_object();
						$tmp = array();
						foreach ($t as $k=>$v) { 
							$display_name = $v->last_name.", ".$v->first_name;

							if ($v->email)
							{
								$display_name.=" - ".$v->email;
							}

							if ($v->phone_number)
							{
								$display_name.=" - ".$v->phone_number;
							}

							$tmp[$k] = array('id'=>$v->person_id, 'name'=>$display_name); 
						}
						die(json_encode($tmp));
						break;
						case 'employees':
						case 'salesPerson':
						$t = $this->Employee->search($this->input->get('term'), 100, 0, 'last_name', 'asc')->result_object();
						$tmp = array();
						foreach ($t as $k=>$v) { $tmp[$k] = array('id'=>$v->person_id, 'name'=>$v->last_name.", ".$v->first_name." - ".$v->email); }
						die(json_encode($tmp));
						break;
						case 'itemsCategory':
						$this->load->model('Category');
						$t = $this->Category->get_search_suggestions($this->input->get('term'));
						$tmp = array();
						foreach ($t as $k=>$v) { $tmp[$k] = array('id'=>$v['id'], 'name'=>$v['label']); }
						die(json_encode($tmp));
						break;
						case 'suppliers':
						$this->load->model('Supplier');
						$t = $this->Supplier->search($this->input->get('term'), 100, 0, 'last_name', 'asc')->result_object();
						$tmp = array();
						foreach ($t as $k=>$v) { $tmp[$k] = array('id'=>$v->person_id, 'name'=>$v->last_name.", ".$v->first_name." - ".$v->company_name." - ".$v->email); }
						die(json_encode($tmp));
						break;
						case 'itemsKitName':
						$this->load->model('Item_kit');
						$t = $this->Item_kit->search($this->input->get('term'), 100, 0, 'name', 'asc')->result_object();
						$tmp = array();
						foreach ($t as $k=>$v) { $tmp[$k] = array('id'=>$v->item_kit_id, 'name'=>$v->name." / #".$v->item_kit_number); }
						die(json_encode($tmp));
						break;
						case 'itemsName':
						$this->load->model('Item');
						$t = $this->Item->get_item_search_suggestions($this->input->get('term'));
						$tmp = array();
						foreach ($t as $v) { $tmp[] = array('id'=>$v['value'], 'name'=>$v['label']); }
						die(json_encode($tmp));
						break;
						case 'paymentType':
						$t = array(lang('common_cash'),lang('common_check'), lang('common_giftcard'),lang('common_debit'),lang('common_credit'));

						if($this->config->item('customers_store_accounts')) 
						{
							$t[] =lang('common_store_account');
						}

						foreach($this->Appconfig->get_additional_payment_types() as $additional_payment_type)
						{
							$t[] = $additional_payment_type;
						}

						$tmp = array();
						foreach ($t as $k => $v) { $tmp[$k] = array('id'=>$v, 'name'=>$v); }
						die(json_encode($tmp));
						break;		
					}
				} else {
					die;	
				}
			} else {
				die(json_encode(array('value' => 'No such data found!')));
			}
		}		
		
		$data = $this->_get_common_report_data();
		$data["title"] = lang('reports_sales_generator');
		$data["subtitle"] = lang('reports_sales_report_generator');
		
		$setValues = array(	'
			report_type' => '', 'sreport_date_range_simple' => '', 
			'start_month' => date("m"), 'start_day' => date('d'), 'start_year' => date("Y"),
			'end_month' => date("m"), 'end_day' => date('d'), 'end_year' => date("Y"),
			'matchType' => '',
			'matched_items_only' => FALSE,
			'tax_exempt' => FALSE,
		);

		foreach ($setValues as $k => $v) { 
			if (empty($v) && !isset($data[$k])) { 
				$data[$k] = ''; 		
			} else {
				$data[$k] = $v;
			}
		}		
		if ($this->input->get('generate_report')) { // Generate Custom Raport
			$data['report_type'] = $this->input->get('report_type');
			$data['sreport_date_range_simple'] = $this->input->get('report_date_range_simple');
			
			
			if ($data['report_type'] == 'simple') {
				$q = explode("/", $data['sreport_date_range_simple']);
				list($data['start_year'], $data['start_month'], $data['start_day']) = explode("-", $q[0]);
				list($data['end_year'], $data['end_month'], $data['end_day']) = explode("-", $q[1]);
			}
			else
			{
				list($data['start_year'], $data['start_month'], $data['start_day']) = explode("-", $this->input->get('start_date'));
				list($data['end_year'], $data['end_month'], $data['end_day']) = explode("-", $this->input->get('end_date'));
			}
			$data['matchType'] = $this->input->get('matchType');
			$data['matched_items_only'] = $this->input->get('matched_items_only') ? TRUE : FALSE;
			$data['tax_exempt'] = $this->input->get('tax_exempt') ? TRUE : FALSE;

			$data['field'] = $this->input->get('field');
			$data['condition'] = $this->input->get('condition');
			$data['value'] = $this->input->get('value');
			
			$data['prepopulate'] = array();
			
			$field = $this->input->get('field');
			$condition = $this->input->get('condition');
			$value = $this->input->get('value');
			
			$tmpData = array();
			foreach ($field as $a => $b) {
				$uData = explode(",",$value[$a]);
				$tmp = $tmpID = array();
				switch ($b) {
					case '1': // Customer
					$this->load->model('Customer');
					$t = $this->Customer->get_multiple_info($uData)->result_object();
					foreach ($t as $k=>$v) { $tmpID[] = $v->person_id; $tmp[$k] = array('id'=>$v->person_id, 'name'=>$v->last_name.", ".$v->first_name." - ".$v->email); }
					break;
					case '2': // Item Serial Number
					$tmpID[] = $value[$a];
					break;
					case '3': // Employees
					$t = $this->Employee->get_multiple_info($uData)->result_object();
					foreach ($t as $k=>$v) { $tmpID[] = $v->person_id;  $tmp[$k] = array('id'=>$v->person_id, 'name'=>$v->last_name.", ".$v->first_name." - ".$v->email); }
					break;
					case '4': // Items Category
					$this->load->model('Category');
					$t = $this->Category->get_multiple_info($uData)->result_object();
					foreach ($t as $k=>$v) { $tmpID[] = $v->id;  $tmp[$k] = array('id'=>$v->id, 'name'=>$v->name); }
					break;
					case '5': // Suppliers 
					$this->load->model('Supplier');
					$t = $this->Supplier->get_multiple_info($uData)->result_object();
					foreach ($t as $k=>$v) { $tmpID[] = $v->person_id;  $tmp[$k] = array('id'=>$v->person_id, 'name'=>$v->last_name.", ".$v->first_name." - ".$v->company_name." - ".$v->email); }
					break;
					case  '6': // Sale Type
					$tmpID[] = $condition[$a];
					break;
					case '7': // Sale Amount
					$tmpID[] = $value[$a];
					break;
					case '8': // Item Kits
					$this->load->model('Item_kit');
					$t = $this->Item_kit->get_multiple_info($uData)->result_object();
					foreach ($t as $k=>$v) { $tmpID[] = $v->item_kit_id;  $tmp[$k] = array('id'=>$v->item_kit_id, 'name'=>$v->name." / #".$v->item_kit_number); }
					break;
					case '9': // Items Name
					$this->load->model('Item');
					$t = $this->Item->get_multiple_info($uData)->result_object();
					foreach ($t as $k => $v) { $tmpID[] = $v->item_id;  $tmp[$k] = array('id'=>$v->item_id, 'name'=>$v->name); }
					break;				
					case '10': // SaleID
					if(strpos(strtolower($value[$a]), strtolower($this->config->item('sale_prefix'))) !== FALSE)
					{							
						$value[$a] =(int)substr(strtolower($value[$a]), strpos(strtolower($value[$a]),$this->config->item('sale_prefix').' ') + strlen(strtolower($this->config->item('sale_prefix')).' '));	
					}
					$tmpID[] = $value[$a];
					break;
					case '11': // Payment type
					foreach ($uData as $k=>$v) { $tmpID[] = $v;  $tmp[$k] = array('id'=>$v, 'name'=>$v); }
					break;
					
					case '12': // Sale Item Description
					$tmpID[] = $value[$a];
					break;
					case '13': // Employees
					$t = $this->Employee->get_multiple_info($uData)->result_object();
					foreach ($t as $k=>$v) { $tmpID[] = $v->person_id;  $tmp[$k] = array('id'=>$v->person_id, 'name'=>$v->last_name.", ".$v->first_name." - ".$v->email); }
					break;

					
				}
				$data['prepopulate']['field'][$a][$b] = $tmp;			

				// Data for sql
				$tmpData[] = array('f' => $b, 'o' => $condition[$a], 'i' => $tmpID);
			}
			
			$params['matchType'] = $data['matchType'];
			$params['matched_items_only'] = $data['matched_items_only'];
			$params['tax_exempt'] = $data['tax_exempt'];
			$params['ops'] = array(
				1 => " = 'xx'", 
				2 => " != 'xx'", 
				5 => " IN ('xx')", 
				6 => " NOT IN ('xx')", 
				7 => " > xx", 
				8 => " < xx", 
				9 => " = xx",
												10 => '', // Sales
												11 => '', // Returns
											);

			$params['tables'] = array(
								1 => 'sales_items_temp.customer_id', // Customers
								2 => 'sales_items_temp.serialnumber', // Item Sale Serial number
								3 => 'sales_items_temp.employee_id', // Employees
								4 => 'sales_items_temp.category_id', // Item Category
								5 => 'sales_items_temp.supplier_id', // Suppliers
								6 => '', // Sale Type
								7 => '', // Sale Amount
								8 => 'sales_items_temp.item_kit_id', // Item Kit Name
								9 => 'sales_items_temp.item_id', // Item Name
								10 => 'sales_items_temp.sale_id', // Sale ID
								11 => 'sales_items_temp.payment_type', // Payment Type
								12 => 'sales_items_temp.description', // Item Sale Serial number
								13 => 'sales_items_temp.sold_by_employee_id', // Item Sale Serial number
							);			
			$params['values'] = $tmpData;
			$params['offset'] = $this->input->get('per_page')  ? $this->input->get('per_page') : 0;
			$params['export_excel'] = $this->input->get('export_excel') ? 1 : 0;
			
			$this->load->model('reports/Sales_generator');
			$model = $this->Sales_generator;
			$model->setParams($params);			

			// Sales Interval Reports
			$interval = array(
				'start_date' => $data['start_year'].'-'.$data['start_month'].'-'.$data['start_day'], 
				'end_date' => $data['end_year'].'-'.$data['end_month'].'-'.$data['end_day']. ' 23:59:59'									
			);
			$this->load->model('Sale');
			$this->Sale->create_sales_items_temp_table($interval);
			$config = array();
			
			//Remove per_page from url so we don't have it duplicated
			$config['base_url'] = preg_replace('/&per_page=[0-9]*/','',current_url());
			$config['total_rows'] = $model->getTotalRows();
			$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
			$config['page_query_string'] = TRUE;
			$this->load->library('pagination');$this->pagination->initialize($config);
			
			$tabular_data = array();
			$report_data = $model->getData();
			
			$summary_data = array();
			$details_data = array();
			
			$location_count = count(Report::get_selected_location_ids());			
			
			foreach($report_data['summary'] as $key=>$row)
			{
				$summary_data_row = array();				
				$summary_data_row[] = array('data'=>anchor('sales/receipt/'.$row['sale_id'], '<i class="ion-printer"></i>', array('target' => '_blank')).' '.anchor('sales/edit/'.$row['sale_id'], '<i class="ion-document-text"></i>', array('target' => '_blank')).' '.anchor('sales/edit/'.$row['sale_id'], lang('common_edit').' '.$row['sale_id'], array('target' => '_blank')), 'align'=>'left');
				
				if ($location_count > 1)
				{
					$summary_data_row[] = array('data'=>$row['location_name'], 'align'=>'left');
				}
				
				$summary_data_row[] = array('data'=>date(get_date_format().'-'.get_time_format(), strtotime($row['sale_time'])), 'align'=>'left');
				$summary_data_row[] = array('data'=>$row['register_name'], 'align'=>'left');
				$summary_data_row[] = array('data'=>to_quantity($row['items_purchased']), 'align'=>'center');
				$summary_data_row[] = array('data'=>$row['employee_name'].($row['sold_by_employee'] && $row['sold_by_employee'] != $row['employee_name'] ? '/'. $row['sold_by_employee']: ''), 'align'=>'left');
				$summary_data_row[] = array('data'=>$row['customer_name'].(isset($row['account_number']) && $row['account_number'] ? ' ('.$row['account_number'].')' : ''), 'align'=>'left');
				$summary_data_row[] = array('data'=>to_currency($row['subtotal']), 'align'=>'right');
				$summary_data_row[] = array('data'=>to_currency($row['total']), 'align'=>'right');
				$summary_data_row[] = array('data'=>to_currency($row['tax']), 'align'=>'right');
				
				if($this->has_profit_permission)
				{
					$summary_data_row[] = array('data'=>to_currency($row['profit']), 'align'=>'right');
				}

				$summary_data_row[] = array('data'=>$row['payment_type'], 'align'=>'right');
				$summary_data_row[] = array('data'=>$row['comment'], 'align'=>'right');

				
				$summary_data[$key] = $summary_data_row;
				
				foreach($report_data['details'][$key] as $drow)
				{
					$details_data_row = array();

					$details_data_row[] = array('data'=>isset($drow['item_number']) ? $drow['item_number'] : $drow['item_kit_number'], 'align'=>'left');
					$details_data_row[] = array('data'=>isset($drow['item_product_id']) ? $drow['item_product_id'] : $drow['item_kit_product_id'], 'align'=>'left');
					$details_data_row[] = array('data'=>isset($drow['item_name']) ? anchor('items/view/'.$drow['item_id'],$drow['item_name']) : anchor('item_kits/view/'.$drow['item_kit_id'],$drow['item_kit_name']), 'align'=>'left');
					$details_data_row[] = array('data'=>$drow['category'], 'align'=>'left');
					$details_data_row[] = array('data'=>$drow['size'], 'align'=>'left');
					$details_data_row[] = array('data'=>$drow['serialnumber'], 'align'=>'left');
					$details_data_row[] = array('data'=>$drow['description'], 'align'=>'left');
					$details_data_row[] = array('data'=>to_currency($drow['current_selling_price']), 'align'=>'left');
					$details_data_row[] = array('data'=>to_quantity($drow['quantity_purchased']), 'align'=>'left');
					$details_data_row[] = array('data'=>to_currency($drow['subtotal']), 'align'=>'right');
					$details_data_row[] = array('data'=>to_currency($drow['total']), 'align'=>'right');
					$details_data_row[] = array('data'=>to_currency($drow['tax']), 'align'=>'right');

					if($this->has_profit_permission)
					{
						$details_data_row[] = array('data'=>to_currency($drow['profit']), 'align'=>'right');					
					}
					
					if($this->has_cost_price_permission)
					{
						$details_data_row[] = array('data'=>to_currency($drow['sale_item_temp_cost_price']), 'align'=>'right');
					}
					

					$details_data_row[] = array('data'=>$drow['discount_percent'].'%', 'align'=>'left');
					$details_data[$key][] = $details_data_row;
					
				}
			}
			
			$reportdata = array(
				"title" => lang('reports_sales_generator'),
				"subtitle" => lang('reports_sales_report_generator')." - ".date(get_date_format(), strtotime($interval['start_date'])) .'-'.date(get_date_format(), strtotime($interval['end_date']))." - ".$config['total_rows'].' '.lang('reports_sales_report_generator_results_found'),
				"headers" => $model->getDataColumns(),
				"summary_data" => $summary_data,
				"details_data" => $details_data,
				"overall_summary_data" => $model->getSummaryData(),
				'pagination' => $this->pagination->create_links(),
				'export_excel' =>$this->input->get('export_excel'),
			);
			
			// Fetch & Output Data 
			
			if (!$this->input->get('export_excel'))
			{
				$data['results'] = $this->load->view("reports/sales_generator_tabular_details", $reportdata, true);	
			}
		}	
		
		if (!$this->input->get('export_excel'))
		{
			$this->load->view("reports/sales_generator",$data);
		}
		else //Excel export use regular tabular_details
		{
			$this->load->view("reports/tabular_details",$reportdata);
		}
	}	
	
	function _get_common_report_data($time=false)
	{
		$data = array();
		$data['report_date_range_simple'] = get_simple_date_ranges($time);
		$data['months'] = get_months();
		$data['days'] = get_days();
		$data['years'] = get_years();
		$data['hours'] = get_hours($this->config->item('time_format'));
		$data['minutes'] = get_minutes();
		$data['selected_month']=date('m');
		$data['selected_day']=date('d');
		$data['selected_year']=date('Y');
		$data['intervals'] = get_time_intervals();	

		return $data;
	}
	
	function _get_simple_date_ranges_expire()
	{	
		$data = array();
		$data['report_date_range_simple'] = get_simple_date_ranges_expire();
		$data['months'] = get_months();
		$data['days'] = get_days();
		$data['years'] = get_years();
		$data['hours'] = get_hours($this->config->item('time_format'));
		$data['minutes'] = get_minutes();
		$data['selected_month']=date('m');
		$data['selected_day']=date('d');
		$data['selected_year']=date('Y');
		$data['intervals'] = get_time_intervals();
		
		return $data;	

	}
	
	//Input for reports that require only a date range and an export to excel. (see routes.php to see that all summary reports route here)
	function date_input_excel_export()
	{
		$data = $this->_get_common_report_data(TRUE);
		
		$this->load->view("reports/date_input_excel_export",$data);	
	}
	
	function date_input_excel_export_customers()
	{
		$data = $this->_get_common_report_data(TRUE);
		
		$this->load->view("reports/date_input_excel_export_customers",$data);			
	}
	
	function date_input_excel_export_compare()
	{
		$data = $this->_get_common_report_data(TRUE);
		
		$this->load->view("reports/date_input_excel_export_compare",$data);	
		
	}
	
	function date_input_excel_export_time()
	{
		$data = $this->_get_common_report_data(TRUE);
		
		$this->load->view("reports/date_input_excel_export_time",$data);	
	}
	
	function date_input_excel_export_store_account_activity()
	{
		$data = $this->_get_common_report_data(TRUE);
		
		$this->load->view("reports/date_input_excel_export_store_account_activity",$data);	
	}

	function day_input_excel_export()
	{		
		$data = $this->_get_common_report_data(TRUE);
		
		$this->load->view("reports/day_input_excel_export",$data);	
	}
	
	function suspended_date_input_excel_export()
	{
		$data = $this->_get_common_report_data(TRUE);
		
		$this->load->view("reports/suspended_date_input_excel_export",$data);	
	}
	
	function employees_date_input_excel_export()
	{
		$data = $this->_get_common_report_data(TRUE);
		$data['no_excel'] = true;
		
		$this->load->view("reports/employees_date_input_excel_export",$data);	
		
	}
	
	/** added for register log */
	function date_input_no_sales()
	{
		$data = $this->_get_common_report_data();
		$locations = array();
		foreach($this->Location->get_all()->result() as $location_row) 
		{
			$locations[$location_row->location_id] = $location_row->name;
		}
		$data['locations'] = $locations;
		$data['can_view_inventory_at_all_locations'] = $this->Employee->has_module_action_permission('reports','view_inventory_at_all_locations', $this->Employee->get_logged_in_employee_info()->person_id);
		
		$this->load->view("reports/date_input_no_sales",$data);	
	}
	
	function date_input_no_sales_expire()
	{
		$data = $this->_get_simple_date_ranges_expire();
		$locations = array();
		foreach($this->Location->get_all()->result() as $location_row) 
		{
			$locations[$location_row->location_id] = $location_row->name;
		}
		$data['locations'] = $locations;
		$data['can_view_inventory_at_all_locations'] = $this->Employee->has_module_action_permission('reports','view_inventory_at_all_locations', $this->Employee->get_logged_in_employee_info()->person_id);
		
		$this->load->view("reports/date_input_no_sales",$data);	
	}
	
	/** also added for register log */
	
	function detailed_register_log($start_date, $end_date, $export_excel=0, $offset = 0)
	{

		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);
		
		$this->load->model('reports/Detailed_register_log');
		$model = $this->Detailed_register_log;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'offset' => $offset, 'export_excel' => $export_excel));
		
		$config = array();
		$config['base_url'] = site_url("reports/detailed_register_log/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 6;
		$this->load->library('pagination');$this->pagination->initialize($config);
		
		$headers = $model->getDataColumns();
		$report_data = $model->getData();
		
		$summary_data = array();

		foreach($report_data as $row)
		{
			if($row['shift_end']=='0000-00-00 00:00:00')
			{
				$shift_end=lang('reports_register_log_open');
				$delete=anchor('reports/delete_register_log/'.$row['register_log_id'].'/'.$start_date.'/'. $end_date, lang('common_delete'), 
					"onclick='return do_link_confirm(".json_encode(lang('reports_confirm_register_log_delete')).", this)'");
			}
			else
			{
				$shift_end=date(get_date_format(), strtotime($row['shift_end'])) .' '.date(get_time_format(), strtotime($row['shift_end']));
				$delete=anchor('reports/delete_register_log/'.$row['register_log_id'].'/'.$start_date.'/'. $end_date, lang('common_delete'), 
					"onclick='return do_link_confirm(".json_encode(lang('reports_confirm_register_log_delete')).", this)'");
			}
			
			$details=anchor('reports/register_log_details/'.$row['register_log_id'], lang('common_det')); 
			
			$summary_data[] = array(
				array('data'=>$delete, 'align'=>'left'), 
				array('data'=>$details, 'align'=>'left'), 
				array('data'=>$row['register_name'], 'align'=>'left'), 
				array('data'=>$row['open_first_name'] . ' ' . $row['open_last_name'], 'align'=>'left'), 
				array('data'=>$row['close_first_name'] . ' ' . $row['close_last_name'], 'align'=>'left'), 
				array('data'=>date(get_date_format(), strtotime($row['shift_start'])) .' '.date(get_time_format(), strtotime($row['shift_start'])), 'align'=>'left'), 
				array('data'=>$shift_end, 'align'=>'left'), 
				array('data'=>to_currency($row['open_amount']), 'align'=>'right'), 
				array('data'=>to_currency($row['close_amount']), 'align'=>'right'), 
				array('data'=>to_currency($row['cash_sales_amount']), 'align'=>'right'),
				array('data'=>to_currency($row['total_cash_additions']), 'align'=>'right'),
				array('data'=>to_currency($row['total_cash_subtractions']), 'align'=>'right'),
				array('data'=>to_currency($row['difference']), 'align'=>'right'),
				array('data'=>$row['notes'], 'align'=>'left')
			);			
		}

		$data = array(
			"title" =>lang('reports_register_log_title'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"headers" => $model->getDataColumns(),
			"data" => $summary_data,
			"summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
		);

		$this->load->view("reports/tabular", $data);
	}
	
	function register_log_details($id)
	{

		
		$data = array(
			'register_log' => $this->Register->get_register_log($id),
			'register_log_details' => $this->Register->get_register_log_details($id)
		);
		
		$this->load->view('reports/register_log_details', $data);
	}
	
	function summary_count_report($start_date, $end_date, $export_excel=0, $offset = 0)
	{

		
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);
		
		$this->load->model('reports/Summary_inventory_count_report');
		$model = $this->Summary_inventory_count_report;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'offset' => $offset, 'export_excel' => $export_excel));
		
		$config = array();
		$config['base_url'] = site_url("reports/summary_count_report/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 6;
		$this->load->library('pagination');$this->pagination->initialize($config);
		
		$headers = $model->getDataColumns();
		$report_data = $model->getData();
		$location_count = count(Report::get_selected_location_ids());

		$summary_data = array();

		foreach($report_data as $row)
		{
			$status = '';
			switch($row['status'])
			{
				case 'open':
				$status = lang('common_open');
				break;

				case 'closed':
				$status = lang('common_closed');
				break;
			}
			$tabular_data_row = array(
				array('data'=>date(get_date_format().' '.get_time_format(), strtotime($row['count_date'])), 'align'=>'left'), 
				array('data'=>$status, 'align'=>'left'), 
				array('data'=>$row['employee_name'], 'align'=>'left'), 
				array('data'=>to_quantity($row['items_counted']), 'align'=>'left'), 
				array('data'=>to_quantity($row['difference']), 'align'=>'left'), 
				array('data'=>$row['comment'], 'align'=>'left'), 
			);
			

			if ($location_count > 1)
			{
				array_unshift($tabular_data_row, array('data'=>$row['location_name'], 'align'=>'left'));
			}
			
			$summary_data[] = $tabular_data_row;			
		}

		$data = array(
			"title" =>lang('reports_summary_count_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"headers" => $model->getDataColumns(),
			"data" => $summary_data,
			"summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
		);

		$this->load->view("reports/tabular", $data);
	}
	
	function detailed_count_report($start_date, $end_date, $export_excel=0, $offset = 0)
	{

		
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);
		
		$this->load->model('reports/Detailed_inventory_count_report');
		$model = $this->Detailed_inventory_count_report;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'offset' => $offset, 'export_excel' => $export_excel));
		
		$config = array();
		$config['base_url'] = site_url("reports/detailed_count_report/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 6;
		$this->load->library('pagination');$this->pagination->initialize($config);
		
		$headers = $model->getDataColumns();
		$report_data = $model->getData();

		$summary_data = array();
		$details_data = array();
		$location_count = count(Report::get_selected_location_ids());
		
		foreach($report_data['summary'] as $key=>$row)
		{
			$status = '';
			switch($row['status'])
			{
				case 'open':
				$status = lang('common_open');
				break;

				case 'closed':
				$status = lang('common_closed');
				break;
			}
			
			$summary_data_row = array(
				array('data'=>date(get_date_format().' '.get_time_format(), strtotime($row['count_date'])), 'align'=>'left'), 
				array('data'=>$status, 'align'=>'left'), 
				array('data'=>$row['employee_name'], 'align'=>'left'), 
				array('data'=>to_quantity($row['items_counted']), 'align'=>'left'), 
				array('data'=>to_quantity($row['difference']), 'align'=>'left'), 
				array('data'=>$row['comment'], 'align'=>'left'), 
			);	
			

			if ($location_count > 1)
			{
				array_unshift($summary_data_row, array('data'=>$row['location_name'], 'align'=>'left'));
			}
			
			
			
			$summary_data[$key] = $summary_data_row;
			
			foreach($report_data['details'][$key] as $drow)
			{
				$details_data_row = array();
				$details_data_row[] = array('data'=>$drow['item_number'], 'align'=>'left');
				$details_data_row[] = array('data'=>$drow['product_id'], 'align'=>'left');
				$details_data_row[] = array('data'=>$drow['name'], 'align'=>'left');
				$details_data_row[] = array('data'=>$drow['category'], 'align'=>'left');
				$details_data_row[] = array('data'=>$drow['size'], 'align'=>'left');
				$details_data_row[] = array('data'=>to_quantity($drow['count']), 'align'=>'left');
				$details_data_row[] = array('data'=>to_quantity($drow['actual_quantity']), 'align'=>'left');
				$details_data_row[] = array('data'=>$drow['comment'], 'align'=>'left');
				$details_data[$key][] = $details_data_row;
			}
			
		}

		$data = array(
			"title" =>lang('reports_detailed_count_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"headers" => $model->getDataColumns(),
			"summary_data" => $summary_data,
			"details_data" => $details_data,
			"overall_summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
		);

		$this->load->view("reports/tabular_details", $data);
	}
	
	function delete_register_log($register_log_id,$start_date,$end_date)
	{
		$this->load->model('reports/Detailed_register_log');
		if($this->Detailed_register_log->delete_register_log($register_log_id))
		{
			redirect('reports/detailed_register_log/'.$start_date.'/'.$end_date);
		}
		
		
	}
	
	function summary_sales_time($start_date, $end_date, $do_compare, $compare_start_date, $compare_end_date, $sale_type, $interval, $export_excel=0)
	{
		$this->load->model('Sale');

		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);
		$compare_start_date=rawurldecode($compare_start_date);
		$compare_end_date=rawurldecode($compare_end_date);
		

		$this->load->model('reports/Summary_sales_time');
		$model = $this->Summary_sales_time;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'interval' => $interval,'export_excel' => $export_excel));

		$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
		
		$tabular_data = array();
		$report_data = $model->getData();
		
		$summary_data = $model->getSummaryData();
		
		if ($do_compare)
		{
			$model_compare = $this->Summary_sales_time;
			$model_compare->setParams(array('start_date'=>$compare_start_date, 'end_date'=>$compare_end_date, 'sale_type' => $sale_type, 'interval' => $interval,'export_excel' => $export_excel));
			
			$this->Sale->drop_sales_items_temp_table();
			$this->Sale->create_sales_items_temp_table(array('start_date'=>$compare_start_date, 'end_date'=>$compare_end_date, 'sale_type' => $sale_type));
			
			$report_data_compare = $model_compare->getData();
			$report_data_summary_compare = $model_compare->getSummaryData();
		}
		
		foreach($report_data as $row)
		{
			if ($do_compare)
			{
				$index_compare = -1;
				$time_range_to_compare_to = $row['time_range'];
				
				for($k=0;$k<count($report_data_compare);$k++)
				{
					if ($report_data_compare[$k]['time_range'] == $time_range_to_compare_to)
					{
						$index_compare = $k;
						break;
					}
				}
				
				if (isset($report_data_compare[$index_compare]))
				{
					$row_compare = $report_data_compare[$index_compare];
				}
				else
				{
					$row_compare = FALSE;
				}
			}
			
			$data_row = array();
			
			$data_row[] = array('data'=>$row['time_range'], 'align'=>'left');
			$data_row[] = array('data'=>$row['number_of_transactions'].($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['number_of_transactions'] >= $row['number_of_transactions'] ? ($row['number_of_transactions'] == $row_compare['number_of_transactions'] ?  '' : 'compare_better') : 'compare_worse').'">'.$row_compare['number_of_transactions'] .'</span>' :''), 'align' => 'right');
			$data_row[] = array('data'=>to_currency($row['subtotal']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['subtotal'] >= $row['subtotal'] ? ($row['subtotal'] == $row_compare['subtotal'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['subtotal']) .'</span>' :''), 'align' => 'right');
			$data_row[] = array('data'=>to_currency($row['total']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['total'] >= $row['total'] ? ($row['total'] == $row_compare['total'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['total']) .'</span>' :''), 'align' => 'right');
			$data_row[] = array('data'=>to_currency($row['tax']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['tax'] >= $row['tax'] ? ($row['tax'] == $row_compare['tax'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['tax']) .'</span>' :''), 'align' => 'right');
			
			if($this->has_profit_permission)
			{
				$data_row[] = array('data'=>to_currency($row['profit']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['profit'] >= $row['profit'] ? ($row['profit'] == $row_compare['profit'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['profit']) .'</span>' :''), 'align' => 'right');
			}
			$tabular_data[] = $data_row;
		}
		
		if ($do_compare)
		{
			foreach($summary_data as $key=>$value)
			{
				$summary_data[$key] = to_currency($value) . ' / <span class="compare '.($report_data_summary_compare[$key] >= $value ? ($value == $report_data_summary_compare[$key] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($report_data_summary_compare[$key]).'</span>';
			}
			
		}
		
		
		$data = array(
			"title" => lang('reports_sales_summary_by_time_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)).($do_compare  ? ' '. lang('reports_compare_to'). ' '. date(get_date_format(), strtotime($compare_start_date)) .'-'.date(get_date_format(), strtotime($compare_end_date)) : ''),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $summary_data,
			"export_excel" => $export_excel,
			"pagination" => '',
		);

		$this->load->view("reports/tabular",$data);
	}
	
	function graphical_summary_sales_time($start_date, $end_date, $sale_type,$interval)
	{
		$this->load->model('Sale');

		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);

		$this->load->model('reports/Summary_sales_time');
		$model = $this->Summary_sales_time;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'interval' => $interval));

		$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$data = array(
			"title" => lang('reports_sales_summary_by_time_report'),
			"graph_file" => site_url("reports/graphical_summary_sales_time_graph/$start_date/$end_date/$sale_type/$interval"),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"summary_data" => $model->getSummaryData()
		);

		$this->load->view("reports/graphical",$data);
		
	}

	//The actual graph data
	function graphical_summary_sales_time_graph($start_date, $end_date, $sale_type,$interval)
	{
		$this->load->model('Sale');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);

		$this->load->model('reports/Summary_sales_time');
		$model = $this->Summary_sales_time;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'interval' => $interval));
		$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
		
		$report_data = $model->getData();

		$graph_data = array();
		foreach($report_data as $row)
		{
			$graph_data[$row['time_range']] = to_quantity($row['number_of_transactions']);
		}


		$data = array(
			"title" => lang('reports_employees_summary_report'),
			"data" => $graph_data,
			"tooltip_template" => "<%=label %>:  <%=value %>",
		);

		$this->load->view("reports/graphs/bar",$data);
	}
	
	//Summary sales report
	function summary_sales($start_date, $end_date, $do_compare, $compare_start_date, $compare_end_date, $sale_type, $export_excel=0, $offset=0)
	{
		$this->load->model('Sale');

		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);
		$compare_start_date=rawurldecode($compare_start_date);
		$compare_end_date=rawurldecode($compare_end_date);

		$this->load->model('reports/Summary_sales');
		$model = $this->Summary_sales;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel' => $export_excel));

		$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$config = array();
		$config['base_url'] = site_url("reports/summary_sales/".rawurlencode($start_date).'/'.rawurlencode($end_date).'/'.$do_compare.'/'.rawurlencode($compare_start_date).'/'.rawurlencode($compare_end_date)."/$sale_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 10;
		$this->load->library('pagination');$this->pagination->initialize($config);

		$tabular_data = array();
		$report_data = $model->getData();
		$summary_data = $model->getSummaryData();
		
		if ($do_compare)
		{
			$model_compare = $this->Summary_sales;
			$model_compare->setParams(array('start_date'=>$compare_start_date, 'end_date'=>$compare_end_date, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel' => $export_excel));
			
			$this->Sale->drop_sales_items_temp_table();
			$this->Sale->create_sales_items_temp_table(array('start_date'=>$compare_start_date, 'end_date'=>$compare_end_date, 'sale_type' => $sale_type));
			
			$report_data_compare = $model_compare->getData();
			$report_data_summary_compare = $model_compare->getSummaryData();
		}
		
		$index = 0;
		foreach($report_data as $row)
		{
			$data_row = array();
			if ($do_compare)
			{
				if (isset($report_data_compare[$index]))
				{
					$row_compare = $report_data_compare[$index];
				}
				else
				{
					$row_compare = FALSE;
				}
			}
			
			$data_row[] = array('data'=>date(get_date_format(), strtotime($row['sale_date'])).($do_compare && $row_compare ? ' / <span class="compare ">'.date(get_date_format(), strtotime($row_compare['sale_date'])).'</span>' :''), 'align'=>'left');
			$data_row[] = array('data'=>to_currency($row['subtotal']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['subtotal'] >= $row['subtotal'] ? ($row['subtotal'] == $row_compare['subtotal'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['subtotal']) .'</span>' :''), 'align'=>'right');
			$data_row[] = array('data'=>to_currency($row['total']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['total'] >= $row['total'] ? ($row['total'] == $row_compare['total'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['total']) .'</span>' :''), 'align'=>'right');
			$data_row[] = array('data'=>to_currency($row['tax']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['tax'] >= $row['tax'] ? ($row['tax'] == $row_compare['tax'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['tax']) .'</span>' :''), 'align'=>'right');
			
			if($this->has_profit_permission)
			{
				$data_row[] = array('data'=>to_currency($row['profit']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['profit'] >= $row['profit'] ? ($row['profit'] == $row_compare['profit'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['profit']) .'</span>' :''), 'align'=>'right');
			}
			$tabular_data[] = $data_row;
			
			$index++;
		}
		
		if ($do_compare)
		{
			foreach($summary_data as $key=>$value)
			{
				$summary_data[$key] = to_currency($value) . ' / <span class="compare '.($report_data_summary_compare[$key] >= $value ? ($value == $report_data_summary_compare[$key] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($report_data_summary_compare[$key]).'</span>';
			}
			
		}
		$data = array(
			"title" => lang('reports_sales_summary_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)).($do_compare  ? ' '. lang('reports_compare_to'). ' '. date(get_date_format(), strtotime($compare_start_date)) .'-'.date(get_date_format(), strtotime($compare_end_date)) : ''),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $summary_data,
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links()
		);

		if($export_excel) $this->summary_sales_excel($start_date, $end_date,$report_data);
		else
			$this->load->view("reports/tabular",$data);
	}

	function summary_sales_excel($start_date, $end_date,$data = array()){
		// var_dump($data);die;
        #-------------------------------------------------------------------------------------------------#

        # Biến hien_thi có cấu trúc C:D:E với C:D là 2 trường cần merge E là trường vị trí hiển thị dữ liệu

        #-------------------------------------------------------------------------------------------------#
		$ten_cong_ty = $this->config->item('company');

        # đầu trang

		$_title = array(
			array('value_field' => 'title','dong_nao'=>4,'hien_thi'=>'A:E:A'),
			array('value_field' => 'date','dong_nao'=>5,'hien_thi'=>'A:E:A'),
			// array('value_field' => 'thong_tin_diem_ban_hang','dong_nao'=>2,'hien_thi'=>'A:D:A'),
			array('value_field' => 'ten_cong_ty','dong_nao'=>1,'hien_thi'=>'A:D:A'),
		);

		$_headers 	 = array(
			array('col' => 'A','value_field' => 'sale_date'),
			array('col' => 'B','value_field' => 'subtotal'),
			array('col' => 'C','value_field' => 'total'),
			array('col' => 'D','value_field' => 'tax'),
			array('col' => 'E','value_field' => 'profit'),
		);		

		$_footer = array(
			# phần tổng cuối cùng
			array('sum' => 'D','value_field' => 'SUM','ten_truong'=>'Tổng thuế: ','hien_thi'=>'C:D:E'),
			array('sum' => 'C','value_field' => 'SUM','ten_truong'=>'Tổng cộng: ','hien_thi'=>'C:D:E'),
		);

		// echo $start_date;
		// die;
		$title = 'BÁO CÁO TỔNG HỢP BÁN HÀNG';
		if($start_date == '1970-01-01') $date = 'Toàn bộ thời gian'; else $date = 'Từ : '.$start_date.' đến : '.$end_date;
   //      echo '<pre>';
			// var_dump($data);die;
		foreach ($data as $value) {
			$result[] = array(
				'sale_date' 	=>  $value['sale_date'],
				'subtotal' 		=> 	$value['subtotal'],
				'total' 		=>  $value['total'],
				'tax' 			=>  $value['tax'],
				'profit' 		=>  $value['profit'],
				'subtotal'     			=>  $value['subtotal'],
				'date'					=>	$date,
				// 'thong_tin_diem_ban_hang' => $value['location_name'],
				'ten_cong_ty'   		=>  $ten_cong_ty,
				'title'         		=> 	$title,
			);
		}
		// die;
		$bizExcel = new BizExcel('bao_cao_tong_hop_ban_hang.xlsx');
		$bizExcel->Row_title($_title);
		$bizExcel->setNumberRowStartBody(7)->setHeaderOfBody($_headers);
		$bizExcel->RowEndBody($_footer);
		$bizExcel->tat_auto_size(true);
		$bizExcel->setDataExcel($result);
		// $this->oPHPExcel->getActiveSheet()->setCellValue('B5','ducanh');

		// $bizExcel->addToNewSheet('Tổng hợp nhóm khách hàng')->generateFile(false, '', false);

		
		$excelContent = $bizExcel->generateFile(false);
		// die;
		$this->load->helper('download');
		force_download('bao_cao_tong_hop_ban_hang.xlsx', $excelContent);
		exit;
	}
	
	//Summary tiers report
	function summary_tiers($start_date, $end_date, $sale_type, $export_excel=0, $offset=0)
	{
		$this->load->model('Sale');

		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);

		$this->load->model('reports/Summary_tiers');
		$model = $this->Summary_tiers;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel' => $export_excel));

		$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$config = array();
		$config['base_url'] = site_url("reports/summary_tiers/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$sale_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 7;
		$this->load->library('pagination');$this->pagination->initialize($config);

		$tabular_data = array();
		$report_data = $model->getData();

		foreach($report_data as $row)
		{
			$data_row = array();
			
			$data_row[] = array('data'=>$row['tier_name'], 'align'=>'left');
			$data_row[] = array('data'=>$row['count'], 'align'=>'right');
			
			$tabular_data[] = $data_row;
		}
		$data = array(
			"title" => lang('reports_tiers_summary_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links()
		);

		$this->load->view("reports/tabular",$data);
	}
	

	//Summary categories report
	function summary_categories($start_date, $end_date,$do_compare, $compare_start_date, $compare_end_date, $sale_type, $export_excel=0, $offset = 0)
	{
		$this->load->model('Sale');

		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);
		$compare_start_date=rawurldecode($compare_start_date);
		$compare_end_date=rawurldecode($compare_end_date);

		$this->load->model('reports/Summary_categories');
		$model = $this->Summary_categories;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'export_excel'=>$export_excel, 'offset' => $offset));

		$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
		
		$config = array();
		$config['base_url'] = site_url("reports/summary_categories/".rawurlencode($start_date).'/'.rawurlencode($end_date).'/'.$do_compare.'/'.rawurlencode($compare_start_date).'/'.rawurlencode($compare_end_date)."/$sale_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 10;

		$this->load->library('pagination');$this->pagination->initialize($config);
		
		$tabular_data = array();
		$report_data = $model->getData();
		$summary_data = $model->getSummaryData();
		
		if ($do_compare)
		{
			$compare_to_categories = array();
			
			for($k=0;$k<count($report_data);$k++)
			{
				$compare_to_categories[] = $report_data[$k]['category'];
			}
			
			$model_compare = $this->Summary_categories;			
			$model_compare->setParams(array('start_date'=>$compare_start_date, 'end_date'=>$compare_end_date, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel' => $export_excel, 'compare_to_categories' =>$compare_to_categories));
			
			$this->Sale->drop_sales_items_temp_table();
			$this->Sale->create_sales_items_temp_table(array('start_date'=>$compare_start_date, 'end_date'=>$compare_end_date, 'sale_type' => $sale_type));
			
			$report_data_compare = $model_compare->getData();
			$report_data_summary_compare = $model_compare->getSummaryData();
		}

		foreach($report_data as $row)
		{
			if ($do_compare)
			{
				$index_compare = -1;
				$category_to_compare = $row['category'];
				
				for($k=0;$k<count($report_data_compare);$k++)
				{
					if ($report_data_compare[$k]['category'] == $category_to_compare)
					{
						$index_compare = $k;
						break;
					}
				}
				
				if (isset($report_data_compare[$index_compare]))
				{
					$row_compare = $report_data_compare[$index_compare];
				}
				else
				{
					$row_compare = FALSE;
				}
			}
			
			$data_row = array();
			
			$data_row[] = array('data'=>$row['category'], 'align' => 'left');
			$data_row[] = array('data'=>to_currency($row['subtotal']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['subtotal'] >= $row['subtotal'] ? ($row['subtotal'] == $row_compare['subtotal'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['subtotal']) .'</span>' :''), 'align' => 'right');
			$data_row[] = array('data'=>to_currency($row['total']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['total'] >= $row['total'] ? ($row['total'] == $row_compare['total'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['total']) .'</span>' :''), 'align' => 'right');
			$data_row[] = array('data'=>to_currency($row['tax']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['tax'] >= $row['tax'] ? ($row['tax'] == $row_compare['tax'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['tax']) .'</span>' :''), 'align' => 'right');
			if($this->has_profit_permission)
			{
				$data_row[] = array('data'=>to_currency($row['profit']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['profit'] >= $row['profit'] ? ($row['profit'] == $row_compare['profit'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['profit']) .'</span>' :''), 'align' => 'right');
			}
			$data_row[] = array('data'=>floatval($row['item_sold']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['item_sold'] >= $row['item_sold'] ? ($row['item_sold'] == $row_compare['item_sold'] ?  '' : 'compare_better') : 'compare_worse').'">'.floatval($row_compare['item_sold']) .'</span>' :''), 'align' => 'right');
			$tabular_data[] = $data_row;				
		}

		if ($do_compare)
		{
			foreach($summary_data as $key=>$value)
			{
				$summary_data[$key] = to_currency($value) . ' / <span class="compare '.($report_data_summary_compare[$key] >= $value ? ($value == $report_data_summary_compare[$key] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($report_data_summary_compare[$key]).'</span>';
			}
			
		}

		$data = array(
			"title" => lang('reports_categories_summary_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)).($do_compare  ? ' '. lang('reports_compare_to'). ' '. date(get_date_format(), strtotime($compare_start_date)) .'-'.date(get_date_format(), strtotime($compare_end_date)) : ''),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $summary_data,
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
		);

		$this->load->view("reports/tabular",$data);
	}

	//Summary customers report
	function summary_customers($start_date, $end_date, $sale_type, $total_spent_condition = 'any', $total_spent_amount = 0, $export_excel=0, $offset = 0)
	{
		$this->load->model('Sale');

		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);

		$this->load->model('reports/Summary_customers');
		# lấy điểm bán hàng
		$location_id = $this->Employee->get_logged_in_employee_current_location_id();

		$model = $this->Summary_customers;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel' => $export_excel, 'total_spent_condition' => $total_spent_condition, 'total_spent_amount' => $total_spent_amount,'location_id' => $location_id));

		$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
		
		$config = array();
		$config['base_url'] = site_url("reports/summary_customers/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$sale_type/$total_spent_condition/$total_spent_amount/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 9;		
		$this->load->library('pagination');$this->pagination->initialize($config);

		$tabular_data = array();
		$report_data = $model->getData();
		$no_customer = $model->getNoCustomerData();
		$report_data = array_merge($no_customer,$report_data);
		
		foreach($report_data as $row)
		{
			$data_row = array();
			
			$data_row[] = array('data'=>$row['person_id'], 'align' => 'left');
			$data_row[] = array('data'=>$row['customer'], 'align' => 'left');
			$data_row[] = array('data'=>$row['phone_number'], 'align' => 'left');
			$data_row[] = array('data'=>to_currency($row['subtotal']), 'align' => 'right');
			$data_row[] =  array('data'=>to_currency($row['total']), 'align' => 'right');
			$data_row[] = array('data'=>to_currency($row['tax']), 'align' => 'right');
			if($this->has_profit_permission)
			{
				$data_row[] = array('data'=>to_currency($row['profit']), 'align' => 'right');
			}

			if ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'advanced')
			{
				$data_row[] = array('data'=>to_currency_no_money($row['points_used']), 'align' => 'right');
				$data_row[] = array('data'=>to_currency_no_money($row['points_gained']), 'align' => 'right');
			}
			elseif ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'simple')
			{
				$sales_until_discount = $this->config->item('number_of_sales_for_discount') - $row['current_sales_for_discount'];
				$data_row[] = array('data'=>to_quantity($sales_until_discount), 'align' => 'right');
			}
			$tabular_data[] = $data_row;				
		}

		$data = array(
			"title" => lang('reports_customers_summary_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
		);

		if($export_excel) $this->summary_customers_excel($start_date, $end_date,$report_data,$location_id);
		else
			$this->load->view("reports/tabular",$data);
	}

	function summary_customers_excel($start_date = false, $end_date = false,$data = array(),$location_id = null){
        #-------------------------------------------------------------------------------------------------#

        # Biến hien_thi có cấu trúc C:D:E với C:D là 2 trường cần merge E là trường vị trí hiển thị dữ liệu

        #-------------------------------------------------------------------------------------------------#
		$ten_cong_ty = $this->config->item('company');
		$thong_tin_diem_ban_hang = $this->Location->get_info($location_id)->name;
        # đầu trang

		$_title = array(
			array('value_field' => 'title','dong_nao'=>4,'hien_thi'=>'A:I:A'),
			array('value_field' => 'date','dong_nao'=>5,'hien_thi'=>'A:I:A'),
			array('value_field' => 'thong_tin_diem_ban_hang','dong_nao'=>6,'hien_thi'=>'A:I:A'),
			array('value_field' => 'ten_cong_ty','dong_nao'=>1,'hien_thi'=>'A:E:A'),
		);

		$_headers 	 = array(
			array('col' => 'A','value_field' => 'customer_id'),
			array('col' => 'B','value_field' => 'customer'),
			array('col' => 'C','value_field' => 'phone_number'),
			array('col' => 'D','value_field' => 'subtotal'),
			array('col' => 'E','value_field' => 'total'),
			array('col' => 'F','value_field' => 'tax'),
			array('col' => 'G','value_field' => 'profit'),
			array('col' => 'H','value_field' => 'points_used'),
			array('col' => 'I','value_field' => 'points_gained'),
		);		

		$_footer = array(
			# phần tổng cuối cùng
			array('sum' => 'D','value_field' => 'SUM','ten_truong'=>'Tổng chưa thuế: ','hien_thi'=>'G:H:I'),
			array('sum' => 'E','value_field' => 'SUM','ten_truong'=>'Tổng có thuế: ','hien_thi'=>'G:H:I'),
			array('sum' => 'F','value_field' => 'SUM','ten_truong'=>'Tổng thuế: ','hien_thi'=>'G:H:I'),
			array('sum' => 'G','value_field' => 'SUM','ten_truong'=>'Tổng lợi nhuận: ','hien_thi'=>'G:H:I'),
		);

		// echo $start_date;
		// die;
		$title = 'BÁO CÁO TỔNG HỢP KHÁCH HÀNG';
		if($start_date == '1970-01-01') $date = 'Toàn bộ thời gian'; else $date = 'Từ : '.$start_date.' đến : '.$end_date;
   //      echo '<pre>';
			// var_dump($data);die;
		foreach ($data as $value) {
			$result[] = array(
				'customer_id' 			=>  $value['customer_id'],
				'customer' 				=> 	$value['customer'],
				'phone_number' 			=>  $value['phone_number'],
				'subtotal' 				=>  $value['subtotal'],
				'total' 				=>  $value['total'],
				'subtotal'     			=>  $value['subtotal'],
				'tax' 					=>  $value['tax'],
				'profit' 				=>  $value['profit'],
				'points_used' 			=>  $value['points_used'],
				'points_gained' 		=>  $value['points_gained'],
				'date'					=>	$date,
				'thong_tin_diem_ban_hang' => $thong_tin_diem_ban_hang,
				'ten_cong_ty'   		=>  $ten_cong_ty,
				'title'         		=> 	$title,
			);
		}
		// die;
		$bizExcel = new BizExcel('bao_cao_tong_hop_khach_hang.xlsx');
		$bizExcel->Row_title($_title);
		$bizExcel->setNumberRowStartBody(8)->setHeaderOfBody($_headers);
		$bizExcel->RowEndBody($_footer);
		$bizExcel->tat_auto_size(FALSE);
		$bizExcel->setDataExcel($result);
		// $this->oPHPExcel->getActiveSheet()->setCellValue('B5','ducanh');

		// $bizExcel->addToNewSheet('Tổng hợp nhóm khách hàng')->generateFile(false, '', false);

		
		$excelContent = $bizExcel->generateFile(false);
		// die;
		$this->load->helper('download');
		force_download('bao_cao_tong_hop_khach_hang.xlsx', $excelContent);
		exit;

	}

	//Summary suppliers report
	function summary_suppliers($start_date, $end_date, $sale_type, $export_excel=0, $offset=0)
	{



		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);

		$this->load->model('reports/Summary_suppliers');

		$model = $this->Summary_suppliers;

		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'offset'=>$offset, 'export_excel' => $export_excel));
		$headers = $model->getDataColumns();
		$this->Receiving->create_receivings_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
		$config = array();
		$config['base_url'] = site_url("reports/summary_suppliers/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$sale_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;  
		$config['uri_segment'] = 7;		
		$config['use_page_numbers'] = TRUE;
		$this->load->library('pagination');$this->pagination->initialize($config);
		$report_data = $model->getData(array('page' =>$this->uri->segment(7,1), 'per_page' => $config['per_page']));
		$overall_summary_data['total_receive']     = 0;
		$overall_summary_data['total_without_tax'] = 0;
		$overall_summary_data['total_with_tax']    = 0;
		foreach($report_data['overall_summary_data'] as $key=>$row)
		{
			$overall_summary_data['total_receive']     += $row['subtotal']; 
			$overall_summary_data['total_without_tax'] += $row['tax']; 
			$overall_summary_data['total_with_tax']    += $row['total'];

		}

		$tabular_data = array();
		$dataExportExcel = array();
		foreach($report_data['summary'] as $row)
		{
			$data_row = array();
			$data_row[] = array('data'=>$row['company_name'], 'align' => 'left');
			$data_row[] = array('data'=>to_currency($row['subtotal']), 'align'=>'right');
			$data_row[] = array('data'=>to_currency($row['tax']), 'align'=> 'right');
			$data_row[] = array('data'=>to_currency($row['total']), 'align'=>'right');
			$dataExportExcel[] =array(
				'company_name' =>$row['company_name'],
				'subtotal'     =>to_currency($row['subtotal']), 
				'tax'          =>to_currency($row['tax']),
				'total'        =>to_currency($row['total']),
			);
			
			
			$tabular_data[] = $data_row;
			
		}
		if($export_excel == 0)
		{
			$data = array(
				"title" => lang('reports_suppliers_summary_report'),
				"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
				"headers" => $headers,
				"data" => $tabular_data,
				"summary_data" => $overall_summary_data,
				"export_excel" => $export_excel,
				"pagination" => $this->pagination->create_links(),
			);

			$this->load->view("reports/tabular",$data);
		}
		else
		{
			$excelColumn = 'A';
			foreach($headers as $key => $header_detail)
			{
				$header_of_col_name[] = array('col' =>$excelColumn,'text' => $header_detail['data'],'styles'=>array('bold' =>true,'font'=>true, 'font_size'=>14,'is_fill'=>true,'color'=>'c0e0d3'));
				$excelColumn++;
			}
			//header of File
			$header_of_multicol[] = array('mergeStartCol' =>'A','mergeEndCol'=> $excelColumn,'text' =>lang('reports_overview_supplier'),'styles'=>array('bold' =>true,'font'=>true, 'font_size'=>22,'is_fill'=>true,'color'=>'c0e0d3'));
			
			$headerOfBody     = array(
				array('col' => 'A','value_field' =>'company_name'),
				array('col' => 'B','value_field' =>'subtotal'),
				array('col' => 'C','value_field' =>'tax'),
				array('col' => 'D','value_field' =>'total')
			);
			$bizExcel = new BizExcel('report_specific_supplier.xlsx');
			$bizExcel->setNumberRowBeginRow(1)->setHeaderOfMultiCol($header_of_multicol);
			$bizExcel->setNumberRowStartBody(2)->setHeaderOfBody($headerOfBody);
			$bizExcel->setHeaderOfCol($header_of_col_name);
			$bizExcel->setDataExcel($dataExportExcel);
			$excelContent = $bizExcel->generateFile(false);
			$this->load->helper('download');
			force_download(lang('reports_overview_supplier').' '. date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)).'.xlsx', $excelContent);
			exit;
		}

	}
	
	//Summary suppliers report
	function summary_suppliers_receivings($start_date, $end_date, $sale_type, $export_excel=0, $offset=0)
	{
		$this->load->model('Receiving');

		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);
		
		$this->load->model('reports/Summary_suppliers_receivings');
		$model = $this->Summary_suppliers_receivings;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'offset'=>$offset, 'export_excel' => $export_excel));

		$this->Receiving->create_receivings_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
		$config = array();
		$config['base_url'] = site_url("reports/summary_suppliers_receivings/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$sale_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 7;		
		$this->load->library('pagination');$this->pagination->initialize($config);
		
		$tabular_data = array();
		$report_data = $model->getData();

		foreach($report_data as $row)
		{
			$data_row = array();
			
			$data_row[] = array('data'=>$row['supplier'], 'align' => 'left');
			$data_row[] = array('data'=>to_currency($row['subtotal']), 'align'=>'left');
			$data_row[] = array('data'=>to_currency($row['total']), 'align'=>'left');
			$data_row[] = array('data'=>to_currency($row['tax']), 'align'=>'left');
			$tabular_data[] = $data_row;			
		}

		$data = array(
			"title" => lang('reports_suppliers_receivings_summary_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
		);

		$this->load->view("reports/tabular",$data);
	}
	

	function summary_items_input()
	{
		$this->load->model('Category');
		$data = $this->_get_common_report_data(TRUE);
		$data['supplier_search_suggestion_url'] = site_url('reports/supplier_search');
		$data['hide_excel_export_and_compare'] = FALSE;
		
		$data['categories'] = array();
		$data['categories'][-1] =lang('common_all');
		
		$categories = $this->Category->sort_categories_and_sub_categories($this->Category->get_all_categories_and_sub_categories());
		
		foreach($categories as $key=>$value)
		{
			$name = str_repeat('&nbsp;&nbsp;', $value['depth']).$value['name'];
			$data['categories'][$key] = $name;
		}
		
		$this->load->view("reports/summary_items_input",$data);
	}
	
	
	function summary_items_input_graphical()
	{
		$this->load->model('Category');
		
		$data = $this->_get_common_report_data(TRUE);
		$data['supplier_search_suggestion_url'] = site_url('reports/supplier_search');
		$data['hide_excel_export_and_compare'] = TRUE;
		
		$data['categories'] = array();
		$data['categories'][-1] =lang('common_all');
		
		$categories = $this->Category->sort_categories_and_sub_categories($this->Category->get_all_categories_and_sub_categories());
		
		foreach($categories as $key=>$value)
		{
			$name = str_repeat('&nbsp;&nbsp;', $value['depth']).$value['name'];
			$data['categories'][$key] = $name;
		}
		
		$this->load->view("reports/summary_items_input",$data);
	}

	//Summary items report
	function summary_items($start_date, $end_date, $do_compare, $compare_start_date, $compare_end_date, $supplier_id = -1, $category_id = -1, $sale_type = 'all', $export_excel=0, $offset = 0)
	{
		$this->load->model('Category');
		$this->load->model('Sale');

		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);
		$compare_start_date=rawurldecode($compare_start_date);
		$compare_end_date=rawurldecode($compare_end_date);
		

		$this->load->model('reports/Summary_items');
		$model = $this->Summary_items;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'category_id' => $category_id, 'supplier_id' => $supplier_id, 'offset' => $offset, 'export_excel' => $export_excel));

		$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$config = array();
		$config['base_url'] = site_url("reports/summary_items/".rawurlencode($start_date).'/'.rawurlencode($end_date).'/'.$do_compare.'/'.rawurlencode($compare_start_date).'/'.rawurlencode($compare_end_date)."/$supplier_id/$category_id/$sale_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 12;

		$this->load->library('pagination');$this->pagination->initialize($config);

		$tabular_data = array();
		$report_data = $model->getData();
		$summary_data = $model->getSummaryData();
		
		if ($do_compare)
		{
			$compare_to_items = array();
			
			for($k=0;$k<count($report_data);$k++)
			{
				$compare_to_items[] = $report_data[$k]['item_id'];
			}
			
			$model_compare = $this->Summary_items;
			$model_compare->setParams(array('start_date'=>$compare_start_date, 'end_date'=>$compare_end_date, 'sale_type' => $sale_type, 'category_id' => $category_id, 'supplier_id' => $supplier_id, 'offset' => $offset, 'export_excel' => $export_excel, 'compare_to_items' => $compare_to_items));
			
			$this->Sale->drop_sales_items_temp_table();
			$this->Sale->create_sales_items_temp_table(array('start_date'=>$compare_start_date, 'end_date'=>$compare_end_date, 'sale_type' => $sale_type));
			
			$report_data_compare = $model_compare->getData();
			$report_data_summary_compare = $model_compare->getSummaryData();
		}


		foreach($report_data as $row)
		{
			if ($do_compare)
			{
				$index_compare = -1;
				$item_id_to_compare_to = $row['item_id'];
				
				for($k=0;$k<count($report_data_compare);$k++)
				{
					if ($report_data_compare[$k]['item_id'] == $item_id_to_compare_to)
					{
						$index_compare = $k;
						break;
					}
				}
				
				if (isset($report_data_compare[$index_compare]))
				{
					$row_compare = $report_data_compare[$index_compare];
				}
				else
				{
					$row_compare = FALSE;
				}
			}
			
			$data_row = array();
			$data_row[] = array('data'=>$row['name'], 'align' => 'left');
			$data_row[] = array('data'=>$row['item_number'], 'align' => 'left');
			$data_row[] = array('data'=>$row['product_id'], 'align' => 'left');
			$data_row[] = array('data'=>$row['category'], 'align' => 'left');
			$data_row[] = array('data'=>to_currency($row['current_selling_price']), 'align' => 'right');
			$data_row[] = array('data'=>to_quantity($row['quantity']), 'align' => 'left');
			$data_row[] = array('data'=>to_quantity($row['quantity_purchased']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['quantity_purchased'] >= $row['quantity_purchased'] ? ($row['quantity_purchased'] == $row_compare['quantity_purchased'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_quantity($row_compare['quantity_purchased']) .'</span>' :''), 'align' => 'left');
			$data_row[] = array('data'=>to_currency($row['subtotal']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['subtotal'] >= $row['subtotal'] ? ($row['subtotal'] == $row_compare['subtotal'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['subtotal']) .'</span>' :''), 'align' => 'right');
			$data_row[] = array('data'=>to_currency($row['total']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['total'] >= $row['total'] ? ($row['total'] == $row_compare['total'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['total']) .'</span>' :''), 'align' => 'right');
			$data_row[] = array('data'=>to_currency($row['tax']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['tax'] >= $row['tax'] ? ($row['tax'] == $row_compare['tax'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['tax']) .'</span>' :''), 'align' => 'right');
			if($this->has_profit_permission)
			{
				$data_row[] = array('data'=>to_currency($row['profit']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['profit'] >= $row['profit'] ? ($row['profit'] == $row_compare['profit'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['profit']) .'</span>' :''), 'align' => 'right');
			}
			$tabular_data[] = $data_row;

		}

		if ($do_compare)
		{
			foreach($summary_data as $key=>$value)
			{
				$summary_data[$key] = to_currency($value) . ' / <span class="compare '.($report_data_summary_compare[$key] >= $value ? ($value == $report_data_summary_compare[$key] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($report_data_summary_compare[$key]).'</span>';
			}
			
		}

		$data = array(
			"title" => lang('reports_items_summary_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)).($do_compare  ? ' '. lang('reports_compare_to'). ' '. date(get_date_format(), strtotime($compare_start_date)) .'-'.date(get_date_format(), strtotime($compare_end_date)) : ''),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $summary_data,
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links()
		);

		$this->load->view("reports/tabular",$data);
	}

	//Summary item kits report
	function summary_item_kits($start_date, $end_date, $do_compare, $compare_start_date, $compare_end_date, $supplier_id = -1, $category_id = -1, $sale_type = 'all', $export_excel=0, $offset = 0)
	{
		$this->load->model('Sale');

		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);
		$compare_start_date=rawurldecode($compare_start_date);
		$compare_end_date=rawurldecode($compare_end_date);
		

		$this->load->model('reports/Summary_item_kits');
		$model = $this->Summary_item_kits;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'export_excel' =>$export_excel, 'offset' => $offset));

		$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$config = array();
		$config['base_url'] = site_url("reports/summary_item_kits/".rawurlencode($start_date).'/'.rawurlencode($end_date).'/'.$do_compare.'/'.rawurlencode($compare_start_date).'/'.rawurlencode($compare_end_date)."/$supplier_id/$category_id/$sale_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 12;

		$this->load->library('pagination');$this->pagination->initialize($config);

		$tabular_data = array();
		$report_data = $model->getData();
		$summary_data = $model->getSummaryData();
		
		if ($do_compare)
		{
			$compare_to_items = array();
			
			for($k=0;$k<count($report_data);$k++)
			{
				$compare_to_item_kits[] = $report_data[$k]['item_kit_id'];
			}
			
			$model_compare = $this->Summary_item_kits;
			$model_compare->setParams(array('start_date'=>$compare_start_date, 'end_date'=>$compare_end_date, 'sale_type' => $sale_type, 'export_excel' =>$export_excel, 'offset' => $offset, 'compare_to_item_kits' => $compare_to_item_kits));
			
			$this->Sale->drop_sales_items_temp_table();
			$this->Sale->create_sales_items_temp_table(array('start_date'=>$compare_start_date, 'end_date'=>$compare_end_date, 'sale_type' => $sale_type));
			
			$report_data_compare = $model_compare->getData();
			$report_data_summary_compare = $model_compare->getSummaryData();
		}


		foreach($report_data as $row)
		{
			if ($do_compare)
			{
				$index_compare = -1;
				$item_kit_id_to_compare_to = $row['item_kit_id'];
				
				for($k=0;$k<count($report_data_compare);$k++)
				{
					if ($report_data_compare[$k]['item_kit_id'] == $item_kit_id_to_compare_to)
					{
						$index_compare = $k;
						break;
					}
				}
				
				if (isset($report_data_compare[$index_compare]))
				{
					$row_compare = $report_data_compare[$index_compare];
				}
				else
				{
					$row_compare = FALSE;
				}
			}
			
			$data_row = array();
			$data_row[] = array('data'=>$row['name'], 'align' => 'left');
			$data_row[] = array('data'=>to_quantity($row['quantity_purchased']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['quantity_purchased'] >= $row['quantity_purchased'] ? ($row['quantity_purchased'] == $row_compare['quantity_purchased'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_quantity($row_compare['quantity_purchased']) .'</span>' :''), 'align' => 'left');
			$data_row[] = array('data'=>to_currency($row['subtotal']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['subtotal'] >= $row['subtotal'] ? ($row['subtotal'] == $row_compare['subtotal'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['subtotal']) .'</span>' :''), 'align' => 'right');
			$data_row[] = array('data'=>to_currency($row['total']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['total'] >= $row['total'] ? ($row['total'] == $row_compare['total'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['total']) .'</span>' :''), 'align' => 'right');
			$data_row[] = array('data'=>to_currency($row['tax']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['tax'] >= $row['tax'] ? ($row['tax'] == $row_compare['tax'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['tax']) .'</span>' :''), 'align' => 'right');
			if($this->has_profit_permission)
			{
				$data_row[] = array('data'=>to_currency($row['profit']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['profit'] >= $row['profit'] ? ($row['profit'] == $row_compare['profit'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['profit']) .'</span>' :''), 'align' => 'right');
			}
			$tabular_data[] = $data_row;

		}

		if ($do_compare)
		{
			foreach($summary_data as $key=>$value)
			{
				$summary_data[$key] = to_currency($value) . ' / <span class="compare '.($report_data_summary_compare[$key] >= $value ? ($value == $report_data_summary_compare[$key] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($report_data_summary_compare[$key]).'</span>';
			}
			
		}

		$data = array(
			"title" => lang('reports_item_kits_summary_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)).($do_compare  ? ' '. lang('reports_compare_to'). ' '. date(get_date_format(), strtotime($compare_start_date)) .'-'.date(get_date_format(), strtotime($compare_end_date)) : ''),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $summary_data,
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links()
		);

		$this->load->view("reports/tabular",$data);
	}

	//Summary employees report
	function summary_employees($start_date, $end_date, $sale_type, $employee_type, $export_excel=0, $offset = 0)
	{
		$this->load->model('Sale');
		$this->check_action_permission('view_employees');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);

		$this->load->model('reports/Summary_employees');
		$model = $this->Summary_employees;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'employee_type' =>$employee_type, 'export_excel' => $export_excel, 'offset' => $offset));
		$locations = Report::get_selected_location_ids();                                                 
		$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
		$config = array();
		$config['base_url'] = site_url("reports/summary_employees/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$sale_type/$employee_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 8;		
		$this->load->library('pagination');
		$this->pagination->initialize($config);

		$tabular_data = array();
		$report_data = $model->getData();
		$summaryData  = $model->getSummaryData();
		$employee_col = ($employee_type == 'logged_in_employee') ? 'employee_id' : 'sold_by_employee_id';
		$totalExpense = 0;
		$summary_commission = 0; 

		foreach($report_data as $row)
		{
			$data_row = array();
			$commission_data = $model->get_commission_by_employee(array('employee_id'=>$row['person_id'], 'start_date'=>$start_date, 'end_date'=>$end_date));

			$listExpensesOfThisReceiving = $this->Expense->get_item(array('start_date'=>$start_date, 'end_date'=>$end_date, 'locations'=>implode(',',$locations), 'person_id'=> $row['person_id'], 'employee_col' => $employee_col ),array('task'=>'sum_total_by_sale'));
			$data_row[] = array('data'=>$row['employee'], 'align' => 'left');
			$data_row[] = array('data'=>to_currency($row['subtotal']), 'align' => 'right');
			$data_row[] = array('data'=>to_currency($row['total']), 'align' => 'right');
			$data_row[] = array('data'=>to_currency($row['tax']), 'align' => 'right');
			if($this->has_profit_permission)
			{
				if ($listExpensesOfThisReceiving[0]['expense_type'] == '1') {
					$profit = $row['profit'] - $listExpensesOfThisReceiving[0]['expense'];
					$totalExpense += $listExpensesOfThisReceiving[0]['expense'];
				} else {
					$profit = $row['profit'];
				}
				$data_row[] = array('data'=>to_currency($profit), 'align' => 'right');
				$data_row[] = array('data'=>to_currency($commission_data[0]['sum_commission']), 'align'=>'right');
				$data_row[] = array('data'=>to_currency($profit - $commission_data[0]['sum_commission']), 'align' => 'right');
			}
			$summary_commission += $commission_data[0]['sum_commission'];
			$tabular_data[] = $data_row;			
		}

		$summaryData['profit_before_charging_commission'] = $summaryData['profit'];
		$summaryData['commission'] = $summary_commission;
		$summaryData['profit'] =  $summaryData['profit'] - $totalExpense - $summary_commission;

		$data = array(
			"title" => lang('reports_employees_summary_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $summaryData,
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
		);

		$this->load->view("reports/tabular",$data);
	}

	function summary_taxes_receivings($start_date, $end_date,$do_compare, $compare_start_date, $compare_end_date, $sale_type, $export_excel=0, $offset=0)
	{
		$this->load->model('Receiving');


		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);
		$compare_start_date=rawurldecode($compare_start_date);
		$compare_end_date=rawurldecode($compare_end_date);

		$this->load->model('reports/Summary_taxes_receivings');
		$model = $this->Summary_taxes_receivings;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel'=>$export_excel));

		$this->Receiving->create_receivings_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
		
		$config = array();
		$config['base_url'] = site_url("reports/summary_taxes_receivings/".rawurlencode($start_date).'/'.rawurlencode($end_date).'/'.$do_compare.'/'.rawurlencode($compare_start_date).'/'.rawurlencode($compare_end_date)."/$sale_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 7;		
		$this->load->library('pagination');$this->pagination->initialize($config);

		$tabular_data = array();
		$report_data = $model->getData();
		$summary_data = $model->getSummaryData();
		
		if ($do_compare)
		{
			$model_compare = $this->Summary_taxes_receivings;
			$model_compare->setParams(array('start_date'=>$compare_start_date, 'end_date'=>$compare_end_date, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel'=>$export_excel));
			
			$this->Receiving->drop_receivings_items_temp_table();
			$this->Receiving->create_receivings_items_temp_table(array('start_date'=>$compare_start_date, 'end_date'=>$compare_end_date, 'sale_type' => $sale_type));
			
			$report_data_compare = $model_compare->getData();
			$report_data_summary_compare = $model_compare->getSummaryData();
		}

		foreach($report_data as $row)
		{
			if ($do_compare)
			{
				if (isset($report_data_compare[$row['name']]))
				{
					$row_compare = $report_data_compare[$row['name']];
				}
				else
				{
					$row_compare = FALSE;
				}
			}
			
			
			$tabular_data[] = array(array('data'=>$row['name'], 'align'=>'left'),array('data'=>to_currency($row['subtotal']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['subtotal'] >= $row['subtotal'] ? ($row['subtotal'] == $row_compare['subtotal'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['subtotal']) : ''), 'align'=>'left'),array('data'=>to_currency($row['tax']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['tax'] >= $row['tax'] ? ($row['tax'] == $row_compare['tax'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['tax']).'</span>' : ''), 'align'=>'left'), array('data'=>to_currency($row['total']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['total'] >= $row['total'] ? ($row['total'] == $row_compare['total'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['total']) .'</span>' :''), 'align'=>'left'));
		}
		
		if ($do_compare)
		{
			foreach($summary_data as $key=>$value)
			{
				$summary_data[$key] = to_currency($value) . ' / <span class="compare '.($report_data_summary_compare[$key] >= $value ? ($value == $report_data_summary_compare[$key] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($report_data_summary_compare[$key]).'</span>';
			}
			
		}

		$data = array(
			"title" => lang('reports_taxes_summary_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)).($do_compare  ? ' '. lang('reports_compare_to'). ' '. date(get_date_format(), strtotime($compare_start_date)) .'-'.date(get_date_format(), strtotime($compare_end_date)) : ''),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $summary_data,
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links()
		);

		$this->load->view("reports/tabular",$data);
		
		
	}
	
	//Summary taxes report
	function summary_taxes($start_date, $end_date,$do_compare, $compare_start_date, $compare_end_date,  $sale_type, $export_excel=0, $offset=0)
	{
		$this->load->model('Sale');

		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);
		$compare_start_date=rawurldecode($compare_start_date);
		$compare_end_date=rawurldecode($compare_end_date);

		$this->load->model('reports/Summary_taxes');
		$model = $this->Summary_taxes;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel'=>$export_excel));

		$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$config = array();
		$config['base_url'] = site_url("reports/summary_taxes/".rawurlencode($start_date).'/'.rawurlencode($end_date).'/'.$do_compare.'/'.rawurlencode($compare_start_date).'/'.rawurlencode($compare_end_date)."/$sale_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 7;		
		$this->load->library('pagination');$this->pagination->initialize($config);

		$tabular_data = array();
		$report_data = $model->getData();
		$summary_data = $model->getSummaryData();
		
		if ($do_compare)
		{
			$model_compare = $this->Summary_taxes;
			$model_compare->setParams(array('start_date'=>$compare_start_date, 'end_date'=>$compare_end_date, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel'=>$export_excel));
			
			$this->Sale->drop_sales_items_temp_table();
			$this->Sale->create_sales_items_temp_table(array('start_date'=>$compare_start_date, 'end_date'=>$compare_end_date, 'sale_type' => $sale_type));
			
			$report_data_compare = $model_compare->getData();
			$report_data_summary_compare = $model_compare->getSummaryData();
		}

		foreach($report_data as $row)
		{
			if ($do_compare)
			{
				if (isset($report_data_compare[$row['name']]))
				{
					$row_compare = $report_data_compare[$row['name']];
				}
				else
				{
					$row_compare = FALSE;
				}
			}
			
			
			$tabular_data[] = array(array('data'=>$row['name'], 'align'=>'left'),array('data'=>to_currency($row['subtotal']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['subtotal'] >= $row['subtotal'] ? ($row['subtotal'] == $row_compare['subtotal'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['subtotal']).'</span>' : ''), 'align'=>'left'),array('data'=>to_currency($row['tax']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['tax'] >= $row['tax'] ? ($row['tax'] == $row_compare['tax'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['tax']).'</span>' : ''), 'align'=>'left'), array('data'=>to_currency($row['total']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['total'] >= $row['total'] ? ($row['total'] == $row_compare['total'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['total']) .'</span>' :''), 'align'=>'left'));
		}
		
		if ($do_compare)
		{
			foreach($summary_data as $key=>$value)
			{
				$summary_data[$key] = to_currency($value) . ' / <span class="compare '.($report_data_summary_compare[$key] >= $value ? ($value == $report_data_summary_compare[$key] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($report_data_summary_compare[$key]).'</span>';
			}
			
		}

		$data = array(
			"title" => lang('reports_taxes_summary_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)).($do_compare  ? ' '. lang('reports_compare_to'). ' '. date(get_date_format(), strtotime($compare_start_date)) .'-'.date(get_date_format(), strtotime($compare_end_date)) : ''),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $summary_data,
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links()
		);

		$this->load->view("reports/tabular",$data);
	}

	//Summary discounts report
	function summary_discounts($start_date, $end_date, $sale_type, $export_excel=0, $offset = 0)
	{
		$this->load->model('Sale');

		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);

		$this->load->model('reports/Summary_discounts');
		$model = $this->Summary_discounts;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'export_excel' => $export_excel, 'offset' => $offset));

		$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$config = array();
		$config['base_url'] = site_url("reports/summary_discounts/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$sale_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 7;		
		$this->load->library('pagination');$this->pagination->initialize($config);

		$tabular_data = array();
		$report_data = $model->getData();

		foreach($report_data as $row)
		{
			$tabular_data[] = array(array('data'=>$row['discount'], 'align'=>'left'),array('data'=>$row['summary'], 'align'=>'left'));
		}

		$data = array(
			"title" => lang('reports_discounts_summary_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links()
		);

		$this->load->view("reports/tabular",$data);
	}

	function store_account_activity($start_date, $end_date, $export_excel=0, $offset=0)
	{

		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);

		$this->load->model('reports/Store_account_activity');
		$model = $this->Store_account_activity;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'offset'=> $offset, 'export_excel' => $export_excel));

		$config = array();
		$config['base_url'] = site_url("reports/store_account_activity/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 6;

		$this->load->library('pagination');$this->pagination->initialize($config);

		$tabular_data = array();
		$report_data = $model->getData();

		foreach($report_data as $row)
		{
			$tabular_data[] = array(array('data'=>$row['sno'], 'align'=> 'left'),
				array('data'=>$row['first_name'].' '.$row['last_name'], 'align'=> 'left'),
				array('data'=>date(get_date_format().'-'.get_time_format(), strtotime($row['date'])), 'align'=> 'left'),
				array('data'=>$row['sale_id'] ? anchor('sales/receipt/'.$row['sale_id'], $this->config->item('sale_prefix').' '.$row['sale_id'], array('target' => '_blank')) : '-', 'align'=> 'center'),
				array('data'=> $row['transaction_amount'] > 0 ? to_currency($row['transaction_amount']) : to_currency(0), 'align'=> 'right'),
				array('data'=>$row['transaction_amount'] < 0 ? to_currency($row['transaction_amount'] * -1)  : to_currency(0), 'align'=> 'right'),
				array('data'=>to_currency($row['balance']), 'align'=> 'right'),
				array('data'=>$row['items'], 'align'=> 'left'),
				array('data'=>$row['comment'], 'align'=> 'left'));

		}

		$data = array(
			"title" => lang('reports_store_account_activity_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
		);

		$this->load->view("reports/tabular",$data);
	}
	
	function summary_payments($start_date, $end_date,$do_compare, $compare_start_date, $compare_end_date, $sale_type, $export_excel=0, $offset=0)
	{
		$this->load->model('Sale');

		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);
		$compare_start_date=rawurldecode($compare_start_date);
		$compare_end_date=rawurldecode($compare_end_date);
		

		$this->load->model('reports/Summary_payments');
		$model = $this->Summary_payments;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'offset'=> $offset, 'export_excel' => $export_excel));
		$sale_ids = $model->get_sale_ids_for_payments();
		$this->Sale->create_sales_items_temp_table(array('sale_ids' => $sale_ids, 'sale_type' => $sale_type));

		$config = array();
		$config['base_url'] = site_url("reports/summary_payments/".rawurlencode($start_date).'/'.rawurlencode($end_date).'/'.$do_compare.'/'.rawurlencode($compare_start_date).'/'.rawurlencode($compare_end_date)."/$sale_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 10;

		$this->load->library('pagination');$this->pagination->initialize($config);
		$tabular_data = array();
		$report_data = $model->getData();
		$summary_data = $model->getSummaryData();
		
		if ($do_compare)
		{
			$model_compare = $this->Summary_payments;
			$model_compare->setParams(array('start_date'=>$compare_start_date, 'end_date'=>$compare_end_date, 'sale_type' => $sale_type, 'offset'=> $offset, 'export_excel' => $export_excel));
			$sale_ids = $model_compare->get_sale_ids_for_payments();
			$this->Sale->drop_sales_items_temp_table();
			$this->Sale->create_sales_items_temp_table(array('sale_ids' => $sale_ids, 'sale_type' => $sale_type));
			$report_data_compare = $model_compare->getData();
			$report_data_summary_compare = $model_compare->getSummaryData();
		}
		
		
		foreach($report_data as $row)
		{
			if ($do_compare)
			{
				if (isset($report_data_compare[$row['payment_type']]))
				{
					$row_compare = $report_data_compare[$row['payment_type']];
				}
				else
				{
					$row_compare = FALSE;
				}
			}
			
			$tabular_data[] = array(array('data'=>$row['payment_type'], 'align'=>'left'),array('data'=>to_currency($row['payment_amount']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['payment_amount'] >= $row['payment_amount'] ? ($row['payment_amount'] == $row_compare['payment_amount'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['payment_amount']) .'</span>' :''), 'align'=>'right'));
		}
		
		
		if ($do_compare)
		{
			foreach($summary_data as $key=>$value)
			{
				$summary_data[$key] = to_currency($value) . ' / <span class="compare '.($report_data_summary_compare[$key] >= $value ? ($value == $report_data_summary_compare[$key] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($report_data_summary_compare[$key]).'</span>';
			}
			
		}
		

		$data = array(
			"title" => lang('reports_payments_summary_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)).($do_compare  ? ' '. lang('reports_compare_to'). ' '. date(get_date_format(), strtotime($compare_start_date)) .'-'.date(get_date_format(), strtotime($compare_end_date)) : ''),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $summary_data,
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
		);

		$this->load->view("reports/tabular",$data);
	}

	//Input for reports that require only a date range. (see routes.php to see that all graphical summary reports route here)
	function date_input()
	{
		$data = $this->_get_common_report_data();
		$this->load->view("reports/date_input",$data);
	}
	
	function date_input_customers()
	{
		$data = $this->_get_common_report_data();
		$this->load->view("reports/date_input_customers",$data);		
	}
	
	function date_input_time()
	{
		$data = $this->_get_common_report_data();
		$this->load->view("reports/date_input_time",$data);
	}
	
	function timeclock_input()
	{
		$data = $this->_get_common_report_data();
		$data['specific_input_name'] = lang('reports_employee');

		$employees = array('' => lang('common_all'));
		foreach($this->Employee->get_all()->result() as $employee)
		{
			$employees[$employee->person_id] = $employee->first_name .' '.$employee->last_name;
		}
		$data['specific_input_data'] = $employees;
		
		$this->load->view("reports/timeclock_input",$data);
	}

	function employees_date_input()
	{
		$data = $this->_get_common_report_data();
		$this->load->view("reports/employees_date_input",$data);
	}

	//Graphical summary sales report
	function graphical_summary_sales($start_date, $end_date, $sale_type)
	{
		$this->load->model('Sale');

		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_sales');
		$model = $this->Summary_sales;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$data = array(
			"title" => lang('reports_sales_summary_report'),
			"graph_file" => site_url("reports/graphical_summary_sales_graph/$start_date/$end_date/$sale_type"),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"summary_data" => $model->getSummaryData()
		);

		$this->load->view("reports/graphical",$data);
	}

	//The actual graph data
	function graphical_summary_sales_graph($start_date, $end_date, $sale_type)
	{
		$this->load->model('Sale');
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_sales');
		$model = $this->Summary_sales;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
		$report_data = $model->getData();

		$graph_data = array();
		foreach($report_data as $row)
		{
			$graph_data[date(get_date_format(), strtotime($row['sale_date']))]= to_currency_no_money($row['total']);
		}

		$currency_symbol = $this->config->item('currency_symbol') ? $this->config->item('currency_symbol') : '$';

		$data = array(
			"title" => lang('reports_sales_summary_report'),
			"data" => $graph_data,
			"tooltip_template" => "<%=label %>: <%= parseFloat(Math.round(value * 100) / 100).toFixed(".$this->decimals.") %> $currency_symbol",
		);

		$this->load->view("reports/graphs/line",$data);

	}

	//Graphical summary items report
	function graphical_summary_items($start_date, $end_date, $supplier_id = -1, $category_id = -1, $sale_type = 'all')
	{
		$this->load->model('Sale');

		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_items');
		$model = $this->Summary_items;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'category_id' => $category_id, 'supplier_id' => $supplier_id));

		$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$data = array(
			"title" => lang('reports_items_summary_report'),
			"graph_file" => site_url("reports/graphical_summary_items_graph/$start_date/$end_date/$supplier_id/$category_id/$sale_type"),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"summary_data" => $model->getSummaryData()
		);

		$this->load->view("reports/graphical",$data);
	}

	//The actual graph data
	function graphical_summary_items_graph($start_date, $end_date, $supplier_id = -1, $category_id = -1, $sale_type = 'all')
	{
		$this->load->model('Sale');
		
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_items');
		$model = $this->Summary_items;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'category_id' => $category_id, 'supplier_id' => $supplier_id));

		$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
		$report_data = $model->getData();

		$graph_data = array();
		foreach($report_data as $row)
		{
			$graph_data[$row['name']] = to_currency_no_money($row['total']);
		}

		$currency_symbol = $this->config->item('currency_symbol') ? $this->config->item('currency_symbol') : '$';

		$data = array(
			"title" => lang('reports_items_summary_report'),
			"data" => $graph_data,
			"tooltip_template" => "<%=label %>: <%= parseFloat(Math.round(value * 100) / 100).toFixed(".$this->decimals.") %> $currency_symbol",
			"legend_template" => "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%> (<%=parseFloat(Math.round(segments[i].value * 100) / 100).toFixed(".$this->decimals.")%> $currency_symbol)<%}%></li><%}%></ul>"			
		);
		$this->load->view("reports/graphs/pie",$data);
	}

	//Graphical summary item kits report
	function graphical_summary_item_kits($start_date, $end_date, $sale_type)
	{
		$this->load->model('Sale');

		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_item_kits');
		$model = $this->Summary_item_kits;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$data = array(
			"title" => lang('reports_item_kits_summary_report'),
			"graph_file" => site_url("reports/graphical_summary_item_kits_graph/$start_date/$end_date/$sale_type"),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"summary_data" => $model->getSummaryData()
		);

		$this->load->view("reports/graphical",$data);
	}

	//The actual graph data
	function graphical_summary_item_kits_graph($start_date, $end_date, $sale_type)
	{
		$this->load->model('Sale');
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_item_kits');
		$model = $this->Summary_item_kits;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
		$report_data = $model->getData();

		$graph_data = array();
		foreach($report_data as $row)
		{
			$graph_data[$row['name']] = to_currency_no_money($row['total']);
		}
		
		$currency_symbol = $this->config->item('currency_symbol') ? $this->config->item('currency_symbol') : '$';
		

		$data = array(
			"title" => lang('reports_item_kits_summary_report'),
			"data" => $graph_data,
			"tooltip_template" => "<%=label %>: <%= parseFloat(Math.round(value * 100) / 100).toFixed(".$this->decimals.") %> $currency_symbol",
			"legend_template" => "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%> (<%=parseFloat(Math.round(segments[i].value * 100) / 100).toFixed(".$this->decimals.")%> $currency_symbol)<%}%></li><%}%></ul>"
		);

		$this->load->view("reports/graphs/pie",$data);
	}

	//Graphical summary customers report
	function graphical_summary_categories($start_date, $end_date, $sale_type)
	{
		$this->load->model('Sale');

		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_categories');
		$model = $this->Summary_categories;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$data = array(
			"title" => lang('reports_categories_summary_report'),
			"graph_file" => site_url("reports/graphical_summary_categories_graph/$start_date/$end_date/$sale_type"),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"summary_data" => $model->getSummaryData()
		);

		$this->load->view("reports/graphical",$data);
	}

	//The actual graph data
	function graphical_summary_categories_graph($start_date, $end_date, $sale_type)
	{
		$this->load->model('Sale');
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_categories');
		$model = $this->Summary_categories;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$report_data = $model->getData();

		$graph_data = array();
		foreach($report_data as $row)
		{
			$graph_data[$row['category']] = to_currency_no_money($row['total']);
		}


		$currency_symbol = $this->config->item('currency_symbol') ? $this->config->item('currency_symbol') : '$';

		$data = array(
			"title" => lang('reports_categories_summary_report'),
			"data" => $graph_data,
			"tooltip_template" => "<%=label %>: <%= parseFloat(Math.round(value * 100) / 100).toFixed(".$this->decimals.") %> $currency_symbol",
			"legend_template" => "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%> (<%=parseFloat(Math.round(segments[i].value * 100) / 100).toFixed(".$this->decimals.")%> $currency_symbol)<%}%></li><%}%></ul>"
		);

		$this->load->view("reports/graphs/pie",$data);
	}

	function graphical_summary_suppliers($start_date, $end_date, $sale_type)
	{
		$this->load->model('Sale');

		
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_suppliers');
		$model = $this->Summary_suppliers;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$data = array(
			"title" => lang('reports_suppliers_summary_report'),
			"graph_file" => site_url("reports/graphical_summary_suppliers_graph/$start_date/$end_date/$sale_type"),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"summary_data" => $model->getSummaryData()
		);

		$this->load->view("reports/graphical",$data);
	}
	
	function graphical_summary_suppliers_receivings($start_date, $end_date, $sale_type)
	{
		$this->load->model('Receiving');

		
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_suppliers_receivings');
		$model = $this->Summary_suppliers_receivings;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$this->Receiving->create_receivings_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$data = array(
			"title" => lang('reports_suppliers_receivings_summary_report'),
			"graph_file" => site_url("reports/graphical_summary_suppliers_receivings_graph/$start_date/$end_date/$sale_type"),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"summary_data" => $model->getSummaryData()
		);

		$this->load->view("reports/graphical",$data);
	}
	
	
	
	//The actual graph data
	function graphical_summary_suppliers_receivings_graph($start_date, $end_date, $sale_type)
	{
		$this->load->model('Receiving');
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_suppliers_receivings');
		$model = $this->Summary_suppliers_receivings;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$this->Receiving->create_receivings_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$report_data = $model->getData();

		$graph_data = array();
		foreach($report_data as $row)
		{
			$graph_data[$row['supplier']] = to_currency_no_money($row['total']);
		}

		$currency_symbol = $this->config->item('currency_symbol') ? $this->config->item('currency_symbol') : '$';

		$data = array(
			"title" => lang('reports_suppliers_receivings_summary_report'),
			"data" => $graph_data,
			"tooltip_template" => "<%=label %>: <%= parseFloat(Math.round(value * 100) / 100).toFixed(".$this->decimals.") %> $currency_symbol",
			"legend_template" => "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%> (<%=parseFloat(Math.round(segments[i].value * 100) / 100).toFixed(".$this->decimals.")%> $currency_symbol)<%}%></li><%}%></ul>"
		);

		$this->load->view("reports/graphs/pie",$data);
	}

	//The actual graph data
	function graphical_summary_suppliers_graph($start_date, $end_date, $sale_type)
	{
		$this->load->model('Sale');
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_suppliers');
		$model = $this->Summary_suppliers;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$report_data = $model->getData();

		$graph_data = array();
		foreach($report_data as $row)
		{
			$graph_data[$row['supplier']] = to_currency_no_money($row['total']);
		}


		$currency_symbol = $this->config->item('currency_symbol') ? $this->config->item('currency_symbol') : '$';

		$data = array(
			"title" => lang('reports_suppliers_summary_report'),
			"data" => $graph_data,
			"tooltip_template" => "<%=label %>: <%= parseFloat(Math.round(value * 100) / 100).toFixed(".$this->decimals.") %> $currency_symbol",
			"legend_template" => "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%> (<%=parseFloat(Math.round(segments[i].value * 100) / 100).toFixed(".$this->decimals.")%> $currency_symbol)<%}%></li><%}%></ul>"
		);
		

		$this->load->view("reports/graphs/pie",$data);
	}

	function graphical_summary_employees($start_date, $end_date, $sale_type, $employee_type)
	{
		$this->load->model('Sale');
		$this->check_action_permission('view_employees');
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_employees');
		$model = $this->Summary_employees;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'employee_type' => $employee_type));

		$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$data = array(
			"title" => lang('reports_employees_summary_report'),
			"graph_file" => site_url("reports/graphical_summary_employees_graph/$start_date/$end_date/$sale_type/$employee_type"),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"summary_data" => $model->getSummaryData()
		);

		$this->load->view("reports/graphical",$data);
	}

	//The actual graph data
	function graphical_summary_employees_graph($start_date, $end_date, $sale_type, $employee_type)
	{
		$this->load->model('Sale');
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_employees');
		$model = $this->Summary_employees;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'employee_type' => $employee_type));

		$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
		$report_data = $model->getData();

		$graph_data = array();
		foreach($report_data as $row)
		{
			$graph_data[$row['employee']] = to_currency_no_money($row['total']);
		}
		
		$currency_symbol = $this->config->item('currency_symbol') ? $this->config->item('currency_symbol') : '$';
		

		$data = array(
			"title" => lang('reports_employees_summary_report'),
			"data" => $graph_data,
			"tooltip_template" => "<%=label %>: <%= parseFloat(Math.round(value * 100) / 100).toFixed(".$this->decimals.") %> $currency_symbol",
		);

		$this->load->view("reports/graphs/bar",$data);
	}

	function graphical_summary_taxes($start_date, $end_date, $sale_type)
	{
		$this->load->model('Sale');

		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_taxes');
		$model = $this->Summary_taxes;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$data = array(
			"title" => lang('reports_taxes_summary_report'),
			"graph_file" => site_url("reports/graphical_summary_taxes_graph/$start_date/$end_date/$sale_type"),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"summary_data" => $model->getSummaryData()
		);

		$this->load->view("reports/graphical",$data);
	}

	//The actual graph data
	function graphical_summary_taxes_graph($start_date, $end_date, $sale_type)
	{
		$this->load->model('Sale');
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_taxes');
		$model = $this->Summary_taxes;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
		$report_data = $model->getData();

		$graph_data = array();
		foreach($report_data as $row)
		{
			$graph_data[$row['name']] = to_currency_no_money($row['tax']);
		}

		$currency_symbol = $this->config->item('currency_symbol') ? $this->config->item('currency_symbol') : '$';
		
		$data = array(
			"title" => lang('reports_taxes_summary_report'),
			"data" => $graph_data,
			"tooltip_template" => "<%=label %>: <%= parseFloat(Math.round(value * 100) / 100).toFixed(".$this->decimals.") %> $currency_symbol",
		);

		$this->load->view("reports/graphs/bar",$data);
	}

	function graphical_summary_taxes_receivings($start_date, $end_date, $sale_type)
	{
		$this->load->model('Receiving');

		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_taxes_receivings');
		$model = $this->Summary_taxes_receivings;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$this->Receiving->create_receivings_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$data = array(
			"title" => lang('reports_taxes_summary_report'),
			"graph_file" => site_url("reports/graphical_summary_taxes_receivings_graph/$start_date/$end_date/$sale_type"),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"summary_data" => $model->getSummaryData()
		);

		$this->load->view("reports/graphical",$data);
	}

	//The actual graph data
	function graphical_summary_taxes_receivings_graph($start_date, $end_date, $sale_type)
	{
		$this->load->model('Receiving');
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_taxes_receivings');
		$model = $this->Summary_taxes_receivings;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$this->Receiving->create_receivings_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
		$report_data = $model->getData();

		$graph_data = array();
		foreach($report_data as $row)
		{
			$graph_data[$row['name']] = to_currency_no_money($row['tax']);
		}

		$currency_symbol = $this->config->item('currency_symbol') ? $this->config->item('currency_symbol') : '$';
		
		$data = array(
			"title" => lang('reports_taxes_summary_report'),
			"data" => $graph_data,
			"tooltip_template" => "<%=label %>: <%= parseFloat(Math.round(value * 100) / 100).toFixed(".$this->decimals.") %> $currency_symbol",
		);

		$this->load->view("reports/graphs/bar",$data);
	}	

	//Graphical summary customers report
	function graphical_summary_customers($start_date, $end_date, $sale_type, $total_spent_condition = 'any', $total_spent_amount = 0)
	{
		$this->load->model('Sale');

		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_customers');
		$model = $this->Summary_customers;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'total_spent_condition' => $total_spent_condition, 'total_spent_amount' => $total_spent_amount));

		$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$data = array(
			"title" => lang('reports_customers_summary_report'),
			"graph_file" => site_url("reports/graphical_summary_customers_graph/$start_date/$end_date/$sale_type/$total_spent_condition/$total_spent_amount"),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"summary_data" => $model->getSummaryData()
		);

		$this->load->view("reports/graphical",$data);
	}

	//The actual graph data
	function graphical_summary_customers_graph($start_date, $end_date, $sale_type, $total_spent_condition = 'any', $total_spent_amount = 0)
	{
		$this->load->model('Sale');
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		$this->load->model('reports/Summary_customers');
		$model = $this->Summary_customers;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'total_spent_condition' => $total_spent_condition, 'total_spent_amount' => $total_spent_amount));

		$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$report_data = $model->getData();

		$graph_data = array();
		foreach($report_data as $row)
		{
			$graph_data[$row['customer']] = to_currency_no_money($row['total']);
		}
		
		$currency_symbol = $this->config->item('currency_symbol') ? $this->config->item('currency_symbol') : '$';

		$data = array(
			"title" => lang('reports_customers_summary_report'),
			"data" => $graph_data,
			"tooltip_template" => "<%=label %>: <%= parseFloat(Math.round(value * 100) / 100).toFixed(".$this->decimals.") %> $currency_symbol",
			"legend_template" => "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%> (<%=parseFloat(Math.round(segments[i].value * 100) / 100).toFixed(".$this->decimals.")%> $currency_symbol)<%}%></li><%}%></ul>"
		);

		$this->load->view("reports/graphs/pie",$data);
	}

	//Graphical summary discounts report
	function graphical_summary_discounts($start_date, $end_date, $sale_type)
	{
		$this->load->model('Sale');

		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_discounts');
		$model = $this->Summary_discounts;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$data = array(
			"title" => lang('reports_discounts_summary_report'),
			"graph_file" => site_url("reports/graphical_summary_discounts_graph/$start_date/$end_date/$sale_type"),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"summary_data" => $model->getSummaryData()
		);

		$this->load->view("reports/graphical",$data);
	}

	//The actual graph data
	function graphical_summary_discounts_graph($start_date, $end_date, $sale_type)
	{
		$this->load->model('Sale');
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_discounts');
		$model = $this->Summary_discounts;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
		$report_data = $model->getData();

		$graph_data = array();
		foreach($report_data as $row)
		{
			if (isset($row['discount_count']))
			{
				$graph_data[$row['discount']] = $row['discount_count'];
			}
			else
			{
				$graph_data[$row['discount']] = $row['summary'];
			}
		}

		$data = array(
			"title" => lang('reports_discounts_summary_report'),
			"data" => $graph_data
		);

		$this->load->view("reports/graphs/bar",$data);
	}

	function graphical_summary_payments($start_date, $end_date, $sale_type)
	{
		$this->load->model('Sale');

		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_payments');
		$model = $this->Summary_payments;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
		$sale_ids = $model->get_sale_ids_for_payments();
		$this->Sale->create_sales_items_temp_table(array('sale_ids' => $sale_ids, 'sale_type' => $sale_type));

		$data = array(
			"title" => lang('reports_payments_summary_report'),
			"graph_file" => site_url("reports/graphical_summary_payments_graph/$start_date/$end_date/$sale_type"),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"summary_data" => $model->getSummaryData()
		);

		$this->load->view("reports/graphical",$data);
	}

	//The actual graph data
	function graphical_summary_payments_graph($start_date, $end_date, $sale_type)
	{
		$this->load->model('Sale');
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_payments');
		$model = $this->Summary_payments;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$sale_ids = $model->get_sale_ids_for_payments();
		$this->Sale->create_sales_items_temp_table(array('sale_ids' => $sale_ids, 'sale_type' => $sale_type));
		$report_data = $model->getData();

		$graph_data = array();
		foreach($report_data as $row)
		{
			$graph_data[$row['payment_type']] = to_currency_no_money($row['payment_amount']);
		}
		$currency_symbol = $this->config->item('currency_symbol') ? $this->config->item('currency_symbol') : '$';


		$data = array(
			"title" => lang('reports_payments_summary_report'),
			"data" => $graph_data,
			"tooltip_template" => "<%=label %>: <%= parseFloat(Math.round(value * 100) / 100).toFixed(".$this->decimals.") %> $currency_symbol",
		);

		$this->load->view("reports/graphs/bar",$data);
	}
	function specific_customer_input()
	{
		$data = $this->_get_common_report_data(TRUE);
		$data['specific_input_name'] = lang('reports_customer');
		$data['search_suggestion_url'] = site_url('reports/customer_search');
		$this->load->view("reports/specific_input",$data);
	}
	
	function specific_customer_store_account_input()
	{
		$data = $this->_get_common_report_data(TRUE);
		$data['specific_input_name'] = lang('reports_customer');
		$data['search_suggestion_url'] = site_url('reports/customer_search');
		$this->load->view("reports/specific_input",$data);
	}


	function specific_customer($start_date, $end_date, $customer_id, $sale_type, $export_excel=0, $offset=0)
	{
		$this->load->model('Sale');
		$this->load->model('Customer');

		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);

		$this->load->model('reports/Specific_customer');
		$model = $this->Specific_customer;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'customer_id' =>$customer_id, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel'=>$export_excel));

		$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'customer_id' =>$customer_id, 'sale_type' => $sale_type));
		
		$config = array();
		$config['base_url'] = site_url("reports/specific_customer/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$customer_id/$sale_type/$export_excel");

		$total = array($model->getTotalRows(), $model->getTotalRowsMailHitory($customer_id));
		rsort($total);
		$config['total_rows'] = $total[0];

		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 8;
		$this->load->library('pagination');
		$this->pagination->initialize($config);
		
		
		$headers = $model->getDataColumns();
		$report_data = $model->getData();

		$summary_data = array();
		$details_data = array();
		$location_count = count(Report::get_selected_location_ids());

		foreach($report_data['summary'] as $key=>$row)
		{
			$summary_data_row = array();			
			$summary_data_row[] = array('data'=>anchor('sales/receipt/'.$row['sale_id'], '<i class="ion-printer"></i>', array('target' => '_blank', 'class'=>'hidden-print')).'<span class="visible-print">'.$row['sale_id'].'</span>'.anchor('sales/edit/'.$row['sale_id'], '<i class="ion-document-text"></i>', array('target' => '_blank')).' '.anchor('sales/edit/'.$row['sale_id'], lang('common_edit').' '.$row['sale_id'], array('target' => '_blank','class'=>'hidden-print')), 'align'=>'left');
			if ($location_count > 1)
			{
				$summary_data_row[] = array('data'=>$row['location_name'], 'align' => 'left');
			}
			
			$summary_data_row[] = array('data'=>date(get_date_format().'-'.get_time_format(), strtotime($row['sale_time'])), 'align'=> 'left');
			$summary_data_row[] = array('data'=>$row['register_name'], 'align'=> 'left');
			$summary_data_row[] = array('data'=>to_quantity($row['items_purchased']), 'align'=> 'left');
			$summary_data_row[] = array('data'=>$row['employee_name'].($row['sold_by_employee'] && $row['sold_by_employee'] != $row['employee_name'] ? '/'. $row['sold_by_employee']: ''), 'align'=>'left');
			$summary_data_row[] = array('data'=>to_currency($row['subtotal']), 'align'=> 'right');
			$summary_data_row[] = array('data'=>to_currency($row['total']), 'align'=> 'right');
			$summary_data_row[] = array('data'=>to_currency($row['tax']), 'align'=> 'right');
			if($this->has_profit_permission)
			{
				$summary_data_row[] = array('data'=>to_currency($row['profit']), 'align'=>'right');
			}

			$summary_data_row[] = array('data'=>$row['payment_type'], 'align'=>'right');
			$summary_data_row[] = array('data'=>$row['comment'], 'align'=>'right');
			$summary_data[$key] = $summary_data_row;
			

			foreach($report_data['details'][$key] as $drow)
			{
				$details_data_row = array();
				$details_data_row[] = array('data'=>isset($drow['item_name']) ? $drow['item_name'] : $drow['item_kit_name'], 'align'=>'left');
				$details_data_row[] = array('data'=>$drow['category'], 'align'=>'left');
				$details_data_row[] = array('data'=>$drow['serialnumber'], 'align'=>'left');
				$details_data_row[] = array('data'=>$drow['description'], 'align'=>'left');
				$details_data_row[] = array('data'=>to_quantity($drow['quantity_purchased']), 'align'=>'left');
				
				$details_data_row[] = array('data'=>to_currency($drow['subtotal']), 'align'=>'right');
				$details_data_row[] = array('data'=>to_currency($drow['total']), 'align'=>'right');
				$details_data_row[] = array('data'=>to_currency($drow['tax']), 'align'=>'right');
				
				if($this->has_profit_permission)
				{
					$details_data_row[] = array('data'=>to_currency($drow['profit']), 'align'=>'right');					
				}
				$details_data_row[] = array('data'=>$drow['discount_percent'].'%', 'align'=> 'left');
				
				$details_data[$key][] = $details_data_row;
			}
		}

		$customer_info = $this->Customer->get_info($customer_id);

		$send_mail = $this->Customer->getAllMailHistory($customer_id, 20, $offset);
		$send_sms = $this->Customer->getAllSmsHistory($customer_id, 20, $offset);

		$data = array(
			"title" => $customer_info->first_name .' '. $customer_info->last_name.' '.lang('reports_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"headers" => $model->getDataColumns(),
			"details_data" => $details_data,
			"overall_summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
			"summary_data" => $summary_data,
			"controller_name" => $this->_controller_name,
			"controller_path" => $this->uri->segment(2),
			//"send_mail" => $send_mail,
		);
		

		$data['send_mail'] = $send_mail;
		$data['send_sms'] = $send_sms;
		//Báo giá

		$data['sale_materials'] = $this->Sale->getAllSalesByCustomer($customer_id);
		$data['congnos'] = $this->Customer->get_all_suspends($config['per_page'],  $offset, 'last_name', 'asc', 'view_scope_owner');

		//cong no
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);

		$this->load->model('reports/Specific_customer_store_account');
		$model_cn = $this->Specific_customer_store_account;		
		$model_cn->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'customer_id' =>$customer_id, 'sale_type' => $sale_type, 'offset'=> $offset, 'export_excel' => $export_excel));

		$tabular_data_cn= array();
		$report_data_cn = $model_cn->getData();

		$data['datas'] = $report_data_cn;
		$this->load->view("reports/tabular_details",$data);
	}

	function specific_cus($customer_id = '') {


		$data['action'] = $this->uri->segment(2);
		$data['locations'] = $this->Location->list_item();
		$data['title'] = lang('reports_specific_customer');
		if(!empty($customer_id) || !empty($_SESSION['specific_cus'])) {

			$data['url_print'] = $_SESSION['specific_cus']['url_print'];
			$data['filter']    = $filter = $_SESSION['specific_cus'];
			unset($_SESSION['specific_cus']);
			if($data['filter']['export_excel'] == 1)
				redirect($data['url_print']);
			else {
				if (!empty($customer_id)) {
					$data['filter']['customer_id'] = $customer_id;
				}
				$data['person_info'] = $this->Customer->get_info($data['filter']['customer_id']);
				$employees = $this->Employee->getEmployeesByCurrentLocation();
				$employeesDropbox = array();
				foreach ($employees as $employee) {
					$employeesDropbox["" . $employee['person_id']] = $employee['username'];
				}
				$employeesDropbox['all'] = 'Tất cả';
				$data['employee_manager'] = $employeesDropbox;

                 //get business type
				$business_type_data = $this->db->select('*')->from('phppos_customers_business_type')->where('customer_id = '.$data['filter']['customer_id'])->get()->result();
				$data['person_info']->business_type = $business_type_data;

				 //get geographical
				$geographical_data = $this->db->select('*')->from('phppos_customers_geographical_area')->where('customer_id = '.$data['filter']['customer_id'])->get()->result();
				$data['person_info']->geographical = $geographical_data;

                 //type customers
				$customer_typers = array();
				$customer_typers_result = $data['type_customers'] = $this->Customer->get_Customer_type();
				if (count($customer_typers_result) > 0) {
					$customer_typers[0] = lang('common_none');
					foreach ($customer_typers_result as $type) {
						$customer_typers[$type['id']] = $type['name'];
					}
				}
				$data['type_customers'] = $customer_typers;

				$data['sex'] = array('1' => 'Nam', '2' => 'Nữ');

                 //tier
				$danh_muc_tong_hop = $this->Customer->get_danh_muc_khach_hang(false, false, $iValid = false);
				foreach ($danh_muc_tong_hop as $key => $value) {
					$data[$key][0] = lang('common_none');
					foreach ($value as $name) {
						$data[$key][$name['id']] = $name['name'];
					}
				}

                 //categories
				$danh_muc_tong_hop = $this->Customer->get_danh_muc_khach_hang(false, false, $iValid = TRUE);
				foreach ($danh_muc_tong_hop as $key => $value) {
					foreach ($value as $name) {
						$has_access = $this->Customer->kiem_tra_danh_muc_customer($key, $data['filter']['customer_id'], $name['id']);
						$data[$key][$name['id']]['name'] = $name['name'];
                         # biến $has_access được sử dụng như 1 cách để kiểm soát dữ liệu được hiển thị
						$data[$key][$name['id']]['has_access'] = $has_access;
					}
				}

                 //customer_reference
				$data['customer_reference'] = $this->Customer->get_danh_muc_khach_hang('customer_reference');

                 //thong tin lien he va nguoi dau moi
				$data['thong_tin_lien_he'] = $this->Customer->lay_thong_tin_lien_he_them($data['filter']['customer_id']);
				$data['thong_tin_dau_moi'] = $this->Customer->lay_thong_tin_dau_moi_them($data['filter']['customer_id']);
               //   echo "<pre>";
              	// var_dump($data);die();
				$this->load->view("reports/specific_cus", $data);
			}

		} else {
			$data['inputs']      = array('input_date_range','select_customers','input_locations');
			$data['no_excel']    = true;
			$data['customer_id'] = $this->input->get('customer_id', -1);
			$this->load->view("reports/n9_tabular",$data);
		}
	}

	function specific_cus_order_detail($sale_id = -1) {
		$post = $this->input->post();
        //$post['sale_id'] = 62;
		if(!empty($post)) {
			$sale_info = $this->sale_lib->get_sale($post['sale_id'], array('included_commission'=>true));
			$this->load->view("reports/partials/order_detail", $sale_info);
		}
		if($sale_id != -1){
			$sale_info = $this->sale_lib->get_sale($sale_id, array('included_commission'=>true));
			$view = $this->load->view("reports/partials/chi_phi_don_hang", $sale_info,TRUE);
			return $view;
		}
	}

	function specific_cus_store() {
		$post  = $this->input->post();
		$arrParam = array_merge($post, $this->input->get());
		$this->load->model('reports/Specific_customer');
		$model = $this->Specific_customer;
		if(!empty($post)) {
			if($post['option'] == 'history_trans') {
				$result = array('total_list'=> $total_list, 'html_string'=>$html_string, 'pagination'=>$pagination);
				echo json_encode($result);
			}

            //create sale temp table
			if($post['option'] == 'purchase') {
				$time_condition = array();
				if(!empty($post['start_date'])) {
					$time_condition[] =  's.sale_time >= \''.$post['start_date'].'\'';
				}

				if(!empty($post['end_date'])) {
					$time_condition[] =  's.sale_time <= \''.$post['end_date'].'\'';
				}

				if(!empty($time_condition)) {
					$time_condition = ' AND ' . implode(' AND ', $time_condition);
				}else
				$time_condition = '';

				$location_ids = $post['location_ids'];

				$customer_id = $post['customer_id'];

				$where = "s.suspended = 0 AND s.store_account_payment = 0 $time_condition AND s.location_id IN ($location_ids) AND s.deleted = 0 AND s.customer_id = $customer_id";
				$this->Sale->create_sales_items_temp_table_n9(array('where'=>$where));
			}
			if($post['option'] != 'balance') {
				$post = array_merge($post, $this->input->get());
				$post['paginator'] = $this->_paginator;
				$post['page']      =  $this->uri->segment(3, 1);

				$config['base_url'] = base_url() . 'reports/specific_cus_store';

				$config['total_rows'] = $model->count_item($post);

				$config['per_page'] = $post['paginator']['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
				$config['uri_segment'] = 3;
				$config['use_page_numbers'] = TRUE;


				$items = $model->list_item($post);
				$html_string = $this->load->view('reports/row/specific_cus_'.$post['option'],array('items'=>$items), true);
	            // var_dump($html_string);

				$this->load->library("pagination");
				$this->pagination->initialize($config);
				$this->pagination->createConfig('front-end');

				$pagination = $this->pagination->create_ajax();

				$total_list = $model->get_total_list($post);

			}

			if($post['option'] == 'balance') {
				$_SESSION['specific_customer_store_acc']['customer_balance_options'] = $post['tai_khoan'];
				$_SESSION['specific_customer_store_acc']['customer_id'] = $post['customer_id'];
				$this->specific_customer_store_acc_store();
			} else
			{
				$result = array('total_list'=> $total_list, 'html_string'=>$html_string, 'pagination'=>$pagination);
				echo json_encode($result);
			} 


		}
	}

	function mail_history_content() {
		$mail_history_info = $this->Customer->get_mail_history($this->input->post('mail_history_id'));
		$this->load->view('reports/partials/mail_history_content',array('item'=>$mail_history_info));
	}



   /*---------------------------------------------------------------------------------------------------#
    *
		*                           REPORT ON EMPLOYEE DETAILS
    *
    *---------------------------------------------------------------------------------------------------
		*/

		function specific_employees($employee_id = 0) {
			$this->check_action_permission('view_employees');
			$data['action'] = $this->uri->segment(2);
			$data['locations'] = $this->Location->list_item();
			$data['title'] = lang('reports');
			$data['no_excel'] = false;
			if(isset($_SESSION['specific_employees'])) {
				$data['url_print'] = $_SESSION['specific_employees']['url_print'];
				$data['filter'] = $_SESSION['specific_employees'];
				if (empty($employee_id)) {
					unset($_SESSION['specific_employees']);
				} else {
					$data['filter']['employee_id'] = $employee_id;
				}
				if($data['filter']['export_excel'] == 1)
					redirect($data['url_print']);
				else
					$this->load->view("reports/specific_employees",$data);

			}else{
				$data['inputs']   = array('input_date_range', 'select_employee','select_multi_group_of_location_no_default','input_locations');
				$this->load->view("reports/n9_tabular",$data);
			}
		}

		function specific_employees_store() {
			$post  = $this->input->post();

			$arrParam = array_merge($post, $this->input->get());

			$this->load->model('reports/Summary_sales');
			$this->load->model('reports/Specific_employees');

			$sale_model = $this->Summary_sales;
			$model      = $this->Specific_employees;
			if(!empty($post)) {
            //create sale temp table
				$time_condition = array();
				if(!empty($post['start_date'])) {
					$time_condition[] =  's.sale_time >= \''.$post['start_date'].'\'';
				}

				if(!empty($post['end_date'])) {
					$time_condition[] =  's.sale_time <= \''.$post['end_date'].'\'';
				}													
				if(!empty($time_condition)) {
					$time_condition = ' AND ' . implode(' AND ', $time_condition);
				}else
				$time_condition = '';

				$location_ids = $post['location_ids'];
				$employee_id  = $post['employee_id'];
				$where = "s.suspended = 0 AND s.store_account_payment = 0 $time_condition AND s.location_id IN ($location_ids) AND s.deleted = 0 AND s.sold_by_employee_id = $employee_id ";

				$this->Sale->create_sales_items_temp_table_n9(array('where'=>$where));
				if (!empty($post['employee_id'])) {
					$employee = $this->Customer->get_info_person_by_id($post['employee_id']);
				}

				$arrParam = array_merge($post, $this->input->get());
				$arrParam['paginator'] = $this->_paginator;
				$arrParam['page']      =  $this->uri->segment(3, 1);

				$config['base_url'] = base_url() . 'reports/specific_employees_store';

				$config['total_rows'] = $sale_model->count_item($arrParam);

				$config['per_page'] = $arrParam['paginator']['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
				$config['uri_segment'] = 3;
				$config['use_page_numbers'] = TRUE;

				$items                                     = $sale_model->list_item($arrParam, array('task'=>'commission', 'set_clause' => 'employee_detailed'));
				$commission_from_specific_employee_by_sale = $model->get_commission_from_specific_employee_by_sale($arrParam);

				$html_string = $this->load->view('reports/row/specific_employees',array('items'=>$items, 'commission_from_specific_employee_by_sale'=>$commission_from_specific_employee_by_sale), true);

				$this->load->library("pagination");
				$this->pagination->initialize($config);
				$this->pagination->createConfig('front-end');

				$pagination = $this->pagination->create_ajax();

				$total_list_tmp      = $sale_model->get_total_purchase($arrParam);
				$employee_commission = $model->get_commission_from_specific_employee($arrParam);

				$total_list = array(
					'specific_employees_order_total' => $total_list_tmp['order_value'],
					'specific_employees_profit_before_charging_commission' => $total_list_tmp['profit_before_charging_commission'],
					'specific_employees_profit'       => $total_list_tmp['profit'],
					'specific_employees_title'        => lang('reports_employee_on_details'). ': '.$employee['last_name']. ' ' . $employee['first_name'],
					'specific_employees_commission'   => to_currency($employee_commission) . '/' .$total_list_tmp['commission'],
					'specific_employees_tax'          => $total_list_tmp['tax']
				);

				$result = array('total_list'=> $total_list, 'html_string'=>$html_string, 'pagination'=>$pagination);
				echo json_encode($result);
			}
		}


		function specific_employees_excel() {
			$arrParams = array_merge($this->input->post(),$this->input->get());

			$this->load->model('reports/Summary_sales');
			$this->load->model('reports/Specific_employees');

			$sale_model = $this->Summary_sales;
			$model = $this->Specific_employees;

        //create sale temp table
			$time_condition = array();
			if(!empty($arrParams['start_date'])) {
				$time_condition[] =  's.sale_time >= \''.$arrParams['start_date'].'\'';
			}

			if(!empty($arrParams['end_date'])) {
				$time_condition[] =  's.sale_time <= \''.$arrParams['end_date'].'\'';
			}

			if(!empty($time_condition)) {
				$time_condition = ' AND ' . implode(' AND ', $time_condition);
			}else
			$time_condition = '';
			$location_ids = $arrParams['location_ids'];
			$where = "s.suspended = 0 AND s.store_account_payment = 0 $time_condition AND s.location_id IN ($location_ids) AND s.deleted = 0";
			$this->Sale->create_sales_items_temp_table_n9(array('where'=>$where));

			$items                                     = $sale_model->list_item($arrParams, array('task'=>'commission', 'set_clause' => 'employee_detailed'));
			$commission_from_specific_employee_by_sale = $model->get_commission_from_specific_employee_by_sale($arrParams);

			$total_list_tmp      = $sale_model->get_total_purchase($arrParams);
			$employee_commission = $model->get_commission_from_specific_employee($arrParams);

			$total_list = array(
				'specific_employees_order_total' => $total_list_tmp['order_value'],
				'specific_employees_profit'      => $total_list_tmp['profit'],
				'specific_employees_commission'  => to_currency($employee_commission),
				'specific_commission'            => $total_list_tmp['commission'],
				'specific_employees_tax'         => $total_list_tmp['tax']
			);

			if(!empty($items)) {
				$sale_prefix = $this->config->item('sale_prefix');
				foreach($items as &$val) {
					$sale_id       = $val['sale_id'];
					$code          = isset($val['code']) ? $val['code'] : $sale_prefix . ' ' . $sale_id;
					$sale_time     = $val['sale_time_format'];
					$order_value   = to_currency($val['order_value'] + $val['tax_value'] + $val['thu_value']);
					$profit        = $val['order_value'] + $val['thu_value'] - $val['chi_value'] - $val['cost_value'] - $val['commission'] - $val['point_payment'] - $val['gift_card_payment'];
					$profit        = to_currency($profit);
					$comment       = $val['comment'];
					$payment_type  = $val['payment_type'];
					$commission    = to_currency($val['commission']);
					$tax           = to_currency($val['tax_value']);


					if(isset($commission_from_specific_employee_by_sale[$sale_id]))
						$specific_commission = $commission_from_specific_employee_by_sale[$sale_id];
					else
						$specific_commission = 0;

					$specific_commission = to_currency($specific_commission);

					$val['code']   			 	  = $code;
					$val['sale_time']   	      = $sale_time;
					$val['order_value']   		  = $order_value;
					$val['profit']   			  = $profit;
					$val['commission']   		  = $commission;
					$val['tax']   			      = $tax;
					$val['specific_commission']   = $specific_commission;
				}
			}

			$this->load->helper('n9excel');
			require_once (APPPATH.'libraries/PHPExcel/PHPExcel.php');
			require_once (APPPATH.'libraries/PHPExcel/PHPExcel/IOFactory.php');
			require_once (APPPATH.'libraries/PHPExcel/PHPEXCHelper.php');

			$company_name    = $this->config->item('company');
			$company_address = nl2br($this->Location->get_info_for_key('address'));

			if(!empty($arrParams['start_date'])){
				$start_date_covert = date('d-m-Y H:i:s', strtotime($arrParams['start_date']));
				$date_arr[]        = $start_date_covert;
			}

			if(!empty($arrParams['end_date'])){
				$end_date_covert = date('d-m-Y H:i:s', strtotime($arrParams['end_date']));
				$date_arr[]      = $end_date_covert;
			}

			if(!empty($date_arr)) {
				$date_covert = 'Từ ' . implode(' đến ', $date_arr);
			}

			$helpExport = new HelpFuncExportExcel ();
			$objReader = PHPExcel_IOFactory::createReader ( "Excel5" );
			$colIndex = '';
			$rowIndex = 0;
			$objPHPExcel = new PHPExcel ();
			$sheet = $objPHPExcel->getActiveSheet ();

			$sheet->mergeCellsByColumnAndRow(0, 1, 6, 1);
			$sheet->setCellValue('A1', $company_name);
			$helpExport->setStyle_13_TNR_B_L($sheet, 'A1', 'D1');
			$sheet->getRowDimension(1)->setRowHeight(24.75);

			$sheet->mergeCellsByColumnAndRow(0, 2, 6, 2);
			$sheet->setCellValue('A2', $company_address);
			$helpExport->setStyle_13_TNR_N_L($sheet, 'A2', 'D2');


			$title = 'Báo cáo chi tiết nhân viên';
			$sheet->mergeCellsByColumnAndRow(0, 4, 6, 4);
			$sheet->setCellValue('A4', $title);
			$helpExport->setStyle_16_TNR_B_C($sheet, 'A4', 'D4');
			$sheet->getRowDimension(4)->setRowHeight(24);

			$current_row = 6;

			if(!empty($date_covert)) {
				$sheet->mergeCellsByColumnAndRow(0, 5, 6, 5);
				$sheet->setCellValue('A5', $date_covert);
				$helpExport->setStyle_11_TNR_I_C($sheet, 'A5', 'D5');

				$current_row = $current_row + 1;
			}

			$employee_info = $this->Employee->get_info($arrParams['employee_id']);

			$sheet->setCellValue('A'.$current_row, 'Tên nhân viên');
			$sheet->setCellValue('B'.$current_row, $employee_info->first_name . ' ' . $employee_info->last_name);
			$helpExport->setStyle_13_TNR_N_L($sheet, 'A'.$current_row, 'B'.$current_row);
			$current_row = $current_row + 2;

			$rowStart = $current_row;
			$freeCol = $current_row + 1;
			$colStart = 'A';
			$rowIndex = $rowStart;
			$colIndex = $colStart;
			$sheet = $objPHPExcel->getActiveSheet ();
        $sheet->getColumnDimension ( 'A' )->setWidth ( 21 ); // TimeS
        $sheet->getColumnDimension ( 'B' )->setWidth ( 24 ); // Sale_id
        $sheet->getColumnDimension ( 'C' )->setWidth ( 24 ); // Tong gia tri
        $sheet->getColumnDimension ( 'D' )->setWidth ( 24 ); // Thue
        $sheet->getColumnDimension ( 'E' )->setWidth ( 24 ); // L?i nhu?n
        $sheet->getColumnDimension ( 'F' )->setWidth ( 24 ); // Hoa h?ng
        // $sheet->getColumnDimension ( 'G' )->setWidth ( 24 ); // T?ng hoa h?ng
        $sheet->getColumnDimension ( 'G' )->setWidth ( 30 ); // Ghi chú

        // $sheet->mergeCellsByColumnAndRow(5, 9, 6, 9);

        $sheet->setCellValue('A9', 'Thời gian');
        $sheet->setCellValue('B9', 'Mã đơn hàng');
        $sheet->setCellValue('C9', 'Tổng giá trị');
        $sheet->setCellValue('D9', 'Thuế');
        $sheet->setCellValue('E9', 'Lợi nhuận');
        $sheet->setCellValue('F9', 'Hoa hồng');
        $sheet->setCellValue('G9', 'Ghi chú');

        $helpExport->setStyle_12_TNR_B_C($sheet, 'A9', 'H9');

        $current_row = $current_row + 1;


        $sheet->freezePane( "A$freeCol" );

        $sheet->getRowDimension(($current_row-1))->setRowHeight(22);

        $i = $current_row;

        if(!empty($items)) {
        	foreach($items as $item) {
        		$sheet->getRowDimension($i)->setRowHeight(22);
        		$sheet->setCellValue('A'.$i, $item['sale_time_format']);
        		$helpExport->setStyle_12_TNR_N_C($sheet, 'A'.$i, 'A'.$i);

        		$sheet->getRowDimension($i)->setRowHeight(22);
        		$sheet->setCellValue('B'.$i, $item['code']);
        		$helpExport->setStyle_12_TNR_N_L($sheet, 'B'.$i, 'B'.$i);

        		$sheet->getRowDimension($i)->setRowHeight(22);
        		$sheet->setCellValue('C'.$i, $item['order_value']);
        		$helpExport->setStyle_12_TNR_N_R($sheet, 'C'.$i, 'C'.$i);

        		$sheet->getRowDimension($i)->setRowHeight(22);
        		$sheet->setCellValue('D'.$i, $item['tax']);
        		$helpExport->setStyle_12_TNR_N_R($sheet, 'D'.$i, 'D'.$i);

        		$sheet->getRowDimension($i)->setRowHeight(22);
        		$sheet->setCellValue('E'.$i, $item['profit']);
        		$helpExport->setStyle_12_TNR_N_R($sheet, 'E'.$i, 'E'.$i);

        		$sheet->getRowDimension($i)->setRowHeight(22);
        		$sheet->setCellValue('F'.$i, $item['specific_commission']);
        		$helpExport->setStyle_12_TNR_N_R($sheet, 'F'.$i, 'F'.$i);

                // $sheet->getRowDimension($i)->setRowHeight(22);
                // $sheet->setCellValue('G'.$i, $item['commission']);
                // $helpExport->setStyle_12_TNR_N_R($sheet, 'G'.$i, 'G'.$i);

        		$sheet->getRowDimension($i)->setRowHeight(22);
        		$sheet->setCellValue('G'.$i, $item['comment']);
        		$helpExport->setStyle_12_TNR_N_L($sheet, 'G'.$i, 'G'.$i);

        		$i++;
        		$current_row = $current_row + 1;
        	}
        }

        $sheet->mergeCellsByColumnAndRow(0, $i, 1, $i);
        $sheet->setCellValue('A'.$i, 'Total');
        $helpExport->setStyle_12_TNR_B_C($sheet, 'A'.$i, 'A'.$i);

        $sheet->setCellValue('C'.$i, $total_list['specific_employees_order_total']);
        $sheet->setCellValue('D'.$i, $total_list['specific_employees_tax']);
        $sheet->setCellValue('E'.$i, $total_list['specific_employees_profit']);
        $sheet->setCellValue('F'.$i, $total_list['specific_employees_commission']);
        // $sheet->setCellValue('G'.$i, $total_list['specific_commission']);
        $helpExport->setStyle_12_TNR_N_R($sheet, 'C'.$i, 'G'.$i);

        $sheet->getRowDimension($i)->setRowHeight(22);

        $sheet->getStyle ( 'A' . $rowStart . ':' . 'G' . ($i) )->getBorders ()->getOutline ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );
        $sheet->getStyle ( 'A' . $rowStart . ':' . 'G' . ($i) )->getBorders ()->getInside ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );

        ////set dinh dang giay a4 cho ban in ra////////////
        $objPHPExcel->getActiveSheet ()->getPageSetup ()->setOrientation ( PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE );
        $objPHPExcel->getActiveSheet ()->getPageSetup ()->setPaperSize ( PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4 );
        $objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 10);

        $pageMargin = $sheet->getPageMargins ();
        $pageMargin->setTop ( .5 );
        $pageMargin->setLeft ( .15 );
        $pageMargin->setRight ( .05 );
        ////////////////////////////////////////////////////
        header ( 'Content-Type: application/vnd.ms-excel' );
        header ( 'Content-Disposition: attachment;filename="bao_cao_chi_tiet_nhan_vien(' . date ( "d/m/Y" ) . ').xls"' );
        header ( 'Cache-Control: max-age=0' );
        $objWriter = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel5' );
        $objWriter->save ( 'php://output' );

    }

    
    


    
    #---------------------------------------------------------------------------------------------------#






							# Báo cáo chi tiết công nợ nhà cung cấp
							# Báo cáo chi tiết công nợ nhà cung cấp
    						# Báo cáo chi tiết công nợ nhà cung cấp
							# Báo cáo chi tiết công nợ nhà cung cấp
							# Báo cáo chi tiết công nợ nhà cung cấp







    #---------------------------------------------------------------------------------------------------#



    function specific_supplier_store_acc() {
    	$data['action'] = $this->uri->segment(2);
    	$data['locations'] = $this->Location->list_item();
    	$data['title'] = 'Báo cáo chi tiết công nợ nhà cung cấp';

    	if(isset($_SESSION['specific_supplier_store_acc'])) {
    		$data['url_print'] = $_SESSION['specific_supplier_store_acc']['url_print'];
    		$data['filter']    = $filter = $_SESSION['specific_supplier_store_acc'];

    		if($data['filter']['export_excel'] == 1)
    			redirect($data['url_print']);
    		else
    			$this->load->view("reports/specific_supplier_store_acc",$data);

    	}else{
    		$data['inputs']      = array('input_date_range','select_suppliers','select_suppliers_liabilities');
    		$data['supplier_id'] = $this->input->get('supp_id', -1);
    		$this->load->view("reports/n9_tabular",$data);
    	}
    }

    function specific_supplier_store_acc_store() {

    	$arrParam = $_SESSION['specific_supplier_store_acc'];

       	// $arrParam['options'] = 1;
    	$arrParam['options'] = $arrParam['supplier_balance_options'];
       // $arrParam['start_date'] = date('Y-m-d',strtotime('2017-04-28'));
       // $arrParam['end_date'] = date('Y-m-d',strtotime('2017-04-28'));
       // var_dump( $arrParam);
    	unset($_SESSION['specific_supplier_store_acc']);

    	if(!empty($arrParam)) {

    		$this->load->model('reports/Specific_supplier_store_account');
    		$model = $this->Specific_supplier_store_account;

    		$arrParam['paginator']             = $this->_paginator;
    		$arrParam['page']                  =  $this->uri->segment(3, 1);

    		$config['base_url'] = base_url() . 'reports/specific_supplier_store_acc_store';
    		$config['total_rows'] = "15";

    		$config['per_page'] = $arrParam['paginator']['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
            //$config['per_page'] = $arrParam['paginator']['per_page'] = 5;
    		$config['uri_segment'] = 3;
    		$config['use_page_numbers'] = TRUE;

    		$items 		= $model->lay_danh_sach_giao_dich_nha_cung_cap($arrParam)->result_array();
// echo '<pre>';
    		$no_dau_ky  = $model->lay_no_dau_ky_theo_tung_nha_cung_cap($arrParam);
    		$tong_tien_giao_dich  = $model->lay_tong_giao_dich_theo_nha_cung_cap($arrParam)->result_array()[0]['TONG_TIEN_GIAO_DICH'];
 // var_dump($no_dau_ky);
            // die;
    		$data = array();

    		if(isset($no_dau_ky[0]['no_dau_ky']))
    			$debt_start = round($no_dau_ky[0]['no_dau_ky'],2) + round($tong_tien_giao_dich,2);
    		else $debt_start = 0;

			# Lấy ra mảng đã sắp xếp
    		$stt = 1;
    		foreach ($items as $key => $value) {

    			if($value['receiving_id'] == NULL){
    				$data[1] = $items[$key];
    				$data[1]['key'] = 0;
    			} else {
    				$stt++;
    				$data[$stt] = $items[$key];
    				$data[$stt]['key'] = $stt;
    			}
    		}
    		if(!empty($data)) sap_xep_mang($data,'key',true);


			# Xử lý tính nợ

    		foreach ($data as $key => $value) {
    			if($key == 0 || $value['receiving_id'] = NULL){
            		# Gán dữ liệu cho nợ đầu bằng nợ đầu kỳ
    				$data[$key]['no_dau']  = $debt_start;
    				$data[$key]['no_cuoi'] = round($debt_start - $data[$key]['ghi_co'] + $data[$key]['ghi_no'],2);

    			} else {

    				if($key == 0) $vitri = 0; else $vitri = $key - 1;
    				if(isset($data[$vitri]['no_cuoi'])) $no_cuoi = round($data[$vitri]['no_cuoi'],2); 
    				else $no_cuoi = round($no_dau_ky[0]['no_dau_ky'],2);

    				$data[$key]['no_dau'] 	= $no_cuoi;
    				$data[$key]['no_cuoi'] = round($data[$key]['no_dau'] - $data[$key]['ghi_co'] + $data[$key]['ghi_no'],2);
    			}


    		}

    		$debt_start = to_currency($debt_start);
    		if(empty($data)) $debt_end = $debt_start; 
    		else $debt_end = to_currency(end($data)['no_cuoi']);

            // var_dump($data);
            // die;
    		$html = $this->load->view('reports/table/supplier_store_accounts', array('items'=>$data), true);
// var_dump( $html);
    		$this->load->library("pagination");
    		$this->pagination->initialize($config);
    		$this->pagination->createConfig('front-end');

    		$pagination = $this->pagination->create_ajax();


    		$result = array('count'=> $config['total_rows'], 'html_string'=>$html, 'pagination'=>$pagination, 'debt_start'=>$debt_start, 'debt_end'=>$debt_end);
    		echo json_encode($result);
    	}
    }

    function specific_supplier_store_acc_excel() {

    	$arrParam = $this->input->get();

    	unset($_SESSION['specific_supplier_store_acc']);
    	$arrParam['options'] = $arrParam['supplier_balance_options'];

    	if($arrParam['supplier_id'] == -1) {
    		echo 'Bạn chưa chọn nhà cung cấp';
    		return;
    	}

    	$this->load->model('reports/Specific_supplier_store_account');
    	$model = $this->Specific_supplier_store_account;
        #-------------------------------------------------------------------------------------------------#

        # Biến hien_thi có cấu trúc C:D:E với C:D là 2 trường cần merge E là trường vị trí hiển thị dữ liệu

        #-------------------------------------------------------------------------------------------------#
    	$thong_tin_diem_ban_hang = $this->Location->get_info($_GET['location_ids'])->name;
    	$ten_cong_ty = $this->config->item('company');

        # đầu trang

    	$_title = array(
    		array('value_field' => 'title','dong_nao'=>4,'hien_thi'=>'A:H:A'),
    		array('value_field' => 'date','dong_nao'=>5,'hien_thi'=>'A:H:B'),
    		array('value_field' => 'ten_khach_hang','dong_nao'=>6,'hien_thi'=>'A:B:A'),
    		array('value_field' => 'dau_ky','dong_nao'=>7,'hien_thi'=>'B:B:B'),
    		array('value_field' => 'cuoi_ky','dong_nao'=>8,'hien_thi'=>'B:B:B'),
    		array('value_field' => 'thong_tin_diem_ban_hang','dong_nao'=>2,'hien_thi'=>'A:D:A'),
    		array('value_field' => 'ten_cong_ty','dong_nao'=>1,'hien_thi'=>'A:D:A'),
    	);
// 		echo $this->config->item('company');
// 		die;
    	$_headers 	 = array(
    		array('col' => 'A','value_field' => '__AUTO__'),
    		array('col' => 'B','value_field' => 'date_sale'),
    		array('col' => 'C','value_field' => 'receiving_id'),
    		array('col' => 'D','value_field' => 'no_dau'),
    		array('col' => 'E','value_field' => 'ghi_no'),
    		array('col' => 'F','value_field' => 'ghi_co'),
    		array('col' => 'G','value_field' => 'no_cuoi'),
    		array('col' => 'H','value_field' => 'comment'),
    	);		


		# dữ liệu truyền ra	
    	$items 		= $model->lay_danh_sach_giao_dich_nha_cung_cap($arrParam)->result_array();
    	$no_dau_ky  = $model->lay_no_dau_ky_theo_tung_nha_cung_cap($arrParam);
    	$tong_tien_giao_dich  = $model->lay_tong_giao_dich_theo_nha_cung_cap($arrParam)->result_array()[0]['TONG_TIEN_GIAO_DICH'];

    	$data = array();


    	$debt_start = round($no_dau_ky[0]['no_dau_ky'],2) + round($tong_tien_giao_dich,2);

		# Lấy ra mảng đã sắp xếp
    	$stt = 1;
    	foreach ($items as $key => $value) {

    		if($value['receiving_id'] == NULL){
    			$data[1] = $items[$key];
    			$data[1]['key'] = 0;
    		} else {
    			$stt++;
    			$data[$stt] = $items[$key];
    			$data[$stt]['key'] = $stt;
    		}
    	}
    	if(!empty($data)) sap_xep_mang($data,'key',true);

		# Xử lý tính nợ

    	foreach ($data as $key => $value) {
    		if($key == 0 || $value['receiving_id'] = NULL){
        		# Gán dữ liệu cho nợ đầu bằng nợ đầu kỳ
    			$data[$key]['no_dau']  = $debt_start;
    			$data[$key]['no_cuoi'] = round($debt_start - $data[$key]['ghi_co'] + $data[$key]['ghi_no'],2);

    		} else {

    			if($key == 0) $vitri = 0; else $vitri = $key - 1;
    			if(isset($data[$vitri]['no_cuoi'])) $no_cuoi = round($data[$vitri]['no_cuoi'],2); 
    			else $no_cuoi = round($no_dau_ky[0]['no_dau_ky'],2);

    			$data[$key]['no_dau'] 	= $no_cuoi;
    			$data[$key]['no_cuoi'] = round($data[$key]['no_dau'] - $data[$key]['ghi_co'] + $data[$key]['ghi_no'],2);
    		}
    	}

    	$dau_ky = $debt_start;



    	if(empty($data)) {
    		$cuoi_ky =  $debt_start;
    		$debt_end = to_currency($debt_start); 
    	}
    	else {
    		$debt_end = to_currency(end($data)['no_cuoi']);
    		$cuoi_ky = end($data)['no_cuoi'];
    	}

    	$debt_start = to_currency($debt_start);

    	if($arrParam['options'] == 1){
    		$title = 'BÁO CÁO CHI TIẾT CÔNG NỢ NHÀ CUNG CẤP - Tài khoản NCC nợ';
    	} else $title = 'BÁO CÁO CHI TIẾT CÔNG NỢ NHÀ CUNG CẤP - Tài khoản nợ NCC';
    	if(empty($arrParam['start_date'])) $date = 'Toàn bộ thời gian'; else $date = 'Từ : '.$arrParam['start_date'].' đến : '.$arrParam['end_date'];


    	$ten_khach_hang = 'Tên nhà cung cấp : '.$no_dau_ky[0]['company_name'];

    	foreach ($data as $value) {
			// echo $value['no_dau_ky'];
    		$result[] = array(
    			'receiving_id' 		=>  $value['receiving_id'],
    			'date_sale' 	=> 	$value['date'],
    			'comment' 		=>  $value['comment'],
    			'no_dau'     	=>  $value['no_dau'],
    			'ghi_co' 		=>  $value['ghi_co'],
    			'ghi_no' 		=>  $value['ghi_no'],
    			'no_cuoi' 		=>  $value['no_cuoi'],
    			'dau_ky' 		=> 	$dau_ky,
    			'cuoi_ky' 		=> 	$cuoi_ky,
    			'ten_khach_hang'=>  $ten_khach_hang,
    			'date'			=>	$date,
    			'thong_tin_diem_ban_hang' => $thong_tin_diem_ban_hang,
    			'ten_cong_ty'   =>  $ten_cong_ty,
    			'title'         => 	$title,
    		);
    	}

    	$bizExcel = new BizExcel('bao_cao_chi_tiet_cong_no_khach_hang.xlsx');
    	$bizExcel->Row_title($_title);
    	$bizExcel->setNumberRowStartBody(10)->setHeaderOfBody($_headers);
    	$bizExcel->tat_auto_size(false);
    	$bizExcel->setDataExcel($result);
		// $this->oPHPExcel->getActiveSheet()->setCellValue('B5','ducanh');

		// $bizExcel->addToNewSheet('Tổng hợp nhóm khách hàng')->generateFile(false, '', false);


    	$excelContent = $bizExcel->generateFile(false);
		// die;
    	$this->load->helper('download');
    	force_download('bao_cao_chi_tiet_cong_no_nha_cung_cap.xlsx', $excelContent);
    	exit;

    }
     #---------------------------------------------------------------------------------------------------#






							# Báo cáo tổng hợp công nợ nhà cung cấp
							# Báo cáo tổng hợp công nợ nhà cung cấp
    						# Báo cáo tổng hợp công nợ nhà cung cấp
							# Báo cáo tổng hợp công nợ nhà cung cấp
							# Báo cáo tổng hợp công nợ nhà cung cấp







    #---------------------------------------------------------------------------------------------------#

    function bao_cao_TONG_HOP_cong_no_nha_cung_cap() {

    	$this->check_action_permission('view_store_account');
    	$this->load->model('reports/Specific_supplier_store_account');
    	$model = $this->Specific_supplier_store_account;

    	$data['action'] = $this->uri->segment(2);
    	$data['locations'] = $this->Location->list_item();
    	$data['title'] = 'Báo cáo tổng hợp công nợ nhà cung cấp';

        //$_SESSION['specific_supplier_store_acc'] = array(1);
    	if(isset($_SESSION['bao_cao_TONG_HOP_cong_no_nha_cung_cap'])) {
    		$data['url_print'] = $_SESSION['bao_cao_TONG_HOP_cong_no_nha_cung_cap']['url_print'];
    		$data['filter']    = $_SESSION['bao_cao_TONG_HOP_cong_no_nha_cung_cap'];


    		if($data['filter']['export_excel'] == 1)
    			redirect($data['url_print']);
    		else
	         	// var_dump($_SESSION['bao_cao_chi_tiet_cong_no_nhom_nha_cung_cap']);
    			$this->load->view("reports/bao_cao_TONG_HOP_cong_no_nha_cung_cap",$data);

    	}else{
    		$data['inputs'] = array('input_date_range');
    		$data['supplier_id'] = $this->input->get('cus_id', -1);
    		$this->load->view("reports/n9_tabular",$data);
    	}
    }

    function bao_cao_TONG_HOP_cong_no_nha_cung_cap_store(){



    	$arrParam = $_SESSION['bao_cao_TONG_HOP_cong_no_nha_cung_cap'];
       // $arrParam['start_date'] = date('Y-m-d',strtotime('2017-05-09'));
       // $arrParam['end_date'] = date('Y-m-d',strtotime('2017-08-28'));
       // $arrParam['nhom_nha_cung_cap'] = 22;

       // var_dump( $arrParam);
    	unset($_SESSION['bao_cao_TONG_HOP_cong_no_nha_cung_cap']);

    	if(!empty($arrParam)) {
    		$this->load->model('reports/Specific_supplier_store_account');
    		$model = $this->Specific_supplier_store_account;

    		$arrParam['paginator']             = $this->_paginator;
    		$arrParam['page']                  =  $this->uri->segment(3, 1);

    		$config['base_url'] = base_url() . 'reports/bao_cao_tong_hop_cong_no_nha_cung_cap_store';



    		$config['per_page'] = $arrParam['paginator']['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;

    		$config['uri_segment'] = 3;
    		$config['use_page_numbers'] = TRUE;

    		$this->load->model('reports/Specific_supplier_store_account');
    		$model = $this->Specific_supplier_store_account;
    		$items = array();
            // echo '<pre>';

    		$tong_hop_no_dau_cong_no_nha_cung_cap = $model->TONG_HOP_no_dau_cong_no_nha_cung_cap($arrParam);
    		$tong_hop_giao_dich_nha_cung_cap = $model->TONG_HOP_giao_dich_nha_cung_cap($arrParam);
    		$tong_khach_no = 0;
    		$tong_no_khach = 0;

    		foreach ($tong_hop_no_dau_cong_no_nha_cung_cap as $key => $value) {
    			if(isset($tong_hop_giao_dich_nha_cung_cap[$key]['GIAO_DICH_KHACH_NO'])) $tong_tien_giao_dich_khach_no = (int)$tong_hop_giao_dich_nha_cung_cap[$key]['GIAO_DICH_KHACH_NO'];
    			else $tong_tien_giao_dich_khach_no = 0;
    			if(isset($tong_hop_giao_dich_nha_cung_cap[$key]['GIAO_DICH_NO_KHACH'])) $tong_tien_giao_dich_no_khach = (int)$tong_hop_giao_dich_nha_cung_cap[$key]['GIAO_DICH_NO_KHACH'];
    			else $tong_tien_giao_dich_no_khach = 0;


    			$tong_hop_no_dau_cong_no_nha_cung_cap[$key]['tai_khoan_khach_no'] = $value['balance'] + $tong_tien_giao_dich_khach_no;
    			$tong_hop_no_dau_cong_no_nha_cung_cap[$key]['tai_khoan_no_khach'] = $value['balance_2'] + $tong_tien_giao_dich_no_khach;

    			$tong_khach_no += $tong_hop_no_dau_cong_no_nha_cung_cap[$key]['tai_khoan_khach_no'];
    			$tong_no_khach += $tong_hop_no_dau_cong_no_nha_cung_cap[$key]['tai_khoan_no_khach'];
    		}
    		foreach ($tong_hop_no_dau_cong_no_nha_cung_cap as $key => $value) {
    			if($value['tai_khoan_no_khach'] == 0 && $value['tai_khoan_khach_no'] == 0)
    				unset($tong_hop_no_dau_cong_no_nha_cung_cap[$key]);
    		}

            // var_dump($tong_hop_no_dau_cong_no_nha_cung_cap);

// die;		# Đếm số kết quả
    		$config['total_rows'] = "'".count($tong_hop_no_dau_cong_no_nha_cung_cap)."'";
             # Chuyển đổi nợ đầu vào nợ cuối thành chuỗi

    		$tong_khach_no = to_currency($tong_khach_no,2);
    		$tong_no_khach = to_currency($tong_no_khach,2);


    		$html = $this->load->view('reports/table/bao_cao_tong_hop_cong_no_nha_cung_cap', array('items'=>$tong_hop_no_dau_cong_no_nha_cung_cap), true);

            // var_dump($html);
            // die;
    		$this->load->library("pagination");
    		$this->pagination->initialize($config);
    		$this->pagination->createConfig('front-end');

    		$pagination = $this->pagination->create_ajax();

    		$result = array('count'=> $config['total_rows'], 'html_string'=>$html, 'pagination'=>$pagination,'debt_start'=>$tong_khach_no, 'debt_end'=>$tong_no_khach);
            // var_dump($result);
    		echo json_encode($result);
    	}
    }

    function bao_cao_TONG_HOP_cong_no_nha_cung_cap_excel() {

    	$arrParam = $this->input->get();

    	unset($_SESSION['bao_cao_TONG_HOP_cong_no_nha_cung_cap']);

    	$this->load->model('reports/Specific_supplier_store_account');
        #-------------------------------------------------------------------------------------------------#

        # Biến hien_thi có cấu trúc C:D:E với C:D là 2 trường cần merge E là trường vị trí hiển thị dữ liệu

        #-------------------------------------------------------------------------------------------------#
    	$thong_tin_diem_ban_hang = 'Điểm bán hàng : '.$this->Location->get_info($_GET['location_ids'])->name;
    	$ten_cong_ty = $this->config->item('company');
   		// var_dump($thong_tin->name);
        # đầu trang

    	$_title = array(
    		array('value_field' => 'title','dong_nao'=>4,'hien_thi'=>'A:F:A'),
    		array('value_field' => 'date','dong_nao'=>5,'hien_thi'=>'A:E:A'),
    		array('value_field' => 'thong_tin_diem_ban_hang','dong_nao'=>6,'hien_thi'=>'A:E:A'),
    		array('value_field' => 'ten_cong_ty','dong_nao'=>1,'hien_thi'=>'A:D:A'),
    	);
// 		echo $this->config->item('company');
// 		die;
    	$_headers 	 = array(
    		array('col' => 'A','value_field' => '__AUTO__'),
    		array('col' => 'B','value_field' => 'code'),
    		array('col' => 'C','value_field' => 'company_name'),
    		array('col' => 'D','value_field' => 'tai_khoan_khach_no'),
    		array('col' => 'E','value_field' => 'tai_khoan_no_khach'),
    	);		

    	$_footer = array(
			# phần tổng cuối cùng
    		array('sum' => 'D','value_field' => 'SUM','ten_truong'=>'Tổng tiền NCC nợ: ','hien_thi'=>'D:D:E'),
    		array('sum' => 'E','value_field' => 'SUM','ten_truong'=>'Tổng tiền nợ NCC: ','hien_thi'=>'D:D:E'),
    	);

		# dữ liệu truyền ra	
    	$model = $this->Specific_supplier_store_account;
    	$tong_hop_no_dau_cong_no_nha_cung_cap = $model->TONG_HOP_no_dau_cong_no_nha_cung_cap($arrParam);
    	$tong_hop_giao_dich_nha_cung_cap = $model->TONG_HOP_giao_dich_nha_cung_cap($arrParam);


    	foreach ($tong_hop_no_dau_cong_no_nha_cung_cap as $key => $value) {
    		if(isset($tong_hop_giao_dich_nha_cung_cap[$key]['GIAO_DICH_KHACH_NO'])) $tong_tien_giao_dich_khach_no = $tong_hop_giao_dich_nha_cung_cap[$key]['GIAO_DICH_KHACH_NO'];
    		else $tong_tien_giao_dich_khach_no = 0;
    		if(isset($tong_hop_giao_dich_nha_cung_cap[$key]['GIAO_DICH_NO_KHACH'])) $tong_tien_giao_dich_no_khach = $tong_hop_giao_dich_nha_cung_cap[$key]['GIAO_DICH_NO_KHACH'];
    		else $tong_tien_giao_dich_no_khach = 0;


    		$tong_hop_no_dau_cong_no_nha_cung_cap[$key]['tai_khoan_khach_no'] = $value['balance'] + $tong_tien_giao_dich_khach_no;

    		$tong_hop_no_dau_cong_no_nha_cung_cap[$key]['tai_khoan_no_khach'] = $value['balance_2'] + $tong_tien_giao_dich_no_khach;

    	}

    	foreach ($tong_hop_no_dau_cong_no_nha_cung_cap as $key => $value) {
    		if($value['tai_khoan_no_khach'] == 0 && $value['tai_khoan_khach_no'] == 0)
    			unset($tong_hop_no_dau_cong_no_nha_cung_cap[$key]);
    	}



    	$title = 'BÁO CÁO TỔNG HỢP CÔNG NỢ nhà cung cấp';
    	if(empty($arrParams['start_date'])) $date = 'Toàn bộ thời gian'; else $date = 'Từ : '.$arrParams['start_date'].' đến : '.$arrParams['end_date'];
			// var_dump($items);
    	foreach ($tong_hop_no_dau_cong_no_nha_cung_cap as $value) {
			// echo $value['no_dau_ky'];
    		$result[] = array(
    			'code' 						=>  $value['code'],
    			'last_name' 				=> 	$value['company_name'],
    			'tai_khoan_khach_no'     	=>  $value['tai_khoan_khach_no'],
    			'tai_khoan_no_khach' 		=>  $value['tai_khoan_no_khach'],
    			'date'						=>	$date,
    			'thong_tin_diem_ban_hang' 	=>  $thong_tin_diem_ban_hang,
    			'ten_cong_ty'   			=>  $ten_cong_ty,
    			'title'         			=> 	$title,
    		);
    	}
    	$bizExcel = new BizExcel('bao_cao_tong_hop_cong_no_nha_cung_cap.xlsx');
    	$bizExcel->Row_title($_title);
    	$bizExcel->setNumberRowStartBody(8)->setHeaderOfBody($_headers);
    	$bizExcel->RowEndBody($_footer);
    	$bizExcel->tat_auto_size(false);
    	$bizExcel->setDataExcel($result);

    	$excelContent = $bizExcel->generateFile(false);
		// die;
    	$this->load->helper('download');
    	force_download('bao_cao_tong_hop_cong_no_nha_cung_cap.xlsx', $excelContent);
    	exit;
    }




	 #---------------------------------------------------------------------------------------------------#

		# Báo cáo Chi tiết công nợ từng khách hàng

    #---------------------------------------------------------------------------------------------------#


    function specific_customer_store_acc() {
    	$this->check_action_permission('view_store_account');

    	$data['action'] = $this->uri->segment(2);
    	$data['locations'] = $this->Location->list_item();
    	$data['title'] = 'Báo cáo chi tiết công nợ';

    	if(isset($_SESSION['specific_customer_store_acc'])) {
    		$data['url_print'] = $_SESSION['specific_customer_store_acc']['url_print'];
    		$data['filter']    = $filter = $_SESSION['specific_customer_store_acc'];


    		if($data['filter']['export_excel'] == 1)
    			redirect($data['url_print']);
    		else
    			$this->load->view("reports/specific_customer_store_acc",$data);

    	} else{
    		$data['inputs']      = array('input_date_range','select_customers','select_customers_liabilities');
    		$data['customer_id'] = $this->input->get('cus_id', -1);
    		$data['no_excel']    = false;
    		$this->load->view("reports/n9_tabular",$data);
    	}
    }

    function specific_customer_store_acc_store() {

    	$arrParam = $_SESSION['specific_customer_store_acc'];
    	$arrParam['options'] = $arrParam['customer_balance_options'];
    	unset($_SESSION['specific_customer_store_acc']);

    	if(!empty($arrParam)) {
    		$this->load->model('reports/Specific_customer_store_account');
    		$model = $this->Specific_customer_store_account;

    		$arrParam['paginator']             = $this->_paginator;
    		$arrParam['page']                  =  $this->uri->segment(3, 1);

    		$config['base_url'] = base_url() . 'reports/specific_customer_store_acc_store';            $config['per_page'] = $arrParam['paginator']['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
    		$config['uri_segment'] = 3;
    		$config['use_page_numbers'] = TRUE;

    		$items 		          = $model->lay_danh_sach_giao_dich_khach_hang($arrParam)->result_array();
    		$no_dau_ky            = $model->lay_no_dau_ky_theo_tung_khach_hang($arrParam);
    		$tong_tien_giao_dich  = $model->lay_tong_giao_dich_theo_tung_khach($arrParam)->result_array()[0]['TONG_TIEN_GIAO_DICH'];

    		$data = array();

    		if(isset($no_dau_ky[0]['no_dau_ky'])) {
    			$debt_start = round($no_dau_ky[0]['no_dau_ky'],2) + round($tong_tien_giao_dich,2);
    		} else {
    			$debt_start = 0;
    		}


    		$config['total_rows'] = count($items);



			# Lấy ra mảng đã sắp xếp
    		$stt = 1;
    		foreach ($items as $key => $value) {

    			if($value['sale_id'] == NULL){
    				$data[1] = $items[$key];
    				$data[1]['key'] = 0;
    			} else {
    				$stt++;
    				$data[$stt] = $items[$key];
    				$data[$stt]['key'] = $stt;
    			}
    		}
    		if(!empty($data)) sap_xep_mang($data,'key',true);


			# Xử lý tính nợ

    		foreach ($data as $key => $value) {
    			if($key == 0 || $value['sale_id'] == NULL){
            		# Gán dữ liệu cho nợ đầu bằng nợ đầu kỳ
    				$data[$key]['no_dau']  = $debt_start;
    				$data[$key]['no_cuoi'] = round($debt_start - $data[$key]['ghi_co'] + $data[$key]['ghi_no'],2);

    			} else {

    				if($key == 0) $vitri = 0; else $vitri = $key - 1;
    				if(isset($data[$vitri]['no_cuoi'])) $no_cuoi = round($data[$vitri]['no_cuoi'],2); 
    				else $no_cuoi = round($no_dau_ky[0]['no_dau_ky'],2);

    				$data[$key]['no_dau'] 	= $no_cuoi;
    				$data[$key]['no_cuoi'] = round($data[$key]['no_dau'] - $data[$key]['ghi_co'] + $data[$key]['ghi_no'],2);
    			}


    		}

    		if($arrParam['customer_balance_options'] == 1)
    			$balance_name = $this->config->item('customer_balance');
    		elseif($balance_name['customer_balance_options'] == 2)
    			$balance_name = $this->config->item('customer_balance_2');

    		$name = $this->Customer->get_info_person_by_id($arrParam['customer_id']);

    		$debt_start = to_currency($debt_start);
    		if(empty($data)) $debt_end = $debt_start; 
    		else $debt_end = to_currency(end($data)['no_cuoi']);

    		$total_list = array(
    			'specific_cus_balance_start' => $debt_start,
    			'specific_cus_balance_end' => $debt_end,
    			'specific_cus_title' => 'Báo cáo chi tiết công nợ: '.$name['last_name']. ' - ' .$balance_name .' ' 
    		);

    		$html = $this->load->view('reports/table/bao_cao_chi_tiet_cong_no_theo_tung_khach', array('items'=>$data), true);

    		$this->load->library("pagination");
    		$this->pagination->initialize($config);
    		$this->pagination->createConfig('front-end');

    		$pagination = $this->pagination->create_ajax();


    		$result = array('total_list'=>$total_list,'count'=> $config['total_rows'], 'html_string'=>$html, 'pagination'=>$pagination, 'debt_start'=>$debt_start, 'debt_end'=>$debt_end);
    		echo json_encode($result);
    	}
    }

    function specific_customer_store_acc_excel() {
    	$arrParam = $this->input->get();

    	unset($_SESSION['specific_customer_store_acc']);
    	$arrParam['options'] = $arrParam['customer_balance_options'];

    	if($arrParam['customer_id'] == -1) {
    		echo 'Bạn chưa chọn khách hàng';
    		return;
    	}

    	$this->load->model('reports/Specific_customer_store_account');
    	$model = $this->Specific_customer_store_account;
        #-------------------------------------------------------------------------------------------------#

        # Biến hien_thi có cấu trúc C:D:E với C:D là 2 trường cần merge E là trường vị trí hiển thị dữ liệu

        #-------------------------------------------------------------------------------------------------#
    	$thong_tin_diem_ban_hang = $this->Location->get_info($_GET['location_ids'])->name;
    	$ten_cong_ty = $this->config->item('company');

        # đầu trang

    	$_title = array(
    		array('value_field' => 'title', 'dong_nao' => 4, 'hien_thi' => 'A:H:A'),
    		array('value_field' => 'date_range', 'dong_nao' => 5, 'hien_thi' => 'A:D:A'),
    		array('value_field' => 'ten_khach_hang', 'dong_nao' => 6, 'hien_thi' => 'A:B:A'),
    		array('value_field' => 'dau_ky', 'dong_nao' => 7, 'hien_thi' => 'B:B:B'),
    		array('value_field' => 'cuoi_ky', 'dong_nao' => 8, 'hien_thi' => 'B:B:B'),
    		array('value_field' => 'thong_tin_diem_ban_hang', 'dong_nao' => 2, 'hien_thi'=>'A:D:A'),
    		array('value_field' => 'ten_cong_ty', 'dong_nao' => 1, 'hien_thi' => 'A:D:A'),
    	);

    	$_headers 	 = array(
    		array('col' => 'A', 'value_field' => '__AUTO__'),
    		array('col' => 'B', 'value_field' => 'date_sale'),
    		array('col' => 'C', 'value_field' => 'sale_id'),
    		array('col' => 'D', 'value_field' => 'no_dau'),
    		array('col' => 'E', 'value_field' => 'ghi_no'),
    		array('col' => 'F', 'value_field' => 'ghi_co'),
    		array('col' => 'G', 'value_field' => 'no_cuoi'),
    		array('col' => 'H', 'value_field' => 'comment'),
    	);		


		# dữ liệu truyền ra	
    	$items 		 = $model->lay_danh_sach_giao_dich_khach_hang($arrParam)->result_array();
    	$no_dau_ky   = $model->lay_no_dau_ky_theo_tung_khach_hang($arrParam);
    	$customer_id = $items[0]['customer_id'];
    	$customer    = $this->Customer->get_info_person_by_id($customer_id);
    	$tong_tien_giao_dich  = $model->lay_tong_giao_dich_theo_tung_khach($arrParam)->result_array()[0]['TONG_TIEN_GIAO_DICH'];
    	$data = array();


    	if(isset($no_dau_ky[0]['no_dau_ky']))
    		$debt_start = round($no_dau_ky[0]['no_dau_ky'],2) + round($tong_tien_giao_dich,2);
    	else $debt_start = 0;

		# Lấy ra mảng đã sắp xếp
    	$stt = 1;
    	foreach ($items as $key => $value) {

    		if($value['sale_id'] == NULL){
    			$data[1] = $items[$key];
    			$data[1]['key'] = 0;
    		} else {
    			$stt++;
    			$data[$stt] = $items[$key];
    			$data[$stt]['key'] = $stt;
    		}
    	}
    	if(!empty($data)) sap_xep_mang($data,'key',true);

		# Xử lý tính nợ

    	foreach ($data as $key => $value) {
    		if($key == 0 || $value['sale_id'] = NULL){
        		# Gán dữ liệu cho nợ đầu bằng nợ đầu kỳ
    			$data[$key]['no_dau']  = $debt_start;
    			$data[$key]['no_cuoi'] = round($debt_start - $data[$key]['ghi_co'] + $data[$key]['ghi_no'],2);

    		} else {

    			if($key == 0) $vitri = 0; else $vitri = $key - 1;
    			if(isset($data[$vitri]['no_cuoi'])) $no_cuoi = round($data[$vitri]['no_cuoi'],2); 
    			else $no_cuoi = round($no_dau_ky[0]['no_dau_ky'],2);

    			$data[$key]['no_dau'] 	= $no_cuoi;
    			$data[$key]['no_cuoi'] = round($data[$key]['no_dau'] - $data[$key]['ghi_co'] + $data[$key]['ghi_no'],2);
    		}
    	}

    	$dau_ky = $debt_start;



    	if(empty($data)) {
    		$cuoi_ky =  $debt_start;
    		$debt_end = to_currency($debt_start); 
    	}
    	else {
    		$debt_end = to_currency(end($data)['no_cuoi']);
    		$cuoi_ky = end($data)['no_cuoi'];
    	}

    	$debt_start = to_currency($debt_start);
    	if($arrParam['options'] == 1){
    		$title = 'BÁO CÁO CHI TIẾT CÔNG NỢ KHÁCH HÀNG - Tài khoản khách nợ';
    	} else $title = 'BÁO CÁO CHI TIẾT CÔNG NỢ KHÁCH HÀNG - Tài khoản nợ khách';
    	if(empty($arrParam['start_date'])) {
    		$date = 'Toàn bộ thời gian'; 
    	}
    	else {
    		$date = 'Từ : '.$arrParam['start_date'].' đến : '.$arrParam['end_date'];
    	}
    	$ten_khach_hang = 'Tên khách hàng : '.$customer['first_name'].' '.$customer['last_name'];
    	foreach ($data as $value) {
    		$result[] = array(
    			'sale_id' 		=>  $value['sale_id'],
    			'date_sale' 	=> 	$value['date'],
    			'comment' 		=>  $value['comment'],
    			'no_dau'     	=>  $value['no_dau'],
    			'ghi_co' 		=>  $value['ghi_co'],
    			'ghi_no' 		=>  $value['ghi_no'],
    			'no_cuoi' 		=>  $value['no_cuoi'],
    			'dau_ky' 		=> 	$dau_ky,
    			'cuoi_ky' 		=> 	$cuoi_ky,
    			'ten_khach_hang'=>  $ten_khach_hang,
    			'date_range'	=>	$date,
    			'thong_tin_diem_ban_hang' => $thong_tin_diem_ban_hang,
    			'ten_cong_ty'   =>  $ten_cong_ty,
    			'title'         => 	$title,
    		);
    	}
    	$bizExcel = new BizExcel('bao_cao_chi_tiet_cong_no_khach_hang.xlsx');
    	$bizExcel->Row_title($_title);
    	$bizExcel->setNumberRowStartBody(10)->setHeaderOfBody($_headers);
    	$bizExcel->tat_auto_size(false);
    	$bizExcel->setDataExcel($result);


    	$excelContent = $bizExcel->generateFile(false);
    	$this->load->helper('download');
    	force_download('bao_cao_chi_tiet_cong_no_khach_hang.xlsx', $excelContent);
    	exit;
    }




    #---------------------------------------------------------------------------------------------------#
							# Báo cáo Tổng hợp công nợ nhóm khách hàng
    #---------------------------------------------------------------------------------------------------#




    function bao_cao_tong_hop_cong_no_nhom_khach_hang() {

    	$this->check_action_permission('view_store_account');
    	$this->load->model('reports/Specific_customer_store_account');
    	$model = $this->Specific_customer_store_account;

    	$data['action'] = $this->uri->segment(2);
    	$data['locations'] = $this->Location->list_item();
    	$data['title'] = 'Báo cáo tổng hợp công nợ nhóm khách hàng';
    	$data['no_excel'] = false;

        //$_SESSION['specific_customer_store_acc'] = array(1);
    	if(isset($_SESSION['bao_cao_tong_hop_cong_no_nhom_khach_hang'])) {
    		$data['url_print'] = $_SESSION['bao_cao_tong_hop_cong_no_nhom_khach_hang']['url_print'];
    		$data['filter']    = $_SESSION['bao_cao_tong_hop_cong_no_nhom_khach_hang'];
        	// unset($_SESSION['bao_cao_tong_hop_cong_no_nhom_khach_hang']);


    		if($data['filter']['export_excel'] == 1)
    			redirect($data['url_print']);
    		else
    			$this->load->view("reports/bao_cao_tong_hop_cong_no_nhom_khach_hang",$data);

    	}else{
    		$data['inputs']      = array('input_date_range');
    		$data['customer_id'] = $this->input->get('cus_id', -1);
    		$this->load->view("reports/n9_tabular",$data);
    	}
    }

    function bao_cao_tong_hop_cong_no_nhom_khach_hang_store(){
    	$post        =  $this->input->post();


    	$arrParam = $_SESSION['bao_cao_tong_hop_cong_no_nhom_khach_hang'];
        # Hủy bỏ SESSION
    	unset($_SESSION['bao_cao_tong_hop_cong_no_nhom_khach_hang']);

    	if(!empty($arrParam)) {
    		$this->load->model('reports/Specific_customer_store_account');
    		$model = $this->Specific_customer_store_account;

    		$arrParam['paginator']             = $this->_paginator;
    		$arrParam['page']                  =  $this->uri->segment(3, 1);

    		$config['base_url'] = base_url() . 'reports/bao_cao_tong_hop_cong_no_nhom_khach_hang_store';

            # Đếm số kết quả
    		$config['total_rows'] = $this->Customer->dem_danh_muc_khach_hang('customers_type');

    		$config['per_page'] = $arrParam['paginator']['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;

    		$config['uri_segment'] = 3;
    		$config['use_page_numbers'] = TRUE;

    		$data = array();
    		$tong_khach_no = 0;
    		$tong_no_khach = 0;

    		$nhom_khach_hang = $this->Customer->get_all_danh_muc_khach_hang('customers_type');

    		foreach ($nhom_khach_hang as $key => $value) {


            	# Lặp mảng nhóm khách hàng
    			$arrParam['nhom_khach_hang'] = $value['id'];
    			$tong_no_dau_ky_theo_nhom_khach_hang = $model->lay_tong_no_dau_theo_nhom_khach_hang($arrParam);
    			$tong_giao_dich_theo_nhom_khach_hang = $model->lay_tong_giao_dich_theo_nhom_khach_hang($arrParam);
            	// echo '<pre>';
            	// var_dump($tong_giao_dich_theo_nhom_khach_hang);
    			if($tong_giao_dich_theo_nhom_khach_hang[0]['TONG_GIAO_DICH_KHACH_NO'] != NULL) 
    				$tong_tien_giao_dich_khach_no = $tong_giao_dich_theo_nhom_khach_hang[0]['TONG_GIAO_DICH_KHACH_NO'];
    			else $tong_tien_giao_dich_khach_no = 0;


    			if($tong_giao_dich_theo_nhom_khach_hang[0]['TONG_GIAO_DICH_NO_KHACH'] != NULL) 
    				$tong_tien_giao_dich_no_khach = $tong_giao_dich_theo_nhom_khach_hang[0]['TONG_GIAO_DICH_NO_KHACH'];
    			else $tong_tien_giao_dich_no_khach = 0;



    			$data[$key]['tong_khach_no'] = $tong_no_dau_ky_theo_nhom_khach_hang[0]['TONG_NO_DAU_KHACH_NO'] + $tong_tien_giao_dich_khach_no;
    			$data[$key]['tong_no_khach'] = $tong_no_dau_ky_theo_nhom_khach_hang[0]['TONG_NO_DAU_NO_KHACH'] + $tong_tien_giao_dich_no_khach;

    			$data[$key]['nhom_khach_hang'] = $value['name'];

    			$tong_khach_no += $data[$key]['tong_khach_no'] ;
    			$tong_no_khach += $data[$key]['tong_no_khach'];


    		}

             # Chuyển đổi nợ đầu vào nợ cuối thành chuỗi

    		$tong_khach_no = number_format($tong_khach_no,1);
    		$tong_no_khach = number_format($tong_no_khach,1);

    		$html = $this->load->view('reports/table/tong_hop_theo_nhom_khach_hang', array('items'=>$data), true);

    		$this->load->library("pagination");
    		$this->pagination->initialize($config);
    		$this->pagination->createConfig('front-end');

    		$pagination = $this->pagination->create_ajax();

    		$result = array('count'=> $config['total_rows'], 'html_string'=>$html, 'pagination'=>$pagination,'debt_start'=>$tong_khach_no, 'debt_end'=>$tong_no_khach);
            // var_dump($result);
    		echo json_encode($result);
    	}
    }

    function bao_cao_tong_hop_cong_no_nhom_khach_hang_excel() {

    	$arrParam = $this->input->get();
    	unset($_SESSION['bao_cao_tong_hop_cong_no_nhom_khach_hang']);


    	$this->load->model('reports/Specific_customer_store_account');
        #-------------------------------------------------------------------------------------------------#

        # Biến hien_thi có cấu trúc C:D:E với C:D là 2 trường cần merge E là trường vị trí hiển thị dữ liệu

        #-------------------------------------------------------------------------------------------------#
    	$thong_tin_diem_ban_hang = $this->Location->get_info($_GET['location_ids'])->name;
    	$ten_cong_ty = $this->config->item('company');

        # đầu trang
    	$_title = array(
    		array('value_field' => 'date','dong_nao'=>5,'hien_thi'=>'A:D:A'),
    		array('value_field' => 'thong_tin_diem_ban_hang','dong_nao'=>2,'hien_thi'=>'A:D:A'),
    		array('value_field' => 'ten_cong_ty','dong_nao'=>1,'hien_thi'=>'A:D:A'),
    	);

    	$_headers 	 = array(
    		array('col' => 'A','value_field' => '__AUTO__'),
    		array('col' => 'B','value_field' => 'nhom_khach_hang'),
    		array('col' => 'C','value_field' => 'tong_khach_no'),
    		array('col' => 'D','value_field' => 'tong_no_khach'),
    	);		

    	$_footer = array(
			# phần tổng cuối cùng
    		array('sum' => 'C','value_field' => 'SUM','ten_truong'=>'Tổng tiền khách nợ: ','hien_thi'=>'C:C:D'),
    		array('sum' => 'D','value_field' => 'SUM','ten_truong'=>'Tổng tiền nợ khách: ','hien_thi'=>'C:C:D'),
    	);

    	if(empty($arrParam['start_date'])) $date = 'Toàn bộ thời gian'; else $date = 'Từ : '.$arrParam['start_date'].' đến : '.$arrParam['end_date'];
		# dữ liệu truyền ra	
    	$model = $this->Specific_customer_store_account;
    	$nhom_khach_hang = $this->Customer->get_all_danh_muc_khach_hang('customers_type');
    	$nhom_khach_hang = $this->Customer->get_all_danh_muc_khach_hang('customers_type');

    	foreach ($nhom_khach_hang as $key => $value) {
        	# Lặp mảng nhóm khách hàng
    		$arrParam['nhom_khach_hang'] = $value['id'];
    		$tong_no_dau_ky_theo_nhom_khach_hang = $model->lay_tong_no_dau_theo_nhom_khach_hang($arrParam);
    		$tong_giao_dich_theo_nhom_khach_hang = $model->lay_tong_giao_dich_theo_nhom_khach_hang($arrParam);

    		if($tong_giao_dich_theo_nhom_khach_hang[0]['TONG_GIAO_DICH_KHACH_NO'] != NULL) 
    			$tong_tien_giao_dich_khach_no = $tong_giao_dich_theo_nhom_khach_hang[0]['TONG_GIAO_DICH_KHACH_NO'];
    		else $tong_tien_giao_dich_khach_no = 0;


    		if($tong_giao_dich_theo_nhom_khach_hang[0]['TONG_GIAO_DICH_NO_KHACH'] != NULL) 
    			$tong_tien_giao_dich_no_khach = $tong_giao_dich_theo_nhom_khach_hang[0]['TONG_GIAO_DICH_NO_KHACH'];
    		else $tong_tien_giao_dich_no_khach = 0;


    		$data[$key]['tong_khach_no'] = $tong_no_dau_ky_theo_nhom_khach_hang[0]['TONG_NO_DAU_KHACH_NO'] + $tong_tien_giao_dich_khach_no;
    		$data[$key]['tong_no_khach'] = $tong_no_dau_ky_theo_nhom_khach_hang[0]['TONG_NO_DAU_NO_KHACH'] + $tong_tien_giao_dich_no_khach;

    		$data[$key]['nhom_khach_hang'] = $value['name'];
    		$data[$key]['date'] = $date;
    		$data[$key]['thong_tin_diem_ban_hang'] = $thong_tin_diem_ban_hang;
    		$data[$key]['ten_cong_ty']   =  $ten_cong_ty;

    	}

    	$bizExcel = new BizExcel('bao_cao_tong_hop_nhom_khach_hang.xlsx');
    	$bizExcel->Row_title($_title);
    	$bizExcel->setNumberRowStartBody(7)->setHeaderOfBody($_headers);
    	$bizExcel->RowEndBody($_footer);
    	$bizExcel->tat_auto_size(false);
    	$bizExcel->setDataExcel($data);

    	$excelContent = $bizExcel->generateFile(false);
		// die;
    	$this->load->helper('download');
    	force_download('bao_cao_tong_hop_nhom_khach_hang.xlsx', $excelContent);
    	exit;
    }



    #---------------------------------------------------------------------------------------------------#





    					# Báo cáo chi tiết công nợ nhóm khách hàng
    					# Báo cáo chi tiết công nợ nhóm khách hàng
    					# Báo cáo chi tiết công nợ nhóm khách hàng
    					# Báo cáo chi tiết công nợ nhóm khách hàng
    					# Báo cáo chi tiết công nợ nhóm khách hàng





    #---------------------------------------------------------------------------------------------------#


    function bao_cao_chi_tiet_cong_no_nhom_khach_hang() {

    	$this->check_action_permission('view_store_account');
    	$this->load->model('reports/Specific_customer_store_account');
    	$model = $this->Specific_customer_store_account;

    	$data['action']    = $this->uri->segment(2);
    	$data['locations'] = $this->Location->list_item();
    	$data['title']     = 'Báo cáo chi tiết công nợ nhóm khách hàng';
    	$data['no_excel']  = false;

    	if (isset($_SESSION['bao_cao_chi_tiet_cong_no_nhom_khach_hang'])) {
    		$data['url_print'] = $_SESSION['bao_cao_chi_tiet_cong_no_nhom_khach_hang']['url_print'];
    		$data['filter']    = $_SESSION['bao_cao_chi_tiet_cong_no_nhom_khach_hang'];
    		$data['filter']['options'] = $data['filter']['customer_balance_options'];

    		if ($data['filter']['export_excel'] == 1)
    			redirect($data['url_print']);
    		else

    			$this->load->view("reports/bao_cao_chi_tiet_cong_no_nhom_khach_hang",$data);

    	} else {
    		$data['inputs'] = array('input_date_range','chon_nhom_khach_hang','select_customers_liabilities');
    		$data['customer_id'] = $this->input->get('cus_id', -1);
    		$this->load->view("reports/n9_tabular",$data);
    	}
    }

    function bao_cao_chi_tiet_cong_no_nhom_khach_hang_store(){

    	$arrParam = $_SESSION['bao_cao_chi_tiet_cong_no_nhom_khach_hang'];
    	$arrParam['options'] = $arrParam['customer_balance_options'];
    	unset($_SESSION['bao_cao_chi_tiet_cong_no_nhom_khach_hang']);

    	if(!empty($arrParam)) {
    		$this->load->model('reports/Specific_customer_store_account');
    		$model = $this->Specific_customer_store_account;

    		$arrParam['paginator']             = $this->_paginator;
    		$arrParam['page']                  =  $this->uri->segment(3, 1);

    		$config['base_url'] = base_url() . 'reports/bao_cao_chi_tiet_cong_no_nhom_khach_hang_store';

            # Đếm số kết quả
    		$config['total_rows'] = (string)$model->list_item_theo_nhom_khach_hang($arrParam)->num_rows();

    		$config['per_page'] = $arrParam['paginator']['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;

    		$config['uri_segment'] = 3;
    		$config['use_page_numbers'] = TRUE;

    		$items = $model->list_item_theo_nhom_khach_hang($arrParam)->result_array();
    		$lay_tong_giao_dich_dau_ky  = $model->lay_tong_giao_dich_dau_ky_nhom_khach_hang($arrParam)->result_array();
    		$no_dau_ky_theo_nhom_khach_hang = $model->lay_no_dau_ky_theo_nhom_khach_hang($arrParam);

    		foreach ($no_dau_ky_theo_nhom_khach_hang as $key => $value) {
    			if(isset($lay_tong_giao_dich_dau_ky[$key]['ghi_co'])) $ghi_co = $lay_tong_giao_dich_dau_ky[$key]['ghi_co']; 
    			else $ghi_co = 0;
    			if(isset($lay_tong_giao_dich_dau_ky[$key]['ghi_no'])) $ghi_no = $lay_tong_giao_dich_dau_ky[$key]['ghi_no']; 
    			else $ghi_no = 0;

    			$no_dau_ky_theo_nhom_khach_hang[$key]['no_dau_ky'] = $no_dau_ky_theo_nhom_khach_hang[$key]['no_dau_ky'] - $ghi_co + $ghi_no;
    		}

           	// var_dump($no_dau_ky_theo_nhom_khach_hang);

    		$tong_no_dau_ky = 0;
    		$tong_no_cuoi_ky = 0;

	        # LỌC RA MẢNG DÀI NHẤT
    		if(isset($no_dau_ky_theo_nhom_khach_hang)) {
    			if(count($items) < count($no_dau_ky_theo_nhom_khach_hang)) {
    				$mang_thuc_hien = $no_dau_ky_theo_nhom_khach_hang;
    			} else $mang_thuc_hien = $items;
    		}
	        // echo '<pre>';
	        // var_dump($mang_thuc_hien);
    		$data = array();

    		foreach ($mang_thuc_hien as $key => $value) {
            	# kiểm tra nếu không có khách hàng đó tức là không có giao dịch
    			if(!isset($items[$key]['person_id'])){
            		# Lưu ghi có và ghi nợ = 0
    				$mang_thuc_hien[$key]['ghi_co'] = 0;
    				$mang_thuc_hien[$key]['ghi_no'] = 0;
    				if(isset($value['no_dau_ky'])) {
    					$mang_thuc_hien[$key]['no_dau_ky'] 	= $value['no_dau_ky'];
    					$mang_thuc_hien[$key]['no_cuoi_ky'] = $value['no_dau_ky'];
    				} elseif(isset($value['no_cuoi_ky'])) {
    					$mang_thuc_hien[$key]['no_cuoi_ky'] = $value['no_cuoi_ky'];
    					$mang_thuc_hien[$key]['no_dau_ky'] 	= $value['no_cuoi_ky'];
    				}

            		# Tính tổng nợ cuối kỳ

    				$tong_no_dau_ky 	= $tong_no_dau_ky  + $mang_thuc_hien[$key]['no_dau_ky'];
    				$tong_no_cuoi_ky 	= $tong_no_cuoi_ky + $mang_thuc_hien[$key]['no_cuoi_ky'];
            		# Lấy ra kết quả cuối cùng
    				$data[$key] = $mang_thuc_hien[$key];


        		# Nếu có giao dịch hay không
    			} else {
            		# Nếu có giao dịch
    				if(isset($items[$key]['ghi_co'])) {

    					$mang_thuc_hien[$key]['ghi_co'] = $items[$key]['ghi_co'];
    					$mang_thuc_hien[$key]['ghi_no'] = $items[$key]['ghi_no'];

            			# Nếu có giao dịch và có nợ đầu kỳ
            			# Tính nợ cuối theo nợ đầu
    					if(isset($no_dau_ky_theo_nhom_khach_hang[$key]['no_dau_ky'])){
    						$mang_thuc_hien[$key]['no_dau_ky'] = $no_dau_ky_theo_nhom_khach_hang[$key]['no_dau_ky'];
    						$mang_thuc_hien[$key]['no_cuoi_ky'] = $no_dau_ky_theo_nhom_khach_hang[$key]['no_dau_ky'] - $items[$key]['ghi_co'] + $items[$key]['ghi_no'];

            			# Nếu có giao dịch mà không có nợ đầu kỳ
        				# Tính nợ đầu theo nợ cuối
    					} 


            			# Tính tổng nợ cuối kỳ

    					$tong_no_dau_ky 	= $tong_no_dau_ky  + $mang_thuc_hien[$key]['no_dau_ky'];
    					$tong_no_cuoi_ky 	= $tong_no_cuoi_ky + $mang_thuc_hien[$key]['no_cuoi_ky'];
            			# Lấy ra kết quả cuối cùng
    					$data[$key] = $mang_thuc_hien[$key];

            		# Nếu không có giao dịch
    				} else {

    					$mang_thuc_hien[$key]['ghi_co'] = 0;
    					$mang_thuc_hien[$key]['ghi_no'] = 0;
    					if(isset($value['no_dau_ky'])) {
    						$mang_thuc_hien[$key]['no_dau_ky'] = $value['no_dau_ky'];
    					} elseif(isset($value['no_cuoi_ky'])) {
    						$mang_thuc_hien[$key]['no_cuoi_ky'] = $value['no_cuoi_ky'];
    					}

	            		# Tính tổng nợ cuối kỳ

    					$tong_no_dau_ky 	= $tong_no_dau_ky  + $mang_thuc_hien[$key]['no_dau_ky'];
    					$tong_no_cuoi_ky 	= $tong_no_cuoi_ky + $mang_thuc_hien[$key]['no_cuoi_ky'];
	            		# Lấy ra kết quả cuối cùng
    					$data[$key] = $mang_thuc_hien[$key];
    				}

    			}

    		}
            # Chuyển đổi nợ đầu vào nợ cuối thành chuỗi

    		$tong_no_dau_ky = number_format($tong_no_dau_ky,1);
    		$tong_no_cuoi_ky = number_format($tong_no_cuoi_ky,1);

    		// var_dump($data);
    		// die;
    		$html = $this->load->view('reports/table/chi_tiet_theo_nhom_khach_hang', array('items'=>$data), true);

    		$this->load->library("pagination");
    		$this->pagination->initialize($config);
    		$this->pagination->createConfig('front-end');

    		$pagination = $this->pagination->create_ajax();

    		$result = array('count'=> $config['total_rows'], 'html_string'=>$html, 'pagination'=>$pagination,'debt_start'=>$tong_no_dau_ky, 'debt_end'=>$tong_no_cuoi_ky);
            // var_dump($result);
    		echo json_encode($result);
    	}
    }

    function bao_cao_chi_tiet_cong_no_nhom_khach_hang_excel() {

    	$arrParam = $this->input->get();

    	unset($_SESSION['bao_cao_chi_tiet_cong_no_nhom_khach_hang']);
    	$arrParam['options'] = $arrParam['customer_balance_options'];

    	if($arrParam['nhom_khach_hang'] == -1) {
    		echo 'Bạn chưa chọn nhóm khách hàng';
    		return;
    	}
    	$this->load->model('reports/Specific_customer_store_account');
        #-------------------------------------------------------------------------------------------------#

        # Biến hien_thi có cấu trúc C:D:E với C:D là 2 trường cần merge E là trường vị trí hiển thị dữ liệu

        #-------------------------------------------------------------------------------------------------#
    	$thong_tin_diem_ban_hang = $this->Location->get_info($_GET['location_ids'])->name;
    	$ten_cong_ty = $this->config->item('company');
   		// var_dump($thong_tin->name);
        # đầu trang

    	$_title = array(
    		array('value_field' => 'title','dong_nao'=>4,'hien_thi'=>'A:I:A'),
    		array('value_field' => 'date','dong_nao'=>5,'hien_thi'=>'A:G:B'),
    		array('value_field' => 'type_customer','dong_nao'=>6,'hien_thi'=>'C:D:C'),
    		array('value_field' => 'thong_tin_diem_ban_hang','dong_nao'=>2,'hien_thi'=>'A:D:A'),
    		array('value_field' => 'ten_cong_ty','dong_nao'=>1,'hien_thi'=>'A:D:A'),
    	);
// 		echo $this->config->item('company');
// 		die;
    	$_headers 	 = array(
    		array('col' => 'A','value_field' => 'code'),
    		array('col' => 'B','value_field' => 'last_name'),
    		array('col' => 'C','value_field' => 'phone_number'),
    		array('col' => 'D','value_field' => 'email'),
    		array('col' => 'E','value_field' => 'address_1'),
    		array('col' => 'F','value_field' => 'no_dau_ky'),
    		array('col' => 'G','value_field' => 'ghi_no'),
    		array('col' => 'H','value_field' => 'ghi_co'),
    		array('col' => 'I','value_field' => 'no_cuoi_ky'),
    	);		

    	$_footer = array(
			# phần tổng cuối cùng
    		array('sum' => 'F','value_field' => 'SUM','hien_thi'=>'F:F:F'),
    		array('sum' => 'G','value_field' => 'SUM','hien_thi'=>'G:G:G'),
    		array('sum' => 'H','value_field' => 'SUM','hien_thi'=>'H:H:H'),
    		array('sum' => 'I','value_field' => 'SUM','hien_thi'=>'I:I:I'),
    		array('sum' => 'F','value_field' => 'SUM','ten_truong'=>'Tổng: ','hien_thi'=>'E:E:E'),

    	);

		# dữ liệu truyền ra	
    	$model = $this->Specific_customer_store_account;
    	$items = $model->list_item_theo_nhom_khach_hang($arrParam)->result_array();
    	$lay_tong_giao_dich_dau_ky  = $model->lay_tong_giao_dich_dau_ky_nhom_khach_hang($arrParam)->result_array();
    	$no_dau_ky_theo_nhom_khach_hang = $model->lay_no_dau_ky_theo_nhom_khach_hang($arrParam);

    	foreach ($no_dau_ky_theo_nhom_khach_hang as $key => $value) {
    		if(isset($lay_tong_giao_dich_dau_ky[$key]['ghi_co'])) $ghi_co = $lay_tong_giao_dich_dau_ky[$key]['ghi_co']; 
    		else $ghi_co = 0;
    		if(isset($lay_tong_giao_dich_dau_ky[$key]['ghi_no'])) $ghi_no = $lay_tong_giao_dich_dau_ky[$key]['ghi_no']; 
    		else $ghi_no = 0;

    		$no_dau_ky_theo_nhom_khach_hang[$key]['no_dau_ky'] = $no_dau_ky_theo_nhom_khach_hang[$key]['no_dau_ky'] - $ghi_co + $ghi_no;
    	}

        # LỌC RA MẢNG DÀI NHẤT
    	if(isset($no_dau_ky_theo_nhom_khach_hang)) {
    		if(count($items) < count($no_dau_ky_theo_nhom_khach_hang)) {
    			$mang_thuc_hien = $no_dau_ky_theo_nhom_khach_hang;
    		} else $mang_thuc_hien = $items;
    	}

    	$data = array();

    	foreach ($mang_thuc_hien as $key => $value) {
            	# kiểm tra nếu không có khách hàng đó tức là không có giao dịch
    		if(!isset($items[$key]['person_id'])){
            		# Lưu ghi có và ghi nợ = 0
    			$mang_thuc_hien[$key]['ghi_co'] = 0;
    			$mang_thuc_hien[$key]['ghi_no'] = 0;
    			if(isset($value['no_dau_ky'])) {
    				$mang_thuc_hien[$key]['no_dau_ky'] 	= $value['no_dau_ky'];
    				$mang_thuc_hien[$key]['no_cuoi_ky'] = $value['no_dau_ky'];
    			} elseif(isset($value['no_cuoi_ky'])) {
    				$mang_thuc_hien[$key]['no_cuoi_ky'] = $value['no_cuoi_ky'];
    				$mang_thuc_hien[$key]['no_dau_ky'] 	= $value['no_cuoi_ky'];
    			}

            		# Lấy ra kết quả cuối cùng
    			$data[$key] = $mang_thuc_hien[$key];


        		# Nếu có giao dịch hay không
    		} else {
            		# Nếu có giao dịch
    			if(isset($items[$key]['ghi_co'])) {

    				$mang_thuc_hien[$key]['ghi_co'] = $items[$key]['ghi_co'];
    				$mang_thuc_hien[$key]['ghi_no'] = $items[$key]['ghi_no'];

            			# Nếu có giao dịch và có nợ đầu kỳ
            			# Tính nợ cuối theo nợ đầu
    				if(isset($no_dau_ky_theo_nhom_khach_hang[$key]['no_dau_ky'])){
    					$mang_thuc_hien[$key]['no_dau_ky'] = $no_dau_ky_theo_nhom_khach_hang[$key]['no_dau_ky'];
    					$mang_thuc_hien[$key]['no_cuoi_ky'] = $no_dau_ky_theo_nhom_khach_hang[$key]['no_dau_ky'] - $items[$key]['ghi_co'] + $items[$key]['ghi_no'];

            			# Nếu có giao dịch mà không có nợ đầu kỳ
        				# Tính nợ đầu theo nợ cuối
    				} 


            			# Lấy ra kết quả cuối cùng
    				$data[$key] = $mang_thuc_hien[$key];

            		# Nếu không có giao dịch
    			} else {

    				$mang_thuc_hien[$key]['ghi_co'] = 0;
    				$mang_thuc_hien[$key]['ghi_no'] = 0;
    				if(isset($value['no_dau_ky'])) {
    					$mang_thuc_hien[$key]['no_dau_ky'] = $value['no_dau_ky'];
    				} elseif(isset($value['no_cuoi_ky'])) {
    					$mang_thuc_hien[$key]['no_cuoi_ky'] = $value['no_cuoi_ky'];
    				}
	            		# Lấy ra kết quả cuối cùng
    				$data[$key] = $mang_thuc_hien[$key];
    			}

    		}

    		if($value['type_customer'] != 0){
    			$customers_type = $this->Customer->lay_ra_ten_danh_muc('customers_type',$value['type_customer']);
					$data[$key]['type_customer'] = $customers_type[0]['name'];  # chuyển đổi dữ liệu từ số sang chữ
				} else $data[$key]['type_customer'] = 'Chưa phân loại';

			}


			if($arrParam['options'] == 1){
				$title = 'BÁO CÁO CHI TIẾT CÔNG NỢ NHÓM KHÁCH HÀNG - Tài khoản khách nợ';
			} else $title = 'BÁO CÁO CHI TIẾT CÔNG NỢ NHÓM KHÁCH HÀNG - Tài khoản nợ khách';
			if(empty($arrParam['start_date'])) $date = 'Toàn bộ thời gian'; else $date = 'Từ : '.$arrParam['start_date'].' đến : '.$arrParam['end_date'];
			// var_dump($items);
			foreach ($data as $value) {
			// echo $value['no_dau_ky'];
				$result[] = array(
					'code' 			=>  $value['code'],
					'last_name' 	=> 	$value['last_name'],
					'phone_number' 	=>  $value['phone_number'],
					'email' 		=>  $value['email'],
					'address_1' 	=>  $value['address_1'],
					'no_dau_ky'     =>  $value['no_dau_ky'],
					'ghi_co' 		=>  $value['ghi_co'],
					'ghi_no' 		=>  $value['ghi_no'],
					'no_cuoi_ky' 	=>  $value['no_cuoi_ky'],
					'type_customer' =>  $value['type_customer'],
					'date'			=>	$date,
					'thong_tin_diem_ban_hang' => $thong_tin_diem_ban_hang,
					'ten_cong_ty'   =>  $ten_cong_ty,
					'title'         => 	$title,
				);
			}

			$bizExcel = new BizExcel('bao_cao_chi_tiet_nhom_khach_hang.xlsx');
			$bizExcel->Row_title($_title);
			$bizExcel->setNumberRowStartBody(8)->setHeaderOfBody($_headers);
			$bizExcel->RowEndBody_theo_tung_cot($_footer);
			$bizExcel->tat_auto_size(false);
			$bizExcel->setDataExcel($result);
		// $this->oPHPExcel->getActiveSheet()->setCellValue('B5','ducanh');

		// $bizExcel->addToNewSheet('Tổng hợp nhóm khách hàng')->generateFile(false, '', false);


			$excelContent = $bizExcel->generateFile(false);
		// die;
			$this->load->helper('download');
			force_download('bao_cao_chi_tiet_nhom_khach_hang.xlsx', $excelContent);
			exit;
		}
    #---------------------------------------------------------------------------------------------------#

    					# BÁO CÁO TỔNG HỢP CÔNG NỢ KHÁCH HÀNG

    #---------------------------------------------------------------------------------------------------#


		function bao_cao_TONG_HOP_cong_no_khach_hang() 
		{

			$this->check_action_permission('view_store_account');
			$this->load->model('reports/Specific_customer_store_account');
			$model = $this->Specific_customer_store_account;

			$data['action'] = $this->uri->segment(2);
			$data['locations'] = $this->Location->list_item();
			$data['title'] = 'Báo cáo tổng hợp công nợ khách hàng';
			$data['no_excel']  = false;

			if (isset($_SESSION['bao_cao_TONG_HOP_cong_no_khach_hang'])) {
				$data['url_print'] = $_SESSION['bao_cao_TONG_HOP_cong_no_khach_hang']['url_print'];
				$data['filter']    = $_SESSION['bao_cao_TONG_HOP_cong_no_khach_hang'];
				unset($_SESSION['bao_cao_TONG_HOP_cong_no_khach_hang']);

				if ($data['filter']['export_excel'] == 1)
					redirect($data['url_print']);
				else

					$this->load->view("reports/bao_cao_TONG_HOP_cong_no_khach_hang",$data);

			}else{
				$data['inputs']      = array('input_date_range','select_customers_liabilities');
				$data['customer_id'] = $this->input->get('cus_id', -1);
				$this->load->view("reports/n9_tabular",$data);
			}
		}

		function bao_cao_TONG_HOP_cong_no_khach_hang_store(){
			$post  = $this->input->post();
			$arrParam = array_merge($post, $this->input->get());

			if(!empty($arrParam)) {
				$this->load->model('reports/Specific_customer_store_account');
				$model = $this->Specific_customer_store_account;
				$arrParam['paginator']             = $this->_paginator;
				$arrParam['page']                  =  $this->uri->segment(3, 1);
				$config['per_page'] = $arrParam['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
				$config['uri_segment'] = 3;
				$config['use_page_numbers'] = TRUE;
				$config['base_url'] = base_url() . 'reports/bao_cao_tong_hop_cong_no_nhom_khach_hang_store';
				$this->load->model('reports/Specific_customer_store_account');
				$model = $this->Specific_customer_store_account;
				$items = array();

            # Đếm số kết quả
				$config['total_rows'] = $model->summary_customer_opening_closing_balance_count($arrParam);
				$summaryData          = $model->summary_customer_opening_closing_balance_total($arrParam);
            //list items
				$items = $model->summary_customer_opening_closing_balance($arrParam);
				$html = $this->load->view('reports/table/bao_cao_tong_hop_cong_no_khach_hang', array('items'=>$items), true);
				$this->load->library("pagination");
				$this->pagination->initialize($config);
				$this->pagination->createConfig('front-end');

				$pagination = $this->pagination->create_ajax();

				$result = array('count'       => $config['total_rows'], 
					'html_string' => $html, 
					'pagination'  => $pagination, 
					'debt_start'  => to_currency($summaryData['total_opening_balance']), 
					'debt_end'    => to_currency($summaryData['total_closing_balance']),
					'debit'       => to_currency($summaryData['total_debit']),
					'credit'      => to_currency($summaryData['total_credit'])
				);

				echo json_encode($result);
			}
		}

		function bao_cao_TONG_HOP_cong_no_khach_hang_excel() {
			$post  = $this->input->post();
			$arrParams = array_merge($post, $this->input->get());

			unset($_SESSION['bao_cao_TONG_HOP_cong_no_khach_hang']);

			$this->load->model('reports/Specific_customer_store_account');
        #-------------------------------------------------------------------------------------------------#

        # Biến hien_thi có cấu trúc C:D:E với C:D là 2 trường cần merge E là trường vị trí hiển thị dữ liệu

        #-------------------------------------------------------------------------------------------------#
			$thong_tin_diem_ban_hang = 'Điểm bán hàng : '.$this->Location->get_info($_GET['location_ids'])->name;
			$ten_cong_ty = $this->config->item('company');
			$customer_balance_options =  $arrParams['customer_balance_options'] == 1 ? 'TK nợ khách' : 'TK khách nợ';
   		// var_dump($thong_tin->name);
        # đầu trang
			$title = 'BÁO CÁO TỔNG HỢP CÔNG NỢ KHÁCH HÀNG';
			if(empty($arrParams['start_date'])) $date = 'Toàn bộ thời gian'; else $date = 'Từ : '.$arrParams['start_date'].' đến : '.$arrParams['end_date'];

			$header_of_multicol[] = array('mergeStartCol' =>'A4','mergeEndCol'=>'G4','text' =>$title,'styles'=>array('bold' =>true,'font'=>true ));
			$header_of_multicol[] = array('mergeStartCol' =>'A5','mergeEndCol'=>'G5','text' =>$date,'styles'=>array('bold' =>true,'font'=>true));
			$header_of_multicol[] = array('mergeStartCol' =>'A6','mergeEndCol'=>'G6','text' =>$customer_balance_options,'styles'=>array('bold' =>true,'font'=>true));
			$header_of_multicol[] = array('mergeStartCol' =>'A2','mergeEndCol'=>'C2','text' =>$thong_tin_diem_ban_hang ,'styles'=>array('bold' =>true,'font'=>true));
			$header_of_multicol[] = array('mergeStartCol' =>'A1','mergeEndCol'=>'C1','text' =>$ten_cong_ty ,'styles'=>array('bold' =>true,'font'=>true));
			

			$_headers 	 = array(
				array('col' => 'A','value_field' => '__AUTO__'),
				array('col' => 'B','value_field' => 'code'),
				array('col' => 'C','value_field' => 'last_name'),
				array('col' => 'D','value_field' => 'opening_balance'),
				array('col' => 'E','value_field' => 'debit'),
				array('col' => 'F','value_field' => 'credit'),
				array('col' => 'G','value_field' => 'closing_balance'),
			);		

			$_footer = array(
			# phần tổng cuối cùng
				array('sum' => 'D','value_field' => 'SUM','ten_truong'=>'Tổng nợ đầu: ','hien_thi'=>'E:E:F'),
				array('sum' => 'E','value_field' => 'SUM','ten_truong'=>'Tổng ghi nợ: ','hien_thi'=>'E:E:F'),
				array('sum' => 'F','value_field' => 'SUM','ten_truong'=>'Tổng ghi có: ','hien_thi'=>'E:E:F'),
				array('sum' => 'G','value_field' => 'SUM','ten_truong'=>'Tổng nợ cuối: ','hien_thi'=>'E:E:F'),
			);

		# dữ liệu truyền ra	
			$model = $this->Specific_customer_store_account;
			$summaryData          = $model->summary_customer_opening_closing_balance_total($arrParams);
            //list items
			$items                = $model->summary_customer_opening_closing_balance($arrParams);




			
			foreach ($items as $value) {
			// echo $value['no_dau_ky'];
				$result[] = array(
					'code' 						=>  $value['code'],
					'last_name' 				=> 	$value['last_name'],
					'opening_balance'     	    =>  $value['opening_balance'],
					'closing_balance' 		    =>  $value['closing_balance'],
					'debit'     	            =>  $value['debit'],
					'credit' 		            =>  $value['debit'],
				);
			}
			$bizExcel = new BizExcel('bao_cao_tong_hop_cong_no_khach_hang.xlsx');
			$bizExcel->setNumberRowStartBody(8)->setHeaderOfBody($_headers);
			$bizExcel->setHeaderOfMultiCol($header_of_multicol);
			$bizExcel->RowEndBody($_footer);
			$bizExcel->tat_auto_size(false);
			$bizExcel->setDataExcel($result);

			$excelContent = $bizExcel->generateFile(false);
		// die;
			$this->load->helper('download');
			force_download('bao_cao_tong_hop_cong_no_khach_hang.xlsx', $excelContent);
			exit;
		}

		function specific_customer_store_account($start_date, $end_date, $customer_id, $sale_type, $export_excel=0, $offset = 0)
		{
			$this->load->model('Sale');
			$this->load->model('Customer');
			$this->check_action_permission('view_store_account');
			$start_date=rawurldecode($start_date);
			$end_date=rawurldecode($end_date);

			$this->load->model('reports/Specific_customer_store_account');
			$model = $this->Specific_customer_store_account;		
			$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'customer_id' =>$customer_id, 'sale_type' => $sale_type, 'offset'=> $offset, 'export_excel' => $export_excel));
			$config = array();
			$config['base_url'] = site_url("reports/specific_customer_store_account/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$customer_id/$sale_type/$export_excel");
			$config['total_rows'] = $model->getTotalRows();
			$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
			$config['uri_segment'] = 8;
			$this->load->library('pagination');$this->pagination->initialize($config);

			$headers = $model->getDataColumns();
			$report_data = $model->getData();

			$tabular_data = array();

			foreach($report_data as $row)
			{
				$tabular_data[] = array(array('data'=>$row['sno'], 'align'=> 'left'),
					array('data'=>date(get_date_format().'-'.get_time_format(), strtotime($row['date'])), 'align'=> 'left'),
					array('data'=>$row['sale_id'] ? anchor('sales/receipt/'.$row['sale_id'], $this->config->item('sale_prefix').' '.$row['sale_id'], array('target' => '_blank')) : '-', 'align'=> 'center'),
					array('data'=> $row['transaction_amount'] > 0 ? to_currency($row['transaction_amount']) : to_currency(0), 'align'=> 'right'),
					array('data'=>$row['transaction_amount'] < 0 ? to_currency($row['transaction_amount'] * -1)  : to_currency(0), 'align'=> 'right'),
					array('data'=>to_currency($row['balance']), 'align'=> 'right'),
					array('data'=>$row['items'], 'align'=> 'left'),
					array('data'=>$row['comment'], 'align'=> 'left'));

			}

			$customer_info = $this->Customer->get_info($customer_id);

			if ($customer_info->company_name)
			{
				$customer_title = $customer_info->company_name.' ('.$customer_info->first_name .' '. $customer_info->last_name.')';
			}
			else
			{
				$customer_title = $customer_info->first_name .' '. $customer_info->last_name;		
			}
			$data = array(
				"title" => lang('reports_detailed_store_account_report').$customer_title,
				"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
				"headers" => $headers,
				"data" => $tabular_data,
				"summary_data" => $model->getSummaryData(),
				"export_excel" => $export_excel,
				"pagination" => $this->pagination->create_links(),
			);

			$this->load->view("reports/tabular",$data);

		}

		function store_account_statements_input()
		{
			$data = $this->_get_common_report_data();

			$data['search_suggestion_url'] = site_url('reports/customer_search');		
			$this->load->view('reports/store_account_statements_input', $data);

		}

		function store_account_statements($customer_id = -1, $start_date, $end_date, $hide_items = 0, $pull_payments_by = 'payment_date', $offset=0)
		{
			$this->load->model('Sale');
			$this->load->model('Customer');
			$this->check_action_permission('view_store_account');
			$this->load->model('reports/Store_account_statements');
			$model = $this->Store_account_statements;
			$model->setParams(array('customer_id' =>$customer_id,'offset' => $offset, 'start_date' => $start_date, 'end_date'=>$end_date, 'pull_payments_by' => $pull_payments_by));
			$config = array();
			$config['base_url'] = site_url("reports/store_account_statements/$customer_id/$start_date/$end_date/$hide_items/$pull_payments_by");
			$config['total_rows'] = $model->getTotalRows();
			$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
			$config['uri_segment'] = 8;
			$this->load->library('pagination');$this->pagination->initialize($config);

			$report_data = $model->getData();

			$data = array(
				"title" => lang('reports_store_account_statements'),
				"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
				'report_data' => $report_data,
				'hide_items' => $hide_items,
				"pagination" => $this->pagination->create_links(),
				'date_column' => $pull_payments_by == 'payment_date' ? 'date' : 'sale_time',
			);

			$this->load->view("reports/store_account_statements",$data);

		}

		function store_account_statements_email_customer($customer_id, $start_date, $end_date, $hide_items = 0, $pull_payments_by = 'payment_date', $offset=0)
		{
			$this->load->model('Sale');
			$this->load->model('Customer');

			$this->check_action_permission('view_store_account');
			$this->load->model('reports/Store_account_statements');
			$model = $this->Store_account_statements;
			$model->setParams(array('customer_id' =>$customer_id,'offset' => $offset, 'start_date' => $start_date, 'end_date'=>$end_date, 'pull_payments_by' => $pull_payments_by));

			$report_data = $model->getData();

			$customer_info = $this->Customer->get_info($customer_id);
			$data = array(
				"title" => lang('reports_store_account_statement'),
				"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
				'report_data' => $report_data,
				'hide_items' => $hide_items,
				'date_column' => $pull_payments_by == 'payment_date' ? 'date' : 'sale_time',
			);

			if (!empty($customer_info->email))
			{
				$this->load->library('email');
				$config = array();
				$config['mailtype'] = 'html';

				$this->email->initialize($config);
				$this->email->from($this->Location->get_info_for_key('email') ? $this->Location->get_info_for_key('email') : 'no-reply@mg.4biz.vn', $this->config->item('company'));
				$this->email->to($customer_info->email); 

				$this->email->subject(lang('reports_store_account_statement'));
				$this->email->message($this->load->view("reports/store_account_statement_email",$data, true));	
				$this->email->send();
			}
		}

		function detail_sales() {
			$this->load->model('Sale');
			$this->check_action_permission('view_sales');

			$data['action'] = $this->uri->segment(2);
			$data['locations'] = $this->Location->list_item();
			$data['title'] = 'Báo cáo chi tiết bán hàng';

			$_SESSION['detail_sales'] = array();

			if(isset($_SESSION['detail_sales'])) {
				$data['url_print'] = $_SESSION['detail_sales']['url_print'];
				$data['filter'] = $_SESSION['detail_sales'];
				unset($_SESSION['detail_sales']);

				if($data['filter']['export_excel'] == 1)
					redirect($data['url_print']);
				else
					$this->load->view("reports/detail_sales",$data);

			}else{
				$data['inputs']   = array('input_date_range', 'select_multiple_employee', 'select_multi_group_of_location', 'select_sale_type', 'input_locations');
				$this->load->view("reports/n9_tabular",$data);
			}
		}


		function detailed_payments($start_date, $end_date, $sale_type, $export_excel=0, $offset = 0)
		{
			$this->load->model('Sale');
			$this->check_action_permission('view_payments');
			$start_date=rawurldecode($start_date);
			$end_date=rawurldecode($end_date);

			$this->load->model('reports/Detailed_payments');
			$model = $this->Detailed_payments;
			$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'offset'=> $offset, 'export_excel' => $export_excel));
			$sale_ids = $model->get_sale_ids_for_payments();
			$this->Sale->create_sales_items_temp_table(array('sale_ids' => $sale_ids, 'sale_type' => $sale_type));

			$config = array();
			$config['base_url'] = site_url("reports/detailed_payments/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$sale_type/$export_excel");
			$config['total_rows'] = $model->getTotalRows();
			$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
			$config['uri_segment'] = 7;
			$this->load->library('pagination');$this->pagination->initialize($config);

			$headers = $model->getDataColumns();
			$report_data = $model->getData();

			$summary_data = array();
			$details_data = array();


			foreach($report_data['summary'] as $sale_id=>$row)
			{			
				foreach($row as $payment_type => $payment_data_row)
				{
					$summary_data_row = array();
					$summary_data_row[] = array('data'=>anchor('sales/receipt/'.$payment_data_row['sale_id'], '<i class="ion-printer"></i>', array('target' => '_blank', 'class'=>'hidden-print')).'<span class="visible-print">'.$payment_data_row['sale_id'].'</span>'.anchor('sales/edit/'.$payment_data_row['sale_id'], '<i class="ion-document-text"></i>', array('target' => '_blank')).' '.anchor('sales/edit/'.$payment_data_row['sale_id'], lang('common_edit').' '.$payment_data_row['sale_id'], array('target' => '_blank','class'=>'hidden-print')), 'align'=>'left');
					$summary_data_row[] = array('data'=>date(get_date_format().'-'.get_time_format(), strtotime($payment_data_row['sale_time'])), 'align'=>'left');
					$summary_data_row[] = array('data'=>date(get_date_format().'-'.get_time_format(), strtotime($payment_data_row['payment_date'])), 'align'=>'left');
					$summary_data_row[] = array('data'=>$payment_data_row['payment_type'], 'align'=>'left');
					$summary_data_row[] = array('data'=>to_currency($payment_data_row['payment_amount']), 'align'=>'right');

					$summary_data[$sale_id.'|'.$payment_type] = $summary_data_row;
				}
			}

			$temp_details_data = array();

			foreach($report_data['details']['sale_ids'] as $sale_id => $drows)
			{
				$payment_types = array();
				foreach ($drows as $drow)
				{
					$payment_types[$drow['payment_type']] = TRUE;
				}

				foreach(array_keys($payment_types) as $payment_type)
				{
					foreach ($drows as $drow)
					{
						$details_data_row = array();

						$details_data_row[] = array('data'=>date(get_date_format().'-'.get_time_format(), strtotime($drow['payment_date'])), 'align'=>'left');
						$details_data_row[] = array('data'=>$drow['payment_type'], 'align'=>'left');
						$details_data_row[] = array('data'=>to_currency($drow['payment_amount']), 'align'=>'right');

						$details_data[$sale_id.'|'.$payment_type][] = $details_data_row;
					}
				}
			}

			$data = array(
				"title" =>lang('reports_detailed_payments_report'),
				"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
				"headers" => $model->getDataColumns(),
				"summary_data" => $summary_data,
				"details_data" => $details_data,
				"overall_summary_data" => $model->getSummaryData(),
				"export_excel" => $export_excel,
				"pagination" => $this->pagination->create_links(),
			);

			$this->load->view("reports/tabular_details",$data);
		}

		function detailed_suspended_sales($start_date, $end_date, $sale_type, $export_excel=0, $offset = 0)
		{			
			$this->load->model('Sale');	
			$this->check_action_permission('view_suspended_sales');
			$start_date=rawurldecode($start_date);
			$end_date=rawurldecode($end_date);

			$this->load->model('reports/Detailed_suspended_sales');
			$model = $this->Detailed_suspended_sales;
			$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel' => $export_excel, 'force_suspended' => true));

			$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'force_suspended' => true));
			$config = array();
			$config['base_url'] = site_url("reports/detailed_suspended_sales/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$sale_type/$export_excel");
			$config['total_rows'] = $model->getTotalRows();
			$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
			$config['uri_segment'] = 7;
			$this->load->library('pagination');$this->pagination->initialize($config);

			$headers = $model->getDataColumns();
			$report_data = $model->getData();

			$summary_data = array();
			$details_data = array();

			$location_count = count(Report::get_selected_location_ids());

			foreach($report_data['summary'] as $key=>$row)
			{
				$summary_data_row = array();

				$link = site_url('reports/specific_customer/'.$start_date.'/'.$end_date.'/'.$row['customer_id'].'/all/0');

				$summary_data_row[] = array('data'=>anchor('sales/receipt/'.$row['sale_id'], '<i class="ion-printer"></i>', array('target' => '_blank', 'class'=>'hidden-print')).'<span class="visible-print">'.$row['sale_id'].'</span>'.anchor('sales/edit/'.$row['sale_id'], '<i class="ion-document-text"></i>', array('target' => '_blank')).' '.anchor('sales/edit/'.$row['sale_id'], lang('common_edit').' '.$row['sale_id'], array('target' => '_blank','class'=>'hidden-print')), 'align'=>'left');

				if ($location_count > 1)
				{
					$summary_data_row[] = array('data'=>$row['location_name'], 'align' => 'left');
				}

				$summary_data_row[] = array('data'=>date(get_date_format().'-'.get_time_format(), strtotime($row['sale_time'])), 'align'=>'left');
				$summary_data_row[] = array('data'=>$row['register_name'], 'align'=>'left');
				$summary_data_row[] = array('data'=>to_quantity($row['items_purchased']), 'align'=>'left');
				$summary_data_row[] = array('data'=>$row['employee_name'].($row['sold_by_employee'] && $row['sold_by_employee'] != $row['employee_name'] ? '/'. $row['sold_by_employee']: ''), 'align'=>'left');
				$summary_data_row[] = array('data'=>'<a href="'.$link.'" target="_blank">'.$row['customer_name'].(isset($row['account_number']) && $row['account_number'] ? ' ('.$row['account_number'].')' : '').'</a>', 'align'=>'left');
				$summary_data_row[] = array('data'=>to_currency($row['subtotal']), 'align'=>'right');
				$summary_data_row[] = array('data'=>to_currency($row['total']), 'align'=>'right');
				$summary_data_row[] = array('data'=>to_currency($row['tax']), 'align'=>'right');

				if($this->has_profit_permission)
				{
					$summary_data_row[] = array('data'=>to_currency($row['profit']), 'align'=>'right');
				}

				$summary_data_row[] = array('data'=>$row['payment_type'], 'align'=>'right');
				$summary_data_row[] = array('data'=>$row['comment'], 'align'=>'right');


				if ($row['suspended'] == 1)
				{
					$summary_data_row[] = array('data'=> lang('common_layaway'), 'align'=>'right');
				}
				elseif ($row['suspended'] == 2)
				{
					$summary_data_row[] = array('data'=> lang('common_estimate'), 'align'=>'right');
				}
				elseif ($row['was_layaway'] == 1)
				{
					$summary_data_row[] = array('data'=> lang('reports_completed_layaway'), 'align'=>'right');
				}
				elseif ($row['was_estimate'] == 1)
				{
					$summary_data_row[] = array('data'=> lang('reports_completed_estimate'), 'align'=>'right');
				}

				$summary_data[$key] = $summary_data_row;


				foreach($report_data['details'][$key] as $drow)
				{
					$details_data_row = array();

					$details_data_row[] = array('data'=>isset($drow['item_number']) ? $drow['item_number'] : $drow['item_kit_number'], 'align'=>'left');
					$details_data_row[] = array('data'=>isset($drow['item_product_id']) ? $drow['item_product_id'] : $drow['item_kit_product_id'], 'align'=>'left');
					$details_data_row[] = array('data'=>isset($drow['item_name']) ? $drow['item_name'] : $drow['item_kit_name'], 'align'=>'left');
					$details_data_row[] = array('data'=>$drow['category'], 'align'=>'left');
					$details_data_row[] = array('data'=>$drow['size'], 'align'=>'left');
					$details_data_row[] = array('data'=>$drow['serialnumber'], 'align'=>'left');
					$details_data_row[] = array('data'=>$drow['description'], 'align'=>'left');
					$details_data_row[] = array('data'=>to_quantity($drow['quantity_purchased']), 'align'=>'left');
					$details_data_row[] = array('data'=>to_currency($drow['subtotal']), 'align'=>'right');
					$details_data_row[] = array('data'=>to_currency($drow['total']), 'align'=>'right');
					$details_data_row[] = array('data'=>to_currency($drow['tax']), 'align'=>'right');

					if($this->has_profit_permission)
					{
						$details_data_row[] = array('data'=>to_currency($drow['profit']), 'align'=>'right');					
					}

					$details_data_row[] = array('data'=>$drow['discount_percent'].'%', 'align'=>'left');
					$details_data[$key][] = $details_data_row;
				}
			}

			$data = array(
				"title" =>lang('reports_detailed_suspended_sales_report'),
				"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
				"headers" => $model->getDataColumns(),
				"summary_data" => $summary_data,
				"details_data" => $details_data,
				"overall_summary_data" => $model->getSummaryData(),
				"export_excel" => $export_excel,
				"pagination" => $this->pagination->create_links(),
			);

			$this->load->view("reports/tabular_details",$data);
		}

		function specific_supplier_input()
		{
			$data = $this->_get_common_report_data(TRUE);
			$data['specific_input_name'] = lang('reports_supplier');
			$data['search_suggestion_url'] = site_url('reports/supplier_search/1');
			$this->load->view("reports/specific_input",$data);
		}

		function specific_supplier($start_date, $end_date, $supplier_id, $sale_type, $export_excel=0, $offset = 0)
		{
			$this->load->model('Supplier');
			$this->check_action_permission('view_suppliers');
			$start_date=rawurldecode($start_date);
			$end_date=rawurldecode($end_date);
			$this->load->model('reports/Specific_supplier');
			$config = array();
			$model    = $this->Specific_supplier;
			$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'supplier_id' =>$supplier_id, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel' => $export_excel));
			$this->Receiving->create_receivings_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'supplier_id' =>$supplier_id, 'sale_type' => $sale_type));
			$headers    = $model->getDataColumns();

			$listExpensesOfReceiving = array();
			$config['per_page']      = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;  
			$report_data             = $model->getData(array('page'=> $this->uri->segment(8,1),'per_page'=>$config['per_page']));
			$reportDataExportExcel   = $model->getData();
			$summary_data            = array();
			$details_data            = array();
			$location_count          = count(Report::get_selected_location_ids());
			$summary_data_row        = array();
			$detail_data_row         = array();
			$dataExcel               = $reportDataExportExcel['summary'];
			$childDataExcel          = array();
			$supplier_info           = $this->Supplier->get_info($supplier_id);

			$overall_summary_data['total_receive']     = 0;
			$overall_summary_data['tax'] = 0;
			$overall_summary_data['total_with_tax']    = 0;		

			foreach($report_data['overall_summary_data'] as $key=>$row)
			{
				$overall_summary_data['total_receive']     += $row['subtotal']; 
				$overall_summary_data['tax'] += $row['tax']; 
				$overall_summary_data['total_with_tax']    += $row['total_after_all'];
				foreach (explode(',',$row['payment_type']) as $index=>$paymentType)
				{
					if($paymentType == lang('reports_store_account'))
					{
						$overall_summary_data['total_with_tax']   -= explode(',',$row['transaction_amount'])[$index];
						break;
					}

				}

			}

			foreach($report_data['summary'] as $key=>$row)
			{	
				$paymentTypeForRowView ='';
		// Get all expenses in each receiving 
				$listExpensesOfThisReceiving = $this->Expense->get_item(array('receiving_id'=>$row['receiving_id'],'deleted' =>true));
				if(!empty($listExpensesOfThisReceiving)){
					$listExpensesOfReceiving[$key]	 =   $listExpensesOfThisReceiving;
				}
				foreach (explode(',',$row['payment_type']) as $index=>$paymentType)
				{
					$paymentTypeForRowView .= $paymentType.': '.to_currency(explode(',',$row['transaction_amount'])[$index])."<br>";
				}     

				$summary_data_row[$key][] = array('data'=>anchor('receivings/receipt/'.$row['receiving_id'], '<i class="ion-printer"></i>', array('target' => '_blank')).' '.anchor('receivings/edit/'.$row['receiving_id'], '<i class="ion-document-text"></i>', array('target' => '_blank')).' '.anchor('receivings/edit/'.$row['receiving_id'], lang('common_edit').' HĐNH '.$row['receiving_id'], array('target' => '_blank')), 'align'=> 'left');
				$summary_data_row[$key][] = array('data'=>date(get_date_format().'-'.get_time_format(), strtotime($row['receiving_time'])), 'align'=> 'left');
				$summary_data_row[$key][] = array('data'=>$row['emloyee_name'], 'align'=> 'left');
				$summary_data_row[$key][] = array('data'=>to_currency($row['subtotal']), 'align'=> 'left');
				$summary_data_row[$key][] = array('data'=>to_currency($row['total_discount']), 'align'=> 'right');
				$summary_data_row[$key][] = array('data'=>to_currency($row['tax']), 'align'=> 'right');
				$summary_data_row[$key][] = array('data'=>to_currency($row['total_after_all']), 'align'=> 'right');
				$summary_data_row[$key][] = array('data'=>$paymentTypeForRowView, 'align'=> 'left');
				foreach($report_data['details'][$key] as $detail_data)
				{
					$detail_data_row[$key][] = array( array('data'=>$detail_data['item_product_id'], 'align'=> 'left'),
						array('data'=>$detail_data['item_name'], 'align'=> 'left'),
						array('data'=>$detail_data['measure_name'], 'align'=> 'left'),
						array('data'=>to_quantity($detail_data['quantity_purchased']), 'align'=> 'right'),
						array('data'=>to_currency($detail_data['subtotal']), 'align'=> 'right'),
						array('data'=>to_currency($detail_data['total_discount']), 'align'=> 'right'),
						array('data'=>to_currency($detail_data['total']), 'align'=> 'right'));
				} 
			}

			$summary_data = $summary_data_row;
			$details_data = $detail_data_row;

			if($export_excel == 0){
				$config['base_url'] = site_url("reports/specific_supplier/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$supplier_id/$sale_type/$export_excel");
				$config['total_rows'] = $model->getTotalRows();

				$config['uri_segment'] = 8;
				$this->load->library('pagination');
				$this->pagination->initialize($config);


				$data = array(
					"title"                   => ($supplier_id!=-1)?$supplier_info->company_name :lang('reports_all'),
					"subtitle"                => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
					"headers"                 => $model->getDataColumns(),
					"summary_data"            => $summary_data,
					"details_data"            => $details_data,
					"overall_summary_data"    => $overall_summary_data,
					"export_excel"            => $export_excel,
					"listExpensesOfReceiving" =>$listExpensesOfReceiving,
					"pagination"              => $this->pagination->create_links(),
				);
				$this->load->view("reports/tabular_details",$data);
			}
			else
			{
				$dataExcel                     = [];
				$childDataExcel                = [];
				$moreChildDataExcel            = [];
				$header_of_child_col_name      = [];
				$more_header_of_child_col_name = [];
				$header_of_col_name            = [];
				$listExpensesOfAllReceiving    = [];

				$headerOfBody     = array(
					array('col' => 'A','value_field' =>'receiving_id'),
					array('col' => 'B','value_field' =>'receiving_time'),
					array('col' => 'C','value_field' =>'emloyee_name'),
					array('col' => 'D','value_field' =>'subtotal'),
					array('col' => 'E','value_field' =>'total_discount'),
					array('col' => 'F','value_field' =>'tax'),
					array('col' => 'G','value_field' =>'total_after_all'),
					array('col' => 'H','value_field' =>'paymentType'),
				);
				$fieldOfChildBody = array(
					array('col' => 'B','value_field' =>'item_product_id'),
					array('col' => 'C','value_field' =>'item_name'),
					array('col' => 'D','value_field' =>'measure_name'),
					array('col' => 'E','value_field' =>'quantity_purchased'),
					array('col' => 'F','value_field' =>'subtotal'),
					array('col' => 'G','value_field' =>'total_discount'),
					array('col' => 'H','value_field' =>'total'),
				);
				$moreFieldOfChildBody['expenses'] = array(
					array('col' => 'B:D','value_field' =>'expense_decription'),
					array('col' => 'E','value_field' =>'expense_type'),
					array('col' => 'F','value_field' =>'expense_amount'),
					array('col' => 'G','value_field' =>'expense_tax')
				);

				foreach($reportDataExportExcel['summary'] as $key=>$row)
				{
					$paymentTypeForExcel ='';
					foreach (explode(',',$row['payment_type']) as $index=>$paymentType)
					{
						$paymentTypeForExcel .= $paymentType.': '.to_currency(explode(',',$row['transaction_amount'])[$index])."\r\n";
					}     
					$listExpensesOfAllReceiving['expenses'][$key]  = $this->Expense->get_item(array('receiving_id'=>$row['receiving_id'],'deleted' =>true));

					$dataExcel[$key] = array(
						'receiving_id'    =>$row['receiving_id'],
						'receiving_time'  =>date(get_date_format().'-'.get_time_format(), strtotime($row['receiving_time'])),
						'emloyee_name'    =>$row['emloyee_name'],
						'subtotal'        =>to_currency($row['subtotal']),
						'total_discount'  =>to_currency($row['total_discount']),
						'tax'             =>to_currency($row['tax']),
						'total_after_all' =>to_currency($row['total_after_all']),
						'paymentType'     =>$paymentTypeForExcel,     


					);

					foreach($reportDataExportExcel['details'][$key] as $detail_data)
					{
						$childDataExcel[$key][] =  array( 
							'item_product_id'   =>$detail_data['item_product_id'],
							'item_name'         =>$detail_data['item_name'], 
							'measure_name'      =>$detail_data['measure_name'],
							'quantity_purchased'=>to_quantity($detail_data['quantity_purchased']),
							'subtotal'          =>to_currency($detail_data['subtotal']),
							'total_discount'    =>to_currency($detail_data['total_discount']),
							'total'             =>to_currency($detail_data['total'])
						);
					}
				}
				foreach($listExpensesOfAllReceiving['expenses'] as $key=>$row)
				{
					foreach($row as $receiving_id => $expense_value){
						$moreChildDataExcel['expenses'][$key][] = array(
							'expense_decription' => $expense_value['expense_description'],
							'expense_type'       => ($expense_value['expense_type']==1)?lang('reports_expense_type_1'):lang('reports_expense_type_2'),
							'expense_tax'        => to_currency($expense_value['expense_tax']),
							'expense_amount'     => to_currency($expense_value['expense_amount'])
						);
					}
				}
      // set header excel



			//Header of parent Col
				$excelColumn = 'A';
				foreach($headers['summary'] as $key => $header_detail)
				{
					$header_of_col_name[] = array('col' =>$excelColumn,'text' => $header_detail['data'],'styles'=>array('bold' =>true,'font'=>true, 'font_size'=>14,'is_fill'=>true,'color'=>'98d9da'));
					$excelColumn++;
				}
			//header of File
				$header_of_multicol[] = array('mergeStartCol' =>'A','mergeEndCol'=>$excelColumn,'text' =>lang('reports_specific_supplier'),'styles'=>array('bold' =>true,'font'=>true, 'font_size'=>26,'is_fill'=>true,'color'=>'98d9da'));

				$excelColumn = 'B';
			//Header of child Column
				foreach($headers['details'] as $key => $header_detail)
				{
					$header_of_child_col_name[] = array('col' =>$excelColumn,'text' => $header_detail['data'],'styles'=>array('font'=>true, 'font_size'=>12,'is_fill'=>true,'color'=>'e7e8e8'));
					$excelColumn++;
				}

				$more_header_of_child_col_name['expenses'][] = array('col' =>'B:D','text' => $headers['expenses'][0]['data'],'styles'=>array('font'=>true, 'font_size'=>12,'is_fill'=>true,'color'=>'e7e8e8'));
				$more_header_of_child_col_name['expenses'][] = array('col' =>'E','text' => $headers['expenses'][1]['data'],'styles'=>array('font'=>true, 'font_size'=>12,'is_fill'=>true,'color'=>'e7e8e8'));
				$more_header_of_child_col_name['expenses'][] = array('col' =>'F','text' => $headers['expenses'][2]['data'],'styles'=>array('font'=>true, 'font_size'=>12,'is_fill'=>true,'color'=>'e7e8e8'));
				$more_header_of_child_col_name['expenses'][] = array('col' =>'G','text' => $headers['expenses'][3]['data'],'styles'=>array('font'=>true, 'font_size'=>12,'is_fill'=>true,'color'=>'e7e8e8'));

				$bizExcel = new BizExcel('report_specific_supplier.xlsx');
				$bizExcel->setNumberRowBeginRow(1)->setHeaderOfMultiCol($header_of_multicol);
				$bizExcel->setNumberRowStartBody(2)->setHeaderOfBody($headerOfBody);
				$bizExcel->setFieldOfChildBody($fieldOfChildBody)->setMoreFieldOfChildBody($moreFieldOfChildBody);
				$bizExcel->setHeaderOfCol($header_of_col_name);
				$bizExcel->setDataExcel($dataExcel);
				$bizExcel->setHeaderOfChildCol($header_of_child_col_name);
				$bizExcel->setHeaderOfMoreChildCol($more_header_of_child_col_name);
				$bizExcel->setChildDataExcel($childDataExcel)->setMoreChildDataExcel($moreChildDataExcel);
				$bizExcel->setMoreChildData(true);
				$excelContent = $bizExcel->generateFile(false);
				$this->load->helper('download');
				force_download(lang('reports_specific_supplier').' '.$supplier_info->company_name. date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)).'.xlsx', $excelContent);
				exit;
			}
		}

		function specific_supplier_receivings($start_date, $end_date, $supplier_id, $sale_type, $export_excel=0, $offset = 0)
		{
			$this->load->model('Receiving');
			$this->load->model('Supplier');
			$this->check_action_permission('view_suppliers');
			$start_date=rawurldecode($start_date);
			$end_date=rawurldecode($end_date);

			$this->load->model('reports/Specific_supplier_receiving');
			$model = $this->Specific_supplier_receiving;
			$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'supplier_id' =>$supplier_id, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel' => $export_excel));

			$this->Receiving->create_receivings_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'supplier_id' =>$supplier_id, 'sale_type' => $sale_type));
			$config = array();
			$config['base_url'] = site_url("reports/specific_supplier_receivings/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$supplier_id/$sale_type/$export_excel");
			$config['total_rows'] = $model->getTotalRows();
			$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
			$config['uri_segment'] = 8;
			$this->load->library('pagination');$this->pagination->initialize($config);


			$headers = $model->getDataColumns();
			$report_data = $model->getData();

			$summary_data = array();
			$details_data = array();
			$location_count = count(Report::get_selected_location_ids());

			foreach($report_data['summary'] as $key=>$row)
			{			
				$summary_data_row[$key] = array();

				$summary_data_row[] = array('data'=>anchor('receivings/edit/'.$row['receiving_id'], lang('common_edit').' '.$row['receiving_id'], array('target' => '_blank')).' ['.anchor('items/generate_barcodes_from_recv/'.$row['receiving_id'], lang('common_barcode_sheet'), array('target' => '_blank')).' / '.anchor('items/generate_barcodes_labels_from_recv/'.$row['receiving_id'], lang('common_barcode_labels'), array('target' => '_blank')).']', 'align'=> 'left');

				if ($location_count > 1)
				{
					$summary_data_row[] = array('data'=>$row['location_name'], 'align' => 'left');
				}

				$summary_data_row[] = array('data'=>date(get_date_format().'-'.get_time_format(), strtotime($row['receiving_time'])), 'align'=> 'left');
				$summary_data_row[] = array('data'=>to_quantity($row['items_purchased']), 'align'=> 'left');
				$summary_data_row[] = array('data'=>to_quantity($row['items_received']), 'align'=> 'left');
				$summary_data_row[] = array('data'=>to_currency($row['subtotal']), 'align'=> 'right');
				$summary_data_row[] = array('data'=>to_currency($row['total']), 'align'=> 'right');
				$summary_data_row[] = array('data'=>to_currency($row['tax']), 'align'=> 'right');

				$summary_data_row[] = array('data'=>$row['payment_type'], 'align'=>'right');
				$summary_data_row[] = array('data'=>$row['comment'], 'align'=>'right');
				$summary_data[$key] = $summary_data_row;
				foreach($report_data['details'][$key] as $drow)
				{
					$details_data_row = array();
					$details_data_row[] =  array('data'=> $drow['item_number'], 'align'=>'left');
					$details_data_row[] = array('data'=> $drow['item_product_id'], 'align'=>'left');
					$details_data_row[] = array('data'=> $drow['item_name'], 'align'=> 'left');
					$details_data_row[] = array('data'=>$drow['category'], 'align'=> 'left');
					$details_data_row[] = array('data'=>to_quantity($drow['quantity_purchased']), 'align'=> 'left');
					$details_data_row[] = array('data'=>to_quantity($drow['quantity_received']), 'align'=> 'left');
					$details_data_row[] = array('data'=>to_currency($drow['subtotal']), 'align'=> 'right');
					$details_data_row[] = array('data'=>to_currency($drow['total']), 'align'=> 'right');
					$details_data_row[] = array('data'=>to_currency($drow['tax']), 'align'=> 'right');
					$details_data_row[] = array('data'=>$drow['discount_percent'].'%', 'align'=> 'left');

					$details_data[$key][] = $details_data_row;
				}	
			}

			$supplier_info = $this->Supplier->get_info($supplier_id);
			$data = array(
				"title" => $supplier_info->first_name .' '. $supplier_info->last_name.' '.lang('reports_recevings_report'),
				"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
				"headers" => $model->getDataColumns(),
				"summary_data" => $summary_data,
				"details_data" => $details_data,
				"overall_summary_data" => $model->getSummaryData(),
				"export_excel" => $export_excel,
				"pagination" => $this->pagination->create_links(),
			);

			$this->load->view("reports/tabular_details",$data);
		}

		function deleted_sales($start_date, $end_date, $sale_type, $export_excel=0, $offset = 0)
		{
			$this->load->model('Sale');
			$this->check_action_permission('view_deleted_sales');
			$start_date=rawurldecode($start_date);
			$end_date=rawurldecode($end_date);

			$this->load->model('reports/Deleted_sales');
			$model = $this->Deleted_sales;
			$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel' => $export_excel));

			$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
			$config = array();
			$config['base_url'] = site_url("reports/deleted_sales/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$sale_type/$export_excel");
			$config['total_rows'] = $model->getTotalRows();
			$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
			$config['uri_segment'] = 7;
			$this->load->library('pagination');$this->pagination->initialize($config);

			$headers = $model->getDataColumns();
			$report_data = $model->getData();

			$summary_data = array();
			$details_data = array();

			$location_count = count(Report::get_selected_location_ids());			

			foreach($report_data['summary'] as $key=>$row)
			{

				$summary_data_row = array();

				$summary_data_row[] = array('data'=>anchor('sales/receipt/'.$row['sale_id'], '<i class="ion-printer"></i>', array('target' => '_blank', 'class'=>'hidden-print')).'<span class="visible-print">'.$row['sale_id'].'</span>'.anchor('sales/edit/'.$row['sale_id'], '<i class="ion-document-text"></i>', array('target' => '_blank')).' '.anchor('sales/edit/'.$row['sale_id'], lang('common_edit').' '.$row['sale_id'], array('target' => '_blank','class'=>'hidden-print')), 'align'=>'left');

				if ($location_count > 1)
				{
					$summary_data_row[] = array('data'=>$row['location_name'], 'align'=>'left');
				}

				$summary_data_row[] = array('data'=>date(get_date_format().'-'.get_time_format(), strtotime($row['sale_time'])), 'align'=>'left');
				$summary_data_row[] = array('data'=>$row['register_name'], 'align'=>'left');
				$summary_data_row[] = array('data'=>to_quantity($row['items_purchased']), 'align'=>'left');
				$summary_data_row[] = array('data'=>$row['deleted_by'], 'align'=>'left');
				$summary_data_row[] = array('data'=>$row['employee_name'].($row['sold_by_employee'] && $row['sold_by_employee'] != $row['employee_name'] ? '/'. $row['sold_by_employee']: ''), 'align'=>'left');
				$summary_data_row[] = array('data'=>$row['customer_name'].(isset($row['account_number']) && $row['account_number'] ? ' ('.$row['account_number'].')' : ''), 'align'=>'left');
				$summary_data_row[] = array('data'=>to_currency($row['subtotal']), 'align'=>'right');
				$summary_data_row[] = array('data'=>to_currency($row['total']), 'align'=>'right');
				$summary_data_row[] = array('data'=>to_currency($row['tax']), 'align'=>'right');
				if($this->has_profit_permission)
				{
					$summary_data_row[] = array('data'=>to_currency($row['profit']), 'align'=>'right');
				}
				$summary_data_row[] = array('data'=>$row['payment_type'], 'align'=>'left');
				$summary_data_row[] = array('data'=>$row['comment'], 'align'=>'left');


				$summary_data[$key] = $summary_data_row;


				foreach($report_data['details'][$key] as $drow)
				{
					$details_data_row = array();
					$details_data_row[] = array('data'=>isset($drow['item_name']) ? $drow['item_name'] : $drow['item_kit_name'], 'align'=>'left');
					$details_data_row[] = array('data'=>$drow['category'], 'align'=>'left');
					$details_data_row[] = array('data'=>$drow['serialnumber'], 'align'=>'left');
					$details_data_row[] = array('data'=>$drow['description'], 'align'=>'left');
					$details_data_row[] = array('data'=>to_quantity($drow['quantity_purchased']), 'align'=>'left');
					$details_data_row[] = array('data'=>to_currency($drow['subtotal']), 'align'=>'right');
					$details_data_row[] = array('data'=>to_currency($drow['total']), 'align'=>'right');
					$details_data_row[] = array('data'=>to_currency($drow['tax']), 'align'=>'right');
					if($this->has_profit_permission)
					{
						$details_data_row[] = array('data'=>to_currency($drow['profit']), 'align'=>'right');
					}

					$details_data_row[] = array('data'=>$drow['discount_percent'].'%', 'align'=>'left');

					$details_data[$key][] = $details_data_row;
				}
			}

			$data = array(
				"title" =>lang('reports_deleted_sales_report'),
				"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
				"headers" => $model->getDataColumns(),
				"summary_data" => $summary_data,
				"details_data" => $details_data,
				"overall_summary_data" => $model->getSummaryData(),
				"export_excel" => $export_excel,
				'pagination' => $this->pagination->create_links(),
			);

			$this->load->view("reports/tabular_details",$data);
		}

		function detailed_suspended_receivings($start_date, $end_date, $supplier_id,$sale_type, $export_excel=0, $offset=0)
		{
			$this->load->model('Receiving');
			$this->check_action_permission('view_receivings');
			$start_date=rawurldecode($start_date);
			$end_date=rawurldecode($end_date);

			$this->load->model('reports/Detailed_receivings');
			$model = $this->Detailed_receivings;
			$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel' => $export_excel, 'supplier_id' => $supplier_id, 'force_suspended' => true));

			$this->Receiving->create_receivings_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'force_suspended' => true));
			$config = array();
			$config['base_url'] = site_url("reports/detailed_receivings/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$supplier_id/$sale_type/$export_excel");
			$config['total_rows'] = $model->getTotalRows();
			$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
			$config['uri_segment'] = 8;
			$this->load->library('pagination');$this->pagination->initialize($config);


			$headers = $model->getDataColumns();
			$report_data = $model->getData();

			$summary_data = array();
			$details_data = array();

			$location_count = count(Report::get_selected_location_ids());

			foreach($report_data['summary'] as $key=>$row)
			{
				$summary_data[$key] = array(array('data'=>anchor('receivings/receipt/'.$row['receiving_id'], '<i class="ion-printer"></i>', array('target' => '_blank')).' '.anchor('receivings/edit/'.$row['receiving_id'], '<i class="ion-document-text"></i>', array('target' => '_blank')).' '.anchor('receivings/edit/'.$row['receiving_id'], lang('common_edit').' '.$row['receiving_id'], array('target' => '_blank')).' ['.anchor('items/generate_barcodes_from_recv/'.$row['receiving_id'], lang('common_barcode_sheet'), array('target' => '_blank')).' / '.anchor('items/generate_barcodes_labels_from_recv/'.$row['receiving_id'], lang('common_barcode_labels'), array('target' => '_blank')).']', 'align'=> 'left'), array('data'=>date(get_date_format(), strtotime($row['receiving_date'])), 'align'=> 'left'), array('data'=>to_quantity($row['items_purchased']), 'align'=> 'left'),array('data'=>to_quantity($row['items_received']), 'align'=> 'left'), array('data'=>$row['employee_name'], 'align'=> 'left'), array('data'=>$row['supplier_name'], 'align'=> 'left'), array('data'=>to_currency($row['subtotal']), 'align'=> 'right'), array('data'=>to_currency($row['total']), 'align'=> 'right'),array('data'=>to_currency($row['tax']), 'align'=> 'right'), array('data'=>$row['payment_type'], 'align'=> 'left'), array('data'=>$row['comment'], 'align'=> 'left'));

				if ($location_count > 1)
				{
					array_unshift($summary_data[$key], array('data'=>$row['location_name'], 'align'=> 'left'));
				}

				foreach($report_data['details'][$key] as $drow)
				{
					$details_data[$key][] = array(array('data'=>$drow['name'], 'align'=> 'left'),array('data'=>$drow['product_id'], 'align'=> 'left'), array('data'=>$drow['category'], 'align'=> 'left'), array('data'=>$drow['size'], 'align'=> 'left'), array('data'=>to_quantity($drow['quantity_purchased']), 'align'=> 'left'),array('data'=>to_quantity($drow['quantity_received']), 'align'=> 'left'), array('data'=>to_currency($drow['subtotal']), 'align'=> 'right'), array('data'=>to_currency($drow['total']), 'align'=> 'right'),array('data'=>to_currency($drow['tax']), 'align'=> 'right'), array('data'=>$drow['discount_percent'].'%', 'align'=> 'left'));
				}
			}

			$data = array(
				"title" =>lang('reports_detailed_suspended_receivings_report'),
				"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
				"headers" => $model->getDataColumns(),
				"summary_data" => $summary_data,
				"details_data" => $details_data,
				"overall_summary_data" => $model->getSummaryData(),
				"export_excel" => $export_excel,
				"pagination" => $this->pagination->create_links(),
			);

			$this->load->view("reports/tabular_details",$data);
		}

		function detailed_receivings($start_date, $end_date, $supplier_id,$sale_type, $export_excel=0, $offset=0)
		{
			$this->load->model('Receiving');
			$this->check_action_permission('view_receivings');
			$start_date=rawurldecode($start_date);
			$end_date=rawurldecode($end_date);

			$this->load->model('reports/Detailed_receivings');
			$model = $this->Detailed_receivings;
			$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel' => $export_excel, 'supplier_id' => $supplier_id));

			$this->Receiving->create_receivings_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
			$config = array();
			$config['base_url'] = site_url("reports/detailed_receivings/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$supplier_id/$sale_type/$export_excel");
			$config['total_rows'] = $model->getTotalRows();
			$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
			$config['uri_segment'] = 8;
			$this->load->library('pagination');$this->pagination->initialize($config);


			$headers = $model->getDataColumns();
			$report_data = $model->getData();

			$summary_data = array();
			$details_data = array();
			$location_count = count(Report::get_selected_location_ids());

			foreach($report_data['summary'] as $key=>$row)
			{
				$summary_data[$key] = array(array('data'=>anchor('receivings/receipt/'.$row['receiving_id'], '<i class="ion-printer"></i>', array('target' => '_blank')).' '.anchor('receivings/edit/'.$row['receiving_id'], '<i class="ion-document-text"></i>', array('target' => '_blank')).' '.anchor('receivings/edit/'.$row['receiving_id'], lang('common_edit').' '.$row['receiving_id'], array('target' => '_blank')).' ['.anchor('items/generate_barcodes_from_recv/'.$row['receiving_id'], lang('common_barcode_sheet'), array('target' => '_blank')).' / '.anchor('items/generate_barcodes_labels_from_recv/'.$row['receiving_id'], lang('common_barcode_labels'), array('target' => '_blank')).']', 'align'=> 'left'), array('data'=>date(get_date_format(), strtotime($row['receiving_date'])), 'align'=> 'left'), array('data'=>to_quantity($row['items_purchased']), 'align'=> 'left'),array('data'=>to_quantity($row['items_received']), 'align'=> 'left'), array('data'=>$row['employee_name'], 'align'=> 'left'), array('data'=>$row['supplier_name'], 'align'=> 'left'), array('data'=>to_currency($row['subtotal']), 'align'=> 'right'), array('data'=>to_currency($row['total']), 'align'=> 'right'),array('data'=>to_currency($row['tax']), 'align'=> 'right'), array('data'=>$row['payment_type'], 'align'=> 'left'), array('data'=>$row['comment'], 'align'=> 'left'));

				if ($location_count > 1)
				{
					array_unshift($summary_data[$key], array('data'=>$row['location_name'], 'align'=> 'left'));
				}

				foreach($report_data['details'][$key] as $drow)
				{
					$details_data[$key][] = array(array('data'=>$drow['name'], 'align'=> 'left'),array('data'=>$drow['product_id'], 'align'=> 'left'), array('data'=>$drow['category'], 'align'=> 'left'), array('data'=>$drow['size'], 'align'=> 'left'), array('data'=>to_quantity($drow['quantity_purchased']), 'align'=> 'left'),array('data'=>to_quantity($drow['quantity_received']), 'align'=> 'left'), array('data'=>to_currency($drow['subtotal']), 'align'=> 'right'), array('data'=>to_currency($drow['total']), 'align'=> 'right'),array('data'=>to_currency($drow['tax']), 'align'=> 'right'), array('data'=>$drow['discount_percent'].'%', 'align'=> 'left'));
				}
			}

			$data = array(
				"title" =>lang('reports_detailed_receivings_report'),
				"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
				"headers" => $model->getDataColumns(),
				"summary_data" => $summary_data,
				"details_data" => $details_data,
				"overall_summary_data" => $model->getSummaryData(),
				"export_excel" => $export_excel,
				"pagination" => $this->pagination->create_links(),
			);

			$this->load->view("reports/tabular_details",$data);
		}

		function deleted_recevings($start_date, $end_date,$sale_type, $export_excel=0, $offset=0)
		{
			$this->load->model('Receiving');
			$this->check_action_permission('view_receivings');
			$start_date=rawurldecode($start_date);
			$end_date=rawurldecode($end_date);

			$this->load->model('reports/Deleted_receivings');
			$model = $this->Deleted_receivings;
			$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel' => $export_excel));

			$this->Receiving->create_receivings_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
			$config = array();
			$config['base_url'] = site_url("reports/deleted_recevings/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$sale_type/$export_excel");
			$config['total_rows'] = $model->getTotalRows();
			$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
			$config['uri_segment'] = 7;
			$this->load->library('pagination');$this->pagination->initialize($config);


			$headers = $model->getDataColumns();
			$report_data = $model->getData();

			$summary_data = array();
			$details_data = array();

			$location_count = count(Report::get_selected_location_ids());

			foreach($report_data['summary'] as $key=>$row)
			{
				$summary_data[$key] = array(array('data'=>anchor('receivings/receipt/'.$row['receiving_id'], '<i class="ion-printer"></i>', array('target' => '_blank')).' '.anchor('receivings/edit/'.$row['receiving_id'], '<i class="ion-document-text"></i>', array('target' => '_blank')).' '.anchor('receivings/edit/'.$row['receiving_id'], lang('common_edit').' '.$row['receiving_id'], array('target' => '_blank')).' ['.anchor('items/generate_barcodes_from_recv/'.$row['receiving_id'], lang('common_barcode_sheet'), array('target' => '_blank')).' / '.anchor('items/generate_barcodes_labels_from_recv/'.$row['receiving_id'], lang('common_barcode_labels'), array('target' => '_blank')).']', 'align'=> 'left'), array('data'=>date(get_date_format(), strtotime($row['receiving_date'])), 'align'=> 'left'), array('data'=>to_quantity($row['items_purchased']), 'align'=> 'left'),array('data'=>to_quantity($row['items_received']), 'align'=> 'left'), array('data'=>$row['employee_name'], 'align'=> 'left'), array('data'=>$row['supplier_name'], 'align'=> 'left'), array('data'=>to_currency($row['subtotal']), 'align'=> 'right'), array('data'=>to_currency($row['total']), 'align'=> 'right'),array('data'=>to_currency($row['tax']), 'align'=> 'right'), array('data'=>$row['payment_type'], 'align'=> 'left'), array('data'=>$row['comment'], 'align'=> 'left'));

				if ($location_count > 1)
				{
					array_unshift($summary_data[$key], array('data'=>$row['location_name'], 'align'=> 'left'));
				}


				foreach($report_data['details'][$key] as $drow)
				{
					$details_data[$key][] = array(array('data'=>$drow['name'], 'align'=> 'left'),array('data'=>$drow['product_id'], 'align'=> 'left'), array('data'=>$drow['category'], 'align'=> 'left'), array('data'=>$drow['size'], 'align'=> 'left'), array('data'=>to_quantity($drow['quantity_purchased']), 'align'=> 'left'),array('data'=>to_quantity($drow['quantity_received']), 'align'=> 'left'), array('data'=>to_currency($drow['subtotal']), 'align'=> 'right'), array('data'=>to_currency($drow['total']), 'align'=> 'right'),array('data'=>to_currency($drow['tax']), 'align'=> 'right'), array('data'=>$drow['discount_percent'].'%', 'align'=> 'left'));
				}
			}

			$data = array(
				"title" =>lang('reports_deleted_recv_reports'),
				"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
				"headers" => $model->getDataColumns(),
				"summary_data" => $summary_data,
				"details_data" => $details_data,
				"overall_summary_data" => $model->getSummaryData(),
				"export_excel" => $export_excel,
				"pagination" => $this->pagination->create_links(),
			);

			$this->load->view("reports/tabular_details",$data);
		}

		function excel_export()
		{
			$this->load->view("reports/excel_export",array());
		}

		function inventory_input()
		{
			$this->load->model('Category');
			$this->load->model('Supplier');
			$data = $this->_get_common_report_data(TRUE);
			$data['specific_input_name'] = lang('reports_supplier');

			$suppliers = array();

			$suppliers[-1] = lang('common_all');
			foreach($this->Supplier->get_all()->result() as $supplier)
			{
				$suppliers[$supplier->person_id] = $supplier->company_name. ' ('.$supplier->first_name .' '.$supplier->last_name.')';
			}

			$data['categories'] = array();
			$data['categories'][-1] =lang('common_all');

			$categories = $this->Category->sort_categories_and_sub_categories($this->Category->get_all_categories_and_sub_categories());

			foreach($categories as $key=>$value)
			{
				$name = str_repeat('&nbsp;&nbsp;', $value['depth']).$value['name'];
				$data['categories'][$key] = $name;
			}

			$data['specific_input_data'] = $suppliers;
			$data['category_data'] = $categories;
			$locations = array();
			foreach($this->Location->get_all()->result() as $location_row) 
			{
				$locations[$location_row->location_id] = $location_row->name;
			}
			$data['locations'] = $locations;

			$data['can_view_inventory_at_all_locations'] = $this->Employee->has_module_action_permission('reports','view_inventory_at_all_locations', $this->Employee->get_logged_in_employee_info()->person_id);

			$this->load->view("reports/inventory_input",$data);
		}

		function inventory_low($supplier = -1, $category_id = -1, $inventory = 'all', $reorder_only = 0, $export_excel=0, $offset=0)
		{
			$category_id = rawurldecode($category_id);

			$this->check_action_permission('view_inventory_reports');
			$this->load->model('reports/Inventory_low');
			$model = $this->Inventory_low;
			$model->setParams(array('supplier'=>$supplier,'category_id' => $category_id, 'export_excel' => $export_excel, 'offset'=>$offset, 'inventory' => $inventory, 'reorder_only' => $reorder_only));

			$config = array();
			$config['base_url'] = site_url("reports/inventory_low/$supplier/$category_id/$inventory/$reorder_only/export_excel");
			$config['total_rows'] = $model->getTotalRows();
			$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
			$config['uri_segment'] = 8;
			$this->load->library('pagination');$this->pagination->initialize($config);

			$tabular_data = array();
			$report_data = $model->getData();
			$location_count = count(Report::get_selected_location_ids());

			foreach($report_data as $row)
			{
				$data_row = array();


				if ($location_count > 1)
				{
					$data_row[] = array('data'=>$row['location_name'], 'align' => 'left');
				}
				$data_row[] = array('data'=>$row['item_id'], 'align' => 'left');
				$data_row[] = array('data'=>$row['name'], 'align' => 'left');
				$data_row[] = array('data'=>$row['category'], 'align'=> 'left');
				$data_row[] = array('data'=>$row['company_name'], 'align'=> 'left');
				$data_row[] = array('data'=>$row['item_number'], 'align'=> 'left');
				$data_row[] = array('data'=>$row['product_id'], 'align'=> 'left');
				$data_row[] = array('data'=>$row['description'], 'align'=> 'left');
				$data_row[] = array('data'=>$row['size'], 'align'=> 'left');
				$data_row[] = array('data'=>$row['location'], 'align'=> 'left');

				if($this->has_cost_price_permission)
				{
					$data_row[] = array('data'=>to_currency($row['cost_price']), 'align'=> 'right');
				}
				$data_row[] = array('data'=>to_currency($row['unit_price']), 'align'=> 'right');
				$data_row[] = array('data'=>to_quantity($row['quantity']), 'align'=> 'left');
				$data_row[] = array('data'=>to_quantity($row['reorder_level']), 'align'=> 'left');

				$tabular_data[] = $data_row;				

			}

			$data = array(
				"title" => lang('reports_low_inventory_report'),
				"subtitle" => '',
				"headers" => $model->getDataColumns(),
				"data" => $tabular_data,
				"summary_data" => $model->getSummaryData(),
				"export_excel" => $export_excel,
				"pagination" => $this->pagination->create_links(),
			);

			$this->load->view("reports/tabular",$data);
		}

		function inventory_summary($supplier = -1, $category_id = -1, $inventory = 'all', $show_only_pending = 0 ,$export_excel=0, $offset = 0)
		{
			$category_id = rawurldecode($category_id);

			$this->check_action_permission('view_inventory_reports');
			$this->load->model('reports/Inventory_summary');
			$model = $this->Inventory_summary;
			$model->setParams(array('supplier'=>$supplier,'category_id' => $category_id, 'export_excel' => $export_excel, 'offset'=>$offset, 'inventory' => $inventory,'show_only_pending' => $show_only_pending));

			$config = array();
			$config['base_url'] = site_url("reports/inventory_summary/$supplier/$category_id/$inventory/$show_only_pending/$export_excel");
			$config['total_rows'] = $model->getTotalRows();
			$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
			$config['uri_segment'] = 8;
			$this->load->library('pagination');$this->pagination->initialize($config);


			$tabular_data = array();
			$report_data = $model->getData();
			foreach($report_data as $row)
			{
				$data_row = array();

				$data_row[] = array('data'=>$row['item_id'], 'align' => 'left');			
				$data_row[] = array('data'=>$row['name'], 'align' => 'left');
				$data_row[] = array('data'=>$row['category'], 'align'=> 'left');
				$data_row[] = array('data'=>$row['company_name'], 'align'=> 'left');
				$data_row[] = array('data'=>$row['item_number'], 'align'=> 'left');
				$data_row[] = array('data'=>$row['product_id'], 'align'=> 'left');
				$data_row[] = array('data'=>$row['description'], 'align'=> 'left');
				$data_row[] = array('data'=>$row['size'], 'align'=> 'left');
				$data_row[] = array('data'=>$row['location'], 'align'=> 'left');
				if($this->has_cost_price_permission)
				{
					$data_row[] = array('data'=>to_currency($row['cost_price']), 'align'=> 'right');
				}
				$data_row[] = array('data'=>to_currency($row['unit_price']), 'align'=> 'right');
				$data_row[] = array('data'=>to_quantity($row['quantity']), 'align'=> 'left');
				$data_row[] = array('data'=>to_quantity($row['pending_inventory']), 'align'=> 'left');
				$data_row[] = array('data'=>to_quantity($row['reorder_level']), 'align'=> 'left');

				$tabular_data[] = $data_row;				

			}

			$data = array(
				"title" => lang('reports_inventory_summary_report'),
				"subtitle" => '',
				"headers" => $model->getDataColumns(),
				"data" => $tabular_data,
				"summary_data" => $model->getSummaryData(),
				"export_excel" => $export_excel,
				"pagination" => $this->pagination->create_links(),
			);

			$this->load->view("reports/tabular",$data);
		}

		function summary_giftcards($export_excel = 0, $offset = 0)
		{
			$this->check_action_permission('view_giftcards');
			$this->load->model('reports/Summary_giftcards');
			$model = $this->Summary_giftcards;
			$model->setParams(array('export_excel' => $export_excel, 'offset' => $offset));
			$config = array();
			$config['base_url'] = site_url("reports/summary_giftcards/$export_excel");
			$config['total_rows'] = $model->getTotalRows();
			$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
			$config['uri_segment'] = 4;
			$this->load->library('pagination');$this->pagination->initialize($config);

			$tabular_data = array();
			$report_data = $model->getData();
			foreach($report_data as $row)
			{
				$tabular_data[] = array(array('data'=>$row['giftcard_number'], 'align'=> 'left'), array('data'=>$row['description'], 'align'=> 'left'),array('data'=>to_currency($row['value']), 'align'=> 'left'), array('data'=>$row['customer_name'].(isset($row['account_number']) && $row['account_number'] ? ' ('.$row['account_number'].')' : ''), 'align'=> 'left'));
			}

			$data = array(
				"title" => lang('reports_giftcard_summary_report'),
				"subtitle" => '',
				"headers" => $model->getDataColumns(),
				"data" => $tabular_data,
				"summary_data" => $model->getSummaryData(),
				"export_excel" => $export_excel,
				"pagination" => $this->pagination->create_links(),
			);

			$this->load->view("reports/tabular",$data);
		}

		function summary_giftcard_sales($start_date, $end_date, $sale_type, $export_excel=0, $offset=0)
		{
			$this->check_action_permission('view_giftcards');
			$start_date=rawurldecode($start_date);
			$end_date=rawurldecode($end_date);

			$this->load->model('reports/Summary_giftcards_sales');
			$model = $this->Summary_giftcards_sales;
			$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel' => $export_excel));

			$config = array();
			$config['base_url'] = site_url("reports/summary_giftcard_sales/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$sale_type/$export_excel");
			$config['total_rows'] = $model->getTotalRows();
			$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
			$config['uri_segment'] = 7;
			$this->load->library('pagination');$this->pagination->initialize($config);

			$tabular_data = array();
			$report_data = $model->getData();
			$location_count = count(Report::get_selected_location_ids());

			foreach($report_data as $row)
			{
				$data_row = array();

				if ($location_count > 1)
				{
					$data_row[] = array('data'=>$row['location_name'], 'align'=> 'left');				
				}

				$data_row[] = array('data'=>date(get_date_format(), strtotime($row['sale_time'])), 'align'=>'left');
				$data_row[] = array('data'=>$row['giftcard_number'], 'align'=> 'left');
				$data_row[] = array('data'=>$row['customer_name'].(isset($row['account_number']) && $row['account_number'] ? ' ('.$row['account_number'].')' : ''), 'align'=> 'left');
				$data_row[] = array('data'=>to_currency($row['gift_card_sale_price']), 'align'=>'left');	

				$tabular_data[] = $data_row;
			}
			$data = array(
				"title" => lang('reports_gift_card_sales_reports'),
				"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
				"headers" => $model->getDataColumns(),
				"data" => $tabular_data,
				"summary_data" => $model->getSummaryData(),
				"export_excel" => $export_excel,
				"pagination" => $this->pagination->create_links()
			);

			$this->load->view("reports/tabular",$data);
		}

		function excel_export_store_account_summary_input()
		{
			$this->load->view("reports/excel_export_store_account_summary_input",array());
		}
		
		function summary_store_accounts($show_accounts_over_credit_limit, $export_excel = 0, $offset=0)
		{
			$this->check_action_permission('view_store_account');
			$this->load->model('reports/Summary_store_accounts');
			$model = $this->Summary_store_accounts;
			$model->setParams(array('show_accounts_over_credit_limit' => $show_accounts_over_credit_limit, 'export_excel' => $export_excel, 'offset' => $offset));

			$config = array();
			$config['base_url'] = site_url("reports/summary_store_accounts/$show_accounts_over_credit_limit/$export_excel");
			$config['total_rows'] = $model->getTotalRows();
			$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
			$config['uri_segment'] = 5;
			$this->load->library('pagination');$this->pagination->initialize($config);

			$tabular_data = array();
			$report_data = $model->getData();
			foreach($report_data as $row)
			{
				$tabular_data[] = array(array('data'=>$row['customer'], 'align'=> 'left'), array('data'=>$row['credit_limit'] ? to_currency($row['credit_limit']) : lang('common_not_set'), 'align'=> 'right'), array('data'=>to_currency($row['balance']), 'align'=> 'right'), array('data'=>anchor("customers/pay_now/".$row['person_id'],lang('common_pay'),array('title'=>lang('common_update'),'class'=>'btn btn-info')), 'align'=> 'right'));
			}

			$data = array(
				"title" => lang('reports_store_account_summary_report'),
				"subtitle" => '',
				"headers" => $model->getDataColumns(),
				"data" => $tabular_data,
				"summary_data" => $model->getSummaryData(),
				"export_excel" => $export_excel,
				'pagination' => $this->pagination->create_links()
			);

			$this->load->view("reports/tabular",$data);
		}

		function detailed_giftcards_input()
		{
			$data['specific_input_name'] = lang('reports_customer');
			$data['search_suggestion_url'] = site_url('reports/customer_search');
			$this->load->view("reports/detailed_giftcards_input",$data);
		}

		function detailed_giftcards($customer_id, $giftcard_number, $export_excel = 0, $offset=0)
		{
			$this->load->model('Sale');
			$this->load->model('Customer');
			$this->check_action_permission('view_giftcards');
			$this->load->model('reports/Detailed_giftcards');
			$model = $this->Detailed_giftcards;
			$model->setParams(array('customer_id' =>$customer_id, 'giftcard_number' => $giftcard_number, 'offset' => $offset, 'export_excel' => $export_excel));

			$this->Sale->create_sales_items_temp_table(array('customer_id' =>$customer_id));

			$config = array();
			$config['base_url'] = site_url("reports/detailed_giftcards/$customer_id/$giftcard_number/$export_excel");
			$config['total_rows'] = $model->getTotalRows();
			$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
			$config['uri_segment'] = 6;
			$this->load->library('pagination');$this->pagination->initialize($config);

			$headers = $model->getDataColumns();
			$report_data = $model->getData();

			$summary_data = array();
			$details_data = array();

			foreach($report_data['summary'] as $key=>$row)
			{
				$summary_data_row = array();

				$summary_data_row[] = array('data'=>anchor('sales/receipt/'.$row['sale_id'], '<i class="ion-printer"></i>', array('target' => '_blank')).' '.anchor('sales/edit/'.$row['sale_id'], '<i class="ion-document-text"></i>', array('target' => '_blank')).' '.anchor('sales/edit/'.$row['sale_id'], lang('common_edit').' '.$row['sale_id'], array('target' => '_blank')), 'align'=>'left');
				$summary_data_row[] = array('data'=>date(get_date_format().'-'.get_time_format(), strtotime($row['sale_time'])), 'align'=> 'left');
				$summary_data_row[] = array('data'=>$row['register_name'], 'align'=>'left');
				$summary_data_row[] = array('data'=>to_quantity($row['items_purchased']), 'align'=> 'left');
				$summary_data_row[] = array('data'=>$row['customer_name'].(isset($row['account_number']) && $row['account_number'] ? ' ('.$row['account_number'].')' : ''), 'align'=> 'left');
				$summary_data_row[] = array('data'=>to_currency($row['subtotal']), 'align'=> 'right');
				$summary_data_row[] = array('data'=>to_currency($row['total']), 'align'=> 'right');
				$summary_data_row[] = array('data'=>to_currency($row['tax']), 'align'=> 'right');

				if($this->has_profit_permission)
				{
					$summary_data_row[] = array('data'=>to_currency($row['profit']), 'align'=>'right');
				}

				$summary_data_row[] = array('data'=>$row['payment_type'], 'align'=>'right');
				$summary_data_row[] = array('data'=>$row['comment'], 'align'=>'right');
				$summary_data[$key] = $summary_data_row;

				foreach($report_data['details'][$key] as $drow)
				{
					$details_data_row = array();

					$details_data_row[] = array('data'=>isset($drow['item_number']) ? $drow['item_number'] : $drow['item_kit_number'], 'align'=>'left');
					$details_data_row[] = array('data'=>isset($drow['item_name']) ? $drow['item_name'] : $drow['item_kit_name'], 'align'=>'left');
					$details_data_row[] = array('data'=>$drow['category'], 'align'=>'left');
					$details_data_row[] = array('data'=>$drow['serialnumber'], 'align'=>'left');;
					$details_data_row[] = array('data'=>$drow['description'], 'align'=>'left');
					$details_data_row[] = array('data'=>to_quantity($drow['quantity_purchased']), 'align'=>'left');
					$details_data_row[] = array('data'=>to_currency($drow['subtotal']), 'align'=>'right');
					$details_data_row[] = array('data'=>to_currency($drow['total']), 'align'=>'right');
					$details_data_row[] = array('data'=>to_currency($drow['tax']), 'align'=>'right');

					if($this->has_profit_permission)
					{
						$details_data_row[] = array('data'=>to_currency($drow['profit']), 'align'=>'right');					
					}

					$details_data_row[] = array('data'=>$drow['discount_percent'].'%', 'align'=>'left');
					$details_data[$key][] = $details_data_row;
				}
			}
			$customer_info = $this->Customer->get_info($customer_id);
			$data = array(
				"title" => $customer_info->first_name .' '. $customer_info->last_name.' '.lang('reports_giftcard'). ' '.lang('reports_report'),
				"subtitle" => '',
				"headers" => $model->getDataColumns(),
				"summary_data" => $summary_data,
				"details_data" => $details_data,
				"overall_summary_data" => $model->getSummaryData(),
				"export_excel" => $export_excel,
				"pagination" => $this->pagination->create_links(),
			);

			$this->load->view("reports/tabular_details",$data);
		}

		function date_input_profit_and_loss()
		{
			$data = $this->_get_common_report_data();
			$this->load->view("reports/date_input_profit_and_loss",$data);	
		}

		function detailed_profit_and_loss($start_date, $end_date)
		{
			$this->load->model('Sale');
			$this->load->model('Receiving');
			$this->check_action_permission('view_profit_and_loss');
			$this->load->model('reports/Detailed_profit_and_loss');
			$model = $this->Detailed_profit_and_loss;
			$end_date=date('Y-m-d 23:59:59', strtotime($end_date));

			$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date));

			$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date));
			$this->Receiving->create_receivings_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date));

			$data = array(
				"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
				"details_data" => $model->getData(),
				"overall_summary_data" => $model->getSummaryData(),
			);

			$this->load->view("reports/profit_and_loss_details",$data);
		}

		function summary_profit_and_loss($start_date, $end_date)
		{
			$this->load->model('Sale');
			$this->load->model('Receiving');

			$this->check_action_permission('view_profit_and_loss');
			$this->load->model('reports/Summary_profit_and_loss');
			$model = $this->Summary_profit_and_loss;
			$end_date=date('Y-m-d 23:59:59', strtotime($end_date));

			$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date));

			$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date));
			$this->Receiving->create_receivings_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date));

			$data = array(
				"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
				"details_data" => $model->getData(),
				"overall_summary_data" => $model->getSummaryData(),
			);

			$this->load->view("reports/profit_and_loss_summary",$data);
		}

		function detailed_inventory_input()
		{
			$data = $this->_get_common_report_data(TRUE);

			$data['specific_input_name'] = lang('common_item');
			$data['search_suggestion_url'] = site_url('reports/item_search');

			$locations = array();
			foreach($this->Location->get_all()->result() as $location_row) 
			{
				$locations[$location_row->location_id] = $location_row->name;
			}
			$data['locations'] = $locations;

			$data['can_view_inventory_at_all_locations'] = $this->Employee->has_module_action_permission('reports','view_inventory_at_all_locations', $this->Employee->get_logged_in_employee_info()->person_id);

			$this->load->view("reports/detailed_inventory_input",$data);	
		}



	/*
	*--------------------------------------------------------------------------------
	*                                  SUMMARY COMMISSION
	*--------------------------------------------------------------------------------
	*/
	
	
	
	/* 
  *  Summary commission
	*  Input values for showing report
	*
	*  @return void
	*/
	function summary_commission() {
		$this->check_action_permission('view_commissions');
		$data = array();

		$data['action']      = $this->uri->segment(2);
		$data['locations'] = $this->Location->list_item();
		$data['title'] 	   = 'Báo cáo tổng hợp hoa hồng';
		if(isset($_SESSION['summary_commission'])) {
			$data['filter']    = $_SESSION['summary_commission'];
			$data['url_print'] = $_SESSION['summary_commission']['url_print'];

			unset($_SESSION['summary_commission']);
			if($data['filter']['export_excel'] == 1) {
				redirect($data['url_print']);
			}
			else
			{
				$this->load->view("reports/summary_commissions",$data);
			}

		}else {
			$data['inputs']   = array('input_date_range', 'select_multi_group_of_location','input_locations', 'export_excel_yes');
			$data['no_excel'] = false;
			$this->load->view("reports/n9_tabular",$data);
		}
	}

 /* 
  *  Summary commission store
	*  The provision of ajax requested information 
	*
	*  @return void
	*/
	function summary_commission_store() {
		$post  = $this->input->post();
		$arrParam = array_merge($post, $this->input->get());
		$employees_condition ='';
		$time_condition = array();
		if(!empty($arrParam['start_date'])) {
			$time_condition[] =  's.sale_time >= \''.$arrParam['start_date'].'\'';
		}

		if(!empty($arrParam['end_date'])) {
			$time_condition[] =  's.sale_time <= \''.$arrParam['end_date'].'\'';
		}

		if(!empty($time_condition)) {
			$time_condition = ' AND ' . implode(' AND ', $time_condition);
		}else
		$time_condition = '';

		$location_ids = $arrParam['location_ids'];
		if(!empty($arrParam['group_ids'])) {
			$group_ids = $arrParam['group_ids'];
			$group_ids_condition = ' AND s.sale_id IN (SELECT sale_id FROM phppos_sales_employees WHERE group_id IN ('.$group_ids.'))';
		}

		if(!empty($arrParam['employees'])) {
			$employees = $arrParam['employees'];
			$employees_condition = ' AND s.sale_id IN (SELECT sale_id FROM phppos_sales_employees WHERE employee_id IN ('.$employees.'))';
		}

		$where = "s.suspended = 0 AND s.store_account_payment = 0 $time_condition AND s.location_id IN ($location_ids) AND s.deleted = 0 $group_ids_condition $employees_condition";
		$this->Sale->create_sales_items_temp_table_n9(array('where'=>$where));

		$this->load->model('reports/Summary_sales');
		$sale_model = $this->Summary_sales;
		$arrParam['page']           =  $this->uri->segment(3, 1);
		$config['base_url']         = base_url() . 'reports/summary_commissions_store';
		$config['total_rows']       = $sale_model->count_item($arrParam);

		$config['per_page']         = $arrParam['paginator']['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;

		$config['uri_segment']      = 3;
		$config['use_page_numbers'] = TRUE;
		$options['task'] = 'commission';


		$this->load->library("pagination");
		$this->pagination->initialize($config);
		$this->pagination->createConfig('front-end');

		$pagination = $this->pagination->create_ajax();

		$total_list_tmp = $sale_model->get_total_purchase($arrParam);

		$total_list = array(
			'detail_commissions_order_total'   => $total_list_tmp['order_value'],
			'detail_commissions_profit'        => $total_list_tmp['profit'],
			'detail_commissions_value'         => $total_list_tmp['commission'],
			'detail_commissions_profit_before' => to_currency(convert_number($total_list_tmp['profit'])+convert_number($total_list_tmp['commission']))
		);
		$result_tmp = $sale_model->list_item($arrParam,$options);
		$items = $sale_ids = array();
		foreach($result_tmp as $val) {
			$sale_id            = $val['sale_id'];
			if($val['commission_status'] == 1)
				$sale_ids[]         = $sale_id;

			$sale_full_ids[]          = $sale_id;

			$val['profit']      =  $val['order_value'] + $val['thu_value'] - $val['chi_value'] - $val['cost_value'] - $val['commission'] - $val['point_payment'] - $val['gift_card_payment'];
			$items[$sale_id]    =  $val;
			$items[$sale_id]['profit_before_charging_commission'] = $val['order_value'] + $val['thu_value'] - $val['chi_value'] - $val['cost_value'] - $val['point_payment'] - $val['gift_card_payment'];
			$items[$sale_id]['income'] = $val['order_value'];             
		}

        // danh sách hoa hồng
		$sale_commission = $sale_commission_by_employee = array();
		$employee_ids = array();
		if(!empty($sale_ids)) {
			$this->db -> select('*')
			-> from('sales_commission')
			-> where('sale_id IN ('.implode(', ', $sale_ids).')');
			$query = $this->db->get();
			$result_tmp = $query->result_array();

			$this->db->flush_cache();
			$data['count_colspan_commission'] = 1;
			if(!empty($result_tmp)) {
				foreach($result_tmp as $val) {
					if(in_array($val['group_id'], explode(',',$arrParam['group_ids']))) {
						$sale_id     = $val['sale_id'];
						$group_id    = $val['group_id'];
						$employee_id = $val['employee_id'];

						$sale_commission[$sale_id.'-'.$group_id.'-'.$employee_id] = $val['commission'];

						if(isset($group[$group_id])) {
							if(!in_array($employee_id, $group[$group_id])) {   
								$group[$group_id][] = $employee_id;
							}      
						} else  {
							$group[$group_id][] = $employee_id;
						}
						$employee_ids[]     = $employee_id;
					}
				}
			}
			$count_comission_in_group = [];
			$count_all_comission_all_group = 0;						
			foreach($group as $group_id => $_employee_ids)
			{
				$count_comission_in_group[$group_id] = count($_employee_ids);
				$count_all_comission_all_group += count($_employee_ids);
			}
			$data['STT']                                  = ($arrParam['page'] -1)*$config['per_page']+1;
			$data['sale_commission']                      = $sale_commission;
			$data['sale_full_ids']                        = $sale_full_ids;
			$data['items']                                = $items;
			$data['group']                                = $group;

			$data['count_comission_in_group']             = $count_comission_in_group;
			$data['count_colspan_commission_by_employee'] = count($employee_ids)+1;
			$data['colspan_comission']                    = $count_all_comission_all_group +	$data['count_colspan_commission_by_employee'];



			$this->db -> select('SUM(commission) AS sum_commission, employee_id, sale_id')
			-> from('sales_commission')
			-> where('sale_id IN ('.implode(', ', $sale_ids).')')
			-> group_by('employee_id')
			-> group_by('sale_id');

			$query = $this->db->get();
			$result_tmp = $query->result_array();

			$this->db->flush_cache();
			if(!empty($result_tmp)) {
				foreach($result_tmp as $val)
					$sale_commission_by_employee[$val['sale_id'] . '-' . $val['employee_id']] = $val['sum_commission'];
			}
		}

		$group_ids    = explode(',', $group_ids);
		$employee_ids = array_unique($employee_ids);
		$data['employee_ids']                = $employee_ids;
		$data['sale_commission_by_employee'] =  $sale_commission_by_employee;
		$data['group_list']    = $this->Group->get_items(array('cid'=>$group_ids, 'include_deleted'=>true));
		$data['employee_list'] = $this->Employee->get_info_by_ids($employee_ids);
		$html_string = $this->load->view('reports/row/summary_commissions',$data,true);
		$result = array('total_list'=> $total_list, 'html_string'=>$html_string, 'pagination'=>$pagination);
		echo json_encode($result);
	}


	function summary_commission_excel() {
		$unit = 1000;
		$number_format = PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1;
		$employees_condition ='';
		$arrParam = $this->input->get();
		$time_condition = array();
		if(!empty($arrParam['start_date'])) {
			$time_condition[] =  's.sale_time >= \''.$arrParam['start_date'].'\'';
		}

		if(!empty($arrParam['end_date'])) {
			$time_condition[] =  's.sale_time <= \''.$arrParam['end_date'].'\'';
		}

		if(!empty($time_condition)) {
			$time_condition = ' AND ' . implode(' AND ', $time_condition);
		}else
		$time_condition = '';

		$location_ids = $arrParam['location_ids'];
		if(!empty($arrParam['group_ids'])) {
			$group_ids = $arrParam['group_ids'];
			$group_ids_condition = ' AND s.sale_id IN (SELECT sale_id FROM phppos_sales_employees WHERE group_id IN ('.$group_ids.'))';
		}

		if(!empty($arrParam['employees'])) {
			$employees = $arrParam['employees'];
			$employees_condition = ' AND s.sale_id IN (SELECT sale_id FROM phppos_sales_employees WHERE employee_id IN ('.$employees.'))';
		}

		$where = "s.suspended = 0 AND s.store_account_payment = 0 $time_condition AND s.location_id IN ($location_ids) AND s.deleted = 0 $group_ids_condition $employees_condition";
		$this->Sale->create_sales_items_temp_table_n9(array('where'=>$where));

		$this->load->model('reports/Summary_sales');
		$sale_model = $this->Summary_sales;

		$result_tmp = $sale_model->list_item($arrParam,array('task' => 'commission'));
		if(empty($result_tmp)){ echo 'Không có đơn hàng nào'; exit(); }

        // danh sách hóa đơn
		$items = $sale_ids = array();
		foreach($result_tmp as $val) {
			$sale_id            = $val['sale_id'];
			if($val['commission_status'] == 1)
				$sale_ids[]         = $sale_id;

			$sale_full_ids[]          = $sale_id;
			$val['profit']      =  $val['order_value'] + $val['thu_value'] - $val['chi_value'] - $val['cost_value'] - $val['commission'] - $val['point_payment'] - $val['gift_card_payment'];
			$items[$sale_id]    =  $val;
			$incomeAndProfitBeforeCharingCommission[$sale_id] = array('profit_before_charging_commission' => $val['order_value'] + $val['thu_value'] - $val['chi_value'] - $val['cost_value'] - $val['point_payment'] - $val['gift_card_payment'], 'income' =>$val['order_value'] );

		}

		if(empty($sale_ids)) { echo 'Không có đơn hàng được tính hoa hồng'; exit();}

        // danh sách hoa hồng
		$sale_commission = $sale_commission_by_employee = array();
		if(!empty($sale_ids)) {
			$this->db -> select('*')
			-> from('sales_commission')
			-> where('sale_id IN ('.implode(', ', $sale_ids).')');

			$query = $this->db->get();
			$result_tmp = $query->result_array();

			$this->db->flush_cache();

			if(!empty($result_tmp)) {
				foreach($result_tmp as $val) {
					if(in_array($val['group_id'], explode(',',$arrParam['group_ids'])))
					{
						$sale_id     = $val['sale_id'];
						$group_id    = $val['group_id'];
						$employee_id = $val['employee_id'];

						$sale_commission[$sale_id.'-'.$group_id.'-'.$employee_id] = $val['commission'];

						if(isset($group[$group_id])) {
							if(!in_array($employee_id, $group[$group_id]))
								$group[$group_id][] = $employee_id;
						}else
						$group[$group_id][] = $employee_id;

						$employee_ids[]     = $employee_id;
					}
				}
			}		
			$this->db-> select('SUM(commission) AS sum_commission, employee_id, sale_id')
			-> from('sales_commission')
			-> where('sale_id IN ('.implode(', ', $sale_ids).')')
			-> group_by('employee_id')
			-> group_by('sale_id');

			$query = $this->db->get();
			$result_tmp = $query->result_array();

			$this->db->flush_cache();
			if(!empty($result_tmp)) {
				foreach($result_tmp as $val)
					$sale_commission_by_employee[$val['sale_id'] . '-' . $val['employee_id']] = $val['sum_commission'];
			}
		}

		$group_ids    = explode(',', $group_ids);
		$employee_ids = array_unique($employee_ids);

		$group_list    = $this->Group->get_items(array('cid'=>$group_ids, 'include_deleted'=>true));
		$employee_list = $this->Employee->get_info_by_ids($employee_ids);

        // excel begin
		$this->load->helper('n9excel');
		require_once (APPPATH.'libraries/PHPExcel/PHPExcel.php');
		require_once (APPPATH.'libraries/PHPExcel/PHPExcel/IOFactory.php');
		require_once (APPPATH.'libraries/PHPExcel/PHPEXCHelper.php');

		$helpExport = new HelpFuncExportExcel ();
		$objReader = PHPExcel_IOFactory::createReader ( "Excel5" );
		$colIndex = '';
		$rowIndex = 0;
		$objPHPExcel = new PHPExcel ();
		$sheet = $objPHPExcel->getActiveSheet ();

		$sheet->getColumnDimension ( 'A' )->setWidth ( 7 );
		$sheet->getColumnDimension ( 'B' )->setWidth ( 18 );
		$sheet->getColumnDimension ( 'C' )->setWidth ( 13 );
		$sheet->getColumnDimension ( 'D' )->setWidth ( 18 );
		$sheet->getColumnDimension ( 'E' )->setWidth ( 18 );                             

		$sheet->mergeCellsByColumnAndRow(0,4,0,6);
		$sheet->setCellValue('A4', 'STT');

		$sheet->mergeCellsByColumnAndRow(1,4,1,6);
		$sheet->setCellValue('B4', 'Code');

		$sheet->mergeCellsByColumnAndRow(2,4,2,6);
		$sheet->setCellValue('C4', 'Ngày');

		$sheet->mergeCellsByColumnAndRow(3,4,3,6);
		$sheet->setCellValue('D4', 'Doanh thu');

		$sheet->mergeCellsByColumnAndRow(4,4,4,6);
		$sheet->setCellValue('E4', 'Lợi nhuận trước hoa hồng');                    

		$sheet->getRowDimension(4)->setRowHeight(15);
		$sheet->getRowDimension(5)->setRowHeight(15);
		$sheet->getRowDimension(6)->setRowHeight(15);

		$helpExport->setStyle_11_A_B_C_T($sheet, 'A4', 'E4');

		$sheet->getStyle ( 'A4' . ':' . 'E6' )->getBorders ()->getOutline ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );
		$sheet->getStyle ( 'A4' . ':' . 'E6' )->getBorders ()->getInside ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );

		$row_start = 8;

		$sheet->freezePane( "E7" );

		$i = $row_start;
		$j=1;

		foreach($items as $key => $val) {
			$date = explode(' ', $val['sale_time_format']);
			$sheet->setCellValue('A'.$i, $j);
			$helpExport->setStyle_11_A_N_C_T($sheet, 'A'.$i, 'A'.$i);

			$sheet->setCellValue('B'.$i, $val['code']);
			$helpExport->setStyle_11_A_N_L_T($sheet, 'B'.$i, 'B'.$i);

			$sheet->setCellValue('C'.$i, $date[0]);
			$helpExport->setStyle_11_A_N_C_T($sheet, 'C'.$i, 'C'.$i);

			$sheet->setCellValue('D'.$i, $incomeAndProfitBeforeCharingCommission[$key]['income']/$unit);
			$helpExport->setStyle_11_A_N_C_T($sheet, 'D'.$i, 'D'.$i);

			$sheet->setCellValue('E'.$i, $incomeAndProfitBeforeCharingCommission[$key]['profit_before_charging_commission']/$unit);
			$helpExport->setStyle_11_A_N_C_T($sheet, 'E'.$i, 'E'.$i);
			$sheet->getStyle('E'.$i)->getNumberFormat()->setFormatCode($number_format);
			$sheet->getStyle('D'.$i)->getNumberFormat()->setFormatCode($number_format);
			$sheet->getRowDimension($i)->setRowHeight(16);

			$i++;
			$j++;
		}

		$sheet->getStyle ( 'A'.($row_start-1) . ':' . 'E'.($i+1) )->getBorders ()->getOutline ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );
		$sheet->getStyle ( 'A'.($row_start-1) . ':' . 'E'.$i )->getBorders ()->getHorizontal ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_DOTTED );

		$sheet->getStyle ( 'A'.($row_start-1) . ':' . 'E'.($i+1) )->getBorders ()->getVertical() ->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );

		$sheet->getStyle ( 'A'.($i+1) . ':' . 'E'.($i+1) )->getBorders () -> getTop() ->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );

		$sheet->setCellValue('B'.($i+1), 'Total');
		$helpExport->setStyle_11_A_B_L_T($sheet, 'B'.($i+1), 'B'.($i+1));

		$current_col = 'E';
		$current_col_number = 4;

		foreach($group as $group_id => $emp_ids) {
			$i = 1;
			$col_name = $current_col;
			foreach($emp_ids as $emp_id) {
				$col_name = $helpExport->get_name_by_column($col_name, 1);

				$sheet->getColumnDimension ( $col_name )->setWidth ( 14 );

				if($i == 1)
					$first_col_name = $col_name;

				$employee_info = $employee_list[$emp_id];

				$sheet->setCellValue($col_name.'6', $employee_info['first_name'] . ' ' . $employee_info['last_name']);
				$helpExport->setStyle_11_A_B_C_T($sheet, $col_name.'6', $col_name.'6');

				$j       = 8;
				$first_j = $j;
				foreach($sale_full_ids as $sale_id) {
					$commission_value = (isset($sale_commission[$sale_id.'-'.$group_id.'-'.$emp_id])) ? $sale_commission[$sale_id.'-'.$group_id.'-'.$emp_id] : 0;
					$sheet->setCellValue($col_name.$j, $commission_value / $unit);
					$helpExport->setStyle_11_A_N_R_T($sheet, $col_name.$j, $col_name.$j);

					$sheet->getStyle($col_name.$j)->getNumberFormat()->setFormatCode($number_format);

					$last_j = $j;
					$j++;
				}

				$sheet->setCellValue($col_name.($j+1), '=SUM('.$col_name.$first_j.':'.$col_name.$last_j.')');
				$helpExport->setStyle_11_A_B_R_T($sheet, $col_name.($j+1), $col_name.($j+1));
				$sheet->getStyle($col_name.($j+1))->getNumberFormat()->setFormatCode($number_format);


				if($i == count($emp_ids)) {
					$last_col_number = $current_col_number + $i;

					$sheet->mergeCellsByColumnAndRow($current_col_number + 1,5,$last_col_number,5);
					$sheet->setCellValue($first_col_name.'5', $group_list[$group_id]['name']);
					$helpExport->setStyle_11_A_B_C_T($sheet, $first_col_name.'5', $first_col_name.'5');

					$current_col = $col_name;
					$current_col_number = $last_col_number;
				}
				$i++;
			}
		}


		$col_name = $current_col;
		foreach($employee_ids as $key => $employee_id) {
			$employee_info = $employee_list[$employee_id];
			$col_name = $helpExport->get_name_by_column($col_name, 1);

			$sheet->setCellValue($col_name.'6', $employee_info['first_name'] . ' ' . $employee_info['last_name']);
			$helpExport->setStyle_11_A_B_C_T($sheet, $col_name.'6', $col_name.'6');

			$j = 8;
			foreach($sale_full_ids as $sale_id) {
				$keyword = $sale_id . '-' . $employee_id;
				$commission_value = isset($sale_commission_by_employee[$keyword]) ? $sale_commission_by_employee[$keyword] : 0;
				$sheet->setCellValue($col_name.$j, $commission_value / $unit);
				$helpExport->setStyle_11_A_N_R_T($sheet, $col_name.$j, $col_name.$j);

				$sheet->getStyle($col_name.$j)->getNumberFormat()->setFormatCode($number_format);

				$j++;
			}

			$sheet->setCellValue($col_name.($j+1), '=SUM('.$col_name.$first_j.':'.$col_name.$last_j.')');
			$helpExport->setStyle_11_A_B_R_T($sheet, $col_name.($j+1), $col_name.($j+1));
			$sheet->getStyle($col_name.($j+1))->getNumberFormat()->setFormatCode($number_format);

			$sheet->getColumnDimension ( $col_name )->setWidth ( 14 );
		}

		$the_col_name_before_last_col = $col_name;
		$last_col_name = $col_name = $helpExport->get_name_by_column($col_name, 1);
		$sheet->setCellValue($last_col_name.'6', 'Total');
		$sheet->getColumnDimension ( $last_col_name )->setWidth ( 14 );
		$helpExport->setStyle_11_A_B_C_T($sheet, $last_col_name.'6', $last_col_name.'6');

		$first_col_name   = $helpExport->get_name_by_column($current_col, 1);
		$first_col_number = $current_col_number + 1;
		$last_col_number  = $first_col_number + count($employee_ids);

		$sheet->mergeCellsByColumnAndRow($first_col_number,5,$last_col_number,5);

		$sheet->setCellValue($first_col_name.'5', 'Tổng hợp theo tên nhân viên');
		$helpExport->setStyle_11_A_B_C_T($sheet, $first_col_name.'5', $first_col_name.'5');

		$unit_name = $unit . ' ' . $this->config->item('currency_symbol');
		$sheet->mergeCellsByColumnAndRow(5,4,$last_col_number,4);
		$sheet->setCellValue('F4', 'Tiền hoa hồng ('.$unit_name.')');
		$helpExport->setStyle_11_A_B_C_T($sheet, 'F4', 'F4');

		$i = 8;
		foreach($sale_full_ids as $sale_id) {
			$sheet->setCellValue($last_col_name.$i, '=SUM('.$first_col_name.$i.':'.$the_col_name_before_last_col.$i.')');
			$helpExport->setStyle_11_A_N_R_T($sheet, $last_col_name.$i, $last_col_name.$i);
			$sheet->getStyle($last_col_name.$i)->getNumberFormat()->setFormatCode($number_format);

			$i++;
		}

		$sheet->setCellValue($last_col_name.($i+1), '=SUM('.$first_col_name.($i+1).':'.$the_col_name_before_last_col.($i+1).')');
		$helpExport->setStyle_11_A_N_R_T($sheet, $last_col_name.($i+1), $last_col_name.($i+1));
		$sheet->getStyle($last_col_name.($i+1))->getNumberFormat()->setFormatCode($number_format);

		$sheet->getStyle ( 'F4' . ':' . $last_col_name.'6' )->getBorders ()->getOutline ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );
		$sheet->getStyle ( 'F4' . ':' . $last_col_name.'6' )->getBorders ()->getInside ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );

		$sheet->getStyle ( 'F7' . ':' . $last_col_name.$i )->getBorders ()->getOutline ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );
		$sheet->getStyle ( 'F7' . ':' . $last_col_name.$i )->getBorders ()->getHorizontal ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_DOTTED );
		$sheet->getStyle ( 'F7' . ':' . $last_col_name.$i )->getBorders ()->getVertical() ->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );

		$sheet->getStyle ( 'F'.($i+1) . ':' . $last_col_name.($i+1) )->getBorders ()->getOutline ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );
		$sheet->getStyle ( 'F'.($i+1) . ':' . $last_col_name.($i+1) )->getBorders ()->getInside ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );

		$total_col_name = $last_col_name;
		$col_number = $last_col_number + 1;
		$col_name   = $helpExport->get_name_by_column($last_col_name, 1);
		$sheet->mergeCellsByColumnAndRow($col_number,4,$col_number + 2,4);
		$sheet->setCellValue($col_name.'4', 'For checking');

		$sheet->mergeCellsByColumnAndRow($col_number,5,$col_number,6);
		$sheet->mergeCellsByColumnAndRow($col_number + 1,5,$col_number + 1,6);
		$sheet->mergeCellsByColumnAndRow($col_number + 2,5,$col_number + 2,6);

		$sheet->getColumnDimension ( $col_name )->setWidth ( 14.5 );
		$sheet->setCellValue($col_name.'5', 'Lợi nhuận');

		$next_col_name   = $helpExport->get_name_by_column($col_name, 1);
		$sheet->getColumnDimension ( $next_col_name )->setWidth ( 17.5 );
		$sheet->setCellValue($next_col_name.'5', 'Tỉ lệ hoa hồng');

		$last_col_name   = $helpExport->get_name_by_column($col_name, 2);
		$sheet->getColumnDimension ( $last_col_name )->setWidth ( 9.5 );
		$sheet->setCellValue($last_col_name.'5', 'Check');

		$helpExport->setStyle_11_A_N_C_T($sheet, $col_name.'4', $last_col_name.'6');

		$sheet->getStyle ( $col_name.'4' . ':' . $last_col_name.'6' )->getBorders ()->getOutline ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );
		$sheet->getStyle ( $col_name.'4' . ':' . $last_col_name.'6' )->getBorders ()->getInside ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );

		$i = 8;
		foreach($items as $val) {
			$sheet->setCellValue($col_name.$i, $val['profit']/$unit);
			$sheet->setCellValue($next_col_name.$i, '='.$total_col_name.$i.'/('.$total_col_name.$i.'+'.$col_name.$i.')*100');

			$sheet->getStyle($col_name.$i . ':' . $next_col_name.$i)->getNumberFormat()->setFormatCode($number_format);
			$helpExport->setStyle_11_A_N_R_T($sheet, $col_name.$i, $next_col_name.$i);

			$i++;
		}

		$sheet->setCellValue($col_name.($i+1), '=SUM('.$col_name.'8:'.$col_name.($i-1).')');
		$sheet->getStyle($col_name.($i+1))->getNumberFormat()->setFormatCode($number_format);
		$helpExport->setStyle_11_A_B_R_T($sheet, $col_name.($i+1), $col_name.($i+1));

		$sheet->getStyle ( $col_name.'7' . ':' . $last_col_name.$i )->getBorders ()->getOutline ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );
		$sheet->getStyle ( $col_name.'7' . ':' . $last_col_name.$i )->getBorders ()->getHorizontal ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_DOTTED );
		$sheet->getStyle ( $col_name.'7' . ':' . $last_col_name.$i )->getBorders ()->getVertical() ->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );


		$sheet->getStyle ( $col_name.($i+1) . ':' . $last_col_name.($i+1) )->getBorders ()->getOutline() ->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );
		$sheet->getStyle ( $col_name.($i+1) . ':' . $last_col_name.($i+1) )->getBorders ()->getInside() ->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );

        ////set dinh dang giay a4 cho ban in ra////////////
		$objPHPExcel->getActiveSheet ()->getPageSetup ()->setOrientation ( PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE );
		$objPHPExcel->getActiveSheet ()->getPageSetup ()->setPaperSize ( PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4 );
		$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 10);

		$pageMargin = $sheet->getPageMargins ();
		$pageMargin->setTop ( .5 );
		$pageMargin->setLeft ( .15 );
		$pageMargin->setRight ( .05 );
        ////////////////////////////////////////////////////
		header ( 'Content-Type: application/vnd.ms-excel' );
		header ( 'Content-Disposition: attachment;filename="summar_commission(' . date ( "d/m/Y" ) . ').xls"' );
		header ( 'Cache-Control: max-age=0' );
		$objWriter = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel5' );
		$objWriter->save ( 'php://output' );

	}

	//Summary employees report (old version)
	function _summary_commissions($start_date, $end_date, $sale_type, $employee_type, $export_excel=0, $offset = 0)
	{
		$this->load->model('Sale');
		$this->check_action_permission('view_commissions');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);

		$this->load->model('reports/Summary_commissions');
		$model = $this->Summary_commissions;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'employee_type' =>$employee_type, 'export_excel' => $export_excel, 'offset' => $offset));

		$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
		$config = array();
		$config['base_url'] = site_url("reports/summary_commissions/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$sale_type/$employee_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 8;		
		$this->load->library('pagination');$this->pagination->initialize($config);

		$tabular_data = array();
		$report_data = $model->getData();

		foreach($report_data as $row)
		{
			$data_row = array();
			
			$data_row[] = array('data'=>$row['employee'], 'align' => 'left');
			$data_row[] = array('data'=>to_currency($row['subtotal']), 'align' => 'right');
			$data_row[] =  array('data'=>to_currency($row['total']), 'align' => 'right');
			$data_row[] = array('data'=>to_currency($row['tax']), 'align' => 'right');
			if($this->has_profit_permission)
			{
				$data_row[] = array('data'=>to_currency($row['profit']), 'align' => 'right');
			}
			$data_row[] = array('data'=>to_currency($row['commission']), 'align' => 'right');			
			$tabular_data[] = $data_row;			
		}

		$data = array(
			"title" => lang('reports_comissions_summary_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
		);

		$this->load->view("reports/tabular",$data);
	}
	
	function graphical_summary_commissions($start_date, $end_date, $sale_type, $employee_type)
	{
		$this->load->model('Sale');
		$this->check_action_permission('view_commissions');
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_commissions');
		$model = $this->Summary_commissions;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'employee_type' => $employee_type));

		$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$data = array(
			"title" => lang('reports_comissions_summary_report'),
			"graph_file" => site_url("reports/graphical_summary_commissions_graph/$start_date/$end_date/$sale_type/$employee_type"),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"summary_data" => $model->getSummaryData()
		);

		$this->load->view("reports/graphical",$data);
	}

	//The actual graph data
	function graphical_summary_commissions_graph($start_date, $end_date, $sale_type, $employee_type)
	{
		$this->load->model('Sale');
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_commissions');
		$model = $this->Summary_commissions;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'employee_type' => $employee_type));

		$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
		$report_data = $model->getData();

		$graph_data = array();
		foreach($report_data as $row)
		{
			$graph_data[$row['employee']] = to_currency_no_money($row['commission']);
		}

		$currency_symbol = $this->config->item('currency_symbol') ? $this->config->item('currency_symbol') : '$';
		
		$data = array(
			"title" => lang('reports_comissions_summary_report'),
			"data" => $graph_data,
			"tooltip_template" => "<%=label %>: <%= parseFloat(Math.round(value * 100) / 100).toFixed(".$this->decimals.") %> $currency_symbol",
		);

		$this->load->view("reports/graphs/bar",$data);
	}
	
	
	/*
	*--------------------------------------------------------------------------------
	*                                  END SUMMARY COMMISSION
	*--------------------------------------------------------------------------------
	*/
	
	
	

	/*
	*--------------------------------------------------------------------------------
	*                                  START DETAIL COMMISSION
	*--------------------------------------------------------------------------------
	*/
	
	function detail_commissions_store() {
		$post  = $this->input->post();

		$this->load->model('reports/Summary_sales');

		$sale_model = $this->Summary_sales;
		if(!empty($post)) {
            //create sale temp table
			$time_condition = array();
			if(!empty($post['start_date'])) {
				$time_condition[] =  's.sale_time >= \''.$post['start_date'].'\'';
			}

			if(!empty($post['end_date'])) {
				$time_condition[] =  's.sale_time <= \''.$post['end_date'].'\'';
			}

			if(!empty($time_condition)) {
				$time_condition = ' AND ' . implode(' AND ', $time_condition);
			}else
			$time_condition = '';

			$location_ids = $post['location_ids'];
			if(!empty($post['group_ids'])) {
				$group_ids = $post['group_ids'];
				$group_ids_condition = ' AND s.sale_id IN (SELECT sale_id FROM phppos_sales_employees WHERE group_id IN ('.$group_ids.'))';
			}

			if(!empty($post['employees'])) {
				$employees = $post['employees'];
				$employees_condition = ' AND s.sale_id IN (SELECT sale_id FROM phppos_sales_employees WHERE employee_id IN ('.$employees.'))';
			}

			$where = "s.suspended = 0 AND s.store_account_payment = 0 $time_condition AND s.location_id IN ($location_ids) AND s.deleted = 0 $group_ids_condition $employees_condition";
			$this->Sale->create_sales_items_temp_table_n9(array('where'=>$where));


			$arrParam = array_merge($post, $this->input->get());
			$arrParam['paginator'] = $this->_paginator;
			$arrParam['page']      =  $this->uri->segment(3, 1);
			$config['base_url'] = base_url() . 'reports/detail_commissions_store';

			$config['total_rows'] = $sale_model->count_item($arrParam);

			$config['per_page'] = $arrParam['paginator']['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
            // $config['per_page'] = $arrParam['paginator']['per_page'] = 1;
			$config['uri_segment'] = 3;
			$config['use_page_numbers'] = TRUE;
			$options['task'] = 'commission';
			$items = $sale_model->list_item($arrParam,$options);
			$html_string = $this->load->view('reports/row/detail_commission',array('items'=>$items,'employee_ids'=>$post['employees']), true);

			$this->load->library("pagination");
			$this->pagination->initialize($config);
			$this->pagination->createConfig('front-end');

			$pagination = $this->pagination->create_ajax();

			$total_list_tmp = $sale_model->get_total_purchase($arrParam);

			$total_list = array(
				'detail_commissions_order_total'   => $total_list_tmp['order_value'],
				'detail_commissions_profit'        => $total_list_tmp['profit'],
				'detail_commissions_value'         => $total_list_tmp['commission'],
				'detail_commissions_profit_before' => to_currency(convert_number($total_list_tmp['profit'])+convert_number($total_list_tmp['commission']))
			);

			$result = array('total_list'=> $total_list, 'html_string'=>$html_string, 'pagination'=>$pagination);
			echo json_encode($result);
		}
	}

	function detail_commissions() {
		$this->check_action_permission('view_commissions');

		$data['action'] = $this->uri->segment(2);
		$data['locations'] = $this->Location->list_item();
		$data['title'] = 'Báo cáo chi tiết tiền hoa hồng';

        //$_SESSION['detail_commissions'] = array();
		if(isset($_SESSION['detail_commissions'])) {
			$data['filter'] = $_SESSION['detail_commissions'];
			$data['url_print'] = $_SESSION['detail_commissions']['url_print'];
			unset($_SESSION['detail_commissions']);            
			if($data['filter']['export_excel'] == 1)
				redirect($data['url_print']);
			else
				$this->load->view("reports/detail_commissions",$data);

		}else{
			$data['inputs']   = array('input_date_range', 'select_multiple_employee','select_multi_group_of_location','input_locations');
			$data['no_excel'] = false;
			$this->load->view("reports/n9_tabular",$data);
		}
	}


// Export excel 
	function detail_commissions_excel() {
		
		$get  = $this->input->get();
		$this->load->model('reports/Summary_sales');

		$sale_model = $this->Summary_sales;
		if(!empty($get)) {
				//create sale temp table
			$time_condition = array();
			if(!empty($get['start_date'])) {
				$time_condition[] =  's.sale_time >= \''.$get['start_date'].'\'';
			}

			if(!empty($get['end_date'])) {
				$time_condition[] =  's.sale_time <= \''.$get['end_date'].'\'';
			}

			if(!empty($time_condition)) {
				$time_condition = ' AND ' . implode(' AND ', $time_condition);
			}else
			$time_condition = '';

			$location_ids = $get['location_ids'];
			if(!empty($get['group_ids'])) {
				$group_ids = $get['group_ids'];
				$group_ids_condition = ' AND s.sale_id IN (SELECT sale_id FROM phppos_sales_employees WHERE group_id IN ('.$group_ids.'))';
			}

			if(!empty($get['employees'])) {
				$employees = $get['employees'];
				$employees_condition = ' AND s.sale_id IN (SELECT sale_id FROM phppos_sales_employees WHERE employee_id IN ('.$employees.'))';
			}

			$where = "s.suspended = 0 AND s.store_account_payment = 0 $time_condition AND s.location_id IN ($location_ids) AND s.deleted = 0 $group_ids_condition $employees_condition";
			$this->Sale->create_sales_items_temp_table_n9(array('where'=>$where));


			$arrParam = $this->input->get();
			$options['task'] = 'commission';
			$items = $sale_model->list_item($arrParam,$options);

				// data row excel
			$sale_prefix = $this->config->item('sale_prefix');
			foreach($items as $val) {

				$sale_id     = $val['sale_id'];
				$code        = isset($val['code']) ? $val['code'] : $sale_prefix . ' ' . $sale_id;
				$sale_time   = $val['sale_time_format'];
				$order_value = to_currency($val['order_value'] + $val['tax_value'] + $val['thu_value']);
				$profit      = $val['order_value'] + $val['thu_value'] - $val['chi_value'] - $val['cost_value'] - $val['commission'] - $val['point_payment'] - $val['gift_card_payment'];
				$profit      = to_currency($profit);
				$comment     = $val['comment'];
				$payment_type= $val['payment_type'];
				$commission  = '';


				foreach(explode(',',$employees) as  $employee_id)
				{ 
					if(isset($val['employee_commission_in_sale'][$employee_id]))
					{
						$commission .= $val['employee_commission_in_sale'][$employee_id]['last_name'] .' '.$val['employee_commission_in_sale'][$employee_id]['first_name'].' :'.to_currency($val['employee_commission_in_sale'][$employee_id]['commission']).', ';
					}

				}
						//setting data for parent rows
				$summary_data_row_export_excel[$sale_id]['code']             = $code;
				$summary_data_row_export_excel[$sale_id]['sale_time_format'] = $sale_time;
				$summary_data_row_export_excel[$sale_id]['order_value']      = $order_value;
				$summary_data_row_export_excel[$sale_id]['profit']           = $profit;
				$summary_data_row_export_excel[$sale_id]['comment']          = $comment;
				$summary_data_row_export_excel[$sale_id]['commission']       = $commission;

						//set data for child rows
				$sale_info = $this->sale_lib->get_sale($sale_id, array('included_commission'=>true));
				foreach($sale_info['cart'] as $cart) {
					$product_name = $cart['name'];

					$unit_price = $cart['quantity_exchange'] * $cart['unit_price'];
					if($cart['tax_included'])
						$unit_price = $unit_price + $cart['tax_by_unit'];

					$quantity    = $cart['quantity'];
					$discount    = $unit_price * $quantity * $cart['discount_percent']/100;
					$total       = $unit_price * $quantity - $discount;
					$total       = to_currency($total);
					$discount    = to_currency($discount);
					$unit_price  = to_currency($unit_price);
					$description = $cart['description'];
					$measure     = $cart['measure_name'];

					$details_data_row_export_excel[$sale_id][] = array(
						'name'        => $product_name,
						'unit_price'  => $unit_price,
						'quantity'    => $quantity,
						'total'       => $total,
						'discount'    => $discount,
						'description' => $description,
						'measure'     => $measure,
					);
				}

						//Expenses data
				$listExpensesOfAllSale['expenses'][$sale_id]  = $this->Expense->get_item(array('sale_id'=>$val['sale_id'],'deleted' =>true));

			}
				// Expense of detail data row excel
				//---------------------------------
			$moreChildDataExcel = [];
			foreach($listExpensesOfAllSale['expenses'] as $key=>$row)
			{
				foreach($row as $Sale_id => $expense_value){
					$moreChildDataExcel['expenses'][$key][] = array(
						'expense_decription' => $expense_value['expense_description'],
						'expense_type'       => ($expense_value['expense_type']==1)?lang('reports_expense_type_1'):lang('reports_expense_type_2'),
						'expense_tax'        => to_currency($expense_value['expense_tax']),
						'expense_amount'     => to_currency($expense_value['expense_amount'])
					);
				}
			}
				//---------------------------------
				// END Expense of detail data row excel


		}

		
		$headers = array();
		$headers['summary'][] = array('data'=>lang('STT'));
		$headers['summary'][] = array('data'=>lang('reports_date'));
		$headers['summary'][] = array('data'=>lang('reports_sale_code'));
		$headers['summary'][] = array('data'=>lang('reports_total_money'));
		if($this->Employee->has_module_action_permission('reports','show_profit',$this->Employee->get_logged_in_employee_info()->person_id))
		{
			$headers['summary'][] = array('data'=>lang('reports_profit'));
		}
		$headers['summary'][] = array('data'=>lang('reports_summary_commission'));
		$headers['summary'][] = array('data'=>lang('reports_notes'));
		
		$headers['details'][] = array('data'=>lang('STT'));
		$headers['details'][] = array('data'=>lang('reports_name'));
		$headers['details'][] = array('data'=>lang('reports_retail_price'));
		$headers['details'][] = array('data'=>lang('reports_quantity'));
		$headers['details'][] = array('data'=>lang('reports_measure_purchased'));
		$headers['details'][] = array('data'=>lang('reports_discount'));
		$headers['details'][] = array('data'=>lang('reports_total'));		
		$headers['details'][] = array('data'=>lang('reports_notes'));

		$headers['expenses'][] = array('data'=>lang('reports_expenses_description'), 'align'=> 'left');
		$headers['expenses'][] = array('data'=>lang('reports_expenses_type'), 'align'=> 'left');
		$headers['expenses'][] = array('data'=>lang('reports_expenses_money'), 'align'=> 'left');
		$headers['expenses'][] = array('data'=>lang('reports_expenses_tax'), 'align'=> 'left');

		$headerOfBodyField[] = '__AUTO__';
		$headerOfBodyField[] = 'sale_time_format';
		$headerOfBodyField[] = 'code';
		$headerOfBodyField[] = 'order_value';
		if($this->Employee->has_module_action_permission('reports','show_profit',$this->Employee->get_logged_in_employee_info()->person_id))
		{
			$headerOfBodyField[] = 'profit';
		}
		$headerOfBodyField[] = 'commission';
		$headerOfBodyField[] = 'comment';
		
		$headerOfChildBodyField[] = '__AUTO__';
		$headerOfChildBodyField[] = 'name';
		$headerOfChildBodyField[] = 'unit_price';
		$headerOfChildBodyField[] = 'quantity';
		$headerOfChildBodyField[] = 'measure';
		$headerOfChildBodyField[] = 'discount';
		$headerOfChildBodyField[] = 'total';
		$headerOfChildBodyField[] = 'description';
		
		$moreFieldOfChildBody['expenses'] = array(
			array('col' => 'B:D','value_field' =>'expense_decription'),
			array('col' => 'E','value_field' =>'expense_type'),
			array('col' => 'F','value_field' =>'expense_amount'),
			array('col' => 'G','value_field' =>'expense_tax')
		);
		
			//Set Header and field of parent Col
		$excelColumn = 'A';
		for($i = 0;$i< count($headers['summary']);$i++)
		{
			$headerOfBody[] = array('col' => $excelColumn,'value_field' =>$headerOfBodyField[$i]);
			$header_of_col_name[] = array('col' =>$excelColumn,'text' => $headers['summary'][$i]['data'],'styles'=>array('bold' =>true,'font'=>true, 'font_size'=>14,'is_fill'=>true,'color'=>'98d9da'));
			$excelColumn++;
		}
			//header of File
		$location_name   = $this->Location->get_info($get['location_ids'])->name;
		$employees_info  = $this->Employee->get_info_by_ids($employees);
		$employee_name   = '';
		foreach($employees_info as $employee_info)
		{
			$employee_name .= $employee_info['last_name'].' '.$employee_info['first_name'].',';
		}
		$company_name    = $this->config->item('company');
		$header_of_multicol[] = array('mergeStartCol' =>'A4','mergeEndCol'=>$excelColumn.'4','text' =>lang('reports_detailed_commission_report'),'styles'=>array('bold' =>true,'font'=>true, 'font_size'=>26));
		$header_of_multicol[] = array('mergeStartCol' =>'A1','mergeEndCol'=>'B1','text' =>$company_name,'styles'=>array('bold' =>true,'font'=>true, 'font_size'=>12));
		$header_of_multicol[] = array('mergeStartCol' =>'A2','mergeEndCol'=>'B2','text' =>$location_name,'styles'=>array('bold' =>false,'font'=>true, 'font_size'=>12));
		$header_of_multicol[] = array('mergeStartCol' =>'A5','mergeEndCol'=>$excelColumn.'5','text' =>lang('reports_date_range').': '.date('d-m-Y', strtotime($get['start_date'])) .'-'.date(get_date_format(), strtotime($get['end_date'])),'styles'=>array('bold' =>false,'font'=>true, 'font_size'=>12));

		$header_of_multicol[] = array('mergeStartCol' =>'A6','mergeEndCol'=>$excelColumn.'6','text' =>lang('reports_employee').': '.$employee_name,'styles'=>array('bold' =>false,'font'=>true, 'font_size'=>12));

		$excelColumn = 'B';
			//Header of child Column
		for($i = 0;$i< count($headers['details']);$i++) {
			$fieldOfChildBody[]         = array('col' => $excelColumn,'value_field' =>$headerOfChildBodyField[$i]);
			$header_of_child_col_name[] = array('col' =>$excelColumn,'text' => $headers['details'][$i]['data'],'styles'=>array('font'=>true, 'font_size'=>12,'is_fill'=>true,'color'=>'e7e8e8'));
			$excelColumn++;
		}
		$more_header_of_child_col_name = array('expenses' => array() );
		$more_header_of_child_col_name['expenses'][] = array('col' =>'B:D','text' => $headers['expenses'][0]['data'],'styles'=>array('font'=>true, 'font_size'=>12,'is_fill'=>true,'color'=>'e7e8e8'));
		$more_header_of_child_col_name['expenses'][] = array('col' =>'E','text' => $headers['expenses'][1]['data'],'styles'=>array('font'=>true, 'font_size'=>12,'is_fill'=>true,'color'=>'e7e8e8'));
		$more_header_of_child_col_name['expenses'][] = array('col' =>'F','text' => $headers['expenses'][2]['data'],'styles'=>array('font'=>true, 'font_size'=>12,'is_fill'=>true,'color'=>'e7e8e8'));
		$more_header_of_child_col_name['expenses'][] = array('col' =>'G','text' => $headers['expenses'][3]['data'],'styles'=>array('font'=>true, 'font_size'=>12,'is_fill'=>true,'color'=>'e7e8e8'));


		$bizExcel = new BizExcel('report_specific_supplier.xlsx');
		$bizExcel->setHeaderOfMultiCol($header_of_multicol);
		$bizExcel->setNumberRowStartBody(7)->setHeaderOfBody($headerOfBody);
		$bizExcel->setFieldOfChildBody($fieldOfChildBody)->setMoreFieldOfChildBody($moreFieldOfChildBody);
		$bizExcel->setHeaderOfCol($header_of_col_name);
		$bizExcel->setDataExcel($summary_data_row_export_excel);
		$bizExcel->setHeaderOfChildCol($header_of_child_col_name);
		$bizExcel->setHeaderOfMoreChildCol($more_header_of_child_col_name);
		$bizExcel->setChildDataExcel($details_data_row_export_excel)->setMoreChildDataExcel($moreChildDataExcel);
		$bizExcel->setMoreChildData(true);
		$excelContent = $bizExcel->generateFile(false);
		$this->load->helper('download');
		force_download(lang('reports_detailed_commission_report'). date('d-m-Y', strtotime($get['start_date'])) .'-'.date(get_date_format(), strtotime($get['end_date'])).'.xlsx', $excelContent);
		exit;
	}


	
	function detail_commission($sale_id) {
		$this->check_action_permission('view_commissions');

		$order_sale = $this->sale_lib->get_sale($sale_id, array('included_commission'=>true));
		if($order_sale['info']['commission_status'] == 0) {
			echo '<h2>Hóa đơn chưa được tính hóa hồng</h2>';
			die;
		}

		$sale_prefix = $this->config->item('sale_prefix');
		$sale_code   = !empty($order_sale['info']['code']) ? $order_sale['info']['code'] : $sale_prefix . '-' . $sale_id;

		$this->db -> select('emp.sale_id, emp.group_id, emp.group_id, p.first_name, p.last_name, g.name AS group_name')
		-> from('sales_employees AS emp')
		-> join('people AS p', 'emp.employee_id = p.person_id')
		-> join('groups AS g', 'emp.group_id = g.group_id')
		-> where('sale_id', $sale_id);

		$query = $this->db->get();

		$result_tmp = $query->result_array();

		$this->db->flush_cache();

		$group_list_info = array();
		if(!empty($result_tmp)) {
			foreach($result_tmp as $val) {
				$group_list_info[$val['group_name']][] = $val['first_name'] . ' ' . $val['last_name'];
			}
		}

		$this->db -> select("COUNT(e.id) AS quantity, c.name, SUM((e.expense_amount+e.expense_tax)*e.expense_type) AS final_amount")
		-> from('expenses AS e')
		-> join('categories As c', 'e.category_id = c.id')
		-> where('e.sale_id', $sale_id)
		-> where('e.deleted', 0)
		-> order_by('e.id', 'DESC')
		-> group_by('c.name');

		$query = $this->db->get();

		$chi_phi_list = $query->result_array();
		$this->db->flush_cache();

		if($order_sale['info']['commission_method'] == 'order') {
			$sales_commission = $this->Sale->get_sales_commission_for_each_employee(array('sale_id'=>$sale_id));
		}

		if(!empty($order_sale['info']['code']))
			$sale_code = $order_sale['info']['code'];
		else
			$sale_code = $this->config->item('sale_prefix') . ' ' . $order_sale['info']['sale_id'];

		$customer_name = '';
		if($order_sale['info']['customer_id'] > 0) {
			$customer_info = $this->Customer->get_info($order_sale['info']['customer_id']);
			$customer_name = $customer_info->first_name . ' ' . $customer_info->last_name;
		}

        // excel begin
		$this->load->helper('n9excel');
		require_once (APPPATH.'libraries/PHPExcel/PHPExcel.php');
		require_once (APPPATH.'libraries/PHPExcel/PHPExcel/IOFactory.php');
		require_once (APPPATH.'libraries/PHPExcel/PHPEXCHelper.php');

		$helpExport = new HelpFuncExportExcel ();
		$objReader = PHPExcel_IOFactory::createReader ( "Excel5" );
		$colIndex = '';
		$rowIndex = 0;
		$objPHPExcel = new PHPExcel ();
		$sheet = $objPHPExcel->getActiveSheet ();

		$sheet->getColumnDimension ( 'A' )->setWidth ( 7.5 );
		$sheet->getColumnDimension ( 'B' )->setWidth ( 27.5 );
		$sheet->getColumnDimension ( 'C' )->setWidth ( 14.6 );
		$sheet->getColumnDimension ( 'D' )->setWidth ( 14.14 );
		$sheet->getColumnDimension ( 'E' )->setWidth ( 13.5 );
		$sheet->getColumnDimension ( 'F' )->setWidth ( 16.71 );
		$sheet->getColumnDimension ( 'G' )->setWidth ( 17 );

		$sheet->mergeCellsByColumnAndRow(0, 1, 6, 1);
		$sheet->setCellValue('A1', 'BẢNG TÍNH PHẦN TRĂM HOA HỒNG');
		$helpExport->setStyle_11_A_B_C_C($sheet, 'A1', 'G1');
		$sheet->getRowDimension(1)->setRowHeight(22.5);
		$sheet->getRowDimension(2)->setRowHeight(22.5);

		$sheet->setCellValue('B3', 'Code:');
		$sheet->setCellValue('C3', $sale_code);
		$sheet->setCellValue('D3', '');

		$helpExport->setStyle_11_A_N_L_T($sheet, 'B3', 'D5');
		if(!empty($group_list_info)) {
			$i = 4;
			foreach($group_list_info as $key => $val) {
				$emp_name = implode(', ', $val);

				$sheet->setCellValue('B'.$i, $key.':');
				$sheet->setCellValue('C'.$i, $emp_name);

				$helpExport->setStyle_11_A_N_L_T($sheet, 'B'.$i, 'C'.$i);

				$i++;
			}
		}

		if(count($group_list_info) > 2)
			$current_row = 7 + count($group_list_info);
		else
			$current_row = 8;

		$sheet->setCellValue('B'.$i, 'Type of visa');
		$sheet->setCellValue('C'.$i, $customer_name);
		$sheet->setCellValue('D'.$i, $order_sale['info']['comment']);

		$helpExport->setStyle_11_A_N_L_C($sheet, 'B'.$i, 'D'.$i);

		$sheet->getStyle ( 'B3:B'.$i )->applyFromArray ( array (
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => 'ffff00')
			)
		) );


		$sheet->getStyle ( 'B3' . ':' . 'B' . ($i) )->getBorders ()->getOutline ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );
		$sheet->getStyle ( 'B3' . ':' . 'B' . ($i) )->getBorders ()->getInside ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );

		$sheet->getStyle ( 'C3' . ':' . 'D' . ($i) )->getBorders ()->getOutline ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );
		$sheet->getStyle ( 'C3' . ':' . 'D' . ($i) )->getBorders ()->getHorizontal ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );

		$sheet->getStyle ( 'C3' . ':' . 'C3' )->getBorders () -> getRight() ->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );

		$helpExport->setStyle_11_A_N_L_T($sheet, 'B'.$i, 'C'.$i);

		$sheet->setCellValue('E3', 'Ngày');
		$helpExport->setStyle_11_A_N_L_T($sheet, 'E3', 'E3');

		$sheet->getStyle ( 'B3:E3' )->applyFromArray ( array (
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => 'ffff00')
			)
		) );

		$sheet->setCellValue('F3', date("d/m/Y", strtotime($order_sale['info']['sale_time'])));
		$helpExport->setStyle_11_A_N_R_T($sheet, 'F3', 'F3');

		$sheet->mergeCellsByColumnAndRow(4, 4, 4, 5);
		$sheet->setCellValue('E4', 'Tỷ giá');
		$helpExport->setStyle_11_A_N_L_T($sheet, 'E4', 'E4');
		$sheet->getStyle ( 'E3:E4' )->applyFromArray ( array (
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => 'ffff00')
			)
		) );

		$sheet->setCellValue('F4', 'USD');
		$sheet->setCellValue('F5', 'EURO');
		$helpExport->setStyle_11_A_N_L_T($sheet, 'F4', 'F5');

		$sheet->getStyle ( 'F4:F5' )->applyFromArray ( array (
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => 'ffff00')
			)
		) );

		$sheet->getStyle ( 'E3' . ':' . 'E5' )->getBorders ()->getOutline ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );
		$sheet->getStyle ( 'F3' . ':' . 'G3' )->getBorders ()->getOutline ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );

		$sheet->getStyle ( 'E4' . ':' . 'G5' )->getBorders ()->getOutline ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );
		$sheet->getStyle ( 'E4' . ':' . 'G5' )->getBorders ()->getInside ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );

		$sheet->mergeCellsByColumnAndRow(1, $current_row, 1, $current_row + count($sales_commission) - 1);
		$sheet->setCellValue('B'.$current_row, '% hoa hồng');
		$helpExport->setStyle_11_A_N_L_T($sheet, 'B'.$current_row, 'B'.$current_row);

		$sheet->getStyle ( 'B'.$current_row.':B'.$current_row )->applyFromArray ( array (
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => 'ffff00')
			)
		) );


		$i = $current_row;
		foreach($sales_commission as $val) {
			$sheet->setCellValue('C'.$i, $val['first_name'] . ' ' . $val['last_name']);
			$sheet->setCellValue('D'.$i, (float)$val['commission_percent'] . '%');

			$helpExport->setStyle_11_A_N_L_T($sheet, 'C'.$i, 'C'.$i);
			$helpExport->setStyle_11_A_N_R_T($sheet, 'D'.$i, 'D'.$i);

			$sheet->getStyle ( 'C'.$i.':C'.$i )->applyFromArray ( array (
				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => 'ffff00')
				)
			) );

			$i++;
		}

		$sheet->mergeCellsByColumnAndRow(1, $i, 1, $i + count($sales_commission) - 1);
		$sheet->setCellValue('B'.$i, 'Hoa hồng được thưởng');
		$helpExport->setStyle_11_A_N_L_T($sheet, 'B'.$i, 'B'.$i);

		$sheet->getStyle ( 'B'.$i.':B'.$i )->applyFromArray ( array (
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => 'ffff00')
			)
		) );

		foreach($sales_commission as $val) {
			$sheet->setCellValue('C'.$i, $val['first_name'] . ' ' . $val['last_name']);
			$sheet->setCellValue('D'.$i, to_currency_without_unit($val['commission']));

			$helpExport->setStyle_11_A_N_L_T($sheet, 'C'.$i, 'C'.$i);
			$helpExport->setStyle_11_A_N_R_T($sheet, 'D'.$i, 'D'.$i);

			$sheet->getStyle ( 'C'.$i.':C'.$i )->applyFromArray ( array (
				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => 'ffff00')
				)
			) );

			$i++;
		}

		$sheet->getStyle ( 'B'.$current_row . ':' . 'D' . ($i-1) )->getBorders ()->getOutline ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );
		$sheet->getStyle ( 'B'.$current_row . ':' . 'D' . ($i-1) )->getBorders ()->getInside ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );

		$sheet->setCellValue('F'.$current_row, 'Lợi nhuận');
		$sheet->setCellValue('F'.($current_row+1), 'Tỷ suất');

		$sheet->getStyle ( 'F'.$current_row.':F'.($current_row + 1) )->applyFromArray ( array (
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => 'ffff00')
			)
		) );

		$helpExport->setStyle_11_A_N_L_T($sheet, 'F'.$current_row, 'F'.($current_row+2));

		$sheet->setCellValue('G'.$current_row, to_currency_without_unit($order_sale['loi_nhuan'] - $order_sale['hoa_hong']));
		$sheet->setCellValue('G'.($current_row+1), $order_sale['ty_suat']);

		$helpExport->setStyle_11_A_N_R_T($sheet, 'G'.$current_row, 'G'.($current_row+1));

		$sheet->getStyle ( 'F'.$current_row . ':' . 'G' . ($current_row+1) )->getBorders ()->getOutline ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );
		$sheet->getStyle ( 'F'.$current_row . ':' . 'G' . ($current_row+1) )->getBorders ()->getHorizontal ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_DOTTED );
		$sheet->getStyle ( 'F'.$current_row . ':' . 'G' . ($current_row+1) )->getBorders ()->getVertical() ->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );

        // tạo border ===
		$sheet->getStyle ( 'A'.$i . ':' . 'G' . $i )->getBorders ()->getBottom() ->setBorderStyle ( PHPExcel_Style_Border::BORDER_DOUBLE );

        // tạo khoảng cách
		for($j=3;$j<=$i;$j++) {
			$sheet->getRowDimension($j)->setRowHeight(22.5);
		}

		$current_row = $i + 1;
        // thu
		$sheet->setCellValue('A'.$current_row, 'THU');
		$helpExport->setStyle_11_A_B_L_B($sheet, 'A'.$current_row, 'A'.$current_row);
		$sheet->getRowDimension($current_row)->setRowHeight(22.5);
		$current_row = $current_row + 1;

		$sheet->mergeCellsByColumnAndRow(0, $current_row, 0, ($current_row+1));
		$sheet->setCellValue('A'.$current_row, 'STT');
		$helpExport->setStyle_11_A_B_L_C($sheet, 'A'.$current_row, 'A'.($current_row+2));

		$sheet->mergeCellsByColumnAndRow(1, $current_row, 3, $current_row);
		$sheet->setCellValue('B' . $current_row, 'GIÁ BÁN');

		$sheet->setCellValue('B' . ($current_row + 1), 'VND');
		$sheet->setCellValue('C' . ($current_row + 1), 'USD');
		$sheet->setCellValue('D' . ($current_row + 1), 'EURO');

		$sheet->mergeCellsByColumnAndRow(4, $current_row, 4, ($current_row+1));
		$sheet->setCellValue('E' . $current_row, 'SỐ LƯỢNG');

		$sheet->mergeCellsByColumnAndRow(5, $current_row, 5, ($current_row+1));
		$sheet->setCellValue('F' . $current_row, 'THÀNH TIỀN');

		$sheet->mergeCellsByColumnAndRow(6, $current_row, 6, ($current_row+1));
		$sheet->setCellValue('G' . $current_row, 'GHI CHÚ');

		$sheet->getStyle ( 'A' . $current_row . ':' . 'G' . ($current_row + 1) )->getBorders ()->getOutline ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );
		$sheet->getStyle ( 'A' . $current_row . ':' . 'G' . ($current_row + 1) )->getBorders ()->getInside ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );

		$helpExport->setStyle_11_A_B_C_C($sheet, 'B'.$current_row, 'G'.($current_row+1));

		$sheet->getStyle ( 'A' . $current_row . ':' . 'G' . ($current_row + 1) )->applyFromArray ( array (
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => 'ffff00')
			)
		) );

		$current_row = $current_row + 2;
		$i = $current_row;

		foreach($order_sale['cart'] as $key => $val) {
			$stt = $key + 1;

			$product_name = $val['name'];
            // $quantity = $val['measure_qty'];
			$don_gia = $val['quantity_exchange'] * $val['unit_price'];
			if($val['tax_included'])
				$don_gia = $don_gia + $val['tax_by_unit'];

			$so_luong      = $val['quantity'];
			$giam_gia      = $don_gia * $so_luong * $val['discount_percent']/100;
			$thanh_tien    = $don_gia * $so_luong - $giam_gia;

			$thanh_tien  = to_currency_without_unit($thanh_tien);
			$giam_gia    = to_currency($giam_gia);
			$don_gia     = to_currency_without_unit($don_gia);
			$description = $val['description'];
			$don_vi      = $val['measure_name'];

			$sheet->setCellValue('A' . $i, $stt);
			$helpExport->setStyle_11_A_N_L_C($sheet, 'A'.$i, 'A'.$i);

			$sheet->setCellValue('B' . $i, $don_gia);
			$helpExport->setStyle_11_A_N_R_C($sheet, 'B'.$i, 'B'.$i);

			$sheet->setCellValue('E' . $i, (float)$so_luong);
			$helpExport->setStyle_11_A_N_R_C($sheet, 'E'.$i, 'E'.$i);

			$sheet->setCellValue('F' . $i, $thanh_tien);
			$helpExport->setStyle_11_A_N_R_C($sheet, 'F'.$i, 'F'.$i);
			$i++;
		}

		$stt = $stt + 1;
		for($j=$i;$j<=$i+2;$j++) {
			$sheet->setCellValue('A' . $j, $stt);
			$helpExport->setStyle_11_A_N_L_C($sheet, 'A'.$j, 'A'.$j);

			$stt = $stt + 1;
		}

		$sheet->setCellValue('B' . $j, 'TỔNG CỘNG');
		$helpExport->setStyle_11_A_B_C_C($sheet, 'B'.$j, 'B'.$j);

		$sheet->setCellValue('F' . $j, to_currency_without_unit($order_sale['gia_tri_don_hang']));
		$helpExport->setStyle_11_A_B_R_C($sheet, 'F'.$j, 'F'.$j);

		$sheet->getStyle ( 'A' . $j . ':' . 'G' . $j )->applyFromArray ( array (
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => 'ffff00')
			)
		) );

		$sheet->getStyle ( 'A'.($current_row) . ':' . 'G' . $j )->getBorders ()->getOutline ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );
		$sheet->getStyle ( 'A'.($current_row) . ':' . 'G' . $j )->getBorders ()->getHorizontal ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_DOTTED );
		$sheet->getStyle ( 'A'.($current_row) . ':' . 'G' . $j )->getBorders ()->getVertical() ->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );

        // set distance for "thu"
		for($i=$current_row;$i<=$j;$i++) {
			$sheet->getRowDimension($i)->setRowHeight(22.5);
		}

		$current_row = $j;
        $current_row = $current_row + 1; //end thu
        $sheet->getRowDimension($current_row)->setRowHeight(15);
        $current_row = $current_row + 2;

        // chi
        $sheet->setCellValue('A'.$current_row, 'CHI');
        $helpExport->setStyle_11_A_B_L_C($sheet, 'A'.$current_row, 'A'.$current_row);
        $current_row = $current_row + 1;

        $sheet->setCellValue('A'.$current_row, 'STT');
        $sheet->setCellValue('B'.$current_row, 'DỊCH VỤ');
        $sheet->setCellValue('C'.$current_row, 'CP DỰ KIẾN');
        $sheet->setCellValue('D'.$current_row, 'CP THỰC TẾ');
        $sheet->setCellValue('E'.$current_row, 'SỐ LƯỢNG');
        $sheet->setCellValue('F'.$current_row, 'THÀNH TIỀN');
        $sheet->setCellValue('G'.$current_row, 'GHI CHÚ');

        $helpExport->setStyle_11_A_B_L_C($sheet, 'A'.$current_row, 'A'.$current_row);
        $helpExport->setStyle_11_A_B_C_C($sheet, 'B'.$current_row, 'G'.$current_row);
        $sheet->getStyle ( 'A' . $current_row . ':' . 'G' . $current_row )->applyFromArray ( array (
        	'fill' => array(
        		'type' => PHPExcel_Style_Fill::FILL_SOLID,
        		'color' => array('rgb' => 'ffff00')
        	)
        ) );

        $sheet->getRowDimension($current_row)->setRowHeight(21.75);

        $sheet->getStyle ( 'A' . $current_row . ':' . 'G' . $current_row )->getBorders ()->getOutline ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );
        $sheet->getStyle ( 'A' . $current_row . ':' . 'G' . $current_row )->getBorders ()->getInside ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );

        $i = $current_row + 1;

        if(!empty($chi_phi_list)) {
        	foreach($chi_phi_list as $key => $val) {
        		$sheet->setCellValue('A'.$i, ($key + 1));
        		$helpExport->setStyle_11_A_B_C_C($sheet, 'A'.$i, 'A'.$i);

        		$sheet->setCellValue('B'.$i, $val['name']);
        		$helpExport->setStyle_11_A_B_L_C($sheet, 'B'.$i, 'B'.$i);

        		$sheet->setCellValue('D'.$i, to_currency_without_unit($val['final_amount'] / $val['quantity']));
        		$helpExport->setStyle_11_A_N_R_C($sheet, 'D'.$i, 'D'.$i);

        		$sheet->setCellValue('E'.$i, $val['quantity']);
        		$helpExport->setStyle_11_A_N_R_C($sheet, 'E'.$i, 'E'.$i);

        		$sheet->setCellValue('F'.$i, to_currency_without_unit($val['final_amount']));
        		$helpExport->setStyle_11_A_N_R_C($sheet, 'F'.$i, 'F'.$i);

        		$i = $i + 3;
        	}
        }

        $sheet->setCellValue('B'.$i, 'TỔNG CỘNG');
        $helpExport->setStyle_11_A_B_L_C($sheet, 'B'.$i, 'B'.$i);

        $sheet->setCellValue('F'.$i, to_currency_without_unit($order_sale['chi_phi_sp']));
        $helpExport->setStyle_11_A_B_R_C($sheet, 'F'.$i, 'F'.$i);

        $sheet->getStyle ( 'A' . $i . ':' . 'G' . $i )->applyFromArray ( array (
        	'fill' => array(
        		'type' => PHPExcel_Style_Fill::FILL_SOLID,
        		'color' => array('rgb' => 'ffff00')
        	)
        ) );

        $i = $i + 2;

        $sheet->setCellValue('B'.$i, 'Duyệt');
        $helpExport->setStyle_11_A_B_C_C($sheet, 'B'.$i, 'B'.$i);

        $sheet->setCellValue('F'.$i, 'Người lập');
        $helpExport->setStyle_11_A_B_C_C($sheet, 'F'.$i, 'F'.$i);

        $i = $i + 1;

        $sheet->getStyle ( 'A'.($current_row+1) . ':' . 'G' . $i )->getBorders ()->getOutline ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );
        $sheet->getStyle ( 'A'.($current_row+1) . ':' . 'G' . $i )->getBorders ()->getHorizontal ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_DOTTED );
        $sheet->getStyle ( 'A'.($current_row+1) . ':' . 'G' . $i )->getBorders ()->getVertical() ->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );

        for($j = $current_row; $j<=$i;$j++) {
        	$sheet->getRowDimension($i)->setRowHeight(15);
        }

        $current_row = $i;

        ////set dinh dang giay a4 cho ban in ra////////////
        $objPHPExcel->getActiveSheet ()->getPageSetup ()->setOrientation ( PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE );
        $objPHPExcel->getActiveSheet ()->getPageSetup ()->setPaperSize ( PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4 );
        $objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 10);

        $pageMargin = $sheet->getPageMargins ();
        $pageMargin->setTop ( .5 );
        $pageMargin->setLeft ( .15 );
        $pageMargin->setRight ( .05 );
        ////////////////////////////////////////////////////
        header ( 'Content-Type: application/vnd.ms-excel' );
        header ( 'Content-Disposition: attachment;filename="commission-'.$sale_code.'(' . date ( "d/m/Y" ) . ').xls"' );
        header ( 'Cache-Control: max-age=0' );
        $objWriter = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel5' );
        $objWriter->save ( 'php://output' );
    }


    function _detailed_commissions($start_date, $end_date, $employee_id, $sale_type, $employee_type, $export_excel=0, $offset=0)
    {
    	$this->load->model('Sale');
    	$this->check_action_permission('view_commissions');
    	$logged_in_employee_id = $this->Employee->get_logged_in_employee_info()->person_id;

    	$can_view_all_employee_commissions = false;
    	if (!$this->Employee->has_module_action_permission('reports','view_all_employee_commissions', $logged_in_employee_id))
    	{
    		$employee_id = $logged_in_employee_id;
    	}

    	$start_date=rawurldecode($start_date);
    	$end_date=rawurldecode($end_date);

    	$this->load->model('reports/Detailed_commissions');
    	$model = $this->Detailed_commissions;
    	$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'employee_id' =>$employee_id, 'sale_type' => $sale_type, 'employee_type' => $employee_type, 'offset' => $offset, 'export_excel'=> $export_excel));

    	$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'employee_id' =>$employee_id, 'sale_type' => $sale_type));

    	$config = array();
    	$config['base_url'] = site_url("reports/detailed_commissions/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$employee_id/$sale_type/$employee_type/$export_excel");
    	$config['total_rows'] = $model->getTotalRows();
    	$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
    	$config['uri_segment'] = 9;
    	$this->load->library('pagination');$this->pagination->initialize($config);

    	$headers = $model->getDataColumns();
    	$report_data = $model->getData();

    	$summary_data = array();
    	$details_data = array();
    	$location_count = count(Report::get_selected_location_ids());

    	foreach($report_data['summary'] as $key=>$row)
    	{
    		$summary_data_row = array();
    		$summary_data_row[] = array('data'=>anchor('sales/receipt/'.$row['sale_id'], '<i class="ion-printer"></i>', array('target' => '_blank')).' '.anchor('sales/edit/'.$row['sale_id'], '<i class="ion-document-text"></i>', array('target' => '_blank')).' '.anchor('sales/edit/'.$row['sale_id'], lang('common_edit').' '.$row['sale_id'], array('target' => '_blank')), 'align'=>'left');

    		if ($location_count > 1)
    		{
    			$summary_data_row[] = array('data'=>$row['location_name'], 'align'=>'left');
    		}

    		$summary_data_row[] = array('data'=>date(get_date_format().'-'.get_time_format(), strtotime($row['sale_time'])), 'align'=> 'left');
    		$summary_data_row[] = array('data'=>to_quantity($row['items_purchased']), 'align'=> 'left');
    		$summary_data_row[] = array('data'=>$row['customer_name'].(isset($row['account_number']) && $row['account_number'] ? ' ('.$row['account_number'].')' : ''), 'align'=> 'left');
    		$summary_data_row[] = array('data'=>to_currency($row['subtotal']), 'align'=> 'right');
    		$summary_data_row[] = array('data'=>to_currency($row['total']), 'align'=> 'right');
    		$summary_data_row[] = array('data'=>to_currency($row['tax']), 'align'=> 'right');
    		if($this->has_profit_permission)
    		{
    			$summary_data_row[] = array('data'=>to_currency($row['profit']), 'align'=>'right');
    		}
    		$summary_data_row[] = array('data'=>to_currency($row['commission']), 'align'=> 'right');
    		$summary_data_row[] = array('data'=>$row['payment_type'], 'align'=>'right');
    		$summary_data_row[] = array('data'=>$row['comment'], 'align'=>'right');
    		$summary_data[$key] = $summary_data_row;


    		foreach($report_data['details'][$key] as $drow)
    		{
    			$details_data_row = array();
    			$details_data_row[] = array('data'=>isset($drow['item_name']) ? $drow['item_name'] : $drow['item_kit_name'], 'align'=>'left');
    			$details_data_row[] = array('data'=>$drow['category'], 'align'=>'left');
    			$details_data_row[] = array('data'=>$drow['serialnumber'], 'align'=>'left');
    			$details_data_row[] = array('data'=>$drow['description'], 'align'=>'left');
    			$details_data_row[] = array('data'=>to_quantity($drow['quantity_purchased']), 'align'=>'left');

    			$details_data_row[] = array('data'=>to_currency($drow['subtotal']), 'align'=>'right');
    			$details_data_row[] = array('data'=>to_currency($drow['total']), 'align'=>'right');
    			$details_data_row[] = array('data'=>to_currency($drow['tax']), 'align'=>'right');

    			if($this->has_profit_permission)
    			{
    				$details_data_row[] = array('data'=>to_currency($drow['profit']), 'align'=>'right');
    			}
    			$details_data_row[] = array('data'=>to_currency($drow['commission']), 'align'=>'right');

    			$details_data_row[] = array('data'=>$drow['discount_percent'].'%', 'align'=> 'left');

    			$details_data[$key][] = $details_data_row;
    		}
    	}
    	$employee_info = $this->Employee->get_info($employee_id);
    	$data = array(
    		"title" => $employee_info->first_name .' '. $employee_info->last_name.' '.lang('reports_report'),
    		"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
    		"headers" => $model->getDataColumns(),
    		"summary_data" => $summary_data,
    		"details_data" => $details_data,
    		"overall_summary_data" => $model->getSummaryData(),
    		"export_excel" => $export_excel,
    		"pagination" => $this->pagination->create_links(),
    	);

    	$this->load->view("reports/tabular_details",$data);
    }

    function detailed_timeclock($start_date, $end_date, $employee_id, $export_excel=0, $offset=0)
    {
    	$this->check_action_permission('view_timeclock');
    	$start_date=rawurldecode($start_date);
    	$end_date=rawurldecode($end_date);

    	$this->load->model('reports/Detailed_timeclock');
    	$model = $this->Detailed_timeclock;
    	$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'employee_id' =>$employee_id, 'offset' => $offset, 'export_excel'=> $export_excel));

    	$config = array();
    	$config['base_url'] = site_url("reports/detailed_timeclock/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$employee_id/$export_excel");
    	$config['total_rows'] = $model->getTotalRows();
    	$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
    	$config['uri_segment'] = 7;
    	$this->load->library('pagination');$this->pagination->initialize($config);

    	$headers = $model->getDataColumns();
    	$report_data = $model->getData();

    	$tabular_data = array();
    	$report_data = $model->getData();

    	foreach($report_data as $row)
    	{
    		$data_row = array();

    		$edit=anchor('timeclocks/view/'.$row['id'].'/'.$start_date.'/'. $end_date.'/'.$employee_id, lang('common_edit'));

    		$delete=anchor('timeclocks/delete/'.$row['id'].'/'.$start_date.'/'. $end_date.'/'.$employee_id, lang('common_delete'), 
    			"onclick='return do_link_confirm(".json_encode(lang('reports_confirm_timeclock_delete')).", this)'");

    		$data_row[] = array('data'=>$edit, 'align' => 'left');
    		$data_row[] = array('data'=>$delete, 'align' => 'left');
    		$data_row[] = array('data'=>$row['first_name'].' '.$row['last_name'], 'align' => 'left');
    		$data_row[] = array('data'=>date(get_date_format().' '.get_time_format(), strtotime($row['clock_in'])), 'align' => 'left');

    		if ($row['clock_out'] != '0000-00-00 00:00:00')
    		{
    			$data_row[] = array('data'=>date(get_date_format().' '.get_time_format(), strtotime($row['clock_out'])), 'align' => 'left');
    			$t1 = strtotime ($row['clock_out']);
    			$t2 = strtotime ($row['clock_in']);
    			$diff = $t1 - $t2;
    			$hours = $diff / ( 60 * 60 );

				//Not really the purpose of this function; but it rounds to 2 decimals
    			$hours = to_currency_no_money($hours,2);	
    		}
    		else
    		{
    			$data_row[] = array('data'=>lang('reports_not_clocked_out'), 'align' => 'left');
    			$hours = lang('reports_not_clocked_out');				
    		}

    		$data_row[] = array('data'=>$hours, 'align' => 'left');			
    		$data_row[] = array('data'=>to_currency($row['hourly_pay_rate']), 'align' => 'left');			
    		$data_row[] = array('data'=>to_currency($row['hourly_pay_rate'] * $hours), 'align' => 'left');			
    		$data_row[] = array('data'=>$row['clock_in_comment'], 'align' => 'left');			
    		$data_row[] = array('data'=>$row['clock_out_comment'], 'align' => 'left');			
    		$tabular_data[] = $data_row;			
    	}

    	$employee_info = $this->Employee->get_info($employee_id);

    	$data = array(
    		"title" => ($employee_id != -1 ? $employee_info->first_name . ' '.$employee_info->last_name . ' - ' : ' ').lang('reports_detailed_timeclock_report'),
    		"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
    		"headers" => $model->getDataColumns(),
    		"data" => $tabular_data,
    		"summary_data" => $model->getSummaryData(),
    		"export_excel" => $export_excel,
    		"pagination" => $this->pagination->create_links(),
    	);

    	$this->load->view("reports/tabular",$data);
    }

    function summary_timeclock($start_date, $end_date, $employee_id, $export_excel=0, $offset=0)
    {
    	$this->check_action_permission('view_timeclock');
    	$start_date=rawurldecode($start_date);
    	$end_date=rawurldecode($end_date);

    	$this->load->model('reports/Summary_timeclock');
    	$model = $this->Summary_timeclock;
    	$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'employee_id' =>$employee_id, 'offset' => $offset, 'export_excel'=> $export_excel));

    	$config = array();
    	$config['base_url'] = site_url("reports/summary_timeclock/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$employee_id/$export_excel");
    	$config['total_rows'] = $model->getTotalRows();
    	$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
    	$config['uri_segment'] = 7;
    	$this->load->library('pagination');$this->pagination->initialize($config);

    	$headers = $model->getDataColumns();
    	$report_data = $model->getData();

    	$tabular_data = array();
    	$report_data = $model->getData();

    	foreach($report_data as $row)
    	{
    		$data_row = array();

    		$data_row[] = array('data'=>$row['first_name'].' '.$row['last_name'], 'align' => 'left');
    		$data_row[] = array('data'=>$row['hours'], 'align' => 'left');			
    		$data_row[] = array('data'=>to_currency($row['total']), 'align' => 'left');			
    		$tabular_data[] = $data_row;			
    	}

    	$employee_info = $this->Employee->get_info($employee_id);

    	$data = array(
    		"title" => ($employee_id != -1 ? $employee_info->first_name . ' '.$employee_info->last_name . ' - ' : ' ').lang('reports_summary_timeclock_report'),
    		"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
    		"headers" => $model->getDataColumns(),
    		"data" => $tabular_data,
    		"summary_data" => $model->getSummaryData(),
    		"export_excel" => $export_excel,
    		"pagination" => $this->pagination->create_links(),
    	);

    	$this->load->view("reports/tabular",$data);
    }

    function closeout($date,$type) {
    	$this->check_action_permission('view_closeout');

    	$this->load->model('Sale');
    	$this->load->model('reports/Closeout');
    	$model = $this->Closeout;
    	$location_id = $this->Employee->get_logged_in_employee_current_location_id();

    	$where = "((s.suspended = 1 AND s.was_layaway = 1) OR s.suspended = 0 OR s.is_vat = 1) AND s.store_account_payment = 0  AND s.sale_time >= '$date 00:00:00' AND s.sale_time <= '$date 23:59:59' AND s.location_id = $location_id AND s.deleted = 0";
    	$this->Sale->create_sales_items_temp_table_n9(array('where'=>$where));

    	$where = "r.receiving_time >= '$date 00:00:00' AND r.receiving_time <= '$date 23:59:59' AND r.location_id = $location_id AND r.deleted = 0";
    	$this->Receiving->create_receivings_items_temp_table_n9(array('where'=>$where));

    	$params = array('date'=>$date, 'location_id'=>$location_id);
    	$report_list = $model->create_report($params);

    	$data['date']        = $date;
    	$data['prev_date']  = date('Y-m-d', strtotime($date .' -1 day'));
    	$data['next_date']  = date('Y-m-d', strtotime($date .' +1 day'));
    	$data['report_list'] = $report_list;


    	if($data['filter']['export_excel'] == 1)
    		$this->closeout_excel($date);
    	else
    		$this->load->view("reports/closeout",$data);
    }

    function closeout_excel($date) {
    	$this->check_action_permission('view_closeout');

    	$this->load->model('Sale');
    	$this->load->model('reports/Closeout');
    	$model = $this->Closeout;
    	$location_id = $this->Employee->get_logged_in_employee_current_location_id();

    	$where = "((s.suspended = 1 AND s.was_layaway = 1) OR s.suspended = 0 OR s.is_vat = 1) AND s.store_account_payment = 0  AND s.sale_time >= '$date 00:00:00' AND s.sale_time <= '$date 23:59:59' AND s.location_id = $location_id AND s.deleted = 0";
    	$this->Sale->create_sales_items_temp_table_n9(array('where'=>$where));

    	$where = "r.receiving_time >= '$date 00:00:00' AND r.receiving_time <= '$date 23:59:59' AND r.location_id = $location_id AND r.deleted = 0";
    	$this->Receiving->create_receivings_items_temp_table_n9(array('where'=>$where));

    	$params = array('date'=>$date, 'location_id'=>$location_id);
    	$report_list = $model->create_report($params);

    	$this->load->helper('n9excel');
    	require_once (APPPATH.'libraries/PHPExcel/PHPExcel.php');
    	require_once (APPPATH.'libraries/PHPExcel/PHPExcel/IOFactory.php');
    	require_once (APPPATH.'libraries/PHPExcel/PHPEXCHelper.php');

    	$company_name    = $this->config->item('company');
    	$company_address = nl2br($this->Location->get_info_for_key('address'));

    	$date_covert = date('d-m-Y', strtotime($date));

    	$date_covert = "Từ $date_covert 00:00 đến $date_covert 23:59";

    	$helpExport = new HelpFuncExportExcel ();
    	$objReader = PHPExcel_IOFactory::createReader ( "Excel5" );
    	$colIndex = '';
    	$rowIndex = 0;
    	$objPHPExcel = new PHPExcel ();
    	$sheet = $objPHPExcel->getActiveSheet ();

    	$sheet->getColumnDimension ( 'A' )->setWidth ( 50 );
    	$sheet->getColumnDimension ( 'B' )->setWidth ( 50 );

    	$sheet->mergeCellsByColumnAndRow(0, 1, 1, 1);
    	$sheet->setCellValue('A1', $company_name);
    	$helpExport->setStyle_13_TNR_B_L($sheet, 'A1', 'B1');
    	$sheet->getRowDimension(1)->setRowHeight(24.75);

    	$sheet->mergeCellsByColumnAndRow(0, 2, 1, 2);
    	$sheet->setCellValue('A2', $company_address);
    	$helpExport->setStyle_13_TNR_N_L($sheet, 'A2', 'B2');

    	$title = 'Báo cáo kết thúc ngày làm việc';
    	$sheet->mergeCellsByColumnAndRow(0, 4, 1, 4);
    	$sheet->setCellValue('A4', $title);
    	$helpExport->setStyle_16_TNR_B_C($sheet, 'A4', 'B4');
    	$sheet->getRowDimension(4)->setRowHeight(24);

    	$sheet->mergeCellsByColumnAndRow(0, 5, 1, 5);
    	$sheet->setCellValue('A5', $date_covert);
    	$helpExport->setStyle_11_TNR_I_C($sheet, 'A5', 'B5');

    	$sheet->setCellValue('A7', 'Mô tả');
    	$sheet->setCellValue('B7', 'Dữ liệu');
    	$helpExport->setStyle_13_TNR_B_C($sheet, 'A7', 'B7');

    	$sheet->getRowDimension(7)->setRowHeight(22);

    	$current_row = 7;
    	$i = 8;
    	foreach($report_list as $val) {
    		if($val['right'] == '--') {
    			$sheet->mergeCellsByColumnAndRow(0, $i, 1, $i);
    			$sheet->setCellValue('A'.$i, mb_strtoupper(strip_tags($val['left']), 'utf-8'));
    			$helpExport->setStyle_13_TNR_B_L($sheet, 'A'.$i, 'A'.$i);
    		}elseif($val['right'] == '&nbsp') {
    			$sheet->setCellValue('A'.$i, '');
    			$sheet->setCellValue('B'.$i, '');
    		}else {
    			$left  = str_replace("&nbsp"," ",$val['left']);
    			$right = str_replace("&nbsp"," ",$val['right']);

    			$sheet->setCellValue('A'.$i, $left);
    			$sheet->setCellValue('B'.$i, $right);
    			$helpExport->setStyle_13_TNR_N_L($sheet, 'A'.$i, 'B'.$i);
    		}

    		$sheet->getRowDimension($i)->setRowHeight(22);

    		$i++;
    	}

    	$sheet->getStyle ( 'A' . $current_row . ':' . 'B' . ($i-1) )->getBorders ()->getOutline ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );
    	$sheet->getStyle ( 'A' . $current_row . ':' . 'B' . ($i-1) )->getBorders ()->getInside ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );

        ////set dinh dang giay a4 cho ban in ra////////////
    	$objPHPExcel->getActiveSheet ()->getPageSetup ()->setOrientation ( PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE );
    	$objPHPExcel->getActiveSheet ()->getPageSetup ()->setPaperSize ( PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4 );
    	$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 10);

    	$pageMargin = $sheet->getPageMargins ();
    	$pageMargin->setTop ( .5 );
    	$pageMargin->setLeft ( .15 );
    	$pageMargin->setRight ( .05 );
        ////////////////////////////////////////////////////
    	header ( 'Content-Type: application/vnd.ms-excel' );
    	header ( 'Content-Disposition: attachment;filename="bao_cao_ket_ngay(' . date ( "d/m/Y" ) . ').xls"' );
    	header ( 'Cache-Control: max-age=0' );
    	$objWriter = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel5' );
    	$objWriter->save ( 'php://output' );
    }

	//Summary tags report
    function summary_tags($start_date, $end_date, $do_compare, $compare_start_date, $compare_end_date, $sale_type, $export_excel=0, $offset = 0)
    {
    	$this->load->model('Sale');
    	$this->load->model('Tag');
    	$this->check_action_permission('view_tags');
    	$start_date=rawurldecode($start_date);
    	$end_date=rawurldecode($end_date);
    	$compare_start_date=rawurldecode($compare_start_date);
    	$compare_end_date=rawurldecode($compare_end_date);

    	$this->load->model('reports/Summary_tags');
    	$model = $this->Summary_tags;
    	$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'export_excel'=>$export_excel, 'offset' => $offset));

    	$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

    	$config = array();
    	$config['base_url'] = site_url("reports/summary_tags/".rawurlencode($start_date).'/'.rawurlencode($end_date).'/'.$do_compare.'/'.rawurlencode($compare_start_date).'/'.rawurlencode($compare_end_date)."/$sale_type/$export_excel");
    	$config['total_rows'] = $model->getTotalRows();
    	$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
    	$config['uri_segment'] = 10;

    	$this->load->library('pagination');$this->pagination->initialize($config);

    	$tabular_data = array();
    	$report_data = $model->getData();
    	$summary_data = $model->getSummaryData();

    	if ($do_compare)
    	{
    		$compare_to_tags = array();

    		foreach(array_keys($report_data) as $tag_name)
    		{
    			$compare_to_tags[] = $tag_name;
    		}

    		$model_compare = $this->Summary_tags;			
    		$model_compare->setParams(array('start_date'=>$compare_start_date, 'end_date'=>$compare_end_date, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel' => $export_excel, 'compare_to_tags' =>$compare_to_tags));

    		$this->Sale->drop_sales_items_temp_table();
    		$this->Sale->create_sales_items_temp_table(array('start_date'=>$compare_start_date, 'end_date'=>$compare_end_date, 'sale_type' => $sale_type));

    		$report_data_compare = $model_compare->getData();
    		$report_data_summary_compare = $model_compare->getSummaryData();
    	}


    	foreach($report_data as $row)
    	{
    		if ($do_compare)
    		{
    			if (isset($report_data_compare[$row['tag']]))
    			{
    				$row_compare = $report_data_compare[$row['tag']];
    			}
    			else
    			{
    				$row_compare = FALSE;
    			}
    		}

    		$data_row = array();

    		$data_row[] = array('data'=>$row['tag'], 'align' => 'left');
    		$data_row[] = array('data'=>to_currency($row['subtotal']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['subtotal'] >= $row['subtotal'] ? ($row['subtotal'] == $row_compare['subtotal'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['subtotal']) .'</span>' :''), 'align' => 'right');
    		$data_row[] = array('data'=>to_currency($row['total']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['total'] >= $row['total'] ? ($row['total'] == $row_compare['total'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['total']) .'</span>' :''), 'align' => 'right');
    		$data_row[] = array('data'=>to_currency($row['tax']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['tax'] >= $row['tax'] ? ($row['tax'] == $row_compare['tax'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['tax']) .'</span>' :''), 'align' => 'right');
    		if($this->has_profit_permission)
    		{
    			$data_row[] = array('data'=>to_currency($row['profit']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['profit'] >= $row['profit'] ? ($row['profit'] == $row_compare['profit'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['profit']) .'</span>' :''), 'align' => 'right');
    		}
    		$data_row[] = array('data'=>floatval($row['item_sold']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['item_sold'] >= $row['item_sold'] ? ($row['item_sold'] == $row_compare['item_sold'] ?  '' : 'compare_better') : 'compare_worse').'">'.floatval($row_compare['item_sold']) .'</span>' :''), 'align' => 'right');
    		$tabular_data[] = $data_row;				
    	}		

    	$data = array(
    		"title" => lang('reports_tags_summary_report'),
    		"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)).($do_compare  ? ' '. lang('reports_compare_to'). ' '. date(get_date_format(), strtotime($compare_start_date)) .'-'.date(get_date_format(), strtotime($compare_end_date)) : ''),
    		"headers" => $model->getDataColumns(),
    		"data" => $tabular_data,
    		"summary_data" => $summary_data,
    		"export_excel" => $export_excel,
    		"pagination" => $this->pagination->create_links(),
    	);

    	$this->load->view("reports/tabular",$data);
    }

	//Graphical summary customers report
    function graphical_summary_tags($start_date, $end_date, $sale_type)
    {
    	$this->load->model('Sale');
    	$this->load->model('Tag');

    	$this->check_action_permission('view_tags');
    	$start_date=rawurldecode($start_date);
    	$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));

    	$this->load->model('reports/Summary_tags');
    	$model = $this->Summary_tags;
    	$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

    	$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

    	$data = array(
    		"title" => lang('reports_tags_summary_report'),
    		"graph_file" => site_url("reports/graphical_summary_tags_graph/$start_date/$end_date/$sale_type"),
    		"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
    		"summary_data" => $model->getSummaryData()
    	);

    	$this->load->view("reports/graphical",$data);
    }

	//The actual graph data
    function graphical_summary_tags_graph($start_date, $end_date, $sale_type)
    {
    	$this->load->model('Sale');
    	$this->load->model('Tag');

    	$start_date=rawurldecode($start_date);
    	$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));

    	$this->load->model('reports/Summary_tags');
    	$model = $this->Summary_tags;
    	$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

    	$this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

    	$report_data = $model->getData();

    	$graph_data = array();
    	foreach($report_data as $row)
    	{
    		$graph_data[$row['tag']] = to_currency_no_money($row['total']);
    	}
    	$currency_symbol = $this->config->item('currency_symbol') ? $this->config->item('currency_symbol') : '$';


    	$data = array(
    		"title" => lang('reports_tags_summary_report'),
    		"data" => $graph_data,
    		"tooltip_template" => "<%=label %>: <%= parseFloat(Math.round(value * 100) / 100).toFixed(".$this->decimals.") %> $currency_symbol",
    		"legend_template" => "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%> (<%=parseFloat(Math.round(segments[i].value * 100) / 100).toFixed(".$this->decimals.")%> $currency_symbol)<%}%></li><%}%></ul>"
    	);

    	$this->load->view("reports/graphs/pie",$data);
    }

    function customer_search()
    {
    	$this->load->model('Customer');

		//allow parallel searchs to improve performance.
    	session_write_close();
    	$suggestions = $this->Customer->get_customer_search_suggestions($this->input->get('term'),100);
    	echo json_encode($suggestions);
    }

    function item_search()
    {
    	$this->load->model('Item');

		//allow parallel searchs to improve performance.
    	session_write_close();
    	$suggestions = $this->Item->get_item_search_suggestions($this->input->get('term'),100);
    	array_unshift($suggestions, array('value' => -1, 'label' => lang('common_all')));		
    	echo json_encode($suggestions);
    }


    function supplier_search($hide_all = 0)
    {
    	$this->load->model('Supplier');

		//allow parallel searchs to improve performance.
    	session_write_close();
    	$suggestions = $this->Supplier->get_supplier_search_suggestions($this->input->get('term'),100);

    	if (!$hide_all)
    	{
    		array_unshift($suggestions, array('value' => -1, 'label' => lang('common_all')));		
    	}

    	echo json_encode($suggestions);
    }

    function expiring_inventory($start_date, $end_date, $export_excel=0, $offset = 0)
    {
    	$start_date = rawurldecode($start_date);
    	$end_date = rawurldecode($end_date);

    	$this->check_action_permission('view_inventory_reports');
    	$this->load->model('reports/Inventory_expire_summary');
    	$model = $this->Inventory_expire_summary;
    	$model->setParams(array(
    		'start_date'=>$start_date,
    		'end_date' => $end_date, 
    		'export_excel' => $export_excel, 
    		'offset'=>$offset));

    	$config = array();
    	$config['base_url'] = site_url("reports/expiring_inventory/$start_date/$end_date/$export_excel");
    	$config['total_rows'] = $model->getTotalRows();
    	$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
    	$config['uri_segment'] = 6;
    	$this->load->library('pagination');$this->pagination->initialize($config);

    	$tabular_data = array();
    	$report_data = $model->getData();
    	$location_count = count(Report::get_selected_location_ids());

    	foreach($report_data as $row)
    	{
    		$data_row = array();


    		if ($location_count > 1)
    		{
    			$data_row[] = array('data'=>$row['location_name'], 'align' => 'left');
    		}

    		$data_row[] = array('data'=>$row['name'], 'align' => 'left');
    		$data_row[] = array('data'=>date(get_date_format(), strtotime($row['expire_date'])), 'align' => 'left');
    		$data_row[] = array('data'=>to_quantity($row['quantity_expiring']), 'align'=> 'left');
    		$data_row[] = array('data'=>$row['category'], 'align'=> 'left');
    		$data_row[] = array('data'=>$row['item_number'], 'align'=> 'left');
    		$data_row[] = array('data'=>$row['product_id'], 'align'=> 'left');
    		$data_row[] = array('data'=>$row['size'], 'align'=> 'left');
    		$data_row[] = array('data'=>$row['description'], 'align'=> 'left');
    		if($this->has_cost_price_permission)
    		{
    			$data_row[] = array('data'=>to_currency($row['cost_price']), 'align'=> 'right');
    		}
    		$data_row[] = array('data'=>to_currency($row['unit_price']), 'align'=> 'right');
			// $data_row[] = array('data'=>to_quantity($row['quantity']), 'align'=> 'left');
    		$data_row[] = array('data'=>to_quantity($row['reorder_level']), 'align'=> 'left');

    		$tabular_data[] = $data_row;				

    	}

    	$data = array(
    		"title" => lang('reports_expired_inventory_report'),
    		"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
    		"headers" => $model->getDataColumns(),
    		"data" => $tabular_data,
    		"summary_data" => $model->getSummaryData(),
    		"export_excel" => $export_excel,
    		"pagination" => $this->pagination->create_links(),
    	);

    	$this->load->view("reports/tabular",$data);
    }

    function report_filter() {
    	$post = $this->input->post();
    	$get  = $this->input->get();

    	if(!empty($post)) {
    		if((isset($get['time']) && $get['time'] == 1) || $post['report_type'] == 'simple') {
    			$start_suffix = ' 00:00:00';
    			$end_suffix = ' 23:59:59';
    		}

    		if($post['report_type'] == 'simple') {
            	# Nếu chọn toàn bộ thời gian
    			if($post['report_date_range_simple'] != 'all') {
    				$array_explode = explode('/', $post['report_date_range_simple']);
    				$filter['start_date'] = $array_explode[0] . $start_suffix;
    				$filter['end_date'] = $array_explode[1] . $end_suffix;

    				$print_url_ext[] = 'start_date=' . $filter['start_date'];
    				$print_url_ext[] = 'end_date=' . $filter['end_date'];

    			} 
    		} elseif($post['report_type'] == 'complex') {

    			if(!empty($post['start_date'])){
    				if(isset($start_suffix)){
    					$filter['start_date'] = $post['start_date'] . $start_suffix ;
    					$print_url_ext[] = 'start_date=' . $filter['start_date'];
    				} else {
    					$filter['start_date'] = $post['start_date'];
    					$print_url_ext[] = 'start_date=' . $filter['start_date'];
    				}

    			}

    			if(!empty($post['end_date'])) {
    				if(isset($start_suffix)){
    					$filter['end_date'] = $post['end_date'] . $end_suffix ;
    					$print_url_ext[] = 'end_date=' . $filter['end_date'];
    				} else {
    					$filter['end_date'] = $post['end_date'];
    					$print_url_ext[] = 'end_date=' . $filter['end_date'];
    				}
    			}
    		}

    		if(!empty($post['selected_location_ids'])) {
    			$filter['selected_location_ids'] = $post['selected_location_ids'];
    			$print_url_ext[] = 'location_ids=' . implode(',', $filter['selected_location_ids']);
    		}else {
    			if($get['action'] != 'specific_customer_store_acc' || $get['action'] != 'specific_supplier_store_acc') {
    				$filter['selected_location_ids'] = Report::get_selected_location_ids();
    				$print_url_ext[] = 'location_ids=' . implode(',', $filter['selected_location_ids']);
    			}
    		}

    		if(isset($post['employees'])) {
    			$filter['employees'] = $post['employees'];
    			$print_url_ext[] = 'employees='.implode(',', $post['employees']);
    		}

    		if(isset($post['employee_id'])) {
    			$filter['employee_id'] = $post['employee_id'];
    			$print_url_ext[] = 'employee_id='.$post['employee_id'];
    		}

    		if(isset($post['customer_id'])) {
    			$filter['customer_id'] = $post['customer_id'];
    			$print_url_ext[] = 'customer_id='.$post['customer_id'];
    		}

    		if(isset($post['nhom_khach_hang'])) {
    			$filter['nhom_khach_hang'] = $post['nhom_khach_hang'];
    			$print_url_ext[] = 'nhom_khach_hang='.$post['nhom_khach_hang'];
    		}

    		if(isset($post['customer_balance_options'])) {
    			$filter['customer_balance_options'] = $post['customer_balance_options'];
    			$print_url_ext[] = 'customer_balance_options='.$post['customer_balance_options'];
    		}

    		if(isset($post['supplier_id'])) {
    			$filter['supplier_id'] = $post['supplier_id'];
    			$print_url_ext[] = 'supplier_id='.$post['supplier_id'];
    		}

    		if(isset($post['supplier_balance_options'])) {
    			$filter['supplier_balance_options'] = $post['supplier_balance_options'];
    			$print_url_ext[] = 'supplier_balance_options='.$post['supplier_balance_options'];
    		}

    		if(isset($post['group_ids'])) {
    			$filter['group_ids'] = $post['group_ids'];
    			$print_url_ext[] = 'group_ids='.implode(',', $post['group_ids']);
    		}

    		$print_url_ext = implode('&', $print_url_ext);

    		$filter['url_print'] = base_url() . 'reports/'.$get['action'].'_excel?'.$print_url_ext;
    		$filter['export_excel'] = $post['export_excel'];

            # Lưu lại vào session
    		$_SESSION[$get['action']] = $filter;
    	}
    	redirect(base_url() . 'reports/'.$get['action']);
    }

    // shift expenses report
    function shift_expenses() {

    	$data = array();
    	$shift_category_id       = $this->config->item('shift_category_id');
    	if($shift_category_id > 0) {
    		$category = $this->Category->getItem($shift_category_id);
    		$data['category'] = $category;
    	}
    	$data['action'] = $this->uri->segment(2);
    	$data['locations'] = $this->Location->list_item();
    	$data['title'] = 'Báo cáo chi phí chuyển quỹ';
    	$data['inputs'] = array('input_date_range', 'input_locations');

    	if(isset($_SESSION['shift_expenses'])) {
    		$data['url_print'] = $_SESSION['shift_expenses']['url_print'];
    		$data['filter'] = $_SESSION['shift_expenses'];
    		unset($_SESSION['shift_expenses']);

    		$this->load->view("reports/shift_expenses",$data);
    	}else {
    		$data['time'] = 1;
    		$data['inputs'] = array('input_date_range', 'input_locations');
    		$this->load->view("reports/n9_tabular",$data);
    	}

    }

    function shift_expenses_store() {
    	$post  = $this->input->post();
    	$this->load->model('reports/Shift_expenses');
    	$model = $this->Shift_expenses;
    	if(!empty($post)) {
    		$arrParam = array_merge($post, $this->input->get());
    		$arrParam['paginator'] = $this->_paginator;
    		$arrParam['page']      =  $this->uri->segment(3, 1);

    		$config['base_url'] = base_url() . 'reports/shift_expenses_store';
    		$config['total_rows'] = $model->count_item($arrParam);

    		$config['per_page'] = $arrParam['paginator']['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
            //$config['per_page'] = $arrParam['paginator']['per_page'] = 1;
    		$config['uri_segment'] = 3;
    		$config['use_page_numbers'] = TRUE;

    		$items = $model->list_item($arrParam);

    		$this->load->library("pagination");
    		$this->pagination->initialize($config);
    		$this->pagination->createConfig('front-end');

    		$pagination = $this->pagination->create_ajax();

    		$shift_total = $model->sum_shift($arrParam);
    		$shift_total = number_format($shift_total,'0','.',',') . ' VND';

    		$result = array('count'=> $config['total_rows'], 'items'=>$items, 'pagination'=>$pagination, 'count_total'=>array('shift_total'=>$shift_total));
    		echo json_encode($result);
    	}
    }

    function shift_expenses_excel() {
    	$this->load->helper('n9excel');
    	require_once (APPPATH.'libraries/PHPExcel/PHPExcel.php');
    	require_once (APPPATH.'libraries/PHPExcel/PHPExcel/IOFactory.php');
    	require_once (APPPATH.'libraries/PHPExcel/PHPEXCHelper.php');

    	$shift_category_id       = $this->config->item('shift_category_id');
    	if($shift_category_id > 0) {
    		$category = $this->Category->getItem($shift_category_id);
    	}

    	if(empty($category))
    		redirect(base_url() . 'reports/shift_expenses');
    	else {
    		$arrParams = array_merge($this->input->post(), $this->input->get());

    		$company_name    = $this->config->item('company');
    		$company_address = nl2br($this->Location->get_info_for_key('address'));
    		if(!empty($arrParams['start_date'])){
    			$start_date_covert = date('d-m-Y H:i:s', strtotime($arrParams['start_date']));
    			$date_arr[]        = $start_date_covert;
    		}

    		if(!empty($arrParams['end_date'])){
    			$end_date_covert = date('d-m-Y H:i:s', strtotime($arrParams['end_date']));
    			$date_arr[]      = $end_date_covert;
    		}

    		if(!empty($date_arr)) {
    			$date_covert = 'Từ ' . implode(' đến ', $date_arr);
    		}

    		if(empty($arrParams['location_ids'])){
    			$arrParams['location_ids'] = implode(',', Report::get_selected_location_ids());
    		}

    		$helpExport = new HelpFuncExportExcel ();
    		$objReader = PHPExcel_IOFactory::createReader ( "Excel5" );
    		$colIndex = '';
    		$rowIndex = 0;
    		$objPHPExcel = new PHPExcel ();
    		$sheet = $objPHPExcel->getActiveSheet ();

    		$sheet->mergeCellsByColumnAndRow(0, 1, 3, 1);
    		$sheet->setCellValue('A1', $company_name);
    		$helpExport->setStyle_13_TNR_B_L($sheet, 'A1', 'D1');
    		$sheet->getRowDimension(1)->setRowHeight(24.75);

    		$sheet->mergeCellsByColumnAndRow(0, 2, 3, 2);
    		$sheet->setCellValue('A2', $company_address);
    		$helpExport->setStyle_11_TNR_N_L($sheet, 'A2', 'D2');

    		$sheet->mergeCellsByColumnAndRow(0, 4, 3, 4);
    		$sheet->setCellValue('A4', mb_strtoupper($category['name'],'UTF-8'));
    		$helpExport->setStyle_13_TNR_B_C($sheet, 'A4', 'D4');
    		$sheet->getRowDimension(4)->setRowHeight(24);

    		$sheet->mergeCellsByColumnAndRow(0, 5, 3, 5);
    		$sheet->setCellValue('A5', $date_covert);
    		$helpExport->setStyle_11_TNR_I_C($sheet, 'A5', 'D5');

    		$rowStart = 7;
    		$freeCol = 8;
    		$colStart = 'A';
    		$rowIndex = $rowStart;
    		$colIndex = $colStart;
    		$sheet = $objPHPExcel->getActiveSheet ();
    		$sheet->getColumnDimension ( 'A' )->setWidth ( 31 );
    		$sheet->getColumnDimension ( 'B' )->setWidth ( 52 );
    		$sheet->getColumnDimension ( 'C' )->setWidth ( 35 );
    		$sheet->getColumnDimension ( 'D' )->setWidth ( 33 );

    		$colIndex = $helpExport->setValueForSheet ( $sheet, $colIndex . $rowIndex, 'Thời gian', $colIndex );
    		$colIndex = $helpExport->setValueForSheet ( $sheet, $colIndex . $rowIndex, 'Diễn giải', $colIndex );
    		$colIndex = $helpExport->setValueForSheet ( $sheet, $colIndex . $rowIndex, 'Số tiền', $colIndex );
    		$colIndex = $helpExport->setValueForSheet ( $sheet, $colIndex . $rowIndex, 'Người nhận', $colIndex );

    		$helpExport->setStyle_11_TNR_B_C ( $sheet, $colStart . $rowIndex, $colIndex . $rowIndex );
    		$sheet->freezePane( "A$freeCol" );

    		$this->load->model('reports/Shift_expenses');
    		$model = $this->Shift_expenses;

    		$result      = $model->list_item($arrParams, array('task'=>'report'));

    		$shift_total = $model->sum_shift($arrParams);
    		$shift_total = number_format($shift_total,'0','.',',') . ' VND';

    		$sheet->getRowDimension(7)->setRowHeight(22);

    		$i = 8;
    		if(!empty($result)) {
    			foreach($result as $val) {
    				$sheet->getRowDimension($i)->setRowHeight(22);
    				$sheet->setCellValue('A'.$i, $val['expense_date']);
    				$helpExport->setStyle_12_TNR_N_C($sheet, 'A'.$i, 'A'.$i);

    				$sheet->setCellValue('B'.$i, $val['expense_description']);
    				$helpExport->setStyle_12_TNR_N_L($sheet, 'B'.$i, 'B'.$i);

    				$sheet->setCellValue('C'.$i, $val['expense_amount']);
    				$helpExport->setStyle_12_TNR_N_R($sheet, 'C'.$i, 'C'.$i);

    				$sheet->setCellValue('D'.$i, $val['fullname']);
    				$helpExport->setStyle_12_TNR_N_C($sheet, 'D'.$i, 'D'.$i);

    				$i++;
    			}
    		}

    		$sheet->getStyle ( 'A' . $rowStart . ':' . 'D' . ($i - 1) )->getBorders ()->getOutline ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );
    		$sheet->getStyle ( 'A' . $rowStart . ':' . 'D' . ($i - 1) )->getBorders ()->getInside ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );

    		$sheet->setCellValue('B'.($i+1), 'Tổng chi phí: ' . $shift_total);
    		$sheet->mergeCellsByColumnAndRow(1, $i+1, 2, $i+1);
    		$helpExport->setStyle_12_TNR_B_L($sheet, 'B'.($i+1), 'B'.($i+1));

            ////set dinh dang giay a4 cho ban in ra////////////
    		$objPHPExcel->getActiveSheet ()->getPageSetup ()->setOrientation ( PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE );
    		$objPHPExcel->getActiveSheet ()->getPageSetup ()->setPaperSize ( PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4 );
    		$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 10);

    		$pageMargin = $sheet->getPageMargins ();
    		$pageMargin->setTop ( .5 );
    		$pageMargin->setLeft ( .15 );
    		$pageMargin->setRight ( .05 );
            ////////////////////////////////////////////////////
    		header ( 'Content-Type: application/vnd.ms-excel' );
    		header ( 'Content-Disposition: attachment;filename="bao_cao_'.rewriteUrl($category['name']).'(' . date ( "d/m/Y" ) . ').xls"' );
    		header ( 'Cache-Control: max-age=0' );
    		$objWriter = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel5' );
    		$objWriter->save ( 'php://output' );
    	}
    }

	//Detailed expenses report
    function detailed_expenses($start_date, $end_date, $export_excel=0, $offset = 0)
    {

    	$start_date=rawurldecode($start_date);
    	$end_date=rawurldecode($end_date);

    	$this->check_action_permission('view_expenses');
    	$this->load->model('reports/Detailed_expenses');
    	$model = $this->Detailed_expenses;
    	$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date,'offset' => $offset, 'export_excel' => $export_excel));
    	$config = array();
    	$config['base_url'] = site_url("reports/detailed_expenses/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$export_excel");

    	$config['total_rows'] = $model->getTotalRows();
    	$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
    	$config['uri_segment'] = 6;
    	$this->load->library('pagination');$this->pagination->initialize($config);
    	$tabular_data = array();
    	$report_data = $model->getData();
    	$location_count = count(Report::get_selected_location_ids());

    	foreach($report_data as $row)
    	{
    		$tabular_data_row = array(
    			array('data'=>$row['id'], 'align'=> 'left'),
    			array('data'=>$row['expense_type'], 'align'=> 'left'),
    			array('data'=>$row['expense_description'], 'align'=> 'left'),
    			array('data'=>$row['category'], 'align'=> 'left'),
    			array('data'=>$row['expense_reason'], 'align'=> 'left'),
    			array('data'=>date(get_date_format(), strtotime($row['expense_date'])), 'align'=> 'left'),
    			array('data'=>  to_currency($row['expense_amount']), 'align'=> 'left'),
    			array('data'=>  to_currency($row['expense_tax']), 'align'=> 'left'),
    			array('data'=>$row['employee_recv'], 'align'=> 'left'),
    			array('data'=>$row['employee_appr'], 'align'=> 'left'),
    			array('data'=>$row['expense_note'], 'align'=> 'left'),
    		);


    		if ($location_count > 1)
    		{
    			array_unshift($tabular_data_row, array('data'=>$row['location_name'], 'align'=>'left'));
    		}
    		$tabular_data[] = $tabular_data_row;

    	}
    	$data = array(
    		"title" => lang('reports_expenses_detailed_report'),
    		"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
    		"headers" => $model->getDataColumns(),
    		"data" => $tabular_data,
    		"summary_data" => $model->getSummaryData(),
    		"export_excel" => $export_excel,
    		"pagination" => $this->pagination->create_links(),
    	);

    	$this->load->view("reports/tabular",$data);
    }


	//Summary expenses report
    function summary_expenses($start_date, $end_date, $export_excel=0, $offset = 0)        
    {
    	$start_date=rawurldecode($start_date);
    	$end_date=rawurldecode($end_date);


    	$this->load->model('reports/Summary_expenses');
    	$model = $this->Summary_expenses;
    	$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date,'offset' => $offset, 'export_excel' => $export_excel));
    	$config = array();
    	$config['base_url'] = site_url("reports/summary_expenses/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$export_excel");
    	$config['total_rows'] = $model->getTotalRows();
    	$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
    	$config['uri_segment'] = 6;
    	$this->load->library('pagination');$this->pagination->initialize($config);
    	$tabular_data = array();
    	$report_data = $model->getData();
    	foreach($report_data as $row)
    	{
    		$tabular_data[] = array(
    			array('data'=>$row['category'], 'align'=> 'left'), 
    			array('data'=>  to_currency($row['expense_tax']), 'align'=> 'left'), 
    			array('data'=>  to_currency($row['expense_amount']), 'align'=> 'left'), 
    		);
    	}
    	$data = array(
    		"title" => lang('reports_expenses_summary_report'),
    		"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
    		"headers" => $model->getDataColumns(),
    		"data" => $tabular_data,
    		"summary_data" => $model->getSummaryData(),
    		"export_excel" => $export_excel,
    		"pagination" => $this->pagination->create_links(),
    	);

    	$this->load->view("reports/tabular",$data);
    }

    function giftcards_audit_input()
    {
    	$data = $this->_get_common_report_data(TRUE);
    	$this->load->view("reports/giftcards_audit_input",$data);
    }

    function giftcard_audit($start_date, $end_date, $giftcard_number = -1, $export_excel = 0, $offset=0)
    {
    	$this->check_action_permission('view_giftcards');
    	$start_date=rawurldecode($start_date);
    	$end_date=rawurldecode($end_date);

    	$this->load->model('reports/Giftcard_audit');
    	$model = $this->Giftcard_audit;
    	$model->setParams(array('start_date' => $start_date, 'end_date' => $end_date, 'giftcard_number' => $giftcard_number, 'offset' => $offset, 'export_excel' => $export_excel));


    	$config = array();
    	$config['base_url'] = site_url("reports/$start_date/$end_date/giftcard_audit/$giftcard_number/$export_excel");
    	$config['total_rows'] = $model->getTotalRows();
    	$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
    	$config['uri_segment'] = 7;
    	$this->load->library('pagination');$this->pagination->initialize($config);
    	$headers = $model->getDataColumns();
    	$report_data = $model->getData();
    	$tabular_data = array();
    	foreach($report_data as $row)
    	{
    		$tabular_data[] = array(
    			array('data'=>date(get_date_format().' '.get_time_format(), strtotime($row['log_date'])), 'align'=> 'left'), 
    			array('data'=>$row['giftcard_number'], 'align'=> 'left'), 
    			array('data'=>$row['description'], 'align'=> 'left'), 
    			array('data'=>$row['log_message'], 'align'=> 'left'), 
    		);
    	}


    	$data = array(
    		"title" => lang('reports_giftcard'). ' '.lang('reports_audit_report'),
    		"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
    		"headers" => $model->getDataColumns(),
    		"data" => $tabular_data,
    		"summary_data" => $model->getSummaryData(),
    		"export_excel" => $export_excel,
    		"pagination" => $this->pagination->create_links(),
    	);

    	$this->load->view("reports/tabular",$data);
    }

    function view($id = -1){		
    	$this->load->model('reports/Specific_customer');
    	$model = $this->Specific_customer;
    	$data['data'] = $model->getHistoryEmailById($id);

    	$this->load->view("reports/view", $data);
    }

    function viewsms($id = -1){
    	$this->load->model('reports/Specific_customer');
    	$model = $this->Specific_customer;

    	$data['data'] = $model->getHistorySmsById($id);

    	$this->load->view("reports/viewsms", $data);
    }

    function tab($ids = ''){
    	unset($_SESSION['tab-click']);
    	$_SESSION['tab-click'] = $ids;
    	if ($ids > 0) {
    		echo json_encode(array('success' => true, 'message' => $ids));
    	} else {
    		echo json_encode(array('success' => false, 'message' => $ids));
    	}
    }

    function deleteditem($item_id = -1)
    {
    	if ($this->Item->deleteItem($item_id)) {
    		echo json_encode(array('success' => true, 'message' => $item_id));
    	}else{
    		echo json_encode(array('success' => false, 'message' => $item_id));	
    	}
    }


    function specific_employees_d13($employee_id = 0) 
    {
    	$this->check_action_permission('view_employees');
    	$this->load->view('reports/specific_employees_v_d13');
    	$rp = new Reports();
    	$rp->get_employee_total_revenue();

    }
    function task_d13($customer_id)
    {

    }

    // load view report contract
    function report_contract(){
    	if ($this->Employee->has_module_action_permission('reports','doi_chieu_hd', $this->Employee->get_logged_in_employee_info()->person_id)){
    		$arrParam['option'] = 'customer';
    		$data['contract'] = $this->Contract->list_item($arrParam);
    		$data['name_location'] = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id())->name;

    		$this->load->view('reports/contract/customer_contract.php',$data);
    	}else{
    		$this->load->view('reports/contract/not_permission_reports.php');
    	}
    	
    }
    // xuat bao cao hop dong theo thang (dev07) 
    function get_report_contract(){
    	$month = $this->input->post('month');
    	$year = $this->input->post('year');
    	$arrParam['option'] = 'customer';
    	// Hợp đồng đã nghiem thu
    	$arrParam['month'] = $month;
    	$arrParam['year'] =$year;
    	// lay so ngay cua thang truyen vao
    	$time_post = $arrParam['year'].'-'.$arrParam['month'];
    	$sumDay = date('t',strtotime($time_post));
    	if ($month==1) {
    		$month_b=12;
    		$month_bb=11;
    		$year_b = $year-1;

    	}
    	else{
    		$month_b=$month-1;
    		$month_bb=$month-2;
    		$year_b = $year;
    	}

    	if ($this->input->post('check')==null) {
    		$data['check'] = 'false';
    		$arrParam['date_signing']=true;
    		$data['contract'] = $this->Contract->get_contract_value(null,null,$arrParam);
    		$data['contract_all'] = $this->Contract->get_contract_value();
    		$data['tong_tien_da_thu_trong_thang'] = $this->Contract->tong_tien_da_thu_trong_thang($arrParam);
    		$data['tong_tien_thanh_toan_don_tich'] = $this->Contract->tong_tien_thanh_toan_don_tich($arrParam);
    		$data['ds_ben_thu_3']= $this->Contract->ds_ben_thu_3();
    		$data['contract_done'] = $this->Contract->get_contract_value(array('done'),null,array('month'=>$month,'year'=>$year,'date_payment'=>true));
    		$data['contract_liquidated'] = $this->Contract->get_contract_value(array('liquidated'),null,array('month'=>$month,'year'=>$year,'date_payment'=>true));
    		$data['contract_progress'] = $this->Contract->get_contract_value(null,null,array('month'=>$month,'year'=>$year,'progress'=>true));

    		$this->load->view('reports/contract/table_report',$data);

    	}elseif($this->input->post('check')=='stock') {
    		// so hop dong ky dau ky
    		$data['month_bb'] = $month_bb;
    		$data['check'] = 'true';
    		// lay theo ngay dang ky hop dong
    		// TU VAN PHAT HANH
    		// $tvph = array(trim('Tư vấn chào bán cổ phiếu ra công chúng'),trim('Tư vấn chào bán cổ phiếu riêng lẻ'),trim('Tư vấn và Đại lý phát hành trái phiếu'),trim('Đại lý phát hành trái phiếu'),trim('Tư vấn chào bán trái phiếu ra công chúng'),trim('Tư vấn chào bán trái phiếu riêng lẻ'));
    		$tvph = array(3,4);

    		$arrParam['date_signing']=true;
    		$tvph_cot1= count($this->Contract->get_contract_value(null,null,array('month'=>$month_b,'year'=>$year_b,'progress'=>true,'name_service'=>$tvph))) + count($this->Contract->get_contract_value(null,null,array('month'=>$month_b,'year'=>$year_b,'date_signing'=>true,'name_service'=>$tvph)));

    		$data['tvph_cot1'] = $tvph_cot1;
    		$tvph_cot2= count($this->Contract->get_contract_value(array('liquidated'),null,array('month'=>$month,'year'=>$year,'date_payment'=>true,'name_service'=>$tvph)));
    		$data['tvph_cot2'] = $tvph_cot2;

    		$tvph_cot3= count($this->Contract->get_contract_value(null,null,array('month'=>$month,'year'=>$year,'date_signing'=>true,'name_service'=>$tvph)));
    		$data['tvph_cot3'] = $tvph_cot3;
    		$tvph_c4 = ($tvph_cot1-$tvph_cot2)+$tvph_cot3;
    		if ($tvph_c4<0) {
    			$data['tvph_cot4'] = 0;
    		}else{
    			$data['tvph_cot4'] = $tvph_c4;
    		}
    		// ===========TU VAN CHUYEN DOI ==============
    		// $tvcd =array(trim('Cổ phần hóa'));
    		$tvcd = array(5);
    		$tvcd_cot1= count($this->Contract->get_contract_value(null,null,array('month'=>$month_b,'year'=>$year_b,'progress'=>true,'name_service'=>$tvcd))) + count($this->Contract->get_contract_value(null,null,array('month'=>$month_b,'year'=>$year_b,'date_signing'=>true,'name_service'=>$tvcd)));
    		
    		$data['tvcd_cot1'] = $tvcd_cot1;
    		$tvcd_cot2= count($this->Contract->get_contract_value(array('liquidated'),null,array('month'=>$month,'year'=>$year,'date_payment'=>true,'name_service'=>$tvcd)));
    		$data['tvcd_cot2'] = $tvcd_cot2;
    		$tvcd_cot3= count($this->Contract->get_contract_value(null,null,array('month'=>$month,'year'=>$year,'date_signing'=>true,'name_service'=>$tvcd)));
    		$data['tvcd_cot3'] = $tvcd_cot3;
    		$tvcd_c4 = ($tvcd_cot1-$tvcd_cot2)+$tvcd_cot3;
    		if ($tvcd_c4<0) {
    			$data['tvcd_cot4'] = 0;
    		}else{
    			$data['tvcd_cot4'] = $tvcd_c4;
    		}
    		
    		// ===========TU VAN KHAC==============
    		// $tvk = array(trim('Đăng ký công ty đại chúng'),trim('Đăng ký giao dịch UPCOM'),trim('Niêm yết'),trim('Tư vấn đại hội đồng cổ đông'),trim('Tư vấn M&A'),trim('Thoái vốn'));
    		$tvk = array(6,7,8);
    		$tvk_cot1= count($this->Contract->get_contract_value(null,null,array('month'=>$month_b,'year'=>$year_b,'progress'=>true,'name_service'=>$tvk))) + count($this->Contract->get_contract_value(null,null,array('month'=>$month_b,'year'=>$year_b,'date_signing'=>true,'name_service'=>$tvk)));

    		$data['tvk_cot1'] = $tvk_cot1;
    		$tvk_cot2= count($this->Contract->get_contract_value(array('liquidated'),null,array('month'=>$month,'year'=>$year,'date_payment'=>true,'name_service'=>$tvk)));
    		$data['tvk_cot2'] = $tvk_cot2;

    		$tvk_cot3= count($this->Contract->get_contract_value(null,null,array('month'=>$month,'year'=>$year,'date_signing'=>true,'name_service'=>$tvk)));
    		$data['tvk_cot3'] = $tvk_cot3;
    		$tvk_c4 =($tvk_cot1-$tvk_cot2)+$tvk_cot3;
    		if ($tvk_c4<0) {
    			$data['tvk_cot4'] = 0;
    		}else{
    			$data['tvk_cot4'] = $tvk_c4;
    		}
    		$this->load->view('reports/contract/table_report',$data);
    	}
    }

    // view table tu van dau tu tai chinh_chung khoan
    function finance_stock(){
    	// echo "<pre>"; print_r($this->Contract->get_contract_value()); die();
    	$data['name_location'] = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id())->name;

    	$arrParam['option'] = 'customer';
    	$data['contract'] = $this->Contract->list_item($arrParam);
    	$data['name_location'] = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id())->name;
    	$tvph = array('Tư vấn chào bán cổ phiếu ra công chúng','Tư vấn chào bán cổ phiếu riêng lẻ','Tư vấn Đại lý phát hành trái phiếu','Đại lý phát hành trái phiếu','Tư vấn chào bán trái phiếu ra công chúng','Tư vấn chào bán trái phiếu riêng lẻ');
    	// $a = $this->Contract->get_contract_value(null,null,array('month'=>2,'year'=>2019,'progress'=>true,'name_service'=>array(3,4)));
    	// echo "<pre>"; print_r($a); die();

    	for ($i=2013; $i <=2023 ; $i++) { 
    		$tmp[]= $i;
    	}

    	$data['year'] =$tmp;
    	$loai_dv = array(trim('Bảo lãnh phát hành cổ phiếu với hình thức nỗ lực tối đa'),trim('Bảo lãnh phát hành cổ phiếu với hình thức cam kết chắc chắn'),trim('Bảo lãnh phát hành trái phiếu với hình thức cố gắng tối đa'),trim('Bảo lãnh phát hành trái phiếu với hình thức cam kết chắc chắn'));

    	$data['bao_lanh_chung_khoan'] = $this->Contract->bao_cao_tai_chinh_dau_tu(array('name_service'=>$loai_dv,'bao_lanh_phat_hanh_chung_khoan'=>true,'location'=>true,'not_liquidated'=>true));
    	if ($this->Employee->has_module_action_permission('reports','tong_hop_hd', $this->Employee->get_logged_in_employee_info()->person_id))
    	{
    		$this->load->view('reports/contract/bao_cao_tai_chinh_chung_khoan',$data);
    	}else{
    		$this->load->view('reports/contract/not_permission_reports.php');
    	}
    	
    }
    // xuat bao cao hoạt động tài chính và tư vấn đầu tư chứng khoán
    function dowload_excel_finance_stock()
    {
    	$month = $this->input->post('input_month');
    	$year = $this->input->post('input_year');
    	if ($month==1) {
    		$month_b=12;
    		$month_bb=11;
    		$year_b = $year-1;

    	}
    	else{
    		$month_b=$month-1;
    		$month_bb=$month-2;
    		$year_b = $year;
    	}
    	$loai_dv = array('Bảo lãnh phát hành cổ phiếu với hình thức nỗ lực tối đa','Bảo lãnh phát hành cổ phiếu với hình thức cam kết chắc chắn','Bảo lãnh phát hành trái phiếu với hình thức cố gắng tối đa','Bảo lãnh phát hành trái phiếu với hình thức cam kết chắc chắn');
    	$month = $this->input->post('input_month');
    	$year = $this->input->post('input_year');
    	$bao_lanh_chung_khoan = $this->Contract->bao_cao_tai_chinh_dau_tu(array('name_service'=>$loai_dv,'bao_lanh_phat_hanh_chung_khoan'=>true));

    	$objPHPExcel = new PHPExcel();
    	$baolanh_chungkhoan = [
    		'STT',
    		'Tên tổ chức phát hành',
    		'Loại chứng khoán bảo lãnh',
    		'Hình thức bảo lãnh',
    		'Tổng giá trị bảo lãnh',
    		'Thời gian bảo lãnh (từ….đến….)',
    		'Vốn chủ sở hữu *',
    		'Tổng giá trị vốn hoạt động ròng *',
    		'Phí bảo lãnh thu được',
    		'Ghi chú'
    	];
    	
    	$style_title= array(
    		'font' => array(
    			'bold' => true
    		),
    		'alignment' => array(
    			'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
    		)
    	);
    	$style_header = array(
    		'font' => array(
    			'bold' => true
    		),
    		'alignment' => array(
    			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    		),
    	);
    	$style_table=array(
    		'alignment' => array(
    			'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
    		),
    		'borders' => array(
    			'allborders' => array(
    				'style' => PHPExcel_Style_Border::BORDER_THIN
    			)
    		)

    	);
    	$style_font_bold =array(
    		'font' =>array(
    			'bold' => true,
    		)
    	);

    	// 
    	$objPHPExcel->getDefaultStyle()->applyFromArray(array('font'=>array('size'=>11,'name'=>'Times New Roman')));
    	$objPHPExcel->getDefaultStyle()->getAlignment()->setWrapText(true);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(25);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
    	$objPHPExcel->getActiveSheet()->mergeCells('A1:N1');

    	$objPHPExcel->getActiveSheet()->setCellValue('A1','3. Hoạt động bảo lãnh phát hành chứng khoán');
    	$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($style_title);
    	$name_col =['A','B','D','E','F','H','J','K','M','N'];
    	for ($i=0; $i <count($name_col); $i++) {
    		$objPHPExcel->getActiveSheet()->setCellValue($name_col[$i].'2',$baolanh_chungkhoan[$i]);
    		$objPHPExcel->getActiveSheet()->getStyle($name_col[$i].'2')->applyFromArray($style_header);

    	}
    	$stt=1;
    	$row_start=3;
    	if (!empty($bao_lanh_chung_khoan)) {
    		foreach ($bao_lanh_chung_khoan as $key => $value) {
    			$objPHPExcel->getActiveSheet()->setCellValue('A'.$row_start,$stt);
    			$objPHPExcel->getActiveSheet()->setCellValue('B'.$row_start,$value['ten_kh']);
    			$objPHPExcel->getActiveSheet()->setCellValue('D'.$row_start,'');
    			$objPHPExcel->getActiveSheet()->setCellValue('E'.$row_start,$value['ten_dv']);
    			$objPHPExcel->getActiveSheet()->setCellValue('F'.$row_start,'');
    			$objPHPExcel->getActiveSheet()->setCellValue('H'.$row_start,'');
    			$objPHPExcel->getActiveSheet()->setCellValue('J'.$row_start,'');
    			$objPHPExcel->getActiveSheet()->setCellValue('K'.$row_start,'');
    			$objPHPExcel->getActiveSheet()->setCellValue('M'.$row_start,'');
    			$objPHPExcel->getActiveSheet()->setCellValue('N'.$row_start,$value['code']);
    			$stt++;
    			$row_start++;
    		}
    	}

    	for ($i=2; $i <=$row_start; $i++) {
    		$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':C'.$i);
    		$objPHPExcel->getActiveSheet()->mergeCells('F'.$i.':G'.$i);
    		$objPHPExcel->getActiveSheet()->mergeCells('H'.$i.':I'.$i);
    		$objPHPExcel->getActiveSheet()->mergeCells('K'.$i.':L'.$i); 
    	}
    	$name_all =['A','B','C','D','E','F','G','H','I','J','K','L','M','N'];
    	for ($i=1; $i <=$row_start; $i++) {
    		$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(32);
    		for ($j=0; $j < count($name_all) ; $j++) { 
    			$r=$i-1;
    			$objPHPExcel->getActiveSheet()->getStyle($name_all[$j].$r)->applyFromArray($style_table);
    		}
    	}
    	// Hoạt động tư vấn tài chính và tư vấn đầu tư chứng khoán
    	$row_start+=2;
    	$objPHPExcel->getActiveSheet()->mergeCells('A'.$row_start.':M'.$row_start);
    	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row_start,'4. Hoạt động tư vấn tài chính và tư vấn đầu tư chứng khoán');
    	$objPHPExcel->getActiveSheet()->getStyle('A'.$row_start)->applyFromArray($style_title);
    	$taichinh_chungkhoan = [
    		' Loại tư vấn',
    		' Số hợp đồng đã ký đầu kỳ',
    		' Số hợp đồng đã thanh lý trong kỳ',
    		' Số hợp đồng ký mới trong kỳ',
    		' Số hợp đồng còn hiệu lực cuối kỳ',
    		' Phí thu được trong tháng'
    	];
    	$row_start++;
    	$name_col =['A','D','F','H','J','L'];
    	for ($i=0; $i <count($name_col); $i++) {
    		$objPHPExcel->getActiveSheet()->setCellValue($name_col[$i].$row_start,$taichinh_chungkhoan[$i]);
    		$objPHPExcel->getActiveSheet()->getStyle($name_col[$i].$row_start)->applyFromArray($style_header);
    	}

    	$tvph = array(3,4,5);

    	$tvph_cot1= count($this->Contract->get_contract_value(null,null,array('month'=>$month_b,'year'=>$year_b,'progress'=>true,'name_service'=>$tvph))) + count($this->Contract->get_contract_value(null,null,array('month'=>$month_b,'year'=>$year_b,'date_signing'=>true,'name_service'=>$tvph)));

    	$tvph_cot2= count($this->Contract->get_contract_value(array('liquidated'),null,array('month'=>$month,'year'=>$year,'date_payment'=>true,'name_service'=>$tvph)));

    	$tvph_cot3= count($this->Contract->get_contract_value(null,null,array('month'=>$month,'year'=>$year,'date_signing'=>true,'name_service'=>$tvph)));
    	$data['tvph_cot3'] = $tvph_cot3;
    	$tvph_cot4 = ($tvph_cot1-$tvph_cot2)+$tvph_cot3;
    	if ($tvph_cot4<0) 
    		$tvph_cot4=0;
    		// ===========TU VAN CHUYEN DOI ==============
    	$tvcd =array(5);
    	$tvcd_cot1= count($this->Contract->get_contract_value(null,null,array('month'=>$month_b,'year'=>$year_b,'progress'=>true,'name_service'=>$tvcd))) + count($this->Contract->get_contract_value(null,null,array('month'=>$month_b,'year'=>$year_b,'date_signing'=>true,'name_service'=>$tvcd)));

    	$tvcd_cot2= count($this->Contract->get_contract_value(array('liquidated'),null,array('month'=>$month,'year'=>$year,'date_payment'=>true,'name_service'=>$tvcd)));
    	$tvcd_cot3= count($this->Contract->get_contract_value(null,null,array('month'=>$month,'year'=>$year,'date_signing'=>true,'name_service'=>$tvcd)));
    	$data['tvcd_cot3'] = $tvcd_cot3;
    	$tvcd_cot4 = ($tvcd_cot1-$tvcd_cot2)+$tvcd_cot3;
    	if ($tvcd_cot4<0) 
    		$tvcd_cot4  = 0;
    	
    		// ===========TU VAN KHAC==============
    	$tvk = array(6,7,8);
    	$tvk_cot1= count($this->Contract->get_contract_value(null,null,array('month'=>$month_b,'year'=>$year_b,'progress'=>true,'name_service'=>$tvk))) + count($this->Contract->get_contract_value(null,null,array('month'=>$month_b,'year'=>$year_b,'date_signing'=>true,'name_service'=>$tvk)));

    	$tvk_cot2= count($this->Contract->get_contract_value(array('liquidated'),null,array('month'=>$month,'year'=>$year,'date_payment'=>true,'name_service'=>$tvk)));

    	$tvk_cot3= count($this->Contract->get_contract_value(null,null,array('month'=>$month,'year'=>$year,'date_signing'=>true,'name_service'=>$tvk)));
    	$tvk_cot4 =($tvk_cot1-$tvk_cot2)+$tvk_cot3;
    	if ($tvk_cot4<0)
    		$tvk_cot4=0;

    	for ($i=$row_start; $i <=($row_start+8); $i++) {
    		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
    		$objPHPExcel->getActiveSheet()->mergeCells('D'.$i.':E'.$i);
    		$objPHPExcel->getActiveSheet()->mergeCells('F'.$i.':G'.$i);
    		$objPHPExcel->getActiveSheet()->mergeCells('H'.$i.':I'.$i);
    		$objPHPExcel->getActiveSheet()->mergeCells('J'.$i.':K'.$i);
    		$objPHPExcel->getActiveSheet()->mergeCells('L'.$i.':M'.$i); 
    	}
    	for ($i=$row_start-1; $i <=$row_start+7; $i++) {
    		$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(32);
    		for ($j=0; $j < count($name_all)-1 ; $j++) { 
    			$r=$i+1;
    			$objPHPExcel->getActiveSheet()->getStyle($name_all[$j].$r)->applyFromArray($style_table);
    		}
    	}
    	$row_start++;
    	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row_start,'I. Tư vấn đầu tư chứng khoán');
    	$objPHPExcel->getActiveSheet()->getStyle('A'.$row_start)->applyFromArray($style_font_bold);
    	$row_start++;
    	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row_start,'II. Tư vấn tài chính');
    	$objPHPExcel->getActiveSheet()->getStyle('A'.$row_start)->applyFromArray($style_font_bold);
    	$row_start++;
    	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row_start,'Tư vấn phát hành');

    	$objPHPExcel->getActiveSheet()->setCellValue('D'.$row_start,$tvph_cot1);
    	$objPHPExcel->getActiveSheet()->setCellValue('F'.$row_start,$tvph_cot2);
    	$objPHPExcel->getActiveSheet()->setCellValue('H'.$row_start,$tvph_cot3);
    	$objPHPExcel->getActiveSheet()->setCellValue('J'.$row_start,$tvph_cot4);

    	$row_start++;
    	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row_start,'Tư vấn chuyển đổi');

    	$objPHPExcel->getActiveSheet()->setCellValue('D'.$row_start,$tvcd_cot1);
    	$objPHPExcel->getActiveSheet()->setCellValue('F'.$row_start,$tvcd_cot2);
    	$objPHPExcel->getActiveSheet()->setCellValue('H'.$row_start,$tvcd_cot3);
    	$objPHPExcel->getActiveSheet()->setCellValue('J'.$row_start,$tvcd_cot4);

    	$row_start++;
    	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row_start,'Tư vấn khác');
    	$objPHPExcel->getActiveSheet()->setCellValue('D'.$row_start,$tvk_cot1);
    	$objPHPExcel->getActiveSheet()->setCellValue('F'.$row_start,$tvk_cot2);
    	$objPHPExcel->getActiveSheet()->setCellValue('H'.$row_start,$tvk_cot3);
    	$objPHPExcel->getActiveSheet()->setCellValue('J'.$row_start,$tvk_cot4);

    	$row_start++;
    	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row_start,'III. Dịch vụ khác');
    	$objPHPExcel->getActiveSheet()->getStyle('A'.$row_start)->applyFromArray($style_font_bold);
    	$objPHPExcel->getActiveSheet()->setCellValue('D'.$row_start,'');
    	$objPHPExcel->getActiveSheet()->setCellValue('F'.$row_start,'');
    	$objPHPExcel->getActiveSheet()->setCellValue('H'.$row_start,'');
    	$objPHPExcel->getActiveSheet()->setCellValue('J'.$row_start,'');
    	$row_start++;
    	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row_start,'Cộng');
    	$objPHPExcel->getActiveSheet()->setCellValue('D'.$row_start,'');
    	$objPHPExcel->getActiveSheet()->setCellValue('F'.$row_start,'');
    	$objPHPExcel->getActiveSheet()->setCellValue('H'.$row_start,'');
    	$objPHPExcel->getActiveSheet()->setCellValue('J'.$row_start,'');
    	$row_start++;
    	$tong1=$tvk_cot1+$tvcd_cot1+$tvph_cot1;
    	$tong2 =$tvk_cot2+$tvcd_cot2+$tvph_cot2;
    	$tong3= $tvk_cot3+$tvcd_cot3+$tvph_cot3;
    	$tong4= $tvk_cot4+$tvcd_cot4+$tvph_cot4;
    	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row_start,'TỔNG CỘNG');
    	$objPHPExcel->getActiveSheet()->getStyle('A'.$row_start)->applyFromArray($style_font_bold);
    	$objPHPExcel->getActiveSheet()->setCellValue('D'.$row_start,$tong1);
    	$objPHPExcel->getActiveSheet()->getStyle('D'.$row_start)->applyFromArray($style_font_bold);
    	$objPHPExcel->getActiveSheet()->setCellValue('F'.$row_start,$tong2);
    	$objPHPExcel->getActiveSheet()->getStyle('F'.$row_start)->applyFromArray($style_font_bold);
    	$objPHPExcel->getActiveSheet()->setCellValue('H'.$row_start,$tong3);
    	$objPHPExcel->getActiveSheet()->getStyle('H'.$row_start)->applyFromArray($style_font_bold);
    	$objPHPExcel->getActiveSheet()->setCellValue('J'.$row_start,$tong4);
    	$objPHPExcel->getActiveSheet()->getStyle('J'.$row_start)->applyFromArray($style_font_bold);

    	$filename = date('d-m-Y').'-TONG-HOP-HOP-DONG-'.$month.'-'.$year;
    	header('Content-Type: application/vnd.ms-excel');
    	header('Content-Disposition: attachment; filename='.$filename.'.xls');
    	header('Cache-Control: max-age=0');
    	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    	$objWriter->save('php://output');
    	exit;
    }

    function dowload_excel_contract(){
    	$month = $this->input->post('input_month');
    	$year = $this->input->post('input_year');
    	$arrParam['option'] = 'customer';
    	$arrParam['month'] = $month;
    	$arrParam['year'] =$year;
    	$arrParam['date_signing']=true;
    	$contract = $this->Contract->get_contract_value(null,null,$arrParam);
    	$contract_all = $this->Contract->get_contract_value();
    	$tong_tien_da_thu_trong_thang = $this->Contract->tong_tien_da_thu_trong_thang($arrParam);

    	$tong_tien_thanh_toan_don_tich = $this->Contract->tong_tien_thanh_toan_don_tich($arrParam);
    	$ds_ben_thu_3= $this->Contract->ds_ben_thu_3();

    	$contract_done = $this->Contract->get_contract_value('done',null,array('month'=>$month,'year'=>$year,'date_payment'=>true));

    	$contract_liquidated = $this->Contract->get_contract_value('liquidated',null,array('month'=>$month,'year'=>$year,'date_payment'=>true));

    	$contract_progress = $this->Contract->get_contract_value(null,null,array('month'=>$month,'year'=>$year,'progress'=>true));

    	$name_location = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id())->name;


    	$data['month'] = $month;
    	$data['year'] =$year;
    	// cau hinh file excel
    	$objPHPExcel = new PHPExcel();

    	$data_title =[
    		'Stt',
    		'Ngày ký HĐ',
    		'Số Hợp đồng',
    		'Nội dung',
    		'Đối tác',
    		'Giá trị hợp đồng chưa VAT (VNĐ)',
    		'VAT (VNĐ)',
    		'Hiện trạng HĐ',
    		'Ngày ký thanh lý/nghiệm thu',
    		'Số tiền đã thu trong tháng (VNĐ)',
    		'Số tiền thanh toán dồn tích (VNĐ)',
    		'CP dồn tích trả cho bên thứ 3 (VNĐ)',
    		'Ghi chú'
    	];
    	$style_center = array(
    		'alignment' => array(
    			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    			'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
    		),
    		'font' => array(
    			'size' => 12,
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
    	$style_backgroud=array(
    		'background'=>'ccc',
    	);
    	$style_font_bold =array(
    		'font' =>array(
    			'bold' => true,
    		)
    	);
// $objPHPExcel->getActiveSheet()->getRowDimension()->setRowHeight(30);
    	$objPHPExcel->getActiveSheet()->mergeCells('A2:M2');
    	$objPHPExcel->getActiveSheet()->mergeCells('A3:M3');
    	$objPHPExcel->getActiveSheet()->mergeCells('A4:M4');
    	$objPHPExcel->getActiveSheet()->setCellValue('A2','THỐNG KÊ HỢP ĐỒNG TƯ VẤN');
    	$objPHPExcel->getActiveSheet()->getStyle('A2')->applyFromArray($style_font_bold);
    	$objPHPExcel->getActiveSheet()->setCellValue('A3',$name_location);
    	$objPHPExcel->getActiveSheet()->setCellValue('A4','Tháng: '.$month.'/'.$year);

    	// $objPHPExcel->getActiveSheet()->getDefaultStyle()->applyFromArray($style_center_vertical);
    	$objPHPExcel->getActiveSheet()->getStyle('A2')->applyFromArray($style_center);
    	$objPHPExcel->getActiveSheet()->getStyle('A3')->applyFromArray($style_center);
    	$objPHPExcel->getActiveSheet()->getStyle('A4')->applyFromArray($style_center);
    	$objPHPExcel->getActiveSheet()->getStyle('A5:M5')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCCC');

    	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(30);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(30);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(30);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(30);
    	// tieu đề cho các cột
    	$name_col =['A','B','C','D','E','F','G','H','I','J','K','L','M'];
    	for ($i=0; $i <count($name_col); $i++) { 
    		$objPHPExcel->getActiveSheet()->setCellValue($name_col[$i].'5',$data_title[$i]);
    		$objPHPExcel->getActiveSheet()->getStyle($name_col[$i].'5')->applyFromArray($style_center);
    		$objPHPExcel->getActiveSheet()->getStyle($name_col[$i].'5')->applyFromArray($style_font_bold);

    	}

    	$objPHPExcel->getActiveSheet()->mergeCells('A6:M6');
    	$objPHPExcel->getActiveSheet()->setCellValue('A6','I.Hợp đồng nghiệm thu và thanh lý trong tháng');

    	$objPHPExcel->getActiveSheet()->getStyle('A6')->applyFromArray(array('font' =>array('bold' => true)));
    	$objPHPExcel->getActiveSheet()->getStyle('A7')->applyFromArray(array('font' =>array('bold' => true)));
    	$objPHPExcel->getActiveSheet()->getStyle('A6')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('e2e2e2');
    	$objPHPExcel->getActiveSheet()->mergeCells('A7:M7');
    	$objPHPExcel->getActiveSheet()->setCellValue('A7','A.Nghiệm thu trong tháng');
    	// hop dong da nghiem thu
    	$stt =1;
    	$tong_nghiem_thu_chua_vat=0;
    	$tong_nghiem_thu_co_vat=0;
    	$tong_da_thu_nt=0;
    	$tong_dt_nt =0;
    	$tong_tien_ben_3_nghiem_thu=0;
    	$row_st= 8;
    	foreach ($contract_done as $key => $value) {
    		$so_tien_co_vat =0;
    		$so_tien_chua_vat=0;
    		foreach ($contract_all as $key1 => $value1) {
    			if ($value1['id'] ==$value['id']) {
    				$so_tien_chua_vat=$value1['co_vat'];
    			}
    		}
    		$so_tien_co_vat = $so_tien_chua_vat*0.1;
    		$tong_nghiem_thu_chua_vat+=$so_tien_chua_vat;
    		$tong_nghiem_thu_co_vat +=$so_tien_co_vat;

    			// stt
    		$objPHPExcel->getActiveSheet()->setCellValue('A'.$row_st,$stt);
    			// ngay ky hd
    		$objPHPExcel->getActiveSheet()->setCellValue('B'.$row_st,date('d-m-Y',strtotime($value['date_signing'])));
    			// so HD
    		$objPHPExcel->getActiveSheet()->setCellValue('C'.$row_st,$value['code']);
    			// ND
    		$objPHPExcel->getActiveSheet()->setCellValue('D'.$row_st,$value['name_contract']);
    			// doi tac
    		$objPHPExcel->getActiveSheet()->setCellValue('E'.$row_st,$value['ten_doi_tac']);
    			// gia tri hd chua vat
    		$objPHPExcel->getActiveSheet()->setCellValue('F'.$row_st,number_format($so_tien_chua_vat));
    			// vat
    		$objPHPExcel->getActiveSheet()->setCellValue('G'.$row_st,number_format($so_tien_co_vat));
    			// hien trang hd
    		if ($value['trang_thai_hop_dong']=='progress') {
    			$hien_trang_hd= "Đang thực hiện";
    		}
    		if ($value['trang_thai_hop_dong']=='done') {
    			$hien_trang_hd= "Đã nghiệm thu";
    		}
    		if ($value['trang_thai_hop_dong']=='pause') {
    			$hien_trang_hd= "Tạm dừng/chưa thanh lý";
    		}
    		if ($value['trang_thai_hop_dong']=='liquidated') {
    			$hien_trang_hd= "Đã thanh lý";
    		}
    		$objPHPExcel->getActiveSheet()->setCellValue('H'.$row_st,$hien_trang_hd);
    			// ngay ky thanh ly/nghiem thu
    		$ngay_ky_thanh_ly ='';
    		foreach ($tong_tien_da_thu_trong_thang as $key => $value_nk) {
    			if ($value_nk['id']==$value['id'] && $value_nk['c_status']=='done') {
    				$ngay_ky_thanh_ly .= $value_nk['name'].': '.date('d-m-Y',strtotime($value_nk['date_payment'])); 
    			}
    		}
    		$objPHPExcel->getActiveSheet()->setCellValue('I'.$row_st,$ngay_ky_thanh_ly);
				// so tien da thu trong thang cua tung hop dong
    		$total =0;
    		foreach ($tong_tien_da_thu_trong_thang as $k => $val) {
    			if ($val['id']==$value['id'] && $val['c_status']=='done') {
    				if ($val['vat']=='published') {
    					$total += $val['price']/1.1;
    				}else{
    					$total +=$val['price'];
    				}
    			}
    		}
    		$tong_da_thu_nt+=$total;
    		$objPHPExcel->getActiveSheet()->setCellValue('J'.$row_st,number_format($total));
    			// thanh toans don tich
    		$tmp =0;
    		foreach ($tong_tien_thanh_toan_don_tich as $key_dt => $value_dt) {
    			if ($value_dt['id'] ==$value['id'] && $value_dt['c_status']=='done') {
    				if ($value_dt['vat']=='published') {
    					$tmp +=$value_dt['price']/1.1;
    				}else{
    					$tmp +=$value_dt['price'];
    				}

    			}
    		}
    		$tong_dt_nt +=$tmp;
    		$objPHPExcel->getActiveSheet()->setCellValue('K'.$row_st,number_format($tmp));
    				// chi phi don tich cho ben thu 3
    		$nt_ben_thu3 = 0;
    		foreach ($ds_ben_thu_3 as $a => $b) {
    			if ($b['contract_id']==$value['id']) {
    				$nt_ben_thu3=$b['chi_phi'];
    			}
    		}
    		$tong_tien_ben_3_nghiem_thu+=$nt_ben_thu3;
    		$objPHPExcel->getActiveSheet()->setCellValue('L'.$row_st,number_format($nt_ben_thu3));
    		$objPHPExcel->getActiveSheet()->setCellValue('M'.$row_st,'');
    		$stt++;
    		$row_st++;
    	}
    // Tổng nghiệm thu
    // $name_col =['A','B','C','D','E','F','G','H','I','J','K','L','M'];
    	$objPHPExcel->getActiveSheet()->mergeCells('A'.$row_st.':E'.$row_st);
    	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row_st,'Tổng nghiệm thu trong tháng');

    	$objPHPExcel->getActiveSheet()->getStyle('A'.$row_st)->applyFromArray(array('font' =>array('bold' => true)));
    	$objPHPExcel->getActiveSheet()->setCellValue('F'.$row_st,number_format($tong_nghiem_thu_chua_vat));
    	$objPHPExcel->getActiveSheet()->setCellValue('G'.$row_st,number_format($tong_nghiem_thu_co_vat));
    	$objPHPExcel->getActiveSheet()->setCellValue('J'.$row_st,number_format($tong_da_thu_nt));
    	$objPHPExcel->getActiveSheet()->setCellValue('K'.$row_st,number_format($tong_dt_nt));
    	$objPHPExcel->getActiveSheet()->setCellValue('L'.$row_st,number_format($tong_tien_ben_3_nghiem_thu));
    	$objPHPExcel->getActiveSheet()->setCellValue('M'.$row_st,'');

    	$objPHPExcel->getActiveSheet()->getStyle('F'.$row_st)->applyFromArray(array('font' =>array('bold' => true)));
    	$objPHPExcel->getActiveSheet()->getStyle('G'.$row_st)->applyFromArray(array('font' =>array('bold' => true)));
    	$objPHPExcel->getActiveSheet()->getStyle('J'.$row_st)->applyFromArray(array('font' =>array('bold' => true)));
    	$objPHPExcel->getActiveSheet()->getStyle('K'.$row_st)->applyFromArray(array('font' =>array('bold' => true)));
    	$objPHPExcel->getActiveSheet()->getStyle('L'.$row_st)->applyFromArray(array('font' =>array('bold' => true)));
    	$objPHPExcel->getActiveSheet()->getStyle('M'.$row_st)->applyFromArray(array('font' =>array('bold' => true)));
    	$row_st++;

    	// HOP DONG THANH LY
    	$objPHPExcel->getActiveSheet()->getStyle('A'.$row_st)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('e2e2e2');
    	$objPHPExcel->getActiveSheet()->mergeCells('A'.$row_st.':M'.$row_st);
    	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row_st,'B.Thanh lý trong tháng');
    	$objPHPExcel->getActiveSheet()->getStyle('A'.$row_st)->applyFromArray(array('font' =>array('bold' => true)));
    	$stt =1;
    	$tong_thanh_ly_chua_vat=0;
    	$tong_thanh_ly_co_vat=0;
    	$so_tien_da_thu_trong_thang_tl =0;
    	$tong_thanh_toan_don_tich=0;
    	$tong_tien_ben_3_thanh_ly=0;
    	$row_st++;
    	foreach ($contract_liquidated as $key => $value_tl) {
    		$so_tien_chua_vat=0;
    		$so_tien_chua_vat=0;
    		foreach ($contract_all as $key1 => $value1) {
    			if ($value1['id'] ==$value_tl['id']) {
    				$so_tien_chua_vat=$value1['co_vat'];
    			}
    		}
    		$so_tien_co_vat = $so_tien_chua_vat*0.1;
    		$tong_thanh_ly_chua_vat+=$so_tien_chua_vat;
    		$tong_thanh_ly_co_vat +=$so_tien_co_vat;
    		// if ($value_tl['vat']=='unpublished') {

    		// }elseif($value_tl['vat']=='published'){
    		// 	$tong_thanh_ly_chua_vat+=$so_tien_chua_vat;
    		// 	$tong_thanh_ly_co_vat +=$so_tien_co_vat;
    		// }

    		$objPHPExcel->getActiveSheet()->setCellValue('A'.$row_st,$stt);
    		$objPHPExcel->getActiveSheet()->setCellValue('B'.$row_st,date('d-m-Y',strtotime($value_tl['date_signing'])));
    		$objPHPExcel->getActiveSheet()->setCellValue('C'.$row_st,$value_tl['code']);
    		$objPHPExcel->getActiveSheet()->setCellValue('D'.$row_st,$value_tl['name_contract']);
    		$objPHPExcel->getActiveSheet()->setCellValue('E'.$row_st,$value_tl['ten_doi_tac']);

    		$objPHPExcel->getActiveSheet()->setCellValue('F'.$row_st,number_format($so_tien_chua_vat));

    		$objPHPExcel->getActiveSheet()->setCellValue('G'.$row_st,number_format($so_tien_co_vat));
    		if ($value_tl['trang_thai_hop_dong']=='progress') {
    			$hien_trang_hd= "Đang thực hiện";
    		}
    		if ($value_tl['trang_thai_hop_dong']=='done') {
    			$hien_trang_hd= "Đã nghiệm thu";
    		}
    		if ($value_tl['trang_thai_hop_dong']=='pause') {
    			$hien_trang_hd= "Tạm dừng/chưa thanh lý";
    		}
    		if ($value_tl['trang_thai_hop_dong']=='liquidated') {
    			$hien_trang_hd= "Đã thanh lý";
    		}
    		$objPHPExcel->getActiveSheet()->setCellValue('H'.$row_st,$hien_trang_hd);
    		$ngay_ky_thanh_ly='';
    		foreach ($tong_tien_da_thu_trong_thang as $key => $value_nk) {
    			if ($value_nk['id']==$value_tl['id'] && $value_nk['c_status']=='liquidated') {
    				$ngay_ky_thanh_ly.=$value_nk['name'].': '.date('d-m-Y',strtotime($value_nk['date_payment']));
    			}
    		}
    		$objPHPExcel->getActiveSheet()->setCellValue('I'.$row_st,$ngay_ky_thanh_ly);
    		$tmp=0;
    		foreach ($tong_tien_da_thu_trong_thang as $k => $value) {
    			if ($value['id']==$value_tl['id'] && $value['c_status']=='liquidated') {
    				if ($value['vat']=='published') {
    					$tmp += $value['price']/1.1;
    				}else{
    					$tmp +=$value['price'];
    				}
    			}
    		}
    		$so_tien_da_thu_trong_thang_tl +=$tmp;
    		$objPHPExcel->getActiveSheet()->setCellValue('J'.$row_st,number_format($tmp));
    		$tmp_tl =0;
    		foreach ($tong_tien_thanh_toan_don_tich as $key_dt => $value_dt_tl) {
    			if ($value_dt_tl['id'] == $value_tl['id'] && $value_dt_tl['c_status']=='liquidated' ) {
    				if ($value_dt_tl['vat']=='published') {
    					$tmp_tl +=$value_dt_tl['price']/1.1;
    				}else{
    					$tmp_tl +=$value_dt_tl['price'];
    				}
    			}
    		}
    		$tong_thanh_toan_don_tich +=$tmp_tl;
    		$objPHPExcel->getActiveSheet()->setCellValue('K'.$row_st,number_format($tmp_tl));
    		$tl_ben_thu_3 = 0;
    		foreach ($ds_ben_thu_3 as $a => $b) {
    			if ($b['contract_id']==$value_tl['id'] && $b['tt']=='liquidated') {
    				$tl_ben_thu_3=$b['chi_phi'];
    			}
    		}
    		$tong_tien_ben_3_thanh_ly+=$tl_ben_thu_3;
    		$objPHPExcel->getActiveSheet()->setCellValue('L'.$row_st, number_format($tl_ben_thu_3));
    		$objPHPExcel->getActiveSheet()->setCellValue('M'.$row_st,'');
    		$stt++;
    		$row_st++;
    	}
    // Tổng nghiệm thu
    // $name_col =['A','B','C','D','E','F','G','H','I','J','K','L','M'];
    	$objPHPExcel->getActiveSheet()->mergeCells('A'.$row_st.':E'.$row_st);
    	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row_st,'Tổng thanh lý trong tháng');
    	$objPHPExcel->getActiveSheet()->getStyle('A'.$row_st)->applyFromArray(array('font' =>array('bold' => true)));
    	$objPHPExcel->getActiveSheet()->setCellValue('F'.$row_st,number_format($tong_thanh_ly_chua_vat));
    	$objPHPExcel->getActiveSheet()->setCellValue('G'.$row_st,number_format($tong_thanh_ly_co_vat));

    	$objPHPExcel->getActiveSheet()->setCellValue('J'.$row_st,number_format($so_tien_da_thu_trong_thang_tl));
    	$objPHPExcel->getActiveSheet()->setCellValue('K'.$row_st,number_format($tong_thanh_toan_don_tich));

    	$objPHPExcel->getActiveSheet()->setCellValue('L'.$row_st,number_format($tong_tien_ben_3_thanh_ly));
    	$objPHPExcel->getActiveSheet()->setCellValue('M'.$row_st,'');


    	$objPHPExcel->getActiveSheet()->getStyle('F'.$row_st)->applyFromArray(array('font' =>array('bold' => true)));
    	$objPHPExcel->getActiveSheet()->getStyle('G'.$row_st)->applyFromArray(array('font' =>array('bold' => true)));
    	$objPHPExcel->getActiveSheet()->getStyle('J'.$row_st)->applyFromArray(array('font' =>array('bold' => true)));
    	$objPHPExcel->getActiveSheet()->getStyle('K'.$row_st)->applyFromArray(array('font' =>array('bold' => true)));
    	$objPHPExcel->getActiveSheet()->getStyle('L'.$row_st)->applyFromArray(array('font' =>array('bold' => true)));
    	$objPHPExcel->getActiveSheet()->getStyle('M'.$row_st)->applyFromArray(array('font' =>array('bold' => true)));
    	$row_st++;

    	$objPHPExcel->getActiveSheet()->mergeCells('A'.$row_st.':E'.$row_st);
    	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row_st,'Tổng nghiệm thu và thanh lý trong tháng');
    	$objPHPExcel->getActiveSheet()->getStyle('A'.$row_st)->applyFromArray(array('font' =>array('bold' => true)));
    	$objPHPExcel->getActiveSheet()->setCellValue('F'.$row_st,'');
    	$objPHPExcel->getActiveSheet()->setCellValue('G'.$row_st,'');
    	// $objPHPExcel->getActiveSheet()->setCellValue('F'.$row_st,number_format($tong_thanh_ly_chua_vat+$tong_nghiem_thu_chua_vat));
    	// $objPHPExcel->getActiveSheet()->setCellValue('G'.$row_st,number_format($tong_thanh_ly_co_vat+$tong_nghiem_thu_co_vat));
    			// tong tien thu trong thang
    	// $objPHPExcel->getActiveSheet()->setCellValue('J'.$row_st,number_format($so_tien_da_thu_trong_thang_tl+$tong_da_thu_nt));
    	$objPHPExcel->getActiveSheet()->setCellValue('J'.$row_st,'');
    			// tong don tich
    	$objPHPExcel->getActiveSheet()->setCellValue('K'.$row_st,number_format($tong_thanh_toan_don_tich+$tong_dt_nt));
    			// tong don tich ben 3
    	$objPHPExcel->getActiveSheet()->setCellValue('L'.$row_st,number_format($tong_tien_ben_3_nghiem_thu+$tong_tien_ben_3_thanh_ly));
    	$objPHPExcel->getActiveSheet()->setCellValue('M'.$row_st,'');



    	// II.Hợp đồng đang thực hiện dở dang tại thời điểm kết thúc tháng
    	$row_st++;
    	$objPHPExcel->getActiveSheet()->getStyle('A'.$row_st)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('e2e2e2');
    	$objPHPExcel->getActiveSheet()->mergeCells('A'.$row_st.':M'.$row_st);
    	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row_st,'II.Hợp đồng đang thực hiện dở dang tại thời điểm kết thúc tháng');
    	$objPHPExcel->getActiveSheet()->getStyle('A'.$row_st)->applyFromArray(array('font' =>array('bold' => true)));
    	$row_st++;
    	$stt =1;
    	$tong_dangth_chua_vat=0;
    	$tong_dangth_co_vat=0;
    	$so_tien_da_thu_trong_thang =0;
    	$tong_thanh_toan_don_tich_dangth=0;
    	$tong_tien_ben_3_dang_thuc_hien=0;

    	foreach ($contract_progress as $key => $value_progress) {
    		$so_tien_chua_vat=$value_progress['co_vat'];
    		$so_tien_co_vat = $so_tien_chua_vat*0.1;
    		$tong_dangth_chua_vat+=$so_tien_chua_vat;
    		$tong_dangth_co_vat +=$so_tien_co_vat;

    		// if ($value_progress['vat']=='unpublished') {
    		// 	$tong_dangth_chua_vat+=$so_tien_chua_vat;
    		// 	$tong_dangth_co_vat +=$so_tien_co_vat;
    		// }elseif($value_progress['vat']=='published'){
    		// 	$tong_dangth_chua_vat+=$so_tien_chua_vat;
    		// 	$tong_dangth_co_vat +=$so_tien_co_vat;
    		// }
    		$objPHPExcel->getActiveSheet()->setCellValue('A'.$row_st,$stt);
    		$objPHPExcel->getActiveSheet()->setCellValue('B'.$row_st,date('d-m-Y',strtotime($value_progress['date_signing'])));
    		$objPHPExcel->getActiveSheet()->setCellValue('C'.$row_st,$value_progress['code']);
    		$objPHPExcel->getActiveSheet()->setCellValue('D'.$row_st,$value_progress['name_contract']);
    		$objPHPExcel->getActiveSheet()->setCellValue('E'.$row_st,$value_progress['ten_doi_tac']);
    					// so tien chua vat
    		$objPHPExcel->getActiveSheet()->setCellValue('F'.$row_st,number_format($so_tien_chua_vat));
    					// so tien da vat

    		$objPHPExcel->getActiveSheet()->setCellValue('G'.$row_st,number_format($so_tien_co_vat));

    		if ($value_progress['trang_thai_hop_dong']=='progress') {
    			$hien_trang_hd= "Đang thực hiện";
    		}
    		if ($value_progress['trang_thai_hop_dong']=='done') {
    			$hien_trang_hd= "Đã nghiệm thu";
    		}
    		if ($value_progress['trang_thai_hop_dong']=='pause') {
    			$hien_trang_hd= "Tạm dừng/chưa thanh lý";
    		}
    		$objPHPExcel->getActiveSheet()->setCellValue('H'.$row_st,$hien_trang_hd);
    		$ngay_ky_thanh_ly ='';
    		foreach ($tong_tien_da_thu_trong_thang as $key => $value_nk) {
    			if ($value_nk['id']==$value_progress['id']) {
    				$ngay_ky_thanh_ly .= $value_nk['name'].': '.date('d-m-Y',strtotime($value_nk['date_payment']));; 
    			}
    		}
    		$objPHPExcel->getActiveSheet()->setCellValue('I'.$row_st,$ngay_ky_thanh_ly);
    		$tt=0;
    		foreach ($tong_tien_da_thu_trong_thang as $k => $val) {
    			if ($val['id']==$value_progress['id'] && $val['c_status']!='liquidated') {
    				if ($val['vat']=='published') {
    					$tt += $val['price']/1.1;
    				}else{
    					$tt +=$val['price'];
    				}
    			}
    		}
    		$so_tien_da_thu_trong_thang += $tt;
    		$objPHPExcel->getActiveSheet()->setCellValue('J'.$row_st,number_format($tt));
    		$tmp_p =0;
    		foreach ($tong_tien_thanh_toan_don_tich as $key_dt => $value_dt_p) {
    			if ($value_dt_p['id'] ==$value_progress['id'] && $value_dt_p['c_status']!='liquidated') {
    				if ($value_dt_p['vat']=='published') {
    					$tmp_p +=$value_dt_p['price']/1.1;
    				}else{
    					$tmp_p +=$value_dt_p['price'];
    				}
    			}
    		}
    		$tong_thanh_toan_don_tich_dangth +=$tmp_p;
    		$objPHPExcel->getActiveSheet()->setCellValue('K'.$row_st,number_format($tmp_p));

    		$sotien_chiben_thu3=0;
    		foreach ($ds_ben_thu_3 as $a => $b) {
    			if ($b['contract_id']==$value_progress['id'] && $b['tt']=='progress') {
    				$sotien_chiben_thu3=$b['chi_phi'];
    			}
    		}
    		$tong_tien_ben_3_dang_thuc_hien+=$sotien_chiben_thu3;
    		$objPHPExcel->getActiveSheet()->setCellValue('L'.$row_st,number_format($sotien_chiben_thu3));
    		$objPHPExcel->getActiveSheet()->setCellValue('M'.$row_st,'');
    		$stt++;
    		$row_st++;
    	}//end foreach

    	// Tổng HĐ đang thực hiện dở dang tại thời điểm kết thúc tháng

    	$objPHPExcel->getActiveSheet()->mergeCells('A'.$row_st.':E'.$row_st);
    	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row_st,'Tổng HĐ đang thực hiện dở dang tại thời điểm kết thúc tháng');
    	$objPHPExcel->getActiveSheet()->getStyle('A'.$row_st)->applyFromArray(array('font' =>array('bold' => true)));
    	$objPHPExcel->getActiveSheet()->setCellValue('F'.$row_st,number_format($tong_dangth_chua_vat));
    	$objPHPExcel->getActiveSheet()->setCellValue('G'.$row_st,number_format($tong_dangth_co_vat));

    	$objPHPExcel->getActiveSheet()->setCellValue('J'.$row_st,number_format($so_tien_da_thu_trong_thang));

    	$objPHPExcel->getActiveSheet()->setCellValue('K'.$row_st,number_format($tong_thanh_toan_don_tich_dangth));
    	$objPHPExcel->getActiveSheet()->setCellValue('L'.$row_st,number_format($tong_tien_ben_3_dang_thuc_hien));
    	$objPHPExcel->getActiveSheet()->setCellValue('M'.$row_st,'');

    	$objPHPExcel->getActiveSheet()->getStyle('F'.$row_st)->applyFromArray(array('font' =>array('bold' => true)));
    	$objPHPExcel->getActiveSheet()->getStyle('G'.$row_st)->applyFromArray(array('font' =>array('bold' => true)));
    	$objPHPExcel->getActiveSheet()->getStyle('J'.$row_st)->applyFromArray(array('font' =>array('bold' => true)));
    	$objPHPExcel->getActiveSheet()->getStyle('K'.$row_st)->applyFromArray(array('font' =>array('bold' => true)));
    	$objPHPExcel->getActiveSheet()->getStyle('L'.$row_st)->applyFromArray(array('font' =>array('bold' => true)));
    	$objPHPExcel->getActiveSheet()->getStyle('M'.$row_st)->applyFromArray(array('font' =>array('bold' => true)));



    	// HỢP ĐỒNG ĐĂNG KÝ MỚI TRONG THÁNG
    	$row_st++;
    	$objPHPExcel->getActiveSheet()->getStyle('A'.$row_st)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('e2e2e2');
    	$objPHPExcel->getActiveSheet()->mergeCells('A'.$row_st.':M'.$row_st);
    	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row_st,'III.Đăng ký mới trong tháng');
    	$objPHPExcel->getActiveSheet()->getStyle('A'.$row_st)->applyFromArray(array('font' =>array('bold' => true)));
    	$row_st++;
    	$stt=1;
    	$total_new = 0;
    	$tong_hdmoi_chua_vat=0;
    	$tong_hdmoi_co_vat=0;
    	$so_tien_da_thu_trong_thang=0;
    	$tong_thanh_toan_don_tich_moi=0;
    	$tong_sotien_chiben_thu3 =0;
    	foreach ($contract as $key => $valuenew) {
    		$so_tien_chua_vat=$valuenew['co_vat'];
    		$so_tien_co_vat = $so_tien_chua_vat*0.1;
    		$tong_hdmoi_chua_vat+=$so_tien_chua_vat;
    		$tong_hdmoi_co_vat +=$so_tien_co_vat;
    		// if ($valuenew['vat']=='unpublished') {
    		// 	$tong_hdmoi_chua_vat+=$so_tien_chua_vat;
    		// 	$tong_hdmoi_co_vat +=$so_tien_co_vat;
    		// }elseif($valuenew['vat']=='published'){
    		// 	$tong_hdmoi_chua_vat+=$so_tien_chua_vat;
    		// 	$tong_hdmoi_co_vat +=$so_tien_co_vat;
    		// }

    		$objPHPExcel->getActiveSheet()->setCellValue('A'.$row_st,$stt);
    		$objPHPExcel->getActiveSheet()->setCellValue('B'.$row_st,date('d-m-Y',strtotime($valuenew['date_signing'])));
    		$objPHPExcel->getActiveSheet()->setCellValue('C'.$row_st,$valuenew['code']);
    		$objPHPExcel->getActiveSheet()->setCellValue('D'.$row_st,$valuenew['name_contract']);
    		$objPHPExcel->getActiveSheet()->setCellValue('E'.$row_st,$valuenew['ten_doi_tac']);
    		$objPHPExcel->getActiveSheet()->setCellValue('F'.$row_st,number_format($so_tien_chua_vat));
    		
    		$objPHPExcel->getActiveSheet()->setCellValue('G'.$row_st,number_format($so_tien_co_vat));

    		if ($valuenew['trang_thai_hop_dong']=='progress') {
    			$objPHPExcel->getActiveSheet()->setCellValue('H'.$row_st,'Đang thực hiện');
    		}
    		if ($valuenew['trang_thai_hop_dong']=='done') {
    			$objPHPExcel->getActiveSheet()->setCellValue('H'.$row_st,'Đã nghiệm thu');
    		}
    		if ($valuenew['trang_thai_hop_dong']=='pause') {
    			$objPHPExcel->getActiveSheet()->setCellValue('H'.$row_st,'Tạm dừng chưa thanh lý');
    		}
    		if ($valuenew['trang_thai_hop_dong']=='liquidated') {
    			$objPHPExcel->getActiveSheet()->setCellValue('H'.$row_st,'Đã thanh lý');
    		}
    		$ngay_ky_thanh_ly='';
    		foreach ($tong_tien_da_thu_trong_thang as $key => $value_nk) {
    			if ($value_nk['id']==$valuenew['id']) {
    				$ngay_ky_thanh_ly .=$value_nk['name'].': '.date('d-m-Y',strtotime($value_nk['date_payment'])); 
    			}
    		}

    		$objPHPExcel->getActiveSheet()->setCellValue('I'.$row_st,$ngay_ky_thanh_ly);
				// Số tiền đã thu trong tháng
    		$totalnew =0;
    		foreach ($tong_tien_da_thu_trong_thang as $k => $val_new) {
    			if ($val_new['id']==$valuenew['id']) {
    				if ($val_new['vat']=='published') {
    					$totalnew = $val_new['price']/1.1;
    				}else{
    					$totalnew =$val_new['price'];
    				}
    			}	
    		}
    		$so_tien_da_thu_trong_thang += $totalnew;
    		$objPHPExcel->getActiveSheet()->setCellValue('J'.$row_st,number_format($totalnew));

				// số tiền thanh toán dồn tích
    		$tmp_new =0;
    		foreach ($tong_tien_thanh_toan_don_tich as $key_dt => $value_new) {
    			if ($value_new['id'] ==$valuenew['id']) {
    				if ($value_new['vat']=='published') {
    					$tmp_new +=$value_new['price']/1.1;
    				}else{
    					$tmp_new+=$value_new['price'];
    				}
    			}
    		}
    		$tong_thanh_toan_don_tich_moi +=$tmp_new;
    		$objPHPExcel->getActiveSheet()->setCellValue('K'.$row_st,number_format($tmp_new));
				// SO TIEN CHIA CHO BEN THU 3
    		$sotien_chiben_thu3 = 0;
    		foreach ($ds_ben_thu_3 as $key => $bnew) {
    			if ($bnew['contract_id']==$valuenew['id']) {
    				$sotien_chiben_thu3=$bnew['chi_phi'];
    			}
    		}
    		$tong_sotien_chiben_thu3+=$sotien_chiben_thu3;
    		$objPHPExcel->getActiveSheet()->setCellValue('L'.$row_st,number_format($sotien_chiben_thu3));
    		$objPHPExcel->getActiveSheet()->setCellValue('M'.$row_st,'');
    		$stt++;
    		$row_st++;
    	}
	// Tổng ký mới tại thời điểm kết thúc tháng

    	$objPHPExcel->getActiveSheet()->mergeCells('A'.$row_st.':E'.$row_st);
    	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row_st,'Tổng ký mới tại thời điểm kết thúc tháng');
    	$objPHPExcel->getActiveSheet()->getStyle('A'.$row_st)->applyFromArray(array('font' =>array('bold' => true)));
    	$objPHPExcel->getActiveSheet()->setCellValue('F'.$row_st,number_format($tong_hdmoi_chua_vat));
    	$objPHPExcel->getActiveSheet()->setCellValue('G'.$row_st,number_format($tong_hdmoi_co_vat));
		// tong tien da thu trong thang cua hop dong moi
    	$objPHPExcel->getActiveSheet()->setCellValue('J'.$row_st,number_format($so_tien_da_thu_trong_thang));
			// TOng thanh toán don tich
    	$objPHPExcel->getActiveSheet()->setCellValue('K'.$row_st,number_format($tong_thanh_toan_don_tich_dangth+$tong_thanh_toan_don_tich+$tong_dt_nt));
    	$objPHPExcel->getActiveSheet()->setCellValue('L'.$row_st,number_format($tong_sotien_chiben_thu3));
    	$objPHPExcel->getActiveSheet()->setCellValue('M'.$row_st,'');

    	$objPHPExcel->getActiveSheet()->getStyle('F'.$row_st)->applyFromArray(array('font' =>array('bold' => true)));
    	$objPHPExcel->getActiveSheet()->getStyle('G'.$row_st)->applyFromArray(array('font' =>array('bold' => true)));
    	$objPHPExcel->getActiveSheet()->getStyle('J'.$row_st)->applyFromArray(array('font' =>array('bold' => true)));
    	$objPHPExcel->getActiveSheet()->getStyle('K'.$row_st)->applyFromArray(array('font' =>array('bold' => true)));
    	$objPHPExcel->getActiveSheet()->getStyle('L'.$row_st)->applyFromArray(array('font' =>array('bold' => true)));
    	$objPHPExcel->getActiveSheet()->getStyle('M'.$row_st)->applyFromArray(array('font' =>array('bold' => true)));
    	// FOOTER EXCEL
    	$footer = $row_st+2;

    	$objPHPExcel->getActiveSheet()->mergeCells('G'.$footer.':I'.$footer);
    	
    	$objPHPExcel->getActiveSheet()->setCellValue('G'.$footer,'Hà Nội, ngày '.date('d').' tháng '.date('m').' năm '.date('Y'));
    	$footer++;

    	$objPHPExcel->getActiveSheet()->setCellValue('G'.$footer,'PHÒNG TƯ VẤN TÀI CHÍNH');
    	$objPHPExcel->getActiveSheet()->getStyle('G'.$footer)->applyFromArray(array('font' =>array('bold' => true)));
    	$objPHPExcel->getActiveSheet()->mergeCells('C'.$footer.':D'.$footer);
    	$objPHPExcel->getActiveSheet()->setCellValue('C'.$footer,'PHÒNG KẾ TOÁN TÀI CHÍNH');
    	$objPHPExcel->getActiveSheet()->getStyle('c'.$footer)->applyFromArray(array('font' =>array('bold' => true)));

    	for ($i=2; $i <$footer+1; $i++) { 
    		$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(32);
    		for ($j=0; $j < count($name_col) ; $j++) { 
    			$t=$i+3;
    			if ($i<$footer-5) {
    				$objPHPExcel->getActiveSheet()->getStyle($name_col[$j].$t)->applyFromArray($style_center_vertical);
    			}
    		}
    	}

    	$filename = date('d-m-Y').'-THONG-KE-HOP-DONG_THANG-'.$month.'-'.$year;
    	header('Content-Type: application/vnd.ms-excel');
    	header('Content-Disposition: attachment; filename='.$filename.'.xls');
    	header('Cache-Control: max-age=0');
    	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    	$objWriter->save('php://output');
    	exit;
    }
    
    function revenue(){
    	// $data['da_dang_thuc_hien'] = $this->Contract->contract_location(array('date_end'=>true,'check_tp'=>'TD','ctstatus'=>array('progress'),'location'=>'HSC','date_start'=>'01-01-2018','date_end'=>'01-01-2020'));
    	// echo $this->db->last_query(); die();
    	// echo "<pre>"; print_r($data['da_dang_thuc_hien'] ); die();
    	$this->check_action_permission('bc_du_bao_doanh_thu');
    	$this->load->view('reports/contract/bao_cao_doanh_thu');
    }
    function export_revenue_data(){
    	$date_start = $this->input->post('date_start');
    	$date_end = $this->input->post('date_end');
    	$data['check'] ='DBDT';
    	$data['title'] = "Dự báo doanh thu sẽ ghi nhận từ ngày $date_start đến ngày $date_end";
    	$location_code = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id())->code;
    	$data['name_location']=$this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id())->name;
    	$data['da_dang_thuc_hien'] = $this->Contract->contract_location(array('date_end'=>true,'check_tp'=>'TD','ctstatus'=>array('progress'),'location'=>$location_code,'date_start'=>$date_start,'date_end'=>$date_end));
    	$this->load->view('reports/contract/table_report',$data);
    }
    function export_revenue_word(){
    	$date_start = $this->input->post('date_start');
    	$date_end = $this->input->post('date_end');
    	$title = "Dự báo doanh thu sẽ ghi nhận từ ngày $date_start đến ngày $date_end";

    	$name_location=$this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id())->name;
    	$location_code = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id())->code;
    	$da_dang_thuc_hien = $this->Contract->contract_location(array('date_end'=>true,'check_tp'=>'TD','ctstatus'=>array('progress'),'location'=>$location_code,'date_start'=>$date_start,'date_end'=>$date_end));

    	$khu_vuc = "Khu vực: $name_location";
    	$objPHPExcel = new PHPExcel();
    	$data_title =[
    		'STT',
    		'Tên dự án',
    		'Tên dịch vụ',
    		'Tên hợp đồng',
    		'Ngày kết thúc dự kiến',
    		'Giá trị hợp đồng (Trừ VAT)',
    	];
    	$style_title= array(
    		'font' => array(
    			'bold' => true
    		),
    		'alignment' => array(
    			'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
    		)
    	);
    	$style_center = array(
    		'alignment' => array(
    			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    			'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
    		),
    		'font' => array(
    			'size' => 11,
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
    	$style_backgroud=array(
    		'background'=>'ccc',
    	);
    	$style_font_bold =array(
    		'font' =>array(
    			'bold' => true,
    		)
    	);
    	$style_header = array(
    		'font' => array(
    			'bold' => true
    		),
    		'alignment' => array(
    			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    		),
    	);
    	$style_right_bold= array(
    		'font' => array(
    			'bold' => true
    		),
    		'alignment' => array(
    			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
    		),
    	);
    	$style_right = array(
    		'alignment' => array(
    			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
    		),
    	);
// $objPHPExcel->getActiveSheet()->getRowDimension()->setRowHeight(30);
    	$objPHPExcel->getActiveSheet()->mergeCells('A2:F2');
    	$objPHPExcel->getActiveSheet()->mergeCells('A3:F3');
    	$objPHPExcel->getActiveSheet()->setCellValue('A2',$title);
    	$objPHPExcel->getActiveSheet()->setCellValue('A3',$khu_vuc);

    	// $objPHPExcel->getActiveSheet()->getDefaultStyle()->applyFromArray($style_center_vertical);
    	$objPHPExcel->getDefaultStyle()->applyFromArray(array('font'=>array('size'=>11,'name'=>'Times New Roman')));
    	$objPHPExcel->getActiveSheet()->getStyle('A2')->applyFromArray($style_center);
    	$objPHPExcel->getActiveSheet()->getStyle('A3')->applyFromArray($style_center);
    	
    	$objPHPExcel->getDefaultStyle()->getAlignment()->setWrapText(true);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
    	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);

    	$objPHPExcel->getActiveSheet()->getStyle('A2')->applyFromArray($style_title);
    	$name_col =['A','B','C','D','E','F'];
    	for ($i=0; $i <count($name_col); $i++) {
    		$objPHPExcel->getActiveSheet()->setCellValue($name_col[$i].'5',$data_title[$i]);
    		$objPHPExcel->getActiveSheet()->getStyle($name_col[$i].'5')->applyFromArray($style_header);
    	}
    	$row_start=6;
    	$tong=0;
    	if (!empty($da_dang_thuc_hien)) {
    		$stt=1;
    		
    		foreach ($da_dang_thuc_hien as $key => $value) 
    		{
    			$objPHPExcel->getActiveSheet()->setCellValue('A'.$row_start,$stt);
    			$objPHPExcel->getActiveSheet()->setCellValue('B'.$row_start,$value['name_task']);
    			$objPHPExcel->getActiveSheet()->setCellValue('C'.$row_start,$value['item_name']);
    			$objPHPExcel->getActiveSheet()->setCellValue('D'.$row_start,$value['name_contract']);
    			$objPHPExcel->getActiveSheet()->setCellValue('E'.$row_start,date('d-m-Y',strtotime($value['date_end'])));
    			$objPHPExcel->getActiveSheet()->getStyle('E'.$row_start)->applyFromArray($style_right);

    			$objPHPExcel->getActiveSheet()->setCellValue('F'.$row_start,number_format($value['co_vat']));
    			$stt++;
    			$tong+=$value['co_vat'];
    			$row_start++;
    		}
    	}

    	$objPHPExcel->getActiveSheet()->mergeCells('A'.$row_start.':E'.$row_start);
    	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row_start,'Tổng doanh thu dự kiến');
    	$objPHPExcel->getActiveSheet()->getStyle('A'.$row_start)->applyFromArray($style_header);
    	$objPHPExcel->getActiveSheet()->setCellValue('F'.$row_start,number_format($tong));
    	
    	$objPHPExcel->getActiveSheet()->getStyle('F'.$row_start)->applyFromArray($style_right_bold);

    	for ($i=1; $i <$row_start+2; $i++) { 
    		$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(30);
    		
    		for ($j=0; $j < count($name_col) ; $j++) { 
    			$t=$i+4;
    			if ($i<$row_start-3) {
    				$objPHPExcel->getActiveSheet()->getStyle($name_col[$j].$t)->applyFromArray($style_center_vertical);
    			}
    		}
    	}
    	$row_start++;
    	$objPHPExcel->getActiveSheet()->mergeCells('E'.$row_start.':F'.$row_start);
    	$objPHPExcel->getActiveSheet()->setCellValue('E'.$row_start,'TRƯỞNG PHÒNG TVTCDN-'.mb_strtoupper($name_location));
    	$objPHPExcel->getActiveSheet()->getStyle('E'.$row_start)->applyFromArray($style_header);

    	header('Content-Type: application/vnd.ms-excel');
    	header('Content-Disposition: attachment; filename='.$title.'.xls');
    	header('Cache-Control: max-age=0');
    	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    	$objWriter->save('php://output');
    	exit;
    }
}
?>
