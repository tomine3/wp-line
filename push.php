<?php

add_action( 'transition_post_status', 'my_send_published_push', 10, 3);
function my_send_published_push($new_status, $old_status, $post){
    $success = "message";
    if($new_status == 'publish'){
        switch($old_status){
            case 'draft':
            case 'pending':
            case 'auto-draft':
            case 'future':
            case 'publish':
                search_send_user($post->ID);
                break;
            case 'private':
                break;
        }
    }
}

function search_send_user($post_id){
    $messages = create_message($post_id);
    $sendUser = array(
        "post_type" => "send_user",
        "orderby"	=> "date",
        "order"		=> "DESC"
    );
    $userdata = new WP_Query($sendUser);
    while($userdata->have_posts()) : $userdata->the_post();
        $isdeleted = strip_tags(get_post_meta(get_the_ID(), "isdelete", true));
        if($isdeleted !== true){
            $type = strip_tags(get_post_meta(get_the_ID(), "user_type", true));
            $id = strip_tags(get_post_meta(get_the_ID(), "user_id", true));
            send_push_message($type, $id, $messages);
        }
    endwhile;
}

function create_message($post_id){
    
    $response_format_text = [
    "type" => "text",
    "text" => $post_id
    ];
    
    return $response_format_text;
}

function send_push_message($type, $id, $messages){
    $accessToken = get_option('line_accesstoken');
    
    $post_data = [
        "to" => $id,
        "messages" => [$messages]
    ];
    
    $ch = curl_init("https://api.line.me/v2/bot/message/push");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json; charser=UTF-8',
        'Authorization: Bearer ' . $accessToken
        ));
    $result = curl_exec($ch);
    curl_close($ch);
}