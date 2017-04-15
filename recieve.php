<?php

require_once('reply.php');
require_once('manage_user.php');
require_once( '../../../' . '/wp-load.php' );

recieve_main_proc();

function recieve_main_proc(){
    
    $jsonObj = recieve_line_response();
    
    $type = $jsonObj->{"events"}[0]->{"message"}->{"type"};
    $replyToken = $jsonObj->{"events"}[0]->{"replyToken"};
    $event_type = $jsonObj->{"events"}[0]->{"type"};
    
    switch ($event_type) {
        case 'message':
            $text = $jsonObj->{"events"}[0]->{"message"}->{"text"};
            reply_main_proc($text, $type, $replyToken, $event_type);
            break;
        case 'join':
        case 'follow':
            if($jsonObj->{"events"}[0]->{"source"}->{"type"} === "user")
                registId($jsonObj->{"events"}[0]->{"source"}->{"userId"}, $jsonObj->{"events"}[0]->{"source"}->{"type"}, $event_type = $jsonObj->{"events"}[0]->{"timestamp"});
            elseif($jsonObj->{"events"}[0]->{"source"}->{"type"} === "group")
                registId($jsonObj->{"events"}[0]->{"source"}->{"groupId"}, $jsonObj->{"events"}[0]->{"source"}->{"type"}, $event_type = $jsonObj->{"events"}[0]->{"timestamp"});
            elseif($jsonObj->{"events"}[0]->{"source"}->{"type"} === "room")
                registId($jsonObj->{"events"}[0]->{"source"}->{"roomId"}, $jsonObj->{"events"}[0]->{"source"}->{"type"}, $event_type = $jsonObj->{"events"}[0]->{"timestamp"});
            break;
        case 'leave':
        case 'unfollow':
            if($jsonObj->{"events"}[0]->{"source"}->{"type"} === "user")
                deleteId($jsonObj->{"events"}[0]->{"source"}->{"userId"}, $jsonObj->{"events"}[0]->{"source"}->{"type"}, $event_type = $jsonObj->{"events"}[0]->{"timestamp"});
            elseif($jsonObj->{"events"}[0]->{"source"}->{"type"} === "group")
                registId($jsonObj->{"events"}[0]->{"source"}->{"groupId"}, $jsonObj->{"events"}[0]->{"source"}->{"type"}, $event_type = $jsonObj->{"events"}[0]->{"timestamp"});
            elseif($jsonObj->{"events"}[0]->{"source"}->{"type"} === "room")
                registId($jsonObj->{"events"}[0]->{"source"}->{"roomId"}, $jsonObj->{"events"}[0]->{"source"}->{"type"}, $event_type = $jsonObj->{"events"}[0]->{"timestamp"});
            break;
        default:
            // code...
            break;
    }
    
}

function recieve_line_response(){

    //ユーザーからのメッセージ取得
    $json_string = file_get_contents('php://input');
    
    //file_put_contents('user.txt', "update" ."\r\n", FILE_APPEND | LOCK_EX);
    
    $jsonObj = json_decode($json_string);
    
    return $jsonObj;

}