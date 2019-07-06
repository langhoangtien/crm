<?php
// require_once (APPPATH.'/libraries/Stock_out_lib.php');

class BizStock_out {
	var $CI;
	
	function __construct()
	{
		$this->CI =& get_instance();
	}
	
	function setValue($key, $value)
	{
		$this->CI->session->set_userdata($key, $value);
	}
	
	function getValue($key)
	{
		return $this->CI->session->userdata($key);
	}
}
