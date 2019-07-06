<?php
if ($check=='false') {
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
	
	if (!empty($contract_done)) {
		$stt =1;
		$tong_nghiem_thu_chua_vat=0;
		$tong_nghiem_thu_co_vat=0;
		$tong_da_thu_nt=0;
		$tong_dt_nt =0;
		$tong_tien_ben_3_nghiem_thu=0;
		foreach ($contract_done as $key => $value) {
			$so_tien_co_vat =0;
			$so_tien_chua_vat=0;
			foreach ($contract_all as $key1 => $value1) {
				if ($value1['id'] ==$value['id']) {
					$so_tien_chua_vat=$value1['co_vat'];
				}
			}
			
			$so_tien_co_vat = $so_tien_chua_vat*0.1;
			$tong_nghiem_thu_chua_vat+=$so_tien_chua_vat;
			$tong_nghiem_thu_co_vat +=$so_tien_co_vat;
			// if ($value['vat']=='unpublished') {

			// }elseif($value['vat']=='published'){
			// 	$tong_nghiem_thu_chua_vat+=$so_tien_chua_vat;
			// 	$tong_nghiem_thu_co_vat +=$so_tien_co_vat;
			// }
			?>

			<tr>
				<td><?php echo $stt; ?></td>
				<td class="text-right"><?php echo date('d-m-Y',strtotime($value['date_signing'])); ?></td>
				<td><?php echo $value['code']; ?></td>
				<td><?php echo $value['name_contract']; ?></td>
				<td><?php if($value['ten_doi_tac']!=null){echo $value['ten_doi_tac'];}else{echo '';} ?></td>
				<td class="text-right">
					<?php 
					echo number_format($so_tien_chua_vat);
					?>
				</td>
				<td class="text-right"><?php
				echo number_format($so_tien_co_vat);
				?></td>
				<td>
					<?php 
					if ($value['trang_thai_hop_dong']=='progress') {
						echo "Đang thực hiện";
					}
					if ($value['trang_thai_hop_dong']=='done') {
						echo "Đã nghiệm thu";
					}
					if ($value['trang_thai_hop_dong']=='pause') {
						echo "Tạm dừng/chưa thanh lý";
					}
					if ($value['trang_thai_hop_dong']=='liquidated') {
						echo "Đã thanh lý";
					}
					?>
				</td>
				<td class="text-right">
					<ul style="padding: 0;">
						<?php
						foreach ($tong_tien_da_thu_trong_thang as $key => $value_nk) {
							if ($value_nk['id']==$value['id'] && $value_nk['c_status']=='done') {
								echo '<li>'.$value_nk['name'].': '.date('d-m-Y',strtotime($value_nk['date_payment'])).'</li>'; 

							}
						}
						?>
					</ul>
				</td>
				<td class="text-right"><?php 
				// so tien da thu trong thang cua tung hop dong
				$total =0;
				foreach ($tong_tien_da_thu_trong_thang as $k => $val) {
					if ($val['id']==$value['id'] && $val['c_status']=='done') {
						if ($val['vat']=='published') {
							$total += $val['price']/1.1;
						}else{
							$total +=$val['price'];
						}
					}
				}
				$tong_da_thu_nt+=$total;
				echo number_format($total);
				?></td>
				<!-- thanh toans don tich -->
				<td class="text-right">
					<?php 
					$tmp =0;
					foreach ($tong_tien_thanh_toan_don_tich as $key_dt => $value_dt) {
						if ($value_dt['id'] ==$value['id'] && $value_dt['c_status']=='done') {
							if ($value_dt['vat']=='published') {
								$tmp +=$value_dt['price']/1.1;
							}else{
								$tmp +=$value_dt['price'];
							}
						}
					}
					$tong_dt_nt +=$tmp;
					echo number_format($tmp);
					?>
				</td>
				<td class="text-right">
					<?php
					$nt_ben_thu3 = 0;
					foreach ($ds_ben_thu_3 as $a => $b) {
						if ($b['contract_id']==$value['id']) {
							$nt_ben_thu3=$b['chi_phi'];
						}
					}
					$tong_tien_ben_3_nghiem_thu+=$nt_ben_thu3;
					echo number_format($nt_ben_thu3);
					?>
				</td>
				<td></td>
			</tr>
			<?php
			$stt++;
		}?>
		<tr>
			<td colspan="5" style="font-weight: 600;">
				Tổng nghiệm thu trong tháng
			</td>
			<td class="text-right" style="font-weight:600;">
				<?php 
				echo number_format($tong_nghiem_thu_chua_vat);
				?>
			</td>
			<td class="text-right" style="font-weight:600;">
				<?php 
				echo number_format($tong_nghiem_thu_co_vat);
				?>
			</td>
			<td></td>
			<td>
			</td>
			<td style="font-weight: 600;" class="text-right">
				<?php
				echo number_format($tong_da_thu_nt);
				?>
			</td>
			<td style="font-weight: 600;" class="text-right"><?php echo number_format($tong_dt_nt); ?></td>
			<td style="font-weight: 600;" class="text-right">
				<?php 
				echo number_format($tong_tien_ben_3_nghiem_thu);
				?>
			</td>
			<td></td>
		</tr>
		<?php

	}else{
		?>
		<tr>
			<td colspan="13" class="text-center" style="font-weight: 600;">Không có dữ liệu</td>
		</tr>
		<?php
	}
	
	?>

	
	<!-- ============= HỢP ĐỒNG THANH LÝ TRONG THÁNG =========  -->
	<tr>
		<td colspan="13" style="font-weight: 600;">
			B. Thanh lý trong tháng
		</td>
	</tr>
	<?php 
	if (!empty($contract_liquidated)) {
		$stt =1;
		$tong_thanh_ly_chua_vat=0;
		$tong_thanh_ly_co_vat=0;
		$so_tien_da_thu_trong_thang_tl =0;
		$tong_thanh_toan_don_tich=0;
		$tong_tien_ben_3_thanh_ly=0;
		foreach ($contract_liquidated as $key => $value_tl) {
			$so_tien_chua_vat=0;
			foreach ($contract_all as $key1 => $value1) {
				if ($value1['id'] ==$value_tl['id']) {
					$so_tien_chua_vat=$value1['co_vat'];
				}
			}
			$tong_thanh_ly_chua_vat+=$so_tien_chua_vat;
			$tong_thanh_ly_co_vat +=$so_tien_co_vat;
			$so_tien_co_vat = $so_tien_chua_vat*0.1;

			// if ($value_tl['vat']=='unpublished') {
			// 	$tong_thanh_ly_chua_vat+=$so_tien_chua_vat;
			// 	$tong_thanh_ly_co_vat +=$so_tien_co_vat;
			// }elseif($value_tl['vat']=='published'){
			// 	$tong_thanh_ly_chua_vat+=$so_tien_chua_vat;
			// 	$tong_thanh_ly_co_vat +=$so_tien_co_vat;
			// }
			?>
			<tr>
				<td><?php echo $stt; ?></td>
				<td><?php echo date('d-m-Y',strtotime($value_tl['date_signing'])); ?></td>
				<td><?php echo $value_tl['code']; ?></td>
				<td><?php echo $value_tl['name_contract']; ?></td>
				<td><?php if($value_tl['ten_doi_tac']!=null){echo $value_tl['ten_doi_tac'];}else{echo '';} ?></td>
				<td class="text-right">
					<?php
					echo number_format($so_tien_chua_vat);
					?>
				</td>
				<td class="text-right">
					<?php 
					echo number_format($so_tien_co_vat); 
					?>
				</td>
				<td>
					<?php 
					if ($value_tl['trang_thai_hop_dong']=='progress') {
						echo "Đang thực hiện";
					}
					if ($value_tl['trang_thai_hop_dong']=='done') {
						echo "Đã nghiệm thu";
					}
					if ($value_tl['trang_thai_hop_dong']=='pause') {
						echo "Tạm dừng/chưa thanh lý";
					}
					if ($value_tl['trang_thai_hop_dong']=='liquidated') {
						echo "Đã thanh lý";
					}
					?>
				</td>
				<td class="text-right">
					<ul style="padding: 0;">
						<?php
						foreach ($tong_tien_da_thu_trong_thang as $key => $value_nk) {
							if ($value_nk['id']==$value_tl['id'] && $value_nk['c_status']=='liquidated') {
								echo '<li>'.$value_nk['name'].': '.date('d-m-Y',strtotime($value_nk['date_payment'])).'</li>'; 

							}
						}
						?>
					</ul>
				</td>
				<td class="text-right">
					<?php 
					$tmp=0;
					foreach ($tong_tien_da_thu_trong_thang as $k => $value) {
						if ($value['id']==$value_tl['id'] && $value['c_status']=='liquidated') {
							if ($value['vat']=='published') {
								$tmp += $value['price']/1.1;
							}else{
								$tmp +=$value['price'];
							}
						}
					}
					$so_tien_da_thu_trong_thang_tl+=$tmp;
					echo number_format($tmp);
					?>
				</td>
				<td class="text-right">
					<?php 
					$tmp_tl =0;
					foreach ($tong_tien_thanh_toan_don_tich as $key_dt => $value_dt_tl) {
						if ($value_dt_tl['id'] == $value_tl['id'] && $value_dt_tl['c_status']=='liquidated' ) {
							if ($value_dt_tl['vat']=='published') {
								$tmp_tl +=$value_dt_tl['price']/1.1;
							}else{
								$tmp_tl +=$value_dt_tl['price'];
							}
						}
					}
					$tong_thanh_toan_don_tich +=$tmp_tl;
					echo number_format($tmp_tl);
					?>
				</td>
				<td class="text-right">
					<?php 
					$tl_ben_thu_3 = 0;
					foreach ($ds_ben_thu_3 as $a => $b) {
						if ($b['contract_id']==$value_tl['id'] && $b['tt']=='liquidated') {
							$tl_ben_thu_3=$b['chi_phi'];
						}
					}
					$tong_tien_ben_3_thanh_ly+=$tl_ben_thu_3;
					echo number_format($tl_ben_thu_3);
					?>
				</td>
				<td></td>
			</tr>
			<?php
			$stt++;
		}?>
		<tr>
			<td colspan="5" style="font-weight: 600;">
				Tổng thanh lý trong tháng
			</td>
			<td style="font-weight: 600;" class="text-right">
				<?php
				echo number_format($tong_thanh_ly_chua_vat);
				?>
			</td>
			<td style="font-weight: 600;" class="text-right">
				<?php 
				echo number_format($tong_thanh_ly_co_vat);
				?>
			</td>
			<td></td> 
			<td></td>
			<td style="font-weight: 600;" class="text-right">
				<?php 
				echo number_format($so_tien_da_thu_trong_thang_tl);
				?>
			</td>
			<td class="text-right"><?php echo number_format($tong_thanh_toan_don_tich); ?></td>
			<td style="font-weight: 600;" class="text-right">
				<?php 
				echo number_format($tong_tien_ben_3_thanh_ly);
				?>
			</td>
			<td></td>
		</tr>
		<?php
	}else{
		?>
		<tr>
			<td colspan="13" class="text-center" style="font-weight: 600;">Không có dữ liệu</td>
		</tr>
		<?php
	}
	if (!empty($contract_done) && !empty($contract_liquidated)) {
		?>
		<tr>
			<td colspan="5" style="font-weight: 600;">
				Tổng nghiệm thu và thanh lý trong tháng
			</td>
			<td style="font-weight: 600;">
				<?php 
				// echo  number_format($tong_thanh_ly_chua_vat+$tong_nghiem_thu_chua_vat);
				?>
			</td>
			<td style="font-weight: 600;">
				<?php 
				// echo  number_format($tong_thanh_ly_co_vat+$tong_nghiem_thu_co_vat);
				?>
			</td>
			<td></td>
			<td></td>
			<td style="font-weight: 600;" class="text-right">
				<?php echo number_format($so_tien_da_thu_trong_thang_tl+$tong_da_thu_nt); ?>
			</td>
			<td style="font-weight: 600;" class="text-right">
				<?php echo number_format($tong_thanh_toan_don_tich+$tong_dt_nt); ?>
			</td>
			<td style="font-weight: 600;" class="text-right">
				<?php echo number_format($tong_tien_ben_3_nghiem_thu+$tong_tien_ben_3_thanh_ly); ?>
			</td>
			<td></td>
		</tr>
	<?php }?>
	<!-- ============= HỢP ĐỒNG ĐANG THỰC HIỆN TRONG THÁNG =========  -->
	<tr>
		<td colspan="13" style="font-weight: 600; background: #e2e2e2;">II. Hợp đồng đang thực hiện dở dang tại thời điểm kết thúc tháng</td>
	</tr>
	<?php 
	if (!empty($contract_progress)) {
		$stt =1;
		$tong_dangth_chua_vat=0;
		$tong_dangth_co_vat=0;
		$so_tien_da_thu_trong_thang =0;
		$tong_thanh_toan_don_tich_dangth=0;
		$tong_tien_ben_3_dang_thuc_hien=0;
		foreach ($contract_progress as $key => $value_progress) {
			$so_tien_chua_vat=$value_progress['co_vat'];
			$so_tien_co_vat = $so_tien_chua_vat*0.1;
			$tong_dangth_chua_vat+=$so_tien_chua_vat;
			$tong_dangth_co_vat +=$so_tien_co_vat;

			// if ($value_progress['vat']=='unpublished') {
			// 	$tong_dangth_chua_vat+=$so_tien_chua_vat;
			// 	$tong_dangth_co_vat +=$so_tien_co_vat;
			// }elseif($value_progress['vat']=='published'){
			// 	$tong_dangth_chua_vat+=$so_tien_chua_vat;
			// 	$tong_dangth_co_vat +=$so_tien_co_vat;
			// }
			?>
			<tr>
				<td><?php echo $stt; ?></td>
				<td><?php echo date('d-m-Y',strtotime($value_progress['date_signing'])); ?></td>
				<td><?php echo $value_progress['code']; ?></td>
				<td><?php echo $value_progress['name_contract']; ?></td>
				<td><?php if($value_progress['ten_doi_tac']!=null){echo $value_progress['ten_doi_tac'];}else{echo '-';} ?></td>
				<td class="text-right">
					<?php 
					echo number_format($so_tien_chua_vat);
					?>
				</td>
				<td class="text-right">
					<?php 
					echo number_format($so_tien_co_vat); 
					?>
				</td>
				<td>
					<?php 
					if ($value_progress['trang_thai_hop_dong']=='progress') {
						echo "Đang thực hiện";
					}
					if ($value_progress['trang_thai_hop_dong']=='done') {
						echo "Đã nghiệm thu";
					}
					if ($value_progress['trang_thai_hop_dong']=='pause') {
						echo "Tạm dừng/chưa thanh lý";
					}
					?>
				</td>
				<td class="text-right">
					<ul style="padding: 0;">
						<?php
						foreach ($tong_tien_da_thu_trong_thang as $key => $value_nk) {
							if ($value_nk['id']==$value_progress['id']) {
								echo '<li>'.$value_nk['name'].': '.date('d-m-Y',strtotime($value_nk['date_payment'])).'</li>'; 
							}
						}
						?>
					</ul>
				</td>
				<td class="text-right">
					<?php 
					$tt=0;
					foreach ($tong_tien_da_thu_trong_thang as $k => $val) {
						if ($val['id']==$value_progress['id'] && $val['c_status']!='liquidated') {
							if ($val['vat']=='published') {
								$tt +=$val['price']/1.1;
							}else{
								$tt +=$val['price'];
							}
						}
					}
					$so_tien_da_thu_trong_thang += $tt;
					echo number_format($tt);
					?>
				</td>
				<td class="text-right">
					<?php 
					$tmp_p =0;
					foreach ($tong_tien_thanh_toan_don_tich as $key_dt => $value_dt_p) {
						if ($value_dt_p['id'] ==$value_progress['id'] && $value_dt_p['c_status']!='liquidated') {
							if ($value_dt_p['vat']=='published') {
								$tmp_p +=$value_dt_p['price']/1.1;
							}else{
								$tmp_p +=$value_dt_p['price'];
							}
						}
					}
					$tong_thanh_toan_don_tich_dangth +=$tmp_p;
					echo number_format($tmp_p);
					?>
				</td>
				<td class="text-right">
					<?php 
					$sotien_chiben_thu3=0;
					foreach ($ds_ben_thu_3 as $a => $b) {
						if ($b['contract_id']==$value_progress['id'] && $b['tt']=='progress') {
							$sotien_chiben_thu3=$b['chi_phi'];
						}
					}
					$tong_tien_ben_3_dang_thuc_hien+=$sotien_chiben_thu3;
					echo number_format($sotien_chiben_thu3);
					?>
				</td>
				<td>-</td>
			</tr>
			<?php
			$stt++;
		}?>
		<tr>
			<td colspan="5" style="font-weight: 600;">
				Tổng HĐ đang thực hiện dở dang tại thời điểm kết thúc tháng
			</td>
			<td style="font-weight: 600;" class="text-right">
				<?php 
				echo number_format($tong_dangth_chua_vat);
				?>
			</td>
			<td style="font-weight: 600;" class="text-right">
				<?php 
				echo number_format($tong_dangth_co_vat);
				?>
			</td>
			<td></td>
			<td></td>
			<td style="font-weight: 600;" class="text-right">
				<?php
				echo number_format($so_tien_da_thu_trong_thang);
				?>
			</td>
			<td style="font-weight: 600;" class="text-right">
				<?php 
				echo number_format($tong_thanh_toan_don_tich_dangth);
				?>
			</td>
			<td style="font-weight: 600;" class="text-right">
				<?php 
				echo number_format($tong_tien_ben_3_dang_thuc_hien);
				?>
			</td>
			<td></td>
		</tr>
		<?php
	}else{
		?>
		<tr>
			<td colspan="13" class="text-center" style="font-weight: 600;">Không có dữ liệu</td>
		</tr>
		<?php
	}
	?>
	
	<!-- ============= HỢP ĐỒNG ĐĂNG KÝ MỚI TRONG THÁNG=========  -->
	<tr>
		<td colspan="13" style="font-weight: 600;">III.Hợp đồng ký mới trong tháng</td>
	</tr>
	<?php
	if (!empty($contract)) {
		$stt=1;
		$total_new = 0;
		$tong_hdmoi_chua_vat=0;
		$tong_hdmoi_co_vat=0;
		$so_tien_da_thu_trong_thang=0;
		$tong_thanh_toan_don_tich_moi=0;
		$tong_sotien_chiben_thu3 =0;
		foreach ($contract as $key => $valuenew) {
			$so_tien_chua_vat=$valuenew['co_vat'];
			$so_tien_co_vat = $so_tien_chua_vat*0.1;
			$tong_hdmoi_chua_vat+=$so_tien_chua_vat;
			$tong_hdmoi_co_vat +=$so_tien_co_vat;
			// if ($valuenew['vat']=='unpublished') {
			// 	$tong_hdmoi_chua_vat+=$so_tien_chua_vat;
			// 	$tong_hdmoi_co_vat +=$so_tien_co_vat;
			// }elseif($valuenew['vat']=='published'){
			// 	$tong_hdmoi_chua_vat+=$so_tien_chua_vat;
			// 	$tong_hdmoi_co_vat +=$so_tien_co_vat;
			// }
			?>
			<tr>
				<td><?php echo $stt; ?></td>
				<td><?php echo date('d-m-Y',strtotime($valuenew['date_signing'])); ?></td>
				<td><?php echo $valuenew['code']; ?></td>
				<td><?php echo $valuenew['name_contract']; ?></td>
				<td><?php if($valuenew['ten_doi_tac']!=null){echo $valuenew['ten_doi_tac'];}else{echo '-';} ?></td>
				<td class="text-right">
					<?php 
					echo number_format($so_tien_chua_vat);
					?>
				</td>
				<td class="text-right">
					<?php echo number_format($so_tien_co_vat); ?>
				</td>
				<td>
					<?php 
					if ($valuenew['trang_thai_hop_dong']=='progress') {
						echo "Đang thực hiện";
					}
					if ($valuenew['trang_thai_hop_dong']=='done') {
						echo "Đã nghiệm thu";
					}
					if ($valuenew['trang_thai_hop_dong']=='pause') {
						echo "Tạm dừng/chưa thanh lý";
					}
					if ($valuenew['trang_thai_hop_dong']=='liquidated') {
						echo "Đã thanh lý";
					}
					?>
				</td>
				<td class="text-right">
					<ul style="padding: 0;">
						<?php
						foreach ($tong_tien_da_thu_trong_thang as $key => $value_nk) {
							if ($value_nk['id']==$valuenew['id']) {
								echo '<li>'.$value_nk['name'].': '.date('d-m-Y',strtotime($value_nk['date_payment'])).'</li>'; 
							}
						}
						?>
					</ul>
				</td>
				<td class="text-right">
					<?php 
					$totalnew =0;
					foreach ($tong_tien_da_thu_trong_thang as $k => $val_new) {
						if ($val_new['id']==$valuenew['id']) {
							if ($val_new['vat']=='published') {
								$totalnew += $val_new['price']/1.1;
							}else{
								$totalnew +=$val_new['price'];
							}
						}	
					}
					$so_tien_da_thu_trong_thang += $totalnew;
					echo number_format($totalnew);
					?>
				</td>
				<!-- thanh toan don tich -->
				<td class="text-right">
					<?php 
					$tmp_new =0;
					foreach ($tong_tien_thanh_toan_don_tich as $key_dt => $value_new) {
						if ($value_new['id'] ==$valuenew['id']) {
							if ($value_new['vat']=='published') {
								$tmp_new +=$value_new['price']/1.1;
							}else{
								$tmp_new+=$value_new['price'];
							}
						}
					}
					$tong_thanh_toan_don_tich_moi +=$tmp_new;
					echo number_format($tmp_new);
					?>
				</td>
				<!-- tong da chi cho ben thu 3 -->
				<td class="text-right">
					<?php 
					$sotien_chiben_thu3 = 0;
					foreach ($ds_ben_thu_3 as $key => $bnew) {
						if ($bnew['contract_id']==$valuenew['id']) {
							$sotien_chiben_thu3=$bnew['chi_phi'];
						}
					}
					$tong_sotien_chiben_thu3+=$sotien_chiben_thu3;
					echo number_format($sotien_chiben_thu3);
					?>
				</td>
				<td></td>
			</tr>
			<?php 
			$stt++;
		}?>
		<tr>
			<td colspan="5" style="font-weight: 600;">
				Tổng ký mới tại thời điểm kết thúc tháng
			</td>
			<td style="font-weight: 600;" class="text-right"><?php echo number_format($tong_hdmoi_chua_vat); ?></td>
			<td style="font-weight: 600;" class="text-right"><?php echo number_format($tong_hdmoi_co_vat); ?></td>
			<td ></td><!-- Trạng thái  -->
			<td></td>
			<td style="font-weight: 600;" class="text-right">
				<?php 
				echo number_format($so_tien_da_thu_trong_thang);
				?>
			</td>
			<td style="font-weight: 600;" class="text-right">
				<?php 
				// echo number_format($tong_thanh_toan_don_tich_dangth+$tong_thanh_toan_don_tich+$tong_dt_nt);
				echo number_format($tong_thanh_toan_don_tich_moi);

				?>
			</td>
			<td style="font-weight: 600;" class="text-right"><?php echo number_format($tong_sotien_chiben_thu3); ?></td>
			<td></td>
		</tr>
		<?php
	}else{
		?>
		<tr>
			<td class="text-center" style="font-weight: 600;" colspan="13">Không có dữ liệu</td>
		</tr>
		<?php 
	}
	?>
	
	<?php 
}elseif ($check=='true') {

	?>
	<tr>
		<td style="font-weight: 600; text-align: left;">I. Tư vấn đầu tư chứng khoán</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="font-weight: 600; text-align: left;" colspan="6">II. Tư vấn tài chính</td>
	</tr>
	<tr>
		<td>Tư vấn phát hành</td>
		<td>
			<?php echo $tvph_cot1; ?>
		</td>
		<td>
			<?php echo $tvph_cot2; ?>
		</td>
		<td>
			<?php echo $tvph_cot3; ?>
		</td>
		<td>
			<?php echo $tvph_cot4; ?>
		</td>
		<td>
			
		</td>
	</tr>
	<tr>
		<td>Tư vấn chuyển đổi</td>
		<td>
			<?php echo $tvcd_cot1; ?>
		</td>
		<td>
			<?php echo $tvcd_cot2; ?>
		</td>
		<td>
			<?php echo $tvcd_cot3; ?>
		</td>
		<td>
			<?php echo $tvcd_cot4; ?>
		</td>
		<td></td>
	</tr>
	<tr>
		<td>Tư vấn khác</td>
		<td>
			<?php echo $tvk_cot1;?>
		</td>
		<td>
			<?php echo $tvk_cot2;?>
		</td>
		<td>
			<?php echo $tvk_cot3;?>
		</td>
		<td>
			<?php echo $tvk_cot4;?>
		</td>
		<td></td>
	</tr>
	<tr>
		<td style="font-weight: 600; text-align: left;">III. Dịch vụ khác</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	
	<tr>
		<td>Cộng</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr class="total2">
		<td style="font-weight: 600;">TỔNG CỘNG</td>
		<td style="font-weight: 600;">
			<?php 
			echo $tvk_cot1+$tvcd_cot1+$tvph_cot1;
			?>
		</td>
		<td style="font-weight: 600;">
			<?php 
			echo $tvk_cot2+$tvcd_cot2+$tvph_cot2;
			?>
		</td>
		<td style="font-weight: 600;">
			<?php 
			echo $tvk_cot3+$tvcd_cot3+$tvph_cot3;
			?>
		</td>
		<td style="font-weight: 600;">
			<?php 
			echo $tvk_cot4+$tvcd_cot4+$tvph_cot4;
			?>
		</td>
		<td style="font-weight: 600;">
			<?php 
			// echo number_format($tvk_cot5+$tvcd_cot5+$tvph_cot5);
			?>
		</td>
	</tr>
	
	<?php 
	// Báo cáo hoạt động kinh doanh ca nhan
}elseif ($check=='1') {
	if ($thoigian=='THANG') {
		$date= date_create($input['year'].'-'.$input['month']);
		$date_e = date_format($date,"Y-m-t");
		$date_s = date_format($date,"Y-m-d");
	}elseif ($thoigian=='QUY') {
		switch ($input['month']) {
			case 1:
			$date_ss = date_create($input['year'].'-01');
			$date = date_create($input['year'].'-03');
			break;
			case 2:
			$date_ss = date_create($input['year'].'-04');
			$date = date_create($input['year'].'-06');
			break;
			case 3:
			$date_ss = date_create($input['year'].'-07');
			$date = date_create($input['year'].'-09');
			break;
			case 4:
			$date_ss = date_create($input['year'].'-10');
			$date = date_create($input['year'].'-12');
			break;
		}
		$date_e = date_format($date,"Y-m-t");
		$date_s = date_format($date_ss,"Y-m-d");
	}elseif ($thoigian=='NAM') {
		$date = date_create($input['year'].'-12');
		$date_ss = date_create($input['year'].'-01');

		$date_e = date_format($date,"Y-m-t");
		$date_s = date_format($date_ss,"Y-m-d");
	}else{
		$date1 = date_create($input['date_start']);
		$date2 = date_create($input['date_end']);
		$date_e = date_format($date2,"Y-m-d");
		$date_s = date_format($date1,"Y-m-d");
	}
	?>
	<h3 class="text-center"><?php echo $title; ?></h3>
	
	<div class="row">
		<div class="controller">
			<div class="col-md-12">
				<h4 style="font-weight:600;">I. Thông tin nhân viên</h4>
				<?php  
				
				foreach ($info_employee as $key => $value) {
					if ($value['employee_id']==$employee_id) {
						$name = 'Tên nhân viên: '.$value['first_name'];
						$location_name =' Khu vực: '.$value['location_name'];
						$rank =' Ngạch: '.$value['rank'];
						$level = ' Cấp bậc: '.$value['level'];
						$phone_number = ' Số điện thoại: '.$value['phone_number'];
						$email = ' Email: '.$value['email'];
						$image_id = $value['image_id'];
					}
				}
				$url_image = base_url()."app_files/view/$image_id";
				?>
				<div class="col-md-3">
					<div class="images" >
						<img src="<?php echo $url_image; ?>" alt="" style="max-width:200px;">
					</div>
				</div>
				<div class="col-md-9">
					<ul id="info_employee_dt">
						<li><?php echo $name; ?></li>
						<li><?php echo $location_name; ?></li>
						<li><?php echo $rank; ?></li>
						<li><?php echo $level; ?></li>
						<li><?php echo $phone_number; ?></li>
						<li><?php echo $email; ?></li>
					</ul>
				</div>
			</div>
		</div>
		<style>
		#info_employee_dt li{
			line-height: 30px;
		}
	</style>
