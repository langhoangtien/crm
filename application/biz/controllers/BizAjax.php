<?php
require_once (APPPATH . "controllers/Secure_area.php");
class BizAjax extends Secure_area
{
    protected $_data;

    function __construct($module_id=null)
    {
        parent::__construct();
        $this->load->model('Sale');
        $this->load->model('Receiving');
        $this->load->library('sale_lib');
        $this->load->library('receiving_lib');
    }

    function select_vat() {
        $array = array('unpublished'=>'Không', 'published'=>'Có');
        echo json_encode($array);
    }

    function update_delivery_quantity() {
        $post = $this->input->post();
        if(!empty($post)) {
            echo json_encode($post);
        }
    }

    function check_location_exist() {
        $post = $this->input->post();
        if(!empty($post)) {
            $this->load->model('Location');
            $item = $this->Location->get_item($this->_data['arrParam'], array('task'=>'by-name'));

            echo json_encode(array('item'=>$item));
        }
    }

    function html_load_request_section() {
        $data = array_merge($this->input->post(), $this->input->get());
        $data['suppliers'] =array();
        if(!empty($data['contract_id']))
        {
            $suppliers = $this->Contract->get_supplier_by_contract($data['contract_id']);
            // var_dump($id);die();
            $data['suppliers'] = $suppliers;
        }
       
        $this->load->view('contracts/request_section', $data);
    }

    function test() {
        //$result = $this->Receiving->get_receiving_info(1);
        $result = $this->receiving_lib->get_receive(1, $options = null);
        echo '<pre>';
        print_r($result);
        echo '</pre>';
    }

    function test2() {
//        echo '<pre>';
//        print_r(get_defined_vars());
//        echo '</pre>';

        echo '<pre>';
        print_r($_SESSION);
        echo '</pre>';

    }

    function emp_list() {
        $post = $this->input->post();
        $arrParams = array_merge($post, $this->input->get());
        if(!empty($post)) {
            $this->load->model('Employee');
            $items = $this->Employee->get_items($arrParams);

            echo json_encode($items);
        }
    }

    function frm_commission_group() {
        $data = array();
        $this->load->model('Group');
        $data['slb_group'] = $this->Group->item_select_box();
        $this->load->view('ajax/form_commission_group', $data);
    }
}