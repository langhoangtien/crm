<div id="data-import" data-action="<?php echo site_url('customers/action_import_data'); ?>">
    <div class="row">
        <?php if (!empty($attribute_sets)) :?>
        <div class="col-md-4">
            <label><i class="icon ti-settings"></i> <?php echo lang('common_select_attribute_set'); ?></label>
            <select data-action="<?php echo site_url('attribute_sets/action_get_attributes'); ?>" onchange="action_load_attributes_by_set($(this), '.attributes-by-set')" class="form-control" name="attribute_set_id" id="attribute_set">
                <option value="0"><?php echo lang('common_select_attribute_set'); ?></option>
                <?php foreach ($attribute_sets as $attribute_set) :?>
                <option value="<?php echo $attribute_set->id; ?>"><?php echo $attribute_set->name; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>
        <?php if (!empty($fields)) :?>
        <div class="col-md-4">
            <label><i class="icon ti-settings"></i> <?php echo lang('common_select_field_to_check_duplicate'); ?></label>
            <select class="form-control" name="check_duplicate_field" id="check-duplicate-field">
                <option value="0"><?php echo lang('common_select_field_to_check_duplicate'); ?></option>
                <optgroup label="<?php echo lang('common_basic_attributes'); ?>">
                    <?php foreach ($fields as $field) :?>
    <?php
        if($field != 'balance' && $field != 'balance_2') {
    ?>
            <option value="basic:<?php echo $field; ?>"><?php echo lang('common_' . $field); ?></option>
    <?php
        }else {
            if($field == 'balance')
                $field_name = $this->config->item('customer_balance');
            else
                $field_name = $this->config->item('customer_balance_2');
    ?>
            <option value="basic:<?php echo $field; ?>"><?php echo $field_name; ?></option>
        <?php
        }
    ?>

                    <?php endforeach; ?>
                    <?php foreach ($person_fields as $field) :?>
                            <option value="person:<?php echo $field; ?>"><?php echo lang('common_' . $field); ?></option>
                    <?php endforeach; ?>
                    
                </optgroup>
                
                <optgroup class="attributes-by-set" id="attributes-by-set" label="<?php echo lang('common_attributes_by_set'); ?>">
                   
                </optgroup>
            </select>
        </div>
        <?php endif; ?>
        <div class="col-md-4">
            <label style="visibility: hidden; margin-bottom: 9px;"><i class="icon ti-settings"></i> <?php echo lang('common_select_field_to_check_duplicate'); ?></label>
            <button type="button" class="btn btn-primary" onclick="action_import_data('data-import');">Thực hiện</button>
        </div>
    </div>

    <!-- <?php var_dump($customers_contract_info_add); ?>  -->
    <!-- table dữ liêu excel -->
     <div  class="item-scroll">
         <table class="table table-hover mt-10">
        <thead>
            <tr>
                <th width="30px"></th>
                <th>
                    <input type="checkbox" id="chk-all" />
                    <label for="chk-all"><span></span></label>
                </th>
                <?php foreach ($columns as $column) :?>
                <th>
                    <select old_value="" onfocus="this.old_value = this.value" onchange="return action_select_column($(this))" name="columns[<?php echo $column; ?>]" class="form-control customer-form">
                        <option value="0"><?php echo lang('common_column') . ' ' . $column; ?></option>


                        <optgroup label="<?php echo lang('common_basic_attributes'); ?>">
                            <?php foreach ($fields as $field) :?>
                                <?php
                                if($field != 'balance' && $field != 'balance_2') {
                                ?>
                                    <option value="basic:<?php echo $field; ?>"><?php echo lang('common_' . $field); ?></option>
                                <?php
                                }else {
                                ?>
                                    <option value="basic:<?php echo $field; ?>"><?php echo $this->config->item('customer_'.$field); ?></option>
                                <?php
                                }
                                ?>
                            <?php endforeach; ?>

                            <?php foreach ($person_fields as $field) :?>
                            <option value="person:<?php echo $field; ?>"><?php echo lang('common_' . $field); ?></option>
                            <?php endforeach; ?>
                        </optgroup>
                        <!-- Thuộc tính theo bộ -->
                        <optgroup class="attributes-by-set" label="<?php echo lang('common_attributes_by_set'); ?>">
                        </optgroup>
                        <!-- Thông tin liên hệ thêm -->
                        <optgroup class="thong_tin_lien_he_them" label="Thông tin liên hệ người đại diện">
                             <?php foreach ($customers_contract_info_add as $field) :?>
                            <option value="thong_tin_lien_he_them:<?php echo $field; ?>"><?php echo lang('common_' . $field); ?></option>
                            <?php endforeach; ?>
                        </optgroup>
                    </select>
                </th>
                <?php endforeach; ?>
                <th></th>
            </tr>
        </thead>

        <!-- Data ở đây -->
        <tbody>
            <?php for ($index = 1; $index <= $num_rows; $index++) :?>
                <tr data-import-id="<?php echo $index; ?>">
                    <td width="45px" <?php if ($index > 1) :?>class="selected"<?php endif; ?>><strong><?php echo ($index); ?>. </strong></td>
                    <td <?php if ($index > 1) :?>class="selected"<?php endif; ?>>
                        <input <?php if ($index > 1) :?>checked="checked"<?php endif; ?> name="selected_rows[<?php echo ($index); ?>]" value="1" class="chk-row" type="checkbox" id="chk-row-<?php echo $index; ?>" />
                        <label for="chk-row-<?php echo $index; ?>"><span></span></label>
                    </td>
                    <?php $column_index = 0; foreach ($columns as $column) :?>
                    <td <?php if ($index > 1) :?>class="selected"<?php endif; ?>>
                        <?php $value = $sheet->getCellByColumnAndRow($column_index, $index)->getValue(); (is_object($value)) ? $value = $value->getPlainText() : $value; ?>
                        <input name="rows[<?php echo ($index); ?>][<?php echo $column; ?>]" type="text" class="form-control customer-form" value="<?php echo $value; ?>" />
                        <?php unset($value); ?>
                    </td>
                    <?php $column_index++; endforeach; ?>
                    <td <?php if ($index > 1) :?>class="selected"<?php endif; ?>>
                        <button type="button" onclick="$(this).parent().parent().remove()" class="btn btn-sm btn-primary"><?php echo lang('common_delete'); ?></button>
                    </td>
                </tr>
            <?php endfor; ?>
        </tbody>
    </table>
    </div>
    
</div>
<style>
    #data-import tr[data-import-id].error td.selected {
        background: red;
    }
    
    .item-scroll {
        overflow-x: scroll;
    }

    .table .customer-form {
        width: 250px;
    }
</style>
<?php $this->load->view('import/client_script'); ?>