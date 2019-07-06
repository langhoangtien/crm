<?php
class Specific_supplier_store_account extends CI_Model
{
	function __construct()
	{
        $this->load->model('Supplier');
	}

    public function count_item($arrParams = null, $options = null) {
        $this->db -> select('COUNT(sa.sno) AS total_item')
                  -> from('store_supplier_accounts AS sa')
                  -> where('sa.supplier_id', $arrParams['supplier_id'])
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

    public function TONG_HOP_no_dau_cong_no_nha_cung_cap($arrParams = null) {
    
      $suppliers = $this->db->dbprefix('suppliers');
      $people = $this->db->dbprefix('people');
      $store_supplier_accounts = $this->db->dbprefix('store_supplier_accounts');

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
   $query = " SELECT * FROM 
                (
                    (   
                         SELECT c.deleted,c.company_name,c.account_number as code,c.person_id AS ID_NHOM_NHA_CUNG_CAP from ".$suppliers." c WHERE c.deleted = 0

                    ) 
                     AS BANG_LOC_NHA_CUNG_CAP
                INNER JOIN
                    (
                        
                      SELECT sa.date,sa.balance,sa.balance_2,sa.options,sa.sno AS SO_NO,sa.supplier_id as ID_KHACH_CONG_NO 
                      FROM ".$store_supplier_accounts." AS sa 
                      WHERE sa.bat_dau = 1 AND sa.options = 1 AND sa.deleted = 0
                      GROUP BY ID_KHACH_CONG_NO
                    ) 
                AS BANG_CONG_NO ON BANG_CONG_NO.ID_KHACH_CONG_NO = BANG_LOC_NHA_CUNG_CAP.ID_NHOM_NHA_CUNG_CAP
                ) ";
// die;
      $result_tmp = $this->db->query($query)->result_array();
      $this->db->flush_cache();

      return isset($result_tmp)?$result_tmp:0;
    }


     public function TONG_HOP_giao_dich_nha_cung_cap($arrParams = null) {
    
      $suppliers = $this->db->dbprefix('suppliers');
      $people = $this->db->dbprefix('people');
      $store_supplier_accounts = $this->db->dbprefix('store_supplier_accounts');

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
                       SELECT c.deleted,c.person_id AS ID_NHOM_NHA_CUNG_CAP from ".$suppliers." c WHERE c.deleted = 0

                  ) 
                   AS BANG_LOC_NHA_CUNG_CAP
              INNER JOIN
                  (
                      
                  SELECT * FROM 
                    (
                          (   
                               SELECT * FROM 
                              (
                                  SELECT SUM(sa.transaction_amount) AS GIAO_DICH_NO_KHACH,sa.supplier_id as ID_KHACH_NO
                                  FROM ".$store_supplier_accounts." AS sa 
                                  ".$where." AND sa.options = 2
                                  GROUP BY ID_KHACH_NO ORDER BY ID_KHACH_NO
                              ) AS BANG_1
                          ) 
                           AS BANG_GIAO_DICH_KHACH_NO
                      LEFT JOIN
                          (
                              
                            SELECT * FROM 
                              (
                                SELECT SUM(sa.transaction_amount) AS GIAO_DICH_KHACH_NO,sa.supplier_id as ID_NO_KHACH 
                                    FROM ".$store_supplier_accounts." AS sa 
                                    ".$where." AND sa.options = 1
                                    GROUP BY ID_NO_KHACH ORDER BY ID_NO_KHACH
                              ) AS BANG_2
                          ) 
                      AS BANG_GIAO_DICH_NO_KHACH ON BANG_GIAO_DICH_NO_KHACH.ID_NO_KHACH = BANG_GIAO_DICH_KHACH_NO.ID_KHACH_NO 
                    )

                  ) 
              AS BANG_CONG_NO ON BANG_CONG_NO.ID_KHACH_NO = BANG_LOC_NHA_CUNG_CAP.ID_NHOM_NHA_CUNG_CAP
              )";

  
// die;
          $result_tmp = $this->db->query($query)->result_array();
          $this->db->flush_cache();

