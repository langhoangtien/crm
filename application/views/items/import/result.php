<div id="data-import" data-action="<?php echo site_url('items/action_import_data'); ?>">
    <div class="row">
        <?php if (!empty($fields)) :?>
            <div class="col-md-4">
                <label><i class="icon ti-settings"></i> <?php echo lang('common_select_field_to_check_duplicate'); ?></label>
                <select class="form-control" name="check_duplicate_field" id="check-duplicate-field">
                    <option value="0"><?php echo lang('common_select_field_to_check_duplicate'); ?></option>
                    <optgroup label="<?php echo lang('common_basic_attributes'); ?>">
                        <?php foreach ($fields as $field) :?>
                            <option value="basic:<?php echo $field; ?>"><?php echo lang('common_' . $field); ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                    <optgroup class="attributes-by-set" id="attributes-by-set" label="<?php echo lang('common_attributes_by_set'); ?>"></optgroup>
                </select>
            </div>
        <?php endif; ?>
        <div class="col-md-4">
            <label><i class="icon ti-settings"></i> Cập nhật lại dữ liệu trùng lặp</label>
            <select class="form-control" name="override_db" id="override_db">
                <option value="0">Không</option>
                <option value="1">Có</option>
            </select>
        </div>
        <div class="col-md-4">
            <button style="margin-top: 30px;" type="button" class="btn btn-primary" onclick="action_import_data('data-import');">Thực hiện</button>
        </div>
    </div>
    
    <div  class="item-scroll">
         <?php $this->load->view('items/import/result/rows'); ?>
    </div>
</div>
<style>
    #data-import tr[data-import-id].error td.selected {
        background: red;
    }
    
    .item-scroll {
        overflow-x: scroll;
    }
</style>
<?php $this->load->view('import/client_script'); ?>