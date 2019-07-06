<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 12/06/2017
 * Time: 14:25
 */
?>
<script>
    // var dialog = bootbox.dialog({
                // title: 'Thông báo cập nhật',
                // message: '<p><i class="fa fa-spin fa-spinner"></i> Có thông tin cập nhật mới, vui lòng chờ</p><div class="myItem" style="width: 100%"></div>',
                // buttons: {
                    // confirm: {
                        // className: 'cap_nhat blue',
                        // label: '<i class="fa fa-check"></i> Đồng ý'
                    // }
                // },
            // });
            dialog.init(function(){
                dialog.find('.cap_nhat').addClass('hidden');
//                var bar = new ldBar(".mySelector", {
//                    "stroke-width": 10,
//                    "stroke": "data:ldbar/res,gradient(0,1,#f99,#ff9)",
//                });

//                var bar1 = new ldBar('.myItem',{
//                    "value":0,
//                    "fill-dir":"ltr",
//                    "stroke": "data:ldbar/res,gradient(0,50,#f99,#ff9)",
//                });
//                var bar2 = document.getElementsByClassName('.myItem').ldBar;
//                bar1.set(100);

                var _data = {};
                setTimeout(function(){
                    cap_nhat();
                }, 8000);

                function cap_nhat() {
                    coreAjax.call(
                        '<?php echo site_url("Update_he_thong/cap_nhat");?>',
                        _data,
                        function(response)
                        {
                            if(response.flag){
                                setTimeout(function(){
                                    dialog.find('.bootbox-body').html('<b>'+response.msg+'</b>').addClass('text-info');
                                    dialog.find('.cap_nhat').removeClass('hidden');
                                }, 3000);
                                setTimeout(function(){
                                    dialog.addClass('hidden');
                                }, 5000);

                            } else {
                                setTimeout(function(){
                                    dialog.find('.bootbox-body').html('<b>'+response.msg+'</b>').addClass('text-danger');
                                    dialog.find('.cap_nhat').removeClass('hidden');
                                }, 3000);
                                setTimeout(function(){

                                }, 5000);
                            }

                        }
                    );
                }

            });
</script>

<?php

?>