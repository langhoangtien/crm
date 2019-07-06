<?php
class Util extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Item');
        $this->load->model('Item_location');
    }

    public function dump_items()
    {
        echo "--- START\n";
        for ($i = 1; $i <= 10000; $i ++) {
            $item_data = array(
                'name' => 'PRODUCT_AUTO_A#' . $i,
                'description' => 'Description ...',
                'tax_included' => 0,
                'category_id' => 20,
    //             'measure_id' => $measureId,
                'measure_converted' => 0,
    //             'size' => $this->input->post('size'),
    //             'expire_days' => $this->input->post('expire_days') ? $this->input->post('expire_days') : NULL,
    //             'supplier_id' => $this->input->post('supplier_id') == -1 || $this->input->post('supplier_id') == '' ? null : $this->input->post('supplier_id'),
                'item_number' => 'NUMBER_A#' . $i,
                'product_id' => 'PRODUCT_ID_A#' . $i,
                'cost_price' => '100000',
                'change_cost_price' => 0,
                'unit_price' => '150000',
    //             'promo_price' => $this->input->post('promo_price') ? $this->input->post('promo_price') : NULL,
    //             'start_date' => $this->input->post('start_date') ? date('Y-m-d', strtotime($this->input->post('start_date'))) : NULL,
    //             'end_date' => $this->input->post('end_date') ? date('Y-m-d', strtotime($this->input->post('end_date'))) : NULL,
    //             'reorder_level' => $this->input->post('reorder_level') != '' ? $this->input->post('reorder_level') : NULL,
                'is_service' => 0,
                'allow_alt_description' => 0,
                'is_serialized' => 0,
                'override_default_tax' => 0,
                'xoay_kho' => 0,
                'stop_producing' => 0,
            );
            if ($this->Item->save($item_data)) {
                $item_id = $item_data['item_id'];
                $location_id = 32;
                $data = array(
                    'location_id' => $location_id,
                    'item_id' => $item_id,
                    'location' => 'Cadosa Xuất ăn',
                    'cost_price' => '100000',
                    'unit_price' => '150000',
                    'promo_price' => NULL,
                    'start_date' => NULL,
                    'end_date' => NULL,
                    'quantity' => 11,
                    'reorder_level' => NULL,
                    'override_default_tax' => 0,
                );
                $this->Item_location->save($data, $item_id, $location_id);
                echo '.';
            }
        }
        echo "\n--- END\n";
    }
    
    public function dump_employees()
    {
        echo "--- START\n";
        $department_id = 10;
        $group_id = 4;
        $location_data = [1, 32, 33, 34, 35];
        
        $permission_data = ['customers', 'items'];
        $permission_action_data = [
            'customers|add_update',
            'customers|delete',
            'customers|search',
            'customers|edit_store_account_balance',
            'customers|edit_customer_points',
            'customers|view_scope_owner',
            'customers|view_scope_location',
            'customers|view_scope_all',
            'items|add_update',
            'items|delete',
            'items|search',
            'items|see_cost_price',
            'items|edit_quantity',
            'items|count_inventory',
            'items|manage_categories',
            'items|manage_tags',
            'items|transfer_pending'
        ];
        for ($i = 1; $i <= 5000; $i ++) {
            $person_data = array(
                'first_name' => 'EmployeeA' . $i,
                'last_name' => '',
                'email' => 'employeea'.$i . '@gmail.com',
                'phone_number' => '',
                'address_1' => 'Ha Noi',
                'address_2' => '',
                'city' => 'Ha Noi',
                'state' => '',
                'zip' => '',
                'country' => '',
                'comments' => ''
            );
            
            $employee_data = array(
                'attribute_set_id' => 0,
                'department_id' => $department_id,
                'group_id' => $group_id,
                'username' => 'employeea' . $i,
                'password' => md5('employeea' . $i),
                'inactive' => 0,
                'reason_inactive' => NULL,
                'hire_date' => NULL,
                'employee_number' => NULL,
                'birthday' => NULL,
                'termination_date' => NULL,
                'force_password_change' => 0,
            );
            
            if ($this->Employee->save_employee($person_data, $employee_data, $permission_data, $permission_action_data, $location_data, $employee_id))
            {
                echo '.';
            }
        }
        echo "\n--- END\n";
    }
}
?>