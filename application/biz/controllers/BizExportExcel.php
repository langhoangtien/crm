<?php
require_once (APPPATH . "controllers/Secure_area.php");

class BizExportExcel extends Secure_area
{
    const CATEGORY_VAI_PHU =  175;
    const CATEGORY_VAI_CHINH = 174;
    const CATEGORY_REM_CUON = 179;
    
    function __construct()
    {
        parent::__construct();
        $this->load->helper('bizexcel');
    }

    public function orderBookExportExcel($sale_id)
    {
        $sale_info = $this->Sale->get_sale_info($sale_id);
        $this->sale_lib->clear_all();
        
        # Lấy dữ liệu cho hóa đơn
        $this->sale_lib->copy_entire_sale($sale_id, true);
        $cart = $this->sale_lib->get_cart();
        $customer_id=$this->sale_lib->get_customer();
        if($customer_id!=-1) {
            $cust_info=$this->Customer->get_info($customer_id);
            $data['customer']=$cust_info->first_name.' '.$cust_info->last_name.($cust_info->company_name==''  ? '' :' - '.$cust_info->company_name).($cust_info->phone_number==''  ? '' :' - '.$cust_info->phone_number);
            $data['customer_address_1'] = $cust_info->address_1;
            $data['customer_address_2'] = $cust_info->address_2;
            $data['customer_city'] = $cust_info->city;
            $data['customer_state'] = $cust_info->state;
            $data['customer_zip'] = $cust_info->zip;
            $data['customer_country'] = $cust_info->country;
            $data['customer_phone'] = $cust_info->phone_number;
            $data['customer_email'] = $cust_info->email;
            $data['customer_points'] = $cust_info->points;
            $data['sales_until_discount'] = $this->config->item('number_of_sales_for_discount') - $cust_info->current_sales_for_discount;
            
            if ($cust_info->balance !=0)
            {
                $data['customer_balance_for_sale'] = !empty($data['payments'][0]['no_dau'])?$data['payments'][0]['no_dau']:0;
            }
        }
        $dataExcel = [];
        if ($sale_info['suspended'] == 1) {
            if($this->config->item('company_user') == 'remHaMy') {
                foreach ($cart as $line => &$item) {
                    foreach ($cart as $line1 => $item1) {
                        if ($item['item_id'] == $item1['item_id'] && $line1 != $line) {
                            $item['quantity'] += $item1['quantity'];
                            unset($cart[$line1]);
                        }
                    }
                    $dataExcel[] = $cart[$line];
                }
            }
        }
		$companyName = $this->config->item('company');
		$companyAddress = nl2br($this->Location->get_info_for_key('address', isset($override_location_id) ? $override_location_id : FALSE));
		$companyPhone   = $this->Location->get_info_for_key('phone', isset($override_location_id) ? $override_location_id : FALSE);
		$companyWebsite = $this->config->item('website');
        
        $header_of_multicol[] = array('mergeStartCol' =>'A1','mergeEndCol'=>'G1','text' =>$companyName,'styles'=>array('bold' =>true,'font'=>true, 'font_size'=>15));
        $header_of_multicol[] = array('mergeStartCol' =>'A2','mergeEndCol'=>'G3','text' =>$companyAddress,'styles'=>array('bold' =>false,'font'=>true, 'font_size'=>12));
        $header_of_multicol[] = array('mergeStartCol' =>'A4','mergeEndCol'=>'G4','text' =>'Điện thoại: '.$companyPhone,'styles'=>array('bold' =>false,'font'=>true, 'font_size'=>12));
        $header_of_multicol[] = array('mergeStartCol' =>'A5','mergeEndCol'=>'G5','text' =>'Website: '.$companyWebsite,'styles'=>array('bold' =>false,'font'=>true, 'font_size'=>12));
        $header_of_multicol[] = array('mergeStartCol' =>'A8','mergeEndCol'=>'G8','text' =>'Ngày :'.date('d-m-Y', strtotime($sale_info['sale_time'])),'styles'=>array('bold' =>false,'font'=>true, 'font_size'=>12));
        $header_of_multicol[] = array('mergeStartCol' =>'A9','mergeEndCol'=>'C9','text' =>$customer,'styles'=>array('bold' =>false,'font'=>true, 'font_size'=>12));
        $header_of_multicol[] = array('mergeStartCol' =>'A10','mergeEndCol'=>'C9','text' =>'','styles'=>array('bold' =>false,'font'=>true, 'font_size'=>12));
        
        $fieldDataOfBody = [
            ['col'=>'A', 'value_field' => '__AUTO__'], 
            ['col'=>'B', 'value_field' => 'item_number'],
            ['col'=>'C', 'value_field' => 'name'],
            ['col'=>'D', 'value_field' => 'supplier_name'],
            ['col'=>'E', 'value_field' => 'tag_name'],
            ['col'=>'F', 'value_field' => 'quantity'],
            ['col'=>'G', 'value_field' => 'quantity']
        ];
        $bizExcel = new BizExcel('order_booked_remHaMy.xlsx');
        $bizExcel->setHeaderOfMultiCol($header_of_multicol);
        $bizExcel->setNumberRowStartBody(11)->setHeaderOfBody($fieldDataOfBody);
        $bizExcel->setDataExcel($dataExcel);
        $excelContent = $bizExcel->generateFile(false);
        $this->load->helper('download');
        force_download( 'Hóa đơn đặt hàng'.$sale_info['sale_time'].'.xlsx', $excelContent);
        exit;
    }
    
