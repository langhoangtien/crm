<?php
require_once (APPPATH . "controllers/Employees.php");

class BizEmployees extends Employees
{
    protected $_scopeOfView = 'view_scope_owner';

    function __construct()
    {
        parent::__construct();
        $this->load->model('Group');
        $this->load->model('Location');
        $this->lang->load('receivings');
    }
    
    public function history_trans()
    {
        $this->load->model('Task');
        $this->load->helper('sort_items');
        $person_id = $this->input->post('s_employee_id');
        $employee = $this->Employee->getByPeopleId(explode(',', $person_id));
        $data['records'] = [];
        if (!empty($employee)) {
            $transItems = $this->Task->listItem(['related_to' => $employee['id'], 'paginator' => ['per_page' => 10000]], array('task'=>'grid-project'));
            $transTreeData = [];
            foreach ($transItems as $item) {
                $item['parent_id'] = '0';
                $transTreeData[] = $item;
                $itemChilds = $this->Task->listItem(['project_id' => $item['project_id'], 'paginator' => ['per_page' => 10000]], array('task'=>'task-by-project'));
                foreach ($itemChilds['ketqua'] as $child) {
                    $child['parent_id'] = $child['parent'];
                    $transTreeData[] = $child;
                }
            }
            $data['records'] = $transTreeData;
        }
        echo json_encode(array(
            'success' => true,
            'html' => $this->load->view('employees/partials/history_trans', $data, TRUE)
        ));
    }
    
    public function modalDetail()
    {
        
        $employeeReportFilter = [
            'employee_id' => $this->input->post('employee_id'),
            'selected_location_ids' => [
                $this->Employee->get_logged_in_employee_current_location_id()
            ],
            'export_excel' => 0,
            'url_print' => ''
        ];
        $_SESSION['specific_employees'] = $employeeReportFilter;
        $data = [];
        echo json_encode(array(
            'success' => true,
            // 'html' => $this->load->view('employees/partials/detail_modal', $data, TRUE)
        ));
    }
    
    public function index()
    {
        
        $data['count'] = $this->Employee->get_employees();
        $data['employees'] = $this->Employee->get_employees("","","","","","",10,0);
        $data['groups'] =$this->Group->get_all()->result_array();
        $data['ranks'] = $this->Employee->get_ranks();
        $config = array();
        $config['base_url']="";
        $config['total_rows'] = count($data['count'] );
        $config['per_page'] =10;
        $config['prev_link'] = "<<";
        $config['next_link'] = ">>";
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
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        
        $this->load->view('employees/manage2', $data);

    }
    
    public function build_list_view()
    {
        $search = $this->input->post('search');
        $page = $this->input->post('page');
    
        $sortBy = $this->input->post('sort_by');
        $sortBy = !empty($sortBy) ? $sortBy : 'id';
        $orderBy = $this->input->post('order_by');
        $orderBy = !empty($orderBy) ? $orderBy : 'DESC';
    
        $perpage = 20;
        $offset = ($page - 1) * $perpage;
        $records = [];
        
        $result = $this->Employee->search($search, $perpage, $offset, $sortBy, $orderBy);
        if ($result) {
            $records = $result->result_array();
        }
        $totalRecords = $this->Employee->search_count_all($search);
        // TODO
        $totalPage = ceil($totalRecords / $perpage);
        $pagination = [
            'total_page' => $totalPage,
            'current_page' => $page,
            'displayed_pages' => $this->getDisplayedPages($page, $totalPage)
        ];
        $data['totalRecords'] = $totalRecords;
        $data['pagination'] = $pagination;
        $data['records'] = $records;
        $data['sortBy'] = $sortBy;
        $data['orderBy'] = $orderBy;
    
        $headers = [
            [
                'name' => 'Tên',
                'field' => 'first_name',
                'class' => 'text-left hr-lbl',
                'style' => '',
                'sortable' => true,
                'order' => 1,
            ],
            [
                'name' => 'Email',
                'field' => 'email',
                'class' => 'text-left hr-lbl',
                'style' => '',
                'sortable' => true,
                'order' => 2,
            ],

            [
                'name' => 'Số điện thoại',
                'field' => 'phone_number',
                'class' => 'text-left hr-lbl',
                'style' => '',
                'sortable' => true,
                'order' => 3,
            ],
            [
                'name' => '&nbsp;',
                'field' => '',
                'class' => 'text-left hr-lbl',
                'style' => '',
                'order' => 11,
            ],
            [
                'name' => '&nbsp;',
                'field' => '',
                'class' => 'text-left hr-lbl',
                'style' => '',
                'order' => 12,
            ],

            [
                'name' => '&nbsp;',
                'field' => '',
                'class' => 'text-left hr-lbl',
                'style' => 'width: 50px;',
                'order' => 13,
            ],
        ];
    
        $data['headers'] = $headers;
        echo json_encode(array(
            'success' => true,
            'html' => $this->load->view('employees/partials/list_view_employees', $data, TRUE)
        ));
    }
    
