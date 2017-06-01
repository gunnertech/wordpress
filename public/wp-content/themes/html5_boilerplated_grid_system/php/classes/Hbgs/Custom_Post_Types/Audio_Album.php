<?php

/**
 *
 * @category   Hbgs
 * @package    Hbgs_Custom_Post_Type
 * @copyright  Copyright (c) 2010 Gunner Technolgoy Inc. (http://www.gunnertech.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    1.0.0: Audio_Album.php 2010-11-18 codyswann
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/wp-content/themes/html5_boilerplated_grid_system/classes/Hbgs/Custom_Post_Type.php';

class Hbgs_Custom_Post_Types_Audio_Album extends Hbgs_Custom_Post_Type {
  function __construct() {
    $this->taxonomies = array(
      array(
        'internal_name' => "genre",
        'label' => "Genre",
        'query_var' => true,
        'rewrite' => true,
        'hierarchical' => true,
        'labels' => array(
          'name' => "Audio Genre",
          'singular_name' => _x('Audi Genre', 'genre_singular_name'),
          'choose_from_most_used' => _x('Choose from the most used genres', 'genre_choose_from_most_used'),
          'add_or_remove_items' => _x('Add or remove genres', 'genre_add_or_remove_genres'),
        )
      )
    );
    parent::__construct();
  }
  
}