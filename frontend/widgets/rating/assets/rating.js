/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

 $('form.ratings select').barrating('show', {

        showValues: true,
        onSelect:function(value, text) {

                var frm = $(this).parents('form.ratings');
              
                 $.ajax({
                            url: frm.attr('action'),
                            type: 'post',
                           // dataType: 'html',
                            data: frm.serialize(),
                            success: function(data){
                                        $('.br-widget').remove();
                                        $("#r1 .r1").text(data.my);
                                        $("#r2 .r2").text(data.responder);

                                        var my = data.my * 1;
                                        var responder = data.responder * 1;

                                        if( my === responder )
                                            $(".info-person .vote-info-txt").text('DRAW');
                                        else
                                            if(my > responder){
                                                $("#r1 .vote-info-txt").text('WINNING :)');
                                                $("#r2 .vote-info-txt").text('LOSING :(');
                                            } else
                                                if(my < responder){
                                                    $("#r2 .vote-info-txt").text('WINNING :)');
                                                    $("#r1 .vote-info-txt").text('LOSING :(');
                                                }
                                    }
                        });
        }
 });
 
$('form.ratings button').hide();
