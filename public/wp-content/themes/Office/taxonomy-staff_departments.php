<?php
/**
 * @package WordPress
 * @subpackage Office Theme
 */
?>
<?php get_header(); ?>


<?php if (have_posts()) : ?>

<div id="page-heading">
	<h1>
	<?php
		$term =	$wp_query->queried_object;
		echo $term->name;
	?> 
	</h1>
    <?php if($data['disable_breadcrumbs'] !='disable') { office_breadcrumbs(); } ?>  
</div>
<!-- /page-heading -->

<?php
$category_description = category_description();
if(!empty($category_description )) {
	echo apply_filters('category_archive_meta','<div id="staff-description">' . $category_description . '</div>');
}
?>

<div id="staff-wrap" class="clearfix">
	
	<?php
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

</div>
<!-- /staff-wrap -->

<?php endif; ?>
<?php get_footer(); ?>