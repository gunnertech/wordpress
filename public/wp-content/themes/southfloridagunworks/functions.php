<?php

add_action("hbgs_load_styles",function() {
});

add_action("hbgs_load_scripts",function() {
  wp_enqueue_script( 'twitter-bootstrap');
  // wp_enqueue_script( 'noisy', 'http://cdnjs.cloudflare.com/ajax/libs/noisy/1.1/jquery.noisy.min.js', array('jquery'));
  wp_enqueue_script( 'theme-script', get_stylesheet_directory_uri().'/js/script.js', array('jquery'));
  
  wp_enqueue_style( 'theme-fonts', 'http://fonts.googleapis.com/css?family=Vast+Shadow');
});
