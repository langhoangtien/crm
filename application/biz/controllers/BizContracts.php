<?php
require_once(APPPATH . "controllers/Secure_area.php");

class BizContracts extends Secure_area
{
    protected $_paginator = array(
        'per_page' => 10,
        'uri_segment' => 3
    );

    protected $_option = null;

    function __construct()
    {
        parent::__construct('contracts');
        $this->lang->load('quotes_contract');

        $this->_scopeOfView = 'view_scope_owner';
        if ($this->Employee->has_module_action_permission(
            $this->module_id,
            'view_scope_location',
            $this->Employee->get_logged_in_employee_info()->person_id)
    ) {
            $this->_scopeOfView = 'view_scope_location';
        }

        if ($this->Employee->has_module_action_permission(
            $this->module_id,
            'view_scope_all',
            $this->Employee->get_logged_in_employee_info()->person_id)
    ) {
            $this->_scopeOfView = 'view_scope_all';
        }
        $this->load->helper('my_table_helper');
        $this->load->library('MySession');
        $this->load->library('sale_lib');
        $this->load->library('receiving_lib');
        $this->load->library('contract_lib');
        $this->load->library('PhpWord');
        $this->load->helper('download_helper');
        $this->load->model('Contract');
        $this->load->model('Customer');
        $this->load->model('Sale');

        //thay đổi thông tin validate
        $this->load->library("form_validation");
        $this->form_validation->set_message('required', '%s ' . lang('required'));
        $this->form_validation->set_message('is_unique', '%s không được trùng lặp.');

        $this->_option = $this->uri->segment(3, 'customer');
    }


    function view()
    {

        $id = $this->uri->segment(4);
        $data = array('id' => $id);
        $data['option'] = $this->_option;
        $data['page'] = $this->uri->segment(5, 1);
        $data['list_type'] = $this->uri->segment(6, 'list');
        if ($id == -1) {
            $this->check_action_permission('add_update');
            $data['action'] = 'add';
            $data['linkRedirect'] = base_url() . 'customers/contract';
            $data['last_id'] = $this->Contract->get_contract_last_id();
            $sale_id = $this->uri->segment(5);
            $data['sale_id'] = $sale_id;
            // if(!is_int($sale_id))
            // redirect('contracts/index/customer','refresh');         
            $this->load->view('contracts/form_contract_add', $data);
        } else {
            $discount_id = $this->Item->get_item_id_for_flat_discount_item();
            $item = $this->Contract->get_item(array('id' => $id));
            $project = $this->Task->get_task_item($item['project_id']);
            $service = $this->Item->get_info($item['item_id']);
            $sale_info = $this->Sale->getInfo($item['sale_id']);
            $customer_info = $this->Customer->get_information($sale_info['customer_id']);

            $data['action'] = 'edit';
            // If not Owner
            if ($item['created_by'] != $this->Employee->get_logged_in_employee_info()->id) {
                // Check View Or Edit Permission By Task Assigning
                $mode = $this->Task->has_view_edit_permission($item['project_id']);
                if ($mode >= 0) {
                    if ($mode == 1) {
                        $data['action'] = 'edit';
                    } else {
                        $data['action'] = 'view';
                    }
                } else {
                    // Check View Permission
                    $permissions = (new MY_System_Info())->get_permissions('contracts');
                    $edit_permissions = ['add_update'];
                    $view_permissions = ['view_scope_all', 'view_scope_location'];
                    $redirect = true;
                    foreach ($view_permissions as $permission) {
                        if (in_array($permission, $permissions)) {
                            $data['action'] = 'view';
                            $redirect = false;
                        }
                    }
                    foreach ($edit_permissions as $permission) {
                        if (in_array($permission, $permissions)) {
                            $data['action'] = 'edit';
                            $redirect = false;
                        }
                    }
                    // Check Assigned Permission
                    $assigned_permission = (new MY_System_Info())->get_task_assigned_permission($project['id']);
                    if (!empty($assigned_permission)) {
                        // Check assigned view permission
                        if (intval($assigned_permission->is_xem) == 1) {
                            $data['action'] = 'view';
                        }
                        // Check assigned edit permission
                        if (intval($assigned_permission->is_implement) == 1 || intval($assigned_permission->is_join) == 1 || intval($assigned_permission->is_pheduyet) == 1) {
                            $data['action'] = 'edit';
                        }
                        $redirect = false;
                    }
                    // Check Spec Groups (Group 1, 2)
                    // Get Group ID
                    $is_super_group = (new MY_System_Info())->is_super_group();
                    if ($is_super_group) {
                        $data['action'] = 'edit';
                        $redirect = false;
                    }
                    // Check Basic Edit Permission If Not Assigning
                    if ($redirect) {
                        $this->check_action_permission('add_update');
                    }
                }
            }

            // var_dump($item);die();
            $item['project_name'] = $project['name'];
            $item['project_id'] = $project['project_id'];
            $item['customer_id'] = $project['customer_ids'];
            $item['service_id'] = $service->item_id;
            $item['service_name'] = $service->name;
            $item['service_type_name'] = $service->category_name;


            $data['discount_id'] = $discount_id;
            $data['item'] = $item;
            $data['customer_info'] = $customer_info;
            $data['contract_rule'] = $this->Contract->item_select_box(array('type' => 'rule'));
            $data['sale_info'] = $sale_info;

            // echo "<pre>";
            // print_r($data['item']); die();
            $this->load->view('contracts/form_contract_' . $data['action'], $data);
        }
    }

    function email()
    {
        $id = $this->uri->segment(4);
        $data = array('id' => $id);
        $data['option'] = $this->_option;
        $data['page'] = $this->uri->segment(5, 1);
        $data['list_type'] = $this->uri->segment(6, 'list');
        $data['list_mail'] = $this->Customer->constract_list(array());
        $contract_info = $this->Contract->get_item(array('id' => $id));
        if ($this->_option == 'customer')
            $data['sale_info'] = $this->sale_lib->getSale($contract_info['sale_id'], $this->config);
        elseif ($this->_option == 'supplier')
            $data['receiving_info'] = $this->receiving_lib->get_receiving($contract_info['receiving_id']);

        $this->load->view('contracts/form_contract_email', $data);
    }

    function send_mail()
    {
        $post = $this->input->post();

        $person_id = $this->session->userdata('person_id');
        $upload_dir = DOCUMENT_PATH . '/files/store_' . $person_id;

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        if (!empty($post)) {
            $flagError = false;
            $contract_info = $this->Contract->get_item(array('id' => $post['contract_id']));
            if (empty($contract_info)) return;

            $post['content_email'] = trim($post['content_email']);
            $post['email'] = trim($post['email']);
            $this->sale_lib->copy_entire_sale($contract_info['sale_id'], true);
            $this->input->post = $post;
            $arrParam = array_merge($post, $this->input->get());

            $this->form_validation->set_rules('title', 'Tiêu đề', 'required');
            $this->form_validation->set_rules('email', 'Email', 'required|callback_validate_email');
            $this->form_validation->set_rules('content_email', 'Nội dung mail', 'required');

            if ($_FILES["file_upload"]['name'] != "") {
                $this->form_validation->set_rules('file_name', 'Tên File', 'required');
            }

            if ($this->form_validation->run($this) == FALSE) {
                $errors = $this->form_validation->error_array();
                $flagError = true;
            } else {
                if ($_FILES["file_upload"]['name'] != "") {
                    $ext = pathinfo($_FILES["file_upload"]['name'], PATHINFO_EXTENSION);
                    $file_name = rewriteUrl($post['file_name']);

                    $config['upload_path'] = $upload_dir;
                    $config['allowed_types'] = 'jpg|png|pdf|docx|doc|xls|xlsx|zip|rar|txt';
                    $config['max_size'] = '20480';
                    $config['encrypt_name'] = FALSE;

                    $config['file_name'] = $file_name . '.' . $ext;
                    if (file_exists($upload_dir . '/' . $file_name . '.' . $ext)) {
                        $config['file_name'] = $file_name . time() . '.' . $ext;
                    }

                    $this->load->library('upload', $config);

                    if ($this->upload->do_upload("file_upload")) {
                        $file_info = $this->upload->data();
                        $arrParam['size'] = $_FILES['file_upload']['size'];
                        $arrParam['extension'] = $ext;
                        $arrParam['file_name'] = $config['file_name'];

                    } else {
                        $flagError = true;
                        $err = $this->upload->display_errors();
                        $err = strip_tags($err);

                        $errors['file_upload'] = $err;
                    }
                }

                if ($flagError == false) {
                    if ($contract_info['option'] == 'customer') {
                        $sale_info = $this->sale_lib->getSale($contract_info['sale_id'], $this->config);
                        $mail_address_name = $sale_info['customer'];
                        $mail_person_id = $sale_info['customer_id'];
                    } elseif ($contract_info['option'] == 'supplier') {
                        $receiving_info = $this->receiving_lib->get_receiving($contract_info['receiving_id']);
                        $mail_address_name = $receiving_info['supplier'];

                        $mail_person_id = $receiving_info['supplier_id'];
                    }

                    $address_list[] = array(
                        'AddAddress' => $post['email'],
                        'AddAddress_name' => $mail_address_name,
                    );

                    $body_list[] = $post['content_email'];

                    $mail['from_name'] = $this->config->item('company');
                    $mail['address_list'] = serialize($address_list);
                    $mail['subject'] = $post['title'];
                    $mail['body'] = serialize($body_list);
                    $mail['type'] = 'sequence';

                    if ($_FILES["file_upload"]['name'] != "") {
                        $mail['file_name'] = $arrParam['file_name'];
                        biz_send_mail($mail, $_FILES);
                    } else
                    biz_send_mail($mail);
                }
            }

            if ($flagError == false) {
                if ($mail_person_id > 0) {
                    $file_save = !empty($arrParam['file_name']) ? $arrParam['file_name'] : '';
                    $extension = !empty($ext) ? $ext : '';

                    $data_history = array(
                        'person_id' => $mail_person_id,
                        'employee_id' => $this->session->userdata('person_id'),
                        'title' => $post['title'],
                        'content' => $post['content_email'],
                        'time' => date('Y-m-d H:i:s'),
                        'note' => 'HOPDONG|' . $contract_info['code'] . '|' . $this->sale_lib->get_total($contract_info['sale_id']),
                        'file' => $file_save,
                        'extension' => $extension,
                        'status' => 1,
                    );

                    $this->Customer->add_mail_history($data_history);
                }

                $_SESSION['notice'] = 'Gửi mail thành công.';
                echo json_encode(array('flag' => 'true'));
            } else {
                echo json_encode(array('flag' => 'false', 'errors' => $errors));
            }
        }
    }

