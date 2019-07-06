<?php
require_once (APPPATH . "controllers/Receivings.php");

class BizReceivings extends Receivings
{
    protected $_prefixDocument = 'REC';

    protected $_paginator = array(
        'per_page' => 10,
        'uri_segment' => 3
    );
	protected $payment_options = array();
    
    function __construct()
    {
        parent::__construct();
        $this->load->model('StockIn');
    }
    
    public function transfer_pending()
    {
        $data = array();
        $data['transferings'] = $this->Receiving->getAllTransferings();
        $this->load->view('receivings/transferings', $data);
    }
    
    function delete_suspended_receiving()
    {
        $this->check_action_permission('delete_receiving');
        $suspended_recv_id = $this->input->post('suspended_receiving_id');
        $recvInfo = $this->Receiving->get_info($recvId)->row();

        if ($suspended_recv_id && empty($recvInfo->is_stock_in))
        {
            $this->receiving_lib->delete_suspended_receiving_id();
            $this->Receiving->delete($suspended_recv_id, false);
        }
        redirect('receivings/suspended');
    }
    
	function delete($receiving_id)
	{
		$this->check_action_permission('delete_receiving');
		
		$receiving_info = $this->Receiving->get_info($receiving_id)->row_array();
		
		$data = array();
		if($receiving_info['deleted'] == 0)
		{
			if ($this->Receiving->delete($receiving_id, false))
			{
				$data['success'] = true;
			}
			else
			{
				$data['success'] = false;
			}
			
		}
		else
		{
			$data['success'] = true;
		}
		
		$this->load->view('receivings/delete', $data);
		
	}
    
    function edit_item($line)
    {
        $_POST['value'] = convert_number($_POST['value']);
        $data= array();

        $this->form_validation->set_rules('price', 'lang:common_price', 'numeric');
        $this->form_validation->set_rules('quantity', 'lang:common_quantity', 'numeric');
        $this->form_validation->set_rules('quantity_received', 'lang:receivings_qty_received', 'numeric');
        $this->form_validation->set_rules('discount', 'lang:common_discount_percent', 'numeric');

        $description = NULL;
        $serialnumber = NULL;
        $price = NULL;
        $quantity = NULL;
        $discount = NULL;
        $expire_date = NULL;
        $quantity_received = NULL;

        $measure = NULL;

        if($this->input->post("name"))
        {
            $variable = $this->input->post("name");
            $$variable = $this->input->post("value");
        }

        if ($discount !== NULL && $discount == '')
        {
            $discount = 0;
        }

        if ($quantity !==NULL && $quantity == '')
        {
            $quantity = 0;
        }

        if ($quantity_received !== NULL && $quantity_received == '')
        {
            $quantity_received = 0;
        }

        if ($this->form_validation->run() != FALSE)
        {
            $mode = $this->receiving_lib->get_mode();
            if ($mode == 'transfer' || $mode == 'return') {
                $quantity = abs($quantity) * 1;
                $quantity_received = abs($quantity_received) * -1;
            }
            $this->receiving_lib->edit_item(
                    $line,
                    $description,
                    $serialnumber,
                    $expire_date,
                    $quantity,
                    $quantity_received,
                    $discount,
                    $price,
                    $measure
            );
        }
        else
        {
            $data['error']=lang('receivings_error_editing_item');
        }

        $this->_reload($data);
    }

    function delete_payment()
    {
        $id = $this->input->post("id");
				$this->receiving_lib->delete_payment($id);
				$total_payment = $this->receiving_lib->get_payments_totals();
				$data['total']=$this->receiving_lib->get_total();
				$payment_options = array(
                lang('common_cash') => lang('common_cash'),
                lang('common_store_account') => lang('common_store_account'),
                lang('common_check') => lang('common_check'),
                lang('common_debit') => lang('common_debit'),
                lang('common_credit') => lang('common_credit')
        );
        foreach($this->Appconfig->get_additional_payment_types() as $additional_payment_type)
        {
            $payment_options[$additional_payment_type] = $additional_payment_type;
        }
				if($total_payment > $data['total'])
					{
						 $payment_options=array(
									lang('common_refund_from_supplier') => lang('common_refund_from_supplier'),
									lang('common_debt_supplier') => lang('common_debt_supplier')
									);
					}
        $deleted_payment_types = $this->config->item('deleted_payment_types');
        $deleted_payment_types = explode(',',$deleted_payment_types);

        foreach($deleted_payment_types as $deleted_payment_type)
        {
            foreach($payment_options as $payment_option)
            {
                if ($payment_option == $deleted_payment_type)
                {
                    unset($payment_options[$payment_option]);
                }
            }
        }
				
	
				$html_payments        = $this->load->view('receivings/partials/payments', ['payments' => $this->receiving_lib->get_payments()], TRUE);
				$html_payments_option = $this->load->view('receivings/partials/payments_option', ['payment_options' => $payment_options,'selected_payment'=> $this->receiving_lib->get_selected_payment(),'mode'=>$this->receiving_lib->get_mode()], TRUE);
        echo json_encode(['success' => true, 'amount_tendered' => to_currency_without_unit($this->getAmountTendered()), 'html_payments' => $html_payments,'html_payments_option'=>$html_payments_option]);
    }

    function add_payment()
    {
        $_POST['amount'] = convert_number($this->input->post('amount'));
        $paymentType = $this->input->post("payment_type")?$this->input->post("payment_type"):'Tiền mặt';
        $amount = $this->input->post("amount");
				if($amount == 0)
				{
					$response = array('success'=>false, 'msg'=>'Không thể thêm thanh toán bằng 0.');
           echo json_encode($response);
           return;
				}
				if(empty($paymentType))
				{
					$response = array('success'=>false, 'msg'=>'Bạn phải chọn một thanh toán.');
           echo json_encode($response);
           return;
				}
        $mode = $this->receiving_lib->get_mode();
        if($mode == 'store_account_payment') {
            $supplier_id=$this->receiving_lib->get_supplier();
            if($supplier_id == -1) {
                $response = array('success'=>false, 'msg'=>'Phải chọn nhà cung cấp.');
                echo json_encode($response);
                return;
            }
        }

        $payments = $this->receiving_lib->get_payments();
        if(!empty($payments)) {
            foreach($payments as $index => $val) {
                if($paymentType == $val['payment_type']) {
                    $response = array('success'=>false, 'msg'=>'Không được tạo thêm thanh toán "'.$paymentType.'"');
                    echo json_encode($response);
                    return;
                }
            }
        }
        # tính tiền còn lại
        $payment_total = $amount;

        $payments = $this->receiving_lib->get_payments();
        if(!empty($payments)) {
            foreach($payments as $val)
                $payment_total = $payment_total + $val['amount'];
        }

        $total    = $this->receiving_lib->get_total();

        if(($mode == 'receive' || $mode == 'purchase_order') && $this->config->item('config_vat_order') == 1){}
        else {
            if($payment_total > $total) {
                $response = array('success'=>false, 'msg'=>'Tổng giá trị đơn hàng không được vượt quá số tiền thanh toán.');
                echo json_encode($response);

                return;
            }
        }
			
        # lưu dữ liệu vào session
        $this->receiving_lib->add_payment($paymentType, $amount);
					
        # Nếu là chuyển kho
        if($mode == 'transfer'){
            echo json_encode(['success' => true]);
        # Nếu không phải
        } else {
            # lấy ra số tiền còn phải trả
            $data['amount_tendered'] = $this->getAmountTendered();
						//payment option
						$total_payment = $this->receiving_lib->get_payments_totals();
						$data['total']=$this->receiving_lib->get_total();
						$payment_options = array(
										lang('common_cash') => lang('common_cash'),
										lang('common_store_account') => lang('common_store_account'),
										lang('common_check') => lang('common_check'),
										lang('common_debit') => lang('common_debit'),
										lang('common_credit') => lang('common_credit')
						);
						foreach($this->Appconfig->get_additional_payment_types() as $additional_payment_type)
						{
								$payment_options[$additional_payment_type] = $additional_payment_type;
						}
				
						if($total_payment > $data['total'])
						{
							 $payment_options=array(
										lang('common_refund_from_supplier') => lang('common_refund_from_supplier'),
										lang('common_debt_supplier') => lang('common_debt_supplier')
										);
						}
						$deleted_payment_types = $this->config->item('deleted_payment_types');
						$deleted_payment_types = explode(',',$deleted_payment_types);

						foreach($deleted_payment_types as $deleted_payment_type)
						{
								foreach($payment_options as $payment_option)
								{
										if ($payment_option == $deleted_payment_type)
										{
												unset($payment_options[$payment_option]);
										}
								}
						}
					
            $html_payments = $this->load->view('receivings/partials/payments', ['payments' => $this->receiving_lib->get_payments()], TRUE);
						$html_payments_option = $this->load->view('receivings/partials/payments_option', ['payment_options' => $payment_options,'selected_payment'=> $this->receiving_lib->get_selected_payment(),'mode'=>$this->receiving_lib->get_mode()], TRUE);
            echo json_encode(['success' => true, 'amount_tendered' => to_currency_without_unit($this->getAmountTendered()), 'html_payments' => $html_payments,'html_payments_option'=>$html_payments_option,'is_po' =>$this->receiving_lib->get_po()]);
        }
    }

