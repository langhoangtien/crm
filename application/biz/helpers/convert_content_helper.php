<?php
function convert_content($html_string, $customer = null){
    $CI =& get_instance();

    if(!empty($customer)) {
        if($customer > 0 && is_numeric($customer)) {
            $CI->load->model('Customer');
            $customer = $CI->Customer->get_information($customer);
        }
    }

    $customer_info = array(
        'TEN_KH' 	   => $customer['first_name'] . ' ' . $customer['last_name'],
        'CT_KH' 	   => isset($customer['company_name'])?$customer['company_name']:'',
        'DIA_CHI_1_KH' => $customer['address_1'],
        'DIA_CHI_2_KH' => isset($customer['address_2'])?$customer['address_2']:'',
        'SDT_KH' 	   => $customer['phone_number'],
   		  'CHUCVU_KH'    => isset($customer['position'])?$customer['position']:'',
        'TKNH_KH' 	   => isset($customer['account_number'])?$customer['account_number']:'',
				'EMAIL_KH'     => $customer['email'],
    );
	
    $info_merge = $customer_info;

    foreach($info_merge as $key => $val)
        $info_merge[$key . '_U'] = $val = mb_strtoupper($val, 'UTF-8');

    foreach($info_merge as $key => $val) {
        $html_string = str_replace('{'.$key.'}',$val,$html_string);
    }


    return $html_string;
}