<?php
require_once (APPPATH . "controllers/Secure_area.php");

class BizApprover_groups extends Secure_area
{
 
    function __construct()
    {
        parent::__construct('approver_groups');
        $this->load->library('MySession');
        $this->load->model('Employee');
        $this->load->model('ApproverGroup');
    }

    public function index()
    {
        $listViewData['cols'] = [
            [
                'field' => 'id',
                'label' => 'ID'
            ],
            [
                'field' => 'name',
                'label' => 'Tên nhóm'
            ],
            [
                'field' => 'code',
                'label' => 'Công đoạn'
            ],
            [
                'field' => 'employees',
                'label' => 'Nhân viên'
            ]
        ];
        $result = $this->ApproverGroup->getAll();
        $result = array_map(function ($row) {
            $employees = '';
            foreach ($row['employees'] as $employee) {
                $employees .= trim($employee['first_name'] . ' ' . $employee['last_name']) . ', ';
            }
            $row['employees'] = substr(trim($employees), 0, -1);
            // $row['id'] = '#' . $row['id'];
            return $row;
        }, $result);
        $listViewData['tblRows'] = $result;
        $data['listViewHtml'] = $this->load->view('approver_groups/partials/list_view', $listViewData, TRUE);
        $this->load->view('approver_groups/index', $data);
    }

    public function view()
    {
        $data = [];
        $availableEmployees = $this->Employee->get_all()->result_array();
        $data['availableEmployees'] = $availableEmployees;
        $data['approver_group_id'] = $groupId = $this->input->post('id');;
        $data['steps'] = $this->ApproverGroup->getStepsAvailable();
        $data['isCreate'] = empty($data['approver_group_id']);
        $data['groupInfo'] = $this->ApproverGroup->getInfo($groupId);
        
        $selectedEmployees = !empty($data['groupInfo']['employees']) ? $data['groupInfo']['employees'] : [];
        
        $data['availableEmployees'] = array_filter($data['availableEmployees'], function($item) use ($selectedEmployees){
            $selected = false;
            foreach ($selectedEmployees as $employee)
            {
                if ($employee['employee_id'] == $item['id'])
                {
                    $selected = true;
                    break;
                }
            }
            if (!$selected) {
                return $item;
            }
        });
        echo json_encode(array(
            'success' => true,
            'html' => $this->load->view('approver_groups/partials/approver_group_modal', $data, TRUE)
        ));
    }

    public function view_approve_statuses()
    {
        $objId = $this->input->post('obj_id');
        $stepCode = $this->input->post('step_code');
        $data = [];
        $data['obj_id'] = $objId;
        $data['step_code'] = $stepCode;
        $data['approve_statuses'] = $this->ApproverGroup->getApproveStatuses($stepCode, $objId);
        $employee = $this->Employee->get_logged_in_employee_info();
        foreach ($data['approve_statuses'] as &$record) {
            $record['allow_approve'] = 0;
            if ($employee->id == $record['employee_id']) {
                $record['allow_approve'] = $this->ApproverGroup->allowApprove($stepCode, $employee->id, $objId);
            }
        }
        echo json_encode(array(
            'success' => true,
            'html' => $this->load->view('approver_groups/partials/approve_statuses_modal', $data, TRUE)
        ));
    }

    public function save()
    {
        $response = [
            'success' => true
        ];
        $groupId = $this->input->post('id');
        $groupData = [
            'name' => $this->input->post('name'),
            'code' => $this->input->post('code'),
            'active' => $this->input->post('active')
        ];
        $groupId = $this->ApproverGroup->save($groupId, $groupData);
        $employeeIds = $this->input->post('employee_ids');
        $this->ApproverGroup->addEmployees($groupId, $employeeIds);
        echo json_encode($response);
    }
    
    public function approve()
    {
        $objId = $this->input->post('obj_id');
        $stepCode = $this->input->post('step_code');
        $commnet = $this->input->post('comment');
        $employee = $this->Employee->get_logged_in_employee_info();
        $this->ApproverGroup->doApprove($stepCode, $employee->id, $objId, $commnet);
        
        if ($this->ApproverGroup->isApproved($stepCode, $objId))
        {
            switch ($stepCode) {
                case 'bao_gia':
                    $this->load->model('Sale');
                    $this->Sale->doApproved($objId);
                    break;
                case 'chuyen_kho':
                    $this->load->library('receiving_lib');
                    $this->load->model('Receiving');
                    $this->load->model('Supplier');
                    
                    $this->receiving_lib->clear_all();
                    $this->receiving_lib->copy_entire_receiving($objId);
                    $recInfo = $this->Receiving->get_info($objId)->row_array();
                    $data['cart'] = $this->receiving_lib->get_cart();
                    $supplier_id = $recInfo['supplier_id'];
                    $location_to_id = $recInfo['transfer_to_location_id'];
                    $location_from_id = $recInfo['location_id'];
                    $employee_id = $recInfo['employee_id'];
                    $comment = $recInfo['comment'];
                    $payment_type = $recInfo['payment_type'];
                    
                    $recId = $this->Receiving->approvedTransfer(
                        $data['cart'],
                        $supplier_id,
                        $employee_id,
                        $comment,
                        $payment_type,
                        $objId,
                        $recInfo['receiving_time'],
                        0,
                        $location_from_id,
                        $location_to_id 
                        );
                    
                    if ($supplier_id != -1) {
                        $suppl_info = $this->Supplier->get_info($supplier_id);
                    }
                    
                    if ($recId > 0 && $this->receiving_lib->get_email_receipt() && !empty($suppl_info->email)) {
                        $this->load->library('email');
                        $config['mailtype'] = 'html';
                        $this->email->initialize($config);
                        $this->email->from($this->Location->get_info_for_key('email') ? $this->Location->get_info_for_key('email') : 'no-reply@mg.4biz.vn', $this->config->item('company'));
                        $this->email->to($suppl_info->email);
                    
                        $this->email->subject(lang('receivings_receipt'));
                        $this->email->message($this->load->view("receivings/receipt_email", $data, true));
                        $this->email->send();
                    }
                    $this->Receiving->doApproved($recId);
                    $this->receiving_lib->clear_all();
                    break;
            }
        }
        
        $response = array('success' => 1);
        echo json_encode($response);
    }
    
    public function disapprove()
    {
        $employee = $this->Employee->get_logged_in_employee_info();
        $objId = $this->input->post('obj_id');
        $stepCode = $this->input->post('step_code');
        $commnet = $this->input->post('comment');
        $this->ApproverGroup->doDisapprove($stepCode, $employee->id, $objId, $commnet);
        $response = array('success' => 1);
        echo json_encode($response);
    }
}
