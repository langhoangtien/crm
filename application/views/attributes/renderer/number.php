<?php

    // Hằng số này chỉ dùng cho rèm Hà My
    const DON_GIA_VAI = 'dgv';
    const KHO_VAI_MAU = 'kvm';
 if (!empty($attribute)) :
 ?>

<input id = "<?php echo $attribute->code ?>" <?php if($this->config->item('company_user') =='remHaMy' && ($attribute->code==DON_GIA_VAI) ) echo 'readonly'?> class="form-control <?php if (!empty($attribute->required) && $attribute->required == Attribute::YES) :?>required<?php endif; ?> attribute-input" type="number" name="attributes[<?php echo $attribute->id; ?>]"  <?php if (!empty($attribute_values[$attribute->id])) :?> value="<?php echo !empty($attribute_values[$attribute->id]->entity_value)? $attribute_values[$attribute->id]->entity_value:0; ?>"<?php else: ?>value="0" <?php endif;?> />
<?php endif; ?>