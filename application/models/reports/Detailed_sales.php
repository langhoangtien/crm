<?php
require_once ("Report.php");
class Detailed_sales extends Report
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
		$return['summary'][] = array('data'=>lang('reports_register'), 'align'=> 'left');
		$return['summary'][] = array('data'=>lang('common_items_purchased'), 'align'=> 'left');
		$return['summary'][] = array('data'=>lang('reports_sold_by'), 'align'=> 'left');
		$return['summary'][] = array('data'=>lang('reports_sold_to'), 'align'=> 'left');		
		$return['summary'][] = array('data'=>lang('reports_subtotal'), 'align'=> 'right');
		$return['summary'][] = array('data'=>lang('reports_total'), 'align'=> 'right');
		$return['summary'][] = array('data'=>lang('common_tax'), 'align'=> 'right');
	    $return['summary'][] = array('data'=>lang('reports_summary_commission'), 'align'=> 'right');		
        
		if($this->Employee->has_module_action_permission('reports','show_profit',$this->Employee->get_logged_in_employee_info()->person_id))
		{
            $return['summary'][] = array('data'=>lang('reports_profit_before_charging_commission'), 'align'=> 'right');
			$return['summary'][] = array('data'=>lang('common_profit'), 'align'=> 'right');
		}
		$return['summary'][] = array('data'=>lang('reports_payment_type'), 'align'=> 'right');
		$return['summary'][] = array('data'=>lang('reports_comments'), 'align'=> 'right');

		$return['details'] = array();
		$return['details'][] = array('data'=>lang('common_item_number'), 'align'=> 'left');
		$return['details'][] = array('data'=>lang('common_product_id'), 'align'=> 'left');
		$return['details'][] = array('data'=>lang('reports_name'), 'align'=> 'left');
		$return['details'][] = array('data'=>lang('reports_category'), 'align'=> 'left');
		$return['details'][] = array('data'=>lang('common_size'), 'align'=> 'left');
		$return['details'][] = array('data'=>lang('common_supplier'), 'align'=> 'left');
		$return['details'][] = array('data'=>lang('reports_serial_number'), 'align'=> 'left');
		$return['details'][] = array('data'=>lang('reports_description'), 'align'=> 'left');
		$return['details'][] = array('data'=>lang('reports_current_selling_price'), 'align'=> 'left');
		
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
				
		$this->db->select('quantity_purchased,locations.name as location_name, sale_id, sale_time, sale_date, register_name, SUM(CASE WHEN quantity_purchased < 0 THEN 1) as items_purchased, CONCAT(sold_by_employee.first_name," ",sold_by_employee.last_name) as sold_by_employee, CONCAT(sold_by_employee.first_name," ",sold_by_employee.last_name) as sold_by_employee, CONCAT(employee.first_name," ",employee.last_name) as employee_name, customer.person_id as customer_id, CONCAT(customer.first_name," ",customer.last_name) as customer_name, customer_data.account_number as account_number,sum(subtotal) as subtotal, sum(total) as total, sum(tax) as tax, sum(profit) as profit, payment_type, comment', false);
		$this->db->from('sales_items_temp');
		$this->db->join('locations', 'sales_items_temp.location_id = locations.location_id');
		$this->db->join('people as employee', 'sales_items_temp.employee_id = employee.person_id');
		$this->db->join('people as sold_by_employee', 'sales_items_temp.sold_by_employee_id = sold_by_employee.person_id', 'left');
		$this->db->join('people as customer', 'sales_items_temp.customer_id = customer.person_id', 'left');
		$this->db->join('customers as customer_data', 'sales_items_temp.customer_id = customer_data.person_id', 'left');
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
		
		$this->db->select('sale_id, sale_time, sale_date, item_number, items.product_id as item_product_id,item_kits.product_id as item_kit_product_id, item_kit_number, items.name as item_name, item_kits.name as item_kit_name, sales_items_temp.category, quantity_purchased, serialnumber, sales_items_temp.description, subtotal,total, tax, profit, discount_percent, items.size, items.unit_price as current_selling_price, suppliers.company_name as supplier_name, suppliers.person_id as supplier_id', false);
		$this->db->from('sales_items_temp');
		$this->db->join('items', 'sales_items_temp.item_id = items.item_id', 'left');
		$this->db->join('item_kits', 'sales_items_temp.item_kit_id = item_kits.item_kit_id', 'left');		
		$this->db->join('suppliers', 'sales_items_temp.supplier_id = suppliers.person_id', 'left');
		
		if (!empty($sale_ids))
		{
			$this->db->group_start();
			$sale_ids_chunk = array_chunk($sale_ids,25);
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
		$this->db->select('COUNT(sale_id) as TONG_DON_HANG,sum(quantity_purchased) as TONG_SO_MAT_HANG,sum(subtotal) as subtotal, sum(total) as total, sum(tax) as tax, sum(profit) as profit', false);

		$this->db->from('sales_items_temp');
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}
		
		if ($this->config->item('hide_store_account_payments_from_report_totals'))
		{
			$this->db->where('store_account_payment', 0);
		}
		
		$this->db->where('deleted', 0);
		$this->db->group_by('sale_id');
		
		$return = array(
			'subtotal' => 0,
			// 'total' => 0,
			'tax' => 0,
			// 'TONG_SO_MAT_HANG'=>0,
		);
		foreach($this->db->get()->result_array() as $row)
		{
			$return['subtotal'] += to_currency_no_money($row['subtotal'],2);
			// $return['total'] += to_currency_no_money($row['total'],2);
			$return['tax'] += to_currency_no_money($row['tax'],2);
			// $return['TONG_SO_MAT_HANG'] += $row['TONG_SO_MAT_HANG'];
		}
		
		if(!$this->Employee->has_module_action_permission('reports','show_profit',$this->Employee->get_logged_in_employee_info()->person_id))
		{
			unset($return['profit']);
		}
		return $return;
	}
    
    public function get_commission_by_sale() 
    {
        if ($this->params['sale_type'] != 'return') {
                   $this->db-> select('c.sale_id as sale_id, SUM(commission) AS sum_commission')
                  -> from('sales_commission AS c')
                  -> join('sales AS s', 'c.sale_id = s.sale_id')
                  -> where('s.commission_status', 1)
                  -> where('s.deleted', 0)
                  -> group_by('s.sale_id');
            $query = $this->db->get();

            $result_tmp = $query->result_array();

            $this->db->flush_cache();
            $result = [];
            
            foreach( $result_tmp as $resultRow) {
                 $result[$resultRow['sale_id']] = $resultRow['sum_commission'];
            }
            
            return $result;
        }
        return array();

    }
}
?>