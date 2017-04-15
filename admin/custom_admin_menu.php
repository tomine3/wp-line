<?php

add_action( 'admin_footer', 'change_publish_btn_name', 10);
function change_publish_btn_name() {
    // 公開（更新）ボタンを押すと確認ウィンドウが開く
    $screen = get_current_screen();
    if($screen->post_type == 'push_message' && is_admin() === true){
        echo $screen->post_type;
        echo '<script type="text/javascript"><!--
        var is_send_chkbox = document.getElementById("acf-field-is_send");
        var setButtonLable = function(f){
            document.getElementById("publish").value = (f || is_send_chkbox.checked) ? "Send Message" : "Store Only";
        };
        setButtonLable();
        is_send_chkbox.addEventListener("click", function() {
            setButtonLable(this.checked);
        });
        // --></script>';
    }
}