<?php
class Kpi_model extends CI_Model{
	
	function revenue_profit($year){
        $data =array();
        $location_id = $this->Employee->get_logged_in_employee_current_location_id();
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


    function tile_kpi($y,$a="profit"){

    	$data_revenue_profit = $this->revenue_profit($y);
    	$location_id = $this->Employee->get_logged_in_employee_current_location_id();
          $data = $this->db->select('*')
                    ->from('kpi')
                    ->where('kpi_type',$a)
                    ->where('type = 0')
                    ->where('year',$y)
                    ->where('location_id',$location_id)
                    ->get()->result();

                $data1 = array();
                foreach ($data as $key)
                {
                    $data1[$key->location_id] = ($key->data_kpi);
                }

              return json_decode($data1[$location_id]);
             

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



#                                                                             KPI                                                                      #
#                                                                                                                                                      #



function get_revenue_plan($year,$kpi="revenue",$arrParam=null,$option=null){
    $this->db->select();
    $this->db->from('kpi_plan_global');
    if(!empty($arrParam['location_id']))
    {
        $this->db->where('location_id', $arrParam['location_id']);
    }
    $this->db->where('year', $year);
    $this->db->where('kpi', $kpi);
    $result =  $this->db->get()->result_array();

    if($option==null){

            $data =array();
    foreach ($result as $key => $value) {
        $data[$value['quater']]['quater']=$value['quater'];
        $data[$value['quater']]['year'] = $value['year'];
        $data[$value['quater']]['location_id'] =$value['location_id'];
        $data[$value['quater']]['vcb'] = empty($value['vcb'])? 0 :$value['vcb'] ;
        $data[$value['quater']]['vcbs'] = empty($value['vcbs'])?0 :$value['vcbs'];
        $data[$value['quater']]['kpi'] = $value['kpi'];

    }
    for ($i=1; $i <5 ; $i++) { 
        if(empty($data[$i]))
        {
            $result[$i] =array();
            $result[$i]['vcb']=0;
            $result[$i]['vcbs']=0;
            $result[$i]['year']=$year;
            $result[$i]['quater'] = $i+1;

        }
        else
        {
            $result[$i] =$data[$i];
        }
    }

    unset($result[0]);


         $total = array('vcb'=>0,'vcbs'=>0);
        foreach ($result as $key => $value) {
            $total['vcb'] += $value['vcb'];
            $total['vcbs'] += $value['vcbs'];
        }
        $result['total'] =$total;
    }
   
    // echo $this->db->last_query();die();
    return $result;
}




    function save_data($data)
    {
        $where = array('year'=>$data['year'],'location_id'=>$data['location_id'],'kpi'=>$data['kpi'],'quater'=>$data['quater']);

        $this->db->where($where);
        $check = $this->db->get('phppos_kpi_plan_global')->row_array();

        if($check){
            $this->db->where($where);
            $this->db->update('phppos_kpi_plan_global', $data);
        }
        else{
            $this->db->insert('phppos_kpi_plan_global', $data);
        }
    }

        function save_room_data($data)
     {
        $where = array('year'=>$data['year'],'location_id'=>$data['location_id'],'group_id'=>$data['group_id'],'quater'=>$data['quater']);

        $this->db->where($where);
        $check = $this->db->get('phppos_kpi_revenue_room')->row_array();

        if($check){
            $this->db->where($where);
            $this->db->update('phppos_kpi_revenue_room', $data);
        }
        else{
            $this->db->insert('phppos_kpi_revenue_room', $data);
        }
    }


    function save_density_data($data)
     {
        $where = array('year'=>$data['year'],'group_id'=>$data['group_id'],'quater'=>$data['quater']);

        $this->db->where($where);
        $check = $this->db->get('phppos_kpi_density')->row_array();

        if($check){
            $this->db->where($where);
            $this->db->update('phppos_kpi_density', $data);
        }
        else{
            $this->db->insert('phppos_kpi_density', $data);
        }
    }


   function save_total_data($data)
     {
        $where = array('year'=>$data['year'],'quater'=>$data['quater'],'type'=>$data['type'],'location_id'=>$data['location_id']);

        $this->db->where($where);
        $check = $this->db->get('phppos_kpi_total')->row_array();

        if($check){
            $this->db->where($where);
            $this->db->update('phppos_kpi_total', $data);
        }
        else{
            $this->db->insert('phppos_kpi_total', $data);
        }
    }



    #Tổng doanh thu
    function get_contract_value($location_id=null,$arrParam=null){
        $this->db->select('SUM(IF(p.vat="published",(p.price / 1.1),p.price)) as value');
        $this->db->from('phppos_contract_payment as p');
        $this->db->join('phppos_contract as c', 'c.id = p.contract_id');
        $this->db->where('c.deleted', 0);
        if(!empty($location_id)){
            $this->db->where('c.locations_id', $location_id);
        }
        $this->db->where('(p.c_status ="done" OR p.c_status ="liquidated")');
        if (!empty($arrParam['start_date'])) 
        {
        $this->db->where('p.date_payment >=', $arrParam['start_date']);
        }
        if (!empty($arrParam['end_date'])) 
        {
        $this->db->where('p.date_payment <', $arrParam['end_date']);
        }
       
        $result = $this->db->get()->row_array();
        return $result;
    }

    #Lấy chi phí chung, hoặc chi phí chia phòng ban
    function get_expenses($location_id=null,$arrParam=null){
        $this->db->select('SUM(e.expense_amount) as value');
        $this->db->from('phppos_expenses as e');
        if($location_id){
            $this->db->where('location_id', $location_id);
        }
        if(!empty($arrParam['expense_options'])){
            $this->db->where('expense_options', $arrParam['expense_options']);
        }
        if (!empty($arrParam['start_date'])) 
        {
        $this->db->where('e.expense_date >=', $arrParam['start_date']);
        }
        if (!empty($arrParam['end_date'])) 
        {
        $this->db->where('e.expense_date <', $arrParam['end_date']);
        }
        $this->db->where('e.deleted', 0);
        return $this->db->get()->row_array();
    }


    #Chi phí bên thứ 3
    function get_supplier_expense($location_id=null,$arrParam=nul){
        $this->db->select('SUM(ri.item_unit_price) as value');
        $this->db->from('phppos_receivings as r');
        $this->db->join('phppos_receivings_items as ri', 'r.receiving_id = ri.receiving_id');
        $this->db->join('phppos_tasks as t', 't.id = r.task_id');
        $this->db->where('r.deleted', 0);
        if($location_id){
            $this->db->where('location_id', $location_id);
        }
        if (!empty($arrParam['start_date'])) 
        {
        $this->db->where('r.receiving_time >=', $arrParam['start_date']);
        }
        if (!empty($arrParam['end_date'])) 
        {
        $this->db->where('r.receiving_time <', $arrParam['end_date']);
        }
        return $this->db->get()->row_array();
    }


    function get_kpi_revenue_room($year,$location_id=null,$quater=null,$group_id=null){
        $this->db->where('kr.year', $year);
        if($location_id)
        {
            $this->db->where('kr.location_id', $location_id);
        }
        if($group_id)
        {
            $this->db->where('kr.group_id', $group_id);
        }
        if($quater)
        {
            $this->db->where('kr.quater', $quater);
        }
        return $this->db->get('phppos_kpi_revenue_room as kr')->result_array();
    }


#Lấy doanh thu theo nhóm dịch vụ
    function get_revenue_categories($category_id,$location_id=null,$arrParam=null)
    {
        $item = $this->get_list_item_by_categories($category_id);
        $this->db->select('SUM(IF(p.vat="published",(p.price / 1.1),p.price)) as value');
        $this->db->from('phppos_contract_payment as p');
        $this->db->join('phppos_contract as c', 'c.id = p.contract_id');
        $this->db->where('(p.c_status ="done" OR p.c_status ="liquidated")');
        $this->db->where_in('c.item_id', $item);
        $this->db->where('c.deleted', 0);
        if($location_id)
        {
            $this->db->where('c.locations_id', $location_id);
        }
        if (!empty($arrParam['start_date'])) 
        {
            $this->db->where('p.date_payment >=', $arrParam['start_date']);
        }
        if (!empty($arrParam['end_date'])) 
        {
            $this->db->where('p.date_payment <', $arrParam['end_date']);
        }
       
        $result = $this->db->get()->row_array();
        return $result;

    }


    #Lấy KPI Lợi nhuận theo quý và theo năm
    function get_plan_profit($location_id,$year,$quater=null){
        $this->db->select('SUM(vcbs) as value');
        $this->db->from('phppos_kpi_plan_global');
        $this->db->where('year', $year);
        if($quater){
            $this->db->where('quater', $quater);
        }
        $this->db->where('kpi','profit');
        $this->db->where('location_id', $location_id);
        return $this->db->get()->row_array();
    }
    # Lấy ds dịch vụ theo nhóm dịch vụ
    function get_list_item_by_categories($group_id)
    {
        $this->db->select('c.id');
        $this->db->where('c.group_id', $group_id);
        $list = $this->db->get('phppos_categories as c')->result_array();
        $result =array();
        foreach ($list as $key => $value) {
            $result[$key] = $value['id'];
        }

        if(empty($result))
            return;
        $this->db->select('i.item_id');
        $this->db->where_in('category_id', $result);
        $list = $this->db->get('phppos_items as i')->result_array();
        $result =array();
         foreach ($list as $key => $value) {
            $result[$key] = $value['item_id'];
        }

        // echo $this->db->last_query();die();
        // echo "<pre>";
        // var_dump($result);die();
        return $result;


    }

 #Tính lương nhân viên

    function get_salary($location_id,$arrParam){
        $list = $this->Kpi_model->list_employee($location_id);
        $total = 0;
        foreach ($list as $key => $value) {
            # Nếu đang hoạt động
            if($value['inactive']==0)
            {
                if(strtotime($value['hire_date']) < strtotime($arrParam['start_date']) )
                {
                    $d1 = new DateTime(date("Y-m-d",strtotime($arrParam['end_date'])));
                    $d2 = new DateTime(date("Y-m-d",strtotime($arrParam['start_date'])));
                    $interval = $d2->diff($d1);
                    $totalMonths = 12 * $interval->y + $interval->m;
                    $days = $interval->d;
                    if($days>20){
                        $totalMonths ++;
                    }
                    $salary = $totalMonths*$value['salary'];   
                }
                else
                {
                    if(strtotime($value['hire_date']) < strtotime($arrParam['end_date']))
                    {
                        $d1 = new DateTime(date("Y-m-d",strtotime($arrParam['end_date'])));
                        $d2 = new DateTime(date("Y-m-d",strtotime($value['hire_date'])));
                        $interval = $d2->diff($d1);
                        $totalMonths = 12 * $interval->y + $interval->m;
                        $days = $interval->d;
                            if($days>20){
                                $totalMonths ++;
                            }
                        $salary = $totalMonths*$value['salary'];   
                    
                    }
                    else{
                        $salary=0;
                    }


                }

                  
            }

            # Nếu không hoạt động
            else
            {

                if(strtotime($value['hire_date']) < strtotime($arrParam['start_date']) )
                {
                    if(strtotime($arrParam['end_date']) > strtotime($value['termination_date']))
                        $arrParam['end_date'] = $value['termination_date'];
                    $d1 = new DateTime(date("Y-m-d",strtotime($arrParam['end_date'])));
                    $d2 = new DateTime(date("Y-m-d",strtotime($arrParam['start_date'])));
                    $interval = $d2->diff($d1);
                    $totalMonths = 12 * $interval->y + $interval->m;
                    $days = $interval->d;
                    if($days>20){
                        $totalMonths ++;
                    }
                    $salary = $totalMonths*$value['salary'];   

                }
                else
                {
                    if(strtotime($value['hire_date']) < strtotime($arrParam['end_date']))
                    {
                        if(strtotime($arrParam['end_date']) > strtotime($value['termination_date']))
                        $arrParam['end_date'] = $value['termination_date'];
                        $d1 = new DateTime(date("Y-m-d",strtotime($arrParam['end_date'])));
                        $d2 = new DateTime(date("Y-m-d",strtotime($arrParam['hire_date'])));
                        $interval = $d2->diff($d1);
                        $totalMonths = 12 * $interval->y + $interval->m;
                        $days = $interval->d;
                        if($days>20){
                            $totalMonths ++;
                        }
                        $salary = $totalMonths*$value['salary'];


                    
                    }
                    else{
                        $salary=0;
                    }


                }
            }

            $total = $total +$salary;
        }
        return $total;
    }



    function list_employee($location_id){
        $this->db->select('e.person_id,e.group_id,e.id,e.inactive,e.hire_date,e.termination_date,l.salary');
        $this->db->from('phppos_employees as e');
        $this->db->join('phppos_employees_locations as el', 'e.person_id = el.employee_id', 'left');
        $this->db->join('phppos_employees_level as l', 'l.id = e.level_id');
        $this->db->join('phppos_locations as lo', 'lo.location_id = el.location_id');
        if(!empty($location_id))
        {
            $this->db->where('el.location_id', $location_id); 
        }  
        $this->db->where('e.deleted', 0);
        $this->db->where('lo.deleted', 0);
        $this->db->where('e.level_id >', 0);
        return $this->db->get()->result_array();
    }
    function density($year,$quater,$group_id){
        $this->db->where(array('year'=>$year,'quater'=>$quater,'group_id'=>$group_id));
        return $this->db->get('phppos_kpi_density')->row_array()['value'];
    }

    function get_rate()
    {
        return $this->db->get('phppos_kpi_rate')->result_array();
    }


    function get_kpi_total($year,$quater,$type=1,$location_id){
        $this->db->where(array('year'=>$year,'quater'=>$quater,'type'=>$type,'location_id'=>$location_id));
        return $this->db->get('phppos_kpi_total')->row_array();
    }
}