<?php
    /**
     * Формирует список твиттер-контактов в формате HTML
     */

    $friendsEmpty = true;
    $followersEmpty = true;

    $contacts = [];

    $link_friends_previous = $link_friends_next = $link_followers_previous = $link_followers_next = '';

    if(isset($friends['next_cursor']))
    if( (int)$friends['next_cursor'] > 0 ){
        $link_friends_next = '<li class="fb_u cursor"  data-entity="friends" data-cursor_type="next" data-cursor="'.$friends['next_cursor'].'">next page &#187;</li>';
    }

    if(isset($friends['previous_cursor']))
    if( (int)$friends['previous_cursor'] < 0 ){
        $link_friends_previous = '<li class="fb_u cursor"  data-entity="friends" data-cursor_type="previous" data-cursor="'.$friends['previous_cursor'].'">&#171; previous page</li>';
    }

    if(isset($followers['next_cursor']))
    if( (int)$followers['next_cursor'] > 0 ){
        $link_followers_next = '<li class="fb_u cursor" data-entity="followers" data-cursor_type="next" data-cursor="'.$followers['next_cursor'].'">  next page</li>';
    }

    if(isset($followers['previous_cursor']))
    if( (int)$followers['previous_cursor'] < 0 ){
        $link_followers_previous = '<li class="fb_u cursor" data-entity="followers" data-cursor_type="previous" data-cursor="'.$followers['previous_cursor'].'">  previous page</li>';
    }

    if(isset($friends['users']))
        if(sizeof($friends['users'])) {
            $contacts = array_merge($contacts, $friends['users']);
            $friendsEmpty = false;
        }

    if(isset($followers['users']))
        if(sizeof($followers['users'])) {
            $contacts = array_merge($contacts, $followers['users']);
            $followersEmpty = false;
        }

    if(!$friendsEmpty AND !$followersEmpty)
        $contacts = \common\helpers\Twitter::friendsArrayUnique($contacts);

    if($link_friends_previous == '' AND $link_friends_next != '')
        $link_friends_previous = '<li class="cursor empty">.</li>';

    if($link_friends_previous != '' AND $link_friends_next == '')
        $link_friends_next = '<li class="cursor empty">.</li>';

    if($link_followers_previous == '' AND $link_followers_next != '')
        $link_followers_previous = '<li class="cursor empty">.</li>';

    if($link_followers_previous != '' AND $link_followers_next == '')
        $link_followers_next = '<li class="cursor empty">.</li>';

    if(sizeof($contacts)){

        echo $link_friends_previous;
        echo $link_friends_next;
        echo $link_followers_previous;
        echo $link_followers_next;

        foreach($contacts as $item){
            echo "<li class=\"fb_u\" data-id=\"{$item['id']}\" data-name=\"{$item['name']}\" data-screen_name=\"{$item['screen_name']}\"> <img src=\"{$item['profile_image_url']}\">  {$item['name']}</li>";
        }

        echo $link_friends_previous;
        echo $link_friends_next;
        echo $link_followers_previous;
        echo $link_followers_next;
    }
?>
