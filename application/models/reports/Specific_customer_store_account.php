<?php
require_once ("Report.php");
class Specific_customer_store_account extends Report
{
  function __construct()
  {
    parent::__construct();
        $this->load->model('Customer');
  }
  
  public function getDataColumns()
  {
    return array(array('data'=>lang('reports_id'), 'align'=>'left'),
    array('data'=>lang('reports_time'), 'align'=> 'left'),
    array('data'=>lang('reports_sale_id'), 'align'=> 'left'),
    array('data'=>lang('reports_debit'), 'align'=> 'left'),
    array('data'=>lang('reports_credit'), 'align'=> 'left'),
    array('data'=>lang('reports_balance'), 'align'=> 'left'),
    array('data'=>lang('reports_items'), 'align'=> 'left'),   
    array('data'=>lang('reports_comment'), 'align'=> 'left'));
    
  }
  
  public function getData()
  {
    $this->db->from('store_accounts');
    $this->db->where('customer_id',$this->params['customer_id']);
    $this->db->where('date BETWEEN "'.$this->params['start_date'].'" and "'.$this->params['end_date'].'"');
    //If we are exporting NOT exporting to excel make sure to use offset and limit
    if (isset($this->params['export_excel']) && !$this->params['export_excel'])
    {
      $this->db->limit($this->report_limit);
      $this->db->offset($this->params['offset']);
    }
    
    $result = $this->db->get()->result_array();
    
    for ($k=0;$k<count($result);$k++)
    {
      $item_names = array();
      $sale_id = $result[$k]['sale_id'];
      
      $this->db->select('name, sales_items.description');
      $this->db->from('items');
      $this->db->join('sales_items', 'sales_items.item_id = items.item_id');
      $this->db->where('sale_id', $sale_id);
      
      foreach($this->db->get()->result_array() as $row)
      {
        $item_name_and_desc = $row['name'];
        
        if ($row['description'])
        {
          $item_name_and_desc .= ' - '.$row['description'];
        }
        
        $item_names[] = $item_name_and_desc;
      }
      
      $this->db->select('name');
      $this->db->from('item_kits');
      $this->db->join('sales_item_kits', 'sales_item_kits.item_kit_id = item_kits.item_kit_id');
      $this->db->where('sale_id', $sale_id);
      
      foreach($this->db->get()->result_array() as $row)
      {
        $item_names[] = $row['name'];
      }
      
      $result[$k]['items'] = implode(', ', $item_names);
    }
    return $result;
  }
  
  public function getTotalRows()
  {
    $this->db->from('store_accounts');
    $this->db->where('customer_id',$this->params['customer_id']);
    $this->db->where('date BETWEEN "'.$this->params['start_date'].'" and "'.$this->params['end_date'].'"');
    return $this->db->count_all_results();
  }
  
  
  public function getSummaryData()
  {

    $summary_data=array('balance'=>$this->Customer->get_info($this->params['customer_id'])->balance);
    return $summary_data;
  }

    public function count_item($arrParams = null, $options = null) {
        $this->db -> select('COUNT(sa.sno) AS total_item')
                  -> from('store_accounts AS sa')
                  -> where('sa.customer_id', $arrParams['customer_id'])
                  -> where('sa.options', $arrParams['options'])
                  -> where('sa.transaction_amount != 0');

        if(!empty($arrParams['start_date'])) {
            $start_date = $arrParams['start_date'];
            $this->db->where("sa.date >= '$start_date'");
        }

        if(!empty($arrParams['end_date'])) {
            $end_date = $arrParams['end_date'];
            $this->db->where("sa.date <= '$end_date'");
        }

        $query = $this->db->get();

        $result = $query->row_array();

        $result = $result['total_item'];

        $this->db->flush_cache();

        return $result;
    }



    public function count_theo_nhom_khach_hang($arrParams = null, $options = null) 
    {
      $paginator = isset($arrParams['paginator'])?$arrParams['paginator']:null;

      $customers = $this->db->dbprefix('customers');
      $people = $this->db->dbprefix('people');
      $store_accounts = $this->db->dbprefix('store_accounts');

     # Lọc ra ngày cuối cùng thực hiện hóa đơn
      if(!empty($arrParams['start_date']) && !empty($arrParams['end_date'])) {
            # Kiểm tra nếu là ngày hôm nay sẽ lấy dữ liệu của hôm qua để tính toán
            $kiem_tra_start =date('d',strtotime($arrParams['start_date']));
            $kiem_tra_end =date('d',strtotime($arrParams['end_date']));
            if((int)$kiem_tra_start == (int)$kiem_tra_end){
              $date = date_create($arrParams['start_date']);
              date_modify($date, '-1 day');
              $start_date = date_format($date, 'Y-m-d H:i');
            } else {
              $start_date =date('Y-m-d H:i',strtotime($arrParams['start_date']));
            }
            $where_start_date = "WHERE sa.date >'".$start_date."'";
           
            $end_date =date('Y-m-d H:i',strtotime($arrParams['end_date']));
            $where_end_date = "WHERE sa.date <'".$end_date."'";
      } else {
        $where_start_date =  "WHERE sa.date >'".date('Y-m-d H:i',strtotime('1970-04-13 00:00'))."'";
        $where_end_date = "WHERE sa.date <'".date('Y-m-d H:i',strtotime('2020-04-13 00:00'))."'";
      }


      $query = "SELECT C.deleted,C.type_customer, AB.tk_khach_no,AB.tk_no_khach, AB.customer_id,P.last_name, P.address_1,P.code,P.phone_number,P.email
      FROM 
        (Select B.customer_id,
        CASE 
            WHEN B.sno_min = A.sno_max 
               THEN A.Max_balance
               ELSE A.Max_balance-B.Min_balance
            END as tk_khach_no,
        CASE 
            WHEN B.sno_min = A.sno_max 
               THEN A.Max_balance_2
               ELSE A.Max_balance_2-B.Min_balance_2
            END as tk_no_khach
        From 
          (SELECT *, sa.balance as Max_balance,sa.sno as sno_max, sa.balance_2 as Max_balance_2 
            FROM ".$store_accounts." as sa 
            JOIN 
              (SELECT sa.customer_id as samax_customerid ,MAX(sa.date) as MAXDATE_balance 
                FROM ".$store_accounts." AS sa
                
                ".$where_end_date."

                GROUP BY samax_customerid) Maxdatetime ON Maxdatetime.MAXDATE_balance = sa.date) 
        AS A
        INNER JOIN 
          (SELECT *, sa.balance as Min_balance,sa.sno as sno_min,sa.balance_2 as Min_balance_2 
            FROM ".$store_accounts." as sa 
            JOIN
              (SELECT sa.customer_id as samin_customerid ,MIN(sa.date) as MINDATE_balance 
                FROM ".$store_accounts." AS sa 

                ".$where_start_date."

                GROUP BY samin_customerid) Minxdatetime ON Minxdatetime.MINDATE_balance = sa.date) 
        AS B
        ON  A.samax_customerid = B.samin_customerid) AB
        INNER JOIN ".$customers." as C ON C.person_id = AB.customer_id 
        INNER JOIN ".$people." P ON P.person_id = AB.customer_id 
        WHERE C.type_customer = ".$arrParams['nhom_khach_hang']."";
        

        $result = (string)$this->db->query($query)->num_rows();

        $this->db->flush_cache();

        return $result;
    }


