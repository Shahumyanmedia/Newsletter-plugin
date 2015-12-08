;(function( $ ){
    jQuery(document).ready(function(){
    //page js
    jQuery('#SH_newsletter input[name="submit"]').on('click', function(){
        var name = jQuery('#SH_newsletter input[name="name"]').val();
        var email = jQuery('#SH_newsletter input[name="email"]').val();
        function validateEmail(emailTest) {
            var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
            return re.test(emailTest);
        }
        if(name != '' && email != '' && validateEmail(email))
        {
            jQuery('.progress').slideDown('200');
            url = "/wp-admin/admin-ajax.php";
            jQuery.ajax({

                url: url,

                type:"POST",

                cache:false,

                data:{

                    action: "register",

                    name:name,

                    email:email

                },

                success:function(data){
                    jQuery('#SH_newsletter input[name="name"]').val('');
                    jQuery('#SH_newsletter input[name="email"]').val('');
                    jQuery('#SH_newsletter .answerThankYou').empty().fadeIn().append(data);
                    setTimeout(function(){jQuery('#SH_newsletter .answerThankYou').fadeOut();}, 3000);
                    jQuery('#SH_newsletter .progress').slideUp('200');
                },

                error: function(err){

                    jQuery('#SH_newsletter .errorBase').fadeIn();
                    setTimeout(function(){jQuery('#SH_newsletter .errorBase').fadeOut();}, 3000);
                }

            });
        }
        if(!validateEmail(email) && email != ''){
            jQuery('#SH_newsletter .errorEmail').fadeIn();
            setTimeout(function(){jQuery('#SH_newsletter .errorEmail').fadeOut();}, 2000);
        }
        if(name == ''){
            jQuery('#SH_newsletter .emptyName').fadeIn();
            setTimeout(function(){jQuery('#SH_newsletter .emptyName').fadeOut();}, 2000);
        }
        if(email == ''){
            jQuery('#SH_newsletter .emptyEmail').fadeIn();
            setTimeout(function(){jQuery('#SH_newsletter .emptyEmail').fadeOut();}, 2000);
        }
    });
        jQuery(document).keydown(function(e){
            if (e.keyCode == 13 ){
                if($('#SH_newsletter input[name="name"]').is(":focus") || $('#SH_newsletter input[name="email"]').is(":focus")){
                    jQuery('#SH_newsletter input[name="submit"]').trigger('click');
                }
            }
        });

    });
})( jQuery );