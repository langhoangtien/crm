<?php if(count($payments) > 0) { ?>
	<ul class="list-group payments">
		<?php foreach($payments as $payment_id=>$payment) { 
			if($payment['payment_type']!=lang('common_refund_from_supplier'))
			{
		?>
		
			<li class="list-group-item">
				<span class="key">
					<a class="delete-payment remove" data-id="<?php echo $payment_id; ?>"><i class="icon ion-android-cancel"></i></a>
					<?php echo $payment['payment_type']; ?> 
				</span>
				<span class="value">
					<?php echo  to_currency($payment['amount']); ?>
				</span>
			</li>
		<?php }
		}		?>
	</ul>
<?php } ?>

<script type="text/javascript" language="javascript">
$(document).ready(function()
{
	$('.payments a.remove').click(function(e){
		e.preventDefault();
		var _data = {};
		_data['id'] = $(this).data('id');
		coreAjax.call(
			'<?php echo site_url("receivings/delete_payment");?>',
			_data,
			function(response)
			{
				$('#amount_tendered').val(response.amount_tendered);
				$('#list_payments').html(response.html_payments);
				$('#list-payments-option').html(response.html_payments_option);

				if(response.amount_tendered <=0 ) {
					$('#finish-recv').show();
				} else {
					$('#finish-recv').hide();
				}
			}
		);
	});
})
</script>