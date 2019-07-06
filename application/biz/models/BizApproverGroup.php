<?php

class BizApproverGroup extends CI_Model
{

    protected $steps = [
        [
            'label' => 'Chuyển kho',
            'code' => 'chuyen_kho'
        ],
        [
            'label' => 'Báo giá',
            'code' => 'bao_gia'
        ],
        [
            'label' => 'Tính giá',
            'code' => 'tinh_gia'
        ]        
    ];
    
    public function getInfo($groupId = 0)
    {
        $this->db->from('approver_groups as ag');
        $this->db->where('ag.id', $groupId);
        $result = $this->db->get()->result_array();
        $group =  !empty($result) ? $result[0] : [];
        
        if (!empty($group)) {
            $this->db->select("age.*, e.id as employee_id, p.first_name, p.last_name");
            $this->db->from('approver_groups_employees as age');
            $this->db->where('age.group_id', $group['id']);
            $this->db->join('employees as e', 'e.id = age.employee_id');
            $this->db->join('people AS p', 'e.person_id = p.person_id', 'left');
            $this->db->order_by('age.order ASC');
            $group['employees'] = $result = $this->db->get()->result_array();
        }
        return $group;
    }
    
    public function getStepLabel($stepCode = '')
    {
        foreach ($this->steps as $step) {
            if ($step['code'] == $stepCode) {
                return $step['label'];
            }
        }
        return '';
    }
    
    public function getAllApproveRequests($employeeId = 0)
    {
        $this->db->from('approver_groups_employees_statuses as ages');
        $this->db->where('ages.employee_id', $employeeId);
        // $this->db->where('ages.approved', 0);
        return $this->db->get()->result_array();
    }
    
    public function getCountApproveNotices($employeeId = 0)
    {
        $this->db->from('approver_groups_employees_statuses as ages');
        $this->db->where('ages.employee_id', $employeeId);
        $this->db->where('ages.approved', 0);
        return $this->db->count_all_results();
    }
    
    public function initApproverStatus($stepCode = '', $objectId = 0)
    {
        $this->db->from('approver_groups_employees as age');
        $this->db->join('approver_groups as ag', 'ag.id = age.group_id');
        $this->db->where('ag.code', $stepCode);
        $result = $this->db->get()->result_array();
        if (empty($result)) return;
        foreach ($result as $record) {
            $approverGroupData = [
                'employee_id' => $record['employee_id'],
                'group_id' => $record['group_id'],
                'step_code' => $stepCode,
                'object_id' => $objectId,
                'order' => $record['order'],
                'approved' => 0,
                
            ];
            $this->db->insert('approver_groups_employees_statuses', $approverGroupData);
        }
    }
    
    public function allowApprove($stepCode = '', $employeeId = 0, $objId = 0)
    {
        $this->db->select("ages.*");
        $this->db->from('approver_groups_employees_statuses as ages');
        $this->db->where('ages.object_id', $objId);
        $this->db->where('ages.step_code', $stepCode);
        $this->db->where('ages.approved !=', 1);
        $this->db->order_by('ages.order ASC');
        $result = $this->db->get()->result_array();
        if (empty($result))
        {
            return false;
        }
        $firtRow = reset($result);
        return ($firtRow['employee_id'] == $employeeId);
    }

    public function getApproveStatuses($stepCode = '', $objId = 0)
    {
        $this->db->select("ages.*, e.id as employee_id, p.first_name, p.last_name");
        $this->db->from('approver_groups_employees_statuses as ages');
        $this->db->join('employees as e', 'e.id = ages.employee_id');
        $this->db->join('people AS p', 'e.person_id = p.person_id', 'left');
        $this->db->where('ages.object_id', $objId);
        $this->db->where('ages.step_code', $stepCode);
        return $this->db->get()->result_array();
    }
    
