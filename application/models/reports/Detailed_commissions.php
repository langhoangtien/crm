<?php
require_once ("Report.php");
class Detailed_commissions extends Report
{
	function __construct()
	{
		parent::__construct();
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
		$return['summary'][] = array('data'=>lang('common_items_purchased'), 'align'=> 'left');
		$return['summary'][] = array('data'=>lang('reports_sold_to'), 'align'=> 'left');
		$return['summary'][] = array('data'=>lang('reports_subtotal'), 'align'=> 'right');
		$return['summary'][] = array('data'=>lang('reports_total'), 'align'=> 'right');
		$return['summary'][] = array('data'=>lang('common_tax'), 'align'=> 'right');
				
		if($this->Employee->has_module_action_permission('reports','show_profit',$this->Employee->get_logged_in_employee_info()->person_id))
		{
			$return['summary'][] = array('data'=>lang('common_profit'), 'align'=> 'right');
		}
		$return['summary'][] = array('data'=>lang('reports_commission'), 'align'=> 'right');
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
		$return['details'][] = array('data'=>lang('reports_commission'), 'align'=> 'right');			
		$return['details'][] = array('data'=>lang('common_discount'), 'align'=> 'right');			
		
		return $return;	
	}
	
	public function getData()
	{
		$employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
		$can_view_all_employee_commissions = false;
		if ($this->Employee->has_module_action_permission('reports','view_all_employee_commissions', $employee_id))
		{
			$can_view_all_employee_commissions = true;
		}
		
		$data = array();
		$data['summary'] = array();
		$data['details'] = array();
		
		$this->db->select('customer_data.account_number as account_number, locations.name as location_name, sale_id, sale_time, sale_date, sum(quantity_purchased) as items_purchased, CONCAT(first_name," ",last_name) as customer_name, sum(subtotal) as subtotal, sum(total) as total, sum(tax) as tax, sum(profit) as profit, sum(commission) as commission, payment_type, comment', false);
		$this->db->from('sales_items_temp');
		$this->db->join('locations', 'sales_items_temp.location_id = locations.location_id');
		$this->db->join('people', 'sales_items_temp.customer_id = people.person_id', 'left');
		$this->db->join('customers as customer_data', 'sales_items_temp.customer_id = customer_data.person_id', 'left');
		
		if ($this->params['employee_type'] == 'logged_in_employee')
		{
			if ($can_view_all_employee_commissions)
			{
				$this->db->where('employee_id', $this->params['employee_id']);
			}
			else
			{
				$this->db->where('employee_id', $employee_id);
			}
		}
		else
		{
			if ($can_view_all_employee_commissions)
			{
				$this->db->where('sold_by_employee_id', $this->params['employee_id']);	
			}
			else
			{
				$this->db->where('sold_by_employee_id', $employee_id);			
			}		
		}

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
		
		$this->db->select('sale_id, sale_time, sale_date, item_number, items.product_id as item_product_id,item_kits.product_id as item_kit_product_id, item_kit_number, items.name as item_name, item_kits.name as item_kit_name, sales_items_temp.category, quantity_purchased, serialnumber, sales_items_temp.description, subtotal,total, tax, profit, commission,discount_percent', false);
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
		
		$employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
		$can_view_all_employee_commissions = false;
		if ($this->Employee->has_module_action_permission('reports','view_all_employee_commissions', $employee_id))
		{
			$can_view_all_employee_commissions = true;
		}
		
		$this->db->select("COUNT(DISTINCT(sale_id)) as sale_count");
		$this->db->from('sales_items_temp');
		
		if ($this->params['employee_type'] == 'logged_in_employee')
		{
			if ($can_view_all_employee_commissions)
			{
				$this->db->where('employee_id', $this->params['employee_id']);
			}
			else
			{
				$this->db->where('employee_id', $employee_id);
			}
		}
		else
		{
			if ($can_view_all_employee_commissions)
			{
				$this->db->where('sold_by_employee_id', $this->params['employee_id']);	
			}
			else
			{
				$this->db->where('sold_by_employee_id', $employee_id);			
			}		
		}
		
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
		$employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
		$can_view_all_employee_commissions = false;
		if ($this->Employee->has_module_action_permission('reports','view_all_employee_commissions', $employee_id))
		{
			$can_view_all_employee_commissions = true;
		}
		
		$this->db->select('sum(subtotal) as subtotal, sum(total) as total, sum(tax) as tax, sum(profit) as profit, sum(commission) as commission', false);
		$this->db->from('sales_items_temp');
		if ($this->params['employee_type'] == 'logged_in_employee')
		{
			if ($can_view_all_employee_commissions)
			{
				$this->db->where('employee_id', $this->params['employee_id']);
			}
			else
			{
				$this->db->where('employee_id', $employee_id);
			}
		}
		else
		{
			if ($can_view_all_employee_commissions)
			{
				$this->db->where('sold_by_employee_id', $this->params['employee_id']);	
			}
			else
			{
				$this->db->where('sold_by_employee_id', $employee_id);			
			}		
		}
		
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}
		$this->db->where('deleted', 0);
		$this->db->group_by('sale_id');
		
		$return = array(
			'subtotal' => 0,
			'total' => 0,
			'tax' => 0,
			'profit' => 0,
			'commission' => 0,
		);
		
		foreach($this->db->get()->result_array() as $row)
		{
			$return['subtotal'] += to_currency_no_money($row['subtotal'],2);
			$return['total'] += to_currency_no_money($row['total'],2);
			$return['tax'] += to_currency_no_money($row['tax'],2);
			$return['profit'] += to_currency_no_money($row['profit'],2);
			$return['commission'] += to_currency_no_money($row['commission'],2);
		}
		
		if(!$this->Employee->has_module_action_permission('reports','show_profit',$this->Employee->get_logged_in_employee_info()->person_id))
		{
			unset($return['profit']);
		}
		return $return;
	}

}
?>