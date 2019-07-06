	<?php if(isset($band) && $band){ ?>
           <h3 style="margin-top: 0;"><?php echo lang('common_customers_'.$customers_table) ?></h3>
        <?php } else { ?>
             <h3 style="margin-top: 0;"><?php echo lang('common_customers_'.$customers_table) ?></h3>
    <?php } ?>

<?php foreach ($danh_muc_con as $value) { ?>

     <div class="list-group">
        <a href="javascript:void(0)" class="list-group-item 
        <?php if(isset($band) && $band){echo 
                'mo_tab_moi';
            } ?>" 
        data-customers_table='<?php echo isset($customers_table) ? $customers_table:''; ?>' 
        data-category_id="<?php echo isset($value['id']) ? $value['id']:'' ; ?>" 
        data-parrent_id="<?php echo isset($value['parrent_id']) ? $value['parrent_id']:''; ?>" 
        data-name='<?php echo isset($value['name']) ? $value['name']:''; ?>'>
        <?php echo isset($value['name']) ? $value['name']:''; ?> 
        <i class="ion-trash-a pull-right larger" style="font-size: 14px;" id="delete_danh_muc_khach_hang" onclick="xoa_danh_muc(<?php echo isset($value['id']) ? $value['id']:''; ?>,'<?php echo isset($customers_table) ? $customers_table:''; ?>','<?php echo isset($value['parrent_id']) ? $value['parrent_id']:''; ?>')">  </i>
		
       
        <!-- chặn hiển thị thêm mới danh mục -->
        <?php if(isset($band) && $band){ ?>
			<i class="pull-right" id="them_moi_danh_muc_con">Thêm &nbsp;&nbsp;</i>
    	<?php } ?>    
         <i class="pull-right" id="edit_danh_muc_khach_hang">Sửa&nbsp;&nbsp;</i>
        </a>
    </div>
<?php } ?>
        <!-- chặn hiển thị thêm mới danh mục -->
	<?php if(isset($band) && $band){ ?>
			 <a href="javascript:void(0);" class="them_moi_danh_muc btn btn-success btn-lg btn-block" data-customers_table="<?php echo isset($customers_table) ? $customers_table:''; ?>">Thêm mới</a>
	<?php } ?>
   


