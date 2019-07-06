<?php
require_once ("Report.php");
class Summary_sales extends Report
{
    protected $_sale_fields = array(
        'sale_time' => 's.sale_time'
    );

	function __construct()
	{
		parent::__construct();
	}

	public function getDataColumns()
	{
		$columns = array();
		
		$columns[] = array('data'=>lang('reports_date'), 'align'=> 'left');
		$columns[] = array('data'=>lang('reports_subtotal'), 'align'=> 'right');
		$columns[] = array('data'=>lang('reports_total'), 'align'=> 'right');
		$columns[] = array('data'=>lang('common_tax'), 'align'=> 'right');

		if($this->Employee->has_module_action_permission('reports','show_profit',$this->Employee->get_logged_in_employee_info()->person_id))
		{
			$columns[] = array('data'=>lang('common_profit'), 'align'=> 'right');
		}
		
		return $columns;		
	}
	
	public function getData()
	{		
		$this->db->select('sale_date, sum(subtotal) as subtotal, sum(total) as total, sum(tax) as tax,sum(profit) as profit', false);
		$this->db->from('sales_items_temp');
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}
		
		$this->db->where('deleted', 0);
		$this->db->group_by('sale_date');
		$this->db->order_by('sale_time', ($this->config->item('report_sort_order')) ? $this->config->item('report_sort_order') : 'asc');
		
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
		$this->db->select('COUNT(DISTINCT(sale_date)) as sale_date_count');
		$this->db->from('sales_items_temp');
		
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}
		
		$this->db->where('deleted', 0);
		
		$ret = $this->db->get()->row_array();
		return $ret['sale_date_count'];
	}
	
	
	public function getSummaryData()
	{
		$this->db->select('sum(subtotal) as subtotal, sum(total) as total, sum(tax) as tax,sum(profit) as profit', false);
		$this->db->from('sales_items_temp');
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

    function count_item($arrParams = null, $options = null) {
        $this->db -> select('COUNT(s.sale_id) AS totalItem')
                  -> from('sales AS s')
                  -> where('(s.suspended = 0 OR s.return = 1)')
                  -> where('s.store_account_payment', 0)
                  -> where('s.deleted', 0);

        $this->set_clause($arrParams);

        $query = $this->db->get();

        $result = $query->row()->totalItem;

        $this->db->flush_cache();

        return $result;
    }

     function list_item($arrParams = null, $options = null) {
		$result = array();
		if($options['task'] != 'commission'){
        $customers = $this->db->dbprefix('customers');
        $people = $this->db->dbprefix('people');
        $sales = $this->db->dbprefix('sales');
        $paginator = isset($arrParams['paginator'])?$arrParams['paginator']:null;
        # Lọc ra ngày cuối cùng thực hiện hóa đơn
        if(!empty($arrParams['start_date']) && !empty($arrParams['end_date'])) {
            $start_date =date('Y-m-d 00:00:00',strtotime($arrParams['start_date']));
            $end_date =date('Y-m-d 23:59:59',strtotime($arrParams['end_date']));
            $where =  "WHERE s.sale_time >'".$start_date."' 
                       AND s.sale_time <'".$end_date."'
                       AND (s.suspended = 0 OR s.return = 1)
                       AND s.store_account_payment = 0
                       AND s.deleted = 0
                       ";

      } else {
        $where =  "    WHERE (s.suspended = 0 OR s.return = 1)
                        AND s.store_account_payment = 0
                        AND s.deleted = 0
                    ";
      }
        if(!empty($arrParams['col']) && !empty($arrParams['order'])){
            $order_by = "ORDER BY `bang`.`payment_type` ASC";
        } else $order_by = '';

        $page = isset($arrParams['page'])?$arrParams['page']:0;
				$limit = '';
        if($page > 0) $limit = "LIMIT ".$paginator['per_page']." OFFSET ".($page - 1)*$paginator['per_page']."" ;
				
      	$query = "SELECT * FROM 
                (
                    (
                        SELECT s.sale_time,s.sale_id, s.code, s.payment_type, s.comment, s.commission_status,s.customer_id
                        FROM ".$sales." AS s 
                        ".$where."
                    ) AS bang
                    LEFT JOIN phppos_people as p ON bang.customer_id = p.person_id
                ) ".$order_by." ".$limit."
                ";
    // die;

        $result = $this->db->query($query)->result_array();

        $this->db->flush_cache();
		 }
		 
		 // get sale commission 
        if ($options['task'] == 'commission'){
            $paginator = isset($arrParams['paginator'])?$arrParams['paginator']:array();

            $this->db -> select('s.sale_id, s.code, s.payment_type, s.comment, s.commission_status')
                                -> select("DATE_FORMAT(s.sale_time, '%d-%m-%Y %H:%i') AS sale_time_format", FALSE)
                                -> from('sales AS s')
                                -> join('people As p', 's.customer_id = p.person_id', 'left')
                                -> where("(s.suspended = 0 OR s.return = 1)")
                                -> where('s.store_account_payment', 0)
                                -> where('s.deleted', 0);
            $set_clause = null;                    
            if (!empty($options['set_clause'])) {
                $set_clause['task'] = $options['set_clause'];
            }
            $this->set_clause($arrParams, $set_clause);

            if(!empty($arrParams['col']) && !empty($arrParams['order'])){
                    $col   = $this->_sale_fields[$arrParams['col']];
                    $order = $arrParams['order'];

                    $this->db->order_by($col, $order);
            } else {
                $this->db->order_by('s.sale_time', 'DESC');
            }

            $page = isset($arrParams['page'])? $arrParams['page']: 1;
            if($page > 0) {
                $this->db->limit($paginator['per_page'],($page - 1)*$paginator['per_page']);
            }
            $query = $this->db->get();
            $result = $query->result_array();

            $this->db->flush_cache();
        }
		if(!empty($result)) {
            foreach($result as $val)
                $sale_ids[] = $val['sale_id'];

            $tmp          = $this->get_order_total_tax($sale_ids);
            $expense_tmp  = $this->get_expenses($sale_ids);

            $sale_order_total_list       = $tmp['total_result'];
           
            $sale_order_tax_list         = $tmp['tax_result'];
            $sale_order_cost_list        = $tmp['cost_result'];
            $sale_expense_thu_list       = isset($expense_tmp['thu'])?$expense_tmp['thu']:0;
            $sale_expense_chi_list       = isset($expense_tmp['chi'])?$expense_tmp['chi']:0;
            $sale_commission_list        = $this->Sale->get_commission_from_sale_order(array('sale_ids'=>$sale_ids));
			$sale_employees_commission   = $this->Sale->get_sales_commission_for_each_employee_in_separate_sales();
            $sale_point_payment_list     = $this->Sale->get_point_amount_from_sale_order(array('sale_ids'=>$sale_ids));
            $sale_gift_card_payment_list = $this->Sale->get_gift_card_amount_from_sale_order(array('sale_ids'=>$sale_ids));

            foreach($result as &$value) {
                if(empty($value['code']))
                $value['code'] = $this->config->item('sale_prefix') . ' ' . $value['sale_id'];

                $value['order_value']                 = (isset($sale_order_total_list[$value['sale_id']]) && !empty($sale_order_total_list[$value['sale_id']])) ? $sale_order_total_list[$value['sale_id']] : 0;
                $value['cost_value']                  = (isset($sale_order_cost_list[$value['sale_id']]) && !empty($sale_order_cost_list[$value['sale_id']])) ? $sale_order_cost_list[$value['sale_id']] : 0;
                $value['tax_value']                   = (isset($sale_order_tax_list[$value['sale_id']]) && !empty($sale_order_tax_list[$value['sale_id']])) ? $sale_order_tax_list[$value['sale_id']] : 0;
                $value['thu_value']                   = (isset($sale_expense_thu_list[$value['sale_id']]) && !empty($sale_expense_thu_list[$value['sale_id']])) ? $sale_expense_thu_list[$value['sale_id']] : 0;
                $value['chi_value']                   = (isset($sale_expense_chi_list[$value['sale_id']]) && !empty($sale_expense_chi_list[$value['sale_id']])) ? $sale_expense_chi_list[$value['sale_id']] : 0;
                $value['commission']                  = isset($sale_commission_list[$value['sale_id']]) ? $sale_commission_list[$value['sale_id']] : 0;
                $value['point_payment']               = isset($sale_point_payment_list[$value['sale_id']]) ? $sale_point_payment_list[$value['sale_id']] : 0;
                $value['gift_card_payment']           = isset($sale_gift_card_payment_list[$value['sale_id']]) ? $sale_gift_card_payment_list[$value['sale_id']] : 0;
                $value['employee_commission_in_sale'] = array();
                foreach($sale_employees_commission as $sale_employee_commission)
                {
                    if($sale_employee_commission['sale_id'] == $value['sale_id'])
                    $value['employee_commission_in_sale'][$sale_employee_commission['employee_id']] = $sale_employee_commission;
                }
								
							 
            }
        }
   

        return $result;
    }


    function set_clause($arrParam = null, $options = null) {
				
        if($options['task'] == null) {
				
            if(!empty($arrParam['location_ids'])) {
                $this->db->where('s.location_id IN ('.$arrParam['location_ids'].')');
            }

            if(!empty($arrParam['employees'])) {
                $this->db->where('s.sale_id IN (
                                SELECT sale_id FROM '.$this->db->dbprefix('sales_employees').' WHERE employee_id IN ('.$arrParam['employees'].')
                                )');
            }
            if(isset($arrParam['employee_id']) && $arrParam['employee_id'] > 0) {
                $this->db->where('s.sale_id IN ((
                                SELECT sale_id FROM '.$this->db->dbprefix('sales_employees').' WHERE employee_id IN ('.$arrParam['employee_id'].')
                                ))');
            }
            if(!empty($arrParam['group_ids'])) {
                $this->db->where('s.sale_id IN ((
                                SELECT sale_id FROM '.$this->db->dbprefix('sales_employees').' WHERE group_id IN ('.$arrParam['group_ids'].')
                                ))');
            }
            if(!empty($arrParam['start_date'])) {
                $this->db->where('s.sale_time >= \''.$arrParam['start_date'].'\'');
            }

            if(!empty($arrParam['end_date'])) {
                $this->db->where('s.sale_time <= \''.$arrParam['end_date'].'\'');
            }

            if(isset($arrParam['sale_type']) && $arrParam['sale_type'] != -1) {
                $this->db->where('s.return', $arrParam['sale_type']);
            }

            if(isset($arrParam['sale_type']) && $arrParam['sale_type'] == 'sales') {
                $this->db->where('s.suspended', 0)
                         ->where('s.store_account_payment', 0);
			}
            if(isset($arrParam['sale_type']) && $arrParam['sale_type'] == 'returns') {
                $this->db->where('s.return', 1);
			}


        }elseif($options['task'] == 'query-string') {

            if(!empty($arrParam['selected_location_ids'])) {

                $where[] = 's.location_id IN ('.$arrParam['location_ids'].')';
            }

            if(!empty($arrParam['employees'])) {
                $where[] = 's.sale_id IN ((
                                SELECT sale_id FROM '.$this->db->dbprefix('sales_employees').' WHERE employee_id IN ('.$arrParam['employees'].')
                                ))';
            }

            if(isset($arrParam['employee_id']) && $arrParam['employee_id'] > 0) {
                $where[] = 's.sale_id IN ((
                                SELECT sale_id FROM '.$this->db->dbprefix('sales_employees').' WHERE employee_id IN ('.$arrParam['employee_id'].')
                                ))';
            }

            if(!empty($arrParam['group_ids'])) {
                $where[] = 's.sale_id IN ((
                                SELECT sale_id FROM '.$this->db->dbprefix('sales_employees').' WHERE group_id IN ('.$arrParam['group_ids'].')
                                ))';
            }

            if(!empty($arrParam['start_date'])) {
                $where[] = 's.sale_time >= \''.$arrParam['start_date'].'\'';
            }

            if(!empty($arrParam['end_date'])) {
                $where[] = 's.sale_time <= \''.$arrParam['end_date'].'\'';
            }

            if(isset($arrParam['sale_type']) && $arrParam['sale_type'] == 'sales') {
                $where[] = 's.suspended = 0 AND s.store_account_payment = 0';
            }


            if(isset($arrParam['sale_type']) && $arrParam['sale_type'] == 'returns')
                $where[] = 's.return = 1';

            if(!empty($where))
                $result = implode(' AND ', $where);
            else
                $result = '';

            return $result;
        }
        elseif ($options['task'] == 'employee_detailed') {
            if(!empty($arrParam['location_ids'])) {
                $this->db->where('s.location_id IN ('.$arrParam['location_ids'].')');
            }
            if(!empty($arrParam['start_date'])) {
                $this->db->where('s.sale_time >= \''.$arrParam['start_date'].'\'');
            }

            if(!empty($arrParam['end_date'])) {
                $this->db->where('s.sale_time <= \''.$arrParam['end_date'].'\'');
            }

            if(isset($arrParam['sale_type']) && $arrParam['sale_type'] != -1) {
                $this->db->where('s.return', $arrParam['sale_type']);
            }
            if(isset($arrParam['employee_id']) && $arrParam['employee_id']> 0) {
                $this->db->where('s.sold_by_employee_id', $arrParam['employee_id']);
            if(isset($arrParam['sale_type']) && $arrParam['sale_type'] == 'sales') {
                $this->db->where('s.suspended', 0)
                         ->where('s.store_account_payment', 0);
			}			}
            if(isset($arrParam['sale_type']) && $arrParam['sale_type'] == 'returns') {
                $this->db->where('s.return', 1);
			}


        }
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


    public function lay_tong_don_hang_nhan_vien($arrParam) {

        $this->db->select('sale_id,tien_thanh_toan,tien_chiet_khau,e.group_id,sale_date,p.last_name,COUNT(sale_id) as TONG_DON_HANG,sum(quantity_purchased) as TONG_SO_MAT_HANG,sum(subtotal) as subtotal, sum(total) as total, sum(tax) as tax, sum(profit) as profit, location_id', false);
        $this->db->group_by('sale_id');
        $this->db->join('people as p', 'sales_items_temp.customer_id = p.person_id');
        $this->db->join('employees as e', 'sales_items_temp.employee_id = e.person_id');

        $this->db->from('sales_items_temp');
        if ($arrParam['location_ids'])
        {
            $this->db->where_in('location_id',$arrParam['location_ids']);
        }
        if ($arrParam['group_ids'])
        {
            // $this->db->where_in('group_id',$arrParam['group_ids']);
        }

        $result_tmp = $this->db->get()->result_array();
        // var_dump($data);
        // die;

        $this->db->flush_cache();


        return $result_tmp;

    }

    public function lay_tong_hop_don_hang_nhan_vien($arrParam)
    {
        $this->db->select('e.group_id,sale_date,COUNT(sale_id) as TONG_DON_HANG,sum(quantity_purchased) as TONG_SO_MAT_HANG,sum(subtotal) as TONG_GIA_TRI, sum(total) as total, sum(tax) as TONG_THUE, sum(profit) as TONG_LOI_NHUAN, location_id', false);
         $this->db->join('employees as e', 'sales_items_temp.employee_id = e.person_id');
        $this->db->from('sales_items_temp');
        if ($arrParam['location_ids'])
        {
            $this->db->where_in('location_id',$arrParam['location_ids']);
        }
        if ($arrParam['group_ids'])
        {
            // $this->db->where_in('group_id',$arrParam['group_ids']);
        }
           
        $return = array(
            'subtotal' => 0,
            'total' => 0,
            'tax' => 0,
            'profit' => 0,
            'TONG_DON_HANG'=>0,
            'TONG_SO_MAT_HANG'=>0,
        );

        // $data = $this->db->get()->result_array();
        // var_dump($data);
        // die;

        foreach($this->db->get()->result_array() as $row)
        {
            $return['TONG_GIA_TRI'] = to_currency_no_money($row['TONG_GIA_TRI'],2);
            $return['TONG_THUE'] = to_currency_no_money($row['TONG_THUE'],2);
            $return['TONG_LOI_NHUAN'] = to_currency_no_money($row['TONG_LOI_NHUAN'],2);
        }
        if(!$this->Employee->has_module_action_permission('reports','show_profit',$this->Employee->get_logged_in_employee_info()->person_id))
        {
            unset($return['profit']);
        }
        return $return;
    }

    function get_total_purchase($arrParams = null, $options = null) {
        // ttotal, tax
        $this->db -> select('SUM(ttotal) AS sum_ttotal, SUM(tax) AS sum_tax, SUM(tcost) AS sum_tcost')
                  -> from('sales_items_temp');
        $query = $this->db->get();

        $result_tmp = $query->row_array();

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
                  -> where('s.commission_status', 1)
                  -> where('s.deleted', 0);
        if(!empty($arrParams['employees']))
				{ 
				  $this->db->where('c.employee_id IN ('.$arrParams['employees'].')');
				}																		 
        $this->set_clause($arrParams);

        $query = $this->db->get();

        $result_tmp = $query->row_array();

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
                  -> where('s.deleted', 0);

        $this->set_clause($arrParams);

        $query = $this->db->get();

        $result_tmp = $query->row_array();

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
                 -> where('s.deleted', 0);

        $query = $this->db->get();

        $this->set_clause($arrParams);

        $result_tmp = $query->row_array();

        $this->db->flush_cache();

        if(!empty($result_tmp)) {
            $gift_card_payment = $result_tmp['the_qua_tang'];
        }else {
            $gift_card_payment = 0;
        }

        // thu + chi
        $where = $this->set_clause($arrParams, array('task'=>'query-string'));

        $sql = "SELECT s.sale_id, SUM(IF(e.expense_type = 1, e.expense_amount + e.expense_tax, 0)) AS chi, SUM(IF(e.expense_type = -1, e.expense_amount + e.expense_tax, 0)) AS thu
                FROM phppos_expenses AS e
                INNER JOIN phppos_sales AS s ON e.sale_id = s.sale_id
                WHERE s.deleted = 0
                ";

        if(!empty($where))
            $sql = $sql . " AND $where";

        $query  = $this->db->query($sql);
        $result_tmp = $query->row_array();

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
            'order_value' => to_currency($order_total),
            'profit'      => to_currency($profit),
            'commission'  => to_currency($commission),
            'tax'         => to_currency($tax),
            'profit_before_charging_commission' => to_currency($profit + $commission),
        );

        return $result;
    }

}
?>