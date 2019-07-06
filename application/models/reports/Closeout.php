<?php
require_once ("Report.php");
class Closeout extends Report
{
	function __construct()
	{
		parent::__construct();
	}

	public function getDataColumns()
	{
		$columns = array();
		
		$columns[] = array('data'=>lang('reports_description'), 'align'=> 'left');
		$columns[] = array('data'=>lang('reports_data'), 'align'=> 'left');
		
		return $columns;		
	}
	
	public function getData()
	{		
		
		$return = array();
		
		$location_ids = self::get_selected_location_ids();
		$location_ids_string = implode(',',$location_ids);
		
		//Sales
		$this->db->select('sum(total) as total, sum(tax) as tax, sum(profit) as profit, sum(quantity_purchased) as quantity', false);
		$this->db->from('sales_items_temp');
		$this->db->where('quantity_purchased > 0');
		
		$this->db->where('deleted', 0);
		$this->db->group_by('sale_id');
		$this->db->order_by('sale_time', ($this->config->item('report_sort_order')) ? $this->config->item('report_sort_order') : 'asc');

		$sales_row = array(
			'total' => 0,
			'tax' => 0,
			'profit' => 0,
			'quantity' => 0,
		);
		
		foreach($this->db->get()->result_array() as $row)
		{
			$sales_row['total'] += to_currency_no_money($row['total'],2);
			$sales_row['tax'] += to_currency_no_money($row['tax'],2);
			$sales_row['profit'] += to_currency_no_money($row['profit'],2);
			$sales_row['quantity'] += $row['quantity'];
		}
				
		$yesterday = date('Y-m-d', strtotime($this->params['date'].' -1 days'));
		$tomorrow = date('Y-m-d', strtotime($this->params['date'].' +1 days'));

		$return[] = array(anchor('reports/closeout/'.$yesterday,'&laquo; '.lang('reports_previous_day')), anchor('reports/closeout/'.$tomorrow,lang('reports_next_day').' &raquo;'));

		$return[] = array('<h1>'.lang('reports_sales').'</h1>', '--');
		$return[] = array(lang('reports_total_sales'). ' ('.lang('common_without_tax').')', isset($sales_row['total']) ? to_currency($sales_row['total'] - $sales_row['tax']) : 0);
		$return[] = array(lang('reports_total_sales').' ('.lang('reports_items_with_tax').')', isset($sales_row['total']) ? to_currency($sales_row['total']) : 0);
		if($this->Employee->has_module_action_permission('reports','show_profit',$this->Employee->get_logged_in_employee_info()->person_id))
		{
			$return[] = array(lang('reports_profit'), isset($sales_row['profit']) ? to_currency($sales_row['profit']) : 0);
		}
					
		$this->load->model('reports/Inventory_summary');
		$model_inv_sum = $this->Inventory_summary;
		$model_inv_sum->setParams(array('supplier'=>-1,'category_id' => -1, 'export_excel' => 1, 'offset'=>0, 'inventory' => 'all','show_only_pending' => 0));
		
		$summary_data = $model_inv_sum->getSummaryData();
		
		$return[] = array(lang('reports_total_items_in_inventory'), to_quantity($summary_data['total_items_in_inventory']));
		$return[] = array(lang('reports_inventory_total'), to_currency($summary_data['inventory_total']));
		
		
		$return[] = array('&nbsp;', '&nbsp;');
		
		$this->db->select('category, sum(subtotal) as subtotal, sum(total) as total', false);
		$this->db->from('sales_items_temp');
		$this->db->where('quantity_purchased > 0');
		
		$this->db->where($this->db->dbprefix('sales_items_temp').'.deleted', 0);
		$this->db->group_by('category');
		$this->db->order_by('category');
		$category_sales = $this->db->get()->result_array();		
		
		
		foreach($category_sales as $category_sale_row)
		{
			$return[] = array($category_sale_row['category'],to_currency($category_sale_row['total']));
		}
		$return[] = array('&nbsp;', '&nbsp;');
		
		
		//Sales total count for day
		$this->db->from('sales_items_temp');
		$this->db->where('quantity_purchased > 0');
		$this->db->where('deleted', 0);
		$this->db->group_by('sale_id');
		
		$number_of_sales_transactions = $this->db->get()->num_rows();
		$average_ticket_size = $number_of_sales_transactions > 0 ? $sales_row['total']/$number_of_sales_transactions : 0;
		
		$return[] = array(lang('reports_number_of_transactions'), to_quantity($number_of_sales_transactions));
		$return[] = array(lang('reports_average_ticket_size'), to_currency($average_ticket_size));
		
		$return[] = array(lang('common_items_sold'), isset($sales_row['quantity']) ? to_quantity($sales_row['quantity']) : 0);
		
		$return[] = array('&nbsp;', '&nbsp;');
		
		$return[] = array(lang('common_tax'), isset($sales_row['tax']) ? to_currency($sales_row['tax']) : 0);		
		
		$this->load->model('reports/Summary_taxes');
		
		$this->Summary_taxes->setParams(array('start_date'=>$this->params['date'], 'end_date'=>$this->params['date'].' 23:59:59','sale_type' => 'sales'));
		$taxes = $this->Summary_taxes->getData();
		
		foreach($taxes as $tax_row)
		{
			if ($tax_row['name'] != lang('reports_non_taxable'))
			{
				$return[] = array($tax_row['name'], lang('common_tax').': '.to_currency($tax_row['tax']).'<br />'.lang('reports_subtotal').': '.to_currency($tax_row['subtotal']).'<br />'.lang('reports_total').': '.to_currency($tax_row['total']));		
			}
		}
		
		if(isset($taxes[lang('reports_non_taxable')]))
		{
			$return[] = array(lang('reports_non_taxable'), to_currency($taxes[lang('reports_non_taxable')]['total']));
		}
		
		$return[] = array('&nbsp;', '&nbsp;');

		
		$this->db->select('sales_payments.sale_id, sales_payments.payment_type, payment_amount, payment_id', false);
		$this->db->from('sales_payments');
		$this->db->join('sales', 'sales.sale_id=sales_payments.sale_id');
		$this->db->where('payment_date BETWEEN '. $this->db->escape($this->params['date']). ' and '. $this->db->escape($this->params['date']. ' 23:59:59').' and location_id IN('.$location_ids_string.')');
		
		if ($this->config->item('hide_store_account_payments_in_reports'))
		{
			$this->db->where('store_account_payment',0);
		}
		
		$this->db->where('payment_amount > 0');
		
		$this->db->where($this->db->dbprefix('sales').'.deleted', 0);
		$this->db->order_by('sale_id, payment_date , payment_type');
				
		$sales_payments = $this->db->get()->result_array();

		$payments_by_sale = array();
		foreach($sales_payments as $row)
		{
        	$payments_by_sale[$row['sale_id']][] = $row;
		}
		
		$payment_data = $this->Sale->get_payment_data($payments_by_sale,$this->params['sales_total_for_payments']);
		
		foreach($payment_data as $payment_row)
		{
			$return[] = array($payment_row['payment_type'],to_currency($payment_row['payment_amount']));
		}
		
		
		if ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'advanced')
		{
			$points = array();
		
			$this->db->select('SUM(points_used) as points_used, SUM(points_gained) as points_gained', false);
			$this->db->from('sales');
			$this->db->where('date(sale_time) BETWEEN '. $this->db->escape($this->params['date']). ' and '. $this->db->escape($this->params['date']. ' 23:59:59').' and location_id IN('.$location_ids_string.')');
			$this->db->where('deleted', 0);
			$this->db->where_in('location_id',$location_ids);
		
			$points = $this->db->get()->row_array();
		
			$return[] = array(lang('reports_points_used'), to_currency_no_money($points['points_used']));
			$return[] = array(lang('reports_points_earned'), to_currency_no_money($points['points_gained']));
		
		}
		if ($this->config->item('customers_store_accounts'))
		{
			$this->db->select("SUM(IF(transaction_amount > 0, `transaction_amount`, 0)) as debits, SUM(IF(transaction_amount < 0, `transaction_amount`, 0)) as credits", false);
			$this->db->from('store_accounts');
			$this->db->join('customers', 'customers.person_id = store_accounts.customer_id');
			$this->db->join('people', 'customers.person_id = people.person_id');
			$this->db->where('date BETWEEN '.$this->db->escape($this->params['date']).' and '.$this->db->escape($this->params['date']. ' 23:59:59'));
			
			$return[] = array('<h1>'.lang('reports_store_account').'</h1>', '--');
			
			//Store account info
			$store_account_credits_and_debits = $this->db->get()->row_array();
		
			$this->db->select('SUM(balance) as total_balance_of_all_store_accounts', false);
			$this->db->from('customers');		
			$total_store_account_balances = $this->db->get()->row_array();
		
			$store_account_info = array_merge($store_account_credits_and_debits, $total_store_account_balances);
			$return[] = array(lang('reports_debits'),to_currency($store_account_info['debits']));
			$return[] = array(lang('reports_credits'),to_currency(abs($store_account_info['credits'])));
			$return[] = array(lang('reports_total_balance_of_all_store_accounts'),to_currency($store_account_info['total_balance_of_all_store_accounts']));
		}
		
		$this->db->select('sum(total) as total, sum(tax) as tax, sum(quantity_purchased) as quantity', false);
		$this->db->from('sales_items_temp');
		$this->db->where('quantity_purchased < 0');
		
		$this->db->where('deleted', 0);
		$this->db->group_by('sale_id');
		$this->db->order_by('sale_time', ($this->config->item('report_sort_order')) ? $this->config->item('report_sort_order') : 'asc');
		
		$sales_row = array(
			'total' => 0,
			'tax' => 0,
			'quantity' => 0,
		);
		
		foreach($this->db->get()->result_array() as $row)
		{
			$sales_row['total'] += to_currency_no_money($row['total'],2);
			$sales_row['tax'] += to_currency_no_money($row['tax'],2);
			$sales_row['quantity'] += $row['quantity'];
		}
		
		$return[] = array('<h1>'.lang('reports_returns').'</h1>', '--');
		$return[] = array(lang('reports_total_returned'), isset($sales_row['total']) ? to_currency(abs($sales_row['total'])) : 0);
		
		$this->db->select('category, sum(subtotal) as subtotal, sum(total) as total', false);
		$this->db->from('sales_items_temp');
		$this->db->where('quantity_purchased < 0');
		
		$this->db->where($this->db->dbprefix('sales_items_temp').'.deleted', 0);
		$this->db->group_by('category');
		$this->db->order_by('category');
		$category_returns = $this->db->get()->result_array();		
		
		
		foreach($category_returns as $category_sale_row)
		{
			$return[] = array($category_sale_row['category'],to_currency(abs($category_sale_row['total'])));
		}
		
