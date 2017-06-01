<?php

/**
 *
 * @category   Hbgs
 * @package    Hbgs_Custom_Post_Type
 * @copyright  Copyright (c) 2010 Gunner Technolgoy Inc. (http://www.gunnertech.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    1.0.0: Event.php 2010-11-18 codyswann
 */

class Hbgs_Custom_Post_Types_Event extends Hbgs_Custom_Post_Type {
  function __construct() {
    global $post;
    
    if(isset($post)) {
      add_filter('gettext', array($this,"translation_mangler"), 10, 4);
    }
    
    add_filter('the_posts', array($this,"show_scheduled_posts"));
    
    parent::__construct();
  }
  
  function show_scheduled_posts($posts) {
     global $wp_query, $wpdb;
     
     if($wp_query->query_vars['post_type'] == 'event' && is_single() && $wp_query->post_count == 0) {
       $posts = $wpdb->get_results($wp_query->request);
     }
     
     return $posts;
  }
  
  
  function translation_mangler($translation, $text, $domain) {
    global $post;
    
    if ($post->post_type == 'event') {
      $translations = &get_translations_for_domain( $domain);
  		if ( $text == 'Scheduled for: <b>%1$s</b>') {
        return $translations->translate( 'Event Date: <b>%1$s</b>' );
      } 
      
      if ( $text == 'Published on: <b>%1$s</b>') {
  			return $translations->translate( 'Event Date: <b>%1$s</b>' );
  		} 
  		
  		if ( $text == 'Publish <b>immediately</b>') {
  			return $translations->translate( 'Event Date: <b>%1$s</b>' );
  		}
		}
		
		return $translation;
	}
}