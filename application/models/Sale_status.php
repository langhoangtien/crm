<?php 
class Sale_status extends CI_Model {
    public function __constructor() {
        parent::__constructor();
    }
    
    public function get_all($type = 0) {
        $this->db->from('sale_status');
        if ($type > 0) {
        	$this->db->where('status_type', $type);
        }
        $this->db->order_by('status_name', 'asc');
        $query = $this->db->get();
        return $query->result();
    }
}