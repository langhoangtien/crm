<?php
require_once(APPPATH . "models/Customer.php");

class BizCustomer extends Customer
{
    protected $_scopeOfView = 'view_scope_owner';
    protected $_mail_template_fields = array(
        'id' => 'm.mail_id',
        'title' => 'm.mail_content'
    );

    protected $_constract_fields = array(
        'id' => 'c.id_quotes_contract',
        'title' => 'c.title_quotes_contract',
        'constract_type_name' => 'ct.title',
        'created' => 'c.created',
    );

    protected $_constract_type_fields = array(
        'id' => 'ct.id',
        'code' => 'ct.code',
        'title' => 'ct.title',
        'status' => 'ct.status',
    );

    protected $_customer_fields = array(
        'id' => 'c.id',
        'last_name' => 'p.last_name',
        'phone_number' => 'p.phone_number',
        'address_1' => 'p.address_1',
        'code' => 'p.code',
        'balance' => 'p.balance',
        'balance_2' => 'p.balance_2',
        'birth_date' => 'p.birth_date',
        'email' => 'p.email',
    );

    protected $_customer_type_fields = array(
        'customer_type_id' => 't.customer_type_id',
        'code' => 't.code',
        'name' => 't.name'
    );

    public function __construct()
    {
        if ($this->Employee->has_module_action_permission(
            'customers',
            'view_scope_all',
            $this->Employee->get_logged_in_employee_info()->person_id)
    ) {
            $this->_scopeOfView = 'view_scope_all';
        } elseif ($this->Employee->has_module_action_permission(
            'customers',
            'view_scope_location',
            $this->Employee->get_logged_in_employee_info()->person_id)
    ) {
            $this->_scopeOfView = 'view_scope_location';
        }
    }
    /*************************************************************************************************************
     *                                        Đức anh Begin                                                            *
     *                                                                                                                *
     *                                                                                                                *
     **************************************************************************************************************/
    // lấy danh mục khách hàng tổng hợp
    public function danh_muc_khach_hang_tong_hop($id = false)
    {
        $this->db->from('categories_manage');
        if ($id) {
            $this->db->where('id', $id);
        }

        $result = $this->db->get()->result_array();
        return $result;
    }

    public function get_all_danh_muc_khach_hang($customers_table = false)
    {
        $this->db->from($customers_table);
        $data = $this->db->get()->result_array();
        return $data;
    }

    public function dem_danh_muc_khach_hang($customers_table = false)
    {
        $this->db->from($customers_table);
        $data = (string)$this->db->get()->num_rows();
        return $data;
    }

    public function lay_ra_ten_danh_muc($customers_table = NULL, $id = NULL, $truong_nao = 'id')
    {
        $this->db->from($customers_table);
        $this->db->where($truong_nao, $id);
        $data = $this->db->get()->result_array();
        return $data;
    }

    // lấy danh mục khách hàng
    public function get_danh_muc_khach_hang($customers_table = false, $parrent_id = false, $iValid = false)
    {
        if ($customers_table) {
            //chỉ lấy danh mục cha
            $this->db->from($customers_table);
            $this->db->where('parrent_id', 0);
            $result = $this->db->get();
            $data = !empty($result) ? $result->result_array() : [];
            if ($parrent_id) {
                // lấy danh mục con
                $this->db->from($customers_table);
                $this->db->where('parrent_id', $parrent_id);
                $result = $this->db->get();
                $data = !empty($result) ? $result->result_array() : [];
            }
        } else {
            if ($iValid) {
                $this->db->from('exchange_form');
                $data['exchange_form'] = $this->db->get()->result_array();
                $this->db->from('business_type');
                $data['business_type'] = $this->db->get()->result_array();
                $this->db->from('geographical_area');
                $data['geographical_area'] = $this->db->get()->result_array();
                $this->db->from('company_form');
                $data['company_form'] = $this->db->get()->result_array();
            } else {
                // nhóm khách hàng
                $this->db->from('customers_type');
                $data['customers_type'] = $this->db->get()->result_array();
                // phân cấp khách hàng
                $this->db->from('price_tiers');
                $data['price_tiers'] = $this->db->get()->result_array();
                // ngành nghề kinh doanh
            }

        }
        return $data;
    }

    function sap_xep_danh_muc_theo_thu_tu($categories)
    {
        $objects = array();
        // turn to array of objects to make sure our elements are passed by reference
        foreach ($categories as $key => $value) {
            $node = new StdClass();
            $node->id = $value['id'];
            $node->parrent_id = $value['parrent_id'];
            $node->name = $value['name'];
            $node->layer = $value['layer'];
            $node->children = array();
            $objects[$value['id']] = $node;
        }
        // list dependencies parent -> children
        foreach ($objects as $obj) {
            $parrent_id = $obj->layer;

            if ($parrent_id != 0) {
                $objects[$obj->parrent_id]->children[] = $obj;
            }
        }
        // clean the object list to make kind of a tree (we keep only root elements)
        $sorted = array_filter($objects, array('BizCustomer', '_filter_to_root'));
        // flatten recursively
        $categories = self::_flatten($sorted);

        $return = array();

        foreach ($categories as $category) {
            $return[$category->id] = array('id' => $category->id, 'layer' => $category->layer, 'name' => $category->name, 'parrent_id' => $category->parrent_id);
        }
        return $return;
    }

    static function _filter_to_root($objects)
    {
        return $objects->layer == 0;
    }

    static function _flatten($elements)
    {
        $result = array();

        foreach ($elements as $element) {
            if (property_exists($element, 'children')) {
                $children = $element->children;
                unset($element->children);
            } else {
                $children = null;
            }

            $result[] = $element;

            if (isset($children)) {
                $flatened = self::_flatten($children);

                if (!empty($flatened)) {
                    $result = array_merge($result, $flatened);
                }
            }
        }
        return $result;
    }

    // lấy tất cả danh danh để tạo khách hàng mới
    // lấy tất cả các danh mục


    // lưu bảng quan hệ với customer, phần danh mục khách hàng
    public function thay_doi_bang_danh_muc_lien_ket($table_name = NULL, $customer_danh_muc_data = NULL, $customer_id = NULL)
    {
        /*
    	* xóa tất cả dữ liệu có liên quan tới customers đang updata
    	* lưu mới thành trường khác
    	*/
        $this->db->where('customer_id', $customer_id);
        $this->db->delete('customers_' . $table_name);
        /*
    	* chèn dữ liệu mới vào bảng
    	*/
        foreach ($customer_danh_muc_data as $value) {
            // biến value tương ứng với business_type_id <> danh mục id
            $this->db->insert('customers_' . $table_name, array('customer_id' => $customer_id, $table_name . '_id' => $value));
        }

    }

    // kiểm tra để hiển thị dữ liệu danh mục nào đã được chọn
    function kiem_tra_danh_muc_customer($table_name, $customer_id, $id_danh_muc)
    {
        $this->db->select($table_name . '_id');
        $this->db->from('customers_' . $table_name);
        $this->db->where('customer_id', $customer_id);
        $result = $this->db->get()->result_array();
        $authed_customers = array();
        foreach ($result as $value) {
            $authed_customers[$value[$table_name . '_id']] = TRUE;
        }
        // kết quả trả về sẽ là true or false cho biến $has_access
        return isset($authed_customers[$id_danh_muc]) && $authed_customers[$id_danh_muc];
    }

    function lay_ra_danh_muc_lien_quan_cua_khach_hang($table_name, $customer_id)
    {
        $this->db->select($table_name . '_id');
        $this->db->from('customers_' . $table_name);
        $this->db->where('customer_id', $customer_id);
        $result = $this->db->get()->result_array();

        return $result;
    }

    /**
     *  Tạo mới danh mục,
     *    Thêm sửa danh mục
     */
    public function tao_moi_danh_muc_khach_hang($category_name = "", $customers_table = NULL, $category_id = FALSE, $parrent_id = FALSE)
    {
        if (!$category_id) {
            // thêm danh mục con
            if ((bool)$parrent_id) {
                /**
                 *    lấy layer của danh mục cha để thêm vào cho danh mục con
                 *    layer là 1 trường trong database cho chức năng báo cáo danh mục này là phần tử tầng thứ mấy trong        nhóm
                 *
                 */
                $this->db->where('id', $parrent_id)
                ->select('layer');
                $layer = $this->db->get($customers_table)->result_array();
                $layer = $layer[0]['layer'] + 1; // tăng layer lên 1 vì là phần tử con
                // thêm mới dữ liệu vào bảng
                $this->db->insert($customers_table, array('name' => $category_name, 'parrent_id' => $parrent_id, 'layer' => $layer));
                $this->db->from($customers_table);
                $data = $this->db->get()->result_array();
                return $data;
            } // nếu k có parrent id thì kiểm tra
            elseif ($category_name) {
                if ($this->db->insert($customers_table, array('name' => $category_name))) {
                    $this->db->from($customers_table);
                    $data = $this->db->get()->result_array();
                    return $data;
                }
            }
        } // nếu có catagorry thì sẽ update
        else {
            $this->db->where('id', $category_id);
            $update_data = array();

            if ($category_name) {
                $update_data['name'] = $category_name;
            }
            $this->db->update($customers_table, $update_data);
            $this->db->from($customers_table);
            $data = $this->db->get()->result_array();
            return $data;
        }
        return FALSE;
    }

    // xóa danh mục khách hàng
    public function xoa_danh_muc_khach_hang($category_id = "", $customers_table = NULL, $parrent_id = null)
    {
        $this->db->where('id', $category_id);
        $this->db->delete($customers_table);
        $this->db->from($customers_table);
        if ($parrent_id == 0) {
            $this->db->where('parrent_id', $category_id);
            $this->db->delete($customers_table);
            $this->db->from($customers_table);
        }
        $data = $this->db->get()->result_array();
        return $data;
    }

    // lấy thông tin liên hệ
    public function lay_thong_tin_lien_he_them($customer_id = NULL)
    {
        $this->db->from('contract_info_add');
        $this->db->where('customer_id', $customer_id);
        $results = $this->db->get()->result_array();
        return $results;
    }

    // lấy thông tin dau moi them
    public function lay_thong_tin_dau_moi_them($customer_id = NULL)
    {
        $this->db->from('customers_head');
        $this->db->where('customer_id', $customer_id);
        $results = $this->db->get()->result_array();
        return $results;
    }

    // lưu bảng thông tin liên hệ thêm
    public function luu_thong_tin_lien_he_them($customer_data = NULL, $customer_id = NULL)
    {
        /*
    	* 	xóa tất cả dữ liệu có liên quan tới customers đang update
    	* 	lưu mới thành trường khác
    	*/
        $this->db->where('customer_id', $customer_id);
        $this->db->delete('contract_info_add');
        /*
    	* 	chèn dữ liệu mới vào bảng
    	* 	chèn dữ liệu 6 cái 1 theo cấu trúc của array
    	* 	$column = array('' => , ); là các trường của bảng
    	*/
        $column = array(
            0 => 'customer_id',
            1 => 'name_more',
            2 => 'sdt',
            3 => 'sex',
            4 => 'birthday',
            5 => 'email_more',
            6 => 'phongban',
            7 => 'note',
        );
        /**    CẤU TRÚC CỦA BIẾN DATA
         *
         *    $data[customer_id] = $customer_id;
         *    $data[trường nào đó của table] = $value;
         *    data vị trí đầu tiên sẽ có giá trị là customer_id
         *
         */
        
        $data[$column [0]] = $customer_id;

        for ($i = 0; $i < count($customer_data);) {
            $customer_data[$i]!=null?$customer_data[$i]:'';
            for ($j = 1; $j <= 7; $j++) {
                // tại biến data key là 1 trường, ta truyền giá trị của $customer_data tương ứng
                // $data[$column[$j]] = $j == 3 ? ($customer_data[$i]!=null?$customer_data[$i]:'0') : $customer_data[$i];

                $data[$column[$j]] = $j == 4 ? ($customer_data[$i]!=null?date('Y-m-d', strtotime($customer_data[$i])):null) : $customer_data[$i];
                $i++;
            }
            $this->db->insert('contract_info_add', $data);
        }
    }

    // lưu bảng thông tin liên hệ thêm
    public function luu_thong_tin_dau_moi_them($customer_data = NULL, $customer_id = NULL)
    {
        /*
    	* 	xóa tất cả dữ liệu có liên quan tới customers đang update
    	* 	lưu mới thành trường khác
    	*/
        $this->db->where('customer_id', $customer_id);
        $this->db->delete('customers_head');
        /*
    	* 	chèn dữ liệu mới vào bảng
    	* 	chèn dữ liệu 6 cái 1 theo cấu trúc của array
    	* 	$column = array('' => , ); là các trường của bảng
    	*/
        $column = array(
            0 => 'customer_id',
            1 => 'name',
            2 => 'phone',
            3 => 'email',
            4 => 'department',
            5 => 'note',
        );
        /**    CẤU TRÚC CỦA BIẾN DATA
         *
         *    $data[customer_id] = $customer_id;
         *    $data[trường nào đó của table] = $value;
         *    data vị trí đầu tiên sẽ có giá trị là customer_id
         *
         */
        // var_dump($customer_data);
        $data[$column [0]] = $customer_id;

        for ($i = 0; $i < count($customer_data);) {
            for ($j = 1; $j <= 5; $j++) {
                // tại biến data key là 1 trường, ta truyền giá trị của $customer_data tương ứng
                $data[$column[$j]] = $customer_data[$i];

                $i++;
            }
            $this->db->insert('customers_head', $data);
        }
    }

