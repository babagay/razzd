var Comments = [];
Comments.offset = 100;
Comments.sending = false;

$(document).ready(function () {
    
    Comments.init();

    $('.comments .top-arrow').click(function(){
        Comments.top();
        return false;
    });
    
    $('.comments .bottom-arrow').click(function(){
        Comments.bottom();
        return false;
    });
    
    $('.comment-form').submit(function(event){
       
        if(!Comments.sending) 
          
            Comments.send();
        
      
        return false;
    });
  
});

Comments.init = function(){
    Comments.selector = $('.comments');
    Comments.list = Comments.selector.find('.comments-list');
    Comments.form = Comments.selector.find('form');
    Comments.list.animate({ scrollTop: Comments.list[0].scrollHeight }, "slow");
}

Comments.top = function(){
    
    var o = Comments.list.scrollTop()-Comments.offset;
    if(o < 0) o = 0;
    
    Comments.list.animate({ scrollTop: o }, "slow");   
}

Comments.bottom = function(){
    
    var o = Comments.list.scrollTop()+Comments.offset;
    if(o > Comments.list[0].scrollHeight) o = Comments.list[0].scrollHeight;
    
    Comments.list.animate({ scrollTop: o }, "slow");   
}

Comments.send = function(){
    
    Comments.sending = true;
    
        $.ajax({
                type: 'POST',
                url: Comments.form.attr('action'),
                data: Comments.form.serialize(),   
                success: function(data){
                        var comments = $('.comments-list',data).html();
                        Comments.list.html(comments);
                        Comments.list.animate({ scrollTop: Comments.list[0].scrollHeight }, "slow");
                        Comments.form.find('#comments-comment').val('');
                        setTimeout(function(){ Comments.sending = false; }, 1000);
                          
                    }
                });
}