		//Sales total count for day
		$this->db->from('sales_items_temp');
		$this->db->where('quantity_purchased < 0');
		$this->db->where('deleted', 0);
		$this->db->group_by('sale_id');
		
		$number_of_returned_transactions = $this->db->get()->num_rows();
		
		$return[] = array(lang('reports_number_of_transactions'), to_quantity($number_of_returned_transactions));
		$return[] = array(lang('reports_items_returned'), isset($sales_row['quantity']) ? to_quantity(abs($sales_row['quantity'])) : 0);
		$return[] = array(lang('common_tax'), isset($sales_row['tax']) ? to_currency(abs($sales_row['tax'])) : 0);
		
		
		$location_ids = self::get_selected_location_ids();
		$location_ids_string = implode(',',$location_ids);
		
		$this->db->select('sales_payments.sale_id, sales_payments.payment_type, payment_amount, payment_id', false);
		$this->db->from('sales_payments');
		$this->db->join('sales', 'sales.sale_id=sales_payments.sale_id');
		$this->db->where('payment_date BETWEEN '. $this->db->escape($this->params['date']). ' and '. $this->db->escape($this->params['date']. ' 23:59:59').' and location_id IN('.$location_ids_string.')');
		
		if ($this->config->item('hide_store_account_payments_in_reports'))
		{
			$this->db->where('store_account_payment',0);
		}
		
		$this->db->where('payment_amount < 0');
		
		$this->db->where($this->db->dbprefix('sales').'.deleted', 0);
		$this->db->order_by('sale_id, payment_date , payment_type');
				
		$sales_payments = $this->db->get()->result_array();

		$payments_by_sale = array();
		foreach($sales_payments as $row)
		{
        	$payments_by_sale[$row['sale_id']][] = $row;
		}
		
		$payment_data = $this->Sale->get_payment_data($payments_by_sale,$this->params['sales_total_for_payments']);
		
		foreach($payment_data as $payment_row)
		{
			$return[] = array($payment_row['payment_type'],to_currency(abs($payment_row['payment_amount'])));
		}
		
		//Receivings
		
		$this->db->select('sum(total) as total, sum(tax) as tax, sum(quantity_purchased) as quantity', false);
		$this->db->from('receivings_items_temp');
		$this->db->where('quantity_purchased > 0');
		
		$this->db->where('deleted', 0);
		$this->db->group_by('receiving_date');
		$this->db->order_by('receiving_time', ($this->config->item('report_sort_order')) ? $this->config->item('report_sort_order') : 'asc');
		
				
		$recvs_row = $this->db->get()->row_array();
		
		$return[] = array('<h1>'.lang('reports_receivings').'</h1>', '--');
		$return[] = array(lang('reports_total_receivings'). ' ('.lang('common_without_tax').')', isset($recvs_row['total']) ? to_currency($recvs_row['total'] - $recvs_row['tax']) : 0);
		$return[] = array(lang('reports_total_receivings').' ('.lang('reports_items_with_tax').')', isset($recvs_row['total']) ? to_currency($recvs_row['total']) : 0);		
		$return[] = array('&nbsp;', '&nbsp;');
		
		$this->db->select('category, sum(subtotal) as subtotal, sum(total) as total', false);
		$this->db->from('receivings_items_temp');
		$this->db->where('quantity_purchased > 0');
		
		$this->db->where($this->db->dbprefix('receivings_items_temp').'.deleted', 0);
		$this->db->group_by('category');
		$this->db->order_by('category');
		$category_recvs = $this->db->get()->result_array();		
		
		
		foreach($category_recvs as $category_recv_row)
		{
			$return[] = array($category_recv_row['category'],to_currency($category_recv_row['total']));
		}
		$return[] = array('&nbsp;', '&nbsp;');
		
		
		//rececvings total count for day
		$this->db->from('receivings_items_temp');
		$this->db->where('quantity_purchased > 0');
		$this->db->where('deleted', 0);
		$this->db->group_by('receiving_id');
		
		$number_of_recevings_transactions = $this->db->get()->num_rows();
		$average_ticket_size = $number_of_recevings_transactions > 0 ? $recvs_row['total']/$number_of_recevings_transactions : 0;
		
		
		$return[] = array(lang('reports_number_of_transactions'), to_quantity($number_of_recevings_transactions));
		$return[] = array(lang('reports_average_ticket_size'), to_currency($average_ticket_size));
		
		$return[] = array(lang('reports_items_recved'), isset($recvs_row['quantity']) ? to_quantity($recvs_row['quantity']) : 0);
		$return[] = array('&nbsp;', '&nbsp;');
		
		$return[] = array(lang('common_tax'), isset($recvs_row['tax']) ? to_currency($recvs_row['tax']) : 0);
		
		$taxes_data = array();
		$this->load->model('reports/Summary_taxes_receivings');
		
		$this->Summary_taxes_receivings->setParams(array('start_date'=>$this->params['date'], 'end_date'=>$this->params['date'].' 23:59:59','sale_type' => 'sales'));
		$taxes = $this->Summary_taxes_receivings->getData();
		
		foreach($taxes as $tax_row)
		{
			if ($tax_row['name'] != lang('reports_non_taxable'))
			{
				$return[] = array($tax_row['name'], lang('common_tax').': '.to_currency($tax_row['tax']).'<br />'.lang('reports_subtotal').': '.to_currency($tax_row['subtotal']).'<br />'.lang('reports_total').': '.to_currency($tax_row['total']));		
			}
		}
		
		if(isset($taxes[lang('reports_non_taxable')]))
		{
			$return[] = array(lang('reports_non_taxable'), to_currency($taxes[lang('reports_non_taxable')]['total']));
		}
		
		$return[] = array('&nbsp;', '&nbsp;');
		
		$this->db->select('categories.name as category, SUM(expense_amount) as amount', false);
		$this->db->from('expenses');
		$this->db->join('categories', 'categories.id = expenses.category_id','left');
		$this->db->where('expenses.deleted', 0);

        $shift_category_id = $this->config->item('shift_category_id');
        if($shift_category_id > 0)
            $this->db->where('category_id != ' . $shift_category_id);

		$this->db->group_by('categories.id');
		$this->db->where($this->db->dbprefix('expenses').'.expense_date BETWEEN '. $this->db->escape($this->params['date']). ' and '. $this->db->escape($this->params['date']. ' 23:59:59').' and location_id IN('.$location_ids_string.')');
		$this->db->order_by('expenses.id');

		$category_expenses = $this->db->get()->result_array();

		$total = 0;
		
		foreach($category_expenses as $category_sale_row)
		{
			$total += $category_sale_row['amount'];
		}
		
		$return[] = array('<h1>'.lang('common_expenses').'</h1>', '--');
		$return[] = array(lang('reports_total_expenses'), to_currency($total));
		
		foreach($category_expenses as $category_sale_row)
		{
			$return[] = array($category_sale_row['category'],to_currency($category_sale_row['amount']));
		}

        if($shift_category_id > 0) {
            $tblCategory = $this->model_load_model('Category');
            $category = $tblCategory->getItem($shift_category_id);
            if(!empty($category)) {
                $this->db -> select('SUM(e.expense_amount) AS sum_shift')
                          -> from('expenses AS e')
                          -> where('e.category_id', $shift_category_id)
                          -> where('e.expense_date BETWEEN '. $this->db->escape($this->params['date']). ' and '. $this->db->escape($this->params['date']. ' 23:59:59').' and e.location_id IN('.$location_ids_string.')')
                          -> where('e.deleted', 0);

                $query = $this->db->get();
                $sum_shift = $query->row()->sum_shift;
                $this->db->flush_cache();

                $return[] = array('<h1>'.$category['name'].'</h1>', '--');
                $return[] = array('Tổng tiền: ',to_currency($sum_shift));
            }
        }

