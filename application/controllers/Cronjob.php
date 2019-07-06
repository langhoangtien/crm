<?php
if (!empty($argv[4])) {
    define('CLI_CUSTOMER', $argv[4]);
}

class Cronjob extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('Customer');
        $this->load->helper('convert_content');
    }

    public function do_send_mail($campain_id = '',$customer = '')
    {
        if ($campain_id != '') {
            
            $mail_campain = $this->Customer->get_all_mail_campain(array(
                'id' => $campain_id
            ))[0];
            $mail_template = $mail_campain['mail_id'];
            if ($mail_campain['smsmail_group_id'] == -1) {
                $customer_infos = $this->Customer->list_item(array(
                    'of_month' => true
                ));
                $note = 'SINH_NHAT';
            } else {
                $customer_infos = $this->Customer->get_customer_in_smsEmail_group(array(), 10000, 0, $mail_campain['smsmail_group_id']);
            }
            $mail_info = $this->Customer->get_mail_template($mail_template);
            
            if (empty($mail_info) || empty($customer_infos))
                return;
            
            $address_list = $body_list = $data_history = array();
            foreach ($customer_infos as $customer) {
                if (!empty($customer['email'])) {
                    $content = $mail_info['mail_content'];
                    $body_list[] = convert_content($content, $customer);
                    
                    $address_list[] = array(
                        'AddAddress' => $customer['email'],
                        'AddAddress_name' => $customer['first_name'] . ' ' . $customer['last_name']
                    );
                    $data_history[] = array(
                        'person_id' => $customer['person_id'],
                        'employee_id' => $mail_campain['employee_id'],
                        'title' => $mail_info['mail_title'],
                        'email' => $customer['email'],
                        'content' => $content,
                        'note' => empty($note) ? '' : $note,
                        'send_from_campain' => $campain_id,
                        'send_to_group' => $mail_campain['smsmail_group_id'],
                        'time' => date('Y-m-d H:i:s'),
                        'status' => 1,
                        'file' => ''
                    );
                } else {
                    $empty_mail[] = $customer['first_name'] . ' ' . $customer['last_name'];
                }
            }
            if (!empty($empty_mail)) {
                $mail_name_list = implode(', ', $empty_mail);
                $msg = $mail_name_list . ' không có địa chỉ mail.';
                
                $response = array(
                    'flag' => 'false',
                    'msg' => $msg
                );
            } else {
                $mail['from_name'] = $this->config->item('company');
                $mail['address_list'] = serialize($address_list);
                $mail['subject'] = $mail_info['mail_title'];
                $mail['body'] = serialize($body_list);
                $mail['type'] = 'sequence';
                biz_send_mail($mail);
                $response = true;
                $this->Customer->save_mail_history($data_history, array(
                    'task' => 'update-multi'
                ));
            }
        }
    }
}
?>