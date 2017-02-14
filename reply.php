<?php

define("MAX_ACTION_NUM", 3);
define("MAX_COLUMNS_NUM", 1);

require_once( '../../../' . '/wp-load.php' );

//ライセンストークンの取得
$accessToken = get_option('line_accesstoken');

//ユーザーからのメッセージ取得
$json_string = file_get_contents('php://input');
//file_put_contents('user.txt', "update" ."\r\n", FILE_APPEND | LOCK_EX);

$jsonObj = json_decode($json_string);

$type = $jsonObj->{"events"}[0]->{"message"}->{"type"};
//メッセージ取得
$text = $jsonObj->{"events"}[0]->{"message"}->{"text"};
//ReplyToken取得
$replyToken = $jsonObj->{"events"}[0]->{"replyToken"};

$event_type = $jsonObj->{"events"}[0]->{"type"};

//返信データ作成
$response_format_text = [
    "type" => "text",
    "text" => $event_type
    ];

$response_format_image = [
    "type" => "image",
    "originalContentUrl" => $text,
    "previewImageUrl" => ""
    ];

$response_format_video = [
    "type" => "video",
    "originalContentUrl" => $text,
    "previewImageUrl" => ""
    ];

$response_format_location = [
    "type" => "location",
    "title" => "",
    "address" => "",
    "latitude" => "",
    "longitude" => ""
    ];

$response_format_sticker = [
    "type" => "sticker",
    "packageId" => "",
    "stickerId" => "",
    ];

$actions_postback_element = [
    "type" => "postback",
    "label" => "Buy",
    "data" => "action=buy&itemid=123",
    "text" => "aaa"
    ];

$actions_uri_element = [
    "type" => "uri",
    "label" => "Buy",
    "text" => ""
    ];

$actions_message_element = [
    "type" => "message",
    "label" => "Buy",
    "uri" => ""
    ];

$template_actions = [];

$response_format_template_buttons = [
    "type" => "template",
    "altText" => "test",
    "template" => [
        "type" => "buttons",
        "thumbnailImageUrl" => "https://example.com/bot/images/image.jpg",
        "title" => "Menu",
        "text" => "Please select",
        "actions" => $template_actions
        ]
    ];

$clumns_element = [
        "thumbnailImageUrl" => "https://example.com/bot/images/image.jpg",
        "title" => "Menu",
        "text" => "Please select",
        "actions" => $template_actions
    ];

$template_clumns = [];


$response_format_template_carousel = [
    "type" => "template",
    "altText" => "test",
    "template" => [
        "type" => "carousel",
        "columns" => $template_clumns
        ]
    ];

$post_data = [
    "replyToken" => $replyToken,
    "messages" => [$response_format_text]
    ];

$replyRule = array(
    "post_type" => "reply_rule",
    "orderby"	=> "date",
    "order"		=> "DESC"
);

if ($event_type === "join" || $event_type === "follow"){
    registId($jsonObj->{"events"}[0]->{"source"}->{"userId"}, $jsonObj->{"events"}[0]->{"source"}->{"type"}, $event_type = $jsonObj->{"events"}[0]->{"timestamp"});
}
else if( $event_type === "leave" || $event_type === "unfollow"){
    deleteId($jsonObj->{"events"}[0]->{"source"}->{"userId"}, $jsonObj->{"events"}[0]->{"source"}->{"type"}, $event_type = $jsonObj->{"events"}[0]->{"timestamp"});
}