</div>
<h4 style="font-weight:600;"><?php echo $title_2;?></h4>
<h5 id="title_table1" style="font-weight:600;">1. Thông tin các dự án, công việc đang thực hiện</h5>
<!-- <p style="font-style: italic;">(Bao gồm các dự án nhân viên đó đang thực hiện)</p> -->

<div class="table-responsive">
	<table class="table tablesorter table-reports table-bordered display table-hover" >
		<thead>
			<!-- Tên dự án, Mã Hợp đồng, Hợp đồng liên quan, tên dịch vụ, Người phụ trách, Người tham gia, Tình trạng của dự án -->
			<tr style="background: #bfbfbf;">
				<th>STT</th>
				<th width="15%">Tên dự án</th>
				<th width="14%">Mã Hợp đồng</th>
				<th width="14%">Hợp đồng liên quan</th>
				<th width="14%">Tên dịch vụ</th>
				<th width="14%">Người phụ trách</th>
				<th width="14%">Người tham gia</th>
				<th width="14%">Tình trạng của dự án</th>
			</tr>
		</thead>
		<tbody id="tbody-report-person">
			<?php
			if ($thoigian=='NAM') {
				$stt=1;
				if (!empty($task_all)) {
					foreach ($task_all as $key => $value) {
						$data['nguoi_tham_gia_da'] =$this->Kpi_Person->get_employee_join_tasks(array('task_id'=>$value['task_id']))->where('(tu.is_join=1 OR tu.is_implement=1)')->group_by('e.id')->get()->result_array();
						$date_finish = strtotime($value['date_finish']);
						$date_end = strtotime($value['date_end']);
						$date_start = strtotime($value['date_start']);
						$date_pheduyet =  strtotime($value['date_pheduyet']);
						if ($value['pheduyet']!=1){
							if (($date_start - strtotime($date_e))<=0)
							{
								?>
								<tr>
									<td><?php echo $stt; ?></td>
									<td><?php echo $value['name_task']; ?></td>
									<td><?php echo $value['code']; ?></td>
									<td>
										<?php echo $value['name_contract']; ?>
									</td>
									<td><?php echo $value['ten_dv']; ?></td>
									<td class="text-left">
										<ul>
											<?php
											$nguoi_tham_gia ='';
											if (!empty($data['nguoi_tham_gia_da'])) {
												foreach ($data['nguoi_tham_gia_da'] as $key => $valpt) {
													if ($valpt['is_implement']==1) {
														echo '<li>'.$valpt['username'].'</li>';
													}
													$nguoi_tham_gia .= '<li>'.$valpt['username'].'</li>';;
												}
											}

											?>
										</ul>
									</td>
									<td>
										<ul style="padding: 0;">
											<?php  
											echo $nguoi_tham_gia
											?>
										</ul>
									</td>
									<td class="text-right">
										<?php echo 'Đang thực hiện ('.round($value['progress'],2).'%)'; ?>
									</td>
								</tr>
								<?php
								$stt++;
							}
							
						}
						elseif ($value['pheduyet']==1) {
							if (($date_pheduyet - strtotime($date_e))<=0 && ($date_pheduyet - strtotime($date_s)) >=0)
							{
								?>
								<tr>
									<td><?php echo $stt; ?></td>
									<td><?php echo $value['name_task']; ?></td>
									<td><?php echo $value['code']; ?></td>
									<td>
										<?php echo $value['name_contract']; ?>
									</td>
									<td><?php echo $value['ten_dv']; ?></td>
									<td class="text-left">
										<ul>
											<?php
											$nguoi_tham_gia ='';
											if (!empty($data['nguoi_tham_gia_da'])) {
												foreach ($data['nguoi_tham_gia_da'] as $key => $valpt) {
													if ($valpt['is_implement']==1) {
														echo '<li>'.$valpt['username'].'</li>';
													}
													$nguoi_tham_gia .= '<li>'.$valpt['username'].'</li>';;
												}
											}

											?>
										</ul>
									</td>
									<td>
										<ul style="padding: 0;">
											<?php  
											echo $nguoi_tham_gia
											?>
										</ul>
									</td>
									<td class="text-right">
										<?php echo 'Đã hoàn thành ('.round($value['progress'],2).'%)'; ?>
									</td>
								</tr>
								<?php
								$stt++;
							}
							if (strtotime($date_e)-$date_start>=0 && $date_pheduyet-strtotime($date_e)>=0) {
								?>
								<tr>
									<td><?php echo $stt; ?></td>
									<td><?php echo $value['name_task']; ?></td>
									<td><?php echo $value['code']; ?></td>
									<td>
										<?php echo $value['name_contract']; ?>
									</td>
									<td><?php echo $value['ten_dv']; ?></td>
									<td class="text-left">
										<ul>
											<?php
											$nguoi_tham_gia ='';
											if (!empty($data['nguoi_tham_gia_da'])) {
												foreach ($data['nguoi_tham_gia_da'] as $key => $valpt) {
													if ($valpt['is_implement']==1) {
														echo '<li>'.$valpt['username'].'</li>';
													}
													$nguoi_tham_gia .= '<li>'.$valpt['username'].'</li>';;
												}
											}

											?>
										</ul>
									</td>
									<td>
										<ul style="padding: 0;">
											<?php  
											echo $nguoi_tham_gia
											?>
										</ul>
									</td>
									<td class="text-right">
										<?php echo 'Đang thực hiện ('.round($value['progress'],2).'%)'; ?>
									</td>
								</tr>
								<?php
								$stt++;
							}
						}
						
					}
				}
			}else{
				if (!empty($task_all)) {
					$stt=1;
					foreach ($task_all as $key => $value) {
						$data['nguoi_tham_gia_da'] =$this->Kpi_Person->get_employee_join_tasks(array('task_id'=>$value['task_id']))->where('(tu.is_join=1 OR tu.is_implement=1)')->group_by('e.id')->get()->result_array();
						$date_finish = strtotime($value['date_finish']);
						$date_end = strtotime($value['date_end']);
						$date_start = strtotime($value['date_start']);
						$date_pheduyet =  strtotime($value['date_pheduyet']);
						if ($value['pheduyet']!=1){
							if (($date_start - strtotime($date_e))<=0)
							{
								?>
								<tr>
									<td><?php echo $stt; ?></td>
									<td><?php echo $value['name_task']; ?></td>
									<td><?php echo $value['code']; ?></td>
									<td>
										<?php echo $value['name_contract']; ?>
									</td>
									<td><?php echo $value['ten_dv']; ?></td>
									<td class="text-left">
										<ul>
											<?php
											$nguoi_tham_gia ='';
											if (!empty($data['nguoi_tham_gia_da'])) {
												foreach ($data['nguoi_tham_gia_da'] as $key => $valpt) {
													if ($valpt['is_implement']==1) {
														echo '<li>'.$valpt['username'].'</li>';
													}
													$nguoi_tham_gia .= '<li>'.$valpt['username'].'</li>';;
												}
											}

											?>
										</ul>
									</td>
									<td>
										<ul style="padding: 0;">
											<?php  
											echo $nguoi_tham_gia
											?>
										</ul>
									</td>
									<td class="text-right">
										<?php echo 'Đang thực hiện ('.round($value['progress'],2).'%)'; ?>
									</td>
								</tr>
								<?php
								$stt++;
							}
							
						}
					}
				}else{
					echo '<tr><td colspan="8" class="text-center">Không có dữ liệu!</td></tr>';
				}
			}
			
			?>
		</tbody>
	</table>