    public function isApproved($stepCode = '', $objId = '')
    {
        $this->db->from('approver_groups_employees_statuses as ages');
        $this->db->where('ages.object_id', $objId);
        $this->db->where('ages.step_code', $stepCode);
        $this->db->where('ages.approved !=', 1);
        return !(int) $this->db->count_all_results();
    }
    public function doDisapprove($stepCode = '', $employeeId = '', $objectId = '', $comment = '')
    {
        $this->db->where('employee_id', $employeeId);
        $this->db->where('step_code', $stepCode);
        $this->db->where('object_id', $objectId);
        return $this->db->update('approver_groups_employees_statuses', ['approved' => -1, 'comment' => $comment]);
    }
    public function doApprove($stepCode = '', $employeeId = '', $objectId = '', $comment = '')
    {
        $this->db->where('employee_id', $employeeId);
        $this->db->where('step_code', $stepCode);
        $this->db->where('object_id', $objectId);
        return $this->db->update('approver_groups_employees_statuses', ['approved' => 1, 'comment' => $comment]);
    }
    
    public function getStepsAvailable()
    {
        return $this->steps;
    }

    function save($groupId = 0, $groupData = [])
    {
        if (empty($groupId) && !empty($groupData)) {
            $this->db->delete('approver_groups', array(
                'code' => $groupData['code']
            ));
            if ($this->db->insert('approver_groups', $groupData)) {
                return $this->db->insert_id();
            }
        } else {
            $this->db->where('id', $groupId);
            if ($this->db->update('approver_groups', $groupData)) {
                return $groupId;
            }
        }
        return FALSE;
    }

    function delete($groupId)
    {
        $this->db->delete('approver_groups', array(
            'id' => $groupId
        ));
        $this->db->delete('approver_groups_employees', array(
            'group_id' => $groupId
        ));
        return $groupId;
    }

    public function addEmployees($groupId = 0, $employeeIds = [])
    {
        $this->db->delete('approver_groups_employees', array(
            'group_id' => $groupId
        ));
        foreach ($employeeIds as $i => $employeeId) {
            $this->db->insert('approver_groups_employees', [
                'group_id' => $groupId,
                'employee_id' => $employeeId,
                'order' => ($i + 1)
            ]);
        }
        return $groupId;
    }

    function getAll($limit = 10000, $offset = 0, $col = 'name', $order = 'asc')
    {
        $this->db->select("ag.id, ag.code, ag.name, e.id as employee_id, p.first_name, p.last_name");
        $this->db->from('approver_groups as ag');
        $this->db->join('approver_groups_employees as age', 'ag.id = age.group_id');
        $this->db->join('employees as e', 'e.id = age.employee_id');
        $this->db->join('people AS p', 'e.person_id = p.person_id', 'left');
        $this->db->where('e.deleted', 0);
        $this->db->limit($limit);
        $this->db->offset($offset);
        $result = [];
        foreach ($this->db->get()->result_array() as $record) {
            $employeeInfo = [
                'employee_id' => $record['employee_id'],
                'first_name' => $record['first_name'],
                'last_name' => $record['last_name']
            ];
            if (empty($result[$record['id']])) {
                $result[$record['id']] = [
                    'id' => $record['id'],
                    'name' => $record['name'],
                    'code' => $record['code'],
                    'employees' => [
                        $employeeInfo
                    ]
                ];
            } else {
                array_push($result[$record['id']]['employees'], $employeeInfo);
            }
        }
        
        return $result;
    }
    
    public function lastestDisapproved($group_id = 2, $obj_id)
    {
        $this->db->select("COUNT(employee_id) as tEmployee");
        $this->db->from('approver_groups_employees');
        $this->db->where('group_id', $group_id);
        $totalEmployeeInGroup =  $this->db->get()->row_array()['tEmployee'];
        
        $this->db->select("approved");
        $this->db->from('approver_groups_employees_statuses');
        $this->db->where('group_id', $group_id);
        $this->db->where('object_id', $obj_id);
        $this->db->order_by('id', 'desc');
        $this->db->limit($totalEmployeeInGroup);
        
        $results = $this->db->get()->result_array();
        $lDisapproved = false;
        foreach ($results as $result) {
            if ($result['approved'] == -1)
            {
                $lDisapproved = true;
                break;
            }
        }
        return $lDisapproved;
    }
    
    public function deleteApproverEmployeeStatus($obj_id, $stepCode)
    {
        $this->db->where('step_code', $stepCode);
        $this->db->where('object_id', $obj_id);
        return $this->db->delete("approver_groups_employees_statuses");
    }
}
