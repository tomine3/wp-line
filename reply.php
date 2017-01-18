<?php

require_once( '../../../' . '/wp-load.php' );

//ライセンストークンの取得
$accessToken = get_option('line_accesstoken');

//ユーザーからのメッセージ取得
$json_string = file_get_contents('php://input');

$jsonObj = json_decode($json_string);

$type = $jsonObj->{"events"}[0]->{"message"}->{"type"};
//メッセージ取得
$text = $jsonObj->{"events"}[0]->{"message"}->{"text"};
//ReplyToken取得
$replyToken = $jsonObj->{"events"}[0]->{"replyToken"};

//返信データ作成
$response_format_text = [
    "type" => "text",
    "text" => "aa"
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

$buttons_actions_element = [
    "type" => "postback",
    "label" => "Buy",
    "data" => "action=buy&itemid=123"
    ];

$buttons_actions = [];

array_push($buttons_actions,$buttons_actions_element);

$response_format_template_buttons = [
    "type" => "template",
    "altText" => "this is a buttons template",
    "template" => [
        "type" => "buttons",
        "thumbnailImageUrl" => "https://example.com/bot/images/image.jpg",
        "title" => "Menu",
        "text" => "Please select",
        "actions" => $buttons_actions
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

//カスタム投稿タイプ「Reply Rule」から返信データを抽出
$reply = new WP_Query($replyRule);
while($reply->have_posts()) : $reply->the_post();
    if(get_the_title() == $text){
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
                $response_format_image["originalContentUrl"] = strip_tags(get_post_meta($post->ID, image_originalcontenturl, true));
                $response_format_image["previewImageUrl"] = strip_tags(get_post_meta($post->ID, image_previewimageurl, true));
                $post_data = [
                    "replyToken" => $replyToken,
                    "messages" => [$response_format_image]
                    ];
                break;
            case 'video':
                $response_format_video["originalContentUrl"] = strip_tags(get_post_meta($post->ID, video_originalcontenturl, true));
                $response_format_video["previewImageUrl"] = strip_tags(get_post_meta($post->ID, video_previewimageurl, true));
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
                $post_data = [
                    "replyToken" => $replyToken,
                    "messages" => [$response_format_template_buttons]
                    ];
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