<?php
require_once ("Report.php");
class Summary_expenses extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
			
		return array(
		array('data'=>lang('common_category'), 'align'=> 'left')	
			, array('data'=>lang('common_tax'), 'align'=> 'left')
			, array('data'=>lang('common_amount'), 'align'=> 'left')
		);
	}
	
	public function getData()
	{		
		$location_ids = self::get_selected_location_ids();
        $shift_category_id       = $this->config->item('shift_category_id');

		$this->db->select('categories.name as category, SUM(expense_amount) as expense_amount,SUM(expense_tax) as expense_tax', false);
		$this->db->from('expenses');
		$this->db->join('categories', 'categories.id = expenses.category_id','left');
		$this->db->where_in('expenses.location_id', $location_ids);
		$this->db->where('expenses.deleted', 0);
		$this->db->group_by('categories.id');
		if (isset($this->params['start_date']) && isset($this->params['end_date']))
		{
 		  $this->db->where($this->db->dbprefix('expenses').'.expense_date BETWEEN '.$this->db->escape($this->params['start_date']).' and '.$this->db->escape($this->params['end_date']));
		}
        if($shift_category_id > 0) {
            $this->db->where('category_id != ' . $shift_category_id);
        }

		$this->db->order_by('expenses.id');
		//If we are exporting NOT exporting to excel make sure to use offset and limit
		if (isset($this->params['export_excel']) && !$this->params['export_excel'])
		{
			$this->db->limit($this->report_limit);
			$this->db->offset($this->params['offset']);
		}
		return $this->db->get()->result_array();		
	}
	
	public function getSummaryData()
	{
		$location_ids = self::get_selected_location_ids();
		$this->db->select('SUM(expense_amount) as total_expenses,SUM(expense_tax) as total_taxes', false);
		$this->db->from('expenses');
		$this->db->where('deleted', 0);
		if (isset($this->params['start_date']) && isset($this->params['end_date']))
		{
 		  $this->db->where($this->db->dbprefix('expenses').'.expense_date BETWEEN '.$this->db->escape($this->params['start_date']).' and '.$this->db->escape($this->params['end_date']));
		}
		$this->db->where_in('expenses.location_id', $location_ids);
		return $this->db->get()->row_array();		
	}
	
	function getTotalRows()
	{
        $location_ids = self::get_selected_location_ids();
        $shift_category_id       = $this->config->item('shift_category_id');
		$this->db->from('expenses');
		$this->db->where('deleted', 0);
        $this->db->where_in('expenses.location_id', $location_ids);
		if (isset($this->params['start_date']) && isset($this->params['end_date']))
		{
		  $this->db->where($this->db->dbprefix('expenses').'.expense_date BETWEEN '.$this->db->escape($this->params['start_date']).' and '.$this->db->escape($this->params['end_date']));
		}

        if($shift_category_id > 0) {
            $this->db->where('category_id != ' . $shift_category_id);
        }

		$this->db->join('people', 'expenses.employee_id = people.person_id', 'left');
		$this->db->order_by('id');
		return $this->db->count_all_results();
	}

