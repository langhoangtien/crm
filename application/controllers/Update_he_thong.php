<?php
require_once ("Secure_area.php");
class Update_he_thong extends Secure_area
{
    function __construct()
    {
        parent::__construct();

    }

    public function cap_nhat(){


        # cập nhật dữ liệu cho
        $success = $this->cap_nhat_du_lieu_cho_bao_cao_xuat_nhap_ton();
        if($success) {
            # Cập nhật dữ liệu thành công
            $config_data=array(
                'key'=>'cap_nhat_du_lieu',
                'value'=>1
            );
            $this->db->replace('app_config', $config_data);

            echo json_encode([
                'flag' => true,
                'msg'=>'Cập nhật thành công, chúc mừng bạn đang sử dụng phiên bản mới nhất',
            ]);
        } else {
            echo json_encode([
                'flag' => false,
                'msg'=>'Cập nhật thất bại, vui lòng liên hệ với Lifetek để được hỗ trợ',
            ]);
        }

    }

    private function cap_nhat_du_lieu_cho_bao_cao_xuat_nhap_ton(){
        $this->db->select('item_id');
        $this->db->where('deleted',0);
        $item_array =$this->db->get('items')->result_array();

        $this->db->select('location_id');
        $location_array =$this->db->get('locations')->result_array();


        $this->db->trans_begin();

        foreach ($item_array as $item_id){
            foreach ($location_array as $location_id){
                $inv_data = array();
                $location_items_data = array();
                $dem = $this->kiem_tra_ton_tai_du_lieu($key1 = 'item_id',$value1=$item_id['item_id'],$key2 = 'location_id',$value2= $location_id['location_id'],$table='location_items');
                if($dem == 0){
                    $location_items_data = array(
                        'item_id'=> $item_id['item_id'],
                        'location_id' => $location_id['location_id'],
                        'cost_price' => NULL,
                        'unit_price' => NULL,
                        'promo_price' => NULL,
                        'start_date' => date('Y-m-d H-m-s'),
                        'end_date' => date('Y-m-d H-m-s'),
                        'quantity' => 0,
                        'reorder_level' => NULL,
                        'override_default_tax' => 0,
                    );

                    $this->db->insert('location_items',$location_items_data);
                }

                $dem2 = $this->kiem_tra_ton_tai_du_lieu($key1 = 'trans_items',$value1=$item_id['item_id'],$key2 = 'location_id',$value2= $location_id['location_id'],$table='inventory');
                if($dem2 == 0){
                    $inv_data = array
                    (
                        'location_id' => $location_id['location_id'],
                        'trans_date'=>date('Y-m-d H:i:s'),
                        'trans_items'=>$item_id['item_id'],
                        'trans_user'=>1,
                        'trans_comment'=>'Tồn kho sau khi thêm mới dữ liệu',
                        'trans_inventory'=>0,
                        'bat_dau'=>1,
                    );
                    $this->db->insert('inventory',$inv_data);
                }

            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {

            return false;
        } else return true;

    }


    private function kiem_tra_ton_tai_du_lieu($key1 = '',$value1='',$key2 = '',$value2='',$table=''){
        $this->db->from($table);
        $this->db->where($key1,$value1);
        $this->db->where($key2,$value2);
        $result = $this->db->get()->num_rows();
        return $result;


    }
}