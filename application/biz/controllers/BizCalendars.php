<?php 
require_once (APPPATH . "controllers/Secure_area.php");
class BizCalendars extends Secure_area
{
	function __construct($module_id = null)
	{
		parent::__construct($module_id);
	}
	function index() {
		$this->load->view('calendar/calendar', array());
	}
	
	function view_event() {
		$this->load->model('TaskPersonal');
		$start = $_GET['start'];
		$end = $_GET['end'];
		$option =1;
		$data = $this->TaskPersonal->get_task_personal_for_calendar($start, $end,$option);
		// echo "<pre>"; print_r($data); die();
		echo json_encode($data);
	}
}