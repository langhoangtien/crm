<?php
require_once(APPPATH . "controllers/Secure_area.php");

class BizTasks extends Secure_area
{

    protected $_data;

    protected $_paginator = array(
        'per_page' => 10,
        'uri_segment' => 3
    );

    function __construct()
    {
        parent::__construct('tasks');
        $get = $this->input->get();
        if (empty($get))
            $get = array();

        $post = $this->input->post();
        if (empty($post))
            $post = array();

        $this->_data['arrParam'] = array_merge($get, $post);

        $this->_data['arrParam']['paginator'] = $this->_paginator;

        // file error messs
        $this->_data['file_errors'] = array(
            '<p>The filetype you are attempting to upload is not allowed.</p>' => 'File tải lên phải có định dạng jpg|png|pdf|docx|doc|xls|xlsx|zip|zar',
            '<p>The file you are attempting to upload is larger than the permitted size.</p>' => 'File tải lên không được quá 100 Mb',
            '<p>The uploaded file exceeds the maximum allowed size in your PHP configuration file.</p>' => 'Tập tin được tải lên vượt quá kích thước tối đa cho phép trong cấu hình PHP'
        );

        // error message redefined Vietnamese language
        $this->load->library("form_validation");
        $this->form_validation->set_message('required', '%s không được rỗng.');
        $this->form_validation->set_message('isset', 'Trường %s phải có giá trị.');
        $this->form_validation->set_message('valid_email', '%s không phải là địa chỉ email.');
        $this->form_validation->set_message('valid_url', '%s không phải là URL.');
        $this->form_validation->set_message('valid_ip', '%s không phải là địa chỉ IP.');
        $this->form_validation->set_message('min_length', '%s phải có ít nhất là %s kí tự.');
        $this->form_validation->set_message('max_length', '%s phải có tối đa là %s kí tự.');
        $this->form_validation->set_message('greater_than', '%s không được nhỏ hơn hoặc bằng %s');
        $this->form_validation->set_message('less_than', '%s không được lớn hơn hoặc bằng %s');
        $this->form_validation->set_message('exact_length', '%s phải có chính xác là %s kí tự.');
        $this->form_validation->set_message('alpha', '%s chỉ được chứa kí tự chữ cái.');
        $this->form_validation->set_message('alpha_numeric', '%s chỉ chứa các kí tự chữ cái và số nguyên.');
        $this->form_validation->set_message('alpha_dash', '%s chỉ chứa các kí tự chữ cái, số nguyên, dấu gạch dưới và dấu gạch ngang.');
        $this->form_validation->set_message('numeric', '%s chỉ chứa số.');
        $this->form_validation->set_message('is_numeric', '%s chỉ chứa kí tự số.');
        $this->form_validation->set_message('integer', '%s phải có kiểu số nguyên.');
        $this->form_validation->set_message('regex_match', '%s không khớp với định dạng.');
        $this->form_validation->set_message('is_unique', '%s đã tồn tại.');

        // load helper
        $this->load->helper('time');
        $this->load->helper('filterext');
        $this->load->helper('recursive');
        $this->load->helper('sort_items');

        $this->load->model('TaskNoRevenue');
    }

    public function index()
    {
        $this->load->library('MY_System_Info');
        $info = new MY_System_Info();
        $user_info = $info->getInfo();
        if (!in_array('tasks_view', $user_info['task_permission']))
            redirect('/no_access/tasks');
        //$this->load->view('tasks/index_view', $this->_data);
        $this->grid();
    }

    public function task_chart()
    {
        $sale_id = $this->uri->segment(0);
        $this->load->library('MY_System_Info');
        $info = new MY_System_Info();
        $user_info = $info->getInfo();
        if (!in_array('tasks_view', $user_info['task_permission']))
            redirect('/no_access/tasks');

        $this->load->view('tasks/index_view', $this->_data);
    }

    public function danhsach()
    {

        $this->_paginator['per_page'] = 10;
        $this->_data['arrParam']['paginator'] = $this->_paginator;

        $this->load->model('Task');
        $config['total_rows'] = $this->Task->countItem($this->_data['arrParam']);

        $config['per_page'] = $this->_paginator['per_page'];
        $config['uri_segment'] = $this->_paginator['uri_segment'];
        $config['use_page_numbers'] = TRUE;

        $this->load->library("pagination");
        $this->pagination->initialize($config);
        $this->pagination->createConfig('front-end');

        $pagination = $this->pagination->create_ajax();

        $this->_data['arrParam']['start'] = $this->uri->segment(3);
        // var_dump($this->_data['arrParam']);die();
        $ketqua = $this->Task->listItem($this->_data['arrParam']);
        // var_dump($ketqua);die();
        $result = array(
            'ketqua' => $ketqua['ketqua'],
            'deny' => $ketqua['deny'],
            'drag_task' => $ketqua['drag_task'],
            'links' => array()
        );
        if (!empty($ketqua['ketqua'])) {
            foreach ($result['ketqua'] as $index => $val) {
                $task_ids[] = $val['id'];
                // Fix Gantt Chart Display The End Date
                $result['ketqua'][$index]['end_date'] = $result['ketqua'][$index]['end_date'] . ' 23:59:59';
            }

            $this->load->model('TasksLinks');
            $arrParams['task_ids'] = array_keys($ketqua['ketqua']);
            $result['links'] = $this->TasksLinks->listItem(array(
                'task_ids' => $task_ids
            ), array(
                'task' => 'by-source'
            ));
        }

        $result['count'] = $config['total_rows'];
        $result['pagination'] = $pagination;

        echo json_encode($result);


    }

    public function customerList()
    {
        $post = $this->input->post();
        if (!empty($post)) {
            $this->load->model('TaskCustomers');
            $result = $this->TaskCustomers->listItem($this->_data['arrParam']);
            
            echo json_encode($result);
        }
    }

    public function trangthaiList()
    {

        $post = $this->input->post();
        if (!empty($post)) {
            // $keywords = trim($post['keywords']);
            // $re_keywords = rewriteUrl($keywords, 'low');
            $task_trangthai = lang('task_trangthai');
            $task_trangthai[5] = 'Chậm tiến độ';
            $task_trangthai[6] = 'Đúng tiến độ';

            $result = array();
            foreach ($task_trangthai as $id => $name) {
                // $re_name = rewriteUrl($name, 'low');
                // if (mb_strpos($re_name, $re_keywords) !== false) {
                $result[] = array(
                    'id' => $id,
                    'name' => $name
                );
                // }
            }

            echo json_encode($result);
        }
    }

    public function userList()
    {
        $post = $this->input->post();
        if (!empty($post)) {
            $this->load->model('TaskUser');
            $result = $this->TaskUser->listItem($this->_data['arrParam']);
            echo json_encode($result);
        }
    }

    public function addcongviec()
    {

        $this->check_action_permission('add_task');
        $post = $this->input->post();

        $this->load->library('MY_System_Info');
        $info = new MY_System_Info();
        $this->_data['user_info'] = $user_info = $info->getInfo();

        $this->load->model('Task');
        $this->load->model('TasksRelation');
        $this->load->model('TaskProgress');
        $this->load->model('TaskTemplate');
        $this->load->model('Sale');
        $this->load->model('Customer');

        $arrParam = $this->_data['arrParam'];
        
        $arrParam['date_start'] = date("Y-m-d",strtotime($arrParam['date_start']. "+ 0 weekdays"));
        // var_dump($arrParam);die();       
        $sale_id = $arrParam['sale_id'];

        if ($arrParam['parent'] > 0) {
            $parent_item = $this->Task->getItem(array(
                'id' => $arrParam['parent']
            ), array(
                'task' => 'public-info'
            ));

            $parents = $this->Task->getInfo(array(
                'lft' => $parent_item['lft'],
                'rgt' => $parent_item['rgt'],
                'project_id' => $parent_item['project_id']
            ), array(
                'task' => 'create-task'
            ));

            $task_ids = $parents['task_ids'];
            $project_relation = $this->TasksRelation->getItems(array(
                'task_ids' => $task_ids
            ), array(
                'task' => 'by-multi-task'
            ));
        }
        // var_dump($project_relatio);die();
        if (!empty($post)) {
            $arrParam['user_info'] = $this->_data['user_info'];

            if (($arrParam['task_template'] == 0) && ($arrParam['parent'] == 0)) {
                // echo "<pre>";
                // var_dump($arrParam);
                echo json_encode(array('flag' => 'false', 'errors' => array('template' => "Bạn phải chọn lộ trình mẫu")));
                return;
            }
            $this->load->library("form_validation");
            $this->form_validation->set_rules('name', 'Tiêu đề', 'required|max_length[255]');
//             $this->form_validation->set_rules('progress', 'Tiến độ', 'required|greater_than[-1]|less_than[101]');
            $this->form_validation->set_rules('color', 'Màu', 'required');
            $this->form_validation->set_rules('date_start', 'Bắt đầu', 'required');
            $this->form_validation->set_rules('date_end', 'Kết thúc', 'required');
            if ($arrParam['parent'] > 0)
                $this->form_validation->set_rules('percent', 'Tỷ lệ', 'required|greater_than[-1]|less_than[101]');

            if ($arrParam['template_task'] > 0)
                $this->form_validation->set_rules('task_template', 'Template', 'is_unique[task_template.id]');

            $flagError = false;
            $task_permission = array();

            $task_permission = $user_info['task_permission'];

            if ($this->form_validation->run($this) == FALSE) {
                $errors = $this->form_validation->error_array();
                if (isset($errors['date_start']) && !isset($errors['date_end']))
                    $errors['date_end'] = '.';

                if (!isset($errors['date_start']) && isset($errors['date_end']))
                    $errors['date_start'] = '.';

                $flagError = true;
            } else {
                // time valid
                $datediff = strtotime($arrParam['date_end']) - strtotime($arrParam['date_start']);

                if ($datediff < 0) {
                    $flagError = true;
                    $errors['date_start'] = 'Kết thúc phải sau bắt đầu.';
                    $errors['date_end'] = '.';
                } else {
                    if ($arrParam['parent'] > 0) {
                        $error_date = $this->validate_min_max_date($arrParam['date_start'], $arrParam['date_end'], $parent_item['date_start'], $parent_item['date_end']);
                        if (!empty($error_date)) {
                            $flagError = true;
                            $errors['date_time'] = $error_date;
                        }
                    }
                }

                // max percent valid
                if ($arrParam['parent'] > 0) {
                    $max_percent = $this->Task->getMaxPercent($arrParam['parent'], $arrParam['project_id']);
                    if ($arrParam['percent'] > $max_percent) {
                        $flagError = true;
                        $errors['percent'] = 'Tỷ lệ không được quá ' . $max_percent . '%';
                    }
                }
            }

            if ($flagError == false) {
                // check permission

                $is_pheduyet = $is_implement = array();
                if (!empty($project_relation)) {
                    foreach ($project_relation as $val) {
                        if ($val['is_pheduyet'] == 1)
                            $is_pheduyet[] = $val['user_id'];

                        if ($val['is_implement'] == 1)
                            $is_implement[] = $val['user_id'];
                    }
                }

                $arrParam['pheduyet'] = -1;
                if (in_array('update_project', $task_permission))
                    $arrParam['pheduyet'] = 2;
                elseif (in_array($user_info['id'], $is_implement) && in_array('update_brand_task', $task_permission))
                    $arrParam['pheduyet'] = 2;
                elseif (count($is_pheduyet) == 0)
                    $arrParam['pheduyet'] = 2;

                /**
                 * pheduyet = -1 // cho phe duyet
                 * pheduyet = 0 // khong duoc phe duyet
                 * pheduyet = 1 //
                 * pheduyet = 2 //
                 */

                // covert status and progress
                $arrParam = $this->convert_progress_task($arrParam);

                // $projectDuration = $this->TaskTemplate->getDurationOfProject($arrParam['task_template']);

                // clone Template
                // $template_id = $this->TaskTemplate->clone_template($arrParam['task_template'], $arrParam['name']);
                // $arrParam['task_template'] = $template_id;
                // var_dump($arrParam);die();
                //   echo "<pre>";
                // var_dump($arrParam);die();

                $last_id = $this->Task->saveItem($arrParam, array(
                    'task' => 'add'
                ));
                $this->Task->task_notice_log($last_id,$arrParam);
                // var_dump($last_id);die();
                // nếu là công việc con
                if ($arrParam['parent'] > 0) {
                    // nếu không phải dự án thì update lại progress item : progress => -1
                    $this->TaskProgress->saveItem(array(
                        'task_ids' => $task_ids
                    ), array(
                        'task' => 'progress-1'
                    ));

                    // update lại tiến đô + lịch sử
                    $arrParam['key'] = 'plus';
                    $this->TaskProgress->solve($arrParam);
                } else {
                    // update first progress
                    $params = array(
                        'task_id' => $last_id,
                        'trangthai' => $arrParam['trangthai'],
                        'prioty' => $arrParam['prioty'],
                        'progress' => $arrParam['progress'],
                        'pheduyet' => $arrParam['pheduyet'],
                        'note' => '',
                        'key' => 'plus',
                        'date_pheduyet' => @date("Y-m-d H:i:s")
                    );

                    $this->TaskProgress->saveItem($params, array(
                        'task' => 'add'
                    ));
                }


                if (!empty($sale_id)) {
                    $this->Sale->update(array('suspended' => 2, 'task_id' => $last_id), $sale_id);
                }
                // add template task
                if ($arrParam['task_template'] > 0) {
                    $arrParam['last_id'] = $last_id;
                    $end = $this->add_template_for_task($arrParam);
                    $de = $arrParam['date_end'];
                    $this->Task->update_time_for_task($last_id);
                    $end = ((strtotime($de) - strtotime($end)) > 0) ? $de : $end;
                    // echo $this->db->last_query();die();
                    # Chia duration cho taskcon
                    // echo $end;die();
                    $this->Task->add_percent_for_child($last_id);
                    $this->db->where('id', $last_id);
                    $this->db->update('phppos_tasks', array('date_end' => $end));
                }

                $respon = array(
                    'flag' => 'true'
                );
            } else {
                if (isset($errors['date_start']))
                    $errors['date_start_formatted'] = $errors['date_start'];

                if (isset($errors['date_end']))
                    $errors['date_end_formatted'] = $errors['date_end'];

                $respon = array(
                    'flag' => 'false',
                    'errors' => $errors
                );
            }

            echo json_encode($respon);
        } else {
            if (!empty($sale_id)) {
                $sale_info = $this->Sale->getInfo($sale_id);
                $customer_info = $this->Customer->get_information($sale_info['customer_id']);
            }

            $task_template = $this->TaskTemplate->itemSelectbox();
            $max_percent = $this->Task->getMaxPercent($arrParam['parent'], $parent_item['project_id']);

            if ($arrParam['parent'] > 0) {
                $parent_item = $this->Task->getItem(array(
                    'id' => $arrParam['parent']
                ), array(
                    'task' => 'public-info'
                ));;
                $parents = $this->Task->getInfo(array(
                    'lft' => $parent_item['lft'],
                    'rgt' => $parent_item['rgt'],
                    'project_id' => $parent_item['project_id']
                ), array(
                    'task' => 'create-task'
                ));

                $task_ids = $parents['task_ids'];
                $project_relation = $this->TasksRelation->getItems(array(
                    'task_ids' => $task_ids
                ), array(
                    'task' => 'by-multi-task'
                ));
            }

            $this->_data['percent'] = $max_percent;
            $this->_data['parent'] = $arrParam['parent'];
            $this->_data['parent_item'] = $parent_item;
            $this->_data['project_relation'] = $project_relation;
            $this->_data['task_template'] = $task_template;
            // view
            $this->load->view('tasks/addform_view', $this->_data);
        }
    }

