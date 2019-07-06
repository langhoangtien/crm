<?php
    if(!empty($items)) {
?>
<?php foreach($items as $item){?>
          <tr style="cursor: pointer;">
						<td class="cb"><?php echo $item['contractCode'];?></td>
						<td class="cb"><?php echo $item['name'];?></td>
						<td class="cb"><?php echo $item['last_name'].' '.$item['first_name'];?></td>
						<td class="cb center"><?php echo $item['date_signing'];?></td>
						<td class="cb center"><?php echo $item['type'];?></td>
						<td class="cb center"><?php echo $item['status_'];?></td>
						<td class="center"><a href="javascript:;" onclick="download_contract_file(<?php echo $item['id']?>);">Tải File</a></td>
						<td class="center"><?php echo $item['email']?></td>
						<td class="center" style="padding: 4px;"><a href="javascript:;" onclick="edit_contract(<?php echo $item['id'];?>);">Sửa</a></td>
					</tr>

<?php
        }
    }else {
?>
        <tr style="cursor: pointer;"><td colspan="12"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>
<?php
    }
?>