		//Cash Tracking
		if ($this->config->item('track_cash'))
		{
			$between = 'between ' . $this->db->escape($this->params['date'] . ' 00:00:00').' and ' . $this->db->escape($this->params['date'] . ' 23:59:59');
			$this->db->select("locations.name as location_name, registers.name as register_name, register_log.*, (register_log.close_amount + register_log.cost - register_log.open_amount - register_log.cash_sales_amount - register_log.total_cash_additions + register_log.total_cash_subtractions) as difference");
			$this->db->from('register_log as register_log');
			$this->db->join('registers', 'registers.register_id = register_log.register_id');
			$this->db->join('locations', 'registers.location_id = locations.location_id');
			$this->db->where('register_log.shift_end ' . $between);
			$this->db->where('register_log.deleted ', 0);
			$this->db->where_in('registers.location_id', $location_ids);
		
			$cash_logging = $this->db->get()->result_array();
			
			$return[] = array('<h1>'.lang('common_track_cash').'</h1>', '--');
			
			
			foreach($cash_logging as $cash_logging_row)
			{
				$data = lang('common_opening_amount').': '.to_currency($cash_logging_row['open_amount']);
				
				if ($cash_logging_row['shift_end']=='0000-00-00 00:00:00')
				{
					$data.= ' / '.lang('common_closing_amount').': '.lang('reports_register_log_open');
					$data .= ' / '.lang('common_cash_sales').': '.to_currency($cash_logging_row['cash_sales_amount']);					
					$data .= ' / '.lang('common_total_cash_additions').': '.to_currency($cash_logging_row['total_cash_additions']);					
					$data .= ' / '.lang('common_total_cash_subtractions').': '.to_currency($cash_logging_row['total_cash_subtractions']);					
				}
				else
				{					
					$data .= ' / '.lang('common_closing_amount').': '.to_currency($cash_logging_row['close_amount']);					
					$data .= ' / '.lang('common_cash_sales').': '.to_currency($cash_logging_row['cash_sales_amount']);					
					$data .= ' / '.lang('common_total_cash_additions').': '.to_currency($cash_logging_row['total_cash_additions']);					
					$data .= ' / '.lang('common_total_cash_subtractions').': '.to_currency($cash_logging_row['total_cash_subtractions']);					
				}

                $data .= ' / '.$cash_logging_row['cost_name'].': '.to_currency($cash_logging_row['cost']);
				$data .= ' / '.lang('reports_difference').': '.to_currency($cash_logging_row['difference']);					
				
				$return[] = array('<h2>'.$cash_logging_row['register_name'].' ('.$cash_logging_row['location_name'].')</h2>' .date(get_date_format().' '.get_time_format(), strtotime($cash_logging_row['shift_start'])).' - '.date(get_date_format().' '.get_time_format(), strtotime($cash_logging_row['shift_end'])),$data);
			}
		}
		
		
		$return[] = array(anchor('reports/closeout/'.$yesterday,'&laquo; '.lang('reports_previous_day')), anchor('reports/closeout/'.$tomorrow,lang('reports_next_day').' &raquo;'));
		
		
		return $return;
	}
	
		
	
	public function getSummaryData()
	{
		return array();
	}

    public function get_ban_hang($arrParams = null) {
        $date        = $arrParams['date'];
        $location_id = $arrParams['location_id'];

        $sql = "SELECT SUM(si.ttotal+si.tax) AS order_total, SUM(si.ttotal) AS sum_ttotal, SUM(si.tcost) AS sum_tcost, SUM(si.tax) AS s_tax
                FROM phppos_sales_items_temp AS si
                WHERE (si.suspended = 0 OR si.is_vat = 1) AND si.return = 0 AND si.store_account_payment = 0";

        $query  			   = $this->db->query($sql);

        $result 		   = $query->row_array();
        $tong_don_hang 		   = (!empty($result['order_total'])) ? $result['order_total'] : 0; // tổng giá trị đơn hàng
        $tong_don_hang_bo_thue = (!empty($result['sum_ttotal'])) ? $result['sum_ttotal'] : 0; // tổng giá trị đơn hàng bỏ thuế
        $tong_gia_von 		   = (!empty($result['sum_tcost'])) ? $result['sum_tcost'] : 0; // tổng giá vốn
        $tong_thue 		   	   = (!empty($result['s_tax'])) ? $result['s_tax'] : 0; // tổng thuế

        $sql = "SELECT SUM(si.ttotal) AS order_total
		        FROM phppos_sales_items_temp AS si
		        WHERE (si.suspended = 0 OR si.is_vat = 1) AND si.return = 0 AND si.tax = 0 AND si.store_account_payment = 0";

        $query  = $this->db->query($sql);
        $result = $query->row_array();

        $tong_don_hang_khong_chiu_thue = (!empty($result['order_total'])) ? $result['order_total'] : 0; // tổng giá trị đơn hàng không chịu thuế

        $sql = "SELECT SUM(si.ttotal+si.tax) AS order_total
		        FROM phppos_sales_items_temp AS si
		        WHERE (si.suspended = 0 OR si.is_vat = 1) AND si.return = 0 AND si.tax != 0 AND si.store_account_payment = 0";

        $query  = $this->db->query($sql);
        $result = $query->row_array();

        $tong_don_hang_chiu_thue = (!empty($result['order_total'])) ? $result['order_total'] : 0; // tổng giá trị đơn hàng chịu thuế

        $sql = "SELECT count(s.sale_id) AS total
				FROM phppos_sales AS s
				WHERE (s.suspended = 0 OR s.is_vat = 1) AND s.return = 0 AND s.store_account_payment = 0 AND s.deleted = 0
				AND s.sale_time >= '$date 00:00:00' AND s.sale_time <= '$date 23:59:59'
				AND s.location_id = $location_id";

        $query  = $this->db->query($sql);
        $result = $query->row_array();

        $order_count = $result['total']; // Số đơn hàng

        if($order_count > 0)
            $trung_binh_don_hang = $tong_don_hang / $order_count;
        else
            $trung_binh_don_hang = 0;

        $hoa_hong = 0;

        $sql = "SELECT SUM(payment_amount) AS  payment_amount_total, p.payment_type
				FROM phppos_sales_payments AS p
				JOIN phppos_sales as s ON s.sale_id = p.sale_id
				WHERE s.suspended = 0 AND s.return = 0 AND s.store_account_payment = 0 AND s.is_vat = 0 AND s.deleted = 0
				AND p.payment_date >= '$date 00:00:00' AND p.payment_date <= '$date 23:59:59'
				AND s.location_id = $location_id
				GROUP BY p.payment_type";

        $query      = $this->db->query($sql);
        $thanh_toan = $query->result_array();

        $ban_hang['tong_don_hang']                 = $tong_don_hang;
        $ban_hang['tong_don_hang_khong_chiu_thue'] = $tong_don_hang_khong_chiu_thue;

        $ban_hang['tong_don_hang_chiu_thue'] 	     = $tong_don_hang_chiu_thue;
        $ban_hang['tong_don_hang_bo_thue'] 	       = $tong_don_hang_bo_thue;
        $ban_hang['tong_thue'] 					           = $tong_thue;

        $ban_hang['so_luong_giao_dich']            = $order_count;
        $ban_hang['trung_binh_don_hang'] 		       = $trung_binh_don_hang;
        $ban_hang['thanh_toan']                    = $thanh_toan;

		    $ban_hang['hoa_hong'] 					           = 0;

        return $ban_hang;
    }

    public function get_tra_hang($arrParams = null) {
        $date        = $arrParams['date'];
        $location_id = $arrParams['location_id'];

        $sql = "SELECT SUM(si.ttotal+si.tax) AS order_total, SUM(si.ttotal) AS sum_ttotal, SUM(si.tcost) AS sum_tcost, SUM(si.tax) AS s_tax
                FROM phppos_sales_items_temp AS si
                WHERE si.return = 1";

        $query  			= $this->db->query($sql);

        $result 		   = $query->row_array();

        $tong_don_hang 		   = (!empty($result['order_total'])) ? $result['order_total'] : 0; // tổng giá trị đơn hàng
        $tong_don_hang 		   = abs($tong_don_hang);

        $tong_don_hang_bo_thue = (!empty($result['sum_ttotal'])) ? $result['sum_ttotal'] : 0; // tổng giá trị đơn hàng bỏ thuế
        $tong_don_hang_bo_thue = abs($tong_don_hang_bo_thue);

        $tong_gia_von 		   = (!empty($result['sum_tcost'])) ? $result['sum_tcost'] : 0; // tổng giá vốn
        $tong_gia_von 		   = abs($tong_gia_von);

        $tong_thue 		   	   = (!empty($result['s_tax'])) ? $result['s_tax'] : 0; // tổng thuế
        $tong_thue 		   	   = abs($tong_thue);

        $sql = "SELECT SUM(si.ttotal) AS order_total
		        FROM phppos_sales_items_temp AS si
		        WHERE si.return = 1 AND si.tax = 0";

        $query  = $this->db->query($sql);
        $result = $query->row_array();

        $tong_don_hang_khong_chiu_thue 		= (!empty($result['order_total'])) ? $result['order_total'] : 0; // tổng giá trị đơn hàng không chịu thuế
        $tong_don_hang_khong_chiu_thue 	    = abs($tong_don_hang_khong_chiu_thue);

        $sql = "SELECT SUM(si.ttotal+si.tax) AS order_total
		        FROM phppos_sales_items_temp AS si
		        WHERE si.return = 1 AND si.tax != 0";

        $query  = $this->db->query($sql);

        $result = $query->row_array();

        $sql = "SELECT SUM(payment_amount) AS  payment_amount_total, p.payment_type
				FROM phppos_sales_payments AS p
				JOIN phppos_sales as s ON s.sale_id = p.sale_id
				WHERE s.return = 1 AND s.deleted = 0
				AND p.payment_date >= '$date 00:00:00' AND p.payment_date <= '$date 23:59:59'
				AND s.location_id = $location_id
				GROUP BY p.payment_type";

        $query      = $this->db->query($sql);
        $thanh_toan = $query->result_array();

        $tong_don_hang_chiu_thue = (!empty($result['order_total'])) ? $result['order_total'] : 0; // tổng giá trị đơn hàng chịu thuế
        $tong_don_hang_chiu_thue = abs($tong_don_hang_chiu_thue);

        $sql = "SELECT count(s.sale_id) AS total
				FROM phppos_sales AS s
				WHERE s.return = 1 AND s.deleted = 0
				AND s.sale_time >= '$date 00:00:00' AND s.sale_time <= '$date 23:59:59'
				AND s.location_id = $location_id";

        $query  = $this->db->query($sql);
        $result = $query->row_array();

        $order_count = $result['total']; // Số đơn hàng

        $tra_hang['tong_don_hang'] 				   = $tong_don_hang;
        $tra_hang['tong_don_hang_khong_chiu_thue'] = $tong_don_hang_khong_chiu_thue;
        $tra_hang['tong_don_hang_chiu_thue'] 	   = $tong_don_hang_chiu_thue;
        $tra_hang['tong_don_hang_bo_thue'] 		   = $tong_don_hang_bo_thue;

        $tra_hang['tong_thue'] 					   = $tong_thue;
        $tra_hang['thanh_toan']                    = $thanh_toan;
        $tra_hang['so_luong_giao_dich']            = $order_count;

        return $tra_hang;
    }

    public function get_dat_hang($arrParams = null) {
        $date        = $arrParams['date'];
        $location_id = $arrParams['location_id'];

        $sql = "SELECT SUM(si.ttotal+si.tax) AS order_total, SUM(si.ttotal) AS sum_ttotal, SUM(si.tcost) AS sum_tcost, SUM(si.tax) AS s_tax
                FROM phppos_sales_items_temp AS si
                WHERE si.suspended = 1 AND si.was_layaway = 1";

        $query  			   = $this->db->query($sql);

        $result 		   = $query->row_array();

        $tong_don_hang 		   = (!empty($result['order_total'])) ? $result['order_total'] : 0; // tổng giá trị đơn hàng
        $tong_don_hang_bo_thue = (!empty($result['sum_ttotal'])) ? $result['sum_ttotal'] : 0; // tổng giá trị đơn hàng bỏ thuế
        $tong_gia_von 		   = (!empty($result['sum_tcost'])) ? $result['sum_tcost'] : 0; // tổng giá vốn
        $tong_thue 		   	   = (!empty($result['s_tax'])) ? $result['s_tax'] : 0; // tổng thuế

        $sql = "SELECT SUM(si.ttotal) AS order_total
		        FROM phppos_sales_items_temp AS si
		        WHERE si.suspended = 1 AND si.was_layaway = 1 AND si.tax = 0";

        $query  = $this->db->query($sql);
        $result = $query->row_array();

        $tong_don_hang_khong_chiu_thue = (!empty($result['order_total'])) ? $result['order_total'] : 0; // tổng giá trị đơn hàng không chịu thuế

        $sql = "SELECT SUM(si.ttotal+si.tax) AS order_total
		        FROM phppos_sales_items_temp AS si
		        WHERE si.suspended = 1 AND si.was_layaway = 1 AND si.tax != 0";

        $query  = $this->db->query($sql);
        $result = $query->row_array();

        $tong_don_hang_chiu_thue = (!empty($result['order_total'])) ? $result['order_total'] : 0; // tổng giá trị đơn hàng chịu thuế

        $sql = "SELECT COUNT(s.sale_id) AS total
				FROM phppos_sales AS s
				WHERE s.suspended = 1 AND s.was_layaway = 1 AND s.deleted = 0
				AND s.sale_time >= '$date 00:00:00' AND s.sale_time <= '$date 23:59:59'
				AND s.location_id = $location_id";

        $query  = $this->db->query($sql);
        $result = $query->row_array();

        $order_count = $result['total']; // Số đơn hàng

        if($order_count > 0)
            $trung_binh_don_hang = $tong_don_hang / $order_count;
        else
            $trung_binh_don_hang = 0;


        $sql = "SELECT SUM(payment_amount) AS  payment_amount_total, p.payment_type
				FROM phppos_sales_payments AS p
				JOIN phppos_sales as s ON s.sale_id = p.sale_id
				WHERE s.suspended = 1 AND s.was_layaway = 1 AND s.deleted = 0
				AND p.payment_date >= '$date 00:00:00' AND p.payment_date <= '$date 23:59:59'
				AND s.location_id = $location_id
				GROUP BY p.payment_type";

        $query  = $this->db->query($sql);
        $thanh_toan = $query->result_array();


        $dat_hang['tong_don_hang'] 				   = $tong_don_hang;
        $dat_hang['tong_don_hang_khong_chiu_thue'] = $tong_don_hang_khong_chiu_thue;
        $dat_hang['tong_don_hang_chiu_thue'] 	   = $tong_don_hang_chiu_thue;
        $dat_hang['tong_don_hang_bo_thue'] 		   = $tong_don_hang_bo_thue;
        $dat_hang['tong_thue'] 					   = $tong_thue;

        $dat_hang['so_luong_giao_dich'] 		   = $order_count;
        $dat_hang['trung_binh_don_hang'] 		   = $trung_binh_don_hang;
        $dat_hang['thanh_toan']                    = $thanh_toan;

        return $dat_hang;
    }

    public function get_nhap_hang($arrParams = null) {
        $date        = $arrParams['date'];
        $location_id = $arrParams['location_id'];

        $sql = "SELECT SUM(ri.ttotal+ri.tax) AS order_total, SUM(ri.ttotal) AS sum_ttotal, SUM(ri.tax) AS s_tax
				FROM phppos_receivings_items_temp AS ri
				WHERE ri.suspended = 0 AND ri.return = 0 AND ri.store_account_payment = 0";

        $query  		   = $this->db->query($sql);

        $result 		   = $query->row_array();

        $tong_don_nhap 		   = (!empty($result['order_total'])) ? $result['order_total'] : 0; // tổng giá trị đơn nhập
        $tong_don_nhap_bo_thue = (!empty($result['sum_ttotal'])) ? $result['sum_ttotal'] : 0; // tổng giá trị đơn nhập bỏ thuế

        $tong_thue 		   	   = (!empty($result['s_tax'])) ? $result['s_tax'] : 0; // tổng thuế

        $sql = "SELECT SUM(ri.ttotal) AS order_total
		        FROM phppos_receivings_items_temp AS ri
		        WHERE ri.suspended = 0 AND ri.tax = 0 AND ri.return = 0 AND ri.store_account_payment = 0";

        $query  = $this->db->query($sql);
        $result = $query->row_array();

        $tong_don_nhap_khong_chiu_thue = (!empty($result['order_total'])) ? $result['order_total'] : 0; // tổng giá trị đơn nhập không chịu thuế

        $sql = "SELECT SUM(ri.ttotal+ri.tax) AS order_total
		        FROM phppos_receivings_items_temp AS ri
		        WHERE ri.suspended = 0 AND ri.tax != 0 AND ri.return = 0 AND ri.store_account_payment = 0";

        $query  = $this->db->query($sql);
        $result = $query->row_array();

        $tong_don_nhap_chiu_thue = (!empty($result['order_total'])) ? $result['order_total'] : 0; // tổng giá trị đơn nhập chịu thuế

        $sql = "SELECT SUM(transaction_amount) AS payment_amount_total, t.payment_type
				FROM phppos_receivings_transactions AS t
				JOIN phppos_receivings as r ON r.receiving_id = t.recv_id
				WHERE r.suspended = 0 AND r.return = 0 AND r.store_account_payment = 0 AND r.deleted = 0
				AND t.datetime >= '$date 00:00:00' AND t.datetime <= '$date 23:59:59'
				AND r.location_id = $location_id
				GROUP BY t.payment_type";

        $query  = $this->db->query($sql);
        $thanh_toan = $query->result_array();

        $sql = "SELECT COUNT(r.receiving_id) AS total
				FROM phppos_receivings as r
				WHERE r.suspended = 0 AND r.return = 0 AND r.store_account_payment = 0 AND r.deleted = 0
				AND r.receiving_time >= '$date 00:00:00' AND r.receiving_time <= '$date 23:59:59'
				AND r.location_id = $location_id";

        $query  = $this->db->query($sql);
        $result = $query->row_array();

        $order_count = $result['total']; // Số đơn đơn nhập

        $nhap_hang['tong_don_nhap'] 				= $tong_don_nhap;
        $nhap_hang['tong_don_nhap_khong_chiu_thue'] = $tong_don_nhap_khong_chiu_thue;
        $nhap_hang['tong_don_nhap_chiu_thue'] 		= $tong_don_nhap_chiu_thue;
        $nhap_hang['tong_don_nhap_bo_thue'] 		= $tong_don_nhap_bo_thue;
        $nhap_hang['tong_thue'] 					= $tong_thue;
        $nhap_hang['thanh_toan']                    = $thanh_toan;
        $nhap_hang['so_luong_giao_dich']            = $order_count;

        return $nhap_hang;
    }

    function get_dat_hang_ncc($arrParams = null) {
        $date        = $arrParams['date'];
        $location_id = $arrParams['location_id'];

        $sql = "SELECT SUM(ri.ttotal+ri.tax) AS order_total, SUM(ri.ttotal) AS sum_ttotal, SUM(ri.tax) AS s_tax
				FROM phppos_receivings_items_temp AS ri
				WHERE ri.suspended = 1";

        $query  			   = $this->db->query($sql);

        $result 		   = $query->row_array();

        $tong_don_nhap 		   = (!empty($result['order_total'])) ? $result['order_total'] : 0; // tổng giá trị đơn nhập
        $tong_don_nhap_bo_thue = (!empty($result['sum_ttotal'])) ? $result['sum_ttotal'] : 0; // tổng giá trị đơn nhập bỏ thuế

        $tong_thue 		   	   = (!empty($result['s_tax'])) ? $result['s_tax'] : 0; // tổng thuế

        $sql = "SELECT SUM(ri.ttotal) AS order_total
		        FROM phppos_receivings_items_temp AS ri
		        WHERE ri.suspended = 1 AND ri.tax = 0";

        $query  = $this->db->query($sql);
        $result = $query->row_array();

        $tong_don_nhap_khong_chiu_thue = (!empty($result['order_total'])) ? $result['order_total'] : 0; // tổng giá trị đơn nhập không chịu thuế

        $sql = "SELECT SUM(ri.ttotal+ri.tax) AS order_total
		        FROM phppos_receivings_items_temp AS ri
		        WHERE ri.suspended = 1 AND ri.tax != 0";

        $query  = $this->db->query($sql);
        $result = $query->row_array();

        $tong_don_nhap_chiu_thue = (!empty($result['order_total'])) ? $result['order_total'] : 0; // tổng giá trị đơn nhập chịu thuế

        $sql = "SELECT SUM(transaction_amount) AS payment_amount_total, t.payment_type
				FROM phppos_receivings_transactions AS t
				JOIN phppos_receivings as r ON r.receiving_id = t.recv_id
				WHERE r.suspended = 1 AND r.deleted = 0
				AND t.datetime >= '$date 00:00:00' AND t.datetime <= '$date 23:59:59'
				AND r.location_id = $location_id
				GROUP BY t.payment_type";

        $query  = $this->db->query($sql);
        $thanh_toan = $query->result_array();

        $sql = "SELECT COUNT(r.receiving_id) AS total
				FROM phppos_receivings as r
				WHERE r.suspended = 1 AND r.deleted = 0
				AND r.receiving_time >= '$date 00:00:00' AND r.receiving_time <= '$date 23:59:59'
				AND r.location_id = $location_id";

        $query  = $this->db->query($sql);
        $result = $query->row_array();

        $order_count = $result['total']; // Số đơn đơn đặt

        $dat_hang_ncc['tong_don_nhap'] 					= $tong_don_nhap;
        $dat_hang_ncc['tong_don_nhap_khong_chiu_thue']  = $tong_don_nhap_khong_chiu_thue;
        $dat_hang_ncc['tong_don_nhap_chiu_thue'] 		= $tong_don_nhap_chiu_thue;
        $dat_hang_ncc['tong_don_nhap_bo_thue'] 			= $tong_don_nhap_bo_thue;
        $dat_hang_ncc['tong_thue'] 						= $tong_thue;
        $dat_hang_ncc['thanh_toan'] 					= $thanh_toan;
        $dat_hang_ncc['so_luong_giao_dich'] 			= $order_count;

        return $dat_hang_ncc;
    }

    function get_tra_hang_ncc($arrParams = null) {
        $date        = $arrParams['date'];
        $location_id = $arrParams['location_id'];

        $sql = "SELECT SUM(ri.ttotal+ri.tax) AS order_total, SUM(ri.ttotal) AS sum_ttotal, SUM(ri.tax) AS s_tax
				FROM phppos_receivings_items_temp AS ri
				WHERE ri.return = 1";

        $query  			   = $this->db->query($sql);

        $result 		   = $query->row_array();

        $tong_don_nhap 		   = (!empty($result['order_total'])) ? $result['order_total'] : 0; // tổng giá trị đơn nhập
        $tong_don_nhap 		   = abs($tong_don_nhap);

        $tong_don_nhap_bo_thue = (!empty($result['sum_ttotal'])) ? $result['sum_ttotal'] : 0; // tổng giá trị đơn nhập bỏ thuế
        $tong_don_nhap_bo_thue = abs($tong_don_nhap_bo_thue);

        $tong_thue 		   	   = (!empty($result['s_tax'])) ? $result['s_tax'] : 0; // tổng thuế
        $tong_thue 			   = abs($tong_thue);

        $sql = "SELECT SUM(ri.ttotal) AS order_total
		        FROM phppos_receivings_items_temp AS ri
		        WHERE ri.return = 1 AND ri.tax = 0";

        $query  = $this->db->query($sql);
        $result = $query->row_array();

        $tong_don_nhap_khong_chiu_thue = (!empty($result['order_total'])) ? $result['order_total'] : 0; // tổng giá trị đơn nhập không chịu thuế
        $tong_don_nhap_khong_chiu_thue = abs($tong_don_nhap_khong_chiu_thue);

        $sql = "SELECT SUM(ri.ttotal+ri.tax) AS order_total
		        FROM phppos_receivings_items_temp AS ri
		        WHERE ri.return = 1 AND ri.tax != 0";

        $query  = $this->db->query($sql);
        $result = $query->row_array();

        $tong_don_nhap_chiu_thue = (!empty($result['order_total'])) ? $result['order_total'] : 0; // tổng giá trị đơn nhập chịu thuế
        $tong_don_nhap_chiu_thue = abs($tong_don_nhap_chiu_thue);

        $sql = "SELECT SUM(transaction_amount) AS payment_amount_total, t.payment_type
				FROM phppos_receivings_transactions AS t
				JOIN phppos_receivings as r ON r.receiving_id = t.recv_id
				WHERE r.return = 1 AND r.deleted = 0
				AND t.datetime >= '$date 00:00:00' AND t.datetime <= '$date 23:59:59'
				AND r.location_id = $location_id
				GROUP BY t.payment_type";

        $query  = $this->db->query($sql);
        $thanh_toan = $query->result_array();

        $sql = "SELECT COUNT(r.receiving_id) AS total
				FROM phppos_receivings as r
				WHERE r.return = 1 AND r.deleted = 0
				AND r.receiving_time >= '$date 00:00:00' AND r.receiving_time <= '$date 23:59:59'
				AND r.location_id = $location_id";

        $query  = $this->db->query($sql);
        $result = $query->row_array();

        $order_count = $result['total']; // Số lượng đơn

        $tra_hang_ncc['tong_don_nhap'] 					= $tong_don_nhap;
        $tra_hang_ncc['tong_don_nhap_khong_chiu_thue']  = $tong_don_nhap_khong_chiu_thue;
        $tra_hang_ncc['tong_don_nhap_chiu_thue'] 		= $tong_don_nhap_chiu_thue;
        $tra_hang_ncc['tong_don_nhap_bo_thue'] 			= $tong_don_nhap_bo_thue;
        $tra_hang_ncc['tong_thue'] 						= $tong_thue;
        $tra_hang_ncc['thanh_toan']                     = $thanh_toan;
        $tra_hang_ncc['so_luong_giao_dich']             = $order_count;

        return $tra_hang_ncc;
    }

    function get_cong_no_khach_hang($arrParams = null) {
        $customer_model = $this->model_load_model('Customer');
        $date           = $arrParams['date'];
        $location_id    = $arrParams['location_id'];

        // khách hàng ghi nợ
        $sql = "SELECT SUM(payment_amount) AS  payment_amount_total
				FROM phppos_sales_payments AS p
				JOIN phppos_sales as s ON s.sale_id = p.sale_id
				WHERE ((s.suspended = 1 AND s.was_layaway = 1) OR s.suspended = 0) AND s.deleted = 0
				AND p.payment_type = 'Sổ ghi nợ'
				AND s.sale_time >= '$date 00:00:00' AND s.sale_time <= '$date 23:59:59'
				AND s.location_id = $location_id";

        $query  = $this->db->query($sql);
        $result = $query->row_array();

        $ghi_no_tai_khoan_1 = (!empty($result['payment_amount_total'])) ? $result['payment_amount_total'] : 0;

        $sql = "SELECT SUM(p.payment_amount) AS  payment_amount_total, p.payment_type
				FROM phppos_sales_payments AS p
				JOIN phppos_sales as s ON s.sale_id = p.sale_id
				WHERE s.store_account_payment = 1 AND s.deleted = 0
				AND p.payment_date >= '$date 00:00:00' AND p.payment_date <= '$date 23:59:59'
				AND s.location_id = $location_id
				GROUP BY p.payment_type";

        $query  	= $this->db->query($sql);
        $thanh_toan_tai_khoan_1_bang_hoa_don = $query->result_array();

        $total = 0;
        $hinh_thuc_thanh_toan_tai_khoan_1 = array();
        if(!empty($thanh_toan_tai_khoan_1_bang_hoa_don)) {
        	foreach($thanh_toan_tai_khoan_1_bang_hoa_don as $payment) {
        		$total = $total + $payment['payment_amount_total'];
                $hinh_thuc_thanh_toan_tai_khoan_1[$payment['payment_type']] = $payment['payment_amount_total'];
        	}
        }

        $tong_so_du_tai_khoan_1 = $customer_model->sum_balance('balance');

        $cong_no_khach_hang['ghi_no_tai_khoan_1'] 		         = $ghi_no_tai_khoan_1;
        $cong_no_khach_hang['thanh_toan_tai_khoan_1']            = $total;
        $cong_no_khach_hang['hinh_thuc_thanh_toan_tai_khoan_1']  = $hinh_thuc_thanh_toan_tai_khoan_1;
        $cong_no_khach_hang['tong_so_du_tai_khoan_1']            = $tong_so_du_tai_khoan_1;

        // nợ khách hàng
        $sql = "SELECT SUM(payment_amount) AS  payment_amount_total
				FROM phppos_sales_payments AS p
				JOIN phppos_sales as s ON s.sale_id = p.sale_id
				WHERE s.return = 1 AND s.deleted = 0
				AND p.payment_type = 'Sổ ghi nợ'
				AND s.sale_time >= '$date 00:00:00' AND s.sale_time <= '$date 23:59:59'
				AND s.location_id = $location_id";

        $query  = $this->db->query($sql);
        $result = $query->row_array();

        $ghi_no_tai_khoan_2 = (!empty($result['payment_amount_total'])) ? $result['payment_amount_total'] : 0;

        // nợ do nhận tiền trả dư
        $sql = "SELECT SUM(si.ttotal+si.tax) AS order_total, si.sale_id
                FROM phppos_sales_items_temp AS si
                WHERE ((si.suspended = 1 AND si.was_layaway = 1) OR si.suspended = 0) AND si.store_account_payment = 0 AND si.is_vat = 0
				GROUP BY si.sale_id";

        $query            = $this->db->query($sql);
        $result_tmp       = $query->result_array();
        $sale_order_value = array();
        if(!empty($result_tmp)) {
            foreach($result_tmp as $val) {
                $sale_order_value[$val['sale_id']] = $val['order_total'];
            }
        }

        $sql = "SELECT SUM(p.payment_amount) AS  payment_amount_total, s.sale_id
				FROM phppos_sales_payments AS p
				JOIN phppos_sales as s ON s.sale_id = p.sale_id
				WHERE ((s.suspended = 1 AND s.was_layaway = 1) OR s.suspended = 0) AND s.store_account_payment = 0 AND s.is_vat = 0 AND s.deleted = 0 AND p.payment_type <> 'Tiền dư cho vào TK nợ khách'
				AND p.payment_date >= '$date 00:00:00' AND p.payment_date <= '$date 23:59:59'
				AND s.location_id = $location_id
				GROUP BY s.sale_id";

        $query            = $this->db->query($sql);
        $result_tmp       = $query->result_array();
        $sale_order_payment_value = array();
        if(!empty($result_tmp)) {
            foreach($result_tmp as $val) {
                $sale_order_payment_value[$val['sale_id']] = $val['payment_amount_total'];
            }
        }


        $tien_tra_du = 0;
        if(!empty($sale_order_value)) {
            foreach($sale_order_value as $sale_id => $amount) {
                if(isset($sale_order_payment_value[$sale_id]) && $sale_order_payment_value[$sale_id] > $amount) {
                    $tien_tra_du = $tien_tra_du + $sale_order_payment_value[$sale_id] - $amount;
                }
            }
        }
        $ghi_no_tai_khoan_2 = $ghi_no_tai_khoan_2 + $tien_tra_du;

        $sql = "SELECT SUM(p.payment_amount) AS  payment_amount_total, p.payment_type
				FROM phppos_sales_payments AS p
				JOIN phppos_sales as s ON s.sale_id = p.sale_id
				WHERE (s.store_account_payment = 2 OR s.is_vat = 1) AND s.deleted = 0
				AND p.payment_date >= '$date 00:00:00' AND p.payment_date <= '$date 23:59:59'
				AND s.location_id = $location_id
				GROUP BY p.payment_type";

        $query  	= $this->db->query($sql);
        $thanh_toan_tai_khoan_2_bang_hoa_don = $query->result_array();

        $total = 0;
        $hinh_thuc_thanh_toan_tai_khoan_2 = array();
        if(!empty($thanh_toan_tai_khoan_2_bang_hoa_don)) {
            foreach($thanh_toan_tai_khoan_2_bang_hoa_don as $payment) {
                $total = $total + $payment['payment_amount_total'];
                $hinh_thuc_thanh_toan_tai_khoan_2[$payment['payment_type']] = $payment['payment_amount_total'];
            }
        }

        $sql = "SELECT SUM(p.payment_amount) AS  payment_amount_total
				FROM phppos_sales_payments AS p
				JOIN phppos_sales as s ON s.sale_id = p.sale_id
				WHERE s.is_vat = 1 AND s.deleted = 0
				AND p.payment_date >= '$date 00:00:00' AND p.payment_date <= '$date 23:59:59'
				AND s.location_id = $location_id
				AND p.payment_type = 'Tiền mặt'";

        $query  	= $this->db->query($sql);
        $result_tmp = $query->row_array();
        $tien_mat_vat 		   = (!empty($result_tmp['payment_amount_total'])) ? $result_tmp['payment_amount_total'] : 0;

        $tong_so_du_tai_khoan_2 = $customer_model->sum_balance('balance_2');

        $cong_no_khach_hang['ghi_no_tai_khoan_2'] 		         = $ghi_no_tai_khoan_2;
        $cong_no_khach_hang['thanh_toan_tai_khoan_2']            = $total;
        $cong_no_khach_hang['hinh_thuc_thanh_toan_tai_khoan_2']  = $hinh_thuc_thanh_toan_tai_khoan_2;
        $cong_no_khach_hang['tong_so_du_tai_khoan_2']            = $tong_so_du_tai_khoan_2;
        $cong_no_khach_hang['tien_mat_vat']                      = $tien_mat_vat;

        return $cong_no_khach_hang;
    }

	function get_cong_no_ncc($arrParams = null) {
		$date        = $arrParams['date'];
		$location_id = $arrParams['location_id'];

		// nợ nhà cung cấp
		$sql = "SELECT SUM(t.transaction_amount) AS  payment_amount_total
				FROM phppos_receivings_transactions AS t
				JOIN phppos_receivings as r ON t.recv_id = r.receiving_id
				WHERE ((r.suspended = 0 AND r.return = 0 AND r.store_account_payment = 0) OR (r.suspended = 1)) AND r.deleted = 0
				AND t.payment_type = 'Sổ ghi nợ'
				AND t.datetime >= '$date 00:00:00' AND t.datetime <= '$date 23:59:59'
				AND r.location_id = $location_id";

		$query  = $this->db->query($sql);
		$result = $query->row_array();

		$ghi_no_tai_khoan_1 = (!empty($result['payment_amount_total'])) ? $result['payment_amount_total'] : 0;

		$sql = "SELECT SUM(t.transaction_amount) AS  payment_amount_total, t.payment_type
				FROM phppos_receivings_transactions AS t
				JOIN phppos_receivings as r ON t.recv_id = r.receiving_id
				WHERE r.store_account_payment = 1 AND r.deleted = 0
				AND t.datetime >= '$date 00:00:00' AND t.datetime <= '$date 23:59:59'
				AND r.location_id = $location_id
				GROUP BY t.payment_type";

		$query  	= $this->db->query($sql);
		$thanh_toan_tai_khoan_1_bang_hoa_don = $query->result_array();

        $total = 0;
        $hinh_thuc_thanh_toan_tai_khoan_1 = array();
        if(!empty($thanh_toan_tai_khoan_1_bang_hoa_don)) {
        	foreach($thanh_toan_tai_khoan_1_bang_hoa_don as $payment) {
        		$total = $total + $payment['payment_amount_total'];
                $hinh_thuc_thanh_toan_tai_khoan_1[$payment['payment_type']] = $payment['payment_amount_total'];
        	}
        }

		$supplier_model = $this->model_load_model('Supplier');
        $tong_so_du_tai_khoan_1 = $supplier_model->sum_balance('balance');

		    $cong_no_ncc['ghi_no_tai_khoan_1'] 		          = $ghi_no_tai_khoan_1;
        $cong_no_ncc['thanh_toan_tai_khoan_1']            = $total;
        $cong_no_ncc['hinh_thuc_thanh_toan_tai_khoan_1']  = $hinh_thuc_thanh_toan_tai_khoan_1;
        $cong_no_ncc['tong_so_du_tai_khoan_1']            = $tong_so_du_tai_khoan_1;

        // nhà cung cấp nợ
        $sql = "SELECT SUM(t.transaction_amount) AS  payment_amount_total
				FROM phppos_receivings_transactions AS t
				JOIN phppos_receivings as r ON t.recv_id = r.receiving_id
				WHERE (r.return = 1 OR t.payment_type = '".lang('common_debt_supplier')."') 
				AND r.deleted = 0
				AND t.datetime >= '$date 00:00:00' AND t.datetime <= '$date 23:59:59'
				AND r.location_id = $location_id";

        $query  = $this->db->query($sql);
        $result = $query->row_array();

				
        $ghi_no_tai_khoan_2 = (!empty($result['payment_amount_total'])) ? $result['payment_amount_total'] : 0;

        $sql = "SELECT SUM(t.transaction_amount) AS  payment_amount_total, t.payment_type
				FROM phppos_receivings_transactions AS t
				JOIN phppos_receivings as r ON t.recv_id = r.receiving_id
				WHERE r.store_account_payment = 2 AND r.deleted = 0
				AND t.datetime >= '$date 00:00:00' AND t.datetime <= '$date 23:59:59'
				AND r.location_id = $location_id
				GROUP BY t.payment_type";

        $query  	= $this->db->query($sql);
        $thanh_toan_tai_khoan_2_bang_hoa_don = $query->result_array();

        $total = 0;
        $hinh_thuc_thanh_toan_tai_khoan_2 = array();
        if(!empty($thanh_toan_tai_khoan_2_bang_hoa_don)) {
            foreach($thanh_toan_tai_khoan_2_bang_hoa_don as $payment) {
                $total = $total + $payment['payment_amount_total'];
                $hinh_thuc_thanh_toan_tai_khoan_2[$payment['payment_type']] = $payment['payment_amount_total'];
            }
        }

        $supplier_model = $this->model_load_model('Supplier');
        $tong_so_du_tai_khoan_2 = $supplier_model->sum_balance('balance_2');

        $cong_no_ncc['ghi_no_tai_khoan_2'] 		          = $ghi_no_tai_khoan_2;
        $cong_no_ncc['thanh_toan_tai_khoan_2']            = $total;
        $cong_no_ncc['hinh_thuc_thanh_toan_tai_khoan_2']  = $hinh_thuc_thanh_toan_tai_khoan_2;
        $cong_no_ncc['tong_so_du_tai_khoan_2']            = $tong_so_du_tai_khoan_2;

		return $cong_no_ncc;
	}

    function get_chi($arrParams = null) {
        $date        = $arrParams['date'];
        $location_id = $arrParams['location_id'];

        // chi
        $this->db->select("SUM((e.expense_amount+e.expense_tax)) AS expense_amount_total, c.name")
                ->from('expenses AS e')
                ->join('categories AS c', 'e.category_id = c.id', 'left')
                ->where('e.deleted', 0)
                ->where("e.expense_date >= '$date 00:00:00'")
                ->where("e.expense_date <= '$date 23:59:59'")
                ->where('e.location_id', $location_id)
                ->where('e.expense_type', 1)
                ->group_by('c.name');

        $query = $this->db->get();
        $chi['list'] = $query->result_array();

        $this->db->flush_cache();

        $total = 0;
        if(!empty($chi['list'])) {
            foreach($chi['list'] as $val)
                $total = $total + $val['expense_amount_total'];
        }

        $chi['total'] = $total;

        //chi tiền mặt
        $this->db->select("SUM((e.expense_amount+e.expense_tax)) AS expense_amount_total")
                ->from('expenses AS e')
                ->where('e.deleted', 0)
                ->where("e.expense_date >= '$date 00:00:00'")
                ->where("e.expense_date <= '$date 23:59:59'")
                ->where('e.location_id', $location_id)
                ->where('e.expense_type', 1)
                ->where("e.payment_type = 'Tiền mặt'");

        $query = $this->db->get();
        $result_tmp = $query->row_array();

        $this->db->flush_cache();

        $chi['tien_mat']  =  (!empty($result_tmp['expense_amount_total'])) ? $result_tmp['expense_amount_total'] : 0;

        return $chi;
    }

    function get_thu($arrParams = null) {
        $date        = $arrParams['date'];
        $location_id = $arrParams['location_id'];

        // thu
        $this->db->select("SUM((e.expense_amount+e.expense_tax)) AS expense_amount_total, c.name")
                ->from('expenses AS e')
                ->join('categories AS c', 'e.category_id = c.id', 'left')
                ->where('e.deleted', 0)
                ->where("e.expense_date >= '$date 00:00:00'")
                ->where("e.expense_date <= '$date 23:59:59'")
                ->where('e.location_id', $location_id)
                ->where('e.expense_type', -1)
                ->group_by('c.name');

        $query = $this->db->get();
        $thu['list'] = $query->result_array();

        $this->db->flush_cache();

        $total = 0;
        if(!empty($thu['list'])) {
            foreach($thu['list'] as $val)
                $total = $total + $val['expense_amount_total'];
        }

        $thu['total'] = $total;

        //thu tiền mặt
        $this->db->select("SUM((e.expense_amount+e.expense_tax)) AS expense_amount_total")
                 ->from('expenses AS e')
                 ->where('e.deleted', 0)
                 ->where("e.expense_date >= '$date 00:00:00'")
                 ->where("e.expense_date <= '$date 23:59:59'")
                 ->where('e.location_id', $location_id)
                 ->where('e.expense_type', -1)
                 ->where("e.payment_type = 'Tiền mặt'");

        $query = $this->db->get();
        $result_tmp = $query->row_array();

        $this->db->flush_cache();

        $thu['tien_mat']  =  (!empty($result_tmp['expense_amount_total'])) ? $result_tmp['expense_amount_total'] : 0;

        return $thu;
    }

    function get_theo_doi_so_giao_ca($arrParams = null) {
        $register_model = $this->model_load_model('Register');
        $result_tmp = $register_model->get_register_log_from_current_location($arrParams);
        $result = array();
        if(!empty($result_tmp)) {
            foreach($result_tmp as $val) {
                $content = array();
                $content[] = 'Tổng số tiền bắt đầu: ' . to_currency($val['open_amount']);
                $content[] = 'Tổng số tiền chốt sổ: ' . to_currency($val['close_amount']);
                $content[] = 'Tiền mặt bán hàng: ' . to_currency($val['cash_sales_amount']);
                $content[] = 'Bổ sung tiền mặt: ' . to_currency($val['total_cash_additions']);
                $content[] = 'Giảm trừ tiền mặt: ' . to_currency($val['total_cash_subtractions']);
                if(!empty($val['cost_name']))
                    $content[] = $val['cost_name'] . ' : ' . to_currency($val['cost']);

                $chenh_lech = $val['cash_sales_amount'] - $val['open_amount'] + $val['total_cash_additions'] - $val['total_cash_subtractions'] - $val['cost'] - $val['close_amount'];

				$content[] = 'Chênh lệch: ' . to_currency($chenh_lech);
                $content = implode('<br />', $content);

                $result[] = array(
                    'name' => '<h2>'.$val['name'].' ('.$val['address'].')</h2> '.$val['shift_start_format'].' - ' . $val['shift_end_format'],
                    'content' => $content
                );
            }
        }

        return $result;
    }

	function get_thanh_toan_ban_hang($arrParams) {
		$date        = $arrParams['date'];
		$location_id = $arrParams['location_id'];

		$sql = "SELECT SUM(payment_amount) AS  payment_amount_total, p.payment_type
				FROM phppos_sales_payments AS p
				JOIN phppos_sales as s ON s.sale_id = p.sale_id
				WHERE s.store_account_payment = 0 AND s.suspended != 2
				AND p.payment_date >= '$date 00:00:00' AND p.payment_date <= '$date 23:59:59'
				AND s.location_id = $location_id
				GROUP BY p.payment_type";

		$query  = $this->db->query($sql);
		$result = $query->result_array();

		return $result;
	}

	function get_thanh_toan_nhap_hang($arrParams) {
		$date        = $arrParams['date'];
		$location_id = $arrParams['location_id'];

		$sql = "SELECT SUM(transaction_amount) AS payment_amount_total, t.payment_type
				FROM phppos_receivings_transactions AS t
				JOIN phppos_receivings as r ON r.receiving_id = t.recv_id
				WHERE r.store_account_payment = 0
				AND t.datetime >= '$date 00:00:00' AND t.datetime <= '$date 23:59:59'
				AND r.location_id = $location_id
				GROUP BY t.payment_type";

		$query  = $this->db->query($sql);
		$result = $query->result_array();

		return $result;
	}

	function get_loi_nhuan($arrParams = null) {
		$date        = $arrParams['date'];
		$location_id = $arrParams['location_id'];

		$this->db->select("SUM(ttotal-tcost) AS loi_nhuan_gop")
				 ->from('sales_items_temp')
				 ->where('suspended', 0)
				 ->where('store_account_payment', 0)
                 ->where('is_vat',0);

		$query = $this->db->get();

		$result_tmp = $query->row_array();

		$this->db->flush_cache();

		if(!empty($result_tmp)) {
			$loi_nhuan_gop = $result_tmp['loi_nhuan_gop'];
		}else
			$loi_nhuan_gop = 0;


		$this->db->select("SUM(p.payment_amount) AS the_qua_tang")
				->from('sales_payments AS p')
				->join('sales AS s', 's.sale_id = p.sale_id')
				->where("p.payment_type LIKE '%quà%'")
				->where("s.sale_time >= '$date 00:00:00'")
				->where("s.sale_time <= '$date 23:59:59'")
				->where('s.location_id', $location_id)
				->where('s.suspended', 0)
				->where('s.store_account_payment', 0)
                ->where('s.is_vat', 0)
				->where('s.deleted', 0);

		$query = $this->db->get();

		$result_tmp = $query->row_array();
		$this->db->flush_cache();

		if(!empty($result_tmp)) {
			$the_qua_tang = $result_tmp['the_qua_tang'];
		}else
			$the_qua_tang = 0;

		$this->db->select("SUM(p.payment_amount) AS diem")
				->from('sales_payments AS p')
				->join('sales AS s', 's.sale_id = p.sale_id')
				->where("p.payment_type = 'Điểm'")
				->where("s.sale_time >= '$date 00:00:00'")
				->where("s.sale_time <= '$date 23:59:59'")
				->where('s.location_id', $location_id)
				->where('s.suspended', 0)
				->where('s.store_account_payment', 0)
                ->where('s.is_vat', 0)
				->where('s.deleted', 0);

		$query = $this->db->get();

		$result_tmp = $query->row_array();
		$this->db->flush_cache();

		if(!empty($result_tmp)) {
			$diem = $result_tmp['diem'];
		}else
			$diem = 0;

		$this->db -> select("SUM((e.expense_amount+e.expense_tax)*e.expense_type) AS expense_amount_total")
			      -> from('expenses AS e')
			      ->where("e.expense_date >= '$date 00:00:00'")
			      ->where("e.expense_date <= '$date 23:59:59'")
			      ->where('e.location_id', $location_id)
			      ->where('e.deleted', 0);

		$query = $this->db->get();

		$result_tmp = $query->row_array();
		$this->db->flush_cache();

		if(!empty($result_tmp)) {
			$chi_phi = $result_tmp['expense_amount_total'];
		}else
			$chi_phi = 0;

		$hoa_hong = 0;

		$result = $loi_nhuan_gop - $the_qua_tang - $chi_phi - $hoa_hong;

		return $result;
	}


    function create_report($params) {
		$payment_type_list = array();
        $tong_tien_mat = 0;
		$refund_money_total = 0;

		// bán hàng
		$loi_nhuan = $this->get_loi_nhuan($params);
		$ban_hang = $this->get_ban_hang($params);

        $resport[] = array(
            'left' => '<h1>Bán hàng</h1>',
            'right' => '--'
        );

        $resport[] = array(
            'left' => 'Tổng đơn bán hàng',
            'right' => to_currency($ban_hang['tong_don_hang'])
        );

        $resport[] = array(
            'left' => 'Tổng đơn bán hàng không chịu thuế',
            'right' => to_currency($ban_hang['tong_don_hang_khong_chiu_thue'])
        );

        $resport[] = array(
            'left' => 'Tổng đơn bán hàng chịu thuế',
            'right' => to_currency($ban_hang['tong_don_hang_chiu_thue'])
        );

        $resport[] = array(
            'left' => 'Tổng đơn bán hàng bỏ thuế',
            'right' => to_currency($ban_hang['tong_don_hang_bo_thue'])
        );

        $resport[] = array(
            'left' => 'Tổng thuế',
            'right' => to_currency($ban_hang['tong_thue'])
        );

        if($ban_hang['hoa_hong'] > 0) {
            $resport[] = array(
                'left' => 'Hoa hồng',
                'right' => to_currency($ban_hang['hoa_hong'])
            );
        }
        $resport[] = array(
            'left' => 'Số lượng giao dịch',
            'right' => $ban_hang['so_luong_giao_dich']
        );

        $resport[] = array(
            'left' => 'Số tiền TB / 1 đơn hàng',
            'right' => to_currency($ban_hang['trung_binh_don_hang'])
        );
        if(!empty($ban_hang['thanh_toan'])) {
            foreach($ban_hang['thanh_toan'] as $val) {
                if(($val['payment_type'] != lang('common_refund_money')) && ($val['payment_type'] != 'Tiền mặt')) {
                      $resport[] = array(
                            'left' => $val['payment_type'],
                            'right' => to_currency($val['payment_amount_total'])
                        );
                }
                if($val['payment_type'] == lang('common_refund_money')) {
                      $refund_money_total = $refund_money_total + $val['payment_amount_total'];
                }
                if($val['payment_type'] == 'Tiền mặt') {
                    $tong_tien_mat = $tong_tien_mat + $val['payment_amount_total'];
                }
            }
                $tong_tien_mat = $tong_tien_mat +  $refund_money_total;
                    $resport[] = array(
                        'left' => 'Tiền mặt',
                        'right' => to_currency($tong_tien_mat )
                    );
                
        }

        $resport[] = array(
            'left' => '&nbsp',
            'right' => '&nbsp'
        );

        // đặt hàng
        $dat_hang = $this->get_dat_hang($params);
        $resport[] = array(
            'left' => 'Tổng đơn đặt hàng',
            'right' => to_currency($dat_hang['tong_don_hang'])
        );

        $resport[] = array(
            'left' => 'Tổng thuế',
            'right' => to_currency($dat_hang['tong_thue'])
        );

        $resport[] = array(
            'left' => 'Số lượng đơn đặt',
            'right' => $dat_hang['so_luong_giao_dich']
        );

        $resport[] = array(
            'left' => 'Số tiền TB/ 1 đơn hàng',
            'right' => to_currency($dat_hang['trung_binh_don_hang'])
        );

        if(!empty($dat_hang['thanh_toan'])) {
            foreach($dat_hang['thanh_toan'] as $val) {
                $resport[] = array(
                    'left' => $val['payment_type'],
                    'right' => to_currency($val['payment_amount_total'])
                );

                if($val['payment_type'] == 'Tiền mặt')
                    $tong_tien_mat = $tong_tien_mat + $val['payment_amount_total'];
            }
        }

        $resport[] = array(
            'left' => '&nbsp',
            'right' => '&nbsp'
        );

		// trả hàng
        $tra_hang = $this->get_tra_hang($params);

        $resport[] = array(
            'left' => 'Tổng đơn trả hàng',
            'right' => to_currency($tra_hang['tong_don_hang'])
        );

        $resport[] = array(
            'left' => 'Tổng đơn trả hàng không chịu thuế',
            'right' => to_currency($tra_hang['tong_don_hang_khong_chiu_thue'])
        );

        $resport[] = array(
            'left' => 'Tổng đơn trả hàng chịu thuế',
            'right' => to_currency($tra_hang['tong_don_hang_chiu_thue'])
        );

        $resport[] = array(
            'left' => 'Tổng đơn trả hàng bỏ thuế',
            'right' => to_currency($tra_hang['tong_don_hang_bo_thue'])
        );

        $resport[] = array(
            'left' => 'Tổng thuế',
            'right' => to_currency($tra_hang['tong_thue'])
        );

        $resport[] = array(
            'left' => 'Số lượng đơn trả',
            'right' => $tra_hang['so_luong_giao_dich']
        );

        if(!empty($tra_hang['thanh_toan'])) {
            foreach($tra_hang['thanh_toan'] as $val) {
                $resport[] = array(
                    'left' => $val['payment_type'],
                    'right' => to_currency($val['payment_amount_total'])
                );

                if($val['payment_type'] == 'Tiền mặt')
                    $tong_tien_mat = $tong_tien_mat - $val['payment_amount_total'];
            }
        }

        $resport[] = array(
			'left' => '&nbsp',
			'right' => '&nbsp'
		);

		$resport[] = array(
            'left' => '<h1>Nhập hàng</h1>',
            'right' => '--'
        );

        $nhap_hang    = $this->get_nhap_hang($params);

        $resport[] = array(
            'left' => 'Tổng đơn nhập hàng',
            'right' => to_currency($nhap_hang['tong_don_nhap'])
        );

        $resport[] = array(
            'left' => 'Tổng đơn nhập hàng không chịu thuế',
            'right' => to_currency($nhap_hang['tong_don_nhap_khong_chiu_thue'])
        );

        $resport[] = array(
            'left' => 'Tổng đơn nhập hàng chịu thuế',
            'right' => to_currency($nhap_hang['tong_don_nhap_chiu_thue'])
        );

        $resport[] = array(
            'left' => 'Tổng đơn nhập hàng bỏ thuế',
            'right' => to_currency($nhap_hang['tong_don_nhap_bo_thue'])
        );

        $resport[] = array(
            'left' => 'Tổng thuế',
            'right' => to_currency($nhap_hang['tong_thue'])
        );

        $resport[] = array(
            'left' => 'Số lượng đơn nhập',
            'right' => $nhap_hang['so_luong_giao_dich']
        );

        if(!empty($nhap_hang['thanh_toan'])) {
            foreach($nhap_hang['thanh_toan'] as $val) {
                if($val['payment_type'] != lang('common_refund_from_supplier') && $val['payment_type'] != 'Tiền mặt' ) {
                    $resport[] = array(
                        'left' => $val['payment_type'],
                        'right' => to_currency($val['payment_amount_total'])
                    );

                }
                if($val['payment_type'] == lang('common_refund_from_supplier')) {
                      $refund_money_total = $refund_money_total + $val['payment_amount_total'];
                }
                if($val['payment_type'] == 'Tiền mặt')
                    $tong_tien_mat = $tong_tien_mat + $val['payment_amount_total'];
            }
						$tong_tien_mat = $tong_tien_mat +  $refund_money_total;
						$resport[] = array(
                    'left' => 'Tiền mặt',
                    'right' => to_currency($tong_tien_mat)
                );
						
        }

        $resport[] = array(
            'left' => '&nbsp',
            'right' => '&nbsp'
        );

        $dat_hang_ncc = $this->get_dat_hang_ncc($params);
        $resport[] = array(
            'left' => 'Tổng đơn đặt hàng',
            'right' => to_currency($dat_hang_ncc['tong_don_nhap'])
        );

        $resport[] = array(
            'left' => 'Tổng thuế',
            'right' => to_currency($dat_hang_ncc['tong_thue'])
        );

        $resport[] = array(
            'left' => 'Số lượng đơn đặt',
            'right' => $dat_hang_ncc['so_luong_giao_dich']
        );

        if(!empty($dat_hang_ncc['thanh_toan'])) {
            foreach($dat_hang_ncc['thanh_toan'] as $val) {
                $resport[] = array(
                    'left' => $val['payment_type'],
                    'right' => to_currency($val['payment_amount_total'])
                );

                if($val['payment_type'] == 'Tiền mặt')
                    $tong_tien_mat = $tong_tien_mat - $val['payment_amount_total'];
            }
        }

        $resport[] = array(
            'left' => '&nbsp',
            'right' => '&nbsp'
        );

        $tra_hang_ncc = $this->get_tra_hang_ncc($params);
        $resport[] = array(
            'left' => 'Tổng đơn trả hàng',
            'right' => to_currency($tra_hang_ncc['tong_don_nhap'])
        );

        $resport[] = array(
            'left' => 'Tổng đơn trả hàng không chịu thuế',
            'right' => to_currency($tra_hang_ncc['tong_don_nhap_khong_chiu_thue'])
        );

        $resport[] = array(
            'left' => 'Tổng đơn trả hàng chịu thuế',
            'right' => to_currency($tra_hang_ncc['tong_don_nhap_chiu_thue'])
        );

        $resport[] = array(
            'left' => 'Tổng đơn trả hàng bỏ thuế',
            'right' => to_currency($tra_hang_ncc['tong_don_nhap_bo_thue'])
        );

        $resport[] = array(
            'left' => 'Tổng thuế',
            'right' => to_currency($tra_hang_ncc['tong_thue'])
        );

        $resport[] = array(
            'left' => 'Số lượng đơn trả hàng',
            'right' => $tra_hang_ncc['so_luong_giao_dich']
        );

        if(!empty($tra_hang_ncc['thanh_toan'])) {
            foreach($tra_hang_ncc['thanh_toan'] as $val) {
                $resport[] = array(
                    'left' => $val['payment_type'],
                    'right' => to_currency($val['payment_amount_total'])
                );

                if($val['payment_type'] == 'Tiền mặt')
                    $tong_tien_mat = $tong_tien_mat + $val['payment_amount_total'];
            }
        }

        $resport[] = array(
            'left' => '&nbsp',
            'right' => '&nbsp'
        );

        $cong_no_khach_hang = $this->get_cong_no_khach_hang($params);


        $resport[] = array(
            'left' => '<h1>Công nợ khách hàng</h1>',
            'right' => '--'
        );

        $resport[] = array(
            'left' => '<span class="bold">'.$this->config->item('customer_balance').'</span>',
            'right' => '--'
        );

        $resport[] = array(
            'left' => 'Ghi nợ',
            'right' => to_currency($cong_no_khach_hang['ghi_no_tai_khoan_1'])
        );

        $resport[] = array(
            'left' => 'Thanh toán',
            'right' => to_currency($cong_no_khach_hang['thanh_toan_tai_khoan_1'])
        );

        if(!empty($cong_no_khach_hang['hinh_thuc_thanh_toan_tai_khoan_1'])) {
            foreach($cong_no_khach_hang['hinh_thuc_thanh_toan_tai_khoan_1'] as $key => $amount) {
                $resport[] = array(
                    'left' => '&nbsp&nbsp&nbsp' . $key,
                    'right' => to_currency($amount)
                );

                if($key == 'Tiền mặt')
                    $tong_tien_mat = $tong_tien_mat + $amount;
            }
        }

        $resport[] = array(
            'left' => 'Tổng số dư',
            'right' => to_currency($cong_no_khach_hang['tong_so_du_tai_khoan_1'])
        );

        $resport[] = array(
            'left' => '<span class="bold">'.$this->config->item('customer_balance_2').'</span>',
            'right' => '--'
        );

        $resport[] = array(
            'left' => 'Ghi nợ',
            'right' => to_currency($cong_no_khach_hang['ghi_no_tai_khoan_2'])
        );

        $resport[] = array(
            'left' => 'Thanh toán',
            'right' => to_currency($cong_no_khach_hang['thanh_toan_tai_khoan_2'])
        );

        if(!empty($cong_no_khach_hang['hinh_thuc_thanh_toan_tai_khoan_2'])) {
            foreach($cong_no_khach_hang['hinh_thuc_thanh_toan_tai_khoan_2'] as $key => $amount) {
                $resport[] = array(
                    'left' => '&nbsp&nbsp' . $key,
                    'right' => to_currency($amount)
                );

                if($key == 'Tiền mặt'){
                    $tong_tien_mat = $tong_tien_mat - $amount;
                }

            }
        }

        $tong_tien_mat = $tong_tien_mat + $cong_no_khach_hang['tien_mat_vat'];

        $resport[] = array(
            'left' => 'Tổng số dư',
            'right' => to_currency($cong_no_khach_hang['tong_so_du_tai_khoan_2'])
        );

		$resport[] = array(
			'left' => '&nbsp',
			'right' => '&nbsp'
		);

		$cong_no_ncc = $this->get_cong_no_ncc($params);

		$resport[] = array(
			'left' => '<h1>Công nợ Nhà cung cấp</h1>',
			'right' => '--'
		);

        $resport[] = array(
            'left' => '<span class="bold">'.$this->config->item('supplier_balance').'</span>',
            'right' => '--'
        );

        $resport[] = array(
            'left' => 'Ghi nợ',
            'right' => to_currency($cong_no_ncc['ghi_no_tai_khoan_1'])
        );

        $resport[] = array(
            'left' => 'Thanh toán',
            'right' => to_currency($cong_no_ncc['thanh_toan_tai_khoan_1'])
        );

        if(!empty($cong_no_ncc['hinh_thuc_thanh_toan_tai_khoan_1'])) {
            foreach($cong_no_ncc['hinh_thuc_thanh_toan_tai_khoan_1'] as $key => $amount) {
                $resport[] = array(
                    'left' => '&nbsp&nbsp' . $key,
                    'right' => to_currency($amount)
                );

                if($key == 'Tiền mặt')
                    $tong_tien_mat = $tong_tien_mat - $amount;
            }
        }

        $resport[] = array(
            'left' => 'Tổng số dư',
            'right' => to_currency($cong_no_ncc['tong_so_du_tai_khoan_1'])
        );

        $resport[] = array(
            'left' => '<span class="bold">'.$this->config->item('supplier_balance_2').'</span>',
            'right' => '--'
        );

        $resport[] = array(
            'left' => 'Ghi nợ',
            'right' => to_currency($cong_no_ncc['ghi_no_tai_khoan_2'])
        );

        $resport[] = array(
            'left' => 'Thanh toán',
            'right' => to_currency($cong_no_ncc['thanh_toan_tai_khoan_2'])
        );

        if(!empty($cong_no_ncc['hinh_thuc_thanh_toan_tai_khoan_2'])) {
            foreach($cong_no_ncc['hinh_thuc_thanh_toan_tai_khoan_2'] as $key => $amount) {
                $resport[] = array(
                    'left' => '&nbsp&nbsp' . $key,
                    'right' => to_currency($amount)
                );

                if($key == 'Tiền mặt')
                    $tong_tien_mat = $tong_tien_mat + $amount;
            }
        }

        $resport[] = array(
            'left' => 'Tổng số dư',
            'right' => to_currency($cong_no_ncc['tong_so_du_tai_khoan_2'])
        );

		$resport[] = array(
			'left' => '&nbsp',
			'right' => '&nbsp'
		);

		// chi phí
        $thu = $this->get_thu($params);
        $resport[] = array(
            'left' => '<h1>Thu</h1>',
            'right' => '--'
        );

        $resport[] = array(
            'left' => 'Tổng cộng',
            'right' => to_currency($thu['total'])
        );

        if(!empty($thu['list'])) {
            foreach($thu['list'] as $val) {
                $resport[] = array(
                    'left' => $val['name'],
                    'right' => to_currency($val['expense_amount_total'])
                );
            }
        }

        $tong_tien_mat = $tong_tien_mat + $thu['tien_mat'];

		$resport[] = array(
			'left' => '&nbsp',
			'right' => '&nbsp'
		);

        // chi phí
        $chi = $this->get_chi($params);
        $resport[] = array(
            'left' => '<h1>Chi</h1>',
            'right' => '--'
        );

        $resport[] = array(
            'left' => 'Tổng cộng',
            'right' => to_currency($chi['total'])
        );

        if(!empty($chi['list'])) {
            foreach($chi['list'] as $val) {
                $resport[] = array(
                    'left' => $val['name'],
                    'right' => to_currency($val['expense_amount_total'])
                );
            }
        }

        $tong_tien_mat = $tong_tien_mat - $chi['tien_mat'];

        $resport[] = array(
            'left' => '&nbsp',
            'right' => '&nbsp'
        );

		$resport[] = array(
			'left' => '<h1>Doanh thu và lợi nhuận</h1>',
			'right' => '--'
		);

        $resport[] = array(
            'left' => 'Doanh thu',
            'right' => to_currency($ban_hang['tong_don_hang'] - $tra_hang['tong_don_hang'])
        );

        $resport[] = array(
            'left' => 'Lợi nhuận',
            'right' => to_currency($loi_nhuan)
        );

        $resport[] = array(
            'left' => '&nbsp',
            'right' => '&nbsp'
        );

        $resport[] = array(
            'left' => '<h1>Tổng tiền mặt</h1>',
            'right' => '--'
        );

        $resport[] = array(
            'left' => 'Tổng cộng',
            'right' => to_currency($tong_tien_mat)
        );

        $resport[] = array(
            'left' => '&nbsp',
            'right' => '&nbsp'
        );

        $result_tmp = $this->get_theo_doi_so_giao_ca($params);

        if(!empty($result_tmp)) {
            $resport[] = array(
                'left' => '<h1>Theo dõi tiền ghi sổ (Giao ca)</h1>',
                'right' => '--'
            );

            foreach($result_tmp as $val) {
                $resport[] = array(
                    'left' => $val['name'],
                    'right' => $val['content']
                );
            }
        }

        return $resport;
    }

    function model_load_model($model_name)
    {
        $CI =& get_instance();
        $CI->load->model($model_name);
        return $CI->$model_name;
    }
}
?>