<?php
require_once (APPPATH . "controllers/Locations.php");

class BizLocations extends Locations
{
	function __construct()
	{
		parent::__construct();
	}

	function save($location_id=-1)
	{
		$countAll = $this->Location->count_all();
		if ($countAll < MAX_LOCATION) {
			parent::save($location_id);
		} else {
			echo json_encode(array('success'=>false,'message'=>'Gói dịch vụ hiện tại không cho phép số lượng điểm bán hàng vượt quá ' . MAX_LOCATION . '. Hãy liên hệ với chúng tôi để biết thêm chi tiết!'));
		}
	}
}
?>