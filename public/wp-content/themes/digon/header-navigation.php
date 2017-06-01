<?php
/**
* Header Navigation
 */
?>
<div class="main-select-menu">
<?php				
// Responsive menu conversion to drop down list
echo Menu_to_SelectMenu ("top_menu","top-select-menu","-","Main Menu");
// Adds responsive WPML language selector menu
do_action('icl_language_selector');
?>
</div>
<div class="logo-menu-wrapper">
		<div class="logo">
			<a href="<?php echo home_url(); ?>/">
				<?php
				$main_logo=of_get_option('main_logo');
				if ( $main_logo<>"" ) {
					echo '<img class="logoimage" src="' . $main_logo .'" alt="logo" />';
				} else {
					echo '<img class="logoimage" src="'.MTHEME_PATH.'/images/logo.png" alt="logo" />';
				}
				?>
			</a>
		</div>
</div>
<div class="top-menu-wrap clearfix">
	<div class="menu-center-wrapper">
		<div class="social-header">
			<?php if ( !function_exists('dynamic_sidebar') 
			
				|| !dynamic_sidebar('Social Header') ) : ?>
			
			<?php endif; ?>
		</div>
		<div class="mainmenu-navigation">
			<?php
			if ( function_exists('wp_nav_menu') ) { 
				// If 3.0 menus exist
				include ( MTHEME_INCLUDES . 'menu/call-menu.php' );

			} else {
			?>
			<ul>
				<li>
					<a href="<?php echo home_url(); ?>/"><?php _e('Home','mthemelocal'); ?></a>
				</li>
			</ul>
			<?php
			}
			?>
		</div>
	</div>
</div>
<div original-title="<?php echo of_get_option('fullscreen_menu_default_text'); ?>" class="etips menu-toggle menu-toggle-off">
Toggle
</div>