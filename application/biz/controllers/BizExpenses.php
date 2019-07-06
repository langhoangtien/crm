<?php
require_once (APPPATH . "controllers/Expenses.php");

class BizExpenses extends Expenses 
{
    protected $_paginator = array(
        'per_page' => 10,
        'uri_segment' => 3
    );

	function __construct() {
		parent::__construct();
		$this->load->helper('bizexcel');
        $this->load->model('Employee');
        $this->load->model('Supplier');
        $this->load->model('Contract');

	}

    public function shift() {
        $data['currrent_page']   = $this->uri->segment(3, 1);
        $data['controller_name'] = $this->uri->segment(1);
        $shift_category_id       = $this->config->item('shift_category_id');
        if($shift_category_id > 0)
            $category = $this->Category->getItem($shift_category_id);

        if(empty($category))
            redirect('404.html');
        else
            $data['category'] = $category;

        $data['total_rows'] = $this->Expense->count_all();

        $this->load->view('expenses/shift_view', $data);
    }

    public function shift_store() {
        $post  = $this->input->post();
        if(!empty($post)) {
            $arrParam = array_merge($post, $this->input->get());
            $arrParam['paginator'] = $this->_paginator;
            $arrParam['page']      =  $this->uri->segment(3, 1);
            $config['base_url']    = base_url() . 'tasks/shift_store';
            $config['total_rows']  = $this->Expense->count_item($arrParam);

            $config['per_page'] = $arrParam['paginator']['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
            //$config['per_page'] = $arrParam['paginator']['per_page'] = 1;
            $config['uri_segment'] = 3;
            $config['use_page_numbers'] = TRUE;

            $items = $this->Expense->list_item($arrParam);

            $this->load->library("pagination");
            $this->pagination->initialize($config);
            $this->pagination->createConfig('front-end');

            $pagination = $this->pagination->create_ajax();

            $result = array('count'=> $config['total_rows'], 'items'=>$items, 'pagination'=>$pagination);

            echo json_encode($result);
        }
    }

    public function delete_item() {
        $post = $this->input->post();
        if(!empty($post)) {
            $flag = 'true';
            $this->Expense->delete_list($post['cid']);
            $msg = 'Thực hiện tác vụ thành công.';
            $response = array('flag'=>$flag, 'msg'=>$msg);
            echo json_encode($response);
        }
    }

	public function reprint($id = 0) {
		$data = [];
		$data['expense_info'] = $this->Expense->get_info($id);
		$typeOfPrint = 'A4.php';
		$data['print_block_html'] = $this->load->view('expenses/partials/' . $typeOfPrint, $data, TRUE);
		$this->load->view('expenses/reprint', $data);
	}
	
	public function export_excel($id = 0) {
		$bizExcel = new BizExcel('A2.xlsx');
		$excelContent = $bizExcel->setExtraData($this->getExtraDataForExportExpense($id))
							->generateFile(false);
		$this->load->helper('download');
		force_download('export_expense.xlsx', $excelContent);
		exit;
	}

    function load_receiver_section() {
    	$row_constant = $this->Expense->get_contract($this->Employee->get_logged_in_employee_current_location_id());
        $post = $this->input->post();

        
        $arrParams = array_merge($post, $this->input->get());
        if(!empty($post)) {
            $data = array();
            if($post['expense_id'] != -1) {
                $expense_info = $this->Expense->get_item(array('id'=>$post['expense_id']));
            }
            if($post['options'] == 'receiving') {
                if(isset($expense_info) && $expense_info[0]['receiving_id'] > 0) {
                    $data['receiving_id'] = $expense_info[0]['receiving_id'];
                    $data['ma_don_nhap_hang'] = $this->config->item('receive_prefix') . ' ' . $expense_info[0]['receiving_id'];
                }
                $data['contract_id'] = $expense_info[0]['contract_id'];
                $data['row_constant']=$row_constant;
                $this->load->view("expenses/partials/receiver_supplier", $data);
            }elseif($post['options'] == 'sale') {
                if(isset($expense_info) && isset($data['cost_constitute_product'])) $data['cost_constitute_product'] = $expense_info['cost_constitute_product'];
                if(isset($expense_info) && $expense_info[0]['sale_id'] > 0) {
                    $sale_info                       = $this->Sale->getInfo($expense_info[0]['sale_id']);
                    $data['sale_id']                 = $expense_info[0]['sale_id'];

                    if(!empty($sale_info['code']))
                        $data['sale_code'] = $sale_info['code'];
                    else
                        $data['sale_code'] = $this->config->item('sale_prefix') . ' ' . $expense_info[0]['sale_id'];
               
                }
                $data['contract_id'] = $expense_info[0]['contract_id'];
                $data['row_constant']=$row_constant;
                $this->load->view("expenses/partials/receiver_customer", $data);
            }
        }
    }
	
	protected function getExtraDataForExportExpense($id = 0) {
		$expense_info = $this->Expense->get_info($id);
		$receiver = $this->Employee->get_info ( $expense_info->employee_id );
		return [
				[
					'cell' => 'A1',
					'value' => $this->config->item('company')
				],
				[
					'cell' => 'A2',
					'value' => $this->Location->get_info_for_key('address', isset($override_location_id) ? $override_location_id : FALSE)
				],
				[
					'cell' => 'I3',
					'value' => date(get_date_format(), strtotime($expense_info->expense_date))
				],
				[
					'cell' => 'C9',
					'value' => $receiver->first_name . ' ' . $receiver->last_name
				],
				[
					'cell' => 'C10',
					'value' => $this->Location->get_info_for_key('address', isset($override_location_id) ? $override_location_id : FALSE)
				],
				[
					'cell' => 'C11',
					'value' => $expense_info->expense_description
				],
				[
					'cell' => 'C12',
					'value' => NumberFormatToCurrency($expense_info->expense_amount) . $this->config->item('currency_symbol')
				],
				[
					'cell' => 'A13',
					'value' => '(Số tiền viết bằng chữ):' . getStringNumber((int) $expense_info->expense_amount)
				],
		];
	}
	
	function index($offset = 0) {
		$data=array();
		$data['current_page'] = $this->uri->segment(3,1);
		$data['controller_name'] = $this->uri->segment(1);
	
		$this->load->view('expenses/manage', $data);
	}

	function list_store() {

		$post = $this->input->post();
		$arrParam = array_merge($post, $this->input->get());

		if (!empty($post)) {
			
			$arrayParam['location_id'] = $this->Employee->get_logged_in_employee_current_location_id();

			$key_filter = 'count_in_expense';
			$_SESSION[$key_filter] = array();
			$arrParam['key_filter'] = $key_filter;
			$this->_paginator['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
			$arrParam['paginator'] = $this->_paginator;
			$arrParam['page'] = $this->uri->segment(3, 1);
 
			$config['base_url'] = base_url() . 'expenses/list_store';
			$config['per_page'] = $arrParam['paginator']['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
			$config['uri_segment'] = 3;
			$config['use_page_numbers'] = TRUE;
			$config['total_rows'] = $this->Expense->count_full($arrParam);
			$items = $this->Expense->full_item($arrParam);
			
			$this->load->library("pagination");
			$this->pagination->initialize($config);
			$this->pagination->createConfig('front-end');
			$STT = ($arrParam['page']-1)*$config['per_page'];
			$pagination = $this->pagination->create_ajax();
			
			$html = $this->load->view('expenses/row/list', array('items'=>$items, 'STT' => $STT), true);
			
			$count_sale_expense = $this->Expense->count_item_by_filter(array('key_filter'=>'count_sale_expense'));
			$count_receiving_expense = $this->Expense->count_item_by_filter(array('key_filter'=>'count_receiving_expense'));
			
			$result = array('count'=> $config['total_rows'], 'html_string'=>$html, 'pagination'=>$pagination,'count_sale_expense'=>$count_sale_expense,  'count_receiving_expense' =>$count_receiving_expense);
			echo json_encode($result);
		}
	}

	function sale_expense($offset = 0) {
		$data=array();
		$data['current_page'] = $this->uri->segment(3,1);
		$data['controller_name'] = $this->uri->segment(1);
	
		$this->load->view('expenses/sale_expense', $data);
	}

	function sale_store() {

		$post = $this->input->post();

		$arrParam = array_merge($post, $this->input->get());

		if (!empty($post)) {
		
			$arrayParam['location_id'] = $this->Employee->get_logged_in_employee_current_location_id();

			$key_filter = 'count_sale_expense';
			$_SESSION[$key_filter] = array();
			$arrParam['key_filter'] = $key_filter;
			$this->_paginator['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
			$arrParam['paginator'] = $this->_paginator;
			$arrParam['page'] = $this->uri->segment(3, 1);
 
			$config['base_url'] = base_url() . 'expenses/sale_store';
			$config['per_page'] = $arrParam['paginator']['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
			$config['uri_segment'] = 3;
			$config['use_page_numbers'] = TRUE;
			$config['total_rows'] = $this->Expense->count_full($arrParam);
			
			$items = $this->Expense->full_item($arrParam);
			$contract = $this->Contract->get_code_contract();
			// var_dump($items);
			$this->load->library("pagination");
			$this->pagination->initialize($config);
			$this->pagination->createConfig('front-end');
			$STT = ($arrParam['page']-1)*$config['per_page'];	
			$pagination = $this->pagination->create_ajax();
			
			$html = $this->load->view('expenses/row/sale', array('items'=>$items, 'STT' => $STT,'contract'=>$contract), true);
			
			$count_in_expense = $this->Expense->count_item_by_filter(array('key_filter'=>'count_in_expense'));
			$count_receiving_expense = $this->Expense->count_item_by_filter(array('key_filter'=>'count_receiving_expense'));
			
			$result = array('count'=> $config['total_rows'], 'html_string'=>$html, 'pagination'=>$pagination,'count_in_expense'=>$count_in_expense,  'count_receiving_expense' => $count_receiving_expense);
			echo json_encode($result);
		}
	}

	function receiving_expense($offset = 0) {
		$data=array();
		$data['current_page'] = $this->uri->segment(3,1);
		$data['controller_name'] = $this->uri->segment(1);

		$this->load->view('expenses/receiving_expense', $data);
	}

	function receiving_store() {

		$post = $this->input->post();
		$arrParam = array_merge($post, $this->input->get());

		if (!empty($post)) {
		
			$arrayParam['location_id'] = $this->Employee->get_logged_in_employee_current_location_id();

			$key_filter = 'count_receiving_expense';
			$_SESSION[$key_filter] = array();
			$arrParam['key_filter'] = $key_filter;
			$this->_paginator['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
			$arrParam['paginator'] = $this->_paginator;
			$arrParam['page'] = $this->uri->segment(3, 1);
 
			$config['base_url'] = base_url() . 'expenses/receiving_store';
			$config['per_page'] = $arrParam['paginator']['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
			$config['uri_segment'] = 3;
			$config['use_page_numbers'] = TRUE;
			$config['total_rows'] = $this->Expense->count_full($arrParam);
			
			$items = $this->Expense->full_item($arrParam);
			
			$this->load->library("pagination");
			$this->pagination->initialize($config);
			$this->pagination->createConfig('front-end');
				$STT = ($arrParam['page']-1)*$config['per_page'];
			$pagination = $this->pagination->create_ajax();
			
			$html = $this->load->view('expenses/row/receiving', array('items'=>$items, 'STT' => $STT), true);
			
			$count_in_expense = $this->Expense->count_item_by_filter(array('key_filter'=>'count_in_expense'));
			$count_sale_expense = $this->Expense->count_item_by_filter(array('key_filter'=>'count_sale_expense'));
	
			$result = array('count'=> $config['total_rows'], 'html_string'=>$html, 'pagination'=>$pagination,'count_sale_expense'=>$count_sale_expense, 'count_in_expense'=>$count_in_expense  );
			echo json_encode($result);
		}
	}
}
?>

