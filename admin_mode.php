<?php

function is_wpline_admin($user_post_id){
    $isdeleted = strip_tags(get_post_meta($user_post_id, "isdelete", true));
    $isadmin = strip_tags(get_post_meta($user_post_id, "isadmin", true));
    if($isadmin === "1" && $isdeleted === "0"){
        return true;
    }
    return false;
}

function search_user($senduser_id){
    $sendUser = array(
        "post_type" => "send_user",
        "orderby"	=> "date",
        "order"		=> "DESC"
    );
    $userdata = new WP_Query($sendUser);
    while($userdata->have_posts()) : $userdata->the_post();
        $id = get_the_ID();
        $user_id = strip_tags(get_post_meta($id, "user_id", true));
        if($user_id === $senduser_id){
            return $id;
        }
    endwhile;
    return false;
}

function admin_mode($user_post_id, $text){
    $mode = strip_tags(get_post_meta($user_post_id, "admin_mode", true));
    switch ($mode) {
        case 'admin_push':
            send_admin_push($text);
            $message = create_adminpush_reply_message_text();
            //file_put_contents('reply.txt', $message ."\r\n", FILE_APPEND | LOCK_EX);
            break;
        case 'nomarl':
            break;
        default:
            break;
    }
    return $message;
}

function create_adminpush_reply_message_text(){
    
    $response_format_text = [
    "type" => "text",
    "text" => ""
    ];
    
    $response_format_text["text"] = "メッセージを送付しました！";
    
    return $response_format_text;
}

function create_adminpush_send_message_text($text){
    
    $response_format_text = [
    "type" => "text",
    "text" => ""
    ];
    
    $response_format_text["text"] = $text;
    
    return $response_format_text;
}

function send_admin_push($text){
    
    $messages = create_adminpush_send_message_text($text);
    
    $sendUser = array(
        "post_type" => "send_user",
        "orderby"	=> "date",
        "order"		=> "DESC"
    );
    $userdata = new WP_Query($sendUser);
    while($userdata->have_posts()) : $userdata->the_post();
        $id = get_the_ID();
        $isdeleted = strip_tags(get_post_meta($id, "isdelete", true));
        if($isdeleted !== "1"){
            $user_id = strip_tags(get_post_meta($id, "user_id", true));
            push_message($user_id, $messages);
        }
    endwhile;
    
}

function push_message($id, $messages){
    
    $accessToken = get_option('line_accesstoken');

    if(isset($messages["type"])){
        $post_data = [
            "to" => $id,
            "messages" => [$messages]
        ];
    }else{
        $post_data =[
            "to" => $id,
            "messages" => [] 
        ];
        $post_data["messages"] = $messages;
    }    

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

    //file_put_contents('result.txt', $result ."\r\n", FILE_APPEND | LOCK_EX);
}