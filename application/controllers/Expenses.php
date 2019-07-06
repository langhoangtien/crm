<?php

require_once ("Secure_area.php");
require_once ("interfaces/Idata_controller.php");

class Expenses extends Secure_area implements Idata_controller {

    public $_thong_bao  = array(
              'required'              => '%s. không được rỗng',
              'trim'                  => '%s. không được chứa khoảng trắng' ,
              '_check_phone_number'    => '%s. không đúng định dạng ext: 888.888.888 or 888 888 888',
              'valid_email'           => '%s. chưa đúng định dạng',
              'is_unique'             => '%s. đã tồn tại',
              'validate_unique_custom'=>  '%s. đã tồn tại',
              ); 
    protected $_fileError = array();   

    function __construct() {

    parent::__construct('expenses');
	$this->load->model('Expense');
	$this->load->model('Category');
    $this->load->model('Sale');
    $this->load->model('Location');
    $this->load->model('Receiving');
  	$this->lang->load('expenses');
  	$this->lang->load('module');


    }

    function index($offset = 0) {
        $params = $this->session->userdata('expenses_search_data') ? $this->session->userdata('expenses_search_data') : array('offset' => 0, 'order_col' => 'id', 'order_dir' => 'desc', 'search' => FALSE);

        if ($offset != $params['offset']) {
            redirect('expenses/index/' . $params['offset']);
        }

        $this->check_action_permission('search');
        $config['base_url'] = site_url('expenses/sorting');
        $config['total_rows'] = $this->Expense->count_all();
        $config['per_page'] = $this->config->item('number_of_items_per_page') ? (int) $this->config->item('number_of_items_per_page') : 20;
        $data['controller_name'] = $this->_controller_name;
        $data['per_page'] = $config['per_page'];
        $data['search'] = $params['search'] ? $params['search'] : "";
        if ($data['search']) {
            $config['total_rows'] = $this->Expense->search_count_all($data['search']);
            $table_data = $this->Expense->search($data['search'], $data['per_page'], $params['offset'], $params['order_col'], $params['order_dir']);
        } else {
            $config['total_rows'] = $this->Expense->count_all();
            $table_data = $this->Expense->get_all($data['per_page'], $params['offset'], $params['order_col'], $params['order_dir']);
        }
        $this->load->library('pagination');$this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['order_col'] = $params['order_col'];
        $data['order_dir'] = $params['order_dir'];
        $data['total_rows'] = $config['total_rows'];
        $data['manage_table'] = get_expenses_manage_table($table_data, $this);
        $this->load->view('expenses/manage', $data);
    }

