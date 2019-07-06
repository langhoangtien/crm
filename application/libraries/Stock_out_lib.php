<?php
class Stock_out_lib
{
    var $CI;

    //This is used when we need to change the sale state and restore it before changing it (The case of showing a receipt in the middle of a sale)
    var $sale_state;
    function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('Register_cart');
        $this->CI->load->model('Item');
        $this->CI->load->model('Location');
        $this->CI->load->model('Employee');
        $this->CI->load->model('Group');
        $this->CI->load->model('Service');
        $this->CI->load->model('Measure');
        $this->CI->load->model('Expense');

        $this->sale_state = array();
   }
}
?>