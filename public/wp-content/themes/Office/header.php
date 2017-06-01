<?php
/**
 * @package WordPress
 * @subpackage Office Theme
 */
global $data; //get theme options
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>

<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<!-- Mobile Specific
================================================== -->
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<!--[if lt IE 9]>
	<script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
<![endif]-->


<!-- Title Tag
================================================== -->
<title><?php wp_title(''); ?><?php if(wp_title('', false)) { echo ' |'; } ?> <?php bloginfo('name'); ?></title>


<!-- Favicon
================================================== -->
<?php if(!empty($data['custom_favicon'])) { ?><link rel="icon" type="image/png" href="<?php echo $data['custom_favicon']; ?>" /><?php } ?>


<!-- Main CSS
================================================== -->
<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_url'); ?>" />

<!--[if IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/css/ie8.css" media="screen" />
<![endif]-->

<!--[if IE 7]>
	<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/css/ie7.css" media="screen" />
<![endif]-->

<!-- WP Head
================================================== -->
<?php if ( is_single() || is_page() ) wp_enqueue_script( 'comment-reply' ); ?>
<?php wp_head(); ?>


<?php 
//show tracking code - header 
echo stripslashes($data['tracking_header']); 
?>

</head>


<!-- Begin Body
================================================== -->
<body <?php body_class(); ?>>

<?php if($data['disable_top_bar'] != 'disable') { ?>
<div id="top-bar" <?php if($data['top_bar_position'] == 'fixed') { echo 'class="top-bar-fixed"'; } ?>>

	<div id="top-bar-inner">
    
		<?php wp_nav_menu( array(
            'theme_location' => 'top_menu',
			'menu_class' => 'top-menu',
            'sort_column' => 'menu_order',
			'fallback_cb' => ''
        )); ?>
        
    <?php if(!empty($data['callout_link'])) { ?><a href="<?php echo $data['callout_link']; ?>" id="top-bar-callout" title="<?php echo $data['callout_text']; ?>" target="_<?php echo $data['callout_target']; ?>"><?php echo $data['callout_text']; ?></a><?php } ?>
    
    </div>
    <!-- /top-bar-inner -->
    
</div>
<!-- /top-bar -->
<?php } ?>

<div id="header" class="clearfix <?php if($data['top_bar_position'] == 'fixed' && $data['disable_top_bar'] != 'disable') { echo 'header-one'; } else { echo 'header-two';  } ?>">

            <div id="logo">
                <?php if($data['custom_logo'] !='') { ?>
                    <a href="<?php echo home_url(); ?>/" title="<?php bloginfo( 'name' ); ?>" rel="home"><img src="<?php echo $data['custom_logo']; ?>" alt="<?php bloginfo( 'name' ) ?>" /></a>
                <?php } else { ?>
                <?php if (is_front_page()) { ?>
                    <h1><a href="<?php echo home_url(); ?>/" title="<?php bloginfo( 'name' ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
                <?php } else { ?>
                    <h2><a href="<?php echo home_url(); ?>/" title="<?php bloginfo( 'name' ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h2>
                <?php } } ?>
            </div>
            <!-- /logo -->
            
			<ul id="social">
				<?php
                    $social_links = array('twitter','tumblr','dribbble','forrst','flickr','google', 'googleplus','facebook','linkedin','youtube','vimeo','rss','support','mail');
                    foreach($social_links as $social_link) {
                        if(!empty($data[$social_link])) { echo '<li><a href="'. $data[$social_link] .'" title="'. $social_link .'" target="_blank" class="tooltip"><img src="'. get_template_directory_uri() .'/images/social/'.$social_link.'.png" alt="" /></a></li>';
                        }
                    }
                ?>
        	</ul>
        	<!-- /social -->
            
        
</div><!-- /header -->

<div id="wrap" class="clearfix">

        <div id="navigation" class="clearfix">
            <?php wp_nav_menu( array(
                'theme_location' => 'main_menu',
                'sort_column' => 'menu_order',
                'menu_class' => 'sf-menu',
                'fallback_cb' => 'default_menu'
            )); ?>
        </div>
        <!-- /navigation -->  

	<div class="container clearfix fitvids">