<?php
require_once (APPPATH . "controllers/Items.php");

class BizItems extends Items
{
    protected $_paginator = array(
        'per_page' => 10,
        'uri_segment' => 3
    );
    
    protected $newStringCode;
    function __construct()
    {
        parent::__construct();
        $this->load->model('Receiving');
        $this->load->model('Item_location');
        $this->load->library('MySession');
        $this->load->model('Measure');
        $this->load->model('ItemMeasures');
        $this->load->helper('items');
        $this->load->model('Location');
        $this->load->model('Service');
        $this->load->library('Sale_lib');
        $this->load->helper('bizexcel');
        $this->load->model('Category');
        $this->load->model('BizCustomer');


        //thay đổi thông tin validate
        $this->load->library("form_validation");
        $this->form_validation->set_message('required', '%s '.lang('required'));
        $this->form_validation->set_message('is_unique', '%s không được trùng lặp.');
    }
    public function index() {
        $data = [];
        $data['categories']['0'] = 'Nhóm dịch vụ';
        $service_type = $this->Tag->get_all();
        
        $categories = $this->Category->sort_categories_and_sub_categories($this->Category->get_all_categories_and_sub_categories());
        // echo "<pre>";
        // print_r($categories);die();
        foreach ($categories as $key => $value) {
            $name = str_repeat('&nbsp;&nbsp;', $value['depth']) . $value['name'];
            $data['categories'][$key] = $name;
        }
       // var_dump($data);
//        foreach ($service_type as $key => $value) {
//            $data['categories'][$key] = $value['name'];
//        }
        $this->load->view('items/manage_v1', $data);
    }
    
    public function build_list_view()
    {
        $search = $this->input->post('search');
        $category = $this->input->post('category');
        $page = $this->input->post('page');
        
        $lowInventory = $this->input->post('low_inventory');
        $lowInventory = !empty($lowInventory) ? $lowInventory : 0;
        
        $sortBy = $this->input->post('sort_by');
        $sortBy = !empty($sortBy) ? $sortBy : 'item_id';
        $orderBy = $this->input->post('order_by');
        $orderBy = !empty($orderBy) ? $orderBy : 'DESC';
        
        $perpage = 20;
        $offset = ($page - 1) * $perpage;
        $records = [];
        $subCategories = $this->Category->get_all_categories_and_sub_categories($category);
        $category_ids = !empty($category) ? [$category] : false;
        if (!empty($category_ids) && !empty($subCategories)) {
            $category_ids = array_merge($category_ids, array_keys($subCategories));
        }
        $options = [
            'search' => [
                'options' => ['low_inventory' => $lowInventory]
            ]
        ];
        $this->Item->setOptions($options);
        
        $result = $this->Item->search($search, $category_ids, $perpage, $offset, $sortBy, $orderBy);
        if ($result) {
            $records = $result->result_array();
        }
        
        $ids = array_map(function($item){
            return $item['item_id'];
        }, $records);
        $totalItemsQty = $this->Item->getTotalQty($ids);
        $records = array_map(function($record) use ($totalItemsQty){
            $totalQty = $record['quantity'];
            foreach ($totalItemsQty as $row) {
                if ($record['item_id'] == $row['item_id']) {
                    $totalQty = $row['total_quantity'];
                    break;
                }
            }
            $record['total_quantity'] = $totalQty;
            return $record;
        }, $records);
        $totalRecords = $this->Item->search_count_all($search, $category_ids);
        $totalPage = ceil($totalRecords / $perpage);
        $pagination = [
            'total_page' => $totalPage,
            'current_page' => $page,
            'displayed_pages' => $this->getDisplayedPages($page, $totalPage)
        ];


        // echo "<pre>";
        // print_r($records);die();
        foreach ($records as $k => $val) {
            $ar_comment = $this->Service->get_document($val['product_id']);
            $ar_comment = explode(',',$ar_comment['document']);
            $data_quotes_contract = $this->BizCustomer->get_list_quotes_contract($ar_comment);
            $records[$k]['document'] = $data_quotes_contract;
        }
        $data['totalRecords'] = $totalRecords;
        $data['pagination'] = $pagination;
        $data['records'] = $records;

        $data['sortBy'] = $sortBy;
        $data['orderBy'] = $orderBy;


        $headers = [
            [
                'name' => 'STT',
                'field' => 'item_id',
                'class' => 'text-left hr-lbl',
                'style' => 'width: 50px;',
                'sortable' => true,
                'order' => 1,
            ],
            [
                'name' => 'Mã dịch vụ',
                'field' => 'product_id',
                'class' => 'text-left hr-lbl',
                'style' => '',
                'sortable' => true,
                'order' => 1,
            ],
//             [
//                 'name' => 'Mã vạch',
//                 'field' => 'item_number',
//                 'class' => 'text-left hr-lbl',
//                 'style' => '',
//                 'sortable' => true,
//                 'order' => 2,
//             ],
            
            [
                'name' => 'Tên dịch vụ',
                'field' => 'name',
                'class' => 'text-left hr-lbl',
                'style' => '',
                'sortable' => true,
                'order' => 3,
            ],
            [
                'name' => 'Nhóm dịch vụ',
                'field' => 'category',
                'class' => 'text-left hr-lbl',
                'style' => '',
                'sortable' => true,
                'order' => 4,
            ],
            
//             [
//                 'name' => $this->config->item('size') == 'remHaMy'?'Khổ vải': 'Kích thước',
//                 'field' => 'size',
//                 'class' => 'text-left hr-lbl',
//                 'style' => '',
//                 'sortable' => true,
//                 'order' => 5,
//             ],
            [
                'name' => 'Phí dự kiến',
                'field' => 'unit_price',
                'class' => 'text-left hr-lbl',
                'style' => '',
                'order' => 6,
            ],
//             [
//                 'name' => $this->config->item('company_user') == 'remHaMy' ? 'Xoay khổ' : 'Số lượng',
//                 'field' => 'quantity',
//                 'class' => 'text-left hr-lbl',
//                 'style' => '',
//                 'sortable' => false,
//                 'order' => 8,
//             ],
//             [
//                 'name' => $this->config->item('company_user') == 'remHaMy' ? 'Sản xuất' : 'Tổng số lượng',
//                 'field' => 'total_quantity',
//                 'class' => 'text-left hr-lbl',
//                 'style' => '',
//                 'sortable' => false,
//                 'order' => 9,
//             ],
            [
                'name' => 'Mẫu đính kèm',
                'field' => '',
                'class' => 'text-left hr-lbl',
                'style' => '',
                'order' => 10,
            ],
//            [
//                'name' => 'Tạo bản sao',
//                'field' => '',
//                'class' => 'text-left hr-lbl',
//                'style' => '',
//                'order' => 11,
//            ],
            [
                'name' => 'Cập nhật',
                'field' => '',
                'class' => 'text-left hr-lbl',
                'style' => '',
                'order' => 12,
            ],
            
            [
                'name' => '&nbsp;',
                'field' => '',
                'class' => 'text-left hr-lbl',
                'style' => 'width: 50px;',
                'order' => 13,
            ],
        ];
        
        if ($this->Employee->has_module_action_permission('items','see_cost_price', $this->Employee->get_logged_in_employee_info()->person_id)) {
            $headers[] = [
                'name' => 'Chi phí bên thứ ba dự kiến',
                'field' => 'cost_price',
                'class' => 'text-left hr-lbl',
                'style' => '',
                'sortable' => false,
                'order' => 7,
            ];
        }
        
        usort($headers, function($a, $b){
            return ($a['order'] < $b['order']) ? -1 : 1;
        });
        
        $data['headers'] = $headers;
        echo json_encode(array(
            'success' => true,
            'html' => $this->load->view('items/partials/list_view', $data, TRUE)
        ));
    }
    
    private function getDisplayedPages($currentPage, $totalPage)
    {
        $availablePages = [];
        if ($totalPage > 5) {
            if (in_array($currentPage, [1, 2, 3])) {
                $availablePages = range(1, 5);
            } elseif (in_array($currentPage, [$totalPage, $totalPage - 1, $totalPage - 2])) {
                $availablePages = range($totalPage - 4, $totalPage);
            } else {
                $availablePages = range($currentPage - 2, $currentPage + 2);
            }
        } else {
            $availablePages = range(1, $totalPage);
        }
        return $availablePages;
    }
    