public function lay_danh_sach_thu_chi_khach_hang_theo_don_hang($arrParams){
		// var_dump($arrParams);

   		$location_ids = self::get_selected_location_ids();

   		// Lọc ra ngày cuối cùng thực hiện hóa đơn
	    if(!empty($arrParams['start_date']) && !empty($arrParams['end_date'])) {
	            $start_date =date('Y-m-d 00:00:00',strtotime($arrParams['start_date']));
	            $end_date =date('Y-m-d 23:59:59',strtotime($arrParams['end_date']));

	            $where =  "WHERE e.expense_date >='".$start_date."' AND e.expense_date <='".$end_date."' AND location_id IN (".$location_ids[0].") AND e.expense_options = 'sale' AND e.deleted = 0";

	    } else {

	        // $where =  "WHERE e.expense_date >'1970-01-01 00:00:00' AND e.expense_date <'9999-04-13 00:00:00' AND location_id IN (".$location_ids[0].") AND e.expense_options = 'sale' AND e.deleted = 0";

	         $where =  "WHERE location_id IN (".$location_ids[0].") AND e.expense_options = 'sale' AND e.deleted = 0";
	    }
	    // echo '<pre>'.
		$query = "SELECT * FROM
					(
						SELECT * FROM
						(
							(
							SELECT *,
								(case when e.expense_type < 0 then e.expense_amount ELSE 0 end) as tien_thu, 
                				(case when e.expense_type > 0 then e.expense_amount ELSE 0 end) as tien_chi  
							FROM phppos_expenses as e 
							".$where."
							
							) as ex
							INNER JOIN 
							(SELECT customer_id,sale_id as id_don_hang FROM phppos_sales WHERE customer_id = ".$arrParams['customer_id'].") as s ON s.id_don_hang = ex.sale_id
						) 
					)as ket_qua 
					INNER JOIN (SELECT last_name as ten_khach_hang,person_id as khach_hang_id FROM phppos_people) as p ON p.khach_hang_id = ket_qua.customer_id
					INNER JOIN (SELECT first_name as nhan_vien,person_id as nhan_vien_id FROM phppos_people) as p1 ON p1.nhan_vien_id = ket_qua.employee_id
					INNER JOIN (SELECT first_name as nhan_vien_phe_duyet,person_id as nhan_vien_phe_duyet_id FROM phppos_people) as p2 ON p2.nhan_vien_phe_duyet_id = ket_qua.approved_employee_id";
// die;
		$data = $this->db->query($query)->result_array();

		return $data;		
    }


   public function lay_danh_sach_thu_chi_nha_cung_cap_theo_don_hang($arrParams){
	    $suppliers = $this->db->dbprefix('suppliers');
	    $receivings = $this->db->dbprefix('receivings');
	    $expenses = $this->db->dbprefix('expenses');
	    $location_ids = self::get_selected_location_ids();

   		// Lọc ra ngày cuối cùng thực hiện hóa đơn
	    if(!empty($arrParams['start_date']) && !empty($arrParams['end_date'])) {
	            $start_date =date('Y-m-d 00:00:00',strtotime($arrParams['start_date']));
	            $end_date =date('Y-m-d 23:59:59',strtotime($arrParams['end_date']));

	            $where =  "WHERE e.expense_date >='".$start_date."' AND e.expense_date <='".$end_date."' AND location_id IN (".$location_ids[0].") AND e.expense_options = 'receiving' AND e.deleted = 0";

	    } else {

	        $where =  "WHERE location_id IN (".$location_ids[0].") AND e.expense_options = 'receiving' AND e.deleted = 0";
	    }

		// echo '<pre>'.
		$query = "SELECT * FROM
					(
						SELECT * FROM
						(
							(
							SELECT *,
								(case when e.expense_type < 0 then e.expense_amount ELSE 0 end) as tien_thu, 
                				(case when e.expense_type > 0 then e.expense_amount ELSE 0 end) as tien_chi  
							FROM ".$expenses." as e 
							".$where."
							
							) as ex
							INNER JOIN 
							(SELECT supplier_id,receiving_id as id_don_hang FROM ".$receivings." WHERE supplier_id = ".$arrParams['supplier_id'].") as s ON s.id_don_hang = ex.receiving_id
						) 
					)as ket_qua 
					INNER JOIN (SELECT 	company_name as ten_nha_cung_cap,person_id as nha_cung_cap_id FROM ".$suppliers.") as s ON s.nha_cung_cap_id = ket_qua.supplier_id
					INNER JOIN (SELECT first_name as nhan_vien,person_id as nhan_vien_id FROM phppos_people) as p1 ON p1.nhan_vien_id = ket_qua.employee_id
					INNER JOIN (SELECT first_name as nhan_vien_phe_duyet,person_id as nhan_vien_phe_duyet_id FROM phppos_people) as p2 ON p2.nhan_vien_phe_duyet_id = ket_qua.approved_employee_id";
// die;
		$data = $this->db->query($query)->result_array();

		return $data;		
    }

    public function lay_danh_sach_thu_chi_noi_bo($arrParams){
	    $suppliers = $this->db->dbprefix('suppliers');
	    $receivings = $this->db->dbprefix('receivings');
	    $expenses = $this->db->dbprefix('expenses');
   		// Lọc ra ngày cuối cùng thực hiện hóa đơn
	    if(!empty($arrParams['start_date']) && !empty($arrParams['end_date'])) {
	            $start_date =date('Y-m-d 00:00:00',strtotime($arrParams['start_date']));
	            $end_date =date('Y-m-d 23:59:59',strtotime($arrParams['end_date']));

	            $where =  "WHERE e.expense_date >='".$start_date."' AND e.expense_date <='".$end_date."' AND location_id IN (".$arrParams['location_ids'].") AND e.expense_options = 'other' AND e.deleted = 0";

	    } else {

	        $where =  "WHERE location_id IN (".$arrParams['location_ids'].") AND e.expense_options = 'other' AND e.deleted = 0";
	    }

		// echo '<pre>'.
		$query = "SELECT * FROM
					(
						SELECT * FROM
						(
							(
							SELECT *,
								(case when e.expense_type < 0 then e.expense_amount ELSE 0 end) as tien_thu, 
			    				(case when e.expense_type > 0 then e.expense_amount ELSE 0 end) as tien_chi  
							FROM ".$expenses." as e 
							".$where."
							)
						) as ex
					)as ket_qua 
					INNER JOIN (SELECT first_name as nhan_vien,person_id as nhan_vien_id FROM phppos_people) as p1 ON p1.nhan_vien_id = ket_qua.employee_id
					INNER JOIN (SELECT first_name as nhan_vien_phe_duyet,person_id as nhan_vien_phe_duyet_id FROM phppos_people) as p2 ON p2.nhan_vien_phe_duyet_id = ket_qua.approved_employee_id ORDER BY id ASC";
		// die;
		$data = $this->db->query($query)->result_array();

		return $data;		
    }
	
}
?>