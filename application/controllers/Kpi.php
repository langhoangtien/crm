<?php
require_once("Secure_area.php");
class Kpi extends Secure_area
{
    function __construct() {
        parent::__construct('kpi');
        $this->load->model('Contract');
        $this->load->model('Location');
        $this->load->model('Expense');
        
    }



    function total(){
        $this->check_action_permission('view_kpi_gobal');
        $this->load->view('kpi/global', $data);
    }



    function index()
    {
        //get year

        $moment = date('Y', time());
        $arrDate = [];
        for ($i = 10; $i >= 0; $i--) {
            $arrDate[$i] = $moment - $i + 5;
        }
        $data['arrDate'] = $arrDate;

        $this->load->view("kpi/index1", $data);
    }

    function sale_expense($year='',$key_filter='count_sale_expense')
    {
        $arrParam = array(
            "keywords"=>'',
            "page"=>'1',
            'year'=>$year,
            'key_filter_expense_date'=>'expense_date',
            "key_filter"=>$key_filter,
            "paginator"=>array('per_page'=>10000,'uri_segment'=>3),
        );
        $items = $this->Expense->full_item($arrParam);
        $data = array();
        foreach ($items as $k => $val) {
            $month = date("m",strtotime($val['expense_date']));
            if(!array_key_exists($val['e_location_id'],$data)){
                $data[$val['e_location_id']][1]=0;
                $data[$val['e_location_id']][2]=0;
                $data[$val['e_location_id']][3]=0;
                $data[$val['e_location_id']][4]=0;
                if($month>0&&$month<4)
                    $data[$val['e_location_id']][1]+=$val['expense_amount'];
                if($month>3&&$month<7)
                    $data[$val['e_location_id']][2]+=$val['expense_amount'];
                if($month>6&&$month<10)
                    $data[$val['e_location_id']][3]+=$val['expense_amount'];
                if($month>9&&$month<=12)
                    $data[$val['e_location_id']][4]+=$val['expense_amount'];
            }
            else{
                if($month>0&&$month<4)
                    $data[$val['e_location_id']][1]+=$val['expense_amount'];
                if($month>3&&$month<7)
                    $data[$val['e_location_id']][2]+=$val['expense_amount'];
                if($month>6&&$month<10)
                    $data[$val['e_location_id']][3]+=$val['expense_amount'];
                if($month>9&&$month<=12)
                    $data[$val['e_location_id']][4]+=$val['expense_amount'];
            }
        }
       // / var_dump($items);
        return $data;
    }
    function render_table()
    {
        $params = $this->input->post();
        $location = $this->Location->list_item();
        $view_data = $this->view_data($params['year']);
        $data_revenue_profit = $this->revenue_profit($params['year']);
//        $location = $this->db->select('location_id as id, name')->from('locations')->where('deleted = 0')->get()->result();
        // $report_contracts = $this->Contract->report_contracts_kpi($params['year']);

        $report_receiving = $this->Contract->report_receiving_kpi($params['year']);
        $data['sale_expense'] = $sale_expense;
        $data['sale_expense_1'] = $sale_expense_1;
        $data['view_data'] = $view_data;
        $data['data_revenue_profit'] = $data_revenue_profit;
        $data['report_receiving'] = $report_receiving;// chi phí bên thứ 3
        // var_dump($data_revenue_profit);
        // var_dump($report_receiving);
        $moment = date('Y', time());
        $arrDate = [];
        for ($i = 10; $i >= 0; $i--) {
            $arrDate[$i] = $moment - $i + 5;
        }
        $data['arrDate'] = $arrDate;
        if ($params['type'] == 0) {
            foreach ($location as $key) {
                $data['location'][$key['location_id']] = $key['name'];
            }
            if ($params['type_kpi'] == 'profit') {
                $data['kpi_profit'] = $this->db->select('*')
                ->from('kpi')
                ->where('kpi_type = "profit"')
                ->where('type = 0')
                ->where('year = ' . $params['year'])
                ->get()->result();
                $data1 = array();
                foreach ($data['kpi_profit'] as $key)
                {
                    $data1[$key->location_id] = ($key->data_kpi);
                }
                $data['kpi_profit_convert'] = $data1;

                $this->load->view("kpi/kpi_profit", $data);
            }
            if ($params['type_kpi'] == 'revenue') {
                $data['kpi_revenue'] = $this->db->select('*')
                ->from('kpi')
                ->where('kpi_type = "revenue"')
                ->where('type = 0')
                ->where('year = ' . $params['year'])
                ->get()->result();

                $data1 = array();
                foreach ($data['kpi_revenue'] as $key)
                {
                    $data1[$key->location_id] = ($key->data_kpi);
                }
                $data['kpi_revenue_convert'] = $data1;

                $this->load->view("kpi/kpi_revenue", $data);
            }

        } elseif ($params['type'] == 1) {

            foreach ($location as $key) {
                $data['location'][$key['location_id']] = $key['name'];
            }
            if ($params['type_table_general'] == 'revenue_profit_real') {
                $revenue_general = $this->Contract->report_contracts($params['year']);
                $sale_expense = ($this->sale_expense($params['year']));
                $sale_expense_1 = ($this->sale_expense($params['year'],'count_in_expense'));
                $data['kpi_revenue'] = $this->db->select('*')
                ->from('kpi')
                ->where('kpi_type = "revenue"')
                ->where('type = 0')
                ->where('year = ' . $params['year'])
                ->get()->result();
                $data1 = array();
                foreach ($data['kpi_revenue'] as $key)
                {
                    $data1[$key->location_id] = ($key->data_kpi);
                }
                $data['kpi_revenue_begin'] = $data1;

                $data['kpi_revenue_profit_convert'] = $revenue_general;

//                echo '<pre>';
//                print_r($data['kpi_revenue_begin']);
// //                print_r($revenue_general);
//                echo '</pre>';
//                die();
                $ktv = array();
                // var_dump($data_revenue_profit);
                foreach ($data_revenue_profit as $key => $value) {
                  foreach ($value as $k => $val) {
                    if($k==1){
                        $ktv[1]['revenue'] += $val['revenue'];
                        $ktv[1]['profit'] += $val['profit'];
                    }
                    if($k==2){
                        $ktv[2]['revenue'] += $val['revenue'];
                        $ktv[2]['profit'] += $val['profit'];
                    }
                    if($k==3){
                        $ktv[3]['revenue'] += $val['revenue'];
                        $ktv[3]['profit'] += $val['profit'];
                    }
                    if($k==4){
                        $ktv[4]['revenue'] += $val['revenue'];
                        $ktv[4]['profit'] += $val['profit'];
                    }
                }
            }
            $data['ktv']=$ktv;

            $this->load->view("kpi/general/revenue_profit", $data);
        } else if ($params['type_table_general'] == 'thkh_hdtv') {


            $this->load->view("kpi/general/thkh_hdtv", $data);
        } else if ($params['type_table_general'] == 'thkh') {


            $this->load->view("kpi/general/thkh", $data);
        }


    }


}


function saveDB()
{
        //get year
    $moment = date('Y', time());
    $arrDate = [];
    for ($i = 10; $i >= 0; $i--) {
        $arrDate[$i] = $moment - $i + 5;
    }
    $data['arrDate'] = $arrDate;

    if ($this->input->post()) {
        $params = $this->input->post();

        /*get location from database*/
//            $location = $this->db->select('id, name')->from('phppos_geographical_area')->get()->result();
        $location = $this->db->select('location_id as id, name')->from('locations')->where('deleted = 0')->get()->result();

        /*mang luu tru du lieu theo tung khu vuc*/
        $index = 0;
        $arrDataKpi = [];
        for ($k = 0; $k < count($location); $k++) {
            $arrKpiTemp = [];
            $j = 0;
            for ($i = $index; $i < $index + 8; $i++) {
                $arrKpiTemp[$j] = $params['arrParam'][$i];
                $j++;
            }
            $index += 8;
            $arrDataKpi[$k] = $arrKpiTemp;
        }

//            echo '<pre>';
//            print_r($arrDataKpi);
//            echo '</pre>';die();

        /*bat dau insert hoac update du lieu*/
        if ($params['type'] == 0) {
                //ke hoach
            $temp = 0;
            foreach ($location as $key) {
                $dataUpdate = [
                    'location_id' => $key->id,
                    'type' => 0,
                    'kpi_type' => $params['type_kpi'],
                    'year' => $params['year'],
                    'create_by' => $this->session->userdata['person_id'],
                    'data_kpi' => json_encode($arrDataKpi[$temp++]),
                ];
                if (
                    empty($this->db->select('*')
                        ->from('kpi')
                        ->where('location_id = ' . $key->id)
                        ->where('type = "0"')
                        ->where('kpi_type = "' . $params['type_kpi'] . '"')
                        ->where('year = ' . $params['year'])
                        ->get()->result())
                ) {
                    $this->db->insert('kpi', $dataUpdate);
                } else {
                    $this->db->where('location_id', $key->id);
                    $this->db->where('type = "0"');
                    $this->db->where('kpi_type = "' . $params['type_kpi'] . '"');
                    $this->db->where('year = ' . $params['year']);
                    $this->db->update('kpi', $dataUpdate);
                }
            }
            foreach ($location as $key) {
                $data['location'][$key->id] = $key->name;
            }
            if ($params['type_kpi'] == 'revenue') {
                $data['kpi_revenue'] = $this->db
                ->select('*')
                ->from('phppos_kpi')
                ->where('kpi_type = "revenue"')
                ->where('year = ' . $params['year'])
                ->get()->result();

                $data1 = array();
                foreach ($data['kpi_revenue'] as $key)
                {
                    $data1[$key->location_id] = ($key->data_kpi);
                }
                $data['kpi_revenue_convert'] = $data1;

                $this->load->view("kpi/kpi_revenue", $data);

            } else if ($params['type_kpi'] == 'profit') {
                $data['kpi_profit'] = $this->db
                ->select('*')
                ->from('phppos_kpi')
                ->where('kpi_type = "profit"')
                ->where('year = ' . $params['year'])
                ->get()->result();

                $data1 = array();
                foreach ($data['kpi_profit'] as $key)
                {
                    $data1[$key->location_id] = ($key->data_kpi);
                }
                $data['kpi_profit_convert'] = $data1;

                $this->load->view("kpi/kpi_profit", $data);
            }


        } else if ($params['type'] == 1) {
                //ket qua

        }
    }
}
function revenue_profit($year){
    $data =array();
    $data_report_contracts = array();
    $location = $this->Location->list_item();
        // var_dump( $location);
        //tổng doanh thu
    $report_contracts = $this->Contract->report_contracts_kpi($year);
    foreach ($report_contracts as $key => $value) {
        foreach ($value as $k => $val) {
            for ($i=1; $i <5 ; $i++) { 
                $data_report_contracts[$key][$i] += $val[$i];
            }

        }
    }  
        // var_dump($data_report_contracts)      ;
        //doanh thu chia cho phòng ban khác
    $report_expenses = $this->Contract->report_expenses_kpi($year,'','sale');
        // chi phí chung
    $report_expenses_1 = $this->Contract->report_expenses_kpi($year,'','other');
         //chi phí bên thứ 3
    $report_receiving = $this->Contract->report_receiving_kpi($year);

    foreach ($location as $k => $val) {
        for($i=1;$i<5;$i++){
            $data[$val['location_id']][$i]['revenue'] = $data_report_contracts[$val['location_id']][$i]-$report_expenses[$val['location_id']][$i];

            $data[$val['location_id']][$i]['profit'] = $data[$val['location_id']][$i]['revenue']-$report_receiving[$val['location_id']][$i]-$report_expenses_1[$val['location_id']][$i];
        }
    }
    return $data;
}
function view_data($year){
    $this->db->from('phppos_kpi');
    $this->db->where('year',$year);
    $query = $this->db->get();
    $row = $query->result_array();
    $data = array();
    foreach ($row as $k => $val) {
        $data[$val['location_id']][$val['kpi_type']] = json_decode($val['data_kpi'],true);
    }
    return  $data;
}


# Lấy dữ liệu 
function kpi_global(){
    $type = $this->input->post("type");
    $locations = $this->Location->list_item();
    $year = $this->input->post('year');
    $kpi = $this->input->post('kpi');

    if(empty($type))
        return;

    if(empty($year))
        return;
    if(empty($kpi))
        return;
        #Nếu là kê hoạch
    if($type=="plan"){
        $kpi = empty($kpi) ? "revenue" : $kpi;
        foreach ($locations as $key=>  $location) {
         $data[$key]['location_name'] = $location['name'];
         $data[$key]['location_id'] = $location['location_id'];
         $data[$key]['location_data'] = $this->Kpi_model->get_revenue_plan($year,$kpi,$location);
     }

     echo json_encode(array('tp'=>'plan','dt'=>$data));
     return;
 }

       # Nếu là kết quả

 if($type=="result"){
    $data = array();


    $i=0;
    foreach ($locations as $key => $value) {
       $data[$i]['location_name'] = $value['name'];
       $data[$i]['location_id'] = $value['location_id'];
       $data[$i]['location_data'] = $this->get_kpi_data($value['location_id'],$year);
       $data[$i]['kpi_vcb'] = $this->get_kpi($year,$value)['vcb'];
       $data[$i]['kpi_vcbs'] = $this->get_kpi($year,$value)['vcbs'];
       $i++;
   }



   $d =array();
   foreach ($data as $key => $value) {

    for ($k=1; $k <11 ; $k++) { 
        $d[$k] += $value['location_data'][$k];
    }


}
$data[$i]['location_name'] = "Khối tư vấn";
$data[$i]['location_id'] = 0;
$data[$i]['location_data'] = $d;


$vcbs  = array();


foreach ($data as $key => $value) {

    for ($k=1; $k <11 ; $k++) { 
        $vcbs[$k] += $value['kpi_vcbs'][$k];
    }


}

$data[$i]['kpi_vcbs'] = $vcbs;


$vcb  = array();


foreach ($data as $key => $value) {

    for ($k=1; $k <11 ; $k++) { 
        $vcb[$k] += $value['kpi_vcb'][$k];
    }


}

$data[$i]['kpi_vcb'] = $vcb;
switch ($kpi) {

             #DOANH THU/LỢI NHUẬN THỰC HIỆN
    case '1':


    foreach ($data as $key => &$value) {
        for ($t=1; $t <11 ; $t++) { 
           $value['location_data'][$t] = ( $value['location_data'][$t]);
       }

   }

   break;


                #TH/KH HĐTV VCBS GIAO
   case '2':


   foreach ($data as $key => $value) {
    $d1=array();
    for ($z=1; $z <11 ; $z++) { 
        $value['kpi_vcbs'][$z] = empty($value['kpi_vcbs'][$z]) ? 0: $value['kpi_vcbs'][$z];
        if($value['kpi_vcbs'][$z]==0){
            $d1[$z] ="0.00%";
        }
        else{
            $d1[$z] = number_format(round($value['location_data'][$z]/$value['kpi_vcbs'][$z]*100,2),2).'%';
        }
        
    }
    $data[$key]['location_data'] =$d1;
}


break;


case '3':



foreach ($data as $key => $value) {
    $d1=array();
    for ($z=1; $z <11 ; $z++) { 
        $value['kpi_vcb'][$z] = empty($value['kpi_vcb'][$z]) ? 1: $value['kpi_vcb'][$z];
        $d1[$z] = number_format(round($value['location_data'][$z]/$value['kpi_vcb'][$z]*100,2),2).'%';
    }
    $data[$key]['location_data'] =$d1;
}


break;

default:
return;
break;
}

echo json_encode(array('tp'=>'result','dt'=>$data));
return;
}

}

function get_kpi($year,$arrParam=null){
    $t1=0;
    $t2=0;
    $d1 = $this->Kpi_model->get_revenue_plan($year,"revenue",$arrParam,$option="get");
        // echo "<pre>";
        // var_dump($d1);die();
    $data =array();
    foreach ($d1 as $key => $value) {
        $data['vcbs'][$value['quater']] = $value['vcbs'];
        $data['vcb'][$value['quater']] = $value['vcb'];
    }

    for ($i=1; $i <5 ; $i++) { 
        $data['vcb'][$i] = empty($data['vcb'][$i]) ? 0 : $data['vcb'][$i];
        $data['vcb'][5] += $data['vcb'][$i];
        $data['vcbs'][$i] = empty($data['vcbs'][$i]) ? 0 : $data['vcbs'][$i];
        $data['vcbs'][5] += $data['vcbs'][$i];
    }


    $d2 = $this->Kpi_model->get_revenue_plan($year,"profit",$arrParam,$option="get");
    foreach ($d2 as $key => $value) {
       $data['vcbs'][$value['quater']+5] = $value['vcbs'];
       $data['vcb'][$value['quater']+5] = $value['vcb'];
   }


   for ($i=6; $i <10 ; $i++) { 
    $data['vcb'][$i] = empty($data['vcb'][$i]) ? 0 : $data['vcb'][$i];
    $data['vcb'][10] += $data['vcb'][$i];
    $data['vcbs'][$i] = empty($data['vcbs'][$i]) ? 0 : $data['vcbs'][$i];
    $data['vcbs'][10] += $data['vcbs'][$i];
}


ksort($data['vcb']);
ksort($data['vcbs']);

        // echo "<pre>";
        // var_dump($data);die();
return $data;

}

