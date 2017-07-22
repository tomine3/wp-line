<?php

require_once('create_message.php');
require_once('admin_mode.php');

function reply_main_proc($text, $type, $replyToken, $event_type, $user_id){

    $user_post_id = search_user($user_id);
    if(is_wpline_admin($user_post_id) === true){
        $messages = admin_mode($user_post_id, $text);
        
    }else{
        $messages = search_reply_rule($text, $event_type);
    }

    //メッセージ以外のときは何も返さず終了
    if($type != "text"){
        exit;
    
    }
    
    //$messages = search_reply_rule($text, $event_type);

    send_reply_message($replyToken, $messages, $type);
    
}

function search_reply_rule($text, $event_type){
    
    //カスタム投稿タイプ「Reply Rule」検索条件Array
    $replyRule = array(
        "post_type" => "reply_rule",
        "orderby"	=> "date",
        "order"		=> "DESC"
    );
    
    //カスタム投稿タイプ「Reply Rule」から返信データを抽出
    $reply = new WP_Query($replyRule);
    while($reply->have_posts()) : $reply->the_post();
        $matcing_type = strip_tags(get_post_meta(get_the_ID(), 'matching_type', true));
        $title = get_the_title();
        $ismatch = matching_type_check($matcing_type, $title, $text);
        if($ismatch === true){
            $messages = create_message(get_the_ID());
        }
    endwhile;
    wp_reset_postdata();

    return $messages;
}

function send_reply_message($replyToken, $messages, $type){

    //Reply用
    $post_data = [
        "replyToken" => $replyToken,
        "messages" => [$messages]
        ];
    
    //file_put_contents('reply.txt', $messages ."\r\n", FILE_APPEND | LOCK_EX);
    
    //ライセンストークンの取得
    $accessToken = get_option('line_accesstoken');
    
    $ch = curl_init("https://api.line.me/v2/bot/message/reply");
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

function matching_type_check($matcing_type, $title, $text){
    $title_array = [];
    $title_array = explode('#', $title);
    if($title_array === false){
        array_push($title_array, $title);
    }
    foreach ($title_array as $value) {
        if($matcing_type === 'f_match'){
            if($value === $text){
                return true;
            }
        }
        else if($matcing_type === 'p_match'){
            if(strpos($text, $value) !== false){
                return true;
            }
        }
    }
    return false;
}