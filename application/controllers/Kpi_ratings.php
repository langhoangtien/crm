<?php
require_once ("Secure_area.php");
require_once ("interfaces/Idata_controller.php");

class Kpi_ratings extends Secure_area
{
    function __construct()
    {
//         error_reporting(-1);
// ini_set('display_errors', 'On');
       parent::__construct();
        $this->load->model('Location');
        $this->load->model('Ratings');
        $this->load->model("Contract");
    }
    
    function index(){
        $year = date("Y") ;
        $arr_year = array();
        for ($i=($year-5); $i < $year+5; $i++) { 
            $arr_year[$i]=$i;
        }
        ($arr_year);
        $data=["arr_year"=>$arr_year,"year"=>$year];
        $this->load->view('Kpi_ratings/revenue_point', $data);
    }
    function rate(){
        $rate_temp = $this->Ratings->view_rate_kpi();
        $rate =  array();
        foreach ($rate_temp as $k => $val) {
           $rate[$val['id']] = json_decode($val['data'],true);
        }
        $data=['rate'=>$rate];
        $this->load->view('Kpi_ratings/rate_kpi', $data);

    }
    function action_rate($num){
        $rate = $this->Ratings->view_rate_kpi();
        $row = 0;
        foreach ($rate as $key => $value) {
            $rate_temp = (json_decode($value['data'],true));
            $start_rate =  $rate_temp['start_rate'];
            $start =  !empty($rate_temp['start'])?$rate_temp['start']:' >= ';
            $end_rate =  $rate_temp['end_rate'];
            $end =  !empty($rate_temp['end'])?$rate_temp['end']:' < ';
            $point =  $rate_temp['point'];
            
            if ($start_rate < $num && $num < $end_rate )
            {
                $row = $point;
            }
        }
        return $row;
    }
    function view(){
        // Qúy
        $year_sub = $this->input->post("year_sub");
        // Năm 
        $year = $this->input->post("year");


        $point= $this->input->post("point");
        $point_sub = $this->input->post("point_sub");
        $location = $this->Location->list_item();
        $data=[];
        switch ($point) {
            case 'DDT':
                if($point_sub=='KH')
                    $this->revenue_point_plan($year,$year_sub,$point,$point_sub);
                if($point_sub=='KQ')
                    $this->revenue_point_result($year,$year_sub,$point,$point_sub);
                break;
            case 'DLN':
                if($point_sub=='KH')
                    $this->profit_plan($year,$year_sub,$point,$point_sub);
                if($point_sub=='KQ')
                    $this->profit_result($year,$year_sub,$point,$point_sub);                    
                break;
            case 'TH':
                if($point_sub=='KH')
                    $this->synthetic_plan($year,$year_sub,$point,$point_sub);
                if($point_sub=='KQ')
                    $this->synthetic_result($year,$year_sub,$point,$point_sub);                    
                break;    
            default:
                break;
        }
    }

