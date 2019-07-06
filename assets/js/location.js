$(document).on('show.bs.modal', '.modal', function (event) {
    var zIndex = 9999040 + (10 * $('.modal:visible').length);
    $(this).css('z-index', zIndex);
    setTimeout(function() {
        $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
    }, 0);
});
function frm_location_group_without_db(location_id) {
    $.ajax({
        type: "POST",
        url: BASE_URL + 'locations/location_group',
        data: {
            id          : location_id,
            type        : 'without_db'
        },
        success: function(html){
            $('#quick_modal').addClass('size-700');
            $('#quick_modal').html(html);
            $('#quick_modal').modal('toggle');
        }
    });
}

function save_location_group_without_db() {
    var form_id = 'location_group_form';
    var location_group_id = $('#'+form_id+' [name="location_group_id"]').val();
    var status = $('#'+form_id+' [name="status"]').val();

    if(location_group_id == -1)
        toastr.error('Phải chọn nhóm.', 'Lỗi');
    else {
        var flag = true;
        var locations = $('[name="location_group_id[]"]');
        if(locations.length) {
            var array = new Array();
            locations.each(function( index ) {
                array[array.length] = $( this ).val();
            });

            if ($.inArray(location_group_id, array) != -1)
            {
                flag = false;
            }
        }

        if(flag == true) {
            $.ajax({
                type: "POST",
                url: BASE_URL + 'locations/add_group_section',
                data: {
                    location_group_id   : location_group_id,
                    status              : status
                },
                success: function(html){
                    var btn_parent = $('#btn_add_group_section').closest('.form-group');
                    btn_parent.before(html);

                    $('#quick_modal').modal('toggle');
                }
            });
        }else {
            $('#quick_modal').modal('toggle');
        }

    }
}

function remove_group_section(obj) {
    var big_section = $(obj).closest('.big-section');
    big_section.remove();
}

function reset_form_error(form_id) {
    $('#'+form_id+' .has-error').removeClass('has-error');
    $('#'+form_id+' p.errors').text('');
}

function save_location() {
    reset_form_error('location_form_n9');
    var url = BASE_URL + 'locations/location_save';

    var checkOptions = {
        url : url,
        dataType: "json",
        success: save_location_data
    };

    $("#location_form_n9").ajaxSubmit(checkOptions);
    return false;
}

function save_location_data(data) {
    if(data.flag == 'false') {
        var first_key = Object.keys(data.errors)[0];
        $.each(data.errors, function( index, value ) {
            element = $( '#location_form_n9 [name="'+index+'"]' );
            group = element.closest('.form-group');
            group.addClass('has-error');
            group.find('p[for="'+index+'"]').text(value);
            if(index == 'employees') {
                $('p[for="employees"]').closest('.form-group').addClass('has-error');
                $('p[for="employees"]').text(value);
            }
            if(index == 'register') {
                $('p[for="register"]').text(value);
                
            }
            
        });

        $( '#location_form_n9 [name="'+first_key+'"]' ).focus();

    }else if(data.flag == 'group-error') {
        $('body').animate({scrollTop: ($('#btn_add_group_section').offset().top) + 'px'}, 500);
        toastr.error(data.msg, 'Lỗi');
    }else {
        window.location.href = BASE_URL + 'locations';
    }
}