    public function lay_no_dau_ky_theo_nhom_khach_hang($arrParams = null) {
// var_dump($arrParams);
// die;
      $customers = $this->db->dbprefix('customers');
      $people = $this->db->dbprefix('people');
      $store_accounts = $this->db->dbprefix('store_accounts');

      $where =  "WHERE sa.bat_dau = 1 AND sa.options = ".$arrParams['options']."";
    // echo '<pre>'.
      $query = "SELECT * FROM
                (
                    (
                        (
                            SELECT * FROM
                            (
                                    (   
                                        SELECT c.deleted,c.person_id AS ID_NHOM_KHACH_HANG,c.type_customer from ".$customers." c where c.deleted = 0 AND c.type_customer =".$arrParams['nhom_khach_hang']." 
                                    ) 
                                     AS BANG_LOC_KHACH_HANG
                                INNER JOIN
                                    (  
                                      SELECT sa.balance AS tai_khoan_khach_no,sa.balance_2 AS tai_khoan_no_khach,sa.date,sa.bat_dau,sa.options,sa.sno,sa.customer_id AS ID_KHACH_THEO_THOI_GIAN
                                      FROM ".$store_accounts." AS sa 
                                      WHERE sa.bat_dau = 1 AND sa.options = ".$arrParams['options']." AND sa.deleted = 0 
                                      GROUP BY ID_KHACH_THEO_THOI_GIAN ORDER BY `ID_KHACH_THEO_THOI_GIAN` ASC
                                    ) 
                                AS BANG_CONG_NO ON BANG_CONG_NO.ID_KHACH_THEO_THOI_GIAN = BANG_LOC_KHACH_HANG.ID_NHOM_KHACH_HANG
                            ) 
                        ) AS BANG_STORE_FINAL 
                        INNER JOIN
                            (
                                SELECT *,INFO_PEOPLE.person_id as INFO_ID FROM ".$people." AS INFO_PEOPLE
                            ) 
                        AS TEST ON TEST.INFO_ID = BANG_STORE_FINAL.ID_KHACH_THEO_THOI_GIAN
                    )
                )";

      // $query = "";
// die;
      $result_tmp = $this->db->query($query)->result_array();
      $this->db->flush_cache();
      foreach ($result_tmp as $key => $value) {

      if(empty($result_tmp))
          $result = 0;
        else {
          if($arrParams['options'] == 1){
            $result_tmp[$key]['no_dau_ky'] = $value['tai_khoan_khach_no'];
          }
          else{
              $result_tmp[$key]['no_dau_ky'] = $value['tai_khoan_no_khach'];
          }
             
        }
      }
        return isset($result_tmp)?$result_tmp:0;
    }



    public function lay_tong_giao_dich_dau_ky_nhom_khach_hang($arrParams = null) {

      $customers = $this->db->dbprefix('customers');
      $people = $this->db->dbprefix('people');
      $store_accounts = $this->db->dbprefix('store_accounts');

      # Lọc ra ngày cuối cùng thực hiện hóa đơn
      if(!empty($arrParams['start_date']) && !empty($arrParams['end_date'])) {
            $start_date =date('Y-m-d 00:00:00',strtotime($arrParams['start_date']));
            $end_date =date('Y-m-d 23:59:59',strtotime($arrParams['end_date']));
            $where =  "WHERE sa.date <'".$start_date."' AND sa.options = ".$arrParams['options']." AND sa.deleted = 0";

      } else {
        $where =  "WHERE sa.bat_dau = 1 AND sa.options = ".$arrParams['options']."";
      }
      // echo '<pre>'.
      $query = "SELECT * FROM
                (
                    (
                        (
                            SELECT * FROM
                            (
                                (
                                    (   
                                        SELECT c.deleted,c.person_id AS ID_NHOM_KHACH_HANG,c.type_customer from ".$customers." c where c.deleted = 0 AND c.type_customer =".$arrParams['nhom_khach_hang']."
                                    ) 
                                     AS BANG_LOC_KHACH_HANG
                                INNER JOIN
                                    (
                                        
                                    SELECT SUM(case when sa.transaction_amount < 0 then -sa.transaction_amount ELSE 0 end) as ghi_co, 
                                            SUM(case when sa.transaction_amount > 0 then sa.transaction_amount ELSE 0 end) as ghi_no, 
                                            sa.date as MOC_THOI_GIAN,sa.bat_dau AS BAT_DAU,sa.options AS OPTION_2,sa.sno AS SO_NO_2,sa.customer_id as ID_KHACH_THEO_THOI_GIAN 
                                            FROM ".$store_accounts." AS sa 
                                            ".$where."
                                            GROUP BY ID_KHACH_THEO_THOI_GIAN ORDER BY sa.customer_id ASC
                                               
                                    ) 
                                AS BANG_CONG_NO ON BANG_CONG_NO.ID_KHACH_THEO_THOI_GIAN = BANG_LOC_KHACH_HANG.ID_NHOM_KHACH_HANG
                                ) 
                            ) 
                        ) AS BANG_STORE_FINAL 
                        INNER JOIN
                            (
                                SELECT *,INFO_PEOPLE.person_id as INFO_ID FROM ".$people." AS INFO_PEOPLE
                            ) 
                        AS TEST ON TEST.INFO_ID = BANG_STORE_FINAL.ID_KHACH_THEO_THOI_GIAN
                    )
                )";

// die;
      $result = $this->db->query($query);

      $this->db->flush_cache();

      return $result;
    }

  

    public function lay_danh_sach_giao_dich_khach_hang($arrParams = null, $options = null){
      $paginator = isset($arrParams['paginator'])?$arrParams['paginator']:null;

      $customers = $this->db->dbprefix('customers');
      $people = $this->db->dbprefix('people');
      $store_accounts = $this->db->dbprefix('store_accounts');

     # Lọc ra ngày cuối cùng thực hiện hóa đơn
      if(!empty($arrParams['start_date']) && !empty($arrParams['end_date'])) {
            $start_date =date('Y-m-d 00:00:00',strtotime($arrParams['start_date']));
            $end_date =date('Y-m-d 23:59:59',strtotime($arrParams['end_date']));

            $where =  "WHERE sa.date >='".$start_date."' AND sa.date <='".$end_date."'";

      } else {

        $where =  "WHERE sa.date >'1970-01-01 00:00:00' AND sa.date <'9999-04-13 00:00:00'";
      }
       
     $query ="SELECT sa.comment,sa.date,sa.sale_id,sa.customer_id,
                (case when sa.transaction_amount < 0 then -sa.transaction_amount ELSE 0 end) as ghi_co, 
                (case when sa.transaction_amount > 0 then sa.transaction_amount ELSE 0 end) as ghi_no 
        FROM phppos_store_accounts AS sa 
        ".$where." AND sa.options = ".$arrParams['options']." AND sa.customer_id = ".$arrParams['customer_id']." AND sa.deleted = 0 ORDER BY sa.date ASC";

      $result = $this->db->query($query);

      $this->db->flush_cache();

      return $result;
    }

    public function lay_tong_giao_dich_theo_tung_khach($arrParams = null, $options = null) {

      $paginator = isset($arrParams['paginator'])?$arrParams['paginator']:null;

      $customers = $this->db->dbprefix('customers');
      $people = $this->db->dbprefix('people');
      $store_accounts = $this->db->dbprefix('store_accounts');

      # Lọc ra ngày cuối cùng thực hiện hóa đơn
      if(!empty($arrParams['start_date']) && !empty($arrParams['end_date'])) {
            $start_date =date('Y-m-d 00:00:00',strtotime($arrParams['start_date']));
            $end_date =date('Y-m-d 23:59:59',strtotime($arrParams['end_date']));
            $where =  "WHERE sa.date <'".$start_date."' AND sa.options = ".$arrParams['options']." AND sa.customer_id = ".$arrParams['customer_id']." AND sa.deleted = 0";

      } else {
        $where =  "WHERE sa.date <'1970-01-01 00:00:00'";
      }

      $query = "SELECT SUM(sa.transaction_amount) as TONG_TIEN_GIAO_DICH
                  FROM ".$store_accounts." AS sa 
                  ".$where."";

      $result = $this->db->query($query);

      $this->db->flush_cache();

      return $result;
    }

