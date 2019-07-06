<?php
class BizService extends CI_Model{
    protected $_table 			         = 'services';
    protected $_fields 		             = array();

    public function __construct(){
        $this->_fields 	 =  array(
            'id' 	 	    => 's.id',
            'name' 	 	    => 's.name',
            'code' 	 	    => 's.code',
        );
    }

    function count_item($arrParams = null, $options = null) {
        if($options == null) {
            $key_filter = 'services_filter';

            $this->db -> select('COUNT(s.id) AS totalItem')
                      -> from($this->_table . ' AS s')
                      ->where('s.deleted', 0);

            if(!empty($arrParams['keywords'])) {
                $keywords = trim($arrParams['keywords']);
                $this->db->where('(s.name LIKE \'%'.$keywords.'%\' OR s.code LIKE \'%'.$keywords.'%\')');

                $_SESSION[$key_filter]['keywords'] = $keywords;
            }

            $query = $this->db->get();

            $result = $query->row()->totalItem;

            $this->db->flush_cache();
        }

        return $result;
    }
    function get_item_service($id){
        $this->db->select('*');
        $this->db->from($this->_table);
        $this->db->where('id',$id);
        $query = $this->db->get();
        return $query->row_array();
    }
    function get_document($code=null,$name=null,$id=null){
        $this->db->select('document');
        $this->db->from($this->_table);
        if ($name !=null) {
            $this->db->where('name',$name);
        }elseif ($code !=null){
            $this->db->where('code',$code);
        }
        $query = $this->db->get();
        return $query->row_array();
    }

    function list_item($arrParams = null, $options = null) {
        if($options['task'] == null) {
            $key_filter = 'services_filter';
            $paginator = $arrParams['paginator'];
            $this->db->select("s.id, s.code, s.name, s.min_profit")
                    ->from($this->_table . ' AS s')
                    ->where('s.deleted', 0);

            if(!empty($arrParams['keywords'])) {
                $keywords = trim($arrParams['keywords']);
                $this->db->where('(s.name LIKE \'%'.$keywords.'%\' OR s.code LIKE \'%'.$keywords.'%\')');

                $_SESSION[$key_filter]['keywords'] = $keywords;
            }

            if(!empty($arrParams['col']) && !empty($arrParams['order'])){
                $col   = $this->_fields[$arrParams['col']];
                $order = $arrParams['order'];

                $this->db->order_by($col, $order);

                $_SESSION[$key_filter]['col']  = $arrParams['col'];
                $_SESSION[$key_filter]['order'] = $arrParams['order'];
            }

            $page = $arrParams['page'];
            $this->db->limit($paginator['per_page'],($page - 1)*$paginator['per_page']);

            $query = $this->db->get();

            $result = $query->result_array();

            $this->db->flush_cache();

        }elseif($options['task'] == 'all') {
            $this->db->select("s.id, s.code, s.name, s.min_profit")
                     ->from($this->_table . ' AS s')
                     ->where('s.deleted', 0);

            if(!empty($arrParams['or_ids'])) {
                $this->db->or_where('s.id IN ('.implode(',', $arrParams['or_ids']).')');
            }

            $query = $this->db->get();

            $result_tmp = $query->result_array();

            $this->db->flush_cache();
            $result = array();
            if(!empty($result_tmp)) {
                foreach($result_tmp as $val)
                    $result[$val['id']] = $val;
            }

        }
        return $result;
    }

    function get_item($arrParams = null, $options = null) {
        global $biz_cached;
        if(!isset($biz_cached['service']['detail_'.$arrParams['id']])) {
            $this->db->select("s.*")
                    ->from($this->_table.' AS s')
                    ->where('s.id', $arrParams['id']);

            if(!isset($arrParams['all']))
                $this->db->where('s.deleted', 0);

            $query = $this->db->get();

            $result = $query->row_array();
            if(!empty($result['document'])) {
                $quote_contract_model = $this->model_load_model('QuotesConstract');
                $cid = explode(',', $result['document']);

                $result['document_list'] = $quote_contract_model->get_items(array('cid'=>$cid));
            }

            $this->db->flush_cache();
        }else {
            $result = $biz_cached['service']['detail_'.$arrParams['id']];
        }

        return $result;
    }

    function save_item($arrParams = null, $options = null) {
        if($options['task'] == 'update') {
            if(isset($arrParams['document']))
                $document = implode(',', $arrParams['document']);

            $data['name']                           = $arrParams['name'];
            $data['code']                           = $arrParams['code'];
            $data['description']                    = $arrParams['description'];
            $data['override_profit_commission']     = $arrParams['override_profit_commission'];
            $data['min_profit']                     = (float)$arrParams['min_profit'];
            $data['min_profit_commission']          = (float)$arrParams['min_profit_commission'];
            $data['document']                       = $document;

            if($arrParams['id'] == -1){
                $this->db->insert($this->_table,$data);
                $this->db->flush_cache();
            }else {
                $this->db->where("id",$arrParams['id']);
                $this->db->update($this->_table,$data);

                $this->db->flush_cache();
            }
        }
    }

    function delete_items($cid, $options = null) {
        $this->db->where('id IN ('.implode(',', $cid).')');
        // $this->db->update($this->_table,array('deleted'=>1));
        $this->db->delete($this->_table);
        $this->db->flush_cache();
    }

    function model_load_model($model_name)
    {
        $CI =& get_instance();
        $CI->load->model($model_name);
        return $CI->$model_name;
    }
}