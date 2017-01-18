<?php

add_action( 'init', 'createLineCustomPost' );

function createLineCustomPost(){
/*NEWS (news) の設定*/
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

/*NEWS分類 (newstype) の設定*/
register_taxonomy('replytype',array (
0 => 'news',
),array(
    'hierarchical' => true,
    'label' => 'type of Reply Rule',
    'rewrite' => array('slug' => 'replytype'),
    'singular_label' => 'Reply Rule'
) );

flush_rewrite_rules();
}