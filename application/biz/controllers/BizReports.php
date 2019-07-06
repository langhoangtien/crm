<?php
require_once (APPPATH . "controllers/Reports.php");
class BizReports extends Reports 
{
	function __construct()
	{
		parent::__construct();
		$this->load->helper('items');
		$this->load->helper('bizexcel');
		$this->load->model('Location');
        $this->load->model('Contract');
    }

    function detailed_inventory($item_id, $export_excel=0)
    {

      $locationIds = Report::get_selected_location_ids();

      $allItems = [];

      foreach ($locationIds as $locationId) {
         $items = $this->Location->getAllQty($locationId, $item_id);
         $allItems[$locationId] = $items; 
     }

     if ($export_excel) {

         $bizExcel = new BizExcel('ATonKho.xlsx');
         $bizExcel->setNumberRowStartBody(4)->setHeaderOfBody($this->getHeaderOfDetailInventory());
         $index = 0;
         foreach ($allItems as $locationId => $items)
         {
            $location = $this->Location->get_info($locationId);
            $bizExcel->setDataExcel($items);
            $bizExcel->addToNewSheet($location->name)->generateFile(false, '', false);
            $index ++;
        }

        $excelContent = $bizExcel->generateFile(false);
        $this->load->helper('download');
        force_download('DetailInventory.xlsx', $excelContent);
    } else {
     $data = array(
        "title" => lang('reports_detail_inventory_report'),
        "subtitle" => '',
        "data" => $allItems,
    );
     $this->load->view("reports/detail_inventory", $data);
 }
}

protected function getHeaderOfDetailInventory() {
  return array(
    array(
      'col' => 'A',
      'value_field' => '__AUTO__',
  ),
    array(
      'col' => 'B',
      'value_field' => 'product_id',
  ),
    array(
      'col' => 'C',
      'value_field' => 'name',
  ),
    array(
      'col' => 'D',
      'value_field' => 'measure_name',
      'footer' => 'SUM'
  ),

    array(
      'col' => 'E',
      'value_field' => 'quantity',
  ),
    array(
      'col' => 'F',
      'value_field' => 'cost_price',
      'footer' => 'SUM',
      'format' => 'price'
  ),
    array(
      'col' => 'G',
      'value_field' => 'total_cost_price',
      'footer' => 'SUM',
      'format' => 'price'
  )
);
}
	 #---------------------------------------------------------------------------------------------------#

							# Báo cáo chi tiết xuất nhập tồn