    public function add_task_by_sale()
    {

        $this->check_action_permission('add_task');
        $post = $this->input->post();
        $this->load->library('MY_System_Info');
        $info = new MY_System_Info();
        $this->_data['user_info'] = $user_info = $info->getInfo();
        // var_dump($post);die();
        $this->load->model('Task');
        $this->load->model('TasksRelation');
        $this->load->model('TaskProgress');
        $this->load->model('TaskTemplate');
        $this->load->model('Sale');
        $this->load->model('Customer');

        $arrParam = $this->_data['arrParam'];
        $sale_id = $arrParam['sale_id'];
        // var_dump($arrParam);die();

        $sale_info = $this->Sale->getInfo($sale_id);

        $customer_info = $this->Customer->get_information($sale_info['customer_id']);
        // var_dump($customer_info);die();
        $supporters = $this->Sale->get_employee_by_sale($sale_id);
        $lst_items = $this->Sale->get_sale_items($sale_id)->result();
        $work_name = '';
        if (!empty($lst_items)) {
            foreach ($lst_items as $item) {
                if ($work_name != '') {
                    $work_name .= ',';
                }
                $work_name .= $item->item_name;
            }
        }

        $task_template = $this->TaskTemplate->itemSelectbox();
        $max_percent = $this->Task->getMaxPercent($arrParam['parent'], $parent_item['project_id']);

        $task = $this->Task->get_project_by_customer($sale_info['customer_id']);
        $this->_data['percent'] = $max_percent;
        $this->_data['parent'] = $arrParam['parent'];
        $this->_data['parent_item'] = $parent_item;
        $this->_data['project_relation'] = $project_relation;
        $this->_data['task_template'] = $task_template;
        // var_dump($task_template);die();
        $this->_data['sale_info'] = $sale_info;
        $this->_data['customer_info'] = $customer_info;
        $this->_data['customer_id'] = $customer_info['id'];
        $this->_data['supporters'] = $supporters;
        $this->_data['work_name'] = $work_name;

        $this->_data['sale_id'] = $sale_id;
        $this->_data['customer_name'] = $customer_info['first_name'] . ' ' . $customer_info['last_name'];
        $this->_data['project_name'] = !empty($task) ? $task['name'] : '';
        $this->_data['project_id'] = !empty($task) ? $task['project_id'] : '';
        // echo "<pre>";
        // var_dump($this->_data);die();
        if (empty(trim($this->_data['customer_name']))) {
            echo 0;
        } else if ($sale_info['suspended'] != 1) {
            echo 1;
        } else {
            $this->load->view('tasks/add_task_by_sale', $this->_data);
        }
    }

    protected function convert_progress_task($arrParam, $options = null)
    {
        if ($arrParam['pheduyet'] == -1 && $options != 'uncheck-pheduyet') {
            $arrParam['trangthai'] = 0;
            $arrParam['progress'] = 0;
        } else {

            if ($arrParam['trangthai'] == 4) {
                $arrParam['progress'] = 0;
            } elseif ($arrParam['trangthai'] != 3) {
                if ($arrParam['trangthai'] == 2 || $arrParam['progress'] == 100) {
                    $arrParam['trangthai'] = 2;
                    $arrParam['progress'] = 100;
                    $arrParam['pheduyet'] = -1;
                } elseif ($arrParam['progress'] > 0 && $arrParam['trangthai'] == 0) {
                    $arrParam['trangthai'] = 1;
                }
            }

            if ($arrParam['task_template'] > 0) {
                $arrParam['trangthai'] = 0;
                $arrParam['progress'] = 0;
            }
        }

        return $arrParam;
    }

# Thêm dự án theo template
    protected function add_template_for_task($arrParam)
    {
        // echo "<pre>";
        // var_dump($arrParam);die();
        $info = new MY_System_Info();
        $user_info = $info->getInfo();

        $this->load->model('TaskTemplate');
        if ($arrParam['parent'] == 0)
            $arrParam['project_id'] = $arrParam['last_id'];

        # Lấy những dự án có template_id được chọn
        $template_task_items = $this->TaskTemplate->listItem(array(
            'template_id' => $arrParam['task_template']
        ), array(
            'task' => 'by-template'
        ));

        $parentArray = array();

        $st = $arrParam['date_start'];
        // echo "<pre>";
        // var_dump($params['date_start']);
        // die();
        // echo "<pre>";var_dump($template_task_items);die();
        foreach ($template_task_items as $key => $val) {
            $params = array();
            $params['name'] = $val['name'];
            if ($val['level'] == 1) {
                # Task id
                $params['parent'] = $arrParam['last_id'];
            } else
                $params['parent'] = $parentArray[$val['parent']];

            $params['color'] = $arrParam['color'];
            $params['detail'] = '';
            $params['percent'] = 0;
            $params['progress'] = 0;
            $params['project_id'] = $arrParam['project_id'];
            // $params['date_start'] = $start;
            // TODO
            // $x= $value['duration'];
            // $start = date("Y-m-d",strtotime("+$x day",$start));

            // $params['date_end'] = $start;
            // var_dump($params);die();  
            if ($val['rgt'] == $val['lft'] + 1) {
                $params['date_start'] = $st;
                $params['date_end'] = date('Y-m-d', strtotime('+' . $val['duration'] . ' weekdays', strtotime($st)));
                $st = $params['date_end'];
            } else {
                $params['date_start'] = $arrParam['date_start'];
                $params['date_end'] = date('Y-m-d', strtotime('+' . $val['duration'] . ' weekdays', strtotime($params['date_start'])));

            }
            $params['percent'] = (int)$val['tile'];
            $params['duration'] = $arrParam['duration'];
            $params['pheduyet'] = $arrParam['pheduyet'];
            $params['trangthai'] = $arrParam['trangthai'];
            $params['prioty'] = $arrParam['prioty'];

            $taskId = $parentArray[$val['id']] = $this->Task->saveItem($params, array(
                'task' => 'add'
            ));

            if ($taskId > 0) {
                $xemArr = array();
                if (!empty($val['xemlist'])) {
                    $xemArr = explode(',', $val['xemlist']);
                }

                $implementArr = array();
                if (!empty($val['implementlist'])) {
                    $implementArr = explode(',', $val['implementlist']);
                }

                $progress_taskArr = array();
                if (!empty($val['approvelist'])) {
                    $progress_taskArr = explode(',', $val['approvelist']);
                }
                $records = $this->Task->do_relation_information($taskId, $xemArr, [], $implementArr, [], [], $progress_taskArr);
                if (!empty($records)) {
                    $this->Task->saveTaskUserRelations($records);
                }
            }

            $items[] = array(
                'task_id' => $parentArray[$val['id']],
                'trangthai' => 0,
                'prioty' => $params['prioty'],
                'progress' => 0,
                'pheduyet' => $params['pheduyet'],
                'note' => '',
                'reply' => '',
                'created' => @date("Y-m-d H:i:s"),
                'created_by' => $user_info['id'],
                'user_pheduyet' => 0,
                'date_pheduyet' => @date("Y-m-d H:i:s"),
                'user_pheduyet_name' => '',
                'key' => 'plus'
            );
        }
        if (!empty($items)) {
            $this->TaskProgress->saveItem(array(
                'items' => $items
            ), array(
                'task' => 'multi-add'
            ));
        }
        return $st;
    }

    protected function update_time_for_tasks_child($task_items, $date_start, $date_end)
    {
        $this->load->model('Task');
        foreach ($task_items as $val) {
            $params = array();
            $fields = array();
            $params['id'] = $val['id'];
            $datediff_start = strtotime($val['date_start']) - strtotime($date_start);
            if ($datediff_start > 0)
                $fields['date_start'] = $val['date_start'];
            else
                $fields['date_start'] = $date_start;

            $datediff_end = strtotime($val['date_end']) - strtotime($date_end);
            if ($datediff_end > 0)
                $fields['date_end'] = $date_end;
            else
                $fields['date_end'] = $val['date_end'];

            $params['fields'] = $fields;
            $this->Task->saveItem($params, array(
                'task' => 'custom'
            ));
        }
    }


    protected function check_pheduyet($task_id)
    {
        $progress = $this->Task->getNodeInfo($task_id)['progress'];
        if ($progress != 100)
            return false;
        $id = $this->Employee->get_logged_in_employee_info()->id;

        if ($this->Employee->has_module_action_permission('tasks', 'approve_all', $this->Employee->get_logged_in_employee_info()->person_id))
            return true;
        if ($this->TasksRelation->get_list_user_by_task($id, $task_id, 'is_progress'))
            return true;
        else
            return false;

    }

    public function editcongviec()
    {
        $arrParam = $this->_data['arrParam'];
        // var_dump($arrParam); die();
        $post = $this->input->post();
        $this->load->library('MY_System_Info');
        $info = new MY_System_Info();
        $this->_data['user_info'] = $user_info = $info->getInfo();

        $task_permission = $user_info['task_permission'];
        // var_dump($task_permission);die();
        $this->load->model('Task');
        $this->load->model('TasksRelation');
        $this->load->model('TaskProgress');

        $arrParam = $this->_data['arrParam'];
        $arrParam['date_start'] = date("Y-m-d", strtotime($arrParam['date_start']));
        $arrParam['date_end'] = date("Y-m-d", strtotime($arrParam['date_end']));
        $item = $this->Task->getItem(array(
            'id' => $arrParam['id']
        ), array(
            'task' => 'public-info',
            'brand' => 'full'
        ));
        $this->_data['log'] = $this->Task->list_log(array('project_id' => $item['project_id']));
        // var_dump($this->_data['log']);die();
        $this->_data['cus'] = $this->Task->get_customer_by_task_id($item['project_id'])['last_name'];
        // echo $this->db->last_query();die();   
        // echo "<pre"; var_dump($item);die();
        $this->_data['check'] = $this->check_pheduyet($item['id']);
        // echo $this->db->last_query();die();
        // var_dump($this->_data['check']);die();

        $this->_data['user_pheduyet'] = "";
        if (!empty($item['user_pheduyet'])) {
            $this->_data['user_pheduyet'] = $this->Employee->get_employee_by_id($item['user_pheduyet'])['username'];
        }
        $this->db->select('t.date_start, t.date_end,t.name,t.percent,t.id');
        $this->db->where('parent', $arrParam['id']);

        $time = 0;

        $childs = $this->db->get('phppos_tasks as t')->result_array();
        foreach ($childs as &$child) {
            $d1 = date_create($child['date_start']);
            $d2 = date_create($child['date_end']);
            $child['t'] = date_diff($d1, $d2)->format('%a');
            $time += $child['t'];
        }
        $time == 0 ? 1 : $time;
        $this->_data['child'] = $childs;
        $this->_data['time'] = $time;

        // echo $this->db->last_query();
        // var_dump($this_data);die();
        if (!empty($post)) {
            $this->load->library("form_validation");
            $this->form_validation->set_rules('name', 'Tiêu đề', 'required|max_length[255]');
            $this->form_validation->set_rules('color', 'Màu', 'required');
            $this->form_validation->set_rules('date_start', 'Bắt đầu', 'required');
            $this->form_validation->set_rules('date_end', 'Kết thúc', 'required');
            if ($item['parent'] > 0)
                $this->form_validation->set_rules('percent', 'Tỷ lệ', 'required|greater_than[-1]|less_than[101]');

            if ($this->form_validation->run($this) == FALSE) {
                $errors = $this->form_validation->error_array();
                $flagError = true;
            } else {
                // time validate
                $datediff = strtotime($arrParam['date_end']) - strtotime($arrParam['date_start']);

                if ($datediff < 0) {
                    $flagError = true;
                    $errors['date_start'] = 'Ngày kết thúc phải sau ngày bắt đầu.';
                    $errors['date_end'] = '.';
                } else {
                    if ($item['parent'] > 0) {
                        $parent_item = $this->Task->getItem(array(
                            'id' => $item['parent']
                        ), array(
                            'task' => 'information'
                        ));
                        $error_date = $this->validate_min_max_date($arrParam['date_start'], $arrParam['date_end'], $parent_item['date_start'], $parent_item['date_end']);
                        if (!empty($error_date)) {
                            $flagError = true;
                            $errors['date_time'] = $error_date;
                        }
                    }
                }

                if ($flagError == false) {
                    $max_percent = $this->Task->getMaxPercent($arrParam['parent'], $arrParam['project_id'], $arrParam['id']);
                    // valid percent
                    // var_dump($max_percent);die();

                    $t = round($arrParam['percent'], 2);
                    // var_dump($t);
                    // die();

                    // // var_dump($max_percent);die();
                    // round($max_percent);
                    if ($t <= $max_percent) {

                    } else {
                        $flagError = true;
                        // var_dump($t);
                        // var_dump($max_percent);die();
                        $errors['percent'] = 'Tỷ lệ không được quá ' . $max_percent . '%';
                    }
                }
            }

            if ($flagError == false) {
                // covert trangthai and progress
                if ($arrParam['trangthai'] == 2 || $arrParam['progress'] == 100) {
                    $arrParam['trangthai'] = 2;
                    $arrParam['progress'] = 100;
                } elseif ($arrParam['progress'] > 0 && $arrParam['trangthai'] == 0) {
                    $arrParam['trangthai'] = 1;
                }

                $arrParam['created_by'] = $item['created_by'];
       // echo "<pre>";var_dump($arrParam);die();
                $this->Task->saveItem($arrParam, array(
                    'task' => 'edit'
                ));
         $this->Task->task_notice_log($arrParam['id'],$arrParam);
                // update time for tasks child
                $params = $item;
                $params['date_start'] = $arrParam['date_start'];
                $params['date_end'] = $arrParam['date_end'];

                $task_items = $this->Task->getItems($params, array(
                    'task' => 'update-task'
                ));

                if (!empty($task_items)) {
                    // echo "<pre>";
                    // var_dump($task_items);die();
                    $this->update_time_for_tasks_child($task_items, $arrParam['date_start'], $arrParam['date_end']);
                }

                // CẬP NHẬT TIẾN ĐỘ CHO CÔNG VIỆC
                if ($arrParam['percent'] != $item['percent']) {
                    $arrParam['key'] = 'pencil-square-o';
                    $arrParam['level'] = $item['level'];

                    $this->TaskProgress->solve($arrParam, array(
                        'task' => 'edit'
                    ));
                }

                $respon = array(
                    'flag' => 'true'
                );
            } else {
                $respon = array(
                    'flag' => 'false',
                    'errors' => $errors
                );
            }

            echo json_encode($respon);
        } else {
            $is_xem = $is_implement = $is_create_task = $is_join = $is_pheduyet = $is_progress = array();
            $is_create_task_parent = $is_pheduyet_parent = $is_progress_parent = array();
            $list_im = array();
            $list_jo = array();

            if (!empty($item['is_xem'])) {
                foreach ($item['is_xem'] as $val)
                    $is_xem[] = $val['id'];

                $is_xem = array_unique($is_xem);
            }


            if (!empty($item['is_join'])) {
                foreach ($item['is_join'] as $val) {
                    $is_join[] = $val['id'];
                    // Check joined user
                    if ($val['id'] == $this->Employee->get_logged_in_employee_info()->person_id) {
                        $data['is_joiner'] = true;
                    }
                }
                $is_join = array_unique($is_join);
                $list_jo = $this->Task->get_info_users($is_join);
            }

            if (!empty($item['is_implement'])) {
                foreach ($item['is_implement'] as $val)
                    $is_implement[] = $val['id'];

                $is_implement = array_unique($is_implement);
                $list_im = $this->Task->get_info_users($is_implement);
            }

            if (!empty($item['is_create_task'])) {

                foreach ($item['is_create_task'] as $key => $val) {
                    $is_create_task[] = $val['id'];
                    $keyArr = explode('-', $key);
                    if ($keyArr[0] != $arrParam['id'])
                        $is_create_task_parent[] = $val['id'];
                }

                $is_create_task_parent = array_unique($is_create_task_parent);
                $is_create_task = array_unique($is_create_task);
            }

            if (!empty($item['is_pheduyet'])) {
                foreach ($item['is_pheduyet'] as $key => $val) {
                    $is_pheduyet[] = $val['id'];

                    $keyArr = explode('-', $key);
                    if ($keyArr[0] != $arrParam['id'])
                        $is_pheduyet_parent[] = $val['id'];
                }

                $is_pheduyet_parent = array_unique($is_pheduyet_parent);
                $is_pheduyet = array_unique($is_pheduyet);
            }

            $item['is_pheduyet_parent'] = $is_pheduyet_parent;

            if (!empty($item['is_progress'])) {
                foreach ($item['is_progress'] as $key => $val) {
                    $is_progress[] = $val['id'];

                    $keyArr = explode('-', $key);
                    if ($keyArr[0] != $arrParam['id'])
                        $is_progress_parent[] = $val['id'];
                }

                $is_progress_parent = array_unique($is_progress_parent);
                $is_progress = array_unique($is_progress);
            }

            if ($item['parent'] > 0) {
                $cid = array(
                    $item['parent'],
                    $item['project_id']
                );
                $items = $this->Task->getItems(array(
                    'cid' => $cid
                ), array(
                    'task' => 'public-info'
                ));

                $this->_data['project_item'] = $items[$item['project_id']];
                $this->_data['parent_item'] = $items[$item['parent']];

                $items = $this->Task->getInfo(array(
                    'lft' => $item['lft'],
                    'rgt' => $item['rgt'],
                    'project_id' => $item['project_id']
                ), array(
                    'task' => 'create-task'
                ));
                $task_ids = $items['task_ids'];

                $project_relation = $this->TasksRelation->getItems(array(
                    'task_ids' => $task_ids
                ), array(
                    'task' => 'by-multi-task'
                ));

                $this->_data['project_relation'] = $project_relation;
            }

            # Người thay thế
            $this->_data['tranfers'] = $this->Task->get_list_task_tranfer($arrParam['id']);
            #Bên thứ 3
            $this->_data['suppliers'] = $this->Task->get_list_item_by_task_id($arrParam['id']);


            $this->_data['item'] = $item;
            // if($item['parent']==0)
            $this->_data['progress_list'] =$this->Task->get_project_info($item['project_id']);
            // echo "<pre>";echo $this->db->last_query();die();
            // var_dump($this->_data['progress_list']);
            if ($item['parent'] == 0) { // project
                if (in_array('update_project', $task_permission))
                    $view = 'tasks/editform_view';
                elseif (in_array($user_info['id'], $is_implement) && in_array('update_brand_task', $task_permission))
                    $view = 'tasks/editform_view';
                elseif (in_array($user_info['id'], $is_implement))
                    $view = 'tasks/quickupdate_view';
                elseif (in_array($user_info['id'], $is_join))
                    $view = 'tasks/join_user_view';
                elseif (in_array($user_info['id'], $is_xem)) {
                    $this->_data['no_comment'] = $this->_data['no_update'] = true;
                    $view = 'tasks/detail_view';
                }
            } else { // tasks
                if (in_array('update_all_task', $task_permission)) {
                    $view = 'tasks/editform_view';
                } elseif (in_array($user_info['id'], $is_implement) && in_array('update_brand_task', $task_permission)) {
                    $view = 'tasks/editform_view';
                } elseif (in_array($user_info['id'], $is_create_task_parent)) {
                    $view = 'tasks/editform_view';
                } elseif (in_array($user_info['id'], $is_implement)) {
                    $view = 'tasks/quickupdate_view';
                } elseif (in_array($user_info['id'], $is_join)) {
                    $view = 'tasks/quickupdate_view';
                } elseif (in_array($user_info['id'], $is_xem) || in_array($user_info['id'], $is_pheduyet_parent)) {
                    $this->_data['no_comment'] = true;
                    $this->_data['is_xem'] = true;
                    $view = 'tasks/detail_view';
                }
            }
            $this->_data['list_jo'] = $list_jo;
            $this->_data['list_im'] = $list_im;

            // project/task brands
            $this->_data['slbTasks'] = $this->Task->itemSelectBox(array(
                'project_id' => $item['project_id'],
                'lft' => $item['lft'],
                'rgt' => $item['rgt']
            ));
            if (!empty($view))
                $this->load->view($view, $this->_data);
        }
    }

