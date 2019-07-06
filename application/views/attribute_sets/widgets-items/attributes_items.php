<?php if (!empty($attribute_sets) && !empty($attribute_groups)) :?>
<?php foreach ($attribute_groups as $attribute_group) :?>
    <?php if (!isset($attribute_group->has_attributes)) continue; ?>
    <div class="panel">
        <div class="panel-heading panel-heading-toggle<?php echo $attribute_group->id ?>">
        <a class="panel-title" target="_blank" ><i class="icon ion-edit"></i> <?php echo $attribute_group->name; ?></a>
        </div>
        <div class="panel-body attribute-group" data-id = "<?php echo $attribute_group->id ?>" style="display: <?php echo isset($dataAttrGroupShowPost[$attribute_group->id])?$dataAttrGroupShowPost[$attribute_group->id]:'none' ?>;">
            <?php foreach ($attributes as $attribute) :?>
            <?php if ($attribute->attribute_group_id == $attribute_group->id) :?>
                <div class="row                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     form-group">
                    <label class="col-sm-3 col-md-3 col-lg-2 control-label">
                        <a target="_blank" href="<?php echo site_url('attributes/view/'.$attribute->id.'/2'); ?>">
                            <?php echo $attribute->name.' ('.$attribute->code.')'; ?>
                        </a>
                    </label>
                    <div class="col-sm-9 col-md-9 col-lg-10">
                        <?php echo $this->Attribute->get_html($attribute); ?>
                    </div>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
    <script type="text/javascript">
    $('.panel-heading-toggle<?php echo $attribute_group->id ?>'  ).click(function() {
        $(this).next().toggle(200);
        }) 
</script>
    <?php endforeach; ?>
<?php endif; ?>


