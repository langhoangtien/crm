<?php if($mode!="transfer") { ?>
	<div class="add-payment">
		<div class="side-heading"><?php echo lang('common_add_payment'); ?></div>
							<?php

							if(isset($payment_options[lang('common_store_account')]) && $mode == 'store_account_payment') {
									unset($payment_options[lang('common_store_account')]);
							}
							?>
		<?php if (!$selected_payment) { $selected_payment = $this->config->item('default_payment_type') ? $this->config->item('default_payment_type') : lang('common_cash'); } ?>
		
			<?php foreach ($payment_options as $key => $value) {
				$active_payment =  ($selected_payment == $value) ? "active" : "";
				$hide_payment =    ($value == lang('common_refund_from_supplier') ) ? "hide" : "";
			?>
				<a tabindex="-1" href="#" class="btn btn-pay select-payment <?php echo $active_payment.' '.$hide_payment; ?>" data-payment="<?php echo H($value); ?>">
				<?php echo $value; ?>
				</a>
			<?php 
			} 
			?>
			
	</div>
<?php }	?>
<script>
			var show_finish_recv = true;
			$('#add_payment_recv_button').removeClass('hide');
			$('.select-payment').each(function(){
				if($(this).data('payment') == <?php echo json_encode(lang('common_debt_supplier'))?> && $(this).hasClass('active'))
				{
					show_finish_recv = false;
				}
				if($(this).data('payment') == <?php echo json_encode(lang('common_refund_from_supplier'))?>)
				{
					$(this).addClass('active');
				}
				
			});

			
		$('.select-payment').on('click',function(e){
			e.preventDefault();
			var selectedPayment = $(this);
			var payment = selectedPayment.data('payment');
			$(this).siblings().removeClass('active');
			if(<?php echo json_encode($this->receiving_lib->get_payments_totals() - $this->receiving_lib->get_total())?> > 0)
			{
				if($(this).data('payment') == <?php echo json_encode(lang('common_debt_supplier')) ?>)
				{
					if(selectedPayment.hasClass('active'))
					{
						payment = <?php echo json_encode(lang('common_refund_from_supplier')) ?>;
						$('#add_payment_recv_button').addClass('hide');
						$('#finish-recv').show();
						$(this).siblings().addClass('active');
						
					}
					else
					{
						$('#finish-recv').hide();
						$('#add_payment_recv_button').removeClass('hide');
						
					}
				}
			}
				if(selectedPayment.data('payment') == <?php echo json_encode(lang('common_debt_supplier')) ?>)
					{
							selectedPayment.toggleClass('active');
					}

			$.post('<?php echo site_url("receivings/set_selected_payment");?>', {payment: payment}, function(response)
			{
				if (response == 1) {
					$('#payment_type').val(selectedPayment.data('payment'));
					if(selectedPayment.data('payment') == <?php echo json_encode(lang('common_debt_supplier')) ?>)
					{
							//selectedPayment.toggleClass('active');
					}
					else
					{
						$('.select-payment').removeClass('active');
					  selectedPayment.addClass('active');
					}

					$("#amount_tendered").focus();
					$("#amount_tendered").select();
				} else {
					show_feedback('error', '', 'Thiếu thông tin nhà cung cấp!');
				}
			});
		});
					$(document).ready(function(){
				
				if(<?php echo json_encode($this->receiving_lib->get_payments_totals() - $this->receiving_lib->get_total())?> > 0)
				{
						if(show_finish_recv)
						{
							$('#finish-recv').show();
							$('#add_payment_recv_button').addClass('hide');
						}
				}
			});
</script>