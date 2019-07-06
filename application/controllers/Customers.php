<?php
require_once("Person_controller.php");

class Customers extends Person_controller
{

    protected $_scopeOfView = 'view_scope_owner';

    function __construct()
    {
        parent::__construct('customers');
        $this->lang->load('customers');
        $this->lang->load('module');
        $this->load->model('Customer');
        $this->load->model('Contract');
        //permition view customer all > location > owner
        if ($this->Employee->has_module_action_permission(
            $this->module_id,
            'view_scope_all',
            $this->Employee->get_logged_in_employee_info()->person_id)
        ) {
            $this->_scopeOfView = 'view_scope_all';
        } elseif ($this->Employee->has_module_action_permission(
            $this->module_id,
            'view_scope_location',
            $this->Employee->get_logged_in_employee_info()->person_id)
        ) {
            $this->_scopeOfView = 'view_scope_location';
        }
    }

    function index($offset = 0)
    {
        $params = $this->session->userdata('customers_search_data') ? $this->session->userdata('customers_search_data') : array('offset' => 0, 'order_col' => 'last_name', 'order_dir' => 'asc', 'search' => FALSE);
        if ($offset != $params['offset']) {
            redirect('customers/index/' . $params['offset']);
        }
        $this->check_action_permission('search');
        $config['base_url'] = site_url('customers/sorting');
        $config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;

        $data['controller_name'] = $this->_controller_name;
        $data['per_page'] = $config['per_page'];
        $data['search'] = $params['search'] ? $params['search'] : "";
        if ($data['search']) {
            $config['total_rows'] = $this->Customer->search_count_all($data['search']);
            $table_data = $this->Customer->search($data['search'], $data['per_page'], $params['offset'], $params['order_col'], $params['order_dir']);
        } else {
            $config['total_rows'] = $this->Customer->count_all();
            $table_data = $this->Customer->get_all($data['per_page'], $params['offset'], $params['order_col'], $params['order_dir']);
        }
        $this->load->library('pagination');
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['order_col'] = $params['order_col'];
        $data['order_dir'] = $params['order_dir'];

        $data['manage_table'] = get_people_manage_table($table_data, $this);
        $data['total_rows'] = $config['total_rows'];
        $this->load->view('people/manage', $data);
    }

