<?php
$this->load->model('reports/Specific_customer');
		$model = $this->Specific_customer;
		?>
<div class="modal-dialog mogal-lg">
	<div class="modal-content customer-recent-sales">
		<div class="modal-header" style="min-height:50px;">

		<h4 style="width:89%; float:left;"><?php echo lang('lich_su_gui_sms'); ?></h4>
		<button type="button" class="bootbox-close-button close" data-dismiss="modal" aria-hidden="true" style="margin-top: -10px;">×</button>
		</div>
		<div class="modal-body ">
		<table>
			<tbody>
			<?php foreach ($data as $key => $row) { ?>
				<tr>
					<td class="lbmodal"><?php echo lang('nguoi_gui') ?>:</td>
					<td>
					<?php
					$models = $model->getHistorySendEmailById($row->employee_id);
					foreach ($models as $v) {
						echo '<strong>'. $v->first_name . ' ' . $v->last_name.'</strong>';
					}
					?>
						
					</td>
				</tr>
				<tr>
					<td class="lbmodal"><?php echo lang('ngay_gui') ?>:</td>
					<td>
						<?php echo date(get_date_format().' '.get_time_format(), strtotime($row->time)); ?>
					</td>
				</tr>
				<tr>
					<td class="lbmodal"><?php echo lang('ho_ten'); ?>:</td>
					<td><strong><?php echo $row->first_name . ' ' . $row->last_name; ?></strong></td>
				</tr>
				<tr>
					<td class="lbmodal"><?php echo lang('E-Mail');?>: </td>
					<td><?php echo $row->email; ?></td>
				</tr>
				<tr>
					<td class="lbmodal"><?php echo lang('Phone') ?>:</td>
					<td><?php echo $row->phone_number;?></td>
				</tr>
				
				<tr>
					<td class="lbmodal"><?php echo lang('tieu_de') ?>:</td>
					<td><strong><?php echo $row->title;?></strong></td>
				</tr>
				<tr>
					<td class="lbmodal topnd" style="position: absolute;"><?php echo lang('noi_dung');?>: </td>
					<td><?php echo $row->content ?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
		</div>
	</div>
</div>
<style type="text/css">
	.lbmodal{
		width: 75px;
	}
</style>