    #---------------------------------------------------------------------------------------------------#

function bao_cao_chi_tiet_xuat_nhap_ton() {

    $data = $this->_get_common_report_data(TRUE);

    $data['specific_input_name'] = lang('common_item');
    $data['search_suggestion_url'] = site_url('reports/item_search');

    $locations = array();
    foreach($this->Location->get_all()->result() as $location_row) 
    {
     $locations[$location_row->location_id] = $location_row->name;
 }
 $data['locations'] = $locations;

 $data['can_view_inventory_at_all_locations'] = $this->Employee->has_module_action_permission('reports','view_inventory_at_all_locations', $this->Employee->get_logged_in_employee_info()->person_id);

 $this->load->view("reports/bao_cao_chi_tiet_xuat_nhap_ton_input",$data);	


}

function bao_cao_chi_tiet_xuat_nhap_ton_store($start_date, $end_date,$id_san_pham, $export_excel=0, $offset = 0) {
  $start_date=rawurldecode($start_date);
  $end_date=rawurldecode($end_date);

  $arrParam['start_date'] = $start_date;
  $arrParam['end_date'] = $end_date;
  $arrParam['id_san_pham'] = $id_san_pham;
  $location_ids = Report::get_selected_location_ids();
  $arrParam['location_ids'] = implode(',', $location_ids);


  $this->load->model('reports/Summary_inventory');
  $model = $this->Summary_inventory;

  $arrParam['paginator']             = $this->_paginator;
  $arrParam['page']                  =  $this->uri->segment(3, 1);

  $config['base_url'] = base_url() . 'reports/bao_cao_chi_tiet_xuat_nhap_ton_store';            
  $config['per_page'] = $arrParam['paginator']['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
  $config['uri_segment'] = 3;
  $config['use_page_numbers'] = TRUE;

  $lay_danh_sach_ton_dau_san_pham = $model->CHI_TIET_ton_dau_san_pham($arrParam);


  $lay_chi_tiet_giao_dich_tung_san_pham = $model->CHI_TIET_GIAO_DICH_tung_san_pham($arrParam);

  $lay_tong_giao_dich_theo_tung_san_pham = $model->TONG_GIAO_DICH_theo_tung_san_pham($arrParam)[0]['TONG_HOP_XUAT_NHAP'];

  if(isset($lay_danh_sach_ton_dau_san_pham[0]['ton_dau_ky']))
     $debt_start = round($lay_danh_sach_ton_dau_san_pham[0]['ton_dau_ky'],2) + round($lay_tong_giao_dich_theo_tung_san_pham,2);
 else $debt_start = 0;

 $config['total_rows'] = count($lay_chi_tiet_giao_dich_tung_san_pham);
			# Lấy ra mảng đã sắp xếp
 $stt = 1;
 $data[1] = $lay_danh_sach_ton_dau_san_pham[0];
 $data[1]['key'] = 0;
 $data[1]['location_id'] = $arrParam['location_ids'];
 $data[1]['location_id'] = $lay_danh_sach_ton_dau_san_pham[0]['TEN_KHO'];

 if(!empty($lay_chi_tiet_giao_dich_tung_san_pham)){
    foreach ($lay_chi_tiet_giao_dich_tung_san_pham as $key => $value) {
        $stt++;
        $data[$stt] = $lay_chi_tiet_giao_dich_tung_san_pham[$key];
        $data[$stt]['key'] = $stt;
        $data[$stt]['location_id'] = $lay_chi_tiet_giao_dich_tung_san_pham[$key]['name'];
    }
} else {

    $data = $lay_danh_sach_ton_dau_san_pham;
    $data[0]['location_id'] = $arrParam['location_ids'];
    $data[0]['location_id'] = $lay_danh_sach_ton_dau_san_pham[0]['TEN_KHO'];

}


if(!empty($data)) sap_xep_mang($data,'key',true);


			# Xử lý tính nợ
if(!isset($data)) {
    echo 'Không có dữ liệu';
    return;
}


foreach ($data as $key => $value) {
   if($key == 0 || $value['bat_dau'] == 1){
            		# Gán dữ liệu cho nợ đầu bằng nợ đầu kỳ

      $data[$key]['ton_dau']  = $debt_start;
      $data[$key]['ton_cuoi'] = round($debt_start - $data[$key]['xuat_kho'] + $data[$key]['nhap_kho'],2);

  } else {

      if($key == 0) $vitri = 0; else $vitri = $key - 1;
      if(isset($data[$vitri]['ton_cuoi'])) $ton_cuoi = round($data[$vitri]['ton_cuoi'],2); 
      else $ton_cuoi = round($ton_dau_ky[0]['ton_dau_ky'],2);

      $data[$key]['ton_dau'] 	= $ton_cuoi;
      $data[$key]['ton_cuoi'] = round($data[$key]['ton_dau'] - $data[$key]['xuat_kho'] + $data[$key]['nhap_kho'],2);
  }


}


$debt_start = $debt_start;
if(empty($data)) $debt_end = $debt_start; 
else $debt_end = end($data)['ton_cuoi'];

$data_tong_hop['body'] = $this->load->view('reports/table/chi_tiet_xuat_nhap_ton_list', array('items'=>$data), true);
$data_tong_hop['debt_start'] = $debt_start;
$data_tong_hop['debt_end'] = $debt_end;
$data_tong_hop['start_date'] = $start_date;
$data_tong_hop['end_date'] = $end_date;
$data_tong_hop['specific_cus_title'] = lang('reports_detail_stock_movement_summary_report');
$data_tong_hop['item_name'] = $lay_danh_sach_ton_dau_san_pham[0]['item_name'];

$this->load->library("pagination");
$this->pagination->initialize($config);
$this->pagination->createConfig('front-end');

$pagination = $this->pagination->create_ajax();

$this->load->view("reports/bao_cao_chi_tiet_xuat_nhap_ton",$data_tong_hop);


}

function bao_cao_chi_tiet_xuat_nhap_ton_excel() {
    $arrParam = $this->input->get();

    unset($_SESSION['bao_cao_chi_tiet_xuat_nhap_ton']);
    $arrParam['options'] = $arrParam['customer_balance_options'];

    if($arrParam['customer_id'] == -1) {
        echo 'Bạn chưa chọn khách hàng';
        return;
    }

    $this->load->model('reports/bao_cao_chi_tiet_xuat_nhap_tonount');
    $model = $this->bao_cao_chi_tiet_xuat_nhap_tonount;
        #-------------------------------------------------------------------------------------------------#

        # Biến hien_thi có cấu trúc C:D:E với C:D là 2 trường cần merge E là trường vị trí hiển thị dữ liệu

        #-------------------------------------------------------------------------------------------------#
    $thong_tin_diem_ban_hang = $this->Location->get_info($_GET['location_ids'])->name;
    $ten_cong_ty = $this->config->item('company');

        # đầu trang

    $_title = array(
        array('value_field' => 'title','dong_nao'=>4,'hien_thi'=>'A:H:A'),
        array('value_field' => 'date','dong_nao'=>5,'hien_thi'=>'A:H:B'),
        array('value_field' => 'ten_khach_hang','dong_nao'=>6,'hien_thi'=>'A:B:A'),
        array('value_field' => 'dau_ky','dong_nao'=>7,'hien_thi'=>'B:B:B'),
        array('value_field' => 'cuoi_ky','dong_nao'=>8,'hien_thi'=>'B:B:B'),
        array('value_field' => 'thong_tin_diem_ban_hang','dong_nao'=>2,'hien_thi'=>'A:D:A'),
        array('value_field' => 'ten_cong_ty','dong_nao'=>1,'hien_thi'=>'A:D:A'),
    );
//      echo $this->config->item('company');
//      die;
    $_headers    = array(
        array('col' => 'A','value_field' => '__AUTO__'),
        array('col' => 'B','value_field' => 'date_sale'),
        array('col' => 'C','value_field' => 'sale_id'),
        array('col' => 'D','value_field' => 'no_dau'),
        array('col' => 'E','value_field' => 'ghi_no'),
        array('col' => 'F','value_field' => 'ghi_co'),
        array('col' => 'G','value_field' => 'no_cuoi'),
        array('col' => 'H','value_field' => 'comment'),
    );      


        # dữ liệu truyền ra 
    $items      = $model->lay_danh_sach_giao_dich_khach_hang($arrParam)->result_array();
    $no_dau_ky  = $model->lay_no_dau_ky_theo_tung_khach_hang($arrParam);
    $tong_tien_giao_dich  = $model->lay_tong_giao_dich_theo_tung_khach($arrParam)->result_array()[0]['TONG_TIEN_GIAO_DICH'];
    $data = array();


    $debt_start = round($no_dau_ky[0]['no_dau_ky'],2) + round($tong_tien_giao_dich,2);

        # Lấy ra mảng đã sắp xếp
    $stt = 1;
    foreach ($items as $key => $value) {

        if($value['sale_id'] == NULL){
            $data[1] = $items[$key];
            $data[1]['key'] = 0;
        } else {
            $stt++;
            $data[$stt] = $items[$key];
            $data[$stt]['key'] = $stt;
        }
    }
    if(!empty($data)) sap_xep_mang($data,'key',true);

        # Xử lý tính nợ

    foreach ($data as $key => $value) {
        if($key == 0 || $value['sale_id'] = NULL){
                # Gán dữ liệu cho nợ đầu bằng nợ đầu kỳ
            $data[$key]['no_dau']  = $debt_start;
            $data[$key]['no_cuoi'] = round($debt_start - $data[$key]['ghi_co'] + $data[$key]['ghi_no'],2);

        } else {

            if($key == 0) $vitri = 0; else $vitri = $key - 1;
            if(isset($data[$vitri]['no_cuoi'])) $no_cuoi = round($data[$vitri]['no_cuoi'],2); 
            else $no_cuoi = round($no_dau_ky[0]['no_dau_ky'],2);

            $data[$key]['no_dau']   = $no_cuoi;
            $data[$key]['no_cuoi'] = round($data[$key]['no_dau'] - $data[$key]['ghi_co'] + $data[$key]['ghi_no'],2);
        }
    }

    $dau_ky = $debt_start;



    if(empty($data)) {
        $cuoi_ky =  $debt_start;
        $debt_end = to_currency($debt_start); 
    }
    else {
        $debt_end = to_currency(end($data)['no_cuoi']);
        $cuoi_ky = end($data)['no_cuoi'];
    }

    $debt_start = to_currency($debt_start);

    if($arrParam['options'] == 1){
        $title = 'BÁO CÁO CHI TIẾT CÔNG NỢ KHÁCH HÀNG - Tài khoản khách nợ';
    } else $title = 'BÁO CÁO CHI TIẾT CÔNG NỢ KHÁCH HÀNG - Tài khoản nợ khách';
    if(empty($arrParam['start_date'])) $date = 'Toàn bộ thời gian'; else $date = 'Từ : '.$arrParam['start_date'].' đến : '.$arrParam['end_date'];


    $ten_khach_hang = 'Tên khách hàng : '.$no_dau_ky[0]['last_name'];
    foreach ($data as $value) {
            // echo $value['no_dau_ky'];
        $result[] = array(
            'sale_id'       =>  $value['sale_id'],
            'date_sale'     =>  $value['date'],
            'comment'       =>  $value['comment'],
            'no_dau'        =>  $value['no_dau'],
            'ghi_co'        =>  $value['ghi_co'],
            'ghi_no'        =>  $value['ghi_no'],
            'no_cuoi'       =>  $value['no_cuoi'],
            'dau_ky'        =>  $dau_ky,
            'cuoi_ky'       =>  $cuoi_ky,
            'ten_khach_hang'=>  $ten_khach_hang,
            'date'          =>  $date,
            'thong_tin_diem_ban_hang' => $thong_tin_diem_ban_hang,
            'ten_cong_ty'   =>  $ten_cong_ty,
            'title'         =>  $title,
        );
    }

    $bizExcel = new BizExcel('bao_cao_chi_tiet_cong_no_khach_hang.xlsx');
    $bizExcel->Row_title($_title);
    $bizExcel->setNumberRowStartBody(10)->setHeaderOfBody($_headers);
    $bizExcel->tat_auto_size(false);
    $bizExcel->setDataExcel($result);


    $excelContent = $bizExcel->generateFile(false);
        // die;
    $this->load->helper('download');
    force_download('bao_cao_chi_tiet_cong_no_khach_hang.xlsx', $excelContent);
    exit;
}



	 #---------------------------------------------------------------------------------------------------#




							# Báo cáo xuất nhập tồn
							# Báo cáo xuất nhập tồn
							# Báo cáo xuất nhập tồn
							# Báo cáo xuất nhập tồn
							# Báo cáo xuất nhập tồn
							# Báo cáo xuất nhập tồn
							# Báo cáo xuất nhập tồn






    #---------------------------------------------------------------------------------------------------#
function bao_cao_xuat_nhap_ton_test($id_sp) {
  $this->load->model('reports/Summary_inventory');
  $model = $this->Summary_inventory;
  $data['data'] = $model->getData($id_sp);
  $this->load->view('reports/test',$data);
}

function bao_cao_xuat_nhap_ton() {


    $data['action']    = $this->uri->segment(2);
    $data['locations'] = $this->Location->list_item();
    $data['title']     = 'Báo cáo xuất nhập tồn';
    $data['no_excel']  = false;

        //$_SESSION['bao_cao_xuat_nhap_ton'] = array(1);
    if(isset($_SESSION['bao_cao_xuat_nhap_ton'])) {
       $data['url_print'] = $_SESSION['bao_cao_xuat_nhap_ton']['url_print'];
       $data['filter']    = $filter = $_SESSION['bao_cao_xuat_nhap_ton'];
             // unset($_SESSION['bao_cao_xuat_nhap_ton']);

       if($data['filter']['export_excel'] == 1)
           redirect($data['url_print']);
       else
           $this->load->view("reports/bao_cao_xuat_nhap_ton",$data);

   } else{
    $data['inputs']      = array('input_date_range','input_locations');
    $data['customer_id'] = $this->input->get('cus_id', -1);
    $this->load->view("reports/n9_tabular",$data);
}
}

function bao_cao_xuat_nhap_ton_store() {

    $arrParam = $_SESSION['bao_cao_xuat_nhap_ton'];
    $arrParam['location_ids'] = implode(',', $arrParam['selected_location_ids']);


    unset($_SESSION['bao_cao_xuat_nhap_ton']);

    if(!empty($arrParam)) {
        $this->load->model('reports/Summary_inventory');
        $model = $this->Summary_inventory;

        $arrParam['paginator']             = $this->_paginator;
        $arrParam['page']                  =  $this->uri->segment(3, 1);

        $config['base_url'] = base_url() . 'reports/bao_cao_xuat_nhap_ton_store';            
        $config['per_page'] = $arrParam['paginator']['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
        $config['uri_segment'] = 3;
        $config['use_page_numbers'] = TRUE;

        $tong_hop_ton_dau_san_pham = $model->TONG_HOP_ton_dau_san_pham($arrParam);
        $tong_hop_xuat_nhap_san_pham_truoc_do = $model->TONG_HOP_xuat_nhap_san_pham_before($arrParam);
        $tong_hop_xuat_nhap_san_pham_trong_khoang_thoi_gian = $model->TONG_HOP_xuat_nhap_san_pham($arrParam);

            // echo '<pre>';
              	// var_dump($tong_hop_ton_dau_san_pham);

        $tong_xuat_kho = 0;
        $tong_nhap_kho = 0;


        foreach ($tong_hop_ton_dau_san_pham as $key => $value) {
           if(isset($tong_hop_xuat_nhap_san_pham_trong_khoang_thoi_gian[$key]['xuat_kho'])) $tong_xuat_kho = $tong_hop_xuat_nhap_san_pham_trong_khoang_thoi_gian[$key]['xuat_kho'];
           else $tong_xuat_kho = 0;
           if(isset($tong_hop_xuat_nhap_san_pham_trong_khoang_thoi_gian[$key]['nhap_kho'])) $tong_nhap_kho = $tong_hop_xuat_nhap_san_pham_trong_khoang_thoi_gian[$key]['nhap_kho'];
           else $tong_nhap_kho = 0;

            	# Lấy số lượng xuất nhập khoảng thời gian trước
           if(isset($tong_hop_xuat_nhap_san_pham_truoc_do[$key])) $tong_hop_xuat_nhap = $tong_hop_xuat_nhap_san_pham_truoc_do[$key]['TONG_SO_LUONG_XUAT_NHAP'];
           else $tong_hop_xuat_nhap = 0;

           $tong_hop_ton_dau_san_pham[$key]['so_luong_xuat_kho'] = $tong_xuat_kho;
           $tong_hop_ton_dau_san_pham[$key]['so_luong_nhap_kho'] = $tong_nhap_kho;

           $tong_hop_ton_dau_san_pham[$key]['ton_dau_ky']  = $value['ton_dau_ky'] + $tong_hop_xuat_nhap;


           $tong_hop_ton_dau_san_pham[$key]['ton_cuoi_ky'] = $tong_hop_ton_dau_san_pham[$key]['ton_dau_ky'] - $tong_xuat_kho + $tong_nhap_kho;
       }

       foreach ($tong_hop_ton_dau_san_pham as $key => $value) {
           if($value['ton_cuoi_ky'] == 0)
              unset($tong_hop_ton_dau_san_pham[$key]);
      }

             # Chuyển đổi nợ đầu vào nợ cuối thành chuỗi
      $config['total_rows'] = count($tong_hop_ton_dau_san_pham);
      $tong_khach_no = 'Chưa có dữ liệu';
      $tong_no_khach = 'Chưa có dữ liệu';


      $html = $this->load->view('reports/table/xuat_nhap_ton_list', array('items'=>$tong_hop_ton_dau_san_pham), true);

      $this->load->library("pagination");
      $this->pagination->initialize($config);
      $this->pagination->createConfig('front-end');

      $pagination = $this->pagination->create_ajax();

      $result = array('count'=> "".$config['total_rows']."", 'html_string'=>$html, 'pagination'=>$pagination,'debt_start'=>$tong_khach_no, 'debt_end'=>$tong_no_khach);

      echo json_encode($result);
  }
}


function bao_cao_xuat_nhap_ton_excel() {

    $arrParam = $_SESSION['bao_cao_xuat_nhap_ton'];
    $arrParam['location_ids'] = implode(',', $arrParam['selected_location_ids']);
    unset($_SESSION['bao_cao_xuat_nhap_ton']);

    if(!empty($arrParam)) {
        $this->load->model('reports/Summary_inventory');
        $model = $this->Summary_inventory;
        $openingStock = $model->TONG_HOP_ton_dau_san_pham($arrParam);
        $summaryImportExportBeforeTimeStart = $model->TONG_HOP_xuat_nhap_san_pham_before($arrParam);
        $summaryImportExportBetweenTimeStartAndTimeEnd = $model->TONG_HOP_xuat_nhap_san_pham($arrParam);
        $summaryExport = 0;
        $summaryImport = 0;


        foreach ($openingStock as $key => $value) {
           if(isset($summaryImportExportBetweenTimeStartAndTimeEnd[$key]['xuat_kho'])) $summaryExport = $summaryImportExportBetweenTimeStartAndTimeEnd[$key]['xuat_kho'];
           else $summaryExport = 0;
           if(isset($summaryImportExportBetweenTimeStartAndTimeEnd[$key]['nhap_kho'])) $summaryImport = $summaryImportExportBetweenTimeStartAndTimeEnd[$key]['nhap_kho'];
           else $summaryImport = 0;

            	# Lấy số lượng xuất nhập khoảng thời gian trước
           if(isset($summaryImportExportBeforeTimeStart[$key])) $summaryExportImport = $summaryImportExportBeforeTimeStart[$key]['TONG_SO_LUONG_XUAT_NHAP'];
           else $summaryExportImport = 0;

           $openingStock[$key]['so_luong_xuat_kho'] = $summaryExport;
           $openingStock[$key]['so_luong_nhap_kho'] = $summaryImport;

           $openingStock[$key]['ton_dau_ky']  = $value['ton_dau_ky'] + $summaryExportImport;


           $openingStock[$key]['ton_cuoi_ky'] = $openingStock[$key]['ton_dau_ky'] - $summaryExport + $summaryImport;
       }

       foreach ($openingStock as $key => $value) {
           if($value['ton_cuoi_ky'] == 0)
              unset($openingStock[$key]);
      }
  }

  if (!empty($arrParam['start_date'])&& !empty($arrParam['end_date'])) {
    $period = lang('common_time_start').': '.$arrParam['start_date'].' '.lang('common_to').': '.$arrParam['end_date'];
}
else {
    $period = lang('reports_all_time');
}

$header_of_multicol[] = array('mergeStartCol' =>'A4','mergeEndCol'=>'J4','text' => lang('reports_summary_items_report'),'styles'=>array('bold' =>true,'font'=>true, 'font_size'=>20));
$header_of_multicol[] = array('mergeStartCol' =>'A5','mergeEndCol'=>'J5','text' => $period ,'styles'=>array('bold' =>true,'font'=>true, 'font_size'=>12));

$company_name    = $this->config->item('company');
$header_of_multicol[] = array('mergeStartCol' =>'A1','mergeEndCol'=>'D1','text' => $company_name ,'styles'=>array('bold' =>true,'font'=>true, 'font_size'=>12));

$fieldOfDataBody = array(
    array('col' => 'A','value_field' =>'ItemCode'),
    array('col' => 'B','value_field' =>'ItemName'),
    array('col' => 'C','value_field' =>'ItemOpeningStock'),
    array('col' => 'D','value_field' =>'ItemOpeningStockTotalMoney'),
    array('col' => 'E','value_field' =>'StockInQuantities'),
    array('col' => 'F','value_field' =>'StockInQuantitiesTotalMoney'),
    array('col' => 'G','value_field' =>'StockOutQuantities'),
    array('col' => 'H','value_field' =>'StockOutQuantitiesTotalMoney'),
    array('col' => 'I','value_field' =>'ItemClosingStock'),
    array('col' => 'J','value_field' =>'ItemClosingTotalMoney')
);
foreach($openingStock as $item)
{
    $array = array(

        'ItemCode'                     => $item['ma_san_pham'],
        'ItemName'                     => $item['ten_san_pham'],
        'ItemOpeningStock'             => round($item['ton_dau_ky'],2),
        'ItemOpeningStockTotalMoney'   => to_currency($item['ton_dau_ky']*$item['gia_von']),
        'StockInQuantities'            => round($item['so_luong_nhap_kho'],2),
        'StockInQuantitiesTotalMoney'  => to_currency($item['so_luong_nhap_kho']*$item['gia_von']),
        'StockOutQuantities'           => round($item['so_luong_xuat_kho'],2),
        'StockOutQuantitiesTotalMoney' => to_currency($item['so_luong_xuat_kho']*$item['gia_von']),
        'ItemClosingStock'             => round($item['ton_cuoi_ky'],2),
        'ItemClosingTotalMoney'        => to_currency($item['ton_cuoi_ky']*$item['gia_von'])
    );
    $result[]    =  $array;    
}     

$bizExcel = new BizExcel('export_import_inventory_reports.xlsx');
$bizExcel->setNumberRowStartBody(9)->setHeaderOfBody($fieldOfDataBody);
$bizExcel->setHeaderOfMultiCol($header_of_multicol);
$bizExcel->setDataExcel($result);
$excelContent = $bizExcel->generateFile(false);
$this->load->helper('download');
force_download(lang('reports_summary_items_report').'-'.lang('common_time_start').'-'.$arrParam['start_date'].'-'.lang('common_to').'-'.$arrParam['end_date'].'.xlsx', $excelContent);
exit;
}


function summary_inventory($start_date, $end_date, $export_excel=0, $offset = 0)
{
  $start_date =date('Y-m-d 00:00:00',strtotime($start_date));
  $end_date =date('Y-m-d 23:59:59',strtotime($end_date));



  $locationIds = Report::get_selected_location_ids();

  $historyTrans = $this->Inventory->getAllHistoryTrans(['start_date' => $start_date, 'end_date' => $end_date, 'locationIds' => $locationIds]);

  $historyTransBefore = $this->Inventory->getAllHistoryTransBefore(['end_date' => $start_date, 'locationIds' => $locationIds]);

  $allItems = [];
  foreach ($historyTrans as $item) {
     $allItems[$item['location_id']][$item['item_id']][] = $item;
 }


 $allTransItems = [];
 foreach ($allItems as $locationId => $items) {
     foreach ($items as $itemId => $rows) {
        $totalIn = 0;
        $totalOut = 0;

        foreach ($rows as $row) {
           if ($row['trans_inventory'] > 0) {
              $totalIn += $row['trans_inventory'];
          } else {
              $totalOut += $row['trans_inventory'];
          }
      }
      $allTransItems[$locationId][$itemId] = [
          'item_id' => $itemId,
          'product_id' => $row['product_id'],
          'name' => $row['name'],
          'category' => $row['category'],
          'cost_price' => $row['cost_price'],
          'unit_price' => $row['unit_price'],
          'total_qty_in' => to_quantity($totalIn),
          'total_cost_in' => NumberFormatToCurrency($totalIn * $row['cost_price']),
          'total_cost_in_origin' => $totalIn * $row['cost_price'],
          'total_qty_out' => to_quantity($totalOut),
          'total_price_out' => NumberFormatToCurrency($totalOut * $row['unit_price']),
          'total_price_out_origin' => $totalOut * $row['unit_price']
      ];
  }

}

$allItems = [];


foreach ($locationIds as $locationId) {
 if (!isset($allItems[$locationId])) {
    $allItems[$locationId] = [];
}
}

if ($export_excel) {
 $bizExcel = new BizExcel('ASummaryInventory.xlsx');
 $bizExcel->setNumberRowStartBody(5)->setHeaderOfBody($this->getHeaderOfSummaryInventory());
 $index = 0;
 foreach ($allItems as $locationId => $items)
 {
    $location = $this->Location->get_info($locationId);
    $bizExcel->setDataExcel($items);
    $bizExcel->addToNewSheet($location->name)->generateFile(false, '', false);
    $index ++;
}

$excelContent = $bizExcel->generateFile(false);
$this->load->helper('download');
force_download('SummaryInventory.xlsx', $excelContent);
} else {

 $data = array(
    "title" => lang('reports_summary_inventory_report'),
    "subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
    "data" => $allItems,
);
 $this->load->view("reports/summary_inventory", $data);

}

}

protected function getHeaderOfSummaryInventory() {
  return array(
    array(
      'col' => 'A',
      'value_field' => 'product_id',
  ),
    array(
      'col' => 'B',
      'value_field' => 'name',
  ),
    array(
      'col' => 'C',
      'value_field' => 'trans_total_qty_before',
      'footer' => 'SUM'
  ),
    array(
      'col' => 'D',
      'value_field' => 'trans_total_price_before',
      'footer' => 'SUM'
  ),

    array(
      'col' => 'E',
      'value_field' => 'total_qty_in',
      'footer' => 'SUM'
  ),
    array(
      'col' => 'F',
      'value_field' => 'total_cost_in',
      'footer' => 'SUM'
  ),
    array(
      'col' => 'G',
      'value_field' => 'total_qty_out',
      'footer' => 'SUM'
  ),
    array(
      'col' => 'H',
      'value_field' => 'total_price_out',
      'footer' => 'SUM'
  ),
    array(
      'col' => 'I',
      'value_field' => 'trans_total_qty_after',
      'footer' => 'SUM'
  ),

    array(
      'col' => 'J',
      'value_field' => 'trans_total_price_after',
      'footer' => 'SUM'
  )
);
}

function detailed_suspended_receivings($start_date, $end_date, $supplier_id,$sale_type, $export_excel=0, $offset=0)
{
  $this->load->model('Receiving');

  $start_date=rawurldecode($start_date);
  $end_date=rawurldecode($end_date);

  $this->load->model('reports/Detailed_receivings');
  $model = $this->Detailed_receivings;
  $model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel' => $export_excel, 'supplier_id' => $supplier_id, 'force_suspended' => true));

  $this->Receiving->create_receivings_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'force_suspended' => true));
  $config = array();
  $config['base_url'] = site_url("reports/detailed_receivings/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$supplier_id/$sale_type/$export_excel");
  $config['total_rows'] = $model->getTotalRows();
  $config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
  $config['uri_segment'] = 8;
  $this->load->library('pagination');$this->pagination->initialize($config);


  $headers = $model->getDataColumns();
  $report_data = $model->getData();

  $summary_data = array();
  $details_data = array();

  $location_count = count(Report::get_selected_location_ids());

  foreach($report_data['summary'] as $key=>$row)
  {
     $summary_data[$key] = array(
        array('data'=>anchor('receivings/receipt/'.$row['receiving_id'], '<i class="ion-printer"></i>', array('target' => '_blank')).' '.anchor('receivings/edit/'.$row['receiving_id'], '<i class="ion-document-text"></i>', array('target' => '_blank')).' '.anchor('receivings/edit/'.$row['receiving_id'], lang('common_edit').' '.$row['receiving_id'], array('target' => '_blank')).' ['.anchor('items/generate_barcodes_from_recv/'.$row['receiving_id'], lang('common_barcode_sheet'), array('target' => '_blank')).' / '.anchor('items/generate_barcodes_labels_from_recv/'.$row['receiving_id'], lang('common_barcode_labels'), array('target' => '_blank')).']', 'align'=> 'left'), 
        array('data'=>date(get_date_format(), strtotime($row['receiving_date'])), 'align'=> 'left'), 
        array('data'=>to_quantity($row['items_purchased']), 'align'=> 'left'),
        array('data'=>to_quantity($row['reports_measure_purchased']), 'align'=> 'left'), 
        array('data'=>$row['employee_name'], 'align'=> 'left'), 
        array('data'=>$row['supplier_name'], 'align'=> 'left'), 
        array('data'=>to_currency($row['subtotal']), 'align'=> 'right'), 
        array('data'=>to_currency($row['total']), 'align'=> 'right'),
        array('data'=>to_currency($row['tax']), 'align'=> 'right'), 
        array('data'=>$row['payment_type'], 'align'=> 'left'), 
        array('data'=>$row['comment'], 'align'=> 'left')
    );

     if ($location_count > 1)
     {
        array_unshift($summary_data[$key], array('data'=>$row['location_name'], 'align'=> 'left'));
    }

    foreach($report_data['details'][$key] as $drow)
    {
        if( $drow['measure_qty'] && $drow['measure_name'] ) {
           $details_data[$key][] = array(
              array('data'=>$drow['item_name'], 'align'=> 'left'),
              array('data'=>$drow['product_id'], 'align'=> 'left'),
              array('data'=>$drow['category'], 'align'=> 'left'),
              array('data'=>$drow['size'], 'align'=> 'left'),
						// array('data'=>to_quantity($drow['quantity_purchased']), 'align'=> 'left'),
						// array('data'=>to_quantity($drow['quantity_received']), 'align'=> 'left'),
              array('data'=>to_quantity($drow['measure_qty']), 'align'=>'left'),
              array('data'=>$drow['measure_name'], 'align'=>'left'),

              array('data'=>to_currency($drow['subtotal']), 'align'=> 'right'),
              array('data'=>to_currency($drow['total']), 'align'=> 'right'),
              array('data'=>to_currency($drow['tax']), 'align'=> 'right'),
              array('data'=>$drow['discount_percent'].'%', 'align'=> 'left')
          );
       } else {
           $this->load->model('Item');
           $details_data[$key][] = array(
              array('data'=>$drow['name'], 'align'=> 'left'),
              array('data'=>$drow['product_id'], 'align'=> 'left'),
              array('data'=>$drow['category'], 'align'=> 'left'),
              array('data'=>$drow['size'], 'align'=> 'left'),
              array('data'=>to_quantity($drow['quantity_purchased']), 'align'=> 'left'),
						// array('data'=>to_quantity($drow['quantity_received']), 'align'=> 'left'),
              array('data'=> $this->Item->getMeasureName($drow['item_id']), 'align'=>'left'),

              array('data'=>to_currency($drow['subtotal']), 'align'=> 'right'),
              array('data'=>to_currency($drow['total']), 'align'=> 'right'),
              array('data'=>to_currency($drow['tax']), 'align'=> 'right'),
              array('data'=>$drow['discount_percent'].'%', 'align'=> 'left')
          );
       }
   }
}

$data = array(
    "title" =>lang('reports_detailed_suspended_receivings_report'),
    "subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
    "headers" => $model->getDataColumns(),
    "summary_data" => $summary_data,
    "details_data" => $details_data,
    "overall_summary_data" => $model->getSummaryData(),
    "export_excel" => $export_excel,
    "pagination" => $this->pagination->create_links(),
);

$this->load->view("reports/tabular_details",$data);
}

function summary_items($start_date, $end_date, $do_compare, $compare_start_date, $compare_end_date, $supplier_id = -1, $category_id = -1, $sale_type = 'all', $export_excel=0, $offset = 0)
{
  $this->load->model('Category');
  $this->load->model('Sale');

  $start_date=rawurldecode($start_date);
  $end_date=rawurldecode($end_date);
  $compare_start_date=rawurldecode($compare_start_date);
  $compare_end_date=rawurldecode($compare_end_date);
  $data_row_excel = array();
  $subtitle = date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)).($do_compare  ? ' '. lang('reports_compare_to'). ' '. date(get_date_format(), strtotime($compare_start_date)) .'-'.date(get_date_format(), strtotime($compare_end_date)) : '');
  $this->load->model('reports/Summary_items');
  $model = $this->Summary_items;

  $headers = $model->getDataColumns();  

  $model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'category_id' => $category_id, 'supplier_id' => $supplier_id, 'offset' => $offset, 'export_excel' => $export_excel));
  $this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

  $config = array();
  $config['base_url'] = site_url("reports/summary_items/".rawurlencode($start_date).'/'.rawurlencode($end_date).'/'.$do_compare.'/'.rawurlencode($compare_start_date).'/'.rawurlencode($compare_end_date)."/$supplier_id/$category_id/$sale_type/$export_excel");
  $config['total_rows'] = $model->getTotalRows();
  $config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
  $config['uri_segment'] = 12;

  $this->load->library('pagination');$this->pagination->initialize($config);

  $tabular_data = array();
  $report_data = $model->getData();
  $summary_data = $model->getSummaryData();

  if ($do_compare)
  {
     $compare_to_items = array();

     for($k=0;$k<count($report_data);$k++)
     {
        $compare_to_items[] = $report_data[$k]['item_id'];
    }

    $model_compare = $this->Summary_items;
    $model_compare->setParams(array('start_date'=>$compare_start_date, 'end_date'=>$compare_end_date, 'sale_type' => $sale_type, 'category_id' => $category_id, 'supplier_id' => $supplier_id, 'offset' => $offset, 'export_excel' => $export_excel, 'compare_to_items' => $compare_to_items));

    $this->Sale->drop_sales_items_temp_table();
    $this->Sale->create_sales_items_temp_table(array('start_date'=>$compare_start_date, 'end_date'=>$compare_end_date, 'sale_type' => $sale_type));

    $report_data_compare = $model_compare->getData();
    $report_data_summary_compare = $model_compare->getSummaryData();
}


