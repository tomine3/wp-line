<?php

require_once('create_message.php');

add_action( 'save_post', 'push_main_proc', 10, 2);
function push_main_proc($post_id, $post){
    if(get_post_type($post_id) !== 'push_message'){
        return;
    }
    if(wp_is_post_revision($post_id)){
        return;
    }
    if($post->post_status == 'publish' || $post->post_status == 'inherit'){
        search_send_user($post_id);
    }
    remove_action( 'save_post', 'push_main_proc');
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

function send_push_message($type, $id, $messages){
    $accessToken = get_option('line_accesstoken');
    
/*
    $response_format_text = [
    "type" => "text",
    "text" => json_encode($messages)
    ];
    $post_data = [
        "to" => $id,
        "messages" => [$response_format_text]
    ];
*/

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
    
    file_put_contents('result.txt', $result ."\r\n", FILE_APPEND | LOCK_EX);
}