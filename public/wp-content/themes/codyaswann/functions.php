<?php

add_action("hbgs_load_styles",function() {
});

add_action("hbgs_load_scripts",function() {
  wp_enqueue_script( 'twitter-bootstrap' );
  wp_enqueue_script( 'codyaswann-script', get_stylesheet_directory_uri().'/js/script.js', array('jquery'));
});
