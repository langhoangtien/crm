<?php

function get_customer_manage_table($customer, $controller) 
{
	$CI = & get_instance();
	$controller_name = str_replace(BIZ_PREFIX, '', strtolower(get_class($CI)));	
	$table = '<table class="tablesorter table table-hover" id="sortable_table">';
	$headers = array(
			lang('customers_sms_tmp_code'),
			lang('customers_sms_tmp_name'),
			lang('customers_sms_tmp_phonenumber'),
			'&nbsp'
	);
	$table.='<thead><tr>';

	$count = 0;
	foreach ($headers as $header) {
		$count++;

		if ($count == 1) {
			$table.="<th class='leftmost'>$header</th>";
		} elseif ($count == count($headers)) {
			$table.="<th class='rightmost'>$header</th>";
		} else {
			$table.="<th>$header</th>";
		}
	}
	$table.='</tr></thead><tbody>';
        
	$table.=get_customer_manage_table_data_rows($customer, $controller);
        $table.='<tr><td colspan="2">'.anchor("$controller_name/send_sms_list", lang('customers_sms_send_list'), array(
            'title' => $customer_id,
            'id'=>'sendsms_list',
            'class' => 'bulk_edit_inactive btn btn-primary btn-lg',
            'data-id'=>$customer_id,
            'data-toggle'=> "modal",
            'data-target'=>"#myModal")).'</td>';
        $table.='<td class="delete_all_sms_tmp a-menu">'.anchor("$controller_name/manage_sms_tmp", lang('customers_sms_del_list'), array('title' => $customer_id, 'class' => 'btn btn-primary btn-lg delete_all_sms_tmp','data-id'=>$customer_id)).'</td></tr>';
	$table.='</tbody></table>';
	return $table;
}

function get_customer_manage_table_data_rows($customer, $controller) 
{
	$CI = & get_instance();
	$table_data_rows = '';

	foreach ($customer as $key =>$value) {
		$table_data_rows.=get_customer_data_row($key, $value, $controller);
	}
	if (count($customer) == 0) {
		$table_data_rows.="<tr><td colspan='11'><span class='col-md-12 text-center text-warning' >".lang('customers_no_sms')."</span></td></tr>";
	}
         
	return $table_data_rows;
}

function get_customer_data_row($customer_id, $customer_info, $controller) 
{
	$CI = & get_instance();
	$controller_name = str_replace(BIZ_PREFIX, '', strtolower(get_class($CI)));

	$table_data_row = '<tr>';
	$table_data_row.="<td>" . $customer_id . "</td>";
	$table_data_row.='<td>' . H($customer_info['name']) . '</a></td>';
	$table_data_row.='<td>' . H($customer_info['phone_number']) . '</td>';
	$table_data_row.='<td class="rightmost">' . anchor($controller_name . "/del_customer/$customer_id/2", lang('common_delete'), array('title' => $customer_id, 'class' => 'delete_sms_tmp','data-id'=>$customer_id)) . '</td>';
	$table_data_row.='</tr>';
	return $table_data_row;
}



//add table send
function get_customer_manage_send_table($customer, $controller) 
{

	//Thay cho biến this <=> controller
	$CI = & get_instance();
	$controller_name = str_replace(BIZ_PREFIX, '', strtolower(get_class($CI)));	
	$table = '<table class="tablesorter table table-hover" id="sortable_table_">';
	$headers = array(
			'<input type="checkbox" id="select_all" /><label for="select_all"><span></span></label> E-Mail 
			<input type="checkbox" id="select_all_sms" /><label for="select_all_sms"><span></span></label> SMS
			',
			lang('customers_sms_tmp_code'),
			lang('customers_sms_tmp_name'),
			lang('customers_sms_tmp_phonenumber'),
			'&nbsp'
	);
	$table.='<thead><tr>';

	$count = 0;
	foreach ($headers as $header) {
		$count++;

		if ($count == 1) {
			$table.="<th class='leftmost'>$header</th>";
		} elseif ($count == count($headers)) {
			$table.="<th class='rightmost'>$header</th>";
		} else {
			$table.="<th>$header</th>";
		}
	}
	$table.='</tr></thead><tbody>';
        
	$table.=get_customer_manage_table_send_data_rows($customer, $controller);
       
	$table.='</tbody><tfoot>';
	 	$table.='<tr><td colspan="4">';
	 	$table .= '<a class="btn btn-primary btn-lg" title="title" id="sendMail_list" href="javascript:;">
			<span class="">Gửi E-Mail</span></a>';

		$table .= '&nbsp &nbsp <a class="btn btn-primary btn-lg" title="title" id="sendSms_list" href="#" data-toggle="modal" data-target="#myModal">
			<span class="">Gửi SMS</span></a>';
		$table .= '&nbsp &nbsp <a class="btn btn-primary btn-lg" title="title" id="create_smsMail_group" href="javascript:;">
			<span class="">Lưu nhóm</span></a>';			 

	 	$table .= '</td>';
        $table.='<td class="delete_all_tmp a-menu">'.anchor("$controller_name/index?send_all=1", lang('customers_del_list'), array('class' => 'btn btn-primary btn-lg delete_all_tmp',)).'</td></tr>';
	 $table.='</tfoot></table>';

	return $table;
}


