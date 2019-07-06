<?php if (!empty($attribute_sets) && !empty($entity_info)) :?>
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title">
            <?php echo lang("common_select_attribute_set"); ?>
        </h3>
    </div>
    <div class="panel-body">
        <div class="form-group">
            <label for="attribute_set_id" class="col-sm-3 col-md-3 col-lg-2 control-label"><?php echo lang('common_select_attribute_set'); ?></label>
            <div class="col-sm-9 col-md-9 col-lg-10">
                <span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
                <select name="attribute_set_id" id="attribute_set_id" class="form-control">
                    <option value="0"><?php echo lang('common_select_attribute_set'); ?></option>
                    <?php foreach ($attribute_sets as $attribute_set) :?>
                    <option <?php 
					if (isset($_SESSION['cartItemsAttributeSet'][$line])) {
						if ($_SESSION['cartItemsAttributeSet'][$line] == $attribute_set->id) echo 'selected';
					} else {
						if ($entity_info->attribute_set_id == $attribute_set->id) echo 'selected';
					} 
					?> value="<?php echo $attribute_set->id; ?>"><?php echo $attribute_set->name; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

        </div>
    </div>

</div>
<div class="spinner" id="ajax-loader-attribute" style = "display:none;">
        <div class="rect1" style = "height:100px;"></div>
        <div class="rect2" style = "height:100px;"></div>
        <div class="rect3" style = "height:100px;"></div>
</div>
<?php endif; ?>

<script>

    $(document).ready(function() {
		   		var change_attribute = '<?php 
					if (isset($_SESSION['cartItemsAttributeSet'][$line])) {
					   echo $_SESSION['cartItemsAttributeSet'][$line];
					} else {
						echo $entity_info->attribute_set_id;
					} 
				?>';
			if (change_attribute != 0) 
			{
		
				var line = <?php echo isset($line)? $line: '""';?>;
				$.ajax({
				 type:'POST',
				 url: 'items/view/<?php echo $entity_info->item_id;?>/2',
				 data:{change_attribute:change_attribute, line:line, action: 'first_load'},
				 success:function(data)
				 {
					$('#attribute_sets').html(JSON.parse(data)['listAttribute']);   
				 }
			 })
			}

	});
	var timer1;
	$('body').on('change','#attribute_set_id',function(){
			var line = <?php echo isset($line)? $line: '""';?>;
			if(timer1){
				window.clearTimeout(timer1);
			}
			timer1 = window.setTimeout(function(){
			 $.ajax({
				 type:'POST',
				 url: 'items/view/<?php echo $entity_info->item_id;?>/2',
				 data:{change_attribute:$('#attribute_set_id').val(), line:line, action: 'change_selected_attribute_set'},
				 success:function(data)
				 {
					$('#attribute_sets').html(JSON.parse(data)['listAttribute']); 

				 }
			})}, 400);
		});
	(function($){
		$.fn.focusTextToEnd = function(){
			this.focus();
			var $thisVal = this.val();
			this.val('').val($thisVal);
			return this;
		}
	}(jQuery));
    function isNumber(n) {
      return !isNaN(parseFloat(n)) && isFinite(n);
    }
    
	var timer;
	$('body').on('keyup', '.attribute-input', function(){
		
		var dataPost = {};
		var dataAttrGroupShowPost = {};
		var id = $(this).attr('id');
		$('.attribute-input').each(function(){
			dataPost[$(this).attr('id')] = $(this).val(); 
		});
		$('.attribute-group').each(function(){
			dataAttrGroupShowPost[$(this).attr('data-id')] = $(this).css('display'); 
		});
		
		var line = <?php echo isset($line)? $line: '""';?>;
		if(this.value.length >=0)
		{
			if(timer){
				window.clearTimeout(timer);
			}
			timer = window.setTimeout(function(){
				$.ajax({
					url: 'items/view/<?php echo $entity_info->item_id;?>/2',
					type: 'POST',
                    beforeSend: function(){
                        // Handle the beforeSend event
                        $('#ajax-loader-attribute').show();
                    }, 
					data:{	dataAttributeKeyUp: dataPost, 
							change_attribute:$('#attribute_set_id').val(),
							dataAttrGroupShowPost: dataAttrGroupShowPost,
							line: line},
					success:function (response){
                        var data = JSON.parse(response);
						$('#attribute_sets').html(data['listAttribute']); 
						$('#'+id).focusTextToEnd();
                        $.ajax({
                            type:'POST',
                            url: 'sales/updateAttributeItem',
                            data:{  
                                ajax_reload:true,
                                },
                            success:function(response1)
                            {
                                $('#register_container').html(JSON.parse(response1)); 
                                $('#ajax-loader-attribute').hide();   
                            }
                        });
                                       
					},
				});
			},1000);
	}});

	$('body').on('change', '.attribute-select', function(){
		
		var dataPost = {};
		var dataAttrGroupShowPost = {};
		var id = $(this).attr('id');
		$('.attribute-input').each(function(){
			dataPost[$(this).attr('id')] = $(this).val(); 
		});
		$('.attribute-group').each(function(){
			dataAttrGroupShowPost[$(this).attr('data-id')] = $(this).css('display'); 
		});
		
		var line = <?php echo isset($line)? $line: '""';?>;
		if(this.value.length >=0)
		{
			if(timer){
				window.clearTimeout(timer);
			}
			timer = window.setTimeout(function(){
				$.ajax({
					url: 'items/view/<?php echo $entity_info->item_id;?>/2',
					type: 'POST',
                    beforeSend: function(){
                        // Handle the beforeSend event
                        $('#ajax-loader-attribute').show();
                    }, 
					data:{	dataAttributeKeyUp: dataPost, 
							change_attribute:$('#attribute_set_id').val(),
							dataAttrGroupShowPost: dataAttrGroupShowPost,
							line: line},
					success:function (response){
                        var data = JSON.parse(response);
						$('#attribute_sets').html(data['listAttribute']); 
						$('#'+id).focusTextToEnd();
						
                        
                        $.ajax({
                            type:'POST',
                            url: 'sales/updateAttributeItem',
                            data:{  ajax_reload:true,
                                },
                            success:function(response1)
                            {
                                $('#register_container').html(JSON.parse(response1)); 
                                $('#ajax-loader-attribute').hide();   
                            }
                        }); 
                                       
					},
				});
			},700);
	}});
</script>