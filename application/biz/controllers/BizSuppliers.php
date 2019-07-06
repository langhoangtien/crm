<?php
require_once (APPPATH . "controllers/Suppliers.php");

class BizSuppliers extends Suppliers 
{
	function save($supplier_id=-1)
	{

		$post      = $this->input->post();
		$balance   = (float)$post['balance'];
		$balance_2 = (float)$post['balance_2'];
		if($balance < 0) {
			echo json_encode(array('success'=>false,'message'=>$this->config->item('supplier_balance') . ' không được âm.'));
			return;
		}

		if($balance_2 < 0) {
			echo json_encode(array('success'=>false,'message'=>$this->config->item('supplier_balance_2') . ' không được âm.'));
			return;
		}


		$this->check_action_permission('add_update');

		$iValid = FALSE;
		if(!$this->input->post('code')){
			$ma_khach_hang = $this->config->item('ma_khach_hang_prefix').' '.$this->config->item('ma_khach_hang_bat_dau_tu');
			$iValid = TRUE;
		}

		$person_data = array(
			'last_name'=>$this->input->post('last_name'),
			'email'=>$this->input->post('email'),
			'phone_number'=>$this->input->post('phone_number'),
			'address_1'=>$this->input->post('address_1'),
			'website'=>$this->input->post('website'),
			'comments'=>$this->input->post('comments'),
			'iValid'=>$iValid,
		);


		if(!empty($this->input->post('birth_date')))
			$person_data['birth_date'] = date('Y-m-d', strtotime($this->input->post('birth_date')));
		$supplier_data=array(
			'attribute_set_id' => $this->input->post('attribute_set_id'),
			'company_name'=>$this->input->post('company_name'),
			'charter_capital'=>$this->input->post('charter_capital'),
			'business_registration_number'=> $this->input->post('business_registration_number'),
			'registration_date'=>$this->input->post('registration_date'),
			'tax_number'=>$this->input->post('tax_number'),
			'unit_type_id'=>$this->input->post('unit_type'),
			'company_form_id'=>$this->input->post('company_form'),
			'override_default_tax'=> $this->input->post('override_default_tax') ? $this->input->post('override_default_tax') : 0,
			'balance' => $balance,
			'balance_2' => $balance_2,
		);


		if(!empty($this->input->post('registration_date')))
			$supplier_data['registration_date'] = date('Y-m-d', strtotime($this->input->post('registration_date')));

		// if($this->input->post('unit_type'))
		// {
		// 	$supplier_data['unit_type_id'] = $this->input->post('unit_type');
		// }

		// if($this->input->post('company_form'))
		// {
		// 	$supplier_data['company_form_id'] = $this->input->post('company_form');
		// }


		if($this->input->post('account_number'))
		{
			$supplier_data['account_number'] = $this->input->post('account_number');
		}
		
		$redirect = $this->input->post('redirect');

		if($supplier_id > 0) {
			$supplier_info = $this->Supplier->get_info($supplier_id);
		}

		if($this->Supplier->save_supplier($person_data,$supplier_data,$supplier_id))
		{
			/* Update Extended Attributes */
			if (!class_exists('Attribute')) {
				$this->load->model('Attribute');
			}
			$attributes = $this->input->post('attributes');
			if (!empty($attributes)) {
				$this->Attribute->reset_attributes(array('entity_id' => $supplier_id, 'entity_type' => 'suppliers'));
				foreach ($attributes as $attribute_id => $value) {
					$attribute_value = array('entity_id' => $supplier_id, 'entity_type' => 'suppliers', 'attribute_id' => $attribute_id, 'entity_value' => $value);
					$this->Attribute->set_attributes($attribute_value);
				}
			}
			/* End Update */

			// if ($this->Location->get_info_for_key('mailchimp_api_key'))
			// {
			// 	$this->Person->update_mailchimp_subscriptions($this->input->post('email'), $this->input->post('first_name'), $this->input->post('last_name'), $this->input->post('mailing_lists'));
			// }
			
			// $success_message = '';
			
			//New supplier
			if($supplier_id==-1)
			{
				$success_message = lang('suppliers_successful_adding').' '.$supplier_data['company_name'];
				echo json_encode(array('success'=>true, 'redirect'=> $redirect, 'message'=>$success_message,'person_id'=>$supplier_data['person_id']));
				$supplier_id = $supplier_data['person_id'];
				
			}
			else //previous supplier
			{
				$success_message = lang('suppliers_successful_updating').' '.$supplier_data['company_name'];
				$this->session->set_flashdata('manage_success_message', $success_message);
				echo json_encode(array('success'=>true,'redirect'=> $redirect, 'message'=>$success_message,'person_id'=>$supplier_id));
			}
			
			$suppliers_taxes_data = array();
			$tax_names = $this->input->post('tax_names');
			$tax_percents = $this->input->post('tax_percents');
			$tax_cumulatives = $this->input->post('tax_cumulatives');
			for($k=0;$k<count($tax_percents);$k++)
			{
				if (is_numeric($tax_percents[$k]))
				{
					$suppliers_taxes_data[] = array('name'=>$tax_names[$k], 'percent'=>$tax_percents[$k], 'cumulative' => isset($tax_cumulatives[$k]) ? $tax_cumulatives[$k] : '0' );
				}
			}
			$this->Supplier_taxes->save($suppliers_taxes_data, $supplier_id);



			// Thêm sủa danh mục địa lý và hình thức công ty
			// lưu khu vực địa lý
			$geographical_area = $this->input->post('geographical_area') ? $this->input->post('geographical_area') : array();
			$table_name = 'geographical_area';
			$this->Supplier->thay_doi_bang_danh_muc_lien_ket($table_name, $geographical_area, $supplier_id);

            // Lưu ngành nghề kinh doanh
			$business_type = $this->input->post('business_type') ? $this->input->post('business_type') : array();
			$table_name = 'business_type';
			$this->Supplier->thay_doi_bang_danh_muc_lien_ket($table_name, $business_type, $supplier_id);


            //Thêm sửa người đại diện
            if($this->input->post('name_more')){
			$supplier_delegate = array(
				'supplier_id' =>$supplier_id,
				'name_more' =>$this->input->post('name_more'),
				'phone_more' =>$this->input->post('phone_more'),
				'email_more' =>$this->input->post('email_more'),
				'position_more' =>$this->input->post('position_more'),
				'note_more' => $this->input->post('note_more'),
				'sex_more' =>$this->input->post('sex_more')
			);
			if($this->input->post('birth_date_more'))
				{
				$supplier_delegate['birth_date_more'] = date('Y/m/d',strtotime($this->input->post('birth_date_more')));
				}
			$this->Supplier->save_delegate($supplier_id,$supplier_delegate);

			}
             // Thêm sửa thông tin gười đầu mối

			$phone_head = $this->input->post('phone_head');
			$name_head = $this->input->post('name_head');
			$email_head = $this->input->post('email_head');
			$position_head = $this->input->post('position_head');
			$note_head = $this->input->post('note_head');
			$supplier_head = array();

			if(is_array($name_head))
			{

				foreach ($name_head as $key => $value) {
					if(!empty($name_head[$key]))
					$supplier_head[] = array(
						'supplier_id'=>$supplier_id,
						'name_head'=>$name_head[$key],
						'phone_head'=>$phone_head[$key],
						'email_head'=>$email_head[$key],
						'position_head'=>$position_head[$key],
						'note_head'=>$note_head[$key]
					);
				}

				if(!empty($supplier_head))
				$this->Supplier->save_head($supplier_id,$supplier_head);

			}

			//Delete Image
			if($this->input->post('del_image') && $supplier_id != -1)
			{
				$supplier_info = $this->Supplier->get_info($supplier_id);				
				if($supplier_info->image_id != null)
				{
					$this->Person->update_image(NULL,$supplier_id);
					$this->load->model('Appfile');
					$this->Appfile->delete($supplier_info->image_id);
				}
			}

			//Save Image File
			if(!empty($_FILES["image_id"]) && $_FILES["image_id"]["error"] == UPLOAD_ERR_OK)
			{			    
				$allowed_extensions = array('png', 'jpg', 'jpeg', 'gif');
				$extension = strtolower(pathinfo($_FILES["image_id"]["name"], PATHINFO_EXTENSION));

				if (in_array($extension, $allowed_extensions))
				{
					$config['image_library'] = 'gd2';
					$config['source_image']	= $_FILES["image_id"]["tmp_name"];
					$config['create_thumb'] = FALSE;
					$config['maintain_ratio'] = TRUE;
					$config['width']	 = 400;
					$config['height']	= 300;
					$this->load->library('image_lib', $config); 
					$this->image_lib->resize();
					$this->load->model('Appfile');
					$image_file_id = $this->Appfile->save($_FILES["image_id"]["name"], file_get_contents($_FILES["image_id"]["tmp_name"]));
				}

				if($supplier_id==-1)
				{
					$this->Person->update_image($image_file_id,$supplier_data['person_id']);
				}
				else
				{
					$this->Person->update_image($image_file_id,$supplier_id);

				}
			}
		}
		else//failure
		{	
			echo json_encode(array('success'=>false,'message'=>lang('suppliers_error_adding_updating').' '.
				$supplier_data['company_name'],'person_id'=>-1));
		}

		// redirect(base_url());

	}