    public function progresslist()
    {
        $this->load->model('TaskProgress');
        $post = $this->input->post();

        if (!empty($post)) {
            $config['base_url'] = base_url() . 'tasks/progresslist';
            $config['total_rows'] = $this->TaskProgress->countItem($this->_data['arrParam'], array(
                'task' => 'public-list'
            ));

            $config['per_page'] = $this->_paginator['per_page'];
            $config['uri_segment'] = $this->_paginator['uri_segment'];
            $config['use_page_numbers'] = TRUE;

            $this->load->library("pagination");
            $this->pagination->initialize($config);
            $this->pagination->createConfig('front-end');

            $pagination = $this->pagination->create_ajax();

            $this->_data['arrParam']['start'] = $this->uri->segment(3);
            $items = $this->TaskProgress->listItem($this->_data['arrParam'], array(
                'task' => 'public-list'
            ));

            $result = array(
                'count' => $config['total_rows'],
                'items' => $items,
                'pagination' => $pagination
            );

            echo json_encode($result);
        }
    }

    public function countTiendo()
    {
        $this->load->model('TaskProgress');
        $post = $this->input->post();
        if (!empty($post)) {
            $result['tiendo_total'] = $this->TaskProgress->countItem($this->_data['arrParam'], array(
                'task' => 'public-list'
            ));
            $result['request_total'] = $this->TaskProgress->countItem($this->_data['arrParam'], array(
                'task' => 'request-list'
            ));
            $result['pheduyet_total'] = $this->TaskProgress->countItem($this->_data['arrParam'], array(
                'task' => 'pheduyet-list'
            ));

            echo json_encode($result);
        }
    }

    public function filelist()
    {
        $this->load->model('TaskFiles');
        $post = $this->input->post();

        if (!empty($post)) {
            $config['base_url'] = base_url() . 'tasks/filelist';
            $config['total_rows'] = $this->TaskFiles->countItem($this->_data['arrParam'], array(
                'task' => 'public-list'
            ));
            $config['per_page'] = $this->_paginator['per_page'];
            $config['uri_segment'] = $this->_paginator['uri_segment'];
            $config['use_page_numbers'] = TRUE;

            $this->load->library("pagination");
            $this->pagination->initialize($config);
            $this->pagination->createConfig('front-end');

            $pagination = $this->pagination->create_ajax();

            $this->_data['arrParam']['start'] = $this->uri->segment(3);
            $items = $this->TaskFiles->listItem($this->_data['arrParam'], array(
                'task' => 'public-list'
            ));

            $result = array(
                'count' => $config['total_rows'],
                'items' => $items,
                'pagination' => $pagination
            );

            echo json_encode($result);
        }
    }

    public function requestlist()
    {
        $this->load->model('TaskProgress');
        $post = $this->input->post();
        if (!empty($post)) {
            $config['base_url'] = base_url() . 'tasks/progresslist';
            $config['total_rows'] = $this->TaskProgress->countItem($this->_data['arrParam'], array(
                'task' => 'request-list'
            ));
            $config['per_page'] = $this->_paginator['per_page'];
            $config['uri_segment'] = $this->_paginator['uri_segment'];
            $config['use_page_numbers'] = TRUE;

            $this->load->library("pagination");
            $this->pagination->initialize($config);
            $this->pagination->createConfig('front-end');

            $pagination = $this->pagination->create_ajax();

            $this->_data['arrParam']['start'] = $this->uri->segment(3);
            $items = $this->TaskProgress->listItem($this->_data['arrParam'], array(
                'task' => 'request-list'
            ));

            $result = array(
                'count' => $config['total_rows'],
                'items' => $items,
                'pagination' => $pagination
            );

            echo json_encode($result);
        }
    }

    public function pheduyetlist()
    {
        $this->load->model('TaskProgress');
        $post = $this->input->post();
        if (!empty($post)) {
            $config['base_url'] = base_url() . 'tasks/pheduyetlist';
            $config['total_rows'] = $this->TaskProgress->countItem($this->_data['arrParam'], array(
                'task' => 'pheduyet-list'
            ));
            $config['per_page'] = $this->_paginator['per_page'];
            $config['uri_segment'] = $this->_paginator['uri_segment'];
            $config['use_page_numbers'] = TRUE;

            $this->load->library("pagination");
            $this->pagination->initialize($config);
            $this->pagination->createConfig('front-end');

            $pagination = $this->pagination->create_ajax();

            $this->_data['arrParam']['start'] = $this->uri->segment(3);
            $items = $this->TaskProgress->listItem($this->_data['arrParam'], array(
                'task' => 'pheduyet-list'
            ));

            $result = array(
                'count' => $config['total_rows'],
                'items' => $items,
                'pagination' => $pagination
            );

            echo json_encode($result);
        }
    }


    function update_task_pause($project_id){
        $list = $this->Task->get_task_by_project_id($project_id);
        foreach ($list as $key => $value) {
            if($value['progress']>=100){
                $data['trangthai'] =2;
            }
            elseif ($value['progress']==0) {
                $data['trangthai'] =0;
            }
            else{
                $data['trangthai'] =1;
            }

            $this->db->where('id', $value['id']);
            $this->db->update('phppos_tasks', $data);
        }
    }

    public function addtiendo()
    {
        $this->load->model('Task');
        $this->load->model('TaskProgress');
        $post = $this->input->post();
        $arrParam = $this->_data['arrParam'];

        $this->load->library('MY_System_Info');
        $info = new MY_System_Info();
        $arrParam['adminInfo'] = $user_info = $info->getInfo();

        if (!empty($post)) {
            $item = $this->Task->getItem(array(
                'id' => $this->_data['arrParam']['task_id']
            ), array(
                'task' => 'public-info',
                'brand' => 'full'
            ));
            // echo "<pre>";
            // var_dump($item);die();
            $flagError = false;
            $data = array();
            $data['pheduyet'] = 1;
            $data['trangthai'] = 1;
            $data['prioty'] = $post['prioty'];
            $data['pheduyet_note'] = $post['note'];
            $id = $post['task_id'];
            $task_info = $this->Task->getNodeInfo($post['task_id']);
            // echo "<pre>";
            // var_dump($item);die();

            // var_dump($post);

            #Nếu là dự án cha và chuyển trạng thái về tạm dừng
            if($item['parent']==0){

                if($post['trangthai']==3 && $item['trangthai'] !=3){
                    $this->db->where('project_id', $item['id']);
                    $this->db->update('phppos_tasks', array('trangthai'=>3));

                $log = array();
                $log['trangthai'] = $post['trangthai'];
                $log['progress'] = $item['progress'];
                $log['task_id'] = $id;
                $log['prioty'] = $data['prioty'];
                $this->Task->add_log($log);
                    $respon = array('flag' => 'true', 'message' => 'Cập nhật thành công');
                    echo json_encode($respon);
                    return;
                }
                elseif ($post['trangthai']==3 && $item['trangthai'] ==3) {
                    
                $log = array();
                $log['trangthai'] = $post['trangthai'];
                $log['progress'] = $item['progress'];
                $log['task_id'] = $id;
                $log['prioty'] = $data['prioty'];
                $this->Task->add_log($log);
                    $respon = array('flag' => 'true', 'message' => 'Cập nhật thành công');
                    echo json_encode($respon);
                    return;
                }

                elseif ($post['trangthai'] !=3 && $item['trangthai']==3) {

                    $this->db->where('id', $item['id']);
                    $this->db->update('phppos_tasks', array('trangthai'=>$post['trangthai']));
                    $this->update_task_pause($item['id']);
                $log = array();
                $log['trangthai'] = $post['trangthai'];
                $log['progress'] = $item['progress'];
                $log['task_id'] = $id;
                $log['prioty'] = $data['prioty'];
                $this->Task->add_log($log);
                 $respon = array('flag' => 'true', 'message' => 'Cập nhật thành công');
                 echo json_encode($respon);
                    return;
                    
                }
            }
            switch ($post['trangthai']) {
                case 1:
                    if ($item['lft'] == $item['rgt'] - 1) {
                        $data['progress'] = $post['progress'];
                        round($data['progress'], 2);
                        if ($data['progress'] >= 100 || $data['progress'] <= 0)
                            $flagError = true;
                        $respon = array('flag' => 'error', 'message' => 'Tiến độ phải trong khoảng từ 0 đến 100%');
                    }
                    break;

                case 2:
                    $data['progress'] = 100;
                    $data['trangthai'] = 2;
                    $data['date_finish'] = date('Y-m-d H:i:s');
                    break;

                case 0:
                    $data['progress'] = 0;
                    break;

                case 3:
                    $data['trangthai'] = 3;
                    $data['progress'] = $item['progress'];
                    break;

                default:
                    $flagError = true;
                    $respon = array('flag' => 'error', 'message' => 'Các chức năng này của dự án hiện đang được xem xét vui lòng liên hệ với quản trị viên');
            }


            if ($flagError == false) {
                if ($task_info['level'] == 0) {
                    $data['pheduyet'] = 2;
                }

                $this->db->where('id', $id);
                $this->db->update('phppos_tasks', $data);
                $this->update_parent_progress($id);
                #LOG 
                $log = array();
                $log['trangthai'] = $data['trangthai'];
                $log['progress'] = $data['progress'];
                $log['task_id'] = $id;
                $log['prioty'] = $data['prioty'];
                $this->Task->add_log($log);
                #
                $respon = array('flag' => 'true', 'message' => 'Cập nhật thành công');
            }
            // echo "<pre>";
            // var_dump($post);die();

            echo json_encode($respon);

        } else {
            $this->_data['item'] = $item = $this->Task->getItem(array(
                'id' => $this->_data['arrParam']['task_id']
            ), array(
                'task' => 'public-info'
            ));
            $this->load->view('tasks/addtiendo_view', $this->_data);
        }
    }

    function task_log()
    {

        $data['count'] = $this->Task->list_log();
        $config = $this->set_config();
        $config['base_url'] = base_url('tasks/task_log');
        $config['total_rows'] = count($data['count']);
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();

        $arrParam = array();
        $arrParam['limit'] = $config['per_page'] = 10;
        $arrParam['offset'] = intval(($this->uri->segment(3, 1)) - 1) * $config['per_page'];
        $list = $this->Task->list_log($arrParam);
        // echo $this->db->last_query();die();
        // echo "<pre>";
        // var_dump($data['list']);die();
        $data['list'] = $list;
        $data['i'] = $arrParam['offset'];
        $this->load->view('tasks/task_log', $data);
    }