//カスタム投稿タイプ「Reply Rule」から返信データを抽出
$reply = new WP_Query($replyRule);
while($reply->have_posts()) : $reply->the_post();
    $matcing_type = strip_tags(get_post_meta($post->ID, matching_type, true));
    $title = get_the_title();
    $ismatch = matching_type_check($matcing_type, $title, $text);
    //if($title === $text){
    if($ismatch === true){
        $reply_type = get_post_meta($post->ID, reply_type, true);
        switch ($reply_type) {
            case 'text':
                $response_format_text["text"] = strip_tags(get_post_meta($post->ID, text, true));
                $post_data = [
                    "replyToken" => $replyToken,
                    "messages" => [$response_format_text]
                    ];
                break;
            case 'image':
                $response_format_image["originalContentUrl"] = strip_tags(get_post_meta($post->ID, originalcontenturl, true));
                $response_format_image["previewImageUrl"] = strip_tags(get_post_meta($post->ID, previewimageurl, true));
                $post_data = [
                    "replyToken" => $replyToken,
                    "messages" => [$response_format_image]
                    ];
                break;
            case 'video':
                $response_format_video["originalContentUrl"] = strip_tags(get_post_meta($post->ID, originalcontenturl, true));
                $response_format_video["previewImageUrl"] = strip_tags(get_post_meta($post->ID, previewimageurl, true));
                $post_data = [
                    "replyToken" => $replyToken,
                    "messages" => [$response_format_video]
                    ];
                break;
            case 'location':
                $response_format_location["title"] = strip_tags(get_post_meta($post->ID, location_title, true));
                $response_format_location["address"] = strip_tags(get_post_meta($post->ID, address, true));
                $response_format_location["latitude"] = strip_tags(get_post_meta($post->ID, latitude, true));
                $response_format_location["longitude"] = strip_tags(get_post_meta($post->ID, longitude, true));
                $post_data = [
                    "replyToken" => $replyToken,
                    "messages" => [$response_format_location]
                    ];
                break;
            case 'sticker':
                $response_format_sticker["packageId"] = strval(strip_tags(get_post_meta($post->ID, packageid, true)));
                $response_format_sticker["stickerId"] = strval(strip_tags(get_post_meta($post->ID, stickerid, true)));
                $post_data = [
                    "replyToken" => $replyToken,
                    "messages" => [$response_format_sticker]
                    ];
                break;
            case 'template':
                //$response_format_sticker["packageId"] = strval(strip_tags(get_post_meta($post->ID, packageid, true)));
                //$response_format_sticker["stickerId"] = strval(strip_tags(get_post_meta($post->ID, stickerid, true)));
                $template_type = get_post_meta($post->ID, template_type, true);
                $response_format_template_buttons["altText"] = strval(strip_tags(get_post_meta($post->ID, alttext, true)));
                $response_format_template_buttons["template"]["type"] = $template_type;
                switch ($template_type) {
                    case 'buttons':
                        $response_format_template_buttons["template"]["thumbnailImageUrl"] = strval(strip_tags(get_post_meta($post->ID, thumbnailimageurl, true)));
                        $response_format_template_buttons["template"]["title"] = strval(strip_tags(get_post_meta($post->ID, template_buttons_title, true)));
                        $response_format_template_buttons["template"]["text"] = strval(strip_tags(get_post_meta($post->ID, template_buttons_text, true)));
                        for( $i = 1; $i <= MAX_ACTION_NUM; $i++){
                            $action_type = strval(strip_tags(get_post_meta($post->ID, actions_type.$i, true)));
                            $action_label = strval(strip_tags(get_post_meta($post->ID, action_label.$i, true)));
                            switch ($action_type) {
                                case 'postback':
                                    $actions_postback_element["label"] = $action_label;
                                    $actions_postback_element["data"] = strval(strip_tags(get_post_meta($post->ID, action_data.$i, true)));
                                    $actions_postback_element["text"] = strval(strip_tags(get_post_meta($post->ID, action_text.$i, true)));
                                    array_push($template_actions,$actions_postback_element);
                                    break;
                                case 'message' :
                                    $actions_message_element["label"] = $action_label;
                                    $actions_message_element["text"] = strval(strip_tags(get_post_meta($post->ID, action_text.$i, true)));
                                    array_push($template_actions,$actions_message_element);
                                    break;
                                case 'uri' :
                                    $actions_uri_element["label"] = $action_label;
                                    $actions_uri_element["uri"] = strval(strip_tags(get_post_meta($post->ID, action_uri.$i, true)));
                                    array_push($template_actions,$actions_uri_element);
                                    break;
                                default:
                                    break;
                            }
                        }
                        $response_format_template_buttons["template"]["actions"] = $template_actions;
                        $post_data = [
                            "replyToken" => $replyToken,
                            "messages" => [$response_format_template_buttons]
                            ];
                        break;
                    case 'carousel':
                        for( $j = 1; $j <= MAX_COLUMNS_NUM; $j++){
                            $template_actions = [];
                            $columns_id = intval(get_post_meta($post->ID, columns.$j , true));
                            if($columns_id !== 0){
                                for( $i = 1; $i <= MAX_ACTION_NUM; $i++){
                                    $action_type = strval(strip_tags(get_post_meta($columns_id, actions_type.$i, true)));
                                    $action_label = strval(strip_tags(get_post_meta($columns_id, action_label.$i, true)));
                                    switch ($action_type) {
                                        case 'postback':
                                            $actions_postback_element["label"] = $action_label;
                                            $actions_postback_element["data"] = strval(strip_tags(get_post_meta($columns_id, action_data.$i, true)));
                                            $actions_postback_element["text"] = strval(strip_tags(get_post_meta($columns_id, action_text.$i, true)));
                                            array_push($template_actions,$actions_postback_element);
                                            break;
                                        case 'message' :
                                            $actions_message_element["label"] = $action_label;
                                            $actions_message_element["text"] = strval(strip_tags(get_post_meta($columns_id, action_text.$i, true)));
                                            array_push($template_actions,$actions_message_element);
                                            break;
                                        case 'uri' :
                                            $actions_uri_element["label"] = $action_label;
                                            $actions_uri_element["uri"] = strval(strip_tags(get_post_meta($columns_id, action_uri.$i, true)));
                                            array_push($template_actions,$actions_uri_element);
                                            break;
                                        default:
                                            break;
                                    }
                                }
                                $clumns_element["actions"] = $template_actions;
                                $clumns_element["thumbnailImageUrl"] = strval(strip_tags(get_post_meta($columns_id, thumbnailimageurl, true)));
                                $clumns_element["title"] = strval(strip_tags(get_post_meta($columns_id, template_buttons_title, true)));
                                $clumns_element["text"] = strval(strip_tags(get_post_meta($columns_id, template_buttons_text, true)));
                                //$clumns_element["text"] = $action_type;
                                array_push($template_clumns,$clumns_element);
                            }
                        }
                        $response_format_template_carousel["template"]["columns"] = $template_clumns;
                        /*
                        $response_format_text = [
                        "type" => "text",
                        "text" => json_encode($response_format_template_carousel)
                        ];
                        
                        $post_data = [
                            "replyToken" => $replyToken,
                            "messages" => [$response_format_text]
                        ];
                        */
                        $post_data = [
                            "replyToken" => $replyToken,
                            "messages" => [$response_format_template_carousel]
                            ];
                        
                        break;
                    default:
                        break;
                }
                break;
            default:
                break;
        }
    }
    //echo strip_tags(get_post_meta($post->ID, video_originalcontenturl, true));
    //echo strip_tags(get_post_meta($post->ID, video_previewimageurl, true));