    function synthetic_result($year,$year_sub,$point,$point_sub){
        $view_row = array(
            'TDT'=>'Tổng doanh thu',
            'DTCPB'=>'Doanh thu chia cho phòng ban khác',
            'DTTH'=>'Doanh thu thực hiện',
            'CPHBTB'=>'Chi phí bên thứ ba',
            'CPC'=>'Chi phí chung',
            'LNTH'=>'Lợi nhuận thực hiện',
            'LNKH'=>'Lợi nhuận kế hoạch',
            'THKH'=>'TH/KH(%)',
            'DTLNTH'=>'Điểm thu lợi nhuận thực hiện(*)',
            'CPLCB'=>'Chi phí lương cơ bản',
            'LNR'=>'Lợi nhuận ròng(trừ cả lương cơ bản)',
        );
         $location = $this->Location->list_item();
        $data_temp = $this->data_profit($view_row,$location,$year,$year_sub);
        $d_dt = 0;
        $d_ln=0;
        foreach ($data_temp['DTLNTH'] as $k => $val) {
            $d_ln+=$val;
        };
        $view_data = $this->Ratings->view_data($year,$year_sub,$point,'KH');
        $view_data = json_decode($view_data['data_room_rate'],true);
        $view_data_kq = $this->Ratings->view_data($year,$year_sub,$point,'KQ');
        $view_data_kq = json_decode($view_data_kq['data_room_rate'],true);
       
        $data=['d_ln'=>$d_ln,'location'=>$location,'view_data'=>$view_data,'view_data_kq'=>$view_data_kq];
        $this->load->view('Kpi_ratings/synthetic_result', $data);
    }
    function synthetic_plan($year,$year_sub,$point,$point_sub){
        $view_data = $this->Ratings->view_data($year,$year_sub,$point,$point_sub);
        $view_data = json_decode($view_data['data_room_rate'],true);
        $location = $this->Location->list_item();
        $data=['location'=>$location,'view_data'=>$view_data];
        $this->load->view('Kpi_ratings/synthetic_plan', $data);
    }
    function revenue_point_plan($year,$year_sub,$point,$point_sub){
        $view_data = $this->Ratings->view_data($year,$year_sub,$point,$point_sub);
        $view_data = json_decode($view_data['data_room_rate'],true);
        $location = $this->Location->list_item();
        $data=['location'=>$location,'view_data'=>$view_data];
        $this->load->view('Kpi_ratings/revenue_point_plan', $data);
    }
    function revenue_point_result($year,$year_sub,$point,$point_sub){
        $report_contracts = $this->Contract->kpi_report_contracts($year);
        $view_data = $this->Ratings->view_data($year,$year_sub,'DDT','KH');
        $view_data = json_decode($view_data['data_room_rate'],true); 
        $view_tt = $this->Ratings->view_data($year,$year_sub,'DDT','KQ');
        $view_tt = json_decode($view_tt['data_room_rate'],true);
        $view_data_temp = array(); 
        $view_data = is_array($view_data)?$view_data:array();
        foreach ($view_data as $key => $value) {
            foreach ($value as $k => $val) {
                if(!array_key_exists($k,$view_data_temp))
                    $view_data_temp[$k]=$val;
                else
                    $view_data_temp[$k]+=$val;
            }
        }
        $location = $this->Location->list_item();

        $arr = array(1=>'tp',2=>'cp',7=>'ma',0=>'tv');
        $arr_temp = array();
        $temp = array();
        foreach ($report_contracts as $k => $val) {
            foreach ($val as $key => $value) {
                for ($i=1; $i <6 ; $i++) { 
                  $temp[$key][$i] += $value[$i];  
                }
               
            }
        }
        foreach ($arr as $k => $val) {
            $th = $temp[$k][$year_sub];
           
            if(!empty($view_data_temp[$val])){
                $thkh = ($th/$view_data_temp[$val])*100;
            }
            else $thkh = 0;
            $arr_temp[$val]=$this->action_rate($thkh);
        }
        $data=['location'=>$location,'view_data'=>$view_data_temp,'report_contracts'=>$report_contracts,'year_sub'=>$year_sub,'view_tt'=>$view_tt,'arr_temp'=>$arr_temp];
        $this->load->view('Kpi_ratings/revenue_point_result', $data);

    }
    function profit_plan($year,$year_sub,$point,$point_sub){
        $view_data = $this->Ratings->view_data($year,$year_sub,$point,$point_sub);
        $view_data = json_decode($view_data['data_room_rate'],true);
        $location = $this->Location->list_item();
        $view_row = array(
            'DTTH'=>'Doanh thu thực hiện',
            'CPHBTB'=>'Chi phí bên thứ ba',
            'CPCPBK'=>'Chi phí cho phòng ban khác',
            'LNTH'=>'Lợi nhuận thực hiện',
            'LNKH'=>'Lợi nhuận kế hoạch',
            'THKH'=>'TH/KH(%)',
            'DTLNTH'=>'Điểm thu lợi nhuận thực hiện(*)',
            'CPLCB'=>'Chi phí lương cơ bản',
            'LNR'=>'Lợi nhuận ròng(trừ cả lương cơ bản)',
        );
        $data=['location'=>$location,'view_row'=>$view_row,'view_data'=>$view_data];
        $this->load->view('Kpi_ratings/profit_plan', $data);

    }
    function profit_result($year,$year_sub,$point,$point_sub){
        $report_contracts = $this->Contract->report_contracts();
        $report_receiving = $this->Contract->report_receiving();
        $view_data = $this->Ratings->view_data($year,$year_sub,$point,$point_sub);
        $view_data = json_decode($view_data['data_room_rate'],true);
        $location = $this->Location->list_item();
        $view_row = array(
            'TDT'=>'Tổng doanh thu',
            'DTCPB'=>'Doanh thu chia cho phòng ban khác',
            'DTTH'=>'Doanh thu thực hiện',
            'CPHBTB'=>'Chi phí bên thứ ba',
            'CPC'=>'Chi phí chung',
            'LNTH'=>'Lợi nhuận thực hiện',
            'LNKH'=>'Lợi nhuận kế hoạch',
            'THKH'=>'TH/KH(%)',
            'DTLNTH'=>'Điểm thu lợi nhuận thực hiện(*)',
            'CPLCB'=>'Chi phí lương cơ bản',
            'LNR'=>'Lợi nhuận ròng(trừ cả lương cơ bản)',
        );
        $data_temp = $this->data_profit($view_row,$location,$year,$year_sub);
        // echo "<pre>"; var_dump($data_temp);die();
        $data=['location'=>$location,'view_row'=>$view_row,'data_temp'=>$data_temp,'year_sub'=>$year_sub];
        $data['report_contracts'] = $report_contracts;
        $this->load->view('Kpi_ratings/profit_result', $data);

    }
    function data_profit($view_row,$location,$year='',$year_sub=''){
        $data = array();
        $view_data = $this->Ratings->view_data($year,$year_sub,'DLN','KH');
        $view_data = json_decode($view_data['data_room_rate'],true);
        $wage = ($this->Contract->get_employee_wage($year_sub));
        $data_kpi = $this->Ratings->get_kpi($year);
        foreach ($data_kpi as $key => $value) {
            $y=0;
            foreach ($value as $k => $val) {
                $y += $val['value'];
            }
            $data_kpi[$key][5]['value'] = $y;
        }
        // var_dump($data_kpi);
        //tổng doanh thu
        $report_contracts = $this->Contract->report_contracts($year);
        foreach ($report_contracts as $k => $val){
            for($i=1;$i<6;$i++){
                $data['TDT'][$k][$i] = $val[$i];
            }
        }
        // var_dump($data);
        //doanh thu chia cho phòng ban khác
        $report_expenses = $this->Contract->report_expenses_kpi($year,'','sale');
        foreach ($report_expenses as $k => $val) {
            for ($i=1; $i <6 ; $i++) {
                $data['DTCPB'][$k][$i] = $val[$i];
            }
        }
        // chi phí chung
        $report_expenses = $this->Contract->report_expenses_kpi($year,'','other');
        foreach ($report_expenses as $k => $val) {
            for ($i=1; $i <6 ; $i++) { 
                $data['CPC'][$k][$i] = $val[$i];
            }
        }
         //chi phí bên thứ 3
        $report_receiving = $this->Contract->report_receiving_kpi($year);
        
        foreach ($report_receiving as $k => $val) {
            for($i=1;$i<6;$i++){

                $data['CPHBTB'][$k][$i] = $val[$i];
            }
        }
        foreach ($location as $k => $val) {
            // danh thu thực hiện
            if(!isset($data['TDT'][$val['location_id']][$year_sub]))
                $data['TDT'][$val['location_id']][$year_sub]=0;
            if(!isset($data['DTCPB'][$val['location_id']][$year_sub]))
                $data['DTCPB'][$val['location_id']][$year_sub]=0;
             if(!isset($data['CPHBTB'][$val['location_id']][$year_sub]))
                $data['CPHBTB'][$val['location_id']][$year_sub]=0;
             if(!isset($data['CPC'][$val['location_id']][$year_sub]))
                $data['CPC'][$val['location_id']][$year_sub]=0;
            if(!isset($data['LNKH'][$val['location_id']]))
                $data['LNKH'][$val['location_id']]=0;            
            if($data['TDT'][$val['location_id']][$year_sub]!=0&&$data['DTCPB'][$val['location_id']][$year_sub]!=0)
            $data['DTTH'][$val['location_id']] = $data['TDT'][$val['location_id']][$year_sub]-$data['DTCPB'][$val['location_id']][$year_sub];
            else $data['DTTH'][$val['location_id']] = 0;
            // lợi nhuận thực hiện
            if($data['DTTH'][$val['location_id']]!=0&&$data['CPHBTB'][$val['location_id']][$year_sub]!=0&&$data['CPC'][$val['location_id']][$year_sub]!=0)
            $data['LNTH'][$val['location_id']] = ($data['DTTH'][$val['location_id']] - $data['CPHBTB'][$val['location_id']][$year_sub] - $data['CPC'][$val['location_id']][$year_sub]);
            else $data['LNTH'][$val['location_id']]=0;
             //lợi nhuận kế hoạch
            if(!empty($data_kpi[$val['location_id']][$year_sub]['value']))
            $data['LNKH'][$val['location_id']]=$data_kpi[$val['location_id']][$year_sub]['value'];
            else $data['LNKH'][$val['location_id']] =0;
            //TH/KH
            if($data['LNKH'][$val['location_id']]>0)
                $data['THKH'][$val['location_id']] = ($data['LNTH'][$val['location_id']]/$data['LNKH'][$val['location_id']])*100 ;
            else
                $data['THKH'][$val['location_id']]=0;
            
            $data['DTLNTH'][$val['location_id']] = $this->action_rate($data['THKH'][$val['location_id']]);
            //chi phí lương cơ bản
            if(!empty($wage[$val['location_id']]))
            $data['CPLCB'][$val['location_id']] = $wage[$val['location_id']];
            else $data['CPLCB'][$val['location_id']]=0;
            // lợi nhuận ròng
            $data['LNR'][$val['location_id']] = $data['DTTH'][$val['location_id']]-$data['CPHBTB'][$val['location_id']][$year_sub]-$data['CPC'][$val['location_id']][$year_sub]-$data['CPLCB'][$val['location_id']];
        }
        // echo "<pre>";var_dump($data);die();
        return $data;
    }
    function save(){
        $arr_post = $this->input->post();
        $location = $this->Location->list_item();
        $arr_data=array();
        $title = array('tp','cp','ma','tv');
        if($arr_post['point']=='DLN'&&$arr_post['point_sub']=='KH')
        {
            $title = array(
            'DTTH',
            'CPHBTB',
            'CPCPBK',
            'LNTH',
            'LNKH',
            'THKH',
            'DTLNTH',
            'CPLCB',
            'LNR',
            );
         }   
        foreach ($arr_post['arr_data'] as $k => $val) {
            $arr_post_temp[$val['name']] = $val['value'];
        }
        foreach ($location as $k => $val) {
            $arr_temp = array();
            foreach ($title as $value) {
                $arr_temp[$value]= $arr_post_temp[$value.'_'.$val['location_id']];
            }
            $arr_data[$val['location_id']]=$arr_temp;
        }
        if(($arr_post['point']=='DDT')&&$arr_post['point_sub']=='KQ'){
            $arr_data=$arr_post_temp;
        }
        if(($arr_post['point']=='TH')){
            $arr_data=$arr_post_temp;
        }
       $data = array(
        'year'=>$arr_post['year'],
        'year_sub'=>$arr_post['year_sub'],
        'point'=>$arr_post['point'],
        'profit'=>$arr_post['point_sub'],
        'status'=>1,
        'data_room_rate'=>json_encode($arr_data)
       );
       $this->Ratings->save($data);
    }
    function save_rate(){
        $arr_post = $this->input->post();
        $arr_data = array();
        foreach ($arr_post as $k => $val) {
            if($k=='point'){
                $arr_data[$k] = $val;
            }
            else{
                $number = preg_replace("/[^0-9]/", '', $val);
                $str = preg_replace("/[^=><]/", '', $val);
                $arr_data[$k.'_rate'] = $number;
                $arr_data[$k] = $str;
            }
        }
            $data = array(
            'data' => json_encode($arr_data),
            'status'=>'1',
        );
        $this->Ratings->save_rate_kpi($data);
        
    }
    function edit(){
        $arr_post = $this->input->post();
        $arr_data = array();
        $id = $arr_post['id'];
        foreach ($arr_post as $k => $val) {
            if($k=='point'){
                 $arr_data[$k] = $val;
            }
            else{
                $number = preg_replace("/[^0-9]/", '', $val);
                $str = preg_replace("/[^=><]/", '', $val);
                $arr_data[$k.'_rate'] = $number;
                $arr_data[$k] = $str;
            }
        }
            $data = array(
            'data' => json_encode($arr_data),
            'status'=>'1',
        );
        $this->Ratings->save_rate_kpi($data,$id);
    }
    function delete(){
        $arr_post = $this->input->post();
        $arr_data = array();
        $id = $arr_post['id'];
        $this->Ratings->delete_rate_kpi($id);
    }
}
?>