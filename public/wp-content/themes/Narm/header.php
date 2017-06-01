<?php
/**
 * The Header for the template.
 *
 * @package WordPress
 * @subpackage Narm
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<title><?php wp_title('&lsaquo;', true, 'right'); ?><?php bloginfo('name'); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php
	/* Always have wp_head() just before the closing </head>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to add elements to <head> such
	 * as styles, scripts, and meta tags.
	 */
	wp_head();
?>

<?php
	/**
	*	Get favicon URL
	**/
	$nm_favicon = get_option('nm_favicon');
	
	
	if(!empty($nm_favicon))
	{
?>
		<link rel="shortcut icon" href="<?php echo $nm_favicon; ?>" />
<?php
	}
?>

<!-- Template stylesheet -->
<link rel="stylesheet" href="<?php bloginfo( 'stylesheet_directory' ); ?>/css/jqueryui/custom.css" type="text/css" media="all"/>
<link rel="stylesheet" href="<?php bloginfo( 'stylesheet_directory' ); ?>/css/screen.css" type="text/css" media="all"/>
<?php

	/**
	*	Check selected skin
	**/
	$nm_skin = get_option('nm_skin');
	if(empty($nm_skin))
	{
		$nm_skin = 'silver';
	}
	
?>

<link rel="stylesheet" href="<?php bloginfo( 'stylesheet_directory' ); ?>/css/skins/<?php echo $nm_skin; ?>.css" type="text/css" media="all"/>

<?php
	$nm_color = get_option('nm_color');
	
	
	if($nm_color == 'dark')
	{
?>
		<link rel="stylesheet" href="<?php bloginfo( 'stylesheet_directory' ); ?>/css/dark.css" type="text/css" media="all"/>
<?php
	}
?>

<link rel="stylesheet" type="text/css" href="<?php bloginfo( 'stylesheet_directory' ); ?>/js/fancybox/jquery.fancybox-1.3.0.css" media="screen"/>

<!--[if IE 7]>
<link rel="stylesheet" href="<?php bloginfo( 'stylesheet_directory' ); ?>/css/ie7.css" type="text/css" media="all"/>
<![endif]-->

<!-- Jquery and plugins -->
<script type="text/javascript" src="<?php bloginfo( 'stylesheet_directory' ); ?>/js/jquery.js"></script>
<script type="text/javascript" src="<?php bloginfo( 'stylesheet_directory' ); ?>/js/jquery-ui.js"></script>
<script type="text/javascript" src="<?php bloginfo( 'stylesheet_directory' ); ?>/js/fancybox/jquery.fancybox-1.3.0.js"></script>
<script type="text/javascript" src="<?php bloginfo( 'stylesheet_directory' ); ?>/js/jquery.easing.js"></script>
<script type="text/javascript" src="<?php bloginfo( 'stylesheet_directory' ); ?>/js/anythingSlider.js"></script>
<script type="text/javascript" src="<?php bloginfo( 'stylesheet_directory' ); ?>/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php bloginfo( 'stylesheet_directory' ); ?>/js/hint.js"></script>
<script type="text/javascript" src="<?php bloginfo( 'stylesheet_directory' ); ?>/js/cufon.js"></script>
<?php
	/**
	*	Get header font
	**/
	$nm_font = get_option('nm_font');
	
	
	if(empty($nm_font))
	{
		$nm_font = 'Colaborate_Thin.font';
	}
?>
<script type="text/javascript" src="<?php bloginfo( 'stylesheet_directory' ); ?>/fonts/<?php echo $nm_font?>.js"></script>
<script type="text/javascript" src="<?php bloginfo( 'stylesheet_directory' ); ?>/js/browser.js"></script>
<script type="text/javascript" src="<?php bloginfo( 'stylesheet_directory' ); ?>/js/custom.js"></script>

<?php
$nm_homepage_hide_slider = get_option('nm_homepage_hide_slider');
$nm_slider_height = get_option('nm_slider_height');

if(empty($nm_slider_height))
{
	$nm_slider_height = 405;
}

$nm_slider_height_offset = $nm_slider_height - 405;
?>

<style>
<?php
if(is_front_page() && $nm_homepage_hide_slider)
{
?>
body.home
{
	background-position: center -487px;
}
#menu_wrapper
{
	margin-bottom: 25px;
}
<?php
}
?>

<?php
if(!$nm_homepage_hide_slider)
{
?>
body.home
{
	background-position: center <?php echo $nm_slider_height_offset; ?>px;
}
<?php
}
?>

#slider
{
	height: <?php echo 405+$nm_slider_height_offset; ?>px;
}
#slider .wrapper
{
	height: <?php echo 400+$nm_slider_height_offset; ?>px;
}
#thumbNav, #thumbLeftNav, #thumbRightNav{ 
	top: <?php echo 383+$nm_slider_height_offset; ?>px;
}
</style>

</head>

<?php

/**
*	Get Current page object
**/
$page = get_page($post->ID);


/**
*	Get current page id
**/
$current_page_id = '';

if(isset($page->ID))
{
    $current_page_id = $page->ID;
}

?>

<body <?php body_class(); ?>>
	
	<?php
		$nm_portfolio_auto_scroll = get_option('nm_portfolio_auto_scroll');
	?>
	<input type="hidden" id="nm_portfolio_auto_scroll" name="nm_portfolio_auto_scroll" value="<?php echo $nm_portfolio_auto_scroll; ?>"/>
	<input type="hidden" id="nm_color" name="nm_color" value="<?php echo $nm_color; ?>"/>

	<!-- Begin template wrapper -->
	<div id="wrapper">
			
		<!-- Begin header -->
		<div id="header_wrapper">
			<div id="top_bar">
					<div class="logo">
						<!-- Begin logo -->
					
						<?php
							//get custom logo
							$nm_logo = get_option('nm_logo');
							
							if(empty($nm_logo))
							{
								if($nm_color == 'light')
								{
									$nm_logo = get_bloginfo( 'stylesheet_directory' ).'/images/logo_white.png';
								}
								else
								{
									$nm_logo = get_bloginfo( 'stylesheet_directory' ).'/images/dark_logo.png';
								}
							}

						?>
						
						<a id="custom_logo" href="<?php bloginfo( 'url' ); ?>"><img src="<?php echo $nm_logo?>" alt=""/></a>
						
						<!-- End logo -->
					
					</div>
			
					<!-- Begin main nav -->
					<div class="right_nav"></div>
					
						<div id="menu_wrapper">
							<!-- Begin main nav -->
							<?php 	
										//Get page nav
										wp_nav_menu( 
												array( 
													'menu_id'			=> 'main_menu',
													'menu_class'		=> 'nav',
													'theme_location' 	=> 'primary-menu',
												) 
										); 
							?>
							<!-- End main nav -->
							
							<!-- Begin search box -->
								<form class="search_box" action="" method="get">
									<p><input type="text" title="Search.." id="s" name="s"/></p>
								</form>
							<!-- End search box -->
						</div>
					<div class="left_nav"></div>
					<!-- End main nav -->
				
				</div>
		</div>
		<!-- End header -->
		
		<br class="clear"/>
