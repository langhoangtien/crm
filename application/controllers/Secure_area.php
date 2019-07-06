<?php
class Secure_area extends MY_Controller 
{
	var $module_id;
	protected   $scope_of_view = 'view_scope_owner';
	protected $_checkPermission = true;
	
	protected $_controller_name = '';
	
	protected $_publicResources = [
        [
            'controller' => 'config',
            'action' => 'do_backup'
        ]
    ];
	
	protected $_hiddenMenus = [
        'stock_in',
        'stock_out',
        'sync_offline',
        'approver_groups',
        'item_kits',
        'price_rules',
        'giftcards',
        'extra_tools'
    ];
	
	protected function checkPublicResource() {
		if (in_array(['controller' => (!empty($this->uri->segment(1)) ? $this->uri->segment(1) : ''), 'action' => (!empty($this->uri->segment(2)) ? $this->uri->segment(2) : '')], $this->_publicResources)) {
			return true;
		}
		
		return false;
	}
	
	/*
	Controllers that are considered secure extend Secure_area, optionally a $module_id can
	be set to also check if a user can access a particular module in the system.
	*/
	function __construct($module_id=null)
	{
		$this->_controller_name = str_replace(BIZ_PREFIX, '', strtolower(get_class($this)));

		parent::__construct();
		
		if ($this->checkPublicResource()) {
			return;
		}
		$this->lang->load('customers');														 
		$this->module_id = $module_id;	
		$this->load->model('Employee');
		$this->load->model('Location');
		$this->load->model('Customer');														 
		$this->load->model('ApproverGroup');
		$this->lang->load('module');
		
		 if(!$this->input->is_cli_request())
     { 
				if(!$this->Employee->is_logged_in())
				{
					redirect('login');
				}
     }
		
		if ($this->_checkPermission && !$this->Employee->has_module_permission($this->module_id,$this->Employee->get_logged_in_employee_info()->person_id))
		{
			redirect('no_access/'.$this->module_id);
		}
		
		$this->Employee->last_active();
		//load up global data
		$logged_in_employee_info=$this->Employee->get_logged_in_employee_info();
		$data['allowed_modules']=$this->Module->get_allowed_modules($logged_in_employee_info->person_id);
		$data['all_allowed_modules']=$this->Module->get_all_allowed_modules($logged_in_employee_info);
		$data['hiddenMenus'] = $this->_hiddenMenus;
		$data['user_info']=$logged_in_employee_info;
		$person_id = $this->Employee->get_logged_in_employee_info()->person_id;
		$data['new_message_count']=$this->Employee->get_unread_messages_count();
				if ($this->Employee->has_module_action_permission(
				$this->module_id,
				'view_scope_all',
				$person_id)
        ) 
				{
            $this->scope_of_view = 'view_scope_all';
        }elseif ($this->Employee->has_module_action_permission(
				$this->module_id,
				'view_scope_location',
				$person_id)
        ) 
				{
            $this->scope_of_view = 'view_scope_location';
        }

        
        if ($this->Employee->has_module_action_permission(
				'customers',
				'view_scope_all',
				$person_id)
        ) 
				{
            $view_customer = 'view_scope_all';
        }elseif ($this->Employee->has_module_action_permission(
				'customers',
				'view_scope_location',
				$person_id)
        ) 
				{
            $view_customer = 'view_scope_location';
        }
        else{
        	$view_customer = "view_scope_owner";

        }

		//Get new customers current day
		$searchParams = array(
						'start_date' 		=> date('Y-m-d').' '.'00:00:00',
						'end_date'		  =>	date('Y-m-d').' '.'23:59:59',
						'scope_of_view' => $view_customer,
						'paginator'			=> array('per_page' =>100000)
		);
                       
		$data['new_customers']			 = $this->Customer->list_item($searchParams);
		$data['new_customer_count'] 							 = count($data['new_customers']);
		$data['show_warning_add_new_customer']		 = (empty ($data['new_customers']))? false : true;
		$locations_list=$this->Location->get_all();
	
		//Get new contract current quater
		$tm = date("m");
		$ty = date("Y");
		$tqm = ceil($tm/3);
		$fq = $tqm*3-2;
		$lq = $fq +2;
		$param1 = "$ty-$fq-01 00:00:00";
		$param2 = date("Y-m-d 00:00:00",strtotime("$param1 +3 month"));
		$searchParamsContract = array(
						'start_date' 		=> $param1,
						'end_date'		  =>	$param2,
						'option'				=> 'customer'
		);
		$data['option']    												 = 'customer';		
		$data['new_contracts']										 = $this->Contract->list_item($searchParamsContract,null);
		$data['new_contracts_count'] 							 = count($data['new_contracts']);
		$data['show_warning_add_new_contracts']		 = (empty ($data['new_contracts']))? false : true;
		$data['thongbaothutien'] = count($this->Task->get_task_stage());
		
		$authenticated_locations = $this->Employee->get_authenticated_location_ids($logged_in_employee_info->person_id);
		$data['norevenue'] = count($this->TaskPersonal->get_list_norevenue());
		$data['norevenue_notice'] = count($this->TaskPersonal->get_list_notice_no_revenue());
		$data['task_notice'] = count($this->Task->get_list_notice_task());
		$locations = array();
		$total_locations_in_system = 0;
		foreach($locations_list->result() as $row)
		{
			if(in_array($row->location_id, $authenticated_locations))
			{
				$locations[$row->location_id] =$row->name;
			}
			
			$total_locations_in_system++;
		}
		
		$locations = [];
		$availableLocations = $this->Location->getLocationsWithChild();
		foreach ($availableLocations as $location) {
			$prefix = '';
			if (!empty($location['level'])) {
				for($i= 0; $i <= (int) $location['level']; $i++) {
					$prefix .= '&ensp;'; //&ensp;
				}
			}
			$locations[$location['location_id']] = $prefix . $location['name'];
		}
		
		$data['total_locations_in_system'] = $total_locations_in_system;
		$data['authenticated_locations'] = $locations;
		
		// TODO AAA
		$numberApproveNotices = $this->ApproverGroup->getCountApproveNotices($logged_in_employee_info->id);
		$data['numberApproveNotices'] = $numberApproveNotices;
		$data['new_message_count'] += $data['numberApproveNotices'];
		
		$location_id = $this->Employee->get_logged_in_employee_current_location_id();
		$loc_info = $this->Location->get_info($location_id);
		
		$data['current_logged_in_location_id'] = $location_id;
		$data['current_employee_location_info'] = $loc_info;
		$data['location_color'] = $loc_info->color;
		$data['so_hop_dong'] = count($this->Employee->get_contract_status_changing($this->Employee->get_logged_in_employee_info()->id,array('time'=>date("Y-m-d"))));

		$person_id_logged = $this->Employee->get_logged_in_employee_info()->person_id;
		$data['number_message_to'] = count($this->db->select('message_id')->from('phppos_message_receiver')->where('message_read',0)->where('receiver_id',$person_id_logged)->group_by('message_id')->get()->result_array());




		$this->load->vars($data);

		$this->load->library("form_validation");
		$this->form_validation->set_message('required', '%s '.lang('required'));
		$this->form_validation->set_message('is_unique', '%s không được trùng lặp.');
		// load data cho config
		// load data cho config
		// load data cho config
		// load data cho config
		$this->load->model('Tier');
		$data['controller_name']=$this->_controller_name;
		$data['payment_options']=array(
				lang('common_cash') => lang('common_cash'),
				lang('common_check') => lang('common_check'),
				lang('common_giftcard') => lang('common_giftcard'),
				lang('common_debit') => lang('common_debit'),
				lang('common_credit') => lang('common_credit'),
				lang('common_store_account') => lang('common_store_account')
		);
		
		$data['receipt_text_size_options']=array(
			'small' => lang('config_small'),
			'medium' => lang('config_medium'),
			'large' => lang('config_large'),
			'extra_large' => lang('config_extra_large'),
		);
		
		
		foreach($this->Appconfig->get_additional_payment_types() as $additional_payment_type)
		{
			$data['payment_options'][$additional_payment_type] = $additional_payment_type;
		}
		
		$data['tiers'] = $this->Tier->get_all();
		$data['currency_denoms'] = $this->Register->get_register_currency_denominations();
		$data['phppos_session_expirations'] = array('0' => lang('config_on_browser_close'));
		
		for($k=1;$k<=24;$k++)
		{
			$expire = $k*60*60;
			$data['phppos_session_expirations']["$expire"] = $k.' '.lang('config_hours');
		}

		$data['qc_types'] = $this->Customer->quotes_contract_by_code('BMHD');

        $categories = $this->Category->sort_categories_and_sub_categories($this->Category->get_all_categories_and_sub_categories());
        $data['categories'][0] = '';
        foreach($categories as $key=>$value)
        {
            $name = str_repeat('&nbsp;&nbsp;', $value['depth']).$value['name'];
            $data['categories'][$key] = $name;
        }

        foreach($this->Employee->get_all()->result() as $employee)
        {
            $employees[$employee->person_id] = $employee->first_name .' '.$employee->last_name;
        }
        $data['employees'] = $employees;
        $data['slb_import_quantity'] = array(
            '50' => 50,
            '100' => 100,
            '150' => 150,
            '200' => 200,
        );

        $data['number_task'] = count($this->Task->get_list_task());
        //commission
        $config_commission = $this->config->item('config_commission');
        if(!empty($config_commission)) {
            $config_commission = unserialize($config_commission);
            $group_ids = array_keys($config_commission);

            $group_list = $this->Group->get_items(array('cid'=>$group_ids, 'include_deleted'=>true));
            foreach($config_commission as &$commission)
                $commission['name'] = $group_list[$commission['group_id']]['name'];
        }

        $data['config_commission'] = $config_commission;

        $data['slb_service_items'] = $this->Item->get_items(array('is_service'=>1));

        $config_adjusted_cost_price = $this->config->item('config_adjusted_cost_price');
        if(!empty($config_adjusted_cost_price)) {
            $config_adjusted_cost_price = unserialize($config_adjusted_cost_price);
        }else
            $config_adjusted_cost_price = array();

        $data['config_adjusted_cost_price'] = $config_adjusted_cost_price;
        // var_dump($data['employees']);
        $data['number_employee'] = $this->Employee->get_employee_active();
        $data['check_view']  = $this->Group->check_view();
		$this->load->vars($data);									 
	}
	