function get_customer_manage_table_send_data_rows($customer, $controller) 
{
	$CI = & get_instance();
	$table_data_rows = '';

	foreach ($customer as $key =>$value) {
		$table_data_rows.=get_customer_send_data_row($key, $value, $controller);
	}
	if (count($customer) == 0) {
		$table_data_rows.="<tr><td colspan='11'><span class='col-md-12 text-center text-warning' >".lang('customers_no_sms')."</span></td></tr>";
	}
         
	return $table_data_rows;
}

function get_customer_send_data_row($customer_id, $customer_info, $controller) 
{
	$CI = & get_instance();
	$controller_name = str_replace(BIZ_PREFIX, '', strtolower(get_class($CI)));
	$table_data_row = '<tr>';

	$checksms = '';
	$checkmail = '';
	if (array_key_exists('send_sms', $customer_info)) {
		$checksms = ($customer_info['send_sms'] == 1) ? 'checked' : '';
		$checkmail = ($customer_info['send_mail'] == 1) ? 'checked' : '';
	
	$table_data_row.="<td><input class='ckmail' type='checkbox' " . $checkmail . " id='customer_$customer_id' value='".$customer_id."'/><label for='customer_$customer_id'><span class='mclick' data-mail='".$customer_id."'></span></label> &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp
		<input class='cksms' type='checkbox' ".$checksms." id='sms_customer_$customer_id' value='".$customer_id."'/><label for='sms_customer_$customer_id'><span class='sclick' data-sms='".$customer_id."'></span></label>
	</td>";

	$table_data_row.="<td>" . $customer_id . "</td>";
	$table_data_row.='<td>' . H($customer_info['name']) . '</a></td>';
	$table_data_row.='<td>' . H($customer_info['phone_number']) . '</td>';
	$table_data_row.='<td class="rightmost">' . anchor($controller_name . "/del_customer/$customer_id/2", lang('common_delete'), array('title' => $customer_id, 'class' => 'delete_sms_mail_tmp','data-id'=>$customer_id)) . '</td>';
	
	}
	$table_data_row.='</tr>';
	return $table_data_row;
}


//quotes_contract type
function get_quotes_contract_type_manage_table($qc_type, $controller) {
	$CI = & get_instance();
	$table = '<table class="tablesorter" id="sortable_table_type">';
	$headers = array(
			'<input type="checkbox" id="select_all_type" /><label for="select_all_type"><span></span></label>',
			lang('customers_quotes_contract_code'),
			lang('customers_quotes_contract_title'),
			lang('customers_quotes_contract_status'),
			'&nbsp'
	);
	$table.='<thead><tr>';

	$count = 0;
	foreach ($headers as $header) {
		$count++;

		if ($count == 1) {
			$table.="<th class='leftmost'>$header</th>";
		} elseif ($count == count($headers)) {
			$table.="<th class='rightmost'>$header</th>";
		} else {
			$table.="<th>$header</th>";
		}
	}
	$table.='</tr></thead><tbody>';
	$table.=get_quotes_contract_type_manage_table_data_rows($qc_type, $controller);
	$table.='</tbody></table>';
	return $table;
}

function get_quotes_contract_type_manage_table_data_rows($qc_type, $controller) {
	$CI = & get_instance();
	$table_data_rows = '';

	foreach ($qc_type->result() as $val) {
		$table_data_rows.=get_quotes_contract_type_data_row($val, $controller);
	}

	if ($qc_type->num_rows() == 0) {
		$table_data_rows.="<tr><td colspan='5'><span class='col-md-12 text-center text-warning' >" . lang('customers_quotes_contract_none_data') . "</div></tr></tr>";
	}

	return $table_data_rows;
}

function get_quotes_contract_type_data_row($qc_type, $controller) {
	$CI = & get_instance();
	$controller_name=str_replace(BIZ_PREFIX, '', strtolower(get_class($CI)));
	$table_data_row = '<tr>';
	if ($qc_type->id < 12) {
		$table_data_row .= "<td width='5%'><span class='imgck'></span></td>";
	}else{
		$table_data_row .= "<td width='5%'><input type='checkbox' id='qc_$qc_type->id' value='" . $qc_type->id . "'/><label for='qc_$qc_type->id'><span></span></label></td>";
	}
	

	$table_data_row .= "<td width='25%'>$qc_type->code</td>";
	$table_data_row .= "<td width='30%'>$qc_type->title</td>";

	

	if ($qc_type->id < 12) {
		$table_data_row .= '<td>Mặc định</td>';
	}else{
		$table_data_row .= '<td width="15%" class="rightmost">' . anchor($controller_name . "/quotes_contract_type_add/$qc_type->id", lang('common_edit'), array('title' => lang('customers_quotes_contract_update'), 'class' => '')) . '</td>';
	}
	$table_data_row.='</tr>';
	return $table_data_row;
}


?>