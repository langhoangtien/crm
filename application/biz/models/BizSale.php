<?php
require_once (APPPATH . "models/Sale.php");
class BizSale extends Sale
{
	# Xóa đơn hàng
	/**
	 * @param $sale_id
	 * @param bool $all_data
	 * @return bool|object
     */
	function delete($sale_id, $all_data = false)
	{
		$sale_info = $this->get_info($sale_id)->row_array();
		$suspended = $sale_info['suspended'];
		$return = $sale_info['return'];
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		
		if($suspended == 1){
			$thay_doi_so_luong_kho = false;
		} elseif ($suspended == 0) {
			$thay_doi_so_luong_kho = true;
		} elseif ($suspended == 2) {
			$thay_doi_so_luong_kho = false;
		}

	

			if($thay_doi_so_luong_kho) {
                $this->db->from('sales_items');
                $this->db->join('sales', 'sales.sale_id = sales_items.sale_id');
                $this->db->where('sales_items.sale_id', $sale_id);
                foreach($this->db->get()->result_array() as $sale_item_row)
                {
                    $sale_remarks =$this->config->item('sale_prefix').' '.$sale_id;
                    $sale_location_id = $sale_item_row['location_id'];
                    $cur_item_info = $this->Item->get_info($sale_item_row['item_id']);

                    $cur_item_quantity = $this->Item_location->get_location_quantity($sale_item_row['item_id'], $sale_location_id);
                        if($return == 1){
                            if($sale_item_row['quantity_purchased']<0){
                                $so_luong_thay_doi = $cur_item_quantity + $sale_item_row['quantity_purchased'];
                                $so_luong_xuat_kho = $sale_item_row['quantity_purchased'];
                            } else {
                                $so_luong_thay_doi = $cur_item_quantity - $sale_item_row['quantity_purchased'];
                                $so_luong_xuat_kho = -$sale_item_row['quantity_purchased'];
                            }
                            $so_luong_thay_doi = $cur_item_quantity + $sale_item_row['quantity_purchased'];
                            $so_luong_xuat_kho = $sale_item_row['quantity_purchased'];
                            $comment = 'Xuất kho do xóa đơn trả hàng '.$sale_remarks;

                        } else {
                            $so_luong_thay_doi = $cur_item_quantity + $sale_item_row['quantity_purchased'];
                            $so_luong_xuat_kho = -$sale_item_row['quantity_purchased'];
                            $comment = 'Nhập lại sản phẩm do xóa đơn hàng '.$sale_remarks;

                        }
                    if (!$cur_item_info->is_service)
                    {
                        //Update stock quantity
                            $this->Item_location->save_quantity($so_luong_thay_doi,$sale_item_row['item_id'], $sale_location_id);

                            $inv_data = array (
                                'location_id' => $sale_location_id,
                                'trans_date'=>date('Y-m-d H:i:s'),
                                'trans_items'=>$sale_item_row['item_id'],
                                'trans_user'=>$employee_id,
                                'trans_comment'=>$comment,
                                'trans_inventory'=>$so_luong_xuat_kho
                        );
                        $this->Inventory->insert($inv_data);
                    }
                }
            }

			$this->update_store_account($sale_id);
			$this->update_giftcard_balance($sale_id);
			$this->update_points($sale_id);
			$this->update_loyalty_simple_count($sale_id);

					$this->db->where('sale_id',$sale_id);
			$this->db->update('store_accounts',array('deleted' => 0));

			if($all_data){
                $this->db->delete('sales_payments', array('sale_id' => $sale_id));
                $this->db->delete('sales_items_taxes', array('sale_id' => $sale_id));
                $this->db->delete('sales_items', array('sale_id' => $sale_id));
                $this->db->delete('sales_item_kits_taxes', array('sale_id' => $sale_id));
                $this->db->delete('sales_item_kits', array('sale_id' => $sale_id));
            }


            $this->db->where('sale_id', $sale_id);
			$ket_qua = $this->db->update('sales', array('deleted' => 1,'deleted_by'=>$employee_id));
		}
	# Khôi phục đơn hàng
	/**
	 * @param $sale_id
	 * @param bool $all_data
	 * @return bool|object
	 */

	function undelete($sale_id, $all_data = false)
	{
		$sale_info = $this->get_info($sale_id)->row_array();
		$suspended = $sale_info['suspended'];
		$return = $sale_info['return'];
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		if($suspended == 1){
			$thay_doi_so_luong_kho = false;
		} elseif ($suspended == 0) {
			$thay_doi_so_luong_kho = true;
	}

		$this->db->from('sales_items');
		$this->db->join('sales', 'sales.sale_id = sales_items.sale_id');
		$this->db->where('sales_items.sale_id', $sale_id);
		if($thay_doi_so_luong_kho) {
			foreach($this->db->get()->result_array() as $sale_item_row)
			{
				$sale_remarks =$this->config->item('sale_prefix').' '.$sale_id;
				$sale_location_id = $sale_item_row['location_id'];
				$cur_item_info = $this->Item->get_info($sale_item_row['item_id']);

				$cur_item_quantity = $this->Item_location->get_location_quantity($sale_item_row['item_id'], $sale_location_id);
				if($return == 1){
					if($sale_item_row['quantity_purchased']<0){
						$so_luong_thay_doi = $cur_item_quantity - $sale_item_row['quantity_purchased'];
						$so_luong_xuat_kho = -$sale_item_row['quantity_purchased'];

					} else {
						$so_luong_thay_doi = $cur_item_quantity + $sale_item_row['quantity_purchased'];
						$so_luong_xuat_kho = $sale_item_row['quantity_purchased'];
		}

					$comment = 'Nhập kho sản phẩm do khôi phục đơn trả hàng '.$sale_remarks;
				} else {
					$so_luong_thay_doi = $cur_item_quantity - $sale_item_row['quantity_purchased'];
					$so_luong_xuat_kho = -$sale_item_row['quantity_purchased'];
					$comment = 'Xuất sản phẩm do khôi phục đơn hàng '.$sale_remarks;

			}
				if (!$cur_item_info->is_service)
			{
					//Update stock quantity
					$this->Item_location->save_quantity($so_luong_thay_doi,$sale_item_row['item_id'], $sale_location_id);

					$inv_data = array (
						'location_id' => $sale_location_id,
						'trans_date'=>date('Y-m-d H:i:s'),
						'trans_items'=>$sale_item_row['item_id'],
						'trans_user'=>$employee_id,
						'trans_comment'=>$comment,
						'trans_inventory'=>$so_luong_xuat_kho
			);
					$this->Inventory->insert($inv_data);
		}

		}
		}
		$this->update_store_account($sale_id,1);
		$this->update_giftcard_balance($sale_id,1);
		$this->update_points($sale_id,1);
		$this->update_loyalty_simple_count($sale_id,1);

		$previous_store_account_amount = $this->get_store_account_payment_total($sale_id);

		if ($previous_store_account_amount) {
		$this->db->where('sale_id',$sale_id);
			$this->db->update('store_accounts',array('deleted' => 0));
		}
		$ket_qua = $this->db->update('sales', array('deleted' => 0,'deleted_by'=>NULL));
		return $ket_qua;

		}

		public function getAllInfo($saleId = 0)
		{
			$this->db->select("sales.*, people.*");
			$this->db->from('sales');
			$this->db->join('employees', 'sales.employee_id = employees.person_id', 'left');
			$this->db->join('people', 'employees.person_id = people.person_id', 'left');
			$this->db->where('sales.deleted', 0);
			$this->db->where('sales.sale_id',$saleId);
			$result = $this->db->get()->result_array();
			if (isset($result[0])) {
				$saleInfo = $result[0];
				$item_names = array();
				$this->db->select('name');
				$this->db->from('items');
				$this->db->join('sales_items', 'sales_items.item_id = items.item_id');
				$this->db->where('sale_id', $saleInfo['sale_id']);
				foreach($this->db->get()->result_array() as $row)
				{
					$item_names[] = $row['name'];
				}
				$saleInfo['items'] = implode(',', $item_names);
				return $saleInfo;
			}
			return null;
		} 
	/**
	 * @param $items
	 * @param $customer_id
	 * @param $employee_id
	 * @param $sold_by_employee_id
	 * @param $comment
	 * @param $show_comment_on_receipt
	 * @param $payments
	 * @param $add_more_customer_to_service
	 * @param bool $sale_id
	 * @param int $suspended
	 * @param bool $change_sale_date
	 * @param int $balance
	 * @param int $store_account_payment
	 * @param array $extraData
	 * @param null $service_id
	 * @param null $code
	 * @param int $assigment
	 * @param int $return
	 * @param int $is_vat
     * @param null $cust_info
     * @return bool|int
	 * $thong_bao_loi xem lỗi ở đâu trong quá trình xử lý
     */
	function save (
			$items,
			$customer_id,
			$employee_id,
			$sold_by_employee_id,
			$comment,
            $more_comment,
			$show_comment_on_receipt,
			$payments,
			$add_more_customer_to_service,	
			$itemContainsLine = [],
			$sale_id=false,
			$suspended = false,
			$change_sale_date=false,
			$balance=0,
			$store_account_payment = 0, 
			$extraData = array(), 
			$service_id, 
			$code = null, 
			$assigment = 0, 
			$return = 0, 
			$is_vat = 0, 
			$cust_info = NULL,
	       $sale_status_id
	    )
	{
		$thong_bao_loi = array();

		if ($this->config->item('test_mode')) {
			$this->load->library('sale_lib');
			$this->sale_lib->clear_all();
			return lang('sales_test_mode_transaction');
		}
		# Xem lại đơn hàng cũ, sửa đơn hàng
        if($sale_id) {
            $is_insert = false;
            $old_debit_payment          = $this->get_debt_payment_amount_from_sale(array('sale_id'=>$sale_id));

            $old_sale_info              = $this->sale_lib->get_sale($sale_id);
            $old_sale_order_total_value = $old_sale_info['gia_tri_don_hang'];
            $old_code_visa 				= $old_sale_info['info']['code'];
            $_SESSION['visa_code'] = $old_code_visa;
            $code = $old_code_visa;
            $old_payment_total          = $this->get_payment_total_from_sale(array('sale_id'=>$sale_id));
		# Thêm mới đơn hàng
        }else{
            $is_insert = true;
        }


		# Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();
		# Run these queries as a transaction, we want to make sure we do all or nothing


		$global_weighted_average_cost = FALSE;

		if ($this->config->item('always_use_average_cost_method'))
		{
			$global_weighted_average_cost=  $this->get_global_weighted_average_cost();
			$global_weighted_average_cost = to_currency_no_money($global_weighted_average_cost, 10);
		}

		if ($sale_id)
		{
			$before_save_sale_info = $this->get_info($sale_id)->row();
		}
		else
		{
			$before_save_sale_info = FALSE;
		}
		//we need to check the sale library for deleted taxes during sale
		$this->load->library('sale_lib');

		if(count($items)==0)
			return -1;

		$payment_types = '';
		foreach($payments as $payment_id=>$payment) {   
            if(!empty($payment['payment_amount']) && !empty($payment['payment_type']))
            $payment_types=$payment_types.$payment['payment_type'].': '.to_currency($payment['payment_amount']).'<br />';
		}

		$tier_id = $this->sale_lib->get_selected_tier_id();
		$deleted_taxes = $this->sale_lib->get_deleted_taxes();

		if (!$tier_id)
		{
			$tier_id = NULL;
		}
		$sales_data = array(
			'customer_id'=> $customer_id > 0 ? $customer_id : null,
			'employee_id'=>$employee_id,
			'sold_by_employee_id' => $sold_by_employee_id,
			'payment_type'=>$payment_types,
			'comment'=>$comment,
			'comment_term'=>$more_comment['comment_term'],
			'comment_guarantee'=>$more_comment['comment_guarantee'],
			'comment_payment'=>$more_comment['comment_payment'],
			'show_comment_on_receipt'=> $show_comment_on_receipt ?  $show_comment_on_receipt : 0,
			'suspended'=>$suspended,
			'deleted' => 0,
			'offline' => (defined('BIZ_OFFLINE') && BIZ_OFFLINE) ? 1 : 0,
			'deleted_by' => NULL,
			'cc_ref_no' => $before_save_sale_info ? $before_save_sale_info->cc_ref_no : '',//Legacy for old payments; set new payments to empty
			'auth_code' => $before_save_sale_info ? $before_save_sale_info->auth_code : '',//Legacy for old payments; set new payments to empty
			'location_id' => $this->Employee->get_logged_in_employee_current_location_id(),
			'register_id' => $this->Employee->get_logged_in_employee_current_register_id(),
			'store_account_payment' => $store_account_payment,
			'tier_id' => $tier_id ? $tier_id : NULL,
			'deleted_taxes' =>  $deleted_taxes? serialize($deleted_taxes) : NULL,
			'deliverer' => $extraData['deliverer'],
			'supporter' => !empty($extraData['supporter'])?$extraData['supporter']:1,
			'delivery_date' => isset($extraData['delivery_date']) ? date('Y-m-d H:i:s', strtotime($extraData['delivery_date'])) : date('Y-m-d H:i:s'),
		    'service_id' => $service_id,
		    'code' => $code,
			'commission_time_method' => $extraData['commission_time_method'],
			'commission_method' => $extraData['commission_method'],
			'min_profit' => $extraData['min_profit'],
			'assigment' => $assigment,
			'return' => $return,
			'is_vat' => $is_vat,
		    'sale_status_id' => $sale_status_id
		);
		// echo "<pre>"; print_r($sales_data); die();
		/*
		 * TODO
		 * suspended = 2|5
		 * Check approver_group active or not
		 * If not -> set status of sale -> approved else status is pending
		 * 
		 * */

		# Tại suspended = 1 đơn hàng tạm dừng
		if ($suspended == 1) //Layaway
		{
			$sales_data['was_layaway'] = 1;
		}
		elseif ($suspended == 2) //estimate
		{
			$sales_data['was_estimate'] = 1;
		}

		if($sale_id)
		{
			$old_data = $this->get_info($sale_id)->row_array();
			$sales_data['sale_time'] = $old_data['sale_time'];

		}
		

		if($change_sale_date)
		{
			$sale_time = strtotime($change_sale_date);
			if($sale_time !== FALSE)
			{
				$sales_data['sale_time']=date('Y-m-d H:i:s', strtotime($change_sale_date));
			}
		}

		if ($sale_id)
		{
			//If we are NOT a suspended sale and wasn't a layaway
			if (!$this->sale_lib->get_suspended_sale_id() && !$old_data['was_layaway'])
			{
				$override_payment_time = $sales_data['sale_time'];
			}
		}
		elseif($change_sale_date)
		{
			if (!$this->sale_lib->get_suspended_sale_id())
			{
				$override_payment_time = $sales_data['sale_time'];
			}
		}

		$store_account_payment_amount = 0;

		if ($store_account_payment)
		{
			$store_account_payment_amount = $this->sale_lib->get_total();
		}

		 $previous_store_account_amount = 0;

		 if ($sale_id !== FALSE)
		 {
			 $previous_store_account_amount = $this->get_store_account_payment_total($sale_id);
		 }



        #------------------------------------------------------------------------------------------------------#
        
                                        # XỬ LÝ TIẾN TRÌNH CỘNG TRỪ KHO
                                        
        #------------------------------------------------------------------------------------------------------#
		if ($sale_id)
		{

			# kiểm tra nếu biến is_stock_out tồn tại thì update số lượng
		

			$thay_doi_so_luong_kho = false;

			$ban_hang_don_hang_tam_dung = false;
			# $is_stock_out = 0 thao tác bán hàng k có xuất kho
			# $is_stock_out = 1 Đơn hàng đã hoàn thành tiến trình xuất kho
			# $is_stock_out = 2 Đơn hàng đang trong tiến trình xuất kho
			if($suspended){
				if($before_save_sale_info->is_stock_out == 0)
				{
					$thay_doi_so_luong_kho = true;
	                $sales_data['suspended'] = $suspended;

				} elseif($before_save_sale_info->is_stock_out == 2) {

	                $sales_data['suspended'] = $before_save_sale_info->suspended;

	            } else $sales_data['suspended'] = $suspended;
			} else {
				$ban_hang_don_hang_tam_dung = true;
				$sales_data['suspended'] = 0;
			}
			


        #------------------------------------------------------------------------------------------------------#
								
								# THỰC HIỆN THAY ĐỔI SỐ LƯỢNG TỒN KHO
                                
        #------------------------------------------------------------------------------------------------------#
			if($is_insert){
				# Nếu là thêm mới
            } else { 
            	unset($sales_data['employee_id']);
				# Xóa tất cả dữ liệu cũ, thêm mới dữ liệu
				$this->db->delete('sales_payments', array('sale_id' => $sale_id));
				$this->db->delete('sales_items_taxes', array('sale_id' => $sale_id));
				$this->db->delete('sales_items', array('sale_id' => $sale_id));
				$this->db->delete('sales_item_kits_taxes', array('sale_id' => $sale_id));
				$this->db->delete('sales_item_kits', array('sale_id' => $sale_id));

            }

		#------------------------------------------------------------------------------------------------------#
								# thực hiện thay đổi số lượng tồn kho
		#------------------------------------------------------------------------------------------------------#

			$this->db->where('sale_id', $sale_id);
			$this->db->update('sales', $sales_data);
		}
        # Nếu không có sale_id thì thêm mới sale data
        # Thêm mới đơn hàng hoặc bấm nút hoàn thành
		else
		{
			// echo "<pre>";
			// var_dump($sales_data);die();
			if(!$suspended) $suspended = 0;
            $sales_data['suspended'] = $suspended;
            $sales_data['is_stock_out'] = 0;
			$is_stock_out = 0;
			$sales_data['sale_time'] = date("Y-m-d H:i:s",strtotime($sales_data['sale_time']));
			$this->db->insert('sales',$sales_data);
			// echo $this->db->last_query();die();
			$sale_id = $this->db->insert_id();

            if($sale_id == 0) $thong_bao_loi[] = "Có lỗi trong quá trình lưu đơn hàng";

        }



		//Loyalty systems

		if ($suspended != 2 && $customer_id > 0 && $this->config->item('enable_customer_loyalty_system'))
		 {
		   $sales_data_loy = array();
		   $customer_info = $this->Customer->get_info($customer_id);

			if ($this->config->item('loyalty_option') == 'simple')
			{
				if (!$store_account_payment)
				{
					if ($this->sale_lib->get_redeem())
					{
						$this->db->where('person_id', $customer_id);
						$this->db->set('current_sales_for_discount','current_sales_for_discount -'.$this->config->item('number_of_sales_for_discount'),false);
						$this->db->update('customers');
						$sales_data_loy['did_redeem_discount'] = 1;
					}
					else
					{
						$this->db->where('person_id', $customer_id);
						$this->db->set('current_sales_for_discount','current_sales_for_discount +1',false);
						$this->db->update('customers');
					}
				}
			}
			else
			{
				$current_points = $customer_info->points;
				$current_spend_for_points = $customer_info->current_spend_for_points;

				//This is duplicated below; but this is ok so we don't break anything else
				$giftcard_payments_amount = 0;
				foreach($payments as $payment_id=>$payment)
				{
					if ( !empty($payment['payment_amount']) && !empty($payment['payment_amount']) && substr( $payment['payment_type'], 0, strlen( lang('common_giftcard') ) ) == lang('common_giftcard') )
					{
						$giftcard_payments_amount+=$payment['payment_amount'];
					}
				}

				//Don't count points or gift cards
				$total_spend_for_sale = $this->sale_lib->get_total() - $this->sale_lib->get_payment_amount(lang('common_points')) - $giftcard_payments_amount;

	         	list($spend_amount_for_points, $points_to_earn) = explode(":",$this->config->item('spend_to_point_ratio'),2);

				if (!$store_account_payment)
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

					$sales_data_loy['points_gained'] = (int)($new_point_value -  $current_points);
				}
				else //Don't change any values for store account payment
				{
					$new_current_spend_for_points = $current_spend_for_points;
					$new_point_value = $current_points;
				}

				//Redeem points
				if ($payment_amount_points = $this->sale_lib->get_payment_amount(lang('common_points')))
				{
					$points_used = to_currency_no_money($payment_amount_points / $this->config->item('point_value'));
					$new_point_value -= $points_used;
					$sales_data_loy['points_used'] = (int)$points_used;

				}
				else
				{
					$sales_data_loy['points_used'] = 0;
				}

				$new_point_value = (int) round(to_currency_no_money($new_point_value));
				$new_current_spend_for_points = to_currency_no_money($new_current_spend_for_points);


                $this->db->where('person_id', $customer_id);
                $this->db->update('customers', array('points' => $new_point_value, 'current_spend_for_points' => $new_current_spend_for_points));

			 }

			if(!empty($sales_data_loy))
			{
				$this->db->where('sale_id', $sale_id);
				$this->db->update('sales', $sales_data_loy);
			}
		 }

