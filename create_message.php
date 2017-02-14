<?php

define("MAX_ACTION_NUM", 3);
define("MAX_COLUMNS_NUM", 5);

function create_message($post_id){
    
    $reply_type = get_post_meta($post_id, 'reply_type', true);
    switch ($reply_type) {
        case 'text':
            $message = create_message_text($post_id);
            break;
        case 'image':
            $message = create_message_image($post_id);
            break;
        case 'video':
            $message = create_message_video($post_id);
            break;
        case 'location':
            $message = create_message_location($post_id);
            break;
        case 'sticker':
            $message = create_message_sticker($post_id);
            break;
        case 'template':
            $message = create_message_template($post_id);
            break;
    }
    wp_reset_postdata();
    
    return $message;
    
}

function create_message_text($post_id){
    
    $response_format_text = [
    "type" => "text",
    "text" => ""
    ];
    
    $response_format_text["text"] = strip_tags(get_post_meta($post_id, 'text', true));
    
    return $response_format_text;
}

function create_message_image($post_id){
    
    $response_format_image = [
    "type" => "image",
    "originalContentUrl" => "",
    "previewImageUrl" => ""
    ];
    
    $response_format_image["originalContentUrl"] = strip_tags(get_post_meta($post_id, 'originalcontenturl', true));
    $response_format_image["previewImageUrl"] = strip_tags(get_post_meta($post_id, 'previewimageurl', true));
    
    return $response_format_image;
}

function create_message_video($post_id){
    
    $response_format_video = [
    "type" => "video",
    "originalContentUrl" => "",
    "previewImageUrl" => ""
    ];
    
    $response_format_video["originalContentUrl"] = strip_tags(get_post_meta($post_id, 'originalcontenturl', true));
    $response_format_video["previewImageUrl"] = strip_tags(get_post_meta($post_id, 'previewimageurl', true));
    
    return $response_format_video;
}

function create_message_location($post_id){
    
    $response_format_location = [
    "type" => "location",
    "title" => "",
    "address" => "",
    "latitude" => "",
    "longitude" => ""
    ];
    
    $response_format_location["title"] = strip_tags(get_post_meta($post_id, 'location_title', true));
    $response_format_location["address"] = strip_tags(get_post_meta($post_id, 'address', true));
    $response_format_location["latitude"] = strip_tags(get_post_meta($post_id, 'latitude', true));
    $response_format_location["longitude"] = strip_tags(get_post_meta($post_id, 'longitude', true));
    
    return $response_format_location;
}

function create_message_sticker($post_id){
    
    $response_format_sticker = [
    "type" => "sticker",
    "packageId" => "",
    "stickerId" => "",
    ];
    
    $response_format_sticker["packageId"] = strval(strip_tags(get_post_meta($post_id, 'packageid', true)));
    $response_format_sticker["stickerId"] = strval(strip_tags(get_post_meta($post_id, 'stickerid', true)));
    
    return $response_format_sticker;
}

function create_message_template($post_id){
    
    $template_type = get_post_meta($post_id, 'template_type', true);
    switch ($template_type) {
        case 'buttons':
            $template = create_message_template_buttons($post_id);
            break;
        case 'carousel':
            $template = create_message_template_carousel($post_id);
            break;
    }
    
    $response_format_template = [
    "type" => "template",
    "altText" => "",
    "template" => $template
    ];
    
    $response_format_template["altText"] = strval(strip_tags(get_post_meta($post_id, 'alttext', true)));
    
    return $response_format_template;
}

function create_message_template_buttons($post_id){
    
    $actions = create_buttons_actions($post_id);
    
    $template = [
    "type" => "buttons",
    "thumbnailImageUrl" => "",
    "title" => "",
    "text" => "",
    "actions" => $actions
    ];
    
    $template["thumbnailImageUrl"] = strval(strip_tags(get_post_meta($post_id, 'thumbnailimageurl', true)));
    $template["title"] = strval(strip_tags(get_post_meta($post_id, 'template_buttons_title', true)));
    $template["text"] = strval(strip_tags(get_post_meta($post_id, 'template_buttons_text', true)));
    
    return $template;
}

function create_buttons_actions($post_id){
    
    $actions = [];
    
    $actions_postback_element = [
    "type" => "postback",
    "label" => "",
    "data" => "",
    "text" => ""
    ];

    $actions_message_element = [
    "type" => "message",
    "label" => "",
    "text" => ""
    ];

    $actions_uri_element = [
    "type" => "uri",
    "label" => "",
    "uri" => ""
    ];
    
    for( $i = 1; $i <= MAX_ACTION_NUM; $i++){
        $action_type = strval(strip_tags(get_post_meta($post_id, 'actions_type'.$i, true)));
        $action_label = strval(strip_tags(get_post_meta($post_id, 'action_label'.$i, true)));
        switch ($action_type) {
            case 'postback':
                $actions_postback_element["label"] = $action_label;
                $actions_postback_element["data"] = strval(strip_tags(get_post_meta($post_id, 'action_data'.$i, true)));
                $actions_postback_element["text"] = strval(strip_tags(get_post_meta($post_id, 'action_text'.$i, true)));
                array_push($actions,$actions_postback_element);
                break;
            case 'message' :
                $actions_message_element["label"] = $action_label;
                $actions_message_element["text"] = strval(strip_tags(get_post_meta($post_id, 'action_text'.$i, true)));
                array_push($actions,$actions_message_element);
                break;
            case 'uri' :
                $actions_uri_element["label"] = $action_label;
                $actions_uri_element["uri"] = strval(strip_tags(get_post_meta($post_id, 'action_uri'.$i, true)));
                array_push($actions,$actions_uri_element);
                break;
            default:
                break;
        }
    }
    
    return $actions;
    
}

function create_message_template_carousel($post_id){
    
    $clumns = create_carousel_clumns($post_id);
    
    $template = [
    "type" => "carousel",
    "columns"=> []
    ];
    
    $template["columns"]= $clumns;

    return $template;
}

function create_carousel_clumns($post_id){
    
    $clumns = [];
    
    $clumns_element = [
        "thumbnailImageUrl" => "",
        "title" => "",
        "text" => "",
        "actions" => []
    ];
    
    for( $j = 1; $j <= MAX_COLUMNS_NUM; $j++){
        $actions = [];
        $columns_id = intval(get_post_meta($post_id, 'columns'.$j , true));
        if($columns_id !== 0){
            $actions = create_buttons_actions($columns_id);
            $clumns_element["actions"] = $actions;
            $clumns_element["thumbnailImageUrl"] = strval(strip_tags(get_post_meta($columns_id, 'thumbnailimageurl', true)));
            $clumns_element["title"] = strval(strip_tags(get_post_meta($columns_id, 'template_buttons_title', true)));
            $clumns_element["text"] = strval(strip_tags(get_post_meta($columns_id, 'template_buttons_text', true)));
            array_push($clumns,$clumns_element);
        }
    }
    
    return $clumns;
}