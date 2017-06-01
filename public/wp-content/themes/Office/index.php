<?php
/**
 * @package WordPress
 * @subpackage Office Theme
 */
global $data
?>
<?php get_header(); ?>

<div class="home-wrap clearfix">

<?php
//show default homepage if blog style is not enabled
if($data['enable_disable_home_blog'] !='enable') {
?>

<?php
//get homepage module blocks
$layout = $data['homepage_blocks']['enabled'];
if ($layout):
foreach ($layout as $key=>$value) {
	
    switch($key) {

    case 'home_slider':
	require( TEMPLATEPATH . '/includes/home/slides.php');


    break;
    case 'home_tagline':
	
	if($data['home_tagline']) { ?>
	<?php if(!empty($data['home_tagline_title'])) { ?>
        <div class="heading">
            <?php if(!empty($data['home_tagline_title_url'])) { ?>
                <h2><a href="<?php echo $data['home_tagline_title_url']; ?>" title="<?php echo $data['home_tagline_title']; ?>"><span><?php echo $data['home_tagline_title']; ?></span></a></h2>
            <?php } else { ?>
                <h2><span><?php echo $data['home_tagline_title']; ?></span></h2>
            <?php } ?>
        </div>
        <!-- /heading -->
    <?php } ?>
	<div id="home-tagline" class="clearfix">
		<?php echo stripslashes(do_shortcode($data['home_tagline'])); ?>
    </div>
    <!-- /home-tagline -->
	<?php }
	
	
	break;
	case 'home_static_video':
	if($data['home_video']) {
		echo '<div class="home-video">'. stripslashes($data['home_video']) .'</div>';
	}
    
    break;
	case 'home_highlights':
	require( TEMPLATEPATH . '/includes/home/highlights.php');
	
	
	break;
	case 'home_portfolio':
	require( TEMPLATEPATH . '/includes/home/portfolio.php');
	if($data['disable_responsive'] !='disable') {
		require( TEMPLATEPATH . '/includes/home/portfolio-small-screens.php');
	}
	
	break;
	case 'home_blog':
	require( TEMPLATEPATH . '/includes/home/blog.php');
	

	break;
	case 'home_static_page':
	get_template_part( 'includes/home/static-page');
	
    }
}
endif;
?>

<?php 
} else {
	require( TEMPLATEPATH . '/includes/hp-blog.php'); //homepage blog style is enabled, so show that instead
} ?>

</div>
<!-- END home-wrap -->   
<?php get_footer(); ?>