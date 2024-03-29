<?php
require_once ("Report.php");
class Summary_employees extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		$columns = array();
		
		$columns[] = array('data'=>lang('reports_employee'), 'align'=> 'left');
		$columns[] = array('data'=>lang('reports_subtotal'), 'align'=> 'right');
		$columns[] = array('data'=>lang('reports_total'), 'align'=> 'right');
		$columns[] = array('data'=>lang('common_tax'), 'align'=> 'right');

		if($this->Employee->has_module_action_permission('reports','show_profit',$this->Employee->get_logged_in_employee_info()->person_id))
		{
            $columns[] = array('data'=>lang('reports_profit_before_charging_commission'), 'align'=>'right');
			$columns[] = array('data'=>lang('reports_commission'), 'align'=>'right');
			$columns[] = array('data'=>lang('common_profit'), 'align'=> 'right');
		}
		
		return $columns;		
	}
	
	public function getData()
	{
		$employee_column = $this->params['employee_type'] == 'logged_in_employee' ? 'employee_id' : 'sold_by_employee_id';
		
		$this->db->select('CONCAT(first_name, " ",last_name) as employee, sum(subtotal) as subtotal, sum(total) as total, sum(tax) as tax, sum(profit) as profit, employees.person_id as person_id', false);
		$this->db->from('sales_items_temp');
		$this->db->join('employees', 'employees.person_id = sales_items_temp.'.$employee_column);
		$this->db->join('people', 'employees.person_id = people.person_id');
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}
		$this->db->where($this->db->dbprefix('sales_items_temp').'.deleted', 0);
		$this->db->group_by($employee_column);
		$this->db->order_by('last_name');

		//If we are exporting NOT exporting to excel make sure to use offset and limit
		if (isset($this->params['export_excel']) && !$this->params['export_excel'])
		{
			$this->db->limit($this->report_limit);
			$this->db->offset($this->params['offset']);
		}

		return $this->db->get()->result_array();		
	}
	
	function getTotalRows()
	{
		$this->db->select('COUNT(DISTINCT(person_id)) as employee_count');
		$this->db->from('sales_items_temp');		
		$this->db->join('employees', 'employees.person_id = sales_items_temp.employee_id');

		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}
		
		$this->db->where($this->db->dbprefix('sales_items_temp').'.deleted', 0);
		
		$ret = $this->db->get()->row_array();
		return $ret['employee_count'];
	}
	
	
	public function getSummaryData()
	{
		$this->db->select('sum(subtotal) as subtotal, sum(total) as total, sum(tax) as tax, sum(profit) as profit', false);
		$this->db->from('sales_items_temp');
		$this->db->join('employees', 'employees.person_id = sales_items_temp.employee_id');
		$this->db->join('people', 'employees.person_id = people.person_id');
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}
		$this->db->where($this->db->dbprefix('sales_items_temp').'.deleted', 0);
		
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
    
        public function get_commission_by_employee($arrParams) 
    {
        if ($this->params['sale_type'] != 'return') {
                   $this->db-> select('SUM(c.commission) AS sum_commission' )
                  -> from('sales_commission AS c')
                  -> join('sales AS s', 'c.sale_id = s.sale_id')
                  -> where('c.employee_id', $arrParams['employee_id'])
                  -> where('s.commission_status', 1)
                  -> where('s.deleted', 0);

            if(!empty($arrParams['start_date'])) 
            {
            		$this->db->where('s.sale_time >= \''.$arrParams['start_date'].'\'');
	        }

	        if(!empty($arrParams['end_date'])) 
	        {
	            $this->db->where('s.sale_time <= \''.$arrParams['end_date'].'\'');
	        }

                  // -> group_by('s.sale_id');
            $query = $this->db->get();

            $result_tmp = $query->result_array();

            $this->db->flush_cache();
         
            return $result_tmp;
        }
        return array();
    }
}
?>