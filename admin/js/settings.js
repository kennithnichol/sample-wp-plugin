jQuery(document).ready(function($) {
    var options =  {
        defaultColor: false,
        hide: true,
        palettes: true
    };

    $('.button-color-picker').wpColorPicker(options);

    $( '#sharebar-networks' ).sortable({
        placeholder: "ui-state-highlight",
        stop: function() {
            $('input.sharebar-network-order').each(function(index) {
                $(this).val(index);
            });
        }
    });
});
