<?php
/*
add_action('admin_menu', 'add_linereply_field');

function add_linereply_field() {
  // add_meta_box(表示されるボックスのHTMLのID, ラベル, 表示する内容を作成する関数名, 投稿タイプ, 表示方法)
  add_meta_box( 'reply-type','Reply type', 'replytype_custom_field', 'post', 'advanced' );
  //add_meta_box( 'music-lyrics','歌詞', 'create_form_music_lyrics', 'page', 'normal' );
}

function replytype_custom_field(){
    global $post;
    
    $options = array('text','image','video');
    
    $replytype = get_post_meta($post->ID,'reply-type',true);
    
    echo '<label for="reply-type">Select reply type</label><br>';
    echo '<input type="radio" name="reply-type" value="'. esc_html($options[0]) .'" checked > '. $options[0] .' ';
    echo '<input type="radio" name="reply-type" value="'. esc_html($options[1]) .'" > '. $options[1] .' ';
    echo '<input type="radio" name="reply-type" value="'. esc_html($options[2]) .'" > '. $options[2] .' ';
}
*/