        $sale_total    = $this->sale_lib->get_total();
        $payment_total = $this->sale_lib->get_payments_totals();
        
		if(isset($_SESSION['sale_time_date'])) {
		            $sale_time_date = $_SESSION['sale_time_date'];
		            $sale_time_date = date('Y-m-d H:i:s', strtotime($sale_time_date));
        }

		# Nếu khác báo giá suspended = 1, suspended = 0
		# Phần ghi sổ nợ
		if ($suspended != 2)
		{
			// update payments and history
            if($this->sale_lib->get_mode() == 'sale') {
                $debit_payment   = $this->sale_lib->get_debit_payment();
                if($is_insert == false) {
                   
                    if($old_debit_payment != $debit_payment) {
                       
                        $new_balance = $cust_info->balance - $old_debit_payment + $debit_payment;

                        $this->db->where('person_id', $cust_info->person_id);
                        $this->db->update('customers',array('balance'=>$new_balance));                               
                        $store_account_transaction = array(
                            'customer_id'=>$cust_info->person_id,  
                            'sale_id'=>$sale_id,
                            'comment'=>'Sửa đơn hàng', 
                            'transaction_amount'=>$debit_payment - $old_debit_payment,
                            'balance'=>$new_balance, 
                            'balance_2'=>$cust_info->balance_2,
                            'options' => 1, 
                            'date' => $sale_time_date
                        );
                        $this->db->insert('store_accounts',$store_account_transaction);
                    }
                    $subtraction_1 = $payment_total - $sale_total;
                    $subtraction_2 = $old_payment_total - $old_sale_order_total_value;                                                                                                     
                    if($subtraction_1 != $subtraction_2) {    
                        if($payment_total > $sale_total) {                           
                            if($old_payment_total > $old_sale_order_total_value) {
                                $transaction_amount = - ($old_payment_total - $old_sale_order_total_value) + ($payment_total - $sale_total);
                                $new_balance_2 = $cust_info->balance_2 + $transaction_amount;
                                $comment_account_transaction = 'Sửa đơn hàng - Khách hàng thanh toán dư tiền';
                            }else {
                                $transaction_amount = $payment_total - $sale_total;
                                $new_balance_2 = $cust_info->balance_2 + $transaction_amount;
                                $comment_account_transaction = 'Sửa đơn hàng';
                            }

                            $this->db->where('person_id', $cust_info->person_id);
                            $this->db->update('customers',array('balance_2'=>$new_balance_2));

                            $store_account_transaction = array(
                                'customer_id'=>$cust_info->person_id,  
                                'sale_id'=>$sale_id,
                                'comment'=>$comment_account_transaction, 
                                'transaction_amount'=>$transaction_amount,
                                'balance'=>$cust_info->balance, 
                                'balance_2'=>$new_balance_2,
                                'options' => 2, 
                                'date' => $sale_time_date
                            );

                            $this->db->insert('store_accounts',$store_account_transaction);
                        }else {
                            if($old_payment_total > $old_sale_order_total_value) {
                                $transaction_amount = $old_sale_order_total_value - $old_payment_total;
                                $new_balance_2 = $cust_info->balance_2 + $transaction_amount;
                                $comment_account_transaction = 'Sửa đơn hàng';                                                                                               
                                $this->db->where('person_id', $cust_info->person_id);
                                $this->db->update('customers',array('balance_2'=>$new_balance_2));                                                                       
                                $store_account_transaction = array(
                                    'customer_id'=>$cust_info->person_id,  
                                    'sale_id'=>$sale_id,
                                    'comment'=>$comment_account_transaction, 
                                    'transaction_amount'=>$transaction_amount,
                                    'balance'=>$cust_info->balance, 
                                    'balance_2'=>$new_balance_2,
                                    'options' => 2, 
                                    'date' => $sale_time_date
                                );

                                $this->db->insert('store_accounts',$store_account_transaction);                                                                                                   
                            }
                        }
                    }

                } else {
					# Nếu thêm mới đơn hàng mà có nợ
                    if($debit_payment > 0) {
                        $new_balance = $cust_info->balance + $debit_payment;
                        $this->db->where('person_id', $cust_info->person_id);
                        $this->db->update('customers',array('balance'=>$new_balance));                                                            
                        $store_account_transaction = array(
                            'customer_id'=>$cust_info->person_id,  
                            'sale_id'=>$sale_id,
                            'comment'=>'', 
                            'transaction_amount'=>$debit_payment,
                            'balance'=>$new_balance, 
                            'balance_2'=>$cust_info->balance_2,
                            'options' => 1, 
                            'date' => $sale_time_date
                        );

                        $this->db->insert('store_accounts',$store_account_transaction);
                    }
                 
					$change_balance2 = false;
					foreach ($payments as $payment_id => $payment) {
						if($payment['payment_type'] ==  lang('common_debt_customer'))
						{
							$change_balance2 = $payment['payment_amount'];
							break;
						}
					}
					if ($change_balance2) { 
							$new_balance_2 = $cust_info->balance_2 + $change_balance2;
							$this->db->where('person_id', $cust_info->person_id);
							$this->db->update('customers',array('balance_2'=>$new_balance_2));

							$store_account_transaction = array(
									'customer_id'=>$cust_info->person_id,  
									'sale_id'=>$sale_id,
									'comment'=>'Khách thanh toán dư tiền', 
									'transaction_amount'=>$change_balance2,
									'balance'=>$cust_info->balance, 
									'balance_2'=>$new_balance_2,
									'options' => 2, 
									'date' => $sale_time_date
							);

							$this->db->insert('store_accounts',$store_account_transaction);
						}
                       
                    
                }
            }
            elseif($this->sale_lib->get_mode() == 'return') {
                $debit_payment   = $this->sale_lib->get_debit_payment();
                if($is_insert == false) {
                    if($old_debit_payment != $debit_payment) {
                        $new_balance_2 = $cust_info->balance_2 - $old_debit_payment + $debit_payment;

                        $this->db->where('person_id', $cust_info->person_id);
                        $this->db->update('customers',array('balance_2'=>$new_balance_2));

                        $store_account_transaction = array(
                            'customer_id'=>$cust_info->person_id,  
                            'sale_id'=>$sale_id,
                            'comment'=>'Sửa đơn hàng', 
                            'transaction_amount'=>$debit_payment - $old_debit_payment,
                            'balance'=>$cust_info->balance, 
                            'balance_2'=>$new_balance_2,
                            'options' => 2, 
                            'date' => $sale_time_date
                        );

                        $this->db->insert('store_accounts',$store_account_transaction);
                    }

                }else {
                    if($debit_payment > 0) {
                        $new_balance_2 = $cust_info->balance_2 + $debit_payment;
                        $this->db->where('person_id', $cust_info->person_id);
                        $this->db->update('customers',array('balance_2'=>$new_balance_2));

                        $store_account_transaction = array(
                            'customer_id'=>$cust_info->person_id,  
                            'sale_id'=>$sale_id,
                            'comment'=>'', 
                            'transaction_amount'=>$debit_payment,
                            'balance'=>$cust_info->balance, 
                            'balance_2'=>$new_balance_2,
                            'options' => 2, 
                            'date' => $sale_time_date
                        );

                        $this->db->insert('store_accounts',$store_account_transaction);
                    }
                }

            }
            elseif($this->sale_lib->get_mode() == 'vat_order') {
                $new_balance_2 = $cust_info->balance_2 - $sale_total;

                $this->db->where('person_id', $cust_info->person_id);
                $this->db->update('customers',array('balance_2'=>$new_balance_2));

                $store_account_transaction = array(
                    'customer_id'=>$cust_info->person_id,  
                    'sale_id'=>$sale_id,
                    'comment'=>$comment, 
                    'transaction_amount'=>-$sale_total,
                    'balance'=>$cust_info->balance, 
                    'balance_2'=>$new_balance_2,
                    'options' => 2, 
                    'date' => $sale_time_date
                );

                $this->db->insert('store_accounts',$store_account_transaction);

                $sale_vat_relationship = $this->sale_lib->get_sale_vat_relationship();
                if(!empty($sale_vat_relationship)) {
                    $this->db->where('sale_vat_id', $sale_id);
                    $this->db->delete('sale_vat_relationships');

                    foreach($sale_vat_relationship as $val) {
                        $vat_relationships_data[] = array(
                            'sale_vat_id' => $sale_id,
                            'sale_id'      => $val
                        );
                    }
                    $this->db->insert_batch('sale_vat_relationships', $vat_relationships_data);
                }
            }

			   //insert store account payment transaction
			if($customer_id > 0 && $store_account_payment) {
                // update balance
                if($store_account_payment == 1) {
                    $new_balance = $cust_info->balance - $store_account_payment_amount;

                    $this->db->where('person_id', $cust_info->person_id);
                    $this->db->update('customers',array('balance'=>$new_balance));

                }else {
                    $new_balance_2 = $cust_info->balance_2 - $store_account_payment_amount;

                    $this->db->where('person_id', $cust_info->person_id);
                    $this->db->update('customers',array('balance_2'=>$new_balance_2));
                }

                if(empty($comment))
                    $transaction_comment = $extraData['comment'];
                else
                    $transaction_comment = $comment;

			 	$store_account_transaction = array(
			        'customer_id'=>$customer_id,
			        'sale_id'=>$sale_id,
					'comment'=>$transaction_comment,
			       	'transaction_amount'=> -$store_account_payment_amount,
					'balance'=>$this->Customer->get_info($customer_id)->balance,
                    'balance_2'=>$this->Customer->get_info($customer_id)->balance_2,
                    'options' => $store_account_payment,
					'date' => $sale_time_date
				);


				$this->db->insert('store_accounts',$store_account_transaction);

                $sale_store_payment = $this->sale_lib->get_sale_store_payment();
                if(!empty($sale_store_payment)) {
                    $sno_id = $this->db->insert_id();
                    $sales_store_account_data = array();
                    foreach($sale_store_payment as $key => $amount_value) {
                        $tmp = array();
                        $tmp['sno_id']  = $sno_id;
                        $tmp['sale_id'] = $key;
                        $tmp['amount']  = $amount_value;

                        $sales_store_account_data[] = $tmp;
                    }

                    $this->db->insert_batch('sales_store_account',$sales_store_account_data);
                }
			 }
		 }
		# Phần ghi sổ nợ

		$total_giftcard_payments = 0;

