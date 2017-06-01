<?php
/**
 * @package WordPress
 * @subpackage Office Theme
 * Template Name: Blog
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
<!-- /page-heading -->


<div class="post clearfix">

<?php
    //query posts
        query_posts(
            array(
            'post_type'=> 'post',
            'paged'=>$paged
        ));
    ?>
	<?php if (have_posts()) : ?>              
        	<?php get_template_part( 'loop', 'entry') ?>                	
    <?php endif; ?>       
    
	<?php pagination(); wp_reset_query(); ?>

</div>
<!-- /post -->

<?php endwhile; ?>
<?php endif; ?>


<?php get_sidebar(); ?>
<?php get_footer(); ?>