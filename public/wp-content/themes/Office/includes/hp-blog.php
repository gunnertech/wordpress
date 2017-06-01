<?php
/**
 * @package WordPress
 * @subpackage Office Theme
 */
?>

<?php
require( TEMPLATEPATH . '/includes/home/slides.php');
?>
<div class="post clearfix">
	<?php
		//show posts
    	if (have_posts()) :        
				get_template_part( 'loop', 'entry');  
    	endif;
	?>

<?php pagination(); ?>
</div>
<!-- /post -->

<?php get_sidebar(); ?>