		foreach($payments as $payment_id=>$payment)
		{
			//Only update giftcard payments if we are NOT an estimate (suspended = 2)
			if ($suspended != 2)
			{
				if (!empty($payment['payment_amount']) && !empty($payment['payment_amount']) && substr( $payment['payment_type'], 0, strlen( lang('common_giftcard') ) ) == lang('common_giftcard') )
				{
					/* We have a gift card and we have to deduct the used value from the total value of the card. */
					$splitpayment = explode( ':', $payment['payment_type'] );
					$cur_giftcard_value = $this->Giftcard->get_giftcard_value( $splitpayment[1] );
                    if ($this->sale_lib->get_mode() == 'return') {
                        $new_value = $cur_giftcard_value + $payment['payment_amount'];
                    } else {
                        $new_value = $cur_giftcard_value + $payment['payment_amount'];
                    }
					$this->Giftcard->update_giftcard_value( $splitpayment[1], $new_value);
					$total_giftcard_payments+=$payment['payment_amount'];
					$this->Giftcard->log_modification(array('sale_id' => $sale_id, "number" => $splitpayment[1], "person" => lang('common_customer'), "old_value" => $cur_giftcard_value, "new_value" => $new_value, "type" => 'sale_return'));

				}
			}

            if(isset($_SESSION['sale_time_date'])) {
                $payment['payment_date'] = $_SESSION['sale_time_date'];
                $payment['payment_date'] = date('Y-m-d h:i', strtotime($payment['payment_date']));
            }

        #----------------------------------------------------------------------------------------------------
            						# Lưu data cho hóa đơn tại đây
        #----------------------------------------------------------------------------------------------------

  
            # kiểm tra nếu có dữ liệu là nợ
            foreach ($payments as $value) {
                # nếu có biến nợ đầu và nợ cuối
                if(isset($data['luu_no_dau']) && isset($data['luu_no_cuoi'])){
                     # kiểm tra nếu có dữ liệu là nợ
                    $data['no_dau'] = $data['luu_no_dau'];

                    $data['no_cuoi'] = $data['luu_no_cuoi'];

                    if(!empty($value['payment_amount']) && !empty($value['payment_amount']) && $value['payment_type'] == lang('common_store_account')){
                            $data['no_cuoi'] = $data['no_dau'] + $value['payment_amount'];
                            $data['luu_no_cuoi'] = $data['no_cuoi']; # lưu lại biến tạm

                    } else {
                        $data['no_cuoi'] = $data['no_cuoi'];
                        $data['luu_no_cuoi'] = $data['no_cuoi'];
                    }

                # nếu chưa có biến nợ đầu và nợ cuối
                #---------------------------------------------------------------------------------------#
                                        # Lưu lại biến tạm
                #---------------------------------------------------------------------------------------#
                } else {
                     # kiểm tra nếu có dữ liệu là nợ
                    if(isset($value['payment_type']) && $value['payment_type'] == 'Sổ ghi nợ'){
                        if ($cust_info->balance !=0) {
                        $data['no_dau']  = abs($cust_info->balance);
                        $data['luu_no_dau'] = $data['no_dau'];
                        $data['no_cuoi'] = $data['no_dau'] + $value['payment_amount'];
                        $data['luu_no_cuoi'] = $data['no_cuoi'];
                        } else {
                            $data['no_dau']  = 0;
                            $data['luu_no_dau'] = $data['no_dau'];  # lưu lại biến tạm
                            $data['no_cuoi'] = $value['payment_amount'];
                            $data['luu_no_cuoi'] = $data['no_cuoi']; # lưu lại biến tạm
                        }

                    } else {
                        $data['no_dau']  = abs($cust_info->balance);
                        $data['luu_no_dau'] = $data['no_dau'];
                        $data['no_cuoi'] = $data['no_dau'] - !empty($value['payment_amount'])?$value['payment_amount']:0;
                        $data['luu_no_cuoi'] = $data['no_cuoi'];
                    }
                }
               
            }

            # Truyền dữ liệu Nợ cũ , nợ đầu

            $data['no_dau'] = $data['luu_no_dau'];
            $data['no_cuoi'] = $data['luu_no_cuoi'];

            $cost_price = 0;
			$sales_payments_data = array
			(
				'sale_id'=>$sale_id,
				'payment_type'=>!empty($payment['payment_type'])?$payment['payment_type']:'',
				'payment_amount'=>!empty($payment['payment_amount'])?$payment['payment_amount']:0,
				'no_dau'=>$data['no_dau'],
				'no_cuoi'=>$data['no_cuoi'],
				'payment_date' => isset($override_payment_time) ? $override_payment_time: $payment['payment_date'],
				'truncated_card' => !empty($payment['truncated_card'])?$payment['truncated_card']:'',
				'card_issuer' => !empty($payment['card_issuer'])?$payment['card_issuer']:'',
				'auth_code' => !empty($payment['auth_code'])?$payment['auth_code']:'',
				'ref_no' => !empty($payment['ref_no'])?$payment['ref_no']:'',
				'cc_token' => !empty($payment['cc_token'])?$payment['cc_token']:'',
				'acq_ref_data' => !empty($payment['acq_ref_data'])?$payment['acq_ref_data']:'',
				'process_data' => !empty($payment['process_data'])?$payment['process_data']:'',
				'entry_method' => !empty($payment['entry_method'])?$payment['entry_method']:'',
				'aid' => !empty($payment['aid'])?$payment['aid']:'',
				'tvr' => !empty($payment['tvr'])?$payment['tvr']:'',
				'iad' => !empty($payment['iad'])?$payment['iad']:'',
				'tsi' => !empty($payment['tsi'])?$payment['tsi']:'',
				'arc' => !empty($payment['arc'])?$payment['arc']:'',
				'cvm' => !empty($payment['cvm'])?$payment['cvm']:'',
				'tran_type' => !empty($payment['tran_type'])?$payment['tran_type']:'',
				'application_label' => !empty($payment['application_label'])?$payment['application_label']:'',
			);

			$this->db->insert('sales_payments',$sales_payments_data);
            
		}


		$has_added_giftcard_value_to_cost_price = $total_giftcard_payments > 0 ? false : true;
		$store_account_item_id = $this->Item->get_store_account_item_id();

        $config_adjusted_cost_price = $this->config->item('config_adjusted_cost_price');
        if(!empty($config_adjusted_cost_price))
            $config_adjusted_cost_price = unserialize($config_adjusted_cost_price);
        else
            $config_adjusted_cost_price = array();

		$items = thay_doi_key_theo_line($items);


		foreach($items as $line=>$item)
		{
			if (isset($item['item_id']))
			{
                if($return == 1) $item['quantity'] = $item['quantity'] * (-1);

					$cur_item_info = $this->Item->get_info($item['item_id']);
					$cur_item_location_info = $this->Item_location->get_info($item['item_id']);
					$qtyOriginal = $item['quantity'];

				if( (int) $item['measure_id'] && (int) $cur_item_info->measure_id && $cur_item_info->measure_id != $item['measure_id'] /* && ($mode == 'receive' || $mode == 'purchase_order') */)
				{
					$convertedValue = $this->ItemMeasures->getConvertedValue($item['item_id'], $item['measure_id']);
					$cost_price = $cost_price * $convertedValue->unit_price_percentage_converted / 100;
					$totalQty = $item['quantity'] = $item['quantity'] * (int)$convertedValue->qty_converted;
					$item['price'] = $item['price'] / (int)$convertedValue->qty_converted;;
				}

				//Redeem profit when giftcard is used; so we set cost price to item price
                if ($item['name']==lang('common_giftcard') && !$this->Giftcard->get_giftcard_id($item['description']) && $this->config->item('calculate_profit_for_giftcard_when') == 'redeeming_giftcard')
				{
					$cost_price = $item['price'];
				}
				elseif ($item['item_id'] != $store_account_item_id)
				{
					$cost_price = $item['cost_price'];
				}
				else // Set cost price = price so we have no profit
				{
					$cost_price = $item['price'];
				}


				if ($this->config->item('calculate_profit_for_giftcard_when') == 'selling_giftcard')
				{
					//Add to the cost price if we are using a giftcard as we have already recorded profit for sale of giftcard
					if (!$has_added_giftcard_value_to_cost_price)
					{
						$cost_price+= $total_giftcard_payments / $item['quantity'];
						$has_added_giftcard_value_to_cost_price = true;
					}
				}
				$reorder_level = ($cur_item_location_info && $cur_item_location_info->reorder_level) ? $cur_item_location_info->reorder_level : $cur_item_info->reorder_level;

				if ($cur_item_info->tax_included)
				{
					$this->load->helper('items');
					$item['price'] = get_price_for_item_excluding_taxes($item['item_id'], $item['price']);
				}

				$this->load->helper('items');

				$sales_items_data = array
				(
					'sale_id'=>$sale_id,
					'item_id'=>$item['item_id'],
                    'item_name' =>$item['name'],
					'line'=>$item['line'],
					'description'=>$item['description'],
					'serialnumber'=>$item['serialnumber'],
					'quantity_purchased'=>$item['quantity'], // qty is converted to base measure
					'measure_id' => $item['measure_id'],
					'measure_qty' => $qtyOriginal, // qty by measure
					'discount_percent'=>$item['discount'],
					'item_cost_price' =>  $global_weighted_average_cost === FALSE ? to_currency_no_money($cost_price,10) : $global_weighted_average_cost,
					'item_unit_price'=>$item['price'],
					'calculatedPrice'=>str_replace(',', '', $item['calculatedPrice']),
					'commission' => get_commission_for_item($item['item_id'],$item['price'],to_currency_no_money($cost_price,10), $item['quantity'], $item['discount']),
				);

                if($assigment == 1 || in_array($item['item_id'], $config_adjusted_cost_price)) {
                    $sales_items_data['item_cost_price'] = $sales_items_data['item_unit_price'];
                }

       
				$this->db->insert('sales_items',$sales_items_data);



				//Only update giftcard payments if we are NOT an estimate (suspended = 2)
				if ($suspended != 2)
				{
					//create giftcard from sales
					if($item['name']==lang('common_giftcard') && !$this->Giftcard->get_giftcard_id($item['description']))
					{
						$giftcard_data = array(
							'giftcard_number'=>$item['description'],
							'value'=>$item['price'],
							'description' => $comment,
							'customer_id'=>$customer_id > 0 ? $customer_id : null,
						);

						$this->Giftcard->save($giftcard_data);

						$employee_info = $this->Employee->get_logged_in_employee_info();
						$this->Giftcard->log_modification(array('sale_id' => $sale_id, "number" => $item['description'], "person"=>$employee_info->first_name . " " . $employee_info->last_name, "new_value" => $item['price'], 'old_value' => 0, "type" => 'create'));
					}
				}
				//Update stock quantity IF not a service
				$is_stock_out = $before_save_sale_info->is_stock_out;
				# Nếu is_stock <> 0 nghĩa là k có thao tác xuất kho

				//Only do stock check + inventory update if we are NOT an estimate
				if ($suspended == 1 || $is_stock_out != 0)
				{

					$stock_recorder_check = false;
					$out_of_stock_check   = false;
					$email=false;
					$message = '';

					//checks if the quantity is greater than reorder level
					if(!$cur_item_info->is_service && $cur_item_location_info->quantity > $reorder_level)
					{
						$stock_recorder_check=true;
					}

					//checks if the quantity is greater than 0
					if(!$cur_item_info->is_service && $cur_item_location_info->quantity > 0)
					{
						$out_of_stock_check=true;
					}

					//Update stock quantity IF not a service
					$is_stock_out = $before_save_sale_info->is_stock_out;
                    # Nếu is_stock <> 0 nghĩa là k có thao tác xuất kho


					if (!$cur_item_info->is_service && $is_stock_out != 0)
					{
                   
						$cur_item_location_info->quantity = $cur_item_location_info->quantity !== NULL ? $cur_item_location_info->quantity : 0;
						$qty_buy = -$item['quantity'];
					  	$old_quantity_purchased = isset($old_sale_info['cart'][$line]['quantity_purchased'])?$old_sale_info['cart'][$line]['quantity_purchased']:0;
						$sale_remarks =$this->config->item('sale_prefix').' '.$sale_id;
						$this->Item_location->update_chuyen_kho_noi_bo($cur_item_location_info->quantity + $old_quantity_purchased - $item['quantity'], $item['item_id']);

						$inv_data = array
						(
							'trans_date'=>date('Y-m-d H:i:s'),
							'trans_items'=>$item['item_id'],
							'trans_user'=>$employee_id,
							'trans_comment'=>'Bán hàng sau khi hoàn thành xuất kho'.$sale_remarks,
							'trans_inventory'=>0,
							'location_id' => $this->Employee->get_logged_in_employee_current_location_id()
						);

						$this->Inventory->insert($inv_data);

					}

                    # nếu k có thao tác xuất kho
                    else {

                    	if($sales_data['suspended'] == 0){
							$old_quantity_purchased = isset($old_sale_info['cart'][$line]['quantity_purchased'])?$old_sale_info['cart'][$line]['quantity_purchased']:0;
							$so_luong_moi = $item['quantity'];

							$this->Item_location->update_chuyen_kho_noi_bo(($cur_item_location_info->quantity - $so_luong_moi), $item['item_id']);
							$qty_buy = -$item['quantity'];
							$sale_remarks =$this->config->item('sale_prefix').' '.$sale_id;
							$inv_data = array
							(
								'trans_date'=>date('Y-m-d H:i:s'),
								'trans_items'=>$item['item_id'],
								'trans_user'=>$employee_id,
								'trans_comment'=>'Bán hàng đơn hàng tạm dừng '.$sale_remarks,
								'trans_inventory'=>$qty_buy,
								'location_id' => $this->Employee->get_logged_in_employee_current_location_id()
							);

							$this->Inventory->insert($inv_data);
						}

                    }

					//Re-init $cur_item_location_info after updating quantity
					$cur_item_location_info = $this->Item_location->get_info($item['item_id']);

					//checks if the quantity is out of stock
					if($out_of_stock_check && $cur_item_location_info->quantity <= 0)
					{
						$message= $cur_item_info->name.' '.lang('sales_is_out_stock').' '.to_quantity($cur_item_location_info->quantity);
						$email=true;

					}
					//checks if the quantity hits reorder level
					else if($stock_recorder_check && ($cur_item_location_info->quantity <= $reorder_level))
					{
						$message= $cur_item_info->name.' '.lang('sales_hits_reorder_level').' '.to_quantity($cur_item_location_info->quantity);
						$email=true;
					}

					//send email
					if($this->Location->get_info_for_key('receive_stock_alert') && $email)
					{
						$this->load->library('email');
						$config = array();
						$config['mailtype'] = 'text';
						$this->email->initialize($config);
						$this->email->from($this->Location->get_info_for_key('email') ? $this->Location->get_info_for_key('email') : 'no-reply@mg.4biz.vn', $this->config->item('company'));
						$this->email->to($this->Location->get_info_for_key('stock_alert_email') ? $this->Location->get_info_for_key('stock_alert_email') : $this->Location->get_info_for_key('email'));

						$this->email->subject(lang('sales_stock_alert_item_name').$this->Item->get_info($item['item_id'])->name);
						$this->email->message($message);
						$this->email->send();
					}

				}
				# Trái lại nếu thêm mới khách hàng, trừ kho
				# Trái lại nếu thêm mới khách hàng, trừ kho
				# Trái lại nếu thêm mới khách hàng, trừ kho
				# Trái lại nếu thêm mới khách hàng, trừ kho, hoặc sửa khách hàng
				# $suspended = 0
				else {
//					var_dump($cur_item_location_info->quantity);die;
					if (!$cur_item_info->is_service)
					{
						$sale_remarks =$this->config->item('sale_prefix').' '.$sale_id;
						$so_luong_cu = isset($old_sale_info['cart'][$line]['quantity_purchased'])?$old_sale_info['cart'][$line]['quantity_purchased']:0;
						$so_luong_cu = round($so_luong_cu,2);
						$so_luong_moi = $item['quantity'];

						# Nếu trả lại hàng
						if($return){
							if($so_luong_moi<0){
								/** @var là số lượng được lưu vào kho $so_luong_thay_doi */
								$so_luong_thay_doi = $cur_item_location_info->quantity - $so_luong_moi;
								/** @var là số lượng được lưu vào báo cáo tồn kho $so_luong_xuat_kho */
								$so_luong_xuat_kho = $so_luong_moi;
							} else {
								/** @var là số lượng được lưu vào kho $so_luong_thay_doi */
								$so_luong_thay_doi = $cur_item_location_info->quantity + $so_luong_moi;
								/** @var là số lượng được lưu vào báo cáo tồn kho $so_luong_xuat_kho */
								$so_luong_xuat_kho = -$so_luong_moi;
							}
							$comment = 'Trả lại hàng bán hàng - nhập kho';

						}
						# Nếu thêm mới đơn hàng
						# abc
						elseif($is_insert) {                           
                            if ( $suspended == 2 || $suspended == 5) {
                                $so_luong_thay_doi = $cur_item_location_info->quantity;
                                $so_luong_xuat_kho = 0;
                            } else {
                                $so_luong_thay_doi = $cur_item_location_info->quantity - $so_luong_moi;
                                $so_luong_xuat_kho = ($so_luong_moi-$so_luong_cu);
                                $comment = 'Bán hàng trực tiếp cho khách hàng'.'_'.$sale_remarks;
                            }
							
						# Nếu bán hàng đơn hàng tạm dừng
						}elseif ($ban_hang_don_hang_tam_dung && $before_save_sale_info->suspended == 1){
							$so_luong_thay_doi = $cur_item_location_info->quantity - $so_luong_moi;
                            $so_luong_xuat_kho = $so_luong_moi;
							$comment = 'Bán hàng đơn hàng tạm dừng'.' '.$sale_remarks;
						}
						else {
                            $so_luong_xuat_kho = ($so_luong_moi - $so_luong_cu);
							# Sản phẩm bị thay đổi về vị trí, hoặc bị xóa
							# bắt đầu xử lý từ line đầu tiên
							if(!empty($_SESSION['items_bi_xoa'])) {

								foreach ($_SESSION['items_bi_xoa'] as $xoa_item){
									$cur_item_location_info_1 = $this->Item_location->get_info($xoa_item['item_id']);

									$so_luong_bi_xoa = $xoa_item['quantity'];
									$this->Item_location->update_chuyen_kho_noi_bo(($cur_item_location_info_1->quantity +$so_luong_bi_xoa), $xoa_item['item_id']);

									$comment = 'Xóa sản phẩm có id '.$xoa_item['item_id'].' số lượng bằng '.$so_luong_bi_xoa.' đơn hàng'.' '.$sale_remarks;
						$inv_data = array
						(
										'trans_date'=>date('Y-m-d H:i:s'),
										'trans_items'=>$xoa_item['item_id'],
										'trans_user'=>$employee_id,
										'trans_comment'=>$comment,
										'trans_inventory'=>$so_luong_bi_xoa,
										'location_id' => $this->Employee->get_logged_in_employee_current_location_id()
									);
									$this->Inventory->insert($inv_data);

								}

								unset($_SESSION['items_bi_xoa']);
							}
							
							# Có sự thay đổi trong đơn hàng
							if($so_luong_moi - $so_luong_cu != 0){
								if($so_luong_cu == 0){
									$comment = 'Thêm mới sản phẩm có id '.$item['item_id'].' số lượng '.$so_luong_moi.' đơn hàng'.' '.$sale_remarks;
								} else {
									$comment = 'Thay đổi số lượng sản phẩm có id '.$item['item_id'].' số lượng từ '.$so_luong_cu.' thành '.$so_luong_moi.' đơn hàng'.' '.$sale_remarks;
								}

							} else{

								$comment = 'Sửa đổi đơn hàng'.' '.$sale_remarks;
							}

							$so_luong_thay_doi = $cur_item_location_info->quantity - $so_luong_moi + $so_luong_cu;
						}


						$this->Item_location->update_chuyen_kho_noi_bo($so_luong_thay_doi, $item['item_id']);

						$inv_data = array
						(
							'trans_date'=>date('Y-m-d H:i:s'),
							'trans_items'=>$item['item_id'],
							'trans_user'=>$employee_id,
							'trans_comment'=>$comment,
							'trans_inventory'=>-$so_luong_xuat_kho,
							'location_id' => $this->Employee->get_logged_in_employee_current_location_id()
						);
						$this->Inventory->insert($inv_data);


					}

				}
			}
			else
			{
				$cur_item_kit_info = $this->Item_kit->get_info($item['item_kit_id']);
				$cur_item_kit_location_info = $this->Item_kit_location->get_info($item['item_kit_id']);

				$cost_price = $item['cost_price'];

				if ($this->config->item('calculate_profit_for_giftcard_when') == 'selling_giftcard')
				{
					//Add to the cost price if we are using a giftcard as we have already recorded profit for sale of giftcard
					if (!$has_added_giftcard_value_to_cost_price)
					{
						$cost_price+= $total_giftcard_payments / $item['quantity'];
						$has_added_giftcard_value_to_cost_price = true;
					}
				}

				if ($cur_item_kit_info->tax_included)
				{
					$this->load->helper('item_kits');
					$item['price'] = get_price_for_item_kit_excluding_taxes($item['item_kit_id'], $item['price']);
				}

				$this->load->helper('item_kits');
				$sales_item_kits_data = array
				(
					'sale_id'=>$sale_id,
					'item_kit_id'=>$item['item_kit_id'],
					'line'=>$item['line'],
					'description'=>$item['description'],
					'quantity_purchased'=>$item['quantity'],
					'discount_percent'=>$item['discount'],
					'item_kit_cost_price' => $global_weighted_average_cost === FALSE ? ($cost_price === NULL ? 0.00 : to_currency_no_money($cost_price,10)) : $global_weighted_average_cost,
					'item_kit_unit_price'=>$item['price'],
					'commission' => get_commission_for_item_kit($item['item_kit_id'],$item['price'],$cost_price === NULL ? 0.00 : to_currency_no_money($cost_price,10), $item['quantity'], $item['discount']),
				);

				$this->db->insert('sales_item_kits',$sales_item_kits_data);

				$listItems = $this->Item_kit_items->get_info($item['item_kit_id']);
				$listItemsOfBom = [];
				foreach ($this->Item_kit->getKitBomItems($item['item_kit_id']) as $kitBom) {
					$tmpItems = $this->Item_kit_items->get_info($kitBom->bom_id);
					foreach ($tmpItems as $tmpItem) {
						$tmpItem->quantity = $tmpItem->quantity * $kitBom->quantity;
					}

					$listItemsOfBom = array_merge($listItemsOfBom, $tmpItems);
				}

				$listItems = array_merge($listItems, $listItemsOfBom);

				foreach($listItems as $item_kit_item)
				{

					$cur_item_info = $this->Item->get_info($item_kit_item->item_id);
					$cur_item_location_info = $this->Item_location->get_info($item_kit_item->item_id);

					$reorder_level = ($cur_item_location_info && $cur_item_location_info->reorder_level !== NULL) ? $cur_item_location_info->reorder_level : $cur_item_info->reorder_level;

					if( (int) $item_kit_item->measure_id && (int) $cur_item_info->measure_id && $cur_item_info->measure_id != $item_kit_item->measure_id /* && ($mode == 'receive' || $mode == 'purchase_order') */)
					{
						$convertedValue = $this->ItemMeasures->getConvertedValue($item_kit_item->item_id, $item_kit_item->measure_id);
						$item_kit_item->quantity = $item_kit_item->quantity * (int)$convertedValue->qty_converted;
					}


					//Only do stock check + inventory update if we are NOT an estimate
					if ($suspended == 0)
					{
						$stock_recorder_check=false;
						$out_of_stock_check=false;
						$email=false;
						$message = '';

						//checks if the quantity is greater than reorder level
						if(!$cur_item_info->is_service && $cur_item_location_info->quantity > $reorder_level)
						{
							$stock_recorder_check=true;
						}

						//checks if the quantity is greater than 0
						if(!$cur_item_info->is_service && $cur_item_location_info->quantity > 0)
						{
							$out_of_stock_check=true;
						}

						//Update stock quantity IF not a service item and the quantity for item is NOT NULL
						if (!$cur_item_info->is_service)
						{
							$cur_item_location_info->quantity = $cur_item_location_info->quantity !== NULL ? $cur_item_location_info->quantity : 0;

							$this->Item_location->save_quantity($cur_item_location_info->quantity - ($item['quantity'] * $item_kit_item->quantity),$item_kit_item->item_id);
						}

						//Re-init $cur_item_location_info after updating quantity
						$cur_item_location_info = $this->Item_location->get_info($item_kit_item->item_id);

						//checks if the quantity is out of stock
						if($out_of_stock_check && !$cur_item_info->is_service && $cur_item_location_info->quantity <= 0)
						{
							$message= $cur_item_info->name.' '.lang('sales_is_out_stock').' '.to_quantity($cur_item_location_info->quantity);
							$email=true;

						}
						//checks if the quantity hits reorder level
						else if($stock_recorder_check && ($cur_item_location_info->quantity <= $reorder_level))
						{
							$message= $cur_item_info->name.' '.lang('sales_hits_reorder_level').' '.to_quantity($cur_item_location_info->quantity);
							$email=true;
						}

						//send email
						if($this->Location->get_info_for_key('receive_stock_alert') && $email)
						{
							$this->load->library('email');
							$config = array();
							$config['mailtype'] = 'text';
							$this->email->initialize($config);
							$this->email->from($this->Location->get_info_for_key('email') ? $this->Location->get_info_for_key('email') : 'no-reply@mg.4biz.vn', $this->config->item('company'));
							$this->email->to($this->Location->get_info_for_key('stock_alert_email') ? $this->Location->get_info_for_key('stock_alert_email') : $this->Location->get_info_for_key('email'));

							$this->email->subject(lang('sales_stock_alert_item_name').$cur_item_info->name);
							$this->email->message($message);
							$this->email->send();
						}

						if (!$cur_item_info->is_service)
						{
							$qty_buy = -$item['quantity'] * $item_kit_item->quantity;
							$sale_remarks =$this->config->item('sale_prefix').' '.$sale_id;
							$inv_data = array
							(
								'trans_date'=>date('Y-m-d H:i:s'),
								'trans_items'=>$item_kit_item->item_id,
								'trans_user'=>$employee_id,
								'trans_comment'=>$sale_remarks,
								'trans_inventory'=>$qty_buy,
								'location_id' => $this->Employee->get_logged_in_employee_current_location_id()
							);
							$this->Inventory->insert($inv_data);
						}
					}
				}
			}
			$customer = $this->Customer->get_info($customer_id);
 			if ($customer_id == -1 or $customer->taxable)
 			{
				if (isset($item['item_id']))
				{
					foreach($this->Item_taxes_finder->get_info($item['item_id']) as $row)
					{
						$tax_name = $row['percent'].'% ' . $row['name'];

						//Only save sale if the tax has NOT been deleted
						if (!in_array($tax_name, $this->sale_lib->get_deleted_taxes()))
						{
							 $this->db->insert('sales_items_taxes', array(
								'sale_id' 	=>$sale_id,
								'item_id' 	=>$item['item_id'],
								'line'      =>$item['line'],
								'name'		=>$row['name'],
								'percent' 	=>$row['percent'],
								'cumulative'=>$row['cumulative']
							));
						}
					}
				}
				else
				{
					foreach($this->Item_kit_taxes_finder->get_info($item['item_kit_id']) as $row)
					{
						$tax_name = $row['percent'].'% ' . $row['name'];

						//Only save sale if the tax has NOT been deleted
						if (!in_array($tax_name, $this->sale_lib->get_deleted_taxes()))
						{
							$this->db->insert('sales_item_kits_taxes', array(
								'sale_id' 		=>$sale_id,
								'item_kit_id'	=>$item['item_kit_id'],
								'line'      	=>$item['line'],
								'name'			=>$row['name'],
								'percent' 		=>$row['percent'],
								'cumulative'	=>$row['cumulative']
							));
						}
					}
				}
			}
		}