          return isset($result_tmp)?$result_tmp:0;
        }


    public function lay_danh_sach_giao_dich_nha_cung_cap($arrParams = null, $options = null){
      $paginator = isset($arrParams['paginator'])?$arrParams['paginator']:null;

      $suppliers = $this->db->dbprefix('suppliers');
      $people = $this->db->dbprefix('people');
      $store_supplier_accounts = $this->db->dbprefix('store_supplier_accounts');

     # Lọc ra ngày cuối cùng thực hiện hóa đơn
      if(!empty($arrParams['start_date']) && !empty($arrParams['end_date'])) {
            $start_date =date('Y-m-d 00:00:00',strtotime($arrParams['start_date']));
            $end_date =date('Y-m-d 23:59:59',strtotime($arrParams['end_date']));

            $where =  "WHERE sa.date >='".$start_date."' AND sa.date <='".$end_date."'";

      } else {

        $where =  "WHERE sa.date >'1970-01-01 00:00:00' AND sa.date <'9999-04-13 00:00:00'";
      }
// echo
     $query ="SELECT sa.comment,sa.date,sa.receiving_id,sa.supplier_id,
                (case when sa.transaction_amount < 0 then -sa.transaction_amount ELSE 0 end) as ghi_co, 
                (case when sa.transaction_amount > 0 then sa.transaction_amount ELSE 0 end) as ghi_no 
        FROM phppos_store_supplier_accounts AS sa 
        ".$where." AND sa.options = ".$arrParams['options']." AND sa.supplier_id = ".$arrParams['supplier_id']." AND sa.deleted = 0 ORDER BY sa.date ASC";
// die;
      $result = $this->db->query($query);

      $this->db->flush_cache();

      return $result;
    }


    public function lay_tong_giao_dich_theo_nha_cung_cap($arrParams = null, $options = null) {

      $paginator = isset($arrParams['paginator'])?$arrParams['paginator']:null;

      $suppliers = $this->db->dbprefix('suppliers');
      $people = $this->db->dbprefix('people');
      $store_supplier_accounts = $this->db->dbprefix('store_supplier_accounts');

      # Lọc ra ngày cuối cùng thực hiện hóa đơn
      if(!empty($arrParams['start_date']) && !empty($arrParams['end_date'])) {
            $start_date =date('Y-m-d 00:00:00',strtotime($arrParams['start_date']));
            $end_date =date('Y-m-d 23:59:59',strtotime($arrParams['end_date']));
            $where =  "WHERE sa.date <'".$start_date."' AND sa.options = ".$arrParams['options']." AND sa.supplier_id = ".$arrParams['supplier_id']." AND sa.deleted = 0";

      } else {
        $where =  "WHERE sa.date <'1970-01-01 00:00:00'";
      }
// echo '<pre>'.
      $query = "SELECT SUM(sa.transaction_amount) as TONG_TIEN_GIAO_DICH
                  FROM ".$store_supplier_accounts." AS sa 
                  ".$where."";
// die;
      $result = $this->db->query($query);

      $this->db->flush_cache();

      return $result;
    }


    public function lay_no_dau_ky_theo_tung_nha_cung_cap($arrParams = null, $options = null){

      $paginator = isset($arrParams['paginator'])?$arrParams['paginator']:null;

      $suppliers = $this->db->dbprefix('suppliers');
      $people = $this->db->dbprefix('people');
      $store_supplier_accounts = $this->db->dbprefix('store_supplier_accounts');
// echo '<pre>'.
      $query = "SELECT * FROM
              (
                  (
                      (
                          SELECT *,sa.supplier_id AS ID_nha_cung_cap 
                          FROM ".$store_supplier_accounts." AS sa 
                          WHERE sa.options = ".$arrParams['options']." AND sa.bat_dau = 1 AND sa.supplier_id = ".$arrParams['supplier_id']." AND sa.deleted = 0    
                      ) 
                      AS BANG_LOC_nha_cung_cap
                          
                      INNER JOIN
                      
                          (
                              SELECT p.company_name,p.person_id AS ID_NHOM_KHACH from ".$suppliers." p
                          ) 
                          
                      AS BANG_LOC_KHACH ON BANG_LOC_KHACH.ID_NHOM_KHACH = BANG_LOC_nha_cung_cap.ID_nha_cung_cap
                  )
              )";
     // die;
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



   
}
?>