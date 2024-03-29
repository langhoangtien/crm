<?php
function get_css_files()
{
	$return = array();

	$css_files = array(
		array('path' =>'assets/css/bootstrap.min.css'),

		// array('path' =>'assets/css/dragtable.css'),
		array('path' =>'assets/css/jquery-ui.css'),
		array('path' =>'assets/css/themify-icons.css'),
		array('path' =>'assets/css/table.css'),
		array('path' =>'assets/css/animate.css'),
		array('path' =>'assets/css/ionicons.min.css'),
		array('path' =>'assets/css/profile.css'),
		array('path' =>'assets/css/mail.css'),
		array('path' =>'assets/css/bootstrap-datepicker3.css'),
		array('path' =>'assets/css/bootstrap-datetimepicker.css'),
		array('path' =>'assets/css/buttons.css'),
		array('path' =>'assets/css/tabs-accordions.css'),
		array('path' =>'assets/css/bootstrap-switch.min.css'),
		array('path' =>'assets/css/loading-bar.css'),
		array('path' =>'assets/css/modals.css'),
		array('path' =>'assets/css/infoboxes.css'),
		array('path' =>'assets/css/basic-tables.css'),
		array('path' =>'assets/css/selectize.css'),
		array('path' =>'assets/css/selectize.bootstrap3.css'),
		array('path' =>'assets/css/bootstrap-editable.css'),
		array('path' =>'assets/css/invoice.css'),
		array('path' =>'assets/css/toastr.css'),
		array('path' =>'assets/css/style.css'),
		array('path' =>'assets/css/extended_style.css'),
		array('path' =>'assets/css/custom.css'),
		array('path' =>'assets/css/bootstrap-select.css'),
		array('path' =>'assets/css/select2.css'),
		array('path' =>'assets/css/token-input-facebook.css'),
		array('path' =>'assets/css/bootstrap-colorpicker.min.css'),
		array('path' =>'assets/css/signin2.css'),
		array('path' =>'assets/css/stacktable.css'),
		array('path' =>'assets/css/dark.css'),
		array('path' =>'assets/css/biz/loading.css'),
		array('path' =>'assets/js/plugins/treeGrid/css/jquery.treegrid.css'),
		array('path' =>'assets/css/dataTables.bootstrap.css'),
		array('path' =>'assets/js/plugins/dataTables/export-datatable/buttons.dataTables.min.css'),
//		array('path' =>'assets/css/check-box.css'),

	);

	if(!defined("ASSET_MODE") or ASSET_MODE == 'development')
	{
		$return = $css_files;
	}
	else
	{
		$return[] = array('path' =>"assets/css/all.css");
	}
	
	if (function_exists('get_instance'))
	{
	   $CI =& get_instance();
		
		if (function_exists('is_rtl_lang'))
		{
			if(is_rtl_lang())
			{
				$return[] = array('path' =>'assets/css/rtl.css');
				$return[] = array('path' =>'assets/css/register-rtl.css');
			}
		}
	}
	
	return $return;
}

function get_js_files()
{
	if(!defined("ASSET_MODE") or ASSET_MODE == 'development')
	{
		return array(
			array('path' =>'assets/js/jquery-1.11.2.min.js'),
			array('path' =>'assets/js/bootstrap-switch.js'),
			array('path' =>'assets/js/quick-nav.js'),
			array('path' =>'assets/js/jquery.clicktoggle.js'),
		    array('path' =>'assets/js/jquery.cookie.js'),
			array('path' =>'assets/js/jquery-ui.custom.min.js'),
			array('path' =>'assets/js/bootstrap-3.min.js'),
			array('path' =>'assets/js/bootbox.min.js'),
			array('path' =>'assets/js/plugins/dataTables/jquery.dataTables.min.js'),
			array('path' =>'assets/js/plugins/dataTables/dataTables.bootstrap.js'),
			array('path' =>'assets/js/bootstrap-datatables.js'),
			array('path' =>'assets/js/moment-with-locales.js'),
			array('path' =>'assets/js/bootstrap-datetimepicker.min.js'),
			array('path' =>'assets/js/daterangepicker.js'),
			array('path' =>'assets/js/bootstrap-select.min.js'),  // Do we use this?
			array('path' =>'assets/js/select2.min.js'),
			array('path' =>'assets/js/imagePreview.js'),
			array('path' =>'assets/js/jquery.tablesorter.min.js'),
			array('path' =>'assets/js/jquery.validate.js'),
			array('path' =>'assets/js/common.js'),
			array('path' =>'assets/js/jquery.form.js'),
			array('path' =>'assets/js/manage_tables.js'),
			array('path' =>'assets/js/jquery.tokeninput.js'),
			array('path' =>'assets/js/jquery.imagerollover.js'),
			array('path' => 'assets/js/bootstrap-colorpicker.min.js'),
			array('path' => 'assets/js/chart.js'),
			array('path' => 'assets/js/SigWebTablet.js'),
			array('path' => 'assets/js/signature_pad.min.js'),
			array('path' => 'assets/js/jquery.playSound.js'),
			array('path' => 'assets/js/toastr.min.js'),
			array('path' => 'assets/js/selectize.js'),
			array('path' => 'assets/js/jquery.sieve.min.js'),
			array('path' => 'assets/js/jquery.nicescroll.min.js'),
			array('path' => 'assets/js/wow.min.js'),
			array('path' => 'assets/js/jquery.accordion.js'),
			array('path' => 'assets/js/form-validation/bootstrap-filestyle.js'),
			array('path' => 'assets/js/bootstrap-editable.min.js'),
			array('path' => 'assets/js/core.js'),
			array('path' => 'assets/js/stacktable.js'),
			array('path' => 'assets/js/biz/jscolor.js'),
			array('path' => 'assets/js/autoNumeric.js'),
			array('path' => 'assets/js/loading-bar.js'),
			array('path' => 'assets/js/plugins/treeGrid/js/jquery.treegrid.js')
		);
	}

	return array(
		 array('path' =>'assets/js/all.js'),
	);



}
?>