    public function addfile()
    {
        $fileError = array(
            '<p>The filetype you are attempting to upload is not allowed.</p>' => 'File tải lên phải có định dạng jpg|png|pdf|docx|doc|xls|xlsx|zip|zar',
            '<p>The file you are attempting to upload is larger than the permitted size.</p>' => 'File tải lên không được quá 100 Mb'
        );
        $post = $this->input->post();

        if (!empty($post)) {
            $arrParam = $this->_data['arrParam'];
            $this->load->library("form_validation");
            $this->form_validation->set_rules('name', 'Tên tài liệu', 'required|max_length[255]|is_unique[task_files.name]');
            $this->form_validation->set_rules('file_name', 'Tên file', 'required|max_length[255]');

            if ($this->form_validation->run($this) == FALSE) {
                $errors = $this->form_validation->error_array();
                $flagError = true;
            } else {
                if ($_FILES["file_upload"]['name'] != "") {
                    $upload_dir = APPPATH . '../assets/tasks/files/';
                    $ext = pathinfo($_FILES["file_upload"]['name'], PATHINFO_EXTENSION);
                    $file_name = rewriteUrl($post['file_name']);
                    $config['upload_path'] = $upload_dir;
                    $config['allowed_types'] = 'gif|jpg|png|pdf|docx|doc|xls|xlsx|zip|zar|rar|xml|xps|wps|rtf|odt|dotx|dotm|csv|xla|xlsb|xlsm|xml|ppt|pptx';
                    $config['max_size'] = '102400';
                    $config['encrypt_name'] = FALSE;

                    $config['file_name'] = $file_name . '.' . $ext;
                    if (file_exists($upload_dir . $file_name . '.' . $ext)) {
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
                        if (isset($fileError[$err]))
                            $errors['file_upload'] = $fileError[$err];
                        else
                            $errors['file_upload'] = $err;
                    }
                } else {
                    $flagError = true;
                    $errors['file_upload'] = 'Phải tải file lên.';
                }
            }

            if ($flagError == true) {
                $respon = array(
                    'flag' => 'false',
                    'errors' => $errors
                );
            } else {
                $this->load->model('TaskFiles');
                $this->TaskFiles->saveItem($arrParam, array(
                    'task' => 'add'
                ));

                $respon = array(
                    'flag' => 'true',
                    'message' => 'Cập nhật thành công'
                );
            }

            echo json_encode($respon);
        } else
            $this->load->view('tasks/addfile_view', $this->_data);
    }

    public function test()
    {
        echo APPPATH . 'assets/tasks/files/';
    }

    public function editfile()
    {
        $fileError = array(
            '<p>The filetype you are attempting to upload is not allowed.</p>' => 'File tải lên phải có định dạng jpg|png|pdf|docx|doc|xls|xlsx|zip|zar',
            '<p>The file you are attempting to upload is larger than the permitted size.</p>' => 'File tải lên không được quá 10 Mb'
        );
        
        $post = $this->input->post();

        $this->load->model('TaskFiles');
        $item = $this->TaskFiles->getItem($this->_data['arrParam'], array(
            'task' => 'public-info'
        ));

        if (!empty($post)) {
            $arrParam = $this->_data['arrParam'];
            $arrParam['file_name'] = trim($arrParam['file_name']);
            $arrParam['task_id'] = $item['task_id'];

            $this->load->library("form_validation");
            $flagError = false;
            $upload_dir = APPPATH . '../assets/tasks/files/';

            if ($_FILES["file_upload"]['name'] != "") {
                $this->form_validation->set_rules('name', 'Tên tài liệu', 'required|max_length[255]');
                $this->form_validation->set_rules('file_name', 'Tên file', 'required|max_length[255]');

                if ($this->form_validation->run($this) == FALSE) {
                    $errors = $this->form_validation->error_array();
                    $flagError = true;
                }

                if ($flagError == false) {
                    $flagError = $this->TaskFiles->validate($arrParam['name'], 'name', $arrParam['id']);
                    if ($flagError == true)
                        $errors['name'] = 'Tên tài liệu đã tồn tại.';
                }

                if ($flagError == false) {
                    $ext = pathinfo($_FILES["file_upload"]['name'], PATHINFO_EXTENSION);
                    $file_name = rewriteUrl($post['file_name']);

                    // remove file cũ
                    @unlink($upload_dir . $item['file_name']);

                    $config['upload_path'] = $upload_dir;
                    $config['allowed_types'] = 'jpg|png|pdf|docx|doc|xls|xlsx|zip|zar';
                    $config['max_size'] = '10240';
                    $config['encrypt_name'] = FALSE;
                    $config['file_name'] = $file_name . '.' . $ext;

                    if (file_exists($upload_dir . $file_name . '.' . $ext)) {
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
                        $errors['file_upload'] = $fileError[$err];
                    }
                }
            } else {
                $this->form_validation->set_rules('name', 'Tên tài liệu', 'required|max_length[255]');
                $this->form_validation->set_rules('file_name', 'Tên file', 'required|max_length[255]');

                if ($this->form_validation->run($this) == FALSE) {
                    $flagError = true;
                    $errors = $this->form_validation->error_array();
                }

                $new_file_name = rewriteUrl($arrParam['file_name']) . '.' . $item['extension'];

                if (!isset($errors['file_name']) && $new_file_name != $item['file_name']) {
                    if (file_exists($upload_dir . $new_file_name)) {
                        $flagError = true;
                        $errors['file_name'] = 'Tên File đã được sử dụng';
                    } else
                        $item['file_name'] = $new_file_name;
                }

                if ($flagError == false) {
                    $arrParam['file_name'] = $item['file_name'];
                    $arrParam['extension'] = $item['extension'];
                    $arrParam['size'] = $item['size'];
                }
            }

            if ($flagError == true) {
                $respon = array(
                    'flag' => 'false',
                    'errors' => $errors
                );
            } else {
                $this->load->model('TaskFiles');
                $this->TaskFiles->saveItem($arrParam, array(
                    'task' => 'edit'
                ));

                $respon = array(
                    'flag' => 'true',
                    'message' => 'Cập nhật thành công'
                );
            }

            echo json_encode($respon);
        } else {
            $this->_data['item'] = $item;

            $this->load->view('tasks/editfile_view', $this->_data);
        }
    }

    public function deletefile()
    {
        $post = $this->input->post();

        if (!empty($post)) {
            $this->load->model('TaskFiles');
            $this->_data['arrParam']['cid'] = $this->_data['arrParam']['file_ids'];

            $this->TaskFiles->deleteItem($this->_data['arrParam'], array(
                'task' => 'delete-multi'
            ));
        }
    }

    public function note()
    {
        $post = $this->input->post();
        if (!empty($post)) {
            $this->load->model('TaskProgress');
            $item = $this->TaskProgress->getItem($this->_data['arrParam'], array(
                'task' => 'public-info'
            ));
            $this->_data['item'] = $item;
            $this->load->view('tasks/note_view', $this->_data);
        }
    }

    public function xulytiendo()
    {
        $post = $this->input->post();
        if (!empty($post)) {
            $arrParam = $this->_data['arrParam'];
            $this->load->model('TaskProgress');
            $this->TaskProgress->saveItem($arrParam, array(
                'task' => 'update-pheduyet'
            ));

            if ($arrParam['pheduyet'] == 1) {
                $this->TaskProgress->handling($arrParam);
                $respon = array(
                    'flag' => 'true',
                    'message' => 'Cập nhật thành công',
                    'reload' => 'true'
                );
            } else {
                $respon = array(
                    'flag' => 'true',
                    'message' => 'Cập nhật thành công'
                );
            }

            echo json_encode($respon);
        } else
            $this->load->view('tasks/xulytiendo_view', $this->_data);
    }

    public function commentlist()
    {
        $this->load->model('TaskComment');
        $post = $this->input->post();
        if (!empty($post)) {
            $config['base_url'] = base_url() . 'tasks/commentlist';
            $config['total_rows'] = $this->TaskComment->countItem($this->_data['arrParam'], array(
                'task' => 'public-list'
            ));
            $config['per_page'] = $this->_paginator['per_page'];
            $config['uri_segment'] = $this->_paginator['uri_segment'];
            $config['use_page_numbers'] = TRUE;

            $this->load->library("pagination");
            $this->pagination->initialize($config);
            $this->pagination->createConfig('front-end');

            $pagination = $this->pagination->create_ajax();

            $this->_data['arrParam']['start'] = $this->uri->segment(3);
            $items = $this->TaskComment->listItem($this->_data['arrParam'], array(
                'task' => 'public-list'
            ));

            $result = array(
                'items' => $items,
                'pagination' => $pagination
            );

            echo json_encode($result);
        }
    }

    public function addcomment()
    {
        $this->load->model('TaskComment');
        $post = $this->input->post();
        $arrParam = $this->_data['arrParam'];

        if (!empty($post)) {
            $this->form_validation->set_rules('content', 'Nội dung', 'required');

            if ($this->form_validation->run($this) == FALSE) {
                $errors = $this->form_validation->error_array();
                $type = 'content';

                $response = array(
                    'flag' => 'false',
                    'msg' => current($errors),
                    'type' => $type
                );
            } else {
                $this->TaskComment->saveItem($arrParam, array(
                    'task' => 'add'
                ));
                $response = array(
                    'flag' => 'true',
                    'msg' => 'Bình luận thành công',
                    'task_id' => $arrParam['task_id']
                );
            }

            echo json_encode($response);
        }
    }

    public function link()
    {
        $this->load->model('TasksLinks');
        $this->load->model('Task');
        $post = $this->input->post();

        $this->load->library('MY_System_Info');
        $info = new MY_System_Info();
        $user_info = $info->getInfo();

        // user permission
        $task_permission = $user_info['task_permission'];

        if (!empty($post)) {
            $item = $this->Task->getItem(array(
                'id' => $post['source']
            ), array(
                'task' => 'public-info',
                'brand' => 'full'
            ));

            $is_create_task_parent = $is_implement = array();
            if (!empty($item['is_create_task'])) {
                foreach ($item['is_create_task'] as $key => $val) {
                    $keyArr = explode('-', $key);
                    if ($keyArr[0] != $post['source'])
                        $is_create_task_parent[] = $val['id'];
                }

                $is_create_task_parent = array_unique($is_create_task_parent);
            }

            if (!empty($item['is_implement'])) {
                foreach ($item['is_implement'] as $val)
                    $is_implement[] = $val['id'];

                $is_implement = array_unique($is_implement);
            }

            $flag = 'false';
            if (in_array('update_all_task', $task_permission)) {
                $flag = 'true';
            } elseif (in_array($user_info['id'], $is_implement) && in_array('update_brand_task', $task_permission)) {
                $flag = 'true';
            } elseif (in_array($user_info['id'], $is_create_task_parent)) {
                $flag = 'true';
            }

            if ($flag == 'true') {
                $arrParam = $post;
                $arrParam['user_info'] = $user_info;
                $this->TasksLinks->saveItem($arrParam, array(
                    'task' => 'add'
                ));

                $msg = 'Thực hiện tác vụ thành công';
            } else
                $msg = 'Bạn không có quyền thực hiện chức năng này.';

            $response = array(
                'flag' => $flag,
                'msg' => $msg
            );
            echo json_encode($response);
        }
    }

    public function delete()
    {
        $post = $this->input->post();
        if (!empty($post)) {
            $this->load->model('TasksLinks');

            $arrParam['id'] = $post['link_id'];
            $this->TasksLinks->deleteItem($arrParam, array(
                'task' => 'delete'
            ));
        }
    }

    public function pheduyet()
    {
        $this->load->model('Task');
        $this->load->model('TaskProgress');
        $post = $this->input->post();
        $arrParam = $this->_data['arrParam'];
        $item = $this->Task->getItem(array(
            'id' => $arrParam['task_id']
        ), array(
            'task' => 'public-info',
            'brand' => 'full'
        ));

        $this->load->library('MY_System_Info');
        $info = new MY_System_Info();
        $user_info = $info->getInfo();

        if (!empty($post)) {
            $is_pheduyet_parent = $is_pheduyet = array();
            if (!empty($item['is_pheduyet'])) {
                foreach ($item['is_pheduyet'] as $key => $val) {
                    $is_pheduyet[] = $val['id'];

                    $keyArr = explode('-', $key);
                    if ($keyArr[0] != $arrParam['id'])
                        $is_pheduyet_parent[] = $val['id'];
                }

                $is_pheduyet_parent = array_unique($is_pheduyet_parent);
            }

            $flag = 'true';
            if (empty($item)) {
                $flag = 'false';
                $msg = 'Công việc không tồn tại.';
            } else {
                if (!in_array($user_info['id'], $is_pheduyet_parent) || $item['pheduyet'] != -1) {
                    $flag = 'fasle';
                    $msg = 'Không thực hiện được tác vụ';
                }

                if ($flag == 'true') {
                    $check = $this->Task->check_parent_appoval($item);
                    if ($check == true) {
                        $flag = 'false';
                        $msg = 'Không thực hiện được tác vụ vì công việc cha chưa hoặc không được phê duyệt';
                    }
                }
            }

            if ($flag == 'true') {
                // update pheduyet
                $arrParam['id'] = $arrParam['task_id'];
                $this->Task->saveItem($arrParam, array(
                    'task' => 'pheduyet'
                ));

                // if the task is not approval
                if ($arrParam['pheduyet_select'] == 0) {
                    // percent = 0
                    $arrParam['fields'] = array(
                        'percent' => 0
                    );
                    $this->Task->saveItem($arrParam, array(
                        'task' => 'custom'
                    ));

                    // remove progress
                    $this->TaskProgress->deleteItem(array(
                        'task_ids' => array(
                            $arrParam['id']
                        )
                    ), array(
                        'task' => 'delete-multi-by-task'
                    ));
                }
            }

            $response = array(
                'flag' => $flag,
                'msg' => $msg
            );

            echo json_encode($response);
        } else {
            $this->load->view('tasks/pheduyet_view', $this->_data);
        }
    }

    public function quickupdate()
    {

        $post = $this->input->post();
        $arrParam = $this->_data['arrParam'];
        $this->load->model('Task');
        if (!empty($post)) {
            $flag = 'true';
            $item = $this->Task->getItem($arrParam, array(
                'task' => 'information'
            ));

           
            if (empty($item)) {
                $flag = 'false';
                $msg = 'Dự án/ Công việc này không tồn tại.';
            } else {
                if ($item['parent'] > 0) {
                    $parent_item = $this->Task->getItem(array(
                        'id' => $item['parent']
                    ), array(
                        'task' => 'information'
                    ));
                    $error_date = $this->validate_min_max_date($arrParam['date_start'], $arrParam['date_end'], $parent_item['date_start'], $parent_item['date_end']);
                    if (!empty($error_date)) {
                        $flag = 'false';
                        $msg = $error_date;
                    }
                }
            }

            if ($flag == 'true') {
                // update the task
                $check = $this->check_task_permission($item['id']);
                // var_dump($check);die();
                $ch = false;
                if(in_array('update_project', $check['task_permission']))
                    $ch = true;
                if(in_array('update_brand_task', $check['task_permission']) && $check['in_task']['is_implement'])
                    $ch = true;
                if(!$ch)
                {
                     $response = array(
                        'flag' => 'false',
                        'msg' => "Bạn không có quyền thao tác với dự án này"
                    );
                    echo json_encode($response);
                    return;
                }
                $date_start = str_replace('/', '-', $arrParam['date_start']);
                $arrParam['date_start'] = date('Y-m-d', strtotime($date_start));

                $date_end = str_replace('/', '-', $arrParam['date_end']);
                $arrParam['date_end'] = date('Y-m-d', strtotime($date_end));

                $datediff = strtotime($arrParam['date_end']) - strtotime($arrParam['date_start']);
                $arrParam['duration'] = floor($datediff / (60 * 60 * 24)) + 1;

                $this->Task->saveItem($arrParam, array(
                    'task' => 'quick-update'
                ));

                $msg = 'Cập nhật thành công.';
            }

            $response = array(
                'flag' => $flag,
                'msg' => $msg
            );
            echo json_encode($response);
        }
    }

    protected function check_task_permission($task_id){
        $task_info = $this->Task->getNodeInfo($task_id);
        $task = $task_info['project_id'];
        $task_permission = $this->Task->get_task_user_relation($task,$this->Employee->get_logged_in_employee_info()->id);
        $info = new MY_System_Info();
        $user_info = $info->getInfo();
        $user_info['in_task'] = $task_permission;
        return $user_info;

    }

