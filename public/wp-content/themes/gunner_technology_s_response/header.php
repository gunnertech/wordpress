<?php

global $wp_query;
$author = isset($wp_query->post) ? get_userdata($wp_query->post->post_author) : false; ?>
<!doctype html>
<!--[if lt IE 7 ]> <html class="no-js ie6" lang="en" xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml"> <![endif]-->
<!--[if IEMobile 7 ]> <html class="no-js iem7" lang="en" xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml"> <![endif]-->
<!--[if IE 7 ]>    <html class="no-js ie7" lang="en" xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml"> <![endif]-->
<!--[if IE 8 ]>    <html class="no-js ie8" lang="en" xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html class="no-js" lang="en" xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml"> <!--<![endif]-->
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="HandheldFriendly" content="True" />
    <meta name="MobileOptimized" content="320" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="cleartype" content="on" />
    
    <?php if($author): ?>
      <meta name="author" content="<?php echo $author->user_nicename; ?>" />
    <?php endif; ?>
    
    <?php echo str_replace("null","",get_option('meta_tags',"")) ?>

    <title><?php wp_title(''); ?></title>
    
    <!-- For iPhone 4 with high-resolution Retina display: -->
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php bloginfo( 'template_url' ) ?>/images/apple-touch-icon-114x114-precomposed.png" />
    <!-- For first-generation iPad: -->
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php bloginfo( 'template_url' ) ?>/images/apple-touch-icon-72x72-precomposed.png" />
    <!-- For non-Retina iPhone, iPod Touch, and Android 2.1+ devices: -->
    <link rel="apple-touch-icon-precomposed" href="<?php bloginfo( 'template_url' ) ?>/images/apple-touch-icon-precomposed.png" />
    <!-- For nokia devices: -->
    <!--link rel="shortcut icon" href="<?php bloginfo( 'template_url' ) ?>/images/apple-touch-icon.png" /-->
    
    <!--[if (gt IE 8) | (IEMobile)]><!-->
    <link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?><?php echo defined('GT_ASSET_VERSION') ? ('?v='.GT_ASSET_VERSION) : '' ?>" />
    <!--<![endif]-->
    
    <!--[if (lt IE 9) & (!IEMobile)]>
    <link rel="stylesheet" href="<?php bloginfo( 'stylesheet_directory' ) ?>/ie.css<?php echo defined('GT_ASSET_VERSION') ? ('?v='.GT_ASSET_VERSION) : '' ?>" />
    <![endif]-->
    
    <?php do_action("hbgs_load_styles"); ?>
    
    <script>
      var g_hbgs_template_url = '<?php echo HBGS_IS_HTTPS_REQUEST ? preg_replace('/http:\/\//',"https://",get_bloginfo('template_url')) : get_bloginfo('template_url'); ?>';
    </script>
    <script src="<?php echo get_bloginfo('template_url') ?>/js/libs/modernizr.js"></script>
    <script>Modernizr.mq('(min-width:0)') || document.write('<script src="<?php echo get_bloginfo('template_url') ?>/js/libs/respond.min.js">\x3C/script>')</script>
    
    <?php      
      do_action("hbgs_load_scripts");
      do_action("hbgs_print_synchronous_scripts");
    ?>
    
    <?php get_template_part("_head_info") ?>
        
    <?php wp_head(); ?>
  </head>
  <body <?php body_class(hbgs_body_classes()) ?>>
    <div id="container">
      <header>
        <?php do_action("hbgs_pre_header_widgets") ?>
        <hgroup>
          <?php if(is_home() || is_front_page()): ?>
            <h1><a href="<?php echo home_url(); ?>/"><?php bloginfo('name'); ?></a></h1>
            <h4><a href="<?php echo home_url(); ?>/"><?php bloginfo('description'); ?></a></h4>
          <?php else: ?>
            <h4><a href="<?php echo home_url(); ?>/"><?php bloginfo('name'); ?></a></h4>
            <h6><a href="<?php echo home_url(); ?>/"><?php bloginfo('description'); ?></a></h6>
          <?php endif; ?>
        </hgroup>
        <?php do_action("hbgs_header_widgets") ?>
        <?php get_template_part( '_more_header' ) ?>
      </header>
      <div id="main" role="main">
        <!-- PRE CONTENT SIDEBARS -->
        <?php do_action("hbgs_pre_content_widgets") ?>
        <!--/ PRE CONTENT SIDEBARS -->
        <?php if ( function_exists('yoast_breadcrumb') ): ?>
        	<?php yoast_breadcrumb('<p id="breadcrumbs">','</p>') ?>
        <?php endif; ?>