    function check_before_complete() {




        $receiving_mode  = $this->receiving_lib->get_mode();
        $supplier_id     = $this->receiving_lib->get_supplier();
        $task_id         = $this->receiving_lib->get_task();
        $minus_liability = $this->receiving_lib->get_minus_liability();
        $receiving_total = $payment_total = $this->receiving_lib->get_total();
        
        $debit_payment   = $this->receiving_lib->get_debit_payment();
        if($receiving_mode == 'receive' || $receiving_mode == 'purchase_order') {


            if($task_id == -1) {
                $response = array('flag'=>'false', 'msg'=>'Phải chọn dự án.');
                echo json_encode($response);
                return;
            }
             if($receiving_total <=0) {
                $response = array('flag'=>'false', 'msg'=>'Thanh toán phải lớn hơn 0.');
                echo json_encode($response);
                return;
            }

            $change_id = $this->receiving_lib->get_change_recv_id();
            if($supplier_id == -1) {
                $response = array('flag'=>'false', 'msg'=>'Phải chọn nhà cung cấp.');
                echo json_encode($response);
                return;
            } else {
                # sửa đơn hàng
                if($change_id > 0) {
                    $old_receiving_info        = $this->receiving_lib->get_receive($change_id);
                    $old_receiving_total_value = $old_receiving_info['gia_tri_don_hang'];
                    $old_payment_total         = $this->Receiving->get_payment_total_from_receiving(array('receiving_id'=>$change_id));
                    $supplier_info = $this->Supplier->get_info($supplier_id);

                    if($debit_payment > 0) {
                        $old_debit_payment   = $this->Receiving->get_debt_payment_amount_from_receiving(array('receiving_id'=>$change_id));
                        $balance_tmp   = $supplier_info->balance - $old_debit_payment + $debit_payment;

                        if($balance_tmp < 0) {
                            $response = array('flag'=>'false', 'msg'=>$this->config->item('supplier_balance') . ' : không thể ghi sổ nợ.');
                            echo json_encode($response);
                            return;
                        }
                    } # end debit_payment

                    if($payment_total > $receiving_total && $supplier_id == -1) {
                        $response = array('flag'=>'false', 'msg'=>'Phải chọn nhà cung cấp.');
                        echo json_encode($response);
                        return;
                    }
                    if($payment_total > $receiving_total && $task_id == -1) {
                        $response = array('flag'=>'false', 'msg'=>'Phải chọn Dự án.');
                        echo json_encode($response);
                        return;
                    }
                    $subtraction_1 = $payment_total - $receiving_total;
                    $subtraction_2 = $old_payment_total - $old_receiving_total_value;
                    if($subtraction_1 !=  $subtraction_2) {
                        if($payment_total > $receiving_total) {
                            if($old_payment_total > $old_receiving_total_value) {
                                $transaction_amount = - ($old_payment_total - $old_receiving_total_value) + ($payment_total - $receiving_total);
                                $new_balance_2 = $supplier_info->balance_2 + $transaction_amount;

                            }else {
                                $transaction_amount = $payment_total - $receiving_total;
                                $new_balance_2 = $supplier_info->balance_2 + $transaction_amount;
                            }

                        if($new_balance_2 < 0) {
                            $response = array('flag'=>'false', 'msg'=>'Công nợ không được âm.');
                            echo json_encode($response);

                            return;
                        }
                    } else {
                        if($old_payment_total > $old_receiving_total_value) {
                            $transaction_amount = $old_receiving_total_value - $old_payment_total;
                            $new_balance_2 = $supplier_info->balance_2 + $transaction_amount;

                            if($new_balance_2 < 0) {
                                $response = array('flag'=>'false', 'msg'=>'Công nợ không được âm.');
                                echo json_encode($response);

                                return;
                            }

                        }
                    }
                }
            } # end change_id
            else {
                if($debit_payment > 0) {
                    $supplier_info = $this->Supplier->get_info($supplier_id);
                }

                if($payment_total > $receiving_total && $supplier_id == -1) {
                    $response = array('flag'=>'false', 'msg'=>'Phải chọn nhà cung cấp.');
                    echo json_encode($response);
                    return;
                }
                  if($payment_total > $receiving_total && $task_id == -1) {
                    $response = array('flag'=>'false', 'msg'=>'Phải chọn dự án.');
                    echo json_encode($response);
                    return;
                }

            } # end else
        } # end else
            

            if($this->config->item('config_vat_order') == 0 && $payment_total > $receiving_total) {
                $response = array('flag'=>'false', 'msg'=>'Tổng tiền thanh toán không được lớn hơn giá trị đơn hàng.');
                echo json_encode($response);
                return;
            }
        } # end purchase_order
        elseif($receiving_mode == 'return') {
            $change_id = $this->receiving_lib->get_change_recv_id();
            if($change_id > 0) {
                if($debit_payment > 0 || $minus_liability > 0) {
                    $old_debit_payment   = $this->Receiving->get_debt_payment_amount_from_receiving(array('receiving_id'=>$change_id));
                    $old_minus_liability = $this->Receving->get_minus_liability_amount_from_receiving(array('receiving_id'=>$change_id));

                    if($supplier_id == -1) {
                        $response = array('flag'=>'false', 'msg'=>'Phải chọn Nhà cung cấp.');
                        echo json_encode($response);
                        return;
                    }

                    $supplier_info = $this->Supplier->get_info($supplier_id);

                    $balance_tmp   = $supplier_info->balance + $old_minus_liability - $minus_liability;
                    $balance_2_tmp = $supplier_info->balance_2 - $old_debit_payment + $debit_payment;

                    if($balance_2_tmp < 0) {
                        $response = array('flag'=>'false', 'msg'=>$this->config->item('supplier_balance') . ' : không thể ghi sổ nợ.');
                        echo json_encode($response);
                        return;
                    }

                    if($balance_tmp < 0) {
                        $response = array('flag'=>'false', 'msg'=>'Không đủ tiền để trừ công nợ.');
                        echo json_encode($response);
                        return;
                    }
                }
            }else {
                if($debit_payment > 0 || $minus_liability > 0) {
                    if($supplier_id == -1) {
                        $response = array('flag'=>'false', 'msg'=>'Phải chọn Nhà cung cấp.');
                        echo json_encode($response);
                        return;
                    }
                     if($task_id == -1) {
                        $response = array('flag'=>'false', 'msg'=>'Phải chọn Dự án.');
                        echo json_encode($response);
                        return;
                    }


                    $supplier_info = $this->Supplier->get_info($supplier_id);

                    if($minus_liability > $supplier_info->balance) {
                        $response = array('flag'=>'false', 'msg'=>'Không đủ tiền để trừ công nợ.');
                        echo json_encode($response);
                        return;
                    }
                }
            }
        }
        elseif($receiving_mode == 'store_account_payment') {
            // check công nợ
            $store_account_payment_amount = $this->receiving_lib->get_total();
            $supplier_id                  = $this->receiving_lib->get_supplier();
            $supplier_info                = $this->Supplier->get_info($supplier_id);
            $store_account_payment_value  = $this->receiving_lib->get_store_account_payment_value();

            if($store_account_payment_value == 1 && $store_account_payment_amount > $supplier_info->balance) {
                $response = array('flag'=>'false', 'msg'=>'Số tiền thanh toán công nợ không hợp lệ.');
                echo json_encode($response);
                return;
            }

            if($store_account_payment_value == 2 && $store_account_payment_amount > $supplier_info->balance_2) {
                $response = array('flag'=>'false', 'msg'=>'Số tiền thanh toán công nợ không hợp lệ.');
                echo json_encode($response);
                return;
            }

            // thanh toán công nợ cho đơn hàng?
            $receiving_store_payment = $this->receiving_lib->get_receiving_store_payment();
            if(empty($receiving_store_payment)) {
                $response = array('flag'=>'true');
                echo json_encode($response);

                return;
            }

            // check xem tổng số tiền chi có bằng số tiền thanh toán ko
            $payment_debt = $this->receiving_lib->get_debt_payment();
            $all_amount   = $this->receiving_lib->get_all_amount_from_receiving_store_payment();

            if($payment_debt != $all_amount) {
                $so_du = $payment_debt - $all_amount;
                $so_du = to_currency($so_du);
                $response = array('flag'=>'false', 'msg'=>'Còn dư ' . $so_du);
                echo json_encode($response);

                return;
            }

            //check xem có đơn hàng nào k còn ghi nợ hay không?
            $receiving_ids = array_keys($receiving_store_payment);
            $debt_orders = $this->Receiving->get_receiving_store_payment_items(array('receiving_ids'=>$receiving_ids));

            if(count($receiving_ids) != count($debt_orders)) {
                $response = array('flag'=>'false', 'msg'=>'1 trong những đơn hàng không còn ghi nợ.');
                echo json_encode($response);

                $this->receiving_lib->clear_receiving_store_payment();

                return;
            }

            //check xem từng khoản chi có nhiều hơn tiền còn nợ ở đơn hàng không
            foreach($debt_orders as $item) {
                if($item['amount'] > $item['con_lai']) {
                    $response = array('flag'=>'false', 'msg'=>$this->config->item('receive_prefix') . ' ' . $item['receiving_id'] . ': Số tiền thanh toán thêm không được quá số tiền nợ còn lại.');
                    echo json_encode($response);

                    return;
                }
            }
        } 
        elseif($receiving_mode == 'transfer') {
            $location_id=$this->receiving_lib->get_location();
            if($location_id == -1) {
                    $response = array('flag'=>'false', 'msg'=>'Bạn chưa chọn kho');
                    echo json_encode($response);
                    return;
                }
        }

        echo json_encode(array('flag'=>'true'));
    }

