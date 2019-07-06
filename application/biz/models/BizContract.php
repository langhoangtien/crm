<?php
class BizContract extends CI_Model{
    protected $_table                    = 'contract';
    protected $_id_admin                 = null;
    protected $_fields                   = array();
    protected $_payment_fields           = array();
    protected $_delivery_fields          = array();
    protected $_contract_payment_fields  = array();
    protected $_status                   = array();
    protected $_type                     = array();
    public $_scopeOfView = 'view_scope_owner';

    public function __construct(){
        parent::__construct();
        $this->load->model('TasksRelation');
        $this->load->library('MY_System_Info');
        $info            = new MY_System_Info();
        $user_info       = $info->getInfo();

        $this->_id_admin = $user_info['id'];



        $this->_scopeOfView = 'view_scope_owner';
        if ($this->Employee->has_module_action_permission(
            'contracts',
            'view_scope_location',
            $this->Employee->get_logged_in_employee_info()->person_id)
    ) {
            $this->_scopeOfView = 'view_scope_location';
        } 

        if($this->Employee->has_module_action_permission(
            'contracts',
            'view_scope_all',
            $this->Employee->get_logged_in_employee_info()->person_id)
    ) {
            $this->_scopeOfView = 'view_scope_all';
        }




        $this->_fields   =  array(
            'code'          => 'c.code',
            'name'          => 'c.name',
            'serial'        => 'c.serial',
            'sale_id'       => 's.sale_id',
            'receiving_id'  => 'r.receiving_id',
            'date_signing'  => 'c.date_signing',
            'type'          => 'c.type',
            'status'        => 'c.status',
        );

        $this->_payment_fields = array(
            'id'            => 'cp.id',
            'name'          => 'cp.name',
            'date_payment'  => 'cp.date_payment',
            'vat'           => 'cp.vat',
        );

        $this->_contract_payment_fields = array(
            'id'            => 'cpd.id',
            'name'          => 'cpd.name',
            'price'         => 'cpd.price',
            'note'          => 'cpd.note',
        );

        $this->_delivery_fields = array(
            'id'            => 'cd.id',
            'name'          => 'cp.name',
            'date'          => 'cd.date',
            'company_name'  => 'cd.company_name',
            'address'       => 'cd.address',
            'payment_name'  => 'cp.name',
        );

        $this->_status = lang('contract_status');
        $this->_type   = lang('contract_type');
    }

    function item_select_box($arrParams = null, $options = null) {
        $this->db->select("c.id, c.name, c.code")
        ->from('contract AS c')
        ->where('c.deleted', 0)
        ->order_by('c.id', 'DESC');

        if(!empty($arrParams['type'])) {
            $this->db->where('c.type', $arrParams['type']);
        }

        if(!empty($arrParams['option'])) {
            $this->db->where('c.option', $arrParams['option']);
        }

        $query = $this->db->get();
        $result = $query->result_array();

        return $result;
    }

    function count_contract_delivery($arrParams = null, $options = null) {
        $this->db -> select('COUNT(cd.id) AS totalItem')
        -> from('contract_delivery AS cd')
        -> join('contract_payment AS cp', 'cd.contract_payment_id = cp.id', 'left')
        -> where('cp.contract_id', $arrParams['contract_id']);


        $query = $this->db->get();

        $result = $query->row()->totalItem;

        $this->db->flush_cache();

        return $result;
    }

    function count_circle_contract($arrParams = null, $options = null) {
        $key_filter = 'contract_'.$arrParams['option'].'_circle_filter';
        if(!empty($arrParams['keywords'])) {
            $keywords = trim($arrParams['keywords']);
            $where    = 'AND (c.name LIKE \'%'.$keywords.'%\' OR c.code LIKE \'%'.$keywords.'%\')';
            $_SESSION[$key_filter]['keywords'] = $keywords;
        }
        else
        {
            $where ='';
        }
        $sql = 'SELECT count(c.id) AS total_item
        FROM '.$this->db->dbprefix('contract').' AS c
        WHERE DATEDIFF(CURDATE() , c.date_start) >= 0
        AND c.option LIKE \''.$arrParams['option'].'\'
        AND c.type LIKE \'parttime\'
        '.$where.'
        AND c.circle > 0
        AND (
        (DATEDIFF(CURDATE() , c.date_start) % c.circle >= (c.circle - c.bidding) OR (DATEDIFF(CURDATE() , c.date_start) % c.circle = 0 AND DATEDIFF(CURDATE() , c.date_start) != 0))
        AND DATEDIFF(CURDATE() , DATE_ADD(c.date_circle_solve, INTERVAL -c.bidding DAY)) >= c.circle
        )
        AND c.deleted = 0
        ';

        $query  = $this->db->query($sql);
        $result = $query->row_array();

        return $result['total_item'];

        $this->db->flush_cache();

        return $result;

    }

    function count_item($arrParams = null, $options = null) {
        if($options == null) {
            $key_filter = 'contract_'.$arrParams['option'].'_filter';

            $this->db -> select('COUNT(c.id) AS totalItem')
            -> from('contract AS c')
            -> where('c.option', $arrParams['option'])
            -> where('c.deleted', 0);

            if(!empty($arrParams['keywords'])) {
                $keywords = trim($arrParams['keywords']);
                $this->db->where('(c.name LIKE \'%'.$keywords.'%\' OR c.code LIKE \'%'.$keywords.'%\')');

                $_SESSION[$key_filter]['keywords'] = $keywords;
            }
            if(!empty($arrParams['start_date']) && !empty($arrParams['end_date'])) 
            {
               $start = $arrParams['start_date'];
               $end   = $arrParams['end_date'];
               $this->db->where("c.date_signing BETWEEN '".$start."' AND '".$end."'");
           }
           if(!empty($arrParams['type']) && $arrParams['type'] != 'all') {
            $type_value = $arrParams['type'];
            $this->db->where('c.type', $type_value);
        }else
        $type_value = 'all';

        $_SESSION[$key_filter]['type'] = $type_value;

        $query = $this->db->get();

        $result = $query->row()->totalItem;

        $this->db->flush_cache();

        return $result;
    }elseif($options['task'] == 'expired') {
        $key_filter = 'contract_'.$arrParams['option'].'_expired_filter';

        $this->db -> select('COUNT(c.id) AS totalItem')
        -> from('contract AS c')
        -> where('c.option', $arrParams['option'])
        -> where('c.type', 'parttime')
        -> where('DATEDIFF(DATE_ADD(CURDATE(), INTERVAL c.bidding DAY) , c.date_expiration) >= 0')
        ->where('DATEDIFF(DATE_ADD(c.date_expiration_solve,INTERVAL c.bidding DAY) , c.date_expiration) < 0')
        ->where('c.deleted', 0);

        if(!empty($arrParams['keywords'])) {
            $keywords = trim($arrParams['keywords']);
            $this->db->where('(c.name LIKE \'%'.$keywords.'%\' OR c.code LIKE \'%'.$keywords.'%\')');

            $_SESSION[$key_filter]['keywords'] = $keywords;
        }

        $query = $this->db->get();

        $result = $query->row()->totalItem;

        $this->db->flush_cache();

        return $result;    
    }

}

function count_contract_payment($arrParams = null, $options = null) {
    $this->db -> select('COUNT(cp.id) AS totalItem')
    -> from('contract_payment AS cp')
    -> where('cp.contract_id', $arrParams['contract_id']);

    $query = $this->db->get();

    $result = $query->row()->totalItem;

    $this->db->flush_cache();

    return $result;
}

function count_payment_detail($arrParams = null, $options = null) {
    $this->db -> select('COUNT(cpd.id) AS totalItem')
    -> from('contract_payment_detail AS cpd')
    -> where('cpd.contract_payment_id', $arrParams['payment_id']);

    $query = $this->db->get();

    $result = $query->row()->totalItem;

    $this->db->flush_cache();

    return $result;
}

function get_items($contract_ids, $options = null) {
    $this->db->select("c.id, c.code, c.created_by, c.file")
    ->from('contract AS c')
    ->where('c.id IN ('.implode(', ', $contract_ids).')')
    ->where('c.deleted', 0);

    $query = $this->db->get();

    $result = $query->result_array();

    $this->db->flush_cache();

    return $result;
}

function list_contract_circle($arrParams = null, $options = null) {
    $key_filter = 'contract_'.$arrParams['option'].'_expired_filter';
    $paginator = $arrParams['paginator'];
    if(!empty($arrParams['keywords'])) {
        $keywords = trim($arrParams['keywords']);
        $where    = 'AND (c.name LIKE \'%'.$keywords.'%\' OR c.code LIKE \'%'.$keywords.'%\')';

        $_SESSION[$key_filter]['keywords'] = $keywords;
    }

    if(!empty($arrParams['col']) && !empty($arrParams['order'])){
        $col   = $this->_fields[$arrParams['col']];
        $order = $arrParams['order'];

        $this->db->order_by($col, $order);

        $_SESSION[$key_filter]['col']  = $arrParams['col'];
        $_SESSION[$key_filter]['order'] = $arrParams['order'];
    }

    $page = $arrParams['page'];
    $limit = $paginator['per_page'];
    $offset = ($page - 1)*$paginator['per_page'];
    if($arrParams['option'] == 'customer') {
        $sql = 'SELECT c.id, c.code, c.name, c.type, c.status, s.customer_id, s.sale_id, DATE_FORMAT(c.date_signing, \'%d-%m-%Y\') AS date_signing, IF(DATEDIFF(CURDATE() , c.date_circle_solve) > circle, \'out\', \'in\') as in_or_out
        FROM '.$this->db->dbprefix(contract).' AS c
        LEFT JOIN '.$this->db->dbprefix(sales).' AS s
        ON c.sale_id = s.sale_id
        WHERE DATEDIFF(CURDATE() , c.date_start) >= 0
        AND c.option LIKE \''.$arrParams['option'].'\'
        AND c.type LIKE \'parttime\'
        '.$where.'
        AND c.circle > 0
        AND (
        (DATEDIFF(CURDATE() , c.date_start) % c.circle >= (c.circle - c.bidding) OR (DATEDIFF(CURDATE() , c.date_start) % c.circle = 0 AND DATEDIFF(CURDATE() , c.date_start) != 0))
        AND DATEDIFF(CURDATE() , DATE_ADD(c.date_circle_solve, INTERVAL -c.bidding DAY)) >= c.circle

        )
        AND c.deleted = 0
        ORDER BY '.$col.' '.$order.'
        LIMIT '.$limit.' OFFSET '.$offset;

    }elseif($arrParams['option'] == 'supplier') {
        $sql = 'SELECT c.id, c.code, c.name, c.type, c.status, r.supplier_id, r.receiving_id, DATE_FORMAT(c.date_signing, \'%d-%m-%Y\') AS date_signing, IF(DATEDIFF(CURDATE() , c.date_circle_solve) > circle, \'out\', \'in\') AS in_or_out
        FROM '.$this->db->dbprefix(contract).' AS c
        LEFT JOIN '.$this->db->dbprefix(receivings).' AS r
        ON c.receiving_id = r.receiving_id
        WHERE DATEDIFF(CURDATE() , c.date_start) >= 0
        AND c.option LIKE \''.$arrParams['option'].'\'
        AND c.type LIKE \'parttime\'
        '.$where.'
        AND c.circle > 0
        AND c.deleted = 0
        AND (
        (DATEDIFF(CURDATE() , c.date_start) % c.circle >= (c.circle - c.bidding) OR (DATEDIFF(CURDATE() , c.date_start) % c.circle = 0 AND DATEDIFF(CURDATE() , c.date_start) != 0))
        AND DATEDIFF(CURDATE() , DATE_ADD(c.date_circle_solve, INTERVAL -c.bidding DAY)) >= c.circle
        )
        ORDER BY '.$col.' '.$order.'
        LIMIT '.$limit.' OFFSET '.$offset;
    }


    $query  = $this->db->query($sql);
    $result = $query->result_array();

    $this->db->flush_cache();
    if(!empty($result)) {
        if($arrParams['option'] == 'customer') {
            $tblCustomers = $this->model_load_model('Customer');
            $customer_ids = array();
            foreach($result as $val) {
                if(!empty($val['customer_id']))
                    $customer_ids[] = $val['customer_id'];
            }

            $customer_ids = array_unique($customer_ids);
            if(count($customer_ids)>0) {
                $sql = 'SELECT p.first_name, p.last_name, p.phone_number, p.email, p.address_1, p.address_2, c.company_name, c.id, c.person_id
                FROM '.$this->db->dbprefix(customers).' AS c
                LEFT JOIN '.$this->db->dbprefix(people).' AS p
                ON c.person_id = p.person_id
                WHERE c.person_id IN ('.implode(',', $customer_ids).')';

                $query  = $this->db->query($sql);
                $result_tmp = $query->result_array();

                $this->db->flush_cache();
                if(!empty($result_tmp)) {
                    foreach($result_tmp as $customer_item)
                        $customer_list[$customer_item['person_id']] = $customer_item;
                }
            }

            foreach($result as &$value) {
                $customer_info          = $customer_list[$value['customer_id']];
                $value['status']        = $this->_status[$value['status']];
                $value['type']          = $this->_type[$value['type']];
                if(!empty($customer_info))
                    $value['customer_name'] = $customer_info['first_name'] . ' ' . $customer_info['last_name'];
                else
                    $value['customer_name'] = '&nbsp';

            }
        }elseif($arrParams['option'] == 'supplier') {
            $tblSupplier = $this->model_load_model('Supplier');
            $supplier_ids = array();
            foreach($result as $val) {
                if(!empty($val['supplier_id']))
                    $supplier_ids[] = $val['supplier_id'];
            }

            $supplier_ids = array_unique($supplier_ids);

            if(count($supplier_ids)>0) {
                $sql = 'SELECT p.first_name, p.last_name, p.phone_number, p.email, p.address_1, p.address_2, s.company_name, s.id, s.person_id
                FROM '.$this->db->dbprefix(suppliers).' AS s
                LEFT JOIN '.$this->db->dbprefix(people).' AS p
                ON s.person_id = p.person_id
                WHERE s.person_id IN ('.implode(',', $supplier_ids).')';

                $query  = $this->db->query($sql);
                $result_tmp = $query->result_array();

                $this->db->flush_cache();
                if(!empty($result_tmp)) {
                    foreach($result_tmp as $supplier_item)
                        $supplier_list[$supplier_item['person_id']] = $supplier_item;
                }
            }

            foreach($result as &$value) {
                $supplier_info          = $supplier_list[$value['supplier_id']];
                $value['status']        = $this->_status[$value['status']];
                $value['type']          = $this->_type[$value['type']];
                if(!empty($supplier_info))
                    $value['supplier_name'] = $supplier_info['company_name'];
                else
                    $value['supplier_name'] = '';
            }
        }
    }

    return $result;
}

