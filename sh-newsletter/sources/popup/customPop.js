jQuery(document).ready(function(){
    window.history.pushState("", "", '/');
    jQuery('body').append('<div class="BGGR"></div> <div id="shPopup"><div class="X">X</div> ' +
    '<div class="shPopupContent">' +
    '<div class="shPopupQuestion TODO">' +
    'You want to unsubscribe?</div>' +
    '<input type="button" class="shPopupButton" id="yes" value="Yes"/>' +
    '<input type="button" class="shPopupButton" id="no" value="No"/>' +
    '</div>' +
    ' </div>');
    jQuery('#no, .X').on('click', function(){
        jQuery('.BGGR,#shPopup ').remove();
    });
    jQuery(document).keydown(function(e){
        if (e.keyCode == 27 ){
            jQuery('.X').trigger('click');
        }
    });

    jQuery('#yes').on('click', function(){
        var key = jQuery('#shKey').attr('data-secret-key');
        var url = "/wp-admin/admin-ajax.php";
        jQuery.ajax({

            url: url,

            type: "POST",

            cache: false,

            data: {

                action: "unsubscribe",

                key: key

            },

            success: function (data) {
                jQuery('.shPopupContent').empty().append('<div class="TODO">'+data+'</div>');
                jQuery('.X').on('click',function(){
                    jQuery('.BGGR,#shPopup ').remove();
                });
            },

            error: function (err) {
                alert('Please try again later');
                jQuery('.error').fadeIn();
            }

        });
    })
});
