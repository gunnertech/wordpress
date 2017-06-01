<?php
/**
 * The main template file.
 *
 * This is the catch-all template for displaying information
 *
 * @package GunnerTechnology
 * @subpackage HTML5 Boilerplated Grid System
 * @since HTML5 Boilerplated Grid System 1.0
 */
 
  global $loop_template;
  $extra = is_category() ? 'category' : (isset($loop_template) ? $loop_template : "");
  
    get_header(); ?>
      <?php get_template_part( 'loop', $extra ) ?>
    <?php get_footer(); ?>