    function tim_kiem_de_luu_du_lieu($keyword, $field_value, $table)
    {
        $this->db->select('id')
        ->from($table)
        ->like($field_value, $keyword);
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function luu_thong_tin_lien_he_them_theo_truong_co_dinh_import_excel($customer_data = NULL, $customer_id = NULL)
    {
        $customer_data['customer_id'] = $customer_id;
        $this->db->insert('contract_info_add', $customer_data);

    }

    function check_duplicate($name, $email, $phone_number, $id = 0)
    {
        if (!$email) {
            //Set to an email no one would have
            $email = 'no-reply@mg.4biz.vn';
        }

        if (!$phone_number) {
            //Set to phone number no one would have
            $phone_number = '555-555-5555';
        }
        // sửa lại hàm chỉ kiểm tra last_name
        $this->db->from('customers');
        $this->db->join('people', 'customers.person_id=people.person_id');
        // $this->db->where('deleted',0);
        $this->db->where("CONCAT(first_name,' ',last_name) = " . $this->db->escape($name) . ' and people.person_id !=' . $this->db->escape($id));
        $this->db->where('people.person_id !=' . $id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return true;
        }

        return false;
    }

    function getCustomerByPeopleId($peopleId = '')
    {
        $this->db->from('customers');
        $this->db->where_in('customers.person_id', $peopleId);
        $result = $this->db->get();
        return !empty($result) ? reset($result->result_array()) : [];
    }

    /****************************************************************************************************************
     *                                        Đức anh end                                                                *
     *                                                                                                                *
     *                                                                                                                *
     *****************************************************************************************************************/
    public function get_all_quotes_contract_type()
    {
        $this->db->select('id, title')
        ->from('quotes_contract_type');

        $query = $this->db->get();

        $result = $query->result_array();
        $this->db->flush_cache();

        return $result;
    }

    public function checkExistByCreatedTime($created_time = '')
    {
        $this->db->from('customers');
        $this->db->where('created_time', $created_time);
        $result = $this->db->get();
        if ($result->num_rows() > 0) {
            return true;
        }
        return false;
    }

    public function getByCreatedTime($created_time = '')
    {
        $this->db->from('customers');
        $this->db->where('created_time', $created_time);
        $result = $this->db->get()->result_array();;
        if (isset($result[0])) {
            return $result[0];
        }
        return [];
    }

    public function getOfflineList($search = [])
    {
        $this->db->from('customers');
        $this->db->where('customers.deleted', 0);
        $this->db->where('offline', 1);

        $customers = $this->db->get()->result_array();

        $records = [];

        for ($k = 0; $k < count($customers); $k++) {
            $offlineDetail = [];
            $offlineDetail['customer'] = $customers[$k];
            $offlineDetail['person'] = $this->getOfflinePerson($customers[$k]['person_id']);
            $records[] = $offlineDetail;
        }

        return $records;
    }

    public function getOfflinePerson($person_id = 0)
    {
        $this->db->from('people');
        $this->db->where('person_id', $person_id);
        $result = $this->db->get()->result_array();

        if (isset($result[0])) {
            return $result[0];
        }
        return null;
    }

    public function update_balance($customerId, $balance)
    {
        $this->db->where_in('person_id', $customerId);
        return $this->db->update('customers', array('balance' => $balance));
    }

    public function update_point($customerId, $point)
    {
        $this->db->where_in('person_id', $customerId);
        return $this->db->update('customers', array('points' => $point));
    }

    public function getStoreAccountDetail($search = [])
    {
        $this->db->distinct();
        $this->db->select('store_accounts.customer_id');
        $this->db->from('store_accounts');
        $this->db->join('customers', 'customers.person_id = store_accounts.customer_id');
        $this->db->join('sales', 'sales.sale_id = store_accounts.sale_id');
        $this->db->where('customers.deleted', 0);
        $this->db->where('customers.balance !=', 0);

        $result = $this->db->get()->result_array();
        foreach ($result as $row) {
            $customer_ids_for_report[] = $row['customer_id'];
        }

        foreach ($customer_ids_for_report as $customer_id) {
            $this->db->from('store_accounts');
            $this->db->where('store_accounts.customer_id', $customer_id);
            $this->db->join('sales', 'sales.sale_id = store_accounts.sale_id');
            $result = $this->db->get()->result_array();
            //If we don't have results from this month, pull the last store account entry we have
            if (count($result) == 0) {
                $this->db->from('store_accounts');
                $this->db->where('store_accounts.customer_id', $customer_id);
                $this->db->join('sales', 'sales.sale_id = store_accounts.sale_id');
                $this->db->limit(1);
                if ($this->params['pull_payments_by'] == 'payment_date') {
                    $this->db->order_by('date', 'DESC');
                } else {
                    $this->db->order_by('sale_time', 'DESC');
                }

                $this->db->limit(1);
                $result = $this->db->get()->result_array();

            }

            for ($k = 0; $k < count($result); $k++) {
                $item_names = array();
                $sale_id = $result[$k]['sale_id'];
                $this->db->select('name, sales_items.description');
                $this->db->from('items');
                $this->db->join('sales_items', 'sales_items.item_id = items.item_id');
                $this->db->where('sale_id', $sale_id);

                foreach ($this->db->get()->result_array() as $row) {
                    $item_name_and_desc = $row['name'];

                    if ($row['description']) {
                        $item_name_and_desc .= ' - ' . $row['description'];
                    }

                    $item_names[] = $item_name_and_desc;
                }

                $this->db->select('name');
                $this->db->from('item_kits');
                $this->db->join('sales_item_kits', 'sales_item_kits.item_kit_id = item_kits.item_kit_id');
                $this->db->where('sale_id', $sale_id);

                foreach ($this->db->get()->result_array() as $row) {
                    $item_names[] = $row['name'];
                }

                $result[$k]['items'] = implode(', ', $item_names);
            }
            $return[] = array('customer_info' => $this->Customer->get_info($customer_id), 'store_account_transactions' => $result);
        }
        return $return;
    }

    public function getData()
    {
        $return = array();

        $customer_ids_for_report = array();
        $customer_id = $this->params['customer_id'];

        if ($customer_id == -1) {
            $this->db->distinct();
            $this->db->select('store_accounts.customer_id');
            $this->db->from('store_accounts');
            $this->db->join('customers', 'customers.person_id = store_accounts.customer_id');
            $this->db->join('sales', 'sales.sale_id = store_accounts.sale_id');
            $this->db->where('customers.deleted', 0);
            $this->db->where('customers.balance !=', 0);
            $this->db->limit($this->report_limit);
            $this->db->offset($this->params['offset']);
            $result = $this->db->get()->result_array();

            foreach ($result as $row) {
                $customer_ids_for_report[] = $row['customer_id'];
            }
        } else {
            $this->db->select('person_id');
            $this->db->from('customers');
            $this->db->where('balance !=', 0);
            $this->db->where('person_id', $customer_id);
            $this->db->where('deleted', 0);

            $result = $this->db->get()->row_array();

            if (!empty($result)) {
                $customer_ids_for_report[] = $result['person_id'];
            }
        }

        foreach ($customer_ids_for_report as $customer_id) {
            $this->db->from('store_accounts');
            $this->db->where('store_accounts.customer_id', $customer_id);
            $this->db->join('sales', 'sales.sale_id = store_accounts.sale_id');

            if ($this->params['pull_payments_by'] == 'payment_date') {
                $this->db->where('date >=', $this->params['start_date']);
                $this->db->where('date <=', $this->params['end_date'] . '23:59:59');
                $this->db->order_by('date');
            } else {
                $this->db->where('sale_time >=', $this->params['start_date']);
                $this->db->where('sale_time <=', $this->params['end_date'] . '23:59:59');
                $this->db->order_by('sale_time', ($this->config->item('report_sort_order')) ? $this->config->item('report_sort_order') : 'asc');
            }


            $result = $this->db->get()->result_array();

            //If we don't have results from this month, pull the last store account entry we have
            if (count($result) == 0) {
                $this->db->from('store_accounts');
                $this->db->where('store_accounts.customer_id', $customer_id);
                $this->db->join('sales', 'sales.sale_id = store_accounts.sale_id');
                $this->db->limit(1);
                if ($this->params['pull_payments_by'] == 'payment_date') {
                    $this->db->order_by('date', 'DESC');
                } else {
                    $this->db->order_by('sale_time', 'DESC');
                }

                $this->db->limit(1);
                $result = $this->db->get()->result_array();

            }

            for ($k = 0; $k < count($result); $k++) {
                $item_names = array();
                $sale_id = $result[$k]['sale_id'];

                $this->db->select('name, sales_items.description');
                $this->db->from('items');
                $this->db->join('sales_items', 'sales_items.item_id = items.item_id');
                $this->db->where('sale_id', $sale_id);

                foreach ($this->db->get()->result_array() as $row) {
                    $item_name_and_desc = $row['name'];

                    if ($row['description']) {
                        $item_name_and_desc .= ' - ' . $row['description'];
                    }

                    $item_names[] = $item_name_and_desc;
                }

                $this->db->select('name');
                $this->db->from('item_kits');
                $this->db->join('sales_item_kits', 'sales_item_kits.item_kit_id = item_kits.item_kit_id');
                $this->db->where('sale_id', $sale_id);

                foreach ($this->db->get()->result_array() as $row) {
                    $item_names[] = $row['name'];
                }

                $result[$k]['items'] = implode(', ', $item_names);
            }
            $return[] = array('customer_info' => $this->Customer->get_info($customer_id), 'store_account_transactions' => $result);
        }

        return $return;
    }

    /*
	Returns all the customers
	*/
    function get_all($limit = 10000, $offset = 0, $col = 'last_name', $order = 'asc', $extra = array())
    {
        $order_by = '';

        if (!$this->config->item('speed_up_search_queries')) {
            $order_by = "ORDER BY " . $col . " " . $order;
        }

        $people = $this->db->dbprefix('people');
        $customers = $this->db->dbprefix('customers');
        $data = $this->db->query("SELECT * 
          FROM " . $people . "
          STRAIGHT_JOIN " . $customers . " ON 										                       
          " . $people . ".person_id = " . $customers . ".person_id
          WHERE deleted =0 $order_by 
          LIMIT  " . $offset . "," . $limit);

        return $data;


    }

    function get_all_export_excel($extra = array())
    {
        $people = $this->db->dbprefix('people');
        $customers = $this->db->dbprefix('customers');
        $employees_locations = $this->db->dbprefix('employees_locations');
        $contract_info_add = $this->db->dbprefix('contract_info_add');
        $business_type = $this->db->dbprefix('customers_business_type');
        $business_type_name = $this->db->dbprefix('business_type');
        $exchange_form = $this->db->dbprefix('customers_exchange_form');
        $exchange_form_name = $this->db->dbprefix('exchange_form');
        $geographical_area = $this->db->dbprefix('customers_geographical_area');
        $geographical_area_name = $this->db->dbprefix('geographical_area');
        $this->db->select('*,GROUP_CONCAT(DISTINCT ga_name.name) AS geographical_area,GROUP_CONCAT(DISTINCT bu_name.name) AS business_type,GROUP_CONCAT(DISTINCT ex_name.name) AS exchange_form')
        ->from($customers . ' AS c')
        ->join($people . ' AS p', 'c.person_id = p.person_id')
        ->join($contract_info_add . ' as con', 'c.person_id = con.customer_id', 'left')
        ->join($geographical_area . ' AS ga', 'c.person_id = ga.customer_id', 'left')
        ->join($business_type . ' AS bu', 'c.person_id = bu.customer_id', 'left')
        ->join($exchange_form . ' AS ex', 'c.person_id = ex.customer_id', 'left')
        ->join($geographical_area_name . ' AS ga_name', 'ga.geographical_area_id = ga_name.id', 'left')
        ->join($exchange_form_name . ' AS ex_name', 'ex.exchange_form_id = ex_name.id', 'left')
        ->join($business_type_name . ' AS bu_name', 'bu.business_type_id = bu_name.id', 'left');

        if (isset($extra['scope_of_view']) && $extra['scope_of_view'] == 'view_scope_owner') {
            $this->db->where('c.created_by', $this->Employee->get_logged_in_employee_info()->person_id);
            $this->db->where('c.created_location_id', $this->Employee->get_logged_in_employee_current_location_id());

        }
        if (isset($extra['scope_of_view']) && $extra['scope_of_view'] == 'view_scope_location') {
            $this->db->where('c.created_location_id', $this->Employee->get_logged_in_employee_current_location_id());

        }
        $this->db->where('c.deleted = 0');
        $this->db->group_by('c.person_id');

        $data = $this->db->get();
        return $data;
        // $query = "SELECT *,GROUP_CONCAT(DISTINCT ga_name.name) as geographical_area,GROUP_CONCAT(DISTINCT bu_name.name) as business_type,GROUP_CONCAT(DISTINCT ex_name.name) as exchange_form FROM ".$customers." as c
        // JOIN ".$people." as p ON c.person_id = p.person_id
        // JOIN ".$contract_info_add." as con ON c.person_id = con.customer_id
        // JOIN ".$geographical_area." as ga ON c.person_id = ga.customer_id
        // JOIN ".$business_type." as bu ON c.person_id = bu.customer_id
        // JOIN ".$exchange_form." as ex ON c.person_id = ex.customer_id
        // JOIN ".$geographical_area_name." as ga_name ON ga.geographical_area_id = ga_name.id
        // JOIN ".$exchange_form_name." as ex_name ON ex.exchange_form_id = ex_name.id
        // JOIN ".$business_type_name." as bu_name ON bu.business_type_id = bu_name.id
        // WHERE c.deleted = 0 GROUP BY c.person_id ".$order_by." LIMIT  ".$offset.",".$limit."";

    }

    function count_all($extra = array())
    {
        $this->db->from('customers');
        $this->db->where('deleted', 0);
        if (isset($extra['scope_of_view']) && $extra['scope_of_view'] == 'view_scope_owner') {
            $this->db->where('created_by', $this->Employee->get_logged_in_employee_info()->person_id);
        } elseif (isset($extra['scope_of_view']) && $extra['scope_of_view'] == 'view_scope_location') {
            $this->db->where('created_location_id', $this->Employee->get_logged_in_employee_current_location_id());
        }

        return $this->db->count_all_results();
    }

    function count_all_customers($extra = array())
    {
        $this->db->from('customers');
        $this->db->where('deleted', 0);

        return $this->db->count_all_results();
    }

    function count_item_by_filter($arrParams = null)
    {
        $filter = isset($_SESSION[$arrParams['key_filter']]) ? $_SESSION[$arrParams['key_filter']] : array();

        $this->db->select('COUNT(c.id) AS totalItem')
        ->from('customers AS c')
        ->join('people AS p', 'c.person_id = p.person_id')
        ->where('c.deleted', 0);

        if ($arrParams['key_filter'] == 'customer_birthday_filter') {
            $current_month = date("m");
            $current_day = date("d");

            $this->db->where("MONTH(p.birth_date)", $current_month)
            ->where("DAY(p.birth_date)", $current_day);
        }
        if ($arrParams['key_filter'] == 'customer_balance_filter') {
            $this->db->where('c.balance > 0');
        }
        if ($arrParams['key_filter'] == 'customer_tmp_filter') {
            $customer_ids = !empty($_SESSION['customer_tmp_ids']) ? $_SESSION['customer_tmp_ids'] : '';
            if (!empty($customer_ids)) {
                $this->db->where('c.person_id IN (' . implode(',', $customer_ids) . ')');
            } else
            $this->db->where('c.person_id = -1');
        }
        if (isset($arrParams['scope_of_view']) && $arrParams['scope_of_view'] == 'view_scope_owner') {
            $this->db->where('c.created_by', $this->Employee->get_logged_in_employee_info()->person_id);
            $this->db->where('c.created_location_id', $this->Employee->get_logged_in_employee_current_location_id());
        }
        if (isset($arrParams['scope_of_view']) && $arrParams['scope_of_view'] == 'view_scope_location') {
            $this->db->where('c.created_location_id', $this->Employee->get_logged_in_employee_current_location_id());
        }

        if (!empty($filter['keywords'])) {
            $keywords = trim($filter['keywords']);
            $this->db->where('(p.first_name LIKE \'%' . $keywords . '%\' OR p.last_name LIKE \'%' . $keywords . '%\' OR p.address_1 LIKE \'%' . $keywords . '%\' OR p.address_2 LIKE \'%' . $keywords . '%\' OR p.phone_number LIKE \'%' . $keywords . '%\' OR p.email LIKE \'%' . $keywords . '%\')');
        }

        if (!empty($filter['tier_id']) && $filter['tier_id'] > 0) {
            $this->db->where('c.tier_id', $filter['tier_id']);
        }

        if (!empty($filter['type'])) {
            $this->db->where('c.' . $filter['type'] . ' > 0');
        }

        if (!empty($filter['customer_type']) && $filter['customer_type'] > 0) {
            $this->db->where('c.type_customer', $arrParams['customer_type']);
        }

        $query = $this->db->get();

        $result = $query->row()->totalItem;

        $this->db->flush_cache();

        return $result;
    }

    function count_item($arrParams = null, $options = null)
    {
        $key_filter = isset($arrParams['key_filter']) ? $arrParams['key_filter'] : '';

        $this->db->select('COUNT(DISTINCT(c.id)) AS totalItem')
        ->from('customers AS c')
        ->join('people AS p', 'c.person_id = p.person_id')
        ->where('c.deleted', 0);

        if ($key_filter == 'customer_tmp_filter') {
            if (!empty($arrParams['customer_ids'])) {
                $customer_ids = implode(', ', $arrParams['customer_ids']);
                $this->db->where('c.person_id IN (' . $customer_ids . ')');
            } else
            $this->db->where('c.person_id = -1');
        }

        if (!empty($arrParams['keywords'])) {
            $keywords = trim($arrParams['keywords']);
            $this->db->where('(p.first_name LIKE \'%' . $keywords . '%\' OR p.last_name LIKE \'%' . $keywords . '%\' OR p.address_1 LIKE \'%' . $keywords . '%\' OR p.address_2 LIKE \'%' . $keywords . '%\' OR p.phone_number LIKE \'%' . $keywords . '%\' OR p.email LIKE \'%' . $keywords . '%\')');
            $_SESSION[$key_filter]['keywords'] = $keywords;
        }
        if (!empty($arrParams['start_date']) && !empty($arrParams['end_date'])) {
            $start = $arrParams['start_date'];
            $end = $arrParams['end_date'];
            $this->db->where("c.created_time BETWEEN '" . $start . "' AND '" . $end . "'");
        }
        if (isset($arrParams['tier_id']) && $arrParams['tier_id'] > 0) {
            $this->db->where('c.tier_id', $arrParams['tier_id']);

            $_SESSION[$key_filter]['tier_id'] = $arrParams['tier_id'];
        }
        if (!empty($arrParams['employee_id']) && $arrParams['employee_id'] > 0) {
            $this->db->where('c.created_by', $arrParams['employee_id']);
            $_SESSION[$key_filter]['employee_id'] = $arrParams['employee_id'];
        }
        if (!empty($arrParams['customer_type']) && $arrParams['customer_type'] > 0) {
            $this->db->where('c.type_customer', $arrParams['customer_type']);

            $_SESSION[$key_filter]['customer_type'] = $arrParams['customer_type'];
        }

        if (isset($arrParams['scope_of_view']) && $arrParams['scope_of_view'] == 'view_scope_owner') {
            $this->db->where('c.created_by', $this->Employee->get_logged_in_employee_info()->person_id);
            $this->db->where('c.created_location_id', $this->Employee->get_logged_in_employee_current_location_id());
            $this->db->or_like('c.watcher_manager', '"'.$this->Employee->get_logged_in_employee_info()->person_id.'"');
            $_SESSION[$key_filter]['scope_of_view'] = $arrParams['scope_of_view'];
        }
        if (isset($arrParams['scope_of_view']) && $arrParams['scope_of_view'] == 'view_scope_location') {
            $this->db->where('c.created_location_id', $this->Employee->get_logged_in_employee_current_location_id());
            $this->db->or_like('c.watcher_manager', '"'.$this->Employee->get_logged_in_employee_info()->person_id.'"');
            $_SESSION[$key_filter]['scope_of_view'] = $arrParams['scope_of_view'];
        }

        if (isset($arrParams['of_month']) && $arrParams['of_month'] == true) {
            $current_month = date("m");
            $current_day = date("d");

            $this->db->where("MONTH(p.birth_date)", $current_month)
            ->where("DAY(p.birth_date)", $current_day);
        }
        if (!empty($arrParams['type'])) {
            $this->db->where('c.' . $arrParams['type'] . ' > 0');

            $_SESSION[$key_filter]['type'] = $arrParams['type'];
        }

        if (isset($arrParams['category_child']) && ($arrParams['category_child'] != -1) && isset($arrParams['categoryId'])) {
            $this->db->where('c.type_customer', $arrParams['category_child']);
        }

        $query = $this->db->get();

        $result = $query->row()->totalItem;

        $this->db->flush_cache();

        return $result;
    }

    function count_type_list($arrParams = null, $options = null)
    {
        $key_filter = 'customer_type_list_filter';

        $this->db->select('COUNT(t.customer_type_id) AS totalItem')
        ->from('customer_type AS t');

        if (!empty($arrParams['keywords'])) {
            $keywords = trim($arrParams['keywords']);
            $this->db->where('(t.code LIKE \'%' . $keywords . '%\' OR t.name LIKE \'%' . $keywords . '%\' OR t.desc LIKE \'%' . $keywords . '%\')');
            $_SESSION[$key_filter]['keywords'] = $keywords;
        }

        $query = $this->db->get();

        $result = $query->row()->totalItem;

        $this->db->flush_cache();

        return $result;
    }

    function search_count_all($searchParams, $limit = 10000)
    {
        $searchText = is_array($searchParams) ? $searchParams['search_text'] : $searchParams;

        $this->db->from('customers');
        $this->db->join('people', 'customers.person_id=people.person_id');

        if ($searchText) {
            if ($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method')) {
                $this->db->where("(MATCH (first_name, last_name, email, phone_number) AGAINST (" . $this->db->escape(escape_full_text_boolean_search($searchText) . '*') . " IN BOOLEAN MODE" . ") or MATCH(account_number, company_name, tax_certificate) AGAINST (" . $this->db->escape(escape_full_text_boolean_search($searchText) . '*') . " IN BOOLEAN MODE" . "))and " . $this->db->dbprefix('customers') . ".deleted=0", NULL, FALSE);
            } else {
                $this->db->where("(first_name LIKE '%" . $this->db->escape_like_str($searchText) . "%' or 
                    last_name LIKE '%" . $this->db->escape_like_str($searchText) . "%' or 
                    email LIKE '%" . $this->db->escape_like_str($searchText) . "%' or 
                    phone_number LIKE '%" . $this->db->escape_like_str($searchText) . "%' or 
                    account_number LIKE '%" . $this->db->escape_like_str($searchText) . "%' or 
                    company_name LIKE '%" . $this->db->escape_like_str($searchText) . "%' or 
                    CONCAT(`first_name`,' ',`last_name`) LIKE '%" . $this->db->escape_like_str($searchText) . "%') and deleted=0");
            }
        } else {
            $this->db->where('deleted', 0);
        }

        if (isset($searchParams['tier_id']) && $searchParams['tier_id'] != 'all') {
            if ($searchParams['tier_id'] == 0) {
                $this->db->where('tier_id IS NULL');
            } else {
                $this->db->where('tier_id', $searchParams['tier_id']);
            }

        }

        if (isset($searchParams['employee_id']) && $searchParams['employee_id'] != 'all') {
            $this->db->where('created_by', $searchParams['employee_id']);
        }

        if (isset($searchParams['scope_of_view']) && $searchParams['scope_of_view'] == 'view_scope_owner') {
            $this->db->where('c.created_by', $this->Employee->get_logged_in_employee_info()->person_id);
            $this->db->where('c.created_location_id', $this->Employee->get_logged_in_employee_current_location_id());

        }
        if (isset($searchParams['scope_of_view']) && $searchParams['scope_of_view'] == 'view_scope_location') {
            $this->db->where('c.created_location_id', $this->Employee->get_logged_in_employee_current_location_id());
        }
        if (!empty($searchParams['start_date']) && !empty($searchParams['end_date'])) {
            $start = $searchParams['start_date'];
            $end = $searchParams['end_date'];
            $this->db->where("created_time BETWEEN '" . $start . "' AND '" . $end . "'");
        }
        $this->db->limit($limit);
        $result = $this->db->get();
        return $result->num_rows();
    }

    /*
	Preform a search on customers
	*/
    function search($searchParams, $limit = 20, $offset = 0, $column = 'last_name', $orderby = 'asc')
    {
        if (is_array($searchParams) && !empty($searchParams['search_text'])) {
            $searchText = $searchParams['search_text'];
        } elseif (!is_array($searchParams)) {
            $searchText = $searchParams;
        } else {
            $searchText = false;
        }


        $this->db->from('customers');
        $this->db->join('people', 'customers.person_id=people.person_id');

        if ($searchText) {
            if ($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method')) {
                $this->db->where("(MATCH (first_name, last_name, email, phone_number) AGAINST (" . $this->db->escape(escape_full_text_boolean_search($searchText) . '*') . " IN BOOLEAN MODE" . ") or MATCH(account_number, company_name, tax_certificate) AGAINST (" . $this->db->escape(escape_full_text_boolean_search($searchText) . '*') . " IN BOOLEAN MODE" . "))and " . $this->db->dbprefix('customers') . ".deleted=0", NULL, FALSE);
            } else {
                $this->db->where("(first_name LIKE '%" . $this->db->escape_like_str($searchText) . "%' or 
                    last_name LIKE '%" . $this->db->escape_like_str($searchText) . "%' or 
                    email LIKE '%" . $this->db->escape_like_str($searchText) . "%' or 
                    phone_number LIKE '%" . $this->db->escape_like_str($searchText) . "%' or 
                    account_number LIKE '%" . $this->db->escape_like_str($searchText) . "%' or 
                    company_name LIKE '%" . $this->db->escape_like_str($searchText) . "%' or 
                    CONCAT(`first_name`,' ',`last_name`) LIKE '%" . $this->db->escape_like_str($searchText) . "%' or 
                    CONCAT(`last_name`,', ',`first_name`) LIKE '%" . $this->db->escape_like_str($searchText) . "%' or 
                    CONCAT(`last_name`,', ',`first_name`, ' ('," . $this->db->dbprefix('customers') . ".person_id,')') LIKE '%" . $this->db->escape_like_str($searchText) . "%'
                ) and deleted=0");
            }
        } else {
            $this->db->where('deleted', 0);
        }

        if (!empty($searchParams['start_date']) && !empty($searchParams['end_date'])) {
            $start = $searchParams['start_date'];
            $end = $searchParams['end_date'];
            $this->db->where("created_time BETWEEN '" . $start . "' AND '" . $end . "'");
        }

        if (isset($searchParams['tier_id']) && $searchParams['tier_id'] != 'all') {
            if ($searchParams['tier_id'] == 0) {
                $this->db->where('tier_id IS NULL');
            } else {
                $this->db->where('tier_id', $searchParams['tier_id']);
            }

        }
        // tìm kiếm theo nhân viên
        if (isset($searchParams['employee_id']) && $searchParams['employee_id'] != 'all') {
            $this->db->where('created_by', $searchParams['employee_id']);
        }

        if (isset($searchParams['scope_of_view']) && $searchParams['scope_of_view'] == 'view_scope_owner') {
            $this->db->where('c.created_by', $this->Employee->get_logged_in_employee_info()->person_id);
            $this->db->where('c.created_location_id', $this->Employee->get_logged_in_employee_current_location_id());
        }
        if (isset($searchParams['scope_of_view']) && $searchParams['scope_of_view'] == 'view_scope_location') {
            $this->db->where('c.created_location_id', $this->Employee->get_logged_in_employee_current_location_id());
        }

        if (!$this->config->item('speed_up_search_queries')) {
            $this->db->order_by($column, $orderby);
        }
        // lọc theo danh mục cha và danh mục con
        if (isset($searchParams['selected_category_child']) && ($searchParams['selected_category_child'] != 'all') && isset($searchParams['categoryId'])) {
            if ($searchParams['customers_table'] == 'customers_type') {
                $this->db->where('type_customer', $searchParams['selected_category_child']);
            } elseif ($searchParams['customers_table'] == 'price_tiers') {
                $this->db->where('tier_id', $searchParams['selected_category_child']);
            } elseif ($searchParams['customers_table'] == 'business_type') {
                $this->db->join('customers_business_type', 'customers.person_id=customers_business_type.customer_id');
                $this->db->where('business_type_id', $searchParams['selected_category_child']);
            } elseif ($searchParams['customers_table'] == 'exchange_form') {
                $this->db->join('customers_exchange_form', 'customers.person_id=customers_exchange_form.customer_id');
                $this->db->where('exchange_form_id', $searchParams['selected_category_child']);

            } elseif ($searchParams['customers_table'] == 'geographical_area') {
                $this->db->join('customers_geographical_area', 'customers.person_id=customers_geographical_area.customer_id');
                $this->db->where('geographical_area_id', $searchParams['selected_category_child']);
            }
        }
        $this->db->limit($limit);
        $this->db->offset($offset);
        return $this->db->get();
    }


    function list_type_list($arrParams = null, $options = null)
    {
        $key_filter = 'customer_type_list_filter';
        $paginator = $arrParams['paginator'];

        $this->db->select("t.*")
        ->from('customer_type AS t');

        if (!empty($arrParams['keywords'])) {
            $keywords = trim($arrParams['keywords']);
            $this->db->where('(t.code LIKE \'%' . $keywords . '%\' OR t.name LIKE \'%' . $keywords . '%\' OR t.desc LIKE \'%' . $keywords . '%\')');
            $_SESSION[$key_filter]['keywords'] = $keywords;
        }

        if (!empty($arrParams['col']) && !empty($arrParams['order'])) {
            $col = $this->_customer_type_fields[$arrParams['col']];
            $order = $arrParams['order'];

            $this->db->order_by($col, $order);

            $_SESSION[$key_filter]['col'] = $arrParams['col'];
            $_SESSION[$key_filter]['order'] = $arrParams['order'];
        }

        $page = $arrParams['page'];
        $this->db->limit($paginator['per_page'], ($page - 1) * $paginator['per_page']);

        $query = $this->db->get();

        $result = $query->result_array();

        $this->db->flush_cache();

        return $result;
    }

    function list_item($arrParams = null, $options = null)
    {

        $key_filter = isset($arrParams['key_filter']) ? $arrParams['key_filter'] : '';
        
        $this->db->select("DISTINCT(c.id), p.first_name, p.last_name, p.email,p.phone_number, p.website, 
            p.authorized_capital, p.total_assets, p.total_revenue, p.total_profit,
            c.balance, c.balance_2,c.credit_limit, 
            c.watcher_manager,
            p.image_id,p.ho_chieu,c.sex, c.person_id, p.code,p.address_1, cia.name_more as representative, cia.phongban,
            GROUP_CONCAT(ch.name  ORDER BY ch.name SEPARATOR ', ' ) as head_name, ch.email as head_email, ch.phone as head_phone, em.username as created_by, c.created_by as person_id_create")
        ->select("DATE_FORMAT(p.birth_date, '%d-%m-%Y') as birth_date_format", FALSE)
        ->select("DATE_FORMAT(c.created_time, '%d-%m-%Y') as created_time", FALSE)

        ->from('customers AS c')
        ->join('people AS p', 'p.person_id = c.person_id', 'left')
        ->join('contract_info_add AS cia', 'cia.customer_id = c.person_id', 'left')
        ->join('customers_head AS ch', 'ch.customer_id = c.person_id', 'left')
        ->join('employees AS em', 'c.created_by = em.person_id', 'left')
        ->where('c.deleted', 0)->order_by('c.id','DESC');

        if ($key_filter == 'customer_tmp_filter') {
            if (!empty($arrParams['customer_ids'])) {
                $customer_ids = implode(', ', $arrParams['customer_ids']);
                $this->db->where('c.person_id IN (' . $customer_ids . ')');
            } else
            $this->db->where('c.person_id = -1');
        }

        if (!empty($arrParams['keywords'])) {
            $keywords = trim($arrParams['keywords']);
            $this->db->where('(p.first_name LIKE \'%' . $keywords . '%\' OR p.last_name LIKE \'%' . $keywords . '%\' OR p.address_1 LIKE \'%' . $keywords . '%\' OR p.ho_chieu LIKE \'%' . $keywords . '%\' OR p.phone_number LIKE \'%' . $keywords . '%\' OR p.email LIKE \'%' . $keywords . '%\' OR p.code LIKE \'%' . $keywords . '%\' OR p.chung_minh_thu LIKE \'%' . $keywords . '%\')');
            $_SESSION[$key_filter]['keywords'] = $keywords;
        }
        if (!empty($arrParams['list_id'])) {
            $list_customer_id = $arrParams['list_id'];
            $this->db->where('c.person_id IN (' . $list_customer_id . ')');
        }
        if (isset($arrParams['of_month']) && $arrParams['of_month'] == true) {
            $current_month = date("m");
            $current_day = date("d");

            $this->db->where("MONTH(p.birth_date)", $current_month)
            ->where("DAY(p.birth_date)", $current_day);
        }
        if (isset($arrParams['tier_id']) && $arrParams['tier_id'] > 0) {
            $this->db->where('c.tier_id', $arrParams['tier_id']);

            $_SESSION[$key_filter]['tier_id'] = $arrParams['tier_id'];
        }
        if (isset($arrParams['credit_limit_percent']) && $arrParams['credit_limit_percent'] == true) {
            $this->db->where('(c.balance/c.credit_limit)>=0.6 OR (c.balance/c.credit_limit)>=0.8 OR (c.balance/c.credit_limit)>=0.9 OR (c.balance/c.credit_limit)>=0.95');
        }
        if (!empty($arrParams['employee_id']) && $arrParams['employee_id'] > 0) {
            $this->db->where('c.created_by', $arrParams['employee_id']);
            $_SESSION[$key_filter]['employee_id'] = $arrParams['employee_id'];
        }
        if ((isset($arrParams['scope_of_view']) && $arrParams['scope_of_view'] == 'view_scope_owner') || (isset($this->_scopeOfView) && $this->_scopeOfView == 'view_scope_owner')) {
         $this->db->group_start();
         $this->db->where('c.created_by', $this->Employee->get_logged_in_employee_info()->person_id);
         $this->db->where('c.created_location_id', $this->Employee->get_logged_in_employee_current_location_id());
         $this->db->or_like('c.watcher_manager', '"'.$this->Employee->get_logged_in_employee_info()->person_id.'"');
         $_SESSION[$key_filter]['scope_of_view'] = $arrParams['scope_of_view'];
         $this->db->group_end();
     }
     if ((isset($arrParams['scope_of_view']) && $arrParams['scope_of_view'] == 'view_scope_location') || (isset($this->_scopeOfView) && $this->_scopeOfView == 'view_scope_owner')) {
         $this->db->group_start();
         $this->db->where('c.created_location_id', $this->Employee->get_logged_in_employee_current_location_id());
         $this->db->or_like('c.watcher_manager', '"'.$this->Employee->get_logged_in_employee_info()->person_id.'"');
         $_SESSION[$key_filter]['scope_of_view'] = $arrParams['scope_of_view'];
         $this->db->group_end();
     }

     if (!empty($arrParams['start_date']) && !empty($arrParams['end_date'])) {
        $start = $arrParams['start_date'];
        $end = $arrParams['end_date'];
        $this->db->where("c.created_time BETWEEN '" . $start . "' AND '" . $end . "'");
    }
    if (!empty($arrParams['type'])) {
        $this->db->where('c.' . $arrParams['type'] . ' > 0');

        $_SESSION[$key_filter]['type'] = $arrParams['type'];
    }

    if (!empty($arrParams['col']) && !empty($arrParams['order'])) {
        $col = $this->_customer_fields[$arrParams['col']];

        $order = $arrParams['order'];

        $this->db->order_by($col, $order);
            // $this->db->order_by('email', 'ASC');

        $_SESSION[$key_filter]['col'] = $arrParams['col'];
        $_SESSION[$key_filter]['order'] = $arrParams['order'];
    }
        // tìm theo danh mục con
    if (isset($arrParams['category_child']) && ($arrParams['category_child'] != -1) && isset($arrParams['categoryId'])) {
        if ($arrParams['customers_table'] == 'customers_type') {
            $this->db->where('type_customer', $arrParams['category_child']);
        } elseif ($arrParams['customers_table'] == 'price_tiers') {
            $this->db->where('tier_id', $arrParams['category_child']);
        } elseif ($arrParams['customers_table'] == 'business_type') {
            $this->db->join('customers_business_type', 'c.person_id=customers_business_type.customer_id');
            $this->db->where('business_type_id', $arrParams['category_child']);
        } elseif ($arrParams['customers_table'] == 'exchange_form') {
            $this->db->join('customers_exchange_form', 'c.person_id=customers_exchange_form.customer_id');
            $this->db->where('exchange_form_id', $arrParams['category_child']);

        } elseif ($arrParams['customers_table'] == 'geographical_area') {
            $this->db->join('customers_geographical_area', 'c.person_id=customers_geographical_area.customer_id');
            $this->db->where('geographical_area_id', $arrParams['category_child']);
        }
    }

    $page = isset($arrParams['page']) ? $arrParams['page'] : 1;
    if (!empty($arrParams['paginator'])) {
        $paginator = $arrParams['paginator'];
        $this->db->limit($paginator['per_page'], ($page - 1) * $paginator['per_page']);
    }
    $query= $this->db->group_by('c.id');
    $query = $this->db->get();
    $result = !empty($query) ? $query->result_array() : [];
    $this->db->flush_cache();
    return $result;
}

function get_all_sms($limit = 10000, $offset = 0, $col = 'id', $order = 'DESC')
{
    $this->db->from('sms');
    $this->db->where('deleted', 0);
    $this->db->order_by($col, $order);
    $this->db->limit($limit);
    $this->db->offset($offset);
    return $this->db->get();
}

function get_info_sms($id)
{
    $this->db->from('sms');
    $this->db->where('id', $id);
    $query = $this->db->get();
    if ($query->num_rows() == 1) {
        return $query->row();
    } else {
            //Get empty base parent object, as $customer_id is NOT an customer
        $person_obj = parent::get_info(-1);
            //Get all the fields from customer table
        $fields = $this->db->list_fields('sms');
            //append those fields to base parent object, we we have a complete empty object
        foreach ($fields as $field) {
            $person_obj->$field = '';
        }
        return $person_obj;
    }
}

function exists_sms($id)
{
    $this->db->where('id', $id);
    $query = $this->db->get("sms");
    return ($query->num_rows() == 1);
}

function save_sms(&$sms_data, $id = false)
{
    if (!$id or !$this->exists_sms($id)) {
        if ($this->db->insert('sms', $sms_data)) {
            $sms_data['id'] = $this->db->insert_id();
            return true;
        }
        return false;
    }
    $this->db->where('id', $id);
    return $this->db->update('sms', $sms_data);
}

function delete_sms_template($arrParams)
{
    if (!empty($arrParams['sms_id'])) {
        $this->db->where('id IN (' . implode(',', $arrParams['sms_id']) . ')');
        $this->db->delete('sms');
    }
}

function delete_sms_list($sms_ids)
{
    $this->db->where_in('id', $sms_ids);
    return $this->db->update('sms', array('deleted' => 1));
}

function get_search_suggestions_sms($search, $limit = 25)
{
    $this->db->from('sms');
    $this->db->where('deleted', 0);
    $this->db->like('title', $search);
    $this->db->order_by("id", "asc");
    $sms = $this->db->get();

    foreach ($sms->result() as $row) {
        $suggestions[] = array('label' => $row->title);
    }
        //only return $limit suggestions
    if (count($suggestions > $limit)) {
        $suggestions = array_slice($suggestions, 0, $limit);
    }
    return $suggestions;
}

function search_sms($search, $limit = 20, $offset = 0, $column = 'id', $orderby = 'desc')
{
    $this->db->from('sms');
    $this->db->like('title', $search);
    $this->db->where('deleted', 0);
    $this->db->order_by($column, $orderby);
    $this->db->limit($limit);
    $this->db->offset($offset);
    return $this->db->get();
}

function search_count_sms($search, $limit = 10000)
{
    $this->db->from('sms');
    $this->db->like('title', $search);
    $this->db->where('deleted', 0);
    $result = $this->db->get();
    return $result->num_rows();
}

function get_table_number_sms()
{
    $this->db->select_max("id");
    $query = $this->db->get("number_sms");
    return $query->row_array();
}

function get_info_id_max_of_table_number_sms($id_max)
{
    $this->db->where("id", $id_max);
    $query = $this->db->get("number_sms");
    return $query->row_array();
}

function save_message($data)
{
    $this->db->insert("messages", $data);
}

function update_number_sms($id, $data)
{
    $this->db->where('id', $id);
    $this->db->update("number_sms", $data);
}

function get_all_quotes_contract($limit = 100, $offset = 0, $col = 'id_quotes_contract', $order = 'desc')
{
    $this->db->from('quotes_contract');
    $this->db->order_by($col, $order);
    $this->db->limit($limit);
    $this->db->offset($offset);
    return $this->db->get();
}

    //Tong loai mau
function count_all_quotes_contract_type()
{
    $this->db->from('quotes_contract_type');
    return $this->db->count_all_results();
}

function get_info_quotes_contract($id, $options = null)
{
    if ($options['task'] == null) {
        $this->db->select("c.*")
        ->from('quotes_contract AS c')
        ->where('c.id_quotes_contract', $id);

        $query = $this->db->get();

        $result = $query->row_array();
        $this->db->flush_cache();
    }
    return $result;
}

function exists_quotes_contract($id)
{
    $this->db->from('quotes_contract');
    $this->db->where('id_quotes_contract', $id);
    $query = $this->db->get();
    return ($query->num_rows() == 1);
}

function lay_mau_dinh_kem($arrParam=null){
    $this->db->select('s.name, s.code, s.document')->from('phppos_services s')
    ->join('phppos_quotes_contract_type t','t.code = s.code')
    ->join('phppos_quotes_contract qt','qt.cat_quotes_contract = t.id');
    if (!empty($arrParam['name_service'])) {
        $this->db->where('s.name', $arrParam['name_service']);
    }
    return $this->db->get()->result_array();
}
function get_list_quotes_contract($id = array())
{
    $this->db->select('id_quotes_contract,title_quotes_contract');
    $this->db->from('quotes_contract');
    $this->db->where_in('id_quotes_contract', $id);
    $query = $this->db->get();
    return ($query->result_array());
}

function save_quotes_contract(&$data, $id = false)
{
    if ($id == -1) {
        $data['created'] = @date("Y-m-d H:i:s");
        if ($this->db->insert('quotes_contract', $data)) {
            $data['id_quotes_contract'] = $this->db->insert_id();
            return true;
        }
        return false;
    } else {
        $this->db->where('id_quotes_contract', $id);
        return $this->db->update('quotes_contract', $data);
    }
}

function save_quotes_contract_type($arrParams, $id)
{
    $data['title'] = stripslashes($arrParams['title']);
    $data['code'] = stripslashes($arrParams['code']);
    $data['status'] = $arrParams['status'];
    if ($id == -1) {
        $this->db->insert('quotes_contract_type', $data);
    } else {
        $this->db->where("id", $id);
        $this->db->update('quotes_contract_type', $data);
    }
    $this->db->flush_cache();
}

function delete_list_quotes_contract($id)
{
    $this->db->where_in('id_quotes_contract', $id);
    return $this->db->delete('quotes_contract');
}

    //Xóa loại mẫu văn bản
function delete_list_quotes_contract_type($arrParams)
{
    $this->db->where('id IN (' . implode(',', $arrParams['cid']) . ')');
    $this->db->delete('quotes_contract_type');
}

function delete_customer($cid)
{
    $cid = implode(',', $cid);
    $this->db->where("person_id IN ($cid)");
    $this->db->update('customers', array('deleted' => 1));
        // update biến xoa tại bảng person
    $this->db->where("person_id IN ($cid)");
    $this->db->update('people', array('xoa' => 1));

    $this->db->flush_cache();
}

function delete_customer_type($cid)
{
    $this->db->where('customer_type_id IN (' . implode(', ', $cid) . ')');
    $this->db->delete('customer_type');

    $this->db->flush_cache();
}

function get_search_suggestions_quotes_contract($search, $limit = 25)
{
    $suggestions = array();

    $this->db->from('quotes_contract');
    $this->db->like('title_quotes_contract', $search);
    $this->db->order_by("title_quotes_contract", "asc");
    $by_name = $this->db->get();
    foreach ($by_name->result() as $row) {
        $suggestions[] = array('label' => $row->title_quotes_contract);
    }

    $this->db->from('quotes_contract');
    $this->db->like('id_quotes_contract', $search);
    $this->db->order_by("id_quotes_contract", "asc");
    $by_id = $this->db->get();
    foreach ($by_id->result() as $row) {
        $suggestions[] = array('label' => $row->id_quotes_contract);
    }

        //only return $limit suggestions
    if (count($suggestions > $limit)) {
        $suggestions = array_slice($suggestions, 0, $limit);
    }
    return $suggestions;
}

function search_quotes_contract($search, $cat = '', $limit = 20, $offset = 0, $column = 'id_quotes_contract', $orderby = 'desc')
{
    $this->db->from('quotes_contract');
    if ($cat) {
        $this->db->where("cat_quotes_contract", $cat);
    }
    $this->db->where("(title_quotes_contract LIKE '%" . $search . "%' OR id_quotes_contract LIKE '%" . $search . "%')");
    $this->db->order_by($column, $orderby);
    $this->db->limit($limit);
    $this->db->offset($offset);
    return $this->db->get();
}

function search_count_all_quotes_contract($search, $cat = '')
{
    $this->db->from('quotes_contract');
    if ($cat) {
        $this->db->where("cat_quotes_contract", $cat);
    }
    $this->db->where("(title_quotes_contract LIKE '%" . $search . "%' OR id_quotes_contract LIKE '%" . $search . "%')");
    $result = $this->db->get();
    return $result->num_rows();
}


function get_list_template_quotes_contract($cat = '')
{
    if ($cat) {
        $this->db->where("cat_quotes_contract", $cat);
    }
    $query = $this->db->get("quotes_contract");
    return $query->result_array();
}

function get_info_person_by_id($id)
{
    $this->db->where('person_id', $id);
    $query = $this->db->get('people');
    return $query->row_array();
}

function get_Customer_type()
{
    $query = $this->db->get('customers_type');
    return $query->result_array();
}

function count_all_constract($arrParams)
{
    $this->db->select('COUNT(c.id_quotes_contract) AS totalItem')
    ->from('quotes_contract AS c');

    if (!empty($arrParams['keywords'])) {
        $keywords = trim($arrParams['keywords']);
        $this->db->where('c.title_quotes_contract LIKE \'%' . $keywords . '%\' OR c.content_quotes_contract LIKE \'%' . $keywords . '%\'');

        $_SESSION['quotes_constract_filter']['keywords'] = $keywords;
    }

    $query = $this->db->get();

    $result = $query->row()->totalItem;

    $this->db->flush_cache();

    return $result;
}

function count_all_constract_type($arrParams)
{
    $this->db->select('COUNT(ct.id) AS totalItem')
    ->from('quotes_contract_type AS ct');

    if (!empty($arrParams['keywords'])) {
        $keywords = trim($arrParams['keywords']);
        $this->db->where('ct.title LIKE \'%' . $keywords . '%\'');

        $_SESSION['constract_type_filter']['keywords'] = $keywords;
    }

    $query = $this->db->get();

    $result = $query->row()->totalItem;

    $this->db->flush_cache();

    return $result;
}

function count_all_mail($arrParams)
{
    $this->db->select('COUNT(m.mail_id) AS totalItem')
    ->from('mail_template AS m');


    function count_all_mail($arrParams)
    {
        $this->db->select('COUNT(m.mail_id) AS totalItem')
        ->from('mail_template AS m');
    }

    if (!empty($arrParams['search_text'])) {
        $keywords = trim($arrParams['search_text']);
        $this->db->where('m.mail_title LIKE \'%' . $keywords . '%\' OR mail_content LIKE \'%' . $keywords . '%\'');


    }

    $query = $this->db->get();

    $result = $query->row()->totalItem;

    $this->db->flush_cache();

    return $result;
}


function get_constract_type_info($id, $options = null)
{
    if ($options['task'] == null) {
        $this->db->select("ct.*")
        ->from('quotes_contract_type AS ct')
        ->where('ct.id', $id);

        $query = $this->db->get();

        $result = $query->row_array();
        $this->db->flush_cache();
    }
    return $result;
}

function constract_list($arrParams)
{

    $this->db->select("c.id_quotes_contract AS id,c.content_quotes_contract as content, c.title_quotes_contract AS title, ct.title AS constract_type_name")
    ->select("DATE_FORMAT(c.created, '%d-%m-%Y %H:%i:%s') as created", FALSE)
    ->from('quotes_contract AS c')
    ->join('quotes_contract_type AS ct', 'ct.id = c.cat_quotes_contract', 'left');


    if (!empty($arrParams['id_quotes_contract'])) {
        $id_quotes_contract = trim($arrParams['id_quotes_contract']);
        $this->db->where('c.id_quotes_contract', $id_quotes_contract);
    }
    if (!empty($arrParams['keywords'])) {
        $keywords = trim($arrParams['keywords']);
        $this->db->where('c.title_quotes_contract LIKE \'%' . $keywords . '%\' OR c.content_quotes_contract LIKE \'%' . $keywords . '%\'');

        $_SESSION['quotes_constract_filter']['keywords'] = $keywords;
    }

    if (!empty($arrParams['col']) && !empty($arrParams['order'])) {
        $col = $this->_constract_fields[$arrParams['col']];
        $order = $arrParams['order'];

        $this->db->order_by($col, $order);

        $_SESSION['quotes_constract_filter']['col'] = $arrParams['col'];
        $_SESSION['quotes_constract_filter']['order'] = $arrParams['order'];
    }

    $page = (empty($arrParams['page'])) ? 1 : $arrParams['page'];
    if (!empty($arrayParram['paginator'])) {
        $paginator = $arrParams['paginator'];
        $this->db->limit($paginator['per_page'], ($page - 1) * $paginator['per_page']);
    }


    $query = $this->db->get();

    $result = $query->result_array();

    return $result;
}

function constract_type_list($arrParams)
{
    $paginator = $arrParams['paginator'];
    $this->db->select("ct.*")
    ->from('quotes_contract_type AS ct');

    $page = (empty($arrParams['page'])) ? 1 : $arrParams['page'];
    $this->db->limit($arrParams['per_page'], ($page - 1) * $arrParams['per_page']);

    if (!empty($arrParams['keywords'])) {
        $keywords = trim($arrParams['keywords']);
        $this->db->where('ct.title LIKE \'%' . $keywords . '%\'');
        $_SESSION['constract_type_filter']['keywords'] = $keywords;
    }

    if (!empty($arrParams['col']) && !empty($arrParams['order'])) {
        $col = $this->_constract_type_fields[$arrParams['col']];
        $order = $arrParams['order'];

        $this->db->order_by($col, $order);

        $_SESSION['constract_type_filter']['col'] = $arrParams['col'];
        $_SESSION['constract_type_filter']['order'] = $arrParams['order'];
    }

    $page = $arrParams['page'];
    $this->db->limit($paginator['per_page'], ($page - 1) * $paginator['per_page']);

    $query = $this->db->get();

    $result = $query->result_array();
    if (!empty($result)) {
        $constract_type_default = $this->config->item('quote_constract_type_default');
        $constract_type_default = (!empty($constract_type_default)) ? unserialize($this->config->item('quote_constract_type_default')) : array();

        foreach ($result as &$value) {
            if ($value['status'] == 0)
                $value['status'] = 'Không sử dụng';
            elseif ($value['status'] == 1)
                $value['status'] = 'Luôn sử dụng';

            if (in_array($value['code'], $constract_type_default))
                $value['no-delete'] = 1;
            else
                $value['no-delete'] = 0;
        }
    }

    $this->db->flush_cache();

    return $result;
}

function listItem($arrParams)
{
    $paginator = $arrParams['paginator'];
    $this->db->select("m.mail_id, m.mail_title")
    ->from('mail_template AS m')
    ->where('m.deleted = 0');

    $page = (empty($arrParams['page'])) ? 1 : $arrParams['page'];
    $this->db->limit($arrParams['per_page'], ($page - 1) * $arrParams['per_page']);

    if (!empty($arrParams['keywords'])) {
        $keywords = trim($arrParams['keywords']);
        $this->db->where('m.mail_title LIKE \'%' . $keywords . '%\' OR mail_content LIKE \'%' . $keywords . '%\'');

        $_SESSION['mail_template_filter']['keywords'] = $keywords;
    }

    if (!empty($arrParams['col']) && !empty($arrParams['order'])) {
        $col = $this->_mail_template_fields[$arrParams['col']];
        $order = $arrParams['order'];

        $this->db->order_by($col, $order);

        $_SESSION['mail_template_filter']['col'] = $arrParams['col'];
        $_SESSION['mail_template_filter']['order'] = $arrParams['order'];
    }

    $page = $arrParams['page'];
    $this->db->limit($paginator['per_page'], ($page - 1) * $paginator['per_page']);

    $query = $this->db->get();

    $result = $query->result_array();
    $this->db->flush_cache();

    return $result;
}

    // Get all mail template
function get_all_mail_template($arrParams)
{
    $this->db->select("*")
    ->from('mail_template AS m');
    $page = (empty($arrParams['page'])) ? 1 : $arrParams['page'];
    $per_page = (empty($arrParams['per_page'])) ? 10000 : $arrParams['per_page'];
    $this->db->limit($per_page, ($page - 1) * $per_page);
    if (!empty($arrParams['search_text'])) {
        $keywords = trim($arrParams['search_text']);
        $this->db->where('m.mail_title LIKE \'%' . $keywords . '%\' OR mail_content LIKE \'%' . $keywords . '%\'');
    }
    if (!empty($arrParams['order_dir']) && !empty($arrParams['order_col'])) {
        $col = $arrParams['order_col'];
        $order = $arrParams['order_dir'];
        $this->db->order_by($col, $order);
    }
    $query = $this->db->get();
    $result = $query->result_array();
    $this->db->flush_cache();

    return $result;
}

function get_all_mail($limit = 10000, $offset = 0, $col = 'mail_id', $order = 'asc')
{
    $this->db->from('mail_template');
    $this->db->where('deleted', 0);
    $this->db->order_by($col, $order);
    $this->db->limit($limit);
    $this->db->offset($offset);
    return $this->db->get();
}

function get_info_mail($mail_id)
{
    $this->db->from('mail_template');
    $this->db->where('mail_id', $mail_id);
    $query = $this->db->get();
    if ($query->num_rows() == 1) {
        return $query->row();
    } else {
            //Get empty base parent object, as $customer_id is NOT an customer
        $mail_obj = parent::get_info(-1);

            //Get all the fields from customer table
        $fields = $this->db->list_fields('mail_template');

            //append those fields to base parent object, we we have a complete empty object
        foreach ($fields as $field) {
            $mail_obj->$field = '';
        }

        return $mail_obj;
    }
}

function getInfo_mail($mail_id)
{
    $this->db->from('mail_template');
    $this->db->where('mail_id', $mail_id);
    $query = $this->db->get();

    $result = $query->row_array();
    return $result;
}

function save_mail(&$mail_data, $mail_id = false)
{
    if (!$mail_id or !$this->exists_mail($mail_id)) {
        if ($this->db->insert('mail_template', $mail_data)) {
            $mail_data['$mail_id'] = $this->db->insert_id();

            return true;
        }
        return false;
    }

    $this->db->where('mail_id', $mail_id);
    return $this->db->update('mail_template', $mail_data);
}

function exists_mail($mail_id)
{
    $this->db->from('mail_template');
    $this->db->where('mail_template.mail_id', $mail_id);
    $query = $this->db->get();
    return ($query->num_rows() == 1);
}

function delete_mail_template($arrParams)
{
    if (!empty($arrParams['mail_id'])) {
        $this->db->where('mail_id IN (' . implode(',', $arrParams['mail_id']) . ')');
        $this->db->delete('mail_template');
    }
}

function add_mail_history($data)
{
    $this->db->insert("mail_history", $data);
}

function checkFileExist($file_name, $employee_id)
{
    $this->db->select('COUNT(*) AS totalItem')
    ->from('mail_history')
    ->where('employee_id', $employee_id)
    ->where('file LIKE \'' . $file_name . '\'');

    $query = $this->db->get();

    $result = $query->row()->totalItem;

    $this->db->flush_cache();

    if ($result == 0)
        return true;
    else
        return false;
}

function add_sms_history($data)
{
    $this->db->insert("sms_history", $data);
}


    //search type
function search_quotes_contract_type($search, $cat = '', $limit = 20, $offset = 0, $column = 'id', $orderby = 'desc')
{
    $this->db->from('quotes_contract_type');
    if ($cat) {
        $this->db->where("id", $cat);
    }
    $this->db->where("(title LIKE '%" . $seach . "%' OR id LIKE '%" . $search . "')");
    $this->db->order_by($column, $orderby);
    $this->db->limit($limit);
    $this->db->offset($offset);
    return $this->db->get();
}

    //
function get_serach_suggestions_quotes_contract_type($search, $limit = 25)
{
    $suggestions = array();
    $this->db->from('quotes_contract_type');
    $this->db->like('title', $search);
    $this->db->order_by('title', 'asc');
    $by_name = $this->db->get();
    foreach ($by_name->result() as $row) {
        $suggestions[] = array('label' => $row->title);
    }

    $this->db->from('quotes_contract_type');
    $this->db->like('id', $search);
    $this->db->order_by('id', 'asc');
    $by_id = $this->db->get();
    foreach ($by_id->result() as $row) {
        $suggestions[] = array('label' => $row->id);
    }

        //only return $limit suggestions
    if (count($suggestions > $limit)) {
        $suggestions = array_slice($suggestions, 0, $limit);
    }
    return $suggestions;
}

function get_area_list($id)
{
    $array = array('cat_quotes_contract' => $id);
    $this->db->select('id_quotes_contract, title_quotes_contract');
    $this->db->where($array);
    $this->db->from('quotes_contract');
    $query = $this->db->get();
    $result = $query->result();
    return $result;
}

function item_select_quote_contract($quote_contract_code = null)
{
    $this->db->select('c.id_quotes_contract, c.title_quotes_contract')
    ->from('quotes_contract AS c')
    ->join('quotes_contract_type AS t', 'c.cat_quotes_contract = t.id', 'left')
    ->order_by('c.id_quotes_contract', 'ASC');

    if (!empty($quote_contract_code)) {
        $this->db->where('t.code', $quote_contract_code);
    }

    $query = $this->db->get();

    $resultTmp = $query->result_array();

    $result[-1] = 'Chọn tệp đính kèm';
    if (!empty($resultTmp)) {
        foreach ($resultTmp as $val)
            $result[$val['id_quotes_contract']] = $val['title_quotes_contract'];
    }

    $this->db->flush_cache();

    return $result;
}

function item_select_customer_type($arrParams = null)
{
    $this->db->select('id, name')
    ->from('customers_type');

    $query = $this->db->get();

    $result_tmp = $query->result_array();
    $this->db->flush_cache();

    $result[-1] = 'Chọn loại khách hàng';
    if (!empty($result_tmp)) {
        foreach ($result_tmp as $val)
            $result[$val['id']] = $val['name'];
    }

    return $result;
}

function findBirthDate()
{
    $this->db->from('people');
    $this->db->join('customers', 'customers.person_id = people.person_id');
    $this->db->where('month(birth_date)', date('m'));
    $this->db->where('day(birth_date) >=', date('d'));
    $this->db->where('deleted', 0);
    $this->db->order_by('people.birth_date desc');
    $query = $this->db->get();
    return $query->result_array();
}

function findPerson($id)
{
    $this->db->where('person_id', $id);
    $query = $this->db->get('people');
    return $query->result_array();
}

function get_customer_mail_auto($people_id)
{
    $this->db->where("people_id", $people_id);
    $this->db->where("year", date('Y'));
    $query = $this->db->get("mail_auto");
    return $query->row_array();
}

function getCountAllBirth()
{
    $this->db->from('people');
    $this->db->join('customers', 'customers.person_id = people.person_id');
    $this->db->where('month(birth_date)', date('m'));
    $this->db->where('day(birth_date) >=', date('d'));
    $this->db->where('deleted', 0);
    return $this->db->count_all_results();
}

function getCountSendMail($person_id = 0)
{
    $this->db->from('mail_history');
    $this->db->where('month(time)', date('m'));
    $this->db->where('day(time) >=', date('d'));
    $this->db->where('person_id', $person_id);
    $this->db->where('status', 1);
    return $this->db->count_all_results();
}

function getCountSendSms($person_id = 0)
{
    $this->db->from('sms_history');
    $this->db->where('month(time)', date('m'));
    $this->db->where('day(time) >=', date('d'));
    $this->db->where('person_id', $person_id);
    $this->db->where('status', 1);
    return $this->db->count_all_results();
}

function find_suspends_date()
{
    $this->db->where('suspended = ', 1);
    $this->db->where('deleted = ', 0);
    $query = $this->db->get('sales');
    if ($query->num_rows() > 0) {
        return $query->result_array();
    } else
    return null;
}

    /*
	Returns all the customers
	*/
    function get_all_suspends($limit = 10000, $offset = 0, $col = 'last_name', $order = 'asc', $extra = array())
    {
        $order_by = '';

        if (!$this->config->item('speed_up_search_queries')) {
            $order_by = " ORDER BY " . $col . " " . $order;
        }

        $people = $this->db->dbprefix('people');
        $customers = $this->db->dbprefix('customers');
        //$employees_locations=$this->db->dbprefix('employees_locations');

        // $query = "SELECT *  FROM ".$people." STRAIGHT_JOIN ".$customers." ON ".$people.".person_id = ".$customers.".person_id
        // 			WHERE deleted = 0 AND ". $customers .".created_by = ". $this->Employee->get_logged_in_employee_info()->person_id ." AND balance <> 0
        // 				$order_by LIMIT  ".$offset.",".$limit;

        $query = "SELECT * 
        FROM " . $people . "
        STRAIGHT_JOIN " . $customers . " ON 										                       
        " . $people . ".person_id = " . $customers . ".person_id
        WHERE deleted =0 AND balance <> 0 $order_by  
        LIMIT  " . $offset . "," . $limit;

        $data = $this->db->query($query);
        return $data;
    }

    function getCountAllSuspends()
    {
        $people = $this->db->dbprefix('people');
        $customers = $this->db->dbprefix('customers');
        $employees_locations = $this->db->dbprefix('employees_locations');

        // $query = "SELECT count(*)  FROM ".$people." STRAIGHT_JOIN ".$customers." ON ".$people.".person_id = ".$customers.".person_id
        // 			WHERE deleted = 0 AND ". $customers .".created_by = ". $this->Employee->get_logged_in_employee_info()->person_id ." AND balance <> 0";
        $query = "SELECT count(*) 
        FROM " . $people . "
        STRAIGHT_JOIN " . $customers . " ON 										                       
        " . $people . ".person_id = " . $customers . ".person_id
        WHERE deleted =0 AND balance <> 0";

        $data = $this->db->query($query)->row_array();
        return $data;
    }


    function getAllMailHistory($search = 0, $limit = 20, $offset = 0, $column = 'id', $orderby = 'desc')
    {
        $this->db->select('*')
        ->from('mail_history');
        //$this->db->where("(title LIKE '%" . $search . "%' OR id LIKE '%" . $search . "')");
        $this->db->where('person_id=' . $search);
        $this->db->order_by($column, $orderby);
        $this->db->limit($limit);
        $this->db->offset($offset);
        //return $this->db->get()->row_array();

        return $this->db->get()->result_array();
    }

    function getAllSmsHistory($search = 0, $limit = 20, $offset = 0, $column = 'id', $orderby = 'desc')
    {
        $this->db->select('*')
        ->from('sms_history');
        //$this->db->where("(title LIKE '%" . $search . "%' OR id LIKE '%" . $search . "')");
        $this->db->where('person_id=' . $search);
        $this->db->order_by($column, $orderby);
        $this->db->limit($limit);
        $this->db->offset($offset);
        //return $this->db->get()->row_array();

        return $this->db->get()->result_array();
    }

    function quotes_contract_by_code($code)
    {
        $this->db->select('c.id_quotes_contract, c.title_quotes_contract')
        ->from('quotes_contract AS c')
        ->join('quotes_contract_type AS ct', 'ct.id = c.cat_quotes_contract')
        ->where('ct.code LIKE \'' . $code . '\'')
        ->order_by('c.created', 'DESC');

        $query = $this->db->get();
        $result = $query->result_array();

        $this->db->flush_cache();

        return $result;
    }


    function get_information($customer_id)
    {
        $this->db->select("p.first_name,p.code, p.last_name, p.phone_number, p.email, p.address_1, p.address_2, c.company_name, c.id, c.person_id, c.position, c.account_number")
        ->from('customers AS c')
        ->join('people AS p', 'c.person_id = p.person_id', 'left')
        ->where('c.person_id', $customer_id);

        $query = $this->db->get();
        $result = $query->row_array();

        return $result;
    }

    function get_info_by_ids($customer_ids)
    {
        $this->db->select("p.first_name, p.last_name, p.phone_number, p.email, p.address_1, p.address_2, c.company_name, c.id, c.person_id")
        ->from('customers AS c')
        ->join('people AS p', 'c.person_id = p.person_id', 'left')
        ->where('c.person_id IN (' . implode(',', $customer_ids) . ')');

        $query = $this->db->get();

        $result_tmp = $query->result_array();

        $result = array();
        if (!empty($result_tmp)) {
            foreach ($result_tmp as $val)
                $result[$val['person_id']] = $val;
        }

        return $result;
    }

    function get_customer_type_item($arrParams = null)
    {
        $this->db->select("*")
        ->from('customer_type')
        ->where('customer_type_id', $arrParams['id']);

        $query = $this->db->get();

        $result = $query->row_array();

        $this->db->flush_cache();

        return $result;
    }

    function item_mail_template($arrParams = null, $options = null)
    {
        $this->db->select("mail_id, mail_title")
        ->from('mail_template')
        ->order_by('mail_id', 'ASC');

        $query = $this->db->get();

        $result_tmp = $query->result_array();

        $this->db->flush_cache();

        $result = array('-1' => 'Chọn mẫu mail');
        if (!empty($result_tmp)) {
            foreach ($result_tmp as $val)
                $result[$val['mail_id']] = $val['mail_title'];
        }

        return $result;
    }

    function get_mail_template($template_id, $options = null)
    {
        $this->db->select("*")
        ->from('mail_template')
        ->where('mail_id', $template_id);
        $query = $this->db->get();

        $result = $query->row_array();

        $this->db->flush_cache();

        return $result;
    }

    function save_mail_history($arrPrams = null, $options = null)
    {
        if ($options['task'] == 'update-multi') {
            $this->db->insert_batch('mail_history', $arrPrams);
            $this->db->flush_cache();
        }
    }

    function save_customer_type($arrParams = null, $options = null)
    {
        if ($options['task'] == 'update') {
            $data['name'] = $arrParams['name'];
            $data['code'] = $arrParams['code'];
            $data['desc'] = $arrParams['desc'];

            if ($arrParams['id'] == -1) {
                $this->db->insert('customer_type', $data);
                $this->db->flush_cache();

            } else {
                $this->db->where("customer_type_id", $arrParams['id']);
                $this->db->update('customer_type', $data);

                $this->db->flush_cache();
            }
        }
    }

    function nhom_khach_hang($arrParams = null, $options = null)
    {
        if ($options['task'] == null) {
            $this->db->select("name,id")
            ->from('customers_type')
            ->order_by('id', 'ASC');

            $query = $this->db->get();

            $result_tmp = $query->result_array();
            $this->db->flush_cache();
            $result[-1] = '';
            if (!empty($result_tmp)) {
                foreach ($result_tmp as $val)
                    $result[$val['id']] = $val['name'];
            }
        }

        return $result;
    }

    function item_select_box($arrParams = null, $options = null)
    {
        if ($options['task'] == null) {
            $this->db->select("p.first_name, p.last_name, c.person_id")
            ->from('customers AS c')
            ->join('people AS p', 'c.person_id = p.person_id', 'left')
            ->where('c.deleted', 0)
            ->order_by('c.id', 'ASC');

            $query = $this->db->get();

            $result_tmp = $query->result_array();
            $this->db->flush_cache();
            $result[-1] = '';
            if (!empty($result_tmp)) {
                foreach ($result_tmp as $val)
                    $result[$val['person_id']] = $val['first_name'] . ' ' . $val['last_name'];
            }
        }

        return $result;
    }

    function sum_balance($options)
    {
        $this->db->select("SUM(c.$options) AS balance_total")
        ->from('customers AS c')
        ->where('c.deleted', 0);

        $query = $this->db->get();

        $result_tmp = $query->row_array();

        $this->db->flush_cache();

        $result = (!empty($result_tmp['balance_total'])) ? $result_tmp['balance_total'] : 0;

        return $result;
    }

    function check_customer_type_field_exist($field, $value, $id)
    {
        $this->db->select("customer_type_id")
        ->from('customer_type')
        ->where("$field = '$value'")
        ->where('customer_type_id != ' . $id);

        $query = $this->db->get();

        $result = $query->row_array();

        $this->db->flush_cache();

        if (!empty($result))
            return true;
        else
            return false;
    }
    /*
	 * BIZ CUSTOMER SMS EMAIL GROUP
	 *
	 *
	 */


    /*
	* Get all customer group
	* @return datatable
	*/
    function get_all_customer_groups($searchCondition, $limit, $offset)
    {
        $limit = (empty($limit)) ? 10000 : $limit;
        $offset = (empty($limit)) ? 0 : $offset;
        $this->db->where('smsmail_group_id >0');
        $this->db->limit($limit, $offset);
        $customers_group = $this->db->dbprefix('customers_smsemail_group');
        if (!empty($searchCondition['name'])) {
            $this->db->like('name', $searchCondition['name']);
        }
        if (isset($searchCondition['id']) && $searchCondition['id'] != '') {
            $this->db->where('smsmail_group_id', $searchCondition['id']);
            $query = $this->db->get($customers_group);
            return $query->row();
        }
        if (!empty($searchCondition['order_col']) && !empty($searchCondition['order_dir'])) {
            $this->db->order_by($searchCondition['order_col'], $searchCondition['order_dir']);
        }
        $query = $this->db->get($customers_group);
        return $query->result();
    }

    /*
	* Count all customer group
	* @return int
	*/

    function count_all_customer_groups($searchCondition = array())
    {
        $this->db->where('smsmail_group_id >0');
        if ($searchCondition['name'] != '') {
            $this->db->like('name', $searchCondition['name']);
        }
        $this->db->from('customers_smsemail_group');
        return $this->db->count_all_results();
    }




    // --------------------------------------------------------------------
    //
    //   SMS EMAIL GROUP
    //
    // --------------------------------------------------------------------


    /**
     * Update SMS Email Group
     *
     * Insert or update group
     *
     * @param    array()    data
     * @param int customer_group_id
     * @return    bool
     */
    function _update_smsEmailGroup($data, $customer_group_id)
    {


        if ($customer_group_id == false) {
            if ($this->db->insert('customers_smsemail_group', $data)) {
                return $this->db->insert_id();
            } else {
                return false;
            }
        } else {
            $this->db->set($data);
            $this->db->where('smsmail_group_id', $customer_group_id);
            $this->db->update('customers_smsemail_group', $data);
        }

    }
    // --------------------------------------------------------------------

    /**
     * Delete SMS Email Group
     *
     * Delete group
     *
     * @param    array()    id
     * @return    void
     */
    function _delete_smsEmailGroup($id)
    {

        $id = implode(',', $id);
        $this->db->where("smsmail_group_id IN ($id)");
        $this->db->delete('customers_smsemail_group');
        $this->db->flush_cache();
    }
    // --------------------------------------------------------------------

    /*
	 * BIZ CUSTOMER SMS EMAIL AND GROUP
	 *
	 *
	 */


    /**
     * Check Exist Customer In SMS Email Group
     *
     * @param    int person_id
     * @param    int smsemail_group_id
     * @return    bool
     */
    function check_exist_customer_smsEmail($person_id, $smsemail_group_id)
    {
        $this->db->where('person_id', $person_id);
        $this->db->where('smsmail_group_id', $smsemail_group_id);
        $this->db->where('deleted', 0);
        $query = $this->db->get('customers_smsemail');
        if ($query->num_rows() == 0) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
    // --------------------------------------------------------------------


    /**
     * Count Customer In SMS Email Group
     *
     * Count Customer In SMS Email Group with condition, without condition
     * @param    array searchCondition
     * @param    int the sms group id
     * @return    int
     */
    function count_customer_in_smsEmail_group($searchCondition = array('name' => ''), $smsmail_group_id)
    {


        $this->db->from('customers');
        $this->db->join('people', 'customers.person_id=people.person_id');
        $this->db->join('customers_smsemail', 'people.person_id=customers_smsemail.person_id');
        $this->db->join('customers_smsemail_group', 'customers_smsemail.smsmail_group_id=customers_smsemail_group.smsmail_group_id');
        $this->db->where('customers.deleted', 0);
        $this->db->where('customers_smsemail_group.smsmail_group_id', $smsmail_group_id);
        if ($searchCondition['name'] != '') {
            $this->db->like('name', $searchCondition['name']);
        }

        return $this->db->count_all_results();

    }
    // --------------------------------------------------------------------

    /**
     * Get Customer In SMS Email Group
     *
     * Count Customer In SMS Email Group with condition, without condition
     * @param    array searchCondition
     * @param    int the sms group id
     * @param int limit
     * @param int offset
     * @return    mixed
     */

    function get_customer_in_smsEmail_group($searchCondition, $limit, $offset, $smsmail_group_id)
    {

        $this->db->limit($limit, $offset);
        $this->db->from('customers');
        $this->db->join('people', 'customers.person_id=people.person_id');
        $this->db->join('customers_smsemail', 'customers.person_id=customers_smsemail.person_id');
        $this->db->join('customers_smsemail_group', 'customers_smsemail.smsmail_group_id=customers_smsemail_group.smsmail_group_id');
        $this->db->where('customers.deleted', 0);
        $this->db->where('customers_smsemail_group.smsmail_group_id', $smsmail_group_id);
        if (!empty($searchCondition['name'])) {
            $this->db->like('people.first_name', $searchCondition['name']);
            // $this->db->or_like(array('people.first_name'=>$searchCondition['name']));
        }
        if (!empty($searchCondition['order_dir']) && !empty($searchCondition['order_col'])) {
            $col = $searchCondition['order_col'];
            $order = $searchCondition['order_dir'];
            $this->db->order_by($col, $order);
        }
        $query = $this->db->get();
        return $query->result_array();

    }


    /**
     * CRUD customer SMS MAIL
     */


    // --------------------------------------------------------------------

    /**
     * Get Customer In SMS Email Group
     *
     * @param    array data
     * @return    bool
     */

    function _insert_smsEmail($data)
    {
        if ($this->db->insert('customers_smsemail', $data)) {
            return false;
        }

    }
    // --------------------------------------------------------------------

    /**
     * Delete Customer In SMS Email Group
     *
     * @param    int person_id
     * @param int smsmail_group_id
     * @return    void
     */
    function _delete_smsEmail($arrParams)
    {
        $person_id = is_array($arrParams['person_id']) ? implode(',', $arrParams['person_id']) : $arrParams['person_id'];
        $smsmail_group_id = is_array($arrParams['smsmail_group_id']) ? implode(',', $arrParams['smsmail_group_id']) : $arrParams['smsmail_group_id'];
        if (!empty($arrParams['person_id'])) {
            $this->db->where('person_id IN (' . $person_id . ')');
        }
        if (!empty($arrParams['smsmail_group_id'])) {
            $this->db->where('smsmail_group_id IN (' . $smsmail_group_id . ')');
        }
        $this->db->delete('customers_smsemail');
        $this->db->flush_cache();

    }
    // --------------------------------------------------------------------


    //--------------------------------------------------------------------------------/
    // MAIL CAMPAIN
    //
    //
    //-------------------------------------------------------------------------------------/


    function count_all_mail_campain($arrParams)
    {
        $this->db->select('COUNT(mc.mail_campain_id) AS totalItem')
        ->from('mail_campain AS mc')
        ->join('mail_template AS mt', 'mt.mail_id = mc.mail_id')
        ->join('customers_smsemail_group AS cseg', 'cseg.smsmail_group_id = mc.smsmail_group_id');

        if (!empty($arrParams['search_text'])) {
            $keywords = trim($arrParams['search_text']);
            $this->db->where('mc.mail_campain_name LIKE \'%' . $keywords . '%\'');
        }

        $query = $this->db->get();

        $result = $query->row()->totalItem;

        $this->db->flush_cache();

        return $result;
    }
    //----------------------------------------------

    /*
	* Get all mail campain
	* Get all mail campain, when searching, sorting
	* @params array arrParams: condition search
	*/
    function get_all_mail_campain($arrParams)
    {
        $this->db->select("*")
        ->from('mail_campain AS mc')
        ->join('mail_template AS mt', 'mt.mail_id = mc.mail_id')
        ->join('customers_smsemail_group AS cseg', 'cseg.smsmail_group_id = mc.smsmail_group_id');
        $page = (empty($arrParams['page'])) ? 1 : $arrParams['page'];
        $per_page = (empty($arrParams['per_page'])) ? 10000 : $arrParams['per_page'];
        $this->db->limit($per_page, ($page - 1) * $per_page);

        if (!empty($arrParams['search_text'])) {
            $keywords = trim($arrParams['search_text']);
            $this->db->where('mc.mail_campain_name LIKE \'%' . $keywords . '%\'');
        }
        if (!empty($arrParams['id'])) {
            $id = $arrParams['id'];
            $this->db->where('mc.mail_campain_id =' . $id);
        }
        if (!empty($arrParams['mail_id'])) {
            $id = $arrParams['mail_id'];
            $this->db->where('mc.mail_id =' . $id);
        }
        if (!empty($arrParams['name'])) {
            $name = $arrParams['name'];
            $this->db->where("mc.mail_campain_name ='" . $name . "'");
        }

        if (!empty($arrParams['order_dir']) && !empty($arrParams['order_col'])) {
            $col = $arrParams['order_col'];
            $order = $arrParams['order_dir'];
            $this->db->order_by($col, $order);
        }
        $query = $this->db->get();
        $result = $query->result_array();
        $this->db->flush_cache();

        return $result;
    }


    /*
	* Insert/Update sms campain
	* @params array data: add data to insert or update
	* @params int $sms_campain_id:  campain id
	*/
    function update_mail_campain($data, $mail_campain_id)
    {
        if ($mail_campain_id < 0) {
            $this->db->insert('mail_campain', $data);
            return $this->db->insert_id();
        } else {
            $this->db->set($data);
            $this->db->where('mail_campain_id', $mail_campain_id);
            $this->db->update('mail_campain', $data);
            return true;
        }

    }

    function delete_mail_campain($arrParams)
    {
        if (!empty($arrParams['mail_campain_id'])) {
            $this->db->where('mail_campain_id IN (' . implode(',', $arrParams['mail_campain_id']) . ')');
        }
        if (!empty($arrParams['mail_id'])) {
            $this->db->where('mail_id IN (' . implode(',', $arrParams['mail_id']) . ')');
        }
        if (!empty($arrParams['smsmail_group_id'])) {
            $this->db->where('smsmail_group_id IN (' . implode(',', $arrParams['smsmail_group_id']) . ')');
        }
        if (!empty($arrParams)) {
            $this->db->delete('mail_campain');
        }

    }
    // SMS CAMPAIN
    //
    //
    //-------------------------------------------------------------------------------------/


    /*
	* Get all sms template
	* Get all sms template, when searching, sorting
	* @params array arrParams: condition search
	*/
    function get_all_sms_template($arrParams)
    {
        $this->db->select("*");
        $this->db->from('sms AS s');
        $page = (empty($arrParams['page'])) ? 1 : $arrParams['page'];
        $per_page = (empty($arrParams['per_page'])) ? 10000 : $arrParams['per_page'];
        $this->db->limit($per_page, ($page - 1) * $per_page);

        if (!empty($arrParams['search_text'])) {
            $keywords = trim($arrParams['search_text']);
            $this->db->where('s.title LIKE \'%' . $keywords . '%\' OR s.message LIKE \'%' . $keywords . '%\'');
        }
        if (!empty($arrParams['order_dir']) && !empty($arrParams['order_col'])) {
            $col = $arrParams['order_col'];
            $order = $arrParams['order_dir'];
            $this->db->order_by($col, $order);
        }
        $query = $this->db->get();
        $result = $query->result_array();
        $this->db->flush_cache();

        return $result;
    }

    function count_all_sms($arrParams)
    {
        $this->db->select('COUNT(s.id) AS totalItem')
        ->from('sms AS s');


        if (!empty($arrParams['search_text'])) {
            $keywords = trim($arrParams['search_text']);
            $this->db->where('s.title LIKE \'%' . $keywords . '%\' OR s.message LIKE \'%' . $keywords . '%\'');
        }
        $query = $this->db->get();

        $result = $query->row()->totalItem;

        $this->db->flush_cache();

        return $result;
    }


    /*
	* Count sms campain
	* Count sms campain, when searching, sorting
	* @params array arrParams: condition search
	*/
    function count_all_sms_campain($arrParams)
    {
        $this->db->select('COUNT(sc.sms_campain_id) AS totalItem')
        ->from('sms_campain AS sc')
        ->join('sms AS s', 's.id = sc.sms_id')
        ->join('customers_smsemail_group AS cseg', 'cseg.smsmail_group_id = sc.smsmail_group_id')
        ->where('s.deleted', 0);

        if (!empty($arrParams['search_text'])) {
            $keywords = trim($arrParams['search_text']);
            $this->db->where('sc.sms_campain_name LIKE \'%' . $keywords . '%\'');
        }

        $query = $this->db->get();

        $result = $query->row()->totalItem;

        $this->db->flush_cache();

        return $result;
    }


    /*
	* Get all sms campain
	* Get all sms campain, when searching, sorting
	* @params array arrParams: condition search
	*/
    function get_all_sms_campain($arrParams)
    {
        $this->db->select("*")
        ->from('sms_campain AS sc')
        ->join('sms AS s', 's.id = sc.sms_id')
        ->join('customers_smsemail_group AS cseg', 'cseg.smsmail_group_id = sc.smsmail_group_id');
        $page = (empty($arrParams['page'])) ? 1 : $arrParams['page'];
        $per_page = (empty($arrParams['per_page'])) ? 10000 : $arrParams['per_page'];
        $this->db->limit($per_page, ($page - 1) * $per_page);

        if (!empty($arrParams['search_text'])) {
            $keywords = trim($arrParams['search_text']);
            $this->db->where('sc.sms_campain_name LIKE \'%' . $keywords . '%\'');
        }
        if (!empty($arrParams['id'])) {
            $id = $arrParams['id'];
            $this->db->where('sc.sms_campain_id =' . $id);
        }
        if (!empty($arrParams['name'])) {
            $name = $arrParams['name'];
            $this->db->where('sc.sms_campain_name', $name);
        }
        if (!empty($arrParams['sms_id'])) {
            $sms_id = $arrParams['sms_id'];
            $this->db->where('sc.sms_id', $sms_id);
        }
        if (!empty($arrParams['order_dir']) && !empty($arrParams['order_col'])) {
            $col = $arrParams['order_col'];
            $order = $arrParams['order_dir'];
            $this->db->order_by($col, $order);
        }
        $query = $this->db->get();
        $result = $query->result_array();
        $this->db->flush_cache();

        return $result;
    }
    //- - - - -------------------------------------------------------

    /*
	* Insert/Update sms campain
	* @params array data: add data to insert or update
	* @params int $sms_campain_id:  campain id
	*/
    function update_sms_campain($data, $sms_campain_id)
    {
        if ($sms_campain_id == -1) {
            $this->db->insert('sms_campain', $data);
            return $this->db->insert_id();
        } else {
            $this->db->set($data);
            $this->db->where('sms_campain_id', $sms_campain_id);
            $this->db->update('sms_campain', $data);;
        }

    }
    //-----------------------------------------------------------------


    /*
	* Delete sms campain
	* @params array arrParams: condition search
	*/
    function delete_sms_campain($arrParams)
    {
        if (!empty($arrParams['sms_campain_id'])) {
            $this->db->where('sms_campain_id IN (' . implode(',', $arrParams['sms_campain_id']) . ')');
        }
        if (!empty($arrParams['sms_id'])) {
            $this->db->where('sms_id IN (' . implode(',', $arrParams['sms_id']) . ')');
        }
        if (!empty($arrParams['smsmail_group_id'])) {
            $this->db->where('smsmail_group_id IN (' . implode(',', $arrParams['smsmail_group_id']) . ')');
        }
        if (!empty($arrParams)) {
            $this->db->delete('sms_campain');
        }

    }


    //------------------------------------------------------------------------------
    // MANAGE CUSTOMER MAIL HISTORY
    //
    //====================================================================================

    function get_mail_history($arrParams)
    {
        $this->db->select("mh.send_to_group as mh_send_to_group,mh.send_from_campain as mh_send_from_campaign,mc.mail_campain_name as mc_mail_campaign_name,csg.name as csg_name,csg.smsmail_group_id,mh.id AS mh_id,mh.content AS mh_content,mh.time AS mh_time,mh.title AS mh_title,mh.email AS mh_email,CONCAT(p.last_name,p.first_name) AS receive_person, p1.first_name AS send_person,CASE WHEN mh.status = 1 THEN 'Đã gửi' ELSE 'Chưa gửi' END AS status_")
        ->from('mail_history AS mh')
        ->join('people AS p', 'p.person_id = mh.person_id')
        ->join('people AS p1', 'p1.person_id = mh.employee_id')
        ->join('customers_smsemail_group AS csg', 'csg.smsmail_group_id = mh.send_to_group', 'left')
        ->join('mail_campain AS mc', 'mh.send_from_campain = mc.mail_campain_id', 'left');
        $page = (empty($arrParams['page'])) ? 1 : $arrParams['page'];
        $per_page = (empty($arrParams['per_page'])) ? 10000 : $arrParams['per_page'];
        $this->db->limit($per_page, ($page - 1) * $per_page);
        if (!is_array($arrParams)) {
            $id = $arrParams;
            $this->db->where('mh.id =' . $id);
        }
        if (!empty($arrParams['search_text'])) {
            $keywords = trim($arrParams['search_text']);
            $this->db->where('(mh.title LIKE \'%' . $keywords . '%\' OR p.first_name LIKE \'%' . $keywords . '%\' OR p.last_name LIKE \'%' . $keywords . '%\')');
        }
        if (!empty($arrParams['id'])) {
            $id = $arrParams['id'];
            $this->db->where('mh.id =' . $id);
        }
        if (!empty($arrParams['start_date']) && !empty($arrParams['end_date'])) {
            $start = date('Y-m-d H:i:s', strtotime($arrParams['start_date']));
            $end = date('Y-m-d H:i:s', strtotime($arrParams['end_date']));
            $this->db->where("mh.time BETWEEN '" . $start . "' AND '" . $end . "'");
        }
        if (!empty($arrParams['smsmail_group_id']) || (isset($arrParams['smsmail_group_id']) && $arrParams['smsmail_group_id'] == '0')) {
            $smsmail_group_id = $arrParams['smsmail_group_id'];
            $this->db->where('mh.send_to_group', $smsmail_group_id);
        }
        if (!empty($arrParams['mail_campain_id']) || (isset($arrParams['mail_campain_id']) && $arrParams['mail_campain_id'] == '0')) {
            $mail_campain_id = $arrParams['mail_campain_id'];
            $this->db->where('mh.send_from_campain', $mail_campain_id);
        }
        if (!empty($arrParams['order_dir']) && !empty($arrParams['order_col'])) {
            $col = $arrParams['order_col'];
            $order = $arrParams['order_dir'];
            $this->db->order_by($col, $order);
        }
        $query = $this->db->get();
        $result = $query->result_array();
        $this->db->flush_cache();
        return $result;

    }

    function count_mail_history($arrParams)
    {
        $this->db->select("COUNT(mh.id) AS totalItem")
        ->from('mail_history AS mh')
        ->join('people AS p', 'p.person_id = mh.person_id')
        ->join('people AS p1', 'p1.person_id = mh.employee_id')
        ->join('customers_smsemail_group AS csg', 'csg.smsmail_group_id = mh.send_to_group', 'left')
        ->join('mail_campain AS mc', 'mh.send_from_campain = mc.mail_campain_id', 'left');

        if (!empty($arrParams['search_text'])) {
            $keywords = trim($arrParams['search_text']);
            $this->db->where('(mh.title LIKE \'%' . $keywords . '%\' OR p.first_name LIKE \'%' . $keywords . '%\' OR p.last_name LIKE \'%' . $keywords . '%\')');
        }
        if (!empty($arrParams['id'])) {
            $id = $arrParams['id'];
            $this->db->where('mh.id =' . $id);
        }
        if (!empty($arrParams['start_date']) && !empty($arrParams['end_date'])) {

            $start = date('Y-m-d H:i:s', strtotime($arrParams['start_date']));
            $end = date('Y-m-d H:i:s', strtotime($arrParams['end_date']));
            $this->db->where("mh.time BETWEEN '" . $start . "' AND '" . $end . "'");
        }
        if (!empty($arrParams['smsmail_group_id']) || (isset($arrParams['smsmail_group_id']) && $arrParams['smsmail_group_id'] == '0')) {
            $smsmail_group_id = $arrParams['smsmail_group_id'];
            $this->db->where('mh.send_to_group', $smsmail_group_id);
        }
        if (!empty($arrParams['mail_campain_id']) || (isset($arrParams['mail_campain_id']) && $arrParams['mail_campain_id'] == '0')) {
            $mail_campain_id = $arrParams['mail_campain_id'];
            $this->db->where('mh.send_from_campain', $mail_campain_id);
        }
        $query = $this->db->get();
        $result = $query->row()->totalItem;
        $this->db->flush_cache();
        return $result;

    }

    function delete_mail_history($arrParams)
    {

        if (!empty($arrParams['mail_history_id'])) {
            $this->db->where('id IN (' . implode(',', $arrParams['mail_history_id']) . ')');
            return $this->db->delete('mail_history');
        }
    }

    //------------------------------------------------------------------------------
    // MANAGE CUSTOMER SMS HISTORY
    //
    //====================================================================================

    function get_sms_history($arrParams)
    {
        $this->db->select("*,CASE WHEN sh.status = 1 THEN 'Đã gửi' ELSE 'Chưa gửi' END AS status_")
        ->from('sms_history AS sh')
        ->join('people AS p', 'p.person_id = sh.person_id', 'left')
        ->join('customers_smsemail AS cs', 'cs.person_id = p.person_id', 'left')
        ->join('customers_smsemail_group AS csg', 'csg.smsmail_group_id = cs.smsmail_group_id', 'left')
        ->join('sms_campain AS sc', 'sc.smsmail_group_id = csg.smsmail_group_id', 'left');

        $page = (empty($arrParams['page'])) ? 1 : $arrParams['page'];
        $per_page = (empty($arrParams['per_page'])) ? 10000 : $arrParams['per_page'];
        $this->db->limit($per_page, ($page - 1) * $per_page);

        if (!empty($arrParams['search_text'])) {
            $keywords = trim($arrParams['search_text']);
            $this->db->where('sh.title LIKE \'%' . $keywords . '%\'');
            $this->db->where('(sh.title LIKE \'%' . $keywords . '%\' OR p.first_name LIKE \'%' . $keywords . '%\' OR p.last_name LIKE \'%' . $keywords . '%\')');
        }
        if (!empty($arrParams['id'])) {
            $id = $arrParams['id'];
            $this->db->where('sh.id =' . $id);
        }
        if (!empty($arrParams['start_date']) && !empty($arrParams['end_date'])) {
            $start = $arrParams['start_date'];
            $end = $arrParams['end_date'];

            $this->db->where("sh.time BETWEEN '" . $start . "' AND '" . $end . "'");
        }
        if (!empty($arrParams['smsmail_group_id']) || (isset($arrParams['smsmail_group_id']) && $arrParams['smsmail_group_id'] == '0')) {
            $smsmail_group_id = $arrParams['smsmail_group_id'];
            $this->db->where('csg.smsmail_group_id', $smsmail_group_id);
        }

        if (!empty($arrParams['sms_campain_id']) || (isset($arrParams['sms_campain_id']) && $arrParams['sms_campain_id'] == '0')) {
            $sms_campain_id = $arrParams['sms_campain_id'];
            $this->db->where('sc.sms_campain_id', $sms_campain_id);
        }
        if (!empty($arrParams['order_dir']) && !empty($arrParams['order_col'])) {
            $col = $arrParams['order_col'];
            $order = $arrParams['order_dir'];
            $this->db->order_by($col, $order);
        }
        $query = $this->db->get();
        $result = $query->result_array();
        $this->db->flush_cache();

        return $result;

    }

    function count_sms_history($arrParams)
    {
        $this->db->select("COUNT(sh.id) AS totalItem")
        ->from('sms_history AS sh')
        ->join('people AS p', 'p.person_id = sh.person_id', 'left')
        ->join('customers_smsemail AS cs', 'cs.person_id = p.person_id', 'left')
        ->join('customers_smsemail_group AS csg', 'csg.smsmail_group_id = cs.smsmail_group_id', 'left')
        ->join('sms_campain AS sc', 'sc.smsmail_group_id = csg.smsmail_group_id', 'left');


        if (!empty($arrParams['search_text'])) {
            $keywords = trim($arrParams['search_text']);
            $this->db->where('sh.title LIKE \'%' . $keywords . '%\'');
            $this->db->where('(sh.title LIKE \'%' . $keywords . '%\' OR p.first_name LIKE \'%' . $keywords . '%\' OR p.last_name LIKE \'%' . $keywords . '%\')');
        }
        if (!empty($arrParams['id'])) {
            $id = $arrParams['id'];
            $this->db->where('sh.id =' . $id);
        }
        if (!empty($arrParams['start_date']) && !empty($arrParams['end_date'])) {
            $start = $arrParams['start_date'];
            $end = $arrParams['end_date'];
            $this->db->where("sh.time BETWEEN '" . $start . "' AND '" . $end . "'");
        }
        if (!empty($arrParams['smsmail_group_id'])) {
            $smsmail_group_id = $arrParams['smsmail_group_id'];
            $this->db->where('csg.smsmail_group_id', $smsmail_group_id);
        }
        if (!empty($arrParams['sms_campain_id'])) {
            $sms_campain_id = $arrParams['sms_campain_id'];
            $this->db->where('sc.sms_campain_id', $sms_campain_id);

        }
        $query = $this->db->get();
        $result = $query->row()->totalItem;
        $this->db->flush_cache();
        return $result;

    }

    function manage_sms_history_delete($arrParams)
    {
        if (!empty($arrParams['id'])) {
            $this->db->where();
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

    public function getDashboardData($rangeDate, $charts = [])
    {
        $dashboardData = [];

        // get top 10 contract value
        $items = $allItems = $this->Sale->getAllDataSale();
        usort($items, function ($a, $b) {
            return $b['total_all_raw'] - $a['total_all_raw'];
        });
        $items = $this->unique_multidim_array($items, 'customer_id');
        $dashboardData['top10ContractValue'] = array_slice($items, 0, 10);

        //get top 10 total income value
        $topIncomes = $this->groupContractValueByCustomerId($allItems);
        usort($topIncomes, function ($a, $b) {
            return $b['total_all_raw'] - $a['total_all_raw'];
        });
//        echo json_encode($topIncomes);die();
        $dashboardData['top10Income'] = array_slice($topIncomes, 0, 10);
//        echo json_encode($topIncomes);die();

        if (empty($charts) || in_array('top10balance', $charts)) {
            $this->db->from('customers');
            $this->db->join('people', 'customers.person_id=people.person_id');
            $this->db->where('customers.deleted = 0');
            $this->db->order_by('customers.balance', 'DESC');
            $this->db->limit(10);
            $result = $this->db->get()->result_array();
            $dashboardData['top10balance'] = $result;
        }

        if (empty($charts) || in_array('flop10balance', $charts)) {
            $this->db->from('customers');
            $this->db->join('people', 'customers.person_id=people.person_id');
            $this->db->where('customers.deleted = 0');
            $this->db->order_by('customers.balance', 'ASC');
            $this->db->limit(10);
            $result = $this->db->get()->result_array();
            $dashboardData['flop10balance'] = $result;
        }

        if (empty($charts) || in_array('soluong', $charts)) {
            $query = "SELECT count(*) as total FROM phppos_customers " .
            "WHERE DATE(created_time) < (CURDATE() - INTERVAL " . $rangeDate . " DAY)";
            $queryTOTAL = $this->db->query($query);
            $total = !empty($queryTOTAL) ? $queryTOTAL->result_array() : [];
            $dashboardData['total'] = !empty($total[0]['total']) ? $total[0]['total'] : 0;

            $query = "SELECT count(*) AS total, DATE(created_time) AS created_date FROM phppos_customers " .
            "WHERE DATE(created_time) BETWEEN (CURDATE() - INTERVAL 30 DAY) " .
            "AND CURDATE() " .
            "GROUP BY created_date ORDER BY created_date";
            $querySL = $this->db->query($query);
            $dashboardData['soluong'] = !empty($querySL) ? $querySL->result_array() : [];
        }

        if (empty($charts) || in_array('type', $charts)) {
            $query = "SELECT count(*) AS total, phppos_customers_type.id, phppos_customers_type.name FROM phppos_customers " .
            "LEFT JOIN phppos_customers_type ON phppos_customers_type.id = phppos_customers.type_customer " .
            "GROUP BY phppos_customers_type.id";
            $queryTYPE = $this->db->query($query);
            $dashboardData['type'] = !empty($queryTYPE) ? $queryTYPE->result_array() : [];
        }

        if (empty($charts) || in_array('reference', $charts)) {
            $query = "SELECT count(*) AS total, phppos_customer_reference.id, phppos_customer_reference.name FROM phppos_customers " .
            "LEFT JOIN phppos_customer_reference ON phppos_customer_reference.id = phppos_customers.reference_by " .
            "GROUP BY phppos_customer_reference.id";
            $queryREF = $this->db->query($query);
            $dashboardData['reference'] = !empty($queryREF) ? $queryREF->result_array() : [];
        }

        return $dashboardData;
    }

    public function countTier($max_id_tier)
    {
        $count_tier = [];
        for ($i = 0; $i < $max_id_tier; $i++) {
            $count_tier[$i] = $this->db->select("count(tier_id) as tier_id")->from("phppos_customers")->where("tier_id =  " . ($i + 1))->get()->result();
        }

        return $count_tier;
    }

    public function getTopContractValue()
    {
        $totalPriceContract = $this->db->select('c.id as contract_id, c.code, c.sale_id, c.name,SUM(p.price) as price, SUM(p.payment_price)AS payment_price')
        ->from('contract AS c')
        ->join('contract_payment AS p', 'c.id = p.contract_id')
        ->group_by('c.id')
        ->get()->result();
        usort($totalPriceContract, function ($a, $b) {
            return $b->price - $a->price;
        });
        $totalPriceContract = array_slice($totalPriceContract, 0, 10);
        $data = [];
        foreach ($totalPriceContract as $value) {
            $data[$value->contract_id] = $this->db->select('pe.person_id, pe.first_name,pe.last_name')
            ->from('people AS pe')
            ->join('sales AS s', 's.customer_id = pe.person_id')
            ->where('s.sale_id = ' . $value->sale_id)
            ->get()->result();
        }

        $total = [];
        $index = 0;
        foreach ($totalPriceContract as $value){
            $temp = (array) $data[$value->contract_id][0];
            $value = (array) $value;
            $total[$index] = array_merge($value, $temp);
            $index++;
        }
        return $total;
    }

    public function getTopInCome()
    {
        $totalPriceContract = $this->db->select('c.id as contract_id, c.code, c.sale_id, c.name,SUM(p.price) as price, SUM(p.payment_price)AS payment_price')
        ->from('contract AS c')
        ->join('contract_payment AS p', 'c.id = p.contract_id')
        ->group_by('c.id')
        ->get()->result();
        usort($totalPriceContract, function ($a, $b) {
            return $b->price - $a->price;
        });
        $totalPriceContract = array_slice($totalPriceContract, 0, 10);

        $totalPriceContractConvert = [];
        foreach ($totalPriceContract as $value){
            $totalPriceContractConvert[$value->sale_id] = $value;
        }

        $customer = $this->db->select('pe.person_id AS customer_id')
        ->from('people AS pe')
        ->get()->result();


        $index = 0;
        $sale = [];
        foreach ($customer as $value){
            $sale[$index++] = $this->db->select('s.customer_id, s.sale_id')
            ->from('sales AS s')
            ->where('s.customer_id = '.$value->customer_id)
            ->get()->result();
        }

        $data = [];
        $index2 = 0;
        foreach ($sale as $value1){
            $sumPrice = 0;
            $customer_id = '';
            $countContract = 0;
            foreach ($value1 as $value2 ){
                $sumPrice += $totalPriceContractConvert[$value2->sale_id]->price;
                $customer_id = $value2->customer_id;
                $countContract++;
            }
            $data[$index2++] = [
                'customer_id' => $customer_id,
                'price_total' => $sumPrice,
                'num_of_contract' => $countContract
            ];
        }

        usort($data, function ($a, $b) {
            return $b['price_total'] - $a['price_total'];
        });
        $data = array_slice($data, 0, 10);
        $dataConvert = [];$index3=0;
        foreach ($data as $key){
            $person_info = $this->db->select('p.first_name, p.last_name, p.person_id')
            ->from('people AS p')
            ->where('p.person_id = '.$key['customer_id'])
            ->get()->result();
            $dataConvert[$index3++] = [
                'customer_id' => $key['customer_id'],
                'price_total' => $key['price_total'],
                'num_of_contract'=> $key['num_of_contract'],
                'first_name'=>$person_info[0]->first_name,
                'last_name'=>$person_info[0]->last_name,
                'person_id'=>$person_info[0]->person_id,


            ];
        }

        return $dataConvert;
    }

#Danh sách 10 hợp đồng to nhất
    function top_contract()
    {
        $person_id = $this->Employee->get_logged_in_employee_info()->person_id;
        // $list = $this->get_list_customer_by_id($person_id);

        // var_dump(empty($list));die();
         // echo $this->db->last_query();die();
        $this->db->select('c.id,CONCAT(pe.first_name," ",pe.last_name) as customer_name, c.name,p.price ,c.code,pe.person_id');
        $this->db->from('phppos_contract as c');
        $this->db->join('(SELECT SUM(IF(pa.vat="published",pa.price/1.1,pa.price)) as price, pa.vat, pa.c_status,pa.contract_id FROM phppos_contract_payment as pa GROUP BY pa.contract_id ) as p', 'p.contract_id = c.id', 'left');
        $this->db->join('phppos_sales as s', 'c.sale_id = s.sale_id');
        $this->db->join('phppos_people pe', 's.customer_id = pe.person_id');
        $this->db->join('phppos_customers as cu', 'cu.person_id = s.customer_id');

        if($this->_scopeOfView=="view_scope_owner"){
         $this->db->where('cu.created_by', $person_id);
         $this->db->or_like('cu.watcher_manager','%'.$person_id.'%');


     }
     if($this->_scopeOfView=="view_scope_location"){
        $this->db->where('cu.created_location_id', $this->Employee->get_logged_in_employee_current_location_id());
        // $this->db->or_where('cu.created_by', $person_id);
        // $this->db->or_like('cu.watcher_manager','%'.$person_id.'%');
    }
    $this->db->group_by('cu.id');
    $this->db->order_by('p.price', 'desc');
    $this->db->limit(10);


    $result = $this->db->get()->result_array();
    return $result;
        // echo $this->db->last_query();die();
}

#DS 10 khách hàng có hợp đồng to nhất
function top_customer()
{
    $list = $this->get_list_customer_by_id($this->Employee->get_logged_in_employee_info()->person_id);
    $this->db->select('COUNT(pe.person_id) as num_of_contract,pe.person_id,CONCAT(pe.first_name," ",pe.last_name) as customer_name, 
        GROUP_CONCAT(c.name) as list_contract,SUM(p.total) as price_total');
    $this->db->from('phppos_people as pe');
    $this->db->join('phppos_sales as s', 's.customer_id = pe.person_id', 'left');
    $this->db->join('phppos_contract as c', 'c.sale_id =s.sale_id');
    $this->db->join('phppos_customers as cu', 'cu.person_id = s.customer_id');
    $this->db->join('(SELECT SUM(IF(pa.vat="published",pa.price/1.1,pa.price)) as total, pa.vat, pa.c_status,pa.contract_id FROM phppos_contract_payment as pa WHERE pa.c_status="done" OR pa.c_status="liquidated" GROUP BY pa.contract_id ) as p', 'p.contract_id = c.id', 'left');
    if($this->_scopeOfView=="view_scope_owner"){
     $this->db->where('cu.created_by', $person_id);
     $this->db->or_like('cu.watcher_manager','%'.$person_id.'%');


 }
 if($this->_scopeOfView=="view_scope_location"){
    $this->db->where('cu.created_location_id', $this->Employee->get_logged_in_employee_current_location_id());
    $this->db->or_where('cu.created_by', $person_id);
    $this->db->or_like('cu.watcher_manager','%'.$person_id.'%');
}
$this->db->group_by('pe.person_id');
$this->db->order_by('price_total', 'desc');
$this->db->limit(10);
$result =  $this->db->get()->result_array();
return $result;

}
public function saveItem($arrParam = null, $options = null){
    if($options['file'] == 'add'){
        $data['customer_id']            =               $arrParam['customer_id'];
        $data['name']                   =               stripslashes($arrParam['name']);
        $data['file_name']              =               stripslashes($arrParam['file_name']);
        $data['size']                   =               $arrParam['size'];
        $data['extension']              =               $arrParam['extension'];
        $data['excerpt']                =               stripslashes($arrParam['excerpt']);

        $data['created']                =               @date("Y-m-d H:i:s");
        $data['modified']               =               @date("Y-m-d H:i:s");
        $data['created_by']             =               $this->Employee->get_logged_in_employee_info()->person_id;

        $this->db->insert('customer_files',$data);

        $this->db->flush_cache();

    }elseif($options['file'] == 'edit'){
        $this->db->where("id",$arrParam['id']);

        $data['customer_id']            =               $arrParam['customer_id'];
        $data['name']                   =               stripslashes($arrParam['name']);
        $data['file_name']              =               stripslashes($arrParam['file_name']);
        $data['size']                   =               $arrParam['size'];
        $data['extension']              =               $arrParam['extension'];
        $data['excerpt']                =               stripslashes($arrParam['excerpt']);

        $data['modified']               =               @date("Y-m-d H:i:s");
        $data['modified_by']            =               $this->Employee->get_logged_in_employee_info()->person_id;

        $this->db->update('phppos_customer_files',$data);

        $this->db->flush_cache();

    }

}


    #Lấy số lượng kahchs hàng theo phân loại
    #Mặc định là khu vực địa lý


function get_list_type($table)
{
    return $this->db->get($table)->result_array();
}


function get_number_type($column ='tier_id',$option=null,$option2=null)
{

 $this->db->select('c.id,c.person_id');
 $this->db->from('phppos_customers as c');
 if($option2!=null)
 {
    $this->db->join($option2['table'], $option2['table'].".".$option2['column']."= c.person_id");
}
$this->db->where('c.deleted',0);
if($option!=null)
{
    $this->db->where($column, $option);
}
$rs = $this->db->get()->result_array();
return count($rs);


}



function so_hop_dong($id){
    $this->db->select('c.name as contract_name,c.code,c.date_signing,c.status,p.last_name,s.sale_id,s.sale_time,c.status,t.name as task_name, c.id as contract_id, s.customer_id');
    $this->db->from('phppos_sales as s');
    $this->db->join('phppos_people as p', 'p.person_id = s.customer_id');
    $this->db->join('phppos_tasks as t', 't.id = s.task_id', 'left');
    $this->db->join('phppos_contract as c', 's.sale_id = c.sale_id', 'left');

    $this->db->where('s.customer_id', $id);
    $list = $this->db->get()->result_array();
    $array = lang('contract_status');
    foreach ($list as $key => &$value) {
        if(!empty($value['status'])){
            $value['status'] = $array[$value['status']];

        }
    }
    return $list;
}


function get_list_customer_by_id($person_id){
    $this->db->select('id');
    $this->db->from('phppos_customers');
    $this->db->where('created_by', $person_id);
    $this->db->or_like('watcher_manager','%'.$person_id.'%');
    $result = $this->db->get()->result_array();
    $data =array();
    foreach ($result as $key => $value) {
        $data[$key] = $value['id'];
    }
    return $data;
}


function count_customer(){
    $person_id  = $this->Employee->get_logged_in_employee_info()->person_id;
    $this->db->select('c.id');
    $this->db->from('phppos_customers as c');
    $this->db->where('c.deleted', 0);
    if($this->_scopeOfView=="view_scope_owner"){
       $this->db->where('c.created_by', $person_id);
   }
   if($this->_scopeOfView=="view_scope_location"){
    $this->db->where('c.created_location_id', $this->Employee->get_logged_in_employee_current_location_id());

}

$result = $this->db->get()->result_array();
return $result;
}

}

?>