    function download_file_contract()
    {
        $this->load->helper('download');
        $id = $this->uri->segment(3);

        $item = $this->Contract->list_file_contract(array('id' => $id));
        // $file = DOCUMENT_PATH . 'files/'.$item[0]['name_file'];
        $file = file_get_contents(base_url('/document/files/' . $item[0]['name_file']));
        force_download($item[0]['name_file'], $file);
    }

    function contract_add()
    {
        $post = $this->input->post();
        $get = $this->input->get();
        $post['code'] = filter_trim_space($post['code']);
        $post['name'] = filter_trim_space($post['name']);
        $post['note'] = filter_trim_space($post['note']);

        $this->input->post = $post;

        $person_id = $this->session->userdata('person_id');
        // $upload_dir = DOCUMENT_PATH . 'files/store_' . $person_id;
        $upload_dir = DOCUMENT_PATH . 'files';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        if (!empty($post)) {
            // echo "<pre>";
            // var_dump($post);die();
            $rt = $post['payment']['c_status'];
            $check = 0;
            if (is_array($rt)) {
                foreach ($rt as $key => $value) {
                    if ($value == 'liquidated')
                        $check++;
                }
            }

            // var_dump($check);die();
            if ($check > 1) {
                echo json_encode(array('flag' => 'false', 'c_status' => 't'));
                return;
            }

            $flagError = false;
            $array_post = array('name', 'code', 'sale_code', 'file_name', 'note');
            foreach ($array_post as $key => $val)
                $post[$key] = trim($val);
            $this->input->post = $post;
            $arrParam = array_merge($post, $this->input->get());
            if ($arrParam['option'] == 'customer') {

                $this->form_validation->set_rules('sale_id', 'Nhu cầu khách hàng', 'required|integer|is_unique[phppos_contract.sale_id]');
            } // $arrParam['sale_id'] = (empty($post['sale_id'])) ? 0 : $post['sale_id'];
            elseif ($arrParam['option'] == 'supplier')
                $arrParam['receiving_id'] = (empty($post['receiving_id'])) ? 0 : $post['receiving_id'];

            $this->input->post = $post;
            $arrParam = array_merge($post, $this->input->get());
            if ($arrParam['option'] == 'customer')
                $arrParam['sale_id'] = (empty($post['sale_id'])) ? 0 : $post['sale_id'];
            elseif ($arrParam['option'] == 'supplier')
                $arrParam['receiving_id'] = (empty($post['receiving_id'])) ? 0 : $post['receiving_id'];

            $arrParam['service_id'] = $post['service_id'];
            $arrParam['project_id'] = $post['project_id'];

            $this->form_validation->set_rules('service_id', 'Tên dịch vụ', 'required');
            $this->form_validation->set_rules('service_name', 'Tên dịch vụ', 'required');
            $this->form_validation->set_rules('project_id', 'Dự án liên quan', 'required');
            $this->form_validation->set_rules('project_name', 'Dự án liên quan', 'required');
            $this->form_validation->set_rules('name', 'Tên', 'required');
            $this->form_validation->set_rules('code', 'Mã', 'required');
            $this->form_validation->set_rules('date_signing', 'Ngày ký', 'required');
            // $this->form_validation->set_rules('date_start', 'Ngày bắt đầu', 'required');
            // $this->form_validation->set_rules('date_expiration', 'Ngày hết hạn', 'required');
            $this->form_validation->set_rules('status', 'Trạng thái', 'callback_validate_luachon');

            if ($post['type'] == 'parttime') {
                $this->form_validation->set_rules('circle', 'Chu kỳ', 'required');
                $this->form_validation->set_rules('bidding', 'Báo trước', 'required');
            }

            if ($this->form_validation->run($this) == FALSE) {
                $errors = $this->form_validation->error_array();
                $flagError = true;
            }

            if (1) { // valid extra
                if (!isset($errors['name'])) {
                    $valid = $this->Contract->valid_contract_name($arrParam['name'], $arrParam['option'], $arrParam['id']);
                    if ($valid == false) {
                        $errors['name'] = 'Tên không được trùng lặp';
                        $flagError = true;
                    }
                }
                if (isset($errors['sale_id'])) {
                    $flagError = true;
                    $response = array('flag' => 'false', 'errors' => $errors);
                    echo json_encode($response);
                    return;

                }

                if (!isset($errors['code'])) {
                    $valid = $this->Contract->valid_contract_code($arrParam['code'], $arrParam['option'], $arrParam['id']);
                    if ($valid == false) {
                        $errors['code'] = 'Mã không được trùng lặp';
                        $flagError = true;
                    }
                }


                if (!empty($arrParam['date_expiration'])) {
                    $duration = distance_between_two_days($arrParam['date_signing'], $arrParam['date_expiration']);
                    if ($duration <= 0) {
                        $flagError = true;
                        $errors['date_expiration'] = 'Ngày hết hạn phải  ngày ký.';
                    }
                }


                if (!empty($arrParam['date_start'])) {
                    $duration = distance_between_two_days($arrParam['date_signing'], $arrParam['date_start']);
                    if ($duration < 0) {
                        $flagError = true;
                        $errors['date_start'] = 'Ngày hiệu liệu phải bắt đầu kể từ ngày ký!';
                    }

                }

                if ($post['type'] == 'parttime') {
                    if ($post['option'] == 'customer' && $post['sale_id'] == 0) {
                        $flagError = true;
                        $errors['sale_code'] = 'Phải chọn 1 đơn hàng';
                    } elseif ($post['option'] == 'supplier' && $post['receiving_id'] == 0) {
                        $flagError = true;
                        $errors['receiving_code'] = 'Phải chọn 1 đơn hàng';
                    }

                    if ($post['option'] == 'customer' && $arrParam['status'] == 'done') {
                        if (!isset($errors['sale_code'])) {
                            $sale_info = $this->Sale->get_sale_info($post['sale_id']);
                            if ($sale_info['was_layaway'] == 1 && $sale_info['suspended'] == 1 && $sale_info['commission_time_method'] == 'contract') {
                                $flagError = true;
                                $errors['sale_code'] = 'Đơn hàng phải được hoàn thành.';
                            }
                        }
                    }
                }

                if ($post['bidding'] > $post['circle']) {
                    $flagError = true;
                    $errors['bidding'] = 'Ngày báo trước không được lớn hơn chu kì.';
                }
            }

            if ($_FILES["file_upload"]['name'] != "") {
                $ext = pathinfo($_FILES["file_upload"]['name'], PATHINFO_EXTENSION);
                $file_name = rewriteUrl($post['file_name']);

                $config['upload_path'] = $upload_dir;
                $config['allowed_types'] = 'jpg|png|pdf|docx|doc|xls|xlsx|zip|zar';
                $config['max_size'] = '20480';
                $config['encrypt_name'] = FALSE;

                $config['file_name'] = $file_name . '.' . $ext;
                if (file_exists($upload_dir . '/' . $file_name . '.' . $ext)) {
                    $config['file_name'] = $file_name . time() . '.' . $ext;
                }

                $this->load->library('upload', $config);

                if ($this->upload->do_upload("file_upload")) {
                    $file_info = $this->upload->data();
                    $arrParam['size'] = $_FILES['file_upload']['size'];
                    $arrParam['extension'] = $ext;
                    $arrParam['file'] = $config['file_name'];

                } else {
                    $flagError = true;
                    $err = $this->upload->display_errors();

                    $err = strip_tags($err);
                    if (isset($this->_fileError[$err]))
                        $errors['file_upload'] = $this->_fileError[$err];
                    else
                        $errors['file_upload'] = strip_tags($err);
                }
            }


            if ($flagError == false) {

                // update contract
                $arrParam['location_id'] = $this->session->userdata['employee_current_location_id'];
                if (empty($arrParam['location_id'])) {
                    $arrParam['location_id'] = $this->Employee->get_logged_in_employee_current_location_id();
                }

                // echo "<pre>";
                // var_dump($arrParam);die();
                $last_id = $this->Contract->save_item($arrParam, array('task' => 'update'));
                $this->update_contract_status($last_id);
                // insert files contract
                $this->Contract->save_contract_file(array('contract_id' => $last_id, 'name_file' => $arrParam['file'], 'note' => $post['note'], 'extension' => $arrParam['extension']));
                // update user commission
                if ($arrParam['status'] == 'done' && $sale_info['commission_time_method'] == 'contract') {
                    $this->db->where("sale_id", $arrParam['sale_id']);
                    $this->db->update('sales', array('commission_status' => 1));
                } else {
                    $this->db->where('sale_id', $arrParam['sale_id']);
                    $this->db->update('phppos_sales', array('suspended' => 0, 'sale_status_id' => 10));
                }
                $response = array('flag' => 'true', 'last_id' => $last_id, 'back_type' => $get['back_type'], 'list_type' => $get['list_type']);

                $_SESSION['notice'] = 'Thêm mới thành công.';
            } else {
                $response = array('flag' => 'false', 'errors' => $errors);
            }

            echo json_encode($response);
        }
    }

