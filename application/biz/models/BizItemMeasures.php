<?php
class BizItemMeasures extends CI_Model
{
	/*
	 Inserts or updates a item
	 */
	function save(&$itemMeasureData,$id=false)
	{
		if($this->db->insert('item_measures',$itemMeasureData))
		{
			$itemMeasureData['id']=$this->db->insert_id();
			return true;
		}
		return false;
	}
	
	function deleteByItemId($itemId=false)
	{
		if($itemId) {
			$this->db->delete('item_measures', array('item_id' => $itemId));
		}
	}
	
	public function getMeasuresByItemId($itemId=false) {
		if($itemId) {
			$this->db->from('item_measures');
			$this->db->join('measures', 'measures.id = item_measures.measure_converted_id', 'left');
			$this->db->where('item_id', $itemId);
			return $this->db->get()->result_array();
		}
		return array();
	}
	
	public function getMeasuresByIdAndItemId($itemId=0, $measureId = 0) {
		if($itemId && $measureId) {
			$this->db->from('item_measures');
			$this->db->join('measures', 'measures.id = item_measures.measure_converted_id', 'left');
			$this->db->where('item_id', $itemId);
			$this->db->where('measure_converted_id', $measureId);
			$result = $this->db->get();
			if($result->num_rows() > 0)
			{
				$row = $result->result();
				return $row[0];
			}
			
		}
		return null;
	}
	
	public function getConvertedValue($itemId = 0, $measureConvertedId = 0) {
		$itemInfo = $this->Item->get_info($itemId);
		if ($itemInfo) {
			$this->db->from('item_measures');
			$this->db->where('item_id', $itemId);
			$this->db->where('measure_id', $itemInfo->measure_id);
			$this->db->where('measure_converted_id', $measureConvertedId);
			$result = $this->db->get();
			if($result->num_rows() > 0)
			{
				$row = $result->result();
				return $row[0];
			}
		}
		return FALSE;
	}


	public function lay_ra_don_vi_quy_doi($itemId = 0, $measure = 0,$don_vi_tinh) {
			$this->db->select('qty_converted,measure_converted_id,measure_id');
			$this->db->from('item_measures');
			$this->db->where('item_id', $itemId);
			$this->db->where('measure_converted_id = '.$measure.' AND measure_id = '.$don_vi_tinh.'');
			$this->db->or_where('measure_converted_id = '.$don_vi_tinh.' AND measure_id = '.$measure.'');
			$result = $this->db->get()->result_array();
		
		return !empty($result)?$result:-1;
	}
	
	public function quy_doi_ra_don_vi_cua_san_pham($itemId = 0, $so_luong_can_quy_doi = 0,$don_vi_tinh,$don_vi_tinh_goc) {
			# $don_vi_quy_doi_goc 
			# đơn vị quy đổi được lấy mặc định theo đơn vị tính của mặt hàng
			$don_vi_quy_doi = $this->lay_ra_don_vi_quy_doi($itemId,$don_vi_tinh,$don_vi_tinh_goc);

	        if($don_vi_tinh == $don_vi_quy_doi[0]['measure_id'] ) {
	        	$so_luong_can_quy_doi = $so_luong_can_quy_doi;
	        }
	        elseif ($don_vi_tinh == $don_vi_quy_doi[0]['measure_converted_id']) {
	        	$so_luong_can_quy_doi = $so_luong_can_quy_doi*$don_vi_quy_doi[0]['qty_converted'];
	        }
		
		return round($so_luong_can_quy_doi,3);
	}


	
}