    public function orderShowPriceExcel($sale_id, $type = "default")
    {
        $sale_info = $this->Sale->get_sale_info($sale_id);
        $this->sale_lib->clear_all();
        
        # Lấy dữ liệu cho hóa đơn
        $this->sale_lib->copy_entire_sale($sale_id, true);
        $cart = $this->sale_lib->get_cart();
        $customer_id=$this->sale_lib->get_customer();
        $itemsContainsLine = $this->sale_lib->getItemContainsLine();
        if($customer_id!=-1) {
            $cust_info=$this->Customer->get_info($customer_id);
            $customer = $cust_info->first_name.' '.$cust_info->last_name.($cust_info->company_name==''  ? '' :' - '.$cust_info->company_name).($cust_info->phone_number==''  ? '' :' - '.$cust_info->phone_number);
            $customer_address_1 = $cust_info->address_1;
            $customer_address_2 = $cust_info->address_2;
            $customer_city = $cust_info->city;
            $customer_state = $cust_info->state;
            $customer_zip = $cust_info->zip;
            $customer_country = $cust_info->country;
            $customer_phone = $cust_info->phone_number;
            $customer_email = $cust_info->email;
            $customer_points = $cust_info->points;
            $sales_until_discount = $this->config->item('number_of_sales_for_discount') - $cust_info->current_sales_for_discount;

        }
        $emp_info=$this->Employee->get_info($sale_info['employee_id']);
        $sold_by_employee_id=$sale_info['sold_by_employee_id'];
        $sale_emp_info=$this->Employee->get_info($sold_by_employee_id);
        $payment_type=$sale_info['payment_type'];
        $amount_change=$this->sale_lib->get_amount_due($sale_id) * -1;
        $employee=$emp_info->first_name.' '.$emp_info->last_name.($sold_by_employee_id && $sold_by_employee_id != $sale_info['employee_id'] ? '/'. $sale_emp_info->first_name.' '.$sale_emp_info->last_name: '');
		$employee_phone=$emp_info->phone_number;
		$employee_email=$emp_info->email;
		$employee_address=$emp_info->address_1;
		$sale_person = $sale_emp_info->first_name.' '.$sale_emp_info->last_name;
        $cartItemsAttributeValue = $this->sale_lib->getCartItemsAttributeValue();
        $dataExcel = [];
        $loadedFile = 'order_show_price.xlsx';
        $fieldDataOfBody = [];
        $typeExport = 'ManyPart';
        $listName = [];
        if ($sale_info['suspended'] == 2) {
            if ($type == "detail_receipt") {
                $loadedFile = 'order_show_price_detail.xlsx';
                $typeExport = 'mergeCol';
                $dataExcel['listVai'] = [];
                $dataExcel['listRemCuon'] = [];
                $dataExcel['listThanh_DongCo_DieuKhien'] = [];
                $listName = [
                    'listVai' => 'Phần rèm vải',
                    'listRemCuon' => 'Phần rèm cuốn',
                    'listThanh_DongCo_DieuKhien' => 'Thanh động cơ điều khiển'
                ];
                $fieldDataOfBody = [
                    ['col'=>'B', 'value_field' => 'name'],
                    ['col'=>'C', 'value_field' => 'measure'],
                    ['col'=>'D', 'value_field' => 'quantity'],
                    ['col'=>'E', 'value_field' => 'price'],
                    ['col'=>'F', 'value_field' => 'discount'],
                    ['col'=>'G', 'value_field' => 'total'],
                ];
                foreach($itemsContainsLine as $itemContainsLine) {
                    $listVai                    = [];
                    $listRemCuon                = [];    
                    $listThanh_DongCo_DieuKhien  = [];          
                    $listVai['itemName'] =  $itemContainsLine['itemName'];
                    foreach($cart as $item) {
                        if (in_array($item['line'], $itemContainsLine['line'])) {
                            if ($item['item_category_id'] == $this::CATEGORY_VAI_PHU  ) {
                                $listVai['item'][] = $item;
                            } elseif ($item['item_category_id'] == $this::CATEGORY_VAI_CHINH) {
                                $listVai['item'][] = $item;  
                            } elseif ($item['item_category_id'] == $this::CATEGORY_REM_CUON) {
                                $listRemCuon['item'][] = $item;
                            } else {
                                $listThanh_DongCo_DieuKhien['item'][] = $item;
                            }
                        }
                    }
                    if (!empty($listVai)) {
                        $listRemCuon['itemName'] =  $itemContainsLine['itemName'];
                        $dataExcel['listVai'][] = $listVai;
                    }
                    if (!empty($listThanh_DongCo_DieuKhien['item'])) {
                        $listThanh_DongCo_DieuKhien['itemName'] =  $itemContainsLine['itemName'];
                        $dataExcel['listThanh_DongCo_DieuKhien'][] = $listThanh_DongCo_DieuKhien;
                    }
                    if (!empty($listRemCuon['item'])) {
                        $listRemCuon['itemName'] =  $itemContainsLine['itemName'];
                        $dataExcel['listRemCuon'][] = $listRemCuon;
                    }
                }
                foreach($cart as $item) {
                    if ($item['item_category_id'] == $this::CATEGORY_REM_CUON) {
                         $dataExcel['listRemCuon'][] = $item;
                    }
                }
            } elseif ($type == "general_receipt") {
                    $fieldDataOfBody = [
                        ['col'=>'B:D', 'value_field' => 'name'],
                        ['col'=>'E', 'value_field' => 'quantity'],
                        ['col'=>'F', 'value_field' => 'price'],
                        ['col'=>'G', 'value_field' => 'total'],
                    ];
                $dongco_dieukhien = 178; // only for rem hà my, get value from table category
                $rem_nguyen_chiec = 179; // only for rem hà my, get value from table category
                $vai_chinh = 174; // only for rem hà my, get value from table category
                $loadedFile = 'order_show_price_general.xlsx';
                foreach($itemsContainsLine as $itemContainLine) {
                    
                    $eachItemsTotal = 0;
                    $itemGeneralName = '';
                    $itemGeneralPrice = 0;
                    $itemGeneralTotal = 0;
                    $itemGeneralQuantity = 0;
                    $stt = 1;
                    $rowExcel =[];
                    foreach ($itemContainLine['line'] as $line1) {
                        
                        foreach($cart as $line => $item) {
                            if($line1 == $line && ($item['item_category_id'] != $dongco_dieukhien  && $item['item_category_id'] != $rem_nguyen_chiec)) {
                                if ($item['item_category_id'] == $vai_chinh) {
                                     $itemGeneralQuantity = $cartItemsAttributeValue[$line][2]->entity_value;
                                }
                                $itemGeneralName    .= $item['name'].', ';
                                $itemGeneralTotal   += abs($item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100);
                                $eachItemsTotal     += abs($item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100);
                                break; 
                            } elseif ($line1 == $line) {
                                $rowExcel[] = $item;
                            }
                        }
                    }
                    $rowExcel[] = [
                                'name' => $itemGeneralName,
                                'quantity' => $itemGeneralQuantity,
                                'price'    => round($itemGeneralTotal/ $itemGeneralQuantity,2)
                            ];
                    $dataExcel[] = ['itemName' => $itemContainLine['itemName'],
                                     'item' => $rowExcel
                    ];
                }
            } else {
                $fieldDataOfBody = [
                    ['col'=>'B', 'value_field' => 'product_id'],
                    ['col'=>'C', 'value_field' => 'name'],
                    ['col'=>'D', 'value_field' => 'measure'],
                    ['col'=>'E', 'value_field' => 'quantity'],
                    ['col'=>'F', 'value_field' => 'price'],
                    ['col'=>'G', 'value_field' => 'discount'],
                    ['col'=>'H', 'value_field' => 'total'],
                ];
                $loadedFile = 'order_show_price.xlsx';
                foreach($itemsContainsLine as $itemContainLine) {
                    $stt = 1;
                    $rowExcel =[];
                    foreach ($itemContainLine['line'] as $line1) {
                        foreach($cart as $line => $item) {
                            if($line1 == $line ) {
                                $rowExcel[] = $item;
                            }
                        }
                    }
                    $dataExcel[] = ['itemName' => $itemContainLine['itemName'],
                                     'item' => $rowExcel
                    ];
                }
            }
        }
		$companyName = $this->config->item('company');
		$companyAddress = nl2br($this->Location->get_info_for_key('address', isset($override_location_id) ? $override_location_id : FALSE));
		$companyPhone   = $this->Location->get_info_for_key('phone', isset($override_location_id) ? $override_location_id : FALSE);
		$companyWebsite = $this->config->item('website');
        
        $header_of_multicol[] = array('mergeStartCol' =>'A1','mergeEndCol'=>'G1','text' =>$companyName,'styles'=>array('bold' =>true,'font'=>true, 'font_size'=>15));
        $header_of_multicol[] = array('mergeStartCol' =>'A2','mergeEndCol'=>'G3','text' =>$companyAddress,'styles'=>array('bold' =>false,'font'=>true, 'font_size'=>12));
        $header_of_multicol[] = array('mergeStartCol' =>'A4','mergeEndCol'=>'G4','text' =>'Điện thoại: '.$companyPhone,'styles'=>array('bold' =>false,'font'=>true, 'font_size'=>12));
        $header_of_multicol[] = array('mergeStartCol' =>'A5','mergeEndCol'=>'G5','text' =>'Website: '.$companyWebsite,'styles'=>array('bold' =>false,'font'=>true, 'font_size'=>12));
        $header_of_multicol[] = array('mergeStartCol' =>'A8','mergeEndCol'=>'G8','text' =>'Ngày :'.date('d-m-Y', strtotime($sale_info['sale_time'])),'styles'=>array('bold' =>false,'font'=>true, 'font_size'=>12));
        $header_of_multicol[] = array('mergeStartCol' =>'A9','mergeEndCol'=>'C9','text' =>'Khách Hàng: '.$customer,'styles'=>array('bold' =>false,'font'=>true, 'font_size'=>12));
        $header_of_multicol[] = array('mergeStartCol' =>'A10','mergeEndCol'=>'C10','text' =>'Số điện thoại: '.$customer_phone,'styles'=>array('bold' =>false,'font'=>true, 'font_size'=>12));
        $header_of_multicol[] = array('mergeStartCol' =>'A11','mergeEndCol'=>'C11','text' =>'Email: '.$customer_email,'styles'=>array('bold' =>false,'font'=>true, 'font_size'=>12));
        $header_of_multicol[] = array('mergeStartCol' =>'A12','mergeEndCol'=>'C12','text' =>'Địa chỉ: '.$customer_address_1,'styles'=>array('bold' =>false,'font'=>true, 'font_size'=>12));
        $header_of_multicol[] = array('mergeStartCol' =>'E9','mergeEndCol'=>'G9','text' =>'Nhân viên: '.$employee,'styles'=>array('bold' =>false,'font'=>true, 'font_size'=>12));
        $header_of_multicol[] = array('mergeStartCol' =>'E10','mergeEndCol'=>'G10','text' =>'Số điện thoại: '.$employee_phone,'styles'=>array('bold' =>false,'font'=>true, 'font_size'=>12));
        $header_of_multicol[] = array('mergeStartCol' =>'E11','mergeEndCol'=>'G11','text' =>'Email: '.$employee_email,'styles'=>array('bold' =>false,'font'=>true, 'font_size'=>12));
        $header_of_multicol[] = array('mergeStartCol' =>'E12','mergeEndCol'=>'G12','text' =>'Địa chỉ: '.$employee_address,'styles'=>array('bold' =>false,'font'=>true, 'font_size'=>12));
       
        

        $bizExcel = new BizExcel($loadedFile);
        $bizExcel->setHeaderOfMultiCol($header_of_multicol);
        $bizExcel->setlistName($listName);
        $bizExcel->setNumberRowStartBody(15)->setHeaderOfBody($fieldDataOfBody);
        $bizExcel->setTypeExport($typeExport);
        $bizExcel->setDataExcel($dataExcel);
        $excelContent = $bizExcel->generateFile(false);
        $this->load->helper('download');
        force_download( 'Hóa đơn báo gia'.$sale_info['sale_time'].'.xlsx', $excelContent);
        exit;
    }
}
