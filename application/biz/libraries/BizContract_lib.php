<?php
    require_once(BIZ_LIB_PATH . 'simple-html-dom/simple_html_dom.php');
    class BizContract_lib {
        var $CI;
        
        function __construct()
        {
            $this->CI =& get_instance();
            $this->CI->load->model('Contract');
            $this->CI->load->model('Customer');
            $this->CI->load->model('Sale');
            $this->CI->load->model('Item');
            $this->CI->load->model('Location');
            $this->CI->load->library('sale_lib');
            $this->CI->load->library('receiving_lib');
        }
        
        function convertTemplate($contract_id, $content_string) {
            $html = str_get_html($content_string);
            $contract_info = $this->CI->Contract->get_item(array('id'=>$contract_id));
            if(empty($contract_info))
            return;
            
            if($contract_info['option'] == 'customer') {
                $order_info = $this->CI->sale_lib->getSale($contract_info['sale_id'], $this->CI->config);
                }elseif($contract_info['option'] == 'supplier') {
                $order_info = $this->CI->receiving_lib->get_receiving($contract_info['receiving_id']);
            }
            // echo "<pre>"; print_r($order_info); die();
            if($contract_info['type'] == 'parttime') {
                $cart = $order_info['cart'];
                $tableData  = $html->find('table.DATA_TABLE', 0);
                if(!empty($tableData)) {
                    $tr_element = $tableData->find('tr');
                    $tong_donhang = $order_info['total'];
                    $total_discount = 0;
                    $total = 0;
                    $total_all = 0;
                    
                    if(!empty($tr_element)) {
                        foreach($tr_element as $key => $element) {
                            $original_str = $element->outertext;
                            $description = $tr_element[$key+1]->outertext;
                            
                            if (strpos($original_str, '{STT}') !== false || strpos($original_str, '{TEN_HH}') !== false || strpos($original_str, '{MA_HH}') !== false) {
                                $new_tr = array();
                                $i=1;
                                foreach($cart as $key_ => $item) {
                                    if ($item['name'] != 'Giảm giá') {
                                        $name      = $item['name'];
                                        $price     = to_currency($item['price']);
                                        $quantity  = to_quantity($item['quantity']);
                                        $measure   = $item['measure'];
                                        $total     = $item['price'] * $item['quantity'];
                                        $discount  = to_quantity($item['discount']);
                                        $total     = $total - $item['discount']*$total/100;
                                        $total_all = $total_all + $total;
                                        $total     = to_currency($total);
                                        
                                        $discount_price = ($item['price'] * $item['discount']) / 100;
                                        $dg_ck = $item['price'] - $discount_price;
                                        
                                        $pattern = $original_str;
                                        $pattern = str_replace("{STT}",$i,$pattern);
                                        $pattern = str_replace("{CHIET_KHAU}",$discount,$pattern);
                                        $pattern = str_replace("{THUE}",to_quantity($item['tax_included']),$pattern);
                                        $pattern = str_replace("{TEN_HH}",$name,$pattern);
                                        $pattern = str_replace("{MA_HH}",$item['product_id'],$pattern);
                                        $pattern = str_replace("{DVT}",$measure,$pattern);
                                        $pattern = str_replace("{SL}", $quantity,$pattern);
                                        $pattern = str_replace("{DG-CK}",to_currency($dg_ck),$pattern);
                                        $pattern = str_replace("{DON_GIA}",$price,$pattern);
                                        $pattern = str_replace("{THANH_TIEN}",$total,$pattern);
                                        
                                        if(strpos($description, '{MO_TA_HH}') == TRUE){
                                            if (!empty($item['description'])) {
                                                $description_ = str_replace("{MO_TA_HH}",$item['description'],$description);
                                                } else {
                                                $description_ ='';
                                            } 
                                        }

                                        $new_tr[] = $pattern.$description_;
                                        $i++;
                                        
                                    } else {
                                        $total_discount     = $item['price'] * $item['quantity'];
                                        $total_discount     = to_currency($total_discount);  
                                    }
                                }
                                
                                $new_tr = implode('', $new_tr);
                                $element->outertext = $new_tr;
                                $tr_element[$key+1]->outertext = '';
                                break;
                            }
                        }
                    }
                }
            }
            
            
            // contract payment
            $contract_payment_list_tmp = $this->CI->Contract->list_contract_payment(array('contract_id'=>$contract_id, 'order_info'=>$order_info), array('task'=>'list-all'));
            $contract_payment_list = array();
            if(!empty($contract_payment_list_tmp)) {
                foreach($contract_payment_list_tmp as $key => $val)
                $contract_payment_list[$key+1] = $val; 
            }
            
            $tbl_payment_list  = $html->find('table.DATA_GD');
            if(!empty($tbl_payment_list)) {
                foreach($tbl_payment_list as $tbl_payment) {
                    $array_class = explode(' ', $tbl_payment->class);
                    $number_class = array();
                    foreach($array_class as $class_item){
                        if(is_numeric($class_item))
                        $number_class[] = $class_item;
                    } 
                    
                    $tr_element = $tbl_payment->find('tr');
                    if(!empty($tr_element)) {
                        foreach($tr_element as $element) {
                            $original_str = $element->outertext;
                            if (strpos($original_str, '{STT}') !== false || strpos($original_str, '{TEN_GD}') !== false || strpos($original_str, '{SO_TIEN}') !== false) {
                                $new_tr = array();
                                if(!empty($contract_payment_list)) {
                                    $i = 1;
                                    foreach($contract_payment_list as $key => $payment_item) {
                                        $name                = $payment_item['name'];
                                        $price               = $payment_item['price'];
                                        $date_payment_format = $payment_item['date_payment_format'];
                                        if($payment_item['vat'] == 'unpublished')
                                        $vat_str = 'Chưa xuất';
                                        elseif($payment_item['vat'] == 'published')
                                        $vat_str = 'Đã xuất';
                                        
                                        if(count($number_class)>0) {
                                            if(in_array($key, $number_class)) {
                                                $pattern = $original_str;
                                                
                                                $pattern = str_replace("{STT}",$i,$pattern);
                                                $pattern = str_replace("{TEN_GD}",$name,$pattern);
                                                $pattern = str_replace("{NGAY_TT_GD}",$date_payment_format,$pattern);
                                                $pattern = str_replace("{SO_TIEN_GD}",$price,$pattern);
                                                $pattern = str_replace("{VAT_GD}",$vat_str,$pattern);
                                                
                                                $new_tr[] = $pattern;
                                                $i++;
                                            }
                                            }else {
                                            $pattern = $original_str;
                                            
                                            $pattern = str_replace("{STT}",$i,$pattern);
                                            $pattern = str_replace("{TEN_GD}",$name,$pattern);
                                            $pattern = str_replace("{NGAY_TT_GD}",$date_payment_format,$pattern);
                                            $pattern = str_replace("{SO_TIEN_GD}",$price,$pattern);
                                            $pattern = str_replace("{VAT_GD}",$vat_str,$pattern);
                                            
                                            $new_tr[] = $pattern;
                                            $i++;
                                        }
                                    }
                                    
                                    $new_tr = implode('', $new_tr);
                                    $element->outertext = $new_tr;
                                    break;
                                    }else {
                                    $pattern = $original_str;
                                    
                                    $pattern = str_replace("{STT}",'',$pattern);
                                    $pattern = str_replace("{TEN_GD}",'',$pattern);
                                    $pattern = str_replace("{NGAY_TT_GD}",'',$pattern);
                                    $pattern = str_replace("{SO_TIEN_GD}",'',$pattern);
                                    $pattern = str_replace("{VAT_GD}",'',$pattern);
                                    
                                    $new_tr[] = $pattern;
                                    
                                    $new_tr = implode('', $new_tr);
                                    $element->outertext = $new_tr;
                                    break;
                                }
                                
                            }
                            
                        }
                    }
                }
            }
            
            // contract delivery
            if($contract_info['type'] == 'parttime') {
                $contract_delivery_list = $this->CI->Contract->list_contract_delivery(array('contract_id'=>$contract_id), array('task'=>'all'));
                $tbl_delivery_list  = $html->find('table.DATA_GH');
                if(!empty($tbl_delivery_list)) {
                    foreach($tbl_delivery_list as $tbl_delivery) {
                        $tr_element = $tbl_delivery->find('tr');
                        if(!empty($tr_element)) {
                            foreach($tr_element as $element) {
                                $original_str = $element->outertext;
                                if (strpos($original_str, '{STT}') !== false || strpos($original_str, '{TEN_GD}') !== false || strpos($original_str, '{TG_GH}') !== false || strpos($original_str, '{CT_GH}') !== false) {
                                    $new_tr = array();
                                    if(!empty($contract_delivery_list)) {
                                        foreach($contract_delivery_list as $key => $delivery_item) {
                                            $date         = $delivery_item['date_format'];
                                            $company_name = $delivery_item['company_name'];
                                            $address      = $delivery_item['address'];
                                            $payment_name = $delivery_item['payment_name'];
                                            
                                            $pattern = $original_str;
                                            
                                            $pattern = str_replace("{STT}",$key + 1,$pattern);
                                            $pattern = str_replace("{TEN_GD}",$payment_name,$pattern);
                                            $pattern = str_replace("{CT_GH}",$company_name,$pattern);
                                            $pattern = str_replace("{DD_GH}",$address,$pattern);
                                            $pattern = str_replace("{TG_GH}",$date,$pattern);
                                            
                                            $new_tr[] = $pattern;
                                        }
                                        }else {
                                        $pattern = $original_str;
                                        
                                        $pattern = str_replace("{STT}",'',$pattern);
                                        $pattern = str_replace("{TEN_GD}",'',$pattern);
                                        $pattern = str_replace("{CT_GH}",'',$pattern);
                                        $pattern = str_replace("{DD_GH}",'',$pattern);
                                        $pattern = str_replace("{TG_GH}",'',$pattern);
                                        
                                        $new_tr[] = $pattern;
                                    }
                                    
                                    $new_tr = implode('', $new_tr);
                                    $element->outertext = $new_tr;
                                    break;
                                }
                            }
                        }
                    }
                    
                }
            }
            
            $html_string = $html->outertext;
            //tổng đơn hàng
            $tong_donhang = $order_info['total'];
            // tổng tiền
            $tong_tien = to_currency($total_all);
            //tổng giá trị đơn hàng
            $tong_dh = to_currency($tong_donhang);
            //tiền bằng chữ
            $bang_chu = getStringNumberComma(cutComma($tong_dh));
            
            
            // tiền đã thanh toán
            $payment_total = 0;
            if(!empty($order_info['payments'])) {
                foreach($order_info['payments'] as $payment)
                $payment_total = $payment_total + $payment['payment_amount'];
            }
            
            $payment_total = to_currency($payment_total);
            
            // tiền trả lại
            $amount_change = to_currency(abs($order_info['amount_change']));
            
            // VAT
            $vat = 0;
            if(!empty($order_info['taxes'])) {
                foreach($order_info['taxes'] as $key => $val){
                    if (strpos($key, 'VAT') !== false) {
                        $vat = $vat + $val;
                    }
                }
            }
            
            $vat = to_currency($vat);
            
            // ngày - tháng - năm
            $day   = date('d');
            $month = date('m');
            $year  = date('Y');
            
            //thông tin khách hàng
            if($contract_info['option'] == 'customer') {
                //thông tin khách hàng
                $customer_info = array(
                'TEN_KH' 	   => $order_info['customer'],
                'CT_KH' 	   => $order_info['customer_company_name'],
                'DIA_CHI_1_KH' => $order_info['customer_address_1'],
                'DIA_CHI_2_KH' => $order_info['customer_address_2'],
                'SDT_KH' 	   => $order_info['customer_phone'],
                'CHUCVU_KH'    => $order_info['customer_position'],
                'TKNH_KH' 	   => $order_info['customer_account_number'],
                'EMAIL_KH'	   => $order_info['customer_email'],	
                );
                }elseif($contract_info['option'] == 'supplier') {
                $supplier_info = array(
                'TEN_NCC' 	    => $order_info['supplier'],
                'DIA_CHI_1_NCC' => $order_info['supplier_address_1'],
                'DIA_CHI_2_NCC' => $order_info['supplier_address_2'],
                'SDT_NCC' 	    => $order_info['supplier_phone'],
                );
                
            }
            
            // kho - công ty
            if($contract_info['option'] == 'customer') {
                $sale_emp_info = $order_info['sale_emp_info'];
                
                $sale_emp_name = $sale_emp_info->first_name;
                if(!empty($sale_emp_info->last_name))
                $sale_emp_name = $sale_emp_name . ' ' . $sale_emp_info->last_name;
            }
            
            $localtion_info = array(
            'LOGO' 				   => '<img src="'.$this->CI->Appconfig->get_logo_image().'" />',
            'NAME_COMPANY' 		   => $this->CI->config->item('company'),
            'ADDRESS_COMPANY' 	   => nl2br($this->CI->Location->get_info_for_key('address')),
            'EMAIL_COMPANY' 	   => $this->CI->Location->get_info_for_key('email'),
            'TEL_COMPANY' 		   => nl2br($this->CI->Location->get_info_for_key('phone')),
            'FAX_COMPANY' 		   => $this->CI->Location->get_info_for_key('fax'),
            'WEBSITE_COMPANY' 	   => $this->CI->config->item('website'),
            'SALE_OFFICE_COMPANY'  => $this->CI->Location->get_info_for_key('sale_office'),
            'ACCOUNT_BANK_COMPANY' => $this->CI->Location->get_info_for_key('account_bank'),
            'SALE_EMP_NAME'		   => $sale_emp_name,
            'SALE_EMP_PHONE'	   => $sale_emp_info->phone_number,
            'SALE_EMP_EMAIL'	   => $sale_emp_info->email,
            );
            
            // merge thông tin
            if($contract_info['option'] == 'customer')
            $info_merge = array_merge($localtion_info, $customer_info);
            elseif($contract_info['option'] == 'supplier')
            $info_merge = array_merge($localtion_info, $supplier_info);
            
            // uppser case
            $info_upper = array();
            foreach($info_merge as $key => $val)
            $info_merge[$key . '_U'] = $val = mb_strtoupper($val, 'UTF-8');
            
            
            foreach($info_merge as $key => $val) {
                $html_string = str_replace('{'.$key.'}',$val,$html_string);
            }
            
            if($contract_info['option'] == 'customer')
            $html_string = str_replace("{ORDER_CODE}",$order_info['sale_id'],$html_string);
            elseif($contract_info['option'] == 'supplier')
            $html_string = str_replace("{ORDER_CODE}",$order_info['supplier_id'],$html_string);
            
            $html_string = str_replace("{TONG_TIEN}",$tong_tien,$html_string);
            $html_string = str_replace("{TONG_DH}",$tong_dh,$html_string);
            $html_string = str_replace("{TIEN_DA_THANH_TOAN}",$payment_total,$html_string);
            $html_string = str_replace("{TIEN_TRA_LAI}",$amount_change,$html_string);
            $html_string = str_replace("{VAT}",$vat,$html_string);
            $html_string = str_replace("{DATE}",$day,$html_string);
            $html_string = str_replace("{MONTH}",$month,$html_string);
            $html_string = str_replace("{YEAR}",$year,$html_string);
            
            $html_string = str_replace("{TEN_HD}",$contract_info['name'],$html_string);
            $html_string = str_replace("{MA_HD}",$contract_info['code'],$html_string);
            $html_string = str_replace("{NGAY_BĐ_HD}",$contract_info['date_start'],$html_string);
            $html_string = str_replace("{NGAY_KY_HD}",$contract_info['date_signing'],$html_string);
            $html_string = str_replace("{NGAY_HET_HD}",$contract_info['date_expiration'],$html_string);
            
            $html_string = str_replace("{BANGCHU}",$bang_chu,$html_string);
            $html_string = str_replace("{HD_BANG_CHU}",$bang_chu,$html_string);
            $html_string = str_replace("{HD_BANG_SO}",$tong_dh,$html_string);
            $html_string = str_replace("{GIAM_GIA}",$total_discount,$html_string);
            
            //---------------------------------------------------------------------
            // Add extra people to a invoice(for VISA only)
            // 
            // Created by LK
            //---------------------------------------------------------------------
            $more_customers_in_service        = array();
            $more_customers_in_service_string ='';
            $more_customers_in_service_body   ='';
            
            // starting calculate font size and font family for whole table 
            
            $substr_contain_font              = substr($content_string,strpos($content_string,'{MORE_CUSTOMER_IN_SERVICE}')-110,300);
            $start_get_font                   = strpos($substr_contain_font ,'span')-1;
            $length                           = strpos($substr_contain_font ,'{MORE_CUSTOMER_IN_SERVICE}')- strpos($substr_contain_font ,'span')+1;
            $font                             = substr($substr_contain_font,$start_get_font,$length);
            $i =1;
            // set default font if font size or font-family not set
            if(!strpos($font,'font-family')&&!strpos($font,'font-size'))
            {
                $font = '<span>';
            }
            // end calculate font size and font family for whole table 
            
            foreach($order_info['more_customers_in_service'] as $customer)
            {
                $more_customers_in_service['name'][]= ($customer['sex'] == 2)?$i.'. '.lang('common_female').': '.$customer['last_name'].' '.$customer['first_name']:$i.'. '.lang('common_male').': '.$customer['last_name'].' '.$customer['first_name'];
                $more_customers_in_service['passport'][]=lang('common_passport').': '.$customer['passport'];
                $i++;
            }
			
            foreach($more_customers_in_service['name'] as $key=>$name)
            {
                $more_customers_in_service_body.='<tr><td>'.$font.$name.'</span></td>
                <td>'.$font.$more_customers_in_service['passport'][$key].'</span></td></tr>';
            }
            
			$more_customers_in_service_string .= '<table cellpadding="1" cellspacing="1" style="width:500px;">
			<tbody>
			'.$more_customers_in_service_body.'
            <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            </tr>
			</tbody>
            </table>
            
            <p>&nbsp;</p>';
            ;
            
            
            
            $html_string = str_replace("{MORE_CUSTOMER_IN_SERVICE}",$more_customers_in_service_string,$html_string);
            //-------------------------------------------------------------------------
            // End extra people to a invoice(for VISA only)
            //----------------------------------------------------------------------------
            return $html_string;
        }
    }    