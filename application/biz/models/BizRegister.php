<?php
require_once (APPPATH . "models/Register.php");
class BizRegister extends Register{
    protected $_current_location_id = null;
    public function __construct(){
        $employee_model = $this->model_load_model('Employee');
        $this->_current_location_id = $employee_model->get_logged_in_employee_current_location_id();
    }

    function get_items_from_current_location($arrParams = null, $options = null) {
        $this->db->select("r.register_id, l.name, l.address")
                ->from('registers AS r')
                ->join('location AS l', 'r.location_id = l.location_id', 'left')
                ->where('r.location_id', $this->_current_location_id);

        if($arrParams['include_deleted'] == true)
            $this->db->where('r.deleted IN (0,1)');
        else
            $this->db->where('r.deleted', 0);

        $query = $this->db->get();

        $result = $this->fetch_assoc($query, 'location_id');

        return $result;
    }

    function get_register_log_from_current_location($arrParams = null, $options = null) {
        $date = $arrParams['date'];

        $this->db->select("l.*, lc.name, lc.address")
                 ->select("DATE_FORMAT(l.shift_start, '%d-%m-%Y %H:%i') as shift_start_format", FALSE)
                 ->select("DATE_FORMAT(l.shift_end, '%d-%m-%Y %H:%i') as shift_end_format", FALSE)
                 ->from('register_log AS l')
                 ->join('registers AS r', 'l.register_id = r.register_id', 'left')
                 ->join('locations AS lc', 'r.location_id = lc.location_id', 'left')
                 ->where('r.location_id', $this->_current_location_id)
                 ->where("DATE(l.shift_end) = '$date'")
                 ->where('l.deleted', 0);

        $query = $this->db->get();
        $result = $query->result_array();
        $this->db->flush_cache();

        return $result;
    }

    protected function fetch_assoc($query, $primary_key = 'id')
    {
        $result = array();
        $result_tmp = $query->result_array();
        $this->db->flush_cache();

        if(!empty($result_tmp)) {
            foreach($result_tmp as $val)
                $result[$primary_key] = $val;
        }

        return $result;
    }

    function model_load_model($model_name)
    {
        $CI =& get_instance();
        $CI->load->model($model_name);
        return $CI->$model_name;
    }
}