foreach($report_data as $row)
{

 if($row['name'] != lang('common_discount'))
 {
    if ($do_compare)
    {
       $index_compare = -1;
       $item_id_to_compare_to = $row['item_id'];

       for($k=0;$k<count($report_data_compare);$k++)
       {
          if ($report_data_compare[$k]['item_id'] == $item_id_to_compare_to)
          {
             $index_compare = $k;
             break;
         }
     }

     if (isset($report_data_compare[$index_compare]))
     {
      $row_compare = $report_data_compare[$index_compare];
  }
  else
  {
      $row_compare = FALSE;
  }
}

$data_row = array();
$data_row[] = array('data'=>$row['name'], 'align' => 'center');
$data_row[] = array('data'=>$row['item_number'], 'align' => 'center');
$data_row[] = array('data'=>$row['product_id'], 'align' => 'center');
$data_row[] = array('data'=>$row['category'], 'align' => 'center');
$data_row[] = array('data'=>to_currency($row['current_selling_price']), 'align' => 'right');
$data_row[] = array('data'=>qtyToString($row['item_id'], $row['quantity']), 'align' => 'center');
$data_row[] = array('data'=>qtyToString($row['item_id'], $row['quantity_purchased']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['quantity_purchased'] >= $row['quantity_purchased'] ? ($row['quantity_purchased'] == $row_compare['quantity_purchased'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_quantity($row_compare['quantity_purchased']) .'</span>' :''), 'align' => 'center');
$data_row[] = array('data'=>to_currency($row['subtotal']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['subtotal'] >= $row['subtotal'] ? ($row['subtotal'] == $row_compare['subtotal'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['subtotal']) .'</span>' :''), 'align' => 'right');
$data_row[] = array('data'=>to_currency($row['total']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['total'] >= $row['total'] ? ($row['total'] == $row_compare['total'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['total']) .'</span>' :''), 'align' => 'right');
$data_row[] = array('data'=>to_currency($row['tax']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['tax'] >= $row['tax'] ? ($row['tax'] == $row_compare['tax'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['tax']) .'</span>' :''), 'align' => 'right');
if($this->has_profit_permission)
{
   $data_row[] = array('data'=>to_currency($row['profit']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['profit'] >= $row['profit'] ? ($row['profit'] == $row_compare['profit'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['profit']) .'</span>' :''), 'align' => 'right');
   $profit = to_currency($row['profit']).($do_compare && $row_compare ? '/'.to_currency($row_compare['profit']) :'');
}
$tabular_data[] = $data_row;
$data_row_excel[] = array(
 'name'                  => $row['name'], 
 'item_number'           => $row['item_number'],
 'product_id'            => $row['product_id'],
 'category'              => $row['category'],
 'current_selling_price' => to_currency($row['current_selling_price']),
 'item_id'               => qtyToString($row['item_id'], $row['quantity']),
 'value'                 => qtyToString($row['item_id'], $row['quantity_purchased']).($do_compare && $row_compare ? '/'.to_quantity($row_compare['quantity_purchased']) :''),
 'subtotal'              => to_currency($row['subtotal']).($do_compare && $row_compare ?'/'.to_currency($row_compare['subtotal']) :'') ,
 'total'                 => to_currency($row['total']).($do_compare && $row_compare ?'/'.to_currency($row_compare['total']) :''),
 'tax'                   => to_currency($row['tax']).($do_compare && $row_compare ?'/'.to_currency($row_compare['tax']) :''),
 'profit'                => !empty($profit)?$profit:''
);


}

}

if ($do_compare)
{
 foreach($summary_data as $key=>$value)
 {

    if($export_excel == 1)
    {
       $summary_data[$key] = to_currency($value) . ' / '.to_currency($report_data_summary_compare[$key]);
   }
   else
   {
       $summary_data[$key] = to_currency($value) . ' / <span class="compare '.($report_data_summary_compare[$key] >= $value ? ($value == $report_data_summary_compare[$key] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($report_data_summary_compare[$key]).'</span>';
   }

}

}
if($export_excel == 0)
{
   $data = array(
       "title" => lang('reports_items_summary_report'),
       "subtitle" => $subtitle,
       "headers" => $model->getDataColumns(),
       "data" => $tabular_data,
       "summary_data" => $summary_data,
       "export_excel" => $export_excel,
       "pagination" => $this->pagination->create_links()
   );

   $this->load->view("reports/tabular",$data);
}
else
{
    $fieldOfBody     = array(
      array('col' => 'A','value_field' =>'__AUTO__'),
      array('col' => 'B','value_field' =>'name'),
      array('col' => 'C','value_field' =>'item_number'),
      array('col' => 'D','value_field' =>'product_id'),
      array('col' => 'E','value_field' =>'category'),
      array('col' => 'F','value_field' =>'current_selling_price'),
      array('col' => 'G','value_field' =>'item_id'),
      array('col' => 'H','value_field' =>'value'),
      array('col' => 'I','value_field' =>'subtotal'),
      array('col' => 'J','value_field' =>'total'),
      array('col' => 'K','value_field' =>'tax'),
  );
    if($this->Employee->has_module_action_permission('reports','show_profit',$this->Employee->get_logged_in_employee_info()->person_id))
    {
        $fieldOfBody[] =  array('col' => 'L','value_field' =>'profit');
    }

			//Header of parent Col
    $excelColumn = 'B';
    foreach($headers as $key => $header_detail)
    {
        $header_of_col_name[] = array('col' =>$excelColumn,'text' => $header_detail['data'],'styles'=>array('bold' =>true,'font'=>true, 'font_size'=>14,'is_fill'=>true,'color'=>'98d9da'));
        $excelColumn++;
    }

    $header_of_col_name[] = array('col' =>'A','text' => 'STT','styles'=>array('bold' =>true,'font'=>true, 'font_size'=>14,'is_fill'=>true,'color'=>'98d9da'));

			//merge cell
    $header_of_multicol[] = array('mergeStartCol' =>'A1','mergeEndCol'=>$excelColumn.'1','text' =>lang('reports_summary_items'),'styles'=>array('bold' =>true,'font'=>true, 'font_size'=>26,'is_fill'=>true,'color'=>'98d9da'));
    $numberRowStartBody = 2;
    if ($do_compare)
    {
        $numberRowStartBody++;
        $header_of_multicol[] = array('mergeStartCol' =>'A2','mergeEndCol'=>'D2','text' =>(lang('reports_summary_items').': '.$subtitle),'styles'=>array('bold' =>true,'font'=>true, 'font_size'=>12));
    }
    $numberRowStartSumary = $numberRowStartBody + count($data_row_excel)+1;
    $header_of_multicol[] = array('mergeStartCol' =>'K'.$numberRowStartSumary,'mergeEndCol'=>'L'.$numberRowStartSumary++,'text' =>(lang('reports_subtotal').': '.to_currency($summary_data['subtotal'])),'styles'=>array('bold' =>true,'font'=>true, 'font_size'=>12));
    $header_of_multicol[] = array('mergeStartCol' =>'K'.$numberRowStartSumary,'mergeEndCol'=>'L'.$numberRowStartSumary++,'text' =>(lang('reports_total_tax_included').': '.to_currency($summary_data['total_'])),'styles'=>array('bold' =>true,'font'=>true, 'font_size'=>12));
    $header_of_multicol[] = array('mergeStartCol' =>'K'.$numberRowStartSumary,'mergeEndCol'=>'L'.$numberRowStartSumary++,'text' =>(lang('reports_tax').': '.to_currency($summary_data['tax'])),'styles'=>array('bold' =>true,'font'=>true, 'font_size'=>12));
    if ($this->has_profit_permission)
    {
      $header_of_multicol[] = array('mergeStartCol' =>'K'.$numberRowStartSumary,'mergeEndCol'=>'L'.$numberRowStartSumary,'text' =>(lang('reports_profit').': '.to_currency($summary_data['profit'])),'styles'=>array('bold' =>true,'font'=>true, 'font_size'=>12));
  }

			//header of File

  $bizExcel = new BizExcel('report_specific_supplier.xlsx');
  $bizExcel->setHeaderOfMultiCol($header_of_multicol);
  $bizExcel->setNumberRowStartBody($numberRowStartBody)->setHeaderOfBody($fieldOfBody);
  $bizExcel->setHeaderOfCol($header_of_col_name);
  $bizExcel->setDataExcel($data_row_excel);
  $excelContent = $bizExcel->generateFile(false);
  $this->load->helper('download');
  force_download(lang('reports_summary_items').' '.$supplier_info->company_name. date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)).'.xlsx', $excelContent);
  exit;
}

}

function inventory_low($supplier = -1, $category_id = -1, $inventory = 'all', $reorder_only = 0, $export_excel=0, $offset=0)
{
  $category_id = rawurldecode($category_id);


  $this->load->model('reports/Inventory_low');
  $model = $this->Inventory_low;
  $model->setParams(array('supplier'=>$supplier,'category_id' => $category_id, 'export_excel' => $export_excel, 'offset'=>$offset, 'inventory' => $inventory, 'reorder_only' => $reorder_only));

  $config = array();
  $config['base_url'] = site_url("reports/inventory_low/$supplier/$category_id/$inventory/$reorder_only/export_excel");
  $config['total_rows'] = $model->getTotalRows();
  $config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
  $config['uri_segment'] = 8;
  $this->load->library('pagination');$this->pagination->initialize($config);

  $tabular_data = array();
  $report_data = $model->getData();
  $location_count = count(Report::get_selected_location_ids());

  foreach($report_data as $row)
  {
     $data_row = array();


     if ($location_count > 1)
     {
        $data_row[] = array('data'=>$row['location_name'], 'align' => 'left');
    }
    $data_row[] = array('data'=>$row['item_id'], 'align' => 'left');
    $data_row[] = array('data'=>$row['name'], 'align' => 'left');
    $data_row[] = array('data'=>$row['category'], 'align'=> 'left');
    $data_row[] = array('data'=>$row['company_name'], 'align'=> 'left');
    $data_row[] = array('data'=>$row['item_number'], 'align'=> 'left');
    $data_row[] = array('data'=>$row['product_id'], 'align'=> 'left');
    $data_row[] = array('data'=>$row['description'], 'align'=> 'left');
    $data_row[] = array('data'=>$row['size'], 'align'=> 'left');
    $data_row[] = array('data'=>$row['location'], 'align'=> 'left');

    if($this->has_cost_price_permission)
    {
        $data_row[] = array('data'=>to_currency($row['cost_price']), 'align'=> 'right');
    }
    $data_row[] = array('data'=>to_currency($row['unit_price']), 'align'=> 'right');
    $data_row[] = array('data'=>qtyToString($row['item_id'], $row['quantity']), 'align'=> 'left');
    $data_row[] = array('data'=>to_quantity($row['reorder_level']), 'align'=> 'left');

    $tabular_data[] = $data_row;

}

$data = array(
    "title" => lang('reports_low_inventory_report'),
    "subtitle" => '',
    "headers" => $model->getDataColumns(),
    "data" => $tabular_data,
    "summary_data" => $model->getSummaryData(),
    "export_excel" => $export_excel,
    "pagination" => $this->pagination->create_links(),
);

$this->load->view("reports/tabular",$data);
}


function detailed_sales($start_date, $end_date, $sale_type, $export_excel=0, $offset = 0)
{
  $this->load->model('Sale');

  $start_date=rawurldecode($start_date);
  $end_date=rawurldecode($end_date);

  $this->load->model('reports/Detailed_sales');
  $model = $this->Detailed_sales;
  $model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel' => $export_excel));

  $this->Sale->create_sales_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));


  $headers = $model->getDataColumns();
  $commission_data = $model->get_commission_by_sale();
  $report_data = $model->getData();
  $summary_data = array();
  $summary_commission = array();
  $details_data = array();

  $details_data_row_for_excel    = array();
  $summary_data_row_export_excel = array();

		# phân trang
  $config['base_url'] = site_url("reports/detailed_sales/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$sale_type/$export_excel");
  $config['total_rows'] = $model->getTotalRows();
  $config['per_page'] = $arrParam['paginator']['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
  $config['uri_segment'] = 7;
  $config['use_page_numbers'] = TRUE;
  $this->load->library("pagination");
  $this->pagination->initialize($config);


  $location_count = count(Report::get_selected_location_ids());

  foreach($report_data['summary'] as $key=>$row)
  {

    $tien_chiet_khau = 0;
    $profit          = 0;
			# ------------------------------------------------------------------------- #
									#  Detail data row
			# ------------------------------------------------------------------------- #

    foreach($report_data['details'][$key] as $drow)
    {
        if ($drow['item_name'] !=  'Giảm giá') {
           $details_data_row = array();

           $details_data_row[] = array('data'=>isset($drow['item_number']) ? $drow['item_number'] : $drow['item_kit_number'], 'align'=>'left');
           $details_data_row[] = array('data'=>isset($drow['item_product_id']) ? $drow['item_product_id'] : $drow['item_kit_product_id'], 'align'=>'left');
           $details_data_row[] = array('data'=>isset($drow['item_name']) ? $drow['item_name'] : $drow['item_kit_name'], 'align'=>'left');
           $details_data_row[] = array('data'=>$drow['category'], 'align'=>'left');
           $details_data_row[] = array('data'=>$drow['size'], 'align'=>'left');
           $details_data_row[] = array('data'=>$drow['supplier_name']. ' ('.$drow['supplier_id'].')', 'align'=>'left');
           $details_data_row[] = array('data'=>$drow['serialnumber'], 'align'=>'left');
           $details_data_row[] = array('data'=>$drow['description'], 'align'=>'left');
           $details_data_row[] = array('data'=>to_currency($drow['current_selling_price']), 'align'=>'left');

           if( $drow['measure_qty'] && $drow['measure_name'] ) {
            $details_data_row[] = array('data'=>to_quantity($drow['measure_qty']), 'align'=>'left');
            $details_data_row[] = array('data'=>$drow['measure_name'], 'align'=>'left');
        } else {
            $details_data_row[] = array('data'=>to_quantity($drow['quantity_purchased']), 'align'=>'left');
            $this->load->model('Item');
            $details_data_row[] = array('data'=> $this->Item->getMeasureName($drow['item_id']), 'align'=>'left');
        }

        $details_data_row[] = array('data'=>to_currency($drow['subtotal']), 'align'=>'right');
        $details_data_row[] = array('data'=>to_currency($drow['total']), 'align'=>'right');
        $details_data_row[] = array('data'=>to_currency($drow['tax']), 'align'=>'right');
        
        if($this->has_profit_permission)
        {
                        $details_data_row[] = array('data'=>to_currency($drow['profit']), 'align'=>'right'); # Lợi nhuận theo từng đơn hàng
                    }

                    $details_data_row[] = array('data'=>$drow['discount_percent'].'%', 'align'=>'left');
                    $details_data[$key][] = $details_data_row;
                    
                    // Detail data row for exporting excel
                    //----------------------------------

                    $details_data_row_for_excel_temp['item_number']     = isset($drow['item_number']) ? $drow['item_number'] : $drow['item_kit_number'];
                    $details_data_row_for_excel_temp['item_product_id'] = isset($drow['item_product_id']) ? $drow['item_product_id'] : $drow['item_kit_product_id'];
                    $details_data_row_for_excel_temp['item_name']       = isset($drow['item_name']) ? $drow['item_name'] : $drow['item_kit_name'];
                    $details_data_row_for_excel_temp['category']        = $drow['category'];
                    $details_data_row_for_excel_temp['size']            = $drow['size'];
                    $details_data_row_for_excel_temp['supplier_name']   = $drow['supplier_name']. ' ('.$drow['supplier_id'].')';
                    $details_data_row_for_excel_temp['serialnumber']    = $drow['serialnumber'];
                    $details_data_row_for_excel_temp['description']     = $drow['description'];
                    $details_data_row_for_excel_temp['current_selling_price'] = to_currency($drow['current_selling_price']);
                    
                    if( $drow['measure_qty'] && $drow['measure_name'] ) {
                        $details_data_row_for_excel_temp['measure_qty']   = to_quantity($drow['measure_qty']);
                        $details_data_row_for_excel_temp['measure_name']  = $drow['measure_name'];
                    } 
                    else 
                    {
                        $details_data_row_for_excel_temp['quantity_purchased'] = to_quantity($drow['quantity_purchased']);
                        $this->load->model('Item');
                        $details_data_row_for_excel_temp['item_id'] = $this->Item->getMeasureName($drow['item_id']);
                    }
                    
                    $details_data_row_for_excel_temp['subtotal']  = to_currency($drow['subtotal']);
                    $details_data_row_for_excel_temp['total']     = to_currency($drow['total']);
                    $details_data_row_for_excel_temp['tax']       = to_currency($drow['tax']);

                    if($this->has_profit_permission)
                    {
                        $details_data_row_for_excel_temp['profit'] = to_currency($drow['profit']);
                        $profit += $drow['profit'];
                    }

                    $details_data_row_for_excel_temp['discount_percent'] = $drow['discount_percent'].'%';

                    $details_data_row_for_excel[$key][] = $details_data_row_for_excel_temp;
                    //----------------------------------------
                    // END detail data row for exporting excel
                }
                else {
                    # Nếu có giảm giá thì tính thêm vào tiền chiết khấu
                    $tien_chiet_khau = $drow['subtotal'];
                    $details_data_discount[$key][] = array(
                        array('data'=>lang('common_discount'), 'align'=>'right'),
                        array('data'=>to_currency(-$drow['subtotal']), 'align'=>'right'),
                    );
                }
            }
			# ------------------------------------------------------------------------- #
									#  Detail data row
			# ------------------------------------------------------------------------- #


			# ------------------------------------------------------------------------- #
									#  Tổng đơn hàng
			# ------------------------------------------------------------------------- #
            $summary_data_row = array();

            $link = site_url('reports/specific_customer/'.$start_date.'/'.$end_date.'/'.$row['customer_id'].'/all/0');
			//summary data row for normal view
            $summary_data_row[] = array('data'=>anchor('sales/receipt/'.$row['sale_id'], '<i class="ion-printer"></i>', array('target' => '_blank', 'class'=>'hidden-print')).'<span class="visible-print">'.$row['sale_id'].'</span>'.anchor('sales/edit/'.$row['sale_id'], '<i class="ion-document-text"></i>', array('target' => '_blank')).' '.anchor('sales/edit/'.$row['sale_id'], lang('common_edit').' '.$row['sale_id'], array('target' => '_blank','class'=>'hidden-print')), 'align'=>'left');

            if ($location_count > 1)
            {
                $summary_data_row[] = array('data'=>$row['location_name'], 'align' => 'left');
            }

            $summary_data_row[] = array('data'=>date(get_date_format().'-'.get_time_format(), strtotime($row['sale_time'])), 'align'=>'left');
            $summary_data_row[] = array('data'=>$row['employee_name'].($row['sold_by_employee'] && $row['sold_by_employee'] != $row['employee_name'] ? '/'. $row['sold_by_employee']: ''), 'align'=>'left');
            $summary_data_row[] = array('data'=>'<a href="'.$link.'" target="_blank">'.$row['customer_name'].(isset($row['phone_number']) && $row['phone_number'] ? ' ('.$row['phone_number'].')' : '').'</a>', 'align'=>'left');
			# số lượng
            $summary_data_row[] = array('data'=>count( $details_data[$key]), 'align'=>'center');



			$summary_data_row[] = array('data'=>to_currency($row['subtotal']-$tien_chiet_khau), 'align'=>'right'); # giá trị đơn hàng



			$summary_data_row[] = array('data'=>to_currency($row['tien_chiet_khau']-$tien_chiet_khau), 'align'=>'right');

			$summary_data_row[] = array('data'=>to_currency($row['tax']), 'align'=>'right'); # thuế
			if($this->has_profit_permission)
			{
				$summary_data_row[] = array('data'=>to_currency($row['total']), 'align'=>'right'); # doanh thu
                $summary_data_row[] = array('data'=>to_currency($row['LOI_NHUAN'] + $tien_chiet_khau), 'align'=>'right');
                $summary_data_row[] = array('data'=>to_currency($commission_data[$row['sale_id']]), 'align'=>'right'); # commission
				$summary_data_row[] = array('data'=>to_currency($row['LOI_NHUAN'] + $tien_chiet_khau - $commission_data[$row['sale_id']]), 'align'=>'right'); # lợi nhuận

			}
            $summary_commission[] = $commission_data[$row['sale_id']];
            $thuc_thu = explode(':', $row['payment_type']);
            $tam = array();
            $how_many = count($thuc_thu);
            $tong_thuc_thu = 0;

			# Tách ra giá tiền từ chuỗi
            if($how_many>1){
                for($i = 0; $i < $how_many-1; $i = $i + 2){
                   $tam[trim($thuc_thu[$i])] = $thuc_thu[$i+1];
               }

               $tong_thuc_thu = 0;

               foreach ($tam as $k => $value) {
                   if($k == 'Tiền mặt' || $k == 'Chuyển khoản') {
                      $tien = explode('V', $value);
                      $thuc_thu_rp = trim(str_replace(',', '', $tien[0]));
                      $tong_thuc_thu += $thuc_thu_rp;
                  }
              }
          }


          $summary_data_row[] = array('data'=>to_currency($tong_thuc_thu), 'align'=>'right');
          $summary_data_row[] = array('data'=>$row['payment_type'], 'align'=>'right');

          $summary_data[$key] = $summary_data_row;
			# hiển thị thêm chi phí

          $details_data['chi_phi_list'][$key] = $this->specific_cus_order_detail($row['sale_id']);

          $listExpensesOfAllSale['expenses'][$key]  = $this->Expense->get_item(array('sale_id'=>$row['sale_id'],'deleted' =>true));

			// Summary data row for exporting excel
			//----------------------------------

          $summary_data_row_export_excel[$key] = array();
          if ($location_count > 1)
          {
            $summary_data_row_export_excel[$key]['location_name'] = $row['location_name'];
        }

        $summary_data_row_export_excel[$key]['sale_time']       = date(get_date_format().'-'.get_time_format(), strtotime($row['sale_time']));
        $summary_data_row_export_excel[$key]['employee_name']   = $row['employee_name'].($row['sold_by_employee'] && $row['sold_by_employee'] != $row['employee_name'] ? '/'. $row['sold_by_employee']: '');
        $summary_data_row_export_excel[$key]['customer_info']   = $row['customer_name'].(isset($row['phone_number']) && $row['phone_number'] ? ' ('.$row['phone_number'].')' : '');
			# số lượng
        $summary_data_row_export_excel[$key]['quantity'] = round($row['items_purchased'],2);

        $summary_data_row_export_excel[$key]['subtotal']        = to_currency($row['subtotal']);
			$summary_data_row_export_excel[$key]['total_discount']  = to_currency($row['tien_chiet_khau']);# tổng tiền chiết khấu

			$summary_data_row_export_excel[$key]['tax']             = to_currency($row['tax']); # thuế

			if($this->has_profit_permission)
			{
				$summary_data_row_export_excel[$key]['revenue'] = to_currency($row['total']); # doanh thu
                $summary_data_row_export_excel[$key]['profit_before_charging_commission'] = to_currency($row['LOI_NHUAN'] + $row['tien_chiet_khau']); #loi nhuan truoc hoa hong
                $summary_data_row_export_excel[$key]['commission'] = to_currency($commission_data[$row['sale_id']]); #hoa hong
				$summary_data_row_export_excel[$key]['profit']        = to_currency($row['LOI_NHUAN'] + $row['tien_chiet_khau'] - $commission_data[$row['sale_id']]); # lợi nhuận

			}

			$summary_data_row_export_excel[$key]['actually_collected']     = to_currency($tong_thuc_thu);
			$summary_data_row_export_excel[$key]['payment_type']    = str_replace('<br />',', ',$row['payment_type']);


			# ------------------------------------------------------------------------- #
								#  Tổng đơn hàng
			# ------------------------------------------------------------------------- #
			
		}
		

		
		if($export_excel == 0)
		{
			$tong_bao_cao  = $model->getSummaryData();
            $tong_bao_cao['profit_before_charging_commission'] = $report_data['sum']['tong_loi_nhuan'];	
            $tong_bao_cao['summary_commission'] = array_sum($summary_commission);
            $tong_bao_cao['profit'] = $report_data['sum']['tong_loi_nhuan'] - array_sum($summary_commission);;            			            
            $tong_bao_cao['actually_collected'] = $report_data['sum']['tong_thuc_thu']; 			
            $tong_bao_cao['total_orders'] = $report_data['sum']['tong_don_hang'];	
            $data = array(
                "title" =>lang('reports_detailed_sales_report'),
                "subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
                "headers" => $model->getDataColumns(),
                "summary_data" => $summary_data,
                "details_data" => $details_data,
                "overall_summary_data" => $tong_bao_cao,
                "export_excel" => $export_excel,
                "details_data_discount"=>!empty($details_data_discount)? $details_data_discount : array(),
                "pagination" => $this->pagination->create_links(),

            );
            $this->load->view("reports/tabular_details",$data);
        }
        else
        {
         $header_of_child_col_name      = [];
         $more_header_of_child_col_name = [];
         $header_of_col_name            = [];

		// Expense of detail data row excel
		//---------------------------------
         $moreChildDataExcel = [];
         foreach($listExpensesOfAllSale['expenses'] as $key=>$row)
         {
            foreach($row as $Sale_id => $expense_value){
               $moreChildDataExcel['expenses'][$key][] = array(
                  'expense_decription' => $expense_value['expense_description'],
                  'expense_type'       => ($expense_value['expense_type']==1)?lang('reports_expense_type_1'):lang('reports_expense_type_2'),
                  'expense_tax'        => to_currency($expense_value['expense_tax']),
                  'expense_amount'     => to_currency($expense_value['expense_amount'])
              );
           }
       }
		//---------------------------------
		// END Expense of detail data row excel


		$headerOfBodyField[] = '__AUTO__'; #STT
		if ($location_count > 1)
		{
			$headerOfBodyField[] = 'location_name'; #ten kho
		}		
		$headerOfBodyField[] = 'sale_time'; #ngay thang
		$headerOfBodyField[] = 'employee_name'; #ten nhan vien
		$headerOfBodyField[] = 'customer_info'; #thong tin khach hang
		$headerOfBodyField[] = 'quantity'; #so luong 
		$headerOfBodyField[] = 'subtotal'; #
		$headerOfBodyField[] = 'total_discount'; #tong chiet khau
		$headerOfBodyField[] = 'tax'; #thue		
		if($this->has_profit_permission)
		{
			$headerOfBodyField[] = 'revenue'; #doanh thu
            $headerOfBodyField[] = 'profit_before_charging_commission'; #loi nhuan truoc hoa hong
            $headerOfBodyField[] = 'commission'; #hoa hong
			$headerOfBodyField[] = 'profit'; # lợi nhuận
		}
		$headerOfBodyField[] = 'actually_collected'; #thuc thu
		$headerOfBodyField[] = 'payment_type'; #phuong thuc thanh thanh toan
		
		$col = 'A';
		for($i = 0; $i< count($headers['summary']);$i++)
		{
			$headerOfBody[] = array('col' => $col,'value_field' =>$headerOfBodyField[$i]);
			$col++;

		}
     $fieldOfChildBody = array(

       array('col' => 'C','value_field' =>'item_product_id'),
       array('col' => 'D','value_field' =>'item_name'),
       array('col' => 'E','value_field' =>'category'),
       array('col' => 'F','value_field' =>'size'),
       array('col' => 'G','value_field' =>'supplier_name'),
       array('col' => 'H','value_field' =>'serialnumber'),
       array('col' => 'I','value_field' =>'description'),
       array('col' => 'J','value_field' =>'current_selling_price'),
       array('col' => 'K','value_field' =>'measure_qty'),
       array('col' => 'L','value_field' =>'measure_name'),
       array('col' => 'M','value_field' =>'subtotal'),
       array('col' => 'N','value_field' =>'tax'),
       array('col' => 'O','value_field' =>'discount_percent'),
       array('col' => 'P','value_field' =>'total'),
   );
     if($this->has_profit_permission)
     {
       $fieldOfChildBody[] = array('col' => 'Q','value_field' =>'profit');
   }

   $moreFieldOfChildBody['expenses'] = array(
      array('col' => 'B:D','value_field' =>'expense_decription'),
      array('col' => 'E','value_field' =>'expense_type'),
      array('col' => 'F','value_field' =>'expense_amount'),
      array('col' => 'G','value_field' =>'expense_tax')
  );

			//Header of parent Col
   $excelColumn = 'A';
   foreach($headers['summary'] as $key => $header_detail)
   {
    if($header_detail['data'] == lang('reports_sale_id')) $header_detail['data'] = 'STT';
    $header_of_col_name[] = array('col' =>$excelColumn,'text' => $header_detail['data'],'styles'=>array('bold' =>true,'font'=>true, 'font_size'=>14,'is_fill'=>true,'color'=>'98d9da'));
    $excelColumn++;
}
			//header of File
$header_of_multicol[] = array('mergeStartCol' =>'A','mergeEndCol'=>$excelColumn,'text' =>lang('reports_sales_details'),'styles'=>array('bold' =>true,'font'=>true, 'font_size'=>26,'is_fill'=>true,'color'=>'98d9da'));

$excelColumn = 'B';
			//Header of child Column
foreach($headers['details'] as $key => $header_detail)
{
    $header_of_child_col_name[] = array('col' =>$excelColumn,'text' => $header_detail['data'],'styles'=>array('font'=>true, 'font_size'=>12,'is_fill'=>true,'color'=>'e7e8e8'));
    $excelColumn++;
}
			//Header of more_child Column
$more_header_of_child_col_name['expenses'][] = array('col' =>'B:D','text' => $headers['expenses'][0]['data'],'styles'=>array('font'=>true, 'font_size'=>12,'is_fill'=>true,'color'=>'e7e8e8'));
$more_header_of_child_col_name['expenses'][] = array('col' =>'E','text' => $headers['expenses'][1]['data'],'styles'=>array('font'=>true, 'font_size'=>12,'is_fill'=>true,'color'=>'e7e8e8'));
$more_header_of_child_col_name['expenses'][] = array('col' =>'F','text' => $headers['expenses'][2]['data'],'styles'=>array('font'=>true, 'font_size'=>12,'is_fill'=>true,'color'=>'e7e8e8'));
$more_header_of_child_col_name['expenses'][] = array('col' =>'G','text' => $headers['expenses'][3]['data'],'styles'=>array('font'=>true, 'font_size'=>12,'is_fill'=>true,'color'=>'e7e8e8'));

$bizExcel = new BizExcel('report_specific_supplier.xlsx');
$bizExcel->setNumberRowBeginRow(1)->setHeaderOfMultiCol($header_of_multicol);
$bizExcel->setNumberRowStartBody(2)->setHeaderOfBody($headerOfBody);
$bizExcel->setFieldOfChildBody($fieldOfChildBody)->setMoreFieldOfChildBody($moreFieldOfChildBody);
$bizExcel->setHeaderOfCol($header_of_col_name);
$bizExcel->setDataExcel($summary_data_row_export_excel);
$bizExcel->setHeaderOfChildCol($header_of_child_col_name);
$bizExcel->setHeaderOfMoreChildCol($more_header_of_child_col_name);
$bizExcel->setChildDataExcel($details_data_row_for_excel)->setMoreChildDataExcel($moreChildDataExcel);
$bizExcel->setMoreChildData(true);
$excelContent = $bizExcel->generateFile(false);
$this->load->helper('download');
force_download(lang('reports_sales_details').date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)).'.xlsx', $excelContent);
exit;
}

}
function closeout($date,$type = 0) {


  $this->load->model('Sale');
  $this->load->model('reports/Closeout');
  $model = $this->Closeout;
  $location_id = $this->Employee->get_logged_in_employee_current_location_id();

  $where = "((s.suspended = 1 AND s.was_layaway = 1) OR s.suspended = 0 OR s.is_vat = 1) AND s.store_account_payment = 0  AND s.sale_time >= '$date 00:00:00' AND s.sale_time <= '$date 23:59:59' AND s.location_id = $location_id AND s.deleted = 0";
  $this->Sale->create_sales_items_temp_table_n9(array('where'=>$where));

  $where = "r.receiving_time >= '$date 00:00:00' AND r.receiving_time <= '$date 23:59:59' AND r.location_id = $location_id AND r.deleted = 0";
  $this->Receiving->create_receivings_items_temp_table_n9(array('where'=>$where));

  $params = array('date'=>$date, 'location_id'=>$location_id);
  $report_list = $model->create_report($params);

  $data['date']        = $date;
  $data['prev_date']  = date('Y-m-d', strtotime($date .' -1 day'));
  $data['next_date']  = date('Y-m-d', strtotime($date .' +1 day'));

  $data['report_list'] = $report_list;
  if($type == 1) $this->closeout_excel($date);
  else $this->load->view("reports/closeout",$data);
}


function closeout_excel($date) {


    $this->load->model('Sale');
    $this->load->model('reports/Closeout');
    $model = $this->Closeout;
    $location_id = $this->Employee->get_logged_in_employee_current_location_id();

    $where = "((s.suspended = 1 AND s.was_layaway = 1) OR s.suspended = 0 OR s.is_vat = 1) AND s.store_account_payment = 0  AND s.sale_time >= '$date 00:00:00' AND s.sale_time <= '$date 23:59:59' AND s.location_id = $location_id AND s.deleted = 0";
    $this->Sale->create_sales_items_temp_table_n9(array('where'=>$where));

    $where = "r.receiving_time >= '$date 00:00:00' AND r.receiving_time <= '$date 23:59:59' AND r.location_id = $location_id AND r.deleted = 0";
    $this->Receiving->create_receivings_items_temp_table_n9(array('where'=>$where));

    $params = array('date'=>$date, 'location_id'=>$location_id);
    $report_list = $model->create_report($params);

    $this->load->helper('n9excel');

    require_once (APPPATH.'libraries/PHPExcel/PHPExcel.php');
    require_once (APPPATH.'libraries/PHPExcel/PHPExcel/IOFactory.php');
    require_once (APPPATH.'libraries/PHPExcel/PHPEXCHelper.php');

    $company_name    = $this->config->item('company');
    $company_address = nl2br($this->Location->get_info_for_key('address'));

    $date_covert = date('d-m-Y', strtotime($date));

    $date_covert = "Từ $date_covert 00:00 đến $date_covert 23:59";

    $helpExport = new HelpFuncExportExcel ();
    $objReader = PHPExcel_IOFactory::createReader ( "Excel5" );
    $colIndex = '';
    $rowIndex = 0;
    $objPHPExcel = new PHPExcel ();
    $sheet = $objPHPExcel->getActiveSheet ();

    $sheet->getColumnDimension ( 'A' )->setWidth ( 50 );
    $sheet->getColumnDimension ( 'B' )->setWidth ( 50 );

    $sheet->mergeCellsByColumnAndRow(0, 1, 1, 1);
    $sheet->setCellValue('A1', $company_name);
    $helpExport->setStyle_13_TNR_B_L($sheet, 'A1', 'B1');
    $sheet->getRowDimension(1)->setRowHeight(24.75);

    $sheet->mergeCellsByColumnAndRow(0, 2, 1, 2);
    $sheet->setCellValue('A2', $company_address);
    $helpExport->setStyle_13_TNR_N_L($sheet, 'A2', 'B2');

    $title = 'Báo cáo kết thúc ngày làm việc';
    $sheet->mergeCellsByColumnAndRow(0, 4, 1, 4);
    $sheet->setCellValue('A4', $title);
    $helpExport->setStyle_16_TNR_B_C($sheet, 'A4', 'B4');
    $sheet->getRowDimension(4)->setRowHeight(24);

    $sheet->mergeCellsByColumnAndRow(0, 5, 1, 5);
    $sheet->setCellValue('A5', $date_covert);
    $helpExport->setStyle_11_TNR_I_C($sheet, 'A5', 'B5');

    $sheet->setCellValue('A7', 'Mô tả');
    $sheet->setCellValue('B7', 'Dữ liệu');
    $helpExport->setStyle_13_TNR_B_C($sheet, 'A7', 'B7');

    $sheet->getRowDimension(7)->setRowHeight(22);

    $current_row = 7;
    $i = 8;
    foreach($report_list as $val) {
        if($val['right'] == '--') {
            $sheet->mergeCellsByColumnAndRow(0, $i, 1, $i);
            $sheet->setCellValue('A'.$i, mb_strtoupper(strip_tags($val['left']), 'utf-8'));
            $helpExport->setStyle_13_TNR_B_L($sheet, 'A'.$i, 'A'.$i);
        }elseif($val['right'] == '&nbsp') {
            $sheet->setCellValue('A'.$i, '');
            $sheet->setCellValue('B'.$i, '');
        }else {
            $left  = str_replace("&nbsp"," ",$val['left']);
            $right = str_replace("&nbsp"," ",$val['right']);

            $sheet->setCellValue('A'.$i, $left);
            $sheet->setCellValue('B'.$i, $right);
            $helpExport->setStyle_13_TNR_N_L($sheet, 'A'.$i, 'B'.$i);
        }

        $sheet->getRowDimension($i)->setRowHeight(22);

        $i++;
    }

    $sheet->getStyle ( 'A' . $current_row . ':' . 'B' . ($i-1) )->getBorders ()->getOutline ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );
    $sheet->getStyle ( 'A' . $current_row . ':' . 'B' . ($i-1) )->getBorders ()->getInside ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );

        ////set dinh dang giay a4 cho ban in ra////////////
    $objPHPExcel->getActiveSheet ()->getPageSetup ()->setOrientation ( PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE );
    $objPHPExcel->getActiveSheet ()->getPageSetup ()->setPaperSize ( PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4 );
    $objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 10);

    $pageMargin = $sheet->getPageMargins ();
    $pageMargin->setTop ( .5 );
    $pageMargin->setLeft ( .15 );
    $pageMargin->setRight ( .05 );
        ////////////////////////////////////////////////////
    header ( 'Content-Type: application/vnd.ms-excel' );
    header ( 'Content-Disposition: attachment;filename="bao_cao_ket_ngay(' . date ( "d/m/Y" ) . ').xls"' );
    header ( 'Cache-Control: max-age=0' );
    $objWriter = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel5' );
    $objWriter->save ( 'php://output' );
}