</div>

<h5 style="font-weight:600;">2. Tình hình hoàn thành các dự án, công việc </h5>

<div class="table-responsive">
	<div class="table-responsive">
		<table class="table tablesorter table-reports table-bordered display table-hover">
			<thead>
				<tr style="background: #bfbfbf;">
					<th>STT</th>
					<th width="15%">Tên dự án</th>
					<th width="15%">Giai đoạn đang thực hiện</th>
					<th width="25%">Công việc đang thực hiện</th>
					<th width="15%">Tỷ lệ đã hoàn thành của dự án</th>
					<th width="10%">% Đóng góp của nhân viên vào cả dự án</th>
					<th width="25%">Công việc thực hiện chậm</th>
				</tr>
			</thead>
			<tbody>
				<?php
				
				if (!empty($task_all)) { 
					$stt=1; 
					foreach ($task_all as $key => $value)
					{
						$date_finish = strtotime($value['date_finish']);
						$date_end = strtotime($value['date_end']);
						$date_start = strtotime($value['date_start']);
						$date_pheduyet =  strtotime($value['date_pheduyet']);
						if ($value['pheduyet']!=1){
							if (($date_start - strtotime($date_e))<=0){
								?>
								<tr>
									<td><?php echo $stt; ?></td>
									<td id="<?php echo $value['task_id']; ?>">
										<?php echo $value['name_task'] ?>
									</td>
									<td>
										<ul style="padding: 0;">
											<?php  
											if (!empty($giai_doan_dang_thuc_hien)) {
												foreach ($giai_doan_dang_thuc_hien as $key => $value_gd) {
													if ($value_gd['parent'] == $value['task_id']) {
														echo '<li>'.$value_gd['name'].'</li>';
													}
												}
											}
											?>
										</ul>
									</td>
									<td>
										<ul style="padding: 0;">
											<?php  
											if (!empty($cong_viec_dang_thuc_hien)) {
												foreach ($cong_viec_dang_thuc_hien as $key => $value_cv) {
													if ($value_cv['project_id']==$value['task_id']) {
														echo $value_cv['name'].'<br>';
													}
												}
											}
											?>
										</ul>
									</td>
									<td class="text-right">
										<?php echo round($value['progress'],2).'%'; ?>
									</td>
									<td class="text-right">
										<?php
										if (!empty($kpi)) {
											foreach ($kpi as $key => $value_kpi) {
												if ($value_kpi['task_id'] == $value['task_id']) {
													$arr_ratio = json_decode($value_kpi['ratio'],true);
													$arr_employeeID = json_decode($value_kpi['employee_id'],true);
													for($i=0;$i<count($arr_employeeID);$i++){
														if ($arr_employeeID[$i] == $employee_id) {
															echo $arr_ratio[$i].'%';
														}
													}
												}
											} 
										}
										?>
									</td>
									<td class="text-left">
										<?php  
										if (!empty($task_child_delay)) {
											foreach ($task_child_delay as $key => $value_delay) {
												if ($value_delay['project_id'] == $value['task_id']) {
													if ($value_delay['progress']==100) {
														if ((strtotime($value_delay['date_finish'])-strtotime($value_delay['date_end']))>0) {
															echo '- '.$value_delay['name'].' ('.round($value_delay['progress'],2) .'%)<br>';
														}
													}else{
														if ((strtotime($value_delay['date_end']) - strtotime(Date('Y-m-d')))<0) {
															echo '- '.$value_delay['name'].' ('.round($value_delay['progress'],2) .'%)<br>';
														}
													}
												}
											}
										}
										?>
									</td>
								</tr>
								<?php 
								$stt++; 
							}
						}
						if ($thoigian=='NAM'|| $thoigian=='THANG'|| $thoigian=='QUY') {
							if ($value['pheduyet']==1)
							{
								if (($date_pheduyet - strtotime($date_e))<=0 && ($date_pheduyet - strtotime($date_s)) >=0)
								{
									?>
									<tr>
										<td><?php echo $stt; ?></td>
										<td id="<?php echo $value['task_id']; ?>">
											<?php echo $value['name_task'] ?>
										</td>
										<td>
											<ul style="padding: 0;">
												<?php  
												if (!empty($giai_doan_dang_thuc_hien)) {
													foreach ($giai_doan_dang_thuc_hien as $key => $value_gd) {
														if ($value_gd['parent'] == $value['task_id']) {
															echo '<li>'.$value_gd['name'].'</li>';
														}
													}
												}
												?>
											</ul>
										</td>
										<td>
											<ul style="padding: 0;">
												<?php  
												if (!empty($cong_viec_dang_thuc_hien)) {
													foreach ($cong_viec_dang_thuc_hien as $key => $value_cv) {
														if ($value_cv['project_id']==$value['task_id']) {
															echo $value_cv['name'].'<br>';
														}
													}
												}
												?>
											</ul>
										</td>
										<td class="text-right">
											<?php echo round($value['progress'],2).'%'; ?>
										</td>
										<td class="text-right">
											<?php
											if (!empty($kpi)) {
												foreach ($kpi as $key => $value_kpi) {
													if ($value_kpi['task_id'] == $value['task_id']) {
														$arr_ratio = json_decode($value_kpi['ratio'],true);
														$arr_employeeID = json_decode($value_kpi['employee_id'],true);
														for($i=0;$i<count($arr_employeeID);$i++){
															if ($arr_employeeID[$i] == $employee_id) {
																echo $arr_ratio[$i].'%';
															}
														}
													}
												} 
											}
											?>
										</td>
										<td class="text-left">
											<?php  
											if (!empty($task_child_delay)) {
												foreach ($task_child_delay as $key => $value_delay) {
													if ($value_delay['project_id'] == $value['task_id']) {
														if ($value_delay['progress']==100) {
															if ((strtotime($value_delay['date_finish'])-strtotime($value_delay['date_end']))>0) {
																echo '- '.$value_delay['name'].' ('.round($value_delay['progress'],2) .'%)<br>';
															}
														}else{
															if ((strtotime($value_delay['date_end']) - strtotime(Date('Y-m-d')))<0) {
																echo '- '.$value_delay['name'].' ('.round($value_delay['progress'],2) .'%)<br>';
															}
														}
													}
												}
											}
											?>
										</td>
									</tr>
									<?php
									$stt++;
								}
								if (strtotime($date_e)-$date_start>=0 && $date_pheduyet-strtotime($date_e)>=0)
								{
									?>
									<tr>
										<td><?php echo $stt; ?></td>
										<td id="<?php echo $value['task_id']; ?>">
											<?php echo $value['name_task'] ?>
										</td>
										<td>
											<ul style="padding: 0;">
												<?php  
												if (!empty($giai_doan_dang_thuc_hien)) {
													foreach ($giai_doan_dang_thuc_hien as $key => $value_gd) {
														if ($value_gd['parent'] == $value['task_id']) {
															echo '<li>'.$value_gd['name'].'</li>';
														}
													}
												}
												?>
											</ul>
										</td>
										<td>
											<ul style="padding: 0;">
												<?php  
												if (!empty($cong_viec_dang_thuc_hien)) {
													foreach ($cong_viec_dang_thuc_hien as $key => $value_cv) {
														if ($value_cv['project_id']==$value['task_id']) {
															echo $value_cv['name'].'<br>';
														}
													}
												}
												?>
											</ul>
										</td>
										<td class="text-right">
											<?php echo round($value['progress'],2).'%'; ?>
										</td>
										<td class="text-right">
											<?php
											if (!empty($kpi)) {
												foreach ($kpi as $key => $value_kpi) {
													if ($value_kpi['task_id'] == $value['task_id']) {
														$arr_ratio = json_decode($value_kpi['ratio'],true);
														$arr_employeeID = json_decode($value_kpi['employee_id'],true);
														for($i=0;$i<count($arr_employeeID);$i++){
															if ($arr_employeeID[$i] == $employee_id) {
																echo $arr_ratio[$i].'%';
															}
														}
													}
												} 
											}
											?>
										</td>
										<td class="text-left">
											<?php  
											if (!empty($task_child_delay)) {
												foreach ($task_child_delay as $key => $value_delay) {
													if ($value_delay['project_id'] == $value['task_id']) {
														if ($value_delay['progress']==100) {
															if ((strtotime($value_delay['date_finish'])-strtotime($value_delay['date_end']))>0) {
																echo '- '.$value_delay['name'].' ('.round($value_delay['progress'],2) .'%)<br>';
															}
														}else{
															if ((strtotime($value_delay['date_end']) - strtotime(Date('Y-m-d')))<0) {
																echo '- '.$value_delay['name'].' ('.round($value_delay['progress'],2) .'%)<br>';
															}
														}
													}
												}
											}
											?>
										</td>
									</tr>
									<?php
									$stt++;
								}

							}
						}
					} ?>
					<?php 
				}else{
					echo '<tr><td colspan="7" rowspan="" headers="" class="text-center" style="font-weight:600;">Không có dữ liệu!</td></tr>';
				}
				?>
			</tbody>
		</table>
	</div>