    public function extract_not_audit_items()
    {

        $data = array();
        $count_id = $this->input->get('count_id', 0);
        $audit_items = $this->Inventory->get_items_counted($count_id, NULL, NULL);
        $auditedIds = array_map(function ($item) {
            return $item['item_id'];
        }, $audit_items);
        $extra['category_id'] = (int) $this->mysession->getValue('AUDIT_CATEGORY');
        $notAuditedItems = $this->Item->getNotAuditedInLocation($auditedIds, $extra);

        $bizExcel = new BizExcel('A1.xlsx');
        $excelContent = $bizExcel->setNumberRowStartBody(10)->setHeaderOfBody($this->getHeaderForNotAuditItems())
            ->setDataExcel($this->formattedNotAuditItems($notAuditedItems))
            ->setExtraData($this->getExtraDataForNotAuditItems())
            ->generateFile(false);
        $this->load->helper('download');
        force_download('not_audit_items.xlsx', $excelContent);
        exit;
    }

    public function getHeaderForNotAuditItems()
    {
        return array(
            array(
                'col' => 'A',
                'text' => 'STT',
                'styles' => array(
                    'color' => '75b6ed',
                    'bold' => true,
                    'is_fill' => true
                ),
                'value_field' => '__AUTO__',
            ),
            array(
                'col' => 'B',
                'text' => 'MÃ VẠCH',
                'styles' => array(
                    'color' => '75b6ed',
                    'bold' => true,
                    'is_fill' => true
                ),
                'value_field' => 'product_id',
            ),
            array(
                'col' => 'C',
                'text' => 'TÊN SẢN PHẨM',
                'styles' => array(
                    'color' => '75b6ed',
                    'bold' => true,
                    'is_fill' => true
                ),
                'value_field' => 'item_name',
            ),
            array(
                'col' => 'D',
                'text' => 'DANH MỤC',
                'styles' => array(
                    'color' => '75b6ed',
                    'bold' => true,
                    'is_fill' => true
                ),
                'value_field' => 'category_name',
            ),
            array(
                'col' => 'E',
                'text' => 'GIÁ VỐN',
                'styles' => array(
                    'color' => '75b6ed',
                    'bold' => true,
                    'is_fill' => true
                ),
                'value_field' => 'item_cost_price',
            ),
            array(
                'col' => 'F',
                'text' => 'GIÁ BÁN',
                'styles' => array(
                    'color' => '75b6ed',
                    'bold' => true,
                    'is_fill' => true
                ),
                'value_field' => 'item_unit_price',
            ),
            array(
                'col' => 'G',
                'text' => 'SỐ LƯỢNG',
                'styles' => array(
                    'color' => '75b6ed',
                    'bold' => true,
                    'is_fill' => true
                ),
                'value_field' => 'location_quantity',
            ),
        );
    }