    /**
     * @param $person_id
     * @return array|null
     */
    function get_related_contract_ids($person_id) {
        $this->db->distinct();
        $this->db->select('c.id');
        $this->db->from('contract AS c');
        $this->db->join('task_user_relations AS t', 'c.project_id = t.task_id');
        $this->db->join('employees AS e', 'e.id = t.user_id');
        $this->db->where('person_id', $person_id);
        $this->db->where('(is_xem = 1 OR is_implement = 1 OR is_pheduyet = 1 OR is_join = 1)');
        $query = $this->db->get();
        // echo $this->db->last_query();
        if (!$query) {
            return null;
        }
        $result = $query->result();
        $query->free_result();
        $this->db->flush_cache();
        $contract_ids = [];
        foreach ($result as $row) {
            $contract_ids[] = $row->id;
        }
        return $contract_ids;
    }

    function list_item($arrParams = null, $options = null) {

    // Get Contracts From Related Projects
    // Lay cac hop dong lien quan den du an (duoc xem, phu trach...)
        $related_contract_ids = $this->get_related_contract_ids($_SESSION['person_id']);

        $location = $this->Employee->get_current_location_info()->location_id;
        if($options['task'] == null) {
            $key_filter = 'contract_'.$arrParams['option'].'_filter';
            if(empty($arrParams['paginator']))
            {
                $paginator['per_page'] = 10000;
            }
            else
            {
                $paginator = $arrParams['paginator'];
            }
            if($arrParams['option'] == 'customer') {

                $this->db->select("c.deleted, c.id, c.code,CONCAT(pe.first_name,pe.last_name) AS customer_name,pe.person_id, c.name, c.type, c.status,
                    t.name AS item_name,s.customer_id, s.sale_id,s.task_id")
                ->select("DATE_FORMAT(c.date_signing, '%d-%m-%Y') as date_signing", FALSE)
                ->from('contract AS c')
                ->join('sales AS s', 'c.sale_id = s.sale_id', 'left')
                ->join('items AS t','c.item_id=t.item_id','left')
                ->join('customers AS cu','s.customer_id = cu.id','left')
                ->join('people AS pe','pe.person_id = cu.person_id','left');
                $this->db->group_start();
                if($this->_scopeOfView == 'view_scope_owner')
                {
                    $this->db->where('c.created_by', $this->Employee->get_logged_in_employee_info()->id);
                }
                elseif ($this->_scopeOfView == 'view_scope_location') {
                    $this->db->where('c.locations_id',$location);  
                }

                $this->db->group_by('c.id');

            // Get Contracts From Related Projects
            // Lay cac hop dong lien quan den du an (duoc xem, phu trach...)
                
                $this->db->where('c.deleted', 0);
                if (!empty($related_contract_ids)) {
                    $this->db->or_where('c.id IN (' . implode(',', $related_contract_ids) . ')');
                }


            }elseif($arrParams['option'] == 'supplier') {
                $this->db->select("c.deleted,c.id, c.code, c.name, c.type, c.status, r.supplier_id, r.receiving_id")
                ->select("DATE_FORMAT(c.date_signing, '%d-%m-%Y') as date_signing", FALSE)
                ->from('contract AS c')
                ->join('receivings AS r', 'r.receiving_id = c.receiving_id', 'left')
                ->where('c.deleted', 0);
            }
            $this->db->group_end();
            $this->db->where('c.option', $arrParams['option']);
            if(!empty($arrParams['start_date']) && !empty($arrParams['end_date'])) 
            {
                $start = $arrParams['start_date'];
                $end   = $arrParams['end_date'];
                $this->db->where("c.date_signing >=",$start);
                $this->db->where('c.date_signing <', $end);
            }
            if (!empty($arrParams['month']) && !empty($arrParams['year'])) {
                $month = $arrParams['month'];
                $year = $arrParams['year'];
                $this->db->where("month(c.date_signing)='".$month."' AND year(c.date_signing) = '".$year."'");
            }
            
            if(!empty($arrParams['type']) && $arrParams['type'] != 'all') {
                $type_value = $arrParams['type'];
                if($type_value=="not-liquidated"){
                    $this->db->where('c.status !=', 'liquidated');
                }
                else{
                    $this->db->where('c.status', $type_value);
                }
                
            }else
            {
                $type_value = 'all';
            }

            if(!empty($arrParams['col']) && !empty($arrParams['order'])){
                $col   = $this->_fields[$arrParams['col']];
                $order = $arrParams['order'];
                $this->db->order_by($col, $order);

                $_SESSION[$key_filter]['col']  = $arrParams['col'];
                $_SESSION[$key_filter]['order'] = $arrParams['order'];
            }

            $page = (empty($arrParams['page']))? 1:$arrParams['page'];
            $this->db->limit($paginator['per_page'],($page - 1)*$paginator['per_page']);

            $query = $this->db->get();
        // echo $this->db->last_query();
            $result = $query->result_array(); 
            $this->db->flush_cache();
        }elseif($options['task'] == 'expired') {
            $key_filter = 'contract_'.$arrParams['option'].'_expired_filter';

            $paginator = $arrParams['paginator'];
            if($arrParams['option'] == 'customer') {
                $this->db->select("c.id, c.code, c.name, c.type, c.status, s.customer_id, s.sale_id, ,IF(DATEDIFF(CURDATE() , c.date_expiration) > 0, 'out', 'in') as in_or_out")
                ->select("DATE_FORMAT(c.date_signing, '%d-%m-%Y') as date_signing", FALSE)
                ->from('contract AS c')
                ->join('sales AS s', 'c.sale_id = s.sale_id', 'left');
            }elseif($arrParams['option'] == 'supplier') {
                $this->db->select("c.id, c.code, c.name, c.type, c.status, r.supplier_id, r.receiving_id, IF(DATEDIFF(CURDATE() , c.date_expiration) > 0, 'out', 'in') as in_or_out")
                ->select("DATE_FORMAT(c.date_signing, '%d-%m-%Y') as date_signing", FALSE)
                ->from('contract AS c')
                ->join('receivings AS r', 'r.receiving_id = c.receiving_id', 'left');
            }

            $this->db->where('c.type', 'parttime')
            ->where('c.option', $arrParams['option'])
            ->where('c.deleted', 0)
            ->where('DATEDIFF(DATE_ADD(CURDATE(), INTERVAL c.bidding DAY) , c.date_expiration) >= 0')
            ->where('DATEDIFF(DATE_ADD(c.date_expiration_solve,INTERVAL c.bidding DAY) , c.date_expiration) < 0');

            if(!empty($arrParams['keywords'])) {
                $keywords = trim($arrParams['keywords']);
                $this->db->where('(c.name LIKE \'%'.$keywords.'%\' OR c.code LIKE \'%'.$keywords.'%\')');

                $_SESSION[$key_filter]['keywords'] = $keywords;
            }

            if(!empty($arrParams['col']) && !empty($arrParams['order'])){
                $col   = $this->_fields[$arrParams['col']];
                $order = $arrParams['order'];

                $this->db->order_by($col, $order);

                $_SESSION[$key_filter]['col']  = $arrParams['col'];
                $_SESSION[$key_filter]['order'] = $arrParams['order'];
            }

            $page = $arrParams['page'];
            $this->db->limit($paginator['per_page'],($page - 1)*$paginator['per_page']);

            $query = $this->db->get();

            $result = $query->result_array();

            $this->db->flush_cache();

        }

        if(!empty($result)) {
            if($arrParams['option'] == 'customer') {
                $tblCustomers = $this->model_load_model('Customer');
                $customer_ids = array();
                foreach($result as $val) {
                    if(!empty($val['customer_id']))
                        $customer_ids[] = $val['customer_id'];
                }

                $customer_ids = array_unique($customer_ids);

                if(!empty($customer_ids)&& count($customer_ids)> 0)
                {
                    $flag = true;
                    $customer_list = $tblCustomers->get_info_by_ids($customer_ids);
                }
                else
                {
                    $flag = false;
                }
                foreach($result as &$value) {
                    if($flag)
                    {
                        $customer_info        = isset($customer_list[$value['customer_id']])?$customer_list[$value['customer_id']]:array();
                    }
                    $value['status']        = $this->_status[$value['status']];
                    $value['type']          = $this->_type[$value['type']];
                    if(!empty($customer_info))
                        $value['customer_name'] = $customer_info['first_name'] . ' ' . $customer_info['last_name'];
                    else
                        $value['customer_name'] = '&nbsp';

                }









            }elseif($arrParams['option'] == 'supplier') {
                $tblSupplier = $this->model_load_model('Supplier');
                $supplier_ids = array();
                foreach($result as $val) {
                    if(!empty($val['supplier_id']))
                        $supplier_ids[] = $val['supplier_id'];
                }

                $supplier_ids = array_unique($supplier_ids);

                if(count($supplier_ids) > 0)
                    $supplier_list = $tblSupplier->get_info_by_ids($supplier_ids);

                foreach($result as &$value) {
                    $supplier_info          = $supplier_list[$value['supplier_id']];
                    $value['status']        = $this->_status[$value['status']];
                    $value['type']          = $this->_type[$value['type']];
                    if(!empty($supplier_info))
                        $value['supplier_name'] = $supplier_info['company_name'];
                    else
                        $value['supplier_name'] = '';
                }
            }
        }

        // var_dump($result);die();
        return $result;
    }

    function list_contract_delivery_items($delivery_id, $options = null) {
        $this->db -> select('*')
        -> from('contract_delivery_items')
        -> where('contract_delivery_id', $delivery_id)
        -> order_by('line ASC');

        $query = $this->db->get();

        $result_tmp = $query->result_array();
        $this->db->flush_cache();
        if(!empty($result_tmp)) {
            foreach($result_tmp as $item) {
                $key = 'i-'.$item['item_id'].'-'.$item['line'];
                $result[$key] = $item;
            }
        }

        $this->db -> select('*')
        -> from('contract_delivery_item_kits')
        -> where('contract_delivery_id', $delivery_id)
        -> order_by('line ASC');

        $query = $this->db->get();

        $result_tmp = $query->result_array();
        $this->db->flush_cache();
        if(!empty($result_tmp)) {
            foreach($result_tmp as $item_kit) {
                $key = 'k-'.$item_kit['item_id'].'-'.$item_kit['line'];
                $result[$key] = $item;
            }
        }

        return $result;

    }

    function list_contract_delivery($arrParams = null, $options = null) {
        if($options == null) {
            $paginator = $arrParams['paginator'];
            $this->db -> select('cd.*, cp.name AS payment_name')
            -> select("DATE_FORMAT(cd.date, '%d-%m-%Y') as date_format", FALSE)
            -> from('contract_delivery AS cd')
            -> join('contract_payment AS cp', 'cd.contract_payment_id = cp.id', 'left')
            -> where('cp.contract_id', $arrParams['contract_id']);

            if(!empty($arrParams['col']) && !empty($arrParams['order'])){
                $col   = $this->_delivery_fields[$arrParams['col']];
                $order = $arrParams['order'];

                $this->db->order_by($col, $order);

                $_SESSION['contract_delivery_filter']['col']   = $arrParams['col'];
                $_SESSION['contract_delivery_filter']['order'] = $arrParams['order'];
            }

            $_SESSION['contract_delivery_filter']['current_page'] = $page = $arrParams['page'];
            $this->db->limit($paginator['per_page'],($page - 1)*$paginator['per_page']);

            $query = $this->db->get();
            $result = $query->result_array();
            $this->db->flush_cache();
        }elseif($options['task'] == 'all') {
            $this->db -> select('cd.*, cp.name AS payment_name')
            -> select("DATE_FORMAT(cd.date, '%d-%m-%Y') as date_format", FALSE)
            -> from('contract_delivery AS cd')
            -> join('contract_payment AS cp', 'cd.contract_payment_id = cp.id', 'left')
            -> where('cp.contract_id', $arrParams['contract_id'])
            -> order_by('cp.ord', 'ASC')
            -> order_by('cd.date', 'ASC');

            $query = $this->db->get();
            $result = $query->result_array();
            $this->db->flush_cache();
        }

        return $result;
    }

    public function list_contract_payment($arrParams = null, $options = null) {
        if($options == null) {
            $paginator = $arrParams['paginator'];
            $this->db -> select('*')
            ->select("DATE_FORMAT(cp.date_payment, '%d-%m-%Y') as date_payment_format", FALSE)
            ->select("(select t.name from ". $this->db->dbprefix("tasks") ." AS t where t.id = cp.task_id limit 1 ) AS task_name ")
            -> from('contract_payment AS cp')
            -> where('cp.contract_id', $arrParams['contract_id']);

            if(!empty($arrParams['col']) && !empty($arrParams['order'])){
                $col   = $this->_payment_fields[$arrParams['col']];
                $order = $arrParams['order'];

                $this->db->order_by($col, $order);
            }

            $page = $arrParams['page'];
            $this->db->limit($paginator['per_page'],($page - 1)*$paginator['per_page']);

            $query = $this->db->get();

            $result = $query->result_array();

            $this->db->flush_cache();

        }elseif($options['task'] == 'list-all') {
            $this->db -> select('*')
            -> select("DATE_FORMAT(cp.date_payment, '%d-%m-%Y') as date_payment_format", FALSE)
            -> from('contract_payment AS cp')
            -> where('cp.contract_id', $arrParams['contract_id'])
            -> order_by('cp.ord ASC');

            $query = $this->db->get();

            $result = $query->result_array();

            $this->db->flush_cache();
        }

        if(!empty($result)) {
            $contract_info = $arrParams['contract_info'];
            foreach($result as &$item) {
                if($contract_info['type'] == 'rule') {
                    $item['payment_price'] = to_currency($item['payment_price']);
                    if($item['price'] != -1)
                        $item['price'] = to_currency($item['price']);
                    elseif($item['percent'] != -1)
                        $item['price'] = to_quantity($item['percent']). '%';
                }else {
                    if(!isset($contract_sale_info))
                        $contract_sale_info = $arrParams['order_info'];

                    if($item['price'] != -1)
                        $item['price'] = to_currency($item['price']);
                    elseif($item['percent'] != -1) {
                        $price = ($item['percent']*$contract_sale_info['total'])/100;
                        $percent = to_quantity($item['percent']) . '%';
                        $item['price'] = to_currency($price);

                        if($options == null)
                            $item['price'] = $percent . ' - ' . $item['price'];
                        elseif($options['task'] == 'list-all')
                            $item['price'] = $item['price'] . ' ('.$percent.')';
                    }

                    $item['payment_price'] = to_currency($item['payment_price']);
                }
            }
        }


        return $result;
    }

    function list_payment_detail($arrParams = null, $options = null) {
        if($options == null) {
            $paginator = $arrParams['paginator'];
            $this->db -> select('*')
            -> from('contract_payment_detail AS cpd')
            -> where('cpd.contract_payment_id', $arrParams['payment_id']);

            if(!empty($arrParams['col']) && !empty($arrParams['order'])){
                $col   = $this->_contract_payment_fields[$arrParams['col']];
                $order = $arrParams['order'];

                $this->db->order_by($col, $order);

                $_SESSION['contract_payment_detail_filter']['col']   = $arrParams['col'];
                $_SESSION['contract_payment_detail_filter']['order'] = $arrParams['order'];
            }

            $_SESSION['contract_payment_detail_filter']['current_page'] = $page = $arrParams['page'];

            $this->db->limit($paginator['per_page'],($page - 1)*$paginator['per_page']);

            $query = $this->db->get();

            $result = $query->result_array();
            if(!empty($result)) {
                foreach($result as &$item) {
                    $item['price'] = to_currency($item['price']);
                    $item['note']  = nl2br($item['note']);
                }
            }
        }

        return $result;
    }

    function get_contract_delivery_detail_info($arrParams = null, $options = null) {
        $this->db -> select("*")
        -> select("DATE_FORMAT(date, '%d-%m-%Y') as date", FALSE)
        -> from('contract_delivery')
        -> where('id', $arrParams['id']);

        $query = $this->db->get();
        $result =  $query->row_array();
        $this->db->flush_cache();

        return $result;
    }

    function get_contract_payment_detail_info($arrParams = null, $options = null) {
        $this->db -> select("*")
        -> from('contract_payment_detail')
        -> where('id', $arrParams['id']);

        $query = $this->db->get();
        $result =  $query->row_array();
        $this->db->flush_cache();

        return $result;
    }

    function get_contract_payment_detail_infos($cid) {
        $this->db -> select("*")
        -> from('contract_payment_detail')
        -> where('id IN ('.implode(',', $cid).')');

        $query = $this->db->get();
        $result =  $query->result_array();
        $this->db->flush_cache();

        return $result;
    }

    function get_contract_payment_info($arrParams = null, $options = null) {
        $this->db -> select("*")
        -> select("DATE_FORMAT(date_payment, '%d/%m/%Y') as date_payment", FALSE)
        -> from('contract_payment')
        -> where('id', $arrParams['id']);

        $query = $this->db->get();
        $result =  $query->row_array();
        $this->db->flush_cache();

        if(!empty($result)) {
            if($result['percent'] != -1){
                $result['price'] = $result['percent'];
                $result['unit']  = 'percent';
            }else
            $result['unit']  = 'money';

        }
        return $result;
    }

    function get_item($arrParams = null, $options = null) {
        $this->db->select("*")
        ->select("DATE_FORMAT(date_start, '%d-%m-%Y') as date_start", FALSE)
        ->select("DATE_FORMAT(date_signing, '%d-%m-%Y') as date_signing", FALSE)
        ->select("DATE_FORMAT(date_expiration, '%d-%m-%Y') as date_expiration", FALSE)
        ->from($this->_table)
        ->where('id', $arrParams['id'])
        ->where('deleted', 0);

        $query = $this->db->get();
        $result =  $query->row_array();
        $this->db->flush_cache();

        if(!empty($result)) {
            if($options['task'] == 'full') {
                $this->db->select("*")
                ->select("DATE_FORMAT(date_payment, '%d-%m-%Y') as date_payment", FALSE)
                ->from('contract_payment')
                ->where('contract_id', $arrParams['id']);

                $query = $this->db->get();
                $result['payment'] =  $query->result_array();
                if(!empty($result['payment'])) {
                    foreach($result['payment'] as &$value) {
                        if($value['price'] != -1)
                            $value['price_format'] = number_format($value['price'],'0','.',',');

                        if($value['percent'] != -1)
                            $value['percent_format'] = number_format($value['percent'],'0','.',',');
                    }
                }
                $this->db->flush_cache();
            }
        }

        return $result;
    }

    function get_quantity_contract_of_current_month($arrParams = null, $options = null) {
        $month = date('m');
        $year  = date("y");
        $time  = $month . '-' . $year;

        $this->db->select("COUNT(c.id) AS total_item")
        ->from('contract AS c')
        ->where('c.code', $arrParams['code'])
        ->where("DATE_FORMAT(c.created, '%m-%y') = '$time'");

        $query = $this->db->get();

        $result_tmp = $query->row_array();

        $this->db->flush_cache();
        $result = $result_tmp['total_item'];

        return $result;
    }

    protected function price_in_sale($item, $taxes) {
        if($item['tax_included'] == 0)
            $price =  $item['item_unit_price'];
        else {
            $price = $item['item_unit_price'];
            if(!empty($taxes)) {
                $percent = 0;
                foreach($taxes as $tax) {
                    if($item['item_id'] == $tax['item_id'] && $item['line'] == $tax['line'])
                        $percent = $percent + $tax['percent'];
                }

                $dividend = (100 + $percent)/100;
                $price = $price / $dividend;
            }
        }

        return $price;
    }

    function save_contract_payment_detail($arrParams = null, $options = null) {
        if($options['task'] == 'update') {
            $arrParams['price'] = convert_number($arrParams['price']);
            $data               = $arrParams;

            if($arrParams['id'] == -1) {
                $this->db->insert('contract_payment_detail',$data);
                $lastId = $this->db->insert_id();
                $this->db->flush_cache();

                if($lastId > 0) {
                    $this->save_payment_price($arrParams['contract_payment_id']);
                }
            }else {
                $this->db->where("id",$arrParams['id']);
                $this->db->update('contract_payment_detail',$data);

                $this->save_payment_price($arrParams['contract_payment_id']);

                $this->db->flush_cache();
            }
        }
    }

    protected function save_payment_price($contract_payment_id) {
        $sum_price = $this->payment_price_total($contract_payment_id);

        $data = array(
            'payment_price' => $sum_price
        );

        $this->db->where("id",$contract_payment_id);
        $this->db->update('contract_payment',$data);

        $this->db->flush_cache();
    }

    function save_contract_payment($arrParams = null, $options = null) {
        if($options['task'] == 'update') {
            $vat = (isset($arrParams['c_payment_vat'])) ? $arrParams['c_payment_vat'] : 'unpublished';

            $data['name']         =  $arrParams['c_payment_name'];
            if(!empty($arrParams['c_date_payment']))
            {
                $data['date_payment'] =  date("Y-m-d", strtotime($arrParams['c_date_payment']));
            }

            $data['vat']          =  $vat;
            $data['contract_id']  =  $arrParams['contract_id'];
            $data['c_status']     =  $arrParams['c_status'];
            $data['task_id']     =  $arrParams['c_task_id'];


            if($arrParams['unit'] == 'money') {
                $data['price'] = convert_number($arrParams['c_payment_price']);
                $data['percent'] = -1;
            }else {
                $data['price']   = -1;
                $data['percent'] = convert_number($arrParams['c_payment_price']);
            }

            if($arrParams['payment_id'] == -1){
                $data['payment_price'] = 0;
                $data['created']          = @date("Y-m-d H:i:s");

                $this->db->insert('contract_payment',$data);
                $lastId = $this->db->insert_id();
                $this->db->flush_cache();

                if($lastId > 0) {
                    $this->db->where("id",$lastId);
                    $this->db->update('contract_payment',array('ord'=>$lastId));

                    $this->db->flush_cache();
                }
            }else {
                $this->db->where("id",$arrParams['payment_id']);
                $this->db->update('contract_payment',$data);
                $this->db->flush_cache();
                $lastId = $arrParams['payment_id'];
            }
        }elseif($options['task'] == 'update-payment-vat') {
            $data = array(
                'vat' => $arrParams['value']
            );
            $this->db->where("id",$arrParams['pk']);
            $this->db->update('contract_payment',$data);

            $this->db->flush_cache();
        }elseif($options['task'] == 'custom') {
            $id   = $arrParams['id'];
            $data = $arrParams['customs'];
            $this->db->where("id",$id);
            $this->db->update('contract_payment',$data);

            $this->db->flush_cache();
        }
    }

    function save_contract_delivery($arrParams = null, $options = null) {
        if($options['task'] == 'update') {
            $data['contract_payment_id']     = $arrParams['contract_payment_id'];
            $data['company_name']            = $arrParams['company_name'];
            $data['address']                 = $arrParams['address'];

            $data['date'] = date("Y-m-d", strtotime($arrParams['date']));

            if($arrParams['contract_delivery_id'] == -1) {
                $this->db->insert('contract_delivery',$data);
                $lastId = $this->db->insert_id();

                $this->db->flush_cache();

                if($lastId > 0) {
                    if(!empty($arrParams['deliver_items'])){
                        foreach($arrParams['deliver_items'] as &$delivery_item)
                            $delivery_item['contract_delivery_id'] = $lastId;

                        $this->db->insert_batch('contract_delivery_items', $arrParams['deliver_items']);
                    }

                    if(!empty($arrParams['deliver_items_kit'])) {
                        foreach($arrParams['deliver_items_kit'] as &$deliver_items_kit)
                            $deliver_items_kit['contract_delivery_id'] = $lastId;

                        $this->db->insert_batch('contract_delivery_item_kits', $arrParams['deliver_items_kit']);
                    }
                }

            }else {
                $this->db->where("id",$arrParams['contract_delivery_id']);
                $this->db->update('contract_delivery',$data);
                $this->db->flush_cache();
            }

        }
    }
    function save_contract_file($arrParam =null){
        $table = 'phppos_contract_files';
        $data['contract_id']       = $arrParam['contract_id'];
        $data['note']              = $arrParam['note'];
        $data['name_file']         = $arrParam['name_file'];
        $data['extension']         = $arrParam['extension'];
        $data['date_up']           = @date("Y-m-d H:i:s");
        $this->db->insert($table,$data);
    }
    function list_file_contract($arrParam=null){
        $this->db->select('*');
        $this->db->from('phppos_contract_files');

        if (!empty($arrParam['contract_id']))
        {
            $this->db->where('contract_id',$arrParam['contract_id']);
        }
        if (!empty($arrParam['id'])) {
            $this->db->where('id', $arrParam['id']);
        }
        return $this->db->get()->result_array();
    }
    function save_item($arrParams = null, $options = null) {
        if($options['task'] == 'update') {
            $data['code']              = $arrParams['code'];
            $data['name']              = $arrParams['name'];
            $data['status']            = $arrParams['status'];
            $data['type']              = $arrParams['type'];
            $data['date_signing']      = date("Y-m-d", strtotime(implode("-", array_reverse(explode("/", $arrParams['date_signing'])))));
            $data['date_start']        = empty($arrParams['date_start']) ? "" : date("Y-m-d",strtotime($arrParams['date_start']));
            $data['date_expiration']   = empty($arrParams['date_expiration']) ? "" : date("Y-m-d",strtotime($arrParams['date_expiration']));
            $data['status_date']       = date("Y-m-d", strtotime(implode("-", array_reverse(explode("/",$arrParams['status_date'])))));
            $data['note']              = $arrParams['note'];
            $data['file']              = $arrParams['file'];
            $data['extension']         = $arrParams['extension'];
            $data['modified']          = @date("Y-m-d H:i:s");
            $data['modified_by']       = $this->_id_admin;
            $data['parent_id']         = $arrParams['parent_id'];
            $data['circle']            = $arrParams['circle'];
            $data['bidding']           = $arrParams['bidding'];
            $data['item_id']            = $arrParams['service_id'];
            $data['project_id']         = $arrParams['project_id'];
            $data['locations_id']       = $arrParams['location_id'];


            $data['option']            = $arrParams['option'];
            if($arrParams['option'] == 'customer') {
                $data['sale_id']           = $arrParams['sale_id'];
                $data['receiving_id']     = 0;
            }elseif($arrParams['option'] == 'supplier') {
                $data['sale_id']           = 0;
                $data['receiving_id']     = $arrParams['receiving_id'];
            }

            if($arrParams['id'] == -1) {
                if($arrParams['type'] == 'rule')
                    $data['parent_id'] = -1;
                else {
                    $data['date_circle_solve']     = $data['date_start'];
                    $data['date_expiration_solve'] = $data['date_start'];
                }

                $data['created']           = @date("Y-m-d H:i:s");
                $data['created_by']        = $this->_id_admin;

                $this->db->insert($this->_table,$data);
                $lastId = $this->db->insert_id();

                $this->db->flush_cache();

                if($lastId > 0) {
                    if(!empty($arrParams['payment'])) {
                        foreach($arrParams['payment']['order'] as $key => $val) {
                            $tmp = array();
                            $amount = str_replace(",","",$arrParams['payment']['price'][$key]) ;
                            $unit   = $arrParams['payment']['unit'][$key];
                            $tmp['ord']          = $val;
                            $tmp['name']         = $arrParams['payment']['name'][$key];
                            $tmp['c_status']         = $arrParams['payment']['c_status'][$key];
                            if(!empty($arrParams['payment']['date_payment'][$key]))
                            {
                                $tmp['date_payment'] = date("Y-m-d", strtotime($arrParams['payment']['date_payment'][$key]));
                            }
                            $tmp['task_id'] = $arrParams['task_id']['task_id'][$key];
                            if($unit == 'money') {
                                $percent = -1;
                                $price  = $amount;
                            }else {
                                $percent = $amount;
                                $price   = -1;
                            }

                            $tmp['price']        = $price ;
                            $tmp['percent']      = $percent ;
                            // if($arrParams['type'] == 'rule')
                            //     $tmp['vat'] = '';
                            // else
                            $tmp['vat']          = $arrParams['payment']['vat'][$key];

                            $tmp['contract_id']  = $lastId;
                            $tmp['created']      = @date("Y-m-d H:i:s");

                            $data_arr[]          = $tmp;
                        }

                        $this->db->insert_batch('contract_payment', $data_arr);
                    }
                }

            }else {
                if($arrParams['old_date_start'] != $arrParams['date_start'] || $arrParams['old_circle'] != $arrParams['circle']){
                    $data['date_circle_solve']     = $data['date_start'];
                }

                $this->db->where("id",$arrParams['id']);

                $this->db->update($this->_table,$data);

                $this->db->flush_cache();

                $lastId = $arrParams['id'];
            }

            return $lastId;
        }
    }

    function delete_contract_delivery($cid) {
        // delete delivery
        $this->db->where('id IN (' . implode(', ', $cid) . ')');
        $this->db->delete('contract_delivery');

        // delete delivery items
        $this->db->where('contract_delivery_id IN (' . implode(', ', $cid) . ')');
        $this->db->delete('contract_delivery_items');

        // delete delivery items kit
        $this->db->where('contract_delivery_id IN (' . implode(', ', $cid) . ')');
        $this->db->delete('contract_delivery_item_kits');
    }

    function delete_contract_payment($cid) {
        // get delivery ids
        $delivery_ids = $this->get_contract_delivery_ids($cid);
        // delete contract payment
        $this->db->where('id IN (' . implode(', ', $cid) . ')');
        $this->db->delete('contract_payment');

        // delete contract payment detail
        $this->db->where('contract_payment_id IN (' . implode(', ', $cid) . ')');
        $this->db->delete('contract_payment_detail');

        // delete delivery
        if(count($delivery_ids)>0) {
            $this->db->where('contract_payment_id IN (' . implode(', ', $cid) . ')');
            $this->db->delete('contract_delivery');

            $this->db->where('contract_delivery_id IN (' . implode(', ', $delivery_ids) . ')');
            $this->db->delete('contract_delivery_items');

            $this->db->where('contract_delivery_id IN (' . implode(', ', $delivery_ids) . ')');
            $this->db->delete('contract_delivery_item_kits');
        }

        $this->db->flush_cache();
    }

    function delete_contract_payment_detail($cid, $contract_payment_id) {
        // delete
        $this->db->where('id IN (' . implode(', ', $cid) . ')');
        $this->db->delete('contract_payment_detail');

        // update price
        $this->save_payment_price($contract_payment_id);
        $this->db->flush_cache();
    }

    function delete_contract($cid) {
       $contracts            = $this->get_items($cid);

       $this->db->where('id IN (' . implode(', ', $cid) . ')');

       $this->db->delete('contract');
        // delete files
       if(!empty($contracts)) {
        foreach($contracts as $contract) {
            $upload_dir = DOCUMENT_PATH . 'files/store_' . $contract['created_by'];
            @unlink($upload_dir . '/' . $contract['file']);
        }
    }
}

function get_receiving_id_in_contract($receiving_ids = array()) {
    $result = array();
    $this->db -> select("receiving_id")
    -> from('contract')
    -> where('receiving_id IN ('.implode(',', $receiving_ids).')');

    $query = $this->db->get();
    $result_tmp = $query->result_array();
    $this->db->flush_cache();

    if(!empty($result_tmp)) {
        foreach($result_tmp as $val) {
            $result[] = $val['receiving_id'];
        }
        $result = array_unique($result);
    }

    return $result;
}

function get_sale_id_in_contract($sale_ids = array()) {
    $result = array();
    $this->db -> select("sale_id")
    -> from('contract')
    -> where('sale_id IN ('.implode(',', $sale_ids).')');

    $query = $this->db->get();
    $result_tmp = $query->result_array();
    $this->db->flush_cache();

    if(!empty($result_tmp)) {
        foreach($result_tmp as $val) {
            $result[] = $val['sale_id'];
        }

        $result = array_unique($result);
    }

    return $result;
}

function get_sale_ids($contract_ids) {
    $this->db -> select("sale_id")
    -> from('contract')
    -> where('id IN ('.implode(',', $contract_ids).')');

    $query = $this->db->get();
    $result_tmp = $query->result_array();
    $this->db->flush_cache();

    $result = array();
    if(!empty($result_tmp)) {
        foreach($result_tmp as $val)
            $result[] = $val['id'];

        $result = array_unique($result);
    }

    return $result;

}

function get_contract_payment_ids($contract_ids) {
    $this->db -> select("id")
    -> from('contract_payment')
    -> where('contract_id IN ('.implode(',', $contract_ids).')');

    $query = $this->db->get();
    $result_tmp = $query->result_array();
    $this->db->flush_cache();

    $result = array();
    if(!empty($result_tmp)) {
        foreach($result_tmp as $val)
            $result[] = $val['id'];

        $result = array_unique($result);
    }

    return $result;
}

function get_contract_delivery_ids($contract_payment_ids) {
    $result = array();

    if(count($contract_payment_ids) > 0) {
        $this->db -> select("id")
        -> from('contract_delivery')
        -> where('contract_payment_id IN ('.implode(',', $contract_payment_ids).')');

        $query = $this->db->get();
        $result_tmp = $query->result_array();
        $this->db->flush_cache();

        if(!empty($result_tmp)) {
            foreach($result_tmp as $val)
                $result[] = $val['id'];

            $result = array_unique($result);
        }
    }

    return $result;
}

function get_sum_quantity_from_delivery($contract_id, $item_id = 0, $item_kit_id = 0, $line = 0) {
    $result  =  array();
    $this->db -> select("contract_id, item_id, line, SUM(quantity) AS s_quantity")
    -> from('contract_delivery_items')
    -> where('contract_id', $contract_id)
    -> group_by('item_id')
    -> group_by('line')
    -> order_by('line ASC');

    if($item_id > 0 && $line > 0 ) {
        $this->db->where('item_id', $item_id)
        ->where('line', $line);
    }

    $query = $this->db->get();
    $result_tmp = $query->result_array();

    if(!empty($result_tmp)) {
        foreach($result_tmp as $item)
            $result['i-'.$item['item_id'].'-'.$item['line']] = $item;
    }

    $this->db->flush_cache();

    $this->db -> select("contract_id, item_kit_id, line, SUM(quantity) AS s_quantity")
    -> from('contract_delivery_item_kits')
    -> where('contract_id', $contract_id)
    -> group_by('item_kit_id')
    -> group_by('line')
    -> order_by('line ASC');

    if($item_kit_id > 0 && $line > 0 ) {
        $this->db->where('item_kit_id', $item_id)
        ->where('line', $line);
    }

    $query = $this->db->get();
    $result_tmp = $query->result_array();
    if(!empty($result_tmp)) {
        foreach($result_tmp as $item) {
            $result['k-'.$item['item_kit_id'].'-'.$item['line']] = $item;
        }
    }

    $this->db->flush_cache();

    return $result;
}

function items_selectbox_contract_payment($contract_id, $options = null) {
    $this->db -> select("id, name")
    -> from('contract_payment')
    -> where('contract_id', $contract_id)
    -> order_by('id ASC');

    $query = $this->db->get();
    $result_tmp = $query->result_array();
    $result[-1] = 'Chọn giai đoạn';
    if(!empty($result_tmp)) {
        foreach($result_tmp as $val)
            $result[$val['id']] = $val['name'];
    }

    return $result;
}

function payment_price_total($payment_id) {
    $this->db -> select("SUM(price) AS sum_total")
    -> from('contract_payment_detail')
    -> where('contract_payment_id', $payment_id);

    $query = $this->db->get();
    $row = $query->row_array();
    return $row['sum_total'];
}

function get_contract_last_id() {
    $this->db -> select("id")
    -> from('contract')
    -> order_by('id', 'DESC')
    -> limit(1,0);

    $query = $this->db->get();
    $row = $query->row_array();
    if(!empty($row))
        $result = $row['id'] + 1;
    else
        $result = 1;

    return $result;
}

function get_item_by_sale($sale_id) {
    $this->db -> select("*")
    -> from('contract')
    -> where('sale_id', $sale_id)
    -> where('option', 'customer')
    -> order_by('id', 'DESC')
    -> limit(1,0);

    $query = $this->db->get();
    $row = $query->row_array();

    return $row;
}

function valid_contract_code($code, $option, $id) {
    $this->db -> select("id")
    -> from('contract')
    -> where('code LIKE \''.$code.'\'')
    -> where('option', $option);
                 // -> where('deleted' == 0);

    if($id > 0)
        $this->db->where('id != ' . $id);

    $query = $this->db->get();
    $row = $query->row_array();
    if(!empty($row))
        return false;
    else
        return true;
}

function valid_contract_name($name, $option, $id) {
    $this->db -> select("id")
    -> from('contract')
    -> where('name LIKE \''.$name.'\'')
    -> where('option', $option);
                  // -> where('deleted' == 0);

    if($id > 0)
        $this->db->where('id != ' . $id);

    $query = $this->db->get();
    $row = $query->row_array();
    if(!empty($row))
        return false;
    else
        return true;
}

function valid_contract_payment_name($name, $id, $contract_id) {
    $this->db -> select("id")
    -> from('contract_payment')
    -> where('name LIKE \''.$name.'\'')
    -> where('contract_id', $contract_id);

    if($id > 0)
        $this->db->where('id != ' . $id);

    $query = $this->db->get();
    $row = $query->row_array();
    if(!empty($row))
        return false;
    else
        return true;

}

function model_load_model($model_name)
{
    $CI =& get_instance();
    $CI->load->model($model_name);
    return $CI->$model_name;
}

function count_all() {
    return $this->db->count_all_results('contract');
}
function dem_so_hop_dong($option=null){

    if(!empty($option['status']))
    {
        $this->db->where('status', $option['status']);
    }
    $this->db->where('deleted', 0);
    return count($this->db->get('phppos_contract')->result_array());
}
function report_expenses($year='',$locations_id='',$options=''){
    $this->db->SELECT('location_id,expense_amount,expense_date');
    $this->db->from('expenses');
    $this->db->where("deleted",0);
    $this->db->where('expense_options',$options);
    $this->db->like('expense_date',$year,'after');
    $query = $this->db->get();
    $row = $query->result_array();
    $data =array();
    foreach ($row as $k => $val) {
        if(!array_key_exists($val['location_id'],$data)){
            $data[$val['location_id']] = $val['expense_amount'];
        }
        else{
            $data[$val['location_id']] += $val['expense_amount'];
        }
    }
    return $data;
}
function report_receiving($year='',$locations_id=''){
    $this->db->SELECT('*');
    $this->db->from('receivings');
    $this->db->join('receivings_items','receivings.receiving_id = receivings_items.receiving_id');
    $this->db->where("receivings.deleted",0);
    $this->db->like('receiving_time',$year,'after');
    $query = $this->db->get();
    $row = $query->result_array();
    $data =array();
    foreach ($row as $k => $val) {
        if(!array_key_exists($val['location_id'],$data)){
            $data[$val['location_id']] = $val['item_unit_price'];
        }
        else{
            $data[$val['location_id']] += $val['item_unit_price'];
        }
    }
    // return $this->db->last_query();

    return $data;
}
function report_contracts($year='',$locations_id=''){
    $this->db->SELECT("contract.locations_id,contract_payment.date_payment,contract_payment.price,contract_payment.payment_price,contract_payment.percent,contract_payment.vat");      
    $this->db->like('phppos_contract_payment.date_payment',$year,'after');
    $this->db->join("contract_payment","contract_payment.contract_id=contract.id");
    $this->db->from("contract");
    if(!empty($locations_id))
        $this->db->where('locations_id',$locations_id);
    $this->db->group_start();
    $this->db->where('c_status','liquidated');
    $this->db->or_where('c_status','done');   
    $this->db->group_end();
    $query = $this->db->get();
    $row = ($query)?$query->result_array():array();
    $data = array();        
    foreach ($row as $k => $val) {
        $month = date("m",strtotime($val['date_payment']));
        if($val['vat']=='published')
            $val['price'] = $val['price']/1.1;
        else 
            $val['price'] = $val['price'];
        if(!array_key_exists($val['locations_id'],$data)){
            $data[$val['locations_id']]['total']= (double) $val['price'];
            $data[$val['locations_id']]['5']= (double) $val['price'];
            $data[$val['locations_id']][1]=0;
            $data[$val['locations_id']][2]=0;
            $data[$val['locations_id']][3]=0;
            $data[$val['locations_id']][4]=0;
            if($month>0&&$month<4)
                $data[$val['locations_id']][1]+=$val['price'];
            if($month>3&&$month<7)
                $data[$val['locations_id']][2]+=$val['price'];
            if($month>6&&$month<10)
                $data[$val['locations_id']][3]+=$val['price'];
            if($month>9&&$month<=12)
                $data[$val['locations_id']][4]+=$val['price'];
        }
        else{
            $data[$val['locations_id']]['total']+= (double) $val['price'];
            $data[$val['locations_id']]['5']+= (double) $val['price'];
            if($month>0&&$month<4)
                $data[$val['locations_id']][1]+=$val['price'];
            if($month>3&&$month<7)
                $data[$val['locations_id']][2]+=$val['price'];
            if($month>6&&$month<10)
                $data[$val['locations_id']][3]+=$val['price'];
            if($month>9&&$month<=12)
                $data[$val['locations_id']][4]+=$val['price'];
        }

    }
    return $data;

}
function report_contracts_kpi($year='',$locations_id=''){
    $this->db->SELECT("items.category_id,contract.locations_id,phppos_contract_payment.date_payment,contract_payment.price,contract_payment.payment_price,contract_payment.percent,contract_payment.vat");      
    $this->db->like('phppos_contract_payment.date_payment',$year,'after');
    $this->db->join("contract_payment","contract_payment.contract_id=contract.id");
    $this->db->join("items","phppos_items.item_id=contract.item_id");
    $this->db->from("contract");
    if(!empty($locations_id))
        $this->db->where('locations_id',$locations_id);
    $this->db->group_start();
    $this->db->where('c_status','liquidated');
    $this->db->or_where('c_status','done');   
    $this->db->group_end();
    $query = $this->db->get();
    $row = ($query)?$query->result_array():array();
        // var_dump($row);
    // echo $this->db->last_query();exit();
    $data = array();        
    foreach ($row as $k => $val) {
        $month = date("m",strtotime($val['date_payment']));
        if($val['vat']=='published'){
            if(!empty($val['price']))
                $val['price'] = $val['price']/1.1;
            else $val['price']=0;
        }
        elseif(empty($val['price'])) $val['price']=0;

        if($val['category_id']==1){
            if(!array_key_exists($val['locations_id'],$data)){
                $data[$val['locations_id']][1]['total'] += $val['price'];
                if($month>0&&$month<4)
                    $data[$val['locations_id']][1][1]+=$val['price'];
                if($month>3&&$month<7)
                    $data[$val['locations_id']][1][2]+=$val['price'];
                if($month>6&&$month<10)
                    $data[$val['locations_id']][1][3]+=$val['price'];
                if($month>9&&$month<=12)
                   $data[$val['locations_id']][1][4]+=$val['price'];
           }
           else{
            $data[$val['locations_id']][1]['total'] += $val['price'];
            if($month>0&&$month<4)
                $data[$val['locations_id']][1][1]+=$val['price'];
            if($month>3&&$month<7)
                $data[$val['locations_id']][1][2]+=$val['price'];
            if($month>6&&$month<10)
                $data[$val['locations_id']][1][3]+=$val['price'];
            if($month>9&&$month<=12)
               $data[$val['locations_id']][1][4]+=$val['price'];
       }
   }
   elseif($val['category_id']==2){
    if(!array_key_exists($val['locations_id'],$data)){
        $data[$val['locations_id']][2]['total'] = $val['price'];
        if($month>0&&$month<4)
            $data[$val['locations_id']][2][1]=$val['price'];
        if($month>3&&$month<7)
            $data[$val['locations_id']][2][2]=$val['price'];
        if($month>6&&$month<10)
            $data[$val['locations_id']][2][3]=$val['price'];
        if($month>9&&$month<=12)
           $data[$val['locations_id']][2][4]=$val['price'];
   }
   else{
    $data[$val['locations_id']][2]['total'] += $val['price'];
    if($month>0&&$month<4)
        $data[$val['locations_id']][2][1]+=$val['price'];
    if($month>3&&$month<7)
        $data[$val['locations_id']][2][2]+=$val['price'];
    if($month>6&&$month<10)
        $data[$val['locations_id']][2][3]+=$val['price'];
    if($month>9&&$month<=12)
       $data[$val['locations_id']][2][4]+=$val['price'];
}
}
elseif($val['category_id']==7){
    if(!array_key_exists($val['locations_id'],$data)){
        $data[$val['locations_id']][7]['total'] = $val['price'];
        if($month>0&&$month<4)
            $data[$val['locations_id']][7][1]=$val['price'];
        if($month>3&&$month<7)
            $data[$val['locations_id']][7][2]=$val['price'];
        if($month>6&&$month<10)
            $data[$val['locations_id']][7][3]=$val['price'];
        if($month>9&&$month<=12)
           $data[$val['locations_id']][7][4]=$val['price'];
   }
   else{
    $data[$val['locations_id']][7]['total'] += $val['price'];
    if($month>0&&$month<4)
        $data[$val['locations_id']][7][1]+=$val['price'];
    if($month>3&&$month<7)
        $data[$val['locations_id']][7][2]+=$val['price'];
    if($month>6&&$month<10)
        $data[$val['locations_id']][7][3]+=$val['price'];
    if($month>9&&$month<=12)
       $data[$val['locations_id']][7][4]+=$val['price'];
}
}
else{
    if(!array_key_exists($val['locations_id'],$data)){
        // if(empty($val['price']))
        $data[$val['locations_id']][0]['total'] += $val['price'];
        if($month>0&&$month<4){
             // if(empty($val['price']))
            $data[$val['locations_id']][0][1]+=$val['price'];
        }
        if($month>3&&$month<7){
             // if(empty($val['price']))
            $data[$val['locations_id']][0][2]+=$val['price'];
        }
        if($month>6&&$month<10){
             // if(empty($val['price']))
            $data[$val['locations_id']][0][3]+=$val['price'];
        }
        if($month>9&&$month<=12){
             // if(empty($val['price']))
           $data[$val['locations_id']][0][4]+=$val['price'];
       }
   }
   else{
    $data[$val['locations_id']][0]['total'] += $val['price'];
    if($month>0&&$month<4){
        if(!empty($val['price']))
            $data[$val['locations_id']][0][1]+=$val['price'];
    }
    if($month>3&&$month<7){
        if(!empty($val['price']))
            $data[$val['locations_id']][0][2]+=$val['price'];

    }
    if($month>6&&$month<10){
        if(!empty($val['price']))
            $data[$val['locations_id']][0][3]+=$val['price'];
    }
    if($month>9&&$month<=12){
        if(!empty($val['price']))
           $data[$val['locations_id']][0][4]+=$val['price'];
   }
}
}
}
return $data;
}
function kpi_report_contracts($year='',$locations_id=''){
    $this->db->SELECT("items.category_id,contract.locations_id,phppos_contract_payment.date_payment,contract_payment.price,contract_payment.payment_price,contract_payment.percent,contract_payment.vat");      
    $this->db->like('phppos_contract_payment.date_payment',$year,'after');
    $this->db->join("contract_payment","contract_payment.contract_id=contract.id");
    $this->db->join("items","phppos_items.item_id=contract.item_id");
    $this->db->from("contract");
    if(!empty($locations_id))
        $this->db->where('locations_id',$locations_id);
    $this->db->group_start();
    $this->db->where('c_status','liquidated');
    $this->db->or_where('c_status','done');   
    $this->db->group_end();
    $query = $this->db->get();
    $row = ($query)?$query->result_array():array();
        // var_dump($row);
    // echo $this->db->last_query();exit();
    $data = array();        
    foreach ($row as $k => $val) {
        $month = date("m",strtotime($val['date_payment']));
        if($val['vat']=='published'){
            if(!empty($val['price']))
                $val['price'] = $val['price']/1.1;
            else $val['price']=0;
        }
        elseif(empty($val['price'])) $val['price']=0;

        if($val['category_id']==1){
            if(!array_key_exists($val['locations_id'],$data)){
                $data[$val['locations_id']][1]['total'] += $val['price'];
                $data[$val['locations_id']][1][5] += $val['price'];
                if($month>0&&$month<4)
                    $data[$val['locations_id']][1][1]+=$val['price'];
                if($month>3&&$month<7)
                    $data[$val['locations_id']][1][2]+=$val['price'];
                if($month>6&&$month<10)
                    $data[$val['locations_id']][1][3]+=$val['price'];
                if($month>9&&$month<=12)
                 $data[$val['locations_id']][1][4]+=$val['price'];
         }
         else{
            $data[$val['locations_id']][1]['total'] += $val['price'];
            $data[$val['locations_id']][1][5] += $val['price'];
            if($month>0&&$month<4)
                $data[$val['locations_id']][1][1]+=$val['price'];
            if($month>3&&$month<7)
                $data[$val['locations_id']][1][2]+=$val['price'];
            if($month>6&&$month<10)
                $data[$val['locations_id']][1][3]+=$val['price'];
            if($month>9&&$month<=12)
             $data[$val['locations_id']][1][4]+=$val['price'];
     }
 }
 elseif($val['category_id']==2){
    if(!array_key_exists($val['locations_id'],$data)){
        $data[$val['locations_id']][2]['total'] = $val['price'];
        $data[$val['locations_id']][2][5] = $val['price'];
        if($month>0&&$month<4)
            $data[$val['locations_id']][2][1]=$val['price'];
        if($month>3&&$month<7)
            $data[$val['locations_id']][2][2]=$val['price'];
        if($month>6&&$month<10)
            $data[$val['locations_id']][2][3]=$val['price'];
        if($month>9&&$month<=12)
         $data[$val['locations_id']][2][4]=$val['price'];
 }
 else{
    $data[$val['locations_id']][2]['total'] += $val['price'];
    $data[$val['locations_id']][2][5] += $val['price'];
    if($month>0&&$month<4)
        $data[$val['locations_id']][2][1]+=$val['price'];
    if($month>3&&$month<7)
        $data[$val['locations_id']][2][2]+=$val['price'];
    if($month>6&&$month<10)
        $data[$val['locations_id']][2][3]+=$val['price'];
    if($month>9&&$month<=12)
     $data[$val['locations_id']][2][4]+=$val['price'];
}
}
elseif($val['category_id']==7){
    if(!array_key_exists($val['locations_id'],$data)){
        $data[$val['locations_id']][7]['total'] = $val['price'];
        $data[$val['locations_id']][7][5] = $val['price'];
        if($month>0&&$month<4)
            $data[$val['locations_id']][7][1]=$val['price'];
        if($month>3&&$month<7)
            $data[$val['locations_id']][7][2]=$val['price'];
        if($month>6&&$month<10)
            $data[$val['locations_id']][7][3]=$val['price'];
        if($month>9&&$month<=12)
         $data[$val['locations_id']][7][4]=$val['price'];
 }
 else{
    $data[$val['locations_id']][7]['total'] += $val['price'];
    $data[$val['locations_id']][7][5] += $val['price'];
    if($month>0&&$month<4)
        $data[$val['locations_id']][7][1]+=$val['price'];
    if($month>3&&$month<7)
        $data[$val['locations_id']][7][2]+=$val['price'];
    if($month>6&&$month<10)
        $data[$val['locations_id']][7][3]+=$val['price'];
    if($month>9&&$month<=12)
     $data[$val['locations_id']][7][4]+=$val['price'];
}
}
else{
    if(!array_key_exists($val['locations_id'],$data)){
        $data[$val['locations_id']][0][1] = 0;
        $data[$val['locations_id']][0][2] = 0;
        $data[$val['locations_id']][0][3] = 0;
        $data[$val['locations_id']][0][4] = 0;
        $data[$val['locations_id']][0][5] = 0;
        if(!empty($val['price']))
            $data[$val['locations_id']][0]['total'] = $val['price'];
        $data[$val['locations_id']][0][5] = $val['price'];
        if($month>0&&$month<4){
           if(!empty($val['price']))
            $data[$val['locations_id']][0][1]=$val['price'];
    }
    if($month>3&&$month<7){
       if(!empty($val['price']))
        $data[$val['locations_id']][0][2]=$val['price'];
}
if($month>6&&$month<10){
   if(!empty($val['price']))
    $data[$val['locations_id']][0][3]=$val['price'];
}
if($month>9&&$month<=12){
   if(!empty($val['price']))
     $data[$val['locations_id']][0][4]=$val['price'];
}
}
else{
    if(empty($val['price']))  $val['price'] =0 ;
    $data[$val['locations_id']][0]['total'] += $val['price'];
    $data[$val['locations_id']][0][5] += $val['price'];
    if($month>0&&$month<4){
        if(!empty($val['price']))
            $data[$val['locations_id']][0][1]+=$val['price'];
    }
    if($month>3&&$month<7){
        if(!empty($val['price']))
            $data[$val['locations_id']][0][2]+=$val['price'];

    }
    if($month>6&&$month<10){
        if(!empty($val['price']))
            $data[$val['locations_id']][0][3]+=$val['price'];
    }
    if($month>9&&$month<=12){
        if(!empty($val['price']))
         $data[$val['locations_id']][0][4]+=$val['price'];
 }
}
}
}
return $data;
}
#Lấy danh sách bên thứ 3 theo contract id
function get_supplier_by_contract($id)
{
    $this->db->select('phppos_sales.sale_id,phppos_contract.code,phppos_items.cost_price_interval,phppos_suppliers.company_name,phppos_receivings.supplier_id,phppos_receivings.task_id, phppos_contract.name,phppos_receivings_items.item_cost_price,phppos_receivings_items.item_unit_price');
    $this->db->from('phppos_contract');
    $this->db->join('phppos_sales', 'phppos_sales.sale_id = phppos_contract.sale_id', 'left');
    $this->db->join('phppos_receivings', 'phppos_sales.task_id = phppos_receivings.task_id', 'left');
    $this->db->join('phppos_suppliers', 'phppos_receivings.supplier_id = phppos_suppliers.person_id', 'left');
    $this->db->join('phppos_receivings_items', 'phppos_receivings_items.receiving_id = phppos_receivings.receiving_id', 'left');
    $this->db->join('phppos_items', 'phppos_receivings_items.item_id = phppos_items.item_id', 'left');
    $this->db->where('phppos_contract.id', $id);
    return $this->db->get()->result_array();
}

# Lấy giá trị hợp đồng / Hợp đồng đã thanh lý nghiệm thu
    #$status =array('done','liquidated')

function get_contract_value($status=null,$option=null,$arrParam=null)
{
    $id_location = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id())->location_id;
    $code_location = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id())->code;
    // var_dump($id_location);
    // var_dump($this->session->userdata['employee_current_location_id']);
    // die();
    $this->db->select('phppos_contract.id, phppos_contract.code,
        phppos_contract.name as name_contract,
        phppos_contract.date_signing,
        phppos_contract.status as trang_thai_hop_dong,
        SUM(phppos_contract_payment.price) as total_value,
        phppos_contract_payment.c_status,
        SUM(IF(phppos_contract_payment.vat="published",
        (phppos_contract_payment.price / 1.1),phppos_contract_payment.price)) as co_vat,phppos_contract_payment.vat');
    if($option=='vat')
    {
        $this->db->select('SUM(IF(phppos_contract_payment.vat="published",(phppos_contract_payment.price/1.1),phppos_contract_payment.price)) as has_vat');
    }



    $this->db->from('phppos_contract');
    $this->db->join('phppos_contract_payment', 'phppos_contract.id = phppos_contract_payment.contract_id','left');
    $this->db->group_by('phppos_contract.id');

    // trang thai giai doan cu hop dong
    if(!empty($status))
    {
        $this->db->where_in('phppos_contract_payment.c_status',$status);
        // $this->db->group_by('phppos_contract_payment.id');
    }

    

    // sap xep danh sach
    if(!empty($arrParam['order']))
    {
        $this->db->order_by($arrParam['order']['name'], $arrParam['order']['order_by']);
    }
    // lay ra so luong cu the
    if(!empty($arrParam['limit']))
    {
        $this->db->limit($arrParam['limit']['limit'],$arrParam['limit']['offset']);
    }

    // lay thong tin hop dong thanh ly theo thang/nam
    if (!empty($arrParam['month']) && !empty($arrParam['year'])){
        $tmp = $arrParam['year'].'-'.$arrParam['month'];
        $sumDay = date('t',strtotime($tmp));
        $month = $arrParam['month'];
        $year = $arrParam['year'];
        
        $this->db->select('CONCAT(p.first_name," ",p.last_name) as ten_doi_tac,
            phppos_contract.modified as ngay_ky_thanh_ly,
            phppos_contract_payment.name as ten_giai_doan,phppos_contract.item_id')
        ->join('phppos_sales as s','s.sale_id = phppos_contract.sale_id','left')
        ->join('phppos_people as p','p.person_id = s.customer_id','left');
        // truong hop lay theo ngay dang ky hop dong
        $this->db->where('s.location_id',$id_location);
        $this->db->where('s.deleted',0);
        if (!empty($arrParam['date_signing'])) {
            $this->db->where("month(phppos_contract.date_signing)",$month);
            $this->db->where('year(phppos_contract.date_signing)',$year);
            
        }
        if (!empty($arrParam['task'])) {
            $this->db->select('t.name as name_task, st.item_name as ten_dv, t.id as task_id');
            $this->db->join('phppos_tasks as t','t.id = s.task_id','left');
            $this->db->join('phppos_sales_items as st','s.sale_id = st.sale_id','left');
        }
        // lay hop dong đã hết hạn trong tháng
        if (!empty($arrParam['date_expiration'])) {
            $this->db->select('phppos_contract.date_expiration as ngay_het_han');
            $this->db->where("month(phppos_contract.date_expiration)='".$month."' AND year(phppos_contract.date_expiration) = '".$year."' AND day(phppos_contract.date_expiration)=".$sumDay);
        }
        // dang thuc hien
        if (!empty($arrParam['progress'])) {
            $date = date_format(date_create($year.'-'.$month),"Y-m-d");
            $this->db->where('(phppos_contract.date_signing)<',$date);
            $this->db->where_in('phppos_contract.status',array('progress','done'));
        }

        if (!empty($arrParam['date_payment'])) {
            $this->db->where("month(phppos_contract_payment.date_payment) ='".$month."' AND year(phppos_contract_payment.date_payment) = '".$year."'");
        }
    }
    if (!empty($arrParam['name_service'])) {
        $this->db->join('phppos_sales_items st','st.sale_id =s.sale_id','left');
        $this->db->join('phppos_items item','st.item_id =item.item_id','left');
        $this->db->where_in('item.category_id',$arrParam['name_service']);
    }



    if(!empty($arrParam['item_id'])){
        $this->db->where_in('phppos_contract.item_id',$arrParam['item_id']);
    }
    // lay thong tin hop dong theo khu vuc
    if (!empty($arrParam['task_id']) && !empty($arrParam['code_location'])) {
        $this->db->join('phppos_sales as s','s.sale_id = phppos_contract.sale_id','left');
        $this->db->join('phppos_locations lc', 'lc.location_id = s.location_id', 'left');
        $this->db->where_in('s.task_id',$arrParam['task_id']);
        $this->db->where('lc.code', $arrParam['code_location']);
    }
    // lay bao cao kpi danh gia ca nhan


    return $this->db->get()->result_array();
}


function get_contract_d7($arrParam=null)
{
    $this->db->select('s.sale_id, s.task_id, tu.user_id, tu.is_implement, tu.is_join, ct.id, ct.name, ct.code')->from('phppos_sales s')->join('phppos_task_user_relations tu','s.task_id = tu.task_id')->join('phppos_contract ct','ct.sale_id = s.sale_id')->join('phppos_task_kpiperson_approve ap','s.task_id = ap.task_id')->where('(tu.is_implement=1 OR tu.is_join=1)');

    if (!empty($arrParam['location_id'])) {
        $this->db->where('s.location_id',$arrParam['location_id']);
    }

    if (!empty($arrParam['kpi'])) {
        $this->db->where('ap.history',1);
    }

    if (!empty($arrParam['group_by'])) {
        $this->db->group_by('s.sale_id');
    }
    return $this->db->get()->result_array();
}

function contract_location($arrParam)
{
    $this->db->select('ct.id, ct.code, t.name as name_task, t.date_end,
        ct.name as name_contract,
        ct.date_signing, ct.date_expiration,
        ct.status as trang_thai_hop_dong,
        SUM(pm.price) as total_value,
        SUM(IF(pm.vat="published",
        (pm.price / 1.1),pm.price)) as co_vat,pm.vat, lc.code,st.item_name');
    $this->db->from('phppos_contract ct');
    $this->db->join('phppos_contract_payment pm', 'ct.id = pm.contract_id','left');
    $this->db->join('phppos_sales as s','s.sale_id = ct.sale_id','left');
    $this->db->join('phppos_tasks t', 't.id = s.task_id', 'left');
    $this->db->join('phppos_locations lc','lc.location_id = s.location_id','left');
    $this->db->join('phppos_sales_items st','st.sale_id = s.sale_id','left');

    // !empty($arrParam['month'] && !empty($arrParam['year']))
    if (!empty($arrParam['check'])) {
        $check = $arrParam['check'];
        if (empty($arrParam['date_payment'])) {
            if ($check=='THANG') {
                $this->db->where('month(ct.date_signing)',$arrParam['month']);
                $this->db->where('year(ct.date_signing)',$arrParam['year']);
            }elseif ($check=='QUY') {
                switch ($arrParam['month']) {
                    case 1:
                    $month_start = 1;
                    $month_end = 3;
                    break;
                    case 2:
                    $month_start = 4;
                    $month_end = 6;
                    break;
                    case 3:
                    $month_start = 7;
                    $month_end = 9;
                    break;
                    case 4:
                    $month_start = 10;
                    $month_end = 12;
                    break;

                }
                $year = $arrParam['year'];
                $this->db->where('month(ct.date_signing)>=',$month_start);
                $this->db->where('month(ct.date_signing) <=',$month_end);
                $this->db->where('year(ct.date_signing)',$year);
            }elseif ($check=='NAM') {
                $this->db->where('year(ct.date_signing)',$arrParam['year']);
            }elseif ($check=='TD') {
                $date_start = date('Y-m-d',strtotime($arrParam['date_start']));
                $date_end = date('Y-m-d',strtotime($arrParam['date_end']));
                $this->db->where('ct.date_signing>=',$date_start);
                $this->db->where('ct.date_signing <=',$date_end);
            }
        }else{
            if ($check=='THANG') {
                $this->db->where('month(pm.date_payment)',$arrParam['month']);
                $this->db->where('year(pm.date_payment)',$arrParam['year']);
            }elseif ($check=='QUY') {
                switch ($arrParam['month']) {
                    case 1:
                    $month_start = 1;
                    $month_end = 3;
                    break;
                    case 2:
                    $month_start = 4;
                    $month_end = 6;
                    break;
                    case 3:
                    $month_start = 7;
                    $month_end = 9;
                    break;
                    case 4:
                    $month_start = 10;
                    $month_end = 12;
                    break;

                }
                $year = $arrParam['year'];
                $this->db->where('month(pm.date_payment)>=',$month_start);
                $this->db->where('month(pm.date_payment) <=',$month_end);
                $this->db->where('year(pm.date_payment)',$year);
            }elseif ($check=='NAM') {
                $this->db->where('year(pm.date_payment)',$arrParam['year']);
            }elseif ($check=='TD') {
                $date_start = date('Y-m-d',strtotime($arrParam['date_start']));
                $date_end = date('Y-m-d',strtotime($arrParam['date_end']));
                $this->db->where('pm.date_payment>=',$date_start);
                $this->db->where('pm.date_payment <=',$date_end);
            }
        }
    }
    if (!empty($arrParam['date_end'])) {
        if ($arrParam['check_tp']=='THANG') {
            $this->db->where('month(t.date_end)',$arrParam['month']);
            $this->db->where('year(t.date_end)',$arrParam['year']);
        }elseif ($arrParam['check_tp']=='QUY') {
            switch ($arrParam['month']) {
                case 1:
                $month_start = 1;
                $month_end = 3;
                break;
                case 2:
                $month_start = 4;
                $month_end = 6;
                break;
                case 3:
                $month_start = 7;
                $month_end = 9;
                break;
                case 4:
                $month_start = 10;
                $month_end = 12;
                break;

            }
            $year = $arrParam['year'];
            $this->db->where('month(t.date_end)>=',$month_start);
            $this->db->where('month(t.date_end) <=',$month_end);
            $this->db->where('year(t.date_end)',$year);
        }elseif ($arrParam['check_tp']=='NAM') {
            $this->db->where('year(t.date_end)',$arrParam['year']);
        }elseif ($arrParam['check_tp']=='TD') {
            $date_start = date('Y-m-d',strtotime($arrParam['date_start']));
            $date_end = date('Y-m-d',strtotime($arrParam['date_end']));
            $this->db->where('t.date_end>=',$date_start);
            $this->db->where('t.date_end <=',$date_end);
        }
    }

    if (!empty($arrParam['ctstatus'])) {
        $this->db->where_in('ct.status', $arrParam['ctstatus']);
    }

    if (!empty($arrParam['c_status'])) {
        $this->db->where_in('pm.c_status', $arrParam['c_status']);
    }
    if (!empty($arrParam['location'])) {
        $this->db->where('lc.code',$arrParam['location']);
    }
    if (!empty($arrParam['id_location'])) {
        $this->db->where('s.location_id', $arrParam['id_location']);
    }

    $this->db->group_by('ct.id');
    return $this->db->get()->result_array();
}

function doanh_thu_hop_dong_kv($code_location,$task_id){
    // task_id array

}
function get_all_contract($arrParam=null){
    $this->db->select('ct.date_signing, ct.code, ct.name_contract, pp.first_name, pp.last_name')
    ->from('phppos_contract_payment pm')
    ->join('phppos_contract ct','ct.id = pm.contract_id')
    ->join('phppos_sales s','s.sale_id = ct.sale_id')
    ->join('phppos_people pp','s.customer_id = pp.person_id');
    if (!empty($arrParam['done'])) {
        $this->db->where('pm.c_status','done');
    }

}
// lấy số tiền của hợp đồng đã thu trong tháng
function tong_tien_da_thu_trong_thang($arrParam=null){
    $this->db->select('c.id,p.date_payment,p.name,p.price, p.vat, c.status, p.c_status')
    ->from('phppos_contract as c')
    ->join('phppos_contract_payment as p','c.id=p.contract_id');
    if (!empty($arrParam['month']) && !empty($arrParam['year'])) {
        $month =$arrParam['month'];
        $year = $arrParam['year'];
        $this->db->where("month(p.date_payment)=".$month." AND year(p.date_payment) = ".$year);
    }
    $this->db->where_in('p.c_status',array('done','liquidated'));

    return $this->db->get()->result_array();
}
// lay danh sach bên thứ 3
function ds_ben_thu_3($arrParam=null){
    $data = $this->db->select('c.id as contract_id,s.task_id,c.code, r.receiving_id,c.status as tt,c.date_signing, SUM(rt.item_unit_price) as chi_phi')
    ->from('phppos_receivings_items rt')
    ->join('phppos_receivings r','r.receiving_id= rt.receiving_id')
    ->join('phppos_sales s','s.task_id=r.task_id')
    ->join('phppos_contract c','c.sale_id = s.sale_id')
    ->group_by('c.id')
    ->get()
    ->result_array();
    return $data;
}
// tong tien thanh toan don tich
function tong_tien_thanh_toan_don_tich($arrParam=null)
{
    $this->db->select('c.id,p.date_payment,p.name,p.price, p.vat, c.status, p.c_status, p.id as payment_id')
    ->from('phppos_contract as c')
    ->join('phppos_contract_payment as p','c.id=p.contract_id');
    if (!empty($arrParam['month']) && !empty($arrParam['year'])) {
        $month =$arrParam['month'];
        $year = $arrParam['year'];
        $time = $arrParam['year'].'-'.$arrParam['month'];
        $this->db->where('month(p.date_payment) <=',$arrParam['month']);
        $this->db->where('year(p.date_payment) <=',$arrParam['year']);

        $this->db->where_in('p.c_status',array('done','liquidated'))
        ->where("c.id IN (SELECT pm.contract_id FROM phppos_contract_payment as pm where year(pm.date_payment)='$year' AND month(pm.date_payment)='$month')")
        ->group_by('p.id');
        return $this->db->get()->result_array();
    }
}
#lây tên khách hàng với code hợp đồng
function get_code_contract(){
    $this->db->select("contract.`code`,contract.id,people.last_name,people.person_id");
    $this->db->FROM("contract");
    $this->db->JOIN("sales","sales.sale_id=contract.sale_id");
    $this->db->join("customers","customers.person_id=sales.customer_id");
    $this->db->JOIN("people","people.person_id=customers.person_id");
    $this->db->where("contract.deleted",0);
    $query = $this->db->get();
    $row = $query->result_array();
    $data = array();
    foreach ($row as $k => $val) {
        $data[$val['id']]['code'] = $val['code'];
        $data[$val['id']]['last_name'] = $val['last_name'];
    }
    return $data;
}
function report_expenses_kpi($year='',$locations_id='',$options=''){
    $this->db->SELECT('*');
    $this->db->from('expenses');
    $this->db->where("deleted",0);
    $this->db->where('expense_options',$options);
    $query = $this->db->get();
    $row = $query->result_array();
    $data =array();
    foreach ($row as $k => $val) {
        $month = date("m",strtotime($val['expense_date'])); 
        if(!array_key_exists($val['location_id'],$data)){
            if($month>0&&$month<4)
                $data[$val['location_id']][1]=$val['expense_amount'];
            if($month>3&&$month<7)
                $data[$val['location_id']][2]=$val['expense_amount'];
            if($month>6&&$month<10)
                $data[$val['location_id']][3]=$val['expense_amount'];
            if($month>9&&$month<=12)
                $data[$val['location_id']][4]=$val['expense_amount'];

            $data[$val['location_id']][5]=$val['expense_amount'];

        }
        else{
            if($month>0&&$month<4)
                $data[$val['location_id']][1]+=$val['expense_amount'];
            if($month>3&&$month<7)
                $data[$val['location_id']][2]+=$val['expense_amount'];
            if($month>6&&$month<10)
                $data[$val['location_id']][3]+=$val['expense_amount'];
            if($month>9&&$month<=12)
                $data[$val['location_id']][4]+=$val['expense_amount'];

            $data[$val['location_id']][5]=+$val['expense_amount'];
        }
    }
    return $data;
}
function report_receiving_kpi($year='',$locations_id=''){
    $this->db->SELECT('*');
    $this->db->from('receivings');
    $this->db->join('receivings_items','receivings.receiving_id = receivings_items.receiving_id');
    $this->db->where("receivings.deleted",0);
    $this->db->like('receiving_time',$year,'after');
    // echo $this->db->last_query();
    $query = $this->db->get();
    $row = $query->result_array();
    $data =array();
    foreach ($row as $k => $val) {
        $month = date("m",strtotime($val['receiving_time'])); 
        if(!array_key_exists($val['location_id'],$data)){
            $data[$val['location_id']][1]=0;
            $data[$val['location_id']][2]=0;
            $data[$val['location_id']][3]=0;
            $data[$val['location_id']][4]=0;
            $data[$val['location_id']][5]=0;
            // $data[$val['location_id']] = $val['item_cost_price'];
            if($month>0&&$month<4)
                $data[$val['location_id']][1]=$val['item_unit_price'];
            if($month>3&&$month<7)
                $data[$val['location_id']][2]=$val['item_unit_price'];
            if($month>6&&$month<10)
                $data[$val['location_id']][3]=$val['item_unit_price'];
            if($month>9&&$month<13)
                $data[$val['location_id']][4]=$val['item_unit_price'];
        }
        else{
            if($month>0&&$month<4)
                $data[$val['location_id']][1]+=$val['item_unit_price'];
            if($month>3&&$month<7)
                $data[$val['location_id']][2]+=$val['item_unit_price'];
            if($month>6&&$month<10)
                $data[$val['location_id']][3]+=$val['item_unit_price'];
            if($month>9&&$month<=12)
                $data[$val['location_id']][4]+=$val['item_unit_price'];
        }
        $data[$val['location_id']][5] = $data[$val['location_id']][1]+$data[$val['location_id']][2]+$data[$val['location_id']][3]+$data[$val['location_id']][4];
    }

    return $data;
}


function get_all_name_service(){
    $categories = $this->Category->sort_categories_and_sub_categories($this->Category->get_all_categories_and_sub_categories());
    foreach ($categories as $key => $value) {
        $name = str_repeat('&nbsp;&nbsp;', $value['depth']) . $value['name'];
        $data['categories'][$key] = $name;
    }
    return $data['categories'];
}
// lấy thông tin 
function bao_cao_tai_chinh_dau_tu($arrParam=null,$item_id=null)
{
    $id_location = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id())->location_id;
    $this->db->select('a.item_id,a.id as contract_id, a.name as ten_hop_dong, i.name as ten_dv, a.date_signing,a.modified,a.status,a.date_expiration as ngay_het_han')
    ->from('phppos_contract a')
    ->join('phppos_items i','i.item_id=a.item_id');
    $time_current = $arrParam['year'].'-'.$arrParam['month'];
    $sumDay_current = date('t',strtotime($time_current));


    if (!empty($arrParam['month']) && !empty($arrParam['year'])) {
        // tu van phat hanh
        switch ((int)$arrParam['month']) {
            case 1:
            $time_before = ((int)$arrParam['year']-1).'-12';
            $sumDay_before = date('t',strtotime($time_before));
            $month_before =12;
            $year_before = ((int)$arrParam['year']-1);
            break;
            default:
            $time_before = $arrParam['year'].'-'.((int)$arrParam['month']-1);
            $sumDay_before =date('t',strtotime($time_before));
            $month_before =(int)$arrParam['month']-1;
            $year_before = (int)$arrParam['year'];
            break;
        }

        if (!empty($arrParam['date_signing'])) {
            $this->db->where('month(a.date_signing)',$arrParam['month']);
            $this->db->where('year(a.date_signing)',$arrParam['year']);
        }
        if (!empty($arrParam['ky_moi_thang_truoc'])) {
            $this->db->where('month(a.date_signing)',$month_before);
            $this->db->where('year(a.date_signing)',$year_before);
        }
        if (!empty($arrParam['status'])) {
            // hop dong da thanh ly trong thang
            if ($arrParam['status']=='progress') {
                $this->db->where('a.status','liquidated');
            }else{
                $this->db->join('phppos_contract_payment pm','pm.contract_id=a.id');
                $this->db->where('pm.c_status',$arrParam['status']);
                $this->db->where('month(pm.date_payment)',$arrParam['month']);
                $this->db->where('year(pm.date_payment)',$arrParam['year']);
            }
        }
    }


    // hop dong hieu luc cuoi ky
    if (!empty($arrParam['month-ex']) && !empty($arrParam['year-ex']) && !empty($arrParam['expiration'])) {
        $time = $arrParam['year-ex'].'-'.$arrParam['month-ex'];
        $sumDay = date('t',strtotime($time));
        $this->db->where('year(a.date_expiration)='.$arrParam['year-ex'].' AND month(a.date_expiration)='.$arrParam['month-ex'].' AND day(a.date_expiration)='.$sumDay);
    }
    if (!empty($arrParam['name_service'])) {
        $this->db->where_in('i.name',$arrParam['name_service']);
    }

    if (!empty($arrParam['bao_lanh_phat_hanh_chung_khoan'])) {
        $this->db->select('CONCAT(p.first_name," ",p.last_name) as ten_kh, a.code')
        ->join('phppos_sales s','s.sale_id=a.sale_id','left')
        ->join('phppos_people p','p.person_id=s.customer_id');
        if (!empty($arrParam['location'])) {
            $this->db->where('s.location_id', $id_location);
        }
    }
    // tru hop dong thanh ly
    if (!empty($arrParam['not_liquidated'])) {
        $this->db->where_not_in('a.status',array('liquidated'));
    }
    $this->db->group_by('a.id');
    return $this->db->get()->result_array();
}

function giai_doan_dang_thuc_hien($code_location,$arrParam=null){
    $this->db->select('t.name,t.id,t.project_id,t.date_end')
    ->from('phppos_sales s')
    ->join('phppos_tasks t','t.project_id = s.task_id')
    ->join('phppos_locations lc','s.location_id=lc.location_id')
    ->where('lc.code',$code_location)
    ->where('t.level',1)
    ->where('t.progress <',100);
    if (!empty($arrParam['date_end'])) {
        $this->db->where('t.date_end <', $arrParam['date_end']);
    }
    if (!empty($arrParam['date_start'])) {
        $this->db->where('t.date_end >=', $arrParam['date_start']);
    }
    return $this->db->get()->result_array();
}

function get_task_kpi_approve($arrParam = null){
    $location_id = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id())->location_id;

    $this->db->select('t.name, t.id as task_id, s.sale_id');
    $this->db->from('phppos_task_kpiperson_approve ap');
    $this->db->join('phppos_tasks t', 't.id = ap.task_id');
    $this->db->join('phppos_sales s', 's.task_id = t.id','left');
    $this->db->where('s.location_id', $location_id);
    $date_start = date('Y-m-d',strtotime($arrParam['date_s']));
    $date_end = date('Y-m-d',strtotime($arrParam['date_e']));
    $this->db->where('t.date_start >=',$date_start);
    $this->db->where('t.date_start <=',$date_end);
    $this->db->where('ap.history', 1);
    return $this->db->get()->result_array();
}

function bao_cao_ca_nhan($arrParam = null)
{
    $code_location = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id())->code;
    $location_id = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id())->location_id;
    // doanh thu ghi nhan
    if (!empty($arrParam['DTGN'])) {
        $this->db->select('s.sale_id,s.task_id, st.item_name as ten_dv, ct.id as contract_id, ct.status');
        $this->db->from('phppos_task_kpiperson_approve kpi');
        $this->db->join('phppos_sales s','s.task_id = kpi.task_id','left');
        $this->db->join('phppos_sales_items st','s.sale_id = st.sale_id','left');

        $this->db->select('t.name as name_task, ct.name as name_contract,t.date_end as dtask_end,SUM(IF(pm.vat="published",
            (pm.price / 1.1),pm.price)) as co_vat')
        ->join('phppos_tasks t','t.id=s.task_id','left')
        ->join('phppos_contract ct','ct.sale_id = s.sale_id','left')
        ->join('phppos_contract_payment pm','pm.contract_id = ct.id','left');

        if (!empty($arrParam['date_start']) && !empty($arrParam['date_end'])) {
            $date_start = date('Y-m-d',strtotime($arrParam['date_start']));
            $date_end = date('Y-m-d',strtotime($arrParam['date_end']));
            $this->db->where('t.date_end>=',$date_start);
            $this->db->where('t.date_end <=',$date_end);
        }
        // chi phi chia phong ban khac
        if (!empty($arrParam['PBK'])) {
            $this->db->select('SUM(ex.expense_amount) as chiphiPBK, SUM(rit.item_unit_price) as chiphibenthuba');
            $this->db->join('phppos_expenses ex', 'ct.id = ex.contract_id','left');
            $this->db->join('phppos_receivings rev', 'rev.task_id = s.task_id', 'left');
            $this->db->join('phppos_receivings_items rit', 'rit.receiving_id = rev.receiving_id', 'left');
            $this->db->where('ex.deleted', 0);
        }
        if (!empty($arrParam['TD'])) {
            $date_start = date('Y-m-d',strtotime($arrParam['date_s']));
            $date_end = date('Y-m-d',strtotime($arrParam['date_e']));
            $this->db->where('t.date_start >=',$date_start);
            $this->db->where('t.date_start <=',$date_end);
        }

        if (!empty($arrParam['kpi'])) {
            $this->db->where('kpi.history', 1);
        }
        // 
        if (!empty($arrParam['not_liquidated'])) {
            if (!empty($arrParam['check'])) {
                if ($arrParam['check']=='QUY') {
                    switch ($arrParam['month']) {
                        case 1:
                        $this->db->where('month(t.date_end)>=', 1);
                        $this->db->where('month(t.date_end)<=', 3);
                        break;
                        case 2:
                        $this->db->where('month(t.date_end)>=', 4);
                        $this->db->where('month(t.date_end)<=', 6);
                        break;
                        case 1:
                        $this->db->where('month(t.date_end)>=', 7);
                        $this->db->where('month(t.date_end)<=', 9);
                        break;
                        case 1:
                        $this->db->where('month(t.date_end)>=', 10);
                        $this->db->where('month(t.date_end)<=', 12);
                        break;
                    }
                }elseif ($arrParam['check']=='THANG') {
                    $this->db->where('month(t.date_end)',$arrParam['month']);
                }
            }
            $this->db->where('year(t.date_end)',$arrParam['year']);
            $this->db->where('pm.c_status !=', 'liquidated');
            $this->db->where('month(t.date_end)',$arrParam['month']);
        }
        
        // hop dong da nghiem thu
        if (!empty($arrParam['done'])) {
            $this->db->where('pm.c_status','done');
        }else{

            if (!empty($arrParam['progress'])) {
               $this->db->where('ct.status','progress');
           }else{
            $this->db->where_in('ct.status',array('done','liquidated'));
        }

    }



    $this->db->where('s.location_id',$location_id);
    $this->db->group_by('ct.id');
    return $this->db->get()->result_array();

}elseif(!empty($arrParam['check'])) {
    $this->db->select('s.sale_id,s.task_id, st.item_name as ten_dv')
    ->from('phppos_sales s')
    ->join('phppos_sales_items st','s.sale_id = st.sale_id');
    $this->db->select('t.id as task_id,t.name as name_task,
        ct.id as contract_id,
        ct.code,phppos_people.last_name as ten_kh, pm.date_payment,
        ct.name as name_contract,
        ct.status,
        ct.date_start as ct_date_start,
        ct.date_signing as ct_date_signing,
        t.trangthai as trangthai_da,
        t.modified as ngay_sua_tt, 
        t.progress, t.percent, t.pheduyet, t.date_start, t.date_end, t.date_finish, t.date_pheduyet')
    ->join('phppos_tasks as t','s.task_id = t.id')
    ->join('phppos_task_user_relations as tu','tu.task_id = t.id','left')
    ->join('phppos_contract ct','ct.project_id = t.id','left')
    ->join('phppos_contract_payment pm','pm.contract_id=ct.id','left');
    $this->db->join('phppos_people','s.customer_id=phppos_people.person_id','left');

    if (!empty($arrParam['employee_id'])) {
        $this->db->where('tu.user_id',$arrParam['employee_id']);
        $this->db->where('(tu.is_join =1 OR tu.is_implement =1)');
    }

    if (!empty($arrParam['location'])) {
        $this->db->where('s.location_id',$location_id);
    }
        // BAO CAO PHONG
    if ($arrParam['check']==='BCP') {
        if (!empty($arrParam['contract_completed'])) {
            if ($arrParam['check_tp']==='THANG') {
                $this->db->where("month(ct.date_signing)='".$arrParam['month']."' AND year(ct.date_signing)='".$arrParam['year']."'");
            }elseif ($arrParam['check_tp']==='QUY') {
                switch ($arrParam['month']) {
                    case 1:
                    $this->db->where("month(ct.date_signing)>='1' AND month(ct.date_signing)<='3' AND year(ct.date_signing)='".$arrParam['year']."'");
                    break;
                    case 2:
                    $this->db->where("month(ct.date_signing)>='4' AND month(ct.date_signing)<='6' AND year(ct.date_signing)='".$arrParam['year']."'");
                    break;
                    case 3:
                    $this->db->where("month(ct.date_signing)>='7' AND month(ct.date_signing)<='9' AND year(ct.date_signing)='".$arrParam['year']."'");
                    break;
                    case 4:
                    $this->db->where("month(ct.date_signing)>='10' AND month(ct.date_signing)<='12' AND year(ct.date_signing)='".$arrParam['year']."'");
                    break;
                }
            }else{
                $this->db->where('year(ct.date_signing)',$arrParam['year']);
            }

            $this->db->where_in('ct.status',array('done','liquidated','progress'));
            $this->db->group_by('ct.id');
        }
        else{
            if ($arrParam['check_tp']==='THANG') {
                $this->db->where("month(t.date_start)='".$arrParam['month']."' AND year(t.date_start)='".$arrParam['year']."'");
            }elseif ($arrParam['check_tp']==='QUY') {
                switch ($arrParam['month']) {
                    case 1:
                    $this->db->where("month(t.date_start)>='1' AND month(t.date_start)<='3' AND year(t.date_start)='".$arrParam['year']."'");
                    break;
                    case 2:
                    $this->db->where("month(t.date_start)>='4' AND month(t.date_start)<='6' AND year(t.date_start)='".$arrParam['year']."'");
                    break;
                    case 3:
                    $this->db->where("month(t.date_start)>='7' AND month(t.date_start)<='9' AND year(t.date_start)='".$arrParam['year']."'");
                    break;
                    case 4:
                    $this->db->where("month(t.date_start)>='10' AND month(t.date_start)<='12' AND year(t.date_start)='".$arrParam['year']."'");
                    break;
                }
            }else{
                $this->db->where('year(t.date_start)',$arrParam['year']);
            }
        }



        // lay theo ngay bat dau
        if (!empty($arrParam['date_st'])) {
            if ($arrParam['check']==='QUY') {
                switch ($arrParam['month']) {
                    case 1:
                    $date = date_create($arrParam['year'].'-03');
                    break;
                    case 2:
                    $date = date_create($arrParam['year'].'-06');
                    break;
                    case 3:
                    $date = date_create($arrParam['year'].'-09');
                    break;
                    case 4:
                    $date = date_create($arrParam['year'].'-12');
                    break;
                }
                $date_fm = date_format($date,"Y-m-t");
                $this->db->where('t.date_start <=',$date_fm);

            }elseif ($arrParam['check']=='TD') {
                $date_start = date('Y-m-d',strtotime($arrParam['date_start']));
                $date_end = date('Y-m-d',strtotime($arrParam['date_end']));

                $this->db->where('t.date_start <=',$date_end);

            }elseif ($arrParam['check']=='NAM') {
                $this->db->where('year(t.date_start)<=',$arrParam['year']);
            }else{
                $date= date_create($arrParam['year'].'-'.$arrParam['month']);
                $date_fm = date_format($date,'Y-m-t');
                $this->db->where('t.date_start <=',$date_fm);
            }
        }

        // lay theo ngay ket thuc thuc te
        if (!empty($arrParam['date_finish'])) {
            if ($arrParam['check']==='QUY') {
                switch ($arrParam['month']) {
                    case 1:
                    $this->db->where("(month(t.date_finish)>='1' AND month(t.date_finish)<='3') AND year(t.date_finish)='".$arrParam['year']."'");
                    break;
                    case 2:
                    $this->db->where("(month(t.date_finish)>='4' AND month(t.date_finish)<='6') AND year(t.date_finish)='".$arrParam['year']."'");

                    break;
                    case 3:
                    $this->db->where("(month(t.date_finish)>='7' AND month(t.date_finish)<='9') AND year(t.date_finish)='".$arrParam['year']."'");

                    break;
                    case 4:
                    $this->db->where("(month(t.date_finish)>='10' AND month(t.date_finish)<='12') AND year(t.date_finish)='".$arrParam['year']."'");

                    break;
                }
            }elseif ($arrParam['check']=='TD') {
                $date_start = date('Y-m-d',strtotime($arrParam['date_start']));
                $date_end = date('Y-m-d',strtotime($arrParam['date_end']));

                $this->db->where('t.date_finish >',$date_start);
                $this->db->where('t.date_finish <',$date_end);

            }elseif ($arrParam['check']=='NAM') {
                $this->db->where('year(t.date_finish)',$arrParam['year']);
            }else{
                $this->db->where('month(t.date_finish)',$arrParam['month']);
                $this->db->where('year(t.date_finish)',$arrParam['year']);
            }
        }

                // 
        if (!empty($arrParam['hop_dong_lien_quan'])) {
            $this->db->where('tu.is_join=1 OR tu.is_implement =1');
            $this->db->group_by('ct.id');
        }

                // Đã hoàn thành
        if (!empty($arrParam['pheduyet'])) {
            $this->db->where('t.pheduyet',1);
        }
                // hoan thanh or chua hoan thanh dự án
        if (!empty($arrParam['hoanthanh'])) {
            $this->db->where('(t.pheduyet = 2 OR t.pheduyet =1)');
        }

        if (!empty($arrParam['dangthuchien'])) {
            $this->db->where('t.pheduyet !=',1);
        }

        if (!empty($arrParam['limit'])) {
            $this->db->limit(50);
            $this->db->order_by('t.name','ASC');
        }
    }
    $this->db->where('t.parent',0);
    $this->db->group_by('t.id');
    return $this->db->get()->result_array();
}
}

