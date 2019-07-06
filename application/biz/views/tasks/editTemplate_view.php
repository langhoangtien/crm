<?php $this->load->view("partial/header"); ?>
    <link href="<?php echo base_url();?>assets/css/font-awesome.min.css" media="screen" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="<?php echo base_url();?>assets/scripts/tasks/codebase/dhtmlxgantt.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/style.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/responsive.css" type="text/css" media="screen" />
    <script src="<?php echo base_url();?>assets/scripts/tasks/codebase/dhtmlxgantt.js" type="text/javascript" charset="utf-8"></script>
    <script src="<?php echo base_url(); ?>assets/js/plugins/jquery-sortable-lists/jquery-sortable-lists.js"></script>
    <link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/style.css" type="text/css" media="screen" />
    <link rel="stylesheet" type="text/css" href="<?php echo base_url() . 'assets/tasks/css/tree.css'; ?>" media="screen">
    <script src="<?php echo base_url() . 'assets/tasks/js/task-core.js'; ?>"></script>
    <script src="<?php echo base_url() . 'assets/tasks/js/template.js'; ?>"></script>
    
<style>
    .close-temp-task {
        float: right;
    }
    .clear-both {
        clear: both;
    }
    .fr {
        float: right;
    }
    .fl {
        float: left;
    }
    .glyphicon {
        cursor: pointer;
    }
</style>

<?php
    $id   = $item['id'];
    $name = $item['name'];
?>
    <div class="main-content">
        <div class="row" id="form">
            <div class="spinner" id="grid-loader" style="display:none">
                <div class="rect1"></div>
                <div class="rect2"></div>
                <div class="rect3"></div>
            </div>
            <div class="col-md-12">
                <form id="template_form" class="form-horizontal" method="post" accept-charset="utf-8" novalidate="novalidate">
                    <input type="hidden" id="count_task" value="<?php echo count($item['tasks']) + 1; ?>" />
                    <div class="panel panel-piluku">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <i class="ion-edit"></i>
                                Sửa mẫu công việc <small>(Các trường màu đỏ là cần nhập)</small>
                                <div class="button_control">
                                    <i class="ion-navicon-round" id="btnListTasks"></i>
                                </div>
                            </h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="name" class="required col-sm-3 col-md-3 col-lg-2 control-label ">Tên mẫu:</label>
                                        <div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="text" name="template_name" id="template_name" value="<?php echo $name; ?>" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="name" class="required col-sm-3 col-md-3 col-lg-2 control-label ">Loại dịch vụ :</label>
                                        <div class="col-sm-9 col-md-9 col-lg-10">
                                            <select name="s_category_id" class="form-control data-n9-s" id="s_category_id">
                                                <?php foreach ($categories['list_items_cate'] as $k => $val) { ?>
                                                    <option value="<?php echo $val[item_id];  ?>" <?php echo ((int)$val['item_id'] == (int)$item['danhmuc']) ? 'Selected' : '' ?>><?php echo $val['name']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="first_name" class="required col-sm-3 col-md-3 col-lg-2 control-label ">Công việc :</label>
                                        <div class="col-sm-9 col-md-9 col-lg-10" id="sort_section">
                                        <?php
                                            $newMenu = '';
                                            recursiveMenu($item['tasks'],$item['id'], 0, $newMenu);
                                            $newMenu = str_replace('</li><ul>','<ul>',$newMenu);
                                            $newMenu = str_replace('</ul><li class="sortableListsOpen">','</ul></li><li class="sortableListsOpen">',$newMenu);
                                            $newMenu = str_replace('<ul></ul>','',$newMenu);

                                            echo $newMenu;
                                        ?>
                                        </div>
                                    </div>

                                    <div class="form-actions pull-right">
                                        <input type="button" value="Thêm công việc" onclick="add_template_task();" class="btn btn-primary submit_button btn-large button_new" style="margin-right: 7px;"/>
                                        <input type="button" value="Lưu lại" onclick="update_template(<?php echo $id; ?>);" class="btn btn-primary submit_button btn-large button_new">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade box-modal" id="quick_modal">
    </div>
    <script type="text/javascript">
        $( document ).ready(function() {
            var options = {
                placeholderCss: {'background-color': 'white'},
                hintCss: {'background-color':'white'},
                opener: {
                    active: true,
                    as: 'html',
                    close: '<i class="fa fa-minus c3"></i>',
                    open: '<i class="fa fa-plus"></i>',
                    openerCss: {

                    }
                },
                insertZonePlus: true,
                ignoreClass: 'clickable'
            };
            $('#sTree2').sortableLists( options );

            $(window).keydown(function(event){
                if(event.keyCode == 13) {
                    if($('#quick_modal').hasClass('in')) {
                        add_template_task_tree();
                    }else {
                        update_template(<?php echo $id; ?>);
                    }

                    event.preventDefault();
                    return false;
                }
            });

            $('body').on('click','#btn_save_templae_task',function(){
                add_template_task_tree();
            });
        });
    </script>
<?php $this->load->view("partial/footer"); ?>