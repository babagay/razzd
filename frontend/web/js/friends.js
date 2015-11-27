var Friends = [];
Friends.send = [];
Friends.send.limit = 0;
Friends.send.offset = 0;
Friends.url = '/site/fb-friends-ajax';
Friends.buttonSearch = '';
Friends.friendsList = '';
Friends.friendsField = '';
Friends.friendSelected = '';
Friends.out = '';


$(document).ready(function () {

        Friends.init();

        /*
        Friends.buttonSearch.click(function(){
            Friends.reset();
            Friends.find();  
        });
         */
    
});
Friends.init = function(){
    Friends.buttonSearch = $("#fb-frinds-search");
    Friends.friendsList = $("#friends-list");
    Friends.friendsField = $("#razz-fb_friend");
    Friends.friendNameField = $("#razz-fbfriendname");
    Friends.friendSelected = $("#friend-selected");
    Friends.reset();
}

Friends.reset = function(){
    Friends.send.limit = 500;
    Friends.send.offset = 0;
}

Friends.find = function(){
    $.ajax({
                url: Friends.url+'?offset='+Friends.send.offset+'&limit='+Friends.send.limit,
                type: 'GET',
                success: function (data) {
                    var f = data.data;
                    Friends.out = '';
                    if(data.error){
                        Friends.out +='<li >';
                                Friends.out +='<a href="/user/security/auth?authclient=facebook">Connect Facebook</a>';
                        Friends.out +='</li>';
                        Friends.friendsList.html(Friends.out);
                    }else{
                         for(var i in f){
                            Friends.out +='<li class="fb_u" id='+f[i].id+' data-name="'+f[i].name+'">';
                                Friends.out +='<img src="//graph.facebook.com/'+f[i].id+'/picture">';
                                Friends.out +=f[i].name;
                           Friends.out +='</li>';
                        }
                        
                        Friends.next(f);
                        Friends.friendsList.html(Friends.out);
                        Friends.selectFriendInit();
                    }
        
                },
                
            });
}
Friends.next = function(data){
    if(data.length != Friends.send.limit)
        return;
    
    Friends.send.offset += Friends.send.limit;
    Friends.out +='<li class="next">';
        Friends.out += 'Next';
    Friends.out +='</li>';
    
}
Friends.selectFriendInit = function(){
    
        Friends.friendsList.find('li.fb_u').click(function(){
            var id = $(this).attr('id');
            var itm = $(this).clone();
            var name = $(this).data('name');
            Friends.friendsField.val(id);
            Friends.friendNameField.val(name);
            Friends.friendSelected.html(itm);
            Friends.friendsList.find('li').remove();
        });

        Friends.friendsList.find('li.next').click(function(){
        Friends.find();
    });
    
}
