<?php
require_once ("Report.php");
class Summary_suppliers extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		$columns = array();
		$columns[] = array('data'=>lang('reports_supplier'), 'align'=> 'left');
		$columns[] = array('data'=>lang('reports_subtotal'), 'align'=> 'right');
		$columns[] = array('data'=>lang('common_tax'), 'align'=> 'right');
		$columns[] = array('data'=>lang('reports_total'), 'align'=> 'right');
		return $columns;		
	}
	
	public function getData($arrParams = array())
	{
		$limit    = '0,100000';
		$page     = !empty($arrParams['page'])?$arrParams['page']:1;
		$per_page = $arrParams['per_page'];
		$offset   = ($page - 1)*$per_page;
		if(!empty($page) && !empty($per_page) )
		{
			$limit =  "$offset,$per_page";
			
		}
			$query1 = $this->db->query('SELECT tbl_sum.subtotal, tbl_sum.total, tbl_sum.tax, tbl_sum.total_discount, '.$this->db->dbprefix('suppliers').'.company_name
				                         FROM 
																 (
			                            SELECT    '.$this->db->dbprefix('receivings_items_temp').'.supplier_id AS supplier_id,
			                                      SUM(100*subtotal/(100-discount_percent)) AS subtotal, 
																						SUM(total) AS total,
																						SUM(tax) AS tax, 
																						SUM(100*subtotal/(100-discount_percent)-subtotal) AS total_discount
															    FROM         '.$this->db->dbprefix('receivings_items_temp').'
																 
																  WHERE        '.$this->db->dbprefix('receivings_items_temp').'.deleted = 0
																  GROUP BY     '.$this->db->dbprefix('receivings_items_temp').'.supplier_id
																	
																 ) 
																 AS tbl_sum
																 
																 INNER JOIN  '.$this->db->dbprefix('suppliers').' 
																 ON          '.$this->db->dbprefix('suppliers').'.person_id = tbl_sum.supplier_id
																  
																LIMIT '.$limit);

		 	$results['summary'] = $query1->result_array();
			$query2 = $this->db->query('SELECT tbl_sum.subtotal, tbl_sum.total, tbl_sum.tax, tbl_sum.total_discount
				                         FROM 
																 (
			                            SELECT    '.$this->db->dbprefix('receivings_items_temp').'.supplier_id AS supplier_id,
																	          SUM(100*subtotal/(100-discount_percent)) AS subtotal, 
																						SUM(total) AS total,
																						SUM(tax) AS tax, 
																						SUM(100*subtotal/(100-discount_percent)-subtotal) AS total_discount
															    FROM         '.$this->db->dbprefix('receivings_items_temp').'
																 
																  WHERE        '.$this->db->dbprefix('receivings_items_temp').'.deleted = 0
																  GROUP BY     '.$this->db->dbprefix('receivings_items_temp').'.supplier_id
																	
																 ) 
																 AS tbl_sum
																 
																 INNER JOIN  '.$this->db->dbprefix('suppliers').' 
																 ON          '.$this->db->dbprefix('suppliers').'.person_id = tbl_sum.supplier_id 
																');
					
			$results['overall_summary_data'] = $query2->result_array();

			return $results;
	}
			
	
	function getTotalRows($arrParams = array())
	{

			$query = $this->db->query('SELECT COUNT('.$this->db->dbprefix('suppliers').'.company_name) AS count_company 
				                         FROM 
																 (
			                            SELECT    '.$this->db->dbprefix('receivings_items_temp').'.supplier_id AS supplier_id,
			                                      SUM(100*subtotal/(100-discount_percent)) AS subtotal, 
																						SUM(total) AS total,
																						SUM(tax) AS tax, 
																						SUM(100*subtotal/(100-discount_percent)-subtotal) AS total_discount
															    FROM         '.$this->db->dbprefix('receivings_items_temp').'
																 
																  WHERE        '.$this->db->dbprefix('receivings_items_temp').'.deleted = 0
																  GROUP BY     '.$this->db->dbprefix('receivings_items_temp').'.supplier_id
																	
																 ) 
																 AS tbl_sum
																 
																 INNER JOIN  '.$this->db->dbprefix('suppliers').' 
																 ON          '.$this->db->dbprefix('suppliers').'.person_id = tbl_sum.supplier_id
																 
																 ');

		 	$results = $query->result_array();
			return 	$results[0]['count_company'];
	}
	public function getSummaryData()
	{
		// implements abstract method
	}
}
?>