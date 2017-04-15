<?php
/*
Plugin Name: WP LINE
Plugin URI: http://www.example.com/plugin
Description: WP LINE
Author: my name
Version: 0.1
Author URI: http://www.example.com
*/

include('admin/custom_field.php');
include('admin/custom_post.php');
include('admin/custom_admin_menu.php');
include('push.php');

add_action( 'admin_menu', 'wpline_menu' );

function wpline_menu() {
    add_menu_page(
        'LINE MENU',
        'LINE MENU',
        'administrator',
        'setting_linetoken',
        'setting_linetoken'
    );
}

function setting_linetoken(){
    
    if(isset($_POST[accesstoken])){
        if(get_option('line_accesstoken') === false ){
            add_option( 'line_accesstoken', $_POST[accesstoken] );
            $message = "Regist Token !";
        }
        else{
            update_option( 'line_accesstoken', $_POST[accesstoken] );
            $message = "Update Token !";
        }
        $token = $_POST[accesstoken];
    }
    else{
        $token = get_option('line_accesstoken');
    }
    
    ?>
    <h1>Setting Line Token</h1>
    <div>
        <p>Your Webhook URL is <?php echo plugins_url() . "/" . "reply.php"; ?></p>
        <p>Please setting your Channel Access Token.</p>
        <form method="post">
            <p><textarea name="accesstoken" cols="50" rows="6"><?php echo $token; ?></textarea></p>
            <p><input type="submit" name="TokenRegister" value="登録"></p>
        </form>
    </div>
    <div>
        <p><?php echo $message ?></p>
    </div>
    <?php
}