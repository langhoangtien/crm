<?php
require_once ("Report.php");
class Specific_customer extends Report
{
    protected $_mail_fields = array(
        'time' => 'm.time'
    );

    protected $_expenses_fields = array(
        'expense_date' => 'e.expense_date'
    );

    protected $_sale_fields = array(
        'sale_time' => 's.sale_time'
    );
		protected $_contracts_fields = array(
        'date_signing' => 'c.date_signing'
    );
	function __construct()
	{
		parent::__construct();
        $this->load->model('reports/Specific_customer_store_account');
        $this->load->model('Sale');
        $this->load->model('Item');
	}
	
	public function getDataColumns()
	{
		$return = array();
		
		$return['summary'] = array();
		$location_count = count(self::get_selected_location_ids());
			
		$return['summary'][] = array('data'=>lang('reports_sale_id'), 'align'=> 'left');
		if ($location_count > 1)
		{
			$return['summary'][] = array('data'=>lang('common_location'), 'align'=> 'left');
		}
		
		$return['summary'][] = array('data'=>lang('reports_date'), 'align'=> 'left');
		$return['summary'][] = array('data'=>lang('reports_register'), 'align'=> 'left');
		$return['summary'][] = array('data'=>lang('common_items_purchased'), 'align'=> 'left');
		$return['summary'][] = array('data'=>lang('reports_sold_by'), 'align'=> 'left');
		$return['summary'][] = array('data'=>lang('reports_subtotal'), 'align'=> 'right');
		$return['summary'][] = array('data'=>lang('reports_total'), 'align'=> 'right');
		$return['summary'][] = array('data'=>lang('common_tax'), 'align'=> 'right');
				
		if($this->Employee->has_module_action_permission('reports','show_profit',$this->Employee->get_logged_in_employee_info()->person_id))
		{
			$return['summary'][] = array('data'=>lang('common_profit'), 'align'=> 'right');
		}
		$return['summary'][] = array('data'=>lang('reports_payment_type'), 'align'=> 'right');
		$return['summary'][] = array('data'=>lang('reports_comments'), 'align'=> 'right');

		$return['details'] = array();
		$return['details'][] = array('data'=>lang('reports_name'), 'align'=> 'left');
		$return['details'][] = array('data'=>lang('reports_category'), 'align'=> 'left');
		$return['details'][] = array('data'=>lang('reports_serial_number'), 'align'=> 'left');
		$return['details'][] = array('data'=>lang('reports_description'), 'align'=> 'left');
		$return['details'][] = array('data'=>lang('reports_quantity_purchased'), 'align'=> 'left');
		$return['details'][] = array('data'=>lang('reports_subtotal'), 'align'=> 'right');
		$return['details'][] = array('data'=>lang('reports_total'), 'align'=> 'right');
		$return['details'][] = array('data'=>lang('common_tax'), 'align'=> 'right');
		if($this->Employee->has_module_action_permission('reports','show_profit',$this->Employee->get_logged_in_employee_info()->person_id))
		{
			$return['details'][] = array('data'=>lang('common_profit'), 'align'=> 'right');			
		}
		$return['details'][] = array('data'=>lang('common_discount'), 'align'=> 'right');			
		
		return $return;		
	}
	
	public function getData()
	{
		$data = array();
		$data['summary'] = array();
		$data['details'] = array();
		
		$this->db->select('locations.name as location_name, sale_id, sale_time, register_name, sale_date, sum(quantity_purchased) as items_purchased, CONCAT(sold_by_employee.first_name," ",sold_by_employee.last_name) as sold_by_employee, CONCAT(employee.first_name," ",employee.last_name) as employee_name, sum(subtotal) as subtotal, sum(total) as total, sum(tax) as tax, sum(profit) as profit, payment_type, comment', false);
		$this->db->from('sales_items_temp');
		$this->db->join('locations', 'sales_items_temp.location_id = locations.location_id');
		$this->db->join('people as employee', 'sales_items_temp.employee_id = employee.person_id');
		$this->db->join('people as sold_by_employee', 'sales_items_temp.sold_by_employee_id = sold_by_employee.person_id', 'left');
		$this->db->where('customer_id', $this->params['customer_id']);
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}
		$this->db->where('sales_items_temp.deleted', 0);
		$this->db->group_by('sale_id');
		$this->db->order_by('sale_time', ($this->config->item('report_sort_order')) ? $this->config->item('report_sort_order') : 'asc');
		