    public function lay_no_dau_ky_theo_tung_khach_hang($arrParams = null, $options = null){

      $paginator = isset($arrParams['paginator'])?$arrParams['paginator']:null;

      $customers = $this->db->dbprefix('customers');
      $people = $this->db->dbprefix('people');
      $store_accounts = $this->db->dbprefix('store_accounts');

      $query = "SELECT * FROM
              (
                  (
                      (
                          SELECT *,sa.customer_id AS ID_KHACH_HANG 
                          FROM ".$store_accounts." AS sa 
                          WHERE sa.options = ".$arrParams['options']." AND sa.bat_dau = 1 AND sa.customer_id = ".$arrParams['customer_id']." AND sa.deleted = 0    
                      ) 
                      AS BANG_LOC_KHACH_HANG
                          
                      INNER JOIN
                      
                          (
                              SELECT p.last_name,p.person_id AS ID_NHOM_KHACH from phppos_people p
                          ) 
                          
                      AS BANG_LOC_KHACH ON BANG_LOC_KHACH.ID_NHOM_KHACH = BANG_LOC_KHACH_HANG.ID_KHACH_HANG
                  )
              )";
     
      $result_tmp = $this->db->query($query)->result_array();

      $this->db->flush_cache();
      foreach ($result_tmp as $key => $value) {

      if(empty($result_tmp))
          $result = 0;
      else {
          if($arrParams['options'] == 1){
            $result_tmp[$key]['no_dau_ky'] = $value['balance'];
          }
          else{
            $result_tmp[$key]['no_dau_ky'] = $value['balance_2'];
          }
             
        }
      }
  
      return isset($result_tmp)?$result_tmp:0;

    }



    public function list_item_theo_nhom_khach_hang($arrParams = null, $options = null) {
      $paginator = isset($arrParams['paginator'])?$arrParams['paginator']:null;

      $customers = $this->db->dbprefix('customers');
      $people = $this->db->dbprefix('people');
      $store_accounts = $this->db->dbprefix('store_accounts');

     # Lọc ra ngày cuối cùng thực hiện hóa đơn
      if(!empty($arrParams['start_date']) && !empty($arrParams['end_date'])) {
            $start_date =date('Y-m-d 00:00:00',strtotime($arrParams['start_date']));
            $end_date =date('Y-m-d 23:59:59',strtotime($arrParams['end_date']));

            $where =  "WHERE sa.date >='".$start_date."' AND sa.date <='".$end_date."'";

      } else {

        $where =  "WHERE sa.date >'1970-01-01 00:00:00' AND sa.date <'9999-04-13 00:00:00'";
      }

      $query = "SELECT * 
                FROM 
                  (SELECT *, customer_ghi_co.id_khach_hang AS person_id_ 
                  FROM ((SELECT c.deleted,c.person_id,c.type_customer from ".$customers." c where c.deleted = 0 AND c.type_customer = ".$arrParams['nhom_khach_hang'].") AS A 
                INNER JOIN 
                  (SELECT sa.date,sa.customer_id as id_khach_hang, 
                  SUM(case when sa.transaction_amount < 0 then -sa.transaction_amount ELSE 0 end) as ghi_co, 
                  SUM(case when sa.transaction_amount > 0 then sa.transaction_amount ELSE 0 end) as ghi_no 
                FROM ".$store_accounts." AS sa 

                    ".$where." AND sa.options = ".$arrParams['options']." AND sa.deleted = 0

                    GROUP BY id_khach_hang) as customer_ghi_co ON customer_ghi_co.id_khach_hang = A.person_id)) 
                    AS B 
                INNER JOIN (SELECT * from ".$people.") AS P ON B.person_id_ = P.person_id ORDER BY P.person_id ASC";

// die;
      $result = $this->db->query($query);

      $this->db->flush_cache();

      return $result;
    }


    public function lay_tong_no_dau_theo_nhom_khach_hang($arrParams = null) {
    
       
      $customers = $this->db->dbprefix('customers');
      $people = $this->db->dbprefix('people');
      $store_accounts = $this->db->dbprefix('store_accounts');
        # Lọc ra ngày cuối cùng thực hiện hóa đơn
      if(!empty($arrParams['start_date']) && !empty($arrParams['end_date'])) {
            $start_date =date('Y-m-d 00:00:00',strtotime($arrParams['start_date']));
            $end_date =date('Y-m-d 23:59:59',strtotime($arrParams['end_date']));
            // $MIN_hay_MAX = "MAX(sa.sno) as lay_balance_cuoi_ngay";
            $where =  "WHERE sa.date >'1970-01-01 00:00:00' AND sa.date <'".$end_date."'";

      } else {
        // $MIN_hay_MAX = "MAX(sa.sno) as lay_balance_cuoi_ngay";
        $where =  "WHERE sa.date >'1970-01-01 00:00:00' AND sa.date <'3000-04-13 00:00:00'";
      }                         
        // echo '<pre>'.
        $query ="SELECT type_customer,
                       SUM(balance) as TONG_NO_DAU_KHACH_NO,
                       SUM(balance_2) as TONG_NO_DAU_NO_KHACH
                FROM 
                (
                    (   
                         SELECT c.deleted,c.person_id AS ID_NHOM_KHACH_HANG,c.type_customer from ".$customers." c WHERE c.deleted = 0 AND c.type_customer = ".$arrParams['nhom_khach_hang']."

                    ) 
                     AS BANG_LOC_KHACH_HANG
                INNER JOIN
                    (
                        
                      SELECT sa.date,sa.balance,sa.balance_2,sa.options,sa.sno AS SO_NO,sa.customer_id as ID_KHACH_CONG_NO 
                      FROM ".$store_accounts." AS sa 
                       ".$where." AND sa.bat_dau = 1 AND sa.options = 1 AND sa.deleted = 0
                      GROUP BY ID_KHACH_CONG_NO
                    ) 
                AS BANG_CONG_NO ON BANG_CONG_NO.ID_KHACH_CONG_NO = BANG_LOC_KHACH_HANG.ID_NHOM_KHACH_HANG
                ) ";
// die;
      $result_tmp = $this->db->query($query)->result_array();
      $this->db->flush_cache();

      return isset($result_tmp)?$result_tmp:0;

    }