	function check_action_permission($action_id, $ajax = null)
	{
		if (!$this->Employee->has_module_action_permission($this->module_id, $action_id, $this->Employee->get_logged_in_employee_info()->person_id))
		{
            if($ajax == 'ajax')
                return 'no-permission';
            else
			    redirect('no_access/'.$this->module_id);
		}
	}

	function set_config(){
    $config =array();
        $config['per_page'] =10;
        $config['prev_link'] = false;
        $config['next_link'] = false;
        $config['first_link'] = '<';
        $config['last_link'] = '>';
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['cur_tag_open'] = '<li class="active"><a>';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="pagi">';
        $config['num_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li class="pagi">';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li class="pagi">';
        $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="pagi">';
        $config['last_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li class="pagi">';
        $config['first_tag_close'] = '</li>';
        $config['use_page_numbers'] = TRUE;
        return $config;
}

function rewriteUrl($value, $options = null){
    $value = trim($value);
    /*a à ả ã á ạ ă ằ ẳ ẵ ắ ặ â ầ ẩ ẫ ấ ậ b c d đ e è ẻ ẽ é ẹ ê ề ể ễ ế ệ
        f g h i ì ỉ ĩ í ị j k l m n o ò ỏ õ ó ọ ô ồ ổ ỗ ố ộ ơ ờ ở ỡ ớ ợ
    p q r s t u ù ủ ũ ú ụ ư ừ ử ữ ứ ự v w x y ỳ ỷ ỹ ý ỵ z*/
    $value = html_entity_decode ($value);
    $charaterA = '#(à|ả|ã|á|ạ|ă|ằ|ẳ|ẵ|ắ|ặ|â|ầ|ẩ|ẫ|ấ|ậ)#imsU';
    $repleceCharaterA = 'a';
    $value = preg_replace($charaterA,$repleceCharaterA,$value);

    $charaterD = '#(è|ẻ|ẽ|é|ẹ|ê|ề|ể|ễ|ế|ệ)#imsU';
    $replaceCharaterD = 'e';
    $value = preg_replace($charaterD,$replaceCharaterD,$value);

    $charaterI = '#(ì|ỉ|ĩ|í|ị)#imsU';
    $replaceCharaterI = 'i';
    $value = preg_replace($charaterI,$replaceCharaterI,$value);

    $charaterO = '#(ò|ỏ|õ|ó|ọ|ô|ồ|ổ|ỗ|ố|ộ|ơ|ờ|ở|ỡ|ớ|ợ)#imsU';
    $replaceCharaterO = 'o';
    $value = preg_replace($charaterO,$replaceCharaterO,$value);

    $charaterU = '#(ù|ủ|ũ|ú|ụ|ư|ừ|ử|ữ|ứ|ự)#imsU';
    $replaceCharaterU = 'u';
    $value = preg_replace($charaterU,$replaceCharaterU,$value);

    $charaterY = '#(ỳ|ỷ|ỹ|ý)#imsU';
    $replaceCharaterY = 'y';
    $value = preg_replace($charaterY,$replaceCharaterY,$value);

    $charaterD = '#(đ|Đ)#imsU';
    $replaceCharaterD = 'd';
    $value = preg_replace($charaterD,$replaceCharaterD,$value);

    if($options == null)
        $value = trim(mb_strtolower(url_title($value), 'UTF-8'));
    else
        $value = trim(mb_strtolower($value, 'UTF-8'));

    return $value;
}
}
?>