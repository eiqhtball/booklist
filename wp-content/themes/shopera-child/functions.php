<?php
add_action( 'wp_enqueue_scripts', 'shopera_child_enqueue' );
function shopera_child_enqueue() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}