    public function lay_tong_giao_dich_theo_nhom_khach_hang($arrParams = null){

      $customers = $this->db->dbprefix('customers');
      $people = $this->db->dbprefix('people');
      $store_accounts = $this->db->dbprefix('store_accounts');

       # Lọc ra ngày cuối cùng thực hiện hóa đơn
        if(!empty($arrParams['start_date']) && !empty($arrParams['end_date'])) {
              $start_date =date('Y-m-d 00:00:00',strtotime($arrParams['start_date']));
              $end_date =date('Y-m-d 23:59:59',strtotime($arrParams['end_date']));
              $where =  "WHERE sa.date <='".$end_date."' AND sa.deleted = 0";            
        } else {
          // $start_date = '9999-01-01 00:00:00';
            $where =  "WHERE sa.date >'1970-01-01 00:00:00' AND sa.date <'3000-04-13 00:00:00'";            
        }
      // $arrParams['nhom_khach_hang'] = 21;
      // echo '<pre>'.
      $query = "SELECT type_customer,ID_NHOM_KHACH_HANG,
                SUM(GIAO_DICH_NO_KHACH) as TONG_GIAO_DICH_NO_KHACH,
                SUM(GIAO_DICH_KHACH_NO) as TONG_GIAO_DICH_KHACH_NO
                FROM 
                  (
                      (   
                           SELECT c.deleted,c.person_id AS ID_NHOM_KHACH_HANG,c.type_customer 
                           FROM phppos_customers c 
                           WHERE c.deleted = 0 AND c.type_customer = ".$arrParams['nhom_khach_hang']."
                      ) 
                       AS BANG_LOC_KHACH_HANG
                  INNER JOIN
                      (
                          
                      SELECT * FROM 
                        (
                              (   
                                   SELECT * FROM 
                                  (
                                    SELECT SUM(sa.transaction_amount) AS GIAO_DICH_NO_KHACH,sa.customer_id as ID_KHACH_NO
                                          FROM phppos_store_accounts AS sa 
                                         ".$where." AND sa.options = 2 AND sa.deleted = 0
                                          GROUP BY ID_KHACH_NO ORDER BY ID_KHACH_NO
                                  ) AS BANG_1
                              ) 
                               AS BANG_GIAO_DICH_KHACH_NO
                          LEFT JOIN
                              (
                                  
                                SELECT * FROM 
                                  (
                                    SELECT SUM(sa.transaction_amount) AS GIAO_DICH_KHACH_NO,sa.customer_id as ID_NO_KHACH 
                                          FROM phppos_store_accounts AS sa 
                                          ".$where." AND sa.options = 1 AND sa.deleted = 0
                                          GROUP BY ID_NO_KHACH ORDER BY ID_NO_KHACH
                                  ) AS BANG_2
                              ) 
                          AS BANG_GIAO_DICH_NO_KHACH ON BANG_GIAO_DICH_NO_KHACH.ID_NO_KHACH = BANG_GIAO_DICH_KHACH_NO.ID_KHACH_NO 
                        )

                      ) 
                  AS BANG_CONG_NO ON BANG_CONG_NO.ID_KHACH_NO = BANG_LOC_KHACH_HANG.ID_NHOM_KHACH_HANG
                  )";
                  // die;

        $result_tmp = $this->db->query($query)->result_array();
        $this->db->flush_cache();

        return isset($result_tmp)?$result_tmp:0;
    }


     public function TONG_HOP_no_dau_cong_no_khach_hang($arrParams = null) {
    
      $customers = $this->db->dbprefix('customers');
      $people = $this->db->dbprefix('people');
      $store_accounts = $this->db->dbprefix('store_accounts');

      # Lọc ra ngày cuối cùng thực hiện hóa đơn
      if(!empty($arrParams['start_date']) && !empty($arrParams['end_date'])) {
            $start_date =date('Y-m-d 00:00:00',strtotime($arrParams['start_date']));
            $end_date =date('Y-m-d 23:59:59',strtotime($arrParams['end_date']));
            // $MIN_hay_MAX = "MAX(sa.sno) as lay_balance_cuoi_ngay";
            $where =  "WHERE sa.date >'1970-01-01 00:00:00' AND sa.date <'".$end_date."'";

      } else {
        // $MIN_hay_MAX = "MAX(sa.sno) as lay_balance_cuoi_ngay";
        $where =  "WHERE sa.date >'1970-01-01 00:00:00' AND sa.date <'3000-04-13 00:00:00'";
      }
       $query = "SELECT *,
            CASE WHEN TEN_NHOM_KHACH_HANG IS NULL THEN 'Chưa tạo nhóm' ELSE TEN_NHOM_KHACH_HANG END as TEN_NHOM FROM
                (       
                    SELECT * FROM
                            (
                                (
                                    (
                                        SELECT * FROM 
                                        (
                                            (   
                                                 SELECT c.deleted,c.person_id AS ID_NHOM_KHACH_HANG,c.type_customer from ".$customers." c WHERE c.deleted = 0

                                            ) 
                                             AS BANG_LOC_KHACH_HANG
                                        INNER JOIN
                                            (
                                                
                                              SELECT sa.date,sa.balance,sa.balance_2,sa.options,sa.sno AS SO_NO,sa.customer_id as ID_KHACH_CONG_NO 
                                              FROM ".$store_accounts." AS sa 
                                              WHERE sa.bat_dau = 1 AND sa.options = 1 AND sa.deleted = 0
                                              GROUP BY ID_KHACH_CONG_NO
                                            ) 
                                        AS BANG_CONG_NO ON BANG_CONG_NO.ID_KHACH_CONG_NO = BANG_LOC_KHACH_HANG.ID_NHOM_KHACH_HANG
                                        ) 
               
                                    ) AS BANG_STORE_FINAL 
                                    INNER JOIN
                                        (
                                            SELECT *,INFO_PEOPLE.person_id as INFO_ID FROM ".$people." AS INFO_PEOPLE
                                        ) 
                                    AS TEST ON TEST.INFO_ID = BANG_STORE_FINAL.ID_KHACH_CONG_NO
                                )
                            )
                ) BANG_KET_QUA
                LEFT JOIN
                (
                    SELECT CT.id,CT.name as TEN_NHOM_KHACH_HANG FROM phppos_customers_type AS CT
                ) 
            AS TABLE_NHOM_KHACH_HANG ON TABLE_NHOM_KHACH_HANG.id = BANG_KET_QUA.type_customer";
// die;
      $result_tmp = $this->db->query($query)->result_array();
      $this->db->flush_cache();

      return isset($result_tmp)?$result_tmp:0;
    }


     public function TONG_HOP_giao_dich_khach_hang($arrParams = null) {
    
      $customers = $this->db->dbprefix('customers');
      $people = $this->db->dbprefix('people');
      $store_accounts = $this->db->dbprefix('store_accounts');

      # Lọc ra ngày cuối cùng thực hiện hóa đơn
      if(!empty($arrParams['start_date']) && !empty($arrParams['end_date'])) {
            $start_date =date('Y-m-d 00:00:00',strtotime($arrParams['start_date']));
            $end_date =date('Y-m-d 23:59:59',strtotime($arrParams['end_date']));
            
             $where =  "WHERE sa.date <='".$end_date."' AND sa.deleted = 0";                                  
      } else {
         // $start_date = '9999-01-01 00:00:00';
        $where =  "WHERE sa.date >'1970-01-01 00:00:00' AND sa.date <'3000-04-13 00:00:00'";                 
      }
      // echo '<pre>'.
      $query = "SELECT * FROM 
              (
                  (   
                       SELECT c.deleted,c.person_id AS ID_NHOM_KHACH_HANG,c.type_customer from ".$customers." c WHERE c.deleted = 0

                  ) 
                   AS BANG_LOC_KHACH_HANG
              INNER JOIN
                  (
                      
                  SELECT * FROM 
                    (
                          (   
                               SELECT * FROM 
                              (
                                  SELECT SUM(sa.transaction_amount) AS GIAO_DICH_NO_KHACH,sa.customer_id as ID_KHACH_NO
                                  FROM ".$store_accounts." AS sa 
                                   ".$where."  AND sa.options = 2 AND sa.deleted = 0
                                  GROUP BY ID_KHACH_NO ORDER BY ID_KHACH_NO
                              ) AS BANG_1
                          ) 
                           AS BANG_GIAO_DICH_KHACH_NO
                      LEFT JOIN
                          (
                              
                            SELECT * FROM 
                              (
                                SELECT SUM(sa.transaction_amount) AS GIAO_DICH_KHACH_NO,sa.customer_id as ID_NO_KHACH 
                                    FROM ".$store_accounts." AS sa 
                                     ".$where." AND sa.options = 1 AND sa.deleted = 0
                                    GROUP BY ID_NO_KHACH ORDER BY ID_NO_KHACH
                              ) AS BANG_2
                          ) 
                      AS BANG_GIAO_DICH_NO_KHACH ON BANG_GIAO_DICH_NO_KHACH.ID_NO_KHACH = BANG_GIAO_DICH_KHACH_NO.ID_KHACH_NO 
                    )

                  ) 
              AS BANG_CONG_NO ON BANG_CONG_NO.ID_KHACH_NO = BANG_LOC_KHACH_HANG.ID_NHOM_KHACH_HANG
              )";

  
// die;
      $result_tmp = $this->db->query($query)->result_array();
      $this->db->flush_cache();

      return isset($result_tmp)?$result_tmp:array();
    }