function cong_viec_dang_thuc_hien()
{
    return $this->db->select('*')->from('phppos_tasks')
    ->where('level>=',2)
    ->where('trangthai',1)
    ->get()
    ->result_array();
}
function thong_ke_cong_viec_qua_han($arrParam=null){
    $this->db->select('*')->from('phppos_tasks')
    ->where('level >=',2);
    if ($arrParam['check']==='THANG') {
        $this->db->where("month(date_start)='".$arrParam['month']."' AND year(date_start)='".$arrParam['year']."'");
    }elseif ($arrParam['check']==='QUY') {
        switch ($arrParam['month']) {
            case 1:
            $this->db->where("month(date_start)>='1' AND month(date_start)<='3' AND year(date_start)='".$arrParam['year']."'");
            break;
            case 2:
            $this->db->where("month(date_start)>='4' AND month(date_start)<='6' AND year(date_start)='".$arrParam['year']."'");
            break;
            case 3:
            $this->db->where("month(date_start)>='7' AND month(date_start)<='9' AND year(date_start)='".$arrParam['year']."'");
            break;
            case 4:
            $this->db->where("month(date_start)>='10' AND month(date_start)<='12' AND year(date_start)='".$arrParam['year']."'");
            break;
        }
    }else{
        $this->db->where('year(date_start)',$arrParam['year']);
    }
    if (!empty($arrParam['group_by'])) {
        $this->db->group_by('t.project_id');
    }
    $this->db->where('progress <',100);
    return $this->db->get()->result_array();
}
function get_ratio_employee_task(){
    return $this->db->get('phppos_task_kpiperson_approve')
    ->result_array();
}
// Cong viec thuc hien cham
function get_task_child_delay($arrParam = null){
    $id_location = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id())->location_id;
    $this->db->select('*')->from('phppos_tasks');
    $this->db->where('level >=',2);
    if (!empty($arrParam['group_by'])) {
        $this->db->group_by('project_id');
    }
    if (!empty($arrParam['project_id'])) {
        $this->db->where('project_id', $arrParam['project_id']);
    }
    if (!empty($arrParam['date_time'])) {
        if ($arrParam['check']==='THANG') {
            $this->db->where('month(date_start)', $arrParam['month']);
            $this->db->where('year(date_start)', $arrParam['year']);

        }elseif ($arrParam['check']==='QUY') {
            switch ($arrParam['month']) {
                case 1:
                $this->db->where("month(date_start)>='1' AND month(date_start)<='3' AND year(date_start)='".$arrParam['year']."'");
                break;
                case 2:
                $this->db->where("month(date_start)>='4' AND month(date_start)<='6' AND year(date_start)='".$arrParam['year']."'");
                break;
                case 3:
                $this->db->where("month(date_start)>='7' AND month(date_start)<='9' AND year(date_start)='".$arrParam['year']."'");
                break;
                case 4:
                $this->db->where("month(date_start)>='10' AND month(date_start)<='12' AND year(date_start)='".$arrParam['year']."'");
                break;
            }
        }else{
            $this->db->where('year(date_start)',$arrParam['year']);
        }
    }
    if (!empty($arrParam['location'])) {
        $this->db->join('phppos_sales', 'phppos_sales.task_id = tasks.project_id');
        $this->db->where('location_id',$id_location);
    }
    return $this->db->get()->result_array();
}