    public function deletecv()
    {
        $post = $this->input->post();
        $this->load->model('Task');
        $this->load->model('TaskProgress');
        $this->load->model('TaskFiles');
        $this->load->model('TaskComment');
        $this->load->model('TasksLinks');
        $this->load->model('TasksRelation');
        $arrParam = $this->_data['arrParam'];

        if (!empty($post)) {
            $items = $this->Task->getItems(array(
                'cid' => $arrParam['ids']
            ), array(
                'task' => 'public-info'
            ));
            foreach ($arrParam['ids'] as $id) {
                $item = $items[$id];
                if ($item['parent'] > 0) {
                    $params = $item;
                    $params['key'] = 'trash-o';

                    $this->TaskProgress->solve($params, array(
                        'task' => 'remove'
                    ));
                }

                $this->Task->deleteItem($id);
            }
            // delete user relation
            $this->TasksRelation->deleteItem(array(
                'cid' => $arrParam['ids']
            ), array(
                'task' => 'delete-multi'
            ));

            // delete files tasks
            $this->TaskFiles->deleteItem(array(
                'task_ids' => $arrParam['ids']
            ), array(
                'task' => 'delete-by-tasks'
            ));

            // delete comment
            $this->TaskComment->deleteItem(array(
                'task_ids' => $arrParam['ids']
            ), array(
                'task' => 'delete-multi-by-task'
            ));

            // delete progress
            $this->TaskProgress->deleteItem(array(
                'task_ids' => $arrParam['ids']
            ), array(
                'task' => 'delete-multi-by-task'
            ));

            // delete links
            $this->TasksLinks->deleteItem(array(
                'task_ids' => $arrParam['ids']
            ), array(
                'task' => 'delete-multi-by-task'
            ));
        }
    }

    public function template()
    {
        $this->check_action_permission('update_task_template');
        $this->load->view('tasks/template_view', $this->_data);
    }

    public function templatelist()
    {
        $this->check_action_permission('update_task_template');
        $this->load->model('TaskTemplate');
        $this->_paginator['per_page'] = 1000;
        $this->_data['arrParam']['paginator'] = $this->_paginator;

        $post = $this->input->post();
        if (!empty($post)) {
            $config['base_url'] = base_url() . 'tasks/templatelist';
            $config['total_rows'] = $this->TaskTemplate->countItem($this->_data['arrParam'], array(
                'task' => 'template-list'
            ));

            $config['per_page'] = $this->_paginator['per_page'];
            $config['uri_segment'] = $this->_paginator['uri_segment'];
            $config['use_page_numbers'] = TRUE;

            $this->load->library("pagination");
            $this->pagination->initialize($config);
            $this->pagination->createConfig('front-end');

            $pagination = $this->pagination->create_ajax();

            $this->_data['arrParam']['start'] = $this->uri->segment(3);
            // echo "<pre>";
            // var_dump($this->_data['arrParam']);die();
            $items = $this->TaskTemplate->listItem($this->_data['arrParam'], array(
                'task' => 'template-list'
            ));
            // echo $this->db->last_query();die();
            $result = array(
                'count' => count($items),
                'items' => $items,
                'pagination' => $pagination
            );
            // var_dump($items);
            echo json_encode($result);
        }
    }

    public function listTemplateTask()
    {
        $this->check_action_permission('update_task_template');
        $arrParam = $this->_data['arrParam'];
        if (isset($arrParam['tasks'])) {
            $tasks = $tasksTmp = array();
            foreach ($arrParam['tasks'] as $val) {
                if ($val['parent'] == 'root') {
                    $val['parent'] = 0;
                    $val['level'] = 1;
                }
                $tasksTmp[$val['id']] = $val;
            }

            foreach ($tasksTmp as &$val) {
                if ($val['parent'] != '0') {
                    $val['level'] = $tasksTmp[$val['parent']]['level'] + 1;
                }

                $tasks[] = $val;
            }

            $orderings = array();
            foreach ($tasks as $val) {
                $orderings[$val['parent']][] = $val['id'];
            }

            $this->_data['items'] = $tasks;
            $this->_data['orderings'] = $orderings;
        }
        $this->load->view('tasks/listTemplateTask_view', $this->_data);
    }

    public function templateAdd()
    {
        $this->check_action_permission('update_task_template');
        $this->load->model('TaskTemplate');
        $this->load->model('Item');
        $post = $this->input->post();
        if (!empty($post)) {
            $this->load->library("form_validation");
            $this->form_validation->set_rules('template_name', 'Tên', 'required|max_length[300]|is_unique[task_template.name]');
            if ($this->form_validation->run($this) == FALSE) {
                $errors = $this->form_validation->error_array();
                $flagError = true;
            } else {
                if (!isset($this->_data['arrParam']['tasks'])) {
                    $flagError = true;
                    $errors[] = 'Phải thêm công việc cho template.';
                }
            }
            if ($flagError == false) {
                // + template
                $params['name'] = $this->_data['arrParam']['template_name'];
                $params['danhmuc'] = $this->_data['arrParam']['cate'];
                $params['parent'] = 0;
                $last_id = $this->TaskTemplate->saveItem($params, array(
                    'task' => 'add'
                ));

                // + task for template
                $arrayParent = array();
                foreach ($this->_data['arrParam']['tasks'] as $key => $params) {
                    if ($params['parent'] == 'root')
                        $params['parent'] = $last_id;
                    else
                        $params['parent'] = $arrayParent[$params['parent']];

                    $params['template_id'] = $last_id;

                    $arrayParent[$params['id']] = $this->TaskTemplate->saveItem($params, array(
                        'task' => 'add'
                    ));
                }

                $respon = array(
                    'flag' => 'true',
                    'msg' => 'Cập nhật thành công.'
                );
                $_SESSION['notice'] = 'Cập nhật thành công';
            } else {
                $respon = array(
                    'flag' => 'false',
                    'msg' => current($errors)
                );
            }
            echo json_encode($respon);
        } else {
            $data = [];
            $list_items_cate = $this->Item->list_items_cate();
            $data['list_items_cate'] = $list_items_cate;
            // $data['categories']['0'] = 'Tên dịch vụ';
            // $categories = $this->Category->sort_categories_and_sub_categories($this->Category->get_all_categories_and_sub_categories());
            // foreach ($categories as $key => $value) {
            //     $name = str_repeat('&nbsp;&nbsp;', $value['depth']) . $value['name'];
            //     $data['categories'][$key] = $name;
            // }
            $this->load->view('tasks/templateAdd_view', $data);
        }
    }

    public function editTemplate()
    {
        $this->load->model('TaskTemplate');
        $this->load->model('Item');
        $post = $this->input->post();
        if (!empty($post)) {
            $this->_data['arrParam']['name'] = $this->_data['arrParam']['template_name'];
            $this->_data['arrParam']['danhmuc'] = $this->_data['arrParam']['cate'];
            $flagError = false;
            if (!isset($this->_data['arrParam']['tasks'])) {
                $flagError = true;
                $errors[] = 'Phải thêm công việc cho template.';
            }

            if ($flagError == false) {
                // update template
                $last_id = $this->TaskTemplate->saveItem($this->_data['arrParam'], array(
                    'task' => 'edit'
                ));
                // delete tasks of template
                $this->TaskTemplate->deleteItem($this->_data['arrParam'], array(
                    'task' => 'delete-task-of-template'
                ));

                // + task for template
                $arrayParent = array();
                foreach ($this->_data['arrParam']['tasks'] as $key => $params) {
                    if ($params['parent'] == 'root')
                        $params['parent'] = $last_id;
                    else
                        $params['parent'] = $arrayParent[$params['parent']];

                    $params['template_id'] = $last_id;
                    $arrayParent[$params['id']] = $this->TaskTemplate->saveItem($params, array(
                        'task' => 'add'
                    ));
                }

                $respon = array(
                    'flag' => 'true',
                    'msg' => 'Cập nhật thành công.'
                );
                $_SESSION['notice'] = 'Cập nhật thành công';
            } else {
                $respon = array(
                    'flag' => 'false',
                    'msg' => current($errors)
                );
            }
            echo json_encode($respon);
        } else {
            $id = $this->uri->segment(3);
            $item = $this->TaskTemplate->getItem(array(
                'id' => $id
            ));

            $data = [];
            $data['categories']['0'] = 'Loại dịch vụ';
            $categories = $this->Category->sort_categories_and_sub_categories($this->Category->get_all_categories_and_sub_categories());
            foreach ($categories as $key => $value) {
                $name = str_repeat('&nbsp;&nbsp;', $value['depth']) . $value['name'];
                $data['categories'][$key] = $name;
            }
            $list_items_cate = $this->Item->list_items_cate();
            $data['list_items_cate'] = $list_items_cate;
            $this->_data['item'] = $item;
            $this->_data['categories'] = $data;
            $this->load->view('tasks/editTemplate_view', $this->_data);
        }
    }

    public function deleteTemplate()
    {
        $this->check_action_permission('update_task_template');
        $post = $this->input->post();
        if (!empty($post)) {
            $this->load->model('TaskTemplate');
            $this->TaskTemplate->deleteItem($this->_data['arrParam'], array(
                'task' => 'delete'
            ));
        }
    }

    public function addcvtemplate()
    {
        $danhmucKH = $this->Customer->get_danh_muc_khach_hang('customers_type');
        $danhmuc = [];
        foreach ($danhmucKH as $record) {
            $danhmuc[$record['id']] = $record['name'];
        }
        $this->_data['danhmuc'] = $danhmuc;
        $this->load->view('tasks/addcvtemplate_view', $this->_data);
    }

    public function project()
    {
        $this->load->view('tasks/project_view', $this->_data);
    }

    public function addProject()
    {
        $this->load->view('tasks/projectAdd_view', $this->_data);
    }

    public function projectlist()
    {
        $this->load->model('Task');
        $this->_paginator['per_page'] = 20;
        $this->_data['arrParam']['paginator'] = $this->_paginator;
        $post = $this->input->post();

        if (!empty($post)) {
            $config['base_url'] = base_url() . 'tasks/projectlist';
            $config['total_rows'] = $this->Task->countItem($this->_data['arrParam'], array(
                'task' => 'public-list'
            ));

            $config['per_page'] = $this->_paginator['per_page'];
            $config['uri_segment'] = $this->_paginator['uri_segment'];
            $config['use_page_numbers'] = TRUE;

            $this->load->library("pagination");
            $this->pagination->initialize($config);
            $this->pagination->createConfig('front-end');

            $pagination = $this->pagination->create_ajax();

            $this->_data['arrParam']['start'] = $this->uri->segment(3);
            $items = $this->Task->listItem($this->_data['arrParam'], array(
                'task' => 'public-list'
            ));

            $result = array(
                'count' => $config['total_rows'],
                'items' => $items,
                'pagination' => $pagination
            );
            echo json_encode($result);
        }
    }

// public function ttt(){echo $_SESSION['person_id'];}
    public function sort()
    {
        $this->load->model('Task');
        $post = $this->input->post();
        if (!empty($post)) {
            $checkExist = $this->Task->checkItemExist($this->_data['arrParam']['id']);
            if ($checkExist) {
                $this->Task->sort($this->_data['arrParam']);
                $resonse = array(
                    'flag' => 'true',
                    'msg' => 'Cập nhật thành công.'
                );
            } else
                $resonse = array(
                    'flag' => 'false',
                    'msg' => 'Dự án/ Công việc không tồn tại.'
                );

            echo json_encode($resonse);
        }
    }

    public function projectGridList()
    {
        $this->load->model('Task');
        $post = $this->input->post();

        if (!empty($post)) {
            $config['base_url'] = base_url() . 'tasks/gridList';
            $config['total_rows'] = $this->Task->countItem($this->_data['arrParam'], array(
                'task' => 'grid-project'
            ));

            $config['per_page'] = $this->_paginator['per_page'];
            $config['uri_segment'] = $this->_paginator['uri_segment'];
            $config['use_page_numbers'] = TRUE;

            $this->load->library("pagination");
            $this->pagination->initialize($config);
            $this->pagination->createConfig('front-end');

            $pagination = $this->pagination->create_ajax();
            $this->_data['arrParam']['start'] = $this->uri->segment(3);

            $items = $this->Task->listItem($this->_data['arrParam'], array(
                'task' => 'grid-project'
            ));

            // TODO
            foreach ($items as $key => &$item) {
                $item['pheduyet_total'] = $this->TaskProgress->countItem([
                    'task_id' => $item['id']
                ], array(
                    'task' => 'pheduyet-list'
                ));
                $page = empty($pagination['current'])? 1 : $pagination['current'];
                $item['stt'] = ($page-1)*10 + $key +1;
            }

            $result = array(
                'count' => $config['total_rows'],
                'items' => $items,
                'pagination' => $pagination
            );

            echo json_encode($result);
        }
    }

    public function tasks_child_statistic()
    {
        $post = $this->input->post();
        $this->load->model('Task');
        if (!empty($post)) {
            $all = $this->Task->statistic($this->_data['arrParam'], array(
                'task' => 'task-by-project'
            ));
            $implement = $this->Task->statistic($this->_data['arrParam'], array(
                'task' => 'task-by-project-implement'
            ));
            $xem = $this->Task->statistic($this->_data['arrParam'], array(
                'task' => 'task-by-project-cc'
            ));
            $cancel = $this->Task->statistic($this->_data['arrParam'], array(
                'task' => 'task-by-project-trangthai',
                'type' => 'cancel'
            ));
            $not_done = $this->Task->statistic($this->_data['arrParam'], array(
                'task' => 'task-by-project-trangthai',
                'type' => 'not-done'
            ));
            $unfulfilled = $this->Task->statistic($this->_data['arrParam'], array(
                'task' => 'task-by-project-trangthai',
                'type' => 'unfulfilled'
            ));
            $processing = $this->Task->statistic($this->_data['arrParam'], array(
                'task' => 'task-by-project-trangthai',
                'type' => 'processing'
            ));
            $slow_proccessing = $this->Task->statistic($this->_data['arrParam'], array(
                'task' => 'task-by-project-trangthai',
                'type' => 'slow_proccessing'
            ));
            $finish = $this->Task->statistic($this->_data['arrParam'], array(
                'task' => 'task-by-project-trangthai',
                'type' => 'finish'
            ));
            $slow_finish = $this->Task->statistic($this->_data['arrParam'], array(
                'task' => 'task-by-project-trangthai',
                'type' => 'slow-finish'
            ));

            $data = array(
                'all' => $all,
                'implement' => $implement,
                'xem' => $xem,
                'cancel' => $cancel,
                'not_done' => $not_done,
                'unfulfilled' => $unfulfilled,
                'processing' => $processing,
                'slow_proccessing' => $slow_proccessing,
                'finish' => $finish,
                'slow_finish' => $slow_finish
            );
            echo json_encode($data);
        }
    }

    public function tasks_statistic()
    {
        $post = $this->input->post();
        $this->load->model('Task');
        if (!empty($post)) {
            $all = $this->Task->statistic($this->_data['arrParam'], array(
                'task' => 'task-by-all'
            ));
            $implement = $this->Task->statistic($this->_data['arrParam'], array(
                'task' => 'task-by-all-implement'
            ));
            $xem = $this->Task->statistic($this->_data['arrParam'], array(
                'task' => 'task-by-all-cc'
            ));
            $cancel = $this->Task->statistic($this->_data['arrParam'], array(
                'task' => 'task-by-all-trangthai',
                'type' => 'cancel'
            ));
            $not_done = $this->Task->statistic($this->_data['arrParam'], array(
                'task' => 'task-by-all-trangthai',
                'type' => 'not-done'
            ));
            $unfulfilled = $this->Task->statistic($this->_data['arrParam'], array(
                'task' => 'task-by-all-trangthai',
                'type' => 'unfulfilled'
            ));
            $processing = $this->Task->statistic($this->_data['arrParam'], array(
                'task' => 'task-by-all-trangthai',
                'type' => 'processing'
            ));
            $slow_proccessing = $this->Task->statistic($this->_data['arrParam'], array(
                'task' => 'task-by-all-trangthai',
                'type' => 'slow_proccessing'
            ));
            $finish = $this->Task->statistic($this->_data['arrParam'], array(
                'task' => 'task-by-all-trangthai',
                'type' => 'finish'
            ));
            $slow_finish = $this->Task->statistic($this->_data['arrParam'], array(
                'task' => 'task-by-all-trangthai',
                'type' => 'slow-finish'
            ));

            $data = array(
                'all' => $all,
                'implement' => $implement,
                'xem' => $xem,
                'cancel' => $cancel,
                'not_done' => $not_done,
                'unfulfilled' => $unfulfilled,
                'processing' => $processing,
                'slow_proccessing' => $slow_proccessing,
                'finish' => $finish,
                'slow_finish' => $slow_finish
            );
            echo json_encode($data);
        }
    }