    private function getDisplayedPages($currentPage, $totalPage)
    {
        $availablePages = [];
        if ($totalPage > 5) {
            if (in_array($currentPage, [1, 2, 3])) {
                $availablePages = range(1, 5);
            } elseif (in_array($currentPage, [$totalPage, $totalPage - 1, $totalPage - 2])) {
                $availablePages = range($totalPage - 4, $totalPage);
            } else {
                $availablePages = range($currentPage - 2, $currentPage + 2);
            }
        } else {
            $availablePages = range(1, $totalPage);
        }
        return $availablePages;
    }
    
    function search_v1()
    {
        $this->check_action_permission('search');
        $search = $this->input->post('search');
        $offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
        $order_col = $this->input->post('order_col') ? $this->input->post('order_col') : 'last_name';
        $order_dir = $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc';
        $search_data = $this->Employee->search(
            $search, 
            100000, 
            $this->input->post('offset') ? $this->input->post('offset') : 0, 
            $this->input->post('order_col') ? $this->input->post('order_col') : 'last_name', 
            $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc'
        );
        echo json_encode(array('success' => true, 'employees' => $search_data->result_array()));
    
    }

    public function approve_notice()
    {
        $data=[];
        $listViewData['cols'] = [
            [
                'field' => 'id',
                'label' => '#ID'
            ],
            [
                'field' => 'step_code_label',
                'label' => 'Công đoạn'
            ],
            [
                'field' => 'extra_info',
                'label' => 'Thông tin chi tiết'
            ]
        ];
        $employee=$this->Employee->get_logged_in_employee_info();
        $result = $this->ApproverGroup->getAllApproveRequests($employee->id);
        
        foreach ($result as &$record) {
            switch ($record['step_code']) {
                case 'chuyen_kho':
                    $record['step_code_label'] = $this->ApproverGroup->getStepLabel($record['step_code']);
                    $recInfo = $this->Receiving->getAllInfo($record['object_id']);
                    $employeeFullName = $recInfo['first_name'] . ' ' . $recInfo['last_name'];
                    $record['extra_info'] = <<<EOD
                    <p>Đơn hàng: #<a href = "receivings/receipt/{$recInfo['receiving_id']}">{$recInfo['receiving_id']}</a> </p>
                    <p>Ngày tạo: {$recInfo['receiving_time']}</p>
                    <p>Nhân viên: {$employeeFullName}</p>
                    <p>D/S sản phẩm: {$recInfo['items']}</p>
EOD;
                    break;
                case 'bao_gia':
                case 'tinh_gia':
                    $this->load->model('Sale');
                    $record['step_code_label'] = $this->ApproverGroup->getStepLabel($record['step_code']);
                    $saleInfo = $this->Sale->getAllInfo($record['object_id']);
                    $employeeFullName = $saleInfo['first_name'] . ' ' . $saleInfo['last_name'];
                    $record['extra_info'] = <<<EOD
                    <p>Đơn hàng: #{$saleInfo['sale_id']} </p>
                    <p>Ngày tạo: {$saleInfo['sale_time']}</p>
                    <p>Nhân viên: {$employeeFullName}</p>
                    <p>D/S sản phẩm: {$saleInfo['items']}</p>
EOD;
                    break;
                default:
                    break;
            }
        }
        $listViewData['tblRows'] = $result;
        $data['listViewHtml'] = $this->load->view('employees/partials/list_view', $listViewData, TRUE);
        $this->load->view('employees/approve_notice', $data);
    }
    

