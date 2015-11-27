jQuery(document).ready(function($){

    var video_box =  $('#video2');

    var initW_input = jQuery('.razz-info input[name=initW]');
    var initH_input = jQuery('.razz-info input[name=initH]');

    var src = video_box.closest(".razz-info").find("input[name=video_src]").val();

    var userId = jQuery('.razz-info').find('input[name=user_id]').val();

    var stored_initW_name = "respondPage_initW_" + userId + src;
    var stored_initH_name = "respondPage_initH_" + userId + src;
    var stored_angle_name = "respondPage_angle_" + userId + src;

    var initialWidth;
    var initialHeight;

    // Init box
    // if( localStorage.getItem(stored_initW_name) ){
    //     initialWidth = localStorage.getItem(stored_initW_name)
    //     video_box.width(initialWidth);
    // }
    // if( localStorage.getItem(stored_initH_name) ){
    //     initialHeight = localStorage.getItem(stored_initH_name);
    //     video_box.height(initialHeight);
    // }
    // if( localStorage.getItem(stored_angle_name)  ){
    //     video_box.css({
    //         'transform': 'rotate('+ localStorage.getItem(stored_angle_name)  +'deg)'
    //     });

    //     video_box.closest(".razz-info").find("input[name=rotary]").val( localStorage.getItem(stored_angle_name) )
    // }

    setTimeout(function(){
        var current_width = $("#video2").width();
        var current_height = $("#video2").height();




        // Проверить значения H, W и angle из localStorage
        if( !localStorage.getItem(stored_initW_name) ){
            initialWidth = current_width;
        }
        if( !localStorage.getItem(stored_initH_name) ){
            initialHeight = current_height;
        }


        $(initW_input).val(initialWidth)
        $(initH_input).val(initialHeight)

    },1500);

    jQuery('.razz-info .rotate')
        .on('click', function() {

            var angle = video_box.closest(".razz-info").find("input[name=rotary]").val();
            angle *= 1
            if(angle < 360) angle += 90
            else angle = 90;

            $(this).closest(".razz-info").find("input[name=rotary]").val(angle)

          //  video_box.css({
          //      'transform': 'rotate('+angle+'deg)'
          //  });

            var initWidth = jQuery('.razz-info').find('input[name=initW]').val();
            var initHeight = jQuery('.razz-info').find('input[name=initH]').val();

         //   video_box.width(initHeight);
         //   video_box.height(initWidth);

            jQuery('.razz-info').find('input[name=initW]').val(initHeight);
            jQuery('.razz-info').find('input[name=initH]').val(initWidth);

            // Cохранить значения H, W и angle в local_storage
            localStorage.setItem(stored_initW_name,initHeight)
            localStorage.setItem(stored_initH_name,initWidth)
            localStorage.setItem(stored_angle_name,angle)



            return false;
        });

});