    public function taskDetail()
    {
        $this->load->model('Task');
        $task_id = $this->input->post('task_id');

        $task_info = $this->Task->getItem(array(
            'id' => $task_id
        ), array(
            'task' => 'public-info',
            'brand' => 'full'
        ));
        $parent_item = $this->Task->getItem(array(
            'id' => $task_info['parent']
        ), array(
            'task' => 'public-info'
        ));;
        $data = [];
        $data['task_info'] = $task_info;
        $data['parent_item'] = $parent_item;
        // TODO
        $data['progress_total'] = $this->TaskProgress->countItem([
            'task_id' => $task_id
        ], array(
            'task' => 'public-list'
        ));
        $data['progress_items'] = $this->TaskProgress->listItem([
            'task_id' => $task_id
        ], array(
            'task' => 'public-list'
        ));

        echo json_encode(array(
            'success' => true,
            'html' => $this->load->view('customers/partial/modal_task_detail', $data, TRUE)
        ));
    }

    public function taskByProjectList()
    {
        $this->load->model('Task');
        $post = $this->input->post();

        if (!empty($post)) {
            $project_id = $this->_data['arrParam']['project_id'];

            $result = $this->Task->listItem($this->_data['arrParam'], array(
                'task' => 'task-by-project'
            ));

            $project = $result['project'];
            $items = $result['ketqua'];

            $items = array_merge($items, array());
            $items = (!empty($items)) ? $items : array();
            // TODO
            foreach ($items as &$item) {
                $item['pheduyet_total'] = $this->TaskProgress->countItem([
                    'task_id' => $item['id']
                ], array(
                    'task' => 'pheduyet-list'
                ));
            }
            $result = array(
                'items' => $items,
                'project' => $project
            );

            echo json_encode($result);
        }
    }

    public function grid()
    {
        // echo "<pre>"; print_r($this->_data); die();
        $this->load->view('tasks/grid_view', $this->_data);
    }

    public function task_list()
    {
        $data = $this->_data;
        $data['employees'] = $this->Employee->selectBoxById();
        // echo "<pre>"; print_r($data); die();
        $this->load->view('tasks/task_list_view', $data);
    }

    public function task_list_store()
    {
        $this->load->model('Task');
        $post = $this->input->post();
        if (!empty($post)) {
            $config['base_url'] = base_url() . 'tasks/task_list_store';
            $config['total_rows'] = $this->Task->count_item($this->_data['arrParam']);

            $config['per_page'] = $this->_paginator['per_page'];
            $config['uri_segment'] = $this->_paginator['uri_segment'];
            $config['use_page_numbers'] = TRUE;

            $this->load->library("pagination");
            $this->pagination->initialize($config);
            $this->pagination->createConfig('front-end');

            $pagination = $this->pagination->create_ajax();

            $this->_data['arrParam']['start'] = $this->uri->segment(3);
            // echo "<pre>";
            // var_dump($this->_data['arrParam']);die();
            $items = $this->Task->list_item($this->_data['arrParam']);


            $result = array(
                'count' => $config['total_rows'],
                'items' => $items,
                'pagination' => $pagination
            );
            // echo "<pre>"; var_dump($result); 
            echo json_encode($result);
        }
    }

    public function add_personal()
    {
        $arrParam = $this->_data['arrParam'];
        $this->load->model('TaskPersonal');
        $this->load->model('TaskPersonalProgress');
        $post = $this->input->post();
        $this->_data['employees'] = $this->Employee->get_list_employees_by_location($this->Employee->get_logged_in_employee_current_location_id());
        // echo "<pre>";
        // var_dump($employees);die();


        if (!empty($post)) {
            $this->load->library("form_validation");
            $this->form_validation->set_rules('name', 'Tiêu đề', 'required|max_length[255]');
            $this->form_validation->set_rules('progress', 'Tiến độ', 'required|greater_than[-1]|less_than[101]');
            $this->form_validation->set_rules('date_start', 'Bắt đầu', 'required');
            $this->form_validation->set_rules('date_end', 'Kết thúc', 'required');

            $flagError = false;

            if ($this->form_validation->run($this) == FALSE) {
                $errors = $this->form_validation->error_array();
                if (isset($errors['date_start']) && !isset($errors['date_end']))
                    $errors['date_end'] = '.';

                if (!isset($errors['date_start']) && isset($errors['date_end']))
                    $errors['date_start'] = '.';

                $flagError = true;
            } else {
                // time valid
                $arrParam['date_start'] = date('Y-m-d', strtotime($arrParam['date_start']));

                $arrParam['date_end'] = date('Y-m-d', strtotime($arrParam['date_end']));

                $datediff = strtotime($arrParam['date_end']) - strtotime($arrParam['date_start']);
                $arrParam['duration'] = floor($datediff / (60 * 60 * 24));
                if ($arrParam['duration'] < 0) {
                    $flagError = true;
                    $errors['date_start'] = '.';
                    $errors['date_end'] = 'Ngày kết thúc phải kể từ ngày bắt đầu.';
                }
            }
            if ($flagError == false) {
                // covert status and progress

                $arrParam = $this->convert_progress_task($arrParam);
                // $arrParam['date_start'] = $this->input->post('date_start_formatted');

                // $arrParam['date_end'] = $this->input->post('date_end_formatted');
                // echo "<pre>";var_dump($arrParam);die();
                $last_id = $this->TaskPersonal->saveItem($arrParam, array(
                    'task' => 'add'
                ));

                // update first progress
                $params = array(
                    'task_id' => $last_id,
                    'trangthai' => $arrParam['trangthai'],
                    'prioty' => $arrParam['prioty'],
                    'progress' => $arrParam['progress'],
                    'note' => ''
                );

                $this->TaskPersonalProgress->saveItem($params, array(
                    'task' => 'add'
                ));

                $response = array(
                    'flag' => 'true',
                    'msg' => 'Cập nhật thành công'
                );
            } else {
                $response = array(
                    'flag' => 'false',
                    'errors' => $errors
                );
            }

            echo json_encode($response);
        } else {
            $this->load->library('MY_System_Info');
            $info = new MY_System_Info();
            $user_info = $info->getInfo();
            $this->_data['user_info'] = $user_info;
            $this->_data['type'] = $arrParam['type'];

            $this->load->view('tasks/add_personal_form_view', $this->_data);
        }
    }

    public function edit_personal()
    {
        $this->load->model('TaskPersonal');

        $arrParam = $this->_data['arrParam'];
        $post = $this->input->post();
        $item = $this->TaskPersonal->getItem(array(
            'id' => $arrParam['id']
        ), array(
            'task' => 'public-info'
        ));
        // echo "<pre>";
        // var_dump($item);die();
        $this->_data['employees'] = $this->Employee->get_all()->result_array();
        if (!empty($post)) {
            $this->load->library("form_validation");
            $this->form_validation->set_rules('name', 'Tiêu đề', 'required|max_length[255]');
            $this->form_validation->set_rules('progress', 'Tiến độ', 'required|greater_than[-1]|less_than[101]');
            $this->form_validation->set_rules('date_start', 'Bắt đầu', 'required');
            $this->form_validation->set_rules('date_end', 'Kết thúc', 'required');
            $arrParam['date_start'] = $arrParam['date_start_formatted'];
            $arrParam['date_end'] = $arrParam['date_end_formatted'];
            $flagError = false;

            if ($this->form_validation->run($this) == FALSE) {
                $errors = $this->form_validation->error_array();
                if (isset($errors['date_start']) && !isset($errors['date_end']))
                    $errors['date_end'] = '.';

                if (!isset($errors['date_start']) && isset($errors['date_end']))
                    $errors['date_start'] = '.';

                $flagError = true;
            } else {
                // time valid
                $datediff = strtotime($arrParam['date_end']) - strtotime($arrParam['date_start']);
                if ($datediff < 0) {
                    $flagError = true;
                    $errors['date_start'] = '.';
                    $errors['date_end'] = 'Ngày kết thúc phải sau ngày bắt đầu.';
                }
            }
            if ($flagError == false) {
                // covert status and progress
                $arrParam = $this->convert_progress_task($arrParam);
                if (!empty($arrParam['date_start'])) {
                    $date_parts = explode('-', $arrParam['date_start']);
                    $arrParam['date_start'] = $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0] . ' 00:00:00';
                }
                if (!empty($arrParam['date_end'])) {
                    $date_parts = explode('-', $arrParam['date_end']);
                    $arrParam['date_end'] = $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0] . ' 00:00:00';
                }
                $last_id = $this->TaskPersonal->saveItem($arrParam, array(
                    'task' => 'edit'
                ));

                $response = array(
                    'flag' => 'true',
                    'msg' => 'Cập nhật thành công'
                );
            } else {
                $response = array(
                    'flag' => 'false',
                    'errors' => $errors
                );
            }