    function add_group_section() {
        $post = $this->input->post();
        $arrParams = array_merge($post, $this->input->get());
        if(!empty($post)) {
            $data = $arrParams;
            $group_info = $this->Group->get_item(array('id'=>$arrParams['group_id']));
            if(empty($group_info)) return;

            $data['group_info'] = $group_info;
            $this->load->view('employees/partials/location_group', $data);
        }
    }

    function location_group() {
        $data = $arrParams = array_merge($this->input->post(), $this->input->get());
        $arrParams['cid'] = $this->Location->get_group_ids_from_location($arrParams);
        $arrParams['not_cid'] = $this->Employee->get_group_id_from_location_commision($arrParams);

        $data['slb_group'] = $this->Group->item_select_box($arrParams);
        $this->load->view('employees/form_location_group', $data);
    }
    
    function deletes()
    {
    	$this->check_action_permission('delete');
    	$employees_to_delete = $this->input->post('items');
    
    	if (in_array(1, $employees_to_delete)) {
    		//failure
    		echo json_encode(array('success' => false, 'message' => lang('employees_cannot_delete_default_user')));
    	} elseif ($this->Employee->delete_list($employees_to_delete)) {
    		echo json_encode(array('success' => true, 'message' => lang('employees_successful_deleted') . ' ' .
    				count($employees_to_delete) . ' ' . lang('employees_one_or_multiple')));
    	} else {
    		echo json_encode(array('success' => false, 'message' => lang('employees_cannot_be_deleted')));
    	}
    }

    // Save employee infomation 

