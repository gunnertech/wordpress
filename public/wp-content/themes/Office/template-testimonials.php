<?php
/**
 * @package WordPress
 * @subpackage Office Theme
 * Template Name: Testimonials
 */
?>
<?php get_header(); ?>

<?php
if (have_posts()) : while (have_posts()) : the_post();
$page_slider = get_post_meta($post->ID, 'office_page_slider', true); //get meta
if ($page_slider == 'enable') {
	get_template_part( 'includes/page-slides'); //show slider
}
?>

<div id="page-heading">
    <h1><?php the_title(); ?></h1>		
    <?php if($data['disable_breadcrumbs'] !='disable') { office_breadcrumbs(); } ?>
</div>
<!-- END page-heading -->

<?php
$content = $post->post_content;
if(!empty($content)) { ?>
	<div id="testimonials-description">
    	<?php the_content(); ?>
    </div>
    <!-- /testimonials-description -->
<?php }?>

<div class="post full-width clearfix">

    <div id="testimonials-wrap clearfix">
    
    <?php
	global $post;
	$args = array(
		'post_type' =>'testimonials',
		'numberposts' => '-1'
	);
	$testimonials = get_posts($args);

	$count=0;
	foreach($testimonials as $post) : setup_postdata($post);
	$count++;
	?>
    
    <div class="testimonial-item one-third <?php if($count == '3') { echo 'column-last'; } ?>">
        <div class="testimonial">
            <?php the_content(); ?>
        </div>
        <!-- /testimonial -->
        <div class="testimonial-author"><?php the_title(); ?></div>
    </div>
    <!-- /testimonial-item -->
    
	<?php if($count == '3') { echo '<div class="clear"></div>'; $count=0; } endforeach; wp_reset_postdata(); ?>
    
	</div>
	<!-- /testimonials-wrap -->
    
</div>
<!-- /post -->

<?php endwhile; ?>
<?php endif; ?>	  

<?php get_footer(); ?>