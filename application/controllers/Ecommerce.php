<?php

class Ecommerce extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        if (!is_cli()) // Running from web should have store config permissions
{
            $this->load->model('Employee');
            $this->load->model('Location');
            if (!$this->Employee->is_logged_in()) {
                redirect('login?continue=' . rawurlencode(uri_string() . '?' . $_SERVER['QUERY_STRING']));
            }
            
            if (!$this->Employee->has_module_permission('config', $this->Employee->get_logged_in_employee_info()->person_id)) {
                redirect('no_access/config');
            }
        }
        $this->load->model('Appconfig');
    }

    public function cancel()
    {
        $this->load->model('Appconfig');
        $this->Appconfig->save('kill_ecommerce_cron', 1);
        $this->Appconfig->save('ecommerce_cron_running', 0);
        $this->Appconfig->save('ecommerce_sync_progress', 100);
    }

    function manual_sync()
    {
        try {
            if (!empty($this->config->item('ecommerce_platform'))) {
                $this->cron();
            }
        } catch (Exception $e) {
            echo "*******EXCEPTION: " . var_export($e->getMessage(), TRUE);
        }
    }

    /*
     * This function is used to sync the PHPPOS items with online ecommerce store.
     */
    // $db_override is NOT used at all; but in database.php to select database based on CLI args for cron in cloud
    public function cron($db_override = '')
    {
        
        // error_reporting(E_ALL);
        // error_reporting(E_ALL ^ (E_NOTICE));
        
        // ini_set("display_errors", "On");
        // error_reporting(E_ALL);
        // if (is_on_demo_host())
        // {
        // echo json_encode(array('success' => FALSE, 'message' => lang('common_disabled_on_demo')));
        // die();
        // }
        ignore_user_abort(TRUE);
        set_time_limit(0);
        session_write_close();
        
        try {
            $this->Appconfig->save('kill_ecommerce_cron', 0);
            
            $platform_model = "";
            $this->load->model("Appconfig");
            if (0 && $this->Appconfig->get_raw_ecommerce_cron_running()) {
                echo json_encode(array(
                    'success' => FALSE,
                    'message' => lang('common_ecommerce_running')
                ));
                die();
            }
            
            $this->load->model('Location');
            if ($timezone = ($this->Location->get_info_for_key('timezone', $this->config->item('ecom_store_location') ? $this->config->item('ecom_store_location') : 1))) {
                date_default_timezone_set($timezone);
            }
            
            $this->Appconfig->save('ecommerce_cron_running', 1);
            $this->Appconfig->save('ecommerce_sync_progress', 0);
            $platform = $this->Appconfig->get("ecommerce_platform");
            if ($platform == "woocommerce") {
                $platform_model = "woo";
            }
            
            // TODO
            $platform_model = "woo";
            
            if ($platform_model != "") {
                $ecommerce_cron_sync_operations = json_decode($this->config->item('ecommerce_cron_sync_operations'));
                $this->load->model($platform_model);
                $this->lang->load('config');
                $valid = array(
                    "sync_phppos_item_changes",
                    "import_ecommerce_items_into_phppos",
                    "sync_inventory_changes",
                    "export_phppos_tags_to_ecommerce",
                    "export_phppos_categories_to_ecommerce",
                    "export_phppos_items_to_ecommerce"
                );
                
                $numsteps = count($ecommerce_cron_sync_operations);
                $stepsCompleted = 0;
                
                foreach ($ecommerce_cron_sync_operations as $operation) {
                    if (is_cli()) {
                        echo "START $operation\n";
                    }
                    
                    if (in_array($operation, $valid)) {
                        $percent = floor(($stepsCompleted / $numsteps) * 100);
                        $message = lang("config_" . $operation);
                        $this->$platform_model->update_sync_progress($percent, $message);
                        
                        $this->$platform_model->$operation();
                        $stepsCompleted++;
                    }
                    
                    if (is_cli()) {
                        echo "DONE $operation\n";
                    }
                }
                
                $percent = floor(($stepsCompleted / $numsteps) * 100);
                $message = lang("config_" . $operation);
                $this->$platform_model->update_sync_progress($percent, $message);
                
                $this->load->model('Appconfig');
                $sync_date = date('Y-m-d H:i:s');
                $this->Appconfig->save('last_ecommerce_sync_date', $sync_date);
                if (is_cli()) {
                    echo "\n\n***************************DONE***********************\n";
                }
                
                echo json_encode(array(
                    'success' => TRUE,
                    'date' => $sync_date
                ));
            }
            
            $this->Appconfig->save('ecommerce_sync_progress', 100);
            $this->Appconfig->save('ecommerce_cron_running', 0);
        } catch (Exception $e) {
            if (1 || is_cli()) {
                echo "*******EXCEPTION: " . var_export($e->getMessage(), TRUE);
            }
            $this->Appconfig->save('ecommerce_cron_running', 0);
        }
    }
}
?>