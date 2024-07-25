<?php

function ibento_change_default_post_menu_icon() {
    global $menu;
    foreach ( $menu as $key => $value ) {
        if ( 'edit.php' == $value[2] ) {
            $menu[$key][6] = 'dashicons-welcome-write-blog';
        }
    }
}
add_action( 'admin_menu', 'ibento_change_default_post_menu_icon' );