endwhile;
wp_reset_postdata();

//メッセージ以外のときは何も返さず終了
if($type != "text"){
    exit;
}

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

function registId($user_id, $user_type, $timestamp){
    
    $exist_user_post_id = isUser($user_id, $user_type);
    wp_reset_postdata();
    
    if($exist_user_post_id !== false){
        $user_post = array(
            'post_type'     => 'send_user'
        );
        $update_id = wp_update_post( $user_post );
        if($update_id) {
            update_post_meta($update_id, 'time_stamp', $timestamp);
            update_post_meta($update_id, 'isdelete', false);
        }
    }
    else{
        $user_post = array(
            'post_title'    => $user_type.$timestamp,
            'post_status'   => 'publish',
            'post_author'   => 1,
            'post_type'     => 'send_user'
        );
        $insert_id = wp_insert_post( $user_post );
        if($insert_id) {
            update_post_meta($insert_id, 'user_type', $user_type);
            update_post_meta($insert_id, 'user_id', $user_id);
            update_post_meta($insert_id, 'time_stamp', $timestamp);
            update_post_meta($insert_id, 'isdelete', false);
        }
    }
}

function deleteId($user_id, $user_type, $timestamp){
    
    $exist_user_post_id = isUser($user_id, $user_type);
    wp_reset_postdata();
    
    file_put_contents('test.txt', 'ok', FILE_APPEND | LOCK_EX);
    if($exist_user_post_id !== false){
        $user_post = array(
            'post_type'     => 'send_user'
        );
        $update_id = wp_update_post( $user_post );
        
        if($update_id) {
            update_post_meta($update_id, 'time_stamp', $timestamp);
            update_post_meta($update_id, 'isdelete', true);
        }
    }
}

function isUser($user_id, $user_type){

    $sendUser = array(
        "post_type" => "send_user",
        "orderby"	=> "date",
        "order"		=> "DESC"
    );
    $userdata = new WP_Query($sendUser);
    while($userdata->have_posts()) : $userdata->the_post();
        $exist_user_id = strip_tags(get_post_meta(get_the_ID(), 'user_id', true));
        $exist_user_type = strip_tags(get_post_meta(get_the_ID(), 'user_type', true));
        if($exist_user_id === $user_id && $exist_user_type === $user_type){
            return get_the_ID();
        }
    endwhile;
    return false;
}