    #Lấy ajax kpi


function get_kpi_data($location_id,$year){
    $data=array();
    for ($i=1; $i <5 ; $i++) { 
        $j = $i*3-2;
        $arrParam['start_date'] = $year."-".$j."-01 00:00:00";
        $start =  $arrParam['start_date'];
        $arrParam['end_date'] = date("Y-m-d 00:00:00",strtotime("$start + 3 month"));

            #Tổng doanh thu
        $value = $this->Kpi_model->get_contract_value($location_id,$arrParam)['value'];
        $value = empty($value) ? 0: $value;

            #Lấy doanh thu theo categories
            // $category_value = $this->Kpi_model->get_revenue_categories($category_id,$location_id,$arrParam);

            #Doanh thu chia phòng ban
        $arrParam['expense_options'] = 'sale';
        $expense = $this->Kpi_model->get_expenses($location_id,$arrParam)['value'];
        $expense = empty($expense) ? 0: $expense;

            #Chi phí chung
        $arrParam['expense_options'] = 'other';
        $general_expense = $this->Kpi_model->get_expenses($location_id,$arrParam)['value'];
        $general_expense = empty($general_expense) ? 0: $general_expense;

            #Chi phí bên thứ 3 
        $supplier_expense = $this->Kpi_model->get_supplier_expense($location_id,$arrParam)['value'];
        $supplier_expense = empty($supplier_expense) ? 0: $supplier_expense;

            #Doanh thu thực hiện (Tổng doanh thu - Doanh thu chia phòng ban)
        $data[$i] = $value - $expense;

            #Lợi nhuận thực hiện = (Doanh thu thực hiện - Chi phí bên thứ 3 - Chi phí chung)
        $data[$i+5] = $data[$i] - $general_expense -$supplier_expense;

    }

    ksort($data);
    for ($i=1; $i <5 ; $i++) { 
     $data[5] = $data[5] +$data[$i];
 }

 for ($i=6; $i <10 ; $i++) { 
     $data[10] = $data[10] +$data[$i];
 }

 ksort($data);
 return $data;
}



function get_kpi_data_room($location_id,$year,$quater=null){
    $data=array();

    if($quater){
     $j = $quater*3-2;
     $arrParam['start_date'] = $year."-".$j."-01 00:00:00";
     $start =  $arrParam['start_date'];
     $arrParam['end_date'] = date("Y-m-d 00:00:00",strtotime("$start + 3 month"));


            #Lấy doanh thu theo categories
     for ($i=1; $i <5 ; $i++) { 
        $category_value[$i] = $this->Kpi_model->get_revenue_categories($i,$location_id,$arrParam)['value'];
        $category_value[$i] = empty($category_value[$i])? 0 : $category_value[$i];
    }

}
else{
    $arrParam['start_date'] = $year."-01-01 00:00:00";
    $start =  $arrParam['start_date'];
    $arrParam['end_date'] = date("Y-m-d 00:00:00",strtotime("$start + 1 year"));


            #Lấy doanh thu theo categories
    for ($i=1; $i <5 ; $i++) { 
        $category_value[$i] = $this->Kpi_model->get_revenue_categories($i,$location_id,$arrParam)['value'];
        $category_value[$i] = empty($category_value[$i])? 0 : $category_value[$i];
    }
}


            #Doanh thu thực hiện (Tổng doanh thu - Doanh thu chia phòng ban)
$data = $category_value ;



       // echo $this->db->last_query();die();
       // echo "<pre>";
       // var_dump($data);die();
       // $data[5]  =array();
       // $data[5][1] =  $data[5][2] = $data[5][3] = $data[5][4] =0;
       // for ($i=1; $i <5 ; $i++) { 
       //      for ($k=1; $k <5 ; $k++) { 
       //          $data[5][$k] = $data[5][$k] + $data[$i][$k];
       //      }

       // }

return $data;
}
function kpi_room(){   
    $this->check_action_permission('view_kpi_room');
    if(empty($this->input->post()))
        return;
    $option = $this->input->post('option');
    $select = $this->input->post('select');
    $year = $this->input->post('year');
    $tp = $this->input->post('tp');
    $quater = $this->input->post('quater');
    $location_id =$this->input->post('location_id');
    $inValid = !($select && $year && $tp);
    $ppr = array();
    $rate=array();
    $density =array();
    $tt=0;
    $locations = $this->Location->list_item();
    if($inValid)
    {
        echo json_encode(array("flag"=>'warning','notice'=>'Thiếu dữ liệu'));
        return;
    }

        #ĐIỂM DOANH THU
    if($select == 'revenue'){

     $result= $this->get_revenue_point($locations,$year,$quater,$tp);
 }

      #ĐIỂM LỢI NHUẬN
 if($select=="profit"){

    $result = $this->get_profit_point($locations,$year,$quater);

}

      #Nếu là tổng hợp
if($select=="total")
{

    if($location_id){
        foreach ($locations as $key => $value) {
                if($value['location_id'] == $location_id)
                    $locations = array(0=>$value);
            }

            
    }

    if(!$quater)
        $quater=5;
    if($tp=='plan'){
        $dt = $this->Kpi_model->get_kpi_total($year,$quater,$type=1,$location_id);
        $tpe = "total_plan";

    }
    if($tp=='result'){
        $dt = $this->Kpi_model->get_kpi_total($year,$quater,$type=1,$location_id);
        $tpe = "total_result";
        $point = $this->Kpi_model->get_kpi_total($year,$quater,$type=2,$location_id);
        $point['customer_in'] = empty($point['customer_in'])? 0 : $point['customer_in'];
        $point['customer_out'] = empty($point['customer_out'])? 0 : $point['customer_out'];
        $point['process'] = empty($point['process'])? 0 : $point['process'];
        $point['training'] = empty($point['training'])? 0 : $point['training'];


    }
    $dt['revenue'] = empty($dt['revenue'])? 0 : $dt['revenue'];
    $dt['profit'] = empty($dt['profit'])? 0 : $dt['profit'];
    $dt['customer_in'] = empty($dt['customer_in'])? 0 : $dt['customer_in'];
    $dt['customer_out'] = empty($dt['customer_out'])? 0 : $dt['customer_out'];
    $dt['process'] = empty($dt['process'])? 0 : $dt['process'];
    $dt['training'] = empty($dt['training'])? 0 : $dt['training'];
    $t =$dt['revenue'] + $dt['profit'] + $dt['customer_out'] + $dt['customer_in'] +$dt['process'] +$dt['training'];
    if($quater==5){
            
        $revenue_point = $this->get_revenue_point($locations,$year,null,$tp)['tt'];
        $profit_point = $this->get_profit_point($locations,$year,null)['dt']['point'];
    }
    else{
        $revenue_point = $this->get_revenue_point($locations,$year,$quater,$tp)['tt'];
        $profit_point = $this->get_profit_point($locations,$year,$quater)['dt']['point'];
    }

// var_dump($profit_point);die();
    $profit_point = $profit_point[count($profit_point)-1];
    $dt['total'] = $t;
    $t_point =$point['customer_out']*$dt['customer_out'] + $point['customer_in']*$dt['customer_in'] +$point['process']*$dt['process'] +$point['training']*$dt['training'] +$profit_point*$dt['profit'] +$revenue_point*$dt['revenue'];
    $t_point = round($t_point/100);
    $result = array('dt'=>$dt,'tp'=>$tpe,'profit_point'=>$profit_point,'revenue_point'=>$revenue_point,'point'=>$point,'t_point'=>$t_point);
}


if($option =="data")
    return $result;
else
  echo json_encode($result);


}

