$(document).on('show.bs.modal', '.modal', function (event) {
    var zIndex = 9999040 + (10 * $('.modal:visible').length);
    $(this).css('z-index', zIndex);
    setTimeout(function() {
        $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
    }, 0);
});

function frm_location_group_without_db(location_id, employee_id) {
    $.ajax({
        type: "POST",
        url: BASE_URL + 'employees/location_group',
        data: {
            location_id        : location_id,
            employee_id        : employee_id
        },
        success: function(html){
            $('#quick_modal').addClass('size-700');
            $('#quick_modal').html(html);
            $('#quick_modal').modal('toggle');
        }
    });
}

function save_commission_from_location_group() {
    var form_id = 'commission_location_group_form';
    var group_id = $('#'+form_id+' [name="group_id"]').val();
    var commission_percent = $('#'+form_id+' [name="commission_percent"]').val();
    var commission_percent_type = $('#'+form_id+' [name="commission_percent_type"]').val();
    var location_id = $('#'+form_id+' [name="location_id"]').val();

    if(group_id == -1)
        toastr.error('Phải chọn nhóm.', 'Lỗi');
    else {
        var flag = true;
        var section = 'location_'+location_id+'_'+group_id+'_section';
        var groups = $('#'+section+' [name="lc_group_id[]"]');
        if(groups.length) {
            var array = new Array();
            groups.each(function( index ) {
                array[array.length] = $( this ).val();
            });

            if ($.inArray(group_id, array) != -1)
            {
                flag = false;
            }
        }

        if(flag == true) {
            $.ajax({
                type: "POST",
                url: BASE_URL + 'employees/add_group_section',
                data: {
                    location_id                 : location_id,
                    group_id                    : group_id,
                    commission_percent          : commission_percent,
                    commission_percent_type     : commission_percent_type
                },
                success: function(html){
                    var btn_parent = $('#btn_add_group_section_'+location_id).closest('.form-group');
                    btn_parent.before(html);

                    $('#quick_modal').modal('toggle');
                }
            });
        }else {
            $('#quick_modal').modal('toggle');
        }
    }
}

function remove_location_group(obj) {
    var big_section = $(obj).closest('.big-section');
    big_section.remove();
}