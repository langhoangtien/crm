<?php
require_once (APPPATH . "models/reports/Detailed_sales.php");
class BizDetailed_sales extends Detailed_sales
{
		public function getDataColumns()
	{
		$return = array();
	
		$return['summary'] = array();
		$location_count = count(self::get_selected_location_ids());
	
		$return['summary'][] = array('data'=>lang('reports_sale_id'), 'align'=> 'center');
		if ($location_count > 1)
		{
			$return['summary'][] = array('data'=>lang('common_location'), 'align'=> 'center');
		}
		$return['summary'][] = array('data'=>lang('reports_date'), 'align'=> 'center');
		// $return['summary'][] = array('data'=>lang('reports_register'), 'align'=> 'center');
		// $return['summary'][] = array('data'=>lang('common_items_purchased'), 'align'=> 'center');
		$return['summary'][] = array('data'=>lang('reports_sold_by'), 'align'=> 'center');
		$return['summary'][] = array('data'=>lang('reports_sold_to'), 'align'=> 'center');
		$return['summary'][] = array('data'=>lang('reports_quantity'), 'align'=> 'center');

		$return['summary'][] = array('data'=>lang('reports_subtotal'), 'align'=> 'right');
		$return['summary'][] = array('data'=>lang('reports_total_discount'), 'align'=> 'right');
		$return['summary'][] = array('data'=>lang('common_tax'), 'align'=> 'right');
	
		if($this->Employee->has_module_action_permission('reports','show_profit',$this->Employee->get_logged_in_employee_info()->person_id))
		{
			$return['summary'][] = array('data'=>lang('reports_revenue'), 'align'=> 'center');
            $return['summary'][] = array('data'=>lang('reports_profit_before_charging_commission'), 'align'=> 'right');
            $return['summary'][] = array('data'=>lang('reports_commission'), 'align'=> 'center');
			$return['summary'][] = array('data'=>lang('common_profit'), 'align'=> 'center');
		}
		$return['summary'][] = array('data'=>lang('reports_actually_collected'), 'align'=> 'center');
		$return['summary'][] = array('data'=>lang('reports_payment_type'), 'align'=> 'center');
		// $return['summary'][] = array('data'=>lang('reports_payment_type'), 'align'=> 'right');
		// $return['summary'][] = array('data'=>lang('reports_comments'), 'align'=> 'right');
	
		$return['details'] = array();
		$return['details'][] = array('data'=>lang('common_item_number'), 'align'=> 'center');
		$return['details'][] = array('data'=>lang('common_product_id'), 'align'=> 'center');
		$return['details'][] = array('data'=>lang('reports_name'), 'align'=> 'center');
		$return['details'][] = array('data'=>lang('reports_category'), 'align'=> 'center');
		$return['details'][] = array('data'=>lang('common_size'), 'align'=> 'center');
		$return['details'][] = array('data'=>lang('common_supplier'), 'align'=> 'center');
		$return['details'][] = array('data'=>lang('reports_serial_number'), 'align'=> 'center');
		$return['details'][] = array('data'=>lang('reports_description'), 'align'=> 'center');
		$return['details'][] = array('data'=>lang('reports_current_selling_price'), 'align'=> 'center');
	
		$return['details'][] = array('data'=>lang('reports_quantity_purchased'), 'align'=> 'center');
		$return['details'][] = array('data'=>lang('reports_measure_purchased'), 'align'=> 'center');
		$return['details'][] = array('data'=>lang('reports_subtotal'), 'align'=> 'center');
		$return['details'][] = array('data'=>lang('reports_total'), 'align'=> 'center');
		$return['details'][] = array('data'=>lang('common_tax'), 'align'=> 'center');
		if($this->Employee->has_module_action_permission('reports','show_profit',$this->Employee->get_logged_in_employee_info()->person_id))
		{
			$return['details'][] = array('data'=>lang('common_profit'), 'align'=> 'center');
		}
		$return['details'][] = array('data'=>lang('common_discount'), 'align'=> 'center');
			//header of expenses
	  $return['expenses'] = array();
		$return['expenses'][] = array('data'=>lang('reports_expenses_description'), 'align'=> 'center');
		$return['expenses'][] = array('data'=>lang('reports_expenses_type'), 'align'=> 'center');
		$return['expenses'][] = array('data'=>lang('reports_expenses_money'), 'align'=> 'center');
		$return['expenses'][] = array('data'=>lang('reports_expenses_tax'), 'align'=> 'center');
		
		return $return;
	}
	/* 
	*luongpham
	*tinh tong loi nhuan, tong chiet khau, tong so dơn hang
	*15/06/17 
	*/
	function sumProfit(){
		$data = array();
		$data['sum'] = array();
		$this->db->select('sum(profit) as profit,LOI_NHUAN,quantity_purchased,locations.name as location_name, sales_items_temp.sale_id, sale_time, sale_date, register_name, sum(quantity_purchased) as items_purchased, CONCAT(sold_by_employee.first_name," ",sold_by_employee.last_name) as sold_by_employee, CONCAT(sold_by_employee.first_name," ",sold_by_employee.last_name) as sold_by_employee, CONCAT(employee.first_name," ",employee.last_name) as employee_name, CONCAT(customer.phone_number) as phone_number, customer.person_id as customer_id, CONCAT(customer.first_name," ",customer.last_name) as customer_name, customer_data.account_number as account_number,sum(subtotal) as subtotal, sum(total) as total, sum(tax) as tax,sales_items_temp.payment_type, comment, tien_chiet_khau', false);
		$this->db->from('sales_items_temp');
		$this->db->join('locations', 'sales_items_temp.location_id = locations.location_id');
		$this->db->join('people as employee', 'sales_items_temp.employee_id = employee.person_id');
		$this->db->join('people as sold_by_employee', 'sales_items_temp.sold_by_employee_id = sold_by_employee.person_id', 'left');
		$this->db->join('people as customer', 'sales_items_temp.customer_id = customer.person_id', 'left');
		$this->db->join('customers as customer_data', 'sales_items_temp.customer_id = customer_data.person_id', 'left');

		$this->db->where('sales_items_temp.deleted', 0);
		$this->db->order_by('sale_time', ($this->config->item('report_sort_order')) ? $this->config->item('report_sort_order') : 'asc');
		$this->db->group_by('sale_id');

		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}