    function contract_edit()
    {
        $post = $this->input->post();
        $get = $this->input->get();

        $post['code'] = filter_trim_space($post['code']);
        $post['name'] = filter_trim_space($post['name']);
        $post['note'] = filter_trim_space($post['note']);

        $this->input->post = $post;

        $item = $this->Contract->get_item(array('id' => $post['id']));
        // echo "<pre>"; var_dump($item);die();
        // $upload_dir = DOCUMENT_PATH . 'files/store_' . $item['created_by'];
        $upload_dir = DOCUMENT_PATH . 'files';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, TRUE);
        }

        if (!empty($post)) {
            // echo "<pre>";
            // var_dump($item);die();
            $flagError = false;
            $array_post = array('name', 'code', 'sale_code', 'file_name', 'note');
            foreach ($array_post as $key => $val)
                $post[$key] = trim($val);

            $this->input->post = $post;
            $arrParam = array_merge($post, $this->input->get());
            $check = false;
            if ($arrParam['status'] == 'pause')
                $check = true;


            if ($arrParam['status'] == $item['status'])
                $check = false;
            if (empty($item))
                return;
            // var_dump($check);die();
            // echo "<pre>";
            // var_dump($arrParam['status']);
            // var_dump($item['status']);die();

            // var_dump(expression);
            if(($arrParam['status'] == "progress" && $item['status'] == "pause")){
                $check = true;
            }
            else{
                if (!(($arrParam['status'] == 'pause') || ($arrParam['status'] == $item['status']))) {
                echo json_encode(array('status' => 'ty'));
                return;
            }
            }
          
            $arrParam['old_date_start'] = $item['date_start'];
            $arrParam['old_circle'] = $item['circle'];

            if ($arrParam['option'] == 'customer')
                $arrParam['sale_id'] = (empty($post['sale_id'])) ? $item['sale_id'] : $post['sale_id'];
            elseif ($arrParam['option'] == 'supplier')
                $arrParam['receiving_id'] = (empty($post['receiving_id'])) ? 0 : $post['receiving_id'];

            $item = $this->Contract->get_item(array('id' => $post['id']));
            $arrParam['file'] = $item['file'];
            $arrParam['extension'] = $item['extension'];
            $arrParam['type'] = $item['type'];

            $this->form_validation->set_rules('name', 'Tên', 'required');
            $this->form_validation->set_rules('code', 'Mã', 'required');
            $this->form_validation->set_rules('date_signing', 'Ngày ký', 'required');
            // $this->form_validation->set_rules('date_start', 'Ngày bắt đầu', 'required');
            // $this->form_validation->set_rules('date_expiration', 'Ngày hết hạn', 'required');
            $this->form_validation->set_rules('status', 'Trạng thái', 'callback_validate_luachon');

            if ($post['type'] == 'parttime') {
                $this->form_validation->set_rules('circle', 'Chu kỳ', 'required');
                $this->form_validation->set_rules('bidding', 'Báo trước', 'required');
            }

            if ($this->form_validation->run($this) == FALSE) {
                $errors = $this->form_validation->error_array();
                $flagError = true;
            }

            if (1) {
                if (!isset($errors['name'])) {
                    $valid = $this->Contract->valid_contract_name($arrParam['name'], $arrParam['option'], $arrParam['id']);
                    if ($valid == false) {
                        $errors['name'] = 'Tên không được trùng lặp';
                        $flagError = true;
                    }
                }

                if (!isset($errors['code'])) {
                    $valid = $this->Contract->valid_contract_code($arrParam['code'], $arrParam['option'], $arrParam['id']);
                    if ($valid == false) {
                        $errors['code'] = 'Mã không được trùng lặp';
                        $flagError = true;
                    }
                }


                if (!empty($arrParam['date_expiration'])) {
                    $duration = distance_between_two_days($arrParam['date_signing'], $arrParam['date_expiration']);
                    if ($duration <= 0) {
                        $flagError = true;
                        $errors['date_expiration'] = 'Ngày hết hạn phải sau ngày ký!';
                    }
                }


                if (!empty($arrParam['date_start'])) {
                    $duration = distance_between_two_days($arrParam['date_signing'], $arrParam['date_start']);
                    if ($duration < 0) {
                        $flagError = true;
                        $errors['date_start'] = 'Ngày hiệu liệu phải bắt đầu kể từ ngày ký!';
                    }

                }


                if ($post['bidding'] > $post['circle']) {
                    $flagError = true;
                    $errors['bidding'] = 'Ngày báo trước không được lớn hơn chu kì.';
                }

                if ($post['type'] == 'parttime' && $post['option'] == 'customer') {
                    $sale_info = $this->Sale->get_sale_info($post['sale_id']);
                    if ($sale_info['commission_time_method'] == 'contract') {
                        if ($item['status'] != 'done' && $arrParam['status'] == 'done' && $sale_info['was_layaway'] == 1 && $sale_info['suspended'] == 1) {
                            $flagError = true;
                            $errors['sale_code'] = 'Đơn hàng phải được hoàn thành.';
                        }
                    }
                }
            }

            if ($_FILES["file_upload"]['name'] != "") {
                @unlink($upload_dir . '/' . $item['file']);
                $ext = pathinfo($_FILES["file_upload"]['name'], PATHINFO_EXTENSION);
                $file_name = rewriteUrl($post['file_name']);

                $config['upload_path'] = $upload_dir;
                $config['allowed_types'] = 'jpg|png|pdf|docx|doc|xls|xlsx|zip|zar';
                $config['max_size'] = '20480';
                $config['encrypt_name'] = FALSE;

                $config['file_name'] = $file_name . '.' . $ext;
                if (file_exists($upload_dir . '/' . $file_name . '.' . $ext)) {
                    $config['file_name'] = $file_name . time() . '.' . $ext;
                }

                $this->load->library('upload', $config);

                if ($this->upload->do_upload("file_upload")) {
                    $file_info = $this->upload->data();
                    $arrParam['size'] = $_FILES['file_upload']['size'];
                    $arrParam['extension'] = $ext;
                    $arrParam['file'] = $config['file_name'];

                } else {
                    $flagError = true;
                    $err = $this->upload->display_errors();
                    $err = strip_tags($err);
                    if (isset($fileError[$err]))
                        $errors['file_upload'] = $fileError[$err];
                    else
                        $errors['file_upload'] = strip_tags($err);
                }

            } else {
                if (!empty($post['file_name'])) {
                    $new_file_name = rewriteUrl($post['file_name']) . '.' . $item['extension'];
                    if ($new_file_name != $item['file']) {
                        if (file_exists($upload_dir . '/' . $new_file_name)) {
                            $flagError = true;
                            $errors['file_name'] = 'Tên File đã được sử dụng';
                        } else {
                            rename($upload_dir . '/' . $item['file'], $upload_dir . '/' . $new_file_name);
                            $arrParam['file'] = $new_file_name;
                        }
                    }
                }
            }

            if ($flagError == false) {
                $arrParam['location_id'] = $item['locations_id'];
                $this->Contract->save_contract_file(array('contract_id' => $post['id'], 'name_file' => $arrParam['file'], 'note' => $post['note'], 'extension' => $arrParam['extension']));
                // echo "<pre>";
                // var_dump($arrParam);die();
                $last_id = $this->Contract->save_item($arrParam, array('task' => 'update'));
                if ($post['type'] == 'parttime' && $post['option'] == 'customer' && $sale_info['commission_time_method'] == 'contract') {
                    // update commission status => 1
                    if ($item['status'] != 'done' && $arrParam['status'] == 'done') {
                        $this->db->where("sale_id", $arrParam['sale_id']);
                        $this->db->update('sales', array('commission_status' => 1));

                    } elseif ($item['status'] == 'done' && $arrParam['status'] != 'done') {
                        $this->db->where("sale_id", $arrParam['sale_id']);
                        $this->db->update('sales', array('commission_status' => 0));
                    }
                }

                $response = array('flag' => 'true', 'last_id' => $last_id, 'back_type' => $get['back_type'], 'list_type' => $get['list_type']);

                $_SESSION['notice'] = 'Cập nhật thành công.';
                if (($arrParam['status'] == 'pause') && $check)
                    $this->Employee->update_contract_action($item['id'], 'pause', 2);
                if (($arrParam['status'] == 'progress') && $check)
                    $this->Employee->update_contract_action($item['id'], 'progress', 2);

            } else {
                $response = array('flag' => 'false', 'errors' => $errors);
            }

            echo json_encode($response);
        }

    }

    function contract_payment_delete()
    {
        $post = $this->input->post();
        // var_dump($post);die();
        if (!empty($post)) {
            $a['id'] = $post['cid'][0];
            $id = $this->Contract->get_contract_payment_info($a)['contract_id'];
            $this->Contract->delete_contract_payment($post['cid']);

            $response = array('flag' => 'true', 'msg' => 'Bạn đã thao tác thành công.');
            echo json_encode($response);
            // var_dump($id);
            $this->update_contract_status($id);
        }
    }

    public function index()
    {
        // var_dump($this->module_id);die();
        // var_dump($this->_scopeOfView);die();
        $status = $_GET['status'];
        // var_dump($status);die();
        $items = $this->Contract->list_item(array('option' => 'customer', 'type' => $status));
        // echo "<pre>";
        // echo $this->db->last_query();die();
        // print_r($items);
        # Giá tri hợp đồng
        $contract_value = $this->Contract->get_contract_value(null, $option = 'vat');
        // echo "<pre>";
        // var_dump($contract_value);die();
        foreach ($items as $key => $value) {
            $items[$key]['price'] = 0;
            foreach ($contract_value as $key2 => $value2) {
                if ($items[$key]['id'] == $value2['id']) {
                    $items[$key]['price'] = number_format($value2['has_vat']);
                }
            }

        }

        # Giá trị hợp đồng đã thnah lý nghiệm thu
        $contract_value_done_vat = $this->Contract->get_contract_value($status = array('done', 'liquidated'), $option = 'vat');
        // echo "<pre>";
        // var_dump($contract_value_done_vat);die();

        foreach ($items as $key => $value) {
            $items[$key]['payment_price'] = 0;
            foreach ($contract_value_done_vat as $key3 => $value3) {
                if ($items[$key]['id'] == $value3['id']) {
                    $items[$key]['payment_price'] = number_format($value3['has_vat']);
                }
            }

        }


        #Thêm người tham gia
        $join_people = $this->TasksRelation->get_list_user_relation('is_join');
        foreach ($items as $key => $value) {
            $items[$key]['join'] = "";
            foreach ($join_people as $key2 => $value2) {
                if ($value['task_id'] == $value2['task_id'])
                    $items[$key]['join'] = $value2['user_ids'];
            }
        }

        #Thêm người phụ trách
        $implement_people = $this->TasksRelation->get_list_user_relation();
        foreach ($items as $key => $value) {

            $items[$key]['implement'] = "";
            foreach ($implement_people as $key3 => $value3) {
                if ($value['task_id'] == $value3['task_id'])
                    $items[$key]['implement'] = $value3['user_ids'];
            }
        }



        $data['t'] = count($items);
        $data['items'] = $items;

        // echo "<pre>";
        // print_r($data['items']);die();
        $this->load->view("contracts/list_d13", $data);
    }
    /*
    *   export excel list hop dong
    */
    function export_excel_contract(){
        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();

        // var_dump($this->module_id);die();
        // var_dump($this->_scopeOfView);die();
        $status = $_GET['status'];
        // var_dump($status);die();
        $items = $this->Contract->list_item(array('option' => 'customer', 'type' => $status));
        // echo "<pre>";
        // echo $this->db->last_query();die();
        // var_dump($items);
        # Giá tri hợp đồng
        $contract_value = $this->Contract->get_contract_value(null, $option = 'vat');
        // echo "<pre>";
        // var_dump($contract_value);die();
        foreach ($items as $key => $value) {
            $items[$key]['price'] = 0;
            foreach ($contract_value as $key2 => $value2) {
                if ($items[$key]['id'] == $value2['id']) {
                    $items[$key]['price'] = number_format($value2['has_vat']);
                }
            }

        }

        # Giá trị hợp đồng đã thnah lý nghiệm thu
        $contract_value_done_vat = $this->Contract->get_contract_value($status = array('done', 'liquidated'), $option = 'vat');
        // echo "<pre>";
        // var_dump($contract_value_done_vat);die();

        foreach ($items as $key => $value) {
            $items[$key]['payment_price'] = 0;
            foreach ($contract_value_done_vat as $key3 => $value3) {
                if ($items[$key]['id'] == $value3['id']) {
                    $items[$key]['payment_price'] = number_format($value3['has_vat']);
                }
            }

        }


        #Thêm người tham gia
        $join_people = $this->TasksRelation->get_list_user_relation('is_join');
        foreach ($items as $key => $value) {
            $items[$key]['join'] = "";
            foreach ($join_people as $key2 => $value2) {
                if ($value['task_id'] == $value2['task_id'])
                    $items[$key]['join'] = $value2['user_ids'];
            }
        }

        #Thêm người phụ trách
        $implement_people = $this->TasksRelation->get_list_user_relation();
        foreach ($items as $key => $value) {

            $items[$key]['implement'] = "";
            foreach ($implement_people as $key3 => $value3) {
                if ($value['task_id'] == $value3['task_id'])
                    $items[$key]['implement'] = $value3['user_ids'];
            }
        }

        
        $style_font_title = array(
            'font' => array(
                'size' => 16,
            )
        );
        $style_center = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'font' => array(
                'size' => 13,
            )
        );
        $style_border_table=array(
            'alignment' => array(
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $style_number =array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            )
        );

        $style_backgroud=array(
            'background'=>'ccc',
        );
        $style_font_bold =array(
            'font' =>array(
                'bold' => true,
            )
        );


        $objPHPExcel->getDefaultStyle()->applyFromArray(array('font'=>array('size'=>13,'name'=>'Times New Roman')));
        $objPHPExcel->getDefaultStyle()->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->mergeCells('B1:H1');
        $objPHPExcel->getActiveSheet()->setCellValue('B1','DANH SÁCH HỢP ĐỒNG');
        $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($style_font_bold);
        $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($style_center);
        $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($style_font_title);
        // $objPHPExcel->getActiveSheet()->setCellValue('E2',$add_location);
        // $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($style_center);
        // $objPHPExcel->getActiveSheet()->getStyle('E2')->applyFromArray($style_font_bold);

        $name_col = ['A','B','C','D','E','F','G','H','I','J','K'];
        $data_title =['STT','Số hiệu hợp đồng','Tên hợp đồng','Tên dịch vụ','Khách hàng','Giá trị hợp đồng','Giá trị nghiệm thu/thanh lý','Trạng thái hợp đồng','Ngày ký','Người phụ trách','Người tham gia'];
        $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
        ->setWidth(25);
        for ($i=0; $i < count($data_title) ; $i++) { 
            // $objPHPExcel->getActiveSheet()->getColumnDimension($name_col[$i])->setWidth(25);
            $objPHPExcel->getActiveSheet()->setCellValue($name_col[$i].'3',$data_title[$i]);
            $objPHPExcel->getActiveSheet()->getStyle($name_col[$i].'3')->applyFromArray($style_center);
            $objPHPExcel->getActiveSheet()->getStyle($name_col[$i].'3')->applyFromArray($style_font_bold);
        }

        $row_start = 4;
        $stt=1;
        foreach ($items as $key => $value) {

            $objPHPExcel->getActiveSheet()->getStyle('A'.$row_start)->getAlignment()->setWrapText(true); 
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$row_start,$stt);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$row_start,htmlspecialchars($value['code']));
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$row_start,htmlspecialchars($value['name']));
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$row_start,htmlspecialchars($value['item_name']));
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$row_start,htmlspecialchars($value['customer_name']));
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$row_start,htmlspecialchars($value['price']));
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$row_start,htmlspecialchars($value['payment_price']));
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$row_start,htmlspecialchars($value['status']));
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$row_start,htmlspecialchars($value['date_signing']));
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$row_start,htmlspecialchars($value['implement']));
            $objPHPExcel->getActiveSheet()->setCellValue('K'.$row_start,htmlspecialchars($value['join']));

            $objPHPExcel->getActiveSheet()->getStyle('F'.$row_start)->applyFromArray($style_number);
            $objPHPExcel->getActiveSheet()->getStyle('I'.$row_start)->applyFromArray($style_number);
            $objPHPExcel->getActiveSheet()->getStyle('G'.$row_start)->applyFromArray($style_number);
            $objPHPExcel->getActiveSheet()->getStyle('A'.$row_start)->applyFromArray($style_center);
            $stt++;$row_start++;

        }
        $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(30);
        for ($i=3; $i < $row_start; $i++) { 
            $objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(30);
            for ($j=0; $j < count($name_col) ; $j++) { 
                $objPHPExcel->getActiveSheet()->getStyle($name_col[$j].$i)->applyFromArray($style_border_table);
            }
        }
        $title = "Danh_sach_hop_dong";
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename='.$title.'.xls');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    public function circle()
    {
        $data = array();
        $data['option'] = $this->_option;
        $data['currrent_page'] = $this->uri->segment(4, 1);
        $data['controller_name'] = $this->uri->segment(1);

        $params = $_SESSION['contract_' . $data['option'] . '_filter'];
        $params['option'] = $data['option'];
        $data['list_count'] = $this->Contract->count_item($params);

        $params = $_SESSION['contract_' . $data['option'] . '_expired_filter'];
        $params['option'] = $data['option'];
        $data['expired_count'] = $this->Contract->count_item($params, array('task' => 'expired'));

        $this->load->view("contracts/circle", $data);
    }

    function contract_circle_store()
    {
        $post = $this->input->post();
        $arrParam = array_merge($post, $this->input->get());
        if (!empty($post)) {
            $key_filter = 'contract_' . $arrParam['option'] . '_circle_filter';
            $_SESSION[$key_filter] = array();
            $arrParam['paginator'] = $this->_paginator;
            $arrParam['page'] = $this->uri->segment(3, 1);

            $config['base_url'] = base_url() . 'contract/contract_circle_store';
            $config['total_rows'] = $this->Contract->count_circle_contract($arrParam);

            $config['per_page'] = $arrParam['paginator']['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
            //$config['per_page'] = $arrParam['paginator']['per_page'] = 1;

            $config['uri_segment'] = 3;
            $config['use_page_numbers'] = TRUE;

            $items = $this->Contract->list_contract_circle($arrParam);

            $this->load->library("pagination");
            $this->pagination->initialize($config);
            $this->pagination->createConfig('front-end');

            $pagination = $this->pagination->create_ajax();

            $result = array('count' => $config['total_rows'], 'items' => $items, 'pagination' => $pagination);
            echo json_encode($result);
        }
    }

    function expired()
    {
        $data = array();
        $data['option'] = $this->_option;
        $data['currrent_page'] = $this->uri->segment(4, 1);
        $data['controller_name'] = $this->uri->segment(1);

        $params = array();
        $params = $_SESSION['contract_' . $data['option'] . '_filter'];
        $params['option'] = $data['option'];
        $data['list_count'] = $this->Contract->count_item($params);


        $params = array();
        $params = $_SESSION['contract_' . $data['option'] . '_circle_filter'];
        $params['option'] = $data['option'];
        $data['circle_count'] = $this->Contract->count_circle_contract($params);

        $this->load->view("contracts/expired", $data);
    }

    function contract_expired_store()
    {
        $post = $this->input->post();
        $arrParam = array_merge($post, $this->input->get());
        if (!empty($post)) {
            $key_filter = 'contract_' . $arrParam['option'] . '_expired_filter';
            $_SESSION[$key_filter] = array();
            $arrParam['paginator'] = $this->_paginator;
            $arrParam['page'] = $this->uri->segment(3, 1);

            $config['base_url'] = base_url() . 'contract/contract_expired_store';
            $config['total_rows'] = $this->Contract->count_item($arrParam, array('task' => 'expired'));

            $config['per_page'] = $arrParam['paginator']['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
            //$config['per_page'] = $arrParam['paginator']['per_page'] = 1;

            $config['uri_segment'] = 3;
            $config['use_page_numbers'] = TRUE;

            $items = $this->Contract->list_item($arrParam, array('task' => 'expired'));

            $this->load->library("pagination");
            $this->pagination->initialize($config);
            $this->pagination->createConfig('front-end');

            $pagination = $this->pagination->create_ajax();

            $result = array('count' => $config['total_rows'],

                'items' => $items,
                'pagination' => $pagination);
            echo json_encode($result);
        }
    }

    function contract_handling()
    {
        $post = $this->input->post();
        if (!empty($post)) {
            $data['date_circle_solve'] = @date("Y-m-d");
            $this->db->where("id", $post['contract_id']);

            $this->db->update('contract', $data);

            $this->db->flush_cache();
        }
    }

    function contract_expired_handling()
    {
        $post = $this->input->post();
        if (!empty($post)) {
            $data['date_expiration_solve'] = @date("Y-m-d");
            $this->db->where("id", $post['contract_id']);

            $this->db->update('contract', $data);

            $this->db->flush_cache();
        }
    }

    function contract_payment_vat()
    {
        $post = $this->input->post();
        if (!empty($post)) {
            $this->Contract->save_contract_payment($post, array('task' => 'update-payment-vat'));
        }
    }

    function contract_payment_save()
    {
        $post = $this->input->post();
        // var_dump($post);die();
        $post['c_payment_name'] = filter_trim_space($post['c_payment_name']);
        $this->input->post = $post;

        $arrParams = array_merge($post, $this->input->get());
        // echo "<pre>";
        // var_dump($arrParams);die();

        // if($arrParams['payment_id'] ==-1)
        // {
        //     $check = $this->check_status($arrParams['contract_id']);
        //     if($check)
        //     {
        //         $response = array('flag'=>'warning', 'canhbao'=>"Hợp đồng này đã thanh lý, bạn không thể thêm giai đoạn được nữa");
        //         echo json_encode($response);
        //         return;
        //     }

        // }
        // else{
        $a['id'] = $arrParams['payment_id'];
        $info = $this->Contract->get_contract_payment_info($a);
        if ($arrParams['c_status'] == "liquidated") {
            if ($this->check_status($arrParams['contract_id'])) {

                if ($info['c_status'] != "liquidated") {
                    $response = array('flag' => 'warning', 'canhbao' => "Hợp đồng này đã thanh lý, không thể thêm giai đoạn thanh lý nữa");
                    echo json_encode($response);
                    return;
                }

            }
        }
        // }


        if (!empty($post)) {
            $flagError = false;
            $id = $arrParams['payment_id'];

            $this->form_validation->set_rules('c_task_id', 'Công việc', 'required');
            $this->form_validation->set_rules('c_payment_name', 'Tiêu đề', 'required');
            if (!empty($post['c_status'])) {
                $this->form_validation->set_rules('c_date_payment', 'Ngày thanh toán', 'required');
            }
            $this->form_validation->set_rules('c_payment_price', 'Số tiền', 'required');

            if ($this->form_validation->run($this) == FALSE) {
                $flagError = true;
                $errors = $this->form_validation->error_array();
                $errors['c_task_id'] = "Chọn công việc";
            }

            if (!isset($errors['c_payment_name'])) {
                $valid_name = $this->Contract->valid_contract_payment_name($post['c_payment_name'], $id, $post['contract_id']);
                if ($valid_name == false) {
                    $flagError = true;
                    $errors['c_payment_name'] = 'Tiêu đề không được trùng lặp.';
                }
            }

            if ($flagError == true) {
                $response = array('flag' => 'false', 'errors' => $errors);
            } else {
                $this->Contract->save_contract_payment($arrParams, array('task' => 'update'));
                $response = array('flag' => 'true');
            }

            $t1 = $this->Contract->get_item(array('id' => $arrParams['contract_id']))['status'];
            // var_dump($t1);die();
            // echo $this->db->last_query();die();
            $this->update_contract_status($arrParams['contract_id']);
            $t2 = $this->check_contract_status($arrParams['contract_id'])['status'];
            if ($t1 != $t2) {
                // var_dump($t1);var_dump($t2);die();
                $this->Employee->update_contract_action($arrParams['contract_id'], $t2, 1);
            }

            echo json_encode($response);
        }
    }


    #Cập nhật lại trạng thái hợp đồng
    function update_contract_status($contract_id)
    {
        $data = $this->check_contract_status($contract_id);
        $this->db->where('id', $contract_id);
        $this->db->update('phppos_contract', $data);
        // echo $this->db->last_query();die();
    }

    function check_status($id)
    {
        $this->db->where('status', 'liquidated');
        $this->db->where('id', $id);
        return $this->db->get('phppos_contract')->row_array();
    }

    #Trả về trạng thái của thanh toán hợp đồng
    function check_contract_status($contract_id)
    {

        #1: đang thực hiện
        #2: Nghiệm thu
        #3: Đã thanh lý
        $this->db->where('contract_id', $contract_id);
        $result = $this->db->get('phppos_contract_payment')->result_array();
        // var_dump($result);die();
        $r['status'] = 'progress';
        foreach ($result as $key => $value) {
            if ($value['c_status'] == 'liquidated') {
                $r['status'] = 'liquidated';
                // $r['date_liquidated'] = $value['date_payment'];
                return $r;
            }

            if ($value['c_status'] == 'done') {
                $r['status'] = 'done';
            }


        }

        return $r;


    }

    function contract_store()
    {
        $post = $this->input->post();
        $arrParam = array_merge($post, $this->input->get());
        if (!empty($post)) {
            $key_filter = 'contract_' . $arrParam['option'] . '_filter';
            $_SESSION[$key_filter] = array();
            $arrParam['paginator'] = $this->_paginator;
            $arrParam['page'] = $this->uri->segment(3, 1);

            $config['base_url'] = base_url() . 'contract/contract_store';

            $config['per_page'] = $arrParam['paginator']['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;

            $config['uri_segment'] = 3;
            $config['use_page_numbers'] = TRUE;

            $items = $this->Contract->list_item($arrParam);

            # Giá tri hợp đồng
            $contract_value = $this->Contract->get_contract_value();
            // echo "<pre>";
            // var_dump($contract_value);die();
            foreach ($items as $key => $value) {
                $items[$key]['price'] = 0;
                foreach ($contract_value as $key2 => $value2) {
                    if ($items[$key]['id'] == $value2['id']) {
                        $items[$key]['price'] = number_format($value2['total_value']);
                    }
                }

            }

            # Giá trị hợp đồng đã thnah lý nghiệm thu
            $contract_value_done_vat = $this->Contract->get_contract_value($status = array('done', 'liquidated'), $option = 'vat');
            // echo "<pre>";
            // var_dump($contract_value_done_vat);die();

            foreach ($items as $key => $value) {
                $items[$key]['payment_price'] = 0;
                foreach ($contract_value_done_vat as $key3 => $value3) {
                    if ($items[$key]['id'] == $value3['id']) {
                        $items[$key]['payment_price'] = number_format($value3['has_vat']);
                    }
                }

            }


            #Thêm người tham gia
            $join_people = $this->TasksRelation->get_list_user_relation('is_join');
            foreach ($items as $key => $value) {
                $items[$key]['join'] = "";
                foreach ($join_people as $key2 => $value2) {
                    if ($value['task_id'] == $value2['task_id'])
                        $items[$key]['join'] = $value2['user_ids'];
                }
            }

            #Thêm người phụ trách
            $implement_people = $this->TasksRelation->get_list_user_relation();
            foreach ($items as $key => $value) {

                $items[$key]['implement'] = "";
                foreach ($implement_people as $key3 => $value3) {
                    if ($value['task_id'] == $value3['task_id'])
                        $items[$key]['implement'] = $value3['user_ids'];
                }
            }


            // echo "<pre>";
            // var_dump($items);die;
            // echo $this->db->last_query();die();
            // var_dump($arrParam['option']);die();
            $config['total_rows'] = count($items);

            $this->load->library("pagination");
            $this->pagination->initialize($config);
            $this->pagination->createConfig('front-end');

            $pagination = $this->pagination->create_ajax();

            $result = array('count' => $config['total_rows'],
                'items' => $items,
                'pagination' => $pagination,
                'new_contracts_count' => $this->Contract->count_item(array(
                    'start_date' => date('Y-m-d') . ' ' . '00:00:00',
                    'end_date' => date('Y-m-d') . ' ' . '23:59:59',
                    'option' => 'customer'
                ), null));
            echo json_encode($result);
        }
    }


    function contract_payment_form()
    {
        $data = $this->input->post();

        $data['tasks'] = $this->Task->get_task_by_project_id($data['project_id']);
        // $data['htmlop'] = $this->Contract->showCategories($data['tasks'],$data['project_id']);
        if (isset($data['html'])) {
            if ($data['id'] > 0) {
                $data['item'] = $this->Contract->get_contract_payment_info(array('id' => $data['id']));
            }
            $data['html'] = false;
        } else {
            $data['html'] = true;
        }
        $data['project_id'] = $data['project_id'];
        $this->load->view('contracts/form_contract_payment', $data);
    }

    function contract_payment_store()
    {
        $post = $this->input->post();
        if (!empty($post)) {
            $arrParam = array_merge($post, $this->input->get());
            $arrParam['paginator'] = $this->_paginator;
            $arrParam['page'] = $this->uri->segment(3, 1);

            $contract_info = $this->Contract->get_item(array('id' => $arrParam['contract_id']));

            if ($contract_info['option'] == 'customer')
                $order_info = $this->sale_lib->getSale($contract_info['sale_id'], $this->config);
            elseif ($contract_info['option'] == 'supplier')
                $order_info = $this->receiving_lib->get_receiving($contract_info['receiving_id']);

            $arrParam['contract_info'] = $contract_info;
            $arrParam['order_info'] = $order_info;

            $config['base_url'] = base_url() . 'contracts/contract_payment_store';
            $config['total_rows'] = $this->Contract->count_contract_payment($arrParam);

            $config['per_page'] = $arrParam['paginator']['per_page'] = 5;

            $config['uri_segment'] = 3;
            $config['use_page_numbers'] = TRUE;

            $items = $this->Contract->list_contract_payment($arrParam);
// echo "<pre>";var_dump($items); die();
            $this->load->library("pagination");
            $this->pagination->initialize($config);
            $this->pagination->createConfig('front-end');

            $pagination = $this->pagination->create_ajax();

            $result = array('count' => $config['total_rows'], 'items' => $items, 'pagination' => $pagination);
            echo json_encode($result);
        }
    }

    function contract_payment_detail_list()
    {
        $data = $this->input->get();
        $this->load->view('contracts/contract_payment_detail_list', $data);
    }

    function contract_other_info()
    {
        $post = $this->input->post();
        if (!empty($post)) {
            $item = $this->Contract->get_item(array('id' => $post['contract_id']));
            echo json_encode($item);
        }
    }

    function contract_payment_detail_store()
    {
        $post = $this->input->post();
        if (!empty($post)) {
            $_SESSION['contract_payment_detail_filter'] = array();
            $arrParam = array_merge($post, $this->input->get());
            $arrParam['paginator'] = $this->_paginator;
            $arrParam['page'] = $this->uri->segment(3, 1);

            $config['base_url'] = base_url() . 'contracts/contract_payment_detail_store';
            $config['total_rows'] = $this->Contract->count_payment_detail($arrParam);

            //$config['per_page'] = $arrParam['paginator']['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
            $config['per_page'] = $arrParam['paginator']['per_page'] = 10;
            $config['uri_segment'] = 3;
            $config['use_page_numbers'] = TRUE;

            $items = $this->Contract->list_payment_detail($arrParam);

            $this->load->library("pagination");
            $this->pagination->initialize($config);
            $this->pagination->createConfig('front-end');

            $pagination = $this->pagination->create_ajax();

            $result = array('count' => $config['total_rows'], 'items' => $items, 'pagination' => $pagination);
            echo json_encode($result);
        }
    }

    function contract_payment_detail_delete()
    {
        $post = $this->input->post();
        if (!empty($post)) {
            $this->Contract->delete_contract_payment_detail($post['cid'], $post['param']);

            $response = array('flag' => 'true', 'msg' => 'Xóa thành công.');
            echo json_encode($response);
        }
    }

    function contract_payment_detail_form()
    {
        $data = $this->input->post();
        if ($data['id'] != -1) {
            $data['item'] = $this->Contract->get_contract_payment_detail_info(array('id' => $data['id']));
        }

        $this->load->view('contracts/form_contract_payment_detail', $data);
    }

    function contract_payment_detail_save()
    {
        $post = $this->input->post();
        $post['name'] = filter_trim_space($post['name']);
        $post['price'] = filter_trim_space($post['price']);
        $post['note'] = filter_trim_space($post['note']);

        $this->input->post = $post;
        $arrParams = array_merge($post, $this->input->get());

        if (!empty($post)) {
            $flagError = false;

            $this->form_validation->set_rules('name', 'Tiêu đề', 'required');
            $this->form_validation->set_rules('price', 'Số tiền', 'required');
        }

        if ($this->form_validation->run($this) == FALSE) {
            $flagError = true;
            $errors = $this->form_validation->error_array();
        }

        if ($flagError == true) {
            $response = array('flag' => 'false', 'errors' => $errors);
        } else {
            $this->Contract->save_contract_payment_detail($arrParams, array('task' => 'update'));
            $response = array('flag' => 'true');
        }

        echo json_encode($response);

    }

    function contract_delivery_form()
    {
        $arrParams = array_merge($this->input->post(), $this->input->get());
        $data = $this->input->post();
        $data['slb_contract_payment'] = $this->Contract->items_selectbox_contract_payment($arrParams['contract_id']);

        $contract_info = $this->Contract->get_item(array('id' => $arrParams['contract_id']));
        if (empty($contract_info))
            return;

        if ($arrParams['delivery_id'] == -1) {
            $delivery = $this->Contract->get_sum_quantity_from_delivery($arrParams['contract_id']);

            if ($contract_info['option'] == 'customer') {
                $order = $this->sale_lib->getSale($contract_info['sale_id'], $this->config);
                $items = $this->sale_lib->get_delivery_items($order['cart'], $delivery);

            } elseif ($contract_info['option'] == 'supplier') {
                $order = $this->receiving_lib->get_receiving($contract_info['receiving_id']);
                $items = $this->receiving_lib->get_delivery_items($order['cart'], $delivery);
            }

            $data['items'] = $items;
        } else {
            $info = $this->Contract->get_contract_delivery_detail_info(array('id' => $arrParams['delivery_id']));
            $data['information'] = $info;
        }

        $data['delivery_id'] = $arrParams['delivery_id'];
        $this->load->view('contracts/form_contract_delivery', $data);
    }

    function contract_delivery_save()
    {
        $post = $this->input->post();
        $arrParams = array_merge($post, $this->input->get());
        if (!empty($post)) {
            $flagError = 'false';
            $this->form_validation->set_rules('contract_payment_id', 'Giai đoạn', 'callback_validate_luachon');
            $this->form_validation->set_rules('date', 'Thời gian', 'required');
            $this->form_validation->set_rules('company_name', 'Công ty', 'required');
            $this->form_validation->set_rules('address', 'Địa chỉ', 'required');

            if ($arrParams['contract_delivery_id'] == -1) {
                if ($this->form_validation->run($this) == FALSE) {
                    $flagError = 'true';
                    $errors = $this->form_validation->error_array();
                }

                if ($flagError == 'false') {
                    $contract_info = $this->Contract->get_item(array('id' => $arrParams['contract_id']));
                    $delivery = $this->Contract->get_sum_quantity_from_delivery($arrParams['contract_id']);
                    $order = $this->sale_lib->getSale($contract_info['sale_id'], $this->config);
                    $items = $this->sale_lib->get_delivery_items($order['cart'], $delivery, 'all');

                    if (count($arrParams['quantity']) > 0) {
                        foreach ($arrParams['quantity'] as $key => $qty) {
                            if ($qty > 0) {
                                if (isset($items[$key]) && $qty > $items[$key]['limit']) {
                                    $flagError = 'no-store';
                                    $update_limit_msg[] = $items[$key]['name'] . ' không đáp ứng đủ số lượng.';
                                    $update_limit[$key] = number_format($items[$key]['limit'], 2) . '/' . number_format($items[$key]['quantity'], 2);
                                } else {
                                    $array = explode('-', $key);
                                    if ($array[0] == 'i') {
                                        $deliver_items[] = array(
                                            'contract_id' => $arrParams['contract_id'],
                                            'item_id' => $array[1],
                                            'line' => $array[2],
                                            'measure_id' => $arrParams['measure_id'][$key],
                                            'quantity' => $qty

                                        );
                                    } elseif ($array[0] == 'k') {
                                        $deliver_items_kit[] = array(
                                            'contract_id' => $arrParams['contract_id'],
                                            'item_kit_id' => $array[1],
                                            'line' => $array[2],
                                            'measure_id' => $arrParams['measure_id'][$key],
                                            'quantity' => $qty
                                        );
                                    }
                                }
                            }
                        }

                        $arrParams['deliver_items'] = $deliver_items;
                        $arrParams['deliver_items_kit'] = $deliver_items_kit;
                    } else
                    $flagError = 'no-limit';
                }


                if ($flagError == 'true') {
                    $response = array('flag' => 'false', 'errors' => $errors);
                } elseif ($flagError == 'no-store')
                $response = array('flag' => 'no-store', 'update_limit' => $update_limit, 'update_limit_msg' => $update_limit_msg);
                elseif ($flagError == 'no-limit') {
                    $response = array('flag' => 'no-limit');
                } else {
                    $this->Contract->save_contract_delivery($arrParams, array('task' => 'update'));
                    $response = array('flag' => 'true');
                }

            } else {
                if ($this->form_validation->run($this) == FALSE) {
                    $flagError = 'true';
                    $errors = $this->form_validation->error_array();
                }

                if ($flagError == 'true') {
                    $response = array('flag' => 'false', 'errors' => $errors);
                } else {
                    $this->Contract->save_contract_delivery($arrParams, array('task' => 'update'));
                    $response = array('flag' => 'true');
                }
            }

            echo json_encode($response);
        }

    }

    function contract_document_form()
    {
        $data = array_merge($this->input->post(), $this->input->get());
        $quote_code =null;
        if($data['contract_id']){
            $quote_code = $this->get_code($data['contract_id']);
        }
        $data['slb_quote_contract'] = $this->Customer->item_select_quote_contract();

        $this->load->view('contracts/form_contract_document', $data);
    }

    function get_code($id){
        $this->db->select('i.product_id');
        $this->db->from('phppos_contract as c');
        $this->db->join('phppos_items as i', 'i.item_id = c.item_id');
        $this->db->where('c.id', $id);
        return $this->db->get()->row_array()['product_id'];
    }
    function do_make_file_download()
    {


        $get = $this->input->get();
        $contract_id = $get['contract_id'];
        $quote_contract_id = $get['quote_contract_id'];
        $this->db->where('id', $contract_id);
        $sale_id = $this->db->get('phppos_contract')->row();
        if(empty($sale_id))
            return;
        else
            $sale_id = $sale_id->sale_id;
        $id = $quote_contract_id;
// // var_dump($sale_id);die();
        $sale = $this->getSale($sale_id);
        $ext = '.docx';


        $file = 'document/bieumau/'.$id.$ext;


        $phpword = new \PhpOffice\PhpWord\TemplateProcessor($file);
        // var_dump($phpword);die();
        $phpword->setValue('{TEN_KH}',$sale['customer']);
        $phpword->setValue('{CT_KH}',$sale['customer_company_name']);
        $phpword->setValue('{DIA_CHI_1_KH}',$sale['customer_address_1']);
        $phpword->setValue('{DIA_CHI_2_KH}',$sale['customer_address_2']);
        $phpword->setValue('{SDT_KH}',$sale['customer_phone']);
        $phpword->setValue('{CHUCVU_KH}',isset($sale['customer_position'])?$sale['customer_position']:'');
        $phpword->setValue('{TKNH_KH}',isset($data['customer_account_number'])?$data['customer_account_number']:'');
        $phpword->setValue('{CT_KH}',$sale['customer_email']);
        $phpword->saveAs('document/edited'.$ext);
        // var_dump('document/edited'.$ext);die();
        force_download(FCPATH.'document/edited'.$ext,null);
        // $quote_contract_info = $this->Customer->get_info_quotes_contract($get['quote_contract_id']);
        // $content_string = $quote_contract_info['content_quotes_contract'];
        // $alias_title = rewriteUrl($quote_contract_info['title_quotes_contract']);

        // $html_string = $this->contract_lib->convertTemplate($get['contract_id'], $content_string);

        // $this->load->view("contracts/do_make_file_download", array('html_string' => $html_string, 'alias_title' => $alias_title));
        // header("Refresh:0");
    }

    function rule_contract_info()
    {
        $post = $this->input->post();
        $arrParams = array_merge($post, $this->input->get());

        if (!empty($post)) {
            $item = $this->Contract->get_item($arrParams, array('task' => 'full'));
            echo json_encode($item);
        }
    }

    function contract_sale_info()
    {
        $post = $this->input->post();
        $get = $this->input->get();
        $arrParams = array_merge($post, $this->input->get());
        if (!empty($post)) {
            $discount_id = $this->Item->get_item_id_for_flat_discount_item();
            $contract_info = $this->Contract->get_item(array('id' => $arrParams['contract_id']));

            if ($contract_info['option'] == 'customer')
                $sale_info = $this->sale_lib->getSale($contract_info['sale_id'], $this->config);
            elseif ($contract_info['option'] == 'supplier')
                $sale_info = $this->receiving_lib->get_receiving($contract_info['receiving_id']);

            $data['sale_info'] = $sale_info;
            $data['discount_id'] = $discount_id;

            $this->load->view('contracts/partial/contract_sale_info', $data);
        }
    }

    function contract_delivery_item_detail_list()
    {
        $get = $this->input->get();
        $items = $this->Contract->list_contract_delivery_items($get['delivery_id']);

        if (!empty($items)) {
            $tmp = current($items);
            $contract_id = $tmp['contract_id'];
            $contract_info = $this->Contract->get_item(array('id' => $contract_id));

            if ($contract_info['option'] == 'customer')
                $sale_info = $this->sale_lib->getSale($contract_info['sale_id'], $this->config);
            else {
                $sale_info = $this->receiving_lib->get_receiving($contract_info['receiving_id']);
            }

            $sale_info_items = array();
            foreach ($sale_info['cart'] as $val) {
                if ($val['quantity'] > 0) {
                    if (isset($val['item_id'])) {
                        $key = 'i-' . $val['item_id'] . '-' . $val['line'];
                        $sale_info_items[$key] = $val;
                    }

                    if (isset($val['item_kit_id'])) {
                        $key = 'i-' . $val['item_kit_id'] . '-' . $val['line'];
                        $sale_info_items[$key] = $val;
                    }
                }
            }

            if (!empty($items)) {
                foreach ($items as $key => &$item) {
                    $item['name'] = $sale_info_items[$key]['name'];
                    $item['price'] = $sale_info_items[$key]['price'];
                    $item['measure'] = $sale_info_items[$key]['measure'];
                }
            }

            $data['items'] = $items;
        }

        $this->load->view('contracts/contract_delivery_item_detail_list', $data);
    }

    function contract_delivery_store()
    {
        $post = $this->input->post();
        if (!empty($post)) {
            $_SESSION['contract_delivery_filter'] = array();
            $arrParam = array_merge($post, $this->input->get());
            $arrParam['paginator'] = $this->_paginator;
            $arrParam['page'] = $this->uri->segment(3, 1);

            $config['base_url'] = base_url() . 'customers/contract_payment_detail_store';
            $config['total_rows'] = $this->Contract->count_contract_delivery($arrParam);

            //$config['per_page'] = $arrParam['paginator']['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
            $config['per_page'] = $arrParam['paginator']['per_page'] = 5;
            $config['uri_segment'] = 3;
            $config['use_page_numbers'] = TRUE;

            $items = $this->Contract->list_contract_delivery($arrParam);

            $this->load->library("pagination");
            $this->pagination->initialize($config);
            $this->pagination->createConfig('front-end');

            $pagination = $this->pagination->create_ajax();

            $result = array('count' => $config['total_rows'], 'items' => $items, 'pagination' => $pagination);
            echo json_encode($result);
        }
    }

    function contract_delivery_delete()
    {
        $post = $this->input->post();
        if (!empty($post)) {
            $this->Contract->delete_contract_delivery($post['cid']);

            $response = array('flag' => 'true', 'msg' => 'Xóa thành công.');
            echo json_encode($response);
        }
    }

    function contract_delete()
    {

        $this->check_action_permission('delete');
        $post = $this->input->post();
        if (!empty($post)) {
            $this->Contract->delete_contract($post['cid']);

            $response = array('flag' => 'true', 'msg' => 'Xóa thành công.');
            echo json_encode($response);
        }
    }

    function get_contract_parttime_code()
    {
        $post = $this->input->post();
        if (!empty($post)) {
            $month = date('m');
            $year = date("y");

            $quantity = $this->Contract->get_quantity_contract_of_current_month(array('code' => $post['code']));
            $quantity = $quantity + 1;
            if ($quantity <= 99)
                $quantity = '00' . $quantity;

            $result = $post['code'] . $year . $month . $quantity;

            echo $result;
        }
    }

    function load_contract_section()
    {
        $get = $this->input->get();
        if (!empty($get['sale_id'])) {
            $this->load->model('Task');
            $this->load->model('Sale');
            $data['sale_id'] = $get['sale_id'];
            $sale_info = $this->Sale->get_info($data['sale_id'])->row_array();
            $task_info = $this->Task->get_task_item($sale_info['task_id']);
            $mimi = $this->Sale->get_item_by_sale_d13($get['sale_id']);
            // var_dump($mimi);die();
            $item['service_name'] = $mimi['service_name'];
            $item['service_id'] = $mimi['service_id'];
            $item['service_type_name'] = $mimi['service_type_name'];
            $item['project_id'] = $task_info['id'];
            $item['project_name'] = $task_info['name'];
            $item['customer_id'] = $task_info['customer_ids'];
        }

        $type = (!isset($get['type'])) ? 'rule' : $get['type'];
        $total = $this->Contract->count_all();
        $current_location = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id());
        $data = array();
        $data['contract_rule'] = $this->Contract->item_select_box(array('type' => 'rule', 'option' => $get['option']));
        $item['type'] = $type;
        $data['option'] = $get['option'];
        $item['code'] = ($total + 1) . '/' . date('Y') . '/VCBS-TVTCDN-' . $current_location->code;
        $data['item'] = $item;
        // echo "<pre>";var_dump($data['item']);die();
        $this->load->view('contracts/partial/' . $get['type'] . '_section', $data);
    }

    function load_template_mail()
    {
        $post = $this->input->post();
        if (!empty($post)) {
            $item = $this->Customer->constract_list(array('id_quotes_contract' => $post['template_id']));
            if (!empty($item[0]['content'])) {
                $html_string = $this->contract_lib->convertTemplate($post['contract_id'], $item[0]['content']);
            } else {
                $html_string = '';
            }


            echo $html_string;
        }
    }

    function validate_email($value)
    {
        $pattern = '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD';
        if (preg_match($pattern, $value) === 1) {
            return true;
        } else {
            $this->form_validation->set_message('validate_email', "Sai định dạng email.");
            return false;
        }

    }

    function validate_luachon($value)
    {
        if ($value == -1) {
            $this->form_validation->set_message('validate_luachon', "Phải lựa chọn.");
            return false;
        } else
        return true;
    }

    function validate_select($value)
    {
        if ($value == 0) {
            $this->form_validation->set_message('validate_select', "Phải lựa chọn.");
            return false;
        } else
        return true;
    }

    function validate_unique_custom($field_value, $value)
    {
        $array = explode('-', $value);
        $table = $array[0];
        $field = $array[1];
        $id = $array[2];
        $id_field = $array[3];

        $this->db->select('COUNT(' . $id_field . ') AS totalItem')
        ->from($table)
        ->where($field, $field_value)
        ->where($id_field . ' != ' . $id);

        $query = $this->db->get();

        $total = $query->row()->totalItem;
        if ($total == 0)
            return true;
        else {
            $this->form_validation->set_message('validate_unique_custom', "'$field_value' đã tồn tại");
            return false;
        }
    }

    function validate_unique($field_value, $value)
    {
        $array = explode('-', $value);
        $table = $array[0];
        $field = $array[1];
        $id = $array[2];

        $this->db->select('COUNT(id) AS totalItem')
        ->from($table)
        ->where($field, $field_value)
        ->where('id != ' . $id);

        $query = $this->db->get();

        $total = $query->row()->totalItem;
        if ($total == 0)
            return true;
        else {
            $this->form_validation->set_message('validate_unique', "'$field_value' đã tồn tại");
            return false;
        }
    }


#check sale

    function check_sale_id($sale_id)
    {
        if (!is_integer($sale_id))
            return "false";
        $this->db->select('c.sale_id');
        $this->db->from('phppos_contract as c');
        $this->db->where('c.sale_id', $sale_id);
        return ($this->db->get()->row_array());

    }

#CÔng việc không tạo doanh thu
    function contract_no_revenue()
    {
        $this->load->view('contracts/contract_no_revenue');
    }


    function list_status_changing()
    {
        $data['count'] = $this->Employee->get_contract_status_changing();
        $config = $this->set_config();
        $config['base_url'] = base_url('contracts/list_status_changing');
        $config['total_rows'] = count($data['count']);
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();

        $arrParam = array();
        $arrParam['limit'] = $config['per_page'] = 10;
        $arrParam['offset'] = intval(($this->uri->segment(3, 1)) - 1) * $config['per_page'];
        $list = $this->Employee->get_contract_status_changing(null, $arrParam);
        // echo $this->db->last_query();die();
        // echo "<pre>";
        // var_dump($data['list']);die();
        $data['list'] = $list;
        $this->load->view('contracts/status_changing', $data);
    }

    function delete_file_contract()
    {
        $id = $this->input->post('file_id');
        if ($this->db->delete('phppos_contract_files', array('id' => $id))) {
            echo json_encode(array('success' => 'success'));
        } else {
            echo json_encode(array('success' => 'errors'));
        }
    }

    function contract_alert()
    {
        $id = $this->Employee->get_logged_in_employee_info()->id;
        $data['list']= $this->Employee->get_contract_status_changing($id,array('time'=>date("Y-m-d 00:00:00")));
        $this->load->view('employees/thongbaohopdong', $data);
        
    }

      protected function getSale($sale_id)
    {
        $this->load->helper('sale');
        $fulfillment = $this->input->get('fulfillment');
        $type        = $this->input->get('type');

        //Before changing the sale session data, we need to save our current state in case they were in the middle of a sale
        $this->sale_lib->save_current_sale_state();

        $data['is_sale'] = FALSE;
        $sale_info = $this->Sale->get_info($sale_id)->row_array();

        $this->sale_lib->clear_all();
        $this->sale_lib->copy_entire_sale($sale_id, true);
        $data['cart']=$this->sale_lib->get_cart();

        $customer_id=$this->sale_lib->get_customer();

        // [4biz] Get customer balance before make orther
        if($customer_id != -1)
        {
            $cust_info=$this->Customer->get_info($customer_id);

            if ($cust_info->balance !=0)
            {
                $data['customer_balance_for_sale_before'] = $cust_info->balance;
            }
        }
        $data['payments']=$this->sale_lib->get_payments();
        $data['is_sale_cash_payment'] = $this->sale_lib->is_sale_cash_payment();
        $data['show_payment_times'] = TRUE;
        $data['signature_file_id'] = $sale_info['signature_image_id'];

        $tier_id = $sale_info['tier_id'];
        $tier_info = $this->Tier->get_info($tier_id);
        $data['tier'] = $tier_info->name;
        $data['register_name'] = $this->Register->get_register_name($sale_info['register_id']);
        $data['override_location_id'] = $sale_info['location_id'];
        $data['deleted'] = $sale_info['deleted'];

        $data['subtotal']=$this->sale_lib->get_subtotal($sale_id);
        $data['taxes']=$this->sale_lib->get_taxes($sale_id);
        $data['total']=$this->sale_lib->get_total($sale_id);
        $data['receipt_title']= $this->config->item('override_receipt_title') ? $this->config->item('override_receipt_title') : lang('sales_receipt');
        $data['comment'] = $this->Sale->get_comment($sale_id);
        $data['show_comment_on_receipt'] = $this->Sale->get_comment_on_receipt($sale_id);
        $data['transaction_time']= date(get_date_format().' '.get_time_format(), strtotime($sale_info['sale_time']));
        $customer_id=$this->sale_lib->get_customer();

        $emp_info=$this->Employee->get_info($sale_info['employee_id']);
        $sold_by_employee_id=$sale_info['sold_by_employee_id'];
        $sale_emp_info=$this->Employee->get_info($sold_by_employee_id);
        $data['sale_emp_info'] = $sale_emp_info;
        $data['payment_type']=$sale_info['payment_type'];
        $data['amount_change']=$this->sale_lib->get_amount_due($sale_id) * -1;
        $data['employee']=$emp_info->first_name.' '.$emp_info->last_name.($sold_by_employee_id && $sold_by_employee_id != $sale_info['employee_id'] ? '/'. $sale_emp_info->first_name.' '.$sale_emp_info->last_name: '');
        $data['ref_no'] = $sale_info['cc_ref_no'];
        $data['auth_code'] = $sale_info['auth_code'];
        $data['discount_exists'] = $this->_does_discount_exists($data['cart']);
        if($customer_id!=-1)
        {
            $cust_info=$this->Customer->get_info($customer_id);
            // echo "<pre>"; print_r($cust_info); die();
            $data['customer']=$cust_info->first_name.' '.$cust_info->last_name.($cust_info->company_name==''  ? '' :' - '.$cust_info->company_name).($cust_info->account_number==''  ? '' :' - '.$cust_info->account_number);
            $data['customer_address_1']       = $cust_info->address_1;
            $data['customer_address_2']       = $cust_info->address_2;
            $data['customer_company_name']    = $cust_info->first_name.' '.$cust_info->last_name;
            $data['customer_person_id']       = $cust_info->person_id;
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
                $data['customer_balance_for_sale'] = $cust_info->balance;
            }
        }       
        $data['sale_id']=$this->config->item('sale_prefix').' '.$sale_id;
        $data['sale_id_raw']=$sale_id;
        $data['store_account_payment'] = FALSE;

        foreach($data['cart'] as $item)
        {
            if ($item['name'] == lang('sales_store_account_payment'))
            {
                $data['store_account_payment'] = TRUE;
                break;
            }
        }

        if ($sale_info['suspended'] > 0)
        {
            if ($sale_info['suspended'] == 1)
            {
                $data['sale_type'] = lang('common_layaway');
            }
            elseif ($sale_info['suspended'] == 2)
            {
                $data['sale_type'] = lang('common_estimate');               
            }
        }
        $this->sale_lib->clear_all();

        //Restore previous state saved above
        $this->sale_lib->restore_current_sale_state();

        return $data;
    }
    
    function _does_discount_exists($cart)
    {
        foreach($cart as $line=>$item)
        {
            if( (isset($item['discount']) && $item['discount']>0 ) || (isset($item['discount_percent']) && $item['discount_percent']>0 ) )
            {
                return TRUE;
            }
        }
        
        return FALSE;
    }

}