            echo json_encode($response);
        } else {
            $this->load->library('MY_System_Info');
            $info = new MY_System_Info();
            $user_info = $info->getInfo();
            $id_admin = $user_info['id'];
            $permissions = $user_info['task_permission'];

            // Check Permission Edit
            $this->_data['mode'] = 'edit';
            if (!in_array('update_personal_task', $permissions)) {
                $this->_data['mode'] = 'view';
            }
            $view = 'tasks/edit_personal_form_view';
            if (in_array($id_admin, $item['implement_ids']) || in_array($id_admin, $item['xem_ids'])) {
                $this->_data['mode'] = 'edit';
            }
//            if ($id_admin == $item['created_by'])
//                $view = 'tasks/edit_personal_form_view';
//            elseif (in_array($id_admin, $item['implement_ids']) || in_array($id_admin, $item['xem_ids']))
//            //    $view = 'tasks/quickupdate_personal_view';
//                $view = 'tasks/edit_personal_form_view';

            $this->_data['item'] = $item;
            // echo "<pre>";
            // var_dump($item);die();
            $this->_data['type'] = $arrParam['type'];
            if (!empty($view))
                $this->load->view($view, $this->_data);
        }
    }

    public function delete_personal()
    {
        $post = $this->input->post();
        if (!empty($post)) {
            $cid = $this->_data['arrParam']['ids'];

            $this->load->model('TaskPersonal');
            $this->load->model('TaskPersonalProgress');
            $this->load->model('TaskPersonalFiles');
            $this->load->model('TaskPersonalComment');

            $this->TaskPersonal->deleteItem(array(
                'cid' => $cid
            ), array(
                'task' => 'delete-multi'
            ));
            $this->TaskPersonalProgress->deleteItem(array(
                'cid' => $cid
            ), array(
                'task' => 'delete-multi-by-task'
            ));
            $this->TaskPersonalFiles->deleteItem(array(
                'cid' => $cid
            ), array(
                'task' => 'delete-by-tasks'
            ));
            $this->TaskPersonalComment->deleteItem(array(
                'cid' => $cid
            ), array(
                'task' => 'delete-multi-by-task'
            ));
        }
    }

    public function personal()
    {
        $this->load->view('tasks/personal_grid_view', $this->_data);
    }

    public function personalList()
    {
        $this->load->model('TaskPersonal');
        $this->_paginator['per_page'] = 20;
        $this->_data['arrParam']['paginator'] = $this->_paginator;
        // var_dump($this->_data['arrParam']);die();
        $post = $this->input->post();

        if (!empty($post)) {
            $config['base_url'] = base_url() . 'tasks/personalList';
            $config['total_rows'] = $this->TaskPersonal->countItem($this->_data['arrParam']);

            $config['per_page'] = $this->_paginator['per_page'];
            $config['uri_segment'] = $this->_paginator['uri_segment'];
            $config['use_page_numbers'] = TRUE;

            $this->load->library("pagination");
            $this->pagination->initialize($config);
            $this->pagination->createConfig('front-end');

            $pagination = $this->pagination->create_ajax();

            $this->_data['arrParam']['start'] = $this->uri->segment(3);
            $items = $this->TaskPersonal->listItem($this->_data['arrParam']);

            $result = array(
                'count' => $config['total_rows'],
                'items' => $items,
                'pagination' => $pagination
            );

            echo json_encode($result);
        }
    }

    public function add_personal_tiendo()
    {
        $this->load->model('TaskPersonalProgress');
        $this->load->model('TaskPersonal');
        $post = $this->input->post();
        $arrParam = $this->_data['arrParam'];

        $item = $this->TaskPersonal->getItem(array(
            'id' => $arrParam['task_id']
        ), array(
            'task' => 'information'
        ));
        if (!empty($post)) {
            $flag = 'true';
            if (empty($item)) {
                $flag = 'false';
                $msg = 'Công việc không tồn tại';
            } else {
                $this->form_validation->set_rules('progress', 'Tiến độ', 'required|greater_than[-1]|less_than[101]');
                if ($this->form_validation->run($this) == FALSE) {
                    $errors = $this->form_validation->error_array();
                    $flag = 'false';
                    $msg = current($errors);
                }
            }

            if ($flag == 'true') {
                $arrParam = $this->convert_progress_task($arrParam);
                $params = array(
                    'id' => $arrParam['task_id'],
                    'trangthai' => $arrParam['trangthai'],
                    'progress' => $arrParam['progress']
                );
                $this->TaskPersonalProgress->saveItem($arrParam, array(
                    'task' => 'add'
                ));
                $this->TaskPersonal->saveItem($params, array(
                    'task' => 'update-progress'
                ));

                $msg = 'Cập nhật thành công.';
            }

            $response = array(
                'flag' => $flag,
                'msg' => $msg
            );

            echo json_encode($response);
        } else {
            $this->_data['item'] = $item;
            $this->load->view('tasks/add_personal_tiendo_view', $this->_data);
        }
    }

    public function personal_progress_list()
    {
        $this->load->model('TaskPersonalProgress');
        $post = $this->input->post();

        if (!empty($post)) {
            $config['base_url'] = base_url() . 'tasks/personal_progress_list';
            $config['total_rows'] = $this->TaskPersonalProgress->countItem($this->_data['arrParam'], array(
                'task' => 'public-list'
            ));

            $config['per_page'] = $this->_paginator['per_page'];
            $config['uri_segment'] = $this->_paginator['uri_segment'];
            $config['use_page_numbers'] = TRUE;

            $this->load->library("pagination");
            $this->pagination->initialize($config);
            $this->pagination->createConfig('front-end');

            $pagination = $this->pagination->create_ajax();

            $this->_data['arrParam']['start'] = $this->uri->segment(3);
            $items = $this->TaskPersonalProgress->listItem($this->_data['arrParam'], array(
                'task' => 'public-list'
            ));

            $result = array(
                'count' => $config['total_rows'],
                'items' => $items,
                'pagination' => $pagination
            );

            echo json_encode($result);
        }
    }

    public function add_personal_file()
    {
        $fileError = $this->_data['file_errors'];
        $post = $this->input->post();

        if (!empty($post)) {
            $arrParam = $this->_data['arrParam'];
            $this->load->library("form_validation");
            $this->form_validation->set_rules('name', 'Tên tài liệu', 'required|max_length[255]|is_unique[task_files.name]');
            $this->form_validation->set_rules('file_name', 'Tên file', 'required|max_length[255]|is_unique[task_files.file_name]');

            if ($this->form_validation->run($this) == FALSE) {
                $errors = $this->form_validation->error_array();
                $flagError = true;
            } else { 

                if ($_FILES["file_upload"]['name'] != "") {
                    $upload_dir = APPPATH . '../assets/task_personal/';
                    // var_dump($upload_dir);die();
                    $ext = pathinfo($_FILES["file_upload"]['name'], PATHINFO_EXTENSION);
                    $file_name = rewriteUrl($post['file_name']);
                    $config['upload_path'] = $upload_dir;
                    $config['allowed_types'] = 'gif|jpg|png|pdf|docx|doc|xls|xlsx|zip|zar|rar|xml|xps|wps|rtf|odt|dotx|dotm|csv|xla|xlsb|xlsm|xml|ppt|pptx';
                    $config['max_size'] = '102400';
                    $config['encrypt_name'] = FALSE;

                    $config['file_name'] = $file_name . '.' . $ext;
                    if (file_exists($upload_dir . $file_name . '.' . $ext)) {
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
                        if (isset($fileError[$err]))
                            $errors['file_upload'] = $fileError[$err];
                        else
                            $errors['file_upload'] = $err;
                    }
                } else {
                    $flagError = true;
                    $errors['file_upload'] = 'Phải tải file lên.';
                }
            }

            if ($flagError == true) {
                $respon = array(
                    'flag' => 'false',
                    'errors' => $errors
                );
            } else {
                $this->load->model('TaskPersonalFiles');
                $this->TaskPersonalFiles->saveItem($arrParam, array(
                    'task' => 'add'
                ));

                $respon = array(
                    'flag' => 'true',
                    'message' => 'Cập nhật thành công'
                );
            }

            echo json_encode($respon);
        } else
            $this->load->view('tasks/add_personal_file_view', $this->_data);
    }

    public function edit_personal_file()
    {
        $this->load->model('TaskPersonalFiles');
        $fileError = array(
            '<p>The filetype you are attempting to upload is not allowed.</p>' => 'File tải lên phải có định dạng jpg|png|pdf|docx|doc|xls|xlsx|zip|zar',
            '<p>The file you are attempting to upload is larger than the permitted size.</p>' => 'File tải lên không được quá 10 Mb'
        );

        $item = $this->TaskPersonalFiles->getItem($this->_data['arrParam'], array(
            'task' => 'public-info'
        ));
        $post = $this->input->post();
        if (!empty($post)) {
            $arrParam = $this->_data['arrParam'];
            $arrParam['file_name'] = trim($arrParam['file_name']);
            $arrParam['task_id'] = $item['task_id'];

            $this->load->library("form_validation");
            $flagError = false;
            $upload_dir = FILE_TASK_PATH;

            if ($_FILES["file_upload"]['name'] != "") {
                $this->form_validation->set_rules('name', 'Tên tài liệu', 'required|max_length[255]');
                $this->form_validation->set_rules('file_name', 'Tên file', 'required|max_length[255]');

                if ($this->form_validation->run($this) == FALSE) {
                    $errors = $this->form_validation->error_array();
                    $flagError = true;
                }

                if ($flagError == false) {
                    $flagError = $this->TaskPersonalFiles->validate($arrParam['name'], 'name', $arrParam['id']);
                    if ($flagError == true)
                        $errors['name'] = 'Tên tài liệu đã tồn tại.';
                }

                if ($flagError == false) {
                    $ext = pathinfo($_FILES["file_upload"]['name'], PATHINFO_EXTENSION);
                    $file_name = rewriteUrl($post['file_name']);

                    // remove file cũ
                    @unlink($upload_dir . $item['file_name']);

                    $config['upload_path'] = $upload_dir;
                    $config['allowed_types'] = 'gif|jpg|png|pdf|docx|doc|xls|xlsx|zip|zar|rar|xml|xps|wps|rtf|odt|dotx|dotm|csv|xla|xlsb|xlsm|xml|ppt|pptx';
                    $config['max_size'] = '102400';
                    $config['encrypt_name'] = FALSE;
                    $config['file_name'] = $file_name . '.' . $ext;

                    if (file_exists($upload_dir . $file_name . '.' . $ext)) {
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
                        $errors['file_upload'] = $fileError[$err];
                    }
                }
            } else {
                $this->form_validation->set_rules('name', 'Tên tài liệu', 'required|max_length[255]');
                $this->form_validation->set_rules('file_name', 'Tên file', 'required|max_length[255]');

                if ($this->form_validation->run($this) == FALSE) {
                    $flagError = true;
                    $errors = $this->form_validation->error_array();
                }

                $new_file_name = rewriteUrl($arrParam['file_name']) . '.' . $item['extension'];

                if (!isset($errors['file_name']) && $new_file_name != $item['file_name']) {
                    if (file_exists($upload_dir . $new_file_name)) {
                        $flagError = true;
                        $errors['file_name'] = 'Tên File đã được sử dụng';
                    } else
                        $item['file_name'] = $new_file_name;
                }

                if ($flagError == false) {
                    $arrParam['file_name'] = $item['file_name'];
                    $arrParam['extension'] = $item['extension'];
                    $arrParam['size'] = $item['size'];
                }
            }

            if ($flagError == true) {
                $respon = array(
                    'flag' => 'false',
                    'errors' => $errors
                );
            } else {
                $this->TaskPersonalFiles->saveItem($arrParam, array(
                    'task' => 'edit'
                ));

                $respon = array(
                    'flag' => 'true',
                    'message' => 'Cập nhật thành công'
                );
            }

            echo json_encode($respon);
        } else {
            $this->_data['item'] = $item;
            $this->load->view('tasks/edit_personal_file_view', $this->_data);
        }
    }

    public function delete_personal_file()
    {
        $post = $this->input->post();

        if (!empty($post)) {
            $this->load->model('TaskPersonalFiles');
            $cid = $this->_data['arrParam']['file_ids'];

            $this->TaskPersonalFiles->deleteItem(array(
                'cid' => $cid
            ), array(
                'task' => 'delete-multi'
            ));
        }
    }

    public function personel_file_list()
    {
        $this->load->model('TaskPersonalFiles');
        $post = $this->input->post();

        if (!empty($post)) {
            $config['base_url'] = base_url() . 'tasks/personel_file_list';
            $config['total_rows'] = $this->TaskPersonalFiles->countItem($this->_data['arrParam'], array(
                'task' => 'public-list'
            ));

            $config['per_page'] = $this->_paginator['per_page'];
            $config['uri_segment'] = $this->_paginator['uri_segment'];
            $config['use_page_numbers'] = TRUE;

            $this->load->library("pagination");
            $this->pagination->initialize($config);
            $this->pagination->createConfig('front-end');

            $pagination = $this->pagination->create_ajax();

            $this->_data['arrParam']['start'] = $this->uri->segment(3);
            $items = $this->TaskPersonalFiles->listItem($this->_data['arrParam'], array(
                'task' => 'public-list'
            ));

            $result = array(
                'count' => $config['total_rows'],
                'items' => $items,
                'pagination' => $pagination
            );

            echo json_encode($result);
        }
    }

    public function add_personal_comment()
    {
        $this->load->model('TaskPersonalComment');
        $post = $this->input->post();
        $arrParam = $this->_data['arrParam'];

        if (!empty($post)) {
            $this->form_validation->set_rules('content', 'Nội dung', 'required');

            if ($this->form_validation->run($this) == FALSE) {
                $errors = $this->form_validation->error_array();
                $type = 'content';

                $response = array(
                    'flag' => 'false',
                    'msg' => current($errors),
                    'type' => $type
                );
            } else {
                $this->TaskPersonalComment->saveItem($arrParam, array(
                    'task' => 'add'
                ));
                $response = array(
                    'flag' => 'true',
                    'msg' => 'Bình luận thành công',
                    'task_id' => $arrParam['task_id']
                );
            }

            echo json_encode($response);
        }
    }

    public function personal_comment_list()
    {
        $this->load->model('TaskPersonalComment');
        $post = $this->input->post();
        if (!empty($post)) {
            $config['base_url'] = base_url() . 'tasks/personal_comment_list';
            $config['total_rows'] = $this->TaskPersonalComment->countItem($this->_data['arrParam'], array(
                'task' => 'public-list'
            ));
            $config['per_page'] = $this->_paginator['per_page'];
            $config['uri_segment'] = $this->_paginator['uri_segment'];
            $config['use_page_numbers'] = TRUE;

            $this->load->library("pagination");
            $this->pagination->initialize($config);
            $this->pagination->createConfig('front-end');

            $pagination = $this->pagination->create_ajax();

            $this->_data['arrParam']['start'] = $this->uri->segment(3);
            $items = $this->TaskPersonalComment->listItem($this->_data['arrParam'], array(
                'task' => 'public-list'
            ));

            $result = array(
                'items' => $items,
                'pagination' => $pagination
            );

            echo json_encode($result);
        }
    }

    public function personal_statistic()
    {
        $this->load->model('TaskPersonal');
        $post = $this->input->post();
        if (!empty($post)) {
            $all = $this->TaskPersonal->statistic($this->_data['arrParam'], array(
                'task' => 'task-by-project'
            ));
            $cancel = $this->TaskPersonal->statistic($this->_data['arrParam'], array(
                'task' => 'task-by-project-trangthai',
                'type' => 'cancel'
            ));
            $not_done = $this->TaskPersonal->statistic($this->_data['arrParam'], array(
                'task' => 'task-by-project-trangthai',
                'type' => 'not-done'
            ));
            $unfulfilled = $this->TaskPersonal->statistic($this->_data['arrParam'], array(
                'task' => 'task-by-project-trangthai',
                'type' => 'unfulfilled'
            ));
            $processing = $this->TaskPersonal->statistic($this->_data['arrParam'], array(
                'task' => 'task-by-project-trangthai',
                'type' => 'processing'
            ));
            $slow_proccessing = $this->TaskPersonal->statistic($this->_data['arrParam'], array(
                'task' => 'task-by-project-trangthai',
                'type' => 'slow_proccessing'
            ));
            $finish = $this->TaskPersonal->statistic($this->_data['arrParam'], array(
                'task' => 'task-by-project-trangthai',
                'type' => 'finish'
            ));
            $slow_finish = $this->TaskPersonal->statistic($this->_data['arrParam'], array(
                'task' => 'task-by-project-trangthai',
                'type' => 'slow-finish'
            ));

            $data = array(
                'all' => $all,
                'cancel' => $cancel,
                'not_done' => $not_done,
                'unfulfilled' => $unfulfilled,
                'processing' => $processing,
                'slow_proccessing' => $slow_proccessing,
                'finish' => $finish,
                'slow_finish' => $slow_finish
            );

            echo json_encode($data);
        }
    }

    public function valid_date($str)
    {
        $regular_string = '/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4})$/';
        if (preg_match($regular_string, $str)) {
            return true;
        } else {

            $this->form_validation->set_message('valid_date', '%s phải mang định dạng m-y-D');
            return false;
        }
    }

    public function validate_min_max_date($date_start, $date_end, $date_start_limit, $date_end_limit)
    {
        $error = '';
        $datediff_start = strtotime($date_start) - strtotime($date_start_limit);
        $datediff_end = strtotime($date_end) - strtotime($date_end_limit);

        $date_start_limit = date('d-m-Y', strtotime($date_start_limit));
        $date_end_limit = date('d-m-Y', strtotime($date_end_limit));

        if ($datediff_start < 0 || $datediff_end > 0) {
            $error = 'Thời gian chỉ trong khoảng từ ' . $date_start_limit . ' đến ' . $date_end_limit;
        }
        return $error;
    }

    public function update_task_pos()
    {
        $this->load->model('Task');
        $id = $this->input->post('id');
        $parent_id = $this->input->post('parent_id');
        $index = $this->input->post('index');

        $task_parent = $this->Task->get_task_item($parent_id);
        $tasks = $this->Task->get_project_by_parent_id($parent_id);
        if (!empty($tasks)) {
            $task = $tasks[0];
            $task_index = $tasks[$index];

            $this->Task->update_item_pos(array(
                'parent' => $task['parent'],
                'lft' => $task_index['lft'],
                'rgt' => $task_index['rgt'],
                'level' => $task_index['level']
            ), array('id' => $id));


            for ($i = $index; $i < count($tasks); $i++) {
                $task_update = $tasks[$i];
                if ($task_update['id'] == $id) continue;

                $this->Task->update_item_pos(array(
                    'lft' => $task_update['lft'] + 1,
                    'rgt' => $task_update['rgt'] + 1
                ), array('id' => $task_update['id']));
            }
        } else {
            $this->Task->update_item_pos(array(
                'parent' => $parent_id,
                'lft' => 0,
                'rgt' => 1,
                'level' => $task_parent['level'] + 1
            ), array('id' => $id));
        }
    }

    function search_projects()
    {
        $this->load->model('Task');
        $name = $this->input->get('term');
        $temp_projects = $this->Task->find_project_by_customer(array('name' => $name));
        foreach ($temp_projects as $pro) {
            $projects[] = array(
                'id' => $pro['project_id'],
                'label' => $pro['label'],
                'value' => $pro['value'],
                'task_id' => $pro['task_id'],
                'image' => base_url() . 'assets/img/item.png');
        }

        echo json_encode($projects);
    }


    #D13

    public function task_tranfer()
    {
        // var_dump($this->Task->get_list_task_tranfer());
        // die();
        // // var_dump($this->input->post());die();
        if (!empty($this->input->post('task_id'))) {
            $res = array();
            $data_tranfer = array();
            $data_task_tranfer = array();
            $data_tranfer['time'] = date('Y-m-d');
            $data_task_tranfer['task_id'] = intval($this->input->post('task_id'));
            if (!empty($this->input->post('implement_t'))) {
                $this->db->insert('phppos_tranfer', $data_tranfer);
                $data_task_tranfer['tranfer_id'] = $this->db->insert_id();
                $data_task_tranfer['user_id'] = intval($this->input->post('implement_t'));
                $this->Task->update_user_relation($data_task_tranfer['user_id'], $data_task_tranfer['task_id'], 'is_implement', 0);
                // echo $this->db->last_query();die();
                if (!empty($this->input->post('im'))) {
                    $im = $this->input->post('im');
                    $data_task_tranfer['type'] = 'implement';
                    foreach ($im as $value) {
                        $data_task_tranfer['user_id_tranfer'] = intval($value);
                        $this->db->insert('phppos_task_tranfer', $data_task_tranfer);
                        $this->Task->update_user_relation($value, $data_task_tranfer['task_id'], 'is_implement', 1);
                    }
                }
                $res['success'] = "Chuyển công việc thành công";
            }

            # Người tham gia
            if (!empty($this->input->post('join_t'))) {

                $this->db->insert('phppos_tranfer', $data_tranfer);
                $data_task_tranfer['tranfer_id'] = $this->db->insert_id();
                $data_task_tranfer['user_id'] = intval($this->input->post('join_t'));
                $this->Task->update_user_relation($data_task_tranfer['user_id'], $data_task_tranfer['task_id'], 'is_join', 0);
                // echo $this->db->last_query();die();
                if (!empty($this->input->post('jo'))) {
                    $jo = $this->input->post('jo');
                    $data_task_tranfer['type'] = 'join';
                    foreach ($jo as $value2) {
                        $data_task_tranfer['user_id_tranfer'] = intval($value2);
                        $this->db->insert('phppos_task_tranfer', $data_task_tranfer);
                        $this->Task->update_user_relation($value2, $data_task_tranfer['task_id'], 'is_join', 1);

                    }
                }
                $res['success'] = "Chuyển công việc thành công";
            }


            if (!empty($res)) {
                $data['implement'] = empty($im) ? null : $im;
                $data['join'] = empty($jo) ? null : $jo;
                // var_dump($data);die();
                // echo json_encode($data['implement']);die();
                $this->Task->task_notice_log($this->input->post('task_id'),$data);
               // var_dump($pp);die();
                echo json_encode($res);
            } else {
                echo json_encode(array('warn' => 'No input'));
            }


        }
    }

    /**
     *
     */
    function task_no_revenue()
    {
        // $this->check_action_permission('personal_task_view');
        $this->load->view('tasks/task_no_revenue', null);
    }


    public function noRevenueList()
    {
        $this->load->model('TaskPersonal');
        $this->_paginator['per_page'] = 20;
        $this->_data['arrParam']['paginator'] = $this->_paginator;
        // var_dump($this->_data['arrParam']);die();
        $post = $this->input->post();

        if (!empty($post)) {
            $config['base_url'] = base_url() . 'tasks/noRevenueList';
            $config['total_rows'] = $this->TaskPersonal->countItem($this->_data['arrParam']);

            $config['per_page'] = $this->_paginator['per_page'];
            $config['uri_segment'] = $this->_paginator['uri_segment'];
            $config['use_page_numbers'] = TRUE;

            $this->load->library("pagination");
            $this->pagination->initialize($config);
            $this->pagination->createConfig('front-end');

            $pagination = $this->pagination->create_ajax();

            $this->_data['arrParam']['start'] = $this->uri->segment(3);
            $this->_data['arrParam']['type'] = 2;
            $items = $this->TaskPersonal->listItem($this->_data['arrParam']);
            
            $result = array(
                'count' => $config['total_rows'],
                'items' => $items,
                'pagination' => $pagination
            );

            echo json_encode($result);
        }
    }

