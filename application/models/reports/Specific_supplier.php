<?php
    require_once ("Report.php");
    class Specific_supplier extends Report
    {
        function __construct()
        {		
            parent::__construct();
        }
        
        public function getDataColumns()
        {
            
            $return = array();
            
            $return['summary']   = array();
            $return['details']   = array();
            $return['expenses']  = array();
            
            $return['summary'][] = array('data'=>lang('ma_don_hang'), 'align'=> 'left');
            $return['summary'][] = array('data'=>lang('common_time'), 'align'=> 'left');
            $return['summary'][] = array('data'=>lang('reports_employees'), 'align'=> 'left');
            $return['summary'][] = array('data'=>lang('reports_total'), 'align'=> 'left');
            $return['summary'][] = array('data'=>lang('reports_discounts'), 'align'=> 'left');
            $return['summary'][] = array('data'=>lang('reports_tax'), 'align'=> 'right');
            $return['summary'][] = array('data'=>lang('reports_total_with_tax'), 'align'=> 'right');
            $return['summary'][] = array('data'=>lang('reports_payment_type'), 'align'=> 'left');
            
            $return['details'][] = array('data'=>lang('reports_product_id'), 'align'=> 'left');
            $return['details'][] = array('data'=>lang('reports_item_name'), 'align'=> 'left');
            $return['details'][] = array('data'=>lang('reports_measure_purchased'), 'align'=> 'left');
            $return['details'][] = array('data'=>lang('reports_quantity'), 'align'=> 'left');
            $return['details'][] = array('data'=>lang('reports_total'), 'align'=> 'left');
            $return['details'][] = array('data'=>lang('reports_discounts'), 'align'=> 'left');
            $return['details'][] = array('data'=>lang('reports_total_money'), 'align'=> 'right');
            
            $return['expenses'][] = array('data'=>lang('reports_expenses_description'), 'align'=> 'left');
            $return['expenses'][] = array('data'=>lang('reports_expenses_type'), 'align'=> 'left');
            $return['expenses'][] = array('data'=>lang('reports_expenses_money'), 'align'=> 'left');
            $return['expenses'][] = array('data'=>lang('reports_expenses_tax'), 'align'=> 'left');
            
            return $return;		
        }
        
         public function getData($arrParams = array())
        {
        $limit    = '0,100000';
            $page     = !empty($arrParams['page'])?$arrParams['page']:1;
            $per_page = !empty($arrParams['per_page'])?$arrParams['per_page']:10000;
            $offset   = ($page - 1)*$per_page;
            $sup_id   = $this->params['supplier_id'];
            if(!empty($page) && !empty($per_page) )
            {
                $limit =  "$offset,$per_page";
                
            }
            $data['summary'] = array();
            $data['details'] = array();
            
            // Create a table which contains all id in 	receivings_items_temp												 
            $this->db->query('         CREATE TEMPORARY TABLE IF NOT EXISTS '.$this->db->dbprefix('list_receiving_id').'
            (SELECT                DISTINCT '.$this->db->dbprefix('receivings_items_temp').'.receiving_id AS receiving_id
            FROM                   '.$this->db->dbprefix('receivings_items_temp').'
            WHERE                  '.$this->db->dbprefix('receivings_items_temp').'.deleted = 0)');
            
            // get infomation for reporting supplier in details
            $query1 = $this->db->query('SELECT        
            tbl_reports_supplier_money.receiving_id      AS receiving_id,
            tbl_reports_supplier_money.subtotal          AS subtotal,
            tbl_reports_supplier_money.total             AS total,
            tbl_reports_supplier_money.tax               AS tax,
            tbl_reports_supplier_money.total_discount    AS total_discount,
            tbl_reports_supplier_money.total_after_all   AS total_after_all,
            tbl_reports_supplier_money.expense           AS expense,
            tbl_reports_supplier_money.emloyee_name      AS emloyee_name,
            tbl_reports_supplier_money.receiving_time    AS receiving_time,
            tbl_receiving_payments.payment_type          AS payment_type,
            tbl_receiving_payments.transaction_amount    AS transaction_amount
            
            FROM
            (SELECT         '.$this->db->dbprefix('receivings_items_temp').'.receiving_id ,
            CONCAT('.$this->db->dbprefix('people').'.last_name,\' \','.$this->db->dbprefix('people').'.first_name) AS emloyee_name,
            receiving_time, 
            SUM(100*subtotal/(100-discount_percent)) AS subtotal, 
            SUM(total) AS total,
            SUM(tax) AS tax, 
            SUM(100*subtotal/(100-discount_percent)-subtotal) AS total_discount,
            IFNULL(expense,0) AS expense, 
            (SUM(total)+IFNULL(expense,0)) AS total_after_all	 																						
            
            FROM            '.$this->db->dbprefix('receivings_items_temp').' 
            LEFT JOIN       (SELECT receiving_id,SUM((expense_amount+expense_tax*expense_amount)*expense_type) AS expense  
            FROM (SELECT * FROM '.$this->db->dbprefix('expenses').' WHERE expense_options = \'receiving\') tbl_expenses_receiving
            GROUP BY tbl_expenses_receiving.receiving_id) AS receiving_expenses 
            ON              '.$this->db->dbprefix('receivings_items_temp').'.receiving_id = receiving_expenses.receiving_id 
            
            INNER JOIN      '.$this->db->dbprefix('people').'  
            ON              '.$this->db->dbprefix('people').'.person_id = '.$this->db->dbprefix('receivings_items_temp').'.employee_id 
            
            WHERE           '.$this->db->dbprefix('receivings_items_temp').'.deleted = 0
            GROUP BY        '.$this->db->dbprefix('receivings_items_temp').'.receiving_id
            
            LIMIT '.$limit.')
            AS               tbl_reports_supplier_money
            
            INNER JOIN       (SELECT           '.$this->db->dbprefix('receivings_transactions').'.recv_id AS receiving_id,
            GROUP_CONCAT(payment_type  SEPARATOR \' ,\') AS payment_type,
            GROUP_CONCAT(transaction_amount) AS transaction_amount
            FROM             '.$this->db->dbprefix('receivings_transactions').'
            WHERE           	'.$this->db->dbprefix('receivings_transactions').'.recv_id IN (SELECT receiving_id FROM '.$this->db->dbprefix('list_receiving_id').'  )
            AND              '.$this->db->dbprefix('receivings_transactions').'.supplier_id = \''.$sup_id.'\'
            GROUP BY          '.$this->db->dbprefix('receivings_transactions').'.recv_id) AS tbl_receiving_payments 
            
            ON               tbl_reports_supplier_money.receiving_id =  tbl_receiving_payments.receiving_id');
            
            //query for overall_summary_data
            $query2 = $this->db->query('SELECT        tbl_reports_supplier_money.receiving_id     AS receiving_id,
            tbl_reports_supplier_money.subtotal         AS subtotal,
            tbl_reports_supplier_money.total            AS total,
            tbl_reports_supplier_money.tax              AS tax,
            tbl_reports_supplier_money.total_discount   AS total_discount,
            tbl_reports_supplier_money.total_after_all  AS total_after_all,
            tbl_receiving_payments.payment_type         AS payment_type,
            tbl_receiving_payments.transaction_amount   AS transaction_amount
            FROM
            
            
            (SELECT        '.$this->db->dbprefix('receivings_items_temp').'.receiving_id AS receiving_id,
            SUM(100*subtotal/(100-discount_percent)) AS subtotal, 
            SUM(total) AS total,
            SUM(tax) AS tax, 
            SUM(100*subtotal/(100-discount_percent)-subtotal) AS total_discount,
            IFNULL(expense,0) AS expense, 
            (SUM(total)+IFNULL(expense,0)) AS total_after_all	 																						
            
            FROM            '.$this->db->dbprefix('receivings_items_temp').' 
            LEFT JOIN       (SELECT receiving_id,SUM((expense_amount+expense_tax*expense_amount)*expense_type) AS expense  
            FROM (SELECT * FROM '.$this->db->dbprefix('expenses').' WHERE expense_options = \'receiving\') tbl_expenses_receiving
            GROUP BY tbl_expenses_receiving.receiving_id) AS receiving_expenses 
            ON              '.$this->db->dbprefix('receivings_items_temp').'.receiving_id = receiving_expenses.receiving_id 
            
            INNER JOIN      '.$this->db->dbprefix('people').'  
            ON              '.$this->db->dbprefix('people').'.person_id = '.$this->db->dbprefix('receivings_items_temp').'.employee_id 
            
            WHERE           '.$this->db->dbprefix('receivings_items_temp').'.deleted = 0
            GROUP BY        '.$this->db->dbprefix('receivings_items_temp').'.receiving_id) 
            
            AS               tbl_reports_supplier_money
            
            INNER JOIN       (SELECT           '.$this->db->dbprefix('receivings_transactions').'.recv_id AS receiving_id,
            GROUP_CONCAT(payment_type  SEPARATOR \' ,\') AS payment_type,
            GROUP_CONCAT(transaction_amount) AS transaction_amount
            FROM             '.$this->db->dbprefix('receivings_transactions').'
            WHERE           	'.$this->db->dbprefix('receivings_transactions').'.recv_id IN (SELECT receiving_id FROM '.$this->db->dbprefix('list_receiving_id').'  )
            AND              '.$this->db->dbprefix('receivings_transactions').'.supplier_id = \''.$sup_id.'\'
            GROUP BY          '.$this->db->dbprefix('receivings_transactions').'.recv_id) AS tbl_receiving_payments 
            
            ON               tbl_reports_supplier_money.receiving_id =  tbl_receiving_payments.receiving_id
            
            '); 
            
		 	$results = $query1->result_array();
			
			$data['overall_summary_data'] = $query2->result_array();
			foreach($results as $result)
			{
				$data['summary'][$result['receiving_id']] = $result;
				$query3 = $this->db->query('SELECT    '.$this->db->dbprefix('items').'.product_id AS item_product_id,
                '.$this->db->dbprefix('items').'.name AS item_name,
                '.$this->db->dbprefix('measures').'.name AS measure_name,
                '.$this->db->dbprefix('receivings_items_temp').'.quantity_purchased,
                ('.$this->db->dbprefix('receivings_items_temp').'.quantity_purchased * '.$this->db->dbprefix('receivings_items_temp').'.item_unit_price) AS subtotal,
                ('.$this->db->dbprefix('receivings_items_temp').'.discount_percent *'.$this->db->dbprefix('receivings_items_temp').'.quantity_purchased * '.$this->db->dbprefix('receivings_items_temp').'.item_unit_price/100) AS total_discount,
                '.$this->db->dbprefix('receivings_items_temp').'.total
                
                FROM       '.$this->db->dbprefix('receivings_items_temp').'
                
                INNER JOIN '.$this->db->dbprefix('items').'
                ON         '.$this->db->dbprefix('items').'.item_id = '.$this->db->dbprefix('receivings_items_temp').'.item_id
                
                LEFT JOIN '.$this->db->dbprefix('measures').'
                ON         '.$this->db->dbprefix('measures').'.id =  '.$this->db->dbprefix('items').'.measure_id
                
                WHERE      receiving_id = '.$result['receiving_id']
                );
                
				$data['details'][$result['receiving_id']] = $query3->result_array();
                
            }
			return $data;
            
            
        }
        
        public function getTotalRows()
        {		
            $this->db->select("COUNT(DISTINCT(receiving_id)) as total");
            $this->db->from('receivings_items_temp');
            if (isset($this->params['supplier_id']) && $this->params['supplier_id']!= -1)
            {
                $supplier_id = $this->params['supplier_id'];
                $this->db->where('receivings_items_temp.supplier_id',$supplier_id);
            }	
            if (!empty($this->params['sale_type']) && $this->params['sale_type']!= 'all')
            {
                $sale_type = $this->params['sale_type'];
                if($sale_type == 'sales')
                {
                    $return = 0;
                }
                elseif($sale_type == 'returns')
                {
                    $return = 1;
                }
                $this->db->where('receivings_items_temp.return',$return);
            }
            
            
            $this->db->where('receivings_items_temp.deleted', 0);
            $ret = $this->db->get()->row_array();
            return $ret['total'];
        }
        
        
        
        public function getSummaryData()
        {
            $this->db->select('sum(subtotal) as subtotal, sum(total) as total, sum(tax) as tax, sum(profit) as profit', false);
            $this->db->from('sales_items_temp');
            $this->db->where('sale_date BETWEEN '. $this->db->escape($this->params['start_date']). ' and '. $this->db->escape($this->params['end_date']).' and supplier_id='.$this->db->escape($this->params['supplier_id']));
            
            if ($this->params['sale_type'] == 'sales')
            {
                $this->db->where('quantity_purchased > 0');
            }
            elseif ($this->params['sale_type'] == 'returns')
            {
                $this->db->where('quantity_purchased < 0');
            }
            $this->db->where('deleted', 0);
            
            if ($this->config->item('hide_store_account_payments_from_report_totals'))
            {
                $this->db->where('store_account_payment', 0);
            }
            
            $this->db->group_by('sale_id');
            
            $return = array(
			'subtotal' => 0,
			'total' => 0,
			'tax' => 0,
			'profit' => 0,
            );
            
            foreach($this->db->get()->result_array() as $row)
            {
                $return['subtotal'] += to_currency_no_money($row['subtotal'],2);
                $return['total'] += to_currency_no_money($row['total'],2);
                $return['tax'] += to_currency_no_money($row['tax'],2);
                $return['profit'] += to_currency_no_money($row['profit'],2);
            }
            
            if(!$this->Employee->has_module_action_permission('reports','show_profit',$this->Employee->get_logged_in_employee_info()->person_id))
            {
                unset($return['profit']);
            }
            return $return;		
            
        }
    }
?>