    #Điểm doanh thu
protected function get_revenue_point($locations,$year,$quater,$tp){

        #Nếu là theo quý
    if($quater){

        $dt = array();
        foreach ($locations as $key => $value) {
            $dt[$key]['location_id'] = $value['location_id'];
            $dt[$key]['location_data'] = $this->Kpi_model->get_kpi_revenue_room($year,$value['location_id'],$quater);
            $dt[$key]['location_name'] = $value['name'];
            $data = array();
            foreach ($dt[$key]['location_data'] as $key2 => $value2) {
                $data[$value2['group_id']] = array();
                $data[$value2['group_id']]['value'] =  $value2['value'];
                $data[$value2['group_id']]['quater'] = $value2['quater'];
                $data[$value2['group_id']]['year'] = $value2['year'];
            }

            for ($i=1; $i <5 ; $i++) { 
                if(empty($data[$i]))
                {
                    $data[$i]['value'] =  0;
                    $data[$i]['quater'] = $quater;
                    $data[$i]['year'] = $year;
                }

            }

            $dt[$key]['location_data'] = $data;
            $z=$key+1;
        }

        $tpe = 'plan_quater';

    }

        #Cả năm
    else{

        $dt = array();
        foreach ($locations as $key => $value) {
            $dt[$key]['location_id'] = $value['location_id'];
            $dt[$key]['location_name'] = $value['name'];
            $dt[$key]['location_data'] = array();
            for ($i=1; $i <5 ; $i++) { 
                $t=0;
                $dt[$key]['location_data'][$i] = $this->Kpi_model->get_kpi_revenue_room($year,$value['location_id'],null,$i);

                foreach ($dt[$key]['location_data'][$i] as $key2 => $value2) {
                    $t += $value2['value'];
                }
                $dt[$key]['location_data'][$i]['value'] =$t;

            }
            $z=$key+1;   

        }

        $tpe='plan_year';

    }

           #Kế hoạch

    $tmp=array();
    $tmp[1] =$tmp[2] =$tmp[3] =$tmp[4] =array();
    $tmp[1]['value'] =$tmp[2]['value'] =$tmp[3]['value'] =$tmp[4]['value'] =0;
    foreach ($dt as $key => $value) {
        $tmp[1]['value'] +=$value['location_data'][1]['value'];
        $tmp[2]['value'] +=$value['location_data'][2]['value'];
        $tmp[3]['value'] +=$value['location_data'][3]['value'];
        $tmp[4]['value'] +=$value['location_data'][4]['value'];

    }
    $dt[$z]['location_name']="Khối tư vấn(Tổng)";
    $dt[$z]['location_id'] =0;
    $dt[$z]['location_data'] = $tmp;


            #Lơi nhuận
    if($tp=='result'){

        if($quater){
          $tm =array();
          foreach ($locations as $key => $value) {
            $dtr[$key]['location_id'] = $value['location_id'];
            $dtr[$key]['location_data'] = $this->get_kpi_data_room($value['location_id'],$year,$quater);
            $dtr[$key]['location_name'] = $value['name'];
            $b = $key+1;
        }
        $tm[1] =$tm[2] =$tm[3] =$tm[4] =0;
        foreach ($dtr as $key => $value) {
            $tm[1] = $tm[1] + $value['location_data'][1];
            $tm[2] = $tm[2] + $value['location_data'][2];
            $tm[3] = $tm[3] + $value['location_data'][3];
            $tm[4] = $tm[4] + $value['location_data'][4];
        }
        ksort($tmp);
        $dtr[$b]['location_id'] =0;
        $dtr[$b]['location_name'] = "Khối tư vấn (Tổng)";
        $dtr[$b]['location_data'] = $tm;

        for ($x=1; $x <5 ; $x++) { 
            $tmp[$x]['value'] = empty($tmp[$x]['value'])? 0: $tmp[$x]['value'];
            if($tmp[$x]['value'] ==0){
                $ppr[$x] =0;
            }
            else{
               $ppr[$x] = round($tm[$x]/$tmp[$x]['value']*100,2); 
            }
            
            $rate[$x] = $this->convert_rate($ppr[$x]);
            $ppr[$x] = $ppr[$x]."%";
        }
        $density = $this->get_density($year,$quater);
        for ($i=1; $i <5 ; $i++) { 
         $tt =$tt + round($rate[$i]*$density[$i]/100);
     }
     $tpe = 'result_quater';
     $dt = $dtr;

 }
 else{
   $tm =array();
   foreach ($locations as $key => $value) {
    $dtr[$key]['location_id'] = $value['location_id'];
    $dtr[$key]['location_data'] = $this->get_kpi_data_room($value['location_id'],$year);
    $dtr[$key]['location_name'] = $value['name'];
    $b = $key+1;
}
$tm[1] =$tm[2] =$tm[3] =$tm[4] =0;
foreach ($dtr as $key => $value) {
    $tm[1] = $tm[1] + $value['location_data'][1];
    $tm[2] = $tm[2] + $value['location_data'][2];
    $tm[3] = $tm[3] + $value['location_data'][3];
    $tm[4] = $tm[4] + $value['location_data'][4];
}
ksort($tmp);
$dtr[$b]['location_id'] =0;
$dtr[$b]['location_name'] = "Khối tư vấn(Tổng)";
$dtr[$b]['location_data'] = $tm;

for ($x=1; $x <5 ; $x++) { 
    $tmp[$x]['value'] = empty($tmp[$x]['value'])? 1: $tmp[$x]['value'];
    $ppr[$x] = round($tm[$x]/$tmp[$x]['value']*100,2);
    $rate[$x] = $this->convert_rate($ppr[$x]);
    $ppr[$x] = $ppr[$x]."%";
}
$density = $this->get_density($year,5);
for ($i=1; $i <5 ; $i++) { 
   $tt =$tt + round($rate[$i]*$density[$i]/100);
}
$tpe = 'result_year';
$dt = $dtr;
}

}

$result = array('tp'=>$tpe,'dt'=>$dt,'ppr'=>$ppr,'rate'=>$rate,'density'=>$density,'tt'=>$tt);
return $result;
}

