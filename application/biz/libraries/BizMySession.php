<?php

class BizMySession {
	var $CI;
	const STOCK_OUT_SESSION_KEY = 'STOCK_OUT_DETAIL';
	const STOCK_IN_SESSION_KEY = 'STOCK_IN_DETAIL';
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

	function unsetValue($key)
	{
		unset($_SESSION['STOCK_OUT_DETAIL'][$key]);
		unset($_SESSION['STOCK_IN_DETAIL'][$key]);

	}

	public function clear_stock() {
        $this->CI->session->unset_userdata(self::STOCK_OUT_SESSION_KEY);
        $this->CI->session->unset_userdata(self::STOCK_IN_SESSION_KEY);
    }

}
