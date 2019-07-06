<?php
class Sale extends CI_Model
{
    protected $_fields = array(
        'sale_id'     => 's.sale_id',
        'customer_id' => 's.customer_id',
        'sale_time'   => 's.sale_time',
    );

    public $view = 'view_owner';
    protected $_sale_tmp_ids = array();

	public function __construct()
	{
      parent::__construct();
		$this->load->model('Inventory');
        $this->load->library('sale_lib');
	}

	public function get_info($sale_id)
	{
		$this->db->from('sales');
		$this->db->where('sale_id',$sale_id);
		return $this->db->get();
	}

	function get_cash_sales_total_for_shift($shift_start, $shift_end)
    {
		$sales_totals = $this->get_sales_totaled_by_id($shift_start, $shift_end);
		$register_id = $this->Employee->get_logged_in_employee_current_register_id();

		$this->db->select('sales_payments.sale_id, sales_payments.payment_type, payment_amount, payment_id', false);
        $this->db->from('sales_payments');
        $this->db->join('sales','sales_payments.sale_id=sales.sale_id');
		$this->db->where('sales_payments.payment_date >=', $shift_start);
		$this->db->where('sales_payments.payment_date <=', $shift_end);
		$this->db->where('register_id', $register_id);
		$this->db->where($this->db->dbprefix('sales').'.deleted', 0);
		$this->db->order_by('payment_date');

		$payments_by_sale = array();
		$sales_payments = $this->db->get()->result_array();

		foreach($sales_payments as $row)
		{
        	$payments_by_sale[$row['sale_id']][] = $row;
		}

		$payment_data = $this->Sale->get_payment_data($payments_by_sale,$sales_totals);

		if (isset($payment_data[lang('common_cash')]))
		{
			return $payment_data[lang('common_cash')]['payment_amount'];
		}

		return 0.00;
    }

	function get_payment_data($payments_by_sale,$sales_totals)
	{
		$payment_data = array();

		$sale_ids = array_keys($payments_by_sale);
		$all_payments_for_sales = $this->_get_all_sale_payments($sale_ids);

		foreach($all_payments_for_sales as $sale_id => $payment_rows)
		{
			if (isset($sales_totals[$sale_id]))
			{
				$total_sale_balance = $sales_totals[$sale_id];
				foreach($payment_rows as $payment_row)
				{
					//Postive sale total, positive payment
					if ($sales_totals[$sale_id] >= 0 && $payment_row['payment_amount'] >=0)
					{
						$payment_amount = $payment_row['payment_amount'] <= $total_sale_balance ? $payment_row['payment_amount'] : $total_sale_balance;
					}//Negative sale total negative payment
					elseif ($sales_totals[$sale_id] < 0 && $payment_row['payment_amount']  < 0)
					{
						$payment_amount = $payment_row['payment_amount'] >= $total_sale_balance ? $payment_row['payment_amount'] : $total_sale_balance;
					}//Positive Sale total negative payment
					elseif($sales_totals[$sale_id] >= 0 && $payment_row['payment_amount']  < 0)
					{
						$payment_amount = $payment_row['payment_amount'];
					}//Negtive sale total postive payment
					elseif($sales_totals[$sale_id] < 0 && $payment_row['payment_amount']  >= 0)
					{
						$payment_amount = $payment_row['payment_amount'];
					}

					if (!isset($payment_data[$payment_row['payment_type']]))
					{
						$payment_data[$payment_row['payment_type']] = array('payment_type' => $payment_row['payment_type'], 'payment_amount' => 0 );
					}

					$exists = $this->_does_payment_exist_in_array($payment_row['payment_id'], $payments_by_sale[$sale_id]);


					if (($total_sale_balance != 0 ||
						($sales_totals[$sale_id] >= 0 && $payment_row['payment_amount']  < 0) ||
						($sales_totals[$sale_id] < 0 && $payment_row['payment_amount']  >= 0)) && $exists)
					{
						$payment_data[$payment_row['payment_type']]['payment_amount'] += $payment_amount;
					}

					$total_sale_balance-=$payment_amount;
				}
			}
		}

		return $payment_data;
	}

	function _does_payment_exist_in_array($payment_id, $payments)
	{
		foreach($payments as $payment)
		{
			if($payment['payment_id'] == $payment_id)
			{
				return TRUE;
			}
		}

		return FALSE;
	}

	function _get_all_sale_payments($sale_ids)
	{
		$return = array();

		if (count($sale_ids) > 0)
		{
			$this->db->select('sales_payments.*, sales.sale_time');
      	$this->db->from('sales_payments');
      	$this->db->join('sales', 'sales.sale_id=sales_payments.sale_id');

			$this->db->group_start();
			$sale_ids_chunk = array_chunk($sale_ids,25);
			foreach($sale_ids_chunk as $sale_ids)
			{
				$this->db->or_where_in('sales_payments.sale_id', $sale_ids);
			}
			$this->db->group_end();
			$this->db->order_by('payment_date');

			$result = $this->db->get()->result_array();

			foreach($result as $row)
			{
				$return[$row['sale_id']][] = $row;
			}
		}
		return $return;
	}

	function get_payment_data_grouped_by_sale($payments_by_sale,$sales_totals)
	{
		$payment_data = array();

		$sale_ids = array_keys($payments_by_sale);
		$all_payments_for_sales = $this->_get_all_sale_payments($sale_ids);

		foreach($all_payments_for_sales as $sale_id => $payment_rows)
		{
			if (isset($sales_totals[$sale_id]))
			{
				$total_sale_balance = $sales_totals[$sale_id];

				foreach($payment_rows as $payment_row)
				{
					//Postive sale total, positive payment
					if ($sales_totals[$sale_id] >= 0 && $payment_row['payment_amount'] >=0)
					{
						$payment_amount = $payment_row['payment_amount'] <= $total_sale_balance ? $payment_row['payment_amount'] : $total_sale_balance;
					}//Negative sale total negative payment
					elseif ($sales_totals[$sale_id] < 0 && $payment_row['payment_amount']  < 0)
					{
						$payment_amount = $payment_row['payment_amount'] >= $total_sale_balance ? $payment_row['payment_amount'] : $total_sale_balance;
					}//Positive Sale total negative payment
					elseif($sales_totals[$sale_id] >= 0 && $payment_row['payment_amount']  < 0)
					{
						$payment_amount = $payment_row['payment_amount'];
					}//Negtive sale total postive payment
					elseif($sales_totals[$sale_id] < 0 && $payment_row['payment_amount']  >= 0)
					{
						$payment_amount = $payment_row['payment_amount'];
					}

					if (!isset($payment_data[$sale_id][$payment_row['payment_type']]))
					{
						$payment_data[$sale_id][$payment_row['payment_type']] = array('sale_id' => $sale_id,'payment_type' => $payment_row['payment_type'], 'payment_amount' => 0,'payment_date' => $payment_row['payment_date'], 'sale_time' => $payment_row['sale_time'] );
					}

					$exists = $this->_does_payment_exist_in_array($payment_row['payment_id'], $payments_by_sale[$sale_id]);

					if (($total_sale_balance != 0 ||
						($sales_totals[$sale_id] >= 0 && $payment_row['payment_amount']  < 0) ||
						($sales_totals[$sale_id] < 0 && $payment_row['payment_amount']  >= 0)) && $exists)
					{
						$payment_data[$sale_id][$payment_row['payment_type']]['payment_amount'] += $payment_amount;
					}

					$total_sale_balance-=$payment_amount;
				}
			}
		}

		return $payment_data;
	}


