$(document).on('show.bs.modal', '.modal', function (event) {
    var zIndex = 9999040 + (10 * $('.modal:visible').length);
    $(this).css('z-index', zIndex);
    setTimeout(function() {
        $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
    }, 0);

    $('#my_table').on('hidden.bs.modal', function () {
        if($('#my_table').hasClass('size-1200'))
            $('#my_table').removeClass('size-1200');

        if($('#my_table').hasClass('size-1000'))
            $('#my_table').removeClass('size-1000');
    })

    $('#quick_modal').on('hidden.bs.modal', function () {
        if($('#quick_modal').hasClass('size-700'))
            $('#quick_modal').removeClass('size-700');

        if($('#quick_modal').hasClass('size-1000'))
            $('#quick_modal').removeClass('size-1000');
    })
});