		$i = $this->db->get()->result_array();

		foreach ($i as $key => $value) {
			// $tien_chiet_khau  = ($value['item_name'] ==  'Giảm Giá')? $value['subtotal'] :0;
			$tien_chiet_khau  = ($value['tien_chiet_khau'] ==  '')? $value['subtotal'] :0;
			$data['sum']['tong_tien_chiet_khau'] += $tien_chiet_khau;
			$data['sum']['tong_loi_nhuan'] += ($value['LOI_NHUAN']+$tien_chiet_khau);
			$data['sum']['tong_don_hang'] += count($value['sale_id']);
            $data['sum']['tong_thuc_thu'] += $value['total'];
            // $thuc_thu = explode(':', $value['payment_type']);
			// $tam = array();
			// $how_many = count($thuc_thu);
			// $tong_thuc_thu = 0;
			// if($how_many>1){
				// for($i = 0; $i < $how_many-1; $i = $i + 2){
					// $tam[trim($thuc_thu[$i])] = $thuc_thu[$i+1];
				// }

				// $tong_thuc_thu = 0;

				// foreach ($tam as $k => $value) {
					// if($k == 'Tiền mặt' || $k == 'Chuyển khoản') {
						// $tien = explode('V', $value);
						// $thuc_thu_rp = trim(str_replace(',', '', $tien[0]));
						// $data['sum']['tong_thuc_thu'] += $thuc_thu_rp;
					// }
				// }
			// }
		}
		return $data;
	}								
	
	public function getData()
	{
		$data = array();
		$data = $this->sumProfit();
		$data['summary'] = array();
		$data['details'] = array();
		$this->db->select('sum(profit) as profit, LOI_NHUAN,quantity_purchased,locations.name as location_name, sales_items_temp.sale_id, sale_time, sale_date, register_name, SUM(quantity_purchased ) as items_purchased, CONCAT(sold_by_employee.first_name," ",sold_by_employee.last_name) as sold_by_employee, CONCAT(sold_by_employee.first_name," ",sold_by_employee.last_name) as sold_by_employee, CONCAT(employee.first_name," ",employee.last_name) as employee_name, CONCAT(customer.phone_number) as phone_number, customer.person_id as customer_id, CONCAT(customer.first_name," ",customer.last_name) as customer_name, customer_data.account_number as account_number,sum(subtotal) as subtotal, sum(total) as total, sum(tax) as tax,sales_items_temp.payment_type, comment, tien_chiet_khau', false);
		$this->db->from('sales_items_temp');
		$this->db->join('locations', 'sales_items_temp.location_id = locations.location_id');
		$this->db->join('people as employee', 'sales_items_temp.employee_id = employee.person_id');
		$this->db->join('people as sold_by_employee', 'sales_items_temp.sold_by_employee_id = sold_by_employee.person_id', 'left');
		$this->db->join('people as customer', 'sales_items_temp.customer_id = customer.person_id', 'left');
		$this->db->join('customers as customer_data', 'sales_items_temp.customer_id = customer_data.person_id', 'left');

		$this->db->where('sales_items_temp.deleted', 0);
		$this->db->order_by('sale_time', ($this->config->item('report_sort_order')) ? $this->config->item('report_sort_order') : 'asc');
		$this->db->group_by('sale_id');

		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}

		$page = $this->uri->segment(7,1);
		$this->params['offset'] = ($page-1)*$this->report_limit;
		//If we are exporting NOT exporting to excel make sure to use offset and limit
		if (!$this->params['export_excel'])
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
		
		$this->db->select('sale_id, sale_time, sale_date, item_number, items.item_id, items.product_id as item_product_id,item_kits.product_id as item_kit_product_id, item_kit_number, items.name as item_name, item_kits.name as item_kit_name, sales_items_temp.category, quantity_purchased, measure_qty, measures.name as measure_name, serialnumber, sales_items_temp.description, subtotal,total, tax, profit, discount_percent, items.size, items.unit_price as current_selling_price, suppliers.company_name as supplier_name, suppliers.person_id as supplier_id', false);
		$this->db->from('sales_items_temp');
		$this->db->join('items', 'sales_items_temp.item_id = items.item_id', 'left');
		$this->db->join('item_kits', 'sales_items_temp.item_kit_id = item_kits.item_kit_id', 'left');
		$this->db->join('measures', 'measures.id = sales_items_temp.measure_id','left');
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
}
?>