function detailed_receivings($start_date, $end_date, $supplier_id,$sale_type, $export_excel=0, $offset=0)
{
  $this->load->model('Receiving');

  $start_date=rawurldecode($start_date);
  $end_date=rawurldecode($end_date);
  $this->load->model('reports/Detailed_receivings');
  $model = $this->Detailed_receivings;
  $model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel' => $export_excel, 'supplier_id' => $supplier_id));

  $this->Receiving->create_receivings_items_temp_table(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));



  $headers = $model->getDataColumns();
  $report_data = $model->getData();
  $summary_data = array();
  $details_data = array();
  $details_data_discount = [];
  $_values_body_sumary     = array();
  $_values_body_detail     = array();
  $listExpensesOfReceiving = array();

  $moreChildDataExcel      = [];

  $location_count = count(Report::get_selected_location_ids());
  foreach($report_data['summary'] as $key=>$row)
  {
     $trans = $this->Receiving->getTransactions($row['receiving_id'], $row['supplier_id']);
     $htmlPaymentTypes = '';
     $comment          = '';
     $comment_tran     = false;
     $listExpensesOfThisReceiving = $this->Expense->get_item(array('receiving_id'=>$row['receiving_id'],'deleted' =>true));
     if(!empty($listExpensesOfThisReceiving)){
      $listExpensesOfReceiving[$key]	 =   $listExpensesOfThisReceiving;
  }
  foreach ($trans as $tran) {
    if($tran['payment_type'] != lang('common_refund_from_supplier') )
    {
       $htmlPaymentTypes .= $tran['payment_type'] . ': ' . to_currency($tran['transaction_amount']).'<br>';
   }
   else
   {
       $comment_tran = true;
       $comment .=  $tran['payment_type'] . ': ' . to_currency(-$tran['transaction_amount']) .'<br>';
   }
}

$summary_data[$key] = array(
   array('data'=>
      anchor('receivings/receipt/'.$row['receiving_id'], '<i class="ion-printer"></i>', array('target' => '_blank')).' '.
      anchor('receivings/edit/'.$row['receiving_id'], '<i class="ion-document-text"></i>', array('target' => '_blank')).' '.

      '<a href="receivings/edit_modal/'.$row['receiving_id'].'" class="edit" data-target="#sua_xoa_don_nhap_hang" data-toggle="modal"> '.lang('common_edit').' '.$row['receiving_id'].' </a>'.' / '.

      anchor('items/generate_barcodes_from_recv/'.$row['receiving_id'], lang('common_barcode_sheet'), array('target' => '_blank')).' / '.
      anchor('items/generate_barcodes_labels_from_recv/'.$row['receiving_id'], lang('common_barcode_labels'), array('target' => '_blank')).']', 'align'=> 'left'),


   array('data'=>date(get_date_format(), strtotime($row['receiving_date'])), 'align'=> 'left'), 
   array('data'=> $row['employee_name'], 'align'=> 'left'), 
   array('data'=> $row['supplier_name'], 'align'=> 'left'), 
   array('data'=> to_currency($row['subtotal']), 'align'=> 'right'),
   array('data'=> to_currency($row['tax']), 'align'=> 'right'),
   array('data'=> to_currency($row['total']), 'align'=> 'right'),
   array('data'=> !empty($htmlPaymentTypes) ? $htmlPaymentTypes : $row['payment_type'], 'align'=> 'left'),					
   array('data'=> $comment_tran?$comment:$row['comment'], 'align'=> 'left'));
$_values_body_sumary[$key] = array(
  'receiving_date' => date(get_date_format(), strtotime($row['receiving_date'])),
  'employee_name'  => $row['employee_name'],
  'supplier_name'  => $row['supplier_name'],
  'subtotal'       => to_currency($row['subtotal']), 
  'tax'            => to_currency($row['tax']),
  'total'          => to_currency($row['total']),
  'payment_type'   => !empty($htmlPaymentTypes) ? str_replace('<br>','',$htmlPaymentTypes) : $row['payment_type'],
  'comment'        => str_replace('<br>','',$comment_tran?$comment:$row['comment'])
);
if ($location_count > 1)
{
    array_unshift($summary_data[$key], array('data'=>$row['location_name'], 'align'=> 'left'));
}

foreach($report_data['details'][$key] as $drow) {   
    if ($drow['product_id'] != 'Giảm giá') {
        if($drow['measure_id']) {
            $this->load->model('Measure');
            $measure = $this->Measure->getInfo($drow['measure_id']);
            $details_data[$key][] = array(
                array('data'=>isset($drow['name'])?$drow['name']:'', 'align'=> 'left'),
                array('data'=>$drow['product_id'], 'align'=> 'left'),
                array('data'=>$drow['category'], 'align'=> 'left'),
                array('data'=>$drow['size'], 'align'=> 'left'),
                array('data'=>to_quantity($drow['measure_qty']), 'align'=> 'left'),
                            // array('data'=>to_quantity($drow['measure_qty_received']), 'align'=> 'left'),
                array('data'=> isset($measure->name)?$measure->name:'', 'align'=> 'left'),
                array('data'=>to_currency($drow['subtotal']), 'align'=> 'right'),
                array('data'=>to_currency($drow['tax']), 'align'=> 'right'),
                array('data'=>to_currency($drow['total']), 'align'=> 'right'),
                array('data'=>$drow['discount_percent'].'%', 'align'=> 'left')
            );
            $_values_body_detail[$key][] = array(
                'name'             => isset($drow['name'])?$drow['name']:'',
                'product_id'       => $drow['product_id'],
                'category'         => $drow['category'],
                'size'             => $drow['size'],
                'measure_qty'      => to_quantity($drow['measure_qty']),
                'measure_name'     => isset($measure->name)?$measure->name:'',
                'subtotal'         => to_currency($drow['subtotal']),
                'tax'              => to_currency($drow['tax']),
                'total'            => to_currency($drow['total']),
                'discount_percent' => $drow['discount_percent'].'%'
            );

        } else {
            $this->load->model('Item');
            $details_data[$key][] = array(
                array('data'=>$drow['name'], 'align'=> 'left'),
                array('data'=>$drow['product_id'], 'align'=> 'left'),
                array('data'=>$drow['category'], 'align'=> 'left'),
                array('data'=>$drow['size'], 'align'=> 'left'),
                array('data'=>to_quantity($drow['quantity_purchased']), 'align'=> 'left'),
                array('data'=>$this->Item->getMeasureName($drow['item_id']), 'align'=> 'left'),
                array('data'=>to_currency($drow['subtotal']), 'align'=> 'right'),
                array('data'=>to_currency($drow['tax']), 'align'=> 'right'),
                array('data'=>to_currency($drow['total']), 'align'=> 'right'),
                array('data'=>$drow['discount_percent'].'%', 'align'=> 'left')
            );
            $_values_body_detail[$key][] = array(
                'name'             => isset($drow['name'])?$drow['name']:'',
                'product_id'       => $drow['product_id'],
                'category'         => $drow['category'],
                'size'             => $drow['size'],
                'measure_qty'      => to_quantity($drow['measure_qty']),
                'measure_name'     => $this->Item->getMeasureName($drow['item_id']),
                'subtotal'         => to_currency($drow['subtotal']),
                'tax'              => to_currency($drow['tax']),
                'total'            => to_currency($drow['total']),
                'discount_percent' => $drow['discount_percent'].'%'
            );
        }
    }
    else {
        $details_data_discount[$key][] = array(
            array('data'=>lang('reports_discount_all'), 'align'=> 'left'),
            array('data'=>to_currency(-$drow['total']), 'align'=> 'right')
        );
        $moreChildDataExcel['discount'][$key][] = array(
            'product_id'  => lang('reports_discount_all'),
            'total'       => to_currency(-$drow['total']));
    }   


}

}       

