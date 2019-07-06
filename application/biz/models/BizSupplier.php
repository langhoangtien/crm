<?php
require_once (APPPATH . "models/Supplier.php");
class BizSupplier extends Supplier
{
    function get_info_by_id($id){
        $this->db->select("*")
                ->from('suppliers')
                ->where('id', $id);

        $query = $this->db->get();

        $result = $query->row_array();

        return $result;
    }
    function get_info_by_person_id($id){
        $this->db->select("*")
                ->from('suppliers')
                ->where('person_id', $id);

        $query = $this->db->get();

        $result = $query->row_array();

        return $result;
    }
    function get_payment_amount_from_recv($arrParams = null, $options = null) {
        $this->db->select("SUM(transaction_amount) as sum_transaction_amount")
                ->from('receivings_transactions')
                ->where('recv_id', $arrParams['recv_id']);

        if(!empty($arrParams['payment_type'])) {
            $payment_type = $arrParams['payment_type'];
            $this->db->where("payment_type = '$payment_type'");
        }

        $query = $this->db->get();

        $result_tmp = $query->row_array();
        if(!empty($result_tmp['sum_transaction_amount']))
            $result = $result_tmp['sum_transaction_amount'];
        else
            $result = 0;

        return $result;
    }

    function sum_balance($options) {
        $this->db->select("SUM(s.$options) AS balance_total")
                ->from('suppliers AS s')
                ->where('s.deleted', 0);

        $query = $this->db->get();

        $result_tmp = $query->row_array();
        $this->db->flush_cache();

        $result = (!empty($result_tmp['balance_total'])) ? $result_tmp['balance_total'] : 0;

        return $result;
    }


    #D13


        function get_all($limit="", $offset="",$order='person_id',$order_by='asc',$unit_type_id="",$search="")
    {

        $availableLocations = $this->Location->getLocationsWithChild();
        $availableLocationIds = array_map(function($location){
            return $location['location_id'];
        }, $availableLocations);
        $availableLocationIds[] = 0;
        $location_id="";


        if ($this->session->userdata('employee_current_location_id')!==NULL)
        {
            $location_id = " WHERE location_id =".$this->session->userdata('employee_current_location_id');
        }


        $this->db->query('DROP TABLE IF EXISTS phppos_total_received');
        $this->db->query('DROP TABLE IF EXISTS  phppos_info_suppliers');
        $this->db->query('CREATE TEMPORARY TABLE phppos_total_received 
            SELECT phppos_receivings.supplier_id, SUM(phppos_receivings_items.item_unit_price * phppos_receivings_items.quantity_received) as total 
            FROM phppos_receivings RIGHT JOIN phppos_receivings_items 
            ON phppos_receivings.receiving_id = phppos_receivings_items.receiving_id'. $location_id.' GROUP BY supplier_id');
        $this->db->query("CREATE TEMPORARY TABLE phppos_info_suppliers
            SELECT phppos_suppliers_head.name_head AS head, phppos_people.person_id,phppos_unit_type.name AS name, phppos_suppliers.company_name,phppos_people.email,phppos_people.phone_number,phppos_suppliers.unit_type_id 
            FROM phppos_people JOIN phppos_suppliers 
            ON phppos_people.person_id = phppos_suppliers.person_id LEFT JOIN phppos_unit_type 
            ON phppos_suppliers.unit_type_id= phppos_unit_type.id LEFT JOIN phppos_suppliers_head
            ON phppos_suppliers.person_id = phppos_suppliers_head.supplier_id 
            WHERE phppos_suppliers."."created_at_location IN (". implode(',', $availableLocationIds) .") AND deleted =0 GROUP BY person_id ");
        $this->db->select();
        $this->db->from('phppos_info_suppliers');
        $this->db->join('phppos_total_received', 'phppos_total_received.supplier_id = phppos_info_suppliers.person_id', 'left');
        if($unit_type_id !=="")
        {
        $this->db->where('unit_type_id', $unit_type_id);
        }
        if($search !=="")
        {
            $this->db->like('company_name', $search, 'BOTH');
            $this->db->or_like('email', $search, 'BOTH');
        }
        $this->db->order_by($order, $order_by);
        if($limit!=="" && $offset !=="")
        {
        $this->db->limit($limit,$offset);
        }
        return $this->db->get();
        
    }



    function save_unit_type($id,$data)
    {

        $this->db->where('id', $id);
        $this->db->update('phppos_unit_type', $data);
      
      
    }
     function del_unit_type($id)
    {

        $this->db->where('id',$id);
        $this->db->delete('phppos_unit_type');
      
    }
    function add_unit_type($name)
    {

        
        $this->db->insert('phppos_unit_type',array('name'=>$name));
      
    }



    function goi_y_ben_thu_ba($search,$limit=25){
        $lo = $this->Employee->get_logged_in_employee_current_location_id();    
        $this->db->select('p.person_id as value,p.email as subtitle,p.image_id as avatar,p.email,s.company_name as label');
        $this->db->from('phppos_suppliers as s');
        $this->db->join('phppos_people as p', 'p.person_id =s.person_id ');
        $this->db->where('s.created_at_location', $lo);
        $this->db->group_start();
        $this->db->like('s.company_name', $search, 'BOTH');
        $this->db->or_like('p.email', $search, 'BOTH');
        $this->db->group_end();
        $this->db->group_by('p.person_id');
        $this->db->limit($limit);
        $ss =  $this->db->get()->result_array();
        foreach ($ss as $key => &$value) {
            $value['avatar'] = ($value['avatar'])? (base_url('app_files/view/'.$value['avatar'])) : (base_url('/assets/img/user.png'));
        }
        return $ss;
    }
}