    public function list_item($arrParams = null, $options = null) {
        $paginator = $arrParams['paginator'];

        $this->db -> select('sa.*')
                  -> select("DATE_FORMAT(sa.date, '%d-%m-%Y %H:%i') as date_format", FALSE)
                  -> from('store_accounts AS sa')
                  -> join('sales AS s', 'sa.sale_id = s.sale_id','left')
                  -> where('sa.customer_id', $arrParams['customer_id'])
                  -> where('sa.options', $arrParams['options'])
                  -> where('sa.transaction_amount != 0');

        if(!empty($arrParams['start_date'])) {
            $start_date = $arrParams['start_date'];
            $this->db->where("sa.date >= '$start_date'");
        }

        if(!empty($arrParams['end_date'])) {
            $end_date = $arrParams['end_date'];
            $this->db->where("sa.date <= '$end_date'");
        }

        $this->db->order_by('sa.sno', 'ASC');

        if(!empty($paginator)) {
            $page = $arrParams['page'];
            $this->db->limit($paginator['per_page'],($page - 1)*$paginator['per_page']);
        }

        $query = $this->db->get();

        $result = $query->result_array();

        $this->db->flush_cache();

        return $result;
    }

     

    public function get_debt_start($arrParams = null) {
        $this->db -> select('sa.*')
                  -> from('store_accounts AS sa')
                  -> where('sa.customer_id', $arrParams['customer_id'])
                  -> where('sa.options', $arrParams['options']);

        if(!empty($arrParams['start_date'])) {
            $start_date = $arrParams['start_date'];
            $this->db->where("sa.date < '$start_date'");
        }

        $this->db->order_by('sa.sno', 'DESC');
        $query = $this->db->get();

        $result_tmp = $query->row_array();

        $this->db->flush_cache();

        if(empty($result_tmp))
            $result = 0;
        else {
            if($arrParams['options'] == 1)
                $result = $result_tmp['balance'];
            else
                $result = $result_tmp['balance_2'];
        }

        return $result;
    }

