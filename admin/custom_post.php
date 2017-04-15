<?php

add_action( 'init', 'createLineCustomPost' );

function createLineCustomPost(){
/*Reply Rule の設定*/
register_post_type('reply_rule', array(
'label' => 'Reply Rule',
'public' => true,
'has_archive' => true,
'supports' => array('title','editor','revisions','thumbnail',),
'labels' => array (
    'name' => 'Reply Rule',
    'singular_name' => 'Reply Rule',
    'menu_name' => 'Reply Rule',
    'add_new' => 'add Reply Rule',
    'add_new_item' => 'add Reply Rule',
    'edit' => 'update',
    'edit_item' => 'update Reply Rule',
    'new_item' => 'add Reply Rule',
    'view' => 'display Reply Rule',
    'view_item' => 'display Reply Rule',
    'search_items' => 'search Reply Rule',
    'not_found' => 'not found',
    'not_found_in_trash' => 'not found',
    'parent' => 'present',
),) );

/*Reply Rule分類 (replytype) の設定*/
register_taxonomy('replytype',array (
0 => 'replytype',
),array(
    'hierarchical' => true,
    'label' => 'type of Reply Rule',
    'rewrite' => array('slug' => 'replytype'),
    'singular_label' => 'Reply Rule'
) );

/*Push Message の設定*/
register_post_type('push_message', array(
'label' => 'Push Message',
'public' => true,
'has_archive' => true,
'supports' => array('title','editor','revisions','thumbnail',),
'labels' => array (
    'name' => 'Push Message',
    'singular_name' => 'Push Message',
    'menu_name' => 'Push Message',
    'add_new' => 'create a new Push Message',
    'add_new_item' => 'create a new Push Message',
    'edit' => 'update',
    'edit_item' => 'resend Push Message',
    'new_item' => 'create a new Push Message',
    'view' => 'display Push Message',
    'view_item' => 'display Push Message',
    'search_items' => 'search Push Message',
    'not_found' => 'not found',
    'not_found_in_trash' => 'not found',
    'parent' => 'present',
),) );

/*Push Message分類 (pushtype) の設定*/
register_taxonomy('pushtype',array (
0 => 'pushtype',
),array(
    'hierarchical' => true,
    'label' => 'type of Push Message',
    'rewrite' => array('slug' => 'pushtype'),
    'singular_label' => 'Push Message'
) );

/*Send User の設定*/
register_post_type('send_user', array(
'label' => 'Send User',
'public' => true,
'has_archive' => true,
'supports' => array('title','editor','revisions','thumbnail',),
'labels' => array (
    'name' => 'Send User',
    'singular_name' => 'Send User',
    'menu_name' => 'Send User',
    'add_new' => 'add Send User',
    'add_new_item' => 'add Send User',
    'edit' => 'update',
    'edit_item' => 'update Send User',
    'new_item' => 'add Send User',
    'view' => 'display Send User',
    'view_item' => 'display Send User',
    'search_items' => 'search Send User',
    'not_found' => 'not found',
    'not_found_in_trash' => 'not found',
    'parent' => 'present',
),) );

/*Send User分類 (usertype) の設定*/
register_taxonomy('usertype',array (
0 => 'usertype',
),array(
    'hierarchical' => true,
    'label' => 'type of Send User',
    'rewrite' => array('slug' => 'usertype'),
    'singular_label' => 'Send User'
) );

flush_rewrite_rules();
}