                 #                                  SORTING D13

	public function sorting_d13()
	{

		$order = $this->input->post('order') ? $this->input->post('order') : "person_id";
		$order_by = $this->input->post('order_by') ? $this->input->post('order_by') : "asc";
		$search = $this->input->post('search') ? $this->input->post('search') : "";
		$unit_type = $this->input->post('unit_type') ? $this->input->post('unit_type') : "";
		$limit = 10;
        $offset = $this->uri->segment(3);
        intval($offset);
        $offset = ($offset<1) ? 1 : $offset;
        $offset = $limit*($offset-1);
        $data = array();
        $data['count'] = $this->Supplier->get_all("","",$order,$order_by,$unit_type,$search)->result_array();
        $data['suppliers'] = $this->Supplier->get_all($limit,$offset,$order,$order_by,$unit_type,$search)->result_array();
        $data['offset'] = $offset;
        $config = array();
        $config['base_url']=base_url('suppliers/sorting_d13');
        $config['total_rows'] = count($data['count'] );
        $data['list'] = $config['total_rows'] = count($data['count'] );
        $config['per_page'] = $limit;
        $config['prev_link'] = "<<";
        $config['next_link'] = ">>";
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['cur_tag_open'] = '<li class="active"><a>';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="pagi">';
        $config['num_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li class="pagi">';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li class="pagi">';
        $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="pagi">';
        $config['last_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li class="pagi">';
        $config['first_tag_close'] = '</li>';
        $config['use_page_numbers'] = TRUE;
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $this->load->view('suppliers/sorting_d13', $data);
	}




 function index($offset = 0)
    {
       	$limit = 10;
        $offset = $this->uri->segment(3);
        intval($offset);
        $offset = ($offset<1) ? 1 : $offset;
        $offset = $limit*($offset-1);
        $data = array();
        $data['count'] = $this->Supplier->get_all()->result_array();
        $data['suppliers'] = $this->Supplier->get_all($limit,$offset)->result_array();
        // var_dump($data['suppliers']);
        $data['offset'] = $offset;
        $config = array();
        $config['base_url']=base_url('suppliers/sorting_d13');
        $config['total_rows'] = count($data['count'] );
        $data['list'] = $config['total_rows'];
        $config['per_page'] = $limit;
        $config['prev_link'] = "<<";
        $config['next_link'] = ">>";
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['cur_tag_open'] = '<li class="active"><a>';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="pagi">';
        $config['num_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li class="pagi">';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li class="pagi">';
        $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="pagi">';
        $config['last_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li class="pagi">';
        $config['first_tag_close'] = '</li>';
        $config['use_page_numbers'] = TRUE;
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['all_unit_type'] = $this->Supplier->get_all_unit_type();
        $this->load->view('people/manage_d13', $data);
    }


    public function unit_type()
    {
    	$this->check_action_permission('add_update_unit_type');
    	$data['unit_type'] = $this->Supplier->get_unit_type();
    	$this->load->view('suppliers/unit_type', $data);
    }

    public function save_unit_type()
    {
    	$this->check_action_permission('add_update_unit_type');
    	$id = $this->input->post('id');
    	$name = $this->input->post('name');
    	$data = array('name'=>$name);

    	$this->Supplier->save_unit_type($id,$data);
       
    	
    }

      public function del_unit_type()
    {

    	$this->check_action_permission('add_update_unit_type');
    	$id = $this->input->post('id');
    	$this->Supplier->del_unit_type($id);
       
    	
    }

        public function add_unit_type()
    {
    	$this->check_action_permission('add_update_unit_type');
    	$name = $this->input->post('name');
    	$this->Supplier->add_unit_type($name);
       
    	
    }



		// function rg()
		// {
		// 	$pattern = '/h(.+)o/';
		// 	$subject = 'hello la xin chao';
		// 	if (preg_match($pattern, $subject,$matches))
		// 	{
		// 		echo '<pre>';
		//     var_dump($matches);
		// 	}
		//     else echo '<h1 style ="text-align:center;color:red"> NO MATCHES</h1>';
		// }




}

?>