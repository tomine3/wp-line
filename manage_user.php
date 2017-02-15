<?php


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