<?php
// We change the headers of the page so that the browser will know what sort of file is dealing with. Also, we will tell the browser it has to treat the file as an attachment which cannot be cached.
$filename = date('d-m-Y').'-THONG-KE-HOP-DONG_THANG-'.$month.'-'.$year;
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=".$filename.".xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<style type="text/css">
th, td{
	border:1px solid #ccc;
}
tr{
	height: 40px;
}
.tr_thead th{
	background: #bfbfbf;
}
</style>
<table class="table tablesorter table-reports table-bordered display table-hover" id="table_report_contract_cus">
	<thead>
	<tr>
		<th align="center" colspan="13" id="title-export1">THỐNG KÊ HỢP ĐỒNG TƯ VẤN
		</th>
	</tr>
	<tr>
		<th align="center"  colspan="13"><?php echo $name_location; ?></th>
	</tr>
	<tr>
		<th align="center"  colspan="13">Tháng:<span id="date_time_m"><?php echo $month; ?></span>/<span id="date_time_y"><?php echo $year; ?></span></th>
	</tr>
	<tr class="tr_thead">
		<th align="center">STT</th>
		<th align="center" width="8%">Ngày ký HĐ</th>
		<th align="center" width="10%">Số Hợp đồng</th>
		<th align="center" width="10%">Nội dung</th>
		<th align="center" width="8%">Đối tác</th>
		<th align="center"  width="8%">Giá trị hợp đồng chưa VAT (VNĐ)</th>
		<th align="center" width="8%">VAT (VNĐ)</th>
		<th align="center" width="10%">Hiện trạng HĐ</th>
		<th align="center">Ngày ký thanh lý/nghiệm thu</th>
		<th align="center">Số tiền đã thu trong tháng (VNĐ)</th>
		<th align="center">Số tiền thanh toán dồn tích (VNĐ)</th>
		<th align="center">CP dồn tích trả cho bên thứ 3 để thực hiện HĐ (VNĐ)</th>
		<th align="center" width="6%">Ghi chú</th> 
	</tr>