if($export_excel == 0)
{
 $config = array();
 $config['base_url'] = site_url("reports/detailed_receivings/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$supplier_id/$sale_type/$export_excel");
 $config['total_rows'] = $model->getTotalRows();

 $config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
 $config['uri_segment'] = 8;
 $this->load->library('pagination');$this->pagination->initialize($config);
 $data = array(
   "title" =>lang('reports_detailed_receivings_report'),
   "subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
   "headers" => $model->getDataColumns(),
   "summary_data" => $summary_data,
   "details_data" => $details_data,
   "overall_summary_data" => $model->getSummaryData(),
   "export_excel" => $export_excel,
   "listExpensesOfReceiving" =>$listExpensesOfReceiving,
   "details_data_discount" =>$details_data_discount,
   "pagination" => $this->pagination->create_links(),
);
 $this->load->view("reports/tabular_details",$data);
}
else
{
 $header_of_child_col_name      = [];
 $more_header_of_child_col_name = [];
 $header_of_col_name            = [];

		// Expense of detail data row excel
		//---------------------------------

 foreach($listExpensesOfReceiving as $key=>$row)
 {
    foreach($row as $Receiving_id => $expense_value){
       $moreChildDataExcel['expenses'][$key][] = array(
          'expense_decription' => $expense_value['expense_description'],
          'expense_type'       => ($expense_value['expense_type']==1)?lang('reports_expense_type_1'):lang('reports_expense_type_2'),
          'expense_tax'        => to_currency($expense_value['expense_tax']),
          'expense_amount'     => to_currency($expense_value['expense_amount'])
      );
   }
}
		//---------------------------------
		// END Expense of detail data row excel
$headerOfBodyField[] = '__AUTO__';
if ($location_count > 1) {
 $headerOfBodyField[] = 'location_name';
}		
$headerOfBodyField[] = 'receiving_date';
$headerOfBodyField[] = 'employee_name';
$headerOfBodyField[] = 'supplier_name';
$headerOfBodyField[] = 'subtotal';
$headerOfBodyField[] = 'total';
$headerOfBodyField[] = 'tax';
$headerOfBodyField[] = 'payment_type';
$headerOfBodyField[] = 'comment';
$headers['summary'][0] = 'STT';
$col = 'A';
for($i = 0; $i< count($headers['summary']);$i++) {
 $headerOfBody[] = array('col' => $col,'value_field' =>$headerOfBodyField[$i]);
 $col++;	
}
$fieldOfChildBody = array(

   array('col' => 'B','value_field' =>'name'),
   array('col' => 'C','value_field' =>'product_id'),
   array('col' => 'D','value_field' =>'category'),
   array('col' => 'E','value_field' =>'size'),
   array('col' => 'F','value_field' =>'measure_qty'),
   array('col' => 'G','value_field' =>'measure_name'),
   array('col' => 'H','value_field' =>'subtotal'),

   array('col' => 'I','value_field' =>'tax'),
   array('col' => 'J','value_field' =>'total'),       
   array('col' => 'K','value_field' =>'discount_percent')
);

$moreFieldOfChildBody['expenses'] = array(
 array('col' => 'B:D','value_field' =>'expense_decription'),
 array('col' => 'E','value_field' =>'expense_type'),
 array('col' => 'F','value_field' =>'expense_amount'),
 array('col' => 'G','value_field' =>'expense_tax')
);
$moreFieldOfChildBody['discount'] = array(
 array('col' => 'B:D','value_field' =>'product_id'),
 array('col' => 'E','value_field' =>'total'),
);

			//Header of parent Col
$excelColumn = 'A';
foreach($headers['summary'] as $key => $header_detail) {
    if($header_detail['data'] == lang('reports_sale_id')) $header_detail['data'] = 'STT';
    $header_of_col_name[] = array('col' =>$excelColumn,'text' => $header_detail['data'],'styles'=>array('bold' =>true,'font'=>true, 'font_size'=>14,'is_fill'=>true,'color'=>'98d9da'));
    $excelColumn++;
}

			//header of File
$locations_name = $this->Location->get_items(Report::get_selected_location_ids());
$locationsNameExcel = ''; 
foreach($locations_name as $location_key=>$location_name) {
    $locationsNameExcel = $location_name -> name;
}

$company_name    = $this->config->item('company');
$header_of_multicol[] = array('mergeStartCol' =>'A4', 'mergeEndCol'=>$excelColumn.'4', 'text' =>lang('reports_detailed_receiving'), 'styles'=>array('bold' =>true,'font'=>true, 'font_size'=>26));
$header_of_multicol[] = array('mergeStartCol' =>'A1','mergeEndCol'=>'C1','text' =>$company_name,'styles'=>array('bold' =>true,'font'=>true, 'font_size'=>12));
$header_of_multicol[] = array('mergeStartCol' =>'A2','mergeEndCol'=>'B2','text' =>$locationsNameExcel,'styles'=>array('bold' =>false,'font'=>true, 'font_size'=>12));
$header_of_multicol[] = array('mergeStartCol' =>'A5','mergeEndCol'=>$excelColumn.'5','text' =>lang('reports_date_range').': '.date('d-m-Y', strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),'styles'=>array('bold' =>false,'font'=>true, 'font_size'=>12));


$excelColumn = 'B';
			//Header of child Column
foreach($headers['details'] as $key => $header_detail) {
    $header_of_child_col_name[] = array('col' =>$excelColumn,'text' => $header_detail['data'],'styles'=>array('font'=>true, 'font_size'=>12,'is_fill'=>true,'color'=>'e7e8e8'));
    $excelColumn++;
}
			//Header of more_child Column
$more_header_of_child_col_name['expenses'][] = array('col' =>'B:D','text' => $headers['expenses'][0]['data'],'styles'=>array('font'=>true, 'font_size'=>12,'is_fill'=>true,'color'=>'e7e8e8'));
$more_header_of_child_col_name['expenses'][] = array('col' =>'E','text' => $headers['expenses'][1]['data'],'styles'=>array('font'=>true, 'font_size'=>12,'is_fill'=>true,'color'=>'e7e8e8'));
$more_header_of_child_col_name['expenses'][] = array('col' =>'F','text' => $headers['expenses'][2]['data'],'styles'=>array('font'=>true, 'font_size'=>12,'is_fill'=>true,'color'=>'e7e8e8'));
$more_header_of_child_col_name['expenses'][] = array('col' =>'G','text' => $headers['expenses'][3]['data'],'styles'=>array('font'=>true, 'font_size'=>12,'is_fill'=>true,'color'=>'e7e8e8'));
$more_header_of_child_col_name['discount'][] = array('col' =>'B:D','text' =>lang('common_discount'),'styles'=>array('font'=>true, 'font_size'=>12,'is_fill'=>true,'color'=>'e7e8e8'));
$more_header_of_child_col_name['discount'][] = array('col' =>'E:G','text' =>lang('reports_total'),'styles'=>array('font'=>true, 'font_size'=>12,'is_fill'=>true,'color'=>'e7e8e8'));

$bizExcel = new BizExcel('report_specific_supplier.xlsx');
$bizExcel->setHeaderOfMultiCol($header_of_multicol);
$bizExcel->setNumberRowStartBody(7)->setHeaderOfBody($headerOfBody);
$bizExcel->setFieldOfChildBody($fieldOfChildBody)->setMoreFieldOfChildBody($moreFieldOfChildBody);
$bizExcel->setHeaderOfCol($header_of_col_name);
$bizExcel->setDataExcel($_values_body_sumary);
$bizExcel->setHeaderOfChildCol($header_of_child_col_name);
$bizExcel->setHeaderOfMoreChildCol($more_header_of_child_col_name);
$bizExcel->setChildDataExcel($_values_body_detail)->setMoreChildDataExcel($moreChildDataExcel);
$bizExcel->setMoreChildData(true);
$excelContent = $bizExcel->generateFile(false);
$this->load->helper('download');
force_download(lang('reports_detailed_receiving').date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)).'.xlsx', $excelContent);
exit;
}
}