	function get_sales_totaled_by_id($shift_start, $shift_end)
	{
		$register_id = $this->Employee->get_logged_in_employee_current_register_id();

		$this->db->select('sales.sale_id', false);
      $this->db->from('sales');
      $this->db->join('sales_payments','sales_payments.sale_id=sales.sale_id');
		$this->db->where('sales_payments.payment_date >=', $shift_start);
		$this->db->where('sales_payments.payment_date <=', $shift_end);
		$this->db->where('register_id', $register_id);
		$this->db->where($this->db->dbprefix('sales').'.deleted', 0);

		$sale_ids = array();
		$result = $this->db->get()->result();
		foreach($result as $row)
		{
			$sale_ids[] = $row->sale_id;
		}

		$sales_totals = array();

		if (count($sale_ids) > 0)
		{
			$where = 'WHERE '.$this->db->dbprefix('sales').'.sale_id IN('.implode(',',$sale_ids).')';
			$this->_create_sales_items_temp_table_query($where);
			$this->db->select('sale_id, SUM(total) as total', false);
			$this->db->from('sales_items_temp');
			$this->db->group_by('sale_id');

			foreach($this->db->get()->result_array() as $sale_total_row)
			{
				$sales_totals[$sale_total_row['sale_id']] = $sale_total_row['total'];
			}
		}

		return $sales_totals;
	}

	function exists($sale_id)
	{
		$this->db->from('sales');
		$this->db->where('sale_id',$sale_id);
		$query = $this->db->get();

		return ($query->num_rows()==1);
	}

	function update($sale_data, $sale_id)
	{
		$this->db->where('sale_id', $sale_id);
		$success = $this->db->update('sales',$sale_data);

		return $success;
	}

	function update_store_account($sale_id,$undelete=0)
	{
		//update if Store account payment exists
		$this->db->from('sales_payments');
		$this->db->where("(sales_payments.payment_type = "."'".lang('common_store_account')."' OR sales_payments.payment_type = "."'".lang('common_debt_customer')."')");
		$this->db->where('sale_id',$sale_id);
		$to_be_paid_result = $this->db->get();

		$customer_id=$this->get_customer($sale_id)->person_id;


		if(!empty($to_be_paid_result) && $to_be_paid_result->num_rows() >=1)
		{
			foreach($to_be_paid_result->result() as $to_be_paid)
			{
			
				if($to_be_paid->payment_amount)
				{
					//update customer balance
					if($undelete==0)
					{
						if($to_be_paid->payment_type == lang('common_debt_customer'))
						{
							$this->db->set('balance_2','balance_2-'.$to_be_paid->payment_amount,false);
						}
						else
						{
							$this->db->set('balance','balance-'.$to_be_paid->payment_amount,false);
						}
					}
					else
					{
						if($to_be_paid->payment_type == lang('common_debt_customer'))
						{
							$this->db->set('balance_2','balance_2+'.$to_be_paid->payment_amount,false);
						}
						else
						{
							$this->db->set('balance','balance+'.$to_be_paid->payment_amount,false);
						}
				
					}
					$this->db->where('person_id', $customer_id);
					$this->db->update('customers');
				}
			}
		}
	}

	function update_giftcard_balance($sale_id,$undelete=0)
	{
		//if gift card payment exists add the amount to giftcard balance
			$this->db->from('sales_payments');
			$this->db->like('payment_type',lang('common_giftcard'));
			$this->db->where('sale_id',$sale_id);
			$sales_payment = $this->db->get();

			if($sales_payment->num_rows() >=1)
			{
				foreach($sales_payment->result() as $row)
				{
					$giftcard_number=str_ireplace(lang('common_giftcard').':','',$row->payment_type);
					$cur_giftcard_value = $this->Giftcard->get_giftcard_value($giftcard_number);
					$value=$row->payment_amount;

					$value_to_add_subtract = 0;
					if($undelete==0)
					{
						$this->db->set('value','value+'.$value,false);
						$value_to_add_subtract = $value;
					}
					else
					{
						$this->db->set('value','value-'.$value,false);
						$value_to_add_subtract = -$value;
					}
					$this->db->where('giftcard_number', $giftcard_number);
					$this->db->update('giftcards');
					$this->Giftcard->log_modification(array('sale_id' => $sale_id, "number" => $giftcard_number, "old_value" => $cur_giftcard_value, "new_value" => $cur_giftcard_value + $value_to_add_subtract, "type" => $undelete ? 'sale_undelete' : 'sale_delete'));
				}
			}
	}