    function sorting() {
        $this->check_action_permission('search');

        $search = $this->input->post('search') ? $this->input->post('search') : "";
        $per_page = $this->config->item('number_of_items_per_page') ? (int) $this->config->item('number_of_items_per_page') : 20;

        $offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
        $order_col = $this->input->post('order_col') ? $this->input->post('order_col') : 'id';
        $order_dir = $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc';

        $expenses_search_data = array('offset' => $offset, 'order_col' => $order_col, 'order_dir' => $order_dir, 'search' => $search);
        $this->session->set_userdata("expenses_search_data", $expenses_search_data);

        if ($search) {
            $config['total_rows'] = $this->Expense->search_count_all($search);
            $table_data = $this->Expense->search($search, $per_page, $this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'id', $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc');
        } else {
            $config['total_rows'] = $this->Expense->count_all();
            $table_data = $this->Expense->get_all($per_page, $this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'id', $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc');
        }
        $config['base_url'] = site_url('expenses/sorting');
        $config['per_page'] = $per_page;
        $this->load->library('pagination');$this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['manage_table'] = get_expenses_manage_table_data_rows($table_data, $this);
        echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination']));
    }

    function search() {
 		//allow parallel searchs to improve performance.
 		session_write_close();
		 
        $this->check_action_permission('search');

        $search = $this->input->post('search');
        $offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
        $order_col = $this->input->post('order_col') ? $this->input->post('order_col') : 'id';
        $order_dir = $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc';

        $expenses_search_data = array('offset' => $offset, 'order_col' => $order_col, 'order_dir' => $order_dir, 'search' => $search);
        $this->session->set_userdata("expenses_search_data", $expenses_search_data);
        $per_page = $this->config->item('number_of_items_per_page') ? (int) $this->config->item('number_of_items_per_page') : 20;
        $search_data = $this->Expense->search($search, $per_page, $this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'id', $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc');
        $config['base_url'] = site_url('expenses/search');
        $config['total_rows'] = $this->Expense->search_count_all($search);
        $config['per_page'] = $per_page;
        $this->load->library('pagination');$this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['manage_table'] = get_expenses_manage_table_data_rows($search_data, $this);
        echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination']));
    }

    function clear_state() {
        $this->session->unset_userdata('expenses_search_data');
        redirect('expenses');
    }

    /*
      Gives search suggestions based on what is being searched for
     */

    function suggest() {
 		//allow parallel searchs to improve performance.
 		session_write_close();
		 
        $suggestions = $this->Expense->get_search_suggestions($this->input->get('term'), 100);
        echo json_encode($suggestions);
    }

    function view($expense_id = -1, $redirect_code = 0) {
         
        $this->check_action_permission('add_update');
        $shift_category_id       = $this->config->item('shift_category_id');
        $logged_employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
        $data['expense_info'] = $this->Expense->get_info($expense_id);
        $data['logged_in_employee_id'] = $logged_employee_id;
        $data['all_modules'] = $this->Module->get_all_modules();
        $data['controller_name'] = $this->_controller_name;

        $data['redirect_code'] = $redirect_code;
		  $data['categories'][''] = lang('common_select_category');
		  
		  if ($this->config->item('track_cash'))
		  {
	  			$data['registers'] = array();
				$data['registers'][''] = lang('common_none');
			  
			  foreach($this->Register->get_all_open()->result() as $register)
			  {
				  $data['registers'][$register->register_id] = $register->name;
			  }
		  }
		
			$categories = $this->Category->sort_categories_and_sub_categories($this->Category->get_all_categories_and_sub_categories());
            foreach($categories as $key=>$value)
			{
				$name = str_repeat('&nbsp;&nbsp;', $value['depth']).$value['name'];
                if($shift_category_id != $key)
				    $data['categories'][$key] = $name;
			}
				
			$employees = array();
			
			foreach($this->Employee->get_list_employees_by_location($this->Employee->get_logged_in_employee_current_location_id()) as $employee)
			{
				$employees[$employee['person_id']] = $employee['employee_name'];
			}
			$data['employees'] = $employees;

        $data['expense_id'] = $expense_id;
        $data['slb'] = $this->Customer->item_Select_box();

        if ($this->Location->get_info_for_key('enable_credit_card_processing'))
        {
            $data['payment_options']=array(
                lang('common_cash') => lang('common_cash'),
                lang('common_check') => lang('common_check'),
                lang('common_credit') => lang('common_credit'),
                lang('common_giftcard') => lang('common_giftcard'));

            if($this->config->item('customers_store_accounts') && $this->sale_lib->get_mode() != 'store_account_payment')
            {
                $data['payment_options']=array_merge($data['payment_options'],	array(lang('common_store_account') => lang('common_store_account')
                ));
            }


            if ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'advanced' && count(explode(":",$this->config->item('spend_to_point_ratio'),2)) == 2 &&  isset($cust_info) && $cust_info->points >=1 && $this->sale_lib->get_payment_amount(lang('common_points')) <=0)
            {
                $data['payment_options']=array_merge($data['payment_options'],	array(lang('common_points') => lang('common_points')));
            }
        }
        else
        {
            $data['payment_options']=array(
                lang('common_cash') => lang('common_cash'),
                lang('common_check') => lang('common_check'),
                lang('common_giftcard') => lang('common_giftcard'),
                lang('common_debit') => lang('common_debit'),
                lang('common_credit') => lang('common_credit')
            );

            if($this->config->item('customers_store_accounts') && $this->sale_lib->get_mode() != 'store_account_payment')
            {
                $data['payment_options']=array_merge($data['payment_options'],	array(lang('common_store_account') => lang('common_store_account')
                ));
            }

            if ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'advanced' && count(explode(":",$this->config->item('spend_to_point_ratio'),2)) == 2 &&  isset($cust_info) && $cust_info->points >=1 && $this->sale_lib->get_payment_amount(lang('common_points')) <=0)
            {
                $data['payment_options']=array_merge($data['payment_options'],	array(lang('common_points') => lang('common_points')));
            }

        }

        foreach($this->Appconfig->get_additional_payment_types() as $additional_payment_type)
        {
            $data['payment_options'][$additional_payment_type] = $additional_payment_type;
        }

        $deleted_payment_types = $this->config->item('deleted_payment_types');
        $deleted_payment_types = explode(',',$deleted_payment_types);

        foreach($deleted_payment_types as $deleted_payment_type)
        {
            foreach($data['payment_options'] as $payment_option)
            {
                if ($payment_option == $deleted_payment_type)
                {
                    unset($data['payment_options'][$payment_option]);
                }
            }
        }
        // echo "<pre>";
        // var_dump($data);
        $this->load->view("expenses/form", $data);
    }

     function kiem_tra_truoc_khi_luu_hoan_thanh($id = -1){
        $post = $this->input->post();
        // var_dump($post);exit();
        if(!empty($post)) {
            $flagError                          = false;
            /*
             *  yêu cầu cho từng trường
             *  Cấu trúc cho hàm callback
             *  $table      = $array[0];
                $field      = $array[1];
                $id         = $array[2];
                $id_field   = $array[3];
                */
                $field      = 'Tên trường cần so sánh';
                $bat_buoc_nhap      = 'required|trim';
                $phone_number   = 'required'; 
            

            $this->form_validation->set_rules('expenses_amount', 'Số tiền', $bat_buoc_nhap,$this->_thong_bao);
            // $this->form_validation->set_rules('expense_reason', 'Lý do', $bat_buoc_nhap,$this->_thong_bao);
         
            $this->form_validation->set_rules('expenses_date', 'Ngày thêm chi phí', $bat_buoc_nhap,$this->_thong_bao);


          

            if($this->form_validation->run($this) == FALSE) {
                $flagError = true;
                $errors = $this->form_validation->error_array();
                
            }
            if($post['expenses_options'] == 'sale'){
              if($post['contract_id'] ==-1){
                $flagError = true;
                $errors['contract_id'] = 'Bạn phải chọn hợp đồng';
              }
            } elseif($post['expenses_options'] == 'receiving'){
              if(empty($post['contract_id'])){
                $flagError = true;
                $errors['contract_id'] = 'Bạn chưa chọn hóa đơn nhập hàng';
              }
              
            }
            // if($post['employee_id'] == -1){


            //     $flagError = true;
            //     $errors['employee_id'] = 'Bạn chưa chọn nhân viên thực hiện';
            // }
            // if($post['approved_employee_id'] == -1){
            //     $flagError = true;
            //     $errors['approved_employee_id'] = 'Bạn chưa chọn nhân viên phê duyệt';
            // }
            // if($post['payment_type'] == -1){
            //     $flagError = true;
            //     $errors['payment_type'] = 'Bạn chưa chọn hình thức thanh toán';
            // }
            if($post['expenses_tax'] < 0) {
                $flagError = true;
                $errors['payment_type'] = 'Thuế không được < 0';
            }
            // var_dump( $errors);exit();

            if($flagError == true) {
                $response = array('success' => false, 'message' => 'Có lỗi khi nhập dữ liệu, chi tiết lỗi tại các dòng' ,'flag'=>false, 'errors'=>$errors);
            } else {
                $response = array('flag'=>true,'success' => true, 'message' => 'Kiểm tra thành công');
            }
            echo json_encode($response);
        }
    }


    function save($id = -1) 
	  {
        // echo "string";die();
        $this->check_action_permission('add_update');
        $post = $this->input->post();
         // var_dump($post);exit();

         if (!$this->Category->exists($this->input->post('category_id')))
        {
            if (!$category_id = $this->Category->get_category_id($this->input->post('category_id')))
            {
                $category_id = $this->Category->save($this->input->post('category_id'));
            }
        }
        else
        {
            $category_id = $this->input->post('category_id');
        }

        $employee_id = $post['employee_id'];
        $sale_id = $contract_id = NULL;

        if(isset($post['sale_id'])) {
             $sale_id     = $post['sale_id'];
        }

        if(isset($post['contract_id'])) {
					
             $contract_id = $post['contract_id'];
        }

        if(isset($post['cash_register_id']) && $post['cash_register_id'] > 0)
             $post['payment_type'] = 'Tiền mặt';
        $timestamp = strtotime($this->input->post('expenses_date')); 
        $date = date('Y-m-d', $timestamp);
        $now = date('Y-m-d');
        if($date == $now) $date = date('Y-m-d H:i:s');
        $expense_data = array(
            'expense_options' => $this->input->post('expenses_options'),
            'payment_type' => $this->input->post('payment_type'),
            'expense_description' => $this->input->post('expenses_description'),
            'expense_date' => $date,
            'expense_amount' => tofloat($this->input->post('expenses_amount')),
            'expense_tax' => $this->input->post('expenses_tax'),
            'expense_note' => $this->input->post('expenses_note'),
            'sale_id' => $sale_id,
            'contract_id' => $contract_id,
            'location_id' => $this->Employee->get_logged_in_employee_current_location_id(),
        );

            if($employee_id != -1)
            $expense_data['employee_id'] =  $employee_id;
            if($this->input->post('approved_employee_id') != -1)
            $expense_data['approved_employee_id'] = $this->input->post('approved_employee_id');
            if($this->input->post('expenses_type') != -1)
            $expense_data['expense_type'] = $this->input->post('expenses_type');
            if(!empty($this->input->post('expense_reason')))
            $expense_data['expense_reason'] = $this->input->post('expense_reason');

        if($id != -1) {
           $old_expense = $this->Expense->get_info($id);
        }

        if ($this->Expense->save($expense_data, $id)) 
		  {
			  if ($this->input->post('cash_register_id'))
			  {
		  			$amount = to_currency_no_money($this->input->post('expenses_amount') + $this->input->post('expenses_tax'));
                    $amount = $amount * $post['expenses_type'];

					$cash_register = $this->Register->get_register_log_by_id($this->input->post('cash_register_id'));
                  if($amount > 0)
                    $cash_register->total_cash_subtractions+=$amount;
                  else
                      $cash_register->total_cash_additions+=abs($amount);

		  			$this->Register->update_register_log($cash_register);
								
		  			$employee_id_audit = $this->Employee->get_logged_in_employee_info()->person_id;
				
		  			$register_audit_log_data = array(
		  				'register_log_id'=> $cash_register->register_log_id,
		  				'employee_id'=> $employee_id_audit,
		  				'date' => date('Y-m-d H:i:s'),
		  				'amount' => $amount * (-1),
		  				'note' => lang('common_expenses'). ' - '.$this->input->post('expenses_note'),
		  			);

		  			$this->Register->insert_audit_log($register_audit_log_data);
			}
			
         	$redirect = $this->input->post('redirect');
			
			$success_message = '';

              if($id == -1) {
                  if($sale_id > 0) {
                      $emp_group_arr = $this->Sale->get_employee_list_from_sale(array('sale_id'=>$sale_id));

                      $this->Sale->delete_sale_commission($sale_id);
                      $this->sale_lib->update_employee_commission($emp_group_arr, $sale_id, true);
                  }
              }else {
                  if($sale_id > 0) {
                      $this->Sale->delete_sale_commission($sale_id);
                      $emp_group_arr = $this->Sale->get_employee_list_from_sale(array('sale_id'=>$sale_id));
                      $this->sale_lib->update_employee_commission($emp_group_arr, $sale_id, true);
                  }
              }

            //New item
            if ($id == -1) 
				  {
                $success_message = lang('expenses_successful_adding').' '.$expense_data['expense_type'].' - '.$this->input->post('expenses_amount');
                echo json_encode(array('success' => true, 'message' => $success_message, 'id' => $expense_data['id'], 'redirect' => $redirect));
            } else

                //
				{ //previous item
                $success_message = lang('common_items_successful_updating') . ' ' . $expense_data['expense_type'].' - '.to_currency($this->input->post('expenses_amount'));
                $this->session->set_flashdata('manage_success_message', $success_message);
                echo json_encode(array('success' => true, 'message' => $success_message, 'id' => $id, 'redirect' => $redirect));
            }
        } 
		  else 
		  {//failure
            echo json_encode(array('success' => false, 'message' => lang('expenses_error_adding_updating')));
        }
    }

    function delete() {
        $this->check_action_permission('delete');
        $expenses_to_delete = $this->input->post('ids');

        $this->Expense->delete_item($expenses_to_delete);
        echo json_encode(array('success' => true, 'message' => lang('expenses_successful_deleted') . ' ' . lang('expenses_one_or_multiple')));
    }
}

?>