    function get_debt_end($arrParams = null) {
        $this->db -> select('sa.*')
                    -> from('store_accounts AS sa')
                    -> where('sa.customer_id', $arrParams['customer_id'])
                    -> where('sa.options', $arrParams['options']);

        if(!empty($arrParams['end_date'])) {
            $end_date = $arrParams['end_date'];
            $this->db->where("sa.date <= '$end_date'");
        }

        $this->db->order_by('sa.sno', 'DESC');
        $query = $this->db->get();

        $result_tmp = $query->row_array();

        $this->db->flush_cache();

        if(!empty($result_tmp)) {
            if($arrParams['options'] == 1)
                $result = $result_tmp['balance'];
            elseif($arrParams['options'] == 2)
                $result = $result_tmp['balance_2'];
        }else {
            $customer_model = $this->load->model('Customer');
            $customer_info = $this->Customer->get_info($arrParams['customer_id']);

            if($arrParams['options'] == 1)
                $result = $customer_info->balance;
            elseif($arrParams['options'] == 2)
                $result = $customer_info->balance_2;
        }

        return $result;

    }
    /*--------------------------------------------------------------------------
    * 
    *               REPORT SUMMARY CUSTOMER LIABILITIES
    *-----------------------------------------------------------------------------
    */

    
     public function  summary_customer_opening_closing_balance($arrParams = [])
    {
        $limit      = '';
        $not_in_groups = 'Không có nhóm';
        if (!empty($arrParams['page']) && !empty($arrParams['per_page'])) {
            $offset      = $arrParams['per_page'];
            $numberLimit = ($arrParams['page']-1)*$arrParams['per_page'];
            $limit  = ' LIMIT '.$numberLimit.','.$offset;
        }
        $query =  ' SELECT  COALESCE(IF(opening_balance.psa_transaction_amount <0, 0, opening_balance.psa_transaction_amount), 0) 
                            + COALESCE(changed_by_manual_or_import_excel_opening.psa_transaction_amount,0) AS opening_balance,
                            
                            COALESCE(IF(closing_balance.psa_transaction_amount <0, 0, closing_balance.psa_transaction_amount),0)
                            + COALESCE(changed_by_manual_or_import_excel_closing.psa_transaction_amount,0)  AS closing_balance,
                            
                            COALESCE( psa_transaction_amount_credit.psa_transaction_amount,0)  AS credit,

                            COALESCE(changed_by_manual_or_import_excel_credit.psa_transaction_amount,0) AS manual_or_excel_credit,

                            COALESCE( psa_transaction_amount_debit.psa_transaction_amount,0) AS debit,
                            
                            COALESCE(changed_by_manual_or_import_excel_debit.psa_transaction_amount,0) AS manual_or_excel_debit,
                            
                            phppos_people.code,
                            phppos_people.last_name,
                            phppos_people.first_name,      
                            IF(phppos_customers_type.name IS NULL, '."'".$not_in_groups."'" .', phppos_customers_type.name) AS customer_type_name
                    FROM
                    
                    (   SELECT      COALESCE(SUM(psa.transaction_amount),0) AS psa_transaction_amount,
                                    psa.customer_id
                        FROM        phppos_store_accounts psa
                        LEFT JOIN   phppos_sales s
                        ON          s.sale_id = psa.sale_id
                        WHERE       psa.date <= '."'".$arrParams['end_date']."'".' 
                                    AND psa.options = '.$arrParams['customer_balance_options'].'
                                    AND ( s.deleted = 0 OR psa.sale_id IS NULL)
                        GROUP BY    psa.customer_id 
                    ) AS closing_balance
                    
                    LEFT JOIN
                    
                    (   SELECT      COALESCE(SUM(psa.transaction_amount),0) AS psa_transaction_amount,
                                    psa.customer_id
                        FROM        phppos_store_accounts psa
                        LEFT JOIN   phppos_sales s
                        ON          s.sale_id = psa.sale_id
                        WHERE       psa.date <= '."'".$arrParams['start_date']."'".'
                                    AND psa.options = '.$arrParams['customer_balance_options'].'
                                    AND ( s.deleted = 0 OR psa.sale_id IS NULL)
                        GROUP BY    psa.customer_id
                    ) AS opening_balance
                    
                    ON opening_balance.customer_id = closing_balance.customer_id
                    
                    LEFT JOIN
                    
                    (
                        SELECT      COALESCE(-SUM(psa_transaction_amount ),0) as psa_transaction_amount,
                                    customer_id
                        
                        FROM 
                        
                        (SELECT     sale_id, 
                                    psa_transaction_amount,
                                    customer_id
                        FROM
                        (SELECT     COALESCE(SUM(psa.transaction_amount),0) AS psa_transaction_amount,
                                    psa.sale_id,
                                    psa.customer_id
                        FROM        phppos_store_accounts psa
                        INNER JOIN  phppos_sales s
                        ON          s.sale_id = psa.sale_id
                        WHERE       psa.date >= '."'".$arrParams['start_date']."'".'
                                    AND psa.date <= '."'".$arrParams['end_date']."'".'
                                    AND psa.options = '.$arrParams['customer_balance_options'].'
                                    AND s.deleted = 0
                        GROUP BY    psa.sale_id) 
                        
                        AS          transaction_amount_by_sale_id
                        
                        WHERE       psa_transaction_amount < 0)
                        
                        AS          transaction_amount_credit 
                        
                        GROUP BY    transaction_amount_credit.customer_id
                        
                        
                    )   AS          psa_transaction_amount_credit
                    
                    ON psa_transaction_amount_credit.customer_id = closing_balance.customer_id
                    
                    LEFT JOIN

                    (
                        SELECT      COALESCE(SUM(psa_transaction_amount ),0) as psa_transaction_amount,
                                    customer_id
                        
                        FROM 
                        
                        (SELECT     sale_id, 
                                    psa_transaction_amount,
                                    customer_id
                        FROM
                        (SELECT     COALESCE(SUM(psa.transaction_amount),0) AS psa_transaction_amount,
                                    psa.sale_id,
                                    psa.customer_id
                        FROM        phppos_store_accounts psa
                        INNER JOIN  phppos_sales s
                        ON          s.sale_id = psa.sale_id
                        WHERE       psa.date >= '."'".$arrParams['start_date']."'".'
                                    AND psa.date <= '."'".$arrParams['end_date']."'".'
                                    AND psa.options ='.$arrParams['customer_balance_options'].'
                                    AND s.deleted = 0
                        GROUP BY    psa.sale_id) 
                        
                        AS          transaction_amount_by_sale_id
                        
                        WHERE       psa_transaction_amount > 0)
                        
                        AS          transaction_amount_debit 
                        
                        GROUP BY    transaction_amount_debit.customer_id
                        
                        
                    )   AS          psa_transaction_amount_debit
                    
                    ON psa_transaction_amount_debit.customer_id = closing_balance.customer_id

                    
                    LEFT JOIN

                    (   SELECT      COALESCE(SUM(psamec.transaction_amount),0) AS psa_transaction_amount,
                                    psamec.customer_id
                        FROM        phppos_store_accounts_manual_change_or_import_excel psamec
                        WHERE       psamec.date >= '."'".$arrParams['start_date']."'".'
                                    AND psamec.date <= '."'".$arrParams['end_date']."'".' 
                                    AND psamec.options = '.$arrParams['customer_balance_options'].'
                                    AND psamec.transaction_amount>0
                        GROUP BY    psamec.customer_id 
                    ) AS changed_by_manual_or_import_excel_debit
                    
                    ON changed_by_manual_or_import_excel_debit.customer_id = closing_balance.customer_id
                    
                    LEFT JOIN
                    
                    (   SELECT      COALESCE(-SUM(psamec.transaction_amount),0) AS psa_transaction_amount,
                                    psamec.customer_id
                        FROM        phppos_store_accounts_manual_change_or_import_excel psamec 
                        WHERE       psamec.date >= '."'".$arrParams['start_date']."'".'
                                    AND psamec.date <= '."'".$arrParams['end_date']."'".'
                                    AND psamec.options = '.$arrParams['customer_balance_options'].'
                                    AND psamec.transaction_amount<0
                        GROUP BY    psamec.customer_id
                    ) AS changed_by_manual_or_import_excel_credit
                    
                    ON changed_by_manual_or_import_excel_credit.customer_id = closing_balance.customer_id
                    
                    LEFT JOIN

                    (   SELECT      COALESCE(SUM(psamec.transaction_amount),0) AS psa_transaction_amount,
                                    psamec.customer_id
                        FROM        phppos_store_accounts_manual_change_or_import_excel psamec
                        WHERE       psamec.date <= '."'".$arrParams['start_date']."'".' 
                                    AND psamec.options = '.$arrParams['customer_balance_options'].'
                        GROUP BY    psamec.customer_id 
                    ) AS changed_by_manual_or_import_excel_opening
                    
                    ON changed_by_manual_or_import_excel_opening.customer_id = closing_balance.customer_id
                    
                    LEFT JOIN
                    
                    (   SELECT      COALESCE(SUM(psamec.transaction_amount),0) AS psa_transaction_amount,
                                    psamec.customer_id
                        FROM        phppos_store_accounts_manual_change_or_import_excel psamec 
                        WHERE       psamec.date <= '."'".$arrParams['end_date']."'".'
                                    AND psamec.options = '.$arrParams['customer_balance_options'].'
                        GROUP BY    psamec.customer_id
                    ) AS changed_by_manual_or_import_excel_closing
                    
                    ON changed_by_manual_or_import_excel_closing.customer_id = closing_balance.customer_id
                    
                    INNER JOIN phppos_customers
                    ON closing_balance.customer_id = phppos_customers.person_id
                    
                    INNER JOIN phppos_people
                    ON closing_balance.customer_id = phppos_people.person_id
                    
                    LEFT JOIN phppos_customers_type
                    ON phppos_customers.type_customer = phppos_customers_type.id
                    
                    WHERE  (psa_transaction_amount_credit.psa_transaction_amount >0 OR
                           psa_transaction_amount_debit.psa_transaction_amount >0 OR
                           changed_by_manual_or_import_excel_credit.psa_transaction_amount>0 OR
                           changed_by_manual_or_import_excel_debit.psa_transaction_amount>0)
                           AND phppos_customers.deleted = 0
                    '.$limit;
                    ;
                    
        $results_opening_closing_balance = $this->db->query($query)->result_array();
        
        return $results_opening_closing_balance;
      
    }
    