	function update_loyalty_simple_count($sale_id, $undelete=0)
	{
		$sale_info = $this->get_info($sale_id)->row_array();
		$store_account_payment = $sale_info['store_account_payment'];
		$customer_id = $sale_info['customer_id'];
		$suspended = $sale_info['suspended'];

	 	if (!$store_account_payment && $suspended != 2 && $customer_id > 0 && $this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'simple')
		{
			if ($sale_info['did_redeem_discount'])
			{
				$this->db->where('person_id', $customer_id);
				$this->db->set('current_sales_for_discount','current_sales_for_discount'.($undelete ? ' - ' : ' + ').$this->config->item('number_of_sales_for_discount'),false);
				$this->db->update('customers');
			}
			else
			{
				$this->db->where('person_id', $customer_id);
				$this->db->set('current_sales_for_discount','current_sales_for_discount'.($undelete ? ' + ' : ' - ').'1',false);
				$this->db->update('customers');
			}
		}
	}
	function update_points($sale_id, $undelete=0)
	{
		$sale_info = $this->get_info($sale_id)->row_array();
		$store_account_payment = $sale_info['store_account_payment'];
		$customer_id = $sale_info['customer_id'];
		$suspended = $sale_info['suspended'];

		 //Update points information if we have NOT a store account payment and not an estimate and we have a customer and we have loyalty enabled
		 if (!$store_account_payment && $suspended != 2 && $customer_id > 0 && $this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'advanced')
		 {
		   $customer_info = $this->Customer->get_info($customer_id);
			$current_points = $customer_info->points;
			$current_spend_for_points = $customer_info->current_spend_for_points;
			$total_spend_for_sale = $this->get_sale_total($sale_id);


			//Remove giftcard from spend
			$this->db->from('sales_payments');
			$this->db->like('payment_type',lang('common_giftcard'));
			$this->db->where('sale_id',$sale_id);
			$sales_payment = $this->db->get();

			if($sales_payment->num_rows() >=1)
			{
				foreach($sales_payment->result() as $row)
				{
					$total_spend_for_sale-=$row->payment_amount;
				}
			}

			//update if Store account payment exists
			$this->db->from('sales_payments');
			$this->db->where('payment_type',lang('common_points'));
			$this->db->where('sale_id',$sale_id);
			$points_payment = $this->db->get()->row_array();

			$points_payment =	isset($points_payment['payment_amount']) ? $points_payment['payment_amount'] : 0;

			//We should NOT count point payments for adding/removing points as we will do this later (at the end of this function)
			$total_spend_for_sale-=$points_payment;

		   list($spend_amount_for_points, $points_to_earn) = explode(":",$this->config->item('spend_to_point_ratio'),2);

			if($undelete) //Put points back
			{
				//If we earn any points
				if ($current_spend_for_points + abs($total_spend_for_sale) >= $spend_amount_for_points)
				{
					$total_amount_towards_points = $current_spend_for_points + abs($total_spend_for_sale);
					$new_points = (((($total_amount_towards_points)-fmod(($total_amount_towards_points), $spend_amount_for_points))/$spend_amount_for_points) * $points_to_earn);

					if ($total_spend_for_sale >= 0)
					{
						$new_point_value = $current_points + $new_points;
					}
					else
					{
						$new_point_value = $current_points - $new_points;
					}

					$new_current_spend_for_points = fmod(($current_spend_for_points + $total_spend_for_sale),$spend_amount_for_points);
				}
				else
				{
					$new_current_spend_for_points = $current_spend_for_points + $total_spend_for_sale;
					$new_point_value = $current_points;
				}

				$this->db->where('person_id', $customer_id);
				$this->db->update('customers', array('points' => $new_point_value, 'current_spend_for_points' => $new_current_spend_for_points));

				//If we are undeleting a sale; any points used should be removed back
				if ($sale_info['points_used'])
				{
 				  $this->db->set('points','points-'.$sale_info['points_used'],false);
 				  $this->db->where('person_id', $customer_id);
 				  $this->db->update('customers');
				}

		 }
		 else //Take points away
		 {
			if ($current_spend_for_points - abs($total_spend_for_sale) >=0) //Just need to remove current spend
			{
				$new_point_value = $current_points;
				$new_current_spend_for_points = $current_spend_for_points - $total_spend_for_sale;
			}
			else
			{

				$total_amount_towards_points = $current_spend_for_points + abs($total_spend_for_sale);
				$new_points =  (((($total_amount_towards_points)-fmod(($total_amount_towards_points), $spend_amount_for_points))/$spend_amount_for_points) * $points_to_earn);

				if ($total_spend_for_sale >= 0)
				{
					$new_point_value = $current_points - $new_points;
				}
				else
				{
					$new_point_value = $current_points + $new_points;
				}

				$new_current_spend_for_points = fmod(($current_spend_for_points - $total_spend_for_sale),$spend_amount_for_points);
			}

			$new_point_value = (int) round(to_currency_no_money($new_point_value));
			$new_current_spend_for_points = to_currency_no_money($new_current_spend_for_points);

			$this->db->where('person_id', $customer_id);
			$this->db->update('customers', array('points' => $new_point_value, 'current_spend_for_points' => $new_current_spend_for_points));


			//If we are deleting a sale; any points used shouold be added back
			if ($sale_info['points_used'])
			{
			  $this->db->set('points','points+'.$sale_info['points_used'],false);
			  $this->db->where('person_id', $customer_id);
			  $this->db->update('customers');
			}
		 }
	  }
	}

	function get_sale_total($sale_id)
	{
		$decimals = $this->config->item('number_of_decimals') !== NULL && $this->config->item('number_of_decimals') != '' ? (int)$this->config->item('number_of_decimals') : 2;
		$query = "SELECT ROUND(SUM(total),$decimals)as total FROM (
		(SELECT
		(ROUND(item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100,CASE WHEN tax_included =1 THEN 10 ELSE $decimals END))+(item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100)*(SUM(CASE WHEN cumulative != 1 THEN percent ELSE 0 END)/100)
		+(((item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100)*(SUM(CASE WHEN cumulative != 1 THEN percent ELSE 0 END)/100) + (item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100))
		*(SUM(CASE WHEN cumulative = 1 THEN percent ELSE 0 END))/100) as total
		FROM ".$this->db->dbprefix('sales_items')."
		INNER JOIN ".$this->db->dbprefix('sales')." ON  ".$this->db->dbprefix('sales_items').'.sale_id='.$this->db->dbprefix('sales').'.sale_id'."
		INNER JOIN ".$this->db->dbprefix('items')." ON  ".$this->db->dbprefix('sales_items').'.item_id='.$this->db->dbprefix('items').'.item_id'."
		LEFT OUTER JOIN ".$this->db->dbprefix('suppliers')." ON  ".$this->db->dbprefix('items').'.supplier_id='.$this->db->dbprefix('suppliers').'.person_id'."
		LEFT OUTER JOIN ".$this->db->dbprefix('sales_items_taxes')." ON  "
		.$this->db->dbprefix('sales_items').'.sale_id='.$this->db->dbprefix('sales_items_taxes').'.sale_id'." and "
		.$this->db->dbprefix('sales_items').'.item_id='.$this->db->dbprefix('sales_items_taxes').'.item_id'." and "
		.$this->db->dbprefix('sales_items').'.line='.$this->db->dbprefix('sales_items_taxes').'.line'. "
		LEFT OUTER JOIN ".$this->db->dbprefix('registers')." ON  ".$this->db->dbprefix('registers').'.register_id='.$this->db->dbprefix('sales').'.register_id'."
		LEFT OUTER JOIN ".$this->db->dbprefix('categories')." ON  ".$this->db->dbprefix('categories').'.id='.$this->db->dbprefix('items').'.category_id'."
		WHERE ".$this->db->dbprefix('sales').".sale_id = $sale_id
		GROUP BY ".$this->db->dbprefix('sales_items').".sale_id, ".$this->db->dbprefix('sales_items').".item_id, ".$this->db->dbprefix('sales_items').".line)
		UNION ALL
		(SELECT
		(ROUND(item_kit_unit_price*quantity_purchased-item_kit_unit_price*quantity_purchased*discount_percent/100,CASE WHEN tax_included =1 THEN 10 ELSE $decimals END))+(item_kit_unit_price*quantity_purchased-item_kit_unit_price*quantity_purchased*discount_percent/100)*(SUM(CASE WHEN cumulative != 1 THEN percent ELSE 0 END)/100)
		+(((item_kit_unit_price*quantity_purchased-item_kit_unit_price*quantity_purchased*discount_percent/100)*(SUM(CASE WHEN cumulative != 1 THEN percent ELSE 0 END)/100) + (item_kit_unit_price*quantity_purchased-item_kit_unit_price*quantity_purchased*discount_percent/100))
		*(SUM(CASE WHEN cumulative = 1 THEN percent ELSE 0 END))/100) as total
		FROM ".$this->db->dbprefix('sales_item_kits')."
		INNER JOIN ".$this->db->dbprefix('sales')." ON  ".$this->db->dbprefix('sales_item_kits').'.sale_id='.$this->db->dbprefix('sales').'.sale_id'."
		INNER JOIN ".$this->db->dbprefix('item_kits')." ON  ".$this->db->dbprefix('sales_item_kits').'.item_kit_id='.$this->db->dbprefix('item_kits').'.item_kit_id'."
		LEFT OUTER JOIN ".$this->db->dbprefix('sales_item_kits_taxes')." ON  "
		.$this->db->dbprefix('sales_item_kits').'.sale_id='.$this->db->dbprefix('sales_item_kits_taxes').'.sale_id'." and "
		.$this->db->dbprefix('sales_item_kits').'.item_kit_id='.$this->db->dbprefix('sales_item_kits_taxes').'.item_kit_id'." and "
		.$this->db->dbprefix('sales_item_kits').'.line='.$this->db->dbprefix('sales_item_kits_taxes').'.line'. "
		LEFT OUTER JOIN ".$this->db->dbprefix('registers')." ON  ".$this->db->dbprefix('registers').'.register_id='.$this->db->dbprefix('sales').'.register_id'."
		LEFT OUTER JOIN ".$this->db->dbprefix('categories')." ON  ".$this->db->dbprefix('categories').'.id='.$this->db->dbprefix('item_kits').'.category_id'."
		WHERE ".$this->db->dbprefix('sales').".sale_id = $sale_id
		GROUP BY ".$this->db->dbprefix('sales_item_kits').".sale_id, ".$this->db->dbprefix('sales_item_kits').".item_kit_id, ".$this->db->dbprefix('sales_item_kits').".line)) as total_for_sale";

