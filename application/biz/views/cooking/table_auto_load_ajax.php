
<div class="row" id="block_table_waiting">
		<?php foreach ($sale_tables_waiting as $sale_table) {
			$color_class = '';
			if ($sale_table['waiting_time'] > 30) {
				$color_class = 'more_30';
			} elseif ($sale_table['waiting_time'] > 15) {
				$color_class = 'more_15';
			}
		?>
		
	<div class="col-md-2 item" data-sale_id="<?php echo $sale_table['sale_id']; ?>">
		<div class="name <?php echo $color_class; ?>"><?php echo $sale_table['table_name']; ?> - <?php echo $sale_table['room_name']; ?> - <?php echo $sale_table['place_name']; ?></div>
		<div class="detail">
			<span class="ti-timer"></span> <?php echo $sale_table['waiting_time_str']; ?>
		</div>
	</div>
	<?php } ?>
</div>