function get_point($diem=0){
    if ($diem==0) {
        return 0;
    }else{
        if ($diem<70 && $diem>0) {
            return 60;
        }elseif ($diem>=70 && $diem<90) {
            return 80;
        }elseif ($diem>=90 && $diem<110) {
            return 100;
        }elseif ($diem>=110 && $diem<130) {
            return 120;
        }else{
            return 140;
        }
    }

}
function showCategories($data, $parent_id, $char = '', $task_id=null)
{
    foreach ($data as $key => $value)
    {
        if ($value['parent'] == $parent_id)
        {
            if ($value['id']==$task_id) {
                echo '<option value="'.$task_id.'" selected>';
            }else{
                echo '<option value="'.$value['id'].'">';
            }
            echo $char . $value['name'];
            echo '</option>';
            unset($data[$key]);
            $this->showCategories($data, $value['id'], $char.'--',$task_id);
        }
    }
}


function task_not_revenue($arrParam = null){
    $this->db->select('*')->from('phppos_tasks_personal');
    $this->db->where('type',2);
    // $this->db->where('joins')
    // $this->db->where("implements LIKE '%".$arrParam['employee_id']."%'");
    // $this->db->or_where("joins LIKE '%".$arrParam['employee_id']."%'");
    if ($arrParam['check']=='THANG') {
        $this->db->where("month(date_start)='".$arrParam['month']."'  AND year(date_start)='".$arrParam['year']."'");
    }elseif ($arrParam['check']=='QUY') {
        switch ($arrParam['month']) {
            case 1:
            $this->db->where("month(date_start)>='1' AND month(date_start)<='3'");
            break;
            case 2:
            $this->db->where("month(date_start)>='4' AND month(date_start)<='6'");
            break;
            case 3:
            $this->db->where("month(date_start)>='7' AND month(date_start)<='9'");
            break;
            case 4:
            $this->db->where("month(date_start)>='10' AND month(date_start)<='12'");
            break;
            $this->db->where('year(date_start)', $arrParam['year']);
        }
    }elseif ($arrParam['check']=='NAM') {
        $this->db->where('year(date_start)',$arrParam['year']);
    }elseif ($arrParam['check']=='TD') {
        $date_start = date('Y-m-d',strtotime($arrParam['date_start']));
        $date_end = date('Y-m-d',strtotime($arrParam['date_end']));
        $this->db->where('date_start>=',$date_start);
        $this->db->where('date_start <=',$date_end);
    }

    $data =$this->db->get()->result_array();
    $arr_employee = array();
    foreach ($data as $key => $value) {
        $arr_employee1 = explode(',',$value['implements']);
        $arr_employee2 = explode(',',$value['join']);
        $arr_employee = array_unique(array_merge($arr_employee1,$arr_employee2));
        if (in_array($arrParam['employee_id'], $arr_employee)) {
            $dt[] = array('name'=>$value['name'],'date_end'=>$value['date_end'],'date_start'=>$value['date_start']);
        }
    }
    return $dt;
}