		$this->db->trans_complete();


		if ($this->db->trans_status() === FALSE)
		{
			# var_dump($thong_bao_loi);
			return -1;
		}
        if ($is_insert && $this->sale_lib->get_mode() == 'return_by_sales' && !empty($this->sale_lib->get_id_sale_return())) {
            $this->db->insert('return_all_sale_id', ['sale_id' => $sale_id, 'sale_return_id' => $this->sale_lib->get_id_sale_return()]);
        }	
        
		if($is_insert && !empty($add_more_customer_to_service)) {

            foreach($add_more_customer_to_service as $key=>$customer)
            {
                $add_more_customer_to_service[$key]['sale_id'] = $sale_id;
            }
            $this->db->insert_batch('sales_more_customer_service_sale',$add_more_customer_to_service);
        } else {
            $this->db->where('sale_id',$sale_id);
            $this->db->delete('sales_more_customer_service_sale');
            foreach($add_more_customer_to_service as $key=>$customer) {
                $add_more_customer_to_service[$key]['sale_id'] = $sale_id;
            }
            if(!empty($add_more_customer_to_service))
            $this->db->insert_batch('sales_more_customer_service_sale',$add_more_customer_to_service);
        }
			
		// only for Rem Ha My
		$cartItemsAttributeValue = $this->sale_lib->getCartItemsAttributeValue();
		if (!empty($itemContainsLine)) {
			$this->db->where('sale_id', $sale_id);
			$this->db->delete('sale_item_contains_line_rem_ha_my');
			foreach ($itemContainsLine as $keyItemContainsLine => $valueItemContainsLine ) {

				$dataItemContainsLine = [
						'sale_id' 				=> $sale_id,
						'createdItemName'		=> $valueItemContainsLine['itemName'],
						
					];
				if (!empty($dataItemContainsLine)) {
					$this->db->insert('sale_item_contains_line_rem_ha_my', $dataItemContainsLine);
				}					
				$itemContainsLineInsertId = $this->db->insert_id();

				foreach ($valueItemContainsLine['line'] as $line) {
					$dataItemLineAttributeValue =[];
					$dataItemLine = [
						'createdItemPossition'  => $itemContainsLineInsertId,
						'line'					=> $line,
						'attribute_set_id'      => $this->sale_lib->getCartItemsAttributeSet()[$line]
					];
					if (!empty($dataItemLine )) {
						$this->db->insert('item_line_rem_ha_my', $dataItemLine);
						$itemLineInsertId = $this->db->insert_id();
					}
					foreach ($cartItemsAttributeValue[$line] as $attribute_id => $attribute_value) {
						$dataItemLineAttributeValue[] = [
							'itemLineId' 	 => $itemLineInsertId,
							'attributeId' 	 => $attribute_id,
							'attributeValue' => $attribute_value->entity_value
						];
					}
					if (!empty($dataItemLineAttributeValue )) {
						$this->db->insert_batch('sale_item_attribute_value_rem_ha_my', $dataItemLineAttributeValue);
					}
				}
				
			


			}
		}
			return $sale_id;
	}

	public function getOfflineList($search = []) {
		$this->db->from('sales');
		$this->db->where('sales.deleted', 0);
		$this->db->where('offline', 1);
		if (!empty($search['start_date'])) {
			$this->db->where('sale_time >= ', $search['start_date']);
		}

		if (!empty($search['end_date'])) {
			$this->db->where('sale_time <= ', $search['end_date'] . ' 23:59:59');
		}

		$sales = $this->db->get()->result_array();

		$records = [];

		for($k=0; $k<count($sales); $k++) {
			$offlineDetail = [];
			$offlineDetail['sale'] = $sales[$k];
			$offlineDetail['sale_items'] = $this->getOfflineSaleItems($sales[$k]['sale_id']);
			$offlineDetail['sale_item_kits'] = $this->getOfflineSaleItemKits($sales[$k]['sale_id']);
			$offlineDetail['sale_payments'] = $this->getOfflineSalePayments($sales[$k]['sale_id']);
			$offlineDetail['sale_store_accounts'] = $this->getOfflineSaleStoreAccounts($sales[$k]['sale_id']);
			$records[] = $offlineDetail;
		}

		return $records;
	}

	protected function getOfflineSaleStoreAccounts($saleId) {
		$this->db->from('store_accounts');
		$this->db->where('sale_id', $saleId);
		return $this->db->get()->result_array();
	}

	protected function getOfflineSalePayments($saleId) {
		$this->db->from('sales_payments');
		$this->db->where('sale_id', $saleId);
		return $this->db->get()->result_array();
	}

	protected function getOfflineSaleItemKits($saleId) {
		$this->db->from('sales_item_kits');
		$this->db->where('sale_id', $saleId);
		return $this->db->get()->result_array();
	}

	protected function getOfflineSaleItems($saleId) {
		$this->db->from('sales_items');
		$this->db->where('sale_id', $saleId);
		return $this->db->get()->result_array();
	}

	public function getOffline($search = []) {
		$this->db->from('sales');
		$this->db->join('customers', 'sales.customer_id = customers.person_id', 'left');
		$this->db->join('people', 'customers.person_id = people.person_id', 'left');
		$this->db->where('sales.deleted', 0);
		$this->db->where('sales.offline', 1);

		if (!empty($search['start_date'])) {
			$this->db->where('sale_time >= ', $search['start_date']);
		}

		if (!empty($search['end_date'])) {
			$this->db->where('sale_time <= ', $search['end_date'] . ' 23:59:59');
		}

		$this->db->order_by('sale_id');
		$sales = $this->db->get()->result_array();

		for($k=0;$k<count($sales);$k++)
		{
			$item_names = array();
			$this->db->select('name, sales_items.*');
			$this->db->from('items');
			$this->db->join('sales_items', 'sales_items.item_id = items.item_id');
			$this->db->where('sale_id', $sales[$k]['sale_id']);

			$totalPrice = 0;
			$totalDiscount = 0;
			foreach($this->db->get()->result_array() as $row)
			{
				$item_names[] = $row['name'];
				$totalPrice = $totalPrice + $row['quantity_purchased'] * $row['item_unit_price'];
				$totalDiscount = $totalDiscount + $row['quantity_purchased'] * $row['item_unit_price'] * $row['discount_percent'] / 100;
			}
			$sales[$k]['total_price'] = $totalPrice;
			$sales[$k]['total_discount'] = $totalDiscount;

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

	public function StockOut($saleId = 0, $stockProcess = 1) {
		$this->db->where('sale_id', $saleId);
		$this->db->update('sales', array('is_stock_out' => $stockProcess));
	}

	public function getSaleForStockOut($keywords = '')
	{
		$location_id = $this->Employee->get_logged_in_employee_current_location_id();

		$this->db->from('sales');
		$this->db->join('customers', 'sales.customer_id = customers.person_id', 'left');
		$this->db->join('people', 'customers.person_id = people.person_id', 'left');
		$this->db->where('sales.deleted', 0);
		$this->db->where('suspended', 1);
		$this->db->where('location_id', $location_id);
		$this->db->like('sale_id', $keywords);
		$this->db->order_by('sale_id');
		$sales = [];

		foreach ($this->db->get()->result() as $row) {
			$sales[] = array(
				'sale_id'=>$row->sale_id,
				'label' => $row->sale_id.' ('.$row->sale_time.')',
				'image' => base_url()."assets/img/item.png" ,
				'category' => '',
				'item_number' => '',
			);
		}
		return $sales;
	}

	function getOrders($params)
	{
		$list_sale_id = [];
		$related_sale_ids = [];
		if (!empty($params['supporter_id'])) {
			$this->db->select("sale_id");
			$this->db->from("sales_employees");
			$this->db->where("employee_id", $params['supporter_id']);
			$query = $this->db->get();
			if ($query->num_rows() > 0) {
				$result = $query->result();
				for ($i = 0; $i < count($result); $i++) {
					$list_sale_id[$i] = $result[$i]->sale_id;
				}
			}
		} else {
            $this->db->select('sale_id');
            $this->db->from('sales_employees');
            $this->db->where('employee_id', $this->Employee->get_logged_in_employee_info()->person_id);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $result = $query->result();
                $query->free_result();
                foreach ($result as $row) {
                	$related_sale_ids[$row->sale_id] = $row->sale_id;
				}
            }
		}

		// Get Related Task IDS
        $this->db->select('sale_id');
        $this->db->from('tasks');
        $this->db->join('task_user_relations', 'tasks.id = task_user_relations.task_id');
        $this->db->join('employees', 'employees.id = task_user_relations.user_id');
        $this->db->where('employees.person_id', $this->Employee->get_logged_in_employee_info()->person_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result();
            $query->free_result();
            foreach ($result as $row) {
                $related_sale_ids[$row->sale_id] = $row->sale_id;
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
		
		$location_id = $this->Employee->get_logged_in_employee_current_location_id();
		// $this->db->select('DATE_FORMAT(sale_time, "%d-%m-%Y") as sale_time,customer_id,sale_id,comment_term,employee_id,sale_status_id');
		$this->db->from('sales');
		$this->db->join('customers', 'sales.customer_id = customers.person_id', 'left');
		$this->db->join('people', 'customers.person_id = people.person_id', 'left');
		$this->db->join('sale_status', 'sales.sale_status_id = sale_status.status_id', 'left');
		$this->db->where('sales.deleted', 0);
		$this->db->where('suspended', 0);
		
		if(!empty($params['view']=='view_scope_all'))
		{

		}
		else if(!empty($params['view']=='view_scope_location'))
		{
			$this->db->where('sales.location_id', $location_id);
		}
		else
		{
			if (!empty($related_sale_ids)) {
                $this->db->where_in('sales.sale_id', $related_sale_ids);
			} else {
                $this->db->where('sales.employee_id', $this->Employee->get_logged_in_employee_info()->person_id);
			}
		}


		if (!empty($params['status_type'])) {
			$this->db->where('sale_status.status_type', $params['status_type']);
		}

		if (!empty($params['from_date'])) {
		    $this->db->where('sales.sale_time >= ', $params['from_date']);
		}
		if (!empty($params['to_date'])) {
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
    		    $this->db->where_in('sales.sale_id', $list_sale_id);
    		} else {
    		    $this->db->where('sales.sale_id', 0);
    		}
		}
		if (!empty($params['customer_name'])) {
		    $this->db->like('people.last_name', $params['customer_name']);
		}
		if (!empty($params['item_id'])) {
		    if (count($lst_sale_id_in_item) > 0) {
		        $this->db->where_in('sales.sale_id', $lst_sale_id_in_item);
		    } else {
		        $this->db->where('sales.sale_id', 0);
		    }
		}

		$this->db->order_by('sales.sale_id');
		$sales = $this->db->get()->result_array();
		// echo $this->db->last_query();

		for($k=0;$k<count($sales);$k++)
		{
			$item_names = array();
			$this->db->select('name, sales_items.quantity_purchased,sales_items.calculatedPrice,sales_items.item_unit_price,sales_items.discount_percent');
			$this->db->from('items');
			$this->db->join('sales_items', 'sales_items.item_id = items.item_id');
			$this->db->where('sale_id', $sales[$k]['sale_id']);

			$totalPrice = 0;
			$totalDiscount = 0;
			$item_cal = 0;
			foreach($this->db->get()->result_array() as $row)
			{
				$item_names[] = $row['name'];
				$item_cal += $row['calculatedPrice'];
				$totalPrice = $totalPrice + $row['quantity_purchased'] * $row['item_unit_price'];
				$totalDiscount = $totalDiscount + $row['quantity_purchased'] * $row['item_unit_price'] * $row['discount_percent'] / 100;
			}
			$sales[$k]['total_price'] = $totalPrice;
			$sales[$k]['total_discount'] = $totalDiscount;
			$sales[$k]['calculatedPrice'] = $item_cal;


		}
		return $sales;
	}

	public function getDetailSalesByLocationId($locationId = 0, $search = []) {
		$this->db->select('sales.*, items.*, sales_items.*, categories.name as category, sales_items.description as sales_items_description');
		$this->db->from('sales_items');
		$this->db->join('sales', 'sales_items.sale_id = sales.sale_id');
		$this->db->join('items', 'items.item_id = sales_items.item_id');
		$this->db->join('categories', 'categories.id = items.category_id');

		$this->db->where('sales.location_id', $locationId);

		if (!empty($search['start_date'])) {
			$this->db->where('sales.sale_time >= ', $search['start_date']);
		}

		if (!empty($search['end_date'])) {
			$this->db->where('sales.sale_time <= ', $search['end_date']);
		}

		$this->db->order_by('sales.sale_time');
		return $this->db->get()->result_array();
	}

	public function getInfo($sale_id)
	{
		$this->db->from('sales');
		$this->db->where('sale_id',$sale_id);
		$result = $this->db->get()->result_array();

		if (isset($result[0])) {
			return $result[0];
		}
		return null;
	}

	public function getWarningOrder($intervalDays = 7)
	{
		$query = "select * from " . $this->db->dbprefix('sales') . " WHERE location_id = ". $this->Employee->get_logged_in_employee_current_location_id() ." AND is_stock_out = 0 AND suspended = 1 AND deleted = 0 AND delivery_date IS NOT NULL AND DATE(delivery_date) >= CURRENT_DATE() AND DATE(delivery_date) <= CURRENT_DATE() + INTERVAL ". $intervalDays ." DAY";
		$query = $this->db->query($query);

		if (!empty($query)) {
			return $query->result_array();
		}

		return null;
	}

	function getMeasureOnSaleItem($saleId, $ItemId)
	{
		$this->db->from('sales_items');
		$this->db->join('measures', 'measures.id = sales_items.measure_id', 'left');
		$this->db->where('sale_id', $saleId);
		$this->db->where('item_id', $ItemId);
		$result = $this->db->get();
		if($result->num_rows() > 0)
		{
			$row = $result->result();
			return $row[0];
		}

		return FALSE;
	}

	public function checkExistBySaleTime($saleTime = '') {
		$this->db->from('sales');
		$this->db->where('sale_time', $saleTime);
		$result = $this->db->get();
		if($result->num_rows() > 0)
		{
			return true;
		}
		return false;
	}

    function create_sales_items_temp_table_n9($arrParams = null, $options = null) {
        $where = (!empty($arrParams['where'])) ? $arrParams['where'] : '';
        $query = "CREATE TEMPORARY TABLE phppos_sales_items_temp(
                    SELECT m.* FROM (
                        SELECT si.sale_id, si.item_id, NULL AS item_kit_id, si.line,
                        si.item_cost_price*si.quantity_purchased AS tcost,
                        si.item_unit_price*si.quantity_purchased-si.item_unit_price*si.quantity_purchased*si.discount_percent/100 AS ttotal,
                        (si.item_unit_price*si.quantity_purchased-si.item_unit_price*si.quantity_purchased*si.discount_percent/100)*(SUM(CASE WHEN sit.cumulative != 1 THEN sit.percent ELSE 0 END)/100)
                        +(((si.item_unit_price*si.quantity_purchased-si.item_unit_price*si.quantity_purchased*si.discount_percent/100)*(SUM(CASE WHEN sit.cumulative != 1 THEN sit.percent ELSE 0 END)/100)
                        + (si.item_unit_price*si.quantity_purchased-si.item_unit_price*si.quantity_purchased*si.discount_percent/100)) *(SUM(CASE WHEN sit.cumulative = 1 THEN sit.percent ELSE 0 END))/100) AS tax,
                        s.suspended, s.store_account_payment, s.was_layaway, s.return, s.comment, s.payment_type, s.location_id, s.customer_id, s.is_vat
                        FROM ".$this->db->dbprefix('sales_items')." AS si
                        LEFT JOIN ".$this->db->dbprefix('sales')." AS s ON s.sale_id = si.sale_id
                        LEFT JOIN ".$this->db->dbprefix('sales_items_taxes')." AS sit ON si.sale_id = sit.sale_id AND si.item_id = sit.item_id AND si.line = sit.line
                        WHERE $where
                        GROUP BY si.sale_id, si.item_id, si.line

                        UNION ALL

                        SELECT sk.sale_id, NULL AS item_id, sk.item_kit_id, sk.line,
                        sk.item_kit_cost_price*sk.quantity_purchased AS tcost,
                        sk.item_kit_unit_price*sk.quantity_purchased-sk.item_kit_unit_price*sk.quantity_purchased*sk.discount_percent/100 AS ttotal,
                        (sk.item_kit_unit_price*sk.quantity_purchased-sk.item_kit_unit_price*sk.quantity_purchased*sk.discount_percent/100)*(SUM(CASE WHEN skt.cumulative != 1 THEN skt.percent ELSE 0 END)/100)
                        +(((sk.item_kit_unit_price*sk.quantity_purchased-sk.item_kit_unit_price*sk.quantity_purchased*sk.discount_percent/100)*(SUM(CASE WHEN skt.cumulative != 1 THEN skt.percent ELSE 0 END)/100)
                        + (sk.item_kit_unit_price*sk.quantity_purchased-sk.item_kit_unit_price*sk.quantity_purchased*sk.discount_percent/100)) *(SUM(CASE WHEN skt.cumulative = 1 THEN skt.percent ELSE 0 END))/100) AS tax,
                        s.suspended, s.store_account_payment, s.was_layaway, s.return, s.comment, s.payment_type, s.location_id, s.customer_id, s.is_vat
                        FROM ".$this->db->dbprefix('sales_item_kits')." AS sk
                        LEFT JOIN ".$this->db->dbprefix('sales')." AS s ON s.sale_id = sk.sale_id
                        LEFT JOIN ".$this->db->dbprefix('sales_item_kits_taxes')." AS skt ON sk.sale_id = skt.sale_id AND sk.item_kit_id = skt.item_kit_id AND sk.line = skt.line
                        WHERE $where
                        GROUP BY sk.sale_id, sk.item_kit_id, sk.line
                    ) AS m

                    )";
                    
        $this->db->query($query);
    }

	function _create_sales_items_temp_table_query($where)
	{
		set_time_limit(0);
		$decimals = $this->config->item('number_of_decimals') !== NULL && $this->config->item('number_of_decimals') != '' ? (int)$this->config->item('number_of_decimals') : 2;



        return $this->db->query("CREATE TEMPORARY TABLE ".$this->db->dbprefix('sales_items_temp')."
		SELECT *,(profit-(CASE WHEN TONG_TIEN_THU_CHI IS NULL THEN 0 ELSE TONG_TIEN_THU_CHI END)) AS LOI_NHUAN FROM
            (
                (
                    (
                        (SELECT ".$this->db->dbprefix('sales').".location_id as location_id, ".$this->db->dbprefix('sales').".deleted as deleted,".$this->db->dbprefix('sales').".deleted_by as deleted_by, ".$this->db->dbprefix('sales').".supporter, sale_time, date(sale_time) as sale_date, ".$this->db->dbprefix('registers').'.name as register_name,'.$this->db->dbprefix('sales_items').".sale_id, comment,payment_type, customer_id, employee_id, sold_by_employee_id,
                    ".$this->db->dbprefix('items').".item_id, NULL as item_kit_id, supplier_id, quantity_purchased, ". $this->db->dbprefix('sales_items') .".measure_id, ". $this->db->dbprefix('sales_items') .".measure_qty, item_cost_price, item_unit_price, ".$this->db->dbprefix('categories').'.name as category'.", ".$this->db->dbprefix('categories').'.id as category_id'.",
                            discount_percent, 
                            ROUND(item_unit_price*quantity_purchased,2) as subtotal,
                            ROUND(item_unit_price*quantity_purchased*discount_percent/100,CASE WHEN tax_included =1 THEN 10 ELSE $decimals END) as tien_chiet_khau,
                            STRIP_NON_DIGIT(payment_type) as tien_thanh_toan,
                            ".$this->db->dbprefix('sales_items').".line as line, serialnumber, ".$this->db->dbprefix('sales_items').".description as description,
                            (ROUND(item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100,CASE WHEN tax_included =1 THEN 10 ELSE $decimals END))+(item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100)*(SUM(CASE WHEN cumulative != 1 THEN percent ELSE 0 END)/100)
                            +(((item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100)*(SUM(CASE WHEN cumulative != 1 THEN percent ELSE 0 END)/100) + (item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100))
                            *(SUM(CASE WHEN cumulative = 1 THEN percent ELSE 0 END))/100) as total,
                            (item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100)*(SUM(CASE WHEN cumulative != 1 THEN percent ELSE 0 END)/100)
                            +(((item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100)*(SUM(CASE WHEN cumulative != 1 THEN percent ELSE 0 END)/100) + (item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100))
                            *(SUM(CASE WHEN cumulative = 1 THEN percent ELSE 0 END))/100) as tax,
                            ROUND((item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100),CASE WHEN tax_included =1 THEN 10 ELSE $decimals END) - (item_cost_price*quantity_purchased) as profit,
                             commission, store_account_payment,item_cost_price as sale_item_temp_cost_price, points_used, points_gained
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
                            $where
                            GROUP BY sale_id, item_id, line)
                            UNION ALL
                            (SELECT ".$this->db->dbprefix('sales').".location_id as location_id, ".$this->db->dbprefix('sales').".deleted as deleted,".$this->db->dbprefix('sales').".deleted_by as deleted_by, ".$this->db->dbprefix('sales').".supporter, sale_time, date(sale_time) as sale_date, ".$this->db->dbprefix('registers').'.name as register_name,'.$this->db->dbprefix('sales_item_kits').".sale_id, comment,payment_type, customer_id, employee_id, sold_by_employee_id,
                    NULL as item_id, ".$this->db->dbprefix('item_kits').".item_kit_id, '' as supplier_id, quantity_purchased, '' as measure_id, '' as measure_qty, item_kit_cost_price, item_kit_unit_price,".$this->db->dbprefix('categories').'.name as category'.", ".$this->db->dbprefix('categories').'.id as category_id'.",
                            discount_percent, ROUND(item_kit_unit_price*quantity_purchased-item_kit_unit_price*quantity_purchased*discount_percent/100,CASE WHEN tax_included =1 THEN 10 ELSE $decimals END) as subtotal,
                            ROUND(item_kit_unit_price*quantity_purchased*discount_percent/100,CASE WHEN tax_included =1 THEN 10 ELSE $decimals END) as tien_chiet_khau,
                            STRIP_NON_DIGIT(payment_type) as tien_thanh_toan,
                            ".$this->db->dbprefix('sales_item_kits').".line as line, '' as serialnumber, ".$this->db->dbprefix('sales_item_kits').".description as description,
                            (ROUND(item_kit_unit_price*quantity_purchased-item_kit_unit_price*quantity_purchased*discount_percent/100,CASE WHEN tax_included =1 THEN 10 ELSE $decimals END))+(item_kit_unit_price*quantity_purchased-item_kit_unit_price*quantity_purchased*discount_percent/100)*(SUM(CASE WHEN cumulative != 1 THEN percent ELSE 0 END)/100)
                            +(((item_kit_unit_price*quantity_purchased-item_kit_unit_price*quantity_purchased*discount_percent/100)*(SUM(CASE WHEN cumulative != 1 THEN percent ELSE 0 END)/100) + (item_kit_unit_price*quantity_purchased-item_kit_unit_price*quantity_purchased*discount_percent/100))
                            *(SUM(CASE WHEN cumulative = 1 THEN percent ELSE 0 END))/100) as total,
                            (item_kit_unit_price*quantity_purchased-item_kit_unit_price*quantity_purchased*discount_percent/100)*(SUM(CASE WHEN cumulative != 1 THEN percent ELSE 0 END)/100)
                            +(((item_kit_unit_price*quantity_purchased-item_kit_unit_price*quantity_purchased*discount_percent/100)*(SUM(CASE WHEN cumulative != 1 THEN percent ELSE 0 END)/100) + (item_kit_unit_price*quantity_purchased-item_kit_unit_price*quantity_purchased*discount_percent/100))
                            *(SUM(CASE WHEN cumulative = 1 THEN percent ELSE 0 END))/100) as tax,
                            ROUND((item_kit_unit_price*quantity_purchased-item_kit_unit_price*quantity_purchased*discount_percent/100),CASE WHEN tax_included =1 THEN 10 ELSE $decimals END) - (item_kit_cost_price*quantity_purchased) as profit, commission, store_account_payment, item_kit_cost_price as sale_item_temp_cost_price, points_used, points_gained
                            FROM ".$this->db->dbprefix('sales_item_kits')."
                    INNER JOIN ".$this->db->dbprefix('sales')." ON  ".$this->db->dbprefix('sales_item_kits').'.sale_id='.$this->db->dbprefix('sales').'.sale_id'."
                    INNER JOIN ".$this->db->dbprefix('item_kits')." ON  ".$this->db->dbprefix('sales_item_kits').'.item_kit_id='.$this->db->dbprefix('item_kits').'.item_kit_id'."
                    LEFT OUTER JOIN ".$this->db->dbprefix('sales_item_kits_taxes')." ON  "
                        .$this->db->dbprefix('sales_item_kits').'.sale_id='.$this->db->dbprefix('sales_item_kits_taxes').'.sale_id'." and "
                        .$this->db->dbprefix('sales_item_kits').'.item_kit_id='.$this->db->dbprefix('sales_item_kits_taxes').'.item_kit_id'." and "
                        .$this->db->dbprefix('sales_item_kits').'.line='.$this->db->dbprefix('sales_item_kits_taxes').'.line'. "
                    LEFT OUTER JOIN ".$this->db->dbprefix('registers')." ON  ".$this->db->dbprefix('registers').'.register_id='.$this->db->dbprefix('sales').'.register_id'."
                    LEFT OUTER JOIN ".$this->db->dbprefix('categories')." ON  ".$this->db->dbprefix('categories').'.id='.$this->db->dbprefix('item_kits').'.category_id'."
                            $where
                            GROUP BY sale_id, item_kit_id, line)
                    ) 
                    AS BANG_SALE
                    LEFT JOIN
                        (
							SELECT ex.sale_id AS EX_SALE_ID,expense_type,
							SUM(CASE WHEN expense_type = 1 THEN expense_amount ELSE -expense_amount END) AS TONG_TIEN_THU_CHI
							FROM phppos_expenses ex GROUP BY EX_SALE_ID
                        ) 
                    AS BANG_CHI_PHI ON BANG_SALE.sale_id = BANG_CHI_PHI.EX_SALE_ID
                )
                            )
		 ORDER BY sale_id, line");
	}

	function get_all_materials() {
		$this->db->from('sales');
		$this->db->where('deleted', 0);
		$this->db->where('quotes_contract', 1);
		$this->db->order_by('sale_id', 'desc');
		return $this->db->get();
	}

	function get_info_sale_order($sale_id) {
		$this->db->select('sales.*,people.last_name as ten_khach_hang,people.address_1 as dia_chi');
		$this->db->from('sales');
		$this->db->join('people', 'sales.customer_id = people.person_id','left');
		$this->db->where('sale_id', $sale_id);
		return $this->db->get()->row();
	}

	function get_sale_item_by_sale_item($sale_id, $item_id) {
		$this->db->where("sale_id", $sale_id);
		$this->db->where("item_id", $item_id);
		$query = $this->db->get("sales_items");
		return $query->row();
	}

	function insert_sale_material($data) {
		$this->db->insert("sales_materials", $data);
	}

	//Get image file
	function getImgBySales($id = -1){
		$this->db->select('*')->from('app_files');
		$this->db->where('file_id =' .$id);

		$result = $this->db->get();
		return $result;
	}

    function get_items($arrParams = null, $options = null) {
        $this->db -> select('s.*')
                  -> from('sales AS s');

        if(!empty($arrParams['sale_ids'])) {
            $this->db->where('s.sale_id IN ('.implode(', ', $arrParams['sale_ids']).')');
        }

        $query = $this->db->get();

        $result_tmp = $query->result_array();

        $this->db->flush_cache();

        $result = array();
        if(!empty($result_tmp)) {
            foreach($result_tmp as $val)
                $result[$val['sale_id']] = $val;
        }

        return $result;
    }

    function get_sale_info($sale_id, $options = null) {
        global $biz_cached;
        if($options == null) {
            if(!isset($biz_cached['sale']['detail_'.$sale_id])) {
                $this->db -> select('s.*, sv.override_profit_commission, sv.min_profit, sv.min_profit_commission')
                          -> from('sales AS s')
                          -> join('services AS sv', 's.service_id = sv.id', 'left')
                          -> where('sale_id', $sale_id);

                $query = $this->db->get();

                $result = $query->row_array();
                if(!empty($result)) {
                    $quote_contract_model = $this->model_load_model('QuotesConstract');
                    $result['sale_mode'] = $this->get_type_of_sale($result);
                    if(!empty($result['document'])) {
                        $cid                 = explode(',', $result['document']);
                        $quote_contract_list = $quote_contract_model->get_items(array('cid'=>$cid));
                        $result['document']  = $quote_contract_list;
                    }

                    $result['sale_mode'] = $this->get_type_of_sale($result);
                }

                $this->db->flush_cache();
            }else
                $result = $biz_cached['sale']['detail_'.$sale_id];
        }

        return $result;
    }

    function get_type_of_sale($sale_info){
        if($sale_info['store_account_payment'] == 1)
            $result = 'store_account_payment';
        else {
            $item_model = $this->model_load_model('Item');
            $item_id_for_flat_discount_item = $this->Item->get_item_id_for_flat_discount_item();

            $this->db->select('SUM(quantity_purchased) AS total')
                     ->from('sales_items')
                     ->where('sale_id', $sale_info['sale_id'])
                     ->where('item_id != ' . $item_id_for_flat_discount_item);

            $query = $this->db->get();

            $result = $query->row_array();

            $this->db->flush_cache();

            $quantity_item = (!empty($result['total'])) ? $result['total'] : 0;

            $this->db->select('SUM(quantity_purchased) AS total')
                    ->from('sales_item_kits')
                    ->where('sale_id', $sale_info['sale_id']);

            $query = $this->db->get();

            $result = $query->row_array();

            $this->db->flush_cache();

            $quantity_item_kit = (!empty($result['total'])) ? $result['total'] : 0;

            $total = $quantity_item + $quantity_item_kit;

            if($total < 0)
                $result = 'return';
            else
                $result = 'sale';
        }

        return $result;
    }

    function get_payment_from_sale($arrParams = null, $options = null) {
        $this->db->select('*')
                 ->from('sales_payments');

        if ($arrParams['sale_id'] > 0) {
            $this->db->where('sale_id', $arrParams['sale_id']);
        }

        $query = $this->db->get();

        $result = $query->result_array();

        $this->db->flush_cache();
        return $result;
    }
		function get_customer_service_from_sale($arrParams = null, $options = null) {
        $this->db->select('*')
                 ->from('sales_more_customer_service_sale');

        if($arrParams['sale_id'] > 0) {
            $this->db->where('sale_id', $arrParams['sale_id']);
        }

        $query = $this->db->get();

        $result = $query->result_array();

        $this->db->flush_cache();
        return $result;
    }

    function get_minus_liability_amount_from_sale($arrParams = null) {
        $result = 0;
        $payments = $this->get_payment_from_sale($arrParams);
        if(!empty($payments)) {
            foreach($payments as $val) {
                if($val['payment_type'] == 'Trừ công nợ')
                    $result = $result + $val['payment_amount'];
            }
        }

        return $result;
    }

    function get_payment_total_from_sale($arrParams = null) {
        $result = 0;
        $this->db->select("SUM(p.payment_amount) AS payment_amount_total")
                 ->from('sales_payments AS p')
                 ->join('sales AS s', 's.sale_id = p.sale_id')
                 ->where('p.sale_id', $arrParams['sale_id'])
                 ->where('p.payment_type<>'."'".lang('common_debt_customer')."'")
                 ->where('s.deleted', 0);

        $query = $this->db->get();

        $result_tmp = $query->row_array();
        $this->db->flush_cache();

        if(!empty($result_tmp)) {
            $result = $result_tmp['payment_amount_total'];
        }else
            $result = 0;

        return $result;
    }

    function get_point_payment_from_sale($arrParams = null) {
        $result = 0;
        $this->db->select("SUM(p.payment_amount) AS diem")
                 ->from('sales_payments AS p')
                 ->join('sales AS s', 's.sale_id = p.sale_id')
                 ->where("p.payment_type = 'Điểm'")
                 ->where('p.sale_id', $arrParams['sale_id'])
                 ->where('s.deleted', 0);

        $query = $this->db->get();

        $result_tmp = $query->row_array();
        $this->db->flush_cache();

        if(!empty($result_tmp)) {
            $result = $result_tmp['diem'];
        }else
            $result = 0;

        return $result;
    }

    function get_gift_card_payment_from_sale($arrParams = null) {
        $result = 0;
        $this->db->select("SUM(p.payment_amount) AS the_qua_tang")
                 ->from('sales_payments AS p')
                 ->join('sales AS s', 's.sale_id = p.sale_id')
                 ->where("p.payment_type LIKE '%quà%'")
                 ->where('p.sale_id', $arrParams['sale_id'])
                 ->where('s.deleted', 0);

        $query = $this->db->get();

        $result_tmp = $query->row_array();
        $this->db->flush_cache();

        if(!empty($result_tmp)) {
            $result = $result_tmp['the_qua_tang'];
        }else
            $result = 0;

        return $result;
    }

    function get_debt_payment_amount_from_sale($arrParams = null) {
        $result = 0;
        $payments = $this->get_payment_from_sale($arrParams);

        if(!empty($payments)) {
            foreach($payments as $val) {
                if($val['payment_type'] == 'Sổ ghi nợ')
                    $result = $result + $val['payment_amount'];
            }
        }

        return $result;
    }

    function get_cash_payment_from_sale($arrParams = null, $options = null) {
        $result = array();
        if(!empty($arrParams['sale_payments'])) {
            foreach($arrParams['sale_payments'] as $val) {
                if($val['payment_type'] == lang('common_cash'))
                    $result[] = $val;
            }
        }

        return $result;
    }

    function get_item_taxes($arrParams = null, $options = null) {
        if($options['task'] == 'by-item-line') {
            $this->db->select('*')
                    ->from('sales_items_taxes')
                    ->where('sale_id', $arrParams['sale_id'])
                    ->where('item_id', $arrParams['item_id'])
                    ->where('line', $arrParams['line']);

            $query = $this->db->get();

            $result = $query->result_array();

            $this->db->flush_cache();
        }

        return $result;
    }

    function get_items_by_sale($sale_id) {
        $this->db -> select('s.sale_id, s.item_id, s.description, s.serialnumber, s.line, s.quantity_purchased, s.measure_id, s.measure_qty, s.item_cost_price, s.item_unit_price, s.discount_percent, s.commission, p.tax_included, p.commission_percent, p.commission_fixed, p.commission_percent_type, p.name AS item_name, p.product_id')
                  -> from('sales_items AS s')
                  -> join('items AS p', 's.item_id = p.item_id')
                  -> where('sale_id', $sale_id);

        $query = $this->db->get();

        $result = $query->result_array();

        $this->db->flush_cache();

        return $result;
    }

    function get_items_kit_by_sale($sale_id) {
        $this->db -> select('s.sale_id, s.item_kit_id, s.description, s.line, s.quantity_purchased, s.item_kit_cost_price, s.item_kit_unit_price, s.discount_percent, s.commission, i.tax_included, i.commission_percent, i.commission_fixed, i.commission_percent_type, i.name as item_kit_name, i.product_id')
                  -> from('sales_item_kits AS s')
                  -> join('item_kits AS i', 's.item_kit_id = i.item_kit_id')
                  -> where('sale_id', $sale_id);

        $query = $this->db->get();
        $result = $query->result_array();

        $this->db->flush_cache();

        return $result;
    }

    function get_item_taxes_by_sale($sale_id) {
        $this->db -> select('sit.*, si.discount_percent, si.item_unit_price')
                  -> from('sales_items_taxes AS sit')
                  -> join('sales_items AS si', 'sit.sale_id = si.sale_id AND sit.item_id = si.item_id AND sit.line = si.line')
                  -> where('sit.sale_id', $sale_id);

        $query = $this->db->get();
        $result = $query->result_array();
        $this->db->flush_cache();

        return $result;
    }

    function get_items_kit_taxes($arrParams = null, $options = null) {
        if($options['task'] == 'by-item-kit-line') {
            $this->db->select('*')
                     ->from('sales_item_kits_taxes')
                     ->where('sale_id', $arrParams['sale_id'])
                     ->where('item_kit_id', $arrParams['item_kit_id'])
                     ->where('line', $arrParams['line']);

            $query = $this->db->get();

            $result = $query->result_array();

            $this->db->flush_cache();
        }

        return $result;
    }

    function get_item_kit_taxes($sale_id) {
        $this->db -> select('*')
                  -> from('sales_item_kits_taxes')
                  -> where('sale_id', $sale_id);

        $query = $this->db->get();
        $result = $query->result_array();

        $this->db->flush_cache();

        return $result;
    }

    function get_point_amount_from_sale_order($arrParams = null, $options = null) {
        $this->db-> select("SUM(p.payment_amount) AS point")
                 -> from('sales_payments AS p')
                 -> where("p.payment_type = 'Điểm'")
                 -> group_by('p.sale_id');

        if(!empty($arrParams['sale_ids'])) {
            $sale_ids = $arrParams['sale_ids'];
            $this->db->where('p.sale_id IN ('.implode(',', $sale_ids).')');
        }

        $query = $this->db->get();

        $result_tmp = $query->result_array();

        $this->db->flush_cache();
        $result = array();

        if(!empty($result_tmp)) {
            foreach($result_tmp as $val)
                $result[$val['sale_id']] = $val['point'];
        }

        return $result;
    }


    function get_gift_card_amount_from_sale_order($arrParams = null, $options = null) {
        $this->db-> select("SUM(p.payment_amount) AS the_qua_tang")
                 -> from('sales_payments AS p')
                 -> where("p.payment_type LIKE '%quà%'")
                 -> group_by('p.sale_id');

        if(!empty($arrParams['sale_ids'])) {
            $sale_ids = $arrParams['sale_ids'];
            $this->db->where('p.sale_id IN ('.implode(',', $sale_ids).')');
        }

        $query = $this->db->get();

        $result_tmp = $query->result_array();

        $this->db->flush_cache();
        $result = array();

        if(!empty($result_tmp)) {
            foreach($result_tmp as $val)
                $result[$val['sale_id']] = $val['the_qua_tang'];
        }

        return $result;
    }

    function get_commission_from_sale_order($arrParams = null, $options = null) {
        $this->db -> select('s.sale_id, SUM(commission) AS sum_commission')
                  -> from('sales_commission AS c')
                  -> join('sales AS s', 'c.sale_id = s.sale_id')
                  -> where('s.commission_status', 1)
                  -> group_by('s.sale_id');

        if(!empty($arrParams['sale_ids'])) {
            $sale_ids = $arrParams['sale_ids'];
            $this->db->where('s.sale_id IN ('.implode(',', $sale_ids).')');
        }

        $query = $this->db->get();
        $result_tmp = $query->result_array();

        $this->db->flush_cache();
        $result = array();
        if(!empty($result_tmp)) {
            foreach($result_tmp as $val)
                $result[$val['sale_id']] = $val['sum_commission'];
        }

        return $result;
    }

    function get_store_accounts($arrParams = null, $options = null) {
        if($options['task'] == 'by-sale-id') {
            $this->db -> select('*')
                      -> from('store_accounts AS s')
                      ->where('s.sale_id', $arrParams['sale_id'])
                      ->order_by('s.sno', 'DESC');

            $query = $this->db->get();

            $result = $query->row_array();

            $this->db->flush_cache();
        }

        return $result;
    }

    function count_item($arrParams = null, $options = null) {
        $location_id = $this->Employee->get_logged_in_employee_current_location_id();
        $sale_prefix = $this->config->item('sale_prefix');

        if($options['task'] == null) {
            $this->db -> select('COUNT(s.sale_id) AS totalItem')
                     -> from('sales AS s')
                     ->where('s.location_id', $location_id)
                     ->where('s.deleted', 0);

            if($options['task'] == 'suspended')
                $this->db->where('r.suspended', 1);

            if(!empty($arrParams['keywords'])) {
                $keywords = trim($arrParams['keywords']);
                $this->db->where("CONCAT_WS('$sale_prefix', ' ', s.sale_id) LIKE '%$keywords%'");

                $_SESSION['sales_model_filter']['keywords'] = $keywords;
            }

            if(!empty($arrParams['start_date'])) {
                $start_date = date("Y-m-d", strtotime($arrParams['start_date'])) . ' 00:00:00';
                $this->db->where("s.sale_time >= '$start_date'");

                $_SESSION['sales_model_filter']['start_date'] = $arrParams['start_date'];
            }else
                $_SESSION['sales_model_filter']['start_date'] = '';

            if(!empty($arrParams['end_date'])) {
                $end_date = date("Y-m-d", strtotime($arrParams['end_date'])) . ' 23:59:59';
                $this->db->where("s.sale_time <= '$end_date'");

                $_SESSION['sales_model_filter']['end_date'] = $arrParams['end_date'];
            }else
                $_SESSION['sales_model_filter']['end_date'] = '';

            $query = $this->db->get();

            $result = $query->row()->totalItem;

            $this->db->flush_cache();
        }
        elseif($options['task'] == 'vat_order') {
            $this->_sale_tmp_ids = array();

            $customer_id = $arrParams['customer_id'];
            $this->db -> select('sv.sale_id')
                      -> from('sale_vat_relationships AS sv')
                      -> join('sales AS s', 'sv.sale_vat_id = s.sale_id AND s.is_vat = 1')
                      -> where('s.customer_id', $customer_id);

            $query = $this->db->get();

            $result_tmp = $query->result_array();
            $this->db->flush_cache();
            if(!empty($result_tmp)) {
                foreach($result_tmp as $val)
                    $this->_sale_tmp_ids[] = $val['sale_id'];
            }

            $this->db -> select('COUNT(s.sale_id) AS total_item')
                     -> select("DATE_FORMAT(s.sale_time, '%d-%m-%Y %H:%i') AS sale_time_format", FALSE)
                     -> from('sales AS s')
                     -> where('s.suspended', 0)
                     -> where('s.store_account_payment', 0)
                     -> where('s.is_vat', 0)
                     -> where('s.location_id', $location_id)
                     -> where('s.deleted', 0)
                     -> where('s.customer_id', $customer_id)
                     -> where('(SELECT SUM(payment_amount) FROM phppos_sales_payments WHERE sale_id = s.sale_id) > (SELECT SUM(ttotal) FROM phppos_sales_items_temp WHERE sale_id = s.sale_id)');

            if(!empty($this->_sale_tmp_ids)) {
                $this->db->where('s.sale_id NOT IN ('.implode(', ', $this->_sale_tmp_ids).')');
            }

            $query = $this->db->get();

            $result = $query->row()->total_item;

            $this->db->flush_cache();
        }
        return $result;
    }

    function count_sale_store_payment($arrParams = null, $options = null) {
        if($options['task'] == null) {
            $this->load->library('sale_lib');
            $customer_id = $this->sale_lib->get_customer();

            $payment_type = lang('common_store_account');
            $location_id  = $this->Employee->get_logged_in_employee_current_location_id();

            $this->db -> select('COUNT(s.sale_id) AS totalItem')
                      -> from('sales_payments AS sp')
                      -> join('sales AS s', 'sp.sale_id = s.sale_id')
                      -> where('s.location_id', $location_id)
                      -> where('s.customer_id', $customer_id)
                      -> where('sp.payment_type = \''.$payment_type.'\'');

            $store_account_payment_value = $this->sale_lib->get_store_account_payment_value();
            if($store_account_payment_value == 1) {
                $this->db->where('s.return != 1');
            }else {
                $this->db->where('s.return', 1);
            }

            $query = $this->db->get();

            $result = $query->row()->totalItem;

            $this->db->flush_cache();

            return $result;
        }
    }

    function list_sale_store_payment($arrParams = null, $options = null) {
        if($options['task'] == null) {
            $paginator = $arrParams['paginator'];
            $customer_id = $this->sale_lib->get_customer();

            $payment_type = lang('common_store_account');
            $location_id  = $this->Employee->get_logged_in_employee_current_location_id();

            if(!empty($arrParams['col']) && !empty($arrParams['order'])){
                $col   = $arrParams['col'];
                $order = $arrParams['order'];

                $order_by = $col . ' ' . $order;

                $_SESSION['sale_store_payment_modal_filter']['col']   = $arrParams['col'];
                $_SESSION['sale_store_payment_modal_filter']['order'] = $arrParams['order'];
            }

            $_SESSION['sale_store_payment_modal_filter']['current_page'] = $page = $arrParams['page'];

            $limit = $paginator['per_page'];
            $offset = ($page - 1)*$paginator['per_page'];

            $store_account_payment_value = $this->sale_lib->get_store_account_payment_value();
            if($store_account_payment_value == 1)
                $where = "s.return != 1";
            else
                $where = "s.return = 1";

            $sql = 'SELECT '.$this->db->dbprefix('sales').'.sale_id, '.$this->db->dbprefix('sales').'.code, DATE_FORMAT('.$this->db->dbprefix('sales').'.sale_time, \'%d-%m-%Y %H:%i:%s\') AS sale_time_format,
                                    IFNULL((SELECT SUM(amount) FROM '.$this->db->dbprefix('sales_store_account').' WHERE sale_id = '.$this->db->dbprefix('sales').'.sale_id),0) AS da_thanh_toan,
                                    IFNULL((SELECT SUM(payment_amount) FROM '.$this->db->dbprefix('sales_payments').' WHERE sale_id = '.$this->db->dbprefix('sales').'.sale_id AND payment_type = \''.$payment_type.'\'),0) AS so_tien_no
                                    FROM '.$this->db->dbprefix('sales').'
                                    WHERE '.$this->db->dbprefix('sales').'.sale_id IN (
                                        SELECT s.sale_id
                                        FROM '.$this->db->dbprefix('sales_payments').' AS sp
                                        JOIN '.$this->db->dbprefix('sales').' As s ON sp.sale_id = s.sale_id
                                        WHERE s.location_id = '.$location_id.'
                                        AND s.customer_id = '.$customer_id.'
                                        AND sp.payment_type = \''.$payment_type.'\'
                                        AND '.$where.'
                                        AND s.deleted = 0
                                    )
                                    ORDER BY ' . $order_by . ' LIMIT ' . $limit .' OFFSET ' . $offset;

            $query = $this->db->query($sql);

            $result_tmp = $query->result_array();
            $result = array();

            if(!empty($result_tmp)) {
                $sale_store_payment = $this->sale_lib->get_sale_store_payment();
                foreach($result_tmp as $val) {
                    $sale_id = $val['sale_id'];
                    if(isset($sale_store_payment[$sale_id]))
                        $val['amount'] = $sale_store_payment[$sale_id];
                    else
                        $val['amount'] = 0;

                    $result[$sale_id] = $val;
                }
            }
        }

        return $result;
    }

    function get_sale_store_payment_items($arrParams = null, $options = null) {
        $this->load->library('sale_lib');
        $sale_prefix = $this->config->item('sale_prefix');
        $payment_type = lang('common_store_account');
        $location_id  = $this->Employee->get_logged_in_employee_current_location_id();
        $customer_id = $this->sale_lib->get_customer();

        $sale_ids = implode(',', $arrParams['sale_ids']);

        $store_account_payment_value = $this->sale_lib->get_store_account_payment_value();
        if($store_account_payment_value == 1)
            $where = "s.return != 1";
        else
            $where = "s.return = 1";

        $query = $this->db->query('SELECT '.$this->db->dbprefix('sales').'.sale_id, '.$this->db->dbprefix('sales').'.code, DATE_FORMAT('.$this->db->dbprefix('sales').'.sale_time, \'%d-%m-%Y %H:%i:%s\') AS sale_time_format,
                                    IFNULL((SELECT SUM(amount) FROM '.$this->db->dbprefix('sales_store_account').' WHERE sale_id = '.$this->db->dbprefix('sales').'.sale_id),0) AS da_thanh_toan,
                                    IFNULL((SELECT SUM(payment_amount) FROM '.$this->db->dbprefix('sales_payments').' WHERE sale_id = '.$this->db->dbprefix('sales').'.sale_id AND payment_type = \''.$payment_type.'\'),0) AS so_tien_no
                                    FROM '.$this->db->dbprefix('sales').'
                                    WHERE '.$this->db->dbprefix('sales').'.sale_id IN (
                                        SELECT s.sale_id
                                        FROM '.$this->db->dbprefix('sales_payments').' AS sp
                                        JOIN '.$this->db->dbprefix('sales').' As s ON sp.sale_id = s.sale_id
                                        WHERE s.location_id = '.$location_id.'
                                        AND s.customer_id = '.$customer_id.'
                                        AND sp.payment_type = \''.$payment_type.'\'
                                        AND '.$where.'
                                        AND s.sale_id IN ('.$sale_ids.')
                                    )');

        $result_tmp = $query->result_array();
        $result = array();

        if(!empty($result_tmp)) {
            $sale_store_payment = $this->sale_lib->get_sale_store_payment();
            foreach($result_tmp as $val) {
                $sale_id = $val['sale_id'];
                if(isset($sale_store_payment[$sale_id]))
                    $val['amount'] = $sale_store_payment[$sale_id];
                else
                    $val['amount'] = 0;

                $val['con_lai'] = $val['so_tien_no'] - $val['da_thanh_toan'];

                if(!empty($val['code']))
                    $val['sale_code'] = $val['code'];
                else
                    $val['sale_code'] = $sale_prefix . ' ' . $val['sale_id'];

                $result[$sale_id] = $val;
            }
        }

        return $result;
    }

    function getAllDataSale($arrParams = array()){
        $sale_prefix     = $this->config->item('sale_prefix');
        $location_id     = $this->Employee->get_logged_in_employee_current_location_id();
        $key_filter = isset($arrParams['key_filter']) ? $arrParams['key_filter'] : '';

		$this->db->select("s.*,CONCAT('$sale_prefix', ' ', s.sale_id) as code_don_hang, p.first_name, p.last_name, c.person_id")
			->select("DATE_FORMAT(s.sale_time, '%d-%m-%Y %H:%i:%s') AS sale_time_format", FALSE)
			->from('sales AS s')
			->join('customers AS c', 'c.id = s.customer_id')
			->join('people AS p', 'p.person_id = c.person_id')
			->where('s.location_id', $location_id)
			->where('s.deleted', 0)
			->where('p.xoa', 0);

        if (!empty($arrParams['keywords'])) {
            $keywords = trim($arrParams['keywords']);
            $this->db->where('(p.first_name LIKE \'%' . $keywords . '%\' OR p.last_name LIKE \'%' . $keywords . '%\' OR p.address_1 LIKE \'%' . $keywords . '%\' OR p.ho_chieu LIKE \'%' . $keywords . '%\' OR p.phone_number LIKE \'%' . $keywords . '%\' OR p.email LIKE \'%' . $keywords . '%\' OR p.code LIKE \'%' . $keywords . '%\' OR p.chung_minh_thu LIKE \'%' . $keywords . '%\')');
            $_SESSION[$key_filter]['keywords'] = $keywords;
        }

        if (!empty($arrParams['employee_id']) && $arrParams['employee_id'] > 0) {
            $this->db->where('c.created_by', $arrParams['employee_id']);
            $_SESSION[$key_filter]['employee_id'] = $arrParams['employee_id'];
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
            }elseif ($arrParams['customers_table'] == 'company_form') {
                $this->db->where('company_form_id', $arrParams['category_child']);
            }
        }

		$query = $this->db->get();

		$result = $query->result_array();

		$this->db->flush_cache();
		if(!empty($result)) {
			$result = $this->do_sale_info($result, array());
		}


        return $result;
	}

    function list_item($arrParams = null, $options = null) {
        $paginator       = $arrParams['paginator'];
        $sale_prefix     = $this->config->item('sale_prefix');
        $location_id     = $this->Employee->get_logged_in_employee_current_location_id();

        if($options['task'] == null) {

            $this->db->select("s.*,CONCAT('$sale_prefix', ' ', s.sale_id) as code_don_hang")
                     ->select("DATE_FORMAT(s.sale_time, '%d-%m-%Y %H:%i:%s') AS sale_time_format", FALSE)
                     ->from('sales AS s')
                     ->where('s.location_id', $location_id)
                     ->where('s.deleted', 0);

            if(isset($arrParams['suspended']) && $arrParams['suspended'] == 1)
                $this->db->where('r.suspended', 1);

            $page = (empty($arrParams['page'])) ? 1 : $arrParams['page'];
            if(!isset($arrParams['per_page'])) $arrParams['per_page'] = 1;
            $this->db->limit($arrParams['per_page'],($page - 1)*$arrParams['per_page']);

            if(!empty($arrParams['keywords'])) {
                $keywords = trim($arrParams['keywords']);
                $this->db->where("CONCAT_WS('$sale_prefix', ' ', s.sale_id) LIKE '%$keywords%'");

                $_SESSION['sales_model_filter']['keywords'] = $keywords;
            }

            if(!empty($arrParams['start_date'])) {
                $start_date = date("Y-m-d", strtotime($arrParams['start_date'])) . ' 00:00:00';
                $this->db->where("s.sale_time >= '$start_date'");

                $_SESSION['sales_model_filter']['start_date'] = $arrParams['start_date'];
            }else
                $_SESSION['sales_model_filter']['start_date'] = '';

            if(!empty($arrParams['end_date'])) {
                $end_date = date("Y-m-d", strtotime($arrParams['end_date'])) . ' 23:59:59';
                $this->db->where("s.sale_time <= '$end_date'");

                $_SESSION['sales_model_filter']['end_date'] = $arrParams['end_date'];
            }else
                $_SESSION['sales_model_filter']['end_date'] = '';

            if(!empty($arrParams['col']) && !empty($arrParams['order'])){
                $col   = $this->_fields[$arrParams['col']];
                $order = $arrParams['order'];

                $this->db->order_by($col, $order);

                $_SESSION['sales_model_filter']['col']   = $arrParams['col'];
                $_SESSION['sales_model_filter']['order'] = $arrParams['order'];
            }

            $_SESSION['sales_model_filter']['current_page'] = $page = $arrParams['page'];

            $this->db->limit($paginator['per_page'],($page - 1)*$paginator['per_page']);

            $query = $this->db->get();

            $result = $query->result_array();

            $this->db->flush_cache();
            if(!empty($result)) {
							
                $result = $this->do_sale_info($result, $options);
            }
        }
        elseif($options['task'] == 'vat_order') {
            $customer_id = $arrParams['customer_id'];
            $this->db -> select('s.sale_id, s.code, s.comment')
                     -> select("DATE_FORMAT(s.sale_time, '%d-%m-%Y %H:%i') AS sale_time_format", FALSE)
                     -> from('sales AS s')
                     -> where('s.suspended', 0)
                     -> where('s.store_account_payment', 0)
                     -> where('s.is_vat', 0)
                     -> where('s.location_id', $location_id)
                     -> where('s.deleted', 0)
                     -> where('s.customer_id', $customer_id)
                     -> where('(select SUM(payment_amount) FROM phppos_sales_payments WHERE sale_id = s.sale_id) > (SELECT SUM(ttotal) FROM phppos_sales_items_temp WHERE sale_id = s.sale_id)');

            if(!empty($this->_sale_tmp_ids)) {
                $this->db->where('s.sale_id NOT IN ('.implode(', ', $this->_sale_tmp_ids).')');
            }

            if(!empty($arrParams['col']) && !empty($arrParams['order'])){
                $col   = $this->_fields[$arrParams['col']];
                $order = $arrParams['order'];

                $this->db->order_by($col, $order);

                $_SESSION['sale_vat_order_modal_filter']['col']  = $arrParams['col'];
                $_SESSION['sale_vat_order_modal_filter']['order'] = $arrParams['order'];
            }

            $page = $arrParams['page'];
            $this->db->limit($paginator['per_page'],($page - 1)*$paginator['per_page']);

            $query = $this->db->get();
            $result = $query->result_array();

            $this->db->flush_cache();

            if(!empty($result)) {
                foreach($result as $val)
                    $sale_ids[] = $val['sale_id'];

                $tmp                      = $this->get_order_total_tax($sale_ids);
                $sale_order_total_list    = $tmp['total_result'];
                $total_payment            = $this->get_total_payment($sale_ids);

                foreach($result as &$value) {
                    if(empty($value['code']))
                        $value['code'] = $this->config->item('sale_prefix') . ' ' . $value['sale_id'];

                    $value['order_value']        = (isset($sale_order_total_list[$value['sale_id']]) && !empty($sale_order_total_list[$value['sale_id']])) ? $sale_order_total_list[$value['sale_id']] : 0;
                    $value['payment_value']  = isset($total_payment[$value['sale_id']]) ? $total_payment[$value['sale_id']] : 0;
                }
            }

        }

        return $result;
    }


    protected function do_sale_info($result, $options = null) {
        if(!empty($result)) {
            $currency_symbol = $this->config->item('currency_symbol');
            $item_model      = $this->model_load_model('Item');
            $customer_ids = array();
            foreach($result as $val) {
                $sale_ids[] = $val['sale_id'];
                if($val['customer_id'] > 0)
                    $customer_ids[] = $val['customer_id'];
            }

            $customer_ids = array_unique($customer_ids);
            if(count($customer_ids) > 0) {
                $customers_info = $this->Customer->get_info_by_ids($customer_ids);
            }

            $this->db->select('i.name, i.product_id, i.category_id, si.quantity_purchased, si.item_unit_price, si.discount_percent, si.sale_id, si.item_id, si.line, i.tax_included, si.item_id')
                     ->from('sales_items AS si')
                     ->join('items AS i', 'si.item_id = i.item_id')
                     ->where('si.sale_id IN ('.implode(', ', $sale_ids).')');

            $query = $this->db->get();
            $tmp = $query->result_array();

            $this->db->flush_cache();

            $discount_items = array();
            $discount_items_total = 0;
            if(!empty($tmp)) {
                $discount_id = $item_model->get_item_id_for_flat_discount_item();
                foreach($tmp as $val) {
                    if($val['item_id'] == $discount_id)
                        $discount_items[$val['sale_id']][] = $val;
                    else
                        $sale_items[$val['sale_id']][] = $val;
                }
            }

            $this->db->select('st.sale_id, st.item_id, st.line, st.name, st.percent, st.cumulative', 'i.tax_included')
                    ->from('sales_items_taxes AS st')
                    ->join('items as i', 'st.item_id = i.item_id')
                    ->where('st.sale_id IN ('.implode(', ', $sale_ids).')');

            $query = $this->db->get();
            $tmp = $query->result_array();

            if(!empty($tmp)) {
                foreach($tmp as $val) {
                    $tax_items[$val['sale_id']][] = $val;
                }
            }

            if($options['validate_contract'] == true) {
                $contract_modal       = $this->model_load_model('Contract');
                $sale_ids_in_contract = $contract_modal->get_sale_id_in_contract($sale_ids);
            }

            foreach($result as &$value) {
                $sale_id = $value['sale_id'];
                $total_all = 0;
                $tax_minus = 0; //all tax is added to the total sale order
                $item_price_line = array();
                if(isset($sale_items[$sale_id])) {
                    foreach($sale_items[$sale_id] as &$item) {
                        $line = $sale_id . '-' . $item['item_id'] .  '-' . $item['line'];
                        if(isset($tax_items)) $tax = $tax_items[$sale_id]; else $tax = 0;
                        $item['price'] =  $this->price_in_sale($item, $tax);
                        $item['discount_price'] = ($item['price'] * $item['discount_percent'] )/100;
                        $item['total'] = ($item['price'] - $item['discount_price']) * $item['quantity_purchased'];
                        $item_price_line[$line] = $item['total'];
                        $total_all = $total_all + $item['total'];
                    }
                }

                $value['tax_info']  = '';

                if(isset($tax_items[$sale_id])) {
                    $tax_info = array();
                    $tax_info_full = array();
                    foreach($tax_items[$sale_id] as $tax) {
                        $line = $sale_id . '-' . $tax['item_id'] .  '-' . $tax['line'];

                        $percent = $tax['percent'];
                        $name = $tax['name'];
                        $price = $item_price_line[$line];
                        $tax_value = ($percent * $price)/100;

                        $percent_format = number_format($percent,2);

                        if(!isset($tax_info[$percent_format . '% ' . $name]))
                            $tax_info[$percent_format . '% ' . $name] =  $tax_value;
                        else
                            $tax_info[$percent_format . '% ' . $name] =  $tax_info[$name] + $tax_value;

                        if($tax['tax_included'] == 0) {
                            $tax_minus = $tax_minus + $tax_value;
                        }
                    }

                    if(count($tax_info)>0) {
                        foreach($tax_info as $key => &$t_value) {
                            $t_value = to_currency($t_value);
                            $tax_info_full[] = $key . ' : ' . $t_value;
                        }
                    }

                    $value['tax_info'] = implode('<br />', $tax_info_full);
                }

                $value['discount_total'] = 0;
                if(isset($discount_items[$sale_id])) {
                    foreach($discount_items[$sale_id] as $discount_item)
                        $value['discount_total'] = $value['discount_total'] + abs($discount_item['item_unit_price']);
                }

                $value['discount_total'] = to_currency($value['discount_total']);
                $value['total_all'] = $value['total_all_raw'] = $total_all - convert_number($value['discount_total']) + $tax_minus;
                $value['total_all'] = to_currency($value['total_all']);
				if(isset($value['customer_id'])){
					 $value['customer_name'] = $customers_info[$value['customer_id']]['first_name'] . ' ' . $customers_info[$value['customer_id']]['last_name'];
				}
               

                if($options['validate_contract'] == true) {
                    if(in_array($sale_id, $sale_ids_in_contract))
                        $value['select'] = 'false';
                    else
                        $value['select'] = 'true';
                }else
                    $value['select'] = 'true';
            }
        }

        return $result;
    }

    function get_total_payment($sale_ids) {
        $this->db -> select('sale_id, SUM(payment_amount) AS sum_payment_amount')
                 -> from('sales_payments')
                 -> where('sale_id IN ('.implode(',', $sale_ids).')')
                 -> group_by('sale_id');

        $query = $this->db->get();

        $result_tmp = $query->result_array();

        $this->db->flush_cache();

        $result = array();
        if(!empty($result_tmp)) {
            foreach($result_tmp as $val) {
                $result[$val['sale_id']] = $val['sum_payment_amount'];

            }
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

    function delete_sale_employee($arrParams = null, $options = null) {
        if($options['task'] == 'by-sale') {
            $this->db->where('sale_id', $arrParams['sale_id']);
            $this->db->delete('sales_employees');

            $this->db->flush_cache();
        }
    }

    function delete_sale_commission($sale_id, $options = null) {
        if($options['task'] == null) {
            $this->db->where('sale_id', $sale_id);
            $this->db->delete('sales_commission');

            $this->db->where('sale_id', $sale_id);
            $this->db->delete('sales_items_commission');

            $this->db->where('sale_id', $sale_id);
            $this->db->delete('sales_item_kits_commission');
        }

        $this->db->flush_cache();
    }

    function price_in_sale($item_or_item_kit, $taxes) {
        if(isset($item_or_item_kit['item_unit_price']))
            $price = $item_or_item_kit['item_unit_price'];
        else
            $price = $item_or_item_kit['item_kit_unit_price'];

        if($item_or_item_kit['tax_included'] == 1) {
            if(!empty($taxes)) {
                $is_cumulative = false;
                foreach($taxes as $tax) {
                    if($tax['cumulative'] == 1)
                        $is_cumulative = true;
                }

                if($is_cumulative == true) {
                    foreach($taxes as $tax) {
                        if($tax['cumulative'] == 1)
                            $tax_2 = $tax;
                        else
                            $tax_1 = $tax;
                    }

                    $tax_value_1 = ($price * $tax_1['percent']) / 100;
                    $tax_value_2 = (($price + $tax_value_1) * $tax_2['percent'])/100;

                    $price = $price + $tax_value_1 + $tax_value_2;
                }else {

                    $percent = 0;
                    foreach($taxes as $tax) {
                        $percent = $percent + $tax['percent'];
                    }

                    $price = $price / 100 * 130;

                }
            }
        }

        return $price;
    }

    function get_group_from_sale($sale_id, $options = null) {
        $this->db -> select('se.*')
                  -> from('sales_employees AS se')
                  -> where('se.sale_id', $sale_id);

        $query = $this->db->get();
        $result_tmp = $query->result_array();

        $this->db->flush_cache();

        $result = array();
        if(!empty($result_tmp)) {
            $employee_model = $this->model_load_model('Employee');
            $group_model    = $this->model_load_model('Group');

            $sale_info = $this->get_sale_info($sale_id);

            foreach($result_tmp as $val) {
                $employee_ids[] = $val['employee_id'];
                $group_ids[]    = $val['group_id'];
            }

            $employee_ids = array_unique($employee_ids);
            $group_ids    = array_unique($group_ids);

            $employee_list = $employee_model->get_info_by_ids($employee_ids);
            $group_list    = $group_model->get_items(array('include_deleted'=>true, 'cid'=>$group_ids));

            foreach($result_tmp as $val) {
                $employee_info = array(
                    'id' => $val['employee_id'],
                    'name' => $employee_list[$val['employee_id']]['first_name'] . ' ' . $employee_list[$val['employee_id']]['last_name']
                );
                $groups[$val['group_id']][] = $employee_info;

            }

            foreach($groups as $group_id => $group) {
                $result[$group_id] = array(
                    'location_id' => $sale_info['location_id'],
                    'group_id' => $group_id,
                    'group_name' => $group_list[$group_id]['name'],
                    'list' => $group
                );
            }
        }

        return $result;
    }


    function get_quantity_sale_of_current_month($arrParams = null, $options = null) {
        $month = date('m');
        $year  = date("y");
        $time  = $month . '-' . $year;

        $this->db->select("COUNT(s.sale_id) AS total_item")
                 ->from('sales AS s')
                 ->where("DATE_FORMAT(s.sale_time, '%m-%y') = '$time'");

        if($arrParams['service_id'] > 0)
            $this->db->where('s.service_id', $arrParams['service_id']);

        $query = $this->db->get();

        $result_tmp = $query->row_array();

        $this->db->flush_cache();
        $result = $result_tmp['total_item'];

        return $result;
    }

    function get_payment_history($arrParams = null, $options = null) {
        $this->db->select("*")
                 ->from('sales_store_account')
                 ->where('sale_id', $arrParams['sale_id'])
                 ->order_by('sno_id', 'ASC');

        $query = $this->db->get();

        $result = $query->result_array();

        $this->db->flush_cache();

        return $result;
    }
    
    function doApproved($sale_id)
    {
        $this->db->where('sale_id', $sale_id);
        $this->db->update('sales', ['status' => 'approved']);
    }

    function update_employee_commission_status($arrParams = null, $options = null) {
        $sale_id                = $arrParams['sale_id'];
        $status                 = $arrParams['status'];
        $commission_method      = $arrParams['commission_method'];

        if($commission_method == 'order') {
            $this->db->where("sale_id",$sale_id);
            $this->db->update('sales_commission',array('status'=>$status));
        }else {
            $this->db->where("sale_id",$sale_id);
            $this->db->update('sales_items_commission',array('status'=>$status));

            $this->db->where("sale_id",$sale_id);
            $this->db->update('sales_item_kits_commission',array('status'=>$status));
        }
    }

    function get_employee_list_from_sale($arrParams = null, $options = null) {
        if($options['task'] == null) {
            $sale_id = $arrParams['sale_id'];
            $this->db -> select('emp.sale_id, emp.group_id, emp.employee_id, p.first_name, p.last_name')
                      -> from('sales_employees AS emp')
                      -> join('people AS p', 'emp.employee_id = p.person_id')
                      -> where('sale_id', $sale_id);

            $query = $this->db->get();

            $result = $query->result_array();

            $this->db->flush_cache();

            return $result;
        }
    }

    function get_sales_commission_for_each_employee($arrParams = null, $options = null) {
        $sale_id = $arrParams['sale_id'];
        $this->db -> select('p.first_name, p.last_name, SUM(sc.commission) AS commission, SUM(sc.commission_percent) AS commission_percent, sc.employee_id')
                  -> from('sales_commission AS sc')
                  -> join('people AS p', 'sc.employee_id = p.person_id');

        if($arrParams['sale_id'] > 0) {
            $this->db->where('sc.sale_id', $arrParams['sale_id']);
        }

        $this->db->group_by('sc.employee_id');


        $query = $this->db->get();
        $result = $query->result_array();

        $this->db->flush_cache();

        return $result;
    }
		    function get_sales_commission_for_each_employee_in_separate_sales($arrParams = null, $options = null) {
        $sale_id = $arrParams['sale_id'];
        $this->db -> select('sc.sale_id as sale_id,p.first_name, p.last_name, SUM(sc.commission) AS commission, SUM(sc.commission_percent) AS commission_percent, sc.employee_id')
                  -> from('sales_commission AS sc')
                  -> join('people AS p', 'sc.employee_id = p.person_id')
									-> group_by('sc.sale_id,sc.employee_id');
        $query = $this->db->get();
        $result = $query->result_array();

        $this->db->flush_cache();

        return $result;
    }

    function valid_vat_order($sale_ids) {
        $this->db -> select("COUNT(*) AS total_item")
                  -> from('sale_vat_relationships')
                  -> where('sale_id IN ('.implode(', ', $sale_ids).')');

        $query = $this->db->get();

        $result = $query->row()->total_item;

        if($result > 0)
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
    
    /*
    * Update store account table
    * @param $data array
    * @param sale_id
    * @return void
    */
    public function updateStoreAccount($data, $sale_id)
    {
        $this->db->set($data);
        $this->db->where('sale_id', $sale_id);
        $this->db->update('store_accounts');
    }
    
    public function get_suspended_sales_for_item($item_id)
    {
        $this->db->from('sales');
        $this->db->join('sales_items', 'sales.sale_id = sales_items.sale_id');
        $this->db->where('sales.suspended', '1');
        $this->db->where('sales_items.item_id', $item_id);
        return $this->db->get()->result_array();
    }
    
    /*
    * Update get store account items by sale_id
    * @param sale_id
    * @return array
    */
    public function getStoreAccountBySaleId($sale_id)
    {
       
        $this->db->where('sale_id', $sale_id);
        return $this->db->get('store_accounts')->row_array();
    }
    
 
    public function get_search_suggestions($searchText)
	{
		$this->db->from('sales');
		$this->db->like('sale_id', $searchText);
		$this->db->where('return', 0);
        $this->db->where('suspended', 0);
        $this->db->where('deleted', 0);
        $this->db->limit(100);
        $temp_suggestions   = $this->db->get()->result_array();
        $suggestions        = [];
        foreach($temp_suggestions as $value) {
            $suggestions[]=array(
                'label'     => 'HĐBH '.$value['sale_id'],
                'sale_id'   => $value['sale_id']
            );
        }
        
        return $suggestions; 
	}

    public function is_return_sale_exists($sale_id) {
        $this->db->select('COUNT(id) as isExists')
                 ->from('return_all_sale_id')
                 ->where('sale_return_id', $sale_id);
        $total = $this->db->get()->row_array()['isExists'];
        if ($total >= 1) {
            return true;
        }
        
        return false;
    }
    
    public function get_employee_by_sale($sale_id) {
    	$this->db->select('people.*,e.id as employee_id');
    	$this->db->from('people');
    	$this->db->join('sales_employees', 'people.person_id = sales_employees.employee_id');
    	$this->db->join('employees as e', 'e.person_id = sales_employees.employee_id', 'left');
    	$this->db->where('sales_employees.sale_id', $sale_id);
    	$query = $this->db->get();
    	// echo $this->db->last_query();die();
    	return $query->result();
    }
    
    public function update_suspended($params) {
    	$this->db->update('sales', array(
    			'suspended' => $params['suspended']
    	), array(
    			'sale_id' => $params['sale_id']
    	));
    }
    
    public function get_all_sales()
    {
        $this->db->from('sales');
        $this->db->where('return', 0);
        $this->db->where('suspended', 0);
        $this->db->where('deleted', 0);
        $this->db->where('sale_id not in (select m.sale_id from '. $this->db->prefix .'tasks m ) ');
        
        $query = $this->db->get();
        return $query->result_array();
    }


    #D13
    function get_item_by_sale_d13($sale_id)
    {
    	$this->db->select('phppos_sales_items.sale_id,phppos_sales_items.item_id as service_id , phppos_categories.name as service_type_name, phppos_sales_items.item_name as service_name');
    	$this->db->from('phppos_sales_items');
    	$this->db->join('phppos_items', 'phppos_sales_items.item_id = phppos_items.item_id', 'left');
    	$this->db->join('phppos_categories', 'phppos_items.category_id = phppos_categories.id', 'left');
    	$this->db->where('sale_id', $sale_id);
    	return $this->db->get()->row_array();
    }
}
?>