</div>
<h4 style="font-weight:600;">III. Phân tích, đánh giá nhân viên</h4>
<h5 style="font-weight:600;">1. Bảng doanh thu</h5>
<div class="row">
	<div class="col-md-6">
		<?php if ($input['check']=='THANG' || $input['check']=='QUY' || $input['check']=='NAM'): ?>
			<table class="table tablesorter table-reports table-bordered display table-hover" id="table_task_not_revenue">
				<thead>
					<tr style="background: #bfbfbf;">
						<th>
							<?php 
							if ($input['check'] == 'THANG') {
								echo 'Tháng';
							}elseif ($input['check'] == 'QUY') {
								echo 'Quý';
							}elseif ($input['check'] == 'NAM') {
								echo 'Năm';
							}
							?></th>
							<th>Doanh thu</th>
						</tr>
					</thead>
					<tbody id="doanh_thu_nhan_vien">

					</tbody>
				</table>
			<?php endif ?>

		</div>

	</div>
	<h5 style="font-weight:600;">2. Thống kê các công việc không tạo doanh thu</h5>
	<div class="table-responsive">
		<div class="table-responsive">
			<table class="table tablesorter table-reports table-bordered display table-hover" id="table_task_not_revenue">
				<thead>
					<tr style="background: #bfbfbf;">
						<th width="10%">STT</th>
						<th>Tên công việc</th>
						<th>Thời gian</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if (!empty($task_not_revenue)) {
						$stt=1;
						foreach ($task_not_revenue as $key => $value) {
							echo '<tr>';
							?>
							<td class="text-center"><?php echo $stt; ?></td>
							<td><?php echo $value['name']; ?></td>
							<td class="text-center">
								<?php 
								$it = date_diff(date_create($value['modified']), date_create($value['date_end']));
								$m = $it->format('%m');
								$y = $it->format('%y');
								$d = $it->format('%d');
								if ($y>0) {
									echo $it->format('%y năm %m tháng %d ngày');
								}elseif ($m>0) {
									echo $it->format('%m tháng %d ngày');
								}elseif($d>0){
									echo $d.' ngày';
								}else{
									echo $it->format('%h giờ');
								}
								?>
							</td>

							<?php
							echo '</tr>';
							$stt++;
						}  
					}else{
						?>
						<tr>
							<td colspan="3" class="text-center" style="font-weight: 600;">Không tồn tại dữ liệu!</td>
						</tr>
						<?php
					}  
					?>
				</tbody>
			</table>
		</div>
	</div>

	<?php
}elseif ($check=='BCP') {
	if (!empty($check_tp)) {
		if ($check_tp=='THANG') {
			$time = 'tháng '.$datetime['month'].'/'.$datetime['year'];
		}elseif($check_tp=='QUY'){
			$time = 'quý '.$datetime['month'].'/'.$datetime['year'];
		}else{
			$time = 'năm '.$datetime['year'];
		}
	}
	if ($check_tp=='THANG') {
		$date= date_create($datetime['year'].'-'.$datetime['month']);
		$date_e = date_format($date,"Y-m-t");
		$date_s = date_format($date,"Y-m-d");
	}elseif ($check_tp=='QUY') {
		switch ($datetime['month']) {
			case 1:
			$date_ss = date_create($datetime['year'].'-01');
			$date = date_create($datetime['year'].'-03');
			break;
			case 2:
			$date_ss = date_create($datetime['year'].'-04');
			$date = date_create($datetime['year'].'-06');
			break;
			case 3:
			$date_ss = date_create($datetime['year'].'-07');
			$date = date_create($datetime['year'].'-09');
			break;
			case 4:
			$date_ss = date_create($datetime['year'].'-10');
			$date = date_create($datetime['year'].'-12');
			break;
		}
		$date_e = date_format($date,"Y-m-t");
		$date_s = date_format($date_ss,"Y-m-d");
	}else{
		$date = date_create($datetime['year'].'-12');
		$date_ss = date_create($datetime['year'].'-01');
		$date_e = date_format($date,"Y-m-t");
		$date_s = date_format($date_ss,"Y-m-d");
	}
	?>
	<?php  
	if ($check_tp=='THANG') {
		?>
		<h4 style="font-weight: 600;">I. Tình hình thực hiện các hợp đồng trong <?php echo $time; ?></h4>
		<div class="table-responsive">
			<div class="table-responsive">
				<!-- <p style="font-style: italic;">(Gồm các hợp đồng đã hoàn thành hoặc đang thực hiện trong <?php echo $time; ?>)</p> -->
				<table class="table tablesorter table-reports table-bordered display table-hover" id="table_contract_in_month">
					<thead>
						<tr style="background: #bfbfbf;">
							<th width="5%">STT</th>
							<th width="20%">Tên dự án</th>
							<th width="10%">Tên hợp đồng</th>
							<th width="15%">Tên khách hàng</th>
							<th width="15%">Loại dịch vụ</th>
							<th width="10%">Người phụ trách</th>
							<th width="10%">Người tham gia</th>
							<th width="10%">Tình trạng của hợp đồng</th>
						</tr>
					</thead>
					<tbody>
						<?php
									// echo "<pre>"; print_r($contract_done); die();
						$stt=1;
						if (!empty($contract_complete)) {
							foreach ($contract_complete as $key => $value) {
								if ($value['status']=='progress') {
									if (strtotime($value['ct_date_start'])-strtotime($date_e)<=0) {
										?>
										<tr>
											<td><?php echo $stt; ?></td>
											<td><?php echo $value['name_task']; ?></td>
											<td><?php echo $value['name_contract']; ?></td>
											<td><?php echo $value['ten_kh']; ?></td>
											<td><?php echo $value['ten_dv']; ?></td>
											<td class="text-left">
												<!-- nguoi phu trach -->
												<?php
												$nguoi_phu_trach='';
												$nguoi_tham_gia='';
												if (!empty($nguoi_tham_gia_da)) {
													foreach ($nguoi_tham_gia_da as $key => $value_tg) {
														if ($value_tg['id'] == $value['task_id']) {
															if ($value_tg['is_implement']==1) {
																echo $value_tg['username'].'<br>';
															}
															if ($value_tg['is_join']==1) {
																$nguoi_tham_gia .=$value_tg['username'].'<br>';
															}
														}
													}
												}
												?>
											</td>
											<td class="text-left"><?php echo $nguoi_tham_gia; ?></td>
											<td><?php 
											echo "Đang thực hiện";
											?></td>
										</tr>
										<?php
									}
									$stt++;
								}
								
							}

						}
						if (!empty($contract_done)) {
							foreach ($contract_done as $key => $value) {
								?>
								<tr>
									<td><?php echo $stt; ?></td>
									<td><?php echo $value['name_task']; ?></td>
									<td><?php echo $value['name_contract']; ?></td>
									<td><?php echo $value['ten_doi_tac']; ?></td>
									<td><?php echo $value['ten_dv']; ?></td>
									<td class="text-left">
										<!-- nguoi phu trach -->
										<?php
										$nguoi_phu_trach='';
										$nguoi_tham_gia='';
										if (!empty($nguoi_tham_gia_da)) {
											foreach ($nguoi_tham_gia_da as $key => $value_tg) {
												if ($value_tg['id'] == $value['task_id']) {
													if ($value_tg['is_implement']==1) {
														echo $value_tg['username'].'<br>';
													}
													if ($value_tg['is_join']==1) {
														$nguoi_tham_gia .=$value_tg['username'].'<br>';
													}
												}
											}
										}
										?>
									</td>
									<td class="text-left"><?php echo $nguoi_tham_gia; ?></td>
									<td><?php 
									echo "Đã nghiệm thu";
									?></td>
								</tr>
								<?php
								$stt++;
							}
						}

						if (!empty($contract_liquidated)) {
							foreach ($contract_liquidated as $key => $value) {
								?>
								<tr>
									<td><?php echo $stt; ?></td>
									<td><?php echo $value['name_task']; ?></td>
									<td><?php echo $value['name_contract']; ?></td>
									<td><?php echo $value['ten_doi_tac']; ?></td>
									<td><?php echo $value['ten_dv']; ?></td>
									<td class="text-left">
										<!-- nguoi phu trach -->
										<?php
										$nguoi_phu_trach='';
										$nguoi_tham_gia='';
										if (!empty($nguoi_tham_gia_da)) {
											foreach ($nguoi_tham_gia_da as $key => $value_tg) {
												if ($value_tg['id'] == $value['task_id']) {
													if ($value_tg['is_implement']==1) {
														echo $value_tg['username'].'<br>';
													}
													if ($value_tg['is_join']==1) {
														$nguoi_tham_gia .=$value_tg['username'].'<br>';
													}
												}
											}
										}
										?>
									</td>
									<td class="text-left"><?php echo $nguoi_tham_gia; ?></td>
									<td><?php 
									echo "Đã thanh lý";
									?></td>
								</tr>
								<?php
								$stt++;
							}
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
		<h4 style="font-weight:600;">II. Tình hình thực hiện các dự án, công việc trong <?php echo $time; ?>  </h4>
		<?php  
	}else{
		?>
		<h4 style="font-weight:600;">I. Tình hình thực hiện các dự án, công việc trong <?php echo $time; ?>  </h4>
		<?php 
	}
	?>
	<h5 style="font-weight:600;">1. Thông tin các dự án, công việc đang thực hiện </h5>
	<div class="table-responsive">
		<div class="table-responsive">
			<!-- <p style="font-style: italic;">(Gồm các dự án khu vực đó đã hoàn thành hoặc đang thực hiện trong <?php echo $time; ?>)</p> -->
			<table class="table tablesorter table-reports table-bordered display table-hover">
				<thead>
					<tr style="background: #bfbfbf;">
						<th>STT</th>
						<th>Tên dự án</th>
						<?php  
						if ($check_tp=='THANG') {
							echo '<th>Khách hàng</th>';
						}else{
							echo '<th>Hợp đồng liên quan</th>';
						}
						?>
						<th>Loại dịch vụ</th>
						<th>Người phụ trách</th>
						<th>Người tham gia</th>
						<th>Tình trạng dự án</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$st =1;
					if (!empty($task_all)) {
						foreach ($task_all as $key => $value) 
						{
							$date_finish = strtotime($value['date_finish']);
							$date_end = strtotime($value['date_end']);
							$date_start = strtotime($value['date_start']);
							$date_pheduyet =  strtotime($value['date_pheduyet']);
							$data['nguoi_tham_gia_da'] =$this->Kpi_Person->get_employee_join_tasks(array('task_id'=>$value['task_id']))->where('(tu.is_join=1 OR tu.is_implement=1)')->group_by('e.id')->get()->result_array();
							if ($value['pheduyet']!=1) {
								if (($date_start - strtotime($date_e))<=0) 
								{
									?>
									<tr>
										<td><?php echo $st; ?></td>
										<td><?php echo $value['name_task']; ?></td>
										<td>
											<?php  
											if ($check_tp=='THANG') {
												if (!empty($khach_hang_du_an)) {
													foreach ($khach_hang_du_an as $key => $valuekh) {
														if ($value['task_id']==$valuekh['task_id']) {
															echo $valuekh['customer_name'];
														}
													}
												}
											}else{
												echo $value['name_contract'];
											}
											?>
										</td>
										<td><?php echo $value['ten_dv']; ?></td>
										<td class="text-left">
											<ul style="padding: 0;">
												<?php
												$nguoi_tham_gia ='';
												if (!empty($data['nguoi_tham_gia_da'])) {
													foreach ($data['nguoi_tham_gia_da'] as $key => $valpt) {
														if ($valpt['is_implement']==1) {
															echo '<li>'.$valpt['username'].'</li>';
														}
														if ($valpt['is_join']==1) {
															$nguoi_tham_gia .= '<li>'.$valpt['username'].'</li>';
														}

													}
												}
												?>
											</ul>
										</td>
										<td class="text-left"><?php echo $nguoi_tham_gia; ?></td>
										<td>
											<?php 
											echo 'Đang thực hiện ('.round($value['progress'],2).'%)';
											?>
										</td>
									</tr>
									<?php
									$st++;
								}
							}elseif ($value['pheduyet']==1){
								if (($date_pheduyet - strtotime($date_e))<=0 && ($date_pheduyet - strtotime($date_s)) >=0) {
									?>
									<tr>
										<td><?php echo $st; ?></td>
										<td><?php echo $value['name_task']; ?></td>
										<td>
											<?php  
											if ($check_tp=='THANG') {
												if (!empty($khach_hang_du_an)) {
													foreach ($khach_hang_du_an as $key => $valuekh) {
														if ($value['task_id']==$valuekh['task_id']) {
															echo $valuekh['customer_name'];
														}
													}
												}
											}else{
												echo $value['name_contract'];
											}
											?>
										</td>
										<td><?php echo $value['ten_dv']; ?></td>
										<td class="text-left">
											<ul style="padding: 0;">
												<?php
												$nguoi_tham_gia ='';
												if (!empty($data['nguoi_tham_gia_da'])) {
													foreach ($data['nguoi_tham_gia_da'] as $key => $valpt) {
														if ($valpt['is_implement']==1) {
															echo '<li>'.$valpt['username'].'</li>';
														}
														if ($valpt['is_join']==1) {
															$nguoi_tham_gia .= '<li>'.$valpt['username'].'</li>';
														}

													}
												}
												?>
											</ul>
										</td>
										<td class="text-left"><?php echo $nguoi_tham_gia; ?></td>
										<td>
											<?php 
											echo 'Đã hoàn thành ('.round($value['progress'],2).'%)';
											?>
										</td>
									</tr>
									<?php
									$st++;
								}
								if (strtotime($date_e)-$date_start>=0 && $date_pheduyet-strtotime($date_e)>=0) {
									?>
									<tr>
										<td><?php echo $st; ?></td>
										<td><?php echo $value['name_task']; ?></td>
										<td>
											<?php  
											if ($check_tp=='THANG') {
												if (!empty($khach_hang_du_an)) {
													foreach ($khach_hang_du_an as $key => $valuekh) {
														if ($value['task_id']==$valuekh['task_id']) {
															echo $valuekh['customer_name'];
														}
													}
												}
											}else{
												echo $value['name_contract'];
											}
											?>
										</td>
										<td><?php echo $value['ten_dv']; ?></td>
										<td class="text-left">
											<ul style="padding: 0;">
												<?php
												$nguoi_tham_gia ='';
												if (!empty($data['nguoi_tham_gia_da'])) {
													foreach ($data['nguoi_tham_gia_da'] as $key => $valpt) {
														if ($valpt['is_implement']==1) {
															echo '<li>'.$valpt['username'].'</li>';
														}
														if ($valpt['is_join']==1) {
															$nguoi_tham_gia .= '<li>'.$valpt['username'].'</li>';
														}

													}
												}
												?>
											</ul>
										</td>
										<td class="text-left"><?php echo $nguoi_tham_gia; ?></td>
										<td>
											<?php 
											echo 'Đang thực hiện ('.round($value['progress'],2).'%)';
											?>
										</td>
									</tr>
									<?php
									$st++;
								}
							}
						}
					}
					if (empty($task_all)) {
						echo "<tr><td colspan='7' class='text-center' style='font-weight:600;'>Không có dữ liệu</td></tr>";
					}

					?>
				</tbody>
			</table>
		</div>
	</div>
	<h5 style="font-weight:600;">2.	Tình hình hoàn thành các dự án, công việc</h5>
	<div class="table-responsive">
		<div class="table-responsive">
			<!-- <p style="font-style: italic;">(Gồm các dự án khu vực đó đã hoàn thành hoặc đang thực hiện trong <?php echo $time; ?>)</p> -->
			<table class="table tablesorter table-reports table-bordered display table-hover">
				<thead>
					<tr style="background: #bfbfbf;">
						<td>STT</td>
						<td>Tên dự án</td>
						<td>Giai đoạn đang thực hiện</td>
						<td>Công việc đang thực hiện</td>
						<td>Tỷ lệ hoàn thành dự án</td>
						<td>Công việc thực hiện chậm</td>
					</tr>
				</thead>
				<tbody>
					<?php 
					$stt=1; 
					if (!empty($task_all))
					{
						foreach ($task_all as $key => $value)
						{
							$date_finish = strtotime($value['date_finish']);
							$date_end = strtotime($value['date_end']);
							$date_start = strtotime($value['date_start']);
							$date_pheduyet =  strtotime($value['date_pheduyet']);
							if ($value['pheduyet']!=1) {
								if (($date_start - strtotime($date_e))<=0) 
								{
									?>
									<tr>
										<td><?php echo $stt; ?></td>
										<td id="<?php echo $value['task_id']; ?>"><?php echo $value['name_task'] ?></td>
										<td>
											<ul style="padding: 0;">
												<?php  
												if (!empty($giai_doan_dang_thuc_hien)) {
													foreach ($giai_doan_dang_thuc_hien as $key => $value_gd) {
														if ($value_gd['parent'] == $value['task_id']) {
															echo '<li>'.$value_gd['name'].'</li>';
														}
													}
												}
												?>
											</ul>
										</td>
										<td>
											<ul style="padding: 0;">
												<?php  
												if (!empty($cong_viec_dang_thuc_hien)) {
													foreach ($cong_viec_dang_thuc_hien as $key => $value_cv) {
														if ($value_cv['project_id']==$value['task_id']) {
															echo $value_cv['name'].'<br>';
														}
													}
												}
												?>
											</ul>
										</td>
										<td class="text-right">
											<?php echo round($value['progress'],2).'(%)'; ?>
										</td>
										<td class="text-left">
											<?php  
											if (!empty($cong_viec_cham)) {
												foreach ($cong_viec_cham as $key => $value_delay) {
													if ($value_delay['project_id'] == $value['task_id']) {
														if ($value_delay['progress']==100) {
															if ((strtotime($value_delay['date_finish'])-strtotime($value_delay['date_end']))>0) {
																echo $value_delay['name'].' ('.round($value_delay['progress'],2) .'%)<br>';
															}
														}else{
															if ((strtotime($value_delay['date_end']) - strtotime(Date('Y-m-d')))<0) {
																echo $value_delay['name'].' ('.round($value_delay['progress'],2) .'%)<br>';
															}
														}
													}
												}
											}
											?>
										</td>
									</tr>
									<?php 
									$stt++; 
								}
							}elseif ($value['pheduyet']==1){
								if (($date_pheduyet - strtotime($date_e))<=0 && ($date_pheduyet - strtotime($date_s)) >=0) {
									?>
									<tr>
										<td><?php echo $stt; ?></td>
										<td id="<?php echo $value['task_id']; ?>"><?php echo $value['name_task'] ?></td>
										<td>
											<ul style="padding: 0;">
												<?php  
												if (!empty($giai_doan_dang_thuc_hien)) {
													foreach ($giai_doan_dang_thuc_hien as $key => $value_gd) {
														if ($value_gd['parent'] == $value['task_id']) {
															echo '<li>'.$value_gd['name'].'</li>';
														}
													}
												}
												?>
											</ul>
										</td>
										<td>
											<ul style="padding: 0;">
												<?php  
												if (!empty($cong_viec_dang_thuc_hien)) {
													foreach ($cong_viec_dang_thuc_hien as $key => $value_cv) {
														if ($value_cv['project_id']==$value['task_id']) {
															echo $value_cv['name'].'<br>';
														}
													}
												}
												?>
											</ul>
										</td>
										<td class="text-right">
											<?php echo round($value['progress'],2).'(%)'; ?>
										</td>
										<td class="text-left">
											<?php  
											if (!empty($cong_viec_cham)) {
												foreach ($cong_viec_cham as $key => $value_delay) {
													if ($value_delay['project_id'] == $value['task_id']) {
														if ($value_delay['progress']==100) {
															if ((strtotime($value_delay['date_finish'])-strtotime($value_delay['date_end']))>0) {
																echo $value_delay['name'].' ('.round($value_delay['progress'],2) .'%)<br>';
															}
														}else{
															if ((strtotime($value_delay['date_end']) - strtotime(Date('Y-m-d')))<0) {
																echo $value_delay['name'].' ('.round($value_delay['progress'],2) .'%)<br>';
															}
														}
													}
												}
											}
											?>
										</td>
									</tr>
									<?php
									$stt++;
								}
								if (strtotime($date_e)-$date_start>=0 && $date_pheduyet-strtotime($date_e)>=0){
									?>	
									<tr>
										<td><?php echo $stt; ?></td>
										<td id="<?php echo $value['task_id']; ?>"><?php echo $value['name_task'] ?></td>
										<td>
											<ul style="padding: 0;">
												<?php  
												if (!empty($giai_doan_dang_thuc_hien)) {
													foreach ($giai_doan_dang_thuc_hien as $key => $value_gd) {
														if ($value_gd['parent'] == $value['task_id']) {
															echo '<li>'.$value_gd['name'].'</li>';
														}
													}
												}
												?>
											</ul>
										</td>
										<td>
											<ul style="padding: 0;">
												<?php  
												if (!empty($cong_viec_dang_thuc_hien)) {
													foreach ($cong_viec_dang_thuc_hien as $key => $value_cv) {
														if ($value_cv['project_id']==$value['task_id']) {
															echo $value_cv['name'].'<br>';
														}
													}
												}
												?>
											</ul>
										</td>
										<td class="text-right">
											<?php echo round($value['progress'],2).'(%)'; ?>
										</td>
										<td class="text-left">
											<?php  
											if (!empty($cong_viec_cham)) {
												foreach ($cong_viec_cham as $key => $value_delay) {
													if ($value_delay['project_id'] == $value['task_id']) {
														if ($value_delay['progress']==100) {
															if ((strtotime($value_delay['date_finish'])-strtotime($value_delay['date_end']))>0) {
																echo $value_delay['name'].' ('.round($value_delay['progress'],2) .'%)<br>';
															}
														}else{
															if ((strtotime($value_delay['date_end']) - strtotime(Date('Y-m-d')))<0) {
																echo $value_delay['name'].' ('.round($value_delay['progress'],2) .'%)<br>';
															}
														}
													}
												}
											}
											?>
										</td>
									</tr>
									<?php
									$stt++;
								}
							}
						}
					}
					else{
						echo '<tr><td colspan="6" rowspan="" headers="" style="font-weight: 600;" class="text-center">Không có dữ liệu</td> </tr>';
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
	<?php 
	if ($check_tp=='THANG') {
		echo '<h4 style="font-weight:600;">III. Hoạt động khác</h4>';
		echo '<h4 style="font-weight:600;">IV. Phân tích, đánh giá</h4>';

	}else{
		echo '<h4 style="font-weight:600;">II. Hoạt động khác</h4>';
		echo '<h4 style="font-weight:600;">III. Phân tích, đánh giá</h4>';

	}

	?>
	<h5 style="font-weight:600;">1. Bảng doanh thu, lợi nhuận</h5>
	<div class="table-responsive">
		<table class="table tablesorter table-reports table-bordered display table-hover">
			<thead>
				<tr style="background: #bfbfbf;">
					<td width="15%">Chỉ tiêu tài chính</td>
					<td width="15%">Tỷ trọng (%)</td>
					<td width="15%">Tỷ trọng thành phần(%)</td>
					<td width="15%">Thực tế</td>
					<td width="15%">Kế hoạch</td>
					<td width="15%">Thực tế/Kế hoạch (%)</td>
					<td width="15%">Điểm</td>
				</tr>
			</thead>
			<tbody>

				<tr>
					<td style="font-weight: 600;">Doanh thu</td>
					<td class="text-right">20</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>Doanh thu bảo lãnh, đại lý phát hành trái phiếu</td>
					<td rowspan="4"></td>
					<td class="text-right" id="density_tp"></td>
					<td class="text-right" id="result_tp"></td>
					<td class="text-right" id="plan_tp"></td>
					<td class="text-right" id="result_plan_tp"></td>
					<td class="text-right" id="point_tp"></td>
				</tr>
				<tr>
					<td>Doanh thu bảo lãnh, đại lý phát hành cổ phiếu</td>
					<td class="text-right" id="density_cp"></td>
					<td class="text-right" id="result_cp"></td>
					<td class="text-right" id="plan_cp"></td>
					<td class="text-right" id="result_plan_cp"></td>
					<td class="text-right" id="point_cp"></td>
				</tr>
				<tr>
					<td>Doanh thu M&A</td>
					<td class="text-right" id="density_ma"></td>
					<td class="text-right" id="result_ma"></td>
					<td class="text-right" id="plan_ma"></td>
					<td class="text-right" id="result_plan_ma"></td>
					<td class="text-right" id="point_ma"></td>
				</tr>
				<tr>
					<td>Doanh thu tư vấn khác</td>
					<td class="text-right" id="density_tvk"></td>
					<td class="text-right" id="result_tvk"></td>
					<td class="text-right" id="plan_tvk"></td>
					<td class="text-right" id="result_plan_tvk"></td>
					<td class="text-right" id="point_tvk"></td>
				</tr>
				<tr>
					<td style="font-weight: 600;">Lợi nhuận</td>
					<td class="text-right">80</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td style="font-weight: 600;">Tổng</td>
					<td class="text-right" style="font-weight: 600;">100</td>
					<td class="text-right" id="sum_density" style="font-weight: 600;"></td>
					<td class="text-right" id="sum_result" style="font-weight: 600;"></td>
					<td class="text-right" id="sum_plan" style="font-weight: 600;"></td>
					<td class="text-right" id="sum_result_plan" style="font-weight: 600;"></td>
					<td class="text-right" id="sum_point" style="font-weight: 600;"></td>
				</tr>
			</tbody>
		</table>
	</div>

	<h5 style="font-weight:600;">2. Bảng đánh giá năng lực</h5>
	<?php 
	if ($check_tp=='THANG') {
		echo '<h4 style="font-weight:600;">V. Thông tin các vấn đề phát sinh</h4>';
	}else{
		echo '<h4 style="font-weight:600;">IV. Thông tin các vấn đề phát sinh</h4>';
	}

	?>

	<h5 style="font-weight:600;">1. Các vấn đề phát sinh đã xử lý</h5>
	<p>...</p>
	<h5 style="font-weight:600;">2. Các vấn đề phát sinh chưa xử lý</h5>
	<p>...</p>
	<h5 style="font-weight:600;">3. Các công việc quá hạn</h5>
	<div class="table-responsive">
		<div class="table-responsive">
			<table class="table tablesorter table-reports table-bordered display table-hover">
				<thead>
					<tr style="background: #bfbfbf;">
						<th width="5%">STT</th>
						<th width="35%">Tên dự án</th>
						<th width="60%">Công việc</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$arr_project1 = $arr_project2 = $arr_project3 = $arr_project= array();
					if (!empty($task_child_delay)) {
						foreach ($task_child_delay as $key => $value) {
							if ((strtotime($value['modified'])-strtotime($value['date_end']))>0)
							{
								$date = date_diff(date_create($value['modified']), date_create($value['date_end']));

							}elseif ((strtotime($value['date_finish']) - strtotime($value['date_end']))>0)
							{
								$date = date_diff(date_create($value['date_finish']), date_create($value['date_end']));
							}

							if ((int)$date->format('%m')>0) {
								$arr_project1[]= $value['project_id'];
							}elseif((int)$date->format('%y')>0){
								$arr_project2[]= $value['project_id'];
							}else{
								if ((int)$date->format('%d')>0) {
									$arr_project3[]= $value['project_id'];
								}
							}
						}
						$arr_project = array_unique(array_merge($arr_project1, $arr_project2,$arr_project3));

						$task_delay = $this->Contract->get_task($arr_project);
					}

					if (!empty($task_delay)) {
						$stt=1;
						foreach ($task_delay as $key => $val) {
							?>
							<tr>
								<td class="text-center"><?php echo $stt; ?></td>
								<td><?php echo $val['name']; ?></td>
								<td>
									<?php  
									if (!empty($task_child_delay)) {
										foreach ($task_child_delay as $key => $value) {
											if ($value['project_id']==$val['id']) {
												if ((strtotime($value['modified'])-strtotime($value['date_end']))>0)
												{
													$date = date_diff(date_create($value['modified']), date_create($value['date_end']));

												}elseif ((strtotime($value['date_finish']) - strtotime($value['date_end']))>0)
												{
													$date = date_diff(date_create($value['date_finish']), date_create($value['date_end']));
												}

												if ((int)$date->format('%m')>0) {
													if ((int)$date->format('%d')>0) {
														echo '- '.$value['name'].'<strong> ('.$date->format('%m tháng %d ngày').'</strong>)<br>';
													}else{
														echo '- '.$value['name'].'<strong> ('.$date->format('%m tháng').'</strong>)<br>';
													}
												}else{
													if ((int)$date->format('%d')>0) {
														echo '- '.$value['name'].'<strong> ('.$date->format('%d ngày').'</strong>)<br>';
													}
												}
											}
										} 
									}
									?>
								</td>
							</tr>
							<?php
							$stt++;
						}
					}

					else{
						?>
						<tr>
							<td colspan="4" class="text-center" style="font-weight: 600;">Không tồn tại dữ liệu!</td>
						</tr>
						<?php
					}  
					?>
				</tbody>
			</table>
		</div>
	</div>

	<?php
}elseif ($check=='BCQT') {
	if ($check_tp=='THANG') {
		$time = 'tháng '.$datetime['month'].'/'.$datetime['year'];
	}elseif($check_tp=='QUY'){
		$time = 'quý '.$datetime['month'].'/'.$datetime['year'];
	}else{
		$time = 'năm '.$datetime['year'];
	}
	if ($check_tp=='THANG') {
		$date= date_create($datetime['year'].'-'.$datetime['month']);
		$date_e = date_format($date,"Y-m-t");
		$date_s = date_format($date,"Y-m-d");
	}elseif ($check_tp=='QUY') {
		switch ($datetime['month']) {
			case 1:
			$date_ss = date_create($datetime['year'].'-01');
			$date = date_create($datetime['year'].'-03');
			break;
			case 2:
			$date_ss = date_create($datetime['year'].'-04');
			$date = date_create($datetime['year'].'-06');
			break;
			case 3:
			$date_ss = date_create($datetime['year'].'-07');
			$date = date_create($datetime['year'].'-09');
			break;
			case 4:
			$date_ss = date_create($datetime['year'].'-10');
			$date = date_create($datetime['year'].'-12');
			break;

		}
		$date_e = date_format($date,"Y-m-t");
		$date_s = date_format($date_ss,"Y-m-d");
	}else{
		$date = date_create($datetime['year'].'-12');
		$date_ss = date_create($datetime['year'].'-01');
		$date_e = date_format($date,"Y-m-t");
		$date_s = date_format($date_ss,"Y-m-d");
	}
	?>
	<!-- <p style="font-style: italic; color: red;">(Bạn có thể xem tối đa 50 bản ghi)</p> -->
	<h4 style="font-weight:600;">I.	Tình hình thực hiện các dự án, công việc trong <?php echo $time; ?>  </h4>


	<h5 style="font-weight:600;">1. Thông tin các dự án, công việc đang thực hiện </h5>

	<div class="table-responsive">
		<div class="table-responsive">
			<!-- <p style="font-style: italic;">(Gồm các dự án khu vực đó đã hoàn thành hoặc đang thực hiện trong <?php echo $time; ?>)</p> -->
			<table class="table tablesorter table-reports table-bordered display table-hover">
				<thead>
					<tr style="background: #bfbfbf;">
						<th width="5%">STT</th>
						<th width="20%">Tên dự án</th>
						<th width="20%">Hợp đồng liên quan</th>
						<th width="15%">Loại dịch vụ</th>
						<th width="15%">Người phụ trách</th>
						<th width="15%">Người tham gia</th>
						<th width="15%">Tình trạng dự án</th>
					</tr>
				</thead>
				<tbody>
					<?php  
					if (!empty($task_all)) 
					{
						$stt =1;
						foreach ($task_all as $key => $value) {
							$data['nguoi_tham_gia_da'] =$this->Kpi_Person->get_employee_join_tasks(array('task_id'=>$value['task_id']))->where('(tu.is_join=1 OR tu.is_implement=1)')->group_by('e.id')->get()->result_array();
							$date_finish = strtotime($value['date_finish']);
							$date_end = strtotime($value['date_end']);
							$date_start = strtotime($value['date_start']);
							$date_pheduyet =  strtotime($value['date_pheduyet']);
							if ($value['pheduyet']!=1) {
								if (($date_start - strtotime($date_e))<=0) {
									?>
									<tr>
										<td><?php echo $stt; ?></td>
										<td><?php echo $value['name_task']; ?></td>
										<td>
											<?php echo $value['name_contract']; ?>
										</td>
										<td><?php echo $value['ten_dv']; ?></td>
										<td class="text-left">
											<ul>
												<?php
												$nguoi_tham_gia ='';
												if (!empty($data['nguoi_tham_gia_da'])) {
													foreach ($data['nguoi_tham_gia_da'] as $key => $valpt) {
														if ($valpt['is_implement']==1) {
															echo '<li>'.$valpt['username'].'</li>';
														}else{
															$nguoi_tham_gia .= '<li>'.$valpt['username'].'</li>';
														}
													}
												}
												?>
											</ul>
										</td>
										<td class="text-left"><?php echo $nguoi_tham_gia; ?></td>
										<td>
											<?php 
											echo 'Đang thực hiện ('.round($value['progress'],2).'%)';
											?>
										</td>
									</tr>
									<?php
									$stt++;
								}
							}elseif ($value['pheduyet']==1) {
								if (($date_pheduyet - strtotime($date_e))<=0 && ($date_pheduyet - strtotime($date_s)) >=0) {
									?>
									<tr>
										<td><?php echo $stt; ?></td>
										<td><?php echo $value['name_task']; ?></td>
										<td>
											<?php echo $value['name_contract']; ?>
										</td>
										<td><?php echo $value['ten_dv']; ?></td>
										<td class="text-left">
											<ul>
												<?php
												$nguoi_tham_gia ='';
												if (!empty($data['nguoi_tham_gia_da'])) {
													foreach ($data['nguoi_tham_gia_da'] as $key => $valpt) {
														if ($valpt['is_implement']==1) {
															echo '<li>'.$valpt['username'].'</li>';
														}else{
															$nguoi_tham_gia .= '<li>'.$valpt['username'].'</li>';
														}
													}
												}
												?>
											</ul>
										</td>
										<td class="text-left"><?php echo $nguoi_tham_gia; ?></td>
										<td>
											<?php 
											echo 'Đã hoàn thành ('.round($value['progress'],2).'%)';
											?>
										</td>
									</tr>
									<?php
									$stt++;
								}
								if (strtotime($date_e)-$date_start>=0 && $date_pheduyet-strtotime($date_e)>=0) {
									?>
									<tr>
										<td><?php echo $stt; ?></td>
										<td><?php echo $value['name_task']; ?></td>
										<td>
											<?php echo $value['name_contract']; ?>
										</td>
										<td><?php echo $value['ten_dv']; ?></td>
										<td class="text-left">
											<ul>
												<?php
												$nguoi_tham_gia ='';
												if (!empty($data['nguoi_tham_gia_da'])) {
													foreach ($data['nguoi_tham_gia_da'] as $key => $valpt) {
														if ($valpt['is_implement']==1) {
															echo '<li>'.$valpt['username'].'</li>';
														}else{
															$nguoi_tham_gia .= '<li>'.$valpt['username'].'</li>';
														}
													}
												}
												?>
											</ul>
										</td>
										<td class="text-left"><?php echo $nguoi_tham_gia; ?></td>
										<td>
											<?php 
											echo 'Đang Thực hiện ('.round($value['progress'],2).'%)';
											?>
										</td>
									</tr>
									<?php
									$stt++;
								}
							}
						}
					}else{
						echo "<tr><td colspan='7' class='text-center' style='font-weight:600;'>Không có dữ liệu</td></tr>";
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
	<h5 style="font-weight:600;">2. Tình hình hoàn thành dự án, công việc </h5>
	<div class="table-responsive">
		<div class="table-responsive">
			<!-- <p style="font-style: italic;">(Gồm các dự án khu vực đó đã hoàn thành hoặc đang thực hiện trong <?php echo $time; ?>)</p> -->
			<table class="table tablesorter table-reports table-bordered display table-hover">
				<thead>
					<tr style="background: #bfbfbf;">
						<th>STT</th>
						<th>Tên dự án</th>
						<th>Giai đoạn đang thực hiện</th>
						<th>Công việc đang thực hiện</th>
						<th>Tỷ lệ hoàn thành dự án</th>
						<th>Công việc thực hiện chậm</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if (!empty($task_all))
					{

						$stt=1; 
						foreach ($task_all as $key => $value)
						{
							$date_finish = strtotime($value['date_finish']);
							$date_end = strtotime($value['date_end']);
							$date_start = strtotime($value['date_start']);
							$date_pheduyet =  strtotime($value['date_pheduyet']);
							if ($value['pheduyet']!=1) {
								if (($date_start - strtotime($date_e))<=0) {
									?>
									<tr>
										<td><?php echo $stt; ?></td>
										<td id="<?php echo $value['task_id']; ?>"><?php echo $value['name_task'] ?></td>
										<td>
											<ul style="padding: 0;">
												<?php  
												if (!empty($giai_doan_dang_thuc_hien)) {
													foreach ($giai_doan_dang_thuc_hien as $key => $value_gd) {
														if ($value_gd['parent'] == $value['task_id']) {
															echo '<li>'.$value_gd['name'].'</li>';
														}
													}
												}
												?>
											</ul>
										</td>
										<td>
											<ul style="padding: 0;">
												<?php  
												if (!empty($cong_viec_dang_thuc_hien)) {
													foreach ($cong_viec_dang_thuc_hien as $key => $value_cv) {
														if ($value_cv['project_id']==$value['task_id']) {
															echo $value_cv['name'].'<br>';
														}
													}
												}
												?>
											</ul>
										</td>
										<td class="text-right">
											<?php echo round($value['progress'],2).'%'; ?>
										</td>
										<td>
											<?php  
											if (!empty($task_child_delay)) {
												foreach ($task_child_delay as $key => $value_delay) {
													if ($value_delay['project_id'] == $value['task_id']) {
														if ($value_delay['progress']==100) {
															if ((strtotime($value_delay['date_finish'])-strtotime($value_delay['date_end']))>0) {
																echo $value_delay['name'].' ('.round($value_delay['progress'],2) .'%)<br>';
															}
														}else{
															if ((strtotime($value_delay['date_end'])) - (strtotime(Date('Y-m-d')))<0) {
																echo $value_delay['name'].' ('.round($value_delay['progress'],2) .'%)<br>';
															}
														}
													}
												}
											}
											?>
										</td>
									</tr>
									<?php 
									$stt++; 
								}
							}elseif($value['pheduyet']==1){
								if (($date_pheduyet - strtotime($date_e))<=0 && ($date_pheduyet - strtotime($date_s)) >=0) {
									?>
									<tr>
										<td><?php echo $stt; ?></td>
										<td id="<?php echo $value['task_id']; ?>"><?php echo $value['name_task'] ?></td>
										<td>
											<ul style="padding: 0;">
												<?php  
												if (!empty($giai_doan_dang_thuc_hien)) {
													foreach ($giai_doan_dang_thuc_hien as $key => $value_gd) {
														if ($value_gd['parent'] == $value['task_id']) {
															echo '<li>'.$value_gd['name'].'</li>';
														}
													}
												}
												?>
											</ul>
										</td>
										<td>
											<ul style="padding: 0;">
												<?php  
												if (!empty($cong_viec_dang_thuc_hien)) {
													foreach ($cong_viec_dang_thuc_hien as $key => $value_cv) {
														if ($value_cv['project_id']==$value['task_id']) {
															echo $value_cv['name'].'<br>';
														}
													}
												}
												?>
											</ul>
										</td>
										<td class="text-right">
											<?php echo round($value['progress'],2).'%'; ?>
										</td>
										<td>
											<?php  
											if (!empty($task_child_delay)) {
												foreach ($task_child_delay as $key => $value_delay) {
													if ($value_delay['project_id'] == $value['task_id']) {
														if ($value_delay['progress']==100) {
															if ((strtotime($value_delay['date_finish'])-strtotime($value_delay['date_end']))>0) {
																echo $value_delay['name'].' ('.round($value_delay['progress'],2) .'%)<br>';
															}
														}else{
															if ((strtotime($value_delay['date_end'])) - (strtotime(Date('Y-m-d')))<0) {
																echo $value_delay['name'].' ('.round($value_delay['progress'],2) .'%)<br>';
															}
														}
													}
												}
											}
											?>
										</td>
									</tr>
									<?php 
									$stt++; 
								}
								if (strtotime($date_e)-$date_start>=0 && $date_pheduyet-strtotime($date_e)>=0) {
									?>
									<tr>
										<td><?php echo $stt; ?></td>
										<td id="<?php echo $value['task_id']; ?>"><?php echo $value['name_task'] ?></td>
										<td>
											<ul style="padding: 0;">
												<?php  
												if (!empty($giai_doan_dang_thuc_hien)) {
													foreach ($giai_doan_dang_thuc_hien as $key => $value_gd) {
														if ($value_gd['parent'] == $value['task_id']) {
															echo '<li>'.$value_gd['name'].'</li>';
														}
													}
												}
												?>
											</ul>
										</td>
										<td>
											<ul style="padding: 0;">
												<?php  
												if (!empty($cong_viec_dang_thuc_hien)) {
													foreach ($cong_viec_dang_thuc_hien as $key => $value_cv) {
														if ($value_cv['project_id']==$value['task_id']) {
															echo $value_cv['name'].'<br>';
														}
													}
												}
												?>
											</ul>
										</td>
										<td class="text-right">
											<?php echo round($value['progress'],2).'%'; ?>
										</td>
										<td>
											<?php  
											if (!empty($task_child_delay)) {
												foreach ($task_child_delay as $key => $value_delay) {
													if ($value_delay['project_id'] == $value['task_id']) {
														if ($value_delay['progress']==100) {
															if ((strtotime($value_delay['date_finish'])-strtotime($value_delay['date_end']))>0) {
																echo $value_delay['name'].' ('.round($value_delay['progress'],2) .'%)<br>';
															}
														}else{
															if ((strtotime($value_delay['date_end'])) - (strtotime(Date('Y-m-d')))<0) {
																echo $value_delay['name'].' ('.round($value_delay['progress'],2) .'%)<br>';
															}
														}
													}
												}
											}
											?>
										</td>
									</tr>
									<?php 
									$stt++; 
								}
							}

						} ?>
						<?php 
					}else{
						echo '<tr><td colspan="6" rowspan="" headers="" style="font-weight: 600;" class="text-center">Không có dữ liệu</td> </tr>';
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
	<h4 style="font-weight:600;">II. Các hoạt động khác</h4>
	<p>...</p>
	<h4 style="font-weight:600;">III. Phân tích, đánh giá nhân viên</h4>
	<h5 style="font-weight:600;">1. Bảng doanh thu, lợi nhuận</h5>
	<div class="table-responsive">
		<div class="table-responsive">
			<table class="table tablesorter table-reports table-bordered display table-hover">
				<thead>
					<tr style="background: #bfbfbf;">
						<td width="15%">Chỉ tiêu tài chính</td>
						<td width="15%">Tỷ trọng (%)</td>
						<td width="15%">Tỷ trọng thành phần(%)</td>
						<td width="15%">Thực tế</td>
						<td width="15%">Kế hoạch</td>
						<td width="15%">Thực tế/Kế hoạch (%)</td>
						<td width="15%">Điểm</td>
					</tr>
				</thead>
				<tbody>

					<tr>
						<td style="font-weight: 600;">Doanh thu</td>
						<td class="text-right">20</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td>Doanh thu bảo lãnh, đại lý phát hành trái phiếu</td>
						<td rowspan="4"></td>
						<td class="text-right" id="density_tp"></td>
						<td class="text-right" id="result_tp"></td>
						<td class="text-right" id="plan_tp"></td>
						<td class="text-right" id="result_plan_tp"></td>
						<td class="text-right" id="point_tp"></td>
					</tr>
					<tr>
						<td>Doanh thu bảo lãnh, đại lý phát hành cổ phiếu</td>
						<td class="text-right" id="density_cp"></td>
						<td class="text-right" id="result_cp"></td>
						<td class="text-right" id="plan_cp"></td>
						<td class="text-right" id="result_plan_cp"></td>
						<td class="text-right" id="point_cp"></td>
					</tr>
					<tr>
						<td>Doanh thu M&A</td>
						<td class="text-right" id="density_ma"></td>
						<td class="text-right" id="result_ma"></td>
						<td class="text-right" id="plan_ma"></td>
						<td class="text-right" id="result_plan_ma"></td>
						<td class="text-right" id="point_ma"></td>
					</tr>
					<tr>
						<td>Doanh thu tư vấn khác</td>
						<td class="text-right" id="density_tvk"></td>
						<td class="text-right" id="result_tvk"></td>
						<td class="text-right" id="plan_tvk"></td>
						<td class="text-right" id="result_plan_tvk"></td>
						<td class="text-right" id="point_tvk"></td>
					</tr>
					<tr>
						<td style="font-weight: 600;">Lợi nhuận</td>
						<td class="text-right">80</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td style="font-weight: 600;">Tổng</td>
						<td class="text-right" style="font-weight: 600;">100</td>
						<td class="text-right" id="sum_density" style="font-weight: 600;"></td>
						<td class="text-right" id="sum_result" style="font-weight: 600;"></td>
						<td class="text-right" id="sum_plan" style="font-weight: 600;"></td>
						<td class="text-right" id="sum_result_plan" style="font-weight: 600;"></td>
						<td class="text-right" id="sum_point" style="font-weight: 600;"></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<h4 style="font-weight:600;">IV. Thông tin các vấn đề phát sinh</h4>
	<h5 style="font-weight:600;">1. Các vấn đề phát sinh đã xử lý</h5>
	<p>...</p>
	<h5 style="font-weight:600;">2. Các vấn đề phát sinh chưa xử lý</h5>
	<p>...</p>
	<div class="container">
		<h5 class="text-right" style="font-weight: 600;">TRƯỞNG PHÒNG TVTCDN – <?php echo mb_strtoupper($name_location); ?></h5>
	</div>
	
	<?php
}elseif ($check=='DTNB') {
	?>

	<div class="col-md-6">
		<h4 class="text-center" style="font-weight: 600;">CÔNG TY CHỨNG KHOÁN NHTMCPNTVN </h4>
		<h4 class="text-center" style="font-weight: 600;">PHÒNG TVTCDN- <?php echo $location->name; ?></h4>
		<hr>
		<p class="text-center" style="font-style: italic;">V/v: Phân chia doanh thu nội bộ</p>
	</div>
	<div class="col-md-6">
		<h4 class="text-center" style="font-weight: 600;">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</h4>
		<p style="font-weight: 600;" class="text-center">Độc lập - Tự do - Hạnh phúc</p>
		<hr>
		<p class="text-center"><?php echo $location->address.', '.$datetime; ?></p>
	</div>
	<div class="col-md-12" style="margin-top: 20px;">
		<h4 class="text-center" style="font-weight: 600;">BIÊN BẢN PHÂN CHIA DOANH THU NỘI BỘ</h4>
		<p class="text-center" style="font-weight: 600;">Hợp đồng: <?php echo $contract_item; ?> số: <?php echo $contract_code; ?></p>
		<div class="kg" style="margin-top: 15px;">
			<p class="text-center" style="font-weight: 600;">Kính gửi: Trưởng phòng TVTCDN - <?php echo $location->name; ?></p>
		</div>
		<p>Căn cứ vào sự đóng góp của từng người vào việc thực hiện hợp đồng số <?php echo $contract_code; ?>, chúng tôi đã thống nhất phân chia doanh thu như sau: </p>
		<div class="table-responsive">
			<div class="table-responsive">
				<table class="table tablesorter table-reports table-bordered display table-hover">
					<thead>
						<tr style="background: #bfbfbf;">
							<td width="10%">STT</td>
							<td width="45%">Họ và tên</td>
							<td width="45%">Tỷ lệ doanh thu</td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td colspan="2" style="font-weight: 600;">Người phụ trách</td>
							<td></td>
						</tr>
						<?php 
						$sum1 =0; 
						if (!empty($nguoi_tham_gia_da)) {
							$stt=1;
							foreach ($nguoi_tham_gia_da as $key => $value) {
								if ($value['is_implement']==1) {
									?>
									<tr>
										<td class="text-center"><?php echo $stt; ?></td>
										<td class="text-left"><?php 
										if ($value['first_name']==null) {
											echo $value['last_name'];
										}echo $value['first_name'];
										?></td>
										<td class="text-right">
											<?php

											$kpi_implement =0;
											$employee_id = $value['employee_id'];
											if (!empty($kpi)) {
												foreach ($kpi as $key => $value_kpi) {
													if ($value_kpi['task_id'] == $task_id) {
														$arr_ratio = json_decode($value_kpi['ratio'],true);
														$arr_employeeID = json_decode($value_kpi['employee_id'],true);
														for($i=0;$i<count($arr_employeeID);$i++){
															if ($arr_employeeID[$i] == $employee_id) {
																$kpi_implement =$arr_ratio[$i];
																$sum1 +=$kpi_implement;
															}

														}
													}
												}

											}
											echo $kpi_implement;
											?>
										</td>
									</tr>
									<?php
									$stt++;
								}

							}
						}
						?>
						<tr>
							<td colspan="2" style="font-weight: 600;">Người tham gia</td>
							<td></td>
						</tr>
						<?php 
						$sum2 =0;
						if (!empty($nguoi_tham_gia_da)) {
							$dem=1; 
							foreach ($nguoi_tham_gia_da as $key => $value) {
								if ($value['is_join']==1) {
									$employee_id = $value['employee_id'];
									?>
									<tr>
										<td class="text-center"><?php echo $dem; ?></td>
										<td class="text-left"><?php if ($value['first_name']==null) {
											echo $value['last_name'];
										}echo $value['first_name']; ?></td>
										<td class="text-right">
											<?php
											$sum_kpi_join= 0;
											$kpi_join =0;

											if (!empty($kpi)) {
												foreach ($kpi as $key => $value_kpi) {
													if ($value_kpi['task_id'] == $task_id) {
														$arr_ratio = json_decode($value_kpi['ratio'],true);
														// $sum2 =array_sum($arr_ratio);
														$arr_employeeID = json_decode($value_kpi['employee_id'],true);
														for($i=0;$i<count($arr_employeeID);$i++){
															if ($arr_employeeID[$i] == $employee_id) {
																$kpi_join = $arr_ratio[$i];
																$sum2+= $kpi_join;
															}

														}
													}
												}
												echo $kpi_join;
											}

											?>
										</td>
									</tr>
									<?php
									$dem++;
								}

							}
						}
						?>
						<tr>
							<td colspan="2" class="text-center" style="font-weight: 600;">Tổng cộng</td>
							<td class="text-right" style="font-weight: 600;"><?php echo $sum1+$sum2; ?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<p>Kính trình Trưởng phòng TVTCDN - <?php echo $location->address; ?> phê duyệt, làm căn cứ để triển khai đánh giá hiệu quả kinh doanh của nhân viên theo đúng quy định.</p>
	</div>
	<div class="row">
		<div class="col-md-12">
			<?php  
			foreach ($nguoi_tham_gia_da as $key => $value) {
				if ($value['is_implement']==1) {
					?>
					<div class="col-md-3">
						<p style="font-weight: 600;">Nguời phụ trách</p>
						<p>
							<?php 
							if ($value['first_name']==null) {
								echo $value['last_name'];
							}echo $value['first_name'];
							?>
						</p>
					</div>
				<?php  }
			}
			?>
			<?php  
			foreach ($nguoi_tham_gia_da as $key => $value) {
				if ($value['is_join']==1) {
					?>
					<div class="col-md-3">
						<p style="font-weight: 600;">Nguời tham gia</p>
						<p>
							<?php 
							if ($value['first_name']==null) {
								echo $value['last_name'];
							}echo $value['first_name'];
							?>
						</p>
					</div>
				<?php  }
			}
			?>
		</div>
	</div>
	<hr>
	<p style="font-weight: 600;">Ý kiến Trưởng phòng TVTCDN-<?php echo $location->name; ?>:</p>
	<?php
}elseif ($check=='DBDT') {
	?>
	<h4 class="text-center" style="font-weight: 600;"><?php echo $title; ?></h4>
	<h4 class="text-center" style="font-weight: 600;"> Khu vực: <?php echo $name_location; ?></h4>
	<div class="col-md-12">
		<div class="table-responsive">
			<table class="table tablesorter table-reports table-bordered display table-hover">
				<thead>
					<tr style="background: #bfbfbf;">
						<th width="5%">STT</th>
						<th width="20%">Tên dự án</th>
						<th width="20%">Tên dịch vụ</th>
						<th width="15%">Tên hợp đồng</th>
						<th width="15%">Ngày kết thúc dự kiến</th>
						<th width="15%">Tổng giá trị hợp đồng ( trừ VAT)</th>
					</tr>
				</thead>
				<tbody>
					<?php  
					if (!empty($da_dang_thuc_hien)) {
						$stt=1;
						$tong=0;
						foreach ($da_dang_thuc_hien as $key => $value) {
							?>
							<tr>
								<td><?php echo $stt; ?></td>
								<td><?php echo $value['name_task']; ?></td>
								<td><?php echo $value['item_name']; ?></td>
								<td><?php echo $value['name_contract']; ?></td>
								<td class="text-right"><?php echo date('d-m-Y',strtotime($value['date_end'])); ?></td>
								<td class="text-right"><?php echo number_format($value['co_vat']); ?></td>
							</tr>
							<?php
							$tong+=$value['co_vat'];
							$stt++;
						}
					}
					?>
					<tr>
						<td colspan="5" class="text-left" style="font-weight: 600;">Tổng doanh thu dự kiến</td>
						<td class="text-right" style="font-weight: 600;"><?php echo number_format($tong); ?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<?php
}
?>
<script type="text/javascript">

</script>