</thead>
<tbody id="tbody-report">


	<?php 
	if (!empty($contract)) {
		?>
		<tr>
			<td colspan="13" style="font-weight: 600; background: #e2e2e2;">
				I. Hợp đồng nghiệm thu và thanh lý trong tháng
			</td>
		</tr>
		<!-- ============= HỢP ĐỒNG NGHIỆM THU TRONG THÁNG =========  -->
		<tr>
			<td colspan="13" style="font-weight: 600;">
				A. Nghiệm thu trong tháng
			</td>
		</tr>
		<?php 
		$stt =1;
		foreach ($contract as $key => $value) {
			if ($value['trang_thai_hop_dong']=='done') {
				?>
				<tr>
					<td><?php echo $stt; ?></td>
					<td><?php echo date('d-m-Y',strtotime($value['date_signing'])); ?></td>
					<td><?php echo $value['code']; ?></td>
					<td><?php echo $value['name_contract']; ?></td>
					<td><?php if($value['ten_doi_tac']!=null){echo $value['ten_doi_tac'];}else{echo '';} ?></td>
					<td><?php echo number_format((int)$value['total_value']); ?></td>
					<td><?php echo number_format((int)$value['co_vat']); ?></td>
					<td>Đã nghiệm thu</td>
					<td><?php echo date('d-m-Y',strtotime($value['ngay_ky_thanh_ly'])); ?></td>
					<td><?php 
					$total =0;
					$total_vat=0;
					$total_not_vat=0;

					foreach ($tong_tien_da_thu_trong_thang as $k => $val) {
						if ($val['id']==$value['id'] && $val['c_status']=='done') {
							if ($val['vat']=='published') {
								$total_vat += $val['price']/1.1;
							}else{
								$total_not_vat +=$val['price'];
							}
							$total = $total_vat+$total_not_vat;
						}
					}
					echo number_format($total);
					?></td>
					<!-- thanh toans don tich -->
					<td>
						<?php 
						$tmp =0;
						$tong_dt_nt =0;
						foreach ($tong_tien_thanh_toan_don_tich as $key_dt => $value_dt) {
							if ($value_dt['id'] ==$value['id'] && $value_dt['c_status']=='done') {
								if ($value_dt['vat']=='published') {
									$tmp +=$value_dt['price']/1.1;
								}else{
									$tmp+=$value_dt['price'];
								}
								$tong_dt_nt +=$tmp;
							}
						}
						echo number_format($tmp);
						?>
					</td>
					<td>
						<?php 
						$nt_ben_thu3 = 0;
						foreach ($ds_ben_thu_3 as $a => $b) {
							if ($b['contract_id']==$value['id'] && $b['tt']=='done') {
								$nt_ben_thu3+=$b['item_unit_price'];
							}
						}
						echo number_format($nt_ben_thu3);
						?>
					</td>
					<td></td>
				</tr>
				<?php
				$stt++;
			}
		}
		?>

		<tr>
			<td colspan="5" style="font-weight: 600;">
				Tổng nghiệm thu trong tháng
			</td>
			<td>
				<?php 
				$tong_nghiem_thu=0;
				$tong_nghiem_thu_vat=0;
				foreach ($contract as $key => $value) {
					if ($value['trang_thai_hop_dong']=='done') {
						$tong_nghiem_thu +=(int)$value['total_value'];
						$tong_nghiem_thu_vat +=$value['co_vat'];
					}
				}
				echo number_format($tong_nghiem_thu);
				?>
			</td>
			<td>
				<?php 
				echo number_format($tong_nghiem_thu_vat);
				?>
			</td>
			<td></td>
			<td>
			</td>
			<td style="font-weight: 600;">
				<?php
				$tong_da_thu_nt=0;
				$tnt1=0;
				foreach ($tong_tien_da_thu_trong_thang as $k => $val) {
					if ($val['status']=='done') {
						if ($val['vat']=='published') {
							$tnt1 = (int)$val['price']/1.1;
						}else{
							$tnt1 =(int)$val['price'];
						}
						$tong_da_thu_nt += $tnt1;
					}
				}
				echo number_format($tong_da_thu_nt);
				?>
			</td>
			<td style="font-weight: 600;"><?php echo number_format($tong_dt_nt); ?></td>
			<td style="font-weight: 600;">
				<?php 
				$tong_tien_ben_3_nghiem_thu=0;
				foreach ($ds_ben_thu_3 as $k => $val) {
					if ($val['tt']=='done') {
						$tong_tien_ben_3_nghiem_thu+=$val['item_unit_price'];
					}
				}
				echo number_format($tong_tien_ben_3_nghiem_thu);
				?>
			</td>
			<td></td>
		</tr>
		<!-- ============= HỢP ĐỒNG THANH LÝ TRONG THÁNG =========  -->
		<tr>
			<td colspan="13" style="font-weight: 600;">
				A. Thanh lý trong tháng
			</td>
		</tr>
		<?php 
		$stt =1;
		foreach ($contract as $key => $value) {
			if ($value['trang_thai_hop_dong']=='liquidated') {
				?>
				<tr>
					<td><?php echo $stt; ?></td>
					<td><?php echo date('d-m-Y',strtotime($value['date_signing'])); ?></td>
					<td><?php echo $value['code']; ?></td>
					<td><?php echo $value['name_contract']; ?></td>
					<td><?php if($value['ten_doi_tac']!=null){echo $value['ten_doi_tac'];}else{echo '';} ?></td>
					<td><?php echo number_format($value['total_value']); ?></td>
					<td><?php echo number_format($value['co_vat']); ?></td>
					<td>Đã thanh lý</td>
					<td><?php echo date('d-m-Y',strtotime($value['ngay_ky_thanh_ly'])); ?></td>
					<td>
						<?php 
						$tong_nghiem_thu_trong_thang =0;
						foreach ($tong_tien_da_thu_trong_thang as $k => $val) {
							if ($val['id']==$value['id'] && $val['c_status']=='liquidated') {
								if ($val['vat']=='published') {
									$tong_nghiem_thu_trong_thang += $val['price']/1.1;
								}else{
									$tong_nghiem_thu_trong_thang +=$val['price'];
								}
							}
						}
						echo number_format($tong_nghiem_thu_trong_thang);
						?>
					</td>
					<td>
						<?php 
						$tmp =0;
						$tong_dt_tl=0;
						foreach ($tong_tien_thanh_toan_don_tich as $key_dt => $value_dt) {
							if ($value_dt['id'] ==$value['id'] && $value_dt['c_status']=='liquidated') {
								if ($value_dt['vat']=='published') {
									$tmp +=$value_dt['price']/1.1;
								}else{
									$tmp+=$value_dt['price'];
								}
								$tong_dt_tl +=$tmp;
							}
						}
						echo number_format($tmp);
						?>
					</td>
					<td>
						<?php 
						$tl_ben_thu_3 = 0;
						foreach ($ds_ben_thu_3 as $a => $b) {
							if ($b['contract_id']==$value['id'] && $b['tt']=='liquidated') {
								$tl_ben_thu_3+=$b['item_unit_price'];
							}
						}
						echo number_format($tl_ben_thu_3);
						?>
					</td>
					<td></td>
				</tr>
				<?php
				$stt++;
			}
		}
		?>
		<tr>
			<td colspan="5" style="font-weight: 600;">
				Tổng thanh lý trong tháng
			</td>
			<td style="font-weight: 600;">
				<?php 
				$tong_thanh_ly=0;
				$tong_thanh_ly_vat=0;
				foreach ($contract as $key => $value) {
					if ($value['trang_thai_hop_dong']=='liquidated') {
						$tong_thanh_ly +=$value['total_value'];
						$tong_thanh_ly_vat +=$value['co_vat'];
					}
				}
				echo number_format($tong_thanh_ly);
				?>
			</td>
			<td style="font-weight: 600;">
				<?php 
				echo number_format($tong_thanh_ly_vat);
				?>
			</td>
			<td>-</td>
			<td></td>
			<td style="font-weight: 600;">
				<?php 
				$total_vat=0;
				$total_not_vat =0;
				$tong_thanh_ly_trong_thang =0;

				foreach ($tong_tien_da_thu_trong_thang as $k => $val) {
					if ($val['status']=='liquidated' && $val['c_status']=='liquidated') {
						if ($val['vat']=='published') {
							$total_vat = $val['price']/1.1;
						}else{
							$total_vat =$val['price'];
						}
						$tong_thanh_ly_trong_thang+=$total_vat;
					}
				}
				echo number_format($tong_thanh_ly_trong_thang);
				?>
			</td>
			<td><?php echo number_format($tong_dt_tl); ?></td>
			<td style="font-weight: 600;">
				<?php 
				$tong_tien_ben_3_thanh_ly=0;
				foreach ($ds_ben_thu_3 as $k => $val) {
					if ($val['tt']=='liquidated') {
						$tong_tien_ben_3_thanh_ly+=$val['item_unit_price'];
					}
				}
				echo number_format($tong_tien_ben_3_thanh_ly);
				?>
			</td>
			<td></td>
		</tr>
		<tr>
			<td colspan="5" style="font-weight: 600;">
				Tổng nghiệm thu và thanh lý trong tháng
			</td>
			<td style="font-weight: 600;">
				<?php 
				echo  number_format($tong_thanh_ly+$tong_nghiem_thu);
				?>
			</td>
			<td style="font-weight: 600;">
				<?php 
				echo  number_format($tong_thanh_ly_vat+$tong_nghiem_thu_vat);
				?>
			</td>
			<td></td>
			<td></td>
			<td style="font-weight: 600;">
				<?php echo number_format($tong_thanh_ly_trong_thang+$tong_da_thu_nt); ?>
			</td>
			<td style="font-weight: 600;">
				<?php echo number_format($tong_dt_tl+$tong_dt_nt); ?>
			</td>
			<td style="font-weight: 600;">
				<?php echo ($tong_tien_ben_3_nghiem_thu+$tong_tien_ben_3_thanh_ly); ?>
			</td>
			<td></td>
		</tr>
		<!-- ============= HỢP ĐỒNG ĐANG THỰC HIỆN TRONG THÁNG =========  -->
		<tr>
			<td colspan="13" style="font-weight: 600; background: #e2e2e2;">II.Hợp đồng đang thực hiện dở dang tại thời điểm kết thúc tháng</td>
		</tr>
		<?php 
		$stt =1;
		foreach ($contract as $key => $value_progress) {
			if ($value_progress['trang_thai_hop_dong']=='progress') {
				?>
				<tr>
					<td><?php echo $stt; ?></td>
					<td><?php echo date('d-m-Y',strtotime($value_progress['date_signing'])); ?></td>
					<td><?php echo $value_progress['code']; ?></td>
					<td><?php echo $value_progress['name_contract']; ?></td>
					<td><?php if($value_progress['ten_doi_tac']!=null){echo $value_progress['ten_doi_tac'];}else{echo '-';} ?></td>
					<td><?php echo number_format((int)$value_progress['total_value']); ?></td>
					<td><?php echo number_format((int)$value_progress['co_vat']); ?></td>
					<td>Đang thực hiện</td>
					<td><?php echo date('d-m-Y',strtotime($value_progress['ngay_ky_thanh_ly'])); ?></td>
					<td>
						<?php 
						$total =0;
						$total_vat=0;
						$total_not_vat=0;
						foreach ($tong_tien_da_thu_trong_thang as $k => $val) {
						// if ($val['id']==$value_progress['id'] && $val['c_status']=='done') {

							if ($val['id']==$value_progress['id']) {
								if ($val['vat']=='published') {
									$total_vat += (int)$val['price']/1.1;
								}else{
									$total_not_vat +=(int)$val['price'];
								}
								$total = $total_vat+$total_not_vat;
							}
						}
						echo number_format($total);
						?>
					</td>
					<td>
						<?php 
						$tmp_p =0;
						$tong_dt_dangth=0;
						foreach ($tong_tien_thanh_toan_don_tich as $key_dt => $value_dt_p) {
							if ($value_dt_p['id'] ==$value_progress['id']) {
								if ($value_dt_p['vat']=='published') {
									$tmp_p +=$value_dt_p['price']/1.1;
								}else{
									$tmp_p +=$value_dt_p['price'];
								}
								$tong_dt_dangth +=$tmp_p;
							}
						}
						echo number_format($tmp_p);
						?>
					</td>
					<td>
						<?php 
						$sotien_chiben_thu3 = 0;
						foreach ($ds_ben_thu_3 as $a => $b) {
							if ($b['contract_id']==$value_progress['id'] && $b['tt']=='progress') {
								$sotien_chiben_thu3+=$b['item_unit_price'];
							}
						}
						echo number_format($sotien_chiben_thu3);
						?>
					</td>
					<td>-</td>
				</tr>
				<?php
				$stt++;
			}
		}
		?>
		<tr>
			<td colspan="5" style="font-weight: 600;">
				Tổng HĐ đang thực hiện dở dang tại thời điểm kết thúc tháng
			</td>
			<td style="font-weight: 600;">
				<?php 
				$tong_dang_th=0;
				foreach ($contract as $key => $value) {
					if ($value['trang_thai_hop_dong']=='progress') {
						$tong_dang_th +=(int)$value['total_value'];
					}
				}
				echo number_format($tong_dang_th);
				?>
			</td>
			<td style="font-weight: 600;">
				<?php 
				$tong_dang_th_vat=0;
				foreach ($contract as $key => $value) {
					if ($value['trang_thai_hop_dong']=='progress') {
						$tong_dang_th_vat +=$value['co_vat'];
					}
				}
				echo number_format($tong_dang_th_vat);
				?>
			</td>
			<td></td>
			<td></td>
			<td style="font-weight: 600;">
				<?php
				foreach ($tong_tien_da_thu_trong_thang as $k => $val) {
					if ($val['status']=='progress') {
						if ($val['vat']=='published') {
							$t1 += (int)$val['price']/1.1;
						}else{
							$t2 +=(int)$val['price'];
						}
						$tong_da_chi_3 = $t1+$t2;
					}
				}
				echo number_format($tong_da_chi_3);
				?>
			</td>
			<td style="font-weight: 600;">
				<?php echo number_format($tong_dt_dangth); ?>
			</td>
			<td style="font-weight: 600;">
				<?php 
				$tong_tien_ben_3_dang_thuc_hien=0;
				foreach ($ds_ben_thu_3 as $k => $val) {
					if ($val['tt']=='progress') {
						$tong_tien_ben_3_dang_thuc_hien+=$val['item_unit_price'];
					}
				}
				echo number_format($tong_tien_ben_3_dang_thuc_hien);
				?>
			</td>
			<td></td>
		</tr>
		<!-- ============= HỢP ĐỒNG ĐĂNG KÝ MỚI TRONG THÁNG=========  -->
		<tr>
			<td colspan="13" style="font-weight: 600;">III.Đăng ký mới trong tháng</td>
		</tr>
		<?php 
		$stt=1;
		$total_new = 0;
		$total_new_vat =0;
		foreach ($contract as $key => $valuenew) {
			$total_new += $valuenew['total_value'];
			$total_new_vat += $valuenew['co_vat'];
			?>
			<tr>
				<td><?php echo $stt; ?></td>
				<td><?php echo date('d-m-Y',strtotime($valuenew['date_signing'])); ?></td>
				<td><?php echo $valuenew['code']; ?></td>
				<td><?php echo $valuenew['name_contract']; ?></td>
				<td><?php if($valuenew['ten_doi_tac']!=null){echo $valuenew['ten_doi_tac'];}else{echo '-';} ?></td>
				<td><?php echo number_format($valuenew['total_value']); ?></td>
				<td><?php echo number_format($valuenew['co_vat']); ?></td>
				<td>
					<?php 
					if ($valuenew['trang_thai_hop_dong']=='progress') {
						echo "Đang thực hiện";
					}
					if ($valuenew['trang_thai_hop_dong']=='done') {
						echo "Đang nghiệm thu";
					}
					if ($valuenew['trang_thai_hop_dong']=='pause') {
						echo "Tạm dừng chưa thanh lý";
					}
					if ($valuenew['trang_thai_hop_dong']=='liquidated') {
						echo "Đã thanh lý";
					}
					?>
				</td>
				<td><?php echo date('d-m-Y',strtotime($valuenew['ngay_ky_thanh_ly'])); ?></td>
				<td>
					<?php 
					$totalnew =0;
					$totalnew_vat=0;
					foreach ($tong_tien_da_thu_trong_thang as $k => $val_new) {
						// if ($val['id']==$valuenew['id'] && $val['c_status']=='done') {
						if ($val_new['id']==$valuenew['id']) {
							if ($val_new['vat']=='published') {
								$totalnew_vat = $val_new['price']/1.1;
							}else{
								$totalnew_vat =$val_new['price'];
							}
							$totalnew += $totalnew_vat;
						}	
					}
					echo number_format($totalnew);
					?>
				</td>
				<!-- thanh toan don tich -->
				<td>
					<?php 
					$tmp_new =0;
					$tong_dt_new=0;
					foreach ($tong_tien_thanh_toan_don_tich as $key_dt => $value_new) {
						if ($value_new['id'] ==$valuenew['id']) {
							if ($value_new['vat']=='published') {
								$tmp_new +=$value_new['price']/1.1;
							}else{
								$tmp_new+=$value_new['price'];
							}
							$tong_dt_new +=$tmp;
						}
					}
					echo number_format($tmp_new);
					?>
				</td>
				<!-- tong da chi cho ben thu 3 -->
				<td>
					<?php 
					$sotien_chiben_thu3 = 0;
					$tong_sotien_chiben_thu3 =0;
					foreach ($ds_ben_thu_3 as $key => $bnew) {
						if ($bnew['contract_id']==$valuenew['id']) {
							$sotien_chiben_thu3+=$bnew['item_unit_price'];
						}
						$tong_sotien_chiben_thu3+=$bnew['item_unit_price'];
					}
					echo number_format($sotien_chiben_thu3);
					?>
				</td>
				<td>-</td>
			</tr>
			<?php 
			$stt++;
		}
		?>
		<tr>
			<td colspan="5" style="font-weight: 600;">
				Tổng ký mới tại thời điểm kết thúc tháng
			</td>
			<td style="font-weight: 600;"><?php echo number_format($total_new); ?></td>
			<td style="font-weight: 600;"><?php echo number_format($total_new_vat); ?></td>
			<td ></td><!-- Trạng thái  -->
			<td></td>
			<td style="font-weight: 600;">
				<?php 
				$$totalnew_vat=0;
				$tong_tien_da_thu_trong_thang_moi=0;
				foreach ($tong_tien_da_thu_trong_thang as $k => $val_new) {
					if ($val_new['vat']=='published') {
						$totalnew_vat = $val_new['price']/1.1;
					}else{
						$totalnew_vat =$val_new['price'];
					}
					$tong_tien_da_thu_trong_thang_moi += $totalnew_vat;
				}
				echo number_format($tong_tien_da_thu_trong_thang_moi);
				?>
			</td>
			<td style="font-weight: 600;">
				<?php echo number_format($tong_dt_new); ?>
			</td>
			<td style="font-weight: 600;"><?php echo number_format($tong_sotien_chiben_thu3); ?></td>
			<td></td>
		</tr>
		<?php 
	}else{
		?>
		<tr>
			<td colspan="13" class="text-center">Không có dữ liệu!</td>
		</tr>
		<?php 
	}
	?>

	<tr>
		<td colspan="13" style="border: none;"></td>
	</tr>
	<tr>
		<td colspan="7" style="border: none;">
			
		</td>
		<td colspan="6" style="border: none;">
			<span style="font-style: italic;"></span>
		</td>
	</tr>
</tbody>
</table>