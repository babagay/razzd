//images centering
function images(){
  if($('.video-li').length){
    var a = $('.video-li img');
    for(var i = 0; i < a.length; i++ ){
      $(a[i]).css({
        'opacity': '0',
        'width': '100%',
        'height': '100%'
      });
      var src = $(a[i]).attr('src');
      $(a[i]).parent().css({
        'height': $(a[i]).parent().outerWidth(),
        'background-image': 'url('+ src+')',
        'background-size': 'cover',
        '-moz-background-size': 'cover',
        'background-position': 'center'
      });
    }
    $(window).resize(function(){
      var c = $('.video-li img');
      for(var i = 0; i < c.length; i++ ){
        $(c[i]).parent().css({
          'height': $(c[i]).parent().outerWidth()
        });
    }
    });
  }
}
function getScrollBarWidth () {
    var $outer = $('<div>').css({visibility: 'hidden', width: 100, overflow: 'scroll'}).appendTo('body'),
        widthWithScroll = $('<div>').css({width: '100%'}).appendTo($outer).outerWidth();
    $outer.remove();
    return 100 - widthWithScroll;
};

jQuery(document).ready(function($){
    if(window.location.hash == '#login'){
        signinPopup();
    }
    
    $(document).on('click','#popupFogotPassword', function(){fogotPassword()});
 
                function fogotPassword() {
                    jQuery(".popup.sign-in-popup").removeClass("popup-opener");
                    jQuery(".popup.registr-popup").addClass("popup-opener");
                    jQuery.get('/site/fogot-password', function (data) {
                        jQuery(".popup.registr-popup .popup-block").html(data);
                    });
                }
                
                $(document).on("submit", '#password-recovery-form', function (e) {
                    e.preventDefault();
                         $.ajax({
                                type: 'POST',
                                url: $('#password-recovery-form').attr('action'),
                                data: $('#password-recovery-form').serialize(),
                                success: function (data) {                                 
                                        var h = $('#password-recovery-form', '<div>'+data+'</div>').html(); 
                                        $('#password-recovery-form').html(h); 
                                }
                            }); 
                });


    $("button.close").on("click", function () {
        $(this).closest(".alert").hide();
    });

    $('.video-preview').on('click', function () {
        var $this = $(this);
        var video = '<video src="' + $this.data('video') + '" autoplay="" preload="none" controls></video>';
        $this.hide();
        $this.after(video);
    });

    jQuery(".notifications .close").click(function () {
        $.get($(this).attr('href'), function () { });

        $(this).parent().parent().hide();

        var ind = 0;

        $(this).closest(".notifications").find(".notifications-list .notifications-li").each(function(){

            var display = $(this).css("display")
            if(display == "block")  ind ++;
        });

        if ( ind == 0 )
            $(this).closest(".notifications").html("No notifications")

        return false;
    });

images();
         if($(window).width() < (768 - getScrollBarWidth())){
          getScrollBarWidth ();
            $('.main-sidebar').slideUp(0);
         }
         if($('.full-height').length){
            $('.full-height').parent().css('height','100%');
            $('.full-height').parent().parent().css('height','100%');
            $('.full-height').parent().parent().parent().css('height','100%');
            $('.full-height').parent().parent().parent().parent().css('height','100%');
            $('#footer').css('margin-top', '-20px');
         }
                jQuery(".popup-click.sign-in-popup-click").click(function () {
                    signinPopup();
                });
                
                function signinPopup() {
                    jQuery(".popup.sign-in-popup").addClass("popup-opener");
                    jQuery.get('/site/login-ajax', function (data) {
                        jQuery(".popup.sign-in-popup .popup-block").html(data);
                        initRegister('#popupRegister');
                        jQuery(".popup.sign-in-popup .submit").click(function () {

                            $.ajax({
                                type: 'POST',
                                url: $('#login-form').attr('action'),
                                data: $('#login-form').serialize(),
                                success: function (data) {
                                    $(".field-login-form-login").removeClass("has-error")

                                    if (data.refresh) {
                                        location.reload();

                                    } else {

                                        var h = $('.inputs', data).html();

                                        $('#login-form .inputs').html(h);

                                        /** Костыли, чтобы в случае ошибки все поля были доступны для изменения */
                                        var helpMess = $(".help-block").text();

                                        if( helpMess != "" ) {
                                            if( $(".field-login-form-password").hasClass("has-error") ){
                                                $(".field-login-form-login").addClass("has-error")
                                            }
                                        }
                                    }
                                }
                            });
                            return false;
                        });

                    });
                }
                
		initRegister('.registr-sign-up .popup-click.register-popup-click');
                function initRegister(p){
                    
                    jQuery(p).click(function(){
                    jQuery(".popup.sign-in-popup").removeClass("popup-opener");
                    jQuery(".popup.registr-popup").addClass("popup-opener");
                    jQuery.get('/site/register-ajax',function(data){
                        jQuery(".popup.registr-popup .popup-block").html(data);
                        jQuery(".popup.registr-popup .submit").click(function(){
                            
                            $.ajax({
                               type: 'POST',
                               url: $('#registration-form').attr('action'),
                               data: $('#registration-form').serialize(),   
                               success: function(data){

                                   if (data.refresh) {
                                        location.reload();

                                    } else {

                                       var h = $('.inputs',data).html();

                                       $('#registration-form .inputs').html(h);

                                       var helpMessUsername = $(".field-register-form-username").find("div.help-block").text();
                                       var helpMessName     = $(".field-register-form-name").find("div.help-block").text();

                                       /** Костыли, чтобы в случае ошибки все поля были доступны для изменения */
                                       $(".field-register-form-name").removeClass("has-error")
                                       $(".field-register-form-username").removeClass("has-error")

                                       if(helpMessUsername != "")
                                           $(".field-register-form-username").addClass("has-error");

                                       if(helpMessName != "")
                                           $(".field-register-form-name").addClass("has-error")

                                       if( $(".field-register-form-email").hasClass("has-error")
                                           || $(".field-register-form-password").hasClass("has-error")
                                           || $(".field-register-form-passwordconfirm").hasClass("has-error")
                                           || $(".field-register-form-username").hasClass("has-error") ){
                                               $(".field-register-form-name").addClass("has-error")
                                           }

                                       if( $(".field-register-form-email").hasClass("has-error")
                                            || $(".field-register-form-password").hasClass("has-error")
                                            || $(".field-register-form-passwordconfirm").hasClass("has-error")
                                            || $(".field-register-form-name").hasClass("has-error")
                                           ){
                                               $(".field-register-form-username").addClass("has-error")
                                           }

                                       if( $(".field-register-form-username").hasClass("has-error") )
                                           $(".field-register-form-email").addClass("has-error")

                                       if( $(".field-register-form-passwordconfirm").hasClass("has-error") )
                                           $(".field-register-form-password").addClass("has-error")

                                       if( $(".field-register-form-password").hasClass("has-error") )
                                           $(".field-register-form-passwordconfirm").addClass("has-error")

                                    }
                               }
                            });
                            return false;
                        });
                       
                    });	
			
		});
                }
		$(document).on('click','.popup_bg, .close_btn',function(e){
                        e.preventDefault();
			jQuery(".popup").removeClass("popup-opener");
		});
		
		if(jQuery("#razz-type input:checked").val() == 2){
                        jQuery(".some-any-form").addClass("anyone");
			jQuery(".some-any-form").removeClass("someone");
			jQuery(".some-any-form .razz-info-tabs span").removeClass("active");
			jQuery('.some-any-form .razz-info-tabs .anyone').addClass("active");
                        
    }
		
		
		
		jQuery(".some-any-form .razz-info-tabs .someone").click(function(){
			jQuery(".some-any-form").addClass("someone");
			jQuery(".some-any-form").removeClass("anyone");
			jQuery(".some-any-form .razz-info-tabs >span").removeClass("active");
			jQuery(this).addClass("active");
                        jQuery('#razz-type input[value=1]').prop("checked",true);
		});
		
		jQuery(".some-any-form .razz-info-tabs .anyone").click(function(){
			jQuery(".some-any-form").addClass("anyone");
			jQuery(".some-any-form").removeClass("someone");
			jQuery(".some-any-form .razz-info-tabs span").removeClass("active");
			jQuery(this).addClass("active");
      jQuery('#razz-type input[value=2]').prop("checked",true);
		});
		
		jQuery(".some-any-form .input-add .email-search").click(function(){
			jQuery(".some-any-form .input-add .fb-friends-list").addClass("fade");
			jQuery(this).addClass("visible");
			jQuery(".some-any-form .input-section .input-add .f-ico").addClass("visible");
		});
		
		jQuery(".some-any-form .input-section .input-add .f-ico").click(function(){
			jQuery(".some-any-form .input-add .fb-friends-list").removeClass("fade");
			jQuery(".some-any-form .input-add .email-search").removeClass("visible");
			jQuery(".some-any-form .input-section .input-add .f-ico").removeClass("visible");
		});
		
		
		jQuery(".some-any-form .input-add #fb-frinds-search").click(function(){
			jQuery(".some-any-form #friends-list").toggleClass("active");
			jQuery(".some-any-form .input-add .email-search").toggleClass("fade");
			jQuery(this).toggleClass("active");
		});
		
		
		jQuery(".some-any-form .input-add #fb-frinds-search").click(function(){
			jQuery(".some-any-form .input-add .fb-friends-list").addClass("active");
		});
		
                
                jQuery(".filter-list select").change(function(){
			var q = jQuery(this).find(':selected').attr('data-query');
                        var l = document.location.pathname;
                        document.location.href = l+'?'+q;
		});
                
                jQuery(".categories a").click(function(){
                        var id = jQuery(this).attr('data-id');
                        var c = jQuery("#razz-category input[value="+id+"]");
                        $('.categories a').removeClass('active');
                        jQuery(this).toggleClass('active');
                        c.prop("checked",!c.prop("checked"));
                        return false;   
		});
                
                jQuery("#razz-category input").each(function(i){
                       var id = jQuery(this).prop("checked");
                       jQuery('.categories ul li a[data-id='+id+']').addClass('active'); 
		});
                
                if(jQuery("#razzsearch-t input[value=1]").prop("checked"))
                    jQuery('#toggle-respond').addClass('active');
                
                 if(jQuery("#razzsearch-t input[value=2]").prop("checked"))
                    jQuery('#toggle-vote').addClass('active');
                
                jQuery("#toggle-respond").click(function(){
                        var c = jQuery("#razzsearch-t input[value=1]");
			jQuery(this).toggleClass('active');
                        c.prop("checked",!c.prop("checked"));
                        return false;   
		});
                
                jQuery("#toggle-vote").click(function(){
                        var c = jQuery("#razzsearch-t input[value=2]");
			jQuery(this).toggleClass('active');
                        c.prop("checked",!c.prop("checked"));
                        return false;   
		});
                         		
	});
	

	jQuery("form.search input[type='text']").focus(function(){
			jQuery(".search-filter").addClass("active");
		});
    jQuery("body").on('click', function(e){
        if (!($(e.target).closest('.main_search').length)){
            jQuery(".search-filter").removeClass("active");
        }
        });
       $('.mobile_menu_switcher').click(function(e){
            e.preventDefault();
            $('.main-sidebar').slideToggle(200);
       }); 
       $(window).resize(function(){
         if($(window).width() < (768 - getScrollBarWidth())){
            $('.main-sidebar').slideUp(0);
         }else{
            $('.main-sidebar').slideDown(0);
         }
       });   

    //carousels
        if($('.related-videos .video-list').find('.crsl-item').length >=4){
            
        } else{
            $('#navbtns').hide();
            // $('.related-videos').hide();
        }
        $('.crsl-items .video-list').slick({
              slidesToShow: 3,
              slidesToScroll: 3,
              slide: '.crsl-item',
              prevArrow: '#navbtns .previous',
              nextArrow: '#navbtns .next',
               responsive: [
                  {
                    breakpoint: 1024,
                    settings: {
                      slidesToShow: 2,
                      slidesToScroll: 2
                    }
                  },
                  {
                    breakpoint: 768,
                    settings: {
                      slidesToShow: 1,
                      slidesToScroll: 1,
                      adaptiveHeight: true
                    }
                  }
                ]
            });
        if($('.vote-on-challenges .video-list').find('.crsl-item').length >=4){
          $('.crsl-items-voted .video-list').slick({
              slidesToShow: 3,
              slidesToScroll: 3,
              slide: '.crsl-item',
              prevArrow: '#vote-navbtns .previous',
              nextArrow: '#vote-navbtns .next',
              responsive: [
                  {
                    breakpoint: 1024,
                    settings: {
                      slidesToShow: 2,
                      slidesToScroll: 2
                    }
                  },
                  {
                    breakpoint: 768,
                    settings: {
                      slidesToShow: 1,
                      slidesToScroll: 1,
                      adaptiveHeight: true
                    }
                  }
                ]
           });
        } else{
          $('#vote-navbtns').hide();
          // $('.crsl-items-voted').hide();
        }
        if($('.crsl-items-respond .video-list').find('.crsl-item').length >=4){
          $('.crsl-items-respond .video-list').slick({
              slidesToShow: 4,
              slidesToScroll: 4,
              slide: '.crsl-item',
              prevArrow: '#respond-navbtns .previous',
              nextArrow: '#respond-navbtns .next',
              responsive: [
                  {
                    breakpoint: 1280,
                    settings: {
                      slidesToShow: 3,
                      slidesToScroll: 3,
                    }
                  },
                  {
                    breakpoint: 1024,
                    settings: {
                      slidesToShow: 2,
                      slidesToScroll: 2
                    }
                  },
                  {
                    breakpoint: 768,
                    settings: {
                      slidesToShow: 1,
                      slidesToScroll: 1,
                      adaptiveHeight: true
                    }
                  }
                ]
           });
        } else {
          $('#respond-navbtns').hide();
            // $('.respond-to-challenges').hide();
        }
        // //square prewiev on index
        // if ($('.main-video').length){
        //   var w = $('.visual-person').first().width();
        //   $('.main-video .visual-person').css('height', w);
        // }
        // //Square preview on carousels
        // if($('.related-videos .video-li').length){
        //   var w = $('.related-videos .visual-person').outerWidth();
        //   $('.related-videos .visual-person').css('height', w);
        // }
        // if($('.crsl-items-respond .video-li').length){
        //   var w = $('.crsl-items-respond .visual-person').outerWidth();
        //   $('.crsl-items-respond .visual-person').css('height', w);
        // }
        // if($('.crsl-items-voted .video-li').length){
        //   var w = $('.crsl-items-voted .visual-person').outerWidth();
        //   $('.crsl-items-voted .visual-person').css('height', w);
        // }
        // if($('.all-list .video-li').length){
        //   var w = $('.all-list .visual-person').outerWidth();
        //   $('.all-list .visual-person').css('height', w);
        // }
        // if( $('.respond_list .video-li').length ){
        //   var w = $('.respond_items_list .visual-person').outerWidth();
        //   $('.respond_items_list .visual-person').css('height', w);
        // }
        
        // $(window).resize(function(){
        //   if ($('.main-video').length){
        //     var w = $('.visual-person').first().width();
        //     $('.main-video .visual-person').css('height', w);
        //   }
        //   if($('.related-videos .video-li').length){
        //     var w = $('.related-videos .visual-person').width();
        //     $('.related-videos .visual-person').css('height', w);
        //   }
        //   if($('.crsl-items-respond .video-li').length){
        //     var w = $('.crsl-items-respond .visual-person').outerWidth();
        //     $('.crsl-items-respond .visual-person').css('height', w);
        //   }
        //   if($('.all-list .video-li').length){
        //     var w = $('.all-list .visual-person').outerWidth();
        //     $('.all-list .visual-person').css('height', w);
        //   }
        //   if($('.crsl-items-voted .video-li').length){
        //     var w = $('.crsl-items-voted .visual-person').outerWidth();
        //     $('.crsl-items-voted .visual-person').css('height', w);
        //   }
        //   if( $('.respond_list .video-li').length ){
        //     var w = $('.respond_items_list .visual-person').outerWidth();
        //     $('.respond_items_list .visual-person').css('height', w);
        //   }
        // });
        // $(document).ready(function(){
        //   //check images
        //   var images = $('.visual-person img');
        //   for(var i = 0; i < images.length; i++){
        //     var w = $(images[i]);
        //     w = w[0].width;
        //     var h = $(images[i]);
        //     h = h[0].height;
        //     if(w < h){
        //       $(images[i]).css({
        //         'width': '100%',
        //         'height': 'auto'
        //       });
        //     }
        //   }
        // });
$('img').load(function(){
  if(this.closest('.visual-person')){
    var w = this.naturalWidth;
    var h = this.naturalHeight;
    if (w < h) {
      $(this).css({
        'width': '100%',
        'height': 'auto'
      });
      $(this).addClass('vertical');
      $(this).siblings('video').css({
                                  'width': '100%',
                                  'height': 'auto'
                                });
    }
  }
});