    function sorting()
    {
        $this->check_action_permission('search');
        $search = $this->input->post('search') ? $this->input->post('search') : "";
        $per_page = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;

        $offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
        $order_col = $this->input->post('order_col') ? $this->input->post('order_col') : 'last_name';
        $order_dir = $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc';

        $customers_search_data = array('offset' => $offset, 'order_col' => $order_col, 'order_dir' => $order_dir, 'search' => $search);
        $this->session->set_userdata("customers_search_data", $customers_search_data);


        if ($search) {
            $config['total_rows'] = $this->Customer->search_count_all($search);
            $table_data = $this->Customer->search($search, $per_page, $this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'last_name', $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc');
        } else {
            $config['total_rows'] = $this->Customer->count_all();
            $table_data = $this->Customer->get_all($per_page, $this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'last_name', $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc');
        }
        $config['base_url'] = site_url('customers/sorting');
        $config['per_page'] = $per_page;
        $this->load->library('pagination');
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['manage_table'] = get_people_manage_table_data_rows($table_data, $this);
        echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination']));

    }

    /*
    Returns customer table data rows. This will be called with AJAX.
    */
    function search()
    {
        $this->check_action_permission('search');
        $search = $this->input->post('search');
        $offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
        $order_col = $this->input->post('order_col') ? $this->input->post('order_col') : 'last_name';
        $order_dir = $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc';

        $customers_search_data = array('offset' => $offset, 'order_col' => $order_col, 'order_dir' => $order_dir, 'search' => $search);
        $this->session->set_userdata("customers_search_data", $customers_search_data);
        $per_page = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
        $search_data = $this->Customer->search($search, $per_page, $offset, $order_col, $order_dir);
        $config['base_url'] = site_url('customers/search');
        $config['total_rows'] = $this->Customer->search_count_all($search);
        $config['per_page'] = $per_page;

        $this->load->library('pagination');
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['total_rows'] = $this->Customer->search_count_all($search);
        $data['manage_table'] = get_people_manage_table_data_rows($search_data, $this);
        echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination']));
    }

    function mailing_label_from_summary_customers_report($start_date, $end_date, $sale_type, $total_spent_condition = 'any', $total_spent_amount = 0)
    {
        $start_date = rawurldecode($start_date);
        $end_date = rawurldecode($end_date);

        $this->load->model('Sale');
        $this->load->model('reports/Summary_customers');
        $model = $this->Summary_customers;
        $model->setParams(array('start_date' => $start_date, 'end_date' => $end_date, 'sale_type' => $sale_type, 'offset' => 0, 'export_excel' => 1, 'total_spent_condition' => $total_spent_condition, 'total_spent_amount' => $total_spent_amount));

        $this->Sale->create_sales_items_temp_table(array('start_date' => $start_date, 'end_date' => $end_date, 'sale_type' => $sale_type));

        $report_data = $model->getData();

        $customer_ids = array();
        foreach ($report_data as $row) {
            $customer_ids[] = $row['customer_id'];
        }

        foreach ($customer_ids as $customer_id) {
            $customer_info = $this->Customer->get_info($customer_id);

            $label = array();
            $label['name'] = $customer_info->first_name . ' ' . $customer_info->last_name;
            $label['address_1'] = $customer_info->address_1;
            $label['address_2'] = $customer_info->address_2;
            $label['city'] = $customer_info->city;
            $label['state'] = $customer_info->state;
            $label['zip'] = $customer_info->zip;
            $label['country'] = $customer_info->country;

            $data['mailing_labels'][] = $label;

        }

        $data['type'] = $this->config->item('mailing_labels_type') == 'excel' ? 'excel' : 'pdf';

        $this->load->view("mailing_labels", $data);

    }

    function mailing_labels($customer_ids)
    {
        $data['mailing_labels'] = array();

        foreach (explode('~', $customer_ids) as $customer_id) {
            $customer_info = $this->Customer->get_info($customer_id);

            $label = array();
            $label['name'] = $customer_info->first_name . ' ' . $customer_info->last_name;
            $label['address_1'] = $customer_info->address_1;
            $label['address_2'] = $customer_info->address_2;
            $label['city'] = $customer_info->city;
            $label['state'] = $customer_info->state;
            $label['zip'] = $customer_info->zip;
            $label['country'] = $customer_info->country;

            $data['mailing_labels'][] = $label;

        }
        $data['type'] = $this->config->item('mailing_labels_type') == 'excel' ? 'excel' : 'pdf';
        $this->load->view("mailing_labels", $data);
    }

    /*
    Gives search suggestions based on what is being searched for
    */
    function suggest()
    {
        //allow parallel searchs to improve performance.
        session_write_close();
        $suggestions = $this->Customer->get_customer_search_suggestions($this->input->get('term'), 100);
        echo json_encode($suggestions);
    }

    /**
     * @param int $customer_id
     * @param int $redirect_code
     * @return string
     */
    function view($customer_id = -1, $redirect_code = 0)
    {
        $this->check_action_permission('add_update');
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
        $data['tiers'] = $tiers;
        $data['person_info'] = $this->Customer->get_info($customer_id);
        $this->load->model('Customer_taxes');
        $data['customer_tax_info'] = $this->Customer_taxes->get_info($customer_id);

        $data['redirect_code'] = $redirect_code;
        $this->load->view("customers/form", $data);

    }

    function account_number_exists()
    {
        if ($this->Customer->account_number_exists($this->input->post('account_number')))
            echo 'false';
        else
            echo 'true';

    }

    function clear_state()
    {
        $this->session->unset_userdata('customers_search_data');
        redirect('customers');
    }

    /*
    Inserts/updates a customer
    */
    function save($customer_id = -1)
    {
        $this->check_action_permission('add_update');
        $person_data = array(
            'first_name' => $this->input->post('first_name'),
            'last_name' => $this->input->post('last_name'),
            'email' => $this->input->post('email'),
            'phone_number' => $this->input->post('phone_number'),
            'address_1' => $this->input->post('address_1'),
            'address_2' => $this->input->post('address_2'),
            'city' => $this->input->post('city'),
            'state' => $this->input->post('state'),
            'zip' => $this->input->post('zip'),
            'country' => $this->input->post('country'),
            'comments' => $this->input->post('comments')
        );


        $customer_data = array(
            'company_name' => $this->input->post('company_name'),
            'tier_id' => $this->input->post('tier_id') ? $this->input->post('tier_id') : NULL,
            'account_number' => $this->input->post('account_number') == '' ? null : $this->input->post('account_number'),
            'taxable' => $this->input->post('taxable') == '' ? 0 : 1,
            'tax_certificate' => $this->input->post('tax_certificate'),
            'override_default_tax' => $this->input->post('override_default_tax') ? $this->input->post('override_default_tax') : 0,
        );


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

        if ($this->Customer->save_customer($person_data, $customer_data, $customer_id)) {
            if ($this->Location->get_info_for_key('mailchimp_api_key')) {
                $this->Person->update_mailchimp_subscriptions($this->input->post('email'), $this->input->post('first_name'), $this->input->post('last_name'), $this->input->post('mailing_lists'));
            }


            $success_message = '';

            //New customer
            if ($customer_id == -1) {
                $success_message = lang('customers_successful_adding') . ' ' . $person_data['first_name'] . ' ' . $person_data['last_name'];
                echo json_encode(array('success' => true, 'message' => $success_message, 'person_id' => $customer_data['person_id'], 'redirect_code' => $redirect_code));
                $customer_id = $customer_data['person_id'];

            } else //previous customer
            {
                $success_message = lang('customers_successful_updating') . ' ' . $person_data['first_name'] . ' ' . $person_data['last_name'];
                $this->session->set_flashdata('manage_success_message', $success_message);
                echo json_encode(array('success' => true, 'message' => $success_message, 'person_id' => $customer_id, 'redirect_code' => $redirect_code));
            }

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


            //Delete Image
            if ($this->input->post('del_image') && $customer_id != -1) {
                $customer_info = $this->Customer->get_info($customer_id);
                if ($customer_info->image_id != null) {
                    $this->Person->update_image(NULL, $customer_id);
                    $this->load->model('Appfile');
                    $this->Appfile->delete($customer_info->image_id);
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

                if ($customer_id == -1) {
                    $this->Person->update_image($image_file_id, $customer_data['person_id']);
                } else {
                    $this->Person->update_image($image_file_id, $customer_id);

                }
            }
        } else//failure
        {
            echo json_encode(array('success' => false, 'message' => lang('customers_error_adding_updating') . ' ' .
                $person_data['first_name'] . ' ' . $person_data['last_name'], 'person_id' => -1));
        }
    }

    /*
    This deletes customers from the customers table
    */
    function delete()
    {
        $this->check_action_permission('delete');
        $customers_to_delete = $this->input->post('ids');

        if ($this->Customer->delete_list($customers_to_delete)) {
            echo json_encode(array('success' => true, 'message' => lang('customers_successful_deleted') . ' ' .
                count($customers_to_delete) . ' ' . lang('customers_one_or_multiple')));
        } else {
            echo json_encode(array('success' => false, 'message' => lang('customers_cannot_be_deleted')));
        }
    }

    function deletes($ids = '')
    {
        $ids = $this->input->post('items');
        if ($this->Customer->deletes($ids)) {
            echo json_encode(array('success' => true, 'message' => $ids));
        } else {
            echo json_encode(array('success' => false, 'message' => lang('customers_cannot_be_deleted')));
        }
    }

    function _excel_get_header_row()
    {
        $return = array(lang('common_last_name'), lang('common_email'), lang('common_phone_number'), lang('common_address_1'), lang('common_comments'), lang('customers_account_number'), lang('customers_taxable'), lang('customers_tax_certificate'), lang('customers_company_name'), lang('common_tier_name'));

        if ($this->config->item('customers_store_accounts')) {
            $return[] = lang('common_balance');
        }

        return $return;
    }

    function excel()
    {
        $this->load->helper('download');
        $this->load->helper('report');
        $header_row = $this->_excel_get_header_row();

        $this->load->helper('spreadsheet');
        // $content = array_to_spreadsheet(array($header_row));
        $bizExcel = new BizExcel('import_customers.xlsx');
        $content = $bizExcel->generateFile(false);
        force_download('import_customers.' . ($this->config->item('spreadsheet_format') == 'XLSX' ? 'xlsx' : 'csv'), $content);
    }


    function check_duplicate()
    {
        echo json_encode(array('duplicate' => $this->Customer->check_duplicate($this->input->post('name'), $this->input->post('email'), $this->input->post('phone_number'))));
    }

    /* added for excel expert */

    public function excel_export()
    {
        $this->load->model('Attribute_set');
        $this->load->model('Attribute_group');
        $this->load->model('Attribute');

        # đầu trang
        $_headers = array(
            array('col' => 'A', 'value_field' => 'code'),
            array('col' => 'B', 'value_field' => 'last_name'),
            array('col' => 'C', 'value_field' => 'phone_number'),
            array('col' => 'D', 'value_field' => 'email'),
            array('col' => 'E', 'value_field' => 'address_1'),
            array('col' => 'F', 'value_field' => 'birth_date'),
            array('col' => 'G', 'value_field' => 'chung_minh_thu'),
            array('col' => 'H', 'value_field' => 'ho_chieu'),
            array('col' => 'I', 'value_field' => 'sex'),
            array('col' => 'J', 'value_field' => 'created_by'),
            array('col' => 'K', 'value_field' => 'balance'),
            array('col' => 'L', 'value_field' => 'balance_2'),
            array('col' => 'M', 'value_field' => 'credit_limit'),
            array('col' => 'N', 'value_field' => 'points'),
            array('col' => 'O', 'value_field' => 'website'),
            array('col' => 'P', 'value_field' => 'fax_number'),
            array('col' => 'Q', 'value_field' => 'code_tax'),
            array('col' => 'R', 'value_field' => 'business_registration'),
            array('col' => 'S', 'value_field' => 'account_number'),
            array('col' => 'T', 'value_field' => 'comments'),
            array('col' => 'U', 'value_field' => 'name_more'),
            array('col' => 'V', 'value_field' => 'sdt'),
            array('col' => 'W', 'value_field' => 'email_more'),
            array('col' => 'Y', 'value_field' => 'phongban'),
            array('col' => 'X', 'value_field' => 'type_customer'),    # nhóm khách hàng
            array('col' => 'Z', 'value_field' => 'tier_id'),            # phân cấp khách hàng
            array('col' => 'AA', 'value_field' => 'business_type'),    # ngành nghề
            array('col' => 'AB', 'value_field' => 'geographical_area'),    # khu vực địa lý
            array('col' => 'AC', 'value_field' => 'exchange_form'), # hình thức trao đổi
        );
        # dữ liệu truyền ra
        $items = $this->Customer->get_all_export_excel(array('scope_of_view' => $this->_scopeOfView))->result_object(); # lấy tất cả
        $i = 'AA';
        foreach ($items as $r) {
            # chuyển đổi phân loại khách hàng
            if ($r->type_customer != 0) {
                $customers_type = $this->Customer->lay_ra_ten_danh_muc('customers_type', $r->type_customer);
                $r->type_customer = $customers_type[0]['name'];  # chuyển đổi dữ liệu từ số sang chữ
            } else $r->type_customer = 'Chưa phân loại';
            # chuyển đổi phân cấp khách hàng
            if ($r->tier_id != 0) {
                $customers_type = $this->Customer->lay_ra_ten_danh_muc('price_tiers', $r->tier_id);
                $r->tier_id = $customers_type[0]['name'];  # chuyển đổi dữ liệu từ số sang chữ
            } else $r->tier_id = 'Chưa phân loại';
            # chuyển đổi nhân viên
            $created_by = $this->Customer->lay_ra_ten_danh_muc('employees', $r->created_by, 'person_id');
            $r->created_by = $created_by[0]['username'];    # chuyển đổi dữ liệu từ số sang chữ
            # chuyển đổi giới tính
            if ($r->sex == 1) $r->sex = 'Nam'; else $r->sex = 'Nữ';
            $r->birth_date = date('d-m-Y', strtotime($r->birth_date));
            $array = array(
                'code' => $r->code,
                'last_name' => $r->last_name,
                'phone_number' => $r->phone_number,
                'email' => $r->email,
                'address_1' => $r->address_1,
                'birth_date' => $r->birth_date,
                'chung_minh_thu' => $r->chung_minh_thu,
                'ho_chieu' => $r->ho_chieu,
                'sex' => $r->sex,
                'created_by' => $r->created_by,
                'balance' => $r->balance,
                'balance_2' => $r->balance_2,
                'credit_limit' => $r->credit_limit,
                'points' => $r->points,
                'website' => $r->website,
                'fax_number' => $r->fax_number,
                'business_registration' => $r->business_registration,
                'code_tax' => $r->code_tax,
                'account_number' => $r->account_number,
                'comments' => $r->comments,
                'name_more' => $r->name_more,
                'sdt' => $r->sdt,
                'email_more' => $r->email_more,
                'phongban' => $r->phongban,
                'type_customer' => $r->type_customer,
                'tier_id' => $r->tier_id,
                'business_type' => $r->business_type,
                'geographical_area' => $r->geographical_area,
                'exchange_form' => $r->exchange_form,
            );
            $attribute_values = $this->Attribute->get_entity_attributes(array('entity_id' => $r->person_id, 'entity_type' => 'customers'));
            foreach ($attribute_values as $attribute_value) {
                $array[$attribute_value->code] = $attribute_value->entity_value;
                $flag = true;
                foreach ($_headers as $key => $product) {
                    if ($product['value_field'] == $attribute_value->code) {
                        $flag = false;
                        break;

                    }
                }
                if ($flag) {
                    $_headers[] = array('col' => ++$i, 'value_field' => $attribute_value->code);
                    $header_of_col[] = array('col' => $i, 'text' => $attribute_value->name, 'styles' => array('is_fill' => true, 'color' => 'c0e0d3'));
                }
            }

            $result[] = $array;
        }
        if (count($header_of_col) > 0) {
            $header_of_multicol[] = array('mergeStartCol' => 'AB', 'mergeEndCol' => $i, 'text' => 'Bộ thuộc tính', 'styles' => array('is_fill' => true, 'color' => 'a7d2c0'));

        }
        $bizExcel = new BizExcel('import_customers.xlsx');
        $bizExcel->setNumberRowBeginRow(0)->setHeaderOfMultiCol($header_of_multicol);
        $bizExcel->setNumberRowBeginRow(1)->setHeaderOfCol($header_of_col);
        $bizExcel->setNumberRowStartBody(2)->setHeaderOfBody($_headers);
        $bizExcel->setDataExcel($result);
        $excelContent = $bizExcel->generateFile(false);
        $this->load->helper('download');
        force_download('danh_sach_khach_hang.xlsx', $excelContent);
        exit;
    }


    function excel_import()
    {
        $this->check_action_permission('add_update');
        $this->load->view("customers/excel_import", null);
    }

    /**
     * @Loads the form for excel import
     */
    function do_excel_import()
    {
        $this->check_action_permission('add_update');
        # lưu biến location
        $_SESSION['employee_current_location_id'] = $this->Employee->get_logged_in_employee_current_location_id();
        $this->load->helper('demo');
        if (is_on_demo_host()) {
            $msg = lang('common_excel_import_disabled_on_demo');
            echo json_encode(array('success' => false, 'message' => $msg));
            return;
        }
        if ($_FILES['file_path']['error'] != UPLOAD_ERR_OK) {
            $msg = lang('common_excel_import_failed');
            echo json_encode(array('success' => false, 'message' => $msg));
            return;
        } else {
            if (($handle = fopen($_FILES['file_path']['tmp_name'], "r")) !== FALSE) {
                $this->load->helper('spreadsheet');
                $objPHPExcel = file_to_obj_php_excel($_FILES['file_path']['tmp_name']);
                $end_column = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();

                $this->load->model('Attribute_set');
                $data['attribute_sets'] = $this->Attribute_set->get_by_related_object('customers');
                $data['sheet'] = $objPHPExcel->getActiveSheet();
                $data['num_rows'] = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
                $data['columns'] = array();

                foreach ($this->excelColumnRange('A', $end_column) as $value) {
                    $data['columns'][] = (string)$value;
                }

                # lấy ra các trường dữ liệu có thể import
                $data['fields'] = $this->Customer->get_import_fields();
                $data['customers_contract_info_add'] = $this->Customer->customers_contract_info_add();
                $data['person_fields'] = $this->Customer->get_person_import_fields();
                $html = $this->load->view('customers/import/result', $data, true);
                $result = array('success' => true, 'message' => lang('common_import_success'), 'html' => $html);
                echo json_encode($result);
                return;
            } else {
                echo json_encode(array('success' => false, 'message' => lang('common_upload_file_not_supported_format')));
                return;
            }
        }
        $result = array('success' => true, 'message' => lang('common_import_success'));
        echo json_encode($result);
    }

    public function excelColumnRange($lower, $upper)
    {
        ++$upper;
        for ($i = $lower; $i !== $upper; ++$i) {
            yield $i;
        }

    }

    /**
     * Import Real Data
     **/
    public function action_import_data()
    {
        $this->check_action_permission('add_update');
//        $this->load->helper('demo');
//        if (is_on_demo_host()) {
//            $msg = lang('common_excel_import_disabled_on_demo');
//            echo json_encode(array('success' => false, 'message' => $msg));
//            return;
//        }
        $_SESSION['ma_khach_hang_bat_dau_tu'] = (int)$this->config->item('ma_khach_hang_bat_dau_tu');

        $this->load->model('Attribute');
        $entity_type = 'customers';
        $person_entity_type = 'people';
        $check_duplicate_field = $this->input->post('check_duplicate_field');
        $field_parts = explode(':', $check_duplicate_field);
        if (count($field_parts) == 2) {
            $check_duplicate_field_type = $field_parts[0];
            $check_duplicate_field_name = $field_parts[1];
        }
        $attribute_set_id = $this->input->post('attribute_set_id');
        $columns = $this->input->post('columns');
        $rows = $this->input->post('rows');
        $selected_rows = $this->input->post('selected_rows');
//        if (empty($rows) || empty($selected_rows)) {
//            $msg = lang('common_error');
//            echo json_encode(array('success' => false, 'message' => $msg));
//            return;
//        }
        $stored_rows = 0;
        $person_import_fields = $this->Customer->get_person_import_fields();
        foreach ($rows as $index => &$row) {
            if (!isset($selected_rows[$index])) {
                unset($rows[$index]);
            } else {
                $row['stt'] = $index;
            }
        }

        $rows_tmp = array_slice($rows, 0, $this->config->item('import_quantity'));
        $rows = array();
        foreach ($rows_tmp as $value) {
            $stt = $value['stt'];
            unset($value['stt']);
            $rows[$stt] = $value;
        }

        $error_rows = $success_rows = array();

        foreach ($rows as $index => $row) {
            $data = array('attribute_set_id' => $attribute_set_id);
            $person_data = $extend_data = $extend_rows = array();
            foreach ($columns as $excel_column => $field_column) {
                if (!empty($field_column) && !empty($row[$excel_column])) {
                    $field_parts = explode(':', $field_column);

                    /* Set Basic Attributes */
                    if (count($field_parts) == 2) {
                        switch ($field_parts[0]) {
                            case 'person': # data people
                                $person_data[$field_parts[1]] = $row[$excel_column];
                                break;
                            case 'basic': # data customer
                                $data[$field_parts[1]] = $row[$excel_column];
                                break;
                            case 'extend': # data thông tin theo bộ thuộc tính
                                $extend_data = array(
                                    'entity_type' => $entity_type,
                                    'attribute_id' => $field_parts[1],
                                    'entity_value' => $row[$excel_column],
                                );
                                $extend_rows[] = $extend_data;
                                break;
                            case 'thong_tin_lien_he_them': # data thông tin liên hệ thêm
                                $thong_tin_lien_he_them[$field_parts[1]] = $row[$excel_column];
                                break;
                            case 'hinh_thuc_trao_doi': # data hình thức trao đổi
                                $hinh_thuc_trao_doi[$field_parts[1]] = $row[$excel_column];
                                break;
                            case 'khu_vuc_dia_ly': # data khu vực địa lý
                                $khu_vuc_dia_ly[$field_parts[1]] = $row[$excel_column];
                                break;
                            default:
                                $data[$field_parts[1]] = $row[$excel_column];
                                break;
                        }
                    }
                }
            }

            // die;
            try {
                /* Check duplicate item */
                $exists_row = false;
                if (isset($check_duplicate_field_type) && isset($check_duplicate_field_name)) {
                    switch ($check_duplicate_field_type) {
                        case 'person':
                            if (!empty($person_data[$check_duplicate_field_name])) {
                                $exists_row = $this->Person->exists_by_field($person_entity_type, $check_duplicate_field_name, $person_data[$check_duplicate_field_name], false, false);
                            }
                            break;
                        case 'basic':
                            if (!empty($data[$check_duplicate_field_name])) {
                                $exists_row = $this->Customer->exists_by_field($entity_type, $check_duplicate_field_name, $data[$check_duplicate_field_name]);
                            }
                            break;
                        case 'extend':
                            if (!empty($extend_data['entity_value'])) {
                                $exists_row = $this->Attribute->exists_by_value($entity_type, $extend_data['attribute_id'], $extend_data['entity_value']);
                            }
                            break;

                        default:
                            if (!empty($data[$check_duplicate_field_name])) {
                                $exists_row = $this->Customer->exists_by_field($entity_type, $check_duplicate_field_name, $data[$check_duplicate_field_name]);
                            }
                            break;
                    }
                }
                if (!$exists_row) {

                    # lấp những trường dữ liệu trống
                    foreach ($person_import_fields as $person_import_field) {
                        if (!isset($person_data[$person_import_field])) {
                            if ($person_import_field == 'code') {

                                $ma_khach_hang = $this->config->item('ma_khach_hang_prefix') . ' ' . $_SESSION['ma_khach_hang_bat_dau_tu'];
                                $person_data[$person_import_field] = $ma_khach_hang;
                                $person_data['iValid'] = TRUE;
                            } else $person_data[$person_import_field] = '';

                        }
                    }
                    // var_dump($person_data);
                    // die;
                    #----------------------------------------------------------------------------------------------- 
                    # Dành cho bảng customers
                    #-----------------------------------------------------------------------------------------------
                    # location
                    $data['created_location_id'] = $_SESSION['employee_current_location_id'];
                    # xử lý lại dữ liệu để lưu nhân viên
                    if (isset($data['created_by'])) {
                        $du_lieu = $this->Employee->get_all()->result_array();
                        foreach ($du_lieu as $value) {
                            $value['username'] = trim(strtolower($this->bo_dau_tieng_viet($value['username'])));
                            $data['created_by'] = trim(strtolower($this->bo_dau_tieng_viet($data['created_by'])));
                            if ($value['username'] == $data['created_by']) {
                                $ketqua = $value['person_id'];
                                break;
                            } else $ketqua = 1;
                        }
                        $data['created_by'] = $ketqua;
                    } else $data['created_by'] = $this->Employee->get_logged_in_employee_info()->person_id;
                    # xử lý lại dữ liệu để lưu giới tính
                    if (isset($data['sex'])) {
                        $data['sex'] = trim(strtolower($this->bo_dau_tieng_viet($data['sex'])));
                        # xử lý lại dữ liệu để lưu giới tính
                        if ($data['sex'] == 'nam') {
                            $data['sex'] = 0;
                        } elseif ($data['sex'] == 'nu') $data['sex'] = 1;
                    }
                    # xử lý lại dữ liệu để lưu phân cấp khách hàng

                    if (isset($data['tier_id'])) {
                        $du_lieu = $this->Tier->get_all()->result_array();
                        foreach ($du_lieu as $value) {
                            $value['name'] = trim(strtolower($this->bo_dau_tieng_viet($value['name'])));
                            $data['tier_id'] = trim(strtolower($this->bo_dau_tieng_viet($data['tier_id'])));
                            if ($value['name'] == $data['tier_id']) {
                                $ketqua = $value['id'];
                                break;
                            } else $ketqua = NULL;
                        }
                        $data['tier_id'] = $ketqua;
                    }
                    # xử lý lại dữ liệu để nhóm khách hàng
                    if (isset($data['type_customer'])) {
                        $du_lieu = $this->Customer->get_Customer_type();
                        foreach ($du_lieu as $value) {
                            $value['name'] = trim(strtolower($this->bo_dau_tieng_viet($value['name'])));
                            $data['type_customer'] = trim(strtolower($this->bo_dau_tieng_viet($data['type_customer'])));
                            if ($value['name'] == $data['type_customer']) {
                                $ketqua = $value['id'];
                                break;
                            } else $ketqua = NULL;
                        }
                        $data['type_customer'] = $ketqua;
                    }

                    #-----------------------------------------------------------------------------------------------
                    # Dành cho bảng people
                    #-----------------------------------------------------------------------------------------------
                    # xử lý lại lưu ngày tháng theo mẫu 30-12-2016
                    if (isset($person_data['birth_date'])) {
                        $person_data['birth_date'] = date('Y-m-d', strtotime($person_data['birth_date']));
                    }
                    #----------------------------------------------------------------------------------------------- 
                    # Dành cho bảng liên quan customers
                    #-----------------------------------------------------------------------------------------------


                    #----------------------------------------------------------------------------------------------- 
                    # Bắt đầu thực hiện lưu dữ liệu
                    #-----------------------------------------------------------------------------------------------

                    $customer_id = $this->Customer->save_customer($person_data, $data, null); # lưu khách hàng và lấy customer_id
                    if (!empty($customer_id)) {
                        $stored_rows++;

                        # lưu thuộc tính theo bộ

                        if (!empty($extend_rows)) {
                            $this->Attribute->reset_attributes(array('entity_id' => $customer_id, 'entity_type' => 'customers'));

                            foreach ($extend_rows as $attribute_id => $value) {
                                $attribute_value = array('entity_id' => $customer_id, 'entity_type' => $value['entity_type'], 'attribute_id' => $value['attribute_id'], 'entity_value' => $value['entity_value']);
                                $this->Attribute->set_attributes($attribute_value);
                            }
                        }

                        # lưu thông tin liên hệ thêm

                        if (!empty($thong_tin_lien_he_them)) {
                            $this->Customer->luu_thong_tin_lien_he_them_theo_truong_co_dinh_import_excel($thong_tin_lien_he_them, $customer_id);
                        }

                        $success_rows[] = $index;
                    } else
                        $error_rows[] = $index;
                } else {
                    $error_rows[] = $index;
                }
            } catch (Exception $ex) {
                $error_rows[] = $index;
                continue;
            }
        }

        $this->Appconfig->save('ma_khach_hang_bat_dau_tu', $_SESSION['ma_khach_hang_bat_dau_tu']);

        echo json_encode(array('error_rows' => $error_rows, 'success_rows' => $success_rows));
    }

    function bo_dau_tieng_viet($str)
    {

        if (!$str) return false;
        $unicode = array(
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ',
            'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ằ|Ẳ|Ẵ|Ặ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'd' => 'đ',
            'D' => 'Đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
            'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ'
        );
        foreach ($unicode as $nonUnicode => $uni)
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        return $str;
    }


    function cleanup()
    {
        $this->Customer->cleanup();
        $_SESSION['notice'] = 'Xóa khách hàng cũ thành công.';

        redirect(base_url() . 'customers');
    }

    function pay_now($customer_id)
    {
        $this->load->model('Sale');
        $this->load->model('Customer');
        $this->load->model('Tier');
        $this->load->model('Category');
        $this->load->model('Giftcard');
        $this->load->model('Tag');
        $this->load->model('Item');
        $this->load->model('Item_location');
        $this->load->model('Item_kit_location');
        $this->load->model('Item_kit_location_taxes');
        $this->load->model('Item_kit');
        $this->load->model('Item_kit_items');
        $this->load->model('Item_kit_taxes');
        $this->load->model('Item_location_taxes');
        $this->load->model('Item_taxes');
        $this->load->model('Item_taxes_finder');
        $this->load->model('Item_kit_taxes_finder');
        $this->load->library('sale_lib');
        $this->sale_lib->clear_all();
        $this->sale_lib->set_customer($customer_id);
        $this->sale_lib->set_mode('store_account_payment');
        $store_account_payment_item_id = $this->Item->create_or_update_store_account_item();
        $this->sale_lib->add_item($store_account_payment_item_id, 1);
        $this->sale_lib->set_store_account_payment_value(1);
        redirect('sales');
    }
}


?>