      function save($employee_id = -1)
    {


        $this->check_action_permission('add_update');
        $post = $this->input->post();
        $employee_location_commission = array();
        if(isset($post['lc_location_id']) && $employee_id != -1) {
            foreach($post['lc_location_id'] as $key => $val) {
                $tmp = array();
                $tmp['location_id']             = $val;
                $tmp['group_id']                = $post['lc_group_id'][$key];
                $tmp['employee_id']             = $employee_id;
                $tmp['commission_percent']      = (float)$post['lc_percent'][$key];
                $tmp['commission_percent_type'] = $post['lc_percent_type'][$key];
                $tmp['ord']                     = $key;

                $employee_location_commission[] = $tmp;
            }
        }

        $person_data = array(
            'first_name' => $this->input->post('first_name'),
            'email' => $this->input->post('email'),
            'phone_number' => $this->input->post('phone_number'),
            'chung_minh_thu' => $this->input->post('indentity_card'),
            'address_1' => $this->input->post('address_1'),
            'comments' => $this->input->post('comments'),

        );
        $permission_data = $this->input->post("permissions") != false ? $this->input->post("permissions") : array();
        $permission_action_data = $this->input->post("permissions_actions") != false ? $this->input->post("permissions_actions") : array();
        $location_data = $this->input->post('locations');
        $redirect_code = $this->input->post('redirect_code');
        //Password has been changed OR first time password set
        if ($this->input->post('password') != '') {
            //Bắt độ mạnh mk
             $this->form_validation->set_rules('password', 'Mật khẩu', 'trim|required|min_length[8]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})/]');
             if($this->form_validation->run($this)==FALSE){
                echo json_encode(array('success'=>false,'message'=>'Mật khẩu phải có ít nhất 8 ký tự, một chứ in hoa, một chữ in thường và một ký tự đặc biệt'));
                return;
             }
            $employee_data = array(
                'department_id' => $this->input->post('department_id'),
                'group_id' => $this->input->post('group_id'),
                'sex'=>$this->input->post('sex'),
                'level_id'=>$this->input->post('level'),
                'username' => $this->input->post('username'),
                'password' => md5($this->input->post('password')),
                'inactive' => $this->input->post('inactive') && $employee_id != 1 ? 1 : 0,
                'reason_inactive' => $this->input->post('reason_inactive') ? $this->input->post('reason_inactive') : NULL,
                'hire_date' => $this->input->post('hire_date') ? date('Y-m-d', strtotime($this->input->post('hire_date'))) : NULL,
                'employee_number' => $this->input->post('employee_number') ? $this->input->post('employee_number') : NULL,
                'birthday' => $this->input->post('birthday') ? date('Y-m-d', strtotime($this->input->post('birthday'))) : NULL,
                'termination_date' => $this->input->post('termination_date') ? date('Y-m-d', strtotime($this->input->post('termination_date'))) : NULL,
                'force_password_change' => $this->input->post('force_password_change') ? 1 : 0,
            );
        } else //Password not changed
        {



            $employee_data = array(
                'department_id' => $this->input->post('department_id'),
                'group_id' => $this->input->post('group_id'),
                'username' => $this->input->post('username'),
                'sex'=>$this->input->post('sex'),
                'level_id'=>$this->input->post('level'),
                'inactive' => $this->input->post('inactive') && $employee_id != 1 ? 1 : 0,
                'reason_inactive' => $this->input->post('reason_inactive') ? $this->input->post('reason_inactive') : NULL,
                'hire_date' => $this->input->post('hire_date') ? date('Y-m-d', strtotime($this->input->post('hire_date'))) : NULL,
                'employee_number' => $this->input->post('employee_number') ? $this->input->post('employee_number') : NULL,
                'birthday' => $this->input->post('birthday') ? date('Y-m-d', strtotime($this->input->post('birthday'))) : NULL,
                'termination_date' => $this->input->post('termination_date') ? date('Y-m-d', strtotime($this->input->post('termination_date'))) : NULL,
                'force_password_change' => $this->input->post('force_password_change') ? 1 : 0,
            );
        }

        //Commission
        // $employee_data['commission_percent'] = (float)$this->input->post('commission_percent');
        // $employee_data['commission_percent_type'] = $this->input->post('commission_percent_type');
        // $employee_data['hourly_pay_rate'] = (float)$this->input->post('hourly_pay_rate');

        $this->load->helper('directory');

        $valid_languages = str_replace(DIRECTORY_SEPARATOR, '', directory_map(APPPATH . 'language/', 1));
        $employee_data = array_merge($employee_data, array('language' => in_array($this->input->post('language'), $valid_languages) ? $this->input->post('language') : 'english'));

        $this->load->helper('demo');
        if ((is_on_demo_host()) && $employee_id == 1) {
            //failure
            echo json_encode(array('success' => false, 'message' => lang('common_employees_error_updating_demo_admin'), 'person_id' => -1));
        } elseif ((is_array($location_data) && count($location_data) > 0) && $this->Employee->save_employee($person_data, $employee_data, $permission_data, $permission_action_data, $location_data, $employee_id)) {

            /* Update Extended Attributes */
            if (!class_exists('Attribute')) {
                $this->load->model('Attribute');
            }
            $attributes = $this->input->post('attributes');
            if (!empty($attributes)) {
                $this->Attribute->reset_attributes(array('entity_id' => $employee_id, 'entity_type' => 'employees'));
                foreach ($attributes as $attribute_id => $value) {
                    $attribute_value = array('entity_id' => $employee_id, 'entity_type' => 'employees', 'attribute_id' => $attribute_id, 'entity_value' => $value);
                    $this->Attribute->set_attributes($attribute_value);
                }
            }
            /* End Update */

            // if ($this->Location->get_info_for_key('mailchimp_api_key')) {
            //     $this->Person->update_mailchimp_subscriptions($this->input->post('email'), $this->input->post('first_name'), $this->input->post('last_name'), $this->input->post('mailing_lists'));
            // }

            $success_message = '';

            //New employee
            if ($employee_id == -1) {
                $success_message = lang('common_employees_successful_adding') . ' ' . $person_data['first_name'] . ' ' . $person_data['last_name'];
                echo json_encode(array('success' => true, 'message' => $success_message, 'person_id' => $employee_data['person_id'], 'redirect_code' => $redirect_code));
            } else //previous employee
            {
                $success_message = lang('common_employees_successful_updating') . ' ' . $person_data['first_name'] . ' ' . $person_data['last_name'];
                $this->session->set_flashdata('manage_success_message', $success_message);
                echo json_encode(array('success' => true, 'message' => $success_message, 'person_id' => $employee_id, 'redirect_code' => $redirect_code));
            }


            //Delete Image
            if ($this->input->post('del_image') && $employee_id != -1) {
                $employee_info = $this->Employee->get_info($employee_id);
                if ($employee_info->image_id != null) {
                    $this->Person->update_image(NULL, $employee_id);
                    $this->load->model('Appfile');
                    $this->Appfile->delete($employee_info->image_id);
                }
            }

            //Save Image File
            if (!empty($_FILES["image_id"]) && $_FILES["image_id"]["error"] == UPLOAD_ERR_OK) {

                $allowed_extensions = array('png', 'jpg', 'jpeg', 'gif');
                $extension = strtolower(pathinfo($_FILES["image_id"]["name"], PATHINFO_EXTENSION));
                if (in_array($extension, $allowed_extensions)) {
                    $config['image_library'] = 'gd2';
                    $config['source_image'] = $_FILES["image_id"]["tmp_name"];
                    $config['create_thumb'] = FALSE;
                    $config['maintain_ratio'] = TRUE;
                    $config['width'] = 400;
                    $config['height'] = 300;
                    $this->load->library('image_lib', $config);
                    $this->image_lib->resize();
                    $this->load->model('Appfile');
                    $image_file_id = $this->Appfile->save($_FILES["image_id"]["name"], file_get_contents($_FILES["image_id"]["tmp_name"]));
                }
                if ($employee_id == -1) {
                    $this->Person->update_image($image_file_id, $employee_data['person_id']);
                } else {
                    $this->Person->update_image($image_file_id, $employee_id);

                }
            }

            // save commission
            if($employee_id != -1) {
                $this->db->where('employee_id = ' . $employee_id);
                $this->db->delete('employee_location_commission');

                $this->db->flush_cache();
            }

            if(!empty($employee_location_commission)) {
                $this->db->insert_batch('employee_location_commission', $employee_location_commission);
                $this->db->flush_cache();
            }
        } else //failure
        {
            echo json_encode(array('success' => false, 'message' => lang('common_employees_error_adding_updating') . ' ' .
                $person_data['first_name'] . ' ' . $person_data['last_name'], 'person_id' => -1));
        }
    }


// sắp xếp lọc
    public function sorting_v1()
    {

        $search = $this->input->post('search_1') ? $this->input->post('search_1') :"";
        $rank = $this->input->post('rank_1') ? $this->input->post('rank_1') : "";
        $level = $this->input->post('level_1') ? $this->input->post('level_1') : "";
        $group = $this->input->post('group_1') ? $this->input->post('group_1') : "";
        $hire_date = $this->input->post('hire_date_1') ? $this->input->post('hire_date_1'): "";
        $order = $this->input->post('order') ? $this->input->post('order'): "";
        $order_by = $this->input->post('order_by') ? $this->input->post('order_by'): "";

        $limit = 1000;
        $offset = $this->uri->segment(3);
        intval($offset);
        $offset = ($offset<1) ? 1 : $offset;
        $offset = $limit*($offset-1);
        $data = array();
        $data['count'] = $this->Employee->get_employees($search,$rank,$level,$group,$hire_date,"","");
        $data['employees'] = $this->Employee->get_employees($search,$rank,$level,$group,$hire_date,$order,$order_by,$limit,$offset);
        $data['offset'] = $offset;
        $config = array();
        $config['base_url']=base_url('employees/sorting_v1');
        $config['total_rows'] = count($data['count'] );
        $config['per_page'] = $limit;
        $config['prev_link'] = "<<";
        $config['next_link'] = ">>";
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
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $this->load->view('employees/sorting_v1', $data);
    }


