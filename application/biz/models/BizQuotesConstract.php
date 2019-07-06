<?php
class BizQuotesConstract extends CI_Model
{
	public function getItem($id) {
		$this->db->select('*')
				->from('quotes_contract')
				->where('id_quotes_contract', $id);
			
		$query = $this->db->get();
		$result = $query->row_array();

		$this->db->flush_cache();

		return $result;
	}

    public function get_items($arrPrams = null, $options = null) {
        $this->db->select('*')
                ->from('quotes_contract')
                ->where('id_quotes_contract IN ('.implode(',', $arrPrams['cid']).')');

        $query = $this->db->get();
        $result_tmp = $query->result_array();
        $this->db->flush_cache();
        $result = array();
        if(!empty($result_tmp)) {
            foreach($result_tmp as $val)
                $result[$val['id_quotes_contract']] = $val;
        }

        return $result;
    }
}