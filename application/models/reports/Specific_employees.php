<?php
require_once ("Report.php");
class Specific_employees extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function get_commission_from_specific_employee_by_sale($arrParams = null, $options = null) {
        $this->db -> select('SUM(commission) AS sum_commission, c.sale_id')
                  -> from('sales_commission AS c')
                  -> join('sales AS s', 'c.sale_id = s.sale_id')
                  -> where('s.commission_status', 1)
                  -> where('s.deleted', 0);

        if(!empty($arrParams['location_ids'])) {

            $this->db->where('s.location_id IN ('.$arrParams['location_ids'].')');
        }


        if(!empty($arrParams['group_ids'])) {
            
            $this->db->where('s.sale_id IN ((
                                SELECT sale_id FROM '.$this->db->dbprefix('sales_employees').' WHERE group_id IN ('.$arrParams['group_ids'].')
                                ))');
        }
        if(!empty($arrParams['group_ids'])) {
            
            $this->db->where('group_id IN ('.$arrParams['group_ids'].')');
        }
        if(!empty($arrParams['start_date'])) {
            $this->db->where('s.sale_time >= \''.$arrParams['start_date'].'\'');
        }

        if(!empty($arrParams['end_date'])) {
            $this->db->where('s.sale_time <= \''.$arrParams['end_date'].'\'');
        }

        $this->db->where('c.employee_id', $arrParams['employee_id'])
                 ->group_by('c.sale_id');

        $query = $this->db->get();

        $result_tmp = $query->result_array();

        $result = array();
        if(!empty($result_tmp)) {
            foreach($result_tmp as $val) {
                $sale_id          = $val['sale_id'];
                $result[$sale_id] = $val['sum_commission'];
            }
        }

        $this->db->flush_cache();

        return $result;
    }

    function get_commission_from_specific_employee($arrParams = null, $options = null) {
        $this->db -> select('SUM(commission) AS sum_commission')
                  -> from('sales_commission AS c')
                  -> join('sales AS s', 'c.sale_id = s.sale_id')
                  -> where('s.commission_status', 1)
                  -> where('s.deleted', 0);

        if(!empty($arrParams['location_ids'])) {
            $this->db->where('s.location_id IN ('.$arrParams['location_ids'].')');
        }

        if(!empty($arrParams['group_ids'])) {
            $this->db->where('s.sale_id IN ((
                                SELECT sale_id FROM '.$this->db->dbprefix('sales_employees').' WHERE group_id IN ('.$arrParams['group_ids'].')
                                ))');
        }
        if(!empty($arrParams['group_ids'])) {
            
            $this->db->where('group_id IN ('.$arrParams['group_ids'].')');
        }
        if(!empty($arrParams['start_date'])) {
            $this->db->where('s.sale_time >= \''.$arrParams['start_date'].'\'');
        }

        if(!empty($arrParams['end_date'])) {
            $this->db->where('s.sale_time <= \''.$arrParams['end_date'].'\'');
        }

        $this->db->where('c.employee_id', $arrParams['employee_id']);

        $query = $this->db->get();

        $result_tmp = $query->row_array();

        $this->db->flush_cache();

        if(!empty($result_tmp)) {
            $result = $result_tmp['sum_commission'];
        }else {
            $result = 0;
        }

        return $result;
    }

}