function detailed_count_report($start_date, $end_date, $export_excel=0, $offset = 0)
{


  $start_date=rawurldecode($start_date);
  $end_date=rawurldecode($end_date);

  $this->load->model('reports/Detailed_inventory_count_report');
  $model = $this->Detailed_inventory_count_report;
  $model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'offset' => $offset, 'export_excel' => $export_excel));

  $config = array();
  $config['base_url'] = site_url("reports/detailed_count_report/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$export_excel");
  $config['total_rows'] = $model->getTotalRows();
  $config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
  $config['uri_segment'] = 6;
  $this->load->library('pagination');$this->pagination->initialize($config);

  $headers = $model->getDataColumns();
  $report_data = $model->getData();

  $summary_data = array();
  $details_data = array();
  $location_count = count(Report::get_selected_location_ids());

  foreach($report_data['summary'] as $key=>$row)
  {
     $status = '';
     switch($row['status'])
     {
        case 'open':
        $status = lang('common_open');
        break;

        case 'closed':
        $status = lang('common_closed');
        break;
    }

    $totalQtyCount = 0;
    foreach($report_data['details'][$key] as $drow)
    {
        $details_data_row = array();
        $details_data_row[] = array('data'=>$drow['item_number'], 'align'=>'left');
        $details_data_row[] = array('data'=>$drow['product_id'], 'align'=>'left');
        $details_data_row[] = array('data'=>$drow['name'], 'align'=>'left');
        $details_data_row[] = array('data'=>$drow['category'], 'align'=>'left');
        $details_data_row[] = array('data'=>$drow['size'], 'align'=>'left');
        $details_data_row[] = array('data'=>to_quantity($drow['count']), 'align'=>'left');
        $details_data_row[] = array('data'=>to_quantity($drow['actual_quantity']), 'align'=>'left');
        $details_data_row[] = array('data'=>$drow['comment'], 'align'=>'left');
        $details_data[$key][] = $details_data_row;
        $totalQtyCount += $drow['count'];
    }

    $summary_data_row = array(
       array('data'=>date(get_date_format().' '.get_time_format(), strtotime($row['count_date'])), 'align'=>'left'),
       array('data'=>$status, 'align'=>'left'),
       array('data'=>$row['employee_name'], 'align'=>'left'),
       array('data'=>to_quantity($row['items_counted']), 'align'=>'left'),
       array('data'=>to_quantity($totalQtyCount), 'align'=>'left'),
       array('data'=>to_quantity($row['difference']), 'align'=>'left'),
       array('data'=>$row['comment'], 'align'=>'left'),
   );

    if ($location_count > 1)
    {
        array_unshift($summary_data_row, array('data'=>$row['location_name'], 'align'=>'left'));
    }
    $summary_data[$key] = $summary_data_row;
}
$data = array(
    "title" =>lang('reports_detailed_count_report'),
    "subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
    "headers" => $model->getDataColumns(),
    "summary_data" => $summary_data,
    "details_data" => $details_data,
    "overall_summary_data" => $model->getSummaryData(),
    "export_excel" => $export_excel,
    "pagination" => $this->pagination->create_links(),
);
$this->load->view("reports/tabular_details", $data);
}

	 #---------------------------------------------------------------------------------------------------#





    					# BÁO CÁO ThU CHI CHI TIẾT KHÁCH HÀNG
    					# BÁO CÁO ThU CHI CHI TIẾT KHÁCH HÀNG
    					# BÁO CÁO ThU CHI CHI TIẾT KHÁCH HÀNG
    					# BÁO CÁO ThU CHI CHI TIẾT KHÁCH HÀNG
    					# BÁO CÁO ThU CHI CHI TIẾT KHÁCH HÀNG
    					# BÁO CÁO ThU CHI CHI TIẾT KHÁCH HÀNG
    					# BÁO CÁO ThU CHI CHI TIẾT KHÁCH HÀNG




    #---------------------------------------------------------------------------------------------------#