		//If we are exporting NOT exporting to excel make sure to use offset and limit
		if (isset($this->params['export_excel']) && !$this->params['export_excel'])
		{
			$this->db->limit($this->report_limit);
			$this->db->offset($this->params['offset']);
		}		
		
		foreach($this->db->get()->result_array() as $sale_summary_row)
		{
			$data['summary'][$sale_summary_row['sale_id']] = $sale_summary_row; 
		}
		
		$sale_ids = array();
		
		foreach($data['summary'] as $sale_row)
		{
			$sale_ids[] = $sale_row['sale_id'];
		}
				
		$this->db->select('sale_id, sale_time, sale_date, item_number, items.product_id as item_product_id,item_kits.product_id as item_kit_product_id, item_kit_number, items.name as item_name, item_kits.name as item_kit_name, sales_items_temp.category, quantity_purchased, serialnumber, sales_items_temp.description, subtotal,total, tax, profit, discount_percent', false);
		$this->db->from('sales_items_temp');
		$this->db->join('items', 'sales_items_temp.item_id = items.item_id', 'left');
		$this->db->join('item_kits', 'sales_items_temp.item_kit_id = item_kits.item_kit_id', 'left');
		
		if (!empty($sale_ids))
		{
			$sale_ids_chunk = array_chunk($sale_ids,25);
			$this->db->group_start();
			foreach($sale_ids_chunk as $sale_ids)
			{
				$this->db->or_where_in('sale_id', $sale_ids);
			}
			$this->db->group_end();
		}
		else
		{
			$this->db->where('1', '2', FALSE);		
		}
		
		foreach($this->db->get()->result_array() as $sale_item_row)
		{
			$data['details'][$sale_item_row['sale_id']][] = $sale_item_row;
		}
		