function get_task($project_id = array()){
    if (!empty($project_id)) {
        return $this->db->select('*')->from('phppos_tasks')
        ->where_in('id',$project_id)->get()->result_array();
    }else{
        return;
    }
    
}

function get_employee_join_tasks_contract(){
    $this->load->model('Kpi_Person');
    return $this->Kpi_Person->get_employee_join_tasks()->select('CONCAT(pp.first_name," ", pp.last_name) as tennhanvien')->where('(tu.is_join=1 OR tu.is_implement=1) AND t.parent =0')->get()->result_array();
}
function get_customer_task(){
    return $this->db->select("CONCAT(pp.first_name,' ',pp.last_name) as customer_name,s.sale_id, s.task_id")
    ->from('phppos_sales s')
    ->join('phppos_people pp','pp.person_id = s.customer_id')
    ->where('s.task_id !=',0)
    ->get()->result_array();
}

function get_employee_wage($v){
    $this->db->select("phppos_employees_locations.employee_id, phppos_employees_level.*,phppos_employees_locations.location_id");
    $this->db->FROM("phppos_employees");
    $this->db->join("phppos_employees_level","phppos_employees.level_id = phppos_employees_level.id");
    $this->db->join("phppos_people","phppos_employees.person_id = phppos_people.person_id");
    $this->db->join("phppos_employees_locations","phppos_employees_locations.employee_id=phppos_people.person_id");
    $row = $this->db->get()->result_array();
    $data = array();
    if($v==5)
        $v = 12;
    else $v = 3;
    foreach ($row as $k => $val) {
       if(!array_key_exists($val['location_id'],$data)){
        $data[$val['location_id']] = ($val['salary']*$v);
    }
    else{
       $data[$val['location_id']] += ($val['salary']*$v);
   }
}
return $data;
}

function count_contract(){
   $items = $this->list_item(array('option' => 'customer', 'location' => $this->_scopeOfView, 'type' => 'not-liquidated'));

   // $items = $this->list_item(array('option' => 'customer', 'location' => $this->_scopeOfView, 'type' => 'progress'));
        // echo "<pre>";
        // echo $this->db->last_query();die();
        // var_dump($items);die();
        # Giá tri hợp đồng

   return count($items);
}


}
?>