function bao_cao_thu_chi_chi_tiet_khach_hang() {


    $this->load->model('reports/Specific_customer_store_account');
    $model = $this->Specific_customer_store_account;

    $data['action'] = $this->uri->segment(2);
    $data['locations'] = $this->Location->list_item();
    $data['title'] = 'Báo cáo thu chi chi tiết khách hàng';

    if(isset($_SESSION['bao_cao_thu_chi_chi_tiet_khach_hang'])) {
       $data['url_print'] = $_SESSION['bao_cao_thu_chi_chi_tiet_khach_hang']['url_print'];
       $data['filter']    = $_SESSION['bao_cao_thu_chi_chi_tiet_khach_hang'];


       if($data['filter']['export_excel'] == 1)
          redirect($data['url_print']);
      else
          $this->load->view("reports/bao_cao_thu_chi_chi_tiet_khach_hang",$data);

  }else{
    $data['inputs'] = array('input_date_range','select_customers');
    $data['customer_id'] = $this->input->get('cus_id', -1);
    $this->load->view("reports/n9_tabular",$data);
}
}

function bao_cao_thu_chi_chi_tiet_khach_hang_store(){
    $arrParam = $_SESSION['bao_cao_thu_chi_chi_tiet_khach_hang'];
       	// $arrParam['location_ids'] = $arrParam['selected_location_ids'][0];

    unset($_SESSION['bao_cao_thu_chi_chi_tiet_khach_hang']);
    if(!empty($arrParam)) {
     $this->load->model('reports/Summary_expenses');


     $arrParam['paginator']             = $this->_paginator;
     $arrParam['page']                  =  $this->uri->segment(3, 1);

     $config['base_url'] = base_url() . 'reports/bao_cao_thu_chi_chi_tiet_khach_hang_store';

     $config['per_page'] = $arrParam['paginator']['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;

     $config['uri_segment'] = 3;
     $config['use_page_numbers'] = TRUE;

     $model = $this->Summary_expenses;
     $tong_thu = 0;
     $tong_chi = 0;
     $items = $model->lay_danh_sach_thu_chi_khach_hang_theo_don_hang($arrParam);
            # Đếm số kết quả
     $config['total_rows'] = count($items);
     foreach ($items as $key => $value) {
       $tong_thu += $value['tien_thu'];
       $tong_chi += $value['tien_chi'];
   }

            # Chuyển đổi nợ đầu vào nợ cuối thành chuỗi

   $tong_thu = number_format($tong_thu,1);
   $tong_chi = number_format($tong_chi,1);


   $html = $this->load->view('reports/table/thu_chi_chi_tiet_khach_hang', array('items'=>$items), true);
            // var_dump($html);
            // die;
   $this->load->library("pagination");
   $this->pagination->initialize($config);
   $this->pagination->createConfig('front-end');

   $pagination = $this->pagination->create_ajax();

   $result = array('count'=> $config['total_rows'], 'html_string'=>$html, 'pagination'=>$pagination,'debt_start'=>$tong_thu, 'debt_end'=>$tong_chi);
            // var_dump($result);
   echo json_encode($result);
}
}

function bao_cao_thu_chi_chi_tiet_khach_hang_excel() {

    $arrParam = $this->input->get();

    unset($_SESSION['bao_cao_thu_chi_chi_tiet_khach_hang']);

    $this->load->model('reports/Summary_expenses');
        #-------------------------------------------------------------------------------------------------#

        # Biến hien_thi có cấu trúc C:D:E với C:D là 2 trường cần merge E là trường vị trí hiển thị dữ liệu

        #-------------------------------------------------------------------------------------------------#
    $thong_tin_diem_ban_hang = $this->Location->get_info($_GET['location_ids'])->name;
    $ten_cong_ty = $this->config->item('company');
        # đầu trang

    $_title = array(
     array('value_field' => 'title','dong_nao'=>4,'hien_thi'=>'A:H:A'),
     array('value_field' => 'date','dong_nao'=>5,'hien_thi'=>'A:H:A'),
     array('value_field' => 'ten_khach_hang','dong_nao'=>6,'hien_thi'=>'A:D:A'),
     array('value_field' => 'thong_tin_diem_ban_hang','dong_nao'=>2,'hien_thi'=>'A:D:A'),
     array('value_field' => 'ten_cong_ty','dong_nao'=>1,'hien_thi'=>'A:D:A'),
 );

    $_headers 	 = array(
     array('col' => 'A','value_field' => '__AUTO__'),
     array('col' => 'B','value_field' => 'expense_date'),
     array('col' => 'C','value_field' => 'id_don_hang'),
     array('col' => 'D','value_field' => 'expense_description'),
     array('col' => 'E','value_field' => 'nhan_vien'),
     array('col' => 'F','value_field' => 'nhan_vien_phe_duyet'),
     array('col' => 'G','value_field' => 'tien_thu'),
     array('col' => 'H','value_field' => 'tien_chi'),
 );		

    $_footer = array(
			# phần tổng cuối cùng
     array('sum' => 'G','value_field' => 'SUM','hien_thi'=>'G:G:G'),
     array('sum' => 'H','value_field' => 'SUM','hien_thi'=>'H:H:H'),
     array('sum' => 'F','value_field' => 'SUM','ten_truong'=>'Tổng: ','hien_thi'=>'A:F:A'),

 );


		# dữ liệu truyền ra	
    $model = $this->Summary_expenses;
    $items = $model->lay_danh_sach_thu_chi_khach_hang_theo_don_hang($arrParam);


    $title = 'BÁO CÁO THU CHI CHI TIẾT KHÁCH HÀNG';
    if(empty($arrParams['start_date'])) $date = 'Toàn bộ thời gian'; else $date = 'Từ : '.$arrParams['start_date'].' đến : '.$arrParams['end_date'];
    foreach ($items as $value) {
     $result[] = array(
        'expense_date' 					=>  $value['expense_date'],
        'id_don_hang' 					=> 	$value['id_don_hang'],
        'expense_description' 			=>  $value['expense_description'],
        'nhan_vien'     				=>  $value['nhan_vien'],
        'nhan_vien_phe_duyet' 		    =>  $value['nhan_vien_phe_duyet'],
        'tien_thu' 		    			=>  $value['tien_thu'],
        'tien_chi' 		    			=>  $value['tien_chi'],
        'date'							=>	$date,
        'ten_khach_hang'				=>  'Tên khách hàng : '.$value['ten_khach_hang'],
        'thong_tin_diem_ban_hang' 		=>  $thong_tin_diem_ban_hang,
        'ten_cong_ty'   				=>  $ten_cong_ty,
        'title'         				=> 	$title,
    );
 }
 $bizExcel = new BizExcel('bao_cao_thu_chi_chi_tiet_khach_hang.xlsx');
 $bizExcel->Row_title($_title);
 $bizExcel->setNumberRowStartBody(8)->setHeaderOfBody($_headers);
 $bizExcel->RowEndBody_theo_tung_cot($_footer);
 $bizExcel->tat_auto_size(false);
 $bizExcel->setDataExcel($result);

 $excelContent = $bizExcel->generateFile(false);
 $this->load->helper('download');
 force_download('bao_cao_thu_chi_chi_tiet_khach_hang.xlsx', $excelContent);
 exit;
}
    #---------------------------------------------------------------------------------------------------#





    					# BÁO CÁO ThU CHI CHI TIẾT NHÀ CUNG CẤP
    					# BÁO CÁO ThU CHI CHI TIẾT NHÀ CUNG CẤP
    					# BÁO CÁO ThU CHI CHI TIẾT NHÀ CUNG CẤP
    					# BÁO CÁO ThU CHI CHI TIẾT NHÀ CUNG CẤP
    					# BÁO CÁO ThU CHI CHI TIẾT NHÀ CUNG CẤP
    					# BÁO CÁO ThU CHI CHI TIẾT NHÀ CUNG CẤP
    					# BÁO CÁO ThU CHI CHI TIẾT NHÀ CUNG CẤP




    #---------------------------------------------------------------------------------------------------#


function bao_cao_thu_chi_chi_tiet_nha_cung_cap() {


    $this->load->model('reports/Specific_customer_store_account');
    $model = $this->Specific_customer_store_account;

    $data['action'] = $this->uri->segment(2);
    $data['locations'] = $this->Location->list_item();
    $data['title'] = 'Báo cáo thu chi chi tiết nhà cung cấp';

    if(isset($_SESSION['bao_cao_thu_chi_chi_tiet_nha_cung_cap'])) {
       $data['url_print'] = $_SESSION['bao_cao_thu_chi_chi_tiet_nha_cung_cap']['url_print'];
       $data['filter']    = $_SESSION['bao_cao_thu_chi_chi_tiet_nha_cung_cap'];


       if($data['filter']['export_excel'] == 1)
          redirect($data['url_print']);
      else
          $this->load->view("reports/bao_cao_thu_chi_chi_tiet_nha_cung_cap",$data);

  }else{
    $data['inputs'] = array('input_date_range','select_suppliers');
    $data['supplier_id'] = $this->input->get('supp_id', -1);
    $this->load->view("reports/n9_tabular",$data);
}
}

function bao_cao_thu_chi_chi_tiet_nha_cung_cap_store(){
    $arrParam = $_SESSION['bao_cao_thu_chi_chi_tiet_nha_cung_cap'];
    unset($_SESSION['bao_cao_thu_chi_chi_tiet_nha_cung_cap']);

    if(!empty($arrParam)) {
     $this->load->model('reports/Summary_expenses');


     $arrParam['paginator']             = $this->_paginator;
     $arrParam['page']                  =  $this->uri->segment(3, 1);

     $config['base_url'] = base_url() . 'reports/bao_cao_thu_chi_chi_tiet_nha_cung_cap_store';


     $config['per_page'] = $arrParam['paginator']['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;

     $config['uri_segment'] = 3;
     $config['use_page_numbers'] = TRUE;

     $model = $this->Summary_expenses;
     $tong_thu = 0;
     $tong_chi = 0;
     $items = $model->lay_danh_sach_thu_chi_nha_cung_cap_theo_don_hang($arrParam);
            # Đếm số kết quả
     $config['total_rows'] = count($items);

     foreach ($items as $key => $value) {
       $tong_thu += $value['tien_thu'];
       $tong_chi += $value['tien_chi'];
   }

            # Chuyển đổi nợ đầu vào nợ cuối thành chuỗi

   $tong_thu = number_format($tong_thu,1);
   $tong_chi = number_format($tong_chi,1);


   $html = $this->load->view('reports/table/thu_chi_chi_tiet_nha_cung_cap', array('items'=>$items), true);
            // var_dump($html);
            // die;
   $this->load->library("pagination");
   $this->pagination->initialize($config);
   $this->pagination->createConfig('front-end');

   $pagination = $this->pagination->create_ajax();

   $result = array('count'=> $config['total_rows'], 'html_string'=>$html, 'pagination'=>$pagination,'debt_start'=>$tong_thu, 'debt_end'=>$tong_chi);
            // var_dump($result);
   echo json_encode($result);
}
}

function bao_cao_thu_chi_chi_tiet_nha_cung_cap_excel() {

    $arrParam = $this->input->get();

    unset($_SESSION['bao_cao_thu_chi_chi_tiet_nha_cung_cap']);

    $this->load->model('reports/Summary_expenses');
        #-------------------------------------------------------------------------------------------------#

        # Biến hien_thi có cấu trúc C:D:E với C:D là 2 trường cần merge E là trường vị trí hiển thị dữ liệu

        #-------------------------------------------------------------------------------------------------#
    $thong_tin_diem_ban_hang = $this->Location->get_info($_GET['location_ids'])->name;
    $ten_cong_ty = $this->config->item('company');
        # đầu trang

    $_title = array(
     array('value_field' => 'title','dong_nao'=>4,'hien_thi'=>'A:H:A'),
     array('value_field' => 'date','dong_nao'=>5,'hien_thi'=>'A:H:A'),
     array('value_field' => 'ten_nha_cung_cap','dong_nao'=>6,'hien_thi'=>'A:D:A'),
     array('value_field' => 'thong_tin_diem_ban_hang','dong_nao'=>2,'hien_thi'=>'A:D:A'),
     array('value_field' => 'ten_cong_ty','dong_nao'=>1,'hien_thi'=>'A:D:A'),
 );

    $_headers 	 = array(
     array('col' => 'A','value_field' => '__AUTO__'),
     array('col' => 'B','value_field' => 'expense_date'),
     array('col' => 'C','value_field' => 'id_don_hang'),
     array('col' => 'D','value_field' => 'expense_description'),
     array('col' => 'E','value_field' => 'nhan_vien'),
     array('col' => 'F','value_field' => 'nhan_vien_phe_duyet'),
     array('col' => 'G','value_field' => 'tien_thu'),
     array('col' => 'H','value_field' => 'tien_chi'),
 );		

    $_footer = array(
			# phần tổng cuối cùng
     array('sum' => 'G','value_field' => 'SUM','hien_thi'=>'G:G:G'),
     array('sum' => 'H','value_field' => 'SUM','hien_thi'=>'H:H:H'),
     array('sum' => 'F','value_field' => 'SUM','ten_truong'=>'Tổng: ','hien_thi'=>'A:F:A'),

 );



		# dữ liệu truyền ra	
    $model = $this->Summary_expenses;
    $items = $model->lay_danh_sach_thu_chi_nha_cung_cap_theo_don_hang($arrParam);


    $title = 'BÁO CÁO THU CHI CHI TIẾT NHÀ CUNG CẤP';
    if(empty($arrParams['start_date'])) $date = 'Toàn bộ thời gian'; else $date = 'Từ : '.$arrParams['start_date'].' đến : '.$arrParams['end_date'];
    foreach ($items as $value) {
     $result[] = array(
        'expense_date' 					=>  $value['expense_date'],
        'id_don_hang' 					=> 	$value['id_don_hang'],
        'expense_description' 			=>  $value['expense_description'],
        'nhan_vien'     				=>  $value['nhan_vien'],
        'nhan_vien_phe_duyet' 		    =>  $value['nhan_vien_phe_duyet'],
        'tien_thu' 		    			=>  $value['tien_thu'],
        'tien_chi' 		    			=>  $value['tien_chi'],
        'date'							=>	$date,
        'ten_nha_cung_cap'				=>  'Tên nhà cung cấp : '.$value['ten_nha_cung_cap'],
        'thong_tin_diem_ban_hang' 		=>  $thong_tin_diem_ban_hang,
        'ten_cong_ty'   				=>  $ten_cong_ty,
        'title'         				=> 	$title,
    );
 }
 $bizExcel = new BizExcel('bao_cao_thu_chi_chi_tiet_khach_hang.xlsx');
 $bizExcel->Row_title($_title);
 $bizExcel->setNumberRowStartBody(8)->setHeaderOfBody($_headers);
 $bizExcel->RowEndBody_theo_tung_cot($_footer);
 $bizExcel->tat_auto_size(false);
 $bizExcel->setDataExcel($result);

 $excelContent = $bizExcel->generateFile(false);
 $this->load->helper('download');
 force_download('bao_cao_thu_chi_chi_tiet_nha_cung_cap.xlsx', $excelContent);
 exit;
}
     #---------------------------------------------------------------------------------------------------#





    					# BÁO CÁO ThU CHI nội bộ
    					# BÁO CÁO ThU CHI nội bộ
    					# BÁO CÁO ThU CHI nội bộ
    					# BÁO CÁO ThU CHI nội bộ
    					# BÁO CÁO ThU CHI nội bộ
    					# BÁO CÁO ThU CHI nội bộ
    					# BÁO CÁO ThU CHI nội bộ




    #---------------------------------------------------------------------------------------------------#


function bao_cao_tong_hop_thu_chi_noi_bo() {


    $this->load->model('reports/Specific_customer_store_account');
    $model = $this->Specific_customer_store_account;

    $data['action'] = $this->uri->segment(2);
    $data['locations'] = $this->Location->list_item();
    $data['title'] = ' Báo cáo thu chi chi tiết nội bộ';
    if(isset($_SESSION['bao_cao_tong_hop_thu_chi_noi_bo'])) {
       $data['url_print'] = $_SESSION['bao_cao_tong_hop_thu_chi_noi_bo']['url_print'];
       $data['filter']    = $_SESSION['bao_cao_tong_hop_thu_chi_noi_bo'];

       if($data['filter']['export_excel'] == 1)
          redirect($data['url_print']);
      else
          $this->load->view("reports/bao_cao_tong_hop_thu_chi_noi_bo",$data);

  }else{
    $data['inputs'] = array('input_date_range','input_locations');
    $this->load->view("reports/n9_tabular",$data);
}
}

function bao_cao_tong_hop_thu_chi_noi_bo_store(){
    $arrParam = $_SESSION['bao_cao_tong_hop_thu_chi_noi_bo'];

    unset($_SESSION['bao_cao_tong_hop_thu_chi_noi_bo']);

    $arrParam['location_ids'] = implode(',', $arrParam['selected_location_ids']);
    if(!empty($arrParam)) {
     $this->load->model('reports/Summary_expenses');


     $arrParam['paginator']             = $this->_paginator;
     $arrParam['page']                  =  $this->uri->segment(3, 1);

     $config['base_url'] = base_url() . 'reports/bao_cao_tong_hop_thu_chi_noi_bo_store';


     $config['per_page'] = $arrParam['paginator']['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;

     $config['uri_segment'] = 3;
     $config['use_page_numbers'] = TRUE;

     $model = $this->Summary_expenses;
     $tong_thu = 0;
     $tong_chi = 0;
     $items = $model->lay_danh_sach_thu_chi_noi_bo($arrParam);
            # Đếm số kết quả
     $config['total_rows'] = "".count($items)."";

     foreach ($items as $key => $value) {
       $tong_thu += $value['tien_thu'];
       $tong_chi += $value['tien_chi'];
   }

            # Chuyển đổi nợ đầu vào nợ cuối thành chuỗi

   $tong_thu = number_format($tong_thu,1);
   $tong_chi = number_format($tong_chi,1);


   $html = $this->load->view('reports/table/thu_chi_chi_tiet_noi_bo', array('items'=>$items), true);
            // var_dump($html);
            // die;
   $this->load->library("pagination");
   $this->pagination->initialize($config);
   $this->pagination->createConfig('front-end');

   $pagination = $this->pagination->create_ajax();

   $result = array('count'=> $config['total_rows'], 'html_string'=>$html, 'pagination'=>$pagination,'debt_start'=>$tong_thu, 'debt_end'=>$tong_chi);
            // var_dump($result);
   echo json_encode($result);
}
}

function bao_cao_tong_hop_thu_chi_noi_bo_excel() {

    $arrParam = $this->input->get();

    unset($_SESSION['bao_cao_tong_hop_thu_chi_noi_bo']);

    $this->load->model('reports/Summary_expenses');
        #-------------------------------------------------------------------------------------------------#

        # Biến hien_thi có cấu trúc C:D:E với C:D là 2 trường cần merge E là trường vị trí hiển thị dữ liệu

        #-------------------------------------------------------------------------------------------------#
    $dia_diem = explode(',', $_GET['location_ids']);
    $thong_tin_diem_ban_hang = '';
    foreach ($dia_diem as $key => $value) {
       $thong_tin_diem_ban_hang = $thong_tin_diem_ban_hang.'  '.$this->Location->get_info($value)->name;
   }
   $ten_cong_ty = $this->config->item('company');
        # đầu trang

   $_title = array(
     array('value_field' => 'title','dong_nao'=>4,'hien_thi'=>'A:H:A'),
     array('value_field' => 'date','dong_nao'=>5,'hien_thi'=>'A:H:A'),
     array('value_field' => 'thong_tin_diem_ban_hang','dong_nao'=>2,'hien_thi'=>'A:D:A'),
     array('value_field' => 'ten_cong_ty','dong_nao'=>1,'hien_thi'=>'A:D:A'),
 );

   $_headers 	 = array(
     array('col' => 'A','value_field' => 'so_don_hang'),
     array('col' => 'B','value_field' => 'expense_date'),
     array('col' => 'C','value_field' => 'expense_description'),
     array('col' => 'D','value_field' => 'expense_reason'),
     array('col' => 'E','value_field' => 'nhan_vien'),
     array('col' => 'F','value_field' => 'nhan_vien_phe_duyet'),
     array('col' => 'G','value_field' => 'tien_thu'),
     array('col' => 'H','value_field' => 'tien_chi'),
 );		

   $_footer = array(
			# phần tổng cuối cùng
     array('sum' => 'G','value_field' => 'SUM','hien_thi'=>'G:G:G'),
     array('sum' => 'H','value_field' => 'SUM','hien_thi'=>'H:H:H'),
     array('sum' => 'F','value_field' => 'SUM','ten_truong'=>'Tổng: ','hien_thi'=>'A:F:A'),

 );

   $_chuky = array(
			# phần tổng cuối cùng
     array('style'=>'in_nghieng','size1'=>9,'ten_truong'=>'Ngày ......../......../..........','hien_thi'=>'G:H'),
     array('style'=>'in_dam','size1'=>9,'size2'=>9,'chu_ky'=>'(Ký, ghi họ tên)','ten_truong'=>'GIÁM ĐỐC: ','hien_thi'=>'G:H'),
     array('style'=>'in_nghieng','size1'=>9,'size2'=>9,'chu_ky'=>'(Ký, ghi họ tên)','ten_truong'=>'KẾ TOÁN TRƯỞNG: ','hien_thi'=>'A:B'),
     array('style'=>'in_nghieng','size1'=>9,'size2'=>9,'chu_ky'=>'(Ký, ghi họ tên)','ten_truong'=>'THỦ QUỸ: ','hien_thi'=>'D:E'),

 );

		# dữ liệu truyền ra	
   $model = $this->Summary_expenses;
   $items = $model->lay_danh_sach_thu_chi_noi_bo($arrParam);


   $result = array();    
   $title = 'SỔ TIỀN THU - CHI';
   if(empty($arrParam['start_date'])) $date = 'Toàn bộ thời gian'; else $date = 'Từ : '.$arrParam['start_date'].' đến : '.$arrParam['end_date'];
   foreach ($items as $value) {
     $result[] = array(
        'expense_date' 					=>  $value['expense_date'],
        'so_don_hang' 					=> 	$value['id'],
        'expense_reason' 				=>  $value['expense_reason'],
        'expense_description' 			=>  $value['expense_description'],
        'nhan_vien'     				=>  $value['nhan_vien'],
        'nhan_vien_phe_duyet' 		    =>  $value['nhan_vien_phe_duyet'],
        'tien_thu' 		    			=>  $value['tien_thu'],
        'tien_chi' 		    			=>  $value['tien_chi'],
        'date'							=>	$date,
        'thong_tin_diem_ban_hang' 		=>  $thong_tin_diem_ban_hang,
        'ten_cong_ty'   				=>  $ten_cong_ty,
        'title'         				=> 	$title,
    );
 }
 $bizExcel = new BizExcel('bao_cao_tong_hop_thu_chi_noi_bo.xlsx');
 $bizExcel->Row_title($_title);
 $bizExcel->setNumberRowStartBody(8)->setHeaderOfBody($_headers);
 $bizExcel->RowEndBody_theo_tung_cot($_footer);
 $bizExcel->tat_auto_size(false);
 $bizExcel->setDataExcel($result);
 $bizExcel->Chu_ky_cuoi_bang($_chuky);

 $excelContent = $bizExcel->generateFile(false);
 $this->load->helper('download');
 force_download('bao_cao_tong_hop_thu_chi_noi_bo.xlsx', $excelContent);
 exit;
}



        #---------------------------------------------------------------------------------------------------#

        #                                    DEV 13 REPORT ON EMPLOYEE DETAILS                              #

        #---------------------------------------------------------------------------------------------------#






function specific_employees_d13($id = 0) {

    $data['id']= $this->uri->segment(3,1);
    // var_dump($this->scope_of_view);
    // var_dump($this->Employee->get_logged_in_employee_info()->id);
    // var_dump($data['id']);
    $error =true;
    if($this->scope_of_view=="view_scope_all"){
        $error =false;
        $data['location'] = $this->Location->getLocations();
        $data['list_employees'] = $this->Employee->get_list_employees_by_location();
        $data['view'] =1;
    }else if($this->scope_of_view=="view_scope_location"){
      $list = $this->Employee->get_list_employees_by_location($this->Employee->get_logged_in_employee_current_location_id());
      $data['location'] = $this->Location->getLocations($this->Employee->get_logged_in_employee_current_location_id());
      // echo $this->db->last_query();die();
      // var_dump($data['location']);die();
      $data['view'] =1;
      $data['list_employees'] = $list;
      $error = true;
      foreach ($list as $key => $value) {
        if($value['id']==$data['id'])
          $error =false;
      }
    }
    else{
      $data['view'] =0;
      $data['location'] =null;
      $data['list_employees'] =null;
      if($data['id']==$this->Employee->get_logged_in_employee_info()->id)
        $error =false;
    }
    if($error)
    {
      echo "Bạn không có quyền xem báo cáo của người này!";
      die();
    }
    $data['employee_id'] = $this->Employee->get_information($id);
    $data['info'] =$this->Employee->get_employee_by_id($id);
    $location_id = $this->Employee->get_logged_in_employee_current_location_id();
    $data['location_id'] = $location_id;
    $id = intval($data['id']);
         // echo $this->db->last_query();die();
    $data['list'] = $this->Employee->get_employee_data($id);
    $data['list_task'] = $this->Employee->get_employee_data($id,array(),'task');
     // echo json_encode($data['list']['series']);die();
    // $this->check_action_permission('view_employees');
    $data['tasks'] = $this->Task->get_list_project_by_id($id);
    // echo "<pre>";
    // var_dump($data['task']);die();
    $this->load->view('reports/specific_employees_v_d13',$data);


}

function employee_total_revenue()
{
    $id = $this->input->post('id') ? $this->input->post('id') :"" ;
    $location_id = $this->input->post('location_id') ? $this->input->post('location_id') :"" ;
    $start_date = $this->input->post('start_date') ? $this->input->post('start_date') :"" ;
    $end_date = $this->input->post('end_date') ? $this->input->post('end_date') : "";
    $result = $this->Employee->get_employee_total_revenue($id,$location_id,$start_date,$end_date);
    $result =  '<div class="info-seven primarybg-info">
    <div class="logo-seven hidden-print"><i class="ti-widget dark-info-primary"></i></div>
    <span class="total">'.round($result["total"]).'</span><span> VND</span>
    <p>Tổng giá trị doanh thu</p>
    </div><script type="text/javascript">
    $(".total").simpleMoneyFormat();

    </script>';
    echo $result;
}




# Ajax chọn theo năm và tháng
    #time theo tháng
    #time theo quý
    #time theo năm
function employee_graph_filter()
{

    $arrParam = $this->input->post();
    if(empty($arrParam))
        die();
    $id = $arrParam['id'];

    if(!empty($this->input->post('colleague')))
    {
        $co = $this->input->post('colleague');
        $result = $this->Employee->get_employee_data($co,$arrParam);
        $result['colleague'] = 'vivu';
    }
    else
    {
        if(empty($this->input->post('location')))
        {
            $result = $this->Employee->get_employee_data($id,$arrParam);
        }
        if(!empty($this->input->post('location')))
        {
            $result['location'] = $this->input->post('location');

                    // $result['series'] =array();
            $location = $this->input->post('location');
                    // var_dump($location);die();
            $employeess = $this->Employee->get_list_employees_by_location($location);
            $result['categories'] = $this->Employee->get_employee_data($id,$arrParam)['categories'];
            foreach ($employeess as $key => $employee) {
                        // $a = $this->get_employee_data($employee['id'],$arrParam)['series'];
                $result['series'][] = $this->Employee->get_employee_data($employee['id'],$arrParam)['series']; 
                         // $b = array_merge($b,$a);

            }
        }

    }
    
                // echo "<pre>";
                // var_dump($result);die();
                // var_dump($b);die();
    echo json_encode($result);
}
function employee_graph_filter_word($arrParam=null)
{
    if(empty($arrParam))
        die();
    $id = $arrParam['employee_id'];
    if(empty($arrParam['location_id']))
    {
        $result = $this->Employee->get_employee_data($id,$arrParam);
    }
    if(!empty($arrParam['location_id']))
    {
        $result['location'] = $arrParam['location_id'];

        $location = $arrParam['location_id'];
        $employeess = $this->Employee->get_list_employees_by_location($location);
        
        foreach ($employeess as $key => $employee) {
            $result['series'][] = $this->Employee->get_employee_data($employee['id'],$arrParam)['series']; 
            $result['categories'] = $this->Employee->get_employee_data($employee['id'],$arrParam)['categories'];  
        }
    }
    return $result;
}



    #Ajax cho số dự án 
public function employee_graph_filter_task()
{
  $arrParam = $this->input->post();
  if(empty($arrParam))
    die();
$id = $arrParam['id'];

if(!empty($this->input->post('colleague')))
{
    $co = $this->input->post('colleague');
    $result = $this->Employee->get_employee_data($co,$arrParam,'task');
    $result['colleague'] = 'vivu';
}
else
{

    if(empty($this->input->post('location')))
    {
        $result = $this->Employee->get_employee_data($id,$arrParam,'task');
    }
    if(!empty($this->input->post('location')))
    {
        $result['location'] = $this->input->post('location');

                    // $result['series'] =array();
        $location = $this->input->post('location');
                    // var_dump($location);die();
        $employeess = $this->Employee->get_list_employees_by_location($location);
        $result['categories'] = $this->Employee->get_employee_data($id,$arrParam,'task')['categories'];
        foreach ($employeess as $key => $employee) {
                        // $a = $this->get_employee_data($employee['id'],$arrParam)['series'];
            $result['series'][] = $this->Employee->get_employee_data($employee['id'],$arrParam,'task')['series']; 
                         // $b = array_merge($b,$a);

        }
    }

}

                // echo "<pre>";
                // var_dump($result);die();
                // var_dump($b);die();
echo json_encode($result);
}




       #Lấy sô dự án theo thời gian


# Đổi tháng ra quý
protected function convert_quater($t)
{

    if ($t <= 3) return "Quý I";
    if ($t <= 6) return "Quý II";
    if ($t <= 9) return "Quý III";

    return "Quý IV";
}

 #Lấy quý  
protected function get_quater($tm)

{
    if($tm<=3) return 1;
    if($tm<=6) return 2;
    if($tm<=9) return 3;
    return 4;
}



# Danh sách tất cả hợp đồng bên thứ 3
function supplier($id,$start_date="",$end_date="")
{
    $data['info'] = $this->Supplier->get_info_by_person_id($id);
    $suppliers = $this->Supplier->report_supplier($id);
    $contract_value_done = $this->Contract->get_contract_value(array('done','liquidated'));
    $contract_value = $this->Contract->get_contract_value();

    foreach ($suppliers as $key => $value)
    {
        $suppliers[$key]['total_value']="";
        foreach ($contract_value as $key2 => $value2) 
        {

            if($value['contract_id']==$value2['id'])
            {
                $suppliers[$key]['total_value'] = $value2['total_value'];
            }
        }
    }

    foreach ($suppliers as $key => $value)
    {
        $suppliers[$key]['total_value_done'] ="";
        foreach ($contract_value_done as $key2 => $value2) 
        {

            if($value['contract_id']==$value2['id'])
            {
                $suppliers[$key]['total_value_done'] = $value2['total_value'];
            }
        }
    }

    $data['supplier'] = $suppliers ;

    $this->load->view('reports/suppliers',$data);
}

function customer($id){

  $id = intval($id);
  $data['list'] = $this->Customer->so_hop_dong($id);
  // echo "<pre>"; print_r($data['list']); die();
  // echo $this->db->last_query();die();
  $this->load->view('reports/customer',$data);
}


}


?>