    function complete()
    {
        $data['cart']=$this->receiving_lib->get_cart();
        if (empty($data['cart']))
        {
            redirect('receivings');
        }
        $data['task_id'] = $this->receiving_lib->get_task();
        $data['taxes']=$this->receiving_lib->get_taxes();
        $data['subtotal']=$this->receiving_lib->get_subtotal();
        $data['total']=$this->receiving_lib->get_total();
        $data['receipt_title']=lang('receivings_receipt');
        $supplier_id=$this->receiving_lib->get_supplier();
        $location_id=$this->receiving_lib->get_location();
        $employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
        $comment = $this->input->post('comment') ? $this->input->post('comment') : '';
        $data['comment'] = $comment;
        $emp_info=$this->Employee->get_info($employee_id);
        $data['payment_type'] = $payment_type = $this->input->post('payment_type');
        $data['payments'] = $this->receiving_lib->get_payments();
        $data['mode']=$this->receiving_lib->get_mode();
        $data['change_receiving_date'] =$this->receiving_lib->get_change_receiving_date_enable() ?  $this->receiving_lib->get_change_receiving_date() : false;
        $old_date = $this->receiving_lib->get_change_recv_id()  ? $this->Receiving->get_info($this->receiving_lib->get_change_recv_id())->row_array() : false;
        $old_date=  $old_date ? date(get_date_format().' '.get_time_format(), strtotime($old_date['receiving_time'])) : date(get_date_format().' '.get_time_format());
        $data['transaction_time']= $this->receiving_lib->get_change_receiving_date_enable() ?  date(get_date_format().' '.get_time_format(), strtotime($this->receiving_lib->get_change_receiving_date())) : $old_date;

        $data['suspended']  = 0;
        $data['is_po'] = 0;
        $data['discount_exists'] = $this->_does_discount_exists($data['cart']);

        if ($this->input->post('amount_tendered'))
        {
            $data['amount_tendered'] = $this->input->post('amount_tendered');
            $decimals = $this->config->item('number_of_decimals') !== NULL && $this->config->item('number_of_decimals') != '' ? (int)$this->config->item('number_of_decimals') : 2;

            $data['amount_change'] = to_currency($data['amount_tendered'] - round($data['total'], $decimals));
           
        }
        $data['employee']=$emp_info->first_name.' '.$emp_info->last_name;

        $suppl_info = array();
        if($supplier_id!=-1)
        {
            $suppl_info=$this->Supplier->get_info($supplier_id);
            $data['supplier_id'] = $supplier_id;
            $data['supplier']=$suppl_info->company_name;

            # Nợ cũ , nợ đầu
          
            # kiểm tra nếu có dữ liệu là nợ
           
            foreach ($data['payments'] as $value) {
                # nếu có biến nợ đầu và nợ cuối
                if(isset($data['luu_no_dau']) && isset($data['luu_no_cuoi'])){
                     # kiểm tra nếu có dữ liệu là nợ
                    $data['no_dau'] = $data['luu_no_dau'];

                    $data['no_cuoi'] = $data['luu_no_cuoi'];

                    if($value['payment_type'] == 'Sổ ghi nợ'){
                            $data['no_cuoi'] = $data['no_dau'] + $value['amount'];
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
                    if($value['payment_type'] == 'Sổ ghi nợ'){
                        if ($suppl_info->balance !=0) {
                        $data['no_dau']  = abs($suppl_info->balance);
                        $data['luu_no_dau'] = $data['no_dau'];
                        $data['no_cuoi'] = $data['no_dau'] + $data['payments'][0]['amount'];
                        $data['luu_no_cuoi'] = $data['no_cuoi'];
                        } else {
                            $data['no_dau']  = 0;
                            $data['luu_no_dau'] = $data['no_dau'];  # lưu lại biến tạm
                            $data['no_cuoi'] = $value['amount'];
                            $data['luu_no_cuoi'] = $data['no_cuoi']; # lưu lại biến tạm
                        }

                    } else {
                        $data['no_dau']  = abs($suppl_info->balance);
                        $data['luu_no_dau'] = $data['no_dau'];
                        $data['no_cuoi'] = $data['no_dau'];
                        $data['luu_no_cuoi'] = $data['no_cuoi'];
                    }
                }
            }
            
            # Truyền dữ liệu Nợ cũ , nợ đầu
            $data['tong_no_dau'] = $data['no_dau'];
            $data['tong_no_cuoi'] = $data['no_cuoi'];
            if ($suppl_info->first_name || $suppl_info->last_name)
            {
                $data['supplier'] .= ' ('.$suppl_info->first_name.' '.$suppl_info->last_name.')';
            }

            $data['supplier_address_1'] = $suppl_info->address_1;
            $data['supplier_address_2'] = $suppl_info->address_2;
            $data['supplier_city'] = $suppl_info->city;
            $data['supplier_state'] = $suppl_info->state;
            $data['supplier_zip'] = $suppl_info->zip;
            $data['supplier_country'] = $suppl_info->country;
            $data['supplier_phone'] = $suppl_info->phone_number;
            $data['supplier_email'] = $suppl_info->email;
        }

        if ($this->config->item('charge_tax_on_recv'))
        {
            //If we don't have any taxes, run a check for items so we don't show the price including tax on receipt
            if (empty($data['taxes']))
            {
                foreach(array_keys($data['cart']) as $key)
                {
                    if (isset($data['cart'][$key]['item_id']))
                    {
                        $item_info = $this->Item->get_info($data['cart'][$key]['item_id']);
                        if($item_info->tax_included)
                        {
                            $this->load->helper('items');
                            $price_to_use = get_price_for_item_excluding_taxes($data['cart'][$key]['item_id'], $data['cart'][$key]['price']);
                            $data['cart'][$key]['price'] = $price_to_use;
                        }
                    }
                }
            }
        }



        # lấy modul đang sử dụng để hiển thị hóa đơn theo từng view khác nhau
        $receiving_mode = $this->receiving_lib->get_mode();

        if($receiving_mode == 'return') $return = 1; else $return = 0;
        $is_vat = ($receiving_mode == 'vat_order') ? 1 : 0;

        $store_account_payment = 0;
        # Nếu là thanh toán công nợ
        if($receiving_mode == 'store_account_payment') {
            $store_account_payment = $this->receiving_lib->get_store_account_payment_value();
            $data['store_account_payment_value'] = $store_account_payment;
            $data['payment_total'] =  $data['payments'][0]['amount'];
            $data['balance'] = abs($suppl_info->balance - (int)$data['payments'][0]['amount']);

        # Nếu là chuyển kho
        }elseif($receiving_mode == 'transfer') {
            // echo 'Chưa cập nhật tính năng sửa hóa đơn chuyển kho';
            // return;
        }

        $suspended_change_recv_id=$this->receiving_lib->get_suspended_receiving_id() ? $this->receiving_lib->get_suspended_receiving_id() : $this->receiving_lib->get_change_recv_id();


        //lưu dữ liệu receiving to database
        
        $receiving_id_raw = $this->Receiving->save($data, $supplier_id,$employee_id,$comment,$payment_type,$suspended_change_recv_id,0,$data['mode'], $data['change_receiving_date'],0, $location_id, $return, $store_account_payment, $is_vat, $suppl_info);

        if ($receiving_mode == 'transfer')
        {
            $stepCode = 'chuyen_kho';
            $this->ApproverGroup->initApproverStatus($stepCode, $receiving_id_raw);
            
            /*
             * TODO
             * If mode is transfer
             * Check approver_group active or not
             * If not -> set status of receiving -> approved else status is pending
             * */
        }
       
        $this->_prefixDocument = !empty($this->config->item('receive_prefix')) ? $this->config->item('receive_prefix') : $this->_prefixDocument;

        $data['receiving_id']=$this->_prefixDocument.$receiving_id_raw;
        $data['receiving_id_raw']=$receiving_id_raw;
		    $data['employee'] = $this->Employee->get_info($employee_id)->first_name;
        if ($data['receiving_id'] == $this->_prefixDocument . '-1')
        {
            $data['error_message'] = '';
            $data['error_message'] .= '<span class="text-danger">'.lang('receivings_transaction_failed').'</span>';
            $data['error_message'] .= '<br /><br />'.anchor('receivings','&laquo; '.lang('receivings_register'));
            $data['error_message'] .= '<br /><br />'.anchor('receivings/complete',lang('common_try_again'). ' &raquo;');
        }
        else
        {
            if ($this->receiving_lib->get_email_receipt() && !empty($suppl_info->email))
            {
                $this->load->library('email');
                $config['mailtype'] = 'html';
                $this->email->initialize($config);
                $this->email->from($this->Location->get_info_for_key('email') ? $this->Location->get_info_for_key('email') : 'no-reply@mg.4biz.vn', $this->config->item('company'));
                $this->email->to($suppl_info->email);

                $this->email->subject(lang('receivings_receipt'));
                $this->email->message($this->load->view("receivings/receipt_email",$data, true));
                $this->email->send();

            }
        }

        $current_location_id = $this->Employee->get_logged_in_employee_current_location_id();
        $current_location = $this->Location->get_info($current_location_id);
        $data['transfer_from_location'] = $current_location->name;

        if ($location_id > 0)
        {
            $transfer_to_location = $this->Location->get_info($location_id);
            $data['transfer_to_location'] = $transfer_to_location->name;
        }

        if ($data['receiving_id'] != $this->_prefixDocument . '-1')
        {
            $this->receiving_lib->clear_all();
        }

        
         //         $this->_reload();
        #Hiến thị hóa đơn theo từng mode khác nhau
        // $typeOfView = $this->getTypeOfOrder($data['mode']);

        // $data['pdf_block_html'] = $this->load->view('receivings/partials/' . $typeOfView, $data, TRUE);
        // $this->load->view("receivings/receipt",$data);
        $this->session->set_userdata(array('status'=>true));
        redirect(base_url('receivings'),'refresh');
        
    }

    protected function  getTypeOfOrder($mode = '')
    {
        $typeOfView = 'receive';

        if($mode == 'transfer')
        {
            $typeOfView = 'move_inventory';
        }

        if($mode =='return')$typeOfView = 'return';
        if($mode=='purchase_order')$typeOfView = 'purchase_order';

        if($mode == 'store_account_payment')  {

            $typeOfView = 'store_account_payment';
        }
        return $typeOfView;
    }

    function receipt($receiving_id)
    {
        //Before changing the recv session data, we need to save our current state in case they were in the middle of a recv
        $this->receiving_lib->save_current_recv_state();

        $receiving_info = $this->Receiving->get_info($receiving_id)->row_array();

        $this->receiving_lib->copy_entire_receiving($receiving_id, TRUE);
        $data['cart']=$this->receiving_lib->get_cart();
        $data['subtotal']=$this->receiving_lib->get_subtotal($receiving_id);
        $data['taxes']=$this->receiving_lib->get_taxes($receiving_id);
        $data['total']=$this->receiving_lib->get_total($receiving_id);
        $data['mode'] = $this->receiving_lib->get_mode();
        $data['receipt_title']=lang('receivings_receipt');
        $data['transaction_time']= date(get_date_format().' '.get_time_format(), strtotime($receiving_info['receiving_time']));
        $supplier_id=$this->receiving_lib->get_supplier();
        $emp_info=$this->Employee->get_info($receiving_info['employee_id']);
        $data['payments'] = $this->receiving_lib->get_payments();

        # lấy dữ liệu nợ đầu và nợ cuối
        $data['tong_no_dau'] = $receiving_info['no_dau'];
        $data['tong_no_cuoi'] = $receiving_info['no_cuoi'];
        #lấy dữ liệu số tiền thanh toán
        $data['payment_total'] = $data['cart'][1]['price'];

        $data['override_location_id'] = $receiving_info['location_id'];
        $data['suspended'] = $receiving_info['suspended'];
        $data['comment'] = $receiving_info['comment'];
        $data['is_po'] = $receiving_info['is_po'];
        $data['discount_exists'] = $this->_does_discount_exists($data['cart']);


        $data['employee']=$emp_info->first_name.' '.$emp_info->last_name;

        if($supplier_id!=-1)
        {
            $supplier_info=$this->Supplier->get_info($supplier_id);
            $data['supplier']=$supplier_info->company_name;
            if ($supplier_info->first_name || $supplier_info->last_name)
            {
                $data['supplier'] .= ' ('.$supplier_info->first_name.' '.$supplier_info->last_name.')';
            }

            $data['supplier_address_1'] = $supplier_info->address_1;
            $data['supplier_address_2'] = $supplier_info->address_2;
            $data['supplier_city'] = $supplier_info->city;
            $data['supplier_state'] = $supplier_info->state;
            $data['supplier_zip'] = $supplier_info->zip;
            $data['supplier_country'] = $supplier_info->country;
            $data['supplier_phone'] = $supplier_info->phone_number;
            $data['supplier_email'] = $supplier_info->email;
            $data['balance'] = $supplier_info->balance;
            $data['store_account_payment_value'] = $receiving_info['store_account_payment'];

        }
        $this->_prefixDocument = !empty($this->config->item('receive_prefix')) ? $this->config->item('receive_prefix') : $this->_prefixDocument;

        $data['receiving_id']=$this->_prefixDocument.$receiving_id;
        $data['receiving_id_raw']=$receiving_id;

        $current_location = $this->Location->get_info($receiving_info['location_id']);
        $data['transfer_from_location'] = $current_location->name;

        if ($receiving_info['transfer_to_location_id'] > 0)
        {
            $transfer_to_location = $this->Location->get_info($receiving_info['transfer_to_location_id']);
            $data['transfer_to_location'] = $transfer_to_location->name;

            $transfer_from_location = $this->Location->get_info($receiving_info['location_id']);
            $data['transfer_from_location'] = $transfer_from_location->name;

            $data['mode'] = 'transfer';
        }
                if($receiving_info['suspended']>0){
                    if($receiving_info['suspended']==1) $data['mode']= 'purchase_order';
                }

        // [4biz] switch to correct view
        $typeOfView = $this->getTypeOfOrder($data['mode']);
        $data['pdf_block_html'] = $this->load->view('receivings/partials/' . $typeOfView, $data, TRUE);
        $this->load->view("receivings/receipt",$data);
        $this->receiving_lib->clear_all();

        //Restore previous state saved above
        $this->receiving_lib->restore_current_recv_state();
    }

    # Hàm sắp xếp mảng
    function sap_xep_mang(&$array, $subkey="id", $sort_ascending=false) {

        if (count($array))
            $temp_array[key($array)] = array_shift($array);

        foreach($array as $key => $val){
            $offset = 0;
            $found = false;
            foreach($temp_array as $tmp_key => $tmp_val)
            {
                if(!$found and strtolower($val[$subkey]) > strtolower($tmp_val[$subkey]))
                {
                    $temp_array = array_merge(    (array)array_slice($temp_array,0,$offset),
                                                array($key => $val),
                                                array_slice($temp_array,$offset)
                                              );
                    $found = true;
                }
                $offset++;
            }
            if(!$found) $temp_array = array_merge($temp_array, array($key => $val));
        }

        if ($sort_ascending) $array = array_reverse($temp_array);
        else $array = $temp_array;

        return $array;
    }

    function _reload($data=array(), $is_ajax = true)
    {
        
        $receivings_mode = $this->receiving_lib->get_mode();
        $person_info = $this->Employee->get_logged_in_employee_info();
				$total_payment = $this->receiving_lib->get_payments_totals();
        $data['cart']=$this->receiving_lib->get_cart();
        $data['modes']=array('receive'=>lang('receivings_receiving'),'return'=>lang('receivings_return'),'purchase_order'=>lang('receivings_purchase_order'), 'store_account_payment'=>'Thanh toán công nợ');
        if($this->config->item('config_vat_order') == 1)
            $data['modes']['vat_order'] = 'Hóa đơn VAT';

        $data['comment'] = $this->receiving_lib->get_comment();
        if ($this->Location->count_all() > 1)
        {
            $data['modes']['transfer']= lang('receivings_transfer');
        }
        $data['mode']=$this->receiving_lib->get_mode();
        $data['selected_payment'] = $this->receiving_lib->get_selected_payment();
        $data['subtotal']=$this->receiving_lib->get_subtotal();
        $data['taxes']= $this->receiving_lib->get_taxes();
        $data['total']=$this->receiving_lib->get_total();
        $data['items_in_cart'] = $this->receiving_lib->get_items_in_cart();
        $data['change_recv_date_enable'] = $this->receiving_lib->get_change_receiving_date_enable();
        $data['change_receiving_date'] = $this->receiving_lib->get_change_receiving_date();
        $data['email_receipt'] = $this->receiving_lib->get_email_receipt();

        $data['line_for_flat_discount_item'] = $this->receiving_lib->get_line_for_flat_discount_item();
        $data['discount_all_percent'] = $this->receiving_lib->get_discount_all_percent();
        $data['discount_all_fixed'] = $this->receiving_lib->get_discount_all_fixed();


        $totalItems = 0;
        $totalQty = 0;
        foreach ($data['cart'] as $item) {
            $totalQty += $item['quantity'];
            $totalItems ++;
        }

        $data['total_items'] = $totalItems;
        $data['total_qty'] = $totalQty;

        $data['items_module_allowed'] = $this->Employee->has_module_permission('items', $person_info->person_id);
        $data['payment_options']=array(
                lang('common_cash') => lang('common_cash'),
                lang('common_store_account') => lang('common_store_account'),
                lang('common_check') => lang('common_check'),
                lang('common_debit') => lang('common_debit'),
                lang('common_credit') => lang('common_credit')
        );
        $data['fullscreen'] = $this->session->userdata('fullscreen');

        foreach($this->Appconfig->get_additional_payment_types() as $additional_payment_type)
        {
            $data['payment_options'][$additional_payment_type] = $additional_payment_type;
        }
				if($total_payment > $data['total'])
				{
					  $data['payment_options']=array(
                lang('common_refund_from_supplier') => lang('common_refund_from_supplier'),
                lang('common_debt_supplier') => lang('common_debt_supplier')
								);
				}
        $deleted_payment_types = $this->config->item('deleted_payment_types');
        $deleted_payment_types = explode(',',$deleted_payment_types);

        foreach($deleted_payment_types as $deleted_payment_type)
        {
            foreach($data['payment_options'] as $payment_option)
            {
                if ($payment_option == $deleted_payment_type)
                {
                    unset($data['payment_options'][$payment_option]);
                }
            }
        }
		
        $supplier_id=$this->receiving_lib->get_supplier();
        $task_id = $this->receiving_lib->get_task();
        
        if($supplier_id!=-1)
        {
            $info=$this->Supplier->get_info($supplier_id);

            $data['supplierInfo'] = $info;
            // var_dump($data['supplierInfo']);die;

            $data['supplier']=$info->company_name;
         

            $data['supplier_email']=$info->email;
            $data['avatar']=$info->image_id ?  site_url('app_files/view/'.$info->image_id) : base_url()."assets/img/user.png";


            $data['supplier_id']=$supplier_id;
        }


# Xử lý TASK
            if($task_id!=-1)
        {
            $info=$this->Task->get_task_item($task_id);

            $data['taskInfo'] = $info;
            // var_dump($data['supplierInfo']);die;

            $data['task']=$info['name'];

            $data['task_id']=$task_id;
        }


        $location_id=$this->receiving_lib->get_location();
        
        if($location_id!=-1)
        {

            $info=$this->Location->get_info($location_id);
            $data['location']=$info->name;
            $data['location_id']=$location_id;
        }

        $data['is_po'] = $this->receiving_lib->get_po();
        $data['amount_tendered'] = $this->getAmountTendered();
        if($data['amount_tendered'] < 0) $data['amount_tendered'] = 0;
        $data['payments'] = $this->receiving_lib->get_payments();


        $recvId = $this->receiving_lib->get_suspended_receiving_id();
        if(empty($recvId))
        {
            $recvId = $this->receiving_lib->get_change_recv_id();
        }

        if (!empty($recvId)) {
            $recvInfo = $this->Receiving->get_info($recvId)->row();
            $stockInItems = $this->StockIn->getStockInItems($recvId);
            $data['stockInItems'] = $stockInItems;
            $data['isStockIn'] = $recvInfo->is_stock_in;
        }

        $change_recv_id = $this->receiving_lib->get_change_recv_id();
        if($change_recv_id > 0) {
            $data['payment_detail_list'] = $this->Receiving->get_store_supplier_accounts(array('receiving_id'=>$change_recv_id));
        }

        if ($is_ajax)
        {
            # hiển thị khi ajax
            $this->load->view("receivings/receiving",$data);
        }
        else
        {
            $this->load->view("receivings/receiving_initial",$data);
        }
    }

    function discount_all() {
        $post = $this->input->post();

        $post['value'] = convert_number($post['value']);
        $_POST['value'] = convert_number($_POST['value']);

        $this->input->post = $post;

        $discount_all_percent = (float)$this->input->post('discount_all_percent');

        if($this->input->post('name')=="discount_all_percent")
        {
            $discount_all_percent = (float)$this->input->post('value');
            $this->receiving_lib->discount_all($discount_all_percent);
        }
        elseif ($this->input->post('name') == 'discount_all_flat')
        {

           $discount_amount = strpos($this->input->post('value'), '%',0) !== FALSE ? (($this->receiving_lib->get_total() + $this->receiving_lib->get_discount_all_fixed()) * ((float)$this->input->post('value')/100)) : (float)$this->input->post('value');
           $this->receiving_lib->delete_item($this->receiving_lib->get_line_for_flat_discount_item());
           $item_id = $this->Item->create_or_update_flat_discount_item();

           $description =  strpos($this->input->post('value'), '%',0) ?  'Phần trăm giảm giá: '.$this->input->post('value') : '';
           $this->receiving_lib->add_item($item_id,-1,NULL,0,to_currency_no_money($discount_amount),0,$description);
        }

        $this->_reload();
    }

    protected function getAmountTendered() {
        $amount_tendered = $this->receiving_lib->get_total();
        $payments = $this->receiving_lib->get_payments();
				$total_payment = 0;
        if( !empty($payments) ) {
            foreach ($payments as $payment) {
							if($payment['payment_type'] == lang('common_debt_supplier'))
							{
								$total_payment -= $payment['amount'];
							}
							else
							{
								$total_payment += $payment['amount'];
							}
                
            }
        }
				if($amount_tendered > $total_payment)
				{
					$amount_tendered = $amount_tendered - $total_payment;
				}
				else
				{
					$amount_tendered = $total_payment - $amount_tendered;
				}
				

        return $amount_tendered;
    }
	function delete_tax($name)
	{
		$this->check_action_permission('delete_taxes');
		$name = rawurldecode($name);
		$this->receiving_lib->add_deleted_tax($name);
		$this->_reload();
	}
    function modal_list() {
        $data = array();
        $this->load->view('receivings/modal/receiving_list', $data);
    }

    function modal_store() {
        $post  = $this->input->post();
        $arrParam                          = array_merge($post, $this->input->get());
        if(!empty($post)) {
            $_SESSION['receivings_model_filter'] = array();

            $arrParam['paginator']             =  $this->_paginator;
            $arrParam['page']                  =  $this->uri->segment(3, 1);

            $config['base_url'] = base_url() . 'receivings/modal_store';
            $config['total_rows'] = $this->Receiving->count_item($arrParam);


            //$config['per_page'] = $arrParam['paginator']['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
            $config['per_page'] = $arrParam['paginator']['per_page'] = 5;
            $config['uri_segment'] = 3;
            $config['use_page_numbers'] = TRUE;

            $items = $this->Receiving->list_item($arrParam);

            $this->load->library("pagination");
            $this->pagination->initialize($config);
            $this->pagination->createConfig('front-end');

            $pagination = $this->pagination->create_ajax();

            $result = array('count'=> $config['total_rows'], 'items'=>$items, 'pagination'=>$pagination);
            echo json_encode($result);
        }
    }

    function modal_ds() {
        $data = array();
        $this->load->view('receivings/modal/receiving_ds', $data);
    }
    function modal_ds_store() {
        $post  = $this->input->post();
        $arrParam                          = array_merge($post, $this->input->get());
        if(!empty($post)) {
            $_SESSION['receivings_model_filter'] = array();

            $arrParam['paginator']             =  $this->_paginator;
            $arrParam['page']                  =  $this->uri->segment(3, 1);

            $config['base_url'] = base_url() . 'receivings/modal_store';
            $config['total_rows'] = $this->Receiving->count_item($arrParam);

            //$config['per_page'] = $arrParam['paginator']['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
            $config['per_page'] = $arrParam['paginator']['per_page'] = 5;
            $config['uri_segment'] = 3;
            $config['use_page_numbers'] = TRUE;

            $items = $this->Receiving->list_item($arrParam);

            $this->load->library("pagination");
            $this->pagination->initialize($config);
            $this->pagination->createConfig('front-end');

            $pagination = $this->pagination->create_ajax();

            $result = array('count'=> $config['total_rows'], 'items'=>$items, 'pagination'=>$pagination);
            echo json_encode($result);
        }
    }


    function ajax_order() {
        $post = $this->input->post();
        $arrParams = array_merge($post, $this->input->get());
        if(!empty($post)) {
            if($arrParams['type'] == 'receivings') {
                $discount_id         = $this->Item->get_item_id_for_flat_discount_item();
                $data                = $this->receiving_lib->get_receiving($arrParams['receiving_id']);
                $data['discount_id'] = $discount_id;

                $this->load->view("receivings/ajax_order",$data);
            }elseif($arrParams['type'] == 'supplier') {
                $receiving_info = $this->Receiving->getInfo($arrParams['receiving_id']);
                $result = $this->Supplier->get_information($receiving_info['supplier_id']);

                if(!empty($result)) {
                    $supplier_info['fullname']     = $result['first_name'] . ' ' . $result['last_name'];
                    $supplier_info['company_name'] = $result['company_name'];
                    $supplier_info['address']      = $result['address_1'];
                    $supplier_info['phone_number'] = $result['phone_number'];
                    if(empty($supplier_info['address']))
                        $supplier_info['address']  = $result['address_2'];
                }

                echo json_encode($supplier_info);
            }
        }
    }

    function modal_store_payment_store() {
        $post        =  $this->input->post();
//        $post['col'] = 'receiving_time';
//        $post['order'] = 'ASC';
        $arrParam    =  array_merge($post, $this->input->get());

        if(!empty($post)) {
            $_SESSION['receiving_store_payment_modal_filter'] = array();
            $arrParam['paginator']             = $this->_paginator;
            $arrParam['page']                  =  $this->uri->segment(3, 1);

            $supplier_id             = $this->receiving_lib->get_supplier();
            $arrParam['supplier_id'] = $supplier_id;

            $config['base_url'] = base_url() . 'receivings/modal_store_payment_store';
            $config['total_rows'] = $this->Receiving->count_receiving_store_payment($arrParam);

            $config['per_page'] = $arrParam['paginator']['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
            //$config['per_page'] = $arrParam['paginator']['per_page'] = 5;
            $config['uri_segment'] = 3;
            $config['use_page_numbers'] = TRUE;

            $items = $this->Receiving->list_receiving_store_payment($arrParam);

            $html = $this->load->view('receivings/row/receiving_store_payment', array('items'=>$items), true);

            $this->load->library("pagination");
            $this->pagination->initialize($config);
            $this->pagination->createConfig('front-end');

            $pagination = $this->pagination->create_ajax();

            $con_lai = $this->receiving_lib->get_last_amount_from_sale_store_payment();
            $con_lai = to_currency($con_lai);

            $result = array('count'=> $config['total_rows'], 'html_string'=>$html, 'con_lai'=>$con_lai,'pagination'=>$pagination);

            echo json_encode($result);
        }
    }

    function modal_store_payment() {
        $mode = $this->receiving_lib->get_mode();
        $flag_error = false;
        if($mode != 'store_account_payment') {
            $flag_error = true;
            echo 'no-receiving';
        }

        $supplier_id = $this->receiving_lib->get_supplier();
        if($supplier_id > 0)
        {}
        else {
            $flag_error = true;
            echo 'no-supplier';
        }

        if($flag_error == false) {
            $data = array();
            $this->load->view('receivings/modal/receiving_store_payment', $data);
        }
    }

    function modal_vat_order() {
        $mode = $this->receiving_lib->get_mode();
        $flag_error = false;
        if($mode != 'vat_order') {
            $flag_error = true;
            echo 'no-receiving';
        }

        $supplier_id = $this->receiving_lib->get_supplier();
        if($supplier_id > 0)
        {}
        else {
            $flag_error = true;
            echo 'no-supplier';
        }

        if($flag_error == false) {
            $data = array();
            $this->load->view('receivings/modal/receiving_vat_order', $data);
        }
    }

    function modal_vat_order_store() {
        $post        =  $this->input->post();
//        $post['col'] = 'sale_id';
//        $post['order'] = 'asc';
        $arrParam    =  array_merge($post, $this->input->get());

        if(!empty($post)) {
            $_SESSION['receiving_vat_order_modal_filter'] = array();
            $arrParam['paginator']             = $this->_paginator;
            $arrParam['page']                  =  $this->uri->segment(3, 1);

            $supplier_id = $this->receiving_lib->get_supplier();
            $arrParam['supplier_id'] = $supplier_id;

            $location_id     = $this->Employee->get_logged_in_employee_current_location_id();

            // create sale tmp
            $where = "r.location_id = $location_id AND r.supplier_id = $supplier_id AND r.deleted = 0";
            $this->Receiving->create_receivings_items_temp_table_n9(array('where'=>$where));

            $config['base_url'] = base_url() . 'receivings/modal_vat_order_store';
            $config['total_rows'] = $this->Receiving->count_item($arrParam, array('task'=>'vat_order'));

            $config['per_page'] = $arrParam['paginator']['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
            //$config['per_page'] = $arrParam['paginator']['per_page'] = 5;
            $config['uri_segment'] = 3;
            $config['use_page_numbers'] = TRUE;

            $items = $this->Receiving->list_item($arrParam, array('task'=>'vat_order'));
            $html = $this->load->view('sales/row/sale_vat_order', array('items'=>$items), true);

            $this->load->library("pagination");
            $this->pagination->initialize($config);
            $this->pagination->createConfig('front-end');

            $pagination = $this->pagination->create_ajax();

            $result = array('count'=> $config['total_rows'], 'html_string'=>$html, 'pagination'=>$pagination);
            echo json_encode($result);
        }
    }

    function update_payment_store() {
        $post = $this->input->post();
        if(!empty($post)) {
            $receiving_id = $post['pk'];
            $amount = (float)convert_number($post['value']);

            $mode = $this->receiving_lib->get_mode();
            if($mode != 'store_account_payment') {
                $response = array('flag'=>'false', 'msg'=>'Bạn đang ở chế độ nhập hàng khác.');
                echo json_encode($response);

                return;
            }

            if($amount < 0) {
                $response = array('flag'=>'false', 'msg'=>'Giá trị không được âm.');
                echo json_encode($response);

                return;
            }

            $this->receiving_lib->update_receiving_store_payment($receiving_id, $amount);

            $response = array('flag'=>'true');
            echo json_encode($response);
        }
    }

    function set_store_account_payment_value() {
        $post = $this->input->post();
        if(!empty($post)) {
            $this->receiving_lib->clear_receiving_store_payment();
            $this->receiving_lib->clear_payments();
            $this->receiving_lib->set_store_account_payment_value($post['value']);
        }
    }



    #                                                                                                              #
    #                                                                                                              #
    #                                          RECEIVINGS  BY   D13                                                #
    #                                                                                                              #
    #                                                                                                              #
    #                                                                                                              #
		

    function index($test=0)
    {
        
        // var_dump($this->Employee->get_employee_active());
        // echo $this->db->last_query();
        // die();
        $this->check_action_permission('add_receiving');
        $this->_reload(array(), false);
    }




    function select_task()
    {
          
        $data = array();
        $task_id = $this->input->post("task");
        // var_dump($task_id); ;die();
        if ($this->Task->checkItemExist($task_id))
        {
           $this->receiving_lib->set_task($task_id);
        }
        else
        {
            $data['error']=lang('receivings_unable_to_add_task');
        }
        $this->_reload($data);
    }


    function delete_task()
    {
        $this->receiving_lib->delete_task();
        $this->_reload();
    }



    

    function reload_d13($data=array(), $is_ajax = true)
    {
       #Phương thức giao dịch
        $receivings_mode = $this->receiving_lib->get_mode();

        #Lấy thông tin nhân viên
        $person_info = $this->Employee->get_logged_in_employee_info();

        #Lấy tổng tiền giao dịch
        $total_payment = $this->receiving_lib->get_payments_totals();

        #Lấy thông tin giỏ hàng
        $data['cart']=$this->receiving_lib->get_cart();

        $data['modes']=array('receive'=>lang('receivings_receiving'),'return'=>lang('receivings_return'),'purchase_order'=>lang('receivings_purchase_order'), 'store_account_payment'=>'Thanh toán công nợ');
        if($this->config->item('config_vat_order') == 1)
            $data['modes']['vat_order'] = 'Hóa đơn VAT';

        $data['comment'] = $this->receiving_lib->get_comment();
        if ($this->Location->count_all() > 1)
        {
            $data['modes']['transfer']= lang('receivings_transfer');
        }
        $data['mode']=$this->receiving_lib->get_mode();
        $data['selected_payment'] = $this->receiving_lib->get_selected_payment();
        $data['subtotal']=$this->receiving_lib->get_subtotal();
        $data['taxes']= $this->receiving_lib->get_taxes();
        $data['total']=$this->receiving_lib->get_total();
        $data['items_in_cart'] = $this->receiving_lib->get_items_in_cart();
        $data['change_recv_date_enable'] = $this->receiving_lib->get_change_receiving_date_enable();
        $data['change_receiving_date'] = $this->receiving_lib->get_change_receiving_date();
        $data['email_receipt'] = $this->receiving_lib->get_email_receipt();

        $data['line_for_flat_discount_item'] = $this->receiving_lib->get_line_for_flat_discount_item();
        $data['discount_all_percent'] = $this->receiving_lib->get_discount_all_percent();
        $data['discount_all_fixed'] = $this->receiving_lib->get_discount_all_fixed();


        $totalItems = 0;
        $totalQty = 0;
        foreach ($data['cart'] as $item) {
            $totalQty += $item['quantity'];
            $totalItems ++;
        }

        $data['total_items'] = $totalItems;
        $data['total_qty'] = $totalQty;

        $data['items_module_allowed'] = $this->Employee->has_module_permission('items', $person_info->person_id);
        $data['payment_options']=array(
                lang('common_cash') => lang('common_cash'),
                lang('common_store_account') => lang('common_store_account'),
                lang('common_check') => lang('common_check'),
                lang('common_debit') => lang('common_debit'),
                lang('common_credit') => lang('common_credit')
        );
        $data['fullscreen'] = $this->session->userdata('fullscreen');

        foreach($this->Appconfig->get_additional_payment_types() as $additional_payment_type)
        {
            $data['payment_options'][$additional_payment_type] = $additional_payment_type;
        }
                if($total_payment > $data['total'])
                {
                      $data['payment_options']=array(
                lang('common_refund_from_supplier') => lang('common_refund_from_supplier'),
                lang('common_debt_supplier') => lang('common_debt_supplier')
                                );
                }
        $deleted_payment_types = $this->config->item('deleted_payment_types');
        $deleted_payment_types = explode(',',$deleted_payment_types);

        foreach($deleted_payment_types as $deleted_payment_type)
        {
            foreach($data['payment_options'] as $payment_option)
            {
                if ($payment_option == $deleted_payment_type)
                {
                    unset($data['payment_options'][$payment_option]);
                }
            }
        }
        
        $supplier_id=$this->receiving_lib->get_supplier();
        // var_dump($supplier_id);die;
        if($supplier_id!=-1)
        {
            $info=$this->Supplier->get_info($supplier_id);

            $data['supplierInfo'] = $info;
            // var_dump($data['supplierInfo']);die;

            $data['supplier']=$info->company_name;
            if ($info->first_name || $info->last_name)
            {
                $data['supplier'] .= ' ('.$info->first_name.' '.$info->last_name.')';
            }

            $data['supplier_email']=$info->email;
            $data['avatar']=$info->image_id ?  site_url('app_files/view/'.$info->image_id) : base_url()."assets/img/user.png";


            $data['supplier_id']=$supplier_id;
        }

        $location_id=$this->receiving_lib->get_location();
        
        if($location_id!=-1)
        {

            $info=$this->Location->get_info($location_id);
            $data['location']=$info->name;
            $data['location_id']=$location_id;
        }

        $data['is_po'] = $this->receiving_lib->get_po();
        $data['amount_tendered'] = $this->getAmountTendered();
        if($data['amount_tendered'] < 0) $data['amount_tendered'] = 0;
        $data['payments'] = $this->receiving_lib->get_payments();


        $recvId = $this->receiving_lib->get_suspended_receiving_id();
        if(empty($recvId))
        {
            $recvId = $this->receiving_lib->get_change_recv_id();
        }

        if (!empty($recvId)) {
            $recvInfo = $this->Receiving->get_info($recvId)->row();
            $stockInItems = $this->StockIn->getStockInItems($recvId);
            $data['stockInItems'] = $stockInItems;
            $data['isStockIn'] = $recvInfo->is_stock_in;
        }

        $change_recv_id = $this->receiving_lib->get_change_recv_id();
        if($change_recv_id > 0) {
            $data['payment_detail_list'] = $this->Receiving->get_store_supplier_accounts(array('receiving_id'=>$change_recv_id));
        }

        if ($is_ajax)
        {
            # hiển thị khi ajax
            $this->load->view("receivings/receiving",$data);
        }
        else
        {
            $this->load->view("receivings/receiving_initial",$data);
        }
    }


function list_receiving(){
    $list = $this->Receiving->get_list();
    $lr =array();
    foreach ($list as $key => $value) {
       $lr[$value['receiving_id']]['id'] = $value['receiving_id'];
       $lr[$value['receiving_id']]['task_name'] = $value['task_name'];
       $lr[$value['receiving_id']]['company_name'] = $value['company_name'];
       $lr[$value['receiving_id']]['receiving_time'] = $value['receiving_time'];
       $lr[$value['receiving_id']]['item_id'][] =$value['item_id'];
       $lr[$value['receiving_id']]['item_unit_price'][] = $value['item_unit_price'];
       $lr[$value['receiving_id']]['name'][] = $value['name'];
       $lr[$value['receiving_id']]['line'][] = $value['line'];
    }
    // echo "<pre>";
    // var_dump($lr);die();
    $data['list'] = $lr;
    $this->load->view('receivings/list', $data);
}

function get_info($id){
    $result = $this->Receiving->get_list($id);
    // foreach ($result as $key => &$value) {
         // $value['item_unit_price'] = number_format($value['item_unit_price']);
    // }
    echo json_encode($result);
}

function edit_r(){
   if($this->input->post('id')){
        $id = $this->input->post('id');
        $line = $this->input->post('line');
        $data =array();
        $data['receiving_id'] = $id;
        $data['line'] = $line;
        $data['item_unit_price'] = str_replace(",","",$this->input->post('price'));
        $this->db->where('receiving_id',$id);
        $this->db->where('line', $line);
        $this->db->update('phppos_receivings_items', $data);
        echo json_encode(array('flag'=>true));
   }
}

function test(){
    echo "<pre>";
    var_dump($_SERVER);
}
}
?>