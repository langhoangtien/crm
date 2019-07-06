<?php
class Shift_expenses extends CI_Model {
    protected $_fields = array(
        'id' 	                => 'e.id',
        'expense_amount' 	    => 'e.expense_amount',
        'expense_description'   => 'e.expense_description',
        'expense_date' 	        => 'e.expense_date',
        'employee_id' 	        => 'e.employee_id',
    );

    function sum_shift($arrParams) {
        $shift_category_id = $this->config->item('shift_category_id');
        $location_ids = $arrParams['location_ids'];

        $this->db -> select('SUM(e.expense_amount) AS sum_shift')
                 -> from('expenses AS e')
                 -> where('e.location_id IN ('.$location_ids.')')
                 -> where('e.category_id', $shift_category_id)
                 -> where('e.deleted', 0);

        if(!empty($arrParams['start_date'])) {
            $this->db->where('e.expense_date >= \''.$arrParams['start_date'].'\'');
        }

        if(!empty($arrParams['end_date'])) {
            $this->db->where('e.expense_date <= \''.$arrParams['end_date'].'\'');
        }

        $query = $this->db->get();
        $result = $query->row()->sum_shift;
        if(empty($result))
            $result = 0;

        $this->db->flush_cache();

        return $result;
    }

    function count_item($arrParams) {
        $shift_category_id = $this->config->item('shift_category_id');
        $location_ids = $arrParams['location_ids'];

        $this->db -> select('COUNT(e.id) AS totalItem')
                  -> from('expenses AS e')
                  -> where('e.location_id IN ('.$location_ids.')')
                  -> where('e.category_id', $shift_category_id)
                  -> where('e.deleted', 0);

        if(!empty($arrParams['start_date'])) {
            $this->db->where('e.expense_date >= \''.$arrParams['start_date'].'\'');
        }

        if(!empty($arrParams['end_date'])) {
            $this->db->where('e.expense_date <= \''.$arrParams['end_date'].'\'');
        }

        $query = $this->db->get();
        $result = $query->row()->totalItem;
        $this->db->flush_cache();

        return $result;
    }

    function list_item($arrParams = null, $options = null) {
        $paginator = $arrParams['paginator'];
        $shift_category_id = $this->config->item('shift_category_id');
        $this->db -> select("e.id, e.expense_amount, e.expense_description, e.employee_id")
                  -> select("DATE_FORMAT(e.expense_date, '%d/%m/%Y %H:%i:%s') AS expense_date", FALSE)
                  -> from('expenses AS e')
                  -> where('category_id', $shift_category_id)
                  -> where('e.deleted', 0);

        if(!empty($arrParams['start_date'])) {
            $this->db->where('e.expense_date >= \''.$arrParams['start_date'].'\'');
        }

        if(!empty($arrParams['end_date'])) {
            $this->db->where('e.expense_date <= \''.$arrParams['end_date'].'\'');
        }

        if(!empty($arrParams['col']) && !empty($arrParams['order'])){
            $col   = $this->_fields[$arrParams['col']];
            $order = $arrParams['order'];

            $this->db->order_by($col, $order);
        }else {
            $this->db->order_by('e.id', 'DESC');
        }

        if($options['task'] != 'report') {
            $page = $arrParams['page'];
            $this->db->limit($paginator['per_page'],($page - 1)*$paginator['per_page']);
        }

        $query = $this->db->get();
        $result = $query->result_array();
        $this->db->flush_cache();
        if(!empty($result)) {
            foreach($result as $val)
                $emp_ids[] = $val['employee_id'];

            $emp_ids = array_unique($emp_ids);
            $this->db-> select("e.id, p.first_name, p.last_name")
                    -> from('employees AS e')
                    -> join('people as p', 'e.person_id = p.person_id')
                    -> where('e.id IN ('.implode(',', $emp_ids).')');

            $query = $this->db->get();
            $employee_list = $query->result_array();

            $this->db->flush_cache();
            foreach($employee_list as $v) {
                $employee_assoc[$v['id']] = $v['first_name'] . ' ' . $v['last_name'];
            }

            foreach($result as &$value) {
                $value['fullname'] = $employee_assoc[$val['employee_id']];
                $value['expense_amount'] = number_format($value['expense_amount'],'0','.',',') . ' VND';
            }
        }

        return $result;
    }
}

?>