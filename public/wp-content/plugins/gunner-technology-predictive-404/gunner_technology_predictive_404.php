<?php
/*
Plugin Name: Predictive 404
Plugin URI: http://gunnertech.com/predictive-404
Description: Uses Levenshtein Distance to Find Most Likely Posts
Version: 0.1
Author: Cody Swann
Author URI: http://gunnertech.com/

Anyone is free to use this script for non-illegal reasons. Use at your own risk.
*/
       
function predictive404_redirect() {
  global $wpdb;
  
  if ( !is_404() ) { return; }

    $url = $_SERVER['REQUEST_URI'];
    if(stristr($url,"http")) {
      $p = parse_url($url);
      $url = $p['path'];
    }
    
    $results = array();
    $score = 10;
    $items = $wpdb->get_results( 
      "SELECT ID FROM $wpdb->posts WHERE post_status = 'publish'"
    );
    
    foreach ( $items as $item ) {
      $perm = get_permalink($item->ID);
      if(stristr($perm,"http")) {
        $p = parse_url($perm);
        $perm = $p['path'];
      }
      $results[$perm] = levenshtein($url,$perm);
    }
    
    $items = $wpdb->get_results( 
      "SELECT term_ID FROM $wpdb->terms"
    );
    
    foreach ( $items as $item ) {
      $perm = get_category_link( $item->term_ID );
      if(stristr($perm,"http")) {
        $p = parse_url($perm);
        $perm = $p['path'];
      }
      $results[$perm] = levenshtein($url,$perm);
    }
    
    asort($results);
    foreach($results as $k=>$v) {
      $correct = $k;
      $score = $v;
      break;
    }
    
    if($score<3) {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: $correct");
        exit;
    } else {
        return;
    }    

}

function predictive404_redirect_canonical_filter($redirect, $request) {
  if ( is_404() ) { return false; }
  return $redirect;
}


// Set up plugin

add_action( 'template_redirect', 'predictive404_redirect' );
add_filter( 'redirect_canonical', 'predictive404_redirect_canonical_filter', 10, 2 );