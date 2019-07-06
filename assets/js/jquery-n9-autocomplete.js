function n9_autocomplete_result(input_name, keycode) {
    var element_items = $('#result_'+input_name+' .item');
    if(element_items.length) {
        var element_active = $('#result_'+input_name+' .item.active');
        if(element_active.length) {
            if(keycode == 40) {
                var element_next = element_active.next();
                if(element_next.length) {
                    element_items.removeClass('active');
                    element_next.addClass('active');
                }else {
                    element_items.removeClass('active');
                    element_items.first().addClass('active');
                }
            }else if(keycode == 38) {
                var element_prev = element_active.prev();
                if(element_prev.length) {
                    element_items.removeClass('active');
                    element_prev.addClass('active');
                }else {
                    element_items.removeClass('active');
                    element_items.last().addClass('active');
                }
            }

        }else{
            element_items.removeClass('active');
            element_items.first().addClass('active');
        }
    }
}

function n9_autocomplete_resize(input_name) {
    var position = $('#'+input_name).offset();
    var top = position.top;
    var left = position.left;
    var height = $('#'+input_name).outerHeight();
    var width = $('#'+input_name).outerWidth();

    top = top + height;

    var styles = {
        top : top+'px',
        left: left+'px',
        width: width+'px'
    };
    $( '#result_'+input_name ).css( styles );
}

function n9_autocomplete_start_search (input_name) {
    var url = $('#'+input_name).attr('data-url');
    var keywords = $.trim($('#'+input_name).val());

    if(keywords) {
        $.ajax({
            type: "POST",
            url: url,
            data: {
                keywords : $('#'+input_name).val()
            },
            success: function(string){
                var result = $.parseJSON(string);
                if(result.length) {
                    var html = '';
                    var item = '';
                    $.each(result, function( index, value ) {
                        item = create_item(input_name, value);
                        html = html + item;
                    });

                    n9_autocomplete_resize(input_name);
                    $('#result_'+input_name).html(html);
                    $( '#result_'+input_name).show();
                }else {
                    $( '#result_'+input_name).hide();
                }
            }
        });
    }else {
        $('#result_'+input_name).html('');
        $( '#result_'+input_name).hide();
    }
}

function item_template_one(obj) {
    var image         = obj.image;
    var full_name     = obj.first_name + ' ' + obj.last_name;
    if(obj.email == null)
        var email = '';
    else
        var email     = obj.email;

    var id            = obj.person_id;

    var html = '<li class="customer-badge suggestions ui-menu-item item" data-id="'+id+'" data-name="'+full_name+'">'+
                    '<a class="suggest-item ui-corner-all"><div class="avatar">'+
                    '<img src="'+image+'" alt=""></div>'+
                    '<div class="details">'+
                    '<div class="name">'+full_name+'</div>'+
                    '<span class="email">'+email+'</span>'+
                    '</div>'+
                    '</a>'+
                '</li>';
    return html;
}

function create_item(input_name, obj) {
    var html = item_template_one(obj);
    return html;
}

function add_item(input_name, id, name) {
    var flag = true;
    var selected_input = $('[name="n9-autocomplete-item['+input_name+'][]"]');
    if(selected_input.length) {
        var selected_ds = new Array();
        $( selected_input ).each(function( index, element ) {
            var value = $(element).val();
            selected_ds[selected_ds.length] = value;
        });

        if ($.inArray(id, selected_ds) != -1)
        {
            flag = false;
        }
    }

    if(flag == true) {
        var attr = $('[name="'+input_name+'"]').attr('data-add-url');
        if (typeof attr !== typeof undefined && attr !== false) {
            handling_add(input_name, id, name, attr);
        }else {
            var link_employee = BASE_URL + 'employees/view/'+id;
            var string = '<span class="key">'+
                            '<a href="javascript:;" class="delete-payment"><i class="icon ion-android-cancel"></i></a> <a href="'+link_employee+'" target="_blank">'+name+'</a>'+
                            '<input type="hidden" name="n9-autocomplete-item['+input_name+'][]" value="'+id+'" />'+
                        '</span>';

            $('#'+input_name+'_select_list').append( string );
        }

    }
    $('#'+input_name).val('');
    $('#result_'+input_name).hide();
    $('#result_'+input_name+' .item').removeClass('active');
}

function item_template(input_name, id, name) {
    var link_employee = BASE_URL + 'employees/view/'+id;
    var string = '<span class="key">'+
                    '<a href="javascript:;" class="delete-payment"><i class="icon ion-android-cancel"></i></a> <a href="'+link_employee+'" target="_blank">'+name+'</a>'+
                    '<input type="hidden" name="n9-autocomplete-item['+input_name+'][]" value="'+id+'" />'+
                '</span>';

    return string;
}

var input_arr = new Array();

function n9_autocomplete(input_name)  {
    var index_array = input_arr.indexOf(input_name);
    //$('.n9-autocomplete-result').remove();
    if($('#result_'+input_name).length == 0) {
        $( "body" ).append('<ul class="ui-autocomplete ui-front ui-menu ui-widget ui-widget-content ui-corner-all n9-autocomplete-result" id="result_'+input_name+'"></ul>');
    }
    if($('#'+input_name).length && index_array == -1) {
        input_arr[input_arr.length] = input_name;
        // create result section

        //
        var n9_typingTimer;
        $('body').on('keyup','#'+input_name,function(e){
            if(e.keyCode != 40 && e.keyCode != 38 && e.keyCode != 13) {
                clearTimeout(n9_typingTimer);
                n9_typingTimer = setTimeout(n9_autocomplete_start_search, 500, input_name);
            }
        });

        $('body').on('keydown',"#"+input_name,function(e){
            if(e.keyCode == 40 || e.keyCode == 38) {
                n9_autocomplete_result(input_name, e.keyCode)
            }

            if(e.keyCode == 13) {
                var active_item = $('#result_'+input_name+' .item.active')
                if(active_item.length) {
                    var id = active_item.attr('data-id');
                    var name = active_item.attr('data-name');
                    add_item(input_name, id, name);
                }
            }

            clearTimeout(n9_typingTimer);
        });

        $('body').on('click',"#"+input_name,function(){
            n9_autocomplete_resize(input_name);
            var element_items = $('#result_'+input_name+' .item');
            if(element_items.length){
                $("#result_"+input_name).show();
            }
        });

        $('body').on('click','#result_'+input_name+' li.item',function(){
            var id   = $(this).attr('data-id');
            var name = $(this).attr('data-name');

            add_item(input_name, id, name);
        });

        $('body').on('click','#'+input_name+'_select_list span.key a.delete-payment',function(){
            var span_parent = $(this).closest('span.key');
            span_parent.remove();
            var input_in_parent = span_parent.find('[name="n9-autocomplete-item['+input_name+'][]"]');
            var id = input_in_parent.val();

            var attr = $('#'+input_name).attr('data-delete-url');
            if (typeof attr !== typeof undefined && attr !== false) {
                var url = attr;
                handling_delete(input_name, id, url);
            }
        });

        $(document).click(function (e)
        {

            if (!$("#result_"+input_name).is(e.target) && $("#result_"+input_name).has(e.target).length === 0 && !$('#'+input_name).is(e.target) && $('#'+input_name).has(e.target).length === 0)

            {
                $("#result_"+input_name).hide();
            }
        });
    }

}

function template_result_autocomplete(id) {
    var html = '<ul class="ui-autocomplete ui-front ui-menu ui-widget ui-widget-content ui-corner-all n9-autocomplete-result" id="'+id+'"></ul>';
    return html;
}