		return $data;
	}
	
	public function getTotalRows()
	{	
		$this->db->select("COUNT(DISTINCT(sale_id)) as sale_count");
		
		$this->db->from('sales_items_temp');
		$this->db->where('customer_id', $this->params['customer_id']);
		
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}
		$this->db->where('sales_items_temp.deleted', 0);		
		$ret = $this->db->get()->row_array();
		return $ret['sale_count'];
	}
	
	
	public function getSummaryData()
	{
		$this->db->select('sum(subtotal) as subtotal, sum(total) as total, sum(tax) as tax, sum(profit) as profit', false);
		$this->db->from('sales_items_temp');
		$this->db->where('customer_id', $this->params['customer_id']);
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}
		$this->db->where('deleted', 0);
		if ($this->config->item('hide_store_account_payments_from_report_totals'))
		{
			$this->db->where('store_account_payment', 0);
		}
		
		$this->db->group_by('sale_id');
		
		$return = array(
			'subtotal' => 0,
			'total' => 0,
			'tax' => 0,
			'profit' => 0,
		);
		
		foreach($this->db->get()->result_array() as $row)
		{
			$return['subtotal'] += to_currency_no_money($row['subtotal'],2);
			$return['total'] += to_currency_no_money($row['total'],2);
			$return['tax'] += to_currency_no_money($row['tax'],2);
			$return['profit'] += to_currency_no_money($row['profit'],2);
		}

		if(!$this->Employee->has_module_action_permission('reports','show_profit',$this->Employee->get_logged_in_employee_info()->person_id))
		{
			unset($return['profit']);
		}
		return $return;
	}

	function getHistoryEmailById($id) {
		$this->db->select('mh.id, mh.title, mh.person_id, mh.content, mh.time, p.first_name, p.last_name, p.phone_number, p.email, mh.employee_id, mh.status, mh.file')
                 ->from('mail_history as mh')
                 ->where('mh.id', $id)
                 ->join('people as p', 'p.person_id = mh.person_id', 'left');

        $query = $this->db->get();
    	return $query->result();
    }

    function getHistorySendEmailById($id) {
		$this->db->select('*')
	     ->from('people')
	     ->where('people.person_id', $id);

        $query = $this->db->get();
    	return $query->result();
    }

    function getHistorySmsById($id) {
		$this->db->select('sh.id, sh.title, sh.person_id, sh.content, sh.time, p.first_name, p.last_name, p.phone_number, p.email, sh.employee_id, sh.status')
	     ->from('sms_history as sh')
	     ->where('sh.id', $id)
	     ->join('people as p', 'p.person_id = sh.person_id', 'left');

        $query = $this->db->get();
    	return $query->result();
    }

    function get_total_list($arrParams = null, $options = null) {
        switch ($arrParams['option']) {
            case 'purchase':
                $result = $this->get_total_purchase($arrParams, $options);
                break;

            case 'mail':
                $result = $this->get_total_mail($arrParams, $options);
                break;
            case 'expenses':
                $result = $this->get_total_expenses($arrParams, $options);
                break;
            case 'balance':
                $result = $this->get_total_balance($arrParams, $options);
                break;
						case 'contract':
                $result = $this->get_total_contract($arrParams, $options);
                break;						
			case 'contract_suspended':
                $result = $this->get_total_contract_suspended($arrParams, $options);
                break;								
        }

        return $result;
    }

    function get_total_purchase($arrParams = null, $options = null) {
        // ttotal, tax
        $this->db -> select('SUM(ttotal) AS sum_ttotal, SUM(tax) AS sum_tax, SUM(tcost) AS sum_tcost')
                  -> from('sales_items_temp');

        $query = $this->db->get();

        $result_tmp = !empty($query) ? $query->row_array() : [];

        $this->db->flush_cache();

        if(!empty($result_tmp['sum_ttotal']))
            $ttotal = $result_tmp['sum_ttotal'];
        else
            $ttotal = 0;

        if(!empty($result_tmp['sum_tcost']))
            $tcost = $result_tmp['sum_tcost'];
        else
            $tcost = 0;

        if(!empty($result_tmp['sum_tax']))
            $tax = $result_tmp['sum_tax'];
        else
            $tax = 0;

        // commission
        $this->db -> select('SUM(commission) AS sum_commission')
                  -> from('sales_commission AS c')
                  -> join('sales AS s', 'c.sale_id = s.sale_id')
                  -> where('s.customer_id', $arrParams['customer_id'])
                  -> where('s.location_id IN ('.$arrParams['location_ids'].')')
                  -> where('s.commission_status', 1)
                  -> where('s.deleted', 0);

        if(!empty($arrParams['start_date'])) {
            $this->db->where('s.sale_time >= \''.$arrParams['start_date'].'\'');
        }

        if(!empty($arrParams['end_date'])) {
            $this->db->where('s.sale_time <= \''.$arrParams['end_date'].'\'');
        }

        $query = $this->db->get();

        $result_tmp = !empty($query) ? $query->row_array() : [];

        $this->db->flush_cache();

        if(!empty($result_tmp)) {
            $commission = $result_tmp['sum_commission'];
        }else {
            $commission = 0;
        }

        // point payment
        $this->db-> select("SUM(p.payment_amount) AS point_payment")
                 -> from('sales_payments AS p')
                 -> join('sales As s', 'p.sale_id = s.sale_id')
                 -> where("p.payment_type = 'Điểm'")
                 -> where('s.customer_id', $arrParams['customer_id'])
                 -> where('s.location_id IN ('.$arrParams['location_ids'].')')
                 -> where('s.deleted', 0);

        if(!empty($arrParams['start_date'])) {
            $this->db->where('s.sale_time >= \''.$arrParams['start_date'].'\'');
        }

        if(!empty($arrParams['end_date'])) {
            $this->db->where('s.sale_time <= \''.$arrParams['end_date'].'\'');
        }

        $query = $this->db->get();

        $result_tmp = !empty($query) ? $query->row_array() : [];

        $this->db->flush_cache();

        if(!empty($result_tmp)) {
            $point_payment = $result_tmp['point_payment'];
        }else {
            $point_payment = 0;
        }

        // gift card payment
        $this->db-> select("SUM(p.payment_amount) AS the_qua_tang")
                 -> from('sales_payments AS p')
                 -> where("p.payment_type LIKE '%quà%'")
                 -> join('sales As s', 'p.sale_id = s.sale_id')
                 -> where("p.payment_type = 'Điểm'")
                 -> where('s.customer_id', $arrParams['customer_id'])
                 -> where('s.location_id IN ('.$arrParams['location_ids'].')')
                 -> where('s.deleted', 0);

        if(!empty($arrParams['start_date'])) {
            $this->db->where('s.sale_time >= \''.$arrParams['start_date'].'\'');
        }

        if(!empty($arrParams['end_date'])) {
            $this->db->where('s.sale_time <= \''.$arrParams['end_date'].'\'');
        }

        $query = $this->db->get();

        $result_tmp = !empty($query) ? $query->row_array() : [];

        $this->db->flush_cache();

        if(!empty($result_tmp)) {
            $gift_card_payment = $result_tmp['the_qua_tang'];
        }else {
            $gift_card_payment = 0;
        }

        // thu + chi
        if(!empty($arrParams['start_date'])) {
            $time_condition[] = 's.sale_time >= \''.$arrParams['start_date'].'\'';
        }

        if(!empty($arrParams['end_date'])) {
            $time_condition[] = 's.sale_time <= \''.$arrParams['end_date'].'\'';
        }

        if(!empty($time_condition)) {
            $time_condition = ' AND '.implode(' AND ', $time_condition);
        }else
            $time_condition = '';

        $customer_id = $arrParams['customer_id'];
        $location_ids = $arrParams['location_ids'];

        $sql = "SELECT s.sale_id, SUM(IF(e.expense_type = 1, e.expense_amount + e.expense_tax, 0)) AS chi, SUM(IF(e.expense_type = -1, e.expense_amount + e.expense_tax, 0)) AS thu
                FROM phppos_expenses AS e
                INNER JOIN phppos_sales AS s ON e.sale_id = s.sale_id
                WHERE s.customer_id = $customer_id
                AND s.location_id IN ($location_ids)
                AND s.deleted = 0
                ";

        $query  = $this->db->query($sql);
        $result_tmp = $query->result_array();

        if(!empty($result_tmp['thu']))
            $thu = $result_tmp['thu'];
        else
            $thu = 0;

        if(!empty($result_tmp['chi']))
            $chi = $result_tmp['chi'];
        else
            $chi = 0;

        // order value
        $order_total = $ttotal + $thu + $tax;

        // profit
        $profit = $ttotal + $thu - $chi - $tcost - $commission - $gift_card_payment - $point_payment;

        $result = array(
            'specific_cus_purchase_order_value' => to_currency($order_total),
            'specific_cus_purchase_profit'      => to_currency($profit)
        );

        return $result;

    }

    function get_total_balance($arrParams = null, $options = null) {
        $model = $this->Specific_customer_store_account;
        $specific_cus_balance_start = $model->get_debt_start($arrParams);
        $specific_cus_balance_end   = $model->get_debt_end($arrParams);

        $result = array(
            'specific_cus_balance_start' => $specific_cus_balance_start,
            'specific_cus_balance_end'   => $specific_cus_balance_end
        );

        return $result;
    }

    function get_total_mail($arrParams = null, $options = null) {
        $result = array('specific_cus_mail_total'=>$this->count_mail($arrParams, $options));

        return $result;
    }

    function get_total_expenses($arrParams = null, $options = null) {
        $arrParams['expense_type'] = 1;
        $expense_chi_total = $this->get_expenses_total($arrParams);

        $arrParams['expense_type'] = -1;
        $expense_thu_total = $this->get_expenses_total($arrParams);

        $result = array(
            'specific_cus_expenses_thu_total' => $expense_chi_total,
            'specific_cus_expenses_chi_total' => $expense_thu_total,
        );

        return $result;
    }
    function get_total_contract($arrParams = null, $options = null) {
				$result = array('specific_cus_contract_total'=>$this->count_contracts($arrParams, $options));
        return $result;
    }
		function get_total_contract_suspended($arrParams = null, $options = null) {
				$result = array('specific_cus_contract_suspended_mail_total'=>$this->count_contract_suspended($arrParams, $options));
				return $result;
    }					 
    function count_item($arrParams = null, $options = null) {
        switch ($arrParams['option']) {
            case 'purchase':
                $result = $this->count_purchase($arrParams, $options);
                break;
            case 'mail':
                $result = $this->count_mail($arrParams, $options);
                break;
            case 'expenses':
                $result = $this->count_expenses($arrParams, $options);
                break;
            case 'balance':
                $result = $this->count_balance($arrParams, $options);
                break;
						case 'contract':
								$result = $this->count_contracts($arrParams, $options);
								break;					
						case 'contract_suspended':
								$result = $this->count_contract_suspended($arrParams, $options);
        }

        return $result;
    }

    function count_purchase($arrParams = null, $options = null) {
        $this->db -> select('COUNT(s.sale_id) AS totalItem')
                  -> from('sales AS s')
                  -> where('s.customer_id', $arrParams['customer_id'])
                  -> where('s.location_id IN ('.$arrParams['location_ids'].')')
                  -> where("s.suspended", 0)
                  -> where('s.store_account_payment', 0)
                  -> where('s.deleted', 0);

        if(!empty($arrParams['start_date'])) {
            $this->db->where('s.sale_time >= \''.$arrParams['start_date'].'\'');
        }

        if(!empty($arrParams['end_date'])) {
            $this->db->where('s.sale_time <= \''.$arrParams['end_date'].'\'');
        }

        $query = $this->db->get();
        if (empty($query)) return 0;
        $result = $query->row()->totalItem;

        $this->db->flush_cache();

        return $result;
    }

    function count_balance($arrParams = null, $options = null) {
        $model = $this->Specific_customer_store_account;
        $result = $model->count_item($arrParams);

        return $result;
    }

    function count_mail($arrParams = null, $options = null) {
        $this->db -> select('COUNT(m.id) AS totalItem')
                    -> from('mail_history AS m')
                    -> where('m.person_id', $arrParams['customer_id']);

        if(!empty($arrParams['start_date'])) {
            $this->db->where('m.time >= \''.$arrParams['start_date'].'\'');
        }

        if(!empty($arrParams['end_date'])) {
            $this->db->where('m.time <= \''.$arrParams['end_date'].'\'');
        }

        $query = $this->db->get();

        $result = $query->row()->totalItem;

        $this->db->flush_cache();

        return $result;
    }

    function count_expenses($arrParams = null, $options = null) {
        $this->db -> select("COUNT(e.id) AS totalItem")
                  -> from('expenses AS e')
                  -> join('sales AS s', 'e.sale_id = s.sale_id')
                  -> where('e.location_id IN ('.$arrParams['location_ids'].')')
                  -> where('s.customer_id', $arrParams['customer_id'])
                  -> where('e.deleted', 0)
                  -> where('s.deleted', 0);

        if(!empty($arrParams['start_date'])) {
            $this->db->where('e.expense_date >= \''.$arrParams['start_date'].'\'');
        }

        if(!empty($arrParams['end_date'])) {
            $this->db->where('e.expense_date <= \''.$arrParams['end_date'].'\'');
        }
        $query = $this->db->get();

        $result = $query->row()->totalItem;

        $this->db->flush_cache();

        return $result;
    }
		function count_contracts($arrParams = null, $options = null) {
         $this->db->select("COUNT(c.id) AS totalItem")
                        ->from('contract AS c')
                        ->join('sales AS s', 'c.sale_id = s.sale_id')
												->join('people AS p', 'p.person_id = s.customer_id')
												->where('p.person_id', $arrParams['customer_id'])
												->where('s.deleted', 0);

        if(!empty($arrParams['start_date'])) 
				{
            $this->db->where('c.date_signing >= \''.$arrParams['start_date'].'\'');
        }

        if(!empty($arrParams['end_date'])) 
				{
            $this->db->where('c.date_signing <= \''.$arrParams['end_date'].'\'');
				}

        $query = $this->db->get();

        $result = $query->row()->totalItem;

        $this->db->flush_cache();

        return $result;
    }
		function count_contract_suspended($arrParams = null) {
        $this->db -> select('COUNT(m.id) AS totalItem')
                    -> from('mail_history AS m')
                    -> where('m.person_id', $arrParams['customer_id'])
										-> where("SPLIT_STRING(m.note,'|',1)='BAOGIA' OR SPLIT_STRING(m.note,'|', 1)='HOPDONG'");

        if(!empty($arrParams['start_date'])) {
            $this->db->where('m.time >= \''.$arrParams['start_date'].'\'');
        }

        if(!empty($arrParams['end_date'])) {
            $this->db->where('m.time <= \''.$arrParams['end_date'].'\'');
        }

        $query = $this->db->get();

        $result = $query->row()->totalItem;

        $this->db->flush_cache();

        return $result;
    }

    function list_item($arrParams = null, $options = null) {
        switch ($arrParams['option']) {
            case 'purchase':
                $result = $this->list_purchase($arrParams, $options);
                break;

            case 'mail':
                $result = $this->list_mail($arrParams, $options);
                break;
            case 'expenses':
                $result = $this->list_expenses($arrParams, $options);
                break;
            case 'balance':
                $result = $this->list_balance($arrParams, $options);
                break;
						case 'contract':
								$result = $this->list_contracts($arrParams, $options);
								break;			
						case 'contract_suspended':
								$result = $this->list_contract_suspended($arrParams, $options);
								break;					
        }

        return $result;
    }

    function list_purchase($arrParams = null, $options = null) {
        $paginator = $arrParams['paginator'];

        $this->db -> select('s.sale_id, s.code, s.payment_type, s.comment')
                -> select("DATE_FORMAT(s.sale_time, '%d-%m-%Y %H:%i') AS sale_time_format", FALSE)
                -> from('sales AS s')
                -> where('s.customer_id', $arrParams['customer_id'])
                -> where('s.location_id IN ('.$arrParams['location_ids'].')')
                -> where("s.suspended", 0)
                -> where('s.store_account_payment', 0)
                -> where('s.deleted', 0);

        if(!empty($arrParams['start_date'])) {
            $this->db->where('s.sale_time >= \''.$arrParams['start_date'].'\'');
        }

        if(!empty($arrParams['end_date'])) {
            $this->db->where('s.sale_time <= \''.$arrParams['end_date'].'\'');
        }

        if(!empty($arrParams['col']) && !empty($arrParams['order'])){
            $col   = $this->_sale_fields[$arrParams['col']];
            $order = $arrParams['order'];

            $this->db->order_by($col, $order);
        }

        $page = $arrParams['page'];
        $this->db->limit($paginator['per_page'],($page - 1)*$paginator['per_page']);

        $query = $this->db->get();

        $result = !empty($query) ? $query->result_array() : [];

        $this->db->flush_cache();

        if(!empty($result)) {
            foreach($result as $val)
                $sale_ids[] = $val['sale_id'];

            $tmp          = $this->get_order_total_tax($sale_ids);
            $expense_tmp  = $this->get_expenses($sale_ids);
            if(isset($expense_tmp['thu'])) $thu = $expense_tmp['thu']; else $thu = '';
            if(isset($expense_tmp['chi'])) $chi = $expense_tmp['chi']; else $chi = '';
           
            $sale_order_total_list       = $tmp['total_result'];
            $sale_order_tax_list         = $tmp['tax_result'];
            $sale_order_cost_list        = $tmp['cost_result'];
            $sale_expense_thu_list       = $thu;
            $sale_expense_chi_list       = $chi;
            $sale_commission_list        = $this->Sale->get_commission_from_sale_order(array('sale_ids'=>$sale_ids));
            $sale_point_payment_list     = $this->Sale->get_point_amount_from_sale_order(array('sale_ids'=>$sale_ids));
            $sale_gift_card_payment_list = $this->Sale->get_gift_card_amount_from_sale_order(array('sale_ids'=>$sale_ids));

            foreach($result as &$value) {
                $value['order_value']        = (isset($sale_order_total_list[$value['sale_id']]) && !empty($sale_order_total_list[$value['sale_id']])) ? $sale_order_total_list[$value['sale_id']] : 0;
                $value['cost_value']         = (isset($sale_order_cost_list[$value['sale_id']]) && !empty($sale_order_cost_list[$value['sale_id']])) ? $sale_order_cost_list[$value['sale_id']] : 0;
                $value['tax_value']          = (isset($sale_order_tax_list[$value['sale_id']]) && !empty($sale_order_tax_list[$value['sale_id']])) ? $sale_order_tax_list[$value['sale_id']] : 0;
                $value['thu_value']          = (isset($sale_expense_thu_list[$value['sale_id']]) && !empty($sale_expense_thu_list[$value['sale_id']])) ? $sale_expense_thu_list[$value['sale_id']] : 0;
                $value['chi_value']          = (isset($sale_expense_chi_list[$value['sale_id']]) && !empty($sale_expense_chi_list[$value['sale_id']])) ? $sale_expense_chi_list[$value['sale_id']] : 0;
                $value['commission']         = isset($sale_commission_list[$value['sale_id']]) ? $sale_commission_list[$value['sale_id']] : 0;
                $value['point_payment']      = isset($sale_point_payment_list[$value['sale_id']]) ? $sale_point_payment_list[$value['sale_id']] : 0;
                $value['gift_card_payment']  = isset($sale_gift_card_payment_list[$value['sale_id']]) ? $sale_gift_card_payment_list[$value['sale_id']] : 0;
            }
        }

        return $result;

    }

    function get_expenses($sale_ids) {
        $sale_ids = implode(', ', $sale_ids);

        $sql = "SELECT s.sale_id, SUM(IF(e.expense_type = 1, e.expense_amount + e.expense_tax, 0)) AS chi, SUM(IF(e.expense_type = -1, e.expense_amount + e.expense_tax, 0)) AS thu
                FROM phppos_expenses AS e
                INNER JOIN phppos_sales AS s ON e.sale_id = s.sale_id
                WHERE s.sale_id IN ($sale_ids)
                GROUP BY s.sale_id";

        $query  = $this->db->query($sql);
        $result_tmp = $query->result_array();

        $this->db->flush_cache();
        $result = array();
        if(!empty($result_tmp)) {
            foreach($result_tmp as $val) {
                $sale_id = $val['sale_id'];
                $thu[$sale_id] = $val['thu'];
                $chi[$sale_id] = $val['chi'];
            }

            $result['thu'] = $thu;
            $result['chi'] = $chi;
        }

        return $result;
    }

    function get_order_total_tax($sale_ids) {
        $this->db -> select('sale_id, SUM(ttotal) AS sum_ttotal, SUM(tax) AS sum_tax, SUM(tcost) AS sum_tcost')
                  -> from('sales_items_temp')
                  -> where('sale_id IN ('.implode(',', $sale_ids).')')
                  -> group_by('sale_id');

        $query = $this->db->get();

        $result_tmp = $query->result_array();

        $this->db->flush_cache();
        $result = array();
        if(!empty($result_tmp)) {
            foreach($result_tmp as $val) {
                $total_result[$val['sale_id']] = $val['sum_ttotal'];
                $cost_result[$val['sale_id']]   = $val['sum_tcost'];
                $tax_result[$val['sale_id']]   = $val['sum_tax'];

            }

            $result['total_result'] = $total_result;
            $result['cost_result']  = $cost_result;
            $result['tax_result']   = $tax_result;
        }

        return $result;

    }

    function list_balance($arrParams = null, $options = null) {
        $model = $this->Specific_customer_store_account;
        $result = $model->list_item($arrParams);

        return $result;
    }

    function list_mail($arrParams = null, $options = null) {
        $paginator = $arrParams['paginator'];

        $this->db -> select('m.id, p.first_name, p.last_name, m.email, m.title, m.status')
                  -> select("DATE_FORMAT(m.time, '%d-%m-%Y %H:%i') AS time_format", FALSE)
                  -> from('mail_history AS m')
                  -> join('people AS p', 'm.employee_id = p.person_id', 'left')
                  -> where('m.person_id', $arrParams['customer_id']);

        if(!empty($arrParams['start_date'])) {
            $this->db->where('m.time >= \''.$arrParams['start_date'].'\'');
        }

        if(!empty($arrParams['end_date'])) {
            $this->db->where('m.time <= \''.$arrParams['end_date'].'\'');
        }

        if(!empty($arrParams['col']) && !empty($arrParams['order'])){
            $col   = $this->_mail_fields[$arrParams['col']];
            $order = $arrParams['order'];

            $this->db->order_by($col, $order);
        }

        $page = $arrParams['page'];
        $this->db->limit($paginator['per_page'],($page - 1)*$paginator['per_page']);

        $query = $this->db->get();

        $result = $query->result_array();

        $this->db->flush_cache();

        return $result;
    }

    function get_expenses_total($arrParams = null, $options = null) {
        $this->db -> select("SUM(e.expense_amount+e.expense_tax) AS amount_total")
                 -> from('expenses AS e')
                 -> join('sales AS s', 'e.sale_id = s.sale_id')
                 -> where('e.location_id IN ('.$arrParams['location_ids'].')')
                 -> where('s.customer_id', $arrParams['customer_id'])
                 -> where('e.deleted', 0)
                 -> where('s.deleted', 0);

        if(!empty($arrParams['start_date'])) {
            $this->db->where('e.expense_date >= \''.$arrParams['start_date'].'\'');
        }

        if(!empty($arrParams['end_date'])) {
            $this->db->where('e.expense_date <= \''.$arrParams['end_date'].'\'');
        }

        if(isset($arrParams['expense_type']))
            $this->db->where('e.expense_type', $arrParams['expense_type']);

        $query = $this->db->get();

        $result = $query->row()->amount_total;

        if(empty($result))
            $result = 0;

        $this->db->flush_cache();

        return $result;
    }

    function list_expenses($arrParams = null, $options = null) {
        $paginator = $arrParams['paginator'];

        $this->db -> select("e.id, e.expense_amount, e.expense_tax, e.expense_type, e.expense_description, e.employee_id, c.name AS category_name")
                  -> select("DATE_FORMAT(e.expense_date, '%d/%m/%Y %H:%i:%s') AS expenses_date_format", FALSE)
                  -> from('expenses AS e')
                  -> join('sales AS s', 'e.sale_id = s.sale_id')
                  -> join('categories AS c', 'e.category_id = c.id','left')
                  -> where('s.customer_id', $arrParams['customer_id'])
                  -> where('e.location_id IN ('.$arrParams['location_ids'].')')
                  -> where('e.deleted', 0)
                  -> where('s.deleted', 0);

        if(!empty($arrParams['start_date'])) {
            $this->db->where('e.expense_date >= \''.$arrParams['start_date'].'\'');
        }

        if(!empty($arrParams['end_date'])) {
            $this->db->where('e.expense_date <= \''.$arrParams['end_date'].'\'');
        }

        if(!empty($arrParams['col']) && !empty($arrParams['order'])){
            $col   = $this->_expenses_fields[$arrParams['col']];
            $order = $arrParams['order'];

            $this->db->order_by($col, $order);
        }

        $page = $arrParams['page'];
        $this->db->limit($paginator['per_page'],($page - 1)*$paginator['per_page']);

        $query = $this->db->get();
        $result = $query->result_array();
        $this->db->flush_cache();

        return $result;
    }
		function list_contracts($arrParams = null, $options = null)
		{
			  $paginator = $arrParams['paginator'];
         $this->db->select("*, c.code as contractCode ,CASE WHEN c.status = 'progress' THEN 'Đang thực hiện'
																														WHEN c.status = 'draft' 	THEN 'Dự thảo' 
																														WHEN c.status = 'cancel' 	THEN 'Hủy bỏ'
																														WHEN c.status = 'done' 		THEN 'Đã thực hiện'
																											 END AS status_,
																											 CASE WHEN c.type = 'parttime' THEN 'Thời vụ/ Đơn hàng'
																														WHEN c.type = 'rule' 	THEN 'Nguyên tắc'
																											 END AS type")																																			
                        ->select("DATE_FORMAT(c.date_signing, '%d-%m-%Y') as date_signing", FALSE)
                        ->from('contract AS c')
                        ->join('sales AS s', 'c.sale_id = s.sale_id')
												->join('people AS p', 'p.person_id = s.customer_id')
												->where('p.person_id', $arrParams['customer_id'])
												-> where('s.deleted', 0);

        if(!empty($arrParams['start_date'])) {
            $this->db->where('c.date_signing >= \''.$arrParams['start_date'].'\'');
        }

        if(!empty($arrParams['end_date'])) {
            $this->db->where('c.date_signing <= \''.$arrParams['end_date'].'\'');
        }

        if(!empty($arrParams['col']) && !empty($arrParams['order'])){
            $col   = $this->_contracts_fields[$arrParams['col']];
            $order = $arrParams['order'];

            $this->db->order_by($col, $order);
        }

        $page = $arrParams['page'];
        $this->db->limit($paginator['per_page'],($page - 1)*$paginator['per_page']);

        $query = $this->db->get();
        $result = $query->result_array();
        $this->db->flush_cache();
				return $result;
		}												
    function list_contract_suspended($arrParams = null, $options = null) {
        $paginator = $arrParams['paginator'];

        $this->db -> select("*,CASE SPLIT_STRING(m.note,'|', 1) 
																	WHEN 'BAOGIA' THEN 'Báo giá' 
																	WHEN 'HOPDONG' THEN 'Hợp đồng'
																	END as contract_suspended_type,
															CASE m.status 
																	WHEN 1 THEN 'Đã gửi' 
																	WHEN 0 THEN 'Chưa gửi'
																	END as _status,
															SPLIT_STRING(m.note,'|', 2) AS contract_suspended_code,
															SPLIT_STRING(m.note,'|', 3) AS contract_suspended_total")
                  -> select("DATE_FORMAT(m.time, '%d-%m-%Y %H:%i') AS time_format", FALSE)
                  -> from('mail_history AS m')
                  -> join('people AS p', 'm.employee_id = p.person_id', 'left')
                  -> where('m.person_id', $arrParams['customer_id'])
									-> where("SPLIT_STRING(m.note,'|', 1) = 'BAOGIA' OR SPLIT_STRING(m.note,'|', 1)='HOPDONG'");

        if(!empty($arrParams['start_date'])) {
            $this->db->where('m.time >= \''.$arrParams['start_date'].'\'');
        }

        if(!empty($arrParams['end_date'])) {
            $this->db->where('m.time <= \''.$arrParams['end_date'].'\'');
        }

        if(!empty($arrParams['col']) && !empty($arrParams['order'])){
            $col   = $this->_mail_fields[$arrParams['col']];
            $order = $arrParams['order'];

            $this->db->order_by($col, $order);
        }

        $page = $arrParams['page'];
        $this->db->limit($paginator['per_page'],($page - 1)*$paginator['per_page']);

        $query = $this->db->get();

        $result = $query->result_array();

        $this->db->flush_cache();

        return $result;
    }		
}
?>