    function level()
    {

        if(!empty($this->input->post('rank_1')))
        {

            $rank_id = $this->input->post('rank_1');

            echo (json_encode($this->Employee->get_level_by_rank($rank_id)));
        }
        else {
            echo json_encode('no value');
        }
    }


    # Lưu ngạch

    public function save_rank()
    {
                 
            # Nếu là sửa
            if($this->input->post('action')=="edit")
            {

            $this->form_validation->set_rules('id', 'id', 'required|integer|max_length[10]');
            $this->form_validation->set_rules('name', 'name', 'required|max_length[100]');
             if ($this->form_validation->run() == FALSE)
                {
                  
                        echo "error";
                        return;
                }
                else
                {
                        $data['name'] = $this->input->post('name');
                        $id = $this->input->post('id');
                        // var_dump($id);var_dump($data);die();
                        if($this->Employee->save_rank($action="edit",$id,$data))
                        {
                         
                        echo "success";
                         return;
                        }
                        else
                        {
                        echo "error";                       
                        return;
                        }
                }
       
            }

            # Nếu là thêm
            if($this->input->post('action')=="add")
            {

            $this->form_validation->set_rules('name', 'name', 'required|max_length[100]');
             if ($this->form_validation->run() == FALSE)
                {
                  
                        echo "error";
                        return;
                }
                else
                {
                        $data['name'] = $this->input->post('name');
                        // var_dump($id);var_dump($data);die();
                        if($this->Employee->save_rank($action="add",$id=-1,$data))
                        {
                         
                        echo "success";
                         return;
                        }
                        else
                        {
                        echo "error";                       
                        return;
                        }
                }
       
            }

            #Nếu là xóa

            if($this->input->post("action")=="delete")
            {
                $this->form_validation->set_rules('id', 'id', 'required|integer|max_length[10]');
                if ($this->form_validation->run() == FALSE)
                {
                  
                        echo "error";
                        return;
                }
                else
                {
                        
                        $id = $this->input->post('id');
                        if($this->Employee->save_rank($action="delete",$id,$data=""))
                        {
                         
                        echo "success";
                         return;
                        }
                        else
                        {
                        echo "error";                       
                        return;
                        }
                }
            }
    
    }


    function employees_log()
    {
    $data['count'] =  $this->Employee->get_list_log();
     $config = $this->set_config();
        $config['base_url']=base_url('employees/employees_log');
        $config['total_rows'] = count($data['count'] );
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
   
    $arrParam = array();
    $arrParam['limit'] =$config['per_page'] =10;
    $arrParam['offset'] = intval(($this->uri->segment(3,1))-1)*$config['per_page'];
    $list = $this->Employee->get_list_log($arrParam);
    // echo $this->db->last_query();die();
    // echo "<pre>";
    // var_dump($data['list']);die();
    $data['list'] = $list;
    $this->load->view('employees/employees_log',$data);
    }


}


?>