    public function  summary_customer_opening_closing_balance_count($arrParams = [])
    {

        
        $query =  ' SELECT          COUNT(phppos_people.code) AS totalRows
                            
                    FROM
                    
                    (   SELECT      COALESCE(SUM(psa.transaction_amount),0) AS psa_transaction_amount,
                                    psa.customer_id
                        FROM        phppos_store_accounts psa
                        LEFT JOIN   phppos_sales s
                        ON          s.sale_id = psa.sale_id
                        WHERE       psa.date <= '."'".$arrParams['end_date']."'".' 
                                    AND psa.options = '.$arrParams['customer_balance_options'].'
                                    AND ( s.deleted = 0 OR psa.sale_id IS NULL)
                        GROUP BY    psa.customer_id 
                    ) AS closing_balance
                    
                    LEFT JOIN
                    
                    (   SELECT      COALESCE(SUM(psa.transaction_amount),0) AS psa_transaction_amount,
                                    psa.customer_id
                        FROM        phppos_store_accounts psa
                        LEFT JOIN   phppos_sales s
                        ON          s.sale_id = psa.sale_id
                        WHERE       psa.date <= '."'".$arrParams['start_date']."'".'
                                    AND psa.options = '.$arrParams['customer_balance_options'].'
                                    AND ( s.deleted = 0 OR psa.sale_id IS NULL)
                        GROUP BY    psa.customer_id
                    ) AS opening_balance
                    
                    ON opening_balance.customer_id = closing_balance.customer_id
                    
                    LEFT JOIN
                    
                    (
                        SELECT      COALESCE(-SUM(psa_transaction_amount ),0) as psa_transaction_amount,
                                    customer_id
                        
                        FROM 
                        
                        (SELECT     sale_id, 
                                    psa_transaction_amount,
                                    customer_id
                        FROM
                        (SELECT     COALESCE(SUM(psa.transaction_amount),0) AS psa_transaction_amount,
                                    psa.sale_id,
                                    psa.customer_id
                        FROM        phppos_store_accounts psa
                        INNER JOIN  phppos_sales s
                        ON          s.sale_id = psa.sale_id
                        WHERE       psa.date >= '."'".$arrParams['start_date']."'".'
                                    AND psa.date <= '."'".$arrParams['end_date']."'".'
                                    AND psa.options = '.$arrParams['customer_balance_options'].'
                                    AND s.deleted = 0
                        GROUP BY    psa.sale_id) 
                        
                        AS          transaction_amount_by_sale_id
                        
                        WHERE       psa_transaction_amount < 0)
                        
                        AS          transaction_amount_credit 
                        
                        GROUP BY    transaction_amount_credit.customer_id
                        
                        
                    )   AS          psa_transaction_amount_credit
                    
                    ON psa_transaction_amount_credit.customer_id = closing_balance.customer_id
                    
                    LEFT JOIN

                    (
                        SELECT      COALESCE(SUM(psa_transaction_amount ),0) as psa_transaction_amount,
                                    customer_id
                        
                        FROM 
                        
                        (SELECT     sale_id, 
                                    psa_transaction_amount,
                                    customer_id
                        FROM
                        (SELECT     COALESCE(SUM(psa.transaction_amount),0) AS psa_transaction_amount,
                                    psa.sale_id,
                                    psa.customer_id
                        FROM        phppos_store_accounts psa
                        INNER JOIN  phppos_sales s
                        ON          s.sale_id = psa.sale_id
                        WHERE       psa.date >= '."'".$arrParams['start_date']."'".'
                                    AND psa.date <= '."'".$arrParams['end_date']."'".'
                                    AND psa.options ='.$arrParams['customer_balance_options'].'
                                    AND s.deleted = 0
                        GROUP BY    psa.sale_id) 
                        
                        AS          transaction_amount_by_sale_id
                        
                        WHERE       psa_transaction_amount > 0)
                        
                        AS          transaction_amount_debit 
                        
                        GROUP BY    transaction_amount_debit.customer_id
                        
                        
                    )   AS          psa_transaction_amount_debit
                    
                    ON psa_transaction_amount_debit.customer_id = closing_balance.customer_id

                    
                    LEFT JOIN

                    (   SELECT      COALESCE(SUM(psamec.transaction_amount),0) AS psa_transaction_amount,
                                    psamec.customer_id
                        FROM        phppos_store_accounts_manual_change_or_import_excel psamec
                        WHERE       psamec.date >= '."'".$arrParams['start_date']."'".'
                                    AND psamec.date <= '."'".$arrParams['end_date']."'".' 
                                    AND psamec.options = '.$arrParams['customer_balance_options'].'
                                    AND psamec.transaction_amount>0
                        GROUP BY    psamec.customer_id 
                    ) AS changed_by_manual_or_import_excel_debit
                    
                    ON changed_by_manual_or_import_excel_debit.customer_id = closing_balance.customer_id
                    
                    LEFT JOIN
                    
                    (   SELECT      COALESCE(-SUM(psamec.transaction_amount),0) AS psa_transaction_amount,
                                    psamec.customer_id
                        FROM        phppos_store_accounts_manual_change_or_import_excel psamec 
                        WHERE       psamec.date >= '."'".$arrParams['start_date']."'".'
                                    AND psamec.date <= '."'".$arrParams['end_date']."'".'
                                    AND psamec.options = '.$arrParams['customer_balance_options'].'
                                    AND psamec.transaction_amount<0
                        GROUP BY    psamec.customer_id
                    ) AS changed_by_manual_or_import_excel_credit
                    
                    ON changed_by_manual_or_import_excel_credit.customer_id = closing_balance.customer_id
                    
                    LEFT JOIN

                    (   SELECT      COALESCE(SUM(psamec.transaction_amount),0) AS psa_transaction_amount,
                                    psamec.customer_id
                        FROM        phppos_store_accounts_manual_change_or_import_excel psamec
                        WHERE       psamec.date <= '."'".$arrParams['start_date']."'".' 
                                    AND psamec.options = '.$arrParams['customer_balance_options'].'
                        GROUP BY    psamec.customer_id 
                    ) AS changed_by_manual_or_import_excel_opening
                    
                    ON changed_by_manual_or_import_excel_opening.customer_id = closing_balance.customer_id
                    
                    LEFT JOIN
                    
                    (   SELECT      COALESCE(SUM(psamec.transaction_amount),0) AS psa_transaction_amount,
                                    psamec.customer_id
                        FROM        phppos_store_accounts_manual_change_or_import_excel psamec 
                        WHERE       psamec.date <= '."'".$arrParams['end_date']."'".'
                                    AND psamec.options = '.$arrParams['customer_balance_options'].'
                        GROUP BY    psamec.customer_id
                    ) AS changed_by_manual_or_import_excel_closing
                    
                    ON changed_by_manual_or_import_excel_closing.customer_id = closing_balance.customer_id
                    
                    INNER JOIN phppos_people
                    ON closing_balance.customer_id = phppos_people.person_id
                    
                    WHERE  psa_transaction_amount_credit.psa_transaction_amount >0 OR
                           psa_transaction_amount_debit.psa_transaction_amount >0 OR
                           changed_by_manual_or_import_excel_credit.psa_transaction_amount>0 OR
                           changed_by_manual_or_import_excel_debit.psa_transaction_amount>0
                           
                    '
                    ;
                    
        $results_opening_closing_balance_count = $this->db->query($query)->row_array();
        
        return $results_opening_closing_balance_count['totalRows'];
      
    }
    
