<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package GunnerTechnology
 * @subpackage HTML5 Boilerplated Grid System
 * @since HTML5 Boilerplated Grid System 1.0
 */
 
global $wp_query;
$author = isset($wp_query->post) ? get_userdata($wp_query->post->post_author) : false; ?>
<!doctype html>
<!--[if lt IE 7 ]> <html <?php body_class(hbgs_body_classes("no-js ie6")) ?> lang="en" xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml"> <![endif]-->
<!--[if IE 7 ]>    <html <?php body_class(hbgs_body_classes("no-js ie7")) ?> lang="en" xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml"> <![endif]-->
<!--[if IE 8 ]>    <html <?php body_class(hbgs_body_classes("no-js ie8")) ?> lang="en" xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html <?php body_class(hbgs_body_classes("no-js")) ?> lang="en" xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml"> <!--<![endif]-->
  <head>
    <meta charset="utf-8">

    <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame 
       Remove this if you use the .htaccess -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <title><?php wp_title(''); ?></title>
    <?php if($author): ?>
	    <meta name="author" content="<?php echo $author->user_nicename; ?>">
	  <?php endif; ?>
    <?php echo str_replace("null","",get_option('meta_tags',"")) ?>

    <!-- Mobile viewport optimized: j.mp/bplateviewport -->
    <!--meta name="viewport" content="width=device-width, initial-scale=1.0"-->

    <link rel="shortcut icon" href="<?php echo hbgs_favicon_url() ?>">
    <link rel="apple-touch-icon" href="<?php echo get_option("apple_touch_url",get_bloginfo( 'template_url' ).'/images/apple-touch-icon.png') ?>">
    
    <!-- Uncomment if you are specifically targeting less enabled mobile browsers
    <link rel="stylesheet" media="handheld" href="css/handheld.css?v=2">  -->
    
    <script>
      var g_hbgs_template_url = '<?php echo gt_is_ssl() ? preg_replace('/http:\/\//',"https://",get_bloginfo('template_url')) : get_bloginfo('template_url'); ?>';
      var WPSC_GoldCart = {
      	thickboxFix: "1",
      	displayMode: "grid",
      	itemsPerRow: "3",
      	productListClass: "product_grid_display"
      };
    </script>
    <script src="<?php echo get_bloginfo('template_url') ?>/js/libs/modernizr.js<?php echo defined('GT_ASSET_VERSION') ? ('?v='.GT_ASSET_VERSION) : '' ?>"></script>
    <?php 
      wp_enqueue_script( 'jquery' );
      wp_enqueue_script( 'plugins', get_bloginfo('template_url').'/js/plugins.js', array('jquery'));
      wp_enqueue_script( 'script', get_bloginfo('template_url').'/js/script.js', array('plugins'));
      wp_enqueue_script( 'hyphenator', get_bloginfo('template_url').'/js/libs/hyphenator.js', array('script'));
      if(!gt_is_ssl()) {
        // wp_enqueue_script( 'eCSStender', 'http://cdn.ecsstender.org/lib/latest/min/eCSStender.js');
        // wp_enqueue_script( 'eCSStender', 'http://cdn.ecsstender.org/ext/CSS3-backgrounds-and-borders/latest/min/eCSStender.css3-backgrounds-and-borders.js',array('eCSStender'));
        // wp_enqueue_script( 'eCSStender', 'http://cdn.ecsstender.org/ext/CSS3-color/latest/min/eCSStender.CSS3-color.js',array('eCSStender'));
        // wp_enqueue_script( 'eCSStender', 'http://cdn.ecsstender.org/ext/CSS3-transforms/latest/min/eCSStender.CSS3-transforms.js',array('eCSStender'));
        // wp_enqueue_script( 'eCSStender', 'http://cdn.ecsstender.org/ext/CSS3-transitions/latest/min/eCSStender.CSS3-transitions.js',array('eCSStender'));
      }
      do_action("hbgs_load_scripts");
      do_action("hbgs_print_synchronous_scripts");
    ?>
    
    <?php
      wp_enqueue_style( 'grid', get_bloginfo( 'template_url' ).'/css/grids/'.get_option('grid_size',"960_24_10_10.css") );
      wp_enqueue_style( 'style', get_bloginfo( 'stylesheet_url' ) );
      do_action("hbgs_load_styles");
    ?>

    <!-- TODO: For the less-enabled mobile browsers like Opera Mini -->
    <!--link rel="stylesheet" media="handheld" href="css/handheld.css?v=1"-->
    
    <?php wp_head(); ?>
  </head>
  <body <?php body_class(hbgs_body_classes("clearfix")) ?>>
    <div id="container" class="clearfix container_24">
      <header class="clearfix grid_24 fullscreen header">
        <?php do_action("hbgs_pre_header_widgets") ?>
        <hgroup class="ir">
          <?php if(is_home() || is_front_page()): ?>
            <h1><a href="<?php echo home_url(); ?>/"><?php bloginfo('name'); ?></a></h1>
            <h4><a href="<?php echo home_url(); ?>/"><?php bloginfo('description'); ?></a></h4>
          <?php else: ?>
            <h4><a href="<?php echo home_url(); ?>/"><?php bloginfo('name'); ?></a></h4>
            <h6><a href="<?php echo home_url(); ?>/"><?php bloginfo('description'); ?></a></h6>
          <?php endif; ?>
        </hgroup>
        <?php do_action("hbgs_header_widgets") ?>
      </header>
      <div id="main" class="clearfix grid_24" role="main">
        <!-- PRE CONTENT SIDEBARS -->
        <?php do_action("hbgs_pre_content_widgets") ?>
        <!--/ PRE CONTENT SIDEBARS -->
        <?php if ( function_exists('yoast_breadcrumb') ): ?>
        	<?php yoast_breadcrumb('<p id="breadcrumbs">','</p>') ?>
        <?php endif; ?>