    public function getExtraDataForNotAuditItems()
    {
        $current_location = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id());
        return [
            [
                'cell' => 'C6',
                'value' => $current_location->name
            ],
            [
                'cell' => 'C7',
                'value' => $current_location->address
            ],
            [
                'cell' => 'C8',
                'value' => date("g:i a d-m-Y")
            ],
        ];
    }

    public function formattedNotAuditItems($notAuditedItems = array())
    {
        $formattedItems = array();
        foreach ($notAuditedItems as $item) {
            $formattedItem = [];
            $formattedItem['item_name'] = $item['name'];
            $formattedItem['product_id'] = $item['product_id'];
            $formattedItem['category_name'] = $item['category'];
            $formattedItem['item_cost_price'] = NumberFormatToCurrency($item['cost_price']);
            $formattedItem['item_unit_price'] = NumberFormatToCurrency($item['unit_price']);
            $formattedItem['location_quantity'] = to_quantity($item['location_quantity']);
            $formattedItems[] = $formattedItem;
        }
        return $formattedItems;
    }


    public function history_transfer()
    {
        $data = array();
        $start_date = $this->input->get('start_date');
        if (empty($start_date)) {
            $data['start_date'] = date('d-m-Y', strtotime("-30 days"));
            $search['start_date'] = date('Y-m-d', strtotime("-30 days"));
        } else {
            $data['start_date'] = $this->input->get('start_date_formatted');
            $search['start_date'] = $this->input->get('start_date');
        }

        $end_date = $this->input->get('end_date');

        if (empty($end_date)) {
            $data['end_date'] = date('d-m-Y');
            $search['end_date'] = date('Y-m-d');
        } else {
            $data['end_date'] = $this->input->get('end_date_formatted');
            $search['end_date'] = $this->input->get('end_date');
        }
        $data['transfer_dimension'] = $search['transfer_dimension'] = $this->input->get('transfer_dimension', 'all');
        $data['history_transfers'] = $this->Receiving->getHistoryTransfers($search);
        $this->load->view('items/history_transfers', $data);
    }

    public function measures($item_id)
    {
        $measuresConverted = $this->Measure->getAvailableMeasuresByItemId($item_id);
        $measureJsonFormat = array();
        foreach ($measuresConverted as $measure) {
            $measureJsonFormat[] = array('value' => $measure['id'], 'text' => $measure['name']);
        }
        echo json_encode($measureJsonFormat);
    }

    function save($item_id = -1)
    {

        if ($this->config->item("ecommerce_platform"))
        {
            require_once (APPPATH."models/interfaces/Ecom.php");
            $ecom_model = Ecom::get_ecom_model();
            $e_new_quantity = 0;
        }
        $this->load->model('Item_taxes');
        $this->load->model('Item_location');
        $this->load->model('Item_location_taxes');

        $this->check_action_permission('add_update');
    
        $is_ecom_configured = false;
        if ($this->config->item("ecommerce_platform"))
        {
            $is_ecom_configured=$ecom_model->is_configured();
        }
        
        if (!$this->Category->exists($this->input->post('category_id'))) {
            if (!$category_id = $this->Category->get_category_id($this->input->post('category_id'))) {
                $category_id = $this->Category->save($this->input->post('category_id'));
            }
        } else {
            $category_id = $this->input->post('category_id');
        }

        // TODO XXX
        $measureId = $this->input->post('measure_id');
        $isMeasureConvert = $this->input->post('convert_measure');
        $measureData = array();
        if (!empty($isMeasureConvert)) {
            $measureData = $this->input->post('measure_converted');
        }

        $item_data = array(
            // 'attribute_set_id' => $this->input->post('attribute_set_id'),
            'name' => $this->input->post('name'),
            'description' => $this->input->post('description'),
            'tax_included' => $this->input->post('tax_included') ? $this->input->post('tax_included') : 0,
            'category_id' => $category_id,
            'measure_converted' => isset($isMeasureConvert) ? $isMeasureConvert : 0,
            'supplier_id' => $this->input->post('supplier_id') == -1 || $this->input->post('supplier_id') == '' ? null : $this->input->post('supplier_id'),
            'product_id' => $this->input->post('product_id') == '' ? null : $this->input->post('product_id'),
            'unit_price_interval' => $this->input->post('unit_price_interval'),
            'cost_price_interval' => $this->input->post('cost_price_interval'),
            'reorder_level' => $this->input->post('reorder_level') != '' ? $this->input->post('reorder_level') : NULL,
            'is_service' => $this->input->post('is_service') ? $this->input->post('is_service') : 0,
            'allow_alt_description' => $this->input->post('allow_alt_description') ? $this->input->post('allow_alt_description') : 0,
            'is_serialized' => $this->input->post('is_serialized') ? $this->input->post('is_serialized') : 0,
            'override_default_tax' => $this->input->post('override_default_tax') ? $this->input->post('override_default_tax') : 0,
            'xoay_kho' => $this->input->post('xoay_kho') ? $this->input->post('xoay_kho') : 0,
            'stop_producing' => $this->input->post('stop_producing') ? $this->input->post('stop_producing') : 0,
        );

        if ($this->input->post('override_default_commission')) {
            if ($this->input->post('commission_type') == 'fixed') {
                $item_data['commission_fixed'] = (float)$this->input->post('commission_value');
                $item_data['commission_percent_type'] = '';
                $item_data['commission_percent'] = NULL;
            } else {
                $item_data['commission_percent'] = (float)$this->input->post('commission_value');
                $item_data['commission_percent_type'] = $this->input->post('commission_percent_type');
                $item_data['commission_fixed'] = NULL;
            }
        } else {
            $item_data['commission_percent'] = NULL;
            $item_data['commission_fixed'] = NULL;
            $item_data['commission_percent_type'] = '';
        }

        $employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
        $cur_item_info = $this->Item->get_info($item_id);

        $redirect = $this->input->post('redirect');
        $sale_or_receiving = $this->input->post('sale_or_receiving');
        // var_dump($item_data));exit();
        if ($this->Item->save($item_data, $item_id)) {

            if (empty($item_id)) {
                $item_id = $item_data['item_id'];
            }

            /* Update Extended Attributes */
            if (!class_exists('Attribute')) {
                $this->load->model('Attribute');
            }
            $attributes = $this->input->post('attributes');
            if (!empty($attributes)) {
                $this->Attribute->reset_attributes(array('entity_id' => $item_id, 'entity_type' => 'items'));
                foreach ($attributes as $attribute_id => $value) {
                    $attribute_value = array('entity_id' => $item_id, 'entity_type' => 'items', 'attribute_id' => $attribute_id, 'entity_value' => $value);
                    $this->Attribute->set_attributes($attribute_value);
                }
            }
            /* End Update */

            $this->Tag->save_tags_for_item(isset($item_data['item_id']) ? $item_data['item_id'] : $item_id, $this->input->post('tags'));
            $tier_type = $this->input->post('tier_type');

            if ($this->input->post('item_tier')) {
                foreach ($this->input->post('item_tier') as $tier_id => $price_or_percent) {
                    if ($price_or_percent) {
                        $tier_data = array('tier_id' => $tier_id);
                        $tier_data['item_id'] = isset($item_data['item_id']) ? $item_data['item_id'] : $item_id;

                        if ($tier_type[$tier_id] == 'unit_price') {
                            $tier_data['unit_price'] = $price_or_percent;
                            $tier_data['percent_off'] = NULL;
                        } else {
                            $tier_data['percent_off'] = (float)$price_or_percent;
                            $tier_data['unit_price'] = NULL;
                        }

                        $this->Item->save_item_tiers($tier_data, $item_id);
                    } else {
                        $this->Item->delete_tier_price($tier_id, $item_id);
                    }

                }
            }


            $success_message = '';

            //New item
            if ($item_id == -1) {
                $success_message = lang('common_successful_adding') . ' ' . $item_data['name'];
                $this->session->set_flashdata('manage_success_message', $success_message);
                echo json_encode(array('success' => true, 'message' => $success_message, 'item_id' => $item_data['item_id'], 'redirect' => $redirect, 'sale_or_receiving' => $sale_or_receiving));
                $item_id = $item_data['item_id'];
            } else //previous item
            {
                $success_message = lang('common_items_successful_updating') . ' ' . $item_data['name'];
                $this->session->set_flashdata('manage_success_message', $success_message);
                echo json_encode(array('success' => true, 'message' => $success_message, 'item_id' => $item_id, 'redirect' => $redirect, 'sale_or_receiving' => $sale_or_receiving));
            }

            if ($this->input->post('additional_item_numbers') && is_array($this->input->post('additional_item_numbers'))) {
                $this->Additional_item_numbers->save($item_id, $this->input->post('additional_item_numbers'));
            } else {
                $this->Additional_item_numbers->delete($item_id);
            }

            if ($this->input->post('locations')) {
                foreach ($this->input->post('locations') as $location_id => $item_location_data) {
                    $override_prices = isset($item_location_data['override_prices']) && $item_location_data['override_prices'];
                    $quantity_add_minus = isset($item_location_data['quantity_add_minus']) && $item_location_data['quantity_add_minus'] ? $item_location_data['quantity_add_minus'] : 0;
                    $item_location_before_save = $this->Item_location->get_info($item_id, $location_id);
                    $new_quantity = ($item_location_before_save->quantity ? $item_location_before_save->quantity : 0) + $quantity_add_minus;

                    $data = array(
                        'location_id' => $location_id,
                        'item_id' => $item_id,
                        'location' => $item_location_data['location'],
                        'cost_price' => $override_prices && $item_location_data['cost_price'] != '' ? $item_location_data['cost_price'] : NULL,
                        'unit_price' => $override_prices && $item_location_data['unit_price'] != '' ? $item_location_data['unit_price'] : NULL,
                        'promo_price' => $override_prices && $item_location_data['promo_price'] != '' ? $item_location_data['promo_price'] : NULL,
                        'start_date' => $override_prices && $item_location_data['promo_price'] != '' && $item_location_data['start_date'] != '' ? date('Y-m-d', strtotime($item_location_data['start_date'])) : NULL,
                        'end_date' => $override_prices && $item_location_data['promo_price'] != '' && $item_location_data['end_date'] != '' ? date('Y-m-d', strtotime($item_location_data['end_date'])) : NULL,
                        'quantity' => !$this->input->post('is_service') ? $new_quantity : NULL,
                        'reorder_level' => isset($item_location_data['reorder_level']) && $item_location_data['reorder_level'] != '' ? $item_location_data['reorder_level'] : NULL,
                        'override_default_tax' => isset($item_location_data['override_default_tax']) && $item_location_data['override_default_tax'] != '' ? $item_location_data['override_default_tax'] : 0,
                    );
                    if($is_ecom_configured == true && $location_id == $ecom_model->ecommerce_store_location)
                    {
                        $e_new_quantity = $new_quantity;
                    }
                    $this->Item_location->save($data, $item_id, $location_id);


                    if (isset($item_location_data['item_tier'])) {
                        $tier_type = $item_location_data['tier_type'];

                        foreach ($item_location_data['item_tier'] as $tier_id => $price_or_percent) {
                            //If we are overriding prices and we have a price/percent, add..otherwise delete
                            if ($override_prices && $price_or_percent) {
                                $tier_data = array('tier_id' => $tier_id);
                                $tier_data['item_id'] = isset($item_data['item_id']) ? $item_data['item_id'] : $item_id;
                                $tier_data['location_id'] = $location_id;

                                if ($tier_type[$tier_id] == 'unit_price') {
                                    $tier_data['unit_price'] = $price_or_percent;
                                    $tier_data['percent_off'] = NULL;
                                } else {
                                    $tier_data['percent_off'] = (float)$price_or_percent;
                                    $tier_data['unit_price'] = NULL;
                                }

                                $this->Item_location->save_item_tiers($tier_data, $item_id, $location_id);
                            } else {
                                $this->Item_location->delete_tier_price($tier_id, $item_id, $location_id);
                            }

                        }
                    }


                    if (isset($item_location_data['tax_names'])) {
                        $location_items_taxes_data = array();
                        $tax_names = $item_location_data['tax_names'];
                        $tax_percents = $item_location_data['tax_percents'];
                        $tax_cumulatives = $item_location_data['tax_cumulatives'];
                        for ($k = 0; $k < count($tax_percents); $k++) {
                            if (is_numeric($tax_percents[$k])) {
                                $location_items_taxes_data[] = array(
                                    'name' => $tax_names[$k], 
                                    'percent' => $tax_percents[$k], 
                                    'cumulative' => isset($tax_cumulatives[$k]) ? $tax_cumulatives[$k] : '0'
                                );
                            }
                        }
                        $this->Item_location_taxes->save($location_items_taxes_data, $item_id, $location_id);
                    }


                    # Lưu dữ liệu xuất nhập kho
                    # Lưu dữ liệu xuất nhập kho
                    # Lưu dữ liệu xuất nhập kho
                    # Lưu dữ liệu xuất nhập kho
                    # Lưu dữ liệu xuất nhập kho
                    # Lưu dữ liệu xuất nhập kho
                    # Lưu dữ liệu xuất nhập kho
                    if(!empty($quantity_add_minus) || $new_quantity == 0){ # Nếu có dữ liệu mới gửi vào
                          $is_service = (bool)$this->input->post('is_service');
                          if($quantity_add_minus != 0 && !empty($item_location_before_save->quantity)){
                                $so_luong_thay_doi = $quantity_add_minus;
                                 $inv_data = array
                                (
                                    'trans_date' => date('Y-m-d H:i:s'),
                                    'trans_items' => $item_id,
                                    'trans_user' => $employee_id,
                                    'trans_comment' => 'Thêm số lượng chỉnh sửa bằng tay',
                                    'trans_inventory' => $so_luong_thay_doi,
                                    'location_id' => $location_id,
                                    'bat_dau' => 0,
                                );
                          }
                          elseif (!empty($quantity_add_minus) && empty($item_location_before_save->quantity)) {
                             $so_luong_thay_doi = $quantity_add_minus;
                             $inv_data = array
                                (
                                    'trans_date' => date('Y-m-d H:i:s'),
                                    'trans_items' => $item_id,
                                    'trans_user' => $employee_id,
                                    'trans_comment' => 'Lưu số lượng ban đầu theo địa điểm',
                                    'trans_inventory' => $so_luong_thay_doi,
                                    'location_id' => $location_id,
                                    'bat_dau' => 1,
                                );
      

                          }
                         elseif(!$is_service) {
                                $inv_data = array
                                (
                                    'trans_date' => date('Y-m-d H:i:s'),
                                    'trans_items' => $item_id,
                                    'trans_user' => $employee_id,
                                    'trans_comment' => lang('items_manually_editing_of_quantity'),
                                    'trans_inventory' => $new_quantity,
                                    'location_id' => $location_id,
                                    'bat_dau' => 1,
                                );


                        }
                        $this->Inventory->insert($inv_data);

                    }
                  
                } # end  foreach 
            }

            $items_taxes_data = array();
            $tax_names = $this->input->post('tax_names');
            $tax_percents = $this->input->post('tax_percents');
            $tax_cumulatives = $this->input->post('tax_cumulatives');
            for ($k = 0; $k < count($tax_percents); $k++) {
                if (is_numeric($tax_percents[$k])) {
                    $items_taxes_data[] = array('name' => $tax_names[$k], 'percent' => $tax_percents[$k], 'cumulative' => isset($tax_cumulatives[$k]) ? $tax_cumulatives[$k] : '0');
                }
            }
            $this->Item_taxes->save($items_taxes_data, $item_id);


            //Delete Image
            if ($this->input->post('del_image') && $item_id != -1) {
                if ($cur_item_info->image_id != null) {
                    $this->load->model('Appfile');
                    $this->Item->update_image(NULL, $item_id);
                    $this->Appfile->delete($cur_item_info->image_id);
                }
            }

            //Save Image File
            if (!empty($_FILES["image_id"]) && $_FILES["image_id"]["error"] == UPLOAD_ERR_OK) {
                $allowed_extensions = array('png', 'jpg', 'jpeg', 'gif');
                $extension = strtolower(pathinfo($_FILES["image_id"]["name"], PATHINFO_EXTENSION));

                if (in_array($extension, $allowed_extensions)) {
                    $config['image_library'] = 'gd2';
                    $config['source_image'] = $_FILES["image_id"]["tmp_name"];
                    $config['create_thumb'] = FALSE;
                    $config['maintain_ratio'] = TRUE;
                    $config['width'] = 400;
                    $config['height'] = 300;
                    $this->load->library('image_lib', $config);
                    $this->image_lib->resize();
                    $this->load->model('Appfile');
                    $image_file_id = $this->Appfile->save($_FILES["image_id"]["name"], file_get_contents($_FILES["image_id"]["tmp_name"]));
                    $this->Item->add_image(isset($item_data['item_id']) ? $item_data['item_id'] : $item_id, $image_file_id);
                }
                $this->Item->update_image($image_file_id, $item_id);
            }
            //Eccommerce
            if($is_ecom_configured == true )
            {
                $ecom_item_data = $item_data;
                $ecom_item_data['quantity'] = $e_new_quantity;
                $ecom_item_data['tags'] = explode(',', $this->input->post('tags'));
                $ecom_item_data['images'] = array();
            
                if ($item_data['is_ecommerce'])
                {
                    foreach($this->Item->get_item_images(isset($item_data['item_id']) ? $item_data['item_id'] : $item_id) as $image)
                    {
                        $ecom_item_data['images'][] = array('image_id' => $image['image_id'], 'ecommerce_image_id' => $image['ecommerce_image_id'], 'src' => app_file_url($image['image_id']), 'name' => isset($titles[$image['image_id']]) ? $titles[$image['image_id']] : '' , 'alt' => isset($alt_texts[$image['image_id']]) ? $alt_texts[$image['image_id']] : '');
                    }
            
                    $ecom_model->save_item_from_phppos_to_ecommerce($ecom_item_data, isset($item_data['item_id']) ? $item_data['item_id'] : $item_id);
                }
            }
            // TODO XXX
            $this->ItemMeasures->deleteByItemId($item_id);
            if (!empty($measureData)) {
                foreach ($measureData as $key => $measureConverted) {
            
                    $itemMeasure = array(
                        'item_id' => $item_id,
                        'measure_id' => $measureId,
                        'measure_converted_id' => $measureConverted['id'],
                        'measure_converted_id' => $measureConverted['id'],
                        'qty_converted' => $measureConverted['qty'],
                        'cost_price_percentage_converted' => $measureConverted['cost_price'],
                        'unit_price_percentage_converted' => $measureConverted['unit_price'],
                    );

                    $this->ItemMeasures->save($itemMeasure);
                }

            }
        } else //failure
        {
            echo json_encode(array('success' => false, 'message' => lang('common_error_adding_updating') . ' ' .
                $item_data['name'], 'item_id' => -1));
             redirect('items/index/');
        }
        $this->session->set_userdata(array("notice"=>"Cập nhật thành công"));
        redirect('items/index/');
    }

    function _get_item_data($item_id)
    {
        $this->load->helper('report');
        $this->load->helper('convert_formula');
        $data = array();
        $data['controller_name'] = $this->_controller_name;

        $data['item_info'] = $this->Item->get_info($item_id);
        /* Load Attribute Sets, Groups And Required Attributes */

        $this->load->model('Attribute_set');
        $this->load->model('Attribute_group');
        $this->load->model('Attribute');
		$data['attribute_sets']    = $this->Attribute_set->get_by_related_object('items');
		$data['attribute_groups']  = $this->Attribute_group->get_all()->result();
        if ($this->config->item('company_user') == 'remHaMy') {
            $CT_RK_VAI_KHO_RONG_CC_260CM = 4;
        }
        if($this->input->post('change_attribute'))
        {
            
			$data['attribute_values']  = $this->Attribute->get_entity_attributes(array('entity_id' => $item_id, 'entity_type' => 'items'));
			$attribute_values_key_code_ = $this->Attribute->get_entity_attributes(array('entity_id' => $item_id, 'entity_type' => 'items', 'key_code' =>true));
            $attribute_set_id = $this->input->post('change_attribute');

			if (!empty($data['item_info']->attribute_set_id)) {
				$data['attributes'] = $this->Attribute_set->get_attributes($data['item_info']->attribute_set_id);
			}
			
			if(!$this->input->post('dataAttributeKeyUp'))  {
				
				$data['attributes'] = $this->Attribute_set->get_attributes($attribute_set_id);
				if (!empty($data['attribute_groups'])) {
					foreach ($data['attribute_groups'] as $key => $attribute_group) {
						if (!empty($data['attributes'])) {
							foreach ($data['attributes'] as $attribute) {
								if ($attribute->attribute_group_id == $attribute_group->id) {
									$data['attribute_groups'][$key]->has_attributes = true;
								}
							}
						}
					}
				}
				// only for Rem Ha My
				
				if ($this->config->item('company_user') == 'remHaMy') {
					if ($this->input->post('line')) {
						$line = $this->input->post('line');
						if ($this->input->post('action') && $this->input->post('action') =='change_selected_attribute_set') {
							
							$itemAttributes     = $this->Attribute_set->get_attributes($attribute_set_id);
							$cartItemsAttribute = $this->sale_lib->getCartItemsAttribute();
							$cartItemsAttribute[$line] = $itemAttributes;	
							$this->sale_lib->setCartItemsAttribute($cartItemsAttribute);
						
							$cartItemsAttributeSet = $this->sale_lib->getCartItemsAttributeSet();
							$cartItemsAttributeSet[$line] = $this->input->post('change_attribute');
							$this->sale_lib->setCartItemsAttributeSet($cartItemsAttributeSet);	
						} elseif($this->input->post('action') && $this->input->post('action') =='first_load' && empty($this->sale_lib->getCartItemsAttributeSet()) ){
							$cartItemsAttributeSet = $this->sale_lib->getCartItemsAttributeSet();
							$cartItemsAttributeSet[$line] = $data['item_info']->attribute_set_id;
							$this->sale_lib->setCartItemsAttributeSet($cartItemsAttributeSet);				
						}
						$cartItemsAttributeValue  = $this->sale_lib->getCartItemsAttributeValue();
						if (!empty($cartItemsAttributeValue)) {
							$data['attribute_values'] = $cartItemsAttributeValue[$line];
                            if ($attribute_set_id == $CT_RK_VAI_KHO_RONG_CC_260CM) {
                                $data['attribute_values'][3] = new stdClass(); //attr 3 chiều cao cửa
                                $data['attribute_values'][3]->entity_value = 260;
                            }
						}
					}
				}
			} elseif ($this->input->post('dataAttributeKeyUp')) {
				$attribute_values = [];
				$attribute_set_id = $this->input->post('change_attribute');
				$data['attributes'] = $this->Attribute_set->get_attributes($attribute_set_id);
				if (!empty($data['attribute_groups'])) {
					foreach ($data['attribute_groups'] as $key => $attribute_group) {
						if (!empty($data['attributes'])) {
							foreach ($data['attributes'] as $_attribute) {
								if ($_attribute->attribute_group_id == $attribute_group->id) {
									$data['attribute_groups'][$key]->has_attributes = true;
								}
							}
						}
					}
				}
				$attribute_values_key_code = $this->input->post('dataAttributeKeyUp');
                $attribute_values_key_code['dgv'] = $data['item_info']->unit_price;
                if ($attribute_set_id == $CT_RK_VAI_KHO_RONG_CC_260CM) {
                    $attribute_values_key_code['ccc'] = 260;
                }
				foreach ($attribute_values_key_code as $code => $attributeValue ) {
					$attribute_id = $this->Attribute->get_attribute_id_by_codes($code);
					$attribute_values[$attribute_id] = new stdClass();
					if((substr(trim($attributeValue),0,1)== "=") && !empty($attributeValue)) {
					    $attribute_values[$attribute_id]->{"entity_value_formula"} = $attributeValue;
					    $convertedFormula = new ConvertFormula($attributeValue, $attribute_values_key_code);
					    $attribute_values[$attribute_id]->entity_value = $convertedFormula->executeFormula();
					} else {
						$attribute_values[$attribute_id]->entity_value = $attributeValue;
					}
				}
				$data['dataAttrGroupShowPost'] = $this->input->post('dataAttrGroupShowPost');
				$data['attribute_values']      = $attribute_values;
				
				// only for Rem Ha My
				
				if ($this->config->item('company_user') == 'remHaMy') {
					if ($this->input->post('line')) {
						$line = $this->input->post('line');
						$cartItemsAttributeValue = [];
						$cartItemsAttributeValue = $this->sale_lib->getCartItemsAttributeValue();
						$cartItemsAttributeValue[$line] = $attribute_values;
						$this->sale_lib->setCartItemsAttributeValue($cartItemsAttributeValue);
						
						$cartItemsAttributeSet = $this->sale_lib->getCartItemsAttributeSet();
						$cartItemsAttributeSetInfo = $this->Attribute_set->get_info($cartItemsAttributeSet[$line]);
						if (strpos($cartItemsAttributeSetInfo->code,'RK') !== false ) {
							$cartItemsAttributeSetType[$line] = 'rem_keo';
						} elseif(strpos($cartItemsAttributeSetInfo->code,'RR') !== false ) {
							$cartItemsAttributeSetType[$line] = 'rem_roman';
						} else {
							$cartItemsAttributeSetType[$line] = 'non_type';
						}
                        
                        $cartItemsCategory = $this->sale_lib->getCartItemsCategory();
                        $cartItemsCategory[$line] = $data['item_info']->category_id;
                        $this->sale_lib->setCartItemsCategory($cartItemsCategory);
					}
				}
				$cartItemsAttributeValue    = $this->sale_lib->getCartItemsAttributeValue();
                $cartItemsCategory          = $this->sale_lib->getCartItemsCategory();
                $cartItemsContainline       = $this->sale_lib->getItemContainsLine();
                $cartItemsAttributeSet      = $this->sale_lib->getCartItemsAttributeSet();    
                
			}
            if($attribute_set_id != 0) {   
                return  json_encode(['listAttribute'=> $this->load->view('attribute_sets/widgets-items/attributes_items',$data,true),
									 'arrValue'=>['cartItemsAttributeSetType' => $cartItemsAttributeSetType,
												  'cartItemsAttributeValue'   => $cartItemsAttributeValue,
                                                  'cartItemsCategory'         => $cartItemsCategory,
                                                  'cartItemsContainline'      => $cartItemsContainline,
                                                  'cartItemsAttributeSet'     => $cartItemsAttributeSet
									]]);            
            }
            else
            {
                return  json_encode('');
            }

        }

        $data['item_info']->measures_converted = $this->ItemMeasures->getMeasuresByItemId($item_id);

        $data['categories'][''] = lang('common_select_category');

        $categories = $this->Category->sort_categories_and_sub_categories($this->Category->get_all_categories_and_sub_categories());
        foreach ($categories as $key => $value) {
            $name = str_repeat('&nbsp;&nbsp;', $value['depth']) . $value['name'];
            $data['categories'][$key] = $name;
        }

        $data['tags'] = implode(',', $this->Tag->get_tags_for_item($item_id));

        $data['measures'] = array();
        $data['measures']['-1'] = '--- Chọn Đơn Vị Tính ---';
        $measures = $this->Measure->get_all();
        foreach ($measures as $key => $measure) {
            $data['measures'][$key] = $measure['name'];
        }

        $data['item_tax_info'] = $this->Item_taxes->get_info($item_id);
        $data['tiers'] = $this->Tier->get_all()->result();
        $data['locations'] = array();
        $data['location_tier_prices'] = array();
        $data['additional_item_numbers'] = $this->Additional_item_numbers->get_item_numbers($item_id);

        if ($item_id != -1) {
            $data['next_item_id'] = $this->Item->get_next_id($item_id);
            $data['prev_item_id'] = $this->Item->get_prev_id($item_id);
            ;
        }

        foreach ($this->Location->get_all()->result() as $location) {
            if ($this->Employee->is_location_authenticated($location->location_id)) {
                $data['locations'][] = $location;
                $data['location_items'][$location->location_id] = $this->Item_location->get_info($item_id, $location->location_id);
                $data['location_taxes'][$location->location_id] = $this->Item_location_taxes->get_info($item_id, $location->location_id);

                foreach ($data['tiers'] as $tier) {
                    $tier_prices = $this->Item_location->get_tier_price_row($tier->id, $data['item_info']->item_id, $location->location_id);
                    if (!empty($tier_prices)) {
                        $data['location_tier_prices'][$location->location_id][$tier->id] = $tier_prices;
                    } else {
                        $data['location_tier_prices'][$location->location_id][$tier->id] = FALSE;
                    }
                }
            }

        }
        if ($item_id == -1) {
            $suppliers = array('' => lang('common_not_set'), '-1' => lang('common_none'));
        } else {
            $suppliers = array('-1' => lang('common_none'));
        }
        foreach ($this->Supplier->get_all()->result_array() as $row) {
            $suppliers[$row['person_id']] = $row['company_name'] . ' (' . $row['first_name'] . ' ' . $row['last_name'] . ')';
        }

        $data['tier_prices'] = array();
        $data['tier_type_options'] = array('unit_price' => lang('common_fixed_price'), 'percent_off' => lang('common_percent_off'));
        foreach ($data['tiers'] as $tier) {
            $tier_prices = $this->Item->get_tier_price_row($tier->id, $data['item_info']->item_id);

            if (!empty($tier_prices)) {
                $data['tier_prices'][$tier->id] = $tier_prices;
            } else {
                $data['tier_prices'][$tier->id] = FALSE;
            }
        }

        $data['suppliers'] = $suppliers;
        $data['selected_supplier'] = $this->Item->get_info($item_id)->supplier_id;

        $decimals = $this->Appconfig->get_raw_number_of_decimals();
        $decimals = $decimals !== NULL && $decimals != '' ? $decimals : 2;
        $data['decimals'] = $decimals;

        return $data;
    }

    function manage_measures()
    {
        // $this->check_action_permission('manage_measures');
        $measures = $this->Measure->get_all();
        $data = array('measures' => $measures, 'measure_list' => $this->_measureList());
        $this->load->view('items/measures', $data);
    }

    function _measureList()
    {
        $measures = $this->Measure->get_all();
        $return = '<ul>';
        foreach ($measures as $measureId => $measure) {
            $return .= '<li>' . $measure['name'] .
                '<a href="javascript:void(0);" class="edit_measure" data-name = "' . H($measure['name']) . '" data-measure_id="' . $measureId . '">[' . lang('common_edit') . ']</a> ' .
                '<a href="javascript:void(0);" class="delete_measure" data-measure_id="' . $measureId . '">[' . lang('common_delete') . ']</a> ';
            $return .= '</li>';
        }
        $return .= '</ul>';

        return $return;
    }

    function saveMeasure($measureId = FALSE)
    {
        // $this->check_action_permission('manage_tags');
        $measureName = $this->input->post('measure_name');

        if ($this->Measure->save($measureName, $measureId)) {
            echo json_encode(array('success' => true, 'message' => lang('items_tag_successful_adding') . ' ' . $measureName));
        } else {
            echo json_encode(array('success' => false, 'message' => lang('items_tag_successful_error')));
        }
    }

    function deleteMeasure()
    {
        // $this->check_action_permission('manage_tags');
        $measureId = $this->input->post('measure_id');
        if ($this->Measure->delete($measureId)) {
            echo json_encode(array('success' => true, 'message' => lang('items_successful_deleted')));
        } else {
            echo json_encode(array('success' => false, 'message' => lang('items_cannot_be_deleted')));
        }
    }

    function measureList()
    {
        echo $this->_measureList();
    }

    public function showNotAudit()
    {
        $response = array('success' => 1);
        $data = array();
        $count_id = $this->input->post('count_id', 0);
        ;
        $data['audit_items'] = $this->Inventory->get_items_counted($count_id, NULL, NULL);
        $auditedIds = array_map(function ($item) {
            return $item['item_id'];
        }, $data['audit_items']);
        $extra['category_id'] = (int)$this->mysession->getValue('AUDIT_CATEGORY');
        $data['notAuditedItems'] = $this->Item->getNotAuditedInLocation($auditedIds, $extra);
        $data['count_id'] = $count_id;
        $response['html'] = $this->load->view('items/partials/not_audited', $data, TRUE);
        echo json_encode($response);
    }

    function item_search()
    {
        //allow parallel searchs to improve performance.
        $extra['category_id'] = (int)$this->mysession->getValue('AUDIT_CATEGORY');
        $extra['by_current_location'] = true;
        session_write_close();
        $suggestions = $this->Item->get_item_search_suggestions($this->input->get('term'), 100, $extra);
        echo json_encode($suggestions);
    }

    function do_count($count_id, $offset = 0)
    {
        $this->check_action_permission('count_inventory');
        $this->session->set_userdata('current_count_id', $count_id);
        $this->mysession->setValue('AUDIT_CATEGORY', 0);

        $data = array();
        $config = array();
        $config['base_url'] = site_url("items/do_count/$count_id");
        $config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
        $config['total_rows'] = $this->Inventory->get_number_of_items_counted($count_id);
        $config['uri_segment'] = 4;
        $data['per_page'] = $config['per_page'];
        $data['count_id'] = $count_id;

        $data['total_rows'] = $config['total_rows'];
        $this->load->library('pagination');
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['count_info'] = $this->Inventory->get_count_info($count_id);

        $data['items_counted'] = $this->Inventory->get_items_counted($count_id, $config['per_page'], $offset);

        $allItems = $this->Inventory->get_items_counted($count_id, null, null);
        $totalItems = 0;
        $totalQty = 0;

        foreach ($allItems as $item) {
            $totalQty += $item['count'];
            $totalItems++;
        }

        $data['totalItems'] = $totalItems;
        $data['totalQty'] = $totalQty;

        $data['mode'] = $this->session->userdata('count_mode') ? $this->session->userdata('count_mode') : 'scan_and_set';
        $data['modes'] = array('scan_and_set' => lang('items_scan_and_set'), 'scan_and_add' => lang('items_scan_and_add'));

        $categories = $this->Category->get_all_categories_and_sub_categories();

        $data['categories'] = array_map(function ($item) {
            return $item['name'];
        }, $categories);
        $data['categories']['all'] = 'Tất cả';
        $data['selected_category'] = 'all';
        $this->load->view('items/do_count', $data);
    }

    function _reload_inventory_counts($data = array())
    {
        $this->check_action_permission('count_inventory');

        $count_id = $this->session->userdata('current_count_id');
        $config = array();

        $config['base_url'] = site_url("items/do_count/$count_id");
        $config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
        $config['total_rows'] = $this->Inventory->get_number_of_items_counted($count_id);
        $config['uri_segment'] = 4;
        $data['per_page'] = $config['per_page'];
        $data['count_info'] = $this->Inventory->get_count_info($count_id);

        $data['total_rows'] = $config['total_rows'];
        $this->load->library('pagination');
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();

        $data['items_counted'] = $this->Inventory->get_items_counted($count_id, $config['per_page']);

        $totalItems = 0;
        $totalQty = 0;

        foreach ($data['items_counted'] as $item) {
            $totalQty += $item['count'];
            $totalItems++;
        }

        $data['totalItems'] = $totalItems;
        $data['totalQty'] = $totalQty;

        $data['mode'] = $this->session->userdata('count_mode') ? $this->session->userdata('count_mode') : 'scan_and_set';
        $data['modes'] = array('scan_and_set' => lang('items_scan_and_set'), 'scan_and_add' => lang('items_scan_and_add'));

        $this->load->view("items/do_count_data", $data);
    }

    public function setCategory()
    {
        $response = array('success' => 1);
        $categoryId = $this->input->post('category_id', 0);
        $this->mysession->setValue('AUDIT_CATEGORY', $categoryId);
        echo json_encode($response);
    }

    public function clear_low_inventory()
    {
        $params = $this->session->userdata('item_search_data') ? $this->session->userdata('item_search_data') : array();
        $params['low_inventory'] = 0;
        $this->session->set_userdata("item_search_data", $params);
        redirect('items');
    }

    public function qty_location()
    {
        $response = array('success' => 1);
        $data = array();
        $itemId = $this->input->post('item_id', 0);
        $data['qty_locations'] = getItemConvertedQtyAllLocation($itemId);
        
        $response['html'] = $this->load->view('items/partials/qty_location', $data, TRUE);
        echo json_encode($response);
    }

    public function transfer_pending()
    {
        $data = array();
        $data['transferings'] = $this->Receiving->getAllTransferings();
        $this->load->view('items/transferings', $data);
    }

    public function delete_transfer()
    {
        $response = array('success' => 1);
        $recId = $this->input->post('rec_id', 0);
        $this->Receiving->removeTransferPending($recId, $this->Employee->get_logged_in_employee_info()->person_id);
        echo json_encode($response);
    }

    public function approve_transfer()
    {
        $response = array('success' => 1);

        $recId = $this->input->post('rec_id', 0);
        $this->receiving_lib->clear_all();
        $this->receiving_lib->copy_entire_receiving($recId);

        $recInfo = $this->Receiving->get_info($recId)->row_array();

        $data['cart'] = $this->receiving_lib->get_cart();
        if (empty($data['cart'])) {
            $response['success'] = 0;
        }

        $supplier_id = $recInfo['supplier_id'];
        $location_to_id = $recInfo['transfer_to_location_id'];
        $location_from_id = $recInfo['location_id'];
        $employee_id = $recInfo['employee_id'];
        $comment = $recInfo['comment'];
        $payment_type = $recInfo['payment_type'];

        $recId = $this->Receiving->approvedTransfer(
            $data['cart'],
            $supplier_id,
            $employee_id,
            $comment,
            $payment_type,
            $recId,
            $recInfo['receiving_time'],
            0,
            $location_from_id
        );

        if ($supplier_id != -1) {
            $suppl_info = $this->Supplier->get_info($supplier_id);
        }

        if ($recId > 0 && $this->receiving_lib->get_email_receipt() && !empty($suppl_info->email)) {
            $this->load->library('email');
            $config['mailtype'] = 'html';
            $this->email->initialize($config);
            $this->email->from($this->Location->get_info_for_key('email') ? $this->Location->get_info_for_key('email') : 'no-reply@mg.4biz.vn', $this->config->item('company'));
            $this->email->to($suppl_info->email);

            $this->email->subject(lang('receivings_receipt'));
            $this->email->message($this->load->view("receivings/receipt_email", $data, true));
            $this->email->send();
        }
        $this->receiving_lib->clear_all();
        echo json_encode($response);
    }

    function finish_count($update_inventory = 0)
    {
        $this->check_action_permission('count_inventory');

        $count_id = $this->session->userdata('current_count_id');

        if ($update_inventory && $this->Employee->has_module_action_permission('items', 'edit_quantity', $this->Employee->get_logged_in_employee_info()->person_id)) {
            $this->Inventory->update_inventory_from_count($count_id);

            $data['audit_items'] = $this->Inventory->get_items_counted($count_id, NULL, NULL);
            $this->Inventory->set_count($count_id, 'closed');
            $data['create_datetime'] = date(get_date_format() . ' ' . get_time_format(), strtotime(''));
            $data['count_id'] = $count_id;
            $this->load->view("items/audit", $data);
        } else {
            $this->Inventory->set_count($count_id, 'closed');
            redirect('items/count');
        }
    }

    function search()
    {
        $this->check_action_permission('search');
        $search = $this->input->post('search');
        $category_id = $this->input->post('category_id');
        $offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
        $order_col = $this->input->post('order_col') ? $this->input->post('order_col') : 'name';
        $order_dir = $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc';
        $fields = $this->input->post('fields') ? $this->input->post('fields') : 'all';

        $params = $this->session->userdata('item_search_data') ? $this->session->userdata('item_search_data') : array();
        $item_search_data = array(
            'offset' => $offset,
            'order_col' => $order_col,
            'order_dir' => $order_dir,
            'search' => $search,
            'category_id' => $category_id,
            'fields' => $fields, 'low_inventory' => $params['low_inventory']);
        $this->session->set_userdata("item_search_data", $item_search_data);
        $per_page = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;

        if ($search || $category_id) {
            $search_data = $this->Item->search(
                $search,
                $category_id,
                $per_page,
                $this->input->post('offset') ? $this->input->post('offset') : 0,
                $this->input->post('order_col') ? $this->input->post('order_col') : 'name',
                $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc',
                $fields
            );
        } else {
            $search_data = $this->Item->get_all(
                $per_page,
                $this->input->post('offset') ? $this->input->post('offset') : 0,
                $this->input->post('order_col') ? $this->input->post('order_col') : 'name',
                $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc'
            );
        }

        $config['base_url'] = site_url('items/search');
        $config['total_rows'] = $this->Item->search_count_all($search, $category_id, 10000, $fields);
        $config['per_page'] = $per_page;

        $totalQty = 0;
        $totalQtyAllLoc = 0;

        $countLowInventory = 0;
        $items = array();
        foreach ($search_data->result() as $item) {
            $reorder_level = $item->location_reorder_level ? $item->location_reorder_level : $item->reorder_level;
            if ($item->quantity !== NULL && ($item->quantity <= 0 || $item->quantity <= $reorder_level)) {
                $items[] = $item;
                $countLowInventory++;
            }

            $totalQty += (int)$item->quantity;
            $totalQtyAllLoc += (int)$this->Item->getTotalInAllLocation($item->item_id);
        }

        if ($params['low_inventory'] === 1) {
            $data['manage_table'] = get_items_manage_table_data_rows_with_array($items, $this);
        } else {
            $data['manage_table'] = get_items_manage_table_data_rows($search_data, $this);
        }

        $this->load->library('pagination');
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        echo json_encode(array(
                'manage_table' => $data['manage_table'],
                'pagination' => $data['pagination'],
                'count_items' => $search_data->num_rows(),
                'count_low_inventory' => $countLowInventory,
                'totalQty' => $totalQty,
                'totalQtyAllLoc' => $totalQtyAllLoc,
            )
        );
    }

    function services() {
        $this->check_action_permission('manage_services');
        $data = array();
        $data['currrent_page']   = $this->uri->segment(3, 1);
        $data['controller_name'] = $this->uri->segment(1);

        $this->load->view("items/services", $data);
    }

    function services_store() {
        $post  = $this->input->post();
        $arrParam = array_merge($post, $this->input->get());
        if(!empty($post)) {
            $key_filter = 'services_filter';
            $_SESSION[$key_filter]         =  array();
            $arrParam['paginator']         =  $this->_paginator;
            $arrParam['page']              =  $this->uri->segment(3, 1);

            $config['base_url'] = base_url() . 'items/servies';
            $config['total_rows'] = $this->Service->count_item($arrParam);

            $config['per_page'] = $arrParam['paginator']['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
            //$config['per_page'] = $arrParam['paginator']['per_page'] = 1;

            $config['uri_segment'] = 3;
            $config['use_page_numbers'] = TRUE;

            $items = $this->Service->list_item($arrParam);
            $html_string = $this->load->view("items/rows/service", array('items'=>$items, 'page'=>$arrParam['page']), true);

            $this->load->library("pagination");
            $this->pagination->initialize($config);
            $this->pagination->createConfig('front-end');

            $pagination = $this->pagination->create_ajax();

            $result = array('count'=> $config['total_rows'], 'html_string'=>$html_string, 'pagination'=>$pagination);
            echo json_encode($result);
        }
    }

    function service_save() {
        $this->check_action_permission('manage_services');
        $post              = $this->input->post();
        $post              = filter_service_input($post);
        $this->input->post = $post;
        $arrParams         = array_merge($post, $this->input->get());
        if(!empty($post)) {
            $flagError     = false;
            $flag_document = false;

            if($arrParams['iview_serviced'] == -1) {
                $this->form_validation->set_rules('name', 'Tên', 'required|is_unique[services.name]');
                // $this->form_validation->set_rules('code', 'Mã', 'required|is_unique[services.code]');
            }else {
                $this->form_validation->set_rules('name', 'Tên', 'required|callback_validate_unique[services-name-'.$arrParams['id'].']');
                // $this->form_validation->set_rules('code', 'Mã', 'required|callback_validate_unique[services-code-'.$arrParams['id'].']');
            }

            if($this->form_validation->run($this) == FALSE){
                $errors = $this->form_validation->error_array();
                $flagError = true;
            }

            if(isset($arrParams['document'])) {
                foreach($arrParams['document'] as $val) {
                    if($val == -1) {
                        $flag_document = true;
                    }
                }
            }
            if($flagError == true) {
                $response = array('flag'=>'false', 'errors'=>$errors);
            }elseif($flag_document == true)
                $response = array('flag'=>'error-document', 'msg'=>'Văn bản phải được chọn lựa.');
            else {
                $this->Service->save_item($arrParams, array('task'=>'update'));
                $response = array('flag'=>'true');
                $_SESSION['notice'] = 'Cập nhật thành công.';
            }

            echo json_encode($response);
        }
    }

    function delete_services() {
        $this->check_action_permission('manage_services');
        $post = $this->input->post();
        if(!empty($post)) {
            $this->Service->delete_items($post['cid']);

            $response = array('flag'=>'true', 'msg'=>'Xóa thành công');
            echo json_encode($response);
        }
    }

    function view_service($id) {
        $data = array('id'=>$id);
        $list_items_cate = $this->Item->list_items_cate();
        $categories = $this->Category->get_all_categories_and_sub_categories_as_tree();
        $arrParam['paginator'] = $this->_paginator;
        $arrParam['paginator']['per_page'] = 100;
        $arrParam['page'] = 1;
        $items_code = $this->Customer->constract_type_list($arrParam);
        $data_code = array();
        foreach ($items_code as $k => $val) {
           // if($val['no-delete']==1)
            $data_code[$k]=$val;
        }
        if($id > 0) {
            $item = $this->Service->get_item(array('id'=>$id));
            $data['item'] = $item;
            $data['slb_template'] = $this->Customer->item_select_quote_contract($item['code']);
            $data['slb_template'][-1] = '&nbsp';
        }
        $data['data_code'] = $data_code;
        $data['categories'] = $categories;
        $data['list_items_cate'] = $list_items_cate;
        $data['page'] = $this->uri->segment(4, 1);
         $this->load->view('items/form_service', $data);
    }

    function document_input(){
        $code = $this->input->post('code');
        $data['slb_template'] = $this->Customer->item_select_quote_contract($code);
        $data['slb_template'][-1] = '&nbsp';
        $data['quantity'] = $this->input->post('quantity');

        $this->load->view('items/partials/document_input', $data);
    }

    function validate_unique($field_value, $value) {
        $array = explode('-', $value);
        $table = $array[0];
        $field = $array[1];
        $id    = $array[2];

        $this->db -> select('COUNT(id) AS totalItem')
                  -> from($table)
                  -> where($field, $field_value)
                  -> where('id != ' . $id);

        $query = $this->db->get();

        $total = $query->row()->totalItem;
        if($total == 0)
            return true;
        else{
            $this->form_validation->set_message('validate_unique', "'$field_value' đã tồn tại");
            return false;
        }
    }

    /*
    *luongpham
    *trang chu kho
    *02/06/2017
    */
    function old() {
            $data = array();
            $data['currrent_page']   = $this->uri->segment(3, 1);
            $data['controller_name'] = $this->uri->segment(1);
            
            $category = array();
            $categories = $this->Category->all_categories();
            // truyền vào data danh mục cha
            $data['category'] = $categories;
            $data['selected_category'] = 'all';
            // danh mục con ban đầu là rỗng
            $data['category_child'] = array();
            $data['selected_category_child'] = 'all';
            
            $this->load->view('items/manage', $data);
        }

    /*
    *luongpham
    *ajax sp kho
    *02/06/2017
    */
        function list_store() {
            
            log_message('error', '=== START AT: '. date('Y-m-d H:i:s') .' \n');
            $post  = $this->input->post();
            
            $arrParam = array_merge($post, $this->input->get());
                
            if(!empty($post)) {
        $categoryId = $this->input->post('category_id', -1);
        $category_child = $this->input->post('category_child', -1);
        # lây id danh mục, nếu tìm kiếm theo danh mục cha
        if(isset($categoryId) && $categoryId != -1){
                    $category_child_total                    = $this->Category->get_all_danh_muc_khach_hang($categoryId);
                    $arrParam['category_child']          = $category_child;
                    $arrParam['categoryId']          = $categoryId;
                    $data['category_child_total']        = $category_child_total;
                    $data['category_child']                  = $category_child;
                    $data['selected_category_child'] = $category_child;
                    $data['category_child_ajax']         = $this->load->view('items/categories_child_ajax',$data,true);
                }
    
                $arrParam['location_id']=$this->Employee->get_logged_in_employee_current_location_id();

                $key_filter = 'count_all_items';
                $_SESSION[$key_filter]             = array();
                $arrParam['key_filter']            = $key_filter;
                $this->_paginator['per_page']      = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
                $arrParam['paginator']             = $this->_paginator;
                $arrParam['page']                  = $this->uri->segment(3, 1);
                $this->Item->_bang_tam($arrParam);
                
                $config['base_url'] = base_url() . 'items/list_store';
                $config['per_page'] = $arrParam['paginator']['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
                $config['uri_segment'] = 3;
                $config['use_page_numbers'] = TRUE;
                $config['total_rows'] = $this->Item->count_item($arrParam);
            
                $items = $this->Item->list_item($arrParam);
    
                $this->load->library("pagination");
                $this->pagination->initialize($config);
                $this->pagination->createConfig('front-end');
                
                $pagination = $this->pagination->create_ajax();
                
                $html = $this->load->view('items/rows/list', array('items'=>$items), true);
                
                $count_low_inventory = $this->Item->count_item_by_filter(array('key_filter'=>'count_low_inventory'));

                $result = array('count'=> $config['total_rows'], 'html_string'=>$html, 'pagination'=>$pagination,'count_low_inventory'=>$count_low_inventory, 'category_child'=>  isset($data['category_child_ajax']) ? $data['category_child_ajax']:'');


                $this->load->library('profiler');
                $test = $this->profiler->_compile_queries();
                log_message('error', html_entity_decode(strip_tags($test)));


                log_message('error', '=== END AT: '. date('Y-m-d H:i:s') .' \n');
                echo json_encode($result);
            }
        }
        
    /*
    *luongpham
    *sp dưới hạn mức kho
    *02/06/2017
    */
        function low_inventory() {
            $data = array();
            $data['currrent_page']   = $this->uri->segment(3, 1);
            $data['controller_name'] = $this->uri->segment(1);
            
            $category = array();
            $categories = $this->Category->all_categories();
            // truyền vào data danh mục cha
            $data['category'] = $categories;
            $data['selected_category'] = 'all';
            // danh mục con ban đầu là rỗng
            $data['category_child'] = array();
            $data['selected_category_child'] = 'all';
            
            $this->load->view('items/low_inventory', $data);
        }

    /*
    *luongpham
    *ajax sp dưới hạn mức kho
    *02/06/2017
    */
        function low_inventory_list() {
            
            $post  = $this->input->post();
            
            $arrParam = array_merge($post, $this->input->get());
                
            if(!empty($post)) {
        $categoryId = $this->input->post('category_id', -1);
        $category_child = $this->input->post('category_child', -1);
        # lây id danh mục, nếu tìm kiếm theo danh mục cha
        if(isset($categoryId) && $categoryId != -1){
                    $category_child_total                    = $this->Category->get_all_danh_muc_khach_hang($categoryId);
                    $arrParam['category_child']          = $category_child;
                    $arrParam['categoryId']          = $categoryId;
                    $data['category_child_total']        = $category_child_total;
                    $data['category_child']                  = $category_child;
                    $data['selected_category_child'] = $category_child;
                    $data['category_child_ajax']         = $this->load->view('items/categories_child_ajax',$data,true);
                }
                $arrParam['location_id']=$this->Employee->get_logged_in_employee_current_location_id();
                $key_filter = 'count_low_inventory';
                $_SESSION[$key_filter]             = array();
                $arrParam['key_filter']            = $key_filter;
                $this->_paginator['per_page']      = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
                $arrParam['paginator']             = $this->_paginator;
                $arrParam['page']                  = $this->uri->segment(3, 1);
                $this->Item->_bang_tam($arrParam);
                
                $config['base_url'] = base_url() . 'items/low_inventory_list';
                $config['per_page'] = $arrParam['paginator']['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
                $config['uri_segment'] = 3;
                $config['use_page_numbers'] = TRUE;
                $config['total_rows'] = $this->Item->count_item($arrParam);
                
                $items = $this->Item->list_item($arrParam);
    
                $this->load->library("pagination");
                $this->pagination->initialize($config);
                $this->pagination->createConfig('front-end');
                
                $pagination = $this->pagination->create_ajax();
                
                $html = $this->load->view('items/rows/low_inventory', array('items'=>$items), true);
                
                $count_all_items = $this->Item->count_item_by_filter(array('key_filter'=>'cousnt_all_items'));
    
                $result = array('count'=> $config['total_rows'], 'html_string'=>$html, 'pagination'=>$pagination, 'count_all_items'=>$count_all_items, 'category_child'=>  isset($data['category_child_ajax']) ? $data['category_child_ajax']:'');
                echo json_encode($result);
            }
        }
}

?>