    public function  summary_customer_opening_closing_balance_total($arrParams = [])
    {

        
        $query =  ' SELECT          COALESCE(IF(SUM(opening_balance.psa_transaction_amount)<0,0,SUM(closing_balance.psa_transaction_amount)),0)
                                            + COALESCE(SUM(changed_by_manual_or_import_excel_opening.psa_transaction_amount),0) 
                                    AS total_opening_balance,
                                    
                                    COALESCE(IF(SUM(closing_balance.psa_transaction_amount)<0,0,SUM(closing_balance.psa_transaction_amount)),0)
                                            +COALESCE(SUM(changed_by_manual_or_import_excel_closing.psa_transaction_amount),0) 
                                    AS total_closing_balance,
                                    
                                    COALESCE(SUM(psa_transaction_amount_credit.psa_transaction_amount),0) 
                                            + COALESCE(SUM(changed_by_manual_or_import_excel_credit.psa_transaction_amount),0)  
                                    AS total_credit,
                                    
                                    COALESCE(SUM(psa_transaction_amount_debit.psa_transaction_amount),0) 
                                            + COALESCE(SUM(changed_by_manual_or_import_excel_debit.psa_transaction_amount),0) 
                                    AS total_debit
                            
                    FROM
                    
                    (   SELECT      COALESCE(SUM(psa.transaction_amount),0) AS psa_transaction_amount,
                                    psa.customer_id
                        FROM        phppos_store_accounts psa
                        LEFT JOIN   phppos_sales s
                        ON          s.sale_id = psa.sale_id
                        WHERE       psa.date <= '."'".$arrParams['end_date']."'".' 
                                    AND psa.options = '.$arrParams['customer_balance_options'].'
                                    AND ( s.deleted = 0 OR psa.sale_id IS NULL)
                        GROUP BY    psa.customer_id 
                    ) AS closing_balance
                    
                    LEFT JOIN
                    
                    (   SELECT      COALESCE(SUM(psa.transaction_amount),0) AS psa_transaction_amount,
                                    psa.customer_id
                        FROM        phppos_store_accounts psa
                        LEFT JOIN   phppos_sales s
                        ON          s.sale_id = psa.sale_id
                        WHERE       psa.date <= '."'".$arrParams['start_date']."'".'
                                    AND psa.options = '.$arrParams['customer_balance_options'].'
                                    AND ( s.deleted = 0 OR psa.sale_id IS NULL)
                        GROUP BY    psa.customer_id
                    ) AS opening_balance
                    
                    ON opening_balance.customer_id = closing_balance.customer_id
                    
                    LEFT JOIN
                    
                    (
                        SELECT      COALESCE(-SUM(psa_transaction_amount ),0) as psa_transaction_amount,
                                    customer_id
                        
                        FROM 
                        
                        (SELECT     sale_id, 
                                    COALESCE(SUM(transaction_amount_by_sale_id.psa_transaction_amount),0) AS psa_transaction_amount,
                                    customer_id
                        FROM
                        (SELECT     COALESCE(SUM(psa.transaction_amount),0) AS psa_transaction_amount,
                                    psa.sale_id,
                                    psa.customer_id
                        FROM        phppos_store_accounts psa
                        INNER JOIN  phppos_sales s
                        ON          s.sale_id = psa.sale_id
                        WHERE       psa.date >= '."'".$arrParams['start_date']."'".'
                                    AND psa.date <= '."'".$arrParams['end_date']."'".'
                                    AND psa.options = '.$arrParams['customer_balance_options'].'
                                    AND s.deleted = 0
                        GROUP BY    psa.sale_id) 
                        
                        AS          transaction_amount_by_sale_id
                        
                        WHERE       psa_transaction_amount < 0)
                        
                        AS          transaction_amount_credit 
                        
                        GROUP BY    transaction_amount_credit.customer_id
                        
                        
                    )   AS          psa_transaction_amount_credit
                    
                    ON psa_transaction_amount_credit.customer_id = closing_balance.customer_id
                    
                    LEFT JOIN

                    (
                        SELECT      COALESCE(SUM(psa_transaction_amount ),0) as psa_transaction_amount,
                                    customer_id
                        
                        FROM 
                        
                        (SELECT     sale_id, 
                                    COALESCE(SUM(transaction_amount_by_sale_id.psa_transaction_amount),0) AS psa_transaction_amount,
                                    customer_id
                        FROM
                        (SELECT     COALESCE(SUM(psa.transaction_amount),0) AS psa_transaction_amount,
                                    psa.sale_id,
                                    psa.customer_id
                        FROM        phppos_store_accounts psa
                        INNER JOIN  phppos_sales s
                        ON          s.sale_id = psa.sale_id
                        WHERE       psa.date >= '."'".$arrParams['start_date']."'".'
                                    AND psa.date <= '."'".$arrParams['end_date']."'".'
                                    AND psa.options ='.$arrParams['customer_balance_options'].'
                                    AND s.deleted = 0
                        GROUP BY    psa.sale_id) 
                        
                        AS          transaction_amount_by_sale_id
                        
                        WHERE       psa_transaction_amount > 0)
                        
                        AS          transaction_amount_debit 
                        
                        GROUP BY    transaction_amount_debit.customer_id
                        
                        
                    )   AS          psa_transaction_amount_debit
                    
                    ON psa_transaction_amount_debit.customer_id = closing_balance.customer_id

                    
                    LEFT JOIN

                    (   SELECT      COALESCE(SUM(psamec.transaction_amount),0) AS psa_transaction_amount,
                                    psamec.customer_id
                        FROM        phppos_store_accounts_manual_change_or_import_excel psamec
                        WHERE       psamec.date >= '."'".$arrParams['start_date']."'".'
                                    AND psamec.date <= '."'".$arrParams['end_date']."'".' 
                                    AND psamec.options = '.$arrParams['customer_balance_options'].'
                                    AND psamec.transaction_amount>0
                        GROUP BY    psamec.customer_id 
                    ) AS changed_by_manual_or_import_excel_debit
                    
                    ON changed_by_manual_or_import_excel_debit.customer_id = closing_balance.customer_id
                    
                    LEFT JOIN
                    
                    (   SELECT      COALESCE(-SUM(psamec.transaction_amount),0) AS psa_transaction_amount,
                                    psamec.customer_id
                        FROM        phppos_store_accounts_manual_change_or_import_excel psamec 
                        WHERE       psamec.date >= '."'".$arrParams['start_date']."'".'
                                    AND psamec.date <= '."'".$arrParams['end_date']."'".'
                                    AND psamec.options = '.$arrParams['customer_balance_options'].'
                                    AND psamec.transaction_amount<0
                        GROUP BY    psamec.customer_id
                    ) AS changed_by_manual_or_import_excel_credit
                    
                    ON changed_by_manual_or_import_excel_credit.customer_id = closing_balance.customer_id
                    
                    LEFT JOIN

                    (   SELECT      COALESCE(SUM(psamec.transaction_amount),0) AS psa_transaction_amount,
                                    psamec.customer_id
                        FROM        phppos_store_accounts_manual_change_or_import_excel psamec
                        WHERE       psamec.date <= '."'".$arrParams['start_date']."'".' 
                                    AND psamec.options = '.$arrParams['customer_balance_options'].'
                        GROUP BY    psamec.customer_id 
                    ) AS changed_by_manual_or_import_excel_opening
                    
                    ON changed_by_manual_or_import_excel_opening.customer_id = closing_balance.customer_id
                    
                    LEFT JOIN
                    
                    (   SELECT      COALESCE(SUM(psamec.transaction_amount),0) AS psa_transaction_amount,
                                    psamec.customer_id
                        FROM        phppos_store_accounts_manual_change_or_import_excel psamec 
                        WHERE       psamec.date <= '."'".$arrParams['end_date']."'".'
                                    AND psamec.options = '.$arrParams['customer_balance_options'].'
                        GROUP BY    psamec.customer_id
                    ) AS changed_by_manual_or_import_excel_closing
                    
                    ON changed_by_manual_or_import_excel_closing.customer_id = closing_balance.customer_id
                    
                    INNER JOIN phppos_customers
                    ON closing_balance.customer_id = phppos_customers.person_id
                    
                    INNER JOIN phppos_people
                    ON closing_balance.customer_id = phppos_people.person_id
                    
                    LEFT JOIN phppos_customers_type
                    ON phppos_customers.type_customer = phppos_customers_type.id
                    
                    WHERE  (psa_transaction_amount_credit.psa_transaction_amount >0 OR
                           psa_transaction_amount_debit.psa_transaction_amount >0 OR
                           changed_by_manual_or_import_excel_credit.psa_transaction_amount>0 OR
                           changed_by_manual_or_import_excel_debit.psa_transaction_amount>0)
                           AND phppos_customers.deleted = 0
                    '
                    ;
                    
        $results_opening_closing_balance_count = $this->db->query($query)->row_array();
        
        return $results_opening_closing_balance_count;
      
    }
    /*--------------------------------------------------------------------------
    * 
    *              END REPORT SUMMARY CUSTOMER LIABILITIES
    *-----------------------------------------------------------------------------
    */
}
?>