		$row = $this->db->query($query)->row_array();
		if (isset($row['total']))
		{
			return $row['total'];
		}

		return 0;
	}

	function delete($sale_id, $all_data = false)
	{

		$sale_info = $this->get_info($sale_id)->row_array();
		$suspended = $sale_info['suspended'];
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;

		//Only update stock quantity if we are NOT an estimate ($suspendd = 2)
		if ($suspended != 2)
		{
			$this->db->select('sales.location_id, item_id, quantity_purchased');
			$this->db->from('sales_items');
			$this->db->join('sales', 'sales.sale_id = sales_items.sale_id');
			$this->db->where('sales_items.sale_id', $sale_id);

			foreach($this->db->get()->result_array() as $sale_item_row)
			{
				$sale_location_id = $sale_item_row['location_id'];
				$cur_item_info = $this->Item->get_info($sale_item_row['item_id']);
				$cur_item_location_info = $this->Item_location->get_info($sale_item_row['item_id'], $sale_location_id);

				$cur_item_quantity = $this->Item_location->get_location_quantity($sale_item_row['item_id'], $sale_location_id);

				if (!$cur_item_info->is_service)
				{
					//Update stock quantity
					$this->Item_location->save_quantity($cur_item_quantity + $sale_item_row['quantity_purchased'],$sale_item_row['item_id'], $sale_location_id);

					$sale_remarks =$this->config->item('sale_prefix').' '.$sale_id;
					$inv_data = array
					(
						'location_id' => $sale_location_id,
						'trans_date'=>date('Y-m-d H:i:s'),
						'trans_items'=>$sale_item_row['item_id'],
						'trans_user'=>$employee_id,
						'trans_comment'=>$sale_remarks,
						'trans_inventory'=>$sale_item_row['quantity_purchased']
					);
					$this->Inventory->insert($inv_data);
				}
			}
		}

		//Only update stock quantity + store accounts + giftcard balance if we are NOT an estimate ($suspended = 2)
		if ($suspended != 2)
		{
			$this->db->select('sales.location_id, item_kit_id, quantity_purchased');
			$this->db->from('sales_item_kits');
			$this->db->join('sales', 'sales.sale_id = sales_item_kits.sale_id');
			$this->db->where('sales_item_kits.sale_id', $sale_id);

			foreach($this->db->get()->result_array() as $sale_item_kit_row)
			{
				foreach($this->Item_kit_items->get_info($sale_item_kit_row['item_kit_id']) as $item_kit_item)
				{
					$sale_location_id = $sale_item_kit_row['location_id'];
					$cur_item_info = $this->Item->get_info($item_kit_item->item_id);
					$cur_item_location_info = $this->Item_location->get_info($item_kit_item->item_id, $sale_location_id);

					if (!$cur_item_info->is_service)
					{
						$cur_item_location_info->quantity = $cur_item_location_info->quantity !== NULL ? $cur_item_location_info->quantity : 0;

						$this->Item_location->save_quantity($cur_item_location_info->quantity + ($sale_item_kit_row['quantity_purchased'] * $item_kit_item->quantity),$item_kit_item->item_id, $sale_location_id);

						$sale_remarks =$this->config->item('sale_prefix').' '.$sale_id;
						$inv_data = array
						(
							'location_id' => $sale_location_id,
							'trans_date'=>date('Y-m-d H:i:s'),
							'trans_items'=>$item_kit_item->item_id,
							'trans_user'=>$employee_id,
							'trans_comment'=>$sale_remarks,
							'trans_inventory'=>$sale_item_kit_row['quantity_purchased'] * $item_kit_item->quantity
						);
						$this->Inventory->insert($inv_data);
					}
				}
			}

			$this->update_store_account($sale_id);
			$this->update_giftcard_balance($sale_id);
			$this->update_points($sale_id);
			$this->update_loyalty_simple_count($sale_id);

			//Only insert store account transaction if we aren't deleting the whole sale.
			//When deleting the whole sale save() takes care of this
			if (!$all_data)
			{
		 		$previous_store_account_amount = $this->get_store_account_payment_total($sale_id);
				if ($previous_store_account_amount)
				{
		
					$store_account_transaction = array(
			   		'customer_id'=>$sale_info['customer_id'],
			      	'sale_id'=>$sale_id,
						'comment'=>$sale_info['comment'],
			      	'transaction_amount'=>-$previous_store_account_amount,
						'balance'=>$this->Customer->get_info($sale_info['customer_id'])->balance,
						'date' => date('Y-m-d H:i:s')
					);
					$this->db->insert('store_accounts',$store_account_transaction);
				}
			}
		}

		if ($all_data)
		{
			$this->db->delete('sales_payments', array('sale_id' => $sale_id));
			$this->db->delete('sales_items_taxes', array('sale_id' => $sale_id));
			$this->db->delete('sales_items', array('sale_id' => $sale_id));
			$this->db->delete('sales_item_kits_taxes', array('sale_id' => $sale_id));
			$this->db->delete('sales_item_kits', array('sale_id' => $sale_id));
		}

		$this->db->where('sale_id', $sale_id);
		return $this->db->update('sales', array('deleted' => 1,'deleted_by'=>$employee_id));
	}

	function undelete($sale_id)
	{

		$sale_info = $this->get_info($sale_id)->row_array();
		$suspended = $sale_info['suspended'];
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;

		//Only update stock quantity + store accounts + giftcard balance if we are NOT an estimate ($suspended = 2)
		if ($suspended != 2)
		{
			$this->db->select('sales.location_id, item_id, quantity_purchased');
			$this->db->from('sales_items');
			$this->db->join('sales', 'sales.sale_id = sales_items.sale_id');
			$this->db->where('sales_items.sale_id', $sale_id);

			foreach($this->db->get()->result_array() as $sale_item_row)
			{
				$sale_location_id = $sale_item_row['location_id'];
				$cur_item_info = $this->Item->get_info($sale_item_row['item_id']);
				$cur_item_location_info = $this->Item_location->get_info($sale_item_row['item_id'], $sale_location_id);

				if (!$cur_item_info->is_service && $cur_item_location_info->quantity !== NULL)
				{
					//Update stock quantity
					$this->Item_location->save_quantity($cur_item_location_info->quantity - $sale_item_row['quantity_purchased'],$sale_item_row['item_id']);

					$sale_remarks =$this->config->item('sale_prefix').' '.$sale_id;
					$inv_data = array
					(
						'location_id' => $sale_location_id,
						'trans_date'=>date('Y-m-d H:i:s'),
						'trans_items'=>$sale_item_row['item_id'],
						'trans_user'=>$employee_id,
						'trans_comment'=>$sale_remarks,
						'trans_inventory'=>-$sale_item_row['quantity_purchased']
						);
					$this->Inventory->insert($inv_data);
				}
			}
            
			$this->update_store_account($sale_id,1);
			$this->update_giftcard_balance($sale_id,1);
			$this->update_points($sale_id,1);
			$this->update_loyalty_simple_count($sale_id,1);

		 	$previous_store_account_amount = $this->get_store_account_payment_total($sale_id);

			if ($previous_store_account_amount)
			{
			 	$this->db->where('sale_id',$sale_id);
				$this->db->update('store_accounts',array('deleted' => 0));
			}


			$this->db->select('sales.location_id, item_kit_id, quantity_purchased');
			$this->db->from('sales_item_kits');
			$this->db->join('sales', 'sales.sale_id = sales_item_kits.sale_id');
			$this->db->where('sales_item_kits.sale_id', $sale_id);

			foreach($this->db->get()->result_array() as $sale_item_kit_row)
			{
				foreach($this->Item_kit_items->get_info($sale_item_kit_row['item_kit_id']) as $item_kit_item)
				{
					$sale_location_id = $sale_item_kit_row['location_id'];
					$cur_item_info = $this->Item->get_info($item_kit_item->item_id);
					$cur_item_location_info = $this->Item_location->get_info($item_kit_item->item_id, $sale_location_id);
					if (!$cur_item_info->is_service && $cur_item_location_info->quantity !== NULL)
					{
						$this->Item_location->save_quantity($cur_item_location_info->quantity - ($sale_item_kit_row['quantity_purchased'] * $item_kit_item->quantity),$item_kit_item->item_id, $sale_location_id);

						$sale_remarks =$this->config->item('sale_prefix').' '.$sale_id;
						$inv_data = array
						(
							'location_id' => $sale_location_id,
							'trans_date'=>date('Y-m-d H:i:s'),
							'trans_items'=>$item_kit_item->item_id,
							'trans_user'=>$employee_id,
							'trans_comment'=>$sale_remarks,
							'trans_inventory'=>-$sale_item_kit_row['quantity_purchased'] * $item_kit_item->quantity
						);
						$this->Inventory->insert($inv_data);
					}
				}
			}
		}

		$this->db->where('sale_id', $sale_id);
		return $this->db->update('sales', array('deleted' => 0, 'deleted_by' => NULL));
	}

	function get_sale_items($sale_id)
	{
		$this->db->from('sales_items');
		$this->db->where('sale_id',$sale_id);
		$this->db->order_by('line');
		return $this->db->get();
	}
	
	function get_sale_items_categories($sale_id)
	{
		$this->db->select("categories.name");
		$this->db->from('sales_items');
		$this->db->join("items", "items.item_id = sales_items.item_id");
		$this->db->join("categories", "categories.id = items.category_id");
		$this->db->where('sale_id',$sale_id);
		$this->db->group_by("categories.name");
		$this->db->order_by('line');
	 	$query = $this->db->get();
	 	return $query->result();
	}

	function get_sale_items_ordered_by_category($sale_id)
	{
		$this->db->select('items.*, sales_items.*, categories.name as category, sales_items.description as sales_items_description');
		$this->db->from('sales_items');
		$this->db->join('items', 'items.item_id = sales_items.item_id');
		$this->db->join('categories', 'categories.id = items.category_id');
		$this->db->where('sale_id',$sale_id);
		$this->db->order_by('categories.name, items.name');
		return $this->db->get();
	}

	function get_sale_item_kits($sale_id)
	{
		$this->db->from('sales_item_kits');
		$this->db->where('sale_id',$sale_id);
		$this->db->order_by('line');
		return $this->db->get();
	}

	function get_sale_item_kits_ordered_by_category($sale_id)
	{
		$this->db->select('item_kits.*, sales_item_kits.*, categories.name as category');
		$this->db->from('sales_item_kits');
		$this->db->join('item_kits', 'item_kits.item_kit_id = sales_item_kits.item_kit_id');
		$this->db->join('categories', 'categories.id = item_kits.category_id');
		$this->db->where('sale_id',$sale_id);
		$this->db->order_by('categories.name, item_kits.name');
		return $this->db->get();
	}

	function get_sale_items_taxes($sale_id, $line = FALSE)
	{
		$item_where = '';

		if ($line)
		{
			$item_where = 'and '.$this->db->dbprefix('sales_items').'.line = '.$line;
		}

		$query = $this->db->query('SELECT name, percent, cumulative, item_unit_price as price, quantity_purchased as quantity, discount_percent as discount '.
		'FROM '. $this->db->dbprefix('sales_items_taxes'). ' JOIN '.
		$this->db->dbprefix('sales_items'). ' USING (sale_id, item_id, line) '.
		'WHERE '.$this->db->dbprefix('sales_items_taxes').".sale_id = $sale_id".' '.$item_where.' '.
		'ORDER BY '.$this->db->dbprefix('sales_items').'.line,'.$this->db->dbprefix('sales_items').'.item_id,cumulative,name,percent');

        return $query->result_array();
	}

	function get_sale_item_kits_taxes($sale_id, $line = FALSE)
	{
		$item_kit_where = '';

		if ($line)
		{
			$item_kit_where = 'and '.$this->db->dbprefix('sales_item_kits').'.line = '.$line;
		}

		$query = $this->db->query('SELECT name, percent, cumulative, item_kit_unit_price as price, quantity_purchased as quantity, discount_percent as discount '.
		'FROM '. $this->db->dbprefix('sales_item_kits_taxes'). ' JOIN '.
		$this->db->dbprefix('sales_item_kits'). ' USING (sale_id, item_kit_id, line) '.
		'WHERE '.$this->db->dbprefix('sales_item_kits_taxes').".sale_id = $sale_id".' '.$item_kit_where.' '.
		'ORDER BY '.$this->db->dbprefix('sales_item_kits').'.line,'.$this->db->dbprefix('sales_item_kits').'.item_kit_id,cumulative,name,percent');
		return $query->result_array();
	}

	function get_sale_payments($sale_id)
	{
		$this->db->from('sales_payments');
		$this->db->where('sale_id',$sale_id);
		return $this->db->get();
	}
	function get_customer_to_service($sale_id)
	{
		$this->db->from('sales_more_customer_service_sale');
		$this->db->where('sale_id',$sale_id);
		return $this->db->get();
	}
	function get_customer($sale_id)
	{
		$this->db->from('sales');
		$this->db->where('sale_id',$sale_id);
		return $this->Customer->get_info($this->db->get()->row()->customer_id);
	}

	function get_comment($sale_id)
	{
		$this->db->from('sales');
		$this->db->where('sale_id',$sale_id);
        $result = $this->db->get()->row_array()['comment'];
       
		return $result;
	}	
    function getSaleComments($sale_id)
	{
		$this->db->from('sales');
		$this->db->where('sale_id',$sale_id);
        $result = $this->db->get()->row_array();
        $comment['comment_term'] = $result['comment_term'];
        $comment['comment_guarantee'] = $result['comment_guarantee'];
        $comment['comment_payment'] = $result['comment_payment'];
		return $comment;
	}

	function get_comment_on_receipt($sale_id)
	{
		$this->db->from('sales');
		$this->db->where('sale_id',$sale_id);
		return $this->db->get()->row()->show_comment_on_receipt;
	}

	function get_sold_by_employee_id($sale_id)
	{
		$this->db->from('sales');
		$this->db->where('sale_id',$sale_id);
		return $this->db->get()->row()->sold_by_employee_id;
	}

	//We create a temp table that allows us to do easy report/sales queries
	public function create_sales_items_temp_table($params)
	{
		$where = '';
		if (isset($params['sale_ids']))
		{
			if (!empty($params['sale_ids']))
			{
				for($k=0;$k<count($params['sale_ids']);$k++)
				{
					$params['sale_ids'][$k] = $this->db->escape($params['sale_ids'][$k]);
				}

				$where.='WHERE '.$this->db->dbprefix('sales').".sale_id IN(".implode(',', $params['sale_ids']).")";
			}
			else
			{
				$where.='WHERE '.$this->db->dbprefix('sales').".sale_id IN(0)";
			}
		}
		elseif (isset($params['start_date']) && isset($params['end_date']))
		{
			$location_ids = implode(',',Report::get_selected_location_ids());

			$where = 'WHERE sale_time BETWEEN '.$this->db->escape($params['start_date']).' and '.$this->db->escape($params['end_date']).' and '.$this->db->dbprefix('sales').'.location_id IN ('.$location_ids.')'. (($this->config->item('hide_store_account_payments_in_reports') ) ? ' and '.$this->db->dbprefix('sales').'.store_account_payment=0' : '');

			//Added for detailed_suspended_report, we don't need this for other reports as we are always going to have start + end date
			if (isset($params['force_suspended']) && $params['force_suspended'])
			{
				$where .=' and (suspended != 0 or (was_layaway = 1 or was_estimate = 1))';
			}
			elseif ($this->config->item('hide_layaways_sales_in_reports'))
			{
				$where .=' and suspended = 0';
			}
			else
			{
				$where .=' and suspended != 2';
			}
		}
		elseif ($this->config->item('hide_layaways_sales_in_reports'))
		{
			$location_ids = implode(',',Report::get_selected_location_ids());
			$where .='WHERE suspended = 0'.' and '.$this->db->dbprefix('sales').'.location_id IN ('.$location_ids.')'.(($this->config->item('hide_store_account_payments_in_reports') ) ? ' and '.$this->db->dbprefix('sales').'.store_account_payment=0' : '');
		}
		else
		{
			$location_ids = implode(',',Report::get_selected_location_ids());
			$where .='WHERE suspended != 2'.' and '.$this->db->dbprefix('sales').'.location_id IN ('.$location_ids.')'.(($this->config->item('hide_store_account_payments_in_reports') ) ? ' and '.$this->db->dbprefix('sales').'.store_account_payment=0' : '');
		}

		if ($where == '')
		{
			$location_ids = implode(',',Report::get_selected_location_ids());
			$where = 'WHERE suspended != 2 and '.$this->db->dbprefix('sales').'.location_id IN ('.$location_ids.')'.(($this->config->item('hide_store_account_payments_in_reports') ) ? ' and '.$this->db->dbprefix('sales').'.store_account_payment=0' : '');
		}
		$return = $this->_create_sales_items_temp_table_query($where);
		return $return;
	}

	

	function drop_sales_items_temp_table()
	{
		$this->db->query('DROP TABLE IF EXISTS '.$this->db->dbprefix('sales_items_temp'));
	}

	public function get_giftcard_value( $giftcardNumber )
	{
		if ( !$this->Giftcard->exists( $this->Giftcard->get_giftcard_id($giftcardNumber)))
			return 0;

		$this->db->from('giftcards');
		$this->db->where('giftcard_number',$giftcardNumber);
		return $this->db->get()->row()->value;
	}

    /**
     * @param $person_id
     * @return array|null
     */
	public function get_related_sale_ids($person_id) {
        $related_sale_ids = [];

        // Get Sales By Owner
        $this->db->distinct();
        $this->db->select('sale_id');
        $this->db->from('sales');
        $this->db->where('sales.employee_id', $person_id);
        $query = $this->db->get();
        if ($query) {
            $result = $query->result();
            $query->free_result();
            $this->db->flush_cache();
            foreach ($result as $row) {
                $related_sale_ids[$row->sale_id] = $row->sale_id;
            }
        }

        // Get Sales By Assigning
        $this->db->distinct();
        $this->db->select('sale_id');
        $this->db->from('sales_employees');
        $this->db->where('sales_employees.employee_id', $person_id);
        $query = $this->db->get();
        if ($query) {
            $result = $query->result();
            $query->free_result();
            $this->db->flush_cache();
            foreach ($result as $row) {
                $related_sale_ids[$row->sale_id] = $row->sale_id;
            }
        }

        // Get Related By Task IDS
        $this->db->distinct();
        $this->db->select('sale_id');
        $this->db->from('tasks');
        $this->db->join('task_user_relations', 'tasks.id = task_user_relations.task_id');
        $this->db->join('employees', 'employees.id = task_user_relations.user_id');
        $this->db->where('employees.person_id', $person_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result();
            $query->free_result();
            $this->db->flush_cache();
            foreach ($result as $row) {
                $related_sale_ids[$row->sale_id] = $row->sale_id;
            }
        }
        return $related_sale_ids;
	}

	function get_all_suspended($params = array(), $suspended_types = array(1,2))
	{
		$location_id = $this->Employee->get_logged_in_employee_current_location_id();
        $person_id = $this->Employee->get_logged_in_employee_info()->person_id;
        // Get Related Orders
        $related_sale_ids = $this->get_related_sale_ids($person_id);
        $list_sale_id = [];
	    if (!empty($params['supporter_id'])) {
    	    $this->db->select("sales_employees.sale_id");
    	    $this->db->from("employees");
    	    $this->db->join("sales_employees", "sales_employees.employee_id = employees.person_id");
    	    $this->db->join('sales', 'sales.sale_id = sales_employees.sale_id');
    	    // $this->db->where("employees.id", $params['supporter_id']);
    	    $this->db->where("sales_employees.employee_id", $params['supporter_id']);
    	    $this->db->or_where('sales.employee_id', $params['supporter_id']);
    	    $this->db->group_by('sales.sale_id');

    	    $query = $this->db->get();
    	    if ($query->num_rows() > 0) {
    	        $result = $query->result();
    	        for ($i = 0; $i < count($result); $i++) {
    	            $list_sale_id[$i] = $result[$i]->sale_id;
    	        }
    	    }
	    }

	    $lst_sale_id_in_item = [];
	    if (!empty($params['item_id'])) {
	        $this->db->select("sale_id");
	        $this->db->from("sales_items");
	        $this->db->where("item_id", $params['item_id']);
	        $query = $this->db->get();
	        if ($query->num_rows() > 0) {
	            $result = $query->result();
	            for ($i = 0; $i < count($result); $i++) {
	                $lst_sale_id_in_item[$i] = $result[$i]->sale_id;
	            }
	        }
	    }

	    $this->db->select('sales.customer_id, sales.employee_id,sales.sold_by_employee_id,sales.sale_id,sales.suspended,sales.location_id,sales.supporter,sales.sale_status_id,sales.task_id,sales.comment_term,sale_time');
		$this->db->from('sales');
		$this->db->join('customers', 'sales.customer_id = customers.person_id', 'left');
		$this->db->join('people', 'customers.person_id = people.person_id', 'left');
		$this->db->join('sale_status', 'sales.sale_status_id = sale_status.status_id', 'left');
		$this->db->where('sales.deleted', 0);

		if(!empty($params['view']=='view_scope_all'))
		{

		}
		else if(!empty($params['view']=='view_scope_location'))
		{
			$this->db->where('sales.location_id', $location_id);
		}
		else
		{
			// Check Assigned Sale Orders
			if (!empty($related_sale_ids)) {
                $this->db->where_in('sales.sale_id', $related_sale_ids);
			}
		}
		if (!empty($params['status_type'])) {
			$this->db->where('sale_status.status_type', $params['status_type']);
		}
		
		if (!empty($params['from_date'])) {
		    $this->db->where('sales.sale_time >= ', $params['from_date']);
		    $params['from_date'] = date("Y-m-d 00:00:00",strtotime($params['from_date']));
		}
		if (!empty($params['to_date'])) {
			$params['to_date'] = date("Y-m-d 23:59:59",strtotime($params['to_date']));
		    $this->db->where('sales.sale_time <= ', $params['to_date']);
		}
		if (!empty($params['status_id'])) {
		    $this->db->where('sales.sale_status_id', $params['status_id']);
		}
		if (!empty($params['employee_id'])) {
		    $this->db->where('sales.employee_id', $params['employee_id']);
		}
		if (!empty($params['supporter_id'])) {
    		if (count($list_sale_id) > 0) {
    		    $this->db->where_in('sale_id', $list_sale_id);
    		} else {
    		    $this->db->where('sale_id', 0);
    		}
		}
		if (!empty($params['customer_name'])) {
		    $this->db->like('people.last_name', $params['customer_name']);
		}
		if (!empty($params['item_id'])) {
		    if (count($lst_sale_id_in_item) > 0) {
		        $this->db->where_in('sale_id', $lst_sale_id_in_item);
		    } else {
		        $this->db->where('sale_id', 0);
		    }
		}

		if (!empty($params['implement'])) {
			$this->db->join('phppos_sales_employees', 'sales.sale_id = phppos_sales_employees.sale_id');
			$this->db->group_by('phppos_sales_employees.sale_id');
		}
		
		// $this->db->where_in('suspended', $suspended_types);
		
		$this->db->order_by('sale_id');
		$sales = $this->db->get()->result_array();
// echo $this->db->last_query();die();
		for($k=0;$k<count($sales);$k++)
		{
			$item_names = array();
			$this->db->select('name,calculatedPrice');
			$this->db->from('items');
			$this->db->join('sales_items', 'sales_items.item_id = items.item_id');
			$this->db->where('sale_id', $sales[$k]['sale_id']);
			$item_cal= 0;
			foreach($this->db->get()->result_array() as $row)
			{
				$item_names[] = $row['name'];
				$item_cal += $row['calculatedPrice'];
			}


			$sales[$k]['items'] = implode(', ', $item_names);
			$sales[$k]['calculatedPrice'] = $item_cal;

		}

		return $sales;

	}

	function count_all()
	{
		$this->db->from('sales');
		$this->db->where('deleted',0);

		if ($this->config->item('hide_store_account_payments_in_reports'))
		{
			$this->db->where('store_account_payment',0);
		}

		return $this->db->count_all_results();
	}

	function get_recent_sales_for_customer($customer_id)
	{
		$return = array();

		$this->db->select('sales.*, SUM(quantity_purchased) as items_purchased');
		$this->db->from('sales');
		$this->db->join('sales_items', 'sales.sale_id = sales_items.sale_id');
		$this->db->where('customer_id', $customer_id);
		$this->db->where('deleted', 0);
		$this->db->order_by('sale_time DESC');
		$this->db->group_by('sales.sale_id');
		$this->db->limit($this->config->item('number_of_recent_sales') ? $this->config->item('number_of_recent_sales') : 10);

		foreach($this->db->get()->result_array() as $row)
		{
			$return[] = $row;
		}

		return $return;
	}

	function get_store_account_payment_total($sale_id)
	{
		$this->db->select('SUM(payment_amount) as store_account_payment_total', false);
		$this->db->from('sales_payments');
		$this->db->where('sale_id', $sale_id);
		$this->db->where('payment_type', lang('common_store_account'));

		$sales_payments = $this->db->get()->row_array();

		return $sales_payments['store_account_payment_total'] ? $sales_payments['store_account_payment_total'] : 0;
	}

	function get_deleted_taxes($sale_id)
	{
		$this->db->from('sales');
		$this->db->where('sale_id',$sale_id);
		return unserialize($this->db->get()->row()->deleted_taxes);
	}

	function get_sales_per_day_for_range($start_date, $end_date)
	{
		$logged_in_location_id = $this->Employee->get_logged_in_employee_current_location_id();

		$this->db->select('count(*) as count, date(sale_time) as sale_date', false);
		$this->db->from('sales');
		$this->db->group_by('sale_date');
		$this->db->order_by('sale_date');
		$this->db->where('location_id', $logged_in_location_id);
		$this->db->where('sale_time BETWEEN '.$this->db->escape($start_date).' and '.$this->db->escape($end_date).' and deleted = 0');
		$return = $this->db->get()->result_array();
		return $return;
	}

	function get_quantity_sold_for_item_in_sale($sale_id, $item_id)
	{
		$this->db->select('quantity_purchased');
		$this->db->from('sales_items');
		$this->db->where('sale_id',$sale_id);
		$this->db->where('item_id',$item_id);
		$row = $this->db->get()->row_array();

		return empty($row) ? 0 : $row['quantity_purchased'];
	}

	function get_quantity_sold_for_item_kit_in_sale($sale_id, $item_kit_id)
	{
		$this->db->select('quantity_purchased');
		$this->db->from('sales_item_kits');
		$this->db->where('sale_id',$sale_id);
		$this->db->where('item_kit_id',$item_kit_id);
		$row = $this->db->get()->row_array();

		return empty($row) ? 0 : $row['quantity_purchased'];

	}

	function can_void_cc_sale($sale_id)
	{
		$processor = false;

		if ($this->Location->get_info_for_key('credit_card_processor') == 'mercury' || !$this->Location->get_info_for_key('credit_card_processor'))
		{
			$processor = 'mercury';
		}
		elseif($this->Location->get_info_for_key('credit_card_processor') == 'stripe')
		{
			$processor = 'stripe';
		}
		elseif($this->Location->get_info_for_key('credit_card_processor') == 'braintree')
		{
			$processor = 'braintree';
		}

		$this->db->from('sales_payments');
		$this->db->where('sale_id',$sale_id);
		$this->db->where_in('payment_type', array(lang('common_credit'),lang('sales_partial_credit')));

		$result = $this->db->get()->result_array();

		if (empty($result))
		{
			return FALSE;
		}

		foreach($result as $row)
		{
			if ($processor == 'mercury')
			{
				if(!($row['auth_code'] && $row['ref_no'] && $row['cc_token'] && $row['acq_ref_data'] && $row['process_data'] && $row['payment_amount'] > 0))
				{
					return FALSE;
				}
			}
			elseif($processor == 'stripe' || $processor == 'braintree')
			{
				if (!$row['ref_no'])
				{
					return FALSE;
				}
			}
		}

		return TRUE;
	}

	function can_void_cc_return($sale_id)
	{
		$processor = false;


		if ($this->Location->get_info_for_key('credit_card_processor') == 'mercury' || !$this->Location->get_info_for_key('credit_card_processor'))
		{
			$processor = 'mercury';
		}
		elseif($this->Location->get_info_for_key('credit_card_processor') == 'stripe')
		{
			$processor = 'stripe';
		}
		elseif($this->Location->get_info_for_key('credit_card_processor') == 'braintree')
		{
			$processor = 'braintree';
		}

		$this->db->from('sales_payments');
		$this->db->where('sale_id',$sale_id);
		$this->db->where_in('payment_type', array(lang('common_credit'),lang('sales_partial_credit')));

		$result = $this->db->get()->result_array();

		if (empty($result))
		{
			return FALSE;
		}

		foreach($result as $row)
		{
			if ($processor == 'mercury')
			{
				//TODO: Don't need acq_ref_data for EMV USB for some reason...Should find out why
				if(!($row['auth_code'] && $row['ref_no'] && $row['cc_token'] && $row['process_data'] && $row['payment_amount'] < 0))
				{
					return FALSE;
				}

			}
			elseif($processor == 'stripe' || $processor == 'braintree')
			{
				return FALSE;
			}
		}

		return TRUE;
	}

	function get_item_ids_sold_for_date_range($start_date, $end_date, $supplier_id, $location_id = FALSE)
	{
		if ($location_id === FALSE)
		{
			$location_id = $this->Employee->get_logged_in_employee_current_location_id();
		}

		$this->db->select('sales_items.item_id');
		$this->db->from('sales_items');
		$this->db->join('items', 'sales_items.item_id = items.item_id');
		$this->db->join('sales', 'sales.sale_id = sales_items.sale_id');
		$this->db->where('sale_time BETWEEN '.$this->db->escape($start_date).' and '.$this->db->escape($end_date).' and sales.deleted = 0');
		$this->db->where('supplier_id', $supplier_id);
		$this->db->where('location_id', $location_id);
		$item_ids = array();

		foreach($this->db->get()->result_array() as $row)
		{
			$item_ids[$row['item_id']] = $row['item_id'];
		}

		return array_values($item_ids);
	}

	function get_last_sale_id($location_id = FALSE)
	{
		if ($location_id === FALSE)
		{
			$location_id = $this->Employee->get_logged_in_employee_current_location_id();
		}

		$this->db->select('sale_id');
		$this->db->from('sales');
		$this->db->where('deleted', 0);
		$this->db->where('location_id', $location_id);
		$this->db->order_by('sale_id DESC');
		$this->db->limit(1);
		$query = $this->db->get();

		if ($row = $query->row_array())
		{
			return $row['sale_id'];
		}

		return FALSE;

	}

	function get_global_weighted_average_cost()
	{
		$current_location=$this->Employee->get_logged_in_employee_current_location_id();

		$this->db->select('sum(IFNULL('.$this->db->dbprefix('location_items').'.cost_price, '.$this->db->dbprefix('items').'.cost_price) * quantity) / sum(quantity) as weighted_cost', FALSE);
		$this->db->from('items');
		$this->db->join('location_items', 'location_items.item_id = items.item_id and location_id = '.$current_location, 'left');
		$this->db->where('is_service !=', 1);
		$this->db->where('items.deleted', 0);

		$row = $this->db->get()->row_array();

		return $row['weighted_cost'];

	}

	function getAllSalesByCustomer($customer_id ='')
	{
		$this->db->select('sales.*,people.last_name, people.first_name, items.product_id, items.name, items.unit_price, items.item_id, sales_items.item_unit_price, sales_items_taxes.percent')->from('sales');
		$this->db->join('items', 'items.item_id = sales.sale_id');
		$this->db->join('people', 'sales.employee_id = people.person_id');
		$this->db->join('sales_items', 'sales_items.sale_id = sales.sale_id');
		$this->db->join('sales_items_taxes', 'sales_items_taxes.sale_id = sales.sale_id');
		$this->db->limit(1);
		$this->db->where('sales.customer_id =' .$customer_id);
		//$this->db->where('items.deleted = 0');
		$this->db->order_by('sales.delivery_date desc');
		$result = $this->db->get();
		return $result->result_array();
	}

	function getMailByPerson($person_id){

		$this->db->select('*')->from('mail_history');
		$this->db->where('person_id='.$person_id);
		$result = $this->db->get();
		return $result->result_array();
	}

	function get_all_suspended_hd()
	{
		$location_id = $this->Employee->get_logged_in_employee_current_location_id();

		$this->db->from('sales');
		$this->db->join('customers', 'sales.customer_id = customers.person_id', 'left');
		$this->db->join('people', 'customers.person_id = people.person_id', 'left');
		$this->db->where('sales.deleted', 0);
		$this->db->where('location_id', $location_id);
		$this->db->order_by('sale_id');
		$sales = $this->db->get()->result_array();

		for($k=0;$k<count($sales);$k++)
		{
			$item_names = array();
			$this->db->select('name');
			$this->db->from('items');
			$this->db->join('sales_items', 'sales_items.item_id = items.item_id');
			$this->db->where('sale_id', $sales[$k]['sale_id']);

			foreach($this->db->get()->result_array() as $row)
			{
				$item_names[] = $row['name'];
			}

			$this->db->select('name');
			$this->db->from('item_kits');
			$this->db->join('sales_item_kits', 'sales_item_kits.item_kit_id = item_kits.item_kit_id');
			$this->db->where('sale_id', $sales[$k]['sale_id']);

			foreach($this->db->get()->result_array() as $row)
			{
				$item_names[] = $row['name'];
			}


			$sales[$k]['items'] = implode(', ', $item_names);
		}

		return $sales;

	}
    
    public function getCustomerFromSale($sale_id) 
    {
		$this->db->from('sales');
		$this->db->join('customers', 'sales.customer_id = customers.person_id', 'left');
		$this->db->join('people', 'customers.person_id = people.person_id', 'left');
        $this->db->where('sales.sale_id',$sale_id);
        return $this->db->get()->row_array();
    }
    
    public function get_years_create() {
        $this->db->select("distinct(date_format(sale_time, '%Y')) as sale_year");
        $this->db->from("sales");
        $this->db->order_by('sale_time', 'asc');
        return $this->db->get()->result_array();
    }
}
?>
