<?php
/**
 * @package WordPress
 * @subpackage Office Theme
 * Template Name: Staff
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

<?php
$content = $post->post_content;
if(!empty($content)) { ?>
	<div id="staff-description">
    	<?php the_content(); ?>
    </div>
    <!-- /staff-description -->
<?php }?>

<div id="staff-wrap" class="clearfix">



	<?php
	query_posts(array(
		'post_type' => 'staff',
		'posts_per_page' => -1,
		'paged' => $paged
	));

	//start loop
	$count=0;
	while (have_posts()) : the_post();
	$count++;
	
	//get images
	$featured_image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'staff-thumb');
	
	//get meta
	$staff_position = get_post_meta($post->ID, 'office_staff_position', TRUE);
	?>
	
	<?php if(has_post_thumbnail() ) { ?>
	<div class="staff-member clearfix">
		<div class="staff-img">
			<a href="<?php the_permalink();?>"><img src="<?php echo $featured_image[0]; ?>" alt="<?php the_title(); ?>" width="<?php echo $featured_image[1]; ?>" height="<?php echo $featured_image[2]; ?>" /></a>
		</div>
		<!-- /staff-img -->
		
		<div class="staff-meta">
        	<h3><?php the_title(); ?></h3>
			<?php if($staff_position) { ?><?php echo ''.$staff_position.''; ?><?php } ?>
		</div>
		<!-- /staff-meta -->
	</div>
	<!-- /staff-member -->
    <?php } ?>
 
<?php endwhile; ?>

<?php pagination(); ?>

</div>
<!-- /staff-wrap -->
<?php wp_reset_query(); ?>

<?php endwhile; ?>
<?php endif; ?>	

<?php get_footer(); ?>