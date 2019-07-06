<?php
class Ratings extends CI_Model
{
	public function __construct()
    {

    }
	function save($data)
	{	
		
		if($this->check_data($data['year'],$data['year_sub'],$data['point'],$data['profit'])){
			$this->db->where('year',$data['year']);
			$this->db->where('year_sub',$data['year_sub']);
			$this->db->where('point',$data['point']);
			$this->db->where('profit',$data['profit']);
			$this->db->update('room_rate',$data);
		}
		else
		$this->db->insert('room_rate', $data);
		
	}
	function check_data($year,$year_sub,$point,$profit){
		
       	$this->db->where('year',$year);
		$this->db->where('year_sub',$year_sub);
		$this->db->where('point',$point);
		$this->db->where('profit',$profit);
        $this->db->select('id');
        $this->db->from('room_rate');
      	return $this->db->count_all_results();
	}

	function view_data($year,$year_sub,$point,$profit){
		
       	$this->db->where('year',$year);
		$this->db->where('year_sub',$year_sub);
		$this->db->where('point',$point);
		$this->db->where('profit',$profit);
        $this->db->select('data_room_rate');
        $this->db->from('room_rate');
        $query = $this->db->get();
        $result = $query->row_array();
      	return $result;
	}
	function save_rate_kpi($data,$id=''){
		if($id > 0){
			$this->db->where('id',$id);
			$this->db->update('rate_kpi',$data);
		}
		else{

			$this->db->insert('rate_kpi', $data);
		}
	}
	function delete_rate_kpi($id){
		if($id > 0){
			$this->db->where('id',$id);
			$this->db->delete('rate_kpi');
		}
		
	}
	function view_rate_kpi(){
		$this->db->where('status',1);
		$this->db->select('data,id');
        $this->db->from('rate_kpi');
        $query = $this->db->get();
        $result = $query->result_array();
      	return $result;
	}

	function get_kpi($year){
		$row = $this->db->select('*')
            ->from('kpi')
            ->where('kpi_type = "profit"')
            ->where('type = 0')
            ->where('year = ' . $year)
            ->get()->result();
        $data = array();
        foreach ($row as $key)
        {
        	$data_kpi = json_decode($key->data_kpi,true);
        	for ($i=0; $i <4 ; $i++) { 
        		$data[$key->location_id][$i+1] = $data_kpi[$i];
        	}
        }
        return $data;
	}
}
?>
