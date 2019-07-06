<?php
require_once ("Report.php");
class Summary_inventory extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	public function getDataColumns()
	{
		
		return array();
	}
	
	public function getData($id = null)
	{
    $this->db->from('inventory');
    $this->db->where('trans_items',$id);
    
    $ket_qua = $this->db->get()->result_array();
		return $ket_qua;		
	}
	
	public function getSummaryData()
	{
		return array();
	}

  
	public function lay_danh_sach_xuat_nhap_san_pham($arrParams = null, $options = null){
      $paginator = isset($arrParams['paginator'])?$arrParams['paginator']:null;
      $inventory = $this->db->dbprefix('inventory');

     # Lọc ra ngày cuối cùng thực hiện hóa đơn
      if(!empty($arrParams['start_date']) && !empty($arrParams['end_date'])) {
            $start_date =date('Y-m-d 00:00:00',strtotime($arrParams['start_date']));
            $end_date =date('Y-m-d 23:59:59',strtotime($arrParams['end_date']));

            $where =  "WHERE inv.trans_date >='".$start_date."' AND inv.trans_date <='".$end_date."'";

      } else {

        $where =  "WHERE inv.trans_date >'1970-01-01 00:00:00' AND inv.trans_date <'9999-04-13 00:00:00'";
      }
// echo '<pre>'.
     $query ="SELECT *,
                (case when inv.trans_inventory < 0 then -inv.trans_inventory ELSE 0 end) as xuat_kho, 
                (case when inv.trans_inventory > 0 then inv.trans_inventory ELSE 0 end) as nhap_kho 
        FROM ".$inventory." AS inv 
        ".$where." AND inv.location_id IN(".$arrParams['location_ids'].") ORDER BY inv.trans_date ASC";
// die;
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
            $where =  "WHERE inv.date <'".$start_date."' AND inv.options = ".$arrParams['options']." AND inv.customer_id = ".$arrParams['customer_id']." AND inv.deleted = 0";

      } else {
        $where =  "WHERE inv.date <'1970-01-01 00:00:00'";
      }

      $query = "SELECT SUM(inv.traninvction_amount) as TONG_TIEN_GIAO_DICH
                  FROM ".$store_accounts." AS inv 
                  ".$where."";

      $result = $this->db->query($query);

      $this->db->flush_cache();

      return $result;
    }

    public function CHI_TIET_ton_dau_san_pham($arrParams = null, $options = null){

      $paginator = isset($arrParams['paginator'])?$arrParams['paginator']:null;

      $inventory = $this->db->dbprefix('inventory');
      $locations = $this->db->dbprefix('locations');
      $items = $this->db->dbprefix('items');
      // echo '<pre>'.
      $query = " SELECT * FROM
                  (
                      (
                          (
                              SELECT *,inv.trans_items AS ID_SAN_PHAM,SUM(inv.trans_inventory) as ton_dau_ky
                              FROM ".$inventory." AS inv 
                              WHERE inv.bat_dau = 1 AND inv.trans_items = ".$arrParams['id_san_pham']."  AND inv.deleted = 0 AND inv.location_id IN(".$arrParams['location_ids'].")
                          ) 
                          AS BANG_LOC_SAN_PHAM
                              
                          INNER JOIN
                          
                              (
                                  SELECT GROUP_CONCAT(name) as TEN_KHO FROM ".$locations." WHERE location_id IN(".$arrParams['location_ids'].")
                              ) 
                              
                          AS BANG_LOC_KHACH
                          INNER JOIN
                          
                              (
                                  SELECT item_id AS ID_SAN_PHAM_2,product_id as MA_SAN_PHAM, name as item_name FROM ".$items."
                              ) 
                              
                          AS BANG_LOC_TEN ON BANG_LOC_TEN.ID_SAN_PHAM_2 = BANG_LOC_SAN_PHAM.ID_SAN_PHAM
                      )
                  )";
                  // die;
     
      $result_tmp = $this->db->query($query)->result_array();

      $this->db->flush_cache();

    
  
      return isset($result_tmp)?$result_tmp:0;

    }

    public function TONG_HOP_ton_dau_san_pham($arrParams = null) {
    
      $customers = $this->db->dbprefix('customers');
      $items = $this->db->dbprefix('items');
      $inventory = $this->db->dbprefix('inventory');

      # Lọc ra ngày cuối cùng thực hiện hóa đơn
      if(!empty($arrParams['start_date']) && !empty($arrParams['end_date'])) {
            $start_date =date('Y-m-d 00:00:00',strtotime($arrParams['start_date']));
            $end_date =date('Y-m-d 23:59:59',strtotime($arrParams['end_date']));
            $MIN_hay_MAX = "MAX(sa.sno) as lay_balance_cuoi_ngay";
            $where =  "WHERE sa.date >'1970-01-01 00:00:00' AND sa.date <'".$end_date."'";

      } else {
        $MIN_hay_MAX = "MAX(sa.sno) as lay_balance_cuoi_ngay";
        $where =  "WHERE sa.date >'1970-01-01 00:00:00' AND sa.date <'3000-04-13 00:00:00'";
      }
      // echo '<pre>'.
   $query = "   SELECT * FROM 
            (
                (   
                     SELECT items.deleted,items.item_id AS ID_SAN_PHAM_1,name as ten_san_pham,product_id as ma_san_pham,cost_price as gia_von from ".$items." items 
					 WHERE items.deleted = 0

                ) 
                 AS BANG_LOC_SAN_PHAM
            INNER JOIN
                (
                    
					SELECT location_id,trans_inventory as ton_dau_ky,ivn.trans_items as ID_SAN_PHAM_2
					FROM ".$inventory." AS ivn 
                    WHERE ivn.bat_dau = 1 AND location_id IN(".$arrParams['location_ids'].")
                    GROUP BY ID_SAN_PHAM_2
                ) 
            AS BANG_SAN_PHAM ON BANG_SAN_PHAM.ID_SAN_PHAM_2 = BANG_LOC_SAN_PHAM.ID_SAN_PHAM_1
            ) ";
// die;
      $result_tmp = $this->db->query($query)->result_array();
      $this->db->flush_cache();

      return isset($result_tmp)?$result_tmp:0;
    }


     public function TONG_HOP_xuat_nhap_san_pham($arrParams = null) {
    
      $customers = $this->db->dbprefix('customers');
      $items = $this->db->dbprefix('items');
      $inventory = $this->db->dbprefix('inventory');

      # Lọc ra ngày cuối cùng thực hiện hóa đơn
      # Lọc ra ngày cuối cùng thực hiện hóa đơn
      if(!empty($arrParams['start_date']) && !empty($arrParams['end_date'])) {
            $start_date =date('Y-m-d 00:00:00',strtotime($arrParams['start_date']));
            $end_date =date('Y-m-d 23:59:59',strtotime($arrParams['end_date']));
            $where =  "WHERE inv.trans_date <='".$end_date."' AND inv.trans_date >='".$start_date."'";

      } else {
        $where =  "WHERE inv.trans_date <'3000-01-01 00:00:00'";
      }
      // echo '<pre>'.
      $query = "
              SELECT *,
	                SUM(case when inv.trans_inventory < 0 then -inv.trans_inventory ELSE 0 end) as xuat_kho, 
	                SUM(case when inv.trans_inventory > 0 then inv.trans_inventory ELSE 0 end) as nhap_kho 
	        FROM ".$inventory." AS inv 
	        ".$where." AND bat_dau <> 1 AND inv.location_id IN(".$arrParams['location_ids'].") GROUP BY trans_items
                 ";

  
// die;
      $result_tmp = $this->db->query($query)->result_array();
      $this->db->flush_cache();

      return isset($result_tmp)?$result_tmp:0;
    }

    public function TONG_HOP_xuat_nhap_san_pham_before($arrParams = null) {
    
      $customers = $this->db->dbprefix('customers');
      $items = $this->db->dbprefix('items');
      $inventory = $this->db->dbprefix('inventory');

      # Lọc ra ngày cuối cùng thực hiện hóa đơn
      # Lọc ra ngày cuối cùng thực hiện hóa đơn
      if(!empty($arrParams['start_date']) && !empty($arrParams['end_date'])) {
            $start_date =date('Y-m-d 00:00:00',strtotime($arrParams['start_date']));
            $end_date =date('Y-m-d 23:59:59',strtotime($arrParams['end_date']));
            $where =  "WHERE inv.trans_date <'".$start_date."'";

      } else {
        $where =  "WHERE inv.trans_date <'1970-01-01 00:00:00'";
      }
      $query = "
              SELECT inv.trans_items,
                  SUM(inv.trans_inventory) as TONG_SO_LUONG_XUAT_NHAP 
          FROM ".$inventory." AS inv 
          ".$where." AND bat_dau <> 1 AND inv.location_id IN(".$arrParams['location_ids'].") GROUP BY trans_items
                 ";

  
      $result_tmp = $this->db->query($query)->result_array();
      $this->db->flush_cache();

      return isset($result_tmp)?$result_tmp:0;
    }

    public function CHI_TIET_GIAO_DICH_tung_san_pham($arrParams = null, $options = null){
      $paginator = isset($arrParams['paginator'])?$arrParams['paginator']:null;

      $items = $this->db->dbprefix('items');
      $inventory = $this->db->dbprefix('inventory');
      $locations = $this->db->dbprefix('locations');

     # Lọc ra ngày cuối cùng thực hiện hóa đơn
      if(!empty($arrParams['start_date']) && !empty($arrParams['end_date'])) {
            $start_date =date('Y-m-d 00:00:00',strtotime($arrParams['start_date']));
            $end_date =date('Y-m-d 23:59:59',strtotime($arrParams['end_date']));

            $where =  "WHERE inv.trans_date >='".$start_date."' AND inv.trans_date <='".$end_date."'";

      } else {
        $where =  "WHERE inv.trans_date >'1970-01-01 00:00:00' AND inv.trans_date <'9999-04-13 00:00:00'";
      }
// echo '<pre>'.
     $query =" SELECT * FROM
                  (
                      (
                          (
                            SELECT inv.trans_comment,inv.trans_date,inv.trans_items as ID_SAN_PHAM,inv.bat_dau,inv.location_id AS DIA_DIEM,
                                (case when inv.trans_inventory < 0 then -inv.trans_inventory ELSE 0 end) as xuat_kho, 
                                (case when inv.trans_inventory > 0 then inv.trans_inventory ELSE 0 end) as nhap_kho 
                            FROM ".$inventory." AS inv 
                            ".$where." AND inv.trans_items = ".$arrParams['id_san_pham']." AND inv.deleted = 0 AND inv.location_id IN(".$arrParams['location_ids'].") AND inv.bat_dau <> 1 ORDER BY inv.trans_date ASC
                          ) 
                          AS BANG_LOC_SAN_PHAM
                              
                          INNER JOIN
                          
                              (
                                  SELECT name,location_id as TEN_KHO FROM ".$locations."
                              ) 
                              
                          AS BANG_LOC_DIA_DIEM ON BANG_LOC_DIA_DIEM.TEN_KHO = BANG_LOC_SAN_PHAM.DIA_DIEM
                          INNER JOIN
                          
                              (
                                  SELECT item_id AS ID_SAN_PHAM_2,product_id as MA_SAN_PHAM FROM ".$items."
                              ) 
                              
                          AS BANG_LOC_TEN ON BANG_LOC_TEN.ID_SAN_PHAM_2 = BANG_LOC_SAN_PHAM.ID_SAN_PHAM
                      )
                  )";
        // die;
      $result = $this->db->query($query)->result_array();

      $this->db->flush_cache();

      return $result;
    }


    public function TONG_GIAO_DICH_theo_tung_san_pham($arrParams = null, $options = null) {
      $paginator = isset($arrParams['paginator'])?$arrParams['paginator']:null;


      $inventory = $this->db->dbprefix('inventory');

      # Lọc ra ngày cuối cùng thực hiện hóa đơn
      if(!empty($arrParams['start_date']) && !empty($arrParams['end_date'])) {
            $start_date =date('Y-m-d 00:00:00',strtotime($arrParams['start_date']));
            $end_date =date('Y-m-d 23:59:59',strtotime($arrParams['end_date']));
            $where =  "WHERE inv.trans_date <'".$start_date."' AND inv.trans_items = ".$arrParams['id_san_pham']." AND inv.deleted = 0 AND inv.bat_dau = 0 AND inv.location_id IN(".$arrParams['location_ids'].")" ;

      } else {
        $where =  "WHERE inv.trans_date <'1970-01-01 00:00:00'";
      }

      $query = "SELECT SUM(inv.trans_inventory) as TONG_HOP_XUAT_NHAP
                  FROM ".$inventory." AS inv 
                  ".$where."";
             
      $result = $this->db->query($query)->result_array();

      $this->db->flush_cache();

      return $result;
    }


    public function lay_tong_xuat_nhap_theo_tung_san_pham($arrParams = null, $options = null) {

      $paginator = isset($arrParams['paginator'])?$arrParams['paginator']:null;

      $items = $this->db->dbprefix('items');
      $inventory = $this->db->dbprefix('inventory');

      # Lọc ra ngày cuối cùng thực hiện hóa đơn
      if(!empty($arrParams['start_date']) && !empty($arrParams['end_date'])) {
            $start_date =date('Y-m-d 00:00:00',strtotime($arrParams['start_date']));
            $end_date =date('Y-m-d 23:59:59',strtotime($arrParams['end_date']));
            $where =  "WHERE inv.trans_date <='".$end_date."' AND inv.trans_date >='".$start_date."'";

      } else {
        $where =  "WHERE inv.trans_date <'3000-01-01 00:00:00'";
      }

      $query = "SELECT SUM(inv.trans_inventory) as TONG_SO_LUONG_XUAT_NHAP
                  FROM ".$inventory." AS inv 
                  ".$where."";

      $result = $this->db->query($query)->result_array();

      $this->db->flush_cache();

      return $result;
    }
}