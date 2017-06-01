<?php
/**
 * @package WordPress
 * @subpackage Office Theme
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


<div class="post clearfix">

    <div class="entry clearfix">	
    <?php the_content(); ?>
    <?php comments_template(); ?>  
	</div>
	<!-- /entry -->
    
</div>
<!-- /post -->

<?php endwhile; ?>
<?php endif; ?>	  

<?php get_sidebar(); ?>
<?php get_footer(); ?>