    # Điểm lợi nhuận
protected function get_profit_point($locations,$year,$quater){

   $dt = $this->get_profit_data($year,$quater,$locations);
   foreach ($locations as $key => $value) {
    $dt['location'][$key] = $value['name'];
}

$count = count($dt['revenue']);
foreach ($dt['revenue'] as $key => $value) {
    $dt['revenue'][$count] +=  $dt['revenue'][$key];
    $dt['expense'][$count] += $dt['expense'][$key]; 
    $dt['general_expense'][$count] += $dt['general_expense'][$key]; 
    $dt['supplier_expense'][$count] += $dt['supplier_expense'][$key];
    $dt['revenue_receving'][$count] += $dt['revenue_receving'][$key];
    $dt['plan_profit'][$count] += $dt['plan_profit'][$key];
    $dt['profit'][$count] +=  $dt['profit'][$key];
    $dt['salary'][$count] += $dt['salary'][$key];
    $dt['net_profit'][$count] += $dt['net_profit'][$key]; 
}  

$x = $dt['plan_profit'][$count] ? $dt['plan_profit'][$count]:1;
$dt['ppr'][$count] = round($dt['profit'][$count]/$x *100,2)."%";
$dt['point'][$count] = $this->convert_rate($dt['ppr'][$count]);
$dt['location'][$count] = "Khối tư vấn(Tổng)";
$result['tp'] = "profit";
$result['dt'] = $dt;

return $result;

}

function get_profit_data($year,$quater=null,$locations){
    $lo =array();
    $revenue =array();
    $expense=array();

    if($quater){
        $j = $quater*3-2;
        $arrParam['start_date'] = $year."-".$j."-01 00:00:00";
        $start =  $arrParam['start_date'];
        $arrParam['end_date'] = date("Y-m-d 00:00:00",strtotime("$start + 3 month"));
    }
    else{
        $arrParam['start_date'] = $year."-01-01 00:00:00";
        $start =  $arrParam['start_date'];
        $arrParam['end_date'] = date("Y-m-d 00:00:00",strtotime("$start + 1 year"));
    }

    foreach ($locations as $key => $value) {
            #Tổng doanh thu
        $revenue[$key] = $this->Kpi_model->get_contract_value($value['location_id'],$arrParam)['value'];
        $revenue[$key] = empty($revenue[$key]) ? 0: $revenue[$key];

            #Lấy doanh thu theo categories
            // $category_value = $this->Kpi_model->get_revenue_categories($category_id,$location_id,$arrParam);

            #Doanh thu chia phòng ban
        $arrParam['expense_options'] = 'sale';
        $expense[$key] = $this->Kpi_model->get_expenses($value['location_id'],$arrParam)['value'];
        $expense[$key] = empty($expense[$key]) ? 0: $expense[$key];

            #Chi phí chung
        $arrParam['expense_options'] = 'other';
        $general_expense[$key] = $this->Kpi_model->get_expenses($value['location_id'],$arrParam)['value'];
        $general_expense[$key] = empty($general_expense[$key]) ? 0: $general_expense[$key];

            #Chi phí bên thứ 3 
        $supplier_expense[$key] = $this->Kpi_model->get_supplier_expense($value['location_id'],$arrParam)['value'];
        $supplier_expense[$key] = empty($supplier_expense[$key]) ? 0: $supplier_expense[$key];

            #Doanh thu thực hiện (Tổng doanh thu - Doanh thu chia phòng ban)
        $revenue_receving[$key] = $revenue[$key] - $expense[$key];


            #Lợi nhuận kế hoạch
        $plan_profit[$key] = $this->Kpi_model->get_plan_profit($value['location_id'],$year,$quater)['value'];
        $plan_profit[$key] = empty($plan_profit[$key]) ? 0: $plan_profit[$key];


            #Lợi nhuận thực hiện = (Doanh thu thực hiện - Chi phí bên thứ 3 - Chi phí chung)
        $profit[$key] = $revenue_receving[$key] - $supplier_expense[$key] -  $general_expense[$key];

            #TH/KH(%)
        $x = $plan_profit[$key] ? $plan_profit[$key]:1;
        $ppr[$key] = round($profit[$key]/$x *100,2)."%";

            #Điểm
        $point[$key] = $this->convert_rate($ppr[$key]);

            #Chi phí lương
        $salary[$key] = $this->Kpi_model->get_salary($value['location_id'],$arrParam);
            # Lợi nhuận ròng
        $net_profit[$key]  =  $profit[$key] - $salary[$key];
    }

    $data['revenue'] = $revenue;
    $data['expense'] = $expense;
    $data['general_expense'] = $general_expense;
    $data['supplier_expense'] = $supplier_expense;
    $data['revenue_receving'] = $revenue_receving;
    $data['plan_profit'] = $plan_profit;
    $data['profit'] = $profit;
    $data['ppr'] = $ppr;
    $data['point'] = $point;
    $data['salary'] = $salary;
    $data['net_profit'] = $net_profit;
    return $data;

}

function get_density($year,$quater){
    $result = array();
    for ($i=1; $i <5 ; $i++) { 
        $result[$i] = $this->Kpi_model->density($year,$quater,$i);
        $result[$i] = empty($result[$i]) ? 0 : $result[$i];
    }
    return $result;
}
function update_data(){

    $this->check_action_permission('add_kpi_global');
    if($this->input->post('type'))
    {
        if($this->input->post('location'))
        {
            $data['location_id'] = $this->input->post('location');
        } 
        else
        {
            echo json_encode(array('flag'=>'warning','notice'=>'Thiếu dữ liệu'));
            return;
        }

        if($this->input->post('quater'))
        {
            $data['quater'] = $this->input->post('quater');
        } 
        else
        {
            echo json_encode(array('flag'=>'warning','notice'=>'Thiếu dữ liệu'));
            return;
        }

        if($this->input->post('ty'))
        {

            if($this->input->post('ty')=='vcbs')
            {
                $data['vcbs'] = str_replace(",","",$this->input->post('value'));
                $data['vcbs'] = empty($data['vcbs'])? 0 : $data['vcbs']; 
            }
            else if($this->input->post('ty')=='vcb')
            {
                $data['vcb'] = str_replace(",","",$this->input->post('value'));
                $data['vcb'] =empty($data['vcb'])? 0 : $data['vcb']; 
            }
            else
            {
                echo json_encode(array('flag'=>'error','notice'=>'Lỗi dữ liệu'));
                return;
            }

        } 
        else
        {
            echo json_encode(array('flag'=>'warning','notice'=>'Thiếu dữ liệu'));
            return;
        }
        if($this->input->post('year'))
        {
            $data['year'] = $this->input->post('year');
        } 
        else
        {
            echo json_encode(array('flag'=>'warning','notice'=>'Thiếu dữ liệu'));
            return;
        }

        if($this->input->post('kpi'))
        {
            $data['kpi'] = $this->input->post('kpi');
        } 
        else
        {
            echo json_encode(array('flag'=>'warning','notice'=>'Thiếu dữ liệu'));
            return;
        }


        $this->Kpi_model->save_data($data);
        echo json_encode(array('flag'=>'success','notice'=>'Cập nhật thành công'));
        return;

    }

}



function update_total(){

    $this->check_action_permission('add_kpi_room');
  $type =$this->input->post('type');
  $year =$this->input->post('year');
  $quater =$this->input->post('quater');
  $name =$this->input->post('name');
  $value =$this->input->post('value');
  $location_id = $this->input->post('location_id');

  $isValid = $type&&$year&&$name;
  if(!$isValid)
  {
      echo json_encode(array('flag'=>'warning','notice'=>'Thiếu dữ liệu'));
      return;
  }
  $data['year'] =$year;
  $data['quater'] =$quater? $quater :5;
  $data['type'] = $type;
  $data['location_id'] = $location_id;
  $data[$name] = str_replace(",","",$value);
  if($type==1){
    if($data[$name]>100 || $data[$name] <0 ){
       echo json_encode(array('flag'=>'warning','notice'=>'Tỷ trọng chỉ trong khoảng 0-100%'));
       return;
   }


   $check = $this->check_total($data,$name);
   if($data[$name] > $check){
    echo json_encode(array('flag'=>'warning','notice'=>'Tổng tỷ trọng không được lớn hơn 100%'));
    return;
}

}

$this->Kpi_model->save_total_data($data);
echo json_encode(array('flag'=>'success','notice'=>'Cập nhật thành công'));
return;

}


protected function check_total($data,$name){
    $this->db->where(array('year'=>$data['year'],'quater'=>$data['quater'],'type'=>$data['type'],'location_id'=>$data['location_id']));
    $result = $this->db->get('phppos_kpi_total')->row_array();
    if(empty($result)){
        $result = 100;
        return $result;
    }

    else{
        $result['revenue'] = empty($result['revenue']) ? 0 : $result['revenue'];
        $result['profit'] = empty($result['profit']) ? 0 : $result['profit'];
        $result['customer_in'] = empty($result['customer_in']) ? 0 : $result['customer_in'];
        $result['customer_out'] = empty($result['customer_out']) ? 0 : $result['customer_out'];
        $result['process'] = empty($result['process']) ? 0 : $result['process'];
        $result['training'] = empty($result['training']) ? 0 : $result['training'];
        $tt = $result['revenue'] + $result['profit'] + $result['customer_out'] + $result['customer_in'] +$result['process'] +$result['training']-$result[$name];
        $result = 100-$tt;
        return $result;
    }
}
#Cập nhật doanh thu kpi giá phòng 
function update_room_data(){

    $this->check_action_permission('add_kpi_room');
    if($this->input->post('location'))
    {
        $data['location_id'] = $this->input->post('location');
    } 
    else
    {
        echo json_encode(array('flag'=>'warning','notice'=>'Thiếu dữ liệu'));
        return;
    }

    if($this->input->post('quater'))
    {
        $data['quater'] = $this->input->post('quater');
    } 
    else
    {
        echo json_encode(array('flag'=>'warning','notice'=>'Thiếu dữ liệu'));
        return;
    }
    if($this->input->post('group'))
    {
        $data['group_id'] = $this->input->post('group');
        $data['value'] = str_replace(",","",$this->input->post('value'));
    } 
    else
    {
        echo json_encode(array('flag'=>'warning','notice'=>'Thiếu dữ liệu'));
        return;
    }

    if($this->input->post('year'))
    {
        $data['year'] = $this->input->post('year');
    } 
    else
    {
        echo json_encode(array('flag'=>'warning','notice'=>'Thiếu dữ liệu'));
        return;
    }


    $this->Kpi_model->save_room_data($data);
    echo json_encode(array('flag'=>'success','notice'=>'Cập nhật thành công'));
    return;


}


function update_density_data(){

    if($this->input->post('year'))
    {
        $data['year'] = $this->input->post('year');
    } 
    else
    {
        echo json_encode(array('flag'=>'warning','notice'=>'Thiếu dữ liệu'));
        return;
    }

    if($this->input->post('quater'))
    {
        $data['quater'] = $this->input->post('quater');
    } 
    else
    {
     $data['quater']=5;
 }
 if($this->input->post('group'))
 {
    $data['group_id'] = $this->input->post('group');
    $data['value'] = str_replace(",","",$this->input->post('value'));
    if($data['value']>100 || $data['value'] <0)
    {
        echo json_encode(array('flag'=>'warning','notice'=>'Tỷ trọng phải trong khoảng từ 0 đến 100%'));
        return;
    }
    $check = $this->check_density($data);
    if($data['value'] > $check){
         echo json_encode(array('flag'=>'warning','notice'=>"Tỷ trọng không được lớn hơn $check %"));
        return;
    }
} 
else
{
    echo json_encode(array('flag'=>'warning','notice'=>'Thiếu dữ liệu'));
    return;
}

$this->Kpi_model->save_density_data($data);
echo json_encode(array('flag'=>'success','notice'=>'Cập nhật thành công'));
return;


}


function check_density($data){
    $where = array('quater'=>$data['quater'],'year'=>$data['year']);
    $this->db->where($where);
    $list = $this->db->get('phppos_kpi_density')->result_array();
    $result = 100;
    if(!empty($list)){
        foreach ($list as $key => $value) {
            if($value['group_id'] != $data['group_id'])
            $result -= $value['value'];
        }

    }
    return $result;
}
function room(){
    $locations = $this->Location->list_item();
    $key = count($locations);
    $data['locations'] = $locations;
    $data['locations'][$key]['location_id']=0;
    $data['locations'][$key]['name']="Khối tư vấn (Tổng)";
    krsort($data['locations']);
    // var_dump($data['locations']);die();

    $this->load->view('kpi/room',$data);
}


function rate(){
    $data['rates'] = $this->Kpi_model->get_rate();
    $this->load->view('kpi/rate',$data);
}

function update_rate(){
    $this->check_action_permission('update_kpi_rate');
    if(empty($this->input->post('rate_start')))
        return;
    $data =array();
    $rate_start =$this->input->post('rate_start');
    $rate_end =$this->input->post('rate_end');
    $rate_point =$this->input->post('rate_point');
    $i=0;
    foreach ($rate_start as $key => $value) {
        $data[$i]['rate_start'] = $rate_start[$i];
        $data[$i]['rate_end'] = $rate_end[$i];
        $data[$i]['point'] = $rate_point[$i];
        $i++;
    }

    $this->db->empty_table('phppos_kpi_rate');
    $this->db->insert_batch('phppos_kpi_rate', $data);
    redirect(base_url('kpi/rate'),'refresh');
}


function convert_rate($rate){
    $this->db->where('rate_start <=',$rate);
    $this->db->where('rate_end >', $rate);
    $result = $this->db->get('phppos_kpi_rate')->row_array()['point'];
    $result = empty($result) ? 0 : $result;
    return $result;
}
function convert_rate_d7($arrParam=null){
    if (empty($arrParam)) {
        $rate = $this->input->post('rate');
    }else{
        $rate = $arrParam['rate'];
    }

    $this->db->where('rate_start <=',$rate);
    $this->db->where('rate_end >', $rate);
    $result = $this->db->get('phppos_kpi_rate')->row_array()['point'];
    $result = empty($result) ? 0 : $result;
    if (empty($arrParam)) {
        echo json_decode($result);
    }else{
        return $result;
    }
    
}
function kpi_room_data_d7($arrParam=null){   

    if(empty($arrParam))
        return;
    $select = $arrParam['select'];
    $year = $arrParam['year'];
    $tp = $arrParam['tp'];
    
    $inValid = !($select && $year && $tp);
    $ppr = array();
    $rate=array();
    $density =array();
    $tt=0;
    $locations = $this->Location->list_item();
    if($inValid)
    {
        return (array("flag"=>'warning','notice'=>'Thiếu dữ liệu'));
    }

        #ĐIỂM DOANH THU
    if($select == 'revenue'){
        $quater = $arrParam['quater'];
     $result= $this->get_revenue_point($locations,$year,$quater,$tp);
 }

      #ĐIỂM LỢI NHUẬN
 if($select=="profit"){

    $result = $this->get_profit_point($locations,$year,$quater);

}

      #Nếu là tổng hợp
if($select=="total")
{
    $quater = $arrParam['quater'];
    if(!$quater)
        $quater=5;
    if($tp=='plan'){
        $dt = $this->Kpi_model->get_kpi_total($year,$quater,$type=1);
        $tpe = "total_plan";
    }
    
    if($tp=='result'){
        $dt = $this->Kpi_model->get_kpi_total($year,$quater,$type=1);
        $tpe = "total_result";
        $point = $this->Kpi_model->get_kpi_total($year,$quater,$type=2);
        $point['customer_in'] = empty($point['customer_in'])? 0 : $point['customer_in'];
        $point['customer_out'] = empty($point['customer_out'])? 0 : $point['customer_out'];
        $point['process'] = empty($point['process'])? 0 : $point['process'];
        $point['training'] = empty($point['training'])? 0 : $point['training'];
    }

    $dt['revenue'] = empty($dt['revenue'])? 0 : $dt['revenue'];
    $dt['profit'] = empty($dt['profit'])? 0 : $dt['profit'];
    $dt['customer_in'] = empty($dt['customer_in'])? 0 : $dt['customer_in'];
    $dt['customer_out'] = empty($dt['customer_out'])? 0 : $dt['customer_out'];
    $dt['process'] = empty($dt['process'])? 0 : $dt['process'];
    $dt['training'] = empty($dt['training'])? 0 : $dt['training'];
    $t =$dt['revenue'] + $dt['profit'] + $dt['customer_out'] + $dt['customer_in'] +$dt['process'] +$dt['training'];
    if($quater==5){
        $revenue_point = $this->get_revenue_point($locations,$year,null,$tp)['tt'];
        $profit_point = $this->get_profit_point($locations,$year,null)['dt']['point'];
    }
    else{
        $revenue_point = $this->get_revenue_point($locations,$year,$quater,$tp)['tt'];
        $profit_point = $this->get_profit_point($locations,$year,$quater)['dt']['point'];
    }


    $profit_point = $profit_point[count($profit_point)-1];
    $dt['total'] = $t;
    $t_point =$point['customer_out'] + $point['customer_in'] +$point['process'] +$point['training'] +$profit_point +$revenue_point;
    $result = array('dt'=>$dt,'tp'=>$tpe,'profit_point'=>$profit_point,'revenue_point'=>$revenue_point,'point'=>$point,'t_point'=>$t_point);
}
return $result;
}

}