#bieu do du an
    function task_gantt_chart()
    {

        $task = $this->Task->getlisttask();
        foreach ($task as $key => $value) {
            $task[$key]['progress'] = $task[$key]['progress'] / 100;
            $task[$key]['start_date'] = date('d-m-Y', strtotime($task[$key]['start_date']));
            $task[$key]['date_end'] = date('d-m-Y', strtotime($task[$key]['date_end']));

        }

        $data['task'] = json_encode($task);
        $this->load->view('tasks/task_grantt_chart', $data);
    }


#Tạo dự án
    function addtask()
    {
        $arr = array_merge($this->input->post(), $this->input->get());
        echo $arr;
    }


# Cập nhật tỷ trọng
    function task_percent()
    {

        if ($this->input->post('parent_id')) {
            // var_dump($this->input->post());die();
            $parent = $this->input->post('parent_id');
            $child = $this->input->post('child_id');
            $percent = $this->input->post('workload_list');

            # Validate dữ liệu đầu vào
            # Phần trăm không được lớn hơn 100 và không đc nhỏ hơn 0
            $t = 0;
            foreach ($percent as $key => $value) {
                floatval($value);
                $t += $value;
                if ($value > 100 || $value < 0) {
                    echo json_encode(array('notice' => 'Tỷ lệ không được nhỏ hơn 0 và không được lớn hơn 100% ', 'flag' => 'error'));
                    die();
                }
            }

            # Tổng tỷ trọng phải bằng 100%
            if ($t != 100) {
                echo json_encode(array('notice' => 'Tổng tỷ trọng phải bằng 100%', 'flag' => 'error'));
                die();
            }

            # Lưu tỷ trọng
            foreach ($child as $key => $value) {
                $data = array('percent' => floatval($percent[$key]));
                $this->db->where(array('id' => $value, 'parent' => $parent));
                $this->db->update('phppos_tasks', $data);
            }
            // echo  $this->db->last_query();

            # Cập nhật phần trăm cho bố
            # Nếu tồn tại công việc con
            if (!empty($child[0])) {

                $this->update_parent_progress($child[0]);


                //  $item = $this->Task->getItem(array(
                // 'id' => $child[0]
                // ), array(
                //     'task' => 'public-info',
                //     'brand' => 'full'
                // ));


                //  # Chia % lại cho công việc cha
                // if(!empty($item['id'])){

                //         $item['key'] = 'pencil-square-o';
                //         $this->TaskProgress->solve($item, array(
                //             'task' => 'edit'
                //         ));
                // }

            }
            echo json_encode(array('notice' => 'Cập nhật tỷ trọng thành công', 'flag' => 'success'));
        }
    }


    # Hoàn thành công việc dự án
    function task_finish()
    {
        if (!empty($this->input->post('id'))) {

            $person_id =$this->Employee->get_logged_in_employee_info()->person_id;
            $data = array();
            $data['progress'] = 100;
            $data['trangthai'] = 2;
            $data['pheduyet'] = 1;
            $data['date_finish'] = date('Y-m-d');
            $id = $this->input->post('id');
            $task_info = $this->Task->getNodeInfo($id);
            if(empty($task_info)){
                echo json_encode(array('flag' => 'warning', 'notice' => 'Có lỗi với dữ liệu đầu vào'));
                return;
            }
            if ($task_info['level'] == 0) {
                $data['pheduyet'] = 2;
            }
            $this->db->where('id', $id);
            $this->db->update('phppos_tasks', $data);

            // echo $this->db->last_query();die();
            // $node = ($this->Task->getNodeInfo($id));
            $log['task_id'] = $id;
            $log['person_id'] =$person_id;
            $log['trangthai'] =2;
            $log['time'] = date("Y-m-d H:i:s");
            $log['progress'] = 100;
            $log['prioty'] = $task_info['prioty'];
            $this->db->insert('phppos_task_log', $log);
            $node = $this->Task->get_list_node($id);

            // echo "<pre>";
            // var_dump($node);die();
            // var_dump($data);
            if (is_array($node)) {
                foreach ($node as $key => $value) {
                    $this->db->where('id', $value);
                    $this->db->update('phppos_tasks', $data);

                }
            }
            $time = date("y-m-d H:i:s");
            if(!empty($node)){

                foreach ($node as $key => $value) {
                $object[$key]['task_id'] = $value;
                $object[$key]['person_id'] =$person_id;
                $object[$key]['trangthai'] =2;
                $object[$key]['progress'] =100;
                $object[$key]['time'] =$time;
                $object[$key]['prioty'] =$value['prioty'];

            }
            $this->db->insert_batch('phppos_task_log', $object);

            }
          
            // echo $this->db->last_query();die();

            # Cập nhật trạng thái cho cha
            $this->update_parent_progress($id);

            echo json_encode(array('flag' => 'success', 'notice' => 'Cập nhật thành công'));

        } else {
            echo json_encode(array('flag' => 'warning', 'notice' => 'Có lỗi với dữ liệu đầu vào'));
        }
    }


    function update_parent_progress($id)
    {
        $n = $this->Task->getNodeInfo($id);
        $this->db->select('t.parent,t.percent,t.progress');
        $this->db->where('parent', $n['parent']);
        $p = $this->db->get('phppos_tasks as t')->result_array();
        // echo "<pre>"; var_dump($p);die();
        if ($n['parent'] != 0) {
            $tt = 0;
            foreach ($p as $key => $value) {
                $tt += ($value['progress'] * $value['percent'] / 100);
            }

            $data['progress'] = 100;
            $data['trangthai'] = 2;
            $data['pheduyet'] = 1;
            #Nếu tt < 100%, trang thai chua hoan thanh
            round($tt, 2);
            // var_dump($vv);die();
            if ($tt == 0) {
                $data['progress'] = 0;
                $data['trangthai'] = 0;
                $data['pheduyet'] = 2;
            } elseif ($tt < 100) {
                $data['progress'] = $tt;
                $data['trangthai'] = 1;
                $data['pheduyet'] = 2;
            } elseif ($tt >= 100) {
                $data['trangthai'] = 2;
                $data['date_finish'] = date('Y-m-d');
            }
            // echo "<pre>";
            // var_dump($data);die();

            # Nếu cha là dự án thì chưa cho phê duyệt
            if ($n['level'] == 1) {
                $data['pheduyet'] = 2;
            }

            //  echo "<pre>";
            // var_dump($data);die();
            $this->db->where('id', $n['parent']);
            $this->db->update('phppos_tasks', $data);

            $this->update_parent_progress($n['parent']);
        } else {
            return;
        }


    }


    function pheduyet_task()
    {
        if ($this->input->post('id')) {
            $id = $this->input->post('id');
            if ($this->check_pheduyet($id)) {
                $user_pheduyet = $this->Employee->get_logged_in_employee_info()->id;
                $this->db->where('id', $id);
                $this->db->update('phppos_tasks', array('pheduyet' => 1, 'date_pheduyet' => date('Y-m-d'), 'user_pheduyet' => $user_pheduyet));
                echo json_encode(array('flag' => 'success', 'notice' => 'Cập nhật thành công'));
            } else {
                echo json_encode(array('flag' => 'warning', 'notice' => 'Bạn không có quyền phê duyệt dự án này!'));
            }

        }
    }


    function get_duration_template()
    {
        if ($this->input->post()) {
            // var_dump($this->input->post());die();
            $id = $this->input->post('id');
            $start = $this->input->post('start');
            $r = $this->TaskTemplate->get_duration_template($id);
            // echo $this->db->last_query();die();
            $d = 0;
            if (is_array($r)) {
                foreach ($r as $key => $value) {
                    $d += $value['duration'];
                }
            }

            $end = date("d-m-Y", strtotime("$start + $d day"));
            $e = date("Y-m-d", strtotime("$start + $d day"));
            echo json_encode(array('end' => $end, 'd' => $d, 'e' => $e));
        }
    }


    function pheduyet_nhanh()
    {
        if ($this->input->post('id')) {
            $id = $this->input->post('id');
            if ($this->check_pheduyet($id)) {
                $data['date_pheduyet'] = date("Y-m-d");
                $data['user_pheduyet'] = $this->Employee->get_logged_in_employee_info()->id;
                $data['pheduyet'] = 1;
                $this->db->where('id', $id);
                $this->db->update('phppos_tasks', $data);
                echo json_encode(array('flag' => 'true', 'notice' => "Phê duyệt thành công!"));
            } else {
                echo json_encode(array('flag' => 'warning', 'notice' => 'Bạn không có quyền phê duyệt dự án này!'));
            }
        }

    }

    function fast_approve_norevenue(){
          if ($this->input->post('id')) {
            $id = $this->input->post('id');

      $this->db->where('tp.id', $id);
      $this->db->where('tp.approved', $this->Employee->get_logged_in_employee_info()->id);
      $check = $this->db->get('phppos_tasks_personal as tp')->row_array();
            if ($check) {
                $data['date_pheduyet'] = date("Y-m-d");
                $data['user_pheduyet'] = $this->Employee->get_logged_in_employee_info()->id;
                $data['pheduyet'] = 1;
                $this->db->where('id', $id);
                $this->db->update('phppos_tasks_personal', $data);
                echo json_encode(array('flag' => 'true', 'notice' => "Phê duyệt thành công!"));
            } else {
                echo json_encode(array('flag' => 'warning', 'notice' => 'Bạn không có quyền phê duyệt dự án này!'));
            }
        }
    }



     function task_alert(){
        $data['list'] = $this->Employee->get_task_alert($this->Employee->get_logged_in_employee_info()->id);
        // echo $this->db->last_query();die();
        $this->load->view('employees/task_alert', $data);
    }


    function approve_notice_d13(){
         
           $data['result'] = $this->Task->get_list_task();
           // echo $this->db->last_query();
           $this->load->view('employees/list_approve',$data);
    }


    function task_finish_alert(){
        #1.Lấy sô dự án hoàn thành trong ngày
        $data['list']  = $this->Task->get_task_stage();
        #2.Lấy số dự án tham gia, phụ trách sẽ kết thúc trong ngày
        $this->load->view('tasks/task_contract_payment', $data);

    }


    function norevenue_approve(){
        $data['list'] = $this->TaskPersonal->get_list_norevenue();
        $this->load->view('tasks/list_revenue', $data);
    }


    #Danh sách thông báo công việc không tạo doanh thu
    function norevenue_notice(){
        $data['list']= $this->TaskPersonal->get_list_notice_no_revenue($option=null);
        $id = $this->Employee->get_logged_in_employee_info()->id;
        $li = array();
        foreach ($data['list'] as $key => $value) {
            $li[$key]['id'] = $value['id'];
            $li[$key]['seens'] = json_decode($value['seens']);
            $li[$key]['seens'][] = $id;
            $data['list'][$key]['implements'] = $this->convert_employee_from_json_to_username($value['implements']);
            $data['list'][$key]['joins'] = $this->convert_employee_from_json_to_username($value['joins']);
            $data['list'][$key]['seens'] = $this->convert_employee_from_json_to_username($value['seens']);
            $data['list'][$key]['approved'] = $this->Employee->get_employee_by_id($value['approved'])['username'];
           
        }
        // var_dump($li);die();
        if(!empty($li)){
            $this->TaskPersonal->update_notice_norevenue($li);
        }
       
       $this->load->view('tasks/norevenue_notice', $data);
    }

        function task_notice(){
        $data['list']= $this->Task->get_list_notice_task($option=null);
        $data['ht'] =1;
        $id = $this->Employee->get_logged_in_employee_info()->id;
        $li = array();
        foreach ($data['list'] as $key => $value) {
            $li[$key]['id'] = $value['id'];
            $li[$key]['seens'] = json_decode($value['seens']);
            $li[$key]['seens'][] = $id;
            $data['list'][$key]['implements'] = $this->convert_employee_from_json_to_username($value['implements']);
            $data['list'][$key]['joins'] = $this->convert_employee_from_json_to_username($value['joins']);
            $data['list'][$key]['seens'] = $this->convert_employee_from_json_to_username($value['seens']);
            $data['list'][$key]['approved'] = $this->convert_employee_from_json_to_username($value['approved']);
           
        }
        // var_dump($li);die();
        if(!empty($li)){
            $this->Task->update_notice($li);
        }
       
       $this->load->view('tasks/task_notice', $data);
    }

    function norevenue_log(){

        $data['list']= $this->TaskPersonal->get_list_notice_no_revenue($option='log');
        $id = $this->Employee->get_logged_in_employee_info()->id;
        foreach ($data['list'] as $key => $value) {
            $data['list'][$key]['implements'] = $this->convert_employee_from_json_to_username($value['implements']);
            $data['list'][$key]['joins'] = $this->convert_employee_from_json_to_username($value['joins']);
            $data['list'][$key]['seens'] = $this->convert_employee_from_json_to_username($value['seens']);
            $data['list'][$key]['approved'] = $this->Employee->get_employee_by_id($value['approved'])['username'];
           
        }
       
       $this->load->view('tasks/norevenue_notice', $data);
    }

        function notice_log(){

        $data['list']= $this->Task->get_list_notice_task($option='log');
        $id = $this->Employee->get_logged_in_employee_info()->id;
        foreach ($data['list'] as $key => $value) {
            $data['list'][$key]['implements'] = $this->convert_employee_from_json_to_username($value['implements']);
            $data['list'][$key]['joins'] = $this->convert_employee_from_json_to_username($value['joins']);
            $data['list'][$key]['seens'] = $this->convert_employee_from_json_to_username($value['seens']);
            $data['list'][$key]['approved'] = $this->convert_employee_from_json_to_username($value['approved']);
           
        }
       
       $this->load->view('tasks/task_notice', $data);
    }
# Chuyển một chỗi json id của employee thành username
    protected function convert_employee_from_json_to_username($list){
        $employees = $this->Employee->get_list_employees_by_location();
        $data = json_decode($list);
        if(empty($data))
            return "";
        foreach ($data as $key => $value) {
            foreach ($employees as $key2 => $value2) {
                if($data[$key] == $value2['id'])
                {
                    $data[$key] = $value2['username'];
                }
            }
        }
       $data = implode(', ', $data);
        return $data;
    }
}