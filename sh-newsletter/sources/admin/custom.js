;(function( $ ){
    jQuery(document).ready(function(){
        jQuery(document).keydown(function(e){
            console.log(e.keyCode);
            if(e.keyCode == 123 ){
               // return false;
            }
        });

    jQuery('textarea').empty();
    //admin js
        //checking
        jQuery('#checkAll').on('click', function(){
            jQuery('.sh_newsletter tr.standard').each(function(){
              jQuery('input',this).trigger('click');
            })
        });
        jQuery('.sh_newsletter tr.standard').each(function(){
            var index = + jQuery('td',this).first().text();
            if(index < 10)
            {
                jQuery('td',this).first().css({paddingRight: 9})
            }
            jQuery('td.email, td.name',this).on('click', function(){
                jQuery('.delete, .addEmail').removeAttr('disabled');
                var how = jQuery(this).attr('class');
                if(how == 'name')
                    jQuery(this).prev().find('input').trigger('click');
                else
                    jQuery(this).prev().prev().find('input').trigger('click');
            });
            jQuery('.checking',this).on('change', function(){
                jQuery('.delete, .addEmail').removeAttr('disabled');
                if(jQuery(this).val() == '0')
                    jQuery(this).attr('value', '1');
                else
                    jQuery(this).attr('value', '0');
            })
        });
        //delete emails with ajax
        var ids =[];
        jQuery('input.delete').on('click', function(){
            jQuery('.sh_newsletter tr.standard').each(function(){
                if(jQuery('.checking',this).val() == '1') {
                    jQuery('.error').fadeOut();
                    var deleteID = jQuery('.checking',this).attr('id');
                    ids.push(deleteID);
                    jQuery('.checking',this).attr('value', '0');
                    jQuery(this).slideToggle().remove();
                }
            });

            url = "/wp-admin/admin-ajax.php";

            jQuery.ajax({

                url: url,

                type:"POST",

                cache:false,

                data:{

                    action: "delete_mail_person",

                    id:ids

                },

                success:function(data){


                },

                error: function(err){

                    jQuery('.error').fadeIn();
                    jQuery('.sh_newsletter tr').fadeIn();
                }

            });
        });
    //add emails in email box
        function checkHeight(){
            var rightH = jQuery('.MailsHeight').height();
            if(rightH>31){
                if(jQuery('.apArrow').length <= 0){
                    jQuery('.sendMails').append('<div class="apArrow">&darr;</div>');
                }
            }else{
                jQuery('.apArrow').remove();
            }
        }
        var emails = [];
        jQuery('input.addEmail').on('click', function() {
            jQuery('.sh_newsletter tr.standard').each(function () {
                if (jQuery('.checking', this).val() == '1') {
                    var thisEmail = jQuery('.email', this).text();
                    emails.push(thisEmail);
                    jQuery('.checking', this).attr('disabled', 'disabled');
                    jQuery(this).css({color:'grey', boxShadow: 'none', backgroundColor:'#f5f5f5', borderLeft: 0});
                }
            });
            jQuery('.sendMails .MailsHeight').empty();

            for (var x in emails) {
                jQuery('.sendMails .MailsHeight').append('<div class="vR">' +
                    '<div class="mail">' +
                        '<div class="M">'+emails[x]+'</div>' +
                        '<div class="X"></div>' +
                    '</div>'+
                '</div>');
            }
            emails = [];
            checkHeight();

        });
       setInterval(function(){
           jQuery('.X').on('click', function(){
               jQuery(this).parents('.vR').remove();
               jQuery(this).parents('.postAdded').remove();
               var deleteMailInSendBox = jQuery(this).prev().text();
               jQuery('tr.standard').each(function(){
                   var inActive = jQuery('.email', this).text();
                   if(deleteMailInSendBox == inActive)
                   {
                       jQuery('input', this).removeAttr('disabled');
                       jQuery('.checking:before',this).css({content: ''});
                       jQuery(this).css({color:'#444444', backgroundColor:'#ffffff', borderLeft: 0});
                   }
               });
               checkHeight();
           });
       },500);
        jQuery('.sendMails').hover(function(){
            var height = jQuery('.MailsHeight').height();
            if(height>30)
                jQuery(this).css({height:height});
        },function(){
            jQuery(this).css({height:31});
        });

        //send mail check and ajax/

        jQuery(function(){
            var btnUpload=jQuery('#upload');
            var status=jQuery('#status p i');
            new AjaxUpload(btnUpload, {
                action: '',
    //Name of the file input box
                name: 'uploadfile',
                onSubmit: function(file, ext){
                    if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){
    // check for valid file extension
                        status.text('Only JPG, PNG, GIF files are allowed');
                        return false;

                    }
                    jQuery('.progress').slideDown('200');
                },
                onComplete: function(file, response){
                    //On completion clear the status
                    jQuery('.progress').slideUp('200');
                    jQuery('#status p i').text('');
                    console.log(response);
                    //Add uploaded file to list
                    if(response==="success"){
                        jQuery('<li></li>').appendTo('#files').html('<img src="./uploads/'+file+'" alt="'+file+'" /><br />'+file).addClass('success');
                    } else{
                        jQuery('<li data-link="'+file+'"></li>').appendTo('#files').html('<div class="vR">' +
                            '<div class="mail">' +
                                '<div class="M">' +
                                    '<i>'+file+'</i>' +
                                '</div>' +
                                '<div class="X"></div>' +
                            '</div>' +
                        '</div>').addClass('success');
                    }
                }
            });
        });
        jQuery('input#SendMail').on('click', function(){
            var sendsMail = [];
            var sendsImg = [];

            jQuery('.sendMails .mail').each(function(){
               var sendingMail = jQuery('.M',this).text();
                sendsMail.push(sendingMail);
            });
            jQuery('#files li').each(function(){
                var imgLink = jQuery('i',this).text();
                sendsImg.push(imgLink);
            });
            var subText = jQuery('#subject').val();
            var areText = jQuery('.EmailSendBox textarea').val();
            if(sendsMail != '' && subText != '' && areText != '' ) {

                jQuery(this).attr('disabled', 'disabled');
                jQuery('input[name="uploadfile"]').attr('disabled', 'disabled');
                jQuery('.progress').slideDown('200');

                var url = "/wp-admin/admin-ajax.php";
                jQuery('.content textarea, .content input#subject, .content #files').attr('disabled', 'disabled');
                jQuery.ajax({

                    url: url,

                    type: "POST",

                    cache: false,

                    data: {

                        action: "sendMail",

                        mails: sendsMail,

                        images: sendsImg,

                        sub: subText,

                        body: areText

                    },

                    success: function (data) {
                        sendsMail = [];
                        jQuery('.answer').empty();
                        jQuery('.answer').append( data );
                        jQuery('.progress').slideUp('200');
                        jQuery('.content textarea, .content input#subject, .content #files').empty().val('').removeAttr('disabled');
                        jQuery('input#SendMail').removeAttr('disabled');
                        jQuery('input[name="uploadfile"]').removeAttr('disabled');
                        setTimeout(function(){jQuery('.answer').empty();}, 10000);
                    },

                    error: function (err) {

                        jQuery('.error').fadeIn();
                    }

                });
            }
            else{
                jQuery('.answer').empty();
                if(sendsMail == ''){
                    jQuery('.answer').append( '<p>Please select Emails</p>' );
                }
                if(subText == ''){
                    jQuery('.answer').append( '<p>Please type subject</p>' );
                }
                if(areText == ''){
                    jQuery('.answer').append( '<p>Please type mail content</p>' );
                }
            }

        });

    /*Settings page*/
    if(jQuery('.settings').length > 0){
        jQuery('#emailTitle').on('input',function(){
            var title =jQuery(this).val();
            console.log(title);
            jQuery('.previewTitle').text(title);
        });
        jQuery('#TitleColor').on('input',function(){
            var tColor =jQuery(this).val();
            jQuery('#preview .previewTitle').css({color:tColor});
        });
        jQuery('#BorderColor').on('input',function(){
            var bColor =jQuery(this).val();
            jQuery('#preview .previewMain').css({borderColor:bColor});
        });
        jQuery('#textColor').on('input',function(){
            var color =jQuery(this).val();
            jQuery('#preview .text').css({color:color});
        });
        jQuery('#BackgroundColor').on('input',function(){
            var bgColor =jQuery(this).val();
            jQuery('#preview .previewMain').css({backgroundColor:bgColor});
        });
        jQuery('#Signature').on('input',function(){
            var sig =jQuery(this).val();
            jQuery('#preview .SignatureType').text(sig);
        });
        jQuery('#SignatureColor').on('input',function(){
            var sigColor =jQuery(this).val();
            jQuery('#preview .SignatureType').css({color:sigColor});
        })
    }











    //scrollBar
        jQuery(".EmailBox .content").mCustomScrollbar({
            scrollButtons:{
                enable:false
            },
            theme:"3d-dark"
        });




    //disable right click
        document.oncontextmenu=new Function("return false");







    })
})( jQuery );
